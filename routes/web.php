<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DayoffRequestController;
use App\Http\Controllers\DayoffTypeController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\PublicHolidayController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDayoffController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'getDashboard'])->name('dashboard');

    Route::get('/user-edit/{id}', [UserController::class, 'getUserEditPage'])->name('user-edit');
    Route::post('/user-update/{id}', [UserController::class, 'updateUser'])->name('user-update');
    Route::get('/user-create', [UserController::class, 'getCreateUserPage'])->name('user-create');
    Route::post('/user-create', [UserController::class, 'createUserWithUserDayoffs'])->name('user-create-form');

    Route::view('/public-holiday', 'publicholidays.create')->name('publicholiday-create');
    Route::post('/public-holiday', [PublicHolidayController::class, 'createPublicHoliday'])->name('publicholiday-create-form');
    Route::get('/public-holidays', [PublicHolidayController::class, 'getPublicHolidays'])->name('public-holidays');

    Route::get('/dayoff-types', [DayoffTypeController::class, 'getDayoffTypes'])->name('dayofftypes');
    Route::get('/dayoff-types/create', [DayoffTypeController::class, 'getDayoffTypeCreatePage'])->name('dayofftypes.createform');
    Route::get('/dayoff-types/{dayoffTypeId}', [DayoffTypeController::class, 'getDayoffTypePage'])->name('dayofftypes.edit');
    Route::put('/dayoff-types/{dayoffTypeId}', [DayoffTypeController::class, 'updateDayoffType'])->name('dayofftypes.update');
    Route::delete('/dayoff-types/{dayoffTypeId}', [DayoffTypeController::class, 'deleteDayoffType'])->name('dayofftypes.delete');
    Route::post('/dayoff-types', [DayoffTypeController::class, 'createDayoffType'])->name('dayofftypes.store');

    Route::get('/dayoff-requests', [DayoffRequestController::class, 'getDayoffRequests'])->name('dayoff-requests');
    Route::get('/create-dayoff-request', [DayoffRequestController::class, 'createDayoffRequestForm'])->name('create-dayoff-request-form');
    Route::post('/create-dayoff-request', [DayoffRequestController::class, 'createDayoffRequest'])->name('create-dayoff-request');

    Route::get('/user-dayoffs', [UserDayoffController::class, 'getUserDayoffs'])->name('user-dayoffs');
});

Route::get('/login/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/login/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);
