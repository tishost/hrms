<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;

class CheckEmailSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check email settings in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking Email Settings...');
        
        // Check email enabled setting
        $emailEnabled = Setting::where('key', 'email_enabled')->value('value');
        $this->info("Email Enabled: " . ($emailEnabled ?? 'null'));
        
        // Check mail settings
        $mailSettings = Setting::where('key', 'like', 'mail_%')->get();
        
        if ($mailSettings->count() > 0) {
            $this->info("\nMail Settings in Database:");
            foreach ($mailSettings as $setting) {
                $value = $setting->key === 'mail_password' ? '***hidden***' : $setting->value;
                $this->line("  {$setting->key}: {$value}");
            }
        } else {
            $this->warn("No mail settings found in database!");
        }
        
        // Check config values
        $this->info("\nConfig Values:");
        $this->line("  MAIL_HOST: " . config('mail.mailers.smtp.host'));
        $this->line("  MAIL_PORT: " . config('mail.mailers.smtp.port'));
        $this->line("  MAIL_USERNAME: " . config('mail.mailers.smtp.username'));
        $this->line("  MAIL_ENCRYPTION: " . config('mail.mailers.smtp.encryption'));
        $this->line("  MAIL_FROM_ADDRESS: " . config('mail.from.address'));
        
        $this->info("\nDone!");
        
        return 0;
    }
}
