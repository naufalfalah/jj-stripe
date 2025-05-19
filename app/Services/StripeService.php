<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\Price;
use Stripe\Product;
use Stripe\Subscription;

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
     * Retrieve customers
     *
     * @param int $limit
     * @param string $startingAfter
     * @param string $endingBefore
     * @return Customer
     */
    public function getCustomers(int $limit = 10, string $startingAfter, string $endingBefore)
    {
        return Customer::all([
            'limit' => $limit,
            'starting_after' => $startingAfter,
            'ending_before' => $endingBefore,
        ]);
    }

    /**
     * Retrieve a customer
     *
     * @param string $customerId
     * @return Customer
     */
    public function getCustomer(string $customerId): Customer
    {
        return Customer::retrieve($customerId);
    }

    /**
     * Update a customer
     *
     * @param string $customerId
     * @param string $email
     * @param string $name
     * @return Customer
     */
    public function updateCustomer(string $customerId, string $email, string $name): Customer
    {
        $customer = Customer::retrieve($customerId);
        $customer->email = $email;
        $customer->name = $name;
        return $customer->save();
    }

    /**
     * Delete a customer
     *
     * @param string $customerId
     * @return Customer
     */
    public function deleteCustomer(string $customerId): Customer
    {
        $customer = Customer::retrieve($customerId);
        return $customer->delete();
    }

    /**
     * Create a subscription
     * 
     * @param string $customerId
     * @param string $priceId
     * @return Subscription
     */
    public function createSubscription(string $customerId, string $priceId)
    {
        return Subscription::create([
            'customer' => $customerId,
            'items' => [
                ['price' => $priceId]
            ],
        ]);
    }

    /**
     * Retrieve subscriptions
     * 
     * @param string $customerId
     * @return Subscription
     */
    public function getSubscriptions(string $customerId)
    {
        return Subscription::all([
            'customer' => $customerId,
        ]);
    }

    /**
     * Retrieve a subscription
     * 
     * @param string $subscriptionId
     * @return Subscription
     */
    public function getSubscription(string $subscriptionId): Subscription
    {
        return Subscription::retrieve($subscriptionId);
    }

    /**
     * Cancel a subscription
     * 
     * @param string $subscriptionId
     * @return Subscription
     */
    public function cancelSubscription(string $subscriptionId)
    {
        $subscription = Subscription::retrieve($subscriptionId);
        return $subscription->cancel();
    }

    /**
     * Create a product
     *
     * @param string $name
     * @param string|null $description
     * @return Product
     */
    public function createProduct(string $name, string $description = null)
    {
        return Product::create([
            'name' => $name,
            'description' => $description,
        ]);
    }

    /**
     * Retrieve products
     *
     * @param int $limit
     * @param string|null $startingAfter
     * @param string|null $endingBefore
     * @return Product
     */
    public function getProducts(int $limit = 10, string $startingAfter = null, string $endingBefore = null)
    {
        return Product::all([
            'limit' => $limit,
            'starting_after' => $startingAfter,
            'ending_before' => $endingBefore,
        ]);
    }

    /**
     * Retrieve a product
     *
     * @param string $productId
     * @return Product
     */
    public function getProduct(string $productId): Product
    {
        return Product::retrieve($productId);
    }

    /**
     * Update a product
     *
     * @param string $productId
     * @param string $name
     * @param string|null $description
     * @return Product
     */
    public function updateProduct(string $productId, string $name, string $description = null): Product
    {
        $product = Product::retrieve($productId);
        $product->name = $name;
        if ($description) {
            $product->description = $description;
        }
        $product = Product::update($productId, [
            'name' => $name,
            'description' => $description,
        ]);

        return $product;
    }

    /**
     * Delete a product
     *
     * @param string $productId
     * @return Product
     */
    public function deleteProduct(string $productId): Product
    {
        $product = Product::retrieve($productId);
        return $product->delete();
    }

    /**
     * Create a price for a product
     *
     * @param string $productId
     * @param int $unitAmount
     * @param string $currency
     * @return Price
     */
    public function createPrice(string $productId, int $unitAmount, string $currency = 'usd')
    {
        return Price::create([
            'unit_amount' => $unitAmount,
            'currency' => $currency,
            'product' => $productId,
        ]);
    }

    /**
     * Retrieve prices for a product
     *
     * @param string $productId
     * @return Price
     */
    public function getPrices(string $productId)
    {
        return Price::all([
            'product' => $productId,
        ]);
    }

    /**
     * Retrieve a price
     *
     * @param string $priceId
     * @return Price
     */
    public function getPrice(string $priceId): Price
    {
        return Price::retrieve($priceId);
    }

    /**
     * Update a price
     *
     * @param string $priceId
     * @param int $unitAmount
     * @param string $currency
     * @return Price
     */
    public function updatePrice(string $priceId, int $unitAmount, string $currency = 'usd'): Price
    {
        $price = Price::retrieve($priceId);
        $price->unit_amount = $unitAmount;
        $price->currency = $currency;
        return $price->save();
    }

    /**
     * Delete a price
     *
     * @param string $priceId
     * @return Price
     */
    public function deletePrice(string $priceId): Price
    {
        $price = Price::retrieve($priceId);
        return $price->delete();
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