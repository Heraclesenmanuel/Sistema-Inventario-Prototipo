<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Configuración del Sistema - UPEL">
    <meta name="theme-color" content="#3F51B5">
    <title><?= APP_NAME ?? 'Inicio' ?> - <?= $titulo ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="stylesheet" href="public/css/config.css">
    <style>
        /* Estilos para el modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            border-radius: 8px;
            width: 90%;
            max-width: 700px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            animation: modalSlideIn 0.3s ease;
        }
        
        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .modal-header {
            background: #2c3e50;
            color: white;
            padding: 20px 30px;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }
        
        .close-modal {
            background: none;
            border: none;
            color: white;
            font-size: 28px;
            cursor: pointer;
            line-height: 1;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background-color 0.3s;
        }
        
        .close-modal:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .modal-body {
            padding: 30px;
        }
        
        .modal-footer {
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        /* Form styles para el modal */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-group input,
        .form-group select {
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        .categorias-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
            max-height: 200px;
            overflow-y: auto;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        
        .categoria-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .categoria-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .categoria-item label {
            cursor: pointer;
            user-select: none;
            font-weight: normal;
        }
        
        /* Estilos para botones */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0056b3;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #545b62;
        }
        
        .btn-editar {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-editar:hover {
            background-color: #0056b3;
        }
        
        .btn-eliminar {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-eliminar:hover:not(:disabled) {
            background-color: #c82333;
        }
        
        .btn-eliminar:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Botones en tabla */
        .acciones-usuario {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <?php include_once 'views/inc/heder.php'; ?>
        
        <main class="main-content">
            <!-- Page Header -->
            <header class="page-header">
                <div class="header-content">
                    <i data-lucide="settings" class="header-icon"></i>
                    <div>
                        <h1><?= $titulo ?></h1>
                        <p class="header-date">
                            <i data-lucide="calendar" class="date-icon"></i>
                            <span>Hoy es: <?= APP_Date ?></span>
                        </p>
                    </div>
                </div>
            </header>

            <?php if(isset($_SESSION['mensaje'])): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: '<?php echo $_SESSION['tipo_mensaje'] === 'success' ? 'success' : 'error'; ?>',
                            title: '<?php echo $_SESSION['tipo_mensaje'] === 'success' ? '¡Éxito!' : 'Error'; ?>',
                            text: '<?php echo addslashes($_SESSION['mensaje']); ?>',
                            confirmButtonColor: '#3F51B5'
                        });
                    });
                </script>
                <?php 
                unset($_SESSION['mensaje']);
                unset($_SESSION['tipo_mensaje']);
                ?>
            <?php endif; ?>

            <!-- Sección: Clave Maestra -->
            <section class="config-section">
                <div class="section-header">
                    <i data-lucide="key" class="section-icon"></i>
                    <h2>Cambio de Clave Maestra</h2>
                </div>
                
                <div class="alert-box alert-warning">
                    <i data-lucide="alert-triangle" class="alert-icon"></i>
                    <div class="alert-content">
                        <strong>Importante:</strong>
                        <p>La clave maestra es utilizada para acciones críticas del sistema. Guárdala en un lugar seguro.</p>
                    </div>
                </div>

                <form class="config-form" id="formClaveMaestra" method="POST" action="?action=config&method=cambiarClave" autocomplete="off">
                    <div class="form-group">
                        <label for="clave_actual" class="form-label">
                            <i data-lucide="lock" class="label-icon"></i>
                            <span>Clave Maestra Actual<span class="required">*</span></span>
                        </label>
                        <div class="input-wrapper password-wrapper">
                            <i data-lucide="shield" class="input-icon"></i>
                            <input 
                                type="password" 
                                id="clave_actual" 
                                name="clave_actual"
                                class="form-input"
                                placeholder="Ingrese la clave maestra actual"
                                required
                                autocomplete="current-password">
                            <button type="button" class="btn-toggle" data-target="clave_actual">
                                <i data-lucide="eye" class="toggle-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="clave_nueva" class="form-label">
                            <i data-lucide="key-round" class="label-icon"></i>
                            <span>Nueva Clave Maestra<span class="required">*</span></span>
                        </label>
                        <div class="input-wrapper password-wrapper">
                            <i data-lucide="lock-keyhole" class="input-icon"></i>
                            <input 
                                type="password" 
                                id="clave_nueva" 
                                name="clave_nueva"
                                class="form-input"
                                placeholder="Ingrese la nueva clave maestra"
                                required
                                minlength="6"
                                autocomplete="new-password">
                            <button type="button" class="btn-toggle" data-target="clave_nueva">
                                <i data-lucide="eye" class="toggle-icon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmar_clave" class="form-label">
                            <i data-lucide="shield-check" class="label-icon"></i>
                            <span>Confirmar Nueva Clave<span class="required">*</span></span>
                        </label>
                        <div class="input-wrapper password-wrapper">
                            <i data-lucide="check-circle" class="input-icon"></i>
                            <input 
                                type="password" 
                                id="confirmar_clave" 
                                name="confirmar_clave"
                                class="form-input"
                                placeholder="Confirme la nueva clave maestra"
                                required
                                minlength="6"
                                autocomplete="new-password">
                            <button type="button" class="btn-toggle" data-target="confirmar_clave">
                                <i data-lucide="eye" class="toggle-icon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" name="bandera_cambiar_clave" class="btn-submit">
                        <i data-lucide="check-circle-2" class="btn-icon"></i>
                        <span>Actualizar Clave Maestra</span>
                    </button>
                </form>
            </section>

            <!-- Sección: Nombre de la Aplicación -->
            <section class="config-section">
                <div class="section-header">
                    <i data-lucide="tag" class="section-icon"></i>
                    <h2>Cambio de Nombre de la Aplicación</h2>
                </div>
                
                <div class="alert-box alert-info">
                    <i data-lucide="info" class="alert-icon"></i>
                    <div class="alert-content">
                        <strong>Información:</strong>
                        <p>Este nombre aparecerá en el título y encabezado de todas las páginas.</p>
                    </div>
                </div>

                <form class="config-form" id="formNombreApp" method="POST" action="?action=config&method=cambiarNombreApp" autocomplete="off">
                    <div class="form-group">
                        <label for="nombre_app" class="form-label">
                            <i data-lucide="type" class="label-icon"></i>
                            <span>Nombre de la Aplicación<span class="required">*</span></span>
                        </label>
                        <div class="input-wrapper">
                            <i data-lucide="text-cursor-input" class="input-icon"></i>
                            <input 
                                type="text" 
                                id="nombre_app" 
                                name="nombre_app"
                                class="form-input"
                                placeholder="Ingrese el nuevo nombre de la aplicación"
                                value="<?= APP_NAME ?? '' ?>"
                                required
                                maxlength="50">
                        </div>
                    </div>

                    <button type="submit" name="cambiar_nombre" class="btn-submit">
                        <i data-lucide="save" class="btn-icon"></i>
                        <span>Actualizar Nombre</span>
                    </button>
                </form>
            </section>

            <!-- Sección: Agregar Usuario -->
            <section class="config-section">
                <div class="section-header">
                    <i data-lucide="user-plus" class="section-icon"></i>
                    <h2>Agregar Nuevo Usuario</h2>
                </div>
                
                <div class="alert-box alert-info">
                    <i data-lucide="info" class="alert-icon"></i>
                    <div class="alert-content">
                        <strong>Información:</strong>
                        <p>Los nuevos usuarios serán creados con el cargo y oficina seleccionados.</p>
                    </div>
                </div>

                <form class="config-form" id="formAgregarUsuario" method="POST" action="?action=config&method=agregarUsuario" autocomplete="off">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cedula" class="form-label">
                                <i data-lucide="id-card" class="label-icon"></i>
                                <span>Cédula<span class="required">*</span></span>
                            </label>
                            <div class="input-wrapper">
                                <i data-lucide="hash" class="input-icon"></i>
                                <input 
                                    type="text" 
                                    id="cedula" 
                                    name="cedula"
                                    class="form-input"
                                    placeholder="Ej: 12345678" 
                                    required 
                                    maxlength="10"
                                    pattern="[0-9]+">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nombre_usuario" class="form-label">
                                <i data-lucide="user" class="label-icon"></i>
                                <span>Nombre Completo<span class="required">*</span></span>
                            </label>
                            <div class="input-wrapper">
                                <i data-lucide="user-circle" class="input-icon"></i>
                                <input 
                                    type="text" 
                                    id="nombre_usuario" 
                                    name="nombre_usuario"
                                    class="form-input"
                                    placeholder="Nombre y apellido" 
                                    required 
                                    maxlength="100">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="id_cargo">Cargo</label>
                        <select name="id_cargo" id="id_cargo" class="form-select-sm" style="width: 100%; padding: 10px;">
                            <?php if(isset($roles['success']) && $roles['success'] && !empty($roles['data'])): ?>
                                <?php foreach($roles['data'] as $rol): ?>
                                    <option value="<?php echo $rol['id_cargo']?>"><?php echo $rol['nombre']?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="1">Administrador</option>
                                <option value="2">Usuario</option>
                                <option value="3">Cuentas</option>
                                <option value="4">Presupuesto</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Oficina/s Del Usuario:</label>
                        <div class="categorias-grid">
                            <?php if(!empty($oficinas)): ?>
                                <?php foreach($oficinas['data'] as $oficina): ?>
                                    <div class="categoria-item">
                                        <input type="checkbox" id="cat_<?php echo $oficina['nombre']?>" name="categorias[]" value="<?php echo $oficina['num_oficina']?>">
                                        <label for="cat_<?php echo $oficina['nombre']?>"><?php echo $oficina['nombre']?></label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="oficinas_seleccionadas" id="oficinasSeleccionadasInput">
                    </div>
                    <button type="submit" name="bandera_agregar_usuario" onclick="enviarOficinas()">
                        <i class="fas fa-user-plus"></i> Agregar Usuario
                    </button>
                </form>
            </section>

            <!-- Sección: Lista de Usuarios -->
            <section class="mostrar-usuarios">
                <h3><i class="fas fa-users"></i> Lista de Usuarios</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cédula</th>
                            <th>Nombre</th>
                            <th>Cargo</th>
                            <th>Cantidad Oficinas</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="usuariosTableBody">
                        <?php if(isset($usuarios['success']) && $usuarios['success'] && !empty($usuarios['data'])): ?>
                            <?php foreach($usuarios['data'] as $usuario): ?>
                                <tr>
                                    <td><?= htmlspecialchars($usuario['id_usuario']) ?></td>
                                    <td><?= htmlspecialchars($usuario['cedula']) ?></td>
                                    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                                    <td><?= $usuario['nombre_cargo']?></td>
                                    <td> <?=htmlspecialchars($usuario['cantidad_oficinas_afiliadas'])?></td>
                                    <td>
                                        <div class="acciones-usuario">
                                            <button class="btn-editar" onclick="editUsuario(<?= $usuario['id_usuario'] ?>)">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <?php if(isset($_SESSION['cedula']) && $_SESSION['cedula'] == $usuario['cedula']): ?>
                                                <button class="btn-eliminar" disabled title="No puedes eliminar tu propia cuenta">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            <?php else: ?>
                                                <button class="btn-eliminar" onclick="eliminarUsuario(<?= $usuario['id_usuario'] ?>, '<?= htmlspecialchars($usuario['nombre']) ?>')">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No hay usuarios registrados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <!-- Modal para Editar Usuario -->
    <div id="usuarioModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="usuarioModalTitle">Editar Usuario</h2>
                <button class="close-modal" onclick="closeUsuarioModal()">&times;</button>
            </div>
            
            <form id="usuarioForm" method="post" onsubmit="saveUsuario(event)">
                <input type="hidden" id="usuarioId" name="id_usuario">
                
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="usuarioCedula">Cédula *</label>
                            <input type="text" id="usuarioCedula" name="cedula" required 
                                   pattern="[0-9]{6,10}" 
                                   title="Ingrese una cédula válida (6-10 dígitos)"
                                   placeholder="Ej: 12345678">
                        </div>
                        
                        <div class="form-group">
                            <label for="usuarioNombre">Nombre Completo *</label>
                            <input type="text" id="usuarioNombre" name="nombre" required 
                                   placeholder="Ej: Juan Pérez">
                        </div>
                        
                        <div class="form-group">
                            <label for="usuarioEmail">Correo Electrónico *</label>
                            <input type="email" id="usuarioEmail" name="email" required 
                                   placeholder="ejemplo@correo.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="usuarioTelefono">Teléfono</label>
                            <input type="tel" id="usuarioTelefono" name="telefono" 
                                   pattern="[0-9]{7,15}" 
                                   title="Ingrese un número de teléfono válido"
                                   placeholder="0414 0000000">
                        </div>
                        
                        <div class="form-group">
                            <label for="usuarioCargo">Cargo *</label>
                            <select id="usuarioCargo" name="cargo_id" required>
                                <option value="">Seleccione un cargo</option>
                                <?php 
                                if(isset($roles['success']) && $roles['success'] && !empty($roles['data'])): 
                                    foreach($roles['data'] as $rol): ?>
                                        <option value="<?= $rol['id_cargo'] ?>">
                                            <?= htmlspecialchars($rol['nombre']) ?>
                                        </option>
                                    <?php endforeach; 
                                endif; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="usuarioEstado">Estado *</label>
                            <select id="usuarioEstado" name="estado" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                                <option value="Suspendido">Suspendido</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="usuarioPassword">Contraseña</label>
                            <input type="password" id="usuarioPassword" name="password" 
                                   placeholder="Dejar vacío para mantener la actual"
                                   autocomplete="new-password"
                                   minlength="8">
                            <small style="font-size: 12px; color: #666;">Mínimo 8 caracteres (solo para cambio)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="usuarioConfirmPassword">Confirmar Contraseña</label>
                            <input type="password" id="usuarioConfirmPassword" name="confirm_password"
                                   autocomplete="new-password"
                                   minlength="8">
                        </div>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Oficinas Asignadas</label>
                        <div class="categorias-grid" id="oficinasContainer">
                            <?php 
                            if(isset($oficinas) && !empty($oficinas['data'])): 
                                foreach($oficinas['data'] as $oficina): ?>
                                    <div class="categoria-item">
                                        <input type="checkbox" 
                                               class="oficina-checkbox"
                                               id="modal_cat_<?= htmlspecialchars($oficina['nombre']) ?>" 
                                               name="oficinas[]" 
                                               value="<?= $oficina['num_oficina'] ?>">
                                        <label for="modal_cat_<?= htmlspecialchars($oficina['nombre']) ?>">
                                            <?= htmlspecialchars($oficina['nombre']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; 
                            else: ?>
                                <div style="grid-column: 1 / -1; text-align: center; padding: 10px;">
                                    No hay oficinas registradas
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeUsuarioModal()">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Variable global para usuarios
        let usuarios = <?= json_encode($usuarios['data'] ?? []) ?>;
        let checkboxes = document.querySelectorAll("input[name='categorias[]']");

        // Función para enviar oficinas al agregar usuario
        function enviarOficinas() {
            const categoriasSeleccionadas = [];
            checkboxes.forEach(checkbox => {
                if(checkbox.checked) {
                    categoriasSeleccionadas.push(checkbox.value);
                }
            });
            console.log('Categorías seleccionadas:', categoriasSeleccionadas);
            const oficinasSeleccionadasInput = document.getElementById('oficinasSeleccionadasInput');
            oficinasSeleccionadasInput.value = JSON.stringify(categoriasSeleccionadas);
            console.log('Oficinas a enviar:', oficinasSeleccionadasInput.value);
        }

        // Control de checkboxes según cargo
        document.getElementById("id_cargo").addEventListener("change", function() {
            if (this.value == 3) { 
                // Si es Cuentas
                checkboxes.forEach((chk) => {
                    chk.checked = false;
                    chk.disabled = true;
                });
                const cuentasCheckbox = document.querySelector("#cat_Cuentas");
                if (cuentasCheckbox) {
                    cuentasCheckbox.checked = true;
                }

            } else if (this.value == 4) { 
                // Si es Presupuesto
                checkboxes.forEach((chk) => {
                    chk.checked = false;
                    chk.disabled = true;
                });
                const presupuestoCheckbox = document.querySelector("#cat_Presupuesto");
                if (presupuestoCheckbox) {
                    presupuestoCheckbox.checked = true;
                }
            } else {
                checkboxes.forEach((chk) => {
                    chk.checked = false;
                    chk.disabled = false;
                });
            }
        });

        // Editar usuario
        async function editUsuario(idUsuario) {
            console.log('Editando usuario ID:', idUsuario);
            
            try {
                // Mostrar carga
                Swal.fire({
                    title: 'Cargando...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                const response = await fetch(`?action=usuario&method=getOficinasUsuario&id=${encodeURIComponent(idUsuario)}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${encodeURIComponent(idUsuario)}`
                });
                
                Swal.close();
                
                // Verificar si la respuesta es JSON
                const text = await response.text();
                console.log('Respuesta del servidor (primeros 500 chars):', text.substring(0, 500));
                
                let recomendacionesdata;
                try {
                    recomendacionesdata = JSON.parse(text);
                    console.log('wawawa', recomendacionesdata)
                } catch (e) {
                    console.error('Error parseando JSON:', e);
                    throw new Error('El servidor no devolvió una respuesta JSON válida');
                }
                
                if (!response.ok) {
                    throw new Error("Error en la petición: " + response.status);
                }

                if (!recomendacionesdata.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error accediendo a los datos del usuario'
                    });
                } else {
                    openUsuarioModal('edit', idUsuario, recomendacionesdata);
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Error al cargar los datos del usuario'
                });
            }
        }

        // Abrir modal de usuario
        function openUsuarioModal(mode, idUsuario = null, recom_data) {
            const modal = document.getElementById('usuarioModal');
            const modalTitle = document.getElementById('usuarioModalTitle');
            const form = document.getElementById('usuarioForm');
            
            form.reset();
            
            // Limpiar todos los checkboxes del modal
            const modalCheckboxes = document.querySelectorAll('#usuarioForm input[name="oficinas[]"]');
            modalCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            if (mode === 'edit' && idUsuario) {
                modalTitle.textContent = 'Editar Usuario';
                
                // Buscar usuario por ID
                const usuario = usuarios.find(u => parseInt(u.id_usuario) === parseInt(idUsuario));
                
                if (usuario) {
                    console.log('Usuario encontrado:', usuario);
                    
                    document.getElementById('usuarioId').value = usuario.id_usuario;
                    document.getElementById('usuarioCedula').value = usuario.cedula || '';
                    document.getElementById('usuarioNombre').value = usuario.nombre || '';
                    document.getElementById('usuarioEmail').value = usuario.email || '';
                    document.getElementById('usuarioCargo').value = usuario.cargo_id || '';
                    document.getElementById('usuarioTelefono').value = usuario.telefono || '';
                    document.getElementById('usuarioEstado').value = usuario.estado || 'Activo';
                    
                    // Marcar las oficinas del usuario
                    if (recom_data.oficinas) {
                        console.log('Oficinas del usuario:', recom_data.oficinas);
                        recom_data.oficinas.forEach(nombreOficina => {
                            // Buscar el checkbox correspondiente
                            const checkbox = document.querySelector(`#usuarioForm input[value="${nombreOficina}"]`);
                            if (checkbox) {
                                checkbox.checked = true;
                            } else {
                                // Intentar por nombre si el valor no coincide
                                const checkboxByName = document.querySelector(`#usuarioForm input[id="modal_cat_${nombreOficina}"]`);
                                if (checkboxByName) {
                                    checkboxByName.checked = true;
                                }
                            }
                        });
                    }
                    
                } else {
                    console.error('Usuario no encontrado con ID:', idUsuario);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar la información del usuario'
                    });
                    return;
                }
            }
            
            modal.classList.add('active');
        }

        // Cerrar modal de usuario
        function closeUsuarioModal() {
            const modal = document.getElementById('usuarioModal');
            modal.classList.remove('active');
        }

        // Guardar usuario
        async function saveUsuario(event) {
            event.preventDefault();
            
            // Validar contraseñas
            const password = document.getElementById('usuarioPassword').value;
            const confirmPassword = document.getElementById('usuarioConfirmPassword').value;
            
            if (password && password !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Las contraseñas no coinciden'
                });
                return;
            }
            
            const formData = new FormData();
            const usuarioId = document.getElementById('usuarioId').value;
            const isEdit = !!usuarioId;
            
            // Obtener oficinas seleccionadas
            const oficinasSeleccionadas = [];
            const checkboxes = document.querySelectorAll('#usuarioForm input[name="oficinas[]"]:checked');
            checkboxes.forEach(checkbox => {
                oficinasSeleccionadas.push(checkbox.value);
            });
            
            console.log('Oficinas seleccionadas:', oficinasSeleccionadas);
            
            // Agregar datos del formulario
            formData.append('cedula', document.getElementById('usuarioCedula').value);
            formData.append('nombre', document.getElementById('usuarioNombre').value);
            formData.append('email', document.getElementById('usuarioEmail').value);
            formData.append('telefono', document.getElementById('usuarioTelefono').value);
            formData.append('cargo_id', document.getElementById('usuarioCargo').value);
            formData.append('estado', document.getElementById('usuarioEstado').value);
            
            if (password) {
                formData.append('password', password);
                formData.append('confirm_password', confirmPassword);
            }
            
            formData.append('oficinas_seleccionadas', JSON.stringify(oficinasSeleccionadas));
            
            if (isEdit) {
                formData.append('id_usuario', usuarioId);
            }
            
            try {
                // Mostrar carga
                Swal.fire({
                    title: 'Guardando...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                let response;
                if (isEdit) {
                    response = await fetch('?action=config&method=actualizarUsuario', {
                        method: 'POST',
                        body: formData
                    });
                } else {
                    response = await fetch('?action=config&method=agregarUsuario', {
                        method: 'POST',
                        body: formData
                    });
                }
                
                // Verificar si la respuesta es JSON
                const text = await response.text();
                console.log('Respuesta del servidor:', text);
                
                let result;
                try {
                    result = JSON.parse(text);
                } catch (e) {
                    console.error('Error parseando JSON:', e);
                    throw new Error('El servidor no devolvió una respuesta JSON válida');
                }
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: isEdit ? 'Usuario actualizado correctamente' : 'Usuario agregado correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    closeUsuarioModal();
                    // Recargar la página después de 2 segundos
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    throw new Error(result.message || 'Error al guardar el usuario');
                }
                
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Error al guardar el usuario'
                });
            }
        }

        // Eliminar usuario
        function eliminarUsuario(id, nombre) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: `¿Deseas eliminar al usuario "${nombre}"? Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar el formulario por POST para eliminar el usuario
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '?action=config&method=eliminarUsuario';

                    const inputId = document.createElement('input');
                    inputId.type = 'hidden';
                    inputId.name = 'id_usuario';
                    inputId.value = id;
                    form.appendChild(inputId);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('usuarioModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeUsuarioModal();
            }
        });

        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeUsuarioModal();
            }
        });

        // TOGGLE PASSWORD VISIBILITY
        document.querySelectorAll('.toggle-password').forEach(function(element) {
            element.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const targetInput = document.getElementById(targetId);
                if (targetInput.type === 'password') {
                    targetInput.type = 'text';
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                } else {
                    targetInput.type = 'password';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                }
            });
        });
    </script>
</body>
</html>