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
        $table->string('title');
        $table->string('category'); // arts, sports, jam...
        $table->text('description')->nullable();
        $table->dateTime('starts_at');
        $table->string('venue');
        $table->unsignedInteger('capacity')->nullable(); // max seats
        $table->dateTime('registration_deadline')->nullable();
        $table->boolean('is_published')->default(true);
        $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('events');
}

};
