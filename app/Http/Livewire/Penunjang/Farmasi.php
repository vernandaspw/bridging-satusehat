<?php

namespace App\Http\Livewire\Penunjang;

use Livewire\Component;
use App\Services\RS\PenunjangService;

class Farmasi extends Component
{
    public $Farmasix = [];
    public $page = 1;

    public function render()
    {
      
        return view('livewire.penunjang.farmasi')->extends('layouts.app')->section('main');
    }

    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData($tanggal = null, $page = null)
    {
        try {
            if (!empty($page)) {
                $this->page = $page;
            }
            $this->Farmasix = PenunjangService::getData($this->page,'Farmasi');
           
            // dd($this->registrations);
        } catch (\Exception $e) {
            // Tangani kesalahan
            return response()->json(['error' => 'Failed to fetch data'], 500);
            // return view('error-view', ['error' => 'Failed to fetch data']);
        }
    }

}
