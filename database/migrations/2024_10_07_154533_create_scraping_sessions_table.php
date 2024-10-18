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
        Schema::create('scraping_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retailer_id')->constrained()->onDelete('cascade');
            $table->foreignId('status_id')->constrained('session_statuses')->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('ended_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scraping_sessions');
    }
};
