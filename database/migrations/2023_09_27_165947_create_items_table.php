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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('websiteId')->references('id')->on('websites')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('schemaId')->references('id')->on('schema_features')->onUpdate('cascade')->onDelete('cascade');
            $table->string('featureTitle');
            $table->integer('order')->default(99);
            $table->boolean('visible')->default(true);
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
