<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Fawaterk Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            background-color: #f9fafb;
            font-family: 'Inter', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .payment-container {
            background: #fff;
            width: 100%;
            max-width: 520px;
            padding: 30px 25px;
            border-radius: 18px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 25px;
        }

        .logo-img {
            max-height: 60px;
            max-width: 200px;
            object-fit: contain;
        }

        h2 {
            text-align: center;
            color: #111827;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .order-summary {
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 15px 18px;
            margin-bottom: 25px;
            color: #374151;
            font-size: 0.95rem;
        }

        .order-summary div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .order-summary div:last-child {
            margin-bottom: 0;
        }

        .order-summary span:last-child {
            font-weight: 600;
            color: #111827;
        }

        #fawaterkDivId {
            margin-top: 25px;
        }

        .footer-note {
            text-align: center;
            color: #6b7280;
            font-size: 0.8rem;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="payment-container">
        <div class="logo-wrapper">
            <img src="{{ json_decode($payment->additional_data)->business_logo ?? 'https://via.placeholder.com/180x60' }}"
                 alt="Business Logo" class="logo-img">
        </div>

        <h2>Complete Your Payment</h2>

        <div class="order-summary">
            <div>
                <span>Order ID:</span>
                <span>#{{ $payment->id }}</span>
            </div>
            <div>
                <span>Total Amount:</span>
                <span>{{ $amount }} {{ $currency }}</span>
            </div>
        </div>

        <div id="fawaterkDivId"></div>

        <p class="footer-note">Secured by Fawaterk – Please don’t close this page while processing.</p>
    </div>

    <script src="https://staging.fawaterk.com/assets_new/vendor/jquery/dist/jquery.min.js"></script>
    <script src="https://staging.fawaterk.com/fawaterkPlugin/fawaterkPlugin.min.js"></script>
    <script>
        const pluginConfig = {
            envType: "{{ $envType }}",
            token: "{{ $apiKey }}",
            style: {
                listing: "horizontal"
            },
            version: "0",
            requestBody: {
                "cartTotal": "{{ $amount }}",
                "currency": "{{ $currency }}",
                "customer": {
                    "customer_unique_id": "{{ $user_id }}",
                    "first_name": "{{ $customer['first_name'] }}",
                    "last_name": "{{ $customer['last_name'] }}",
                    "email": "{{ $customer['email'] }}",
                    "phone": "{{ $customer['phone'] }}"
                },
                redirectionUrls: {
                    successUrl: "{{ url('/payment/fawaterk/success') }}",
                    failUrl: "{{ url('/payment/fawaterk/fail') }}",
                    pendingUrl: "{{ url('/payment/fawaterk/pending') }}"
                },
                cartItems: @json($cartItems),
                "deduct_total_amount": 1,
                "payLoad": {
                    "pl1": "1",
                    "pl2": "2"
                },
            }
        };
        fawaterkCheckout(pluginConfig);
    </script>

</body>

</html>
