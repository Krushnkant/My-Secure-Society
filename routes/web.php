<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\admin\BlockController;
use App\Http\Controllers\admin\BusinessCategoryController;
use App\Http\Controllers\admin\CountryStateCityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\admin\FlatController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\admin\SocietyController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\admin\DailyHelpServiceController;
use App\Http\Controllers\admin\SubscriptionOrderController;
use App\Http\Controllers\admin\OrderPaymentController;
use App\Http\Controllers\admin\EmergencyContactController;
use App\Http\Controllers\admin\ServiceVendorController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Auth
Route::get('admin',[AuthController::class,'index'])->name('admin.login')->middleware('guest');
Route::post('adminpostlogin', [AuthController::class, 'postLogin'])->name('admin.postlogin');
Route::get('logout', [AuthController::class, 'logout'])->name('admin.logout');
Route::get('forgot-password', [AuthController::class,'forgot_password'])->name('admin.forgot_password')->middleware('guest');
Route::post('postforgetpassword',[AuthController::class,'postForgetpassword'])->name('admin.postforgetpassword');
Route::get('reset-password/{token}', [AuthController::class, 'reset_password'])->name('admin.reset_password');
Route::post('reset-password', [AuthController::class, 'postResetPassword'])->name('admin.postResetPassword');
Route::get('admin/403_page',[AuthController::class,'invalid_page'])->name('admin.403_page');

//Country State City
Route::post('get-states-by-country', [CountryStateCityController::class, 'getState']);
Route::post('get-cities-by-state', [CountryStateCityController::class, 'getCity']);

Route::group(['prefix'=>'admin','middleware'=>['auth','userpermission'],'as'=>'admin.'],function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');



    // Designation
    Route::get('designation',[DesignationController::class,'index'])->name('designation.list');
    Route::post('designation/listdata',[DesignationController::class,'listdata'])->name('designation.listdata');
    Route::post('designation/addorupdate',[DesignationController::class,'addorupdate'])->name('designation.addorupdate');
    Route::get('designation/{id}/edit',[DesignationController::class,'edit'])->name('designation.edit');
    Route::get('designation/{id}/delete',[DesignationController::class,'delete'])->name('designation.delete');
    Route::get('designation/changestatus/{id}',[DesignationController::class,'changestatus'])->name('designation.changestatus');
    Route::post('designation/multipledelete', [DesignationController::class,'multipledelete'])->name('designation.multipledelete');

    // Designation Permission
    Route::get('designation/{id}/permission',[DesignationController::class,'permissiondesignation'])->name('designation.permissiondesignation');
    Route::post('designation/savepermission',[DesignationController::class,'savepermission'])->name('designation.savepermission');

    // Business Category
    Route::get('businesscategory',[BusinessCategoryController::class,'index'])->name('businesscategory.list');
    Route::post('businesscategory/listdata',[BusinessCategoryController::class,'listdata'])->name('businesscategory.listdata');
    Route::post('businesscategory/addorupdate',[BusinessCategoryController::class,'addorupdate'])->name('businesscategory.addorupdate');
    Route::get('businesscategory/{id}/edit',[BusinessCategoryController::class,'edit'])->name('businesscategory.edit');
    Route::get('businesscategory/{id}/delete',[BusinessCategoryController::class,'delete'])->name('businesscategory.delete');
    Route::get('businesscategory/changestatus/{id}',[BusinessCategoryController::class,'changestatus'])->name('businesscategory.changestatus');
    Route::post('businesscategory/multipledelete', [BusinessCategoryController::class,'multipledelete'])->name('businesscategory.multipledelete');
    Route::get('businesscategory/ajaxlist/{id?}',[BusinessCategoryController::class,'ajaxlist'])->name('businesscategory.ajaxlist');

    // Users
    Route::get('users',[UserController::class,'index'])->name('users.list');
    Route::post('users/listdata',[UserController::class,'listdata'])->name('users.listdata');
    Route::post('users/addorupdate',[UserController::class,'addorupdate'])->name('users.addorupdate');
    Route::get('users/{id}/edit',[UserController::class,'edit'])->name('users.edit');
    Route::get('users/{id}/delete',[UserController::class,'delete'])->name('users.delete');
    Route::get('users/changestatus/{id}',[UserController::class,'changestatus'])->name('users.changestatus');
    Route::post('users/multipledelete', [UserController::class,'multipledelete'])->name('users.multipledelete');

    // Society
    Route::get('society',[SocietyController::class,'index'])->name('society.list');
    Route::post('society/listdata',[SocietyController::class,'listdata'])->name('society.listdata');
    Route::post('society/addorupdate',[SocietyController::class,'addorupdate'])->name('society.addorupdate');
    Route::get('society/{id}/edit',[SocietyController::class,'edit'])->name('society.edit');
    Route::get('society/{id}/delete',[SocietyController::class,'delete'])->name('society.delete');
    Route::get('society/changestatus/{id}',[SocietyController::class,'changestatus'])->name('society.changestatus');
    Route::post('society/multipledelete', [SocietyController::class,'multipledelete'])->name('society.multipledelete');

    // Society Block
    Route::get('block/{id}',[BlockController::class,'index'])->name('block.list');
    Route::post('block/listdata',[BlockController::class,'listdata'])->name('block.listdata');
    Route::post('block/addorupdate',[BlockController::class,'addorupdate'])->name('block.addorupdate');
    Route::get('block/{id}/edit',[BlockController::class,'edit'])->name('block.edit');
    Route::get('block/{id}/delete',[BlockController::class,'delete'])->name('block.delete');
    Route::get('block/changestatus/{id}',[BlockController::class,'changestatus'])->name('block.changestatus');
    Route::post('block/multipledelete', [BlockController::class,'multipledelete'])->name('block.multipledelete');

    // Block Flat
    Route::get('flat/{id}',[FlatController::class,'index'])->name('flat.list');
    Route::post('flat/listdata',[FlatController::class,'listdata'])->name('flat.listdata');
    Route::post('flat/addorupdate',[FlatController::class,'addorupdate'])->name('flat.addorupdate');
    Route::get('flat/{id}/edit',[FlatController::class,'edit'])->name('flat.edit');
    Route::get('flat/{id}/delete',[FlatController::class,'delete'])->name('flat.delete');
    Route::get('flat/changestatus/{id}',[FlatController::class,'changestatus'])->name('flat.changestatus');
    Route::post('flat/multipledelete', [FlatController::class,'multipledelete'])->name('flat.multipledelete');

     // Designation Permission
     Route::get('company/profile',[CompanyProfileController::class,'profile'])->name('company.profile');
     Route::post('company/profile/update',[CompanyProfileController::class,'update'])->name('company.profile.update');


     // User Profile
     Route::get('user/profile',[AuthController::class,'profile'])->name('user.profile');
     Route::post('user/profile/update',[AuthController::class,'updateprofile'])->name('user.profile.update');
     Route::post('user/password/change',[AuthController::class,'changepassword'])->name('user.password.change');

     // Subscription Order
    Route::get('subscriptionorder',[SubscriptionOrderController::class,'index'])->name('subscriptionorder.list');
    Route::post('subscriptionorder/listdata',[SubscriptionOrderController::class,'listdata'])->name('subscriptionorder.listdata');
    Route::post('subscriptionorder/addorupdate',[SubscriptionOrderController::class,'addorupdate'])->name('subscriptionorder.addorupdate');
    Route::get('subscriptionorder/{id}/edit',[SubscriptionOrderController::class,'edit'])->name('subscriptionorder.edit');
    Route::get('subscriptionorder/{id}/delete',[SubscriptionOrderController::class,'delete'])->name('subscriptionorder.delete');
    Route::get('subscriptionorder/changestatus/{id}',[SubscriptionOrderController::class,'changestatus'])->name('subscriptionorder.changestatus');
    Route::post('subscriptionorder/multipledelete', [SubscriptionOrderController::class,'multipledelete'])->name('subscriptionorder.multipledelete');

    // Order Payment
    Route::get('orderpayment/{id}',[OrderPaymentController::class,'index'])->name('orderpayment.list');
    Route::post('orderpayment/listdata',[OrderPaymentController::class,'listdata'])->name('orderpayment.listdata');
    Route::post('orderpayment/addorupdate',[OrderPaymentController::class,'addorupdate'])->name('orderpayment.addorupdate');
    Route::get('orderpayment/{id}/edit',[OrderPaymentController::class,'edit'])->name('orderpayment.edit');
    Route::get('orderpayment/{id}/delete',[OrderPaymentController::class,'delete'])->name('orderpayment.delete');
    Route::get('orderpayment/changestatus/{id}',[OrderPaymentController::class,'changestatus'])->name('orderpayment.changestatus');
    Route::post('orderpayment/multipledelete', [OrderPaymentController::class,'multipledelete'])->name('orderpayment.multipledelete');

    // Emergency Contact
    Route::get('emergencycontact',[EmergencyContactController::class,'index'])->name('emergencycontact.list');
    Route::post('emergencycontact/listdata',[EmergencyContactController::class,'listdata'])->name('emergencycontact.listdata');
    Route::post('emergencycontact/addorupdate',[EmergencyContactController::class,'addorupdate'])->name('emergencycontact.addorupdate');
    Route::get('emergencycontact/{id}/edit',[EmergencyContactController::class,'edit'])->name('emergencycontact.edit');
    Route::get('emergencycontact/{id}/delete',[EmergencyContactController::class,'delete'])->name('emergencycontact.delete');
    Route::get('emergencycontact/changestatus/{id}',[EmergencyContactController::class,'changestatus'])->name('emergencycontact.changestatus');
    Route::post('emergencycontact/multipledelete', [EmergencyContactController::class,'multipledelete'])->name('emergencycontact.multipledelete');

     // service vendor
    Route::get('servicevendor',[ServiceVendorController::class,'index'])->name('servicevendor.list');
    Route::post('servicevendor/listdata',[ServiceVendorController::class,'listdata'])->name('servicevendor.listdata');
    Route::post('servicevendor/addorupdate',[ServiceVendorController::class,'addorupdate'])->name('servicevendor.addorupdate');
    Route::get('servicevendor/{id}/edit',[ServiceVendorController::class,'edit'])->name('servicevendor.edit');
    Route::get('servicevendor/{id}/delete',[ServiceVendorController::class,'delete'])->name('servicevendor.delete');
    Route::get('servicevendor/changestatus/{id}',[ServiceVendorController::class,'changestatus'])->name('servicevendor.changestatus');
    Route::post('servicevendor/multipledelete', [ServiceVendorController::class,'multipledelete'])->name('servicevendor.multipledelete');

     // Daily Help Service
     Route::get('dailyhelpservice',[DailyHelpServiceController::class,'index'])->name('dailyhelpservice.list');
     Route::post('dailyhelpservice/listdata',[DailyHelpServiceController::class,'listdata'])->name('dailyhelpservice.listdata');
     Route::post('dailyhelpservice/addorupdate',[DailyHelpServiceController::class,'addorupdate'])->name('dailyhelpservice.addorupdate');
     Route::get('dailyhelpservice/{id}/edit',[DailyHelpServiceController::class,'edit'])->name('dailyhelpservice.edit');
     Route::get('dailyhelpservice/{id}/delete',[DailyHelpServiceController::class,'delete'])->name('dailyhelpservice.delete');
     Route::get('dailyhelpservice/changestatus/{id}',[DailyHelpServiceController::class,'changestatus'])->name('dailyhelpservice.changestatus');
     Route::post('dailyhelpservice/multipledelete', [DailyHelpServiceController::class,'multipledelete'])->name('dailyhelpservice.multipledelete');


});
