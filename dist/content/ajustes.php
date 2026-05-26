<?php
include '../bd.php';
$error = "";
$success = "";

$rif_clinica = $_SESSION['usuario']['RIF_clinica'];
$nombre_clinic = $_SESSION['usuario']['Nombre_clinic'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombres = mysqli_real_escape_string($conexion, $_POST['nombres']);
    $id_veterinario = $_POST['cedula_prefix'] . mysqli_real_escape_string($conexion, $_POST['cedula']);
    $email_user = mysqli_real_escape_string($conexion, $_POST['email_user']);
    if ($_POST['email_domain'] === 'otros') {
        $email_domain = '@' . mysqli_real_escape_string($conexion, $_POST['email_domain_custom']);
    } else {
        $email_domain = $_POST['email_domain'];
    }
    $gmail = $email_user . $email_domain;
    $password = mysqli_real_escape_string($conexion, $_POST['password']);
    $editing_id = mysqli_real_escape_string($conexion, $_POST['editing_id'] ?? '');

    if (empty($nombres) || empty($id_veterinario) || empty($gmail)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (!empty($editing_id)) {
        try {
            if (!empty($password)) {
                $update = mysqli_query($conexion, "UPDATE veterinario SET Id_veterinario='$id_veterinario', Nombres='$nombres', Gmail='$gmail', Password='$password' WHERE Id_veterinario='$editing_id' AND RIF_clinica='$rif_clinica'");
            } else {
                $update = mysqli_query($conexion, "UPDATE veterinario SET Id_veterinario='$id_veterinario', Nombres='$nombres', Gmail='$gmail' WHERE Id_veterinario='$editing_id' AND RIF_clinica='$rif_clinica'");
            }
            if ($update) {
                $success = "Administrador actualizado exitosamente.";
            } else {
                $error = "Error al actualizar: " . mysqli_error($conexion);
            }
        } catch (mysqli_sql_exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    } elseif (empty($password)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        $verificar = mysqli_query($conexion, "SELECT * FROM veterinario WHERE Id_veterinario='$id_veterinario' AND RIF_clinica='$rif_clinica'");
        if (mysqli_num_rows($verificar) > 0) {
            $error = "La cédula ya está registrada.";
        } else {
            try {
                $insertar = mysqli_query($conexion, "INSERT INTO veterinario (RIF_clinica, Nombres, Id_veterinario, Gmail, Password, rol) VALUES ('$rif_clinica', '$nombres', '$id_veterinario', '$gmail', '$password', 'vet')");
                if ($insertar) {
                    $success = "Administrador registrado exitosamente.";
                } else {
                    $error = "Error al registrar: " . mysqli_error($conexion);
                }
            } catch (mysqli_sql_exception $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}

if (isset($_GET['editar'])) {
    $id = mysqli_real_escape_string($conexion, $_GET['editar']);
    $q = mysqli_query($conexion, "SELECT * FROM veterinario WHERE Id_veterinario='$id' AND RIF_clinica='$rif_clinica'");
    if ($row = mysqli_fetch_assoc($q)) {
        header('Content-Type: application/json');
        echo json_encode($row);
    }
    exit();
}

if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conexion, $_GET['delete']);
    mysqli_query($conexion, "DELETE FROM veterinario WHERE Id_veterinario='$id' AND RIF_clinica='$rif_clinica'");
    exit();
}

$por_pagina = 4;
$pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$total_query = mysqli_query($conexion, "SELECT COUNT(*) as total FROM veterinario WHERE RIF_clinica='$rif_clinica'");
$total = mysqli_fetch_assoc($total_query)['total'];
$total_paginas = max(1, ceil($total / $por_pagina));
if ($pagina > $total_paginas) $pagina = $total_paginas;
$inicio = ($pagina - 1) * $por_pagina;
$query_lista = mysqli_query($conexion, "SELECT * FROM veterinario WHERE RIF_clinica='$rif_clinica' ORDER BY Nombres ASC LIMIT $por_pagina OFFSET $inicio");
?>
<div class="animate-fadeIn">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-green-800"><?php echo $nombre_clinic; ?></h1>
        <p class="text-gray-600 mt-1">RIF: <?php echo $rif_clinica; ?></p>
    </div>



    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-5 py-4">
                    <h2 class="text-white font-bold text-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Registro administrador
                    </h2>
                </div>
                <form id="form-admin" class="p-5 space-y-4 pb-16" action="" method="POST">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombres</label>
                        <input type="text" name="nombres" placeholder="Nombre y apellido" maxlength="25"
                               class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                    </div>
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
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Correo electrónico</label>
                        <div class="flex w-full overflow-hidden">
                            <input type="text" name="email_user" placeholder="correo personal" maxlength="25"
                                   class="flex-1 min-w-0 px-4 py-3 border-2 border-green-200 border-r-0 rounded-l-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                            <div class="relative rounded-r-xl border-2 border-l-0 border-green-200 bg-gradient-to-br from-green-600 to-emerald-600 flex-shrink-0 w-24">
                                <select name="email_domain" id="email-domain-select"
                                        class="appearance-none bg-transparent text-white font-bold pl-3 pr-7 py-3 cursor-pointer outline-none h-full">
                                    <option value="@gmail.com" class="text-gray-800 bg-white">@gmail.com</option>
                                    <option value="@hotmail.com" class="text-gray-800 bg-white">@hotmail.com</option>
                                    <option value="@outlook.com" class="text-gray-800 bg-white">@outlook.com</option>
                                    <option value="@yahoo.com" class="text-gray-800 bg-white">@yahoo.com</option>
                                    <option value="otros" class="text-gray-800 bg-white">Otros</option>
                                </select>
                                <input type="text" name="email_domain_custom" id="email-domain-custom" placeholder="ejemplo.com"
                                       class="h-full px-3 py-3 rounded-r-xl outline-none text-white placeholder-white/70 focus:border-green-500 transition-all bg-transparent"
                                       style="display:none; position:absolute; inset:0; width:100%;">
                                <svg id="domain-arrow" class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path d="M6 9l6 6 6-6"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña</label>
                        <input type="password" name="password" placeholder="••••••••"
                               class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                    </div>
                    <input type="hidden" name="editing_id" id="editing_id" value="">
                    <button type="submit" id="btn-submit-admin"
                            class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl active:scale-[0.98] flex items-center justify-center gap-2">
                        <svg id="btn-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        <span id="btn-text">Guardar administrador</span>
                    </button>
                    <button type="button" id="btn-cancel-edit" onclick="cancelarEdicion()"
                            class="w-full bg-gray-200 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-300 transition-all hidden">
                        Cancelar edición
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-8">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-2xl p-6 flex items-start gap-4 shadow-sm">
                <div class="shrink-0 w-10 h-10 rounded-full border-2 border-white/60 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86l-8.57 14.86A1 1 0 002.57 20h18.86a1 1 0 00.86-1.49L13.71 3.86a1 1 0 00-1.72 0z" />
                    </svg>
                </div>
                <p class="text-white text-base leading-relaxed">
                    Los administradores registrados tendrán accesibilidad a todo el sistema, se recomienda evaluar de manera correcta el perfil de este.
                </p>
            </div>

            <div id="admin-card" class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <h2 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Lista de Administradores
                            <span id="admin-count" class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium"><?php echo $total; ?></span>
                        </h2>
                        <span id="page-indicator" class="text-xs text-gray-400">Página <?php echo $pagina; ?> de <?php echo $total_paginas; ?></span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-green-50">
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Cédula</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Nombres</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Correo</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="admin-tbody">
                            <?php if ($total > 0): while ($row = mysqli_fetch_assoc($query_lista)): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm font-medium"><?php echo $row['Id_veterinario']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm"><?php echo $row['Nombres']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm"><?php echo $row['Gmail']; ?></td>
                                <td class="p-4 border-b border-gray-100 whitespace-nowrap">
                                    <button onclick="editarAdmin('<?php echo $row['Id_veterinario']; ?>')" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:from-green-700 hover:to-emerald-700 transition-all inline-block cursor-pointer">Editar</button>
                                    <button onclick="borrarAdmin('<?php echo $row['Id_veterinario']; ?>', '<?php echo addslashes($row['Nombres']); ?>')" class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-600 transition-all inline-block ml-1.5 cursor-pointer">Borrar</button>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm italic text-center" colspan="4">
                                    <div class="flex flex-col items-center py-8">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <p class="text-gray-400 text-sm font-medium">No hay administradores registrados aún.</p>
                                        <p class="text-gray-400 text-xs mt-1">Utiliza el formulario para registrar un nuevo administrador.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($total > 0): ?>
                <div id="admin-paginacion" class="flex items-center justify-between px-5 py-4 border-t border-gray-100">
                    <span id="pag-info" class="text-sm text-gray-500">Mostrando <?php echo $inicio + 1; ?> - <?php echo min($inicio + $por_pagina, $total); ?> de <?php echo $total; ?></span>
                    <div class="flex items-center gap-2">
                        <?php if ($pagina > 1): ?>
                        <a href="?pagina=<?php echo $pagina - 1; ?>" onclick="event.preventDefault(); cambiarPagina(<?php echo $pagina - 1; ?>)" class="px-3 py-1.5 rounded-lg text-sm font-medium text-green-700 hover:bg-green-50 border border-green-200 cursor-pointer transition-colors">‹</a>
                        <?php endif; ?>
                        <?php for ($p = 1; $p <= $total_paginas; $p++): ?>
                        <a href="?pagina=<?php echo $p; ?>" onclick="event.preventDefault(); cambiarPagina(<?php echo $p; ?>)" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors border <?php echo $p === $pagina ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white border-green-600 cursor-default' : 'text-green-700 hover:bg-green-50 border-green-200 cursor-pointer'; ?>"><?php echo $p; ?></a>
                        <?php endfor; ?>
                        <?php if ($pagina < $total_paginas): ?>
                        <a href="?pagina=<?php echo $pagina + 1; ?>" onclick="event.preventDefault(); cambiarPagina(<?php echo $pagina + 1; ?>)" class="px-3 py-1.5 rounded-lg text-sm font-medium text-green-700 hover:bg-green-50 border border-green-200 cursor-pointer transition-colors">›</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <h2 class="text-2xl font-bold text-green-800 mb-6 flex items-center gap-2">
        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Precios por servicios prestados
    </h2>

    <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
            <h3 class="text-white font-bold text-lg flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                Establecer precios
            </h3>
        </div>
        <div class="p-6 space-y-8" id="precios-form">
            <div class="bg-green-50/50 rounded-xl p-5 border border-green-100">
                <h4 class="font-bold text-green-800 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Consulta
                </h4>
                <div class="flex items-center gap-3">
                    <label class="text-sm text-gray-700 font-medium w-40 shrink-0">Precio de consulta</label>
                    <div class="relative w-28">
                        <input type="number" step="0.01" min="0" value="0.00"
                               class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                               data-servicio="Consulta General" disabled>
                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 mt-3">
                    <label class="text-sm text-gray-700 font-medium w-40 shrink-0">Dto. múltiple</label>
                    <div class="relative w-28">
                        <input type="number" step="0.5" min="0" max="100" value="0.00"
                               class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                               data-servicio="Descuento Multiple" disabled>
                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">%</span>
                    </div>
                </div>
            </div>

            <div class="bg-green-50/50 rounded-xl p-5 border border-green-100">
                <h4 class="font-bold text-green-800 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Tests Rápidos
                </h4>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 w-40 shrink-0">Parvovirus</span>
                        <div class="relative w-28">
                            <input type="number" step="0.01" min="0" value="0.00"
                                   class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                   data-servicio="Parvovirus" disabled>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 w-40 shrink-0">Anaplasma</span>
                        <div class="relative w-28">
                            <input type="number" step="0.01" min="0" value="0.00"
                                   class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                   data-servicio="Anaplasma" disabled>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 w-40 shrink-0">Distemper</span>
                        <div class="relative w-28">
                            <input type="number" step="0.01" min="0" value="0.00"
                                   class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                   data-servicio="Distemper" disabled>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 w-40 shrink-0">Brucella</span>
                        <div class="relative w-28">
                            <input type="number" step="0.01" min="0" value="0.00"
                                   class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                   data-servicio="Brucella" disabled>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 w-40 shrink-0">FeLV / FIV</span>
                        <div class="relative w-28">
                            <input type="number" step="0.01" min="0" value="0.00"
                                   class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                   data-servicio="FeLV / FIV" disabled>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 w-40 shrink-0">Giardia</span>
                        <div class="relative w-28">
                            <input type="number" step="0.01" min="0" value="0.00"
                                   class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                   data-servicio="Giardia" disabled>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-green-50/50 rounded-xl p-5 border border-green-100">
                <h4 class="font-bold text-green-800 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    Laboratorio
                </h4>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 w-40 shrink-0">Hematología</span>
                        <div class="relative w-28">
                            <input type="number" step="0.01" min="0" value="0.00"
                                   class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                   data-servicio="Hematología" disabled>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 w-40 shrink-0">Bioquímica sanguínea</span>
                        <div class="relative w-28">
                            <input type="number" step="0.01" min="0" value="0.00"
                                   class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                   data-servicio="Bioquímica sanguínea" disabled>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 w-40 shrink-0">Perfil tiroideo</span>
                        <div class="relative w-28">
                            <input type="number" step="0.01" min="0" value="0.00"
                                   class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                   data-servicio="Perfil tiroideo" disabled>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 w-40 shrink-0">Orina</span>
                        <div class="relative w-28">
                            <input type="number" step="0.01" min="0" value="0.00"
                                   class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                   data-servicio="Orina" disabled>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 w-40 shrink-0">Pruebas hepáticas</span>
                        <div class="relative w-28">
                            <input type="number" step="0.01" min="0" value="0.00"
                                   class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                   data-servicio="Pruebas hepáticas" disabled>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 w-40 shrink-0">Cultivo bacteriano</span>
                        <div class="relative w-28">
                            <input type="number" step="0.01" min="0" value="0.00"
                                   class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                   data-servicio="Cultivo bacteriano" disabled>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 w-40 shrink-0">Parasitología</span>
                        <div class="relative w-28">
                            <input type="number" step="0.01" min="0" value="0.00"
                                   class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                   data-servicio="Parasitología" disabled>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 w-40 shrink-0">Inmunología</span>
                        <div class="relative w-28">
                            <input type="number" step="0.01" min="0" value="0.00"
                                   class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                   data-servicio="Inmunología" disabled>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-green-50/50 rounded-xl p-5 border border-green-100">
                <h4 class="font-bold text-green-800 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Vacunas
                </h4>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 w-40 shrink-0">Polivalente</span>
                        <div class="relative w-28">
                            <input type="number" step="0.01" min="0" value="0.00"
                                   class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                   data-servicio="Polivalente" disabled>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 w-40 shrink-0">Trivalente felina</span>
                        <div class="relative w-28">
                            <input type="number" step="0.01" min="0" value="0.00"
                                   class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                   data-servicio="Trivalente felina" disabled>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 w-40 shrink-0">Leucemia</span>
                        <div class="relative w-28">
                            <input type="number" step="0.01" min="0" value="0.00"
                                   class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                   data-servicio="Leucemia" disabled>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 w-40 shrink-0">Antirábica</span>
                        <div class="relative w-28">
                            <input type="number" step="0.01" min="0" value="0.00"
                                   class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                   data-servicio="Antirábica" disabled>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 w-40 shrink-0">Leptospirosis</span>
                        <div class="relative w-28">
                            <input type="number" step="0.01" min="0" value="0.00"
                                   class="precio-input w-full px-3 py-2 pr-6 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                   data-servicio="Leptospirosis" disabled>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-xs">$</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center pt-2">
                <button id="btn-toggle-precios" type="button"
                        class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-10 py-3.5 rounded-xl text-base font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl active:scale-[0.98]">
                    Editar precios
                </button>
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
            <p id="modal-confirmar-msg" class="text-gray-600 text-sm mb-6">¿Estás seguro de borrar este administrador?</p>
            <div class="flex gap-3">
                <button onclick="cerrarConfirmacion()" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl text-base font-semibold hover:bg-gray-300 transition-all cursor-pointer">Cancelar</button>
                <button onclick="ejecutarConfirmacion()" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl text-base font-semibold hover:from-green-700 hover:to-emerald-700 transition-all cursor-pointer">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var btn = document.getElementById('btn-toggle-precios');
    var inputs = document.querySelectorAll('.precio-input');

    function setEditMode(editing) {
        for (var i = 0; i < inputs.length; i++) {
            if (editing) {
                inputs[i].removeAttribute('disabled');
            } else {
                inputs[i].setAttribute('disabled', 'disabled');
            }
        }
        btn.textContent = editing ? 'Establecer precios' : 'Editar precios';
    }

    function cargarPrecios() {
        fetch('/dist/content/inicio_data.php?action=cargar_precios')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                for (var i = 0; i < inputs.length; i++) {
                    var servicio = inputs[i].getAttribute('data-servicio');
                    if (data[servicio] !== undefined) {
                        inputs[i].value = parseFloat(data[servicio]).toFixed(2);
                    }
                }
            })
            .catch(function() {});
    }

    function guardarPrecios() {
        var data = {};
        for (var i = 0; i < inputs.length; i++) {
            var servicio = inputs[i].getAttribute('data-servicio');
            data[servicio] = parseFloat(inputs[i].value) || 0;
        }
        return fetch('/dist/content/inicio_data.php?action=guardar_precios', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        }).then(function(r) { return r.json(); });
    }

    cargarPrecios();
    setEditMode(false);

    btn.addEventListener('click', function() {
        if (btn.textContent === 'Establecer precios') {
            btn.textContent = 'Guardando...';
            btn.disabled = true;
            guardarPrecios().then(function(resp) {
                alert(resp.message);
                if (resp.success) {
                    setEditMode(false);
                }
                btn.disabled = false;
            }).catch(function() {
                alert('Error de conexion');
                btn.disabled = false;
            });
        } else {
            setEditMode(true);
        }
    });
})();

document.addEventListener('change', function(e) {
    if (e.target && e.target.id === 'email-domain-select') {
        var sel = e.target;
        var custom = document.getElementById('email-domain-custom');
        var arrow = document.getElementById('domain-arrow');
        if (sel.value === 'otros') {
            sel.style.display = 'none';
            arrow.style.display = 'none';
            custom.style.display = 'block';
            custom.focus();
        }
    }
});

document.addEventListener('blur', function(e) {
    if (e.target && e.target.id === 'email-domain-custom') {
        var custom = e.target;
        var sel = document.getElementById('email-domain-select');
        var arrow = document.getElementById('domain-arrow');
        if (custom.value.trim() === '') {
            custom.style.display = 'none';
            sel.style.display = 'block';
            arrow.style.display = 'block';
            sel.value = '@gmail.com';
        }
    }
}, true);

document.getElementById('form-admin').addEventListener('submit', async function(e) {
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

async function cambiarPagina(p) {
    const res = await fetch(window.location.pathname + '?pagina=' + p);
    const html = await res.text();
    const d = document.createElement('div');
    d.innerHTML = html;
    const tbody = d.querySelector('#admin-tbody');
    const pag = d.querySelector('#admin-paginacion');
    const cnt = d.querySelector('#admin-count');
    const idx = d.querySelector('#page-indicator');
    if (tbody) document.querySelector('#admin-tbody').replaceWith(tbody.cloneNode(true));
    if (pag) document.querySelector('#admin-paginacion').replaceWith(pag.cloneNode(true));
    if (cnt) document.querySelector('#admin-count').textContent = cnt.textContent;
    if (idx) document.querySelector('#page-indicator').textContent = idx.textContent;
    const card = document.querySelector('#admin-card');
    if (card) {
        card.classList.remove('animate-fadeIn');
        void card.offsetWidth;
        card.classList.add('animate-fadeIn');
    }
}

window.editarAdmin = async function(id) {
    const res = await fetch(window.location.pathname + '?editar=' + encodeURIComponent(id));
    const data = await res.json();

    const m = data.Id_veterinario.match(/^([VE]-)(\d+)$/);
    if (m) {
        document.querySelector('select[name="cedula_prefix"]').value = m[1];
        document.querySelector('input[name="cedula"]').value = m[2];
    }

    const em = data.Gmail.match(/^(.+)@(.+)$/);
    if (em) {
        document.querySelector('input[name="email_user"]').value = em[1];
        const domain = '@' + em[2];
        const sel = document.getElementById('email-domain-select');
        const custom = document.getElementById('email-domain-custom');
        const arrow = document.getElementById('domain-arrow');
        const found = Array.from(sel.options).some(o => o.value === domain);
        if (found) {
            sel.value = domain;
            sel.style.display = 'block';
            arrow.style.display = 'block';
            custom.style.display = 'none';
            custom.value = '';
        } else {
            sel.value = 'otros';
            sel.style.display = 'none';
            arrow.style.display = 'none';
            custom.style.display = 'block';
            custom.value = em[2];
        }
    }

    document.querySelector('input[name="nombres"]').value = data.Nombres;
    document.querySelector('input[name="password"]').value = '';
    document.querySelector('input[name="password"]').placeholder = 'Dejar vacío para mantener';

    document.getElementById('editing_id').value = data.Id_veterinario;
    document.getElementById('btn-text').textContent = 'Guardar cambios';
    document.getElementById('btn-icon').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />';
    document.getElementById('btn-cancel-edit').classList.remove('hidden');

    return false;
};

window.cancelarEdicion = function() {
    document.getElementById('form-admin').reset();
    document.getElementById('editing_id').value = '';
    document.getElementById('btn-text').textContent = 'Guardar administrador';
    document.getElementById('btn-icon').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />';
    document.getElementById('btn-cancel-edit').classList.add('hidden');

    const sel = document.getElementById('email-domain-select');
    const custom = document.getElementById('email-domain-custom');
    const arrow = document.getElementById('domain-arrow');
    sel.value = '@gmail.com';
    sel.style.display = 'block';
    arrow.style.display = 'block';
    custom.style.display = 'none';
    custom.value = '';

    document.querySelector('input[name="password"]').placeholder = '••••••••';

    return false;
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

window.borrarAdmin = function(id, nombres) {
    window.mostrarConfirmacion('¿Estás seguro de borrar a "' + nombres + '"?', function() {
        fetch(window.location.pathname + '?delete=' + encodeURIComponent(id))
        .then(function() { if (typeof loadPage === 'function') loadPage(window.location.pathname); else location.reload(); });
    });
};
</script>