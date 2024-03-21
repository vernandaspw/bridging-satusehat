<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class Pasien extends Model
{
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'headers' => [
                'Content-Type' => 'application/json'
            ],
        ]);
    }

    public function getData($no_mr = null, $no_bpjs = null, $nik = null, $nama = null, $take = null)
    {
        try {
            // Menyiapkan query parameters
            $queryParams = [];
            if ($no_mr !== null) {
                $queryParams['no_mr'] = $no_mr;
            }
            if ($no_bpjs !== null) {
                $queryParams['no_bpjs'] = $no_bpjs;
            }
            if ($nik !== null) {
                $queryParams['nik'] = $nik;
            }
            if ($nama !== null) {
                $queryParams['nama'] = $nama;
            }
            if ($take !== null) {
                $queryParams['take'] = $take;
            }
            // Mengirim permintaan HTTP dengan query parameters
            $request = $this->httpClient->get(env('SIFA_SATUSEHAT_SERVICE_URL') .'/pasien', [
                'headers' => [
                    'X-TOKEN' => env('SIFA_SATUSEHAT_SERVICE_TOKEN')
                ],
                'query' => $queryParams,
            ]);

            // $request = $this->httpClient->get('https://daftar.rsumm.co.id/api.simrs/pasien', [
            //     'query' => $queryParams,
            // ]);

            // Mengambil respons dari API
            $response = $request->getBody()->getContents();
            $data = json_decode($response, true);

            // Mengembalikan data pasien
            return $data['data']; // Mengambil bagian 'data' dari respons
        } catch (\Exception $e) {
            // Tangani kesalahan
            return []; // Mengembalikan array kosong jika terjadi kesalahan
        }
    }

    public static function getByNik($nik)
    {
        try {
            $headers = [
                'X-TOKEN' =>env('SIFA_SATUSEHAT_SERVICE_TOKEN'),
            ];
            $request = Http::withHeaders($headers)->get(env('SIFA_SATUSEHAT_SERVICE_URL') . '/pasien/detail/' . $nik);
            $response = $request->getBody()->getContents();
            $result = json_decode($response, true);

            return $result['data'];
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    public function updateIHS($norm, $kodeIHS)
    {
        try {
            // dd($norm, $kodeIHS);
            $request = $this->httpClient->post(env('SIFA_SATUSEHAT_SERVICE_URL') .'/pasien/ihs/' . $norm, [
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
