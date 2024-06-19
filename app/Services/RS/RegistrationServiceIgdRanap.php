<?php

namespace App\Services\RS;

use GuzzleHttp\Client;

class RegistrationServiceIgdRanap
{
    public static function getData($tanggal = null, $page = null, $tipe = null)
    {
        
        try {
            $query = [];

            if ($tanggal != null) {
                $query['tanggal'] = $tanggal;
            } else {
                $query['tanggal'] = date('Y-m-d');
            }

            if ($page != null) {
                $query['page'] = $page;
            }

            $httpClient = new Client([
                'headers' => [
                    'X-TOKEN' => env('BRIDGING_SATUSEHAT_SERVICE_TOKEN'),
                    // 'Content-Type' => 'application/json',
                ],
                'query' => $query,
            ]);
            $tipeG = strtolower($tipe);
            
            $request = $httpClient->get(env('BRIDGING_SATUSEHAT_SERVICE_URL') . '/registration/'. $tipeG);
            
            // Mengambil respons dari API
            
            $response = $request->getBody()->getContents();
            $data = json_decode($response, true);

            return $data['data']; // Mengambil bagian 'data' dari respons
        } catch (\Exception $e) {
            // Tangani kesalahan
            return []; // Mengembalikan array kosong jika terjadi kesalahan
        }
    }


    public static function getCount($year = null, $month = null)
    {
        try {
            $query = [];

            if ($year != null) {
                $query['year'] = $year;
            } else {
                $query['year'] = date('Y');
            }

            if ($year != null) {
                $query['month'] = $month;
            } else {
                $query['month'] = date('m');
            }

            $httpClient = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-TOKEN' => env('BRIDGING_SATUSEHAT_SERVICE_TOKEN'),
                ],
                'query' => $query,
            ]);

            $request = $httpClient->get(env('BRIDGING_SATUSEHAT_SERVICE_URL') . '/registration/rajal/count');

            // Mengambil respons dari API
            $response = $request->getBody()->getContents();
            $data = json_decode($response, true);

            return $data['data'];
        } catch (\Throwable $e) {
            dd($e->getMessage());
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
                    'X-TOKEN' => env('BRIDGING_SATUSEHAT_SERVICE_TOKEN'),
                ],
                'query' => $query,
            ]);

            $request = $httpClient->get(env('BRIDGING_SATUSEHAT_SERVICE_URL') . '/registration/rajal/date');

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

    public static function getLastDay($tanggal = null, $hari = null, $tipe = null)
    {
        try {
            $query = [];

            if ($tanggal != null) {
                $query['tanggal'] = $tanggal;
            } else {
                $query['tanggal'] = date('Y-m-d');
            }

            if ($hari != null) {
                $query['hari'] = $hari;
            } else {
                $query['hari'] = 0;
            }

            $query['isProd'] = env('IS_PROD');

            $httpClient = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-TOKEN' => env('BRIDGING_SATUSEHAT_SERVICE_TOKEN'),
                ],
                'query' => $query,
            ]);

            switch ($tipe) {
                case  'IGD':
                    $request = $httpClient->get(env('BRIDGING_SATUSEHAT_SERVICE_URL') . '/registration/igd/lastday');
                    break;
                case 'RANAP':
                    $request = $httpClient->get(env('BRIDGING_SATUSEHAT_SERVICE_URL') . '/registration/ranap/lastday');
                    break;
                default:
            }




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

    public static function getByKodeReg($noReg, $tipe)
    {
        $httpClient = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'X-TOKEN' => env('BRIDGING_SATUSEHAT_SERVICE_TOKEN'),
            ],
        ]);

        switch ($tipe) {
            case  'IGD':
                $request = $httpClient->get(env('BRIDGING_SATUSEHAT_SERVICE_URL') . '/registration/igd/detail?noreg=' . $noReg, [
                    'headers' => [
                        'X-TOKEN' => env('BRIDGING_SATUSEHAT_SERVICE_TOKEN'),
                        'Content-Type' => 'application/json',
                    ],
                ]);
                break;
            case 'RANAP':
                $request = $httpClient->get(env('BRIDGING_SATUSEHAT_SERVICE_URL') . '/registration/ranap/detail?noreg=' . $noReg, [
                    'headers' => [
                        'X-TOKEN' => env('BRIDGING_SATUSEHAT_SERVICE_TOKEN'),
                        'Content-Type' => 'application/json',
                    ],
                ]);
                break;
            default:
        }



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
                    'X-TOKEN' => env('BRIDGING_SATUSEHAT_SERVICE_TOKEN'),
                ],
                'body' => json_encode([
                    'noreg' => $noreg,
                    'encounter_id' => $encounter_id,
                    'isProd' => env('IS_PROD'),
                ]),
            ]);

            $request = $httpClient->post(env('BRIDGING_SATUSEHAT_SERVICE_URL') . '/registration/update/encounterid');
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
