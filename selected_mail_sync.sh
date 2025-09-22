#!/bin/bash
#
# Selected Users Mail Sync Script (improved)
# - Syncs mail for pre-defined users only
# - Optional safe deletions with backups on remote
# - Dry-run support and minor hardening

# ================= Configuration (defaults) =================
LOCK_FILE="/tmp/selected_mail_sync.lock"
LOG_FILE="/tmp/selected_mail_sync.log"
SECONDARY_SERVER="103.4.145.54"
SSH_PORT="22"
ALLOW_DELETE=false
BACKUP_BASE="/root/rsync_backups"   # remote backup root when --delete is enabled
DRY_RUN=${DRY_RUN:-0}

# Pre-defined users to sync (space separated)
SELECTED_USERS="sksrc"  # Example: "sksrc user1 user2"

# Excludes
RSYNC_EXCLUDES=(
  --exclude='*.lock'
  --exclude='tmp/'
  --exclude='cache/'
  --exclude='logs/'
  --exclude='dovecot.index*'
  --exclude='dovecot.list*'
  --exclude='dovecot.mailbox.log'
  --exclude='maildirsize'
  --exclude='dovecot-uidlist'
  --exclude='dovecot-uidvalidity'
)

# Rsync options
RSYNC_COMMON="-avz --progress --update --checksum"
# ===========================================================

print_usage() {
  cat <<USAGE
Usage: $0 [options]

Options:
  -n, --dry-run           Show what would be done, without making changes
      --delete            Allow deletions on remote (with backups)
  -s, --server HOST       Secondary server IP/host (default: ${SECONDARY_SERVER})
  -p, --port PORT         SSH port (default: ${SSH_PORT})
  -u, --users "u1 u2"     Space-separated list of users to sync
      --log-file PATH     Log file path (default: ${LOG_FILE})
      --lock-file PATH    Lock file path (default: ${LOCK_FILE})
      --backup-base PATH  Remote backup base when deleting (default: ${BACKUP_BASE})
  -h, --help              Show this help
USAGE
}

log() { echo "$(date '+%F %T') - $1" | tee -a "$LOG_FILE"; }

run_cmd() {
  local cmd="$1"
  if [ "$DRY_RUN" = "1" ]; then
    log "[DRY RUN] $cmd"
    return 0
  else
    log "[RUN] $cmd"
    eval "$cmd"
    return $?
  fi
}

check_lock() {
  if [ -f "$LOCK_FILE" ]; then
    PID=$(cat "$LOCK_FILE" 2>/dev/null || echo "")
    if [ -n "$PID" ] && kill -0 "$PID" 2>/dev/null; then
      log "Script already running (PID: $PID). Exiting."
      exit 1
    else
      log "Stale lock file found. Removing."
      rm -f "$LOCK_FILE"
    fi
  fi
  echo $$ > "$LOCK_FILE"
}

cleanup() {
  rm -f "$LOCK_FILE"
  log "=========== Sync Process Completed ==========="
}
trap cleanup EXIT INT TERM

error_exit() { log "ERROR: $1"; exit 1; }

SSH_OPTS_COMMON="-o BatchMode=yes -o StrictHostKeyChecking=accept-new -o ConnectTimeout=8"

user_exists_remote() {
  ssh -p "$SSH_PORT" $SSH_OPTS_COMMON root@"$SECONDARY_SERVER" "id -u $1 >/dev/null 2>&1 && echo exists || echo notfound" 2>/dev/null || echo "notfound"
}

build_rsync_excludes() {
  local extra=""
  for e in "${RSYNC_EXCLUDES[@]}"; do
    extra="$extra $e"
  done
  echo "$extra"
}

clean_mail_indexes_remote() {
  local username="$1"
  local domain="$2"
  local email_account="$3"
  local remote_cmd="cd /home/$username/mail/$domain/$email_account 2>/dev/null || true; find . -type f -name 'dovecot*' -delete 2>/dev/null || true; find . -type f -name 'maildirsize' -delete 2>/dev/null || true"
  run_cmd "ssh -p $SSH_PORT $SSH_OPTS_COMMON root@$SECONDARY_SERVER \"$remote_cmd\""
}

restart_dovecot_remote() {
  run_cmd "ssh -p $SSH_PORT $SSH_OPTS_COMMON root@$SECONDARY_SERVER 'systemctl restart dovecot || service dovecot restart || true'"
}

# Function to find all domains for a user
find_user_domains() {
  local username="$1"
  local domains=()
  if [ -d "/home/$username/mail" ]; then
    for domain_dir in /home/$username/mail/*; do
      if [ -d "$domain_dir" ] && [ "$(basename "$domain_dir")" != "cur" ] && [ "$(basename "$domain_dir")" != "new" ] && [ "$(basename "$domain_dir")" != "tmp" ]; then
        domains+=("$(basename "$domain_dir")")
      fi
    done
  fi
  echo "${domains[@]}"
}

# Function to find all email accounts for a user's domain
find_domain_email_accounts() {
  local username="$1"
  local domain="$2"
  local email_accounts=()
  if [ -d "/home/$username/mail/$domain" ]; then
    for email_dir in /home/$username/mail/$domain/*; do
      if [ -d "$email_dir" ] && [ -d "$email_dir/cur" ] && [ -d "$email_dir/new" ]; then
        email_accounts+=("$(basename "$email_dir")")
      fi
    done
  fi
  echo "${email_accounts[@]}"
}

# Function to sync a single email account
sync_single_email_account() {
  local username="$1"
  local domain="$2"
  local email_account="$3"
  local rsync_excludes
  rsync_excludes=$(build_rsync_excludes)

  log "Syncing: $email_account@$domain (user: $username)"

  local local_path="/home/$username/mail/$domain/$email_account"
  if [ ! -d "$local_path" ]; then
    log "WARNING: Email account $email_account@$domain not found locally"
    return 1
  fi

  # Ensure directory structure exists on remote
  run_cmd "ssh -p $SSH_PORT $SSH_OPTS_COMMON root@$SECONDARY_SERVER 'mkdir -p /home/$username/mail/$domain/$email_account'"

  # If deletions allowed, ensure remote backup dir exists for this account
  local backup_dir_arg=""
  if [ "$ALLOW_DELETE" = "true" ]; then
    local account_backup_dir="$BACKUP_BASE/$RUN_ID/$username/$domain/$email_account"
    run_cmd "ssh -p $SSH_PORT $SSH_OPTS_COMMON root@$SECONDARY_SERVER 'mkdir -p \"$account_backup_dir\"'"
    backup_dir_arg="--delete --backup --backup-dir=\"$account_backup_dir\""
  fi

  # Sync the email account directory
  run_cmd "rsync $RSYNC_COMMON $rsync_excludes $backup_dir_arg -e \"ssh -p $SSH_PORT $SSH_OPTS_COMMON\" \"$local_path/\" root@$SECONDARY_SERVER:\"/home/$username/mail/$domain/$email_account/\""

  # Set proper permissions
  if [ "$(user_exists_remote "$username")" = "exists" ]; then
    run_cmd "ssh -p $SSH_PORT $SSH_OPTS_COMMON root@$SECONDARY_SERVER 'chown -R $username:$username /home/$username/mail/$domain/$email_account || true'"
  fi

  clean_mail_indexes_remote "$username" "$domain" "$email_account"

  log "Completed: $email_account@$domain"
}

# Function to process all accounts for a user
process_user() {
  local username="$1"

  # Check if user exists locally
  if [ ! -d "/home/$username" ]; then
    log "WARNING: User $username does not exist locally"
    return 1
  fi

  # Check if user has mail directory
  if [ ! -d "/home/$username/mail" ]; then
    log "WARNING: User $username has no mail directory"
    return 1
  fi

  log "Processing user: $username"

  local domains
  domains=$(find_user_domains "$username")
  if [ -z "$domains" ]; then
    log "No domains found for user: $username"
    return 0
  fi

  for domain in $domains; do
    log "Processing domain: $domain"

    local email_accounts
    email_accounts=$(find_domain_email_accounts "$username" "$domain")
    if [ -z "$email_accounts" ]; then
      log "No email accounts found for domain: $domain"
      continue
    fi

    for email_account in $email_accounts; do
      sync_single_email_account "$username" "$domain" "$email_account"
    done
  done
}

# ================== MAIN ==================

# Parse CLI options
while [ $# -gt 0 ]; do
  case "$1" in
    -n|--dry-run)
      DRY_RUN=1
      ;;
    --delete|--allow-delete)
      ALLOW_DELETE=true
      ;;
    -s|--server)
      shift; SECONDARY_SERVER="${1:-$SECONDARY_SERVER}"
      ;;
    -p|--port)
      shift; SSH_PORT="${1:-$SSH_PORT}"
      ;;
    -u|--users)
      shift; SELECTED_USERS="${1:-$SELECTED_USERS}"
      ;;
    --log-file)
      shift; LOG_FILE="${1:-$LOG_FILE}"
      ;;
    --lock-file)
      shift; LOCK_FILE="${1:-$LOCK_FILE}"
      ;;
    --backup-base)
      shift; BACKUP_BASE="${1:-$BACKUP_BASE}"
      ;;
    -h|--help)
      print_usage; exit 0
      ;;
    *)
      echo "Unknown option: $1" >&2; echo; print_usage; exit 2
      ;;
  esac
  shift
done

log "=========== Selected Users Mail Sync Started ==========="
check_lock

# Unique run id used for backup-dir when deletions are allowed
RUN_ID=$(date '+%F_%H-%M-%S')

# Test SSH connection
if ! ssh -p "$SSH_PORT" $SSH_OPTS_COMMON root@"$SECONDARY_SERVER" "echo ok" >/dev/null 2>&1; then
  error_exit "Cannot reach secondary server $SECONDARY_SERVER over SSH"
fi
log "Secondary server $SECONDARY_SERVER is reachable"

# Check if selected users are configured
if [ -z "$SELECTED_USERS" ]; then
  error_exit "No users selected for sync. Please configure SELECTED_USERS variable or pass --users."
fi

log "Selected users: $SELECTED_USERS"

# Process all selected users
for username in $SELECTED_USERS; do
  process_user "$username"
done

# Restart dovecot once after all syncs
restart_dovecot_remote

log "All selected users mail accounts synchronized"
log "=========== Sync Completed ==========="

