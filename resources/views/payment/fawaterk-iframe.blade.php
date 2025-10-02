<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>fawaterk payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-color: #f7f7f7;
            font-family: Arial, sans-serif;
            padding: 20px;
            margin: 0;
        }

        .payment-container {
            width: 100%;
            max-width: 600px;
            height: 600;
            margin: 40px auto;
            background: #fff;
            padding: 25px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        #fawaterkDivId {
            margin-top: 20px;
        }

        .logo-wrapper {
            margin-bottom: 15px;
        }

        .logo-img {
            max-height: 50px;
            max-width: 200px;
            object-fit: contain;
        }
    </style>
</head>

<body>

    <div class="payment-container">
        <div class="logo-wrapper">
        </div>

        <h2>complete your payment</h2>

        <div id="fawaterkDivId"></div>
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
                    "phone": "{{ $customer['phone'] }}",
                },
                redirectionUrls: {
                    successUrl: "{{ url('/payment/fawaterk/success') }}",
                    failUrl: "{{ url('/payment/fawaterk/fail') }}",
                    pendingUrl: "{{ url('/payment/fawaterk/pending') }}"
                },
                // cartItems: @json($cartItems),
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
