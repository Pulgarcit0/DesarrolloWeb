/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.9-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: gestion_financiera
-- ------------------------------------------------------
-- Server version	10.11.9-MariaDB-0+deb12u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Consejos_Financieros`
--

DROP TABLE IF EXISTS `Consejos_Financieros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Consejos_Financieros` (
  `id_consejo` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha_publicacion` date NOT NULL,
  `categoria_consejo` enum('Ahorro','Inversión','Gasto','Deuda') NOT NULL,
  `id_material` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_consejo`),
  KEY `id_material` (`id_material`),
  CONSTRAINT `Consejos_Financieros_ibfk_1` FOREIGN KEY (`id_material`) REFERENCES `Educacion_Financiera` (`id_material`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Consejos_Financieros`
--

LOCK TABLES `Consejos_Financieros` WRITE;
/*!40000 ALTER TABLE `Consejos_Financieros` DISABLE KEYS */;
/*!40000 ALTER TABLE `Consejos_Financieros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Educacion_Financiera`
--

DROP TABLE IF EXISTS `Educacion_Financiera`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Educacion_Financiera` (
  `id_material` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `fecha_publicacion` date DEFAULT NULL,
  PRIMARY KEY (`id_material`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Educacion_Financiera`
--

LOCK TABLES `Educacion_Financiera` WRITE;
/*!40000 ALTER TABLE `Educacion_Financiera` DISABLE KEYS */;
/*!40000 ALTER TABLE `Educacion_Financiera` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Eventos_Financieros`
--

DROP TABLE IF EXISTS `Eventos_Financieros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Eventos_Financieros` (
  `id_evento` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `nombre_evento` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha_evento` date NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `tipo_evento` enum('Recordatorio','Meta de Ahorro','Pago Programado') NOT NULL,
  `id_meta` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_evento`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_meta` (`id_meta`),
  CONSTRAINT `Eventos_Financieros_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `Usuarios` (`id_usuario`),
  CONSTRAINT `Eventos_Financieros_ibfk_2` FOREIGN KEY (`id_meta`) REFERENCES `Metas_De_Ahorro` (`id_meta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Eventos_Financieros`
--

LOCK TABLES `Eventos_Financieros` WRITE;
/*!40000 ALTER TABLE `Eventos_Financieros` DISABLE KEYS */;
/*!40000 ALTER TABLE `Eventos_Financieros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Historial_Reportes`
--

DROP TABLE IF EXISTS `Historial_Reportes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Historial_Reportes` (
  `id_reporte` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(255) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `total_gastos` decimal(10,2) NOT NULL,
  `total_ingresos` decimal(10,2) NOT NULL,
  `balance_neto` decimal(10,2) NOT NULL,
  `fecha_generacion` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_reporte`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Historial_Reportes`
--

LOCK TABLES `Historial_Reportes` WRITE;
/*!40000 ALTER TABLE `Historial_Reportes` DISABLE KEYS */;
INSERT INTO `Historial_Reportes` VALUES
(1,'Usuario Logueado','2024-12-01','2024-12-06',4000.00,20000.00,16000.00,'2024-12-07 04:51:15');
/*!40000 ALTER TABLE `Historial_Reportes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Metas_De_Ahorro`
--

DROP TABLE IF EXISTS `Metas_De_Ahorro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Metas_De_Ahorro` (
  `id_meta` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_meta` varchar(100) NOT NULL,
  `monto_meta` decimal(10,2) NOT NULL,
  `monto_actual` decimal(10,2) DEFAULT 0.00,
  `descripcion` text DEFAULT NULL,
  `estado` enum('En progreso','Completada') DEFAULT 'En progreso',
  PRIMARY KEY (`id_meta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Metas_De_Ahorro`
--

LOCK TABLES `Metas_De_Ahorro` WRITE;
/*!40000 ALTER TABLE `Metas_De_Ahorro` DISABLE KEYS */;
/*!40000 ALTER TABLE `Metas_De_Ahorro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Notificaciones`
--

DROP TABLE IF EXISTS `Notificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Notificaciones` (
  `id_notificacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `mensaje` text NOT NULL,
  `fecha_envio` datetime NOT NULL,
  `leida` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id_notificacion`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `Notificaciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `Usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Notificaciones`
--

LOCK TABLES `Notificaciones` WRITE;
/*!40000 ALTER TABLE `Notificaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `Notificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Pagos`
--

DROP TABLE IF EXISTS `Pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Pagos` (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `id_tipo_gasto` int(11) DEFAULT NULL,
  `monto_pagado` decimal(10,2) NOT NULL,
  `fecha_pago` date NOT NULL,
  `descripcion` text DEFAULT NULL,
  `metodo_pago` enum('Efectivo','Tarjeta','Transaccion') NOT NULL,
  `id_recurrencia` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_pago`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_tipo_gasto` (`id_tipo_gasto`),
  KEY `id_recurrencia` (`id_recurrencia`),
  CONSTRAINT `Pagos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `Usuarios` (`id_usuario`),
  CONSTRAINT `Pagos_ibfk_2` FOREIGN KEY (`id_tipo_gasto`) REFERENCES `Tipos_De_Gasto` (`id_tipo_gasto`),
  CONSTRAINT `Pagos_ibfk_3` FOREIGN KEY (`id_recurrencia`) REFERENCES `Recurrencias` (`id_recurrencia`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Pagos`
--

LOCK TABLES `Pagos` WRITE;
/*!40000 ALTER TABLE `Pagos` DISABLE KEYS */;
INSERT INTO `Pagos` VALUES
(1,1,1,1500.00,'2024-12-01',NULL,'Efectivo',NULL),
(2,1,2,1200.00,'2024-12-02',NULL,'Tarjeta',NULL),
(3,1,3,800.00,'2024-12-03',NULL,'Transaccion',NULL),
(4,1,4,500.00,'2024-12-04',NULL,'Efectivo',NULL),
(5,1,1,-15000.00,'2024-12-01','Salario mensual','Transaccion',NULL),
(6,1,2,-5000.00,'2024-12-05','Venta de productos','Efectivo',NULL),
(7,1,3,-2000.00,'2024-12-10','Intereses de inversiones','Tarjeta',NULL),
(8,1,4,-1000.00,'2024-12-15','Regalo de cumpleaños','Efectivo',NULL),
(9,1,5,-300.00,'2024-12-20','Devolución de un préstamo','Efectivo',NULL),
(10,NULL,4,200.00,'2024-12-07',NULL,'Efectivo',NULL),
(11,NULL,11,3000.00,'2024-12-07',NULL,'Efectivo',NULL),
(12,NULL,7,3000.00,'2024-12-07',NULL,'Efectivo',NULL),
(13,NULL,5,-3000.00,'2024-12-07',NULL,'Efectivo',NULL),
(14,NULL,8,2000.00,'2024-12-07',NULL,'Efectivo',NULL),
(15,NULL,10,1222.00,'2024-12-07',NULL,'Efectivo',NULL),
(16,NULL,10,1222.00,'2024-12-07',NULL,'Efectivo',NULL),
(17,NULL,6,10000.00,'2024-12-07',NULL,'Efectivo',NULL),
(18,NULL,8,-30000.00,'2024-12-07',NULL,'Efectivo',NULL),
(19,NULL,1,-1.00,'2024-12-07',NULL,'Efectivo',NULL),
(20,NULL,1,15000.00,'2024-12-07',NULL,'Efectivo',NULL),
(21,NULL,1,-1.00,'2024-12-07',NULL,'Efectivo',NULL),
(22,NULL,1,0.00,'2024-12-07',NULL,'Efectivo',NULL),
(23,NULL,1,-20000.00,'2024-12-07',NULL,'Efectivo',NULL);
/*!40000 ALTER TABLE `Pagos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Preferencias_Usuario`
--

DROP TABLE IF EXISTS `Preferencias_Usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Preferencias_Usuario` (
  `id_preferencia` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `notificaciones_email` tinyint(1) DEFAULT 1,
  `notificaciones_push` tinyint(1) DEFAULT 1,
  `preferencia_idioma` varchar(20) DEFAULT 'es',
  `preferencia_moneda` varchar(10) DEFAULT 'MXN',
  PRIMARY KEY (`id_preferencia`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `Preferencias_Usuario_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `Usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Preferencias_Usuario`
--

LOCK TABLES `Preferencias_Usuario` WRITE;
/*!40000 ALTER TABLE `Preferencias_Usuario` DISABLE KEYS */;
/*!40000 ALTER TABLE `Preferencias_Usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Recurrencias`
--

DROP TABLE IF EXISTS `Recurrencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Recurrencias` (
  `id_recurrencia` int(11) NOT NULL AUTO_INCREMENT,
  `id_tipo_gasto` int(11) DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `frecuencia` enum('Diaria','Semanal','Mensual','Anual') NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id_recurrencia`),
  KEY `id_tipo_gasto` (`id_tipo_gasto`),
  CONSTRAINT `Recurrencias_ibfk_1` FOREIGN KEY (`id_tipo_gasto`) REFERENCES `Tipos_De_Gasto` (`id_tipo_gasto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Recurrencias`
--

LOCK TABLES `Recurrencias` WRITE;
/*!40000 ALTER TABLE `Recurrencias` DISABLE KEYS */;
/*!40000 ALTER TABLE `Recurrencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Reportes`
--

DROP TABLE IF EXISTS `Reportes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Reportes` (
  `id_reporte` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('Mensual','Anual','Personalizado') NOT NULL,
  `fecha_generacion` date NOT NULL,
  `contenido` text DEFAULT NULL,
  PRIMARY KEY (`id_reporte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Reportes`
--

LOCK TABLES `Reportes` WRITE;
/*!40000 ALTER TABLE `Reportes` DISABLE KEYS */;
/*!40000 ALTER TABLE `Reportes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Tipos_De_Gasto`
--

DROP TABLE IF EXISTS `Tipos_De_Gasto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Tipos_De_Gasto` (
  `id_tipo_gasto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_tipo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id_tipo_gasto`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Tipos_De_Gasto`
--

LOCK TABLES `Tipos_De_Gasto` WRITE;
/*!40000 ALTER TABLE `Tipos_De_Gasto` DISABLE KEYS */;
INSERT INTO `Tipos_De_Gasto` VALUES
(1,'Casa','Gastos relacionados con el hogar'),
(2,'Comida','Gastos de alimentación'),
(3,'Transporte','Gastos de transporte'),
(4,'Entretenimiento','Gastos de ocio'),
(5,'Salario','Ingresos provenientes del salario'),
(6,'Venta','Ingresos por ventas realizadas'),
(7,'Inversiones','Ganancias por inversiones'),
(8,'Regalos','Dinero recibido como regalo'),
(10,'hola','Ganancia por decir hola'),
(11,'famamacia','gastos de medicamentos');
/*!40000 ALTER TABLE `Tipos_De_Gasto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Usuarios`
--

DROP TABLE IF EXISTS `Usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `fecha_registro` date NOT NULL,
  `ultimo_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Usuarios`
--

LOCK TABLES `Usuarios` WRITE;
/*!40000 ALTER TABLE `Usuarios` DISABLE KEYS */;
INSERT INTO `Usuarios` VALUES
(1,'Hugo','Martínez','hugo@example.com','12345678','2024-12-03',NULL),
(2,'admin','admin','admin@gmail.com','$2y$10$5J2N4.04Q0.l95WARmH9HuBNwqXCX0/MxUlH62Iq4RzjW14Y8cxuW','2024-12-03',NULL),
(3,'admin','admin','administrador@gmail.com','$2y$10$OqtGICYtU9OTRlOrprP5Lu7LeKgik2yofrgl3RV9o.Gpuh53rNycC','2024-12-03',NULL),
(4,'hugolino','Valentin','vale.metal520.69@gmail.com','$2y$10$1yhkQGvkrZTtJJDIiHKWbuNSNxZACPNz2w.Rfx2uCWl5mBVPJyaua','2024-12-06',NULL);
/*!40000 ALTER TABLE `Usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-12-10 23:36:57
