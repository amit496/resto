<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('app_name')->default('Multi Restaurant System');
            $table->string('app_logo')->nullable();
            $table->string('app_favicon')->nullable();
            $table->string('currency')->default('INR');
            $table->string('currency_symbol')->default('Rs');
            $table->unsignedTinyInteger('decimal_places')->default(2);
            $table->string('timezone')->default('Asia/Calcutta');
            $table->string('order_prefix')->default('ORD');
            $table->boolean('auto_accept_orders')->default(false);
            $table->boolean('allow_scheduled_orders')->default(true);
            $table->boolean('allow_guest_checkout')->default(true);
            $table->decimal('min_order_amount', 10, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('service_charge', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('packing_fee', 10, 2)->default(0);
            $table->unsignedInteger('est_prep_time_min')->default(20);
            $table->unsignedInteger('est_delivery_time_min')->default(40);
            $table->unsignedInteger('max_delivery_km')->default(10);
            $table->boolean('enable_coupons')->default(true);
            $table->boolean('enable_tips')->default(true);
            $table->boolean('enable_kot')->default(true);
            $table->boolean('enable_stock_deduction')->default(true);
            $table->string('support_phone')->nullable();
            $table->string('support_email')->nullable();
            $table->text('invoice_footer')->nullable();
            $table->string('theme_color')->default('#F59E0B');
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
