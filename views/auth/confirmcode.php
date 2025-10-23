<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">
                <i class="bi bi-shield-lock"></i>
                <h1>Ingrese el código</h1>
            </div>
            <form action="?action=inicio&method=checkCode" method="post">
                <?php if (isset($_GET['error']) && $_GET['error']==1): ?>
                    <div class="alert error">
                        <i class="bi bi-exclamation-circle"></i>
                        <span>Codigo incorrecto.</span>
                    </div>
                <?php elseif (isset($_GET['error']) && $_GET['error']==2): ?>
                    <div class="alert error">
                        <i class="bi bi-exclamation-circle"></i>
                        <span>Error accediendo a nuestros servidores, por favor intentelo de nuevo en un momento.</span>
                    </div>
                <?php elseif (isset($_GET['msg'])): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-shield-lock"></i>
                        <span>Te hemos enviado el codigo a tu correo anteriormente, por favor revise y coloquelo en esta pagina.</span>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="user">
                        <i class="bi bi-person icon"></i>
                        <span>Código</span>
                    </label>
                    <input type="text" id="codigo" name="codigo" placeholder="Ingrese el codigo enviado a su correo" required minlength="32">
                </div>
                <button type="submit" name="init" class="btn-submit">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Recuperar</span>
                </button>
            </form>
        </div>
    </div>
</body>
</html>