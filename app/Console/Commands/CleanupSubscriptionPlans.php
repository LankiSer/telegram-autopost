<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;

class CleanupSubscriptionPlans extends Command
{
    protected $signature = 'subscriptions:cleanup-plans';
    protected $description = 'Remove duplicate subscription plans and keep only the newest of each name';

    public function handle()
    {
        $this->info('Starting cleanup of subscription plans...');
        
        try {
            // Find all plan names
            $planNames = SubscriptionPlan::select('name')
                ->groupBy('name')
                ->get()
                ->pluck('name');
                
            $totalRemoved = 0;
                
            // For each plan name, keep only the newest entry
            foreach ($planNames as $name) {
                // Get the newest plan with this name
                $newestPlan = SubscriptionPlan::where('name', $name)
                    ->orderBy('created_at', 'desc')
                    ->first();
                    
                // Remove all other plans with the same name
                $removed = SubscriptionPlan::where('name', $name)
                    ->where('id', '!=', $newestPlan->id)
                    ->delete();
                    
                $totalRemoved += $removed;
                
                $this->info("Processed plan '{$name}': removed {$removed} duplicates, kept ID {$newestPlan->id}");
            }
            
            $this->info("Cleanup completed. Removed {$totalRemoved} duplicate plans.");
            
        } catch (\Exception $e) {
            $this->error("Error during cleanup: {$e->getMessage()}");
        }
    }
} 