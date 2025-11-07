<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Factories\HasFactory;

return new class extends Migration
{
    use HasFactory;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ledger_backups', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('rlr_id')->constrained('relation_ledger_request')->restrictOnDelete();
            $table->foreignId('ledger_id')->constrained('ledgers')->restrictOnDelete();
            $table->foreignId('update_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('delete_by')->constrained('users')->restrictOnDelete();

            // Backup data
            $table->json('old_data')->nullable(false);  // not null
            $table->json('new_data')->nullable();  // nullable

            // Type and remarks
            $table->enum('action_type', ['update', 'delete'])->nullable(false);
            $table->text('remarks')->nullable();

            // Timestamp
            $table->timestamp('created_at')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_backups');
    }
};
