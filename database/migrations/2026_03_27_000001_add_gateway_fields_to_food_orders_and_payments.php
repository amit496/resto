<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('food_orders', function (Blueprint $table) {
            $table->string('billing_country', 2)->nullable()->after('customer_id');
            $table->string('billing_currency', 10)->nullable()->after('billing_country');
            $table->string('payment_gateway', 50)->nullable()->after('billing_currency');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('gateway_code', 50)->nullable()->after('method');
            $table->string('gateway_country', 2)->nullable()->after('gateway_code');
            $table->json('gateway_payload')->nullable()->after('transaction_ref');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['gateway_code', 'gateway_country', 'gateway_payload']);
        });

        Schema::table('food_orders', function (Blueprint $table) {
            $table->dropColumn(['billing_country', 'billing_currency', 'payment_gateway']);
        });
    }
};
