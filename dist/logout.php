<?php
session_start();
$rol = $_SESSION['usuario']['rol'] ?? '';
session_destroy();
$destino = ($rol === 'admin') ? '/dist/login.php' : '/dist/login_usuarios.php';
header("Location: $destino");
exit();
?>
