<?php

namespace Tests\Feature;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

class ApiUserListTest extends \PHPUnit\Framework\TestCase
{
    private $client;
    private $baseUrl = 'http://127.0.0.1:8001';

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'http_errors' => false,
            'timeout' => 5,
        ]);
    }

    /**
     * Test API get user list
     */
    public function test_api_user_list_returns_json(): void
    {
        try {
            $response = $this->client->get('/api/user/list');

            $this->assertEquals(200, $response->getStatusCode());
            
            $body = json_decode((string) $response->getBody(), true);
            
            $this->assertIsArray($body);
            $this->assertEquals('success', $body['status']);
            $this->assertArrayHasKey('data', $body);
            $this->assertArrayHasKey('count', $body);
            $this->assertIsArray($body['data']);
            $this->assertGreaterThan(0, $body['count']);
            
        } catch (ConnectException $e) {
            $this->markTestSkipped('Server không chạy tại ' . $this->baseUrl);
        }
    }
}
