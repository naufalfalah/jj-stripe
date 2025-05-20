<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;

class StripeSubscriptionController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function createSubscription(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|string',
            'price_id' => 'required|string',
        ]);

        $subscription = $this->stripeService->createSubscription($request->customer_id, $request->price_id);
        if ($subscription->status !== 'active') {
            return response()->json(['error' => 'Subscription creation failed'], 400);
        }

        return response()->json($subscription);
    }

    public function cancelSubscription(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|string',
        ]);

        $subscription = $this->stripeService->cancelSubscription($request->subscription_id);
        if ($subscription->status !== 'canceled') {
            return response()->json(['error' => 'Subscription cancellation failed'], 400);
        }

        return response()->json($subscription);
    }
}