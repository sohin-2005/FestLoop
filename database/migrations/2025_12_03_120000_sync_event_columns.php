<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('name')->nullable()->after('title');
            $table->dateTime('start_time')->nullable()->after('starts_at');
            $table->dateTime('end_time')->nullable()->after('start_time');
            $table->string('location')->nullable()->after('venue');
        });

        // Copy existing data into new columns
        DB::statement('UPDATE events SET name = title, start_time = starts_at, location = venue');
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['name', 'start_time', 'end_time', 'location']);
        });
    }
};
