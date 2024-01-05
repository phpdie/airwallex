<?php

namespace Airwallex;
class Client
{
    private $clientId;

    private $apiKey;

    private $sandbox;

    public function __construct($clientId, $apiKey, $sandbox = false)
    {
        $this->clientId = $clientId;
        $this->apiKey = $apiKey;
        $this->sandbox = $sandbox;
    }

    private function getUrl($key = 'api')
    {
        $env = $this->sandbox ? 'sandbox' : 'production';
        $envUrl = [
            'sandbox' => [
                'api' => 'https://api-demo.airwallex.com',
            ],
            'production' => [
                'api' => 'https://api.airwallex.com',
            ],
        ];
        return $envUrl[$env][$key] ?? '';
    }

    public function getAccessToken()
    {
        $header = [
            'x-client-id:' . $this->clientId,
            'x-api-key:' . $this->apiKey,
            'Content-length:0',
        ];
        $url = $this->getUrl() . '/api/v1/authentication/login';
        return $this->curl($url, [], [], 'POST', $header);
    }

    private function curl($url, $query = [], $body = [], $method = 'GET', $headers = [])
    {
        $curl = curl_init();
        $opt = [
            CURLOPT_URL => $query ? $url . '?' . http_build_query($query) : $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
        ];
        if ($headers) $opt[CURLOPT_HTTPHEADER] = $headers;
        if ($body) $opt[CURLOPT_POSTFIELDS] = $body;

        curl_setopt_array($curl, $opt);
        $response = curl_exec($curl);
        $errno = curl_errno($curl);

        if ($errno) {
            curl_close($curl);
            throw new Exception($errno);
        }
        curl_close($curl);

        return $response;
    }
}