{{-- Razorpay Payment Information --}}
@if ($order->payment && $order->payment->method === 'razorpay')
    <div class="box-shadow rounded-lg p-4 mb-4">
        <h3 class="text-lg font-semibold mb-3">
            Razorpay Payment Details
        </h3>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <strong>Payment ID:</strong><br>
                {{ $order->payment->additional['razorpay_payment_id'] ?? 'N/A' }}
            </div>

            <div>
                <strong>Razorpay Order ID:</strong><br>
                {{ $order->payment->additional['razorpay_order_id'] ?? 'N/A' }}
            </div>

            <div>
                <strong>Signature:</strong><br>
                <span style="word-break: break-all;">
                    {{ $order->payment->additional['razorpay_signature'] ?? 'N/A' }}
                </span>
            </div>
        </div>
    </div>
@endif
