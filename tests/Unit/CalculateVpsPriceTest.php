<?php

namespace Tests\Unit;

use App\Models\Product_Meta;
use Tests\TestCase;

/**
 * Test calculateVpsPrice function with dedicated network and IP pricing
 *
 * Kiểm tra:
 * 1. Làm tròn các giá trị lẻ (disk, network dedicated)
 * 2. Các giá trị ngoài khoảng min/max
 * 3. Giá tính toán có đúng không
 * 4. IP pricing (free quantity support)
 */
class CalculateVpsPriceTest extends TestCase
{
    private $pricing;
    private $pricePerCore;
    private $pricePerGBRam;
    private $pricePerGBDisk;
    private $pricePer100MbitNetwork;
    private $pricePerIP;
    private $diskStep;
    private $networkStep;
    private $freeIPCount;

    protected function setUp(): void
    {
        parent::setUp();

        // Lấy config từ vps_config.php
        $this->pricing = config('vps_config');
        
        // Extract prices từ specs config
        $specs = $this->pricing['specs'];
        
        // Convert K to VND for test comparisons (since calculateVpsPrice returns VND)
        $this->pricePerCore = $specs['n_cpu_core']['price'] * 1000;
        $this->pricePerGBRam = $specs['n_ram_gb']['price'] * 1000;
        $this->pricePerGBDisk = $specs['n_gb_disk']['price'] * 1000;
        $this->pricePer100MbitNetwork = $specs['n_network_dedicated_mbit']['price'] * 1000;
        $this->pricePerIP = $specs['n_ip_address']['price'] * 1000;
        
        // Rounding steps
        $this->diskStep = $specs['n_gb_disk']['rounding'] ?? $specs['n_gb_disk']['step'];
        $this->networkStep = $specs['n_network_dedicated_mbit']['rounding'] ?? $specs['n_network_dedicated_mbit']['step'];
        
        // Free quantities
        $this->freeIPCount = $specs['n_ip_address']['free'] ?? 0;
    }

    /**
     * Test giá trị bình thường
     * CPU: 2, RAM: 4, Disk: 20, Network Shared: 200, Network Dedicated: 0, IP: 1
     */
    public function test_normal_values()
    {
        $price = Product_Meta::calculateVpsPrice(2, 4, 20, 200, 0, 1);

        $expectedPrice = (2 * $this->pricePerCore) +
                         (4 * $this->pricePerGBRam) +
                         (20 * $this->pricePerGBDisk) +
                         0;

        $this->assertEquals($expectedPrice, $price, 'Normal values should calculate correctly');
    }

    /**
     * Test disk lẻ: 88 GB -> 90 GB
     */
    public function test_disk_rounding_88_to_90()
    {
        $price = Product_Meta::calculateVpsPrice(2, 4, 88, 200, 0, 1);

        $expectedPrice = (2 * $this->pricePerCore) +
                         (4 * $this->pricePerGBRam) +
                         (90 * $this->pricePerGBDisk) +
                         0;

        $this->assertEquals($expectedPrice, $price, 'Disk 88 should round up to 90');
    }

    /**
     * Test network dedicated: 150 Mbps -> 200 Mbps
     */
    public function test_network_dedicated_rounding_150_to_200()
    {
        $price = Product_Meta::calculateVpsPrice(2, 4, 20, 200, 150, 1);

        $dedicatedBandwidth = ceil(200 / 100);
        $expectedPrice = (2 * $this->pricePerCore) +
                         (4 * $this->pricePerGBRam) +
                         (20 * $this->pricePerGBDisk) +
                         ($dedicatedBandwidth * $this->pricePer100MbitNetwork);

        $this->assertEquals($expectedPrice, $price, 'Network Dedicated 150 should round up to 200');
    }

    /**
     * Test network dedicated: 500 Mbps
     */
    public function test_network_dedicated_large_500()
    {
        $price = Product_Meta::calculateVpsPrice(2, 4, 20, 200, 500, 1);

        $dedicatedBandwidth = ceil(500 / 100);
        $expectedPrice = (2 * $this->pricePerCore) +
                         (4 * $this->pricePerGBRam) +
                         (20 * $this->pricePerGBDisk) +
                         ($dedicatedBandwidth * $this->pricePer100MbitNetwork);

        $this->assertEquals($expectedPrice, $price, 'Network Dedicated 500 Mbps');
    }

    /**
     * Test network dedicated = 0
     */
    public function test_network_dedicated_zero()
    {
        $price = Product_Meta::calculateVpsPrice(1, 1, 20, 200, 0, 1);

        $expectedPrice = (1 * $this->pricePerCore) +
                         (1 * $this->pricePerGBRam) +
                         (20 * $this->pricePerGBDisk) +
                         0;

        $this->assertEquals($expectedPrice, $price, 'Network Dedicated 0 should be free');
    }

    /**
     * Test CPU và RAM lẻ
     */
    public function test_decimal_cpu_and_ram()
    {
        $price = Product_Meta::calculateVpsPrice(2.5, 4.7, 25, 200, 0, 1);

        $expectedPrice = (2.5 * $this->pricePerCore) +
                         (4.7 * $this->pricePerGBRam) +
                         (30 * $this->pricePerGBDisk) +
                         0;

        $this->assertEquals($expectedPrice, $price, 'CPU and RAM decimal values should not be rounded');
    }

    /**
     * Test giá trị nhỏ hơn min
     */
    public function test_values_below_minimum()
    {
        $price = Product_Meta::calculateVpsPrice(0, 1, 5, 200, 0, 1);

        $expectedPrice = (0 * $this->pricePerCore) +
                         (1 * $this->pricePerGBRam) +
                         (10 * $this->pricePerGBDisk) +
                         0;

        $this->assertEquals($expectedPrice, $price, 'Values below minimum should still calculate');
    }

    /**
     * Test network dedicated > baseline
     */
    public function test_network_dedicated_above_baseline()
    {
        $price = Product_Meta::calculateVpsPrice(2, 4, 20, 200, 300, 1);

        $dedicatedBandwidth = ceil(300 / 100);
        $expectedPrice = (2 * $this->pricePerCore) +
                         (4 * $this->pricePerGBRam) +
                         (20 * $this->pricePerGBDisk) +
                         ($dedicatedBandwidth * $this->pricePer100MbitNetwork);

        $this->assertEquals($expectedPrice, $price, 'Network Dedicated 300 Mbps');
    }

    /**
     * Test user case: CPU=1, RAM=1, Disk=20, Dedicated=200, IP=1
     */
    public function test_user_request_case_with_dedicated()
    {
        $price = Product_Meta::calculateVpsPrice(1, 1, 20, 200, 200, 1);

        $expectedPrice = (1 * $this->pricePerCore) +
                         (1 * $this->pricePerGBRam) +
                         (20 * $this->pricePerGBDisk) +
                         (2 * $this->pricePer100MbitNetwork);

        $this->assertEquals($expectedPrice, $price, 'User case with dedicated network');
    }

    /**
     * Test IP pricing: 1 IP (free)
     */
    public function test_ip_pricing_1_ip_free()
    {
        $price = Product_Meta::calculateVpsPrice(1, 1, 20, 200, 0, 1);

        $expectedPrice = (1 * $this->pricePerCore) +
                         (1 * $this->pricePerGBRam) +
                         (20 * $this->pricePerGBDisk) +
                         0;

        $this->assertEquals($expectedPrice, $price, '1 IP should be free');
    }

    /**
     * Test IP pricing: 2 IPs (1 free + 1 paid)
     */
    public function test_ip_pricing_2_ips()
    {
        $price = Product_Meta::calculateVpsPrice(1, 1, 20, 200, 0, 2);

        $chargedIPs = 2 - $this->freeIPCount;
        $expectedPrice = (1 * $this->pricePerCore) +
                         (1 * $this->pricePerGBRam) +
                         (20 * $this->pricePerGBDisk) +
                         ($chargedIPs * $this->pricePerIP);

        $this->assertEquals($expectedPrice, $price, '2 IPs should charge for 1 extra IP');
    }

    /**
     * Test IP pricing: 5 IPs (1 free + 4 paid)
     */
    public function test_ip_pricing_5_ips()
    {
        $price = Product_Meta::calculateVpsPrice(1, 1, 20, 200, 0, 5);

        $chargedIPs = 5 - $this->freeIPCount;
        $expectedPrice = (1 * $this->pricePerCore) +
                         (1 * $this->pricePerGBRam) +
                         (20 * $this->pricePerGBDisk) +
                         ($chargedIPs * $this->pricePerIP);

        $this->assertEquals($expectedPrice, $price, '5 IPs should charge for 4 extra IPs');
    }

    /**
     * Test config loaded correctly
     */
    public function test_config_loaded_correctly()
    {
        $this->assertNotNull($this->pricing);
        $this->assertArrayHasKey('specs', $this->pricing);
        $this->assertArrayHasKey('defaults', $this->pricing);
        $this->assertGreaterThan(0, $this->pricePerIP, 'IP price should be configured');
        $this->assertGreaterThanOrEqual(0, $this->freeIPCount, 'Free IP count should be configured');
    }
}
