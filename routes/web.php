<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

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
