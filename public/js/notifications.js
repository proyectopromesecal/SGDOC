/**
 * SIGEDOC - Sistema de Notificaciones
 * - Polling automático cada 30 seg al endpoint PHP
 * - Notificaciones nativas de Windows (Web Notifications API)
 * - Widget de campana con badge de no leídas
 */

const SIGEDOC_NOTIF = (() => {
    let ultimasCargadas   = [];
    let idsYaNotificados  = new Set(JSON.parse(localStorage.getItem('sg_notif_vistas') || '[]'));
    let intervalId        = null;
    const POLL_INTERVAL   = 30000; // 30 segundos

    // ─── Permisos de Notificación Windows ────────────────────────────────────
    async function pedirPermiso() {
        if (!('Notification' in window)) return false;
        if (Notification.permission === 'granted') return true;
        if (Notification.permission === 'denied')  return false;
        const result = await Notification.requestPermission();
        return result === 'granted';
    }

    // ─── Mostrar notificación nativa Windows ──────────────────────────────────
    function mostrarNotifWindows(titulo, mensaje, link) {
        if (Notification.permission !== 'granted') return;
        const notif = new Notification('SIGEDOC — ' + titulo, {
            body: mensaje,
            icon: '/images/favicon/favicon-32x32.png',
            badge: '/images/favicon/favicon-32x32.png',
            tag: 'sigedoc-notif',
            requireInteraction: false,
            silent: false,
        });
        notif.onclick = () => {
            window.focus();
            if (link) window.location.href = link;
            notif.close();
        };
    }

    // ─── Renderizar widget HTML ───────────────────────────────────────────────
    function renderizarWidget(notificaciones, noLeidas) {
        const badge      = document.getElementById('notif-badge');
        const contador   = document.getElementById('notif-count');
        const lista      = document.getElementById('notif-lista');

        // Badge
        if (badge && contador) {
            if (noLeidas > 0) {
                badge.classList.remove('hidden');
                contador.textContent = noLeidas > 9 ? '9+' : noLeidas;
            } else {
                badge.classList.add('hidden');
            }
        }

        // Lista
        if (!lista) return;
        if (notificaciones.length === 0) {
            lista.innerHTML = `
                <div class="py-10 flex flex-col items-center gap-3 text-center">
                    <span class="material-symbols-outlined text-3xl text-slate-200">notifications_off</span>
                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Sin notificaciones nuevas</p>
                </div>`;
            return;
        }

        lista.innerHTML = notificaciones.map(n => {
            const leida = n.leida == 1 || n.leida === true;
            return `
            <div class="flex items-start gap-3 px-5 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer rounded-2xl group ${leida ? 'opacity-60' : ''}"
                 onclick="SIGEDOC_NOTIF.abrirNotif('${escHtml(n.id)}', '${escHtml(n.link || '')}')">
                <div class="shrink-0 size-8 rounded-xl flex items-center justify-center ${leida ? 'bg-slate-100 dark:bg-slate-800' : 'bg-primary/10 dark:bg-primary/20'}">
                    <span class="material-symbols-outlined text-[15px] ${leida ? 'text-slate-300' : 'text-primary'}">notifications</span>
                </div>
                <div class="flex-1 min-w-0 space-y-0.5">
                    <p class="text-[10px] font-black uppercase text-slate-700 dark:text-slate-200 flex items-center gap-2">
                        ${escHtml(n.titulo)}
                        ${!leida ? '<span class="size-1.5 bg-primary rounded-full inline-block shrink-0 animate-pulse"></span>' : ''}
                    </p>
                    <p class="text-[9px] text-slate-400 font-medium leading-relaxed line-clamp-2">${escHtml(n.mensaje)}</p>
                    <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest">${escHtml(n.tiempo_transcurrido || '')}</p>
                </div>
            </div>`;
        }).join('');
    }

    // ─── Llamada al backend ───────────────────────────────────────────────────
    async function fetchNotificaciones() {
        try {
            const resp = await fetch('/notificaciones/obtenerRecientes', {
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!resp.ok) return;
            const data = await resp.json();
            if (!data.success) return;

            const nuevas = data.notificaciones || [];
            const noLeidas = data.no_leidas || 0;

            // Detectar notificaciones realmente nuevas (no leídas y no ya alertadas)
            nuevas.forEach(n => {
                if (!n.leida && !idsYaNotificados.has(String(n.id))) {
                    mostrarNotifWindows(n.titulo, n.mensaje, n.link);
                    idsYaNotificados.add(String(n.id));
                }
            });

            // Persistir IDs vistos
            localStorage.setItem('sg_notif_vistas', JSON.stringify([...idsYaNotificados]));

            ultimasCargadas = nuevas;
            renderizarWidget(nuevas, noLeidas);
        } catch (e) {
            console.warn('[SIGEDOC Notif] Error al obtener notificaciones:', e);
        }
    }

    // ─── Abrir notificación ───────────────────────────────────────────────────
    function abrirNotif(id, link) {
        fetch(`/notificaciones/marcarLeida/${id}`, { credentials: 'same-origin' }).catch(() => {});
        if (link && link !== 'undefined') window.location.href = link;
        fetchNotificaciones();
    }

    // ─── Marcar todas leídas ──────────────────────────────────────────────────
    function marcarTodas() {
        fetch('/notificaciones/marcarTodas', { credentials: 'same-origin' })
            .then(() => fetchNotificaciones())
            .catch(() => {});
    }

    // ─── Toggle panel ─────────────────────────────────────────────────────────
    function toggle() {
        const panel = document.getElementById('notif-panel');
        if (!panel) return;
        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
            fetchNotificaciones();
        } else {
            panel.classList.add('hidden');
        }
    }

    // ─── Cerrar al hacer clic fuera ───────────────────────────────────────────
    document.addEventListener('click', e => {
        const panel   = document.getElementById('notif-panel');
        const trigger = document.getElementById('notif-trigger');
        if (!panel || !trigger) return;
        if (!panel.contains(e.target) && !trigger.contains(e.target)) {
            panel.classList.add('hidden');
        }
    });

    // ─── Inicializar ──────────────────────────────────────────────────────────
    async function init() {
        await pedirPermiso();
        fetchNotificaciones();
        intervalId = setInterval(fetchNotificaciones, POLL_INTERVAL);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────
    function escHtml(s) {
        if (!s) return '';
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    return { init, toggle, marcarTodas, abrirNotif, fetchNotificaciones };
})();

document.addEventListener('DOMContentLoaded', () => SIGEDOC_NOTIF.init());
