<div class="animate-fadeIn p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-green-800">Inventario</h1>
        <p class="text-gray-600 mt-1">Gestiona tu inventario</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-green-100 p-6 flex items-center gap-4">
            <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center shrink-0">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-bold text-gray-800" id="totalProductos">0</p>
                <p class="text-sm text-gray-500">Productos</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-green-100 p-6 flex items-center gap-4">
            <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center shrink-0">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-bold text-gray-800" id="totalProveedores">0</p>
                <p class="text-sm text-gray-500">Proveedores</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-green-100 p-6 flex items-center gap-4">
            <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center shrink-0">
                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-bold text-red-600" id="stockBajo">0</p>
                <p class="text-sm text-gray-500">Stock Bajo</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
            <h2 class="text-white font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                Productos estrella
            </h2>
        </div>
        <div class="p-6">
            <div id="productos-estrella-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6"></div>
            <div id="productos-estrella-empty" class="hidden text-center py-8">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                </svg>
                <p class="text-gray-400 text-sm font-medium">No hay ventas registradas aún.</p>
            </div>
        </div>
    </div>
</div>

<script>
fetch('/dist/content/inicio_data.php?action=inventario_resumen')
    .then(function(r) { return r.json(); })
    .then(function(d) {
        document.getElementById('totalProductos').textContent = d.total_productos;
        document.getElementById('totalProveedores').textContent = d.total_proveedores;
        document.getElementById('stockBajo').textContent = d.stock_bajo;
    })
    .catch(function() {});

fetch('/dist/content/inicio_data.php?action=productos_estrella')
    .then(function(r) { return r.json(); })
    .then(function(d) {
        var grid = document.getElementById('productos-estrella-grid');
        var empty = document.getElementById('productos-estrella-empty');
        grid.innerHTML = '';
        if (!d.productos || d.productos.length === 0) {
            empty.classList.remove('hidden');
            return;
        }
        empty.classList.add('hidden');
        d.productos.forEach(function(p) {
            var pct = d.max > 0 ? Math.round((p.total_vendido / d.max) * 100) : 0;
            var offset = 283 - (283 * pct / 100);
            var div = document.createElement('div');
            div.className = 'flex flex-col items-center text-center';
            div.innerHTML = '<svg width="120" height="120" class="mb-2">'
                + '<circle cx="60" cy="60" r="45" fill="none" stroke="#e5e7eb" stroke-width="8"/>'
                + '<circle cx="60" cy="60" r="45" fill="none" stroke="#059669" stroke-width="8" stroke-dasharray="283" stroke-dashoffset="' + offset + '" transform="rotate(-90 60 60)" stroke-linecap="round"/>'
                + '<text x="60" y="60" text-anchor="middle" dominant-baseline="central" font-size="22" font-weight="bold" fill="#059669">' + pct + '%</text>'
                + '</svg>'
                + '<p class="font-semibold text-gray-800 text-sm leading-tight">' + p.descripcion + '</p>'
                + '<p class="text-xs text-gray-500 mt-1">' + p.total_vendido + ' vendidos</p>';
            grid.appendChild(div);
        });
    })
    .catch(function() {});
</script>
