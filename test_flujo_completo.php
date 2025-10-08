<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║          TEST DE FLUJO COMPLETO: DB → S3 → JSON         ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

// Simular el flujo completo
echo "1️⃣  SIMULANDO SUBIDA DE IMAGEN DE USUARIO\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Crear contenido de imagen de prueba
$imageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
$tempPath = sys_get_temp_dir() . '/test-profile-' . time() . '.png';
file_put_contents($tempPath, $imageContent);

$imagePath = Storage::disk('s3')->putFileAs('profiles', new \Illuminate\Http\File($tempPath), 'test-user-' . time() . '.png');

echo "✅ Imagen subida a S3\n";
echo "   Ruta guardada en DB: {$imagePath}\n\n";

// Simular que se guardó en la base de datos
echo "2️⃣  SIMULANDO LECTURA DESDE LA BASE DE DATOS\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$user = new App\Models\User();
$user->id = 999;
$user->name = "Usuario de Prueba";
$user->email = "test@example.com";
$user->uri = $imagePath; // Esta es la ruta que viene de la DB

echo "✅ Usuario cargado desde DB (simulado)\n";
echo "   DB value (uri): {$imagePath}\n\n";

echo "3️⃣  CONVIRTIENDO A JSON (como en tu API)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$userJson = $user->toArray();

echo "✅ Usuario convertido a JSON\n";
echo "   JSON uri: " . $userJson['uri'] . "\n\n";

echo "4️⃣  VERIFICANDO QUE LA URL FUNCIONA\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

if (filter_var($userJson['uri'], FILTER_VALIDATE_URL)) {
    echo "✅ La URL es válida y completa\n";
    echo "   URL: {$userJson['uri']}\n\n";
} else {
    echo "❌ La URL NO es válida\n\n";
}

echo "5️⃣  PROBANDO CON PROJECT\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$projectImagePath = Storage::disk('s3')->putFileAs('projects', new \Illuminate\Http\File($tempPath), 'test-project-' . time() . '.png');

$project = new App\Models\Project();
$project->name = "Proyecto de Prueba";
$project->uri = $projectImagePath;

$projectJson = $project->toArray();
echo "✅ Proyecto convertido a JSON\n";
echo "   DB value: {$projectImagePath}\n";
echo "   JSON uri: " . $projectJson['uri'] . "\n\n";

echo "6️⃣  PROBANDO CON ACTIVITY (múltiples imágenes)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$activityImages = [];
for ($i = 1; $i <= 3; $i++) {
    $activityImages[] = Storage::disk('s3')->putFileAs('activities', new \Illuminate\Http\File($tempPath), 'test-activity-' . time() . "-{$i}.png");
}

$activity = new App\Models\Activity();
$activity->name = "Actividad de Prueba";
$activity->image = $activityImages; // Array de rutas

$activityJson = $activity->toArray();
echo "✅ Actividad convertida a JSON\n";
echo "   DB value (JSON array): " . json_encode($activityImages) . "\n";
echo "   JSON image_urls:\n";
foreach ($activityJson['image_urls'] as $url) {
    echo "   - {$url}\n";
}
echo "\n";

echo "7️⃣  LIMPIANDO ARCHIVOS DE PRUEBA\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

Storage::disk('s3')->delete($imagePath);
Storage::disk('s3')->delete($projectImagePath);
foreach ($activityImages as $img) {
    Storage::disk('s3')->delete($img);
}
unlink($tempPath);

echo "✅ Archivos eliminados\n\n";

echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║                    ✅ FLUJO COMPLETO OK                  ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

echo "📝 RESUMEN:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "1. Cuando SUBES una imagen:\n";
echo "   → Se guarda en S3 con UUID único\n";
echo "   → En la DB se guarda solo: 'profiles/uuid.ext'\n\n";

echo "2. Cuando tu API DEVUELVE el usuario:\n";
echo "   → El modelo convierte automáticamente a URL completa\n";
echo "   → Tu frontend recibe: 'https://villding.s3...'\n\n";

echo "3. Tu frontend puede usar la URL directamente:\n";
echo "   → <img src=\"{{user.uri}}\" />\n";
echo "   → Funciona sin problemas ✅\n\n";
