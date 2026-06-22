<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>sgdoc - Acceso Institucional</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
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
                        sans: ["Inter", "sans-serif"],
                    },
                },
            },
        };
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .dark .glass-card {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 min-h-screen flex items-center justify-center p-6 relative overflow-hidden transition-colors duration-500">
    
    <!-- Decorative Elements -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/5 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-secondary/5 rounded-full blur-[120px]"></div>

    <div class="max-w-md w-full relative z-10">
        <!-- Logo & Branding -->
        <div class="text-center mb-10 space-y-4">
            <div class="inline-block p-4 bg-white dark:bg-slate-800 rounded-3xl shadow-xl shadow-teal-900/5 mb-2">
                <img src="/images/logo.png" alt="PROMESE/CAL" class="h-12 w-auto object-contain">
            </div>
            <div>
                <h1 class="text-4xl font-black text-primary tracking-tighter uppercase">sgdoc</h1>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] mt-1">Sistema Integrado de Gestión Documental</p>
            </div>
        </div>

        <!-- Login Card -->
        <div class="glass-card rounded-[2.5rem] shadow-2xl shadow-teal-900/10 overflow-hidden border border-white/40 dark:border-slate-800">
            <div class="p-10 space-y-8">
                
                <div class="space-y-1">
                    <h2 class="text-xl font-black text-primary uppercase tracking-widest">Inicio de Sesión</h2>
                    <p class="text-xs text-slate-400 font-medium italic">Acceda con sus credenciales institucionales seguras.</p>
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

                <form action="/login" method="POST" class="space-y-5">
                    <?= \App\Core\Security::csrfInput() ?>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Usuario</label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-primary transition-colors text-xl">person</span>
                            <input type="text" name="usuario" class="w-full px-4 py-3.5 bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl text-sm font-semibold focus:ring-1 focus:ring-primary/20 transition-all outline-none pl-12" placeholder="ej. juan.perez" required autofocus>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Contraseña</label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-primary transition-colors text-xl">lock</span>
                            <input type="password" name="password" class="w-full px-4 py-3.5 bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl text-sm font-semibold focus:ring-1 focus:ring-primary/20 transition-all outline-none pl-12" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <div class="relative flex items-center">
                                <input type="checkbox" class="peer h-4 w-4 rounded border-slate-200 text-primary focus:ring-primary/20 transition-all">
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 group-hover:text-slate-600 transition-colors uppercase tracking-tight">Recordar sesión</span>
                        </label>
                        <a href="mailto:mesadeayuda@promesecal.gob.do?subject=Soporte%20Plataforma%20sgdoc" class="text-[10px] font-black text-primary hover:text-primary/80 uppercase tracking-tight">¿Soporte Técnico?</a>
                    </div>

                    <button type="submit" class="w-full bg-primary hover:bg-[#005f6b] text-white py-4 rounded-2xl font-black uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 transition-all flex items-center justify-center gap-2 group">
                        Ingresar al Portal
                        <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </button>
                </form>

                <div class="mt-8 pt-8 border-t border-slate-100 dark:border-slate-800 text-center space-y-4">
                    <p class="text-[10px] text-slate-400 font-medium leading-relaxed px-4">
                        Plataforma centralizada para la gestión, firma y aprobación de documentos institucionales de PROMESE/CAL.
                    </p>
                    <div class="flex flex-col gap-1">
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Versión 1.0.0</p>
                        <p class="text-[9px] font-bold text-slate-300 uppercase tracking-widest">© 2026 Todos los derechos reservados</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Theme Switcher Floating -->
    <button onclick="document.documentElement.classList.toggle('dark')" class="fixed bottom-8 right-8 p-3 bg-white dark:bg-slate-800 rounded-full shadow-2xl border border-slate-100 dark:border-slate-800 text-slate-400 hover:text-primary transition-all">
        <span class="material-symbols-outlined">dark_mode</span>
    </button>

</body>
</html>
