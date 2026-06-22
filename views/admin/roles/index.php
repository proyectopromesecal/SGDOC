<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Gestión de Roles - sgdoc</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
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
        .modal-overlay { animation: fadeIn 0.15s ease; }
        .modal-box    { animation: slideUp 0.2s ease; }
        @keyframes fadeIn  { from { opacity: 0; }          to { opacity: 1; } }
        @keyframes slideUp { from { transform: translateY(16px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
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
                <span class="text-primary">Gestión de Roles</span>
            </div>
            <div class="ml-auto flex items-center gap-6">
                <button onclick="toggleDarkMode()" class="text-slate-300 hover:text-primary transition-colors h-8 w-8 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">dark_mode</span>
                </button>
            </div>
        </header>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto px-12 py-10 space-y-10">
            <div class="max-w-4xl mx-auto space-y-8">

                <!-- Page Title -->
                <div class="flex justify-between items-end">
                    <div class="space-y-1">
                        <h1 class="text-[32px] font-black text-slate-custom dark:text-white tracking-tight uppercase">Gestión de Roles</h1>
                        <p class="text-[11px] text-slate-400 font-medium italic">Administra los roles del sistema. Los cambios se reflejan dinámicamente en la Matriz de Permisos.</p>
                    </div>
                    <button onclick="abrirModal('modal-crear')"
                            class="flex items-center gap-3 bg-primary hover:bg-[#005f6b] text-white px-6 py-3 rounded-full font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 transition-all">
                        <span class="material-symbols-outlined text-base">add_circle</span>
                        Nuevo Rol
                    </button>
                </div>

                <!-- Mensajes -->
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

                <!-- Tabla de Roles -->
                <div class="bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm overflow-hidden">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-100 dark:border-slate-800">
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest w-12">#</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nombre del Rol</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Estado</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                            <?php if (empty($roles)): ?>
                                <tr>
                                    <td colspan="4" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <span class="material-symbols-outlined text-4xl text-slate-200">manage_accounts</span>
                                            <p class="text-[11px] font-bold text-slate-300 uppercase tracking-widest">No hay roles registrados</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($roles as $i => $rol): ?>
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition-colors group">
                                        <td class="px-8 py-5">
                                            <span class="text-[10px] font-black text-slate-300"><?= $i + 1 ?></span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="h-9 w-9 rounded-xl flex items-center justify-center shadow-sm
                                                    <?= strtolower($rol['nombre']) === 'administrador' ? 'bg-secondary/10' : 'bg-primary/10' ?>">
                                                    <span class="material-symbols-outlined text-base
                                                        <?= strtolower($rol['nombre']) === 'administrador' ? 'text-secondary' : 'text-primary' ?>">
                                                        <?= strtolower($rol['nombre']) === 'administrador' ? 'admin_panel_settings' : 'badge' ?>
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-black text-slate-700 dark:text-white uppercase tracking-tight"><?= htmlspecialchars($rol['nombre']) ?></p>
                                                    <?php if (strtolower($rol['nombre']) === 'administrador'): ?>
                                                        <p class="text-[9px] font-bold text-secondary uppercase tracking-widest">Rol protegido del sistema</p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <?php if (strtolower($rol['nombre']) === 'administrador'): ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-secondary rounded-full text-[9px] font-black uppercase tracking-widest">
                                                    <span class="material-symbols-outlined text-[10px]">lock</span>
                                                    Inmutable
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[9px] font-black uppercase tracking-widest">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                                                    Activo
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-8 py-5">
                                            <div class="flex items-center justify-end gap-2">
                                                <?php if (strtolower($rol['nombre']) !== 'administrador'): ?>
                                                    <button onclick="abrirEditar(<?= $rol['id'] ?>, '<?= htmlspecialchars(addslashes($rol['nombre'])) ?>')"
                                                            title="Editar nombre"
                                                            class="h-8 w-8 flex items-center justify-center rounded-xl text-slate-400 hover:bg-primary/10 hover:text-primary transition-all">
                                                        <span class="material-symbols-outlined text-base">edit</span>
                                                    </button>
                                                    <button onclick="abrirEliminar(<?= $rol['id'] ?>, '<?= htmlspecialchars(addslashes($rol['nombre'])) ?>')"
                                                            title="Eliminar rol"
                                                            class="h-8 w-8 flex items-center justify-center rounded-xl text-slate-400 hover:bg-red-50 hover:text-secondary transition-all">
                                                        <span class="material-symbols-outlined text-base">delete</span>
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-[9px] text-slate-200 font-black uppercase tracking-widest">—</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Info Footer -->
                <div class="flex items-center gap-3 px-2 pb-12">
                    <span class="material-symbols-outlined text-amber-400 text-lg">info</span>
                    <p class="text-[10px] text-slate-400 font-medium italic">
                        Los nuevos roles aparecen automáticamente en la <a href="/configuracion/permisos" class="text-primary hover:underline font-bold">Matriz de Permisos</a> para que puedas asignarles accesos.
                    </p>
                </div>

            </div>
        </div>
    </main>

    <!-- ===== MODAL: CREAR ROL ===== -->
    <div id="modal-crear" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-overlay absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="cerrarModal('modal-crear')"></div>
        <div class="modal-box relative bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl w-full max-w-md p-8 space-y-6 border border-slate-100 dark:border-slate-800">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-[20px] font-black text-slate-800 dark:text-white uppercase tracking-tight">Nuevo Rol</h2>
                    <p class="text-[10px] text-slate-400 font-medium mt-1">Define un nombre único para el nuevo perfil de acceso.</p>
                </div>
                <button onclick="cerrarModal('modal-crear')" class="text-slate-300 hover:text-slate-500 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="/configuracion/roles/crear" method="POST" class="space-y-5">
                <?= \App\Core\Security::csrfInput() ?>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Nombre del Rol</label>
                    <input type="text" name="nombre" id="nuevo-nombre" required placeholder="ej. Auditor Interno"
                           class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-bold text-slate-800 dark:text-white placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                           autocomplete="off">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="cerrarModal('modal-crear')"
                            class="flex-1 px-6 py-3 rounded-full border border-slate-200 dark:border-slate-700 text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-all">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 bg-primary hover:bg-[#005f6b] text-white px-6 py-3 rounded-full font-black text-[10px] uppercase tracking-widest shadow-lg shadow-teal-900/20 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-base">add_circle</span>
                        Crear Rol
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== MODAL: EDITAR ROL ===== -->
    <div id="modal-editar" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-overlay absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="cerrarModal('modal-editar')"></div>
        <div class="modal-box relative bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl w-full max-w-md p-8 space-y-6 border border-slate-100 dark:border-slate-800">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-[20px] font-black text-slate-800 dark:text-white uppercase tracking-tight">Editar Rol</h2>
                    <p class="text-[10px] text-slate-400 font-medium mt-1">Cambia el nombre del rol. El cambio aplica inmediatamente.</p>
                </div>
                <button onclick="cerrarModal('modal-editar')" class="text-slate-300 hover:text-slate-500 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="/configuracion/roles/editar" method="POST" class="space-y-5">
                <?= \App\Core\Security::csrfInput() ?>
                <input type="hidden" name="id" id="editar-id">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Nuevo Nombre</label>
                    <input type="text" name="nombre" id="editar-nombre" required placeholder="Nombre del rol"
                           class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-bold text-slate-800 dark:text-white placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                           autocomplete="off">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="cerrarModal('modal-editar')"
                            class="flex-1 px-6 py-3 rounded-full border border-slate-200 dark:border-slate-700 text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-all">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 bg-primary hover:bg-[#005f6b] text-white px-6 py-3 rounded-full font-black text-[10px] uppercase tracking-widest shadow-lg shadow-teal-900/20 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-base">save</span>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== MODAL: CONFIRMAR ELIMINAR ===== -->
    <div id="modal-eliminar" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-overlay absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="cerrarModal('modal-eliminar')"></div>
        <div class="modal-box relative bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl w-full max-w-sm p-8 space-y-6 border border-red-100 dark:border-red-900/30">
            <div class="flex flex-col items-center text-center gap-4">
                <div class="h-14 w-14 rounded-2xl bg-red-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl text-secondary">warning</span>
                </div>
                <div>
                    <h2 class="text-[18px] font-black text-slate-800 dark:text-white uppercase tracking-tight">¿Eliminar Rol?</h2>
                    <p class="text-[11px] text-slate-400 font-medium mt-2">
                        Estás a punto de eliminar el rol <strong id="eliminar-nombre" class="text-slate-700 dark:text-white"></strong>.<br>
                        Esta acción no se puede deshacer.
                    </p>
                </div>
            </div>
            <form action="/configuracion/roles/eliminar" method="POST" class="space-y-3">
                <?= \App\Core\Security::csrfInput() ?>
                <input type="hidden" name="id" id="eliminar-id">
                <button type="submit"
                        class="w-full bg-secondary hover:bg-red-700 text-white px-6 py-3 rounded-full font-black text-[10px] uppercase tracking-widest shadow-lg shadow-red-900/20 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-base">delete_forever</span>
                    Sí, eliminar rol
                </button>
                <button type="button" onclick="cerrarModal('modal-eliminar')"
                        class="w-full px-6 py-3 rounded-full border border-slate-200 dark:border-slate-700 text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-all">
                    Cancelar
                </button>
            </form>
        </div>
    </div>

    <script>
        function abrirModal(id) {
            document.getElementById(id).classList.remove('hidden');
            // Focus al input si existe
            const input = document.querySelector(`#${id} input[type="text"]`);
            if (input) setTimeout(() => input.focus(), 100);
        }
        function cerrarModal(id) {
            document.getElementById(id).classList.add('hidden');
        }
        function abrirEditar(id, nombre) {
            document.getElementById('editar-id').value   = id;
            document.getElementById('editar-nombre').value = nombre;
            abrirModal('modal-editar');
        }
        function abrirEliminar(id, nombre) {
            document.getElementById('eliminar-id').value     = id;
            document.getElementById('eliminar-nombre').textContent = nombre;
            abrirModal('modal-eliminar');
        }
        // Cerrar modales con Escape
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                ['modal-crear','modal-editar','modal-eliminar'].forEach(cerrarModal);
            }
        });
    </script>
</body>
</html>
