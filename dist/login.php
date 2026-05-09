<?php
session_start();
include 'bd.php';
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $RIF_clinica = mysqli_real_escape_string($conexion, $_POST['RIF_clinica']);
  $Password = mysqli_real_escape_string($conexion, $_POST['Password']);

  if (empty($RIF_clinica) || empty($Password)) {
    $error = "Por favor, verifique los datos. Todos los campos son obligatorios.";
  } else {
    $query = mysqli_query($conexion, "SELECT * FROM veterinario WHERE RIF_clinica='$RIF_clinica' AND Password='$Password'");
    if (mysqli_num_rows($query) > 0) {
      $usuario = mysqli_fetch_assoc($query);
      $_SESSION['usuario'] = $usuario;
      header("Location: menu.html");
      exit();
    } else {
      $error = "Por favor, verifique los datos. RIF o contraseña incorrectos.";
    }
  }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="./style.css" rel="stylesheet">
  <title>inicio de sesion</title>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-600 via-emerald-500 to-teal-400 flex items-center justify-center p-4">
  
  <div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row">
    
    <div class="md:w-1/2 bg-gradient-to-br from-green-700 to-emerald-600 p-8 flex flex-col items-center justify-center relative overflow-hidden">
      <div class="absolute inset-0 opacity-20">
        <div class="absolute top-10 left-10 w-32 h-32 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-40 h-40 bg-teal-300 rounded-full blur-3xl"></div>
      </div>
      <h2 class="text-white text-3xl font-bold mb-2 relative z-10">Bienvenido</h2>
      
      <img src="../src/imagenes/Imagen1.png" alt="Animal" class="w-48 h-48 object-cover rounded-full border-8 border-white/30 shadow-2xl mb-6 relative z-10">
      
      <p class="text-green-100 text-center relative z-10">Dedicados al bienestar de los mas vulnerables</p>
    </div>

    <div class="md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
      <h1 class="text-3xl font-bold text-green-800 mb-8 text-center">Iniciar Sesión</h1>
      
      <form class="space-y-5" action="login.php" method="POST">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Rif de la clinica</label>
          <input type="text" name="RIF_clinica" placeholder="Ingresar el Rif" 
                 class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all" required>
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
          <a href="#" class="text-green-600 hover:text-green-700 font-medium">¿Olvidaste tu contraseña?</a>
        </div>

        <button type="submit" 
                class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
          Iniciar Sesión
        </button>
      </form>

      <p class="mt-6 text-center text-gray-600">
        ¿No tienes cuenta? 
        <a href="registro1.php" class="text-green-600 font-semibold hover:text-green-700">Regístrate</a>
      </p>
    </div>
  </div>

  <?php if (!empty($error)): ?>
  <div id="modal" class="fixed inset-0 bg-black/20 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-6 max-w-xs w-full mx-4">
        <div class="text-center">
        <h3 class="text-lg font-bold text-gray-800 mb-2">Error</h3>
        <p class="text-gray-600 text-sm mb-4"><?php echo $error; ?></p>
        <button onclick="document.getElementById('modal').remove()" 
                class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-2 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all text-sm">
          Aceptar
        </button>
      </div>
    </div>
  </div>
  <?php endif; ?>

</body>
</html>
