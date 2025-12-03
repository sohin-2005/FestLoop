<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('banner_image')->nullable();
            // 'category' already exists on the original create_events_table migration
            $table->text('venue_details')->nullable();
            $table->integer('max_participants')->nullable();
            // 'registration_deadline' already exists on the original create_events_table migration
            $table->boolean('requires_approval')->default(false);
            $table->string('organizer')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('rules')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'banner_image',
                'venue_details',
                'max_participants',
                'requires_approval',
                'organizer',
                'contact_email',
                'contact_phone',
                'rules',
            ]);
        });
    }
};
