<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>VPS Billing Report - {{ $user->email }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        .container { padding: 20px; }
        h1 { font-size: 18px; margin-bottom: 10px; }
        .summary { background: #667eea; color: white; padding: 15px; margin-bottom: 20px; }
        .summary .total { font-size: 24px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 9px; }
        th { background: #f0f0f0; padding: 8px; text-align: left; font-weight: 600; border: 1px solid #ddd; }
        td { padding: 8px; border: 1px solid #eee; }
        .cost { text-align: right; font-weight: 600; }
        .new-instance { border-top: 2px solid #2196F3; }
        .powered-on { color: #4caf50; font-weight: 600; }
        .powered-off { color: #f44336; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <h1>💰 VPS Billing Report</h1>
        <p style="margin-bottom: 15px;">
            <strong>User:</strong> {{ $user->email }} (ID: {{ $user->id }})<br>
            @if($date_from || $date_to)
            <strong>Period:</strong> 
            @if($date_from) {{ \Carbon\Carbon::parse($date_from)->format('Y-m-d') }} @endif
            @if($date_from && $date_to) to @endif
            @if($date_to) {{ \Carbon\Carbon::parse($date_to)->format('Y-m-d') }} @endif
            <br>
            @endif
            <strong>Generated:</strong> {{ $generated_at->format('Y-m-d H:i:s') }}
        </p>
        
        <div class="summary">
            <div style="font-size: 14px; margin-bottom: 5px;">Total Cost</div>
            <div class="total">{{ number_format($totalCost, $decimal, $dec_separator, $thousands_separator) }} K VND</div>
            <div style="margin-top: 5px; font-size: 11px;">
                ≈ {{ number_format($totalCost / 1000, $decimal, $dec_separator, $thousands_separator) }} Million VND
            </div>
        </div>
        
        <h2 style="font-size: 14px; margin-top: 20px;">Instance Totals Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Instance ID</th>
                    <th>Instance Name</th>
                    <th>Records</th>
                    <th style="text-align: right;">Total Cost (K VND)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($instanceTotals as $instId => $instData)
                <tr>
                    <td>{{ $instId }}</td>
                    <td>{{ $instData['name'] }}</td>
                    <td>{{ number_format($instData['count'], 0) }}</td>
                    <td class="cost">{{ number_format($instData['total'], $decimal, $dec_separator, $thousands_separator) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <h2 style="font-size: 14px; margin-top: 20px; page-break-before: always;">All VPS Usage Records (Detail)</h2>
        <table>
            <thead>
                <tr>
                    <th>Inst ID</th>
                    <th>Name</th>
                    <th>Created</th>
                    <th>Time</th>
                    <th>CPU/RAM/Disk/IP</th>
                    @if ($hasAnyPriceMonth)
                    <th>Price/M</th>
                    @endif
                    <th>Power</th>
                    <th style="text-align: right;">Fee</th>
                    <th>Formula</th>
                </tr>
            </thead>
            <tbody>
                @php $currentInstanceId = null; @endphp
                @foreach ($results as $row)
                @php 
                    $isNewInstance = $currentInstanceId !== $row['instance_id'];
                    $currentInstanceId = $row['instance_id'];
                    $isPoweredOff = strtoupper($row['power_state']) === 'POWERED_OFF';
                @endphp
                <tr @if($isNewInstance) class="new-instance" @endif>
                    <td>{{ $row['instance_id'] }}</td>
                    <td>{{ $row['instance_name'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($row['created_at'])->format('m-d H:i') }}</td>
                    <td><small>{{ $row['time_usage'] }}</small></td>
                    <td>{{ $row['cpu'] }}/{{ $row['ram_gb'] }}/{{ $row['disk_gb'] }}/{{ $row['ip_count'] }}</td>
                    @if ($hasAnyPriceMonth)
                    <td>
                        @if ($row['price_month'] && !$isPoweredOff)
                        {{ number_format($row['price_month'], 0) }}K
                        @else
                        -
                        @endif
                    </td>
                    @endif
                    <td class="{{ strtoupper($row['power_state']) === 'POWERED_ON' ? 'powered-on' : 'powered-off' }}">
                        {{ $row['power_state'] }}
                    </td>
                    <td class="cost">{{ number_format($row['calculated_fee'], $decimal, $dec_separator, $thousands_separator) }}</td>
                    <td>
                        @if (!$isPoweredOff)
                        <small>{!! nl2br(e($row['fee_text'])) !!}</small>
                        @else
                        -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
