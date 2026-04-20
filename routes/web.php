<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadShowController;
use App\Http\Controllers\LeadStatusController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\LeadSourceController;
use App\Http\Controllers\OutcomeCategoryController;
use App\Http\Controllers\OutcomeSubCategoryController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MagiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::get('/quotations/{id}/pdf',  [QuotationController::class, 'downloadPdf'])->name('quotation.pdf');

Route::get('/magi', function () {
    return view('pages.magi.index');
})->middleware('auth');

Route::get('/db/tables',       [MagiController::class, 'listTables']);
Route::post('/db/fetch-table', [MagiController::class, 'fetchTable']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Forgot password placeholder
Route::get('/forgot-password', function () {
    return redirect()->route('login')->with('error', 'Password reset is coming soon. Please contact your administrator.');
})->name('password.request');


Route::get('/lead/form-customization', fn() => view('pages.field_customization.index'))->middleware('auth');
Route::middleware(['auth'])->group(function () {

  // Dashboard
    Route::get('/', fn() => redirect()->route('dashboard'));
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

     // Super Admin Dashboard (API-integrated blade)
    Route::get('/dashboard/admin', [DashboardController::class, 'index'])
        ->name('dashboard.admin');

    Route::get('/product-dashboard/admin', [SuperAdminDashboardController::class, 'adminProductindex']);

    // Default redirect by role
    Route::get('/dashboard', function () {

        if (auth()->user()->hasAnyRole(['Super Admin', 'admin'])) {
            return redirect()->route('dashboard.admin');
        }
        return redirect()->route('leads.index');
    })->name('dashboard');


    Route::prefix('users')->name('users.')->middleware(['auth'])->group(function () {

    // List, Create, Store, Show, Edit, Update, Delete
    Route::get('/',              [UserController::class, 'index'])->name('index');
    Route::get('/create',        [UserController::class, 'create'])->name('create');
    Route::post('/',             [UserController::class, 'store'])->name('store');
    Route::get('/{user}',        [UserController::class, 'show'])->name('show');
    Route::get('/{user}/edit',   [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}',        [UserController::class, 'update']) ->name('update');
    Route::delete('/{user}',     [UserController::class, 'destroy'])->name('destroy');

    // Extra actions
    Route::patch('/{user}/toggle-status',  [UserController::class, 'toggleStatus'])->name('toggle-status');
    Route::post('/{user}/reset-password',  [UserController::class, 'resetPassword'])->name('reset-password');
});

    // ── Main Lead CRUD ─────────────────────────────────────────
   Route::prefix('leads')->name('leads.')->group(function () {

        // ── Core CRUD ─────────────────────────────────────────
        Route::get('/',            [LeadController::class, 'index'])->name('index');
        Route::get('/create',      [LeadController::class, 'create'])->name('create');
        Route::post('/',           [LeadController::class, 'store'])->name('store');
        Route::get('/{lead}',      [LeadController::class, 'show'])->name('show');
        Route::get('/{lead}/edit', [LeadController::class, 'edit'])->name('edit');
        Route::put('/{lead}',      [LeadController::class, 'update'])->name('update');
        Route::delete('/{lead}',   [LeadController::class, 'destroy'])->name('destroy');
        Route::patch('/{lead}/status', [LeadController::class, 'updateStatus'])->name('update-status');

        // ── Call Updates ──────────────────────────────────────
        Route::post('/{lead}/calls',             [LeadShowController::class, 'storeCall'])->name('calls.store');
        Route::delete('/{lead}/calls/{call}',    [LeadShowController::class, 'destroyCall'])->name('calls.destroy');

        // ── Reminders ─────────────────────────────────────────
        Route::post('/{lead}/reminders',                  [LeadShowController::class, 'storeReminder'])->name('reminders.store');
        Route::patch('/{lead}/reminders/{reminder}/done', [LeadShowController::class, 'completeReminder'])->name('reminders.complete');
        Route::delete('/{lead}/reminders/{reminder}',     [LeadShowController::class, 'destroyReminder'])->name('reminders.destroy');

        // ── Products ───────────────────────────────────────────
        Route::post('/{lead}/products',
            [LeadShowController::class, 'storeProduct'])->name('products.store');

        Route::patch('/{lead}/products/{product}/status',
            [LeadShowController::class, 'updateProductStatus'])->name('products.update-status');

        Route::delete('/{lead}/products/{product}',
            [LeadShowController::class, 'destroyProduct'])->name('products.destroy');

        // ── Product Payments (per-product payment history) ─────
        Route::post('/{lead}/products/{product}/payments',
            [LeadShowController::class, 'storeProductPayment'])->name('products.payments.store');

        Route::delete('/{lead}/products/{product}/payments/{payment}',
            [LeadShowController::class, 'destroyProductPayment'])->name('products.payments.destroy');

        // ── Quotations ─────────────────────────────────────────
        Route::post('/{lead}/quotations',
            [LeadShowController::class, 'storeQuotation'])->name('quotations.store');
        Route::patch('/{lead}/quotations/{quotation}/status',
            [LeadShowController::class, 'updateQuotationStatus'])->name('quotations.update-status');
        Route::delete('/{lead}/quotations/{quotation}',
            [LeadShowController::class, 'destroyQuotation'])->name('quotations.destroy');
    });

    Route::prefix('products')->name('products.')->group(function () {

    // AJAX helpers (must be before {product} wildcard)
    Route::get('attributes-by-category/{category}', [ProductController::class, 'attributesByCategory'])
         ->name('attributes-by-category');

    Route::post('preview-price', [ProductController::class, 'previewPrice'])
         ->name('preview-price');

    // Standard resource routes
    Route::get('/',               [ProductController::class, 'index'])->name('index');
    Route::get('/create',         [ProductController::class, 'create'])->name('create');
    Route::post('/',              [ProductController::class, 'store'])->name('store');
    Route::get('/{product}',      [ProductController::class, 'show'])->name('show');
    Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
    Route::put('/{product}',      [ProductController::class, 'update'])->name('update');
    Route::delete('/{product}',   [ProductController::class, 'destroy'])->name('destroy');


});

Route::middleware(['auth'])->prefix('settings')->name('settings.')->group(function () {

    // Main settings dashboard
    Route::get('/', fn() => view('pages.settings.index'))->name('index');

    // Lead Status  (no create/show pages – handled via modal on index)
    Route::resource('lead-statuses', LeadStatusController::class)
         ->except(['create', 'show']);

    // Lead Source
    Route::resource('lead-sources', LeadSourceController::class)
         ->except(['create', 'show']);

    // Outcome Category
    Route::resource('outcome-categories', OutcomeCategoryController::class)
         ->except(['create', 'show']);

    // Outcome Sub Category
    Route::resource('outcome-sub-categories', OutcomeSubCategoryController::class)
         ->except(['create', 'show']);
});
    Route::get('/get-subcategories/{id}', [OutcomeCategoryController::class, 'getSubCategories']);

     Route::get('/quotations',               [QuotationController::class, 'index'])  ->name('quotations.index');
    Route::get('/quotations/create/{leadId}',        [QuotationController::class, 'create']) ->name('quotations.create');
    Route::post('/quotations',              [QuotationController::class, 'store'])  ->name('quotations.store');
    Route::get('/quotations/{quotation}',   [QuotationController::class, 'show'])   ->name('quotations.show');
    Route::patch('/quotations/{quotation}/approve', [QuotationController::class, 'approve'])->name('quotations.approve');
    Route::delete('/quotations/{quotation}',[QuotationController::class, 'destroy'])->name('quotations.destroy');


    // Helper: product search for Select2 AJAX (optional)
    Route::get('/api/products-search',      [QuotationController::class, 'productsApi'])->name('api.products.search');

    Route::post('/ai/summarize', [AiController::class, 'summarize'])->name('ai.summarize');


//     Route::prefix('products')->name('products.')->middleware(['auth'])->group(function () {
//     Route::get('/',                  [ProductController::class, 'index'])->name('index');
//     Route::get('/create',            [ProductController::class, 'create'])->name('create');
//     Route::post('/',                 [ProductController::class, 'store'])->name('store');
//     Route::get('/{product}',         [ProductController::class, 'show'])->name('show');
//     Route::get('/{product}/edit',    [ProductController::class, 'edit'])->name('edit');
//     Route::put('/{product}',         [ProductController::class, 'update'])->name('update');
//     Route::delete('/{product}',      [ProductController::class, 'destroy'])->name('destroy');
//     Route::patch('/{product}/toggle',[ProductController::class, 'toggleStatus'])->name('toggle');
// });

});