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
        Schema::create('scraped_data_images', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->foreignId('scraped_data_id')->constrained('scraped_data')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scraped_data_images', function(Blueprint $table) {
            $table->dropForeign(['scraped_data_id']);
        });
        Schema::dropIfExists('scraped_data_images');
    }
};
