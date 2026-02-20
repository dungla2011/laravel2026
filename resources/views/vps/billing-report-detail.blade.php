<?php
$uid = getCurrentUserId();
?>
@extends("layouts.member")
@section('header')
    @include('parts.header-all')
@endsection

@section('title')
    {{ \App\Models\SiteMng::getTitle() }}
@endsection

@section("css")
    <style>
        .compact-table td { padding: 5px 10px; font-size: 13px; }
        .compact-table th { padding: 5px 10px; font-size: 13px; background: #f8f9fa; width: 120px; }
        .formula-box { background: #f1f3f5; padding: 12px; border-radius: 4px; font-family: monospace; font-size: 13px; border-left: 3px solid #007bff; }
        .result-box { background: #d3f9d8; padding: 15px; border-radius: 4px; border-left: 3px solid #51cf66; }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-8">
                        <h4 class="m-0">Chi tiết tính phí #{{ $usage->id }} - {{ $instance ? $instance->name : 'N/A' }}</h4>
                    </div>
                    <div class="col-sm-4 text-right">
                        <a href="{{ route('vps.billing.report') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                
                <div class="row">
                    <!-- Left: Info -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header p-2">
                                <strong>Thông tin</strong>
                            </div>
                            <div class="card-body p-2">
                                <table class="table table-sm compact-table mb-0">
                                    <tr>
                                        <th>Instance</th>
                                        <td>{{ $usage->instance_id }} - {{ $instance->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Power</th>
                                        <td>
                                            <span class="badge {{ strtoupper($usage->power_state) === 'POWERED_ON' ? 'badge-success' : 'badge-danger' }}">
                                                {{ $usage->power_state }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created</th>
                                        <td>{{ \Carbon\Carbon::parse($usage->created_at)->format('m-d H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Timestamp</th>
                                        <td>{{ \Carbon\Carbon::parse($usage->timestamp_minute)->format('m-d H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Config</th>
                                        <td>
                                            <span class="badge badge-info">{{ $usage->cpu }}C</span>
                                            <span class="badge badge-info">{{ $usage->ram_gb }}G</span>
                                            <span class="badge badge-info">{{ $usage->disk_gb }}D</span>
                                            <span class="badge badge-info">{{ $usage->number_ip_address }}IP</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Calculation -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header p-2">
                                <strong><i class="fas fa-calculator"></i> Công thức tính phí</strong>
                            </div>
                            <div class="card-body p-3">

                                @if($details['type'] === 'fixed_monthly')
                                    <div class="formula-box">
                                        <strong>Fixed Monthly:</strong><br>
                                        Price: {{ number_format($details['price_month'], 0) }} K/tháng<br>
                                        Duration: {{ $details['duration_minutes'] }} phút ({{ $details['duration_days'] }}d {{ $details['duration_hours'] }}h {{ $details['duration_mins'] }}m)<br>
                                        <br>
                                        Fee = {{ number_format($details['price_month'], 0) }} × {{ $details['duration_minutes'] }} / 43200 = <strong class="text-primary">{{ number_format($details['fee'], 0) }} K</strong>
                                    </div>
                                @else
                                    @php $priceConfig = json_decode($usage->price_config, true); @endphp
                                    
                                    <table class="table table-sm table-bordered compact-table mb-2">
                                        <tr>
                                            <th>Resource</th>
                                            <th>Price/30d</th>
                                            <th>Qty</th>
                                            <th>Daily Fee</th>
                                        </tr>
                                        <tr>
                                            <td>CPU</td>
                                            <td>{{ number_format($priceConfig['n_cpu_core_price'] ?? 0, 0) }}K</td>
                                            <td>{{ $details['cpu_count'] }}</td>
                                            <td>{{ number_format($details['cpu_daily_fee'], 1) }}K</td>
                                        </tr>
                                        <tr>
                                            <td>RAM</td>
                                            <td>{{ number_format($priceConfig['n_ram_gb_price'] ?? 0, 0) }}K</td>
                                            <td>{{ $details['ram_gb'] }}</td>
                                            <td>{{ number_format($details['ram_daily_fee'], 1) }}K</td>
                                        </tr>
                                        <tr>
                                            <td>Disk</td>
                                            <td>{{ number_format($priceConfig['n_gb_disk_price'] ?? 0, 0) }}K</td>
                                            <td>{{ $details['disk_gb'] }}</td>
                                            <td>{{ number_format($details['disk_daily_fee'], 1) }}K</td>
                                        </tr>
                                        <tr>
                                            <td>IP</td>
                                            <td>{{ number_format($priceConfig['n_ip_address_price'] ?? 0, 0) }}K</td>
                                            <td>{{ $details['ip_count'] }}</td>
                                            <td>{{ number_format($details['ip_daily_fee'], 1) }}K</td>
                                        </tr>
                                        <tr class="bg-light">
                                            <td colspan="3"><strong>Total Daily</strong></td>
                                            <td><strong>{{ number_format($details['daily_total_fee'], 1) }}K</strong></td>
                                        </tr>
                                    </table>

                                    <div class="formula-box">
                                        Duration: {{ $details['duration_minutes'] }} phút = {{ number_format($details['duration_minutes'] / 1440, 4) }} days<br>
                                        <br>
                                        Fee = {{ number_format($details['daily_total_fee'], 1) }} K/day × {{ number_format($details['duration_minutes'] / 1440, 4) }} days<br>
                                        = <strong class="text-primary">{{ number_format($details['fee'], 0) }} K VND</strong>
                                    </div>
                                @endif

                                <div class="result-box mt-3">
                                    <strong>Final Fee: </strong>
                                    <span class="text-success" style="font-size: 24px; font-weight: bold;">
                                        {{ number_format($details['fee'], 0, ',', '.') }} K
                                    </span>
                                    @if(isset($details['powered_off']) && $details['powered_off'])
                                        <span class="badge badge-danger ml-2">POWERED_OFF = 0</span>
                                    @endif
                                </div>

                            </div>
                        </div>

            </div>
        </section>
    </div>
@endsection
