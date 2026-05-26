<?php
$rol_menu = $_SESSION['usuario']['rol'] ?? '';
$permisos = [
    'recepcionista' => ['inicio', 'inventario', 'ventas'],
    'aux-vet'       => ['inicio', 'consultas', 'historias'],
    'propietario'   => ['historias'],
    'admin'         => ['*'],
];
function tienePermiso($modulo, $rol, $permisos) {
    if ($rol === 'admin') return true;
    return isset($permisos[$rol]) && in_array($modulo, $permisos[$rol]);
}
?>
<nav class="fixed left-0 top-0 h-screen bg-gradient-to-b from-green-700 to-emerald-700 w-64 shadow-2xl rounded-r-3xl flex flex-col z-50">
  <div class="p-6 border-b border-white/10">
    <div class="flex items-center gap-3">
      <img src="src/imagenes/29.jpg" alt="Logo" class="w-12 h-12 rounded-full object-cover border-2 border-white/30">
      <div>
        <h1 class="text-white font-bold text-lg">Cheetos Paws</h1>
        <p class="text-green-200 text-xs">Sistema Veterinario</p>
      </div>
    </div>
  </div>

  <ul class="p-4 space-y-1 flex-1 overflow-y-auto">
    <li>
      <a href="dist/content/inicio.php" data-link data-modulo="inicio"
         <?php if (!tienePermiso('inicio', $rol_menu, $permisos)): ?>data-restricted="true" title="Este módulo no está permitido para tu usuario"<?php endif; ?>
         class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-white">
        <svg class="w-5 h-5 opacity-80 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <span class="text-sm font-medium">Inicio</span>
      </a>
    </li>

    <li>
      <a href="dist/content/info.php" data-link data-modulo="registros"
         onclick="toggleSubmenu('submenu-registros', this)"
         <?php if (!tienePermiso('registros', $rol_menu, $permisos)): ?>data-restricted="true" title="Este módulo no está permitido para tu usuario"<?php endif; ?>
         class="menu-toggle flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-white cursor-pointer">
        <svg class="w-5 h-5 opacity-80 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
        <span class="text-sm font-medium flex-1">Registros</span>
        <svg class="arrow w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </a>
      <ul id="submenu-registros" class="submenu ml-6 space-y-1">
        <li>
          <a href="dist/content/recepcionista.php" data-link data-modulo="registros"
             <?php if (!tienePermiso('registros', $rol_menu, $permisos)): ?>data-restricted="true" title="Este módulo no está permitido para tu usuario"<?php endif; ?>
             class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-green-100">
            <span class="w-2 h-2 bg-green-300 rounded-full"></span>
            <span class="text-xs">Recepcionista</span>
          </a>
        </li>
        <li>
          <a href="dist/content/propietario.php" data-link data-modulo="registros"
             <?php if (!tienePermiso('registros', $rol_menu, $permisos)): ?>data-restricted="true" title="Este módulo no está permitido para tu usuario"<?php endif; ?>
             class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-green-100">
            <span class="w-2 h-2 bg-green-300 rounded-full"></span>
            <span class="text-xs">Propietario</span>
          </a>
        </li>
        <li>
          <a href="dist/content/auxiliar.php" data-link data-modulo="registros"
             <?php if (!tienePermiso('registros', $rol_menu, $permisos)): ?>data-restricted="true" title="Este módulo no está permitido para tu usuario"<?php endif; ?>
             class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-green-100">
            <span class="w-2 h-2 bg-green-300 rounded-full"></span>
            <span class="text-xs">Auxiliar veterinario</span>
          </a>
        </li>
        <li>
          <a href="dist/content/paciente.php" data-link data-modulo="registros"
             <?php if (!tienePermiso('registros', $rol_menu, $permisos)): ?>data-restricted="true" title="Este módulo no está permitido para tu usuario"<?php endif; ?>
             class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-green-100">
            <span class="w-2 h-2 bg-green-300 rounded-full"></span>
            <span class="text-xs">Paciente</span>
          </a>
        </li>
      </ul>
    </li>

    <li>
      <a href="dist/content/consultas.php" data-link data-title="Consultas" data-modulo="consultas"
         <?php if (!tienePermiso('consultas', $rol_menu, $permisos)): ?>data-restricted="true" title="Este módulo no está permitido para tu usuario"<?php endif; ?>
         class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-white">
        <svg class="w-5 h-5 opacity-80 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        <span class="text-sm font-medium">Consultas</span>
      </a>
    </li>

    <li>
      <a href="dist/content/historia.php" data-link data-title="historias medicas" data-modulo="historias"
         <?php if (!tienePermiso('historias', $rol_menu, $permisos)): ?>data-restricted="true" title="Este módulo no está permitido para tu usuario"<?php endif; ?>
         class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-white">
        <svg class="w-5 h-5 opacity-80 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
        <span class="text-sm font-medium">Historias Médicas</span>
      </a>
    </li>

    <li>
      <a href="dist/content/inventario.php" data-link data-modulo="inventario"
         onclick="toggleSubmenu('submenu-inventario', this)"
         <?php if (!tienePermiso('inventario', $rol_menu, $permisos)): ?>data-restricted="true" title="Este módulo no está permitido para tu usuario"<?php endif; ?>
         class="menu-toggle flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-white cursor-pointer">
        <svg class="w-5 h-5 opacity-80 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        <span class="text-sm font-medium flex-1">Inventario</span>
        <svg class="arrow w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </a>
      <ul id="submenu-inventario" class="submenu ml-6 space-y-1">
        <li>
          <a href="dist/content/proveedores.php" data-link data-title="proveedores" data-modulo="inventario"
             <?php if (!tienePermiso('inventario', $rol_menu, $permisos)): ?>data-restricted="true" title="Este módulo no está permitido para tu usuario"<?php endif; ?>
             class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-green-100">
            <span class="w-2 h-2 bg-green-300 rounded-full"></span>
            <span class="text-xs">Proveedores</span>
          </a>
        </li>
        <li>
          <a href="dist/content/productos.php" data-link data-title="productos" data-modulo="inventario"
             <?php if (!tienePermiso('inventario', $rol_menu, $permisos)): ?>data-restricted="true" title="Este módulo no está permitido para tu usuario"<?php endif; ?>
             class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-green-100">
            <span class="w-2 h-2 bg-green-300 rounded-full"></span>
            <span class="text-xs">Productos</span>
          </a>
        </li>
      </ul>
    </li>

    <li>
      <a href="dist/content/ventas.php" data-link data-modulo="ventas"
         onclick="toggleSubmenu('submenu-ventas', this)"
         <?php if (!tienePermiso('ventas', $rol_menu, $permisos)): ?>data-restricted="true" title="Este módulo no está permitido para tu usuario"<?php endif; ?>
         class="menu-toggle flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-white cursor-pointer">
        <svg class="w-5 h-5 opacity-80 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/></svg>
        <span class="text-sm font-medium flex-1">Ventas</span>
        <svg class="arrow w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </a>
      <ul id="submenu-ventas" class="submenu ml-6 space-y-1">
        <li>
          <a href="dist/content/clientesnormales.php" data-link data-title="clientes" data-modulo="ventas"
             <?php if (!tienePermiso('ventas', $rol_menu, $permisos)): ?>data-restricted="true" title="Este módulo no está permitido para tu usuario"<?php endif; ?>
             class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-green-100">
            <span class="w-2 h-2 bg-green-300 rounded-full"></span>
            <span class="text-xs">Clientes</span>
          </a>
        </li>
        <li>
          <a href="dist/content/historialventas.php" data-link data-title="Historial ventas" data-modulo="ventas"
             <?php if (!tienePermiso('ventas', $rol_menu, $permisos)): ?>data-restricted="true" title="Este módulo no está permitido para tu usuario"<?php endif; ?>
             class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-green-100">
            <span class="w-2 h-2 bg-green-300 rounded-full"></span>
            <span class="text-xs">Historial de ventas</span>
          </a>
        </li>
      </ul>
    </li>
    <li>
      <a href="dist/content/reportes.php" data-link data-title="Reportes" data-modulo="reportes"
         <?php if (!tienePermiso('reportes', $rol_menu, $permisos)): ?>data-restricted="true" title="Este módulo no está permitido para tu usuario"<?php endif; ?>
         class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-white">
        <svg class="w-5 h-5 opacity-80 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
        <span class="text-sm font-medium">Reportes</span>
      </a>
    </li>
    <li>
      <a href="dist/content/ajustes.php" data-link data-title="Ajustes del sistema" data-modulo="ajustes"
         <?php if (!tienePermiso('ajustes', $rol_menu, $permisos)): ?>data-restricted="true" title="Este módulo no está permitido para tu usuario"<?php endif; ?>
         class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-white">
        <svg class="w-5 h-5 opacity-80 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
        <span class="text-sm font-medium">Ajustes</span>
      </a>
    </li>
  </ul>

  <div class="p-4 border-t border-white/10">
      <a href="/dist/logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-500/20 transition-all text-white">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4V1a2 2 0 00-2-2H5a2 2 0 00-2 2v18a2 2 0 002 2h6a2 2 0 002-2v-4" />
      </svg>
      <span class="text-sm font-medium">Cerrar Sesión</span>
    </a>
  </div>

  <script>
    function toggleSubmenu(id, el) {
      if (el.dataset.restricted === 'true') return;
      const submenu = document.getElementById(id);
      const arrow = el.querySelector('.arrow');
      submenu.classList.toggle('open');
      if (arrow) arrow.classList.toggle('rotated');
    }
  </script>
</nav>
