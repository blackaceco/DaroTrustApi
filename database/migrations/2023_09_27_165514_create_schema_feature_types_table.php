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
        Schema::create('schema_feature_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schemaFeatureId')->references('id')->on('schema_features')->onUpdate('restrict')->onDelete('restrict');
            $table->string('valueKey');
            $table->string('valueType');
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schema_feature_types');
    }
};
