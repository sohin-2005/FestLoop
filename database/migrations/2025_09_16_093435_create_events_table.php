<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// database/migrations/YYYY_MM_DD_HHMMSS_create_events_table.php
public function up(): void
{
    Schema::create('events', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('category')->nullable();
        $table->text('description')->nullable();
        $table->string('location');
        $table->dateTime('start_time');
        $table->dateTime('end_time')->nullable();
        $table->string('banner_image')->nullable();
        $table->text('venue_details')->nullable();
        $table->unsignedInteger('max_participants')->nullable();
        $table->dateTime('registration_deadline')->nullable();
        $table->boolean('requires_approval')->default(false);
        $table->string('contact_email')->nullable();
        $table->string('contact_phone')->nullable();
        $table->text('rules')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('events');
}


};
