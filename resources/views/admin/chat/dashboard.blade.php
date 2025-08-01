@extends('layouts.admin')

@section('title', 'Chat Agent Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Waiting Sessions</h5>
                            <h3 class="mb-0" id="waiting-sessions-count">{{ $waitingSessions->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">My Active Sessions</h5>
                            <h3 class="mb-0" id="my-sessions-count">{{ $mySessions->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-comments fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Total Messages</h5>
                            <h3 class="mb-0" id="total-messages">0</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-envelope fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Resolved Sessions</h5>
                            <h3 class="mb-0" id="resolved-sessions">0</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Waiting Sessions -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock text-warning me-2"></i>
                        Waiting Sessions
                    </h5>
                </div>
                <div class="card-body">
                    <div id="waiting-sessions-list">
                        @if($waitingSessions->count() > 0)
                            @foreach($waitingSessions as $session)
                                <div class="session-item border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Session: {{ substr($session->session_id, 0, 20) }}...</h6>
                                            <small class="text-muted">
                                                <i class="fas fa-globe me-1"></i>{{ $session->visitor_ip }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>{{ $session->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <button class="btn btn-primary btn-sm take-session-btn" 
                                                data-session-id="{{ $session->session_id }}">
                                            <i class="fas fa-hand-paper me-1"></i>Take Session
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-check-circle fa-3x mb-3"></i>
                                <p>No waiting sessions</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- My Active Sessions -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-comments text-success me-2"></i>
                        My Active Sessions
                    </h5>
                </div>
                <div class="card-body">
                    <div id="my-sessions-list">
                        @if($mySessions->count() > 0)
                            @foreach($mySessions as $session)
                                <div class="session-item border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Session: {{ substr($session->session_id, 0, 20) }}...</h6>
                                            <small class="text-muted">
                                                <i class="fas fa-globe me-1"></i>{{ $session->visitor_ip }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>{{ $session->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <div>
                                            <button class="btn btn-info btn-sm me-2 open-chat-btn" 
                                                    data-session-id="{{ $session->session_id }}">
                                                <i class="fas fa-comment me-1"></i>Open Chat
                                            </button>
                                            <button class="btn btn-success btn-sm resolve-session-btn" 
                                                    data-session-id="{{ $session->session_id }}">
                                                <i class="fas fa-check me-1"></i>Resolve
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-comments fa-3x mb-3"></i>
                                <p>No active sessions</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Sessions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history text-info me-2"></i>
                        Recent Sessions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Session ID</th>
                                    <th>Visitor IP</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSessions as $session)
                                    <tr>
                                        <td>{{ substr($session->session_id, 0, 20) }}...</td>
                                        <td>{{ $session->visitor_ip }}</td>
                                        <td>
                                            @if($session->status == 'waiting')
                                                <span class="badge bg-warning">Waiting</span>
                                            @elseif($session->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($session->status == 'resolved')
                                                <span class="badge bg-info">Resolved</span>
                                            @endif
                                        </td>
                                        <td>{{ $session->created_at->diffForHumans() }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary view-session-btn" 
                                                    data-session-id="{{ $session->session_id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chat Modal -->
<div class="modal fade" id="chatModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-comments me-2"></i>
                    Chat Session: <span id="current-session-id"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="chat-messages" class="border rounded p-3" style="height: 400px; overflow-y: auto;">
                    <!-- Messages will be loaded here -->
                </div>
                <div class="mt-3">
                    <div class="input-group">
                        <input type="text" id="agent-message-input" class="form-control" 
                               placeholder="Type your message...">
                        <button class="btn btn-primary" id="send-agent-message">
                            <i class="fas fa-paper-plane"></i> Send
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="resolve-session-modal">
                    <i class="fas fa-check"></i> Resolve Session
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentSessionId = null;
let chatModal = null;

document.addEventListener('DOMContentLoaded', function() {
    chatModal = new bootstrap.Modal(document.getElementById('chatModal'));
    
    // Load initial analytics
    loadAnalytics();
    
    // Set up event listeners
    setupEventListeners();
    
    // Auto-refresh every 10 seconds for real-time updates
    setInterval(refreshData, 10000);
    
    // Check for new messages every 5 seconds
    setInterval(checkNewMessages, 5000);
});

function setupEventListeners() {
    // Take session buttons
    document.querySelectorAll('.take-session-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const sessionId = this.dataset.sessionId;
            takeSession(sessionId);
        });
    });
    
    // Open chat buttons
    document.querySelectorAll('.open-chat-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const sessionId = this.dataset.sessionId;
            openChat(sessionId);
        });
    });
    
    // Resolve session buttons
    document.querySelectorAll('.resolve-session-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const sessionId = this.dataset.sessionId;
            resolveSession(sessionId);
        });
    });
    
    // View session buttons
    document.querySelectorAll('.view-session-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const sessionId = this.dataset.sessionId;
            openChat(sessionId);
        });
    });
    
    // Send message
    document.getElementById('send-agent-message').addEventListener('click', sendAgentMessage);
    document.getElementById('agent-message-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendAgentMessage();
        }
    });
    
    // Resolve session from modal
    document.getElementById('resolve-session-modal').addEventListener('click', function() {
        if (currentSessionId) {
            resolveSession(currentSessionId);
            chatModal.hide();
        }
    });
}

async function takeSession(sessionId) {
    try {
        const response = await fetch('{{ route("admin.chat.take-session") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ session_id: sessionId })
        });
        
        if (response.ok) {
            showAlert('Session taken successfully!', 'success');
            refreshData();
        } else {
            showAlert('Failed to take session', 'error');
        }
    } catch (error) {
        console.error('Error taking session:', error);
        showAlert('Error taking session', 'error');
    }
}

async function openChat(sessionId) {
    currentSessionId = sessionId;
    document.getElementById('current-session-id').textContent = sessionId;
    
    try {
        const response = await fetch(`{{ route('admin.chat.session', 'SESSION_ID') }}`.replace('SESSION_ID', sessionId));
        const data = await response.json();
        
        if (data.success) {
            displayChatMessages(data.data.messages);
            chatModal.show();
        }
    } catch (error) {
        console.error('Error loading chat:', error);
        showAlert('Error loading chat', 'error');
    }
}

function displayChatMessages(messages) {
    const chatContainer = document.getElementById('chat-messages');
    chatContainer.innerHTML = '';
    
    messages.forEach(message => {
        const messageDiv = document.createElement('div');
        messageDiv.className = `mb-3 ${message.message_type === 'user' ? 'text-end' : ''}`;
        
        const messageClass = message.message_type === 'user' ? 'bg-primary text-white' : 
                           message.message_type === 'agent' ? 'bg-success text-white' : 'bg-light';
        
        messageDiv.innerHTML = `
            <div class="d-inline-block p-2 rounded ${messageClass}" style="max-width: 70%;">
                <small class="d-block mb-1">${message.message_type.toUpperCase()}</small>
                <div>${message.message}</div>
                <small class="d-block mt-1">${new Date(message.created_at).toLocaleTimeString()}</small>
            </div>
        `;
        
        chatContainer.appendChild(messageDiv);
    });
    
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

async function sendAgentMessage() {
    const input = document.getElementById('agent-message-input');
    const message = input.value.trim();
    
    if (!message || !currentSessionId) return;
    
    try {
        const response = await fetch('{{ route("admin.chat.send-message") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                session_id: currentSessionId,
                message: message
            })
        });
        
        if (response.ok) {
            input.value = '';
            openChat(currentSessionId); // Refresh chat
        } else {
            showAlert('Failed to send message', 'error');
        }
    } catch (error) {
        console.error('Error sending message:', error);
        showAlert('Error sending message', 'error');
    }
}

async function resolveSession(sessionId) {
    try {
        const response = await fetch('{{ route("admin.chat.resolve-session") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ session_id: sessionId })
        });
        
        if (response.ok) {
            showAlert('Session resolved successfully!', 'success');
            refreshData();
        } else {
            showAlert('Failed to resolve session', 'error');
        }
    } catch (error) {
        console.error('Error resolving session:', error);
        showAlert('Error resolving session', 'error');
    }
}

async function loadAnalytics() {
    try {
        const response = await fetch('{{ route("admin.chat.analytics") }}');
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('total-messages').textContent = data.data.my_messages;
            document.getElementById('resolved-sessions').textContent = data.data.resolved_sessions;
        }
    } catch (error) {
        console.error('Error loading analytics:', error);
    }
}

async function refreshData() {
    // Refresh waiting sessions
    try {
        const response = await fetch('{{ route("admin.chat.waiting-sessions") }}');
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('waiting-sessions-count').textContent = data.data.length;
            
            // Update waiting sessions list
            const waitingList = document.getElementById('waiting-sessions-list');
            if (waitingList) {
                waitingList.innerHTML = '';
                data.data.forEach(session => {
                    const sessionDiv = document.createElement('div');
                    sessionDiv.className = 'd-flex justify-content-between align-items-center p-2 border-bottom';
                    sessionDiv.innerHTML = `
                        <div>
                            <strong>Session: ${session.session_id}</strong><br>
                            <small class="text-muted">${session.created_at}</small>
                        </div>
                        <button class="btn btn-sm btn-primary take-session-btn" data-session-id="${session.session_id}">
                            <i class="fas fa-hand-paper"></i> Take
                        </button>
                    `;
                    waitingList.appendChild(sessionDiv);
                });
            }
        }
    } catch (error) {
        console.error('Error refreshing data:', error);
    }
    
    // Refresh my sessions
    try {
        const response = await fetch('{{ route("admin.chat.my-sessions") }}');
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('my-sessions-count').textContent = data.data.length;
            
            // Update my sessions list
            const mySessionsList = document.getElementById('my-sessions-list');
            if (mySessionsList) {
                mySessionsList.innerHTML = '';
                data.data.forEach(session => {
                    const sessionDiv = document.createElement('div');
                    sessionDiv.className = 'd-flex justify-content-between align-items-center p-2 border-bottom';
                    sessionDiv.innerHTML = `
                        <div>
                            <strong>Session: ${session.session_id}</strong><br>
                            <small class="text-muted">${session.created_at}</small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-info open-chat-btn me-1" data-session-id="${session.session_id}">
                                <i class="fas fa-comments"></i> Chat
                            </button>
                            <button class="btn btn-sm btn-success resolve-session-btn" data-session-id="${session.session_id}">
                                <i class="fas fa-check"></i> Resolve
                            </button>
                        </div>
                    `;
                    mySessionsList.appendChild(sessionDiv);
                });
            }
        }
    } catch (error) {
        console.error('Error refreshing data:', error);
    }
    
    // Re-attach event listeners after updating DOM
    setupEventListeners();
}

async function checkNewMessages() {
    try {
        const response = await fetch('{{ route("admin.chat.waiting-sessions") }}');
        const data = await response.json();
        
        if (data.success && data.data.length > 0) {
            // Show notification for new waiting sessions
            const currentCount = parseInt(document.getElementById('waiting-sessions-count').textContent || '0');
            if (data.data.length > currentCount) {
                showNotification('New chat session waiting!', 'info');
                // Play notification sound
                const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUarm7blmGgU7k9n1unEiBC13yO/eizEIHWq+8+OWT');
                audio.play().catch(e => console.log('Audio play failed:', e));
            }
        }
    } catch (error) {
        console.error('Error checking new messages:', error);
    }
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.querySelector('main').insertBefore(alertDiv, document.querySelector('main').firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        <i class="fas fa-${type === 'info' ? 'info-circle' : 'bell'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>
@endpush 