<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('provider')->comment('Payment provider (e.g., Stripe, PayPal)');
            $table->string('provider_id')->comment('Unique identifier for the payment method in the provider system');
            $table->string('card_type')->nullable()->comment('Type of card (e.g., Visa, MasterCard)');
            $table->string('card_number')->nullable()->comment('Full card number, should be stored securely or encrypted');
            $table->string('last_four')->nullable()->comment('Last four digits of the card number');
            $table->string('expiry_month')->nullable()->comment('Expiry month of the card');
            $table->string('expiry_year')->nullable()->comment('Expiry year of the card');
            $table->string('billing_address')->nullable()->comment('Billing address associated with the payment method');   
            $table->string('billing_city')->nullable()->comment('Billing city associated with the payment method');
            $table->string('billing_state')->nullable()->comment('Billing state associated with the payment method');
            $table->string('billing_zip')->nullable()->comment('Billing zip code associated with the payment method');
            $table->string('billing_country')->nullable()->comment('Billing country associated with the payment method');
            $table->boolean('is_default')->default(false)->comment('Indicates if this is the default payment method for the user');
            $table->boolean('is_active')->default(true)->comment('Indicates if the payment method is active');
            $table->text('metadata')->nullable()->comment('Additional metadata for the payment method');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_payment_methods');
    }
};
