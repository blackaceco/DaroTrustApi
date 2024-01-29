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
        Schema::create('tag_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagId')->references('id')->on('tags')->onUpdate('restrict')->onDelete('restrict');
            $table->foreignId('itemId')->references('id')->on('items')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tag_items');
    }
};
