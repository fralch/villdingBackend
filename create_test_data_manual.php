<?php

require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

echo "Creando datos de prueba manualmente...\n";

try {
    // 1. Crear ProjectType
    $projectTypeId = DB::table('project_types')->insertGetId([
        'name' => 'Construcción',
        'description' => 'Proyectos de construcción civil',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "✓ ProjectType creado: ID $projectTypeId\n";

    // 2. Crear ProjectSubtype
    $projectSubtypeId = DB::table('project_subtypes')->insertGetId([
        'project_type_id' => $projectTypeId,
        'name' => 'Edificación',
        'description' => 'Construcción de edificios',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "✓ ProjectSubtype creado: ID $projectSubtypeId\n";

    // 3. Crear Project
    $projectId = DB::table('projects')->insertGetId([
        'name' => 'Edificio Los Pinos',
        'location' => 'Lima, Perú',
        'company' => 'Constructora ABC S.A.C.',
        'code' => 'ERP-2025-001',
        'start_date' => '2025-01-15',
        'end_date' => '2025-12-31',
        'project_type_id' => $projectTypeId,
        'project_subtype_id' => $projectSubtypeId,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "✓ Project creado: ID $projectId\n";

    // 4. Crear Tracking
    $trackingId = DB::table('trackings')->insertGetId([
        'project_id' => $projectId,
        'title' => 'Seguimiento Semanal - Octubre 2025',
        'description' => 'Seguimiento de actividades de construcción',
        'date_start' => '2025-10-20',
        'duration_days' => 7,
        'status' => 'activo',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "✓ Tracking creado: ID $trackingId\n";

    // 5. Crear Activities para el 20 de octubre de 2025
    $activities = [
        [
            'description' => 'Excavación de cimientos - Sector A',
            'date' => '2025-10-20',
            'status' => 'completado'
        ],
        [
            'description' => 'Colocación de acero de refuerzo - Columnas',
            'date' => '2025-10-20',
            'status' => 'en_progreso'
        ],
        [
            'description' => 'Vaciado de concreto - Zapatas',
            'date' => '2025-10-20',
            'status' => 'pendiente'
        ]
    ];

    foreach ($activities as $index => $activity) {
        $activityId = DB::table('activities')->insertGetId([
            'tracking_id' => $trackingId,
            'description' => $activity['description'],
            'date' => $activity['date'],
            'status' => $activity['status'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "✓ Activity " . ($index + 1) . " creada: ID $activityId - {$activity['description']}\n";
    }

    echo "\n=== DATOS DE PRUEBA CREADOS EXITOSAMENTE ===\n";
    echo "Tracking ID: $trackingId\n";
    echo "Fecha de actividades: 2025-10-20\n";
    echo "Total de actividades: " . count($activities) . "\n";
    
    return $trackingId;

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    return false;
}