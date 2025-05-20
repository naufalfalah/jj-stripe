<?php

namespace App\Services;

use Stripe\Customer;
use Stripe\Invoice;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;
use Stripe\Subscription;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a new customer
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
     * @return Customer
     */
    public function getCustomers(int $limit, string $startingAfter, string $endingBefore)
    {
        return Customer::all([
            'limit' => $limit,
            'starting_after' => $startingAfter,
            'ending_before' => $endingBefore,
        ]);
    }

    /**
     * Retrieve a customer
     */
    public function getCustomer(string $customerId): Customer
    {
        return Customer::retrieve($customerId);
    }

    /**
     * Update a customer
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
     */
    public function deleteCustomer(string $customerId): Customer
    {
        $customer = Customer::retrieve($customerId);

        return $customer->delete();
    }

    /**
     * Create a subscription
     *
     * @return Subscription
     */
    public function createSubscription(string $customerId, string $priceId)
    {
        return Subscription::create([
            'customer' => $customerId,
            'items' => [
                ['price' => $priceId],
            ],
        ]);
    }

    /**
     * Retrieve subscriptions
     *
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
     */
    public function getSubscription(string $subscriptionId): Subscription
    {
        return Subscription::retrieve($subscriptionId);
    }

    /**
     * Cancel a subscription
     *
     * @return Subscription
     */
    public function cancelSubscription(string $subscriptionId)
    {
        $subscription = Subscription::retrieve($subscriptionId);

        return $subscription->cancel();
    }

    /**
     * Create a payment intent
     */
    public function createPaymentIntent(int $amount, string $currency = 'usd', ?string $customerId = null): PaymentIntent
    {
        return PaymentIntent::create([
            'amount' => $amount,
            'currency' => $currency,
            'customer' => $customerId,
        ]);
    }

    /**
     * Retrieve a payment intent
     */
    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        return PaymentIntent::retrieve($paymentIntentId);
    }

    /**
     * Create a payment method
     *
     * @return PaymentMethod
     */
    public function createPaymentMethod(string $type, array $card, ?array $billingDetails = null)
    {
        return PaymentMethod::create([
            'type' => $type,
            'card' => $card,
            'billing_details' => $billingDetails,
        ]);
    }

    /**
     * Retrieve payment methods
     *
     * @return PaymentMethod
     */
    public function getPaymentMethods(string $customerId, string $type, int $limit = 10, ?string $startingAfter = null, ?string $endingBefore = null)
    {
        return PaymentMethod::all([
            'customer' => $customerId,
            'type' => $type,
            'limit' => $limit,
            'starting_after' => $startingAfter,
            'ending_before' => $endingBefore,
        ]);
    }

    /**
     * Retrieve a payment method
     */
    public function getPaymentMethod(string $paymentMethodId): PaymentMethod
    {
        return PaymentMethod::retrieve($paymentMethodId);
    }

    /**
     * Update a payment method
     */
    public function updatePaymentMethod(string $paymentMethodId, array $billingDetails): PaymentMethod
    {
        $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
        $paymentMethod->billing_details = $billingDetails;

        return $paymentMethod->save();
    }

    /**
     * Delete a payment method
     */
    public function deletePaymentMethod(string $paymentMethodId): PaymentMethod
    {
        $paymentMethod = PaymentMethod::retrieve($paymentMethodId);

        return $paymentMethod->detach();
    }

    /**
     * Create an invoice
     *
     * @return Invoice
     */
    public function createInvoice(string $customerId, string $subscriptionId)
    {
        return Invoice::create([
            'customer' => $customerId,
            'subscription' => $subscriptionId,
        ]);
    }

    /**
     * Retrieve invoices
     *
     * @return Invoice
     */
    public function getInvoices(int $limit = 10, ?string $startingAfter = null, ?string $endingBefore = null)
    {
        return Invoice::all([
            'limit' => $limit,
            'starting_after' => $startingAfter,
            'ending_before' => $endingBefore,
        ]);
    }

    /**
     * Retrieve an invoice
     */
    public function getInvoice(string $invoiceId): Invoice
    {
        return Invoice::retrieve($invoiceId);
    }

    /**
     * Pay an invoice
     */
    public function payInvoice(string $invoiceId, string $paymentMethodId): Invoice
    {
        $invoice = Invoice::retrieve($invoiceId);
        $invoice->pay(['payment_method' => $paymentMethodId]);

        return $invoice;
    }

    /**
     * Send an invoice
     */
    public function sendInvoice(string $invoiceId): Invoice
    {
        $invoice = Invoice::retrieve($invoiceId);
        $invoice->send();

        return $invoice;
    }

    /**
     * Create a product
     *
     * @return Product
     */
    public function createProduct(string $name, ?string $description = null)
    {
        return Product::create([
            'name' => $name,
            'description' => $description,
        ]);
    }

    /**
     * Retrieve products
     *
     * @return Product
     */
    public function getProducts(int $limit = 10, ?string $startingAfter = null, ?string $endingBefore = null)
    {
        return Product::all([
            'limit' => $limit,
            'starting_after' => $startingAfter,
            'ending_before' => $endingBefore,
        ]);
    }

    /**
     * Retrieve a product
     */
    public function getProduct(string $productId): Product
    {
        return Product::retrieve($productId);
    }

    /**
     * Update a product
     */
    public function updateProduct(string $productId, string $name, ?string $description = null): Product
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
     */
    public function deleteProduct(string $productId): Product
    {
        $product = Product::retrieve($productId);

        return $product->delete();
    }

    /**
     * Create a price for a product
     *
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
     */
    public function getPrice(string $priceId): Price
    {
        return Price::retrieve($priceId);
    }

    /**
     * Update a price
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
     */
    public function deletePrice(string $priceId): Price
    {
        $price = Price::retrieve($priceId);

        return $price->delete();
    }
}
