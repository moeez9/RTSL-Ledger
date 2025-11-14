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
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();

            // Link to the Relation Ledger Request
            $table->foreignId('rlr_id')->constrained('relation_ledger_requests')->restrictOnDelete();

            // Transaction fields
            $table->integer('qty')->default(0);
            $table->string('set')->nullable();
            $table->decimal('rate', 12, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->decimal('debit', 10, 2)->default(0);
            $table->decimal('credit', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);

            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Audit trail (all link to users)
            $table->foreignId('inserted_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->restrictOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
