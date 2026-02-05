<?php

use Illuminate\Support\Facades\Route;
use SleepyBear\Razorpay\Http\Controllers\Admin\RazorpayController;

Route::group(
    ['middleware' => ['web', 'admin'], 'prefix' => 'admin/razorpay'],
    function () {
        Route::controller(RazorpayController::class)->group(function () {
            Route::get('', 'index')->name('admin.razorpay.index');
        });
    }
);
