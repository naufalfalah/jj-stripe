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
        Schema::table('packages', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('url');
            $table->integer('duration')->nullable()->after('logo');
            $table->string('status')->default('active')->after('duration');
            $table->string('stripe_product_id')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('logo');
            $table->dropColumn('duration');
            $table->dropColumn('status');
            $table->dropColumn('stripe_product_id');
        });
    }
};
