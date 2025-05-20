<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;

class StripePriceController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function createPrice(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string',
            'unit_amount' => 'required|integer',
            'currency' => 'string|nullable',
            'recurring' => 'nullable|array',
        ]);

        $price = $this->stripeService->createPrice(
            $request->product_id,
            $request->unit_amount,
            $request->currency,
            $request->recurring
        );

        return response()->json($price);
    }

    public function getPrices(Request $request)
    {
        $request->validate([
            'limit' => 'integer|min:1|max:100',
            'starting_after' => 'nullable|string',
            'ending_before' => 'nullable|string',
        ]);

        $prices = $this->stripeService->getPrices(
            $request->get('limit', 10),
            $request->get('starting_after'),
            $request->get('ending_before')
        );

        return response()->json($prices);
    }

    public function getPrice(Request $request, $priceId)
    {
        $price = $this->stripeService->getPrice($priceId);

        return response()->json($price);
    }

    public function updatePrice(Request $request, $priceId)
    {
        $request->validate([
            'unit_amount' => 'required|integer',
            'currency' => 'required|string',
            'recurring' => 'nullable|array',
        ]);

        $price = $this->stripeService->updatePrice(
            $priceId,
            $request->unit_amount,
            $request->currency,
            $request->recurring
        );

        return response()->json($price);
    }

    public function deletePrice(Request $request, $priceId)
    {
        $this->stripeService->deletePrice($priceId);

        return response()->json(['message' => 'Price deleted successfully']);
    }
}
