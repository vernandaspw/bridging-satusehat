<?php

namespace App\Models;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Dokter extends Model
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

    public function getData()
    {
        try {
            // $request = $this->httpClient->get('https://daftar.rsumm.co.id/api.simrs/dokter');

            $request = $this->httpClient->get(env('SIFA_SATUSEHAT_SERVICE_URL') . '/dokter', [
                'headers' => [
                    'X-TOKEN' => env('SIFA_SATUSEHAT_SERVICE_TOKEN'),
                ],
            ]);
            $response = $request->getBody()->getContents();
            $data = json_decode($response, true);

            return $data['data']; // Mengambil bagian 'data' dari respons
        } catch (\Exception $e) {
            dd($e->getMessage());
            // Tangani kesalahan
            return []; // Mengembalikan array kosong jika terjadi kesalahan
        }
    }

    // Method to fetch data for a specific doctor
    public static function editDokter($kode_dokter)
    {
        try {
            $httpClient = new Client();
            $response = $httpClient->get('https://daftar.rsumm.co.id/api.simrs/dokter/' . $kode_dokter);
            $responseData = json_decode($response->getBody()->getContents(), true);

            // Check if 'data' key exists in the response
            if (isset($responseData['data'])) {
                return $responseData['data'];
            } else {
                \Log::error('Data key not found in API response');
                return null; // Or handle the error in another way
            }
        } catch (\Exception $e) {
            // Log the error
            \Log::error($e->getMessage());
            // Return null or handle the error in another way
            return null;
        }
    }

    public function getSelect()
    {
        $request = $this->httpClient->get('https://daftar.rsumm.co.id/api.simrs/dokter/select');
        $response = $request->getBody()->getContents();
        $data = json_decode($response, true);
        return $data['data'];
    }

    public function getByKodeDokter($kodeDokter)
    {
        $response = Http::get('https://daftar.rsumm.co.id/api.simrs/dokter/detail/' . $kodeDokter);
        return $response->json();
    }

    public static function getByKode($kodeDokter)
    {
        try {
            $headers = [
                'X-TOKEN' =>env('SIFA_SATUSEHAT_SERVICE_TOKEN'),
            ];
            $request = Http::withHeaders($headers)->get(env('SIFA_SATUSEHAT_SERVICE_URL') . '/dokter/detail/' . $kodeDokter);
            $response = $request->getBody()->getContents();
            $result = json_decode($response, true);

            return $result['data'];
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getNik($kodeDokter)
    {
        $request = $this->httpClient->get(env('SIFA_SATUSEHAT_SERVICE_URL') . '/dokter/detail/' . $kodeDokter, [
            'headers' => [
                'X-TOKEN' => env('SIFA_SATUSEHAT_SERVICE_TOKEN'),
            ],
        ]);
        $response = $request->getBody()->getContents();
        $result = json_decode($response, true);

        return $result['data']['nik'];
    }

    public function updateIHS($kodeDokter, $kodeIHS)
    {
        try {
            // dd($kodeDokter, $kodeIHS);
            $request = $this->httpClient->post(env('SIFA_SATUSEHAT_SERVICE_URL') . '/dokter/ihs/' . $kodeDokter, [
                'headers' => [
                    'X-TOKEN' => env('SIFA_SATUSEHAT_SERVICE_TOKEN'),
                ],
                'body' => json_encode([
                    'kodeIHS' => $kodeIHS,
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
