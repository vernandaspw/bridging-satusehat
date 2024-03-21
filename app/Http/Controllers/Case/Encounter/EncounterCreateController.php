<?php

namespace App\Http\Controllers\Case\Encounter;

use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\Encounter;
use App\Models\Location;
use App\Models\Mapping\MappingEncounter;
use App\Models\Pasien;
use App\Services\SatuSehat\EncounterService;
use App\Services\SatuSehat\PatientService;

class EncounterCreateController extends Controller
{
    protected $pendaftaran;
    protected $patientService;
    protected $mappingEncounter;
    protected $encounterService;

    public function __construct()
    {
        $this->pendaftaran = new Pendaftaran();
        $this->patientService = new PatientService();
        $this->mappingEncounter = new MappingEncounter();
        $this->encounterService = new EncounterService();
    }
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $noReg = $request->noreg;

        // fetch detail by no reg
        $detailPendaftaran = $this->pendaftaran->getByKodeReg($noReg);
        // dd($detailPendaftaran);
        if(!empty($detailPendaftaran['ss_encounter_id'])){
            $errorMessage = 'sudah pernah mengirim encounter';
            return redirect()->back()->with('error', $errorMessage);
        }
        //   check nik and kode dokter
        if (empty($detailPendaftaran['nik'])) {
            $errorMessage = 'nik data is not available.';
            return redirect()->back()->with('error', $errorMessage);
        }

        if (empty($detailPendaftaran['kode_dokter'])) {
            $errorMessage = 'Sorry, the patient you are looking for is not registered with any doctor or the search results are ambiguous.';
            return redirect()->back()->with('error', $errorMessage);
        }
        // get nik & kode dokter
        $nik = $detailPendaftaran['nik'];
        $kodeDokter = $detailPendaftaran['kode_dokter'];
        // fetch pasien API by nik
        $patient =  $this->patientService->getRequest('Patient', ['identifier' => $nik]);
        if (empty($patient['entry'])) {
            $errorMessage = 'The patient has not been registered in SatuSehat.';
            return redirect()->back()->with('error', $errorMessage);
        }
        // get name and ihs number/id
        $patientId = $patient['entry'][0]['resource']['id'];
        $patientName = $patient['entry'][0]['resource']['name'][0]['text'];

        // fetch mapping encounter by kode dokter

        // CEK IHS DOKTER
        $dokter = Dokter::getByKode($kodeDokter);
        if (empty($dokter['ihs'])) {
            $errorMessage = 'IHS Dokter is not available.';
            return redirect()->back()->with('error', $errorMessage);
        }

        // CEK IHS PASIEN
        $pasien = Pasien::getByNik($nik);

        if (empty($pasien['ihs'])) {
            $errorMessage = 'IHS pasien is not available.';
            return redirect()->back()->with('error', $errorMessage);
        }
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
            if(empty($encounterID)){
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

        //    redirect view
        // return view('pages.encounter.create', compact('result'));
    }
}
