<?php

namespace App\Imports;

use App\Models\Course;
use App\Services\StudentService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class StudentEnrolmentsImport implements ToCollection, WithHeadingRow, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    protected $courseRun;

    public $smerrors = [];

    public function __construct()
    {
        // $this->courseRun = $courseRun;
    }

    /**
     * Transform a date value into a Carbon object.
     *
     * @return \Carbon\Carbon|null
    */
    public function transformDate($value, $format = 'Y-m-d')
    {
        try {
            return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) {
            return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }

    public function getNationality($citizenship)
    {
        $SingaporeCitizen = [
            "singapore citizen", "singapore", "singaporean",
            "singaporean citizen"
        ];
        $SingaporePermanentResident = [
            "singapore permanent resident", "singapore pr", "singaporean pr",
        ];
        $NonSingaporeCitizenPR = [
            "non-singapore citizen/pr", "non-singapore citizen/pr (fin)",
            "non-singaporean/pr", "non-singaporean", "non-singapore citizen",
            "non-singaporean/pr (malaysian)", "malaysian", "foreigner",
            "foreign passport", "fin/work permit", "china"
        ];
        if( in_array(strtolower($citizenship), $SingaporeCitizen) ) {
            return "Singapore Citizen";
        }
        if( in_array(strtolower($citizenship), $SingaporePermanentResident) ) {
            return "Singapore Permanent Resident";
        }
        if( in_array(strtolower($citizenship), $NonSingaporeCitizenPR) ) {
            return "Non-Singapore Citizen/PR";
        }
        return "Non-Singapore Citizen/PR";
    }

    public function collection(Collection $rows)
    {
        // dd($rows);
        foreach ($rows as $k => $row)
        {
            // dd(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['registration_start_date']));
            // if( $k == 0 ) continue;
            if( empty($row['name']) || empty($row['nric']) ) continue;
            $course = Course::with(['courseMain'])->find($row['course_run_id']);
            if( $course ) {
                if($row['assessment_results']){
                    $assessment = strtolower($row['assessment_results']) == 'yes' ? 'c' : 'nyc';
                }else{
                    $assessment = null;
                }
                try {
                    $studentEn = [];
                    $studentEn['course_id']                   = $row['course_run_id'];
                    $studentEn['name']                        = $row['name'];
                    $studentEn['nric']                        = $row['nric'];
                    $studentEn['sponsored_by_company']        = strtolower($row['company']) != 'nil' ? "Yes" : "No (I'm signing up as an individual)";
                    $studentEn['company_sme']                 = $row['company_sme'];
                    $studentEn['nationality']                 = $this->getNationality($row['citizenship']);
                    $studentEn['email']                       = $row['email_address'];
                    $studentEn['mobile_no']                   = $row['contact_number'];
                    $studentEn['dob']                         = $this->transformDate($row['date_of_birth'])->format('Y-m-d');
                    $studentEn['age']                         = $this->transformDate($row['date_of_birth'])->age;

                    $studentEn['company_name']                = strtolower($row['company']) != 'nil' ? $row['company'] : '';
                    $studentEn['company_uen']                 = $row['company_uen'];
                    $studentEn['company_contact_person']      = $row['company_contact_name'];
                    $studentEn['company_contact_person_email'] = $row['company_contact_email'];
                    $studentEn['company_contact_person_number'] = $row['company_contact_number'];
                    $studentEn['billing_address']             = $row['mailing_address'];
                    $studentEn['payment_status']              = strtolower($row['paid']) == 'yes' ? 3 : 1;
                    $studentEn['remarks']                     = $row['remark'];
                    $studentEn['amount']                      = $row['amount_paid_sgd'];
                    $studentEn['assessment']                  = $assessment;
                    $studentEn['learning_mode']               = "f2f";

                    $studentEn['billing_email']             = $row['billing_email'];
                    $studentEn['xero_invoice_number']             = $row['xero_invoice_number'];

                    if( !empty($row['student_enrollment_id']) ) {
                        $studentEn['student_enrollment_id']               = $row['student_enrollment_id'];
                    }
                    // payment data
                    $payment = [];
                    if( $course->courseMain->course_mode_training == "online" ) {
                        $studentEn['learning_mode']               = "online";
                    }
                    if( strtolower($row['paid']) == 'yes' ) {
                        $payment['payment_remark']          = $row['payment_remark'];
                        $payment['payment_mode']            = $row['payment_mode'];
                        $payment['payment_date']            = $course->course_end_date;
                        $payment['fee_amount']              = $row['amount_paid_sgd'];
                    }
                    // now import it
                    $studentService = new StudentService;
                    $studentEnrollment = $studentService->importStudentEnrolment($studentEn, $payment);
                } catch (\Exception $e) {
                    $this->smerrors[] = [
                        'message' => $e->getMessage(),
                        'column_no' => ($k + 2)
                    ];
                }
            } else {
                $this->smerrors[] = [
                    'message' => 'Course Not Found',
                    'column_no' => ($k + 2)
                ];
            }

        }
    }
}
