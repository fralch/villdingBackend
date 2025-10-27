<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Project;
use App\Models\ProjectType;
use App\Models\ProjectSubtype;
use App\Models\Tracking;
use App\Models\Activity;

// Configurar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Creando datos de prueba para Postman ===\n\n";

try {
    // 1. Crear tipo de proyecto
    $projectType = ProjectType::create([
        'name' => 'Construcción',
        'description' => 'Proyectos de construcción civil'
    ]);
    echo "✓ Tipo de proyecto creado: ID {$projectType->id}\n";

    // 2. Crear subtipo de proyecto
    $projectSubtype = ProjectSubtype::create([
        'project_type_id' => $projectType->id,
        'name' => 'Edificación',
        'description' => 'Construcción de edificios'
    ]);
    echo "✓ Subtipo de proyecto creado: ID {$projectSubtype->id}\n";

    // 3. Crear proyecto
    $project = Project::create([
        'name' => 'Edificio Residencial Los Pinos',
        'location' => 'Lima, Perú',
        'company' => 'Constructora ABC S.A.C.',
        'code' => 'ERP-2025-001',
        'start_date' => '2025-01-15',
        'end_date' => '2025-12-31',
        'project_type_id' => $projectType->id,
        'project_subtype_id' => $projectSubtype->id,
    ]);
    echo "✓ Proyecto creado: ID {$project->id}\n";

    // 4. Crear tracking
    $tracking = Tracking::create([
        'project_id' => $project->id,
        'title' => 'Seguimiento Semanal 01 - Cimentación',
        'description' => 'Seguimiento de trabajos de cimentación y estructura',
        'date_start' => '2025-01-20',
        'duration_days' => 7,
        'status' => 'activo'
    ]);
    echo "✓ Tracking creado: ID {$tracking->id}\n";

    // 5. Crear actividades para diferentes fechas
    $activities = [
        [
            'date' => '2025-01-20',
            'name' => 'Excavación de zapatas',
            'description' => 'Excavación manual y mecánica de zapatas según planos estructurales',
            'status' => 'completado'
        ],
        [
            'date' => '2025-01-20',
            'name' => 'Verificación de niveles',
            'description' => 'Control topográfico de niveles de excavación',
            'status' => 'completado'
        ],
        [
            'date' => '2025-01-21',
            'name' => 'Colocación de acero de refuerzo',
            'description' => 'Habilitación y colocación de acero corrugado en zapatas',
            'status' => 'en_proceso'
        ],
        [
            'date' => '2025-01-21',
            'name' => 'Preparación de concreto',
            'description' => 'Preparación de mezcla de concreto f\'c=210 kg/cm²',
            'status' => 'programado'
        ],
        [
            'date' => '2025-01-22',
            'name' => 'Vaciado de concreto',
            'description' => 'Vaciado de concreto en zapatas y vigas de cimentación',
            'status' => 'programado'
        ]
    ];

    foreach ($activities as $activityData) {
        $activity = Activity::create([
            'project_id' => $project->id,
            'tracking_id' => $tracking->id,
            'name' => $activityData['name'],
            'description' => $activityData['description'],
            'fecha_creacion' => $activityData['date'],
            'status' => $activityData['status'],
            'image' => null
        ]);
        echo "✓ Actividad creada: {$activityData['name']} (Fecha: {$activityData['date']})\n";
    }

    echo "\n=== DATOS CREADOS EXITOSAMENTE ===\n";
    echo "Para probar en Postman, usa estos datos:\n\n";
    echo "TRACKING ID: {$tracking->id}\n";
    echo "FECHAS DISPONIBLES:\n";
    echo "- 2025-01-20 (2 actividades)\n";
    echo "- 2025-01-21 (2 actividades)\n";
    echo "- 2025-01-22 (1 actividad)\n\n";
    
    echo "ENDPOINT: POST http://localhost:8000/endpoint/tracking/report/daily/{$tracking->id}\n";
    echo "BODY (JSON): {\"date\": \"2025-01-20\"}\n\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}