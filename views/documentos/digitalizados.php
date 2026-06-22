<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Registro Digitalizados - sgdoc</title>
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
                <span class="text-primary">Digitalizados</span>
                <span class="text-slate-300">›</span>
                <span class="text-primary">Nuevo Registro</span>
            </div>
            
            <div class="ml-auto flex items-center gap-6">
                <button onclick="toggleDarkMode()" class="text-slate-400 hover:text-primary transition-colors h-8 w-8 flex items-center justify-center rounded-lg">
                    <span class="material-symbols-outlined text-xl">dark_mode</span>
                </button>
            </div>
        </header>

        <!-- Scrollable Body -->
        <div class="flex-1 overflow-y-auto p-10">
            
            <div class="max-w-4xl mx-auto space-y-10">
                <!-- Page Title -->
                <div class="space-y-1">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="size-10 bg-amber-500/10 text-amber-500 rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined font-variation-settings: 'FILL' 1">scanner</span>
                        </div>
                        <h1 class="text-[32px] font-black text-slate-custom dark:text-white tracking-tight uppercase">Archivo Digitalizado</h1>
                    </div>
                    <p class="text-slate-400 text-[11px] font-medium italic">Registro de documentos físicos previamente digitalizados para archivo histórico.</p>
                </div>

                <!-- Messages -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 text-secondary rounded-2xl flex items-center gap-3 text-xs font-bold border border-red-100/50">
                        <span class="material-symbols-outlined text-lg">error</span>
                        <?= htmlspecialchars($_SESSION['error']) ?><?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <!-- Form Card -->
                <form action="/documentos/guardar_digitalizado" method="POST" enctype="multipart/form-data" class="space-y-8">
                    <?= \App\Core\Security::csrfInput() ?>
                    <div class="bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-10 shadow-sm space-y-10">
                        
                        <!-- Metadata Section -->
                        <div class="space-y-8">
                            <div class="flex items-center gap-4">
                                <span class="text-[10px] font-black text-amber-500 bg-amber-500/5 px-3 py-1 rounded-full uppercase tracking-tighter">Información del Documento</span>
                                <div class="h-px bg-slate-50 dark:bg-slate-800 flex-1"></div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-1.5">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1.5 block">Nro. de Documento Físico *</label>
                                    <div class="relative group">
                                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-amber-500 transition-colors text-xl">tag</span>
                                        <input type="text" name="id" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-amber-500/20 transition-all outline-none pl-12" placeholder="ej. DOC-F-2025-001" required>
                                    </div>
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1.5 block">Categoría de Archivo *</label>
                                    <div class="relative group">
                                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-amber-500 transition-colors text-xl">folder</span>
                                        <select name="tipo" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-amber-500/20 transition-all outline-none pl-12 appearance-none">
                                            <option value="Archivo Histórico">Archivo Histórico</option>
                                            <option value="Correspondencia Externa">Correspondencia Externa</option>
                                            <option value="Documentación Legal">Documentación Legal</option>
                                            <option value="Facturas Digitalizadas">Facturas Digitalizadas</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1.5 block">Descripción o Notas de Digitalización *</label>
                                <div class="relative group">
                                    <span class="material-symbols-outlined absolute left-4 top-5 text-slate-300 group-focus-within:text-amber-500 transition-colors text-xl">notes</span>
                                    <textarea name="descripcion" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-amber-500/20 transition-all outline-none pl-12 min-h-[100px]" placeholder="Breve nota sobre el documento digitalizado..." required></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Scan Upload Section -->
                        <div class="space-y-8">
                            <div class="flex items-center gap-4">
                                <span class="text-[10px] font-black text-amber-500 bg-amber-500/5 px-3 py-1 rounded-full uppercase tracking-tighter">Archivo Escaneado</span>
                                <div class="h-px bg-slate-50 dark:bg-slate-800 flex-1"></div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1.5 block">Carga de Documento Digitalizado *</label>
                                <label class="border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-[2rem] p-12 flex flex-col items-center justify-center bg-slate-50/50 dark:bg-slate-950/20 hover:bg-white dark:hover:bg-slate-800 transition-all cursor-pointer relative group overflow-hidden">
                                    <div class="size-16 bg-white dark:bg-slate-900 rounded-2xl shadow-xl shadow-amber-900/5 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined text-3xl text-amber-500 font-variation-settings: 'FILL' 1">file_upload</span>
                                    </div>
                                    <div class="text-center space-y-2 relative z-10">
                                        <p class="text-[11px] font-black text-slate-700 dark:text-white uppercase tracking-tight">Seleccione el escaneo</p>
                                        <p id="scan-file-name" class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Solo PDF</p>
                                    </div>
                                    <input type="file" name="archivo" accept="application/pdf" class="absolute inset-0 opacity-0 cursor-pointer z-50" required onchange="document.getElementById('scan-file-name').textContent = this.files[0].name; document.getElementById('scan-file-name').className='text-[9px] text-amber-600 font-bold uppercase tracking-widest'">
                                </label>
                            </div>

                            <div class="p-6 bg-amber-500 text-white shadow-2xl rounded-3xl flex items-center gap-6 relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-32 h-full bg-white/10 skew-x-[-20deg] translate-x-16"></div>
                                <div class="size-12 bg-white/20 rounded-2xl flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined font-variation-settings: 'FILL' 1">history_edu</span>
                                </div>
                                <div class="space-y-0.5">
                                    <h4 class="text-[11px] font-black uppercase tracking-widest">Registro de Tipo Archivo Digital</h4>
                                    <p class="text-[10px] font-medium opacity-80">Estos documentos se registran con estado "DIGITALIZADO" y no requieren flujo de firmas.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-6 pt-6 pb-20">
                        <a href="/documentos" class="text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-[0.2em] transition-colors">Cancelar</a>
                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-10 py-4 rounded-full font-black uppercase tracking-[0.2em] shadow-xl shadow-amber-900/20 transition-all flex items-center gap-3 group">
                            Archivar Documento
                            <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">inventory_2</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <footer class="h-10 px-10 bg-slate-50 dark:bg-slate-900 border-t border-slate-200/50 dark:border-slate-800 flex items-center justify-center shrink-0">
            <p class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.4em]">sgdoc Digital-Inbound • Promese/Cal • <?= date('Y') ?></p>
        </footer>
    </main>
</body>
</html>
