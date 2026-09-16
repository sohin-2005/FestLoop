<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the real MEC clubs plus the accounts needed to use the app.
     * For fictional demo clubs/events: php artisan db:seed --class=DemoSeeder
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@festloop.test'], [
            'name'     => 'FestLoop Admin',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        User::updateOrCreate(['email' => 'test@example.com'], [
            'name'     => 'Test User',
            'password' => Hash::make('password'),
            'role'     => 'student',
        ]);

        $this->call(MecClubsSeeder::class);
    }
}
