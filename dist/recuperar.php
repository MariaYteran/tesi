<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="../css/output.css" rel="stylesheet">
  <title>Recuperación de contraseña</title>
  <?php $tipo = $_GET['tipo'] ?? 'admin'; ?>
  <style>
    .slide-container { position: relative; overflow: hidden; }
    .slide-step { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); position: absolute; width: 100%; opacity: 0; pointer-events: none; }
    .slide-step.active { position: relative; opacity: 1; pointer-events: auto; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .page-enter { animation: fadeInUp 0.5s ease-out forwards; }
    @keyframes fadeOutDown { from { opacity: 1; transform: translateY(0); } to { opacity: 0; transform: translateY(30px); } }
    .page-exit { animation: fadeOutDown 0.35s ease-in forwards; }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-600 via-emerald-500 to-teal-400 flex items-center justify-center p-4">

  <div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row page-enter">

    <div class="md:w-1/2 bg-gradient-to-br from-green-700 to-emerald-600 p-8 flex flex-col items-center justify-center relative overflow-hidden">
      <div class="absolute inset-0 opacity-20">
        <div class="absolute top-10 left-10 w-32 h-32 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-40 h-40 bg-teal-300 rounded-full blur-3xl"></div>
      </div>
      <h2 class="text-white text-3xl font-bold mb-1 relative z-10">Bienvenido</h2>
      <img src="../src/imagenes/logo1.png" alt="Animal" class="w-52 h-52 object-cover object-top rounded-full border-8 border-white/30 shadow-2xl mb-3 -translate-y-1 relative z-10">
      <p class="text-green-100 text-center relative z-10">Dedicados al bienestar de los mas vulnerables</p>
    </div>

    <div class="md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
      <h1 class="text-3xl font-bold text-green-800 mb-2 text-center">Recuperación de contraseña</h1>

      <div class="slide-container relative min-h-[260px] mt-6">
        <!-- Paso 1: Cédula -->
        <div id="step-1" class="slide-step active" data-slide="0">
          <div class="space-y-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Cédula</label>
              <div class="flex">
                <select id="cedula-prefix"
                        class="inline-flex items-center px-3 bg-gradient-to-br from-green-600 to-emerald-600 text-white font-bold rounded-l-xl border-2 border-r-0 border-green-200 outline-none cursor-pointer">
                  <option value="V-" class="text-gray-800 bg-white">V-</option>
                  <option value="E-" class="text-gray-800 bg-white">E-</option>
                </select>
                <input id="input-cedula" type="text" placeholder="12345678"
                       oninput="this.value=this.value.replace(/\D/g,'')"
                       class="flex-1 px-4 py-3 border-2 border-green-200 rounded-r-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all">
              </div>
            </div>
            <button onclick="enviarCodigoRecuperacion()" id="btn-paso1"
                    class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
              Enviar código
            </button>
          </div>
        </div>

        <!-- Paso 2: Código -->
        <div id="step-2" class="slide-step" data-slide="1">
          <div class="space-y-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Código de verificación</label>
              <input id="input-codigo" type="text" placeholder="Ingresa el código"
                     class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all">
            </div>
            <button onclick="verificarCodigoRecuperacion()" id="btn-paso2"
                    class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
              Verificar código
            </button>
          </div>
        </div>

        <!-- Paso 3: Nueva contraseña -->
        <div id="step-3" class="slide-step" data-slide="2">
          <p class="text-gray-600 text-sm text-center mb-6">
            Restablece tu contraseña
          </p>
          <div class="space-y-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Nueva contraseña</label>
              <input id="input-password" type="password" placeholder="Ingresa tu nueva contraseña"
                     class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all">
            </div>
            <button onclick="cambiarPasswordRecuperacion()" id="btn-paso3"
                    class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
              Aceptar
            </button>
          </div>
        </div>
      </div>

      <p class="mt-6 text-center text-gray-600">
          <a href="#" onclick="irLogin()" class="text-green-600 font-semibold hover:text-green-700">Volver al inicio de sesión</a>
      </p>
    </div>
  </div>

  <!-- Modal error -->
  <div id="modal-error" class="fixed inset-0 bg-black/20 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-xl w-96 mx-auto overflow-hidden">
      <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5 flex items-center justify-between">
        <h3 class="text-lg font-bold text-white">Error</h3>
        <button onclick="cerrarModal()" class="text-red-300 hover:text-red-100 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div class="px-8 py-8 text-center">
        <div class="w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <p id="modal-mensaje" class="text-gray-600 text-sm mb-6">El campo se encuentra vacío</p>
        <button onclick="cerrarModal()"
                class="px-12 mx-auto bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl text-base font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md block">
          Aceptar
        </button>
      </div>
    </div>
  </div>

  <script>
    var pasoActual = 1;

    function irPaso(siguiente, inputId) {
      if (inputId) {
        var input = document.getElementById(inputId);
        if (!input.value.trim()) {
          mostrarModal('El campo se encuentra vacío');
          return;
        }
      }

      if (siguiente === 4) {
        window.location.href = 'login.php';
        return;
      }

      var actual = document.getElementById('step-' + pasoActual);
      var sig = document.getElementById('step-' + siguiente);

      var actualSlide = parseInt(actual.getAttribute('data-slide'));
      var sigSlide = parseInt(sig.getAttribute('data-slide'));

      var direccion = sigSlide > actualSlide ? 1 : -1;

      actual.classList.remove('active');
      actual.style.transform = 'translateX(' + (-direccion * 100) + '%)';
      actual.style.opacity = '0';
      actual.style.position = 'absolute';

      sig.style.transform = 'translateX(' + (direccion * 100) + '%)';
      sig.style.opacity = '0';
      sig.style.position = 'absolute';
      sig.classList.add('active');

      requestAnimationFrame(function() {
        sig.style.transform = 'translateX(0)';
        sig.style.opacity = '1';
        sig.style.position = 'relative';

        actual.style.position = 'absolute';
      });

      pasoActual = siguiente;
    }

    function mostrarModal(mensaje) {
      document.getElementById('modal-mensaje').textContent = mensaje;
      document.getElementById('modal-error').classList.remove('hidden');
    }

    function cerrarModal() {
      document.getElementById('modal-error').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') cerrarModal();
    });

    function enviarCodigoRecuperacion() {
      var prefix = document.getElementById('cedula-prefix').value;
      var input = document.getElementById('input-cedula');
      if (!input.value.trim()) { mostrarModal('Ingrese su cédula'); return; }
      var btn = document.getElementById('btn-paso1');
      btn.disabled = true; btn.textContent = 'Enviando...';
      var fd = new FormData();
      fd.append('action', 'recuperar_enviar_codigo');
      fd.append('id_veterinario', prefix + input.value.trim());
      fd.append('tipo', '<?php echo $tipo; ?>');
      fetch('/dist/content/inicio_data.php', { method:'POST', body:fd })
        .then(function(r) { return r.json(); })
        .then(function(d) {
          if (d.success) {
            irPaso(2, '');
          } else {
            mostrarModal(d.message || 'Error al enviar el código');
          }
        })
        .catch(function() { mostrarModal('Error de conexión'); })
        .then(function() { btn.disabled = false; btn.textContent = 'Enviar código'; });
    }

    function verificarCodigoRecuperacion() {
      var input = document.getElementById('input-codigo');
      if (!input.value.trim()) { mostrarModal('Ingrese el código'); return; }
      var btn = document.getElementById('btn-paso2');
      btn.disabled = true; btn.textContent = 'Verificando...';
      var fd = new FormData();
      fd.append('action', 'recuperar_verificar_codigo');
      fd.append('codigo', input.value.trim());
      fetch('/dist/content/inicio_data.php', { method:'POST', body:fd })
        .then(function(r) { return r.json(); })
        .then(function(d) {
          if (d.success) {
            irPaso(3, '');
          } else {
            mostrarModal(d.message || 'Código incorrecto');
          }
        })
        .catch(function() { mostrarModal('Error de conexión'); })
        .then(function() { btn.disabled = false; btn.textContent = 'Verificar código'; });
    }

    function cambiarPasswordRecuperacion() {
      var input = document.getElementById('input-password');
      if (!input.value.trim()) { mostrarModal('Ingrese la nueva contraseña'); return; }
      var btn = document.getElementById('btn-paso3');
      btn.disabled = true; btn.textContent = 'Guardando...';
      var fd = new FormData();
      fd.append('action', 'recuperar_cambiar_pass');
      fd.append('password', input.value.trim());
      fetch('/dist/content/inicio_data.php', { method:'POST', body:fd })
        .then(function(r) { return r.json(); })
        .then(function(d) {
          if (d.success) {
            alert('Contraseña actualizada correctamente');
            document.body.classList.add('page-exit');
            setTimeout(function() { window.location.href = '<?php echo $tipo === 'usuario' ? 'login_usuarios.php' : 'login.php'; ?>'; }, 350);
          } else {
            mostrarModal(d.message || 'Error al cambiar la contraseña');
          }
        })
        .catch(function() { mostrarModal('Error de conexión'); })
        .then(function() { btn.disabled = false; btn.textContent = 'Aceptar'; });
    }

    function irLogin() {
      document.body.classList.add('page-exit');
      setTimeout(function() { window.location.href = '<?php echo $tipo === 'usuario' ? 'login_usuarios.php' : 'login.php'; ?>'; }, 350);
    }
  </script>

</body>
</html>