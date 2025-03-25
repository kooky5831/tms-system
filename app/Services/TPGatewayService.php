<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Services\VenueService;
use Auth;

class TPGatewayService
{
    protected $passphrase = 'grE9yosJesjo1LtTdyCwQIUKmRdob3GxrKbvxwIvT7s=';

    protected $iv = 'SSGAPIInitVector';


    /*
    * Get all Trainers
    */
    public function getAllTrainersFromTPgateway() {
        $uen = config('settings.tpgateway_uenno');
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        try {
            $responseData = $this->tpGatewayCallApiGetRequest("trainingProviders/".$uen."/trainers", [], 'v2.0');
        } catch (\Exception $e) {
            dd($e->getMessage());
            \Log::info($e->getMessage());
        }

        $decryptedData = $this->CryptoJSAesDecrypt($responseData);
        $response = json_decode($decryptedData);
        return $response;
    }


    /*
    * Add Trainer to TP Gateway
    */
    public function addTrainerToTpGateway($req_data) {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        try {
            $decryptedData = $this->encryptCallApiDecrypt('trainingProviders/'.config('settings.tpgateway_uenno').'/trainers', $req_data, false, "v2.0");
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }

        $response = json_decode($decryptedData);
        return $response;
    }


    /*
    * Update Trainer to TP Gateway
    */
    public function updateTrainerToTpGateway($trainer_id, $req_data) {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        try {
            $decryptedData = $this->encryptCallApiDecrypt('trainingProviders/'.config('settings.tpgateway_uenno').'/trainers/'.$trainer_id, $req_data, false, "v2.0");
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }

        $response = json_decode($decryptedData);
        return $response;
    }

    /*
    * Retrieve course sessions
    */
    public function getCourseSessionsFromTpGateway($courseRunId,$courseRefNo, $sessMonth = null) {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        $reqData = [
            "uen" => config('settings.tpgateway_uenno'),
            "courseReferenceNumber" => urlencode($courseRefNo),
            // "sessionMonth" => $sessMonth
        ];
        try {
            $responseData = $this->tpGatewayCallApiGetRequest("courses/runs/".$courseRunId."/sessions", $reqData, 'v1.4');
        } catch (\Exception $e) {
            dd($e->getMessage());
            \Log::info($e->getMessage());
        }

        $response = json_decode($responseData);
        return $response;
    }

    /*
    * Add course Run with sessions and trainers
    * NOTE: This API version is deprecated now. We are not using this API in TMS. Here we keep this function code as a reference.
    */
    public function addCourseRunToTpGateway($req_data) {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        try {
            $responseData = $this->tpGatewayCallApi('courses/runs', $req_data, 'v1.4');
        } catch (\Exception $e) {
            dd($e->getMessage());
            \Log::info($e->getMessage());
        }

        $response = json_decode($responseData);
        return $response;
    }

    /*
    * Update/Delete course Run with sessions and trainers
    * NOTE: This API version is deprecated now. We are not using this API in TMS. Here we keep this function code as a reference.
    */
    public function udpateCourseRunToTpGateway($runId, $req_data) {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        try {
            $responseData = $this->tpGatewayCallApi('courses/runs'.$runId, $req_data, 'v1.4');
        } catch (\Exception $e) {
            dd($e->getMessage());
            \Log::info($e->getMessage());
        }

        $response = json_decode($responseData);
        return $response;
    }


    /*
    * Add course Run with sessions and trainers New Version v1.0
    */
    public function addCourseRunToTpGatewayNew($req_data) {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        try {
            $decryptedData = $this->encryptCallApiDecrypt('courses/courseRuns/publish', $req_data, false, "v1.0");
        } catch (\Exception $e) {
            dd($e->getMessage());
            \Log::info($e->getMessage());
        }

        $response = json_decode($decryptedData);
        return $response;
    }

    /*
    * Update/Delete course Run with sessions and trainers New Version v1.0
    */
    public function udpateCourseRunToTpGatewayNew($runId, $req_data) {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        try {
            $decryptedData = $this->encryptCallApiDecrypt('courses/courseRuns/edit/'.$runId, $req_data, false, "v1.0");
        } catch (\Exception $e) {
            dd($e->getMessage());
            \Log::info($e->getMessage());
        }

        $response = json_decode($decryptedData);
        return $response;
    }

    /*
    * display grant calculator
    */
    public function checkGrantCalculator($req_data) {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        try {
            $decryptedData = $this->encryptCallApiDecrypt('tpg/grants/search', $req_data);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }

        $response = json_decode($decryptedData);
        return $response;
    }


    /*
    * Get Grant details by Grant Reference Number v1
    */
    public function checkGrantStatus($refNo) {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        try {
            $responseData = $this->tpGatewayCallApiGetRequest("tpg/grants/details/".$refNo, [], 'v1');
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }

        $decryptedData = $this->CryptoJSAesDecrypt($responseData);
	    $response = json_decode($decryptedData);
	    return $response;
    }

    /*
    * cancel/update student enrollment
    */
    public function updateCancelStudentEnrolment($refNumber, $req_data) {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        try {
            $decryptedData = $this->encryptCallApiDecrypt('tpg/enrolments/details/'.$refNumber, $req_data);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }

        $response = json_decode($decryptedData);
        return $response;
    }

    /*
    * Get student details for payment status sync
    */
    public function getStudentEnrolmentPaymentStatus($refNumber){
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        try {
            $decryptedData = $this->tpGatewayCallApiGetRequest("tpg/enrolments/details/".$refNumber, [],'');
            //$decryptedData = $this->tpGatewayCallApiGetRequest('tpg/enrolments/details/'.$refNumber);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }

        $decryptedData = $this->CryptoJSAesDecrypt($decryptedData);
        $response = json_decode($decryptedData);
        return $response;
    }

    /*
    * add student enrollement
    */
    public function studentEnrolment($req_data) {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        try {
            $decryptedData = $this->encryptCallApiDecrypt('tpg/enrolments', $req_data);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }
        $response = json_decode($decryptedData);
        return $response;
        // {"status":200,"data":{"enrolment":{"referenceNumber":"ENR-2108-069510","status":"Confirmed"}},"meta":{"createdOn":"2021-08-19T10:53:57Z","updatedOn":"2021-08-19T10:53:57Z"},"error":{}}
    }

    /*
    * Retrieve course run by id
    */
    public function getCourseRunFromTpGateway($courseRunId) {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        try {
            // $responseData = $this->tpGatewayCallApiGetRequest("courses/runs/".$courseRunId, [], 'v1.4');
            $responseData = $this->tpGatewayCallApiGetRequest("courses/courseRuns/id/".$courseRunId, [], 'v1.0');
        } catch (\Exception $e) {
            dd($e->getMessage());
            \Log::info($e->getMessage());
        }

        $response = json_decode($responseData);
        return $response;
    }

    public function getTpgStudentEnrolmentById($enrolmentId)
    {
        $enrolmentRes = $this->getStudentEnrolmentFromTpGateway($enrolmentId);
        if( $enrolmentRes->status == 200 ) {
            $res = [ 'status' => true, 'data' => $enrolmentRes->data->enrolment, 'msg' => 'Student Enrolment data fetched' ];
        } else {
            $res = [ 'status' => false, 'data' => [], 'msg' => 'No Enrolment found' ];
        }
        return $res;
    }

    /*
    * get student enrollement
    */
    public function getStudentEnrolmentFromTpGateway($enrollmentId) {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        try {
            $responseData = $this->tpGatewayCallApiGetRequest("tpg/enrolments/details/".$enrollmentId, [], 'v1');
        } catch (\Exception $e) {
            dd($e->getMessage());
            \Log::info($e->getMessage());
        }
        /*try {
            $decryptedData = $this->encryptCallApiDecrypt("tpg/enrolments/details/".$enrollmentId, $req_data);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }
        $response = json_decode($decryptedData);*/
        $decryptedData = $this->CryptoJSAesDecrypt($responseData);
        $response = json_decode($decryptedData);
        return $response;
        // {"status":200,"data":{"enrolment":{"referenceNumber":"ENR-2108-069510","status":"Confirmed"}},"meta":{"createdOn":"2021-08-19T10:53:57Z","updatedOn":"2021-08-19T10:53:57Z"},"error":{}}
    }

    /*
    * Retrieve Courses based on Training Provider UEN
    */
    public function retrieveCourses() {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }

        /* $certfile       = base_path() . '/config/tpGatewayCertificates/cert.pem';
        $keyfile        = base_path() . '/config/tpGatewayCertificates/key.pem';

        $curl5 = curl_init();

        curl_setopt_array($curl5, array(
          CURLOPT_URL => 'https://api.ssg-wsg.sg/tpg/courses/registry/details/TGS-2020505239',
          CURLOPT_SSLCERT  =>  $certfile,
          CURLOPT_SSLCERTTYPE  => 'PEM',
          CURLOPT_SSLKEY => $keyfile ,
          CURLOPT_SSLKEYTYPE  => 'PEM',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          // CURLOPT_POSTFIELDS => $response4,
          CURLOPT_HTTPHEADER => array(
            ': ',
            'Content-Type: application/json'
          ),
        ));

        $response5 = curl_exec($curl5);

        curl_close($curl5);
        echo $response5;
        exit; */

        $req_data_grant = [];
        // grant calvulator
        $req_data_grant['grants'] = [
            "enrolment" => [
                "referenceNumber" => "ENR-2104-037036"
            ],
            "trainee" => [
                "id" => "S9348650A"
            ],
            "employer" => [
                "uen" => "201708981D"
            ],
            'trainingPartner' => [
                "code" => config('settings.tpgateway_code'),
                "uen" => config('settings.tpgateway_uenno')
            ],
            "course" => [
                "referenceNumber" => "TGS-2020505239",
                "run" => [ 'id' => "255735" ]
            ]
        ];
        $req_data_grant['meta'] = [
            "lastUpdateDateFrom" => date('Y')."-01-01",
            "lastUpdateDateTo" => date('Y', strtotime('+1 year'))."-01-01"
        ];
        $req_data_grant['sortBy'] = [
            "field" => "updatedOn",
            "order" => "asc"
        ];
        $req_data_grant['parameters'] = [
            "page" => 0,
            "pageSize" => 20
        ];

        $grant = $this->checkGrantCalculator($req_data_grant);
        dd($grant);

        $response = Http::asJson()->withHeaders(['x-api-version' => 'v1'])
                        ->withOptions([
                            'cert' => base_path() . '/config/tpGatewayCertificates/cert.pem',
                            'ssl_key' => base_path() . '/config/tpGatewayCertificates/key.pem'
                        ])
                        ->get(config('settings.tpgateway_baseurl').'tpg/courses/registry/details/TGS-2020505239');
                        // ->get(config('settings.tpgateway_baseurl').'trainingproviders/'.config('settings.tpgateway_uenno').'/courses');
        dd($response);
        if( $response->successful() ) {

            return $response->json();
        }
        return FALSE;
    }

    public function getCourseAttendance($runId, $req_data) {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        try {
            $responseData = $this->tpGatewayCallApiGetRequest("courses/runs/".$runId."/sessions/attendance", $req_data, 'v1.4');
        } catch (\Exception $e) {
            dd($e->getMessage());
            \Log::info($e->getMessage());
        }
        $decryptedData = $this->CryptoJSAesDecrypt($responseData);
        $response = json_decode($decryptedData);
        return $response;
    }

    public function courseAssessments($req_data) {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        // echo json_encode($req_data); exit;
        try {
            $decryptedData = $this->encryptCallApiDecrypt('tpg/assessments', $req_data);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }
        $response = json_decode($decryptedData);
        return $response;
    }

    public function getCourseAssessments($req_data) {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        // echo json_encode($req_data); exit;
        try {
            $decryptedData = $this->encryptCallApiDecrypt('tpg/assessments/search', $req_data);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }
        $response = json_decode($decryptedData);
        return $response;
    }

    public function coursePayments($referenceNumber, $req_data)
    {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        // echo json_encode($req_data); exit;
        try {
            $decryptedData = $this->encryptCallApiDecrypt('tpg/enrolments/feeCollections/'.$referenceNumber, $req_data);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }
        $response = json_decode($decryptedData);
        return $response;
    }

    public function courseAttendance($runId, $req_data) {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        try {
            $decryptedData = $this->encryptCallApiDecrypt('courses/runs/'.$runId.'/sessions/attendance', $req_data, false, 'v1.4');
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }
        $response = json_decode($decryptedData);
        return $response;
    }

    public function getCourseFromRefNumber($refNo) {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        $response = Http::asJson()->withHeaders(['x-api-version' => 'v1.1'])
                        ->withOptions([
                            'cert' => base_path() . '/config/tpGatewayCertificates/cert.pem',
                            'ssl_key' => base_path() . '/config/tpGatewayCertificates/key.pem'
                        ])
                        ->get(config('settings.tpgateway_baseurl').'/courses/directory/'.$refNo);
        // dd($response);
        if( $response->successful() ) {
            return $response->json();
        }
        return FALSE;
    }

    // Create request object
    public function createEnrolmentRequest($course, $record)
    {
        $req_data['enrolment']['trainingPartner'] = [
            "code" => config('settings.tpgateway_code'),
            "uen" => config('settings.tpgateway_uenno')
        ];
        $req_data['enrolment']["course"] = [
            "referenceNumber" => $course->courseMain->reference_number,
            "run" => [ "id" => $course->tpgateway_id ]
        ];
        if( $record->sponsored_by_company == "Yes" ) {
            $req_data['enrolment']["trainee"] = [
                "id" => $record->student->nric,
                "idType" => [ "type" => $record->nationality == "Non-Singapore Citizen/PR" ? "Others" : "NRIC" ],
                "dateOfBirth" => $record->dob,
                "fullName" => $record->student->name,
                "contactNumber" => [
                    "countryCode" => "+65",
                    "areaCode" => "",
                    "phoneNumber" => $record->mobile_no
                ],
                "emailAddress" => $record->email,
                "sponsorshipType" => "EMPLOYER",
                "employer" => [
                    "uen" => $record->company_uen,
                    "contact" => [
                        "fullName" => $record->company_contact_person,
                        "contactNumber" => [
                            "countryCode" => "+65",
                            "areaCode" => "",
                            "phoneNumber" => $record->company_contact_person_number
                        ],
                        "emailAddress" => $record->company_contact_person_email
                    ]
                ],
                "fees" => [
                    "discountAmount" => $record->discountAmount,
                    "collectionStatus" => getPaymentStatusForTPG($record->payment_tpg_status)
                ],
                "enrolmentDate" => Carbon::now(config('settings.tpgatewayTimezone'))->format('Y-m-d')
            ];
        } else {
            $req_data['enrolment']["trainee"] = [
                "id" => $record->student->nric,
                "idType" => [ "type" => $record->nationality == "Non-Singapore Citizen/PR" ? "Others" : "NRIC" ],
                "dateOfBirth" => $record->dob,
                "fullName" => $record->student->name,
                "contactNumber" => [
                    "countryCode" => "+65",
                    "areaCode" => "",
                    "phoneNumber" => $record->mobile_no
                ],
                "emailAddress" => $record->email,
                "sponsorshipType" => "INDIVIDUAL",
                "employer" => [
                    "uen" => "",
                    "contact" => [
                        "fullName" => "",
                        "contactNumber" => [
                            "countryCode" => "",
                            "areaCode" => "",
                            "phoneNumber" => ""
                        ],
                        "emailAddress" => ""
                    ]
                ],
                "fees" => [
                    "discountAmount" => $record->discountAmount,
                    "collectionStatus" => getPaymentStatusForTPG($record->payment_tpg_status)
                ],
                "enrolmentDate" => Carbon::now(config('settings.tpgatewayTimezone'))->format('Y-m-d')
            ];
        }

        // $req_data['enrolment'] = [
        //     'trainingPartner' => [
        //         "code" => config('settings.tpgateway_code'),
        //         "uen" => config('settings.tpgateway_uenno')
        //     ],
        //     "course" => [
        //         "referenceNumber" => $course->courseMain->reference_number,
        //         "run" => [ "id" => $course->tpgateway_id ]
        //     ],
        //     "trainee" => [
        //         "id" => $record->student->nric,
        //         "idType" => [ "type" => $record->nationality == "Non-Singapore Citizen/PR" ? "Others" : "NRIC" ],
        //         "dateOfBirth" => $record->dob,
        //         "fullName" => $record->student->name,
        //         "contactNumber" => [
        //             "countryCode" => "+65",
        //             "areaCode" => "",
        //             "phoneNumber" => $record->mobile_no
        //         ],
        //         "emailAddress" => $record->email,
        //         "sponsorshipType" => $record->sponsored_by_company == "Yes" ? "EMPLOYER" : "INDIVIDUAL",
        //         "employer" => [
        //             "uen" => $record->company_uen,
        //             "contact" => [
        //                 "fullName" => $record->company_contact_person,
        //                 "contactNumber" => [
        //                     "countryCode" => "+65",
        //                     "areaCode" => "",
        //                     "phoneNumber" => $record->company_contact_person_number
        //                 ],
        //                 "emailAddress" => $record->company_contact_person_email
        //             ]
        //         ],
        //         "fees" => [
        //             "discountAmount" => 0,
        //             "collectionStatus" => "Pending Payment"
        //         ],
        //         "enrolmentDate" => Carbon::now(config('settings.tpgatewayTimezone'))->format('Y-m-d')
        //     ],
        // ];
        return $req_data;
    }

    public function createEnrollmentCancelRequest($course, $record)
    {
        $req_data['enrolment'] = [
            "action" => "Cancel",
            "course" => ["run" => ["id" => $course->tpgateway_id]],
            "trainee" => [
                "contactNumber" => [
                    "countryCode" => "+65",
                    "areaCode" => "",
                    "phoneNumber" => $record->mobile_no
                ],
                "email" => $record->email,
            ],
            /*"fees" => [
                "discountAmount" => "78",
                "feecollectionStatus" => "Full Payment"
            ]*/
        ];
        if( $record->sponsored_by_company == "Yes" ) {
            $req_data['enrolment']["employer"] = [
                "contact" => [
                    "fullName" => $record->company_contact_person,
                    "contactNumber" => [
                        "countryCode" => "+65",
                        "areaCode" => "",
                        "phoneNumber" => $record->company_contact_person_number
                    ],
                    "email" => $record->company_contact_person_email
                ]
            ];
        }
        return $req_data;
    }

    public function createEnrollmentUpdateRequest($course, $record)
    {
        $req_data['enrolment'] = [
            "action" => "Update",
            "course" => ["run" => ["id" => $course->tpgateway_id]],
            "trainee" => [
                "contactNumber" => [
                    "countryCode" => "+65",
                    "areaCode" => "",
                    "phoneNumber" => $record->mobile_no
                ],
                "email" => $record->email,
            ],
            /*"fees" => [
                "discountAmount" => "78",
                "feecollectionStatus" => "Full Payment"
            ]*/
        ];
        if( $record->sponsored_by_company == "Yes" ) {
            $req_data['enrolment']["employer"] = [
                "contact" => [
                    "fullName" => $record->company_contact_person,
                    "contactNumber" => [
                        "countryCode" => "+65",
                        "areaCode" => "",
                        "phoneNumber" => $record->company_contact_person_number
                    ],
                    "email" => $record->company_contact_person_email
                ]
            ];
        }
        return $req_data;
    }

    public function createGrantRequest($course, $record)
    {
        $req_data['grants'] = [
            "enrolment" => [
                "referenceNumber" => $record->tpgateway_refno
            ],
            "trainee" => [
                "id" => $record->student->nric
            ],
            "employer" => [
                "uen" => ($record->sponsored_by_company == "Yes") ? $record->company_uen : "",
            ],
            'trainingPartner' => [
                "code" => config('settings.tpgateway_code'),
                "uen" => config('settings.tpgateway_uenno')
            ],
            "course" => [
                "referenceNumber" => $course->courseMain->reference_number,
                "run" => [ 'id' => $course->tpgateway_id ]
            ]
        ];
        $req_data['meta'] = [
            "lastUpdateDateFrom" => Carbon::createFromDate($course->course_start_date)->subYear(1)->startOfYear()->format('Y-m-d'),
            "lastUpdateDateTo" => Carbon::createFromDate($course->course_start_date)->addYear(1)->endOfYear()->format('Y-m-d'),
        ];
        $req_data['sortBy'] = [
            "field" => "updatedOn",
            "order" => "asc"
        ];
        $req_data['parameters'] = [
            "page" => 0,
            "pageSize" => 100
        ];
        return $req_data;
    }

    public function createGrantRequestFromTPGateway($enrolment)
    {
        $req_data['grants'] = [
            "enrolment" => [
                "referenceNumber" => $enrolment->referenceNumber
            ],
            "trainee" => [
                "id" => $enrolment->trainee->id
            ],
            "employer" => [
                "uen" => $enrolment->trainee->employer->uen
            ],
            'trainingPartner' => [
                "code" => config('settings.tpgateway_code'),
                "uen" => config('settings.tpgateway_uenno')
            ],
            "course" => [
                "referenceNumber" => $enrolment->course->referenceNumber,
                "run" => [ 'id' => $enrolment->course->run->id ]
            ]
        ];
        $req_data['meta'] = [
            "lastUpdateDateFrom" => date('Y')."-01-01",
            "lastUpdateDateTo" => date('Y', strtotime('+1 year'))."-01-01"
        ];
        $req_data['sortBy'] = [
            "field" => "updatedOn",
            "order" => "asc"
        ];
        $req_data['parameters'] = [
            "page" => 0,
            "pageSize" => 20
        ];
        return $req_data;
    }

    public function createCourseRunRequest($record, $tpSessions, $tpTrainers, $method = null)
    {
        $user = Auth::user();
        $venueService = new VenueService;
        $venue = $venueService->getVenueById($record->venue_id);
        $req_data['course'] = [
            'trainingProvider' => [
                "uen" => config('settings.tpgateway_uenno')
                // "uen" => "10000000K"
            ],
            "courseReferenceNumber" => $record->courseMain->reference_number,
        ];
        $req_data['course']['runs'][0] = [
            'venue' => [
                "block"             => $venue->block,
                "street"            => $venue->street,
                "floor"             => $venue->floor,
                "unit"              => $venue->unit,
                "building"          => $venue->building,
                "postalCode"        => $venue->postal_code,
                "room"              => $venue->room,
                "wheelChairAccess"  => $venue->wheelchairaccess ? true : false,
                "primaryVenue"      => true,
            ],
            'sequenceNumber' => 0,
            'registrationDates' => [
                'opening' => convertToTPDate($record->registration_opening_date),
                'closing' => convertToTPDate($record->registration_closing_date),
            ],
            'courseDates' => [
                'start' => convertToTPDate($record->course_start_date),
                'end'   => convertToTPDate($record->course_end_date),
            ],
            'scheduleInfoType' => [
                'code' => $record->schinfotype_code,
                'description' => $record->sch_info,
            ],
            'scheduleInfo'  => $record->sch_info,
            'intakeSize'    => $record->intakesize,
            'threshold'     => $record->threshold,
            'modeOfTraining'=> $record->modeoftraining,
            'courseAdminEmail' => $user->email,
            'courseVacancy' => [
                'code'          => $record->coursevacancy_code,
                'description'   => $record->coursevacancy_desc,
            ],
            'sessions'      => $tpSessions,
            'linkCourseRunTrainer' => $tpTrainers
        ];
        if( !is_null($method) ) {
            $req_data['course']['runs'][0]['action'] = $method;
        }
        return $req_data;
        
    }

    /* Build Add course Run Request - For New API version v1.0 */

    public function buildAddCourseRunRequest($record, $tpSessions, $tpTrainers, $method = null)
    {
        $user = Auth::user();
        $venueService = new VenueService;
        $venue = $venueService->getVenueById($record->venue_id);
        $req_data['course'] = [
            'trainingProvider' => [
                "uen" => config('settings.tpgateway_uenno')
            ],
            "courseReferenceNumber" => $record->courseMain->reference_number,
        ];
        $req_data['course']['runs'][0] = [
            'venue' => [
                "block"             => $venue->block,
                "street"            => $venue->street,
                "floor"             => $venue->floor,
                "unit"              => $venue->unit,
                "building"          => $venue->building,
                "postalCode"        => $venue->postal_code,
                "room"              => $venue->room,
                "wheelChairAccess"  => $venue->wheelchairaccess ? true : false,
                //"primaryVenue"      => true,
            ],
            'sequenceNumber' => 0,
            'registeredUserCount' => $record->registeredusercount,
            'registrationDates' => [
                'opening' => (int)convertToTPDate($record->registration_opening_date),
                'closing' => (int)convertToTPDate($record->registration_closing_date),
            ],
            'courseDates' => [
                'start' => (int)convertToTPDate($record->course_start_date),
                'end'   => (int)convertToTPDate($record->course_end_date),
            ],
            'scheduleInfoType' => [
                'code' => $record->schinfotype_code,
                'description' => $record->sch_info,
            ],
            'scheduleInfo'  => $record->sch_info,
            'intakeSize'    => (int)$record->intakesize,
            'threshold'     => (int)$record->threshold,
            'modeOfTraining'=> $record->modeoftraining,
            'courseAdminEmail' => $user->email,
            'courseVacancy' => [
                'code'          => $record->coursevacancy_code,
                'description'   => $record->coursevacancy_desc,
            ],
            'sessions'      => $tpSessions,
            'linkCourseRunTrainer' => $tpTrainers
        ];
        if( !is_null($method) ) {
            $req_data['course']['runs'][0]['action'] = $method;
        }
    
        return $req_data;
        
    }

    /* Build Update course Run Request - For New API version v1.0 */

    public function buildUpdateCourseRunRequest($record, $tpSessions, $tpTrainers, $method = null)
    {
        $user = Auth::user();
        $venueService = new VenueService;
        $venue = $venueService->getVenueById($record->venue_id);
        $req_data['course'] = [
            'trainingProvider' => [
                "uen" => config('settings.tpgateway_uenno')
            ],
            "courseReferenceNumber" => $record->courseMain->reference_number,
        ];
        $req_data['course']['run'] = [
            'venue' => [
                "block"             => $venue->block,
                "street"            => $venue->street,
                "floor"             => $venue->floor,
                "unit"              => $venue->unit,
                "building"          => $venue->building,
                "postalCode"        => $venue->postal_code,
                "room"              => $venue->room,
                "wheelChairAccess"  => $venue->wheelchairaccess ? true : false,
                //"primaryVenue"      => true,
            ],
            'sequenceNumber' => 0,
            'registeredUserCount' => $record->registeredusercount,
            'registrationDates' => [
                'opening' => (int)convertToTPDate($record->registration_opening_date),
                'closing' => (int)convertToTPDate($record->registration_closing_date),
            ],
            'courseDates' => [
                'start' => (int)convertToTPDate($record->course_start_date),
                'end'   => convertToTPDate($record->course_end_date),
            ],
            'scheduleInfoType' => [
                'code' => $record->schinfotype_code,
                'description' => $record->sch_info,
            ],
            'scheduleInfo'  => $record->sch_info,
            'intakeSize'    => (int)$record->intakesize,
            'threshold'     => (int)$record->threshold,
            'modeOfTraining'=> $record->modeoftraining,
            'courseAdminEmail' => $user->email,
            'courseVacancy' => [
                'code'          => $record->coursevacancy_code,
                'description'   => $record->coursevacancy_desc,
            ],
            'sessions'      => $tpSessions,
            'linkCourseRunTrainer' => $tpTrainers
        ];
        if( !is_null($method) ) {
            $req_data['course']['run']['action'] = $method;
        }
        return $req_data;
        
    }

    /*  Update or Void Assessment v1 */
    public function updateVoidCourseAssessment($ref_no, $req_data) {
        if( !config('settings.tpgateway_active') ) {
            return FALSE;
        }
        try {
            $decryptedData = $this->encryptCallApiDecrypt('tpg/assessments/details/'.$ref_no, $req_data);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }
        $response = json_decode($decryptedData);
        return $response;
    }

    private function CryptoJSAesEncrypt($dataToEncrypt) {

        $output = false;

        $aesKey = base64_decode($this->passphrase);

        $output = openssl_encrypt($dataToEncrypt, 'AES-256-CBC', $aesKey,
        OPENSSL_RAW_DATA, $this->iv);

        $output = base64_encode($output);
        return $output;
    }

    private function CryptoJSAesDecrypt($dataTodecrypt) {

        $output = false;
        $dataTodecrypt = base64_decode($dataTodecrypt);

        $aesKey = base64_decode($this->passphrase);

        $dataTodecrypt = $output = openssl_decrypt($dataTodecrypt, 'AES-256-CBC',
        $aesKey, OPENSSL_RAW_DATA, $this->iv);
        return $output;
    }

    private function encryptCallApiDecrypt($apiEndpoint,$req_data, $decrypt = TRUE, $version = "v1")
    {
        $curl2 = curl_init();

        $certfile = base_path() . '/config/tpGatewayCertificates/cert.pem';
        $keyfile = base_path() . '/config/tpGatewayCertificates/key.pem';

        $encryptedData = $this->CryptoJSAesEncrypt(json_encode($req_data));

        curl_setopt_array($curl2, array(
          CURLOPT_URL => config('settings.tpgateway_baseurl').$apiEndpoint,
          CURLOPT_SSLCERT  =>  $certfile,
          CURLOPT_SSLCERTTYPE  => 'PEM',
          CURLOPT_SSLKEY => $keyfile ,
          CURLOPT_SSLKEYTYPE  => 'PEM',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $encryptedData,
          CURLOPT_HTTPHEADER => array(
            'x-api-version: '.$version,
            'Content-Type: application/json',
          ),
        ));

        $response = curl_exec($curl2);

        //print_r($responseX);
        $err = curl_error($curl2);
        curl_close($curl2);
        if( $decrypt ) {
            $decryptedData = $this->CryptoJSAesDecrypt($response);
            return $decryptedData;
        }
        return $response;
        // print_r($decryptedData);
    }

    private function tpGatewayCallApi($apiEndpoint,$req_data, $version = "v1")
    {
        $curl2 = curl_init();

        $certfile = base_path() . '/config/tpGatewayCertificates/cert.pem';
        $keyfile = base_path() . '/config/tpGatewayCertificates/key.pem';

        curl_setopt_array($curl2, array(
          CURLOPT_URL => config('settings.tpgateway_baseurl').$apiEndpoint,
          CURLOPT_SSLCERT  =>  $certfile,
          CURLOPT_SSLCERTTYPE  => 'PEM',
          CURLOPT_SSLKEY => $keyfile ,
          CURLOPT_SSLKEYTYPE  => 'PEM',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => json_encode($req_data),
          CURLOPT_HTTPHEADER => array(
            'x-api-version: '.$version,
            'Content-Type: application/json',
          ),
        ));

        $response = curl_exec($curl2);

        //print_r($responseX);
        $err = curl_error($curl2);
        curl_close($curl2);
        return $response;
        // print_r($decryptedData);
    }

    private function tpGatewayCallApiGetRequest($apiEndpoint,$req_data, $version = "v1")
    {
        $curl2 = curl_init();

        $certfile = base_path() . '/config/tpGatewayCertificates/cert.pem';
        $keyfile = base_path() . '/config/tpGatewayCertificates/key.pem';

        if( count($req_data) > 0 ) {
            $data = http_build_query($req_data);
            $apiUrl = config('settings.tpgateway_baseurl').$apiEndpoint."?".$data;
        } else {
            $apiUrl = config('settings.tpgateway_baseurl').$apiEndpoint;
        }

        curl_setopt_array($curl2, array(
          CURLOPT_URL => $apiUrl,
          CURLOPT_SSLCERT  =>  $certfile,
          CURLOPT_SSLCERTTYPE  => 'PEM',
          CURLOPT_SSLKEY => $keyfile ,
          CURLOPT_SSLKEYTYPE  => 'PEM',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_HTTPHEADER => array(
            'x-api-version: '.$version,
            'Content-Type: application/json',
          ),
        ));

        $response = curl_exec($curl2);

        $err = curl_error($curl2);
        curl_close($curl2);
        return $response;
    }

}
