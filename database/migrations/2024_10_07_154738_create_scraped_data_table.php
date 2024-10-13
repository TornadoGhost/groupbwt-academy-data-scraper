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
        Schema::create('scraped_data', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('description');
            $table->float('price');
            $table->float('avg_rating')->default(0);
            $table->integer('stars_1')->default(0);
            $table->integer('stars_2')->default(0);
            $table->integer('stars_3')->default(0);
            $table->integer('stars_4')->default(0);
            $table->integer('stars_5')->default(0);
            $table->foreignId('retailer_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('session_id')->constrained('scraping_sessions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scraped_data');
    }
};
