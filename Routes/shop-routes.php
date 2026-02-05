<?php

use Illuminate\Support\Facades\Route;
use SleepyBear\Razorpay\Http\Controllers\Shop\RazorpayWebhookController;
use SleepyBear\Razorpay\Http\Controllers\Shop\RazorController;

Route::group([
    'middleware' => ['web', 'theme', 'locale', 'currency'],
], function () {

    Route::get('razorpay/redirect', [RazorpayController::class, 'redirect'])
        ->name('razorpay.process');

    Route::get('razorpay/callback', [RazorpayController::class, 'callback'])
        ->name('razorpay.callback');

    Route::get('razorpay/cancel', [RazorpayController::class, 'cancel'])
        ->name('razorpay.cancel');
        
        
     Route::post('razorpay/webhook', [RazorpayWebhookController::class, 'handle'])
    ->name('razorpay.webhook');//webhook
   
   
   Route::get('razorpay/pending', function () {
    return view('razorpay::shop.pending');
})->name('razorpay.pending');

Route::get('razorpay/success', [
    \SleepyBear\Razorpay\Http\Controllers\Shop\RazorpayController::class,
    'success'
])->name('razorpay.success');


    
});
