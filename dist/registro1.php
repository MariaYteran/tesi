<?php
session_start();
include 'bd.php';
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $Nombre_clinic = mysqli_real_escape_string($conexion, $_POST['Nombre_clinic']);
  $RIF_clinica = 'J-' . mysqli_real_escape_string($conexion, $_POST['RIF_clinica']);
  $Nombres = mysqli_real_escape_string($conexion, $_POST['Nombres']);
  $Id_veterinario = $_POST['Id_veterinario_prefix'] . mysqli_real_escape_string($conexion, $_POST['Id_veterinario']);
  $email_user = mysqli_real_escape_string($conexion, $_POST['email_user']);
  if ($_POST['email_domain'] === 'otros') {
      $email_domain = '@' . mysqli_real_escape_string($conexion, $_POST['email_domain_custom']);
  } else {
      $email_domain = $_POST['email_domain'];
  }
  $Gmail = $email_user . $email_domain;
  $Password = mysqli_real_escape_string($conexion, $_POST['Password']);

  if (empty($Nombre_clinic) || empty($RIF_clinica) || empty($Nombres) || empty($Id_veterinario) || empty($Gmail) || empty($Password)) {
    $error = "Por favor, verifique los datos. Todos los campos son obligatorios.";
  } else {
    $vet_existe = mysqli_query($conexion, "SELECT Id_veterinario FROM veterinario WHERE Id_veterinario='$Id_veterinario'");
    if (mysqli_num_rows($vet_existe) > 0) {
      $error = "Por favor, verifique los datos. El ID de veterinario ya está registrado.";
    } else {
      $clinica_existe = mysqli_query($conexion, "SELECT RIF_clinica FROM clinica WHERE RIF_clinica='$RIF_clinica'");
      if (mysqli_num_rows($clinica_existe) > 0) {
        // Clinica exists — add new vet as regular member
        $insertar = mysqli_query($conexion, "INSERT INTO veterinario (RIF_clinica, Nombres, Id_veterinario, Gmail, Password, rol) VALUES ('$RIF_clinica', '$Nombres', '$Id_veterinario', '$Gmail', '$Password', 'vet')");
      } else {
        // New clinic — create clinica entry + first vet as admin
        $insertar = mysqli_query($conexion, "INSERT INTO clinica (RIF_clinica, nombre) VALUES ('$RIF_clinica', '$Nombre_clinic')");
        if ($insertar) {
          $insertar = mysqli_query($conexion, "INSERT INTO veterinario (RIF_clinica, Nombres, Id_veterinario, Gmail, Password, rol) VALUES ('$RIF_clinica', '$Nombres', '$Id_veterinario', '$Gmail', '$Password', 'admin')");
        }
      }
      if ($insertar) {
        $success = "Registro exitoso. Ahora puedes iniciar sesión.";
      } else {
        $error = "Por favor, verifique los datos. Ocurrió un error al registrar.";
      }
    }
  }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="../css/output.css" rel="stylesheet">
  <title>Registros Cheetos Paws</title>
  <style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .page-enter { animation: fadeInUp 0.5s ease-out forwards; }
    @keyframes fadeOutDown { from { opacity: 1; transform: translateY(0); } to { opacity: 0; transform: translateY(30px); } }
    .page-exit { animation: fadeOutDown 0.35s ease-in forwards; }
    .scroll-form::-webkit-scrollbar { width: 5px; }
    .scroll-form::-webkit-scrollbar-track { background: #f0fdf4; border-radius: 8px; }
    .scroll-form::-webkit-scrollbar-thumb { background: #86efac; border-radius: 8px; }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-600 via-emerald-500 to-teal-400 flex items-center justify-center p-4">
  
    <div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row md:h-[540px] page-enter">
    
    <div class="md:w-1/2 bg-gradient-to-br from-green-700 to-emerald-600 p-8 flex flex-col items-center justify-center relative overflow-hidden">
      <div class="absolute inset-0 opacity-20">
        <div class="absolute top-10 left-10 w-32 h-32 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-40 h-40 bg-teal-300 rounded-full blur-3xl"></div>
      </div>
      <h2 class="text-white text-3xl font-bold mb-1 relative z-10">Unete a nuestro equipo</h2>
      <img src="../src/imagenes/logo1.png" alt="Animal" class="w-52 h-52 object-cover object-top rounded-full border-8 border-white/30 shadow-2xl mb-3 -translate-y-1 relative z-10">
      
      <p class="text-green-100 text-center relative z-10">crea tu usuario en cheetos paws</p>
    </div>

    <div class="md:w-1/2 p-8 md:p-12 flex flex-col min-h-0">
      <h1 class="text-3xl font-bold text-green-800 text-center">Registrarse</h1>
      
      <form class="mt-6 flex flex-col flex-1 min-h-0" action="registro1.php" method="POST">
        <div class="overflow-y-auto scroll-form space-y-3 pr-1 flex-1 min-h-0">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de la clinica</label>
            <input type="text" name="Nombre_clinic" placeholder="Ingresa el nombre" 
                   class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all" required>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Rif de la clinica</label>
            <div class="flex">
              <span class="inline-flex items-center px-4 bg-gradient-to-br from-green-600 to-emerald-600 text-white font-bold rounded-l-xl border-2 border-r-0 border-green-200">J-</span>
              <input type="text" name="RIF_clinica" placeholder="123456789" maxlength="15"
                     oninput="this.value=this.value.replace(/\D/g,'')"
                     class="flex-1 px-4 py-3 border-2 border-green-200 rounded-r-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all" required>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nombres del usuario</label>
            <input type="text" name="Nombres" placeholder="Ingresar Nombres" 
                   class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all" required>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cédula del veterinario</label>
            <div class="flex">
              <div class="relative rounded-l-xl border-2 border-r-0 border-green-200 bg-gradient-to-br from-green-600 to-emerald-600">
                <select name="Id_veterinario_prefix"
                        class="appearance-none bg-transparent text-white font-bold pl-4 pr-8 py-3 cursor-pointer outline-none">
                  <option value="V-" class="text-gray-800 bg-white">V-</option>
                  <option value="E-" class="text-gray-800 bg-white">E-</option>
                </select>
                <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                  <path d="M6 9l6 6 6-6"/>
                </svg>
              </div>
              <input type="text" name="Id_veterinario" placeholder="12345678" maxlength="15"
                     oninput="this.value=this.value.replace(/\D/g,'')"
                     class="flex-1 px-4 py-3 border-2 border-green-200 rounded-r-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all" required>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Correo electrónico</label>
            <div class="flex w-full overflow-hidden">
              <input type="text" name="email_user" placeholder="correo personal"
                     class="flex-1 min-w-0 px-4 py-3 border-2 border-green-200 border-r-0 rounded-l-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all" required>
              <div class="relative rounded-r-xl border-2 border-l-0 border-green-200 bg-gradient-to-br from-green-600 to-emerald-600 flex-shrink-0 w-24">
                <select name="email_domain" id="email-domain-select"
                        class="appearance-none bg-transparent text-white font-bold pl-4 pr-8 py-3 cursor-pointer outline-none h-full">
                  <option value="@gmail.com" class="text-gray-800 bg-white">@gmail.com</option>
                  <option value="@hotmail.com" class="text-gray-800 bg-white">@hotmail.com</option>
                  <option value="@outlook.com" class="text-gray-800 bg-white">@outlook.com</option>
                  <option value="@yahoo.com" class="text-gray-800 bg-white">@yahoo.com</option>
                  <option value="otros" class="text-gray-800 bg-white">Otros</option>
                </select>
                <input type="text" name="email_domain_custom" id="email-domain-custom" placeholder="ejemplo.com"
                       class="h-full px-4 py-3 rounded-r-xl outline-none text-white placeholder-white/70 focus:border-green-500 transition-all bg-transparent"
                       style="display:none; position:absolute; inset:0; width:100%;">
                <svg id="domain-arrow" class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
            <input type="password" name="Password" placeholder="Ingresa tu contraseña" 
                   class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all" required>
          </div>
        </div>

        <button type="submit" 
                class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 mt-4">
          Registrarse
        </button>
      </form>

      <p class="mt-4 text-center text-gray-600">
        ¿Ya tienes cuenta?
        <a href="#" onclick="irLogin()" class="text-green-600 font-semibold hover:text-green-700">Volver al inicio de sesión</a>
      </p>
    </div>
  </div>

  <?php if (!empty($error) || !empty($success)): ?>
  <div id="modal" class="fixed inset-0 bg-black/20 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-96 mx-auto overflow-hidden">
      <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5 flex items-center justify-between">
        <h3 class="text-lg font-bold text-white"><?php echo !empty($error) ? 'Error' : 'Éxito'; ?></h3>
        <button onclick="document.getElementById('modal').remove()" class="<?php echo !empty($error) ? 'text-red-300 hover:text-red-100' : 'text-green-200 hover:text-white'; ?> transition-colors">
          <?php if (!empty($error)): ?>
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
          <?php else: ?>
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <?php endif; ?>
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
        <button onclick="document.getElementById('modal').remove()" 
                class="px-12 mx-auto bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl text-base font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md block">
          Aceptar
        </button>
        <?php else: ?>
        <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <p class="text-gray-600 text-sm mb-6"><?php echo $success; ?></p>
        <button onclick="window.location.href='login.php'" 
                class="px-12 mx-auto bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl text-base font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md block">
          Ir al Login
        </button>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

<script>
document.addEventListener('change', function(e) {
    if (e.target.id === 'email-domain-select') {
        const select = e.target;
        const custom = document.getElementById('email-domain-custom');
        const arrow = document.getElementById('domain-arrow');
        if (select.value === 'otros') {
            select.style.display = 'none';
            custom.style.display = 'block';
            arrow.style.display = 'none';
            custom.focus();
        }
    }
});
document.addEventListener('blur', function(e) {
    if (e.target.id === 'email-domain-custom' && e.target.value.trim() === '') {
        const select = document.getElementById('email-domain-select');
        const arrow = document.getElementById('domain-arrow');
        e.target.style.display = 'none';
        select.style.display = 'block';
        select.value = '@gmail.com';
        arrow.style.display = 'block';
    }
}, true);

function irLogin() {
  document.body.classList.add('page-exit');
  setTimeout(function() { window.location.href = 'login.php'; }, 350);
}
</script>

</body>
</html>