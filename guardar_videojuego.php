<?php
// Incluimos la conexión
require_once 'conexion.php';

// Verificamos que el formulario se haya enviado con método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Si alguien accede directamente a este archivo, lo redirigimos al formulario
    header('Location: videojuegos.php');
    exit;
}

// Obtenemos los datos del formulario
$titulo = trim($_POST['titulo'] ?? '');
$genero = trim($_POST['genero'] ?? '');
$plataforma = trim($_POST['plataforma'] ?? '');
$anio = trim($_POST['anio'] ?? '');

// Validación básica: el título es obligatorio
if (empty($titulo)) {
    // Redirige con mensaje de error
    header('Location: videojuegos.php?error=El título es obligatorio');
    exit;
}

// Si el año está vacío, lo ponemos como NULL en la base de datos
if ($anio === '') {
    $anio = null;
}

try {
    // Conectamos a PostgreSQL
    $pdo = obtenerConexion();
    
    // Sentencia SQL con marcadores de posición (seguro contra inyección SQL)
    $sql = "INSERT INTO videojuegos (titulo, genero, plataforma, anio_lanzamiento) 
            VALUES (:titulo, :genero, :plataforma, :anio)";
    
    $stmt = $pdo->prepare($sql);
    
    // Ejecutamos pasando los valores en un array asociativo
    $stmt->execute([
        ':titulo' => $titulo,
        ':genero' => $genero,
        ':plataforma' => $plataforma,
        ':anio' => $anio
    ]);
    
    // Si llegamos aquí, la inserción fue exitosa
    header('Location: videojuegos.php?exito=Videojuego registrado correctamente');
    exit;
    
} catch (PDOException $e) {
    // Si hay un error (ej: campo duplicado, problema de conexión, etc.)
    // Mostramos el mensaje de error en la URL (pero cuidado con inyección, mejor usamos un código)
    // Aquí lo simplificamos mostrando el error tal cual, pero en producción no se debe hacer.
    header('Location: videojuegos.php?error=Error al guardar: ' . urlencode($e->getMessage()));
    exit;
}
?>