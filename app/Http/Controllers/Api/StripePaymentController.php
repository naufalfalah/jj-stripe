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
}