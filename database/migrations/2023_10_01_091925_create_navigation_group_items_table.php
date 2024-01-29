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
        Schema::create('navigation_group_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('navigationGroupId')->references('id')->on('navigation_groups')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('itemId')->references('id')->on('navigation_items')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigation_group_items');
    }
};
