<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Landing Page / Login
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Registration
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// Forgot Password
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/reports/laba-rugi', [ReportController::class, 'cetakLabaRugi'])->name('reports.laba-rugi');
    Route::get('/reports/posisi-keuangan', [ReportController::class, 'cetakPosisiKeuangan'])->name('reports.posisi-keuangan');
    Route::get('/reports/arus-kas', [ReportController::class, 'cetakArusKas'])->name('reports.arus-kas');
    Route::get('/reports/perubahan-ekuitas', [ReportController::class, 'cetakPerubahanEkuitas'])->name('reports.perubahan-ekuitas');
    Route::get('/reports/neraca-saldo', [ReportController::class, 'cetakNeracaSaldo'])->name('reports.neraca-saldo');

    // Invoices
    Route::get('/invoices/{invoice}/print', [\App\Http\Controllers\InvoiceController::class, 'print'])->name('invoices.print');
});
