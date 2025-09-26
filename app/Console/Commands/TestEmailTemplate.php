<?php

namespace App\Console\Commands;

use App\Events\UserRegistered;
use App\Events\PaymentCompleted;
use App\Models\User;
use App\Models\EmailTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;

class TestEmailTemplate extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:email-template {template_name} {email}';

    /**
     * The console command description.
     */
    protected $description = 'Test email template by sending it to a specific email address';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $templateName = $this->argument('template_name');
        $email = $this->argument('email');

        // Find the template
        $template = EmailTemplate::where('name', $templateName)->first();

        if (!$template) {
            $this->error("Template '{$templateName}' not found!");
            return 1;
        }

        if (!$template->is_active) {
            $this->error("Template '{$templateName}' is not active!");
            return 1;
        }

        // Create a test user
        $testUser = new User([
            'name' => 'Test User',
            'email' => $email,
        ]);

        // Send test email based on template type
        switch ($templateName) {
            case 'user_registration':
                Event::dispatch(new UserRegistered($testUser));
                $this->info("User registration email sent to {$email}");
                break;

            case 'payment_confirmation':
                // Create a test payment
                $testPayment = new \App\Models\Payment([
                    'amount' => 100.00,
                    'transaction_id' => 'TEST_' . time(),
                ]);
                Event::dispatch(new PaymentCompleted($testUser, $testPayment));
                $this->info("Payment confirmation email sent to {$email}");
                break;

            default:
                $this->error("Unknown template type: {$templateName}");
                return 1;
        }

        $this->info("Test email sent successfully!");
        return 0;
    }
}
