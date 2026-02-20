<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "/var/www/html/vendor/autoload.php";
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Get user by email
$targetEmail = 'khanhdh389@gmail.com';
$targetUser = \App\Models\User::where('email', $targetEmail)->first();

if (!$targetUser) {
    die("<div style='padding: 50px; text-align: center; font-size: 18px; color: #f44336;'>User with email '{$targetEmail}' not found!</div>");
}

// Number format for VND currency
$decimal = 0; // No decimal places (VND has no cents like USD)
$dec_separator = ','; // Decimal separator (not used when $decimal = 0)
$thousands_separator = '.'; // VND uses dot (.) as thousand separator, not comma

// Get all VPS usages for this user (grouped by instance for display)
$usages = \App\Models\VpsUsage::where('user_id', $targetUser->id)
    ->whereNull('deleted_at')
    ->orderBy('instance_id', 'ASC')
    ->orderBy('timestamp_minute', 'ASC')
    ->get();

$results = [];
$totalCost = 0;
$instanceTotals = [];
$hasAnyPriceConfig = false; // Track if any instance uses price_config (no price_month)
$hasAnyPriceMonth = false; // Track if any instance has price_month > 0

foreach ($usages as $usage) {
    // Get instance info
    $instance = \App\Models\VpsInstance::find($usage->instance_id);
    if (!$instance) continue;

    $instanceName = $instance->name;
    $instanceId = $instance->id;

    // Parse price_config (only show if no price_month)
    $priceConfig = $usage->price_config ? json_decode($usage->price_config, true) : null;
    $configDisplay = '';
    $priceMonth = $usage->price_month; // Get from vps_usages table

    // Track if any instance uses price_config or price_month
    if (!$priceMonth) {
        $hasAnyPriceConfig = true;
    } else {
        $hasAnyPriceMonth = true;
    }

    // Only show price config if no price_month
    if (!$priceMonth && $priceConfig) {
        $configDisplay = sprintf(
            'CPU: %.1fK, RAM: %.1fK, Disk: %.1fK, IP: %.1fK',
            $priceConfig['n_cpu_core_price'] ?? 0,
            $priceConfig['n_ram_gb_price'] ?? 0,
            $priceConfig['n_gb_disk_price'] ?? 0,
            $priceConfig['n_ip_address_price'] ?? 0
        );
    }

    // Calculate fee: use price_month from vps_usages if available, otherwise use calculateFee()
    if ($priceMonth && $priceMonth > 0) {
        // Fixed monthly price - calculate based on duration
        $pricePerMonth = floatval($priceMonth);

        // Calculate duration from created_at to timestamp_minute
        $createdTime = new DateTime($usage->created_at);
        $timestampTime = new DateTime($usage->timestamp_minute);
        $interval = $createdTime->diff($timestampTime);
        $durationMinutes = ($interval->days * 1440) + ($interval->h * 60) + $interval->i;

        // Calculate fee based on monthly rate
        $fee = $pricePerMonth * ($durationMinutes / 43200); // 43200 = 30 days * 1440 minutes
        $fee = round($fee, 0); // Round to integer, no decimals

        // Convert duration to days/hours/minutes
        $days = floor($durationMinutes / 1440);
        $remainingMinutes = $durationMinutes % 1440;
        $hours = floor($remainingMinutes / 60);
        $minutes = $remainingMinutes % 60;

        $durationText = '';
        if ($days > 0) {
            $durationText .= $days . ' ngày ';
        }
        if ($hours > 0 || $days > 0) {
            $durationText .= $hours . ' giờ ';
        }
        $durationText .= $minutes . ' phút';

        $feeText = sprintf(
            "Fixed: %s K/month × %s = %s K",
            number_format($pricePerMonth, 0, ',', '.'),
            $durationText,
            number_format($fee, 0, ',', '.')
        );
    } else {
        // Use calculateFee() method from VpsUsage model
        $feeResult = $usage->calculateFee();
        $fee = $feeResult['fee'];
        $feeText = $feeResult['text'];
    }

    // POWERED_OFF = no charge
    if (strtoupper($usage->power_state) === 'POWERED_OFF') {
        $fee = 0;
        $feeText .= " (POWERED_OFF = No charge)";
    }

    $totalCost += $fee;

    // Calculate time usage duration (from created_at to timestamp_minute)
    $createdTime = new DateTime($usage->created_at);
    $timestampTime = new DateTime($usage->timestamp_minute);
    $interval = $createdTime->diff($timestampTime);
    $timeUsage = '';
    if ($interval->days > 0) {
        $timeUsage .= $interval->days . ' ngày ';
    }
    if ($interval->h > 0 || $interval->days > 0) {
        $timeUsage .= $interval->h . ' giờ ';
    }
    $timeUsage .= $interval->i . ' phút';

    // Track totals per instance
    if (!isset($instanceTotals[$instanceId])) {
        $instanceTotals[$instanceId] = [
            'name' => $instanceName,
            'total' => 0,
            'count' => 0
        ];
    }
    $instanceTotals[$instanceId]['total'] += $fee;
    $instanceTotals[$instanceId]['count']++;

    $results[] = [
        'instance_id' => $instanceId,
        'instance_name' => $instanceName,
        'timestamp' => $usage->timestamp_minute,
        'created_at' => $usage->created_at,
        'time_usage' => $timeUsage,
        'cpu' => $usage->cpu,
        'ram_gb' => $usage->ram_gb,
        'disk_gb' => $usage->disk_gb,
        'ip_count' => $usage->number_ip_address,
        'price_month' => $priceMonth,
        'price_config' => $configDisplay,
        'calculated_fee' => $fee,
        'fee_text' => $feeText,
        'power_state' => $usage->power_state
    ];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VPS Cost Calculation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
        }

        .summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .summary h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .summary .total {
            font-size: 36px;
            font-weight: bold;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #667eea;
        }

        .stat-card .label {
            color: #666;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .stat-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #f0f0f0;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #ddd;
            position: sticky;
            top: 0;
            font-size: 13px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .status-active {
            color: #4caf50;
            font-weight: 600;
        }

        .status-deleted {
            color: #f44336;
            font-weight: 600;
        }

        .status-inactive {
            color: #ff9800;
            font-weight: 600;
        }

        .cost {
            text-align: right;
            font-weight: 600;
            color: #667eea;
        }

        .billing-monthly {
            background: #e3f2fd;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            color: #1976d2;
            display: inline-block;
        }

        .billing-usage {
            background: #f3e5f5;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            color: #7b1fa2;
            display: inline-block;
        }

        .new-instance {
            border-top: 3px solid #2196F3;
        }

        .details {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        .refresh-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .refresh-btn:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>💰 VPS Cost Calculation Report</h1>
        <p style="color: #666; margin-bottom: 20px;">
            Chi tiết từng record vps_usages và tổng theo instance
            <br>
            <strong>User: <?= htmlspecialchars($targetEmail) ?> (ID: <?= $targetUser->id ?>)</strong>
        </p>

        <button class="refresh-btn" onclick="location.reload()">🔄 Refresh Data</button>

        <div class="summary">
            <h2>Total Cost</h2>
            <div class="total"><?= number_format($totalCost, $decimal, $dec_separator, $thousands_separator) ?> K VND</div>
            <div style="margin-top: 10px; opacity: 0.9;">
                ≈ <?= number_format($totalCost / 1000, $decimal, $dec_separator, $thousands_separator) ?> Million VND
            </div>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="label">Total Usage Records</div>
                <div class="value"><?= number_format(count($results), 0, $dec_separator, $thousands_separator) ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Unique Instances</div>
                <div class="value"><?= count($instanceTotals) ?></div>
            </div>
        </div>

        <h2 style="margin-top: 30px;">Instance Totals Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Instance ID</th>
                    <th>Instance Name</th>
                    <th>Record Count</th>
                    <th style="text-align: right;">Total Cost (K VND)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($instanceTotals as $instId => $instData): ?>
                <tr>
                    <td><?= $instId ?></td>
                    <td><?= htmlspecialchars($instData['name']) ?></td>
                    <td><?= number_format($instData['count'], 0, $dec_separator, $thousands_separator) ?></td>
                    <td class="cost"><?= number_format($instData['total'], $decimal, $dec_separator, $thousands_separator) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2 style="margin-top: 30px;">All VPS Usage Records (Detail)</h2>
        <table>
            <thead>
                <tr>
                    <th>Instance ID</th>
                    <th>Instance Name</th>
                    <th>Created At</th>
                    <th>Timestamp</th>
                    <th>Time Usage</th>
                    <th>Config</th>
                    <?php if ($hasAnyPriceMonth): ?>
                    <th>Price Month</th>
                    <?php endif; ?>
                    <?php if ($hasAnyPriceConfig): ?>
                    <th>Price Config</th>
                    <?php endif; ?>
                    <th>Power</th>
                    <th style="text-align: right;">Fee (K VND)</th>
                    <th>Formula</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $currentInstanceId = null;
                foreach ($results as $row):
                    $isNewInstance = $currentInstanceId !== $row['instance_id'];
                    $currentInstanceId = $row['instance_id'];
                ?>
                <tr <?= $isNewInstance ? 'class="new-instance"' : '' ?>>
                    <td><?= $row['instance_id'] ?></td>
                    <td><?= htmlspecialchars($row['instance_name']) ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($row['timestamp'])) ?></td>
                    <td><small><?= $row['time_usage'] ?></small></td>
                    <td>
                        CPU: <?= $row['cpu'] ?><br>
                        RAM: <?= $row['ram_gb'] ?> GB<br>
                        Disk: <?= $row['disk_gb'] ?> GB<br>
                        IP: <?= $row['ip_count'] ?>
                    </td>
                    <?php if ($hasAnyPriceMonth): ?>
                    <td>
                        <?php if ($row['price_month'] && strtoupper($row['power_state']) !== 'POWERED_OFF'): ?>
                        <strong><?= number_format($row['price_month'], 0, $dec_separator, $thousands_separator) ?> K</strong>
                        <?php elseif (strtoupper($row['power_state']) === 'POWERED_OFF'): ?>
                        <small style="color: #999;">-</small>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                    <?php if ($hasAnyPriceConfig): ?>
                    <td>
                        <?php if (strtoupper($row['power_state']) !== 'POWERED_OFF'): ?>
                        <small><?= htmlspecialchars($row['price_config']) ?></small>
                        <?php else: ?>
                        <small style="color: #999;">-</small>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                    <td>
                        <span style="color: <?= strtoupper($row['power_state']) === 'POWERED_ON' ? '#4caf50' : '#f44336' ?>; font-weight: 600;">
                            <?= $row['power_state'] ?>
                        </span>
                        <?php if (strtoupper($row['power_state']) === 'POWERED_OFF'): ?>
                        <br><small style="color: #999;">(No charge)</small>
                        <?php endif; ?>
                    </td>
                    <td class="cost"><?= number_format($row['calculated_fee'], $decimal, $dec_separator, $thousands_separator) ?></td>
                    <td>
                        <?php if (strtoupper($row['power_state']) !== 'POWERED_OFF'): ?>
                        <small style="color: #666;"><?= htmlspecialchars($row['fee_text']) ?></small>
                        <?php else: ?>
                        <small style="color: #999;">-</small>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="background: #e3f2fd; padding: 20px; border-radius: 8px; margin-top: 30px;">

            <ul style="margin-top: 10px; margin-left: 20px;">
                <li>Report shows ALL vps_usages records (snapshots) for user: <strong><?= htmlspecialchars($targetEmail) ?></strong></li>
                <li>Each record represents VPS configuration at a specific timestamp_minute</li>
                <li>calculated_fee for each record is calculated from its price_config: CPU × n_cpu_core_price + RAM × n_ram_gb_price + Disk × n_gb_disk_price + IP × n_ip_address_price</li>
                <li>Instance Totals section shows sum of all records grouped by instance_id</li>
                <li>Blue border separates different instances in detailed list</li>
                <li>Only includes non-deleted usage records (deleted_at IS NULL)</li>
                <li>Generated at: <?= date('Y-m-d H:i:s') ?></li>
            </ul>
        </div>
    </div>
</body>
</html>
                <li>Generated at: <?= date('Y-m-d H:i:s') ?></li>
            </ul>
        </div>
    </div>
</body>
</html>
