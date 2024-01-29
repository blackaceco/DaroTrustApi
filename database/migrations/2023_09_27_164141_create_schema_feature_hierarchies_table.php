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
        Schema::create('schema_feature_hierarchies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parentId')->references('id')->on('schema_features')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('childId')->references('id')->on('schema_features')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schema_feature_hierarchies');
    }
};
