<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained()->onDelete('cascade');
        $table->enum('method', ['cash_on_delivery', 'credit_card', 'paypal'])->default('cash_on_delivery');
        $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
        $table->string('transaction_id')->nullable(); // لو دفع أونلاين
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
