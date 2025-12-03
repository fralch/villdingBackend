<?php

/**
 * Script de prueba para verificar la duplicación de actividades con imágenes en S3
 *
 * Uso desde Windows:
 * php test_duplicate.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Storage;
use App\Models\Activity;

echo "=== Test de Duplicación de Actividades con S3 ===\n\n";

// 1. Verificar configuración de S3
echo "1. Verificando configuración de S3...\n";
echo "   Bucket: " . env('AWS_BUCKET') . "\n";
echo "   Region: " . env('AWS_DEFAULT_REGION') . "\n";
echo "   URL: " . env('AWS_URL') . "\n";

try {
    // Test de conexión a S3
    $files = Storage::disk('s3')->files('activities');
    echo "   ✓ Conexión a S3 exitosa\n";
    echo "   Total de archivos en 'activities': " . count($files) . "\n\n";
} catch (\Exception $e) {
    echo "   ✗ Error al conectar con S3: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Buscar una actividad con imágenes
echo "2. Buscando actividad con imágenes...\n";
$activity = Activity::whereNotNull('image')->first();

if (!$activity) {
    echo "   ✗ No se encontró ninguna actividad con imágenes\n";
    echo "   Crea una actividad con imágenes primero\n";
    exit(1);
}

echo "   ✓ Actividad encontrada: ID = {$activity->id}\n";
echo "   Nombre: {$activity->name}\n";

$images = $activity->image ?? [];
echo "   Número de imágenes: " . count($images) . "\n";

if (empty($images)) {
    echo "   ✗ La actividad no tiene imágenes\n";
    exit(1);
}

echo "   Imágenes:\n";
foreach ($images as $index => $imagePath) {
    echo "   - [{$index}] {$imagePath}\n";

    // Verificar que cada imagen existe en S3
    if (Storage::disk('s3')->exists($imagePath)) {
        $size = Storage::disk('s3')->size($imagePath);
        echo "     ✓ Existe en S3 (Tamaño: " . number_format($size / 1024, 2) . " KB)\n";
    } else {
        echo "     ✗ NO existe en S3\n";
    }
}

echo "\n3. Simulando proceso de duplicación...\n";

foreach ($images as $index => $imagePath) {
    echo "\n   Procesando imagen {$index}: {$imagePath}\n";

    try {
        // Paso 1: Descargar contenido
        echo "   - Descargando contenido...\n";
        $content = Storage::disk('s3')->get($imagePath);

        if ($content) {
            $size = strlen($content);
            echo "     ✓ Descargado: " . number_format($size / 1024, 2) . " KB\n";

            // Paso 2: Generar nuevo nombre
            $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
            $newFileName = 'test-' . \Illuminate\Support\Str::uuid()->toString() . '.' . $extension;
            $newPath = 'activities/' . $newFileName;

            echo "   - Nuevo path: {$newPath}\n";

            // Paso 3: Subir con nuevo nombre (sin 'public')
            echo "   - Subiendo a S3...\n";
            $success = Storage::disk('s3')->put($newPath, $content);

            if ($success) {
                echo "     ✓ Subido exitosamente\n";

                // Paso 4: Verificar que existe
                if (Storage::disk('s3')->exists($newPath)) {
                    echo "     ✓ Verificado en S3\n";

                    // Paso 5: Obtener URL
                    $url = Storage::disk('s3')->url($newPath);
                    echo "     ✓ URL: {$url}\n";

                    // Limpiar archivo de prueba
                    echo "   - Eliminando archivo de prueba...\n";
                    Storage::disk('s3')->delete($newPath);
                    echo "     ✓ Limpieza completada\n";
                } else {
                    echo "     ✗ Error: El archivo no existe después de subirlo\n";
                }
            } else {
                echo "     ✗ Error al subir\n";
            }
        } else {
            echo "     ✗ No se pudo descargar el contenido\n";
        }

    } catch (\Exception $e) {
        echo "     ✗ Error: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Test Completado ===\n";
echo "\nSi todos los pasos muestran ✓, la duplicación debería funcionar correctamente.\n";
echo "Ahora puedes probar la duplicación desde tu app.\n";
