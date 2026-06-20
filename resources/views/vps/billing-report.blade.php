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
        .table-sm td, .table-sm th{
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

        /* PDF Export Optimization */
        .content-wrapper {
            padding-top: 0 !important;
        }
        .all_content_report_vps {
            /*padding: 0 !important;*/
            /*margin: 0 !important;*/
        }
        .all_content_report_vps > .container-fluid {
            padding: 5px !important;
            /*margin: 0 !important;*/
        }
        .all_content_report_vps .row {
            /*margin: 0 !important;*/
            /*margin-bottom: 5px !important;*/
        }
        .all_content_report_vps .info-box {
            /*margin-bottom: 0 !important;*/
        }
    </style>
@endsection

@section('content')
    <?php
    $totalCostVND = $totalCost;
    ?>
    <div class="content-wrapper" style="padding-top: 1px!important;">

        <!-- Main content -->
        <section class="content all_content_report_vps mt-3">
            <div class="container-fluid">
                <!-- Summary Financial Info -->
                <!-- Partner Info -->
                <div class="row mb-3 partner-info d-none">
                    <div class=" text-center" style="border-bottom: 1px solid #ccc; padding-bottom: 20px">
                        <div class="text-muted">CÔNG TY CÔNG NGHỆ SỐ GLAXY VIỆT NAM - GLX.COM.VN
                        </div>
                    </div>


                    <DIV class="m-3 text-center">
                        <?php

                        $toTime = request('to_time');

                        ?>
                        <b style="color: gray; font-size: 120%">

                        Đối soát sử dụng VPS đến thời điểm {{ $toTime ? $toTime : "hiện tại - " . nowyh_vn()  }}
                        </b>
                    </DIV>

                    <div class="text-center">
                        <?php
                        $pn = \App\Models\PartnerInfo::where("user_id", getCurrentUserId())->first();
                        if($pn){
                            ?>
                        <div class="text-muted">
                           Khách hàng:  {{ $pn->partner_name }} - {{ $pn->tax_code }}
                        </div>
                            <?php
                        }
                        ?>
                    </div>


                </div>
                <div class="row">
                    <?php
                    $soDuDuong = $soDu = $totalRecharge / 1.1 - $totalCostVND;
                    if($soDu < 0){
                        $soDuDuong = -1 * $soDu;
                    }

                    $tt = number_format($soDu , 0, ',', '.');
                    ?>
                    <div class="col-12 col-md-4">
                        <div class="info-box bg-gradient-success">
                            <span class="info-box-icon"><i class="fas fa-coins"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">

                                    {{ $soDu > 0 ? "Số dư: " : "Cần thanh toán: " }}

                                </span>
                                <span class="info-box-number">{{ number_format($soDuDuong , 0, ',', '.'); }} VND </span>
                                <div class="progress">
                                </div>
                                <span class="progress-description">
                                    {{ \LadLib\Common\cstring2::toTienVietNamString3(( $soDuDuong))  }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-wallet"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Đã thanh toán:

                                </span>
                                <span class="info-box-number">{{ number_format($totalRecharge / 1.1, 0, ',', '.') }} VND
                                         <a href="/member/user-recharge" class="text-white" style="font-size: 80%"><i>
                                        (Chưa VAT, Xem danh sách)
                                         </i></a>
                                </span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 100%"></div>
                                </div>
                                <span class="progress-description">
{{ \LadLib\Common\cstring2::toTienVietNamString3($totalRecharge)  }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Tổng chi phí VPS:

                                </span>
                                <span class="info-box-number">{{ number_format($totalCostVND, 0, ',', '.') }} VND</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: {{ $totalRecharge > 0 ? min(100, (($totalCostVND ) / $totalRecharge) * 100) : 0 }}%"></div>
                                </div>
                                <span class="progress-description">
                                    {{ \LadLib\Common\cstring2::toTienVietNamString3($totalCostVND)  }}
                                </span>
                            </div>
                        </div>
                    </div>


                </div>

                <!-- Info boxes -->
                <div class="row">

                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-database"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Usage Records:</span>
                                <span class="info-box-number">{{ number_format(count($results), 0) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-server"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">VPS Instances:</span>
                                <span class="info-box-number">{{ count($instanceTotals) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4 select_date_bill">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-calendar"></i></span>
                            <div class="info-box-content">

                                <div style="margin-top: 8px; display: flex; gap: 5px;">
                                    <input type="date" id="fromDate" class="form-control form-control-sm"
                                        value="{{ request('from_time') }}" style="font-size: 100%; width: 50%; {{ request('from_time') ? 'color:white; border: 1px solid #17a2b8; background-color: #17a2b8;' : '' }}"
                                        placeholder="Từ ngày"
                                        >
                                    <input type="date" id="paymentDate" class="form-control form-control-sm"
                                        value="{{ request('to_time') }}" style="font-size: 100%; width: 50%; {{ request('to_time') ? 'color:white; border: 1px solid red; background-color: red;' : '' }}"
                                        placeholder="Đến ngày"
                                        >
                                </div>
                                <div style="margin-top: 8px; display: flex; gap: 5px;">
                                    <button class="btn btn-primary btn-sm flex-grow-1" onclick="filterByDate()">
                                        <i class="fas fa-search"></i> Chọn ngày
                                    </button>
                                    <button class="btn btn-secondary btn-sm" onclick="clearDate()"
                                        {{ (!request('to_time') && !request('from_time')) ? 'disabled' : '' }}>
                                        <i class="fas fa-times"></i> Xoá
                                    </button>
                                </div>
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
                                            <strong>{{ number_format($totalCostVND, $decimal, $dec_separator, $thousands_separator) }}</strong>
                                            <small class="text-muted"> VND</small>
                                        </h1>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-equals"></i> Approximately {{ number_format($totalCostVND , 0, $dec_separator, $thousands_separator) }} VND
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
                                            <th style="text-align: center; width: 50px;">
                                                <input type="checkbox" id="selectAllVps" onchange="selectAllVpsCheckboxes(this)" title="Select/Deselect all">
                                                <br><small>Filter</small>
                                            </th>
                                            <th>Mã số</th>
                                            <th>Tên Vps</th>
                                            <th>Địa chỉ IP</th>
                                            <th>Comment</th>
                                            <th>Tính phí từ</th>
                                            <th>Tính phí đến</th>
                                            <th>Thời gian sử dụng</th>
                                            <th>Phí/Tháng </th>
                                            <th>Trạng thái</th>
                                            <th class="text-right"> Tổng số </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $currentInstanceId = null; $previousRow = null; @endphp
                                        @foreach ($results as $row)
                                        @php

                                            $startTime = \Carbon\Carbon::parse($row['created_at'])->format('Y-m-d H:i');
                                            if($fromTime = request('from_time') ?? ''){
                                                $fromTime = "$fromTime 00:00";
                                                if($fromTime > $startTime)
                                                    $startTime = $fromTime;
                                            }


                                            $isNewInstance = $currentInstanceId !== $row['instance_id'];
                                            $currentInstanceId = $row['instance_id'];
                                            $isPoweredOff = strtoupper($row['power_state']) === 'POWERED_OFF';
                                            $isCrossed = $isPoweredOff && $row['calculated_fee'] == 0;

                                            // Check if each spec changed from previous row
                                            $cpuChanged = false;
                                            $ramChanged = false;
                                            $diskChanged = false;
                                            $ipChanged = false;

                                            if (!$isNewInstance && $previousRow && strtoupper($row['power_state']) === 'OLD_CONFIG') {
                                                $cpuChanged = $previousRow['cpu'] != $row['cpu'];
                                                $ramChanged = $previousRow['ram_gb'] != $row['ram_gb'];
                                                $diskChanged = $previousRow['disk_gb'] != $row['disk_gb'];
                                                $ipChanged = $previousRow['ip_count'] != $row['ip_count'];
                                            }
                                        @endphp
                                        <tr @if($isNewInstance) class="new-instance" @endif data-instance-id="{{ $row['instance_id'] }}" data-created-at="{{ $row['created_at'] }}" data-timestamp="{{ $row['timestamp'] }}">
                                            <td class="stt {{ $isCrossed ? 'row-crossed' : '' }}">{{ $loop->iteration }}</td>
                                            <td class="filter-checkbox" style="text-align: center;">
                                                <input type="checkbox" class="vps-filter-checkbox" value="{{ $row['instance_id'] }}" onchange="updateFilterButton()" title="Select this VPS">
                                            </td>
                                            <td class="instance-id {{ $isCrossed ? 'row-crossed' : '' }}"><span class="badge1 badge-secondary1">{{ $row['instance_id'] }}</span></td>
                                            <td class="name {{ $isCrossed ? 'row-crossed' : '' }}">
                                                <a href="/member/vps-instance/edit/{{$row['instance_id']}}" target="_blank" data-code-pos='ppp17720149046371'>
                                                {{ $row['instance_name'] }}
                                                </a>
                                                <br>
                                                <small1>
                                                    <span class="badge {{ $cpuChanged ? 'badge-warning' : 'badge-secondary' }}">{{ $row['cpu'] }} Core </span>
                                                    <span class="badge {{ $ramChanged ? 'badge-warning' : 'badge-secondary' }}">{{ $row['ram_gb'] }} GB</span>
                                                    <span class="badge {{ $diskChanged ? 'badge-warning' : 'badge-secondary' }}">{{ $row['disk_gb'] }} GB</span>
                                                    <span class="badge {{ $ipChanged ? 'badge-warning' : 'badge-secondary' }}">{{ $row['ip_count'] }} IP</span>
                                                </small1>
                                            </td>
                                            <td class="list-ip-address {{ $isCrossed ? 'row-crossed' : '' }}">
                                                <small>{!!  str_replace( ",", "<br>", $row['list_ip_address']) !!}</small>
                                            </td>
                                            <td class="list-ip-address">
                                                <small>{!!  str_replace( ",", "<br>", $row['comment']?? '') !!}</small>
                                            </td>
                                            <td class="last-billing-start-at {{ $isCrossed ? 'row-crossed' : '' }}">
                                                @if($row['last_billing_start_at'])
                                                    <small class="text">{{ \Carbon\Carbon::parse($row['last_billing_start_at'])->format('Y-m-d H:i') }}</small><br>
                                                    <small class="text" title="Ngày tạo - Created date"><i>({{ \Carbon\Carbon::parse($row['created_at'])->format('Y-m-d H:i') }})</i></small>
                                                @else
                                                    <small>{{$startTime}}</small>
                                                @endif
                                            </td>
                                            <td class="timestamp {{ $isCrossed ? 'row-crossed' : '' }}">
                                                <small>{{ \Carbon\Carbon::parse($row['timestamp'])->format('Y-m-d H:i') }}  </small>
                                            </td>
                                            <td class="time-usage {{ $isCrossed ? 'row-crossed' : '' }}"><small class="text-muted">{{ $row['time_text'] }}</small></td>
                                            <td class="price-month {{ $isCrossed ? 'row-crossed' : '' }}">
                                                @php
                                                    $displayPrice = number_formatvn0($row['price_month'] ?? $row['price_config']);
                                                    $isChangeConfig = !$row['is_latest_config'];
                                                @endphp
                                                @if ($displayPrice && !$isPoweredOff)
                                                    @if (!$isChangeConfig)
                                                    <span class="color-primary">{{ ($displayPrice) }} VND </span>
                                                    @else
                                                    <span class="color-primary">{{($displayPrice) }} VND </span>
                                                    @endif
                                                @elseif ($isPoweredOff)
                                                <small class="text-muted">-</small>
                                                @else
                                                <small class="text-muted">N/A</small>
                                                @endif
                                            </td>
                                            <td class="power-state {{ $isCrossed ? 'row-crossed' : '' }}">

                                                    <span class="{{ strtoupper($row['power_state']) === 'POWERED_ON' ? 'badge badge-success' : 'badge badge-secondary' }}">
                                                        {{ $row['power_state'] }}
                                                    </span>

                                            </td>
                                            <td class="text-right fee {{ $isCrossed ? 'row-crossed' : '' }}">
                                                <a href="{{ route('vps.billing.detail', $row['usage_id']) }}{{ http_build_query(array_filter(['from_time' => request('from_time'), 'to_time' => request('to_time')])) ? '?' . http_build_query(array_filter(['from_time' => request('from_time'), 'to_time' => request('to_time')])) : '' }}" class="text-primary" title="Click to view detailed calculation">
                                                    <strong>{{ number_format($row['calculated_fee'], 0, ',', '.') }} VND </strong>
                                                    <i class="fas fa-external-link-alt ml-1" style="font-size: 10px;"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @php $previousRow = $row; @endphp
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <th colspan="8" class="text-right" title="Chỉ lấy các Cấu hình cuối cùng nếu có thay đổi">TỔNG CỘNG:</th>
                                            <th class="text-left">

                                                    <span class="" style=" padding: 5px 5px;">{{ $formatted = number_format($totalPriceMonth, 0, ',', '.') }} VND / Tháng</span>

                                            </th>
                                            <th>{{ number_format(count($results), 0, ',', '.') }} records</th>
                                            <th class="text-right">
                                                <strong class="text-danger">{{ number_format($totalCost, 0, ',', '.') }} VND </strong>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- Filter Button Section -->
                            <div class="card-footer" style="background-color: #f8f9fa; padding: 10px; border-top: 1px solid #ddd;">
                                <button type="button" class="btn btn-primary" id="filterVpsBtn" onclick="applyVpsFilter()" disabled>
                                    <i class="fas fa-filter"></i> Filter VPS (0 selected)
                                </button>
                                <button type="button" class="btn btn-secondary" id="clearFilterBtn" onclick="clearVpsFilter()" style="display: none;">
                                    <i class="fas fa-times"></i> Clear Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>





            </div>
        </section>

        <!-- Export Buttons -->
        <section class="content mx-2 mb-5">
        <div class="row">
            <div class="col-12">

                <button type="button" class="btn btn-success ml-2 mb-3 float-right" id="exportExcelBtn111">
                    <a href="{{ route('vps.billing.excel', request()->query()) }}" class="text-white" style="text-decoration: none;">
                    Export Excel
                    </a>
                </button>


                <?php
                if(isAdminCookie())
                 {
?>
                <a href="{{ route('vps.billing.send-email-form', request()->query()) }}" class="btn btn-primary ml-2 mb-3 float-right">
                    <i class="fas fa-envelope mr-2"></i> Send Mail
                </a>

                <!-- <button type="button" class="btn btn-info ml-2" id="exportPngBtn">
                    <i class="fas fa-image mr-2"></i> Export PNG
                </button>
                                <button type="button" class="btn btn-success" id="exportPdfBtnxxx">
                    <i class="fas fa-file-pdf mr-2"></i>
                    <a href="{{ route('vps.billing.pdf', array_merge(['email' => $user->email], request()->only(['to_time', 'from_time']))) }}" class="text-white" style="text-decoration: none;">
                    Export PDF
                    </a>
                </button> -->

                <?php
                }
                ?>

            </div>
        </div>
        </section>
    </div>



    <script>
    // VPS Filter Functions
    function selectAllVpsCheckboxes(selectAllCheckbox) {
        const checkboxes = document.querySelectorAll('.vps-filter-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
        updateFilterButton();
    }

    function updateFilterButton() {
        const checkboxes = document.querySelectorAll('.vps-filter-checkbox:checked');
        const filterBtn = document.getElementById('filterVpsBtn');
        const clearBtn = document.getElementById('clearFilterBtn');

        const count = checkboxes.length;
        filterBtn.disabled = count === 0;
        filterBtn.innerHTML = `<i class="fas fa-filter"></i> Filter VPS (${count} selected)`;

        // Show current filter status if any
        const urlParams = new URLSearchParams(window.location.search);
        const hasFilter = urlParams.has('limit_vps_id');
        if (hasFilter) {
            clearBtn.style.display = 'inline-block';
        }
    }

    function applyVpsFilter() {
        const checkboxes = document.querySelectorAll('.vps-filter-checkbox:checked');
        const selectedIds = Array.from(checkboxes).map(cb => cb.value).join(',');

        if (!selectedIds) {
            alert('Vui lòng chọn ít nhất một VPS!');
            return;
        }

        // Get current URL and add/update limit_vps_id parameter
        const url = new URL(window.location);
        url.searchParams.set('limit_vps_id', selectedIds);
        window.location.href = url.toString();
    }

    function clearVpsFilter() {
        const url = new URL(window.location);
        url.searchParams.delete('limit_vps_id');
        window.location.href = url.toString();
    }

    // Check if there's an active filter and highlight checkboxes
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const limitId = urlParams.get('limit_vps_id');

        if (limitId) {
            const ids = limitId.split(',');
            const checkboxes = document.querySelectorAll('.vps-filter-checkbox');
            checkboxes.forEach(cb => {
                if (ids.includes(cb.value)) {
                    cb.checked = true;
                }
            });
            document.getElementById('clearFilterBtn').style.display = 'inline-block';
        }

        updateFilterButton();
    });

    // Filter by payment date
    function filterByDate() {
        const fromInput = document.getElementById('fromDate');
        const toInput = document.getElementById('paymentDate');
        const fromValue = fromInput.value;
        const toValue = toInput.value;

        if (!fromValue && !toValue) {
            alert('Vui lòng chọn ít nhất một ngày!');
            return;
        }

        const url = new URL(window.location);
        if (fromValue) {
            url.searchParams.set('from_time', fromValue);
        } else {
            url.searchParams.delete('from_time');
        }
        if (toValue) {
            url.searchParams.set('to_time', toValue);
        } else {
            url.searchParams.delete('to_time');
        }
        window.location.href = url.toString();
    }

    // Clear date filter
    function clearDate() {
        const url = new URL(window.location);
        url.searchParams.delete('to_time');
        url.searchParams.delete('from_time');
        window.location.href = url.toString();
    }

    // Function to highlight config changes
    function highlightConfigChanges(rows) {
        for (let i = 1; i < rows.length; i++) {
            const currentRow = rows[i];
            const previousRow = rows[i - 1];

            const currentInstanceId = currentRow.getAttribute('data-instance-id');
            const previousInstanceId = previousRow.getAttribute('data-instance-id');
            const powerState = currentRow.querySelector('td.power-state');

            // Check if it's OLD_CONFIG and same instance
            if (currentInstanceId === previousInstanceId && powerState && powerState.textContent.includes('OLD_CONFIG')) {
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

        const exportExcelBtn = document.getElementById('exportExcelBtn');
        if (exportExcelBtn) {
            exportExcelBtn.addEventListener('click', function() {
                exportToExcel();
            });
        }

        const exportPngBtn = document.getElementById('exportPngBtn');
        if (exportPngBtn) {
            exportPngBtn.addEventListener('click', function() {
                exportToPng();
            });
        }
    });

    function exportToPdf() {
        // Get content to export - only the report content, not the button
        const element = document.querySelector('.all_content_report_vps');
        const fileName = `vps-billing-${new Date().toISOString().split('T')[0]}.pdf`;

        // Clone element and remove extra padding
        const clonedElement = element.cloneNode(true);
        clonedElement.style.padding = '0';
        clonedElement.style.margin = '0';

        // Options for html2pdf - optimize to fit in 1 page
        const options = {
            margin: [1, 1, 1, 1],  // Minimal margins (top, left, bottom, right) in mm
            filename: fileName,
            image: { type: 'jpeg', quality: 0.95 },
            html2canvas: {
                scale: 1.0,  // Reduced from 1.5 to reduce whitespace
                useCORS: true,
                allowTaint: true,
                logging: false
            },
            jsPDF: {
                orientation: 'landscape',
                unit: 'mm',
                format: [600, 400],  // Custom size to fit content
                compress: true
            },
            pagebreak: { mode: ['avoid-all'] }  // Avoid page breaks
        };

        // Check if html2pdf library is loaded
        if (typeof html2pdf === 'undefined') {
            // Load library dynamically
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
            script.onload = function() {
                html2pdf().set(options).from(clonedElement).save();
            };
            document.head.appendChild(script);
        } else {
            html2pdf().set(options).from(clonedElement).save();
        }
    }

    function exportToPng() {
        // Get content to export
        const element = document.querySelector('.all_content_report_vps');
        const fileName = `vps-billing-${new Date().toISOString().split('T')[0]}.png`;

        // Load html2canvas library if not loaded
        if (typeof html2canvas === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
            script.onload = function() {
                captureAsImage(element, fileName);
            };
            document.head.appendChild(script);
        } else {
            captureAsImage(element, fileName);
        }
    }

    function captureAsImage(element, fileName) {
        html2canvas(element, {
            scale: 2,
            useCORS: true,
            allowTaint: true,
            logging: false,
            backgroundColor: '#ffffff'
        }).then(canvas => {
            const link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = fileName;
            link.click();
        }).catch(err => {
            console.error('Error capturing image:', err);
            alert('Error capturing PNG. Please try again.');
        });
    }

    function exportToExcel() {
        // Get current URL parameters
        const url = new URL(window.location);
        const params = new URLSearchParams(url.search);

        // Build download URL with parameters
        let downloadUrl = '{{ route("vps.billing.excel") }}?email={{ $user->email }}';

        // Add filter parameters if they exist
        if (params.has('to_time')) {
            downloadUrl += '&to_time=' + params.get('to_time');
        }
        if (params.has('from_time')) {
            downloadUrl += '&from_time=' + params.get('from_time');
        }

        // Trigger download
        window.location.href = downloadUrl;
    }
    </script>

@endsection
