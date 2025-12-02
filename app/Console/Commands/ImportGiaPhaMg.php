<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GiaPha;
use App\Models\GiaPhaMg;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportGiaPhaMg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:giaphamg {--batch=1000 : Number of records per batch} {--truncate : Clear MongoDB collection first} {--no-check : Skip duplicate checking for faster import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from MySQL GiaPha table to MongoDB GiaPhaMg collection';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting import from MySQL GiaPha to MongoDB GiaPhaMg...');
        $this->info('=====================================================');

        try {
            // Step 1: Check MySQL connection and table
            $this->info('1. Checking MySQL connection and table structure...');
            
            $mysqlConnection = config('database.connections.mysql.database');
            $this->info("   MySQL Database: {$mysqlConnection}");
            
            // Get table structure from GiaPha model
            $giaPhaSample = GiaPha::first();
            if ($giaPhaSample) {
                $columns = array_keys($giaPhaSample->toArray());
                $this->info("   Found " . count($columns) . " columns in GiaPha table:");
                foreach ($columns as $column) {
                    $this->info("     - {$column}");
                }
            } else {
                // Try to get table structure directly
                $tableName = (new GiaPha())->getTable();
                $columns = $this->getTableColumns($tableName);
                $this->info("   Found " . count($columns) . " columns in {$tableName} table:");
                foreach ($columns as $column) {
                    $this->info("     - {$column->Field} ({$column->Type})");
                }
            }

            // Step 2: Check MongoDB connection
            $this->info('2. Checking MongoDB connection...');
            $mongoConnection = (new GiaPhaMg())->getConnectionName();
            $this->info("   MongoDB Connection: {$mongoConnection}");
            $this->info("   MongoDB Collection: " . (new GiaPhaMg())->getTable());

            // Step 3: Truncate if requested
            if ($this->option('truncate')) {
                $this->info('3. Truncating MongoDB collection...');
                if ($this->confirm('Are you sure you want to clear all data in GiaPhaMg collection?')) {
                    GiaPhaMg::truncate();
                    $this->info('   ✓ Collection cleared');
                } else {
                    $this->info('   Skipped truncation');
                }
            }

            // Step 4: Count source records
            $totalRecords = GiaPha::count();
            $this->info("4. Found {$totalRecords} records in MySQL GiaPha table");

            if ($totalRecords === 0) {
                $this->warn('No records found to import!');
                return Command::SUCCESS;
            }

            // Step 5: Import data in batches
            $batchSize = (int) $this->option('batch');
            $noCheck = $this->option('no-check');
            $this->info("5. Starting import in batches of {$batchSize}...");
            if ($noCheck) {
                $this->warn("   Duplicate checking disabled for faster import");
            }

            $bar = $this->output->createProgressBar($totalRecords);
            $bar->start();

            $imported = 0;
            $errors = 0;
            $skipped = 0;

            GiaPha::chunk($batchSize, function ($records) use (&$imported, &$errors, &$skipped, $bar, $noCheck) {
                $batchData = [];
                
                foreach ($records as $record) {
                    try {
                        // Convert MySQL record to array
                        $data = $record->toArray();
                        
                        // Handle special fields
                        if (isset($data['id'])) {
                            $data['idsql'] = $data['id']; // Keep original MySQL ID as idsql
                            unset($data['id']); // Let MongoDB generate new _id
                        }

                        // Add timestamps if not exist
                        if (!isset($data['created_at'])) {
                            $data['created_at'] = now();
                        }
                        if (!isset($data['updated_at'])) {
                            $data['updated_at'] = now();
                        }

                        // Check if record already exists (only if not skipping checks)
                        if (!$noCheck && isset($data['idsql'])) {
                            $existing = GiaPhaMg::where('idsql', $data['idsql'])->first();
                            if ($existing) {
                                $skipped++;
                                $bar->advance();
                                continue;
                            }
                        }

                        $batchData[] = $data;

                    } catch (\Exception $e) {
                        $errors++;
                        $this->error("Error preparing record ID {$record->id}: " . $e->getMessage());
                    }

                    $bar->advance();
                }

                // Bulk insert the batch
                if (!empty($batchData)) {
                    try {
                        // Use model's insert method for bulk insert
                        GiaPhaMg::insert($batchData);
                        $imported += count($batchData);
                    } catch (\Exception $e) {
                        $this->error("Bulk insert error: " . $e->getMessage());
                        // Fallback to individual inserts
                        foreach ($batchData as $data) {
                            try {
                                GiaPhaMg::create($data);
                                $imported++;
                            } catch (\Exception $e2) {
                                $errors++;
                            }
                        }
                    }
                }
            });

            $bar->finish();
            $this->newLine(2);

            // Step 6: Summary
            $this->info('✅ Import completed!');
            $this->info("   Imported: {$imported} records");
            $this->info("   Skipped: {$skipped} records (already exist)");
            if ($errors > 0) {
                $this->warn("   Errors: {$errors} records failed");
            }

            // Verify import
            $mongoCount = GiaPhaMg::count();
            $this->info("   MongoDB collection now has: {$mongoCount} records");

            $this->newLine();
            $this->info('🎯 Next steps:');
            $this->info('- Check imported data: GiaPhaMg::count()');
            $this->info('- Sample record: GiaPhaMg::first()');
            $this->info('- Create CRUD interface similar to TestMongo1');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Import failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }

    /**
     * Get table columns information
     */
    private function getTableColumns($tableName)
    {
        try {
            return DB::connection('mysql')->select("DESCRIBE {$tableName}");
        } catch (\Exception $e) {
            $this->error("Could not describe table {$tableName}");
            $this->error('Available tables:');
            $tables = DB::connection('mysql')->select('SHOW TABLES');
            foreach ($tables as $table) {
                $tableNameFound = array_values((array) $table)[0];
                $this->error("  - {$tableNameFound}");
            }
            throw $e;
        }
    }
} 