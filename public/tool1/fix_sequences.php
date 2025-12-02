<?php
/**
 * Fix All PostgreSQL Sequences - Simple Usage
 * Run this script after pgloader import from MySQL
 */

require_once __DIR__ . '/../../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "<pre>";
echo "╔═════════════════════════════════════════════════════════════════╗\n";
echo "║         FIX ALL POSTGRESQL SEQUENCES AFTER PGLOADER            ║\n";
echo "╚═════════════════════════════════════════════════════════════════╝\n\n";

// Example 1: Fix all sequences with verbose output
echo "=== OPTION 1: Fix All Sequences (Verbose) ===\n\n";
$stats = fixAllPostgresSequences($verbose = true, $dryRun = false);

// Example 2: Dry run to see what would be fixed (without making changes)
// echo "=== OPTION 2: Dry Run (Check Only) ===\n\n";
// $stats = fixAllPostgresSequences($verbose = true, $dryRun = true);

// Example 3: Silent mode (no output, just return stats)
// $stats = fixAllPostgresSequences($verbose = false, $dryRun = false);
// print_r($stats);

// Example 4: Fix a specific table
// echo "\n=== OPTION 4: Fix Single Table ===\n\n";
// fixSequenceForTable('model_meta_infos', 'id', $verbose = true);

echo "\n";
echo "╔═════════════════════════════════════════════════════════════════╗\n";
echo "║                          COMPLETED!                             ║\n";
echo "╚═════════════════════════════════════════════════════════════════╝\n";

echo "\n📋 Usage Examples:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "1️⃣  Fix all sequences:\n";
echo "   \$stats = fixAllPostgresSequences();\n\n";

echo "2️⃣  Dry run (check without fixing):\n";
echo "   \$stats = fixAllPostgresSequences(true, true);\n\n";

echo "3️⃣  Silent mode:\n";
echo "   \$stats = fixAllPostgresSequences(false);\n\n";

echo "4️⃣  Fix single table:\n";
echo "   fixSequenceForTable('users', 'id');\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n💡 Pro Tips:\n";
echo "   • Run this after every pgloader import\n";
echo "   • Add to deployment script: php public/tool1/fix_sequences.php\n";
echo "   • Check with dry run first if unsure\n";
echo "   • Functions are globally available (autoloaded)\n\n";

echo "</pre>";
