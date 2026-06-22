<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Configuración Maestra - sgdoc</title>
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
                <span class="text-primary">Configuración Global</span>
            </div>
            <div class="ml-auto flex items-center gap-6">
                <button onclick="toggleDarkMode()" class="text-slate-300 hover:text-primary transition-colors h-8 w-8 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">dark_mode</span>
                </button>
            </div>
        </header>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto px-12 py-10">
            
            <div class="max-w-5xl space-y-10">
                <!-- Page Title -->
                <div class="space-y-1">
                    <h1 class="text-[32px] font-black text-slate-custom dark:text-white tracking-tight uppercase tracking-tighter">Parámetros Maestros</h1>
                    <p class="text-[11px] text-slate-400 font-medium italic">Gestione las rutas raíz del servidor y los protocolos de integridad documental.</p>
                </div>

                <form action="/configuracion/guardar" method="POST" class="space-y-8">
                    <?= \App\Core\Security::csrfInput() ?>
                    
                    <!-- Group 1: Paths -->
                    <div class="bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-10 shadow-sm space-y-8">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="size-8 bg-teal-50 dark:bg-teal-900/20 text-primary rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-lg">folder_shared</span>
                            </div>
                            <h3 class="text-[10px] font-black text-slate-700 dark:text-white uppercase tracking-widest">Infraestructura de Almacenamiento</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Documentos Originales</label>
                                <input type="text" name="path_originales" value="<?= htmlspecialchars($config['path_originales'] ?? '') ?>" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none" placeholder="/storage/originals">
                                <p class="text-[8px] text-slate-300 font-bold uppercase tracking-tighter ml-1">Directorio raíz para archivos de entrada</p>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Escaneos Digitalizados</label>
                                <input type="text" name="path_escaneos" value="<?= htmlspecialchars($config['path_escaneos'] ?? '') ?>" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none" placeholder="/storage/digitized">
                                <p class="text-[8px] text-slate-300 font-bold uppercase tracking-tighter ml-1">Ruta dedicada para carga histórica</p>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Repositorio Firmado</label>
                                <input type="text" name="path_firmados" value="<?= htmlspecialchars($config['path_firmados'] ?? '') ?>" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none" placeholder="/storage/signed">
                                <p class="text-[8px] text-slate-300 font-bold uppercase tracking-tighter ml-1">Archivos con sellos criptográficos</p>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Archivos de Soporte</label>
                                <input type="text" name="path_soporte" value="<?= htmlspecialchars($config['path_soporte'] ?? '') ?>" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none" placeholder="/storage/support">
                                <p class="text-[8px] text-slate-300 font-bold uppercase tracking-tighter ml-1">Adjuntos complementarios al registro</p>
                            </div>
                        </div>
                    </div>

                    <!-- Group 2: Constraints -->
                    <div class="bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-10 shadow-sm space-y-8">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="size-8 bg-amber-50 dark:bg-amber-900/20 text-amber-500 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-lg">security</span>
                            </div>
                            <h3 class="text-[10px] font-black text-slate-700 dark:text-white uppercase tracking-widest">Protocolos de Seguridad y Carga</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Límite de Carga Unitaria</label>
                                <div class="relative group">
                                    <input type="number" name="max_file_size" value="<?= htmlspecialchars($config['max_file_size'] ?? '25') ?>" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 rounded-2xl text-xl font-black focus:ring-1 focus:ring-primary/20 transition-all outline-none pr-16 text-primary">
                                    <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-300 dark:text-slate-600">MB</span>
                                </div>
                                <p class="text-[8px] text-slate-300 font-bold uppercase tracking-tighter ml-1">Tamaño máximo por archivo individual</p>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Formatos Permitidos</label>
                                <input type="text" name="allowed_extensions" value="<?= htmlspecialchars($config['allowed_extensions'] ?? 'pdf,docx,jpg,png') ?>" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none text-slate-500 italic" placeholder="pdf, docx, jpg">
                                <p class="text-[8px] text-slate-300 font-bold uppercase tracking-tighter ml-1">Extensiones autorizadas (separadas por coma)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-4 px-4">
                        <div class="flex items-center gap-6">
                            <button type="submit" class="bg-primary hover:bg-[#005f6b] text-white px-10 py-4 rounded-full font-black uppercase text-[10px] tracking-[0.2em] shadow-xl shadow-teal-900/10 transition-all flex items-center gap-3 group">
                                Actualizar Parámetros
                                <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">bolt</span>
                            </button>
                            <a href="/dashboard" class="text-[10px] font-black text-slate-300 hover:text-secondary uppercase tracking-[0.2em] transition-colors">Cancelar</a>
                        </div>
                        <p class="text-[9px] font-black text-slate-200 dark:text-slate-800 uppercase tracking-widest hidden md:block">Nexus Config v2.4</p>
                    </div>
                </form>
            </div>
            </div>
        </div>

        <footer class="h-10 px-10 flex items-center justify-center shrink-0">
            <p class="text-[8px] font-black text-slate-200 dark:text-slate-800 uppercase tracking-[0.5em]">sgdoc Admin-Nexus • 2026</p>
        </footer>
    </main>
</body>
</html>
