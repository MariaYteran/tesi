<?php
session_start();
include 'bd.php';
$error = "";

mysqli_query($conexion, "SELECT 1 FROM clinica LIMIT 1");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $prefix = ($_POST['cedula_prefix'] ?? 'V-') === 'E-' ? 'E-' : 'V-';
  $cedula = $prefix . mysqli_real_escape_string($conexion, $_POST['cedula']);
  $Password = mysqli_real_escape_string($conexion, $_POST['Password']);

  if (empty($cedula) || empty($Password)) {
    $error = "Por favor, verifique los datos. Todos los campos son obligatorios.";
  } else {
    $tables = [
      'recepcionista' => 'id_recepcionista',
      'aux-vet' => 'id_auxiliar',
      'propietario' => 'id_propietario'
    ];
    $found = false;
    foreach ($tables as $table => $id_field) {
      $q = mysqli_query($conexion, "SELECT * FROM `$table` WHERE `$id_field`='$cedula' AND `password`='$Password' LIMIT 1");
      if (mysqli_num_rows($q) > 0) {
        $usuario = mysqli_fetch_assoc($q);
        $usuario['rol'] = $table;
        $_SESSION['usuario'] = $usuario;
        $_SESSION['mostrar_tour'] = true;
        $found = true;
        break;
      }
    }
    if ($found) {
      header("Location: ../index.php");
      exit();
    } else {
      $error = "Por favor, verifique los datos. Cédula o contraseña incorrectos.";
    }
  }
}

$error_pass = $error !== "" && (strpos($error, 'contraseña') !== false || strpos($error, 'obligatorios') !== false);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="../css/output.css" rel="stylesheet">
  <title>inicio de sesion - usuarios</title>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-600 via-emerald-500 to-teal-400 flex items-center justify-center p-4">
  
  <div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row md:h-[540px]">
    
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
      <h1 class="text-3xl font-bold text-green-800 mb-8 text-center">Iniciar Sesión</h1>
      
      <form class="space-y-5" action="login_usuarios.php" method="POST">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Cédula</label>
          <div class="flex">
            <select name="cedula_prefix"
                    class="inline-flex items-center px-3 bg-gradient-to-br from-green-600 to-emerald-600 text-white font-bold rounded-l-xl border-2 border-r-0 border-green-200 outline-none cursor-pointer">
              <option value="V-" class="text-gray-800 bg-white">V-</option>
              <option value="E-" class="text-gray-800 bg-white">E-</option>
            </select>
            <input type="text" name="cedula" placeholder="12345678" maxlength="11"
                   oninput="this.value=this.value.replace(/\D/g,'')"
                   class="flex-1 px-4 py-3 border-2 border-green-200 rounded-r-xl outline-none text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all" required>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
          <input type="password" name="Password" placeholder="Ingresa tu contraseña" 
                 class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all" required>
        </div>

        <div class="flex items-center justify-between text-sm">
          <label class="flex items-center">
            <input type="checkbox" class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-500">
            <span class="ml-2 text-gray-600">Recordarme</span>
          </label>
          <a href="#" onclick="irRecuperar()" class="text-green-600 hover:text-green-700 font-medium">¿Olvidaste tu contraseña?</a>
        </div>

        <button type="submit" 
                class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
          Iniciar Sesión
        </button>
      </form>

      <p class="mt-3 text-center">
        <a href="#" onclick="irRoles()" class="text-gray-400 hover:text-gray-600 text-sm flex items-center justify-center gap-1 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
          Ir a bienvenido
        </a>
      </p>
    </div>
  </div>

  <?php if (!empty($error)): ?>
  <div id="modal" class="fixed inset-0 bg-black/20 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-96 mx-auto overflow-hidden">
      <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5 flex items-center justify-between">
        <h3 class="text-lg font-bold text-white">Error</h3>
        <button onclick="document.getElementById('modal').remove()" class="text-red-300 hover:text-red-100 transition-colors">
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
        <p class="text-gray-600 text-sm mb-6"><?php echo $error; ?></p>
        <button onclick="document.getElementById('modal').remove()" 
                class="px-12 mx-auto bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl text-base font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md block">
          Aceptar
        </button>
      </div>
    </div>
  </div>
  <?php endif; ?>

<style>
  @keyframes fadeOutDown { from { opacity: 1; transform: translateY(0); } to { opacity: 0; transform: translateY(30px); } }
  .page-exit { animation: fadeOutDown 0.35s ease-in forwards; }
</style>
<script>
function irRecuperar() {
  document.body.classList.add('page-exit');
  setTimeout(function() { window.location.href = 'recuperar.php?tipo=usuario'; }, 350);
}
function irRoles() {
  document.body.classList.add('page-exit');
  setTimeout(function() { window.location.href = '../roles.php'; }, 350);
}
</script>
</body>
</html>
