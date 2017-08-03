<?php

namespace Tests;

use N1X0N\HttpService\HttpService;
use Illuminate\Config\Repository as Config;

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

        $config = new Config();
        $host = $config->get('hosts.user');
        dd($host, $http_host);
        $this->assertEquals($host, $http_host);
    }
}