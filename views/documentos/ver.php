<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Expediente #<?= htmlspecialchars($documento['id']) ?> - sgdoc</title>
    
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
                <span class="hover:text-primary transition-colors cursor-pointer" onclick="location.href='/documentos'">Expedientes</span>
                <span class="text-slate-300">›</span>
                <span class="text-primary"><?= htmlspecialchars($documento['id']) ?></span>
            </div>
            <div class="ml-auto flex items-center gap-6">
                <button onclick="toggleDarkMode()" class="text-slate-300 hover:text-primary transition-colors h-8 w-8 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">dark_mode</span>
                </button>
            </div>
        </header>

        <!-- Messages Area -->
        <div class="px-10 pt-6 -mb-6 space-y-4">
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
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-hidden flex">
            
            <!-- Left: Document Preview -->
            <div class="flex-1 p-10 flex flex-col gap-10 overflow-y-auto">
                <!-- Viewer Header -->
                <div class="flex items-center justify-between">
                    <div class="space-y-1">
                        <div class="flex items-center gap-4">
                            <?php
                            $stClass = 'bg-slate-50 text-slate-400';
                            if ($documento['estado'] === 'SOLICITADO') $stClass = 'bg-blue-50 text-blue-500';
                            else if ($documento['estado'] === 'AUTORIZADO_ENCARGADO') $stClass = 'bg-violet-50 text-violet-500';
                            else if ($documento['estado'] === 'AUTORIZADO_DEPARTAMENTO') $stClass = 'bg-sky-50 text-sky-500';
                            else if ($documento['estado'] === 'APROBADO_COMPRAS') $stClass = 'bg-amber-50 text-amber-500';
                            else if ($documento['estado'] === 'AUTORIZADO') $stClass = 'bg-emerald-50 text-emerald-500';
                            else if ($documento['estado'] === 'RECHAZADO') $stClass = 'bg-red-50 text-secondary';
                            ?>
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter shadow-sm <?= $stClass ?>"><?= htmlspecialchars($documento['estado']) ?></span>
                            <h2 class="text-2xl font-black text-slate-custom dark:text-white tracking-tight uppercase">Visor de Expediente</h2>
                        </div>
                        <p id="preview-name" class="text-[11px] text-slate-400 font-bold uppercase tracking-widest italic">Documento Maestro: <?= basename($documento['ruta_original']) ?></p>
                    </div>
                    <div class="flex gap-4">
                        <button onclick="resetPreview()" class="p-2.5 bg-slate-50 dark:bg-slate-900 rounded-2xl text-slate-300 hover:text-primary transition-colors border border-slate-100 dark:border-slate-800 shadow-sm flex items-center justify-center" title="Ver Documento Maestro"><span class="material-symbols-outlined">first_page</span></button>
                        <a href="/documentos/descargar/<?= htmlspecialchars($documento['id']) ?>/original" class="p-2.5 bg-slate-50 dark:bg-slate-900 rounded-2xl text-slate-300 hover:text-primary transition-colors border border-slate-100 dark:border-slate-800 shadow-sm flex items-center justify-center"><span class="material-symbols-outlined">download</span></a>
                    </div>
                </div>

                <!-- Central Viewer -->
                <div id="viewer-wrapper" class="flex-1 bg-slate-50 dark:bg-slate-900/50 rounded-[2.5rem] border border-slate-100 dark:border-slate-900 overflow-hidden relative min-h-[500px]">
                    <div id="viewer-content" class="w-full h-full">
                        <?php 
                        $file_ext = strtolower(pathinfo($documento['ruta_original'] ?? '', PATHINFO_EXTENSION));
                        if (in_array($file_ext, ['pdf', 'jpg', 'jpeg', 'png'])): 
                        ?>
                            <iframe src="/documentos/visualizar/<?= htmlspecialchars($documento['id']) ?>/original" class="w-full h-full border-none" title="Preview"></iframe>
                        <?php else: ?>
                            <div class="w-full h-full flex flex-col items-center justify-center gap-4 text-center p-10">
                                <span class="material-symbols-outlined text-6xl text-slate-200">description_off</span>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Previsualización no disponible para (<?= $file_ext ?: 'unknown' ?>)</p>
                                <p class="text-[9px] text-slate-300 font-medium">Este tipo de archivo debe descargarse para su revisión.</p>
                                <a href="/documentos/descargar/<?= htmlspecialchars($documento['id']) ?>/original" class="text-primary text-[10px] font-black uppercase tracking-widest mt-2 underline">Descargar archivo maestro</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right: Document Info & Workflow -->
            <div class="w-[400px] border-l border-slate-50 dark:border-slate-900 overflow-y-auto p-10 space-y-10">

                <!-- ══════════════════════════════════════════
                     STEPPER VISUAL DE FLUJO DE APROBACIÓN
                ═══════════════════════════════════════════ -->
                <?php
                $tipoNombreStepper = $documento['tipo_solicitud_nombre'] ?? $documento['tipo'] ?? '';
                $flujoDoc = \App\Controllers\DocumentoController::obtenerFlujo($tipoNombreStepper);
                $estadoDoc = $documento['estado'];
                $isRechazado = ($estadoDoc === 'RECHAZADO');

                if ($flujoDoc === 'A') {
                    $stepperPasos = [
                        ['label' => 'Creación',   'sub' => 'Secretaria',     'icon' => 'add_circle'],
                        ['label' => 'Encargado',  'sub' => 'Dpto. Origen',   'icon' => 'manage_accounts'],
                        ['label' => 'Jefe',       'sub' => 'Dpto. Destino',  'icon' => 'fact_check'],
                        ['label' => 'Revisión',   'sub' => 'Firma Compras',  'icon' => 'verified'],
                        ['label' => 'Gerencia',   'sub' => 'Aprobación Final','icon' => 'task_alt'],
                    ];
                    $nivelMap = ['SOLICITADO'=>1,'AUTORIZADO_ENCARGADO'=>2,'AUTORIZADO_DEPARTAMENTO'=>3,'APROBADO_COMPRAS'=>4,'AUTORIZADO'=>5,'RECHAZADO'=>0];
                } else {
                    $stepperPasos = [
                        ['label' => 'Creación',   'sub' => 'Secretaria',     'icon' => 'add_circle'],
                        ['label' => 'Encargado',  'sub' => 'Dpto. Origen',   'icon' => 'manage_accounts'],
                        ['label' => 'Jefe',       'sub' => 'Aprobación Final','icon' => 'task_alt'],
                    ];
                    $nivelMap = ['SOLICITADO'=>1,'AUTORIZADO_ENCARGADO'=>2,'AUTORIZADO'=>3,'RECHAZADO'=>0];
                }

                $nivelActual = $nivelMap[$estadoDoc] ?? 0;
                if ($estadoDoc === 'AUTORIZADO') $nivelActual = count($stepperPasos);
                $progressPct = $nivelActual > 0 ? min(100, round(($nivelActual - 1) / max(count($stepperPasos) - 1, 1) * 100)) : 0;
                if ($estadoDoc === 'AUTORIZADO') $progressPct = 100;
                ?>
                <div class="pb-2">
                    <!-- Header fila superior -->
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-xs font-black text-slate-custom dark:text-white uppercase tracking-tight flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">schema</span> Flujo de Aprobación
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-full text-[8px] font-black uppercase tracking-widest <?= $flujoDoc === 'A' ? 'bg-violet-50 text-violet-500 dark:bg-violet-900/20 dark:text-violet-400' : 'bg-teal-50 text-teal-600 dark:bg-teal-900/20 dark:text-teal-400' ?>">
                                Flujo <?= $flujoDoc ?> <?= $flujoDoc === 'A' ? '· Gerencia' : '· Dpto.' ?>
                            </span>
                            <?php if ($isRechazado): ?>
                            <span class="px-2.5 py-1 rounded-full text-[8px] font-black uppercase tracking-widest bg-red-50 text-secondary dark:bg-red-900/20">✕ Rechazado</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Stepper -->
                    <div class="relative">
                        <!-- Track gris de fondo -->
                        <div class="absolute top-[17px] left-[17px] right-[17px] h-[2px] bg-slate-100 dark:bg-slate-800 z-0 rounded-full"></div>
                        <!-- Barra de progreso animada -->
                        <div class="absolute top-[17px] left-[17px] h-[2px] z-0 rounded-full transition-all duration-700 ease-in-out <?= $isRechazado ? 'bg-red-300' : 'bg-gradient-to-r from-emerald-400 via-teal-400 to-blue-500' ?>" style="width:calc(<?= $progressPct ?>% - 17px)"></div>

                        <div class="relative z-10 flex justify-between">
                        <?php foreach ($stepperPasos as $idx => $paso):
                            $stepNum = $idx + 1;
                            $esPasado = ($estadoDoc === 'AUTORIZADO') || ($nivelActual > $stepNum);
                            $esActivo = ($nivelActual === $stepNum) && !$isRechazado;
                            $esCortado = $isRechazado && ($stepNum === $nivelActual);

                            if ($esCortado) {
                                $circle = 'bg-red-500 ring-4 ring-red-100 dark:ring-red-900/30 shadow-lg shadow-red-200';
                                $iconC  = 'text-white';
                                $labC   = 'text-secondary';
                            } elseif ($esPasado) {
                                $circle = 'bg-emerald-500 ring-4 ring-emerald-50 dark:ring-emerald-900/20 shadow-sm shadow-emerald-200';
                                $iconC  = 'text-white';
                                $labC   = 'text-emerald-600 dark:text-emerald-400';
                            } elseif ($esActivo) {
                                $circle = 'bg-blue-500 ring-4 ring-blue-100 dark:ring-blue-900/30 shadow-lg shadow-blue-200';
                                $iconC  = 'text-white';
                                $labC   = 'text-blue-600 dark:text-blue-400';
                            } else {
                                $circle = 'bg-slate-100 dark:bg-slate-800';
                                $iconC  = 'text-slate-300 dark:text-slate-600';
                                $labC   = 'text-slate-300 dark:text-slate-600';
                            }
                            $alignC = ($idx === 0) ? 'items-start text-left' : (($idx === count($stepperPasos)-1) ? 'items-end text-right' : 'items-center text-center');
                        ?>
                            <div class="flex flex-col gap-1.5 flex-1 <?= $alignC ?>">
                                <div class="relative size-[34px] rounded-full flex items-center justify-center <?= $circle ?> transition-all duration-500">
                                    <?php if ($esActivo): ?>
                                    <span class="absolute inset-0 rounded-full bg-blue-400 opacity-25 animate-ping"></span>
                                    <?php endif; ?>
                                    <span class="material-symbols-outlined text-[14px] <?= $iconC ?>">
                                        <?= ($esPasado && !$esCortado) ? 'check' : ($esCortado ? 'close' : $paso['icon']) ?>
                                    </span>
                                </div>
                                <p class="text-[7.5px] font-black uppercase tracking-widest <?= $labC ?> leading-none"><?= $paso['label'] ?></p>
                                <p class="text-[6.5px] font-medium text-slate-300 dark:text-slate-700 uppercase tracking-wider leading-none"><?= $paso['sub'] ?></p>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Info Section -->
                <div class="space-y-8">
                    <h3 class="text-xs font-black text-slate-custom dark:text-white uppercase tracking-tight flex items-center gap-2">
                        <span class="size-2 bg-primary rounded-full"></span> Propiedades del Registro
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Tipo de Solicitud</span>
                            <p class="text-[11px] font-bold text-slate-800 dark:text-white uppercase"><?= htmlspecialchars($documento['tipo_solicitud_nombre'] ?? $documento['tipo']) ?></p>
                        </div>
                        <div>
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Departamento Destino</span>
                            <?php if (!empty($documento['depto_destino_nombre'])): ?>
                                <p class="text-[11px] font-bold text-slate-800 dark:text-white uppercase"><?= htmlspecialchars($documento['depto_destino_nombre']) ?></p>
                            <?php else: ?>
                                <div class="flex items-center gap-2">
                                    <p class="text-[11px] font-bold text-slate-500 uppercase"><?= htmlspecialchars($documento['depto_solicitante'] ?? 'No especificado') ?></p>
                                    <span class="text-[8px] px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-400 uppercase font-black tracking-widest">Histórico</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Prioridad de Atención</span>
                            <?php
                            $prioColor = 'text-slate-400 bg-slate-50 dark:bg-slate-900 border-slate-200';
                            $prioIcon = 'stat_0';
                            $prioridad = $documento['prioridad'] ?? 'Normal';
                            if ($prioridad === 'Crítica') {
                                $prioColor = 'text-red-500 bg-red-50 dark:bg-red-900/20 border-red-100/50';
                                $prioIcon = 'priority_high';
                            } else if ($prioridad === 'Alta') {
                                $prioColor = 'text-amber-500 bg-amber-50 dark:bg-amber-900/20 border-amber-100/50';
                                $prioIcon = 'keyboard_double_arrow_up';
                            } else if ($prioridad === 'Normal') {
                                $prioColor = 'text-teal-500 bg-teal-50 dark:bg-teal-900/20 border-teal-100/50';
                                $prioIcon = 'remove';
                            } else if ($prioridad === 'Baja') {
                                $prioColor = 'text-slate-400 bg-slate-50 dark:bg-slate-900/20 border-slate-200/50';
                                $prioIcon = 'keyboard_arrow_down';
                            }
                            ?>
                            <div class="flex items-center gap-2 <?= $prioColor ?> px-3 py-1.5 rounded-lg border w-fit">
                                <span class="material-symbols-outlined text-sm"><?= $prioIcon ?></span>
                                <span class="text-[10px] font-black uppercase tracking-widest"><?= htmlspecialchars($prioridad) ?></span>
                            </div>
                        </div>
                        <div>
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Descripción Operativa</span>
                            <p class="text-[10px] font-medium text-slate-500 leading-relaxed italic"><?= htmlspecialchars($documento['descripcion']) ?></p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Fecha Creación</span>
                                <p class="text-[11px] font-bold text-slate-800 dark:text-white uppercase"><?= date('d/m/Y', strtotime($documento['fecha_creacion'])) ?></p>
                            </div>
                            <div>
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Solicitante</span>
                                <p class="text-[11px] font-bold text-slate-800 dark:text-white uppercase"><?= htmlspecialchars($documento['nombre_usuario']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ╔══════════════════════════════════╗
                     ║   ACCIONES DE WORKFLOW           ║
                     ╚══════════════════════════════════╝ -->
                <?php
                    $rol = $_SESSION['rol_nombre'] ?? '';
                    $isAdmin = (strpos(strtolower($_SESSION['rol_nombre'] ?? ''), 'admin') !== false);
                    $estado = $documento['estado'];
                    $esAdmin    = strpos(strtolower($rol), 'admin') !== false;
                    $esGerencia = \App\Controllers\AuthController::tienePermiso('documentos_autorizar_gerencia');
                    $esJefeDepto  = ($rol === 'Jefe de departamento');
                    $esEncargado = \App\Controllers\AuthController::tienePermiso('documentos_autorizar_encargado');
                    $esSecretaria = \App\Controllers\AuthController::tienePermiso('documentos_gestionar_propios');
                    $esPropietario = ($documento['usuario_id'] == ($_SESSION['usuario_id'] ?? 0));

                    // ¿El Encargado pertenece al departamento ORIGEN del documento?
                    $esDptoOrigen = false;
                    if ($esEncargado) {
                        if (!empty($_SESSION['departamento_id']) && !empty($documento['departamento_origen_id'])) {
                            $esDptoOrigen = ($_SESSION['departamento_id'] == $documento['departamento_origen_id']);
                        } else {
                            $esDptoOrigen = (($_SESSION['departamento'] ?? '') === ($documento['depto_solicitante'] ?? ''));
                        }
                    }

                    // ¿El Jefe pertenece al departamento DESTINO del documento?
                    $esDptoDestino = false;
                    if ($esJefeDepto) {
                        if (!empty($_SESSION['departamento_id']) && !empty($documento['departamento_destino_id'])) {
                            $esDptoDestino = ($_SESSION['departamento_id'] == $documento['departamento_destino_id']);
                        } else {
                            $deptoAsignado = !empty($documento['depto_destino_nombre']) ? $documento['depto_destino_nombre'] : $documento['depto_solicitante'];
                            $esDptoDestino = ($deptoAsignado === ($_SESSION['departamento'] ?? ''));
                        }
                    }

                    // PASO 1: Encargado de departamento autoriza SOLICITADO → AUTORIZADO_ENCARGADO
                    $puedeAutorizarEncargado = (($esEncargado && $esDptoOrigen && !$esPropietario) || $esAdmin) && $estado === 'SOLICITADO';

                    // PASO 2: Jefe de departamento destino autoriza AUTORIZADO_ENCARGADO → AUTORIZADO_DEPARTAMENTO/AUTORIZADO
                    $puedeAutorizarDepto = (($esJefeDepto && $esDptoDestino && !$esPropietario) || $esAdmin) && $estado === 'AUTORIZADO_ENCARGADO';

                    // PASO 3 (Flujo A): Firma intermedia AUTORIZADO_DEPARTAMENTO → APROBADO_COMPRAS
                    $puedeAprobarCompras = ($esJefeDepto || $esAdmin) && $estado === 'AUTORIZADO_DEPARTAMENTO';

                    // PASO 4 (Flujo A): Gerencia autoriza definitivamente APROBADO_COMPRAS → AUTORIZADO
                    $puedeAutorizar = ($esGerencia || $esAdmin) && $estado === 'APROBADO_COMPRAS';
                    
                    // ¿Puede rechazar? (cualquier aprobador en turno activo)
                    $puedeRechazar = $puedeAutorizarEncargado || $puedeAutorizarDepto || $puedeAprobarCompras || $puedeAutorizar;
                    $puedeDevolver = false;
                    
                    // ¿Puede editar? El creador si está RECHAZADO
                    $puedeEditar = (($esSecretaria || $esJefeDepto) && $esPropietario && $estado === 'RECHAZADO') || ($esAdmin && $estado === 'RECHAZADO');

                    $hayAcciones = $puedeAutorizarEncargado || $puedeAutorizarDepto || $puedeAprobarCompras || $puedeAutorizar || $puedeRechazar || $puedeEditar;
                ?>
                <?php if ($hayAcciones): ?>
                <div class="space-y-4">
                    <h3 class="text-xs font-black text-slate-custom dark:text-white uppercase tracking-tight flex items-center gap-2">
                        <span class="size-2 bg-primary rounded-full animate-pulse"></span> Acciones Disponibles
                    </h3>

                    <div class="space-y-3">

                        <?php if ($puedeAutorizarEncargado): ?>
                        <!-- BOTÓN AUTORIZAR (Encargado de departamento) → PASO 1 -->
                        <form method="POST" action="/documentos/autorizar_encargado/<?= htmlspecialchars($documento['id']) ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <button type="submit"
                                onclick="return confirm('¿Confirma la autorización de la solicitud #<?= htmlspecialchars($documento['id']) ?>? Será enviada al Jefe de departamento destino.')"
                                class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-violet-500 hover:bg-violet-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all shadow-sm shadow-violet-500/20">
                                <span class="material-symbols-outlined text-base">manage_accounts</span>
                                Autorizar (Encargado)
                            </button>
                        </form>
                        <?php endif; ?>

                        <?php if ($puedeAutorizarDepto): ?>
                        <!-- BOTÓN AUTORIZAR (Jefe de departamento destino) → PASO 2 -->
                        <form method="POST" action="/documentos/autorizar_depto/<?= htmlspecialchars($documento['id']) ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <button type="submit"
                                onclick="return confirm('¿Confirma la autorización departamental del expediente #<?= htmlspecialchars($documento['id']) ?>?')"
                                class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-blue-500 hover:bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all shadow-sm shadow-blue-500/20">
                                <span class="material-symbols-outlined text-base">fact_check</span>
                                Autorizar (Jefe Destino)
                            </button>
                        </form>
                        <?php endif; ?>

                        <?php if ($puedeAprobarCompras): ?>
                        <!-- BOTÓN APROBAR (Compras) -->
                        <form method="POST" action="/documentos/aprobar/<?= htmlspecialchars($documento['id']) ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <button type="submit"
                                onclick="return confirm('¿Confirma la aprobación y firma digital del expediente <?= htmlspecialchars($documento['id']) ?>?')"
                                class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all shadow-sm shadow-emerald-500/20">
                                <span class="material-symbols-outlined text-base">verified</span>
                                Aprobar y Firmar
                            </button>
                        </form>
                        <?php endif; ?>

                        <?php if ($puedeAutorizar): ?>
                        <!-- BOTÓN AUTORIZAR (Gerencia / Admin) -->
                        <form method="POST" action="/documentos/autorizar/<?= htmlspecialchars($documento['id']) ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <button type="submit"
                                onclick="return confirm('¿Confirma la autorización final del expediente <?= htmlspecialchars($documento['id']) ?>?')"
                                class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all shadow-sm shadow-emerald-500/20">
                                <span class="material-symbols-outlined text-base">task_alt</span>
                                Autorizar Definitivamente
                            </button>
                        </form>
                        <?php endif; ?>

                        <?php if ($puedeDevolver || $puedeRechazar): ?>
                        <!-- BOTÓN DEVOLVER / RECHAZAR -->
                        <button type="button" onclick="openRejectionModal()"
                            class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-white dark:bg-slate-900 border border-red-200 dark:border-red-900 text-secondary text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-red-50 dark:hover:bg-secondary/10 transition-all">
                            <span class="material-symbols-outlined text-base">undo</span>
                            <?= $puedeDevolver ? 'Devolver a Revisión' : 'Observar / Rechazar' ?>
                        </button>
                        <?php endif; ?>

                        <?php if ($puedeEditar): ?>
                        <!-- BOTÓN EDITAR (Solicitante, doc rechazado) -->
                        <a href="/documentos/editar/<?= htmlspecialchars($documento['id']) ?>"
                            class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all shadow-sm shadow-amber-500/20">
                            <span class="material-symbols-outlined text-base">edit_note</span>
                            Corregir y Reenviar
                        </a>
                        <?php endif; ?>

                    </div>
                </div>
                <?php endif; ?>

                <!-- MODAL DE RECHAZO / DEVOLUCIÓN -->
                <div id="modal-rechazo" class="fixed inset-0 z-[200] hidden items-center justify-center p-6 bg-slate-950/60 backdrop-blur-md">
                    <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-[2.5rem] shadow-2xl flex flex-col border border-slate-100 dark:border-slate-800 overflow-hidden">
                        <div class="p-8 pb-4 flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-black text-slate-custom dark:text-white uppercase tracking-tight">
                                    <?php if ($puedeDevolver ?? false): ?>Devolver a Revisión<?php else: ?>Observar Documento<?php endif; ?>
                                </h3>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Expediente #<?= htmlspecialchars($documento['id']) ?></p>
                            </div>
                            <button onclick="closeRejectionModal()" class="size-10 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-400 hover:text-secondary transition-colors">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <form method="POST" action="/documentos/rechazar/<?= htmlspecialchars($documento['id']) ?>" class="p-8 pt-4 space-y-4">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block">Motivo / Comentario</label>
                                <textarea name="comentario" rows="4" required
                                    placeholder="Explique el motivo de la devolución u observación..."
                                    class="w-full px-4 py-3 text-[11px] font-medium bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl focus:outline-none focus:border-primary transition-colors resize-none placeholder:text-slate-300"></textarea>
                            </div>
                            <button type="submit" class="w-full py-3 bg-secondary hover:bg-red-700 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all shadow-sm shadow-secondary/20">
                                Confirmar y Enviar
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Attachments Section (Supplementary) -->
                <?php if (!empty($adjuntos)): ?>

                <div class="space-y-6">
                    <h3 class="text-xs font-black text-slate-custom dark:text-white uppercase tracking-tight flex items-center gap-2">
                        <span class="size-2 bg-primary rounded-full"></span> Documentos de Soporte
                    </h3>
                    <div class="space-y-3">
                        <?php foreach ($adjuntos as $adj): ?>
                        <?php 
                            $adj_ext = strtolower(pathinfo($adj['nombre_archivo'], PATHINFO_EXTENSION));
                            $is_previewable = in_array($adj_ext, ['pdf', 'jpg', 'jpeg', 'png', 'txt']);
                        ?>
                        <div 
                            onclick="<?= $is_previewable ? 'switchPreview(\'/documentos/visualizar_soporte/' . $adj['id'] . '\', \'' . htmlspecialchars($adj['nombre_archivo']) . '\', true)' : 'alert(\'Este tipo de archivo no permite previsualización directa.\')' ?>"
                            class="p-4 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-3xl flex items-center justify-between group hover:border-primary hover:bg-white dark:hover:bg-slate-900 transition-all cursor-pointer">
                            <div class="flex items-center gap-3 overflow-hidden text-slate-500">
                                <span class="material-symbols-outlined text-sm <?= $is_previewable ? 'text-primary' : '' ?>">
                                    <?= $is_previewable ? 'visibility' : 'attach_file' ?>
                                </span>
                                <span class="text-[9px] font-bold uppercase truncate max-w-[150px] group-hover:text-slate-700 dark:group-hover:text-white transition-colors"><?= htmlspecialchars($adj['nombre_archivo']) ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="/documentos/descargar_soporte/<?= htmlspecialchars($adj['id']) ?>" onclick="event.stopPropagation()" class="size-8 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl flex items-center justify-center text-slate-300 hover:text-primary transition-colors shadow-sm">
                                    <span class="material-symbols-outlined text-lg">download</span>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-[8px] text-slate-300 font-bold uppercase text-center tracking-tighter">Haga clic en un adjunto para previsualizar</p>
                </div>
                <?php endif; ?>
                       <!-- Tracking / Seguimiento Integrado -->
                <div class="pt-8 border-t border-slate-50 dark:border-slate-900 space-y-6">
                    <h3 class="text-xs font-black text-slate-custom dark:text-white uppercase tracking-tight flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">route</span> Línea de Vida del Expediente
                    </h3>
                    
                    <div class="relative pl-10 space-y-8 before:content-[''] before:absolute before:left-[19px] before:top-2 before:bottom-0 before:w-[2px] before:bg-slate-200 dark:before:bg-slate-800">
                        <?php if (empty($seguimiento)): ?>
                            <div class="py-4 text-center space-y-2">
                                <span class="material-symbols-outlined text-2xl text-slate-200">history_toggle_off</span>
                                <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest leading-tight">Sin registros previos de trazabilidad</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($seguimiento as $index => $log): ?>
                                <?php
                                $icon = 'check_circle';
                                $bgClass = 'bg-slate-200 dark:bg-slate-700';
                                $textClass = 'text-slate-600 dark:text-slate-300';
                                $iconClass = 'text-slate-500 dark:text-slate-300';

                                if ($index === 0) {
                                    $icon = 'radio_button_checked';
                                    $bgClass = 'bg-primary';
                                    $textClass = 'text-primary font-black';
                                    $iconClass = 'text-white';
                                } else if ($log['accion'] === 'CREACION') {
                                    $icon = 'arrow_forward';
                                    $bgClass = 'bg-emerald-500';
                                    $textClass = 'text-emerald-500';
                                    $iconClass = 'text-white';
                                } else if (strpos($log['accion'], 'RECHAZA') !== false || strpos($log['accion'], 'DEVOLVER') !== false) {
                                    $icon = 'close';
                                    $bgClass = 'bg-red-500';
                                    $textClass = 'text-red-500';
                                    $iconClass = 'text-white';
                                } else {
                                    $icon = 'check';
                                    $bgClass = 'bg-amber-500';
                                    $textClass = 'text-amber-500';
                                    $iconClass = 'text-white';
                                }
                                ?>
                                <div class="relative group">
                                    <div class="absolute -left-[35px] top-0 size-8 <?= $bgClass ?> rounded-full z-10 flex items-center justify-center ring-4 ring-slate-50 dark:ring-slate-900 shadow-sm">
                                        <span class="material-symbols-outlined text-[14px] <?= $iconClass ?>"><?= $icon ?></span>
                                    </div>
                                    
                                    <div class="flex flex-col pt-1 space-y-1.5">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-[10px] font-black tracking-widest uppercase <?= $textClass ?>"><?= htmlspecialchars($log['accion']) ?></span>
                                            <span class="text-[9px] font-bold text-slate-400"><?= ($index === 0) ? 'Ahora' : date('d M Y, h:i A', strtotime($log['fecha_movimiento'])) ?></span>
                                        </div>
                                        
                                        <div class="flex items-center gap-1.5 text-slate-500">
                                            <span class="material-symbols-outlined text-[12px]">location_on</span>
                                            <span class="text-[10px] font-bold uppercase tracking-tight"><?= htmlspecialchars($log['nombre_usuario']) ?> — <?= htmlspecialchars($log['departamento'] ?: 'N/D') ?></span>
                                        </div>

                                        <?php if (!empty($log['detalles'])): ?>
                                            <div class="pt-1.5">
                                                <p class="text-[10px] text-slate-400 font-medium leading-relaxed italic line-clamp-2"><?= htmlspecialchars($log['detalles']) ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Final Step -->
                        <?php if ($documento['estado'] === 'AUTORIZADO' && !empty($documento['fecha_finalizacion'])): ?>
                            <div class="relative group mt-4">
                                <div class="absolute -left-[35px] top-0 size-8 bg-indigo-500 rounded-full z-10 flex items-center justify-center ring-4 ring-slate-50 dark:ring-slate-900 shadow-sm">
                                    <span class="material-symbols-outlined text-[14px] text-white">done_all</span>
                                </div>
                                <div class="flex flex-col pt-1 space-y-1.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[10px] font-black tracking-widest uppercase text-indigo-500">Cierre de Ciclo</span>
                                        <span class="text-[9px] font-bold text-slate-400"><?= date('d M Y, h:i A', strtotime($documento['fecha_finalizacion'])) ?></span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-slate-500">
                                        <span class="material-symbols-outlined text-[12px]">location_on</span>
                                        <span class="text-[10px] font-bold uppercase tracking-tight">Autorización Finalizada</span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (count($seguimiento) > 3): ?>
                        <div class="pt-6">
                            <button onclick="openSeguimientoModal()" class="w-full py-2.5 bg-white dark:bg-slate-900 rounded-xl text-[9px] font-black text-slate-500 hover:text-primary hover:border-primary transition-all uppercase tracking-widest border border-slate-200 dark:border-slate-800 shadow-sm">Ver historial completo</button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Trazabilidad / Seguimiento Modal (Still available for long history) -->
                <div id="modal-seguimiento" class="fixed inset-0 z-[100] hidden items-center justify-center p-6 bg-slate-950/60 backdrop-blur-md">
                    <div class="bg-white dark:bg-slate-900 w-full max-w-2xl max-h-[85vh] rounded-[3rem] shadow-2xl flex flex-col relative border border-slate-100 dark:border-slate-800 overflow-hidden">
                        
                        <!-- Modal Header -->
                        <div class="p-10 pb-6 flex items-center justify-between shrink-0">
                            <div class="space-y-1">
                                <h3 class="text-2xl font-black text-slate-custom dark:text-white uppercase tracking-tight">Bitácora de Expediente</h3>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Trazabilidad histórica completa #<?= htmlspecialchars($documento['id']) ?></p>
                            </div>
                            <button onclick="closeSeguimientoModal()" class="size-12 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-400 hover:text-secondary transition-colors">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        
                        <div class="flex-1 overflow-y-auto px-10 pb-10 custom-scrollbar">
                            <div class="relative pl-8 space-y-10 before:content-[''] before:absolute before:left-[11px] before:top-2 before:bottom-0 before:w-[2px] before:bg-slate-100 dark:before:bg-slate-800">
                                <?php foreach ($seguimiento as $index => $log): ?>
                                    <div class="relative group">
                                        <div class="absolute -left-[27px] top-1 size-4 bg-white dark:bg-slate-900 border-2 border-<?= ($index === 0) ? 'primary' : 'slate-200 dark:border-slate-700' ?> rounded-full z-10 group-hover:scale-125 transition-transform"></div>
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between gap-4">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[11px] font-black text-slate-custom dark:text-white uppercase tracking-tight"><?= htmlspecialchars($log['accion']) ?></span>
                                                    <span class="px-2 py-0.5 rounded-full bg-teal-50 dark:bg-teal-900 text-[8px] font-black text-primary border border-teal-100/30 uppercase"><?= htmlspecialchars($log['estado_nuevo'] ?: 'MOVIMIENTO') ?></span>
                                                </div>
                                                <span class="text-[9px] font-bold text-slate-300 dark:text-slate-600 uppercase tracking-widest"><?= date('d M Y, h:i A', strtotime($log['fecha_movimiento'])) ?></span>
                                            </div>
                                            <div class="flex items-center gap-3 text-slate-400">
                                                <div class="size-6 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-xs">person</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <p class="text-[10px] font-black text-slate-700 dark:text-slate-300 uppercase leading-none"><?= htmlspecialchars($log['nombre_usuario']) ?></p>
                                                    <p class="text-[8px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider"><?= htmlspecialchars($log['departamento'] ?: 'Departamento No Definido') ?></p>
                                                </div>
                                            </div>
                                            <?php if (!empty($log['detalles'])): ?>
                                                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100/50 dark:border-slate-800">
                                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed italic"><?= htmlspecialchars($log['detalles']) ?></p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function openSeguimientoModal() {
                        const modal = document.getElementById('modal-seguimiento');
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    }
                    function closeSeguimientoModal() {
                        const modal = document.getElementById('modal-seguimiento');
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }
                    
                    // Rejection Modal functions
                    function openRejectionModal() {
                        const modal = document.getElementById('modal-rechazo');
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    }
                    function closeRejectionModal() {
                        const modal = document.getElementById('modal-rechazo');
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }
                    
                    const puedeAprobarCompras = <?= ($documento['estado'] === 'AUTORIZADO_DEPARTAMENTO') ? 'true' : 'false' ?>;
                </script>

                <script>
                    const masterUrl = "/documentos/visualizar/<?= htmlspecialchars($documento['id']) ?>/original";
                    const masterName = "<?= basename($documento['ruta_original']) ?>";

                    function switchPreview(url, name, isSupport = false) {
                        const content = document.getElementById('viewer-content');
                        const label = document.getElementById('preview-name');
                        
                        // Actualizar Etiqueta
                        label.innerHTML = isSupport 
                            ? `<span class="text-primary font-black">Viendo Adjunto:</span> ${name}`
                            : `<span class="text-slate-400 font-bold">Documento Maestro:</span> ${name}`;

                        // Actualizar Iframe
                        content.innerHTML = `<iframe src="${url}" class="w-full h-full border-none animate-in fade-in duration-500" title="Preview"></iframe>`;
                        
                        // Scroll al visor si estamos en móvil
                        if (window.innerWidth < 1024) {
                            document.getElementById('viewer-wrapper').scrollIntoView({ behavior: 'smooth' });
                        }
                    }

                    function resetPreview() {
                        switchPreview(masterUrl, masterName, false);
                    }
                </script>

            </div>
        </div>

        <footer class="h-10 px-10 flex items-center justify-center shrink-0 border-t border-slate-50 dark:border-slate-900">
            <p class="text-[8px] font-black text-slate-200 dark:text-slate-800 uppercase tracking-[0.5em]">sgdoc Audit-Point • 2026</p>
        </footer>
    </main>
</body>
</html>
