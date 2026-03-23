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
-- Table structure for table `noticias_imagenes`
--

DROP TABLE IF EXISTS `noticias_imagenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `noticias_imagenes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `imagen_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_noticia` date NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_fecha` (`fecha_noticia`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `noticias_imagenes`
--

LOCK TABLES `noticias_imagenes` WRITE;
/*!40000 ALTER TABLE `noticias_imagenes` DISABLE KEYS */;
INSERT INTO `noticias_imagenes` VALUES (1,'uploads/images/b73462b7f5740034f3600a3956dfedcc.png','2026-03-19',1,'2026-03-19 16:30:28'),(2,'uploads/images/2019bcb3e6e4a865ba7277c141e3c072.png','2026-03-19',1,'2026-03-19 16:30:37'),(3,'uploads/images/d86bdce00dfcce1c574721d9084541a6.png','2026-03-19',1,'2026-03-19 16:30:47'),(4,'uploads/images/69a5c58583157854db338c95948cb829.png','2026-03-20',1,'2026-03-19 22:11:04'),(5,'uploads/images/d3e04fbd06061ccadae05ed25e5e6a19.png','2026-03-20',1,'2026-03-19 22:11:10'),(6,'uploads/images/78b7ae8ca9101855d8495eb939b17a50.png','2026-03-20',1,'2026-03-19 22:11:16'),(7,'uploads/images/aa8a5a229e4e3cd1b093dc8cf4cc4c73.png','2026-03-23',1,'2026-03-23 08:59:41');
/*!40000 ALTER TABLE `noticias_imagenes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-23 13:53:05
