<div class="animate-fadeIn">
    <div class="flex items-center gap-4 mb-6">
        <a href="/dist/content/historia.php" data-link
           class="inline-flex items-center gap-2 text-sm font-medium text-white bg-gradient-to-r from-green-600 to-emerald-600 px-4 py-2 rounded-xl shadow-sm hover:shadow-md hover:from-green-700 hover:to-emerald-700 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Volver
        </a>
        <h1 class="text-3xl font-bold text-green-800" id="hist-paciente-title">Historia del Paciente</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
            <h2 class="text-white font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Información del Paciente
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-5 gap-x-10">
                <div class="space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-green-100">
                        <span class="text-sm text-gray-500 font-medium">Cédula</span>
                        <p class="text-gray-800 font-semibold" id="info-cedula">-</p>
                    </div>
                    <div class="flex items-center justify-between pb-2 border-b border-green-100">
                        <span class="text-sm text-gray-500 font-medium">Nombre</span>
                        <p class="text-gray-800 font-semibold" id="info-nombre">-</p>
                    </div>
                    <div class="flex items-center justify-between pb-2 border-b border-green-100">
                        <span class="text-sm text-gray-500 font-medium">Edad</span>
                        <p class="text-gray-800 font-semibold" id="info-edad">-</p>
                    </div>
                    <div class="flex items-center justify-between pb-2 border-b border-green-100">
                        <span class="text-sm text-gray-500 font-medium">Sexo</span>
                        <p class="text-gray-800 font-semibold" id="info-sexo">-</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-green-100">
                        <span class="text-sm text-gray-500 font-medium">Especie</span>
                        <p class="text-gray-800 font-semibold" id="info-especie">-</p>
                    </div>
                    <div class="flex items-center justify-between pb-2 border-b border-green-100">
                        <span class="text-sm text-gray-500 font-medium">Raza</span>
                        <p class="text-gray-800 font-semibold" id="info-raza">-</p>
                    </div>
                    <div class="flex items-center justify-between pb-2 border-b border-green-100">
                        <span class="text-sm text-gray-500 font-medium">Peso</span>
                        <p class="text-gray-800 font-semibold" id="info-peso">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
            <h2 class="text-white font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Consultas
            </h2>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <input type="date" id="filter-fecha" onchange="filtrarConsultas()"
                       class="w-full sm:w-64 px-4 py-2.5 border-2 border-green-200 rounded-xl outline-none text-gray-700 bg-white focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
            </div>
            <div id="consultas-lista" class="space-y-3"></div>
            <div id="consultas-empty" class="hidden text-center py-10">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-400 text-sm font-medium">No hay consultas registradas para este paciente.</p>
            </div>
        </div>
    </div>
</div>

<div id="modal-consulta" class="fixed inset-0 bg-black/20 flex items-center justify-center z-50 hidden p-4 md:p-6">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-auto max-h-[85vh] flex flex-col">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5 flex items-center justify-between rounded-t-2xl flex-shrink-0">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Detalle de Consulta
            </h3>
            <button onclick="cerrarModalConsulta()" class="text-white/70 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="overflow-y-auto p-6 space-y-6" id="modal-consulta-body">
            <div class="bg-green-50/80 rounded-xl p-4 border border-green-100 flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <div>
                    <span class="text-xs text-gray-500 uppercase tracking-wider font-medium">Fecha</span>
                    <p class="text-gray-800 font-semibold" id="modal-fecha">-</p>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-green-800 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Diagnóstico
                </h4>
                <div class="bg-green-50/50 rounded-xl p-4 border border-green-100">
                    <p class="text-gray-700 text-sm leading-relaxed" id="modal-diagnostico">-</p>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-green-800 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Examen Físico
                </h4>
                <div class="bg-green-50/50 rounded-xl p-4 border border-green-100">
                    <p class="text-gray-700 text-sm leading-relaxed" id="modal-examen">-</p>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-green-800 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Tests Rápidos
                </h4>
                <div class="flex flex-wrap gap-2" id="modal-tests"></div>
            </div>

            <div>
                <h4 class="font-bold text-green-800 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    Laboratorio
                </h4>
                <div class="flex flex-wrap gap-2 mb-3" id="modal-laboratorio"></div>
                <div>
                    <h5 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Observaciones</h5>
                    <div class="bg-green-50/50 rounded-xl p-4 border border-green-100">
                        <p class="text-gray-700 text-sm leading-relaxed" id="modal-lab-obs">-</p>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-green-800 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Vacunas
                </h4>
                <div class="flex flex-wrap gap-2 mb-3" id="modal-vacunas"></div>
                <div>
                    <h5 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Observaciones</h5>
                    <div class="bg-green-50/50 rounded-xl p-4 border border-green-100">
                        <p class="text-gray-700 text-sm leading-relaxed" id="modal-vac-obs">-</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2">
                <button onclick="enviarCorreo(this)"
                        class="px-5 bg-gradient-to-r from-blue-500 to-blue-600 text-white py-2.5 rounded-xl text-sm font-semibold hover:from-blue-600 hover:to-blue-700 transition-all shadow-md flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    Enviar por Correo
                </button>
                <button onclick="descargarPDF()"
                        class="px-5 bg-gradient-to-r from-red-500 to-red-600 text-white py-2.5 rounded-xl text-sm font-semibold hover:from-red-600 hover:to-red-700 transition-all shadow-md flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                    Descargar PDF
                </button>
            </div>
            <button onclick="cerrarModalConsulta()"
                    class="px-8 bg-gradient-to-r from-green-600 to-emerald-600 text-white py-2.5 rounded-xl text-sm font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md">
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- Modal Emergencia -->
<div id="modal-emergencia" class="fixed inset-0 bg-black/20 flex items-center justify-center z-50 hidden p-4 md:p-6">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-auto max-h-[85vh] flex flex-col">
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-5 flex items-center justify-between rounded-t-2xl flex-shrink-0">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                Detalle de Emergencia
            </h3>
            <button onclick="cerrarModalEmergencia()" class="text-white/70 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="overflow-y-auto p-6 space-y-6" id="modal-emergencia-body">
            <div class="bg-red-50/80 rounded-xl p-4 border border-red-100 flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <div>
                    <span class="text-xs text-gray-500 uppercase tracking-wider font-medium">Fecha</span>
                    <p class="text-gray-800 font-semibold" id="emergencia-fecha">-</p>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-red-800 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Diagnóstico
                </h4>
                <div class="bg-red-50/50 rounded-xl p-4 border border-red-100">
                    <p class="text-gray-700 text-sm leading-relaxed" id="emergencia-diagnostico">-</p>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-red-800 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Veterinario
                </h4>
                <div class="bg-red-50/50 rounded-xl p-4 border border-red-100">
                    <p class="text-gray-700 text-sm leading-relaxed" id="emergencia-veterinario">-</p>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between flex-shrink-0">
            <button onclick="descargarPDFEmergencia()"
                    class="px-5 bg-gradient-to-r from-red-500 to-red-600 text-white py-2.5 rounded-xl text-sm font-semibold hover:from-red-600 hover:to-red-700 transition-all shadow-md flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                Descargar PDF
            </button>
            <button onclick="cerrarModalEmergencia()"
                    class="px-8 bg-gradient-to-r from-red-500 to-red-600 text-white py-2.5 rounded-xl text-sm font-semibold hover:from-red-600 hover:to-red-700 transition-all shadow-md">
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    var pacienteId = null;
    var consultasCache = [];

    function getParam(name) {
        var url = window.location.search;
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
        var results = regex.exec(url);
        return results ? decodeURIComponent(results[2] || '') : null;
    }

    function formatDate(fecha) {
        var partes = fecha.split('-');
        return partes[2] + '/' + partes[1] + '/' + partes[0];
    }

    function init() {
        pacienteId = getParam('paciente');
        if (!pacienteId) { pacienteId = 'M-001'; }

        fetch('/dist/content/inicio_data.php?action=datos_mascota&id=' + encodeURIComponent(pacienteId))
            .then(function(r) { return r.json(); })
            .then(function(p) {
                if (!p || !p.id_mascota) return;
                document.getElementById('hist-paciente-title').textContent = 'Historia de ' + p.nombre;
                document.getElementById('info-cedula').textContent = p.id_mascota;
                document.getElementById('info-nombre').textContent = p.nombre;
                document.getElementById('info-edad').textContent = p.edad;
                document.getElementById('info-sexo').textContent = p.sexo;
                document.getElementById('info-especie').textContent = p.especie;
                document.getElementById('info-raza').textContent = p.raza;
                document.getElementById('info-peso').textContent = p.peso;
            })
            .catch(function() {});

        renderConsultas();
    }

    window.renderConsultas = function() {
        var lista = document.getElementById('consultas-lista');
        var empty = document.getElementById('consultas-empty');
        var filterFecha = document.getElementById('filter-fecha').value;

        fetch('/dist/content/inicio_data.php?action=consultas_mascota&id=' + encodeURIComponent(pacienteId))
            .then(function(r) { return r.json(); })
            .then(function(data) {
                consultasCache = data || [];
                var consultas = consultasCache;

                if (filterFecha) {
                    consultas = consultas.filter(function(c) { return c.fecha === filterFecha; });
                }

                lista.innerHTML = '';
                if (consultas.length === 0) {
                    empty.classList.remove('hidden');
                    return;
                }
                empty.classList.add('hidden');

                for (var i = 0; i < consultas.length; i++) {
                    var c = consultas[i];
                    var div = document.createElement('div');
                    if (c.es_emergencia) {
                        div.className = 'flex items-center justify-between p-4 bg-red-50/80 rounded-xl border border-red-200 hover:bg-red-50 transition-all';
                        div.innerHTML = '<div class="flex items-center gap-3"><svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg><span class="text-sm font-medium text-gray-700">' + formatDate(c.fecha) + '</span><span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-bold rounded-full">EMERGENCIA</span></div><button onclick="abrirModalEmergencia(' + c.id_consulta + ')" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:from-red-600 hover:to-red-700 transition-all shadow-sm hover:shadow-md active:scale-[0.98]">Ver Emergencia</button>';
                    } else {
                        div.className = 'flex items-center justify-between p-4 bg-green-50/50 rounded-xl border border-green-100 hover:bg-green-50 transition-all';
                        div.innerHTML = '<div class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg><span class="text-sm font-medium text-gray-700">' + formatDate(c.fecha) + '</span></div><button onclick="abrirModal(' + c.id_consulta + ')" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-sm hover:shadow-md active:scale-[0.98]">Ver Consulta</button>';
                    }
                    lista.appendChild(div);
                }
            })
            .catch(function() {});
    };

    window.filtrarConsultas = function() {
        renderConsultas();
    };

    window.abrirModal = function(idConsulta) {
        fetch('/dist/content/inicio_data.php?action=detalle_consulta&id=' + idConsulta)
            .then(function(r) { return r.json(); })
            .then(function(c) {
                if (!c || !c.id_consulta) return;

                document.getElementById('modal-fecha').textContent = formatDate(c.fecha);
                document.getElementById('modal-diagnostico').textContent = c.diagnostico || '-';
                document.getElementById('modal-examen').textContent = c.examen_fisico || '-';

                var testsCont = document.getElementById('modal-tests');
                testsCont.innerHTML = '';
                var tests = c.tests_rapidos || [];
                if (tests.length === 0) {
                    testsCont.innerHTML = '<span class="text-sm text-gray-400 italic">No se realizaron tests rapidos</span>';
                } else {
                    for (var i = 0; i < tests.length; i++) {
                        var badge = document.createElement('span');
                        badge.className = 'px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium';
                        badge.textContent = tests[i];
                        testsCont.appendChild(badge);
                    }
                }

                var labCont = document.getElementById('modal-laboratorio');
                labCont.innerHTML = '';
                var labs = c.laboratorio || [];
                if (labs.length === 0) {
                    labCont.innerHTML = '<span class="text-sm text-gray-400 italic">No se realizaron examenes de laboratorio</span>';
                } else {
                    for (var i = 0; i < labs.length; i++) {
                        var badge = document.createElement('span');
                        badge.className = 'px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-medium';
                        badge.textContent = labs[i].tipo;
                        labCont.appendChild(badge);
                    }
                }
                document.getElementById('modal-lab-obs').textContent = (labs.length > 0 && labs[0].observaciones) ? labs[0].observaciones : 'Sin observaciones';

                var vacCont = document.getElementById('modal-vacunas');
                vacCont.innerHTML = '';
                var vacs = c.vacunas || [];
                if (vacs.length === 0) {
                    vacCont.innerHTML = '<span class="text-sm text-gray-400 italic">No se aplicaron vacunas</span>';
                } else {
                    for (var i = 0; i < vacs.length; i++) {
                        var badge = document.createElement('span');
                        badge.className = 'px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium';
                        badge.textContent = vacs[i].nombre;
                        vacCont.appendChild(badge);
                    }
                }
                document.getElementById('modal-vac-obs').textContent = (vacs.length > 0 && vacs[0].observaciones) ? vacs[0].observaciones : 'Sin observaciones';

                // Store id_consulta and propietario email for email/pdf buttons
                document.getElementById('modal-consulta').dataset.consultaId = c.id_consulta;
                document.getElementById('modal-consulta').dataset.propEmail = c.prop_gmail || '';

                document.getElementById('modal-consulta').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            })
            .catch(function() {});
    };

    window.cerrarModalConsulta = function() {
        document.getElementById('modal-consulta').classList.add('hidden');
        document.body.style.overflow = '';
    };

    window.enviarCorreo = function(btn) {
        var idc = document.getElementById('modal-consulta').dataset.consultaId;
        if (!idc) return;
        var propEmail = document.getElementById('modal-consulta').dataset.propEmail;
        if (!propEmail) {
            alert('El propietario no tiene un correo electronico registrado.');
            return;
        }
        btn.textContent = 'Enviando...';
        btn.disabled = true;
        var fd = new FormData();
        fd.append('action', 'enviar_consulta');
        fd.append('id_consulta', idc);
        fd.append('email', propEmail);
        fetch('/dist/content/inicio_data.php', { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                alert(data.message);
                btn.textContent = 'Enviar por Correo';
                btn.disabled = false;
            })
            .catch(function() {
                alert('Error de conexion con el servidor. Verifique que el servidor tenga configurado SMTP para enviar correos.');
                btn.textContent = 'Enviar por Correo';
                btn.disabled = false;
            });
    };

    window.descargarPDF = function() {
        var idc = document.getElementById('modal-consulta').dataset.consultaId;
        if (!idc) return;
        fetch('/dist/content/inicio_data.php?action=pdf_consulta&id=' + idc)
            .then(function(r) {
                var ct = r.headers.get('Content-Type') || '';
                if (!ct.includes('pdf')) {
                    return r.text().then(function(t) { alert('Error: ' + t); });
                }
                return r.blob().then(function(blob) {
                    var url = URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = 'historia_' + idc + '.pdf';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                });
            })
            .catch(function() { alert('Error al descargar el PDF'); });
    };

    window.abrirModalEmergencia = function(idEmergencia) {
        var idc = Math.abs(idEmergencia);
        fetch('/dist/content/inicio_data.php?action=detalle_emergencia&id=' + idc)
            .then(function(r) { return r.json(); })
            .then(function(c) {
                if (!c || !c.id_cita) return;
                document.getElementById('emergencia-fecha').textContent = formatDate(c.fecha);
                document.getElementById('emergencia-diagnostico').textContent = c.diagnostico || '-';
                document.getElementById('emergencia-veterinario').textContent = c.nombre_veterinario || '-';
                document.getElementById('modal-emergencia').dataset.emergenciaId = idc;
                document.getElementById('modal-emergencia').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            })
            .catch(function() {});
    };

    window.cerrarModalEmergencia = function() {
        document.getElementById('modal-emergencia').classList.add('hidden');
        document.body.style.overflow = '';
    };

    window.descargarPDFEmergencia = function() {
        var idc = document.getElementById('modal-emergencia').dataset.emergenciaId;
        if (!idc) return;
        fetch('/dist/content/inicio_data.php?action=pdf_emergencia&id=' + idc)
            .then(function(r) {
                var ct = r.headers.get('Content-Type') || '';
                if (!ct.includes('pdf')) {
                    return r.text().then(function(t) { alert('Error: ' + t); });
                }
                return r.blob().then(function(blob) {
                    var url = URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = 'emergencia_' + idc + '.pdf';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                });
            })
            .catch(function() { alert('Error al descargar el PDF'); });
    };

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') cerrarModalConsulta();
    });

    document.getElementById('modal-consulta').addEventListener('click', function(e) {
        if (e.target === this) cerrarModalConsulta();
    });

    init();
})();
</script>
