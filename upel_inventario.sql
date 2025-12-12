-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-12-2025 a las 11:12:07
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `upel_inventario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `codigo_recuperacion`
--

CREATE TABLE `codigo_recuperacion` (
  `id_usuario` int(11) NOT NULL,
  `codigo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config_pag`
--

CREATE TABLE `config_pag` (
  `id_config` int(11) NOT NULL,
  `NombreAPP` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `config_pag`
--

INSERT INTO `config_pag` (`id_config`, `NombreAPP`) VALUES
(1, 'UPELink');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `director`
--

CREATE TABLE `director` (
  `ced_dir` varchar(100) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telf` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `director`
--

INSERT INTO `director` (`ced_dir`, `nombre`, `telf`) VALUES
('25000423', 'Magda Perozo', '04165348900'),
('25000623', 'Maria Jose', '04165348900'),
('29543222', 'Adam Sandler', '04245432222'),
('30000000', 'Ezequiel Angulo', '04245354900'),
('31466704', 'Juanito Pulga', '04125555555'),
('34566777', 'Jose Jose', '04124326789'),
('8514695', 'Roselys Sanchez', '04145544899');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento`
--

CREATE TABLE `movimiento` (
  `id_producto` int(11) NOT NULL,
  `id_solicitud` int(11) NOT NULL,
  `un_anadidas` int(11) DEFAULT NULL,
  `fecha_movimiento` date DEFAULT NULL,
  `id_prod` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion`
--

CREATE TABLE `notificacion` (
  `id_notif` int(11) NOT NULL,
  `tipo` int(11) NOT NULL,
  `fecha_notif` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notificacion`
--

INSERT INTO `notificacion` (`id_notif`, `tipo`, `fecha_notif`) VALUES
(1, 1, '2024-03-15 09:30:00'),
(2, 1, '2024-03-15 10:15:00'),
(3, 1, '2024-03-14 14:20:00'),
(4, 2, '2024-03-15 11:00:00'),
(5, 2, '2024-03-14 16:45:00'),
(6, 2, '2024-03-13 09:20:00'),
(7, 3, '2024-03-15 12:30:00'),
(8, 3, '2024-03-14 17:10:00'),
(9, 3, '2024-03-13 10:40:00'),
(10, 4, '2024-03-15 08:15:00'),
(11, 4, '2024-03-14 13:25:00'),
(12, 5, '2024-03-15 15:40:00'),
(13, 5, '2024-03-14 11:55:00'),
(14, 6, '2024-03-15 07:00:00'),
(15, 6, '2024-03-14 07:00:00'),
(16, 7, '2024-03-15 16:20:00'),
(17, 7, '2024-03-14 10:30:00'),
(18, 8, '2024-03-15 03:00:00'),
(19, 9, '2024-03-15 02:00:00'),
(20, 10, '2024-03-15 18:05:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `oficina`
--

CREATE TABLE `oficina` (
  `num_oficina` varchar(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ced_dir` varchar(100) NOT NULL,
  `telefono` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `oficina`
--

INSERT INTO `oficina` (`num_oficina`, `nombre`, `ced_dir`, `telefono`) VALUES
('143', 'Cuentas', '29543222', '04245432222'),
('204', 'Deportes', '34566777', '04124326789'),
('205', 'Servicios Generales', '25000423', '04165348900'),
('212', 'Informatica', '31466704', '04125555555'),
('305', 'Consejeria/Orientacion', '25000423', '04165348900'),
('313', 'Biblioteca', '30000000', '04245354900'),
('325', 'Presupuesto', '8514695', '04145544899');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ofic_usuario`
--

CREATE TABLE `ofic_usuario` (
  `num_oficina` varchar(10) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ofic_usuario`
--

INSERT INTO `ofic_usuario` (`num_oficina`, `id_usuario`) VALUES
('143', 1),
('143', 8),
('143', 20),
('143', 23),
('143', 27),
('143', 28),
('205', 1),
('212', 8),
('212', 23),
('305', 23),
('305', 27),
('313', 1),
('313', 8),
('313', 28),
('325', 21),
('325', 27);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `un_disponibles` int(11) DEFAULT 0,
  `medida` varchar(100) DEFAULT NULL,
  `id_tipo` int(11) DEFAULT NULL,
  `fecha_r` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id_producto`, `nombre`, `un_disponibles`, `medida`, `id_tipo`, `fecha_r`) VALUES
(1, 'Tornillo', 0, 'Unidades', 4, '0000-00-00'),
(2, 'RAM', 0, 'Unidades', 4, '0000-00-00'),
(3, 'Cargador Laptop', 0, 'Unidades', 4, '0000-00-00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prod_solic`
--

CREATE TABLE `prod_solic` (
  `id_solicitud` int(11) NOT NULL,
  `num_linea` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `un_deseadas` int(11) DEFAULT 0,
  `medida` varchar(100) DEFAULT NULL,
  `id_tipo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `prod_solic`
--

INSERT INTO `prod_solic` (`id_solicitud`, `num_linea`, `nombre`, `un_deseadas`, `medida`, `id_tipo`) VALUES
(1, 1, 'Tornillo', 4, 'Unidades', 4),
(2, 1, 'Pelota Futbol', 4, 'Unidades', 3),
(3, 0, 'RAM', 6, 'Unidades', 4),
(28, 1, 'RAM', 90, 'Unidades', 4),
(31, 1, 'Hoja Carta', 100, 'Unidades', 1),
(32, 1, 'Marcador', 200, 'Unidades', 2),
(32, 2, 'Hoja Carta', 20, 'Unidades', 3),
(33, 1, 'Cargador Laptop', 4, 'Unidades', 4),
(33, 2, 'Tornillo', 5, 'Unidades', 4),
(34, 0, 'RAM', 90, 'Kilogramos', 5),
(37, 1, 'Tornillo', 6, 'Unidades', 4),
(42, 1, 'RAM', 1, 'Unidades', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `rif` varchar(13) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(100) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `ced_encargado` varchar(12) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `nota` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`rif`, `nombre`, `email`, `telefono`, `direccion`, `ced_encargado`, `estado`, `nota`) VALUES
('J12345678', 'Distribuidora ABC', 'juanes.edizahir@gmail.com', '04245354900', 'Barquisimeto, Edo. Lara', '', 'Activo', ''),
('J1234876', 'Enchunfex', 'juanoindistruies@gmail.com', '04221323450', 'Carabobo.', '', 'Activo', ''),
('J12765356', 'ImpasablesVentas', 'contacto.proveedor@gmail.com', '04165406656', 'Merida', '', 'Activo', 'Buenos'),
('J12765543', 'Chamo Industries', 'contacto.proveedor@gmail.com', '04165406678', 'Zulia', '', 'Activo', ''),
('J15753321', 'SuarezFamily', 'eduarjosesuarez@gmail.com', '04221323654', 'Estado Lara, Barquisimeto.', '', 'Inactivo', ''),
('J26432677', 'Industrias Comunica', 'jemenezenterprise@gmail.com', '04245354900', 'Barquisimeto, Edo. Lara', '', 'Activo', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prov_recomendaciones`
--

CREATE TABLE `prov_recomendaciones` (
  `rif_proveedor` varchar(13) NOT NULL,
  `id_tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `prov_recomendaciones`
--

INSERT INTO `prov_recomendaciones` (`rif_proveedor`, `id_tipo`) VALUES
('J12345678', 1),
('J12345678', 4),
('J12345678', 5),
('J1234876', 2),
('J1234876', 4),
('J1234876', 5),
('J12765356', 1),
('J12765356', 4),
('J12765356', 6),
('J12765543', 1),
('J12765543', 2),
('J12765543', 3),
('J12765543', 5),
('J15753321', 4),
('J26432677', 1),
('J26432677', 2),
('J26432677', 3),
('J26432677', 5),
('J26432677', 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `receptor_notif`
--

CREATE TABLE `receptor_notif` (
  `id_usuario` int(11) NOT NULL,
  `id_notif` int(11) NOT NULL,
  `leido` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `receptor_notif`
--

INSERT INTO `receptor_notif` (`id_usuario`, `id_notif`, `leido`) VALUES
(1, 2, 0),
(1, 5, 0),
(1, 8, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_usuario`
--

CREATE TABLE `rol_usuario` (
  `id_cargo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `rol_usuario`
--

INSERT INTO `rol_usuario` (`id_cargo`, `nombre`) VALUES
(1, 'Administrador'),
(2, 'Usuario'),
(3, 'Cuentas'),
(4, 'Presupuesto');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio_proveedor`
--

CREATE TABLE `servicio_proveedor` (
  `id_tipo` int(11) NOT NULL,
  `rif_prov` varchar(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud`
--

CREATE TABLE `solicitud` (
  `id_solicitud` int(11) NOT NULL,
  `id_solicitante` int(11) NOT NULL,
  `fecha_solic` datetime DEFAULT NULL,
  `fecha_deseo` date DEFAULT NULL,
  `comentarios` varchar(500) NOT NULL,
  `num_oficina` varchar(10) DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'Pendiente',
  `apelada` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `solicitud`
--

INSERT INTO `solicitud` (`id_solicitud`, `id_solicitante`, `fecha_solic`, `fecha_deseo`, `comentarios`, `num_oficina`, `estado`, `apelada`) VALUES
(1, 1, '2025-12-05 00:00:00', '2025-12-11', '', '143', 'Rechazado', 0),
(2, 1, '2025-12-05 01:00:00', '2025-12-11', '', '204', 'Aprobado', 0),
(3, 1, '2025-12-04 02:00:00', '2025-12-11', '', '313', 'En Revisión', 0),
(28, 1, '2025-12-05 04:00:00', '2025-12-12', '', '212', 'Aprobado', 0),
(31, 1, '2025-12-05 05:00:00', '2025-12-12', '', '143', 'En Revisión', 0),
(32, 1, '2025-12-05 06:00:00', '2025-12-12', '', '212', 'Aprobado', 0),
(33, 1, '2025-12-05 07:00:00', '2025-12-12', '', '212', 'Aprobado', 0),
(34, 1, '2025-12-07 08:00:00', '2025-12-14', '', '143', 'En Revisión', 0),
(37, 20, '2025-12-08 23:04:28', '2025-12-15', '', '212', 'Pendiente', 0),
(42, 20, '2025-12-11 10:03:59', '2025-12-15', '', '212', 'En Revisión', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_notif`
--

CREATE TABLE `tipo_notif` (
  `id_tipo_notif` int(11) NOT NULL,
  `mensaje` varchar(70) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_notif`
--

INSERT INTO `tipo_notif` (`id_tipo_notif`, `mensaje`) VALUES
(1, 'Stock crítico: El producto está por debajo del nivel mínimo'),
(2, 'Solicitud pendiente de aprobación'),
(3, 'Pedido completado exitosamente'),
(4, 'Nuevo usuario registrado en el sistema'),
(5, 'Error en el sistema de inventario'),
(6, 'Recordatorio: Revisar inventario mensual'),
(7, 'Alerta de vencimiento próximo'),
(8, 'Sistema actualizado correctamente'),
(9, 'Backup automático completado'),
(10, 'Intento de acceso no autorizado detectado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_prod`
--

CREATE TABLE `tipo_prod` (
  `id_tipo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telf` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_prod`
--

INSERT INTO `tipo_prod` (`id_tipo`, `nombre`, `telf`) VALUES
(1, 'Alimentos', ''),
(2, 'Limpieza', ''),
(3, 'Oficina', ''),
(4, 'Electrónicos', ''),
(5, 'Productos de salud', ''),
(6, 'Material educativo', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `cedula` varchar(100) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `id_cargo` int(11) NOT NULL,
  `correo` varchar(200) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `cedula`, `clave`, `id_cargo`, `correo`, `nombre`) VALUES
(1, '31987430', '12345678', 4, 'heracles.sanchez@gmail.com', 'HERACLES SANCHEZ'),
(8, '31414098', '123456', 1, 'heracles.edizahir@gmail.com', 'Luis Nuñez'),
(20, '30987788', '12345678', 3, 'francesca@gmail.com', 'Franchesca Izquierdo'),
(21, '31466704', '12345678', 4, 'sistemasuarez4@gmail.com', 'Eduar Suarez'),
(23, '30000000', '12345678', 2, '', 'Ezequiel Angulo'),
(27, '12345678', '12345678', 1, 'lolsapo@gmail.com', 'Hernandez Hector'),
(28, '87654321', '12345678', 2, 'juanjuan@gmail.juan', 'JUan juanito');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_super`
--

CREATE TABLE `usuario_super` (
  `id_usuario` int(11) NOT NULL,
  `claveSuper` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario_super`
--

INSERT INTO `usuario_super` (`id_usuario`, `claveSuper`) VALUES
(1, 'loljajaqmal'),
(27, '12345678');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `codigo_recuperacion`
--
ALTER TABLE `codigo_recuperacion`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `config_pag`
--
ALTER TABLE `config_pag`
  ADD PRIMARY KEY (`id_config`);

--
-- Indices de la tabla `director`
--
ALTER TABLE `director`
  ADD PRIMARY KEY (`ced_dir`);

--
-- Indices de la tabla `movimiento`
--
ALTER TABLE `movimiento`
  ADD PRIMARY KEY (`id_producto`,`id_solicitud`),
  ADD KEY `id_solic_movm_fk` (`id_solicitud`),
  ADD KEY `id_prod_mov_fk` (`id_prod`);

--
-- Indices de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD PRIMARY KEY (`id_notif`),
  ADD KEY `id_tipo_notif_fk` (`tipo`);

--
-- Indices de la tabla `oficina`
--
ALTER TABLE `oficina`
  ADD PRIMARY KEY (`num_oficina`),
  ADD KEY `ced_dir_fk` (`ced_dir`);

--
-- Indices de la tabla `ofic_usuario`
--
ALTER TABLE `ofic_usuario`
  ADD PRIMARY KEY (`num_oficina`,`id_usuario`),
  ADD KEY `id_usuario_ofic_fk` (`id_usuario`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_tipo_fk` (`id_tipo`);

--
-- Indices de la tabla `prod_solic`
--
ALTER TABLE `prod_solic`
  ADD PRIMARY KEY (`id_solicitud`,`num_linea`),
  ADD KEY `id_tipo_solic_fk` (`id_tipo`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`rif`);

--
-- Indices de la tabla `prov_recomendaciones`
--
ALTER TABLE `prov_recomendaciones`
  ADD PRIMARY KEY (`rif_proveedor`,`id_tipo`),
  ADD KEY `fk_tipo` (`id_tipo`);

--
-- Indices de la tabla `receptor_notif`
--
ALTER TABLE `receptor_notif`
  ADD PRIMARY KEY (`id_usuario`,`id_notif`),
  ADD KEY `id_notif_rec_notif_fk` (`id_notif`);

--
-- Indices de la tabla `rol_usuario`
--
ALTER TABLE `rol_usuario`
  ADD PRIMARY KEY (`id_cargo`);

--
-- Indices de la tabla `servicio_proveedor`
--
ALTER TABLE `servicio_proveedor`
  ADD PRIMARY KEY (`id_tipo`,`rif_prov`),
  ADD KEY `rif_prov_serv_fk` (`rif_prov`);

--
-- Indices de la tabla `solicitud`
--
ALTER TABLE `solicitud`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `num_oficina_solic_fk` (`num_oficina`);

--
-- Indices de la tabla `tipo_notif`
--
ALTER TABLE `tipo_notif`
  ADD PRIMARY KEY (`id_tipo_notif`);

--
-- Indices de la tabla `tipo_prod`
--
ALTER TABLE `tipo_prod`
  ADD PRIMARY KEY (`id_tipo`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `fk_usuario_cargo` (`id_cargo`);

--
-- Indices de la tabla `usuario_super`
--
ALTER TABLE `usuario_super`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `config_pag`
--
ALTER TABLE `config_pag`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  MODIFY `id_notif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `rol_usuario`
--
ALTER TABLE `rol_usuario`
  MODIFY `id_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `solicitud`
--
ALTER TABLE `solicitud`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `tipo_prod`
--
ALTER TABLE `tipo_prod`
  MODIFY `id_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `codigo_recuperacion`
--
ALTER TABLE `codigo_recuperacion`
  ADD CONSTRAINT `id_user_code_fk` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `movimiento`
--
ALTER TABLE `movimiento`
  ADD CONSTRAINT `id_prod_fk` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`),
  ADD CONSTRAINT `id_prod_mov_fk` FOREIGN KEY (`id_prod`) REFERENCES `producto` (`id_producto`),
  ADD CONSTRAINT `id_solic_movm_fk` FOREIGN KEY (`id_solicitud`) REFERENCES `solicitud` (`id_solicitud`);

--
-- Filtros para la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD CONSTRAINT `id_tipo_notif_fk` FOREIGN KEY (`tipo`) REFERENCES `tipo_notif` (`id_tipo_notif`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `oficina`
--
ALTER TABLE `oficina`
  ADD CONSTRAINT `ced_dir_fk` FOREIGN KEY (`ced_dir`) REFERENCES `director` (`ced_dir`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `ofic_usuario`
--
ALTER TABLE `ofic_usuario`
  ADD CONSTRAINT `id_usuario_ofic_fk` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `num_oficina_fk` FOREIGN KEY (`num_oficina`) REFERENCES `oficina` (`num_oficina`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `id_tipo_fk` FOREIGN KEY (`id_tipo`) REFERENCES `tipo_prod` (`id_tipo`);

--
-- Filtros para la tabla `prod_solic`
--
ALTER TABLE `prod_solic`
  ADD CONSTRAINT `id_solic_fk` FOREIGN KEY (`id_solicitud`) REFERENCES `solicitud` (`id_solicitud`) ON DELETE CASCADE,
  ADD CONSTRAINT `id_tipo_solic_fk` FOREIGN KEY (`id_tipo`) REFERENCES `tipo_prod` (`id_tipo`);

--
-- Filtros para la tabla `prov_recomendaciones`
--
ALTER TABLE `prov_recomendaciones`
  ADD CONSTRAINT `fk_prov` FOREIGN KEY (`rif_proveedor`) REFERENCES `proveedor` (`rif`),
  ADD CONSTRAINT `fk_tipo` FOREIGN KEY (`id_tipo`) REFERENCES `tipo_prod` (`id_tipo`);

--
-- Filtros para la tabla `receptor_notif`
--
ALTER TABLE `receptor_notif`
  ADD CONSTRAINT `id_notif_rec_notif_fk` FOREIGN KEY (`id_notif`) REFERENCES `notificacion` (`id_notif`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_usuario_rec_notif_fk` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `servicio_proveedor`
--
ALTER TABLE `servicio_proveedor`
  ADD CONSTRAINT `id_tipo_serv_fk` FOREIGN KEY (`id_tipo`) REFERENCES `tipo_prod` (`id_tipo`),
  ADD CONSTRAINT `rif_prov_serv_fk` FOREIGN KEY (`rif_prov`) REFERENCES `proveedor` (`rif`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitud`
--
ALTER TABLE `solicitud`
  ADD CONSTRAINT `num_oficina_solic_fk` FOREIGN KEY (`num_oficina`) REFERENCES `oficina` (`num_oficina`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_cargo` FOREIGN KEY (`id_cargo`) REFERENCES `rol_usuario` (`id_cargo`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario_super`
--
ALTER TABLE `usuario_super`
  ADD CONSTRAINT `id_user_fk` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
