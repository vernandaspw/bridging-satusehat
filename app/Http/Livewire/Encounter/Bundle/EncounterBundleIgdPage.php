<?php

namespace App\Http\Livewire\Encounter\Bundle;

use App\Services\RS\RegistrationServiceIgdRanap;
use App\Services\SatuSehat\PatientService;
use App\Services\SatuSehat\PracticionerService;
use App\Services\SatuSehat\EncounterService;
use App\Services\RS\RegistrationService;
use App\Models\Location;
use GuzzleHttp\Client;
use App\Services\SatuSehat\AccessToken;
use App\Services\SatuSehat\ConfigSatuSehat;

use Livewire\Component;

class EncounterBundleIgdPage extends Component
{
    public $registrations = [];
    public $tanggal;
    public $page = 1;

    public function render()
    {
        return view('livewire.encounter.bundle.encounter-bundle-igd-page')->extends('layouts.app')->section('main');
    }

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        $this->fetchData();
    }

    public function page($page)
    {
        $this->page = $page;
        $this->fetchData(null, $page);
        // dd($this->registrations);
    }

    public function updated()
    {
        $this->fetchData($this->tanggal);
    }

    public function SimpanPasienIHS($nik, $rm)
    {
        // $this->updateIHSPasien($pasien['no_mr'], $ihs);

        $patient = PatientService::getRequest('Patient', ['identifier' => $nik]);
     
        if (!empty($patient['entry'])) {       
            $ihs = $patient['entry'][0]['resource']['id'];
            
            if (env('IS_PROD') == false) {
                $status = false;
            } else {
                $status = true;
            }

            try {
                // SERVICE SIMPAN IHS PASIEN
                $httpClient = new Client();
                $res = $httpClient->post(env('BRIDGING_SATUSEHAT_SERVICE_URL') . '/pasien/ihs/' . $rm, [
                    'headers' => [
                        'X-TOKEN' => env('BRIDGING_SATUSEHAT_SERVICE_TOKEN'),
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode([
                        'kodeIHS' => $ihs,
                        'isProd' => $status
                    ]),
                ]);
                // $response = $request->getBody()->getContents();              
                $response = json_decode($res->getBody()->getContents());              
                $statusCode = $res->getStatusCode();
           
                if ($statusCode != 200) {
                    throw new \Exception("Failed to update IHS: " . $statusCode);
                }

                if ($status) {
                    return $response->data->ihs;
                } else {
                    return $response->data->ihs_sanbox;
                }
            } catch (\Exception $e) {
                dd($e->getMessage());
                // Tangani kesalahan
                return []; // Mengembalikan array kosong jika terjadi kesalahan
            }
        } else {
            return "0";
        }
    }

    public function SimpanDokterIHS($kode_dokter, $nik_dokter)
    {
        // $this->updateIHSPasien($pasien['no_mr'], $ihs);
        //ParamedicIHS,ParamedicIHSsanbox
        $params = [
            'identifier' => $nik_dokter,
        ];
        $token = AccessToken::token();
        $url = ConfigSatuSehat::setUrl() . 'Practitioner';
        if (isset($params['identifier'])) {
            $params['identifier'] = 'https://fhir.kemkes.go.id/id/nik|' . $params['identifier'];
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
        $Paramedic = json_decode($data, true);
        if (!empty($Paramedic['entry'])) {
            $kodeIHSDokter = $Paramedic['entry'][0]['resource']['id'];
            // dd($kodeIHSDokter);
            if (env('IS_PROD') == false) {
                $status = false;
            } else {
                $status = true;
            }
           
            try {
                // SERVICE SIMPAN IHS Dokter
                $httpClient = new Client();
                $request = $httpClient->post(env('BRIDGING_SATUSEHAT_SERVICE_URL') . '/dokter/ihs/' . $kode_dokter, [
                    'headers' => [
                        'X-TOKEN' => env('BRIDGING_SATUSEHAT_SERVICE_TOKEN'),
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode([
                        'kodeIHS' => $kodeIHSDokter,
                        'isProd' => $status
                    ]),
                ]);
                $response = json_decode($request->getBody()->getContents());  
                $statusCode = $request->getStatusCode();
                // dd($response);
                if ($statusCode != 200) {
                    throw new \Exception("Failed to update IHS: " . $statusCode);
                }

                if ($status) {
                    return $response->data->ihs;
                } else {
                    return $response->data->ihs_sanbox;
                }
            } catch (\Exception $e) {
                dd($e->getMessage());
                // Tangani kesalahan
                return []; // Mengembalikan array kosong jika terjadi kesalahan
            }
        } else {
            return "0";
        }
    }

    public function kirimPerTanggal()
    {
        // kirim yang telah discharge dan belum memiliki encounterID
        $tanggal = $this->tanggal;
        try {
            $registrations = RegistrationServiceIgdRanap::getLastDay($tanggal, 0, 'IGD');

            foreach ($registrations as $registration) {
                // $registration=1;
                $nik = $registration['nik'];
                $nik_dokter = $registration['nik_dokter'];
                $kode_dokter = $registration['kode_dokter'];
                //  CEK NIK PASIEN

                // $registration['location_ihs'] - $registration['ihs_pasien'] - $registration['ihs_dokter']
                if (empty($registration['ihs_pasien'])) {
                    if (!empty($nik)) {
                        if ($this->SimpanPasienIHS($nik, $registration['no_mr']) != "0") {
                            $registration['ihs_pasien'] = $this->SimpanPasienIHS($nik, $registration['no_mr']);
                        }
                    }
                }

                if (empty($registration['ihs_dokter'])) {
                    if (!empty($nik_dokter)) {
                        if ($this->SimpanDokterIHS($kode_dokter, $nik_dokter) != "0") {
                            $registration['ihs_dokter'] = $this->SimpanDokterIHS($kode_dokter, $nik_dokter);
                        }
                    }
                }

                if (!empty($registration['ihs_pasien']) && !empty($registration['ihs_dokter']) && !empty($registration['location_ihs'])) {
                   
                  
                    $location = Location::where('location_id', $registration['location_ihs'])->first();
                   
                    $location_id = $registration['location_ihs'];
                    $location_name = $location->name;
                    $organization_id = $location->organization_id;
                    $noReg = $registration['no_registrasi'];
                   
                    $body = [
                        'kodeReg' => $noReg,
                        'status' => 'arrived',
                        'patientId' => $registration['ihs_pasien'],
                        'patientName' => $registration['nama_pasien'],
                        'practitionerIhs' => $registration['ihs_dokter'],
                        'practitionerName' => $registration['nama_dokter'],
                        'organizationId' => $organization_id,
                        'locationId' => $location_id,
                        'locationName' => $location_name,
                        'statusHistory' => 'arrived',
                        'RegistrationDateTime' => $registration['RegistrationDateTime'],
                        'DischargeDateTime' => $registration['DischargeDateTime'],
                        'diagnosas' => $registration['diagnosas'],
                    ];
                    try {                        

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

                    } catch (\Throwable $e) {
                        dd($e->getMessage());
                        $errorMessage = 'Coba ulang ' . $e->getMessage();
                        return $this->emit('error', $errorMessage);
                    }
                }
            }
            $message = 'Bundle Encounter data has been created successfully.';
            return $this->emit('success', $message);
        } catch (\Exception $e) {
            dd($e);
            // Tangani kesalahan
            return response()->json(['error' => 'Failed to fetch data'], 500);
            // return view('error-view', ['error' => 'Failed to fetch data']);
        }
    }


    public function tanggal()
    {
        $this->fetchData($this->tanggal);
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
            $this->registrations = RegistrationServiceIgdRanap::getData($this->tanggal, $this->page, 'IGD');

            // dd($this->registrations);
        } catch (\Exception $e) {
            // Tangani kesalahan
            return response()->json(['error' => 'Failed to fetch data'], 500);
            // return view('error-view', ['error' => 'Failed to fetch data']);
        }
    }
}
