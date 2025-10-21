<?php

echo "=== GENERANDO DATOS DE PRUEBA Y REPORTE ===\n\n";

// Paso 1: Crear datos de prueba usando artisan tinker
echo "1. Creando datos de prueba...\n";

$commands = [
    // Crear ProjectType
    'php artisan tinker --execute="$pt = new App\Models\ProjectType(); $pt->name = \'Construcción\'; $pt->description = \'Proyectos de construcción civil\'; $pt->save(); echo \'ProjectType ID: \' . $pt->id . PHP_EOL;"',
    
    // Crear ProjectSubtype (necesitamos obtener el ID del ProjectType primero)
    'php artisan tinker --execute="$pt = App\Models\ProjectType::first(); $ps = new App\Models\ProjectSubtype(); $ps->project_type_id = $pt->id; $ps->name = \'Edificación\'; $ps->description = \'Construcción de edificios\'; $ps->save(); echo \'ProjectSubtype ID: \' . $ps->id . PHP_EOL;"',
    
    // Crear Project
    'php artisan tinker --execute="$pt = App\Models\ProjectType::first(); $ps = App\Models\ProjectSubtype::first(); $p = new App\Models\Project(); $p->name = \'Edificio Los Pinos\'; $p->location = \'Lima, Perú\'; $p->company = \'Constructora ABC\'; $p->code = \'ERP-2025-001\'; $p->start_date = \'2025-01-15\'; $p->end_date = \'2025-12-31\'; $p->project_type_id = $pt->id; $p->project_subtype_id = $ps->id; $p->save(); echo \'Project ID: \' . $p->id . PHP_EOL;"',
    
    // Crear Tracking
    'php artisan tinker --execute="$p = App\Models\Project::first(); $t = new App\Models\Tracking(); $t->project_id = $p->id; $t->title = \'Seguimiento Octubre 2025\'; $t->description = \'Seguimiento de actividades\'; $t->date_start = \'2025-10-20\'; $t->duration_days = 7; $t->status = \'activo\'; $t->save(); echo \'Tracking ID: \' . $t->id . PHP_EOL;"'
];

$trackingId = null;

foreach ($commands as $command) {
    echo "Ejecutando: " . substr($command, 0, 50) . "...\n";
    $output = shell_exec($command . ' 2>&1');
    echo "Resultado: " . trim($output) . "\n";
    
    // Extraer el Tracking ID del último comando
    if (strpos($output, 'Tracking ID:') !== false) {
        preg_match('/Tracking ID: (\d+)/', $output, $matches);
        if (isset($matches[1])) {
            $trackingId = $matches[1];
        }
    }
    echo "\n";
}

if (!$trackingId) {
    echo "❌ No se pudo obtener el Tracking ID. Intentando obtenerlo...\n";
    $output = shell_exec('php artisan tinker --execute="$t = App\Models\Tracking::first(); if($t) echo $t->id; else echo \'No tracking found\';" 2>&1');
    $trackingId = trim($output);
    echo "Tracking ID obtenido: $trackingId\n\n";
}

// Paso 2: Crear actividades para el 20 de octubre de 2025
echo "2. Creando actividades para el 20 de octubre de 2025...\n";

$activities = [
    'Excavación de cimientos - Sector A',
    'Colocación de acero de refuerzo - Columnas',
    'Vaciado de concreto - Zapatas'
];

$statuses = ['completado', 'en_progreso', 'pendiente'];

foreach ($activities as $index => $description) {
    $status = $statuses[$index];
    $activityCommand = "php artisan tinker --execute=\"\$a = new App\Models\Activity(); \$a->tracking_id = $trackingId; \$a->description = '$description'; \$a->date = '2025-10-20'; \$a->status = '$status'; \$a->save(); echo 'Activity ID: ' . \$a->id . PHP_EOL;\" 2>&1";
    
    echo "Creando actividad: $description\n";
    $output = shell_exec($activityCommand);
    echo "Resultado: " . trim($output) . "\n\n";
}

// Paso 3: Generar el reporte
echo "3. Generando reporte para el 20 de octubre de 2025...\n";

$reportCommand = "php artisan tinker --execute=\"
use App\Http\Controllers\Trackings\TrackingController;
use Illuminate\Http\Request;

\$controller = new TrackingController();
\$request = new Request();
\$request->merge(['date' => '2025-10-20']);

try {
    \$response = \$controller->generateDailyReport(\$request, $trackingId);
    echo 'Reporte generado exitosamente!' . PHP_EOL;
    echo 'Status: ' . \$response->getStatusCode() . PHP_EOL;
    echo 'Content-Type: ' . \$response->headers->get('Content-Type') . PHP_EOL;
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
\" 2>&1";

echo "Ejecutando generación de reporte...\n";
$reportOutput = shell_exec($reportCommand);
echo "Resultado del reporte:\n" . $reportOutput . "\n";

echo "\n=== RESUMEN ===\n";
echo "Tracking ID creado: $trackingId\n";
echo "Fecha de actividades: 2025-10-20\n";
echo "Actividades creadas: " . count($activities) . "\n";
echo "Reporte generado para verificar funcionalidad\n";

echo "\n=== DATOS PARA POSTMAN ===\n";
echo "URL: http://localhost:8000/endpoint/tracking/report/daily/$trackingId\n";
echo "Method: POST\n";
echo "Body (JSON):\n";
echo "{\n";
echo "  \"date\": \"2025-10-20\"\n";
echo "}\n";