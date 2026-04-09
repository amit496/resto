<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->after('remember_token');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('stripe_checkout_session_id')->nullable()->after('gateway_payload');
            $table->string('stripe_payment_intent_id')->nullable()->after('stripe_checkout_session_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['stripe_checkout_session_id', 'stripe_payment_intent_id']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('stripe_customer_id');
        });
    }
};
