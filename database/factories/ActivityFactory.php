<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Project;
use App\Models\Tracking;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'project_id' => Project::factory(),
            'tracking_id' => Tracking::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'location' => $this->faker->address(),
            'horas' => $this->faker->time('H:i'),
            'status' => $this->faker->randomElement(['pendiente', 'programado', 'completado']),
            'icon' => $this->faker->randomElement(['🏗️', '🔨', '⚙️', '🏭', '🚧']),
            'image' => null, // Null por defecto, se puede sobrescribir con un array JSON
            'comments' => $this->faker->sentence(),
            'fecha_creacion' => $this->faker->date(),
        ];
    }
}
