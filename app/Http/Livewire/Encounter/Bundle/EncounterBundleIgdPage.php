<?php

namespace App\Http\Livewire\Encounter\Bundle;

use App\Services\RS\RegistrationServiceIgdRanap;
use App\Services\SatuSehat\PatientService;
use App\Services\SatuSehat\PracticionerService;
use App\Models\Location;
use GuzzleHttp\Client;

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

    public function updated(){
      $this->fetchData($this->tanggal);
    }

    public function SimpanPasienIHS($nik,$rm){
        // $this->updateIHSPasien($pasien['no_mr'], $ihs);
       
        $patient = PatientService::getRequest('Patient', ['identifier' => $nik]);
        $ihs = $patient['entry'][0]['resource']['id'];

        if(env('IS_PROD')){
            $status = TRUE;
        }else{
            $status = FALSE;
        }

        try {          
            // SERVICE SIMPAN IHS PASIEN
            $httpClient = new Client();
            $request = $httpClient->post(env('BRIDGING_SATUSEHAT_SERVICE_URL') . '/pasien/ihs/' . $rm, [
                'headers' => [
                    'X-TOKEN' => env('BRIDGING_SATUSEHAT_SERVICE_TOKEN'),
                ],
                'body' => json_encode([
                    'kodeIHS' => $ihs,
                    'isProd' => $status
                ]),
            ]);
            $response = $request->getBody()->getContents();
            $statusCode = $request->getStatusCode();

            if ($statusCode != 200) {
                throw new \Exception("Failed to update IHS: " . $statusCode);
            }
       
            if(env('IS_PROD')){
                return $response['data']['ihs'];
            }else{
                return $response['data']['ihs_sanbox'];
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            // Tangani kesalahan
            return []; // Mengembalikan array kosong jika terjadi kesalahan
        }
    }

    public function SimpanDokterIHS($kode_dokter,$nik_dokter){
        // $this->updateIHSPasien($pasien['no_mr'], $ihs);
        //ParamedicIHS,ParamedicIHSsanbox
       
        $Paramedic = PracticionerService::getRequest('Practitioner', ['identifier' => $nik_dokter]);
        $kodeIHSDokter = $Paramedic['entry'][0]['resource']['id'];

        if(env('IS_PROD')){
            $status = TRUE;
        }else{
            $status = FALSE;
        }

        try {          
            // SERVICE SIMPAN IHS Dokter
            $httpClient = new Client();
            $request = $httpClient->post(env('BRIDGING_SATUSEHAT_SERVICE_URL') . '/dokter/ihs/' . $kode_dokter, [
                'headers' => [
                    'X-TOKEN' => env('BRIDGING_SATUSEHAT_SERVICE_TOKEN'),
                ],
                'body' => json_encode([
                    'kodeIHS' => $kodeIHSDokter,
                    'isProd' => $status
                ]),
            ]);
            $response = $request->getBody()->getContents();
            $statusCode = $request->getStatusCode();

            if ($statusCode != 200) {
                throw new \Exception("Failed to update IHS: " . $statusCode);
            }
       
            if(env('IS_PROD')){
                return $response['data']['ParamedicIHS'];
            }else{
                return $response['data']['ParamedicIHSsanbox'];
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            // Tangani kesalahan
            return []; // Mengembalikan array kosong jika terjadi kesalahan
        }
    }

    public function kirimPerTanggal()
    {
        // kirim yang telah discharge dan belum memiliki encounterID
        $tanggal = $this->tanggal;
        try {
            $registrations = RegistrationServiceIgdRanap::getLastDay($tanggal, 0,'IGD');

            
            foreach ($registrations as $registration) {
                // $registration=1;
                $nik = $registration['nik'];
                $nik_dokter = $registration['nik_dokter'];
                $kode_dokter = $registration['kode_dokter'];
                //  CEK NIK PASIEN
          
                // $registration['location_ihs'] - $registration['ihs_pasien'] - $registration['ihs_dokter']
                if (empty($registration['ihs_pasien'])) {
                    $registration['ihs_pasien']=$this->SimpanPasienIHS($nik,$registration['no_mr']);                    
                }
               
                if (empty($registration['ihs_dokter'])) {
                    $registration['ihs_dokter']=$this->SimpanDokterIHS($kode_dokter,$nik_dokter);                    
                }
                
                 if (!empty($registration['ihs_pasien']) && !empty($registration['ihs_dokter'])) {
                    $location = Location::where('identifier_value', $registration['RoomCode'])
                    ->orWhere('identifier_value', $registration['RoomID'])
                    ->orWhere('identifier_value', $registration['ServiceUnitID'])
                    ->first();

                    $location_id = $registration['location_id'];
                    $location_name = $registration['location_name'];
                    $organization_id = $location->organization_id;
                    $noReg = $registration['no_registrasi'];
                    $body = [
                        'kodeReg' => $noReg,
                        'status' => 'arrived',
                        'patientId' => $ihs_pasien,
                        'patientName' => $nama_pasien,
                        'practitionerIhs' => $registration['ihs_dokter'],
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
                        // jika

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
            $this->registrations = RegistrationServiceIgdRanap::getData($this->tanggal, $this->page,'IGD');
            
            // dd($this->registrations);
        } catch (\Exception $e) {
            // Tangani kesalahan
            return response()->json(['error' => 'Failed to fetch data'], 500);
            // return view('error-view', ['error' => 'Failed to fetch data']);
        }
    }
}
