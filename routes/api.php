<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Admin API Routes untuk real-time data
Route::prefix('admin')->name('admin.api.')->middleware(['auth'])->group(function () {
    Route::get('/field-status', [AdminController::class, 'getFieldStatus'])->name('field-status');
    Route::get('/live-bookings', [AdminController::class, 'getLiveBookings'])->name('live-bookings');
    Route::get('/dashboard-stats', [AdminController::class, 'getDashboardStats'])->name('dashboard-stats');
});
