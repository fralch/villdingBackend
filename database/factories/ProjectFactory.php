<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectType;
use App\Models\ProjectSubtype;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'location' => $this->faker->city() . ', ' . $this->faker->country(),
            'company' => $this->faker->company(),
            'code' => strtoupper($this->faker->unique()->bothify('??####')),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'nearest_monday' => $this->faker->date(),
            'uri' => null, // Null por defecto, se puede sobrescribir
            'project_type_id' => ProjectType::factory(),
            'project_subtype_id' => ProjectSubtype::factory(),
        ];
    }
}
