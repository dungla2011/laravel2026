<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixPostgresSequences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:fix-sequences 
                            {--table= : Fix only specific table}
                            {--column=id : Column name for specific table}
                            {--dry-run : Check without making changes}
                            {--silent : No verbose output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix PostgreSQL sequences after pgloader import from MySQL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $table = $this->option('table');
        $column = $this->option('column');
        $dryRun = $this->option('dry-run');
        $silent = $this->option('silent');

        $this->newLine();
        $this->info('╔═══════════════════════════════════════════════════════════════╗');
        $this->info('║      FIX POSTGRESQL SEQUENCES AFTER PGLOADER IMPORT          ║');
        $this->info('╚═══════════════════════════════════════════════════════════════╝');
        $this->newLine();

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        if ($table) {
            // Fix specific table
            $this->line("Fixing sequence for table: <fg=cyan>{$table}</>");
            $this->newLine();

            $result = fixSequenceForTable($table, $column, !$silent);

            if ($result) {
                $this->newLine();
                $this->info('✅ Successfully fixed sequence for ' . $table);
            } else {
                $this->newLine();
                $this->error('❌ Failed to fix sequence for ' . $table);
                return 1;
            }

        } else {
            // Fix all sequences
            $stats = fixAllPostgresSequences(!$silent, $dryRun);

            $this->newLine();
            $this->info('═══════════════════════════════════════════════════════════════');
            $this->info('SUMMARY:');
            $this->info('═══════════════════════════════════════════════════════════════');
            
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total tables', $stats['total']],
                    ['✅ Fixed', $stats['fixed']],
                    ['✓ OK/Skipped', $stats['skipped']],
                    ['❌ Errors', $stats['errors']],
                ]
            );

            if ($stats['errors'] > 0) {
                $this->newLine();
                $this->warn('⚠️  There were errors. Check the output above for details.');
                return 1;
            }

            if ($dryRun && $stats['fixed'] > 0) {
                $this->newLine();
                $this->comment('💡 Run without --dry-run to apply fixes');
            }
        }

        $this->newLine();
        $this->info('✨ Done!');
        $this->newLine();

        return 0;
    }
}
