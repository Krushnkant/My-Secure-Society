<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\api\v1\BannerController;
use App\Http\Controllers\api\v1\DocumentFolderController;
use App\Http\Controllers\api\v1\FamilyMemberController;
use App\Http\Controllers\api\v1\SocietyDocumentController;
use App\Http\Controllers\api\v1\SocietyMemberController;
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
    Route::get('get_token',[AuthController::class,'get_token']);

    Route::post('profile/edit',[UserController::class,'edit_profile']);
    Route::get('profile/get',[UserController::class,'get_profile']);

    Route::post('flat/save',[SocietyMemberController::class,'save_flat']);
    Route::post('flats/list',[SocietyMemberController::class,'flat_list']);
    Route::post('flat/delete',[SocietyMemberController::class,'delete_flat']);

    Route::post('family/save',[FamilyMemberController::class,'save_family']);
    Route::post('family/list',[FamilyMemberController::class,'family_list']);
    Route::post('family/delete',[FamilyMemberController::class,'delete_family_member']);

    Route::post('folder/save',[DocumentFolderController::class,'save_folder']);
    Route::post('folder/list',[DocumentFolderController::class,'folder_list']);
    Route::post('folder/delete',[DocumentFolderController::class,'delete_folder']);
    Route::post('folder/get',[DocumentFolderController::class,'get_folder']);

    Route::post('document/save',[SocietyDocumentController::class,'save_document']);
    Route::post('document/list',[SocietyDocumentController::class,'folder_document']);
    Route::post('document/delete',[SocietyDocumentController::class,'delete_document']);
    Route::post('document/get',[SocietyDocumentController::class,'get_document']);

    
    Route::get('banner/list',[BannerController::class,'banner_list']);
    Route::get('banner/config/list',[BannerController::class,'banner_config_list']);
});    
