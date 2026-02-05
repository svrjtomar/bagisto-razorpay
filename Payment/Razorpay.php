<?php

namespace SleepyBear\Razorpay\Payment;

use Webkul\Payment\Payment\Payment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Razorpay extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code = 'razorpay';

    /**
     * Get redirect URL for Razorpay payment
     */
    public function getRedirectUrl()
    {
        return route('razorpay.process');
    }

   
       // Fallback Razorpay icon
     

    /**
     * Check if payment method is available
     */
    public function isAvailable()
    {
      //  $clientId     = $this->getConfigData('client_id');
      //  $clientSecret = $this->getConfigData('client_secret');

       // return ! empty($clientId) && ! empty($clientSecret);
       
      return (bool) core()->getConfigData('sales.payment_methods.razorpay.active');

    }
    
     /**
     * Get payment method image
     */
    
    
public function getImage()
{
    return asset('vendor/sleepybear/razorpay/images/razorpay.png');
}




}
