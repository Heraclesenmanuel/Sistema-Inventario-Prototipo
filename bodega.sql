-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-10-2025 a las 21:58:44
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
-- Base de datos: `bodega`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `claveSuper` varchar(100) NOT NULL,
  `NombreAPP` varchar(100) NOT NULL,
  `precio_dollar` double(100,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `admin`
--

INSERT INTO `admin` (`id`, `claveSuper`, `NombreAPP`, `precio_dollar`) VALUES
(1, 'Heraclesjaja', 'Oficinas - UPEL', 190.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre_apellido` varchar(100) NOT NULL,
  `cedula` varchar(100) NOT NULL,
  `telefono` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre_apellido`, `cedula`, `telefono`) VALUES
(1, 'Heracles Sanchecito', '31987430', '04245354900'),
(2, 'Erika', '55555555', '033334445555555');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentascobrar`
--

CREATE TABLE `cuentascobrar` (
  `id_historial` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `cliente` varchar(100) NOT NULL,
  `tipo_pago` varchar(100) NOT NULL,
  `tipo_venta` varchar(100) NOT NULL,
  `total_usd` decimal(10,2) NOT NULL,
  `productos_vendidos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`productos_vendidos`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuentascobrar`
--

INSERT INTO `cuentascobrar` (`id_historial`, `fecha`, `cliente`, `tipo_pago`, `tipo_venta`, `total_usd`, `productos_vendidos`) VALUES
(1, '2025-10-15', '4', '1', '1', 20.00, '2'),
(2, '2025-10-15', 'Heracles Sanchecito - 31987430', 'credito', 'parcial', 12.20, '[{\"id\":1,\"nombre\":\"juan\",\"codigo\":\"12334\",\"cantidad\":1,\"precio_usd\":97.2,\"medida\":\"juanes\"}]'),
(3, '2025-10-17', 'Heracles Sanchecito - 31987430', 'credito', 'parcial', 577.20, '[{\"id\":1,\"nombre\":\"juan\",\"codigo\":\"12334\",\"cantidad\":6,\"precio_usd\":97.2,\"medida\":\"juanes\"}]');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial`
--

CREATE TABLE `historial` (
  `id_historial` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `cliente` varchar(100) NOT NULL,
  `tipo_pago` varchar(100) NOT NULL,
  `tipo_venta` varchar(100) NOT NULL,
  `total_usd` decimal(10,2) NOT NULL,
  `productos_vendidos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`productos_vendidos`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial`
--

INSERT INTO `historial` (`id_historial`, `fecha`, `cliente`, `tipo_pago`, `tipo_venta`, `total_usd`, `productos_vendidos`) VALUES
(1, '2025-10-15', '2', '1', '1', 200.00, '3'),
(2, '2025-10-15', 'Heracles Sanchecito - 31987430', 'efectivo', 'contado', 291.60, '[{\"id\":1,\"nombre\":\"juan\",\"codigo\":\"12334\",\"cantidad\":3,\"precio_usd\":97.2,\"medida\":\"juanes\"}]'),
(3, '2025-10-15', 'Heracles Sanchecito - 31987430', 'efectivo', 'contado', 291.60, '[{\"id\":1,\"nombre\":\"juan\",\"codigo\":\"12334\",\"cantidad\":3,\"precio_usd\":97.2,\"medida\":\"juanes\"}]'),
(4, '2025-10-15', 'Heracles Sanchecito - 31987430', 'efectivo', 'contado', 680.40, '[{\"id\":1,\"nombre\":\"juan\",\"codigo\":\"12334\",\"cantidad\":7,\"precio_usd\":97.2,\"medida\":\"juanes\"}]'),
(5, '2025-10-15', 'Heracles Sanchecito - 31987430', 'credito', 'credito', 97.20, '[{\"id\":1,\"nombre\":\"juan\",\"codigo\":\"12334\",\"cantidad\":1,\"precio_usd\":97.2,\"medida\":\"juanes\"}]'),
(6, '2025-10-17', 'Heracles Sanchecito - 31987430', 'efectivo', 'contado', 874.80, '[{\"id\":1,\"nombre\":\"juan\",\"codigo\":\"12334\",\"cantidad\":9,\"precio_usd\":97.2,\"medida\":\"juanes\"}]'),
(7, '2025-10-17', 'Heracles Sanchecito - 31987430', 'credito', 'credito', 583.20, '[{\"id\":1,\"nombre\":\"juan\",\"codigo\":\"12334\",\"cantidad\":6,\"precio_usd\":97.2,\"medida\":\"juanes\"}]');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inf_usuarios`
--

CREATE TABLE `inf_usuarios` (
  `id` int(11) NOT NULL,
  `cedula` varchar(100) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `id_cargo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inf_usuarios`
--

INSERT INTO `inf_usuarios` (`id`, `cedula`, `clave`, `id_cargo`, `nombre`) VALUES
(2, '31987430', 'Heraclesjaja', 1, 'Heraculo'),
(3, '30000000', 'Heraclesjaja', 1, 'Hejeeeee'),
(4, '00000000', 'Fiuuuu', 1, 'FRANCHESCO VIRGOLINI'),
(9, '3178940333', 'asasass', 1, 'loljaja');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id_producto` int(11) NOT NULL,
  `codigo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `un_disponibles` int(11) DEFAULT 0,
  `precio_compra` decimal(10,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `medida` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id_producto`, `codigo`, `nombre`, `un_disponibles`, `precio_compra`, `precio_venta`, `medida`) VALUES
(1, 12334, 'juan', 74, 4.00, 97.20, 'juanes'),
(4, 121212, 'erererer', 4, 0.23, 0.25, 'ewrfewifcoewi'),
(7, 12121244, 'juan', 232, 32.00, 3232.00, 'wqdwq');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id_proveedor` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(100) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `nombre_encargado` varchar(100) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `nota` varchar(100) NOT NULL,
  `nombre_proveedor` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id_proveedor`, `email`, `telefono`, `direccion`, `nombre_encargado`, `estado`, `nota`, `nombre_proveedor`) VALUES
(1, 'SANCHEZHERACLES@GMAIL.COM', '04245354900', 'Av. Ribereña', 'Luisito Comunica', 'Activo', 'Listop', 'LUIS HITO COMUNI KA');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `cuentascobrar`
--
ALTER TABLE `cuentascobrar`
  ADD PRIMARY KEY (`id_historial`);

--
-- Indices de la tabla `historial`
--
ALTER TABLE `historial`
  ADD PRIMARY KEY (`id_historial`);

--
-- Indices de la tabla `inf_usuarios`
--
ALTER TABLE `inf_usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id_producto`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `cuentascobrar`
--
ALTER TABLE `cuentascobrar`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `historial`
--
ALTER TABLE `historial`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `inf_usuarios`
--
ALTER TABLE `inf_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
