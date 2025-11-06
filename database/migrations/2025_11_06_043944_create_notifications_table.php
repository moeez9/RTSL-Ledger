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
            $table->foreignId('users_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('sender_id')->nullable()->constrained('users')->restrictOnDelete();

            // Optional link to related entity (ledger, request, etc.)
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('related_table')->nullable(); //  (store table name)

            // Notification details
            $table->string('title', 150);
            $table->text('message')->nullable();
            $table->boolean('is_read')->default(false);

            $table->timestamps();
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
