<?php

namespace Database\Factories;

use App\Models\Club;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Club> */
class ClubFactory extends Factory
{
    protected $model = Club::class;

    public function definition(): array
    {
        return [
            'name'     => fake()->unique()->company().' Club',
            'category' => fake()->randomElement(array_keys(Club::CATEGORIES)),
            'tagline'  => fake()->sentence(4),
            'about'    => fake()->paragraph(),
            'status'   => 'pending',
        ];
    }
}
