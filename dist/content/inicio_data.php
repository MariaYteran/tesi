<?php
include __DIR__ . '/../bd.php';
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function obtenerTasaBCV() {
    $cacheDir = __DIR__ . '/../../cache';
    if (!is_dir($cacheDir)) @mkdir($cacheDir, 0755, true);
    $cacheFile = $cacheDir . '/bcv_rate.json';
    $cacheTime = 43200; // 12 horas

    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
        $data = json_decode(file_get_contents($cacheFile), true);
        return (float)($data['tasa'] ?? 0);
    }

    $tasa = 0;
    $ch = @curl_init('https://ve.dolarapi.com/v1/dolares/oficial');
    if ($ch) {
        @curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'CheetosPaws/1.0'
        ]);
        $response = @curl_exec($ch);
        $httpCode = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
        @curl_close($ch);
        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            $tasa = (float)($data['promedio'] ?? 0);
        }
    }

    // Fallback a cache existente si API falla
    if ($tasa <= 0 && file_exists($cacheFile)) {
        $data = json_decode(file_get_contents($cacheFile), true);
        $tasa = (float)($data['tasa'] ?? 0);
    }

    if ($tasa > 0) {
        @file_put_contents($cacheFile, json_encode([
            'tasa' => $tasa,
            'fecha' => $data['fecha'] ?? date('c'),
            'actualizado' => date('Y-m-d H:i:s')
        ]));
    }

    return $tasa;
}

if ($action === 'semanal') {
    $lunes = date('Y-m-d', strtotime('monday this week'));
    $domingo = date('Y-m-d', strtotime('sunday this week'));

    $diasMap = [0 => 'L', 1 => 'M', 2 => 'M', 3 => 'J', 4 => 'V', 5 => 'S', 6 => 'D'];
    $dias = [];
    for ($i = 0; $i <= 6; $i++) {
        $dias[$i] = ['label' => $diasMap[$i], 'perros' => 0, 'gatos' => 0, 'emergencias' => 0];
    }

    $q = mysqli_query($conexion, "
        SELECT WEEKDAY(co.fecha) as weekday, m.especie, COUNT(*) as total
        FROM consulta co
        JOIN mascota m ON co.id_mascota = m.id_mascota
        WHERE co.RIF_clinica='$RIF_clinica' AND co.fecha >= '$lunes' AND co.fecha <= '$domingo'
        GROUP BY WEEKDAY(co.fecha), m.especie
    ");

    while ($r = mysqli_fetch_assoc($q)) {
        $w = (int)$r['weekday'];
        if ($r['especie'] === 'Perro') {
            $dias[$w]['perros'] = (int)$r['total'];
        } elseif ($r['especie'] === 'Gato') {
            $dias[$w]['gatos'] = (int)$r['total'];
        }
    }

    $q2 = mysqli_query($conexion, "
        SELECT WEEKDAY(c.fecha) as weekday, COUNT(*) as total
        FROM citas c
        WHERE c.RIF_clinica='$RIF_clinica' AND c.estado = 'completada' AND c.es_emergencia = 1
          AND c.fecha >= '$lunes' AND c.fecha <= '$domingo'
        GROUP BY WEEKDAY(c.fecha)
    ");

    while ($r = mysqli_fetch_assoc($q2)) {
        $dias[(int)$r['weekday']]['emergencias'] = (int)$r['total'];
    }

    echo json_encode(array_values($dias));
    exit;
}

if ($action === 'hoy') {
    $hoy = date('Y-m-d');
    $q = mysqli_query($conexion, "
        SELECT c.id_mascota, m.nombre, c.hora, c.motivo, m.especie
        FROM citas c
        JOIN mascota m ON c.id_mascota = m.id_mascota
        WHERE c.RIF_clinica='$RIF_clinica' AND c.fecha = '$hoy' AND c.estado = 'pendiente'
        ORDER BY c.hora ASC
    ");

    $citas = [];
    while ($r = mysqli_fetch_assoc($q)) {
        $citas[] = [
            'id_mascota' => $r['id_mascota'],
            'nombre'     => $r['nombre'],
            'hora'       => $r['hora'] ? substr($r['hora'], 0, 5) : '--:--',
            'motivo'     => $r['motivo'],
            'especie'    => $r['especie']
        ];
    }

    echo json_encode($citas);
    exit;
}

if ($action === 'mes') {
    $mes = (int)($_GET['mes'] ?? date('n'));
    $anio = (int)($_GET['año'] ?? date('Y'));
    $primer = "$anio-$mes-01";
    $ultimo = date('Y-m-t', strtotime($primer));

    $citas = [];
    $q = mysqli_query($conexion, "
        SELECT DISTINCT DAY(fecha) as d
        FROM citas
        WHERE RIF_clinica='$RIF_clinica' AND fecha >= '$primer' AND fecha <= '$ultimo'
    ");
    while ($r = mysqli_fetch_assoc($q)) {
        $citas[] = (int)$r['d'];
    }

    $actividades = [];
    $q2 = mysqli_query($conexion, "
        SELECT DISTINCT DAY(fecha) as d
        FROM actividad
        WHERE RIF_clinica='$RIF_clinica' AND fecha >= '$primer' AND fecha <= '$ultimo'
    ");
    while ($r = mysqli_fetch_assoc($q2)) {
        $actividades[] = (int)$r['d'];
    }

    echo json_encode(['citas' => $citas, 'actividades' => $actividades]);
    exit;
}

if ($action === 'dia') {
    $fecha = $_GET['fecha'] ?? '';

    $citas = [];
    $q = mysqli_query($conexion, "
        SELECT c.id_cita, c.id_veterinario, c.hora, m.nombre, m.especie, c.motivo
        FROM citas c
        JOIN mascota m ON c.id_mascota = m.id_mascota
        WHERE c.RIF_clinica='$RIF_clinica' AND c.fecha = '$fecha'
        ORDER BY c.hora ASC
    ");
    while ($r = mysqli_fetch_assoc($q)) {
        $citas[] = [
            'id_cita'   => (int)$r['id_cita'],
            'id_veterinario' => $r['id_veterinario'],
            'hora'    => $r['hora'] ? substr($r['hora'], 0, 5) : '--:--',
            'nombre'  => $r['nombre'],
            'especie' => $r['especie'],
            'motivo'  => $r['motivo']
        ];
    }

    $actividades = [];
    $q2 = mysqli_query($conexion, "
        SELECT titulo, descripcion
        FROM actividad
        WHERE RIF_clinica='$RIF_clinica' AND fecha = '$fecha'
        ORDER BY id_actividad ASC
    ");
    while ($r = mysqli_fetch_assoc($q2)) {
        $actividades[] = [
            'titulo'      => $r['titulo'],
            'descripcion' => $r['descripcion']
        ];
    }

    echo json_encode(['citas' => $citas, 'actividades' => $actividades]);
    exit;
}

if ($action === 'actividad') {
    $fecha = mysqli_real_escape_string($conexion, $_POST['fecha'] ?? '');
    $titulo = mysqli_real_escape_string($conexion, $_POST['titulo'] ?? '');
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion'] ?? '');
    if (empty($fecha) || empty($titulo)) {
        echo json_encode(['success' => false, 'message' => 'Fecha y título son obligatorios']);
        exit;
    }
    if ($fecha < date('Y-m-d')) {
        echo json_encode(['success' => false, 'message' => 'No se pueden registrar actividades en fechas pasadas']);
        exit;
    }
    $ins = mysqli_query($conexion, "INSERT INTO actividad (titulo, descripcion, fecha, RIF_clinica) VALUES ('$titulo', '$descripcion', '$fecha', '$RIF_clinica')");
    if ($ins) {
        echo json_encode(['success' => true, 'message' => 'Actividad guardada']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . mysqli_error($conexion)]);
    }
    exit;
}

if ($action === 'cargar_cita') {
    $idc = intval($_GET['id'] ?? 0);
    $res = mysqli_query($conexion, "SELECT c.*, m.nombre AS mascota_nombre, m.especie, v.Nombres AS nombre_veterinario FROM citas c JOIN mascota m ON c.id_mascota = m.id_mascota LEFT JOIN veterinario v ON c.id_veterinario = v.Id_veterinario WHERE c.RIF_clinica='$RIF_clinica' AND c.id_cita=$idc LIMIT 1");
    $r = mysqli_fetch_assoc($res);
    echo json_encode($r ?: []);
    exit;
}

if ($action === 'citas_por_fecha') {
    $fecha = mysqli_real_escape_string($conexion, $_GET['fecha'] ?? '');
    if (!$fecha) { echo json_encode([]); exit; }
    $res = mysqli_query($conexion, "
        SELECT c.id_cita, c.id_mascota, c.id_veterinario, c.hora, c.motivo,
               m.nombre AS nombre_mascota, m.id_propietario
        FROM citas c
        JOIN mascota m ON c.id_mascota = m.id_mascota
        WHERE c.RIF_clinica='$RIF_clinica' AND c.fecha = '$fecha' AND c.estado = 'pendiente'
        ORDER BY c.hora ASC
    ");
    $citas = [];
    while ($r = mysqli_fetch_assoc($res)) { $citas[] = $r; }
    echo json_encode($citas);
    exit;
}

if ($action === 'verificar_fecha') {
    $fecha = mysqli_real_escape_string($conexion, $_GET['fecha'] ?? '');
    if (!$fecha) { echo json_encode(['blocked' => false]); exit; }
    if ($fecha < date('Y-m-d')) {
        echo json_encode(['blocked' => true, 'mensaje' => 'No se pueden agendar citas en fechas pasadas']);
        exit;
    }
    $actCheck = mysqli_query($conexion, "SELECT titulo FROM actividad WHERE RIF_clinica='$RIF_clinica' AND fecha='$fecha' LIMIT 1");
    if (mysqli_num_rows($actCheck) > 0) {
        $act = mysqli_fetch_assoc($actCheck);
        echo json_encode(['blocked' => true, 'mensaje' => 'Fecha bloqueada por actividad: ' . $act['titulo']]);
        exit;
    }
    echo json_encode(['blocked' => false]);
    exit;
}

if ($action === 'emergencia') {
    $id_mascota = mysqli_real_escape_string($conexion, $_POST['id_mascota'] ?? '');
    $id_veterinario = !empty($_POST['id_veterinario']) ? "'" . mysqli_real_escape_string($conexion, $_POST['id_veterinario']) . "'" : 'NULL';
    $diagnostico = !empty($_POST['diagnostico']) ? "'" . mysqli_real_escape_string($conexion, $_POST['diagnostico']) . "'" : 'NULL';
    $hoy = date('Y-m-d');
    if (!$id_mascota) {
        echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios']);
        exit;
    }
    $q = "INSERT INTO citas (id_mascota, id_veterinario, fecha, hora, motivo, estado, es_emergencia, RIF_clinica)
          VALUES ('$id_mascota', $id_veterinario, '$hoy', NULL, $diagnostico, 'completada', 1, '$RIF_clinica')";
    $ok = mysqli_query($conexion, $q);
    echo json_encode(['success' => (bool)$ok, 'message' => $ok ? 'Emergencia registrada correctamente' : 'Error: ' . mysqli_error($conexion)]);
    exit;
}

if ($action === 'actualizar_cita') {
    $idc = intval($_POST['id_cita'] ?? 0);
    $id_mascota = mysqli_real_escape_string($conexion, $_POST['id_mascota'] ?? '');
    $id_veterinario = !empty($_POST['id_veterinario']) ? "'" . mysqli_real_escape_string($conexion, $_POST['id_veterinario']) . "'" : 'NULL';
    $fecha = mysqli_real_escape_string($conexion, $_POST['fecha'] ?? '');
    $hora = !empty($_POST['hora']) ? "'" . mysqli_real_escape_string($conexion, $_POST['hora']) . "'" : 'NULL';
    $motivo = !empty($_POST['motivo']) ? "'" . mysqli_real_escape_string($conexion, $_POST['motivo']) . "'" : 'NULL';
    if (!$idc || !$id_mascota || !$fecha) {
        echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios']);
        exit;
    }
    $check = mysqli_query($conexion, "SELECT fecha FROM citas WHERE RIF_clinica='$RIF_clinica' AND id_cita=$idc");
    $cur = mysqli_fetch_assoc($check);
    if ($cur && $cur['fecha'] < date('Y-m-d')) {
        echo json_encode(['success' => false, 'message' => 'No se puede editar una cita pasada']);
        exit;
    }
    if ($fecha < date('Y-m-d')) {
        echo json_encode(['success' => false, 'message' => 'No se pueden agendar citas en fechas pasadas']);
        exit;
    }
    $actCheck = mysqli_query($conexion, "SELECT id_actividad FROM actividad WHERE RIF_clinica='$RIF_clinica' AND fecha='$fecha' LIMIT 1");
    if (mysqli_num_rows($actCheck) > 0) {
        echo json_encode(['success' => false, 'message' => 'Fecha bloqueada por una actividad']);
        exit;
    }
    $qr = mysqli_query($conexion, "UPDATE citas SET id_mascota='$id_mascota', id_veterinario=$id_veterinario, fecha='$fecha', hora=$hora, motivo=$motivo WHERE RIF_clinica='$RIF_clinica' AND id_cita=$idc");
    echo json_encode(['success' => (bool)$qr, 'message' => $qr ? 'Cita actualizada correctamente' : 'Error: ' . mysqli_error($conexion)]);
    exit;
}

if ($action === 'eliminar_cita') {
    $idc = intval($_POST['id_cita'] ?? 0);
    if (!$idc) {
        echo json_encode(['success' => false, 'message' => 'ID de cita no valido']);
        exit;
    }
    $check = mysqli_query($conexion, "SELECT fecha FROM citas WHERE RIF_clinica='$RIF_clinica' AND id_cita=$idc");
    $r = mysqli_fetch_assoc($check);
    if ($r && $r['fecha'] < date('Y-m-d')) {
        echo json_encode(['success' => false, 'message' => 'No se puede cancelar una cita pasada']);
        exit;
    }
    $qr = mysqli_query($conexion, "DELETE FROM citas WHERE RIF_clinica='$RIF_clinica' AND id_cita=$idc");
    echo json_encode(['success' => (bool)$qr, 'message' => $qr ? 'Cita cancelada correctamente' : 'Error: ' . mysqli_error($conexion)]);
    exit;
}

if ($action === 'cargar_precios') {
    $res = mysqli_query($conexion, "SELECT servicio, precio FROM precios WHERE RIF_clinica='$RIF_clinica'");
    $precios = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $precios[$r['servicio']] = (float)$r['precio'];
    }
    echo json_encode($precios);
    exit;
}

if ($action === 'guardar_precios') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) {
        echo json_encode(['success' => false, 'message' => 'Datos invalidos']);
        exit;
    }
    $stmt = mysqli_prepare($conexion, "INSERT INTO precios (servicio, precio, RIF_clinica) VALUES (?, ?, '$RIF_clinica') ON DUPLICATE KEY UPDATE precio=VALUES(precio)");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Error de preparacion: ' . mysqli_error($conexion)]);
        exit;
    }
    foreach ($data as $servicio => $precio) {
        $s = mysqli_real_escape_string($conexion, $servicio);
        $p = (float)$precio;
        mysqli_query($conexion, "INSERT INTO precios (servicio, precio, RIF_clinica) VALUES ('$s', $p, '$RIF_clinica') ON DUPLICATE KEY UPDATE precio=$p");
    }
    echo json_encode(['success' => true, 'message' => 'Precios guardados correctamente']);
    exit;
}

if ($action === 'guardar_consulta') {
    $fecha = mysqli_real_escape_string($conexion, $_POST['fecha'] ?? '');
    $id_mascota = mysqli_real_escape_string($conexion, $_POST['id_mascota'] ?? '');
    $id_veterinario = mysqli_real_escape_string($conexion, $_POST['id_veterinario'] ?? '');
    $id_propietario = mysqli_real_escape_string($conexion, $_POST['id_propietario'] ?? '');
    $id_cita = $_POST['id_cita'] ?? '';
    $id_auxiliar = mysqli_real_escape_string($conexion, $_POST['id_auxiliar'] ?? '');
    $diagnostico = mysqli_real_escape_string($conexion, $_POST['diagnostico'] ?? '');
    $examen_fisico = mysqli_real_escape_string($conexion, $_POST['examen_fisico'] ?? '');
    $tests_json = $_POST['tests'] ?? '[]';
    $labs_json = $_POST['laboratorios'] ?? '[]';
    $vacs_json = $_POST['vacunas'] ?? '[]';
    $receta = mysqli_real_escape_string($conexion, $_POST['receta'] ?? '');
    $tipo_pago = mysqli_real_escape_string($conexion, $_POST['tipo_pago'] ?? '');
    $multi_pet = $_POST['multi_pet'] ?? '0';

    if (!$fecha || !$id_mascota) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios']);
        exit;
    }

    mysqli_query($conexion, "BEGIN");
    $qr = mysqli_query($conexion, "INSERT INTO consulta (fecha, id_mascota, id_veterinario, id_propietario, diagnostico, examen_fisico, receta, RIF_clinica, id_auxiliar) VALUES ('$fecha', '$id_mascota', " . ($id_veterinario ? "'$id_veterinario'" : "NULL") . ", " . ($id_propietario ? "'$id_propietario'" : "NULL") . ", '$diagnostico', '$examen_fisico', '$receta', '$RIF_clinica', " . ($id_auxiliar ? "'$id_auxiliar'" : "NULL") . ")");

    if (!$qr) {
        mysqli_query($conexion, "ROLLBACK");
        echo json_encode(['success' => false, 'message' => 'Error al guardar consulta: ' . mysqli_error($conexion)]);
        exit;
    }

    $id_consulta = mysqli_insert_id($conexion);

    $tests = json_decode($tests_json, true);
    if (is_array($tests)) {
        foreach ($tests as $t) {
            $nombre = mysqli_real_escape_string($conexion, $t);
            if ($nombre) {
                mysqli_query($conexion, "INSERT INTO test_rapidos (id_consulta, nombre, RIF_clinica) VALUES ($id_consulta, '$nombre', '$RIF_clinica')");
            }
        }
    }

    $labs = json_decode($labs_json, true);
    if (is_array($labs)) {
        foreach ($labs as $l) {
            $tipo = mysqli_real_escape_string($conexion, $l['tipo'] ?? '');
            $obs = mysqli_real_escape_string($conexion, $l['observaciones'] ?? '');
            if ($tipo) {
                mysqli_query($conexion, "INSERT INTO laboratorio (id_consulta, tipo, observaciones, RIF_clinica) VALUES ($id_consulta, '$tipo', '$obs', '$RIF_clinica')");
            }
        }
    }

    $vacs = json_decode($vacs_json, true);
    if (is_array($vacs)) {
        foreach ($vacs as $v) {
            $nombre = mysqli_real_escape_string($conexion, $v['nombre'] ?? '');
            $obs = mysqli_real_escape_string($conexion, $v['observaciones'] ?? '');
            if ($nombre) {
                mysqli_query($conexion, "INSERT INTO vacunas (id_consulta, nombre, observaciones, RIF_clinica) VALUES ($id_consulta, '$nombre', '$obs', '$RIF_clinica')");
            }
        }
    }

    if ($id_cita) {
        $idc = intval($id_cita);
        mysqli_query($conexion, "UPDATE citas SET estado='completada' WHERE RIF_clinica='$RIF_clinica' AND id_cita=$idc");
    }

    // Si es multi-pet, marcar como completadas las citas pendientes de esta mascota en esta fecha
    if ($multi_pet === '1') {
        $idm_esc = mysqli_real_escape_string($conexion, $id_mascota);
        mysqli_query($conexion, "UPDATE citas SET estado='completada' WHERE RIF_clinica='$RIF_clinica' AND id_mascota='$idm_esc' AND fecha='$fecha' AND estado='pendiente'");
    }

    mysqli_query($conexion, "COMMIT");

    // Insert into ventas (solo para modo single, multi-pet se consolida al final)
    if ($multi_pet !== '1') {
        $total = 0.00;
        $items = [];
        $pr = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT precio FROM precios WHERE RIF_clinica='$RIF_clinica' AND servicio='Consulta General' LIMIT 1"));
        $precio_consulta = (float)($pr['precio'] ?? 0);
        $total += $precio_consulta;
        $items[] = ['servicio' => 'Consulta General', 'precio' => $precio_consulta];
        foreach ($tests as $t) {
            $sn = mysqli_real_escape_string($conexion, $t);
            $pr = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT precio FROM precios WHERE RIF_clinica='$RIF_clinica' AND servicio='$sn' LIMIT 1"));
            $p = (float)($pr['precio'] ?? 0);
            $total += $p;
            $items[] = ['servicio' => $t, 'precio' => $p];
        }
        foreach ($labs as $l) {
            $sn = mysqli_real_escape_string($conexion, $l['tipo'] ?? '');
            $pr = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT precio FROM precios WHERE RIF_clinica='$RIF_clinica' AND servicio='$sn' LIMIT 1"));
            $p = (float)($pr['precio'] ?? 0);
            $total += $p;
            $items[] = ['servicio' => $l['tipo'], 'precio' => $p];
        }
        foreach ($vacs as $v) {
            $sn = mysqli_real_escape_string($conexion, $v['nombre'] ?? '');
            $pr = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT precio FROM precios WHERE RIF_clinica='$RIF_clinica' AND servicio='$sn' LIMIT 1"));
            $p = (float)($pr['precio'] ?? 0);
            $total += $p;
            $items[] = ['servicio' => $v['nombre'], 'precio' => $p];
        }
        $servicios_json = mysqli_real_escape_string($conexion, json_encode($items, JSON_UNESCAPED_UNICODE));
        $id_mascota_esc = mysqli_real_escape_string($conexion, $id_mascota);
        $id_prop_esc = $id_propietario ? "'" . mysqli_real_escape_string($conexion, $id_propietario) . "'" : 'NULL';
        $tipo_pago_esc = $tipo_pago ? "'$tipo_pago'" : 'NULL';
        mysqli_query($conexion, "INSERT INTO ventas (id_consulta, fecha, id_mascota, id_propietario, servicios, total, tipo_pago, RIF_clinica) VALUES ($id_consulta, '$fecha', '$id_mascota_esc', $id_prop_esc, '$servicios_json', $total, $tipo_pago_esc, '$RIF_clinica')");
    }

    $tasaBCV = obtenerTasaBCV();
    $totalBs = $tasaBCV > 0 && $multi_pet !== '1' ? round($total * $tasaBCV, 2) : 0;
    echo json_encode(['success' => true, 'message' => 'Consulta guardada exitosamente', 'id_consulta' => $id_consulta, 'tasa_bcv' => $tasaBCV, 'total_bs' => $totalBs]);
    exit;
}

if ($action === 'consolidar_factura_multi') {
    $ids = json_decode($_POST['ids'] ?? '[]', true);
    $ids = array_map('intval', $ids);
    if (empty($ids)) { echo json_encode(['success'=>false,'message'=>'Sin consultas']); exit; }
    $ids_str = implode(',', $ids);
    $res = mysqli_query($conexion, "SELECT c.id_consulta, c.fecha, c.id_propietario, m.nombre AS mascota_nombre, p.nombres AS prop_nombre, p.apellidos AS prop_apellido, v.Nombres AS nombre_veterinario FROM consulta c JOIN mascota m ON c.id_mascota = m.id_mascota LEFT JOIN propietario p ON c.id_propietario = p.id_propietario LEFT JOIN veterinario v ON c.id_veterinario = v.Id_veterinario WHERE c.RIF_clinica='$RIF_clinica' AND c.id_consulta IN ($ids_str)");
    $mascotas = []; $fecha = ''; $prop_nombre = ''; $prop_apellido = ''; $vet_nombre = ''; $id_prop = '';
    $servicios_agrupados = [];
    $primer_id = $ids[0];
    while ($r = mysqli_fetch_assoc($res)) {
        $mascotas[] = $r['mascota_nombre'];
        if (!$fecha) { $fecha = $r['fecha']; $prop_nombre = $r['prop_nombre']; $prop_apellido = $r['prop_apellido']; $vet_nombre = $r['nombre_veterinario']; $id_prop = $r['id_propietario']; }
        $cid = $r['id_consulta'];
        $tr = mysqli_query($conexion, "SELECT nombre FROM test_rapidos WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$cid");
        while ($t = mysqli_fetch_assoc($tr)) $servicios_agrupados[$t['nombre']] = ($servicios_agrupados[$t['nombre']] ?? 0) + 1;
        $lr = mysqli_query($conexion, "SELECT tipo FROM laboratorio WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$cid");
        while ($l = mysqli_fetch_assoc($lr)) $servicios_agrupados[$l['tipo']] = ($servicios_agrupados[$l['tipo']] ?? 0) + 1;
        $vr = mysqli_query($conexion, "SELECT nombre FROM vacunas WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$cid");
        while ($v = mysqli_fetch_assoc($vr)) $servicios_agrupados[$v['nombre']] = ($servicios_agrupados[$v['nombre']] ?? 0) + 1;
    }
    $pr = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT precio FROM precios WHERE RIF_clinica='$RIF_clinica' AND servicio='Consulta General' LIMIT 1"));
    $precio_consulta = (float)($pr['precio'] ?? 0);
    $cantidad = count($mascotas);
    $subtotal_consultas = $cantidad * $precio_consulta;
    $items = [];
    $items[] = ['_meta' => true, 'pacientes' => $mascotas, 'ids_consulta' => $ids];
    $items[] = ['servicio' => "Consulta General x $cantidad", 'precio' => $subtotal_consultas];
    foreach ($servicios_agrupados as $nombre => $cnt) {
        $sn = mysqli_real_escape_string($conexion, $nombre);
        $pr2 = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT precio FROM precios WHERE RIF_clinica='$RIF_clinica' AND servicio='$sn' LIMIT 1"));
        $p = (float)($pr2['precio'] ?? 0);
        $items[] = ['servicio' => $cnt > 1 ? "$nombre x $cnt" : $nombre, 'precio' => $p * $cnt];
    }
    $dr = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT precio FROM precios WHERE RIF_clinica='$RIF_clinica' AND servicio='Descuento Multiple' LIMIT 1"));
    $dto_pct = (float)($dr['precio'] ?? 0);
    if ($dto_pct > 0) {
        $dto_valor = round($subtotal_consultas * $dto_pct / 100, 2);
        if ($dto_valor > 0) $items[] = ['servicio' => 'Descuento múltiple', 'precio' => -$dto_valor];
    }
    $total = array_sum(array_column($items, 'precio'));
    $servicios_json = mysqli_real_escape_string($conexion, json_encode($items, JSON_UNESCAPED_UNICODE));
    $id_prop_esc = $id_prop ? "'" . mysqli_real_escape_string($conexion, $id_prop) . "'" : 'NULL';
    mysqli_query($conexion, "INSERT INTO ventas (id_consulta, fecha, id_mascota, id_propietario, servicios, total, RIF_clinica) VALUES ($primer_id, '$fecha', 'Multi', $id_prop_esc, '$servicios_json', $total, '$RIF_clinica')");
    $tasaBCV = obtenerTasaBCV();
    $totalBs = $tasaBCV > 0 ? round($total * $tasaBCV, 2) : 0;
    echo json_encode(['success'=>true, 'id_consulta'=>$primer_id, 'mascotas'=>$mascotas, 'prop_nombre'=>$prop_nombre, 'prop_apellido'=>$prop_apellido, 'fecha'=>$fecha, 'nombre_veterinario'=>$vet_nombre, 'items'=>$items, 'total'=>$total, 'tasa_bcv'=>$tasaBCV, 'total_bs'=>$totalBs, 'message'=>'Factura consolidada creada exitosamente']);
    exit;
}

if ($action === 'buscar_mascotas') {
    $q = mysqli_real_escape_string($conexion, $_GET['q'] ?? '');
    $res = mysqli_query($conexion, "SELECT id_mascota, nombre, especie, raza, edad, sexo, peso, id_propietario FROM mascota WHERE RIF_clinica='$RIF_clinica' AND (id_mascota LIKE '%$q%' OR nombre LIKE '%$q%' OR especie LIKE '%$q%' OR raza LIKE '%$q%') ORDER BY id_mascota ASC LIMIT 20");
    $lista = [];
    while ($r = mysqli_fetch_assoc($res)) { $lista[] = $r; }
    echo json_encode($lista);
    exit;
}

if ($action === 'buscar_veterinario') {
    $q = mysqli_real_escape_string($conexion, $_GET['q'] ?? '');
    $res = mysqli_query($conexion, "SELECT Id_veterinario, Nombres FROM veterinario WHERE RIF_clinica='$RIF_clinica' AND (Id_veterinario LIKE '%$q%' OR Nombres LIKE '%$q%') ORDER BY Id_veterinario ASC LIMIT 20");
    $lista = [];
    while ($r = mysqli_fetch_assoc($res)) { $lista[] = $r; }
    echo json_encode($lista);
    exit;
}

if ($action === 'buscar_auxiliar') {
    $q = mysqli_real_escape_string($conexion, $_GET['q'] ?? '');
    $res = mysqli_query($conexion, "SELECT id_auxiliar, nombres, apellidos FROM `aux-vet` WHERE RIF_clinica='$RIF_clinica' AND id_auxiliar LIKE '%$q%' ORDER BY id_auxiliar ASC LIMIT 20");
    $lista = [];
    while ($r = mysqli_fetch_assoc($res)) { $lista[] = $r; }
    echo json_encode($lista);
    exit;
}

if ($action === 'buscar_propietarios') {
    $q = mysqli_real_escape_string($conexion, $_GET['q'] ?? '');
    $res = mysqli_query($conexion, "SELECT id_propietario, nombres, apellidos, telefono FROM propietario WHERE RIF_clinica='$RIF_clinica' AND (id_propietario LIKE '%$q%' OR nombres LIKE '%$q%' OR apellidos LIKE '%$q%' OR telefono LIKE '%$q%') ORDER BY id_propietario ASC LIMIT 20");
    $lista = [];
    while ($r = mysqli_fetch_assoc($res)) { $lista[] = $r; }
    echo json_encode(['propietarios' => $lista]);
    exit;
}

if ($action === 'mascotas_por_propietario') {
    $id = mysqli_real_escape_string($conexion, $_GET['id'] ?? '');
    if (($_SESSION['usuario']['rol'] ?? '') === 'propietario') {
        $id = mysqli_real_escape_string($conexion, $_SESSION['usuario']['id_propietario'] ?? '');
    }
    $res = mysqli_query($conexion, "SELECT id_mascota, nombre, especie, raza, edad, sexo, peso FROM mascota WHERE RIF_clinica='$RIF_clinica' AND id_propietario='$id' ORDER BY id_mascota ASC");
    $lista = [];
    while ($r = mysqli_fetch_assoc($res)) { $lista[] = $r; }
    echo json_encode($lista);
    exit;
}

if ($action === 'datos_mascota') {
    $id = mysqli_real_escape_string($conexion, $_GET['id'] ?? '');
    $extra_prop = '';
    if (($_SESSION['usuario']['rol'] ?? '') === 'propietario') {
        $id_prop = mysqli_real_escape_string($conexion, $_SESSION['usuario']['id_propietario'] ?? '');
        $extra_prop = " AND m.id_propietario='$id_prop'";
    }
    $res = mysqli_query($conexion, "SELECT m.*, p.nombres AS prop_nombre, p.apellidos AS prop_apellido, p.telefono AS prop_telefono, p.gmail AS prop_gmail FROM mascota m LEFT JOIN propietario p ON m.id_propietario = p.id_propietario WHERE m.RIF_clinica='$RIF_clinica' AND m.id_mascota='$id'$extra_prop LIMIT 1");
    $r = mysqli_fetch_assoc($res);
    echo json_encode($r ?: []);
    exit;
}

if ($action === 'consultas_mascota') {
    $id = mysqli_real_escape_string($conexion, $_GET['id'] ?? '');
    $extra_prop = '';
    if (($_SESSION['usuario']['rol'] ?? '') === 'propietario') {
        $id_prop = mysqli_real_escape_string($conexion, $_SESSION['usuario']['id_propietario'] ?? '');
        $extra_prop = " AND m.id_propietario='$id_prop'";
    }
    // Regular consultations
    $res = mysqli_query($conexion, "SELECT c.id_consulta, c.fecha, c.diagnostico, c.examen_fisico, c.id_veterinario, v.Nombres AS nombre_veterinario FROM consulta c JOIN mascota m ON c.id_mascota = m.id_mascota AND m.RIF_clinica='$RIF_clinica' LEFT JOIN veterinario v ON c.id_veterinario = v.Id_veterinario WHERE c.RIF_clinica='$RIF_clinica' AND c.id_mascota='$id'$extra_prop ORDER BY c.fecha DESC");
    $lista = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $idc = $r['id_consulta'];
        $tr = mysqli_query($conexion, "SELECT nombre FROM test_rapidos WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$idc");
        $r['tests_rapidos'] = [];
        while ($t = mysqli_fetch_assoc($tr)) { $r['tests_rapidos'][] = $t['nombre']; }
        $lr = mysqli_query($conexion, "SELECT tipo, observaciones FROM laboratorio WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$idc");
        $r['laboratorio'] = [];
        while ($l = mysqli_fetch_assoc($lr)) { $r['laboratorio'][] = $l; }
        $vr = mysqli_query($conexion, "SELECT nombre, observaciones FROM vacunas WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$idc");
        $r['vacunas'] = [];
        while ($v = mysqli_fetch_assoc($vr)) { $r['vacunas'][] = $v; }
        $r['es_emergencia'] = 0;
        $lista[] = $r;
    }
    // Emergencies from citas
    $res_e = mysqli_query($conexion, "SELECT ct.id_cita, ct.fecha, ct.motivo AS diagnostico, ct.id_veterinario, v.Nombres AS nombre_veterinario FROM citas ct JOIN mascota m ON ct.id_mascota = m.id_mascota AND m.RIF_clinica='$RIF_clinica' LEFT JOIN veterinario v ON ct.id_veterinario = v.Id_veterinario WHERE ct.RIF_clinica='$RIF_clinica' AND ct.id_mascota='$id' AND ct.es_emergencia=1 AND ct.estado='completada'$extra_prop ORDER BY ct.fecha DESC");
    while ($r = mysqli_fetch_assoc($res_e)) {
        $r['id_consulta'] = -$r['id_cita'];
        unset($r['id_cita']);
        $r['examen_fisico'] = null;
        $r['tests_rapidos'] = [];
        $r['laboratorio'] = [];
        $r['vacunas'] = [];
        $r['es_emergencia'] = 1;
        $lista[] = $r;
    }
    // Sort merged list by fecha DESC
    usort($lista, function($a, $b) { return strcmp($b['fecha'], $a['fecha']); });
    echo json_encode($lista);
    exit;
}

if ($action === 'detalle_consulta') {
    $idc = intval($_GET['id'] ?? 0);
    $extra_prop = '';
    if (($_SESSION['usuario']['rol'] ?? '') === 'propietario') {
        $id_prop = mysqli_real_escape_string($conexion, $_SESSION['usuario']['id_propietario'] ?? '');
        $extra_prop = " AND m.id_propietario='$id_prop'";
    }
    $res = mysqli_query($conexion, "SELECT c.*, m.nombre AS mascota_nombre, m.especie, m.raza, m.edad, m.sexo, m.peso, p.nombres AS prop_nombre, p.apellidos AS prop_apellido, p.telefono AS prop_telefono, p.gmail AS prop_gmail, v.Nombres AS nombre_veterinario, a.nombres AS aux_nombre, a.apellidos AS aux_apellido, v2.servicios AS ventas_servicios, v2.total AS ventas_total FROM consulta c JOIN mascota m ON c.id_mascota = m.id_mascota LEFT JOIN propietario p ON c.id_propietario = p.id_propietario LEFT JOIN veterinario v ON c.id_veterinario = v.Id_veterinario LEFT JOIN `aux-vet` a ON c.id_auxiliar = a.id_auxiliar LEFT JOIN ventas v2 ON v2.id_consulta = c.id_consulta WHERE c.RIF_clinica='$RIF_clinica' AND c.id_consulta=$idc$extra_prop LIMIT 1");
    $r = mysqli_fetch_assoc($res);
    if ($r) {
        $tr = mysqli_query($conexion, "SELECT nombre FROM test_rapidos WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$idc");
        $r['tests_rapidos'] = [];
        while ($t = mysqli_fetch_assoc($tr)) { $r['tests_rapidos'][] = $t['nombre']; }
        $lr = mysqli_query($conexion, "SELECT tipo, observaciones FROM laboratorio WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$idc");
        $r['laboratorio'] = [];
        while ($l = mysqli_fetch_assoc($lr)) { $r['laboratorio'][] = $l; }
        $vr = mysqli_query($conexion, "SELECT nombre, observaciones FROM vacunas WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$idc");
        $r['vacunas'] = [];
        while ($v = mysqli_fetch_assoc($vr)) { $r['vacunas'][] = $v; }
        $r['tasa_bcv'] = obtenerTasaBCV();
    }
    echo json_encode($r ?: []);
    exit;
}

if ($action === 'detalle_emergencia') {
    $idc = intval($_GET['id'] ?? 0);
    $idc_abs = abs($idc);
    $extra_prop = '';
    if (($_SESSION['usuario']['rol'] ?? '') === 'propietario') {
        $id_prop = mysqli_real_escape_string($conexion, $_SESSION['usuario']['id_propietario'] ?? '');
        $extra_prop = " AND m.id_propietario='$id_prop'";
    }
    $res = mysqli_query($conexion, "SELECT ct.id_cita, ct.fecha, ct.motivo AS diagnostico, ct.id_veterinario, ct.id_mascota, m.nombre AS mascota_nombre, m.especie, m.raza, v.Nombres AS nombre_veterinario FROM citas ct JOIN mascota m ON ct.id_mascota = m.id_mascota AND m.RIF_clinica='$RIF_clinica' LEFT JOIN veterinario v ON ct.id_veterinario = v.Id_veterinario WHERE ct.RIF_clinica='$RIF_clinica' AND ct.id_cita=$idc_abs$extra_prop LIMIT 1");
    $r = mysqli_fetch_assoc($res);
    echo json_encode($r ?: []);
    exit;
}

if ($action === 'datos_clinica') {
    $res = mysqli_query($conexion, "SELECT nombre AS Nombre_clinic, RIF_clinica FROM clinica WHERE RIF_clinica='$RIF_clinica' LIMIT 1");
    $r = mysqli_fetch_assoc($res);
    echo json_encode($r ?: []);
    exit;
}

if ($action === 'enviar_consulta') {
    require __DIR__ . '/../../vendor/autoload.php';
    $idc = intval($_POST['id_consulta'] ?? 0);
    $email_to = $_POST['email'] ?? '';
    if (!$idc || !$email_to) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos']);
        exit;
    }
    // Fetch consulta data
    $res = mysqli_query($conexion, "SELECT c.*, m.nombre AS mascota_nombre, m.especie, m.raza, m.edad, m.sexo, m.peso, p.nombres AS prop_nombre, p.apellidos AS prop_apellido, p.telefono AS prop_telefono, v.Nombres AS nombre_veterinario FROM consulta c JOIN mascota m ON c.id_mascota = m.id_mascota LEFT JOIN propietario p ON c.id_propietario = p.id_propietario LEFT JOIN veterinario v ON c.id_veterinario = v.Id_veterinario WHERE c.RIF_clinica='$RIF_clinica' AND c.id_consulta=$idc LIMIT 1");
    $r = mysqli_fetch_assoc($res);
    if (!$r) {
        echo json_encode(['success' => false, 'message' => 'Consulta no encontrada']);
        exit;
    }
    // Fetch tests, labs, vacunas
    $tr = mysqli_query($conexion, "SELECT nombre FROM test_rapidos WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$idc");
    $tests = [];
    while ($t = mysqli_fetch_assoc($tr)) { $tests[] = $t['nombre']; }
    $lr = mysqli_query($conexion, "SELECT tipo, observaciones FROM laboratorio WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$idc");
    $labs = [];
    while ($l = mysqli_fetch_assoc($lr)) { $labs[] = $l; }
    $vr = mysqli_query($conexion, "SELECT nombre, observaciones FROM vacunas WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$idc");
    $vacs = [];
    while ($v = mysqli_fetch_assoc($vr)) { $vacs[] = $v; }

    // Get clinic data
    $clinica = mysqli_query($conexion, "SELECT nombre AS Nombre_clinic, RIF_clinica FROM clinica WHERE RIF_clinica='$RIF_clinica' LIMIT 1");
    $cli = mysqli_fetch_assoc($clinica);
    $cli_nombre = $cli['Nombre_clinic'] ?? 'Clinica Veterinaria';
    $cli_rif = $cli['RIF_clinica'] ?? '';

    ob_start();
    ?>
    <!DOCTYPE html>
    <html><head><meta charset="utf-8"><title>Historia Clinica</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:Arial,sans-serif; background:#fff; padding:30px; color:#1f2937; }
        .container { max-width:750px; margin:0 auto; border:2px solid #059669; border-radius:12px; overflow:hidden; }
        .header { background:#059669; color:#fff; padding:20px 30px; }
        .header table { width:100%; }
        .header .title { font-size:22px; font-weight:bold; }
        .header .clinic-data { text-align:right; font-size:14px; }
        .header .subtitle { text-align:center; font-size:18px; font-weight:bold; margin-top:10px; padding-top:10px; border-top:1px solid rgba(255,255,255,0.3); }
        .content { padding:20px 30px; }
        table.data { width:100%; border-collapse:collapse; margin-bottom:20px; }
        table.data th { background:#ecfdf5; padding:8px 12px; font-weight:bold; color:#065f46; border:1px solid #a7f3d0; text-align:left; }
        table.data td { padding:6px 12px; border:1px solid #d1d5db; }
        table.data .label { color:#6b7280; width:120px; }
        .section { margin-bottom:16px; }
        .section-title { background:#ecfdf5; padding:8px 12px; font-weight:bold; color:#065f46; border:1px solid #a7f3d0; border-radius:6px 6px 0 0; }
        .section-body { padding:10px 12px; border:1px solid #d1d5db; border-top:none; border-radius:0 0 6px 6px; line-height:1.6; }
        .foot { background:#f3f4f6; padding:10px 30px; text-align:center; font-size:12px; color:#6b7280; border-top:1px solid #d1d5db; }
    </style>
    </head><body>
    <div class="container">
        <div class="header">
            <table><tr>
                <td class="title">CLINICA VETERINARIA</td>
                <td class="clinic-data"><?php echo htmlspecialchars($cli_nombre); ?><br>RIF: <?php echo htmlspecialchars($cli_rif); ?></td>
            </tr></table>
            <div class="subtitle">HISTORIA CLINICA</div>
        </div>
        <div class="content">
            <table class="data"><tr><th colspan="2">DATOS DEL PACIENTE</th></tr>
                <tr><td class="label">Cedula</td><td><b><?php echo htmlspecialchars($r['id_mascota']); ?></b></td></tr>
                <tr><td class="label">Nombre</td><td><?php echo htmlspecialchars($r['mascota_nombre']); ?></td></tr>
                <tr><td class="label">Especie</td><td><?php echo htmlspecialchars($r['especie']); ?></td></tr>
                <tr><td class="label">Raza</td><td><?php echo htmlspecialchars($r['raza']); ?></td></tr>
                <tr><td class="label">Edad</td><td><?php echo htmlspecialchars($r['edad']); ?></td></tr>
                <tr><td class="label">Sexo</td><td><?php echo htmlspecialchars($r['sexo']); ?></td></tr>
                <tr><td class="label">Peso</td><td><?php echo htmlspecialchars($r['peso']); ?></td></tr>
            </table>
            <table class="data"><tr><th colspan="2">PROPIETARIO</th></tr>
                <tr><td class="label">Cedula</td><td><?php echo htmlspecialchars($r['id_propietario'] ?? ''); ?></td></tr>
                <tr><td class="label">Nombre</td><td><?php echo htmlspecialchars(($r['prop_nombre'] ?? '') . ' ' . ($r['prop_apellido'] ?? '')); ?></td></tr>
                <tr><td class="label">Telefono</td><td><?php echo htmlspecialchars($r['prop_telefono'] ?? ''); ?></td></tr>
            </table>
            <table class="data"><tr><th colspan="2">CONSULTA</th></tr>
                <tr><td class="label">Fecha</td><td><?php echo htmlspecialchars($r['fecha']); ?></td></tr>
                <tr><td class="label">Veterinario</td><td><?php echo htmlspecialchars($r['nombre_veterinario'] ?? ''); ?></td></tr>
            </table>
            <div class="section"><div class="section-title">DIAGNOSTICO</div><div class="section-body"><?php echo nl2br(htmlspecialchars($r['diagnostico'] ?? '')); ?></div></div>
            <div class="section"><div class="section-title">EXAMEN FISICO</div><div class="section-body"><?php echo nl2br(htmlspecialchars($r['examen_fisico'] ?? '')); ?></div></div>
            <div class="section"><div class="section-title">TESTS RAPIDOS</div><div class="section-body"><?php if (empty($tests)): ?><i>No se realizaron tests rapidos</i><?php else: foreach ($tests as $t): ?>&bull; <?php echo htmlspecialchars($t); ?><br><?php endforeach; endif; ?></div></div>
            <div class="section"><div class="section-title">LABORATORIO</div><div class="section-body"><?php if (empty($labs)): ?><i>No se realizaron examenes de laboratorio</i><?php else: foreach ($labs as $l): ?>&bull; <?php echo htmlspecialchars($l['tipo']); ?><br><?php endforeach; if (!empty($labs[0]['observaciones'])): ?><div style="margin-top:6px;"><b>Observaciones:</b> <?php echo nl2br(htmlspecialchars($labs[0]['observaciones'])); ?></div><?php endif; endif; ?></div></div>
            <div class="section"><div class="section-title">VACUNAS</div><div class="section-body"><?php if (empty($vacs)): ?><i>No se aplicaron vacunas</i><?php else: foreach ($vacs as $v): ?>&bull; <?php echo htmlspecialchars($v['nombre']); ?><br><?php endforeach; if (!empty($vacs[0]['observaciones'])): ?><div style="margin-top:6px;"><b>Observaciones:</b> <?php echo nl2br(htmlspecialchars($vacs[0]['observaciones'])); ?></div><?php endif; endif; ?></div></div>
        </div>
        <div class="foot">Generado el <?php echo date('d/m/Y'); ?> &mdash; <?php echo htmlspecialchars($cli_nombre); ?></div>
    </div>
    </body></html>
    <?php
    $html = ob_get_clean();

    // Generate PDF with Dompdf
    $dompdf = new Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->render();
    $pdf_output = $dompdf->output();

    // Send email via PHPMailer with SMTP (Outlook/Hotmail) + PDF attachment
    $smtp = require __DIR__ . '/../../config/email.php';
    set_time_limit(120);
    ob_start();
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function($str, $level) { file_put_contents('C:/xampp/apache/logs/phpmailer_debug.log', date('Y-m-d H:i:s') . " [$level] $str\n", FILE_APPEND); };
        $mail->Host       = $smtp['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp['username'];
        $mail->Password   = str_replace(' ', '', $smtp['password']);
        $mail->SMTPSecure = $smtp['encryption'];
        $mail->Port       = $smtp['port'];
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom($smtp['username'], $smtp['from_name']);
        $mail->addAddress($email_to);
        $mail->isHTML(true);
        $mail->Subject = 'Historia Clinica - ' . $r['mascota_nombre'];
        $mail->Body    = '<p>Estimado propietario, adjuntamos la historia clinica de <b>' . htmlspecialchars($r['mascota_nombre']) . '</b>.</p>';
        $mail->addStringAttachment($pdf_output, 'historia_' . $idc . '.pdf', 'base64', 'application/pdf');
        $mail->send();
        ob_end_clean();
        echo json_encode(['success' => true, 'message' => 'Correo enviado exitosamente']);
    } catch (Exception $e) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Error al enviar el correo: ' . $mail->ErrorInfo]);
    }
    exit;
}

if ($action === 'pdf_receta') {
    require __DIR__ . '/../../vendor/autoload.php';
    $receta_text = $_POST['receta'] ?? '';
    $id_mascota = mysqli_real_escape_string($conexion, $_POST['id_mascota'] ?? '');
    $fecha = mysqli_real_escape_string($conexion, $_POST['fecha'] ?? '');
    $id_propietario = mysqli_real_escape_string($conexion, $_POST['id_propietario'] ?? '');
    $id_vet = mysqli_real_escape_string($conexion, $_POST['id_veterinario'] ?? '');
    $mr = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT m.nombre AS mascota_nombre, m.especie, m.raza, m.id_propietario, p.nombres AS prop_nombre, p.apellidos AS prop_apellido FROM mascota m LEFT JOIN propietario p ON m.id_propietario = p.id_propietario AND p.RIF_clinica='$RIF_clinica' WHERE m.RIF_clinica='$RIF_clinica' AND m.id_mascota='$id_mascota' LIMIT 1"));
    $vet_nombre = '';
    if ($id_vet) {
        $vr = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT Nombres FROM veterinario WHERE RIF_clinica='$RIF_clinica' AND Id_veterinario='$id_vet' LIMIT 1"));
        $vet_nombre = $vr['Nombres'] ?? '';
    }
    $cli = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT nombre AS Nombre_clinic, RIF_clinica FROM clinica WHERE RIF_clinica='$RIF_clinica' LIMIT 1"));
    $cli_nombre = $cli['Nombre_clinic'] ?? 'Clinica Veterinaria';
    $cli_rif = $cli['RIF_clinica'] ?? '';
    ob_start();
    ?>
    <!DOCTYPE html>
    <html><head><meta charset="utf-8"><title>Receta</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:Arial,sans-serif; background:#fff; padding:30px; color:#1f2937; }
        .container { max-width:650px; margin:0 auto; border:2px solid #059669; border-radius:12px; overflow:hidden; }
        .header { background:#059669; color:#fff; padding:20px 30px; text-align:center; }
        .header .title { font-size:22px; font-weight:bold; }
        .header .subtitle { font-size:16px; font-weight:bold; margin-top:8px; padding-top:8px; border-top:1px solid rgba(255,255,255,0.3); }
        .content { padding:20px 30px; }
        table.data { width:100%; border-collapse:collapse; margin-bottom:20px; }
        table.data th { background:#ecfdf5; padding:8px 12px; font-weight:bold; color:#065f46; border:1px solid #a7f3d0; text-align:left; font-size:14px; }
        table.data td { padding:6px 12px; border:1px solid #d1d5db; font-size:14px; }
        table.data .label { color:#6b7280; width:110px; }
        .receta-box { border:2px solid #059669; border-radius:8px; padding:16px; margin-top:12px; min-height:200px; line-height:1.8; font-size:14px; }
        .receta-box .content-text { white-space:pre-wrap; }
        .foot { background:#f3f4f6; padding:10px 30px; text-align:center; font-size:12px; color:#6b7280; border-top:1px solid #d1d5db; }
        .firma { margin-top:40px; text-align:right; font-size:13px; color:#4b5563; }
        .firma hr { width:250px; margin-left:auto; border:none; border-top:1px solid #9ca3af; margin-bottom:4px; }
    </style>
    </head><body>
    <div class="container">
        <div class="header">
            <div class="title">CLINICA VETERINARIA</div>
            <div style="font-size:14px;margin-top:4px;"><?php echo htmlspecialchars($cli_nombre); ?> &mdash; RIF: <?php echo htmlspecialchars($cli_rif); ?></div>
            <div class="subtitle">RECETA / PRESCRIPCION</div>
        </div>
        <div class="content">
            <table class="data"><tr><th colspan="2">DATOS DEL PACIENTE</th></tr>
                <tr><td class="label">Cedula</td><td><b><?php echo htmlspecialchars($id_mascota); ?></b></td></tr>
                <tr><td class="label">Nombre</td><td><?php echo htmlspecialchars($mr['mascota_nombre'] ?? ''); ?></td></tr>
                <tr><td class="label">Especie</td><td><?php echo htmlspecialchars($mr['especie'] ?? ''); ?></td></tr>
                <tr><td class="label">Raza</td><td><?php echo htmlspecialchars($mr['raza'] ?? ''); ?></td></tr>
                <tr><td class="label">Propietario</td><td><?php echo htmlspecialchars(($mr['prop_nombre'] ?? '') . ' ' . ($mr['prop_apellido'] ?? '')); ?></td></tr>
                <tr><td class="label">Fecha</td><td><?php echo htmlspecialchars($fecha); ?></td></tr>
                <?php if ($vet_nombre): ?>
                <tr><td class="label">Veterinario</td><td><?php echo htmlspecialchars($vet_nombre); ?></td></tr>
                <?php endif; ?>
            </table>
            <div style="font-weight:bold;color:#065f46;margin-bottom:8px;font-size:14px;">INDICACIONES</div>
            <div class="receta-box">
                <div class="content-text"><?php echo nl2br(htmlspecialchars($receta_text)); ?></div>
            </div>
            <div class="firma">
                <hr>
                <span>Firma del Veterinario</span>
            </div>
        </div>
        <div class="foot">Generado el <?php echo date('d/m/Y'); ?> &mdash; <?php echo htmlspecialchars($cli_nombre); ?></div>
    </div>
    </body></html>
    <?php
    $html = ob_get_clean();
    $dompdf = new Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->render();
    $dompdf->stream("receta_{$id_mascota}_{$fecha}.pdf", ['Attachment' => true]);
    exit;
}

if ($action === 'pdf_consulta') {
    require __DIR__ . '/../../vendor/autoload.php';
    $idc = intval($_GET['id'] ?? 0);
    $res = mysqli_query($conexion, "SELECT c.*, m.nombre AS mascota_nombre, m.especie, m.raza, m.edad, m.sexo, m.peso, p.nombres AS prop_nombre, p.apellidos AS prop_apellido, p.telefono AS prop_telefono, v.Nombres AS nombre_veterinario FROM consulta c JOIN mascota m ON c.id_mascota = m.id_mascota LEFT JOIN propietario p ON c.id_propietario = p.id_propietario LEFT JOIN veterinario v ON c.id_veterinario = v.Id_veterinario WHERE c.RIF_clinica='$RIF_clinica' AND c.id_consulta=$idc LIMIT 1");
    $r = mysqli_fetch_assoc($res);
    if (!$r) { echo "Consulta no encontrada"; exit; }
    $tr = mysqli_query($conexion, "SELECT nombre FROM test_rapidos WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$idc");
    $tests = []; while ($t = mysqli_fetch_assoc($tr)) { $tests[] = $t['nombre']; }
    $lr = mysqli_query($conexion, "SELECT tipo, observaciones FROM laboratorio WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$idc");
    $labs = []; while ($l = mysqli_fetch_assoc($lr)) { $labs[] = $l; }
    $vr = mysqli_query($conexion, "SELECT nombre, observaciones FROM vacunas WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$idc");
    $vacs = []; while ($v = mysqli_fetch_assoc($vr)) { $vacs[] = $v; }
    $cli = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT nombre AS Nombre_clinic, RIF_clinica FROM clinica WHERE RIF_clinica='$RIF_clinica' LIMIT 1"));
    $cli_nombre = $cli['Nombre_clinic'] ?? 'Clinica Veterinaria';
    $cli_rif = $cli['RIF_clinica'] ?? '';
    ob_start();
    ?>
    <!DOCTYPE html>
    <html><head><meta charset="utf-8"><title>Historia Clinica</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:Arial,sans-serif; background:#fff; padding:30px; color:#1f2937; }
        .container { max-width:750px; margin:0 auto; border:2px solid #059669; border-radius:12px; overflow:hidden; }
        .header { background:#059669; color:#fff; padding:20px 30px; }
        .header table { width:100%; }
        .header .title { font-size:22px; font-weight:bold; }
        .header .clinic-data { text-align:right; font-size:14px; }
        .header .subtitle { text-align:center; font-size:18px; font-weight:bold; margin-top:10px; padding-top:10px; border-top:1px solid rgba(255,255,255,0.3); }
        .content { padding:20px 30px; }
        table.data { width:100%; border-collapse:collapse; margin-bottom:20px; }
        table.data th { background:#ecfdf5; padding:8px 12px; font-weight:bold; color:#065f46; border:1px solid #a7f3d0; text-align:left; }
        table.data td { padding:6px 12px; border:1px solid #d1d5db; }
        table.data .label { color:#6b7280; width:120px; }
        .section { margin-bottom:16px; }
        .section-title { background:#ecfdf5; padding:8px 12px; font-weight:bold; color:#065f46; border:1px solid #a7f3d0; border-radius:6px 6px 0 0; }
        .section-body { padding:10px 12px; border:1px solid #d1d5db; border-top:none; border-radius:0 0 6px 6px; line-height:1.6; }
        .foot { background:#f3f4f6; padding:10px 30px; text-align:center; font-size:12px; color:#6b7280; border-top:1px solid #d1d5db; }
        @media print { body { padding:0; } .container { border:2px solid #059669; } }
    </style>
    </head><body>
    <div class="container">
        <div class="header">
            <table><tr>
                <td class="title">CLINICA VETERINARIA</td>
                <td class="clinic-data"><?php echo htmlspecialchars($cli_nombre); ?><br>RIF: <?php echo htmlspecialchars($cli_rif); ?></td>
            </tr></table>
            <div class="subtitle">HISTORIA CLINICA</div>
        </div>
        <div class="content">
            <table class="data"><tr><th colspan="2">DATOS DEL PACIENTE</th></tr>
                <tr><td class="label">Cedula</td><td><b><?php echo htmlspecialchars($r['id_mascota']); ?></b></td></tr>
                <tr><td class="label">Nombre</td><td><?php echo htmlspecialchars($r['mascota_nombre']); ?></td></tr>
                <tr><td class="label">Especie</td><td><?php echo htmlspecialchars($r['especie']); ?></td></tr>
                <tr><td class="label">Raza</td><td><?php echo htmlspecialchars($r['raza']); ?></td></tr>
                <tr><td class="label">Edad</td><td><?php echo htmlspecialchars($r['edad']); ?></td></tr>
                <tr><td class="label">Sexo</td><td><?php echo htmlspecialchars($r['sexo']); ?></td></tr>
                <tr><td class="label">Peso</td><td><?php echo htmlspecialchars($r['peso']); ?></td></tr>
            </table>
            <table class="data"><tr><th colspan="2">PROPIETARIO</th></tr>
                <tr><td class="label">Cedula</td><td><?php echo htmlspecialchars($r['id_propietario'] ?? ''); ?></td></tr>
                <tr><td class="label">Nombre</td><td><?php echo htmlspecialchars(($r['prop_nombre'] ?? '') . ' ' . ($r['prop_apellido'] ?? '')); ?></td></tr>
                <tr><td class="label">Telefono</td><td><?php echo htmlspecialchars($r['prop_telefono'] ?? ''); ?></td></tr>
            </table>
            <table class="data"><tr><th colspan="2">CONSULTA</th></tr>
                <tr><td class="label">Fecha</td><td><?php echo htmlspecialchars($r['fecha']); ?></td></tr>
                <tr><td class="label">Veterinario</td><td><?php echo htmlspecialchars($r['nombre_veterinario'] ?? ''); ?></td></tr>
            </table>
            <div class="section"><div class="section-title">DIAGNOSTICO</div><div class="section-body"><?php echo nl2br(htmlspecialchars($r['diagnostico'] ?? '')); ?></div></div>
            <div class="section"><div class="section-title">EXAMEN FISICO</div><div class="section-body"><?php echo nl2br(htmlspecialchars($r['examen_fisico'] ?? '')); ?></div></div>
            <div class="section"><div class="section-title">TESTS RAPIDOS</div><div class="section-body"><?php if (empty($tests)): ?><i>No se realizaron tests rapidos</i><?php else: foreach ($tests as $t): ?>&bull; <?php echo htmlspecialchars($t); ?><br><?php endforeach; endif; ?></div></div>
            <div class="section"><div class="section-title">LABORATORIO</div><div class="section-body"><?php if (empty($labs)): ?><i>No se realizaron examenes de laboratorio</i><?php else: foreach ($labs as $l): ?>&bull; <?php echo htmlspecialchars($l['tipo']); ?><br><?php endforeach; if (!empty($labs[0]['observaciones'])): ?><div style="margin-top:6px;"><b>Observaciones:</b> <?php echo nl2br(htmlspecialchars($labs[0]['observaciones'])); ?></div><?php endif; endif; ?></div></div>
            <div class="section"><div class="section-title">VACUNAS</div><div class="section-body"><?php if (empty($vacs)): ?><i>No se aplicaron vacunas</i><?php else: foreach ($vacs as $v): ?>&bull; <?php echo htmlspecialchars($v['nombre']); ?><br><?php endforeach; if (!empty($vacs[0]['observaciones'])): ?><div style="margin-top:6px;"><b>Observaciones:</b> <?php echo nl2br(htmlspecialchars($vacs[0]['observaciones'])); ?></div><?php endif; endif; ?></div></div>
            <div class="section"><div class="section-title">RECETA / PRESCRIPCION</div><div class="section-body"><?php if (empty($r['receta'])): ?><i>No se indico receta</i><?php else: echo nl2br(htmlspecialchars($r['receta'])); endif; ?></div></div>
        </div>
        <div class="foot">Generado el <?php echo date('d/m/Y'); ?> &mdash; <?php echo htmlspecialchars($cli_nombre); ?></div>
    </div>
    </body></html>
    <?php
    $html = ob_get_clean();
    try {
        $dompdf = new Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
        $pdf = $dompdf->output();
    } catch (Exception $e) {
        echo "Error interno al generar el PDF: " . $e->getMessage();
        exit;
    }
    if ($mode === 'enviar') {
        $prop_email = '';
        $pid = $r['id_propietario'] ?? '';
        if ($pid) {
            $er = mysqli_query($conexion, "SELECT Gmail FROM propietario WHERE RIF_clinica='$RIF_clinica' AND id_propietario='$pid' LIMIT 1");
            $erow = mysqli_fetch_assoc($er);
            $prop_email = $erow['Gmail'] ?? '';
        }
        if (!empty($prop_email)) {
            try {
                $smtp = require __DIR__ . '/../../config/email.php';
                set_time_limit(120);
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->SMTPDebug = 2;
                $mail->Debugoutput = function($str, $level) { file_put_contents('C:/xampp/apache/logs/phpmailer_debug.log', date('Y-m-d H:i:s') . " [$level] $str\n", FILE_APPEND); };
                $mail->Host = $smtp['host'];
                $mail->SMTPAuth = true;
                $mail->Username = $smtp['username'];
                $mail->Password = str_replace(' ', '', $smtp['password']);
                $mail->SMTPSecure = $smtp['encryption'];
                $mail->Port = $smtp['port'];
                $mail->CharSet = 'UTF-8';
                $cli_nombre_completo = ($r['prop_nombre'] ?? '') . ' ' . ($r['prop_apellido'] ?? '');
                $mail->setFrom($smtp['username'], $smtp['from_name']);
                $mail->addAddress($prop_email, trim($cli_nombre_completo));
                $mail->Subject = "Factura de Consulta - $cli_nombre";
                $mail->Body = "Hola " . trim($cli_nombre_completo) . ",\n\nAdjuntamos la factura de su consulta veterinaria.\n\nTotal: \$" . number_format($total, 2) . "\n\nGracias por su preferencia.";
                $mail->addStringAttachment($pdf, "factura_{$idc}.pdf");
                $mail->send();
                echo json_encode(['success' => true, 'email_sent' => true, 'message' => 'Factura enviada correctamente']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error al enviar el correo: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'El propietario no tiene correo registrado']);
        }
        exit;
    }
    header_remove('Content-Type');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="historia_' . $idc . '.pdf"');
    echo $pdf;
    exit;
}

if ($action === 'pdf_emergencia') {
    require __DIR__ . '/../../vendor/autoload.php';
    $idc = intval($_GET['id'] ?? 0);
    $res = mysqli_query($conexion, "SELECT c.*, m.nombre AS mascota_nombre, m.especie, m.raza, m.edad, m.sexo, m.id_propietario, p.nombres AS prop_nombre, p.apellidos AS prop_apellido, v.Nombres AS nombre_veterinario FROM citas c JOIN mascota m ON c.id_mascota = m.id_mascota LEFT JOIN propietario p ON m.id_propietario = p.id_propietario LEFT JOIN veterinario v ON c.id_veterinario = v.Id_veterinario WHERE c.RIF_clinica='$RIF_clinica' AND c.id_cita=$idc AND c.es_emergencia=1 LIMIT 1");
    $r = mysqli_fetch_assoc($res);
    if (!$r) { echo "Emergencia no encontrada"; exit; }
    $cli = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT nombre AS Nombre_clinic, RIF_clinica FROM clinica WHERE RIF_clinica='$RIF_clinica' LIMIT 1"));
    $cli_nombre = $cli['Nombre_clinic'] ?? 'Clinica Veterinaria';
    $cli_rif = $cli['RIF_clinica'] ?? '';
    ob_start();
    ?>
    <!DOCTYPE html>
    <html><head><meta charset="utf-8"><title>Reporte de Emergencia</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:Arial,sans-serif; background:#fff; padding:30px; color:#1f2937; }
        .container { max-width:650px; margin:0 auto; border:2px solid #dc2626; border-radius:12px; overflow:hidden; }
        .header { background:#dc2626; color:#fff; padding:20px 30px; text-align:center; }
        .header .title { font-size:22px; font-weight:bold; }
        .header .subtitle { font-size:18px; font-weight:bold; margin-top:10px; padding-top:10px; border-top:1px solid rgba(255,255,255,0.3); }
        .header .clinic-name { font-size:14px; margin-top:4px; }
        .content { padding:20px 30px; }
        table.data { width:100%; border-collapse:collapse; margin-bottom:20px; }
        table.data th { background:#fef2f2; padding:8px 12px; font-weight:bold; color:#991b1b; border:1px solid #fecaca; text-align:left; font-size:14px; }
        table.data td { padding:6px 12px; border:1px solid #d1d5db; font-size:14px; }
        table.data .label { color:#6b7280; width:120px; }
        .section { margin-bottom:16px; }
        .section-title { background:#fef2f2; padding:8px 12px; font-weight:bold; color:#991b1b; border:1px solid #fecaca; border-radius:6px 6px 0 0; }
        .section-body { padding:10px 12px; border:1px solid #d1d5db; border-top:none; border-radius:0 0 6px 6px; line-height:1.6; font-size:14px; }
        .foot { background:#f3f4f6; padding:10px 30px; text-align:center; font-size:12px; color:#6b7280; border-top:1px solid #d1d5db; }
    </style>
    </head><body>
    <div class="container">
        <div class="header">
            <div class="title">CLINICA VETERINARIA</div>
            <div class="clinic-name"><?php echo htmlspecialchars($cli_nombre); ?> &mdash; RIF: <?php echo htmlspecialchars($cli_rif); ?></div>
            <div class="subtitle">REPORTE DE EMERGENCIA</div>
        </div>
        <div class="content">
            <table class="data"><tr><th colspan="2">DATOS DEL PACIENTE</th></tr>
                <tr><td class="label">Cedula</td><td><b><?php echo htmlspecialchars($r['id_mascota']); ?></b></td></tr>
                <tr><td class="label">Nombre</td><td><?php echo htmlspecialchars($r['mascota_nombre']); ?></td></tr>
                <tr><td class="label">Especie</td><td><?php echo htmlspecialchars($r['especie']); ?></td></tr>
                <tr><td class="label">Raza</td><td><?php echo htmlspecialchars($r['raza']); ?></td></tr>
                <tr><td class="label">Edad</td><td><?php echo htmlspecialchars($r['edad']); ?></td></tr>
                <tr><td class="label">Sexo</td><td><?php echo htmlspecialchars($r['sexo']); ?></td></tr>
            </table>
            <table class="data"><tr><th colspan="2">PROPIETARIO</th></tr>
                <tr><td class="label">Nombre</td><td><?php echo htmlspecialchars(($r['prop_nombre'] ?? '') . ' ' . ($r['prop_apellido'] ?? '')); ?></td></tr>
            </table>
            <table class="data"><tr><th colspan="2">EMERGENCIA</th></tr>
                <tr><td class="label">Fecha</td><td><?php echo htmlspecialchars($r['fecha']); ?></td></tr>
                <tr><td class="label">Veterinario</td><td><?php echo htmlspecialchars($r['nombre_veterinario'] ?? ''); ?></td></tr>
            </table>
            <div class="section"><div class="section-title">DIAGNOSTICO</div><div class="section-body"><?php echo nl2br(htmlspecialchars($r['motivo'] ?? '')); ?></div></div>
        </div>
        <div class="foot">Generado el <?php echo date('d/m/Y'); ?> &mdash; <?php echo htmlspecialchars($cli_nombre); ?></div>
    </div>
    </body></html>
    <?php
    $html = ob_get_clean();
    try {
        $dompdf = new Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
        $pdfOutput = $dompdf->output();
    } catch (Exception $e) {
        echo "Error interno al generar el PDF: " . $e->getMessage();
        exit;
    }
    header_remove('Content-Type');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="emergencia_' . $idc . '.pdf"');
    echo $pdfOutput;
    exit;
}

if ($action === 'pdf_factura') {
    require __DIR__ . '/../../vendor/autoload.php';
    $idc = intval($_GET['id'] ?? 0);
    // Fetch consulta
    $res = mysqli_query($conexion, "SELECT c.*, m.nombre AS mascota_nombre, m.especie, m.raza, m.edad, m.sexo, m.peso, p.nombres AS prop_nombre, p.apellidos AS prop_apellido, p.telefono AS prop_telefono, v.Nombres AS nombre_veterinario FROM consulta c JOIN mascota m ON c.id_mascota = m.id_mascota LEFT JOIN propietario p ON c.id_propietario = p.id_propietario LEFT JOIN veterinario v ON c.id_veterinario = v.Id_veterinario WHERE c.RIF_clinica='$RIF_clinica' AND c.id_consulta=$idc LIMIT 1");
    $r = mysqli_fetch_assoc($res);
    if (!$r) { echo "Consulta no encontrada"; exit; }
    // Fetch services used
    $tr = mysqli_query($conexion, "SELECT nombre FROM test_rapidos WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$idc");
    $tests = []; while ($t = mysqli_fetch_assoc($tr)) { $tests[] = $t['nombre']; }
    $lr = mysqli_query($conexion, "SELECT tipo FROM laboratorio WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$idc");
    $labs = []; while ($l = mysqli_fetch_assoc($lr)) { $labs[] = $l['tipo']; }
    $vr = mysqli_query($conexion, "SELECT nombre FROM vacunas WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$idc");
    $vacs = []; while ($v = mysqli_fetch_assoc($vr)) { $vacs[] = $v['nombre']; }
    // Fetch prices
    $pres = mysqli_query($conexion, "SELECT servicio, precio FROM precios WHERE RIF_clinica='$RIF_clinica'");
    $precios = [];
    while ($p = mysqli_fetch_assoc($pres)) { $precios[$p['servicio']] = (float)$p['precio']; }
    // Build items + total
    $items = [];
    $items[] = ['servicio' => 'Consulta General', 'precio' => $precios['Consulta General'] ?? 0];
    foreach ($tests as $t) { $items[] = ['servicio' => $t, 'precio' => $precios[$t] ?? 0]; }
    foreach ($labs as $l) { $items[] = ['servicio' => $l, 'precio' => $precios[$l] ?? 0]; }
    foreach ($vacs as $v) { $items[] = ['servicio' => $v, 'precio' => $precios[$v] ?? 0]; }
    $total = array_sum(array_column($items, 'precio'));
    // Clinic data
    $cli = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT nombre AS Nombre_clinic, RIF_clinica FROM clinica WHERE RIF_clinica='$RIF_clinica' LIMIT 1"));
    $cli_nombre = $cli['Nombre_clinic'] ?? 'Clinica Veterinaria';
    $cli_rif = $cli['RIF_clinica'] ?? '';
    ob_start();
    ?>
    <!DOCTYPE html>
    <html><head><meta charset="utf-8"><title>Factura</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:Arial,sans-serif; background:#fff; padding:30px; color:#1f2937; }
        .container { max-width:750px; margin:0 auto; border:2px solid #059669; border-radius:12px; overflow:hidden; }
        .header { background:#059669; color:#fff; padding:20px 30px; }
        .header table { width:100%; }
        .header .title { font-size:22px; font-weight:bold; }
        .header .clinic-data { text-align:right; font-size:14px; }
        .header .subtitle { text-align:center; font-size:18px; font-weight:bold; margin-top:10px; padding-top:10px; border-top:1px solid rgba(255,255,255,0.3); }
        .content { padding:20px 30px; }
        table.data { width:100%; border-collapse:collapse; margin-bottom:20px; }
        table.data th { background:#ecfdf5; padding:8px 12px; font-weight:bold; color:#065f46; border:1px solid #a7f3d0; text-align:left; }
        table.data td { padding:6px 12px; border:1px solid #d1d5db; }
        table.data .label { color:#6b7280; width:120px; }
        .total-row td { font-weight:bold; background:#ecfdf5; border-top:2px solid #059669; }
        .foot { background:#f3f4f6; padding:10px 30px; text-align:center; font-size:12px; color:#6b7280; border-top:1px solid #d1d5db; }
    </style>
    </head><body>
    <div class="container">
        <div class="header">
            <table><tr>
                <td class="title">CLINICA VETERINARIA</td>
                <td class="clinic-data"><?php echo htmlspecialchars($cli_nombre); ?><br>RIF: <?php echo htmlspecialchars($cli_rif); ?></td>
            </tr></table>
            <div class="subtitle">FACTURA</div>
        </div>
        <div class="content">
            <table class="data"><tr><th colspan="2">DATOS DEL PACIENTE</th></tr>
                <tr><td class="label">Cedula</td><td><b><?php echo htmlspecialchars($r['id_mascota']); ?></b></td></tr>
                <tr><td class="label">Nombre</td><td><?php echo htmlspecialchars($r['mascota_nombre']); ?></td></tr>
                <tr><td class="label">Especie</td><td><?php echo htmlspecialchars($r['especie']); ?></td></tr>
                <tr><td class="label">Raza</td><td><?php echo htmlspecialchars($r['raza']); ?></td></tr>
                <tr><td class="label">Edad</td><td><?php echo htmlspecialchars($r['edad']); ?></td></tr>
                <tr><td class="label">Sexo</td><td><?php echo htmlspecialchars($r['sexo']); ?></td></tr>
            </table>
            <table class="data"><tr><th colspan="2">PROPIETARIO</th></tr>
                <tr><td class="label">Cedula</td><td><?php echo htmlspecialchars($r['id_propietario'] ?? ''); ?></td></tr>
                <tr><td class="label">Nombre</td><td><?php echo htmlspecialchars(($r['prop_nombre'] ?? '') . ' ' . ($r['prop_apellido'] ?? '')); ?></td></tr>
                <tr><td class="label">Telefono</td><td><?php echo htmlspecialchars($r['prop_telefono'] ?? ''); ?></td></tr>
            </table>
            <table class="data"><tr><th colspan="2">DETALLE DE SERVICIOS</th></tr>
                <tr><th style="padding:8px 12px;border:1px solid #a7f3d0;text-align:left;background:#ecfdf5;color:#065f46;">Servicio</th>
                    <th style="padding:8px 12px;border:1px solid #a7f3d0;text-align:right;background:#ecfdf5;color:#065f46;width:120px;">Precio</th></tr>
                <?php foreach ($items as $it): ?>
                <tr><td style="padding:6px 12px;border:1px solid #d1d5db;"><?php echo htmlspecialchars($it['servicio']); ?></td>
                    <td style="padding:6px 12px;border:1px solid #d1d5db;text-align:right;">$<?php echo number_format($it['precio'], 2); ?></td></tr>
                <?php endforeach; ?>
                <tr class="total-row"><td style="padding:8px 12px;border:1px solid #d1d5db;font-weight:bold;background:#ecfdf5;border-top:2px solid #059669;">TOTAL</td>
                    <td style="padding:8px 12px;border:1px solid #d1d5db;text-align:right;font-weight:bold;background:#ecfdf5;border-top:2px solid #059669;font-size:16px;">$<?php echo number_format($total, 2); ?></td></tr>
            </table>
        </div>
        <div class="foot">Generado el <?php echo date('d/m/Y'); ?> &mdash; <?php echo htmlspecialchars($cli_nombre); ?></div>
    </div>
    </body></html>
    <?php
    $html = ob_get_clean();
    $dompdf = new Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->render();
    $dompdf->stream("factura_{$idc}.pdf", ['Attachment' => true]);
    exit;
}

if ($action === 'inventario_resumen') {
    $tp = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) AS n FROM producto WHERE RIF_clinica='$RIF_clinica'"))['n'];
    $tprov = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) AS n FROM proveedor WHERE RIF_clinica='$RIF_clinica'"))['n'];
    $sb = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) AS n FROM producto WHERE RIF_clinica='$RIF_clinica' AND cantidad <= stock_minimo"))['n'];
    echo json_encode(['total_productos' => (int)$tp, 'total_proveedores' => (int)$tprov, 'stock_bajo' => (int)$sb]);
    exit;
}

if ($action === 'productos_estrella') {
    $res = mysqli_query($conexion, "
        SELECT dv.id_producto, COALESCE(p.descripcion, dv.descripcion) AS descripcion, SUM(dv.cantidad) AS total_vendido
        FROM detalle_venta dv
        LEFT JOIN producto p ON dv.id_producto = p.id_producto AND p.RIF_clinica = dv.RIF_clinica
        WHERE dv.RIF_clinica = '$RIF_clinica'
        GROUP BY dv.id_producto, dv.descripcion
        ORDER BY total_vendido DESC
        LIMIT 5
    ");
    $productos = [];
    $max = 0;
    while ($r = mysqli_fetch_assoc($res)) {
        $productos[] = $r;
        if ($r['total_vendido'] > $max) $max = (int)$r['total_vendido'];
    }
    echo json_encode(['productos' => $productos, 'max' => $max]);
    exit;
}

if ($action === 'buscar_cliente_venta') {
    $q = mysqli_real_escape_string($conexion, $_GET['q'] ?? '');
    $res = mysqli_query($conexion, "
        (SELECT id_propietario AS cedula, nombres, apellidos, telefono, gmail AS email, 'Propietario' AS tipo
         FROM propietario
         WHERE RIF_clinica='$RIF_clinica' AND (id_propietario LIKE '%$q%' OR nombres LIKE '%$q%' OR apellidos LIKE '%$q%'))
        UNION
        (SELECT id_propietario AS cedula, nombres, apellidos, telefono, '' AS email, 'Cliente' AS tipo
         FROM clientenormal
         WHERE RIF_clinica='$RIF_clinica' AND (id_propietario LIKE '%$q%' OR nombres LIKE '%$q%' OR apellidos LIKE '%$q%'))
        ORDER BY cedula ASC LIMIT 20
    ");
    $lista = [];
    while ($r = mysqli_fetch_assoc($res)) { $lista[] = $r; }
    echo json_encode($lista);
    exit;
}

if ($action === 'buscar_producto_venta') {
    $q = mysqli_real_escape_string($conexion, $_GET['q'] ?? '');
    $res = mysqli_query($conexion, "SELECT id_producto, descripcion, precio_venta, cantidad AS stock FROM producto WHERE RIF_clinica='$RIF_clinica' AND (id_producto LIKE '%$q%' OR descripcion LIKE '%$q%') ORDER BY id_producto ASC LIMIT 20");
    $lista = [];
    while ($r = mysqli_fetch_assoc($res)) { $lista[] = $r; }
    echo json_encode($lista);
    exit;
}

if ($action === 'guardar_venta_normal') {
    $fecha = mysqli_real_escape_string($conexion, $_POST['fecha'] ?? date('Y-m-d'));
    $id_cliente = mysqli_real_escape_string($conexion, $_POST['id_cliente'] ?? '');
    $nombres = mysqli_real_escape_string($conexion, $_POST['nombres_cliente'] ?? '');
    $apellidos = mysqli_real_escape_string($conexion, $_POST['apellidos_cliente'] ?? '');
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono_cliente'] ?? '');
    $email = mysqli_real_escape_string($conexion, $_POST['email_cliente'] ?? '');
    $tipo_pago = mysqli_real_escape_string($conexion, $_POST['tipo_pago'] ?? '');
    $items_json = $_POST['items'] ?? '[]';
    $items = json_decode($items_json, true);
    if (!$items || !is_array($items) || count($items) === 0) {
        echo json_encode(['success' => false, 'message' => 'No hay productos en la venta']);
        exit;
    }
    $total = 0;
    foreach ($items as $it) { $total += (float)($it['subtotal'] ?? 0); }
    $total = number_format($total, 2, '.', '');
    $iq = mysqli_query($conexion, "INSERT INTO venta (fecha, id_cliente, nombres_cliente, apellidos_cliente, telefono_cliente, email_cliente, total, tipo_pago, RIF_clinica) VALUES ('$fecha', '$id_cliente', '$nombres', '$apellidos', '$telefono', '$email', '$total', '$tipo_pago', '$RIF_clinica')");
    if (!$iq) {
        echo json_encode(['success' => false, 'message' => 'Error al guardar venta: ' . mysqli_error($conexion)]);
        exit;
    }
    $id_venta = mysqli_insert_id($conexion);
    $ok = true;
    foreach ($items as $it) {
        $prod = mysqli_real_escape_string($conexion, $it['id_producto'] ?? '');
        $desc = mysqli_real_escape_string($conexion, $it['descripcion'] ?? '');
        $cant = (int)($it['cantidad'] ?? 0);
        $precio = number_format((float)($it['precio_unitario'] ?? 0), 2, '.', '');
        $subtotal = number_format((float)($it['subtotal'] ?? 0), 2, '.', '');
        if (!$prod || $cant <= 0) continue;
        $dqr = mysqli_query($conexion, "INSERT INTO detalle_venta (id_venta, id_producto, descripcion, cantidad, precio_unitario, subtotal, RIF_clinica) VALUES ($id_venta, '$prod', '$desc', $cant, '$precio', '$subtotal', '$RIF_clinica')");
        if (!$dqr) { $ok = false; break; }
        mysqli_query($conexion, "UPDATE producto SET cantidad = GREATEST(0, cantidad - $cant) WHERE RIF_clinica='$RIF_clinica' AND id_producto = '$prod'");
    }
    if (!$ok) {
        mysqli_query($conexion, "DELETE FROM venta WHERE RIF_clinica='$RIF_clinica' AND id_venta = $id_venta");
        echo json_encode(['success' => false, 'message' => 'Error al guardar detalle de venta']);
        exit;
    }
    $tasaBCV = obtenerTasaBCV();
    $totalBs = $tasaBCV > 0 ? round((float)$total * $tasaBCV, 2) : 0;
    echo json_encode(['success' => true, 'message' => 'Venta guardada exitosamente', 'id_venta' => $id_venta, 'tasa_bcv' => $tasaBCV, 'total_bs' => $totalBs]);
    exit;
}

if ($action === 'procesar_factura_venta') {
    $idv = intval($_POST['id_venta'] ?? 0);
    $tipo_pago = mysqli_real_escape_string($conexion, $_POST['tipo_pago'] ?? '');
    $mode = $_POST['mode'] ?? 'descargar';
    if (!$idv) { echo json_encode(['success' => false, 'message' => 'ID de venta no valido']); exit; }
    if ($tipo_pago) {
        mysqli_query($conexion, "UPDATE venta SET tipo_pago = '$tipo_pago' WHERE RIF_clinica='$RIF_clinica' AND id_venta = $idv");
    }
    if ($mode === 'guardar') {
        if (!$tipo_pago) { echo json_encode(['success' => false, 'message' => 'Debe seleccionar un tipo de pago']); exit; }
        echo json_encode(['success' => true, 'message' => 'Tipo de pago guardado correctamente']);
        exit;
    }
    $vr = mysqli_query($conexion, "SELECT * FROM venta WHERE RIF_clinica='$RIF_clinica' AND id_venta = $idv LIMIT 1");
    $v = mysqli_fetch_assoc($vr);
    if (!$v) { echo json_encode(['success' => false, 'message' => 'Venta no encontrada']); exit; }
    $dr = mysqli_query($conexion, "SELECT * FROM detalle_venta WHERE RIF_clinica='$RIF_clinica' AND id_venta = $idv");
    $detalle = [];
    while ($d = mysqli_fetch_assoc($dr)) { $detalle[] = $d; }
    $items = [];
    foreach ($detalle as $d) {
        $items[] = [
            'servicio' => $d['id_producto'] . ' - ' . $d['descripcion'],
            'precio' => (float)$d['precio_unitario'],
            'cantidad' => (int)$d['cantidad'],
            'subtotal' => (float)$d['subtotal']
        ];
    }
    $total = (float)$v['total'];
    $tasaBCV = obtenerTasaBCV();
    $totalBs = $tasaBCV > 0 ? round($total * $tasaBCV, 2) : 0;
    $cli_nombre = $v['nombres_cliente'] . ' ' . $v['apellidos_cliente'];
    $cli_cedula = $v['id_cliente'];
    $cli_telefono = $v['telefono_cliente'];
    $cli_email = $v['email_cliente'];
    $cli_tipo_pago = $v['tipo_pago'];
    require_once __DIR__ . '/../../vendor/autoload.php';
    $cli_res = mysqli_query($conexion, "SELECT nombre AS Nombre_clinic, RIF_clinica FROM clinica WHERE RIF_clinica='$RIF_clinica' LIMIT 1");
    $cli_row = mysqli_fetch_assoc($cli_res);
    $clinic_nombre = $cli_row['Nombre_clinic'] ?? 'Clinica Veterinaria';
    $clinic_rif = $cli_row['RIF_clinica'] ?? '';
    ob_start();
    ?>
    <!DOCTYPE html>
    <html><head><meta charset="utf-8"><title>Factura #<?php echo $idv; ?></title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:Arial,sans-serif; background:#fff; padding:30px; color:#1f2937; }
        .container { max-width:750px; margin:0 auto; border:2px solid #059669; border-radius:12px; overflow:hidden; }
        .header { background:#059669; color:#fff; padding:20px 30px; }
        .header table { width:100%; }
        .header .title { font-size:22px; font-weight:bold; }
        .header .clinic-data { text-align:right; font-size:14px; }
        .header .subtitle { text-align:center; font-size:18px; font-weight:bold; margin-top:10px; padding-top:10px; border-top:1px solid rgba(255,255,255,0.3); }
        .content { padding:20px 30px; }
        table.data { width:100%; border-collapse:collapse; margin-bottom:20px; }
        table.data th { background:#ecfdf5; padding:8px 12px; font-weight:bold; color:#065f46; border:1px solid #a7f3d0; text-align:left; }
        table.data td { padding:6px 12px; border:1px solid #d1d5db; }
        table.data .label { color:#6b7280; width:100px; }
        .total-row td { font-weight:bold; background:#ecfdf5; border-top:2px solid #059669; }
        .foot { background:#f3f4f6; padding:10px 30px; text-align:center; font-size:12px; color:#6b7280; border-top:1px solid #d1d5db; }
    </style>
    </head><body>
    <div class="container">
        <div class="header">
            <table><tr>
                <td class="title">CLINICA VETERINARIA</td>
                <td class="clinic-data"><?php echo htmlspecialchars($clinic_nombre); ?><br>RIF: <?php echo htmlspecialchars($clinic_rif); ?></td>
            </tr></table>
            <div class="subtitle">FACTURA #<?php echo $idv; ?></div>
        </div>
        <div class="content">
            <table class="data"><tr><th colspan="2">DATOS DEL CLIENTE</th></tr>
                <tr><td class="label">Cedula</td><td><b><?php echo htmlspecialchars($cli_cedula); ?></b></td></tr>
                <tr><td class="label">Nombre</td><td><?php echo htmlspecialchars($cli_nombre); ?></td></tr>
                <tr><td class="label">Telefono</td><td><?php echo htmlspecialchars($cli_telefono); ?></td></tr>
                <tr><td class="label">Fecha</td><td><?php echo htmlspecialchars($v['fecha']); ?></td></tr>
                <?php if ($cli_tipo_pago): ?>
                <tr><td class="label">Pago</td><td><?php echo htmlspecialchars($cli_tipo_pago); ?></td></tr>
                <?php endif; ?>
            </table>
            <table class="data"><tr><th colspan="4">DETALLE DE COMPRA</th></tr>
                <tr>
                    <th style="padding:8px 12px;border:1px solid #a7f3d0;text-align:left;background:#ecfdf5;color:#065f46;">Producto</th>
                    <th style="padding:8px 12px;border:1px solid #a7f3d0;text-align:center;background:#ecfdf5;color:#065f46;width:60px;">Cant</th>
                    <th style="padding:8px 12px;border:1px solid #a7f3d0;text-align:right;background:#ecfdf5;color:#065f46;width:100px;">Precio</th>
                    <th style="padding:8px 12px;border:1px solid #a7f3d0;text-align:right;background:#ecfdf5;color:#065f46;width:100px;">Subtotal</th>
                </tr>
                <?php foreach ($items as $it): ?>
                <tr>
                    <td style="padding:6px 12px;border:1px solid #d1d5db;"><?php echo htmlspecialchars($it['servicio']); ?></td>
                    <td style="padding:6px 12px;border:1px solid #d1d5db;text-align:center;"><?php echo $it['cantidad']; ?></td>
                    <td style="padding:6px 12px;border:1px solid #d1d5db;text-align:right;">$<?php echo number_format($it['precio'], 2); ?></td>
                    <td style="padding:6px 12px;border:1px solid #d1d5db;text-align:right;">$<?php echo number_format($it['subtotal'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3" style="padding:8px 12px;border:1px solid #d1d5db;font-weight:bold;background:#ecfdf5;border-top:2px solid #059669;text-align:right;">TOTAL</td>
                    <td style="padding:8px 12px;border:1px solid #d1d5db;text-align:right;font-weight:bold;background:#ecfdf5;border-top:2px solid #059669;font-size:16px;">$<?php echo number_format($total, 2); ?></td>
                </tr>
                <?php if ($totalBs > 0): ?>
                <tr class="total-row">
                    <td colspan="3" style="padding:8px 12px;border:1px solid #d1d5db;font-weight:bold;background:#ecfdf5;border-top:2px solid #059669;text-align:right;">TOTAL EN BS</td>
                    <td style="padding:8px 12px;border:1px solid #d1d5db;text-align:right;font-weight:bold;background:#ecfdf5;border-top:2px solid #059669;">Bs <?php echo number_format($totalBs, 2, ',', '.'); ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        <div class="foot">Generado el <?php echo date('d/m/Y'); ?> &mdash; <?php echo htmlspecialchars($clinic_nombre); ?></div>
    </div>
    </body></html>
    <?php
    $html = ob_get_clean();
    $dompdf = new Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->render();
    $pdf = $dompdf->output();
    if ($mode === 'enviar') {
        $email_sent = false;
        if (!empty($cli_email)) {
            try {
                $smtp = require __DIR__ . '/../../config/email.php';
                set_time_limit(120);
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->SMTPDebug = 2;
                $mail->Debugoutput = function($str, $level) { file_put_contents('C:/xampp/apache/logs/phpmailer_debug.log', date('Y-m-d H:i:s') . " [$level] $str\n", FILE_APPEND); };
                $mail->Host = $smtp['host'];
                $mail->SMTPAuth = true;
                $mail->Username = $smtp['username'];
                $mail->Password = str_replace(' ', '', $smtp['password']);
                $mail->SMTPSecure = $smtp['encryption'];
                $mail->Port = $smtp['port'];
                $mail->CharSet = 'UTF-8';
                $mail->setFrom($smtp['username'], $smtp['from_name']);
                $mail->addAddress($cli_email, $cli_nombre);
                $mail->Subject = "Factura #$idv - $clinic_nombre";
                $mail->Body = "Hola $cli_nombre,\n\nAdjuntamos la factura de su compra #$idv.\n\nTotal: \$$total\n\nGracias por su preferencia.";
                $mail->addStringAttachment($pdf, "factura_$idv.pdf");
                $mail->send();
                $email_sent = true;
                echo json_encode(['success' => true, 'email_sent' => true, 'message' => 'Factura enviada correctamente']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error al enviar el correo: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'El cliente no tiene correo registrado']);
        }
        exit;
    }
    // mode = descargar (default)
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="factura_' . $idv . '.pdf"');
    echo $pdf;
    exit;
}

if ($action === 'procesar_factura_consulta') {
    $idc = intval($_POST['id_consulta'] ?? 0);
    $tipo_pago = mysqli_real_escape_string($conexion, $_POST['tipo_pago'] ?? '');
    $mode = $_POST['mode'] ?? 'descargar';
    if (!$idc) { echo json_encode(['success' => false, 'message' => 'ID de consulta no valido']); exit; }
    if ($tipo_pago) {
        mysqli_query($conexion, "UPDATE ventas SET tipo_pago = '$tipo_pago' WHERE RIF_clinica='$RIF_clinica' AND id_consulta = $idc");
    }
    if ($mode === 'guardar') {
        if (!$tipo_pago) { echo json_encode(['success' => false, 'message' => 'Debe seleccionar un tipo de pago']); exit; }
        echo json_encode(['success' => true, 'message' => 'Tipo de pago guardado correctamente']);
        exit;
    }
    // mode = descargar — generate PDF
    require __DIR__ . '/../../vendor/autoload.php';
    $res = mysqli_query($conexion, "SELECT c.*, m.nombre AS mascota_nombre, m.especie, m.raza, m.edad, m.sexo, m.peso, p.nombres AS prop_nombre, p.apellidos AS prop_apellido, p.telefono AS prop_telefono, v.Nombres AS nombre_veterinario, v2.servicios AS ventas_servicios, v2.total AS ventas_total FROM consulta c JOIN mascota m ON c.id_mascota = m.id_mascota LEFT JOIN propietario p ON c.id_propietario = p.id_propietario LEFT JOIN veterinario v ON c.id_veterinario = v.Id_veterinario LEFT JOIN ventas v2 ON v2.id_consulta = c.id_consulta WHERE c.RIF_clinica='$RIF_clinica' AND c.id_consulta=$idc LIMIT 1");
    $r = mysqli_fetch_assoc($res);
    if (!$r) { echo json_encode(['success' => false, 'message' => 'Consulta no encontrada']); exit; }
    $items = [];
    $total = 0;
    if ($r['ventas_servicios']) {
        $items = json_decode($r['ventas_servicios'], true) ?: [];
        $total = (float)($r['ventas_total'] ?? 0);
    } else {
        $tr = mysqli_query($conexion, "SELECT nombre FROM test_rapidos WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$idc");
        $tests = []; while ($t = mysqli_fetch_assoc($tr)) { $tests[] = $t['nombre']; }
        $lr = mysqli_query($conexion, "SELECT tipo FROM laboratorio WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$idc");
        $labs = []; while ($l = mysqli_fetch_assoc($lr)) { $labs[] = $l['tipo']; }
        $vr = mysqli_query($conexion, "SELECT nombre FROM vacunas WHERE RIF_clinica='$RIF_clinica' AND id_consulta=$idc");
        $vacs = []; while ($v = mysqli_fetch_assoc($vr)) { $vacs[] = $v['nombre']; }
        $pres = mysqli_query($conexion, "SELECT servicio, precio FROM precios WHERE RIF_clinica='$RIF_clinica'");
        $precios = [];
        while ($p = mysqli_fetch_assoc($pres)) { $precios[$p['servicio']] = (float)$p['precio']; }
        $items[] = ['servicio' => 'Consulta General', 'precio' => $precios['Consulta General'] ?? 0];
        foreach ($tests as $t) { $items[] = ['servicio' => $t, 'precio' => $precios[$t] ?? 0]; }
        foreach ($labs as $l) { $items[] = ['servicio' => $l, 'precio' => $precios[$l] ?? 0]; }
        foreach ($vacs as $v) { $items[] = ['servicio' => $v, 'precio' => $precios[$v] ?? 0]; }
        $total = array_sum(array_column($items, 'precio'));
    }
    $tasaBCV = obtenerTasaBCV();
    $totalBs = $tasaBCV > 0 ? round($total * $tasaBCV, 2) : 0;
    $es_multi = ($r['id_mascota'] ?? '') === 'Multi';
    $pacientes_lista = [];
    $items_visibles = [];
    foreach ($items as $it) {
        if (isset($it['_meta']) && $it['_meta']) {
            $pacientes_lista = $it['pacientes'] ?? [];
        } else {
            $items_visibles[] = $it;
        }
    }
    $cli = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT nombre AS Nombre_clinic, RIF_clinica FROM clinica WHERE RIF_clinica='$RIF_clinica' LIMIT 1"));
    $cli_nombre = $cli['Nombre_clinic'] ?? 'Clinica Veterinaria';
    $cli_rif = $cli['RIF_clinica'] ?? '';
    ob_start();
    ?>
    <!DOCTYPE html>
    <html><head><meta charset="utf-8"><title>Factura</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:Arial,sans-serif; background:#fff; padding:30px; color:#1f2937; }
        .container { max-width:750px; margin:0 auto; border:2px solid #059669; border-radius:12px; overflow:hidden; }
        .header { background:#059669; color:#fff; padding:20px 30px; }
        .header table { width:100%; }
        .header .title { font-size:22px; font-weight:bold; }
        .header .clinic-data { text-align:right; font-size:14px; }
        .header .subtitle { text-align:center; font-size:18px; font-weight:bold; margin-top:10px; padding-top:10px; border-top:1px solid rgba(255,255,255,0.3); }
        .content { padding:20px 30px; }
        table.data { width:100%; border-collapse:collapse; margin-bottom:20px; }
        table.data th { background:#ecfdf5; padding:8px 12px; font-weight:bold; color:#065f46; border:1px solid #a7f3d0; text-align:left; }
        table.data td { padding:6px 12px; border:1px solid #d1d5db; }
        table.data .label { color:#6b7280; width:120px; }
        .total-row td { font-weight:bold; background:#ecfdf5; border-top:2px solid #059669; }
        .foot { background:#f3f4f6; padding:10px 30px; text-align:center; font-size:12px; color:#6b7280; border-top:1px solid #d1d5db; }
    </style>
    </head><body>
    <div class="container">
        <div class="header">
            <table><tr>
                <td class="title">CLINICA VETERINARIA</td>
                <td class="clinic-data"><?php echo htmlspecialchars($cli_nombre); ?><br>RIF: <?php echo htmlspecialchars($cli_rif); ?></td>
            </tr></table>
            <div class="subtitle">FACTURA</div>
        </div>
        <div class="content">
            <?php if ($es_multi && $pacientes_lista): ?>
            <table class="data"><tr><th colspan="2">PACIENTES</th></tr>
                <tr><td colspan="2"><?php echo htmlspecialchars(implode(', ', $pacientes_lista)); ?></td></tr>
            </table>
            <?php else: ?>
            <table class="data"><tr><th colspan="2">DATOS DEL PACIENTE</th></tr>
                <tr><td class="label">Cedula</td><td><b><?php echo htmlspecialchars($r['id_mascota']); ?></b></td></tr>
                <tr><td class="label">Nombre</td><td><?php echo htmlspecialchars($r['mascota_nombre']); ?></td></tr>
                <tr><td class="label">Especie</td><td><?php echo htmlspecialchars($r['especie']); ?></td></tr>
                <tr><td class="label">Raza</td><td><?php echo htmlspecialchars($r['raza']); ?></td></tr>
                <tr><td class="label">Edad</td><td><?php echo htmlspecialchars($r['edad']); ?></td></tr>
                <tr><td class="label">Sexo</td><td><?php echo htmlspecialchars($r['sexo']); ?></td></tr>
            </table>
            <?php endif; ?>
            <table class="data"><tr><th colspan="2">PROPIETARIO</th></tr>
                <tr><td class="label">Cedula</td><td><?php echo htmlspecialchars($r['id_propietario'] ?? ''); ?></td></tr>
                <tr><td class="label">Nombre</td><td><?php echo htmlspecialchars(($r['prop_nombre'] ?? '') . ' ' . ($r['prop_apellido'] ?? '')); ?></td></tr>
                <tr><td class="label">Telefono</td><td><?php echo htmlspecialchars($r['prop_telefono'] ?? ''); ?></td></tr>
            </table>
            <table class="data"><tr><th colspan="2">DETALLE DE SERVICIOS</th></tr>
                <tr><th style="padding:8px 12px;border:1px solid #a7f3d0;text-align:left;background:#ecfdf5;color:#065f46;">Servicio</th>
                    <th style="padding:8px 12px;border:1px solid #a7f3d0;text-align:right;background:#ecfdf5;color:#065f46;width:120px;">Precio</th></tr>
                <?php foreach ($items_visibles as $it): ?>
                <tr><td style="padding:6px 12px;border:1px solid #d1d5db;"><?php echo htmlspecialchars($it['servicio']); ?></td>
                    <td style="padding:6px 12px;border:1px solid #d1d5db;text-align:right;">$<?php echo number_format($it['precio'], 2); ?></td></tr>
                <?php endforeach; ?>
                <tr class="total-row"><td style="padding:8px 12px;border:1px solid #d1d5db;font-weight:bold;background:#ecfdf5;border-top:2px solid #059669;">TOTAL</td>
                    <td style="padding:8px 12px;border:1px solid #d1d5db;text-align:right;font-weight:bold;background:#ecfdf5;border-top:2px solid #059669;font-size:16px;">$<?php echo number_format($total, 2); ?></td></tr>
                <?php if ($totalBs > 0): ?>
                <tr class="total-row"><td style="padding:8px 12px;border:1px solid #d1d5db;font-weight:bold;background:#ecfdf5;border-top:2px solid #059669;">TOTAL EN BS</td>
                    <td style="padding:8px 12px;border:1px solid #d1d5db;text-align:right;font-weight:bold;background:#ecfdf5;border-top:2px solid #059669;">Bs <?php echo number_format($totalBs, 2, ',', '.'); ?></td></tr>
                <?php endif; ?>
            </table>
        </div>
        <div class="foot">Generado el <?php echo date('d/m/Y'); ?> &mdash; <?php echo htmlspecialchars($cli_nombre); ?></div>
    </div>
    </body></html>
    <?php
    $html = ob_get_clean();
    $dompdf = new Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->render();
    $dompdf->stream("factura_{$idc}.pdf", ['Attachment' => true]);
    exit;
}

if ($action === 'buscar_historial_ventas') {
    $cedula = mysqli_real_escape_string($conexion, $_GET['cedula'] ?? '');
    $fecha_inicio = mysqli_real_escape_string($conexion, $_GET['fecha_inicio'] ?? '');
    $fecha_fin = mysqli_real_escape_string($conexion, $_GET['fecha_fin'] ?? '');
    $pagina = max(1, intval($_GET['pagina'] ?? 1));
    $por_pagina = max(1, min(50, intval($_GET['por_pagina'] ?? 10)));
    $offset = ($pagina - 1) * $por_pagina;

    if (!$cedula) {
        echo json_encode(['success' => false, 'message' => 'Ingrese una cédula']);
        exit;
    }

    $filtro_fecha_inicio = $fecha_inicio ? "AND v.fecha >= '$fecha_inicio'" : '';
    $filtro_fecha_fin = $fecha_fin ? "AND v.fecha <= '$fecha_fin'" : '';
    $filtro_fecha_inicio2 = $fecha_inicio ? "AND vs.fecha >= '$fecha_inicio'" : '';
    $filtro_fecha_fin2 = $fecha_fin ? "AND vs.fecha <= '$fecha_fin'" : '';

    $count_r = mysqli_query($conexion, "
        SELECT COUNT(*) AS total FROM (
            SELECT v.id_venta FROM venta v
            WHERE v.RIF_clinica='$RIF_clinica' AND v.id_cliente LIKE '%$cedula%' $filtro_fecha_inicio $filtro_fecha_fin
            UNION ALL
            SELECT vs.id_consulta FROM ventas vs
            LEFT JOIN propietario p ON vs.id_propietario = p.id_propietario
            WHERE vs.RIF_clinica='$RIF_clinica' AND p.id_propietario LIKE '%$cedula%' $filtro_fecha_inicio2 $filtro_fecha_fin2
        ) AS sub
    ");
    $total = (int)mysqli_fetch_assoc($count_r)['total'];
    $total_paginas = max(1, ceil($total / $por_pagina));

    $res = mysqli_query($conexion, "
        (SELECT v.id_venta AS id, 'venta' AS tipo, v.fecha, v.id_cliente AS cedula,
                v.nombres_cliente AS nombre, v.apellidos_cliente AS apellidos, v.total,
                v.telefono_cliente AS telefono, v.email_cliente AS email, v.tipo_pago
         FROM venta v
         WHERE v.RIF_clinica='$RIF_clinica' AND v.id_cliente LIKE '%$cedula%' $filtro_fecha_inicio $filtro_fecha_fin)
        UNION ALL
        (SELECT vs.id_consulta AS id, 'consulta' AS tipo, vs.fecha, p.id_propietario AS cedula,
                p.nombres AS nombre, p.apellidos AS apellidos, vs.total,
                p.telefono AS telefono, p.gmail AS email, '' AS tipo_pago
         FROM ventas vs
         LEFT JOIN propietario p ON vs.id_propietario = p.id_propietario
         WHERE vs.RIF_clinica='$RIF_clinica' AND p.id_propietario LIKE '%$cedula%' $filtro_fecha_inicio2 $filtro_fecha_fin2)
        ORDER BY fecha DESC
        LIMIT $por_pagina OFFSET $offset
    ");

    $datos = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $datos[] = $r;
    }

    echo json_encode([
        'success' => true,
        'total' => $total,
        'pagina' => $pagina,
        'por_pagina' => $por_pagina,
        'total_paginas' => $total_paginas,
        'datos' => $datos
    ]);
    exit;
}

if ($action === 'detalle_historial') {
    $id = intval($_GET['id'] ?? 0);
    $tipo = $_GET['tipo'] ?? '';

    if (!$id || !$tipo) {
        echo json_encode(['success' => false, 'message' => 'Parametros invalidos']);
        exit;
    }

    if ($tipo === 'venta') {
        $vr = mysqli_query($conexion, "SELECT * FROM venta WHERE RIF_clinica='$RIF_clinica' AND id_venta = $id LIMIT 1");
        $v = mysqli_fetch_assoc($vr);
        if (!$v) {
            echo json_encode(['success' => false, 'message' => 'Venta no encontrada']);
            exit;
        }
        $dr = mysqli_query($conexion, "SELECT * FROM detalle_venta WHERE RIF_clinica='$RIF_clinica' AND id_venta = $id");
        $items = [];
        while ($d = mysqli_fetch_assoc($dr)) {
            $items[] = $d;
        }
        $tasaBCV = obtenerTasaBCV();
        echo json_encode([
            'success' => true,
            'tipo' => 'venta',
            'cliente' => [
                'cedula' => $v['id_cliente'],
                'nombres' => $v['nombres_cliente'],
                'apellidos' => $v['apellidos_cliente'],
                'telefono' => $v['telefono_cliente'],
                'email' => $v['email_cliente']
            ],
            'fecha' => $v['fecha'],
            'total' => (float)$v['total'],
            'tipo_pago' => $v['tipo_pago'] ?? '',
            'id' => (int)$v['id_venta'],
            'items' => $items,
            'tasa_bcv' => $tasaBCV,
            'total_bs' => $tasaBCV > 0 ? round((float)$v['total'] * $tasaBCV, 2) : 0
        ]);
        exit;
    }

    if ($tipo === 'consulta') {
        $vr = mysqli_query($conexion, "
            SELECT vs.*, p.nombres, p.apellidos, p.telefono, p.gmail AS email
            FROM ventas vs
            LEFT JOIN propietario p ON vs.id_propietario = p.id_propietario
            WHERE vs.RIF_clinica='$RIF_clinica' AND vs.id_consulta = $id LIMIT 1
        ");
        $v = mysqli_fetch_assoc($vr);
        if (!$v) {
            echo json_encode(['success' => false, 'message' => 'Factura de consulta no encontrada']);
            exit;
        }
        $items = json_decode($v['servicios'], true) ?: [];
        $tasaBCV = obtenerTasaBCV();
        echo json_encode([
            'success' => true,
            'tipo' => 'consulta',
            'cliente' => [
                'cedula' => $v['id_propietario'] ?? '',
                'nombres' => $v['nombres'] ?? '',
                'apellidos' => $v['apellidos'] ?? '',
                'telefono' => $v['telefono'] ?? '',
                'email' => $v['email'] ?? ''
            ],
            'fecha' => $v['fecha'],
            'total' => (float)$v['total'],
            'tipo_pago' => '',
            'id' => (int)$v['id_consulta'],
            'items' => $items,
            'tasa_bcv' => $tasaBCV,
            'total_bs' => $tasaBCV > 0 ? round((float)$v['total'] * $tasaBCV, 2) : 0
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Tipo no valido']);
    exit;
}

if ($action === 'enviar_factura_historial') {
    $id = intval($_POST['id'] ?? 0);
    $tipo = $_POST['tipo'] ?? '';
    $email_to = $_POST['email'] ?? '';

    if (!$id || !$tipo || !$email_to) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos']);
        exit;
    }

    require_once __DIR__ . '/../../vendor/autoload.php';

    if ($tipo === 'venta') {
        $vr = mysqli_query($conexion, "SELECT * FROM venta WHERE RIF_clinica='$RIF_clinica' AND id_venta = $id LIMIT 1");
        $v = mysqli_fetch_assoc($vr);
        if (!$v) { echo json_encode(['success' => false, 'message' => 'Venta no encontrada']); exit; }
        $dr = mysqli_query($conexion, "SELECT * FROM detalle_venta WHERE RIF_clinica='$RIF_clinica' AND id_venta = $id");
        $detalle = [];
        while ($d = mysqli_fetch_assoc($dr)) { $detalle[] = $d; }
        $items = [];
        foreach ($detalle as $d) {
            $items[] = [
                'servicio' => $d['id_producto'] . ' - ' . $d['descripcion'],
                'precio' => (float)$d['precio_unitario'],
                'cantidad' => (int)$d['cantidad'],
                'subtotal' => (float)$d['subtotal']
            ];
        }
        $total = (float)$v['total'];
        $cli_nombre = $v['nombres_cliente'] . ' ' . $v['apellidos_cliente'];
        $cli_cedula = $v['id_cliente'];
        $cli_telefono = $v['telefono_cliente'];
    } elseif ($tipo === 'consulta') {
        $vr = mysqli_query($conexion, "
            SELECT vs.*, p.nombres, p.apellidos, p.telefono, p.gmail
            FROM ventas vs
            LEFT JOIN propietario p ON vs.id_propietario = p.id_propietario
            WHERE vs.RIF_clinica='$RIF_clinica' AND vs.id_consulta = $id LIMIT 1
        ");
        $v = mysqli_fetch_assoc($vr);
        if (!$v) { echo json_encode(['success' => false, 'message' => 'Factura no encontrada']); exit; }
        $items = json_decode($v['servicios'], true) ?: [];
        $total = (float)$v['total'];
        $cli_nombre = ($v['nombres'] ?? '') . ' ' . ($v['apellidos'] ?? '');
        $cli_cedula = $v['id_propietario'] ?? '';
        $cli_telefono = $v['telefono'] ?? '';
    } else {
        echo json_encode(['success' => false, 'message' => 'Tipo no valido']);
        exit;
    }

    $cli_res = mysqli_query($conexion, "SELECT nombre AS Nombre_clinic, RIF_clinica FROM clinica WHERE RIF_clinica='$RIF_clinica' LIMIT 1");
    $cli_row = mysqli_fetch_assoc($cli_res);
    $clinic_nombre = $cli_row['Nombre_clinic'] ?? 'Clinica Veterinaria';
    $clinic_rif = $cli_row['RIF_clinica'] ?? '';

    ob_start();
    ?>
    <!DOCTYPE html>
    <html><head><meta charset="utf-8"><title>Factura #<?php echo $id; ?></title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:Arial,sans-serif; background:#fff; padding:30px; color:#1f2937; }
        .container { max-width:750px; margin:0 auto; border:2px solid #059669; border-radius:12px; overflow:hidden; }
        .header { background:#059669; color:#fff; padding:20px 30px; }
        .header table { width:100%; }
        .header .title { font-size:22px; font-weight:bold; }
        .header .clinic-data { text-align:right; font-size:14px; }
        .header .subtitle { text-align:center; font-size:18px; font-weight:bold; margin-top:10px; padding-top:10px; border-top:1px solid rgba(255,255,255,0.3); }
        .content { padding:20px 30px; }
        table.data { width:100%; border-collapse:collapse; margin-bottom:20px; }
        table.data th { background:#ecfdf5; padding:8px 12px; font-weight:bold; color:#065f46; border:1px solid #a7f3d0; text-align:left; }
        table.data td { padding:6px 12px; border:1px solid #d1d5db; }
        table.data .label { color:#6b7280; width:120px; }
        .total-row td { font-weight:bold; background:#ecfdf5; border-top:2px solid #059669; }
        .foot { background:#f3f4f6; padding:10px 30px; text-align:center; font-size:12px; color:#6b7280; border-top:1px solid #d1d5db; }
    </style>
    </head><body>
    <div class="container">
        <div class="header">
            <table><tr>
                <td class="title">CLINICA VETERINARIA</td>
                <td class="clinic-data"><?php echo htmlspecialchars($clinic_nombre); ?><br>RIF: <?php echo htmlspecialchars($clinic_rif); ?></td>
            </tr></table>
            <div class="subtitle">FACTURA #<?php echo $id; ?> <?php echo $tipo === 'consulta' ? '(Consulta)' : '(Venta)'; ?></div>
        </div>
        <div class="content">
            <table class="data"><tr><th colspan="2">DATOS DEL CLIENTE</th></tr>
                <tr><td class="label">Cedula</td><td><b><?php echo htmlspecialchars($cli_cedula); ?></b></td></tr>
                <tr><td class="label">Nombre</td><td><?php echo htmlspecialchars($cli_nombre); ?></td></tr>
                <tr><td class="label">Telefono</td><td><?php echo htmlspecialchars($cli_telefono); ?></td></tr>
                <tr><td class="label">Fecha</td><td><?php echo htmlspecialchars($v['fecha']); ?></td></tr>
            </table>
            <table class="data"><tr><th colspan="<?php echo $tipo === 'venta' ? '4' : '2'; ?>">DETALLE</th></tr>
                <tr>
                    <th style="padding:8px 12px;border:1px solid #a7f3d0;text-align:left;background:#ecfdf5;color:#065f46;"><?php echo $tipo === 'venta' ? 'Producto' : 'Servicio'; ?></th>
                    <?php if ($tipo === 'venta'): ?>
                    <th style="padding:8px 12px;border:1px solid #a7f3d0;text-align:center;background:#ecfdf5;color:#065f46;width:60px;">Cant</th>
                    <th style="padding:8px 12px;border:1px solid #a7f3d0;text-align:right;background:#ecfdf5;color:#065f46;width:100px;">Precio</th>
                    <th style="padding:8px 12px;border:1px solid #a7f3d0;text-align:right;background:#ecfdf5;color:#065f46;width:100px;">Subtotal</th>
                    <?php else: ?>
                    <th style="padding:8px 12px;border:1px solid #a7f3d0;text-align:right;background:#ecfdf5;color:#065f46;width:120px;">Precio</th>
                    <?php endif; ?>
                </tr>
                <?php foreach ($items as $it): ?>
                <tr>
                    <td style="padding:6px 12px;border:1px solid #d1d5db;"><?php echo htmlspecialchars($it['servicio'] ?? ($tipo === 'venta' ? ($it['id_producto'] . ' - ' . $it['descripcion']) : '')); ?></td>
                    <?php if ($tipo === 'venta'): ?>
                    <td style="padding:6px 12px;border:1px solid #d1d5db;text-align:center;"><?php echo $it['cantidad']; ?></td>
                    <td style="padding:6px 12px;border:1px solid #d1d5db;text-align:right;">$<?php echo number_format((float)($it['precio'] ?? $it['precio_unitario']), 2); ?></td>
                    <td style="padding:6px 12px;border:1px solid #d1d5db;text-align:right;">$<?php echo number_format((float)($it['subtotal'] ?? 0), 2); ?></td>
                    <?php else: ?>
                    <td style="padding:6px 12px;border:1px solid #d1d5db;text-align:right;">$<?php echo number_format((float)($it['precio'] ?? 0), 2); ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="<?php echo $tipo === 'venta' ? '3' : '1'; ?>" style="padding:8px 12px;border:1px solid #d1d5db;font-weight:bold;background:#ecfdf5;border-top:2px solid #059669;text-align:right;">TOTAL</td>
                    <td style="padding:8px 12px;border:1px solid #d1d5db;text-align:right;font-weight:bold;background:#ecfdf5;border-top:2px solid #059669;font-size:16px;">$<?php echo number_format($total, 2); ?></td>
                </tr>
            </table>
        </div>
        <div class="foot">Generado el <?php echo date('d/m/Y'); ?> &mdash; <?php echo htmlspecialchars($clinic_nombre); ?></div>
    </div>
    </body></html>
    <?php
    $html = ob_get_clean();
    $dompdf = new Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->render();
    $pdf = $dompdf->output();

    $smtp = require __DIR__ . '/../../config/email.php';
    set_time_limit(120);
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function($str, $level) { file_put_contents('C:/xampp/apache/logs/phpmailer_debug.log', date('Y-m-d H:i:s') . " [$level] $str\n", FILE_APPEND); };
        $mail->Host = $smtp['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp['username'];
        $mail->Password = str_replace(' ', '', $smtp['password']);
        $mail->SMTPSecure = $smtp['encryption'];
        $mail->Port = $smtp['port'];
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($smtp['username'], $smtp['from_name']);
        $mail->addAddress($email_to, $cli_nombre);
        $tipo_label = $tipo === 'consulta' ? 'Consulta' : 'Venta';
        $mail->Subject = "Factura #$id ($tipo_label) - $clinic_nombre";
        $mail->Body = "Hola $cli_nombre,\n\nAdjuntamos la factura #$id de $tipo_label.\n\nTotal: \$$total\n\nGracias por su preferencia.";
        $mail->addStringAttachment($pdf, "factura_$id.pdf");
        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Factura enviada correctamente']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error al enviar el correo: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'registros_consultas') {
    $filtro = $_GET['filtro'] ?? 'mes';
    if ($filtro === 'semana') {
        $where = "RIF_clinica='$RIF_clinica' AND fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $group = "DATE(fecha)";
    } elseif ($filtro === 'año') {
        $where = "RIF_clinica='$RIF_clinica' AND fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        $group = "DATE_FORMAT(fecha, '%Y-%m')";
    } else {
        $where = "RIF_clinica='$RIF_clinica' AND fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $group = "DATE(fecha)";
    }
    $chart = [];
    $r = mysqli_query($conexion, "SELECT $group AS periodo, COUNT(*) AS consultas FROM consulta WHERE $where GROUP BY periodo ORDER BY periodo ASC");
    while ($f = mysqli_fetch_assoc($r)) { $chart[] = $f; }
    $rp = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(DISTINCT id_mascota) AS total FROM consulta WHERE $where"));
    $rs = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COALESCE(SUM(total),0) AS total FROM ventas WHERE $where"));
    echo json_encode(['chart' => $chart, 'total_pacientes' => (int)($rp['total'] ?? 0), 'total_servicios' => (float)($rs['total'] ?? 0)]);
    exit;
}

if ($action === 'registros_inventario') {
    $filtro = $_GET['filtro'] ?? 'mes';
    if ($filtro === 'semana') {
        $where = "v.RIF_clinica='$RIF_clinica' AND v.fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $date_where = "fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    } elseif ($filtro === 'año') {
        $where = "v.RIF_clinica='$RIF_clinica' AND v.fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        $date_where = "fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
    } else {
        $where = "v.RIF_clinica='$RIF_clinica' AND v.fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $date_where = "fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    }
    $group = ($filtro === 'año') ? "DATE_FORMAT(v.fecha, '%Y-%m')" : "DATE(v.fecha)";
    $chart = [];
    $r = mysqli_query($conexion, "SELECT $group AS periodo, COUNT(DISTINCT v.id_venta) AS ventas, COALESCE(SUM(dv.cantidad),0) AS productos_vendidos FROM venta v LEFT JOIN detalle_venta dv ON v.id_venta = dv.id_venta WHERE $where GROUP BY periodo ORDER BY periodo ASC");
    while ($f = mysqli_fetch_assoc($r)) { $chart[] = $f; }
    $ri = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COALESCE(SUM(cantidad),0) AS total FROM proveedor WHERE RIF_clinica='$RIF_clinica'"));
    $rv_cnt = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) AS total FROM venta WHERE RIF_clinica='$RIF_clinica' AND $date_where"));
    $total_ventas_count = (int)($rv_cnt['total'] ?? 0);
    $total_inversiones = (float)($ri['total'] ?? 0);
    echo json_encode(['chart' => $chart, 'total_inversiones' => $total_inversiones, 'total_ventas_count' => $total_ventas_count]);
    exit;
}

if ($action === 'registros_detalles') {
    $filtro = $_GET['filtro'] ?? 'mes';
    if ($filtro === 'semana') {
        $where = "c.RIF_clinica='$RIF_clinica' AND c.fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    } elseif ($filtro === 'año') {
        $where = "c.RIF_clinica='$RIF_clinica' AND c.fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
    } else {
        $where = "c.RIF_clinica='$RIF_clinica' AND c.fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    }
    $tests = [];
    $r = mysqli_query($conexion, "SELECT t.nombre, COUNT(*) AS total FROM test_rapidos t JOIN consulta c ON t.id_consulta = c.id_consulta WHERE $where GROUP BY t.nombre ORDER BY total DESC");
    while ($f = mysqli_fetch_assoc($r)) { $tests[] = $f; }
    $labs = [];
    $r = mysqli_query($conexion, "SELECT l.tipo AS nombre, COUNT(*) AS total FROM laboratorio l JOIN consulta c ON l.id_consulta = c.id_consulta WHERE $where GROUP BY l.tipo ORDER BY total DESC");
    while ($f = mysqli_fetch_assoc($r)) { $labs[] = $f; }
    $vacs = [];
    $r = mysqli_query($conexion, "SELECT v.nombre, COUNT(*) AS total FROM vacunas v JOIN consulta c ON v.id_consulta = c.id_consulta WHERE $where GROUP BY v.nombre ORDER BY total DESC");
    while ($f = mysqli_fetch_assoc($r)) { $vacs[] = $f; }
    echo json_encode(['tests' => $tests, 'laboratorio' => $labs, 'vacunas' => $vacs]);
    exit;
}

if ($action === 'registros_pagos') {
    $filtro = $_GET['filtro'] ?? 'mes';
    if ($filtro === 'semana') {
        $where = "t.RIF_clinica='$RIF_clinica' AND t.fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    } elseif ($filtro === 'año') {
        $where = "t.RIF_clinica='$RIF_clinica' AND t.fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
    } else {
        $where = "t.RIF_clinica='$RIF_clinica' AND t.fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    }
    $r = mysqli_query($conexion, "SELECT t.tipo_pago, SUM(t.total) AS total FROM (SELECT fecha, tipo_pago, total, RIF_clinica FROM venta WHERE RIF_clinica='$RIF_clinica' UNION ALL SELECT fecha, tipo_pago, total, RIF_clinica FROM ventas WHERE RIF_clinica='$RIF_clinica') t WHERE $where AND t.tipo_pago IS NOT NULL AND t.tipo_pago != '' GROUP BY t.tipo_pago ORDER BY total DESC");
    $chart = [];
    $mapa = ['efectivo_bs' => 'Efectivo Bs', 'efectivo_usd' => 'Efectivo $', 'pago movil' => 'Pago Móvil', 'punto' => 'Punto'];
    $total_ingresos = 0;
    $total_transacciones = 0;
    while ($f = mysqli_fetch_assoc($r)) {
        $chart[] = ['tipo' => $f['tipo_pago'], 'total' => (float)$f['total'], 'label' => $mapa[$f['tipo_pago']] ?? $f['tipo_pago']];
        $total_ingresos += (float)$f['total'];
        $total_transacciones++;
    }
    echo json_encode(['chart' => $chart, 'total_ingresos' => $total_ingresos, 'total_transacciones' => $total_transacciones]);
    exit;
}

if ($action === 'pdf_registro_consultas') {
    require __DIR__ . '/../../vendor/autoload.php';
    $filtro = $_GET['filtro'] ?? 'mes';
    $tab = $_GET['tab'] ?? 'consultas';
    $cli_r = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT nombre AS Nombre_clinic, RIF_clinica FROM clinica WHERE RIF_clinica='$RIF_clinica' LIMIT 1"));
    $cli_nombre = $cli_r['Nombre_clinic'] ?? 'Clinica Veterinaria';
    $cli_rif = $cli_r['RIF_clinica'] ?? '';
    if ($filtro === 'semana') {
        $where = "RIF_clinica='$RIF_clinica' AND fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $label = 'los ultimos 7 dias';
    } elseif ($filtro === 'año') {
        $where = "RIF_clinica='$RIF_clinica' AND fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        $label = 'los ultimos 12 meses';
    } else {
        $where = "RIF_clinica='$RIF_clinica' AND fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $label = 'los ultimos 30 dias';
    }
    if ($tab === 'inventario') {
        $group = ($filtro === 'año') ? "DATE_FORMAT(v.fecha, '%Y-%m')" : "DATE(v.fecha)";
        $w2 = str_replace('fecha', 'v.fecha', $where);
        $w2 = str_replace('RIF_clinica=', 'v.RIF_clinica=', $w2);
        $r = mysqli_query($conexion, "SELECT $group AS periodo, COUNT(DISTINCT v.id_venta) AS ventas, COALESCE(SUM(dv.cantidad),0) AS productos_vendidos FROM venta v LEFT JOIN detalle_venta dv ON v.id_venta = dv.id_venta WHERE $w2 GROUP BY periodo ORDER BY periodo ASC");
        $chart = [];
        while ($f = mysqli_fetch_assoc($r)) { $chart[] = $f; }
        $ri = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COALESCE(SUM(cantidad),0) AS total FROM proveedor WHERE RIF_clinica='$RIF_clinica'"));
        $rv_cnt = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) AS total FROM venta WHERE RIF_clinica='$RIF_clinica' AND $w2"));
        $total_inversiones = (float)($ri['total'] ?? 0);
        $total_ventas_pdf = (int)($rv_cnt['total'] ?? 0);
        ob_start(); ?>
        <!DOCTYPE html><html><head><meta charset="utf-8"><title>Reporte Inventario</title>
        <style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:Arial,sans-serif;background:#fff;padding:30px;color:#1f2937}.container{max-width:800px;margin:0 auto;border:2px solid #059669;border-radius:12px;overflow:hidden}.header{background:#059669;color:#fff;padding:20px 30px;text-align:center}.header h1{font-size:22px;font-weight:bold}.header p{font-size:14px;opacity:0.9;margin-top:4px}.content{padding:20px 30px}table.data{width:100%;border-collapse:collapse;margin-bottom:20px}table.data th{background:#ecfdf5;padding:8px 12px;font-weight:bold;color:#065f46;border:1px solid #a7f3d0;text-align:left}table.data td{padding:6px 12px;border:1px solid #d1d5db}.total-row td{font-weight:bold;background:#ecfdf5;border-top:2px solid #059669}.stats{display:flex;gap:16px;margin-bottom:20px}.stat-box{flex:1;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:8px;padding:16px;text-align:center}.stat-box .num{font-size:24px;font-weight:bold;color:#065f46}.stat-box .lbl{font-size:12px;color:#6b7280;margin-top:4px}.foot{background:#f3f4f6;padding:10px 30px;text-align:center;font-size:12px;color:#6b7280;border-top:1px solid #d1d5db}</style></head><body>
        <div class="container"><div class="header"><h1><?php echo htmlspecialchars($cli_nombre); ?></h1><p>Reporte de Inventario - <?php echo $label; ?></p></div>
        <div class="content">
            <div class="stats">
                <div class="stat-box"><div class="num">$<?php echo number_format($total_inversiones, 2); ?></div><div class="lbl">Inversiones (proveedores)</div></div>
                <div class="stat-box"><div class="num"><?php echo $total_ventas_pdf; ?></div><div class="lbl">Ventas realizadas</div></div>
            </div>
            <table class="data"><tr><th>Periodo</th><th>Ventas</th><th>Productos vendidos</th></tr>
            <?php foreach ($chart as $c): ?>
            <tr><td><?php echo htmlspecialchars($c['periodo']); ?></td><td><?php echo $c['ventas']; ?></td><td><?php echo $c['productos_vendidos']; ?></td></tr>
            <?php endforeach; ?>
            </table>
        </div>
        <div class="foot">Generado el <?php echo date('d/m/Y'); ?> &mdash; <?php echo htmlspecialchars($cli_nombre); ?></div></div></body></html>
        <?php
    } elseif ($tab === 'detalles') {
        $w = "c.RIF_clinica='$RIF_clinica' AND " . substr($where, strpos($where, 'fecha'));
        $r = mysqli_query($conexion, "SELECT t.nombre, COUNT(*) AS total FROM test_rapidos t JOIN consulta c ON t.id_consulta = c.id_consulta WHERE $w GROUP BY t.nombre ORDER BY total DESC");
        $tests = []; while ($f = mysqli_fetch_assoc($r)) { $tests[] = $f; }
        $r = mysqli_query($conexion, "SELECT l.tipo AS nombre, COUNT(*) AS total FROM laboratorio l JOIN consulta c ON l.id_consulta = c.id_consulta WHERE $w GROUP BY l.tipo ORDER BY total DESC");
        $labs = []; while ($f = mysqli_fetch_assoc($r)) { $labs[] = $f; }
        $r = mysqli_query($conexion, "SELECT v.nombre, COUNT(*) AS total FROM vacunas v JOIN consulta c ON v.id_consulta = c.id_consulta WHERE $w GROUP BY v.nombre ORDER BY total DESC");
        $vacs = []; while ($f = mysqli_fetch_assoc($r)) { $vacs[] = $f; }
        ob_start(); ?>
        <!DOCTYPE html><html><head><meta charset="utf-8"><title>Reporte Detalles de Consulta</title>
        <style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:Arial,sans-serif;background:#fff;padding:30px;color:#1f2937}.container{max-width:800px;margin:0 auto;border:2px solid #059669;border-radius:12px;overflow:hidden}.header{background:#059669;color:#fff;padding:20px 30px;text-align:center}.header h1{font-size:22px}.header p{font-size:14px;opacity:0.9;margin-top:4px}.content{padding:20px 30px}.section{margin-bottom:20px}.section h3{background:#ecfdf5;padding:8px 12px;font-weight:bold;color:#065f46;border:1px solid #a7f3d0;border-radius:6px 6px 0 0;font-size:14px}table.data{width:100%;border-collapse:collapse}table.data th{background:#ecfdf5;padding:6px 10px;font-weight:bold;color:#065f46;border:1px solid #a7f3d0;text-align:left;font-size:12px}table.data td{padding:5px 10px;border:1px solid #d1d5db;font-size:12px}.foot{background:#f3f4f6;padding:10px 30px;text-align:center;font-size:12px;color:#6b7280;border-top:1px solid #d1d5db}</style></head><body>
        <div class="container"><div class="header"><h1><?php echo htmlspecialchars($cli_nombre); ?></h1><p>Detalles de Consulta - <?php echo $label; ?></p></div>
        <div class="content">
            <div class="section"><h3>Tests Rapidos</h3><table class="data"><tr><th>Test</th><th>Cantidad</th></tr>
            <?php foreach ($tests as $t): ?><tr><td><?php echo htmlspecialchars($t['nombre']); ?></td><td><?php echo $t['total']; ?></td></tr><?php endforeach; ?>
            </table></div>
            <div class="section"><h3>Laboratorio</h3><table class="data"><tr><th>Tipo</th><th>Cantidad</th></tr>
            <?php foreach ($labs as $l): ?><tr><td><?php echo htmlspecialchars($l['nombre']); ?></td><td><?php echo $l['total']; ?></td></tr><?php endforeach; ?>
            </table></div>
            <div class="section"><h3>Vacunas</h3><table class="data"><tr><th>Vacuna</th><th>Cantidad</th></tr>
            <?php foreach ($vacs as $v): ?><tr><td><?php echo htmlspecialchars($v['nombre']); ?></td><td><?php echo $v['total']; ?></td></tr><?php endforeach; ?>
            </table></div>
        </div>
        <div class="foot">Generado el <?php echo date('d/m/Y'); ?> &mdash; <?php echo htmlspecialchars($cli_nombre); ?></div></div></body></html>
        <?php
    } elseif ($tab === 'pagos') {
        $r = mysqli_query($conexion, "SELECT t.tipo_pago, SUM(t.total) AS total FROM (SELECT fecha, tipo_pago, total, RIF_clinica FROM venta WHERE RIF_clinica='$RIF_clinica' UNION ALL SELECT fecha, tipo_pago, total, RIF_clinica FROM ventas WHERE RIF_clinica='$RIF_clinica') t WHERE $where AND t.tipo_pago IS NOT NULL AND t.tipo_pago != '' GROUP BY t.tipo_pago ORDER BY total DESC");
        $chart = []; $total_ingresos = 0; $total_transacciones = 0;
        $mapa = ['efectivo_bs' => 'Efectivo Bs', 'efectivo_usd' => 'Efectivo $', 'pago movil' => 'Pago Móvil', 'punto' => 'Punto'];
        while ($f = mysqli_fetch_assoc($r)) {
            $chart[] = $f;
            $total_ingresos += (float)$f['total'];
            $total_transacciones++;
        }
        ob_start(); ?>
        <!DOCTYPE html><html><head><meta charset="utf-8"><title>Reporte Pagos</title>
        <style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:Arial,sans-serif;background:#fff;padding:30px;color:#1f2937}.container{max-width:800px;margin:0 auto;border:2px solid #059669;border-radius:12px;overflow:hidden}.header{background:#059669;color:#fff;padding:20px 30px;text-align:center}.header h1{font-size:22px;font-weight:bold}.header p{font-size:14px;opacity:0.9;margin-top:4px}.content{padding:20px 30px}table.data{width:100%;border-collapse:collapse;margin-bottom:20px}table.data th{background:#ecfdf5;padding:8px 12px;font-weight:bold;color:#065f46;border:1px solid #a7f3d0;text-align:left}table.data td{padding:6px 12px;border:1px solid #d1d5db}.stats{display:flex;gap:16px;margin-bottom:20px}.stat-box{flex:1;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:8px;padding:16px;text-align:center}.stat-box .num{font-size:24px;font-weight:bold;color:#065f46}.stat-box .lbl{font-size:12px;color:#6b7280;margin-top:4px}.foot{background:#f3f4f6;padding:10px 30px;text-align:center;font-size:12px;color:#6b7280;border-top:1px solid #d1d5db}</style></head><body>
        <div class="container"><div class="header"><h1><?php echo htmlspecialchars($cli_nombre); ?></h1><p>Reporte de Tipos de Pago - <?php echo $label; ?></p></div>
        <div class="content">
            <div class="stats">
                <div class="stat-box"><div class="num">$<?php echo number_format($total_ingresos, 2); ?></div><div class="lbl">Total ingresos</div></div>
                <div class="stat-box"><div class="num"><?php echo $total_transacciones; ?></div><div class="lbl">Transacciones</div></div>
            </div>
            <table class="data"><tr><th>Tipo de pago</th><th>Total</th></tr>
            <?php foreach ($chart as $c): ?>
            <tr><td><?php echo htmlspecialchars($mapa[$c['tipo_pago']] ?? $c['tipo_pago']); ?></td><td>$<?php echo number_format((float)$c['total'], 2); ?></td></tr>
            <?php endforeach; ?>
            </table>
        </div>
        <div class="foot">Generado el <?php echo date('d/m/Y'); ?> &mdash; <?php echo htmlspecialchars($cli_nombre); ?></div></div></body></html>
        <?php
    } else {
        $group = ($filtro === 'año') ? "DATE_FORMAT(fecha, '%Y-%m')" : "DATE(fecha)";
        $r = mysqli_query($conexion, "SELECT $group AS periodo, COUNT(*) AS consultas FROM consulta WHERE $where GROUP BY periodo ORDER BY periodo ASC");
        $chart = []; while ($f = mysqli_fetch_assoc($r)) { $chart[] = $f; }
        $rp = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(DISTINCT id_mascota) AS total FROM consulta WHERE $where"));
        $rs = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COALESCE(SUM(total),0) AS total FROM ventas WHERE $where"));
        $total_pacientes = (int)($rp['total'] ?? 0);
        $total_servicios = (float)($rs['total'] ?? 0);
        ob_start(); ?>
        <!DOCTYPE html><html><head><meta charset="utf-8"><title>Reporte Consultas</title>
        <style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:Arial,sans-serif;background:#fff;padding:30px;color:#1f2937}.container{max-width:800px;margin:0 auto;border:2px solid #059669;border-radius:12px;overflow:hidden}.header{background:#059669;color:#fff;padding:20px 30px;text-align:center}.header h1{font-size:22px;font-weight:bold}.header p{font-size:14px;opacity:0.9;margin-top:4px}.content{padding:20px 30px}table.data{width:100%;border-collapse:collapse;margin-bottom:20px}table.data th{background:#ecfdf5;padding:8px 12px;font-weight:bold;color:#065f46;border:1px solid #a7f3d0;text-align:left}table.data td{padding:6px 12px;border:1px solid #d1d5db}.total-row td{font-weight:bold;background:#ecfdf5;border-top:2px solid #059669}.stats{display:flex;gap:16px;margin-bottom:20px}.stat-box{flex:1;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:8px;padding:16px;text-align:center}.stat-box .num{font-size:24px;font-weight:bold;color:#065f46}.stat-box .lbl{font-size:12px;color:#6b7280;margin-top:4px}.foot{background:#f3f4f6;padding:10px 30px;text-align:center;font-size:12px;color:#6b7280;border-top:1px solid #d1d5db}</style></head><body>
        <div class="container"><div class="header"><h1><?php echo htmlspecialchars($cli_nombre); ?></h1><p>Reporte de Consultas - <?php echo $label; ?></p></div>
        <div class="content">
            <div class="stats">
                <div class="stat-box"><div class="num"><?php echo $total_pacientes; ?></div><div class="lbl">Pacientes atendidos</div></div>
                <div class="stat-box"><div class="num">$<?php echo number_format($total_servicios, 2); ?></div><div class="lbl">Servicios prestados</div></div>
            </div>
            <table class="data"><tr><th>Periodo</th><th>Consultas</th></tr>
            <?php foreach ($chart as $c): ?>
            <tr><td><?php echo htmlspecialchars($c['periodo']); ?></td><td><?php echo $c['consultas']; ?></td></tr>
            <?php endforeach; ?>
            </table>
        </div>
        <div class="foot">Generado el <?php echo date('d/m/Y'); ?> &mdash; <?php echo htmlspecialchars($cli_nombre); ?></div></div></body></html>
        <?php
    }
    $html = ob_get_clean();
    $dompdf = new Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->render();
    $dompdf->stream("registro_{$tab}.pdf", ['Attachment' => true]);
    exit;
}

if ($action === 'enviar_reporte_registro') {
    require __DIR__ . '/../../vendor/autoload.php';
    $filtro = $_POST['filtro'] ?? 'mes';
    $tab = $_POST['tab'] ?? 'consultas';
    $cli_r = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT nombre AS Nombre_clinic, RIF_clinica FROM clinica WHERE RIF_clinica='$RIF_clinica' LIMIT 1"));
    $cli_nombre = $cli_r['Nombre_clinic'] ?? 'Clinica Veterinaria';
    $cli_rif = $cli_r['RIF_clinica'] ?? '';
    if ($filtro === 'semana') { $where = "RIF_clinica='$RIF_clinica' AND fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)"; $label = 'los ultimos 7 dias'; }
    elseif ($filtro === 'año') { $where = "RIF_clinica='$RIF_clinica' AND fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)"; $label = 'los ultimos 12 meses'; }
    else { $where = "RIF_clinica='$RIF_clinica' AND fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)"; $label = 'los ultimos 30 dias'; }
    if ($tab === 'inventario') {
        $group = ($filtro === 'año') ? "DATE_FORMAT(v.fecha, '%Y-%m')" : "DATE(v.fecha)";
        $w2 = str_replace('fecha', 'v.fecha', $where);
        $w2 = str_replace('RIF_clinica=', 'v.RIF_clinica=', $w2);
        $r = mysqli_query($conexion, "SELECT $group AS periodo, COUNT(DISTINCT v.id_venta) AS ventas, COALESCE(SUM(dv.cantidad),0) AS productos_vendidos FROM venta v LEFT JOIN detalle_venta dv ON v.id_venta = dv.id_venta WHERE $w2 GROUP BY periodo ORDER BY periodo ASC");
        $chart = []; while ($f = mysqli_fetch_assoc($r)) { $chart[] = $f; }
        $ri = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COALESCE(SUM(cantidad),0) AS total FROM proveedor WHERE RIF_clinica='$RIF_clinica'"));
        $rv_cnt = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) AS total FROM venta WHERE RIF_clinica='$RIF_clinica' AND $w2"));
        $total_inversiones = (float)($ri['total'] ?? 0);
        $total_ventas_pdf = (int)($rv_cnt['total'] ?? 0);
        ob_start(); ?>
        <!DOCTYPE html><html><head><meta charset="utf-8"><title>Reporte Inventario</title>
        <style><?php echo file_get_contents(__DIR__ . '/../../css/output.css'); ?></style></head><body>
        <div class="p-6"><h1 class="text-2xl font-bold text-green-800"><?php echo htmlspecialchars($cli_nombre); ?></h1><p class="text-gray-600">Reporte de Inventario - <?php echo $label; ?></p>
        <div class="grid grid-cols-2 gap-4 my-4"><div class="bg-green-50 p-4 rounded-xl text-center"><div class="text-2xl font-bold text-green-700">$<?php echo number_format($total_inversiones, 2); ?></div><div class="text-sm text-gray-500">Inversiones</div></div><div class="bg-green-50 p-4 rounded-xl text-center"><div class="text-2xl font-bold text-green-700"><?php echo $total_ventas_pdf; ?></div><div class="text-sm text-gray-500">Ventas realizadas</div></div></div>
        <table class="w-full border-collapse"><tr class="bg-green-100"><th class="p-2 border text-left">Periodo</th><th class="p-2 border text-left">Ventas</th><th class="p-2 border text-left">Productos vendidos</th></tr>
        <?php foreach ($chart as $c): ?><tr><td class="p-2 border"><?php echo htmlspecialchars($c['periodo']); ?></td><td class="p-2 border"><?php echo $c['ventas']; ?></td><td class="p-2 border"><?php echo $c['productos_vendidos']; ?></td></tr><?php endforeach; ?>
        </table></div>
        <?php
    } elseif ($tab === 'detalles') {
        $w = "c.RIF_clinica='$RIF_clinica' AND " . substr($where, strpos($where, 'fecha'));
        $r = mysqli_query($conexion, "SELECT t.nombre, COUNT(*) AS total FROM test_rapidos t JOIN consulta c ON t.id_consulta = c.id_consulta WHERE $w GROUP BY t.nombre ORDER BY total DESC");
        $tests = []; while ($f = mysqli_fetch_assoc($r)) { $tests[] = $f; }
        $r = mysqli_query($conexion, "SELECT l.tipo AS nombre, COUNT(*) AS total FROM laboratorio l JOIN consulta c ON l.id_consulta = c.id_consulta WHERE $w GROUP BY l.tipo ORDER BY total DESC");
        $labs = []; while ($f = mysqli_fetch_assoc($r)) { $labs[] = $f; }
        $r = mysqli_query($conexion, "SELECT v.nombre, COUNT(*) AS total FROM vacunas v JOIN consulta c ON v.id_consulta = c.id_consulta WHERE $w GROUP BY v.nombre ORDER BY total DESC");
        $vacs = []; while ($f = mysqli_fetch_assoc($r)) { $vacs[] = $f; }
        ob_start(); ?>
        <!DOCTYPE html><html><head><meta charset="utf-8"><title>Reporte Detalles</title>
        <style><?php echo file_get_contents(__DIR__ . '/../../css/output.css'); ?></style></head><body>
        <div class="p-6"><h1 class="text-2xl font-bold text-green-800"><?php echo htmlspecialchars($cli_nombre); ?></h1><p class="text-gray-600">Detalles de Consulta - <?php echo $label; ?></p>
        <div class="my-4"><h3 class="font-bold text-green-700">Tests Rapidos</h3><table class="w-full border-collapse"><tr class="bg-green-100"><th class="p-2 border text-left">Test</th><th class="p-2 border text-left">Cantidad</th></tr>
        <?php foreach ($tests as $t): ?><tr><td class="p-2 border"><?php echo htmlspecialchars($t['nombre']); ?></td><td class="p-2 border"><?php echo $t['total']; ?></td></tr><?php endforeach; ?>
        </table></div>
        <div class="my-4"><h3 class="font-bold text-green-700">Laboratorio</h3><table class="w-full border-collapse"><tr class="bg-green-100"><th class="p-2 border text-left">Tipo</th><th class="p-2 border text-left">Cantidad</th></tr>
        <?php foreach ($labs as $l): ?><tr><td class="p-2 border"><?php echo htmlspecialchars($l['nombre']); ?></td><td class="p-2 border"><?php echo $l['total']; ?></td></tr><?php endforeach; ?>
        </table></div>
        <div class="my-4"><h3 class="font-bold text-green-700">Vacunas</h3><table class="w-full border-collapse"><tr class="bg-green-100"><th class="p-2 border text-left">Vacuna</th><th class="p-2 border text-left">Cantidad</th></tr>
        <?php foreach ($vacs as $v): ?><tr><td class="p-2 border"><?php echo htmlspecialchars($v['nombre']); ?></td><td class="p-2 border"><?php echo $v['total']; ?></td></tr><?php endforeach; ?>
        </table></div></div>
        <?php
    } elseif ($tab === 'pagos') {
        $r = mysqli_query($conexion, "SELECT t.tipo_pago, SUM(t.total) AS total FROM (SELECT fecha, tipo_pago, total, RIF_clinica FROM venta WHERE RIF_clinica='$RIF_clinica' UNION ALL SELECT fecha, tipo_pago, total, RIF_clinica FROM ventas WHERE RIF_clinica='$RIF_clinica') t WHERE $where AND t.tipo_pago IS NOT NULL AND t.tipo_pago != '' GROUP BY t.tipo_pago ORDER BY total DESC");
        $chart = []; $total_ingresos = 0; $total_transacciones = 0;
        $mapa = ['efectivo_bs' => 'Efectivo Bs', 'efectivo_usd' => 'Efectivo $', 'pago movil' => 'Pago Móvil', 'punto' => 'Punto'];
        while ($f = mysqli_fetch_assoc($r)) {
            $chart[] = $f;
            $total_ingresos += (float)$f['total'];
            $total_transacciones++;
        }
        ob_start(); ?>
        <!DOCTYPE html><html><head><meta charset="utf-8"><title>Reporte Pagos</title>
        <style><?php echo file_get_contents(__DIR__ . '/../../css/output.css'); ?></style></head><body>
        <div class="p-6"><h1 class="text-2xl font-bold text-green-800"><?php echo htmlspecialchars($cli_nombre); ?></h1><p class="text-gray-600">Reporte de Tipos de Pago - <?php echo $label; ?></p>
        <div class="grid grid-cols-2 gap-4 my-4"><div class="bg-green-50 p-4 rounded-xl text-center"><div class="text-2xl font-bold text-green-700">$<?php echo number_format($total_ingresos, 2); ?></div><div class="text-sm text-gray-500">Total ingresos</div></div><div class="bg-green-50 p-4 rounded-xl text-center"><div class="text-2xl font-bold text-green-700"><?php echo $total_transacciones; ?></div><div class="text-sm text-gray-500">Transacciones</div></div></div>
        <table class="w-full border-collapse"><tr class="bg-green-100"><th class="p-2 border text-left">Tipo de pago</th><th class="p-2 border text-left">Total</th></tr>
        <?php foreach ($chart as $c): ?><tr><td class="p-2 border"><?php echo htmlspecialchars($mapa[$c['tipo_pago']] ?? $c['tipo_pago']); ?></td><td class="p-2 border">$<?php echo number_format((float)$c['total'], 2); ?></td></tr><?php endforeach; ?>
        </table></div>
        <?php
    } else {
        $group = ($filtro === 'año') ? "DATE_FORMAT(fecha, '%Y-%m')" : "DATE(fecha)";
        $r = mysqli_query($conexion, "SELECT $group AS periodo, COUNT(*) AS consultas FROM consulta WHERE $where GROUP BY periodo ORDER BY periodo ASC");
        $chart = []; while ($f = mysqli_fetch_assoc($r)) { $chart[] = $f; }
        $rp = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(DISTINCT id_mascota) AS total FROM consulta WHERE $where"));
        $rs = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COALESCE(SUM(total),0) AS total FROM ventas WHERE $where"));
        $total_pacientes = (int)($rp['total'] ?? 0); $total_servicios = (float)($rs['total'] ?? 0);
        ob_start(); ?>
        <!DOCTYPE html><html><head><meta charset="utf-8"><title>Reporte Consultas</title>
        <style><?php echo file_get_contents(__DIR__ . '/../../css/output.css'); ?></style></head><body>
        <div class="p-6"><h1 class="text-2xl font-bold text-green-800"><?php echo htmlspecialchars($cli_nombre); ?></h1><p class="text-gray-600">Reporte de Consultas - <?php echo $label; ?></p>
        <div class="grid grid-cols-2 gap-4 my-4"><div class="bg-green-50 p-4 rounded-xl text-center"><div class="text-2xl font-bold text-green-700"><?php echo $total_pacientes; ?></div><div class="text-sm text-gray-500">Pacientes atendidos</div></div><div class="bg-green-50 p-4 rounded-xl text-center"><div class="text-2xl font-bold text-green-700">$<?php echo number_format($total_servicios, 2); ?></div><div class="text-sm text-gray-500">Servicios prestados</div></div></div>
        <table class="w-full border-collapse"><tr class="bg-green-100"><th class="p-2 border text-left">Periodo</th><th class="p-2 border text-left">Consultas</th></tr>
        <?php foreach ($chart as $c): ?><tr><td class="p-2 border"><?php echo htmlspecialchars($c['periodo']); ?></td><td class="p-2 border"><?php echo $c['consultas']; ?></td></tr><?php endforeach; ?>
        </table></div>
        <?php
    }
    $html = ob_get_clean();
    $smtp = require __DIR__ . '/../../config/email.php';
    $dompdf = new Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->render();
    $pdf = $dompdf->output();
    set_time_limit(120);
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function($str, $level) { file_put_contents('C:/xampp/apache/logs/phpmailer_debug.log', date('Y-m-d H:i:s') . " [$level] $str\n", FILE_APPEND); };
        $mail->Host = $smtp['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp['username'];
        $mail->Password = str_replace(' ', '', $smtp['password']);
        $mail->SMTPSecure = $smtp['encryption'];
        $mail->Port = $smtp['port'];
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($smtp['username'], $smtp['from_name']);
        $mail->addAddress($smtp['username'], $smtp['from_name']);
        $mail->Subject = "Reporte de $tab - $cli_nombre";
        $mail->Body = "Adjuntamos el reporte de $tab ($label).\n\nGenerado el " . date('d/m/Y');
        $mail->addStringAttachment($pdf, "reporte_$tab.pdf");
        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Reporte enviado correctamente']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error al enviar el correo: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'recuperar_enviar_codigo') {
    $idv = mysqli_real_escape_string($conexion, $_POST['id_veterinario'] ?? '');
    $tipo = $_POST['tipo'] ?? 'admin';
    if (!$idv) { echo json_encode(['success' => false, 'message' => 'Ingrese su cédula']); exit; }
    $u = null;
    $tabla = '';
    $id_field = '';
    if ($tipo === 'usuario') {
        $tables = [
            'recepcionista' => 'id_recepcionista',
            'aux-vet' => 'id_auxiliar',
            'propietario' => 'id_propietario'
        ];
        foreach ($tables as $tbl => $fld) {
            $r = mysqli_query($conexion, "SELECT * FROM `$tbl` WHERE `$fld`='$idv' LIMIT 1");
            if (mysqli_num_rows($r) > 0) { $u = mysqli_fetch_assoc($r); $tabla = $tbl; $id_field = $fld; break; }
        }
    } else {
        $r = mysqli_query($conexion, "SELECT * FROM veterinario WHERE Id_veterinario='$idv' LIMIT 1");
        if (mysqli_num_rows($r) > 0) { $u = mysqli_fetch_assoc($r); $tabla = 'veterinario'; $id_field = 'Id_veterinario'; }
    }
    if (!$u) { echo json_encode(['success' => false, 'message' => 'Cédula no registrada']); exit; }
    if (empty($u['Gmail']) && empty($u['gmail'])) { echo json_encode(['success' => false, 'message' => 'El usuario no tiene correo registrado']); exit; }
    $gmail = $u['Gmail'] ?? $u['gmail'] ?? '';
    $nombres = $u['Nombres'] ?? $u['nombres'] ?? '';
    $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $_SESSION['recuperar_codigo'] = $codigo;
    $_SESSION['recuperar_timestamp'] = time();
    $_SESSION['recuperar_id'] = $idv;
    $_SESSION['recuperar_tabla'] = $tabla;
    $_SESSION['recuperar_id_field'] = $id_field;
    require __DIR__ . '/../../vendor/autoload.php';
    $smtp = require __DIR__ . '/../../config/email.php';
    set_time_limit(120);
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function($str, $level) { file_put_contents('C:/xampp/apache/logs/phpmailer_debug.log', date('Y-m-d H:i:s') . " [$level] $str\n", FILE_APPEND); };
        $mail->Host = $smtp['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp['username'];
        $mail->Password = str_replace(' ', '', $smtp['password']);
        $mail->SMTPSecure = $smtp['encryption'];
        $mail->Port = $smtp['port'];
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($smtp['username'], $smtp['from_name']);
        $mail->addAddress($gmail, $nombres);
        $mail->Subject = 'Codigo de recuperacion - ' . $smtp['from_name'];
        $mail->Body = "Hola $nombres,\n\nTu codigo de verificacion es: $codigo\n\nEste codigo expira en 15 minutos.\n\nSi no solicitaste este cambio, ignora este mensaje.";
        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Codigo enviado a tu correo']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error al enviar el correo: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'recuperar_verificar_codigo') {
    $codigo = $_POST['codigo'] ?? '';
    if (!$codigo) { echo json_encode(['success' => false, 'message' => 'Ingrese el codigo']); exit; }
    if (!isset($_SESSION['recuperar_codigo']) || !isset($_SESSION['recuperar_timestamp'])) {
        echo json_encode(['success' => false, 'message' => 'No hay solicitud activa. Solicite un nuevo codigo.']); exit;
    }
    if (time() - $_SESSION['recuperar_timestamp'] > 900) {
        session_destroy();
        echo json_encode(['success' => false, 'message' => 'El codigo ha expirado. Solicite uno nuevo.']); exit;
    }
    if ($codigo !== $_SESSION['recuperar_codigo']) {
        echo json_encode(['success' => false, 'message' => 'Codigo incorrecto']); exit;
    }
    echo json_encode(['success' => true, 'message' => 'Codigo verificado correctamente']);
    exit;
}

if ($action === 'recuperar_cambiar_pass') {
    $password = mysqli_real_escape_string($conexion, $_POST['password'] ?? '');
    if (!$password) { echo json_encode(['success' => false, 'message' => 'Ingrese la nueva contraseña']); exit; }
    if (!isset($_SESSION['recuperar_id'])) {
        echo json_encode(['success' => false, 'message' => 'No hay solicitud activa']); exit;
    }
    $idv = mysqli_real_escape_string($conexion, $_SESSION['recuperar_id']);
    $tabla = $_SESSION['recuperar_tabla'] ?? 'veterinario';
    $id_field = $_SESSION['recuperar_id_field'] ?? 'Id_veterinario';
    mysqli_query($conexion, "UPDATE `$tabla` SET `password`='$password' WHERE `$id_field`='$idv'");
    unset($_SESSION['recuperar_codigo'], $_SESSION['recuperar_timestamp'], $_SESSION['recuperar_id'], $_SESSION['recuperar_tabla'], $_SESSION['recuperar_id_field']);
    echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente']);
    exit;
}

echo json_encode(['error' => 'Accion no valida']);
