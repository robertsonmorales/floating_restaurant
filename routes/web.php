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