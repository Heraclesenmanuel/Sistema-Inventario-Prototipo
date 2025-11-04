<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de <?= APP_NAME ?? 'Inicio' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
</head>

<body>
    <div class="dashboard">
        <?=  require_once 'views/inc/heder.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de <?= APP_NAME ?? 'Inicio' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
</head>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$nombre = "Invitado";
if (!empty($_SESSION['nombre']) && is_string($_SESSION['nombre'])) {
    $nombre = htmlspecialchars(trim($_SESSION['nombre']), ENT_QUOTES, 'UTF-8');
}
$es_admin = isset($_SESSION['id_cargo']) && $_SESSION['id_cargo'] == 1;
?>
<body>
    
    <main class="main-content">
            <div class="welcome-image-container">
                <img src="./public/img/graduados.jpg" alt="Bienvenida" class="welcome-image">
                <div class="welcome-text">
                    <h4>Bienvenid@, </h4> <h4><?php echo $nombre; ?></h4>
                    <p>Al Sistema de Gestión </p>
                    <p>Administrativa</p>
                </div>
            </div>
        
        <div class="quick-access-container">
            <h2 class="quick-access-title">Accesos Rápidos</h2>
            <div class="quick-access-grid">
                <!-- Fila 1 -->
                <a href="?action=inventario&method=home" class="quick-access-item">
                    <i class="fas fa-warehouse"></i>
                    <h3>Inventario</h3>
                </a>
                
                <a href="?action=proveedor&method=home" class="quick-access-item">
                    <i class="fas fa-truck"></i>
                    <h3>Proveedores</h3>

                <a href="?action=cliente&method=users" class="quick-access-item">
                    <i class="fas fa-users"></i>
                    <h3>Oficinas</h3>
                </a>

                <a href="?action=reporte&method=home" class="quick-access-item">
                    <i class="fas fa-chart-bar"></i>
                    <h3>Estadísticas</h3>

                <a href="?action=solicitudes&method=home" class="quick-access-item">
                    <i class="	fas fa-receipt"></i>
                    <h3>Solicitudes</h3>
                </a>

                <a href="?action=algo&method=users" class="quick-access-item">
                    <i class="fas fa-question"></i>
                    <h3>Algo mas</h3>
                </a>
                
            </div>
        </div> <br><br>
        <footer>
    </footer>
    </main>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/menu.js"></script>

</body>
</html>