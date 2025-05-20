<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;

class StripeCustomerController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

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
}
