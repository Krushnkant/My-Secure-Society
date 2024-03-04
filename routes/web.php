<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\admin\BussinessCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\ProductController;

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

Route::group(['prefix'=>'admin','middleware'=>['auth'],'as'=>'admin.'],function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('products', [ProductController::class, 'index'])->name('products');
    Route::post('/get-products', [ProductController::class, 'getProducts'])->name('products.getProducts');


    Route::get('bussiness_category',[BussinessCategoryController::class,'index'])->name('bussiness_category.list');
    Route::get('bussiness_category/create',[BussinessCategoryController::class,'create'])->name('bussiness_category.add');
    Route::post('bussiness_category/save',[BussinessCategoryController::class,'save'])->name('bussiness_category.save');
    Route::post('allbussinesscategorylist',[BussinessCategoryController::class,'allbussinesscategorylist'])->name('allbussinesscategorylist');


    Route::get('designation',[DesignationController::class,'index'])->name('designation.list');
    Route::post('alldesignationlist',[DesignationController::class,'alldesignationlist'])->name('designation.alldesignationlist');
    Route::post('addorupdatedesignation',[DesignationController::class,'addorupdatedesignation'])->name('designation.addorupdatedesignation');
    Route::get('designation/{id}/edit',[DesignationController::class,'editdesignation'])->name('designation.edit');
    Route::get('designation/{id}/delete',[DesignationController::class,'deletedesignation'])->name('designation.delete');
    Route::get('changedesignationstatus/{id}',[DesignationController::class,'changedesignationstatus'])->name('designation.changedesignationstatus');
   

});