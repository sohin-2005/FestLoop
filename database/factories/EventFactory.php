<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Event> */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 day', '+2 months');

        return [
            'name'        => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'location'    => fake()->streetAddress(),
            'start_time'  => $start,
            'end_time'    => (clone $start)->modify('+2 hours'),
            'category'    => fake()->randomElement(array_keys(Event::CATEGORIES)),
            'mode'        => 'offline',
        ];
    }
}
