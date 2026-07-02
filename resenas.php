<?php
require_once 'conexion.php';

$pdo = obtenerConexion();

// ============================================================
// 1. OBTENER LISTA DE VIDEOJUEGOS PARA EL SELECTOR DEL FORMULARIO
// ============================================================
$sqlJuegos = "SELECT id, titulo FROM videojuegos ORDER BY titulo ASC";
$stmtJuegos = $pdo->query($sqlJuegos);
$juegosParaSelect = $stmtJuegos->fetchAll();

// ============================================================
// 2. OBTENER LISTA DE RESEÑAS (CON FILTRO OPCIONAL POR ID_VIDEOJUEGO)
// ============================================================
$filtroId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($filtroId > 0) {
    // Reseñas de un solo juego
    $sqlResenas = "SELECT r.id, r.calificacion, r.comentario, r.fecha, v.titulo 
                   FROM resenas r
                   JOIN videojuegos v ON r.id_videojuego = v.id
                   WHERE r.id_videojuego = :id
                   ORDER BY r.fecha DESC";
    $stmtResenas = $pdo->prepare($sqlResenas);
    $stmtResenas->execute([':id' => $filtroId]);
} else {
    // Todas las reseñas
    $sqlResenas = "SELECT r.id, r.calificacion, r.comentario, r.fecha, v.titulo 
                   FROM resenas r
                   JOIN videojuegos v ON r.id_videojuego = v.id
                   ORDER BY r.fecha DESC";
    $stmtResenas = $pdo->query($sqlResenas);
}
$resenas = $stmtResenas->fetchAll();

// ============================================================
// 3. OBTENER EL NOMBRE DEL JUEGO FILTRADO (para mostrar en el título)
// ============================================================
$nombreJuegoFiltrado = '';
if ($filtroId > 0) {
    $sqlNombre = "SELECT titulo FROM videojuegos WHERE id = :id";
    $stmtNombre = $pdo->prepare($sqlNombre);
    $stmtNombre->execute([':id' => $filtroId]);
    $juego = $stmtNombre->fetch();
    if ($juego) {
        $nombreJuegoFiltrado = $juego['titulo'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>GameHub - Reseñas</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="contenedor">
        <h1>⭐ Reseñas</h1>
        <a href="index.php" class="boton-volver">← Volver al inicio</a>

        <?php if ($nombreJuegoFiltrado): ?>
            <p><strong>Filtrando por:</strong> <?= htmlspecialchars($nombreJuegoFiltrado) ?> 
            <a href="resenas.php" class="enlace-ver">[Mostrar todas]</a></p>
        <?php endif; ?>

        <hr>

        <!-- ===== FORMULARIO PARA REGISTRAR NUEVA RESEÑA ===== -->
        <h2>Registrar nueva reseña</h2>
        <form action="guardar_resena.php" method="POST">
            <div class="campo">
                <label for="id_videojuego">Videojuego *</label>
                <select name="id_videojuego" id="id_videojuego" required>
                    <option value="">-- Selecciona un juego --</option>
                    <?php foreach ($juegosParaSelect as $juego): ?>
                        <option value="<?= $juego['id'] ?>" <?= ($filtroId == $juego['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($juego['titulo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="calificacion">Calificación * (1-5)</label>
                <select name="calificacion" id="calificacion" required>
                    <option value="">-- Elige --</option>
                    <option value="1">1 - Muy malo</option>
                    <option value="2">2 - Malo</option>
                    <option value="3">3 - Regular</option>
                    <option value="4" selected>4 - Bueno</option>
                    <option value="5">5 - Excelente</option>
                </select>
            </div>
            <div class="campo">
                <label for="comentario">Comentario</label>
                <textarea name="comentario" id="comentario" rows="3" placeholder="Escribe tu opinión..."></textarea>
            </div>
            <button type="submit" class="boton">Guardar reseña</button>
        </form>

        <hr>

        <!-- ===== LISTADO DE RESEÑAS ===== -->
        <h2>Lista de reseñas</h2>
        <?php if (count($resenas) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Videojuego</th>
                        <th>Calificación</th>
                        <th>Comentario</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resenas as $resena): ?>
                    <tr>
                        <td><?= htmlspecialchars($resena['id']) ?></td>
                        <td><?= htmlspecialchars($resena['titulo']) ?></td>
                        <td><?= str_repeat('⭐', $resena['calificacion']) ?> (<?= $resena['calificacion'] ?>)</td>
                        <td><?= htmlspecialchars($resena['comentario']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($resena['fecha'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay reseñas registradas para este filtro. ¡Escribe la primera!</p>
        <?php endif; ?>
    </div>
</body>
</html>