<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\DocumentFolderController;
use App\Http\Controllers\Api\V1\FamilyMemberController;
use App\Http\Controllers\Api\V1\ResidentController;
use App\Http\Controllers\Api\V1\SocietyController;
use App\Http\Controllers\Api\V1\SocietyDocumentController;
use App\Http\Controllers\Api\V1\SocietyMemberController;
use App\Http\Controllers\Api\V1\AnnouncementController;
use App\Http\Controllers\Api\V1\PostController;
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
    Route::post('profile/update_profilepic',[UserController::class,'update_profilepic']);
    Route::post('profile/update_coverpic',[UserController::class,'update_coverpic']);

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

    Route::post('society/list',[SocietyController::class,'society_list']);
    Route::post('block/list',[SocietyController::class,'block_list']);
    Route::post('flat/list',[SocietyController::class,'flat_list']);

    Route::post('resident/list',[ResidentController::class,'resident_list']);
    Route::post('resident/get',[ResidentController::class,'get_resident']);
    Route::post('resident/change_status',[ResidentController::class,'change_status']);

    Route::post('announcement/save',[AnnouncementController::class,'save_announcement']);
    Route::post('announcement/list',[AnnouncementController::class,'announcement_list']);
    Route::post('announcement/delete',[AnnouncementController::class,'delete_announcement']);
    Route::post('announcement/get',[AnnouncementController::class,'get_announcement']);

    Route::post('daily_post/save',[PostController::class,'save_daily_post']);
    Route::post('daily_post/list',[PostController::class,'daily_post_list']);
    Route::post('daily_post/delete',[PostController::class,'delete_daily_post']);
    Route::post('daily_post/get',[PostController::class,'get_daily_post']);
});

Route::get('country/list', [UserController::class, 'get_country']);
Route::get('state/list/{id}', [UserController::class, 'get_state']);
Route::get('city/list/{id}', [UserController::class, 'get_city']);
