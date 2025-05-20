<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;

class StripeProductController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function createProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $product = $this->stripeService->createProduct($request->name, $request->description);

        return response()->json($product);
    }

    public function getProducts(Request $request)
    {
        $request->validate([
            'limit' => 'integer|min:1|max:100',
            'starting_after' => 'nullable|string',
            'ending_before' => 'nullable|string',
        ]);

        $products = $this->stripeService->getProducts(
            $request->get('limit', 10),
            $request->get('starting_after'),
            $request->get('ending_before')
        );

        return response()->json($products);
    }

    public function getProduct(Request $request, $productId)
    {
        $product = $this->stripeService->getProduct($productId);

        return response()->json($product);
    }

    public function updateProduct(Request $request, $productId)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $product = $this->stripeService->updateProduct($productId, $request->name, $request->description);

        return response()->json($product);
    }

    public function deleteProduct(Request $request, $productId)
    {
        $this->stripeService->deleteProduct($productId);

        return response()->json(['message' => 'Product deleted successfully']);
    }
}