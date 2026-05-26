<!doctype html>
<html lang="es" class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cheetos Paws - Sistema Veterinario</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="css/output.css">
  <style>
    body { font-family: system-ui, Segoe UI, Roboto, Arial, sans-serif; }
    .fade-up { opacity: 0; transform: translateY(40px); transition: opacity 0.8s ease, transform 0.8s ease; }
    .fade-up.visible { opacity: 1; transform: translateY(0); }
    .nav-link::after { content: ''; display: block; width: 0; height: 2px; background: #16a34a; transition: width 0.3s ease; margin-top: 2px; }
    .nav-link:hover::after, .nav-link.active::after { width: 100%; }
    .btn-primary { transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(22, 163, 74, 0.4); }
    .parallax-bg { background-attachment: fixed; }
    @media (max-width: 768px) { .parallax-bg { background-attachment: scroll; } }
  </style>
</head>
<body class="text-gray-800 bg-white">

  <!-- NAV -->
  <nav class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md shadow-sm border-b border-green-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-green-600 to-emerald-500 flex items-center justify-center text-white font-bold text-sm shadow-md">CP</div>
        <span class="font-bold text-lg text-green-800">Cheetos Paws</span>
      </div>
      <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-600">
        <a href="#hero" class="nav-link">Inicio</a>
        <a href="#sistema" class="nav-link">Sistema</a>
        <a href="#roles" class="nav-link">Roles</a>
        <a href="#maltrato" class="nav-link">Maltrato Animal</a>
        <a href="#comparativa" class="nav-link">Caninos vs Felinos</a>
        <a href="#estigmas" class="nav-link">Estigmas</a>
      </div>
      <a href="roles.php" class="inline-flex items-center gap-2 bg-green-600 text-white px-5 py-2 rounded-full font-semibold text-sm shadow-lg btn-primary">
        Ingresar
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
      </a>
      <button id="menu-toggle" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
    </div>
    <!-- mobile nav -->
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-green-100 px-4 py-3 space-y-2 text-sm font-medium text-gray-600">
      <a href="#hero" class="block py-1">Inicio</a>
      <a href="#sistema" class="block py-1">Sistema</a>
      <a href="#roles" class="block py-1">Roles</a>
      <a href="#maltrato" class="block py-1">Maltrato Animal</a>
      <a href="#comparativa" class="block py-1">Caninos vs Felinos</a>
      <a href="#estigmas" class="block py-1">Estigmas</a>
    </div>
  </nav>

  <!-- 1. HERO -->
  <section id="hero" class="min-h-screen flex items-center relative overflow-hidden pt-16">
    <div class="absolute inset-0 bg-gradient-to-br from-green-900/85 via-green-800/70 to-emerald-900/85 z-10"></div>
    <div class="absolute inset-0 z-0 parallax-bg" style="background-image:url('https://images.unsplash.com/photo-1601758125946-6ec2ef64daf8?w=1920&q=80');background-size:cover;background-position:center;"></div>
    <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
      <div class="max-w-3xl">
        <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur rounded-full px-4 py-1.5 text-white/80 text-sm mb-6 border border-white/10">
          <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
          Sistema integral de gestión veterinaria
        </div>
        <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold text-white leading-tight mb-6">
          Cheetos <span class="text-green-300">Paws</span><br>
          <span class="text-2xl sm:text-3xl md:text-4xl font-light text-green-100">Inteligente y Centralizada</span>
        </h1>
        <p class="text-lg sm:text-xl text-green-50/90 max-w-xl mb-8 leading-relaxed">
          Gestiona tu clínica veterinaria desde un solo panel. Consultas, facturación, inventario y más — todo en un ecosistema integrado.
        </p>
        <div class="flex flex-wrap gap-4">
          <a href="roles.php" class="inline-flex items-center gap-2 bg-white text-green-700 px-7 py-3.5 rounded-full font-bold text-base shadow-xl btn-primary hover:bg-green-50">
            Comenzar ahora
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </a>
          <a href="#sistema" class="inline-flex items-center gap-2 bg-white/10 text-white border border-white/20 px-7 py-3.5 rounded-full font-semibold text-base hover:bg-white/20 transition-all">
            Conocer más
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
          </a>
        </div>
        <div class="mt-12 flex items-center gap-8 text-white/60 text-sm">
          <div class="flex items-center gap-2"><svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Gestión integral</div>
          <div class="flex items-center gap-2"><svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Tiempo real</div>
          <div class="flex items-center gap-2"><svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> Reportes PDF</div>
        </div>
      </div>
    </div>
  </section>

  <!-- 2. SISTEMA -->
  <section id="sistema" class="py-20 md:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-16 fade-up">
        <span class="text-green-600 font-semibold text-sm tracking-widest uppercase">¿Qué es Cheetos Paws?</span>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-3">Un ecosistema completo para tu clínica veterinaria</h2>
        <p class="text-gray-500 mt-4 max-w-2xl mx-auto text-lg">Todo lo que necesitas para administrar tu clínica veterinaria de forma eficiente y profesional.</p>
      </div>

      <div class="grid md:grid-cols-2 gap-8 fade-up">
        <div class="relative rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 min-h-[280px] group">
          <img src="https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=800&q=80" alt="Facturación" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
          <div class="absolute inset-0 bg-gradient-to-br from-green-900/90 via-emerald-800/85 to-green-800/90"></div>
          <div class="relative z-10 p-8 flex flex-col justify-end h-full min-h-[280px]">
            <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Facturación Inteligente</h3>
            <p class="text-gray-100 leading-relaxed text-sm">Facturas consolidadas para consultas multi-mascota con descuentos automáticos. Soporta divisas y múltiples métodos de pago.</p>
          </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 min-h-[280px] group">
          <img src="https://images.unsplash.com/photo-1553413077-190dd305871c?w=800&q=80" alt="Inventario" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
          <div class="absolute inset-0 bg-gradient-to-br from-emerald-900/90 via-green-800/85 to-emerald-800/90"></div>
          <div class="relative z-10 p-8 flex flex-col justify-end h-full min-h-[280px]">
            <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Inventario + Productos Estrella</h3>
            <p class="text-gray-100 leading-relaxed text-sm">Control de stock, alertas de reposición y análisis de productos más vendidos basado en datos reales de ventas.</p>
          </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 min-h-[280px] group">
          <img src="https://images.unsplash.com/photo-1506784983877-45594efa4cbe?w=800&q=80" alt="Agenda" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
          <div class="absolute inset-0 bg-gradient-to-br from-green-800/90 via-emerald-900/85 to-green-900/90"></div>
          <div class="relative z-10 p-8 flex flex-col justify-end h-full min-h-[280px]">
            <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Agenda y Emergencias</h3>
            <p class="text-gray-100 leading-relaxed text-sm">Calendario de citas con filtros por mes/semana/año y sistema de emergencias integrado con historial clínico.</p>
          </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 min-h-[280px] group">
          <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&q=80" alt="Reportes" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
          <div class="absolute inset-0 bg-gradient-to-br from-emerald-800/90 via-green-900/85 to-emerald-900/90"></div>
          <div class="relative z-10 p-8 flex flex-col justify-end h-full min-h-[280px]">
            <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Reportes y PDF</h3>
            <p class="text-gray-100 leading-relaxed text-sm">Genera reportes detallados, facturas PDF, recetas médicas y más. Con nombre real de tu clínica en cada documento.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 3. ROLES -->
  <section id="roles" class="py-20 md:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-16 fade-up">
        <span class="text-green-600 font-semibold text-sm tracking-widest uppercase">Roles del Sistema</span>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-3">Cada rol tiene su lugar</h2>
        <p class="text-gray-500 mt-4 max-w-2xl mx-auto text-lg">Cheetos Paws se adapta a las necesidades de cada miembro del equipo veterinario.</p>
      </div>
      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 fade-up">
        <div class="relative rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 min-h-[260px] group hover:-translate-y-1">
          <img src="https://images.unsplash.com/photo-1553877522-43269d4ea984?w=600&q=80" alt="Administrador" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
          <div class="absolute inset-0 bg-gradient-to-br from-green-900/85 via-emerald-800/80 to-green-800/85"></div>
          <div class="relative z-10 p-6 flex flex-col justify-end items-center text-center h-full min-h-[260px]">
            <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center mb-3">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-white">Administrador</h3>
            <p class="text-gray-100 text-sm mt-2">Control total del sistema, usuarios, inventario, reportes y configuración.</p>
          </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 min-h-[260px] group hover:-translate-y-1">
          <img src="https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=600&q=80" alt="Auxiliar" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
          <div class="absolute inset-0 bg-gradient-to-br from-emerald-900/85 via-green-800/80 to-emerald-800/85"></div>
          <div class="relative z-10 p-6 flex flex-col justify-end items-center text-center h-full min-h-[260px]">
            <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center mb-3">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-4 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <h3 class="text-lg font-bold text-white">Auxiliar Veterinario</h3>
            <p class="text-gray-100 text-sm mt-2">Consulta, vacunación, signos vitales y apoyo en procedimientos.</p>
          </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 min-h-[260px] group hover:-translate-y-1">
          <img src="https://images.unsplash.com/photo-1548199973-03cce0bbc87b?w=600&q=80" alt="Propietario" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
          <div class="absolute inset-0 bg-gradient-to-br from-green-800/85 via-emerald-900/80 to-green-900/85"></div>
          <div class="relative z-10 p-6 flex flex-col justify-end items-center text-center h-full min-h-[260px]">
            <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center mb-3">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-white">Propietario</h3>
            <p class="text-gray-100 text-sm mt-2">Historial de mascotas, facturas, recetas y agendar citas.</p>
          </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 min-h-[260px] group hover:-translate-y-1">
          <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=600&q=80" alt="Recepcionista" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
          <div class="absolute inset-0 bg-gradient-to-br from-emerald-800/85 via-green-900/80 to-emerald-900/85"></div>
          <div class="relative z-10 p-6 flex flex-col justify-end items-center text-center h-full min-h-[260px]">
            <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center mb-3">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <h3 class="text-lg font-bold text-white">Recepcionista</h3>
            <p class="text-gray-100 text-sm mt-2">Citas, registro clientes, facturación básica y atención al público.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 4. MALTRATO ANIMAL -->
  <section id="maltrato" class="py-20 md:py-28 bg-gradient-to-br from-red-100 via-red-50 to-rose-100 relative overflow-hidden">
    <div class="absolute top-10 right-10 w-96 h-96 bg-red-300 rounded-full blur-3xl opacity-30 -z-10"></div>
    <div class="absolute bottom-10 left-10 w-80 h-80 bg-rose-300 rounded-full blur-3xl opacity-30 -z-10"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-red-200 rounded-full blur-3xl opacity-20 -z-10"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-14 fade-up">
        <span class="text-red-600 font-semibold text-sm tracking-widest uppercase bg-red-200 px-4 py-1.5 rounded-full inline-block">Maltrato Animal</span>
        <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mt-4">Una realidad <span class="text-red-500">que debemos cambiar</span></h2>
        <p class="text-gray-700 mt-4 max-w-2xl mx-auto text-lg">El maltrato animal nos duele a todos. Como comunidad veterinaria, alzamos la voz por quienes no pueden hablar. Conoce los tipos, actúa y denuncia.</p>
      </div>

      <div class="grid md:grid-cols-3 gap-8 fade-up">
        <div class="bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500">
          <div class="h-48 overflow-hidden relative">
            <img src="https://images.unsplash.com/photo-1601758228041-f3b2795255f1?w=600&q=80" alt="Maltrato físico" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-red-900/80 via-red-800/30 to-transparent"></div>
            <div class="absolute bottom-4 left-5 right-5 flex items-center gap-3">
              <span class="w-10 h-10 bg-red-500 text-white rounded-full flex items-center justify-center font-bold shadow-lg shrink-0">1</span>
              <h4 class="font-bold text-white text-xl drop-shadow-lg">Maltrato Físico</h4>
            </div>
          </div>
          <div class="p-6 space-y-3">
            <p class="text-gray-700 leading-relaxed">Golpes, lesiones, abandono, falta de alimentación y condiciones insalubres. Es la forma más visible de maltrato, pero no la única.</p>
            <ul class="space-y-2 text-sm text-gray-600">
              <li class="flex items-start gap-2"><span class="text-red-500 mt-1 shrink-0">•</span>Golpes, patadas y lesiones provocadas intencionalmente</li>
              <li class="flex items-start gap-2"><span class="text-red-500 mt-1 shrink-0">•</span>Abandono en la vía pública o condiciones inhumanas</li>
              <li class="flex items-start gap-2"><span class="text-red-500 mt-1 shrink-0">•</span>Falta de alimentación, agua potable y refugio adecuado</li>
              <li class="flex items-start gap-2"><span class="text-red-500 mt-1 shrink-0">•</span>Omisión de atención veterinaria ante enfermedades o lesiones</li>
              <li class="flex items-start gap-2"><span class="text-red-500 mt-1 shrink-0">•</span>Encadenamiento permanente y hacinamiento</li>
            </ul>
          </div>
        </div>
        <div class="bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500">
          <div class="h-48 overflow-hidden relative">
            <img src="https://images.unsplash.com/photo-1548767797-d8c844163c4c?w=600&q=80" alt="Maltrato psicológico" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-red-900/80 via-red-800/30 to-transparent"></div>
            <div class="absolute bottom-4 left-5 right-5 flex items-center gap-3">
              <span class="w-10 h-10 bg-red-500 text-white rounded-full flex items-center justify-center font-bold shadow-lg shrink-0">2</span>
              <h4 class="font-bold text-white text-xl drop-shadow-lg">Maltrato Psicológico</h4>
            </div>
          </div>
          <div class="p-6 space-y-3">
            <p class="text-gray-700 leading-relaxed">El daño emocional en los animales es tan real como el físico y deja cicatrices invisibles que duran toda la vida.</p>
            <ul class="space-y-2 text-sm text-gray-600">
              <li class="flex items-start gap-2"><span class="text-red-500 mt-1 shrink-0">•</span>Aislamiento social prolongado sin interacción con otros animales o humanos</li>
              <li class="flex items-start gap-2"><span class="text-red-500 mt-1 shrink-0">•</span>Gritos constantes, amenazas y violencia intrafamiliar presenciada</li>
              <li class="flex items-start gap-2"><span class="text-red-500 mt-1 shrink-0">•</span>Privación de estímulos naturales: juegos, exploración, ejercicio</li>
              <li class="flex items-start gap-2"><span class="text-red-500 mt-1 shrink-0">•</span>Confinamiento en espacios reducidos sin enriquecimiento ambiental</li>
              <li class="flex items-start gap-2"><span class="text-red-500 mt-1 shrink-0">•</span>Estrés crónico por falta de rutina y cuidado predecible</li>
            </ul>
          </div>
        </div>
        <div class="bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500">
          <div class="h-48 overflow-hidden relative">
            <img src="https://images.unsplash.com/photo-1534567153574-2b12153a87f0?w=600&q=80" alt="Explotación animal" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-red-900/80 via-red-800/30 to-transparent"></div>
            <div class="absolute bottom-4 left-5 right-5 flex items-center gap-3">
              <span class="w-10 h-10 bg-red-500 text-white rounded-full flex items-center justify-center font-bold shadow-lg shrink-0">3</span>
              <h4 class="font-bold text-white text-xl drop-shadow-lg">Explotación</h4>
            </div>
          </div>
          <div class="p-6 space-y-3">
            <p class="text-gray-700 leading-relaxed">Miles de animales son explotados cada día en todo el mundo por beneficio económico sin importar su bienestar.</p>
            <ul class="space-y-2 text-sm text-gray-600">
              <li class="flex items-start gap-2"><span class="text-red-500 mt-1 shrink-0">•</span>Crianza intensiva en granjas sin condiciones de bienestar animal</li>
              <li class="flex items-start gap-2"><span class="text-red-500 mt-1 shrink-0">•</span>Peleas ilegales de perros y gallos con fines de apuesta</li>
              <li class="flex items-start gap-2"><span class="text-red-500 mt-1 shrink-0">•</span>Sobrecarga de trabajo en animales de carga y tracción</li>
              <li class="flex items-start gap-2"><span class="text-red-500 mt-1 shrink-0">•</span>Tráfico ilegal de especies silvestres y domésticas</li>
              <li class="flex items-start gap-2"><span class="text-red-500 mt-1 shrink-0">•</span>Reproducción forzada continua sin descanso ni cuidado veterinario</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="mt-12 max-w-4xl mx-auto fade-up">
        <div class="bg-red-700 rounded-2xl p-8 md:p-10 shadow-xl text-white relative overflow-hidden">
          <div class="absolute top-0 right-0 w-60 h-60 bg-white/10 rounded-full blur-3xl"></div>
          <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
          <div class="flex flex-col md:flex-row items-start gap-6 relative z-10">
            <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center shrink-0">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1">
              <h4 class="font-bold text-xl mb-3">¿Sabías que?</h4>
              <p class="text-red-100 leading-relaxed">En Venezuela, la <strong class="text-white">Ley de Protección a la Fauna Doméstica</strong> (2010) establece penas de hasta <strong class="text-white">2 años de prisión</strong> por maltrato animal. Si eres testigo de algún caso, denuncia ante el Ministerio Público, las autoridades ambientales o a través de las organizaciones protectoras de animales.</p>
              <div class="flex flex-wrap gap-3 mt-5">
                <a href="https://www.instagram.com/explore/tags/maltratoanimal/" target="_blank" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 transition-colors px-5 py-2.5 rounded-full text-sm font-semibold backdrop-blur">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                  Comparte y crea conciencia
                </a>
                <a href="https://www.google.com/search?q=denunciar+maltrato+animal+venezuela" target="_blank" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 transition-colors px-5 py-2.5 rounded-full text-sm font-semibold backdrop-blur border border-white/20">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                  Cómo denunciar
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 5. CANINOS VS FELINOS -->
  <section id="comparativa" class="py-20 md:py-28 bg-gradient-to-br from-emerald-50 via-white to-green-50 relative overflow-hidden">
    <div class="absolute -top-10 right-1/3 w-72 h-72 bg-amber-200 rounded-full blur-3xl opacity-20 -z-10"></div>
    <div class="absolute -bottom-10 left-1/4 w-64 h-64 bg-green-200 rounded-full blur-3xl opacity-30 -z-10"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-16 fade-up">
        <span class="text-green-600 font-semibold text-sm tracking-widest uppercase">Comparativa</span>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-3">Caninos vs Felinos</h2>
        <p class="text-gray-500 mt-4 max-w-xl mx-auto text-lg">Dos especies, dos mundos. Conoce sus diferencias para ofrecer el mejor cuidado.</p>
      </div>

      <div class="grid md:grid-cols-2 gap-8 lg:gap-12 fade-up">
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
          <div class="h-52 overflow-hidden">
            <img src="https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=800&q=80" alt="Perro" class="w-full h-full object-cover">
          </div>
          <div class="p-8">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">🐕</div>
              <h3 class="text-2xl font-bold text-gray-800">Caninos</h3>
            </div>
            <ul class="space-y-3 text-gray-600">
              <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span><strong>Sociales por naturaleza:</strong> Necesitan interacción constante con humanos y otros perros.</span></li>
              <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span><strong>Ejercicio diario:</strong> Requieren paseos, juegos y actividad física regular para su salud mental.</span></li>
              <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span><strong>Entrenables:</strong> Responden bien al adiestramiento positivo y pueden aprender comandos complejos.</span></li>
              <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span><strong>Visitas al veterinario:</strong> Vacunación anual obligatoria, desparasitación mensual y control de peso constante.</span></li>
            </ul>
          </div>
        </div>
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
          <div class="h-52 overflow-hidden">
            <img src="https://images.unsplash.com/photo-1574158622682-e40e69881006?w=800&q=80" alt="Gato" class="w-full h-full object-cover">
          </div>
          <div class="p-8">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">🐱</div>
              <h3 class="text-2xl font-bold text-gray-800">Felinos</h3>
            </div>
            <ul class="space-y-3 text-gray-600">
              <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span><strong>Independientes pero sensibles:</strong> Disfrutan la compañía en sus términos; el estrés es su principal enemigo.</span></li>
              <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span><strong>Aseo meticuloso:</strong> Se acicalan solos, pero requieren cepillado regular y control de bolas de pelo.</span></li>
              <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span><strong>Territoriales:</strong> Necesitan espacios verticales, rascadores y zonas de escondite para sentirse seguros.</span></li>
              <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span><strong>Visitas al veterinario:</strong> Son propensos a enfermedad renal silenciosa; análisis anuales de sangre y orina son clave.</span></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 6. ESTIGMAS -->
  <section id="estigmas" class="py-20 md:py-28 bg-gradient-to-br from-green-50 via-white to-emerald-50 relative overflow-hidden">
    <div class="absolute top-20 left-20 w-64 h-64 bg-green-200 rounded-full blur-3xl opacity-30 -z-10"></div>
    <div class="absolute bottom-20 right-20 w-72 h-72 bg-emerald-200 rounded-full blur-3xl opacity-30 -z-10"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-14 fade-up">
        <span class="text-green-600 font-semibold text-sm tracking-widest uppercase bg-green-100 px-4 py-1.5 rounded-full inline-block">Estigmas Sociales</span>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-4">Mitos y realidades sobre perros y gatos</h2>
        <p class="text-gray-500 mt-3 max-w-xl mx-auto text-lg">Haz clic en cada card para descubrir la verdad detrás de las creencias populares.</p>
      </div>

      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 fade-up">
        <div class="group cursor-pointer perspective" onclick="toggleEstigma(this)">
          <div class="relative bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-500">
            <div class="h-44 overflow-hidden">
              <img src="https://images.unsplash.com/photo-1574158622682-e40e69881006?w=600&q=80" alt="Gato" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
            </div>
            <div class="p-5">
              <div class="flex items-center gap-2 mb-2">
                <span class="px-3 py-0.5 bg-red-100 text-red-600 text-xs font-bold rounded-full">MITO</span>
              </div>
              <h4 class="font-bold text-gray-800">"Los gatos son traicioneros y no sienten cariño"</h4>
              <div class="estigma-reveal max-h-0 overflow-hidden transition-all duration-500 mt-0">
                <div class="pt-3 border-t border-green-100 mt-3">
                  <p class="text-gray-600 text-sm leading-relaxed"><strong class="text-green-700">Realidad:</strong> Los gatos expresan afecto de forma diferente. Ronronean, amasan, te siguen y parpadean lentamente como señal de confianza. Estudios demuestran que reconocen la voz de sus dueños y prefieren su compañía.</p>
                </div>
              </div>
              <button class="mt-3 text-green-600 text-sm font-semibold flex items-center gap-1 group-hover:gap-2 transition-all" onclick="event.stopPropagation(); toggleEstigma(this.closest('.perspective'))">
                <span class="ver-mas">Ver realidad</span>
                <span class="ver-menos hidden">Ocultar</span>
                <svg class="w-4 h-4 arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </button>
            </div>
          </div>
        </div>

        <div class="group cursor-pointer perspective" onclick="toggleEstigma(this)">
          <div class="relative bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-500">
            <div class="h-44 overflow-hidden">
              <img src="https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=600&q=80" alt="Perro" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
            </div>
            <div class="p-5">
              <div class="flex items-center gap-2 mb-2">
                <span class="px-3 py-0.5 bg-red-100 text-red-600 text-xs font-bold rounded-full">MITO</span>
              </div>
              <h4 class="font-bold text-gray-800">"Los perros de razas peligrosas deberían prohibirse"</h4>
              <div class="estigma-reveal max-h-0 overflow-hidden transition-all duration-500 mt-0">
                <div class="pt-3 border-t border-green-100 mt-3">
                  <p class="text-gray-600 text-sm leading-relaxed"><strong class="text-green-700">Realidad:</strong> No existen razas peligrosas, sino dueños irresponsables. La agresividad canina depende de la educación y socialización. Países que eliminaron las listas de razas peligrosas no vieron correlación entre raza y accidentes.</p>
                </div>
              </div>
              <button class="mt-3 text-green-600 text-sm font-semibold flex items-center gap-1 group-hover:gap-2 transition-all" onclick="event.stopPropagation(); toggleEstigma(this.closest('.perspective'))">
                <span class="ver-mas">Ver realidad</span>
                <span class="ver-menos hidden">Ocultar</span>
                <svg class="w-4 h-4 arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </button>
            </div>
          </div>
        </div>

        <div class="group cursor-pointer perspective" onclick="toggleEstigma(this)">
          <div class="relative bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-500">
            <div class="h-44 overflow-hidden">
              <img src="https://images.unsplash.com/photo-1513360371669-4adf3dd7dff8?w=600&q=80" alt="Gato y bebé" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
            </div>
            <div class="p-5">
              <div class="flex items-center gap-2 mb-2">
                <span class="px-3 py-0.5 bg-red-100 text-red-600 text-xs font-bold rounded-full">MITO</span>
              </div>
              <h4 class="font-bold text-gray-800">"Los gatos les roban el alma a los bebés"</h4>
              <div class="estigma-reveal max-h-0 overflow-hidden transition-all duration-500 mt-0">
                <div class="pt-3 border-t border-green-100 mt-3">
                  <p class="text-gray-600 text-sm leading-relaxed"><strong class="text-green-700">Realidad:</strong> Superstición medieval sin fundamento. Los gatos son compañeros seguros para niños. Crecer con mascotas fortalece el sistema inmunológico infantil y reduce el riesgo de alergias según múltiples estudios.</p>
                </div>
              </div>
              <button class="mt-3 text-green-600 text-sm font-semibold flex items-center gap-1 group-hover:gap-2 transition-all" onclick="event.stopPropagation(); toggleEstigma(this.closest('.perspective'))">
                <span class="ver-mas">Ver realidad</span>
                <span class="ver-menos hidden">Ocultar</span>
                <svg class="w-4 h-4 arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </button>
            </div>
          </div>
        </div>

        <div class="group cursor-pointer perspective" onclick="toggleEstigma(this)">
          <div class="relative bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-500">
            <div class="h-44 overflow-hidden">
              <img src="https://images.unsplash.com/photo-1544568100-847a948585b9?w=600&q=80" alt="Perro adulto" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
            </div>
            <div class="p-5">
              <div class="flex items-center gap-2 mb-2">
                <span class="px-3 py-0.5 bg-red-100 text-red-600 text-xs font-bold rounded-full">MITO</span>
              </div>
              <h4 class="font-bold text-gray-800">"Un perro adulto no puede aprender trucos nuevos"</h4>
              <div class="estigma-reveal max-h-0 overflow-hidden transition-all duration-500 mt-0">
                <div class="pt-3 border-t border-green-100 mt-3">
                  <p class="text-gray-600 text-sm leading-relaxed"><strong class="text-green-700">Realidad:</strong> Falso. Los perros adultos pueden aprender nuevos comandos y modificar comportamientos. La neuroplasticidad canina se mantiene toda la vida. La paciencia y el refuerzo positivo son la clave, sin importar la edad.</p>
                </div>
              </div>
              <button class="mt-3 text-green-600 text-sm font-semibold flex items-center gap-1 group-hover:gap-2 transition-all" onclick="event.stopPropagation(); toggleEstigma(this.closest('.perspective'))">
                <span class="ver-mas">Ver realidad</span>
                <span class="ver-menos hidden">Ocultar</span>
                <svg class="w-4 h-4 arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </button>
            </div>
          </div>
        </div>

        <div class="group cursor-pointer perspective" onclick="toggleEstigma(this)">
          <div class="relative bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-500">
            <div class="h-44 overflow-hidden">
              <img src="https://images.unsplash.com/photo-1495360010541-f48722b34f7d?w=600&q=80" alt="Gato cayendo" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
            </div>
            <div class="p-5">
              <div class="flex items-center gap-2 mb-2">
                <span class="px-3 py-0.5 bg-red-100 text-red-600 text-xs font-bold rounded-full">MITO</span>
              </div>
              <h4 class="font-bold text-gray-800">"Los gatos siempre caen de pie"</h4>
              <div class="estigma-reveal max-h-0 overflow-hidden transition-all duration-500 mt-0">
                <div class="pt-3 border-t border-green-100 mt-3">
                  <p class="text-gray-600 text-sm leading-relaxed"><strong class="text-green-700">Realidad:</strong> Aunque tienen reflejo de enderezamiento, los gatos no siempre caen ilesos. Caídas desde altura pueden causar fracturas graves y daños internos (síndrome del gato paracaidista). Es vital proteger ventanas y balcones.</p>
                </div>
              </div>
              <button class="mt-3 text-green-600 text-sm font-semibold flex items-center gap-1 group-hover:gap-2 transition-all" onclick="event.stopPropagation(); toggleEstigma(this.closest('.perspective'))">
                <span class="ver-mas">Ver realidad</span>
                <span class="ver-menos hidden">Ocultar</span>
                <svg class="w-4 h-4 arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </button>
            </div>
          </div>
        </div>

        <div class="group cursor-pointer perspective" onclick="toggleEstigma(this)">
          <div class="relative bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-500">
            <div class="h-44 overflow-hidden">
              <img src="https://images.unsplash.com/photo-1522276498395-f4f68f7f8454?w=600&q=80" alt="Perro feliz" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
            </div>
            <div class="p-5">
              <div class="flex items-center gap-2 mb-2">
                <span class="px-3 py-0.5 bg-red-100 text-red-600 text-xs font-bold rounded-full">MITO</span>
              </div>
              <h4 class="font-bold text-gray-800">"Los perros solo entienden órdenes con castigos"</h4>
              <div class="estigma-reveal max-h-0 overflow-hidden transition-all duration-500 mt-0">
                <div class="pt-3 border-t border-green-100 mt-3">
                  <p class="text-gray-600 text-sm leading-relaxed"><strong class="text-green-700">Realidad:</strong> El refuerzo positivo es mucho más efectivo que el castigo. Premiar conductas deseadas fortalece el vínculo y acelera el aprendizaje. El castigo genera miedo y ansiedad, empeorando los problemas de comportamiento.</p>
                </div>
              </div>
              <button class="mt-3 text-green-600 text-sm font-semibold flex items-center gap-1 group-hover:gap-2 transition-all" onclick="event.stopPropagation(); toggleEstigma(this.closest('.perspective'))">
                <span class="ver-mas">Ver realidad</span>
                <span class="ver-menos hidden">Ocultar</span>
                <svg class="w-4 h-4 arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="bg-green-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
      <div class="grid md:grid-cols-3 gap-10">
        <div>
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center font-bold text-sm">CP</div>
            <span class="font-bold text-lg text-white">Cheetos Paws</span>
          </div>
          <p class="text-gray-300 text-sm leading-relaxed">Sistema integral de gestión clínica veterinaria. Desarrollado para optimizar la administración de clínicas y el cuidado animal.</p>
        </div>
        <div>
          <h4 class="font-bold mb-4 text-gray-300 uppercase tracking-wider text-sm">Secciones</h4>
          <ul class="space-y-2 text-sm text-gray-400">
            <li><a href="#hero" class="hover:text-white transition-colors">Inicio</a></li>
            <li><a href="#sistema" class="hover:text-white transition-colors">Sistema</a></li>
            <li><a href="#roles" class="hover:text-white transition-colors">Roles</a></li>
            <li><a href="#maltrato" class="hover:text-white transition-colors">Maltrato Animal</a></li>
            <li><a href="#comparativa" class="hover:text-white transition-colors">Caninos vs Felinos</a></li>
            <li><a href="#estigmas" class="hover:text-white transition-colors">Estigmas</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-bold mb-4 text-gray-300 uppercase tracking-wider text-sm">Acceso</h4>
          <ul class="space-y-2 text-sm text-gray-400">
            <li><a href="roles.php" class="hover:text-white transition-colors">Ingresar al sistema</a></li>
            <li><a href="dist/registro1.php" class="hover:text-white transition-colors">Registrar clínica</a></li>
          </ul>
          <div class="mt-6 pt-6 border-t border-green-800">
            <p class="text-gray-500 text-xs">© 2026 Cheetos Paws. Todos los derechos reservados.</p>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <script>
    // Intersection Observer for fade-up animations
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));

    // Mobile menu toggle
    document.getElementById('menu-toggle')?.addEventListener('click', () => {
      document.getElementById('mobile-menu')?.classList.toggle('hidden');
    });

    // Toggle estigma cards
    window.toggleEstigma = function(card) {
      const reveal = card.querySelector('.estigma-reveal');
      const verMas = card.querySelector('.ver-mas');
      const verMenos = card.querySelector('.ver-menos');
      const arrow = card.querySelector('.arrow-icon');
      const isOpen = reveal.classList.contains('max-h-0');
      // Close all others
      document.querySelectorAll('.estigma-reveal').forEach(el => {
        if (el !== reveal) {
          el.classList.add('max-h-0');
          el.closest('.perspective')?.querySelector('.ver-mas')?.classList.remove('hidden');
          el.closest('.perspective')?.querySelector('.ver-menos')?.classList.add('hidden');
          el.closest('.perspective')?.querySelector('.arrow-icon')?.classList.remove('rotate-180');
        }
      });
      if (isOpen) {
        reveal.classList.remove('max-h-0');
        verMas?.classList.add('hidden');
        verMenos?.classList.remove('hidden');
        arrow?.classList.add('rotate-180');
      } else {
        reveal.classList.add('max-h-0');
        verMas?.classList.remove('hidden');
        verMenos?.classList.add('hidden');
        arrow?.classList.remove('rotate-180');
      }
    };

    // Active nav link highlight on scroll
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');
    window.addEventListener('scroll', () => {
      let current = '';
      sections.forEach(sec => {
        const top = sec.offsetTop - 120;
        if (window.scrollY >= top) current = sec.id;
      });
      navLinks.forEach(link => {
        link.classList.toggle('active', link.getAttribute('href') === '#' + current);
      });
    });

    // Close mobile menu on link click
    document.querySelectorAll('#mobile-menu a').forEach(link => {
      link.addEventListener('click', () => document.getElementById('mobile-menu')?.classList.add('hidden'));
    });
  </script>
</body>
</html>
