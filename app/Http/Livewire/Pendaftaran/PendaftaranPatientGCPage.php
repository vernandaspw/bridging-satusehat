<?php

namespace App\Http\Livewire\Pendaftaran;

use App\Services\RS\RsPasienService;
use Livewire\Component;

class PendaftaranPatientGCPage extends Component
{
    public function render()
    {
        return view('livewire.pendaftaran.pendaftaran-patient-g-c-page')->extends('layouts.app')->section('main');
    }

    public function mount()
    {
        $this->fetchData();
    }

    public $data = [];

    public function fetchData($MedicalNo = null, $nik = null, $no_bpjs = null, $nama = null, $page = null)
    {
        try {
            $this->data = RsPasienService::getData($MedicalNo, $nik, $no_bpjs, $nama, $page);
            // dd($this->data);
        } catch (\Exception $e) {
            return $this->emit('error', $e->getMessage());
        }
    }

    public function page($page)
    {
        $this->fetchData(null, null, null, null, $page);
        // dd($this->registrations);
    }

}
