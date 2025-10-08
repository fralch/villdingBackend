<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Storage;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║     VERIFICACIÓN COMPLETA DE S3 PARA LARAVEL            ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

// Verificar configuración
echo "📋 CONFIGURACIÓN\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Disk por defecto: " . config('filesystems.default') . "\n";
echo "Bucket: " . config('filesystems.disks.s3.bucket') . "\n";
echo "Región: " . config('filesystems.disks.s3.region') . "\n";
echo "Visibilidad: " . config('filesystems.disks.s3.visibility', 'no configurada') . "\n";
echo "Throw errors: " . (config('filesystems.disks.s3.throw') ? 'Sí' : 'No') . "\n";
echo "\n";

$allPassed = true;

try {
    // Test 1: Upload simple
    echo "🧪 TEST 1: Upload simple\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $testPath = 'test/verification-' . time() . '.txt';
    $content = "Prueba de S3 - " . date('Y-m-d H:i:s');

    $result = Storage::disk('s3')->put($testPath, $content);

    if ($result) {
        echo "✅ Archivo subido correctamente\n";
        echo "   Ruta: {$testPath}\n\n";
    } else {
        echo "❌ Error al subir archivo\n\n";
        $allPassed = false;
    }

    // Test 2: Verificar existencia
    echo "🧪 TEST 2: Verificar existencia\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    if (Storage::disk('s3')->exists($testPath)) {
        echo "✅ El archivo existe en S3\n\n";
    } else {
        echo "❌ El archivo no existe\n\n";
        $allPassed = false;
    }

    // Test 3: Obtener URL
    echo "🧪 TEST 3: Generar URL\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $url = Storage::disk('s3')->url($testPath);
    echo "✅ URL generada:\n";
    echo "   {$url}\n\n";

    // Test 4: Leer contenido
    echo "🧪 TEST 4: Leer contenido\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $retrieved = Storage::disk('s3')->get($testPath);
    if ($retrieved === $content) {
        echo "✅ Contenido leído correctamente\n";
        echo "   Contenido: {$retrieved}\n\n";
    } else {
        echo "❌ El contenido no coincide\n\n";
        $allPassed = false;
    }

    // Test 5: Upload con subdirectorios (simular estructura real)
    echo "🧪 TEST 5: Upload en estructura real\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    $timestamp = time();
    $testPaths = [
        'profiles/test-profile-' . $timestamp . '.txt' => 'Perfil de prueba',
        'projects/test-project-' . $timestamp . '.txt' => 'Proyecto de prueba',
        'activities/test-activity-' . $timestamp . '.txt' => 'Actividad de prueba',
    ];

    foreach ($testPaths as $path => $testContent) {
        $uploadResult = Storage::disk('s3')->put($path, $testContent);
        if ($uploadResult) {
            echo "✅ {$path}\n";
            $url = Storage::disk('s3')->url($path);
            echo "   URL: {$url}\n";
        } else {
            echo "❌ Error en {$path}\n";
            $allPassed = false;
        }
    }
    echo "\n";

    // Test 6: Eliminar archivos
    echo "🧪 TEST 6: Eliminar archivos\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    $allTestPaths = array_merge([$testPath], array_keys($testPaths));
    foreach ($allTestPaths as $path) {
        if (Storage::disk('s3')->delete($path)) {
            echo "✅ Eliminado: {$path}\n";
        } else {
            echo "❌ Error al eliminar: {$path}\n";
            $allPassed = false;
        }
    }
    echo "\n";

    // Resultado final
    echo "╔══════════════════════════════════════════════════════════╗\n";
    if ($allPassed) {
        echo "║                  ✅ TODOS LOS TESTS PASARON              ║\n";
        echo "╚══════════════════════════════════════════════════════════╝\n\n";
        echo "🎉 ¡S3 está completamente configurado y funcionando!\n\n";
        echo "📝 Próximos pasos:\n";
        echo "   1. Tu aplicación ya puede usar Storage::disk('s3')\n";
        echo "   2. Las imágenes se guardarán automáticamente en S3\n";
        echo "   3. Verifica que tu app esté usando FILESYSTEM_DISK=s3\n\n";
    } else {
        echo "║              ⚠️  ALGUNOS TESTS FALLARON                 ║\n";
        echo "╚══════════════════════════════════════════════════════════╝\n\n";
        echo "Revisa los errores arriba y verifica:\n";
        echo "   1. Permisos IAM correctos\n";
        echo "   2. Configuración del bucket\n";
        echo "   3. Región correcta en .env\n\n";
    }

} catch (\Exception $e) {
    echo "\n╔══════════════════════════════════════════════════════════╗\n";
    echo "║                    ❌ ERROR FATAL                        ║\n";
    echo "╚══════════════════════════════════════════════════════════╝\n\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Clase: " . get_class($e) . "\n";
    echo "Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n\n";

    if (strpos($e->getMessage(), 'AccessDenied') !== false) {
        echo "🔐 Problema de permisos IAM\n";
        echo "Sigue las instrucciones en AWS_SETUP_GUIDE.md\n\n";
    } elseif (strpos($e->getMessage(), 'NoSuchBucket') !== false) {
        echo "🪣 El bucket no existe o la región es incorrecta\n";
        echo "Verifica AWS_DEFAULT_REGION y AWS_BUCKET en .env\n\n";
    }
}
