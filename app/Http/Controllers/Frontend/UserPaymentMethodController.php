<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\UserPaymentMethod;
use Illuminate\Http\Request;

class UserPaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = UserPaymentMethod::where('user_id', auth('web')->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('client.user-payment-method.index', compact('paymentMethods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'card_type' => 'required|string|max:50',
            'card_number' => 'required|string|max:25',
            'expiry_month' => 'required|string|max:2',
            'expiry_year' => 'required|string|max:4',
            'billing_address' => 'string|max:255',
            'billing_city' => 'string|max:100',
            'billing_state' => 'string|max:100',
            'billing_zip' => 'string|max:20',
            'billing_country' => 'string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $validated['user_id'] = auth('web')->id();
        $validated['is_default'] = $request->has('is_default');
        if (!empty($validated['is_default'])) {
            UserPaymentMethod::unsetOtherDefaults($validated['user_id']);
        }

        // Only save last four digits for security
        $validated['last_four'] = substr(preg_replace('/\D/', '', $validated['card_number']), -4);

        // encrypt card_number
        $validated['card_number'] = encrypt($validated['card_number']); 

        $method = UserPaymentMethod::create($validated);

        return redirect()->route('user.user-payment-method.index')->with('success', 'Payment method added successfully.');
    }

    public function update(Request $request, $id)
    {
        $method = UserPaymentMethod::where('user_id', auth('web')->id())->findOrFail($id);

        $validated = $request->validate([
            'card_type' => 'nullable|string|max:50',
            'card_number' => 'nullable|string|max:25',
            'last_four' => 'nullable|string|max:4',
            'expiry_month' => 'required|string|max:2',
            'expiry_year' => 'required|string|max:4',
            'billing_address' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_zip' => 'nullable|string|max:20',
            'billing_country' => 'nullable|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $validated['is_default'] = $request->has('is_default');
        if (!empty($validated['is_default'])) {
            UserPaymentMethod::unsetOtherDefaults(auth('web')->id());
        }

        if (!empty($validated['card_number'])) {
            $validated['last_four'] = substr(preg_replace('/\D/', '', $validated['card_number']), -4);
        } else {
            unset($validated['card_number']);
        }

        $method->update($validated);

        return redirect()->route('user.user-payment-method.index')->with('success', 'Payment method updated successfully.');
    }

    public function destroy($id)
    {
        $method = UserPaymentMethod::where('user_id', auth('web')->id())->findOrFail($id);
        $method->delete();

        return redirect()->route('user.user-payment-method.index')->with('success', 'Payment method deleted successfully.');
    }
}