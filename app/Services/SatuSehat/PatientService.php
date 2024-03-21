<?php

namespace App\Services\SatuSehat;

use GuzzleHttp\Client;

class PatientService
{
    protected $httpClient;
    protected $accessToken;
    protected $config;

    public function __construct()
    {
        $this->httpClient = new Client();
        $this->accessToken = new AccessToken();
        $this->config = new ConfigSatuSehat();
    }

    protected static function processParams($params)
    {

        if (isset($params['identifier'])) {
            $params['identifier'] = 'https://fhir.kemkes.go.id/id/nik|' . $params['identifier'];
        }
        if (isset($params['name'])) {
            $params['name'] = $params['name'];
        }

        if (isset($params['birthdate'])) {
            $params['birthdate'] = $params['birthdate'];
        }

        return $params;
    }

    public static function getRequest($endpoint, $params = [])
    {
        $token = AccessToken::token();

        $url = ConfigSatuSehat::setUrl() . $endpoint;

        $params = PatientService::processParams($params);

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        $httpClient = new Client();
        $response = $httpClient->get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        $data = $response->getBody()->getContents();
        return json_decode($data, true);
    }
}
