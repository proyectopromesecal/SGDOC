<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Corregir Expediente #<?= htmlspecialchars($documento['id']) ?> - sgdoc</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: { primary: "#007281", secondary: "#E41E26", "slate-custom": "#111827" },
                    fontFamily: { sans: ["Inter", "sans-serif"] },
                },
            },
        };
    </script>
    <style>body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }</style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen flex">
    
    <?php include VIEWS_PATH . '/partials/sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-14 px-8 flex items-center border-b border-slate-200/50 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-md shrink-0 z-20">
            <div class="flex items-center gap-2 text-[9px] font-black uppercase tracking-[0.3em] text-slate-400">
                <span class="hover:text-primary transition-colors cursor-pointer" onclick="location.href='/dashboard'">sgdoc</span>
                <span class="text-slate-300">›</span>
                <span class="hover:text-primary transition-colors cursor-pointer" onclick="location.href='/documentos'">Expedientes</span>
                <span class="text-slate-300">›</span>
                <span class="text-primary">Corregir #<?= htmlspecialchars($documento['id']) ?></span>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-10">
            <div class="max-w-3xl mx-auto space-y-10">
                <div class="space-y-1">
                    <h1 class="text-[32px] font-black text-slate-custom dark:text-white tracking-tight uppercase">Corregir Documento</h1>
                    <p class="text-slate-400 text-[11px] font-medium italic">Actualice la información solicitada para re-enviar el expediente a revisión.</p>
                </div>

                <!-- Rejection Note -->
                <div class="p-6 bg-red-50 dark:bg-red-900/20 rounded-[2rem] border border-red-100/50 space-y-2">
                    <div class="flex items-center gap-2 text-secondary">
                        <span class="material-symbols-outlined text-lg">info</span>
                        <h4 class="text-[10px] font-black uppercase tracking-widest">Motivo del Rechazo:</h4>
                    </div>
                    <p class="text-xs font-bold text-slate-600 dark:text-slate-100 italic">"<?= htmlspecialchars($documento['comentario_rechazo'] ?: 'No se especificó un motivo detallado.') ?>"</p>
                </div>

                <form action="/documentos/actualizar/<?= $documento['id'] ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
                    <?= \App\Core\Security::csrfInput() ?>
                    <div class="bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-10 shadow-sm space-y-8">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 block">Tipo de Solicitud *</label>
                                <select name="tipo_solicitud_id" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none appearance-none" required>
                                    <option value="" disabled>SELECCIONE LA TIPOLOGÍA...</option>
                                    <?php foreach ($tiposSolicitud as $t): ?>
                                        <option value="<?= $t['id'] ?>" <?= $documento['tipo_solicitud_id'] == $t['id'] ? 'selected' : '' ?>><?= htmlspecialchars($t['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 block">Departamento Destino *</label>
                                <select name="departamento_destino_id" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none appearance-none" required>
                                    <option value="" disabled>SELECCIONE EL DEPARTAMENTO...</option>
                                    <?php foreach ($departamentos as $d): ?>
                                        <option value="<?= $d['id'] ?>" <?= $documento['departamento_destino_id'] == $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 block">Prioridad de Atención</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-primary transition-colors text-xl">priority_high</span>
                                <select name="prioridad" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none pl-12 appearance-none">
                                    <option value="Baja" <?= ($documento['prioridad'] ?? '') == 'Baja' ? 'selected' : '' ?>>Prioridad Baja</option>
                                    <option value="Normal" <?= ($documento['prioridad'] ?? 'Normal') == 'Normal' ? 'selected' : '' ?>>Prioridad Normal</option>
                                    <option value="Alta" <?= ($documento['prioridad'] ?? '') == 'Alta' ? 'selected' : '' ?>>Prioridad Alta</option>
                                    <option value="Crítica" <?= ($documento['prioridad'] ?? '') == 'Crítica' ? 'selected' : '' ?>>Atención Crítica</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 block">Descripción o Referencia</label>
                            <textarea name="descripcion" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-[11px] font-bold focus:ring-1 focus:ring-primary/20 transition-all outline-none min-h-[120px]" required><?= htmlspecialchars($documento['descripcion']) ?></textarea>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 block">Cargar Nuevo Archivo (Opcional)</label>
                            <label class="border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-[2rem] p-8 flex flex-col items-center justify-center bg-slate-50/50 dark:bg-slate-950/20 cursor-pointer relative group">
                                <span class="material-symbols-outlined text-3xl text-slate-300 group-hover:text-primary transition-colors">cloud_upload</span>
                                <p class="text-[10px] font-black text-slate-400 uppercase mt-2">Seleccionar archivo para reemplazar el anterior</p>
                                <input type="file" name="archivo" accept="application/pdf" class="absolute inset-0 opacity-0 cursor-pointer z-50">
                            </label>
                            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-tight ml-1">Archivo actual: <?= basename($documento['ruta_original']) ?></p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-6 pt-6">
                        <a href="/documentos/ver/<?= $documento['id'] ?>" class="text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest">Descartar</a>
                        <button type="submit" class="bg-primary hover:bg-[#005f6b] text-white px-10 py-4 rounded-full font-black uppercase tracking-widest shadow-xl shadow-teal-900/20 transition-all flex items-center gap-3">
                            Confirmar Correcciones
                            <span class="material-symbols-outlined text-lg">check_circle</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
