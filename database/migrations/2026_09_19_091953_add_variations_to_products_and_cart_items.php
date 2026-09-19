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
        Schema::table('products', function (Blueprint $table) {
            $table->json('colors')->nullable();
            $table->json('sizes')->nullable();
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('color')->nullable();
            $table->string('size')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['colors', 'sizes']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn(['color', 'size']);
        });
    }
};
