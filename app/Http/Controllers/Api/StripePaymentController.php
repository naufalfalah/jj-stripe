<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;

class StripePaymentController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer',
            'currency' => 'required|string',
            'customer_id' => 'nullable|string',
        ]);

        $paymentIntent = $this->stripeService->createPaymentIntent($request->amount, $request->currency, $request->customer_id);

        return response()->json($paymentIntent);
    }

    public function confirmPaymentIntent(Request $request, $paymentIntentId)
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $paymentIntent = $this->stripeService->confirmPaymentIntent($paymentIntentId, $request->payment_method);

        return response()->json($paymentIntent);
    }

    public function getPaymentIntent(Request $request, $paymentIntentId)
    {
        $paymentIntent = $this->stripeService->getPaymentIntent($paymentIntentId);

        return response()->json($paymentIntent);
    }

    public function cancelPaymentIntent(Request $request, $paymentIntentId)
    {
        $paymentIntent = $this->stripeService->cancelPaymentIntent($paymentIntentId);

        return response()->json($paymentIntent);
    }
}
