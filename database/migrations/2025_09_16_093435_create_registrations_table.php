<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  // database/migrations/YYYY_MM_DD_HHMMSS_create_registrations_table.php

  public function up(): void
  {
      Schema::create('registrations', function (Blueprint $table) {
          $table->id();
          $table->foreignId('user_id')->constrained()->cascadeOnDelete();
          $table->foreignId('event_id')->constrained()->cascadeOnDelete();
          $table->string('status')->default('registered'); // or 'cancelled'
          $table->timestamps();
  
          $table->unique(['user_id', 'event_id']); // one registration per user per event
      });
  }
  
  public function down(): void
  {
      Schema::dropIfExists('registrations');
  }
  
};
