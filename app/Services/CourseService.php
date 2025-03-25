<?php

namespace App\Services;

use Auth;
use Notification;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Course;
use App\Models\Trainer;
use App\Models\CourseMain;
use App\Models\Refreshers;
use Illuminate\Support\Str;
use App\Models\EmailTemplate;
use App\Services\UserService;
use App\Models\CourseSessions;
use App\Services\VenueService;
use App\Models\StudentEnrolment;
use App\Mail\EmailStudentExamLink;
use App\Services\TPGatewayService;
use Illuminate\Support\Facades\Log;
use App\Models\AssessmentExamCourse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use App\Mail\EmailStudentForNotifyExam;
use App\Models\StudentCourseAttendance;
use Illuminate\Support\Facades\Storage;
use Assessments\Student\Models\ExamAssessment;
use App\Notifications\CourseRunCancelNotifyEmail;
use Assessments\Student\Models\AssessmentMainExam;
use Assessments\Student\Models\AssessmentQuestions;
use Assessments\Student\Models\AssessmentSubmission;
use Assessments\Student\Models\AssessmentStudentExam;
use Assessments\Student\Models\AssessmentStudentRemarks;

class CourseService
{
    protected $course_model;

    public function __construct()
    {
        $this->course_model = new Course;
        $this->studentenrolment_model = new StudentEnrolment;
    }

    public function getAllCourse()
    {
        // return $this->course_model->select()->with(['courseMain']);
        $s = $this->course_model->select('courses.*', 'course_mains.name', 'course_mains.reference_number', 'users.name as trainername')
            ->join('course_mains', 'course_mains.id', '=', 'courses.course_main_id')
            ->join('users', 'users.id', '=', 'courses.maintrainer');
        // ->whereDate('course_end_date', '>=', Carbon::now());
        /*$thiscourseRunId = $request->get('courserun');
        if( $thiscourseRunId > 0 ) {
            $s->where('course_id', $thiscourseRunId);
        }*/
        return $s;
    }

    public function getUpcomingCourseSessionForEnrollment()
    {
        return $this->course_model->with(['session', 'courseMain'])
            ->whereDate('course_end_date', '>=', Carbon::now())
            ->whereColumn('registeredusercount', '<', 'intakesize')
            ->where('is_published', 1)
            ->get();
    }

    public function getAllCompletedCourse()
    {
        // return $this->course_model->select()->with(['courseMain']);
        $s = $this->course_model->select('courses.*', 'course_mains.name', 'course_mains.reference_number', 'users.name as trainername')
            ->join('course_mains', 'course_mains.id', '=', 'courses.course_main_id')
            ->join('users', 'users.id', '=', 'courses.maintrainer')
            ->whereDate('course_end_date', '<', Carbon::now());
        /*$thiscourseRunId = $request->get('courserun');
        if( $thiscourseRunId > 0 ) {
            $s->where('course_id', $thiscourseRunId);
        }*/
        return $s;
    }

    public function getAllCompletedCourseReport($request)
    {
        // return $this->course_model->select()->with(['courseMain']);
        $s = $this->course_model->select('courses.*', 'course_mains.name', 'course_mains.reference_number', 'users.name as trainername')
            ->join('course_mains', 'course_mains.id', '=', 'courses.course_main_id')
            ->join('users', 'users.id', '=', 'courses.maintrainer');
        $thiscourseRunId = $request->get('courserun');
        $startDate = $request->get('from');
        $endDate = $request->get('to');
        $modeoftraining = $request->get('modeoftraining');
        $trainers = $request->get('trainers');
        $is_published = $request->get('is_published');
        $mincancelusercount = $request->get('mincancelusercount');
        $maxcancelusercount = $request->get('maxcancelusercount');
        $minregisteredusercount = $request->get('minregisteredusercount');
        $maxregisteredusercount = $request->get('maxregisteredusercount');


        $coursemainId = $request->get('coursemain');
        if (is_array($coursemainId)) {
            $s->whereIn('courses.course_main_id', $coursemainId);
        }
        if ($startDate) {
            $s->whereDate('courses.course_start_date', '>=', date("Y-m-d", strtotime($startDate)));
        }
        if ($endDate) {
            $s->whereDate('courses.course_end_date', '<=', date("Y-m-d", strtotime($endDate)));
        }
        if ($thiscourseRunId > 0) {
            $s->where('courses.id', $thiscourseRunId);
        }
        if (is_array($modeoftraining)) {
            $s->whereIn('courses.modeoftraining', $modeoftraining);
        }
        if (is_array($trainers)) {
            $s->whereIn('courses.maintrainer', $trainers);
        }
        if (is_array($is_published)) {
            $s->whereIn('courses.is_published', $is_published);
        }
        if (isset($mincancelusercount)) {
            $s->where('courses.cancelusercount', '>=', $mincancelusercount);
        }
        if (isset($maxcancelusercount)) {
            $s->where('courses.cancelusercount', '<=', $maxcancelusercount);
        }

        if (isset($minregisteredusercount)) {
            $s->where('courses.registeredusercount', '>=', $minregisteredusercount);
        }
        if (isset($maxregisteredusercount)) {
            $s->where('courses.registeredusercount', '<=', $maxregisteredusercount);
        }
        /*$thiscourseRunId = $request->get('courserun');
        if( $thiscourseRunId > 0 ) {
            $s->where('course_id', $thiscourseRunId);
        }*/
        return $s;
    }

    public function getSignupsForCoursesWithFilter($request)
    {
        $s = $this->course_model->select('courses.*', 'course_mains.name', 'course_mains.reference_number', 'users.name as trainername')
            ->join('course_mains', 'course_mains.id', '=', 'courses.course_main_id')
            ->join('users', 'users.id', '=', 'courses.maintrainer');
        $thiscourseRunId = $request->get('courserun');
        $startDate = $request->get('from');
        $endDate = $request->get('to');

        $courserunid = $request->get('courserunid');


        $coursemainId = $request->get('coursemain');
        if (is_array($coursemainId)) {
            $s->whereIn('courses.course_main_id', $coursemainId);
        } else if ($coursemainId > 0) {
            $s->where('courses.course_main_id', $coursemainId);
        }

        $trainerId = $request->get('trainers');
        if (is_array($trainerId)) {
            $s->whereIn('courses.maintrainer', $trainerId);
        } else if ($trainerId > 0) {
            $s->where('courses.maintrainer', $trainerId);
        }

        $typeId = $request->get('modeoftraining');
        if (is_array($typeId)) {
            $s->whereIn('courses.modeoftraining', $typeId);
        } else if ($typeId > 0) {
            $s->where('courses.modeoftraining', $typeId);
        }

        if ($startDate) {
            $s->whereDate('courses.course_start_date', '>=', date("Y-m-d", strtotime($startDate)));
        }
        if ($endDate) {
            $s->whereDate('courses.course_end_date', '<=', date("Y-m-d", strtotime($endDate)));
        }

        if ($thiscourseRunId > 0) {
            $s->where('courses.id', $thiscourseRunId);
        }

        if ($courserunid > 0) {
            $s->where('courses.tpgateway_id', $courserunid);
        }
        $is_published = $request->get('is_published');
        if (is_array($is_published)) {
            $s->whereIn('courses.is_published', $is_published);
        }

        $course_type = $request->get('course_type');
        if (is_array($course_type)) {
            $s->whereIn('course_mains.course_type', $course_type);
        }
        return $s;
    }

    public function getAllCourseList()
    {
        return $this->course_model->get();
    }

    public function getAllCourseListWithRelation($relation)
    {
        return $this->course_model->with($relation)->get();
    }

    public function getAllCourseListWithRelationForBooking($relation, $useFor = 'softbooking')
    {
        $s = $this->course_model->with($relation)
            ->whereDate('course_start_date', '>', Carbon::now());
        if ($useFor != 'waitinglist') {
            $s->whereColumn('registeredusercount', '<', 'intakesize');
        }
        // ->where('is_published', 1)

        return $s->get();
    }

    public function getAllCourseRunByCourseMainId($id)
    {
        $s = $this->course_model->select('courses.*', 'course_mains.name', 'course_mains.reference_number', 'users.name as trainername')
            ->join('course_mains', 'course_mains.id', '=', 'courses.course_main_id')
            ->join('users', 'users.id', '=', 'courses.maintrainer')
            ->where('course_main_id', $id);

        return $s;
    }

    public function getCourseRunByCourseMainId($id)
    {
        $s = $this->course_model->select('id', 'tpgateway_id', 'course_start_date')
            ->where('course_main_id', $id)->get();
        return $s;
    }

    public function getCourseById($id)
    {
        return $this->course_model->find($id);
    }

    public function getCourseByTPGRunId($id)
    {
        return $this->course_model->where('tpgateway_id', $id)->first();
    }

    public function getSessionByCourseId($id)
    {
        return CourseSessions::where('course_id', $id)->first();
    }

    public function getAllSessionByCourseId($id)
    {
        return CourseSessions::where('course_id', $id)->get();
    }

    public function getCourseByIdWithSession($id)
    {
        return $this->course_model->with(['session', 'trainers'])->find($id);
    }

    public function getCourseRunFullDetailsById($id)
    {
        return $this->course_model->with(['session', 'trainers', 'courseMain', 'courseSoftBooking', 'courseDocuments', 'maintrainerUser', 'venue', 'courseWaitingList', 'courseRefreshers', 'courseActiveEnrolments', 'courseCancelledEnrolments', 'courseHoldingEnrolments', 'courseTasks'])->find($id);
    }

    public function getTrainerCourseRuns()
    {
        $user = \Auth::user();
        $s = $this->course_model->select('courses.*', 'course_mains.name', 'course_mains.reference_number', 'users.name as trainername')
            ->join('course_mains', 'course_mains.id', '=', 'courses.course_main_id')
            ->join('users', 'users.id', '=', 'courses.maintrainer')
            ->where('courses.maintrainer', $user->id);
        return $s;
        // $courseIds = \DB::table('course_trainer')->where('trainer_id', $user->id)->pluck('course_id');
        // dd($courseIds);
        // return $this->course_model->with(['session', 'trainers', 'courseMain', 'courseEnrolments'])->whereIn('id', $courseIds);
        // return $this->course_model->with(['session', 'trainers', 'courseMain', 'courseEnrolments'])->get();
    }

    public function getCourseByIdStudentEnrolment($id)
    {
        return $this->course_model->with(['session', 'trainers', 'courseMain', 'courseActiveEnrolments', 'courseActiveRefreshers'])->find($id);
    }

    public function getCourseByIdAndStudentEnrolment($id)
    {
        return $this->course_model->with(['courseMain', 'courseActiveEnrolments'])->find($id);
    }

    public function getCourseByIdAndStudentEnrolmentByIds($courseid, $enrolledids)
    {
        $enrolledids = explode(",", $enrolledids);

        return $this->studentenrolment_model->with('courseRun.courseMain')->whereIn('id', $enrolledids)->get();
        // return $this->course_model->with(['courseMain', 'courseEnrolments'])->find($courseid);
    }

    public function getCourseByIdAndStudentEnrolmentByIdsOnReport($enrolledids)
    {
        $enrolledids = explode(",",$enrolledids);

        return $this->studentenrolment_model->with('courseRun.courseMain')->whereIn('id', $enrolledids)->get();
        // return $this->course_model->with(['courseMain', 'courseEnrolments'])->find($courseid);
    }

    public function getCourseByIdAndStudentrefResherByIds($courseid, $refresherIds)
    {
        $refresherIds = explode(",", $refresherIds);
        return Refreshers::with('course.courseMain')->whereIn('id', $refresherIds)->get();
    }

    public function getSessionById($id)
    {
        return CourseSessions::findOrFail($id);
    }

    public function getCourseWithSession()
    {
        return $this->course_model->with(['session', 'courseMain'])->get();
    }

    public function getCourseWithSessionForEnrollment()
    {
        return $this->course_model->with(['session', 'courseMain'])
            ->whereDate('registration_opening_date', '<=', Carbon::now())
            ->whereDate('registration_closing_date', '>=', Carbon::now())
            ->whereColumn('registeredusercount', '<', 'intakesize')
            ->where('is_published', 1)
            ->get();
    }

    public function getCourseWithSessionForEnrollmentEdit($course)
    {
        return $this->course_model->select(
            'courses.id',
            'courses.tpgateway_id',
            'courses.course_start_date',
            'course_mains.name',
            'course_mains.reference_number',
            'course_mains.course_type_id'
        )
            ->join('course_mains', 'course_mains.id', '=', 'courses.course_main_id')
            ->where('courses.course_main_id', $course->course_main_id)
            ->whereDate('courses.course_start_date', '>=', Carbon::now())
            ->whereColumn('registeredusercount', '<', 'intakesize')
            ->where('courses.is_published', 1)
            ->orWhere('courses.id', $course->id)
            ->orderBy('course_mains.name', 'ASC')
            ->orderBy('courses.course_start_date', 'ASC')
            ->get();
    }

    public function getAllCourseRun()
    {
        return $this->course_model->with(['courseMain'])->select(['id', 'tpgateway_id', 'course_start_date', 'course_end_date', 'course_main_id'])->get();
    }

    public function getAllSession()
    {
        //return $this->course_model->with(['session'])->get();
        return CourseSessions::get();
    }

    public function registerCourse($request)
    {
        \Log::info('Add Course Run Function call');
        // dd($request);
        $userService = new UserService;
        $venueService = new VenueService;

        $record = $this->course_model;

        $record->venue_id                   = $request->get('venue_id');
        $record->maintrainer                = $request->get('coursetrainers');
        $record->course_main_id             = $request->get('course_main_id');
        $record->registration_opening_date  = $request->get('registration_opening_date');
        $record->registration_closing_date  = $request->get('registration_closing_date');
        if ($request->has('registration_closing_time')) {
            $record->registration_closing_time  = $request->get('registration_closing_time');
        }
        $record->course_start_date          = $request->get('course_start_date');
        $record->course_end_date            = $request->get('course_end_date');
        $record->course_link                = $request->get('course_link');
        $record->meeting_id                 = $request->get('meeting_id');
        $record->meeting_pwd                = $request->get('meeting_pwd');
        // $record->schinfotype_code           = $request->get('schinfotype_code');
        // $record->schinfotype_desc           = $request->get('schinfotype_desc');
        // $record->sch_info                   = $request->get('sch_info');
        $record->coursevacancy_code         = $request->get('coursevacancy_code');
        // $record->coursevacancy_desc         = $request->get('coursevacancy_desc');

        $record->course_remarks         = $request->get('course_remarks');

        $record->schinfotype_code           = "01";
        // $record->coursevacancy_code         = "A";
        $record->coursevacancy_desc         = getCourseVacancy($record->coursevacancy_code);
        $record->minintakesize              = $request->get('minintakesize');
        $record->intakesize                 = $request->get('intakesize');
        $record->threshold                  = $request->get('threshold');
        $record->modeoftraining             = $request->get('modeoftraining');
        $record->is_published               = $request->get('publish');
        $record->created_by                 = Auth::Id();
        $record->updated_by                 = Auth::Id();

        if ($request->hasfile('coursefileimage')) {
            // upload file
            $uploadedFile = $request->file('coursefileimage');
            $filename = "course_" . rand(1000, 9999) . "_" . time() . "." . $uploadedFile->getClientOriginalExtension();

            Storage::disk('public_course_uploads')->putFileAs(
                config('uploadpath.course_img'),
                $uploadedFile,
                $filename
            );
            $record->coursefileimage = $filename;
        } else {
            $record->coursefileimage = 'no-image.jpg';
        }

        $record->save();
        $courseId = $record->id;

        $tpSessions = [];
        if (!in_array($record->modeoftraining, skipVenue())) {
            $sessionsCount = count($request->get('sessions'));
            if ($sessionsCount > 0) {
                for ($i = 0; $i < $sessionsCount; $i++) {
                    $sessionSch = convertToSessionDates($request->get('sessions')[$i]['session_schedule']);
                    $sessionSchTpg = convertToSessionDatesOnly($request->get('sessions')[$i]['session_schedule']);
                    // add to sessions
                    $sessions = new CourseSessions;
                    $sessions->course_id             = $courseId;
                    $sessions->start_date            = $sessionSch['start_date'];
                    $sessions->end_date              = $sessionSch['end_date'];
                    $sessions->start_time            = $sessionSch['start_time'];
                    $sessions->end_time              = $sessionSch['end_time'];
                    $sessions->session_schedule      = $request->get('sessions')[$i]['session_schedule'];
                    $sessions->session_mode          = $request->get('sessions')[$i]['session_mode'];
                    $sessions->created_by            = Auth::Id();
                    $sessions->updated_by            = Auth::Id();
                    $sessions->save();

                    $tpSessions[$i]['startDate']        = convertToTPDate($sessionSchTpg['start_date']);
                    $tpSessions[$i]['endDate']          = convertToTPDate($sessionSchTpg['end_date']);
                    $tpSessions[$i]['startTime']        = convertToTPTime($sessionSchTpg['start_time']);
                    $tpSessions[$i]['endTime']          = convertToTPTime($sessionSchTpg['end_time']);
                    //$tpSessions[$i]['modeOfTraining']   = $record->modeoftraining;
                    $tpSessions[$i]['modeOfTraining']   = $request->get('sessions')[$i]['session_mode'];
                    // get venue details
                    $venue = $venueService->getVenueById($record->venue_id);
                    $tpSessions[$i]['venue'] = [
                        "block"             => $venue->block,
                        "street"            => $venue->street,
                        "floor"             => $venue->floor,
                        "unit"              => $venue->unit,
                        "building"          => $venue->building,
                        "postalCode"        => $venue->postal_code,
                        "room"              => $venue->room,
                        "wheelChairAccess"  => $venue->wheelchairaccess ? true : false,
                        "primaryVenue"      => false,
                    ];
                }
            }
        }

        $common = new CommonService;
        //$scheduleInfo = $common->makeSessionScheduleInfoString($record->session);
        $scheduleInfo = $common->makeSessionString($record->session);
        $record->sch_info                   = $scheduleInfo;
        $record->schinfotype_desc           = getCourseType($record->courseMain->course_type) . " (" . $record->course_start_date . " - " . $record->course_end_date . ")";
        $record->save();

        $record->trainers()->attach($request->get('courseassistanttrainers'));
        $tpTrainers = [];
        // get trainer details from our table
        $trainerData = $userService->getUserByIdWithTrainer($record->maintrainer);
        /*$tpTrainers[0] = [
            'indexNumber' => 0,
            'trainerType' => [
                "code"  => 1,
                "description" => "Existing"
            ],
            'id'    => $trainerData->trainer->tpgateway_id,
            'inTrainingProviderProfile' => false,
        ];*/
        $qual = [];
        $qualfy = !is_null($trainerData->trainer->qualifications) ? json_decode($trainerData->trainer->qualifications) : [];
        if (count($qualfy)) {
            foreach ($qualfy as $key => $qualification) {
                $qual[$key]['ssecEQA']['code'] = $qualification->level;
                $qual[$key]['description'] = $qualification->description;
            }
        }

        $rolesArr = [];
        $roles = !is_null($trainerData->trainer->role_type) ? json_decode($trainerData->trainer->role_type) : [];
        if (count($roles)) {
            foreach ($roles as $key => $r) {
                $rolesArr[$key]['role']['id'] = (int)$r;
                $rolesArr[$key]['role']['description'] = getTrainerRoles($r);
            }
        }

        // Encode profile avatar
        $full_profile_image_path = (file_exists(public_path('storage/') . config('uploadpath.user_profile_storage') . "/" . $trainerData->profile_avatar)) ?
            public_path('storage/') . config('uploadpath.user_profile_storage') . "/" . $trainerData->profile_avatar :
            public_path('storage/') . config('uploadpath.user_profile_storage') . "/default.jpg";

        $profile_image = file_get_contents($full_profile_image_path);
        $profile_image_content = base64_encode($profile_image);

        $tpTrainers[0]["trainer"] = [
            "id"        => $trainerData->trainer->tpgateway_id,
            "name"      => $trainerData->name,
            "email"     => $trainerData->email,
            "indexNumber" => 0,
            'trainerType' => [
                "code"  => "1",
                "description" => "Existing"
            ],
            'linkedSsecEQAs' => $qual,
            'experience' => $trainerData->trainer->experience,
            'linkedInURL' => $trainerData->trainer->linkedInURL,
            'salutationId' => $trainerData->trainer->salutationId,
            'domainAreaOfPractice' => $trainerData->trainer->domainAreaOfPractice,
            'inTrainingProviderProfile' => false,
            'photo' => [
                'name' => $trainerData->profile_avatar,
                'content' => $profile_image_content
            ],
            'idNumber' => $trainerData->trainer->id_number,
            'idType' => [
                'code' => $trainerData->trainer->id_type,
                'description' => getTrainerIdType($trainerData->trainer->id_type)
            ],
            'roles' => $rolesArr,
        ];

        if ($record->courseMain->course_type == CourseMain::COURSE_TYPE_WSQ) {
            // add to TP Gateway
            $tpgatewayReq = new TPGatewayService;
            $req_data = $tpgatewayReq->buildAddCourseRunRequest($record, $tpSessions, $tpTrainers);
            $courseRes = $tpgatewayReq->addCourseRunToTpGatewayNew($req_data);
            \Log::info('TPGateway Response');
            \Log::info(print_r($courseRes, true));
            $record->courseRunResponse = json_encode($courseRes);
            $record->save();
            if (isset($courseRes->status) && $courseRes->status == 200) {
                $record->tpgateway_id = $courseRes->data->runs[0]->id;
                $record->save();
            }
        }
        \Log::info('Add Course Run Function End');
        return $record;
    }

    public function addCourseRunToTPGateway($id)
    {
        $tpgatewayReq = new TPGatewayService;
        $userService = new UserService;
        $venueService = new VenueService;
        $record = $this->getCourseByIdWithSession($id);
        $tpSessions = [];
        if (!in_array($record->modeoftraining, skipVenue())) {
            $sessionsCount = count($record->session);
            if ($sessionsCount > 0) {

                foreach ($record->session as $i => $sess) {
                    $sessionSch = convertToSessionDatesOnly($sess->session_schedule);
                    // dd($sessionSch);
                    // add to sessions
                    $tpSessions[$i]['startDate']        = convertToTPDate($sessionSch['start_date']);
                    $tpSessions[$i]['endDate']          = convertToTPDate($sessionSch['end_date']);
                    $tpSessions[$i]['startTime']        = convertToTPTime($sessionSch['start_time']);
                    $tpSessions[$i]['endTime']          = convertToTPTime($sessionSch['end_time']);
                    //$tpSessions[$i]['modeOfTraining']   = $record->modeoftraining;
                    $tpSessions[$i]['modeOfTraining']   = $sess->session_mode;
                    // get venue details
                    $venue = $venueService->getVenueById($record->venue_id);
                    $tpSessions[$i]['venue'] = [
                        "block"             => $venue->block,
                        "street"            => $venue->street,
                        "floor"             => $venue->floor,
                        "unit"              => $venue->unit,
                        "building"          => $venue->building,
                        "postalCode"        => $venue->postal_code,
                        "room"              => $venue->room,
                        "wheelChairAccess"  => $venue->wheelchairaccess ? true : false,
                        "primaryVenue"      => false,
                    ];
                }
            }
        }
        $tpTrainers = [];
        // get trainer details from our table
        $trainerData = $userService->getUserByIdWithTrainer($record->maintrainer);
        /* $tpTrainers[0] = [
            'indexNumber' => 0,
            'trainerType' => [
                "code"  => 1,
                "description" => "Existing"
            ],
            'id'    => $trainerData->trainer->tpgateway_id,
            'inTrainingProviderProfile' => false,
        ];*/
        $qual = [];
        $qualfy = !is_null($trainerData->trainer->qualifications) ? json_decode($trainerData->trainer->qualifications) : [];
        if (count($qualfy)) {
            foreach ($qualfy as $key => $qualification) {
                $qual[$key]['ssecEQA']['code'] = $qualification->level;
                $qual[$key]['description'] = $qualification->description;
            }
        }

        $rolesArr = [];
        $roles = !is_null($trainerData->trainer->role_type) ? json_decode($trainerData->trainer->role_type) : [];
        if (count($roles)) {
            foreach ($roles as $key => $r) {
                $rolesArr[$key]['role']['id'] = (int)$r;
                $rolesArr[$key]['role']['description'] = getTrainerRoles($r);
            }
        }

        // Encode profile avatar
        $full_profile_image_path = (file_exists(public_path('storage/') . config('uploadpath.user_profile_storage') . "/" . $trainerData->profile_avatar)) ?
            public_path('storage/') . config('uploadpath.user_profile_storage') . "/" . $trainerData->profile_avatar :
            public_path('storage/') . config('uploadpath.user_profile_storage') . "/default.jpg";

        $profile_image = file_get_contents($full_profile_image_path);
        $profile_image_content = base64_encode($profile_image);

        $tpTrainers[0]["trainer"] = [
            "id"        => $trainerData->trainer->tpgateway_id,
            "name"      => $trainerData->name,
            "email"     => $trainerData->email,
            "indexNumber" => 0,
            'trainerType' => [
                "code"  => "1",
                "description" => "Existing"
            ],
            'linkedSsecEQAs' => $qual,
            'experience' => $trainerData->trainer->experience,
            'linkedInURL' => $trainerData->trainer->linkedInURL,
            'salutationId' => $trainerData->trainer->salutationId,
            'domainAreaOfPractice' => $trainerData->trainer->domainAreaOfPractice,
            'inTrainingProviderProfile' => false,
            'photo' => [
                'name' => $trainerData->profile_avatar,
                'content' => $profile_image_content
            ],
            'idNumber' => $trainerData->trainer->id_number,
            'idType' => [
                'code' => $trainerData->trainer->id_type,
                'description' => getTrainerIdType($trainerData->trainer->id_type)
            ],
            'roles' => $rolesArr,
        ];

        $req_data = $tpgatewayReq->buildAddCourseRunRequest($record, $tpSessions, $tpTrainers);
        $courseRes = $tpgatewayReq->addCourseRunToTpGatewayNew($req_data);
        $record->courseRunResponse = json_encode($courseRes);
        $record->save();
        $resstatus = false;
        if (isset($courseRes->status) && $courseRes->status == 200) {
            $resstatus = true;
            $record->tpgateway_id = $courseRes->data->runs[0]->id;
            $record->save();
        }
        return ['status' => $resstatus, 'res' => $record];
    }

    public function updateCourse($id, $request)
    {
        \Log::info('Add Course Run Function call');
        $record = $this->getCourseById($id);
        if ($record) {
            $userService = new UserService;
            $venueService = new VenueService;
            $record->venue_id                   = $request->get('venue_id');
            $record->maintrainer                = $request->get('coursetrainers');
            $record->course_main_id             = $request->get('course_main_id');
            $record->registration_opening_date  = $request->get('registration_opening_date');
            $record->registration_closing_date  = $request->get('registration_closing_date');
            if ($request->has('registration_closing_time')) {
                $record->registration_closing_time  = $request->get('registration_closing_time');
            }
            $record->course_start_date          = $request->get('course_start_date');
            $record->course_end_date            = $request->get('course_end_date');
            $record->course_link                = $request->get('course_link');
            $record->meeting_id                 = $request->get('meeting_id');
            $record->meeting_pwd                = $request->get('meeting_pwd');
            // $record->schinfotype_code           = $request->get('schinfotype_code');
            // $record->schinfotype_desc           = $request->get('schinfotype_desc');
            // $record->sch_info                   = $request->get('sch_info');
            $record->coursevacancy_code         = $request->get('coursevacancy_code');
            $record->coursevacancy_desc         = getCourseVacancy($record->coursevacancy_code);
            $record->course_remarks         = $request->get('course_remarks');
            // $record->coursevacancy_desc         = $request->get('coursevacancy_desc');
            $record->minintakesize              = $request->get('minintakesize');
            $record->intakesize                 = $request->get('intakesize');
            $record->threshold                  = $request->get('threshold');
            $record->modeoftraining             = $request->get('modeoftraining');

            $record->is_published               = $request->get('publish');
            $record->updated_by                 = Auth::Id();

            if ($request->hasfile('coursefileimage')) {
                // upload file
                $uploadedFile = $request->file('coursefileimage');
                $filename = "course_" . rand(1000, 9999) . "_" . time() . "." . $uploadedFile->getClientOriginalExtension();

                Storage::disk('public_course_uploads')->putFileAs(
                    config('uploadpath.course'),
                    $uploadedFile,
                    $filename
                );
                $record->coursefileimage = $filename;
            }

            $record->save();
            $courseId = $record->id;
            CourseSessions::where('course_id', $courseId)->delete();
            if (!in_array($record->modeoftraining, skipVenue())) {
                if ($request->has('sessions')) {
                    $sessionsCount = count($request->get('sessions'));
                    if ($sessionsCount > 0) {
                        for ($i = 0; $i < $sessionsCount; $i++) {
                            $sessionSch = convertToSessionDates($request->get('sessions')[$i]['session_schedule']);
                            $sessionSchTpg = convertToSessionDatesOnly($request->get('sessions')[$i]['session_schedule']);
                            // add to sessions
                            $sessions = new CourseSessions;
                            $sessions->course_id             = $courseId;
                            $sessions->start_date            = $sessionSch['start_date'];
                            $sessions->end_date              = $sessionSch['end_date'];
                            $sessions->start_time            = $sessionSch['start_time'];
                            $sessions->end_time              = $sessionSch['end_time'];
                            $sessions->session_schedule      = $request->get('sessions')[$i]['session_schedule'];
                            $sessions->session_mode          = $request->get('sessions')[$i]['session_mode'];
                            $sessions->created_by            = Auth::Id();
                            $sessions->updated_by            = Auth::Id();
                            $sessions->save();

                            /*$tpSessions[$i]['startDate']        = convertToTPDate($sessionSchTpg['start_date']);
                            $tpSessions[$i]['endDate']          = convertToTPDate($sessionSchTpg['end_date']);
                            $tpSessions[$i]['startTime']        = convertToTPTime($sessionSchTpg['start_time']);
                            $tpSessions[$i]['endTime']          = convertToTPTime($sessionSchTpg['end_time']);
                            $tpSessions[$i]['modeOfTraining']   = $record->modeoftraining;
                            $tpSessions[$i]['action']           = "update";
                            $tpSessions[$i]['sessionId']        = $record->courseMain->reference_number."-".$record->tpgateway_id."-S".$sessionIndex;
                            // get venue details
                            $venue = $venueService->getVenueById($record->venue_id);
                            $tpSessions[$i]['venue'] = [
                                "block"             => $venue->block,
                                "street"            => $venue->street,
                                "floor"             => $venue->floor,
                                "unit"              => $venue->unit,
                                "building"          => $venue->building,
                                "postalCode"        => $venue->postal_code,
                                "room"              => $venue->room,
                                "wheelChairAccess"  => $venue->wheelchairaccess ? true : false,
                                "primaryVenue"      => false,
                            ];*/
                        }

                        $tpSessions = [];
                        $tpgatewayReq = new TPGatewayService;
                        $sessionRes = $tpgatewayReq->getCourseSessionsFromTpGateway($record->tpgateway_id, $record->courseMain->reference_number);
                        if (isset($sessionRes->status) && $sessionRes->status == 200) {
                            $tpSessionsCount = count($sessionRes->data->sessions);
                            $venue = $venueService->getVenueById($record->venue_id);

                            if ($sessionsCount > $tpSessionsCount) {
                                foreach ($sessionRes->data->sessions as $key => $tpgsession) {
                                    for ($ts = 0; $ts < $sessionsCount; $ts++) {
                                        $sessionSchTpg = convertToSessionDatesOnly($request->get('sessions')[$ts]['session_schedule']);
                                        $tpSessions[$ts]['startDate']        = convertToTPDate($sessionSchTpg['start_date']);
                                        $tpSessions[$ts]['endDate']          = convertToTPDate($sessionSchTpg['end_date']);
                                        $tpSessions[$ts]['startTime']        = convertToTPTime($sessionSchTpg['start_time']);
                                        $tpSessions[$ts]['endTime']          = convertToTPTime($sessionSchTpg['end_time']);
                                        //$tpSessions[$ts]['modeOfTraining']   = $record->modeoftraining;
                                        $tpSessions[$ts]['modeOfTraining']   = $request->get('sessions')[$ts]['session_mode'];

                                        if ($ts == $key) {
                                            $tpSessions[$ts]['sessionId'] = $tpgsession->id;
                                            $tpSessions[$ts]['action'] = 'update';
                                        }

                                        $tpSessions[$ts]['venue'] = [
                                            "block"             => $venue->block,
                                            "street"            => $venue->street,
                                            "floor"             => $venue->floor,
                                            "unit"              => $venue->unit,
                                            "building"          => $venue->building,
                                            "postalCode"        => $venue->postal_code,
                                            "room"              => $venue->room,
                                            "wheelChairAccess"  => $venue->wheelchairaccess ? true : false,
                                            "primaryVenue"      => false,
                                        ];
                                    }
                                }
                            } else if ($sessionsCount < $tpSessionsCount) {
                                $tpSessionArray = json_decode(json_encode($sessionRes->data->sessions), true);
                                $result = array_diff_key($tpSessionArray, $request->get('sessions'));
                                foreach ($tpSessionArray as $key => $tpgsession) {
                                    for ($ts = 0; $ts < $sessionsCount; $ts++) {
                                        $sessionSchTpg = convertToSessionDatesOnly($request->get('sessions')[$ts]['session_schedule']);
                                        $tpSessions[$ts]['startDate']        = convertToTPDate($sessionSchTpg['start_date']);
                                        $tpSessions[$ts]['endDate']          = convertToTPDate($sessionSchTpg['end_date']);
                                        $tpSessions[$ts]['startTime']        = convertToTPTime($sessionSchTpg['start_time']);
                                        $tpSessions[$ts]['endTime']          = convertToTPTime($sessionSchTpg['end_time']);
                                        //$tpSessions[$ts]['modeOfTraining']   = $record->modeoftraining;
                                        $tpSessions[$ts]['modeOfTraining']   = $request->get('sessions')[$ts]['session_mode'];

                                        if ($ts == $key) {
                                            $tpSessions[$ts]['sessionId'] = $tpgsession['id'];
                                            $tpSessions[$ts]['action'] = 'update';
                                        }

                                        $tpSessions[$ts]['venue'] = [
                                            "block"             => $venue->block,
                                            "street"            => $venue->street,
                                            "floor"             => $venue->floor,
                                            "unit"              => $venue->unit,
                                            "building"          => $venue->building,
                                            "postalCode"        => $venue->postal_code,
                                            "room"              => $venue->room,
                                            "wheelChairAccess"  => $venue->wheelchairaccess ? true : false,
                                            "primaryVenue"      => false
                                        ];
                                    }

                                    if (!empty($result)) {
                                        foreach ($result as $key => $tpgsession) {
                                            $tpSessions[$key]['startDate']        = $tpgsession['startDate'];
                                            $tpSessions[$key]['endDate']          = $tpgsession['endDate'];
                                            $tpSessions[$key]['startTime']        = $tpgsession['startTime'];
                                            $tpSessions[$key]['endTime']          = $tpgsession['endTime'];
                                            $tpSessions[$key]['modeOfTraining']   = $tpgsession['modeOfTraining'];
                                            $tpSessions[$key]['sessionId']        = $tpgsession['id'];
                                            $tpSessions[$key]['action']           = 'delete';

                                            $tpSessions[$key]['venue'] = [
                                                "block"             => $venue->block,
                                                "street"            => $venue->street,
                                                "floor"             => $venue->floor,
                                                "unit"              => $venue->unit,
                                                "building"          => $venue->building,
                                                "postalCode"        => $venue->postal_code,
                                                "room"              => $venue->room,
                                                "wheelChairAccess"  => $venue->wheelchairaccess ? true : false,
                                                "primaryVenue"      => false,
                                            ];
                                        }
                                    }
                                }
                            } else {
                                foreach ($sessionRes->data->sessions as $key => $tpgsession) {
                                    for ($ts = 0; $ts < $sessionsCount; $ts++) {

                                        $sessionSchTpg = convertToSessionDatesOnly($request->get('sessions')[$ts]['session_schedule']);
                                        $tpSessions[$ts]['startDate']        = convertToTPDate($sessionSchTpg['start_date']);
                                        $tpSessions[$ts]['endDate']          = convertToTPDate($sessionSchTpg['end_date']);
                                        $tpSessions[$ts]['startTime']        = convertToTPTime($sessionSchTpg['start_time']);
                                        $tpSessions[$ts]['endTime']          = convertToTPTime($sessionSchTpg['end_time']);
                                        //$tpSessions[$ts]['modeOfTraining']   = $record->modeoftraining;
                                        $tpSessions[$ts]['modeOfTraining']   = $request->get('sessions')[$ts]['session_mode'];

                                        if ($ts == $key) {
                                            $tpSessions[$ts]['sessionId'] = $tpgsession->id;
                                            $tpSessions[$ts]['action'] = 'update';
                                        }

                                        $tpSessions[$ts]['venue'] = [
                                            "block"             => $venue->block,
                                            "street"            => $venue->street,
                                            "floor"             => $venue->floor,
                                            "unit"              => $venue->unit,
                                            "building"          => $venue->building,
                                            "postalCode"        => $venue->postal_code,
                                            "room"              => $venue->room,
                                            "wheelChairAccess"  => $venue->wheelchairaccess ? true : false,
                                            "primaryVenue"      => false,
                                        ];
                                    }
                                }
                            }
                        }
                        /* Get Session from TPGateway End */
                    }
                }
            }

            $record->trainers()->detach();
            $record->trainers()->attach($request->get('courseassistanttrainers'));
            $tpTrainers = [];
            // get trainer details from our table
            $trainerData = $userService->getUserByIdWithTrainer($record->maintrainer);

            /*
            $qual = [];
            $qualfy = !is_null($trainerData->trainer->qualifications) ? json_decode($trainerData->trainer->qualifications) : [];
            if( count($qualfy) ) {
                foreach ($qualfy as $key => $qualification) {
                    $qual[$key]['ssecEQA']['code'] = $qualification->level;
                    $qual[$key]['description'] = $qualification->description;
                }
            }

            $rolesArr = [];
            $roles = !is_null($trainerData->trainer->role_type) ? json_decode($trainerData->trainer->role_type) : [];
            if( count($roles) ) {
                foreach ($roles as $key => $r) {
                    $rolesArr[$key]['id'] = (int)$r;
                    $rolesArr[$key]['description'] = getTrainerRoles($r);
                }
            }
            
            // Encode profile avatar
            $full_profile_image_path = (file_exists( public_path('storage/').config('uploadpath.user_profile_storage')."/". $trainerData->profile_avatar)) ? 
                                    public_path('storage/').config('uploadpath.user_profile_storage')."/".$trainerData->profile_avatar : 
                                    public_path('storage/').config('uploadpath.user_profile_storage')."/default.jpg" ;

            $profile_image = file_get_contents($full_profile_image_path);
            $profile_image_content = base64_encode($profile_image); */

            $tpTrainers[0]["trainer"] = [
                "id"        => $trainerData->trainer->tpgateway_id,
                'idNumber' => $trainerData->trainer->id_number,
                "name"      => $trainerData->name,
                "email"     => $trainerData->email,
                'trainerType' => [
                    "code"  => "1",
                    "description" => "Existing"
                ],
                'inTrainingProviderProfile' => true,

                /*
                Note: No need to pass all trainer data to TPGateway while we perform an update operation and if we set trainerType is "1 - Existing"
                */

                /*"indexNumber" => 0,
                'linkedSsecEQAs' => $qual,
                'experience' => $trainerData->trainer->experience,
                'linkedInURL' => $trainerData->trainer->linkedInURL,
                'salutationId' => $trainerData->trainer->salutationId,
                'domainAreaOfPractice' => $trainerData->trainer->domainAreaOfPractice,
                
                'photo' => [
                    'name' => $trainerData->profile_avatar,
                    'content' => $profile_image_content
                ],
                'idType' => [
                    'code' => $trainerData->trainer->id_type,
                    'description' => getTrainerIdType($trainerData->trainer->id_type)
                ],
                'roles' => $rolesArr, */
            ];

            // add to TP Gateway
            $tpgatewayReq = new TPGatewayService;
            $req_data = $tpgatewayReq->buildUpdateCourseRunRequest($record, $tpSessions, $tpTrainers, 'update');
            //dd(json_encode($req_data));
            $courseRes = $tpgatewayReq->udpateCourseRunToTpGatewayNew($record->tpgateway_id, $req_data);
            //dd(json_encode($courseRes));
            \Log::info('TPGateway Response');
            \Log::info(print_r($courseRes, true));
            $record->courseRunResponse = json_encode($courseRes);
            $record->save();

            return $record;
        }
        \Log::info('Add Course Run Function End');
        return false;
    }

    public function cancelCourseRun($courserun_id, $request)
    {

        $record = $this->getCourseById($courserun_id);

        if ($record) {
            if (!empty($record->tpgateway_id)) {

                // add to TP Gateway
                $tpgatewayReq = new TPGatewayService;

                // Build req data for TPGateway
                $req_data['course'] = [
                    "courseReferenceNumber" => $record->courseMain->reference_number,
                    'trainingProvider' => [
                        "uen" => config('settings.tpgateway_uenno')
                    ],
                ];
                $req_data['course']['run'] = [
                    "action" => "delete"
                ];

                \Log::info('TPGateway Course Run Payload Start');
                \Log::info(print_r($req_data, true));
                \Log::info('TPGateway Course Run Payload End');
                //dd(json_encode($req_data));
                $courseRes = $tpgatewayReq->udpateCourseRunToTpGatewayNew($record->tpgateway_id, $req_data);
                //dd($courseRes);
                $record->courseRunResponse = json_encode($courseRes);
                $record->save();
                \Log::info('TPGateway Course Run Cancel API Responce');
                \Log::info(print_r($courseRes, true));
                if (isset($courseRes->status) && $courseRes->status == 200) {
                    $record->is_published = 2;
                    $record->save();

                    $msg = "The below Course Run is canceled";
                    Notification::route('mail', getenv('ADMIN_NOTIFICATION_EMAIL'))->notify(new CourseRunCancelNotifyEmail($record->id, $record->courseMain->name, $record->tpgateway_id, $msg));
                    return ['status' => TRUE, 'msg' => 'Course run Canceled Successfully '];
                }
            } else {
                $record->is_published = 2;
                $record->save();
                $msg = "Below Course Run has No TPGateway ID found! Course run locally canceled";
                Notification::route('mail', getenv('ADMIN_NOTIFICATION_EMAIL'))->notify(new CourseRunCancelNotifyEmail($record->id, $record->courseMain->name, $record->tpgateway_id, $msg));
                return ['status' => FALSE, 'msg' => 'No TPGateway ID found! Course run locally canceled'];
            }

            $msg = "An Error occurred during cancel course run. The below Course Run is not canceled";
            Notification::route('mail', getenv('ADMIN_NOTIFICATION_EMAIL'))->notify(new CourseRunCancelNotifyEmail($record->id, $record->courseMain->name, $record->tpgateway_id, $msg));

            return ['status' => FALSE, 'msg' => 'An Error occurred! Please try again later'];
        }

        return ['status' => FALSE, 'msg' => 'Course Run data not found'];
    }

    public function updateSessionForCourseRun($sessionId, $tpgId)
    {
        $session = CourseSessions::find($sessionId);
        if (isset($session->id)) {
            $session->tpgateway_id = $tpgId;
            $session->save();
        }
    }

    public function saveCourseRunAttendanceAssessment($id, $request)
    {
        // dd($request);
        try {
            $result = $this->getCourseByIdStudentEnrolment($id);
            $studentService = new \App\Services\StudentService;

            foreach ($result->courseActiveEnrolments as $s => $student) {
                $studentAttendances = [];
                foreach ($result->session as $session) {
                    $tmp = [
                        'session_id'    => $session->id,
                        'tpgId'         => $session->tpgateway_id,
                        'start_date'    => $session->start_date,
                        'end_date'      => $session->end_date,
                        'start_time'    => $session->start_time,
                        'ispresent'     => $request->get("attendance_" . $student->id . "_" . $session->id),
                        'remark'        => $request->get("att_remark_" . $student->id . "_" . $session->id),
                        'att_sync'      => $request->get("att_sync_" . $student->id . "_" . $session->id),
                    ];
                    array_push($studentAttendances, $tmp);

                    /* Add Course Attendance Start */
                    $this->addUpdateCourseAttendance($session, $student->id, $request);
                    /* Add Course Attendance End */
                }
                $stuEnrol = $studentService->getStudentEnrolmentById($student->id);
                $stuEnrol->attendance = json_encode($studentAttendances);
                $stuEnrol->assessment = $request->get("assessment_" . $student->id);
                $stuEnrol->payment_tpg_status = $request->get("payment_" . $student->id);
                $stuEnrol->assessment_remark = $request->get("assessment_remark_" . $student->id);
                if (is_null($stuEnrol->assessment_date)) {
                    $stuEnrol->assessment_date = $result->course_end_date;
                }
                $stuEnrol->save();
                // attendance_{{$student->id}}_{{$session->id}}
            }
            foreach ($result->courseActiveRefreshers as $s => $student) {
                $studentAttendances = [];
                foreach ($result->session as $session) {
                    $tmp = [
                        'tpgId'         => $session->tpgateway_id,
                        'start_date'    => $session->start_date,
                        'end_date'      => $session->end_date,
                        'start_time'    => $session->start_time,
                        'ispresent'     => $request->get("attendance_refreshers_" . $student->id . "_" . $session->id),
                        'remark'        => $request->get("att_remark_refreshers_" . $student->id . "_" . $session->id),
                        'att_sync'      => $request->get("att_sync_refreshers_" . $student->id . "_" . $session->id),
                    ];
                    array_push($studentAttendances, $tmp);

                    /* Add Course Attendance Start */
                    $this->addUpdateCourseAttendance($session, $student->id, $request);
                    /* Add Course Attendance End */
                }
                $stuEnrol = Refreshers::find($student->id);
                $stuEnrol->attendance = json_encode($studentAttendances);
                $stuEnrol->assessment = $request->get("assessment_refreshers_" . $student->id);
                $stuEnrol->assessment_remark = $request->get("assessment_refreshers_remark_" . $student->id);
                if (is_null($stuEnrol->assessment_date)) {
                    $stuEnrol->assessment_date = $result->course_end_date;
                }
                $stuEnrol->save();
                // attendance_{{$student->id}}_{{$session->id}}
            }
            return TRUE;
        } catch (Exception $e) {
            return ['status' => false, 'msg' => $e->getMessage()];
        }
    }

    public function addUpdateCourseAttendance($session, $student_id, $request)
    {
        StudentCourseAttendance::updateOrCreate(
            [
                'session_id' => isset($session->id) ? $session->id : null,
                'student_enrolment_id' => isset($student_id) ? $student_id : null,
            ],
            [
                'session_id' => $session->id,
                'student_enrolment_id' => $student_id,
                'course_id' => $session->course_id,
                'is_present' => ($request->get("attendance_" . $student_id . "_" . $session->id) != null) ? $request->get("attendance_" . $student_id . "_" . $session->id) : '',
            ]
        );
    }

    public function showGrant()
    {
        $tpgatewayReq = new TPGatewayService;
        if ($res = $tpgatewayReq->displayGrantCalculator()) {
            dd($res);
            /*$record->tpgateway_id = $res['data']['runs'][0]['id'];
            $record->save();*/
        }
        return $record;
    }

    public function getCourseRunIdsForMainCourse($courseMainId)
    {
        $_today = date('Y-m-d');
        $_prevYear = date('Y') - 1;
        $_prevDate = $_prevYear . date('-m-d');
        // dd($_prevDate);
        $courseRunIds = $this->course_model->where('course_main_id', $courseMainId)
            ->whereDate('course_start_date', '>=', $_prevDate)->pluck('id');
        return $courseRunIds;
    }

    public function getRefreshersById($id)
    {
        return Refreshers::with(['course', 'student'])->find($id);
    }

    public function checkRefreshersAdded($req)
    {
        $courseId = $req->get('course_id');
        $studentId = $req->get('student_id');
        return Refreshers::where('course_id', $courseId)
            ->where('student_id', $studentId)
            ->where('status', '!=', Refreshers::STATUS_CANCELLED)
            ->first();
    }

    public function registerRefreshersCourse($req)
    {
        $record = new Refreshers;
        $record->course_id              = $req->get('course_id');
        $record->student_id             = $req->get('student_id');
        $record->isAttendanceRequired   = $req->has('isAttendanceRequired') ? 1 : 0;
        $record->isAssessmentRequired   = $req->has('isAssessmentRequired') ? 1 : 0;
        $record->status                 = $req->get('status');
        $record->notes                  = $req->get('notes');
        $record->created_by             = Auth::Id();
        $record->updated_by             = Auth::Id();
        $record->save();
        return $record;
    }

    public function updateRefreshersCourse($id, $req)
    {
        $record = Refreshers::find($id);
        if ($record) {
            $record->isAttendanceRequired   = $req->has('isAttendanceRequired') ? 1 : 0;
            $record->isAssessmentRequired   = $req->has('isAssessmentRequired') ? 1 : 0;
            $record->status                 = $req->get('status');
            $record->notes                  = $req->get('notes');
            $record->updated_by             = Auth::Id();
            $record->save();
            return $record;
        }
        return FALSE;
    }

    public function getStudentRefreshersByStudentIdWithRealtionData($id)
    {
        return Refreshers::where('student_id', $id)->with(['course'])->get();
    }

    public function getCourserRunDocument($id)
    {
        return \App\Models\CourseDocuments::find($id);
    }

    public function storeUploadCourseRunDocuments($request)
    {
        $courseDocument = new \App\Models\CourseDocuments;
        $courseRunId = $request->get('courserun_id');
        $courseDocument->course_id  = $courseRunId;
        $courseDocument->category   = $request->get('category_id');
        if ($request->hasfile('file_name')) {
            // upload file
            $uploadedFile = $request->file('file_name');
            $filename = "course_" . $courseRunId . "_" . rand(1000, 9999) . "_" . time() . "." . $uploadedFile->extension();

            Storage::disk('public_document_uploads')->putFileAs(
                config('uploadpath.course_document'),
                $uploadedFile,
                $filename
            );
            $courseDocument->file_size  = $uploadedFile->getSize();
            $courseDocument->mime_type  = $uploadedFile->getMimeType();
            $courseDocument->file_name  = $filename;
        }
        $courseDocument->created_by     = Auth::Id();
        $courseDocument->updated_by     = Auth::Id();
        $courseDocument->save();
        return $courseDocument;
    }

    public function updateUploadCourseRunDocuments($request)
    {
        $courseRunId = $request->get('courserun_id');
        $courseDocId = $request->get('coursedoc_id');
        $courseDocument = \App\Models\CourseDocuments::find($courseDocId);
        if (!empty($courseDocument->id)) {
            $courseDocument->course_id  = $courseRunId;
            $courseDocument->category   = $request->get('category_id');
            if ($request->hasfile('file_name')) {
                // upload file
                $uploadedFile = $request->file('file_name');
                $filename = "course_" . $courseRunId . "_" . rand(1000, 9999) . "_" . time() . "." . $uploadedFile->extension();

                Storage::disk('public_document_uploads')->putFileAs(
                    config('uploadpath.course_document'),
                    $uploadedFile,
                    $filename
                );
                $courseDocument->file_size  = $uploadedFile->getSize();
                $courseDocument->mime_type  = $uploadedFile->getMimeType();
                $courseDocument->file_name  = $filename;
            }
            $courseDocument->updated_by     = Auth::Id();
            $courseDocument->save();
            return $courseDocument;
        }
        return FALSE;
    }

    public function getAllStudentEnrolmentPerMonthCourseRuns($request)
    {
        $s = $this->course_model->select('course_mains.name as coursemainname', 'courses.course_start_date', 'courses.id as course_id', \DB::raw('count(student_enrolments.id) as `data`'), \DB::raw("DATE_FORMAT(student_enrolments.created_at, '%m-%Y') new_date"),  \DB::raw('YEAR(student_enrolments.created_at) year, MONTH(student_enrolments.created_at) month'))
            ->join('student_enrolments', 'courses.id', '=', 'student_enrolments.course_id')
            ->leftjoin('course_mains', 'course_mains.id', '=', 'courses.course_main_id');
        $coursemainId = $request->get('coursemain');
        $startDate = $request->get('from');
        $endDate = $request->get('to');
        if (is_array($coursemainId)) {
            $s->whereIn('courses.course_main_id', $coursemainId);
        } else if ($coursemainId > 0) {
            $s->where('courses.course_main_id', $coursemainId);
        }

        if ($startDate) {
            $s->whereDate('courses.course_start_date', '>=', $startDate);
        }
        if ($endDate) {
            $s->whereDate('courses.course_end_date', '<=', $endDate);
        }
        $s->groupby('courses.id', 'year', 'month')->orderBy('month');
        return $s;
    }

    // Import Course Run
    public function importCourseRun($courseMainId, $request)
    {
        $record = $this->course_model;
        $record->venue_id                   = $request['venue_id'];
        $record->maintrainer                = $request['coursetrainers'];
        $record->course_main_id             = $courseMainId;
        $record->registration_opening_date  = $request['registration_opening_date'];
        $record->registration_closing_date  = $request['registration_closing_date'];
        if ($request['registration_closing_time']) {
            $record->registration_closing_time  = $request['registration_closing_time'];
        }
        $record->course_start_date          = $request['course_start_date'];
        $record->course_end_date            = $request['course_end_date'];

        $record->schinfotype_code           = "01";
        $record->coursevacancy_code         = "A";
        $record->coursevacancy_desc         = getCourseVacancy($record->coursevacancy_code);
        $record->minintakesize              = 1;
        $record->intakesize                 = $request['intakesize'];
        $record->threshold                  = 0;
        $record->modeoftraining             = $request['modeoftraining'];
        $record->is_published               = 1;
        if (!empty($request['tpgateway_id'])) {
            $record->tpgateway_id           = $request['tpgateway_id'];
        }
        $record->created_by                 = Auth::Id();
        $record->updated_by                 = Auth::Id();

        $record->coursefileimage = 'no-image.jpg';

        $record->save();
        $record->schinfotype_desc           = getCourseType($record->courseMain->course_type) . " (" . $record->course_start_date . " - " . $record->course_end_date . ")";
        $record->save();
        return $record->id;
    }

    public function importCourseRunSession($courseSession)
    {
        $record = $this->getCourseById($courseSession['course_run']);

        if (!empty($record->id)) {
            if (!in_array($record->modeoftraining, skipVenue())) {
                // add to sessions
                $sessions = new CourseSessions;
                $sessions->course_id             = $courseSession['course_run'];
                $sessions->start_date            = $courseSession['start_date'];
                $sessions->end_date              = $courseSession['end_date'];
                $sessions->start_time            = $courseSession['start_time'];
                $sessions->end_time              = $courseSession['end_time'];
                $sessions->session_schedule      = $courseSession['session_schedule'];
                $sessions->session_mode          = $courseSession['session_mode'];
                $sessions->created_by            = Auth::Id();
                $sessions->updated_by            = Auth::Id();
                $sessions->save();
            }
            $common = new CommonService;
            $scheduleInfo = $common->makeSessionScheduleInfoString($record->session);
            $record->sch_info                   = $scheduleInfo;
            $record->save();
            return $record->id;
        }
        return FALSE;
    }

    // Sync From TP Gateway
    public function addCourseRunFromTPG($course, $sessions)
    {
        $res = ['status' => false];
        // get venue
        $venue = \App\Models\Venue::where('building', $course->run->venue->building)->first();
        if (empty($venue->id)) {
            $res['msg'] = 'Venue name not found : ' . $course->venue->building . ', ' . $course->venue->postal_code;
            return $res;
        }
        // get main trainer
        if (!empty($course->run->linkCourseRunTrainer[0])) {
            $train = $course->run->linkCourseRunTrainer[0]->trainer;
            $trainer = User::where('email', $train->email)->userTrainer()->first();
            if (empty($trainer->id)) {
                $res['msg'] = 'Trainer not found with email: ' . $train->email;
                return $res;
            }
        } else {
            $res['msg'] = 'No Trainer assigned to this course in TP Gateway';
            return $res;
        }
        // get main course
        $courseMain = CourseMain::where('reference_number', $course->referenceNumber)->first();
        if (empty($courseMain->id)) {
            $res['msg'] = 'Course Reference not found in our database';
            return $res;
        }

        $record = $this->course_model;

        $record->tpgateway_id               = $course->run->id;
        $record->venue_id                   = $venue->id;
        $record->maintrainer                = $trainer->id;
        $record->course_main_id             = $courseMain->id;
        $record->registration_opening_date  = convertFromTPDate($course->run->registrationOpeningDate);
        $record->registration_closing_date  = convertFromTPDate($course->run->registrationClosingDate);
        $record->course_start_date          = convertFromTPDate($course->run->courseStartDate);
        $record->course_end_date            = convertFromTPDate($course->run->courseEndDate);
        $record->schinfotype_code           = $course->run->scheduleInfoType->code;
        $record->schinfotype_desc           = $course->run->scheduleInfoType->description;
        $record->sch_info                   = $course->run->scheduleInfo;
        $record->coursevacancy_code         = $course->run->courseVacancy->code;
        $record->coursevacancy_desc         = $course->run->courseVacancy->description;

        $record->minintakesize              = 1;
        $record->intakesize                 = $course->run->intakeSize;
        $record->threshold                  = $course->run->threshold;
        $record->modeoftraining             = $course->run->modeOfTraining;
        $record->is_published               = 1;
        $record->created_by                 = Auth::Id();
        $record->updated_by                 = Auth::Id();

        $record->coursefileimage = 'no-image.jpg';

        $record->save();
        $courseId = $record->id;

        if (!in_array($record->modeoftraining, skipVenue())) {
            $sessionsCount = count($sessions);
            if ($sessionsCount > 0) {
                foreach ($sessions as $session) {
                    // add to sessions
                    $sessions = new CourseSessions;
                    $sessions->course_id             = $courseId;
                    $sessions->tpgateway_id          = $session->id;
                    $sessions->start_date            = convertFromTPDate($session->startDate);
                    $sessions->end_date              = convertFromTPDate($session->endDate);
                    $sessions->start_time            = $session->startTime;
                    $sessions->end_time              = $session->endTime;
                    $sessions->session_schedule      = convertToSessionSchedule($session);
                    $sessions->created_by            = Auth::Id();
                    $sessions->updated_by            = Auth::Id();
                    $sessions->save();
                }
            }
        }
        $res['status'] = true;
        $res['msg'] = "Course run added successfully";
        return $res;
    }

    public function searchCourseRunsAjax($q)
    {
        $record = $this->course_model->select(
            'courses.id',
            'courses.tpgateway_id',
            'courses.course_start_date',
            'course_mains.name',
            'course_mains.reference_number',
            'course_mains.course_type_id'
        )
            ->join('course_mains', 'course_mains.id', '=', 'courses.course_main_id');
        if (!empty($q)) {
            $record->where('courses.tpgateway_id', 'like', '%' . $q . '%')
                ->orWhere('courses.course_start_date', 'like', '%' . $q . '%')
                ->orWhere('course_mains.name', 'like', '%' . $q . '%')
                ->orWhere('course_mains.reference_number', 'like', '%' . $q . '%')
                ->whereColumn('registeredusercount', '<', 'intakesize')
                ->limit(7);
        } else {
            $record->whereDate('course_end_date', '>=', Carbon::now())
                ->whereColumn('registeredusercount', '<', 'intakesize')
                ->where('is_published', 1);
        }
        $courseRuns = $record->orderBy('course_mains.name', 'ASC')
            ->orderBy('courses.course_start_date', 'ASC')
            ->get();
        $ret = [];
        foreach ($courseRuns as $course) {
            $ret[] = [
                "id"    => $course->id,
                "coursetype" => $course->course_type_id,
                "text"  => $course->tpgateway_id . " (" . $course->course_start_date . ') - ' . $course->name . " " . $course->reference_number,
            ];
        }
        return $ret;
    }

    public function multi_diff($arr1, $arr2)
    {
        $result = array();
        foreach ($arr1 as $k => $v) {
            if (!isset($arr2[$k])) {
                $result[$k] = $v;
            } else {
                if (is_array($v) && is_array($arr2[$k])) {
                    $diff = multi_diff($v, $arr2[$k]);
                    if (!empty($diff))
                        $result[$k] = $diff;
                }
            }
        }
        return $result;
    }

    //Exam releted course services
    public function getAllExams($request)
    {
        $startDate = $request->get('from');
        $endDate = $request->get('to');

        $user = Auth::user()->id;
        $today = Carbon::today()->format('Y-m-d');
        
        $allExamCourseRuns = Course::select('courses.*', 'tms_exam_assement_course_runs.is_assigned')
                            ->with('courseMain')
                            ->join('tms_exam_course_mains','tms_exam_course_mains.course_main_id','=','courses.course_main_id')
                            ->leftjoin('tms_exam_assement_course_runs','tms_exam_assement_course_runs.course_run_id','=','courses.id')
                            ->whereHas('courseMain', function($query) use($user){
                                $query->whereHas('trainers', function($query) use($user){
                                    $query->where('coursemain_trainer.trainer_id', $user);
                                });
                            })
                            ->where('courses.is_published', Course::STATUS_PUBLISHED)
                            ->groupBy('courses.id');
                            
                            // ->where('courses.course_end_date', '>=', $today)

        if ($startDate) {
            $allExamCourseRuns->whereDate('courses.course_start_date', '>=', date("Y-m-d", strtotime($startDate)));
        }
        if ($endDate) {
            $allExamCourseRuns->whereDate('courses.course_end_date', '<=', date("Y-m-d", strtotime($endDate)));
        }
        
        return $allExamCourseRuns;
    }

    /*public function getAllExamsForAdmin()
    {
        $user = Auth::user()->id;
        // $allExams = AssessmentMainExam::whereHas('courses', function($query) use ($user){
        //     $query->where('courses.maintrainer', $user->id);
        // })->get();
        $allExams = Course::select('courses.*', 'tms_exam_assement_course_runs.is_assigned') // Select columns from the courses table
                        ->with('courseMain')
                        ->join('tms_exam_course_mains','tms_exam_course_mains.course_main_id','=','courses.course_main_id')
                        ->leftjoin('tms_exam_assement_course_runs','tms_exam_assement_course_runs.course_run_id','=','courses.id')
                        ->groupBy('courses.id')->get();

        return $allExams;
    }*/

    public function searchTrainerCourseRunAjax($q)
    {
        $user = Auth::user();
        $record = $this->course_model->select(
            'courses.id',
            'courses.tpgateway_id',
            'courses.course_start_date',
            'course_mains.name',
            'course_mains.reference_number',
            'course_mains.course_type_id'
        )
            ->join('course_mains', 'course_mains.id', '=', 'courses.course_main_id');

        if (!empty($q)) {
            $record->where('courses.tpgateway_id', 'like', '%' . $q . '%')
                ->orWhere('courses.course_start_date', 'like', '%' . $q . '%')
                ->orWhere('course_mains.name', 'like', '%' . $q . '%')
                ->orWhere('course_mains.reference_number', 'like', '%' . $q . '%')
                ->whereColumn('registeredusercount', '<', 'intakesize')
                ->where('courses.maintrainer', '=', $user->id)
                ->limit(7);
        } else {
            $record->whereDate('course_end_date', '>=', Carbon::now())
                ->whereColumn('registeredusercount', '<', 'intakesize')
                ->where('courses.maintrainer', '=', $user->id)
                ->where('is_published', 1);
        }
        $courseRunsTrainer = $record->orderBy('course_mains.name', 'ASC')
            ->orderBy('courses.course_start_date', 'ASC')
            ->get();
        $ret = [];
        foreach ($courseRunsTrainer as $course) {
            $ret[] = [
                "id"    => $course->id,
                "coursetype" => $course->course_type_id,
                "text"  => $course->tpgateway_id . " (" . $course->course_start_date . ') - ' . $course->name . " " . $course->reference_number,
            ];
        }
        return $ret;
    }

    public function getStudentQuestionAnswer($assessmentId, $studentEnr){
        
        $previewExam = AssessmentQuestions::withTrashed()->with('submitedSubmission')->join('tms_student_submitted_assessments', function($join) use ($assessmentId, $studentEnr){
            $join->on('tms_questions.id' ,'=', 'tms_student_submitted_assessments.question_id')
                ->where('tms_student_submitted_assessments.assessment_id', $assessmentId)
                ->where('tms_student_submitted_assessments.student_enr_id' ,'=', $studentEnr);  
        })
        ->leftjoin('tms_student_results', function($join) use ($studentEnr){
            $join->on('tms_student_results.assessment_id' ,'=', 'tms_student_submitted_assessments.assessment_id')
                ->where('tms_student_results.student_enr_id', $studentEnr);
        })
        ->orderBy('tms_questions.id', 'ASC')
        ->get([
            'tms_questions.question',
            'tms_questions.question_weightage',
            'tms_questions.id',
            'tms_student_submitted_assessments.question_id AS answer_que_id',
            'tms_student_submitted_assessments.submitted_answer AS submitted_answer',
            'tms_student_submitted_assessments.answer_image AS submitted_image',
            'tms_student_submitted_assessments.id AS submission_id',
            'tms_student_submitted_assessments.student_enr_id AS student_enr_id',
            'tms_student_submitted_assessments.assessment_id AS assessment_id',
            'tms_student_submitted_assessments.is_reviewed AS is_reviewed',
            'tms_student_submitted_assessments.is_pass AS is_pass',
            'tms_student_results.is_passed AS is_passed',
            'tms_student_results.assessment_recovery',
            'tms_student_results.assessment_reschedule_note',
            // 'tms_student_results.is_pass AS is_passed_qa'
        ]);
        // dd($previewExam);
        return $previewExam;
    }


    public function assessmentById($examId){
        $assesmentById = AssessmentExamCourse::join('tms_exam_assessments', function($join) use ($examId) {
                            $join->on('tms_exam_assement_course_runs.assessment_id', '=','tms_exam_assessments.id')
                                    ->where('tms_exam_assessments.exam_id', $examId);
                        })
                        ->with(['assessment', 'courseRuns']);
        return $assesmentById;
    }

    public function mainAssessmentById($examId){
        $mainAssessments = ExamAssessment::where('exam_id', $examId);
        return $mainAssessments;
    }
}
