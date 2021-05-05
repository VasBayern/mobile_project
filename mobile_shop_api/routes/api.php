<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RamController;
use App\Http\Controllers\RomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/** Authentication */
Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);

/** Forgot password */
Route::get('password/forgot', [PasswordController::class, 'forgot']);
Route::post('password/reset', [PasswordController::class, 'reset']);
Route::get('password/token', [PasswordController::class, 'getToken'])->name('password.reset');

Route::group(['middleware' => 'auth:sanctum'], function () {
    /** User account */
    Route::get('user', [UserController::class, 'getUser']);
    Route::put('user/update', [UserController::class, 'update']);
    Route::delete('user/destroy/{id}', [UserController::class, 'destroy']);
    
    /** Logout */
    Route::post('logout', [LoginController::class, 'logout']);

    /** Email verification */
    Route::get('email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::get('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

    /** Change password */
    Route::patch('password/change', [PasswordController::class, 'change']);
});

/** Category */
Route::apiResource('categories', CategoryController::class);
Route::get('categories/excel/export', [CategoryController::class, 'export']);

/** Brand */
Route::apiResource('brands', BrandController::class);
Route::get('brands/excel/export', [BrandController::class, 'export']);

/** Ram */
Route::apiResource('rams', RamController::class);
Route::get('rams/excel/export', [RamController::class, 'export']);

/** Rom */
Route::apiResource('roms', RomController::class);
Route::get('roms/excel/export', [RomController::class, 'export']);

/** Color */
Route::apiResource('colors', ColorController::class);
Route::get('colors/excel/export', [ColorController::class, 'export']);

/** Product */
Route::resource('products', ProductController::class);
Route::get('products/excel/export', [ProductController::class, 'export']);