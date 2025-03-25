<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('getcourseschedule', 'HomeController@getCourseSchedule')->name('api.getcourseschedule');
Route::get('getcoursescheduledates', 'HomeController@getCourseScheduleDates')->name('api.getcoursescheduledates');
Route::get('getcoursename/{id}', 'HomeController@getCourseNameById')->name('api.getcoursenamebyid');

Route::post('validate-course-enrolment', 'HomeController@validateCourseEnrolment')->name('api.courseenrollement');
Route::post('student-enrolment', 'HomeController@studentEnrolment')->name('api.studentenrollement');

Route::post('payment', 'HomeController@addPayment')->name('api.addpayment');

Route::get('program-types', 'HomeController@programTypes')->name('api.programtypes');

Route::group(['middleware' => 'auth:api'], function() {
    //
});
