<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Mantenimiento de Tipos de Solicitud - sgdoc</title>
    
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
                <span class="text-slate-400">Mantenimiento</span>
                <span class="text-slate-300">›</span>
                <span class="text-primary">Tipos de Solicitud</span>
            </div>
            <div class="ml-auto flex items-center gap-6">
                <button onclick="toggleDarkMode()" class="text-slate-300 hover:text-primary transition-colors h-8 w-8 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">dark_mode</span>
                </button>
            </div>
        </header>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto px-12 py-10 space-y-10">
            
            <!-- Alerts -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-800/30 text-emerald-800 dark:text-emerald-200 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm">
                    <span class="material-symbols-outlined text-lg text-emerald-500">check_circle</span>
                    <span class="text-[11px] font-bold uppercase tracking-wider"><?= htmlspecialchars($_SESSION['success']) ?></span>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-800/30 text-red-800 dark:text-red-200 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm">
                    <span class="material-symbols-outlined text-lg text-red-500">error</span>
                    <span class="text-[11px] font-bold uppercase tracking-wider"><?= htmlspecialchars($_SESSION['error']) ?></span>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Page Title -->
            <div class="flex justify-between items-end">
                <div class="space-y-1">
                    <h1 class="text-[32px] font-black text-slate-custom dark:text-white tracking-tight">Tipos de Solicitud</h1>
                    <p class="text-[11px] text-slate-400 font-medium italic">Gestione las diferentes tipologías de solicitudes que los usuarios pueden registrar en el sistema.</p>
                </div>
            </div>

            <!-- Table Section -->
            <div class="space-y-6">
                <div class="flex items-center justify-between px-4">
                    <h2 class="text-xs font-black text-slate-400 uppercase tracking-widest">Catálogo de Tipologías</h2>
                    <div class="flex items-center gap-3 flex-wrap">
                        <button onclick="openModal('modal-nuevo')" class="bg-primary hover:bg-[#005f6b] text-white px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg shadow-teal-900/20">
                            <span class="material-symbols-outlined text-sm">add</span>
                            Nuevo Tipo de Solicitud
                        </button>
                        <div class="relative w-64 group">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg group-focus-within:text-primary transition-colors">search</span>
                            <input type="text" id="search-tipos" placeholder="BUSCAR TIPO SOLICITUD..." class="w-full pl-12 pr-4 py-2.5 bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black tracking-widest text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-primary/50 transition-all shadow-sm placeholder:text-slate-400">
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm overflow-hidden flex flex-col">
                    <div class="overflow-x-auto custom-scrollbar flex-1">
                        <table class="w-full text-left table-fixed min-w-[800px]">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-100 dark:border-slate-800">
                                    <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[10%]">Código</th>
                                    <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[35%]">Nombre</th>
                                    <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[30%]">Descripción</th>
                                    <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[12%] text-center">Estado</th>
                                    <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 w-[13%] text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tipos-table-body" class="divide-y divide-slate-50 dark:divide-slate-900">
                                <?php if (!empty($tipos)): ?>
                                    <?php foreach ($tipos as $t): ?>
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition-colors group tipo-row">
                                        <td class="px-8 py-6 align-middle">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">TSO-<?= str_pad($t['id'], 3, '0', STR_PAD_LEFT) ?></p>
                                        </td>
                                        <td class="px-8 py-6 align-middle">
                                            <div class="flex items-center gap-3">
                                                <div class="size-9 bg-teal-50 dark:bg-teal-950/20 rounded-xl flex items-center justify-center text-primary border border-teal-100/30 shadow-sm shrink-0">
                                                    <span class="material-symbols-outlined text-sm">lists</span>
                                                </div>
                                                <p class="text-[11px] font-black text-slate-700 dark:text-white uppercase tracking-tight line-clamp-2"><?= htmlspecialchars($t['nombre']) ?></p>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 align-middle">
                                            <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium truncate" title="<?= htmlspecialchars($t['descripcion'] ?? '') ?>">
                                                <?= htmlspecialchars($t['descripcion'] ?: 'Sin descripción') ?>
                                            </p>
                                        </td>
                                        <td class="px-8 py-6 align-middle text-center">
                                            <span class="inline-flex text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-widest <?= $t['activo'] == 1 ? 'text-emerald-600 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100/20' : 'text-slate-400 bg-slate-100 dark:bg-slate-800' ?>">
                                                <?= $t['activo'] == 1 ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 align-middle text-right">
                                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all transform group-hover:translate-x-0 translate-x-2">
                                                <button onclick='editarTipo(<?= json_encode($t) ?>)' class="size-9 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-800 rounded-xl flex items-center justify-center text-slate-400 hover:text-primary transition-all shadow-sm" title="Editar Tipo">
                                                    <span class="material-symbols-outlined text-lg">edit</span>
                                                </button>
                                                <form method="POST" action="/tipos-solicitudes/estado/<?= $t['id'] ?>" style="display:inline;">
                                                    <?= \App\Core\Security::csrfInput() ?>
                                                    <button type="submit" class="size-9 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-800 rounded-xl flex items-center justify-center text-slate-400 hover:text-<?= $t['activo'] == 1 ? 'secondary' : 'emerald-500' ?> transition-all shadow-sm" title="<?= $t['activo'] == 1 ? 'Desactivar' : 'Activar' ?>">
                                                        <span class="material-symbols-outlined text-lg"><?= $t['activo'] == 1 ? 'block' : 'undo' ?></span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="py-12 text-center text-slate-400">
                                            <span class="material-symbols-outlined text-4xl opacity-30 mb-2">folder_open</span>
                                            <p class="text-[10px] font-bold uppercase tracking-widest">No hay tipos de solicitudes registrados</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if (isset($totalPaginas) && $totalPaginas > 1): ?>
                    <div class="flex justify-between items-center pt-6 pb-6 border-t border-slate-50 dark:border-slate-900 px-4">
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">Mostrando <?= count($tipos) ?> de <?= $totalRegistros ?> tipos</p>
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
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">Mostrando todos los tipos (<?= $totalRegistros ?? count($tipos) ?>)</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div id="modal-nuevo" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-white/60 dark:bg-slate-950/60 backdrop-blur-xl">
            <div class="bg-white dark:bg-slate-900 w-full max-w-sm rounded-[2.5rem] shadow-2xl p-10 space-y-8 relative">
                <button onclick="closeModal('modal-nuevo')" class="absolute top-8 right-8 text-slate-300 hover:text-secondary"><span class="material-symbols-outlined">close</span></button>
                <div class="space-y-1">
                    <h3 class="text-xl font-black text-slate-custom dark:text-white uppercase tracking-tight">Nuevo Tipo</h3>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Registrar nueva tipología de solicitud</p>
                </div>
                <form action="/tipos-solicitudes/guardar" method="POST" class="space-y-4">
                    <?= \App\Core\Security::csrfInput() ?>
                    <div class="space-y-1">
                        <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Nombre</label>
                        <input type="text" name="nombre" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none" placeholder="EJ. SOLICITUD DE CAPACITACIÓN" required>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Descripción</label>
                        <textarea name="descripcion" rows="3" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none resize-none" placeholder="OPCIONAL..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] shadow-lg shadow-teal-900/20">Crear Tipo</button>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div id="modal-editar" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-white/60 dark:bg-slate-950/60 backdrop-blur-xl">
            <div class="bg-white dark:bg-slate-900 w-full max-w-sm rounded-[2.5rem] shadow-2xl p-10 space-y-8 relative">
                <button onclick="closeModal('modal-editar')" class="absolute top-8 right-8 text-slate-300 hover:text-secondary"><span class="material-symbols-outlined">close</span></button>
                <div class="space-y-1">
                    <h3 class="text-xl font-black text-slate-custom dark:text-white uppercase tracking-tight">Editar Tipo</h3>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Modificar tipología existente</p>
                </div>
                <form action="/tipos-solicitudes/actualizar" method="POST" class="space-y-4">
                    <?= \App\Core\Security::csrfInput() ?>
                    <input type="hidden" name="id" id="edit-id">
                    <div class="space-y-1">
                        <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Nombre</label>
                        <input type="text" name="nombre" id="edit-nombre" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none" required>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Descripción</label>
                        <textarea name="descripcion" id="edit-descripcion" rows="3" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none resize-none"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-slate-custom text-white py-4 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em]">Guardar Cambios</button>
                </form>
            </div>
        </div>

        <footer class="h-10 px-10 flex items-center justify-center shrink-0">
            <p class="text-[8px] font-black text-slate-200 dark:text-slate-800 uppercase tracking-[0.5em]">sgdoc Nexus • 2026</p>
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
        function editarTipo(t) {
            document.getElementById('edit-id').value = t.id;
            document.getElementById('edit-nombre').value = t.nombre;
            document.getElementById('edit-descripcion').value = t.descripcion || '';
            openModal('modal-editar');
        }

        window.onclick = function(e) { 
            if (e.target.classList.contains('fixed') && e.target.classList.contains('inset-0')) {
                e.target.classList.add('hidden');
                e.target.classList.remove('flex');
            }
        }

        // Buscador
        document.getElementById('search-tipos')?.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#tipos-table-body .tipo-row');
            
            rows.forEach(row => {
                const textInfo = row.innerText.toLowerCase();
                row.style.display = textInfo.includes(term) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
