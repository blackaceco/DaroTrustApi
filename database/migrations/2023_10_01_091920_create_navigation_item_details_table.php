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
        Schema::create('navigation_item_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itemId')->references('id')->on('navigation_items')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('languageId')->references('id')->on('languages')->onUpdate('restrict')->onDelete('restrict');
            $table->string('valueType');
            $table->string('key');
            $table->string('value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigation_item_details');
    }
};
