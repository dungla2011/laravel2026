<?php

namespace App\Services;

/**
 * Calculate VPS usage fees based on pricing config and resource usage
 *
 * Pricing is stored as JSON in vps_usages.price_config
 * Format: {
 *   "n_cpu_core_price": 50,           // VND per CPU core per day
 *   "n_ram_gb_price": 30,             // VND per GB RAM per day
 *   "n_gb_disk_price": 1,             // VND per GB disk per day
 *   "n_ip_address_price": 50,         // VND per IP address per day
 *   "n_network_dedicated_mbit_price": 1000  // VND per Mbps per day
 * }
 */
class VpsUsageFeeService
{
    /**
     * Count chargeable IPs (excluding local IPs and 1 free internet IP)
     * - Local IPs: 192.168.*, 10.*, 172.16-31.* (FREE)
     * - First Internet IP: FREE
     * - Additional Internet IPs: CHARGEABLE
     *
     * @param string $ipListString Comma-separated IP addresses
     * @return int Number of chargeable IPs
     */
    public static function countChargeableIPs($ipListString)
    {
        if (empty($ipListString)) {
            return 0;
        }

        // Split IPs by comma
        $ips = array_filter(array_map('trim', explode(',', $ipListString)));
        
        $internetIpCount = 0;
        
        foreach ($ips as $ip) {
            // Check if IP is local/private
            if (self::isLocalIP($ip)) {
                continue; // Skip local IPs - they are free
            }
            
            // Count internet IPs
            $internetIpCount++;
        }
        
        // First internet IP is free, charge for the rest
        return max(0, $internetIpCount - 1);
    }

    /**
     * Check if IP is a local/private IP address
     *
     * @param string $ip IP address
     * @return bool True if IP is local/private
     */
    private static function isLocalIP($ip)
    {
        // 192.168.0.0/16
        if (preg_match('/^192\.168\./', $ip)) {
            return true;
        }
        
        // 10.0.0.0/8
        if (preg_match('/^10\./', $ip)) {
            return true;
        }
        
        // 172.16.0.0/12
        if (preg_match('/^172\.(1[6-9]|2[0-9]|3[01])\./', $ip)) {
            return true;
        }
        
        // 127.0.0.0/8 (localhost)
        if (preg_match('/^127\./', $ip)) {
            return true;
        }
        
        // 169.254.0.0/16 (link-local)
        if (preg_match('/^169\.254\./', $ip)) {
            return true;
        }
        
        return false;
    }

    /**
     * Calculate daily fee (per 1440 minutes / 24 hours)
     *
     * @param array $priceConfig Pricing configuration array (prices for 30 days)
     * @param int $cpu CPU cores count
     * @param int $ramGb RAM in GB
     * @param int $diskGb Disk in GB
     * @param int $ipCount Number of IP addresses (default: 1)
     * @param int $networkMbit Dedicated network Mbps (default: 0)
     * @return float Daily fee in thousands VND
     */
    public static function calculateDailyFee($priceConfig, $cpu, $ramGb, $diskGb, $ipCount = 1, $networkMbit = 0)
    {
        if (is_string($priceConfig)) {
            $priceConfig = json_decode($priceConfig, true);
        }

        if (!is_array($priceConfig)) {
            return 0;
        }

        $fee = 0;

        // Prices are for 30 days, so divide by 30 to get daily price
        // CPU cost per day
        $fee += (($priceConfig['n_cpu_core_price'] ?? 0) / 30) * $cpu;

        // RAM cost per day
        $fee += (($priceConfig['n_ram_gb_price'] ?? 0) / 30) * $ramGb;

        // Disk cost per day
        $fee += (($priceConfig['n_gb_disk_price'] ?? 0) / 30) * $diskGb;

        // IP address cost per day
        $fee += (($priceConfig['n_ip_address_price'] ?? 0) / 30) * $ipCount;

        // Dedicated network cost per day
        $fee += (($priceConfig['n_network_dedicated_mbit_price'] ?? 0) / 30) * $networkMbit;

        return round($fee, 4);
    }

    /**
     * Calculate fee for specific duration (in minutes)
     *
     * @param array $priceConfig Pricing configuration array
     * @param int $cpu CPU cores count
     * @param int $ramGb RAM in GB
     * @param int $diskGb Disk in GB
     * @param int $durationMinutes Duration in minutes
     * @param int $ipCount Number of IP addresses (default: 1)
     * @param int $networkMbit Dedicated network Mbps (default: 0)
     * @return float Fee for given duration in thousands VND
     */
    public static function calculateFee($priceConfig, $cpu, $ramGb, $diskGb, $durationMinutes = 0, $ipCount = 0, $networkMbit = 0)
    {
        if ($durationMinutes <= 0) {
            return 0;
        }

        // Calculate daily fee first
        $dailyFee = self::calculateDailyFee($priceConfig, $cpu, $ramGb, $diskGb, $ipCount, $networkMbit);

        // Convert to per-minute fee and multiply by duration
        // 1440 minutes = 24 hours = 1 day
        $fee = $dailyFee * ($durationMinutes / 1440);

        // Debug logging
        // echo ('\VPS Fee Calculation', [
        //     'cpu' => $cpu,
        //     'ramGb' => $ramGb,
        //     'diskGb' => $diskGb,
        //     'durationMinutes' => $durationMinutes,
        //     'dailyFee' => $dailyFee,
        //     'finalFee' => round($fee, 4),
        // ]);


        echo ("\nVPS fee cal: CPU = $cpu cores, RAM = $ramGb GB, Disk = $diskGb GB, Duration = $durationMinutes minutes => Fee = " . round($fee, 4) . " K VND");

        return round($fee, 4);
    }

    /**
     * Calculate fee from database record
     *
     * @param object|array $record vps_usages table record
     * @return float Calculated fee in thousands VND
     */
    public static function calculateFeeFromRecord($record)
    {
        $priceConfig = is_array($record) ? $record['price_config'] : $record->price_config;
        $cpu = is_array($record) ? $record['cpu'] : $record->cpu;
        $ramGb = is_array($record) ? $record['ram_gb'] : $record->ram_gb;
        $diskGb = is_array($record) ? $record['disk_gb'] : $record->disk_gb;

        // Calculate duration from created_at and lastest_time_the_same
        if (is_array($record)) {
            $createdAt = strtotime($record['created_at']);
            $lastestTime = strtotime($record['lastest_time_the_same'] ?? $record['created_at']);
        } else {
            $createdAt = strtotime($record->created_at);
            $lastestTime = strtotime($record->lastest_time_the_same ?? $record->created_at);
        }

        $durationMinutes = max(0, floor(($lastestTime - $createdAt) / 60));
        $ipCount = 0; // Default to 0 IP

        return self::calculateFee($priceConfig, $cpu, $ramGb, $diskGb, $durationMinutes, $ipCount);
    }

    /**
     * Get price per component for display
     *
     * @param array $priceConfig Pricing configuration
     * @param int $cpu CPU cores
     * @param int $ramGb RAM GB
     * @param int $diskGb Disk GB
     * @return array Breakdown of daily costs
     */
    public static function getPriceBreakdown($priceConfig, $cpu, $ramGb, $diskGb, $ipCount = 1, $networkMbit = 0)
    {
        if (is_string($priceConfig)) {
            $priceConfig = json_decode($priceConfig, true);
        }

        return [
            'cpu' => ($priceConfig['n_cpu_core_price'] ?? 0) * $cpu,
            'ram' => ($priceConfig['n_ram_gb_price'] ?? 0) * $ramGb,
            'disk' => ($priceConfig['n_gb_disk_price'] ?? 0) * $diskGb,
            'ip' => ($priceConfig['n_ip_address_price'] ?? 0) * $ipCount,
            'network' => ($priceConfig['n_network_dedicated_mbit_price'] ?? 0) * $networkMbit,
        ];
    }
}
