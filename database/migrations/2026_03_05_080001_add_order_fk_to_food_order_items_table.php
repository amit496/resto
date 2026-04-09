<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('food_order_items', function (Blueprint $table) {
            $table->foreign('food_order_id')->references('id')->on('food_orders')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('food_order_items', function (Blueprint $table) {
            $table->dropForeign(['food_order_id']);
        });
    }
};
