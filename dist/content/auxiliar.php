<?php
session_start();
include '../bd.php';
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_auxiliar = $_POST['cedula_prefix'] . mysqli_real_escape_string($conexion, $_POST['cedula']);
    $nombres = mysqli_real_escape_string($conexion, $_POST['nombres']);
    $apellidos = mysqli_real_escape_string($conexion, $_POST['apellidos']);
    $email_user = mysqli_real_escape_string($conexion, $_POST['email_user']);
    $email_domain_raw = $_POST['email_domain'] ?? '@gmail.com';
    if ($email_domain_raw === 'otros') {
        $email_domain = '@' . mysqli_real_escape_string($conexion, $_POST['email_domain_custom'] ?? '');
    } else {
        $email_domain = $email_domain_raw;
    }
    $gmail = $email_user . $email_domain;
    $telefono = $_POST['telefono_prefix'] . mysqli_real_escape_string($conexion, $_POST['telefono']);
    $password = mysqli_real_escape_string($conexion, $_POST['password']);
    $editing_id = mysqli_real_escape_string($conexion, $_POST['editing_id'] ?? '');

    if (empty($id_auxiliar) || empty($nombres) || empty($apellidos) || empty($gmail) || empty($telefono) || empty($password)) {
        if (!empty($editing_id) && empty($password)) {
            // editing with no password change is allowed
        } else {
            $error = "Todos los campos son obligatorios.";
        }
    }

    if (empty($error) && !empty($editing_id)) {
        try {
            $verificar = mysqli_query($conexion, "SELECT * FROM `aux-vet` WHERE id_auxiliar='$id_auxiliar' AND id_auxiliar != '$editing_id' AND RIF_clinica='$RIF_clinica'");
            if (mysqli_num_rows($verificar) > 0) {
                $error = "La cédula ya está registrada por otro auxiliar.";
            } else {
                if (!empty($password)) {
                    $update = mysqli_query($conexion, "UPDATE `aux-vet` SET id_auxiliar='$id_auxiliar', nombres='$nombres', apellidos='$apellidos', gmail='$gmail', telefono='$telefono', password='$password' WHERE id_auxiliar='$editing_id' AND RIF_clinica='$RIF_clinica'");
                } else {
                    $update = mysqli_query($conexion, "UPDATE `aux-vet` SET id_auxiliar='$id_auxiliar', nombres='$nombres', apellidos='$apellidos', gmail='$gmail', telefono='$telefono' WHERE id_auxiliar='$editing_id' AND RIF_clinica='$RIF_clinica'");
                }
                if ($update) {
                    $success = "Auxiliar actualizado exitosamente.";
                } else {
                    $error = "Error al actualizar: " . mysqli_error($conexion);
                }
            }
        } catch (mysqli_sql_exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    } elseif (empty($error)) {
        try {
            $verificar = mysqli_query($conexion, "SELECT * FROM `aux-vet` WHERE id_auxiliar='$id_auxiliar' AND RIF_clinica='$RIF_clinica'");
            if (mysqli_num_rows($verificar) > 0) {
                $error = "La cédula ya está registrada.";
            } else {
                $insertar = mysqli_query($conexion, "INSERT INTO `aux-vet` (id_auxiliar, nombres, apellidos, gmail, telefono, password, RIF_clinica) VALUES ('$id_auxiliar', '$nombres', '$apellidos', '$gmail', '$telefono', '$password', '$RIF_clinica')");
                if ($insertar) {
                    $success = "Auxiliar registrado exitosamente.";
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
    $q = mysqli_query($conexion, "SELECT * FROM `aux-vet` WHERE id_auxiliar='$id' AND RIF_clinica='$RIF_clinica'");
    if ($row = mysqli_fetch_assoc($q)) {
        header('Content-Type: application/json');
        echo json_encode($row);
    }
    exit();
}

if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conexion, $_GET['delete']);
    mysqli_query($conexion, "DELETE FROM `aux-vet` WHERE id_auxiliar='$id' AND RIF_clinica='$RIF_clinica'");
    exit();
}

$por_pagina = 4;
$pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$total_query = mysqli_query($conexion, "SELECT COUNT(*) as total FROM `aux-vet` WHERE RIF_clinica='$RIF_clinica'");
$total = mysqli_fetch_assoc($total_query)['total'];
$total_paginas = max(1, ceil($total / $por_pagina));
$pagina = min($pagina, $total_paginas);
$inicio = ($pagina - 1) * $por_pagina;
$query_lista = mysqli_query($conexion, "SELECT * FROM `aux-vet` WHERE RIF_clinica='$RIF_clinica' ORDER BY nombres ASC LIMIT $inicio, $por_pagina");
?>
<div class="animate-fadeIn">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-green-800">Auxiliares Veterinarios</h1>
            <p class="text-gray-600">Personal de apoyo médico.</p>
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
                        Registrar Auxiliar
                    </h2>
                </div>
                <form id="form-auxiliar" class="p-5 space-y-4" action="" method="POST">
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
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombres</label>
                        <input type="text" name="nombres" placeholder="Juan Carlos" maxlength="25"
                               class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Apellidos</label>
                        <input type="text" name="apellidos" placeholder="Pérez Rodríguez" maxlength="25"
                               class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Correo electrónico</label>
                        <div class="flex w-full overflow-hidden">
                            <input type="text" name="email_user" placeholder="correo personal" maxlength="25"
                                   class="flex-1 min-w-0 px-4 py-3 border-2 border-green-200 border-r-0 rounded-l-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                            <div class="relative rounded-r-xl border-2 border-l-0 border-green-200 bg-gradient-to-br from-green-600 to-emerald-600 flex-shrink-0 w-24">
                                <select name="email_domain" id="email-domain-select"
                                        class="appearance-none bg-transparent text-white font-bold pl-4 pr-8 py-3 cursor-pointer outline-none h-full">
                                    <option value="@gmail.com" class="text-gray-800 bg-white">@gmail.com</option>
                                    <option value="@hotmail.com" class="text-gray-800 bg-white">@hotmail.com</option>
                                    <option value="@outlook.com" class="text-gray-800 bg-white">@outlook.com</option>
                                    <option value="@yahoo.com" class="text-gray-800 bg-white">@yahoo.com</option>
                                    <option value="otros" class="text-gray-800 bg-white">Otros</option>
                                </select>
                                <input type="text" name="email_domain_custom" id="email-domain-custom" placeholder="ejemplo.com"
                                       class="h-full px-4 py-3 rounded-r-xl outline-none text-white placeholder-white/70 focus:border-green-500 transition-all bg-transparent"
                                       style="display:none; position:absolute; inset:0; width:100%;">
                                <svg id="domain-arrow" class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono</label>
                        <div class="flex">
                            <div class="relative rounded-l-xl border-2 border-r-0 border-green-200 bg-gradient-to-br from-green-600 to-emerald-600">
                                <select name="telefono_prefix"
                                        class="appearance-none bg-transparent text-white font-bold pl-4 pr-8 py-3 cursor-pointer outline-none">
                                    <option value="0412-" class="text-gray-800 bg-white">0412</option>
                                    <option value="0414-" class="text-gray-800 bg-white">0414</option>
                                    <option value="0416-" class="text-gray-800 bg-white">0416</option>
                                    <option value="0422-" class="text-gray-800 bg-white">0422</option>
                                    <option value="0424-" class="text-gray-800 bg-white">0424</option>
                                    <option value="0426-" class="text-gray-800 bg-white">0426</option>
                                </select>
                                <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path d="M6 9l6 6 6-6"/>
                                </svg>
                            </div>
                            <input type="text" name="telefono" placeholder="1234567" maxlength="7"
                                   oninput="this.value=this.value.replace(/\D/g,'')"
                                   class="flex-1 px-4 py-3 border-2 border-green-200 rounded-r-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña</label>
                        <input type="password" name="password" placeholder="Ingresa la contraseña"
                               class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" id="btn-aux-submit"
                                class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl active:scale-[0.98] flex items-center justify-center gap-2">
                            <svg id="btn-aux-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            <span id="btn-aux-text">Guardar Auxiliar</span>
                        </button>
                        <button type="button" id="btn-cancel-aux-edit" onclick="cancelarEdicionAuxiliar()"
                                class="hidden px-4 py-3 rounded-xl font-semibold bg-gray-200 text-gray-700 hover:bg-gray-300 transition-all cursor-pointer">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div id="aux-card" class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Lista de Auxiliares
                        <span id="aux-count" class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium"><?php echo $total; ?></span>
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-green-50">
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Cédula</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Nombres</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Apellidos</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Correo</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Teléfono</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="aux-tbody">
                            <?php if ($total > 0): while ($row = mysqli_fetch_assoc($query_lista)): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm font-medium"><?php echo $row['id_auxiliar']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm"><?php echo $row['nombres']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm"><?php echo $row['apellidos']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm"><?php echo $row['gmail']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm"><?php echo $row['telefono']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm">
                                    <div class="flex gap-2">
                                        <button onclick="editarAuxiliar('<?php echo $row['id_auxiliar']; ?>')"
                                                class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:from-green-700 hover:to-emerald-700 transition-all cursor-pointer">Editar</button>
                                        <button onclick="borrarAuxiliar('<?php echo $row['id_auxiliar']; ?>', '<?php echo $row['nombres']; ?>', '<?php echo $row['apellidos']; ?>')"
                                                class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-600 transition-all cursor-pointer">Borrar</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm italic text-center" colspan="6">
                                    <div class="flex flex-col items-center py-8">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <p class="text-gray-400 text-sm font-medium">No hay auxiliares registrados aún.</p>
                                        <p class="text-gray-400 text-xs mt-1">Utiliza el formulario para registrar un nuevo auxiliar.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($total > 0): ?>
                <div id="aux-paginacion" class="flex items-center justify-between px-5 py-4 border-t border-gray-100">
                    <span class="text-sm text-gray-500">Mostrando <?php echo $inicio + 1; ?> - <?php echo min($inicio + $por_pagina, $total); ?> de <span id="aux-total"><?php echo $total; ?></span></span>
                    <div class="flex items-center gap-2">
                        <?php if ($pagina > 1): ?>
                        <a href="?pagina=<?php echo $pagina - 1; ?>" onclick="event.preventDefault(); cambiarPaginaAux(<?php echo $pagina - 1; ?>)" class="px-3 py-1.5 rounded-lg text-sm font-medium text-green-700 hover:bg-green-50 border border-green-200 transition-colors">‹</a>
                        <?php endif; ?>
                        <?php for ($p = 1; $p <= $total_paginas; $p++): ?>
                        <a href="?pagina=<?php echo $p; ?>" onclick="event.preventDefault(); cambiarPaginaAux(<?php echo $p; ?>)" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors border <?php echo $p === $pagina ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white border-green-600' : 'text-green-700 hover:bg-green-50 border-green-200'; ?>"><?php echo $p; ?></a>
                        <?php endfor; ?>
                        <?php if ($pagina < $total_paginas): ?>
                        <a href="?pagina=<?php echo $pagina + 1; ?>" onclick="event.preventDefault(); cambiarPaginaAux(<?php echo $pagina + 1; ?>)" class="px-3 py-1.5 rounded-lg text-sm font-medium text-green-700 hover:bg-green-50 border border-green-200 transition-colors">›</a>
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
            <p id="modal-confirmar-msg" class="text-gray-600 text-sm mb-6">¿Estás seguro de borrar este auxiliar?</p>
            <div class="flex gap-3">
                <button onclick="cerrarConfirmacion()" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl text-base font-semibold hover:bg-gray-300 transition-all cursor-pointer">Cancelar</button>
                <button onclick="ejecutarConfirmacion()" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl text-base font-semibold hover:from-green-700 hover:to-emerald-700 transition-all cursor-pointer">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('form-auxiliar').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const domainSelect = document.querySelector('select[name="email_domain"]');
    if (domainSelect) formData.set('email_domain', domainSelect.value);
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

document.addEventListener('change', function(e) {
    if (e.target.id === 'email-domain-select') {
        const select = e.target;
        const custom = document.getElementById('email-domain-custom');
        const arrow = document.getElementById('domain-arrow');
        if (select.value === 'otros') {
            select.style.display = 'none';
            custom.style.display = 'block';
            arrow.style.display = 'none';
            custom.focus();
        }
    }
});
document.addEventListener('blur', function(e) {
    if (e.target.id === 'email-domain-custom' && e.target.value.trim() === '') {
        const select = document.getElementById('email-domain-select');
        const arrow = document.getElementById('domain-arrow');
        e.target.style.display = 'none';
        select.style.display = 'block';
        select.value = '@gmail.com';
        arrow.style.display = 'block';
    }
}, true);

async function cambiarPaginaAux(p) {
    const res = await fetch(window.location.pathname + '?pagina=' + p);
    const html = await res.text();
    const d = document.createElement('div');
    d.innerHTML = html;
    const tbody = d.querySelector('#aux-tbody');
    const pag = d.querySelector('#aux-paginacion');
    const cnt = d.querySelector('#aux-count');
    const tot = d.querySelector('#aux-total');
    if (tbody) document.querySelector('#aux-tbody').replaceWith(tbody.cloneNode(true));
    if (pag) document.querySelector('#aux-paginacion').replaceWith(pag.cloneNode(true));
    if (cnt) document.querySelector('#aux-count').textContent = cnt.textContent;
    if (tot) document.querySelector('#aux-total').textContent = tot.textContent;
    const card = document.querySelector('#aux-card');
    if (card) {
        card.classList.remove('animate-fadeIn');
        void card.offsetWidth;
        card.classList.add('animate-fadeIn');
    }
}

window.editarAuxiliar = async function(id) {
    const res = await fetch(window.location.pathname + '?editar=' + encodeURIComponent(id));
    const data = await res.json();

    const m = data.id_auxiliar.match(/^([VE]-)(\d+)$/);
    if (m) {
        document.querySelector('select[name="cedula_prefix"]').value = m[1];
        document.querySelector('input[name="cedula"]').value = m[2];
    }

    document.querySelector('input[name="nombres"]').value = data.nombres;
    document.querySelector('input[name="apellidos"]').value = data.apellidos;

    const em = data.gmail.match(/^(.+?)(@.+)$/);
    if (em) {
        document.querySelector('input[name="email_user"]').value = em[1];
        document.querySelector('select[name="email_domain"]').value = em[2];
    }

    const t = data.telefono.match(/^(041[246]|042[246])-(\d+)$/);
    if (t) {
        document.querySelector('select[name="telefono_prefix"]').value = t[1] + '-';
        document.querySelector('input[name="telefono"]').value = t[2];
    }

    document.getElementById('editing_id').value = data.id_auxiliar;
    document.getElementById('btn-aux-text').textContent = 'Guardar cambios';
    document.getElementById('btn-aux-icon').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />';
    document.getElementById('btn-cancel-aux-edit').classList.remove('hidden');
};

window.cancelarEdicionAuxiliar = function() {
    document.getElementById('form-auxiliar').reset();
    document.getElementById('editing_id').value = '';
    document.getElementById('btn-aux-text').textContent = 'Guardar Auxiliar';
    document.getElementById('btn-aux-icon').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />';
    document.getElementById('btn-cancel-aux-edit').classList.add('hidden');
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

window.borrarAuxiliar = function(id, nombres, apellidos) {
    window.mostrarConfirmacion('¿Estás seguro de borrar a "' + nombres + ' ' + apellidos + '"?', function() {
        fetch(window.location.pathname + '?delete=' + encodeURIComponent(id))
        .then(function() { if (typeof loadPage === 'function') loadPage(window.location.pathname); else location.reload(); });
    });
};
</script>
