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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('wam_id')->unique(); // WhatsApp Message ID
            $table->foreignId('contact_id')->constrained('contacts')->onDelete('cascade');
            $table->string('type')->default('text'); // text, image, document, etc.
            $table->text('body')->nullable();
            $table->string('status')->nullable(); // sent, delivered, read, failed
            $table->enum('direction', ['incoming', 'outgoing']);
            $table->json('metadata')->nullable(); // Extra data like media IDs
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
