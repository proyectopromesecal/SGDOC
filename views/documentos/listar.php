<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Catálogo de Documentos - sgdoc</title>
    
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
                <span class="text-primary">Documentos</span>
            </div>
            
            <div class="ml-auto flex items-center gap-6">
                <button onclick="toggleDarkMode()" class="text-slate-300 hover:text-primary transition-colors h-8 w-8 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">dark_mode</span>
                </button>
            </div>
        </header>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto px-12 py-10 space-y-10">
            
            <!-- Page Title Area -->
            <div class="flex justify-between items-end">
                <div class="space-y-1">
                    <h1 class="text-[32px] font-black text-slate-custom dark:text-white tracking-tight uppercase tracking-tighter">Gestión Documental</h1>
                    <p class="text-[11px] text-slate-400 font-medium italic">Monitor central de auditoría y ciclo de vida institucional.</p>
                </div>
                
                <div class="flex items-center gap-4">
                    <form method="GET" action="/documentos" class="flex items-center gap-3">
                        <select name="estado" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl py-2 pl-4 pr-10 text-[10px] font-black uppercase tracking-widest focus:ring-1 focus:ring-primary/20 transition-all appearance-none cursor-pointer" onchange="this.form.submit()">
                            <option value="">Filtrar Estado</option>
                            <?php if ($_SESSION['rol_nombre'] !== 'Pendiente de Acceso'): ?>
                                <option value="SOLICITADO" <?= (isset($_GET['estado']) && $_GET['estado'] == 'SOLICITADO') ? 'selected' : '' ?>>Solicitados</option>
                                <option value="APROBADO_COMPRAS" <?= (isset($_GET['estado']) && $_GET['estado'] == 'APROBADO_COMPRAS') ? 'selected' : '' ?>>Por Aprobar</option>
                                <option value="AUTORIZADO" <?= (isset($_GET['estado']) && $_GET['estado'] == 'AUTORIZADO') ? 'selected' : '' ?>>Autorizados</option>
                                <option value="RECHAZADO" <?= (isset($_GET['estado']) && $_GET['estado'] == 'RECHAZADO') ? 'selected' : '' ?>>Rechazados</option>
                            <?php endif; ?>
                        </select>
                    </form>

                    <?php if (in_array($_SESSION['rol_nombre'], ['Jefe de departamento', 'Administrador'])): ?>
                    <a href="/documentos/crear" class="bg-primary hover:bg-[#005f6b] text-white px-6 py-2.5 rounded-full font-black text-[10px] uppercase tracking-widest shadow-xl shadow-teal-900/10 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">add_circle</span>
                        Nueva Solicitud
                    </a>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="flex items-center gap-10 border-b border-slate-100 dark:border-slate-800 shrink-0">
            <?php if ($_SESSION['rol_nombre'] !== 'Pendiente de Acceso'): ?>
                <a href="/documentos" class="pb-4 text-[11px] font-black uppercase tracking-[0.2em] transition-all relative <?= !isset($_GET['estado']) ? 'text-primary' : 'text-slate-400 hover:text-slate-600' ?>">
                    Todos
                    <?php if (!isset($_GET['estado'])): ?>
                        <div class="absolute bottom-0 left-0 w-full h-0.5 bg-primary animate-in fade-in slide-in-from-bottom-1"></div>
                    <?php endif; ?>
                </a>
                <a href="/documentos?estado=SOLICITADO" class="pb-4 text-[11px] font-black uppercase tracking-[0.2em] transition-all relative <?= (isset($_GET['estado']) && $_GET['estado'] == 'SOLICITADO') ? 'text-primary' : 'text-slate-400 hover:text-slate-600' ?>">
                    Por Procesar
                    <?php if (isset($_GET['estado']) && $_GET['estado'] == 'SOLICITADO'): ?>
                        <div class="absolute bottom-0 left-0 w-full h-0.5 bg-primary animate-in fade-in slide-in-from-bottom-1"></div>
                    <?php endif; ?>
                </a>
                <a href="/documentos?estado=AUTORIZADO" class="pb-4 text-[11px] font-black uppercase tracking-[0.2em] transition-all relative <?= (isset($_GET['estado']) && $_GET['estado'] == 'AUTORIZADO') ? 'text-primary' : 'text-slate-400 hover:text-slate-600' ?>">
                    Autorizados
                    <?php if (isset($_GET['estado']) && $_GET['estado'] == 'AUTORIZADO'): ?>
                        <div class="absolute bottom-0 left-0 w-full h-0.5 bg-primary animate-in fade-in slide-in-from-bottom-1"></div>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            </div>

            <div class="bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm overflow-hidden min-h-[500px] flex flex-col">
                <div class="overflow-x-auto custom-scrollbar flex-1">
                    <table class="w-full text-left table-fixed min-w-[1000px]">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-100 dark:border-slate-800">
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[10%]">ID Registro</th>
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[12%]">Urgencia</th>
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[31%]">Documento & Finalidad</th>
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[18%]">Gestor</th>
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[15%]">Estado Acta</th>
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[10%] text-center">Fecha</th>
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[12%] text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-900">
                            <?php if (empty($documentos)): ?>
                                <tr>
                                    <td colspan="6" class="py-32 text-center">
                                        <div class="opacity-20">
                                            <span class="material-symbols-outlined text-7xl block mb-4">inventory_2</span>
                                            <p class="text-[10px] font-black uppercase tracking-[0.3em]">Repositorio vacío / Sin resultados</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($documentos as $doc): ?>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition-colors group">
                                    <td class="px-8 py-6 align-middle">
                                        <span class="text-[10px] font-black text-primary bg-teal-50 dark:bg-teal-900/20 px-3 py-1.5 rounded-lg border border-teal-100/30">
                                            <?= htmlspecialchars($doc['id']) ?>
                                        </span>
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
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-black text-slate-700 dark:text-white uppercase leading-tight truncate"><?= htmlspecialchars($doc['tipo']) ?></p>
                                            <p class="text-[10px] text-slate-400 font-medium italic truncate"><?= htmlspecialchars($doc['descripcion']) ?></p>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 align-middle">
                                        <div class="flex items-center gap-3">
                                            <div class="size-8 bg-slate-50 dark:bg-slate-800 rounded-xl flex items-center justify-center text-[10px] font-black text-slate-400 border border-slate-100 dark:border-slate-800 shadow-sm">
                                                <?= strtoupper(substr($doc['nombre_usuario'], 0, 1)) ?>
                                            </div>
                                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tight truncate"><?= htmlspecialchars($doc['nombre_usuario']) ?></span>
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
                                            <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full <?= $colorClass ?>">
                                                <?= htmlspecialchars($doc['estado']) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 align-middle text-center">
                                        <span class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-tighter">
                                            <?= date('d/m/Y', strtotime($doc['fecha_creacion'])) ?>
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 align-middle text-right">
                                        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all transform group-hover:translate-x-0 translate-x-2">
                                            <a href="/documentos/ver/<?= htmlspecialchars($doc['id']) ?>" class="size-9 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-800 rounded-xl flex items-center justify-center text-slate-400 hover:text-primary transition-all shadow-sm">
                                                <span class="material-symbols-outlined text-lg">visibility</span>
                                            </a>
                                            <a href="/documentos/descargar/<?= htmlspecialchars($doc['id']) ?>/original" class="size-9 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-800 rounded-xl flex items-center justify-center text-slate-400 hover:text-primary transition-all shadow-sm">
                                                <span class="material-symbols-outlined text-lg">download</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPaginas > 1): ?>
            <div class="flex justify-between items-center pt-6 border-t border-slate-50 dark:border-slate-900 px-4">
                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">Mostrando <?= count($documentos) ?> de <?= $totalRegistros ?> documentos</p>
                <div class="flex items-center gap-1">
                    <?php if ($pagina > 1): ?>
                        <a href="?p=<?= $pagina - 1 ?><?= isset($_GET['estado']) ? '&estado='.$_GET['estado'] : '' ?>" class="size-8 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-800 rounded-lg flex items-center justify-center text-slate-400 hover:text-primary hover:border-primary/20 transition-all shadow-sm">
                            <span class="material-symbols-outlined text-sm">chevron_left</span>
                        </a>
                    <?php endif; ?>

                    <?php for($i = 1; $i <= $totalPaginas; $i++): ?>
                        <a href="?p=<?= $i ?><?= isset($_GET['estado']) ? '&estado='.$_GET['estado'] : '' ?>" class="size-8 border rounded-lg flex items-center justify-center text-[10px] font-black transition-all <?= $i == $pagina ? 'bg-primary border-primary text-white shadow-lg shadow-teal-900/10' : 'bg-white dark:bg-slate-800 border-slate-100 dark:border-slate-800 text-slate-400 hover:text-primary hover:border-primary/20 shadow-sm' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($pagina < $totalPaginas): ?>
                        <a href="?p=<?= $pagina + 1 ?><?= isset($_GET['estado']) ? '&estado='.$_GET['estado'] : '' ?>" class="size-8 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-800 rounded-lg flex items-center justify-center text-slate-400 hover:text-primary hover:border-primary/20 transition-all shadow-sm">
                            <span class="material-symbols-outlined text-sm">chevron_right</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="flex justify-between items-center pt-6 border-t border-slate-50 dark:border-slate-900 px-4">
                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">Mostrando todos los documentos (<?= $totalRegistros ?>)</p>
                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">sgdoc • 2026</p>
            </div>
            <?php endif; ?>
        </div>

        <footer class="h-10 px-10 flex items-center justify-center shrink-0">
            <p class="text-[8px] font-black text-slate-200 dark:text-slate-800 uppercase tracking-[0.5em]">sgdoc Audit-Point • Promese/Cal • 2026</p>
        </footer>
    </main>
</body>
</html>
