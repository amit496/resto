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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('image')->nullable()->after('slug');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->json('images')->nullable()->after('description');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('image')->nullable()->after('email');
        });

        Schema::table('delivery_boys', function (Blueprint $table) {
            $table->string('image')->nullable()->after('vehicle_no');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_image')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('images');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('delivery_boys', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_image');
        });
    }
};
