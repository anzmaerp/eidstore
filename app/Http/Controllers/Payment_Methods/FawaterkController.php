<?php

namespace App\Http\Controllers\Payment_Methods;

use App\Models\PaymentRequest;
use App\Models\User;
use App\Traits\Processor;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Services\FawaterkPaymentService;

class FawaterkController extends Controller
{
    use Processor;

    private PaymentRequest $payment;
    private User $user;
    private FawaterkPaymentService $driver;

    public function __construct(PaymentRequest $payment, User $user, FawaterkPaymentService $driver)
    {
        $this->payment = $payment;
        $this->user = $user;
        $this->driver = $driver;

        Log::info("FawaterkController initialized");
    }

    public function index(Request $request)
    {
        Log::info("FawaterkController@index called", $request->all());

        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            Log::error("Validation failed in index", $validator->errors()->toArray());
            return response()->json(
                $this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)),
                400
            );
        }

        $data = $this->payment::where([
            'id' => $request['payment_id'],
            'is_paid' => 0
        ])->first();

        if (!$data) {
            Log::warning("Payment not found or already paid", ['payment_id' => $request['payment_id']]);
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }

        Log::info("Payment data found", $data->toArray());

        $payer = json_decode($data['payer_information'], true);

        $cartItems = [[
            'name'     => 'Order #' . $data->id,
            'price'    => round($data['payment_amount'], 2),
            'quantity' => 1,
        ]];

        $keys = $this->driver->getKeys();

        Log::info("Returning iframe view with data", [
            'payment_id' => $data->id,
            'amount'     => $data['payment_amount'],
            'currency'   => $data['currency_code'],
        ]);

        return view('payment.fawaterk-iframe', [
            'envType' => $keys['mode'] ?? 'test',
            'hashKey'   => $this->driver->generateHashKey(),
            'apiKey'    => $this->driver->getKeys()['vendor_key'],
            'amount'   => round($data['payment_amount'], 2),
            'currency' => $data['currency_code'],
            'customer' => [
                'first_name' => $payer['name'] ?? '',
                'last_name'  => '',
                'email'      => !empty($payer['email']) ? $payer['email'] : 'example@example.com',
                'phone'      => $payer['phone'] ?? '0000000000',
            ],
            'cartItems' => $cartItems,
            'user_id'   => $data->customer_id ?? auth()->id(),
        ]);
    }

public function success(Request $request)
{
    Log::info("FawaterkController@success called", $request->all());

    $invoiceId = $request->invoice_id;

    $payment = PaymentRequest::where('transaction_id', $invoiceId)
        ->orWhereRaw("JSON_EXTRACT(additional_data, '$.fawaterk_invoice_id') = ?", [$invoiceId])
        ->first();

    if (!$payment) {
        Log::error("Payment not found in success()", ['invoice_id' => $invoiceId]);
        return response()->json([
            'status'  => 'error',
            'message' => 'Payment not found',
        ], 404);
    }

    // Call Processor with correct payment_id
    $processorResponse = $this->handlePaymentResponse((object)[
        'payment_id' => $payment->id,       // internal UUID
        'invoice_id' => $invoiceId,
    ]);

    Log::info("Processor response in success", $processorResponse);

    if ($processorResponse['status'] === 'error') {
        return response()->json($processorResponse, 400);
    }

    return $this->payment_response($payment, 'success');
}




    public function failed(Request $request): Application|JsonResponse|Redirector|RedirectResponse
    {
        Log::error("FawaterkController@failed called", $request->all());

        $payment_data = $this->payment::find($request['payment_id']);
        if ($payment_data && function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }

        return $this->payment_response($payment_data, 'fail');
    }

    public function canceled(Request $request): Application|JsonResponse|Redirector|RedirectResponse
    {
        Log::warning("FawaterkController@canceled called", $request->all());

        $payment_data = $this->payment::find($request['payment_id']);
        if ($payment_data && function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }

        return $this->payment_response($payment_data, 'cancel');
    }
}
