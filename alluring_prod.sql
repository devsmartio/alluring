-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2018 at 12:01 AM
-- Server version: 10.1.35-MariaDB
-- PHP Version: 7.2.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `alluring_prod`
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
(13, 'Variables del sistema', 'MantVariablesSistema', 1, 1),
(17, 'Paises', 'MantPaises', 1, 1),
(18, 'Departamentos', 'MantDepartamentos', 1, 1),
(19, 'Municipios', 'MantMunicipios', 1, 1),
(20, 'Tipo de Cambio', 'MantTipoCambio', 1, 1),
(22, 'Administracion Clientes', 'TrxAdministracionClientes', 1, 2),
(23, 'Tipos de Cliente', 'MantTipoCliente', 1, 1),
(24, 'Categorias de Productos', 'MantCategoriaProd', 1, 1),
(25, 'Mantenimiento de Bodegas', 'MantBodegas', 1, 1),
(28, 'Administracion de Productos', 'TrxAdministracionProductos', 1, 2),
(29, 'Carga Masiva de Productos', 'TrxCargaMasivaProductos', 1, 2),
(30, 'Traslado de Bodegas', 'TrxTrasladoBodegas', 1, 2),
(31, 'Carga Masiva de Clientes', 'TxCargaMasivaClientes', 1, 2),
(32, 'Generacion de Etiquetas', 'TrxGeneracionEtiquetas', 1, 2),
(33, 'Reingreso de Consignacion', 'TrxReingresoConsignacion', 1, 2),
(34, 'Ventas', 'TrxVenta', 1, 2),
(35, 'Reporte Clientes', 'RptClientes', 1, 3),
(36, 'Inventario', 'RptInventario', 1, 3),
(37, 'Ventas', 'RptVentas', 1, 3),
(38, 'Ventas por item', 'RptVentasDetalle', 1, 3),
(39, 'Configuración bodegas', 'MantBodegasEmpleados', 1, 1),
(40, 'Descuentos', 'MantDescuentos', 1, 1);

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
(1, 'Super Admin', 1),
(2, 'Ventas', 1),
(3, 'Admin', 1);

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
(1, 13),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(2, 22),
(2, 23),
(2, 24),
(2, 25),
(2, 28),
(2, 29),
(2, 30),
(2, 31),
(2, 33),
(2, 34),
(2, 35),
(2, 36),
(2, 37),
(2, 38),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 17),
(3, 18),
(3, 19),
(3, 20),
(3, 22),
(3, 23),
(3, 24),
(3, 25),
(3, 28),
(3, 29),
(3, 30),
(3, 31),
(3, 32),
(3, 33),
(3, 34),
(3, 35),
(3, 36),
(3, 39),
(3, 40);

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
  `LAST_LOGIN` datetime NOT NULL,
  `is_seller` bit(1) NOT NULL DEFAULT b'0',
  `EMAIL` varchar(50) DEFAULT NULL,
  `PHONE` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `app_user`
--

INSERT INTO `app_user` (`ID`, `FIRST_NAME`, `LAST_NAME`, `PASSWORD`, `FK_PROFILE`, `CREATED`, `LAST_LOGIN`, `is_seller`, `EMAIL`, `PHONE`) VALUES
('aXNhLmZlcg', 'Isabel', 'Fernandez', '5e66acdea9996881ab7b630c1e9cecb4', 3, '2018-09-04 13:17:47', '2018-09-04 13:17:47', b'0', 'isa@correo.com', '123456'),
('c2FkbWlu', 'Super', 'Admin', 'e10adc3949ba59abbe56e057f20f883e', 3, '2018-12-28 16:57:56', '2018-12-28 16:57:56', b'0', 'ventas@alluring.com.gt', '12345678'),
('YnJjcnV6', 'bryan', 'Cruz', 'e10adc3949ba59abbe56e057f20f883e', 1, '2018-12-28 16:56:22', '2018-12-28 16:56:22', b'1', 'bryan.cruz@getsmartio.com', '40242180'),
('Z2xhZHlzLmNvbmVkZXJh', 'Gladys', 'Conedera', 'af6f1fedd625479932e1f169030e6933', 2, '2018-11-27 12:03:11', '2018-11-27 12:03:11', b'0', 'yicm86@gmail.com', '22332655');

-- --------------------------------------------------------

--
-- Table structure for table `bancos`
--

CREATE TABLE `bancos` (
  `id_banco` int(5) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bancos`
--

INSERT INTO `bancos` (`id_banco`, `nombre`, `usuario_creacion`, `fecha_creacion`) VALUES
(1, 'Banco Banrural', 'Pruebas', '2018-08-20 00:00:00'),
(2, 'Banco Industrial', 'Pruebas', '2018-08-20 00:00:00'),
(3, 'Banco GyT Continental', 'Pruebas', '2018-08-20 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(20) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `identificacion` varchar(20) NOT NULL,
  `id_pais` int(5) DEFAULT NULL,
  `id_departamento` int(5) DEFAULT NULL,
  `id_municipio` int(20) DEFAULT NULL,
  `correo` varchar(100) NOT NULL,
  `id_tipo_precio` int(5) DEFAULT NULL,
  `id_cliente_referido` int(20) DEFAULT NULL,
  `tiene_credito` bit(1) NOT NULL DEFAULT b'0',
  `dias_credito` int(11) DEFAULT NULL,
  `factura_nit` varchar(20) DEFAULT NULL,
  `factura_nombre` varchar(50) DEFAULT NULL,
  `factura_direccion` varchar(50) DEFAULT NULL,
  `observaciones` varchar(1000) DEFAULT NULL,
  `catalogo_usuario` varchar(100) DEFAULT NULL,
  `catalogo_password_hash` varchar(1000) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `id_usuario` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `clientes_bodegas`
--

CREATE TABLE `clientes_bodegas` (
  `id_cliente_bodega` int(20) NOT NULL,
  `id_cliente` int(20) NOT NULL,
  `id_sucursal` int(20) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `clientes_telefonos`
--

CREATE TABLE `clientes_telefonos` (
  `id_cliente_telefono` int(20) NOT NULL,
  `id_cliente` int(20) NOT NULL,
  `numero` int(8) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `clientes_tipos_precio`
--

CREATE TABLE `clientes_tipos_precio` (
  `id_tipo_precio` int(5) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `porcentaje_descuento` decimal(10,2) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_creacion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cuentas`
--

CREATE TABLE `cuentas` (
  `id_cuenta` int(20) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cuentas`
--

INSERT INTO `cuentas` (`id_cuenta`, `nombre`) VALUES
(1, 'Cuenta 1'),
(2, 'Inventario'),
(3, 'Reingreso'),
(4, 'Venta');

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
-- Table structure for table `descuentos`
--

CREATE TABLE `descuentos` (
  `id_descuento` int(11) NOT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `id_tipo` int(11) DEFAULT NULL,
  `id_tipo_precio` int(11) DEFAULT NULL,
  `porcentaje_descuento` decimal(10,2) DEFAULT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `activo` bit(1) NOT NULL DEFAULT b'1',
  `usuario_creacion` varchar(100) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `id_usuario` varchar(200) NOT NULL,
  `es_vendedor` bit(1) NOT NULL DEFAULT b'0'
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

-- --------------------------------------------------------

--
-- Table structure for table `formas_pago`
--

CREATE TABLE `formas_pago` (
  `id_forma_pago` int(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `formas_pago`
--

INSERT INTO `formas_pago` (`id_forma_pago`, `nombre`, `usuario_creacion`, `fecha_creacion`) VALUES
(1, 'Efectivo', 'Pruebas', '2018-08-20 00:00:00'),
(2, 'Cheque', 'Pruebas', '2018-08-20 00:00:00'),
(3, 'Tarjeta', 'Pruebas', '2018-08-20 00:00:00'),
(4, 'Credito devolucion', 'Dev', '2018-10-19 13:21:31');

-- --------------------------------------------------------

--
-- Table structure for table `generacion_etiquetas`
--

CREATE TABLE `generacion_etiquetas` (
  `codigo_origen` varchar(50) DEFAULT NULL,
  `cantidad` decimal(10,2) NOT NULL DEFAULT '0.00',
  `id_sucursal` int(20) DEFAULT NULL,
  `codigo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `monedas`
--

CREATE TABLE `monedas` (
  `id_moneda` int(5) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `simbolo` varchar(5) NOT NULL,
  `moneda_defecto` bit(1) NOT NULL DEFAULT b'0',
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `monedas`
--

INSERT INTO `monedas` (`id_moneda`, `nombre`, `simbolo`, `moneda_defecto`, `fecha_creacion`, `usuario_creacion`) VALUES
(1, 'Quetzal', 'Q', b'1', '2016-08-24 19:30:10', 'Bryan'),
(2, 'Dolar', '$', b'0', '2016-08-27 20:22:43', 'Bryan'),
(29, 'Euro', 'E', b'0', '2018-09-03 22:03:31', 'Pruebas ');

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
(1, 'Ciudad de Guatemala', 2, 'Pruebas ', '2017-09-06 08:22:25'),
(2, 'Teculutan', 3, 'Pruebas ', '2018-07-20 23:44:00');

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
(1, 'Guatemala', 'Pruebas ', '2017-09-06 08:04:21'),
(2, 'El Salvador', 'Pruebas ', '2018-07-20 23:40:33'),
(3, 'Honduras', 'Pruebas ', '2018-09-12 20:13:34');

-- --------------------------------------------------------

--
-- Table structure for table `producto`
--

CREATE TABLE `producto` (
  `id_producto` int(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `id_tipo` int(20) DEFAULT NULL,
  `precio_venta` decimal(10,2) NOT NULL DEFAULT '0.00',
  `imagen` varchar(255) NOT NULL,
  `codigo_origen` varchar(255) DEFAULT NULL,
  `codigo` varchar(50) NOT NULL
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

--
-- Stand-in structure for view `reporte_clientes`
-- (See below for the actual view)
--
CREATE TABLE `reporte_clientes` (
`identificacion` varchar(20)
,`nombres` varchar(100)
,`apellidos` varchar(100)
,`factura_nit` varchar(20)
,`numero` int(8)
,`direccion` varchar(100)
,`tiene_credito` bit(1)
,`dias_credito` int(11)
,`id_departamento` int(5)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `reporte_inventario`
-- (See below for the actual view)
--
CREATE TABLE `reporte_inventario` (
`codigo_origen` varchar(255)
,`id_producto` int(20)
,`codigo` varchar(50)
,`nombre_producto` varchar(50)
,`nombre_categoria` varchar(50)
,`nombre_sucursal` varchar(50)
,`total_existencias` decimal(33,2)
,`id_sucursal` int(20)
,`id_tipo` int(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `reporte_ventas`
-- (See below for the actual view)
--
CREATE TABLE `reporte_ventas` (
`nombre_sucursal` varchar(50)
,`nombre_cliente` varchar(201)
,`venta` decimal(10,2)
,`id_sucursal` int(20)
,`id_cliente` int(20)
,`fecha_creacion` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `reporte_venta_detalle`
-- (See below for the actual view)
--
CREATE TABLE `reporte_venta_detalle` (
`codigo_origen` varchar(255)
,`nombre_producto` varchar(50)
,`cantidad` decimal(10,2)
,`id_sucursal` int(20)
,`fecha_creacion` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `sucursales`
--

CREATE TABLE `sucursales` (
  `id_sucursal` int(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `identificador_excel` varchar(100) DEFAULT NULL,
  `es_consignatario` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tipo`
--

CREATE TABLE `tipo` (
  `id_tipo` int(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tipo_cambio`
--

CREATE TABLE `tipo_cambio` (
  `id_tipo_cambio` int(20) NOT NULL,
  `id_moneda_muchos` int(20) NOT NULL,
  `id_moneda_uno` int(20) NOT NULL,
  `factor` decimal(10,2) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL
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
  `id_sucursal_destino` int(20) DEFAULT NULL,
  `comentario_envio` varchar(500) DEFAULT NULL,
  `comentario_recepcion` varchar(500) DEFAULT NULL,
  `id_empleado_recibe` int(20) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_recepcion` datetime DEFAULT NULL,
  `id_cliente_recibe` int(20) DEFAULT NULL,
  `es_consignacion` bit(1) NOT NULL DEFAULT b'0',
  `dias_consignacion` int(20) DEFAULT NULL,
  `porcetaje_compra_min` float DEFAULT NULL,
  `es_devuelto` bit(1) NOT NULL DEFAULT b'0'
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
  `id_empleado` int(20) DEFAULT NULL,
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
-- Table structure for table `trx_venta`
--

CREATE TABLE `trx_venta` (
  `id_venta` bigint(50) NOT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `id_cliente` int(20) DEFAULT NULL,
  `estado` varchar(1) NOT NULL DEFAULT 'P',
  `usuario_creacion` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_venta` varchar(100) DEFAULT NULL,
  `id_transaccion` bigint(50) DEFAULT NULL,
  `fecha_devolucion` datetime DEFAULT NULL,
  `credito_devolucion` decimal(10,2) DEFAULT NULL,
  `es_anulado` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `trx_venta_detalle`
--

CREATE TABLE `trx_venta_detalle` (
  `id_venta_detalle` bigint(50) NOT NULL,
  `id_venta` bigint(50) NOT NULL,
  `id_producto` int(20) NOT NULL,
  `id_sucursal` int(20) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `cantidad_devolucion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `trx_venta_formas_pago`
--

CREATE TABLE `trx_venta_formas_pago` (
  `id_venta_formas_pago` bigint(50) NOT NULL,
  `id_venta` bigint(50) NOT NULL,
  `id_forma_pago` int(20) NOT NULL,
  `id_moneda` int(5) DEFAULT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `numero_cheque` varchar(255) DEFAULT NULL,
  `id_banco` int(5) DEFAULT NULL,
  `numero_autorizacion` varchar(255) DEFAULT NULL,
  `autorizado_por` varchar(255) DEFAULT NULL,
  `numero_voucher` varchar(255) DEFAULT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `usuarios_bodegas`
--

CREATE TABLE `usuarios_bodegas` (
  `id_bodega` int(20) NOT NULL,
  `id_usuario` varchar(200) NOT NULL
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
(5, 'FECHA_EXP', '2019-12-31', '2017-03-20 22:47:21', 'admin'),
(6, 'BODEGA_CAT', '7', '0000-00-00 00:00:00', ''),
(7, 'BODEGA_CAT', '7', '2018-12-03 10:44:21', 'dev');

-- --------------------------------------------------------

--
-- Structure for view `reporte_clientes`
--
DROP TABLE IF EXISTS `reporte_clientes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_clientes`  AS  select `c`.`identificacion` AS `identificacion`,`c`.`nombres` AS `nombres`,`c`.`apellidos` AS `apellidos`,`c`.`factura_nit` AS `factura_nit`,`t`.`numero` AS `numero`,`c`.`direccion` AS `direccion`,`c`.`tiene_credito` AS `tiene_credito`,`c`.`dias_credito` AS `dias_credito`,`c`.`id_departamento` AS `id_departamento` from (`clientes` `c` left join `clientes_telefonos` `t` on((`t`.`id_cliente` = `c`.`id_cliente`))) ;

-- --------------------------------------------------------

--
-- Structure for view `reporte_inventario`
--
DROP TABLE IF EXISTS `reporte_inventario`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_inventario`  AS  select `p`.`codigo_origen` AS `codigo_origen`,`p`.`id_producto` AS `id_producto`,`p`.`codigo` AS `codigo`,`p`.`nombre` AS `nombre_producto`,max(`t`.`nombre`) AS `nombre_categoria`,max(`s`.`nombre`) AS `nombre_sucursal`,coalesce((sum(`trx`.`haber`) - sum(`trx`.`debe`)),0) AS `total_existencias`,max(`s`.`id_sucursal`) AS `id_sucursal`,max(`t`.`id_tipo`) AS `id_tipo` from (((`producto` `p` left join `tipo` `t` on((`t`.`id_tipo` = `p`.`id_tipo`))) left join `trx_transacciones` `trx` on((`trx`.`id_producto` = `p`.`id_producto`))) left join `sucursales` `s` on((`s`.`id_sucursal` = `trx`.`id_sucursal`))) group by `p`.`id_producto`,`s`.`id_sucursal` ;

-- --------------------------------------------------------

--
-- Structure for view `reporte_ventas`
--
DROP TABLE IF EXISTS `reporte_ventas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_ventas`  AS  select distinct `s`.`nombre` AS `nombre_sucursal`,concat(`c`.`nombres`,' ',`c`.`apellidos`) AS `nombre_cliente`,`v`.`total` AS `venta`,`s`.`id_sucursal` AS `id_sucursal`,`c`.`id_cliente` AS `id_cliente`,`v`.`fecha_creacion` AS `fecha_creacion` from (((`trx_venta` `v` left join `clientes` `c` on((`c`.`id_cliente` = `v`.`id_cliente`))) left join `trx_venta_detalle` `vd` on((`vd`.`id_venta` = `v`.`id_venta`))) left join `sucursales` `s` on((`s`.`id_sucursal` = `vd`.`id_sucursal`))) ;

-- --------------------------------------------------------

--
-- Structure for view `reporte_venta_detalle`
--
DROP TABLE IF EXISTS `reporte_venta_detalle`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_venta_detalle`  AS  select `p`.`codigo_origen` AS `codigo_origen`,`p`.`nombre` AS `nombre_producto`,`vd`.`cantidad` AS `cantidad`,`s`.`id_sucursal` AS `id_sucursal`,`v`.`fecha_creacion` AS `fecha_creacion` from (((`trx_venta` `v` left join `trx_venta_detalle` `vd` on((`vd`.`id_venta` = `v`.`id_venta`))) left join `sucursales` `s` on((`s`.`id_sucursal` = `vd`.`id_sucursal`))) left join `producto` `p` on((`p`.`id_producto` = `vd`.`id_producto`))) ;

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
-- Indexes for table `bancos`
--
ALTER TABLE `bancos`
  ADD PRIMARY KEY (`id_banco`);

--
-- Indexes for table `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD KEY `clientes_pais` (`id_pais`),
  ADD KEY `clientes_departamento` (`id_departamento`),
  ADD KEY `clientes_municipio` (`id_municipio`),
  ADD KEY `clientes_tipo_precio` (`id_tipo_precio`),
  ADD KEY `clientes_cliente_referido` (`id_cliente_referido`);

--
-- Indexes for table `clientes_bodegas`
--
ALTER TABLE `clientes_bodegas`
  ADD PRIMARY KEY (`id_cliente_bodega`),
  ADD KEY `clientes_bodegas_cliente` (`id_cliente`),
  ADD KEY `clientes_bodegas_bodega` (`id_sucursal`);

--
-- Indexes for table `clientes_telefonos`
--
ALTER TABLE `clientes_telefonos`
  ADD PRIMARY KEY (`id_cliente_telefono`),
  ADD KEY `clientes_telefonos_cliente` (`id_cliente`);

--
-- Indexes for table `clientes_tipos_precio`
--
ALTER TABLE `clientes_tipos_precio`
  ADD PRIMARY KEY (`id_tipo_precio`);

--
-- Indexes for table `cuentas`
--
ALTER TABLE `cuentas`
  ADD PRIMARY KEY (`id_cuenta`);

--
-- Indexes for table `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`id_departamento`),
  ADD KEY `id_pais` (`id_pais`);

--
-- Indexes for table `descuentos`
--
ALTER TABLE `descuentos`
  ADD PRIMARY KEY (`id_descuento`);

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
-- Indexes for table `formas_pago`
--
ALTER TABLE `formas_pago`
  ADD PRIMARY KEY (`id_forma_pago`);

--
-- Indexes for table `monedas`
--
ALTER TABLE `monedas`
  ADD PRIMARY KEY (`id_moneda`);

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
  ADD KEY `id_tipo` (`id_tipo`);

--
-- Indexes for table `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- Indexes for table `sucursales`
--
ALTER TABLE `sucursales`
  ADD PRIMARY KEY (`id_sucursal`);

--
-- Indexes for table `tipo`
--
ALTER TABLE `tipo`
  ADD PRIMARY KEY (`id_tipo`);

--
-- Indexes for table `tipo_cambio`
--
ALTER TABLE `tipo_cambio`
  ADD PRIMARY KEY (`id_tipo_cambio`),
  ADD KEY `id_moneda_muchos` (`id_moneda_muchos`),
  ADD KEY `id_moneda_uno` (`id_moneda_uno`);

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
-- Indexes for table `trx_venta`
--
ALTER TABLE `trx_venta`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `fk_venta_transacciones` (`id_transaccion`);

--
-- Indexes for table `trx_venta_detalle`
--
ALTER TABLE `trx_venta_detalle`
  ADD PRIMARY KEY (`id_venta_detalle`),
  ADD KEY `id_venta` (`id_venta`);

--
-- Indexes for table `trx_venta_formas_pago`
--
ALTER TABLE `trx_venta_formas_pago`
  ADD PRIMARY KEY (`id_venta_formas_pago`),
  ADD KEY `id_venta` (`id_venta`);

--
-- Indexes for table `usuarios_bodegas`
--
ALTER TABLE `usuarios_bodegas`
  ADD KEY `FK_usuarios_bodegas_bodegas` (`id_bodega`);

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
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `app_module_category`
--
ALTER TABLE `app_module_category`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `app_profile`
--
ALTER TABLE `app_profile`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bancos`
--
ALTER TABLE `bancos`
  MODIFY `id_banco` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clientes_bodegas`
--
ALTER TABLE `clientes_bodegas`
  MODIFY `id_cliente_bodega` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clientes_telefonos`
--
ALTER TABLE `clientes_telefonos`
  MODIFY `id_cliente_telefono` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clientes_tipos_precio`
--
ALTER TABLE `clientes_tipos_precio`
  MODIFY `id_tipo_precio` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cuentas`
--
ALTER TABLE `cuentas`
  MODIFY `id_cuenta` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `id_departamento` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `descuentos`
--
ALTER TABLE `descuentos`
  MODIFY `id_descuento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id_empleado` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `estados_proyecto`
--
ALTER TABLE `estados_proyecto`
  MODIFY `id_estado_proyecto` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `formas_pago`
--
ALTER TABLE `formas_pago`
  MODIFY `id_forma_pago` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `monedas`
--
ALTER TABLE `monedas`
  MODIFY `id_moneda` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `movimiento_sucursales_estado`
--
ALTER TABLE `movimiento_sucursales_estado`
  MODIFY `id_movimiento_sucursales_estado` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `municipios`
--
ALTER TABLE `municipios`
  MODIFY `id_municipio` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `paises`
--
ALTER TABLE `paises`
  MODIFY `id_pais` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT for table `sucursales`
--
ALTER TABLE `sucursales`
  MODIFY `id_sucursal` int(20) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `trx_venta`
--
ALTER TABLE `trx_venta`
  MODIFY `id_venta` bigint(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trx_venta_detalle`
--
ALTER TABLE `trx_venta_detalle`
  MODIFY `id_venta_detalle` bigint(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trx_venta_formas_pago`
--
ALTER TABLE `trx_venta_formas_pago`
  MODIFY `id_venta_formas_pago` bigint(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `variables_sistema`
--
ALTER TABLE `variables_sistema`
  MODIFY `id_variables_sistema` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- Constraints for table `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_departamento` FOREIGN KEY (`id_departamento`) REFERENCES `departamentos` (`id_departamento`),
  ADD CONSTRAINT `clientes_municipio` FOREIGN KEY (`id_municipio`) REFERENCES `municipios` (`id_municipio`),
  ADD CONSTRAINT `clientes_pais` FOREIGN KEY (`id_pais`) REFERENCES `paises` (`id_pais`),
  ADD CONSTRAINT `clientes_tipo_precio` FOREIGN KEY (`id_tipo_precio`) REFERENCES `clientes_tipos_precio` (`id_tipo_precio`);

--
-- Constraints for table `clientes_bodegas`
--
ALTER TABLE `clientes_bodegas`
  ADD CONSTRAINT `clientes_bodegas_bodega` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  ADD CONSTRAINT `clientes_bodegas_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);

--
-- Constraints for table `clientes_telefonos`
--
ALTER TABLE `clientes_telefonos`
  ADD CONSTRAINT `clientes_telefonos_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);

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

--
-- Constraints for table `trx_venta`
--
ALTER TABLE `trx_venta`
  ADD CONSTRAINT `fk_venta_transacciones` FOREIGN KEY (`id_transaccion`) REFERENCES `trx_transacciones` (`id_transaccion`);

--
-- Constraints for table `trx_venta_detalle`
--
ALTER TABLE `trx_venta_detalle`
  ADD CONSTRAINT `venta_detalle_ibfk_2` FOREIGN KEY (`id_venta`) REFERENCES `trx_venta` (`id_venta`);

--
-- Constraints for table `trx_venta_formas_pago`
--
ALTER TABLE `trx_venta_formas_pago`
  ADD CONSTRAINT `venta_formas_pago_ibfk_2` FOREIGN KEY (`id_venta`) REFERENCES `trx_venta` (`id_venta`);

--
-- Constraints for table `usuarios_bodegas`
--
ALTER TABLE `usuarios_bodegas`
  ADD CONSTRAINT `FK_usuarios_bodegas_bodegas` FOREIGN KEY (`id_bodega`) REFERENCES `sucursales` (`id_sucursal`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
