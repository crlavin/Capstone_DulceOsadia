-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-10-2025 a las 15:49:03
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dulceosadia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `atributo_insumo`
--

CREATE TABLE `atributo_insumo` (
  `id_atributo` int(11) NOT NULL,
  `nombre_atributo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallereceta`
--

CREATE TABLE `detallereceta` (
  `id_receta` int(11) NOT NULL,
  `id_insumo` int(11) NOT NULL,
  `cantidad_usada` varchar(20) DEFAULT NULL,
  `unidad` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detallereceta`
--

INSERT INTO `detallereceta` (`id_receta`, `id_insumo`, `cantidad_usada`, `unidad`) VALUES
(1, 1, '7.8000', 'gramos'),
(1, 4, '7.8000', 'gramos'),
(1, 9, '0.4000', 'gramos'),
(2, 2, '7.0000', 'gramos'),
(2, 6, '10.0000', 'gramos'),
(2, 7, '6.0000', 'gramos'),
(3, 1, '6', 'gramos'),
(3, 2, '5', 'gramos'),
(3, 5, '8', 'gramos'),
(3, 9, '1', 'gramos'),
(4, 2, '5', 'gramos'),
(4, 7, '15', 'gramos'),
(4, 9, '1', 'gramos'),
(5, 2, '12', 'gramos'),
(5, 7, '40', 'gramos'),
(5, 11, '14', 'gramos'),
(6, 1, '12', 'gramos'),
(6, 7, '40', 'gramos'),
(6, 11, '14', 'gramos'),
(7, 1, '12', 'gramos'),
(7, 11, '21', 'gramos'),
(7, 12, '35', 'gramos'),
(8, 2, '12', 'gramos'),
(8, 11, '21', 'gramos'),
(8, 12, '35', 'gramos'),
(10, 3, '4', 'gramos'),
(10, 6, '8', 'gramos'),
(10, 8, '5', 'gramos'),
(11, 7, '9.82', 'gramos'),
(11, 10, '8.18', 'gramos'),
(11, 13, '3', 'gramos'),
(12, 2, '4.24', 'gramos'),
(12, 7, '8.47', 'gramos'),
(12, 10, '4.24', 'gramos'),
(12, 14, '1.06', 'ML'),
(15, 1, '10.7', 'gramos'),
(15, 2, '70', 'gramos'),
(15, 9, '3.55', 'gramos'),
(15, 16, '35.7', 'gramos'),
(16, 1, '6.42', 'gramos'),
(16, 2, '40', 'gramos'),
(16, 9, '2.13', 'gramos'),
(16, 16, '21.42', 'gramos'),
(17, 2, '4.49', 'gramos'),
(17, 7, '8.98', 'gramos'),
(17, 10, '4.49', 'gramos'),
(17, 17, '0.045', 'ML');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `familiado`
--

CREATE TABLE `familiado` (
  `id_familiaDO` int(11) NOT NULL,
  `nombre_familia` varchar(100) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `familiado`
--

INSERT INTO `familiado` (`id_familiaDO`, `nombre_familia`, `descripcion`) VALUES
(1, 'Bombones', 'Bombones rellenos, decorados y bañados en chocolate'),
(2, 'Alfajores', 'Galletas rellenas de manjar o mermelada, bañadas en chocolate'),
(3, 'Sin Azúcar', 'Productos elaborados sin azúcar añadida'),
(4, 'Premium', 'Bombones y barras con ingredientes gourmet'),
(5, 'Cajas Variadas', 'Mix de bombones y trufas en presentaciones múltiples');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumos`
--

CREATE TABLE `insumos` (
  `id_insumo` int(11) NOT NULL,
  `SKU` varchar(20) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `unidad_med` varchar(20) DEFAULT NULL,
  `cantidadActual` decimal(10,2) DEFAULT NULL,
  `cantidadMinima` decimal(10,2) DEFAULT NULL,
  `precioUnitario` decimal(10,2) DEFAULT NULL,
  `proveedorId` int(11) DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL,
  `observaciones` varchar(255) DEFAULT NULL,
  `id_familiaDO` int(11) DEFAULT NULL,
  `id_proveedor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `insumos`
--

INSERT INTO `insumos` (`id_insumo`, `SKU`, `nombre`, `descripcion`, `categoria`, `unidad_med`, `cantidadActual`, `cantidadMinima`, `precioUnitario`, `proveedorId`, `fecha_ingreso`, `fecha_vencimiento`, `estado`, `observaciones`, `id_familiaDO`, `id_proveedor`) VALUES
(1, 'CHB001', 'Chocolate blanco Caravella', NULL, 'Chocolate', 'gramos', 5000.00, NULL, 6100.00, NULL, '0000-00-00', '0000-00-00', 'activo', NULL, NULL, NULL),
(2, 'CHN002', 'Chocolate negro Costa', NULL, 'Chocolate', 'gramos', 5000.00, NULL, 5800.00, NULL, '2025-09-02', '2026-02-02', 'activo', NULL, NULL, NULL),
(3, 'CHA003', 'Chocolate amargo sin azúcar Neucober', NULL, 'Chocolate sin azúcar', 'gramos', 3000.00, NULL, 8800.00, NULL, '2025-09-02', '2026-02-02', 'activo', NULL, NULL, NULL),
(4, 'MAN004', 'Maní sin sal', NULL, 'Frutos secos', 'gramos', 5000.00, NULL, 3000.00, NULL, '2025-09-02', '2026-05-02', 'activo', NULL, NULL, NULL),
(5, 'CRE005', 'Crema de avellanas Halta', NULL, 'Bases', 'gramos', 1500.00, NULL, 5500.00, NULL, '2025-09-29', '2025-10-20', 'activo', NULL, NULL, NULL),
(6, 'NUE006', 'Nueces', NULL, 'Frutos secos', 'gramos', 2000.00, NULL, 9000.00, NULL, '2025-09-02', '2025-12-02', 'activo', NULL, NULL, NULL),
(7, 'MAN007', 'Manjar Colun', NULL, 'Bases', 'gramos', 10000.00, NULL, 2700.00, NULL, '2025-09-02', '2026-02-02', 'activo', NULL, NULL, NULL),
(8, 'MAN008', 'Manjar sin azúcar Langer', NULL, 'Base sin azúcar', 'gramos', 3000.00, NULL, 5890.00, NULL, '2025-09-02', '2026-02-02', 'activo', NULL, NULL, NULL),
(9, 'VAI009', 'Vaina de trigo Conebric', NULL, 'Bases', 'gramos', 1200.00, NULL, 16600.00, NULL, '2025-09-02', '2026-04-02', 'activo', NULL, NULL, NULL),
(10, 'GAL010', 'Galletas Fruna', NULL, 'Bases', 'gramos', 3000.00, NULL, 2133.00, NULL, '2025-09-02', '2026-06-02', 'activo', NULL, NULL, NULL),
(11, 'GAL011', 'Galletas Alfajor del Valle', NULL, 'Bases', 'gramos', 8400.00, NULL, 4430.00, NULL, '2025-09-02', '2025-12-02', 'activo', NULL, NULL, NULL),
(12, 'MER012', 'Mermelada de frambuesa Langer', NULL, 'Bases', 'gramos', 3000.00, NULL, 2500.00, NULL, '0000-00-00', '0000-00-00', 'activo', NULL, NULL, NULL),
(13, 'COC013', 'Coco rallado', NULL, 'Decoración', 'gramos', 3000.00, NULL, 7000.00, NULL, '0000-00-00', '0000-00-00', 'activo', NULL, NULL, NULL),
(14, 'RON014', 'Ron (esencia)', NULL, 'Esencias', 'ml', 500.00, NULL, 6560.00, NULL, '2025-10-02', '2026-10-02', 'activo', NULL, NULL, NULL),
(15, 'LEC015', 'Leche condensada', NULL, 'Bases', 'gramos', 3000.00, NULL, 4000.00, NULL, '2025-09-02', '2026-05-02', 'activo', NULL, NULL, NULL),
(16, 'PIS016', 'Pistacho', NULL, 'Frutos secos', 'gramos', 2000.00, NULL, 32000.00, NULL, '0000-00-00', '0000-00-00', 'activo', NULL, NULL, NULL),
(17, 'NAR017', 'Naranja (esencia)', NULL, 'Esencias', 'ml', 50.00, NULL, 100000.00, NULL, '0000-00-00', '0000-00-00', 'activo', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `SKU` int(11) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `unidad_medida_venta` varchar(20) DEFAULT NULL,
  `peso_promedio_gramos` varchar(20) DEFAULT NULL,
  `precioUnitario` decimal(10,1) DEFAULT NULL,
  `id_familiaDO` int(11) NOT NULL,
  `id_receta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id_producto`, `nombre`, `SKU`, `descripcion`, `unidad_medida_venta`, `peso_promedio_gramos`, `precioUnitario`, `id_familiaDO`, `id_receta`) VALUES
(1, 'Bombón crema de maní', 2025001, 'Bombón elaborado con chocolate blanco y crema de maní y crocante de barquillo. Bañado en chocolate', 'unidad', '16.0000', 400.0, 1, 1),
(2, 'Nuez Rellena', 2025002, 'Nueces rellenas en su centro con manjar de leche y bañadas en chocolate', 'unidad', '20.0000', 400.0, 1, 2),
(3, 'Bombón de avellana', 2025003, 'Bombón elaborado con chocolate y crema de avellanas con una avellana entera en el centro.', 'unidad', '22.0000', 500.0, 1, 3),
(4, 'Cuchuflíe', 2025004, 'Vaina de trigo rellena con manjar de leche y bañada en chocolate blanco o negro', 'unidad', '25.0000', 333.0, 1, 4),
(5, 'Alfajor Tradicional', 2025005, 'Galletas de alfajor con relleno de manjar de leche bañado en chocolate negro', 'unidad', '45.0000', 800.0, 2, 5),
(6, 'Alfajor Tradicional Blanco', 2025006, 'Galletas de alfajor con relleno de manjar de leche bañado en chocolate blanco', 'unidad', '45.0000', 800.0, 2, 6),
(7, 'Alfajor Frambuesa Blanco', 2025007, 'Galletas de alfajor con relleno de mermelada de frambuesa bañado en chocolate blanco', 'unidad', '45.0000', 800.0, 2, 7),
(8, 'Alfajor Frambuesa Negro', 2025008, 'Galletas de alfajor con relleno de mermelada de frambuesa bañado en chocolate negro', 'unidad', '45.0000', 800.0, 2, 8),
(9, 'Chocolates sin azúcar', 2025009, 'Bombón base de manjar y chocolate sin azúcar espolvoreado con cacao natural', 'caja', '18.0000', 3500.0, 3, 9),
(10, 'Nuez rellena sin azúcar', 2025010, 'Nuez rellena de manjar sin azúcar y bañada en chocolate amargo sin azúcar', 'caja', '17.0000', 3500.0, 3, 10),
(11, 'Cocadas', 2025011, 'Masa elaborada con manjar de leche y harina de galletas espolvoreado con coco rallado', 'caja', '18.0000', 2000.0, 5, 11),
(12, 'Trufas con sabor a ron', 2025012, 'Masa elaborada con manjar de leche, harina de galletas, esencia de ron y cobertura de chocolate decorado con palitos de chocolate', 'caja', '18.0000', 2000.0, 5, 12),
(13, 'Prestigio de coco', 2025013, 'Barra de coco y leche condensada bañada en chocolate', 'pack', '22.0000', 1000.0, 1, 13),
(14, 'Mix de bombones', 2025014, 'Pote de cocadas, trufas sabor ron, bombón de maní y nueces rellenas', 'caja', '20.0000', 3500.0, 5, 14),
(15, 'Barra de chocolate tipo Dubai', 2025015, 'Barra de chocolate rellena de pasta de pistacho crocante', 'unidad', '200.0000', 4500.0, 4, 15),
(16, 'Mini Barra de chocolate tipo Dubai', 2025016, 'MINI Barra de chocolate rellena de pasta de pistacho crocante', 'unidad', '45.0000', 1500.0, 4, 16),
(17, 'Trufas sabor naranja', 2025017, 'Trufa de chocolate con sabor a naranja ', 'unidad', '18.00', 1000.0, 5, 17);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id_proveedor` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id_proveedor`, `nombre`, `direccion`, `telefono`, `email`) VALUES
(1, 'gapicomercial', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `receta`
--

CREATE TABLE `receta` (
  `id_receta` int(11) NOT NULL,
  `nombre_receta` varchar(100) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `rendimiento_total` decimal(10,4) DEFAULT NULL,
  `unidad_rendimiento` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `receta`
--

INSERT INTO `receta` (`id_receta`, `nombre_receta`, `descripcion`, `rendimiento_total`, `unidad_rendimiento`) VALUES
(1, 'Receta Bombón crema de maní', 'Chocolate blanco, crema de maní, barquillo', 100.0000, 'unidades'),
(2, 'Receta Nuez Rellena', 'Nuez, manjar, chocolate', 100.0000, 'unidades'),
(3, 'Receta Bombón de avellana', 'Chocolate, crema de avellanas, avellana entera', 100.0000, 'unidades'),
(4, 'Receta Cuchuflíe', 'Vaina de trigo, manjar, chocolate', 100.0000, 'unidades'),
(5, 'Receta Alfajor Tradicional', 'Galletas, manjar, chocolate negro', 100.0000, 'unidades'),
(6, 'Receta Alfajor Blanco', 'Galletas, manjar, chocolate blanco', 100.0000, 'unidades'),
(7, 'Receta Alfajor Frambuesa Blanco', 'Galletas, mermelada frambuesa, chocolate blanco', 100.0000, 'unidades'),
(8, 'Receta Alfajor Frambuesa Negro', 'Galletas, mermelada frambuesa, chocolate negro', 100.0000, 'unidades'),
(9, 'Receta Chocolates sin azúcar', 'Manjar sin azúcar, chocolate sin azúcar', 100.0000, 'unidades'),
(10, 'Receta Nuez sin azúcar', 'Nuez, manjar sin azúcar, chocolate amargo sin azúcar', 100.0000, 'unidades'),
(11, 'Receta Cocadas', 'Manjar, galletas, coco rallado', 100.0000, 'unidades'),
(12, 'Receta Trufas ron', 'Manjar, galletas, ron, chocolate', 100.0000, 'unidades'),
(13, 'Receta Prestigio de coco', 'Coco, leche condensada, chocolate', 100.0000, 'unidades'),
(14, 'Receta Mix bombones', 'Cocadas, trufas, bombón maní, nueces rellenas', 100.0000, 'unidades'),
(15, 'Receta Dubai', 'Chocolate, pasta de pistacho crocante', 100.0000, 'unidades'),
(16, 'Receta Mini Dubai', 'Chocolate, pasta de pistacho crocante', 100.0000, 'unidades'),
(17, 'Trufas de naranja', 'Manjar, galletas, naranja, chocolate', 100.0000, 'unidades');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `idusuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `rol` varchar(20) DEFAULT 'usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`idusuario`, `nombre`, `correo`, `usuario`, `password`, `apellido`, `rol`) VALUES
(1, 'Martin', 'martintru@mail.cl', 'martincho', '$2y$10$3H65xFw7xuz2jg23dTRDJeBoHfTiP7306568ua1.j8hodT.Dbs2iu', '', 'usuario'),
(4, 'Donny', 'donnytru@mail.cl', 'martincho2', '$2y$10$WHZWyfRgDc/Z30C9I8UtV.RJFZVperLNydWz6RkvxnZBd7o8cX4f.', '', 'usuario'),
(5, 'alfredo', 'alfredogarcia@mail.com', 'alfredo', '$2y$10$D0USh8bjL/rlCPS2S5zjLuWDH102HsPiicNHv.XnB6ukJ4zfggpzG', '', 'usuario'),
(6, 'hola', 'prueba@mail.com', 'prueba', '$2y$10$3OH67OnQQdU.wYPNUY4MduaFm4KHf8PVteWPeirKodCg7gFUhx.JO', '', 'usuario'),
(8, 'Admin', 'admindulceosadia@mail.cl', 'admin', '$2y$10$m8vrz705eaPSIAxWT7XmquKa3qHpuFnH5Rn..fY.E9X47DEf24psK', '', 'admin'),
(9, 'cristian', 'cr.lavin@mail.com', 'crlavin', '$2y$10$bS52IqGHZIR1UMDmbbL7GeeVkB6FTXf8TD1SQpHzwEZp8wXwdA2ii', '', 'usuario'),
(10, 'juanito', 'juanito@mail.com', 'juanito', '$2y$10$TfolxTLCrtLQcF0qvzm8mOz3nqkPxH1FZQ5jlvnXE9lNXgXl6oGwe', '', 'usuario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `valor_atributo_insumo`
--

CREATE TABLE `valor_atributo_insumo` (
  `id_insumo` int(11) NOT NULL,
  `id_atributo` int(11) NOT NULL,
  `valor` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `atributo_insumo`
--
ALTER TABLE `atributo_insumo`
  ADD PRIMARY KEY (`id_atributo`);

--
-- Indices de la tabla `detallereceta`
--
ALTER TABLE `detallereceta`
  ADD PRIMARY KEY (`id_receta`,`id_insumo`),
  ADD KEY `id_insumo` (`id_insumo`);

--
-- Indices de la tabla `familiado`
--
ALTER TABLE `familiado`
  ADD PRIMARY KEY (`id_familiaDO`);

--
-- Indices de la tabla `insumos`
--
ALTER TABLE `insumos`
  ADD PRIMARY KEY (`id_insumo`),
  ADD KEY `proveedorId` (`proveedorId`),
  ADD KEY `id_familiaDO` (`id_familiaDO`),
  ADD KEY `id_proveedor` (`id_proveedor`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_familiaDO` (`id_familiaDO`),
  ADD KEY `id_receta` (`id_receta`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- Indices de la tabla `receta`
--
ALTER TABLE `receta`
  ADD PRIMARY KEY (`id_receta`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idusuario`);

--
-- Indices de la tabla `valor_atributo_insumo`
--
ALTER TABLE `valor_atributo_insumo`
  ADD KEY `id_insumo` (`id_insumo`),
  ADD KEY `id_atributo` (`id_atributo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `atributo_insumo`
--
ALTER TABLE `atributo_insumo`
  MODIFY `id_atributo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `familiado`
--
ALTER TABLE `familiado`
  MODIFY `id_familiaDO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `insumos`
--
ALTER TABLE `insumos`
  MODIFY `id_insumo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `receta`
--
ALTER TABLE `receta`
  MODIFY `id_receta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detallereceta`
--
ALTER TABLE `detallereceta`
  ADD CONSTRAINT `detallereceta_ibfk_1` FOREIGN KEY (`id_receta`) REFERENCES `receta` (`id_receta`);

--
-- Filtros para la tabla `insumos`
--
ALTER TABLE `insumos`
  ADD CONSTRAINT `insumos_ibfk_1` FOREIGN KEY (`proveedorId`) REFERENCES `proveedores` (`id_proveedor`),
  ADD CONSTRAINT `insumos_ibfk_2` FOREIGN KEY (`id_familiaDO`) REFERENCES `familiado` (`id_familiaDO`),
  ADD CONSTRAINT `insumos_ibfk_3` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`);

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`id_familiaDO`) REFERENCES `familiado` (`id_familiaDO`),
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`id_receta`) REFERENCES `receta` (`id_receta`);

--
-- Filtros para la tabla `valor_atributo_insumo`
--
ALTER TABLE `valor_atributo_insumo`
  ADD CONSTRAINT `valor_atributo_insumo_ibfk_1` FOREIGN KEY (`id_insumo`) REFERENCES `insumos` (`id_insumo`),
  ADD CONSTRAINT `valor_atributo_insumo_ibfk_2` FOREIGN KEY (`id_atributo`) REFERENCES `atributo_insumo` (`id_atributo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
