<?php

namespace App\Exports;

use App\Models\GrantLog;
use App\Models\Grant;
use App\Models\StudentEnrolment;
use Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class GrantLogsExport implements FromCollection, WithMapping , WithStrictNullComparison ,ShouldAutoSize,WithHeadings
{

    use Exportable;

    public $grantLogs;

    public function __construct($grantLogs)
    {
        $this->grantLogs = $grantLogs;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->grantLogs;
    }

    public function headings(): array
    {
        return [
            'Student Enrolment Id',
            'Student Name',
            'Description',
            'Grant Id',
            'Grant Ref. No.',
            'Notes',
            'Event',
            'Grant Notify',
            'Time',
            'Created By',
            'Updated By',
        ];
    }

    public function map($grantLogs): array
    {
        return [
            $this->getTpgEnr($grantLogs->student_enrolment_id),
            userNameFromEnrolment($grantLogs->student_enrolment_id),
            $this->makeDescription($grantLogs->event, $grantLogs->grant_refno, $grantLogs->student_enrolment_id),
            $grantLogs->grant_id,
            $grantLogs->grant_refno,
            $grantLogs->notes,
            $grantLogs->event,
            $grantLogs->grant_notify == 0 ? "No" : "Yes",
            date('d-m-Y h:i A', strtotime($grantLogs->created_at)),
            userName($grantLogs->created_by),
            userName($grantLogs->updated_by),
       ];

    }

    public function makeDescription($event, $grant_refno, $student_enrolment_id){
        $grant = Grant::where('grant_refno', $grant_refno)->first();
        if($event == 'Created'){
            return auditDescription($event, "Grant Created", $grant->grant_status, $grant_refno, "App\Models\Grant-Logs", $student_enrolment_id);
        }else{
            return auditDescription($event, "Grant Processing", $grant->grant_status, $grant_refno, "App\Models\Grant-Logs", $student_enrolment_id);
        }
    }

    public function getTpgEnr($enrolId){
        $getTpgEnr = StudentEnrolment::find($enrolId);
        return $getTpgEnr->tpgateway_refno;
    }
}
