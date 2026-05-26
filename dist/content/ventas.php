<div class="animate-fadeIn">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-green-800">Ventas</h1>
            <p class="text-gray-600">Realiza las ventas a los clientes</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-5 py-4">
            <h2 class="text-white font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Nueva Venta
            </h2>
        </div>

        <div class="p-5 space-y-4">
            <div class="flex items-center gap-3">
                <label class="text-base font-semibold text-green-800 whitespace-nowrap">Cédula del Cliente</label>
                <div class="relative flex-1 flex">
                    <div class="relative rounded-l-xl border-2 border-r-0 border-green-200 bg-gradient-to-br from-green-600 to-emerald-600">
                        <select id="selectPrefijoCedula" class="appearance-none bg-transparent text-white font-bold pl-4 pr-8 py-3 cursor-pointer outline-none">
                            <option value="V-" class="text-gray-800 bg-white">V-</option>
                            <option value="E-" class="text-gray-800 bg-white">E-</option>
                        </select>
                        <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </div>
                    <input type="text" id="inputCedula" placeholder="12345678" maxlength="15"
                           oninput="this.value=this.value.replace(/\D/g,'')"
                           class="flex-1 px-4 py-3 border-2 border-green-200 rounded-r-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 transition-all"
                           autocomplete="off">
                    <div id="suggestionsCliente" class="absolute top-full left-0 right-0 bg-white border border-green-200 rounded-xl shadow-lg z-40 hidden max-h-[200px] overflow-y-auto mt-1"></div>
                </div>
            </div>

            <div id="datosCliente" class="hidden p-4 bg-green-50 rounded-xl border border-green-200 space-y-1.5">
                <p class="text-gray-700"><span class="font-semibold text-green-800">Cédula:</span> <span id="infoCedula">—</span></p>
                <p class="text-gray-700"><span class="font-semibold text-green-800">Nombres:</span> <span id="infoNombres">—</span></p>
                <p class="text-gray-700"><span class="font-semibold text-green-800">Apellidos:</span> <span id="infoApellidos">—</span></p>
                <p class="text-gray-700"><span class="font-semibold text-green-800">Correo:</span> <span id="infoCorreo">—</span></p>
                <p class="text-gray-700"><span class="font-semibold text-green-800">Teléfono:</span> <span id="infoTelefono">—</span></p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-green-50">
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Código</th>
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Descripción</th>
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Cantidad</th>
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Precio</th>
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100 text-right">Subtotal</th>
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="tablaVenta"></tbody>
                </table>
            </div>

            <div class="flex items-center gap-3 border-t border-gray-100 pt-3">
                <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <input type="text" id="inputAgregarProducto" placeholder="Agregar producto por código (ej: PRO-001) y presiona Enter..."
                       class="flex-1 px-4 py-3 text-sm border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
            </div>

            <div class="grid grid-cols-6 gap-4 items-center border-t border-gray-100 pt-3">
                <div class="col-span-4"></div>
                <div class="col-span-1 text-right">
                    <span class="text-lg font-bold text-green-800">Total: $<span id="totalVenta">0.00</span></span>
                </div>
                <div class="col-span-1"></div>
            </div>

            <div class="flex justify-center pt-2">
                <button onclick="generarFactura()"
                        class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-10 py-3.5 rounded-xl font-semibold tracking-wide hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl active:scale-[0.98] flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Generar Factura
                </button>
            </div>
        </div>
    </div>
</div>

<div id="modalFacturaVenta" class="fixed inset-0 bg-black/20 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-xl w-[600px] mx-4 overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5 flex items-center justify-between">
            <h3 class="text-xl font-bold text-white">Factura</h3>
            <button onclick="cerrarModalVenta()" class="text-white/80 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="facturaVentaBody" class="px-6 py-5 max-h-[70vh] overflow-y-auto"></div>
        <div class="px-6 py-3 border-t border-green-100">
            <label class="block text-sm font-semibold text-green-800 mb-2">Tipo de pago</label>
            <select id="selectTipoPago" class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 bg-white focus:border-green-500 transition-all text-sm">
                <option value="">Seleccionar tipo de pago</option>
                <option value="efectivo_bs">Efectivo Bs</option>
                <option value="efectivo_usd">Efectivo $</option>
                <option value="pago movil">Pago Móvil</option>
                <option value="punto">Punto</option>
            </select>
        </div>
        <div class="px-6 py-4 border-t border-green-100 flex items-center gap-3 justify-end">
            <button onclick="descargarPDFVenta()" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-3 rounded-xl text-sm font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Descargar PDF
            </button>
            <button onclick="enviarFacturaVenta(this)" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-3 rounded-xl text-sm font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Enviar por correo
            </button>
            <button onclick="guardarVenta()" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-xl text-sm font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-md flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                Guardar
            </button>
        </div>
    </div>
</div>

<div id="modalAdvertenciaPago" class="fixed inset-0 bg-black/20 flex items-center justify-center z-[60] hidden">
    <div class="bg-white rounded-2xl shadow-xl w-[380px] mx-4 overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-5 flex items-center gap-3">
            <svg class="w-7 h-7 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <h3 class="text-xl font-bold text-white">Advertencia</h3>
        </div>
        <div class="px-6 py-5">
            <p class="text-gray-700 text-center text-base">Debes seleccionar un tipo de pago primero.</p>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-center">
            <button onclick="cerrarAdvertenciaPago()" class="bg-gradient-to-r from-amber-500 to-orange-500 text-white px-8 py-2.5 rounded-xl font-semibold hover:from-amber-600 hover:to-orange-600 transition-all">
                Aceptar
            </button>
        </div>
    </div>
</div>

<script>
var datosClienteActual = null;
var idVentaActual = null;
var itemsVenta = [];
var tasaBcv = 0;
var totalBs = 0;

function initVentas() {
    hacerTypeaheadCliente();
    hacerTypeaheadProducto();
    var inp = document.getElementById('inputAgregarProducto');
    if (inp) {
        inp.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                var codigo = e.target.value.trim();
                if (codigo) {
                    agregarFila(codigo);
                    e.target.value = '';
                    document.getElementById('suggestionsProducto').classList.add('hidden');
                }
            }
        });
    }
}

function hacerTypeaheadCliente() {
    var input = document.getElementById('inputCedula');
    var sug = document.getElementById('suggestionsCliente');
    var prefijo = document.getElementById('selectPrefijoCedula');
    if (!input || !sug) return;
    input.addEventListener('input', function() {
        clearTimeout(this._timer);
        var q = this.value.trim();
        if (q.length < 1) { sug.classList.add('hidden'); return; }
        var busqueda = (prefijo ? prefijo.value : 'V-') + q;
        this._timer = setTimeout(function() {
            fetch('/dist/content/inicio_data.php?action=buscar_cliente_venta&q=' + encodeURIComponent(busqueda))
                .then(function(r) { return r.json(); })
                .then(function(datos) {
                    if (datos.length === 0) { sug.classList.add('hidden'); return; }
                    var html = '';
                    for (var i = 0; i < datos.length; i++) {
                        var d = datos[i];
                        html += '<div class="px-4 py-2.5 cursor-pointer hover:bg-green-50 border-b border-green-100 last:border-b-0 text-sm" data-cedula="' + d.cedula + '" data-nombres="' + (d.nombres || '') + '" data-apellidos="' + (d.apellidos || '') + '" data-telefono="' + (d.telefono || '') + '" data-email="' + (d.email || '') + '" data-tipo="' + (d.tipo || '') + '" onclick="seleccionarCliente(this)">';
                        html += '<span class="font-semibold text-green-800">' + d.cedula + '</span>';
                        html += ' <span class="text-gray-600">' + (d.nombres || '') + ' ' + (d.apellidos || '') + '</span>';
                        html += ' <span class="text-xs text-gray-400">(' + (d.tipo || '') + ')</span>';
                        html += '</div>';
                    }
                    sug.innerHTML = html;
                    sug.classList.remove('hidden');
                })
                .catch(function() { sug.classList.add('hidden'); });
        }, 200);
    });
    input.addEventListener('blur', function() { setTimeout(function() { sug.classList.add('hidden'); }, 300); });
    input.addEventListener('focus', function() { if (this.value.trim().length >= 1) { var evt = new Event('input'); this.dispatchEvent(evt); } });
}

function seleccionarCliente(el) {
    var cedula = el.dataset.cedula;
    var nombres = el.dataset.nombres;
    var apellidos = el.dataset.apellidos;
    var telefono = el.dataset.telefono;
    var email = el.dataset.email;
    var tipo = el.dataset.tipo;
    var m = cedula.match(/^([VE]-)(\d+)$/i);
    if (m) {
        document.getElementById('selectPrefijoCedula').value = m[1].toUpperCase();
        document.getElementById('inputCedula').value = m[2];
    } else {
        document.getElementById('inputCedula').value = cedula;
    }
    document.getElementById('infoCedula').textContent = cedula;
    document.getElementById('infoNombres').textContent = nombres;
    document.getElementById('infoApellidos').textContent = apellidos;
    document.getElementById('infoCorreo').textContent = email || '—';
    document.getElementById('infoTelefono').textContent = telefono || '—';
    document.getElementById('datosCliente').classList.remove('hidden');
    document.getElementById('suggestionsCliente').classList.add('hidden');
    datosClienteActual = { cedula: cedula, nombres: nombres, apellidos: apellidos, telefono: telefono, email: email, tipo: tipo };
}

function hacerTypeaheadProducto() {
    var input = document.getElementById('inputAgregarProducto');
    if (!input) return;
    var sug = document.createElement('div');
    sug.id = 'suggestionsProducto';
    sug.className = 'absolute top-full left-0 right-0 bg-white border border-green-200 rounded-xl shadow-lg z-40 hidden max-h-[200px] overflow-y-auto mt-1';
    input.parentNode.style.position = 'relative';
    input.parentNode.appendChild(sug);
    input.addEventListener('input', function() {
        clearTimeout(this._timer);
        var q = this.value.trim();
        if (q.length < 1) { sug.classList.add('hidden'); return; }
        this._timer = setTimeout(function() {
            fetch('/dist/content/inicio_data.php?action=buscar_producto_venta&q=' + encodeURIComponent(q))
                .then(function(r) { return r.json(); })
                .then(function(datos) {
                    if (datos.length === 0) { sug.classList.add('hidden'); return; }
                    var html = '';
                    for (var i = 0; i < datos.length; i++) {
                        var d = datos[i];
                        html += '<div class="px-4 py-2.5 cursor-pointer hover:bg-green-50 border-b border-green-100 last:border-b-0 text-sm" data-codigo="' + d.id_producto + '" data-desc="' + (d.descripcion || '') + '" data-precio="' + (d.precio_venta || 0) + '" onclick="seleccionarProducto(this)">';
                        html += '<span class="font-semibold text-green-800">' + d.id_producto + '</span>';
                        html += ' <span class="text-gray-600">' + (d.descripcion || '') + '</span>';
                        html += ' <span class="text-xs text-gray-400">$' + parseFloat(d.precio_venta || 0).toFixed(2) + ' | Stock: ' + (d.stock || 0) + '</span>';
                        html += '</div>';
                    }
                    sug.innerHTML = html;
                    sug.classList.remove('hidden');
                })
                .catch(function() { sug.classList.add('hidden'); });
        }, 200);
    });
    input.addEventListener('blur', function() { setTimeout(function() { sug.classList.add('hidden'); }, 300); });
}

function seleccionarProducto(el) {
    var codigo = el.dataset.codigo;
    agregarFila(codigo);
    document.getElementById('inputAgregarProducto').value = '';
    document.getElementById('suggestionsProducto').classList.add('hidden');
}

function agregarFila(codigo) {
    var tbody = document.getElementById('tablaVenta');
    var tr = document.createElement('tr');
    tr.className = 'hover:bg-gray-50 transition-colors fila-venta';
    tr.innerHTML = '<td class="p-4 border-b border-gray-100"><input type="text" class="input-codigo w-full px-0 py-2 text-sm bg-transparent outline-none border-0" placeholder="PRO-001" value="' + codigo + '"></td>' +
        '<td class="p-4 border-b border-gray-100"><span class="desc-cell text-gray-700 text-sm">—</span></td>' +
        '<td class="p-4 border-b border-gray-100"><input type="number" class="input-cantidad w-24 px-0 py-2 text-sm bg-transparent outline-none border-0" min="1" value="1"></td>' +
        '<td class="p-4 border-b border-gray-100"><span class="precio-cell text-gray-700 text-sm font-medium">$0.00</span></td>' +
        '<td class="p-4 border-b border-gray-100 text-right"><span class="subtotal-cell text-gray-700 text-sm font-medium">$0.00</span></td>' +
        '<td class="p-4 border-b border-gray-100 text-center"><button onclick="eliminarFila(this)" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-all" title="Eliminar"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button></td>';

    var inputCodigo = tr.querySelector('.input-codigo');
    var inputCantidad = tr.querySelector('.input-cantidad');
    inputCodigo.addEventListener('change', function() { buscarProducto(inputCodigo); });
    inputCantidad.addEventListener('input', function() { calcularFila(inputCantidad); });
    tbody.appendChild(tr);
    if (codigo) {
        buscarProducto(inputCodigo);
    }
    inputCodigo.focus();
    inputCodigo.select();
}

function buscarProducto(input) {
    var tr = input.closest('tr');
    var codigo = input.value.trim();
    if (!codigo) return;
    fetch('/dist/content/inicio_data.php?action=buscar_producto_venta&q=' + encodeURIComponent(codigo))
        .then(function(r) { return r.json(); })
        .then(function(datos) {
            var descCell = tr.querySelector('.desc-cell');
            var precioCell = tr.querySelector('.precio-cell');
            if (datos.length > 0 && datos[0].id_producto === codigo) {
                descCell.textContent = datos[0].descripcion;
                precioCell.textContent = '$' + parseFloat(datos[0].precio_venta || 0).toFixed(2);
            } else {
                descCell.textContent = '—';
                precioCell.textContent = '$0.00';
            }
            calcularFila(tr.querySelector('.input-cantidad'));
        });
}

function calcularFila(input) {
    var tr = input.closest('tr');
    var cantidad = parseInt(input.value) || 0;
    var precioTexto = tr.querySelector('.precio-cell').textContent.replace('$', '');
    var precio = parseFloat(precioTexto) || 0;
    var subtotal = cantidad * precio;
    tr.querySelector('.subtotal-cell').textContent = '$' + subtotal.toFixed(2);
    actualizarTotal();
}

function actualizarTotal() {
    var subtotales = document.querySelectorAll('.subtotal-cell');
    var total = 0;
    subtotales.forEach(function(el) {
        total += parseFloat(el.textContent.replace('$', '')) || 0;
    });
    document.getElementById('totalVenta').textContent = total.toFixed(2);
}

function eliminarFila(btn) {
    btn.closest('tr').remove();
    actualizarTotal();
}

function generarFactura() {
    if (!datosClienteActual) {
        alert('Ingresa y selecciona un cliente.');
        return;
    }
    var filas = document.querySelectorAll('.fila-venta');
    if (filas.length === 0) {
        alert('Agrega al menos un producto a la venta.');
        return;
    }
    var items = [];
    filas.forEach(function(tr) {
        var codigo = tr.querySelector('.input-codigo').value.trim();
        var cantidad = parseInt(tr.querySelector('.input-cantidad').value) || 0;
        var desc = tr.querySelector('.desc-cell').textContent;
        var precio = parseFloat(tr.querySelector('.precio-cell').textContent.replace('$', '')) || 0;
        if (codigo && cantidad > 0 && precio > 0) {
            items.push({
                id_producto: codigo,
                descripcion: desc,
                cantidad: cantidad,
                precio_unitario: precio,
                subtotal: cantidad * precio
            });
        }
    });
    if (items.length === 0) {
        alert('Los productos deben tener código, cantidad y precio válidos.');
        return;
    }
    var hoy = new Date().toISOString().split('T')[0];
    var fd = new FormData();
    fd.append('action', 'guardar_venta_normal');
    fd.append('fecha', hoy);
    fd.append('id_cliente', datosClienteActual.cedula);
    fd.append('nombres_cliente', datosClienteActual.nombres);
    fd.append('apellidos_cliente', datosClienteActual.apellidos);
    fd.append('telefono_cliente', datosClienteActual.telefono);
    fd.append('email_cliente', datosClienteActual.email);
    fd.append('items', JSON.stringify(items));

    fetch('/dist/content/inicio_data.php', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                idVentaActual = data.id_venta;
                itemsVenta = items;
                tasaBcv = data.tasa_bcv || 0;
                totalBs = data.total_bs || 0;
                abrirModalFactura();
            } else {
                alert('Error: ' + (data.message || 'No se pudo guardar la venta'));
            }
        })
        .catch(function() {
            alert('Error de conexión al guardar la venta');
        });
}

function abrirModalFactura() {
    var body = document.getElementById('facturaVentaBody');
    var total = 0;
    itemsVenta.forEach(function(it) { total += it.subtotal; });
    var html = '';
    html += '<div class="mb-4"><h4 class="font-bold text-green-800 text-lg text-center">FACTURA #' + idVentaActual + '</h4></div>';
    html += '<div class="bg-green-50 rounded-xl p-4 mb-4 border border-green-100">';
    html += '<p class="text-sm"><span class="font-semibold text-gray-600">Cliente:</span> <span class="text-gray-800">' + datosClienteActual.cedula + '</span></p>';
    html += '<p class="text-sm"><span class="font-semibold text-gray-600">Nombre:</span> <span class="text-gray-800">' + datosClienteActual.nombres + ' ' + datosClienteActual.apellidos + '</span></p>';
    html += '<p class="text-sm"><span class="font-semibold text-gray-600">Teléfono:</span> <span class="text-gray-800">' + (datosClienteActual.telefono || '—') + '</span></p>';
    if (datosClienteActual.email) {
        html += '<p class="text-sm"><span class="font-semibold text-gray-600">Correo:</span> <span class="text-gray-800">' + datosClienteActual.email + '</span></p>';
    }
    html += '<p class="text-sm"><span class="font-semibold text-gray-600">Tipo:</span> <span class="text-gray-800">' + (datosClienteActual.tipo || '—') + '</span></p>';
    html += '</div>';
    html += '<table class="w-full border-collapse mb-4">';
    html += '<tr><th class="text-left text-sm font-semibold text-green-700 bg-green-50 px-3 py-2 border border-green-100">Código</th><th class="text-left text-sm font-semibold text-green-700 bg-green-50 px-3 py-2 border border-green-100">Descripción</th><th class="text-center text-sm font-semibold text-green-700 bg-green-50 px-3 py-2 border border-green-100 w-16">Cant</th><th class="text-right text-sm font-semibold text-green-700 bg-green-50 px-3 py-2 border border-green-100 w-24">Precio</th><th class="text-right text-sm font-semibold text-green-700 bg-green-50 px-3 py-2 border border-green-100 w-24">Subtotal</th></tr>';
    itemsVenta.forEach(function(it) {
        html += '<tr><td class="text-sm px-3 py-2 border border-gray-200">' + it.id_producto + '</td><td class="text-sm px-3 py-2 border border-gray-200">' + it.descripcion + '</td><td class="text-sm px-3 py-2 border border-gray-200 text-center">' + it.cantidad + '</td><td class="text-sm px-3 py-2 border border-gray-200 text-right">$' + it.precio_unitario.toFixed(2) + '</td><td class="text-sm px-3 py-2 border border-gray-200 text-right">$' + it.subtotal.toFixed(2) + '</td></tr>';
    });
    html += '<tr><td colspan="4" class="text-sm font-bold px-3 py-2 border border-green-200 bg-green-50 text-right">TOTAL</td><td class="text-sm font-bold px-3 py-2 border border-green-200 bg-green-50 text-right text-green-800">$' + total.toFixed(2) + '</td></tr>';
    if (totalBs > 0) {
        html += '<tr><td colspan="4" class="text-sm font-bold px-3 py-2 border border-green-200 bg-green-50 text-right">TOTAL EN BS</td><td class="text-sm font-bold px-3 py-2 border border-green-200 bg-green-50 text-right text-green-800">Bs ' + totalBs.toFixed(2).replace('.', ',') + '</td></tr>';
    }
    html += '</table>';
    body.innerHTML = html;
    document.getElementById('selectTipoPago').value = '';
    document.getElementById('modalFacturaVenta').classList.remove('hidden');
}

function descargarPDFVenta() {
    if (!idVentaActual) return;
    var tipoPago = document.getElementById('selectTipoPago').value;
    if (!tipoPago) { mostrarAdvertenciaPago(); return; }
    var fd = new FormData();
    fd.append('action', 'procesar_factura_venta');
    fd.append('id_venta', idVentaActual);
    fd.append('tipo_pago', tipoPago);
    fd.append('mode', 'descargar');
    fetch('/dist/content/inicio_data.php', { method: 'POST', body: fd })
        .then(function(r) { return r.blob(); })
        .then(function(blob) {
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'factura_' + idVentaActual + '.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(url);
        });
}

function enviarFacturaVenta(btn) {
    if (!idVentaActual) return;
    var tipoPago = document.getElementById('selectTipoPago').value;
    if (!tipoPago) { mostrarAdvertenciaPago(); return; }
    if (!btn) btn = document.querySelector('#modalFacturaVenta button:nth-child(3)');
    btn.disabled = true;
    var originalHTML = btn.innerHTML;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg> Enviando...';
    var fd = new FormData();
    fd.append('action', 'procesar_factura_venta');
    fd.append('id_venta', idVentaActual);
    fd.append('tipo_pago', tipoPago);
    fd.append('mode', 'enviar');
    fetch('/dist/content/inicio_data.php', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Enviado';
                setTimeout(function() { cerrarModalVenta(); }, 1500);
            } else {
                alert('Error: ' + (data.message || 'No se pudo enviar'));
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        })
        .catch(function() {
            alert('Error de conexion al enviar el correo');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        });
}

function guardarVenta() {
    if (!idVentaActual) return;
    var tipoPago = document.getElementById('selectTipoPago').value;
    if (!tipoPago) { mostrarAdvertenciaPago(); return; }
    var fd = new FormData();
    fd.append('action', 'procesar_factura_venta');
    fd.append('id_venta', idVentaActual);
    fd.append('tipo_pago', tipoPago);
    fd.append('mode', 'guardar');
    fetch('/dist/content/inicio_data.php', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                cerrarModalVenta();
            } else {
                alert('Error: ' + (data.message || 'No se pudo guardar'));
            }
        })
        .catch(function() {
            alert('Error de conexion al guardar');
        });
}

function mostrarAdvertenciaPago() {
    document.getElementById('modalAdvertenciaPago').classList.remove('hidden');
}
function cerrarAdvertenciaPago() {
    document.getElementById('modalAdvertenciaPago').classList.add('hidden');
}

function cerrarModalVenta() {
    document.getElementById('modalFacturaVenta').classList.add('hidden');
    document.getElementById('inputCedula').value = '';
    document.getElementById('selectPrefijoCedula').value = 'V-';
    document.getElementById('datosCliente').classList.add('hidden');
    document.getElementById('tablaVenta').innerHTML = '';
    document.getElementById('totalVenta').textContent = '0.00';
    document.getElementById('selectTipoPago').value = '';
    datosClienteActual = null;
    idVentaActual = null;
    itemsVenta = [];
}

initVentas();
</script>
