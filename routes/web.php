<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\admin\BusinessCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;

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

Route::get('admin',[AuthController::class,'index'])->name('admin.login')->middleware('guest');
Route::post('adminpostlogin', [AuthController::class, 'postLogin'])->name('admin.postlogin');
Route::get('logout', [AuthController::class, 'logout'])->name('admin.logout');
Route::get('forgot-password', [AuthController::class,'forgot_password'])->name('admin.forgot_password')->middleware('guest');
Route::post('postforgetpassword',[AuthController::class,'postForgetpassword'])->name('admin.postforgetpassword');
Route::get('reset-password/{token}', [AuthController::class, 'reset_password'])->name('admin.reset_password');
Route::post('reset-password', [AuthController::class, 'postResetPassword'])->name('admin.postResetPassword');
Route::get('admin/403_page',[AuthController::class,'invalid_page'])->name('admin.403_page');

Route::group(['prefix'=>'admin','middleware'=>['auth','userpermission'],'as'=>'admin.'],function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('products', [ProductController::class, 'index'])->name('products');
    Route::post('/get-products', [ProductController::class, 'getProducts'])->name('products.getProducts');

    // Business Category
    Route::get('businesscategory',[BusinessCategoryController::class,'index'])->name('businesscategory.list');
    Route::post('businesscategory/listdata',[BusinessCategoryController::class,'listdata'])->name('businesscategory.listdata');
    Route::post('businesscategory/addorupdate',[BusinessCategoryController::class,'addorupdate'])->name('businesscategory.addorupdate');
    Route::get('businesscategory/{id}/edit',[BusinessCategoryController::class,'edit'])->name('businesscategory.edit');
    Route::get('businesscategory/{id}/delete',[BusinessCategoryController::class,'delete'])->name('businesscategory.delete');
    Route::get('businesscategory/changestatus/{id}',[BusinessCategoryController::class,'changestatus'])->name('businesscategory.changestatus');
    Route::post('businesscategory/multipledelete', [BusinessCategoryController::class,'multipledelete'])->name('businesscategory.multipledelete');
    
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

    // Users
    Route::get('users',[UserController::class,'index'])->name('users.list');
    Route::post('users/listdata',[UserController::class,'listdata'])->name('users.listdata');
    Route::post('users/addorupdate',[UserController::class,'addorupdate'])->name('users.addorupdate');
    Route::get('users/{id}/edit',[UserController::class,'edit'])->name('users.edit');
    Route::get('users/{id}/delete',[UserController::class,'delete'])->name('users.delete');
    Route::get('users/changestatus/{id}',[UserController::class,'changestatus'])->name('users.changestatus');
    Route::post('users/multipledelete', [UserController::class,'multipledelete'])->name('users.multipledelete');

});