<?php
$mostrarTour = isset($_SESSION['mostrar_tour']) && $_SESSION['mostrar_tour'];
if ($mostrarTour) {
    $_SESSION['mostrar_tour'] = false;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="/">
  <title>Cheetos Paws - App</title>
  <link rel="stylesheet" href="css/output.css">
  <link rel="stylesheet" href="css/animations.css">
  <style>/* small fallback */ body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif}</style>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gray-100">
  <?php include 'dist/partials/menu.php'; ?>

  <main id="app" class="ml-64 p-6">
    <?php
      $rol_actual = $_SESSION['usuario']['rol'] ?? '';
      $initial = $rol_actual === 'propietario' ? 'dist/content/historia.php' : 'dist/content/inicio.php';
      if (file_exists($initial)) {
        include $initial;
      } else {
        echo '<h2 class="text-2xl font-bold">Bienvenido</h2><p>Contenido inicial no encontrado.</p>';
      }
    ?>
  </main>
  
  <script src="app.js?v=2" defer></script>

  <!-- Tour de bienvenida -->
  <div id="tour-modal" style="display:none;">
    <div class="tour-welcome">
      <span class="tour-welcome-title">¡Bienvenido!</span>
      <span class="tour-welcome-text">A continuación, te mostraremos un breve recorrido por cada módulo del sistema para que conozcas sus funciones principales. Si ya estás familiarizado, puedes omitir esta guía presionando el botón Omitir.</span>
      <button onclick="omitirTour()" class="tour-welcome-btn">Omitir</button>
    </div>
  </div>

  <div id="tour-toast" class="fixed z-[9998]" style="display:none;">
    <span class="tour-name"></span>
    <span class="tour-desc"></span>
  </div>

  <style>
    .tour-highlight {
      position: relative;
      z-index: 9997;
      box-shadow: 0 0 0 2px #22c55e, 0 0 20px rgba(34,197,94,0.6) !important;
      border-radius: 12px;
    }
    #tour-toast {
      left: 276px;
      max-width: 360px;
      background: #1e293b;
      color: #e2e8f0;
      padding: 14px 18px;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.35);
      font-size: 13px;
      line-height: 1.5;
      transition: opacity 0.35s ease, transform 0.35s ease;
      opacity: 0;
      transform: translateX(-12px);
      pointer-events: none;
    }
    #tour-toast.show {
      opacity: 1;
      transform: translateX(0);
    }
    #tour-toast::before {
      content: '';
      position: absolute;
      left: -7px;
      top: 50%;
      transform: translateY(-50%);
      border: 7px solid transparent;
      border-right-color: #1e293b;
    }
    #tour-toast .tour-name {
      font-weight: 700;
      color: #22c55e;
      display: block;
      margin-bottom: 4px;
      font-size: 14px;
    }
    #tour-toast .tour-desc {
      display: block;
    }
    #tour-modal {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 9999;
    }
    .tour-welcome {
      background: #1e293b;
      color: #e2e8f0;
      padding: 20px 24px;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.35);
      max-width: 380px;
      text-align: center;
      font-size: 13px;
      line-height: 1.5;
      transition: opacity 0.35s ease, transform 0.35s ease;
    }
    .tour-welcome-title {
      font-weight: 700;
      color: #22c55e;
      display: block;
      margin-bottom: 8px;
      font-size: 16px;
    }
    .tour-welcome-text {
      display: block;
      margin-bottom: 16px;
    }
    .tour-welcome-btn {
      background: linear-gradient(to right, #16a34a, #059669);
      color: white;
      border: none;
      padding: 8px 24px;
      border-radius: 10px;
      font-weight: 600;
      font-size: 13px;
      cursor: pointer;
      transition: opacity 0.2s;
    }
    .tour-welcome-btn:hover {
      opacity: 0.9;
    }
  </style>

  <script>
    (function() {
      if (!<?php echo json_encode($mostrarTour); ?>) return;

      var steps = [
        { selector: 'a[data-link][href*="inicio.php"]', name: 'Inicio', desc: 'En esta parte del sistema podrás visualizar la cantidad de usuarios agendados, agendar citas, ver calendario de citas u actividades especiales, y atender emergencias.' },
        { selector: 'a[data-link][href*="info.php"]', name: 'Registros', desc: 'Aquí podrás registrar los diferentes roles del sistema.' },
        { selector: 'a[data-link][href*="consultas.php"]', name: 'Consultas', desc: 'En este apartado, se visualizarán las citas agendadas anteriormente para generar la consulta, también podrás hacer multi-consultas de las mascotas con su propietario.' },
        { selector: 'a[data-link][href*="historia.php"]', name: 'Historial Médico', desc: 'Aquí podrás visualizar todas las consultas de las mascotas con sus detalles.' },
        { selector: 'a[data-link][href*="inventario.php"]', name: 'Inventario', desc: 'Esta sección podrás visualizar la cantidad de productos, de proveedores y los productos estrella, además de registrar nuevos productos y proveedores.' },
        { selector: 'a[data-link][href*="ventas.php"]', name: 'Ventas', desc: 'Realizarás las ventas de tus productos, además de visualizar el historial de ventas y registrar clientes nuevos.' },
        { selector: 'a[data-link][href*="reportes.php"]', name: 'Reportes', desc: 'En este apartado, podrás ver los reportes con respecto a los tipos de pagos, detalles de las consultas, inventario y consultas.' },
        { selector: 'a[data-link][href*="ajustes.php"]', name: 'Ajustes', desc: 'En este módulo se registran los administradores y se establecen los precios por los servicios prestados.' },
        { selector: 'nav a[href*="logout.php"]', name: 'Cerrar Sesión', desc: 'En este botón estarás cerrando tu sesión para vernos nuevamente en otro momento.' }
      ];

      var stepTimer, startTimer;
      var modal = document.getElementById('tour-modal');
      var toast = document.getElementById('tour-toast');

      function omitirTour() {
        clearTimeout(startTimer);
        clearTimeout(stepTimer);
        limpiarPaso();
        modal.style.display = 'none';
        toast.style.display = 'none';
        // session handles persistence
      }
      window.omitirTour = omitirTour;

      function limpiarPaso() {
        document.querySelectorAll('.tour-highlight').forEach(function(el) {
          el.classList.remove('tour-highlight');
        });
        document.querySelectorAll('.submenu.open').forEach(function(el) {
          el.classList.remove('open');
          var flecha = el.closest('li') && el.closest('li').querySelector('.arrow');
          if (flecha) flecha.classList.remove('rotated');
        });
        toast.classList.remove('show');
      }

      function mostrarPaso(index) {
        if (index >= steps.length) {
          omitirTour();
          return;
        }
        limpiarPaso();
        var paso = steps[index];
        var el = document.querySelector(paso.selector);
        var restringido = el && el.dataset.restricted === 'true';

        if (el && !restringido) {
          var submenu = el.closest('.submenu');
          if (submenu && !submenu.classList.contains('open')) {
            submenu.classList.add('open');
            var flecha = el.closest('li') && el.closest('li').querySelector('.arrow');
            if (flecha) flecha.classList.add('rotated');
          }
          el.classList.add('tour-highlight');
          el.scrollIntoView({ block: 'center', behavior: 'smooth' });
          var rect = el.getBoundingClientRect();
          var top = rect.top + rect.height / 2 - 20;
          top = Math.max(16, Math.min(top, window.innerHeight - 100));
          toast.style.top = top + 'px';
        } else {
          toast.style.top = '120px';
        }

        toast.querySelector('.tour-name').textContent = paso.name;
        toast.querySelector('.tour-desc').textContent = paso.desc;
        toast.style.display = 'block';
        void toast.offsetWidth;
        toast.classList.add('show');

        stepTimer = setTimeout(function() { mostrarPaso(index + 1); }, 4500);
      }

      modal.style.display = 'flex';
      startTimer = setTimeout(function() {
        modal.style.display = 'none';
        mostrarPaso(0);
      }, 2000);
    })();
  </script>
</body>
</html>
