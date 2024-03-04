<?php

namespace App\Http\Controllers\MasterData;

use App\Models\Dokter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DokterController extends Controller
{
    protected $dokter;

    public function __construct(Dokter $dokter)
    {
        $this->dokter = $dokter;
    }

    public function index(Request $request)
    {
        try {
            $title = 'Dokter';
            $data = $this->dokter->getData();
            return view('pages.md.dokter.index', ['data' => $data]);
        } catch (\Exception $e) {
            // Tangani kesalahan
            return response()->json(['error' => 'Failed to fetch data'], 500);
            // return view('error-view', ['error' => 'Failed to fetch data']);
        }
    }

    public function edit($kode_dokter)
    {
        try {
            $title = 'Dokter';
            $data = $this->dokter->editDokter($kode_dokter);
            return view('pages.md.dokter.edit', ['data' => $data]);
        } catch (\Exception $e) {
            // Tangani kesalahan
            return response()->json(['error' => 'Failed to fetch data'], 500);
            // return view('error-view', ['error' => 'Failed to fetch data']);
        }
    }
}
