<?php
// ============================================================
// CONFIGURACIÓN PARA BASE DE DATOS EN RENDER (DESDE LOCAL)
// ============================================================
define('DB_HOST', 'dpg-d936aiojs32c73boer5g-a.oregon-postgres.render.com');
define('DB_PORT', '5432');
define('DB_NAME', 'gamehub_t2m0');
define('DB_USER', 'gamehub_t2m0_user');
define('DB_PASS', 'KhbeB8dHES31FFX1NbAu00bz15TQWp7s');

// URL del servicio Flask (aún no creado)
define('FLASK_API_URL', 'http://localhost:5000/api/resena');

// ============================================================
// FUNCIÓN PARA CONECTAR A POSTGRESQL (PDO)
// ============================================================
function obtenerConexion() {
    try {
        // DSN con sslmode=allow (funciona sin SNI)
        $dsn = sprintf(
            "pgsql:host=%s;port=%s;dbname=%s;sslmode=allow",
            DB_HOST,
            DB_PORT,
            DB_NAME
        );

        $opciones = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
        return $pdo;

    } catch (PDOException $e) {
        die("❌ Error de conexión a la base de datos: " . $e->getMessage());
    }
}
?><?php
// ============================================================
// CONEXIÓN PDO A POSTGRESQL (LOCAL Y PRODUCCIÓN EN RENDER)
// ============================================================

// Credenciales comunes
define('DB_PORT', '5432');
define('DB_NAME', 'gamehub_t2m0');
define('DB_USER', 'gamehub_t2m0_user');
define('DB_PASS', 'KhbeB8dHES31FFX1NbAu00bz15TQWp7s');

// URL del servicio Flask (en Railway). Cambia por la URL real cuando la tengas.
define('FLASK_API_URL', 'https://tu-app-flask.railway.app/api/resena');

// ============================================================
// DETECCIÓN DE ENTORNO
// ============================================================
// Render inyecta automáticamente la variable RENDER en producción
$isProduction = (getenv('RENDER') !== false);

if ($isProduction) {
    // ============================================================
    // ENTORNO DE PRODUCCIÓN (RENDER) - CONEXIÓN INTERNA
    // ============================================================
    define('DB_HOST', 'dpg-d936aiojs32c73boer5g-a'); // Hostname interno

    // DSN sin certificado (la conexión es interna y segura)
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

    // DSN con verificación SSL y certificado
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
        die("❌ Error de conexión: " . $e->getMessage());
    }
}
?>