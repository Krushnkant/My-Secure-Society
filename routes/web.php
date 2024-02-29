<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
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

Route::get('admin',[AuthController::class,'index'])->name('admin.login');
Route::post('adminpostlogin', [AuthController::class, 'postLogin'])->name('admin.postlogin');
Route::get('logout', [AuthController::class, 'logout'])->name('admin.logout');
Route::get('admin/403_page',[AuthController::class,'invalid_page'])->name('admin.403_page');

Route::group(['prefix'=>'admin','middleware'=>['auth'],'as'=>'admin.'],function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('products', [ProductController::class, 'index'])->name('products');
    Route::post('/get-products', [ProductController::class, 'getProducts'])->name('products.getProducts');
});