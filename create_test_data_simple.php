<?php

// Script para crear datos de prueba usando comandos artisan

echo "Creando datos de prueba...\n";

// Crear ProjectType
$output = shell_exec('php artisan tinker --execute="$pt = App\Models\ProjectType::create([\'name\' => \'Construcción\', \'description\' => \'Proyectos de construcción civil\']); echo $pt->id;"');
$projectTypeId = trim($output);
echo "✓ ProjectType creado: ID $projectTypeId\n";

// Crear ProjectSubtype
$output = shell_exec("php artisan tinker --execute=\"\$ps = App\Models\ProjectSubtype::create(['project_type_id' => $projectTypeId, 'name' => 'Edificación', 'description' => 'Construcción de edificios']); echo \$ps->id;\"");
$projectSubtypeId = trim($output);
echo "✓ ProjectSubtype creado: ID $projectSubtypeId\n";

// Crear Project
$output = shell_exec("php artisan tinker --execute=\"\$p = App\Models\Project::create(['name' => 'Edificio Los Pinos', 'location' => 'Lima, Perú', 'company' => 'Constructora ABC', 'code' => 'ERP-001', 'start_date' => '2025-01-15', 'end_date' => '2025-12-31', 'project_type_id' => $projectTypeId, 'project_subtype_id' => $projectSubtypeId]); echo \$p->id;\"");
$projectId = trim($output);
echo "✓ Project creado: ID $projectId\n";

// Crear Tracking
$output = shell_exec("php artisan tinker --execute=\"\$t = App\Models\Tracking::create(['project_id' => $projectId, 'title' => 'Seguimiento Semanal 01', 'description' => 'Seguimiento de cimentación', 'date_start' => '2025-01-20', 'duration_days' => 7, 'status' => 'activo']); echo \$t->id;\"");
$trackingId = trim($output);
echo "✓ Tracking creado: ID $trackingId\n";

// Crear Activities
$activities = [
    ['description' => 'Excavación de cimientos', 'date' => '2025-01-20', 'status' => 'completado'],
    ['description' => 'Colocación de acero de refuerzo', 'date' => '2025-01-20', 'status' => 'en_progreso'],
    ['description' => 'Vaciado de concreto', 'date' => '2025-01-20', 'status' => 'pendiente']
];

foreach ($activities as $index => $activity) {
    $output = shell_exec("php artisan tinker --execute=\"\$a = App\Models\Activity::create(['tracking_id' => $trackingId, 'description' => '{$activity['description']}', 'date' => '{$activity['date']}', 'status' => '{$activity['status']}']); echo \$a->id;\"");
    $activityId = trim($output);
    echo "✓ Activity " . ($index + 1) . " creada: ID $activityId\n";
}

echo "\n=== DATOS DE PRUEBA CREADOS ===\n";
echo "Tracking ID para usar en Postman: $trackingId\n";
echo "Fecha para el reporte: 2025-01-20\n";
echo "URL del endpoint: http://localhost:8000/endpoint/tracking/report/daily/$trackingId\n";
echo "\nJSON para el body de Postman:\n";
echo "{\n";
echo "  \"date\": \"2025-01-20\"\n";
echo "}\n";