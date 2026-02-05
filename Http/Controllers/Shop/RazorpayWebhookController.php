<?php

namespace SleepyBear\Razorpay\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Webkul\Sales\Models\OrderPayment;
use Webkul\Sales\Repositories\OrderRepository;

class RazorpayWebhookController extends Controller
{
    public function handle(
        Request $request,
        OrderRepository $orderRepository
    ) {
        Log::info('RAZORPAY WEBHOOK HIT', [
            'event' => $request->input('event'),
        ]);

        // 🔐 Verify webhook signature
        $payload   = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');

        $secret = core()->getConfigData(
            'sales.payment_methods.razorpay.webhook_secret'
        );

        if (! $this->verifySignature($payload, $signature, $secret)) {
            Log::warning('Razorpay webhook signature mismatch');
            return response()->json(['status' => 'invalid signature'], 400);
        }

        // We ONLY care about captured payments
        if ($request->input('event') !== 'payment.captured') {
            return response()->json(['status' => 'ignored'], 200);
        }

        $payment = $request->input('payload.payment.entity');

        $razorpayOrderId   = $payment['order_id'] ?? null;
        $razorpayPaymentId = $payment['id'] ?? null;

        if (! $razorpayOrderId || ! $razorpayPaymentId) {
            Log::error('Webhook missing Razorpay identifiers', $payment);
            return response()->json(['status' => 'missing data'], 400);
        }

        // 🔁 Idempotency: do not process twice
        $orderPayment = OrderPayment::where(
            'additional->razorpay_order_id',
            $razorpayOrderId
        )->first();

        if (! $orderPayment) {
            Log::warning('Order payment not found for Razorpay order', [
                'razorpay_order_id' => $razorpayOrderId,
            ]);

            return response()->json(['status' => 'order not found'], 200);
        }

        // ✅ Update order status to processing
        $orderRepository->update(
            ['status' => 'processing'],
            $orderPayment->order_id
        );

        Log::info('Order marked as processing via Razorpay webhook', [
            'order_id' => $orderPayment->order_id,
            'razorpay_order_id' => $razorpayOrderId,
        ]);

        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * Verify Razorpay webhook signature
     */
    protected function verifySignature(
        string $payload,
        ?string $signature,
        ?string $secret
    ): bool {
        if (! $signature || ! $secret) {
            return false;
        }

        $expected = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $signature);
    }
}
