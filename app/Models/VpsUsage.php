<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;

class VpsUsage extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra, UnixTimeId;
    protected $guarded = [];

    /**
     * Relationship: VpsUsage belongs to VpsInstance
     */
    public function instance()
    {
        return $this->belongsTo(VpsInstance::class, 'instance_id', 'id');
    }

    /**
     * Calculate fee with formula breakdown
     *
     * @param string|null $fromTime Thời gian bắt đầu tính phí (nếu có)
     * @param string|null $toTime Thời gian kết thúc tính phí (nếu có)
     * @return array ['text' => string, 'fee' => float, 'details' => array]
     */
    public function calculateFee($fromTime = null, $toTime = null)
    {
        // Check if using fixed monthly price
        if ($this->price_month && $this->price_month > 0) {
            return $this->calculateFeeFixedMonthly($fromTime, $toTime);
        }

        // Otherwise use config-based calculation
        return $this->calculateFeeConfigBased($fromTime, $toTime);
    }

     private function calculateFeeFixedMonthly_Old($fromTime = null, $toTime = null)
    {
        $pricePerMonth = floatval($this->price_month);

        // Use custom fromTime if provided, otherwise use last_billing_start_at or created_at
        $startTime = $fromTime ?: ($this->last_billing_start_at ?: $this->created_at);
        $createdTime = new \DateTime($startTime);

        // Use custom toTime if provided, otherwise use timestamp_minute
        $endTime = $toTime ?: $this->timestamp_minute;

        //Nếu ko còn dùng nữa, là đã hết hạn, thì $endTime tính đến lúc dùng thôi
//        if($this->end_time_used)
        if($this->power_state == 'OLD_CONFIG')
        {
            $endTime = $this->timestamp_minute;
        }

        $timestampTime = new \DateTime($endTime);
        $interval = $createdTime->diff($timestampTime);
        $durationMinutes = ($interval->days * 1440) + ($interval->h * 60) + $interval->i;

        $fee = $pricePerMonth * ($durationMinutes / 43200);
        $fee = round($fee, 0);

        // Convert duration to text
        $days = floor($durationMinutes / 1440);
        $remainingMinutes = $durationMinutes % 1440;
        $hours = floor($remainingMinutes / 60);
        $minutes = $remainingMinutes % 60;

        $durationText = '';
        if ($days > 0) $durationText .= $days . ' ngày ';
        if ($hours > 0 || $days > 0) $durationText .= $hours . ' giờ ';
        $durationText .= $minutes . ' phút';

        $text = sprintf(
            "Fixed: %s K/month × %s = %s K",
            number_format($pricePerMonth, 0, ',', '.'),
            $durationText,
            number_format($fee, 0, ',', '.')
        );

        return [
            'text' => $text,
            'fee' => $fee,
            'details' => [
                'type' => 'fixed_monthly',
                'price_month' => $pricePerMonth,
                'duration_minutes' => $durationMinutes,
                'duration_days' => $days,
                'duration_hours' => $hours,
                'duration_mins' => $minutes,
                'period_fee' => $fee,
                'power_state' => $this->power_state
            ]
        ];
    }



    /**
     * Calculate fee using fixed monthly price - calculated by actual months
     * Special logic: if end day = start day, count as full month
     *
     * @param string|null $fromTime Thời gian bắt đầu tính phí (nếu có)
     * @param string|null $toTime Thời gian kết thúc tính phí (nếu có)
     */
    private function calculateFeeFixedMonthly($fromTime = null, $toTime = null)
    {
        $pricePerMonth = floatval($this->price_month);

        // Use custom fromTime if provided, otherwise use last_billing_start_at or created_at
        $startTime = $fromTime ?: ($this->last_billing_start_at ?: $this->created_at);

        // Ensure fromTime has time component, default to 00:00:00
        if (!preg_match('/\d{2}:\d{2}:\d{2}/', $startTime)) {
            $startTime .= ' 00:00:00';
        }

        $startDate = new \DateTime($startTime);

        // Use custom toTime if provided, otherwise use timestamp_minute
        $endTime = $toTime ?: $this->timestamp_minute;

        // Ensure toTime has time component, default to 00:00:00
        if (!preg_match('/\d{2}:\d{2}:\d{2}/', $endTime)) {
            $endTime .= ' 00:00:00';
        }

        //Nếu ko còn dùng nữa, là đã hết hạn, thì $endTime tính đến lúc dùng thôi
        if($this->power_state == 'OLD_CONFIG')
        {
            $endTime = $this->timestamp_minute;
        }

        $endDate = new \DateTime($endTime);

        // Calculate fee by counting full months using anniversary date logic
        $monthCount = 0;
        $partialMonthFee = 0;
        $monthBreakdown = [];

        // Start date anniversary (day of month we started on)
        $startDay = (int)$startDate->format('d');

        // Simple logic: count how many times we can add 1 month and still be <= endDate
        $anniversaryDate = clone $startDate;
        $firstAnniversary = clone $startDate;
        $firstAnniversary->add(new \DateInterval('P1M'));

        // Count full months by anniversary dates
        while ($firstAnniversary <= $endDate) {
            $monthCount++;
            $monthBreakdown[] = sprintf("%s (full month)", $anniversaryDate->format('M Y'));
            $anniversaryDate = clone $firstAnniversary;
            $firstAnniversary->add(new \DateInterval('P1M'));
        }

        // If endDate is before the next anniversary, calculate pro-rata for remaining days
        if ($anniversaryDate < $endDate) {
            // There are remaining days after the last full month
            $daysInMonth = (int)$endDate->format('t');
            $daysUsed = (int)$endDate->format('d');
            $partialMonthFee = $pricePerMonth * ($daysUsed / $daysInMonth);

            $monthBreakdown[] = sprintf(
                "%s (%.0f/%d days)",
                $endDate->format('M Y'),
                $daysUsed,
                $daysInMonth
            );
        }

        // Total fee: full months + partial month pro-rata
        $fee = ($monthCount * $pricePerMonth) + $partialMonthFee;
        $fee = round($fee, 0);

        // Format text
        $breakdownText = implode(' + ', $monthBreakdown);
        $text = sprintf(
            "Fixed: %s K/month × [%s] = %s K",
            number_format($pricePerMonth, 0, ',', '.'),
            $breakdownText,
            number_format($fee, 0, ',', '.')
        );

        // Duration text for reference
        $interval = $startDate->diff($endDate);
        $days = $interval->days;
        $remainingMinutes = ($interval->h * 60) + $interval->i;
        $hours = floor($remainingMinutes / 60);
        $minutes = $remainingMinutes % 60;

        $durationText = '';
        if ($days > 0) $durationText .= $days . ' ngày ';
        if ($hours > 0 || $days > 0) $durationText .= $hours . ' giờ ';
        $durationText .= $minutes . ' phút';

        return [
            'text' => $text,
            'fee' => $fee,
            'details' => [
                'type' => 'fixed_monthly',
                'price_month' => $pricePerMonth,
                'full_months' => $monthCount,
                'partial_month_fee' => round($partialMonthFee, 0),
                'duration_days' => $days,
                'duration_hours' => $hours,
                'duration_mins' => $minutes,
                'duration_text' => $durationText,
                'period_fee' => $fee,
                'power_state' => $this->power_state,
                'breakdown' => $monthBreakdown
            ]
        ];
    }

    /**
     * Calculate fee using config-based pricing
     *
     * @param string|null $fromTime Thời gian bắt đầu tính phí (nếu có)
     * @param string|null $toTime Thời gian kết thúc tính phí (nếu có)
     */
    private function calculateFeeConfigBased($fromTime = null, $toTime = null)
    {
        // Parse price config
        $priceConfig = json_decode($this->price_config, true);
        if (!$priceConfig) {
            return [
                'text' => 'No price config',
                'fee' => 0,
                'details' => []
            ];
        }

        // Calculate duration in minutes
        // Use custom fromTime if provided, otherwise use last_billing_start_at or created_at
        if ($fromTime) {
            $startTime = strtotime($fromTime);
        } else {
            $startTime = $this->last_billing_start_at ? strtotime($this->last_billing_start_at) : strtotime($this->created_at);
        }

        ////////////////////
        $endTime = $toTime ?: $this->timestamp_minute;
        //Nếu ko còn dùng nữa, là đã hết hạn, thì $endTime tính đến lúc dùng thôi
        if($this->power_state == 'OLD_CONFIG')
        {
            $endTime = ($this->timestamp_minute);
        }
        $endTime = strtotime($endTime);

        $durationMinutes = max(0, floor(($endTime - $startTime) / 60));

        // Determine CPU and RAM for fee calculation (0 if powered off)
        $feeCpu = (strtoupper($this->power_state) === 'POWERED_OFF') ? 0 : $this->cpu;
        $feeRam = (strtoupper($this->power_state) === 'POWERED_OFF') ? 0 : $this->ram_gb;

        // Get prices per 30 days
        $priceCpu = $priceConfig['n_cpu_core_price'] ?? 0;
        $priceRam = $priceConfig['n_ram_gb_price'] ?? 0;
        $priceDisk = $priceConfig['n_gb_disk_price'] ?? 0;
        $priceIp = $priceConfig['n_ip_address_price'] ?? 0;

        // Calculate daily fees
        $dailyCpuFee = ($priceCpu / 30) * $feeCpu;
        $dailyRamFee = ($priceRam / 30) * $feeRam;
        $dailyDiskFee = ($priceDisk / 30) * $this->disk_gb;

        // Count chargeable IPs (excluding local IPs and 1 free internet IP)
        $chargeableIpCount = \App\Services\VpsUsageFeeService::countChargeableIPs($this->list_ip_address);
        $dailyIpFee = ($priceIp / 30) * $chargeableIpCount;

        // Total daily fee
        $dailyTotalFee = $dailyCpuFee + $dailyRamFee + $dailyDiskFee + $dailyIpFee;

        // Calculate period fee
        $periodFee = $dailyTotalFee * ($durationMinutes / 1440);
        $periodFee = round($periodFee, 0); // Round to integer, no decimals

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

        // Build formula text (2 lines, shorter version)
        $text = sprintf(
            "%d×%sK + %d×%sK + %d×%sK + %d×%sK = %s K\n | %s",
            $feeCpu,
            number_format($priceCpu, 0),
            $feeRam,
            number_format($priceRam, 0),
            $this->disk_gb,
            number_format($priceDisk, 0),
            $chargeableIpCount,
            number_format($priceIp, 0),
            number_format($periodFee, 0, ',', '.'),
            $durationText
        );

        return [
            'text' => $text,
            'fee' => $periodFee,
            'details' => [
                'cpu_count' => $feeCpu,
                'cpu_price' => $priceCpu,
                'cpu_daily_fee' => $dailyCpuFee,
                'ram_gb' => $feeRam,
                'ram_price' => $priceRam,
                'ram_daily_fee' => $dailyRamFee,
                'disk_gb' => $this->disk_gb,
                'disk_price' => $priceDisk,
                'disk_daily_fee' => $dailyDiskFee,
                'ip_count' => $chargeableIpCount,
                'ip_price' => $priceIp,
                'ip_daily_fee' => $dailyIpFee,
                'daily_total_fee' => $dailyTotalFee,
                'duration_minutes' => $durationMinutes,
                'period_fee' => $periodFee,
                'power_state' => $this->power_state
            ]
        ];
    }

    /**
     * Test function: Calculate fee for manual testing
     * Usage: VpsUsage::testCalculateMonthly('2026-03-11 00:00:00', '2027-02-11 23:59:59', 1280000);
     */
    public static function testCalculateMonthly($from, $to, $pricePerMonth)
    {
        // Create test instance
        $test = new self();
        $test->price_month = $pricePerMonth;
        $test->last_billing_start_at = $from;
        $test->timestamp_minute = $to;
        $test->power_state = 'POWERED_ON';

        // Calculate
        $result = $test->calculateFeeFixedMonthly($from, $to);

        // Calculate duration
        $startDate = new \DateTime($from);
        $endDate = new \DateTime($to);
        $interval = $startDate->diff($endDate);

        // Get start and end days
        $startDay = (int)$startDate->format('d');
        $endDay = (int)$endDate->format('d');

        echo "<pre>";
        echo "=== TEST CALCULATE MONTHLY ===\n";
        echo "From: $from (day " . $startDay . ")\n";
        echo "To: $to (day " . $endDay . ")\n";
        echo "Duration: " . $interval->days . " days\n";
        echo "Price/month: " . number_format($pricePerMonth, 0, ',', '.') . "\n\n";

        echo "Calculation:\n";
        echo "- Start day: " . $startDay . "\n";
        echo "- End day: " . $endDay . "\n";

        if ($endDay == $startDay) {
            echo "- Logic: End day = Start day → Count as FULL MONTHS\n";
        } elseif ($endDay > $startDay) {
            echo "- Logic: End day > Start day → Full months + " . ($endDay - $startDay) . " extra days\n";
        } else {
            echo "- Logic: End day < Start day → Pro-rata calculation\n";
        }

        echo "\nResult: " . $result['text'] . "\n";
        echo "Total Fee: " . number_format($result['fee'], 0, ',', '.') . " K\n\n";

        echo "Breakdown:\n";
        foreach ($result['details']['breakdown'] as $line) {
            echo "  - $line\n";
        }
        echo "\nFull months: " . $result['details']['full_months'] . "\n";

        echo "Partial fee: " . number_format($result['details']['partial_month_fee'], 0, ',', '.') . " K\n";
        echo "</pre>";

        return $result;
    }
}
