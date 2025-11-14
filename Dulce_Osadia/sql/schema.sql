-- Esquema de base de datos para el proyecto Dulce Osadia
-- Generado a partir del análisis del código fuente

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS `dulce_osadia` CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `dulce_osadia`;

-- Tabla: producto
CREATE TABLE IF NOT EXISTS `producto` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` TEXT,
  `img` VARCHAR(255),
  `precio` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `stock` INT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla: clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombres` VARCHAR(150) NOT NULL,
  `apellidos` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `telefono` VARCHAR(30),
  `dni` VARCHAR(50),
  `estatus` TINYINT(1) NOT NULL DEFAULT 1,
  `fecha_Alta` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_clientes_email` (`email`),
  UNIQUE KEY `uniq_clientes_dni` (`dni`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla: usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `usuario` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) DEFAULT NULL,
  `id_cliente` INT UNSIGNED NOT NULL,
  `activacion` TINYINT(1) NOT NULL DEFAULT 0,
  `token_password` VARCHAR(255) DEFAULT NULL,
  `password_request` TINYINT(1) NOT NULL DEFAULT 0,
  UNIQUE KEY `uniq_usuarios_usuario` (`usuario`),
  KEY `idx_usuarios_id_cliente` (`id_cliente`),
  CONSTRAINT `fk_usuarios_clientes` FOREIGN KEY (`id_cliente`) REFERENCES `clientes`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla: compra
CREATE TABLE IF NOT EXISTS `compra` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `id_transaccion` VARCHAR(100) NOT NULL,
  `fecha` DATETIME NOT NULL,
  `status` VARCHAR(50) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `id_cliente` INT UNSIGNED NOT NULL,
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  UNIQUE KEY `uniq_compra_id_transaccion` (`id_transaccion`),
  KEY `idx_compra_id_cliente` (`id_cliente`),
  CONSTRAINT `fk_compra_clientes` FOREIGN KEY (`id_cliente`) REFERENCES `clientes`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla: detalle_compra
CREATE TABLE IF NOT EXISTS `detalle_compra` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `id_compra` INT UNSIGNED NOT NULL,
  `id_producto` INT UNSIGNED NOT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `precio` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `cantidad` INT UNSIGNED NOT NULL DEFAULT 1,
  KEY `idx_detalle_compra_compra` (`id_compra`),
  KEY `idx_detalle_compra_producto` (`id_producto`),
  CONSTRAINT `fk_detalle_compra_compra` FOREIGN KEY (`id_compra`) REFERENCES `compra`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detalle_compra_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Notas:
-- 1) `clientes.fecha_Alta` mantiene el nombre usado en el código.
-- 2) `usuarios` incluye campos para activación y recuperación de contraseña.
-- 3) `compra.id_transaccion` se marca como único para evitar duplicados.
-- 4) Ajusta tamaños de VARCHAR/DECIMAL según tus necesidades reales.