<div class="animate-fadeIn p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-green-800">Consultas</h1>
        <p class="text-gray-600 mt-1">Gestiona tu consulta médica</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
            <h2 class="text-white font-bold text-lg flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Requisitos de Consulta
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de consulta</label>
                    <input type="date" id="inputFechaConsulta"
                           class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                    <div id="listaCitasFecha" class="absolute top-full left-0 right-0 bg-white border border-green-200 rounded-xl shadow-lg z-40 hidden h-[130px] overflow-y-auto mt-1"></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Cédula del veterinario</label>
                    <input type="hidden" id="inputIdCita">
                    <input type="text" id="inputVetConsulta" placeholder="V-12345678"
                           class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                </div>
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Cédula del auxiliar</label>
                    <input type="text" id="inputAuxiliarConsulta" placeholder="V-12345678"
                           autocomplete="off"
                           class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                    <input type="hidden" id="inputAuxiliarId">
                    <div id="listaAuxiliares" class="absolute top-full left-0 right-0 bg-white border border-green-200 rounded-xl shadow-lg z-40 hidden max-h-48 overflow-y-auto mt-1"></div>
                </div>
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Cédula del propietario</label>
                    <input type="text" id="inputPropietarioConsulta" placeholder="Buscar por cédula o nombre"
                           autocomplete="off"
                           class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                    <input type="hidden" id="inputPropietarioId">
                    <div id="listaPropietarios" class="absolute top-full left-0 right-0 bg-white border border-green-200 rounded-xl shadow-lg z-40 hidden max-h-48 overflow-y-auto mt-1"></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Cédula de la mascota</label>
                    <input type="text" id="inputMascotaConsulta" placeholder="V-12345678"
                           class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                </div>
            </div>
            <div id="mascotas-propietario-container" class="hidden mb-4">
                <h4 class="text-sm font-semibold text-green-700 mb-2">Mascotas del propietario</h4>
                <div id="mascotas-propietario-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2"></div>
                <div id="no-mascotas-msg" class="hidden text-center py-4 text-sm text-gray-400">No tiene mascotas registradas.</div>
            </div>
            <div id="consulta-queue-info" class="hidden text-center mb-3">
                <span class="inline-block bg-green-100 text-green-800 font-semibold px-4 py-2 rounded-lg text-sm"></span>
            </div>
            <div class="text-center">
                <button onclick="generarConsulta()"
                        class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-10 py-3.5 rounded-xl text-base font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl active:scale-[0.98]">
                    Generar Consulta
                </button>
            </div>
        </div>
    </div>

    <!-- Modal validación requisitos -->
    <div id="modalRequisitos" class="fixed inset-0 bg-black/20 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-96 mx-4 overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5 flex items-center justify-between">
                <h3 class="text-xl font-bold text-white">Advertencia</h3>
                <button onclick="document.getElementById('modalRequisitos').classList.add('hidden')" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-6 py-8 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <p class="text-gray-700 font-semibold text-lg">Por favor rellene los requisitos necesarios para la consulta</p>
                <button onclick="document.getElementById('modalRequisitos').classList.add('hidden')" class="mt-6 bg-gradient-to-r from-green-600 to-emerald-600 text-white px-8 py-2.5 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md">
                    Entendido
                </button>
            </div>
        </div>
    </div>

    <div id="card-consulta" class="hidden bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
            <h2 class="text-white font-bold text-lg flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Consulta
            </h2>
        </div>
        <div class="p-6 space-y-8">

            <div class="bg-green-50/50 rounded-xl p-5 border border-green-100">
                <h3 class="font-bold text-green-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Diagnóstico
                </h3>
                <textarea id="inputDiagnostico" rows="3" placeholder="Escribir diagnóstico..."
                          class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all resize-y"></textarea>
            </div>

            <div class="bg-green-50/50 rounded-xl p-5 border border-green-100">
                <h3 class="font-bold text-green-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Examen Físico
                </h3>
                <textarea id="inputExamenFisico" rows="3" placeholder="Describir examen físico..."
                          class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all resize-y"></textarea>
            </div>

            <div class="bg-green-50/50 rounded-xl p-5 border border-green-100">
                <h3 class="font-bold text-green-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Tests Rápidos
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                        <input type="checkbox" data-nombre="Parvovirus" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700">Parvovirus</span>
                    </label>
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                        <input type="checkbox" data-nombre="Anaplasma" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700">Anaplasma</span>
                    </label>
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                        <input type="checkbox" data-nombre="Distemper" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700">Distemper</span>
                    </label>
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                        <input type="checkbox" data-nombre="Brucella" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700">Brucella</span>
                    </label>
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                        <input type="checkbox" data-nombre="Fel/FIV" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700">Fel/FIV</span>
                    </label>
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                        <input type="checkbox" data-nombre="Giardia" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700">Giardia</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Otros</label>
                    <input type="text" id="inputOtrosTests" placeholder="Especificar otros tests..."
                           class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                </div>
            </div>

            <div class="bg-green-50/50 rounded-xl p-5 border border-green-100">
                <h3 class="font-bold text-green-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    Laboratorio
                </h3>
                <div class="space-y-2 mb-4">
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                        <input type="checkbox" data-nombre="Hematología" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700">Hematología</span>
                    </label>
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                        <input type="checkbox" data-nombre="Bioquímica sanguínea" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700">Bioquímica sanguínea</span>
                    </label>
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                        <input type="checkbox" data-nombre="Perfil tiroideo" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700">Perfil tiroideo</span>
                    </label>
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                        <input type="checkbox" data-nombre="Orina" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700">Orina</span>
                    </label>
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                        <input type="checkbox" data-nombre="Pruebas hepáticas" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700">Pruebas hepáticas</span>
                    </label>
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                        <input type="checkbox" data-nombre="Cultivo bacteriano" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700">Cultivo bacteriano</span>
                    </label>
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                        <input type="checkbox" data-nombre="Parasitología" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700">Parasitología</span>
                    </label>
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                        <input type="checkbox" data-nombre="Inmunología" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700">Inmunología</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Observaciones</label>
                    <textarea id="inputObsLab" rows="3" placeholder="Describir resultado de los exámenes..."
                              class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all resize-y"></textarea>
                </div>
            </div>

            <div class="bg-green-50/50 rounded-xl p-5 border border-green-100">
                <h3 class="font-bold text-green-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Vacunas
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                        <input type="checkbox" data-nombre="Polivalente" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700">Polivalente</span>
                    </label>
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                        <input type="checkbox" data-nombre="Trivalente felina" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700">Trivalente felina</span>
                    </label>
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                        <input type="checkbox" data-nombre="Leucemia" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700">Leucemia</span>
                    </label>
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                        <input type="checkbox" data-nombre="Antirábica" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700">Antirábica</span>
                    </label>
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                        <input type="checkbox" data-nombre="Leptospirosis" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700">Leptospirosis</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Observaciones</label>
                    <textarea id="inputObsVac" rows="3" placeholder="Describir control de vacunas..."
                              class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all resize-y"></textarea>
                </div>
            </div>

            <!-- Receta / Prescripcion -->
            <div class="bg-green-50/50 rounded-xl p-5 space-y-4">
                <h4 class="font-bold text-green-800 text-sm uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Receta / Prescripcion
                </h4>
                <textarea id="inputReceta" rows="4" placeholder="Medicamento, dosis, frecuencia, duracion..."
                          class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all resize-y"></textarea>
            </div>

            <div class="flex justify-end pt-1 pb-3">
                <button id="btnPDFReceta" onclick="descargarPDFReceta()" type="button"
                    class="bg-white border-2 border-green-600 text-green-700 px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-green-50 transition-all shadow-sm hover:shadow-md flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Descargar PDF Receta
                </button>
            </div>

            <div id="msgGuardarConsulta" class="text-sm font-semibold hidden text-center mb-2"></div>
            <div class="flex items-center justify-center gap-4 pt-2">
                <button id="btnGuardarConsulta" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-10 py-3.5 rounded-xl text-base font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl active:scale-[0.98]">
                    Guardar Consulta
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Factura -->
<div id="modalFactura" class="fixed inset-0 bg-black/20 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-xl w-[600px] mx-4 overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5 flex items-center justify-between">
            <h3 class="text-xl font-bold text-white">Factura</h3>
            <button onclick="document.getElementById('modalFactura').classList.add('hidden')" class="text-white/80 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="facturaBody" class="px-6 py-5 max-h-[70vh] overflow-y-auto"></div>
        <div class="px-6 py-3 border-t border-green-100">
            <label class="block text-sm font-semibold text-green-800 mb-2">Tipo de pago</label>
            <select id="selectTipoPagoConsulta" class="w-full px-4 py-3 border-2 border-green-200 rounded-xl outline-none text-gray-700 bg-white focus:border-green-500 transition-all text-sm">
                <option value="">Seleccionar tipo de pago</option>
                <option value="efectivo_bs">Efectivo Bs</option>
                <option value="efectivo_usd">Efectivo $</option>
                <option value="pago movil">Pago Móvil</option>
                <option value="punto">Punto</option>
            </select>
        </div>
        <div class="px-6 py-4 border-t border-green-100 flex items-center gap-3 justify-end">
            <button onclick="descargarPDFConsulta()" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-3 rounded-xl text-sm font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Descargar PDF
            </button>
            <button onclick="enviarFacturaConsulta(this)" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-3 rounded-xl text-sm font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Enviar por correo
            </button>
            <button onclick="guardarFacturaConsulta()" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-xl text-sm font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-md flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                Guardar
            </button>
        </div>
    </div>
</div>

<!-- Modal Advertencia Pago -->
<div id="modalAdvertenciaPagoConsulta" class="fixed inset-0 bg-black/20 flex items-center justify-center z-[60] hidden">
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
            <button onclick="cerrarAdvertenciaPagoConsulta()" class="bg-gradient-to-r from-amber-500 to-orange-500 text-white px-8 py-2.5 rounded-xl font-semibold hover:from-amber-600 hover:to-orange-600 transition-all">
                Aceptar
            </button>
        </div>
    </div>
</div>

<script>
var idConsultaActual = null;
var consultaQueue = [];
var consultaQueueIndex = 0;
var inputPropId = document.getElementById('inputPropietarioId');
var _multiPetConsultaIds = [];

function generarConsulta() {
    var card = document.getElementById('card-consulta');
    if (!card.classList.contains('hidden')) return;

    var fecha = document.getElementById('inputFechaConsulta').value.trim();
    var vet = document.getElementById('inputVetConsulta').value.trim();
    var propId = inputPropId.value.trim();

    if (!fecha || !vet || !propId) {
        document.getElementById('modalRequisitos').classList.remove('hidden');
        return;
    }

    var checks = document.querySelectorAll('#mascotas-propietario-grid .mascota-check:checked');
    if (checks.length > 0) {
        consultaQueue = [];
        for (var i = 0; i < checks.length; i++) {
            var cb = checks[i];
            var parent = cb.closest('div');
            consultaQueue.push({ id_mascota: cb.value, nombre: parent.querySelector('div p:first-child').textContent.trim() });
        }
        checks.forEach(function(cb) { cb.checked = false; });
        consultaQueueIndex = 0;
        loadQueuePet();
    } else {
        var masc = document.getElementById('inputMascotaConsulta').value.trim();
        if (!masc) { document.getElementById('modalRequisitos').classList.remove('hidden'); return; }
        consultaQueue = [];
        document.getElementById('consulta-queue-info').classList.add('hidden');
        document.getElementById('inputMascotaConsulta').value = masc;
        card.classList.remove('hidden');
        card.classList.add('animate-consulta');
    }
}

function loadQueuePet() {
    if (consultaQueueIndex >= consultaQueue.length) { return; }
    var pet = consultaQueue[consultaQueueIndex];
    document.getElementById('inputMascotaConsulta').value = pet.id_mascota;
    var info = document.getElementById('consulta-queue-info');
    info.querySelector('span').textContent = 'Mascota ' + (consultaQueueIndex + 1) + ' de ' + consultaQueue.length + ' — ' + pet.nombre;
    info.classList.remove('hidden');
    var card = document.getElementById('card-consulta');
    card.classList.remove('hidden');
    card.classList.add('animate-consulta');
}

document.getElementById('inputFechaConsulta').addEventListener('change', function() {
    var fecha = this.value;
    var lista = document.getElementById('listaCitasFecha');
    if (!fecha) { lista.classList.add('hidden'); return; }
    fetch('/dist/content/inicio_data.php?action=citas_por_fecha&fecha=' + encodeURIComponent(fecha))
        .then(function(r) { return r.json(); })
        .then(function(datos) {
            if (datos.length === 0) { lista.classList.add('hidden'); return; }
            var html = '';
            for (var i = 0; i < datos.length; i++) {
                var c = datos[i];
                html += '<div class="px-4 py-2.5 cursor-pointer hover:bg-green-50 border-b border-green-100 last:border-b-0 text-sm flex flex-col" onclick="seleccionarCita(\'' + c.id_cita + '\',\'' + c.id_mascota + '\',\'' + (c.id_veterinario || '') + '\',\'' + (c.id_propietario || '') + '\')">';
                html += '<span class="font-semibold text-green-800">' + (c.hora || '') + ' — ' + (c.nombre_mascota || '') + '</span>';
                html += '<span class="text-gray-500 text-xs">' + (c.motivo || '') + '</span>';
                html += '</div>';
            }
            lista.innerHTML = html;
            lista.classList.remove('hidden');
        })
        .catch(function() { lista.classList.add('hidden'); });
});

window.seleccionarCita = function(idCita, idMascota, idVet, idProp) {
    document.getElementById('inputIdCita').value = idCita;
    document.getElementById('inputMascotaConsulta').value = idMascota;
    document.getElementById('inputVetConsulta').value = idVet;
    document.getElementById('inputPropietarioConsulta').value = idProp;
    document.getElementById('inputPropietarioId').value = idProp;
    document.getElementById('listaCitasFecha').classList.add('hidden');
    cargarMascotasPropietario(idProp);
};

// Propietario autocomplete
var inputPropSearch = document.getElementById('inputPropietarioConsulta');
var listaProp = document.getElementById('listaPropietarios');

inputPropSearch.addEventListener('input', function() {
    var q = this.value.trim();
    if (q.length < 2) { listaProp.classList.add('hidden'); return; }
    fetch('/dist/content/inicio_data.php?action=buscar_propietarios&q=' + encodeURIComponent(q))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var items = data && data.propietarios ? data.propietarios : [];
            if (items.length === 0) { listaProp.classList.add('hidden'); return; }
            var html = '';
            for (var i = 0; i < items.length; i++) {
                html += '<div class="px-4 py-2.5 cursor-pointer hover:bg-green-50 border-b border-green-100 last:border-b-0 text-sm" onclick="seleccionarPropietario(\'' + items[i].id_propietario + '\',\'' + items[i].nombres.replace(/'/g,"\\'") + '\',\'' + items[i].apellidos.replace(/'/g,"\\'") + '\')">';
                html += '<span class="font-semibold text-green-800">' + items[i].id_propietario + '</span>';
                html += ' <span class="text-gray-600">' + items[i].nombres + ' ' + items[i].apellidos + '</span>';
                html += '</div>';
            }
            listaProp.innerHTML = html;
            listaProp.classList.remove('hidden');
        })
        .catch(function() { listaProp.classList.add('hidden'); });
});

document.addEventListener('click', function(e) {
    if (!inputPropSearch.contains(e.target) && !listaProp.contains(e.target)) {
        listaProp.classList.add('hidden');
    }
});

window.seleccionarPropietario = function(id, nombres, apellidos) {
    inputPropSearch.value = id + ' — ' + nombres + ' ' + apellidos;
    inputPropId.value = id;
    listaProp.classList.add('hidden');
    cargarMascotasPropietario(id);
};

// Auxiliar autocomplete
var inputAuxSearch = document.getElementById('inputAuxiliarConsulta');
var inputAuxId = document.getElementById('inputAuxiliarId');
var listaAux = document.getElementById('listaAuxiliares');

inputAuxSearch.addEventListener('input', function() {
    var q = this.value.trim();
    if (q.length < 2) { listaAux.classList.add('hidden'); return; }
    fetch('/dist/content/inicio_data.php?action=buscar_auxiliar&q=' + encodeURIComponent(q))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var items = data || [];
            if (items.length === 0) { listaAux.classList.add('hidden'); return; }
            var html = '';
            for (var i = 0; i < items.length; i++) {
                html += '<div class="px-4 py-2.5 cursor-pointer hover:bg-green-50 border-b border-green-100 last:border-b-0 text-sm" onclick="seleccionarAuxiliar(\'' + items[i].id_auxiliar.replace(/'/g,"\\'") + '\',\'' + items[i].nombres.replace(/'/g,"\\'") + '\',\'' + items[i].apellidos.replace(/'/g,"\\'") + '\')">';
                html += '<span class="font-semibold text-green-800">' + items[i].id_auxiliar + '</span>';
                html += ' <span class="text-gray-600">' + items[i].nombres + ' ' + items[i].apellidos + '</span>';
                html += '</div>';
            }
            listaAux.innerHTML = html;
            listaAux.classList.remove('hidden');
        })
        .catch(function() { listaAux.classList.add('hidden'); });
});

document.addEventListener('click', function(e) {
    if (!inputAuxSearch.contains(e.target) && !listaAux.contains(e.target)) {
        listaAux.classList.add('hidden');
    }
});

window.seleccionarAuxiliar = function(id, nombres, apellidos) {
    inputAuxSearch.value = id + ' — ' + nombres + ' ' + apellidos;
    inputAuxId.value = id;
    listaAux.classList.add('hidden');
};

window.cargarMascotasPropietario = function(idProp) {
    var container = document.getElementById('mascotas-propietario-container');
    var grid = document.getElementById('mascotas-propietario-grid');
    var noMsg = document.getElementById('no-mascotas-msg');
    grid.innerHTML = '<div class="col-span-full text-center text-gray-400 text-sm py-4">Cargando...</div>';
    container.classList.remove('hidden');
    noMsg.classList.add('hidden');

    fetch('/dist/content/inicio_data.php?action=mascotas_por_propietario&id=' + encodeURIComponent(idProp))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            grid.innerHTML = '';
            if (!data || data.length === 0) {
                container.classList.add('hidden');
                return;
            }
            for (var i = 0; i < data.length; i++) {
                var m = data[i];
                var div = document.createElement('div');
                div.className = 'flex items-center gap-3 p-3 bg-white rounded-xl border border-green-100 hover:border-green-300 transition-all cursor-pointer';
                div.innerHTML = '<input type="checkbox" value="' + m.id_mascota + '" class="mascota-check w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">'
                    + '<div><p class="text-sm font-semibold text-gray-800">' + m.nombre + '</p>'
                    + '<p class="text-xs text-gray-500">' + m.id_mascota + ' · ' + m.especie + ' · ' + m.raza + '</p></div>';
                grid.appendChild(div);
            }
        })
        .catch(function() {
            grid.innerHTML = '<div class="col-span-full text-center text-red-400 text-sm py-4">Error al cargar mascotas</div>';
        });
};

document.getElementById('btnGuardarConsulta').addEventListener('click', async function() {
    var msg = document.getElementById('msgGuardarConsulta');
    msg.className = 'text-sm font-semibold text-center mb-2 text-green-600';
    msg.textContent = 'Guardando...';

    var fecha = document.getElementById('inputFechaConsulta').value;
    var id_mascota = document.getElementById('inputMascotaConsulta').value;
    var id_veterinario = document.getElementById('inputVetConsulta').value;
    var id_propietario = (inputPropId.value || document.getElementById('inputPropietarioConsulta').value);
    var diagnostico = document.getElementById('inputDiagnostico').value;
    var examen_fisico = document.getElementById('inputExamenFisico').value;

    function recolectarChecks(container, attr) {
        var checks = container.querySelectorAll('input[type="checkbox"]:checked');
        return Array.from(checks).map(function(cb) { return cb.getAttribute(attr); });
    }

    var secciones = document.querySelectorAll('#card-consulta .bg-green-50\\/50');
    var tests = recolectarChecks(secciones[2], 'data-nombre');
    var labs = recolectarChecks(secciones[3], 'data-nombre');
    var vacs = recolectarChecks(secciones[4], 'data-nombre');

    var otrosVal = document.getElementById('inputOtrosTests').value.trim();
    if (otrosVal) { tests.push(otrosVal); }

    var obsLab = document.getElementById('inputObsLab').value;
    var obsVac = document.getElementById('inputObsVac').value;

    var fd = new FormData();
    fd.append('action', 'guardar_consulta');
    fd.append('fecha', fecha);
    fd.append('id_mascota', id_mascota);
    fd.append('id_veterinario', id_veterinario);
    fd.append('id_propietario', id_propietario);
    fd.append('id_cita', document.getElementById('inputIdCita').value);
    fd.append('id_auxiliar', document.getElementById('inputAuxiliarId').value);
    fd.append('diagnostico', diagnostico);
    fd.append('examen_fisico', examen_fisico);
    fd.append('tests', JSON.stringify(tests));
    fd.append('laboratorios', JSON.stringify(labs.map(function(l) { return {tipo: l, observaciones: obsLab}; })));
    fd.append('vacunas', JSON.stringify(vacs.map(function(v) { return {nombre: v, observaciones: obsVac}; })));
    fd.append('receta', document.getElementById('inputReceta').value);
    fd.append('multi_pet', consultaQueue.length > 1 ? '1' : '0');

    try {
        var resp = await fetch('/dist/content/inicio_data.php', { method: 'POST', body: fd });
        var data = await resp.json();
        msg.textContent = data.message;
        msg.className = 'text-sm font-semibold text-center mb-2 ' + (data.success ? 'text-green-600' : 'text-red-600');
        if (data.success) {
            if (consultaQueue.length > 0) {
                _multiPetConsultaIds.push(data.id_consulta);
                if (consultaQueueIndex < consultaQueue.length - 1) {
                    consultaQueueIndex++;
                    document.getElementById('inputDiagnostico').value = '';
                    document.getElementById('inputExamenFisico').value = '';
                    document.getElementById('inputOtrosTests').value = '';
                    document.getElementById('inputObsLab').value = '';
                    document.getElementById('inputObsVac').value = '';
                    document.getElementById('inputReceta').value = '';
                    document.querySelectorAll('#card-consulta input[type="checkbox"]').forEach(function(cb) { cb.checked = false; });
                    msg.textContent = '¡Guardado! Cargando siguiente mascota...';
                    setTimeout(function() { msg.textContent = ''; }, 2000);
                    loadQueuePet();
                } else {
                    msg.textContent = 'Consolidando factura...';
                    consolidarFacturaMulti();
                }
            } else {
                var idConsulta = data.id_consulta;
                document.getElementById('card-consulta').classList.add('hidden');
                consultaQueue = [];
                document.getElementById('consulta-queue-info').classList.add('hidden');
                document.getElementById('inputFechaConsulta').value = '';
                document.getElementById('inputVetConsulta').value = '';
                document.getElementById('inputAuxiliarConsulta').value = '';
                document.getElementById('inputAuxiliarId').value = '';
                document.getElementById('inputPropietarioConsulta').value = '';
                inputPropId.value = '';
                document.getElementById('inputMascotaConsulta').value = '';
                document.getElementById('inputIdCita').value = '';
                document.getElementById('inputDiagnostico').value = '';
                document.getElementById('inputExamenFisico').value = '';
                document.getElementById('inputOtrosTests').value = '';
                document.getElementById('inputObsLab').value = '';
                document.getElementById('inputObsVac').value = '';
                document.getElementById('inputReceta').value = '';
                document.querySelectorAll('#card-consulta input[type="checkbox"]').forEach(function(cb) { cb.checked = false; });
                document.getElementById('mascotas-propietario-container').classList.add('hidden');
                setTimeout(function() { abrirFactura(idConsulta); }, 500);
            }
        }
    } catch(e) {
        msg.textContent = 'Error de conexión';
        msg.className = 'text-sm font-semibold text-center mb-2 text-red-600';
    }
});

window.consolidarFacturaMulti = async function() {
    var fd = new FormData();
    fd.append('action', 'consolidar_factura_multi');
    fd.append('ids', JSON.stringify(_multiPetConsultaIds));
    try {
        var resp = await fetch('/dist/content/inicio_data.php', { method: 'POST', body: fd });
        var data = await resp.json();
        if (!data.success) { alert(data.message || 'Error al consolidar factura'); return; }
        document.getElementById('card-consulta').classList.add('hidden');
        consultaQueue = [];
        _multiPetConsultaIds = [];
        document.getElementById('consulta-queue-info').classList.add('hidden');
        document.getElementById('inputFechaConsulta').value = '';
        document.getElementById('inputVetConsulta').value = '';
        document.getElementById('inputAuxiliarConsulta').value = '';
        document.getElementById('inputAuxiliarId').value = '';
        document.getElementById('inputPropietarioConsulta').value = '';
        inputPropId.value = '';
        document.getElementById('inputMascotaConsulta').value = '';
        document.getElementById('inputIdCita').value = '';
        document.getElementById('inputDiagnostico').value = '';
        document.getElementById('inputExamenFisico').value = '';
        document.getElementById('inputOtrosTests').value = '';
        document.getElementById('inputObsLab').value = '';
        document.getElementById('inputObsVac').value = '';
        document.getElementById('inputReceta').value = '';
        document.querySelectorAll('#card-consulta input[type="checkbox"]').forEach(function(cb) { cb.checked = false; });
        document.getElementById('mascotas-propietario-container').classList.add('hidden');
        idConsultaActual = data.id_consulta;
        renderMultiFactura(data);
    } catch(e) {
        alert('Error de conexion al consolidar factura');
    }
};

window.renderMultiFactura = function(data) {
    var body = document.getElementById('facturaBody');
    document.getElementById('modalFactura').classList.remove('hidden');
    document.getElementById('selectTipoPagoConsulta').value = '';
    var html = '<div class="mb-4"><h4 class="font-bold text-green-800 text-lg text-center">FACTURA CONSOLIDADA</h4></div>';
    html += '<div class="bg-green-50 rounded-xl p-4 mb-4 border border-green-100">';
    html += '<p class="text-sm"><span class="font-semibold text-gray-600">Propietario:</span> <span class="text-gray-800">' + (data.prop_nombre || '') + ' ' + (data.prop_apellido || '') + '</span></p>';
    html += '<p class="text-sm"><span class="font-semibold text-gray-600">Fecha:</span> <span class="text-gray-800">' + (data.fecha || '') + '</span></p>';
    if (data.nombre_veterinario) html += '<p class="text-sm"><span class="font-semibold text-gray-600">Veterinario:</span> <span class="text-gray-800">' + data.nombre_veterinario + '</span></p>';
    if (data.mascotas && data.mascotas.length) html += '<p class="text-sm"><span class="font-semibold text-gray-600">Pacientes (' + data.mascotas.length + '):</span> <span class="text-gray-800">' + data.mascotas.join(', ') + '</span></p>';
    html += '</div>';
    html += '<table class="w-full border-collapse mb-4">';
    html += '<tr><th class="text-left text-sm font-semibold text-green-700 bg-green-50 px-3 py-2 border border-green-100">Servicio</th><th class="text-right text-sm font-semibold text-green-700 bg-green-50 px-3 py-2 border border-green-100 w-24">Precio</th></tr>';
    (data.items || []).forEach(function(it) {
        if (it._meta) return;
        var cls = it.precio < 0 ? 'text-red-600' : '';
        html += '<tr><td class="text-sm px-3 py-2 border border-gray-200 ' + cls + '">' + it.servicio + '</td><td class="text-sm px-3 py-2 border border-gray-200 text-right ' + cls + '">$' + it.precio.toFixed(2) + '</td></tr>';
    });
    html += '<tr><td class="text-sm font-bold px-3 py-2 border border-green-200 bg-green-50">TOTAL</td><td class="text-sm font-bold px-3 py-2 border border-green-200 bg-green-50 text-right text-green-800">$' + (data.total || 0).toFixed(2) + '</td></tr>';
    if (data.total_bs && data.tasa_bcv) {
        html += '<tr><td class="text-sm font-bold px-3 py-2 border border-green-200 bg-green-50">TOTAL EN BS</td><td class="text-sm font-bold px-3 py-2 border border-green-200 bg-green-50 text-right text-green-800">Bs ' + parseFloat(data.total_bs).toFixed(2).replace('.', ',') + '</td></tr>';
    }
    html += '</table>';
    body.innerHTML = html;
};

window.descargarPDFReceta = function() {
    var receta = document.getElementById('inputReceta').value.trim();
    if (!receta) {
        alert('Escriba la receta antes de descargar el PDF.');
        return;
    }
    var id_mascota = document.getElementById('inputMascotaConsulta').value;
    var fecha = document.getElementById('inputFechaConsulta').value;
    var id_propietario = (inputPropId.value || document.getElementById('inputPropietarioConsulta').value);
    var id_veterinario = document.getElementById('inputVetConsulta').value;
    var fd = new FormData();
    fd.append('action', 'pdf_receta');
    fd.append('receta', receta);
    fd.append('id_mascota', id_mascota);
    fd.append('fecha', fecha);
    fd.append('id_propietario', id_propietario);
    fd.append('id_veterinario', id_veterinario);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/dist/content/inicio_data.php', true);
    xhr.responseType = 'blob';
    xhr.onload = function() {
        if (xhr.status === 200) {
            var blob = xhr.response;
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'receta_' + id_mascota + '_' + fecha + '.pdf';
            link.click();
        }
    };
    xhr.send(fd);
};

window.abrirFactura = async function(idConsulta) {
    idConsultaActual = idConsulta;
    var body = document.getElementById('facturaBody');
    body.innerHTML = '<div class="text-center text-gray-400 py-8">Cargando...</div>';
    document.getElementById('modalFactura').classList.remove('hidden');
    document.getElementById('selectTipoPagoConsulta').value = '';

    try {
        var resp = await fetch('/dist/content/inicio_data.php?action=detalle_consulta&id=' + idConsulta);
        var consulta = await resp.json();
        if (!consulta || !consulta.id_consulta) {
            body.innerHTML = '<div class="text-center text-red-500 py-8">Error al cargar la consulta</div>';
            return;
        }

        var items = [];
        var total = 0;

        if (consulta.ventas_servicios) {
            items = JSON.parse(consulta.ventas_servicios);
            total = parseFloat(consulta.ventas_total || 0);
        } else {
            var preResp = await fetch('/dist/content/inicio_data.php?action=cargar_precios');
            var precios = await preResp.json();

            items.push({ servicio: 'Consulta General', precio: precios['Consulta General'] || 0 });
            if (consulta.tests_rapidos && consulta.tests_rapidos.length) {
                consulta.tests_rapidos.forEach(function(t) {
                    items.push({ servicio: t, precio: precios[t] || 0 });
                });
            }
            if (consulta.laboratorio && consulta.laboratorio.length) {
                consulta.laboratorio.forEach(function(l) {
                    items.push({ servicio: l.tipo, precio: precios[l.tipo] || 0 });
                });
            }
            if (consulta.vacunas && consulta.vacunas.length) {
                consulta.vacunas.forEach(function(v) {
                    items.push({ servicio: v.nombre, precio: precios[v.nombre] || 0 });
                });
            }

            items.forEach(function(it) { total += it.precio; });
        }

        var html = '';
        html += '<div class="mb-4"><h4 class="font-bold text-green-800 text-lg text-center">FACTURA</h4></div>';
        html += '<div class="bg-green-50 rounded-xl p-4 mb-4 border border-green-100">';
        html += '<p class="text-sm"><span class="font-semibold text-gray-600">Paciente:</span> <span class="text-gray-800">' + (consulta.mascota_nombre || '') + '</span></p>';
        html += '<p class="text-sm"><span class="font-semibold text-gray-600">Propietario:</span> <span class="text-gray-800">' + ((consulta.prop_nombre || '') + ' ' + (consulta.prop_apellido || '')) + '</span></p>';
        html += '<p class="text-sm"><span class="font-semibold text-gray-600">Fecha:</span> <span class="text-gray-800">' + (consulta.fecha || '') + '</span></p>';
        if (consulta.nombre_veterinario) html += '<p class="text-sm"><span class="font-semibold text-gray-600">Veterinario:</span> <span class="text-gray-800">' + consulta.nombre_veterinario + '</span></p>';
        if (consulta.aux_nombre) html += '<p class="text-sm"><span class="font-semibold text-gray-600">Auxiliar:</span> <span class="text-gray-800">' + consulta.aux_nombre + ' ' + (consulta.aux_apellido || '') + '</span></p>';
        html += '</div>';

        html += '<table class="w-full border-collapse mb-4">';
        html += '<tr><th class="text-left text-sm font-semibold text-green-700 bg-green-50 px-3 py-2 border border-green-100">Servicio</th><th class="text-right text-sm font-semibold text-green-700 bg-green-50 px-3 py-2 border border-green-100 w-24">Precio</th></tr>';
        items.forEach(function(it) {
            html += '<tr><td class="text-sm px-3 py-2 border border-gray-200">' + it.servicio + '</td><td class="text-sm px-3 py-2 border border-gray-200 text-right">$' + it.precio.toFixed(2) + '</td></tr>';
        });
        html += '<tr><td class="text-sm font-bold px-3 py-2 border border-green-200 bg-green-50">TOTAL</td><td class="text-sm font-bold px-3 py-2 border border-green-200 bg-green-50 text-right text-green-800">$' + total.toFixed(2) + '</td></tr>';
        if (consulta.tasa_bcv && consulta.tasa_bcv > 0) {
            var bsTotal = total * consulta.tasa_bcv;
            html += '<tr><td class="text-sm font-bold px-3 py-2 border border-green-200 bg-green-50">TOTAL EN BS</td><td class="text-sm font-bold px-3 py-2 border border-green-200 bg-green-50 text-right text-green-800">Bs ' + bsTotal.toFixed(2).replace('.', ',') + '</td></tr>';
        }
        html += '</table>';

        body.innerHTML = html;
    } catch(e) {
        body.innerHTML = '<div class="text-center text-red-500 py-8">Error de conexion</div>';
    }
};

window.descargarPDFConsulta = function() {
    if (!idConsultaActual) return;
    var tipoPago = document.getElementById('selectTipoPagoConsulta').value;
    if (!tipoPago) { mostrarAdvertenciaPagoConsulta(); return; }
    var fd = new FormData();
    fd.append('action', 'procesar_factura_consulta');
    fd.append('id_consulta', idConsultaActual);
    fd.append('tipo_pago', tipoPago);
    fd.append('mode', 'descargar');
    fetch('/dist/content/inicio_data.php', { method: 'POST', body: fd })
        .then(function(r) { return r.blob(); })
        .then(function(blob) {
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'factura_' + idConsultaActual + '.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(url);
        });
};

window.enviarFacturaConsulta = function(btn) {
    if (!idConsultaActual) return;
    var tipoPago = document.getElementById('selectTipoPagoConsulta').value;
    if (!tipoPago) { mostrarAdvertenciaPagoConsulta(); return; }
    btn.disabled = true;
    btn.innerHTML = 'Enviando...';
    var fd = new FormData();
    fd.append('action', 'procesar_factura_consulta');
    fd.append('id_consulta', idConsultaActual);
    fd.append('tipo_pago', tipoPago);
    fd.append('mode', 'enviar');
    fetch('/dist/content/inicio_data.php', { method:'POST', body:fd })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (d.success) {
                alert('Factura enviada correctamente al correo del propietario');
            } else {
                alert(d.message || 'Error al enviar la factura');
            }
        })
        .catch(function() { alert('Error de conexion'); })
        .then(function() { btn.disabled = false; btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg> Enviar por correo'; });
};

window.guardarFacturaConsulta = function() {
    if (!idConsultaActual) return;
    var tipoPago = document.getElementById('selectTipoPagoConsulta').value;
    if (!tipoPago) { mostrarAdvertenciaPagoConsulta(); return; }
    var fd = new FormData();
    fd.append('action', 'procesar_factura_consulta');
    fd.append('id_consulta', idConsultaActual);
    fd.append('tipo_pago', tipoPago);
    fd.append('mode', 'guardar');
    fetch('/dist/content/inicio_data.php', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                document.getElementById('modalFactura').classList.add('hidden');
            } else {
                alert('Error: ' + (data.message || 'No se pudo guardar'));
            }
        })
        .catch(function() {
            alert('Error de conexion al guardar');
        });
};

function mostrarAdvertenciaPagoConsulta() {
    document.getElementById('modalAdvertenciaPagoConsulta').classList.remove('hidden');
}
function cerrarAdvertenciaPagoConsulta() {
    document.getElementById('modalAdvertenciaPagoConsulta').classList.add('hidden');
}
</script>

<style>
    @keyframes consultaFadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .animate-consulta {
        animation: consultaFadeIn 0.5s ease-out forwards;
    }
</style>
