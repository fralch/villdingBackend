<?php

namespace Database\Factories;

use App\Models\ProjectType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectTypeFactory extends Factory
{
    protected $model = ProjectType::class;

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
            'name' => 'Tipo Proyecto ' . uniqid() . '_' . $counter,
        ];
    }
}
