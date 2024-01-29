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
        Schema::create('page_group_schema_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pageGroupId')->references('id')->on('page_groups')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('schemaFeatureId')->references('id')->on('schema_features')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('primary')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_group_schema_items');
    }
};
