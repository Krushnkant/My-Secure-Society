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
use App\Http\Controllers\Api\V1\AmenityController;
use App\Http\Controllers\Api\V1\EmergencyContactController;
use App\Http\Controllers\Api\V1\EmergencyAlertController;
use App\Http\Controllers\Api\V1\BusinessProfileController;

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
    Route::post('get_token',[AuthController::class,'get_token']);

    Route::post('profile/set',[UserController::class,'edit_profile']);
    Route::get('profile/get',[UserController::class,'get_profile']);
    Route::post('profile/update_profilepic',[UserController::class,'update_profilepic']);
    Route::post('profile/update_coverpic',[UserController::class,'update_coverpic']);
    Route::get('profile/my_address',[UserController::class,'address_list']);

    Route::post('users/flat/save',[SocietyMemberController::class,'save_flat']);
    Route::post('users/flat/list',[SocietyMemberController::class,'flat_list']);
    Route::post('users/flat/delete',[SocietyMemberController::class,'delete_flat']);

    Route::post('family_member/save',[FamilyMemberController::class,'save_family']);
    Route::post('family_member/list',[FamilyMemberController::class,'family_list']);
    Route::post('family_member/delete',[FamilyMemberController::class,'delete_family_member']);

    Route::post('folder/save',[DocumentFolderController::class,'save_folder']);
    Route::post('folder/list',[DocumentFolderController::class,'folder_list']);
    Route::post('folder/delete',[DocumentFolderController::class,'delete_folder']);
    Route::post('folder/get',[DocumentFolderController::class,'get_folder']);

    Route::post('document/save',[SocietyDocumentController::class,'save_document']);
    Route::post('document/list',[SocietyDocumentController::class,'document_list']);
    Route::post('document/delete',[SocietyDocumentController::class,'delete_document']);
    Route::post('document/get',[SocietyDocumentController::class,'get_document']);

    Route::get('banner/list',[BannerController::class,'banner_list']);
    Route::get('banner/config/get',[BannerController::class,'get_banner_config']);
    Route::post('banner/config/set',[BannerController::class,'set_banner_config']);

    Route::post('society/list',[SocietyController::class,'society_list']);
    Route::post('block/list',[SocietyController::class,'block_list']);
    Route::post('flat/list',[SocietyController::class,'flat_list']);

    Route::post('resident/list',[ResidentController::class,'resident_list']);
    Route::post('resident/get',[ResidentController::class,'get_resident']);
    Route::post('resident/change_status',[ResidentController::class,'change_status']);
    Route::post('resident/update_designation',[ResidentController::class,'update_designation']);

    Route::post('announcement/save',[AnnouncementController::class,'save_announcement']);
    Route::post('announcement/list',[AnnouncementController::class,'announcement_list']);
    Route::post('announcement/delete',[AnnouncementController::class,'delete_announcement']);
    Route::post('announcement/get',[AnnouncementController::class,'get_announcement']);

    Route::post('daily_post/save',[PostController::class,'save_daily_post']);
    Route::post('daily_post/list',[PostController::class,'daily_post_list']);
    Route::post('daily_post/delete',[PostController::class,'delete_daily_post']);
    Route::post('daily_post/get',[PostController::class,'get_daily_post']);
    Route::post('daily_post/update_like',[PostController::class,'update_like']);

    Route::post('amenity/save',[AmenityController::class,'save_amenity']);
    Route::post('amenity/list',[AmenityController::class,'amenity_list']);
    Route::post('amenity/delete',[AmenityController::class,'delete_amenity']);
    Route::post('amenity/get',[AmenityController::class,'get_amenity']);

    Route::post('amenity/booking/create',[AmenityController::class,'create_amenity_booking']);
    Route::post('amenity/booking/list',[AmenityController::class,'amenity_booking_list']);
    Route::post('amenity/booking/change_status',[AmenityController::class,'amenity_booking_change_status']);

    Route::post('emergency_contact/save',[EmergencyContactController::class,'save_emergency_contact']);
    Route::post('emergency_contact/list',[EmergencyContactController::class,'emergency_contact_list']);
    Route::post('emergency_contact/delete',[EmergencyContactController::class,'delete_emergency_contact']);

    Route::post('emergency_alert/create',[EmergencyAlertController::class,'save_emergency_alert']);
    Route::get('emergency_alert/list',[EmergencyAlertController::class,'emergency_alert_list']);
    Route::post('emergency_alert/delete',[EmergencyAlertController::class,'delete_emergency_alert']);

    Route::post('business_category/get',[BusinessProfileController::class,'get_business_category']);

    Route::post('business_profile/save',[BusinessProfileController::class,'save_business_profilet']);
    Route::post('business_profile/list',[BusinessProfileController::class,'business_profile_list']);
    Route::post('business_profile/get',[BusinessProfileController::class,'get_emergency_contact']);
    Route::post('business_profile/delete',[BusinessProfileController::class,'delete_business_profile']);

});

Route::post('country/list', [UserController::class, 'get_country']);
Route::post('state/list', [UserController::class, 'get_state']);
Route::post('city/list', [UserController::class, 'get_city']);
