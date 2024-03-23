<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [AuthController::class, 'login']);
Route::post('verify_otp',[AuthController::class,'verify_otp']);
Route::post('send_otp',[AuthController::class,'send_otp']);

Route::group(['middleware' => 'jwt'], function(){
    Route::post('edit_profile',[UserController::class,'edit_profile']);
    Route::get('get_profile',[UserController::class,'get_profile']);
});    
