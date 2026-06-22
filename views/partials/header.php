<header class="main-header">
    <div class="header-left">
        <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
        <img src="/images/logo-promese.png" alt="PROMESE/CAL" class="header-logo" onerror="this.style.display='none'">
        <h1 class="header-title">sgdoc</h1>
    </div>
    <div class="header-right">
        <span class="user-info">
            <strong><?= htmlspecialchars($_SESSION['usuario']) ?></strong>
            <small>(<?= htmlspecialchars($_SESSION['rol_nombre']) ?>)</small>
        </span>
        <a href="/logout" class="btn btn-sm btn-danger">Cerrar Sesión</a>
    </div>
</header>
