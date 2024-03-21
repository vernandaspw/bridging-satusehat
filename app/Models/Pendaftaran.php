<?php

namespace App\Models;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Pendaftaran extends Model
{
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public static function getData($tanggal = null)
    {
        try {
            $queryParams = [];

            if ($tanggal != null) {
                $queryParams['tanggal'] = $tanggal;
            } else {
                $queryParams['tanggal'] = date('Y-m-d');
            }

            $httpClient = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-TOKEN' => env('SIFA_SATUSEHAT_SERVICE_TOKEN'),
                ],
                'query' => $queryParams,
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

    public function byKodeDokter($kodeDokter = '')
    {
        $request = $this->httpClient->get('https://daftar.rsumm.co.id/api.simrs/dokter/select/' . $kodeDokter);
        $response = $request->getBody()->getContents();
        $data = json_decode($response, true);
        return $data['data'];
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

    public function updateEncounterId($noreg, $encounter_id)
    {
        try {
            // dd($kodeDokter, $kodeIHS);
            $request = $this->httpClient->post(env('SIFA_SATUSEHAT_SERVICE_URL') . '/registration/update/encounterid', [
                'headers' => [
                    'X-TOKEN' => env('SIFA_SATUSEHAT_SERVICE_TOKEN'),
                ],
                'body' => json_encode([
                    'noreg' => $noreg,
                    'encounter_id' => $encounter_id,
                ]),
            ]);
            $response = $request->getBody()->getContents();
            // dd($response);
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
