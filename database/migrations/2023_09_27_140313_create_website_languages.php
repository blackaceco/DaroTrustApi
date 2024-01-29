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
        Schema::create('website_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('websiteId')->references('id')->on('websites')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('languageId')->references('id')->on('languages')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('active')->default(false);
            $table->boolean('default')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_languages');
    }
};
