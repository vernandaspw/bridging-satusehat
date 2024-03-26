<?php

namespace App\Services\SatuSehat;

use Carbon\Carbon;
use DateTime;
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
        if (!empty($body['diagnosas'])) {
            foreach ($body['diagnosas'] as $indexDiagnosa => $diagnosa) {
                $diagnosis = [
                    [
                        "condition" => [
                            "reference" => "urn:uuid:" . Str::uuid(),
                            "display" => "",
                        ],
                        "use" => [
                            "coding" => [
                                [
                                    "system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
                                    "code" => $diagnosa['pdiag_diagnosa'],
                                    "display" => "Discharge diagnosis",
                                ],
                            ],
                        ],
                        "rank" => $indexDiagnosa + 1,
                    ],
                ];
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
                            "display" => $body['locationName'],
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
                        "value" => $body['patientId'],
                    ],
                ],
            ],
            "request" => [
                "method" => "POST",
                "url" => "Encounter",
            ],
        ];

        // conditions
        if (!empty($diagnosis)) {
            foreach ($diagnosis as $indexDiagnosisItem => $diagnosisItem) {
                // dd($diagnosisItem);
                $condition = [
                    "fullUrl" => "urn:uuid:" . substr($diagnosisItem['condition']['reference'], strlen("urn:uuid:")),
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
                                    "code" => $diagnosisItem['use']['coding'][0]['code'],
                                    "display" => $diagnosisItem['use']['coding'][0]['display'],
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
            }
        }

        $data = [
            "resourceType" => "Bundle",
            "type" => "transaction",
            "entry" => [
                $encounter,
                $condition,
            ],
        ];

        return $data;
    }

    public static function PostEncounterCondition(array $body)
    {
        $token = AccessToken::token();

        $url = ConfigSatuSehat::setUrl();

        $bodyRaw = self::bodyPostEncounterCondition($body);
        $jsonData = json_encode($bodyRaw, JSON_PRETTY_PRINT);
        // dd($jsonData);

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
    }
}
