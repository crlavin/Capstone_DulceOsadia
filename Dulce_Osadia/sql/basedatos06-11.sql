CREATE database dulceosadia;
USE dulceosadia;
-- Se creara la tabla presentaciones_venta
CREATE TABLE `presentaciones_venta` (
  `id_presentacion` INT AUTO_INCREMENT PRIMARY KEY,
  `id_producto` INT NOT NULL,
  `nombre_presentacion` VARCHAR(150) NOT NULL,
  `SKU` VARCHAR(50) UNIQUE,
  `unidades_por_paquete` INT NOT NULL,
  `precio_venta` DECIMAL(10, 2) NULL, -- <<-- Se cambia a NULL para permitir valores nulos
  `estado` VARCHAR(20) DEFAULT 'Activo',
  FOREIGN KEY (`id_producto`) REFERENCES `producto`(`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
--  se insertan datos 
INSERT INTO presentaciones_venta (id_producto, nombre_presentacion, SKU, unidades_por_paquete, precio_venta)
SELECT
  id_producto,
  CONCAT(nombre, ' - ', unidad_medida_venta),
  SKU,
  CASE id_producto
    WHEN 1 THEN 3
    WHEN 2 THEN 5
    WHEN 3 THEN 1
    WHEN 4 THEN 7
    WHEN 5 THEN 1
    WHEN 6 THEN 1
    WHEN 7 THEN 1
    WHEN 8 THEN 1
    WHEN 9 THEN 8
    WHEN 10 THEN 8
    WHEN 11 THEN 13
    WHEN 12 THEN 11
    WHEN 13 THEN 2
    WHEN 14 THEN 11
    WHEN 15 THEN 1
    WHEN 16 THEN 1
    WHEN 17 THEN 4
    ELSE 1
  END,
  NULL -- <<<<<<<<<<<<<<<< ESTE ES EL CAMBIO PRINCIPAL
FROM producto;

SET FOREIGN_KEY_CHECKS = 0;

-- En la tabla producto se eliminaran algunos atributos
ALTER TABLE producto
DROP COLUMN SKU,
DROP COLUMN precioUnitario,
DROP COLUMN unidad_medida_venta;


ALTER TABLE insumos
ADD COLUMN ultima_actualizacion TIMESTAMP 
DEFAULT CURRENT_TIMESTAMP 
ON UPDATE CURRENT_TIMESTAMP;



CREATE TABLE proveedores (
  id_proveedor INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100),
  direccion VARCHAR(150),
  telefono VARCHAR(20),
  email VARCHAR(100)
);
CREATE TABLE familiaDO (
  id_familiaDO INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nombre_familia VARCHAR(100),
  descripcion VARCHAR(255)
);

CREATE TABLE insumos (
  id_insumo INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  SKU INT,
  nombre VARCHAR(100),
  descripcion VARCHAR(255),
  categoria VARCHAR(50),
  unidad_med VARCHAR(20),
  cantidadActual DECIMAL(10,2),
  cantidadMinima DECIMAL(10,2),
  precioUnitario DECIMAL(10,2),
  proveedorId INT NOT NULL,
  fecha_ingreso DATE,
  fecha_vencimiento DATE,
  estado VARCHAR(20),
  observaciones VARCHAR(255),
  id_familiaDO INT NOT NULL,
  FOREIGN KEY (proveedorId) REFERENCES proveedores(id_proveedor),
  FOREIGN KEY (id_familiaDO) REFERENCES familiaDO(id_familiaDO)

);
CREATE TABLE receta (
  id_receta INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nombre_receta VARCHAR(100),
  descripcion VARCHAR(255),
  rendimiento_total DECIMAL(10,4),
  unidad_rendimiento VARCHAR(20)
);
CREATE TABLE detalleReceta (
  id_receta INT NOT NULL,
  id_insumo INT NOT NULL,
  cantidad_usada DECIMAL(10,4),
  unidad VARCHAR(20),
  PRIMARY KEY (id_receta, id_insumo),
  FOREIGN KEY (id_receta) REFERENCES receta(id_receta),
  FOREIGN KEY (id_insumo) REFERENCES insumos(id_insumo)
);
CREATE TABLE producto (
  id_producto INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100),
  SKU INT,
  descripcion VARCHAR(255),
  unidad_medida_venta VARCHAR(20),
  peso_promedio_gramos DECIMAL(10,4),
  precioUnitario DECIMAL(10,4),
  id_familiaDO INT NOT NULL,
  id_receta INT NOT NULL, -- 🔗 NUEVO CAMPO
  FOREIGN KEY (id_familiaDO) REFERENCES familiaDO(id_familiaDO),
  FOREIGN KEY (id_receta) REFERENCES receta(id_receta) -- 🔗 NUEVA RELACIÓN
);

alter table producto

MODIFY peso_promedio_gramos VARCHAR(20),

MODIFY precioUnitario DECIMAL(10,1);
CREATE TABLE atributo_insumo (
  id_atributo INT AUTO_INCREMENT PRIMARY KEY,
  nombre_atributo VARCHAR(50) NOT NULL
);

CREATE TABLE valor_atributo_insumo (
  id_insumo INT NOT NULL,
  id_atributo INT NOT NULL,
  valor VARCHAR(100) NOT NULL,
  FOREIGN KEY (id_insumo) REFERENCES insumos(id_insumo),
  FOREIGN KEY (id_atributo) REFERENCES atributo_insumo(id_atributo)
);




ALTER TABLE producto
ADD CONSTRAINT unique_sku UNIQUE (SKU);
ALTER TABLE producto
DROP INDEX unique_sku;

TRUNCATE TABLE producto;

ALTER TABLE detalleReceta DROP FOREIGN KEY detallereceta_ibfk_2;

TRUNCATE TABLE insumos;


alter table insumos
drop id_familiaDO;
INSERT INTO receta (id_receta, nombre_receta, descripcion, rendimiento_total, unidad_rendimiento) VALUES
(1, 'Receta Bombón crema de maní', 'Chocolate blanco, crema de maní, barquillo', 100, 'unidades'),
(2, 'Receta Nuez Rellena', 'Nuez, manjar, chocolate', 100, 'unidades'),
(3, 'Receta Bombón de avellana', 'Chocolate, crema de avellanas, avellana entera', 100, 'unidades'),
(4, 'Receta Cuchuflíe', 'Vaina de trigo, manjar, chocolate', 100, 'unidades'),
(5, 'Receta Alfajor Tradicional', 'Galletas, manjar, chocolate negro', 100, 'unidades'),
(6, 'Receta Alfajor Blanco', 'Galletas, manjar, chocolate blanco', 100, 'unidades'),
(7, 'Receta Alfajor Frambuesa Blanco', 'Galletas, mermelada frambuesa, chocolate blanco', 100, 'unidades'),
(8, 'Receta Alfajor Frambuesa Negro', 'Galletas, mermelada frambuesa, chocolate negro', 100, 'unidades'),
(9, 'Receta Chocolates sin azúcar', 'Manjar sin azúcar, chocolate sin azúcar', 100, 'unidades'),
(10, 'Receta Nuez sin azúcar', 'Nuez, manjar sin azúcar, chocolate amargo sin azúcar', 100, 'unidades'),
(11, 'Receta Cocadas', 'Manjar, galletas, coco rallado', 100, 'unidades'),
(12, 'Receta Trufas ron', 'Manjar, galletas, ron, chocolate', 100, 'unidades'),
(13, 'Receta Prestigio de coco', 'Coco, leche condensada, chocolate', 100, 'unidades'),
(14, 'Receta Mix bombones', 'Cocadas, trufas, bombón maní, nueces rellenas', 100, 'unidades'),
(15, 'Receta Dubai', 'Chocolate, pasta de pistacho crocante', 100, 'unidades'),
(16, 'Receta Mini Dubai', 'Chocolate, pasta de pistacho crocante', 100, 'unidades');

INSERT INTO receta (id_receta, nombre_receta, descripcion, rendimiento_total, unidad_rendimiento) VALUES
(17, 'Trufas de naranja', 'Manjar, galletas, naranja, chocolate', 100, 'unidades');


INSERT INTO familiaDO (id_familiaDO, nombre_familia, descripcion) VALUES
('Bombones', 'Bombones rellenos, decorados y bañados en chocolate'),
('Alfajores', 'Galletas rellenas de manjar o mermelada, bañadas en chocolate'),
('Sin Azúcar', 'Productos elaborados sin azúcar añadida'),
('Premium', 'Bombones y barras con ingredientes gourmet'),
('Cajas Variadas', 'Mix de bombones y trufas en presentaciones múltiples');

INSERT INTO producto (
  nombre,
  SKU,
  descripcion,
  unidad_medida_venta,
  peso_promedio_gramos,
  precioUnitario,
  id_familiaDO,
  id_receta
) VALUES
('Bombón crema de maní', 2025001,
 'Bombón elaborado con chocolate blanco y crema de maní y crocante de barquillo. Bañado en chocolate',
 'unidad', 18.00, 400.00, 1, 1),

('Nuez Rellena', 2025002,
 'Nueces rellenas en su centro con manjar de leche y bañadas en chocolate',
 'unidad', 20.00, 400.00, 1, 2),

('Bombón de avellana', 2025003,
 'Bombón elaborado con chocolate y crema de avellanas con una avellana entera en el centro.',
 'unidad', 22.00, 500.00, 1, 3),

('Cuchuflíe', 2025004,
 'Vaina de trigo rellena con manjar de leche y bañada en chocolate blanco o negro',
 'unidad', 25.00, 333.00, 1, 4),

('Alfajor Tradicional', 2025005,
 'Galletas de alfajor con relleno de manjar de leche bañado en chocolate negro',
 'unidad', 45.00, 800.00, 2, 5),

('Alfajor Tradicional Blanco', 2025006,
 'Galletas de alfajor con relleno de manjar de leche bañado en chocolate blanco',
 'unidad', 45.00, 800.00, 2, 6),

('Alfajor Frambuesa Blanco', 2025007,
 'Galletas de alfajor con relleno de mermelada de frambuesa bañado en chocolate blanco',
 'unidad', 45.00, 800.00, 2, 7),

('Alfajor Frambuesa Negro', 2025008,
 'Galletas de alfajor con relleno de mermelada de frambuesa bañado en chocolate negro',
 'unidad', 45.00, 800.00, 2, 8),

('Chocolates sin azúcar', 2025009,
 'Bombón base de manjar y chocolate sin azúcar espolvoreado con cacao natural',
 'caja', 18.00, 3500.00, 3, 9),

('Nuez rellena sin azúcar', 2025010,
 'Nuez rellena de manjar sin azúcar y bañada en chocolate amargo sin azúcar',
 'caja', 17.00, 3500.00, 3, 10),

('Cocadas', 2025011,
 'Masa elaborada con manjar de leche y harina de galletas espolvoreado con coco rallado',
 'caja', 18.00, 2000.00, 5, 11),

('Trufas con sabor a ron', 2025012,
 'Masa elaborada con manjar de leche, harina de galletas, esencia de ron y cobertura de chocolate decorado con palitos de chocolate',
 'caja', 18.00, 2000.00, 5, 12),

('Prestigio de coco', 2025013,
 'Barra de coco y leche condensada bañada en chocolate',
 'pack', 22.00, 1000.00, 1, 13),

('Mix de bombones', 2025014,
 'Pote de cocadas, trufas sabor ron, bombón de maní y nueces rellenas',
 'caja', 20.00, 3500.00, 5, 14),

('Barra de chocolate tipo Dubai', 2025015,
 'Barra de chocolate rellena de pasta de pistacho crocante',
 'unidad', 200.00, 4500.00, 4, 15),

('Mini Barra de chocolate tipo Dubai', 2025016,
 'MINI Barra de chocolate rellena de pasta de pistacho crocante',
 'unidad', 45.00, 1500.00, 4, 16);
 
 INSERT INTO producto (
  nombre,
  SKU,
  descripcion,
  unidad_medida_venta,
  peso_promedio_gramos,
  precioUnitario,
  id_familiaDO,
  id_receta
) VALUES
('Trufas sabor naranja', 2025017,
 'Trufa de chocolate con sabor a naranja ',
 'unidad', 18.00, 1000.00, 5, 17);
 
 
INSERT INTO proveedores (id_proveedor, nombre)
VALUES (1, 'gapicomercial');
ALTER TABLE insumos
ADD COLUMN id_proveedor INT,
ADD FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor);
ALTER TABLE insumos MODIFY proveedorId INT NULL;
ALTER TABLE insumos MODIFY id_familiaDO INT NULL;
ALTER TABLE insumos MODIFY SKU VARCHAR(20);
truncate table insumos;
INSERT INTO insumos (SKU, nombre, unidad_med, cantidadActual,  categoria, estado ) VALUES
('CHB001', 'Chocolate blanco Caravella', 'gramos', 1000,  'Chocolate', 'activo'),
('CHN002', 'Chocolate negro Costa', 'gramos', 1000,  'Chocolate', 'activo'),
('CHA003', 'Chocolate amargo sin azúcar Neucober', 'gramos', 1000,  'Chocolate sin azúcar', 'activo'),
('MAN004', 'Maní sin sal', 'gramos', 1000,  'Frutos secos', 'activo'),
('CRE005', 'Crema de avellanas Halta', 'gramos', 1000, 'Bases', 'activo'),
('NUE006', 'Nueces', 'gramos', 1000,  'Frutos secos', 'activo'),
('MAN007', 'Manjar Colun', 'gramos', 5000, 'Bases', 'activo'),
('MAN008', 'Manjar sin azúcar Langer', 'gramos', 1000, 'Base sin azúcar', 'activo'),
('VAI009', 'Vaina de trigo Conebric', 'gramos', 750, 'Bases', 'activo'),
('GAL010', 'Galletas Fruna', 'gramos', 1000,  'Bases', 'activo'),
('GAL011', 'Galletas Alfajor del Valle', 'gramos', 700, 'Bases', 'activo'),
('MER012', 'Mermelada de frambuesa Langer', 'gramos', 1000, 'Bases', 'activo'),
('COC013', 'Coco rallado', 'gramos', 500,  'Decoración', 'activo'),
('RON014', 'Ron (esencia)', 'ml', 500, 'Esencias', 'activo'),
('LEC015', 'Leche condensada', 'gramos', 397, 'Bases', 'activo'),
('PIS016', 'Pistacho', 'gramos', 1000, 'Frutos secos', 'activo');

INSERT INTO insumos (SKU, nombre, unidad_med, cantidadActual,  categoria, estado ) VALUES
('NAR017', 'Naranja (esencia)', 'ml', 25, 'Esencias', 'activo');


SET SQL_SAFE_UPDATES = 0;

UPDATE insumos
SET unidad_med = 'gramos'
WHERE SKU = 'VAI009';

SET SQL_SAFE_UPDATES = 1;


DELETE FROM insumos
WHERE SKU IN (
  'CHB001', 'CHN002', 'CHA003', 'MAN004', 'CRE005', 'NUE006',
  'MAN007', 'MAN008', 'VAI009', 'GAL010', 'GAL011', 'MER012',
  'COC013', 'RON014', 'LEC015', 'PIS016'
);

INSERT INTO detalleReceta (id_receta, id_insumo, cantidad_usada, unidad)
VALUES (1, 2, 7.0, 'gramos');

INSERT INTO detalleReceta (id_receta, id_insumo, cantidad_usada, unidad) VALUES
(1, 1, 8.5000, 'gramos'),   -- Chocolate blanco Caravella
(1, 4, 8.5000, 'gramos'),   -- Maní sin sal
(1, 9, 1.6000, 'unidad'),   -- Vaina de trigo Conebric


(2, 2, 4.0000, 'gramos'),   -- Chocolate negro Costa
(2, 7, 5.0000, 'gramos'),   -- Manjar Colun
(2, 6, 13.0000, 'gramos');  -- Nueces


INSERT INTO detalleReceta (id_receta, id_insumo, cantidad_usada, unidad) VALUES
(3, 5,8 , 'gramos'),   -- Crema de avellanas halta
(3, 1,6 , 'gramos'),   -- Chocolate blanco
(3, 9, 1, 'gramos'),   -- Vaina de trigo Conebric
(3, 2, 5, 'gramos'),   -- chocolate costa


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
(4,9,1,'unidad'), -- vaina cuchuflies
(4, 7, 15, 'gramos'),   -- Manjar Colun
(4, 2, 5, 'gramos'),   -- chocolate costa
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
(5,11,14,'gramos'), -- Galletas de alfajor 2
(5, 7, 40, 'gramos'),   -- Manjar Colun
(5, 2, 12, 'gramos'),   -- chocolate costa
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
(6,11,14,'gramos'), -- Galletas de alfajor 2
(6, 7, 40, 'gramos'),   -- Manjar Colun
(6, 1, 12, 'gramos'),   -- chocolate caravella
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
(7,11,21,'gramos'), -- Galletas de alfajor 3
(7, 12, 35, 'gramos'),   -- Mermelada frambuesa
(7, 1, 12, 'gramos'),   -- chocolate caravella
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
(8,11,21,'gramos'), -- Galletas de alfajor 3
(8, 12, 35, 'gramos'),   -- Mermelada frambuesa
(8, 2, 12, 'gramos'),   -- chocolate costa
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

-- 9 --


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
(10, 3, 4, 'gramos'),   -- Chocolate negro sin azucar
(10, 8, 5, 'gramos'),   -- Manjar Langer sin azucar
(10, 6, 8, 'gramos'),  -- Nueces
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
(11, 10, 8.18, 'gramos'),   -- Galletas fruna
(11, 7, 9.82, 'gramos'),   -- Manjar Colun
(11, 13, 3, 'gramos'),  -- coco
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
(12, 10, 5.45, 'gramos'),   -- Galletas fruna
(12, 7, 5.45, 'gramos'),   -- Manjar Colun
(12,2, 5.45, 'gramos'),  -- chocolate costa
(12,14,1.64,'ML'), -- Esencia ron
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
-- 13 --



-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
(15, 16, 35.7, 'gramos'),   -- Pistacho
(15, 1, 10.7, 'gramos'),   -- Chocolate blanco caravella
(15, 9, 3.55, 'gramos'),  -- Vaina de trigo
(15, 2, 70, 'gramos'),   -- Chocolate costa
-- -- -- -- -- -- -- Mini-- -- -- -- -- -- -- -- -- -- -- --
(16, 16, 21.42, 'gramos'),   -- Pistacho
(16, 1, 6.42, 'gramos'),   -- Chocolate blanco caravella
(16, 9, 2.13, 'gramos'),  -- Vaina de trigo
(16, 2, 40, 'gramos');   -- Chocolate costa
-- -- -- -- -- -- -- --Quedan todas esas recetas  -- -- -- -- -- -- -- -- -- -- --
INSERT INTO detalleReceta (id_receta, id_insumo, cantidad_usada, unidad) VALUES
(17, 10, 4.49, 'gramos'),   -- Galletas fruna
(17, 7, 8.98 ,'gramos'),   -- Manjar Colun
(17,2, 4.49, 'gramos'),  -- chocolate costa
(17,17,0.045,'ML'); -- Esencia naranja



ALTER USER 'root'@'localhost' IDENTIFIED BY 'dulceosadia';

-- -- -- 30-09-2025 -- -- --
use dulceosadia ;
CREATE TABLE IF NOT EXISTS usuarios (
  idusuario INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(100) NOT NULL,
  usuario VARCHAR(100) NOT NULL,
  password VARCHAR(100) NOT NULL,
  PRIMARY KEY (idusuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE usuarios ADD apellido VARCHAR(100) NOT NULL;
ALTER TABLE usuarios ADD rol VARCHAR(20) DEFAULT 'usuario';
INSERT INTO usuarios (nombre, correo, usuario, password, rol)
VALUES ('Admin', 'admin@mail.cl', 'admin', 'dulce_admin_osadia_23', 'admin');
ALTER TABLE usuarios ;
INSERT INTO usuarios (nombre, correo, usuario, password, rol)
VALUES ('Admin', 'admindulceosadia@mail.cl', 'admin', '$2y$10$m8vrz705eaPSIAxWT7XmquKa3qHpuFnH5Rn..fY.E9X47DEf24psK', 'admin');


UPDATE insumos
SET
    -- ¡IMPORTANTE! Aquí va el costo por CADA vaina individual.
    precio_por_gramos = 13.8333,

    -- También actualizamos el precio de compra de la presentación para mantener el registro.
    precio_presentacion_compra = 16600
WHERE
    id_insumo = 9; -- ID para 'Vaina de trigo Conebric'

-- 1. Añadir la nueva columna para el precio de presentación de compra.
ALTER TABLE `insumos` ADD `precio_presentacion_compra` DECIMAL(10,2) NULL;
SET SQL_SAFE_UPDATES = 0;
-- 2. Mover los datos actuales de 'precioUnitario' a la nueva columna 'precio_presentacion_compra'.
UPDATE `insumos` SET `precio_presentacion_compra` = `precioUnitario`;

-- 3. Renombrar la columna 'precioUnitario' a 'precio_por_gramos'.
ALTER TABLE `insumos` CHANGE `precioUnitario` `precio_por_gramos` DECIMAL(10,4) NULL;


-- 4. Actualizar la columna 'precio_por_gramos' con el costo por gramo/ml/unidad.
UPDATE `insumos`
SET `precio_por_gramos` =
    CASE
        WHEN `unidad_med` IN ('gramos', 'ml') AND `precio_presentacion_compra` IS NOT NULL AND `precio_presentacion_compra` > 0
            THEN `precio_presentacion_compra` / 1000.0
        WHEN `precio_presentacion_compra` IS NOT NULL AND `precio_presentacion_compra` > 0
            THEN `precio_presentacion_compra`
        ELSE 0
    END;

-- Opcional: Si quieres, puedes poner 'precio_por_unidad_medida' como NOT NULL después de la actualización inicial.
-- ALTER TABLE `insumos` MODIFY `precio_por_unidad_medida` DECIMAL(10,4) NOT NULL;







-- =============================
-- MIGRACIÓN 06-11: asegurar columnas y cargar datos de insumos si faltan
-- =============================
-- 1) Asegurar columnas necesarias (usa IF NOT EXISTS para evitar errores)
ALTER TABLE `insumos`
  ADD COLUMN IF NOT EXISTS `precio_por_gramos` DECIMAL(10,4) NULL,
  ADD COLUMN IF NOT EXISTS `ultima_actualizacion` DATETIME NULL,
  ADD COLUMN IF NOT EXISTS `precio_presentacion_compra` DECIMAL(10,2) NULL,
  ADD COLUMN IF NOT EXISTS `id_familiaDO` INT NULL,
  ADD COLUMN IF NOT EXISTS `proveedorId` INT NULL,
  ADD COLUMN IF NOT EXISTS `observaciones` VARCHAR(255) NULL;

-- Ajustar tipos para compatibilidad con los datos (SKU alfanumérico)
ALTER TABLE `insumos` MODIFY COLUMN `SKU` VARCHAR(20);

-- 2) Cargar dataset solicitado, ignorando duplicados por clave primaria
-- Limpieza preventiva para evitar duplicado por PK de una fila existente
DELETE FROM `insumos` WHERE `SKU` IN ('NAR017');
INSERT IGNORE INTO `insumos`
  (`id_insumo`, `SKU`, `nombre`, `descripcion`, `categoria`, `unidad_med`, `cantidadActual`, `cantidadMinima`, `precio_por_gramos`, `proveedorId`, `fecha_ingreso`, `fecha_vencimiento`, `estado`, `observaciones`, `id_familiaDO`, `id_proveedor`, `ultima_actualizacion`, `precio_presentacion_compra`) VALUES 
 (1, 'CHB001', 'Chocolate blanco Caravella', NULL, 'Chocolate', 'gramos', 2660.00, NULL, 6.1000, NULL, '2025-10-15', '2025-10-31', 'activo', NULL, NULL, NULL, '2025-10-18 01:26:40', 6100.00), 
 (2, 'CHN002', 'Chocolate negro Costa', NULL, 'Chocolate', 'gramos', 3000.00, NULL, 6.1000, NULL, '2025-10-17', '2026-10-17', 'activo', NULL, NULL, NULL, '2025-10-18 01:11:31', 6100.00), 
 (3, 'CHA003', 'Chocolate amargo sin azúcar Neucober', NULL, 'Chocolate sin azúcar', 'gramos', 3000.00, NULL, 8.8000, NULL, '2025-09-02', '2026-02-02', 'activo', NULL, NULL, NULL, '2025-10-17 22:29:25', 8800.00), 
 (4, 'MAN004', 'Maní sin sal', NULL, 'Frutos secos', 'gramos', 3160.00, NULL, 3.0000, NULL, '2025-10-15', '2026-01-22', 'activo', NULL, NULL, NULL, '2025-10-17 22:29:25', 3000.00), 
 (5, 'CRE005', 'Crema de avellanas Halta', NULL, 'Bases', 'gramos', 1500.00, NULL, 5.5000, NULL, '2025-09-29', '2025-10-20', 'activo', NULL, NULL, NULL, '2025-10-17 22:29:25', 5500.00), 
 (6, 'NUE006', 'Nueces', NULL, 'Frutos secos', 'gramos', 1000.00, NULL, 9.0000, NULL, '2025-09-02', '2025-12-02', 'activo', NULL, NULL, NULL, '2025-10-17 22:29:25', 9000.00), 
 (7, 'MAN007', 'Manjar Colun', NULL, 'Bases', 'gramos', 10000.00, NULL, 2.7000, NULL, '2025-10-14', '2026-01-14', 'activo', NULL, NULL, NULL, '2025-10-17 22:29:25', 2700.00), 
 (8, 'MAN008', 'Manjar sin azúcar Langer', NULL, 'Base sin azúcar', 'gramos', 3000.00, NULL, 5.8900, NULL, '2025-09-02', '2026-02-02', 'activo', NULL, NULL, NULL, '2025-10-17 22:29:25', 5890.00), 
 (9, 'VAI009', 'Vaina de trigo Conebric', NULL, 'Bases', 'gramos', 1040.00, NULL, 13.8333, NULL, '2025-09-02', '2026-04-02', 'activo', NULL, NULL, NULL, '2025-10-17 22:55:46', 16600.00), 
 (10, 'GAL010', 'Galletas Fruna', NULL, 'Bases', 'gramos', 3000.00, NULL, 2.1330, NULL, '2025-09-02', '2026-06-02', 'activo', NULL, NULL, NULL, '2025-10-17 22:29:25', 2133.00), 
 (11, 'GAL011', 'Galletas Alfajor del Valle', NULL, 'Bases', 'gramos', 8400.00, NULL, 4.4300, NULL, '2025-09-02', '2025-12-02', 'activo', NULL, NULL, NULL, '2025-10-18 01:26:40', 4430.00), 
 (12, 'MER012', 'Mermelada de frambuesa Langer', NULL, 'Bases', 'gramos', 2000.00, NULL, 2.5000, NULL, '2025-10-16', '2025-12-16', 'activo', NULL, NULL, NULL, '2025-10-18 01:26:40', 2500.00), 
 (13, 'COC013', 'Coco rallado', NULL, 'Decoración', 'gramos', 3000.00, NULL, 7.0000, NULL, NULL, NULL, 'activo', NULL, NULL, NULL, '2025-10-17 22:29:25', 7000.00), 
 (14, 'RON014', 'Ron (esencia)', NULL, 'Esencias', 'ml', 500.00, NULL, 6.5600, NULL, '2025-10-02', '2026-10-02', 'activo', NULL, NULL, NULL, '2025-10-17 22:29:25', 6560.00), 
 (15, 'LEC015', 'Leche condensada', NULL, 'Bases', 'gramos', 3000.00, NULL, 4.0000, NULL, '2025-09-02', '2026-05-02', 'activo', NULL, NULL, NULL, '2025-10-17 22:29:25', 4000.00), 
 (16, 'PIS016', 'Pistacho', NULL, 'Frutos secos', 'gramos', 1000.00, NULL, 32.0000, NULL, '2025-10-15', '2026-10-15', 'activo', NULL, NULL, NULL, '2025-10-17 22:29:25', 32000.00), 
 (17, 'NAR017', 'Naranja (esencia)', NULL, 'Esencias', 'ml', 50.00, NULL, 100.0000, NULL, NULL, NULL, 'activo', NULL, NULL, NULL, '2025-10-17 22:29:25', 100000.00);

-- Relleno de columnas vacías para filas existentes (seguro por SKU)
UPDATE `insumos` SET 
  `precio_por_gramos` = 6.1000,
  `precio_presentacion_compra` = 6100.00,
  `fecha_ingreso` = '2025-10-15',
  `fecha_vencimiento` = '2025-10-31',
  `ultima_actualizacion` = '2025-10-18 01:26:40'
WHERE `SKU` = 'CHB001';

UPDATE `insumos` SET 
  `precio_por_gramos` = 6.1000,
  `precio_presentacion_compra` = 6100.00,
  `fecha_ingreso` = '2025-10-17',
  `fecha_vencimiento` = '2026-10-17',
  `ultima_actualizacion` = '2025-10-18 01:11:31'
WHERE `SKU` = 'CHN002';

UPDATE `insumos` SET 
  `precio_por_gramos` = 8.8000,
  `precio_presentacion_compra` = 8800.00,
  `fecha_ingreso` = '2025-09-02',
  `fecha_vencimiento` = '2026-02-02',
  `ultima_actualizacion` = '2025-10-17 22:29:25'
WHERE `SKU` = 'CHA003';

UPDATE `insumos` SET 
  `precio_por_gramos` = 3.0000,
  `precio_presentacion_compra` = 3000.00,
  `fecha_ingreso` = '2025-10-15',
  `fecha_vencimiento` = '2026-01-22',
  `ultima_actualizacion` = '2025-10-17 22:29:25'
WHERE `SKU` = 'MAN004';

UPDATE `insumos` SET 
  `precio_por_gramos` = 5.5000,
  `precio_presentacion_compra` = 5500.00,
  `fecha_ingreso` = '2025-09-29',
  `fecha_vencimiento` = '2025-10-20',
  `ultima_actualizacion` = '2025-10-17 22:29:25'
WHERE `SKU` = 'CRE005';

UPDATE `insumos` SET 
  `precio_por_gramos` = 9.0000,
  `precio_presentacion_compra` = 9000.00,
  `fecha_ingreso` = '2025-09-02',
  `fecha_vencimiento` = '2025-12-02',
  `ultima_actualizacion` = '2025-10-17 22:29:25'
WHERE `SKU` = 'NUE006';

UPDATE `insumos` SET 
  `precio_por_gramos` = 2.7000,
  `precio_presentacion_compra` = 2700.00,
  `fecha_ingreso` = '2025-10-14',
  `fecha_vencimiento` = '2026-01-14',
  `ultima_actualizacion` = '2025-10-17 22:29:25'
WHERE `SKU` = 'MAN007';

UPDATE `insumos` SET 
  `precio_por_gramos` = 5.8900,
  `precio_presentacion_compra` = 5890.00,
  `fecha_ingreso` = '2025-09-02',
  `fecha_vencimiento` = '2026-02-02',
  `ultima_actualizacion` = '2025-10-17 22:29:25'
WHERE `SKU` = 'MAN008';

UPDATE `insumos` SET 
  `precio_por_gramos` = 13.8333,
  `precio_presentacion_compra` = 16600.00,
  `fecha_ingreso` = '2025-09-02',
  `fecha_vencimiento` = '2026-04-02',
  `ultima_actualizacion` = '2025-10-17 22:55:46'
WHERE `SKU` = 'VAI009';

UPDATE `insumos` SET 
  `precio_por_gramos` = 2.1330,
  `precio_presentacion_compra` = 2133.00,
  `fecha_ingreso` = '2025-09-02',
  `fecha_vencimiento` = '2026-06-02',
  `ultima_actualizacion` = '2025-10-17 22:29:25'
WHERE `SKU` = 'GAL010';

UPDATE `insumos` SET 
  `precio_por_gramos` = 4.4300,
  `precio_presentacion_compra` = 4430.00,
  `fecha_ingreso` = '2025-09-02',
  `fecha_vencimiento` = '2025-12-02',
  `ultima_actualizacion` = '2025-10-18 01:26:40'
WHERE `SKU` = 'GAL011';

UPDATE `insumos` SET 
  `precio_por_gramos` = 2.5000,
  `precio_presentacion_compra` = 2500.00,
  `fecha_ingreso` = '2025-10-16',
  `fecha_vencimiento` = '2025-12-16',
  `ultima_actualizacion` = '2025-10-18 01:26:40'
WHERE `SKU` = 'MER012';

UPDATE `insumos` SET 
  `precio_por_gramos` = 7.0000,
  `precio_presentacion_compra` = 7000.00,
  `fecha_ingreso` = NULL,
  `fecha_vencimiento` = NULL,
  `ultima_actualizacion` = '2025-10-17 22:29:25'
WHERE `SKU` = 'COC013';

UPDATE `insumos` SET 
  `precio_por_gramos` = 6.5600,
  `precio_presentacion_compra` = 6560.00,
  `fecha_ingreso` = '2025-10-02',
  `fecha_vencimiento` = '2026-10-02',
  `ultima_actualizacion` = '2025-10-17 22:29:25'
WHERE `SKU` = 'RON014';

UPDATE `insumos` SET 
  `precio_por_gramos` = 4.0000,
  `precio_presentacion_compra` = 4000.00,
  `fecha_ingreso` = '2025-09-02',
  `fecha_vencimiento` = '2026-05-02',
  `ultima_actualizacion` = '2025-10-17 22:29:25'
WHERE `SKU` = 'LEC015';

UPDATE `insumos` SET 
  `precio_por_gramos` = 32.0000,
  `precio_presentacion_compra` = 32000.00,
  `fecha_ingreso` = '2025-10-15',
  `fecha_vencimiento` = '2026-10-15',
  `ultima_actualizacion` = '2025-10-17 22:29:25'
WHERE `SKU` = 'PIS016';

UPDATE `insumos` SET 
  `precio_por_gramos` = 100.0000,
  `precio_presentacion_compra` = 100000.00,
  `fecha_ingreso` = NULL,
  `fecha_vencimiento` = NULL,
  `ultima_actualizacion` = '2025-10-17 22:29:25'
WHERE `SKU` = 'NAR017';

-- Asignar proveedor (id_proveedor=1) y familia por SKU
UPDATE `insumos` SET `id_proveedor` = 1, `id_familiaDO` = 1 WHERE `SKU` = 'CHB001'; -- Bombones
UPDATE `insumos` SET `id_proveedor` = 1, `id_familiaDO` = 1 WHERE `SKU` = 'CHN002'; -- Bombones
UPDATE `insumos` SET `id_proveedor` = 1, `id_familiaDO` = 3 WHERE `SKU` = 'CHA003'; -- Sin Azúcar
UPDATE `insumos` SET `id_proveedor` = 1, `id_familiaDO` = 1 WHERE `SKU` = 'MAN004'; -- Bombones
UPDATE `insumos` SET `id_proveedor` = 1, `id_familiaDO` = 1 WHERE `SKU` = 'CRE005'; -- Bombones
UPDATE `insumos` SET `id_proveedor` = 1, `id_familiaDO` = 1 WHERE `SKU` = 'NUE006'; -- Bombones
UPDATE `insumos` SET `id_proveedor` = 1, `id_familiaDO` = 2 WHERE `SKU` = 'MAN007'; -- Alfajores
UPDATE `insumos` SET `id_proveedor` = 1, `id_familiaDO` = 3 WHERE `SKU` = 'MAN008'; -- Sin Azúcar
UPDATE `insumos` SET `id_proveedor` = 1, `id_familiaDO` = 1 WHERE `SKU` = 'VAI009'; -- Bombones
UPDATE `insumos` SET `id_proveedor` = 1, `id_familiaDO` = 2 WHERE `SKU` = 'GAL010'; -- Alfajores
UPDATE `insumos` SET `id_proveedor` = 1, `id_familiaDO` = 2 WHERE `SKU` = 'MER012'; -- Alfajores
UPDATE `insumos` SET `id_proveedor` = 1, `id_familiaDO` = 4 WHERE `SKU` = 'COC013'; -- Premium
UPDATE `insumos` SET `id_proveedor` = 1, `id_familiaDO` = 4 WHERE `SKU` = 'RON014'; -- Premium
UPDATE `insumos` SET `id_proveedor` = 1, `id_familiaDO` = 2 WHERE `SKU` = 'LEC015'; -- Alfajores
UPDATE `insumos` SET `id_proveedor` = 1, `id_familiaDO` = 4 WHERE `SKU` = 'PIS016'; -- Premium
UPDATE `insumos` SET `id_proveedor` = 1, `id_familiaDO` = 5 WHERE `SKU` = 'NAR017'; -- Cajas Variadas








