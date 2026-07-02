<?php
require_once 'conexion.php';

$pdo = obtenerConexion();
$stmt = $pdo->query("SELECT COUNT(*) as total FROM videojuegos");
$result = $stmt->fetch();

echo "✅ Conexión exitosa. Total de videojuegos: " . $result['total'];
?>