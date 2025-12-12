<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?? 'Sistema' ?> - Notificaciones</title>
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
    
    <!-- Estilos -->
    <link rel="stylesheet" href="public/css/admin.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- CSS para Notificaciones -->
    <style>
        :root {
            --primary-color: #3F51B5;
            --primary-light: #757DE8;
            --danger-color: #F44336;
            --warning-color: #FF9800;
            --success-color: #4CAF50;
            --info-color: #2196F3;
            --gray-50: #FAFAFA;
            --gray-100: #F5F5F5;
            --gray-200: #EEEEEE;
            --gray-300: #E0E0E0;
            --gray-400: #BDBDBD;
            --gray-600: #757575;
            --gray-700: #616161;
            --gray-900: #212121;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.12);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 25px rgba(0,0,0,0.1);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            color: var(--gray-900);
            line-height: 1.6;
            display: flex;
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
            display: flex;
            flex-direction: column;
        }

        /* Header Principal */
        .main-header {
            background: white;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 100;
            width: 100%;
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .header-title h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .unread-badge {
            background: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .header-title p {
            color: var(--gray-600);
            font-size: 0.95rem;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        /* Filtros y Búsqueda */
        .controls-panel {
            background: white;
            padding: 1.5rem 2rem;
            margin: 1rem 2rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            width: calc(100% - 4rem);
        }

        .filters-grid {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 1.5rem;
            align-items: center;
        }

        .search-container {
            position: relative;
            flex: 1;
        }

        .search-input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-md);
            font-size: 1rem;
            transition: all 0.3s;
            background: var(--gray-50);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-light);
            background: white;
            box-shadow: 0 0 0 3px rgba(63, 81, 181, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
        }

        .filter-group {
            display: flex;
            gap: 0.75rem;
        }

        .filter-btn {
            padding: 0.75rem 1.25rem;
            border: 2px solid var(--gray-200);
            background: white;
            border-radius: var(--radius-md);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
        }

        .filter-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .filter-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .action-btn {
            padding: 0.875rem 1.5rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
        }

        .action-btn:hover {
            background: #303F9F;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Contenedor Principal */
        .notifications-container {
            padding: 0 2rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            flex: 1;
        }

        /* Estadísticas */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.3s;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon.unread {
            background: linear-gradient(135deg, #3F51B5, #757DE8);
            color: white;
        }

        .stat-icon.urgent {
            background: linear-gradient(135deg, #FF5252, #F44336);
            color: white;
        }

        .stat-icon.warning {
            background: linear-gradient(135deg, #FFB74D, #FF9800);
            color: white;
        }

        .stat-icon.info {
            background: linear-gradient(135deg, #64B5F6, #2196F3);
            color: white;
        }

        .stat-info h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stat-info p {
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        /* Lista de Notificaciones */
        .notifications-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .notification-item {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: all 0.3s;
            border-left: 4px solid transparent;
            position: relative;
        }

        .notification-item:hover {
            transform: translateX(5px);
            box-shadow: var(--shadow-lg);
        }

        .notification-item.unread {
            background: linear-gradient(90deg, #F0F4FF 0%, white 5%);
            border-left-color: var(--primary-color);
        }

        .notification-item.urgent {
            background: linear-gradient(90deg, #FFEBEE 0%, white 5%);
            border-left-color: var(--danger-color);
        }

        .notification-item.warning {
            background: linear-gradient(90deg, #FFF3E0 0%, white 5%);
            border-left-color: var(--warning-color);
        }

        .notification-item.info {
            background: linear-gradient(90deg, #E3F2FD 0%, white 5%);
            border-left-color: var(--info-color);
        }

        .notification-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .icon-unread {
            background: #F0F4FF;
            color: var(--primary-color);
        }

        .icon-urgent {
            background: #FFEBEE;
            color: var(--danger-color);
        }

        .icon-warning {
            background: #FFF3E0;
            color: var(--warning-color);
        }

        .icon-info {
            background: #E3F2FD;
            color: var(--info-color);
        }

        .notification-content {
            flex: 1;
            min-width: 0;
        }

        .notification-message {
            font-size: 1rem;
            margin-bottom: 0.5rem;
            color: var(--gray-900);
        }

        .notification-meta {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            color: var(--gray-600);
            font-size: 0.875rem;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .notification-recipients {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .recipient-badge {
            background: var(--gray-100);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            color: var(--gray-700);
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .recipient-count {
            background: var(--primary-color);
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .notification-actions {
            display: flex;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        .action-btn-small {
            padding: 0.5rem;
            border: none;
            border-radius: var(--radius-sm);
            background: transparent;
            color: var(--gray-600);
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .action-btn-small:hover {
            background: var(--gray-100);
            color: var(--gray-900);
        }

        .action-btn-small.mark-read:hover {
            color: var(--success-color);
        }

        .action-btn-small.delete:hover {
            color: var(--danger-color);
        }

        /* Badge de tipo de notificación */
        .type-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-urgent {
            background: #FFEBEE;
            color: var(--danger-color);
        }

        .badge-warning {
            background: #FFF3E0;
            color: var(--warning-color);
        }

        .badge-info {
            background: #E3F2FD;
            color: var(--info-color);
        }

        .badge-success {
            background: #E8F5E9;
            color: var(--success-color);
        }

        /* Estado vacío */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            margin-top: 2rem;
            width: 100%;
        }

        .empty-icon {
            font-size: 4rem;
            color: var(--gray-300);
            margin-bottom: 1.5rem;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
            color: var(--gray-700);
        }

        .empty-state p {
            color: var(--gray-600);
            max-width: 400px;
            margin: 0 auto 1.5rem;
        }

        /* Indicador de no leído */
        .unread-dot {
            position: absolute;
            right: -8px;
            top: -8px;
            width: 16px;
            height: 16px;
            background: var(--primary-color);
            border-radius: 50%;
            border: 3px solid white;
        }

        /* Fecha relativa */
        .relative-time {
            font-size: 0.75rem;
            color: var(--gray-400);
        }

        /* Modal de detalles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-overlay.active {
            display: flex;
            animation: fadeIn 0.3s ease-out;
        }

        .modal-content {
            background: white;
            border-radius: var(--radius-lg);
            padding: 2rem;
            max-width: 600px;
            width: 100%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: var(--shadow-lg);
            animation: slideUp 0.3s ease-out;
            margin-left: var(--sidebar-width);
        }

        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .notification-item {
            animation: fadeIn 0.4s ease-out;
        }

        /* Filtros activos */
        .filter-group .active {
            position: relative;
        }

        .filter-group .active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        /* Mensaje largo */
        .notification-message.truncate {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Lista de destinatarios */
        .recipients-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .recipient-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--gray-50);
            border-radius: var(--radius-sm);
        }

        .recipient-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .recipient-info {
            flex: 1;
        }

        .recipient-name {
            font-weight: 500;
            color: var(--gray-900);
        }

        .recipient-role {
            font-size: 0.75rem;
            color: var(--gray-600);
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .notifications-container {
                padding: 0 1rem 1rem;
            }
            
            .controls-panel {
                margin: 1rem;
                width: calc(100% - 2rem);
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .modal-content {
                margin-left: 0;
                max-width: 90%;
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
            }
            
            .main-content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
            
            .modal-content {
                margin-left: 70px;
            }
            
            .header-title h1 {
                font-size: 1.5rem;
            }
            
            .header-actions {
                flex-wrap: wrap;
                justify-content: flex-end;
            }
        }

        @media (max-width: 768px) {
            .main-header,
            .controls-panel {
                padding: 1rem;
            }
            
            .sidebar {
                display: none;
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .modal-content {
                margin-left: 0;
                max-width: 95%;
                padding: 1.5rem;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .filter-group {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .action-btn {
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
            }
            
            .filter-btn {
                padding: 0.625rem 1rem;
                font-size: 0.875rem;
            }

            .header-content {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
            
            .header-actions {
                width: 100%;
                justify-content: space-between;
            }

            .stats-row {
                grid-template-columns: 1fr;
            }

            .notification-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                padding: 1rem;
            }

            .notification-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .notification-actions {
                align-self: flex-end;
                margin-top: 0.5rem;
            }
            
            .notification-icon {
                width: 40px;
                height: 40px;
            }
        }

        @media (max-width: 480px) {
            .controls-panel {
                padding: 1rem;
                margin: 0.5rem;
                width: calc(100% - 1rem);
            }
            
            .notifications-container {
                padding: 0 0.5rem 1rem;
            }
            
            .stat-card {
                padding: 1rem;
            }
            
            .stat-icon {
                width: 40px;
                height: 40px;
            }
            
            .stat-info h3 {
                font-size: 1.25rem;
            }
            
            .action-btn {
                padding: 0.625rem;
                font-size: 0.8rem;
            }
            
            .filter-btn {
                padding: 0.5rem 0.75rem;
                font-size: 0.8rem;
            }
            
            .notification-meta {
                font-size: 0.75rem;
            }
            
            .type-badge {
                font-size: 0.65rem;
                padding: 0.2rem 0.5rem;
            }
        }
    </style>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <?php include_once 'views/inc/heder.php'; ?>
        </aside>
        
        <!-- Contenido principal -->
        <div class="main-content">
            <!-- Header Principal -->
            <header class="main-header">
                <div class="header-content">
                    <div class="header-title">
                        <h1>
                            🔔 Centro de Notificaciones
                            <?php 
                            $unreadCount = 0;
                            foreach($notificacionesConUsuarios ?? [] as $notif) {
                                if(!$notif['leido']) $unreadCount++;
                            }
                            if($unreadCount > 0): ?>
                                <span class="unread-badge"><?= $unreadCount ?> sin leer</span>
                            <?php endif; ?>
                        </h1>
                        <p>Gestiona todas las notificaciones del sistema</p>
                    </div>
                    <div class="header-actions">
                        <button class="action-btn" onclick="marcarTodasComoLeidas()">
                            <i data-lucide="check-circle"></i>
                            Marcar todas como leídas
                        </button>
                    </div>
                </div>
            </header>

            <!-- Panel de Control -->
            <div class="controls-panel">
                <div class="filters-grid">
                    <div class="search-container">
                        <i data-lucide="search" class="search-icon"></i>
                        <input type="text" 
                               id="searchInput" 
                               class="search-input" 
                               placeholder="Buscar en notificaciones..."
                               onkeyup="filtrarNotificaciones()">
                    </div>
                    
                    <div class="filter-group">
                        <button class="filter-btn active" onclick="filtrarPorTipo('todas')">
                            <i data-lucide="bell"></i>
                            Todas
                        </button>
                        <button class="filter-btn" onclick="filtrarPorTipo('no-leidas')">
                            <i data-lucide="mail"></i>
                            No leídas
                        </button>
                        <button class="filter-btn" onclick="filtrarPorTipo('urgentes')">
                            <i data-lucide="alert-octagon"></i>
                            Urgentes
                        </button>
                    </div>
                    
                    <button class="action-btn" onclick="limpiarNotificaciones()">
                        <i data-lucide="trash-2"></i>
                        Limpiar leídas
                    </button>
                </div>
            </div>

            <!-- Contenedor Principal -->
            <div class="notifications-container">
                <!-- Estadísticas -->
                <div class="stats-row">
                    <?php 
                    $unreadCount = 0;
                    $urgentCount = 0;
                    $warningCount = 0;
                    $infoCount = 0;
                    
                    foreach($notificacionesConUsuarios ?? [] as $notif) {
                        if(!$notif['leido']) $unreadCount++;
                        switch($notif['tipo']) {
                            case 1: $urgentCount++; break;
                            case 2: $warningCount++; break;
                            case 3: $infoCount++; break;
                        }
                    }
                    ?>
                    
                    <div class="stat-card" onclick="filtrarPorTipo('no-leidas')">
                        <div class="stat-icon unread">
                            <i data-lucide="mail"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="unreadCount"><?= $unreadCount ?></h3>
                            <p>No leídas</p>
                        </div>
                    </div>
                    
                    <div class="stat-card" onclick="filtrarPorTipo('urgentes')">
                        <div class="stat-icon urgent">
                            <i data-lucide="alert-octagon"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="urgentCount"><?= $urgentCount ?></h3>
                            <p>Urgentes</p>
                        </div>
                    </div>
                    
                    <div class="stat-card" onclick="filtrarPorTipo('advertencias')">
                        <div class="stat-icon warning">
                            <i data-lucide="alert-triangle"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="warningCount"><?= $warningCount ?></h3>
                            <p>Advertencias</p>
                        </div>
                    </div>
                    
                    <div class="stat-card" onclick="filtrarPorTipo('informativas')">
                        <div class="stat-icon info">
                            <i data-lucide="info"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="infoCount"><?= $infoCount ?></h3>
                            <p>Informativas</p>
                        </div>
                    </div>
                </div>

                <!-- Lista de Notificaciones -->
                <div class="notifications-list" id="notificationsList">
                    <?php if(!empty($notificacionesConUsuarios)): ?>
                        <?php foreach($notificacionesConUsuarios as $notif): ?>
                            <?php 
                            // Determinar clase según tipo
                            $tipoClase = match($notif['tipo']) {
                                1 => 'urgent',
                                2 => 'warning',
                                3 => 'info',
                                4 => 'success',
                                default => 'info'
                            };
                            
                            $iconClass = match($notif['tipo']) {
                                1 => 'icon-urgent',
                                2 => 'icon-warning',
                                3 => 'icon-info',
                                4 => 'icon-success',
                                default => 'icon-info'
                            };
                            
                            $iconName = match($notif['tipo']) {
                                1 => 'alert-octagon',
                                2 => 'alert-triangle',
                                3 => 'info',
                                4 => 'check-circle',
                                default => 'info'
                            };
                            
                            $badgeClass = match($notif['tipo']) {
                                1 => 'badge-urgent',
                                2 => 'badge-warning',
                                3 => 'badge-info',
                                4 => 'badge-success',
                                default => 'badge-info'
                            };
                            
                            $badgeText = match($notif['tipo']) {
                                1 => 'URGENTE',
                                2 => 'ADVERTENCIA',
                                3 => 'INFORMACIÓN',
                                4 => 'ÉXITO',
                                default => 'INFORMACIÓN'
                            };
                            
                            // Contar destinatarios
                            $destinatarios = $notif['usuarios'] ?? [];
                            $totalDestinatarios = count($destinatarios);
                            $primerosDestinatarios = array_slice($destinatarios, 0, 2);
                            ?>
                            
                            <div class="notification-item <?= $tipoClase ?> <?= !$notif['leido'] ? 'unread' : '' ?>" 
                                 data-id="<?= $notif['id_notif'] ?>"
                                 data-tipo="<?= $notif['tipo'] ?>"
                                 data-leido="<?= $notif['leido'] ? '1' : '0' ?>">
                                
                                <?php if(!$notif['leido']): ?>
                                    <div class="unread-dot"></div>
                                <?php endif; ?>
                                
                                <div class="notification-icon <?= $iconClass ?>">
                                    <i data-lucide="<?= $iconName ?>"></i>
                                </div>
                                
                                <div class="notification-content">
                                    <div class="notification-message truncate">
                                        <span class="type-badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                                        <?= htmlspecialchars($notif['mensaje'] ?? 'Sin mensaje') ?>
                                    </div>
                                    
                                    <div class="notification-meta">
                                        <div class="meta-item">
                                            <i data-lucide="calendar"></i>
                                            <span><?= date('d/m/Y', strtotime($notif['fecha_notif'])) ?></span>
                                        </div>
                                        
                                        <div class="meta-item">
                                            <i data-lucide="clock"></i>
                                            <span><?= date('h:i A', strtotime($notif['fecha_notif'])) ?></span>
                                            <span class="relative-time">(<?= obtenerTiempoRelativo($notif['fecha_notif']) ?>)</span>
                                        </div>
                                    </div>
                                    
                                    <?php if($totalDestinatarios > 0): ?>
                                        <div class="notification-recipients">
                                            <i data-lucide="users" style="width: 14px; height: 14px; color: var(--gray-500);"></i>
                                            <?php foreach($primerosDestinatarios as $destinatario): ?>
                                                <span class="recipient-badge">
                                                    <i data-lucide="user" style="width: 12px; height: 12px;"></i>
                                                    <?= htmlspecialchars($destinatario['nombre'] ?? 'Usuario') ?>
                                                </span>
                                            <?php endforeach; ?>
                                            
                                            <?php if($totalDestinatarios > 2): ?>
                                                <span class="recipient-count">
                                                    +<?= $totalDestinatarios - 2 ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="notification-actions">
                                    <?php if(!$notif['leido']): ?>
                                        <button class="action-btn-small mark-read" 
                                                onclick="marcarComoLeida(<?= $notif['id_notif'] ?>, this)"
                                                title="Marcar como leída">
                                            <i data-lucide="mail-open"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="action-btn-small" 
                                                onclick="marcarComoNoLeida(<?= $notif['id_notif'] ?>, this)"
                                                title="Marcar como no leída">
                                            <i data-lucide="mail"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button class="action-btn-small" 
                                            onclick="verDetalles(<?= htmlspecialchars(json_encode($notif)) ?>)"
                                            title="Ver detalles">
                                        <i data-lucide="eye"></i>
                                    </button>
                                    
                                    <button class="action-btn-small delete" 
                                            onclick="eliminarNotificacion(<?= $notif['id_notif'] ?>, this)"
                                            title="Eliminar">
                                        <i data-lucide="trash-2"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i data-lucide="bell-off"></i>
                            </div>
                            <h3>No hay notificaciones</h3>
                            <p>Todas las notificaciones están gestionadas o no hay nuevas alertas.</p>
                            <button class="action-btn" onclick="recargarNotificaciones()" style="margin-top: 1rem;">
                                <i data-lucide="refresh-cw"></i>
                                Recargar notificaciones
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalles -->
    <div class="modal-overlay" id="detailsModal">
        <div class="modal-content">
            <!-- Contenido dinámico -->
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Inicializar Iconos
        lucide.createIcons();

        // Estado global
        let filtroActivo = 'todas';
        let notificaciones = <?= json_encode($notificacionesConUsuarios ?? []) ?>;
        let currentUserId = <?= json_encode($_SESSION['user_id'] ?? 0) ?>;

        // Filtrar por tipo
        function filtrarPorTipo(tipo) {
            // Actualizar botones activos
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            filtroActivo = tipo;
            aplicarFiltros();
        }

        // Aplicar filtros combinados
        function aplicarFiltros() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const items = document.querySelectorAll('.notification-item');
            let visibles = 0;
            let unreadCount = 0;
            let urgentCount = 0;
            let warningCount = 0;
            let infoCount = 0;

            items.forEach(item => {
                const texto = item.textContent.toLowerCase();
                const tipo = item.dataset.tipo;
                const leido = item.dataset.leido;
                
                // Verificar búsqueda
                const coincideBusqueda = !searchTerm || texto.includes(searchTerm);
                
                // Verificar filtro por tipo
                let coincideFiltro = false;
                switch(filtroActivo) {
                    case 'todas':
                        coincideFiltro = true;
                        break;
                    case 'no-leidas':
                        coincideFiltro = leido === '0';
                        break;
                    case 'urgentes':
                        coincideFiltro = tipo === '1';
                        break;
                    case 'advertencias':
                        coincideFiltro = tipo === '2';
                        break;
                    case 'informativas':
                        coincideFiltro = tipo === '3';
                        break;
                    default:
                        coincideFiltro = true;
                }
                
                if (coincideBusqueda && coincideFiltro) {
                    item.style.display = 'flex';
                    visibles++;
                    
                    // Contar estadísticas
                    if (leido === '0') unreadCount++;
                    if (tipo === '1') urgentCount++;
                    if (tipo === '2') warningCount++;
                    if (tipo === '3') infoCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Actualizar contadores
            actualizarContadores(unreadCount, urgentCount, warningCount, infoCount);

            // Mostrar estado vacío si no hay resultados
            mostrarEstadoVacio(visibles === 0 && notificaciones.length > 0);
        }

        // Actualizar contadores en UI
        function actualizarContadores(unread, urgent, warning, info) {
            document.getElementById('unreadCount').textContent = unread;
            document.getElementById('urgentCount').textContent = urgent;
            document.getElementById('warningCount').textContent = warning;
            document.getElementById('infoCount').textContent = info;
        }

        // Mostrar/ocultar estado vacío
        function mostrarEstadoVacio(mostrar) {
            let emptyState = document.querySelector('.empty-state');
            const list = document.getElementById('notificationsList');
            
            if (mostrar && !emptyState) {
                emptyState = document.createElement('div');
                emptyState.className = 'empty-state';
                emptyState.innerHTML = `
                    <div class="empty-icon">
                        <i data-lucide="search-x"></i>
                    </div>
                    <h3>Sin coincidencias</h3>
                    <p>No encontramos notificaciones que coincidan con tu búsqueda.</p>
                    <button class="action-btn" onclick="limpiarFiltros()" style="margin-top: 1rem;">
                        <i data-lucide="x-circle"></i>
                        Limpiar filtros
                    </button>
                `;
                list.parentNode.insertBefore(emptyState, list.nextSibling);
            } else if (!mostrar && emptyState && emptyState.parentNode) {
                emptyState.remove();
            }
        }

        // Limpiar filtros
        function limpiarFiltros() {
            document.getElementById('searchInput').value = '';
            filtrarPorTipo('todas');
        }

        // Función de búsqueda
        function filtrarNotificaciones() {
            aplicarFiltros();
        }

        // Marcar como leída (solo para el usuario actual)
        function marcarComoLeida(id, btn) {
            $.post('marcar_leida.php', { 
                id_notif: id,
                id_usuario: currentUserId
            })
            .done(function(response) {
                const item = btn.closest('.notification-item');
                item.classList.remove('unread');
                item.dataset.leido = '1';
                
                // Actualizar botón
                btn.innerHTML = '<i data-lucide="mail"></i>';
                btn.onclick = function() { marcarComoNoLeida(id, this); };
                btn.title = "Marcar como no leída";
                
                // Actualizar contadores
                aplicarFiltros();
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Marcada como leída!',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .fail(function() {
                Swal.fire('Error', 'No se pudo marcar como leída', 'error');
            });
        }

        // Marcar como no leída
        function marcarComoNoLeida(id, btn) {
            $.post('marcar_no_leida.php', { 
                id_notif: id,
                id_usuario: currentUserId
            })
            .done(function(response) {
                const item = btn.closest('.notification-item');
                item.classList.add('unread');
                item.dataset.leido = '0';
                
                // Actualizar botón
                btn.innerHTML = '<i data-lucide="mail-open"></i>';
                btn.onclick = function() { marcarComoLeida(id, this); };
                btn.title = "Marcar como leída";
                
                // Actualizar contadores
                aplicarFiltros();
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Marcada como no leída!',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .fail(function() {
                Swal.fire('Error', 'No se pudo marcar como no leída', 'error');
            });
        }

        // Marcar todas como leídas (para el usuario actual)
        function marcarTodasComoLeidas() {
            Swal.fire({
                title: '¿Marcar todas como leídas?',
                text: "Todas las notificaciones se marcarán como revisadas.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3F51B5',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, marcar todas',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('marcar_todas_leidas.php', { id_usuario: currentUserId })
                        .done(function(response) {
                            document.querySelectorAll('.notification-item').forEach(item => {
                                item.classList.remove('unread');
                                item.dataset.leido = '1';
                            });
                            
                            // Actualizar botones
                            document.querySelectorAll('.mark-read').forEach(btn => {
                                btn.innerHTML = '<i data-lucide="mail"></i>';
                                btn.onclick = function() { 
                                    const id = this.closest('.notification-item').dataset.id;
                                    marcarComoNoLeida(id, this);
                                };
                                btn.title = "Marcar como no leída";
                            });
                            
                            aplicarFiltros();
                            
                            Swal.fire(
                                '¡Hecho!',
                                'Todas las notificaciones han sido marcadas como leídas.',
                                'success'
                            );
                        })
                        .fail(function() {
                            Swal.fire('Error', 'No se pudieron marcar todas como leídas', 'error');
                        });
                }
            });
        }

        // Eliminar notificación (solo para el usuario actual)
        function eliminarNotificacion(id, btn) {
            Swal.fire({
                title: '¿Eliminar esta notificación?',
                text: "Esta acción solo la eliminará para ti, otros usuarios seguirán viéndola.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3F51B5',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('eliminar_notificacion.php', { 
                        id_notif: id,
                        id_usuario: currentUserId
                    })
                    .done(function(response) {
                        const item = btn.closest('.notification-item');
                        item.style.animation = 'fadeOut 0.3s ease-out';
                        setTimeout(() => {
                            item.remove();
                            aplicarFiltros();
                        }, 300);
                        
                        Swal.fire(
                            'Eliminada',
                            'La notificación ha sido eliminada para ti.',
                            'success'
                        );
                    })
                    .fail(function() {
                        Swal.fire('Error', 'No se pudo eliminar la notificación', 'error');
                    });
                }
            });
        }

        // Limpiar notificaciones leídas (solo para el usuario actual)
        function limpiarNotificaciones() {
            Swal.fire({
                title: '¿Limpiar notificaciones leídas?',
                text: "Se eliminarán todas las notificaciones marcadas como leídas.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3F51B5',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, limpiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('limpiar_leidas.php', { id_usuario: currentUserId })
                        .done(function(response) {
                            document.querySelectorAll('.notification-item[data-leido="1"]').forEach(item => {
                                item.remove();
                            });
                            
                            aplicarFiltros();
                            
                            Swal.fire(
                                '¡Limpio!',
                                'Todas las notificaciones leídas han sido eliminadas.',
                                'success'
                            );
                        })
                        .fail(function() {
                            Swal.fire('Error', 'No se pudieron limpiar las notificaciones', 'error');
                        });
                }
            });
        }

        // Ver detalles
        function verDetalles(notif) {
            const tipoTexto = match(notif.tipo) {
                1 => 'Urgente',
                2 => 'Advertencia',
                3 => 'Informativa',
                4 => 'Éxito',
                default => 'Informativa'
            };
            
            const icono = match(notif.tipo) {
                1 => 'alert-octagon',
                2 => 'alert-triangle',
                3 => 'info',
                4 => 'check-circle',
                default => 'info'
            };
            
            const color = match(notif.tipo) {
                1 => '#F44336',
                2 => '#FF9800',
                3 => '#2196F3',
                4 => '#4CAF50',
                default => '#2196F3'
            };
            
            const destinatarios = notif.usuarios || [];
            const esParaMi = destinatarios.some(u => u.id_usuario == currentUserId);
            
            let destinatariosHTML = '';
            if (destinatarios.length > 0) {
                destinatariosHTML = `
                    <div class="recipients-list">
                        <p style="font-weight: 500; margin-bottom: 0.75rem; color: var(--gray-700);">👥 Destinatarios (${destinatarios.length})</p>
                        ${destinatarios.map(user => `
                            <div class="recipient-item">
                                <div class="recipient-avatar">
                                    ${(user.nombre || 'U').charAt(0).toUpperCase()}
                                </div>
                                <div class="recipient-info">
                                    <div class="recipient-name">${user.nombre || 'Usuario'} ${user.id_usuario == currentUserId ? '<span style="color: var(--primary-color); font-weight: 600;">(Tú)</span>' : ''}</div>
                                    <div class="recipient-role">${user.rol || 'Usuario'}</div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
            
            document.getElementById('modalContent').innerHTML = `
                <div style="margin-bottom: 2rem;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: ${color}20; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="${icono}" style="color: ${color};"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 0.25rem 0; color: ${color};">${tipoTexto}</h4>
                            <p style="margin: 0; color: var(--gray-600); font-size: 0.875rem;">ID: ${notif.id_notif}</p>
                        </div>
                    </div>
                    
                    <div style="background: var(--gray-50); padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
                        <p style="margin: 0; font-size: 1.1rem;">${notif.mensaje}</p>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                        <div>
                            <p style="font-weight: 500; margin-bottom: 0.5rem; color: var(--gray-700);">📅 Fecha de notificación</p>
                            <p style="margin: 0; color: var(--gray-600);">${new Date(notif.fecha_notif).toLocaleDateString('es-ES', { 
                                weekday: 'long', 
                                year: 'numeric', 
                                month: 'long', 
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            })}</p>
                        </div>
                        
                        <div>
                            <p style="font-weight: 500; margin-bottom: 0.5rem; color: var(--gray-700);">📊 Estado</p>
                            <p style="margin: 0; color: var(--gray-600);">
                                ${notif.leido ? 
                                    '<span style="color: var(--success-color); font-weight: 600;">Leída</span>' : 
                                    '<span style="color: var(--primary-color); font-weight: 600;">No leída</span>'}
                            </p>
                        </div>
                        
                        <div>
                            <p style="font-weight: 500; margin-bottom: 0.5rem; color: var(--gray-700);">👥 Para</p>
                            <p style="margin: 0; color: var(--gray-600);">
                                ${esParaMi ? 
                                    '<span style="color: var(--primary-color); font-weight: 600;">Incluye a ti</span>' : 
                                    'No incluye a ti'}
                            </p>
                        </div>
                        
                        <div>
                            <p style="font-weight: 500; margin-bottom: 0.5rem; color: var(--gray-700);">👤 Destinatarios</p>
                            <p style="margin: 0; color: var(--gray-600);">${destinatarios.length} usuario(s)</p>
                        </div>
                    </div>
                    
                    ${destinatariosHTML}
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    ${!notif.leido && esParaMi ? `
                        <button onclick="marcarComoLeida(${notif.id_notif}, null); cerrarModal();" 
                                style="flex: 1; padding: 0.75rem; background: var(--primary-color); color: white; border: none; border-radius: var(--radius-md); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                            <i data-lucide="mail-open"></i> Marcar como leída
                        </button>
                    ` : esParaMi ? `
                        <button onclick="marcarComoNoLeida(${notif.id_notif}, null); cerrarModal();" 
                                style="flex: 1; padding: 0.75rem; background: var(--gray-200); color: var(--gray-700); border: none; border-radius: var(--radius-md); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                            <i data-lucide="mail"></i> Marcar como no leída
                        </button>
                    ` : ''}
                    
                    ${esParaMi ? `
                        <button onclick="eliminarNotificacion(${notif.id_notif}, null); cerrarModal();" 
                                style="flex: 1; padding: 0.75rem; background: #FFEBEE; color: var(--danger-color); border: none; border-radius: var(--radius-md); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                            <i data-lucide="trash-2"></i> Eliminar
                        </button>
                    ` : ''}
                </div>
            `;
            
            document.getElementById('detailsModal').classList.add('active');
            lucide.createIcons();
        }

        // Cerrar modal
        function cerrarModal() {
            document.getElementById('detailsModal').classList.remove('active');
        }

        // Recargar notificaciones
        function recargarNotificaciones() {
            location.reload();
        }

        // Función match (para navegadores más antiguos)
        function match(value) {
            return function(...cases) {
                if (cases.length % 2 !== 0) {
                    throw new Error('El número de argumentos debe ser par');
                }
                for (let i = 0; i < cases.length; i += 2) {
                    if (cases[i] === value) {
                        return cases[i + 1];
                    }
                }
                return cases[cases.length - 1];
            };
        }

        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            aplicarFiltros();
            
            // Cerrar modal con ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    cerrarModal();
                }
            });
            
            // Cerrar modal al hacer clic fuera
            document.getElementById('detailsModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    cerrarModal();
                }
            });
        });

        // Función auxiliar para tiempo relativo (implementar en backend)
        function obtenerTiempoRelativo(fecha) {
            const ahora = new Date();
            const fechaNotif = new Date(fecha);
            const diffMs = ahora - fechaNotif;
            const diffMin = Math.floor(diffMs / 60000);
            
            if (diffMin < 1) return 'Ahora';
            if (diffMin < 60) return `Hace ${diffMin} min`;
            if (diffMin < 1440) return `Hace ${Math.floor(diffMin / 60)} h`;
            return `Hace ${Math.floor(diffMin / 1440)} d`;
        }
    </script>
</body>
</html>