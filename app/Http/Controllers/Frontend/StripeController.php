<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function createCustomer(Request $request)
    {
        $customer = $this->stripeService->createCustomer($request->email, $request->name);

        return response()->json($customer);
    }

    public function createPaymentIntent(Request $request)
    {
        $paymentIntent = $this->stripeService->createPaymentIntent($request->amount, $request->currency, $request->customer_id);

        return response()->json($paymentIntent);
    }
}