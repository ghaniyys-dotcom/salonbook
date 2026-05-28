<?php

use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\CustomerLookupController;
use App\Http\Controllers\Api\ServiceApiController;
use App\Http\Controllers\Api\AvailabilityApiController;
use App\Http\Controllers\Api\LiveBookingApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:100,1')->group(function () {
    Route::get('/services', [ServiceApiController::class, 'index']);
    Route::get('/services/{service}', [ServiceApiController::class, 'show']);
    Route::get('/bookings', [BookingApiController::class, 'index']);
    Route::get('/bookings/{booking}', [BookingApiController::class, 'show']);
    Route::get('/availabilities', [AvailabilityApiController::class, 'index']);
    Route::get('/customers/lookup', [CustomerLookupController::class, 'lookup']);
    Route::get('/live-bookings', [LiveBookingApiController::class, 'stream']);
    Route::get('/live-bookings-poll', [LiveBookingApiController::class, 'poll']);

    // Strict rate limit for booking creation (anti-spam)
    Route::post('/bookings', [BookingApiController::class, 'store'])
        ->middleware('throttle:10,1');
});
