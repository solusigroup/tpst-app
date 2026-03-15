<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Central\CentralTenantController;
use App\Http\Controllers\Central\CentralUserController;
use App\Http\Controllers\Central\CentralDashboardController;

/*
|--------------------------------------------------------------------------
| Central / Super Admin Routes
|--------------------------------------------------------------------------
|
| Access is restricted to users with is_super_admin = true
|
*/

Route::middleware(['auth', 'superadmin'])->prefix('central')->name('central.')->group(function () {
    
    // Central Dashboard
    Route::get('/', [CentralDashboardController::class, 'index'])->name('dashboard');

    // Tenant Management
    Route::resource('tenants', CentralTenantController::class);

    // User Management (All Users accross Tenants)
    Route::resource('users', CentralUserController::class);

});
