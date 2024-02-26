<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\Auth\LoginTokenController;

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

// Define Custom User Registration & Login Routes
Route::group([], function() {
    Route::get('/register', [LoginRegisterController::class, 'register'])->name('register');
    Route::post('/store', [LoginRegisterController::class, 'store'])->name('store');
    Route::get('/login', [LoginRegisterController::class, 'login'])->name('login');
    Route::post('/authenticate', [LoginRegisterController::class, 'authenticate'])->name('authenticate');
    Route::get('/home', [LoginRegisterController::class, 'home'])->name('home');
    Route::post('/logout', [LoginRegisterController::class, 'logout'])->name('logout');
    Route::post('/login/token', [LoginTokenController::class, 'create'])->name('login.token.create');
    Route::get('/login/token/{token}', [LoginTokenController::class, 'verify'])->name('login.token.verify');
    Route::get('/token/verify', [LoginRegisterController::class, 'verifyToken'])->name('token.verify');
});

// Define Custom Verification Routes
Route::group([], function() {
    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
});