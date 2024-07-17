<?php

namespace App\Console\Commands;

use App\Models\Location;
use App\Models\LogEncounter;
use App\Services\RS\RegistrationService;
use App\Services\SatuSehat\EncounterService;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class EncounterRajalDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encounter:rajal-day';

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
        // $tanggal = date('2024-06-29');
        $tanggal = date('Y-m-d');
        // hari = 1 (1 hari terakhir / kemarin)
        $hari = 1;
        try {
            $registrations = RegistrationService::getLastDay($tanggal, $hari);

            // pengiriman data
            $regArrs = array_chunk($registrations, 5);
            // dd($regArrs);
            foreach ($regArrs as $regArr) {
                foreach ($regArr as $registration) {
                    // dd($registration);
                    $noReg = $registration['no_registrasi'];
                    // $log_cek = LogEncounter::where('noreg', $noReg)->first();
                    $status = null;
                    $jenis = 'rajal';
                    // dd($log_cek);
                    // $registration = RegistrationService::getByKodeReg($noReg);

                    // if (!empty($registration['ss_encounter_id'])) {
                    //     $errorMessage = 'sudah pernah mengirim encounter';
                    //     return redirect()->back()->with('error', $errorMessage);
                    // }
                    if (env('IS_PROD')) {
                        $encounterId = $registration['ss_encounter_id'];
                    } else {
                        $encounterId = $registration['ss_encounter_id_sanbox'];
                    }

                    if (empty($encounterId)) {
                        // dd($encounterId);
                        //  CEK PASIEN
                        if (!empty($registration['nik'])) {

                            $nik_pasien = $registration['nik'];
                            $nama_pasien = $registration['nama_pasien'];

                            // dd(strlen($nik_pasien));
                            if (strlen($nik_pasien) == 16) {
                                $ihs_pasien = $this->getIhsPasienByNIK($registration['nik']);
                                if (!empty($ihs_pasien)) {
                                    //   CEK DOKTER
                                    if (!empty($registration['nik_dokter'])) {
                                        $kode_dokter = $registration['kode_dokter'];
                                        $nik_dokter = $registration['nik_dokter'];
                                        $nama_dokter = $registration['nama_dokter'];
                                        $ihs_dokter = $this->getIhsDokterByNIK($kode_dokter, $registration['nik_dokter']);
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

                                                // mencari diagnosa utama

                                                $diagnosaUtama = null;
                                                foreach ($registration['diagnosas'] as $diagnosa) {
                                                    if ($diagnosa['pdiag_tipe'] == 'UTAMA') {
                                                        $diagnosaUtama = $diagnosa;
                                                    }
                                                };

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
                                                    'diagnosa_utama' => $diagnosaUtama ? $diagnosaUtama : null,
                                                    'observationNadi' => $registration['observationNadi'],
                                                    'procedures' => $registration['procedures'],
                                                ];
                                                // dd($body);
                                                $resultApi = EncounterService::PostEncounterCondition($body);
                                                if (!empty($resultApi['entry'][0]['response']['resourceID'])) {
                                                    $encounterID = $resultApi['entry'][0]['response']['resourceID'];
                                                } else {
                                                    $url = $resultApi['entry'][0]['response']['location'];
                                                    $uuid = explode('/', parse_url($url, PHP_URL_PATH))[4];
                                                    $encounterID = $uuid;
                                                }
                                                // dd($encounterID);

                                                if ($encounterID) {
                                                    //    $cek =  RegistrationService::updateEncounterId($noReg, $encounterID);
                                                    //    dd($cek);
                                                    if (env('IS_PROD') == false) {
                                                        $status = 0;
                                                    } else {
                                                        $status = 1;
                                                    }

                                                    // try {
                                                    // dd($kodeDokter, $kodeIHS);
                                                    $httpClient = new Client([
                                                        'headers' => [
                                                            'Content-Type' => 'application/json',
                                                            'X-TOKEN' => env('BRIDGING_SATUSEHAT_SERVICE_TOKEN'),
                                                        ],
                                                        'body' => json_encode([
                                                            'noreg' => $noReg,
                                                            'encounter_id' => $encounterID,
                                                            'isProd' => $status,
                                                        ]),
                                                    ]);

                                                    $request = $httpClient->post(env('BRIDGING_SATUSEHAT_SERVICE_URL') . '/registration/update/encounterid');
                                                    $response = $request->getBody()->getContents();

                                                    $statusCode = $request->getStatusCode();
                                                    // dd($statusCode);
                                                    if ($statusCode == 200) {
                                                        $result = json_decode($response, true);
                                                        // dd($result);
                                                        // return $result;
                                                    }
                                                    // return null;
                                                    // else {
                                                    //     // Tangani kesalahan jika status bukan 200 OK
                                                    //     // Misalnya, lempar Exception dengan pesan kesalahan yang sesuai
                                                    //     throw new \Exception("Failed to update IHS: " . $statusCode);
                                                    // }
                                                    // } catch (\Exception $e) {
                                                    //     dd($e->getMessage());
                                                    //     // Tangani kesalahan
                                                    //     return null; // Mengembalikan array kosong jika terjadi kesalahan
                                                    // }

                                                }
                                            } else {
                                                $log_cek = LogEncounter::where('noreg', $noReg)->first();
                                                $errorMessage = 'ID location is not available.';
                                                if ($log_cek) {
                                                    $log_cek->status = $errorMessage;
                                                    $log_cek->updated_by = auth()->user()->id;
                                                    $log_cek->save();
                                                } else {
                                                    $log = new LogEncounter();
                                                    $log->jenis = $jenis;
                                                    $log->noreg = $noReg;
                                                    $log->status = $errorMessage;
                                                    $log->updated_by = auth()->user()->id;
                                                    $log->save();
                                                }
                                            }
                                        } else {
                                            $log_cek = LogEncounter::where('noreg', $noReg)->first();
                                            $errorMessage = 'The practitioner has not been registered in SatuSehat';
                                            if ($log_cek) {
                                                $log_cek->status = $errorMessage;
                                                $log_cek->updated_by = auth()->user()->id;
                                                $log_cek->save();

                                            } else {
                                                $log = new LogEncounter();
                                                $log->jenis = $jenis;
                                                $log->noreg = $noReg;
                                                $log->status = $status;
                                                $log->updated_by = auth()->user()->id;
                                                $log->save();

                                            }
                                        }
                                    } else {
                                        $log_cek = LogEncounter::where('noreg', $noReg)->first();
                                        $errorMessage = 'Sorry, NIK DOKTER TIDAK ADA';
                                        if ($log_cek) {
                                            $log_cek->status = $errorMessage;
                                            $log_cek->updated_by = auth()->user()->id;
                                            $log_cek->save();

                                        } else {
                                            $log = new LogEncounter();
                                            $log->jenis = $jenis;
                                            $log->noreg = $noReg;
                                            $log->status = $status;
                                            $log->updated_by = auth()->user()->id;
                                            $log->save();

                                        }
                                    }
                                } else {
                                    $ihs_pasien = $this->getIhsPasienByNIK($registration['nik']);
                                    if (empty($ihs_pasien)) {
                                        $log_cek = LogEncounter::where('noreg', $noReg)->first();
                                        $errorMessage = 'The patient has not been registered in SatuSehat';
                                        if ($log_cek) {
                                            $log_cek->status = $errorMessage;
                                            $log_cek->updated_by = auth()->user()->id;
                                            $log_cek->save();
                                        } else {
                                            $log = new LogEncounter();
                                            $log->jenis = $jenis;
                                            $log->noreg = $noReg;
                                            $log->status = $errorMessage;
                                            $log->updated_by = auth()->user()->id;
                                            $log->save();
                                        }
                                    }
                                }
                            } else {
                                $log_cek = LogEncounter::where('noreg', $noReg)->first();
                                $errorMessage = 'The patient has invalid in SatuSehat';
                                if ($log_cek) {
                                    $log_cek->status = $errorMessage;
                                    $log_cek->updated_by = auth()->user()->id;
                                    $log_cek->save();

                                } else {
                                    $log = new LogEncounter();
                                    $log->jenis = $jenis;
                                    $log->noreg = $noReg;
                                    $log->status = $errorMessage;
                                    $log->updated_by = auth()->user()->id;
                                    $log->save();

                                }
                            }
                        } else {
                            $log_cek = LogEncounter::where('noreg', $noReg)->first();
                            $errorMessage = 'nik pasien is not available.';
                            if ($log_cek) {
                                $log_cek->status = $errorMessage;
                                $log_cek->updated_by = auth()->user()->id;
                                $log_cek->save();
                            } else {
                                $log = new LogEncounter();
                                $log->jenis = $jenis;
                                $log->noreg = $noReg;
                                $log->status = $errorMessage;
                                $log->updated_by = auth()->user()->id;
                                $log->save();
                            }
                        }
                    }
                }
            }
            $this->comment('ok');
            // $message = 'Bundle Encounter data has been created successfully.';
            // return $this->emit('success', $message);
        } catch (\Exception $e) {
            $this->comment('failed' . $e->getMessage());
            // dd($e);
            // Tangani kesalahan
            // return response()->json(['error' => 'Failed to fetch data'], 500);
            // return view('error-view', ['error' => 'Failed to fetch data']);
        }

        return Command::SUCCESS;

    }
}
