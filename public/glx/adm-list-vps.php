<?php
//$time = microtime(1);
//use App\Models\User_Meta;
//
//$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'glx.com.vn';
use App\Helpers\BkavEHoaDonAPI;

//
require "/var/www/html/vendor/autoload.php";
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

if(!getCurrentUserId()){
    die("Not login!!!");
}

$em = getCurrentUserEmail();
if(!in_array($em, [
    'thuctt8x@gmail.com', 'dungbkhn02@gmail.com', 'dungla2011@gmail.com',
])) {

    die("Not valid access!!!");
}

$mmHostIp = ['10.0.1.11','10.0.1.12','10.0.1.13',];

$mm = \App\Models\VpsUsage::whereIn("last_host_ip", $mmHostIp)
    ->join('vps_instances', 'vps_instances.id', '=', 'vps_usages.instance_id')
    ->join('users', 'users.id', '=', 'vps_instances.user_id')
    ->select('vps_usages.*', 'vps_usages.created_at as vps_created_at', 'users.email', 'vps_usages.cpu', 'vps_usages.ram_gb', 'vps_usages.disk_gb', 'vps_usages.bios_uuid')
    ->get();

// Keep only the latest instance_id for each VPS (by name)
$vpsMap = [];
foreach ($mm as $oneVps) {
    if (empty($oneVps->list_ip_address)) continue;
    $key = $oneVps->name;
    if (!isset($vpsMap[$key]) || $oneVps->id > $vpsMap[$key]->id) {
        $vpsMap[$key] = $oneVps;
    }
}
$mm = array_values($vpsMap);

// Function to convert IP address to number for proper sorting
function ipToNumber($ip) {
    $parts = explode('.', $ip);
    return ($parts[0] << 24) + ($parts[1] << 16) + ($parts[2] << 8) + $parts[3];
}

// Function to format time difference
function formatTimeDiff($timestamp) {
    $now = time();
    $diff = abs($now - $timestamp);

    $minutes = floor($diff / 60);
    $hours = floor($diff / 3600);
    $days = floor($diff / 86400);

    if ($days > 0) {
        $timeStr = "$days ngày";
    } elseif ($hours > 0) {
        $timeStr = "$hours giờ";
    } else {
        $timeStr = "$minutes phút";
    }

    $isRecent = $diff <= 600; // 600 seconds = 10 minutes
    $color = $isRecent ? 'green' : 'red';

    return ['text' => "$timeStr trước", 'color' => $color, 'timestamp' => $timestamp];
}

// Function to sort and format last_host_ip
function formatLastHostIp($ips) {
    if (empty($ips)) return '';

    $ipArray = array_map('trim', explode(',', $ips));
    $ip103 = [];
    $otherIps = [];

    foreach ($ipArray as $ip) {
        if (strpos($ip, '103.') === 0) {
            $ip103[] = $ip;
        } else {
            $otherIps[] = $ip;
        }
    }

    $sorted = array_merge($ip103, $otherIps);
    return implode('<br>', $sorted);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VPS List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { font-size: 13px; }
        body { background: #f5f5f5; margin: 0; padding: 10px; }
        .search-container { position: sticky; top: 0; z-index: 100; background: #f5f5f5; padding: 5px 10px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 10px; }
        .search-container h3 { margin: 0; font-size: 18px; }
        .search-box input { width: 250px; padding: 5px 8px; font-size: 12px; }
        .container-fluid { padding: 0; }
        table { background: white; margin-bottom: 0; font-size: 12px; }
        table th, table td { padding: 6px 8px; vertical-align: middle; }
        table th { padding: 8px; }
        .sortable { cursor: pointer; user-select: none; }
        .sortable:hover { background-color: #555; }
        .sortable::after { content: ' ⇅'; }
        .sortable.asc::after { content: ' ↑'; }
        .sortable.desc::after { content: ' ↓'; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="search-container">
            <h3>VPS List</h3>
            <div class="search-box">
                <input type="text" id="searchInput" class="form-control" placeholder="Search by Name or IP Address...">
            </div>
        </div>

        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th style="width: 50px;">STT</th>
                    <th>Instance ID</th>
                    <th>Name</th>
                    <th class="sortable" data-sort="ip" data-direction="asc">IP Address</th>
                    <th>Last Host IP</th>
                    <th>Created At</th>
                    <th>Email</th>
                    <th>Last Found IP</th>
                </tr>
            </thead>
            <tbody id="vpsTableBody">
                <?php $stt = 1; foreach ($mm AS $oneVps): ?>
                    <?php
                        $timestamp = strtotime($oneVps->last_found_ip);
                        $timeData = formatTimeDiff($timestamp);
                        $date = new DateTime();
                        $date->setTimestamp($timestamp);
                        $dateStr = $date->format('d/m/Y H:i:s');

                        $createdAt = new DateTime($oneVps->vps_created_at);
                        $createdAtStr = $createdAt->format('d/m/Y H:i:s');
                    ?>
                    <tr class="vps-row" data-name="<?= strtolower($oneVps->name) ?>" data-ip="<?= strtolower($oneVps->list_ip_address) ?>" data-timestamp="<?= $timestamp ?>">
                        <td style="font-weight: bold; text-align: center;"><?= $stt++ ?></td>
                        <td><?= htmlspecialchars($oneVps->id) ?></td>
                        <td>
                            <div><?= htmlspecialchars($oneVps->name) ?></div>
                            <div style="font-size: 11px; font-family: monospace; color: #666; margin-top: 2px;"><?= htmlspecialchars(substr($oneVps->bios_uuid ?? '', 0, 12)) ?></div>
                        </td>
                        <td><?= htmlspecialchars($oneVps->list_ip_address) ?></td>
                        <td style="font-size: 0.9em; line-height: 1.5;"><?= formatLastHostIp($oneVps->last_host_ip) ?></td>
                        <td><?= $createdAtStr ?></td>
                        <td><?= htmlspecialchars($oneVps->email ?? 'N/A') ?></td>
                        <td class="last-found-ip-cell" data-timestamp="<?= $timestamp ?>">
                            <div><?= $dateStr ?></div>
                            <div style="font-size: 0.85em; margin-top: 3px; color: <?= $timeData['color'] ?>; font-weight: bold;" class="time-diff"><?= $timeData['text'] ?></div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('.vps-row');

        // Compare IP addresses numerically
        function compareIPs(ip1, ip2) {
            const parts1 = ip1.split('.').map(Number);
            const parts2 = ip2.split('.').map(Number);
            for (let i = 0; i < 4; i++) {
                if (parts1[i] !== parts2[i]) return parts1[i] - parts2[i];
            }
            return 0;
        }

        // Format time difference
        function formatTimeDiff(timestamp) {
            const now = Math.floor(Date.now() / 1000);
            const diff = Math.abs(now - timestamp);

            const minutes = Math.floor(diff / 60);
            const hours = Math.floor(diff / 3600);
            const days = Math.floor(diff / 86400);

            let timeStr = '';
            if (days > 0) timeStr = `${days} ngày`;
            else if (hours > 0) timeStr = `${hours} giờ`;
            else timeStr = `${minutes} phút`;

            const isRecent = diff <= 600;
            const color = isRecent ? 'green' : 'red';

            return { text: `${timeStr} trước`, color: color };
        }

        // Update time display every second
        function updateTimeDisplay() {
            document.querySelectorAll('.time-diff').forEach(cell => {
                const row = cell.closest('tr');
                const timestamp = parseInt(row.getAttribute('data-timestamp'));
                const { text, color } = formatTimeDiff(timestamp);
                cell.textContent = text;
                cell.style.color = color;
            });
        }

        // Sort table by column
        document.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', function() {
                const sortType = this.getAttribute('data-sort');
                const direction = this.getAttribute('data-direction') === 'asc' ? 'desc' : 'asc';

                const tbody = document.querySelector('#vpsTableBody');
                const rowsArray = Array.from(tbody.querySelectorAll('tr'));

                rowsArray.sort((a, b) => {
                    let aVal, bVal;

                    if (sortType === 'ip') {
                        aVal = a.getAttribute('data-ip');
                        bVal = b.getAttribute('data-ip');
                        return direction === 'asc' ? compareIPs(aVal, bVal) : compareIPs(bVal, aVal);
                    }

                    return 0;
                });

                // Re-append rows in sorted order
                rowsArray.forEach(row => tbody.appendChild(row));

                // Update STT column
                let stt = 1;
                rowsArray.forEach(row => {
                    row.querySelector('td:first-child').textContent = stt++;
                });

                // Update sort indicator
                document.querySelectorAll('.sortable').forEach(h => {
                    h.classList.remove('asc', 'desc');
                });
                this.classList.add(direction);
                this.setAttribute('data-direction', direction);
            });
        });

        // Search functionality
        searchInput.addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();

            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                const ip = row.getAttribute('data-ip');

                if (searchText === '' || name.includes(searchText) || ip.includes(searchText)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Update time display every second
        setInterval(updateTimeDisplay, 1000);
    </script>
</body>
</html>
