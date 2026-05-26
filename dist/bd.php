<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$conexion = mysqli_connect("localhost", "root", "", "cheetos_paws");
$RIF_clinica = mysqli_real_escape_string($conexion, $_SESSION['usuario']['RIF_clinica'] ?? '');
$es_admin = ($_SESSION['usuario']['rol'] ?? '') === 'admin';
?>