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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // Recipient and sender
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('sender_id')->constrained('users')->restrictOnDelete();

            // Notification details
            $table->string('title', 100)->nullable(false);
            $table->text('message')->nullable(false);
            $table->enum('type', ['ledger', 'feedback', 'bug', 'request', 'update', 'alert'])->nullable(false);
            $table->enum('action_type', ['inserted', 'updated', 'deleted', 'approved'])->default('inserted');

            // Related entity reference
            $table->string('related_table', 50)->nullable();
            $table->integer('related_id')->nullable();

            // Status fields
            $table->boolean('is_read')->default(false);
            $table->enum('status', ['active', 'deleted', 'hidden'])->default('active');

            $table->timestamps();
            $table->timestamp('expired_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
