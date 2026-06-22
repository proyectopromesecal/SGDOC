<aside class="w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 hidden md:flex flex-col shrink-0">
    <div class="p-6 border-b border-slate-100 dark:border-slate-800">
        <img alt="PROMESE/CAL" class="h-9 w-auto mx-auto object-contain" src="/images/logo.png" onerror="this.src='/images/logo.png'">
    </div>
    
    <?php $isAdmin = (strpos(strtolower($_SESSION['rol_nombre']), 'admin') !== false); ?>

    <nav class="flex-1 px-4 py-8 space-y-1.5 overflow-y-auto">
        <p class="px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Menú Principal</p>
        
        <a href="/dashboard" class="flex items-center px-4 py-3 rounded-xl transition-all group <?= $_SERVER['REQUEST_URI'] == '/dashboard' ? 'bg-teal-50 text-primary dark:bg-teal-900/10' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' ?>">
            <span class="material-symbols-outlined mr-3 <?= $_SERVER['REQUEST_URI'] == '/dashboard' ? 'text-primary' : 'text-slate-400 group-hover:text-primary' ?>">dashboard</span>
            <span class="text-sm font-bold">Dashboard</span>
        </a>

        <?php if ($isAdmin || $_SESSION['rol_nombre'] !== 'Pendiente de Acceso'): ?>
        <a href="/documentos" class="flex items-center px-4 py-3 rounded-xl transition-all group <?= strpos($_SERVER['REQUEST_URI'], '/documentos') === 0 && $_SERVER['REQUEST_URI'] !== '/documentos/digitalizados' ? 'bg-teal-50 text-primary dark:bg-teal-900/10' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' ?>">
            <span class="material-symbols-outlined mr-3 <?= strpos($_SERVER['REQUEST_URI'], '/documentos') === 0 && $_SERVER['REQUEST_URI'] !== '/documentos/digitalizados' ? 'text-primary' : 'text-slate-400 group-hover:text-primary' ?>">description</span>
            <span class="text-sm font-bold">Documentos</span>
        </a>
        <?php endif; ?>
        
        <a href="/seguimiento" class="flex items-center px-4 py-3 rounded-xl transition-all group <?= $_SERVER['REQUEST_URI'] == '/seguimiento' ? 'bg-teal-50 text-primary dark:bg-teal-900/10' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' ?>">
            <span class="material-symbols-outlined mr-3 <?= $_SERVER['REQUEST_URI'] == '/seguimiento' ? 'text-primary' : 'text-slate-400 group-hover:text-primary' ?>">route</span>
            <span class="text-sm font-bold">Seguimiento</span>
        </a>



        <?php if ($isAdmin): ?>
            <div class="pt-8 pb-4">
                <p class="px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Administración</p>
            </div>
            
            <a href="/usuarios" class="flex items-center px-4 py-3 rounded-xl transition-all group <?= $_SERVER['REQUEST_URI'] == '/usuarios' ? 'bg-teal-50 text-primary dark:bg-teal-900/10' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' ?>">
                <span class="material-symbols-outlined mr-3 <?= $_SERVER['REQUEST_URI'] == '/usuarios' ? 'text-primary' : 'text-slate-400 group-hover:text-primary' ?>">group</span>
                <span class="text-sm font-bold">Usuarios</span>
            </a>

            <a href="/departamentos" class="flex items-center px-4 py-3 rounded-xl transition-all group <?= $_SERVER['REQUEST_URI'] == '/departamentos' ? 'bg-teal-50 text-primary dark:bg-teal-900/10' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' ?>">
                <span class="material-symbols-outlined mr-3 <?= $_SERVER['REQUEST_URI'] == '/departamentos' ? 'text-primary' : 'text-slate-400 group-hover:text-primary' ?>">corporate_fare</span>
                <span class="text-sm font-bold">Departamentos</span>
            </a>

            <a href="/tipos-solicitudes" class="flex items-center px-4 py-3 rounded-xl transition-all group <?= $_SERVER['REQUEST_URI'] == '/tipos-solicitudes' ? 'bg-teal-50 text-primary dark:bg-teal-900/10' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' ?>">
                <span class="material-symbols-outlined mr-3 <?= $_SERVER['REQUEST_URI'] == '/tipos-solicitudes' ? 'text-primary' : 'text-slate-400 group-hover:text-primary' ?>">lists</span>
                <span class="text-sm font-bold">Tipos de Solicitud</span>
            </a>
            
            <a href="/bitacora" class="flex items-center px-4 py-3 rounded-xl transition-all group <?= $_SERVER['REQUEST_URI'] == '/bitacora' ? 'bg-teal-50 text-primary dark:bg-teal-900/10' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' ?>">
                <span class="material-symbols-outlined mr-3 <?= $_SERVER['REQUEST_URI'] == '/bitacora' ? 'text-primary' : 'text-slate-400 group-hover:text-primary' ?>">history</span>
                <span class="text-sm font-bold">Bitácora</span>
            </a>
            
            <a href="/configuracion" class="flex items-center px-4 py-3 rounded-xl transition-all group <?= $_SERVER['REQUEST_URI'] == '/configuracion' ? 'bg-teal-50 text-primary dark:bg-teal-900/10' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' ?>">
                <span class="material-symbols-outlined mr-3 <?= $_SERVER['REQUEST_URI'] == '/configuracion' ? 'text-primary' : 'text-slate-400 group-hover:text-primary' ?>">settings</span>
                <span class="text-sm font-bold">Configuración</span>
            </a>
            
            <a href="/configuracion/permisos" class="flex items-center px-4 py-3 rounded-xl transition-all group <?= $_SERVER['REQUEST_URI'] == '/configuracion/permisos' ? 'bg-teal-50 text-primary dark:bg-teal-900/10' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' ?>">
                <span class="material-symbols-outlined mr-3 <?= $_SERVER['REQUEST_URI'] == '/configuracion/permisos' ? 'text-primary' : 'text-slate-400 group-hover:text-primary' ?>">security</span>
                <span class="text-sm font-bold">Permisos & Seguridad</span>
            </a>

            <a href="/configuracion/roles" class="flex items-center px-4 py-3 rounded-xl transition-all group <?= $_SERVER['REQUEST_URI'] == '/configuracion/roles' ? 'bg-teal-50 text-primary dark:bg-teal-900/10' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' ?>">
                <span class="material-symbols-outlined mr-3 <?= $_SERVER['REQUEST_URI'] == '/configuracion/roles' ? 'text-primary' : 'text-slate-400 group-hover:text-primary' ?>">manage_accounts</span>
                <span class="text-sm font-bold">Gestión de Roles</span>
            </a>
        <?php endif; ?>
    </nav>

    <!-- Campana de Notificaciones -->
    <div class="px-4 pb-3 relative">
        <!-- Trigger -->
        <button id="notif-trigger" onclick="SIGEDOC_NOTIF.toggle()"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all group relative">
            <div class="relative">
                <span class="material-symbols-outlined text-xl text-slate-400 group-hover:text-primary transition-colors">notifications</span>
                <!-- Badge número -->
                <span id="notif-badge" class="hidden absolute -top-1.5 -right-1.5 size-4 bg-secondary text-white rounded-full flex items-center justify-center">
                    <span id="notif-count" class="text-[8px] font-black leading-none">0</span>
                </span>
            </div>
            <span class="text-sm font-bold group-hover:text-primary transition-colors">Notificaciones</span>
        </button>

        <!-- Panel desplegable -->
        <div id="notif-panel" class="hidden absolute bottom-full left-3 right-3 mb-2 bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-800 overflow-hidden z-[999]" style="max-height: 480px;">
            <!-- Panel Header -->
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                <p class="text-[11px] font-black text-slate-700 dark:text-white uppercase tracking-widest">Notificaciones</p>
                <button onclick="SIGEDOC_NOTIF.marcarTodas()" class="text-[9px] font-black text-primary hover:underline uppercase tracking-widest transition-colors">Marcar leídas</button>
            </div>

            <!-- Lista -->
            <div id="notif-lista" class="overflow-y-auto" style="max-height: 360px;">
                <div class="py-10 flex flex-col items-center gap-3 text-center">
                    <span class="material-symbols-outlined text-3xl text-slate-200">hourglass_empty</span>
                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Cargando...</p>
                </div>
            </div>
        </div>
    </div>

    <div class="p-4 border-t border-slate-100 dark:border-slate-800">
        <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-2xl">
            <div class="h-9 w-9 <?= $isAdmin ? 'bg-secondary' : 'bg-primary' ?> text-white rounded-xl flex items-center justify-center shadow-lg shadow-teal-900/20">
                <span class="material-symbols-outlined text-sm"><?= $isAdmin ? 'admin_panel_settings' : 'person' ?></span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[10px] font-black text-slate-800 dark:text-white truncate uppercase tracking-tighter"><?= htmlspecialchars($_SESSION['usuario']) ?></p>
                <p class="text-[8px] font-bold <?= $isAdmin ? 'text-secondary' : 'text-slate-400' ?> uppercase tracking-widest"><?= htmlspecialchars($_SESSION['rol_nombre']) ?></p>
            </div>
            <a href="/logout" class="text-slate-300 hover:text-secondary transition-colors" title="Cerrar Sesión">
                <span class="material-symbols-outlined text-lg">logout</span>
            </a>
        </div>
    </div>
</aside>

<!-- Scripts de Notificaciones (se cargan una sola vez via sidebar) -->
<script src="/js/notifications.js"></script>
