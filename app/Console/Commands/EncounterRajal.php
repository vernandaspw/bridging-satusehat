<?php

namespace App\Console\Commands;

use App\Models\Dokter;
use App\Models\Location;
use App\Services\RS\RegistrationService;
use App\Services\SatuSehat\AccessToken;
use App\Services\SatuSehat\ConfigSatuSehat;
use App\Services\SatuSehat\EncounterService;
use App\Services\SatuSehat\PatientService;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class EncounterRajal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encounter:rajal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // kirim data yang tidak memiliki encounterID jika prod dan encounterIDsanbox jika sanbox

        // mengambil data yang tidak memiliki encounterID bersadarkan APP_TIPE
        $tanggal = date('2024-03-25');
        // hari = 1 (1 hari terakhir / kemarin)
        $hari = 0;
        try {
            $registrations = RegistrationService::getLastDay($tanggal, $hari);

            // pengiriman data
            foreach ($registrations as $registration) {
                // dd($registration);
                $nik = $registration['nik'];
                //  CEK NIK PASIEN
                if (!empty($nik)) {
                    $patient = PatientService::getRequest('Patient', ['identifier' => $nik]);

                    if (!empty($patient['entry'])) {

                        $ihs = $patient['entry'][0]['resource']['id'];

                        $headers = [
                            'X-TOKEN' => env('BRIDGING_SATUSEHAT_SERVICE_TOKEN'),
                        ];
                        $request = Http::withHeaders($headers)->get(env('BRIDGING_SATUSEHAT_SERVICE_URL') . '/pasien/detail/' . $nik);
                        $response = $request->getBody()->getContents();
                        $pasienData = json_decode($response, true);
                        $pasien = $pasienData['data'];
                        if (env('IS_PROD')) {
                            $pasienIHS = $pasien['ihs'];
                        } else {
                            $pasienIHS = $pasien['ihs_sanbox'];
                        }
                        if ($ihs != $pasienIHS) {
                            // $this->updateIHSPasien($pasien['no_mr'], $ihs);
                            try {
                                $httpClient = new Client();
                                $request = $httpClient->post(env('BRIDGING_SATUSEHAT_SERVICE_URL') . '/pasien/ihs/' . $pasien['no_mr'], [
                                    'headers' => [
                                        'X-TOKEN' => env('BRIDGING_SATUSEHAT_SERVICE_TOKEN'),
                                    ],
                                    'body' => json_encode([
                                        'kodeIHS' => $ihs,
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
                            }
                        }
                        $ihs_pasien = $ihs;
                        $nik_pasien = $registration['nik'];
                        $nama_pasien = $registration['nama_pasien'];

                        // CEK IHS DOKTER
                        $nik_dokter = $registration['nik_dokter'];
                        if (!empty($nik_dokter)) {
                            $kodeDokter = $registration['kode_dokter'];
                            $nama_dokter = $registration['nama_dokter'];

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
                            $practitionerSatuSehat = json_decode($data, true);

                            if (!empty($practitionerSatuSehat['entry'])) {
                                $kodeIHSDokter = $practitionerSatuSehat['entry'][0]['resource']['id'];
                                $dokter = Dokter::getByKode($kodeDokter);

                                if (!empty($dokter)) {

                                    // jika kode IHS tidak sama dengan IHS dokter registration
                                    // maka update data baru
                                    if (env('IS_PROD')) {
                                        $dokterIHS = $dokter['ihs'];
                                    } else {
                                        $dokterIHS = $dokter['ihs_sanbox'];
                                    }
                                    if ($kodeIHSDokter != $dokterIHS) {
                                        // $this->updateIHSDokter($kodeDokter, $kodeIHSDokter);
                                        try {
                                            // dd($kodeDokter, $kodeIHS);
                                            $httpClient = new Client();
                                            $request = $httpClient->post(env('BRIDGING_SATUSEHAT_SERVICE_URL') . '/dokter/ihs/' . $kodeDokter, [
                                                'headers' => [
                                                    'X-TOKEN' => env('BRIDGING_SATUSEHAT_SERVICE_TOKEN'),
                                                ],
                                                'body' => json_encode([
                                                    'kodeIHS' => $kodeIHSDokter,
                                                ]),
                                            ]);
                                            $response = $request->getBody()->getContents();
                                            // dd($response);
                                            $statusCode = $request->getStatusCode();

                                            if ($statusCode != 200) {
                                                throw new \Exception("Failed to update IHS: " . $statusCode);
                                            }
                                        } catch (\Exception $e) {
                                            dd($e->getMessage());
                                            // Tangani kesalahan
                                        }
                                    }

                                    $ihs_dokter = $kodeIHSDokter;

                                    if (!empty($ihs_dokter)) {

                                        // CEK LOKASI
                                        $location = Location::where('identifier_value', $registration['RoomCode'])
                                            ->orWhere('identifier_value', $registration['RoomID'])
                                            ->orWhere('identifier_value', $registration['ServiceUnitID'])
                                            ->first();
                                        if (!empty($location)) {
                                            $location_id = $location->location_id;
                                            $location_name = $location->name;
                                            $organization_id = $location->organization_id;
                                            $noReg = $registration['no_registrasi'];
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
                                                // $this->fetchData($this->tanggal);

                                            } catch (\Throwable $e) {
                                                dd($e->getMessage());
                                                $errorMessage = 'Coba ulang ' . $e->getMessage();
                                                return $this->emit('error', $errorMessage);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $this->comment('ok');
            // $message = 'Bundle Encounter data has been created successfully.';
            // return $this->emit('success', $message);
        } catch (\Exception $e) {
            $this->comment('failed'. $e->getMessage());
            dd($e);
            // Tangani kesalahan
            return response()->json(['error' => 'Failed to fetch data'], 500);
            // return view('error-view', ['error' => 'Failed to fetch data']);
        }

        return Command::SUCCESS;

    }
}
