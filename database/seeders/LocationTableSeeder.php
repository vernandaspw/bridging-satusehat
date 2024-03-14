<?php

namespace Database\Seeders;

use App\Models\Location;
use GuzzleHttp\Client;
use Illuminate\Database\Seeder;
use App\Services\SatuSehat\AccessToken;
use App\Services\SatuSehat\ConfigSatuSehat;


class LocationTableSeeder extends Seeder
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
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function bodyRaw()
    {
        $data = [
            "resourceType" => "Location",
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/location/" . env('SATU_SEHAT_ORGANIZATION_ID'),
                    "value" => "RSUD SITI FATIMAH"
                ]
            ],
            "status" => "active",
            "name" => "RSUD SITI FATIMAH",
            "description" => "Rumah Sakit Umum Daerah Provinsi Sumatera Selatan",
            "mode" => "instance",
            "telecom" => [
                [
                    "system" => "phone",
                    "value" => "07115718883",
                    "use" => "work"
                ],
                [
                    "system" => "email",
                    "value" => "itrsudprovsumsel@gmail.com",
                    "use" => "work"
                ],
                [
                    "system" => "url",
                    "value" => "http://rsud.sumselprov.go.id",
                    "use" => "work"
                ]
            ],
            "address" => [
                "use" => "work",
                "line" => [
                    "Kol. Hj. Burlian Km 4.5, Sumatera Selatan, Indonesia"
                ],
                "city" => "Kota Palembang",
                "postalCode" => "34125",
                "country" => "ID",
                "extension" => [
                    [
                        "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode",
                        "extension" => [
                            [
                                "url" => "province",
                                "valueCode" => "18"
                            ],
                            [
                                "url" => "city",
                                "valueCode" => "1872"
                            ],
                            [
                                "url" => "district",
                                "valueCode" => "187203"
                            ],
                            [
                                "url" => "village",
                                "valueCode" => "1872031001"
                            ]
                        ]
                    ]
                ]
            ],
            "physicalType" => [
                "coding" => [
                    [
                        "system" => "http://terminology.hl7.org/CodeSystem/location-physical-type",
                        "code" => "si",
                        "display" => "site"
                    ]
                ]
            ],
            "managingOrganization" => [
                "reference" => "Organization/" . env('SATU_SEHAT_ORGANIZATION_ID')
            ]
        ];

        return $data;
    }

    public function run()
    {
        $token = $this->accessToken->token();

        $url = $this->config->setUrl() . 'Location';

        $bodyRaw = $this->bodyRaw();


        $response = $this->httpClient->post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
            'json' => $bodyRaw
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        $organizationId = $data['managingOrganization']['reference'];
        $parts = explode('/', $organizationId);

        Location::create([
            'location_id' => $data['id'],
            'name' => $data['name'],
            'status' => $data['status'],
            'organization_id' => end($parts),
            'description' => $data['description'],
            'created_by' => 'seeder'
        ]);
    }
}
