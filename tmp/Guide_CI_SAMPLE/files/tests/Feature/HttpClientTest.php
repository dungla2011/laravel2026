<?php

namespace Tests\Feature;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

class HttpClientTest extends \PHPUnit\Framework\TestCase
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
     * Test trang chủ với server thực tế
     */
    public function test_home_page_returns_200(): void
    {
        try {
            $response = $this->client->get('/');

            $this->assertEquals(200, $response->getStatusCode());
            $this->assertStringContainsString('Laravel', (string) $response->getBody());
        } catch (ConnectException $e) {
            $this->markTestSkipped('Server không chạy tại ' . $this->baseUrl);
        }
    }
}
