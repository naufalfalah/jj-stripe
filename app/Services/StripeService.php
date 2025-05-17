<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentIntent;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a new customer
     *
     * @param string $email
     * @param string $name
     * @return Customer
     */
    public function createCustomer(string $email, string $name): Customer
    {
        return Customer::create([
            'email' => $email,
            'name' => $name,
        ]);
    }

    /**
     * Create a payment intent
     *
     * @param int $amount
     * @param string $currency
     * @param string|null $customerId
     * @return PaymentIntent
     */
    public function createPaymentIntent(int $amount, string $currency = 'usd', string $customerId = null): PaymentIntent
    {
        return PaymentIntent::create([
            'amount' => $amount,
            'currency' => $currency,
            'customer' => $customerId,
        ]);
    }

    /**
     * Retrieve a payment intent
     *
     * @param string $paymentIntentId
     * @return PaymentIntent
     */
    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        return PaymentIntent::retrieve($paymentIntentId);
    }
}