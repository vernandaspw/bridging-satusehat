<?php

namespace App\Http\Livewire\Penunjang;

use App\Services\RS\PenunjangService;
use Livewire\Component;

class Radiologi extends Component
{
    public $Radiologix = [];
    public $page = 1;

    public function render()
    {
        return view('livewire.penunjang.radiologi')->extends('layouts.app')->section('main');
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
            $this->Radiologix = PenunjangService::getData($this->page,'radiologi');
           
            // dd($this->registrations);
        } catch (\Exception $e) {
            // Tangani kesalahan
            return response()->json(['error' => 'Failed to fetch data'], 500);
            // return view('error-view', ['error' => 'Failed to fetch data']);
        }
    }
}