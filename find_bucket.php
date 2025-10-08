<?php

require __DIR__ . '/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== Buscando bucket 'villding' en diferentes regiones ===\n\n";

$regions = [
    'us-east-1',      // N. Virginia
    'us-east-2',      // Ohio
    'us-west-1',      // N. California
    'us-west-2',      // Oregon
    'sa-east-1',      // São Paulo
];

$bucket = $_ENV['AWS_BUCKET'] ?? 'villding';

foreach ($regions as $region) {
    echo "Probando región: {$region}... ";

    $config = [
        'version' => 'latest',
        'region'  => $region,
        'credentials' => [
            'key'    => $_ENV['AWS_ACCESS_KEY_ID'] ?? '',
            'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'] ?? '',
        ],
    ];

    try {
        $s3Client = new S3Client($config);
        $result = $s3Client->headBucket(['Bucket' => $bucket]);

        echo "✓ ENCONTRADO!\n\n";
        echo "=== BUCKET ENCONTRADO EN {$region} ===\n\n";

        // Probar escritura
        echo "Probando escritura...\n";
        try {
            $testKey = 'test/region-test-' . time() . '.txt';
            $result = $s3Client->putObject([
                'Bucket' => $bucket,
                'Key'    => $testKey,
                'Body'   => 'Test from region detection',
                'ACL'    => 'public-read',
            ]);
            echo "✓ Escritura exitosa!\n";
            echo "URL: " . $result['ObjectURL'] . "\n\n";

            // Limpiar
            $s3Client->deleteObject([
                'Bucket' => $bucket,
                'Key'    => $testKey,
            ]);
            echo "✓ Archivo de prueba eliminado\n\n";

            echo "ACTUALIZA tu .env con:\n";
            echo "AWS_DEFAULT_REGION={$region}\n";

        } catch (AwsException $e) {
            echo "✗ Error de escritura: " . $e->getAwsErrorMessage() . "\n";
            echo "Código: " . $e->getAwsErrorCode() . "\n";
        }

        break;

    } catch (AwsException $e) {
        if ($e->getAwsErrorCode() === 'NotFound') {
            echo "✗ No existe en esta región\n";
        } else {
            echo "✗ " . $e->getAwsErrorCode() . "\n";
        }
    }
}

echo "\n=== Fin de búsqueda ===\n";
