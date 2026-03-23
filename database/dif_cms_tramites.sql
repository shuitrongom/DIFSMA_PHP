CREATE DATABASE  IF NOT EXISTS `difsanma_dif_cms` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `difsanma_dif_cms`;
-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: localhost    Database: difsanma_dif_cms
-- ------------------------------------------------------
-- Server version	8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tramites`
--

DROP TABLE IF EXISTS `tramites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tramites` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'PMPNNA, DAAM, DANF, DAD, DPAF, DSJAIG',
  `titulo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `imagen_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contenido` longtext COLLATE utf8mb4_unicode_ci COMMENT 'HTML enriquecido (TinyMCE)',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tramites`
--

LOCK TABLES `tramites` WRITE;
/*!40000 ALTER TABLE `tramites` DISABLE KEYS */;
INSERT INTO `tramites` VALUES (1,'PMPNNA','Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes',NULL,'<p>Información sobre los servicios de la Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes.</p>','2026-03-19 16:03:45'),(2,'DAAM','Dirección de Atención a Adultos Mayores',NULL,'<p>Información sobre los programas y servicios de atención a adultos mayores.</p>','2026-03-19 16:03:45'),(3,'DANF','Dirección de Alimentación y Nutrición Familiar',NULL,'<p>Información sobre los programas de alimentación y nutrición familiar.</p>','2026-03-19 16:03:45'),(4,'DAD','Dirección de Atención a la Discapacidad',NULL,'<p>Información sobre los servicios de atención a personas con discapacidad.</p>','2026-03-19 16:03:45'),(5,'DPAF','Dirección de Prevención y Bienestar Familiar',NULL,'<p>Información sobre los programas de prevención y bienestar familiar.</p>','2026-03-19 16:03:45'),(6,'DSJAIG','Dirección de Servicios Jurídicos – Asistenciales e Igualdad de Género',NULL,'<p>Información sobre los servicios jurídicos asistenciales e igualdad de género.</p>','2026-03-19 16:03:45');
/*!40000 ALTER TABLE `tramites` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-23 13:53:03
