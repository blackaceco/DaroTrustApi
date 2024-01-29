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
        Schema::create('schema_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('websiteId')->references('id')->on('websites')->onUpdate('restrict')->onDelete('restrict');
            $table->smallInteger('min');
            $table->smallInteger('max')->nullable();
            $table->string('featureTitle');
            $table->boolean('sortable')->default(false);
            $table->boolean('groupable')->default(false);
            $table->boolean('taggable')->default(false);
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schema_features');
    }
};
