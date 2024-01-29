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
        Schema::create('item_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itemId')->references('id')->on('items')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('languageId')->references('id')->on('languages')->onUpdate('restrict')->onDelete('restrict');
            $table->string('valueType');
            $table->string('key');
            $table->text('value');
            $table->integer('order')->default(99);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_details');
    }
};
