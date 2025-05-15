<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProviderListingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/', [WelcomeController::class, 'contactUsMail'])-> name('contactUsMail');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/provider/profile', [ProfileController::class, 'updateProviderProfile'])
        ->name('provider.profile.update');
    Route::post('/profile/setWorkingHours', [ProfileController::class, 'setWorkingHours'])->name('setWorkingHours');
    Route::post('/profile/enableCalendar', [ProfileController::class, 'enableGoogleCalendar'])->name('enableGoogleCalendar');
    Route::get('/providers', [ProviderListingController::class, 'index'])->name('index');
    Route::get('/provider/{id}/booking', [BookingController::class, 'index'])->name('booking.index');
    /*     Route::post('/provider/{id}/booking', [BookingController::class, 'store'])->name('booking.store'); */
    Route::get('/get-business-hours/{providerId}', [BookingController::class, 'getBusinessHours']);
    Route::post('/create-booking/{id}', [BookingController::class, 'store'])->name('store');
    Route::prefix('get-bookings')->group(function () {
        Route::get('/provider/{providerId}', [BookingController::class, 'getAppointmentsforProviders']);
        Route::get('/customer/{userId}', [BookingController::class, 'getAppointmentsforCustomers']);
    });
    Route::get('/mybookings', [BookingManagementController::class, 'index'])->name('myBookingsIndex');
    Route::patch('/approveApplication/{id}', [BookingManagementController::class, 'approveApplication'])->name('approveApplication');
    Route::patch('/declineApplication/{id}', [BookingManagementController::class, 'declineApplication'])->name('declineApplication');
    // redirect a post-redirect-get pattern szerint a foglalás után
    Route::get('/appointment-booked-info', function () {
        return view('includes.appointmentBookedInfo');
    })->name('booking.appointmentBookedInfo');

    //Route::post('/calendar/create', [GoogleCalendarController::class, 'storeEvent']);

});

require __DIR__.'/auth.php';
