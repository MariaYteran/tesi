<?php
include __DIR__ . '/../bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $id_mascota    = $_POST['id_mascota'] ?? '';
    $id_veterinario = $_POST['id_veterinario'] ?? null;
    $fecha         = $_POST['fecha'] ?? '';
    $hora          = $_POST['hora'] ?? null;
    $motivo        = $_POST['motivo'] ?? null;

    if (!$id_mascota || !$fecha) {
        echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios']);
        exit;
    }

    if ($fecha < date('Y-m-d')) {
        echo json_encode(['success' => false, 'message' => 'No se pueden agendar citas en fechas pasadas']);
        exit;
    }

    $fch = mysqli_real_escape_string($conexion, $fecha);
    $actCheck = mysqli_query($conexion, "SELECT id_actividad FROM actividad WHERE fecha='$fch' LIMIT 1");
    if (mysqli_num_rows($actCheck) > 0) {
        echo json_encode(['success' => false, 'message' => 'Esta fecha esta bloqueada por una actividad']);
        exit;
    }

    $vet = $id_veterinario ? "'" . mysqli_real_escape_string($conexion, $id_veterinario) . "'" : 'NULL';
    $hr  = $hora          ? "'" . mysqli_real_escape_string($conexion, $hora) . "'" : 'NULL';
    $mot = $motivo        ? "'" . mysqli_real_escape_string($conexion, $motivo) . "'" : 'NULL';
    $mid = mysqli_real_escape_string($conexion, $id_mascota);

    $q = "INSERT INTO citas (id_mascota, id_veterinario, fecha, hora, motivo, estado, RIF_clinica)
          VALUES ('$mid', $vet, '$fch', $hr, $mot, 'pendiente', '$RIF_clinica')";

    if (mysqli_query($conexion, $q)) {
        echo json_encode(['success' => true, 'message' => 'Cita agendada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . mysqli_error($conexion)]);
    }
    exit;
}

$total_propietarios  = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as t FROM propietario WHERE RIF_clinica='$RIF_clinica'"))['t'];
$total_recepcionistas = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as t FROM recepcionista WHERE RIF_clinica='$RIF_clinica'"))['t'];
$total_pacientes     = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as t FROM mascota WHERE RIF_clinica='$RIF_clinica'"))['t'];
$total_auxiliares    = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as t FROM `aux-vet` WHERE RIF_clinica='$RIF_clinica'"))['t'];

$vets = mysqli_query($conexion, "SELECT Id_veterinario, Nombres FROM veterinario WHERE RIF_clinica='$RIF_clinica'");
?>
<div class="animate-fadeIn">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-green-800">Hola bienvenido</h1>
            <p class="text-gray-600 mt-1">Sistema web al alcance de todos</p>
        </div>
        <div class="relative">
            <button id="btnNotif" onclick="abrirModales()" class="w-12 h-12 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 text-white shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </button>
            <div id="tooltipNotif" class="absolute top-full right-0 mt-2 w-64 p-3 bg-gray-800 text-white text-xs rounded-xl shadow-lg opacity-0 pointer-events-none transition-opacity duration-200 z-50">
                Aquí podrás visualizar tus citas de hoy y tu rendimiento semanal
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-5 py-4 flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
            </div>
            <div class="p-5 text-center">
                <p class="text-3xl font-bold text-green-800"><?php echo $total_propietarios; ?></p>
                <p class="text-sm text-gray-500 mt-1">Propietarios registrados</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-5 py-4 flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="p-5 text-center">
                <p class="text-3xl font-bold text-green-800"><?php echo $total_recepcionistas; ?></p>
                <p class="text-sm text-gray-500 mt-1">Recepcionistas registrados</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-5 py-4 flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <div class="p-5 text-center">
                <p class="text-3xl font-bold text-green-800"><?php echo $total_pacientes; ?></p>
                <p class="text-sm text-gray-500 mt-1">Pacientes registrados</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-5 py-4 flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
            <div class="p-5 text-center">
                <p class="text-3xl font-bold text-green-800"><?php echo $total_auxiliares; ?></p>
                <p class="text-sm text-gray-500 mt-1">Auxiliares registrados</p>
            </div>
        </div>
    </div>

    <h2 class="text-2xl font-bold text-green-800 mb-6 flex items-center gap-2">
        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        Herramientas inmediatas
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
            <div class="p-6 text-center">
                <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="font-bold text-green-800 text-lg mb-3">Agendar Citas</h3>
                <button onclick="document.getElementById('modalAgendar').classList.remove('hidden')" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-2 rounded-xl text-sm font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md hover:shadow-lg">
                    Ir a →
                </button>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
            <div class="p-6 text-center">
                <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="font-bold text-green-800 text-lg mb-3">Calendario de Citas</h3>
                <button onclick="document.getElementById('modalCalendario').classList.remove('hidden')" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-2 rounded-xl text-sm font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md hover:shadow-lg">
                    Ir a →
                </button>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
            <div class="p-6 text-center">
                <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-bold text-green-800 text-lg mb-3">Emergencias Médicas</h3>
                <button onclick="document.getElementById('modalEmergencia').classList.remove('hidden')" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-2 rounded-xl text-sm font-semibold hover:from-red-600 hover:to-red-700 transition-all shadow-md hover:shadow-lg">
                    Ir a →
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agendar Cita -->
<div id="modalAgendar" class="fixed inset-0 bg-black/20 z-50 hidden overflow-y-auto">
    <div class="min-h-full flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-96 flex flex-col max-h-[90vh] overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5 flex items-center justify-between shrink-0">
                <h3 class="text-xl font-bold text-white">Agendar Cita</h3>
                <button onclick="document.getElementById('modalAgendar').classList.add('hidden')" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="formAgendar" class="flex flex-col min-h-0 flex-1">
                <div class="px-6 pt-5 pb-2 shrink-0">
                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Cédula de la mascota</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 border-2 border-r-0 border-green-200 rounded-l-xl bg-green-50 text-green-700 font-bold text-sm">V-</span>
                            <input type="text" id="inputMascota" name="id_mascota" maxlength="11" class="w-full border-2 border-green-200 rounded-r-xl px-4 py-3 focus:outline-none focus:border-green-500" placeholder="12345678" required autocomplete="off">
                        </div>
                        <div id="suggestionsMascota" class="absolute top-full left-0 right-0 bg-white border border-green-200 rounded-xl shadow-lg z-50 hidden max-h-48 overflow-y-auto mt-1"></div>
                    </div>
                </div>
                <div class="px-6 pb-5 overflow-y-auto min-h-0 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Médico asignado</label>
                        <select name="id_veterinario" class="w-full border-2 border-green-200 rounded-xl px-4 py-3 focus:outline-none focus:border-green-500 bg-white">
                            <option value="">Seleccione un médico</option>
                            <?php while ($v = mysqli_fetch_assoc($vets)): ?>
                            <option value="<?php echo $v['Id_veterinario']; ?>"><?php echo htmlspecialchars($v['Nombres']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Fecha de la cita</label>
                        <input type="date" name="fecha" min="<?php echo date('Y-m-d'); ?>" class="w-full border-2 border-green-200 rounded-xl px-4 py-3 focus:outline-none focus:border-green-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Hora</label>
                        <input type="time" name="hora" class="w-full border-2 border-green-200 rounded-xl px-4 py-3 focus:outline-none focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Motivo</label>
                        <input type="text" name="motivo" class="w-full border-2 border-green-200 rounded-xl px-4 py-3 focus:outline-none focus:border-green-500" placeholder="Ej: Vacunación, control...">
                    </div>
                    <div id="msgAgendar" class="text-sm font-semibold hidden"></div>
                    <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3.5 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md mt-2">
                        Agendar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Emergencia Médica -->
<div id="modalEmergencia" class="fixed inset-0 bg-black/20 z-50 hidden overflow-y-auto">
    <div class="min-h-full flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-96 overflow-hidden">
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-5 flex items-center justify-between">
            <h3 class="text-xl font-bold text-white">Emergencia Médica</h3>
            <button onclick="document.getElementById('modalEmergencia').classList.add('hidden')" class="text-white/80 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="px-6 py-6">
            <form id="formEmergencia" class="space-y-4">
                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Cédula de la mascota</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-4 border-2 border-r-0 border-green-200 rounded-l-xl bg-green-50 text-green-700 font-bold text-sm">V-</span>
                        <input type="text" id="inputMascotaEmerg" maxlength="11" class="w-full border-2 border-green-200 rounded-r-xl px-4 py-3 focus:outline-none focus:border-green-500" placeholder="12345678" required autocomplete="off">
                    </div>
                    <div id="suggestionsMascotaEmerg" class="absolute top-full left-0 right-0 bg-white border border-green-200 rounded-xl shadow-lg z-50 hidden max-h-48 overflow-y-auto mt-1"></div>
                </div>
                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Cédula del veterinario</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-4 border-2 border-r-0 border-green-200 rounded-l-xl bg-green-50 text-green-700 font-bold text-sm">V-</span>
                        <input type="text" id="inputVetEmerg" maxlength="11" class="w-full border-2 border-green-200 rounded-r-xl px-4 py-3 focus:outline-none focus:border-green-500" placeholder="12345678" autocomplete="off">
                    </div>
                    <div id="suggestionsVetEmerg" class="absolute top-full left-0 right-0 bg-white border border-green-200 rounded-xl shadow-lg z-50 hidden max-h-48 overflow-y-auto mt-1"></div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Diagnóstico de emergencia</label>
                    <textarea id="inputDiagnosticoEmerg" class="w-full border-2 border-green-200 rounded-xl px-4 py-3 focus:outline-none focus:border-green-500 resize-none" rows="4" placeholder="Describa el diagnóstico" required></textarea>
                </div>
                <div id="msgEmergencia" class="text-sm font-semibold hidden"></div>
                <button type="submit" class="w-full bg-gradient-to-r from-red-500 to-red-600 text-white py-3.5 rounded-xl font-semibold hover:from-red-600 hover:to-red-700 transition-all shadow-md mt-2">
                    Guardar Emergencia
                </button>
            </form>
    </div>
    </div>
</div>
</div>

<!-- Modal Calendario -->
<div id="modalCalendario" class="fixed inset-0 bg-black/20 z-50 hidden overflow-y-auto">
    <div class="min-h-full flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-[896px] overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5 flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Calendario de Citas</h3>
            <button onclick="document.getElementById('modalCalendario').classList.add('hidden')" class="text-white/80 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="px-4 py-6">
            <div class="flex flex-row gap-6">
                <div class="w-[380px] flex-shrink-0">
                    <div class="flex flex-col items-center">
                        <div class="relative dropdown-container mb-2">
                            <span id="calYearDisplay" onclick="calToggleDropdown('calDropdownAños')" class="text-2xl font-bold text-green-700 cursor-pointer hover:text-green-600 transition-colors select-none"></span>
                            <div id="calDropdownAños" class="dropdown-menu absolute bg-white rounded-xl shadow-lg border border-green-100 p-4 z-50 grid grid-cols-5 gap-2 hidden" style="top:110%; left:50%; transform:translateX(-50%); min-width:260px;"></div>
                        </div>
                        <div class="relative dropdown-container mb-4">
                            <div class="flex items-center justify-center gap-4">
                                <svg onclick="calCambiarMes(-1)" class="w-5 h-5 text-green-600 cursor-pointer hover:text-green-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                </svg>
                                <span id="calMonthDisplay" onclick="calToggleDropdown('calDropdownMeses')" class="text-xl font-semibold text-green-800 cursor-pointer hover:text-green-700 transition-colors select-none"></span>
                                <svg onclick="calCambiarMes(1)" class="w-5 h-5 text-green-600 cursor-pointer hover:text-green-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                            <div id="calDropdownMeses" class="dropdown-menu absolute bg-white rounded-xl shadow-lg border border-green-100 p-4 z-50 grid grid-cols-3 gap-2 hidden" style="top:110%; left:50%; transform:translateX(-50%); min-width:280px;"></div>
                        </div>
                        <div id="calGrid" class="w-full"></div>
                    </div>
                </div>
                <div id="dayPanel" class="flex-1 border-l border-green-100 pl-6 hidden flex flex-col min-h-[300px]">
                    <div class="flex items-center justify-between mb-4">
                        <h4 id="dayPanelTitle" class="text-lg font-bold text-green-800"></h4>
                        <button onclick="cerrarDayPanel()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div id="dayCitas" class="space-y-2 max-h-60 overflow-y-auto mb-4"></div>
                    <form id="formActividad" class="space-y-3 border-t border-green-100 pt-4 mt-auto">
                        <h5 class="font-semibold text-green-700 text-sm">Actividad especial</h5>
                        <input type="hidden" name="fecha" id="actFecha">
                        <input type="text" name="titulo" placeholder="Título de la actividad" class="w-full border-2 border-green-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-green-500" required>
                        <textarea name="descripcion" placeholder="Descripción (opcional)" class="w-full border-2 border-green-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-green-500 resize-none" rows="2"></textarea>
                        <div id="msgActividad" class="text-sm font-semibold hidden"></div>
                        <button type="submit" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md">
                            Guardar actividad
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<!-- Modal Editar Cita -->
<div id="modalEditarCita" class="fixed inset-0 bg-black/20 z-50 hidden overflow-y-auto">
    <div class="min-h-full flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5 flex items-center justify-between">
            <h3 class="text-xl font-bold text-white">Editar Cita</h3>
            <button onclick="document.getElementById('modalEditarCita').classList.add('hidden')" class="text-white/80 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="px-6 py-5">
            <form id="formEditarCita" class="space-y-4">
                <input type="hidden" id="editIdCita" name="id_cita">
                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Cedula de la mascota</label>
                    <input type="text" id="inputMascotaEditar" name="id_mascota" class="w-full border-2 border-green-200 rounded-xl px-4 py-3 focus:outline-none focus:border-green-500" placeholder="Ingrese la cedula" required autocomplete="off">
                    <div id="suggestionsMascotaEditar" class="absolute top-full left-0 right-0 bg-white border border-green-200 rounded-xl shadow-lg z-50 hidden max-h-48 overflow-y-auto mt-1"></div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Medico asignado</label>
                    <select name="id_veterinario" id="inputVetEditar" class="w-full border-2 border-green-200 rounded-xl px-4 py-3 focus:outline-none focus:border-green-500 bg-white">
                        <option value="">Seleccione un medico</option>
                        <?php mysqli_data_seek($vets, 0); while ($v = mysqli_fetch_assoc($vets)): ?>
                        <option value="<?php echo $v['Id_veterinario']; ?>"><?php echo htmlspecialchars($v['Nombres']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Fecha de la cita</label>
                    <input type="date" name="fecha" id="inputFechaEditar" min="<?php echo date('Y-m-d'); ?>" class="w-full border-2 border-green-200 rounded-xl px-4 py-3 focus:outline-none focus:border-green-500" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Hora</label>
                    <input type="time" name="hora" id="inputHoraEditar" class="w-full border-2 border-green-200 rounded-xl px-4 py-3 focus:outline-none focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Motivo</label>
                    <input type="text" name="motivo" id="inputMotivoEditar" class="w-full border-2 border-green-200 rounded-xl px-4 py-3 focus:outline-none focus:border-green-500" placeholder="Ej: Vacunacion, control...">
                </div>
                <div id="msgEditarCita" class="text-sm font-semibold hidden"></div>
                <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3.5 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md mt-2">
                    Guardar Cambios
                </button>
            </form>
        </div>
    </div>
    </div>
</div>

<!-- Modal Confirmar Cancelacion -->
<div id="modalConfirmarCancelar" class="fixed inset-0 bg-black/20 z-50 hidden overflow-y-auto">
    <div class="min-h-full flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full overflow-hidden">
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-5 flex items-center justify-between">
            <h3 class="text-xl font-bold text-white">Cancelar Cita</h3>
            <button onclick="document.getElementById('modalConfirmarCancelar').classList.add('hidden')" class="text-white/80 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="px-6 py-6">
            <p class="text-gray-700 text-sm mb-6">Esta seguro de cancelar la cita de <strong id="cancelarNombreMascota"></strong>?</p>
            <input type="hidden" id="cancelarIdCita">
            <div class="flex gap-3">
                <button onclick="document.getElementById('modalConfirmarCancelar').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-300 transition-all">
                    No, volver
                </button>
                <button onclick="confirmarCancelacion()" class="flex-1 bg-gradient-to-r from-red-500 to-red-600 text-white py-3 rounded-xl font-semibold hover:from-red-600 hover:to-red-700 transition-all shadow-md">
                    Si, cancelar
                </button>
            </div>
            <div id="msgConfirmarCancelar" class="text-sm font-semibold hidden mt-3"></div>
        </div>
    </div>
    </div>
</div>

<!-- Overlay combinado para Rendimiento Semanal + Citas de Hoy -->
<div id="overlayModales" class="fixed inset-0 bg-black/20 z-50 hidden overflow-y-auto">
    <div class="min-h-full flex items-start justify-center p-4">
        <div class="grid grid-cols-[minmax(500px,2fr)_minmax(320px,1fr)] gap-6 items-start" onclick="event.stopPropagation()">
        <!-- Modal Rendimiento Semanal -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5 flex items-center justify-between">
                <h3 class="text-lg font-bold text-white">Rendimiento Semanal</h3>
                <button onclick="cerrarModales()" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-6 py-6">
                <div class="flex items-stretch gap-4">
                    <div class="flex-1 min-h-0">
                        <div class="flex h-56 gap-1 relative">
                            <div class="flex flex-col items-end justify-between h-full pr-2 pt-0.5 pb-5 text-xs text-gray-500 font-medium w-6 flex-shrink-0">
                                <span>20</span><span>18</span><span>16</span><span>14</span><span>12</span>
                                <span>10</span><span>8</span><span>6</span><span>4</span><span>2</span><span>0</span>
                            </div>
                            <div id="chartBars" class="flex-1 flex h-full gap-1.5"></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-6 mt-4 text-sm">
                    <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-green-700"></span> Perros</span>
                    <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-green-300"></span> Gatos</span>
                    <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-red-500"></span> Emergencias</span>
                </div>
            </div>
        </div>

        <!-- Modal Citas de Hoy -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5 flex items-center justify-between">
                <h3 class="text-lg font-bold text-white">Citas de Hoy</h3>
                <button onclick="cerrarModales()" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-6 py-6">
                <div id="listaCitasHoy" class="space-y-3 min-h-[100px]">
                    <div class="text-center text-gray-400 py-6">Cargando...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var _today = new Date();
var _todayStr = _today.getFullYear() + '-' + String(_today.getMonth() + 1).padStart(2, '0') + '-' + String(_today.getDate()).padStart(2, '0');

(function() {
    var btn = document.getElementById('btnNotif');
    var tooltip = document.getElementById('tooltipNotif');
    if (btn && tooltip) {
        btn.addEventListener('mouseenter', function() { tooltip.classList.remove('opacity-0', 'pointer-events-none'); tooltip.classList.add('opacity-100'); });
        btn.addEventListener('mouseleave', function() { tooltip.classList.remove('opacity-100'); tooltip.classList.add('opacity-0', 'pointer-events-none'); });
    }

    var formAgendar = document.getElementById('formAgendar');
    var msgEl = document.getElementById('msgAgendar');
    if (formAgendar) {
        formAgendar.addEventListener('submit', async function(e) {
            e.preventDefault();
            msgEl.className = 'text-sm font-semibold';
            msgEl.textContent = 'Verificando...';
            var fd = new FormData(this);
            fd.set('id_mascota', 'V-' + document.getElementById('inputMascota').value.replace(/\D/g, ''));
            var fechaVal = fd.get('fecha');
            if (fechaVal < _todayStr) {
                msgEl.textContent = 'No se pueden agendar citas en fechas pasadas';
                msgEl.className = 'text-sm font-semibold text-red-600';
                return;
            }
            var horaVal = fd.get('hora');
            if (fechaVal === _todayStr && horaVal) {
                var p = horaVal.split(':');
                var selMin = parseInt(p[0]) * 60 + parseInt(p[1]);
                var now = new Date();
                var nowMin = now.getHours() * 60 + now.getMinutes();
                if (selMin <= nowMin) {
                    msgEl.textContent = 'La hora debe ser posterior a la hora actual';
                    msgEl.className = 'text-sm font-semibold text-red-600';
                    return;
                }
            }
            try {
                var cr = await fetch('/dist/content/inicio_data.php?action=verificar_fecha&fecha=' + encodeURIComponent(fechaVal));
                var cd = await cr.json();
                if (cd.blocked) {
                    msgEl.textContent = cd.mensaje;
                    msgEl.className = 'text-sm font-semibold text-red-600';
                    return;
                }
            } catch(e) {
                msgEl.textContent = 'Error al verificar fecha';
                msgEl.className = 'text-sm font-semibold text-red-600';
                return;
            }
            msgEl.textContent = 'Guardando...';
            try {
                var resp = await fetch('/dist/content/inicio.php', { method: 'POST', body: fd });
                var data = await resp.json();
                msgEl.textContent = data.message;
                msgEl.className = 'text-sm font-semibold ' + (data.success ? 'text-green-600' : 'text-red-600');
                if (data.success) {
                    var fechaCita = fd.get('fecha');
                    this.reset();
                    setTimeout(function() { document.getElementById('modalAgendar').classList.add('hidden'); }, 1500);
                    if (typeof window.refrescarCalendario === 'function') {
                        window.refrescarCalendario('cita', fechaCita);
                    }
                }
            } catch(e) {
                msgEl.textContent = 'Error de conexión';
                msgEl.className = 'text-sm font-semibold text-red-600';
            }
        });
    }

    /* Time restriction: when today is selected, limit time to current time */
    function actualizarMinHora(fechaInput, horaInput) {
        if (!fechaInput || !horaInput) return;
        fechaInput.addEventListener('change', function() {
            if (this.value === _todayStr) {
                var n = new Date();
                var m = n.getMinutes();
                m = Math.ceil(m / 5) * 5;
                if (m >= 60) { m = 0; n.setHours(n.getHours() + 1); }
                horaInput.min = String(n.getHours()).padStart(2, '0') + ':' + String(m).padStart(2, '0');
            } else {
                horaInput.removeAttribute('min');
            }
        });
    }
    actualizarMinHora(
        document.querySelector('#formAgendar [name="fecha"]'),
        document.querySelector('#formAgendar [name="hora"]')
    );
    actualizarMinHora(
        document.getElementById('inputFechaEditar'),
        document.getElementById('inputHoraEditar')
    );

        var formAct = document.getElementById('formActividad');
        var msgAct = document.getElementById('msgActividad');
        if (formAct) {
            formAct.addEventListener('submit', async function(e) {
                e.preventDefault();
                msgAct.className = 'text-sm font-semibold';
                msgAct.textContent = 'Guardando...';
                var fd = new FormData(this);
                fd.append('action', 'actividad');
                try {
                    var resp = await fetch('/dist/content/inicio_data.php', { method: 'POST', body: fd });
                    var data = await resp.json();
                    msgAct.textContent = data.message;
                    msgAct.className = 'text-sm font-semibold ' + (data.success ? 'text-green-600' : 'text-red-600');
                    if (data.success) {
                        this.reset();
                        document.getElementById('actFecha').value = fd.get('fecha');
                        if (typeof window.refrescarCalendario === 'function') {
                            window.refrescarCalendario('actividad', fd.get('fecha'));
                        }
                    }
                } catch(e) {
                    msgAct.textContent = 'Error de conexión';
                    msgAct.className = 'text-sm font-semibold text-red-600';
                }
            });
        }

    var overlay = document.getElementById('overlayModales');
    if (overlay) {
        overlay.addEventListener('click', function() { this.classList.add('hidden'); });
    }

    /* Emergencia */
    var formEmerg = document.getElementById('formEmergencia');
    var msgEmerg = document.getElementById('msgEmergencia');
    if (formEmerg) {
        formEmerg.addEventListener('submit', async function(e) {
            e.preventDefault();
            msgEmerg.className = 'text-sm font-semibold';
            msgEmerg.textContent = 'Guardando...';
            var fd = new FormData();
            fd.append('id_mascota', 'V-' + document.getElementById('inputMascotaEmerg').value.replace(/\D/g, ''));
            fd.append('id_veterinario', 'V-' + document.getElementById('inputVetEmerg').value.replace(/\D/g, ''));
            fd.append('diagnostico', document.getElementById('inputDiagnosticoEmerg').value);
            fd.append('action', 'emergencia');
            try {
                var resp = await fetch('/dist/content/inicio_data.php', { method: 'POST', body: fd });
                var data = await resp.json();
                msgEmerg.textContent = data.message;
                msgEmerg.className = 'text-sm font-semibold ' + (data.success ? 'text-green-600' : 'text-red-600');
                if (data.success) {
                    formEmerg.reset();
                    if (typeof window.refrescarCalendario === 'function') {
                        var hoy = new Date();
                        var mes = String(hoy.getMonth() + 1).padStart(2, '0');
                        var dia = String(hoy.getDate()).padStart(2, '0');
                        var fechaStr = hoy.getFullYear() + '-' + mes + '-' + dia;
                        window.refrescarCalendario('cita', fechaStr);
                    }
                    setTimeout(function() { document.getElementById('modalEmergencia').classList.add('hidden'); }, 1500);
                }
            } catch(e) {
                msgEmerg.textContent = 'Error de conexión';
                msgEmerg.className = 'text-sm font-semibold text-red-600';
            }
        });
    }

    function hacerTypeahead(inputId, sugId, urlBase, selectFn, idField, nameField, extraField) {
        idField = idField || 'id_mascota';
        nameField = nameField || 'nombre';
        extraField = extraField || 'especie';
        var input = document.getElementById(inputId);
        var sug = document.getElementById(sugId);
        var timer = null;
        if (!input || !sug) return;
        input.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            clearTimeout(timer);
            var q = this.value.trim();
            if (q.length < 1) { sug.classList.add('hidden'); return; }
            timer = setTimeout(function() {
                fetch(urlBase + encodeURIComponent(q))
                    .then(function(r) { return r.json(); })
                    .then(function(datos) {
                        if (datos.length === 0) { sug.classList.add('hidden'); return; }
                        var html = '';
                        for (var i = 0; i < datos.length; i++) {
                            idSafe = (datos[i][idField] || '').replace(/'/g, "\\'");
                            nameSafe = (datos[i][nameField] || '').replace(/'/g, "\\'");
                            html += '<div class="px-4 py-2.5 cursor-pointer hover:bg-green-50 border-b border-green-100 last:border-b-0 text-sm" onclick="' + selectFn + '(\'' + idSafe + '\',\'' + nameSafe + '\')">';
                            html += '<span class="font-semibold text-green-800">' + (datos[i][idField] || '') + '</span>';
                            html += ' <span class="text-gray-600">' + (datos[i][nameField] || '') + '</span>';
                            if (datos[i][extraField]) html += ' <span class="text-xs text-gray-400">(' + datos[i][extraField] + ')</span>';
                            html += '</div>';
                        }
                        sug.innerHTML = html;
                        sug.classList.remove('hidden');
                    })
                    .catch(function() { sug.classList.add('hidden'); });
            }, 200);
        });
        input.addEventListener('blur', function() { setTimeout(function() { sug.classList.add('hidden'); }, 200); });
        input.addEventListener('focus', function() { if (this.value.trim().length >= 1) { var evt = new Event('input'); this.dispatchEvent(evt); } });
    }

    hacerTypeahead('inputMascotaEmerg', 'suggestionsMascotaEmerg', '/dist/content/inicio_data.php?action=buscar_mascotas&q=', 'seleccionarMascotaEmerg', 'id_mascota', 'nombre', 'especie');
    hacerTypeahead('inputVetEmerg', 'suggestionsVetEmerg', '/dist/content/inicio_data.php?action=buscar_veterinario&q=', 'seleccionarVetEmerg', 'Id_veterinario', 'Nombres', '');

    /* Typeahead mascota (agendar) */
    var inputMascota = document.getElementById('inputMascota');
    var sugMascota = document.getElementById('suggestionsMascota');
    var timerMascota = null;
    if (inputMascota && sugMascota) {
        inputMascota.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            clearTimeout(timerMascota);
            var q = this.value.trim();
            if (q.length < 1) { sugMascota.classList.add('hidden'); return; }
            timerMascota = setTimeout(function() {
                fetch('/dist/content/inicio_data.php?action=buscar_mascotas&q=' + encodeURIComponent(q))
                    .then(function(r) { return r.json(); })
                    .then(function(datos) {
                        if (datos.length === 0) { sugMascota.classList.add('hidden'); return; }
                        var html = '';
                        for (var i = 0; i < datos.length; i++) {
                            html += '<div class="px-4 py-2.5 cursor-pointer hover:bg-green-50 border-b border-green-100 last:border-b-0 text-sm" onclick="seleccionarMascota(\'' + datos[i].id_mascota + '\')">';
                            html += '<span class="font-semibold text-green-800">' + datos[i].id_mascota + '</span>';
                            html += ' <span class="text-gray-600">' + datos[i].nombre + '</span>';
                            html += ' <span class="text-xs text-gray-400">(' + datos[i].especie + ')</span>';
                            html += '</div>';
                        }
                        sugMascota.innerHTML = html;
                        sugMascota.classList.remove('hidden');
                    })
                    .catch(function() { sugMascota.classList.add('hidden'); });
            }, 200);
        });
        inputMascota.addEventListener('blur', function() {
            setTimeout(function() { sugMascota.classList.add('hidden'); }, 200);
        });
        inputMascota.addEventListener('focus', function() {
            if (this.value.trim().length >= 1) {
                var evt = new Event('input');
                this.dispatchEvent(evt);
            }
        });
    }

    /* --- Edit cita form --- */
    var formEditar = document.getElementById('formEditarCita');
    var msgEditar = document.getElementById('msgEditarCita');
    if (formEditar) {
        formEditar.addEventListener('submit', async function(e) {
            e.preventDefault();
            msgEditar.className = 'text-sm font-semibold';
            msgEditar.textContent = 'Verificando...';
            var fd = new FormData(this);
            fd.append('action', 'actualizar_cita');
            var fechaVal = fd.get('fecha');
            if (fechaVal < _todayStr) {
                msgEditar.textContent = 'No se pueden agendar citas en fechas pasadas';
                msgEditar.className = 'text-sm font-semibold text-red-600';
                return;
            }
            var horaVal = fd.get('hora');
            if (fechaVal === _todayStr && horaVal) {
                var p = horaVal.split(':');
                var selMin = parseInt(p[0]) * 60 + parseInt(p[1]);
                var now = new Date();
                var nowMin = now.getHours() * 60 + now.getMinutes();
                if (selMin <= nowMin) {
                    msgEditar.textContent = 'La hora debe ser posterior a la hora actual';
                    msgEditar.className = 'text-sm font-semibold text-red-600';
                    return;
                }
            }
            try {
                var cr = await fetch('/dist/content/inicio_data.php?action=verificar_fecha&fecha=' + encodeURIComponent(fechaVal));
                var cd = await cr.json();
                if (cd.blocked) {
                    msgEditar.textContent = cd.mensaje;
                    msgEditar.className = 'text-sm font-semibold text-red-600';
                    return;
                }
            } catch(e) {
                msgEditar.textContent = 'Error al verificar fecha';
                msgEditar.className = 'text-sm font-semibold text-red-600';
                return;
            }
            msgEditar.textContent = 'Guardando...';
            try {
                var resp = await fetch('/dist/content/inicio_data.php', { method: 'POST', body: fd });
                var data = await resp.json();
                msgEditar.textContent = data.message;
                msgEditar.className = 'text-sm font-semibold ' + (data.success ? 'text-green-600' : 'text-red-600');
                if (data.success) {
                    setTimeout(function() {
                        document.getElementById('modalEditarCita').classList.add('hidden');
                        var fechaEl = document.querySelector('#calGrid [data-fecha]');
                        if (fechaEl) seleccionarDia(fechaEl.dataset.fecha, null);
                    }, 1500);
                }
            } catch(e) {
                msgEditar.textContent = 'Error de conexion';
                msgEditar.className = 'text-sm font-semibold text-red-600';
            }
        });
    }

    /* Typeahead mascota (editar) */
    var inputMascotaEditar = document.getElementById('inputMascotaEditar');
    var sugMascotaEditar = document.getElementById('suggestionsMascotaEditar');
    var timerMascotaEditar = null;
    if (inputMascotaEditar && sugMascotaEditar) {
        inputMascotaEditar.addEventListener('input', function() {
            clearTimeout(timerMascotaEditar);
            var q = this.value.trim();
            if (q.length < 1) { sugMascotaEditar.classList.add('hidden'); return; }
            timerMascotaEditar = setTimeout(function() {
                fetch('/dist/content/inicio_data.php?action=buscar_mascotas&q=' + encodeURIComponent(q))
                    .then(function(r) { return r.json(); })
                    .then(function(datos) {
                        if (datos.length === 0) { sugMascotaEditar.classList.add('hidden'); return; }
                        var html = '';
                        for (var i = 0; i < datos.length; i++) {
                            html += '<div class="px-4 py-2.5 cursor-pointer hover:bg-green-50 border-b border-green-100 last:border-b-0 text-sm" onclick="seleccionarMascotaEditar(\'' + datos[i].id_mascota + '\')">';
                            html += '<span class="font-semibold text-green-800">' + datos[i].id_mascota + '</span>';
                            html += ' <span class="text-gray-600">' + datos[i].nombre + '</span>';
                            html += ' <span class="text-xs text-gray-400">(' + datos[i].especie + ')</span>';
                            html += '</div>';
                        }
                        sugMascotaEditar.innerHTML = html;
                        sugMascotaEditar.classList.remove('hidden');
                    })
                    .catch(function() { sugMascotaEditar.classList.add('hidden'); });
            }, 200);
        });
        inputMascotaEditar.addEventListener('blur', function() {
            setTimeout(function() { sugMascotaEditar.classList.add('hidden'); }, 200);
        });
        inputMascotaEditar.addEventListener('focus', function() {
            if (this.value.trim().length >= 1) {
                var evt = new Event('input');
                this.dispatchEvent(evt);
            }
        });
    }
})();

window.seleccionarMascota = function(id) {
    var inp = document.getElementById('inputMascota');
    var sug = document.getElementById('suggestionsMascota');
    if (inp) inp.value = id.replace(/\D/g, '');
    if (sug) sug.classList.add('hidden');
};

window.seleccionarMascotaEmerg = function(id) {
    document.getElementById('inputMascotaEmerg').value = id.replace(/\D/g, '');
    var sug = document.getElementById('suggestionsMascotaEmerg');
    if (sug) sug.classList.add('hidden');
};

window.seleccionarVetEmerg = function(id, nombre) {
    document.getElementById('inputVetEmerg').value = id.replace(/\D/g, '');
    var sug = document.getElementById('suggestionsVetEmerg');
    if (sug) sug.classList.add('hidden');
};

window.seleccionarMascotaEditar = function(id) {
    var inp = document.getElementById('inputMascotaEditar');
    var sug = document.getElementById('suggestionsMascotaEditar');
    if (inp) inp.value = id;
    if (sug) sug.classList.add('hidden');
};

window.editarCita = function(idc) {
    fetch('/dist/content/inicio_data.php?action=cargar_cita&id=' + idc)
        .then(function(r) { return r.json(); })
        .then(function(c) {
            if (!c || !c.id_cita) { alert('Error al cargar la cita'); return; }
            if (c.fecha < _todayStr) {
                alert('No se puede editar una cita pasada');
                return;
            }
            document.getElementById('editIdCita').value = c.id_cita;
            document.getElementById('inputMascotaEditar').value = c.id_mascota;
            document.getElementById('inputVetEditar').value = c.id_veterinario || '';
            document.getElementById('inputFechaEditar').value = c.fecha;
            document.getElementById('inputHoraEditar').value = c.hora ? c.hora.substring(0, 5) : '';
            var horaEdit = document.getElementById('inputHoraEditar');
            if (c.fecha === _todayStr && horaEdit) {
                var now = new Date();
                var m = now.getMinutes();
                m = Math.ceil(m / 5) * 5;
                if (m >= 60) { m = 0; now.setHours(now.getHours() + 1); }
                horaEdit.min = String(now.getHours()).padStart(2, '0') + ':' + String(m).padStart(2, '0');
            }
            document.getElementById('inputMotivoEditar').value = c.motivo || '';
            document.getElementById('msgEditarCita').className = 'text-sm font-semibold hidden';
            document.getElementById('modalEditarCita').classList.remove('hidden');
        })
        .catch(function() { alert('Error de conexion al cargar la cita'); });
};

window.cancelarCita = function(idc, nombre, fecha) {
    if (fecha && fecha < _todayStr) {
        alert('No se puede cancelar una cita pasada');
        return;
    }
    document.getElementById('cancelarIdCita').value = idc;
    document.getElementById('cancelarNombreMascota').textContent = nombre;
    document.getElementById('msgConfirmarCancelar').className = 'text-sm font-semibold hidden';
    document.getElementById('modalConfirmarCancelar').classList.remove('hidden');
};

window.confirmarCancelacion = async function() {
    var idc = document.getElementById('cancelarIdCita').value;
    if (!idc) return;
    var msgEl = document.getElementById('msgConfirmarCancelar');
    var btn = document.querySelector('#modalConfirmarCancelar button[onclick="confirmarCancelacion()"]');
    msgEl.className = 'text-sm font-semibold';
    msgEl.textContent = 'Cancelando...';
    if (btn) btn.disabled = true;
    var fd = new FormData();
    fd.append('action', 'eliminar_cita');
    fd.append('id_cita', idc);
    try {
        var resp = await fetch('/dist/content/inicio_data.php', { method: 'POST', body: fd });
        var data = await resp.json();
        msgEl.textContent = data.message;
        msgEl.className = 'text-sm font-semibold ' + (data.success ? 'text-green-600' : 'text-red-600');
        if (data.success) {
            setTimeout(function() {
                document.getElementById('modalConfirmarCancelar').classList.add('hidden');
                // Refresh day panel if a day is selected
                var fechaEl = document.querySelector('#calGrid [data-fecha]');
                if (fechaEl) seleccionarDia(fechaEl.dataset.fecha, null);
                // Refresh calendar grid via exposed function
                if (typeof window.fetchMesData === 'function') window.fetchMesData();
            }, 1500);
        }
    } catch(e) {
        msgEl.textContent = 'Error de conexion';
        msgEl.className = 'text-sm font-semibold text-red-600';
    }
    if (btn) { setTimeout(function() { btn.disabled = false; }, 2000); }
};

window.abrirModales = async function() {
    var overlay = document.getElementById('overlayModales');
    overlay.classList.remove('hidden');

    try {
        var [semanal, hoy] = await Promise.all([
            fetch('/dist/content/inicio_data.php?action=semanal').then(function(r) { return r.json(); }),
            fetch('/dist/content/inicio_data.php?action=hoy').then(function(r) { return r.json(); })
        ]);

        renderChart(semanal);
        renderCitasHoy(hoy);
    } catch(e) {
        console.error('Error cargando datos', e);
    }
};

window.cerrarModales = function() {
    document.getElementById('overlayModales').classList.add('hidden');
};

function renderChart(datos) {
    var container = document.getElementById('chartBars');
    if (!container) return;
    var CHART_H = 224;
    var labels = ['L','M','M','J','V','S','D'];
    var html = '';

    for (var i = 0; i < datos.length; i++) {
        var d = datos[i];
        var ph = (Math.min(d.perros, 20) / 20) * CHART_H;
        var gh = (Math.min(d.gatos, 20) / 20) * CHART_H;
        var eh = (Math.min(d.emergencias, 20) / 20) * CHART_H;
        var maxChild = Math.max(ph, gh, eh, 1);

        html += '<div class="flex-1 flex flex-col items-center h-full">';
        html += '<div class="flex-1"></div>';
        html += '<div class="w-full flex flex-row items-end justify-center gap-px" style="height:' + maxChild + 'px">';
        html += '<div style="height:' + ph + 'px" class="flex-1 bg-green-700 rounded-t' + (d.perros > 0 ? '' : ' opacity-0') + '"></div>';
        html += '<div style="height:' + gh + 'px" class="flex-1 bg-green-300 rounded-t' + (d.gatos > 0 ? '' : ' opacity-0') + '"></div>';
        html += '<div style="height:' + eh + 'px" class="flex-1 bg-red-500 rounded-t' + (d.emergencias > 0 ? '' : ' opacity-0') + '"></div>';
        html += '</div>';
        html += '<span class="text-xs text-gray-500 font-medium pt-0.5">' + labels[i] + '</span>';
        html += '</div>';
    }

    container.innerHTML = html;
}

function renderCitasHoy(citas) {
    var container = document.getElementById('listaCitasHoy');
    if (!container) return;

    if (citas.length === 0) {
        container.innerHTML = '<div class="text-center text-gray-400 py-6">No hay citas programadas para hoy</div>';
        return;
    }

    var html = '';
    for (var i = 0; i < citas.length; i++) {
        var c = citas[i];
        var icono = c.especie === 'Perro' ? '🐕' : '🐈';
        html += '<div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-green-100">';
        html += '<span class="text-lg">' + icono + '</span>';
        html += '<div class="flex-1">';
        html += '<p class="font-semibold text-green-800 text-sm">' + c.nombre + '</p>';
        html += '<p class="text-xs text-gray-500">' + c.motivo + '</p>';
        html += '</div>';
        html += '<span class="text-sm font-medium text-green-700">' + c.hora + '</span>';
        html += '</div>';
    }

    container.innerHTML = html;
}

/* ====== Calendar JS ====== */
(function() {
    var meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    var diasCabecera = ['Lu','Ma','Mi','Ju','Vi','Sa','Do'];
    var hoy = new Date();
    var mes = hoy.getMonth();
    var año = hoy.getFullYear();
    var datosMes = { citas: [], actividades: [] };

    window.refrescarCalendario = function(tipo, fechaStr) {
        var partes = fechaStr.split('-');
        if (partes.length < 3) return;
        var dia = parseInt(partes[2], 10);
        if (tipo === 'cita' && datosMes.citas.indexOf(dia) === -1) {
            datosMes.citas.push(dia);
        }
        if (tipo === 'actividad' && datosMes.actividades.indexOf(dia) === -1) {
            datosMes.actividades.push(dia);
        }
        generarCalendario();
    };

    function fetchMesData() {
        var url = '/dist/content/inicio_data.php?action=mes&mes=' + (mes + 1) + '&año=' + año;
        fetch(url)
            .then(function(r) { return r.json(); })
            .then(function(d) {
                datosMes = d;
                generarCalendario();
            })
            .catch(function() { datosMes = { citas: [], actividades: [] }; });
    }
    window.fetchMesData = fetchMesData;

    function generarCalendario() {
        var yearSpan = document.getElementById('calYearDisplay');
        var monthSpan = document.getElementById('calMonthDisplay');
        if (!yearSpan || !monthSpan) return;
        yearSpan.textContent = año;
        monthSpan.textContent = meses[mes];
        var grid = document.getElementById('calGrid');
        if (!grid) return;
        var primerDia = new Date(año, mes, 1);
        var diasEnMes = new Date(año, mes + 1, 0).getDate();
        var offset = (primerDia.getDay() + 6) % 7;
        var fechaBase = año + '-' + String(mes + 1).padStart(2, '0') + '-';
        var html = '<div class="grid grid-cols-7 gap-1">';
        for (var i = 0; i < diasCabecera.length; i++) {
            html += '<div class="text-center text-xs font-semibold text-green-600 py-2">' + diasCabecera[i] + '</div>';
        }
        for (var i = 0; i < offset; i++) {
            html += '<div class="text-center"></div>';
        }
        for (var d = 1; d <= diasEnMes; d++) {
            var fechaStr = fechaBase + String(d).padStart(2, '0');
            var esHoy = (d === hoy.getDate() && mes === hoy.getMonth() && año === hoy.getFullYear());
            var tieneCitas = datosMes.citas.indexOf(d) !== -1;
            var tieneActividades = datosMes.actividades.indexOf(d) !== -1;
            var cls = 'text-center p-2 rounded-xl text-sm font-medium transition-colors cursor-pointer select-none relative';

            if (esHoy) {
                cls += ' bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-sm';
            } else if (tieneCitas && tieneActividades) {
                cls += ' bg-green-100 text-green-900 border border-green-400';
            } else if (tieneCitas) {
                cls += ' bg-green-100 text-green-900 border border-green-400';
            } else if (tieneActividades) {
                cls += ' bg-red-100 text-red-900 border border-red-400';
            } else {
                cls += ' text-green-800 hover:bg-green-50';
            }

            html += '<div class="' + cls + '" onclick="seleccionarDia(\'' + fechaStr + '\', this)" data-fecha="' + fechaStr + '">';
            html += '' + d;

            if (!esHoy) {
                if (tieneCitas) {
                    html += '<div class="w-1.5 h-1.5 rounded-full bg-green-500 mx-auto mt-0.5"></div>';
                } else if (tieneActividades) {
                    html += '<div class="w-1.5 h-1.5 rounded-full bg-red-500 mx-auto mt-0.5"></div>';
                }
            }

            html += '</div>';
        }
        html += '</div>';
        grid.innerHTML = html;
    }

    window.seleccionarDia = function(fecha, el) {
        var panel = document.getElementById('dayPanel');
        var title = document.getElementById('dayPanelTitle');
        if (!panel || !title) return;

        var partes = fecha.split('-');
        var fechaObj = new Date(parseInt(partes[0]), parseInt(partes[1]) - 1, parseInt(partes[2]));
        var opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        title.textContent = fechaObj.toLocaleDateString('es-ES', opciones);

        document.getElementById('actFecha').value = fecha;

        var citasContainer = document.getElementById('dayCitas');
        citasContainer.innerHTML = '<div class="text-center text-gray-400 py-4 text-sm">Cargando...</div>';
        panel.classList.remove('hidden');

        var allDays = document.querySelectorAll('#calGrid [data-fecha]');
        for (var i = 0; i < allDays.length; i++) {
            allDays[i].classList.remove('ring-2', 'ring-green-500');
        }
        if (el) el.classList.add('ring-2', 'ring-green-500');

        fetch('/dist/content/inicio_data.php?action=dia&fecha=' + fecha)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var html = '';

                if (data.citas && data.citas.length > 0) {
                    html += '<h5 class="text-sm font-bold text-green-700 mb-2">Citas</h5>';
                    var esPasado = fecha < _todayStr;
                    for (var i = 0; i < data.citas.length; i++) {
                        var c = data.citas[i];
                        var icono = c.especie === 'Perro' ? '🐕' : '🐈';
                        html += '<div class="flex items-center gap-2 p-2 bg-gray-50 rounded-xl border border-green-100">';
                        html += '<span>' + icono + '</span>';
                        html += '<div class="flex-1">';
                        html += '<p class="font-semibold text-green-800 text-xs">' + c.nombre + '</p>';
                        html += '<p class="text-xs text-gray-500">' + (c.motivo || '') + '</p>';
                        html += '</div>';
                        html += '<span class="text-xs font-medium text-green-700">' + c.hora + '</span>';
                        if (!esPasado) {
                        html += '<button onclick="editarCita(' + c.id_cita + ')" class="text-blue-600 hover:text-blue-800 transition-colors" title="Editar">' +
                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>' +
                            '</button>';
                        html += '<button onclick="cancelarCita(' + c.id_cita + ',\'' + c.nombre.replace(/'/g, "\\'") + '\',\'' + fecha + '\')" class="text-red-500 hover:text-red-700 transition-colors" title="Cancelar">' +
                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>' +
                            '</button>';
                        }
                        html += '</div>';
                    }
                } else {
                    html += '<p class="text-sm text-gray-400 py-2">No hay citas para este día</p>';
                }

                if (data.actividades && data.actividades.length > 0) {
                    html += '<h5 class="text-sm font-bold text-red-600 mt-3 mb-2">Actividades</h5>';
                    for (var i = 0; i < data.actividades.length; i++) {
                        var a = data.actividades[i];
                        html += '<div class="p-2 bg-red-50 rounded-xl border border-red-100">';
                        html += '<p class="font-semibold text-red-800 text-xs">' + a.titulo + '</p>';
                        if (a.descripcion) html += '<p class="text-xs text-gray-500 mt-0.5">' + a.descripcion + '</p>';
                        html += '</div>';
                    }
                }

                citasContainer.innerHTML = html || '<p class="text-sm text-gray-400 py-2">No hay eventos para este día</p>';
            })
            .catch(function() {
                citasContainer.innerHTML = '<p class="text-sm text-red-400 py-2">Error al cargar datos</p>';
            });
    };

    window.cerrarDayPanel = function() {
        document.getElementById('dayPanel').classList.add('hidden');
        var allDays = document.querySelectorAll('#calGrid [data-fecha]');
        for (var i = 0; i < allDays.length; i++) {
            allDays[i].classList.remove('ring-2', 'ring-green-500');
        }
    };

    window.calCambiarMes = function(delta) {
        mes += delta;
        if (mes < 0) { mes = 11; año--; }
        if (mes > 11) { mes = 0; año++; }
        fetchMesData();
    };

    window.calToggleDropdown = function(id) {
        var modals = document.querySelectorAll('#modalCalendario .dropdown-menu');
        for (var i = 0; i < modals.length; i++) {
            if (modals[i].id !== id) modals[i].classList.add('hidden');
        }
        var el = document.getElementById(id);
        if (!el) return;
        el.classList.toggle('hidden');
        if (id === 'calDropdownAños') renderAños();
        if (id === 'calDropdownMeses') renderMeses();
    };

    function renderAños() {
        var cont = document.getElementById('calDropdownAños');
        if (!cont) return;
        var html = '';
        for (var y = año - 7; y <= año + 4; y++) {
            var esActual = (y === año);
            var cls = esActual ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white' : 'text-green-700 hover:bg-green-50';
            html += '<span onclick="calSeleccionarAño(' + y + ')" class="text-center px-3 py-2 rounded-lg text-sm font-medium cursor-pointer transition-colors ' + cls + '">' + y + '</span>';
        }
        cont.innerHTML = html;
    }

    function renderMeses() {
        var cont = document.getElementById('calDropdownMeses');
        if (!cont) return;
        var html = '';
        for (var i = 0; i < meses.length; i++) {
            var esActual = (i === mes);
            var cls = esActual ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white' : 'text-green-700 hover:bg-green-50';
            html += '<span onclick="calSeleccionarMes(' + i + ')" class="text-center px-3 py-2 rounded-lg text-sm font-medium cursor-pointer transition-colors ' + cls + '">' + meses[i] + '</span>';
        }
        cont.innerHTML = html;
    }

    window.calSeleccionarAño = function(y) {
        año = y;
        var dd = document.getElementById('calDropdownAños');
        if (dd) dd.classList.add('hidden');
        fetchMesData();
    };

    window.calSeleccionarMes = function(m) {
        mes = m;
        var dd = document.getElementById('calDropdownMeses');
        if (dd) dd.classList.add('hidden');
        fetchMesData();
    };

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#modalCalendario .dropdown-container')) {
            var dds = document.querySelectorAll('#modalCalendario .dropdown-menu');
            for (var i = 0; i < dds.length; i++) dds[i].classList.add('hidden');
        }
    });

    var mc = document.getElementById('modalCalendario');
    if (mc) {
        var obs = new MutationObserver(function() {
            if (!mc.classList.contains('hidden')) {
                cerrarDayPanel();
                fetchMesData();
            }
        });
        obs.observe(mc, { attributes: true, attributeFilter: ['class'] });
    }
})();
</script>
