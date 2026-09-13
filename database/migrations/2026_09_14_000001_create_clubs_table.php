<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_name', 12)->nullable();
            $table->string('tagline')->nullable();
            $table->string('category')->default('technical');
            $table->text('about')->nullable();
            $table->text('mission')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('cover_path')->nullable();
            $table->string('accent_color', 7)->default('#F2762E');
            $table->unsignedSmallInteger('founded_year')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('faculty_advisor')->nullable();
            $table->string('status')->default('pending'); // pending | approved | rejected
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::table('coordinators', function (Blueprint $table) {
            $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete();
            $table->string('position')->nullable();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('club_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('mode')->default('offline'); // offline | online | hybrid
            $table->string('external_registration_url')->nullable();
            $table->text('recap')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('department')->nullable();
            $table->unsignedTinyInteger('year_of_study')->nullable();
            $table->string('roll_number')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['department', 'year_of_study', 'roll_number']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('club_id');
            $table->dropColumn(['mode', 'external_registration_url', 'recap']);
        });

        Schema::table('coordinators', function (Blueprint $table) {
            $table->dropConstrainedForeignId('club_id');
            $table->dropColumn('position');
        });

        Schema::dropIfExists('clubs');
    }
};
