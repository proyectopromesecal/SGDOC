<?php
// Agrupar trazas por documento para la vista de tarjetas
$docs_agrupados = [];
foreach ($trazas as $traza) {
    $did = $traza['documento_id'];
    if (!isset($docs_agrupados[$did])) {
        $docs_agrupados[$did] = [
            'documento_id'    => $did,
            'doc_descripcion' => $traza['doc_descripcion'],
            'nombre_usuario'  => $traza['nombre_usuario'],
            'rol_nombre'      => $traza['rol_nombre'] ?? '',
            'departamento'    => $traza['departamento'] ?? '',
            'ultima_fecha'    => $traza['fecha_movimiento'],
            'ultimo_estado'   => $traza['estado_nuevo'],
            'ultima_accion'   => $traza['accion'],
            'movimientos'     => []
        ];
    }
    $docs_agrupados[$did]['movimientos'][] = $traza;
}
// Ordenar por último movimiento DESC
usort($docs_agrupados, fn($a, $b) => strtotime($b['ultima_fecha']) - strtotime($a['ultima_fecha']));
$total_docs = count($docs_agrupados);
$total_movs = count($trazas);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Trazabilidad de Documentos - sgdoc</title>
    
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
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
        .card-collapse { max-height: 0; overflow: hidden; transition: max-height 0.4s cubic-bezier(0.4,0,0.2,1); }
        .card-collapse.open { max-height: 2000px; }
        .chevron { transition: transform 0.3s ease; }
        .chevron.open { transform: rotate(180deg); }
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
                <span class="text-primary">Trazabilidad de Documentos</span>
            </div>
            <div class="ml-auto flex items-center gap-6">
                <button onclick="toggleDarkMode()" class="text-slate-300 hover:text-primary transition-colors h-8 w-8 flex items-center justify-center rounded-lg">
                    <span class="material-symbols-outlined text-xl">dark_mode</span>
                </button>
            </div>
        </header>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <div class="p-10 space-y-8">

                <!-- Title + Search Row -->
                <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
                    <div class="space-y-1">
                        <h1 class="text-[28px] font-black text-slate-custom dark:text-white tracking-tight uppercase">Trazabilidad de Documentos</h1>
                        <p class="text-slate-400 text-[11px] font-medium">Seguimiento en tiempo real de todos los expedientes en proceso.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- Search -->
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 text-[18px]">search</span>
                            <input
                                type="text"
                                id="buscador"
                                placeholder="Buscar expediente, usuario..."
                                onkeyup="filtrarTarjetas(this.value)"
                                class="pl-9 pr-4 py-2 text-[11px] font-medium bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-primary transition-colors w-64 placeholder:text-slate-300"
                            />
                        </div>
                        <button onclick="location.reload()" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-primary hover:border-primary transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">refresh</span> Actualizar
                        </button>
                    </div>
                </div>

                <!-- Stats Pills -->
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Total -->
                    <button id="btn-todos" onclick="setFiltroEstado('todos')" class="filtro-btn active flex items-center gap-2 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border transition-all bg-primary text-white border-primary">
                        Todos <span class="bg-white/20 text-white px-1.5 py-0.5 rounded-full text-[9px]"><?= $total_docs ?></span>
                    </button>
                    <!-- En tránsito / Pendientes -->
                    <?php
                    $activos = array_filter($docs_agrupados, fn($d) => !in_array($d['ultimo_estado'], ['AUTORIZADO', 'RECHAZADO', 'DIGITALIZADO']));
                    $finalizados = array_filter($docs_agrupados, fn($d) => in_array($d['ultimo_estado'], ['AUTORIZADO', 'RECHAZADO']));
                    ?>
                    <button id="btn-activos" onclick="setFiltroEstado('activo')" class="filtro-btn flex items-center gap-2 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border transition-all bg-white dark:bg-slate-800 text-slate-500 border-slate-200 dark:border-slate-700 hover:border-primary hover:text-primary">
                        En Proceso <span class="bg-slate-100 dark:bg-slate-700 px-1.5 py-0.5 rounded-full text-[9px]"><?= count($activos) ?></span>
                    </button>
                    <button id="btn-finalizados" onclick="setFiltroEstado('finalizado')" class="filtro-btn flex items-center gap-2 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border transition-all bg-white dark:bg-slate-800 text-slate-500 border-slate-200 dark:border-slate-700 hover:border-primary hover:text-primary">
                        Finalizados <span class="bg-slate-100 dark:bg-slate-700 px-1.5 py-0.5 rounded-full text-[9px]"><?= count($finalizados) ?></span>
                    </button>

                    <div class="ml-auto flex items-center gap-2 text-[10px] text-slate-400">
                        <span id="contador-mostrados" class="font-black text-slate-600 dark:text-slate-300"><?= $total_docs ?></span>
                        <span>de <?= $total_docs ?> expedientes</span>
                        <span class="mx-2 text-slate-200">•</span>
                        <span class="text-primary font-black"><?= $total_movs ?> movimientos</span>
                    </div>
                </div>

                <!-- Cards List -->
                <?php if (empty($docs_agrupados)): ?>
                <div class="bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-3xl p-20 flex flex-col items-center gap-4 text-center">
                    <span class="material-symbols-outlined text-6xl text-slate-200">route</span>
                    <p class="text-[11px] font-black text-slate-300 uppercase tracking-widest">No se han registrado movimientos todavía</p>
                </div>
                <?php else: ?>
                <div id="lista-tarjetas" class="space-y-3">
                    <?php foreach ($docs_agrupados as $idx => $doc):
                        $movs = $doc['movimientos'];
                        $ultimo = $movs[0]; // DESC order => último movimiento
                        $primero = end($movs); // primer movimiento (creación)
                        reset($movs);

                        $estado = strtoupper($doc['ultimo_estado'] ?? '');
                        $accion = strtoupper($doc['ultima_accion'] ?? '');

                        // Estado badge color
                        $badgeClass = 'text-slate-500 border-slate-200 bg-slate-50 dark:bg-slate-800 dark:border-slate-700';
                        $dotClass   = 'bg-slate-300';
                        $estadoLabel = $estado;
                        if ($estado === 'SOLICITADO') {
                            $badgeClass = 'text-blue-600 border-blue-200 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800';
                            $dotClass   = 'bg-blue-400';
                            $estadoLabel = 'En Revisión';
                        } elseif ($estado === 'APROBADO_COMPRAS') {
                            $badgeClass = 'text-amber-600 border-amber-200 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-800';
                            $dotClass   = 'bg-amber-400';
                            $estadoLabel = 'Aprobado Compras';
                        } elseif ($estado === 'AUTORIZADO') {
                            $badgeClass = 'text-emerald-600 border-emerald-200 bg-emerald-50 dark:bg-emerald-900/20 dark:border-emerald-800';
                            $dotClass   = 'bg-emerald-400';
                            $estadoLabel = 'Autorizado';
                        } elseif ($estado === 'RECHAZADO') {
                            $badgeClass = 'text-red-600 border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800';
                            $dotClass   = 'bg-red-400';
                            $estadoLabel = 'Rechazado';
                        }

                        $esFinalizado = in_array($estado, ['AUTORIZADO', 'RECHAZADO']);
                        $dataEstado = $esFinalizado ? 'finalizado' : 'activo';

                        // Calcular tiempo transcurrido desde primer movimiento
                        $tInicio = strtotime($primero['fecha_movimiento']);
                        $tAhora  = time();
                        $diffSec = $tAhora - $tInicio;
                        $diffH   = floor($diffSec / 3600);
                        $diffM   = floor(($diffSec % 3600) / 60);
                        $tiempoLabel = $diffH > 0 ? "{$diffH}h {$diffM}m" : "{$diffM}m";
                    ?>
                    <div class="doc-card bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-3xl overflow-hidden transition-all hover:shadow-md hover:border-slate-200 dark:hover:border-slate-700"
                         data-id="<?= htmlspecialchars($doc['documento_id']) ?>"
                         data-desc="<?= htmlspecialchars(strtolower($doc['doc_descripcion'])) ?>"
                         data-user="<?= htmlspecialchars(strtolower($doc['nombre_usuario'])) ?>"
                         data-estado="<?= $dataEstado ?>">

                        <!-- Card Header (always visible) -->
                        <div class="flex items-center gap-4 px-6 py-5 cursor-pointer" onclick="toggleCard(<?= $idx ?>)">

                            <!-- Avatar -->
                            <div class="size-11 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-slate-400 text-xl">description</span>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0 space-y-1">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <p class="text-[13px] font-black text-slate-800 dark:text-white uppercase tracking-tight"><?= htmlspecialchars($doc['documento_id']) ?></p>
                                    <?php if (!empty($doc['doc_descripcion'])): ?>
                                    <p class="text-[10px] text-slate-400 font-medium truncate max-w-[280px]"><?= htmlspecialchars($doc['doc_descripcion']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="flex items-center gap-4 text-[10px] text-slate-400 flex-wrap">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px]">person</span>
                                        <?= htmlspecialchars($doc['nombre_usuario']) ?>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px]">schedule</span>
                                        <?= $tiempoLabel ?>
                                    </span>
                                    <?php if (!empty($doc['departamento'])): ?>
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px]">location_on</span>
                                        <?= htmlspecialchars($doc['departamento']) ?>
                                    </span>
                                    <?php endif; ?>
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px]">calendar_today</span>
                                        <?= date('d M Y', strtotime($doc['ultima_fecha'])) ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Estado + Chevron -->
                            <div class="flex items-center gap-3 shrink-0">
                                <div class="flex items-center gap-1.5 text-[9px] font-black uppercase tracking-widest border px-3 py-1.5 rounded-full <?= $badgeClass ?>">
                                    <span class="size-1.5 rounded-full <?= $dotClass ?> <?= !$esFinalizado ? 'animate-pulse' : '' ?>"></span>
                                    <?= $estadoLabel ?>
                                </div>
                                <span id="chevron-<?= $idx ?>" class="material-symbols-outlined text-slate-300 text-xl chevron">expand_more</span>
                            </div>
                        </div>

                        <!-- Card Body (expandable) -->
                        <div id="card-body-<?= $idx ?>" class="card-collapse">
                            <div class="border-t border-slate-100 dark:border-slate-800 px-6 py-6 grid grid-cols-1 md:grid-cols-2 gap-8">

                                <!-- LEFT: Recorrido del expediente -->
                                <div class="space-y-4">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[14px] text-primary">alt_route</span>
                                        Recorrido del Expediente
                                    </p>

                                    <div class="relative pl-9 space-y-5 before:content-[''] before:absolute before:left-[15px] before:top-2 before:bottom-0 before:w-[2px] before:bg-slate-100 dark:before:bg-slate-800">
                                        <?php foreach ($movs as $mi => $mov):
                                            $mAccion = strtoupper($mov['accion']);
                                            $isFirst = ($mi === 0);

                                            // Ícono y color por tipo de acción
                                            if ($isFirst) {
                                                $mBg   = 'bg-primary';
                                                $mIcon = 'radio_button_checked';
                                                $mText = 'text-primary';
                                            } elseif (str_contains($mAccion, 'CREACI')) {
                                                $mBg   = 'bg-emerald-500';
                                                $mIcon = 'arrow_forward';
                                                $mText = 'text-emerald-600';
                                            } elseif (str_contains($mAccion, 'RECHAZ') || str_contains($mAccion, 'DEVOLV')) {
                                                $mBg   = 'bg-secondary';
                                                $mIcon = 'close';
                                                $mText = 'text-secondary';
                                            } elseif (str_contains($mAccion, 'AUTORIZ') || str_contains($mAccion, 'APROB')) {
                                                $mBg   = 'bg-emerald-500';
                                                $mIcon = 'check';
                                                $mText = 'text-emerald-600';
                                            } else {
                                                $mBg   = 'bg-amber-400';
                                                $mIcon = 'pending';
                                                $mText = 'text-amber-600';
                                            }
                                        ?>
                                        <div class="relative">
                                            <!-- Circle icon -->
                                            <div class="absolute -left-[33px] top-0.5 size-7 <?= $mBg ?> rounded-full flex items-center justify-center z-10 ring-4 ring-white dark:ring-slate-900 shadow-sm">
                                                <span class="material-symbols-outlined text-[12px] text-white"><?= $mIcon ?></span>
                                            </div>

                                            <div class="space-y-0.5 pl-1">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="text-[10px] font-black uppercase <?= $mText ?>"><?= htmlspecialchars($mAccion) ?></span>
                                                    <span class="text-[9px] font-bold text-slate-400 shrink-0">
                                                        <?= $isFirst ? '<span class="text-primary font-black">Ahora</span>' : date('d M y, h:i A', strtotime($mov['fecha_movimiento'])) ?>
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-1 text-slate-400">
                                                    <span class="material-symbols-outlined text-[11px]">location_on</span>
                                                    <span class="text-[9px] font-bold uppercase tracking-tight">
                                                        <?= htmlspecialchars($mov['nombre_usuario']) ?><?= !empty($mov['departamento']) ? ' — ' . htmlspecialchars($mov['departamento']) : '' ?>
                                                    </span>
                                                </div>
                                                <?php if (!empty($mov['detalles'])): ?>
                                                <p class="text-[9px] text-slate-400 italic leading-relaxed line-clamp-2 mt-0.5"><?= htmlspecialchars($mov['detalles']) ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- RIGHT: Datos del expediente -->
                                <div class="space-y-4">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[14px] text-primary">folder_open</span>
                                        Datos del Expediente
                                    </p>

                                    <div class="grid grid-cols-2 gap-x-6 gap-y-4">
                                        <div>
                                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Expediente / ID</p>
                                            <p class="text-[13px] font-black text-slate-800 dark:text-white"><?= htmlspecialchars($doc['documento_id']) ?></p>
                                        </div>
                                        <div>
                                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Total Movimientos</p>
                                            <p class="text-[13px] font-black text-primary"><?= count($movs) ?></p>
                                        </div>
                                        <div>
                                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Solicitante</p>
                                            <p class="text-[11px] font-bold text-slate-700 dark:text-white uppercase"><?= htmlspecialchars($doc['nombre_usuario']) ?></p>
                                        </div>
                                        <div>
                                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Departamento</p>
                                            <p class="text-[11px] font-bold text-slate-700 dark:text-white uppercase"><?= htmlspecialchars($doc['departamento'] ?: 'N/D') ?></p>
                                        </div>
                                        <div>
                                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Fecha Inicio</p>
                                            <p class="text-[11px] font-bold text-slate-700 dark:text-white uppercase"><?= date('d M Y, h:i A', strtotime($primero['fecha_movimiento'])) ?></p>
                                        </div>
                                        <div>
                                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Último Movimiento</p>
                                            <p class="text-[11px] font-bold text-slate-700 dark:text-white uppercase"><?= $esFinalizado ? date('d M Y, h:i A', strtotime($doc['ultima_fecha'])) : 'En proceso' ?></p>
                                        </div>
                                    </div>

                                    <!-- Progress bar (tiempo) -->
                                    <div class="pt-2 space-y-1.5">
                                        <div class="flex justify-between items-center">
                                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Tiempo en Proceso</p>
                                            <p class="text-[11px] font-black text-primary"><?= $tiempoLabel ?></p>
                                        </div>
                                        <div class="h-1.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full <?= $esFinalizado ? 'bg-emerald-500' : 'bg-primary' ?>" style="width: <?= min(100, count($movs) * 25) ?>%"></div>
                                        </div>
                                        <?php if (!$esFinalizado): ?>
                                        <p class="text-[9px] text-primary font-black flex items-center gap-1">
                                            <span class="size-1.5 bg-primary rounded-full animate-pulse inline-block"></span>
                                            Expediente activo en proceso
                                        </p>
                                        <?php else: ?>
                                        <p class="text-[9px] text-emerald-600 font-black">Proceso finalizado</p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Action button -->
                                    <div class="pt-2">
                                        <a href="/documentos/ver/<?= htmlspecialchars($doc['documento_id']) ?>"
                                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-primary/90 transition-all shadow-sm shadow-primary/20">
                                            <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                                            Ver Expediente
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Pagination -->
                <?php if (isset($totalPaginas) && $totalPaginas > 1): ?>
                <div class="flex justify-between items-center pt-6 pb-6 border-t border-slate-50 dark:border-slate-900 px-4 mt-8">
                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">Mostrando trazas recientes (<?= count($trazas) ?>)</p>
                    <div class="flex items-center gap-1">
                        <?php if ($pagina > 1): ?>
                            <a href="?p=<?= $pagina - 1 ?>" class="size-8 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-800 rounded-lg flex items-center justify-center text-slate-400 hover:text-primary hover:border-primary/20 transition-all shadow-sm">
                                <span class="material-symbols-outlined text-sm">chevron_left</span>
                            </a>
                        <?php endif; ?>

                        <?php 
                        // Simplified paginator for many pages
                        $startP = max(1, $pagina - 2);
                        $endP = min($totalPaginas, $pagina + 2);
                        for($i = $startP; $i <= $endP; $i++): 
                        ?>
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
        </div>

        <!-- Footer -->
        <footer class="h-10 px-10 bg-slate-50 dark:bg-slate-900 border-t border-slate-200/50 dark:border-slate-800 flex items-center justify-center shrink-0">
            <p class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.4em]">sgdoc Trace-Ability • <?= date('Y') ?></p>
        </footer>
    </main>

    <script>
        let currentFilter = 'todos';

        function toggleCard(idx) {
            const body    = document.getElementById('card-body-' + idx);
            const chevron = document.getElementById('chevron-' + idx);
            body.classList.toggle('open');
            chevron.classList.toggle('open');
        }

        function setFiltroEstado(estado) {
            currentFilter = estado;

            // Update button styles
            document.querySelectorAll('.filtro-btn').forEach(btn => {
                btn.classList.remove('bg-primary', 'text-white', 'border-primary');
                btn.classList.add('bg-white', 'dark:bg-slate-800', 'text-slate-500', 'border-slate-200', 'dark:border-slate-700');
            });

            const activeBtn = estado === 'todos' ? 'btn-todos' : estado === 'activo' ? 'btn-activos' : 'btn-finalizados';
            const btn = document.getElementById(activeBtn);
            btn.classList.add('bg-primary', 'text-white', 'border-primary');
            btn.classList.remove('bg-white', 'dark:bg-slate-800', 'text-slate-500', 'border-slate-200', 'dark:border-slate-700');

            filtrarTarjetas(document.getElementById('buscador').value);
        }

        function filtrarTarjetas(texto) {
            const q = texto.toLowerCase().trim();
            const cards = document.querySelectorAll('.doc-card');
            let visible = 0;
            cards.forEach(card => {
                const id   = card.dataset.id.toLowerCase();
                const desc = card.dataset.desc;
                const user = card.dataset.user;
                const est  = card.dataset.estado;

                const matchText  = !q || id.includes(q) || desc.includes(q) || user.includes(q);
                const matchState = currentFilter === 'todos' || est === currentFilter;

                if (matchText && matchState) {
                    card.style.display = '';
                    visible++;
                } else {
                    card.style.display = 'none';
                }
            });
            document.getElementById('contador-mostrados').textContent = visible;
        }
    </script>
</body>
</html>
