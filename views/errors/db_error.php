<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicio No Disponible - sgdoc</title>
    <!--
        NOTA: Esta vista es INTENCIONALMENTE autocontenida.
        No carga ningún recurso externo (CDN, fonts, etc.)
        para garantizar que funcione aunque no haya conexión a Internet.
    -->
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: #1e293b;
        }

        .wrapper { max-width: 460px; width: 100%; }

        /* ── Card ─────────────────────────────── */
        .card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,.10), 0 4px 16px rgba(0,0,0,.06);
            padding: 48px 40px;
            text-align: center;
        }

        /* ── Icono animado ─────────────────────── */
        @keyframes pulse-ring {
            0%   { transform: scale(.95); box-shadow: 0 0 0 0 rgba(228, 30, 38, .35); }
            70%  { transform: scale(1);   box-shadow: 0 0 0 18px rgba(228, 30, 38, 0); }
            100% { transform: scale(.95); box-shadow: 0 0 0 0 rgba(228, 30, 38, 0); }
        }

        .icon-ring {
            width: 96px; height: 96px;
            background: #fff1f2;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 28px;
            animation: pulse-ring 2s infinite;
        }

        .icon-ring svg { width: 48px; height: 48px; }

        /* ── Badge ────────────────────────────── */
        .badge {
            display: inline-block;
            background: #fee2e2;
            color: #dc2626;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
            padding: 5px 16px;
            border-radius: 999px;
            margin-bottom: 14px;
        }

        /* ── Textos ───────────────────────────── */
        h1 {
            font-size: 26px;
            font-weight: 800;
            line-height: 1.25;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .subtitle {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        /* ── Divider ──────────────────────────── */
        .divider { border: none; border-top: 1px solid #f1f5f9; margin-bottom: 24px; }

        /* ── Info box ─────────────────────────── */
        .info-box {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            text-align: left;
            margin-bottom: 28px;
        }

        .info-box .label {
            font-size: 10px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: 12px;
        }

        .info-box ul { list-style: none; display: flex; flex-direction: column; gap: 10px; }

        .info-box li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            color: #475569;
            line-height: 1.5;
        }

        .info-box li svg { flex-shrink: 0; width: 18px; height: 18px; margin-top: 1px; color: #0d9488; }

        /* ── Barra de progreso ────────────────── */
        @keyframes countdown { from { width: 100%; } to { width: 0%; } }

        .progress-wrap { margin-bottom: 28px; }

        .progress-track {
            background: #e2e8f0;
            border-radius: 999px;
            height: 4px;
            overflow: hidden;
            margin-bottom: 6px;
        }

        .progress-bar {
            height: 4px;
            border-radius: 999px;
            background: linear-gradient(90deg, #0d9488, #14b8a6);
            animation: countdown 30s linear forwards;
        }

        .progress-label { font-size: 11px; color: #94a3b8; text-align: center; }
        .progress-label span { font-weight: 700; }

        /* ── Botones ──────────────────────────── */
        .btn-row { display: flex; gap: 12px; flex-wrap: wrap; }

        .btn {
            flex: 1;
            min-width: 120px;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 14px 20px;
            border-radius: 14px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: transform .15s, background .15s, box-shadow .15s;
        }

        .btn:hover { transform: translateY(-1px); }

        .btn-primary {
            background: #007281;
            color: #ffffff;
            box-shadow: 0 6px 20px rgba(0,114,129,.25);
        }
        .btn-primary:hover { background: #005f6b; box-shadow: 0 8px 24px rgba(0,114,129,.35); }

        .btn-secondary { background: #f1f5f9; color: #475569; }
        .btn-secondary:hover { background: #e2e8f0; }

        .btn svg { width: 16px; height: 16px; }

        /* ── Footer ───────────────────────────── */
        .footer { text-align: center; color: #94a3b8; font-size: 11px; margin-top: 20px; }
    </style>
    <script>
        // Auto-reload en 30 segundos
        setTimeout(() => window.location.reload(), 30000);
    </script>
</head>
<body>
    <div class="wrapper">

        <div class="card">

            <!-- Icono -->
            <div class="icon-ring">
                <!-- database_off icon (inline SVG — sin dependencia de Material Symbols CDN) -->
                <svg viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <ellipse cx="12" cy="5" rx="9" ry="3"/>
                    <path d="M3 5v4c0 1.657 4.03 3 9 3s9-1.343 9-3V5"/>
                    <path d="M3 9v4c0 1.306 2.837 2.417 6.8 2.858"/>
                    <path d="M21 9v1"/>
                    <line x1="3" y1="3" x2="21" y2="21"/>
                    <path d="M16.72 16.72A9.1 9.1 0 0 0 21 15v-2"/>
                </svg>
            </div>

            <!-- Badge -->
            <div class="badge">Servicio No Disponible</div>

            <!-- Título -->
            <h1>No se pudo conectar<br>con la base de datos</h1>

            <p class="subtitle">
                El sistema está experimentando dificultades para conectarse al
                servidor de base de datos. El equipo técnico ha sido notificado
                automáticamente.
            </p>

            <hr class="divider">

            <!-- Qué hacer -->
            <div class="info-box">
                <p class="label">¿Qué puedes hacer?</p>
                <ul>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                        <span>Esta página se recargará automáticamente en <strong id="countdown">30</strong> segundos.</span>
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>Si el problema persiste, intenta acceder nuevamente en unos minutos.</span>
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.15 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.12 1.18h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.09 8.91a16 16 0 0 0 5.85 5.85l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7 2 2 0 0 1 1.72 2.01z"/></svg>
                        <span>Contacta a soporte técnico si el error no se resuelve.</span>
                    </li>
                </ul>
            </div>

            <!-- Barra de progreso -->
            <div class="progress-wrap">
                <div class="progress-track">
                    <div class="progress-bar" id="progressBar"></div>
                </div>
                <p class="progress-label">Recargando en <span id="countdown2">30</span>s&hellip;</p>
            </div>

            <!-- Botones -->
            <div class="btn-row">
                <button class="btn btn-primary" onclick="window.location.reload()" id="btn-retry">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    Reintentar ahora
                </button>
                <a href="mailto:mesadeayuda@promesecal.gob.do" class="btn btn-secondary" id="btn-support">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    Soporte
                </a>
            </div>
        </div>

        <!-- Footer -->
        <p class="footer">
            &copy; <?= date('Y') ?> sgdoc &mdash; Sistema Integrado de Gestión Documental
        </p>
    </div>

    <script>
        // Contador regresivo en ambas etiquetas
        let seconds = 30;
        const el1 = document.getElementById('countdown');
        const el2 = document.getElementById('countdown2');

        const interval = setInterval(() => {
            seconds--;
            if (el1) el1.textContent = seconds;
            if (el2) el2.textContent = seconds;
            if (seconds <= 0) clearInterval(interval);
        }, 1000);
    </script>
</body>
</html>
