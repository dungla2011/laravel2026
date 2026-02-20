<?php

//
//$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
error_reporting(E_ALL);
ini_set('display_errors', 1);
//
//
require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);



use App\Models\VpsUsage;
use Illuminate\Support\Facades\DB;

// Check admin permission
if(!isAdminCookie()){
    die('Not admin!');
}

// Read POST data directly from php://input if Laravel consumed it
$rawInput = file_get_contents('php://input');
parse_str($rawInput, $postData);

// Check if this is a confirmation - support both GET and POST
$isConfirmed = (isset($_GET['confirm']) && $_GET['confirm'] == '1') ||
               (isset($postData['confirm_merge']) && $postData['confirm_merge'] == '1');

// DEBUG OUTPUT
echo "<div style='background: #fff3cd; padding: 20px; margin: 20px; border: 2px solid #856404; border-radius: 5px;'>";
echo "<h3>🐛 DEBUG INFO</h3>";
echo "<p><strong>REQUEST_METHOD:</strong> " . $_SERVER['REQUEST_METHOD'] . "</p>";
echo "<p><strong>\$_GET:</strong> <pre>" . print_r($_GET, true) . "</pre></p>";
echo "<p><strong>Raw input:</strong> <pre>" . htmlspecialchars($rawInput) . "</pre></p>";
echo "<p><strong>Parsed POST:</strong> <pre>" . print_r($postData, true) . "</pre></p>";
echo "<p><strong>\$isConfirmed:</strong> " . ($isConfirmed ? 'TRUE' : 'FALSE') . "</p>";
echo "</div>";

try {
    // Get all VPS usages, ordered by bios_uuid, name, config, and time
    $allUsages = VpsUsage::orderBy('bios_uuid')
        ->orderBy('name')
        ->orderBy('cpu')
        ->orderBy('ram_gb')
        ->orderBy('disk_gb')
        ->orderBy('created_at')
        ->get();

    // Group by VPS identity (bios_uuid + name + config)
    $groupedByVps = [];

    foreach ($allUsages as $usage) {
        // Create unique key for this VPS configuration
        $vpsKey = sprintf(
            "%s|%s|%d|%d|%d",
            $usage->bios_uuid ?? 'null',
            $usage->name ?? 'null',
            $usage->cpu ?? 0,
            $usage->ram_gb ?? 0,
            $usage->disk_gb ?? 0
        );

        if (!isset($groupedByVps[$vpsKey])) {
            $groupedByVps[$vpsKey] = [];
        }

        $groupedByVps[$vpsKey][] = $usage;
    }

    // Build merge plan
    $mergePlan = [];
    $totalMerged = 0;
    $totalDeleted = 0;

    foreach ($groupedByVps as $vpsKey => $usages) {
        if (count($usages) <= 1) {
            continue; // Nothing to merge
        }

        $prevUsage = null;

        foreach ($usages as $currentUsage) {
            if ($prevUsage === null) {
                $prevUsage = $currentUsage;
                continue;
            }

            // Check if power_state is the same as previous record
            if ($prevUsage->power_state === $currentUsage->power_state) {
                // Same power state - will merge
                $mergePlan[] = [
                    'action' => 'merge',
                    'keep_id' => $prevUsage->id,
                    'delete_id' => $currentUsage->id,
                    'vps_key' => $vpsKey,
                    'power_state' => $prevUsage->power_state,
                    'keep_created' => $prevUsage->created_at,
                    'delete_created' => $currentUsage->created_at,
                    'keep_count' => $prevUsage->count_update_status,
                    'delete_count' => $currentUsage->count_update_status,
                ];
                $totalMerged++;
                $totalDeleted++;
            } else {
                // Different power state - mark boundary
                $mergePlan[] = [
                    'action' => 'boundary',
                    'from_id' => $prevUsage->id,
                    'to_id' => $currentUsage->id,
                    'from_state' => $prevUsage->power_state,
                    'to_state' => $currentUsage->power_state,
                ];
                $prevUsage = $currentUsage;
            }
        }
    }

    // DEBUG: Show merge plan count
    echo "<div style='background: #d1ecf1; padding: 15px; margin: 20px; border: 2px solid #0c5460; border-radius: 5px;'>";
    echo "<h3>📊 MERGE PLAN STATS</h3>";
    echo "<p><strong>Total merge operations:</strong> " . count($mergePlan) . "</p>";
    echo "<p><strong>Records to merge:</strong> $totalMerged</p>";
    echo "<p><strong>Records to delete:</strong> $totalDeleted</p>";
    echo "<p><strong>VPS groups with > 1 record:</strong> " . count(array_filter($groupedByVps, function($v) { return count($v) > 1; })) . "</p>";
    echo "</div>";

    // If not confirmed, show preview table
    if (!$isConfirmed) {
        echo "<div style='background: #e8f5e9; padding: 15px; margin: 20px; border: 2px solid #4CAF50; border-radius: 5px;'>";
        echo "<h3>👁️ PREVIEW MODE</h3>";
        echo "<p>You are viewing the preview. Click the button below to execute.</p>";
        echo "</div>";
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>VPS Usage Consolidation - Preview</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
                .container { max-width: 1400px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
                .stats { background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .stats h3 { margin-top: 0; color: #2e7d32; }
                .confirm-btn {
                    background: #4CAF50;
                    color: white;
                    padding: 15px 30px;
                    border: none;
                    border-radius: 5px;
                    font-size: 16px;
                    cursor: pointer;
                    margin: 20px 0;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                }
                .confirm-btn:hover { background: #45a049; }
                .confirm-btn:disabled { background: #ccc; cursor: not-allowed; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th { background: #4CAF50; color: white; padding: 12px; text-align: left; position: sticky; top: 0; }
                td { padding: 10px; border-bottom: 1px solid #ddd; }
                tr:hover { background: #f5f5f5; }
                .merge-row { background: #fff3cd; }
                .boundary-row { background: #d1ecf1; border-top: 2px solid #0c5460; }
                .action-merge { color: #856404; font-weight: bold; }
                .action-boundary { color: #0c5460; font-weight: bold; }
                .power-on { color: #4CAF50; font-weight: bold; }
                .power-off { color: #f44336; font-weight: bold; }
                .loading { display: none; text-align: center; margin: 20px 0; }
                .loading.active { display: block; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>🔄 VPS Usage Consolidation - Preview</h1>

                <div class="stats">
                    <h3>📊 Statistics</h3>
                    <p><strong>Total Records:</strong> <?= $allUsages->count() ?></p>
                    <p><strong>VPS Groups:</strong> <?= count($groupedByVps) ?></p>
                    <p><strong>Records to Merge:</strong> <?= $totalMerged ?></p>
                    <p><strong>Records to Delete:</strong> <?= $totalDeleted ?></p>
                </div>

                <a href="?confirm=1" class="confirm-btn" id="confirmBtn" style="display: inline-block; text-decoration: none; text-align: center;">
                    ✅ Confirm and Execute Merge
                </a>

                <div class="loading" id="loading">
                    <h3>⏳ Processing merge operation...</h3>
                    <p>Please wait, this may take a few moments.</p>
                </div>

                <h2>📋 Merge Plan (<?= count($mergePlan) ?> operations)</h2>

                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Action</th>
                            <th>Keep ID</th>
                            <th>Delete ID</th>
                            <th>VPS Key</th>
                            <th>Power State</th>
                            <th>Keep Created</th>
                            <th>Delete Created</th>
                            <th>Keep Count</th>
                            <th>Delete Count</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mergePlan as $idx => $plan): ?>
                            <?php if ($plan['action'] === 'merge'): ?>
                                <tr class="merge-row">
                                    <td><?= $idx + 1 ?></td>
                                    <td class="action-merge">MERGE</td>
                                    <td><?= $plan['keep_id'] ?></td>
                                    <td><?= $plan['delete_id'] ?></td>
                                    <td style="font-size: 11px;"><?= htmlspecialchars($plan['vps_key']) ?></td>
                                    <td class="<?= $plan['power_state'] === 'POWERED_ON' ? 'power-on' : 'power-off' ?>">
                                        <?= $plan['power_state'] ?>
                                    </td>
                                    <td><?= $plan['keep_created'] ?></td>
                                    <td><?= $plan['delete_created'] ?></td>
                                    <td><?= $plan['keep_count'] ?></td>
                                    <td><?= $plan['delete_count'] ?></td>
                                    <td>Merge into ID <?= $plan['keep_id'] ?>, delete ID <?= $plan['delete_id'] ?></td>
                                </tr>
                            <?php else: ?>
                                <tr class="boundary-row">
                                    <td><?= $idx + 1 ?></td>
                                    <td class="action-boundary">BOUNDARY</td>
                                    <td colspan="3">Power state changed</td>
                                    <td>
                                        <span class="<?= $plan['from_state'] === 'POWERED_ON' ? 'power-on' : 'power-off' ?>">
                                            <?= $plan['from_state'] ?>
                                        </span>
                                        →
                                        <span class="<?= $plan['to_state'] === 'POWERED_ON' ? 'power-on' : 'power-off' ?>">
                                            <?= $plan['to_state'] ?>
                                        </span>
                                    </td>
                                    <td colspan="4"></td>
                                    <td>Keep both: ID <?= $plan['from_id'] ?> and ID <?= $plan['to_id'] ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <script>
                document.getElementById('confirmBtn').addEventListener('click', function() {
                    this.style.opacity = '0.5';
                    this.style.pointerEvents = 'none';
                    document.getElementById('loading').classList.add('active');
                });
            </script>
        </body>
        </html>
        <?php
        exit;
    }

    // EXECUTE MODE - User confirmed
    echo "<div style='background: #fff3cd; padding: 20px; margin: 20px; border: 3px solid #856404; border-radius: 5px;'>";
    echo "<h2>⚙️ EXECUTE MODE - RUNNING MERGE...</h2>";
    echo "<p>Starting transaction and processing records...</p>";
    echo "</div>";

    DB::beginTransaction();

    $executedMerges = 0;
    $executedDeletes = 0;

    foreach ($groupedByVps as $vpsKey => $usages) {
        if (count($usages) <= 1) {
            continue;
        }

        echo "<div style='background: #e7f3ff; padding: 10px; margin: 10px; border-left: 4px solid #2196F3;'>";
        echo "Processing VPS: <strong>" . htmlspecialchars($vpsKey) . "</strong> (" . count($usages) . " records)<br>";

        $prevUsage = null;

        foreach ($usages as $currentUsage) {
            if ($prevUsage === null) {
                $prevUsage = $currentUsage;
                echo "→ First record ID: {$prevUsage->id} (power: {$prevUsage->power_state})<br>";
                continue;
            }

            if ($prevUsage->power_state === $currentUsage->power_state) {
                echo "→ <span style='color: green;'>✅ MERGING</span> ID {$currentUsage->id} into ID {$prevUsage->id} (same power: {$prevUsage->power_state})<br>";

                // Update previous record
                $prevUsage->lastest_time_the_same = $currentUsage->lastest_time_the_same ?? $currentUsage->created_at;
                $prevUsage->count_update_status += ($currentUsage->count_update_status + 1);

                if ($currentUsage->list_ip_address) {
                    $prevUsage->list_ip_address = $currentUsage->list_ip_address;
                }
                if ($currentUsage->last_found_ip) {
                    $prevUsage->last_found_ip = $currentUsage->last_found_ip;
                }
                if ($currentUsage->mac_address) {
                    $prevUsage->mac_address = $currentUsage->mac_address;
                }

                // Recalculate fee
                $createdAt = strtotime($prevUsage->created_at);
                $latestTime = strtotime($prevUsage->lastest_time_the_same);
                $durationMinutes = max(0, floor(($latestTime - $createdAt) / 60));

                $priceConfig = json_decode($prevUsage->price_config, true);
                if ($priceConfig) {
                    $feeCpu = ($prevUsage->power_state === 'POWERED_OFF') ? 0 : $prevUsage->cpu;
                    $feeRam = ($prevUsage->power_state === 'POWERED_OFF') ? 0 : $prevUsage->ram_gb;

                    $chargeableIpCount = \App\Services\VpsUsageFeeService::countChargeableIPs($prevUsage->list_ip_address);

                    $calculatedFee = \App\Services\VpsUsageFeeService::calculateFee(
                        $priceConfig,
                        $feeCpu,
                        $feeRam,
                        $prevUsage->disk_gb,
                        $durationMinutes,
                        $chargeableIpCount
                    );

                    $prevUsage->calculated_fee = $calculatedFee;
                }

                $prevUsage->save();
                $currentUsage->forceDelete();

                echo "   → Saved ID {$prevUsage->id}, Deleted ID {$currentUsage->id}<br>";

                $executedMerges++;
                $executedDeletes++;
            } else {
                echo "→ <span style='color: orange;'>🔄 BOUNDARY</span> Power changed: {$prevUsage->power_state} → {$currentUsage->power_state} (keeping ID {$currentUsage->id})<br>";
                $prevUsage = $currentUsage;
            }
        }

        echo "</div>";
    }

    DB::commit();

    echo "<div style='background: #d4edda; padding: 20px; margin: 20px; border: 3px solid #28a745; border-radius: 5px;'>";
    echo "<h2>✅ TRANSACTION COMMITTED</h2>";
    echo "<p><strong>Executed Merges:</strong> $executedMerges</p>";
    echo "<p><strong>Executed Deletes:</strong> $executedDeletes</p>";
    echo "</div>";

    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>VPS Usage Consolidation - Complete</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
            .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
            h1 { color: #4CAF50; }
            .success { background: #d4edda; color: #155724; padding: 20px; border-radius: 5px; margin: 20px 0; border: 1px solid #c3e6cb; }
            .stats { margin: 30px 0; }
            .stat-item { font-size: 18px; margin: 10px 0; }
            .btn-back {
                background: #2196F3;
                color: white;
                padding: 12px 24px;
                text-decoration: none;
                border-radius: 5px;
                display: inline-block;
                margin-top: 20px;
            }
            .btn-back:hover { background: #0b7dda; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>✅ Consolidation Complete!</h1>

            <div class="success">
                <h2>🎉 Success!</h2>
                <p>VPS usage records have been successfully consolidated.</p>
            </div>

            <div class="stats">
                <div class="stat-item"><strong>Total Records Processed:</strong> <?= $allUsages->count() ?></div>
                <div class="stat-item"><strong>VPS Groups:</strong> <?= count($groupedByVps) ?></div>
                <div class="stat-item"><strong>Records Merged:</strong> <?= $executedMerges ?></div>
                <div class="stat-item"><strong>Records Deleted:</strong> <?= $executedDeletes ?></div>
            </div>

            <a href="javascript:window.location.reload()" class="btn-back">🔄 Run Again</a>
            <a href="/admin/model/VpsUsage" class="btn-back">📋 View Records</a>
        </div>
    </body>
    </html>
    <?php

} catch (\Exception $e) {
    if ($isConfirmed) {
        DB::rollBack();
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Error</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
            .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
            h1 { color: #f44336; }
            .error { background: #f8d7da; color: #721c24; padding: 20px; border-radius: 5px; margin: 20px 0; border: 1px solid #f5c6cb; }
            pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
            .btn-back {
                background: #2196F3;
                color: white;
                padding: 12px 24px;
                text-decoration: none;
                border-radius: 5px;
                display: inline-block;
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>❌ Error Occurred</h1>

            <div class="error">
                <h3>Error Message:</h3>
                <p><?= htmlspecialchars($e->getMessage()) ?></p>
            </div>

            <h3>Stack Trace:</h3>
            <pre><?= htmlspecialchars($e->getTraceAsString()) ?></pre>

            <a href="javascript:history.back()" class="btn-back">← Go Back</a>
        </div>
    </body>
    </html>
    <?php
}
