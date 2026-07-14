<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\MeController;
use App\Http\Controllers\Auth\RefreshController;
use App\Http\Controllers\Admin\Users\CreateUserController;
use App\Http\Controllers\Organization\Users\CreateOrganizationUserController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public auth routes (no token required)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('login', LoginController::class)->name('login');
    Route::post('register', RegisterController::class)->name('register');
    Route::post('refresh', RefreshController::class)->name('refresh');
});

/*
|--------------------------------------------------------------------------
| Protected routes (JWT required)
|--------------------------------------------------------------------------
*/
Route::scopeBindings()->middleware('auth.jwt')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('logout', LogoutController::class)->name('logout');
        Route::get('me', MeController::class)->name('me');
    })->withoutScopedBindings();

    Route::prefix('admin/dashboard')->name('admin.dashboard.')->group(function () {
        Route::prefix('users')->name('users.')->group(function () {
            Route::post('/', CreateUserController::class)->name('create'); // Create a new user for a specific client
        });
    });

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::prefix('users')->name('users.')->group(function () {
            Route::post('/', CreateOrganizationUserController::class)->name('create'); // Create a new user for the authenticated user's client
        });
    });
});
