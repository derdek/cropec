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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/dayoff-types', [UserController::class, 'getDayoffTypes'])->name('dayoff-types');
    Route::get('/public-holidays', [UserController::class, 'getPublicHolidays'])->name('public-holidays');
    Route::get('/dayoff-requests', [UserController::class, 'getDayoffRequests'])->name('dayoff-requests');
    Route::get('/dayoff-new-requests', [UserController::class, 'getManagedDayoffRequests'])->name('dayoff-new-requests');
    Route::get('/user-dayoffs', [UserController::class, 'getUserDayoffs'])->name('user-dayoffs');
});
