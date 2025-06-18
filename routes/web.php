<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\Admin\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// User Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/fields', [FieldController::class, 'index'])->name('fields');
Route::get('/fields/all', [FieldController::class, 'allFields'])->name('fields.all');
Route::get('/field/{id}', [FieldController::class, 'show'])->name('field.detail');
Route::get('/user/field-detail/ajax/{courtId}', [FieldController::class, 'getCourtScheduleAjax'])->name('field.schedule.ajax');

// Admin API Routes (untuk integrasi dengan Filament)
Route::prefix('admin-api')->name('admin.api.')->group(function () {
    Route::get('/booking-data', [AdminController::class, 'getBookingData'])->name('booking-data');
    Route::post('/schedule/{id}/availability', [AdminController::class, 'updateScheduleAvailability'])->name('schedule.availability');
});

// Additional Admin Routes (jika diperlukan di luar Filament)
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
});
