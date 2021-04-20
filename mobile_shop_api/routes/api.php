<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Export\CategoryExportController;
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

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);

Route::get('password/forgot', [PasswordController::class, 'forgot']);
Route::post('password/reset', [PasswordController::class, 'reset']);
Route::get('password/token', [PasswordController::class, 'getToken'])->name('password.reset');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('user', [UserController::class, 'getUser']);
    Route::put('user/update', [UserController::class, 'update']);
    Route::post('logout', [LoginController::class, 'logout']);

    Route::get('email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::get('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

    Route::patch('password/change', [PasswordController::class, 'change']);
});

Route::apiResource('categories', CategoryController::class);
Route::get('categories/excel/export', [CategoryExportController::class, 'export']);