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
        Schema::create('contact_us_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('websiteId')->references('id')->on('websites')->onUpdate('cascade')->onDelete('cascade');
            $table->string('subject');
            $table->string('name');
            $table->string('email');
            // $table->string('phone');
            $table->text('message');
            $table->string('ipAddress');
            $table->string('status')->default('pending');   // pending , seen , accepted , rejected
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_us_forms');
    }
};
