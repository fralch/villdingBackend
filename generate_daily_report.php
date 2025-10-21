<?php

require_once __DIR__ . '/bootstrap/app.php';

use App\Http\Controllers\TrackingController;
use Illuminate\Http\Request;

// Crear una instancia del controlador
$controller = new TrackingController();

// Crear un request simulado para el reporte diario
$request = new Request();
$request->merge([
    'date' => '2025-10-20'
]);

echo "=== GENERANDO REPORTE DIARIO PARA EL 20 DE OCTUBRE DE 2025 ===\n\n";

try {
    // Llamar al método de reporte diario
    $response = $controller->dailyReport($request);
    
    // Obtener el contenido de la respuesta
    $data = $response->getData(true);
    
    echo "✅ REPORTE GENERADO EXITOSAMENTE\n\n";
    
    echo "📊 RESUMEN DEL REPORTE:\n";
    echo "Fecha: " . $data['date'] . "\n";
    echo "Total de actividades: " . count($data['activities']) . "\n\n";
    
    echo "📋 ACTIVIDADES DEL DÍA:\n";
    foreach ($data['activities'] as $index => $activity) {
        echo ($index + 1) . ". " . $activity['name'] . "\n";
        echo "   Descripción: " . $activity['description'] . "\n";
        echo "   Estado: " . $activity['status'] . "\n";
        echo "   Proyecto: " . $activity['project']['name'] . "\n";
        echo "   Tracking: " . $activity['tracking']['title'] . "\n\n";
    }
    
    echo "🌐 ENDPOINT PARA POSTMAN:\n";
    echo "URL: GET http://localhost:8000/api/tracking/daily-report?date=2025-10-20\n\n";
    
    echo "📝 EJEMPLO DE RESPUESTA JSON:\n";
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    
    echo "✅ VERIFICACIÓN COMPLETADA - Todo funciona correctamente!\n";
    
} catch (Exception $e) {
    echo "❌ ERROR AL GENERAR EL REPORTE:\n";
    echo $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}