<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Acceso | sgdoc</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #007281;
            --secondary: #E41E26;
            --dark: #1a1a1a;
            --light: #f4f7f6;
            --glass: rgba(255, 255, 255, 0.85);
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #007281 0%, #004d57 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
            position: relative;
        }

        /* Background animated elements */
        body::before, body::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            z-index: 0;
            animation: float 15s infinite alternate ease-in-out;
        }

        body::before {
            top: -50px;
            left: -50px;
        }

        body::after {
            bottom: -50px;
            right: -50px;
            animation-delay: -5s;
        }

        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            100% { transform: translateY(50px) rotate(20deg); }
        }

        .container {
            width: 100%;
            max-width: 550px;
            position: relative;
            z-index: 1;
        }

        .card {
            background: var(--glass);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            text-align: center;
            transform: translateY(0);
            transition: transform 0.3s ease;
        }

        .logo {
            width: 120px;
            margin-bottom: 30px;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        }

        h1 {
            color: var(--primary);
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
            letter-spacing: -0.5px;
        }

        p.subtitle {
            color: #555;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .user-info {
            background: rgba(0, 114, 129, 0.05);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            text-align: left;
            border: 1px solid rgba(0, 114, 129, 0.1);
        }

        .avatar {
            width: 50px;
            height: 50px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
        }

        .user-details h3 {
            font-size: 16px;
            color: var(--dark);
            margin-bottom: 2px;
        }

        .user-details span {
            font-size: 13px;
            color: #666;
            display: block;
        }

        .form-group {
            text-align: left;
            margin-bottom: 25px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
            margin-left: 4px;
        }

        textarea {
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            border: 2px solid rgba(0, 0, 0, 0.05);
            background: rgba(255, 255, 255, 0.5);
            font-size: 15px;
            transition: all 0.3s ease;
            resize: none;
            min-height: 120px;
        }

        textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(0, 114, 129, 0.1);
        }

        .btn {
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            border: none;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 10px 20px -5px rgba(0, 114, 129, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(0, 114, 129, 0.5);
            background: #008a9c;
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .success-msg {
            background: #d1fae5;
            color: #065f46;
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 20px;
            border: 1px solid #a7f3d0;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logout-link {
            display: inline-block;
            margin-top: 25px;
            color: #666;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .logout-link:hover {
            color: var(--secondary);
        }

        .version {
            position: absolute;
            bottom: -40px;
            left: 0;
            right: 0;
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <!-- Usamos un placeholder con estilo si no hay logo disponible -->
            <div style="margin-bottom: 20px;">
                <span style="font-size: 40px; color: var(--primary); font-weight: 800;">sgdoc</span>
            </div>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="success-msg">
                    <i class="fas fa-check-circle fa-2x" style="margin-bottom: 10px; display: block;"></i>
                    <p><?php echo $_SESSION['success']; ?></p>
                    <?php unset($_SESSION['success']); ?>
                </div>
                <a href="/logout" class="btn btn-primary">
                    <i class="fas fa-sign-out-alt"></i> Salir del Sistema
                </a>
            <?php else: ?>
                <h1>¡Bienvenido a sgdoc!</h1>
                <p class="subtitle">Detectamos que es tu primera vez en el sistema. Para empezar, por favor envía una solicitud de acceso a Mesa de Ayuda.</p>

                <div class="user-info">
                    <div class="avatar">
                        <?php echo substr($user['nombre'] ?? $user['usuario'], 0, 1); ?>
                    </div>
                    <div class="user-details">
                        <h3><?php echo htmlspecialchars($user['nombre'] ?? $user['usuario']); ?></h3>
                        <span><?php echo htmlspecialchars($user['departamento'] ?? 'Departamento no asignado'); ?></span>
                        <span style="font-size: 11px; opacity: 0.7;"><?php echo htmlspecialchars($user['cargo'] ?? 'Cargo no asignado'); ?></span>
                    </div>
                </div>

                <form action="/solicitud-acceso" method="POST" id="accessForm">
                    <?= \App\Core\Security::csrfInput() ?>
                    <div class="form-group">
                        <textarea name="motivo" id="motivo" placeholder="Ej: Requiero acceso para registrar facturas de proveedores y gestionar los documentos de mi departamento." required></textarea>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Enviar Solicitud (Abrir Correo)
                    </button>
                </form>

                <script>
                    document.getElementById('accessForm').addEventListener('submit', function(e) {
                        e.preventDefault(); 
                        
                        const motivo = document.getElementById('motivo').value;
                        const nombre = "<?php echo addslashes($user['nombre'] ?? $user['usuario']); ?>";
                        const depto = "<?php echo addslashes($user['departamento'] ?? 'N/A'); ?>";
                        
                        const subject = encodeURIComponent("Solicitud de Acceso sgdoc - " + nombre);
                        const body = encodeURIComponent(
                            "Estimados Mesa de Ayuda,\n\n" +
                            "Solicito acceso al sistema sgdoc.\n\n" +
                            "Datos del Solicitante:\n" +
                            "- Nombre: " + nombre + "\n" +
                            "- Departamento: " + depto + "\n\n" +
                            "Justificación:\n" +
                            motivo + "\n\n" +
                            "Quedo a la espera de su respuesta."
                        );
                        
                        const targetMail = "<?php echo $_ENV['MAIL_HELP_DESK'] ?? 'mesadeayuda@promesecal.gob.do'; ?>";
                        const mailtoUrl = `mailto:${targetMail}?subject=${subject}&body=${body}`;
                        
                        // Abrir el cliente de correo
                        window.location.href = mailtoUrl;
                        
                        // Enviar el formulario al servidor para registro en bitácora tras un breve delay
                        const form = this;
                        setTimeout(function() {
                            form.submit();
                        }, 1000);
                    });
                </script>

                <a href="/logout" class="logout-link">
                    <i class="fas fa-arrow-left"></i> Volver al Login
                </a>
            <?php endif; ?>
        </div>
        <div class="version">
            © <?php echo date('Y'); ?> PROMESE/CAL - Sistema Integrado de Gestión Documental
        </div>
    </div>
</body>
</html>
