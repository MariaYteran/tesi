<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cheetos Paws - App</title>
  <link rel="stylesheet" href="css/output.css">
  <link rel="stylesheet" href="css/animations.css">
  <style>/* small fallback */ body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif}</style>
</head>
<body class="bg-gray-100">
  <?php include 'dist/partials/menu.php'; ?>

  <main id="app" class="ml-64 p-6">
    <?php
      $initial = 'dist/content/home.html';
      if (file_exists($initial)) {
        include $initial;
      } else {
        echo '<h2 class="text-2xl font-bold">Bienvenido</h2><p>Contenido inicial no encontrado.</p>';
      }
    ?>
  </main>

  <script src="app.js" defer></script>
</body>
</html>
