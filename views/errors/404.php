<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada - sgdoc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full text-center space-y-8">
        <div class="relative">
            <h1 class="text-[150px] font-black text-slate-200 leading-none">404</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-6xl">find_in_page</span>
            </div>
        </div>
        <div class="space-y-4">
            <h2 class="text-3xl font-extrabold text-slate-800">¿Te has perdido?</h2>
            <p class="text-slate-500 font-medium">La página que buscas no existe o ha sido movida. Verifica la URL o vuelve al inicio.</p>
        </div>
        <div class="flex flex-col gap-3">
            <a href="/dashboard" class="bg-primary hover:bg-[#005f6b] text-white py-4 rounded-2xl font-bold uppercase text-[11px] tracking-widest shadow-xl shadow-teal-900/20 transition-all">
                Volver al Panel Principal
            </a>
            <button onclick="history.back()" class="text-slate-400 hover:text-slate-600 font-bold text-[10px] uppercase tracking-widest">
                Regresar a la página anterior
            </button>
        </div>
    </div>
</body>
</html>
