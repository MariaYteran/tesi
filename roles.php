<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cheetos Paws - Roles</title>
  <link rel="stylesheet" href="css/output.css">
  <style>
    body { font-family: system-ui, Segoe UI, Roboto, Arial, sans-serif; }
    .card-hover { transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.4s ease; }
    .card-hover:hover { transform: translateY(-12px) scale(1.02); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
    .fade-in { opacity: 0; transform: translateY(30px); transition: opacity 0.6s ease, transform 0.6s ease; }
    .fade-in.visible { opacity: 1; transform: translateY(0); }
    .glow-green { box-shadow: 0 0 25px rgba(22, 163, 74, 0.3); }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-700 via-emerald-600 to-green-500">
  <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
    <div class="text-center mb-12 fade-in visible">
      <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur rounded-full mb-6">
        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
      </div>
      <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-3">Bienvenido a Cheetos Paws</h1>
      <p class="text-green-100 text-lg md:text-xl max-w-2xl mx-auto">Sistema integral de gestión clínica veterinaria multi-sede. Selecciona tu rol para continuar.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 max-w-6xl w-full">
      <a href="dist/login.php" class="block bg-white/95 backdrop-blur rounded-3xl p-8 shadow-xl card-hover cursor-pointer group">
        <div class="w-16 h-16 bg-gradient-to-br from-green-600 to-emerald-600 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Administrador</h2>
        <p class="text-gray-500 text-sm leading-relaxed">Gestión completa de la clínica: usuarios, inventario, reportes, facturación y configuración del sistema.</p>
        <div class="mt-4 inline-flex items-center gap-1 text-green-600 font-semibold text-sm group-hover:gap-2 transition-all">Ingresar <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></div>
      </a>

      <a href="dist/login_usuarios.php" class="block bg-white/95 backdrop-blur rounded-3xl p-8 shadow-xl card-hover cursor-pointer group">
        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Auxiliar Veterinario</h2>
        <p class="text-gray-500 text-sm leading-relaxed">Registro de consultas, toma de signos vitales, administración de vacunas y apoyo en procedimientos.</p>
        <div class="mt-4 inline-flex items-center gap-1 text-green-600 font-semibold text-sm group-hover:gap-2 transition-all">Ingresar <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></div>
      </a>

      <a href="dist/login_usuarios.php" class="block bg-white/95 backdrop-blur rounded-3xl p-8 shadow-xl card-hover cursor-pointer group">
        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Propietario</h2>
        <p class="text-gray-500 text-sm leading-relaxed">Acceso al historial clínico de tus mascotas, descarga de facturas y recetas, agendar citas.</p>
        <div class="mt-4 inline-flex items-center gap-1 text-green-600 font-semibold text-sm group-hover:gap-2 transition-all">Ingresar <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></div>
      </a>

      <a href="dist/login_usuarios.php" class="block bg-white/95 backdrop-blur rounded-3xl p-8 shadow-xl card-hover cursor-pointer group">
        <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Recepcionista</h2>
        <p class="text-gray-500 text-sm leading-relaxed">Gestión de citas, registro de clientes y mascotas, facturación básica y atención al público.</p>
        <div class="mt-4 inline-flex items-center gap-1 text-green-600 font-semibold text-sm group-hover:gap-2 transition-all">Ingresar <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></div>
      </a>
    </div>

    <a href="landing.php" class="mt-10 text-white/70 hover:text-white transition-colors text-sm flex items-center gap-1">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      Volver a inicio
    </a>
  </div>
</body>
</html>
