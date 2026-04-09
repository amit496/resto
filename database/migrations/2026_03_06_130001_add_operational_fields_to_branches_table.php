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
        Schema::table('branches', function (Blueprint $table) {
            $table->string('manager_name')->nullable()->after('phone');
            $table->string('manager_phone', 30)->nullable()->after('manager_name');
            $table->string('manager_email')->nullable()->after('manager_phone');
            $table->time('opening_time')->nullable()->after('address');
            $table->time('closing_time')->nullable()->after('opening_time');
            $table->string('weekly_off', 50)->nullable()->after('closing_time');
            $table->decimal('latitude', 10, 7)->nullable()->after('weekly_off');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->decimal('delivery_radius_km', 5, 2)->nullable()->after('longitude');
            $table->string('gst_no', 30)->nullable()->after('delivery_radius_km');
            $table->string('fssai_no', 30)->nullable()->after('gst_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn([
                'manager_name',
                'manager_phone',
                'manager_email',
                'opening_time',
                'closing_time',
                'weekly_off',
                'latitude',
                'longitude',
                'delivery_radius_km',
                'gst_no',
                'fssai_no',
            ]);
        });
    }
};
