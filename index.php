<?php
session_start();
if (isset($_SESSION['usuario'])) {
    require __DIR__ . '/app.php';
} else {
    require __DIR__ . '/landing.php';
}
