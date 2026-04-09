<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('food_orders', function (Blueprint $table) {
            $table->string('order_source', 30)->default('online')->after('order_type');
            $table->string('bill_no')->nullable()->after('notes');
            $table->string('bill_status', 30)->default('unbilled')->after('bill_no');
            $table->timestamp('billed_at')->nullable()->after('bill_status');
            $table->foreignId('created_by')->nullable()->after('customer_id')->constrained('users')->nullOnDelete();
            $table->foreignId('billed_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('food_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('billed_by');
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn(['order_source', 'bill_no', 'bill_status', 'billed_at']);
        });
    }
};
