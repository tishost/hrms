<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Owner;
use App\Services\PackageLimitService;

class InitializePackageLimits extends Command
{
    protected $signature = 'package:initialize-limits {--owner-id= : Initialize for specific owner ID}';
    protected $description = 'Initialize package limits for existing owners';

    public function handle()
    {
        $packageLimitService = new PackageLimitService();

        if ($ownerId = $this->option('owner-id')) {
            $owner = Owner::find($ownerId);
            if (!$owner) {
                $this->error("Owner with ID {$ownerId} not found.");
                return 1;
            }

            $this->info("Initializing package limits for owner: {$owner->name}");
            $packageLimitService->initializeLimits($owner);
            $this->info("Package limits initialized successfully for owner: {$owner->name}");
        } else {
            $owners = Owner::all();
            $this->info("Found {$owners->count()} owners. Initializing package limits...");

            $bar = $this->output->createProgressBar($owners->count());
            $bar->start();

            foreach ($owners as $owner) {
                $packageLimitService->initializeLimits($owner);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Package limits initialized for all owners successfully!");
        }

        return 0;
    }
}
