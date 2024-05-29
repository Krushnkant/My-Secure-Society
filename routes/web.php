<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BlockController;
use App\Http\Controllers\Admin\BusinessCategoryController;
use App\Http\Controllers\Admin\CountryStateCityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\FlatController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SocietyController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\Admin\DailyHelpServiceController;
use App\Http\Controllers\Admin\SubscriptionOrderController;
use App\Http\Controllers\Admin\OrderPaymentController;
use App\Http\Controllers\Admin\EmergencyContactController;
use App\Http\Controllers\Admin\ServiceVendorController;
use App\Http\Controllers\Admin\SocietyMemberController;
use App\Http\Controllers\Admin\VisitingHelpCategoryController;

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
    Route::post('designation/add',[DesignationController::class,'addorupdate'])->name('designation.add');
    Route::post('designation/update',[DesignationController::class,'addorupdate'])->name('designation.update');
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
    Route::post('businesscategory/add',[BusinessCategoryController::class,'addorupdate'])->name('businesscategory.add');
    Route::post('businesscategory/update',[BusinessCategoryController::class,'addorupdate'])->name('businesscategory.update');
    Route::get('businesscategory/{id}/edit',[BusinessCategoryController::class,'edit'])->name('businesscategory.edit');
    Route::get('businesscategory/{id}/delete',[BusinessCategoryController::class,'delete'])->name('businesscategory.delete');
    Route::get('businesscategory/changestatus/{id}',[BusinessCategoryController::class,'changestatus'])->name('businesscategory.changestatus');
    Route::post('businesscategory/multipledelete', [BusinessCategoryController::class,'multipledelete'])->name('businesscategory.multipledelete');
    Route::get('businesscategory/ajaxlist/{id?}',[BusinessCategoryController::class,'ajaxlist'])->name('businesscategory.ajaxlist');

    // Users
    Route::get('users',[UserController::class,'index'])->name('users.list');
    Route::post('users/listdata',[UserController::class,'listdata'])->name('users.listdata');
    Route::post('users/add',[UserController::class,'addorupdate'])->name('users.add');
    Route::post('users/update',[UserController::class,'addorupdate'])->name('users.update');
    Route::get('users/{id}/edit',[UserController::class,'edit'])->name('users.edit');
    Route::get('users/{id}/delete',[UserController::class,'delete'])->name('users.delete');
    Route::get('users/changestatus/{id}',[UserController::class,'changestatus'])->name('users.changestatus');
    Route::post('users/multipledelete', [UserController::class,'multipledelete'])->name('users.multipledelete');

    // Society
    Route::get('society',[SocietyController::class,'index'])->name('society.list');
    Route::post('society/listdata',[SocietyController::class,'listdata'])->name('society.listdata');
    Route::post('society/add',[SocietyController::class,'addorupdate'])->name('society.add');
    Route::post('society/update',[SocietyController::class,'addorupdate'])->name('society.update');
    Route::get('society/{id}/edit',[SocietyController::class,'edit'])->name('society.edit');
    Route::get('society/{id}/delete',[SocietyController::class,'delete'])->name('society.delete');
    Route::get('society/changestatus/{id}',[SocietyController::class,'changestatus'])->name('society.changestatus');
    Route::post('society/multipledelete', [SocietyController::class,'multipledelete'])->name('society.multipledelete');

    // Society Block
    Route::get('block/{id}',[BlockController::class,'index'])->name('block.list');
    Route::post('block/listdata',[BlockController::class,'listdata'])->name('block.listdata');
    Route::post('block/add',[BlockController::class,'addorupdate'])->name('block.add');
    Route::post('block/update',[BlockController::class,'addorupdate'])->name('block.update');
    Route::get('block/{id}/edit',[BlockController::class,'edit'])->name('block.edit');
    Route::get('block/{id}/delete',[BlockController::class,'delete'])->name('block.delete');
    Route::get('block/changestatus/{id}',[BlockController::class,'changestatus'])->name('block.changestatus');
    Route::post('block/multipledelete', [BlockController::class,'multipledelete'])->name('block.multipledelete');

    // Block Flat
    Route::get('flat/{id}',[FlatController::class,'index'])->name('flat.list');
    Route::post('flat/listdata',[FlatController::class,'listdata'])->name('flat.listdata');
    Route::post('flat/add',[FlatController::class,'addorupdate'])->name('flat.add');
    Route::post('flat/update',[FlatController::class,'addorupdate'])->name('flat.update');
    Route::get('flat/{id}/edit',[FlatController::class,'edit'])->name('flat.edit');
    Route::get('flat/{id}/delete',[FlatController::class,'delete'])->name('flat.delete');
    Route::get('flat/changestatus/{id}',[FlatController::class,'changestatus'])->name('flat.changestatus');
    Route::post('flat/multipledelete', [FlatController::class,'multipledelete'])->name('flat.multipledelete');

    // Society Member
    Route::get('societymember/{id}',[SocietyMemberController::class,'index'])->name('societymember.list');
    Route::post('societymember/listdata',[SocietyMemberController::class,'listdata'])->name('societymember.listdata');
    Route::post('societymember/add',[SocietyMemberController::class,'addorupdate'])->name('societymember.add');
    Route::post('societymember/update',[SocietyMemberController::class,'addorupdate'])->name('societymember.update');
    Route::get('societymember/{id}/edit',[SocietyMemberController::class,'edit'])->name('societymember.edit');
    Route::get('societymember/{id}/delete',[SocietyMemberController::class,'delete'])->name('societymember.delete');
    Route::get('societymember/changestatus/{id}',[SocietyMemberController::class,'changestatus'])->name('societymember.changestatus');
    Route::post('societymember/multipledelete', [SocietyMemberController::class,'multipledelete'])->name('societymember.multipledelete');
    Route::post('get-flat-by-block', [SocietyMemberController::class, 'getFlat']);

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
    Route::post('subscriptionorder/add',[SubscriptionOrderController::class,'addorupdate'])->name('subscriptionorder.add');
    Route::post('subscriptionorder/update',[SubscriptionOrderController::class,'addorupdate'])->name('subscriptionorder.update');
    Route::get('subscriptionorder/{id}/edit',[SubscriptionOrderController::class,'edit'])->name('subscriptionorder.edit');
    Route::get('subscriptionorder/{id}/delete',[SubscriptionOrderController::class,'delete'])->name('subscriptionorder.delete');
    Route::get('subscriptionorder/changestatus/{id}',[SubscriptionOrderController::class,'changestatus'])->name('subscriptionorder.changestatus');
    Route::post('subscriptionorder/multipledelete', [SubscriptionOrderController::class,'multipledelete'])->name('subscriptionorder.multipledelete');

    // Order Payment
    Route::get('orderpayment/{id}',[OrderPaymentController::class,'index'])->name('orderpayment.list');
    Route::post('orderpayment/listdata',[OrderPaymentController::class,'listdata'])->name('orderpayment.listdata');
    Route::post('orderpayment/add',[OrderPaymentController::class,'addorupdate'])->name('orderpayment.add');
    Route::post('orderpayment/update',[OrderPaymentController::class,'addorupdate'])->name('orderpayment.update');
    Route::get('orderpayment/{id}/edit',[OrderPaymentController::class,'edit'])->name('orderpayment.edit');
    Route::get('orderpayment/{id}/delete',[OrderPaymentController::class,'delete'])->name('orderpayment.delete');
    Route::get('orderpayment/changestatus/{id}',[OrderPaymentController::class,'changestatus'])->name('orderpayment.changestatus');
    Route::post('orderpayment/multipledelete', [OrderPaymentController::class,'multipledelete'])->name('orderpayment.multipledelete');

    // Emergency Contact
    Route::get('emergencycontact',[EmergencyContactController::class,'index'])->name('emergencycontact.list');
    Route::post('emergencycontact/listdata',[EmergencyContactController::class,'listdata'])->name('emergencycontact.listdata');
    Route::post('emergencycontact/add',[EmergencyContactController::class,'addorupdate'])->name('emergencycontact.add');
    Route::post('emergencycontact/update',[EmergencyContactController::class,'addorupdate'])->name('emergencycontact.update');
    Route::get('emergencycontact/{id}/edit',[EmergencyContactController::class,'edit'])->name('emergencycontact.edit');
    Route::get('emergencycontact/{id}/delete',[EmergencyContactController::class,'delete'])->name('emergencycontact.delete');
    Route::get('emergencycontact/changestatus/{id}',[EmergencyContactController::class,'changestatus'])->name('emergencycontact.changestatus');
    Route::post('emergencycontact/multipledelete', [EmergencyContactController::class,'multipledelete'])->name('emergencycontact.multipledelete');

     // service vendor
    Route::get('servicevendor',[ServiceVendorController::class,'index'])->name('servicevendor.list');
    Route::post('servicevendor/listdata',[ServiceVendorController::class,'listdata'])->name('servicevendor.listdata');
    Route::post('servicevendor/add',[ServiceVendorController::class,'addorupdate'])->name('servicevendor.add');
    Route::post('servicevendor/update',[ServiceVendorController::class,'addorupdate'])->name('servicevendor.update');
    Route::get('servicevendor/{id}/edit',[ServiceVendorController::class,'edit'])->name('servicevendor.edit');
    Route::get('servicevendor/{id}/delete',[ServiceVendorController::class,'delete'])->name('servicevendor.delete');
    Route::get('servicevendor/changestatus/{id}',[ServiceVendorController::class,'changestatus'])->name('servicevendor.changestatus');
    Route::post('servicevendor/multipledelete', [ServiceVendorController::class,'multipledelete'])->name('servicevendor.multipledelete');

     // Daily Help Service
     Route::get('dailyhelpservice',[DailyHelpServiceController::class,'index'])->name('dailyhelpservice.list');
     Route::post('dailyhelpservice/listdata',[DailyHelpServiceController::class,'listdata'])->name('dailyhelpservice.listdata');
     Route::post('dailyhelpservice/add',[DailyHelpServiceController::class,'addorupdate'])->name('dailyhelpservice.add');
     Route::post('dailyhelpservice/update',[DailyHelpServiceController::class,'addorupdate'])->name('dailyhelpservice.update');
     Route::get('dailyhelpservice/{id}/edit',[DailyHelpServiceController::class,'edit'])->name('dailyhelpservice.edit');
     Route::get('dailyhelpservice/{id}/delete',[DailyHelpServiceController::class,'delete'])->name('dailyhelpservice.delete');
     Route::get('dailyhelpservice/changestatus/{id}',[DailyHelpServiceController::class,'changestatus'])->name('dailyhelpservice.changestatus');
     Route::post('dailyhelpservice/multipledelete', [DailyHelpServiceController::class,'multipledelete'])->name('dailyhelpservice.multipledelete');

       // Visiting Help Category
    Route::get('visitinghelpcategory',[VisitingHelpCategoryController::class,'index'])->name('visitinghelpcategory.list');
    Route::post('visitinghelpcategory/listdata',[VisitingHelpCategoryController::class,'listdata'])->name('visitinghelpcategory.listdata');
    Route::post('visitinghelpcategory/add',[VisitingHelpCategoryController::class,'addorupdate'])->name('visitinghelpcategory.add');
    Route::post('visitinghelpcategory/update',[VisitingHelpCategoryController::class,'addorupdate'])->name('visitinghelpcategory.update');
    Route::get('visitinghelpcategory/{id}/edit',[VisitingHelpCategoryController::class,'edit'])->name('visitinghelpcategory.edit');
    Route::get('visitinghelpcategory/{id}/delete',[VisitingHelpCategoryController::class,'delete'])->name('visitinghelpcategory.delete');
    Route::post('visitinghelpcategory/multipledelete', [VisitingHelpCategoryController::class,'multipledelete'])->name('visitinghelpcategory.multipledelete');


});
