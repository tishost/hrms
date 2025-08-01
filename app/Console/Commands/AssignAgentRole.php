<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignAgentRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-agent {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign agent role to a user by email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        // Find user by email
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }
        
        // Get or create agent role
        $agentRole = Role::firstOrCreate(['name' => 'agent', 'guard_name' => 'web']);
        
        // Assign role to user
        $user->assignRole($agentRole);
        
        $this->info("Agent role assigned to {$user->name} ({$user->email}) successfully!");
        
        return 0;
    }
}
