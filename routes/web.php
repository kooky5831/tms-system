<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Http\Controllers\Admin\XeroWebhookController;
Route::post('xero-webhook', XeroWebhookController::class);

Auth::routes();

Route::get('/', 'HomeController@index')->name('front.home');
Route::get('/.well-known/jwks.json', [App\Http\Controllers\JWKSController::class, 'serve']);