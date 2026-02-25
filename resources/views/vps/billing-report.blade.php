<?php
$uid = getCurrentUserId();
?>
@extends("layouts.member")
@section('header')
    @include('parts.header-all')
@endsection


@section('title')
    {{
    \App\Models\SiteMng::getTitle()
    }}
@endsection

@section('meta-description')
    <?php
    \App\Models\SiteMng::getDesc()
    ?>
@endsection

@section("css")
    <style>
        .badge-danger,.badge-secondary {
            font-weight: inherit;
        }
        .table-sm td{
            vertical-align: middle;
        }
        td.instance-id{
            font-size: 80%;
        }

        .new-instance {
             border-top: 2px solid #007bff !important;
        }
        .powered-on {
            color: #28a745;
            font-weight: 600;
        }
        .powered-off {
            color: #dc3545;
            font-weight: 600;
        }
        .row-crossed {
            text-decoration: line-through;
            opacity: 0.6;
        }
        .timeline-connected {
            background-color: #e7f4f9 !important;
            border-left: 3px solid #17a2b8 !important;
        }
        .time-connected {
            background-color: #f0f8f5 !important;
        }
        .time-connected-color-1 { background-color: snow !important; }
        .time-connected-color-2 { background-color: snow !important; }
        .time-connected-color-3 { background-color: snow !important; }
        .time-connected-color-4 { background-color: snow !important; }
        .time-connected-color-5 { background-color: snow !important; }
        /* Connected cell highlighting */
        .connected-cell-1 { background-color: lavender !important; border: 0px solid lavender !important; font-weight: bold; }
        .connected-cell-2 { background-color: lavender !important; border: 0px solid lavender !important; font-weight: bold; }
        .connected-cell-3 { background-color: lavender !important; border: 0px solid lavender !important; font-weight: bold; }
        .connected-cell-4 { background-color: lavender !important; border: 0px solid lavender !important; font-weight: bold; }
        .connected-cell-5 { background-color: lavender !important; border: 2px solid lavender !important; font-weight: bold; }
    </style>
@endsection

@section('content')
    <div class="content-wrapper pt-3">
        <!-- Content Header -->
        <div class="content-header d-none">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3 class="m-0"> VPS Billing Report</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">VPS Billing</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Summary Financial Info -->
                <div class="row">
                    <div class="col-12 col-md-4">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-wallet"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Đã thanh toán</span>
                                <span class="info-box-number">{{ number_format($totalRecharge / 1000000 / 1.1, 2, ',', '.') }}M VND (Chưa VAT)</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 100%"></div>
                                </div>
                                <span class="progress-description">
                                    <a href="/member/user-recharge" class="text-white">
                                        <i class="fas fa-arrow-circle-right"></i> Chi tiết thanh toán
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Tổng chi phí VPS</span>
                                <span class="info-box-number">{{ number_format($totalCost / 1000, 2, ',', '.') }}M VND</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: {{ $totalRecharge > 0 ? min(100, (($totalCost * 1000) / $totalRecharge) * 100) : 0 }}%"></div>
                                </div>
                                <span class="progress-description">
                                    {{ number_format($totalCost, 0, ',', '.') }} K VND
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="info-box bg-gradient-success">
                            <span class="info-box-icon"><i class="fas fa-coins"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Số dư tài khoản</span>
                                <span class="info-box-number">{{ number_format(($totalRecharge / 1000 / 1.1 - $totalCost) / 1000, 2, ',', '.') }}M VND</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: {{ $totalRecharge > 0 ? (($totalRecharge / 1000 / 1.1 - $totalCost) / ($totalRecharge / 1000 / 1.1)) * 100 : 0 }}%"></div>
                                </div>
                                <span class="progress-description">
                                    {{ number_format($totalRecharge / 1000 / 1.1 - $totalCost, 0, ',', '.') }} K VND
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info boxes -->
                <div class="row">

                    <div class="col-12 col-sm-6 col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-database"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Usage Records</span>
                                <span class="info-box-number">{{ number_format(count($results), 0) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-server"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">VPS Instances</span>
                                <span class="info-box-number">{{ count($instanceTotals) }}</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Total Cost Card -->
                <div class="row" style="display: none">
                    <div class="col-12">
                        <div class="card card-primary card-outline">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-9">
                                        <h3 class="mb-1">Total Cost</h3>
                                        <h1 class="display-4 text-primary mb-2">
                                            <strong>{{ number_format($totalCost, $decimal, $dec_separator, $thousands_separator) }}</strong>
                                            <small class="text-muted">K VND</small>
                                        </h1>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-equals"></i> Approximately {{ number_format($totalCost / 1000, 3, $dec_separator, $thousands_separator) }} Million VND
                                        </p>
                                        @if($date_from || $date_to)
                                        <p class="text-muted mt-2">
                                            <i class="fas fa-calendar"></i> Period:
                                            @if($date_from) {{ \Carbon\Carbon::parse($date_from)->format('Y-m-d') }} @endif
                                            @if($date_from && $date_to) to @endif
                                            @if($date_to) {{ \Carbon\Carbon::parse($date_to)->format('Y-m-d') }} @endif
                                        </p>
                                        @endif
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <a href="{{ route('vps.billing.pdf', ['email' => $user->email]) }}" class="btn btn-primary btn-lg mb-2 btn-block">
                                            <i class="fas fa-file-pdf"></i> Download PDF
                                        </a>
                                        <button onclick="location.reload()" class="btn btn-default btn-block">
                                            <i class="fas fa-sync-alt"></i> Refresh
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instance Totals -->
                <div class="row d-none" >
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-chart-bar"></i> Danh sách VPS </h3>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th width="50">STT</th>
                                            <th>Mã số VPS</th>
                                            <th>Tên VPS</th>
                                            <th>Số bản ghi</th>
                                            <th class="text-right">Tổng chi phí (K VND)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($instanceTotals as $instId => $instData)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><span class="badge1 badge-secondary1">{{ $instId }}</span></td>
                                            <td>{{ $instData['name'] }}</td>
                                            <td>{{ number_format($instData['count'], 0, $dec_separator, $thousands_separator) }}</td>
                                            <td class="text-right">
                                                <strong class="text-primary">{{ number_format($instData['total'], $decimal, $dec_separator, $thousands_separator) }}</strong>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <th colspan="3" class="text-right">TỔNG CỘNG:</th>
                                            <th>{{ number_format(count($results), 0, $dec_separator, $thousands_separator) }}</th>
                                            <th class="text-right">
                                                <strong class="text-danger">{{ number_format($totalCost, $decimal, $dec_separator, $thousands_separator) }}</strong>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Records -->
                <div class="row" data-code-pos='ppp17711880674321'>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-list"></i> Chi tiết sử dụng VPS</h3>
                            </div>
                            <div class="card-body table-responsive p-0" data-code-pos='ppp17711880711391'>
                                <table class="table table-hover table-sm text-nowrap">
                                    <thead>
                                        <tr class="bg-light">
                                            <th>STT</th>
                                            <th>Mã số</th>
                                            <th>Tên Vps</th>
                                            <th>Địa chỉ IP</th>
                                            <th>Ngày tính phí</th>
                                            <th>Tính phí đến</th>
                                            <th>Thời gian sử dụng</th>
                                            <th>Phí/Tháng</th>
                                            <th>Trạng thái</th>
                                            <th class="text-right"> Tổng Chi phí</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $currentInstanceId = null; $previousRow = null; @endphp
                                        @foreach ($results as $row)
                                        @php
                                            $isNewInstance = $currentInstanceId !== $row['instance_id'];
                                            $currentInstanceId = $row['instance_id'];
                                            $isPoweredOff = strtoupper($row['power_state']) === 'POWERED_OFF';
                                            $isCrossed = $isPoweredOff && $row['calculated_fee'] == 0;

                                            // Check if each spec changed from previous row
                                            $cpuChanged = false;
                                            $ramChanged = false;
                                            $diskChanged = false;
                                            $ipChanged = false;

                                            if (!$isNewInstance && $previousRow && strtoupper($row['power_state']) === 'CHANGE_CONFIG') {
                                                $cpuChanged = $previousRow['cpu'] != $row['cpu'];
                                                $ramChanged = $previousRow['ram_gb'] != $row['ram_gb'];
                                                $diskChanged = $previousRow['disk_gb'] != $row['disk_gb'];
                                                $ipChanged = $previousRow['ip_count'] != $row['ip_count'];
                                            }
                                        @endphp
                                        <tr @if($isNewInstance) class="new-instance" @endif data-instance-id="{{ $row['instance_id'] }}" data-created-at="{{ $row['created_at'] }}" data-timestamp="{{ $row['timestamp'] }}">
                                            <td class="stt {{ $isCrossed ? 'row-crossed' : '' }}">{{ $loop->iteration }}</td>
                                            <td class="instance-id {{ $isCrossed ? 'row-crossed' : '' }}"><span class="badge1 badge-secondary1">{{ $row['instance_id'] }}</span></td>
                                            <td class="name {{ $isCrossed ? 'row-crossed' : '' }}">
                                                <a href="/member/vps-instance/edit/{{$row['instance_id']}}" target="_blank" data-code-pos='ppp17720149046371'>
                                                {{ $row['instance_name'] }}
                                                </a>
                                                <br>
                                                <small1>
                                                    <span class="badge {{ $cpuChanged ? 'badge-danger' : 'badge-secondary' }}">{{ $row['cpu'] }} Core </span>
                                                    <span class="badge {{ $ramChanged ? 'badge-danger' : 'badge-secondary' }}">{{ $row['ram_gb'] }} GB</span>
                                                    <span class="badge {{ $diskChanged ? 'badge-danger' : 'badge-secondary' }}">{{ $row['disk_gb'] }} GB</span>
                                                    <span class="badge {{ $ipChanged ? 'badge-danger' : 'badge-secondary' }}">{{ $row['ip_count'] }} IP</span>
                                                </small1>
                                            </td>
                                            <td class="list-ip-address {{ $isCrossed ? 'row-crossed' : '' }}">
                                                <small>{!!  str_replace( ",", "<br>", $row['list_ip_address']) !!}</small>
                                            </td>
                                            <td class="last-billing-start-at {{ $isCrossed ? 'row-crossed' : '' }}">
                                                @if($row['last_billing_start_at'])
                                                    <small class="text">{{ \Carbon\Carbon::parse($row['last_billing_start_at'])->format('Y-m-d H:i') }}</small><br>
                                                    <small class="text" title="Ngày tạo - Created date"><i>({{ \Carbon\Carbon::parse($row['created_at'])->format('Y-m-d H:i') }})</i></small>
                                                @else
                                                    <small>{{ \Carbon\Carbon::parse($row['created_at'])->format('Y-m-d H:i') }}</small>
                                                @endif
                                            </td>
                                            <td class="timestamp {{ $isCrossed ? 'row-crossed' : '' }}">
                                                <small>{{ \Carbon\Carbon::parse($row['timestamp'])->format('Y-m-d H:i') }}</small>
                                            </td>
                                            <td class="time-usage {{ $isCrossed ? 'row-crossed' : '' }}"><small class="text-muted">{{ $row['time_usage'] }}</small></td>
                                            <td class="price-month {{ $isCrossed ? 'row-crossed' : '' }}">
                                                @php
                                                    $displayPrice = (float)($row['price_month'] ?? $row['price_config']);
                                                    $isChangeConfig = !$row['is_latest_config'];
                                                @endphp
                                                @if ($displayPrice && !$isPoweredOff)
                                                    @if (!$isChangeConfig)
                                                    <span class="badge badge-primary">{{ number_format($displayPrice, 0, $dec_separator, $thousands_separator) }}K</span>
                                                    @else
                                                    <small class="badge badge-secondary">{{ number_format($displayPrice, 0, $dec_separator, $thousands_separator) }}K</small>
                                                    @endif
                                                @elseif ($isPoweredOff)
                                                <small class="text-muted">-</small>
                                                @else
                                                <small class="text-muted">N/A</small>
                                                @endif
                                            </td>
                                            <td class="power-state {{ $isCrossed ? 'row-crossed' : '' }}">
                                                @if($row['is_latest_config'])
                                                    <span class="{{ strtoupper($row['power_state']) === 'POWERED_ON' ? 'badge badge-success' : 'badge badge-danger' }}">
                                                        {{ $row['power_state'] }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">
                                                        CHANGE_CONFIG
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-right fee {{ $isCrossed ? 'row-crossed' : '' }}">
                                                <a href="{{ route('vps.billing.detail', $row['usage_id']) }}" class="text-primary" title="Click to view detailed calculation">
                                                    <strong>{{ number_format($row['calculated_fee'], $decimal, $dec_separator, $thousands_separator) }} K</strong>
                                                    <i class="fas fa-external-link-alt ml-1" style="font-size: 10px;"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @php $previousRow = $row; @endphp
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <th colspan="7" class="text-right" title="Chỉ lấy các Cấu hình cuối cùng nếu có thay đổi">TỔNG CỘNG:</th>
                                            <th class="text-left">
                                                <h4 class="mb-0" title="Chỉ lấy các Cấu hình cuối cùng nếu có thay đổi">
                                                    <span class="badge badge-success" style="font-size: 16px; padding: 8px 12px;">{{ number_format($totalPriceMonth, 0, $dec_separator, $thousands_separator) }}K <small>/Tháng</small></span>
                                                </h4>
                                            </th>
                                            <th>{{ number_format(count($results), 0, $dec_separator, $thousands_separator) }} records</th>
                                            <th class="text-right">
                                                <strong class="text-danger">{{ number_format($totalCost, $decimal, $dec_separator, $thousands_separator) }} K </strong>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Notes -->
                <div class="row d-none">
                    <div class="col-12">
                        <div class="callout callout-info">
                            <h5><i class="fas fa-info-circle"></i> Report Notes</h5>
                            <ul class="mb-0">
                                <li>Each record represents VPS configuration at a specific timestamp_minute</li>
                                <li><strong class="text-danger">POWERED_OFF state = NO CHARGE:</strong> Fee is set to 0</li>
                                <li>Instance Totals section shows sum of all records grouped by instance_id</li>
                                <li>Blue border separates different instances in detailed list</li>
                                <li>Time-connected rows: Same instance_id with consecutive billing periods shown with background color</li>
                                <li>Generated at: <strong>{{ $generated_at->format('Y-m-d H:i:s') }}</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Export PDF Button -->
                <div class="row mt-4 mb-3">
                    <div class="col-12">
                        <button type="button" class="btn btn-danger" id="exportPdfBtn">
                            <i class="fas fa-file-pdf mr-2"></i> Export PDF
                        </button>
                    </div>
                </div>

            </div>
        </section>
    </div>

    <script>
    // Function to highlight config changes
    function highlightConfigChanges(rows) {
        for (let i = 1; i < rows.length; i++) {
            const currentRow = rows[i];
            const previousRow = rows[i - 1];

            const currentInstanceId = currentRow.getAttribute('data-instance-id');
            const previousInstanceId = previousRow.getAttribute('data-instance-id');
            const powerState = currentRow.querySelector('td.power-state');

            // Check if it's CHANGE_CONFIG and same instance
            if (currentInstanceId === previousInstanceId && powerState && powerState.textContent.includes('CHANGE_CONFIG')) {
                // Extract config values from badges
                const currentNameCell = currentRow.querySelector('td.name');
                const previousNameCell = previousRow.querySelector('td.name');

                if (currentNameCell && previousNameCell) {
                    const currentBadges = Array.from(currentNameCell.querySelectorAll('.badge'));
                    const previousBadges = Array.from(previousNameCell.querySelectorAll('.badge'));

                    // Extract spec values
                    const getCurrentSpecs = (badges) => ({
                        cpu: badges[0]?.textContent.match(/\d+/)?.[0],
                        ram: badges[1]?.textContent.match(/\d+/)?.[0],
                        disk: badges[2]?.textContent.match(/\d+/)?.[0],
                        ip: badges[3]?.textContent.match(/\d+/)?.[0]
                    });

                    const currentSpecs = getCurrentSpecs(currentBadges);
                    const previousSpecs = getCurrentSpecs(previousBadges);

                    // Compare and color changed specs on PREVIOUS row (older config)
                    if (currentSpecs.cpu !== previousSpecs.cpu && previousBadges[0]) {
                        previousBadges[0].classList.remove('badge-info');
                        previousBadges[0].classList.add('badge-warning');
                    }
                    if (currentSpecs.ram !== previousSpecs.ram && previousBadges[1]) {
                        previousBadges[1].classList.remove('badge-info');
                        previousBadges[1].classList.add('badge-warning');
                    }
                    if (currentSpecs.disk !== previousSpecs.disk && previousBadges[2]) {
                        previousBadges[2].classList.remove('badge-info');
                        previousBadges[2].classList.add('badge-warning');
                    }
                    if (currentSpecs.ip !== previousSpecs.ip && previousBadges[3]) {
                        previousBadges[3].classList.remove('badge-info');
                        previousBadges[3].classList.add('badge-warning');
                    }

                    console.log(`✏️  Config change detected: Row ${i+1} (Instance: ${currentInstanceId})`);
                }
            }
        }
    }

    // Helper function to process a single table
    function processTable(tableElement, tableName) {
        const tbody = tableElement.querySelector('tbody');
        if (!tbody) {
            console.warn(`⚠️  ${tableName}: No tbody found`);
            return;
        }

        const rows = Array.from(tbody.querySelectorAll('tr'));
        console.log(`📊 ${tableName}: Found ${rows.length} rows`);

        if (rows.length === 0) return;

        // First: Highlight config changes
        highlightConfigChanges(rows);

        const colorMap = new Map();
        let currentColorIndex = 0;
        const colors = ['time-connected-color-1', 'time-connected-color-2', 'time-connected-color-3', 'time-connected-color-4', 'time-connected-color-5'];
        const colorLines = ['snow', 'snow', 'snow', 'snow', 'snow'];
        let connectedCount = 0;
        const connectedPairs = [];

        // Log first row for debugging
        if (rows.length > 0) {
            const firstRow = rows[0];
            console.log(`   First row data-instance-id: ${firstRow.getAttribute('data-instance-id')}`);
            console.log(`   First row data-created-at: ${firstRow.getAttribute('data-created-at')}`);
            console.log(`   First row data-timestamp: ${firstRow.getAttribute('data-timestamp')}`);
        }

        // Process rows for time-connected highlighting
        for (let i = 0; i < rows.length; i++) {
            const currentRow = rows[i];
            const nextRow = i + 1 < rows.length ? rows[i + 1] : null;

            const currentInstanceId = currentRow.getAttribute('data-instance-id');
            const currentCreatedAt = currentRow.getAttribute('data-created-at');
            const nextInstanceId = nextRow ? nextRow.getAttribute('data-instance-id') : null;
            const nextTimestamp = nextRow ? nextRow.getAttribute('data-timestamp') : null;

            // Check if next row is connected
            if (nextRow && currentInstanceId && nextInstanceId && currentInstanceId === nextInstanceId && currentCreatedAt && nextTimestamp) {
                try {
                    const currTime = new Date(currentCreatedAt).getTime();
                    const nextTime = new Date(nextTimestamp).getTime();

                    if (Math.abs(currTime - nextTime) < 60000) {
                        const key = `${currentInstanceId}-${i}`;

                        let colorClass;
                        let colorIdx;
                        if (colorMap.has(key)) {
                            colorClass = colorMap.get(key);
                            colorIdx = colors.indexOf(colorClass);
                        } else {
                            colorIdx = currentColorIndex % colors.length;
                            colorClass = colors[colorIdx];
                            currentColorIndex++;
                        }

                        currentRow.classList.add(colorClass);
                        currentRow.style.fontWeight = '500';

                        if (!colorMap.has(`${nextInstanceId}-${i+1}`)) {
                            colorMap.set(`${nextInstanceId}-${i+1}`, colorClass);
                        }
                        nextRow.classList.add(colorClass);
                        nextRow.style.fontWeight = '500';
                        connectedCount++;

                        // Highlight the specific cells that have matching values
                        // Current row's "Ngày tính phí" (created_at) matches Next row's "Tính phí đến" (timestamp)
                        const currentCreatedCell = currentRow.querySelector('td.last-billing-start-at');
                        const nextTimestampCell = nextRow.querySelector('td.timestamp');

                        const cellColorClass = `connected-cell-${colorIdx + 1}`;
                        if (currentCreatedCell) {
                            currentCreatedCell.classList.add(cellColorClass);
                        }
                        if (nextTimestampCell) {
                            nextTimestampCell.classList.add(cellColorClass);
                        }

                        // Store for drawing lines
                        connectedPairs.push({
                            currentRowIdx: i,
                            nextRowIdx: i + 1,
                            colorIdx: colorIdx,
                            lineColor: colorLines[colorIdx]
                        });

                        console.log(`   ✅ Connected: Row ${i+1} -> Row ${i+2} (Instance: ${currentInstanceId})`);
                    }
                } catch (e) {
                    console.error(`   ❌ Error parsing dates in row ${i+1}:`, e);
                }
            }
        }

        // Add instance ID to all TD elements
        rows.forEach(row => {
            const instanceId = row.getAttribute('data-instance-id');
            if (instanceId) {
                row.querySelectorAll('td').forEach(td => {
                    td.setAttribute('data-instance-id', instanceId);
                });
            }
        });

        // Draw connecting lines using HTML divs
        if (connectedPairs.length > 0) {
            drawConnectorLines(tableElement, rows, connectedPairs);
        }

        console.log(`   ✨ ${tableName}: Found ${connectedCount} time-connected pairs\n`);
    }

    // Draw connector lines between rows using Canvas
    function drawConnectorLines(tableElement, rows, connectedPairs) {
        // Create canvas overlay
        const canvas = document.createElement('canvas');
        canvas.style.position = 'fixed';
        canvas.style.top = '0';
        canvas.style.left = '0';
        canvas.style.pointerEvents = 'none';
        canvas.style.zIndex = '9999';
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        document.body.appendChild(canvas);
        const ctx = canvas.getContext('2d');

        const lineColors = ['#28a745', '#007bff', '#fd7e14', '#e83e8c', '#6f42c1'];

        const drawLines = () => {
            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            connectedPairs.forEach(pair => {
                const currentRow = rows[pair.currentRowIdx];
                const nextRow = rows[pair.nextRowIdx];

                const currentRect = currentRow.getBoundingClientRect();
                const nextRect = nextRow.getBoundingClientRect();

                // Get the rightmost position of the rows
                const x1 = currentRect.right - 10;  // A bit inside from right edge
                const y1 = currentRect.bottom;
                const x2 = nextRect.right - 10;
                const y2 = nextRect.top;

                // Draw line
                ctx.beginPath();
                ctx.moveTo(x1, y1);
                ctx.lineTo(x2, y2);
                ctx.strokeStyle = lineColors[pair.colorIdx];
                ctx.lineWidth = 3;
                ctx.lineCap = 'round';
                ctx.globalAlpha = 0.9;
                ctx.stroke();
                ctx.globalAlpha = 1.0;

                // Draw circle at start
                ctx.beginPath();
                ctx.arc(x1, y1, 4, 0, 2 * Math.PI);
                ctx.fillStyle = lineColors[pair.colorIdx];
                ctx.fill();

                // Draw circle at end
                ctx.beginPath();
                ctx.arc(x2, y2, 4, 0, 2 * Math.PI);
                ctx.fillStyle = lineColors[pair.colorIdx];
                ctx.fill();
            });
        };

        // Draw initially
        drawLines();

        // Redraw on scroll and resize
        window.addEventListener('scroll', drawLines);
        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            drawLines();
        });
    }

    // Run AFTER all other scripts to ensure table is fully loaded
    setTimeout(function() {
        console.log('=== VPS Billing Report - Time Connection Processing ===\n');

        // Get all table.table-hover elements
        const tables = document.querySelectorAll('table.table-hover');
        console.log(`Found ${tables.length} tables.table-hover\n`);

        if (tables.length === 0) {
            console.error('❌ No tables.table-hover found');
            return;
        }

        // Process each table
        tables.forEach((table, idx) => {
            const headers = table.querySelector('thead tr');
            let tableName = `Table ${idx + 1}`;

            // Try to identify table by context
            const card = table.closest('.card');
            if (card) {
                const cardTitle = card.querySelector('.card-title');
                if (cardTitle) {
                    tableName = cardTitle.textContent.trim();
                }
            }

            // Skip processing summary table (Instance Totals Summary)
            if (tableName.includes('Instance Totals') || tableName.includes('Summary')) {
                console.log(`⏭️  Skipping: ${tableName} (summary table)\n`);
                return;
            }

            console.log(`📋 Processing: ${tableName}`);
            processTable(table, tableName);
        });

        console.log('✅ All tables processed!');
    }, 500); // Wait 500ms for other scripts to finish

    // Export to PDF using html2pdf
    document.addEventListener('DOMContentLoaded', function() {
        const exportPdfBtn = document.getElementById('exportPdfBtn');
        if (exportPdfBtn) {
            exportPdfBtn.addEventListener('click', function() {
                exportToPdf();
            });
        }
    });

    function exportToPdf() {
        // Get content to export
        const element = document.querySelector('.content-wrapper');
        const fileName = `vps-billing-${new Date().toISOString().split('T')[0]}.pdf`;

        // Options for html2pdf
        const options = {
            margin: 10,
            filename: fileName,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { orientation: 'landscape', unit: 'mm', format: 'a4' }
        };

        // Check if html2pdf library is loaded
        if (typeof html2pdf === 'undefined') {
            // Load library dynamically
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
            script.onload = function() {
                html2pdf().set(options).from(element).save();
            };
            document.head.appendChild(script);
        } else {
            html2pdf().set(options).from(element).save();
        }
    }
    </script>
@endsection
