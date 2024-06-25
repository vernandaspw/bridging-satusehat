<?php

namespace App\Services\RS;

use GuzzleHttp\Client;

class PenunjangService
{
    public static function getData($page = null, $tipe = null)
    {        
        try {
            $query = [];

            if ($page != null) {
                $query['page'] = $page;
            }

            $httpClient = new Client([
                'headers' => [
                    'X-TOKEN' => env('BRIDGING_SATUSEHAT_SERVICE_TOKEN'),
                    'Content-Type' => 'application/json',
                ],
                'query' => $query,
            ]);
            $tipeG = strtolower($tipe);            
            
            $request = $httpClient->get(env('BRIDGING_SATUSEHAT_SERVICE_URL') . '/penunjang/'. $tipeG);
            
            // Mengambil respons dari API
            
            $response = $request->getBody()->getContents();
            $data = json_decode($response, true);

            return $data['data']; // Mengambil bagian 'data' dari respons
        } catch (\Exception $e) {
            // Tangani kesalahan
            return []; // Mengembalikan array kosong jika terjadi kesalahan
        }


       
         
    }

}