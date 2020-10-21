<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\HomeController::class, 'showMain']);

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/cashier', [App\Http\Controllers\HomeController::class, 'cashier'])->name('cashier');
    Route::get('/cook', [App\Http\Controllers\HomeController::class, 'cook'])->name('cook');
    
    Route::resource('/user_accounts', App\Http\Controllers\UserAccountController::class);
    Route::resource('/menu_categories', App\Http\Controllers\MenuCategoryController::class);
    Route::resource('/menu_types', App\Http\Controllers\MenuTypeController::class);
    Route::resource('/menus', App\Http\Controllers\MenuController::class);
    Route::resource('/product_categories', App\Http\Controllers\ProductCategoryController::class);
    Route::resource('/product_units', App\Http\Controllers\ProductUnitController::class);
    Route::resource('/products', App\Http\Controllers\ProductController::class);
    Route::resource('/order_status', App\Http\Controllers\OrderStatusController::class);
    Route::resource('/order_substatus', App\Http\Controllers\OrderSubstatusController::class);
    Route::resource('/cancellation_reasons', App\Http\Controllers\CancellationReasonController::class);
    Route::resource('/table_maintenance', App\Http\Controllers\TableMaintenanceController::class);
    Route::resource('/expense_categories', App\Http\Controllers\ExpenseCategoryController::class);
    Route::resource('/customer_discounts', App\Http\Controllers\CustomerDiscountController::class);
    Route::resource('/employee_positions', App\Http\Controllers\EmployeePositionController::class);
    Route::resource('/audit_trail_logs', App\Http\Controllers\AuditTrailLogController::class);
    Route::resource('/stocks', App\Http\Controllers\StockController::class);
    Route::resource('/deliveries', App\Http\Controllers\DeliveryController::class);
    Route::resource('/damages', App\Http\Controllers\DamageController::class);
    Route::resource('/inventory_logs', App\Http\Controllers\InventoryLogController::class);
    Route::resource('/employees', App\Http\Controllers\EmployeeController::class);


    // My Account
    $match = ['PUT', 'POST'];
    Route::get('/account_settings/password', [App\Http\Controllers\MyAccountController::class, 'password'])->name('account_settings.password');
    Route::match($match, '/account_settings/password_update', [App\Http\Controllers\MyAccountController::class, 'passwordUpdate'])->name('account_settings.password_update');

    Route::get('/account_settings/email', [App\Http\Controllers\MyAccountController::class, 'email'])->name('account_settings.email');
    Route::match($match, '/account_settings/email_update', [App\Http\Controllers\MyAccountController::class, 'emailUpdate'])->name('account_settings.email_update');

    Route::match($match, '/account_settings/change_profile', [App\Http\Controllers\MyAccountController::class, 'changeProfile'])->name('account_settings.change_profile');

    Route::resource('/account_settings', App\Http\Controllers\MyAccountController::class);    
    // Ends here
});