<?php

namespace App\Components;

//use Aws\Handler\GuzzleV6\GuzzleHandler as BaseAwsGuzzleHandler;
use Aws\Handler\Guzzle\GuzzleHandler as BaseAwsGuzzleHandler;

use GuzzleHttp\Client;
class GuzzleHandler2 extends BaseAwsGuzzleHandler
{
    public function __construct()
    {
        parent::__construct(new Client(
            ['verify' => false]
        ));
    }
}
