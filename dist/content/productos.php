<?php
session_start();
include '../bd.php';
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_proveedor = mysqli_real_escape_string($conexion, $_POST['id_proveedor']);
    $id_producto = mysqli_real_escape_string($conexion, $_POST['codigo']);
    $cantidad = mysqli_real_escape_string($conexion, $_POST['cantidad']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    $precio_costo = mysqli_real_escape_string($conexion, $_POST['precio_costo']);
    $precio_venta = mysqli_real_escape_string($conexion, $_POST['precio_venta']);
    $editing_id = mysqli_real_escape_string($conexion, $_POST['editing_id'] ?? '');

    if (empty($id_proveedor) || empty($id_producto) || empty($cantidad) || empty($descripcion) || empty($precio_costo) || empty($precio_venta)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (!empty($editing_id)) {
        try {
            $verificar = mysqli_query($conexion, "SELECT * FROM producto WHERE id_producto='$id_producto' AND id_producto != '$editing_id' AND RIF_clinica='$RIF_clinica'");
            if (mysqli_num_rows($verificar) > 0) {
                $error = "El código del producto ya existe.";
            } else {
                $update = mysqli_query($conexion, "UPDATE producto SET id_proveedor='$id_proveedor', id_producto='$id_producto', cantidad='$cantidad', descripcion='$descripcion', precio_costo='$precio_costo', precio_venta='$precio_venta' WHERE id_producto='$editing_id' AND RIF_clinica='$RIF_clinica'");
                if ($update) {
                    $success = "Producto actualizado exitosamente.";
                } else {
                    $error = "Error al actualizar: " . mysqli_error($conexion);
                }
            }
        } catch (mysqli_sql_exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        try {
            $verificar = mysqli_query($conexion, "SELECT * FROM producto WHERE id_producto='$id_producto' AND RIF_clinica='$RIF_clinica'");
            if (mysqli_num_rows($verificar) > 0) {
                $error = "El código del producto ya existe.";
            } else {
                $insertar = mysqli_query($conexion, "INSERT INTO producto (id_proveedor, id_producto, cantidad, descripcion, precio_costo, precio_venta, RIF_clinica) VALUES ('$id_proveedor', '$id_producto', '$cantidad', '$descripcion', '$precio_costo', '$precio_venta', '$RIF_clinica')");
                if ($insertar) {
                    $success = "Producto registrado exitosamente.";
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
    $q = mysqli_query($conexion, "SELECT * FROM producto WHERE id_producto='$id' AND RIF_clinica='$RIF_clinica'");
    if ($row = mysqli_fetch_assoc($q)) {
        header('Content-Type: application/json');
        echo json_encode($row);
        exit;
    }
}

if (isset($_GET['eliminar'])) {
    $id = mysqli_real_escape_string($conexion, $_GET['eliminar']);
    mysqli_query($conexion, "DELETE FROM producto WHERE id_producto='$id' AND RIF_clinica='$RIF_clinica'");
    exit();
}

$por_pagina = 4;
$pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$total_query = mysqli_query($conexion, "SELECT COUNT(*) as total FROM producto WHERE RIF_clinica='$RIF_clinica'");
$total = mysqli_fetch_assoc($total_query)['total'];
$total_paginas = max(1, ceil($total / $por_pagina));
$pagina = min($pagina, $total_paginas);
$inicio = ($pagina - 1) * $por_pagina;
$query_lista = mysqli_query($conexion, "SELECT p.*, pr.empresa FROM producto p LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor WHERE p.RIF_clinica='$RIF_clinica' ORDER BY p.descripcion ASC LIMIT $inicio, $por_pagina");

$query_proveedores = mysqli_query($conexion, "SELECT id_proveedor, empresa FROM proveedor WHERE RIF_clinica='$RIF_clinica' ORDER BY empresa ASC");
?>
<div class="animate-fadeIn">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-green-800">Productos</h1>
            <p class="text-gray-600">Registra tus productos</p>
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
                        Registrar Producto
                    </h2>
                </div>
                <form id="form-producto" class="p-5 space-y-4" action="" method="POST">
                    <input type="hidden" id="editing_id" name="editing_id" value="">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Proveedor</label>
                        <select name="id_proveedor" id="select-proveedor" required
                                class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 bg-white focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                            <option value="">Seleccione un proveedor</option>
                            <?php mysqli_data_seek($query_proveedores, 0); while ($prov = mysqli_fetch_assoc($query_proveedores)): ?>
                            <option value="<?php echo $prov['id_proveedor']; ?>"><?php echo $prov['id_proveedor'] . ' - ' . $prov['empresa']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Código del producto</label>
                        <input type="text" name="codigo" placeholder="PRO-001" maxlength="15"
                               class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Cantidad</label>
                        <input type="number" name="cantidad" placeholder="0" maxlength="4"
                               oninput="if(this.value.length>4)this.value=this.value.slice(0,4)"
                               class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción</label>
                        <input type="text" name="descripcion" placeholder="Nombre del producto" maxlength="25"
                               class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Precio Costo</label>
                        <input type="number" name="precio_costo" id="precio-costo" placeholder="0.00" step="0.01" min="0" maxlength="4"
                               oninput="if(this.value.length>4)this.value=this.value.slice(0,4)"
                               class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Precio Venta</label>
                        <input type="number" name="precio_venta" placeholder="0.00" step="0.01" min="0" maxlength="4"
                               oninput="if(this.value.length>4)this.value=this.value.slice(0,4)"
                               class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" id="btn-prod-submit"
                                class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl active:scale-[0.98] flex items-center justify-center gap-2">
                            <svg id="btn-prod-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            <span id="btn-prod-text">Guardar Producto</span>
                        </button>
                        <button type="button" id="btn-cancel-prod-edit" onclick="cancelarEdicionProducto()"
                                class="hidden px-4 py-3 rounded-xl font-semibold bg-gray-200 text-gray-700 hover:bg-gray-300 transition-all cursor-pointer">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div id="producto-card" class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Lista de Productos
                        <span id="producto-count" class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium"><?php echo $total; ?></span>
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-green-50">
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Cédula Proveedor</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Código</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Cantidad</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Descripción</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Precio Costo</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Precio Venta</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="producto-tbody">
                            <?php if ($total > 0): while ($row = mysqli_fetch_assoc($query_lista)): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm font-medium"><?php echo $row['id_proveedor']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm"><?php echo $row['id_producto']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm"><?php echo $row['cantidad']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm"><?php echo $row['descripcion']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm">$<?php echo $row['precio_costo']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm">$<?php echo $row['precio_venta']; ?></td>
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm">
                                    <div class="flex gap-2">
                                        <button onclick="editarProducto('<?php echo $row['id_producto']; ?>')"
                                                class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:from-green-700 hover:to-emerald-700 transition-all cursor-pointer">Editar</button>
                                        <button onclick="borrarProducto('<?php echo $row['id_producto']; ?>', '<?php echo $row['descripcion']; ?>')"
                                                class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-600 transition-all cursor-pointer">Borrar</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 border-b border-gray-100 text-gray-700 text-sm italic text-center" colspan="7">
                                    <div class="flex flex-col items-center py-8">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <p class="text-gray-400 text-sm font-medium">No hay productos registrados aún.</p>
                                        <p class="text-gray-400 text-xs mt-1">Utiliza el formulario para registrar un nuevo producto.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($total > 0): ?>
                <div id="producto-paginacion" class="flex items-center justify-between px-5 py-4 border-t border-gray-100">
                    <span class="text-sm text-gray-500">Mostrando <?php echo $inicio + 1; ?> - <?php echo min($inicio + $por_pagina, $total); ?> de <span id="producto-total"><?php echo $total; ?></span></span>
                    <div class="flex items-center gap-2">
                        <?php if ($pagina > 1): ?>
                        <a href="?pagina=<?php echo $pagina - 1; ?>" onclick="event.preventDefault(); cambiarPaginaProducto(<?php echo $pagina - 1; ?>)" class="px-3 py-1.5 rounded-lg text-sm font-medium text-green-700 hover:bg-green-50 border border-green-200 transition-colors">‹</a>
                        <?php endif; ?>
                        <?php for ($p = 1; $p <= $total_paginas; $p++): ?>
                        <a href="?pagina=<?php echo $p; ?>" onclick="event.preventDefault(); cambiarPaginaProducto(<?php echo $p; ?>)" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors border <?php echo $p === $pagina ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white border-green-600' : 'text-green-700 hover:bg-green-50 border-green-200'; ?>"><?php echo $p; ?></a>
                        <?php endfor; ?>
                        <?php if ($pagina < $total_paginas): ?>
                        <a href="?pagina=<?php echo $pagina + 1; ?>" onclick="event.preventDefault(); cambiarPaginaProducto(<?php echo $pagina + 1; ?>)" class="px-3 py-1.5 rounded-lg text-sm font-medium text-green-700 hover:bg-green-50 border border-green-200 transition-colors">›</a>
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
            <p id="modal-confirmar-msg" class="text-gray-600 text-sm mb-6">¿Estás seguro de borrar este producto?</p>
            <div class="flex gap-3">
                <button onclick="cerrarConfirmacion()" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl text-base font-semibold hover:bg-gray-300 transition-all cursor-pointer">Cancelar</button>
                <button onclick="ejecutarConfirmacion()" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl text-base font-semibold hover:from-green-700 hover:to-emerald-700 transition-all cursor-pointer">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('form-producto').addEventListener('submit', async function(e) {
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

async function cambiarPaginaProducto(p) {
    const res = await fetch(window.location.pathname + '?pagina=' + p);
    const html = await res.text();
    const d = document.createElement('div');
    d.innerHTML = html;
    const tbody = d.querySelector('#producto-tbody');
    const pag = d.querySelector('#producto-paginacion');
    const cnt = d.querySelector('#producto-count');
    const tot = d.querySelector('#producto-total');
    if (tbody) document.querySelector('#producto-tbody').replaceWith(tbody.cloneNode(true));
    if (pag) document.querySelector('#producto-paginacion').replaceWith(pag.cloneNode(true));
    if (cnt) document.querySelector('#producto-count').textContent = cnt.textContent;
    if (tot) document.querySelector('#producto-total').textContent = tot.textContent;
    const card = document.querySelector('#producto-card');
    if (card) {
        card.classList.remove('animate-fadeIn');
        void card.offsetWidth;
        card.classList.add('animate-fadeIn');
    }
}

window.editarProducto = async function(id) {
    const res = await fetch(window.location.pathname + '?editar=' + encodeURIComponent(id));
    const data = await res.json();

    document.getElementById('select-proveedor').value = data.id_proveedor;
    document.querySelector('input[name="codigo"]').value = data.id_producto;
    document.querySelector('input[name="cantidad"]').value = data.cantidad;
    document.querySelector('input[name="descripcion"]').value = data.descripcion;
    document.querySelector('input[name="precio_costo"]').value = data.precio_costo;

    document.querySelector('input[name="precio_venta"]').value = data.precio_venta;

    document.getElementById('editing_id').value = data.id_producto;
    document.getElementById('btn-prod-text').textContent = 'Guardar cambios';
    document.getElementById('btn-prod-icon').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />';
    document.getElementById('btn-cancel-prod-edit').classList.remove('hidden');
};

window.cancelarEdicionProducto = function() {
    document.getElementById('form-producto').reset();
    document.getElementById('editing_id').value = '';
    document.getElementById('btn-prod-text').textContent = 'Guardar Producto';
    document.getElementById('btn-prod-icon').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />';
    document.getElementById('btn-cancel-prod-edit').classList.add('hidden');
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

window.borrarProducto = function(id, descripcion) {
    window.mostrarConfirmacion('¿Estás seguro de borrar "' + descripcion + '"?', function() {
        fetch(window.location.pathname + '?delete=' + encodeURIComponent(id))
        .then(function() { if (typeof loadPage === 'function') loadPage(window.location.pathname); else location.reload(); });
    });
};
</script>
