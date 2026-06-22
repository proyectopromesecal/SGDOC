<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Gestión de Usuarios - sgdoc</title>
    
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

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Header -->
        <header class="h-14 px-8 flex items-center border-b border-slate-200/50 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-md shrink-0 z-20">
            <div class="flex items-center gap-2 text-[9px] font-black uppercase tracking-[0.3em] text-slate-400">
                <span class="hover:text-primary transition-colors cursor-pointer" onclick="location.href='/dashboard'">sgdoc</span>
                <span class="text-slate-300">›</span>
                <span class="text-primary">Gestión de Usuarios</span>
            </div>
            <div class="ml-auto flex items-center gap-6">
                <button onclick="toggleDarkMode()" class="text-slate-300 hover:text-primary transition-colors h-8 w-8 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">dark_mode</span>
                </button>
            </div>
        </header>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto px-12 py-10 space-y-10">
            
            <!-- Page Title -->
            <div class="flex justify-between items-end">
                <div class="space-y-1">
                    <h1 class="text-[32px] font-black text-slate-custom dark:text-white tracking-tight">Control de Usuarios</h1>
                    <p class="text-[11px] text-slate-400 font-medium italic">Administre las credenciales, roles y permisos de acceso al sistema institucional.</p>
                </div>
            </div>


            <!-- Table -->
            <div class="space-y-6">
                <div class="flex items-center justify-between px-4">
                    <h2 class="text-xs font-black text-slate-400 uppercase tracking-widest">Usuarios del Sistema</h2>
                    <div class="flex items-center gap-3 flex-wrap">
                        <button onclick="openModal('modal-nuevo')" class="bg-primary hover:bg-teal-700 text-white px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all flex items-center gap-2 shadow-sm">
                            <span class="material-symbols-outlined text-sm">person_add</span>
                            Nuevo Usuario
                        </button>
                        <button onclick="openModal('modal-ldap-buscar')" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-200 px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all flex items-center gap-2 shadow-sm">
                            <span class="material-symbols-outlined text-sm text-primary">manage_search</span>
                            Buscar en LDAP
                        </button>
                        <button id="btn-sincronizar-ldap" onclick="abrirConfirmSincronizar(this)" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-200 px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all flex items-center gap-2 shadow-sm">
                            <span class="material-symbols-outlined text-sm text-amber-500">sync</span>
                            Sincronizar LDAP
                        </button>
                        <form method="POST" action="/usuarios/desactivar_activos" onsubmit="return confirm('¿Está seguro de desactivar masivamente todos los usuarios activos?');">
                            <?= \App\Core\Security::csrfInput() ?>
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all flex items-center gap-2 shadow-sm">
                                <span class="material-symbols-outlined text-sm">group_remove</span>
                                Desactivar Activos
                            </button>
                        </form>
                        <div class="relative w-64 group ml-4">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg group-focus-within:text-primary transition-colors">search</span>
                            <input type="text" id="search-usuarios" placeholder="BUSCAR AGENTE..." class="w-full pl-12 pr-4 py-2.5 bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black tracking-widest text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-primary/50 transition-all shadow-sm placeholder:text-slate-400">
                        </div>
                    </div>
                </div>
            <div class="bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm overflow-hidden min-h-[500px] flex flex-col">
                <div class="overflow-x-auto custom-scrollbar flex-1">
                    <table class="w-full text-left table-fixed min-w-[900px]">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-100 dark:border-slate-800">
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[35%]">Agente & Identidad</th>
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[25%]">Jerarquía / Rol</th>
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[15%] text-center">Protocolo Firma</th>
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[25%] text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="users-table-body" class="divide-y divide-slate-50 dark:divide-slate-900">
                            <?php foreach ($usuarios as $u): ?>
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition-colors group user-row">
                                <td class="px-8 py-6 align-middle">
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <div class="size-10 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center text-[11px] font-black text-slate-400 border border-slate-200/50 dark:border-slate-700 shadow-sm">
                                                <?= strtoupper(substr($u['usuario'], 0, 1)) ?>
                                            </div>
                                            <div class="absolute -bottom-1 -right-1 size-3.5 border-2 border-white dark:border-slate-900 rounded-full <?= $u['status'] == 1 ? 'bg-emerald-500' : 'bg-red-500' ?>"></div>
                                        </div>
                                        <div class="space-y-0.5">
                                            <p class="text-[11px] font-black text-slate-700 dark:text-white uppercase tracking-tight"><?= htmlspecialchars($u['usuario']) ?></p>
                                            <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                                <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">ID: SIG-<?= str_pad($u['id'], 4, '0', STR_PAD_LEFT) ?></span>
                                                <?php if (!empty($u['departamento_nombre'])): ?>
                                                    <span class="text-[9px] font-bold text-slate-300 dark:text-slate-600">•</span>
                                                    <span class="text-[9px] font-black text-primary uppercase tracking-wider" title="Departamento del Catálogo"><?= htmlspecialchars($u['departamento_nombre']) ?></span>
                                                <?php elseif (!empty($u['departamento'])): ?>
                                                    <span class="text-[9px] font-bold text-slate-300 dark:text-slate-600">•</span>
                                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider" title="Departamento Histórico"><?= htmlspecialchars($u['departamento']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 align-middle">
                                    <span class="text-[10px] font-black text-primary bg-teal-50 dark:bg-teal-900/20 px-3 py-1.5 rounded-lg border border-teal-100/30 uppercase tracking-widest">
                                        <?= htmlspecialchars($u['rol_nombre']) ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6 align-middle text-center">
                                    <?php if (!empty($u['firma_digital'])): ?>
                                        <div class="flex items-center justify-center text-emerald-500 gap-1.5" title="Certificado Vinculado">
                                            <span class="material-symbols-outlined text-[20px] font-variation-settings: 'FILL' 1">verified</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="flex items-center justify-center text-slate-200 dark:text-slate-700" title="Pendiente de Firma">
                                            <span class="material-symbols-outlined text-[20px]">pending</span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-8 py-6 align-middle text-right">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all transform group-hover:translate-x-0 translate-x-2">
                                        <button onclick='editarUsuario(<?= json_encode($u) ?>)' class="size-9 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-800 rounded-xl flex items-center justify-center text-slate-400 hover:text-primary transition-all shadow-sm" title="Editar Credenciales">
                                            <span class="material-symbols-outlined text-lg">edit</span>
                                        </button>
                                        <form method="POST" action="/usuarios/estado/<?= $u['id'] ?>" style="display:inline;">
                                            <?= \App\Core\Security::csrfInput() ?>
                                            <button type="submit" class="size-9 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-800 rounded-xl flex items-center justify-center text-slate-400 hover:text-<?= $u['status'] == 1 ? 'secondary' : 'emerald-500' ?> transition-all shadow-sm" title="<?= $u['status'] == 1 ? 'Desactivar Agente' : 'Activar Agente' ?>">
                                                <span class="material-symbols-outlined text-lg"><?= $u['status'] == 1 ? 'block' : 'undo' ?></span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if (isset($totalPaginas) && $totalPaginas > 1): ?>
                <div class="flex justify-between items-center pt-6 pb-6 border-t border-slate-50 dark:border-slate-900 px-4">
                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">Mostrando <?= count($usuarios) ?> de <?= $totalRegistros ?> usuarios</p>
                    <div class="flex items-center gap-1">
                        <?php if ($pagina > 1): ?>
                            <a href="?p=<?= $pagina - 1 ?>" class="size-8 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-800 rounded-lg flex items-center justify-center text-slate-400 hover:text-primary hover:border-primary/20 transition-all shadow-sm">
                                <span class="material-symbols-outlined text-sm">chevron_left</span>
                            </a>
                        <?php endif; ?>

                        <?php for($i = 1; $i <= $totalPaginas; $i++): ?>
                            <a href="?p=<?= $i ?>" class="size-8 border rounded-lg flex items-center justify-center text-[10px] font-black transition-all <?= $i == $pagina ? 'bg-primary border-primary text-white shadow-lg shadow-teal-900/10' : 'bg-white dark:bg-slate-800 border-slate-100 dark:border-slate-800 text-slate-400 hover:text-primary hover:border-primary/20 shadow-sm' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($pagina < $totalPaginas): ?>
                            <a href="?p=<?= $pagina + 1 ?>" class="size-8 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-800 rounded-lg flex items-center justify-center text-slate-400 hover:text-primary hover:border-primary/20 transition-all shadow-sm">
                                <span class="material-symbols-outlined text-sm">chevron_right</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="flex justify-between items-center pt-6 pb-6 border-t border-slate-50 dark:border-slate-900 px-4">
                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">Mostrando todos los usuarios (<?= $totalRegistros ?? count($usuarios) ?>)</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- LDAP & Pendientes Inferior -->
        <div class="space-y-4 bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-[2rem] p-6 shadow-sm">
            <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                    </span>
                    <h2 class="text-xs font-black text-amber-600 uppercase tracking-widest whitespace-nowrap">Solicitudes de Acceso Pendientes</h2>
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    <div class="relative w-64 group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg group-focus-within:text-amber-500 transition-colors">search</span>
                        <input type="text" id="search-pendientes" placeholder="BUSCAR PENDIENTES..." class="w-full pl-12 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black tracking-widest text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-amber-500/50 transition-all placeholder:text-slate-300">
                    </div>
                </div>
            </div>

            <?php if (!empty($pendientes)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-4 border-t border-slate-100 dark:border-slate-800" id="pendientes-container">
                <?php foreach ($pendientes as $p): ?>
                    <div class="pendiente-card bg-amber-50/50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800 rounded-3xl p-6 transition-all hover:shadow-lg">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="size-10 bg-amber-100 dark:bg-amber-800 rounded-2xl flex items-center justify-center text-amber-600 font-black">
                                    <?= strtoupper(substr($p['usuario'], 0, 1)) ?>
                                </div>
                                <div>
                                    <p class="pendiente-text text-[11px] font-black text-slate-700 dark:text-white uppercase"><?= htmlspecialchars($p['usuario']) ?></p>
                                    <p class="pendiente-text text-[9px] text-slate-400 font-bold uppercase"><?= htmlspecialchars($p['departamento'] ?? 'Sin Depto') ?></p>
                                </div>
                            </div>
                            <button onclick='abrirAprobacion(<?= json_encode($p) ?>)' class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">
                                Aprobar
                            </button>
                        </div>
                        <div class="space-y-3">
                            <div class="p-3 bg-white dark:bg-slate-900/50 rounded-xl border border-amber-100/50 dark:border-amber-800/50">
                                <p class="text-[8px] text-slate-400 font-black uppercase mb-1 tracking-widest">Identidad Completa</p>
                                <p class="pendiente-text text-[10px] font-bold text-slate-600 dark:text-slate-300"><?= htmlspecialchars($p['nombre'] ?? 'No especificado') ?></p>
                            </div>

                            <!-- Trazabilidad de Solicitud -->
                            <?php if (isset($p['solicitud_info']) && $p['solicitud_info']): ?>
                            <div class="p-4 bg-amber-500/5 dark:bg-amber-400/5 rounded-2xl border border-amber-500/10 dark:border-amber-400/10 space-y-3">
                                <div class="flex items-center gap-2 text-amber-600 dark:text-amber-400">
                                    <span class="material-symbols-outlined text-xs">history</span>
                                    <p class="text-[8px] font-black uppercase tracking-widest">Trazabilidad de Registro</p>
                                </div>
                                <div class="pl-2 border-l-2 border-amber-500/20 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <p class="text-[8px] font-black text-slate-400 uppercase"><?= date('d/m/Y H:i', strtotime($p['solicitud_info']['fecha'])) ?></p>
                                        <div class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[10px] text-slate-300">lan</span>
                                            <span class="text-[8px] font-bold text-slate-300 uppercase"><?= $p['solicitud_info']['ip'] ?: '0.0.0.0' ?></span>
                                        </div>
                                    </div>
                                    <p class="text-[9px] text-slate-500 dark:text-slate-400 font-medium italic leading-relaxed line-clamp-2">
                                        "<?= htmlspecialchars($p['solicitud_info']['detalles']) ?>"
                                    </p>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800 text-center">
                                <p class="text-[8px] font-black text-slate-300 uppercase italic">Sin detalles de bitácora registrados</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-center py-6 bg-slate-50/50 dark:bg-slate-900/20 rounded-2xl">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-lg">check_circle</span>
                    No hay solicitudes pendientes en este momento.
                </p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Modals -->
        <div id="modal-nuevo" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-white/60 dark:bg-slate-950/60 backdrop-blur-xl">
            <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-[2.5rem] shadow-2xl p-10 space-y-6 relative overflow-y-auto max-h-[90vh] custom-scrollbar">
                <button onclick="closeModal('modal-nuevo')" class="absolute top-8 right-8 text-slate-300 hover:text-secondary"><span class="material-symbols-outlined">close</span></button>
                <div class="space-y-1">
                    <h3 class="text-xl font-black text-slate-custom dark:text-white uppercase tracking-tight">Nuevo Usuario</h3>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Inscripción en base de datos</p>
                </div>
                <form action="/usuarios/guardar" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <?= \App\Core\Security::csrfInput() ?>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Usuario</label>
                            <input type="text" name="usuario" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none" required>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Contraseña</label>
                            <input type="password" name="password" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none" required>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Nombre Completo</label>
                        <input type="text" name="nombre" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none" required>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Correo Electrónico</label>
                        <input type="email" name="email" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Cargo</label>
                            <input type="text" name="cargo" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none" required>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Rol</label>
                            <select name="rol_id" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none appearance-none" required>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?= $rol['id'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Departamento del Catálogo</label>
                        <select name="departamento_id" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none appearance-none">
                            <option value="">-- SELECCIONE DEPARTAMENTO --</option>
                            <?php foreach ($departamentos as $dept): ?>
                                <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Firma Digital (P12, PEM, PNG)</label>
                        <div class="relative group">
                            <input type="file" name="firma_digital" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold text-slate-300 flex items-center gap-2 group-hover:bg-slate-100 dark:group-hover:bg-slate-800 transition-all">
                                <span class="material-symbols-outlined text-lg">upload_file</span>
                                <span id="file-label-new">Adjuntar Certificado o Firma</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] shadow-lg shadow-teal-900/20">Registrar Agente</button>
                </form>
            </div>
        </div>

        <div id="modal-editar" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-white/60 dark:bg-slate-950/60 backdrop-blur-xl">
            <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-[2.5rem] shadow-2xl p-10 space-y-6 relative overflow-y-auto max-h-[90vh] custom-scrollbar">
                <button onclick="closeModal('modal-editar')" class="absolute top-8 right-8 text-slate-300 hover:text-secondary"><span class="material-symbols-outlined">close</span></button>
                <div class="space-y-1">
                    <h3 class="text-xl font-black text-slate-custom dark:text-white uppercase tracking-tight">Editar Usuario / Rol</h3>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Actualización de credenciales</p>
                </div>
                <form action="/usuarios/actualizar" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <?= \App\Core\Security::csrfInput() ?>
                    <input type="hidden" name="id" id="edit-id">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Usuario</label>
                            <input type="text" name="usuario" id="edit-usuario" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none" required>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Contraseña</label>
                            <input type="password" name="password" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none" placeholder="DEJAR VACÍO">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Nombre Completo</label>
                        <input type="text" name="nombre" id="edit-nombre" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Correo Electrónico</label>
                        <input type="email" name="email" id="edit-email" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Cargo</label>
                            <input type="text" name="cargo" id="edit-cargo" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Rol</label>
                            <select name="rol_id" id="edit-rol" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none appearance-none" required>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?= $rol['id'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-[9px] text-slate-400 font-medium italic mt-1">Para configurar los permisos de un rol, vaya a <a href="/configuracion/permisos" class="text-primary hover:underline">Matriz de Permisos</a>.</p>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Departamento del Catálogo</label>
                        <select name="departamento_id" id="edit-departamento-id" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none appearance-none">
                            <option value="">-- SELECCIONE DEPARTAMENTO --</option>
                            <?php foreach ($departamentos as $dept): ?>
                                <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Actualizar Firma/Certificado (P12, PNG)</label>
                        <div class="relative group">
                            <input type="file" name="firma_digital" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold text-slate-300 flex items-center gap-2 group-hover:bg-slate-100 dark:group-hover:bg-slate-800 transition-all">
                                <span class="material-symbols-outlined text-lg">upload_file</span>
                                <span id="file-label-edit">Reemplazar Firma Actual</span>
                            </div>
                        </div>
                        <p id="firma-status" class="text-[8px] text-emerald-500 font-bold uppercase ml-1 hidden">✓ El usuario ya tiene una firma vinculada</p>
                    </div>

                    <button type="submit" class="w-full bg-slate-custom text-white py-4 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em]">Guardar Cambios</button>
                </form>
            </div>
        </div>

        <div id="modal-aprobar" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-white/60 dark:bg-slate-950/60 backdrop-blur-xl">
            <div class="bg-white dark:bg-slate-900 w-full max-w-sm rounded-[2.5rem] shadow-2xl p-10 space-y-8 relative">
                <button onclick="closeModal('modal-aprobar')" class="absolute top-8 right-8 text-slate-300 hover:text-secondary"><span class="material-symbols-outlined">close</span></button>
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="size-16 bg-amber-100 text-amber-500 rounded-[2rem] flex items-center justify-center">
                        <span class="material-symbols-outlined text-3xl">verified_user</span>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-xl font-black text-slate-custom dark:text-white uppercase tracking-tight">Aprobar Acceso</h3>
                        <p id="aprobar-nombre" class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"></p>
                    </div>
                </div>
                <form id="form-aprobar" method="POST" class="space-y-6">
                    <?= \App\Core\Security::csrfInput() ?>
                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Asignar Rol Inicial</label>
                        <select name="rol_id" class="w-full px-4 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none appearance-none" required>
                            <?php foreach ($roles as $rol): ?>
                                <option value="<?= $rol['id'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full bg-emerald-500 text-white py-4 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] shadow-lg shadow-emerald-900/20">Confirmar Aprobación</button>
                    <p class="text-[9px] text-slate-400 text-center italic leading-relaxed">Al aprobar, el usuario podrá acceder a todos los módulos permitidos por el rol asignado.</p>
                </form>
            </div>
        </div>

        <div id="modal-ldap-buscar" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-white/60 dark:bg-slate-950/60 backdrop-blur-xl">
            <div class="bg-white dark:bg-slate-900 w-full max-w-2xl rounded-[2.5rem] shadow-2xl p-10 space-y-8 relative max-h-[90vh] flex flex-col">
                <button onclick="closeModal('modal-ldap-buscar')" class="absolute top-8 right-8 text-slate-300 hover:text-secondary"><span class="material-symbols-outlined">close</span></button>
                <div class="space-y-1 shrink-0">
                    <h3 class="text-xl font-black text-slate-custom dark:text-white uppercase tracking-tight">Directorio Activo (LDAP)</h3>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Búsqueda y Activación de Usuarios Institucionales</p>
                </div>
                
                <div class="flex gap-2 shrink-0">
                    <input type="text" id="ldap-search-input" placeholder="INGRESE NOMBRE O USUARIO A BUSCAR..." class="flex-1 px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none">
                    <button onclick="buscarEnLdap()" class="bg-primary hover:bg-[#005f6b] text-white px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-teal-900/10 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">search</span> Buscar
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto custom-scrollbar border border-slate-100 dark:border-slate-800 rounded-2xl bg-slate-50/50 dark:bg-slate-900/50 p-2">
                    <div id="ldap-results" class="space-y-2">
                        <div class="text-center py-8 text-slate-400">
                            <span class="material-symbols-outlined text-4xl mb-2 opacity-50">directory_sync</span>
                            <p class="text-[10px] font-bold uppercase tracking-widest">Ingrese un término para buscar en LDAP</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Confirmar Sincronización LDAP -->
        <div id="modal-confirm-sync" class="fixed inset-0 z-[60] hidden items-center justify-center p-6 bg-white/60 dark:bg-slate-950/60 backdrop-blur-xl">
            <div class="bg-white dark:bg-slate-900 w-full max-w-sm rounded-[2.5rem] shadow-2xl p-10 space-y-8 relative">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="size-16 bg-amber-100 dark:bg-amber-900/30 text-amber-500 rounded-[2rem] flex items-center justify-center">
                        <span class="material-symbols-outlined text-3xl">sync</span>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-xl font-black text-slate-custom dark:text-white uppercase tracking-tight">Sincronizar LDAP</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                            &iquest;Desea sincronizar la informaci&oacute;n (Nombres y Departamentos) de todos los usuarios con el Directorio Activo?
                        </p>
                        <p class="text-[10px] text-amber-500 font-bold uppercase tracking-widest">Esto puede tardar unos momentos.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button onclick="closeModal('modal-confirm-sync')" class="flex-1 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">Cancelar</button>
                    <button id="btn-confirm-sync-ok" onclick="ejecutarSincronizarLdap()" class="flex-1 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest bg-amber-500 hover:bg-amber-600 text-white transition-all shadow-lg shadow-amber-900/20">Confirmar</button>
                </div>
            </div>
        </div>

        <footer class="h-10 px-10 flex items-center justify-center shrink-0">
            <p class="text-[8px] font-black text-slate-200 dark:text-slate-800 uppercase tracking-[0.5em]">sgdoc User-Nexus • 2026</p>
        </footer>
    </main>

    <script>
        function openModal(id) { 
            const el = document.getElementById(id);
            el.classList.remove('hidden');
            el.classList.add('flex');
        }
        function closeModal(id) { 
            const el = document.getElementById(id);
            el.classList.add('hidden');
            el.classList.remove('flex');
        }
        function editarUsuario(u) {
            document.getElementById('edit-id').value = u.id;
            document.getElementById('edit-usuario').value = u.usuario;
            document.getElementById('edit-rol').value = u.rol_id;
            document.getElementById('edit-nombre').value = u.nombre || '';
            document.getElementById('edit-email').value = u.email || '';
            document.getElementById('edit-cargo').value = u.cargo || '';
            document.getElementById('edit-departamento-id').value = u.departamento_id || '';
            
            // Mostrar estado de firma
            const status = document.getElementById('firma-status');
            if (u.firma_digital) {
                status.classList.remove('hidden');
            } else {
                status.classList.add('hidden');
            }
            
            openModal('modal-editar');
        }
        function abrirAprobacion(u) {
            document.getElementById('aprobar-nombre').textContent = u.nombre || u.usuario;
            document.getElementById('form-aprobar').action = '/usuarios/aprobar/' + u.id;
            openModal('modal-aprobar');
        }

        // Listener para nombres de archivos
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                const labelId = this.name === 'firma_digital' 
                    ? (this.closest('form').action.includes('guardar') ? 'file-label-new' : 'file-label-edit')
                    : '';
                
                if (labelId && this.files && this.files[0]) {
                    const label = document.getElementById(labelId);
                    label.textContent = this.files[0].name;
                    label.classList.remove('text-slate-300');
                    label.classList.add('text-primary');
                }
            });
        });
        window.onclick = function(e) { 
            if (e.target.classList.contains('fixed') && e.target.classList.contains('inset-0')) {
                e.target.classList.add('hidden');
                e.target.classList.remove('flex');
            }
        }

        // Buscador de usuarios activos
        document.getElementById('search-usuarios')?.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#users-table-body .user-row');
            
            rows.forEach(row => {
                const textInfo = row.innerText.toLowerCase();
                row.style.display = textInfo.includes(term) ? '' : 'none';
            });
        });

        // Buscador de usuarios pendientes
        document.getElementById('search-pendientes')?.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('#pendientes-container .pendiente-card');
            
            cards.forEach(card => {
                const textInfo = card.innerText.toLowerCase();
                card.style.display = textInfo.includes(term) ? '' : 'none';
            });
        });

        async function buscarEnLdap() {
            const term = document.getElementById('ldap-search-input').value;
            if (!term) return;

            const resultsContainer = document.getElementById('ldap-results');
            resultsContainer.innerHTML = '<div class="text-center py-8 text-primary"><span class="material-symbols-outlined animate-spin text-4xl mb-2">sync</span><p class="text-[10px] font-bold uppercase tracking-widest">Buscando en Directorio Activo...</p></div>';

            try {
                const csrfToken = document.querySelector('input[name="csrf_token"]').value;
                const formData = new FormData();
                formData.append('termino', term);
                formData.append('csrf_token', csrfToken);
                
                const response = await fetch('/usuarios/buscar_ldap', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success && data.usuarios.length > 0) {
                    let html = '';
                    data.usuarios.forEach(u => {
                        html += `
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-4 rounded-xl flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-black text-slate-700 dark:text-white uppercase">${u.nombre || u.usuario}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">@${u.usuario} | ${u.departamento || 'Sin Depto'}</p>
                            </div>
                            <form action="/usuarios/activar_ldap" method="POST" class="shrink-0 flex items-center gap-2">
                                ${document.querySelector('input[name="csrf_token"]').outerHTML}
                                <input type="hidden" name="usuario" value="${u.usuario}">
                                <input type="hidden" name="nombre" value="${u.nombre}">
                                <input type="hidden" name="email" value="${u.email}">
                                <input type="hidden" name="departamento" value="${u.departamento}">
                                <input type="hidden" name="cargo" value="${u.cargo}">
                                <select name="rol_id" class="px-3 py-2 bg-slate-50 dark:bg-slate-900 border-none rounded-xl text-[9px] font-bold outline-none" required>
                                    ${document.querySelector('select[name="rol_id"]').innerHTML}
                                </select>
                                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-sm">
                                    Activar
                                </button>
                            </form>
                        </div>`;
                    });
                    resultsContainer.innerHTML = html;
                } else {
                    resultsContainer.innerHTML = '<div class="text-center py-8 text-amber-500"><span class="material-symbols-outlined text-4xl mb-2 opacity-50">warning</span><p class="text-[10px] font-bold uppercase tracking-widest">No se encontraron resultados en LDAP</p></div>';
                }
            } catch (error) {
                console.error(error);
                resultsContainer.innerHTML = '<div class="text-center py-8 text-red-500"><span class="material-symbols-outlined text-4xl mb-2 opacity-50">error</span><p class="text-[10px] font-bold uppercase tracking-widest">Error al conectar con LDAP</p></div>';
            }
        }

        let _syncBtn = null;

        function abrirConfirmSincronizar(btn) {
            _syncBtn = btn;
            openModal('modal-confirm-sync');
        }

        async function ejecutarSincronizarLdap() {
            closeModal('modal-confirm-sync');

            const btn = _syncBtn || document.getElementById('btn-sincronizar-ldap');
            const originalHTML = btn ? btn.innerHTML : '';

            if (btn) {
                btn.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">sync</span> Sincronizando...';
                btn.disabled = true;
            }

            try {
                const csrfToken = document.querySelector('input[name="csrf_token"]').value;
                const formData = new FormData();
                formData.append('csrf_token', csrfToken);

                const response = await fetch('/usuarios/sincronizar_ldap', { 
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                const modalBody = document.querySelector('#modal-confirm-sync .space-y-8');

                if (data.success) {
                    // Éxito
                    modalBody.innerHTML = `
                        <div class="flex flex-col items-center text-center space-y-4">
                            <div class="size-16 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-500 rounded-[2rem] flex items-center justify-center">
                                <span class="material-symbols-outlined text-3xl">check_circle</span>
                            </div>
                            <div class="space-y-2">
                                <h3 class="text-xl font-black text-slate-custom dark:text-white uppercase tracking-tight">Sincronizaci&oacute;n Completada</h3>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Usuarios actualizados desde el Directorio Activo.</p>
                                <p class="text-3xl font-black text-emerald-500">${data.actualizados || 0}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">registros actualizados</p>
                            </div>
                        </div>
                        <button onclick="closeModal('modal-confirm-sync'); location.reload();" class="w-full py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest bg-emerald-500 hover:bg-emerald-600 text-white transition-all">Aceptar</button>
                    `;
                } else {
                    // Error devuelto por el servidor
                    const errMsg = data.error || 'No se pudo conectar con el Directorio Activo.';
                    modalBody.innerHTML = `
                        <div class="flex flex-col items-center text-center space-y-4">
                            <div class="size-16 bg-red-100 dark:bg-red-900/30 text-red-500 rounded-[2rem] flex items-center justify-center">
                                <span class="material-symbols-outlined text-3xl">error</span>
                            </div>
                            <div class="space-y-2">
                                <h3 class="text-xl font-black text-slate-custom dark:text-white uppercase tracking-tight">Error de Sincronizaci&oacute;n</h3>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">${errMsg}</p>
                            </div>
                        </div>
                        <button onclick="closeModal('modal-confirm-sync');" class="w-full py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest bg-red-500 hover:bg-red-600 text-white transition-all">Cerrar</button>
                    `;
                    if (btn) { btn.innerHTML = originalHTML; btn.disabled = false; }
                }
                openModal('modal-confirm-sync');

            } catch(e) {
                console.error(e);
                const modalBody = document.querySelector('#modal-confirm-sync .space-y-8');
                modalBody.innerHTML = `
                    <div class="flex flex-col items-center text-center space-y-4">
                        <div class="size-16 bg-red-100 dark:bg-red-900/30 text-red-500 rounded-[2rem] flex items-center justify-center">
                            <span class="material-symbols-outlined text-3xl">error</span>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-xl font-black text-slate-custom dark:text-white uppercase tracking-tight">Error de Sincronizaci&oacute;n</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">${e.message || 'Error inesperado.'}</p>
                        </div>
                    </div>
                    <button onclick="closeModal('modal-confirm-sync');" class="w-full py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest bg-red-500 hover:bg-red-600 text-white transition-all">Cerrar</button>
                `;
                openModal('modal-confirm-sync');

                if (btn) {
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }
            }
        }
    </script>
</body>
</html>
