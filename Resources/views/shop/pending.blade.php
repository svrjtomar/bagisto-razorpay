<x-shop::layouts>
    <x-slot:title>
        Payment Processing
    </x-slot>

    <div style="text-align:center; padding:40px;">
        <h2>Payment Successful 🎉</h2>
        <p>Your payment was received.</p>
        <p>Please wait while we confirm your order…</p>

        <script>
            setTimeout(function () {
                window.location.href = "{{ route('shop.checkout.onepage.success') }}";
            }, 5000);
        </script>
    </div>
</x-shop::layouts>
