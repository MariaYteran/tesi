<?php
$sql = file_get_contents(__DIR__ . '/migracion_multiclinica.sql');
$statements = explode(';', $sql);
$conexion = mysqli_connect('localhost', 'root', '', 'cheetos_paws');
$count = 0;
foreach ($statements as $stmt) {
    $stmt = trim($stmt);
    if (empty($stmt)) continue;
    if (mysqli_query($conexion, $stmt)) {
        $count++;
        echo "OK: " . substr($stmt, 0, 80) . "\n";
    } else {
        echo "ERROR: " . mysqli_error($conexion) . "\n  SQL: " . substr($stmt, 0, 80) . "\n";
    }
}
echo "\nTotal statements executed: $count\n";
