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
        Schema::create('meta_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('metaId')->references('id')->on('metas')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('languageId')->references('id')->on('languages')->onUpdate('restrict')->onDelete('restrict');
            $table->string('title');
            $table->string('websiteName')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->text('keywords')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meta_details');
    }
};
