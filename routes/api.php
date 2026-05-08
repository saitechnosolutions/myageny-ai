<?php

use App\Http\Controllers\AdminDashboardProductController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\App\AuthController as MobileAuthController;
use App\Http\Controllers\App\DailyAttendanceController as MobileDailyAttendanceController;
use App\Http\Controllers\App\DashboardController as MobileDashboardController;
use App\Http\Controllers\App\LeadController as MobileLeadController;
use App\Http\Controllers\App\LeadShowController as MobileLeadShowController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadFormFieldController;
use App\Http\Controllers\LeadProductController;
use App\Http\Controllers\LeadProductPriceRequestController;
use App\Http\Controllers\OutcomeCategoryController;
use App\Http\Controllers\App\AppApiController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SuperAdminDashboardController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Home Page Dashboard Routes
|--------------------------------------------------------------------------
*/
    Route::post('/ai/summarize', [AiController::class, 'summarize'])->name('ai.summarize');
    Route::get('/dashboard-data', [SuperAdminDashboardController::class, 'dashboardData']);
    Route::get('/product-dashboard-data', [AdminDashboardProductController::class, 'index']);
    Route::get('/getLeadQuotations/{leadid}', [QuotationController::class, 'getLeadQuotations']);
    Route::get('/getAllQuotations', [QuotationController::class, 'getAllQuotations']);

        // Filter dropdown options
        Route::get('/product-dashboard-data/filters', [AdminDashboardProductController::class, 'filterOptions']);

    // Route::get('/products', [ProductController::class, 'getProducts']);



 // ── Product catalogue ──────────────────────────────────────────
    Route::get('products',        [LeadProductController::class, 'productList']);
    Route::get('products/{id}',   [LeadProductController::class, 'productDetail']);

    // ── Lead Products (Deals) ──────────────────────────────────────
    Route::get ('lead-products/{lead_id}', [LeadProductController::class, 'index']);
    Route::post('lead-products',           [LeadProductController::class, 'store']);
    Route::post('lead-product-price-requests', [LeadProductPriceRequestController::class, 'store']);
    Route::put ('lead-products/status',    [LeadProductController::class, 'updateStatus']);
    Route::delete('lead-products/{id}',    [LeadProductController::class, 'destroy']);

    // ── Payments ───────────────────────────────────────────────────
    Route::get   ('payments/{lead_product_id}', [LeadProductController::class, 'paymentHistory']);
    Route::post  ('payments',                   [LeadProductController::class, 'storePayment']);
    Route::delete('payments/{id}',              [LeadProductController::class, 'destroyPayment']);

/*
|--------------------------------------------------------------------------
| Mobile Auth Routes
|--------------------------------------------------------------------------
*/

Route::prefix('lead-form-fields')->group(function () {

    // ── Meta / Utility ───────────────────────────────────────────────
    Route::get('field-types',  [LeadFormFieldController::class, 'fieldTypes']);   // GET  /api/lead-form-fields/field-types
    Route::get('schema',       [LeadFormFieldController::class, 'schema']);       // GET  /api/lead-form-fields/schema
    Route::post('reorder',     [LeadFormFieldController::class, 'reorder']);      // POST /api/lead-form-fields/reorder
    Route::post('calculate',   [LeadFormFieldController::class, 'calculate']);    // POST /api/lead-form-fields/calculate

    // ── CRUD ─────────────────────────────────────────────────────────
    Route::get('/',            [LeadFormFieldController::class, 'index']);        // GET  /api/lead-form-fields
    Route::post('/',           [LeadFormFieldController::class, 'store']);        // POST /api/lead-form-fields
    Route::get('/{leadFormField}',    [LeadFormFieldController::class, 'show']);  // GET  /api/lead-form-fields/{id}
    Route::put('/{leadFormField}',    [LeadFormFieldController::class, 'update']); // PUT  /api/lead-form-fields/{id}
    Route::patch('/{leadFormField}',  [LeadFormFieldController::class, 'update']); // PATCH /api/lead-form-fields/{id}
    Route::delete('/{leadFormField}', [LeadFormFieldController::class, 'destroy']); // DELETE /api/lead-form-fields/{id}
    Route::patch('/{leadFormField}/toggle', [LeadFormFieldController::class, 'toggle']); // PATCH /api/lead-form-fields/{id}/toggle
});

Route::prefix('mobile/auth')->group(function () {
    Route::post('login',    [MobileAuthController::class, 'login']);
    Route::post('register', [MobileAuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [MobileAuthController::class, 'logout']);
        Route::get('me',      [MobileAuthController::class, 'me']);
    });
});


Route::get('/get-outcome-category', [OutcomeCategoryController::class, 'getOutcomeCategory']);
Route::get('/get-subcategories/{id}', [OutcomeCategoryController::class, 'getSubCategories']);
Route::get('/get-lead-status', [LeadController::class, 'leadStatus']);
Route::get('/get-lead-source', [LeadController::class, 'leadSource']);
Route::get('/quotation/{quotation}', [QuotationController::class, 'apiShow']);
Route::post('/create-quotation', [QuotationController::class, 'apiStore']);
Route::put('/quotation/{quotation}', [QuotationController::class, 'apiUpdate']);
Route::patch('/quotation/{quotation}', [QuotationController::class, 'apiUpdate']);

/*
|--------------------------------------------------------------------------
| Mobile Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->prefix('mobile')->name('mobile.')->group(function () {
    Route::get('dashboard', [MobileDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::post('check-in', [MobileDailyAttendanceController::class, 'attendanceCheckIn'])->name('check-in');
        Route::post('check-out', [MobileDailyAttendanceController::class, 'attendanceCheckOut'])->name('check-out');
        Route::get('daily-list', [MobileDailyAttendanceController::class, 'dailyAttendanceList'])->name('daily-list');
    });
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

    Route::post('/reminder-list', [AppApiController::class, 'reminderList']);

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
