<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('food_orders', function (Blueprint $table) {
            $table->foreignId('customer_address_id')
                ->nullable()
                ->after('customer_id')
                ->constrained('customer_addresses')
                ->nullOnDelete();

            $table->string('delivery_name', 120)->nullable()->after('customer_address_id');
            $table->string('delivery_phone', 30)->nullable()->after('delivery_name');
            $table->text('delivery_address_line_1')->nullable()->after('delivery_phone');
            $table->text('delivery_address_line_2')->nullable()->after('delivery_address_line_1');
            $table->string('delivery_landmark', 120)->nullable()->after('delivery_address_line_2');
            $table->string('delivery_city', 80)->nullable()->after('delivery_landmark');
            $table->string('delivery_state', 80)->nullable()->after('delivery_city');
            $table->string('delivery_postal_code', 20)->nullable()->after('delivery_state');
            $table->string('delivery_country_code', 2)->nullable()->after('delivery_postal_code');
            $table->text('delivery_instructions')->nullable()->after('delivery_country_code');
        });
    }

    public function down(): void
    {
        Schema::table('food_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_address_id');
            $table->dropColumn([
                'delivery_name',
                'delivery_phone',
                'delivery_address_line_1',
                'delivery_address_line_2',
                'delivery_landmark',
                'delivery_city',
                'delivery_state',
                'delivery_postal_code',
                'delivery_country_code',
                'delivery_instructions',
            ]);
        });
    }
};

