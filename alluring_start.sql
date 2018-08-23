-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 13, 2018 at 03:45 AM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fodes`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_defaultscreen`
--

CREATE TABLE `app_defaultscreen` (
  `id_profile` int(20) NOT NULL,
  `id_module` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app_modules`
--

CREATE TABLE `app_modules` (
  `ID` int(20) NOT NULL,
  `NAME` varchar(50) NOT NULL,
  `PATH` varchar(50) NOT NULL,
  `LOAD_SEQ` int(20) NOT NULL DEFAULT '1',
  `FK_MODULE_CATEGORY` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `app_modules`
--

INSERT INTO `app_modules` (`ID`, `NAME`, `PATH`, `LOAD_SEQ`, `FK_MODULE_CATEGORY`) VALUES
(1, 'M?dulos', 'MantModulos', 1, 1),
(2, 'Perfiles', 'MantPerfiles', 2, 1),
(3, 'Usuarios', 'MantUsuarios', 2, 1),
(4, 'Accesos', 'PermissionsManager', 2, 1),
(5, 'Monedas', 'MantMonedas', 2, 1),
(6, 'Clientes', 'MantClientes', 1, 1),
(7, 'Proveedores', 'MantProveedores', 1, 1),
(8, 'Empleados', 'MantEmpleados', 1, 1),
(9, 'Tipos de producto', 'MantTipoProducto', 1, 1),
(10, 'Productos', 'MantProductos', 1, 1),
(11, 'Ingreso inventario', 'TrxIngresoInventario', 1, 2),
(12, 'Traslado sucursales', 'TrxMovimientoSucursales', 1, 2),
(13, 'Variables del sistema', 'MantVariablesSistema', 1, 1),
(14, 'Inventario', 'RptInventario', 1, 3),
(15, 'Pantalla de inicio', 'MantDefaultScreen', 1, 1),
(16, 'Salida inventario', 'TrxSalidaInventario', 1, 2),
(17, 'Paises', 'MantPaises', 1, 1),
(18, 'Departamentos', 'MantDepartamentos', 1, 1),
(19, 'Municipios', 'MantMunicipios', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `app_module_category`
--

CREATE TABLE `app_module_category` (
  `ID` int(20) NOT NULL,
  `NAME` varchar(50) NOT NULL,
  `ICON` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `app_module_category`
--

INSERT INTO `app_module_category` (`ID`, `NAME`, `ICON`) VALUES
(1, 'Configuraciones', 'glyphicon-cog'),
(2, 'Operaciones', 'glyphicon-wrench'),
(3, 'Reportes', 'glyphicon-list-alt');

-- --------------------------------------------------------

--
-- Table structure for table `app_profile`
--

CREATE TABLE `app_profile` (
  `ID` int(20) NOT NULL,
  `NAME` varchar(50) NOT NULL,
  `STATUS` int(5) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `app_profile`
--

INSERT INTO `app_profile` (`ID`, `NAME`, `STATUS`) VALUES
(1, 'Super Admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `app_profile_access`
--

CREATE TABLE `app_profile_access` (
  `FK_PROFILE` int(20) NOT NULL,
  `FK_MODULE` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `app_profile_access`
--

INSERT INTO `app_profile_access` (`FK_PROFILE`, `FK_MODULE`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19);

-- --------------------------------------------------------

--
-- Table structure for table `app_user`
--

CREATE TABLE `app_user` (
  `ID` varchar(200) NOT NULL,
  `FIRST_NAME` varchar(200) NOT NULL,
  `LAST_NAME` varchar(200) DEFAULT NULL,
  `PASSWORD` varchar(200) NOT NULL,
  `FK_PROFILE` int(20) NOT NULL DEFAULT '1',
  `CREATED` datetime NOT NULL,
  `LAST_LOGIN` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `app_user`
--

INSERT INTO `app_user` (`ID`, `FIRST_NAME`, `LAST_NAME`, `PASSWORD`, `FK_PROFILE`, `CREATED`, `LAST_LOGIN`) VALUES
('cHJ1ZWJhcw', 'Pruebas ', 'Ejemplos', 'e10adc3949ba59abbe56e057f20f883e', 1, '2016-08-24 19:32:42', '2016-08-24 19:32:42');

-- --------------------------------------------------------

--
-- Table structure for table `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(20) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  identificacion varchar(20) not null,
  id_pais int(5) not null,
  id_departamento int(5) not null,
  id_municipio int(20) not null,
  correo varchar(100) not null,
  id_tipo_precio int(5) not null,
  id_empleado int(20) not null,
  id_cliente_referido int(20) null,
  `tiene_credito` bit(1) NOT NULL DEFAULT b'0',
  `dias_credito` int(11) NULL,
  `factura_nit` varchar(20) DEFAULT NULL,
  `factura_nombre` varchar(50) NULL,
  `factura_direccion` varchar(50) NULL,
  observaciones varchar(1000) null,
  catalogo_usuario varchar(100),
  catalogo_password_hash varchar(1000),
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL
  
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


create table clientes_tipos_precio (
	id_tipo_precio int(5) not null auto_increment primary key,
    nombre varchar(50) not null,
    porcentaje_descuento decimal(10,2) not null,
    fecha_creacion datetime not null default CURRENT_TIMESTAMP,
    usuario_creacion varchar(100) not null
);

create table clientes_telefonos (
	id_cliente_telefono int(20) not null primary key auto_increment,
    id_cliente int(20) not null,
    numero int(8) not null,
    fecha_creacion datetime not null,
    usuario_creacion varchar(100) not null
);

create table clientes_bodegas (
	id_cliente_bodega int(20) not null primary key auto_increment,
    id_cliente int(20) not null,
    id_sucursal int(20) not null,
    fecha_creacion datetime not null,
    usuario_creacion varchar(100) not null
);

-- --------------------------------------------------------

--
-- Table structure for table `sucursales`
--

CREATE TABLE `sucursales` (
  `id_sucursal` int(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sucursales`
--
ALTER TABLE `sucursales`
  ADD PRIMARY KEY (`id_sucursal`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sucursales`
--
ALTER TABLE `sucursales`
  MODIFY `id_sucursal` int(20) NOT NULL AUTO_INCREMENT;
--
 
-- --------------------------------------------------------

--
-- Table structure for table `departamentos`
--

CREATE TABLE `departamentos` (
  `id_departamento` int(5) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `id_pais` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `departamentos`
--

INSERT INTO `departamentos` (`id_departamento`, `nombre`, `usuario_creacion`, `fecha_creacion`, `id_pais`) VALUES
(2, 'Guatemala', 'Pruebas ', '2017-09-06 08:06:02', 1),
(3, 'Zacapa', 'Pruebas ', '2017-09-06 08:11:13', 1);

-- --------------------------------------------------------

--
-- Table structure for table `empleados`
--

CREATE TABLE `empleados` (
  `id_empleado` int(20) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `id_sucursal` int(20) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  `id_interno` varchar(50) DEFAULT NULL,
  `id_usuario` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `estados_proyecto`
--

CREATE TABLE `estados_proyecto` (
  `id_estado_proyecto` int(5) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `usuario_creacion` varchar(100) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `estados_proyecto`
--

-- --------------------------------------------------------

--
-- Table structure for table `monedas`
--

CREATE TABLE `monedas` (
  `id_moneda` int(5) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `simbolo` varchar(5) NOT NULL,
  moneda_defecto bit(1) not null DEFAULT b'0',
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `monedas`
--

INSERT INTO `monedas` (`id_moneda`, `nombre`, `simbolo`, moneda_defecto,`fecha_creacion`, `usuario_creacion`) VALUES
(1, 'Quetzal', 'Q', 1,'2016-08-24 19:30:10', 'Bryan'),
(2, 'Dolar', '$', 0,'2016-08-27 20:22:43', 'Bryan');

-- --------------------------------------------------------

--
-- Table structure for table `movimiento_sucursales_estado`
--

CREATE TABLE `movimiento_sucursales_estado` (
  `id_movimiento_sucursales_estado` int(5) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_creacion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `movimiento_sucursales_estado`
--

INSERT INTO `movimiento_sucursales_estado` (`id_movimiento_sucursales_estado`, `nombre`, `fecha_creacion`, `usuario_creacion`) VALUES
(1, 'En Ruta', '2016-10-03 00:49:09', 'dev'),
(2, 'Entregada', '2016-10-03 00:49:09', 'dev'),
(3, 'Rechazada', '2016-10-03 00:49:09', 'dev');

-- --------------------------------------------------------

--
-- Table structure for table `municipios`
--

CREATE TABLE `municipios` (
  `id_municipio` int(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_departamento` int(20) NOT NULL,
  `usuario_creacion` varchar(100) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `municipios`
--

INSERT INTO `municipios` (`id_municipio`, `nombre`, `id_departamento`, `usuario_creacion`, `fecha_creacion`) VALUES
(1, 'Ciudad de Guatemala', 2, 'Pruebas ', '2017-09-06 08:22:25');

-- --------------------------------------------------------

--
-- Table structure for table `paises`
--

CREATE TABLE `paises` (
  `id_pais` int(5) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `paises`
--

INSERT INTO `paises` (`id_pais`, `nombre`, `usuario_creacion`, `fecha_creacion`) VALUES
(1, 'Guatemala', 'Pruebas ', '2017-09-06 08:04:21');

-- --------------------------------------------------------

--
-- Table structure for table `producto`
--

CREATE TABLE `producto` (
  `id_producto` int(20) NOT NULL,
  `id_subtipo` int(20) DEFAULT NULL,
  `nombre` varchar(50) NOT NULL,
  `precio_por_defecto` decimal(10,2) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `minimo_inventario` int(10) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `id_proveedor` int(20) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `id_tipo` int(20) DEFAULT NULL,
  `precio_docena` decimal(10,2) NOT NULL DEFAULT '0.00',
  `precio_mayorista` decimal(10,2) NOT NULL DEFAULT '0.00',
  `id_marca` int(20) NOT NULL,
  `sku` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `proveedor`
--

CREATE TABLE `proveedor` (
  `id_proveedor` int(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `nit` varchar(20) DEFAULT NULL,
  `fecha_pago` datetime DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` int(20) DEFAULT NULL,
  `es_internacional` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Table structure for table `tipo`
--

CREATE TABLE `tipo` (
  `id_tipo` int(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `prefijo` varchar(20) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `id_categoria` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tipo_cambio`
--

CREATE TABLE `tipo_cambio` (
  `id_tipo_cambio` int(20) NOT NULL,
  `id_moneda_muchos` int(20) NOT NULL,
  `id_moneda_uno` int(20) NOT NULL,
  factor decimal(10,2) not null,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `trx_ingreso_inventario`
--

CREATE TABLE `trx_ingreso_inventario` (
  `id_ingreso_inventario` int(20) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `id_empleado` int(20) NOT NULL,
  `revertido` bit(1) NOT NULL DEFAULT b'0',
  `comentario` varchar(500) NOT NULL,
  `id_sucursal` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `trx_ingreso_inventario_detalle`
--

CREATE TABLE `trx_ingreso_inventario_detalle` (
  `id_ingreso_inventario_detalle` int(20) NOT NULL,
  `id_ingreso_inventario` int(20) NOT NULL,
  `id_producto` int(20) NOT NULL,
  `cantidad` int(20) NOT NULL,
  `costo_producto` decimal(10,2) NOT NULL,
  `id_transaccion` bigint(50) NOT NULL,
  `cantidad_vendida` int(20) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `trx_movimiento_sucursales`
--

CREATE TABLE `trx_movimiento_sucursales` (
  `id_movimiento_sucursales` int(20) NOT NULL,
  `id_movimiento_sucursales_estado` int(5) NOT NULL,
  `id_empleado_envia` int(20) NOT NULL,
  `id_sucursal_origen` int(20) NOT NULL,
  `id_sucursal_destino` int(20) NOT NULL,
  `comentario_envio` varchar(500) DEFAULT NULL,
  `comentario_recepcion` varchar(500) DEFAULT NULL,
  `id_empleado_recibe` int(20) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_recepcion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `trx_movimiento_sucursales_detalle`
--

CREATE TABLE `trx_movimiento_sucursales_detalle` (
  `id_movimiento_sucursales_detalle` int(20) NOT NULL,
  `id_movimiento_sucursales` int(20) NOT NULL,
  `id_producto` int(20) NOT NULL,
  `unidades` int(20) NOT NULL,
  `id_transaccion` bigint(50) DEFAULT NULL,
  `id_transaccion_destino` bigint(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `trx_salida_inventario`
--

CREATE TABLE `trx_salida_inventario` (
  `id_salida_inventario` int(20) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `id_empleado` int(20) NOT NULL,
  `revertido` bit(1) NOT NULL DEFAULT b'0',
  `comentario` varchar(300) NOT NULL,
  `id_sucursal` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `trx_salida_inventario_detalle`
--

CREATE TABLE `trx_salida_inventario_detalle` (
  `id_salida_inventario_detalle` int(20) NOT NULL,
  `id_salida_inventario` int(20) NOT NULL,
  `id_producto` int(20) NOT NULL,
  `cantidad` int(20) NOT NULL,
  `costo_producto` decimal(10,2) NOT NULL,
  `id_transaccion` bigint(50) NOT NULL,
  `comentario` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `trx_transacciones`
--

CREATE TABLE `trx_transacciones` (
  `id_transaccion` bigint(50) NOT NULL,
  `id_cuenta` int(20) NOT NULL,
  `id_empleado` int(20) NOT NULL,
  `id_sucursal` int(20) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `id_moneda` int(5) DEFAULT NULL,
  `id_producto` int(20) DEFAULT NULL,
  `debe` decimal(10,2) NOT NULL DEFAULT '0.00',
  `haber` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fecha_creacion` datetime NOT NULL,
  `id_cliente` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `variables_sistema`
--

CREATE TABLE `variables_sistema` (
  `id_variables_sistema` int(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `valor` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `variables_sistema`
--

INSERT INTO `variables_sistema` (`id_variables_sistema`, `nombre`, `valor`, `fecha_creacion`, `usuario_creacion`) VALUES
(1, 'PassCambiarPrecios', 'precio123456', '2016-09-27 00:00:00', 'dev'),
(2, 'Perfil_SuperUsuario', '1', '2016-10-06 21:41:42', 'dev'),
(3, 'PassIngresarGasto', 'gasto1234', '2016-10-11 00:42:08', 'dev'),
(4, 'ReservaCajero', '500', '2016-10-11 04:20:24', 'dev'),
(5, 'FECHA_EXP', '2017-12-31', '2017-03-20 22:47:21', 'admin');

-- --------------------------------------------------------

--
-- Structure for view `reporte_inventario`
--
 -- DROP TABLE IF EXISTS `reporte_inventario`;

-- CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_inventario`  AS  select max(`pr`.`nombre`) AS `nombre_proveedor`,`tr`.`id_sucursal` AS `id_sucursal`,max(`p`.`id_proveedor`) AS `id_proveedor`,max(`p`.`nombre`) AS `nombre_producto`,(sum(`tr`.`haber`) - sum(`tr`.`debe`)) AS `cantidad`,max(`p`.`minimo_inventario`) AS `minimo_inventario`,max(`su`.`nombre`) AS `nombre_sucursal`,max(`p`.`descripcion`) AS `descripcion_producto`,max(`m`.`nombre`) AS `nombre_marca`,max(`t`.`nombre`) AS `nombre_tipo`,max(`s`.`nombre`) AS `nombre_subtipo`,`p`.`costo` AS `costo` from ((((((`trx_transacciones` `tr` join `producto` `p` on((`p`.`id_producto` = `tr`.`id_producto`))) join `proveedor` `pr` on((`pr`.`id_proveedor` = `p`.`id_proveedor`))) join `marca` `m` on((`m`.`id_marca` = `p`.`id_marca`))) join `subtipo` `s` on((`s`.`id_subtipo` = `p`.`id_subtipo`))) join `tipo` `t` on((`p`.`id_tipo` = `t`.`id_tipo`))) join `sucursales` `su` on((`su`.`id_sucursal` = `tr`.`id_sucursal`))) where (`tr`.`id_producto` is not null) group by `tr`.`id_producto`,`tr`.`id_sucursal` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_defaultscreen`
--
ALTER TABLE `app_defaultscreen`
  ADD PRIMARY KEY (`id_profile`,`id_module`),
  ADD KEY `id_module` (`id_module`);

--
-- Indexes for table `app_modules`
--
ALTER TABLE `app_modules`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_MODULE_CATEGORY` (`FK_MODULE_CATEGORY`);

--
-- Indexes for table `app_module_category`
--
ALTER TABLE `app_module_category`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `app_profile`
--
ALTER TABLE `app_profile`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `app_profile_access`
--
ALTER TABLE `app_profile_access`
  ADD PRIMARY KEY (`FK_PROFILE`,`FK_MODULE`),
  ADD KEY `FK_MODULE` (`FK_MODULE`);

--
-- Indexes for table `app_user`
--
ALTER TABLE `app_user`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indexes for table `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`id_departamento`),
  ADD KEY `id_pais` (`id_pais`);

--
-- Indexes for table `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id_empleado`),
  ADD KEY `id_sucursal` (`id_sucursal`);

--
-- Indexes for table `estados_proyecto`
--
ALTER TABLE `estados_proyecto`
  ADD PRIMARY KEY (`id_estado_proyecto`);

--
-- Indexes for table `monedas`
--
ALTER TABLE `monedas`
  ADD PRIMARY KEY (`id_moneda`);

--
-- Indexes for table `moneda_tipo`
--

--
-- Indexes for table `movimiento_sucursales_estado`
--
ALTER TABLE `movimiento_sucursales_estado`
  ADD PRIMARY KEY (`id_movimiento_sucursales_estado`);

--
-- Indexes for table `municipios`
--
ALTER TABLE `municipios`
  ADD PRIMARY KEY (`id_municipio`),
  ADD KEY `FK_Municipios_Departamentos` (`id_departamento`);

--
-- Indexes for table `paises`
--
ALTER TABLE `paises`
  ADD PRIMARY KEY (`id_pais`);

--
-- Indexes for table `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_subtipo` (`id_subtipo`),
  ADD KEY `id_tipo` (`id_tipo`),
  ADD KEY `id_marca` (`id_marca`);

--
-- Indexes for table `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- Indexes for table `tipo`
--
ALTER TABLE `tipo`
  ADD PRIMARY KEY (`id_tipo`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indexes for table `tipo_cambio`
--
ALTER TABLE `tipo_cambio`
  ADD PRIMARY KEY (`id_tipo_cambio`),
  ADD KEY `id_moneda_muchos` (`id_moneda_muchos`),
  ADD KEY `id_moneda_uno` (`id_moneda_uno`);

--
-- Indexes for table `trx_ingreso_inventario`
--
ALTER TABLE `trx_ingreso_inventario`
  ADD PRIMARY KEY (`id_ingreso_inventario`),
  ADD KEY `id_empleado` (`id_empleado`),
  ADD KEY `id_sucursal` (`id_sucursal`);

--
-- Indexes for table `trx_ingreso_inventario_detalle`
--
ALTER TABLE `trx_ingreso_inventario_detalle`
  ADD PRIMARY KEY (`id_ingreso_inventario_detalle`),
  ADD KEY `id_ingreso_inventario` (`id_ingreso_inventario`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_transaccion` (`id_transaccion`);

--
-- Indexes for table `trx_movimiento_sucursales`
--
ALTER TABLE `trx_movimiento_sucursales`
  ADD PRIMARY KEY (`id_movimiento_sucursales`),
  ADD KEY `id_empleado_envia` (`id_empleado_envia`),
  ADD KEY `id_sucursal_origen` (`id_sucursal_origen`),
  ADD KEY `id_sucursal_destino` (`id_sucursal_destino`),
  ADD KEY `id_empleado_recibe` (`id_empleado_recibe`);

--
-- Indexes for table `trx_movimiento_sucursales_detalle`
--
ALTER TABLE `trx_movimiento_sucursales_detalle`
  ADD PRIMARY KEY (`id_movimiento_sucursales_detalle`),
  ADD KEY `id_movimiento_sucursales` (`id_movimiento_sucursales`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_transaccion` (`id_transaccion`),
  ADD KEY `id_transaccion_destino` (`id_transaccion_destino`);

--
-- Indexes for table `trx_salida_inventario`
--
ALTER TABLE `trx_salida_inventario`
  ADD PRIMARY KEY (`id_salida_inventario`),
  ADD KEY `id_empleado` (`id_empleado`),
  ADD KEY `id_sucursal` (`id_sucursal`);

--
-- Indexes for table `trx_salida_inventario_detalle`
--
ALTER TABLE `trx_salida_inventario_detalle`
  ADD PRIMARY KEY (`id_salida_inventario_detalle`),
  ADD KEY `id_salida_inventario` (`id_salida_inventario`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_transaccion` (`id_transaccion`);

--
-- Indexes for table `trx_transacciones`
--
ALTER TABLE `trx_transacciones`
  ADD PRIMARY KEY (`id_transaccion`),
  ADD KEY `id_cuenta` (`id_cuenta`),
  ADD KEY `id_empleado` (`id_empleado`),
  ADD KEY `id_sucursal` (`id_sucursal`),
  ADD KEY `id_moneda` (`id_moneda`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indexes for table `variables_sistema`
--
ALTER TABLE `variables_sistema`
  ADD PRIMARY KEY (`id_variables_sistema`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_modules`
--
ALTER TABLE `app_modules`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `app_module_category`
--
ALTER TABLE `app_module_category`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `app_profile`
--
ALTER TABLE `app_profile`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `id_departamento` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id_empleado` int(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `estados_proyecto`
--
ALTER TABLE `estados_proyecto`
  MODIFY `id_estado_proyecto` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `monedas`
--
ALTER TABLE `monedas`
  MODIFY `id_moneda` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `moneda_tipo`
--
-- AUTO_INCREMENT for table `movimiento_sucursales_estado`
--
ALTER TABLE `movimiento_sucursales_estado`
  MODIFY `id_movimiento_sucursales_estado` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `municipios`
--
ALTER TABLE `municipios`
  MODIFY `id_municipio` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `paises`
--
ALTER TABLE `paises`
  MODIFY `id_pais` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `producto`
--
ALTER TABLE `producto`
  MODIFY `id_producto` int(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id_proveedor` int(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tipo`
--
ALTER TABLE `tipo`
  MODIFY `id_tipo` int(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tipo_cambio`
--
ALTER TABLE `tipo_cambio`
  MODIFY `id_tipo_cambio` int(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `trx_ingreso_inventario`
--
ALTER TABLE `trx_ingreso_inventario`
  MODIFY `id_ingreso_inventario` int(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `trx_ingreso_inventario_detalle`
--
ALTER TABLE `trx_ingreso_inventario_detalle`
  MODIFY `id_ingreso_inventario_detalle` int(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `trx_movimiento_sucursales`
--
ALTER TABLE `trx_movimiento_sucursales`
  MODIFY `id_movimiento_sucursales` int(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `trx_movimiento_sucursales_detalle`
--
ALTER TABLE `trx_movimiento_sucursales_detalle`
  MODIFY `id_movimiento_sucursales_detalle` int(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `trx_salida_inventario`
--
ALTER TABLE `trx_salida_inventario`
  MODIFY `id_salida_inventario` int(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `trx_salida_inventario_detalle`
--
ALTER TABLE `trx_salida_inventario_detalle`
  MODIFY `id_salida_inventario_detalle` int(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `trx_transacciones`
--
ALTER TABLE `trx_transacciones`
  MODIFY `id_transaccion` bigint(50) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `variables_sistema`
--
ALTER TABLE `variables_sistema`
  MODIFY `id_variables_sistema` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `app_defaultscreen`
--
ALTER TABLE `app_defaultscreen`
  ADD CONSTRAINT `app_defaultscreen_ibfk_1` FOREIGN KEY (`id_profile`) REFERENCES `app_profile` (`ID`),
  ADD CONSTRAINT `app_defaultscreen_ibfk_2` FOREIGN KEY (`id_module`) REFERENCES `app_modules` (`ID`);

--
-- Constraints for table `app_modules`
--
ALTER TABLE `app_modules`
  ADD CONSTRAINT `app_modules_ibfk_1` FOREIGN KEY (`FK_MODULE_CATEGORY`) REFERENCES `app_module_category` (`ID`);

--
-- Constraints for table `app_profile_access`
--
ALTER TABLE `app_profile_access`
  ADD CONSTRAINT `app_profile_access_ibfk_1` FOREIGN KEY (`FK_PROFILE`) REFERENCES `app_profile` (`ID`),
  ADD CONSTRAINT `app_profile_access_ibfk_2` FOREIGN KEY (`FK_MODULE`) REFERENCES `app_modules` (`ID`);

--
-- Constraints for table `departamentos`
--
ALTER TABLE `departamentos`
  ADD CONSTRAINT `departamentos_ibfk_1` FOREIGN KEY (`id_pais`) REFERENCES `paises` (`id_pais`);

--
-- Constraints for table `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`);

--
-- Constraints for table `municipios`
--
ALTER TABLE `municipios`
  ADD CONSTRAINT `FK_Municipios_Departamentos` FOREIGN KEY (`id_departamento`) REFERENCES `departamentos` (`id_departamento`);

--
-- Constraints for table `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`id_tipo`) REFERENCES `tipo` (`id_tipo`);



--
-- Constraints for table `tipo_cambio`
--
ALTER TABLE `tipo_cambio`
  ADD CONSTRAINT `tipo_cambio_ibfk_1` FOREIGN KEY (`id_moneda_muchos`) REFERENCES `monedas` (`id_moneda`),
  ADD CONSTRAINT `tipo_cambio_ibfk_2` FOREIGN KEY (`id_moneda_uno`) REFERENCES `monedas` (`id_moneda`);

--
-- Constraints for table `trx_ingreso_inventario`
--
ALTER TABLE `trx_ingreso_inventario`
  ADD CONSTRAINT `trx_ingreso_inventario_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id_empleado`),
  ADD CONSTRAINT `trx_ingreso_inventario_ibfk_2` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`);

--
-- Constraints for table `trx_ingreso_inventario_detalle`
--
ALTER TABLE `trx_ingreso_inventario_detalle`
  ADD CONSTRAINT `trx_ingreso_inventario_detalle_ibfk_1` FOREIGN KEY (`id_ingreso_inventario`) REFERENCES `trx_ingreso_inventario` (`id_ingreso_inventario`),
  ADD CONSTRAINT `trx_ingreso_inventario_detalle_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`),
  ADD CONSTRAINT `trx_ingreso_inventario_detalle_ibfk_3` FOREIGN KEY (`id_transaccion`) REFERENCES `trx_transacciones` (`id_transaccion`);

--
-- Constraints for table `trx_movimiento_sucursales`
--
ALTER TABLE `trx_movimiento_sucursales`
  ADD CONSTRAINT `trx_movimiento_sucursales_ibfk_1` FOREIGN KEY (`id_empleado_envia`) REFERENCES `empleados` (`id_empleado`),
  ADD CONSTRAINT `trx_movimiento_sucursales_ibfk_2` FOREIGN KEY (`id_sucursal_origen`) REFERENCES `sucursales` (`id_sucursal`),
  ADD CONSTRAINT `trx_movimiento_sucursales_ibfk_3` FOREIGN KEY (`id_sucursal_destino`) REFERENCES `sucursales` (`id_sucursal`),
  ADD CONSTRAINT `trx_movimiento_sucursales_ibfk_4` FOREIGN KEY (`id_empleado_recibe`) REFERENCES `empleados` (`id_empleado`);

--
-- Constraints for table `trx_movimiento_sucursales_detalle`
--
ALTER TABLE `trx_movimiento_sucursales_detalle`
  ADD CONSTRAINT `trx_movimiento_sucursales_detalle_ibfk_1` FOREIGN KEY (`id_movimiento_sucursales`) REFERENCES `trx_movimiento_sucursales` (`id_movimiento_sucursales`),
  ADD CONSTRAINT `trx_movimiento_sucursales_detalle_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`),
  ADD CONSTRAINT `trx_movimiento_sucursales_detalle_ibfk_3` FOREIGN KEY (`id_transaccion`) REFERENCES `trx_transacciones` (`id_transaccion`),
  ADD CONSTRAINT `trx_movimiento_sucursales_detalle_ibfk_4` FOREIGN KEY (`id_transaccion_destino`) REFERENCES `trx_transacciones` (`id_transaccion`);

--
-- Constraints for table `trx_salida_inventario`
--
ALTER TABLE `trx_salida_inventario`
  ADD CONSTRAINT `trx_salida_inventario_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id_empleado`),
  ADD CONSTRAINT `trx_salida_inventario_ibfk_2` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`);

--
-- Constraints for table `trx_salida_inventario_detalle`
--
ALTER TABLE `trx_salida_inventario_detalle`
  ADD CONSTRAINT `trx_salida_inventario_detalle_ibfk_1` FOREIGN KEY (`id_salida_inventario`) REFERENCES `trx_salida_inventario` (`id_salida_inventario`),
  ADD CONSTRAINT `trx_salida_inventario_detalle_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`),
  ADD CONSTRAINT `trx_salida_inventario_detalle_ibfk_3` FOREIGN KEY (`id_transaccion`) REFERENCES `trx_transacciones` (`id_transaccion`);

create table cuentas (
	id_cuenta int(20) not null auto_increment primary key,
    nombre varchar(50)
);


--
-- Constraints for table `trx_transacciones`
--
ALTER TABLE `trx_transacciones`
  ADD CONSTRAINT `trx_transacciones_ibfk_1` FOREIGN KEY (`id_cuenta`) REFERENCES `cuentas` (`id_cuenta`),
  ADD CONSTRAINT `trx_transacciones_ibfk_2` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id_empleado`),
  ADD CONSTRAINT `trx_transacciones_ibfk_3` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  ADD CONSTRAINT `trx_transacciones_ibfk_4` FOREIGN KEY (`id_moneda`) REFERENCES `monedas` (`id_moneda`),
  ADD CONSTRAINT `trx_transacciones_ibfk_5` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`),
  ADD CONSTRAINT `trx_transacciones_ibfk_6` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);


alter table clientes add constraint clientes_pais foreign key (id_pais) references paises(id_pais);
alter table clientes add constraint clientes_departamento foreign key (id_departamento) references departamentos(id_departamento);
alter table clientes add constraint clientes_municipio foreign key (id_municipio) references municipios(id_municipio); 
alter table clientes add constraint clientes_tipo_precio foreign key (id_tipo_precio) references clientes_tipos_precio(id_tipo_precio); 
alter table clientes add constraint clientes_empleado foreign key (id_empleado) references empleados(id_empleado); 
alter table clientes add constraint clientes_cliente_referido foreign key (id_cliente_referido) references clientes(id_cliente); 
alter table clientes_telefonos add constraint clientes_telefonos_cliente foreign key (id_cliente) references clientes(id_cliente); 
alter table clientes_bodegas add constraint clientes_bodegas_cliente foreign key (id_cliente) references clientes(id_cliente); 
alter table clientes_bodegas add constraint clientes_bodegas_bodega foreign key (id_sucursal) references sucursales(id_sucursal);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
ALTER TABLE `empleados`
ADD COLUMN es_vendedor bit(1) NOT NULL DEFAULT b'0';
ALTER TABLE `clientes` MODIFY id_empleado int(20) NULL;
alter table `clientes` drop foreign key clientes_cliente_referido;
alter table tipo drop column prefijo;
alter table tipo drop column id_categoria;
alter table sucursales add column identificador_excel varchar(100) NULL;
alter table producto add column precio_venta decimal(10,2) NOT NULL DEFAULT '0.00';
alter table producto add column imagen varchar(255) NOT NULL;
alter table producto add column codigo_origen varchar(255) NOT NULL;
ALTER TABLE trx_movimiento_sucursales ADD COLUMN id_cliente_recibe int(20) NULL;
ALTER TABLE trx_movimiento_sucursales ADD COLUMN es_consignacion bit(1) NOT NULL DEFAULT 0;
ALTER TABLE trx_movimiento_sucursales ADD COLUMN dias_consignacion int(20) NULL;
ALTER TABLE trx_movimiento_sucursales ADD COLUMN porcetaje_compra_min float NULL;

create table generacion_etiquetas (
	codigo_origen varchar(255) NOT NULL,
    cantidad decimal(10,2) NOT NULL DEFAULT '0.00'
);

CREATE TABLE `trx_venta` (
  `id_venta` bigint(50) NOT NULL AUTO_INCREMENT,
  `total` decimal(10,2) NOT NULL,
  `id_cliente` int(20) NOT NULL,
  `id_empleado` int(20) NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  PRIMARY KEY (`id_venta`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `trx_venta_detalle` (
  `id_venta_detalle` bigint(50) NOT NULL AUTO_INCREMENT,
  `id_venta` bigint(50) NOT NULL,
  `id_producto` int(20) NOT NULL,
  `cantidad` decimal(10,2)  NOT NULL,
  `precio_venta` decimal(10,2)  NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  KEY `id_venta` (`id_venta`),
  CONSTRAINT `venta_detalle_ibfk_2` FOREIGN KEY (`id_venta`) REFERENCES `trx_venta` (`id_venta`),
  PRIMARY KEY (`id_venta_detalle`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `trx_venta_formas_pago` (
  `id_venta_formas_pago` bigint(50) NOT NULL AUTO_INCREMENT,
  `id_venta` bigint(50) NOT NULL,
  `id_forma_pago` int(20) NOT NULL,
  `id_moneda` int(5) NULL,
  `cantidad` decimal(10,2) NULL,
  `monto` decimal(10,2) NULL,
  `numero_cheque` varchar(255) NULL,
  `id_banco` int(5) NULL,
  `numero_autorizacion` varchar(255) NULL,
  `autorizado_por` varchar(255) NULL,
  `numero_voucher` varchar(255) NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  KEY `id_venta` (`id_venta`),
  CONSTRAINT `venta_formas_pago_ibfk_2` FOREIGN KEY (`id_venta`) REFERENCES `trx_venta` (`id_venta`),
  PRIMARY KEY (`id_venta_formas_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `formas_pago` (
  `id_forma_pago` int(20) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  PRIMARY KEY (`id_forma_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `bancos` (
  `id_banco` int(5) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  PRIMARY KEY (`id_banco`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

