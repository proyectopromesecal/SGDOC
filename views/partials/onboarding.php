<?php
/**
 * sgdoc - Onboarding Modal para Nuevos Usuarios
 * Se muestra una sola vez, controlado por cookie persistente y sesión PHP.
 */
$userId = $_SESSION['usuario_id'] ?? 'guest';
$cookieName = 'sgdoc_onboarded_' . $userId;

if (empty($_COOKIE[$cookieName]) && (!isset($_SESSION['onboarding_completado']) || $_SESSION['onboarding_completado'] !== true)):
?>
<style>
    #sgdoc-onboarding {
        animation: fadeInOverlay 0.4s ease;
    }
    @keyframes fadeInOverlay {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
    #onboarding-card {
        animation: slideUpCard 0.45s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes slideUpCard {
        from { opacity: 0; transform: translateY(32px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .step-panel { display: none; }
    .step-panel.active { display: flex; }
    .onboarding-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #cbd5e1; transition: all 0.3s ease;
    }
    .onboarding-dot.active {
        width: 24px; border-radius: 4px; background: #007281;
    }
    .onboarding-icon-wrap {
        width: 80px; height: 80px; border-radius: 2rem;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.5rem;
    }
</style>

<div id="sgdoc-onboarding" class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div id="onboarding-card" class="bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 w-full max-w-lg overflow-hidden">

        <!-- Top bar: color primario -->
        <div class="h-1.5 bg-gradient-to-r from-[#007281] to-[#009db3]"></div>

        <!-- Cabecera con logo + nombre -->
        <div class="flex items-center justify-between px-8 pt-7 pb-4 border-b border-slate-50">
            <div class="flex items-center gap-3">
                <img src="/images/logo.png" alt="PROMESE/CAL" class="h-7 w-auto object-contain opacity-80">
                <span class="text-[10px] font-black text-[#007281] uppercase tracking-[0.35em]">sgdoc</span>
            </div>
            <button onclick="sgdocOnboarding.skip()" class="text-slate-300 hover:text-slate-500 transition-colors text-[10px] font-bold uppercase tracking-widest flex items-center gap-1">
                Omitir
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>

        <!-- Pasos -->
        <div class="px-10 py-8 min-h-[340px] flex flex-col">

            <!-- PASO 1: Bienvenida -->
            <div class="step-panel active flex-col items-center text-center flex-1" data-step="1">
                <div class="onboarding-icon-wrap bg-[#007281]/10">
                    <span class="material-symbols-outlined text-4xl text-[#007281]">waving_hand</span>
                </div>
                <p class="text-[10px] font-black text-[#007281] uppercase tracking-[0.35em] mb-2">Paso 01 de 04</p>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight mb-3">¡Bienvenido a sgdoc!</h2>
                <p class="text-sm text-slate-500 font-medium leading-relaxed max-w-sm">
                    El sistema centralizado de <strong>gestión, firma y aprobación</strong> de documentos institucionales de <strong>PROMESE/CAL</strong>. Este tutorial rápido te mostrará lo esencial en menos de 2 minutos.
                </p>
            </div>

            <!-- PASO 2: Crear solicitud -->
            <div class="step-panel flex-col items-center text-center flex-1" data-step="2">
                <div class="onboarding-icon-wrap bg-emerald-50">
                    <span class="material-symbols-outlined text-4xl text-emerald-500">note_add</span>
                </div>
                <p class="text-[10px] font-black text-[#007281] uppercase tracking-[0.35em] mb-2">Paso 02 de 04</p>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight mb-3">Crea una Solicitud</h2>
                <p class="text-sm text-slate-500 font-medium leading-relaxed max-w-sm">
                    Desde el botón <span class="inline-flex items-center gap-1 bg-[#007281] text-white text-[10px] font-black px-2 py-0.5 rounded-full"><span class="material-symbols-outlined text-xs">add_circle</span> Nueva Solicitud</span> podrás registrar un documento, adjuntar archivos y asignarle prioridad antes de enviarlo al flujo de aprobación.
                </p>
            </div>

            <!-- PASO 3: Flujo de aprobación -->
            <div class="step-panel flex-col items-center text-center flex-1" data-step="3">
                <div class="onboarding-icon-wrap bg-amber-50">
                    <span class="material-symbols-outlined text-4xl text-amber-500">verified</span>
                </div>
                <p class="text-[10px] font-black text-[#007281] uppercase tracking-[0.35em] mb-2">Paso 03 de 04</p>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight mb-3">Flujo de Aprobación</h2>
                <p class="text-sm text-slate-500 font-medium leading-relaxed max-w-sm">
                    Cada documento pasa por un flujo de <strong>4 estados</strong>: <em>Solicitado → Por Aprobar → Autorizado → Digitalizado</em>. Puedes ver el historial completo de cada expediente en tiempo real.
                </p>
                <!-- Mini estados visuales -->
                <div class="flex items-center gap-1.5 mt-4 flex-wrap justify-center">
                    <span class="text-[9px] font-black uppercase px-2.5 py-1 rounded-full bg-blue-50 text-blue-500">Solicitado</span>
                    <span class="material-symbols-outlined text-slate-300 text-sm">arrow_forward</span>
                    <span class="text-[9px] font-black uppercase px-2.5 py-1 rounded-full bg-amber-50 text-amber-500">Por Aprobar</span>
                    <span class="material-symbols-outlined text-slate-300 text-sm">arrow_forward</span>
                    <span class="text-[9px] font-black uppercase px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-500">Autorizado</span>
                </div>
            </div>

            <!-- PASO 4: Notificaciones -->
            <div class="step-panel flex-col items-center text-center flex-1" data-step="4">
                <div class="onboarding-icon-wrap bg-violet-50">
                    <span class="material-symbols-outlined text-4xl text-violet-500">notifications_active</span>
                </div>
                <p class="text-[10px] font-black text-[#007281] uppercase tracking-[0.35em] mb-2">Paso 04 de 04</p>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight mb-3">Mantente Informado</h2>
                <p class="text-sm text-slate-500 font-medium leading-relaxed max-w-sm">
                    Recibirás <strong>notificaciones en tiempo real</strong> cada vez que un documento cambie de estado. También puedes habilitarlas como alertas de escritorio en tu navegador para no perderte nada.
                </p>
                <div class="mt-4 p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center gap-3 w-full max-w-xs text-left">
                    <span class="material-symbols-outlined text-[#007281]">mail</span>
                    <div>
                        <p class="text-[10px] font-black text-slate-700 uppercase">¿Necesitas ayuda?</p>
                        <p class="text-[10px] text-slate-400">mesadeayuda@promesecal.gob.do</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer: dots + botones -->
        <div class="px-10 pb-8 flex items-center justify-between">
            <!-- Progress dots -->
            <div class="flex items-center gap-2" id="onboarding-dots">
                <div class="onboarding-dot active" data-dot="1"></div>
                <div class="onboarding-dot" data-dot="2"></div>
                <div class="onboarding-dot" data-dot="3"></div>
                <div class="onboarding-dot" data-dot="4"></div>
            </div>

            <!-- Botones -->
            <div class="flex items-center gap-3">
                <button id="btn-onboarding-prev" onclick="sgdocOnboarding.prev()" class="hidden px-5 py-2.5 rounded-full text-[11px] font-black uppercase tracking-widest border border-slate-200 text-slate-500 hover:border-[#007281] hover:text-[#007281] transition-all">
                    ← Atrás
                </button>
                <button id="btn-onboarding-next" onclick="sgdocOnboarding.next()" class="px-6 py-2.5 bg-[#007281] hover:bg-[#005f6b] text-white rounded-full text-[11px] font-black uppercase tracking-widest shadow-lg shadow-teal-900/20 transition-all flex items-center gap-2">
                    Siguiente <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
const sgdocOnboarding = (() => {
    let current = 1;
    const total = 4;

    function goTo(step) {
        // Paneles
        document.querySelectorAll('.step-panel').forEach(p => p.classList.remove('active'));
        document.querySelector(`.step-panel[data-step="${step}"]`).classList.add('active');

        // Dots
        document.querySelectorAll('.onboarding-dot').forEach(d => d.classList.remove('active'));
        document.querySelector(`.onboarding-dot[data-dot="${step}"]`).classList.add('active');

        // Botón Atrás
        document.getElementById('btn-onboarding-prev').classList.toggle('hidden', step === 1);

        // Botón Siguiente / Empezar
        const btnNext = document.getElementById('btn-onboarding-next');
        if (step === total) {
            btnNext.innerHTML = '¡Empezar! <span class="material-symbols-outlined text-sm">check_circle</span>';
            btnNext.onclick = () => sgdocOnboarding.finish();
        } else {
            btnNext.innerHTML = 'Siguiente <span class="material-symbols-outlined text-sm">arrow_forward</span>';
            btnNext.onclick = () => sgdocOnboarding.next();
        }

        current = step;
    }

    function finish() {
        const userId = "<?= $_SESSION['usuario_id'] ?? 'guest' ?>";
        const d = new Date();
        d.setTime(d.getTime() + (365*24*60*60*1000)); // 1 año
        document.cookie = "sgdoc_onboarded_" + userId + "=1; expires=" + d.toUTCString() + "; path=/";

        fetch('/onboarding/completar', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .catch(() => {}); // silencioso si falla
        close();
    }

    function close() {
        const el = document.getElementById('sgdoc-onboarding');
        el.style.animation = 'fadeInOverlay 0.3s ease reverse';
        setTimeout(() => el.remove(), 280);
    }

    return {
        next:   () => { if (current < total) goTo(current + 1); },
        prev:   () => { if (current > 1)     goTo(current - 1); },
        skip:   () => { finish(); },
        finish: () => { finish(); }
    };
})();
</script>

<?php endif; ?>
