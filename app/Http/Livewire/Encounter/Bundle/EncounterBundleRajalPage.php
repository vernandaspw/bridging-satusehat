<?php

namespace App\Http\Livewire\Encounter\Bundle;

use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Services\RS\RegistrationService;
use App\Services\SatuSehat\PatientService;
use Livewire\Component;

class EncounterBundleRajalPage extends Component
{

    public function render()
    {
        return view('livewire.encounter.bundle.encounter-bundle-rajal-page')->extends('layouts.app')->section('main');
    }

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        $this->fetchData();
    }

    public $registrations = [];
    public $tanggal;
    public function fetchData($tanggal = null, $page = null)
    {
        try {
            $this->registrations = RegistrationService::getData($tanggal, $page);
        } catch (\Exception $e) {
            // Tangani kesalahan
            return response()->json(['error' => 'Failed to fetch data'], 500);
            // return view('error-view', ['error' => 'Failed to fetch data']);
        }
    }
    public function page($page)
    {
        $this->fetchData(null, $page);
        // dd($this->registrations);
    }

    public function tanggal()
    {
        $this->fetchData($this->tanggal, null);
    }

    public function kirim($noReg)
    {
        $detailPendaftaran = RegistrationService::getByKodeReg($noReg);
        // if (!empty($detailPendaftaran['ss_encounter_id'])) {
        //     $errorMessage = 'sudah pernah mengirim encounter';
        //     return redirect()->back()->with('error', $errorMessage);
        // }
        $nik = $detailPendaftaran['nik'];
        //  CEK NIK PASIEN
        if (empty($nik)) {
            $errorMessage = 'nik data is not available.';
            return $this->emit('error', $errorMessage);
        }
        // CEK IHS PASIEN
        $patient = PatientService::getRequest('Patient', ['identifier' => $nik]);
        if (empty($patient['entry'])) {
            $errorMessage = 'The patient has not been registered in SatuSehat.';
            return $this->emit('error', $errorMessage);
        }
        $patientId = $patient['entry'][0]['resource']['id'];
        $patientName = $patient['entry'][0]['resource']['name'][0]['text'];

        //   CEK KODE DOKTER
        $kodeDokter = $detailPendaftaran['kode_dokter'];
        if (empty($kodeDokter)) {
            $errorMessage = 'Sorry, the patient you are looking for is not registered with any doctor or the search results are ambiguous.';
            return $this->emit('error', $errorMessage);
        }
        // CEK IHS DOKTER
        $dokter = Dokter::getByKode($kodeDokter);
        if (empty($dokter['ihs'])) {
            $errorMessage = 'IHS Dokter is not available.';
            return $this->emit('error', $errorMessage);
        }


        // CEK IHS PASIEN DI SPHAIRA
        $pasien = Pasien::getByNik($nik);
        if (empty($pasien['ihs'])) {
            $errorMessage = 'IHS pasien is not available.';
            return $this->emit('error', $errorMessage);
        }

        // CEK LOKASI IHS
        dd('perlu maping data pada ogranisasi = serviceUnit, location = room');

        $location = Location::where('ServiceUnitID', $detailPendaftaran['ServiceUnitID'])->first();
        if (empty($location)) {
            $errorMessage = 'ID location by ServiceUnitID is not available.';
            return redirect()->back()->with('error', $errorMessage);
        }
        $location_id = $location->location_id;
        $organization_id = $location->organization_id;

        $body = [
            'kodeReg' => $noReg,
            'status' => 'arrived',
            'patientId' => $patientId,
            'patientName' => $patientName,
            'practitionerIhs' => $dokter['ihs'],
            'practitionerName' => $dokter['nama_dokter'],
            'organizationId' => $organization_id,
            'locationId' => $location_id,
            'locationName' => $location->name,
            'statusHistory' => 'arrived',
            'RegistrationDateTime' => $detailPendaftaran['RegistrationDateTime'],
            'DischargeDateTime' => $detailPendaftaran['DischargeDateTime'],
        ];
        // dd($body);

        try {

            // send API
            $resultApi = $this->encounterService->PostEncounterCondition($body);
            // dd($resultApi);
            // $serviceProvider = $resultApi['serviceProvider']['reference'];
            // $serviceProv = explode('/', $serviceProvider);

            // // send DB
            // Encounter::create([
            //     'encounter_id' => $resultApi['id'],
            //     'kode_register' => $body['kodeReg'],
            //     'class_code' => $resultApi['class']['code'],
            //     'patient_ihs'  => $body['patientId'],
            //     'patient_name'  => $body['patientName'],
            //     'practitioner_ihs'  => $body['practitionerIhs'],
            //     'practitioner_name'  => $body['practitionerName'],
            //     'location_id' => $body['locationId'],
            //     'service_provider' => end($serviceProv),
            //     'status' => $body['status'],
            //     'status_history' => $body['statusHistory'],
            //     'periode_start' => $resultApi['period']['start'],
            //     'created_by' => auth()->user()->id ?? ''
            // ]);
            $encounterID = $resultApi['entry'][0]['response']['resourceID'];
            if (empty($encounterID)) {
                $errorMessage = 'EncounterID tidak valid';
                return redirect()->back()->with('error', $errorMessage);
            }
            // return $encounterID;
            // simpan encounterID ke registration
            $this->pendaftaran->updateEncounterId($noReg, $encounterID);

            $message = 'Bundle Encounter data has been created successfully.';
            return redirect()->back()->with('success', $message);
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            dd($errorMessage);
        }

    }

    public function kirimPerTanggal()
    {

    }
}
