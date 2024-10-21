<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->integer('status_code')->default(0);
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();

            // Унікальний індекс для retailer_id і дати
            $table->unique(['retailer_id', DB::raw('DATE(started_at)')], 'retailer_date_unique');
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
