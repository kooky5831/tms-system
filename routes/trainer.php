<?php

/*
|--------------------------------------------------------------------------
| Front Routes
|--------------------------------------------------------------------------
|
| Here is where you can register front routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['namespace' => 'Trainer', 'as' => 'trainer.', 'prefix' => 'trainer'], function () {
    Route::get('/', 'HomeController@index')->name('dashboard');

    Route::post('search-trainerdata', 'HomeController@assessmentListDatatable')->name('ajax.search.trainerdata');
    Route::post('search-trainer-courseruns', 'HomeController@courseListDatatable')->name('ajax.search.trainer.courseruns');
    Route::get('search-courseruns', 'CourseController@searchTrainerCourseRuns')->name('ajax.search.courseruns');
    
    Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
        Route::get('/mark-assessmnet/{id}', 'ExamSettingController@markAssessment')->name('mark_assessment');
        Route::post('/mark-assessmnet/{id}', 'ExamSettingController@listDatatableMark')->name('listdatatable_mark');
        Route::post('/generate-assessment', 'ExamSettingController@generateAssessmentTrainer')->name('generate_assess');
        
        //new routes ps start
        Route::get('/all-assessments/{id}', 'ExamSettingController@allAssessments')->name('all_assessments');
        Route::get('/main-assessments/{id}', 'ExamSettingController@getMainAssessments')->name('get-assessments');
        //new routes ps end

        Route::match(['GET', 'POST'], '/add', 'ExamSettingController@create')->name('add');
        Route::match(['GET', 'POST'], '/add-questions/{id}', 'ExamSettingController@createQuestions')->name('add-questions');
        Route::match(['GET', 'POST'], 'edit/{id}', 'ExamSettingController@update')->name('edit');

    });
    /* venue course start */
    Route::group(['prefix' => 'courserun', 'as' => 'course.'], function () {
        Route::get('/', 'CourseController@index')->name('list');
        Route::post('list', 'CourseController@listDatatable')->name('listdatatable');
        Route::get('/view/{id}', 'CourseController@courseRunView')->name('courserunview');
        Route::get('{id}/student', 'CourseController@studentList')->name('student');
        // Route::get('{id}/student/generate-certificate', 'CourseController@generateCertificateForAll')->name('generateCertificate');
        Route::get('generate-attendance/{id}', 'CourseController@generateAttendance')->name('generate-attendance');
        Route::get('generate-assessment/{id}', 'CourseController@generateAssessment')->name('generate-assessment');
        Route::get('{id}/attendance-assessment', 'CourseController@attendanceAssessment')->name('get-attendance-assessment');

        // Route::post('{id}/attendance-assessment', 'CourseController@saveAttendanceAssessment')->name('save-attendance-assessment');
        Route::post('{id}/updateExamFlag-assessment', 'CourseController@saveAttendanceAssessment')->name('save-attendance-assessment');

        // Export
        Route::post('course-runs-trainee-export/{id}', 'CourseController@courseRunTraineeExportExcel')->name('courseRunTrainee.export.excel');
        Route::post('course-runs-refreshers-export/{id}', 'CourseController@courseRunRefreshersExportExcel')->name('courseRunRefreshers.export.excel');
    });

    //Exam settings routes start
    Route::group(['prefix' => 'exam-settings', 'as' => 'exam-settings.'], function () {
        Route::get('/', 'ExamSettingController@index')->name('list');        
        Route::post('list', 'ExamSettingController@examCourseRunsListDatatable')->name('listdatatable');

        Route::get('{id}/{courserunid}/assigned/{exam_id}', 'ExamSettingController@assignedExamStudent')->name('assigned');
        Route::get('/delete-questions/{id}', 'ExamSettingController@deleteQuestions')->name('delete-question');

        Route::post('assessment/review-marks', 'ExamSettingController@storeAssessment')->name('assessment.exam.reviews');
        Route::get('delete/{id}', 'ExamSettingController@delete')->name('delete');

        Route::get('restore/{id}', 'ExamSettingController@restore')->name('restore');
        Route::post('image-upload', 'ExamSettingController@storeCkImage')->name('ck-image-upload');
        
        Route::post('image-delete', 'ExamSettingController@deleteImageById')->name('delete-image');
        Route::post('assess-name', 'ExamSettingController@assessName')->name('assess_name');

        // New Routes for view trainees
        Route::get('/view-trainees/{courserunid}', 'ExamSettingController@viewTrainees')->name('view_trainees');
        Route::post('/view-trainees/{courserunid}', 'ExamSettingController@viewAllTraineesDataTable')->name('list_view_trainees');
        Route::get('/review-student-qa/student/{assessmentId}/{studentenr}', 'ExamSettingController@reviewAssessment')->name('review_stud_exam');
    });
    //Exam settings routes end

    Route::group(['prefix' => 'studentenrolment', 'as' => 'studentenrolment.'], function () {
        Route::get('/{id?}', 'CourseController@index')->name('list');
        Route::post('list', 'CourseController@listDatatable')->name('listdatatable');
        Route::get('view/{id}', 'CourseController@studentEnrolmentView')->name('view');
        // Route::get('student-urls-list/student', 'ExamSettingController@studentUrlsListindex')->name('student_urls_listindex');
        // Route::post('student-urls-list/student', 'ExamSettingController@studentUrlsList')->name('student_urls_list');
    });

    Route::group(['prefix' => 'refreshers', 'as' => 'refreshers.'], function () {
        Route::match(['GET', 'POST'], 'add/{id}', 'CourseController@refreshersAdd')->name('add');
        Route::match(['GET', 'POST'], 'edit/{id}', 'CourseController@refreshersEdit')->name('edit');
    });

    // General Task
    Route::match(['GET', 'POST'], '/profile', 'UserController@profile')->name('profile');
    Route::match(['GET', 'POST'], '/change-password', 'UserController@changePassword')->name('changepassword');

    Route::prefix('ajax')->group(function () {
        Route::post('get-coursemain-list', 'CourseController@getCourseMainList')->name('ajax.getcoursermain.modal.list');
        Route::post('refresher-view-notes', 'CourseController@refresherNotesView')->name('ajax.refresher.modal.viewnotes');
        Route::post('upload-documents', 'CourseController@uploadCourseRunDocuments')->name('ajax.courserun-uploaddocuments');
        Route::post('upload-documents-edit', 'CourseController@uploadCourseRunDocumentsEdit')->name('ajax.courserun-uploaddocuments.modal.edit');
        Route::post('upload-documents-update', 'CourseController@uploadCourseRunDocumentsUpdate')->name('ajax.courserun-uploaddocuments.modal.store');
        
        //new routes ps start
        Route::post('get-all-assessments', 'ExamSettingController@getAssessmentByID')->name('ajax.get-all-assessments');
        Route::post('get-main-assessments', 'ExamSettingController@getMainAssessmentByID')->name('ajax.get-main-assessments');
        //new routes ps end
    });

    Route::group(['prefix' => 'course-resources', 'as' => 'course-resources.'], function () {
        Route::get('/', 'CourseController@courseResourceIndex')->name('index');
        Route::match(['GET', 'POST'], 'add', 'CourseController@courseResourceAdd')->name('add');
        Route::match(['GET', 'POST'], 'edit/{id}', 'CourseController@courseResourceEdit')->name('edit');
        Route::post('list', 'CourseController@resourceListDatatable')->name('listdatatable');
        Route::get('/resources/{id}/{resourceId}', 'CourseController@getResourceById')->name('get-resources');
        Route::get('/remove-resource/{id}', 'CourseController@removeResourceById')->name('remove-resource');
    });
    
});
