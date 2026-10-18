<?php

use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonorController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('donors', DonorController::class);
Route::resource('donations', DonationController::class)->only(['index', 'create', 'store', 'destroy']);

Route::resource('requests', BloodRequestController::class)->parameters(['requests' => 'bloodRequest']);
Route::patch('requests/{bloodRequest}/fulfill', [BloodRequestController::class, 'fulfill'])->name('requests.fulfill');
Route::patch('requests/{bloodRequest}/cancel', [BloodRequestController::class, 'cancel'])->name('requests.cancel');
