<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?? 'Sistema' ?> - Página No Encontrada</title>
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
    <link rel="stylesheet" href="public/css/error.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <main class="error-container">
        <div class="error-icon">
            <i data-lucide="map-pin-off" class="icon-svg"></i>
        </div>
        
        <h1 class="error-code">404</h1>
        <h2 class="error-title">¡Vaya! Llevenos a la feria y la verá.</h2>
        
        <p class="error-description">
            Pagina no implementada
        </p>
        
        <a href="./" class="btn-home">
            <i data-lucide="home"></i>
            Regresar al Inicio
        </a>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>