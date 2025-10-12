<?php

require __DIR__.'/vendor/autoload.php';

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== Probando Conexión a Aiven MySQL ===\n\n";

echo "Configuración:\n";
echo "Host: " . $_ENV['DB_HOST'] . "\n";
echo "Puerto: " . $_ENV['DB_PORT'] . "\n";
echo "Base de datos: " . $_ENV['DB_DATABASE'] . "\n";
echo "Usuario: " . $_ENV['DB_USERNAME'] . "\n";
echo "Certificado SSL: " . $_ENV['MYSQL_ATTR_SSL_CA'] . "\n\n";

// Verificar que existe el certificado
if (!file_exists($_ENV['MYSQL_ATTR_SSL_CA'])) {
    echo "❌ ERROR: El archivo del certificado SSL no existe: " . $_ENV['MYSQL_ATTR_SSL_CA'] . "\n";
    exit(1);
}

echo "✓ Certificado SSL encontrado\n\n";

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s',
        $_ENV['DB_HOST'],
        $_ENV['DB_PORT'],
        $_ENV['DB_DATABASE']
    );

    echo "DSN: $dsn\n\n";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_SSL_CA => $_ENV['MYSQL_ATTR_SSL_CA'],
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
    ];

    echo "Intentando conectar...\n";

    $pdo = new PDO(
        $dsn,
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD'],
        $options
    );

    echo "✓ ¡Conexión exitosa!\n\n";

    // Obtener versión de MySQL
    $stmt = $pdo->query('SELECT VERSION()');
    $version = $stmt->fetch(PDO::FETCH_NUM)[0];

    echo "Versión de MySQL: $version\n";

    // Probar una consulta simple
    $stmt = $pdo->query('SELECT DATABASE()');
    $db = $stmt->fetch(PDO::FETCH_NUM)[0];

    echo "Base de datos actual: $db\n";

    // Listar tablas
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "\nTablas en la base de datos (" . count($tables) . "):\n";
    if (count($tables) > 0) {
        foreach ($tables as $table) {
            echo "  - $table\n";
        }
    } else {
        echo "  (no hay tablas)\n";
    }

    echo "\n✓ ¡Prueba completada exitosamente!\n";

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
