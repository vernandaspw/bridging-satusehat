<?php

namespace App\Http\Livewire\Encounter\Bundle;

use App\Models\Dokter;
use App\Models\Location;
use App\Models\Pasien;
use App\Services\RS\RegistrationService;
use App\Services\SatuSehat\EncounterService;
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
        // dd($detailPendaftaran['diagnosas']);
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
        // cek roomID, rooomCode, cek serviceUnitID
        if (!empty($detailPendaftaran['RoomID'])) {
            $location = Location::where('RoomID', $detailPendaftaran['RoomID'])->first();
        } elseif (!empty($detailPendaftaran['RoomCode'])) {
            $location = Location::where('RoomCode', $detailPendaftaran['RoomCode'])->first();
        } elseif (!empty($detailPendaftaran['ServiceUnitID'])) {
            $location = Location::where('ServiceUnitID', $detailPendaftaran['ServiceUnitID'])->first();
        } else {
            $location = Location::where('identifier_value', $detailPendaftaran['identifier_value'])->first();
        }

        if (empty($location)) {
            $errorMessage = 'ID location is not available.';
            return $this->emit('error', $errorMessage);
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
            'diagnosas' => $detailPendaftaran['diagnosas'],
        ];

        try {
            // send API
            $resultApi = EncounterService::PostEncounterCondition($body);

            // $serviceProvider = $resultApi['serviceProvider']['reference'];
            // $serviceProv = explode('/', $serviceProvider);

            if (!empty($resultApi['entry'][0]['response']['resourceID'])) {
                $encounterID = $resultApi['entry'][0]['response']['resourceID'];
            } else {
                $url = $resultApi['entry'][0]['response']['location'];
                $uuid = explode('/', parse_url($url, PHP_URL_PATH))[4];
                $encounterID = $uuid;
            }

            if (empty($encounterID)) {
                $errorMessage = 'EncounterID tidak valid';
                return $this->emit('error', $errorMessage);
            }
            // return $encounterID;
            // simpan encounterID ke registration
            RegistrationService::updateEncounterId($noReg, $encounterID);
            $this->fetchData($this->tanggal);
            $message = 'Bundle Encounter data has been created successfully.';
            return $this->emit('success', $message);
        } catch (\Throwable $e) {

            $errorMessage = 'Coba ulang ' . $e->getMessage();
            return $this->emit('error', $errorMessage);
        }
    }

    public function kirimPerTanggal()
    {
        // kirim yang telah discharge dan belum memiliki encounterID
        $tanggal = $this->tanggal;
        try {
            $registrations = RegistrationService::getDate($tanggal);

            // pengiriman data
            foreach ($registrations as $registration) {
                $nik = $registration['nik'];
                //  CEK NIK PASIEN

                if (!empty($nik)) {
                    $patient = PatientService::getRequest('Patient', ['identifier' => $nik]);
                }
                if (!empty($patient['entry'])) {
                    $patientId = $patient['entry'][0]['resource']['id'];
                    $patientName = $patient['entry'][0]['resource']['name'][0]['text'];
                }
                $kodeDokter = $registration['kode_dokter'];
                if (!empty($kodeDokter)) {
                    $dokter = Dokter::getByKode($kodeDokter);
                }
                // CEK IHS DOKTER
                if (!empty($dokter['ihs'])) {

                }
                $pasien = Pasien::getByNik($nik);

                // CEK IHS PASIEN DI SPHAIRA
                if (!empty($pasien['ihs'])) {

                }

                // CEK LOKASI IHS
                // cek roomID, rooomCode, cek serviceUnitID
                if (!empty($registration['RoomID'])) {
                    $location = Location::where('RoomID', $registration['RoomID'])->first();
                } elseif (!empty($registration['RoomCode'])) {
                    $location = Location::where('RoomCode', $registration['RoomCode'])->first();
                } elseif (!empty($registration['ServiceUnitID'])) {
                    $location = Location::where('ServiceUnitID', $registration['ServiceUnitID'])->first();
                } elseif (!empty($registration['identifier_value'])) {
                    $location = Location::where('identifier_value', $registration['identifier_value'])->first();
                }

                if (!empty($location)) {
                    $location_id = $location->location_id;
                    $organization_id = $location->organization_id;
                }

                if (!empty($registration['no_registrasi']) &&
                    !empty($patientId) &&
                    !empty($patientName) &&
                    !empty($$dokter['ihs']) &&
                    !empty($dokter['nama_dokter']) &&
                    !empty($organization_id) &&
                    !empty($$location->name) &&
                    !empty($registration['RegistrationDateTime']) &&
                    !empty($registration['DischargeDateTime'])
                ) {
                    $noReg = $registration['no_registrasi'];
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
                        'RegistrationDateTime' => $registration['RegistrationDateTime'],
                        'DischargeDateTime' => $registration['DischargeDateTime'],
                        'diagnosas' => $registration['diagnosas'],
                    ];

                    try {
                        // send API
                        $resultApi = EncounterService::PostEncounterCondition($body);
                        // $serviceProvider = $resultApi['serviceProvider']['reference'];
                        // $serviceProv = explode('/', $serviceProvider);

                        // dd($resultApi);
                        if (!empty($resultApi['entry'][0]['response']['resourceID'])) {
                            $encounterID = $resultApi['entry'][0]['response']['resourceID'];
                        } else {
                            $url = $resultApi['entry'][0]['response']['location'];
                            $uuid = explode('/', parse_url($url, PHP_URL_PATH))[4];
                            $encounterID = $uuid;
                        }

                        if (!empty($encounterID)) {
                            // simpan encounterID ke registration
                            RegistrationService::updateEncounterId($noReg, $encounterID);
                            $this->fetchData($this->tanggal);
                            $message = 'Bundle Encounter data has been created successfully.';
                            return $this->emit('success', $message);
                        }
                    } catch (\Throwable $e) {

                        $errorMessage = 'Coba ulang ' . $e->getMessage();
                        return $this->emit('error', $errorMessage);
                    }

                }
            }

        } catch (\Exception $e) {
            dd($e);
            // Tangani kesalahan
            return response()->json(['error' => 'Failed to fetch data'], 500);
            // return view('error-view', ['error' => 'Failed to fetch data']);
        }

    }
}
