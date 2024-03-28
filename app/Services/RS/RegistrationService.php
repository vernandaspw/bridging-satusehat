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

    public static function getDate($tanggal = null)
    {
        try {
            $query = [];

            if ($tanggal != null) {
                $query['tanggal'] = $tanggal;
            } else {
                $query['tanggal'] = date('Y-m-d');
            }

            $httpClient = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-TOKEN' => env('SIFA_SATUSEHAT_SERVICE_TOKEN'),
                ],
                'query' => $query,
            ]);

            $request = $httpClient->get(env('SIFA_SATUSEHAT_SERVICE_URL') . '/registration/rajal/date');

            // Mengambil respons dari API
            $response = $request->getBody()->getContents();
            $data = json_decode($response, true);

            return $data['data'];
        } catch (\Exception $e) {
            dd($e);
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

    public static function updateEncounterId($noreg, $encounter_id)
    {
        try {
            // dd($kodeDokter, $kodeIHS);
            $httpClient = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-TOKEN' => env('SIFA_SATUSEHAT_SERVICE_TOKEN'),
                ],
                'body' => json_encode([
                    'noreg' => $noreg,
                    'encounter_id' => $encounter_id,
                    'isProd' => env('IS_PROD')
                ]),
            ]);

            $request = $httpClient->post(env('SIFA_SATUSEHAT_SERVICE_URL') . '/registration/update/encounterid');
            $response = $request->getBody()->getContents();

            $statusCode = $request->getStatusCode();

            if ($statusCode == 200) {
                $result = json_decode($response, true);
                return $result;
            } else {
                // Tangani kesalahan jika status bukan 200 OK
                // Misalnya, lempar Exception dengan pesan kesalahan yang sesuai
                throw new \Exception("Failed to update IHS: " . $statusCode);
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            // Tangani kesalahan
            return []; // Mengembalikan array kosong jika terjadi kesalahan
        }
    }

}
