<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Test VPS Pricing API with IP pricing
 * Endpoint: https://glx.lad.vn/_site/hosting_site/price-vps.php
 */
class VpsApiTest extends TestCase
{
    private $apiUrl = 'https://glx.lad.vn/_site/hosting_site/price-vps.php';

    /**
     * Call API via curl
     */
    private function callApi($params)
    {
        $url = $this->apiUrl . '?' . http_build_query($params);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $this->fail("API returned HTTP $httpCode. Response: $response");
        }

        return json_decode($response, true);
    }

    /**
     * Test API: Disk rounding 88 -> 90
     */
    public function test_api_disk_rounding_88_to_90()
    {
        $params = [
            'n_cpu_core' => 1,
            'n_ram_gb' => 1,
            'n_gb_disk' => 88,
            'n_network_mbit' => 200,
            'n_network_dedicated_mbit' => 0,
            'n_ip_address' => 1,
        ];

        $data = $this->callApi($params);

        $this->assertTrue($data['success']);
        $config = $data['data']['configuration'];
        $this->assertEquals(90, $config['n_gb_disk'], 'Disk 88 should round to 90');
        $this->assertEquals(1, $config['n_ip_address']);
    }

    /**
     * Test API: User request with dedicated network and IP pricing
     */
    public function test_api_user_request_case_with_dedicated()
    {
        $params = [
            'n_cpu_core' => 1,
            'n_ram_gb' => 1,
            'n_gb_disk' => 20,
            'n_network_mbit' => 200,
            'n_network_dedicated_mbit' => 200,
            'n_ip_address' => 1,
        ];

        $data = $this->callApi($params);

        $this->assertTrue($data['success'], 'API call should succeed');

        $config = $data['data']['configuration'];
        $this->assertEquals(1, $config['n_cpu_core']);
        $this->assertEquals(1, $config['n_ram_gb']);
        $this->assertEquals(20, $config['n_gb_disk']);
        $this->assertEquals(200, $config['n_network_mbit']);
        $this->assertEquals(200, $config['n_network_dedicated_mbit']);
        $this->assertEquals(1, $config['n_ip_address']);

        $this->assertArrayHasKey('breakdown', $data['data']);
        $this->assertArrayHasKey('network_dedicated', $data['data']['breakdown']);
        $this->assertArrayHasKey('ip_address', $data['data']['breakdown']);
        $this->assertArrayHasKey('total_price', $data['data']);

        $totalPrice = $data['data']['total_price'] ?? 0;
        // CPU: 50K, RAM: 30K, Disk: 20K, Network: 2000K, IP: 0 = 2100K = 2.100.000đ
        $expectedMinPrice = 2000000;
        $this->assertGreaterThanOrEqual($expectedMinPrice, $totalPrice, 'Total price should be >= 2.100.000đ');
    }

    /**
     * Test API: Network dedicated rounding 150 -> 200
     */
    public function test_api_network_dedicated_rounding()
    {
        $params = [
            'n_cpu_core' => 1,
            'n_ram_gb' => 1,
            'n_gb_disk' => 20,
            'n_network_mbit' => 200,
            'n_network_dedicated_mbit' => 150,
            'n_ip_address' => 1,
        ];

        $data = $this->callApi($params);

        $this->assertTrue($data['success']);
        $config = $data['data']['configuration'];
        $this->assertEquals(200, $config['n_network_dedicated_mbit'], 'Network Dedicated 150 should round to 200');
    }

    /**
     * Test API: Network dedicated = 0
     */
    public function test_api_network_dedicated_zero()
    {
        $params = [
            'n_cpu_core' => 1,
            'n_ram_gb' => 1,
            'n_gb_disk' => 20,
            'n_network_mbit' => 200,
            'n_network_dedicated_mbit' => 0,
            'n_ip_address' => 1,
        ];

        $data = $this->callApi($params);

        $this->assertTrue($data['success']);
        $breakdown = $data['data']['breakdown'];
        $this->assertEquals(0, $breakdown['network_dedicated']['total'], 'Network Dedicated 0 should be free');
    }

    /**
     * Test API: IP pricing - 1 IP (free)
     */
    public function test_api_ip_pricing_1_ip_free()
    {
        $params = [
            'n_cpu_core' => 1,
            'n_ram_gb' => 1,
            'n_gb_disk' => 20,
            'n_network_mbit' => 200,
            'n_network_dedicated_mbit' => 0,
            'n_ip_address' => 1,
        ];

        $data = $this->callApi($params);

        $this->assertTrue($data['success']);
        $breakdown = $data['data']['breakdown'];
        $this->assertEquals(0, $breakdown['ip_address']['total'], '1 IP should be free');
    }

    /**
     * Test API: IP pricing - 2 IPs (1 free + 1 paid @ 50K)
     */
    public function test_api_ip_pricing_2_ips()
    {
        $params = [
            'n_cpu_core' => 1,
            'n_ram_gb' => 1,
            'n_gb_disk' => 20,
            'n_network_mbit' => 200,
            'n_network_dedicated_mbit' => 0,
            'n_ip_address' => 2,
        ];

        $data = $this->callApi($params);

        $this->assertTrue($data['success']);
        $config = $data['data']['configuration'];
        $this->assertEquals(2, $config['n_ip_address']);
        
        $breakdown = $data['data']['breakdown'];
        $ipCost = $breakdown['ip_address']['total'];
        $expectedIPCost = 50000;  // 1 extra IP @ 50K
        $this->assertEquals($expectedIPCost, $ipCost, '2 IPs should charge 50K for 1 extra IP');
    }

    /**
     * Test API: IP pricing - 5 IPs (1 free + 4 paid @ 50K each = 200K)
     */
    public function test_api_ip_pricing_5_ips()
    {
        $params = [
            'n_cpu_core' => 1,
            'n_ram_gb' => 1,
            'n_gb_disk' => 20,
            'n_network_mbit' => 200,
            'n_network_dedicated_mbit' => 0,
            'n_ip_address' => 5,
        ];

        $data = $this->callApi($params);

        $this->assertTrue($data['success']);
        $config = $data['data']['configuration'];
        $this->assertEquals(5, $config['n_ip_address']);
        
        $breakdown = $data['data']['breakdown'];
        $ipCost = $breakdown['ip_address']['total'];
        $expectedIPCost = 200000;  // 4 extra IPs @ 50K each
        $this->assertEquals($expectedIPCost, $ipCost, '5 IPs should charge 200K for 4 extra IPs');
    }
}
