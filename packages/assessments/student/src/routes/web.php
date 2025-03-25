<?php

use Assessments\Student\Http\Controllers\AssessmentController;
use Assessments\Student\Http\Controllers\Auth\StudentLoginController;

use Illuminate\Support\Facades\Route;


Route::group(['namespace' => 'Student', 'as' => 'student.', 'prefix' => 'student', 'middleware' => ['web','prevent-back-history', 'userAuth']], function () {   
    Route::get('assessment/portal/', [AssessmentController::class, 'assessmentDashboard'])->name('assessment.dashboard');
    Route::post('assessment/rules', [AssessmentController::class, 'assessmentRulesModal'])->name('assessment.ajax.examrules.modal');
    Route::get('assessmet/exam-live/{id}/{studentid}/{exam_id}/{assessment_id}', [AssessmentController::class, 'examinationSystem'])->name('assessment.exam');

    Route::post('assessment/exam-form', [AssessmentController::class, 'examSubmission'])->name('assessmet.exam.post');
    Route::post('assessment/exam-finish-status', [AssessmentController::class, 'examUpdateFinish'])->name('assessmet.exam.finish.post');
    Route::post('assessment/exam-started-status', [AssessmentController::class, 'examStarted'])->name('assessmet.exam.started.post');

    Route::post('assessment/exam-last-time', [AssessmentController::class, 'examLastTime'])->name('assessmet.exam.lasttime.post');
    Route::post('assessment/auto-save-answer', [AssessmentController::class, 'autoSaveAnswer'])->name('assessmet.exam.auto.save');

    Route::post('assessment/exam-attachment', [AssessmentController::class, 'submitAttachment'])->name('assessmet.exam.attachment');
    Route::post('assessment/exam-attachment-delete', [AssessmentController::class, 'removeAttachment'])->name('assessmet.exam.attachment.delete');
    Route::get('assessment/get-exam-attachment/{studentenrol}', [AssessmentController::class, 'getAttachment'])->name('assessmet.exam.attachment.get');
    
    //ajax call
    Route::get('assessment/view/{assessmentId}/{studentId}', [AssessmentController::class, 'examPreview'])->name('assessment.exam.preview');
    Route::post('assessment/review-marks', [AssessmentController::class, 'storeAssessment'])->name('assessment.exam.reviews');
    Route::post('image-upload', [AssessmentController::class, 'storeCkImage'])->name('assessment.ck-image-upload');

    //comment code trainee submit feedback
    // Route::post('assessment/feedback', [AssessmentController::class, 'traineeFeedback'])->name('assessment.feedback');
    
    
    Route::post('assessment/feedback/screenshot', [AssessmentController::class, 'uploadScreenshotToDrive'])->name('assessment.feedback.screenshot');
    Route::get('assessment/feedback', [AssessmentController::class, 'traineeFeedback'])->name('assessment.feedback');
    
    Route::post('assessment/feedback/getdata', [AssessmentController::class, 'getFeedbackData'])->name('assessment.feedback.getdata');
    
    // Course Resoucres
    Route::group(['prefix' => 'assessment/course-resources', 'as' => 'course-resources.'], function () {
        Route::get('/', [AssessmentController::class, 'CourseResourcesList'])->name('assessment.courseresource');
        Route::get('resource/{id}', [AssessmentController::class, 'getCourseResourceById'])->name('assessment.get-resources');
    });
});

Route::group(['namespace' => 'Student', 'as' => 'student.', 'prefix' => 'student', 'middleware' => ['web','userAuth']], function () {   
    Route::group(['prefix' => 'assessment/course-resources', 'as' => 'course-resources.'], function () {
        Route::get('download-all-resource/{id}', [AssessmentController::class, 'downloadAllResourceZip'])->name('download-all-resource');
    });
});