<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Course;
use App\Services\Google;
use App\Services\CommonService;
use App\Services\CourseService;
use App\Facades\SchedulerLog;
use App\Notifications\TrainerFolderCreationEmail;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Revolution\Google\Sheets\Facades\Sheets;

class MakeTrainerFolderJob implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Course Id
    */
    private $courseId;


    /**
     * @var User Id
    */
    private $userId;

    public $timeout = 0;

    public $tries = 1;
	
	public $failOnTimeout = false;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($courseId, $userId)
    {
        $this->courseId = $courseId;
        $this->userId = $userId;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Google $google, Request $request)
    {
        $courseId = $this->courseId;
        SchedulerLog::info("Call Create Trainer Folder Handle Function");
        //$day8 = Carbon::now()->addDays(8)->format('Y-m-d');

        $course = Course::with('courseMain','maintrainerUser','session', 'courseMain.assessments')->where('id' , '=' , $courseId)->where('is_published', 1)->first();
        //dd($course);
        

        SchedulerLog::info("Course Data Start");
        SchedulerLog::info(print_r($course, true));
        SchedulerLog::info("Course Data End");

        $client = new \Google\Client();
        $client->setAuthConfig(config('google.service.file'));
        $client->addScope([\Google\Service\Drive::DRIVE, \Google\Service\Docs::DOCUMENTS]);
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');
        
        if(!empty($course)){

            $this->common = new CommonService;
            $course->trainer_job_status = "Processing";
            $course->save();
            
            //foreach($allCourses as $course) {

                SchedulerLog::info("Course Loop Start");
                $courseName = $course->courseMain->name;
                $courseName = str_replace("/","-",$courseName);
                $courseSkill = $course->courseMain->skill_code;
                $courseTriner = $course->maintrainerUser->name;
                $startDate = $course->course_start_date;
                $endDate = $course->course_end_date;

                // $trainerSignature = "https://i.postimg.cc/6q8j07tn/Equinet-Co-Logo.png";
                $trainerSignature = config('app.name').config('uploadpath.trainer_sign').'/'.$course->maintrainerUser->trainer_signature;


                $dateoutput = $this->common->makeSessionString($course->session);
                $courseOnlyDate = substr($dateoutput, 0, strpos($dateoutput, "("));
                $courseTime = substr($dateoutput, strpos($dateoutput, "("));
                $courseTime = str_replace(array('(',')'),'',$courseTime);
        
                $folderName = $courseName  ." - ".$dateoutput;
                
                //$mainFolder = env('GOOGLE_DRIVE_FOLDER_ID');
                $sharedDriveId = $course->courseMain->shared_drive_id;
                $mainFolder = ($course->courseMain->trainer_folder_id) ? $course->courseMain->trainer_folder_id : $sharedDriveId;
                Config::set('filesystems.disks.google.teamDriveId', $sharedDriveId);

                if(!empty($mainFolder)) {
                    $allFiles = collect(Storage::disk('google')->listContents('/', true));
                    $mainMeta = $allFiles->where('type', 'dir')->where('extraMetadata.id', $mainFolder)->first();
                    $mainPath = !empty($mainMeta['path']) ? $mainMeta['path'] . "/" : "";

                    if (!Storage::disk('google')->directoryExists($mainPath . $folderName)) {
                        if(Storage::disk('google')->makeDirectory($mainPath . $folderName)) {
                            SchedulerLog::info("Course Main Folder Created");

                            //Doc File
                            $docCopyFile = $allFiles->where('type', 'file')->where('extraMetadata.id', $course->courseMain->doc_file_id)->first();
                            $docCopyFileId = $docCopyFile['path'];
                            SchedulerLog::info("Reference Document File ID: ".$docCopyFileId);
                        
                            $docTemplateName = $folderName." - Template-For-Trainer";

                            //Sheet File
                            $sheetCopyFile = $allFiles->where('type', 'file')->where('extraMetadata.id', $course->courseMain->spreadsheet_file_id)->first();
                            $sheetCopyFileId = $sheetCopyFile['path'];
                            SchedulerLog::info("Reference Spreadsheet File ID: ".$sheetCopyFileId);

                            $attendanceFile = $allFiles->where('type', 'file')->where('extraMetadata.id', $course->courseMain->attendance_file_id)->first();
                            $attendanceFileId = $attendanceFile['path'];
                            SchedulerLog::info("Reference Attendance File ID: ".$attendanceFileId);

                            // Assessment File
                            $assessmentCopyFile = $allFiles->where('type', 'file')->where('extraMetadata.id', $course->courseMain->assessment_file_id)->first();
                            $assessmentCopyFileId = $assessmentCopyFile['path'];
                            SchedulerLog::info("Reference Assessment File ID: ".$assessmentCopyFileId);
                            
                            $sheetTemplateName = 'Template-'.$folderName;
                    

                            SchedulerLog::info("Start Copy Spreadsheet and Update Spreadsheet Data");
                            // 1. Copy Sheet File and Update Data
                            $newSheetFile = $mainPath . $folderName . "/" . $sheetTemplateName;
                            Storage::disk('google')->copy($sheetCopyFileId,$newSheetFile);
        
                            $sheetFileMeta = Storage::disk('google')->getAdapter()->getMetadata($newSheetFile); 
                            $newSheetFileId = $sheetFileMeta['extraMetadata']['id'];
                            
                            $updateCourseName = "Equinet Academy ".$courseName;
                            $updateCourseDate = "Date: ".$dateoutput;
                            $updateCourseTrainer = "Trainer: ".$courseTriner;

                            Sheets::spreadsheet($newSheetFileId)->range('A1')->update([[$updateCourseName]]);
                            Sheets::spreadsheet($newSheetFileId)->range('A3')->update([[$updateCourseDate]]);
                            Sheets::spreadsheet($newSheetFileId)->range('A4')->update([[$updateCourseTrainer]]);
                            Sheets::spreadsheet($newSheetFileId)->range('F4')->update([['=IMAGE("'.$trainerSignature.'",4,100,100)']],'USER_ENTERED');

                            SchedulerLog::info("End Update Spreadsheet Data");


                            SchedulerLog::info("Start Copy Attendance Spreadsheet and Update Attendance Data");
                            // 2. Copy Sheet File and Update Data
                            $attendanceTemplateName = 'Attendance - '.$folderName;
                            $attendanceFolderMeta = Storage::disk('google')->getAdapter()->getMetadata($mainPath . $folderName);
                            $attendanceFolderId = $attendanceFolderMeta['path'];             
                            $attendanceSheetFile = $attendanceFolderId."/".$attendanceTemplateName;
                            Storage::disk('google')->copy($attendanceFileId,$attendanceSheetFile);
        
                            $sheetFileMeta = Storage::disk('google')->getAdapter()->getMetadata($attendanceSheetFile); 
                            $newAttendanceFileId = $sheetFileMeta['extraMetadata']['id'];
                            
                            $updateCourseName = "Equinet Academy ".$courseName;
                            $updateCourseDate = "Date: ".$dateoutput;
                            $updateCourseTrainer = "Trainer: ".$courseTriner;

                            Sheets::spreadsheet($newAttendanceFileId)->range('A1')->update([[$updateCourseName]]);
                            Sheets::spreadsheet($newAttendanceFileId)->range('A3')->update([[$updateCourseDate]]);
                            Sheets::spreadsheet($newAttendanceFileId)->range('A4')->update([[$updateCourseTrainer]]);
                            Sheets::spreadsheet($newAttendanceFileId)->range('F4')->update([['=IMAGE("'.$trainerSignature.'",4,100,100)']],'USER_ENTERED');

                            SchedulerLog::info("End Update Attendance Data");

                            SchedulerLog::info("Get Course Enrolment Data Start");
                            // Append Student Data
                            $this->courseService = new CourseService;
                            $data = $this->courseService->getCourseRunFullDetailsById($course->id);
                            $courseEnrolments = $data->courseActiveEnrolments;
                            $courseRefreshers = $data->courseRefreshers;
                            foreach ($courseRefreshers as $refresher) {
                                if ($refresher->isAssessmentRequired == 1) {
                                    $courseEnrolments = $courseEnrolments->push($refresher);
                                }
                            }
                            $totalCourseEnrolments = $courseEnrolments;
                            SchedulerLog::info(print_r($courseEnrolments, true));

                            SchedulerLog::info("Get Course Enrolment Data End");
                            
                            $studentData = [];
                            $attendanceData = [];
                            $data_index = 7;
                            $colRange = 'A'.$data_index;
                            $sr = 0;

                            $tmp = "Assessment Records";
                            Storage::disk('google')->makeDirectory($mainPath . $folderName . "/" . $tmp);
                            $assessmentFolderMeta = Storage::disk('google')->getAdapter()->getMetadata($mainPath . $folderName . "/" . $tmp);
                            $assessmentFolderId = $assessmentFolderMeta['path'];

                            $columnData = array(
                                'Name',
                                'NRIC',
                                'Alias',
                                'ID Verification',
                                'Assessment Results',
                                'Remarks',
                                'Assessment  Record HyperLink - use this g doc to record your assessment findings',
                            );

                            $courseAssessments = $course->courseMain->assessments;
                            $courseAssessmentsCount = count($courseAssessments);

                            for($i=1; $i<=$courseAssessmentsCount; $i++){
                                $columnData[] = 'Assessment Paper '.$i.' - HyperLink';
                            }

                            Sheets::spreadsheet($newSheetFileId)->majorDimension('COLUMNS')->range($colRange)->append([$columnData]);


                            /* Build Attendance sheet Column Header Start */
                                
                                $attendanceColumnData = array(
                                    'S/No',
                                    'Name',
                                    'NRIC',
                                    'Email Address',
                                    'Alias',
                                    'Notes',
                                );

                                $sessionCount = 1;
                                $totalSessions = $course->session;
                                
                                foreach ($totalSessions as $session) {
                                    $sd = Carbon::parse($session->start_date)->format('d M Y');
                                    $st = Carbon::parse($session->start_time)->format('g:i A');
                                    $et = Carbon::parse($session->end_time)->format('g:i A');
                                    $attendanceColumnData[] = $sd." ".$st.'-'.$et;
                                    $sessionCount++;
                                }

                                Sheets::spreadsheet($newAttendanceFileId)->majorDimension('COLUMNS')->range($colRange)->append([$attendanceColumnData]);

                            /* Build Attendance sheet Column Header End */

                            foreach( $totalCourseEnrolments as $enrolment )
                            {
                                SchedulerLog::info("Start Append Enrolment Data");
                                $data_index++;
                                $sr++;
                                $range = 'A'.$data_index;
                                // 3. Copy Assessment Record Document
                                $course_date = Carbon::parse($startDate);
                                $courseDate = $course_date->format('d M Y');
                                $course_end_date = Carbon::parse($endDate);
                                $courseEndDate = $course_end_date->format('d M Y');
                                $assessmentTemplateName = $courseName ." - ".str_replace(["/", "'",","],"-",$enrolment->student->name)." - ".$courseDate." - Assessment Records";
                                
                                $newAssessmentFile = $assessmentFolderId."/".$assessmentTemplateName;
                                Storage::disk('google')->copy($assessmentCopyFileId,$newAssessmentFile);

                                $assessmentFileMeta = Storage::disk('google')->getAdapter()->getMetadata($newAssessmentFile); 
                                $newAssessmentFileId = $assessmentFileMeta['extraMetadata']['id'];
                                
                                $this->replaceSpecificText($client, $newAssessmentFileId, $trainerSignature);
                                
                                $studentData['name'] = $enrolment->student->name;
                                $studentData['nric'] = $enrolment->student->nric;
                                $studentData['alias'] = '';
                                $studentData['id_verification'] = '';
                                $studentData['assessment'] = (!is_null($enrolment->assessment)) ? getAssessmentName($enrolment->assessment) : "-";
                                $studentData['remarks'] = '';
                                $studentData['assessment_record'] ='https://docs.google.com/document/d/'.$newAssessmentFileId;

                                $shortNRIC = substr($enrolment->student->nric, -5);

                                $assessShortTitle = $course->courseMain->assessment_short_title;
                                $assessShortLink = $assessShortTitle.'/'.$shortNRIC;
                                $this->generateShortLink($assessShortLink, 'https://docs.google.com/document/d/'.$newAssessmentFileId);

                                $attendanceData['sr'] = $sr;
                                $attendanceData['name'] = $enrolment->student->name;
                                $attendanceData['nric'] = $enrolment->student->nric;
                                $attendanceData['email'] = $enrolment->student->email;
                                $attendanceData['alias'] = '';
                                $attendanceData['notes'] = '';
                                
                                $attendance = is_null($enrolment->attendance) ? NULL : json_decode($enrolment->attendance);
                                if(!empty($attendance))
                                {
                                    foreach($attendance as $key => $att)
                                    {
                                        $attendanceData[$key] = ($att->ispresent) ? TRUE : FALSE;
                                    }
                                }
                                
                                Sheets::spreadsheet($newAttendanceFileId)->range($range)->append([$attendanceData]);

                                foreach($courseAssessments as $key => $courseAssessment)
                                {
                                    $index = $key+1;
                                    $courseAbbr = $courseAssessment->short_url;
                                    $assessTitle = $courseAssessment->assessment_file_title;
                                    $assessmentFileMetaData = $allFiles->where('type', 'file')->where('extraMetadata.id', $courseAssessment->assessment_file_id)->first();
                                    $assessmentFileId = $assessmentFileMetaData['path'];

                                    $linkPath = $courseAbbr.'/'.$shortNRIC;

                                    $formatedTime = Carbon::parse($courseAssessment->start_time)->format('g:i A') ." - ".Carbon::parse($courseAssessment->end_time)->format('g:i A');

                                    /* Copy Multiple Assessment Start */
                                    $stuAssess = $assessmentFolderId."/".$assessTitle."-".str_replace(["/", "'",","],"-",$enrolment->student->name)."-".$courseDate;
                                    Storage::disk('google')->copy($assessmentFileId, $stuAssess);
                
                                    $assessFileMeta = Storage::disk('google')->getAdapter()->getMetadata($stuAssess); 
                                    $newAssessFileId = $assessFileMeta['extraMetadata']['id'];

                                    $assessUpdate = array(
                                        'StudentName' => $enrolment->student->name,
                                    );

                                    $this->replaceText($client, $newAssessFileId, $assessUpdate);

                                    $assessmenttoUpdate = array(
                                        'CourseName' => $courseName,
                                        'CourseSkill' => $courseSkill,
                                        'CourseEndDate' => $courseEndDate,
                                        'formatedTime'.$index => $formatedTime,
                                        'CourseTrainer' => $courseTriner,
                                        'TrainerSignature' => '',
                                        'StudentName' => $enrolment->student->name,
                                        'StudentNRIC' => convertNricToView($enrolment->student->nric)
                                    );
                                    
                                    $this->replaceText($client, $newAssessmentFileId, $assessmenttoUpdate);

                                    /* Copy Multiple Assessment End */

                                    $studentData['paper'.$index] = $this->generateShortLink($linkPath, 'https://docs.google.com/document/d/'.$newAssessFileId);
                                }
                                
                                Sheets::spreadsheet($newSheetFileId)->range($range)->append([$studentData]);
                            }

                            SchedulerLog::info("End Append Enrolment Data");
                            
                            SchedulerLog::info("Start Copy Document and Update Document Data");
                            // 2. Copy Doc File and Update Data
                            $newDocFile = $mainPath . $folderName. "/" . $docTemplateName;
                            Storage::disk('google')->copy($docCopyFileId, $newDocFile);
        
                            $docFileMeta = Storage::disk('google')->getAdapter()->getMetadata($newDocFile); 
                            $newDocFileId = $docFileMeta['extraMetadata']['id'];
                            
                            $idVerificationLink = 'https://docs.google.com/spreadsheets/d/'.$newSheetFileId;
                            $attendanceSheetLink = 'https://docs.google.com/spreadsheets/d/'.$newAttendanceFileId;

                            $toUpdate = array(
                                'CourseName' => $courseName,
                                'CourseDate' => $courseOnlyDate,
                                'CourseTime' => $courseTime,
                                'CourseTrainer' => $courseTriner,
                                'Id_Verification' => $idVerificationLink,
                                'Attendance_Sheet' => $attendanceSheetLink
                            );

                            $this->replaceText($client, $newDocFileId, $toUpdate);

                            $this->replaceSpecificText($client, $newDocFileId, $idVerificationLink);
                            $this->replaceSpecificText($client, $newDocFileId, $attendanceSheetLink);

                            SchedulerLog::info("End Copy Document and Update Document Data");

                        }
                            
                    }
                }
            //}
            $course->trainer_job_status = "Completed";
            $course->save();
            $user = User::find($this->userId);
            $user->notify(new TrainerFolderCreationEmail($user));
            SchedulerLog::info('Successfully created folder on Google Drive.');
        }
        else{
            SchedulerLog::info('No Course Found!');
        }

        SchedulerLog::info("Handle Function End......");
    }

    public function replaceText($client, $documentId, $toUpdate)
    {
        SchedulerLog::info("Call Replace Text function Start");
        SchedulerLog::info("Document ID for Text Replace: ".$documentId);

        SchedulerLog::info("Update Text Data Start");
        SchedulerLog::info(print_r($toUpdate, true));
        SchedulerLog::info("Update Text Data End ");

        $service = new \Google\Service\Docs($client);

        foreach($toUpdate as $key => $value)
        {
            SchedulerLog::info("Start Updating Data.. Enter in loop ");
            $e = new \Google\Service\Docs\SubstringMatchCriteria();
            $e->text = "{".$key."}";
            $e->setMatchCase(false);

            $requests[] = new \Google\Service\Docs\Request(array(
                'replaceAllText' => array(
                    'replaceText' => $value,
                    'containsText' => $e
                ),
                
            )); 
        }

        SchedulerLog::info("End Updating Data.. End loop ");
    
        $batchUpdateRequest = new \Google\Service\Docs\BatchUpdateDocumentRequest(array(
            'requests' => $requests
        ));

        $service->documents->batchUpdate($documentId, $batchUpdateRequest);

        SchedulerLog::info("Call Replace Text function End");
    }

    public function generateShortLink($path, $originalURL)
    {
        SchedulerLog::info("Call Generate short link function Start");

        SchedulerLog::info("Path: ".$path);
        SchedulerLog::info("Original Link: ".$path);

        $response = Http::withHeaders(['authorization' => 'sk_TksDQqwfcLaNtQQE', 'content-type' => 'application/json'])->post('https://api.short.io/links', ['originalURL' => $originalURL, 'path' => $path, 'domain' => 'eqnassess.link']);
        
        if( $response->successful() ) {
            $jsonData = $response->object();
            $result = $jsonData->shortURL;
        } else {
            $result = "";
        }

        return $result;

        SchedulerLog::info("Call Generate short link function End");
    }

    public function replaceSpecificText($client, $documentId, $value)
    {
        SchedulerLog::info("Call Replace specific text function Start");

        SchedulerLog::info("Update Document ID: ".$documentId);

        $service = new \Google\Service\Docs($client);
        $doc = $service->documents->get($documentId);

        $allText = [];
        foreach ($doc->body->content as $structuralElement) {
            if ($structuralElement->table) {
                foreach ($structuralElement->table->tableRows as $tableRows) {
                    foreach ($tableRows->tableCells as $tableCells) {
                        foreach ($tableCells->content as $content) {
                            foreach ($content->paragraph->elements as $paragraphElement) {
                                $allText[] = $paragraphElement->textRun->content;
                                if(strtolower(str_replace(" ","",$paragraphElement->textRun->content)) === strtolower(str_replace(" ","","Assessor’s Signature:\n"))){
                                    $requests = new \Google\Service\Docs\Request(array(
                                        'insertInlineImage' => array(
                                            'uri' => $value,
                                            'location' => array(
                                                'index' => $paragraphElement->startIndex + 23,
                                            ),
                                            'objectSize' => array(
                                                'height' => array(
                                                    'magnitude' => 110,
                                                    'unit' => 'PT',
                                                ),
                                                'width' => array(
                                                    'magnitude' => 110,
                                                    'unit' => 'PT',
                                                ),
                                            )
                                        )
                                    ));

                                    $batchUpdateRequest = new \Google\Service\Docs\BatchUpdateDocumentRequest(array(
                                        'requests' => array($requests)
                                    ));
                            
                                    $service->documents->batchUpdate($documentId, $batchUpdateRequest);
                                }

                                if($paragraphElement->textRun->content === $value){
                                    $requests = new \Google\Service\Docs\Request(array(
                                        'updateTextStyle' => array(
                                            'range' => array(
                                                'startIndex' => $paragraphElement->startIndex,
                                                'endIndex' => $paragraphElement->endIndex
                                            ),
                                            'textStyle' => array(
                                                'link' => array(
                                                    'url' => $value
                                                ),
                                            ),
                                            'fields' =>  'link'
                                        ),
                                    ));

                                    $batchUpdateRequest = new \Google\Service\Docs\BatchUpdateDocumentRequest(array(
                                        'requests' => array($requests)
                                    ));
                            
                                    $service->documents->batchUpdate($documentId, $batchUpdateRequest);
                                }
                            }
                        }
                    }
                }
            }
        }

        SchedulerLog::info("Call Replace specific text function End");
    }
}
