<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds Stripe payment tracking and refund status fields to the orders table.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('stripe_checkout_session_id')->nullable()->after('shipping_address_id');
            $table->string('stripe_payment_intent_id')->nullable()->unique()->after('stripe_checkout_session_id');
            $table->enum('refund_status', ['requested', 'refunded'])->nullable()->after('stripe_payment_intent_id');
            $table->timestamp('refunded_at')->nullable()->after('refund_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_checkout_session_id',
                'stripe_payment_intent_id',
                'refund_status',
                'refunded_at',
            ]);
        });
    }
};
