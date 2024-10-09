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
        Schema::create('product_retailer', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('retailer_id')->constrained()->onDelete('cascade');
            $table->primary(['product_id', 'retailer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_retailer', function(Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['retailer_id']);
        });
        Schema::dropIfExists('product_retailer');
    }
};
