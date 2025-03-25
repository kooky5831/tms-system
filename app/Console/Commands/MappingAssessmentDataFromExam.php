<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Assessments\Student\Models\ExamAssessment;
use Assessments\Student\Models\AssessmentMainExam;
use Assessments\Student\Models\AssessmentQuestions;
use Assessments\Student\Models\AssessmentMainCourse;

class MappingAssessmentDataFromExam extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:mapping-assessment-data-from-exam';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to Mapping Assessment From Exam';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $exams = DB::select('SELECT * FROM assessment_main_exams Group BY course_id');
        if(!empty($exams)){
            foreach($exams as $exam){

                $addexam = new AssessmentMainExam;
                $addexam->exam_duration = $exam->exam_duration;
                $addexam->exam_time = $exam->exam_time;
                $addexam->save();

                $addCourseMain = new AssessmentMainCourse;
                $addCourseMain->exam_id = $addexam->id;
                $addCourseMain->course_main_id = $exam->course_id;
                $addCourseMain->save();

                $AssessmentExams = DB::select('SELECT * FROM assessment_main_exams WHERE course_id = '. $exam->course_id);
                foreach($AssessmentExams as $assessmentExam) {
                  $addAsessment = new  ExamAssessment;
                  $addAsessment->exam_id =  $addexam->id;
                  $addAsessment->title =  $assessmentExam->assessment_name;
                  $addAsessment->type =  $assessmentExam->assessment_type;
                  $addAsessment->save();

                //   Add is_converted tempory column to assessment_question table to avoid getting same data.
                  $getQuestions = AssessmentQuestions::where(['assessment_id' => $assessmentExam->id, 'is_converted' => 0])->get();
                    foreach($getQuestions as $question){
                        $updateQuestions = AssessmentQuestions::find($question->id);
                        $updateQuestions->assessment_id = $addAsessment->id;
                        $updateQuestions->is_converted = 1;
                        \Log::info('Course ids ===> '. $updateQuestions);
                        $updateQuestions->save();
                    }
                }      
            }
        }
    }
}
