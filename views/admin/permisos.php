<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Matriz de Permisos - sgdoc</title>
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
                    colors: { primary: "#007281", secondary: "#E41E26", "slate-custom": "#111827" },
                    fontFamily: { sans: ["'Plus Jakarta Sans'", "sans-serif"] },
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
                <span class="hover:text-primary transition-colors cursor-pointer" onclick="location.href='/configuracion'">Configuración</span>
                <span class="text-slate-300">›</span>
                <span class="text-primary">Matriz de Permisos</span>
            </div>
            <div class="ml-auto flex items-center gap-6">
                <button onclick="toggleDarkMode()" class="text-slate-300 hover:text-primary transition-colors h-8 w-8 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">dark_mode</span>
                </button>
            </div>
        </header>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto px-12 py-10 space-y-10">
            
            <div class="max-w-7xl mx-auto space-y-10">
                <!-- Page Title -->
                <div class="flex justify-between items-end">
                    <div class="space-y-1">
                        <h1 class="text-[32px] font-black text-slate-custom dark:text-white tracking-tight uppercase">Seguridad por Roles</h1>
                        <p class="text-[11px] text-slate-400 font-medium italic">Matriz de control de acceso dinámica. Defina qué acciones puede realizar cada perfil en el sistema.</p>
                    </div>
                </div>

                <!-- Messages -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 text-secondary rounded-2xl flex items-center gap-3 text-xs font-bold border border-red-100/50">
                        <span class="material-symbols-outlined text-lg">error</span>
                        <?= htmlspecialchars($_SESSION['error']) ?><?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 rounded-2xl flex items-center gap-3 text-xs font-bold border border-emerald-100/50">
                        <span class="material-symbols-outlined text-lg">check_circle</span>
                        <?= htmlspecialchars($_SESSION['success']) ?><?php unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <form action="/configuracion/permisos/guardar" method="POST" class="space-y-8">
                    <?= \App\Core\Security::csrfInput() ?>
                    <div class="bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm overflow-hidden">
                        <table class="w-full text-left table-fixed">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-100 dark:border-slate-800">
                                    <th class="px-8 py-6 w-[25%] text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest border-r border-slate-100 dark:border-slate-800">Acción / Permiso</th>
                                    <?php foreach ($roles as $rol): ?>
                                        <th class="px-4 py-6 text-center text-[10px] font-black text-slate-600 dark:text-slate-200 uppercase tracking-widest">
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="material-symbols-outlined text-primary mb-1">badge</span>
                                                <div class="mb-2"><?= htmlspecialchars($rol['nombre']) ?></div>
                                                <?php if ($rol['nombre'] !== 'Administrador'): ?>
                                                    <button type="button" onclick="selectAllByRole(<?= $rol['id'] ?>)" class="text-[8px] text-primary hover:text-secondary transition-colors underline decoration-dotted">Todo</button>
                                                <?php endif; ?>
                                            </div>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-900">
                                <?php foreach ($permisosAgrupados as $modulo => $permisos): ?>
                                    <tr class="bg-slate-100/30 dark:bg-slate-800/20">
                                        <td colspan="<?= count($roles) + 1 ?>" class="px-8 py-3 text-[9px] font-black text-primary uppercase tracking-[0.2em] italic">
                                            Módulo: <?= htmlspecialchars($modulo) ?>
                                        </td>
                                    </tr>
                                    <?php foreach ($permisos as $p): ?>
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition-colors">
                                            <td class="px-8 py-5 border-r border-slate-100 dark:border-slate-800">
                                                <div class="space-y-0.5">
                                                    <p class="text-[11px] font-bold text-slate-700 dark:text-white uppercase"><?= htmlspecialchars($p['nombre']) ?></p>
                                                    <p class="text-[9px] text-slate-400 font-medium leading-tight"><?= htmlspecialchars($p['descripcion']) ?></p>
                                                </div>
                                            </td>
                                            <?php foreach ($roles as $rol): ?>
                                                <td class="px-4 py-5 text-center align-middle">
                                                    <?php if ($rol['nombre'] === 'Administrador'): ?>
                                                        <div class="flex flex-col items-center justify-center opacity-40 cursor-not-allowed" title="El Administrador tiene permisos fijos por seguridad">
                                                            <div class="w-10 h-5 bg-primary/40 rounded-full relative">
                                                                <div class="absolute right-[2px] top-[2px] bg-white rounded-full h-4 w-4"></div>
                                                            </div>
                                                            <span class="text-[7px] font-black uppercase tracking-tighter mt-1 text-primary">Inmutable</span>
                                                        </div>
                                                    <?php else: ?>
                                                        <label class="relative inline-flex items-center cursor-pointer group">
                                                            <input type="checkbox" name="permisos[<?= $rol['id'] ?>][]" value="<?= $p['id'] ?>" 
                                                                <?= in_array($p['id'], $matriz[$rol['id']]) ? 'checked' : '' ?>
                                                                class="sr-only peer">
                                                            <div class="w-10 h-5 bg-slate-200 dark:bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary shadow-inner"></div>
                                                        </label>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-between pt-6 pb-20 px-8">
                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined text-amber-500">info</span>
                            <p class="text-[10px] text-slate-400 font-medium italic">Los cambios afectarán a los usuarios la próxima vez que inicien sesión en el sistema.</p>
                        </div>
                        <div class="flex gap-6">
                            <a href="/configuracion" class="text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest flex items-center">Cancelar</a>
                            <button type="submit" class="bg-primary hover:bg-[#005f6b] text-white px-10 py-4 rounded-full font-black uppercase tracking-[0.2em] shadow-xl shadow-teal-900/10 transition-all flex items-center gap-3">
                                Guardar Matriz de Seguridad
                                <span class="material-symbols-outlined text-lg">security</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <footer class="h-10 px-10 flex items-center justify-center shrink-0 border-t border-slate-50 dark:border-slate-900">
            <p class="text-[8px] font-black text-slate-200 dark:text-slate-800 uppercase tracking-[0.5em]">sgdoc Secure-Access • Promese/Cal • 2026</p>
        </footer>
    </main>
    <script>
        function selectAllByRole(roleId) {
            const checkboxes = document.querySelectorAll(`input[name="permisos[${roleId}][]"]`);
            const allChecked = Array.from(checkboxes).every(c => c.checked);
            checkboxes.forEach(c => c.checked = !allChecked);
        }
    </script>
</body>
</html>
