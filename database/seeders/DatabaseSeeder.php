<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usar ProjectType existente (ya se crean en la migración)
        $projectType = \App\Models\ProjectType::where('name', 'Edificación Urbana')->first();
        
        // Usar ProjectSubtype existente
        $projectSubtype = \App\Models\ProjectSubtype::where('name', 'Edificio Multifamiliar')->first();

        // Crear Project
        $project = \App\Models\Project::create([
            'name' => 'Edificio Los Pinos',
            'location' => 'Lima, Perú',
            'company' => 'Constructora ABC S.A.C.',
            'code' => 'ERP-2025-001',
            'start_date' => '2025-01-15',
            'end_date' => '2025-12-31',
            'project_type_id' => $projectType->id,
            'project_subtype_id' => $projectSubtype->id,
        ]);

        // Crear Tracking
        $tracking = \App\Models\Tracking::create([
            'project_id' => $project->id,
            'title' => 'Seguimiento Octubre 2025',
            'description' => 'Seguimiento de actividades de construcción',
            'date_start' => '2025-10-20',
            'duration_days' => 7,
            'status' => true
        ]);

        // Crear Activities para el 20 de octubre de 2025
        $activities = [
            [
                'name' => 'Excavación de cimientos',
                'description' => 'Excavación de cimientos - Sector A',
                'fecha_creacion' => '2025-10-20',
                'status' => 'finalizado'
            ],
            [
                'name' => 'Colocación de acero',
                'description' => 'Colocación de acero de refuerzo - Columnas',
                'fecha_creacion' => '2025-10-20',
                'status' => 'en progreso'
            ],
            [
                'name' => 'Vaciado de concreto',
                'description' => 'Vaciado de concreto - Zapatas',
                'fecha_creacion' => '2025-10-20',
                'status' => 'pendiente'
            ]
        ];

        foreach ($activities as $activity) {
            \App\Models\Activity::create([
                'project_id' => $project->id,
                'tracking_id' => $tracking->id,
                'name' => $activity['name'],
                'description' => $activity['description'],
                'fecha_creacion' => $activity['fecha_creacion'],
                'status' => $activity['status']
            ]);
        }
    }
}
