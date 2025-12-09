<?php

namespace App\Console\Commands;

use App\Services\VpsUsageAggregationService;
use Illuminate\Console\Command;

class AggregateVpsUsageCommand extends Command
{
    protected $signature = 'vps-usage:aggregate {--type=hourly : Type of aggregation (hourly|daily|cleanup)}';
    protected $description = 'Aggregate VPS usage data into summaries';

    public function handle()
    {
        $service = new VpsUsageAggregationService();
        $type = $this->option('type');
        
        try {
            match ($type) {
                'hourly' => $count = $service->aggregateHourly(1),
                'daily' => $count = $service->aggregateDaily(1),
                'cleanup' => $count = $service->cleanupOldData(7),
                default => $this->error("Unknown type: $type"),
            };
            
            if (isset($count)) {
                $this->info("✓ Successfully processed $count records");
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
