<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProviderListingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/provider/profile', [ProfileController::class, 'updateProviderProfile'])
        ->name('provider.profile.update');
    Route::get('/providers', [ProviderListingController::class, 'index'])->name('index');
    Route::get('/provider/{id}/booking', [BookingController::class, 'index'])->name('booking.index');
    Route::post('/provider/{id}/booking', [BookingController::class, 'store'])->name('booking.store');

});

require __DIR__.'/auth.php';
