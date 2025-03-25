<?php

use App\Notifications\GenerateCertificateToAdmin;


Route::group(['namespace' => 'Admin', 'as' => 'admin.', 'prefix' => 'admin'], function () {
    Route::get('/', 'HomeController@index')->name('dashboard');
    Route::get('testingtpg', 'HomeController@testTPGateway')->name('home.testtpg');
    Route::get('downloadlogfile/{fname}', 'HomeController@downloadLogFile')->name('download.log');
    Route::get('gettptrainers', 'HomeController@getAllTrainers')->name('home.gettptrainers');
    Route::get('reset-password-nric/{nric}', 'HomeController@resetNricAsPassword')->name('home.resetnricaspassword');

	Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        // super admin
        Route::get('superadmin', 'UserController@superadminUsers')->name('superadmin');
        Route::post('superadmin/list', 'UserController@superadminUsersDatatable')->name('superadmin.listdatatable');
        Route::match(['GET', 'POST'], 'superadmin-add', 'UserController@superadminAdd')->name('superadmin.add');
        Route::match(['GET', 'POST'], 'superadmin-edit/{id}', 'UserController@superadminEdit')->name('superadmin.edit');
    });

    Route::get('user/staff', 'UserController@adminUsers')->name('user.admin');
    Route::post('user/staff/list', 'UserController@adminUsersDatatable')->name('user.admin.listdatatable');
	Route::match(['GET', 'POST'], 'user/staff-add', 'UserController@adminAdd')->name('admin.add');
	Route::match(['GET', 'POST'], 'user/staff-edit/{id}', 'UserController@adminEdit')->name('admin.edit');

	Route::get('user/trainer', 'UserController@trainerUsers')->name('user.trainer');
    Route::post('user/trainer/list', 'UserController@trainerUsersDatatable')->name('user.trainer.listdatatable');
	Route::match(['GET', 'POST'], 'user/trainer/view/{id}', 'UserController@trainerView')->name('user.trainer.view');
	Route::match(['GET', 'POST'], 'user/trainer-add', 'UserController@trainerAdd')->name('trainer.add');
	Route::match(['GET', 'POST'], 'user/trainer-edit/{id}', 'UserController@trainerEdit')->name('trainer.edit');
    Route::post('get-trainer-response-view-modal', 'UserController@getTrainerResponseView')->name('ajax.trainerResponse.modal.view');
	/* venue route start */
    Route::group(['prefix' => 'venue', 'as' => 'venue.'], function () {
		Route::get('/', 'VenueController@index')->name('list');
        Route::post('list', 'VenueController@listDatatable')->name('listdatatable');
        Route::match(['GET', 'POST'], 'add', 'VenueController@venueAdd')->name('add');
        Route::match(['GET', 'POST'], 'edit/{id}', 'VenueController@venueEdit')->name('edit');
	});
	/* venue route end */

	// course run start
	Route::group(['prefix' => 'courserun', 'as' => 'course.'], function () {
        Route::get('/', 'CourseController@listAllIndex')->name('listall');
        Route::post('list/', 'CourseController@listAllDatatable')->name('listalldatatable');
        Route::get('completed', 'CourseController@listAllCompletedIndex')->name('listallcompleted');
        Route::post('completedlist/', 'CourseController@listAllCompletedDatatable')->name('listallcompleteddatatable');
		Route::get('/{id}', 'CourseController@index')->name('list');
        Route::post('list/{id}', 'CourseController@listDatatable')->name('listdatatable');
        Route::get('/view/{id}', 'CourseController@courseRunView')->name('courserunview');
        Route::get('{id}/student', 'CourseController@studentList')->name('student');
        Route::get('{id}/student/generate-certificate', 'CourseController@generateCertificateForAll')->name('generateCertificate');
        Route::match(['GET', 'POST'], 'add/{id?}', 'CourseController@courseAdd')->name('add');
        Route::match(['GET', 'POST'], 'edit/{id}', 'CourseController@courseEdit')->name('edit');
        Route::get('generate-attendance/{id}', 'CourseController@generateAttendance')->name('generate-attendance');
        Route::get('generate-assessment/{id}', 'CourseController@generateAssessment')->name('generate-assessment');
        Route::get('{id}/attendance-assessment', 'CourseController@attendanceAssessment')->name('get-attendance-assessment');
        Route::post('{id}/attendance-assessment', 'CourseController@saveAttendanceAssessment')->name('save-attendance-assessment');
        Route::post('submit-attendance-tpgateway/{id}', 'CourseController@submitAttendanceTpGateway')->name('submit-attendance-tpgateway');
        Route::post('submit-assessment-tpgateway/{id}', 'CourseController@submitAssessmentTpGateway')->name('submit-assessment-tpgateway');
        Route::get('add-to-tpgateway/{id}', 'CourseController@addCourseRunToTpGateway')->name('add-courserun-tpgateway');
        Route::post('sync-attendance-tpgateway/{id}', 'CourseController@syncAttendanceTpGateway')->name('sync-attendance-tpgateway');
        Route::post('sync-assessment-tpgateway/{id}', 'CourseController@syncAssessmentTpGateway')->name('sync-assessment-tpgateway');

        //Submit payment to TP gateway
        Route::post('submit-payment-tpgateway/{id}', 'CourseController@submitPaymentTpGateway')->name('submit-payment-tpgateway');

        //Get Payment status from TP gateway
        Route::post('get-payment-tpgateway/{id}', 'CourseController@getPaymentTpGateway')->name('get-payment-tpgateway');


        //Cancel Course Run
        Route::post('get-courserun-cancel', 'CourseController@courseRunCancel')->name('ajax.courserun.modal.cancel');

        /*grant calculator test api start*/
        // Route::get('/{id}', 'CourseController@grantCalculator')->name('grantcalculator');
        /*Route::match(['GET', 'POST'], 'edit/{id}', 'CourseController@courseEdit')->name('edit');*/

        /*grant calculator test api end*/

        //Route::get('generate-documents/{id}', 'CourseController@generateDocuments')->name('generate-documents');

        //Generate-invoice for all
        Route::get('{id}/student/preview-invoice', 'InvoiceController@previewInvoice')->name('preview-invoice');
        Route::get('{id}/create-invoice-pdf', 'InvoiceController@createInvoicePdf')->name('createinvoice');
        Route::get('grant-calculation/{id}', 'InvoiceController@grantCalculation')->name('calculation');
        

	});
	// course run end

	// courseType start
	Route::group(['prefix' => 'course-type', 'as' => 'coursetype.'], function () {
		Route::get('/', 'CourseTypeController@index')->name('list');
        Route::match(['GET', 'POST'], 'add', 'CourseTypeController@courseTypeAdd')->name('add');
        Route::match(['GET', 'POST'], 'edit/{id}', 'CourseTypeController@courseTypeEdit')->name('edit');
	});
	// courseType end

	// main course start
	Route::group(['prefix' => 'coursemain', 'as' => 'coursemain.'], function () {
		Route::get('/', 'CourseMainController@index')->name('list');
        Route::post('list', 'CourseMainController@listDatatable')->name('listdatatable');
        Route::match(['GET', 'POST'], 'add', 'CourseMainController@courseMainAdd')->name('add');
        Route::match(['GET', 'POST'], 'edit/{id}', 'CourseMainController@courseMainEdit')->name('edit');
        Route::get('/checkcourse', 'CourseMainController@courseMainCheck')->name('check');
	});
	// main course end

    // course triggers start
    Route::group(['prefix' => 'coursetrigger', 'as' => 'coursetrigger.'], function () {
        Route::get('/', 'CourseMainController@triggersListIndex')->name('list');
        Route::post('list', 'CourseMainController@triggersListDatatable')->name('listdatatable');
        Route::match(['GET', 'POST'], 'add', 'CourseMainController@addTrigger')->name('add');
        Route::match(['GET', 'POST'], 'edit/{id}', 'CourseMainController@editTrigger')->name('edit');
        Route::get('test-trigger-sms', 'CourseMainController@testsmsTriggers')->name('testsmsTriggers');
    });
    // course triggers end

    // admin tasks start
    Route::group(['prefix' => 'tasks', 'as' => 'tasks.'], function () {
        Route::get('/', 'AdminTasksController@index')->name('list');
        Route::post('list', 'AdminTasksController@listDatatable')->name('listdatatable');
        Route::get('send-sms/{id}', 'AdminTasksController@sendTaskSMS')->name('sendTasksms');
        Route::post('send-sms/{id}/submit', 'AdminTasksController@sendTaskSMSsubmit')->name('sendTasksmsSubmit');
        Route::get('send-email/{id}', 'AdminTasksController@sendTaskEmail')->name('sendTaskEmail');
        Route::post('send-email/{id}/submit', 'AdminTasksController@sendTaskEmailsubmit')->name('sendTaskEmailSubmit');
    });
    // admin tasks end

    // admin tasks email logs start
    Route::group(['prefix' => 'maillogs', 'as' => 'maillogs.'], function () {
        Route::get('/', 'AdminTasksMailLogs@index')->name('list');
        Route::post('list', 'AdminTasksMailLogs@listDatatable')->name('listdatatable');
        Route::get('view/{id}', 'AdminTasksMailLogs@viewMailLogs')->name('viewmaillogs');

    });
    // admin tasks email logs end
    

    // admin errors start
    /*Route::group(['prefix' => 'errors', 'as' => 'errors.'], function () {
        Route::get('/', 'AdminErrorsController@index')->name('list');
        Route::post('list', 'AdminErrorsController@listDatatable')->name('listdatatable');
        Route::post('get-exception-update-status', 'AdminErrorsController@updateStatus')->name('adminerror.modal.getexception');
        Route::post('exception-update-notes', 'AdminErrorsController@updateException')->name('adminerror.modal.updateexception');
    });*/
    // admin errors end

    // SMS templates start
    Route::group(['prefix' => 'sms-templates', 'as' => 'smstemplates.'], function () {
        Route::get('/', 'SMSTemplateController@index')->name('list');
        Route::post('list', 'SMSTemplateController@listDatatable')->name('listdatatable');
        Route::match(['GET', 'POST'], 'add', 'SMSTemplateController@smsTemplateAdd')->name('add');
        Route::match(['GET', 'POST'], 'edit/{id}', 'SMSTemplateController@smsTemplateEdit')->name('edit');
    });
    // SMS templates end

    // Course tags start
    Route::group(['prefix' => 'course-tags', 'as' => 'coursetags.'], function () {
        Route::get('/', 'CourseTagsController@index')->name('list');
        Route::post('list', 'CourseTagsController@listDatatable')->name('listdatatable');
        Route::match(['GET', 'POST'], 'add', 'CourseTagsController@courseTagsAdd')->name('add');
        Route::match(['GET', 'POST'], 'edit/{id}', 'CourseTagsController@courseTagEdit')->name('edit');
    });
    // Course tags end

    // Email Templates start
    Route::group(['prefix' => 'email-templates', 'as' => 'emailtemplates.'], function () {
        Route::get('/', 'EmailTemplateController@index')->name('list');
        Route::post('list', 'EmailTemplateController@listDatatable')->name('listdatatable');
        Route::match(['GET', 'POST'], 'add', 'EmailTemplateController@emailTemplateAdd')->name('add');
        Route::match(['GET', 'POST'], 'edit/{id}', 'EmailTemplateController@emailTemplateEdit')->name('edit');
    });
    // Email Templates end

	// student_enrolments start
	Route::group(['prefix' => 'studentenrolment', 'as' => 'studentenrolment.'], function () {
		Route::match(['GET', 'POST'], 'add', 'StudentEnrolmentController@studentEnrolmentAdd')->name('add');
		Route::get('add-via-courserun/{courserun}', 'StudentEnrolmentController@studentEnrolmentAddViaCourseRun')->name('add_enroll_via');
        Route::match(['GET', 'POST'], 'edit/{id}', 'StudentEnrolmentController@studentEnrolmentEdit')->name('edit');
		Route::get('/{id?}', 'StudentEnrolmentController@index')->name('list');
        Route::post('list', 'StudentEnrolmentController@listDatatable')->name('listdatatable');
        Route::get('view/{id}', 'StudentEnrolmentController@studentEnrolmentView')->name('view');
	});
	// student_enrolments end

    // Grant Start
    Route::group(['prefix' => 'grants', 'as' => 'grants.'], function () {
		Route::get('/', 'GrantController@index')->name('list');
        Route::post('list', 'GrantController@listDatatable')->name('listdatatable');
        Route::post('grants-details-export-excel', 'GrantController@grantDetailsExportExcel')->name('grantdetails.export.excel');
	});
    // Grant End

    // students start
    Route::group(['prefix' => 'students', 'as' => 'students.'], function () {
        Route::get('/{id?}', 'StudentEnrolmentController@studentsIndex')->name('list');
        Route::post('list', 'StudentEnrolmentController@studentListDatatable')->name('listdatatable');
        Route::post('get-student-courses-modal', 'HomeController@getTraineeCourseRunModal')->name('trainee.courserun');
        // Route::get('view/{id}', 'StudentEnrolmentController@studentEnrolmentView')->name('view');
        // Route::match(['GET', 'POST'], 'add', 'StudentEnrolmentController@studentEnrolmentAdd')->name('add');
        // Route::match(['GET', 'POST'], 'edit/{id}', 'StudentEnrolmentController@studentEnrolmentEdit')->name('edit');
    });
    // student_enrolments end

    Route::group(['prefix' => 'invoice-settings', 'as' => 'invoicesettings.'], function(){
        Route::get('setting', 'SettingController@invoiceSettings')->name('get.settings');
        Route::get('all-settings','SettingController@listDatatable')->name('all.settings');
        Route::get('edit/{id}', 'SettingController@editSettings')->name('edit.settings');
        Route::post('set-settings', 'SettingController@setInvoiceSetting')->name('set.settings');
    });

    Route::group(['prefix' => 'course-feedback', 'as' => 'course-feedback.'], function(){
        Route::get('edit', 'SettingController@editFeedBackSettings')->name('edit.settings');
        Route::post('set-settings', 'SettingController@setFeedbackSetting')->name('set.settings');
    });

	// course_soft_bookings start
	Route::group(['prefix' => 'softbooking', 'as' => 'softbooking.'], function () {
		Route::get('/', 'SoftBookingController@index')->name('list');
        Route::post('list', 'SoftBookingController@listDatatable')->name('listdatatable');
		Route::match(['GET', 'POST'], 'add', 'SoftBookingController@softBookingAdd')->name('add');
        Route::match(['GET', 'POST'], 'edit/{id}', 'SoftBookingController@softBookingEdit')->name('edit');
    });
	// course_soft_bookings end

    // course_waiting_list start
    Route::group(['prefix' => 'waiting-list', 'as' => 'waitinglist.'], function () {
        Route::get('/', 'WaitingListController@index')->name('list');
        Route::post('list', 'WaitingListController@listDatatable')->name('listdatatable');
        Route::match(['GET', 'POST'], 'add', 'WaitingListController@waitingListAdd')->name('add');
        Route::match(['GET', 'POST'], 'edit/{id}', 'WaitingListController@waitingListEdit')->name('edit');
    });
    // course_waiting_list end

    // course refreshers start
    Route::group(['prefix' => 'refreshers', 'as' => 'refreshers.'], function () {
        Route::match(['GET', 'POST'], 'add/{id}', 'CourseController@refreshersAdd')->name('add');
        Route::match(['GET', 'POST'], 'edit/{id}', 'CourseController@refreshersEdit')->name('edit');
        Route::get('view/{id}', 'CourseController@refreshersView')->name('view');
    });
    // course refreshers end

    // course data-import start
    Route::group(['prefix' => 'data-import', 'as' => 'dataImport.'], function () {
        Route::match(['GET', 'POST'], 'course-runs', 'DataImportController@courseRunImport')->name('courseRun');
        Route::match(['GET', 'POST'], 'student-enrolment', 'DataImportController@studentEnrolmentImport')->name('studentEnrolment');
        Route::get('sync-tpg-courseruns', 'DataImportController@syncTpgCourseRuns')->name('syncTpgCourseRuns');
        Route::get('sync-tpg-studentenrolment', 'DataImportController@syncTpgStudentEnrolment')->name('syncTpgStudentEnrolment');
    });
    // course data-import end

	// payment start
	Route::group(['prefix' => 'payment', 'as' => 'payment.'], function () {
		Route::get('/', 'PaymentController@index')->name('list');
        Route::post('list', 'PaymentController@listDatatable')->name('listdatatable');
		Route::match(['GET', 'POST'], 'add', 'PaymentController@paymentAdd')->name('add');
		Route::get('view/{id}', 'PaymentController@paymentView')->name('view');
        Route::match(['GET', 'POST'], 'edit/{id}', 'PaymentController@paymentEdit')->name('edit');
        Route::get('/payxero', 'PaymentController@payPaymentXero')->name('setPayment');
    });
	// payment end

    // reports start
    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
        Route::get('course-registration', 'ReportsController@courseRegistration')->name('courseregistration');
        Route::post('course-registration-list', 'ReportsController@courseRegistrationListDatatable')->name('courseregistration.listdatatable');
        Route::get('student-details', 'ReportsController@studentDetails')->name('studentdetails');
        Route::post('student-details-list', 'ReportsController@studentDetailsListDatatable')->name('studentDetails.listdatatable');
        Route::get('course-signups', 'ReportsController@courseSignups')->name('courseSignups');
        Route::post('course-signups-list', 'ReportsController@courseSignupsListDatatable')->name('courseSignups.listdatatable');
        Route::get('course-runs-signups', 'ReportsController@courseRunsSignups')->name('courseRunsSignups');
        // Export Excel
        Route::post('course-registration-export-excel', 'ReportsController@courseRegistrationExportExcel')->name('courseregistration.export.excel');
        Route::post('student-details-export-excel', 'ReportsController@studentDetailsExportExcel')->name('studentDetails.export.excel');
        Route::post('course-signups-export-excel', 'ReportsController@courseSignupsExportExcel')->name('courseSignups.export.excel');
        Route::post('course-signups-runs-export-excel', 'ReportsController@courseRunsSignupsExportExcel')->name('courseRunsSignups.export.excel');
        Route::post('student-report-export-excel', 'ReportsController@paymentReportExportExcel')->name('paymentReport.export.excel');
        Route::post('refresher-report-export-excel', 'ReportsController@refresherExportExcel')->name('refresher.export.excel');
        Route::post('assessment-report-export-excel', 'ReportsController@assessmentExportExcel')->name('assessment.export.excel');

        // Course Run exports
        Route::get('course-run-export', 'ReportsController@courseRunExport')->name('courserunexports');
        Route::post('course-runs-export-excel', 'ReportsController@courseRunsExportExcel')->name('courseRuns.export.excel');
        Route::post('course-runs-trainee-export/{id}', 'ReportsController@courseRunTraineeExportExcel')->name('courseRunTrainee.export.excel');
        Route::post('course-runs-refreshers-export/{id}', 'ReportsController@courseRunRefreshersExportExcel')->name('courseRunRefreshers.export.excel');
        Route::post('course-runs-export/{id}', 'ReportsController@exportCourseRun')->name('courserun.export.excel');
                
        //Payment reports
        Route::get('payment-report', 'ReportsController@paymentReport')->name('paymentreport');
        Route::post('payment-report-list', 'ReportsController@PaymentReportlistDatatable')->name('paymentreport.listdatatable');
        Route::post('payment-report-update', 'ReportsController@updatePaymentReport')->name('paymentreport.updatePayment');
        Route::get('payment-report-reminder/{id}', 'ReportsController@sendReminderEmail')->name('paymentreport.sendreminder');
        Route::post('payment-report-reminder/{id}/send', 'ReportsController@sendReminder')->name('paymentreport.send');
        Route::post('get-payment-tpg', 'CourseController@getPaymentTpGatewayByIds')->name('paymentreport.gettpg');
        Route::post('submit-payment-tpg', 'ReportsController@submitPaymentTPG')->name('paymentreport.submittpg');

        // Refresher Reports
        Route::get('refresher-details', 'ReportsController@refresherDetails')->name('refresherdetails');
        Route::post('refresher-details-list', 'ReportsController@refresherDetailsListDatatable')->name('refresherDetails.listdatatable');



    });
    // reports end

	Route::get('courserun/sessions/{courserun}', 'CourseController@getSessions');

	Route::get('staff-permission', 'UserController@adminPermission')->name('user.admin.permission');
	Route::post('staff-permission/{id}', 'UserController@adminPermissionPost')->name('user.admin.permissionpost');

    // General Task
    Route::match(['GET', 'POST'], '/profile', 'UserController@profile')->name('profile');
    Route::match(['GET', 'POST'], '/change-password', 'UserController@changePassword')->name('changepassword');

    //ActiveCampains Start
    //Tags
    Route::get('/get-tags', 'ActiveCampaignController@getTagFromActiveCampaings')->name('get-tag');
    Route::get('/add-tags', 'ActiveCampaignController@addTagToActiveCampaings')->name('add-tag');
    Route::get('/set-tags-on-contact', 'ActiveCampaignController@setTagToContactOnActiveCampaings')->name('set-tag');
    
    //Contact
    Route::get('/get-contact', 'ActiveCampaignController@getContactFromActiveCampaingsByEmail')->name('get-contact');
    Route::get('/add-contact', 'ActiveCampaignController@addContactOnActiveCampaings')->name('add-contact');

    //List
    Route::get('/get-list', 'ActiveCampaignController@getListFromActiveCampaings')->name('get-list');
    //ActiveCampains End

    Route::prefix('ajax')->group(function () {
        Route::post('get-coursemain-list', 'CourseMainController@getCourseMainList')->name('ajax.getcoursermain.modal.list');
        Route::post('get-studentenrolment-payment-list-modal', 'StudentEnrolmentController@getPaymentList')->name('ajax.studentEnrolmentPayment.modal.list');
        Route::post('get-student-courseruns-list-modal', 'StudentEnrolmentController@getStudentsCourseRunsList')->name('ajax.studentCourseRun.modal.list');
        Route::get('search-studentenrolment', 'StudentEnrolmentController@searchStudentEnrolment')->name('ajax.search.studentenrolment');
        Route::get('search-maincourses', 'CourseMainController@searchMainCourses')->name('ajax.search.maincourses');
        Route::get('search-courseruns', 'CourseController@searchCourseRuns')->name('ajax.search.courseruns');
        Route::get('search-student', 'StudentEnrolmentController@searchStudent')->name('ajax.search.student');

        Route::post('get-studentenrolment-cancel', 'StudentEnrolmentController@studentEnrolmentCancel')->name('ajax.studentEnrolment.modal.cancel');
        Route::post('get-studentenrolment-hold', 'StudentEnrolmentController@studentEnrolmentHold')->name('ajax.studentEnrolment.modal.hold');
        Route::post('do-payment-cancel', 'PaymentController@paymentCancel')->name('ajax.studentPayment.modal.cancel');

        Route::post('get-studentenrolment-response-view-modal', 'StudentEnrolmentController@getEnrolmentResponseView')->name('ajax.studentEnrolmentViewResponse.modal.view');
        Route::post('do-studentenrolment-again', 'StudentEnrolmentController@doEnrolmentAgain')->name('ajax.studentEnrolmentAgain');
        Route::post('do-studentenrolment-grant-again', 'StudentEnrolmentController@doEnrolmentGrantSearchAgain')->name('ajax.studentEnrolmentGrantSearchAgain');
        Route::post('do-studentenrolment-attendane-again', 'StudentEnrolmentController@doEnrolmentAttendanceAgain')->name('ajax.studentEnrolmentAttendanceAgain');
        Route::post('do-studentenrolment-assessment-again', 'StudentEnrolmentController@doEnrolmentAssessmentAgain')->name('ajax.studentEnrolmentAssessmentAgain');
        Route::post('get-student-edit', 'StudentEnrolmentController@getStudentsEdit')->name('ajax.studentEdit.modal');
        Route::post('student-edit-submit', 'StudentEnrolmentController@getStudentsEditSubmit')->name('ajax.studentEdit.modal.store');
        Route::post('get-studentrefresher-response-view-modal', 'StudentEnrolmentController@getRefresherResponseView')->name('ajax.studentRefresherViewResponse.modal.view');
        Route::post('do-studentrefresher-attendane-again', 'StudentEnrolmentController@doRefresherAttendanceAgain')->name('ajax.studentRefresherAttendanceAgain');
        Route::post('do-studentrefresher-assessment-again', 'StudentEnrolmentController@doRefresherAssessmentAgain')->name('ajax.studentRefresherAssessmentAgain');

        Route::post('upload-documents', 'CourseController@uploadCourseRunDocuments')->name('ajax.courserun-uploaddocuments');
        Route::post('upload-documents-edit', 'CourseController@uploadCourseRunDocumentsEdit')->name('ajax.courserun-uploaddocuments.modal.edit');
        Route::post('upload-documents-update', 'CourseController@uploadCourseRunDocumentsUpdate')->name('ajax.courserun-uploaddocuments.modal.store');

        Route::post('softbooking-view-notes', 'SoftBookingController@softNotesView')->name('ajax.softbooking.modal.viewnotes');
        Route::post('waiting-list-view-notes', 'WaitingListController@waitingNotesView')->name('ajax.waitinglist.modal.viewnotes');
        Route::post('refresher-view-notes', 'CourseController@refresherNotesView')->name('ajax.refresher.modal.viewnotes');

        Route::post('get-task-update-notes', 'AdminTasksController@updateNotesGetView')->name('ajax.admintask.modal.getupdatenotes');
        Route::post('task-update-notes', 'AdminTasksController@updateNotes')->name('ajax.admintask.modal.updatenotes');
        Route::post('mark-task-completed', 'AdminTasksController@markTaskCompleted')->name('ajax.admintask.modal.markTaskCompleted');
        Route::post('get-task-details', 'AdminTasksController@getTaskDetailsView')->name('ajax.admintask.modal.gettaskdetails');
        Route::post('mark-task-uncomplete', 'AdminTasksController@marktaskUncomplete')->name('ajax.admintask.modal.marktaskUncomplete');
        
        //mark-all-task-complete
        Route::post('mark-all-task-completed', 'AdminTasksController@markAllTaskCompleted')->name('ajax.admintask.modal.markAllTaskCompletere');

        // reports
        Route::post('courserun-list', 'ReportsController@courseRunList')->name('ajax.reports.courserun.list');

        //Student Activity
        // Route::post('get-student-activity-list-modal', 'StudentEnrolmentController@getStudentsActivityList')->name('ajax.studentActivity.modal.list');
        //Get TGP Payment response for enrollment
        Route::post('get-studentpayment-response-view-modal', 'StudentEnrolmentController@getPaymentResponseView')->name('ajax.studentPaymentResponse.modal.view');
        Route::post('get-tpg-courseruns', 'DataImportController@getTpgCourseRunsById')->name('ajax.getTpgCourseRun');
        Route::post('save-tpg-courseruns', 'DataImportController@saveCourseRunTpGateway')->name('ajax.saveTpgCourseRun');
        Route::post('get-tpg-studentenrolment', 'DataImportController@getTpgStudentEnrolmentById')->name('ajax.getTpgStudentEnrolment');
        Route::post('save-tpg-studentenrolment', 'DataImportController@saveTpgStudentEnrolment')->name('ajax.saveTpgStudentEnrolment');


        Route::post('generate-documents', 'CourseController@generateDocuments')->name('ajax.generate.documents');

        //fetch grant status
        Route::post('grant-status', 'GrantController@fetchGrantStatus')->name('ajax.grant.status');

        // Void Assessment
        Route::post('void-student-assessment', 'CourseController@voidStudentAssessment')->name('ajax.voidAssessment');

        Route::post('get-all-assessments', 'ExamSettingController@getAssessmentByID')->name('ajax.get-all-assessments');
        Route::post('get-main-assessments', 'ExamSettingController@getMainAssessmentByID')->name('ajax.get-main-assessments');
    });

    //Xero implmentation
    Route::group(['prefix' => 'xero', 'as' => 'xero.'], function(){
    //     Route::get('create-invoice/{id}', 'XeroController@createInvoiceForCourserun')->name('create-invoice');
        Route::get('generate-invoice/{id}', 'XeroController@createXeroInvoice')->name('generate.xeroinvoice');
    //     Route::get('edit-invoice/{id}', 'XeroController@editXeroInvoice')->name('edit-invoice');
        Route::get('update-invoice/{id}', 'XeroController@updateXeroInvoice')->name('update.xeroinvoice');
        Route::get('create-xero-contact', 'XeroController@createXeroContact')->name('create-xero-contact');
        Route::get('get-invoice-xero/{id}', 'XeroController@getXeroInvoice')->name('get-xero-invoice');
        Route::get('void-invoice', 'XeroController@voidXeroInvoice')->name('void-xero-invoice');
        Route::get('get-invoices', 'XeroController@getInvoices')->name('get-xero-invoices');
        Route::get('get-accounts', 'XeroController@getAllAccounts')->name('get-xero-accounts');
        Route::get('get-taxrates', 'XeroController@getAllTaxRates')->name('get-xero-taxrates');
        Route::post('save-xero-codes', 'XeroController@setXeroCodes')->name('set-xero-code');
        Route::get('get-xero-codes', 'XeroController@getAllSavedCode')->name('get-xero-code');
        Route::get('get-xero-themes', 'XeroController@getAllBrandingTheme')->name('get-xero-themes');
        Route::match(['GET', 'POST'], '/set-xero-theme', 'XeroController@settingTheme')->name('set-xero-theme');
    });

    /*
    * We name this route xero.auth.success as by default the config looks for a route with this name to redirect back to
    * after authentication has succeeded. The name of this route can be changed in the config file.
    */
    Route::get('/manage/xero', 'XeroController@index')->name('xero.auth.success');
    Route::get('/xero/create-invoice', 'XeroController@createInvoiceXero')->name('xero.createinvoice');
    Route::get('/xero/create-invoice-payment', 'XeroController@createPaymentXero')->name('xero.createinvoicepayment');
    Route::get('/xero/create-contact', 'XeroController@createContactsXero')->name('xero.createcontact');
    Route::get('/xero/get-contacts', 'XeroController@getContact')->name('xero.getcontacts');
    Route::get('/xero/getcurrency', 'XeroController@getCurrencyXero')->name('xero.getcurrency');
    Route::get('/xero/getbrandingtheme', 'XeroController@getBrandingTheme')->name('xero.getbrandingtheme');
    Route::get('/xero/getlineitems', 'XeroController@getItem')->name('xero.getlineitems');

    // Route::group(['prefix' => 'activitylist', 'as' => 'activitylist.'], function () {
	// 	Route::get('/', 'ActivityController@index')->name('list');
    //     Route::post('list', 'ActivityController@listDatatable')->name('listdatatable');
    //     //Student Auto Search
    //     Route::get('/student-autocomplete-search','ActivityController@searchStudent')->name('studentsearch');
    //     //Course Auto Search
    //     Route::get('/course-autocomplete-search','ActivityController@searchCourse')->name('coursesearch');
	// });

    Route::group(['prefix' => 'activities', 'as' => 'activities.'], function () {
        Route::get('/', 'AuditLogController@index')->name('list');
        Route::post('list', 'AuditLogController@listDatatable')->name('listdatatable');
        Route::get('/student-autocomplete-search','AuditLogController@searchStudent')->name('studentsearch');
        Route::get('/get-actions', 'AuditLogController@actionableDropdown')->name('get-actions');
        Route::get('/course-autocomplete-search','AuditLogController@searchCourse')->name('coursesearch');
    });

    /* Grant Log Routes Start */
    Route::group(['prefix' => 'grant', 'as' => 'grant.'], function () {
        Route::get('/', 'GrantLogController@index')->name('list');
        Route::post('list', 'GrantLogController@listDatatable')->name('listdatatable');
        Route::post('get-grant-action', 'GrantLogController@updateStatus')->name('grantlog.modal.getgrantaction');
        Route::post('grantlog-update-notes', 'GrantLogController@updateGrantLog')->name('grantlog.modal.updategrantlog');
        Route::post('grantlog-mark-resolved', 'GrantLogController@resolvedGrantLog')->name('grantlog.resolvedgrantlog');
        Route::post('grantlog-export', 'GrantLogController@grantLogExportExcel')->name('grantlog.export.excel');
    });
    /* Grant Log Routes End */


    /* platform type route start */
    // Route::group(['prefix' => 'platformtype', 'as' => 'platformtype.'], function () {
	// 	Route::get('/', 'PlatformTypeController@index')->name('list');
    //     Route::post('list', 'PlatformTypeController@listDatatable')->name('listdatatable');
    //     Route::match(['GET', 'POST'], 'add', 'PlatformTypeController@platformTypeAdd')->name('add');
    //     Route::match(['GET', 'POST'], 'edit/{id}', 'PlatformTypeController@platformTypeEdit')->name('edit');
	// });

    Route::group(['prefix' => 'program-type', 'as' => 'programtype.'], function () {
		Route::get('/', 'ProgramTypeController@index')->name('list');
        Route::match(['GET', 'POST'], 'add', 'ProgramTypeController@programTypeAdd')->name('add');
        Route::match(['GET', 'POST'], 'edit/{id}', 'ProgramTypeController@programTypeEdit')->name('edit');
	});
	/* platform type route end */
    
    /* Route For slack webhook */
    Route::post('/slack-endpoint', 'SlackController@slackResponse')->withoutMiddleware(['auth', 'adminAuth']);

    //Exam settings routes
    Route::group(['prefix' => 'assessments', 'as' => 'assessments.'], function(){
        Route::group(['prefix' => 'exam-settings', 'as' => 'exam-settings.'], function () {
            Route::get('/', 'ExamSettingController@index')->name('list');
            Route::post('list', 'ExamSettingController@listDatatable')->name('listdatatable');

            Route::get('{id}/{courserunid}/assigned/{exam_id}', 'ExamSettingController@assignedExamStudent')->name('assigned');
            Route::match(['GET', 'POST'], '/add', 'ExamSettingController@create')->name('add');

            Route::match(['GET', 'POST'], '/add-questions/{id}', 'ExamSettingController@createQuestions')->name('add-questions');
            Route::get('/delete-questions/{id}', 'ExamSettingController@deleteQuestions')->name('delete-question');

            Route::match(['GET', 'POST'], 'edit/{id}', 'ExamSettingController@update')->name('edit');
            Route::post('assessment/review-marks', 'ExamSettingController@storeAssessment')->name('exam.reviews');
    
    
            /*Route::get('student-exam-link/{id}', 'ExamSettingController@createStudentExamLink')->name('studetn_exam_link');
            Route::get('review-student-exam/{id}/student', 'ExamSettingController@indexStudentList')->name('review_student_exam_list_data');
            Route::post('review-student-exam/{id}/student', 'ExamSettingController@reviewStudentExamList')->name('review_studetn_exam_list');
            Route::get('reassign-student-exam/student', 'ExamSettingController@remarkStudentExamModal')->name('remark_student_modal');
    
            Route::post('reassign-student-exam/{id}/student', 'ExamSettingController@remarkStudentExam')->name('remark_student');
            Route::get('reassign-student-exam/{id}/student', 'ExamSettingController@reassignStudentExam')->name('reassign_exam');
            
            Route::post('get-studentenrolment-remark-list-modal', 'ExamSettingController@getRemarksList')->name('remarks_list');
            Route::get('get-courserun/{id}', 'ExamSettingController@courseRunofCourseMain')->name('course_runlist');
            Route::post('get-courserun-list/{id}', 'ExamSettingController@courseRunList')->name('course_runlistDatatable');*/
    
        
            /*Route::get('student-exam-link/{id}', 'ExamSettingController@createStudentExamLink')->name('studetn_exam_link');
            Route::get('review-student-exam/{id}/student', 'ExamSettingController@indexStudentList')->name('review_student_exam_list_data');
            Route::post('review-student-exam/{id}/student', 'ExamSettingController@reviewStudentExamList')->name('review_studetn_exam_list');
            Route::get('reassign-student-exam/student', 'ExamSettingController@remarkStudentExamModal')->name('remark_student_modal');
    
            Route::post('reassign-student-exam/{id}/student', 'ExamSettingController@remarkStudentExam')->name('remark_student');
            Route::get('reassign-student-exam/{id}/student', 'ExamSettingController@reassignStudentExam')->name('reassign_exam');
            
            Route::post('get-studentenrolment-remark-list-modal', 'ExamSettingController@getRemarksList')->name('remarks_list');
            Route::get('get-courserun/{id}', 'ExamSettingController@courseRunofCourseMain')->name('course_runlist');
            Route::post('get-courserun-list/{id}', 'ExamSettingController@courseRunList')->name('course_runlistDatatable');*/
    
            Route::get('delete/{id}', 'ExamSettingController@delete')->name('delete');
            Route::get('restore/{id}', 'ExamSettingController@restore')->name('restore');
    
            Route::post('image-upload', 'ExamSettingController@storeCkImage')->name('ck-image-upload');
            Route::post('image-delete', 'ExamSettingController@deleteImageById')->name('delete-image');
    
            Route::post('assess-name', 'ExamSettingController@assessName')->name('assess_name');

            Route::get('/all-assessments/{id}', 'ExamSettingController@allAssessments')->name('all_assessments');
            Route::get('/main-assessments/{id}', 'ExamSettingController@getMainAssessments')->name('get-assessments');

            Route::get('/get-assessment-pdf/{assessmentID}/{studentEnrolId}', 'ExamSettingController@getDataAssessmentPdf')->name('get_pdf_assessment');
        });
        Route::group(['prefix' => 'examdashboard', 'as' => 'examdashboard.'], function () {

            Route::get('/', 'HomeController@examindex')->name('examdashboard');
            Route::post('search-trainerdata', 'HomeController@assessmentListDatatable')->name('ajax.search');

            Route::get('/mark-assessmnet/{id}', 'ExamSettingController@markAssessment')->name('mark_assessment');
            Route::post('/mark-assessmnet/{id}', 'ExamSettingController@listDatatableMark')->name('listdatatable_mark');

            Route::get('/review-student-qa/student/{assessmentId}/{studentenr}', 'ExamSettingController@reviewAssessment')->name('review_stud_exam');
            Route::post('/generate-assessment', 'ExamSettingController@generateAssessmentadmin')->name('generate_assess');

            Route::get('/view-trainees/{courserunid}', 'ExamSettingController@viewTrainees')->name('view_trainees');
            Route::post('/view-trainees/{courserunid}', 'ExamSettingController@viewAllTraineesDataTable')->name('list_view_trainees');

        });
    });

    Route::group(['prefix' => 'course-resources', 'as' => 'course-resources.'], function () {
        Route::get('/', 'CourseMainController@courseResourceIndex')->name('index');
        Route::match(['GET', 'POST'], 'add', 'CourseMainController@courseResourceAdd')->name('add');
        Route::match(['GET', 'POST'], 'edit/{id}', 'CourseMainController@courseResourceEdit')->name('edit');
        Route::post('list', 'CourseMainController@resourceListDatatable')->name('listdatatable');
        Route::get('/resources/{id}/{resourceId}', 'CourseMainController@getResourceById')->name('get-resources');
        Route::get('/remove-resource/{id}', 'CourseMainController@removeResourceById')->name('remove-resource');
    });
});

