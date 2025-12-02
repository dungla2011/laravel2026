<?php

namespace App\Console\Commands;

use App\Services\BalanceService;
use Illuminate\Console\Command;

class VerifyBalanceSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balance:verify-sync {--user-id= : Specific user ID to verify} {--fix : Automatically fix discrepancies} {--rebuild : Full rebuild from transactions}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Verify & fix sync between user_balance and user_balance_transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        $shouldFix = $this->option('fix');
        $shouldRebuild = $this->option('rebuild');

        if ($shouldRebuild) {
            $this->info('🔨 Rebuilding all balances from transactions...');
            $results = BalanceService::rebuildAllBalances();
            $this->displayRebuildResults($results);
            return 0;
        }

        $this->info('🔍 Verifying balance sync...' . ($userId ? " (User ID: {$userId})" : ''));

        $results = BalanceService::verifyAndFixBalance($userId);

        // Display results
        $this->newLine();
        $this->info("Checked: {$results['checked']} users");
        $this->info("✅ Synced: {$results['synced']} users");
        $this->warn("⚠️  Fixed: {$results['fixed']} users");

        if (!empty($results['errors'])) {
            $this->error("❌ Errors: " . count($results['errors']));
            foreach ($results['errors'] as $error) {
                $this->error("  User {$error['user_id']}: {$error['error']}");
            }
        }

        // Show discrepancies if any
        $discrepancies = array_filter($results['details'], fn($d) => $d['status'] === 'MISMATCH');
        if (!empty($discrepancies)) {
            $this->newLine();
            $this->error('Discrepancies found:');
            $this->table(
                ['User ID', 'Stored Balance', 'Actual Balance', 'Difference'],
                array_map(function ($d) {
                    return [
                        $d['user_id'],
                        number_format($d['stored_balance'] ?? 0, 0),
                        number_format($d['actual_balance'] ?? 0, 0),
                        number_format(abs(($d['actual_balance'] ?? 0) - ($d['stored_balance'] ?? 0)), 0),
                    ];
                }, $discrepancies)
            );
        }

        return 0;
    }

    /**
     * Display rebuild results
     */
    private function displayRebuildResults(array $results)
    {
        $this->newLine();
        $this->info("✅ Rebuilt: {$results['rebuilt']} users");

        if (!empty($results['errors'])) {
            $this->error("❌ Errors: " . count($results['errors']));
            foreach ($results['errors'] as $error) {
                $this->error("  User {$error['user_id']}: {$error['error']}");
            }
        }
    }
}
