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
    Route::get('/my_account/change_password', [
    	App\Http\Controllers\MyAccountController::class, 'changingPassword'
    ])->name('my_account.change_password');

    Route::match($match, '/my_account/password_update', [
    	App\Http\Controllers\MyAccountController::class, 'updatePassword'
    ])->name('my_account.password_update');

    Route::match($match, '/my_account/change_profile', [
    	App\Http\Controllers\MyAccountController::class, 'changeProfile'
    ])->name('my_account.change_profile');

    Route::resource('/my_account', App\Http\Controllers\MyAccountController::class);
    // Ends here
});