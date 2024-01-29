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
        Schema::create('navigation_item_hierarchies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parentId')->references('id')->on('navigation_items')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('childId')->references('id')->on('navigation_items')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigation_item_hierarchies');
    }
};
