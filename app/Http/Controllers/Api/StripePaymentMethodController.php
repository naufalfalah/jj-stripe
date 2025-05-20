<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;

class StripePaymentMethodController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function createPaymentMethod(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'card' => 'required|array',
            'billing_details' => 'nullable|array',
        ]);

        $paymentMethod = $this->stripeService->createPaymentMethod(
            $request->type,
            $request->card,
            $request->billing_details
        );

        return response()->json($paymentMethod);
    }

    public function getPaymentMethods(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|string',
            'type' => 'required|string',
            'limit' => 'integer|min:1|max:100',
            'starting_after' => 'nullable|string',
            'ending_before' => 'nullable|string',
        ]);

        $paymentMethods = $this->stripeService->getPaymentMethods(
            $request->customer_id,
            $request->type,
            $request->get('limit', 10),
            $request->get('starting_after'),
            $request->get('ending_before')
        );

        return response()->json($paymentMethods);
    }

    public function getPaymentMethod(Request $request, $paymentMethodId)
    {
        $paymentMethod = $this->stripeService->getPaymentMethod($paymentMethodId);

        return response()->json($paymentMethod);
    }

    public function updatePaymentMethod(Request $request, $paymentMethodId)
    {
        $request->validate([
            'billing_details' => 'nullable|array',
        ]);

        $paymentMethod = $this->stripeService->updatePaymentMethod(
            $paymentMethodId,
            $request->billing_details
        );

        return response()->json($paymentMethod);
    }

    public function deletePaymentMethod(Request $request, $paymentMethodId)
    {
        $this->stripeService->deletePaymentMethod($paymentMethodId);

        return response()->json(['message' => 'Payment method deleted successfully']);
    }
}
