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
use App\Http\Controllers\Api\V1\BloodDonateController;
use App\Http\Controllers\Api\V1\DeliveredCourierController;
use App\Http\Controllers\Api\V1\DesignationController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\LoanRequestController;
use App\Http\Controllers\Api\V1\PaymentChargeController;
use App\Http\Controllers\Api\V1\ServiceCategoryController;
use App\Http\Controllers\Api\V1\ServiceProviderControler;
use App\Http\Controllers\Api\V1\ServiceRequestController;
use App\Http\Controllers\Api\V1\SocietyDepartmentController;
use App\Http\Controllers\Api\V1\StaffDutyAreaController;
use App\Http\Controllers\Api\V1\StaffMemberController;
use App\Http\Controllers\Api\V1\VisitorController;

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
Route::post('auth/staff_member/login', [AuthController::class, 'staff_member_login']);
Route::post('auth/staff_member/forgot_password', [AuthController::class, 'staff_member_forgot_password']);
Route::post('auth/staff_member/otp_verify',[AuthController::class,'staff_member_verify_otp']);


Route::group(['middleware' => 'jwt'], function(){
    Route::post('auth/staff_member/change_password',[AuthController::class,'staff_member_change_password']);
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
    Route::post('daily_post/change_status',[PostController::class,'change_status_daily_post']);
    Route::post('daily_post/get',[PostController::class,'get_daily_post']);
    Route::post('daily_post/update_like',[PostController::class,'update_like']);
    Route::get('daily_post/report_reason/list',[PostController::class,'report_reason_list']);
    Route::post('daily_post/update_poll',[PostController::class,'update_poll']);

    Route::post('amenity/save',[AmenityController::class,'save_amenity']);
    Route::post('amenity/list',[AmenityController::class,'amenity_list']);
    Route::post('amenity/delete',[AmenityController::class,'delete_amenity']);
    Route::post('amenity/get',[AmenityController::class,'get_amenity']);
    Route::post('amenity/slot/list',[AmenityController::class,'amenity_slot_list']);

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

    Route::post('business_profile/save',[BusinessProfileController::class,'save_business_profile']);
    Route::post('business_profile/list',[BusinessProfileController::class,'business_profile_list']);
    Route::post('business_profile/get',[BusinessProfileController::class,'get_business_profile']);
    Route::post('business_profile/delete',[BusinessProfileController::class,'delete_business_profile']);

    Route::post('blood_donate/request',[BloodDonateController::class,'request_blood_donate']);
    Route::post('blood_donate/request/list',[BloodDonateController::class,'request_blood_donate_list']);
    Route::post('blood_donate/request/get',[BloodDonateController::class,'get_request_blood_donate']);
    Route::post('blood_donate/request/change_status',[BloodDonateController::class,'change_status_request_blood_donate']);
    Route::post('blood_donate/request/reply',[BloodDonateController::class,'reply_request_blood_donate']);
    Route::post('blood_donate/request/reply/list',[BloodDonateController::class,'reply_request_blood_donate_list']);
    Route::post('blood_donate/request/reply/delete',[BloodDonateController::class,'reply_request_blood_donate_delete']);

    Route::get('daily_help/service/list',[ServiceProviderControler::class,'daily_help_service_list']);
    Route::post('daily_help/service_provider/save',[ServiceProviderControler::class,'save_service_provider']);
    Route::post('daily_help/service_provider/list',[ServiceProviderControler::class,'service_provider_list']);
    Route::post('daily_help/service_provider/get',[ServiceProviderControler::class,'get_service_provider']);
    Route::post('daily_help/service_provider/delete',[ServiceProviderControler::class,'delete_service_provider']);


    Route::post('daily_help/service_provider/add_to_flat',[ServiceProviderControler::class,'service_provider_add_flat']);
    Route::post('daily_help/service_provider/delete_flat',[ServiceProviderControler::class,'service_provider_delete_flat']);
    Route::post('daily_help/service_provider/add_review',[ServiceProviderControler::class,'service_provider_add_review']);
    Route::post('daily_help/service_provider/review/list',[ServiceProviderControler::class,'service_provider_review_list']);


    Route::post('service_vendor/list',[VisitorController::class,'service_vendor_list']);
    Route::post('gatepass/save',[VisitorController::class,'save_gatepass']);
    Route::post('gatepass/list',[VisitorController::class,'gatepass_list']);
    Route::post('gatepass/get',[VisitorController::class,'get_gatepass']);
    Route::post('gatepass/change_status',[VisitorController::class,'gatepass_change_status']);

    Route::post('visitor/new/save',[VisitorController::class,'save_new_visitor']);
    Route::post('visitor/list',[VisitorController::class,'visitor_list']);
    Route::post('visitor/change_status',[VisitorController::class,'visitor_change_status']);

    Route::post('visiting_help/category/list',[VisitorController::class,'visiting_help_category_list']);

    Route::post('delivered_at_gate/new_item/save',[DeliveredCourierController::class,'save_courier_delivered_at_gate']);
    Route::post('delivered_at_gate/courier/list',[DeliveredCourierController::class,'delivered_at_gate_courier_list']);
    Route::post('delivered_at_gate/courier/get',[DeliveredCourierController::class,'get_courier_delivered_at_gate']);
    Route::post('delivered_at_gate/courier/change_status',[DeliveredCourierController::class,'delivered_at_gate_courier_change_status']);

    Route::post('department/save',[SocietyDepartmentController::class,'save_department']);
    Route::post('department/list',[SocietyDepartmentController::class,'department_list']);
    Route::post('department/delete',[SocietyDepartmentController::class,'delete_department']);

    Route::post('duty_area/save',[StaffDutyAreaController::class,'save_duty_area']);
    Route::post('duty_area/list',[StaffDutyAreaController::class,'duty_area_list']);
    Route::post('duty_area/get',[StaffDutyAreaController::class,'get_duty_area']);
    Route::post('duty_area/delete',[StaffDutyAreaController::class,'delete_duty_area']);

    Route::post('staff_member/save',[StaffMemberController::class,'save_staff_member']);
    Route::post('staff_member/list',[StaffMemberController::class,'staff_member_list']);
    Route::post('staff_member/get',[StaffMemberController::class,'get_staff_member']);
    Route::post('staff_member/delete',[StaffMemberController::class,'delete_staff_member']);

    Route::post('staff_member/duty_area/save',[StaffMemberController::class,'save_staff_member_duty_area']);
    Route::post('staff_member/duty_area/list',[StaffMemberController::class,'staff_member_duty_area_list']);
    Route::post('staff_member/duty_area/get',[StaffMemberController::class,'get_staff_member_duty_area']);
    Route::post('staff_member/duty_area/delete',[StaffMemberController::class,'delete_staff_member_duty_area']);

    Route::post('staff_member/fill_attendance',[StaffMemberController::class,'staff_member_fill_attendance']);
    Route::post('staff_member/attendance/list',[StaffMemberController::class,'staff_member_attendance_list']);

    Route::post('designation/save',[DesignationController::class,'save_designation']);
    Route::post('designation/list',[DesignationController::class,'designation_list']);
    Route::post('designation/get',[DesignationController::class,'get_designation']);
    Route::post('designation/change_status',[DesignationController::class,'change_status']);

    Route::post('designation/authority/get',[DesignationController::class,'get_designation_authority']);
    Route::post('designation/authority/set',[DesignationController::class,'set_designation_authority']);

    Route::post('category/save',[ServiceCategoryController::class,'save_category']);
    Route::post('category/list',[ServiceCategoryController::class,'category_list']);
    Route::post('category/get',[ServiceCategoryController::class,'get_category']);
    Route::post('category/delete',[ServiceCategoryController::class,'delete_category']);

    Route::post('service_request/save',[ServiceRequestController::class,'save_service_request']);
    Route::post('service_request/list',[ServiceRequestController::class,'service_request_list']);
    Route::post('service_request/get',[ServiceRequestController::class,'get_service_request']);
    Route::post('service_request/reply/save',[ServiceRequestController::class,'save_service_request_reply']);
    Route::post('service_request/reply/list',[ServiceRequestController::class,'service_request_reply_list']);

    Route::post('payment/charge/save',[PaymentChargeController::class,'save_payment_charge']);
    Route::post('payment/charge/list',[PaymentChargeController::class,'payment_charge_list']);
    Route::post('payment/charge/get',[PaymentChargeController::class,'get_payment_charge']);
    Route::post('payment/charge/delete',[PaymentChargeController::class,'delete_payment_charge']);
    Route::post('payment/society_ledger/get',[PaymentChargeController::class,'get_payment_society_ledger']);

    Route::post('invoice/create',[InvoiceController::class,'create_invoice']);
    Route::post('invoice/list',[InvoiceController::class,'invoice_list']);
    Route::post('invoice/get',[InvoiceController::class,'get_invoice']);
    Route::post('invoice/cancel',[InvoiceController::class,'cancel_invoice']);
    Route::post('invoice/payment/pay',[InvoiceController::class,'pay_invoice_payment']);

    Route::post('loan/request/create',[LoanRequestController::class,'create_loan_request']);
    Route::post('loan/request/list',[LoanRequestController::class,'loan_request_list']);
    Route::post('loan/request/get',[LoanRequestController::class,'get_loan_request']);
    Route::post('loan/request/change_status',[LoanRequestController::class,'loan_request_change_status']);

});

Route::post('country/list', [UserController::class, 'get_country']);
Route::post('state/list', [UserController::class, 'get_state']);
Route::post('city/list', [UserController::class, 'get_city']);
