<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Historial de Auditoría (Bitácora) - sgdoc</title>
    
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
                <span class="text-primary">Bitácora de Auditoría</span>
            </div>
            
            <div class="ml-auto flex items-center gap-6">
                <button onclick="toggleDarkMode()" class="text-slate-300 hover:text-primary transition-colors h-8 w-8 flex items-center justify-center rounded-lg">
                    <span class="material-symbols-outlined text-xl">dark_mode</span>
                </button>
            </div>
        </header>

        <!-- Scrollable Body -->
        <div class="flex-1 overflow-y-auto p-10 space-y-8">
            
            <!-- Page Title Area -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                <div class="space-y-1">
                    <h1 class="text-[32px] font-black text-slate-custom dark:text-white tracking-tight uppercase">Trazabilidad de Sistema</h1>
                    <p class="text-slate-400 text-[11px] font-medium italic">Registro inmutable de acciones, eventos y procesos de firma digital.</p>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="flex gap-2">
                        <button class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-primary transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">filter_list</span> Filtrar Logs
                        </button>
                        <button class="bg-slate-custom text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">download</span> Exportar CSV
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-50 dark:border-slate-800 flex items-center gap-4">
                    <div class="size-10 bg-primary/5 text-primary rounded-xl flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined font-variation-settings: 'FILL' 1">history</span>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest">Total Eventos</p>
                        <p class="text-lg font-black text-slate-700 dark:text-white"><?= $totalRegistros ?? count($registros) ?></p>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-50 dark:border-slate-800 flex items-center gap-4">
                    <div class="size-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined font-variation-settings: 'FILL' 1">verified</span>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest">Firmas Exitosas</p>
                        <p class="text-lg font-black text-slate-700 dark:text-white">Active</p>
                    </div>
                </div>
            </div>

            <!-- Main Log Block -->
            <div class="bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm overflow-hidden min-h-[500px] flex flex-col">
                <div class="overflow-x-auto custom-scrollbar flex-1">
                    <table class="w-full text-left table-fixed min-w-[1000px]">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-100 dark:border-slate-800">
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[8%] text-center">Referencia</th>
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[17%]">Instante Auditado</th>
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[15%]">Agente</th>
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[20%]">Acción / Evento</th>
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[30%]">Descargo Técnico</th>
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[10%] text-right">Integridad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-900">
                            <?php if (empty($registros)): ?>
                                <tr>
                                    <td colspan="6" class="py-32 text-center">
                                        <div class="opacity-20">
                                            <span class="material-symbols-outlined text-7xl block mb-4">database_off</span>
                                            <p class="text-[10px] font-black uppercase tracking-[0.3em]">Historial inactivo o sin registros</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($registros as $index => $log): ?>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition-colors group">
                                    <td class="px-8 py-6 align-middle text-center">
                                        <span class="text-[9px] font-black text-slate-200 dark:text-slate-700 group-hover:text-primary transition-colors">
                                            #<?= str_pad($index + 1, 3, '0', STR_PAD_LEFT) ?>
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 align-middle">
                                        <div class="space-y-0.5">
                                            <p class="text-[11px] font-black text-slate-700 dark:text-white uppercase tracking-tight"><?= date('d M Y', strtotime($log['fecha'])) ?></p>
                                            <p class="text-[9px] text-slate-300 font-bold uppercase tracking-widest"><?= date('H:i:s', strtotime($log['fecha'])) ?></p>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 align-middle">
                                        <div class="flex items-center gap-3">
                                            <div class="size-8 bg-slate-50 dark:bg-slate-900 rounded-xl flex items-center justify-center text-[10px] font-black text-slate-400 border border-slate-100 dark:border-slate-800 shadow-sm">
                                                <?= strtoupper(substr($log['nombre_usuario'], 0, 1)) ?>
                                            </div>
                                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tight truncate"><?= htmlspecialchars($log['nombre_usuario']) ?></span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 align-middle">
                                        <?php
                                        $accion = strtoupper($log['accion']);
                                        $colorClass = 'text-primary bg-teal-50 dark:bg-teal-900/10';
                                        if (str_contains($accion, 'LOGIN')) $colorClass = 'text-blue-500 bg-blue-50 dark:bg-blue-900/10';
                                        if (str_contains($accion, 'FIRMA') || str_contains($accion, 'AUTORIZ')) $colorClass = 'text-emerald-500 bg-emerald-50 dark:bg-emerald-900/10';
                                        if (str_contains($accion, 'RECHAZ') || str_contains($accion, 'ERROR')) $colorClass = 'text-secondary bg-red-50 dark:bg-red-900/10';
                                        ?>
                                        <div class="flex">
                                            <span class="text-[9px] font-black <?= $colorClass ?> uppercase tracking-widest px-3 py-1.5 rounded-lg border border-transparent shadow-sm">
                                                <?= htmlspecialchars($accion) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 align-middle">
                                        <p class="text-[10px] text-slate-400 font-medium leading-relaxed italic truncate group-hover:whitespace-normal transition-all" title="<?= htmlspecialchars($log['detalles']) ?>">
                                            <?= htmlspecialchars($log['detalles']) ?>
                                        </p>
                                    </td>
                                    <td class="px-8 py-6 align-middle text-right">
                                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-emerald-50 dark:bg-emerald-900/10 text-emerald-600 rounded-md">
                                            <span class="size-1 bg-emerald-500 rounded-full animate-pulse"></span>
                                            <span class="text-[8px] font-black uppercase">SAFE</span>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Audit info with Pagination -->
                <div class="px-10 py-6 bg-slate-50/50 dark:bg-slate-900/80 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">enhanced_encryption</span>
                        Registros cifrados con firma de tiempo (TSA) - Mostrando <?= count($registros) ?> de <?= $totalRegistros ?>
                    </p>
                    <?php if (isset($totalPaginas) && $totalPaginas > 1): ?>
                    <div class="flex items-center gap-4">
                        <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Página <?= $pagina ?> de <?= $totalPaginas ?></span>
                        <div class="flex gap-1">
                            <?php if ($pagina > 1): ?>
                            <a href="?p=<?= $pagina - 1 ?><?= !empty($_SERVER['QUERY_STRING']) ? '&'.preg_replace('/(^|&)p=[^&]*/', '', $_SERVER['QUERY_STRING']) : '' ?>" class="p-2 text-slate-400 hover:text-primary transition-colors flex items-center justify-center">
                                <span class="material-symbols-outlined text-sm">west</span>
                            </a>
                            <?php endif; ?>
                            
                            <?php if ($pagina < $totalPaginas): ?>
                            <a href="?p=<?= $pagina + 1 ?><?= !empty($_SERVER['QUERY_STRING']) ? '&'.preg_replace('/(^|&)p=[^&]*/', '', $_SERVER['QUERY_STRING']) : '' ?>" class="p-2 text-slate-400 hover:text-primary transition-colors flex items-center justify-center">
                                <span class="material-symbols-outlined text-sm">east</span>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- System Footer -->
        <footer class="h-10 px-10 bg-slate-50 dark:bg-slate-900 border-t border-slate-200/50 dark:border-slate-800 flex items-center justify-center shrink-0">
            <p class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.4em]">sgdoc Secure-Audit • Promese/Cal • <?= date('Y') ?></p>
        </footer>
    </main>
</body>
</html>
