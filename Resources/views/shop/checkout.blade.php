<!DOCTYPE html>
<html>
<head>
    <title>Razorpay</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    
     <style>
        body {
            margin: 0;
            height: 100vh;
            background: grey);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .payment-box {
            background: white;
            border-radius: 14px;
            padding: 32px 36px;
            width: 320px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.55);
            animation: slideIn 0.6s ease;
        }

        .logo {
            margin-bottom: 18px;
        }

        .logo img {
            height: 34px;
        }

        .spinner {
            width: 56px;
            height: 56px;
            border: 5px solid rgba(255, 255, 255, 0.15);
            border-top: 5px solid #3399cc;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 18px auto;
        }

        .title {
            color: black;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .subtitle {
            color: #94a3b8;
            font-size: 13px;
            line-height: 1.4;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    
    
</head>
<body>
    
    
    
     <!-- Animation Box -->
    <div class="payment-box">
        <div class="logo">
            <!-- Use Razorpay logo or your own -->
            <img src="https://razorpay.com/assets/razorpay-logo.svg" alt="Razorpay">
        </div>

        <div class="spinner"></div>

        <div class="title">Redirecting to Razorpay</div>
        <div class="subtitle">
            Please wait while we open the secure payment window
        </div>
    </div>
    
    
    
    
<script>
    var options = {
        "key": "{{ $key }}",
        "amount": "{{ $amount }}",
        "currency": "INR",
        "name": "{{ $name }}",
        "order_id": "{{ $order['id'] }}",
        "handler": function (response) {
            window.location.href =
                "{{ route('razorpay.callback') }}?payment_id=" +
                response.razorpay_payment_id +
                "&order_id=" +
                response.razorpay_order_id +
                "&signature=" +
                response.razorpay_signature;
        }
    };

    var rzp = new Razorpay(options);
    rzp.open();
</script>
</body>
</html>
