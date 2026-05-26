<?php
session_start();
include '../bd.php';
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_mascota = $_POST['cedula_prefix'] . mysqli_real_escape_string($conexion, $_POST['cedula']);
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $edad = mysqli_real_escape_string($conexion, trim($_POST['edad_valor'] . ' ' . $_POST['edad_unidad']));
    $sexo = mysqli_real_escape_string($conexion, $_POST['sexo']);
    $especie = mysqli_real_escape_string($conexion, $_POST['especie']);
    $raza = mysqli_real_escape_string($conexion, $_POST['raza']);
    $peso = mysqli_real_escape_string($conexion, trim($_POST['peso_valor'] . ' ' . $_POST['peso_unidad']));
    $id_propietario = mysqli_real_escape_string($conexion, $_POST['id_propietario']);
    $editing_id = mysqli_real_escape_string($conexion, $_POST['editing_id'] ?? '');

    if (empty($id_mascota) || empty($nombre) || empty($edad) || empty($sexo) || empty($especie) || empty($raza) || empty($peso) || empty($id_propietario)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (!empty($editing_id)) {
        try {
            $verificar = mysqli_query($conexion, "SELECT * FROM mascota WHERE id_mascota='$id_mascota' AND id_mascota != '$editing_id' AND RIF_clinica='$RIF_clinica'");
            if (mysqli_num_rows($verificar) > 0) {
                $error = "El ID de mascota ya existe.";
            } else {
                $update = mysqli_query($conexion, "UPDATE mascota SET id_mascota='$id_mascota', nombre='$nombre', edad='$edad', sexo='$sexo', especie='$especie', raza='$raza', peso='$peso', id_propietario='$id_propietario' WHERE id_mascota='$editing_id' AND RIF_clinica='$RIF_clinica'");
                if ($update) {
                    $success = "Paciente actualizado exitosamente.";
                } else {
                    $error = "Error al actualizar: " . mysqli_error($conexion);
                }
            }
        } catch (mysqli_sql_exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        try {
            $verificar = mysqli_query($conexion, "SELECT * FROM mascota WHERE id_mascota='$id_mascota' AND RIF_clinica='$RIF_clinica'");
            if (mysqli_num_rows($verificar) > 0) {
                $error = "El ID ya está registrado.";
            } else {
                $insertar = mysqli_query($conexion, "INSERT INTO mascota (id_mascota, nombre, edad, sexo, especie, raza, peso, id_propietario, RIF_clinica) VALUES ('$id_mascota', '$nombre', '$edad', '$sexo', '$especie', '$raza', '$peso', '$id_propietario', '$RIF_clinica')");
                if ($insertar) {
                    $success = "Paciente registrado exitosamente.";
                } else {
                    $error = "Error al registrar: " . mysqli_error($conexion);
                }
            }
        } catch (mysqli_sql_exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

if (isset($_GET['editar'])) {
    $id = mysqli_real_escape_string($conexion, $_GET['editar']);
    $q = mysqli_query($conexion, "SELECT * FROM mascota WHERE id_mascota='$id' AND RIF_clinica='$RIF_clinica'");
    if ($row = mysqli_fetch_assoc($q)) {
        header('Content-Type: application/json');
        echo json_encode($row);
        exit;
    }
}

if (isset($_GET['eliminar'])) {
    $id = mysqli_real_escape_string($conexion, $_GET['eliminar']);
    mysqli_query($conexion, "DELETE FROM mascota WHERE id_mascota='$id' AND RIF_clinica='$RIF_clinica'");
    exit();
}

$por_pagina = 4;
$pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$total_query = mysqli_query($conexion, "SELECT COUNT(*) as total FROM mascota WHERE RIF_clinica='$RIF_clinica'");
$total = mysqli_fetch_assoc($total_query)['total'];
$por_pagina = 10;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$inicio = ($pagina - 1) * $por_pagina;
$total_paginas = max(1, ceil($total / $por_pagina));
$query_lista = mysqli_query($conexion, "SELECT * FROM mascota WHERE RIF_clinica='$RIF_clinica' ORDER BY nombre ASC LIMIT $inicio, $por_pagina");
$query_propietarios = mysqli_query($conexion, "SELECT id_propietario, nombres, apellidos FROM propietario WHERE RIF_clinica='$RIF_clinica' ORDER BY nombres ASC");
?>
<div class="animate-fadeIn">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-green-800">Pacientes</h1>
            <p class="text-gray-600">Gestión de mascotas.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-5 py-4">
                    <h2 class="text-white font-bold text-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Registrar Paciente
                    </h2>
                </div>
                <form id="form-paciente" class="p-5 space-y-4" action="" method="POST">
                    <input type="hidden" id="editing_id" name="editing_id" value="">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Cédula</label>
                        <div class="flex">
                            <div class="relative rounded-l-xl border-2 border-r-0 border-green-200 bg-gradient-to-br from-green-600 to-emerald-600">
                                <select name="cedula_prefix"
                                        class="appearance-none bg-transparent text-white font-bold pl-4 pr-8 py-3 cursor-pointer outline-none">
                                    <option value="V-" class="text-gray-800 bg-white">V-</option>
                                    <option value="E-" class="text-gray-800 bg-white">E-</option>
                                </select>
                                <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path d="M6 9l6 6 6-6"/>
                                </svg>
                            </div>
                            <input type="text" name="cedula" placeholder="12345678" maxlength="15"
                                   oninput="this.value=this.value.replace(/\D/g,'')"
                                   class="flex-1 px-4 py-3 border-2 border-green-200 rounded-r-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre</label>
                        <input type="text" name="nombre" placeholder="Max" maxlength="25"
                               class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Edad</label>
                        <div class="flex">
                            <div class="relative rounded-l-xl border-2 border-r-0 border-green-200 bg-gradient-to-br from-green-600 to-emerald-600">
                                <select name="edad_unidad"
                                        class="appearance-none bg-transparent text-white font-bold pl-4 pr-8 py-3 cursor-pointer outline-none">
                                    <option value="años" class="text-gray-800 bg-white">Años</option>
                                    <option value="meses" class="text-gray-800 bg-white">Meses</option>
                                </select>
                                <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path d="M6 9l6 6 6-6"/>
                                </svg>
                            </div>
                            <input type="number" name="edad_valor" placeholder="2" min="0" maxlength="2"
                                   oninput="if(this.value.length>2)this.value=this.value.slice(0,2)"
                                   class="flex-1 px-4 py-3 border-2 border-green-200 rounded-r-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Sexo</label>
                        <select name="sexo" id="select-sexo" class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 bg-white focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all appearance-none">
                            <option value="">Seleccionar sexo...</option>
                            <option value="Macho">Macho</option>
                            <option value="Hembra">Hembra</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Especie</label>
                        <select name="especie" id="select-especie" class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 bg-white focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all appearance-none">
                            <option value="">Seleccionar especie...</option>
                            <option value="Perro">Perro</option>
                            <option value="Gato">Gato</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Raza</label>
                        <input type="text" name="raza" placeholder="Pastor Alemán" maxlength="25"
                               class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Peso</label>
                        <div class="flex">
                            <div class="relative rounded-l-xl border-2 border-r-0 border-green-200 bg-gradient-to-br from-green-600 to-emerald-600">
                                <select name="peso_unidad"
                                        class="appearance-none bg-transparent text-white font-bold pl-4 pr-8 py-3 cursor-pointer outline-none">
                                    <option value="kg" class="text-gray-800 bg-white">kg</option>
                                    <option value="lb" class="text-gray-800 bg-white">lb</option>
                                    <option value="g" class="text-gray-800 bg-white">g</option>
                                </select>
                                <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path d="M6 9l6 6 6-6"/>
                                </svg>
                            </div>
                            <input type="number" name="peso_valor" placeholder="15" min="0" step="0.1" maxlength="4"
                                   oninput="if(this.value.length>4)this.value=this.value.slice(0,4)"
                                   class="flex-1 px-4 py-3 border-2 border-green-200 rounded-r-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Propietario</label>
                        <select name="id_propietario" id="select-propietario" class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 bg-white focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all appearance-none">
                            <option value="">Seleccionar propietario...</option>
                            <?php mysqli_data_seek($query_propietarios, 0); while ($p = mysqli_fetch_assoc($query_propietarios)): ?>
                            <option value="<?php echo $p['id_propietario']; ?>"><?php echo $p['id_propietario'] . ' - ' . $p['nombres'] . ' ' . $p['apellidos']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" id="btn-pac-submit"
                                class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl active:scale-[0.98] flex items-center justify-center gap-2">
                            <svg id="btn-pac-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            <span id="btn-pac-text">Guardar Paciente</span>
                        </button>
                        <button type="button" id="btn-cancel-pac-edit" onclick="cancelarEdicionPaciente()"
                                class="hidden px-4 py-3 rounded-xl font-semibold bg-gray-200 text-gray-700 hover:bg-gray-300 transition-all cursor-pointer">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div id="pac-card" class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Lista de Pacientes
                        <span id="pac-count" class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium"><?php echo $total; ?></span>
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-green-50">
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Cédula</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Nombre</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Edad</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Sexo</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Especie</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Raza</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Peso</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="pac-tbody">
                            <?php if ($total > 0): while ($row = mysqli_fetch_assoc($query_lista)): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm font-medium"><?php echo $row['id_mascota']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm"><?php echo $row['nombre']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm"><?php echo $row['edad']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm"><?php echo $row['sexo']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm"><?php echo $row['especie']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm"><?php echo $row['raza']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm"><?php echo $row['peso']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm">
                                    <div class="flex gap-2">
                                        <button onclick="editarPaciente('<?php echo $row['id_mascota']; ?>')"
                                                class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:from-green-700 hover:to-emerald-700 transition-all cursor-pointer">Editar</button>
                                        <button onclick="borrarPaciente('<?php echo $row['id_mascota']; ?>', '<?php echo $row['nombre']; ?>')"
                                                class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-600 transition-all cursor-pointer">Borrar</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm italic text-center" colspan="8">
                                    <div class="flex flex-col items-center py-8">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <p class="text-gray-400 text-sm font-medium">No hay pacientes registrados aún.</p>
                                        <p class="text-gray-400 text-xs mt-1">Utiliza el formulario para registrar un nuevo paciente.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($total > 0): ?>
                <div id="pac-paginacion" class="flex items-center justify-between px-5 py-4 border-t border-gray-100">
                    <span class="text-sm text-gray-500">Mostrando <?php echo $inicio + 1; ?> - <?php echo min($inicio + $por_pagina, $total); ?> de <span id="pac-total"><?php echo $total; ?></span></span>
                    <div class="flex items-center gap-2">
                        <?php if ($pagina > 1): ?>
                        <a href="?pagina=<?php echo $pagina - 1; ?>" onclick="event.preventDefault(); cambiarPaginaPac(<?php echo $pagina - 1; ?>)" class="px-3 py-1.5 rounded-lg text-sm font-medium text-green-700 hover:bg-green-50 border border-green-200 transition-colors">‹</a>
                        <?php endif; ?>
                        <?php for ($p = 1; $p <= $total_paginas; $p++): ?>
                        <a href="?pagina=<?php echo $p; ?>" onclick="event.preventDefault(); cambiarPaginaPac(<?php echo $p; ?>)" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors border <?php echo $p === $pagina ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white border-green-600' : 'text-green-700 hover:bg-green-50 border-green-200'; ?>"><?php echo $p; ?></a>
                        <?php endfor; ?>
                        <?php if ($pagina < $total_paginas): ?>
                        <a href="?pagina=<?php echo $pagina + 1; ?>" onclick="event.preventDefault(); cambiarPaginaPac(<?php echo $pagina + 1; ?>)" class="px-3 py-1.5 rounded-lg text-sm font-medium text-green-700 hover:bg-green-50 border border-green-200 transition-colors">›</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($error) || !empty($success)): ?>
<div id="modal" class="fixed inset-0 bg-black/20 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-96 mx-auto overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5 flex items-center justify-between">
            <h3 class="text-lg font-bold text-white"><?php echo !empty($error) ? 'Error' : 'Éxito'; ?></h3>
            <button onclick="document.getElementById('modal').remove()" class="text-white/80 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="px-8 py-8 text-center">
            <?php if (!empty($error)): ?>
            <div class="w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-gray-600 text-sm mb-6"><?php echo $error; ?></p>
            <?php else: ?>
            <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <p class="text-gray-600 text-sm mb-6"><?php echo $success; ?></p>
            <?php endif; ?>
            <button onclick="document.getElementById('modal').remove()" 
                    class="px-12 mx-auto bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl text-base font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md block">
                Aceptar
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

<div id="modal-confirmar" class="fixed inset-0 bg-black/20 flex items-center justify-center z-50" style="display:none;">
    <div class="bg-white rounded-2xl shadow-xl w-96 mx-auto overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5 flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Confirmar</h3>
            <button onclick="cerrarConfirmacion()" class="text-white/80 hover:text-white transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="px-8 py-8 text-center">
            <div class="w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <p id="modal-confirmar-msg" class="text-gray-600 text-sm mb-6">¿Estás seguro de borrar este paciente?</p>
            <div class="flex gap-3">
                <button onclick="cerrarConfirmacion()" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl text-base font-semibold hover:bg-gray-300 transition-all cursor-pointer">Cancelar</button>
                <button onclick="ejecutarConfirmacion()" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl text-base font-semibold hover:from-green-700 hover:to-emerald-700 transition-all cursor-pointer">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('form-paciente').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const res = await fetch(this.action, { method: 'POST', body: formData });
    const html = await res.text();
    const app = document.getElementById('app');
    app.innerHTML = html;
    app.querySelectorAll('script').forEach(old => {
        const s = document.createElement('script');
        Array.from(old.attributes).forEach(a => s.setAttribute(a.name, a.value));
        s.textContent = old.textContent;
        old.parentNode.replaceChild(s, old);
    });
});

async function cambiarPaginaPac(p) {
    const res = await fetch(window.location.pathname + '?pagina=' + p);
    const html = await res.text();
    const d = document.createElement('div');
    d.innerHTML = html;
    const tbody = d.querySelector('#pac-tbody');
    const pag = d.querySelector('#pac-paginacion');
    const cnt = d.querySelector('#pac-count');
    const tot = d.querySelector('#pac-total');
    if (tbody) document.querySelector('#pac-tbody').replaceWith(tbody.cloneNode(true));
    if (pag) document.querySelector('#pac-paginacion').replaceWith(pag.cloneNode(true));
    if (cnt) document.querySelector('#pac-count').textContent = cnt.textContent;
    if (tot) document.querySelector('#pac-total').textContent = tot.textContent;
    const card = document.querySelector('#pac-card');
    if (card) {
        card.classList.remove('animate-fadeIn');
        void card.offsetWidth;
        card.classList.add('animate-fadeIn');
    }
}

window.editarPaciente = async function(id) {
    const res = await fetch(window.location.pathname + '?editar=' + encodeURIComponent(id));
    const data = await res.json();

    const m = data.id_mascota.match(/^([VE]-)(\d+)$/);
    if (m) {
        document.querySelector('select[name="cedula_prefix"]').value = m[1];
        document.querySelector('input[name="cedula"]').value = m[2];
    }

    document.querySelector('input[name="nombre"]').value = data.nombre;

    var edadParts = data.edad.trim().split(' ');
    document.querySelector('input[name="edad_valor"]').value = edadParts[0] || '';
    if (edadParts[1]) {
        var edadSel = document.querySelector('select[name="edad_unidad"]');
        for (var i = 0; i < edadSel.options.length; i++) {
            if (edadSel.options[i].value === edadParts[1]) { edadSel.selectedIndex = i; break; }
        }
    }

    document.getElementById('select-sexo').value = data.sexo;
    document.getElementById('select-especie').value = data.especie;
    document.querySelector('input[name="raza"]').value = data.raza;

    var pesoParts = data.peso.trim().split(' ');
    document.querySelector('input[name="peso_valor"]').value = pesoParts[0] || '';
    if (pesoParts[1]) {
        var pesoSel = document.querySelector('select[name="peso_unidad"]');
        for (var i = 0; i < pesoSel.options.length; i++) {
            if (pesoSel.options[i].value === pesoParts[1]) { pesoSel.selectedIndex = i; break; }
        }
    }
    document.getElementById('select-propietario').value = data.id_propietario;

    document.getElementById('editing_id').value = data.id_mascota;
    document.getElementById('btn-pac-text').textContent = 'Guardar cambios';
    document.getElementById('btn-pac-icon').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />';
    document.getElementById('btn-cancel-pac-edit').classList.remove('hidden');
};

window.cancelarEdicionPaciente = function() {
    document.getElementById('form-paciente').reset();
    document.getElementById('editing_id').value = '';
    document.getElementById('btn-pac-text').textContent = 'Guardar Paciente';
    document.getElementById('btn-pac-icon').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />';
    document.getElementById('btn-cancel-pac-edit').classList.add('hidden');
};

var confirmarCallback = null;

window.mostrarConfirmacion = function(mensaje, callback) {
    document.getElementById('modal-confirmar-msg').textContent = mensaje;
    confirmarCallback = callback;
    document.getElementById('modal-confirmar').style.display = 'flex';
};

window.cerrarConfirmacion = function() {
    document.getElementById('modal-confirmar').style.display = 'none';
    confirmarCallback = null;
};

window.ejecutarConfirmacion = function() {
    if (typeof confirmarCallback === 'function') {
        confirmarCallback();
    }
    cerrarConfirmacion();
};

window.borrarPaciente = function(id, nombre) {
    window.mostrarConfirmacion('¿Estás seguro de borrar a "' + nombre + '"?', function() {
        fetch(window.location.pathname + '?delete=' + encodeURIComponent(id))
        .then(function() { if (typeof loadPage === 'function') loadPage(window.location.pathname); else location.reload(); });
    });
};
</script>
