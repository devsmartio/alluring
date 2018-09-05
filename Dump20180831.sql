CREATE DATABASE  IF NOT EXISTS `alluring` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `alluring`;
-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: 45.79.16.122    Database: alluring
-- ------------------------------------------------------
-- Server version	5.7.22-0ubuntu0.17.10.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `app_defaultscreen`
--

DROP TABLE IF EXISTS `app_defaultscreen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_defaultscreen` (
  `id_profile` int(20) NOT NULL,
  `id_module` int(20) NOT NULL,
  PRIMARY KEY (`id_profile`,`id_module`),
  KEY `id_module` (`id_module`),
  CONSTRAINT `app_defaultscreen_ibfk_1` FOREIGN KEY (`id_profile`) REFERENCES `app_profile` (`ID`),
  CONSTRAINT `app_defaultscreen_ibfk_2` FOREIGN KEY (`id_module`) REFERENCES `app_modules` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_defaultscreen`
--

LOCK TABLES `app_defaultscreen` WRITE;
/*!40000 ALTER TABLE `app_defaultscreen` DISABLE KEYS */;
/*!40000 ALTER TABLE `app_defaultscreen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_module_category`
--

DROP TABLE IF EXISTS `app_module_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_module_category` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(50) NOT NULL,
  `ICON` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_module_category`
--

LOCK TABLES `app_module_category` WRITE;
/*!40000 ALTER TABLE `app_module_category` DISABLE KEYS */;
INSERT INTO `app_module_category` VALUES (1,'Configuraciones','glyphicon-cog'),(2,'Operaciones','glyphicon-wrench'),(3,'Reportes','glyphicon-list-alt');
/*!40000 ALTER TABLE `app_module_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_modules`
--

DROP TABLE IF EXISTS `app_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_modules` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(50) NOT NULL,
  `PATH` varchar(50) NOT NULL,
  `LOAD_SEQ` int(20) NOT NULL DEFAULT '1',
  `FK_MODULE_CATEGORY` int(20) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK_MODULE_CATEGORY` (`FK_MODULE_CATEGORY`),
  CONSTRAINT `app_modules_ibfk_1` FOREIGN KEY (`FK_MODULE_CATEGORY`) REFERENCES `app_module_category` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_modules`
--

LOCK TABLES `app_modules` WRITE;
/*!40000 ALTER TABLE `app_modules` DISABLE KEYS */;
INSERT INTO `app_modules` VALUES (1,'M?dulos','MantModulos',1,1),(2,'Perfiles','MantPerfiles',2,1),(3,'Usuarios','MantUsuarios',2,1),(4,'Accesos','PermissionsManager',2,1),(5,'Monedas','MantMonedas',2,1),(6,'Clientes','MantClientes',1,1),(7,'Proveedores','MantProveedores',1,1),(8,'Empleados','MantEmpleados',1,1),(9,'Tipos de producto','MantTipoProducto',1,1),(10,'Productos','MantProductos',1,1),(11,'Ingreso inventario','TrxIngresoInventario',1,2),(12,'Traslado sucursales','TrxMovimientoSucursales',1,2),(13,'Variables del sistema','MantVariablesSistema',1,1),(14,'Inventario','RptInventario',1,3),(15,'Pantalla de inicio','MantDefaultScreen',1,1),(16,'Salida inventario','TrxSalidaInventario',1,2),(17,'Paises','MantPaises',1,1),(18,'Departamentos','MantDepartamentos',1,1),(19,'Municipios','MantMunicipios',1,1),(20,'Tipo de Cambio','MantTipoCambio',1,1),(22,'Administracion Clientes','TrxAdministracionClientes',1,2),(23,'Tipos de Cliente','MantTipoCliente',1,1),(24,'Categorias de Productos','MantCategoriaProd',1,1),(25,'Mantenimiento de Bodegas','MantBodegas',1,1),(28,'Administracion de Productos','TrxAdministracionProductos',1,2),(29,'Carga Masiva de Productos','TrxCargaMasivaProductos',1,2),(30,'Traslado de Bodegas','TrxTrasladoBodegas',1,2),(31,'Carga Masiva de Clientes','TxCargaMasivaClientes',1,2),(32,'Generacion de Etiquetas','TrxGeneracionEtiquetas',1,2),(33,'Reingreso de Consignacion','TrxReingresoConsignacion',1,2),(34,'Ventas','TrxVenta',1,2),(35,'Reporte Clientes','RptClientes',1,3);
/*!40000 ALTER TABLE `app_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_profile`
--

DROP TABLE IF EXISTS `app_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_profile` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(50) NOT NULL,
  `STATUS` int(5) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_profile`
--

LOCK TABLES `app_profile` WRITE;
/*!40000 ALTER TABLE `app_profile` DISABLE KEYS */;
INSERT INTO `app_profile` VALUES (1,'Super Admin',1),(2,'Ventas',1),(3,'Admin',1);
/*!40000 ALTER TABLE `app_profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_profile_access`
--

DROP TABLE IF EXISTS `app_profile_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_profile_access` (
  `FK_PROFILE` int(20) NOT NULL,
  `FK_MODULE` int(20) NOT NULL,
  PRIMARY KEY (`FK_PROFILE`,`FK_MODULE`),
  KEY `FK_MODULE` (`FK_MODULE`),
  CONSTRAINT `app_profile_access_ibfk_1` FOREIGN KEY (`FK_PROFILE`) REFERENCES `app_profile` (`ID`),
  CONSTRAINT `app_profile_access_ibfk_2` FOREIGN KEY (`FK_MODULE`) REFERENCES `app_modules` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_profile_access`
--

LOCK TABLES `app_profile_access` WRITE;
/*!40000 ALTER TABLE `app_profile_access` DISABLE KEYS */;
INSERT INTO `app_profile_access` VALUES (1,1),(1,2),(3,2),(1,3),(3,3),(1,4),(1,5),(3,5),(1,8),(3,8),(1,13),(1,17),(3,17),(1,18),(3,18),(1,19),(3,19),(1,20),(3,20),(1,22),(3,22),(1,23),(3,23),(1,24),(3,24),(1,25),(3,25),(1,28),(3,28),(1,29),(3,29),(1,30),(3,30),(1,31),(3,31),(1,32),(3,32),(1,33),(3,33),(1,34),(3,34),(1,35),(3,35);
/*!40000 ALTER TABLE `app_profile_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_user`
--

DROP TABLE IF EXISTS `app_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_user` (
  `ID` varchar(200) NOT NULL,
  `FIRST_NAME` varchar(200) NOT NULL,
  `LAST_NAME` varchar(200) DEFAULT NULL,
  `PASSWORD` varchar(200) NOT NULL,
  `FK_PROFILE` int(20) NOT NULL DEFAULT '1',
  `CREATED` datetime NOT NULL,
  `LAST_LOGIN` datetime NOT NULL,
  `is_seller` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_user`
--

LOCK TABLES `app_user` WRITE;
/*!40000 ALTER TABLE `app_user` DISABLE KEYS */;
INSERT INTO `app_user` VALUES ('aXNhLmZlcg','Isabel','Fernandez','1c876b3d79a5c98000b171decc6360b5',3,'2018-09-04 13:17:47','2018-09-04 13:17:47','\0'),('cHJ1ZWJhcw','Pruebas ','Ejemplos','e10adc3949ba59abbe56e057f20f883e',1,'2016-08-24 19:32:42','2016-08-24 19:32:42',''),('cmJyYXVu','Roberto','Braun','fe01ce2a7fbac8fafaed7c982a04e229',1,'2018-09-04 12:46:11','2018-09-04 12:46:11','\0'),('ZW9oZXJyZXI','Eder','Herrera','e10adc3949ba59abbe56e057f20f883e',1,'2018-07-21 00:46:44','2018-07-21 00:46:44','');
/*!40000 ALTER TABLE `app_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bancos`
--

DROP TABLE IF EXISTS `bancos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bancos` (
  `id_banco` int(5) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  PRIMARY KEY (`id_banco`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bancos`
--

LOCK TABLES `bancos` WRITE;
/*!40000 ALTER TABLE `bancos` DISABLE KEYS */;
INSERT INTO `bancos` VALUES (1,'Banco Banrural','Pruebas','2018-08-20 00:00:00'),(2,'Banco Industrial','Pruebas','2018-08-20 00:00:00'),(3,'Banco GyT Continental','Pruebas','2018-08-20 00:00:00');
/*!40000 ALTER TABLE `bancos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `id_cliente` int(20) NOT NULL AUTO_INCREMENT,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `identificacion` varchar(20) NOT NULL,
  `id_pais` int(5) DEFAULT NULL,
  `id_departamento` int(5) DEFAULT NULL,
  `id_municipio` int(20) DEFAULT NULL,
  `correo` varchar(100) NOT NULL,
  `id_tipo_precio` int(5) DEFAULT NULL,
  `id_empleado` int(20) DEFAULT NULL,
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
  PRIMARY KEY (`id_cliente`),
  KEY `clientes_pais` (`id_pais`),
  KEY `clientes_departamento` (`id_departamento`),
  KEY `clientes_municipio` (`id_municipio`),
  KEY `clientes_tipo_precio` (`id_tipo_precio`),
  KEY `clientes_empleado` (`id_empleado`),
  KEY `clientes_cliente_referido` (`id_cliente_referido`),
  CONSTRAINT `clientes_departamento` FOREIGN KEY (`id_departamento`) REFERENCES `departamentos` (`id_departamento`),
  CONSTRAINT `clientes_empleado` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id_empleado`),
  CONSTRAINT `clientes_municipio` FOREIGN KEY (`id_municipio`) REFERENCES `municipios` (`id_municipio`),
  CONSTRAINT `clientes_pais` FOREIGN KEY (`id_pais`) REFERENCES `paises` (`id_pais`),
  CONSTRAINT `clientes_tipo_precio` FOREIGN KEY (`id_tipo_precio`) REFERENCES `clientes_tipos_precio` (`id_tipo_precio`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes_bodegas`
--

DROP TABLE IF EXISTS `clientes_bodegas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes_bodegas` (
  `id_cliente_bodega` int(20) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(20) NOT NULL,
  `id_sucursal` int(20) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  PRIMARY KEY (`id_cliente_bodega`),
  KEY `clientes_bodegas_cliente` (`id_cliente`),
  KEY `clientes_bodegas_bodega` (`id_sucursal`),
  CONSTRAINT `clientes_bodegas_bodega` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `clientes_bodegas_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes_bodegas`
--

LOCK TABLES `clientes_bodegas` WRITE;
/*!40000 ALTER TABLE `clientes_bodegas` DISABLE KEYS */;
/*!40000 ALTER TABLE `clientes_bodegas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes_telefonos`
--

DROP TABLE IF EXISTS `clientes_telefonos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes_telefonos` (
  `id_cliente_telefono` int(20) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(20) NOT NULL,
  `numero` int(8) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  PRIMARY KEY (`id_cliente_telefono`),
  KEY `clientes_telefonos_cliente` (`id_cliente`),
  CONSTRAINT `clientes_telefonos_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes_telefonos`
--

LOCK TABLES `clientes_telefonos` WRITE;
/*!40000 ALTER TABLE `clientes_telefonos` DISABLE KEYS */;
/*!40000 ALTER TABLE `clientes_telefonos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes_tipos_precio`
--

DROP TABLE IF EXISTS `clientes_tipos_precio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes_tipos_precio` (
  `id_tipo_precio` int(5) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `porcentaje_descuento` decimal(10,2) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_creacion` varchar(100) NOT NULL,
  PRIMARY KEY (`id_tipo_precio`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes_tipos_precio`
--

LOCK TABLES `clientes_tipos_precio` WRITE;
/*!40000 ALTER TABLE `clientes_tipos_precio` DISABLE KEYS */;
INSERT INTO `clientes_tipos_precio` VALUES (1,'Cliente Tipo A',50.00,'2018-07-20 23:57:29','Pruebas '),(2,'Cliente Tipo B',35.00,'2018-07-20 23:58:17','Pruebas '),(3,'Cliente Tipo C',25.00,'2018-07-20 23:58:17','Pruebas '),(4,'Tipo Cliente D',15.00,'2018-07-26 12:01:12','Pruebas '),(5,'Cliente Tipo 25',25.00,'2018-09-03 22:07:13','Pruebas ');
/*!40000 ALTER TABLE `clientes_tipos_precio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cuentas`
--

DROP TABLE IF EXISTS `cuentas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cuentas` (
  `id_cuenta` int(20) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_cuenta`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuentas`
--

LOCK TABLES `cuentas` WRITE;
/*!40000 ALTER TABLE `cuentas` DISABLE KEYS */;
INSERT INTO `cuentas` VALUES (1,'Cuenta 1'),(2,'Inventario'),(3,'Reingreso'),(4,'Venta');
/*!40000 ALTER TABLE `cuentas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departamentos`
--

DROP TABLE IF EXISTS `departamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departamentos` (
  `id_departamento` int(5) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `id_pais` int(5) NOT NULL,
  PRIMARY KEY (`id_departamento`),
  KEY `id_pais` (`id_pais`),
  CONSTRAINT `departamentos_ibfk_1` FOREIGN KEY (`id_pais`) REFERENCES `paises` (`id_pais`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departamentos`
--

LOCK TABLES `departamentos` WRITE;
/*!40000 ALTER TABLE `departamentos` DISABLE KEYS */;
INSERT INTO `departamentos` VALUES (2,'Guatemala','Pruebas ','2017-09-06 08:06:02',1),(3,'Zacapa','Pruebas ','2017-09-06 08:11:13',1);
/*!40000 ALTER TABLE `departamentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleados`
--

DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `empleados` (
  `id_empleado` int(20) NOT NULL AUTO_INCREMENT,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `id_sucursal` int(20) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  `id_interno` varchar(50) DEFAULT NULL,
  `id_usuario` varchar(200) NOT NULL,
  `es_vendedor` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`id_empleado`),
  KEY `id_sucursal` (`id_sucursal`),
  CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleados`
--

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
INSERT INTO `empleados` VALUES (3,'Alluring','Empleado 1',5,'2018-09-04 22:57:12','Pruebas ','169','aXNhLmZlcg','');
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estados_proyecto`
--

DROP TABLE IF EXISTS `estados_proyecto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estados_proyecto` (
  `id_estado_proyecto` int(5) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `usuario_creacion` varchar(100) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id_estado_proyecto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estados_proyecto`
--

LOCK TABLES `estados_proyecto` WRITE;
/*!40000 ALTER TABLE `estados_proyecto` DISABLE KEYS */;
/*!40000 ALTER TABLE `estados_proyecto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `formas_pago`
--

DROP TABLE IF EXISTS `formas_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formas_pago` (
  `id_forma_pago` int(20) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  PRIMARY KEY (`id_forma_pago`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `formas_pago`
--

LOCK TABLES `formas_pago` WRITE;
/*!40000 ALTER TABLE `formas_pago` DISABLE KEYS */;
INSERT INTO `formas_pago` VALUES (1,'Efectivo','Pruebas','2018-08-20 00:00:00'),(2,'Cheque','Pruebas','2018-08-20 00:00:00'),(3,'Tarjeta','Pruebas','2018-08-20 00:00:00');
/*!40000 ALTER TABLE `formas_pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `generacion_etiquetas`
--

DROP TABLE IF EXISTS `generacion_etiquetas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `generacion_etiquetas` (
  `codigo_origen` varchar(255) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL DEFAULT '0.00',
  `id_sucursal` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `generacion_etiquetas`
--

LOCK TABLES `generacion_etiquetas` WRITE;
/*!40000 ALTER TABLE `generacion_etiquetas` DISABLE KEYS */;
INSERT INTO `generacion_etiquetas` VALUES ('Y02',5.00,5),('Y02',5.00,4),('Y03',5.00,5),('Y03',5.00,4),('Y01',5.00,5),('Y01',5.00,4);
/*!40000 ALTER TABLE `generacion_etiquetas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `monedas`
--

DROP TABLE IF EXISTS `monedas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `monedas` (
  `id_moneda` int(5) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `simbolo` varchar(5) NOT NULL,
  `moneda_defecto` bit(1) NOT NULL DEFAULT b'0',
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_moneda`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monedas`
--

LOCK TABLES `monedas` WRITE;
/*!40000 ALTER TABLE `monedas` DISABLE KEYS */;
INSERT INTO `monedas` VALUES (1,'Quetzal','Q','','2016-08-24 19:30:10','Bryan'),(2,'Dolar','$','\0','2016-08-27 20:22:43','Bryan'),(29,'Euro','E','\0','2018-09-03 22:03:31','Pruebas ');
/*!40000 ALTER TABLE `monedas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movimiento_sucursales_estado`
--

DROP TABLE IF EXISTS `movimiento_sucursales_estado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimiento_sucursales_estado` (
  `id_movimiento_sucursales_estado` int(5) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_creacion` varchar(100) NOT NULL,
  PRIMARY KEY (`id_movimiento_sucursales_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimiento_sucursales_estado`
--

LOCK TABLES `movimiento_sucursales_estado` WRITE;
/*!40000 ALTER TABLE `movimiento_sucursales_estado` DISABLE KEYS */;
INSERT INTO `movimiento_sucursales_estado` VALUES (1,'En Ruta','2016-10-03 00:49:09','dev'),(2,'Entregada','2016-10-03 00:49:09','dev'),(3,'Rechazada','2016-10-03 00:49:09','dev');
/*!40000 ALTER TABLE `movimiento_sucursales_estado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `municipios`
--

DROP TABLE IF EXISTS `municipios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `municipios` (
  `id_municipio` int(20) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `id_departamento` int(20) NOT NULL,
  `usuario_creacion` varchar(100) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id_municipio`),
  KEY `FK_Municipios_Departamentos` (`id_departamento`),
  CONSTRAINT `FK_Municipios_Departamentos` FOREIGN KEY (`id_departamento`) REFERENCES `departamentos` (`id_departamento`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `municipios`
--

LOCK TABLES `municipios` WRITE;
/*!40000 ALTER TABLE `municipios` DISABLE KEYS */;
INSERT INTO `municipios` VALUES (1,'Ciudad de Guatemala',2,'Pruebas ','2017-09-06 08:22:25'),(2,'Teculutan',3,'Pruebas ','2018-07-20 23:44:00');
/*!40000 ALTER TABLE `municipios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paises`
--

DROP TABLE IF EXISTS `paises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paises` (
  `id_pais` int(5) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  PRIMARY KEY (`id_pais`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paises`
--

LOCK TABLES `paises` WRITE;
/*!40000 ALTER TABLE `paises` DISABLE KEYS */;
INSERT INTO `paises` VALUES (1,'Guatemala','Pruebas ','2017-09-06 08:04:21'),(2,'El Salvador','Pruebas ','2018-07-20 23:40:33');
/*!40000 ALTER TABLE `paises` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `producto`
--

DROP TABLE IF EXISTS `producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `producto` (
  `id_producto` int(20) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `id_tipo` int(20) DEFAULT NULL,
  `precio_venta` decimal(10,2) NOT NULL DEFAULT '0.00',
  `imagen` varchar(255) NOT NULL,
  `codigo_origen` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_producto`),
  KEY `id_tipo` (`id_tipo`),
  CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`id_tipo`) REFERENCES `tipo` (`id_tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producto`
--

LOCK TABLES `producto` WRITE;
/*!40000 ALTER TABLE `producto` DISABLE KEYS */;
/*!40000 ALTER TABLE `producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proveedor`
--

DROP TABLE IF EXISTS `proveedor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedor` (
  `id_proveedor` int(20) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `nit` varchar(20) DEFAULT NULL,
  `fecha_pago` datetime DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` int(20) DEFAULT NULL,
  `es_internacional` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`id_proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedor`
--

LOCK TABLES `proveedor` WRITE;
/*!40000 ALTER TABLE `proveedor` DISABLE KEYS */;
/*!40000 ALTER TABLE `proveedor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `reporte_clientes`
--

DROP TABLE IF EXISTS `reporte_clientes`;
/*!50001 DROP VIEW IF EXISTS `reporte_clientes`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `reporte_clientes` AS SELECT 
 1 AS `identificacion`,
 1 AS `nombres`,
 1 AS `apellidos`,
 1 AS `factura_nit`,
 1 AS `numero`,
 1 AS `direccion`,
 1 AS `tiene_credito`,
 1 AS `dias_credito`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `sucursales`
--

DROP TABLE IF EXISTS `sucursales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sucursales` (
  `id_sucursal` int(20) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `identificador_excel` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sucursales`
--

LOCK TABLES `sucursales` WRITE;
/*!40000 ALTER TABLE `sucursales` DISABLE KEYS */;
INSERT INTO `sucursales` VALUES (1,'Sucursal No. 1','2018-07-21 23:35:20','Pruebas','Bodega1'),(4,'Sucursal No. 2','2018-08-03 13:53:16','Pruebas ','Bodega2'),(5,'Bodega #3','2018-09-03 22:09:47','Pruebas ','Bodega3');
/*!40000 ALTER TABLE `sucursales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo`
--

DROP TABLE IF EXISTS `tipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo` (
  `id_tipo` int(20) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  PRIMARY KEY (`id_tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo`
--

LOCK TABLES `tipo` WRITE;
/*!40000 ALTER TABLE `tipo` DISABLE KEYS */;
INSERT INTO `tipo` VALUES (1,'Categoria 1','2018-07-26 12:37:37','Pruebas '),(2,'Categoria 2','2018-07-26 12:37:45','Pruebas '),(3,'Categoria 3','2018-09-03 22:06:34','Pruebas ');
/*!40000 ALTER TABLE `tipo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_cambio`
--

DROP TABLE IF EXISTS `tipo_cambio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_cambio` (
  `id_tipo_cambio` int(20) NOT NULL AUTO_INCREMENT,
  `id_moneda_muchos` int(20) NOT NULL,
  `id_moneda_uno` int(20) NOT NULL,
  `factor` decimal(10,2) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  PRIMARY KEY (`id_tipo_cambio`),
  KEY `id_moneda_muchos` (`id_moneda_muchos`),
  KEY `id_moneda_uno` (`id_moneda_uno`),
  CONSTRAINT `tipo_cambio_ibfk_1` FOREIGN KEY (`id_moneda_muchos`) REFERENCES `monedas` (`id_moneda`),
  CONSTRAINT `tipo_cambio_ibfk_2` FOREIGN KEY (`id_moneda_uno`) REFERENCES `monedas` (`id_moneda`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_cambio`
--

LOCK TABLES `tipo_cambio` WRITE;
/*!40000 ALTER TABLE `tipo_cambio` DISABLE KEYS */;
INSERT INTO `tipo_cambio` VALUES (1,1,2,7.41,'2018-07-17 18:47:35','Pruebas '),(5,1,1,1.00,'2018-07-17 00:00:00','Pruebas');
/*!40000 ALTER TABLE `tipo_cambio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trx_movimiento_sucursales`
--

DROP TABLE IF EXISTS `trx_movimiento_sucursales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trx_movimiento_sucursales` (
  `id_movimiento_sucursales` int(20) NOT NULL AUTO_INCREMENT,
  `id_movimiento_sucursales_estado` int(5) NOT NULL,
  `id_empleado_envia` int(20) NOT NULL,
  `id_sucursal_origen` int(20) NOT NULL,
  `id_sucursal_destino` int(20) NOT NULL,
  `comentario_envio` varchar(500) DEFAULT NULL,
  `comentario_recepcion` varchar(500) DEFAULT NULL,
  `id_empleado_recibe` int(20) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_recepcion` datetime DEFAULT NULL,
  `id_cliente_recibe` int(20) DEFAULT NULL,
  `es_consignacion` bit(1) NOT NULL DEFAULT b'0',
  `dias_consignacion` int(20) DEFAULT NULL,
  `porcetaje_compra_min` float DEFAULT NULL,
  PRIMARY KEY (`id_movimiento_sucursales`),
  KEY `id_empleado_envia` (`id_empleado_envia`),
  KEY `id_sucursal_origen` (`id_sucursal_origen`),
  KEY `id_sucursal_destino` (`id_sucursal_destino`),
  KEY `id_empleado_recibe` (`id_empleado_recibe`),
  CONSTRAINT `trx_movimiento_sucursales_ibfk_1` FOREIGN KEY (`id_empleado_envia`) REFERENCES `empleados` (`id_empleado`),
  CONSTRAINT `trx_movimiento_sucursales_ibfk_2` FOREIGN KEY (`id_sucursal_origen`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `trx_movimiento_sucursales_ibfk_3` FOREIGN KEY (`id_sucursal_destino`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `trx_movimiento_sucursales_ibfk_4` FOREIGN KEY (`id_empleado_recibe`) REFERENCES `empleados` (`id_empleado`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trx_movimiento_sucursales`
--

LOCK TABLES `trx_movimiento_sucursales` WRITE;
/*!40000 ALTER TABLE `trx_movimiento_sucursales` DISABLE KEYS */;
/*!40000 ALTER TABLE `trx_movimiento_sucursales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trx_movimiento_sucursales_detalle`
--

DROP TABLE IF EXISTS `trx_movimiento_sucursales_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trx_movimiento_sucursales_detalle` (
  `id_movimiento_sucursales_detalle` int(20) NOT NULL AUTO_INCREMENT,
  `id_movimiento_sucursales` int(20) NOT NULL,
  `id_producto` int(20) NOT NULL,
  `unidades` int(20) NOT NULL,
  `id_transaccion` bigint(50) DEFAULT NULL,
  `id_transaccion_destino` bigint(50) DEFAULT NULL,
  PRIMARY KEY (`id_movimiento_sucursales_detalle`),
  KEY `id_movimiento_sucursales` (`id_movimiento_sucursales`),
  KEY `id_producto` (`id_producto`),
  KEY `id_transaccion` (`id_transaccion`),
  KEY `id_transaccion_destino` (`id_transaccion_destino`),
  CONSTRAINT `trx_movimiento_sucursales_detalle_ibfk_1` FOREIGN KEY (`id_movimiento_sucursales`) REFERENCES `trx_movimiento_sucursales` (`id_movimiento_sucursales`),
  CONSTRAINT `trx_movimiento_sucursales_detalle_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`),
  CONSTRAINT `trx_movimiento_sucursales_detalle_ibfk_3` FOREIGN KEY (`id_transaccion`) REFERENCES `trx_transacciones` (`id_transaccion`),
  CONSTRAINT `trx_movimiento_sucursales_detalle_ibfk_4` FOREIGN KEY (`id_transaccion_destino`) REFERENCES `trx_transacciones` (`id_transaccion`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trx_movimiento_sucursales_detalle`
--

LOCK TABLES `trx_movimiento_sucursales_detalle` WRITE;
/*!40000 ALTER TABLE `trx_movimiento_sucursales_detalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `trx_movimiento_sucursales_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trx_salida_inventario`
--

DROP TABLE IF EXISTS `trx_salida_inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trx_salida_inventario` (
  `id_salida_inventario` int(20) NOT NULL AUTO_INCREMENT,
  `fecha_creacion` datetime NOT NULL,
  `id_empleado` int(20) NOT NULL,
  `revertido` bit(1) NOT NULL DEFAULT b'0',
  `comentario` varchar(300) NOT NULL,
  `id_sucursal` int(5) DEFAULT NULL,
  PRIMARY KEY (`id_salida_inventario`),
  KEY `id_empleado` (`id_empleado`),
  KEY `id_sucursal` (`id_sucursal`),
  CONSTRAINT `trx_salida_inventario_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id_empleado`),
  CONSTRAINT `trx_salida_inventario_ibfk_2` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trx_salida_inventario`
--

LOCK TABLES `trx_salida_inventario` WRITE;
/*!40000 ALTER TABLE `trx_salida_inventario` DISABLE KEYS */;
/*!40000 ALTER TABLE `trx_salida_inventario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trx_salida_inventario_detalle`
--

DROP TABLE IF EXISTS `trx_salida_inventario_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trx_salida_inventario_detalle` (
  `id_salida_inventario_detalle` int(20) NOT NULL AUTO_INCREMENT,
  `id_salida_inventario` int(20) NOT NULL,
  `id_producto` int(20) NOT NULL,
  `cantidad` int(20) NOT NULL,
  `costo_producto` decimal(10,2) NOT NULL,
  `id_transaccion` bigint(50) NOT NULL,
  `comentario` varchar(20) NOT NULL,
  PRIMARY KEY (`id_salida_inventario_detalle`),
  KEY `id_salida_inventario` (`id_salida_inventario`),
  KEY `id_producto` (`id_producto`),
  KEY `id_transaccion` (`id_transaccion`),
  CONSTRAINT `trx_salida_inventario_detalle_ibfk_1` FOREIGN KEY (`id_salida_inventario`) REFERENCES `trx_salida_inventario` (`id_salida_inventario`),
  CONSTRAINT `trx_salida_inventario_detalle_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`),
  CONSTRAINT `trx_salida_inventario_detalle_ibfk_3` FOREIGN KEY (`id_transaccion`) REFERENCES `trx_transacciones` (`id_transaccion`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trx_salida_inventario_detalle`
--

LOCK TABLES `trx_salida_inventario_detalle` WRITE;
/*!40000 ALTER TABLE `trx_salida_inventario_detalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `trx_salida_inventario_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trx_transacciones`
--

DROP TABLE IF EXISTS `trx_transacciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trx_transacciones` (
  `id_transaccion` bigint(50) NOT NULL AUTO_INCREMENT,
  `id_cuenta` int(20) NOT NULL,
  `id_empleado` int(20) NOT NULL,
  `id_sucursal` int(20) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `id_moneda` int(5) DEFAULT NULL,
  `id_producto` int(20) DEFAULT NULL,
  `debe` decimal(10,2) NOT NULL DEFAULT '0.00',
  `haber` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fecha_creacion` datetime NOT NULL,
  `id_cliente` int(20) DEFAULT NULL,
  PRIMARY KEY (`id_transaccion`),
  KEY `id_cuenta` (`id_cuenta`),
  KEY `id_empleado` (`id_empleado`),
  KEY `id_sucursal` (`id_sucursal`),
  KEY `id_moneda` (`id_moneda`),
  KEY `id_producto` (`id_producto`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `trx_transacciones_ibfk_1` FOREIGN KEY (`id_cuenta`) REFERENCES `cuentas` (`id_cuenta`),
  CONSTRAINT `trx_transacciones_ibfk_2` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id_empleado`),
  CONSTRAINT `trx_transacciones_ibfk_3` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `trx_transacciones_ibfk_4` FOREIGN KEY (`id_moneda`) REFERENCES `monedas` (`id_moneda`),
  CONSTRAINT `trx_transacciones_ibfk_5` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`),
  CONSTRAINT `trx_transacciones_ibfk_6` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=179 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trx_transacciones`
--

LOCK TABLES `trx_transacciones` WRITE;
/*!40000 ALTER TABLE `trx_transacciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `trx_transacciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trx_venta`
--

DROP TABLE IF EXISTS `trx_venta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trx_venta` (
  `id_venta` bigint(50) NOT NULL AUTO_INCREMENT,
  `total` decimal(10,2) DEFAULT NULL,
  `id_cliente` int(20) DEFAULT NULL,
  `id_empleado` int(20) DEFAULT NULL,
  `estado` varchar(1) NOT NULL DEFAULT 'P',
  `usuario_creacion` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  PRIMARY KEY (`id_venta`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trx_venta`
--

LOCK TABLES `trx_venta` WRITE;
/*!40000 ALTER TABLE `trx_venta` DISABLE KEYS */;
/*!40000 ALTER TABLE `trx_venta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trx_venta_detalle`
--

DROP TABLE IF EXISTS `trx_venta_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trx_venta_detalle` (
  `id_venta_detalle` bigint(50) NOT NULL AUTO_INCREMENT,
  `id_venta` bigint(50) NOT NULL,
  `id_producto` int(20) NOT NULL,
  `id_sucursal` int(20) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `usuario_creacion` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  PRIMARY KEY (`id_venta_detalle`),
  KEY `id_venta` (`id_venta`),
  CONSTRAINT `venta_detalle_ibfk_2` FOREIGN KEY (`id_venta`) REFERENCES `trx_venta` (`id_venta`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trx_venta_detalle`
--

LOCK TABLES `trx_venta_detalle` WRITE;
/*!40000 ALTER TABLE `trx_venta_detalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `trx_venta_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trx_venta_formas_pago`
--

DROP TABLE IF EXISTS `trx_venta_formas_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trx_venta_formas_pago` (
  `id_venta_formas_pago` bigint(50) NOT NULL AUTO_INCREMENT,
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
  `fecha_creacion` datetime NOT NULL,
  PRIMARY KEY (`id_venta_formas_pago`),
  KEY `id_venta` (`id_venta`),
  CONSTRAINT `venta_formas_pago_ibfk_2` FOREIGN KEY (`id_venta`) REFERENCES `trx_venta` (`id_venta`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trx_venta_formas_pago`
--

LOCK TABLES `trx_venta_formas_pago` WRITE;
/*!40000 ALTER TABLE `trx_venta_formas_pago` DISABLE KEYS */;
/*!40000 ALTER TABLE `trx_venta_formas_pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `variables_sistema`
--

DROP TABLE IF EXISTS `variables_sistema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `variables_sistema` (
  `id_variables_sistema` int(20) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `valor` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  PRIMARY KEY (`id_variables_sistema`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `variables_sistema`
--

LOCK TABLES `variables_sistema` WRITE;
/*!40000 ALTER TABLE `variables_sistema` DISABLE KEYS */;
INSERT INTO `variables_sistema` VALUES (1,'PassCambiarPrecios','precio123456','2016-09-27 00:00:00','dev'),(2,'Perfil_SuperUsuario','1','2016-10-06 21:41:42','dev'),(3,'PassIngresarGasto','gasto1234','2016-10-11 00:42:08','dev'),(4,'ReservaCajero','500','2016-10-11 04:20:24','dev'),(5,'FECHA_EXP','2019-12-31','2017-03-20 22:47:21','admin');
/*!40000 ALTER TABLE `variables_sistema` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `reporte_clientes`
--

/*!50001 DROP VIEW IF EXISTS `reporte_clientes`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `reporte_clientes` AS select `c`.`identificacion` AS `identificacion`,`c`.`nombres` AS `nombres`,`c`.`apellidos` AS `apellidos`,`c`.`factura_nit` AS `factura_nit`,`t`.`numero` AS `numero`,`c`.`direccion` AS `direccion`,`c`.`tiene_credito` AS `tiene_credito`,`c`.`dias_credito` AS `dias_credito` from (`clientes` `c` left join `clientes_telefonos` `t` on((`t`.`id_cliente` = `c`.`id_cliente`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-09-05 13:02:09
