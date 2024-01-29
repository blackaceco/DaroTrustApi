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
        Schema::create('schema_feature_group_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schemaFeatureId')->references('id')->on('schema_features')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('groupTypeId')->references('id')->on('group_types')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('multiple')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schema_feature_group_types');
    }
};
