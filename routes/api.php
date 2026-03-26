<?php

use App\Http\Controllers\App\AuthController as MobileAuthController;
use App\Http\Controllers\App\DashboardController as MobileDashboardController;
use App\Http\Controllers\App\LeadController as MobileLeadController;
use App\Http\Controllers\App\LeadShowController as MobileLeadShowController;
use App\Http\Controllers\SuperAdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('dashboard/admin', [SuperAdminDashboardController::class, 'index'])
    ->name('dashboard.admin');

/*
|--------------------------------------------------------------------------
| Mobile Auth Routes
|--------------------------------------------------------------------------
*/
Route::prefix('mobile/auth')->group(function () {
    Route::post('login',    [MobileAuthController::class, 'login']);
    Route::post('register', [MobileAuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [MobileAuthController::class, 'logout']);
        Route::get('me',      [MobileAuthController::class, 'me']);
    });
});

/*
|--------------------------------------------------------------------------
| Mobile Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->prefix('mobile')->name('mobile.')->group(function () {
    Route::get('dashboard', [MobileDashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Mobile Leads Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->prefix('mobile/leads')->name('mobile.leads.')->group(function () {

    // NOTE: 'meta' must be defined BEFORE the {lead} wildcard route
    // to avoid Laravel trying to resolve "meta" as a Lead model ID.
    Route::get('meta', [MobileLeadController::class, 'meta'])->name('meta');

    // CRUD
    Route::get('/',                [MobileLeadController::class, 'index'])->name('index');
    Route::post('/',               [MobileLeadController::class, 'store'])->name('store');
    Route::get('/{lead}',          [MobileLeadController::class, 'show'])->name('show');
    Route::put('/{lead}',          [MobileLeadController::class, 'update'])->name('update');
    Route::delete('/{lead}',       [MobileLeadController::class, 'destroy'])->name('destroy');
    Route::patch('/{lead}/status', [MobileLeadController::class, 'updateStatus'])->name('update-status');

    // ── Call Updates ────────────────────────────────────────────────
    Route::post('/{lead}/calls',          [MobileLeadShowController::class, 'storeCall'])->name('calls.store');
    Route::delete('/{lead}/calls/{call}', [MobileLeadShowController::class, 'destroyCall'])->name('calls.destroy');

    // ── Reminders ───────────────────────────────────────────────────
    Route::post('/{lead}/reminders',                      [MobileLeadShowController::class, 'storeReminder'])->name('reminders.store');
    Route::patch('/{lead}/reminders/{reminder}/complete', [MobileLeadShowController::class, 'completeReminder'])->name('reminders.complete');
    Route::delete('/{lead}/reminders/{reminder}',         [MobileLeadShowController::class, 'destroyReminder'])->name('reminders.destroy');

    // ── Lead Products ───────────────────────────────────────────────
    Route::post('/{lead}/products',                   [MobileLeadShowController::class, 'storeProduct'])->name('products.store');
    Route::put('/{lead}/products/{product}',          [MobileLeadShowController::class, 'updateProduct'])->name('products.update');
    Route::patch('/{lead}/products/{product}/status', [MobileLeadShowController::class, 'updateProductStatus'])->name('products.status');
    Route::delete('/{lead}/products/{product}',       [MobileLeadShowController::class, 'destroyProduct'])->name('products.destroy');

    // ── Product Payments ────────────────────────────────────────────
    Route::post('/{lead}/products/{product}/payments',              [MobileLeadShowController::class, 'storeProductPayment'])->name('products.payments.store');
    Route::delete('/{lead}/products/{product}/payments/{payment}',  [MobileLeadShowController::class, 'destroyProductPayment'])->name('products.payments.destroy');

    // ── Quotations ──────────────────────────────────────────────────
    Route::post('/{lead}/quotations',                     [MobileLeadShowController::class, 'storeQuotation'])->name('quotations.store');
    Route::patch('/{lead}/quotations/{quotation}/status', [MobileLeadShowController::class, 'updateQuotationStatus'])->name('quotations.status');
    Route::delete('/{lead}/quotations/{quotation}',       [MobileLeadShowController::class, 'destroyQuotation'])->name('quotations.destroy');
});