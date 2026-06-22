<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Nueva Solicitud - sgdoc</title>
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
    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
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
        
        /* Tom Select Custom Styling to match standard inputs */
        .ts-wrapper .ts-control {
            background-color: transparent !important;
            border: none !important;
            padding: 1rem 1.25rem !important; /* py-4 px-5 */
            padding-left: 3rem !important; /* pl-12 for icon */
            border-radius: 1rem !important; /* rounded-2xl */
            font-size: 11px !important;
            font-weight: 700 !important;
            color: inherit !important;
            box-shadow: none !important;
        }

        .ts-wrapper.focus .ts-control {
            box-shadow: 0 0 0 1px rgba(0, 114, 129, 0.2) !important; /* focus:ring-1 focus:ring-primary/20 */
        }

        .ts-wrapper {
            background-color: #f8fafc;
            border-radius: 1rem;
            border: none;
            padding: 0;
        }

        .dark .ts-wrapper {
            background-color: #1e293b;
        }

        .ts-wrapper.single .ts-control:after {
            right: 1.5rem !important;
        }
        
        .ts-wrapper .ts-dropdown {
            border: 1px solid #f1f5f9;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            overflow: hidden;
            margin-top: 0.5rem;
            z-index: 50 !important; /* Ensure dropdown is above z-10 icons */
        }
        
        .dark .ts-wrapper .ts-dropdown {
            background-color: #1e293b;
            border-color: #334155;
        }
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
                <a href="/documentos" class="hover:text-primary transition-colors">Documentos</a>
                <span class="text-slate-300">›</span>
                <span class="text-primary">Nueva Solicitud</span>
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
                        <div class="size-10 bg-primary/10 text-primary rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined font-variation-settings: 'FILL' 1">add_circle</span>
                        </div>
                        <h1 class="text-[32px] font-black text-slate-custom dark:text-white tracking-tight uppercase">Registro de Documento</h1>
                    </div>
                    <p class="text-slate-400 text-[11px] font-medium italic">Complete los campos requeridos para iniciar el flujo de aprobación y firma digital.</p>
                </div>

                <!-- Messages -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 text-secondary rounded-2xl flex items-center gap-3 text-xs font-bold border border-red-100/50">
                        <span class="material-symbols-outlined text-lg">error</span>
                        <?= htmlspecialchars($_SESSION['error']) ?><?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <!-- Form Card -->
                <form action="/documentos/guardar" method="POST" enctype="multipart/form-data" class="space-y-8">
                    <?= \App\Core\Security::csrfInput() ?>
                    <div class="bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-10 shadow-sm space-y-10">
                        
                        <!-- Essential Info Section -->
                        <div class="space-y-8">
                            <div class="flex items-center gap-4">
                                <span class="text-[10px] font-black text-primary bg-primary/5 px-3 py-1 rounded-full uppercase tracking-tighter">Fase 1: Identificación</span>
                                <div class="h-px bg-slate-50 dark:bg-slate-800 flex-1"></div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-1.5">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1.5 block">ID Identificador Institucional *</label>
                                    <div class="relative group">
                                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-primary transition-colors text-xl">tag</span>
                                        <input type="text" name="id" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none pl-12" placeholder="ej. SOL-2026-0001" required>
                                    </div>
                                    <p class="text-[8px] text-slate-300 font-bold uppercase tracking-tight ml-1">Código único de seguimiento asignado por su departamento.</p>
                                </div>

                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between mb-1.5 ml-1">
                                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block">Tipo de Solicitud *</label>
                                        <button type="button" onclick="openModal('modalTipoSolicitud')" class="text-[9px] font-black text-primary uppercase tracking-widest flex items-center gap-1 hover:text-[#005f6b] transition-colors"><span class="material-symbols-outlined text-[14px]">add_circle</span> CREAR</button>
                                    </div>
                                    <div class="relative group">
                                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 z-10 text-xl pointer-events-none">category</span>
                                        <select id="tipo_solicitud_id" name="tipo_solicitud_id" class="w-full pl-12" required>
                                            <option value="" disabled selected>SELECCIONE LA TIPOLOGÍA...</option>
                                            <?php foreach ($tiposSolicitud as $t): ?>
                                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-1.5">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1.5 block">Prioridad de Atención *</label>
                                    <div class="relative group">
                                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 z-10 text-xl pointer-events-none">priority_high</span>
                                        <select id="prioridad" name="prioridad" class="w-full pl-12">
                                            <option value="Baja">Prioridad Baja</option>
                                            <option value="Normal" selected>Prioridad Normal</option>
                                            <option value="Alta">Prioridad Alta</option>
                                            <option value="Crítica">Atención Crítica</option>
                                        </select>
                                    </div>
                                    <p class="text-[8px] text-slate-300 font-bold uppercase tracking-tight ml-1 mt-1">Establece el nivel de urgencia para el procesamiento.</p>
                                </div>

                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between mb-1.5 ml-1">
                                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block">Departamento Destino *</label>
                                        <button type="button" onclick="openModal('modalDepartamento')" class="text-[9px] font-black text-primary uppercase tracking-widest flex items-center gap-1 hover:text-[#005f6b] transition-colors"><span class="material-symbols-outlined text-[14px]">add_circle</span> CREAR</button>
                                    </div>
                                    <div class="relative group">
                                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 z-10 text-xl pointer-events-none">corporate_fare</span>
                                        <select id="departamento_destino_id" name="departamento_destino_id" class="w-full pl-12" required>
                                            <option value="" disabled selected>SELECCIONE EL DEPARTAMENTO...</option>
                                            <?php foreach ($departamentos as $d): ?>
                                                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <p class="text-[8px] text-slate-300 font-bold uppercase tracking-tight ml-1 mt-1">La solicitud será dirigida y aprobada por este departamento.</p>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1.5 block">Breve Descripción o Referencia *</label>
                                <div class="relative group">
                                    <span class="material-symbols-outlined absolute left-4 top-5 text-slate-300 group-focus-within:text-primary transition-colors text-xl">subject</span>
                                    <textarea name="descripcion" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none pl-12 min-h-[120px]" placeholder="Ej: Adquisición de materiales de oficina para el departamento de finanzas..." required></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Section -->
                        <div class="space-y-8">
                            <div class="flex items-center gap-4">
                                <span class="text-[10px] font-black text-primary bg-primary/5 px-3 py-1 rounded-full uppercase tracking-tighter">Fase 2: Adjuntos & Firma</span>
                                <div class="h-px bg-slate-50 dark:bg-slate-800 flex-1"></div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1.5 block">Documento Maestro (Original) *</label>
                                <label class="border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-[2rem] p-8 flex flex-col items-center justify-center bg-slate-50/50 dark:bg-slate-950/20 hover:bg-white dark:hover:bg-slate-800 transition-all cursor-pointer relative group overflow-hidden">
                                    <div class="size-12 bg-white dark:bg-slate-900 rounded-xl shadow-xl shadow-teal-900/5 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined text-2xl text-primary font-variation-settings: 'FILL' 1">description</span>
                                    </div>
                                    <div class="text-center space-y-1 relative z-10">
                                    <div class="text-center space-y-1 relative z-10">
                                        <p class="text-[10px] font-black text-slate-700 dark:text-white uppercase tracking-tight">Archivo Maestro</p>
                                        <p id="master-file-name" class="text-[9px] text-slate-400 font-bold uppercase">Solo PDF • Máx 100MB</p>
                                    </div>
                                    <input type="file" name="archivo" accept="application/pdf" class="absolute inset-0 opacity-0 cursor-pointer z-50" required onchange="document.getElementById('master-file-name').textContent = this.files[0].name; document.getElementById('master-file-name').className='text-[9px] text-emerald-500 font-bold uppercase'">
                                </div>
                                </label>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 block">Documentos de Soporte (Adjuntos Adicionales)</label>
                                    <button type="button" onclick="addAdjuntoRow()" class="flex items-center gap-2 text-[9px] font-black text-primary uppercase tracking-widest hover:opacity-70 transition-opacity">
                                        <span class="material-symbols-outlined text-sm">add_circle</span> Añadir Línea
                                    </button>
                                </div>
                                
                                <div id="adjuntos-container" class="space-y-3">
                                    <!-- Dynamic rows will appear here -->
                                </div>
                                
                                <p class="text-[8px] text-slate-300 font-bold uppercase tracking-tight ml-1 italic">Puede adjuntar cotizaciones, facturas, memorandos o correos de soporte.</p>
                            </div>

                            <!-- Security Banner -->
                            <div class="p-6 bg-slate-custom dark:bg-slate-950 shadow-2xl rounded-3xl flex items-center gap-6 relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-32 h-full bg-primary/5 skew-x-[-20deg] translate-x-16"></div>
                                <div class="size-12 bg-primary/20 rounded-2xl flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-primary font-variation-settings: 'FILL' 1">verified_user</span>
                                </div>
                                <div class="space-y-0.5">
                                    <h4 class="text-[11px] font-black text-white uppercase tracking-widest">Protocolo de Firma Digital Activo</h4>
                                    <p class="text-[10px] text-slate-400 font-medium">Al completar el registro, el sistema aplicará un sello criptográfico institucional de trazabilidad.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-6 pt-6 pb-20">
                        <a href="/documentos" class="text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-[0.2em] transition-colors">Cancelar Registro</a>
                        <button type="submit" class="bg-primary hover:bg-[#005f6b] text-white px-10 py-4 rounded-full font-black uppercase tracking-[0.2em] shadow-xl shadow-teal-900/20 transition-all flex items-center gap-3 group">
                            Procesar Solicitud
                            <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">send</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <footer class="h-10 px-10 bg-slate-50 dark:bg-slate-900 border-t border-slate-200/50 dark:border-slate-800 flex items-center justify-center shrink-0">
            <p class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.4em]">sgdoc Secure-Node • Promese/Cal • <?= date('Y') ?></p>
        </footer>
    </main>

    <script>
        let adjuntoCount = 0;

        function addAdjuntoRow() {
            adjuntoCount++;
            const container = document.getElementById('adjuntos-container');
            const row = document.createElement('div');
            row.id = `adjunto-row-${adjuntoCount}`;
            row.className = "flex items-center gap-4 bg-slate-50 dark:bg-slate-800/50 p-3 rounded-2xl border border-slate-100 dark:border-slate-800 group animate-in slide-in-from-left-2 duration-300";
            
            row.innerHTML = `
                <div class="size-8 bg-white dark:bg-slate-900 rounded-lg flex items-center justify-center text-slate-300">
                    <span class="material-symbols-outlined text-lg">attachment</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p id="label-${adjuntoCount}" class="text-[9px] font-black text-slate-400 uppercase tracking-tight truncate">Esperando archivo PDF...</p>
                    <input type="file" name="adjuntos[]" accept="application/pdf" class="hidden" onchange="updateRowLabel(this, ${adjuntoCount})">
                </div>
                <button type="button" onclick="triggerInput(${adjuntoCount})" class="text-[9px] font-black text-primary uppercase tracking-widest px-3 py-1 bg-white dark:bg-slate-900 rounded-lg border border-slate-100 dark:border-slate-800 hover:bg-slate-50 transition-colors">Seleccionar</button>
                <button type="button" onclick="removeAdjuntoRow(${adjuntoCount})" class="text-slate-300 hover:text-secondary transition-colors opacity-0 group-hover:opacity-100">
                    <span class="material-symbols-outlined text-lg">delete</span>
                </button>
            `;
            container.appendChild(row);
        }

        function triggerInput(id) {
            const row = document.getElementById(`adjunto-row-${id}`);
            row.querySelector('input').click();
        }

        function updateRowLabel(input, id) {
            const label = document.getElementById(`label-${id}`);
            if (input.files && input.files[0]) {
                label.textContent = input.files[0].name;
                label.className = "text-[9px] font-black text-emerald-500 uppercase tracking-tight truncate";
            }
        }

        function removeAdjuntoRow(id) {
            const row = document.getElementById(`adjunto-row-${id}`);
            row.remove();
        }

        // Initialize Tom Select
        const tomSelectConfig = {
            create: false,
            sortField: { field: "text", direction: "asc" },
            controlInput: '<input>',
            render: {
                item: function(data, escape) {
                    return '<div class="text-[11px] font-bold text-slate-700 dark:text-slate-200">' + escape(data.text) + '</div>';
                },
                option: function(data, escape) {
                    return '<div class="py-2 px-3 text-[11px] font-bold text-slate-600 dark:text-slate-300 hover:bg-primary/10 hover:text-primary cursor-pointer">' + escape(data.text) + '</div>';
                }
            }
        };

        const tsTipo = new TomSelect("#tipo_solicitud_id", tomSelectConfig);
        const tsPrio = new TomSelect("#prioridad", tomSelectConfig);
        const tsDepto = new TomSelect("#departamento_destino_id", tomSelectConfig);

        // Modals Logic
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            // Clear inputs
            const form = document.querySelector(`#${id} form`);
            if(form) form.reset();
        }

        async function saveAjax(e, type, endpoint, selectTsInstance) {
            e.preventDefault();
            const form = e.target;
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">refresh</span> Guardando...';
            btn.disabled = true;

            const formData = new FormData(form);

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();

                if (data.success) {
                    // Add new option and select it
                    selectTsInstance.addOption({value: data.id, text: data.nombre});
                    selectTsInstance.addItem(data.id);
                    closeModal(type === 'tipo' ? 'modalTipoSolicitud' : 'modalDepartamento');
                    
                    // Show a toast or something if needed
                } else {
                    alert(data.message || 'Ocurrió un error al guardar.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Ocurrió un error de conexión.');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }
    </script>

    <!-- Modals -->
    <!-- Modal Tipo Solicitud -->
    <div id="modalTipoSolicitud" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-8 w-full max-w-md shadow-2xl animate-in zoom-in-95 duration-200">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-[14px] font-black uppercase tracking-widest text-slate-800 dark:text-white flex items-center gap-2"><span class="material-symbols-outlined text-primary">category</span> Nuevo Tipo de Solicitud</h3>
                <button onclick="closeModal('modalTipoSolicitud')" class="text-slate-400 hover:text-secondary transition-colors"><span class="material-symbols-outlined">close</span></button>
            </div>
            <form onsubmit="saveAjax(event, 'tipo', '/tipos-solicitudes/guardar-ajax', tsTipo)" class="space-y-4">
                <?= \App\Core\Security::csrfInput() ?>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1.5 block">Nombre *</label>
                    <input type="text" name="nombre" class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 outline-none" required>
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1.5 block">Descripción</label>
                    <textarea name="descripcion" class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 outline-none min-h-[80px]"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeModal('modalTipoSolicitud')" class="px-5 py-2 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-slate-700">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-primary hover:bg-[#005f6b] text-white rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-2 transition-colors"><span class="material-symbols-outlined text-sm">save</span> Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Departamento -->
    <div id="modalDepartamento" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-8 w-full max-w-md shadow-2xl animate-in zoom-in-95 duration-200">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-[14px] font-black uppercase tracking-widest text-slate-800 dark:text-white flex items-center gap-2"><span class="material-symbols-outlined text-primary">corporate_fare</span> Nuevo Departamento</h3>
                <button onclick="closeModal('modalDepartamento')" class="text-slate-400 hover:text-secondary transition-colors"><span class="material-symbols-outlined">close</span></button>
            </div>
            <form onsubmit="saveAjax(event, 'depto', '/departamentos/guardar-ajax', tsDepto)" class="space-y-4">
                <?= \App\Core\Security::csrfInput() ?>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1.5 block">Nombre del Departamento *</label>
                    <input type="text" name="nombre" class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 outline-none" required>
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1.5 block">Descripción</label>
                    <textarea name="descripcion" class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 outline-none min-h-[80px]"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeModal('modalDepartamento')" class="px-5 py-2 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-slate-700">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-primary hover:bg-[#005f6b] text-white rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-2 transition-colors"><span class="material-symbols-outlined text-sm">save</span> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
