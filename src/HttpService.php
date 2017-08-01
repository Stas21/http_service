<?php

namespace N1X0N\HttpService;

use Exception;
use Guzzle\Http\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

/**
 * Class HttpService
 *
 * @package N1X0N\HttpService\Services
 */
class HttpService
{
    /**
     * @var array
     */
    private $methods = [
        'get',
        'post',
        'put',
        'delete',
    ];

    /**
     * @var array
     */
    private $headers = [
        'Accept' => 'application/json'
    ];

    /**
     * @var bool
     */
    private $http_errors;

    /**
     * @var
     */
    private $client;

    /**
     * @var
     */
    private $host;

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setHost(string $host)
    {
        $this->host = Config::get('services.' . $host);

        return $this;
    }

    /**
     * Send request.
     *
     * @param string $uri
     * @param string $method
     * @param array  $data
     * @param array  $headers
     *
     * @return array
     */
    public function send(string $uri, string $method, array $data, array $headers = [])
    {
        return $this
            ->mergeHeaders($headers)
            ->createClient()
            ->sendRequest($method, $uri, $data)
            ->getResponse();
    }

    /**
     * Call methods.
     *
     * @param $name
     * @param $arguments
     *
     * @return array
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (!in_array($name, $this->methods)) {
            throw new Exception('Method not found');
        }

        $uri = Arr::get($arguments, 0, '');
        $data = Arr::get($arguments, 1, []);
        $headers = Arr::get($arguments, 2, []);

        return $this->send($uri, $name, $data, $headers);
    }

    /**
     * Set headers.
     *
     * @param array $headers
     *
     * @return $this
     */
    private function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Merge array headers.
     *
     * @param array $headers
     *
     * @return $this
     */
    public function mergeHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * Add header.
     *
     * @param string $header_name
     * @param string $header_value
     *
     * @return $this
     */
    public function addHeader(string $header_name, string $header_value)
    {
        $this->headers[$header_name] = $header_value;

        return $this;
    }

    /**
     * Reset headers.
     *
     * @return $this
     */
    public function resetHeaders()
    {
        $this->headers = [
            'Accept' => 'application/json'
        ];

        return $this;
    }

    /**
     * Create client.
     *
     * @return $this
     */
    public function createClient()
    {
        $this->client = new Client(['base_uri' => $this->host]);

        return $this;
    }

    /**
     * Send request.
     *
     * @param string $method
     * @param string $url
     * @param array  $data
     *
     * @return $this
     */
    public function sendRequest(string $method, string $url, array $data)
    {
        if (Arr::get($data, 'file')) {
            $file = $data['file'];
            $request_data = [
                'headers'     => $this->headers,
                'http_errors' => $this->http_errors,
                'multipart'   => [
                    [
                        'name'     => 'file',
                        'contents' => file_get_contents($file->getPathname()),
                        'filename' => $file->getClientOriginalName()
                    ]
                ],
            ];
        } else {
            $request_data = [
                'headers'     => $this->headers,
                'http_errors' => $this->http_errors,
                'json'        => $data
            ];
        }
        $this->response = $this->client->$method($url, $request_data);

        return $this;
    }

    /**
     * Get array response.
     *
     * @return array
     * @throws Exception
     */
    public function getResponse()
    {
        if (!$this->response) {
            Throw new Exception('Not sent a request');
        }

        return [
            'body'   => json_decode($this->response->getBody()->getContents()),
            'status' => $this->response->getStatusCode(),
        ];
    }
}
