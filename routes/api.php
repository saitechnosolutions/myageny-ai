<?php
// ================================================================
// FILE: routes/api.php
// Complete REST API for myAgenci.ai Mobile App
// Uses Laravel Sanctum for token-based authentication
// ================================================================


use App\Http\Controllers\SuperAdminDashboardController;
use Illuminate\Support\Facades\Route;


Route::get('dashboard/admin', [SuperAdminDashboardController::class, 'index'])
    ->name('dashboard.admin');