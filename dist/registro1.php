<?php
session_start();
include 'bd.php';
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $Nombre_clinic = mysqli_real_escape_string($conexion, $_POST['Nombre_clinic']);
  $RIF_clinica = mysqli_real_escape_string($conexion, $_POST['RIF_clinica']);
  $Nombres = mysqli_real_escape_string($conexion, $_POST['Nombres']);
  $Id_veterinario = mysqli_real_escape_string($conexion, $_POST['Id_veterinario']);
  $Gmail = mysqli_real_escape_string($conexion, $_POST['Gmail']);
  $Password = mysqli_real_escape_string($conexion, $_POST['Password']);

  if (empty($Nombre_clinic) || empty($RIF_clinica) || empty($Nombres) || empty($Id_veterinario) || empty($Gmail) || empty($Password)) {
    $error = "Por favor, verifique los datos. Todos los campos son obligatorios.";
  } else {
    $verificar = mysqli_query($conexion, "SELECT * FROM veterinario WHERE Id_veterinario='$Id_veterinario' OR RIF_clinica='$RIF_clinica'");
    if (mysqli_num_rows($verificar) > 0) {
      $error = "Por favor, verifique los datos. El RIF o ID ya están registrados.";
    } else {
      $insertar = mysqli_query($conexion, "INSERT INTO veterinario (RIF_clinica, Nombre_clinic, Nombres, Id_veterinario, Gmail, Password) VALUES ('$RIF_clinica', '$Nombre_clinic', '$Nombres', '$Id_veterinario', '$Gmail', '$Password')");
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
  <link href="./style.css" rel="stylesheet">
  <title>Registros Cheetos Paws</title>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-600 via-emerald-500 to-teal-400 flex items-center justify-center p-4">
  
  <div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row">
    
    <div class="md:w-1/2 bg-gradient-to-br from-green-700 to-emerald-600 p-8 flex flex-col items-center justify-center relative overflow-hidden">
      <div class="absolute inset-0 opacity-20">
        <div class="absolute top-10 left-10 w-32 h-32 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-40 h-40 bg-teal-300 rounded-full blur-3xl"></div>
      </div>
      <h2 class="text-white text-3xl font-bold mb-2  relative z-10 -mt-5">Unete a nuestro equipo</h2>
      <img src="../src/imagenes/Imagen1.png" alt="Animal" class="w-48 h-48 object-cover rounded-full border-8 border-white/30 shadow-2xl mb-6 relative z-10">
      
      <p class="text-green-100 text-center relative z-10">crea tu usuario en cheetos paws</p>
    </div>

    <div class="md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
      <h1 class="text-3xl font-bold text-green-800 mb-8 text-center">Registrarse</h1>
      
      <form class="space-y-2" action="registro1.php" method="POST">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de la clinica</label>
          <input type="text" name="Nombre_clinic" placeholder="Ingresa el nombre" 
                 class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all" required>
        </div>

        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Rif de la clinica</label>
          <input type="text" name="RIF_clinica" placeholder="Ingresa el rif" 
                 class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all" required>
        </div>


        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Nombres del usuario</label>
          <input type="text" name="Nombres" placeholder="Ingresar Nombres" 
                 class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all" required>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Id veterinario</label>
          <input type="text" name="Id_veterinario" placeholder="Ingresar Id" 
                 class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all" required>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Gmail</label>
          <input type="email" name="Gmail" placeholder="Ingresa Gmail" 
                 class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all" required>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
          <input type="password" name="Password" placeholder="Ingresa tu contraseña" 
                 class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all" required>
        </div>

       
        <button type="submit" 
                class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-3">
          Registrarse
        </button>
      </form>

      
    </div>
  </div>

  <?php if (!empty($error) || !empty($success)): ?>
  <div id="modal" class="fixed inset-0 bg-black/20 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-6 max-w-xs w-full mx-4">
      <div class="text-center">
        <?php if (!empty($error)): ?>
        <h3 class="text-lg font-bold text-gray-800 mb-2">Error</h3>
        <p class="text-gray-600 text-sm mb-4"><?php echo $error; ?></p>
        <button onclick="document.getElementById('modal').remove()" 
                class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-2 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all text-sm">
          Aceptar
        </button>
        <?php else: ?>
        <h3 class="text-lg font-bold text-gray-800 mb-2">Éxito</h3>
        <p class="text-gray-600 text-sm mb-4"><?php echo $success; ?></p>
        <button onclick="window.location.href='login.php'" 
                class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-2 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all text-sm">
          Ir al Login
        </button>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

</body>
</html>
