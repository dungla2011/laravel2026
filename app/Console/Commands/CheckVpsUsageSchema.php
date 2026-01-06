<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckVpsUsageSchema extends Command
{
    protected $signature = 'check:vps-usage-schema';
    protected $description = 'Check vps_usages table structure and sample data';

    public function handle()
    {
        $this->line('╔════════════════════════════════════════════════════════════════╗');
        $this->line('║          CẤU TRÚC BẢNG vps_usages                            ║');
        $this->line('╚════════════════════════════════════════════════════════════════╝');
        $this->line('');

        // Get columns
        $columns = Schema::getColumns('vps_usages');
        $this->table(
            ['#', 'Column Name', 'Type', 'Nullable', 'Default'],
            collect($columns)->map(function ($col, $i) {
                return [
                    $i + 1,
                    $col['name'],
                    $col['type'],
                    $col['nullable'] ? 'Yes' : 'No',
                    $col['default'] ?? '-'
                ];
            })->toArray()
        );

        $this->line('');
        $this->line('╔════════════════════════════════════════════════════════════════╗');
        $this->line('║                      INDEXES                                   ║');
        $this->line('╚════════════════════════════════════════════════════════════════╝');
        $this->line('');

        $indexes = DB::select("SHOW INDEXES FROM vps_usages");
        $indexGroups = [];
        foreach ($indexes as $index) {
            $indexGroups[$index->Key_name][] = $index->Column_name;
        }

        foreach ($indexGroups as $indexName => $columns) {
            $this->line("📌 <info>$indexName</info>: " . implode(", ", $columns));
        }

        $this->line('');
        $this->line('╔════════════════════════════════════════════════════════════════╗');
        $this->line('║                    STATISTICS                                  ║');
        $this->line('╚════════════════════════════════════════════════════════════════╝');
        $this->line('');

        $count = DB::table('vps_usages')->count();
        $this->line("📊 Total records: <info>$count</info>");

        $stats = DB::table('vps_usages')
            ->selectRaw('
                COUNT(DISTINCT user_id) as users,
                COUNT(DISTINCT instance_id) as instances,
                COUNT(DISTINCT power_state) as power_states,
                MIN(created_at) as oldest,
                MAX(created_at) as newest,
                AVG(CAST(price_per_minute AS DECIMAL(10,8))) as avg_price
            ')
            ->first();

        $this->line("👥 Unique Users: <info>" . $stats->users . "</info>");
        $this->line("🖥️  Unique Instances: <info>" . $stats->instances . "</info>");
        $this->line("⚡ Power States: <info>" . $stats->power_states . "</info>");
        $this->line("📅 Oldest: <info>" . $stats->oldest . "</info>");
        $this->line("📅 Newest: <info>" . $stats->newest . "</info>");
        $this->line("💰 Avg Price/Minute: <info>" . number_format($stats->avg_price, 8) . "</info>");

        $this->line('');
        $this->line('╔════════════════════════════════════════════════════════════════╗');
        $this->line('║                 SAMPLE DATA (Latest 5)                         ║');
        $this->line('╚════════════════════════════════════════════════════════════════╝');
        $this->line('');

        $samples = DB::table('vps_usages')
            ->selectRaw('
                id, 
                name, 
                instance_id, 
                user_id, 
                price_per_minute, 
                power_state, 
                cpu,
                ram_gb,
                disk_gb,
                timestamp_minute,
                created_at
            ')
            ->limit(5)
            ->orderByDesc('id')
            ->get();

        $this->table(
            ['ID', 'Name', 'Instance', 'User', 'Price/Min', 'State', 'CPU', 'RAM', 'Disk', 'Created'],
            $samples->map(function ($row) {
                return [
                    $row->id,
                    $row->name,
                    $row->instance_id,
                    $row->user_id,
                    number_format($row->price_per_minute, 8),
                    $row->power_state,
                    $row->cpu,
                    $row->ram_gb . 'GB',
                    $row->disk_gb . 'GB',
                    $row->created_at
                ];
            })->toArray()
        );

        $this->line('');
        $this->line('<info>✅ Done!</info>');
    }
}
