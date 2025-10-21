<?php

namespace Database\Factories;

use App\Models\Tracking;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrackingFactory extends Factory
{
    protected $model = Tracking::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'project_id' => Project::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'date_start' => $this->faker->date(),
            'duration_days' => $this->faker->numberBetween(7, 30),
            'status' => true,
        ];
    }
}
