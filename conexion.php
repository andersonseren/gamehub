<?php
// ============================================================
// CONEXIÓN PDO A POSTGRESQL (LOCAL Y PRODUCCIÓN)
// ============================================================

define('DB_PORT', '5432');
define('DB_NAME', 'gamehub_t2m0');
define('DB_USER', 'gamehub_t2m0_user');
define('DB_PASS', 'KhbeB8dHES31FFX1NbAu00bz15TQWp7s');

// URL del servicio Flask (actualízala después)
define('FLASK_API_URL', 'https://tu-app-flask.railway.app/api/resena');

// ============================================================
// DETECCIÓN DE ENTORNO (RENDER o DOCKER)
// ============================================================
$isProduction = (getenv('RENDER') !== false || file_exists('/.dockerenv'));

if ($isProduction) {
    // ============================================================
    // ENTORNO DE PRODUCCIÓN (RENDER o DOCKER)
    // ============================================================
    // Hostname interno (sin .render.com)
    define('DB_HOST', 'dpg-d936aiojs32c73boer5g-a');
    
    // DSN sin certificado (conexión interna o con SSL simple)
    $dsn = sprintf(
        "pgsql:host=%s;port=%s;dbname=%s;sslmode=require",
        DB_HOST,
        DB_PORT,
        DB_NAME
    );
} else {
    // ============================================================
    // ENTORNO DE DESARROLLO (LOCAL) - CONEXIÓN EXTERNA CON SSL
    // ============================================================
    define('DB_HOST', 'dpg-d936aiojs32c73boer5g-a.oregon-postgres.render.com');
    define('CA_BUNDLE', __DIR__ . '/cacert.pem');
    
    if (!file_exists(CA_BUNDLE)) {
        die("❌ No se encuentra el archivo cacert.pem. 
             Descárgalo desde https://curl.se/docs/caextract.html 
             y colócalo en la raíz del proyecto.");
    }
    
    $dsn = sprintf(
        "pgsql:host=%s;port=%s;dbname=%s;sslmode=verify-ca;sslrootcert=%s",
        DB_HOST,
        DB_PORT,
        DB_NAME,
        CA_BUNDLE
    );
}

// ============================================================
// FUNCIÓN OBTENER CONEXIÓN (PDO)
// ============================================================
function obtenerConexion() {
    global $dsn;
    
    try {
        $opciones = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 10,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
        return $pdo;
        
    } catch (PDOException $e) {
        // En producción, mostramos el error (por ahora, para depurar)
        die("❌ Error de conexión: " . $e->getMessage());
    }
}
?>
