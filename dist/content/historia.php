<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$rol_hist = $_SESSION['usuario']['rol'] ?? '';
$id_prop_hist = $_SESSION['usuario']['id_propietario'] ?? '';
?>
<div class="animate-fadeIn" data-rol="<?= $rol_hist ?>" data-id-propietario="<?= $id_prop_hist ?>">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-green-800">Historias Médicas</h1>
            <p class="text-gray-600">Busca la historia médica del paciente</p>
        </div>
    </div>

<?php if ($rol_hist !== 'propietario'): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-5 py-4">
            <h2 class="text-white font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Historiales Médicos
            </h2>
        </div>
        <div class="p-5">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex gap-1 bg-green-50 rounded-xl p-1">
                    <button onclick="setHistFilter('paciente')" id="histFilterPaciente" class="px-5 py-2 rounded-lg text-sm font-medium transition-all bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-sm">Pacientes</button>
                    <button onclick="setHistFilter('propietario')" id="histFilterPropietario" class="px-5 py-2 rounded-lg text-sm font-medium transition-all text-gray-600 hover:text-green-700">Propietarios</button>
                </div>
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="histSearchInput" oninput="histBuscar()" placeholder="Buscar paciente o propietario..." class="w-full pl-10 pr-3 py-2.5 text-sm border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 bg-green-50/50 focus:border-green-500 focus:ring-2 focus:ring-green-200 focus:bg-white transition-all">
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden">
        <div id="histEmptyState" class="flex flex-col items-center py-16">
            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-gray-400 text-sm font-medium">Selecciona un filtro y realiza una búsqueda</p>
            <p class="text-gray-400 text-xs mt-1">Escribe en el campo de búsqueda para encontrar historiales</p>
        </div>

        <div id="histResultPaciente" class="hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    Resultados: Pacientes
                    <span id="histCountPaciente" class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">0</span>
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-green-50">
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Cédula</th>
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Nombre</th>
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Especie</th>
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Raza</th>
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Edad</th>
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="histTbodyPaciente"></tbody>
                </table>
            </div>
        </div>

        <div id="histResultPropietario" class="hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    Resultados: Propietarios
                    <span id="histCountPropietario" class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">0</span>
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-green-50">
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Cédula</th>
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Nombres</th>
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Apellidos</th>
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Teléfono</th>
                            <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="histTbodyPropietario"></tbody>
                </table>
            </div>
            <div id="histTutorMascotas" class="hidden p-5 border-t border-green-100 bg-green-50/30">
                <h3 class="font-bold text-green-800 text-lg mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    <span>Tutor de las mascotas</span>
                    <span id="histPropietarioNombre" class="text-sm font-normal text-gray-500 ml-2"></span>
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-green-50">
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Cédula Mascota</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Nombre</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Especie</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Raza</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Edad</th>
                                <th class="p-4 font-semibold text-green-900 text-sm border-b border-green-100">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="histTbodyMascotas"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var filtroActual = 'paciente';

    window.setHistFilter = function(modo) {
        filtroActual = modo;
        var btnPac = document.getElementById('histFilterPaciente');
        var btnProp = document.getElementById('histFilterPropietario');
        if (modo === 'paciente') {
            btnPac.className = 'px-5 py-2 rounded-lg text-sm font-medium transition-all bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-sm';
            btnProp.className = 'px-5 py-2 rounded-lg text-sm font-medium transition-all text-gray-600 hover:text-green-700';
        } else {
            btnProp.className = 'px-5 py-2 rounded-lg text-sm font-medium transition-all bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-sm';
            btnPac.className = 'px-5 py-2 rounded-lg text-sm font-medium transition-all text-gray-600 hover:text-green-700';
        }
        document.getElementById('histTutorMascotas').classList.add('hidden');
        histBuscar();
    };

    var timerBuscar = null;
    window.histBuscar = function() {
        clearTimeout(timerBuscar);
        timerBuscar = setTimeout(function() {
            var termino = document.getElementById('histSearchInput').value.trim();
            document.getElementById('histResultPaciente').classList.add('hidden');
            document.getElementById('histResultPropietario').classList.add('hidden');
            document.getElementById('histEmptyState').classList.remove('hidden');
            if (!termino) return;

            var url = filtroActual === 'paciente'
                ? '/dist/content/inicio_data.php?action=buscar_mascotas&q=' + encodeURIComponent(termino)
                : '/dist/content/inicio_data.php?action=buscar_propietarios&q=' + encodeURIComponent(termino);

            fetch(url).then(function(r) { return r.json(); }).then(function(data) {
                if (filtroActual === 'paciente') {
                    renderTablaPaciente(data);
                } else {
                    renderTablaPropietario(data.propietarios || []);
                }
            }).catch(function() {});
        }, 200);
    };

    function renderTablaPaciente(lista) {
        document.getElementById('histEmptyState').classList.add('hidden');
        document.getElementById('histResultPaciente').classList.remove('hidden');
        document.getElementById('histCountPaciente').textContent = lista.length;
        var tbody = document.getElementById('histTbodyPaciente');
        if (lista.length === 0) {
            tbody.innerHTML = '<tr><td class="p-4 border-b border-gray-100 text-gray-700 text-sm italic text-center" colspan="6"><div class="flex flex-col items-center py-8"><svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><p class="text-gray-400 text-sm font-medium">No se encontraron pacientes.</p></div></td></tr>';
            return;
        }
        var html = '';
        for (var i = 0; i < lista.length; i++) {
            var p = lista[i];
            html += '<tr class="hover:bg-gray-50 transition-colors">';
            html += '<td class="p-4 border-b border-gray-100 text-gray-700 text-sm font-medium">' + p.id_mascota + '</td>';
            html += '<td class="p-4 border-b border-gray-100 text-gray-700 text-sm">' + p.nombre + '</td>';
            html += '<td class="p-4 border-b border-gray-100 text-gray-700 text-sm">' + p.especie + '</td>';
            html += '<td class="p-4 border-b border-gray-100 text-gray-700 text-sm">' + p.raza + '</td>';
            html += '<td class="p-4 border-b border-gray-100 text-gray-700 text-sm">' + p.edad + '</td>';
            html += '<td class="p-4 border-b border-gray-100"><a href="dist/content/historiapaciente.php?paciente=' + p.id_mascota + '" data-link data-title="Historia del paciente" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-4 py-1.5 rounded-lg text-xs font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-sm inline-block">Ver Historial</a></td>';
            html += '</tr>';
        }
        tbody.innerHTML = html;
    }

    function renderTablaPropietario(lista) {
        document.getElementById('histEmptyState').classList.add('hidden');
        document.getElementById('histResultPropietario').classList.remove('hidden');
        document.getElementById('histCountPropietario').textContent = lista.length;
        var tbody = document.getElementById('histTbodyPropietario');
        if (lista.length === 0) {
            tbody.innerHTML = '<tr><td class="p-4 border-b border-gray-100 text-gray-700 text-sm italic text-center" colspan="5"><div class="flex flex-col items-center py-8"><svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><p class="text-gray-400 text-sm font-medium">No se encontraron propietarios.</p></div></td></tr>';
            return;
        }
        var html = '';
        for (var i = 0; i < lista.length; i++) {
            var p = lista[i];
            html += '<tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="histSeleccionarPropietario(\'' + p.id_propietario + '\')">';
            html += '<td class="p-4 border-b border-gray-100 text-gray-700 text-sm font-medium">' + p.id_propietario + '</td>';
            html += '<td class="p-4 border-b border-gray-100 text-gray-700 text-sm">' + p.nombres + '</td>';
            html += '<td class="p-4 border-b border-gray-100 text-gray-700 text-sm">' + p.apellidos + '</td>';
            html += '<td class="p-4 border-b border-gray-100 text-gray-700 text-sm">' + p.telefono + '</td>';
            html += '<td class="p-4 border-b border-gray-100"><button onclick="histSeleccionarPropietario(\'' + p.id_propietario + '\')" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-4 py-1.5 rounded-lg text-xs font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-sm">Ver Mascotas</button></td>';
            html += '</tr>';
        }
        tbody.innerHTML = html;
    }

    window.histSeleccionarPropietario = function(cedula) {
        fetch('/dist/content/inicio_data.php?action=mascotas_por_propietario&id=' + encodeURIComponent(cedula))
            .then(function(r) { return r.json(); })
            .then(function(mascotas) {
                document.getElementById('histPropietarioNombre').textContent = '(' + cedula + ')';
                var tbody = document.getElementById('histTbodyMascotas');
                if (mascotas.length === 0) {
                    tbody.innerHTML = '<tr><td class="p-4 border-b border-gray-100 text-gray-700 text-sm italic text-center" colspan="6"><p class="text-gray-400 text-sm font-medium">No tiene mascotas registradas.</p></td></tr>';
                } else {
                    var html = '';
                    for (var j = 0; j < mascotas.length; j++) {
                        var m = mascotas[j];
                        html += '<tr class="hover:bg-gray-50 transition-colors">';
                        html += '<td class="p-4 border-b border-gray-100 text-gray-700 text-sm font-medium">' + m.id_mascota + '</td>';
                        html += '<td class="p-4 border-b border-gray-100 text-gray-700 text-sm">' + m.nombre + '</td>';
                        html += '<td class="p-4 border-b border-gray-100 text-gray-700 text-sm">' + m.especie + '</td>';
                        html += '<td class="p-4 border-b border-gray-100 text-gray-700 text-sm">' + m.raza + '</td>';
                        html += '<td class="p-4 border-b border-gray-100 text-gray-700 text-sm">' + m.edad + '</td>';
                        html += '<td class="p-4 border-b border-gray-100"><a href="dist/content/historiapaciente.php?paciente=' + m.id_mascota + '" data-link data-title="Historia del paciente" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-4 py-1.5 rounded-lg text-xs font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-sm inline-block">Ver Historial</a></td>';
                        html += '</tr>';
                    }
                    tbody.innerHTML = html;
                }
                document.getElementById('histTutorMascotas').classList.remove('hidden');
            })
            .catch(function() {});
    };

    // Auto-load for propietario
    var rootEl = document.querySelector('[data-rol]');
    if (rootEl && rootEl.dataset.rol === 'propietario') {
        var idProp = rootEl.dataset.idPropietario;
        if (idProp) {
            document.getElementById('histEmptyState').classList.add('hidden');
            document.getElementById('histResultPropietario').classList.remove('hidden');
            histSeleccionarPropietario(idProp);
        }
    }
})();
</script>
