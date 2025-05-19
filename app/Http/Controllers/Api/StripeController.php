<?php

namespace App\Http\Controllers\Api;

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

    // Customer Management
    public function createCustomer(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
        ]);

        $customer = $this->stripeService->createCustomer($request->email, $request->name);

        return response()->json($customer);
    }

    public function getCustomers(Request $request)
    {
        $request->validate([
            'limit' => 'integer|min:1|max:100',
            'starting_after' => 'nullable|string',
            'ending_before' => 'nullable|string',
        ]);

        $customer = $this->stripeService->getCustomers(
            $request->get('limit', 10),
            $request->get('starting_after'),
            $request->get('ending_before')
        );

        return response()->json($customer);
    }

    public function getCustomer(Request $request, $customerId)
    {
        $customer = $this->stripeService->getCustomer($customerId);

        return response()->json($customer);
    }

    public function updateCustomer(Request $request, $customerId)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
        ]);

        $customer = $this->stripeService->updateCustomer($customerId, $request->email, $request->name);

        return response()->json($customer);
    }

    public function deleteCustomer(Request $request, $customerId)
    {
        $this->stripeService->deleteCustomer($customerId);

        return response()->json(['message' => 'Customer deleted successfully']);
    }

    // Product Management
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

    // Price Management
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

    // Payment Intent Management
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