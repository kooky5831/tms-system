<?php

use App\Models\User;
use App\Models\Course;
use App\Models\CourseMain;
use App\Models\CourseTags;
use App\Models\Refreshers;
use Illuminate\Support\Carbon;
use App\Models\StudentEnrolment;
use App\Models\Student;
use Assessments\Student\Models\AssessmentMainCourse;

if( !function_exists('setflashmsg') ) {
    function setflashmsg($msg,$type = 1) {
        if($type == 1) {
            request()->session()->flash('notify-success', $msg);
        } else {
            request()->session()->flash('notify-error', $msg);
        }
    }
}

if( !function_exists('adminDefaultPwd') ) {
    function adminDefaultPwd() {
        return '123456';
    }
}

if( !function_exists('generateRandomString') ) {
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

// This function will return a random
// string of specified length
if( !function_exists('random_strings') ) {
    function random_strings($length_of_string) {

        // md5 the timestamps and returns substring
        // of specified length
        return substr(md5(time()), 0, $length_of_string);
    }
}

if( !function_exists('getAgeFromDOB') ) {
    function getAgeFromDOB($dob) {
        # object oriented
        // $from = new DateTime($dob);
        // $to   = new DateTime('today');
        // echo $from->diff($to)->y;

        # procedural
        return date_diff(date_create($dob), date_create('today'))->y;
    }
}

if( !function_exists('getSalutations') ) {
    function getSalutations($ret = NULL) {
        $data = [1 => 'Mr', 2 => 'Ms', 3 => 'Mdm', 4 => 'Mrs', 5 => 'Dr', 6 => 'Prof'];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if( !function_exists('cleanAmount') ) {
    function cleanAmount($string) {
        $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\.\-]/', '', $string); // Removes special chars.
    }
}

if( !function_exists('getLearningMode') ) {
    function getLearningMode($ret = NULL) {
        $data = [
            'f2f'       => "Face-to-Face Classroom",
            'online'    => "Online-Based Classroom"
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if( !function_exists('getAttAssCategory') ) {
    function getAttAssCategory($ret = NULL) {
        $data = [
            1   => "Attendance",
            2   => "Assessment"
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if( !function_exists('getModeOfTraining') ) {
    function getModeOfTraining($ret = NULL) {
        $data = [
            1 =>  "Classroom",
            2 =>  "Asynchronous eLearning",
            3 =>  "In-house",
            4 =>  "On-the-Job",
            5 =>  "Practical / Practicum",
            6 =>  "Supervised Field",
            7 =>  "Traineeship",
            8 =>  "Assessment",
            9 =>  "Synchronous eLearning"
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if( !function_exists('getModeOfPayment') ) {
    function getModeOfPayment($ret = NULL) {
        $data = [
            1 =>  "Cheque",
            2 =>  "Others (e.g Vendors@gov)",
            3 =>  "iBanking",
            4 =>  "Cash",
            5 =>  "PayPal",
            6 =>  "Credit Card",
            7 =>  "Debit Card",
            8 =>  "SkillsFuture Credits",
            9 =>  "PSEA",
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if( !function_exists('getModeOfFeeStatus') ) {
    function getModeOfFeeStatus($ret = NULL) {
        $data = [
            1 =>  "Not Paid",
            2 =>  "Pending",
            3 =>  "Fully",
            4 =>  "Partial",
            5 =>  "Cancelled"
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if( !function_exists('dynamicTriggerVarNames') ) {
    function dynamicTriggerVarNames($ret = NULL) {
        $data = [
            '{studentname}'         =>  "{studentname}",
            '{coursedate}'          =>  "{coursedate}",
            '{coursetime}'          =>  "{coursetime}",
            '{coursename}'          =>  "{coursename}",
            '{staffname}'           =>  "{staffname}",
            '{coursemeetinglink}'   =>  "{coursemeetinglink}",
            '{coursemeetingId}'     =>  "{coursemeetingId}",
            '{coursemeetingPwd}'    =>  "{coursemeetingPwd}",
            '{assessmentexamurl}'   =>  "{assessmentexamurl}",
            '{user_id}'             =>  "{user_id}",
            '{password}'            =>  "{password}",
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if(!function_exists('invoiceDynamicTriggersVariables')){
    function invoiceDynamicTriggersVariables($ret = NULL){        
        $data = [
            '{studentname}'         =>  "{studentname}",
            '{coursedate}'          =>  "{coursedate}",
            '{coursetime}'          =>  "{coursetime}",
            '{coursename}'          =>  "{coursename}",
            '{totalFees}'           =>  "{totalFees}",
            '{companyName}'         =>  "{companyName}",
            '{remainingAmount}'     =>  "{remainingAmount}",
            '{dueDate}'             =>  "{dueDate}",
            '{registrationDate}'    =>  "{registrationDate}",
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if( !function_exists('triggerEventTypes') ) {
    function triggerEventTypes($ret = NULL) {
        $data = [
            1 =>  "Email",
            2 =>  "SMS",
            3 =>  "Text Task",
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if( !function_exists('getDaysOfWeek') ) {
    function getDaysOfWeek($ret = NULL) {
        $data = [
            1 =>  "Monday",
            2 =>  "Tuesday",
            3 =>  "Wednesday",
            4 =>  "Thursday",
            5 =>  "Friday",
            6 =>  "Saturday",
            7 =>  "Sunday",
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if( !function_exists('triggerEventWhen') ) {
    function triggerEventWhen($ret = NULL) {
        $data = [
            1 =>  "Days Before Course",
            2 =>  "Time of Month",
            3 =>  "Day of Week",
            4 =>  "Days After Course",
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if( !function_exists('getModeOfPaymentStatus') ) {
    function getModeOfPaymentStatus($ret = NULL) {
        $data = [
            1 =>  "Paid",
            2 =>  "Pending"
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if( !function_exists('getAssessmentName') ) {
    function getAssessmentName($ret = NULL) {
        $data = [
            'c' =>  "Competent",
            'nyc' =>  "Not Competent"
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if( !function_exists('syncAssessmentWithTrainer') ) {
    function syncAssessmentWithTrainer($ret = NULL) {
        $data = [
            'c' =>  "Competent",
            'nyc' =>  "Not Competent",
            'reschedule' =>  "Assessment Returned",
            'incomplete' =>  "Incomplete Submission"
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "--";
    }
}

if( !function_exists('getCourseVacancy') ) {
    function getCourseVacancy($ret = NULL) {
        $data = [
            "A" =>  "Available",
            "F" =>  "Full",
            "L" =>  "Limited Vacancy"
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if( !function_exists('getMealRestrictionsType') ) {
    function getMealRestrictionsType($ret = NULL) {
        $data = [
            "Halal" =>  "Halal",
            "Vegetarian" =>  "Vegetarian",
            "Other" =>  "Other"
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if( !function_exists('getPaymentStatus') ) {
    function getPaymentStatus($ret = NULL) {
        $data = [
            1 =>  "Pending",
            2 =>  "Partial",
            3 =>  "Full",
            4 =>  "Refunded",
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if( !function_exists('calculatePaymentStatus') ) {
    function calculatePaymentStatus($paidAmt, $amt) {
        if( $paidAmt == 0 ) {
            return 1;
        } else if( $paidAmt < $amt ) {
            return 2;
        } else if( $paidAmt >= $amt ) {
            return 3;
        }
        return 1;
        // 4 =>  "Refunded",
    }
}

if( !function_exists('getPaymentStatusForTPG') ) {
    function getPaymentStatusForTPG($ret = NULL) {
        $data = [
            1 =>  "Pending Payment",
            2 =>  "Partial Payment",
            3 =>  "Full Payment",
            4 =>  "Cancelled"
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if(!function_exists('getPaymentStatusFormTPG')){
    function getPaymentStatusFormTPG($ret = NULL) {
        $data = [
            "Pending Payment" =>  1,
            "Partial Payment" =>  2,
            "Full Payment" =>  3,
            "Cancelled" =>  4
        ];
        if( is_null($ret) ) {
            return $data['Pending Payment'];
        }
        return array_key_exists($ret, $data) ? $data[$ret] : 1;
    }
}

if( !function_exists('getPaymentStatusFromTPG') ) {
    function getPaymentStatusFromTPG($ret = NULL) {
        $data = [
            "Pending Payment"   =>  1,
            "Partial Payment"   =>  2,
            "Full Payment"      =>  3,
            "Cancelled"         =>  4
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if( !function_exists('getCourseType') ) {
    function getCourseType($ret = NULL) {
        $data = [
            1 =>  "WSQ",
            2 =>  "non-WSQ",
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if( !function_exists('getCourseSoftBookingStatus') ) {
    function getCourseSoftBookingStatus($ret = NULL) {
        $data = [
            0 =>  "Pending",
            1 =>  "Booked",
            2 =>  "Cancelled",
            3 =>  "Expired",
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if( !function_exists('getCourseWaitingListStatus') ) {
    function getCourseWaitingListStatus($ret = NULL) {
        $data = [
            0 =>  "Pending",
            1 =>  "Accepted",
            2 =>  "Cancelled",
        ];
        if( is_null($ret) ) {
            return $data;
        }
        return array_key_exists($ret, $data) ? $data[$ret] : "-";
    }
}

if( !function_exists('convertNricToView') ) {
    function convertNricToView($nric) {
        return substr_replace($nric, 'XXXX', 1, 4);
    }
}

if( !function_exists('convertToTPDate') ) {
    function convertToTPDate($ret) {
        return date('Ymd', strtotime($ret));
    }
}

if( !function_exists('convertFromTPDate') ) {
    function convertFromTPDate($ret) {
        return substr($ret, 0, 4)."-".substr($ret, 4, 2)."-".substr($ret, 6, 2);
    }
}

if( !function_exists('getDateDifference') ) {
    function getDateDifference($start,$end) {
        $start_date = strtotime($start);
        $end_date = strtotime($end);
        $getDateDiff = ($end_date - $start_date)/60/60/24;
        return $getDateDiff;
    }
}

if( !function_exists('convertToSessionSchedule') ) {
    function convertToSessionSchedule($session) {

        $ret = "";
        $ret .= date('Y/m/d', strtotime(convertFromTPDate($session->startDate)));
        $ret .= date(' h:i A', strtotime(convertFromTPDate($session->startDate)." ".$session->startTime));
        $ret .= " - ";
        $ret .= date('Y/m/d', strtotime(convertFromTPDate($session->endDate)));
        $ret .= date(' h:i A', strtotime(convertFromTPDate($session->startDate)." ".$session->endTime));
        return $ret;
    }
}

if( !function_exists('convertToSessionDates') ) {
    function convertToSessionDates($val, $ret = NULL) {

        $breakString = explode("-",$val);

        $data = [];

        $startBreakDate = array_filter(explode(" ",$breakString[0]));
        $endBreakDate = array_filter(explode(" ",$breakString[1]));

        $data['start_date'] = date('Y-m-d',strtotime($startBreakDate[0]));
        $data['end_date'] = date('Y-m-d',strtotime($endBreakDate[1]));

        $petchStartTime = $startBreakDate[1].$startBreakDate[2];
        $petchEndTime = $endBreakDate[1].$endBreakDate[2].$endBreakDate[3];

        $data['start_time'] = date('H:i',strtotime($petchStartTime));
        $data['end_time'] = date('H:i',strtotime($petchEndTime));

        if( is_null($ret) ) {
            return $data;
        } else {
            return array_key_exists($ret, $data) ? $data[$ret] : "";
        }
    }
}

if( !function_exists('convertToSessionDatesOnly') ) {
    function convertToSessionDatesOnly($val, $ret = NULL) {

        $breakString = explode("-",$val);

        $data = [];

        $startBreakDate = array_filter(explode(" ",$breakString[0]));
        $endBreakDate = array_filter(explode(" ",$breakString[1]));

        $data['start_date'] = date('Y-m-d',strtotime($startBreakDate[0]));
        $data['end_date'] = date('Y-m-d',strtotime($endBreakDate[1]));

        $petchStartTime = $startBreakDate[1].$startBreakDate[2];
        $petchEndTime = $endBreakDate[1].$endBreakDate[2].$endBreakDate[3];

        $data['start_time'] = date('h:i A',strtotime($petchStartTime));
        $data['end_time'] = date('h:i A',strtotime($petchEndTime));

        if( is_null($ret) ) {
            return $data;
        } else {
            return array_key_exists($ret, $data) ? $data[$ret] : "";
        }
    }
}

if( !function_exists('convertToTPTime') ) {
    function convertToTPTime($ret) {
        return date('H:i', strtotime($ret));
    }
}

if( !function_exists('skipVenue') ) {
    function skipVenue() {
        return [2,4];
    }
}

if( !function_exists('getNationalityList') ) {
    function getNationalityList() {
        return ["Singapore Citizen", "Singapore Permanent Resident", "Non-Singapore Citizen/PR"];
    }
}

if( !function_exists('getEducationalQualificationsList') ) {
    function getEducationalQualificationsList() {
        return [
            "Primary Qualification (e.g PSLE) or Below",
            "Lower Secondary (Secondary Education without O level/N level pass)",
            "Secondary Qualification Equivalent (At least 1 pass in O level/N level or ITE Certificate)",
            "Post Secondary Qualification or Equivalent (A Level/IB Diploma/Higher NITEC)",
            "Polytechnic Diploma or Other Diploma Qualifications",
            "Bachelor's Degree and Above"
        ];
    }
}

if( !function_exists('getDesignationList') ) {
    function getDesignationList() {
        return [
            "Legislators, Senior Officials and Managers",
            "Professionals",
            "Clerical Support Workers",
            "Service and Sales Workers",
            "Workers Not Elsewhere Classified"
        ];
    }
}

if( !function_exists('getSalaryRangeList') ) {
    function getSalaryRangeList() {
        return [
            "Unemployed",
            "Below $1,000",
            "$1,000 - $1,499",
            "$1,500 - $1,999",
            "$2,000 - $2,499",
            "$2,500 - $2,999",
            "$3,000 - $3,499",
            "$3,500 and Above",
        ];
    }
}

if( !function_exists('getCountryList') ) {
    function getCountryList($ret = NULL) {

        $data = [ "Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra",
            "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina",
            "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas",
            "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize",
            "Benin", "Bermuda", "Bhutan", "Bolivia", "Bonaire, Sint Eustatius and Saba",
            "Bosnia and Herzegovina", "Botswana", "Bouvet Island", "Brazil",
            "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria",
            "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde",
            "Cayman Islands", "Central African Republic", "Chad", "Chile", "China",
            "Christmas Island", "Cocos Islands", "Colombia", "Comoros",
            "Congo, Democratic Republic of the", "Congo, Republic of the", "Cook Islands",
            "Costa Rica", "Croatia", "Cuba", "Curaçao", "Cyprus", "Czech Republic",
            "Côte d'Ivoire", "Denmark", "Djibouti", "Dominica", "Dominican Republic",
            "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea",
            "Estonia", "Eswatini (Swaziland)", "Ethiopia", "Falkland Islands",
            "Faroe Islands", "Fiji", "Finland", "France", "French Guiana", "French Polynesia",
            "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana",
            "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala",
            "Guernsey", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and McDonald Islands",
            "Holy See", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia",
            "Iran", "Iraq", "Ireland", "Isle of Man", "Israel", "Italy", "Jamaica", "Japan", "Jersey",
            "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kuwait", "Kyrgyzstan",
            "Lao People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya",
            "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia", "Madagascar", "Malawi",
            "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania",
            "Mauritius", "Mayotte", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro",
            "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands",
            "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island",
            "North Korea", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau",
            "Palestine, State of", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines",
            "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Romania", "Russia", "Rwanda",
            "Réunion", "Saint Barthélemy", "Saint Helena", "Saint Kitts and Nevis", "Saint Lucia",
            "Saint Martin", "Saint Pierre and Miquelon", "Saint Vincent and the Grenadines", "Samoa",
            "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles",
            "Sierra Leone", "Singapore", "Sint Maarten", "Slovakia", "Slovenia", "Solomon Islands",
            "Somalia", "South Africa", "South Georgia", "South Korea", "South Sudan", "Spain", "Sri Lanka",
            "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Sweden", "Switzerland", "Syria",
            "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tokelau", "Tonga",
            "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands",
            "Tuvalu", "US Minor Outlying Islands", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom",
            "United States", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam",
            "Virgin Islands, British", "Virgin Islands, U.S.", "Wallis and Futuna", "Western Sahara",
            "Yemen", "Zambia", "Zimbabwe", "Åland Islands", ];
        if( is_null($ret) ) {
            return $data;
        }

        return in_array($ret, $data) ? $data[$ret] : "-";
    }

    if( !function_exists('getModuleNameByType') ) {
        function getModuleNameByType($type) {
            $moduleTypes = array(
                'App\Models\StudentEnrolment' => 'Student Enrolmet',
                'App\Models\Student' => 'Student',
                'App\Models\Course' => 'Course Run',
                'App\Models\CourseMain' => 'Course',
                'App\Models\CourseDocuments' => 'Course Documents',
                'App\Models\CourseSoftBooking' => 'Course Soft Booking',
                'App\Models\Payment' => 'Payment',
                'App\Models\Venue' => 'Venue',
                'App\Models\WaitingList' => 'WaitingList',
                'App\Models\Grant' => 'Grant',
                'App\Models\Grant-Logs' => 'Grant-Logs',
            );
            return array_key_exists($type, $moduleTypes) ? $moduleTypes[$type] : "-";
        }
    }

    if( !function_exists('getAdminNameById') ) {
        function getAdminNameById($id) {
            $user = User::where('id',$id)->first();
            return ($user) ? $user->name : "System";
        }
    }

    // if( !function_exists('getCurrentActivity') ) {
    //     function getCurrentActivity($field, $revision)
    //     {
    //         $output = array();
    //         $output['title'] = '';
    //         $output['description'] = '';
    //         if($revision->revisionable_type)
    //         {
    //             $type = getModuleNameByType($revision->revisionable_type);
    //             $model = $revision->revisionable_type;
    //             $modelData = $model::where('id',$revision->revisionable_id);
    //         }
            
    //         if(!empty($type))
    //         {
    //             switch($type)
    //             {
    //                 case "Student Enrolmet":
    //                     $studentData  = $modelData->with('student')->first();
    //                     //$studentNRIC = ($studentData->student->nric) ? $studentData->student->nric : '' ;
    //                     $studentName = $studentData->student->name ?? '';
    //                     $courseRunData = $modelData->with('courseRun.courseMain')->first();
    //                     $courseName = $courseRunData->courseRun->courseMain->name ?? '';                        
    //                     $courseStartDate = $courseRunData->courseRun->course_start_date ?? '' ;
                        
    //                     if($revision->key == 'attendance')
    //                     {
    //                         $output['title'] = 'Attendance';
    //                         $output['description'] = 'Attendance for '.$courseStartDate.' - '.$courseName.' was submitted by '.getAdminNameById($revision->user_id).' for '.$studentName;
    //                     }
    //                     else if($revision->key == 'assessment')
    //                     {
    //                         $output['title'] = 'Assessment';
    //                         $output['description'] = 'Assessment for '.$courseStartDate.' - '.$courseName.' was submitted by '.getAdminNameById($revision->user_id).' for '.$studentName;
    //                     }
    //                     else if($revision->key == 'tgp_payment_response')
    //                     {
    //                         $output['title'] = 'Payments';
    //                         $output['description'] = 'Payment for '.$studentName.'('.$revision->revisionable_id.') was added by '.getAdminNameById($revision->user_id);
    //                     }
    //                     else if( $revision->key =='created_at' && !$revision->old_value ) 
    //                     { 
    //                         $output['title'] = 'Student Enrolmet';
    //                         $output['description'] = $studentName .' was enrolled in '.$courseStartDate.'-'.$courseName. ' by '.getAdminNameById($revision->user_id);
    //                     }
    //                     else
    //                     {
    //                         $output['title'] = 'Student Enrolmet';
    //                         $output['description'] = str_replace("_"," ",ucfirst($revision->key)).' of '.$studentName.' was changed by '.getAdminNameById($revision->user_id);
    //                     }
    //                 break;
    //                 case "Student":
    //                     $studentData  = $modelData->first();
    //                     $studentName = $studentData->name ?? '';

    //                     if( $revision->key =='created_at' && !$revision->old_value ) 
    //                     { 
    //                         $output['title'] = 'Student Details';
    //                         $output['description'] = str_replace("_"," ",ucfirst($revision->key)).' of '.$studentName .'('.$revision->new_value.') was added by '.getAdminNameById($revision->user_id);
                            
    //                     }
    //                     else
    //                     {
    //                         $output['title'] = 'Student Details';
    //                         if(!empty($revision->old_value))
    //                         {
    //                             $output['description'] = str_replace("_"," ",ucfirst($revision->key)).' of '.$studentName .' was changed by '.getAdminNameById($revision->user_id).' from '.$revision->old_value. ' to ' .$revision->new_value;
    //                         }
    //                         else
    //                         {
    //                             $output['description'] = str_replace("_"," ",ucfirst($revision->key)).' of '.$studentName .' set to '.$revision->new_value .' by '.getAdminNameById($revision->user_id);
    //                         }
    //                     }
    //                 break;
    //                 case "Course Run":
    //                     $courseRunData  = $modelData->with('courseMain')->first();
    //                     $courseName = $courseRunData->courseMain->name ?? '';
    //                     $courseId = $courseRunData->id ?? '';
    //                     $courseRunId = $courseRunData->tpgateway_id ?? '';
    //                     $courseStartDate = $courseRunData->course_start_date ?? '';
    //                     if( $revision->key =='created_at' && !$revision->old_value ) 
    //                     { 
    //                         $output['title'] = 'Courses and runs';
    //                         $output['description'] = $courseStartDate .' - '.$courseName. ' was added by '.getAdminNameById($revision->user_id);
    //                     }
    //                     else
    //                     {
    //                         $output['title'] = 'Courses and runs';
    //                         if(!empty($revision->old_value))
    //                         {
    //                             $output['description'] = 'Changed '.$courseName.'\'s  '.str_replace("_"," ",ucfirst($revision->key)).' from '.$revision->old_value. ' to ' .$revision->new_value;
    //                         }
    //                         else
    //                         {
    //                             $output['description'] = $courseName.'\'s  '.str_replace("_"," ",ucfirst($revision->key)).' was set to '.$revision->new_value. ' by ' .getAdminNameById($revision->user_id);
    //                         }
    //                     }
    //                 break;
    //                 case "Course":
    //                     $courseMainData  = $modelData->first();
    //                     $courseName = $courseMainData->name ?? '';
    //                     $courseId = $courseMainData->id ?? '';
    //                     if( $revision->key =='created_at' && !$revision->old_value ) 
    //                     { 
                            
    //                         $output['title'] = 'Course';
    //                         $output['description'] = str_replace("_"," ",ucfirst($revision->key)).' of '.$courseName.' ('.$revision->new_value.') was added by '.getAdminNameById($revision->user_id);
    //                     }
    //                     else
    //                     {
    //                         $output['title'] = 'Course';
    //                         $output['description'] = str_replace("_"," ",ucfirst($revision->key)).' of '.$courseName .' was changed by '.getAdminNameById($revision->user_id).' from '.$revision->old_value. ' to ' .$revision->new_value;
                            
    //                     }
    //                 break;
    //                 case "Course Documents":
    //                     if( $revision->key =='created_at' && !$revision->old_value ) 
    //                     { 
    //                         $output['title'] = 'Course Documents';
    //                         $output['description'] = 'Added Course Name Run Id';
                            
    //                     }
    //                     else
    //                     {
    //                         $output['title'] = 'Course Documents';
    //                         $output['description'] = 'Changed Course Run ID from to new value';
    //                     }
    //                 break;
    //                 case "Course Soft Booking":
    //                     $studentData  = $modelData->with('course.CourseMain')->first();
    //                     $studentName = $studentData->name ?? '';
    //                     $courseName = $studentData->course->CourseMain->name ?? '';
    //                     $courseStartDate = $studentData->course->course_start_date ?? '' ;
    //                     if( $revision->key =='created_at' && !$revision->old_value ) 
    //                     { 
    //                         $output['title'] = 'Student Enrolments (Soft Booking)';
    //                         $output['description'] = $studentName. ' was softbooked for ' .$courseStartDate.' - '.$courseName .' by '.getAdminNameById($revision->user_id);
    //                     }
    //                     else
    //                     {
    //                         $output['title'] = 'Student Enrolments (Soft Booking)';
    //                         $output['description'] = $studentName. ' was softbooked for ' .$courseStartDate.' - '.$courseName .' by '.getAdminNameById($revision->user_id);
    //                     }
    //                 break;
    //                 case "Venue":
    //                     if( $revision->key =='created_at' && !$revision->old_value ) 
    //                     { 
    //                         $output['title'] = 'Venue';
    //                         $output['description'] = 'Added Venue';
    //                     }
    //                     else
    //                     {
    //                         $output['title'] = 'Venue';
    //                         $output['description'] = 'Changed Venue'.'\'s '.$revision->key.' from '.$revision->old_value. ' to ' .$revision->new_value;
    //                     }
    //                 break;
    //                 case "Payment":
    //                     if( $revision->key =='created_at' && !$revision->old_value ) 
    //                     { 
    //                         $output['title'] = 'Payments';
    //                         $output['description'] = 'Added Payment';
    //                     }
    //                     else
    //                     {
    //                         $output['title'] = 'Payments';
    //                         $output['description'] = 'Changed Venue'.'\'s '.$revision->key.' from '.$revision->old_value. ' to ' .$revision->new_value;
    //                     }
    //                 break;
    //                 case "WaitingList":
    //                     $studentData  = $modelData->with('course.CourseMain')->first();
    //                     $studentName = $studentData->name ?? '';
    //                     $courseName = $studentData->course->CourseMain->name ?? '';
    //                     $courseStartDate = $studentData->course->course_start_date ?? '' ;
    //                     if( $revision->key =='created_at' && !$revision->old_value ) 
    //                     { 
    //                         $output['title'] = 'Student Enrolments (WaitingList)';
    //                         $output['description'] = $studentName. ' was added to waitlist for ' .$courseStartDate.' - '.$courseName .' by '.getAdminNameById($revision->user_id);
                            
    //                     }
    //                     else
    //                     {
    //                         $output['title'] = 'Student Enrolments (WaitingList)';
    //                         $output['description'] = $studentName. ' was added to waitlist for ' .$courseStartDate.' - '.$courseName .' by '.getAdminNameById($revision->user_id);
    //                     }
    //                 break;
    //                 default:
    //                 break;
    //             }
    //             if($field == 'category')
    //             {
    //                 return $output['title'];    
    //             }
    //             else
    //             {
    //                 return $output['description'];
    //             }
                
    //         }        
    //     }
    // }

    if( !function_exists('getTrainerIdType') ) {
        function getTrainerIdType($code = NULL)
        {
            $types = [
                "SP" => "Singapore Pink Identification Card",
                "SB" => "Singapore Blue Identification Card",
                "SO" => "FIN/Work Permit",
                "FP" => "Foreign Passport",
                "OT" => "Others"
            ];

            if( is_null($code) ) {
                return $types;
            }
            return array_key_exists($code, $types) ? $types[$code] : "-";
        }
    }


    if( !function_exists('getTrainerRoles') ) {
        function getTrainerRoles($id = NULL)
        {
            $roles = [
                1 => "Trainer",
                2 => "Assessor",
            ];

            if( is_null($id) ) {
                return $roles;
            }
            return array_key_exists($id, $roles) ? $roles[$id] : "-";
        }
    }

    if( !function_exists('emailTemplateTriggerTypes') ) {
        function emailTemplateTriggerTypes($ret = NULL) {
            $data = [
                1 =>  "Admin",
                2 =>  "Course",
                3 =>  "Invoice",
            ];
            if( is_null($ret) ) {
                return $data;
            }
            return array_key_exists($ret, $data) ? $data[$ret] : "-";
        }
    }

    /**
     * Replaces the content text with the values
     *
     * @author The Chief
     * @param string $content Content text
     * @param array $valueArr Replacement values array
     * @return string
     */
    if( !function_exists('replaceEmailContent') ) {
        function replaceEmailContent($content = "", $valueArr = [])
        {
            if ($content != "" && count($valueArr) > 0) {
                foreach ($valueArr as $key => $value) {
                    $content = str_replace('{' . $key . '}', $value, $content);
                }

                return $content;
            } else {
                return $content;
            }
        }
    }

    if(!function_exists('paymentStatusBadge')){
        function paymentStatusBadge($paymetStatus){
            if( $paymetStatus == StudentEnrolment::PAYMENT_STATUS_FULL ) {
                return '<span class="badge badge-soft-success">Full</span>';
            } else if( $paymetStatus == StudentEnrolment::PAYMENT_STATUS_PARTIAL ) {
                return '<span class="badge badge-soft-info">Partial</span>';
            } else if( $paymetStatus == StudentEnrolment::PAYMENT_STATUS_PENDING ) {
                return '<span class="badge badge-soft-primary">Pending</span>';
            } else {
                return '<span class="badge badge-soft-danger">Refunded</span>';
            }
        }
    }

    if(!function_exists('tpgPaymentStatusBadge')){
        function tpgPaymentStatusBadge($paymetStatus){
            if( $paymetStatus == StudentEnrolment::TPG_STATUS_PENDING ) {
                return '<span class="badge badge-soft-primary">Pending</span>';
            } else if( $paymetStatus == StudentEnrolment::TPG_STATUS_PARTIAL ) {
                return '<span class="badge badge-soft-info">Partial</span>';
            } else if( $paymetStatus == StudentEnrolment::TPG_STATUS_FULL ) {
                return '<span class="badge badge-soft-success">Full</span>';
            } else if( $paymetStatus == StudentEnrolment::TPG_STATUS_CANCELLED ) {
                return '<span class="badge badge-soft-danger">Refunded</span>';
            }
        }
    }

    if(!function_exists('enrollStatusBadge')){
        function enrollStatusBadge($status){
            if( $status == StudentEnrolment::STATUS_ENROLLED ) {
                return '<span class="badge badge-soft-success">Enrolled</span>';
            } else if( $status == StudentEnrolment::STATUS_CANCELLED ) {
                return '<span class="badge badge-soft-danger">Enrolment Cancelled</span>';
            } else if($status == StudentEnrolment::STATUS_HOLD) {
                return '<span class="badge badge-soft-warning warning">Holding List</span>';
            } else if($status == StudentEnrolment::STATUS_NOT_ENROLLED) {
                return '<span class="badge badge-soft-danger">Not Enrolled</span>';
            }
        }
    }
       
    if(!function_exists('isPublishedStatusBadge')){
        function isPublishedStatusBadge($isPublished){
            if( $isPublished == Course::STATUS_PUBLISHED ) {
                return '<span class="badge badge-soft-success">Published</span>';
            } else if( $isPublished == Course::STATUS_UNPUBLISHED ) {
                return '<span class="badge badge-soft-warning warning">Un Published</span>';
            }
            else {
                return '<span class="badge badge-soft-danger">Cancelled</span>';
            }
        }
    }

    if(!function_exists('enrolledStatus')){
        function enrolledStatus($ret = NULL){
            $data = [
                StudentEnrolment::STATUS_ENROLLED     => 'Enrolled',
                StudentEnrolment::STATUS_CANCELLED    => 'Enrolment Cancelled',
                StudentEnrolment::STATUS_HOLD         => 'Holding List',
                StudentEnrolment::STATUS_NOT_ENROLLED => 'Not Enrolled',
            ];
            if( is_null($ret) ) {
                return $data;
            }
            return array_key_exists($ret, $data) ? $data[$ret] : "-";
        }
    }

    if(!function_exists('enrolledStatusWithRefreshers')){
        function enrolledStatusWithRefreshers($ret = NULL){
            $data = [
                StudentEnrolment::STATUS_ENROLLED     => 'Enrolled',
                StudentEnrolment::STATUS_CANCELLED    => 'Enrolment Cancelled',
                StudentEnrolment::STATUS_HOLD         => 'Holding List',
                StudentEnrolment::STATUS_NOT_ENROLLED => 'Not Enrolled',
                // StudentEnrolment::REFRESHER_STATUS => 'Refresher',
            ];
            if( is_null($ret) ) {
                return $data;
            }
            return array_key_exists($ret, $data) ? $data[$ret] : "-";
        }
    }

    if(!function_exists('StatusWithRefreshers')){
        function StatusWithRefreshers($ret = NULL){
            $data = [
                Refreshers::STATUS_PENDING     => 'Pending',
                Refreshers::STATUS_ACCEPTED    => 'Accepted',
                Refreshers::STATUS_CANCELLED   => 'Cancelled',
            ];
            if( is_null($ret) ) {
                return $data;
            }
            return array_key_exists($ret, $data) ? $data[$ret] : "-";
        }
    }

    if(!function_exists('paymentStatus')){
        function paymentStatus($ret = NULL){
            $data = [
                StudentEnrolment::PAYMENT_STATUS_PENDING => "PENDING",
                StudentEnrolment::PAYMENT_STATUS_PARTIAL => "PARTIAL",
                StudentEnrolment::PAYMENT_STATUS_FULL => "FULL",
                StudentEnrolment::PAYMENT_STATUS_REFUND => "REFUND",
            ];
            if( is_null($ret) ) {
                return $data;
            }
            return array_key_exists($ret, $data) ? $data[$ret] : "-";
        }
    }

    if(!function_exists('courseTriggerStatus')){
        function courseTriggerStatus($triggerStatus){
            if( $triggerStatus ) { 
                return '<span class="badge badge-soft-success">Active</span>'; 
            }else { 
                return '<span class="badge badge-soft-danger">Inactive</span>'; 
            }
        }
    }

    if(!function_exists('userName')){
        function userName($id){
            $userName = User::find($id);
            return $userName->name;
        }
    }

    if(!function_exists('userNameFromEnrolment')){
        function userNameFromEnrolment($id){
            \Log::info("testttt 1 => ". $id);
            $usrName = StudentEnrolment::find($id);
            \Log::info("testttt 2 => ". $usrName->student_id);
            $userName = Student::find($usrName->student_id);
            return $userName->name;
        }
    }

    if(!function_exists('enrolmentId')){
        function enrolmentId($id){
            $grant = Grant::find($id);
            return $grant->student_enrolment_id;
        }
    }

    if( !function_exists('getStudnetStatus') ) {
        function getStudnetStatus($ret = NULL) {
            $data = [
                0 =>  "Paid",
                1 =>  "Cancelled",
            ];
            if( is_null($ret) ) {
                return $data;
            }
            return array_key_exists($ret, $data) ? $data[$ret] : "-";
        }
    }
    
    if(!function_exists('auditDescription')){
        function auditDescription($event, $oldValue, $newValue, $userId, $auditableType, $auditableId){
            
            $type = getModuleNameByType($auditableType);
            if(!empty($type))
            {
                switch($type)
                {
                    case "Course":
                        if($event == "updated"){
                            $description = "Edited the " . array_keys($oldValue)[0] . " field from <b><a href='". route('admin.course.list', $auditableId) . "'>" . $oldValue[array_keys($oldValue)[0]] . "</a></b> to <b>" . $newValue[array_keys($oldValue)[0]] . "</b> (Course id " . $auditableId . ")";
                        }elseif($event == "created") {
                            $description = "Created <b><a href='" . route('admin.course.list', $auditableId) . "'>" . $newValue[array_keys($newValue)[1]] . "</a></b> (Course id " . $auditableId . ")";
                        }
                        return $description;
                    break;
                    
                    case "Student Enrolmet":
                        if($event == "updated"){
                            $alldetails = $auditableType::where('id', $auditableId);
                            $studentData  = $alldetails->with('student')->first();
                            $studentName = $studentData->student->name ?? '';
                            $courseRunData = $alldetails->with('courseRun.courseMain')->first();
                            $courseName = $courseRunData->courseRun->courseMain->name ?? '';
                            $courseStartDate = $courseRunData->courseRun->course_start_date ?? '' ;

                            $descriptionOne = "Edited <b>" . $studentName . "</b>'s enrolment (<a href='" . route('admin.studentenrolment.view', $auditableId) . "'>enrolment ID <b>" . $auditableId . "</b></a>) for <b><a href='" . route('admin.course.courserunview', $courseRunData->courseRun->id) . "'>" . $courseName . "</b>(Start date <b>". $courseStartDate ."</b></a>).";
                            $descriptionTwo = "";
                            if(array_key_exists("education_qualification", $newValue) && count($newValue) > 2){
                                
                                $descriptionTwo = "<b>Education qualification</b> from <b>" . ($oldValue['education_qualification'] ?? "Empty") . "</b> to <b>" . $newValue['education_qualification'] ."</b>";

                            } else if (array_key_exists("company_sme", $newValue) && count($newValue) > 2) {

                                $descriptionTwo = "<b>Company SME</b> from <b>" . ($oldValue['company_sme'] ?? "Empty") . "</b> to <b>" . $newValue['company_sme'] . "</b>";

                            } else if (array_key_exists("payment_remark", $newValue)) {

                                $descriptionTwo = "<b>Payment Remark</b> from <b>" . ($oldValue['payment_remark'] ?? "Empty") . "</b> to <b>" . $newValue['payment_remark'] . "</b>";

                            } else if (!array_key_exists("education_qualification", $newValue)) {
                                
                                if(array_keys($newValue)[0] == "status") {
                                    if($newValue[array_keys($newValue)[0]] == StudentEnrolment::STATUS_CANCELLED){
                                        $descriptionOne = "Cancelled <b>" . $studentName . "</b>’s  enrolment (<a href='" . route('admin.studentenrolment.view', $auditableId) . "'>enrolment ID <b>" . $auditableId . "</b></a>) for <b><a href = '" . route('admin.course.courserunview', $courseRunData->courseRun->id) . "'>" . $courseName . "</b> (start date <b>" . $courseStartDate . "</b>)).</a>";
                                    } else if($newValue[array_keys($newValue)[0]] == StudentEnrolment::STATUS_ENROLLED) {
                                        $descriptionTwo = "Student enrolment status is <b>" . enrolledStatus($newValue[array_keys($newValue)[0]]) ."</b>";
                                    } else if($newValue[array_keys($newValue)[0]] == StudentEnrolment::STATUS_HOLD) {
                                        $descriptionTwo = "Edited Student enrolment from <b>" . enrolledStatus($oldValue[array_keys($oldValue)[0]]) . "</b> to <b>" . enrolledStatus($newValue[array_keys($newValue)[0]]) . "</b>";
                                    } else if($newValue[array_keys($newValue)[0]] == StudentEnrolment::STATUS_NOT_ENROLLED) {
                                        $descriptionTwo = "Edited Student enrolment from <b>" . enrolledStatus($oldValue[array_keys($oldValue)[0]]) . "</b> to <b>" .enrolledStatus($newValue[array_keys($newValue)[0]]) ."</b>";
                                    }
                                } elseif(array_keys($newValue)[0] == "payment_status") {
                                    $count = 0;
                                    foreach($newValue as $key => $value){
                                        if($oldValue[array_keys($newValue)[$count]] != $value){
                                            if(array_keys($newValue)[$count] == "payment_status"){
                                                $descriptionTwo .= "<b>" . array_keys($newValue)[$count] . "</b> from <b>" . paymentStatus($oldValue[array_keys($newValue)[$count]]) . "</b> to <b>" . paymentStatus($newValue[array_keys($newValue)[$count]]) . "</b>" . " , ";
                                            }else{
                                                $descriptionTwo .= "<b>" . array_keys($newValue)[$count] . "</b> from <b>" . ($oldValue[array_keys($newValue)[$count]] ?? "Empty") . "</b> to <b>" . $newValue[array_keys($newValue)[$count]] . "</b> , ";
                                            }     
                                        }
                                        $count++;
                                    }
                                    $descriptionTwo = rtrim($descriptionTwo,' ,');
                                } 
                                elseif(array_keys($newValue)[0] == "attendance") {
                                    if(empty($oldValue['attendance'])){
                                        $descriptionTwo = "<b>" . array_keys($newValue)[0] . "</b> is Submited.";
                                    }else{
                                        $descriptionTwo = "<b>" . array_keys($newValue)[0] . "</b> is either updated or synced from TPG.";
                                    }
                                    
                                }

                                elseif(array_keys($newValue)[0] == "assessment") {
                                    if(empty($oldValue['assessment'])){
                                        $descriptionTwo = "<b>" . array_keys($newValue)[0] . "</b> is Submited.";
                                    }else{
                                        $descriptionTwo = "<b>" . array_keys($newValue)[0] . "</b> is either updated or synced from TPG.";
                                    }
                                    
                                }
                                
                                else {
                                    $descriptionTwo = "<b>" . array_keys($newValue)[0] . "</b> from <b>" . ($oldValue[array_keys($newValue)[0]]  ?? "Empty") . "</b> to <b>" . $newValue[array_keys($newValue)[0]] . "</b>";
                                }

                            }
                            return $descriptionOne."<br><br>".$descriptionTwo;
                        } 
                        else if ($event == "created") {
                            $alldetails = $auditableType::where('id', $auditableId);
                            $studentData  = $alldetails->with('student')->first();
                            $studentName = $studentData->student->name ?? '';
                            $courseRunData = $alldetails->with('courseRun.courseMain')->first();
                            $courseName = $courseRunData->courseRun->courseMain->name ?? '';
                            $courseStartDate = $courseRunData->courseRun->course_start_date ?? '' ;
                            
                            $description = "Enrolled <b>" . $studentName . "</b> into <b><a href='" . route('admin.course.courserunview', $courseRunData->courseRun->id) . "'>" . $courseName . "</a></b>(Start date " . $courseStartDate . ") with <b><a href ='" . route('admin.studentenrolment.view', $auditableId) . "'>Enrolment ID " . $auditableId ."</a></b>";
                            return $description;
                        }
                    break;
                    
                    case "Course Run":
                        $courseRunData  = $auditableType::where('id', $auditableId);
                        $courseRunData  = $courseRunData->with('courseMain')->first();
                        $courseName = $courseRunData->courseMain->name ?? '';
                        $courseRunId = $courseRunData->id ?? '';
                        $courseTpId = $courseRunData->tpgateway_id ?? '';
                        $courseStartDate = $courseRunData->course_start_date ?? '';

                        if($event == "created") {
                            $description = "Added <b>" . $courseName . "</b> course run with course start date " . $courseStartDate . "<b><a href='" . route('admin.course.courserunview', $courseRunId) . "'> (Run ID " . $courseRunId . ")</a></b>";
                            return $description;
                        } elseif($event == "updated") {
                            $description = "";
                            if(count($newValue) >= 2){
                                $count = 0;
                                foreach($newValue as $key => $value){
                                    if($oldValue[array_keys($newValue)[$count]] != $value){
                                        $description .= "Edited the <b>" . array_keys($newValue)[$count] . "</b> field from <b><a href='" . route('admin.course.courserunview', $courseRunId) . "'>" . ($oldValue[array_keys($newValue)[$count]] ?? "Empty") . "</a></b> to <b>" . $value . "</b> <a href='" . route('admin.course.courserunview', $courseRunId) . "'><b>(" . $courseName . " course run " . $courseRunId . ")</b></a> <br>";
                                    }
                                    $count++;
                                }
                                return $description;
                            } else {
                                $description = "Edited the <b>" . array_keys($newValue)[0] . "</b> field from <b><a href='" . route('admin.course.courserunview', $courseRunId) . "'>" . ($oldValue[array_keys($newValue)[0]] ?? "Empty") . "</a></b> to <b>" . $newValue[array_keys($newValue)[0]] . "</b> <a href='" . route('admin.course.courserunview', $courseRunId) . "'><b>(" . $courseName . " course run " . $courseRunId . ")</b></a>";
                                return $description;
                            }
                        }
                    break;
                    
                    case "Payment":
                        $paymentDetails = $auditableType::where('id', $auditableId)->first();
                        $alldetails = StudentEnrolment::where('id', $paymentDetails->student_enrolments_id);
                        $studentData  = $alldetails->with('student')->first();
                        $studentName = $studentData->student->name ?? '';
                        $courseRunData = $alldetails->with('courseRun.courseMain')->first();
                        $courseName = $courseRunData->courseRun->courseMain->name ?? '';
                        $courseStartDate = $courseRunData->courseRun->course_start_date ?? '';
                        
                        if($event == "created") {
                            $description = "Added payment of " . array_key_exists('fee_amount', $newValue) ? 0 : $newValue['fee_amount'] . " to <b>" . $studentName . "</b>’s enrolment in <a href='" . route('admin.course.courserunview', $courseRunData->courseRun->id) . "'><b>" . $courseName . "</b> (start date " . $courseStartDate . ")</a>. Enrolment <a href='" . route('admin.studentenrolment.view', $newValue['student_enrolments_id']) . "'><b>" . $newValue['student_enrolments_id'] . "</b></a>, payment ID <b><a href='". route('admin.payment.view', $auditableId) ."'>" . $auditableId . "</a></b>.";
                            return $description;
                        } elseif($event == "updated") {
                            $descriptionOne = "Edited <b>" . $studentName . "</b>’s payment ID <a href='". route('admin.payment.view', $auditableId) ."'><b>" . $auditableId . "</b></a> for <a href='" . route('admin.course.courserunview', $courseRunData->courseRun->id) . "'><b>" . $courseName . " (start date " . $courseStartDate . ")</b></a>. <br><br>";
                            $descriptionTwo = "";
                            $count = 0;
                            foreach($newValue as $key => $value){
                                if($oldValue[array_keys($newValue)[$count]] != $value){
                                    if(array_keys($newValue)[$count] == "payment_mode" || array_keys($newValue)[$count] == "payment_method") {
                                        $descriptionTwo .= "<b>". array_keys($newValue)[$count] . "</b>: <b>" .  getModeOfPayment($oldValue[array_keys($newValue)[$count]]) . "</b> → <b>" . getModeOfPayment($newValue[array_keys($newValue)[$count]]) . "</b> , ";
                                    } else {
                                        if(isset($newValue['status'])) {
                                            $descriptionTwo .= "<b>" . array_keys($newValue)[$count] . "</b>: <b>" . getStudnetStatus($oldValue[array_keys($newValue)[$count]]) . "</b> → <b>" . getStudnetStatus($newValue[array_keys($newValue)[$count]]) . "</b> , ";
                                        } else{
                                            $descriptionTwo .= "<b>" . array_keys($newValue)[$count] . "</b>: <b>" . $oldValue[array_keys($newValue)[$count]] . "</b> → <b>" . $newValue[array_keys($newValue)[$count]] . "</b> , ";
                                        }
                                    }
                                }
                                $count++;
                            }
                            return $descriptionOne . rtrim($descriptionTwo, ' ,');
                        }
                    break;

                    case "Student":
                        $studentDetails = $auditableType::where('id', $auditableId)->first();
                        $studentName = $studentDetails->name;
                        if($event == "updated") {
                            $description = "";
                            $count = 0;
                            foreach($newValue as $key => $value){
                                $description .= "Edited <b>" . $studentName . "</b>’s details. " . "<b>" . array_keys($newValue)[$count] . ": " . ($oldValue[array_keys($newValue)[$count]]  ?? "Empty") . " → " . $newValue[array_keys($newValue)[$count]] . "</b><br>";
                                $count++;
                            }
                            return $description;
                        }
                    break;

                    case "Grant":
                        if($event == "Created") {
                            return "<a href='" . route('admin.studentenrolment.view', $auditableId) . "' target='_blank'>Enrolment ID <b>" . $auditableId . "</b></a> has Grant Reference Number <b>".$userId. "</b> has been created with status <b>".$newValue."</b>";
                        } else{
                            return "<a href='" . route('admin.studentenrolment.view', $auditableId) . "' target='_blank'>Enrolment ID <b>" . $auditableId . "</b></a> has Grant Reference Number <b>".$userId. "</b> status has been changed from <b>".$oldValue."</b> to <b>".$newValue."</b>";
                        }
                        
                    break;

                    case "Grant-Logs":
                        if($event == "Created") {
                            return "Enrolment ID " . $auditableId . " has Grant Reference Number " . $userId . " has been created with status " . $newValue;
                        } else{
                            return "Enrolment ID " . $auditableId . " has Grant Reference Number " . $userId . " status has been changed from " . $oldValue . " to " . $newValue;
                        }
                    break;
                }
            }
        }
    }

    if( !function_exists('getCourseGSTRate') ) {
        function getCourseGSTRate($enrolment) {
            $enrolmentYear = Carbon::createFromFormat('Y-m-d H:i:s', $enrolment->created_at)->year;
            if($enrolmentYear == "2022"){
                $gst = 7;
            }
            else if($enrolmentYear == "2023"){
                $gst = 8;
            } 
            else if ($enrolmentYear == "2024"){
                $gst = 9;
            }
            else {
                $gst = 7;
            }
            return $gst;
        }
    }

    if( !function_exists('courseTypes') ){
        function courseModule($ret = NULL) {
            $data = [
                CourseMain::SINGLE_COURSE => 'Single Course',
                CourseMain::MODULAR_COURSE => 'Modular Course',
                CourseMain::BOOSTER_SESSIONS => 'Booster Sessions',
            ];
            if( is_null($ret) ) {
                return $data;
            }
            return array_key_exists($ret, $data) ? $data[$ret] : "-";
        }
    }

    if( !function_exists('courseMode') ){
        function courseType($ret = NULL) {
            $data = [
                CourseMain::COURSE_TYPE_WSQ => 'WSQ',
                CourseMain::COURSE_TYPE_NONWSQ => 'non-WSQ',
            ];
            if( is_null($ret) ) {
                return $data;
            }
            return array_key_exists($ret, $data) ? $data[$ret] : "-";
        }
    }

    if( !function_exists('courseMode') ){
        function courseMode($ret = NULL) {
            $data = [
                "online" => 'Online',
                "offline"=> 'Offline',
            ];
            if( is_null($ret) ) {
                return $data;
            }
            return array_key_exists($ret, $data) ? $data[$ret] : "-";
        }
    }

    if( !function_exists('courseTags') ){
        function courseTags($ret = NULL) {
            $data = [
                CourseTags::WSQ_F2F_COURSE => 'WSQ F2F Course',
                CourseTags::WSQ_ONLINE_COURSE => 'WSQ Online Course',
                CourseTags::NON_WSQ_F2F_COURSE => 'non-WSQ F2F course',
            ];
            if( is_null($ret) ) {
                return $data;
            }
            return array_key_exists($ret, $data) ? $data[$ret] : "-";
        }
    }

    if(!function_exists('paymentStatusTPG')){
        function paymentStatusTPG($ret = NULL){
            $data = [
                StudentEnrolment::STATUS_ENROLLED     => 'Enrolled',
                StudentEnrolment::STATUS_CANCELLED    => 'Enrolment Cancelled',
                StudentEnrolment::STATUS_HOLD         => 'Holding List',
                StudentEnrolment::STATUS_NOT_ENROLLED => 'Not Enrolled',
            ];
            if( is_null($ret) ) {
                return $data;
            }
            return array_key_exists($ret, $data) ? $data[$ret] : "-";
        }
    }

    if(!function_exists('getFileExtension')){
        function getFileExtension($fileName){
            $extension = explode(".", $fileName);
            if($extension[1] == "jpg" || $extension[1] == "png" || $extension[1] == "jpeg") {
                return '<i class="far fa-file-image text-info" style="font-size:40px;"></i>';
            } elseif ($extension[1] == "pdf") {
                return '<i class="far fa-file-pdf text-danger" style="font-size:40px;"></i>';
            } elseif ($extension[1] == "docx" || $extension[1] == "doc") {
                return '<i class="far fa-file-word text-info" style="font-size:40px;"></i>';
            } elseif ($extension[1] == "xlsx" || $extension[1] == "xls") {
                return '<i class="far fa-file-excel text-success" style="font-size:40px;"></i>';
            } elseif ($extension[1] == "zip") {
                return '<i class="far fa-file-archive text-primary" style="font-size:40px;"></i>';
            }
        }
    }

    if(!function_exists('resourceExpireAfterThreeYear')){
        function resourceExpireAfterThreeYear($date){

            $stratDate = Carbon::createFromFormat('Y-m-d', $date); 
            $convertedDate = $stratDate->addYear(3);
            return $convertedDate;
        }
    }

    if(!function_exists('getStudentNRIC')){
        function getStudentNRIC($id){
            $user = Student::where('user_id',$id)->first();
            return $user->name;
        }
    }

    if(!function_exists('resizeImage')){
        function resizeImage($filename, $max_width, $max_height)
        {   
            list($orig_width, $orig_height) = getimagesize($filename);

            $width = $orig_width;
            $height = $orig_height;

            # taller
            if ($height > $max_height) {
                $width = ($max_height / $height) * $width;
                $height = $max_height;
            }

            # wider
            if ($width > $max_width) {
                $height = ($max_width / $width) * $height;
                $width = $max_width;
            }

            $file_dimensions = getimagesize($filename);
            $file_type = strtolower($file_dimensions['mime']);

            if($file_type == "image/png"){
                $thumb = imagecreatetruecolor($width, $height);
                $source = imagecreatefrompng($filename);
                imagecopyresampled($thumb, $source, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height);
            } else if ($file_type=='image/jpeg'||$file_type=='image/pjpeg') {
                $thumb = imagecreatetruecolor($width, $height);
                $source = imagecreatefromjpeg($filename);
                imagecopyresampled($thumb, $source, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height);
            }

            return $thumb;
        }
    }
}

