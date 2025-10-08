<?php

require __DIR__ . '/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Cargar configuración de .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== Diagnóstico detallado de S3 ===\n\n";

$config = [
    'version' => 'latest',
    'region'  => $_ENV['AWS_DEFAULT_REGION'] ?? 'us-east-1',
    'credentials' => [
        'key'    => $_ENV['AWS_ACCESS_KEY_ID'] ?? '',
        'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'] ?? '',
    ],
];

echo "Configuración:\n";
echo "- Región: " . $config['region'] . "\n";
echo "- Access Key: " . substr($config['credentials']['key'], 0, 10) . "...\n";
echo "- Bucket: " . ($_ENV['AWS_BUCKET'] ?? 'villding') . "\n\n";

try {
    $s3Client = new S3Client($config);
    $bucket = $_ENV['AWS_BUCKET'] ?? 'villding';

    echo "1. Verificando acceso al bucket '{$bucket}'...\n";
    try {
        $result = $s3Client->headBucket(['Bucket' => $bucket]);
        echo "   ✓ Bucket existe y es accesible\n\n";

        echo "2. Verificando permisos de lectura...\n";
        try {
            $result = $s3Client->listObjectsV2([
                'Bucket' => $bucket,
                'MaxKeys' => 1
            ]);
            echo "   ✓ Permisos de lectura: OK\n\n";
        } catch (AwsException $e) {
            echo "   ✗ Error de lectura: " . $e->getMessage() . "\n\n";
        }

        echo "3. Verificando permisos de escritura...\n";
        try {
            $testKey = 'test/diagnostic-' . time() . '.txt';
            $result = $s3Client->putObject([
                'Bucket' => $bucket,
                'Key'    => $testKey,
                'Body'   => 'Test content from diagnostic script',
                'ACL'    => 'public-read',
            ]);
            echo "   ✓ Permisos de escritura: OK\n";
            echo "   Archivo creado: {$testKey}\n";
            echo "   URL: " . $result['ObjectURL'] . "\n\n";

            echo "4. Eliminando archivo de prueba...\n";
            $s3Client->deleteObject([
                'Bucket' => $bucket,
                'Key'    => $testKey,
            ]);
            echo "   ✓ Archivo eliminado\n\n";

            echo "=== ✓ DIAGNÓSTICO COMPLETO ===\n";
            echo "Tu configuración de S3 está funcionando correctamente.\n";
            echo "\nPróximos pasos:\n";
            echo "1. Asegúrate de que FILESYSTEM_DISK=s3 en tu .env\n";
            echo "2. Limpia la caché de configuración: php artisan config:clear\n";
            echo "3. Tu aplicación debería poder usar S3 sin problemas\n";

        } catch (AwsException $e) {
            echo "   ✗ Error de escritura: " . $e->getAwsErrorMessage() . "\n";
            echo "   Código de error: " . $e->getAwsErrorCode() . "\n\n";

            echo "Posibles soluciones:\n";
            echo "- Verifica que tu usuario IAM tenga el permiso 's3:PutObject'\n";
            echo "- Verifica que el bucket no tenga políticas que bloqueen la escritura\n";
            echo "- Verifica que Block Public Access no esté habilitado si necesitas ACL públicas\n";
        }

    } catch (AwsException $e) {
        echo "   ✗ Bucket no encontrado o no accesible\n";
        echo "   Error: " . $e->getAwsErrorMessage() . "\n\n";

        echo "Posibles soluciones:\n";
        echo "- Verifica que el bucket 'villding' exista en la región {$config['region']}\n";
        echo "- Verifica que tu usuario IAM tenga permisos sobre este bucket\n";
    }

} catch (AwsException $e) {
    echo "✗ Error al conectar con AWS\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "Código: " . $e->getAwsErrorCode() . "\n\n";

    echo "Verifica:\n";
    echo "1. Que AWS_ACCESS_KEY_ID sea correcta\n";
    echo "2. Que AWS_SECRET_ACCESS_KEY sea correcta\n";
    echo "3. Que las credenciales no hayan expirado\n";
} catch (\Exception $e) {
    echo "✗ Error general: " . $e->getMessage() . "\n";
}
