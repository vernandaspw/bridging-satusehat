<?php

namespace App\Services\SatuSehat;

use DateTime;
use GuzzleHttp\Client;

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
    protected function bodyPostEncounterCondition(array $body)
    {
        $waktuWIB = date('Y-m-d\TH:i:sP', time());
        $dateTimeWIB = new DateTime($waktuWIB);
        $dateTimeWIB->modify("-7 hours");
        $waktuUTC = $dateTimeWIB->format('Y-m-d\TH:i:sP');

        $data = [
            // "resourceType" => "Bundle",
            // "type" => "transaction",
            // "entry" => [
            //     [
            //         "fullUrl" => "urn:uuid:98068af1-77da-4cce-ab83-68dca3290e40",
            //         "resource" => [
            //             "resourceType" => "Encounter",
            //             "status" => "finished",
            //             "class" => [
            //                 "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
            //                 "code" => "AMB",
            //                 "display" => "ambulatory",
            //             ],
            //             "subject" => [
            //                 "reference" => "Patient/" .$body['patientId'],
            //                 "display" => $body['patientName'],
            //             ],
            //             "participant" => [
            //                 [
            //                     "type" => [
            //                         [
            //                             "coding" => [
            //                                 [
            //                                     "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
            //                                     "code" => "ATND",
            //                                     "display" => "attender",
            //                                 ],
            //                             ],
            //                         ],
            //                     ],
            //                     "individual" => [
            //                         "reference" => "Practitioner/" . $body['practitionerIhs'],
            //                         "display" => $body['practitionerName'],
            //                     ],
            //                 ],
            //             ],
            //             "period" => [
            //                 "start" => "2024-01-18T14:00:00+00:00",
            //                 "end" => "2024-01-18T16:00:00+00:00",
            //             ],
            //             "location" => [
            //                 [
            //                     "location" => [
            //                         "reference" => "Location" . $body['locationId'],
            //                         "display" => $body['locationName'],
            //                     ],
            //                 ],
            //             ],
            //             "diagnosis" => [
            //                 [
            //                     "condition" => [
            //                         "reference" => "urn:uuid:41871f14-15ca-40ea-bd4f-31c0044753fb",
            //                         "display" => "Acute appendicitis, other and unspecified",
            //                     ],
            //                     "use" => [
            //                         "coding" => [
            //                             [
            //                                 "system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
            //                                 "code" => "DD",
            //                                 "display" => "Discharge diagnosis",
            //                             ],
            //                         ],
            //                     ],
            //                     "rank" => 1,
            //                 ],
            //                 [
            //                     "condition" => [
            //                         "reference" => "urn:uuid:cd9c3136-1bad-4aca-a43a-36e51eb68277",
            //                         "display" => "Dengue haemorrhagic fever",
            //                     ],
            //                     "use" => [
            //                         "coding" => [
            //                             [
            //                                 "system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
            //                                 "code" => "DD",
            //                                 "display" => "Discharge diagnosis",
            //                             ],
            //                         ],
            //                     ],
            //                     "rank" => 2,
            //                 ],
            //             ],
            //             "statusHistory" => [
            //                 [
            //                     "status" => "arrived",
            //                     "period" => [
            //                         "start" => "2022-12-18T14:00:00+00:00",
            //                         "end" => "2022-12-18T15:00:00+00:00",
            //                     ],
            //                 ],
            //                 [
            //                     "status" => "in-progress",
            //                     "period" => [
            //                         "start" => "2022-12-18T15:00:00+00:00",
            //                         "end" => "2022-12-18T16:00:00+00:00",
            //                     ],
            //                 ],
            //                 [
            //                     "status" => "finished",
            //                     "period" => [
            //                         "start" => "2022-12-18T16:00:00+00:00",
            //                         "end" => "2022-12-18T16:00:00+00:00",
            //                     ],
            //                 ],
            //             ],
            //             "serviceProvider" => [
            //                 "reference" => "Organization/9e54ca56-5dd2-47c6-83e5-187ace4023c3",
            //             ],
            //             "identifier" => [
            //                 [
            //                     "system" => "http://sys-ids.kemkes.go.id/encounter/9e54ca56-5dd2-47c6-83e5-187ace4023c3",
            //                     "value" => "P01504896438",
            //                 ],
            //             ],
            //         ],
            //         "request" => [
            //             "method" => "POST",
            //             "url" => "Encounter",
            //         ],
            //     ],
            //     [
            //         "fullUrl"=> "urn:uuid:41871f14-15ca-40ea-bd4f-31c0044753fb",
            //         "resource"=> [
            //             "resourceType"=> "Condition",
            //             "clinicalStatus"=> [
            //                 "coding"=> [
            //                     [
            //                         "system"=> "http://terminology.hl7.org/CodeSystem/condition-clinical",
            //                         "code"=> "active",
            //                         "display"=> "Active"
            //                     ]
            //                 ]
            //                     ],
            //             "category"=> [
            //                 [
            //                     "coding"=> [
            //                         [
            //                             "system"=> "http://terminology.hl7.org/CodeSystem/condition-category",
            //                             "code"=> "encounter-diagnosis",
            //                             "display"=> "Encounter Diagnosis"
            //                         ]
            //                     ]
            //                 ]
            //             ],
            //             "code"=> [
            //                 "coding"=> [
            //                     [
            //                         "system"=> "http://hl7.org/fhir/sid/icd-10",
            //                         "code"=> "K35.8",
            //                         "display"=> "Acute appendicitis, other and unspecified"
            //                     ]
            //                 ]
            //                     ],
            //             "subject"=> [
            //                 "reference"=> "Patient/P01504896438",
            //                 "display"=> "FARMA WARDANA"
            //             ],
            //             "encounter"=> [
            //                 "reference"=> "urn:uuid:98068af1-77da-4cce-ab83-68dca3290e40",
            //                 "display"=> "Kunjungan FARMA WARDANA di hari Selasa, 18 Desember 2022"
            //             ]
            //             ],
            //         "request"=> [
            //             "method"=> "POST",
            //             "url"=> "Condition"
            //         ]
            //     ],
            //     [
            //         "fullUrl"=> "urn:uuid:cd9c3136-1bad-4aca-a43a-36e51eb68277",
            //         "resource"=> [
            //             "resourceType"=> "Condition",
            //             "clinicalStatus"=> [
            //                 "coding"=> [
            //                     [
            //                         "system"=> "http://terminology.hl7.org/CodeSystem/condition-clinical",
            //                         "code"=> "active",
            //                         "display"=> "Active"
            //                     ]
            //                 ]
            //                     ],
            //             "category"=> [
            //                 [
            //                     "coding"=> [
            //                         [
            //                             "system"=> "http://terminology.hl7.org/CodeSystem/condition-category",
            //                             "code"=> "encounter-diagnosis",
            //                             "display"=> "Encounter Diagnosis"
            //                     ]
            //                     ]
            //                 ]
            //             ],
            //             "code"=> [
            //                 "coding"=> [
            //                     [
            //                         "system"=> "http://hl7.org/fhir/sid/icd-10",
            //                         "code"=> "A91",
            //                         "display"=> "Dengue haemorrhagic fever"
            //                     ]
            //                 ]
            //                     ],
            //             "subject"=> [
            //                 "reference"=> "Patient/P01504896438",
            //                 "display"=> "FARMA WARDANA"
            //             ],
            //             "encounter"=> [
            //                 "reference"=> "urn:uuid:98068af1-77da-4cce-ab83-68dca3290e40",
            //                 "display"=> "Kunjungan FARMA WARDANA di hari Selasa, 18 Desember 2022"
            //             ]
            //             ],
            //         "request"=> [
            //             "method"=> "POST",
            //             "url"=> "Condition"
            //         ]
            //     ]
            // ],
  "resourceType" => "Bundle",
            "type" => "transaction",
            "entry" => [
                [
                    "fullUrl" => "urn:uuid:98068af1-77da-4cce-ab83-68dca3290e40",
                    "resource" => [
                        "resourceType" => "Encounter",
                        "status" => "finished",
                        "class" => [
                            "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                            "code" => "AMB",
                            "display" => "ambulatory",
                        ],
                        "subject" => [
                            "reference" => "Patient/P01504896438",
                            "display" => "FARMA WARDANA",
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
                                    "reference" => "Practitioner/1000426772",
                                    "display" => "Herry Rahardjo"
                                ],
                            ],
                        ],
                        "period" => [
                            "start" => "2024-01-18T14:00:00+00:00",
                            "end" => "2024-01-18T16:00:00+00:00",
                        ],
                        "location" => [
                            [
                                "location" => [
                                    "reference" => "Location/fc1e0e39-a3ec-461c-be1c-e0b84cbde848",
                                    "display" =>"Poli Urologi"
                                ],
                            ],
                        ],
                        "diagnosis" => [
                            [
                                "condition" => [
                                    "reference" => "urn:uuid:41871f14-15ca-40ea-bd4f-31c0044753fb",
                                    "display" => "Acute appendicitis, other and unspecified",
                                ],
                                "use" => [
                                    "coding" => [
                                        [
                                            "system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
                                            "code" => "DD",
                                            "display" => "Discharge diagnosis",
                                        ],
                                    ],
                                ],
                                "rank" => 1,
                            ],
                            [
                                "condition" => [
                                    "reference" => "urn:uuid:cd9c3136-1bad-4aca-a43a-36e51eb68277",
                                    "display" => "Dengue haemorrhagic fever",
                                ],
                                "use" => [
                                    "coding" => [
                                        [
                                            "system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
                                            "code" => "DD",
                                            "display" => "Discharge diagnosis",
                                        ],
                                    ],
                                ],
                                "rank" => 2,
                            ],
                        ],
                        "statusHistory" => [
                            [
                                "status" => "arrived",
                                "period" => [
                                    "start" => "2022-12-18T14:00:00+00:00",
                                    "end" => "2022-12-18T15:00:00+00:00",
                                ],
                            ],
                            [
                                "status" => "in-progress",
                                "period" => [
                                    "start" => "2022-12-18T15:00:00+00:00",
                                    "end" => "2022-12-18T16:00:00+00:00",
                                ],
                            ],
                            [
                                "status" => "finished",
                                "period" => [
                                    "start" => "2022-12-18T16:00:00+00:00",
                                    "end" => "2022-12-18T16:00:00+00:00",
                                ],
                            ],
                        ],
                        "serviceProvider" => [
                            "reference" => "Organization/9e54ca56-5dd2-47c6-83e5-187ace4023c3",
                        ],
                        "identifier" => [
                            [
                                "system" => "http://sys-ids.kemkes.go.id/encounter/9e54ca56-5dd2-47c6-83e5-187ace4023c3",
                                "value" => "P01504896438",
                            ],
                        ],
                    ],
                    "request" => [
                        "method" => "POST",
                        "url" => "Encounter",
                    ],
                ],
                [
                    "fullUrl"=> "urn:uuid:41871f14-15ca-40ea-bd4f-31c0044753fb",
                    "resource"=> [
                        "resourceType"=> "Condition",
                        "clinicalStatus"=> [
                            "coding"=> [
                                [
                                    "system"=> "http://terminology.hl7.org/CodeSystem/condition-clinical",
                                    "code"=> "active",
                                    "display"=> "Active"
                                ]
                            ]
                                ],
                        "category"=> [
                            [
                                "coding"=> [
                                    [
                                        "system"=> "http://terminology.hl7.org/CodeSystem/condition-category",
                                        "code"=> "encounter-diagnosis",
                                        "display"=> "Encounter Diagnosis"
                                    ]
                                ]
                            ]
                        ],
                        "code"=> [
                            "coding"=> [
                                [
                                    "system"=> "http://hl7.org/fhir/sid/icd-10",
                                    "code"=> "K35.8",
                                    "display"=> "Acute appendicitis, other and unspecified"
                                ]
                            ]
                                ],
                        "subject"=> [
                            "reference"=> "Patient/P01504896438",
                            "display"=> "FARMA WARDANA"
                        ],
                        "encounter"=> [
                            "reference"=> "urn:uuid:98068af1-77da-4cce-ab83-68dca3290e40",
                            "display"=> "Kunjungan FARMA WARDANA di hari Selasa, 18 Desember 2022"
                        ]
                        ],
                    "request"=> [
                        "method"=> "POST",
                        "url"=> "Condition"
                    ]
                ],
                [
                    "fullUrl"=> "urn:uuid:cd9c3136-1bad-4aca-a43a-36e51eb68277",
                    "resource"=> [
                        "resourceType"=> "Condition",
                        "clinicalStatus"=> [
                            "coding"=> [
                                [
                                    "system"=> "http://terminology.hl7.org/CodeSystem/condition-clinical",
                                    "code"=> "active",
                                    "display"=> "Active"
                                ]
                            ]
                                ],
                        "category"=> [
                            [
                                "coding"=> [
                                    [
                                        "system"=> "http://terminology.hl7.org/CodeSystem/condition-category",
                                        "code"=> "encounter-diagnosis",
                                        "display"=> "Encounter Diagnosis"
                                ]
                                ]
                            ]
                        ],
                        "code"=> [
                            "coding"=> [
                                [
                                    "system"=> "http://hl7.org/fhir/sid/icd-10",
                                    "code"=> "A91",
                                    "display"=> "Dengue haemorrhagic fever"
                                ]
                            ]
                                ],
                        "subject"=> [
                            "reference"=> "Patient/P01504896438",
                            "display"=> "FARMA WARDANA"
                        ],
                        "encounter"=> [
                            "reference"=> "urn:uuid:98068af1-77da-4cce-ab83-68dca3290e40",
                            "display"=> "Kunjungan FARMA WARDANA di hari Selasa, 18 Desember 2022"
                        ]
                        ],
                    "request"=> [
                        "method"=> "POST",
                        "url"=> "Condition"
                    ]
                ]
            ],
        ];

        return $data;
    }

    function PostEncounterCondition(array $body)
    {
        $token = $this->accessToken->token();

        $url = $this->config->setUrl();

        $bodyRaw = $this->bodyPostEncounterCondition($body);

        $response = $this->httpClient->post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => $bodyRaw,
        ]);

        $data = $response->getBody()->getContents();
        return json_decode($data, true);
    }
}
