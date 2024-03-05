<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StallController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ParkingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [HomeController::class, 'index'])->middleware('auth')->name('home');
Route::get('/user/stalls', 'UserController@mystall')->name('tenant.stall')->middleware('auth');

// Route::get('/user/dashboard', [UserController::class, 'index'])->middleware('auth')->name('user.index');
// Route::get('/tenant/apply', [TenantController::class, 'create'])->middleware('auth')->name('tenant.create');
// Route::post('/tenants/store/{stallId}', [TenantController::class, 'store'])->name('tenant.store');

Route::middleware(['auth', 'admin'])->group(function () {
    //Route::get('/analytics', [AdminController::class, 'index'])->name('admin.analytics');
    // OKAY NA RIN TO
    Route::get('/stall', [StallController::class, 'index'])->name('stall.index');
    Route::get('/stall/create', [StallController::class, 'create'])->name('stall.create');
    Route::post('/stall', [StallController::class, 'store'])->name('stall.store');
    Route::delete('/stall/{id}/delete', [StallController::class, 'destroy'])->name('stall.delete');
    Route::get('/stall/{id}/edit', [StallController::class, 'edit'])->name('stall.edit');
    Route::put('/stall/{id}/update', [StallController::class, 'update'])->name('stall.update');



    Route::get('/tenant', [TenantController::class, 'index'])->name('tenant.index');
    Route::get('/tenant/{id}/edit', 'TenantController@edit')->name('tenant.edit');
    Route::put('/tenant/{id}', 'TenantController@update')->name('tenant.update');



    //OK NA TO
    Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
    Route::get('/payment/create', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('/payment/store', [PaymentController::class, 'store'])->name('payment.store');
    Route::get('/payment/{id}/edit', [PaymentController::class, 'edit'])->name('payment.edit');
    Route::put('/payment/{payment}', [PaymentController::class, 'update'])->name('payment.update');



    //FOR PARKING
    Route::get('/parking', [ParkingController::class, 'index'])->name('parking.index');
    Route::get('/clear', [ParkingController::class, 'clear'])->name('parking.clear');
    Route::post('/parking', [ParkingController::class, 'store'])->name('parking.store');
    Route::put('/parkings/{id}', [ParkingController::class, 'update'])->name('parking.update');
    Route::get('/parking/create', [ParkingController::class, 'create'])->name('parking.create');
    Route::delete('/parking/deleteSelected', [ParkingController::class, 'deleteSelected'])->name('parking.deleteSelected');
});

Route::middleware('auth')->group(function () {
    //Route::get('/mystall', [UserController::class, 'mystall'])->name('tenant.stall');
    Route::get('/user', [UserController::class, 'indexFilter'])->name('user.filter');
    Route::get('/user/dashboard', [UserController::class, 'index'])->name('user.index');
    Route::get('/tenant/apply', [TenantController::class, 'create'])->name('tenant.create');
    Route::post('/tenants/store/{stallId}', [TenantController::class, 'store'])->name('tenant.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
