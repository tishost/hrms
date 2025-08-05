<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;

class CreateEmailSetting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:setup {--enabled=1 : Set email enabled (1 or 0)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update email enabled setting';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $enabled = $this->option('enabled');
        
        // Validate input
        if (!in_array($enabled, ['0', '1'])) {
            $this->error('Enabled must be 0 or 1');
            return 1;
        }

        try {
            // Create or update the setting
            Setting::updateOrCreate(
                ['key' => 'email_enabled'],
                ['value' => $enabled]
            );

            $status = $enabled === '1' ? 'enabled' : 'disabled';
            $this->info("Email notifications {$status} successfully!");

            // Show current setting
            $currentValue = Setting::where('key', 'email_enabled')->value('value');
            $this->info("Current email_enabled setting: {$currentValue}");

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to create email setting: ' . $e->getMessage());
            return 1;
        }
    }
}
