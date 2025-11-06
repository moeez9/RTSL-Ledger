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
        Schema::create('request_for_ledger_update', function (Blueprint $table) {
            $table->id();

            // Link to ledger and its parent relationship
            $table->foreignId('rlr_id')->constrained('relation_ledger_request')->restrictOnDelete();
            $table->foreignId('ledger_id')->constrained('ledgers')->restrictOnDelete();

            // Identify who requested — via business_types (not users)
            $table->foreignId('seller_id')->constrained('business_types')->restrictOnDelete();
            $table->foreignId('buyer_id')->constrained('business_types')->restrictOnDelete();

            // Request details
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Audit trail
            $table->foreignId('requested_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->restrictOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_for_ledger_updates');
    }
};
