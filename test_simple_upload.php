<?php

require __DIR__ . '/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== Test simple de upload a S3 ===\n\n";

$bucket = $_ENV['AWS_BUCKET'] ?? 'villding';
$region = $_ENV['AWS_DEFAULT_REGION'] ?? 'us-east-1';

$config = [
    'version' => 'latest',
    'region'  => $region,
    'credentials' => [
        'key'    => $_ENV['AWS_ACCESS_KEY_ID'] ?? '',
        'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'] ?? '',
    ],
];

echo "Configuración:\n";
echo "- Bucket: {$bucket}\n";
echo "- Región: {$region}\n\n";

try {
    $s3Client = new S3Client($config);
    $testKey = 'test/simple-upload-' . time() . '.txt';

    echo "1. Intentando subir archivo SIN ACL pública...\n";
    $result = $s3Client->putObject([
        'Bucket' => $bucket,
        'Key'    => $testKey,
        'Body'   => 'Test content - ' . date('Y-m-d H:i:s'),
    ]);

    echo "   ✓ Archivo subido exitosamente!\n";
    echo "   ETag: " . $result['ETag'] . "\n";
    echo "   VersionId: " . ($result['VersionId'] ?? 'N/A') . "\n\n";

    echo "2. Obteniendo URL del archivo...\n";
    $url = $s3Client->getObjectUrl($bucket, $testKey);
    echo "   URL: {$url}\n\n";

    echo "3. Leyendo archivo...\n";
    $getResult = $s3Client->getObject([
        'Bucket' => $bucket,
        'Key'    => $testKey,
    ]);
    $content = (string) $getResult['Body'];
    echo "   Contenido: {$content}\n\n";

    echo "4. Eliminando archivo de prueba...\n";
    $s3Client->deleteObject([
        'Bucket' => $bucket,
        'Key'    => $testKey,
    ]);
    echo "   ✓ Archivo eliminado\n\n";

    echo "=== ✓ PRUEBA EXITOSA ===\n";
    echo "S3 está funcionando correctamente!\n";

} catch (AwsException $e) {
    echo "\n=== ✗ ERROR ===\n";
    echo "Mensaje: " . $e->getAwsErrorMessage() . "\n";
    echo "Código: " . $e->getAwsErrorCode() . "\n";
    echo "HTTP Status: " . $e->getStatusCode() . "\n\n";

    if ($e->getAwsErrorCode() === 'NoSuchBucket') {
        echo "El bucket '{$bucket}' no existe.\n";
        echo "\nPara crear el bucket:\n";
        echo "1. Ve a https://s3.console.aws.amazon.com/\n";
        echo "2. Haz clic en 'Create bucket'\n";
        echo "3. Nombre: {$bucket}\n";
        echo "4. Región: {$region}\n";
    } elseif ($e->getAwsErrorCode() === 'AccessDenied') {
        echo "El usuario no tiene permisos suficientes.\n";
        echo "\nNecesitas estos permisos en IAM:\n";
        echo "- s3:PutObject\n";
        echo "- s3:GetObject\n";
        echo "- s3:DeleteObject\n";
        echo "- s3:ListBucket (opcional)\n";
    }
}
