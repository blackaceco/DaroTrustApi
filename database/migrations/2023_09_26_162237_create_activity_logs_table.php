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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adminId')->references('id')->on('admins')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('websiteId')->nullable()->references('id')->on('websites')->onUpdate('cascade')->onDelete('cascade');
            $table->string('ipAddress');
            $table->foreignId('entityId')->nullable();   // didn't reference anything because it can save id of multiple entities
            $table->string('entity')->nullable();
            $table->string('action');   // ENUM:: create, update, delete
            $table->text('oldValue')->nullable();
            $table->text('newValue')->nullable();
            $table->timestamp('createdAt')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
