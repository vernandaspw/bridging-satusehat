<?php

namespace App\Services\RS;

use Illuminate\Support\Facades\Http;

class RsDokterService
{
    public static function getByKode($kodeDokter)
    {
        try {
            $headers = [
                'X-TOKEN' => env('BRIDGING_SATUSEHAT_SERVICE_TOKEN'),
            ];
            $request = Http::withHeaders($headers)->get(env('BRIDGING_SATUSEHAT_SERVICE_URL') . '/dokter/detail/' . $kodeDokter);
            $response = $request->getBody()->getContents();
            $result = json_decode($response, true);

            return $result['data'];
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }
}
