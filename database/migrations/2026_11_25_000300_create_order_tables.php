<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            // One key per checkout attempt: a repeated submit maps to the same order
            // and the same gateway session, so the customer can never pay twice.
            $table->string('idempotency_key')->unique();
            $table->string('status')->default('pending'); // pending|paid|partially_refunded|refunded|canceled
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('locale', 5)->default('en');
            $table->string('currency', 3)->default('usd');
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('total');
            $table->unsignedInteger('refunded_amount')->default(0);
            $table->string('gateway')->default('stripe');
            $table->string('gateway_session_id')->nullable()->unique();
            $table->string('gateway_payment_id')->nullable()->index();
            $table->text('checkout_url')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->unsignedInteger('unit_price');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('line_total');
            $table->timestamps();
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('amount');
            $table->string('reason')->nullable();
            $table->string('status')->default('pending'); // pending|succeeded|failed
            $table->string('gateway_refund_id')->nullable()->unique();
            $table->timestamps();
        });

        // Every webhook event id is stored once; replays from the gateway are ignored.
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('gateway');
            $table->string('event_id');
            $table->string('type');
            $table->json('payload');
            $table->string('status')->default('received'); // received|processed|ignored|failed
            $table->text('error')->nullable();
            $table->timestamps();
            $table->unique(['gateway', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
