<?php

namespace SleepyBear\Razorpay\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;


class RazorpayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerConfig();
        
     $this->app->singleton(\SleepyBear\Razorpay\Services\RazorpayService::class);
     
      $this->mergeConfigFrom(
        __DIR__ . '/../Config/paymentmethods.php',
        'payment_methods');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin-routes.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/shop-routes.php');

        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');


        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'razorpay');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'razorpay');
        
      
      
           $this->loadViewsFrom(
        __DIR__ . '/../Resources/views/admin',
        'admin'
    );
 
         

        // Publish payment method config
        $this->publishes([
            __DIR__ . '/../Config/paymentmethods.php' => config_path('paymentmethods.php'),
        ], 'razorpay-config');

        // Publish system config
        $this->publishes([
            __DIR__ . '/../Config/system.php' => config_path('razorpay-system.php'),
        ], 'razorpay-config');

        // Publish views
        $this->publishes([
            __DIR__ . '/../Resources/views' => resource_path('views/vendor/razorpay'),
        ], 'razorpay-views');

        // Publish translations
        $this->publishes([
            __DIR__ . '/../Resources/lang' => lang_path('vendor/razorpay'),
        ], 'razorpay-lang');

        // Publish assets (logo, js if any)
        $this->publishes([
            __DIR__ . '/../Resources/assets' => public_path('vendor/sleepybear/razorpay'),
        ], 'razorpay-assets');

        // Publish everything
        $this->publishes([
            __DIR__ . '/../Config/paymentmethods.php' => config_path('paymentmethods.php'),
            __DIR__ . '/../Config/system.php' => config_path('razorpay-system.php'),
            __DIR__ . '/../Resources/views' => resource_path('views/vendor/razorpay'),
            __DIR__ . '/../Resources/lang' => lang_path('vendor/razorpay'),
            __DIR__ . '/../Resources/assets' => public_path('vendor/sleepybear/razorpay'),
        ], 'razorpay');
        
        
          View::addNamespace('admin', resource_path('views'));
    }

    /**
     * Register package config.
     */
    protected function registerConfig(): void
    {
        // Payment method registration (checkout visibility)
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/paymentmethods.php',
            'payment_methods'
        );

        // Admin system configuration
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php',
            'core'
        );
    }
}
