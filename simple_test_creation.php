<?php

echo "=== CREANDO DATOS DE PRUEBA PASO A PASO ===\n\n";

// Paso 1: Verificar datos existentes
echo "1. Verificando datos existentes...\n";
$output = shell_exec('php artisan tinker --execute="echo App\Models\Project::count();" 2>/dev/null');
$projectCount = trim($output);
echo "Proyectos existentes: $projectCount\n\n";

// Paso 2: Crear tracking si hay un proyecto
if ($projectCount > 0) {
    echo "2. Obteniendo ID del primer proyecto...\n";
    $output = shell_exec('php artisan tinker --execute="echo App\Models\Project::first()->id;" 2>/dev/null');
    $projectId = trim($output);
    echo "Project ID: $projectId\n\n";
    
    echo "3. Creando tracking...\n";
    $trackingCommand = 'php artisan tinker --execute="$t = new App\Models\Tracking(); $t->project_id = ' . $projectId . '; $t->title = \'Seguimiento Octubre 2025\'; $t->description = \'Seguimiento de actividades\'; $t->date_start = \'2025-10-20\'; $t->duration_days = 7; $t->status = \'activo\'; $t->save(); echo $t->id;" 2>/dev/null';
    
    $trackingOutput = shell_exec($trackingCommand);
    $trackingId = trim($trackingOutput);
    echo "Tracking ID creado: $trackingId\n\n";
    
    if (is_numeric($trackingId)) {
        echo "4. Creando actividades para el 20 de octubre de 2025...\n";
        
        $activities = [
            ['desc' => 'Excavación de cimientos - Sector A', 'status' => 'completado'],
            ['desc' => 'Colocación de acero de refuerzo - Columnas', 'status' => 'en_progreso'],
            ['desc' => 'Vaciado de concreto - Zapatas', 'status' => 'pendiente']
        ];
        
        foreach ($activities as $index => $activity) {
            $activityCommand = 'php artisan tinker --execute="$a = new App\Models\Activity(); $a->tracking_id = ' . $trackingId . '; $a->description = \'' . $activity['desc'] . '\'; $a->date = \'2025-10-20\'; $a->status = \'' . $activity['status'] . '\'; $a->save(); echo $a->id;" 2>/dev/null';
            
            $activityOutput = shell_exec($activityCommand);
            $activityId = trim($activityOutput);
            echo "   ✓ Actividad " . ($index + 1) . " creada: ID $activityId - {$activity['desc']}\n";
        }
        
        echo "\n5. Generando reporte de prueba...\n";
        
        // Crear un archivo temporal para el test del reporte
        $testScript = '<?php
require_once __DIR__ . "/vendor/autoload.php";

$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Trackings\TrackingController;
use Illuminate\Http\Request;

$controller = new TrackingController();
$request = new Request();
$request->merge(["date" => "2025-10-20"]);

try {
    $response = $controller->generateDailyReport($request, ' . $trackingId . ');
    echo "✓ Reporte generado exitosamente!\n";
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content-Type: " . $response->headers->get("Content-Type") . "\n";
    
    // Verificar si es un PDF
    if ($response->headers->get("Content-Type") === "application/pdf") {
        echo "✓ PDF generado correctamente\n";
        echo "Tamaño del PDF: " . strlen($response->getContent()) . " bytes\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}';
        
        file_put_contents('test_report_temp.php', $testScript);
        
        $reportOutput = shell_exec('php test_report_temp.php 2>&1');
        echo $reportOutput . "\n";
        
        // Limpiar archivo temporal
        unlink('test_report_temp.php');
        
        echo "\n=== DATOS PARA POSTMAN ===\n";
        echo "URL: http://localhost:8000/endpoint/tracking/report/daily/$trackingId\n";
        echo "Method: POST\n";
        echo "Headers: Content-Type: application/json\n";
        echo "Body (JSON):\n";
        echo "{\n";
        echo "  \"date\": \"2025-10-20\"\n";
        echo "}\n\n";
        
        echo "=== RESUMEN FINAL ===\n";
        echo "✓ Tracking ID: $trackingId\n";
        echo "✓ Fecha de actividades: 2025-10-20\n";
        echo "✓ Actividades creadas: 3\n";
        echo "✓ Reporte de prueba ejecutado\n";
        
    } else {
        echo "❌ Error: No se pudo crear el tracking\n";
    }
} else {
    echo "❌ No hay proyectos en la base de datos. Ejecuta primero las migraciones y seeders.\n";
}