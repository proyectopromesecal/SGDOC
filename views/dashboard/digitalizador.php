<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Dashboard Digitalizador - sgdoc</title>
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
                <span class="text-amber-600">Dashboard Digitalizador</span>
                <span class="text-slate-300">›</span>
                <span class="text-slate-400">Resumen de Carga</span>
            </div>
            
            <div class="ml-auto flex items-center gap-6">
                <button onclick="toggleDarkMode()" class="text-slate-300 hover:text-primary transition-colors"><span class="material-symbols-outlined">dark_mode</span></button>
            </div>
        </header>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto px-12 py-10 space-y-12">
            
            <!-- Page Title -->
            <div class="flex justify-between items-start">
                <div class="space-y-1">
                    <h1 class="text-[32px] font-black text-slate-custom dark:text-white tracking-tight uppercase">Centro de Archivo</h1>
                    <p class="text-[11px] text-slate-400 font-medium italic">Monitor de digitalización institucional y registro de carga histórica.</p>
                </div>
                <a href="/documentos/digitalizados" class="bg-amber-500 hover:bg-amber-600 text-white px-8 py-3 rounded-full font-black text-[10px] uppercase tracking-widest shadow-xl shadow-amber-900/20 transition-all flex items-center gap-3 group">
                    <span class="material-symbols-outlined text-sm font-variation-settings: 'FILL' 1">scanner</span>
                    Nueva Digitalización
                    <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
            </div>

            <!-- Stats Block -->
            <div class="grid grid-cols-3 gap-8">
                <div class="p-8 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-24 h-full bg-amber-500/5 skew-x-[-20deg] translate-x-12"></div>
                    <div class="flex items-center gap-4 mb-2">
                        <div class="size-10 bg-amber-500/10 text-amber-500 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined font-variation-settings: 'FILL' 1">inventory_2</span>
                        </div>
                        <span class="text-[9px] font-black text-amber-600 uppercase tracking-widest">Digitalizados Hoy</span>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Total de Carga Actual</p>
                    <h3 class="text-[48px] font-black tracking-tighter leading-none text-slate-custom dark:text-white mt-1"><?= $stats['DIGITALIZADO'] ?></h3>
                </div>

                <div class="p-8 bg-slate-custom dark:bg-slate-950 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-primary/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex items-center gap-4 mb-2">
                        <div class="size-10 bg-white/10 text-white rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined">analytics</span>
                        </div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Nivel de Precisión</span>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Estado de Integridad</p>
                    <h3 class="text-[48px] font-black tracking-tighter leading-none text-white mt-1">100<span class="text-lg text-primary">%</span></h3>
                </div>

                <div class="p-8 border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-[2.5rem] flex flex-col items-center justify-center text-center space-y-2 opacity-50">
                    <span class="material-symbols-outlined text-4xl text-slate-200">browse_gallery</span>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Estadísticas de Meta-datos</p>
                    <p class="text-[8px] font-medium text-slate-300 uppercase">Resumen automatizado en proceso...</p>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <div class="size-2 bg-amber-500 rounded-full"></div>
                    <h2 class="text-xs font-black text-slate-700 dark:text-white uppercase tracking-tight">Carga Recientemente Procesada</h2>
                </div>
                
                <div class="bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] overflow-hidden shadow-sm">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-100 dark:border-slate-800">
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5">EXPEDIENTE</th>
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5">TIPO DE ARCHIVO</th>
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5">FECHA DE REGISTRO</th>
                                <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-8 py-5 text-right">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-900">
                            <?php if (empty($documentosRecientes)): ?>
                                <tr>
                                    <td colspan="4" class="py-20 text-center text-slate-300">
                                        <span class="material-symbols-outlined text-4xl block mb-2">cloud_off</span>
                                        <p class="text-[9px] font-black uppercase tracking-widest">No hay digitalizaciones recientes en este turno</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($documentosRecientes as $doc): ?>
                                <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-900/5 transition-colors group">
                                    <td class="px-8 py-6">
                                        <span class="text-[10px] font-black text-amber-600 bg-amber-50 dark:bg-amber-900/20 px-2.5 py-1 rounded-md border border-amber-100/50">
                                            <?= htmlspecialchars($doc['id']) ?>
                                        </span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <p class="text-[11px] font-black text-slate-700 dark:text-white uppercase"><?= htmlspecialchars($doc['tipo']) ?></p>
                                        <p class="text-[9px] text-slate-400 font-medium line-clamp-1 italic"><?= htmlspecialchars($doc['descripcion']) ?></p>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm text-slate-300">calendar_today</span>
                                            <span class="text-[10px] font-bold text-slate-500"><?= date('d M, Y • H:i', strtotime($doc['fecha_creacion'])) ?></span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a href="/documentos/ver/<?= htmlspecialchars($doc['id']) ?>" class="size-8 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-800 rounded-xl flex items-center justify-center text-slate-400 hover:text-amber-500 transition-colors shadow-sm">
                                                <span class="material-symbols-outlined text-lg">visibility</span>
                                            </a>
                                            <a href="/documentos/descargar/<?= htmlspecialchars($doc['id']) ?>/original" class="size-8 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-800 rounded-xl flex items-center justify-center text-slate-400 hover:text-amber-500 transition-colors shadow-sm">
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
        </div>

        <footer class="h-10 px-10 flex items-center justify-center shrink-0">
            <p class="text-[8px] font-black text-slate-200 dark:text-slate-800 uppercase tracking-[0.5em]">sgdoc Digital-Inbound • Promese/Cal • 2026</p>
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
                        Plataforma oficial para la carga histórica y gestión de documentos digitalizados.
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
                <p class="text-[8px] font-black text-slate-300 uppercase underline tracking-widest cursor-pointer hover:text-primary">Seguridad de la Información</p>
            </div>
        </div>
    </div>
</body>
</html>
