<?php

namespace Database\Factories;

use App\Models\ProjectSubtype;
use App\Models\ProjectType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectSubtypeFactory extends Factory
{
    protected $model = ProjectSubtype::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array
     */
    public function definition()
    {
        static $counter = 0;
        $counter++;

        return [
            'name' => 'Subtipo Proyecto ' . uniqid() . '_' . $counter,
            'project_type_id' => ProjectType::factory(),
        ];
    }
}
