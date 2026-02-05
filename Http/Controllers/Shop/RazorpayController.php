<?php

namespace SleepyBear\Razorpay\Http\Controllers\Shop;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Transformers\OrderResource;
use Razorpay\Api\Api;
use Webkul\Sales\Models\OrderComment;
use Webkul\Sales\Models\OrderPayment;



class RazorpayController extends Controller
{
    
    
    public function redirect()
{
    $cart = \Webkul\Checkout\Facades\Cart::getCart();

    if (! $cart) {
        return redirect()->route('shop.checkout.cart.index');
    }

    $amount = (int) round($cart->grand_total * 100); // paisa

    $keyId     = core()->getConfigData('sales.payment_methods.razorpay.key_id');
    $keySecret = core()->getConfigData('sales.payment_methods.razorpay.key_secret');

    if (! $keyId || ! $keySecret) {
        return redirect()->route('shop.checkout.cart.index')
            ->with('error', 'Razorpay is not configured');
    }
    
     $receipt = 'bagisto_txn_' . \Illuminate\Support\Str::uuid();

    session([
        'razorpay_receipt' => $receipt,
    ]);

    // 🔑 Razorpay order creation payload
    $orderPayload = [
        
        'amount'   => $amount,
        'currency' => 'INR',

        // 🔥 CRITICAL: This links Razorpay → Bagisto
        'receipt' => 'bagisto_cart_' . $cart->id,

        'payment_capture' => 1,
    ];

    $ch = curl_init('https://api.razorpay.com/v1/orders');
    curl_setopt_array($ch, [
        CURLOPT_USERPWD        => $keyId . ':' . $keySecret,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($orderPayload),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 30,
    ]);

    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (! isset($response['id'])) {
        return redirect()->route('shop.checkout.cart.index')
            ->with('error', 'Unable to start Razorpay payment');
    }

    return view('razorpay::shop.checkout', [
        'order'  => $response,
        'key'    => $keyId,
        'amount' => $amount,
        'name'   => config('app.name'),
    ]);
}

    
  /* 
   public function redirect()
{
    if (! $cart = \Webkul\Checkout\Facades\Cart::getCart()) {
        return redirect()->route('shop.checkout.cart.index');
    }

    $amount = (int) round($cart->grand_total * 100); // paisa

    $keyId     = core()->getConfigData('sales.payment_methods.razorpay.key_id');
    $keySecret = core()->getConfigData('sales.payment_methods.razorpay.key_secret');

    if (! $keyId || ! $keySecret) {
        return redirect()->route('shop.checkout.cart.index')
            ->with('error', 'Razorpay is not configured');
    }

    // ✅ IMPORTANT: bind Razorpay order to cart
    $orderPayload = [
        'amount'          => $amount,
        'currency'        => 'INR',

        // 🔑 Use cart id for traceability
        'receipt' => 'bagisto_cart_' . $cart->id,

        // 🔑 Metadata (CRITICAL for webhook)
        'notes' => [
            'cart_id' => $cart->id,
            'email'   => $cart->customer_email,
        ],

        'payment_capture' => 1,
    ];

    $ch = curl_init('https://api.razorpay.com/v1/orders');
    curl_setopt_array($ch, [
        CURLOPT_USERPWD        => $keyId . ':' . $keySecret,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($orderPayload),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 30,
    ]);

    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (! isset($response['id'])) {
        return redirect()->route('shop.checkout.cart.index')
            ->with('error', 'Unable to start Razorpay payment');
    }

    // ⚠️ Optional UX helper (NOT source of truth)
    session([
        'razorpay_order_id' => $response['id'],
    ]);

    return view('razorpay::shop.checkout', [
        'order'  => $response,        // Razorpay order
        'key'    => $keyId,
        'amount' => $amount,
        'name'   => config('app.name'),
    ]);
}

    */
    
    
  public function callback(
    Request $request,
    OrderRepository $orderRepository,
    InvoiceRepository $invoiceRepository
) {
    Log::info('RAZORPAY CALLBACK HIT', [
        'all' => $request->all(),
    ]);

    $paymentId       = $request->get('payment_id');
    $razorpayOrderId = $request->get('order_id');
    $signature       = $request->get('signature');

    if (! $paymentId || ! $razorpayOrderId || ! $signature) {
        return redirect()->route('shop.checkout.cart.index')
            ->with('error', 'Invalid Razorpay response');
    }

    $keyId     = core()->getConfigData('sales.payment_methods.razorpay.key_id');
    $keySecret = core()->getConfigData('sales.payment_methods.razorpay.key_secret');

    // 🔐 Verify signature
    $generatedSignature = hash_hmac(
        'sha256',
        $razorpayOrderId . '|' . $paymentId,
        $keySecret
    );

    if (! hash_equals($generatedSignature, $signature)) {
        Log::error('Razorpay signature mismatch');

        return redirect()->route('shop.checkout.cart.index')
            ->with('error', 'Payment verification failed');
    }

    /**
     * 🔁 IDEMPOTENCY GUARD (CORRECT VERSION)
     */
    $existingPayment = \Webkul\Sales\Models\OrderPayment::where(
        'additional->razorpay_order_id',
        $razorpayOrderId
    )->first();

    if ($existingPayment) {
        session()->flash('order_id', $existingPayment->order_id);
        return redirect()->route('shop.checkout.onepage.success');
    }

    /**
     * 🔍 Fetch Razorpay order → extract cart id
     */
    $ch = curl_init("https://api.razorpay.com/v1/orders/{$razorpayOrderId}");
    curl_setopt_array($ch, [
        CURLOPT_USERPWD        => $keyId . ':' . $keySecret,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
    ]);
    

    $rzpOrder = json_decode(curl_exec($ch), true);
    curl_close($ch);
    
    

    $receipt = $rzpOrder['receipt'] ?? null;

    if (! $receipt || ! str_starts_with($receipt, 'bagisto_cart_')) {
        Log::error('Invalid Razorpay receipt', $rzpOrder);

        return redirect()->route('shop.checkout.cart.index')
            ->with('error', 'Unable to identify order');
    }

    $cartId = (int) str_replace('bagisto_cart_', '', $receipt);

    $cart = app(\Webkul\Checkout\Repositories\CartRepository::class)->find($cartId);

    if (! $cart) {
        Log::error('Cart not found', ['cart_id' => $cartId]);

        return redirect()->route('shop.checkout.cart.index')
            ->with('error', 'Cart expired');
    }

    /**
     * ✅ Create Bagisto Order
     */
    $orderData = (new \Webkul\Sales\Transformers\OrderResource($cart))->jsonSerialize();
    $order = $orderRepository->create($orderData);


     $order->payment()->updateOrCreate(
        ['order_id' => $order->id],
        [
            'method' => 'razorpay',
            'additional' => [
                'razorpay_order_id'   => $razorpayOrderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature'  => $signature,
                'receipt'             => $receipt,
            ],
        ]
    );



// 📝 Add Razorpay details as system order comment
OrderComment::create([
    'order_id'        => $order->id,
    'comment'         => implode("\n", [
        '[Razorpay Payment]',
        'Payment Status: Paid',
        'Razorpay Order ID: ' . $razorpayOrderId,
        'Razorpay Payment ID: ' . $paymentId,
    ]),
    'customer_notified' => 0,
    'visible_on_front' => 0,
]);


    /**
     * ✅ Attach Razorpay payment (ADMIN SAFE)
     */
  // 🔴 IMPORTANT: Force-load fresh order payment
$order = $orderRepository->find($order->id);

// Ensure payment row exists
$payment = $order->payment;

if (! $payment) {
    // Fallback (should not normally happen)
    $payment = $order->payment()->create([
        'method' => 'razorpay',
    ]);
}

    /**
     * ✅ Order MUST be processing BEFORE redirect
     */
    $orderRepository->update([
        'status' => 'processing',
    ], $order->id);

    /**
     * ✅ Invoice
     */
    $invoiceRepository->create([
        'order_id' => $order->id,
        'invoice'  => [
            'items' => $order->items->pluck('qty_ordered', 'id')->toArray(),
        ],
    ]);


\Webkul\Checkout\Facades\Cart::deActivateCart();



    /**
     * 🔑 REQUIRED — FLASH, NOT PUT
     */
    session()->flash('order_id', $order->id);

 return redirect()->route('shop.checkout.onepage.success');

}



   public function cancel()
{
    // Clear Razorpay-related session data
    session()->forget([
        'razorpay_order_id',
        'razorpay_order_processed',
    ]);

    // Optional flash message
    session()->flash(
        'warning',
        'Payment was cancelled. You can try again.'
    );

    // Redirect back to cart
    return redirect()->route('shop.checkout.cart.index');
}

}
