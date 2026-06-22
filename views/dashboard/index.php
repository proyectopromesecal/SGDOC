<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Dashboard - sgdoc</title>
    
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon/favicon-16x16.png">
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }

        function toggleDarkMode() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#007281",
                        secondary: "#E41E26",
                        "slate-custom": "#111827",
                    },
                    fontFamily: {
                        sans: ["'Plus Jakarta Sans'", "sans-serif"],
                    },
                },
            },
        };
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; -webkit-font-smoothing: antialiased; letter-spacing: -0.01em; }
        .custom-scrollbar::-webkit-scrollbar { height: 4px; width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen flex">
    
    <!-- Sidebar -->
    <?php include VIEWS_PATH . '/partials/sidebar.php'; ?>

    <!-- Onboarding nuevos usuarios -->
    <?php include VIEWS_PATH . '/partials/onboarding.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Header -->
        <header class="h-14 px-8 flex items-center border-b border-slate-200/50 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-md shrink-0 z-20">
            <div class="flex items-center gap-2 text-[9px] font-black uppercase tracking-[0.3em] text-slate-400">
                <span class="text-primary">Dashboard</span>
                <span class="text-slate-300">›</span>
                <span class="text-slate-400">Overview</span>
            </div>
            
            <div class="ml-auto flex items-center gap-8">
                <div class="relative hidden lg:block">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 text-lg">search</span>
                    <input type="text" placeholder="Buscar documentos..." class="bg-slate-50 dark:bg-slate-900 border-none rounded-xl py-2 pl-10 pr-4 text-[11px] font-medium w-64 focus:ring-1 focus:ring-primary/20">
                </div>
                <div class="flex items-center gap-4">
                    <a href="/notas" class="text-slate-300 hover:text-primary transition-colors flex items-center justify-center p-2" title="Manejo de Versiones">
                        <span class="material-symbols-outlined">history</span>
                    </a>
                    <button onclick="document.getElementById('modal-about').classList.remove('hidden')" class="text-slate-300 hover:text-primary transition-colors p-2" title="Acerca de"><span class="material-symbols-outlined">info</span></button>
                <div class="relative">
                    <button id="btn-notif" class="relative text-slate-300 hover:text-primary transition-colors p-2">
                        <span class="material-symbols-outlined">notifications</span>
                        <span id="badge-notif" class="absolute top-1 right-1 size-2.5 bg-red-500 rounded-full border-2 border-slate-50 dark:border-slate-900 hidden flex items-center justify-center text-[6px] text-white font-bold"></span>
                    </button>
                    
                    <!-- Notification Panel -->
                    <div id="panel-notif" class="hidden absolute top-full right-0 mt-2 w-80 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-2xl z-50 overflow-hidden">
                        <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Notificaciones</h4>
                            <button onclick="fetch('/notificaciones/marcarTodas').then(()=>location.reload())" class="text-[9px] font-bold text-primary hover:text-primary/80">Marcar leídas</button>
                        </div>
                        <div id="list-notif" class="max-h-64 overflow-y-auto custom-scrollbar">
                            <!-- Items via JS -->
                            <p class="p-4 text-[10px] text-slate-400 text-center italic">Cargando...</p>
                        </div>
                    </div>
                </div>
                <button onclick="toggleDarkMode()" class="text-slate-300 hover:text-primary transition-colors"><span class="material-symbols-outlined">dark_mode</span></button>
                </div>
            </div>
        </header>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto px-12 py-10 space-y-12">
            
            <!-- Page Title -->
            <div class="flex justify-between items-start">
                <div class="space-y-1">
                    <h1 class="text-[32px] font-black text-slate-custom dark:text-white tracking-tight">Panel de Control</h1>
                    <p class="text-[11px] text-slate-400 font-medium italic">Bienvenido al sistema integrado de gestión documental sgdoc.</p>
                </div>
                <a href="/documentos/crear" class="bg-primary hover:bg-[#005f6b] text-white px-6 py-2.5 rounded-full font-black text-[10px] uppercase tracking-widest shadow-xl shadow-teal-900/10 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">add_circle</span>
                    Nueva Solicitud
                </a>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Solicitados -->
                <div class="p-8 bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm hover:shadow-xl hover:shadow-blue-900/5 transition-all group">
                    <div class="flex items-center justify-between mb-6">
                        <div class="size-12 bg-blue-50 dark:bg-blue-900/20 text-blue-500 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined font-variation-settings: 'FILL' 1">description</span>
                        </div>
                        <span class="text-[9px] font-black text-blue-400 bg-blue-50 dark:bg-blue-900/20 px-2.5 py-1 rounded-full uppercase tracking-widest">+12%</span>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Solicitados</p>
                        <h3 class="text-[44px] font-black tracking-tighter leading-none text-slate-custom dark:text-white"><?= $stats['SOLICITADO'] ?></h3>
                    </div>
                </div>

                <!-- Por Aprobar -->
                <div class="p-8 bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm hover:shadow-xl hover:shadow-amber-900/5 transition-all group">
                    <div class="flex items-center justify-between mb-6">
                        <div class="size-12 bg-amber-50 dark:bg-amber-900/20 text-amber-500 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined font-variation-settings: 'FILL' 1">pending_actions</span>
                        </div>
                        <span class="text-[9px] font-black text-amber-600 bg-amber-50 dark:bg-amber-900/20 px-2.5 py-1 rounded-full uppercase tracking-widest">Pendiente</span>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Por Aprobar</p>
                        <h3 class="text-[44px] font-black tracking-tighter leading-none text-slate-custom dark:text-white"><?= $stats['APROBADO_COMPRAS'] ?></h3>
                    </div>
                </div>

                <!-- Autorizados -->
                <div class="p-8 bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm hover:shadow-xl hover:shadow-emerald-900/5 transition-all group">
                    <div class="flex items-center justify-between mb-6">
                        <div class="size-12 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-500 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined font-variation-settings: 'FILL' 1">verified</span>
                        </div>
                        <div class="flex -space-x-2">
                            <div class="size-6 rounded-full border-2 border-white dark:border-slate-900 bg-slate-100 flex items-center justify-center text-[8px] font-black">🛡️</div>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Autorizados</p>
                        <h3 class="text-[44px] font-black tracking-tighter leading-none text-slate-custom dark:text-white"><?= $stats['AUTORIZADO'] ?></h3>
                    </div>
                </div>

                <!-- Rechazados -->
                <div class="p-8 bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm hover:shadow-xl hover:shadow-red-900/5 transition-all group">
                    <div class="flex items-center justify-between mb-6">
                        <div class="size-12 bg-red-50 dark:bg-red-900/20 text-secondary rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined font-variation-settings: 'FILL' 1">cancel</span>
                        </div>
                        <span class="text-[9px] font-black text-secondary bg-red-50 dark:bg-red-900/20 px-2.5 py-1 rounded-full uppercase tracking-widest text-right">Observados</span>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Rechazados</p>
                        <h3 class="text-[44px] font-black tracking-tighter leading-none text-slate-custom dark:text-white"><?= $stats['RECHAZADO'] ?></h3>
                    </div>
                </div>
            </div>



            <!-- Content Grid -->
            <div class="grid grid-cols-1 gap-12">
                <!-- Recent Documents Section -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between px-4">
                        <div class="flex items-center gap-3">
                            <div class="size-2 bg-primary rounded-full animate-pulse"></div>
                            <h2 class="text-xs font-black text-slate-700 dark:text-white uppercase tracking-tight">Actividad Reciente en el Sistema</h2>
                        </div>
                        <a href="/documentos" class="bg-slate-50 dark:bg-slate-900 text-slate-400 hover:text-primary px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all border border-slate-100 dark:border-slate-800">
                            Explorar Catálogo Completo
                        </a>
                    </div>
                    
                    <div class="bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] overflow-x-auto shadow-sm custom-scrollbar">
                        <table class="w-full text-left min-w-[900px] table-fixed">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-100 dark:border-slate-800">
                                    <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[12%]">Identificador</th>
                                    <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[13%]">Urgencia</th>
                                    <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[35%]">Tipo & Finalidad</th>
                                    <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[20%] text-center">Estado Actual</th>
                                    <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[20%] text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-900">
                                <?php foreach ($documentosRecientes as $doc): ?>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition-colors group">
                                    <td class="px-8 py-6 align-middle">
                                        <div class="flex items-center">
                                            <span class="text-[10px] font-black text-primary bg-teal-50 dark:bg-teal-900/20 px-3 py-1.5 rounded-lg border border-teal-100/30">
                                                <?= htmlspecialchars($doc['id']) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 align-middle">
                                        <?php
                                        $prioColor = 'text-slate-400 bg-slate-50 border-slate-200';
                                        $prioIcon = 'stat_0';
                                        $prioridad = $doc['prioridad'] ?? 'Normal';
                                        if ($prioridad === 'Crítica') {
                                            $prioColor = 'text-red-600 bg-red-50 dark:bg-red-900/10 border-red-100/50';
                                            $prioIcon = 'priority_high';
                                        } else if ($prioridad === 'Alta') {
                                            $prioColor = 'text-amber-600 bg-amber-50 dark:bg-amber-900/10 border-amber-100/50';
                                            $prioIcon = 'keyboard_double_arrow_up';
                                        } else if ($prioridad === 'Normal') {
                                            $prioColor = 'text-teal-600 bg-teal-50 dark:bg-teal-900/10 border-teal-100/50';
                                            $prioIcon = 'remove';
                                        } else if ($prioridad === 'Baja') {
                                            $prioColor = 'text-slate-400 bg-slate-50 dark:bg-slate-900/10 border-slate-200/30';
                                            $prioIcon = 'keyboard_arrow_down';
                                        }
                                        ?>
                                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg border <?= $prioColor ?> w-fit">
                                            <span class="material-symbols-outlined text-[14px]"><?= $prioIcon ?></span>
                                            <span class="text-[9px] font-black uppercase tracking-widest"><?= htmlspecialchars($prioridad) ?></span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 align-middle">
                                        <div class="space-y-0.5 min-w-0">
                                            <p class="text-[11px] font-black text-slate-700 dark:text-white uppercase leading-tight truncate"><?= htmlspecialchars($doc['tipo']) ?></p>
                                            <p class="text-[10px] text-slate-400 font-medium italic truncate"><?= htmlspecialchars($doc['descripcion']) ?></p>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 align-middle">
                                        <?php
                                        $colorClass = 'text-slate-400 bg-slate-50';
                                        if ($doc['estado'] === 'SOLICITADO') $colorClass = 'text-blue-500 bg-blue-50 dark:bg-blue-900/10';
                                        else if ($doc['estado'] === 'APROBADO_COMPRAS') $colorClass = 'text-amber-500 bg-amber-50 dark:bg-amber-900/10';
                                        else if ($doc['estado'] === 'AUTORIZADO') $colorClass = 'text-emerald-500 bg-emerald-50 dark:bg-emerald-900/10';
                                        else if ($doc['estado'] === 'RECHAZADO') $colorClass = 'text-secondary bg-red-50 dark:bg-red-900/10';
                                        else if ($doc['estado'] === 'DIGITALIZADO') $colorClass = 'text-amber-600 bg-amber-50 dark:bg-amber-900/10';
                                        ?>
                                        <div class="flex">
                                            <span class="text-[9px] font-black uppercase tracking-[0.15em] px-3 py-1.5 rounded-full <?= $colorClass ?>">
                                                <?= htmlspecialchars($doc['estado']) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 align-middle text-right">
                                        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all transform group-hover:translate-x-0 translate-x-2">
                                            <a href="/documentos/ver/<?= htmlspecialchars($doc['id']) ?>" class="size-9 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-800 rounded-xl flex items-center justify-center text-slate-400 hover:text-primary transition-all shadow-sm hover:shadow-md" title="Ver Detalles">
                                                <span class="material-symbols-outlined text-lg">visibility</span>
                                            </a>
                                            <a href="/documentos/descargar/<?= htmlspecialchars($doc['id']) ?>/original" class="size-9 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-800 rounded-xl flex items-center justify-center text-slate-400 hover:text-primary transition-all shadow-sm hover:shadow-md" title="Descargar Original">
                                                <span class="material-symbols-outlined text-lg">download</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <footer class="h-10 px-10 flex items-center justify-center shrink-0">
            <p class="text-[8px] font-black text-slate-200 dark:text-slate-800 uppercase tracking-[0.5em]">sgdoc Audit-Point • Promese/Cal • 2026</p>
        </footer>
    </main>
    <!-- About Modal -->
    <div id="modal-about" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-[2.5rem] shadow-2xl border border-slate-100 dark:border-slate-800 overflow-hidden transform transition-all">
            <div class="p-10 text-center space-y-6">
                <div class="size-20 bg-primary/10 text-primary rounded-[2rem] flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-4xl">info</span>
                </div>
                <div class="space-y-2">
                    <h3 class="text-2xl font-black text-slate-custom dark:text-white uppercase tracking-tight">sgdoc v2.5</h3>
                    <p class="text-[11px] font-bold text-primary uppercase tracking-[0.2em]">Sistema Integrado de Gestión Documental</p>
                </div>
                <div class="h-px w-12 bg-slate-100 dark:bg-slate-800 mx-auto"></div>
                <div class="space-y-4">
                    <img src="/images/logo.png" class="h-10 mx-auto object-contain opacity-80" alt="PROMESE/CAL">
                    <p class="text-[11px] text-slate-400 font-medium leading-relaxed px-10">
                        Plataforma oficial para la gestión del ciclo de vida documental, firmas digitales y flujos de aprobación departamental.
                    </p>
                </div>
                <div class="pt-4 grid grid-cols-2 gap-4 text-left">
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Institución</p>
                        <p class="text-[10px] font-bold text-slate-700 dark:text-white">PROMESE/CAL</p>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Tecnología</p>
                        <p class="text-[10px] font-bold text-slate-700 dark:text-white">PHP 8.1 / SQL Server</p>
                    </div>
                </div>
                <button onclick="document.getElementById('modal-about').classList.add('hidden')" class="w-full bg-slate-900 dark:bg-white dark:text-slate-900 text-white font-black text-[10px] uppercase tracking-[0.2em] py-4 rounded-2xl hover:opacity-90 transition-all">
                    Cerrar ventana
                </button>
                <p class="text-[8px] font-black text-slate-300 uppercase underline tracking-widest cursor-pointer hover:text-primary">Términos y Condiciones de Seguridad</p>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('btn-notif');
            const badge = document.getElementById('badge-notif');
            const panel = document.getElementById('panel-notif');
            const list = document.getElementById('list-notif');

            if(!btn) return;

            // Almacén para evitar notificaciones duplicadas en el escritorio
            const notifiedIds = new Set();
            let firstLoad = true;

            // Solicitar permisos de notificación
            if ("Notification" in window) {
                if (Notification.permission !== "granted" && Notification.permission !== "denied") {
                    // Podemos añadir un botón o hacerlo al primer clic
                    console.log("Solicitando permisos de notificación...");
                }
            }

            function showDesktopNotification(title, message, id) {
                if (!("Notification" in window)) return;
                
                if (Notification.permission === "granted" && !notifiedIds.has(id)) {
                    const notification = new Notification(title, {
                        body: message,
                        icon: '/images/logo.png', // Opcional: ruta al logo
                        badge: '/images/logo.png'
                    });
                    
                    notification.onclick = function() {
                        window.focus();
                        this.close();
                    };
                    
                    notifiedIds.add(id);
                }
            }

            // Cargar
            function loadNotifs() {
                fetch('/notificaciones/obtenerRecientes')
                    .then(r => r.json())
                    .then(data => {
                        if(data.success) {
                            // Badge
                            if(data.no_leidas > 0) {
                                badge.textContent = data.no_leidas > 9 ? '9+' : data.no_leidas;
                                badge.classList.remove('hidden');
                            } else {
                                badge.classList.add('hidden');
                            }
                            // List
                            list.innerHTML = '';
                            if(data.notificaciones.length === 0) {
                               list.innerHTML = '<p class="p-6 text-[10px] text-slate-400 text-center italic">No hay notificaciones recientes</p>';
                            } else {
                                data.notificaciones.forEach(n => {
                                    // Si es nueva y no leída, mostrar notificación de Windows
                                    if (!firstLoad && n.leida == 0 && !notifiedIds.has(n.id)) {
                                        showDesktopNotification(n.titulo, n.mensaje, n.id);
                                    } else if (firstLoad) {
                                        // En la carga inicial solo guardamos IDs para no inundar
                                        if (n.leida == 0) notifiedIds.add(n.id);
                                    }

                                    const item = document.createElement('a');
                                    item.href = n.link || '#';
                                    item.className = 'block p-4 border-b border-slate-50 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors group';
                                    
                                    // Marcar como leida al click
                                    item.onclick = (e) => {
                                        fetch('/notificaciones/marcarLeida/' + n.id);
                                    };

                                    const iconColor = n.tipo === 'error' ? 'text-red-500 bg-red-50' : (n.tipo === 'success' ? 'text-emerald-500 bg-emerald-50' : 'text-primary bg-teal-50');
                                    const isUnread = n.leida == 0;

                                    item.innerHTML = `
                                        <div class="flex gap-3">
                                            <div class="relative shrink-0">
                                                <div class="size-8 rounded-xl ${n.leida == 0 ? 'bg-primary/10 text-primary' : 'bg-slate-100 text-slate-400'} flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-sm">notifications</span>
                                                </div>
                                                ${isUnread ? '<div class="absolute top-0 right-0 size-2 bg-red-500 rounded-full border border-white"></div>' : ''}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[10px] font-black uppercase text-slate-700 dark:text-white truncate group-hover:text-primary transition-colors">${n.titulo}</p>
                                                <p class="text-[10px] text-slate-500 line-clamp-2 leading-relaxed">${n.mensaje}</p>
                                                <p class="text-[8px] text-slate-300 mt-1 font-bold uppercase tracking-widest">${n.tiempo_transcurrido}</p>
                                            </div>
                                        </div>
                                    `;
                                    list.appendChild(item);
                                });
                            }
                            firstLoad = false;
                        }
                    })
                    .catch(e => console.error(e));
            }

            // Solicitar permisos al cargar si es necesario
            btn.addEventListener('mouseenter', () => {
                if ("Notification" in window && Notification.permission === "default") {
                    Notification.requestPermission();
                }
            });

            // Toggle
            btn.onclick = (e) => {
                e.stopPropagation();
                panel.classList.toggle('hidden');
                if(!panel.classList.contains('hidden')) {
                    // Refresh on open
                    loadNotifs();
                }
            };

            // Close on click outside
            document.addEventListener('click', (e) => {
                if(!panel.contains(e.target) && !btn.contains(e.target)) {
                    panel.classList.add('hidden');
                }
            });

            loadNotifs();
            // Poll cada 30 segundos
            setInterval(loadNotifs, 30000);
        });
    </script>
</body>
</html>
