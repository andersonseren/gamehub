<?php
require_once 'conexion.php';

// Verificar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: resenas.php');
    exit;
}

// Obtener y limpiar datos
$id_videojuego = (int)($_POST['id_videojuego'] ?? 0);
$calificacion = (int)($_POST['calificacion'] ?? 0);
$comentario = trim($_POST['comentario'] ?? '');

// Validaciones
if ($id_videojuego <= 0 || $calificacion < 1 || $calificacion > 5) {
    header('Location: resenas.php?error=Datos inválidos (juego o calificación)');
    exit;
}

try {
    $pdo = obtenerConexion();

    // --- 1. Verificar que el videojuego existe y obtener su título ---
    $sqlCheck = "SELECT id, titulo FROM videojuegos WHERE id = :id";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([':id' => $id_videojuego]);
    $juego = $stmtCheck->fetch();

    if (!$juego) {
        header('Location: resenas.php?error=El videojuego seleccionado no existe');
        exit;
    }

    // --- 2. Insertar la reseña en PostgreSQL ---
    $sqlInsert = "INSERT INTO resenas (id_videojuego, calificacion, comentario) 
                  VALUES (:id_videojuego, :calificacion, :comentario)";
    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->execute([
        ':id_videojuego' => $id_videojuego,
        ':calificacion' => $calificacion,
        ':comentario' => $comentario
    ]);

    // --- 3. Preparar los datos para enviar a Flask ---
    $datosFlask = [
        'id_videojuego' => $id_videojuego,
        'titulo' => $juego['titulo'],
        'calificacion' => $calificacion,
        'comentario' => $comentario,
        'fecha' => date('c') // Fecha en formato ISO 8601 (ej: 2026-07-02T15:30:00Z)
    ];

    // Convertir a JSON
    $jsonData = json_encode($datosFlask);

    // --- 4. Enviar a Flask (sin importar si falla) ---
    $url = FLASK_API_URL; // Definida en conexion.php

    // Configurar el contexto de la petición HTTP
    $opciones = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => $jsonData,
            'timeout' => 5 // Timeout de 5 segundos para no bloquear la página
        ]
    ];
    $contexto = stream_context_create($opciones);

    // Enviar la petición. Ignoramos el resultado para no interrumpir el flujo.
    @file_get_contents($url, false, $contexto);

    // La reseña ya está guardada en PostgreSQL. Redirigimos con éxito.
    header('Location: resenas.php?exito=Reseña guardada correctamente');
    exit;

} catch (PDOException $e) {
    // Si hay error en PostgreSQL, redirigimos con el mensaje
    header('Location: resenas.php?error=Error en BD: ' . urlencode($e->getMessage()));
    exit;
} catch (Exception $e) {
    // Cualquier otro error
    header('Location: resenas.php?error=Error: ' . urlencode($e->getMessage()));
    exit;
}
?>