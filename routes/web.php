<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\AccessMappingController;
use App\Http\Controllers\AssetEntryController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeOnboardingController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\FacebookIntegrationController;
use App\Http\Controllers\HolidayCalendarController;
use App\Http\Controllers\InternJoiningFormController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadCallUpdateController;
use App\Http\Controllers\LeadProductPriceRequestController;
use App\Http\Controllers\LeadShowController;
use App\Http\Controllers\LeadSourceController;
use App\Http\Controllers\LeadStatusController;
use App\Http\Controllers\MastersController;
use App\Http\Controllers\OutcomeCategoryController;
use App\Http\Controllers\OutcomeSubCategoryController;
use App\Http\Controllers\ProductAttributeController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\QuotationSettingsController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\UserController;
use App\Models\EmployeeOnboarding;
use App\Models\InternJoiningForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

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

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Forgot password placeholder
Route::get('/forgot-password', function () {
    return redirect()->route('login')->with('error', 'Password reset is coming soon. Please contact your administrator.');
})->name('password.request');


Route::get('/lead/form-customization', fn() => view('pages.field_customization.index'))
    ->middleware(['auth', 'can:form_customization.menuview']);
Route::middleware(['auth'])->group(function () {

  // Dashboard
    Route::get('/', fn() => redirect()->route('dashboard'));
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

     // Super Admin Dashboard (API-integrated blade)
    Route::get('/dashboard/admin', [DashboardController::class, 'index'])
        ->middleware('can:dashboard.view')
        ->name('dashboard.admin');

    Route::get('/product-dashboard/admin', [SuperAdminDashboardController::class, 'adminProductindex'])
        ->middleware('can:dashboard.view');

    // Default redirect by role
    Route::get('/dashboard', function () {
    return redirect()->route('dashboard.admin');
        // if (auth()->user()->hasAnyRole(['Super Admin', 'admin', 'super_admin'])) {
        //     return redirect()->route('dashboard.admin');
        // }
        // return redirect()->route('leads.index');
    })->name('dashboard');

    // Masters
    Route::get('/masters', [MastersController::class, 'index'])
        ->middleware('can:masters.view')
        ->name('masters.index');

    Route::get('/authentications', [UserController::class, 'authIndex'])
        ->middleware('can:authentication.menuview')
        ->name('auth.index');

    Route::prefix('authentications')->name('auth.')->group(function () {
        Route::get('/roles', [RolePermissionController::class, 'rolesIndex'])->middleware('can:roles.view')->name('roles.index');
        Route::get('/roles/create', [RolePermissionController::class, 'rolesCreate'])->middleware('can:roles.manage')->name('roles.create');
        Route::post('/roles', [RolePermissionController::class, 'rolesStore'])->middleware('can:roles.manage')->name('roles.store');
        Route::get('/roles/{role}/edit', [RolePermissionController::class, 'rolesEdit'])->middleware('can:roles.manage')->name('roles.edit');
        Route::put('/roles/{role}', [RolePermissionController::class, 'rolesUpdate'])->middleware('can:roles.manage')->name('roles.update');
        Route::get('/roles/{role}/permissions', [RolePermissionController::class, 'rolesPermissionsEdit'])->middleware('can:roles.manage')->name('roles.permissions.edit');
        Route::put('/roles/{role}/permissions', [RolePermissionController::class, 'rolesPermissionsUpdate'])->middleware('can:roles.manage')->name('roles.permissions.update');
        Route::delete('/roles/{role}', [RolePermissionController::class, 'rolesDestroy'])->middleware('can:roles.manage')->name('roles.destroy');

        Route::get('/permissions', [RolePermissionController::class, 'permissionsIndex'])->middleware('can:permissions.view')->name('permissions.index');
        Route::get('/permissions/create', [RolePermissionController::class, 'permissionsCreate'])->middleware('can:permissions.manage')->name('permissions.create');
        Route::post('/permissions', [RolePermissionController::class, 'permissionsStore'])->middleware('can:permissions.manage')->name('permissions.store');
        Route::get('/permissions/{permission}/edit', [RolePermissionController::class, 'permissionsEdit'])->middleware('can:permissions.manage')->name('permissions.edit');
        Route::put('/permissions/{permission}', [RolePermissionController::class, 'permissionsUpdate'])->middleware('can:permissions.manage')->name('permissions.update');
        Route::delete('/permissions/{permission}', [RolePermissionController::class, 'permissionsDestroy'])->middleware('can:permissions.manage')->name('permissions.destroy');

        Route::get('/role-mappings', [AccessMappingController::class, 'roleIndex'])->middleware('can:roles.view')->name('role-mappings.index');
        Route::put('/role-mappings', [AccessMappingController::class, 'roleUpdate'])->middleware('can:roles.manage')->name('role-mappings.update');
        Route::get('/user-mappings', [AccessMappingController::class, 'userIndex'])->middleware('can:users.view')->name('user-mappings.index');
        Route::post('/user-mappings', [AccessMappingController::class, 'userUpdate'])->middleware('can:users.manage')->name('user-mappings.update');
        Route::delete('/user-mappings/{mapping}', [AccessMappingController::class, 'userDestroy'])->middleware('can:users.manage')->name('user-mappings.destroy');

        Route::post('/users/{user}/assign-role', [RolePermissionController::class, 'assignUserRole'])->middleware('can:users.manage')->name('users.assign-role');
    });

    Route::prefix('users')->name('users.')->middleware(['auth'])->group(function () {

    // List, Create, Store, Show, Edit, Update, Delete
    Route::get('/',              [UserController::class, 'index'])->middleware('can:users.view')->name('index');
    Route::get('/create',        [UserController::class, 'create'])->middleware('can:users.manage')->name('create');
    Route::post('/',             [UserController::class, 'store'])->middleware('can:users.manage')->name('store');
    Route::get('/{user}',        [UserController::class, 'show'])->middleware('can:users.view')->name('show');
    Route::get('/{user}/edit',   [UserController::class, 'edit'])->middleware('can:users.manage')->name('edit');
    Route::put('/{user}',        [UserController::class, 'update'])->middleware('can:users.manage')->name('update');
    Route::delete('/{user}',     [UserController::class, 'destroy'])->middleware('can:users.manage')->name('destroy');

    // Extra actions
    Route::patch('/{user}/toggle-status',  [UserController::class, 'toggleStatus'])->middleware('can:users.manage')->name('toggle-status');
    Route::post('/{user}/reset-password',  [UserController::class, 'resetPassword'])->middleware('can:users.manage')->name('reset-password');
});

    Route::get('/companies', [CompanyController::class, 'index'])->middleware('can:companies.view')->name('companies.index');
    Route::get('/companies/create', [CompanyController::class, 'create'])->middleware('can:companies.manage')->name('companies.create');
    Route::post('/companies', [CompanyController::class, 'store'])->middleware('can:companies.manage')->name('companies.store');
    Route::get('/companies/{company}', [CompanyController::class, 'show'])->middleware('can:companies.view')->name('companies.show');
    Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->middleware('can:companies.manage')->name('companies.edit');
    Route::put('/companies/{company}', [CompanyController::class, 'update'])->middleware('can:companies.manage')->name('companies.update');
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->middleware('can:companies.manage')->name('companies.destroy');
    Route::get('/hrms/dashboard', function () {
        $stats = [
            'employees_total' => EmployeeOnboarding::count(),
            'employees_pending' => EmployeeOnboarding::where('status', 'pending')->count(),
            'employees_verified' => EmployeeOnboarding::where('status', 'verified')->count(),
            'interns_total' => InternJoiningForm::count(),
        ];

        return view('pages.hrms.dashboard.index', compact('stats'));
    })->name('hrms.dashboard');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
    Route::resource('assets', AssetEntryController::class);
    Route::resource('employee-onboarding', EmployeeOnboardingController::class);
    Route::resource('interns', InternJoiningFormController::class);
    Route::get('/lead-price-requests', [LeadProductPriceRequestController::class, 'index'])
        ->middleware('can:price_requests.view')
        ->name('lead-price-requests.index');
    Route::patch('/lead-price-requests/{priceRequest}/approve', [LeadProductPriceRequestController::class, 'approve'])
        ->middleware('can:price_requests.approve')
        ->name('lead-price-requests.approve');
    Route::patch('/lead-price-requests/{priceRequest}/reject', [LeadProductPriceRequestController::class, 'reject'])
        ->middleware('can:price_requests.reject')
        ->name('lead-price-requests.reject');

    // ── Main Lead CRUD ─────────────────────────────────────────
   Route::prefix('leads')->name('leads.')->group(function () {

        // ── Core CRUD ─────────────────────────────────────────
        Route::get('/',            [LeadController::class, 'index'])->middleware('can:leads.view')->name('index');
        Route::get('/products',    [LeadController::class, 'productsIndex'])->middleware('can:leads.view')->name('products.index');
        Route::get('/create',      [LeadController::class, 'create'])->middleware('can:leads.create')->name('create');
        Route::get('/call-updates',[LeadCallUpdateController::class, 'index'])->middleware('can:call_updates.view')->name('calls.index');
        Route::post('/',           [LeadController::class, 'store'])->middleware('can:leads.create')->name('store');
        Route::get('/{lead}',      [LeadController::class, 'show'])->middleware('can:leads.view')->name('show');
        Route::get('/{lead}/edit', [LeadController::class, 'edit'])->middleware('can:leads.edit')->name('edit');
        Route::put('/{lead}',      [LeadController::class, 'update'])->middleware('can:leads.edit')->name('update');
        Route::delete('/{lead}',   [LeadController::class, 'destroy'])->middleware('can:leads.delete')->name('destroy');
        Route::patch('/{lead}/status', [LeadController::class, 'updateStatus'])->middleware('can:leads.update')->name('update-status');

        // ── Call Updates ──────────────────────────────────────
        Route::post('/{lead}/calls',             [LeadShowController::class, 'storeCall'])->middleware('can:call_updates.create')->name('calls.store');
        Route::delete('/{lead}/calls/{call}',    [LeadShowController::class, 'destroyCall'])->middleware('can:call_updates.delete')->name('calls.destroy');

        // ── Reminders ─────────────────────────────────────────
        Route::post('/{lead}/reminders',                  [LeadShowController::class, 'storeReminder'])->middleware('can:leads.edit')->name('reminders.store');
        Route::patch('/{lead}/reminders/{reminder}/done', [LeadShowController::class, 'completeReminder'])->middleware('can:leads.edit')->name('reminders.complete');
        Route::delete('/{lead}/reminders/{reminder}',     [LeadShowController::class, 'destroyReminder'])->middleware('can:leads.edit')->name('reminders.destroy');

        // ── Products ───────────────────────────────────────────
        Route::post('/{lead}/products',
            [LeadShowController::class, 'storeProduct'])->middleware('can:leads.edit')->name('products.store');

        Route::patch('/{lead}/products/{product}/status',
            [LeadShowController::class, 'updateProductStatus'])->middleware('can:leads.edit')->name('products.update-status');

        Route::delete('/{lead}/products/{product}',
            [LeadShowController::class, 'destroyProduct'])->middleware('can:leads.edit')->name('products.destroy');

        // ── Product Payments (per-product payment history) ─────
        Route::post('/{lead}/products/{product}/payments',
            [LeadShowController::class, 'storeProductPayment'])->middleware('can:leads.edit')->name('products.payments.store');

        Route::delete('/{lead}/products/{product}/payments/{payment}',
            [LeadShowController::class, 'destroyProductPayment'])->middleware('can:leads.edit')->name('products.payments.destroy');

        // ── Quotations ─────────────────────────────────────────
        Route::post('/{lead}/quotations',
            [LeadShowController::class, 'storeQuotation'])->middleware('can:quotations.create')->name('quotations.store');
        Route::patch('/{lead}/quotations/{quotation}/status',
            [LeadShowController::class, 'updateQuotationStatus'])->middleware('can:quotations.approve')->name('quotations.update-status');
        Route::delete('/{lead}/quotations/{quotation}',
            [LeadShowController::class, 'destroyQuotation'])->middleware('can:quotations.delete')->name('quotations.destroy');
    });

    Route::prefix('products')->name('products.')->group(function () {

    // AJAX helpers (must be before {product} wildcard)
    Route::get('attributes-by-category/{category}', [ProductController::class, 'attributesByCategory'])
         ->middleware('can:products.view')
         ->name('attributes-by-category');

    Route::post('preview-price', [ProductController::class, 'previewPrice'])
         ->middleware('can:products.view')
         ->name('preview-price');

    // Standard resource routes
    Route::get('/',               [ProductController::class, 'index'])->middleware('can:products.view')->name('index');
    Route::get('/create',         [ProductController::class, 'create'])->middleware('can:products.create')->name('create');
    Route::post('/',              [ProductController::class, 'store'])->middleware('can:products.create')->name('store');
    Route::get('/{product}',      [ProductController::class, 'show'])->middleware('can:products.view')->name('show');
    Route::get('/{product}/edit', [ProductController::class, 'edit'])->middleware('can:products.edit')->name('edit');
    Route::put('/{product}',      [ProductController::class, 'update'])->middleware('can:products.edit')->name('update');
    Route::delete('/{product}',   [ProductController::class, 'destroy'])->middleware('can:products.delete')->name('destroy');


});

Route::prefix('settings')->name('settings.')->middleware('can:settings.view')->group(function () {

    // Main settings dashboard
    Route::get('/', fn() => view('pages.settings.index'))->name('index');

    // Lead Status  (no create/show pages – handled via modal on index)
    Route::resource('lead-statuses', LeadStatusController::class)
         ->middleware('can:settings.manage')
         ->except(['create', 'show']);

    Route::resource('product-category', ProductCategoryController::class)
         ->middleware('can:settings.manage')
         ->except(['create', 'show']);

    Route::resource('departments', DepartmentController::class)
         ->middleware('can:settings.manage')
         ->except(['show']);

    Route::post('holiday-calendars/import', [HolidayCalendarController::class, 'import'])
         ->middleware('can:settings.manage')
         ->name('holiday-calendars.import');

    Route::resource('holiday-calendars', HolidayCalendarController::class)
         ->middleware('can:settings.manage')
         ->except(['show']);

    Route::resource('product-attribute', ProductAttributeController::class)
         ->middleware('can:settings.manage')
         ->except(['create', 'show']);

    Route::get('/facebook-integration',       [FacebookIntegrationController::class, 'index'])->name('facebook-integration');

    Route::get('/auth/redirect', function () {
    return Socialite::driver('facebook')->redirect();
});

Route::get('/api_integrations',[FacebookIntegrationController::class,'index']);
Route::get('/facebook_integration',[FacebookIntegrationController::class,'facebookIndex'])->name('facebook_integration'); // FACEBOOK INTEGRATION INDEX PAGE
Route::post('/connectfb',[FacebookIntegrationController::class,'connectfb'])->middleware('can:settings.manage');
Route::post('/mapfields',[FacebookIntegrationController::class,'mapfields'])->middleware('can:settings.manage');
Route::post('/fbassignleads',[FacebookIntegrationController::class,'fbassignleads'])->middleware('can:settings.manage');

    Route::get('/authenticate/redirect/{social}',[FacebookIntegrationController::class,'socialiteRedirect'])->name('socialite-redirect');
Route::get('/authenticate/callback/{social}',[FacebookIntegrationController::class,'socialiteCallback'])->name('socialite-callback');

Route::get('/auth/facebook',[FacebookIntegrationController::class,'socialiteRedirect']);
Route::get('/auth/facebook/callback',[FacebookIntegrationController::class,'socialiteCallback'])->name('facebook_callback');

Route::post('/multiple_campaigns',[FacebookIntegrationController::class,'multipleCampaigns'])->middleware('can:settings.manage')->name('multiple_campaigns');
Route::post('/choose_camps',[FacebookIntegrationController::class,'chooseCampaigns'])->middleware('can:settings.manage');

Route::get('/viewassigned', [FacebookIntegrationController::class, 'viewAssigned']);
Route::post('/assignUsers', [FacebookIntegrationController::class, 'assignUsers'])->middleware('can:settings.manage');

Route::get('/fb_multiple_campaigns/{adid}', [FacebookIntegrationController::class, 'fbMultipleCampaigns'])->name('fb_multiple_campaigns');
Route::get('/fb_multiple_accounts/{adid}', [FacebookIntegrationController::class, 'fbMultipleAdAccs'])->name('fb_multiple_accounts');
Route::get('/fb_ac_error', [FacebookIntegrationController::class, 'fberrorLogin'])->name('fb_ac_error');

Route::post('/choose_ad_accouts',[FacebookIntegrationController::class,'chooseadaccs'])->middleware('can:settings.manage');
Route::post('/fbassignleads',[FacebookIntegrationController::class,'fbassignleads'])->middleware('can:settings.manage')->name('fbassignleads');

Route::post('/deleteintegration',[FacebookIntegrationController::class,'deleteintegration'])->middleware('can:settings.manage')->name('deleteintegration');
Route::post('/editfieldmaps',[FacebookIntegrationController::class,'editfieldmaps'])->middleware('can:settings.manage')->name('editfieldmaps');
Route::post('/facebook-integration/{campaignMaster}/sync', [FacebookIntegrationController::class, 'syncCampaign'])
    ->middleware('can:settings.manage')
    ->name('facebook-integration.sync');


    Route::get('/quotation-setting',       [QuotationSettingsController::class, 'index'])->name('quotation');

    Route::post('/quotation', [QuotationSettingsController::class, 'update'])->middleware('can:settings.manage')->name('quotation.update');
    Route::delete('/quotation/file/{type}', [QuotationSettingsController::class, 'deleteFile'])->middleware('can:settings.manage')->name('quotation.file.delete');

    // Lead Source
    Route::resource('lead-sources', LeadSourceController::class)
         ->middleware('can:lead_source.view')
         ->except(['create', 'show']);

    // Outcome Category
    Route::resource('outcome-categories', OutcomeCategoryController::class)
         ->middleware('can:outcome_category.view')
         ->except(['create', 'show']);

    // Outcome Sub Category
    Route::resource('outcome-sub-categories', OutcomeSubCategoryController::class)
         ->middleware('can:outcome_sub_category.view')
         ->except(['create', 'show']);
});
    Route::get('/get-subcategories/{id}', [OutcomeCategoryController::class, 'getSubCategories'])->middleware('can:leads.view');

     Route::get('/quotations', [QuotationController::class, 'index'])->middleware('can:quotations.view')->name('quotations.index');
    Route::get('/quotations/create/{leadId?}', [QuotationController::class, 'create'])->middleware('can:quotations.create')->name('quotations.create');
    Route::post('/quotations', [QuotationController::class, 'store'])->middleware('can:quotations.create')->name('quotations.store');
    Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->middleware('can:quotations.view')->name('quotations.show');
    Route::patch('/quotations/{quotation}/approve', [QuotationController::class, 'approve'])->middleware('can:quotations.approve')->name('quotations.approve');
    Route::delete('/quotations/{quotation}', [QuotationController::class, 'destroy'])->middleware('can:quotations.delete')->name('quotations.destroy');


    // Helper: product search for Select2 AJAX (optional)
    Route::get('/api/products-search', [QuotationController::class, 'productsApi'])->middleware('can:quotations.create')->name('api.products.search');

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