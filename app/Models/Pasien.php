<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;

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
            $request = $this->httpClient->get('http://localhost:5000/sifa-si-master/api/pasien', [
                'headers' => [
                    'X-TOKEN' => env('SIFA_MASTER_TOKEN')
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
}
