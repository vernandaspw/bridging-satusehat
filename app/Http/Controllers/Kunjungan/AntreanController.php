<?php

namespace App\Http\Controllers\Kunjungan;

use App\Models\Antrean;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AntreanController extends Controller
{
    protected $antrean;

    public function __construct(Antrean $antrean)
    {
        $this->antrean = $antrean;
    }

    public function index(Request $request)
    {
        try {
            // Filter
            // Retrieve query parameters
            $kode_dokter = $request->input('kode_dokter');
            $tanggal = $request->input('tanggal');

            $title = 'Antrean';
            $data = $this->antrean->getData($kode_dokter, $tanggal);
            $dokters = $this->antrean->byKodeDokter();
            return view('pages.kunjungan.antrean.index', ['data' => $data, 'dokters' => $dokters]);
        } catch (\Exception $e) {
            // Tangani kesalahan
            return response()->json(['error' => 'Failed to fetch data'], 500);
            // return view('error-view', ['error' => 'Failed to fetch data']);
        }
    }
}
