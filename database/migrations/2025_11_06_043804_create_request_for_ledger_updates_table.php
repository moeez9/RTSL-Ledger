<?php
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use SoftDeletes, HasFactory;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('request_for_ledger_updates', function (Blueprint $table) {
            $table->id();

            // Link to ledger and its parent relationship
            $table->foreignId('rlr_id')->constrained('relation_ledger_request')->restrictOnDelete();
            $table->foreignId('ledger_id')->constrained('ledgers')->restrictOnDelete();

            // Identify who requested — via business_users (not users)
            $table->foreignId('seller_id')->constrained('business_users')->restrictOnDelete();
            $table->foreignId('buyer_id')->constrained('business_users')->restrictOnDelete();

            // Request details
            $table->string('reason', 255)->default('no reason provided');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Audit trail
            $table->foreignId('requested_by')->nullable()->constrained('business_users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('business_users')->restrictOnDelete();

            $table->timestamps();
            $table->softDeletes();

             // Indexes for faster queries
            $table->index('rlr_id');
            $table->index('ledger_id');
            $table->index('seller_id');
            $table->index('buyer_id');
            $table->index('reason');
            $table->index('status');
            $table->index('requested_by');
            $table->index('approved_by');
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
