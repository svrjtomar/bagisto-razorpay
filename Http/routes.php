<?php

use Illuminate\Support\Facades\Route;
use SleepyBear\Razorpay\Http\Controllers\Shop\RazorpayController;

Route::group([
    'middleware' => ['web', 'theme', 'locale', 'currency'],
], function () {

    Route::get('razorpay/redirect', [RazorpayController::class, 'redirect'])
        ->name('razorpay.process');

    Route::get('razorpay/callback', [RazorpayController::class, 'callback'])
        ->name('razorpay.callback');

    Route::get('razorpay/cancel', [RazorpayController::class, 'cancel'])
        ->name('razorpay.cancel');
});
