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
use Illuminate\Http\Response;

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

        $invoice = $this->driver->createInvoice($data, $payer, $cartItems);

        if (!empty($invoice['error'])) {
            Log::error("Fawaterk invoice creation failed", ['message' => $invoice['message']]);
            return response()->json(['error' => true, 'message' => $invoice['message']], 400);
        }

        $data->transaction_id = $invoice['invoice_id'];
        $data->additional_data = json_encode(array_merge(
            json_decode($data->additional_data ?? '{}', true),
            ['fawaterk_invoice_id' => $invoice['invoice_id']]
        ));
        $data->save();

        Log::info("Fawaterk transaction_id saved", [
            'payment_id' => $data->id,
            'transaction_id' => $invoice['invoice_id']
        ]);

        return view('payment.fawaterk-iframe', [
            'payment' => $data, // Pass the PaymentRequest instance as $payment
            'envType' => $this->driver->getKeys()['mode'] ?? 'test',
            'hashKey' => $this->driver->generateHashKey(),
            'apiKey' => $this->driver->getKeys()['vendor_key'],
            'amount' => round($data['payment_amount'], 2),
            'currency' => $data['currency_code'],
            'customer' => [
                'first_name' => $payer['name'] ?? '',
                'last_name'  => '',
                'email'      => $payer['email'] ?? 'example@example.com',
                'phone'      => $payer['phone'] ?? '0000000000',
            ],
            'cartItems' => $cartItems,
            'user_id' => $data->customer_id ?? auth()->id(),
            'payment_url' => $invoice['payment_url'] ?? '#',
            'invoice_id' => $invoice['invoice_id'] // Pass invoice_id for the template
        ]);
    }

    public function success(Request $request): Application|JsonResponse|Redirector|RedirectResponse|Response
    {
        Log::info("FawaterkController@success called", ['data' => $request->all()]);

        $invoiceId = $request->invoice_id ?? null;

        if (!$invoiceId) {
            Log::error("Missing invoice_id in Fawaterk success callback");
            return response()->json([
                'status'  => 'error',
                'message' => 'Missing invoice_id parameter',
            ], 400);
        }

        $payment = PaymentRequest::where('transaction_id', $invoiceId)->first();

        if (!$payment) {
            $payment = PaymentRequest::latest('id')->first();
            Log::warning("Payment not found for invoice_id, fallback to latest record", [
                'invoice_id' => $invoiceId,
                'fallback_payment_id' => $payment?->id,
            ]);
        }

        if (!$payment) {
            Log::error("No payment record found at all for invoice_id {$invoiceId}");
            return response()->json([
                'status'  => 'error',
                'message' => 'No payment record found',
            ], 404);
        }

        $payment->update([
            'is_paid'        => 1,
            'payment_method' => 'fawaterk',
            'transaction_id' => $invoiceId,
        ]);

        if (isset($payment) && function_exists($payment->success_hook)) {
            call_user_func($payment->success_hook, $payment);
        }

        Log::info("Fawaterk payment success processed", [
            'payment_id' => $payment->id,
            'invoice_id' => $invoiceId,
        ]);

        if (in_array($payment->payment_platform, ['web', 'app']) && $payment['external_redirect_link']) {
            $redirectUrl = $payment['external_redirect_link'] . '?flag=success&&token=' . base64_encode(
                'payment_method=' . $payment->payment_method . '&&transaction_reference=' . $payment->transaction_id
            );

            return response()->make("<script>window.top.location.href='{$redirectUrl}';</script>");
        }

        return $this->payment_response($payment, 'success');
    }

    public function failed(Request $request): Application|JsonResponse|Redirector|RedirectResponse|Response
    {
        Log::error("FawaterkController@failed called", $request->all());

        $payment_data = $this->payment::find($request['payment_id']);
        if ($payment_data && function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }

        if ($payment_data && in_array($payment_data->payment_platform, ['web', 'app']) && $payment_data['external_redirect_link']) {
            $redirectUrl = $payment_data['external_redirect_link'] . '?flag=fail&&token=' . base64_encode(
                'payment_method=' . $payment_data->payment_method . '&&transaction_reference=' . $payment_data->transaction_id
            );

            return response()->make("<script>window.top.location.href='{$redirectUrl}';</script>");
        }

        return $this->payment_response($payment_data, 'fail');
    }

    public function canceled(Request $request): Application|JsonResponse|Redirector|RedirectResponse|Response
    {
        Log::warning("FawaterkController@canceled called", $request->all());

        $payment_data = $this->payment::find($request['payment_id']);
        if ($payment_data && function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }

        if ($payment_data && in_array($payment_data->payment_platform, ['web', 'app']) && $payment_data['external_redirect_link']) {
            $redirectUrl = $payment_data['external_redirect_link'] . '?flag=cancel&&token=' . base64_encode(
                'payment_method=' . $payment_data->payment_method . '&&transaction_reference=' . $payment_data->transaction_id
            );

            return response()->make("<script>window.top.location.href='{$redirectUrl}';</script>");
        }

        return $this->payment_response($payment_data, 'cancel');
    }

    public function pending(Request $request): Application|JsonResponse|Redirector|RedirectResponse|Response
    {
        Log::info("FawaterkController@pending called", $request->all());

        $payment_data = $this->payment::find($request['payment_id']);
        if ($payment_data && function_exists($payment_data->pending_hook)) {
            call_user_func($payment_data->pending_hook, $payment_data);
        }

        if ($payment_data && in_array($payment_data->payment_platform, ['web', 'app']) && $payment_data['external_redirect_link']) {
            $redirectUrl = $payment_data['external_redirect_link'] . '?flag=pending&&token=' . base64_encode(
                'payment_method=' . $payment_data->payment_method . '&&transaction_reference=' . $payment_data->transaction_id
            );

            return response()->make("<script>window.top.location.href='{$redirectUrl}';</script>");
        }

        return $this->payment_response($payment_data, 'pending');
    }
}
