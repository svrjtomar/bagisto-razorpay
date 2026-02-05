<?php

namespace SleepyBear\Razorpay\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\InvoiceRepository;
use SleepyBear\Razorpay\Services\RazorpayService;
use Webkul\Checkout\Repositories\CartRepository;
use Webkul\Sales\Transformers\OrderResource;

/**
 * Razorpay Controller
 *
 * NOTE:
 * This controller is a direct adaptation of PhonePe controller.
 * Logic is intentionally unchanged.
 */
class RazorpayController extends Controller
{
    /**
     * Razorpay service instance
     */
    protected $razorpayService;

    protected $orderRepository;
    protected $invoiceRepository;
    protected $cartRepository;

    public function __construct(
        RazorpayService $razorpayService,
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository,
        CartRepository $cartRepository
    ) {
        $this->razorpayService   = $razorpayService;
        $this->orderRepository  = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->cartRepository   = $cartRepository;
    }

    /**
     * Redirect to Razorpay payment gateway
     */
    public function redirect()
    {
        try {
            $cart = Cart::getCart();

            if (! $cart) {
                return redirect()->route('shop.checkout.cart.index')
                    ->with('error', 'Cart not found. Please try again.');
            }

            $merchantOrderId = 'ORD-' . time() . '-' . rand(1000, 9999);
            $grandTotal = $cart->grand_total;
            $amountInPaise = (int) round($grandTotal * 100);

            if ($amountInPaise < 100) {
                return redirect()->route('shop.checkout.cart.index')
                    ->with('error', 'Minimum order amount is ₹1');
            }

            $paymentData = [
                'merchantOrderId' => $merchantOrderId,
                'amount'          => $amountInPaise,
            ];

            session([
                'razorpay_cart_id'            => $cart->id,
                'razorpay_merchant_order_id'  => $merchantOrderId,
                'razorpay_amount'             => $grandTotal,
            ]);

            $response = $this->razorpayService->createPayment($paymentData);

            if (isset($response['redirectUrl'])) {
                session(['razorpay_order_id' => $response['orderId'] ?? null]);

                Log::channel('razorpay')->info('Redirecting to Razorpay', [
                    'merchant_order_id' => $merchantOrderId,
                    'amount'            => $grandTotal,
                ]);

                return redirect($response['redirectUrl']);
            }

            throw new \Exception('Invalid Razorpay response');

        } catch (\Exception $e) {
            Log::channel('razorpay')->error('Razorpay initiation failed', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('shop.checkout.cart.index')
                ->with('error', 'Payment initiation failed.');
        }
    }

    /**
     * Razorpay callback
     */
    public function callback(Request $request)
    {
        try {
            $cartId           = session('razorpay_cart_id');
            $merchantOrderId  = session('razorpay_merchant_order_id');

            if (! $cartId || ! $merchantOrderId) {
                return redirect()->route('shop.checkout.cart.index')
                    ->with('error', 'Payment session expired.');
            }

            $paymentStatus = $this->razorpayService->checkPaymentStatus($merchantOrderId);

            if (($paymentStatus['state'] ?? null) === 'COMPLETED') {
                return $this->handleSuccessfulPayment(
                    $cartId,
                    session('razorpay_order_id'),
                    $paymentStatus
                );
            }

            return redirect()->route('shop.checkout.cart.index')
                ->with('warning', 'Payment not completed.');

        } catch (\Exception $e) {
            Log::channel('razorpay')->error('Razorpay callback failed', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('shop.checkout.cart.index')
                ->with('error', 'Payment verification failed.');
        }
    }

    protected function handleSuccessfulPayment($cartId, $razorpayOrderId, $paymentStatus)
    {
        $cart = $this->cartRepository->find($cartId);

        $data  = (new OrderResource($cart))->jsonSerialize();
        $order = $this->orderRepository->create($data);

        $this->orderRepository->update(['status' => 'processing'], $order->id);

        $order->payment->update([
            'additional' => [
                'razorpay_order_id' => $razorpayOrderId,
            ],
        ]);

        $this->invoiceRepository->create($this->prepareInvoiceData($order));
        Cart::deActivateCart();

        session()->forget([
            'razorpay_cart_id',
            'razorpay_merchant_order_id',
            'razorpay_order_id',
            'razorpay_amount',
        ]);

        session()->flash('order_id', $order->id);

        return redirect()->route('shop.checkout.onepage.success');
    }

    protected function prepareInvoiceData($order)
    {
        $data = ['order_id' => $order->id];

        foreach ($order->items as $item) {
            $data['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $data;
    }

    public function cancel()
    {
        session()->forget([
            'razorpay_cart_id',
            'razorpay_merchant_order_id',
            'razorpay_order_id',
            'razorpay_amount',
        ]);

        return redirect()->route('shop.checkout.cart.index')
            ->with('warning', 'Payment cancelled.');
    }
}
