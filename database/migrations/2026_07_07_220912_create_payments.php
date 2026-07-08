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
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')
                ->references('id')
                ->on('invoices')
                ->nullOnDelete();
            $table->enum('gateway',[
                'midtrans',
                'xendit'
            ]);
            $table->string('gateway_transaction_id')
                ->nullable();
            $table->string('method');

            $table->string('reference')->unique();
            $table->bigInteger('amount');
            $table->enum('status', [
                'pending',
                'paid',
                'failed',
                'expired',
                'cancelled',
                'refund',
            ])->default('pending');
            $table->string('redirect_url')->nullable();
            $table->index('status');
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
