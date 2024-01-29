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
        Schema::create('group_type_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('groupTypeId')->references('id')->on('group_types')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('languageId')->references('id')->on('languages')->onUpdate('restrict')->onDelete('restrict');
            $table->string('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_type_details');
    }
};
