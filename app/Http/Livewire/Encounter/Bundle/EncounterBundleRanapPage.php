<?php

namespace App\Http\Livewire\Encounter\Bundle;

use App\Services\RS\RegistrationServiceIgdRanap;
use Livewire\Component;

class EncounterBundleRanapPage extends Component
{
    public $registrations = [];
    public $tanggal;
    public $page = 1;

    public function render()
    {
        return view('livewire.encounter.bundle.encounter-bundle-ranap-page')->extends('layouts.app')->section('main');
    }

    public function page($page)
    {
        $this->page = $page;
        $this->fetchData(null, $page);
    }

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        $this->fetchData();
    }

    public function fetchData($tanggal = null, $page = null)
    {
        try {
            if (!empty($tanggal)) {
                $this->tanggal = $tanggal;
            }
            if (!empty($page)) {
                $this->page = $page;
            }
            $this->registrations = RegistrationServiceIgdRanap::getData($this->tanggal, $this->page, 'RANAP');
            // dd($this->registrations);
           
        } catch (\Exception $e) {
            // Tangani kesalahan
            return response()->json(['error' => 'Failed to fetch data'], 500);
            // return view('error-view', ['error' => 'Failed to fetch data']);
        }
    }

    public function updated()
    {
        $this->fetchData($this->tanggal);
    
    }

    public function tanggal()
    {
        $this->fetchData($this->tanggal);
    }
}
