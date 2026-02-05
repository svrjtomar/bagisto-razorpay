<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>

    <style>
        body {
            margin: 0;
            height: 100vh;
            background: linear-gradient(135deg, #020617, #0f172a);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            color: #fff;
        }

        .success-box {
            background: #0b1220;
            border-radius: 16px;
            padding: 36px 42px;
            width: 360px;
            text-align: center;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.6);
            animation: scaleIn 0.6s ease;
        }

        .checkmark {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            animation: pop 0.4s ease 0.4s both;
        }

        .checkmark svg {
            width: 36px;
            height: 36px;
            stroke: #ffffff;
            stroke-width: 3;
            fill: none;
        }

        .title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .subtitle {
            font-size: 14px;
            color: #94a3b8;
            margin-bottom: 22px;
            line-height: 1.5;
        }

        .actions a {
            display: inline-block;
            padding: 12px 22px;
            border-radius: 8px;
            background: #3399cc;
            color: #ffffff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.2s ease;
        }

        .actions a:hover {
            background: #2b85b1;
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.96);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes pop {
            from {
                transform: scale(0.4);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>

<body>

    <div class="success-box">
        <div class="checkmark">
            <svg viewBox="0 0 24 24">
                <path d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <div class="title">Payment Successful</div>

        <div class="subtitle">
            Thank you for your order.<br>
            Your payment has been received successfully.
        </div>

        <div class="actions">
            <a href="{{ route('shop.customer.orders.index') }}">
                View My Orders
            </a>
        </div>
    </div>

</body>
</html>
