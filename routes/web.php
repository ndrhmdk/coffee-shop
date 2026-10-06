<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomepageController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderCompleteController;

Route::get('/test-error', function () {
    throw new Exception('This is a test exception.');
});

Route::get('/test-log', function () {
    Log::info('Test log message from Coffee Shop.', [
        'section' => 'Section 3',
        'feature' => 'Logging',
    ]);

    return 'Log created.';
});

Route::get('/test-log-telescope', function () {
    Log::warning('Section 3 - Telescope test', [
        'feature' => 'Telescope',
    ]);

    return 'Telescope log test created';
});

Route::get('/test-exception', function () {
    throw new Exception('Section 3 Telescope exception test.');
});

Route::get(
    '/',
    [HomepageController::class, 'index']
)->name('home');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/{order}', [CheckoutController::class,'show'])->name('checkout.show');
Route::get('/order-complete/{order}', OrderCompleteController::class)->name('orders.complete');