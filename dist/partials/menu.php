<?php // dist/partials/menu.php - partial menu reused across pages ?>
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
      <a href="dist/content/home.html" data-link class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-white">
        <img src="src/imagenes/menu-alt-2-svgrepo-com.svg" class="w-5 h-5 opacity-80" style="filter: invert(1) brightness(2);">
        <span class="text-sm font-medium">Inicio</span>
      </a>
    </li>

    <li>
      <a href="#" onclick="toggleSubmenu('submenu-registros', this)" class="menu-toggle flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-white cursor-pointer">
        <img src="src/imagenes/menu-alt-2-svgrepo-com.svg" class="w-5 h-5 opacity-80" style="filter: invert(1) brightness(2);">
        <span class="text-sm font-medium flex-1">Registros</span>
        <svg class="arrow w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </a>
      <ul id="submenu-registros" class="submenu ml-6 space-y-1">
        <li>
          <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-green-100">
            <span class="w-2 h-2 bg-green-300 rounded-full"></span>
            <span class="text-xs">Recepcionista</span>
          </a>
        </li>
        <li>
          <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-green-100">
            <span class="w-2 h-2 bg-green-300 rounded-full"></span>
            <span class="text-xs">Propietario</span>
          </a>
        </li>
        <li>
          <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-green-100">
            <span class="w-2 h-2 bg-green-300 rounded-full"></span>
            <span class="text-xs">Paciente</span>
          </a>
        </li>
      </ul>
    </li>

    <li>
      <a href="dist/content/olaprueba.html" data-link data-title="Consultas" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-white">
        <img src="src/imagenes/menu-alt-2-svgrepo-com.svg" class="w-5 h-5 opacity-80" style="filter: invert(1) brightness(2);">
        <span class="text-sm font-medium">Consultas</span>
      </a>
    </li>

    <li>
      <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-white">
        <img src="src/imagenes/menu-alt-2-svgrepo-com.svg" class="w-5 h-5 opacity-80" style="filter: invert(1) brightness(2);">
        <span class="text-sm font-medium">Historias Médicas</span>
      </a>
    </li>

    <li>
      <a href="#" onclick="toggleSubmenu('submenu-inventario', this)" class="menu-toggle flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-white cursor-pointer">
        <img src="src/imagenes/menu-alt-2-svgrepo-com.svg" class="w-5 h-5 opacity-80" style="filter: invert(1) brightness(2);">
        <span class="text-sm font-medium flex-1">Inventario</span>
        <svg class="arrow w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </a>
      <ul id="submenu-inventario" class="submenu ml-6 space-y-1">
        <li>
          <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-green-100">
            <span class="w-2 h-2 bg-green-300 rounded-full"></span>
            <span class="text-xs">Proveedores</span>
          </a>
        </li>
        <li>
          <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-green-100">
            <span class="w-2 h-2 bg-green-300 rounded-full"></span>
            <span class="text-xs">Ventas</span>
          </a>
        </li>
      </ul>
    </li>

    <li>
      <a href="#" onclick="toggleSubmenu('submenu-ajustes', this)" class="menu-toggle flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-white cursor-pointer">
        <img src="src/imagenes/menu-alt-2-svgrepo-com.svg" class="w-5 h-5 opacity-80" style="filter: invert(1) brightness(2);">
        <span class="text-sm font-medium flex-1">Ajustes</span>
        <svg class="arrow w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </a>
      <ul id="submenu-ajustes" class="submenu ml-6 space-y-1">
        <li>
          <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-green-100">
            <span class="w-2 h-2 bg-green-300 rounded-full"></span>
            <span class="text-xs">Configuraciones</span>
          </a>
        </li>
        <li>
          <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-green-100">
            <span class="w-2 h-2 bg-green-300 rounded-full"></span>
            <span class="text-xs">Detalles de consultas</span>
          </a>
        </li>
      </ul>
    </li>
  </ul>

  <div class="p-4 border-t border-white/10">
    <a href="dist/login.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-500/20 transition-all text-white">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4V1a2 2 0 00-2-2H5a2 2 0 00-2 2v18a2 2 0 002 2h6a2 2 0 002-2v-4" />
      </svg>
      <span class="text-sm font-medium">Cerrar Sesión</span>
    </a>
  </div>

  <script>
    function toggleSubmenu(id, el) {
      const submenu = document.getElementById(id);
      const arrow = el.querySelector('.arrow');
      submenu.classList.toggle('open');
      if (arrow) arrow.classList.toggle('rotated');
    }
  </script>
</nav>
