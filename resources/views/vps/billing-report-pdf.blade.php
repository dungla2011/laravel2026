<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>VPS Billing Report - {{ $user->email }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; line-height: 1.4; }
        .container { padding: 20px; }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 2px solid #667eea; 
            padding-bottom: 10px;
        }
        .header h1 { font-size: 20px; color: #333; margin-bottom: 5px; }
        .header p { font-size: 9px; color: #666; }
        
        .info-section { 
            margin: 15px 0; 
            padding: 10px; 
            background: #f8f9fa; 
            border-left: 4px solid #667eea;
        }
        .info-section strong { display: inline-block; width: 100px; }
        
        .summary-box {
            background: #667eea;
            color: white;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 3px;
            text-align: center;
        }
        .summary-box .label { font-size: 9px; }
        .summary-box .amount { 
            font-size: 16px; 
            font-weight: bold; 
            margin: 5px 0;
        }
        
        .section-title { 
            font-size: 12px; 
            font-weight: bold; 
            margin-top: 15px; 
            margin-bottom: 10px; 
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 10px 0;
            font-size: 9px;
        }
        th { 
            background: #e7e7e7; 
            padding: 6px; 
            text-align: left; 
            font-weight: 600; 
            border: 1px solid #ccc;
        }
        td { 
            padding: 5px 6px; 
            border: 1px solid #ddd; 
        }
        tr:nth-child(even) { background: #f9f9f9; }
        
        .cost { 
            text-align: right; 
            font-weight: 600; 
        }
        .new-instance { 
            border-top: 2px solid #2196F3 !important;
            background: #e3f2fd !important;
        }
        .powered-on { 
            color: #4caf50; 
            font-weight: 600; 
        }
        .powered-off { 
            color: #f44336; 
            font-weight: 600; 
        }
        
        .page-break { page-break-after: always; }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 8px;
            color: #999;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>💰 VPS Billing Report</h1>
            <p>
                <strong>User:</strong> {{ $user->email }} (ID: {{ $user->id }})<br>
                @if($date_from || $date_to)
                    <strong>Period:</strong> 
                    @if($date_from) {{ \Carbon\Carbon::parse($date_from)->format('d/m/Y') }} @endif
                    @if($date_from && $date_to) to @endif
                    @if($date_to) {{ \Carbon\Carbon::parse($date_to)->format('d/m/Y') }} @endif
                    <br>
                @endif
                <strong>Generated:</strong> {{ $generated_at->format('d/m/Y H:i:s') }}
            </p>
        </div>

        <!-- Summary Info -->
        <div class="info-section">
            <strong>Đã thanh toán:</strong> {{ number_format($totalRecharge / 1000000, 2, ',', '.') }}M VND<br>
            <strong>Tổng chi phí:</strong> {{ number_format($totalCost, $decimal, $dec_separator, $thousands_separator) }}K VND<br>
            <strong>Số dư:</strong> {{ number_format(($totalRecharge / 1000000 - $totalCost / 1000) * 1000000, $decimal, $dec_separator, $thousands_separator) }}K VND
        </div>

        <!-- Main Summary -->
        <div class="summary-box">
            <div class="label">TỔNG CHI PHÍ DỊCH VỤ</div>
            <div class="amount">{{ number_format($totalCost, $decimal, $dec_separator, $thousands_separator) }}K</div>
            <div style="font-size: 10px;">≈ {{ number_format($totalCost / 1000, 2, ',', '.') }}M VND</div>
        </div>

        <!-- Quick Stats -->
        <table>
            <tr>
                <td><strong>Tổng Records:</strong> {{ count($results) }}</td>
                <td><strong>Tổng Instances:</strong> {{ count($instanceTotals) }}</td>
                <td class="cost"><strong>Tháng/Năm:</strong> {{ number_format($totalPriceMonth, 0, $dec_separator, $thousands_separator) }}K</td>
            </tr>
        </table>

        <!-- Detailed Records -->
        <div class="section-title page-break">📋 Detailed VPS Usage Records</div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Inst Name</th>
                    <th>Created</th>
                    <th>Duration</th>
                    <th>CPU/RAM/Disk/IP</th>
                    @if ($hasAnyPriceMonth)
                    <th>Price/M</th>
                    @endif
                    <th>Power</th>
                    <th style="text-align: right;">Fee (K)</th>
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
                    <td>{{ $row['usage_id'] }}</td>
                    <td>{{ $row['instance_name'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($row['created_at'])->format('m/d H:i') }}</td>
                    <td style="font-size: 8px;">{{ $row['time_usage'] }}</td>
                    <td>{{ $row['cpu'] }}/{{ $row['ram_gb'] }}/{{ $row['disk_gb'] }}/{{ $row['ip_count'] }}</td>
                    @if ($hasAnyPriceMonth)
                    <td style="text-align: center;">
                        @if ($row['price_month'] && !$isPoweredOff)
                            {{ number_format($row['price_month'], 0) }}K
                        @else
                            -
                        @endif
                    </td>
                    @endif
                    <td>
                        <span class="{{ strtoupper($row['power_state']) === 'POWERED_ON' ? 'powered-on' : 'powered-off' }}">
                            {{ substr($row['power_state'], 0, 6) }}
                        </span>
                    </td>
                    <td class="cost">
                        @if (!$isPoweredOff)
                            {{ number_format($row['calculated_fee'], $decimal, $dec_separator, $thousands_separator) }}
                        @else
                            0
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #f0f0f0; font-weight: bold;">
                    <td colspan="6" style="text-align: right;">TỔNG CỘNG:</td>
                    <td>{{ count($results) }} rows</td>
                    <td class="cost">{{ number_format($totalCost, $decimal, $dec_separator, $thousands_separator) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Generated on {{ $generated_at->format('Y-m-d H:i:s') }}</p>
            <p>This is an automated report and requires no signature</p>
        </div>
    </div>
</body>
</html>
