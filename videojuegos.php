<?php
// Incluimos la conexión a la base de datos
require_once 'conexion.php';

// Obtenemos la conexión
$pdo = obtenerConexion();

// Consultamos todos los videojuegos (ordenados por título)
$sql = "SELECT id, titulo, genero, plataforma, anio_lanzamiento 
        FROM videojuegos 
        ORDER BY titulo ASC";
$stmt = $pdo->query($sql);
$juegos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>GameHub - Videojuegos</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="contenedor">
        <h1>📋 Videojuegos</h1>
        <a href="index.php" class="boton-volver">← Volver al inicio</a>

        <hr>

        <!-- ===== FORMULARIO PARA REGISTRAR NUEVO VIDEOJUEGO ===== -->
        <h2>Registrar nuevo videojuego</h2>
        <form action="guardar_videojuego.php" method="POST">
            <div class="campo">
                <label for="titulo">Título *</label>
                <input type="text" name="titulo" id="titulo" required placeholder="Ej: The Witcher 3">
            </div>
            <div class="campo">
                <label for="genero">Género</label>
                <input type="text" name="genero" id="genero" placeholder="Ej: RPG, Acción, Aventura">
            </div>
            <div class="campo">
                <label for="plataforma">Plataforma</label>
                <input type="text" name="plataforma" id="plataforma" placeholder="Ej: PC, PS5, Xbox, Switch">
            </div>
            <div class="campo">
                <label for="anio">Año de lanzamiento</label>
                <input type="number" name="anio" id="anio" placeholder="Ej: 2020" min="1980" max="2030">
            </div>
            <button type="submit" class="boton">Guardar videojuego</button>
        </form>

        <hr>

        <!-- ===== LISTADO DE VIDEOJUEGOS ===== -->
        <h2>Lista de videojuegos</h2>
        <?php if (count($juegos) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Género</th>
                        <th>Plataforma</th>
                        <th>Año</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($juegos as $juego): ?>
                    <tr>
                        <td><?= htmlspecialchars($juego['id']) ?></td>
                        <td><?= htmlspecialchars($juego['titulo']) ?></td>
                        <td><?= htmlspecialchars($juego['genero']) ?></td>
                        <td><?= htmlspecialchars($juego['plataforma']) ?></td>
                        <td><?= htmlspecialchars($juego['anio_lanzamiento']) ?></td>
                        <td>
                            <a href="resenas.php?id=<?= $juego['id'] ?>" class="enlace-ver">Ver reseñas</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay videojuegos registrados. ¡Agrega uno usando el formulario!</p>
        <?php endif; ?>
    </div>
</body>
</html>