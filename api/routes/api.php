<?php

use App\Http\Controllers\Admin\Users\CreateUserController;
use App\Http\Controllers\Admin\Users\DeleteUserController;
use App\Http\Controllers\Admin\Users\ListUserController;
use App\Http\Controllers\Admin\Users\UpdateUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\MeController;
use App\Http\Controllers\Auth\RefreshController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Organization\Users\CreateOrganizationUserController;
use App\Http\Controllers\Organization\Users\DeleteOrganizationUserController;
use App\Http\Controllers\Organization\Users\IndexOrganizationUserController;
use App\Http\Controllers\Organization\Users\UpdateOrganizationUserController;
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

    Route::prefix('admin/dashboard')->name('admin.dashboard.')->middleware('role:SUPER_ADMIN')->group(function () {
        Route::prefix('users')->name('users.')->group(function () {
            Route::post('/', CreateUserController::class)->name('create');
            Route::get('/', ListUserController::class)->name('list');
            Route::patch('/{userId}', UpdateUserController::class)->name('update');
            Route::delete('/{userId}', DeleteUserController::class)->name('delete');
        });
    });

    Route::prefix('dashboard')->name('dashboard.')->middleware('role:OWNER,MANAGER')->group(function () {
        Route::prefix('users')->name('users.')->group(function () {
            Route::post('/', CreateOrganizationUserController::class)->name('create');
            Route::get('/', IndexOrganizationUserController::class)->name('index');
            Route::patch('/{userId}', UpdateOrganizationUserController::class)->name('update');
            Route::delete('/{userId}', DeleteOrganizationUserController::class)->name('delete');
        });
    });
});
