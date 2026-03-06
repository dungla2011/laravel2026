<?php

namespace App\Services;

use App\Components\ClassMail1;
use App\Components\ClassMailV2;
use App\Models\PartnerInfo;
use App\Models\User;
use App\Models\VpsUsage;
use App\Models\VpsInstance;
use LadLib\Common\cstring2;
use PHPMailer\PHPMailer\PHPMailer;

class VpsBillingReportService
{
    /**
     * Generate billing report data for a user
     *
     * @param User $user Target user
     * @param array $options Additional options (date_from, date_to, etc.)
     * @return array Report data with results, totals, and summary
     */
    public function generateReportData(User $user, array $options = [], $fromTime = null, $toTime = null): array
    {


        // Number format for VND currency
        $decimal = 0;
        $dec_separator = ',';
        $thousands_separator = '.';

        // Query usages with optional date filtering
        // Get VPS usages through vps_instances by user_id (not from vps_usages.user_id)
        $query = VpsUsage::whereHas('instance', function($q) use ($user) {
            $q->where('user_id', $user->id)
              ->whereNull('deleted_at');
        })
            ->whereNull('deleted_at');

        if (!empty($options['date_from'])) {
            $query->where('created_at', '>=', $options['date_from']);
        }

        if (!empty($options['date_to'])) {
            $query->where('created_at', '<=', $options['date_to']);
        }

        $usages = $query
//            ->orderBy('timestamp_minute', 'ASC')
            ->orderBy('instance_id', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->get();

        // Find the latest usage_id for each instance_id
        $latestUsageByInstance = [];
        foreach ($usages as $usage) {
            $instId = $usage->instance_id;
            if (!isset($latestUsageByInstance[$instId])) {
                $latestUsageByInstance[$instId] = $usage->id;
            }
        }

        $results = [];
        $totalCost = 0;
        $totalLatestCost = 0;  // Only latest config per instance
        $totalPriceMonth = 0;
        $instanceTotals = [];
        $hasAnyPriceConfig = false;
        $hasAnyPriceMonth = false;

        // Calculate total recharge amount
        $totalRecharge = \App\Models\UserRecharge::where('user_id', $user->id)
            ->sum('amount');
        $totalRecharge = round($totalRecharge, 0);


        foreach ($usages as $usage) {
            // Get instance info
            $instance = VpsInstance::find($usage->instance_id);
            if (!$instance) continue;

            $instanceName = $instance->name;
            $instanceId = $instance->id;

            // Parse price_config
            $priceConfig = $usage->price_config ? json_decode($usage->price_config, true) : null;
            $configDisplay = '';
            $configPrice = 0;
            $priceMonth = $usage->price_month;

            // Track if any instance uses price_config or price_month
            if (!$priceMonth) {
                $hasAnyPriceConfig = true;
            } else {
                $hasAnyPriceMonth = true;
            }

            // Calculate price from config if no price_month
            if (!$priceMonth && $priceConfig) {
                $cpuPrice = ($priceConfig['n_cpu_core_price'] ?? 0) * $usage->cpu;
                $ramPrice = ($priceConfig['n_ram_gb_price'] ?? 0) * $usage->ram_gb;
                $diskPrice = ($priceConfig['n_gb_disk_price'] ?? 0) * $usage->disk_gb;
                $ipPrice = ($priceConfig['n_ip_address_price'] ?? 0) * $usage->number_ip_address;

                $configPrice = $cpuPrice + $ramPrice + $diskPrice + $ipPrice;
                $configDisplay = sprintf(
                    'CPU: %.1fK, RAM: %.1fK, Disk: %.1fK, IP: %.1fK',
                    $cpuPrice,
                    $ramPrice,
                    $diskPrice,
                    $ipPrice
                );
            }

            // Calculate fee - unified in VpsUsage::calculateFee()
            if($usage instanceof VpsUsage);
            $feeResult = $usage->calculateFee($fromTime , $toTime );
            $fee = $feeResult['fee'];
            $feeText = $feeResult['text'];

            // POWERED_OFF = no charge
            if (strtoupper($usage->power_state) === 'POWERED_OFF') {
                $fee = 0;
                $feeText .= " (POWERED_OFF = No charge)";
            }

            $totalCost += $fee;

            // Check if this is the latest usage for this instance
            $isLatest = ($latestUsageByInstance[$instanceId] ?? null) === $usage->id;

            // Sum price_month for latest config (with fallback to price_config)
            if ($isLatest && strtoupper($usage->power_state) !== 'POWERED_OFF') {
                $displayPrice = $priceMonth ?? $this->extractPriceFromConfig($priceConfig, $usage->cpu, $usage->ram_gb, $usage->disk_gb, $usage->number_ip_address);
                if ($displayPrice) {
                    $totalPriceMonth += $displayPrice;
                }
            }

            // Add to totalLatestCost only if this is latest config
            if ($isLatest) {
                $totalLatestCost += $fee;
            }
            // Use last_billing_start_at if set, otherwise use created_at
            $startTime = $usage->last_billing_start_at ?: $usage->created_at;
            $createdTime = new \DateTime($startTime);
//            $timestampTime = new \DateTime($usage->timestamp_minute);

            $endTime = $toTime ?: $usage->timestamp_minute;
            //Nếu ko còn dùng nữa, là đã hết hạn, thì $endTime tính đến lúc dùng thôi
//            if($usage->end_time_used)
            if($usage->power_state == 'OLD_CONFIG')
            {
                $endTime = $usage->timestamp_minute;
            }

            $interval = $createdTime->diff(new \DateTime($endTime));
            $timeUsage = '';
            if ($interval->days > 0) $timeUsage .= $interval->days . ' ngày ';
            if ($interval->h > 0 || $interval->days > 0) $timeUsage .= $interval->h . ' giờ ';
            $timeUsage .= $interval->i . ' phút ';// " xxx = $startTime - $endTime ";

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
                'usage_id' => $usage->id,
                'instance_id' => $instanceId,
                'instance_name' => $instanceName,
                'list_ip_address' => $usage->list_ip_address,
                'timestamp' => $endTime,
                'created_at' => $usage->created_at,
                'last_billing_start_at' => $usage->last_billing_start_at,
                'time_usage' => $timeUsage,
                'cpu' => $usage->cpu,
                'ram_gb' => $usage->ram_gb,
                'disk_gb' => $usage->disk_gb,
                'ip_count' => $usage->number_ip_address,
                'price_month' => $priceMonth,
                'price_config' => $configPrice,  // Store numeric value, not string
                'price_config_display' => $configDisplay,  // Store display string separately
                'calculated_fee' => $fee,
                'fee_text' => $feeText,
                'power_state' => $usage->power_state,
                'is_latest_config' => $isLatest
            ];
        }

        $ret = [
            'user' => $user,
            'results' => $results,
            'totalCost' => $totalCost,
            'totalLatestCost' => $totalLatestCost,
            'totalPriceMonth' => $totalPriceMonth,
            'totalRecharge' => round($totalRecharge, 0),
            'instanceTotals' => $instanceTotals,
            'hasAnyPriceConfig' => $hasAnyPriceConfig,
            'hasAnyPriceMonth' => $hasAnyPriceMonth,
            'decimal' => $decimal,
            'dec_separator' => $dec_separator,
            'thousands_separator' => $thousands_separator,
            'generated_at' => now(),
            'date_from' => $options['date_from'] ?? null,
            'date_to' => $options['date_to'] ?? null,
        ];

        return $ret;

    }

    /**
     * Generate HTML view for billing report
     *
     * @param User $user Target user
     * @param array $options Additional options
     * @return string HTML content
     */
    public function generateHtml(User $user, array $options = [], $fromTime = null, $toTime =  null): string
    {
        $data = $this->generateReportData($user, $options, $fromTime, $toTime);
        return view('vps.billing-report', $data)->render();
    }

    /**
     * Generate PDF for billing report
     *
     * Note: Server-side PDF includes HTML content and CSS styling from billing-report view.
     * JavaScript logic (highlighting, connector lines) runs client-side and won't appear in server-side PDF.
     * For full PDF with all visual effects, use client-side export (PNG/PDF buttons in view).
     *
     * @param User $user Target user
     * @param array $options Additional options
     * @return \Barryvdh\DomPDF\PDF PDF instance
     */
    public function generatePdf(User $user, array $options = [],$fromTime = null, $toTime =  null)
    {
        $data = $this->generateReportData($user, $options,$fromTime , $toTime );


//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($data['results']);
//        echo "</pre>";
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($data['user']);
//        echo "</pre>";
//        die();

        // Render the full view
        $fullHtml = view('vps.billing-report', $data)->render();

        // Extract only the report content (without export buttons)
        // Look for the .all_content_report_vps section
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $fullHtml);

        $xpath = new \DOMXPath($dom);
        $reportSection = $xpath->query("//*[contains(@class, 'all_content_report_vps')]")->item(0);

        if ($reportSection) {
            // Extract styles from head
            $styles = $xpath->query("//style");
            $styleHtml = '';
            foreach ($styles as $style) {
                $styleHtml .= '<style>' . $style->nodeValue . '</style>';
            }

            // Build minimal HTML with styles and extracted content
            $extractedHtml = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    ' . $styleHtml . '
    <style>
        * {
            font-family: "DejaVu Sans", Arial, sans-serif;
        }
        body {
            margin: 8px;
            padding: 8px;
            font-family: "DejaVu Sans", Arial, sans-serif;
            charset: utf-8;
        }
        .info-box-content{
            border: 1px dashed #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }
            .select_date_bill{
                display: none;
            }
        .all_content_report_vps {
            padding: 0 !important;
            margin: 0 !important;
        }
        /* Table styling for PDF - explicit for DomPDF */
        table {
            border-collapse: collapse !important;
            width: 100%;
            margin-bottom: 10px;
        }
        table th {
            border: 2px solid #000 !important;
            padding: 6px 8px !important;
            background-color: #f0f0f0 !important;
            font-weight: bold !important;
            text-align: left;
            min-height: 20px;
        }
        table td {
            border: 1px solid blue !important;
            padding: 6px 8px !important;
            min-height: 18px;
            vertical-align: middle;
        }
        table thead {
            background-color: #f0f0f0 !important;
        }
        table thead th {
            background-color: #f0f0f0 !important;
        }
        table tfoot {
            background-color: #f5f5f5 !important;
        }
        table tfoot td {
            font-weight: bold !important;
            background-color: #f5f5f5 !important;
        }
        tr {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>';

            // Get the innerHTML of the report section
            $reportContent = $dom->saveHTML($reportSection);

            // Add cellpadding and cellspacing to all tables for DomPDF compatibility
            $reportContent = preg_replace_callback(
                '/<table([^>]*)>/i',
                function($matches) {
                    $attrs = $matches[1];
                    // Only add if not already present
                    if (stripos($attrs, 'cellpadding') === false) {
                        $attrs .= ' cellpadding="8" cellspacing="0"';
                    }
                    return '<table' . $attrs . '>';
                },
                $reportContent
            );

            // Remove all <a> tags but keep their content (with DOTALL flag for newlines)
            $reportContent = preg_replace('/<a[^>]*>(.*?)<\/a>/is', '$1', $reportContent);

            // Clean up the extracted HTML
            $extractedHtml .= $reportContent . '</body></html>';

            // Create PDF from extracted content
            $pdf = \PDF::loadHTML($extractedHtml);
        } else {
            // Fallback: use full view if extraction fails
            $pdf = \PDF::loadHTML($fullHtml);
        }

        // Set paper size and orientation to match the view layout
        $pdf->setPaper('a3', 'landscape');

        // Additional PDF settings for better rendering and Unicode support
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isPhpEnabled', true);
        $pdf->setOption('dpi', 300);
        $pdf->setOption('defaultFont', 'DejaVu Sans');
        $pdf->setOption('fontDir', config_path('pdf-fonts'));
        $pdf->setOption('enable_unicode', true);

        if(request('html')){
            die($extractedHtml);
        }

        return $pdf;

    }

    static function getFreeMoneyVpsBilling($userIdOrObj)
    {
        $billS = new VpsBillingReportService();

        if(is_numeric($userIdOrObj)){
            $userIdOrObj = User::find($userIdOrObj);
        }

        if(!$userIdOrObj){
            loi("isAllowCreateVps Not valid userid!");
        }

        $mm = $billS->generateReportData($userIdOrObj, [], null, nowyh());

        $totalCost = $mm['totalCost'] ?? 0;
        $totalCost1000 = $totalCost * 1000;

        $totalRecharge = $mm['totalRecharge'] ?? 0;
        $totalRechargeNoVAt = $totalRecharge / 1.1;
//        echo "<br/>\n $totalRechargeNoVAt - $totalCost1000  = " . ($totalRechargeNoVAt - $totalCost1000);
//
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($mm);
//        echo "</pre>";

        $free = $totalRechargeNoVAt - $totalCost1000;
        return $free;

    }

    /**
     * Generate Excel for billing report
     *
     * @param User $user Target user
     * @param array $options Additional options
     * @return \Symfony\Component\HttpFoundation\StreamedResponse Excel/CSV file download
     */
    public function generateExcel(User $user, array $options = [], $fromTime = null, $toTime = null)
    {
        $data = $this->generateReportData($user, $options, $fromTime, $toTime);

        // if(@request('dump'))
            {
            // print_r($data);
            // die();
        }


        $fileName = sprintf(
            'vps-billing-%s-%s.xlsx',
            $user->id,
            now()->format('Y-m-d_His')
        );

        // Use PhpOffice\PhpSpreadsheet\IOFactory for Excel generation
        if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory'))
            return $this->generateExcelWithPhpOffice($data, $fileName, $fromTime, $toTime);

        return $this->generateCsvExport($data, $fileName, $fromTime, $toTime);
    }

    /**
     * Generate spreadsheet data structure (shared by both Excel and CSV methods)
     * This is the centralized place to maintain data structure - modify here for both formats
     */
    private function generateSpreadsheetData($data)
    {
        $uid = $data['user']->id ?? 0;
        $pn = \App\Models\PartnerInfo::where("user_id", $uid)->first();
        $kh = "Đơn vị sử dụng: ";
        $email = $data['user']->email ?? ' Lỗi email ';
        if($pn) {
            $kh .= $pn->partner_name . " " . $pn->tax_code . " (" . $email . ")";
        }
        else{
            $kh .= " $email ";
        }

        $rows = [];

        // Header
        $rows[] = ['ĐỐI SOÁT DỊCH VỤ - CÔNG TY CÔNG NGHỆ SỐ GALAXY VIETNAM - GLX.COM.VN'];
        $rows[] = [$kh];
        $timeDoiSoat = $data['results'][0]['timestamp'] ?? nowyh();
        $rows[] = ["Thời điểm đối soát: " . $timeDoiSoat];
        if ($data['date_from']) {
            $rows[] = ['Date From', $data['date_from']];
        }
        if ($data['date_to']) {
            $rows[] = ['Date To', $data['date_to']];
        }
        $rows[] = [];

        // Summary
        $rows[] = ['Tổng hợp (Chưa gồm thuế VAT)'];
        $rows[] = ['Tổng sử dụng (VND): ', ($data['totalCost'] * 1000), cstring2::toTienVietNamString3($data['totalCost'] * 1000)];
        $rows[] = ['Tổng đã thanh toán (VND)', round($data['totalRecharge'] / 1.1), cstring2::toTienVietNamString3(round($data['totalRecharge'] / 1.1))];

        $canThanhToan = round($data['totalRecharge'] / 1.1) - $data['totalCost'] * 1000;
        $txt = "Cần thanh toán: ";
        if($canThanhToan > 0)
            $txt = "Số dư: ";
        else
            $canThanhToan = -1 * $canThanhToan;
        $rows[] = [$txt, $canThanhToan, cstring2::toTienVietNamString3($canThanhToan)];
        $rows[] = [];

        // Detailed records header
        $rows[] = ['DETAILED USAGE RECORDS'];
        $rows[] = [
            'STT',
            'Instance ID',
            'VPS Name',
            'IP Addresses',
            'Billing Start',
            'Billing End',
            'Duration',
            'CPU',
            'RAM (GB)',
            'Disk (GB)',
            'IPs',
            'Monthly Price',
            'Monthly Price (Old Config)',
            'Power State',
            'Total Fee (VND)'
        ];

        // Usage records and track monthly price sum
        $monthlyPriceSum = 0;

        if($data['results'] )
        foreach ($data['results'] as $key => $row) {
            $monthlyPrice = ($row['power_state']) === 'OLD_CONFIG' ? 0 : ($row['price_month'] * 1000 ?: $row['price_config'] * 1000);
            // $monthlyPrice = ($row['price_month'] * 1000 ?: $row['price_config'] * 1000);
            $monthlyPriceSum += $monthlyPrice;
            $rows[] = [
                $key + 1,
                $row['instance_id'],
                $row['instance_name'],
                $row['list_ip_address'],
                $row['last_billing_start_at'] ?: $row['created_at'],
                $row['timestamp'],
                $row['time_usage'],
                $row['cpu'],
                $row['ram_gb'],
                $row['disk_gb'],
                $row['ip_count'],
                $monthlyPrice,
                ($row['power_state']) === 'OLD_CONFIG' ? ($row['price_month'] * 1000 ?: $row['price_config'] * 1000) : '',
                $row['power_state'],
                $row['calculated_fee'] * 1000
            ];
        }

        // Subtotals row for Monthly Price and Total Fee columns
        $rows[] = ['', '', '', '', '', '', '', '', '', '', 'Monthly:', $monthlyPriceSum, '', 'Total Fees', ($data['totalCost'] * 1000)];

        return $rows;
    }

    /**
     * Create spreadsheet object with all data and formatting
     * Shared between download and email sending
     */
    private function createSpreadsheet($data, $fromTime = null, $toTime = null)
    {
        $totalRecharge = $data['totalRecharge'] ?? 0;
        $totalCost = $data['totalCost'] ?? 0;
        $totalRechargeNoVat = $totalRecharge / 1.1;

        $needPay = $totalCost * 1000 - $totalRechargeNoVat;
        $needPayDot = number_formatvn0($needPay);
        $needPayStrVn = cstring2::toTienVietNamString3($needPay);
        $needPayVAT = $needPay / 10;

        $toTimeVN = nowy_vn();
        if($toTime)
            $toTimeVN = nowy_vn(strtotime($toTime));


        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $rows = $this->generateSpreadsheetData($data);

        // Add rows to spreadsheet using setCellValue with letter-based columns
        foreach ($rows as $rowIndex => $row) {
            $rowNum = $rowIndex + 1;
            foreach ($row as $colIndex => $value) {
                // Convert column index to letter: 0=A, 1=B, ..., 25=Z, 26=AA, etc.
                $colLetter = $this->getColumnLetterFromIndex($colIndex);
                $sheet->setCellValue($colLetter . $rowNum, $value);
            }
        }

        // Apply green styling to first 3 header rows (rows 1-3 in Excel)
        for ($headerRow = 1; $headerRow <= 3; $headerRow++) {
            for ($col = 0; $col < 15; $col++) {
                $colLetter = $this->getColumnLetterFromIndex($col);
                $cellStyle = $sheet->getStyle($colLetter . $headerRow);
                $cellStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $cellStyle->getFill()->getStartColor()->setARGB('FFD4EDDA'); // Light green background
                $cellStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF155724')); // Dark green text
                $cellStyle->getFont()->setBold(true);
            }
        }

        // Set column widths
        $columnWidths = $this->getColumnWidths();
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        // Footer rows with auto-increment
        $linkRow = $rowIndex + 2;
        $style = $sheet->getStyle('A' . $linkRow);
        $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $style->getFill()->getStartColor()->setARGB('FFD4EDDA'); // Light green background
        $sheet->setCellValue('A' . $linkRow, "Chi phí: ");
        $sheet->setCellValue('B' . $linkRow, $needPay);

//        $sheet->mergeCells('A' . $linkRow . ':G' . $linkRow);
        $linkRow++;
        $style = $sheet->getStyle('A' . $linkRow);
        $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $style->getFill()->getStartColor()->setARGB('FFD4EDDA'); // Light green background
        $sheet->setCellValue('A' . $linkRow, "Thuế VAT:  ");
        $sheet->setCellValue('B' . $linkRow, ($needPayVAT));
//        $sheet->mergeCells('A' . $linkRow . ':G' . $linkRow);
        $linkRow++;

        $style = $sheet->getStyle('A' . $linkRow);
        $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $style->getFill()->getStartColor()->setARGB('FFD4EDDA'); // Light green background
        $sheet->setCellValue('A' . $linkRow, "Cần thanh toán:  ");
        $sheet->setCellValue('B' . $linkRow, ($needPayVAT + $needPay));
        $sheet->setCellValue('C' . $linkRow, "(" . cstring2::toTienVietNamString3($needPay * 1.1) . ")");
//        $sheet->mergeCells('A' . $linkRow . ':G' . $linkRow);
        $linkRow++;
        $linkRow++;

        $sheet->setCellValue('A' . $linkRow, "Tài khoản thanh toán:  110389898, Công ty TNHH Công nghệ số Galaxy Việt nam.");
        $sheet->mergeCells('A' . $linkRow . ':G' . $linkRow);
        $linkRow++;

        $sheet->setCellValue('A' . $linkRow, "Tại: Ngân hàng Thương mại Cổ phần Á Châu (ACB)");
        $sheet->mergeCells('A' . $linkRow . ':G' . $linkRow);
        $linkRow += 2;  // Skip 2 blank rows


        $sheet->setCellValue('A' . $linkRow, "Xin cảm ơn Quý khách đã sử dụng dịch vụ. Mọi thắc mắc vui lòng liên hệ bộ phận hỗ trợ.");
        $sheet->mergeCells('A' . $linkRow . ':G' . $linkRow);
        $linkRow += 2;  // Skip 2 blank rows


        //Thêm 1 hàng lấy link url hiện tại, laravel, và cho phép click vào link đó trong excel để mở ra:
        $domain = request()->getHost();
        $currentUrl = "https://$domain/member/vps-billing/report?to_time=".$toTime;

        $sheet->setCellValue('A' . $linkRow, 'Thông tin chi tiết : ' . $currentUrl);
        // Merge cells A to G (7 columns)
        $sheet->mergeCells('A' . $linkRow . ':G' . $linkRow);
        // Set hyperlink
        $sheet->getCell('A' . $linkRow)->getHyperlink()->setUrl($currentUrl);


        // Add green background color and formatting
        $style = $sheet->getStyle('A' . $linkRow);
        $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $style->getFill()->getStartColor()->setARGB('FFD4EDDA'); // Light green background
        $style->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF155724')); // Dark green text
        $style->getFont()->setBold(true);
        $style->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        return $spreadsheet;
    }

    /**
     * Generate Excel using PhpOffice\PhpSpreadsheet\IOFactory
     */
    private function generateExcelWithPhpOffice($data, $fileName, $fromTime = null, $toTime = null)
    {
        $spreadsheet = $this->createSpreadsheet($data, $fromTime, $toTime);

        // Create writer and save to stream
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        return response()->streamDownload(
            function() use ($writer) {
                $writer->save('php://output');
            },
            $fileName,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    /**
     * Define column widths for Excel spreadsheet
     * Columns: STT, Instance ID, VPS Name, IP Addresses, Billing Start, Billing End, Duration, CPU, RAM, Disk, IPs, Monthly Price, Old Config Price, Power State, Total Fee
     */
    private function getColumnWidths()
    {
        return [
            'A' => 18,      // STT
            'B' => 12,     // Instance ID
            'C' => 18,     // VPS Name
            'D' => 15,     // IP Addresses
            'E' => 15,     // Billing Start
            'F' => 15,     // Billing End
            'G' => 12,     // Duration
            'H' => 5,      // CPU
            'I' => 10,     // RAM (GB)
            'J' => 10,     // Disk (GB)
            'K' => 6,      // IPs
            'L' => 14,     // Monthly Price
            'M' => 15,     // Old Config Price
            'N' => 12,     // Power State
            'O' => 15,     // Total Fee (VND)
        ];
    }

    /**
     * Convert column index (0, 1, 2, ...) to Excel column letter (A, B, C, ..., Z, AA, AB, etc.)
     */
    private function getColumnLetterFromIndex($index)
    {
        $letter = '';
        while ($index >= 0) {
            $letter = chr(65 + ($index % 26)) . $letter;
            $index = intdiv($index, 26) - 1;
        }
        return $letter;
    }

    /**
     * Generate CSV export
     * Uses generateSpreadsheetData() to ensure consistency with Excel output
     * Modify generateSpreadsheetData() only - changes will apply to both CSV and Excel
     */
    private function generateCsvExport($data, $fileName,$fromTime = null, $toTime = null)
    {
        if(request('dump')){
            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            print_r($data);
            echo "</pre>";
            die();
        }

        $fileName = str_replace('.xlsx', '.csv', $fileName);

        $response = response()->streamDownload(
            function() use ($data) {
                // Output UTF-8 BOM for proper Unicode support in Excel
                echo "\xEF\xBB\xBF";

                $output = fopen('php://output', 'w');

                // Set CSV delimiter to use for proper Excel import
                ini_set('auto_detect_line_endings', FALSE);

                // Get rows from shared data generator
                $rows = $this->generateSpreadsheetData($data);

                // Write all rows as CSV
                foreach ($rows as $row) {
                    fputcsv($output, $row, ',');
                }

                fclose($output);
            },
            $fileName,
            [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => "attachment; filename=\"$fileName\"",
            ]
        );

        return $response;
    }

    /**
     * Send billing report via email
     *
     * @param User $user Target user
     * @param array $options Additional options (date_from, date_to)
     * @param string $title Email subject - custom or auto-generated
     * @param string $content Email body content - custom or auto-generated
     * @param string|null $fromTime From timestamp
     * @param string|null $toTime To timestamp
     * @return bool Success status
     */
    public function sendEmail(User $user, array $options = [], $title = null, $content = null, $fromTime = null, $toTime = null): bool
    {
        try {
            $data = $this->generateReportData($user, $options, $fromTime, $toTime);

            $partner_name  = 'Quý khách';
            if($prof = PartnerInfo::where("user_id", $user->id)->first()){
                if($prof->partner_name ){
                    $partner_name = $prof->partner_name;
                }
            }

//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($data);
//            echo "</pre>";
            // Generate spreadsheet
            $spreadsheet = $this->createSpreadsheet($data, $fromTime, $toTime);

//            die("xxx1 - " . __LINE__);
            if(!$user = getCurrentUserId(1)){
                return false;
            }

            // Save to temp file
            $tempFile = tempnam(sys_get_temp_dir(), "vps_billing-$user->id" . date("Y-m-d"));
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($tempFile);

            $from = "sale@glx.com.vn";
            $fromName = "Galaxy Cloud";

            // Use provided title or generate default
            if (!$title)
                $title = "GalaxyCloud - Đối soát # " . nowy_vn();

            $to = $user->email;

            // Use provided content or generate default
            if (!$content) {
//                $domain = getDomainHostName();
//                $link = "https://$domain/member/vps-billing/report";
                if($fromTime)
//                    $link .= "?from_time=$fromTime";
                $content = "Kính chào $partner_name!
Xin cảm ơn Quý khách hàng!
---------------
http://glx.com.vn";
            }

            $debug = false;
            if($options['debug'] ?? 0)
                $debug = true;
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPDebug = $debug;

            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 465;
            $mail->CharSet = 'UTF-8';

            if (ClassMail1::$nAccForce >= 0 && ClassMail1::$nAccForce < count(ClassMail1::$mAcc)) {
                $mail->Username = ClassMail1::$mAcc[ClassMail1::$nAccForce][0];
                $mail->Password = dfh1b(ClassMail1::$mAcc[ClassMail1::$nAccForce][1]);
            } else {
                $rand = rand(0, count(ClassMail1::$mAcc) - 1);
                $mail->Username = ClassMail1::$mAcc[$rand][0];
                $mail->Password = dfh1b(ClassMail1::$mAcc[$rand][1]);
            }

            $mail->From = "$from";
            $mail->FromName = "$fromName";
            $mail->AddReplyTo("$from", "$fromName");
            $mail->IsHTML(false);
            $mail->Subject = "$title";
            $mail->Body = "$content";
            $mail->AddAddress($to);
            $mail->AddCC("sale@glx.com.vn");
            $mail->AddCC("dungla2011@gmail.com");

            // Attach Excel file
            if (file_exists($tempFile))
                $mail->AddAttachment($tempFile, "DoiSoatDichVu-VPS-$user->id-". nowy() .'.xlsx');

            if ($mail->Send()) {
                @unlink($tempFile);
                return true;
            } else {
                @unlink($tempFile);
                return false;
            }
        } catch (\Exception $e) {
            echo "<br>Error: " . $e->getMessage();
            \Log::error('Failed to send billing report email: ' . $e->getMessage());
            return false;
        }
    }


    public function sendEmailPdfOk(User $user, array $options = []): bool
    {
        try {
            $pdf = $this->generatePdf($user, $options);
            $fileName = sprintf(
                'vps-billing-%s-%s.pdf',
                $user->id,
                now()->format('Y-m-d')
            );

            // Send email with PDF attachment
            \Mail::to($user->email)->send(
                new \App\Mail\VpsBillingReport($user, $pdf->output(), $fileName)
            );

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send billing report email: ' . $e->getMessage());
            return false;
        }
    }
    /**
     * Extract total monthly price from price_config with quantities
     */
    private function extractPriceFromConfig($priceConfig, $cpu = 0, $ram = 0, $disk = 0, $ip = 0)
    {
        if (!$priceConfig || !is_array($priceConfig)) {
            return 0;
        }

        $total = 0;
        $total += ($priceConfig['n_cpu_core_price'] ?? 0) * $cpu;
        $total += ($priceConfig['n_ram_gb_price'] ?? 0) * $ram;
        $total += ($priceConfig['n_gb_disk_price'] ?? 0) * $disk;
        $total += ($priceConfig['n_ip_address_price'] ?? 0) * $ip;

        return $total;
    }

}
