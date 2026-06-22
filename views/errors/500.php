<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Error Interno - sgdoc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full text-center space-y-8">
        <div class="relative">
            <h1 class="text-[150px] font-black text-slate-200 leading-none">500</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="size-20 bg-secondary/10 rounded-full flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined text-5xl">warning</span>
                </div>
            </div>
        </div>
        <div class="space-y-4">
            <h2 class="text-3xl font-extrabold text-slate-800">Error en el Servidor</h2>
            <p class="text-slate-500 font-medium">Ha ocurrido un problema interno intentando procesar tu solicitud. El equipo técnico ha sido notificado.</p>
        </div>
        <div class="flex flex-col gap-3">
            <a href="/dashboard" class="bg-primary hover:bg-[#005f6b] text-white py-4 rounded-2xl font-bold uppercase text-[11px] tracking-widest shadow-xl shadow-teal-900/20 transition-all">
                Volver al Inicio
            </a>
        </div>
    </div>
</body>
</html>
