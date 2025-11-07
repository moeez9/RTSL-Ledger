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
        Schema::create('relation_ledger_request', function (Blueprint $table) {
            $table->id();

            // Businesses involved (seller and buyer)
            $table->foreignId('seller_business_user_id')->constrained('business_users')->restrictOnDelete();
            $table->foreignId('buyer_business_user_id')->constrained('business_users')->restrictOnDelete();

            // Participants (seller and buyer) → also from business_types
            $table->foreignId('seller_id')->constrained('business_users')->restrictOnDelete();
            $table->foreignId('buyer_id')->constrained('business_users')->restrictOnDelete();

            $table->enum('status', ['pending', 'accepted', 'cancelled'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relation_ledger_requests');
    }
};
