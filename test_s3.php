<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Storage;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Probando configuración de S3 ===\n\n";

// Verificar configuración
echo "Configuración de S3:\n";
echo "- Disk por defecto: " . config('filesystems.default') . "\n";
echo "- Bucket: " . config('filesystems.disks.s3.bucket') . "\n";
echo "- Región: " . config('filesystems.disks.s3.region') . "\n";
echo "- Access Key ID: " . substr(config('filesystems.disks.s3.key'), 0, 5) . "...\n\n";

try {
    // Crear un archivo de prueba
    $testContent = "Test file created at " . now();
    $testPath = 'test/test-' . time() . '.txt';

    echo "1. Intentando subir archivo de prueba a S3...\n";

    // Activar logging de errores
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    $uploaded = Storage::disk('s3')->put($testPath, $testContent);

    echo "   Resultado de put(): " . var_export($uploaded, true) . "\n";

    if ($uploaded) {
        echo "   ✓ Archivo subido exitosamente: {$testPath}\n\n";

        // Verificar que existe
        echo "2. Verificando existencia del archivo...\n";
        if (Storage::disk('s3')->exists($testPath)) {
            echo "   ✓ Archivo existe en S3\n\n";

            // Obtener URL
            echo "3. Obteniendo URL del archivo...\n";
            $url = Storage::disk('s3')->url($testPath);
            echo "   URL: {$url}\n\n";

            // Leer contenido
            echo "4. Leyendo contenido...\n";
            $content = Storage::disk('s3')->get($testPath);
            echo "   Contenido: {$content}\n\n";

            // Eliminar archivo de prueba
            echo "5. Eliminando archivo de prueba...\n";
            Storage::disk('s3')->delete($testPath);
            echo "   ✓ Archivo eliminado\n\n";

            echo "=== ✓ TODAS LAS PRUEBAS PASARON ===\n";
            echo "Tu configuración de S3 está funcionando correctamente.\n";
        } else {
            echo "   ✗ Error: El archivo no existe después de subirlo\n";
        }
    } else {
        echo "   ✗ Error: No se pudo subir el archivo\n";
    }

} catch (\Exception $e) {
    echo "\n=== ✗ ERROR ===\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "Tipo: " . get_class($e) . "\n";
    echo "\nVerifica:\n";
    echo "1. Que las credenciales de AWS sean correctas\n";
    echo "2. Que el bucket 'villding' exista\n";
    echo "3. Que las credenciales tengan permisos sobre el bucket\n";
    echo "4. Que la región sea correcta\n";
}
