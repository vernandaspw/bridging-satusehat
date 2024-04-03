<?php

namespace App\Services\RS;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class RsPasienService
{
    public static function getData($MedicalNo = null, $nik = null, $no_bpjs = null, $nama = null, $page = null)
    {
        try {
            $query = [];

            if($MedicalNo != null) {
                $query['MedicalNo'] = $MedicalNo;
            }
            if($nik != null) {
                $query['nik'] = $nik;
            }
            if($no_bpjs != null) {
                $query['no_bpjs'] = $no_bpjs;
            }
            if($nama != null) {
                $query['nama'] = $nama;
            }
            if($page != null) {
                $query['page'] = $page;
            }
            $httpClient = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-TOKEN' => env('BRIDGING_SATUSEHAT_SERVICE_TOKEN'),
                ],
                'query' => $query,
            ]);
            $request = $httpClient->get(env('BRIDGING_SATUSEHAT_SERVICE_URL') . '/ss/pasien');

            // Mengambil respons dari API
            $response = $request->getBody()->getContents();
            $data = json_decode($response, true);

            return $data['data'];
        } catch (\Exception $e) {
            // Tangani kesalahan
            return [];
        }
    }
}
