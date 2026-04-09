<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('food_orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('customer_id')->constrained()->nullOnDelete();
            $table->string('coupon_code')->nullable()->after('coupon_id');
            $table->decimal('service_charge', 10, 2)->default(0)->after('tax_amount');
            $table->decimal('delivery_fee', 10, 2)->default(0)->after('service_charge');
            $table->decimal('packing_fee', 10, 2)->default(0)->after('delivery_fee');
            $table->unsignedInteger('loyalty_points_used')->default(0)->after('discount_amount');
            $table->decimal('loyalty_discount_amount', 10, 2)->default(0)->after('loyalty_points_used');
        });
    }

    public function down(): void
    {
        Schema::table('food_orders', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn([
                'coupon_id',
                'coupon_code',
                'service_charge',
                'delivery_fee',
                'packing_fee',
                'loyalty_points_used',
                'loyalty_discount_amount',
            ]);
        });
    }
};
