<?php

namespace App\Exports;

use App\Models\Refreshers;
use Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class StudentDetailsExport implements FromCollection,WithMapping , WithStrictNullComparison ,ShouldAutoSize,WithHeadings
{
    use Exportable;

    public $students;

    public function __construct($students)
    {
        $this->students = $students;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->students;
    }

    public function headings(): array
    {
        return [
            'Internal Course Run ID',
            'Internal Enrolment ID',
            'Course Run ID',
            'Enrolment ID',
            'Course Name',
            'Course Start Date',
            'Course End Date',
            'Student Name',
            'NRIC',
            'Email',
            'Phone No',
            'Date Of Birth',
            'Nationality',
            'Company Name',
            'Company UEN',
            'Billing Email',
            'Company contact person',
            'Company contact number',
            'Company contact email',
            'Invoice #',
            'Payment Status',
            'Payment TPG Status',
            'Payment Mode',
            'Course Fees',
            'Amount Paid',
            'Amount Remaining',
            'Remarks',
            'Attendance',
            'Assessment',
            'Enrollment Status',
        ];
    }

    public function map($student): array
    {
        $enrollmentText = "Not Enrolled";
        if( $student->status == 0 ) {
            $refresherData = Refreshers::where('course_id', $student->course_id)->where('student_id', $student->student_id)->where('status', Refreshers::STATUS_ACCEPTED)->first();
            Log::info("course_id -- " . $student->course_id);
            Log::info("student_id -- " . $student->student_id);
            Log::info("refresherData -- " . $refresherData);
            if( $refresherData ){
                $enrollmentText = "Refreshers";
            } else {
                $enrollmentText = "Enrolled";
            }
        } else if( $student->status == 1 ) {
            $enrollmentText = "Enrolment Cancelled";
        }
        $paymentMode = "-";
        if( !is_null($student->payment_mode_company) ) {
            $paymentMode = $student->payment_mode_company;
        } elseif( !is_null($student->payment_mode_individual) ) {
            $paymentMode = $student->payment_mode_individual;
        } elseif( !is_null($student->other_paying_by) ) {
            $paymentMode = $student->other_paying_by;
        }
        $totalSession = count($student->attendances);
        $presentSessionCount = $student->attendances->where('is_present',1)->where('attendance_sync',1)->count();
        $percentProgress = 0;
            if($presentSessionCount && $totalSession != 0){
                $percentProgress = round($presentSessionCount / $totalSession * 100);
            }
        return [
            $student->course_id,
            $student->id,
            $student->tpgateway_id,
            $student->tpgateway_refno,
            $student->coursemainname,
            $student->course_start_date,
            $student->course_end_date,
            $student->student_name,
            $student->nric,
            $student->email,
            $student->mobile_no,
            $student->dob,
            $student->nationality,
            $student->company_name,
            $student->company_uen,
            $student->billing_email,
            $student->company_contact_person,
            $student->company_contact_person_number,
            $student->company_contact_person_email,
            $student->xero_invoice_number,
            getPaymentStatus($student->payment_status),
            getPaymentStatus($student->payment_tpg_status),
            $paymentMode,
            $student->amount,
            $student->amount_paid,
            $student->amount - $student->amount_paid,
            $student->remarks,
            $percentProgress .'%',
            $student->assessment,
            $enrollmentText,
       ];

    }
}
