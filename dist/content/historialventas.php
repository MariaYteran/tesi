<div class="animate-fadeIn">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-green-800">Historial ventas</h1>
            <p class="text-gray-600">Busca tus ventas realizadas</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-green-100 mb-6">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-t-2xl px-5 py-4">
            <h2 class="text-white font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Buscar Ventas
            </h2>
        </div>
        <div class="p-5">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha Inicio</label>
                    <input type="date" id="fechaInicio"
                           class="px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha Fin</label>
                    <input type="date" id="fechaFin"
                           class="px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                </div>
                <div class="flex-1 min-w-[220px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Cédula del Cliente</label>
                    <div class="relative flex">
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
                               class="flex-1 px-4 py-3 border-2 border-green-200 rounded-r-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 transition-all"
                               autocomplete="off">
                        <div id="suggestionsCliente" class="absolute top-full left-0 right-0 bg-white border border-green-200 rounded-xl shadow-lg z-40 hidden max-h-[200px] overflow-y-auto mt-1"></div>
                    </div>
                </div>
                <button onclick="buscarHistorial()"
                        class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:from-green-700 hover:to-emerald-700 transition-all shadow-sm hover:shadow-md active:scale-[0.98] flex items-center justify-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Buscar
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h2 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Resultados
                    <span id="totalVentas" class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">0</span>
                </h2>
                <div id="paginaInfoHeader" class="text-sm text-gray-500 hidden"></div>
            </div>
        </div>

        <div id="emptyState" class="flex flex-col items-center py-16">
            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <p class="text-gray-400 text-sm font-medium">Selecciona filtros y busca ventas</p>
            <p class="text-gray-400 text-xs mt-1">Puedes filtrar por rango de fechas y/o cédula del cliente</p>
        </div>

        <div id="resultadosTable" class="hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-green-50">
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Fecha</th>
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Tipo</th>
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Cédula</th>
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Nombre</th>
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100 text-right">Total</th>
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaHistorial"></tbody>
                </table>
            </div>

            <div id="paginacion" class="hidden flex items-center justify-center gap-4 py-4 border-t border-gray-100">
                <button onclick="cambiarPagina('prev')" id="btnPrev"
                        class="p-2 rounded-lg text-gray-600 hover:bg-green-50 transition-all disabled:opacity-30 disabled:cursor-not-allowed" title="Anterior">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <span id="paginaEstado" class="text-sm font-medium text-gray-700">Página 1 de 1</span>
                <button onclick="cambiarPagina('next')" id="btnNext"
                        class="p-2 rounded-lg text-gray-600 hover:bg-green-50 transition-all disabled:opacity-30 disabled:cursor-not-allowed" title="Siguiente">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 19l7-7-7-7" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<div id="modalFacturaHistorial" class="fixed inset-0 bg-black/20 flex items-center justify-center z-50 hidden p-4 md:p-6">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-auto max-h-[85vh] flex flex-col">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5 flex items-center justify-between rounded-t-2xl flex-shrink-0">
            <h3 class="text-lg font-bold text-white flex items-center gap-2" id="modalFacturaTitulo">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Factura
            </h3>
            <button onclick="cerrarModalHistorial()" class="text-white/70 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="modalFacturaBody" class="overflow-y-auto p-6 space-y-4"></div>
        <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-3 justify-end flex-shrink-0">
            <button onclick="descargarPDFHistorial()" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:from-red-600 hover:to-red-700 transition-all shadow-md flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Descargar PDF
            </button>
            <button onclick="enviarCorreoHistorial()" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:from-blue-600 hover:to-blue-700 transition-all shadow-md flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Enviar por Correo
            </button>
            <button onclick="cerrarModalHistorial()" class="px-8 bg-gradient-to-r from-green-600 to-emerald-600 text-white py-2.5 rounded-xl text-sm font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md">
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
var paginaActual = 1;
var porPagina = 10;
var totalPaginas = 0;
var datosActuales = [];
var detalleActual = null;

function initHistorialVentas() {
    var input = document.getElementById('inputCedula');
    var sug = document.getElementById('suggestionsCliente');
    var prefijo = document.getElementById('selectPrefijoCedula');
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            buscarHistorial();
        }
    });
    input.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g,'');
        clearTimeout(this._timer);
        var q = this.value.trim();
        if (q.length < 1) { sug.classList.add('hidden'); return; }
        var busqueda = (prefijo.value || 'V-') + q;
        this._timer = setTimeout(function() {
            fetch('/dist/content/inicio_data.php?action=buscar_cliente_venta&q=' + encodeURIComponent(busqueda))
                .then(function(r) { return r.json(); })
                .then(function(datos) {
                    if (datos.length === 0) { sug.classList.add('hidden'); return; }
                    var html = '';
                    for (var i = 0; i < datos.length; i++) {
                        var d = datos[i];
                        html += '<div class="px-4 py-2.5 cursor-pointer hover:bg-green-50 border-b border-green-100 last:border-b-0 text-sm" data-cedula="' + d.cedula + '" onclick="seleccionarClienteHistorial(this)">';
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

function seleccionarClienteHistorial(el) {
    var cedula = el.dataset.cedula;
    var m = cedula.match(/^([VE]-)(\d+)$/i);
    if (m) {
        document.getElementById('selectPrefijoCedula').value = m[1].toUpperCase();
        document.getElementById('inputCedula').value = m[2];
    } else {
        document.getElementById('inputCedula').value = cedula;
    }
    document.getElementById('suggestionsCliente').classList.add('hidden');
}

function buscarHistorial() {
    var fechaInicio = document.getElementById('fechaInicio').value;
    var fechaFin = document.getElementById('fechaFin').value;
    var prefijo = document.getElementById('selectPrefijoCedula').value;
    var cedula = prefijo + document.getElementById('inputCedula').value;

    if (!document.getElementById('inputCedula').value.trim()) {
        alert('Ingresa una cédula para buscar.');
        return;
    }

    paginaActual = 1;
    fetchDatos(cedula, fechaInicio, fechaFin, paginaActual);
}

function fetchDatos(cedula, fechaInicio, fechaFin, pagina) {
    var url = '/dist/content/inicio_data.php?action=buscar_historial_ventas&cedula=' + encodeURIComponent(cedula)
            + '&pagina=' + pagina + '&por_pagina=' + porPagina;
    if (fechaInicio) url += '&fecha_inicio=' + encodeURIComponent(fechaInicio);
    if (fechaFin) url += '&fecha_fin=' + encodeURIComponent(fechaFin);

    fetch(url)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data.success) {
                alert(data.message || 'Error al buscar');
                return;
            }
            datosActuales = data.datos || [];
            totalPaginas = data.total_paginas || 1;
            document.getElementById('totalVentas').textContent = data.total || 0;

            if (datosActuales.length === 0) {
                document.getElementById('emptyState').classList.remove('hidden');
                document.getElementById('resultadosTable').classList.add('hidden');
                return;
            }
            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('resultadosTable').classList.remove('hidden');
            renderizarPagina(pagina);
        })
        .catch(function() {
            alert('Error de conexión');
        });
}

function renderizarPagina(pagina) {
    paginaActual = pagina;
    if (paginaActual < 1) paginaActual = 1;
    if (paginaActual > totalPaginas) paginaActual = totalPaginas;

    var inicio = (paginaActual - 1) * porPagina;
    var fin = Math.min(inicio + porPagina, datosActuales.length);
    var paginados = datosActuales.slice(inicio, fin);

    var tbody = document.getElementById('tablaHistorial');
    tbody.innerHTML = '';
    paginados.forEach(function(v) {
        var tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50 transition-colors';
        var tipoBadge = v.tipo === 'consulta'
            ? '<span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">Consulta</span>'
            : '<span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Venta</span>';
        tr.innerHTML = '<td class="p-4 border-b border-gray-100 text-gray-700 text-sm">' + formatearFecha(v.fecha) + '</td>' +
            '<td class="p-4 border-b border-gray-100">' + tipoBadge + '</td>' +
            '<td class="p-4 border-b border-gray-100 text-gray-700 text-sm">' + v.cedula + '</td>' +
            '<td class="p-4 border-b border-gray-100 text-gray-700 text-sm">' + (v.nombre || '') + ' ' + (v.apellidos || '') + '</td>' +
            '<td class="p-4 border-b border-gray-100 text-gray-700 text-sm text-right font-semibold text-green-700">$' + parseFloat(v.total).toFixed(2) + '</td>' +
            '<td class="p-4 border-b border-gray-100 text-center"><button onclick="abrirDetalle(\'' + v.id + '\',\'' + v.tipo + '\')" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-sm hover:shadow-md active:scale-[0.98]">Ver Factura</button></td>';
        tbody.appendChild(tr);
    });

    document.getElementById('paginaEstado').textContent = 'Página ' + paginaActual + ' de ' + totalPaginas;

    var pag = document.getElementById('paginacion');
    if (totalPaginas <= 1) {
        pag.classList.add('hidden');
    } else {
        pag.classList.remove('hidden');
        document.getElementById('btnPrev').disabled = paginaActual <= 1;
        document.getElementById('btnNext').disabled = paginaActual >= totalPaginas;
    }
}

function cambiarPagina(dir) {
    if (dir === 'prev' && paginaActual > 1) renderizarPagina(paginaActual - 1);
    if (dir === 'next' && paginaActual < totalPaginas) renderizarPagina(paginaActual + 1);
}

function abrirDetalle(id, tipo) {
    fetch('/dist/content/inicio_data.php?action=detalle_historial&id=' + id + '&tipo=' + tipo)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data.success) {
                alert(data.message || 'Error al cargar detalle');
                return;
            }
            detalleActual = data;
            renderizarModal(data);
        })
        .catch(function() {
            alert('Error de conexión');
        });
}

function renderizarModal(d) {
    document.getElementById('modalFacturaTitulo').textContent = 'FACTURA #' + d.id + ' (' + (d.tipo === 'consulta' ? 'Consulta' : 'Venta') + ')';

    var html = '';
    html += '<div class="bg-green-50 rounded-xl p-4 border border-green-100">';
    html += '<p class="text-sm"><span class="font-semibold text-gray-600">Cliente:</span> <span class="text-gray-800">' + (d.cliente.cedula || '') + '</span></p>';
    html += '<p class="text-sm"><span class="font-semibold text-gray-600">Nombre:</span> <span class="text-gray-800">' + (d.cliente.nombres || '') + ' ' + (d.cliente.apellidos || '') + '</span></p>';
    html += '<p class="text-sm"><span class="font-semibold text-gray-600">Teléfono:</span> <span class="text-gray-800">' + (d.cliente.telefono || '—') + '</span></p>';
    html += '<p class="text-sm"><span class="font-semibold text-gray-600">Fecha:</span> <span class="text-gray-800">' + formatearFecha(d.fecha) + '</span></p>';
    if (d.tipo_pago) {
        html += '<p class="text-sm"><span class="font-semibold text-gray-600">Pago:</span> <span class="text-gray-800 capitalize">' + d.tipo_pago.replace(/_/g, ' ') + '</span></p>';
    }
    html += '</div>';

    if (d.tipo === 'venta') {
        html += '<table class="w-full border-collapse"><tr><th class="text-left text-sm font-semibold text-green-700 bg-green-50 px-3 py-2 border border-green-100">Código</th><th class="text-left text-sm font-semibold text-green-700 bg-green-50 px-3 py-2 border border-green-100">Descripción</th><th class="text-center text-sm font-semibold text-green-700 bg-green-50 px-3 py-2 border border-green-100 w-16">Cant</th><th class="text-right text-sm font-semibold text-green-700 bg-green-50 px-3 py-2 border border-green-100 w-24">Precio</th><th class="text-right text-sm font-semibold text-green-700 bg-green-50 px-3 py-2 border border-green-100 w-24">Subtotal</th></tr>';
        d.items.forEach(function(it) {
            html += '<tr><td class="text-sm px-3 py-2 border border-gray-200">' + it.id_producto + '</td><td class="text-sm px-3 py-2 border border-gray-200">' + it.descripcion + '</td><td class="text-sm px-3 py-2 border border-gray-200 text-center">' + it.cantidad + '</td><td class="text-sm px-3 py-2 border border-gray-200 text-right">$' + parseFloat(it.precio_unitario).toFixed(2) + '</td><td class="text-sm px-3 py-2 border border-gray-200 text-right">$' + parseFloat(it.subtotal).toFixed(2) + '</td></tr>';
        });
        html += '<tr><td colspan="4" class="text-sm font-bold px-3 py-2 border border-green-200 bg-green-50 text-right">TOTAL</td><td class="text-sm font-bold px-3 py-2 border border-green-200 bg-green-50 text-right text-green-800">$' + parseFloat(d.total).toFixed(2) + '</td></tr>';
    } else {
        html += '<table class="w-full border-collapse"><tr><th class="text-left text-sm font-semibold text-green-700 bg-green-50 px-3 py-2 border border-green-100">Servicio</th><th class="text-right text-sm font-semibold text-green-700 bg-green-50 px-3 py-2 border border-green-100 w-32">Precio</th></tr>';
        d.items.forEach(function(it) {
            html += '<tr><td class="text-sm px-3 py-2 border border-gray-200">' + (it.servicio || '') + '</td><td class="text-sm px-3 py-2 border border-gray-200 text-right">$' + parseFloat(it.precio || 0).toFixed(2) + '</td></tr>';
        });
        html += '<tr><td class="text-sm font-bold px-3 py-2 border border-green-200 bg-green-50 text-right">TOTAL</td><td class="text-sm font-bold px-3 py-2 border border-green-200 bg-green-50 text-right text-green-800">$' + parseFloat(d.total).toFixed(2) + '</td></tr>';
    }
    if (d.total_bs && d.tasa_bcv) {
        var bsColspan = d.tipo === 'venta' ? 4 : 1;
        html += '<tr><td colspan="' + bsColspan + '" class="text-sm font-bold px-3 py-2 border border-green-200 bg-green-50 text-right">TOTAL EN BS</td><td class="text-sm font-bold px-3 py-2 border border-green-200 bg-green-50 text-right text-green-800">Bs ' + parseFloat(d.total_bs).toFixed(2).replace('.', ',') + '</td></tr>';
    }
    html += '</table>';

    document.getElementById('modalFacturaBody').innerHTML = html;
    document.getElementById('modalFacturaHistorial').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function cerrarModalHistorial() {
    document.getElementById('modalFacturaHistorial').classList.add('hidden');
    document.body.style.overflow = '';
    detalleActual = null;
}

function descargarPDFHistorial() {
    if (!detalleActual) return;
    var d = detalleActual;
    if (d.tipo === 'venta') {
        var fd = new FormData();
        fd.append('action', 'procesar_factura_venta');
        fd.append('id_venta', d.id);
        fd.append('tipo_pago', d.tipo_pago || '');
        fd.append('mode', 'descargar');
        fetch('/dist/content/inicio_data.php', { method: 'POST', body: fd })
            .then(function(r) { return r.blob(); })
            .then(function(blob) {
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = 'factura_' + d.id + '.pdf';
                document.body.appendChild(a);
                a.click();
                a.remove();
                URL.revokeObjectURL(url);
            });
    } else {
        var a = document.createElement('a');
        a.href = '/dist/content/inicio_data.php?action=pdf_factura&id=' + d.id;
        a.download = 'factura_' + d.id + '.pdf';
        document.body.appendChild(a);
        a.click();
        a.remove();
    }
}

function enviarCorreoHistorial() {
    if (!detalleActual) return;
    var d = detalleActual;
    var email = d.cliente.email;
    if (!email) {
        alert('El cliente no tiene un correo electrónico registrado.');
        return;
    }
    var btn = document.querySelector('#modalFacturaHistorial .px-6.py-4 button:nth-child(2)');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg> Enviando...';
    }
    var fd = new FormData();
    fd.append('action', 'enviar_factura_historial');
    fd.append('id', d.id);
    fd.append('tipo', d.tipo);
    fd.append('email', email);
    fetch('/dist/content/inicio_data.php', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            alert(data.message);
            if (btn) { btn.disabled = false; btn.innerHTML = 'Enviar por Correo'; }
        })
        .catch(function() {
            alert('Error de conexión al enviar el correo');
            if (btn) { btn.disabled = false; btn.innerHTML = 'Enviar por Correo'; }
        });
}

function formatearFecha(fecha) {
    if (!fecha) return '';
    var partes = fecha.split('-');
    return partes[2] + '/' + partes[1] + '/' + partes[0];
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') cerrarModalHistorial();
});

document.getElementById('modalFacturaHistorial').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalHistorial();
});

initHistorialVentas();
</script>