<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class VpsPricingService
{
    /**
     * Extract pricing config from config/vps_config.php
     * 
     * @return array
     */
    public static function getPricingConfig(): array
    {
        $specs = Config::get('vps_config.specs', []);
        
        return [
            'n_cpu_core_price' => $specs['n_cpu_core']['price'] ?? 0,
            'n_ram_gb_price' => $specs['n_ram_gb']['price'] ?? 0,
            'n_gb_disk_price' => $specs['n_gb_disk']['price'] ?? 0,
            'n_ip_address_price' => $specs['n_ip_address']['price'] ?? 0,
            'n_network_dedicated_mbit_price' => $specs['n_network_dedicated_mbit']['price'] ?? 0,
        ];
    }

    /**
     * Get pricing config as JSON string for storage
     * 
     * @return string JSON
     */
    public static function getPricingConfigJson(): string
    {
        return json_encode(self::getPricingConfig());
    }

    /**
     * Check if pricing has changed by comparing current config with stored config
     * 
     * @param array|string $storedConfig
     * @return bool
     */
    public static function hasPricingChanged($storedConfig): bool
    {
        if (is_string($storedConfig)) {
            $storedConfig = json_decode($storedConfig, true);
        }

        $currentConfig = self::getPricingConfig();

        // Compare each pricing component
        return $storedConfig['n_cpu_core_price'] !== $currentConfig['n_cpu_core_price']
            || $storedConfig['n_ram_gb_price'] !== $currentConfig['n_ram_gb_price']
            || $storedConfig['n_gb_disk_price'] !== $currentConfig['n_gb_disk_price']
            || $storedConfig['n_ip_address_price'] !== $currentConfig['n_ip_address_price']
            || $storedConfig['n_network_dedicated_mbit_price'] !== $currentConfig['n_network_dedicated_mbit_price'];
    }
}
