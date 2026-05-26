<div class="animate-fadeIn">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-green-800">Calendario de Citas</h1>
        <p class="text-gray-600 mt-1">Verifica los agendados del mes</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-green-100 p-6 max-w-2xl mx-auto">
        <div class="flex flex-col items-center">

            <div class="relative dropdown-container mb-2">
                <span id="yearDisplay" onclick="toggleDropdown('dropdownAños')" class="text-2xl font-bold text-green-700 cursor-pointer hover:text-green-600 transition-colors select-none"></span>
                <div id="dropdownAños" class="dropdown-menu absolute bg-white rounded-xl shadow-lg border border-green-100 p-4 z-50 grid grid-cols-5 gap-2 hidden" style="top:110%; left:50%; transform:translateX(-50%); min-width:260px;"></div>
            </div>

            <div class="relative dropdown-container mb-6">
                <div class="flex items-center justify-center gap-4">
                    <svg onclick="cambiarMes(-1)" class="w-5 h-5 text-green-600 cursor-pointer hover:text-green-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span id="monthDisplay" onclick="toggleDropdown('dropdownMeses')" class="text-xl font-semibold text-green-800 cursor-pointer hover:text-green-700 transition-colors select-none"></span>
                    <svg onclick="cambiarMes(1)" class="w-5 h-5 text-green-600 cursor-pointer hover:text-green-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
                <div id="dropdownMeses" class="dropdown-menu absolute bg-white rounded-xl shadow-lg border border-green-100 p-4 z-50 grid grid-cols-3 gap-2 hidden" style="top:110%; left:50%; transform:translateX(-50%); min-width:280px;"></div>
            </div>

            <div id="calendarGrid" class="w-full"></div>

        </div>
    </div>

    <div class="text-center mt-8">
        <a href="dist/content/inicio.php" data-link class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white px-10 py-3.5 rounded-xl font-semibold shadow-md hover:shadow-lg hover:from-green-700 hover:to-emerald-700 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver a inicio
        </a>
    </div>
</div>

<script>
(function() {
    const meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    const diasCabecera = ['Lu','Ma','Mi','Ju','Vi','Sa','Do'];
    const hoy = new Date();
    let mes = hoy.getMonth();
    let año = hoy.getFullYear();

    function generar() {
        const yearSpan = document.getElementById('yearDisplay');
        const monthSpan = document.getElementById('monthDisplay');
        yearSpan.textContent = año;
        monthSpan.textContent = meses[mes];

        const grid = document.getElementById('calendarGrid');
        const primerDia = new Date(año, mes, 1);
        const ultimoDia = new Date(año, mes + 1, 0);
        const diasEnMes = ultimoDia.getDate();
        const offset = (primerDia.getDay() + 6) % 7;

        let html = '<div class="grid grid-cols-7 gap-1.5">';

        diasCabecera.forEach(d => {
            html += `<div class="text-center text-xs font-semibold text-green-600 py-2">${d}</div>`;
        });

        for (let i = 0; i < offset; i++) {
            html += '<div class="text-center"></div>';
        }

        for (let d = 1; d <= diasEnMes; d++) {
            const esHoy = (d === hoy.getDate() && mes === hoy.getMonth() && año === hoy.getFullYear());
            const cls = esHoy
                ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-sm'
                : 'text-green-800 hover:bg-green-50';
            html += `<div class="text-center p-2 rounded-xl text-sm font-medium transition-colors cursor-default ${cls}">${d}</div>`;
        }

        html += '</div>';
        grid.innerHTML = html;
    }

    window.cambiarMes = function(delta) {
        mes += delta;
        if (mes < 0) { mes = 11; año--; }
        if (mes > 11) { mes = 0; año++; }
        generar();
    };

    window.toggleDropdown = function(id) {
        document.querySelectorAll('.dropdown-menu').forEach(d => {
            if (d.id !== id) d.classList.add('hidden');
        });
        document.getElementById(id).classList.toggle('hidden');
        if (id === 'dropdownAños') renderAños();
        if (id === 'dropdownMeses') renderMeses();
    };

    function renderAños() {
        const cont = document.getElementById('dropdownAños');
        let html = '';
        for (let y = año - 7; y <= año + 4; y++) {
            const esActual = y === año;
            const cls = esActual ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white' : 'text-green-700 hover:bg-green-50';
            html += `<span onclick="seleccionarAño(${y})" class="text-center px-3 py-2 rounded-lg text-sm font-medium cursor-pointer transition-colors ${cls}">${y}</span>`;
        }
        cont.innerHTML = html;
    }

    function renderMeses() {
        const cont = document.getElementById('dropdownMeses');
        let html = '';
        meses.forEach((m, i) => {
            const esActual = i === mes;
            const cls = esActual ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white' : 'text-green-700 hover:bg-green-50';
            html += `<span onclick="seleccionarMes(${i})" class="text-center px-3 py-2 rounded-lg text-sm font-medium cursor-pointer transition-colors ${cls}">${m}</span>`;
        });
        cont.innerHTML = html;
    }

    window.seleccionarAño = function(y) {
        año = y;
        document.getElementById('dropdownAños').classList.add('hidden');
        generar();
    };

    window.seleccionarMes = function(m) {
        mes = m;
        document.getElementById('dropdownMeses').classList.add('hidden');
        generar();
    };

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-container')) {
            document.querySelectorAll('.dropdown-menu').forEach(d => d.classList.add('hidden'));
        }
    });

    generar();
})();
</script>
