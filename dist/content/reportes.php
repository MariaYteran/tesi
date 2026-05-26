<span class="hidden bg-green-600 text-gray-800 border-green-600 bg-green-50 grid-cols-4 shadow-xl"></span>
<div class="animate-fadeIn p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-green-800">Generación de Registros</h1>
        <p class="text-gray-600 mt-1">Panel de análisis y generación de reportes</p>
    </div>

    <div id="tabCards" class="grid grid-cols-4 gap-4 mb-8">
        <button onclick="cambiarTab('consultas')" id="tab-consultas" class="tab-btn bg-white rounded-2xl shadow-sm border-2 border-green-200 p-5 text-center transition-all cursor-pointer active-tab">
            <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            <h3 class="font-bold text-green-800 text-lg">Consultas</h3>
            <p class="text-xs text-gray-500 mt-1">Ver estadísticas de consultas</p>
        </button>
        <button onclick="cambiarTab('inventario')" id="tab-inventario" class="tab-btn bg-white rounded-2xl shadow-sm border-2 border-green-200 p-5 text-center transition-all cursor-pointer">
            <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <h3 class="font-bold text-green-800 text-lg">Inventario</h3>
            <p class="text-xs text-gray-500 mt-1">Ver estadísticas de inventario</p>
        </button>
        <button onclick="cambiarTab('detalles')" id="tab-detalles" class="tab-btn bg-white rounded-2xl shadow-sm border-2 border-green-200 p-5 text-center transition-all cursor-pointer">
            <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="font-bold text-green-800 text-lg">Detalles de Consulta</h3>
            <p class="text-xs text-gray-500 mt-1">Desglose de servicios por consulta</p>
        </button>
        <button onclick="cambiarTab('pagos')" id="tab-pagos" class="tab-btn bg-white rounded-2xl shadow-sm border-2 border-green-200 p-5 text-center transition-all cursor-pointer">
            <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h3 class="font-bold text-green-800 text-lg">Pagos</h3>
            <p class="text-xs text-gray-500 mt-1">Estadísticas por tipo de pago</p>
        </button>
    </div>

    <div id="filtroBar" class="flex justify-center mb-6">
        <div class="inline-flex bg-white rounded-xl shadow-sm border border-green-100 p-1 gap-1">
            <button onclick="cambiarFiltro('mes')" id="filtro-mes" class="filtro-btn px-6 py-2 rounded-lg text-sm font-semibold transition-all filtro-activo">Mes</button>
            <button onclick="cambiarFiltro('semana')" id="filtro-semana" class="filtro-btn px-6 py-2 rounded-lg text-sm font-semibold transition-all">Semana</button>
            <button onclick="cambiarFiltro('año')" id="filtro-año" class="filtro-btn px-6 py-2 rounded-lg text-sm font-semibold transition-all">Año</button>
        </div>
    </div>

    <div id="chartContainer" class="bg-white rounded-2xl shadow-sm border border-green-100 p-6 mb-6">
        <canvas id="mainChart" height="280"></canvas>
    </div>

    <div id="detallesContainer" class="hidden bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
            <h3 class="text-white font-bold text-lg">Detalles de Consulta</h3>
        </div>
        <div class="p-5">
            <div class="flex justify-center gap-2 mb-5">
                <button onclick="cambiarSubTab('tests')" id="subtab-tests" class="subtab-btn px-5 py-2 rounded-lg text-sm font-semibold transition-all subtab-activo">Tests Rápidos</button>
                <button onclick="cambiarSubTab('laboratorio')" id="subtab-laboratorio" class="subtab-btn px-5 py-2 rounded-lg text-sm font-semibold transition-all">Laboratorio</button>
                <button onclick="cambiarSubTab('vacunas')" id="subtab-vacunas" class="subtab-btn px-5 py-2 rounded-lg text-sm font-semibold transition-all">Vacunas</button>
            </div>
            <div id="detallesLista" class="max-h-[300px] overflow-y-auto space-y-2"></div>
        </div>
    </div>

    <div id="statsContainer" class="grid grid-cols-2 gap-6 mb-6 max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-green-100 p-6 text-center">
            <p id="statValor1" class="text-3xl font-bold text-green-800">0</p>
            <p id="statLabel1" class="text-sm text-gray-500 mt-1">Pacientes atendidos</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-green-100 p-6 text-center">
            <p id="statValor2" class="text-3xl font-bold text-green-800">$0</p>
            <p id="statLabel2" class="text-sm text-gray-500 mt-1">Servicios prestados</p>
        </div>
    </div>

    <div class="flex justify-center gap-4">
        <button onclick="descargarPDFRegistro()" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-8 py-3 rounded-xl text-sm font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Descargar PDF
        </button>
        <button onclick="enviarCorreoRegistro(this)" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-8 py-3 rounded-xl text-sm font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Enviar por correo
        </button>
    </div>
</div>

<script>
var chartInstance = null;
var tabActivo = 'consultas';
var filtroActivo = 'mes';
var subTabActivo = 'tests';

function cambiarTab(tab) {
    tabActivo = tab;
    document.querySelectorAll('.tab-btn').forEach(function(b) {
        b.classList.remove('active-tab', 'shadow-xl', 'border-green-600');
        b.classList.add('border-green-200', 'shadow-sm');
    });
    var btn = document.getElementById('tab-' + tab);
    btn.classList.add('active-tab', 'shadow-xl', 'border-green-600');
    btn.classList.remove('border-green-200', 'shadow-sm');

    var dc = document.getElementById('detallesContainer');
    var cc = document.getElementById('chartContainer');
    var sc = document.getElementById('statsContainer');

    if (tab === 'detalles') {
        cc.classList.remove('hidden');
        dc.classList.remove('hidden');
        sc.classList.add('hidden');
        document.getElementById('filtroBar').classList.remove('hidden');
        cargarDetalles();
    } else {
        dc.classList.add('hidden');
        cc.classList.remove('hidden');
        sc.classList.remove('hidden');
        document.getElementById('filtroBar').classList.remove('hidden');
        cargarDatos();
    }
}

function cambiarFiltro(filtro) {
    filtroActivo = filtro;
    document.querySelectorAll('.filtro-btn').forEach(function(b) {
        b.classList.remove('filtro-activo', 'bg-green-600', 'text-white');
        b.classList.add('bg-white', 'text-gray-700');
    });
    var btn = document.getElementById('filtro-' + filtro);
    btn.classList.add('filtro-activo', 'bg-green-600', 'text-white');
    btn.classList.remove('bg-white', 'text-gray-700');

    if (tabActivo !== 'detalles') {
        cargarDatos();
    } else {
        cargarDetalles();
    }
}

function cambiarSubTab(sub) {
    subTabActivo = sub;
    document.querySelectorAll('.subtab-btn').forEach(function(b) {
        b.classList.remove('subtab-activo', 'bg-green-600', 'text-white');
        b.classList.add('bg-white', 'text-gray-700', 'border', 'border-green-200');
    });
    var btn = document.getElementById('subtab-' + sub);
    btn.classList.add('subtab-activo', 'bg-green-600', 'text-white');
    btn.classList.remove('bg-white', 'text-gray-700', 'border', 'border-green-200');
    renderDetallesLista();
    renderDetallesChart();
}

function cargarDatos() {
    if (tabActivo === 'consultas') {
        cargarConsultas();
    } else if (tabActivo === 'inventario') {
        cargarInventario();
    } else if (tabActivo === 'pagos') {
        cargarPagos();
    }
}

function cargarConsultas() {
    document.getElementById('statValor2').parentElement.classList.remove('hidden');
    fetch('/dist/content/inicio_data.php?action=registros_consultas&filtro=' + filtroActivo)
        .then(function(r) { return r.json(); })
        .then(function(d) {
            document.getElementById('statValor1').textContent = d.total_pacientes;
            document.getElementById('statLabel1').textContent = 'Pacientes atendidos';
            document.getElementById('statValor2').textContent = '$' + Number(d.total_servicios).toFixed(2);
            document.getElementById('statLabel2').textContent = 'Servicios prestados';
            renderChart(d.chart, 'consultas', '# consultas');
        })
        .catch(function() {});
}

function cargarInventario() {
    document.getElementById('statValor2').parentElement.classList.remove('hidden');
    fetch('/dist/content/inicio_data.php?action=registros_inventario&filtro=' + filtroActivo)
        .then(function(r) { return r.json(); })
        .then(function(d) {
            document.getElementById('statValor1').textContent = '$' + Number(d.total_inversiones).toFixed(2);
            document.getElementById('statLabel1').textContent = 'Inversiones (proveedores)';
            document.getElementById('statValor2').textContent = d.total_ventas_count;
            document.getElementById('statLabel2').textContent = 'Ventas realizadas';
            renderChartMulti(d.chart, 'inventario');
        })
        .catch(function() {});
}

function cargarPagos() {
    document.getElementById('statValor2').parentElement.classList.add('hidden');
    fetch('/dist/content/inicio_data.php?action=registros_pagos&filtro=' + filtroActivo)
        .then(function(r) { return r.json(); })
        .then(function(d) {
            document.getElementById('statValor1').textContent = '$' + Number(d.total_ingresos).toFixed(2);
            document.getElementById('statLabel1').textContent = 'Total ingresos';
            document.getElementById('statValor2').textContent = d.total_transacciones;
            document.getElementById('statLabel2').textContent = 'Transacciones';
            renderChartPagos(d.chart);
        })
        .catch(function() {});
}

function cargarDetalles() {
    fetch('/dist/content/inicio_data.php?action=registros_detalles&filtro=' + filtroActivo)
        .then(function(r) { return r.json(); })
        .then(function(d) {
            window._detallesData = d;
            renderDetallesLista();
            renderDetallesChart();
        })
        .catch(function() {});
}

function renderDetallesLista() {
    var data = window._detallesData;
    if (!data) return;
    var lista = document.getElementById('detallesLista');
    var items = [];
    if (subTabActivo === 'tests') items = data.tests || [];
    else if (subTabActivo === 'laboratorio') items = data.laboratorio || [];
    else if (subTabActivo === 'vacunas') items = data.vacunas || [];

    if (items.length === 0) {
        lista.innerHTML = '<div class="text-center text-gray-400 py-8">No hay datos disponibles</div>';
        return;
    }

    var totalItems = items.reduce(function(s, it) { return s + (it.total || 0); }, 0);
    var html = '';
    for (var i = 0; i < items.length; i++) {
        var it = items[i];
        var pct = totalItems > 0 ? ((it.total / totalItems) * 100).toFixed(1) : 0;
        html += '<div class="flex items-center gap-3 p-3 bg-green-50 rounded-xl border border-green-100">';
        html += '<input type="checkbox" checked class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">';
        html += '<span class="flex-1 text-sm font-medium text-gray-700">' + (it.nombre || it.tipo || '') + '</span>';
        html += '<span class="text-sm font-bold text-green-700">' + it.total + '</span>';
        html += '<span class="text-xs text-gray-400 w-10 text-right">' + pct + '%</span>';
        html += '</div>';
    }
    lista.innerHTML = html;
}

function renderDetallesChart() {
    var data = window._detallesData;
    if (!data) return;
    var items = [];
    if (subTabActivo === 'tests') items = data.tests || [];
    else if (subTabActivo === 'laboratorio') items = data.laboratorio || [];
    else if (subTabActivo === 'vacunas') items = data.vacunas || [];

    var ctx = document.getElementById('mainChart').getContext('2d');
    if (chartInstance) chartInstance.destroy();

    if (items.length === 0) {
        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: { labels: ['Sin datos'], datasets: [{ label: 'Sin datos', data: [0], backgroundColor: '#d1d5db' }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
        return;
    }

    var labels = items.map(function(it) { return it.nombre || it.tipo || ''; });
    var values = items.map(function(it) { return it.total || 0; });

    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: subTabActivo === 'tests' ? 'Tests Rápidos' : subTabActivo === 'laboratorio' ? 'Laboratorio' : 'Vacunas',
                data: values,
                backgroundColor: '#059669',
                borderRadius: 4,
                borderSkipped: false,
                maxBarThickness: 35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, stepSize: 1 },
                    grace: '10%'
                }
            }
        }
    });
}

function formatLabels(chartData) {
    var dias = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
    var meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    return chartData.map(function(d) {
        var p = d.periodo;
        if (filtroActivo === 'semana') {
            var dt = new Date(p + (p.length === 10 ? 'T00:00:00' : ''));
            return dias[dt.getDay()];
        } else if (filtroActivo === 'mes') {
            var parts = p.split('-');
            var m = parseInt(parts[1], 10);
            return parseInt(parts[2], 10) + ' ' + meses[m - 1];
        } else {
            var parts = p.split('-');
            var m = parseInt(parts[1], 10);
            return meses[m - 1] + ' ' + parts[0];
        }
    });
}

function renderChart(chartData, label, datasetLabel) {
    var ctx = document.getElementById('mainChart').getContext('2d');
    if (chartInstance) chartInstance.destroy();
    if (!chartData || chartData.length === 0) {
        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: { labels: ['Sin datos'], datasets: [{ label: datasetLabel, data: [0], backgroundColor: '#d1d5db' }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
        return;
    }
    var labels = formatLabels(chartData);
    var values = chartData.map(function(d) { return d[label] || d.consultas || 0; });
    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: datasetLabel,
                data: values,
                backgroundColor: '#059669',
                borderRadius: 4,
                borderSkipped: false,
                maxBarThickness: 35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, stepSize: 1 },
                    grace: '10%'
                }
            }
        }
    });
}

function renderChartMulti(chartData, label) {
    var ctx = document.getElementById('mainChart').getContext('2d');
    if (chartInstance) chartInstance.destroy();
    if (!chartData || chartData.length === 0) {
        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: { labels: ['Sin datos'], datasets: [
                { label: 'Ventas', data: [0], backgroundColor: '#059669' },
                { label: 'Productos', data: [0], backgroundColor: '#6ee7b7' }
            ]},
            options: { responsive: true, maintainAspectRatio: false }
        });
        return;
    }
    var labels = formatLabels(chartData);
    var ventas = chartData.map(function(d) { return d.ventas || 0; });
    var productos = chartData.map(function(d) { return d.productos_vendidos || 0; });
    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { label: 'Ventas', data: ventas, backgroundColor: '#059669', borderRadius: 4, borderSkipped: false, maxBarThickness: 35 },
                { label: 'Productos vendidos', data: productos, backgroundColor: '#6ee7b7', borderRadius: 4, borderSkipped: false, maxBarThickness: 25 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { boxWidth: 12, padding: 12 } }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, stepSize: 1 },
                    grace: '10%'
                }
            }
        }
    });
}

function renderChartPagos(chartData) {
    var ctx = document.getElementById('mainChart').getContext('2d');
    if (chartInstance) chartInstance.destroy();
    if (!chartData || chartData.length === 0) {
        chartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: { labels: ['Sin datos'], datasets: [{ data: [1], backgroundColor: ['#d1d5db'] }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, cutout: '55%' }
        });
        return;
    }
    var colores = { 'efectivo_bs': '#166534', 'efectivo_usd': '#059669', 'pago movil': '#10b981', 'punto': '#6ee7b7' };
    chartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: chartData.map(function(d) { return d.label; }),
            datasets: [{
                data: chartData.map(function(d) { return d.total; }),
                backgroundColor: chartData.map(function(d) { return colores[d.tipo] || '#d1d5db'; }),
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 14, padding: 14, font: { size: 13 } }
                }
            },
            cutout: '55%'
        }
    });
}

function descargarPDFRegistro() {
    var url = '/dist/content/inicio_data.php?action=pdf_registro_consultas&filtro=' + filtroActivo + '&tab=' + tabActivo;
    var a = document.createElement('a');
    a.href = url;
    a.download = 'registro_' + tabActivo + '.pdf';
    document.body.appendChild(a);
    a.click();
    a.remove();
}

function enviarCorreoRegistro(btn) {
    btn.disabled = true;
    btn.innerHTML = 'Enviando...';
    var fd = new FormData();
    fd.append('action', 'enviar_reporte_registro');
    fd.append('filtro', filtroActivo);
    fd.append('tab', tabActivo);
    fetch('/dist/content/inicio_data.php', { method:'POST', body:fd })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            alert(d.message || (d.success ? 'Reporte enviado correctamente' : 'Error al enviar'));
        })
        .catch(function() { alert('Error de conexion'); })
        .then(function() { btn.disabled = false; btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg> Enviar por correo'; });
}

window.cambiarTab = cambiarTab;
window.cambiarFiltro = cambiarFiltro;
window.cambiarSubTab = cambiarSubTab;
window.cargarPagos = cargarPagos;
window.descargarPDFRegistro = descargarPDFRegistro;
window.enviarCorreoRegistro = enviarCorreoRegistro;

cambiarTab('consultas');
cambiarFiltro('mes');
</script>