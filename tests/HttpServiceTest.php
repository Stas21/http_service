<?php

namespace Tests;

use Illuminate\Support\Facades\Config;
use N1X0N\HttpService\HttpService;

class HttpServiceTest extends TestCase
{
    private $http_service;

    protected function setUp()
    {
        parent::setUp();

        $this->http_service = new HttpService();
    }

    public function test_basic_test()
    {
        $http_service = $this->http_service;
        $http_service->setHost('user');
        $http_host = $http_service->getHost();

        $host = Config::get('host.user');
        $this->assertEquals($host, $http_host);
    }
}