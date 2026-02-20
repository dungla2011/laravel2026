<?php

namespace App\Services;

use App\Models\User;
use App\Models\VpsUsage;
use App\Models\VpsInstance;

class VpsBillingReportService
{
    /**
     * Generate billing report data for a user
     *
     * @param User $user Target user
     * @param array $options Additional options (date_from, date_to, etc.)
     * @return array Report data with results, totals, and summary
     */
    public function generateReportData(User $user, array $options = []): array
    {
        // Number format for VND currency
        $decimal = 0;
        $dec_separator = ',';
        $thousands_separator = '.';

        // Query usages with optional date filtering
        $query = VpsUsage::where('user_id', $user->id)
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
            $feeResult = $usage->calculateFee();
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
            $timestampTime = new \DateTime($usage->timestamp_minute);
            $interval = $createdTime->diff($timestampTime);
            $timeUsage = '';
            if ($interval->days > 0) $timeUsage .= $interval->days . ' ngày ';
            if ($interval->h > 0 || $interval->days > 0) $timeUsage .= $interval->h . ' giờ ';
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
                'usage_id' => $usage->id,
                'instance_id' => $instanceId,
                'instance_name' => $instanceName,
                'list_ip_address' => $usage->list_ip_address,
                'timestamp' => $usage->timestamp_minute ? \Carbon\Carbon::parse($usage->timestamp_minute)->toIso8601String() : null,
                'created_at' => $usage->created_at ? $usage->created_at->toIso8601String() : null,
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

        return [
            'user' => $user,
            'results' => $results,
            'totalCost' => $totalCost,
            'totalLatestCost' => $totalLatestCost,
            'totalPriceMonth' => $totalPriceMonth,
            'totalRecharge' => $totalRecharge,
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
    }

    /**
     * Generate HTML view for billing report
     *
     * @param User $user Target user
     * @param array $options Additional options
     * @return string HTML content
     */
    public function generateHtml(User $user, array $options = []): string
    {
        $data = $this->generateReportData($user, $options);
        return view('vps.billing-report', $data)->render();
    }

    /**
     * Generate PDF for billing report
     *
     * @param User $user Target user
     * @param array $options Additional options
     * @return \Barryvdh\DomPDF\PDF PDF instance
     */
    public function generatePdf(User $user, array $options = [])
    {
        $data = $this->generateReportData($user, $options);

        // Use dompdf to generate PDF
        $pdf = \PDF::loadView('vps.billing-report-pdf', $data);

        // Set paper size and orientation
        $pdf->setPaper('a4', 'landscape');

        return $pdf;
    }

    /**
     * Send billing report via email
     *
     * @param User $user Target user
     * @param array $options Additional options
     * @return bool Success status
     */
    public function sendEmail(User $user, array $options = []): bool
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
