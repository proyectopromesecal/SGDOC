<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Manejo de Versiones - sgdoc</title>
    
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
                    }
                }
            }
        };
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen flex">
    
    <?php include VIEWS_PATH . '/partials/sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-14 px-8 flex items-center border-b border-slate-200/50 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-md shrink-0 z-20">
            <div class="flex items-center gap-2 text-[9px] font-black uppercase tracking-[0.3em] text-slate-400">
                <span class="text-primary">Proyecto</span>
                <span class="text-slate-300">›</span>
                <span class="text-slate-400">Manejo de Versiones</span>
            </div>
            <div class="ml-auto flex items-center gap-4">
                <button onclick="toggleDarkMode()" class="text-slate-300 hover:text-primary transition-colors"><span class="material-symbols-outlined">dark_mode</span></button>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto px-12 py-10 space-y-12">
            <div class="flex justify-between items-start">
                <div class="space-y-1">
                    <h1 class="text-[32px] font-black text-slate-900 dark:text-white tracking-tight">Manejo de Versiones</h1>
                    <p class="text-[11px] text-slate-400 font-medium italic">Historial de actualizaciones, características y desarrollo del sistema.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if (!empty($notas)): ?>
                    <?php foreach ($notas as $nota): ?>
                    <div class="bg-white dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl transition-all relative overflow-hidden group">
                        <div class="absolute top-0 left-0 w-2 h-full" style="background-color: <?= htmlspecialchars($nota['color_tag'] ?? '#007281') ?>"></div>
                        <div class="flex justify-between items-start mb-6">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest"><?= date('d M, Y', strtotime($nota['fecha_creacion'])) ?></span>
                            <div class="size-2 rounded-full" style="background-color: <?= htmlspecialchars($nota['color_tag'] ?? '#007281') ?>"></div>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white mb-3 leading-tight"><?= htmlspecialchars($nota['titulo']) ?></h3>
                        <div class="text-[12px] text-slate-500 dark:text-slate-400 leading-relaxed mb-8 prose dark:prose-invert max-w-none">
                            <?= nl2br(htmlspecialchars($nota['contenido'])) ?>
                        </div>
                        <div class="flex items-center gap-3 mt-auto pt-6 border-t border-slate-50 dark:border-slate-800">
                            <div class="size-8 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-[10px] font-black" style="color: <?= htmlspecialchars($nota['color_tag'] ?? '#007281') ?>">
                                <?= strtoupper(substr($nota['autor_nombre'], 0, 1)) ?>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Por: <?= htmlspecialchars($nota['autor_nombre']) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-20">
                        <span class="material-symbols-outlined text-6xl text-slate-200">note_stack</span>
                        <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mt-4">No hay notas registradas</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if (isset($totalPaginas) && $totalPaginas > 1): ?>
            <div class="flex justify-between items-center pt-6 pb-6 border-t border-slate-50 dark:border-slate-900 px-4 mt-8">
                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">Mostrando <?= count($notas) ?> de <?= $totalRegistros ?> notas</p>
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
            <?php endif; ?>
        </div>
    </main>


</body>
</html>
