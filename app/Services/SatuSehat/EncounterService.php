<?php

namespace App\Services\SatuSehat;

use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class EncounterService
{
    protected $httpClient;
    protected $accessToken;
    protected $config;

    public function __construct()
    {
        $this->httpClient = new Client();
        $this->accessToken = new AccessToken();
        $this->config = new ConfigSatuSehat();
    }

    protected function bodyPost(array $body)
    {
        $waktuWIB = date('Y-m-d\TH:i:sP', time());
        $dateTimeWIB = new DateTime($waktuWIB);
        $dateTimeWIB->modify("-7 hours");
        $waktuUTC = $dateTimeWIB->format('Y-m-d\TH:i:sP');

        $data = [
            "resourceType" => "Encounter",
            "status" => $body['status'],
            "class" => [
                "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                "code" => "AMB",
                "display" => "ambulatory",
            ],
            "subject" => [
                "reference" => "Patient/" . $body['patientId'],
                "display" => $body['patientName'],
            ],
            "participant" => [
                [
                    "type" => [
                        [
                            "coding" => [
                                [
                                    "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                    "code" => "ATND",
                                    "display" => "attender",
                                ],
                            ],
                        ],
                    ],
                    "individual" => [
                        "reference" => "Practitioner/" . $body['practitionerIhs'],
                        "display" => $body['practitionerName'],
                    ],
                ],
            ],
            "period" => [
                "start" => $waktuUTC,
            ],
            "location" => [
                [
                    "location" => [
                        "reference" => "Location/" . $body['locationId'],
                        "display" => $body['locationName'],
                    ],
                ],
            ],
            "statusHistory" => [
                [
                    "status" => $body['statusHistory'],
                    "period" => [
                        "start" => $waktuUTC,
                    ],
                ],
            ],
            "serviceProvider" => [
                "reference" => "Organization/" . $this->config->setOrganizationId(),
            ],
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/encounter/" . $this->config->setOrganizationId(),
                    "value" => $body['kodeReg'],
                ],
            ],
        ];

        return $data;
    }

    protected function bodyPatch(array $body)
    {
    }

    protected function processParams($param)
    {
    }

    public function getRequest($endpoint, $params = [])
    {
    }

    public function postRequest($endpoint, array $body)
    {
        $token = $this->accessToken->token();

        $url = $this->config->setUrl() . $endpoint;

        $bodyRaw = $this->bodyPost($body);

        $response = $this->httpClient->post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => $bodyRaw,
        ]);

        $data = $response->getBody()->getContents();
        return json_decode($data, true);
    }
    protected static function bodyPostEncounterCondition(array $body)
    {
        $waktuWIB = date('Y-m-d\TH:i:sP', time());
        $dateTimeWIB = new DateTime($waktuWIB);
        $dateTimeWIB->modify("-7 hours");
        $waktuUTC = $dateTimeWIB->format('Y-m-d\TH:i:sP');

        $uuidEncounter = Str::uuid();

        // diagnosis
        $diagnosis = [];
        $diagnosis_data = [];
        if (!empty($body['diagnosas'])) {
            // dd($body['diagnosas']);
            foreach ($body['diagnosas'] as $indexDiagnosa => $diagnosa) {
                $item_data = [
                    'uuid' => strval(Str::uuid()),
                    "code" => $diagnosa['pdiag_diagnosa'] ? $diagnosa['pdiag_diagnosa'] : '-',
                    'name' => '-',
                ];
                $diagnosis_data[] = $item_data;
                $item = [
                    "condition" => [
                        "reference" => "urn:uuid:" . $item_data['uuid'],
                        "display" => $diagnosa['pdiag_diagnosa'] ? $diagnosa['pdiag_diagnosa'] : '-',
                    ],
                    "use" => [
                        "coding" => [
                            [
                                "system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
                                "code" => 'DD',
                                "display" => "Discharge diagnosis",
                            ],
                        ],
                    ],
                    "rank" => $indexDiagnosa + 1,
                ];
                $diagnosis[] = $item;

            }
        }

        $encounter = [
            "fullUrl" => "urn:uuid:" . $uuidEncounter,
            "resource" => [
                "resourceType" => "Encounter",
                "status" => "finished",
                "class" => [
                    "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                    "code" => "AMB",
                    "display" => "ambulatory",
                ],
                "subject" => [
                    "reference" => "Patient/" . $body['patientId'],
                    "display" => $body['patientName'],
                ],
                "participant" => [
                    [
                        "type" => [
                            [
                                "coding" => [
                                    [
                                        "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                        "code" => "ATND",
                                        "display" => "attender",
                                    ],
                                ],
                            ],
                        ],
                        "individual" => [
                            "reference" => "Practitioner/" . $body['practitionerIhs'],
                            "display" => $body['practitionerName'],
                        ],
                    ],
                ],
                "period" => [
                    "start" => Carbon::createFromFormat('Y-m-d H:i:s.u', $body['RegistrationDateTime'])->setTimezone('UTC')->toIso8601String(),
                    "end" => Carbon::createFromFormat('Y-m-d H:i:s.u', $body['DischargeDateTime'])->setTimezone('UTC')->toIso8601String(),
                ],
                "location" => [
                    [
                        "location" => [
                            "reference" => "Location/" . $body['locationId'],
                            "display" => $body['locationName'] ? $body['locationName'] : '-',
                        ],
                    ],
                ],

                "diagnosis" => $diagnosis,
                "statusHistory" => [
                    [
                        "status" => "arrived",
                        "period" => [
                            "start" => Carbon::createFromFormat('Y-m-d H:i:s.u', $body['RegistrationDateTime'])->setTimezone('UTC')->toIso8601String(),
                            "end" => Carbon::createFromFormat('Y-m-d H:i:s.u', $body['RegistrationDateTime'])->setTimezone('UTC')->toIso8601String(),
                        ],
                    ],
                    [
                        "status" => "in-progress",
                        "period" => [
                            "start" => Carbon::createFromFormat('Y-m-d H:i:s.u', $body['RegistrationDateTime'])->setTimezone('UTC')->toIso8601String(),
                            "end" => Carbon::createFromFormat('Y-m-d H:i:s.u', $body['RegistrationDateTime'])->setTimezone('UTC')->toIso8601String(),
                        ],
                    ],
                    [
                        "status" => "finished",
                        "period" => [
                            "start" => Carbon::createFromFormat('Y-m-d H:i:s.u', $body['DischargeDateTime'])->setTimezone('UTC')->toIso8601String(),
                            "end" => Carbon::createFromFormat('Y-m-d H:i:s.u', $body['DischargeDateTime'])->setTimezone('UTC')->toIso8601String(),
                        ],
                    ],
                ],
                "serviceProvider" => [
                    "reference" => "Organization/" . env('SATU_SEHAT_ORGANIZATION_ID'),
                ],
                "identifier" => [
                    [
                        "system" => "http://sys-ids.kemkes.go.id/encounter/" . env('SATU_SEHAT_ORGANIZATION_ID'),
                        "value" => $body['kodeReg'],
                    ],
                ],
            ],
            "request" => [
                "method" => "POST",
                "url" => "Encounter",
            ],
        ];

        // conditions
        $conditions = [];
        if (!empty($diagnosis)) {
            foreach ($diagnosis_data as $diagnosisItem) {
                // dd($diagnosisItem);
                $condition = [
                    // "fullUrl" => "urn:uuid:" . substr($diagnosisItem['data']['condition']['reference'], strlen("urn:uuid:")),
                    "fullUrl" => "urn:uuid:" . $diagnosisItem['uuid'],
                    "resource" => [
                        "resourceType" => "Condition",
                        "clinicalStatus" => [
                            "coding" => [
                                [
                                    "system" => "http://terminology.hl7.org/CodeSystem/condition-clinical",
                                    "code" => "active",
                                    "display" => "Active",
                                ],
                            ],
                        ],
                        "category" => [
                            [
                                "coding" => [
                                    [
                                        "system" => "http://terminology.hl7.org/CodeSystem/condition-category",
                                        "code" => "encounter-diagnosis",
                                        "display" => "Encounter Diagnosis",
                                    ],
                                ],
                            ],
                        ],
                        "code" => [
                            "coding" => [
                                [
                                    "system" => "http://hl7.org/fhir/sid/icd-10",
                                    "code" => $diagnosisItem['code'],
                                    "display" => $diagnosisItem['name'],
                                ],
                            ],
                        ],
                        "subject" => [
                            "reference" => "Patient/" . $body['patientId'],
                            "display" => $body['patientName'],
                        ],
                        "encounter" => [
                            "reference" => "urn:uuid:" . $uuidEncounter,
                            "display" => "Kunjungan " . $body['patientName'],
                        ],
                    ],
                    "request" => [
                        "method" => "POST",
                        "url" => "Condition",
                    ],
                ];
                $conditions[] = $condition;
            }
        }

        // $observationNadi = [];
        if (!empty($body['observationNadi'])) {
            $observationNadi =
                [
                "fullUrl" => "urn:uuid:" . Str::uuid(),
                "resource" => [
                    "resourceType" => "Observation",
                    "status" => "final",
                    "category" => [
                        [
                            "coding" => [
                                [
                                    "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                    "code" => "vital-signs",
                                    "display" => "Vital Signs",
                                ],
                            ],
                        ],
                    ],
                    "code" => [
                        "coding" => [
                            [
                                "system" => "http://loinc.org",
                                "code" => "8867-4",
                                "display" => "Heart rate",
                            ],
                        ],
                    ],
                    "subject" => [
                        "reference" => "Patient/" . $body['patientId'],
                    ],
                    "performer" => [
                        [
                            "reference" => "Practitioner/" . $body['practitionerIhs'],
                        ],
                    ],
                    "encounter" => [
                        "reference" => "urn:uuid:" . $uuidEncounter,
                        "display" => "Pemeriksaan Fisik Nadi ",
                    ],
                    "effectiveDateTime" => $body['observationNadi']['date'] ? (DateTime::createFromFormat('Y-m-d H:i:s.u', $body['observationNadi']['date']))->setTimezone(new DateTimeZone('-07:00'))->format('Y-m-d\TH:i:sP') : '',
                    "issued" => $body['observationNadi']['date'] ? (DateTime::createFromFormat('Y-m-d H:i:s.u', $body['observationNadi']['date']))->setTimezone(new DateTimeZone('-07:00'))->format('Y-m-d\TH:i:sP') : '',
                    "valueQuantity" => [
                        "value" => intval($body['observationNadi']['value']),
                        "unit" => "beats/minute",
                        "system" => "http://unitsofmeasure.org",
                        "code" => "/min",
                    ],
                ],
                "request" => [
                    "method" => "POST",
                    "url" => "Observation",
                ],
            ];

        }
        // dd($observationNadi);
        $procedures = [];
        if (!empty($body['procedures'])) {
            foreach ($body['procedures'] as $key => $procedure) {
                $procedures[] =
                    [
                    "fullUrl" => "urn:uuid:" . Str::uuid(),
                    "resource" => [

                        "resourceType" => "Procedure",
                        "status" => "completed",
                        "category" => [
                            "coding" => [
                                [
                                    "system" => "http://snomed.info/sct",
                                    "code" => "103693007",
                                    "display" => "Diagnostic procedure",
                                ],
                            ],
                            "text" => "Diagnostic procedure",
                        ],
                        "code" => [
                            "coding" => [
                                [
                                    "system" => "http://hl7.org/fhir/sid/icd-9-cm",
                                    "code" => $procedure['pprosedur_prosedur'],
                                    "display" => "",
                                ],
                            ],
                        ],
                        "subject" => [
                            "reference" => "Patient/" . $body['patientId'],
                            "display" => $body['patientName'],
                        ],
                        "encounter" => [
                            "reference" => "Encounter/" . $uuidEncounter,
                            "display" => '',
                        ],
                        "performedPeriod" => [
                            "start" => $procedure['created_at'] ? date_format(date_create_from_format('Y-m-d H:i:s.u', $procedure['created_at']), 'Y-m-d\TH:i:sP') : null,
                            "end" => $procedure['created_at'] ? date_format(date_create_from_format('Y-m-d H:i:s.u', $procedure['created_at']), 'Y-m-d\TH:i:sP') : null,
                        ],
                        "performer" => [
                            [
                                "actor" => [
                                    "reference" => "Practitioner/" . $body['practitionerIhs'],
                                    "display" => $body['practitionerName'],
                                ],
                            ],
                        ],
                        "reasonCode" => [
                            [
                                "coding" => [
                                    [
                                        "system" => "http://hl7.org/fhir/sid/icd-10",
                                        "code" => $body['diagnosa_utama']['pdiag_diagnosa'],
                                        "display" => "",
                                    ],
                                ],
                            ],
                        ],
                        "bodySite" => [
                            [
                                "coding" => [
                                    [
                                        "system" => "http://snomed.info/sct",
                                        "code" => "302551006",
                                        "display" => "Entire Thorax",
                                    ],
                                ],
                            ],
                        ],
                        "note" => [
                            [
                                "text" => "",
                            ],
                        ],
                    ],
                    "request" => [
                        "method" => "POST",
                        "url" => "Procedure",
                    ],
                ];
            }
        }

        $data = [
            "resourceType" => "Bundle",
            "type" => "transaction",
            "entry" =>
            array_merge([$encounter, $observationNadi],
                $conditions,
                $procedures
            ),
        ];
        // dd($data);

        return $data;
    }

    public static function PostEncounterCondition(array $body)
    {
        try {
            $token = AccessToken::token();

            $url = ConfigSatuSehat::setUrl();

            $bodyRaw = self::bodyPostEncounterCondition($body);
            // dd($bodyRaw);
            $jsonData = json_encode($bodyRaw, JSON_PRETTY_PRINT);

            $httpClient = new Client(
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                    ],
                    'json' => $bodyRaw,
                ]
            );
            $response = $httpClient->post($url);

            $data = $response->getBody()->getContents();
            return json_decode($data, true);
        } catch (\Throwable $e) {
            dd($e->getMessage());
        }
    }
}
