<?php

namespace App\Services\RS;

use GuzzleHttp\Client;

class RegistrationService
{
    public static function getData($tanggal = null, $page = null)
    {
        try {
            $query = [];

            if ($tanggal != null) {
                $query['tanggal'] = $tanggal;
            } else {
                $query['tanggal'] = date('Y-m-d');
            }

            if($page != null) {
                $query['page'] = $page;
            }
            // dd($page);
            $httpClient = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-TOKEN' => env('SIFA_SATUSEHAT_SERVICE_TOKEN'),
                ],
                'query' => $query,
            ]);

            $request = $httpClient->get(env('SIFA_SATUSEHAT_SERVICE_URL') . '/registration/rajal');

            // $request = $this->httpClient->get('https://daftar.rsumm.co.id/api.simrs/pendaftaran', [
            //     'query' => $queryParams,
            // ]);

            // Mengambil respons dari API
            $response = $request->getBody()->getContents();
            $data = json_decode($response, true);

            return $data['data']; // Mengambil bagian 'data' dari respons
        } catch (\Exception $e) {
            // Tangani kesalahan
            return []; // Mengembalikan array kosong jika terjadi kesalahan
        }
    }

    public static function getByKodeReg($noReg)
    {
        $httpClient = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'X-TOKEN' => env('SIFA_SATUSEHAT_SERVICE_TOKEN'),
            ],
        ]);

        $request = $httpClient->get(env('SIFA_SATUSEHAT_SERVICE_URL') . '/registration/rajal/detail?noreg=' . $noReg, [
            'headers' => [
                'X-TOKEN' => env('SIFA_SATUSEHAT_SERVICE_TOKEN'),
            ],
        ]);
        $response = $request->getBody()->getContents();
        $data = json_decode($response, true);

        return $data['data'];
    }
}
