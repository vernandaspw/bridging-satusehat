<?php

namespace App\Http\Livewire\Encounter\Bundle;

use App\Models\Dokter;
use App\Models\Location;
use App\Models\Pasien;
use App\Services\RS\RegistrationService;
use App\Services\SatuSehat\AccessToken;
use App\Services\SatuSehat\ConfigSatuSehat;
use App\Services\SatuSehat\EncounterService;
use App\Services\SatuSehat\PatientService;
use GuzzleHttp\Client;
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
    public $page = 1;
    public function fetchData($tanggal = null, $page = null)
    {
        try {
            if(!empty($tanggal)){
                $this->tanggal = $tanggal;
            }
            if(!empty($page)){
                $this->page = $page;
            }
            $this->registrations = RegistrationService::getData($this->tanggal, $this->page);
            // dd($this->registrations);
        } catch (\Exception $e) {
            // Tangani kesalahan
            return response()->json(['error' => 'Failed to fetch data'], 500);
            // return view('error-view', ['error' => 'Failed to fetch data']);
        }
    }
    public function page($page)
    {
        $this->page = $page;
        $this->fetchData(null, $page);
        // dd($this->registrations);
    }

    public function tanggal()
    {
        $this->fetchData($this->tanggal);
    }

    // DOKTER ===============================================================
    public function updateIHSDokter($kodeDokter, $kodeIHS)
    {
        try {
            // dd($kodeDokter, $kodeIHS);
            $httpClient = new Client();
            $request = $httpClient->post(env('SIFA_SATUSEHAT_SERVICE_URL') . '/dokter/ihs/' . $kodeDokter, [
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

    public function getIhsDokterByNIK($kode, $nik)
    {
        $params = [
            'identifier' => $nik,
        ];
        $token = AccessToken::token();
        $url = ConfigSatuSehat::setUrl() . 'Practitioner';
        if (isset($params['identifier'])) {
            $params['identifier'] = 'https://fhir.kemkes.go.id/id/nik|' . $params['identifier'];
        }
        if (isset($params['name'])) {
            $params['name'] = $params['name'];
        }
        if (isset($params['birthdate'])) {
            $params['birthdate'] = $params['birthdate'];
        }
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        $httpClient = new Client();
        $response = $httpClient->get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);
        $data = $response->getBody()->getContents();
        $practitionerSatuSehat = json_decode($data, true);

        if (empty($practitionerSatuSehat['entry'])) {
            return;
        }
        $kodeIHS = $practitionerSatuSehat['entry'][0]['resource']['id'];

        $dokter = Dokter::getByKode($kode);

        // jika kode IHS tidak sama dengan IHS dokter registration
        // maka update data baru
        if (env('IS_PROD')) {
            $dokterIHS = $dokter['ihs'];
        } else {
            $dokterIHS = $dokter['ihs_sanbox'];
        }
        if ($kodeIHS != $dokterIHS) {
            $this->updateIHSDokter($kode, $kodeIHS);
        }

        return $kodeIHS;
    }

    // END DOKTER ===============================================================

    public function updateIHSPasien($norm, $kodeIHS)
    {
        try {
            $httpClient = new Client();
            $request = $httpClient->post(env('SIFA_SATUSEHAT_SERVICE_URL') . '/pasien/ihs/' . $norm, [
                'headers' => [
                    'X-TOKEN' => env('SIFA_SATUSEHAT_SERVICE_TOKEN'),
                ],
                'body' => json_encode([
                    'kodeIHS' => $kodeIHS,
                ]),
            ]);
            $response = $request->getBody()->getContents();
            $statusCode = $request->getStatusCode();

            if ($statusCode != 200) {
                throw new \Exception("Failed to update IHS: " . $statusCode);
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            // Tangani kesalahan
            return []; // Mengembalikan array kosong jika terjadi kesalahan
        }
    }
    public function getIhsPasienByNIK($nik)
    {
        $patient = PatientService::getRequest('Patient', ['identifier' => $nik]);

        if (empty($patient['entry'])) {
            return;
        }
        $ihs = $patient['entry'][0]['resource']['id'];

        $pasien = Pasien::getByNik($nik);

        if (env('IS_PROD')) {
            $pasienIHS = $pasien['ihs'];
        } else {
            $pasienIHS = $pasien['ihs_sanbox'];
        }
        if ($ihs != $pasienIHS) {
            $this->updateIHSPasien($pasien['no_mr'], $ihs);
        }

        return $ihs;
    }

    public function kirim($noReg)
    {
        $registration = RegistrationService::getByKodeReg($noReg);
        // if (!empty($registration['ss_encounter_id'])) {
        //     $errorMessage = 'sudah pernah mengirim encounter';
        //     return redirect()->back()->with('error', $errorMessage);
        // }
        // dd($registration);

        //  CEK PASIEN
        if (empty($registration['nik'])) {
            $errorMessage = 'nik pasien is not available.';
            return $this->emit('error', $errorMessage);
        }
        $nik_pasien = $registration['nik'];
        $nama_pasien = $registration['nama_pasien'];
        $ihs_pasien = $this->getIhsPasienByNIK($registration['nik']);
        if (empty($ihs_pasien)) {
            $errorMessage = 'The patient has not been registered in SatuSehat';
            return $this->emit('error', $errorMessage);
        }

        //   CEK DOKTER
        if (empty($registration['nik_dokter'])) {
            $errorMessage = 'Sorry, NIK DOKTER TIDAK ADA';
            return $this->emit('error', $errorMessage);
        }
        $kode_dokter = $registration['kode_dokter'];
        $nik_dokter = $registration['nik_dokter'];
        $nama_dokter = $registration['nama_dokter'];
        $ihs_dokter = $this->getIhsDokterByNIK($kode_dokter, $registration['nik_dokter']);
        if (empty($ihs_dokter)) {
            $errorMessage = 'IThe practitioner has not been registered in SatuSehat';
            return $this->emit('error', $errorMessage);
        }

        // CEK LOKASI
        $location = Location::where('identifier_value', $registration['RoomCode'])
            ->orWhere('identifier_value', $registration['RoomID'])
            ->orWhere('identifier_value', $registration['ServiceUnitID'])
            ->first();

        if (empty($location)) {
            $errorMessage = 'ID location is not available.';
            return $this->emit('error', $errorMessage);
        }
        $location_id = $location->location_id;
        $location_name = $location->name;
        $organization_id = $location->organization_id;

        $body = [
            'kodeReg' => $noReg,
            'status' => 'arrived',
            'patientId' => $ihs_pasien,
            'patientName' => $nama_pasien,
            'practitionerIhs' => $ihs_dokter,
            'practitionerName' => $nama_dokter,
            'organizationId' => $organization_id,
            'locationId' => $location_id,
            'locationName' => $location_name,
            'statusHistory' => 'arrived',
            'RegistrationDateTime' => $registration['RegistrationDateTime'],
            'DischargeDateTime' => $registration['DischargeDateTime'],
            'diagnosas' => $registration['diagnosas'],
        ];

        try {
            // send API

            $resultApi = EncounterService::PostEncounterCondition($body);
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

            RegistrationService::updateEncounterId($noReg, $encounterID);
            $this->fetchData($this->tanggal);
            $message = 'Bundle Encounter data has been created successfully.';
            return $this->emit('success', $message);
        } catch (\Throwable $e) {
            dd($e->getMessage());
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
            dd('maintance');
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
                if (!empty($registration['RoomCode'])) {
                    $location = Location::where('RoomCode', $registration['RoomCode'])->first();
                } else if (!empty($registration['RoomID'])) {
                    $location = Location::where('RoomID', $registration['RoomID'])->first();
                } elseif (!empty($registration['ServiceUnitID'])) {
                    $location = Location::where('ServiceUnitID', $registration['ServiceUnitID'])->first();
                } else {
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