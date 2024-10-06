<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [UserController::class, 'getDashboard'])->name('dashboard');

    Route::get('/user-edit/{id}', [UserController::class, 'getUserEditPage'])->name('user-edit');
    Route::post('/user-update/{id}', [UserController::class, 'updateUser'])->name('user-update');
    Route::get('/user-create', [UserController::class, 'getCreateUserPage'])->name('user-create');
    Route::post('/user-create', [UserController::class, 'createUserWithUserDayoffs'])->name('user-create-form');

    Route::view('/public-holiday', 'publicholidays.create')->name('publicholiday-create');
    Route::post('/public-holiday', [UserController::class, 'createPublicHoliday'])->name('publicholiday-create-form');

    Route::get('/dayoff-types', [UserController::class, 'getDayoffTypes'])->name('dayoff-types');
    Route::get('/public-holidays', [UserController::class, 'getPublicHolidays'])->name('public-holidays');
    Route::get('/dayoff-requests', [UserController::class, 'getDayoffRequests'])->name('dayoff-requests');
    Route::get('/dayoff-new-requests', [UserController::class, 'getManagedDayoffRequests'])->name('dayoff-new-requests');
    Route::get('/user-dayoffs', [UserController::class, 'getUserDayoffs'])->name('user-dayoffs');
    Route::get('/create-dayoff-request', [UserController::class, 'createDayoffRequestForm'])->name('create-dayoff-request-form');
    Route::post('/create-dayoff-request', [UserController::class, 'createDayoffRequest'])->name('create-dayoff-request');

    Route::get('/calendar', [UserController::class, 'getCalendar'])->name('calendar');
});
