<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Menambah kolom customer_name
            $table->string('customer_name')->after('user_id');
            // Membuat user_id nullable untuk admin booking
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('customer_name');
            // Kembalikan user_id menjadi not null
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
