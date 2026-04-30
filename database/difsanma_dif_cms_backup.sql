-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 30-04-2026 a las 00:27:42
-- Versión del servidor: 5.7.44-48
-- Versión de PHP: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `difsanma_dif_cms`
--
CREATE DATABASE IF NOT EXISTS `difsanma_dif_cms` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `difsanma_dif_cms`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'bcrypt hash via password_hash()',
  `rol` enum('admin','usuario') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin`
--

INSERT INTO `admin` (`id`, `username`, `nombre`, `email`, `password`, `rol`, `activo`, `created_at`) VALUES
(1, 'admin', 'Administrador', NULL, '$2y$10$jmMV7gJLGVxLgXeBDSApmOev0nP4JnSO8xOI3FvjG3aDV4N2nMgTe', 'admin', 1, '2026-03-27 03:30:26'),
(3, 'shuitron', 'Sergio Huitron Gomez', 'shuitrongomez05@gmail.com', '$2y$10$66syxTiKt5ovhoxp6AsrXOMmkjrF4gzdf2NbR870a9hjclCTsMK2K', 'usuario', 1, '2026-04-17 11:53:16'),
(4, 'comunicación', 'Patricia Victoria Ramírez Estrada', 'comsoc@difsanmateoatenco.gob.mx', '$2y$10$3pWu96M.gC.P1TPrCJmLx.Lmj5mYdQ5DykkA53c.FeqvuAozmo2fW', 'usuario', 1, '2026-04-17 12:30:14'),
(5, 'contabilidad', 'Rene Alvarado Fonseca', 'uippet@difsanmateoatenco.gob.mx', '$2y$10$UoGRyRtOsQwNMkEFrOMgh.ZGjP9gRQAZPg0ArpN58l.Qlu.qYOCbu', 'usuario', 1, '2026-04-21 10:44:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_historial`
--

CREATE TABLE `admin_historial` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `accion` varchar(100) NOT NULL,
  `seccion` varchar(200) NOT NULL,
  `descripcion` text,
  `ip` varchar(45) DEFAULT NULL,
  `dispositivo` varchar(20) DEFAULT NULL,
  `hostname` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `admin_historial`
--

INSERT INTO `admin_historial` (`id`, `user_id`, `username`, `accion`, `seccion`, `descripcion`, `ip`, `dispositivo`, `hostname`, `created_at`) VALUES
(1, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', NULL, NULL, '2026-04-06 14:15:21'),
(2, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', NULL, NULL, '2026-04-06 14:25:04'),
(3, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', NULL, NULL, '2026-04-06 15:47:19'),
(4, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 16:59:37'),
(5, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 17:50:48'),
(6, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 18:26:19'),
(7, 1, 'admin', 'reporte', 'Reportes', 'PDF descargado. Periodo: 2026-04-01 al 2026-04-06', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 18:26:40'),
(8, 1, 'admin', 'reporte', 'Reportes', 'Excel descargado. Periodo: 2026-04-01 al 2026-04-06', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 18:34:26'),
(9, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 21:34:48'),
(10, 1, 'admin', 'create', 'Usuarios', '', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 21:50:35'),
(11, 1, 'admin', 'create', 'Usuarios', 'Nombre: tes', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 21:51:29'),
(12, 1, 'admin', 'toggle', 'Usuarios', '', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 21:51:38'),
(13, 1, 'admin', 'toggle', 'Usuarios', '', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 21:51:41'),
(14, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 21:57:57'),
(15, 1, 'admin', 'delete', 'Usuarios', '', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 21:58:03'),
(16, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 22:09:50'),
(17, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 22:45:29'),
(18, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 22:59:21'),
(19, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 23:21:36'),
(20, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 23:35:51'),
(21, 1, 'admin', 'add', 'Slider Principal', '', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 23:36:24'),
(22, 1, 'admin', 'delete', 'Slider Principal', '', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 23:37:02'),
(23, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-06 23:47:28'),
(24, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 00:10:21'),
(25, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 11:17:57'),
(26, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 11:24:45'),
(27, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 17:20:41'),
(28, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 17:34:01'),
(29, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 17:56:15'),
(30, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 22:44:23'),
(31, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 22:49:31'),
(32, 1, 'admin', 'add', 'Slider Principal', 'Se agregó un nuevo elemento en Slider Principal', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 22:51:28'),
(33, 1, 'admin', 'delete', 'Slider Principal', 'Se eliminó el registro (ID 4) en Slider Principal', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 22:51:37'),
(34, 1, 'admin', 'create', 'Transparencia Dinamica', 'Se creó un nuevo registro en Transparencia Dinamica: \"teststes\"', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 22:51:54'),
(35, 1, 'admin', 'delete', 'Transparencia Dinamica', 'Se eliminó el registro (ID 2) en Transparencia Dinamica', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 22:52:20'),
(36, 1, 'admin', 'create', 'Transparencia Dinamica', 'Se creó un nuevo registro en Transparencia Dinamica: \"eersasfsafs\"', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 22:52:43'),
(37, 1, 'admin', 'delete', 'Transparencia Dinamica', 'Se eliminó el registro (ID 3) en Transparencia Dinamica', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 22:52:55'),
(38, 1, 'admin', 'reporte', 'Reportes', 'Excel descargado. Periodo: 2026-04-07 al 2026-04-07', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 22:56:31'),
(39, 1, 'admin', 'reporte', 'Reportes', 'PDF descargado. Periodo: 2026-04-07 al 2026-04-07', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 22:57:22'),
(40, 1, 'admin', 'reporte', 'Reportes', 'Excel descargado. Periodo: 2026-04-07 al 2026-04-07', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 23:01:42'),
(41, 1, 'admin', 'reporte', 'Reportes', 'PDF descargado. Periodo: 2026-04-07 al 2026-04-07', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 23:01:59'),
(42, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 23:19:19'),
(43, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 23:33:50'),
(44, 1, 'admin', 'link_edit', 'Footer', 'Se editó el enlace \"Declaraciones\" en Footer', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-07 23:34:07'),
(45, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-08 11:06:06'),
(46, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-08 14:04:43'),
(47, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 1) en Tramites: \"Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes\"', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-08 14:05:24'),
(48, 1, 'admin', 'delete', 'Slider Principal', 'Se eliminó el registro (ID 1) en Slider Principal', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-08 14:06:51'),
(49, 1, 'admin', 'delete', 'Slider Principal', 'Se eliminó el registro (ID 2) en Slider Principal', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-08 14:06:55'),
(50, 1, 'admin', 'add', 'Slider Principal', 'Se agregó un nuevo elemento en Slider Principal', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-08 14:07:21'),
(51, 1, 'admin', 'add', 'Slider Principal', 'Se agregó un nuevo elemento en Slider Principal', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-08 14:07:48'),
(52, 1, 'admin', 'add', 'Slider Principal', 'Se agregó un nuevo elemento en Slider Principal', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-08 14:07:55'),
(53, 1, 'admin', 'add', 'Slider Principal', 'Se agregó un nuevo elemento en Slider Principal', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-08 14:08:06'),
(54, 1, 'admin', 'reorder', 'Slider Principal', 'Se reordenaron los elementos en Slider Principal', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-08 14:08:12'),
(55, 1, 'admin', 'reorder', 'Slider Principal', 'Se reordenaron los elementos en Slider Principal', '189.225.234.38', 'pc', 'Windows 10/11 / Edge 146', '2026-04-08 14:08:20'),
(56, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-09 16:02:29'),
(57, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-09 17:05:45'),
(58, 1, 'admin', 'save_config', 'Autismo', 'Se realizó la acción \"save_config\" en Autismo', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-09 17:06:09'),
(59, 1, 'admin', 'upload_image', 'Autismo', 'Se realizó la acción \"upload_image\" en Autismo', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-09 17:06:27'),
(60, 1, 'admin', 'upload_image', 'Autismo', 'Se realizó la acción \"upload_image\" en Autismo', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-09 17:06:43'),
(61, 1, 'admin', 'upload_image', 'Autismo', 'Se realizó la acción \"upload_image\" en Autismo', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-09 17:07:04'),
(62, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-09 17:21:11'),
(63, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:16:18'),
(64, 1, 'admin', 'delete', 'Programas', 'Se eliminó el registro (ID 1) en Programas', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:16:27'),
(65, 1, 'admin', 'delete', 'Programas', 'Se eliminó el registro (ID 2) en Programas', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:16:31'),
(66, 1, 'admin', 'delete', 'Programas', 'Se eliminó el registro (ID 3) en Programas', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:16:33'),
(67, 1, 'admin', 'delete', 'Programas', 'Se eliminó el registro (ID 4) en Programas', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:16:36'),
(68, 1, 'admin', 'delete', 'Programas', 'Se eliminó el registro (ID 7) en Programas', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:16:39'),
(69, 1, 'admin', 'delete', 'Programas', 'Se eliminó el registro (ID 8) en Programas', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:16:41'),
(70, 1, 'admin', 'delete', 'Programas', 'Se eliminó el registro (ID 9) en Programas', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:16:43'),
(71, 1, 'admin', 'delete', 'Programas', 'Se eliminó el registro (ID 10) en Programas', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:16:45'),
(72, 1, 'admin', 'delete', 'Programas', 'Se eliminó el registro (ID 11) en Programas', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:16:47'),
(73, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:18:12'),
(74, 1, 'admin', 'create', 'Programas', 'Se creó un nuevo registro en Programas: \"Procuraduría Municipal de Protección a Niñas, Niños y Adolescentes\"', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:19:31'),
(75, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 14) en Programa Editar: \"Procuraduría Municipal de Protección a Niñas, Niños y Adolescentes\"', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:20:35'),
(76, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 14)', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:22:23'),
(77, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 14)', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:23:49'),
(78, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 14)', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:25:14'),
(79, 1, 'admin', 'create', 'Programas', 'Se creó un nuevo registro en Programas: \"Dirección de Alimentación y Nutrición Familiar\"', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:25:53'),
(80, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 15) en Programa Editar: \"Dirección de Alimentación y Nutrición Familiar\"', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:26:39'),
(81, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:28:11'),
(82, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:29:31'),
(83, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:30:56'),
(84, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:32:27'),
(85, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:39:52'),
(86, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 14)', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:40:46'),
(87, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 14)', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:40:57'),
(88, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 14)', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:41:09'),
(89, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:41:26'),
(90, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:42:12'),
(91, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:42:23'),
(92, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:42:33'),
(93, 1, 'admin', 'create', 'Programas', 'Se creó un nuevo registro en Programas: \"Dirección de Servicios Júridicos Asistenciales e Igualdad de Género\"', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:43:12'),
(94, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 16) en Programa Editar: \"Dirección de Servicios Júridicos Asistenciales e Igualdad de Género\"', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:43:42'),
(95, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 16)', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:45:22'),
(96, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 16)', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:46:53'),
(97, 1, 'admin', 'create', 'Programas', 'Se creó un nuevo registro en Programas: \"DIF Cerca de ti\"', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:47:38'),
(98, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 17) en Programa Editar: \"DIF Cerca de ti\"', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:48:59'),
(99, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 17)', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:50:59'),
(100, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 17)', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:52:29'),
(101, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 17)', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:54:00'),
(102, 1, 'admin', 'create', 'Programas', 'Se creó un nuevo registro en Programas: \"Dirección de Atención a Personas con Discapacidad\"', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 01:57:02'),
(103, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 18) en Programa Editar: \"Dirección de Atención a Personas con Discapacidad\"', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 02:02:24'),
(104, 1, 'admin', 'create', 'Programas', 'Se creó un nuevo registro en Programas: \"Unidad Municipal de Autismo\"', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 02:02:52'),
(105, 1, 'admin', 'create', 'Programas', 'Se creó un nuevo registro en Programas: \"Dirección de Prevención y Bienestar Familiar\"', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 02:03:43'),
(106, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 20) en Programa Editar: \"Dirección de Prevención y Bienestar Familiar\"', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 02:04:21'),
(107, 1, 'admin', 'create', 'Programas', 'Se creó un nuevo registro en Programas: \"Coordinación de Atención a Pacientes con Cáncer\"', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 02:04:54'),
(108, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 21) en Programa Editar: \"Coordinación de Atención a Pacientes con Cáncer\"', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 02:05:28'),
(109, 1, 'admin', 'create', 'Programas', 'Se creó un nuevo registro en Programas: \"Dirección de Atención al Adulto Mayor\"', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 02:06:09'),
(110, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 22) en Programa Editar: \"Dirección de Atención al Adulto Mayor\"', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 02:07:07'),
(111, 1, 'admin', 'edit', 'Slider Principal', 'Se modificó el registro (ID 8) en Slider Principal', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 02:14:07'),
(112, 1, 'admin', 'galeria_add', 'Tramites', 'Se realizó la acción \"galeria_add\" en Tramites', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 02:19:55'),
(113, 1, 'admin', 'galeria_add', 'Tramites', 'Se realizó la acción \"galeria_add\" en Tramites', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 02:20:14'),
(114, 1, 'admin', 'galeria_add', 'Tramites', 'Se realizó la acción \"galeria_add\" en Tramites', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 02:20:26'),
(115, 1, 'admin', 'galeria_add', 'Tramites', 'Se realizó la acción \"galeria_add\" en Tramites', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 02:20:41'),
(116, 1, 'admin', 'galeria_delete', 'Tramites', 'Se realizó la acción \"galeria_delete\" en Tramites', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 02:22:25'),
(117, 1, 'admin', 'galeria_add', 'Tramites', 'Se realizó la acción \"galeria_add\" en Tramites', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 02:22:37'),
(118, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 1) en Tramites: \"Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes\"', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 02:22:44'),
(119, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 02:37:38'),
(120, 1, 'admin', 'galeria_reorder', 'Tramites', 'Se realizó la acción \"galeria_reorder\" en Tramites', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 02:37:51'),
(121, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 1) en Tramites: \"Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes\"', '189.136.4.193', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 02:37:52'),
(122, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 18:11:33'),
(123, 1, 'admin', 'link_edit', 'Footer', 'Se editó el enlace \"Servicios\" en Footer', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 18:12:22'),
(124, 1, 'admin', 'link_edit', 'Footer', 'Se editó el enlace \"Declaraciones\" en Footer', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 18:13:03'),
(125, 1, 'admin', 'link_delete', 'Footer', 'Se eliminó un enlace en Footer', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 18:13:15'),
(126, 1, 'admin', 'link_delete', 'Footer', 'Se eliminó un enlace en Footer', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 18:13:36'),
(127, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 18:37:02'),
(128, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 18:43:25'),
(129, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 18:48:45'),
(130, 1, 'admin', 'edit_titulo', 'Cuenta Publica', 'Se realizó la acción \"edit_titulo\" en Cuenta Publica: \"CUENTA PÚBLICA 2019\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 18:48:56'),
(131, 1, 'admin', 'edit_titulo', 'Cuenta Publica', 'Se realizó la acción \"edit_titulo\" en Cuenta Publica: \"CUENTA PÚBLICA 2018\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 18:49:09'),
(132, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 19:00:10'),
(133, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 14) en Programa Editar: \"Procuraduría Municipal de Protección a Niñas, Niños y Adolescentes\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 19:00:20'),
(134, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 15) en Programa Editar: \"Dirección de Alimentación y Nutrición Familiar\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 19:00:53'),
(135, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 16) en Programa Editar: \"Dirección de Servicios Júridicos Asistenciales e Igualdad de Género\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 19:01:06'),
(136, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 18) en Programa Editar: \"Dirección de Atención a Personas con Discapacidad\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 19:01:40'),
(137, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 19) en Programa Editar: \"Unidad Municipal de Autismo\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 19:01:51'),
(138, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 20) en Programa Editar: \"Dirección de Prevención y Bienestar Familiar\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 19:02:01'),
(139, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 22) en Programa Editar: \"Dirección de Atención al Adulto Mayor\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-10 19:02:22'),
(140, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-11 02:53:36'),
(141, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 10:58:45'),
(142, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 3) en Tramites: \"Dirección de Alimentación y Nutrición Familiar\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 10:59:56'),
(143, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 11:05:57'),
(144, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 4) en Tramites: \"Dirección de Atención a la Discapacidad\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 11:07:10'),
(145, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 4) en Tramites: \"Dirección de Atención a la Discapacidad\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 11:07:50'),
(146, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 2) en Tramites: \"Dirección de Atención a Adultos Mayores\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 11:12:03'),
(147, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 1) en Tramites: \"Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 11:15:04'),
(148, 1, 'admin', 'create', 'Tramites', 'Se creó un nuevo registro en Tramites: \"CARAVANA DE SERVICIOS DIF CERCA DE TI\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 11:15:58'),
(149, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 7) en Tramites: \"Caravana de Servicios DIF CERCA DE TI\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 11:17:09'),
(150, 1, 'admin', 'create', 'Tramites', 'Se creó un nuevo registro en Tramites: \"Coordinación de Atención a Pacientes con Cáncer\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 11:20:21'),
(151, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 8) en Tramites: \"Coordinación de Atención a Pacientes con Cáncer\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 11:22:47'),
(152, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 11:38:01'),
(153, 1, 'admin', 'edit', 'Slider Comunica', 'Se modificó el registro (ID 40) en Slider Comunica', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 11:38:23'),
(154, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 11:45:35'),
(155, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 12:00:15'),
(156, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 12:06:02'),
(157, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 12:11:32'),
(158, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 12:16:43'),
(159, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 12:26:03'),
(160, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 12:46:40'),
(161, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 8) en Tramites: \"Coordinación de Atención a Pacientes con Cáncer\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 12:48:44'),
(162, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 4) en Tramites: \"Dirección de Atención a la Discapacidad\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 12:51:26'),
(163, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 5) en Tramites: \"Dirección de Prevención y Bienestar Familiar\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 12:53:17'),
(164, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 6) en Tramites: \"Dirección de Servicios Jurídicos – Asistenciales e Igualdad de Género\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 12:53:56'),
(165, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 13:07:16'),
(166, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 13:44:16'),
(167, 1, 'admin', 'galeria_delete', 'Tramites', 'Se realizó la acción \"galeria_delete\" en Tramites', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 13:52:44'),
(168, 1, 'admin', 'galeria_delete', 'Tramites', 'Se realizó la acción \"galeria_delete\" en Tramites', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 13:52:46'),
(169, 1, 'admin', 'galeria_delete', 'Tramites', 'Se realizó la acción \"galeria_delete\" en Tramites', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 13:52:48'),
(170, 1, 'admin', 'galeria_delete', 'Tramites', 'Se realizó la acción \"galeria_delete\" en Tramites', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 13:52:50'),
(171, 1, 'admin', 'galeria_delete', 'Tramites', 'Se realizó la acción \"galeria_delete\" en Tramites', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 13:52:52'),
(172, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 1) en Tramites: \"Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes\"', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 13:53:12'),
(173, 1, 'admin', 'galeria_add', 'Tramites', 'Se realizó la acción \"galeria_add\" en Tramites', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 13:53:35'),
(174, 1, 'admin', 'galeria_add', 'Tramites', 'Se realizó la acción \"galeria_add\" en Tramites', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 13:54:01'),
(175, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 14:09:27'),
(176, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 14:53:06'),
(177, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 20:44:34'),
(178, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 22:02:31'),
(179, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-13 22:09:59'),
(180, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '201.175.224.167', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 10:12:26'),
(181, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '201.175.224.167', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 10:36:34'),
(182, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:06:55'),
(183, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 19) en Programa Editar: \"Unidad Municipal de Autismo\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:07:17'),
(184, 1, 'admin', 'galeria_add', 'Tramites', 'Se realizó la acción \"galeria_add\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:10:02'),
(185, 1, 'admin', 'galeria_delete', 'Tramites', 'Se realizó la acción \"galeria_delete\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:10:09'),
(186, 1, 'admin', 'galeria_delete', 'Tramites', 'Se realizó la acción \"galeria_delete\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:10:11'),
(187, 1, 'admin', 'galeria_delete', 'Tramites', 'Se realizó la acción \"galeria_delete\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:10:14'),
(188, 1, 'admin', 'galeria_delete', 'Tramites', 'Se realizó la acción \"galeria_delete\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:10:16'),
(189, 1, 'admin', 'galeria_delete', 'Tramites', 'Se realizó la acción \"galeria_delete\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:10:18'),
(190, 1, 'admin', 'galeria_add', 'Tramites', 'Se realizó la acción \"galeria_add\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:10:48'),
(191, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 1) en Tramites: \"Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:10:56'),
(192, 1, 'admin', 'galeria_add', 'Tramites', 'Se realizó la acción \"galeria_add\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:12:17'),
(193, 1, 'admin', 'galeria_reorder', 'Tramites', 'Se realizó la acción \"galeria_reorder\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:12:42'),
(194, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 2) en Tramites: \"Dirección de Atención a Adultos Mayores\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:12:43'),
(195, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 19) en Programa Editar: \"Unidad Municipal de Autismo\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:13:08'),
(196, 1, 'admin', 'galeria_add', 'Tramites', 'Se realizó la acción \"galeria_add\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:16:08'),
(197, 1, 'admin', 'galeria_reorder', 'Tramites', 'Se realizó la acción \"galeria_reorder\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:16:28'),
(198, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 3) en Tramites: \"Dirección de Alimentación y Nutrición Familiar\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:16:28'),
(199, 1, 'admin', 'galeria_add', 'Tramites', 'Se realizó la acción \"galeria_add\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:18:18'),
(200, 1, 'admin', 'galeria_reorder', 'Tramites', 'Se realizó la acción \"galeria_reorder\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:18:29'),
(201, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 4) en Tramites: \"Dirección de Atención a la Discapacidad\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:18:31'),
(202, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 19) en Programa Editar: \"Unidad Municipal de Autismo\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:19:41'),
(203, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 19) en Programa Editar: \"Unidad Municipal de Autismo\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:20:37'),
(204, 1, 'admin', 'galeria_add', 'Tramites', 'Se realizó la acción \"galeria_add\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:22:40'),
(205, 1, 'admin', 'galeria_reorder', 'Tramites', 'Se realizó la acción \"galeria_reorder\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:22:49'),
(206, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 5) en Tramites: \"Dirección de Prevención y Bienestar Familiar\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:22:51'),
(207, 1, 'admin', 'galeria_add', 'Tramites', 'Se realizó la acción \"galeria_add\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:25:54'),
(208, 1, 'admin', 'galeria_reorder', 'Tramites', 'Se realizó la acción \"galeria_reorder\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:26:04'),
(209, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 7) en Tramites: \"Caravana de Servicios DIF CERCA DE TI\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:26:06'),
(210, 1, 'admin', 'galeria_add', 'Tramites', 'Se realizó la acción \"galeria_add\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:27:40'),
(211, 1, 'admin', 'galeria_reorder', 'Tramites', 'Se realizó la acción \"galeria_reorder\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:27:48'),
(212, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 8) en Tramites: \"Coordinación de Atención a Pacientes con Cáncer\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:27:49'),
(213, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 11:37:15'),
(214, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 12:22:55'),
(215, 1, 'admin', 'edit', 'Slider Principal', 'Se modificó el registro (ID 8) en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 12:24:01'),
(216, 1, 'admin', 'edit', 'Slider Principal', 'Se modificó el registro (ID 8) en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 12:24:21'),
(217, 1, 'admin', 'create', 'Programas', 'Se creó un nuevo registro en Programas: \"tetsts\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 12:27:31'),
(218, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 23) en Programa Editar: \"tetsts\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 12:28:23'),
(219, 1, 'admin', 'delete', 'Programas', 'Se eliminó el registro (ID 23) en Programas', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 12:29:22'),
(220, 1, 'admin', 'galeria_reorder', 'Tramites', 'Se realizó la acción \"galeria_reorder\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 12:33:52'),
(221, 1, 'admin', 'galeria_reorder', 'Tramites', 'Se realizó la acción \"galeria_reorder\" en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 12:33:56'),
(222, 1, 'admin', 'add', 'Noticias', 'Se agregó un nuevo elemento en Noticias: \"2026-04-14\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 12:36:25'),
(223, 1, 'admin', 'delete', 'Noticias', 'Se eliminó el registro (ID 8) en Noticias', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 12:37:41'),
(224, 1, 'admin', 'reporte', 'Reportes', 'PDF descargado. Periodo: 2026-04-01 al 2026-04-14', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 12:47:14'),
(225, 1, 'admin', 'reporte', 'Reportes', 'Excel descargado. Periodo: 2026-04-01 al 2026-04-14', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 12:47:36'),
(226, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 146', '2026-04-14 12:55:41'),
(227, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-15 15:31:07'),
(228, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-15 15:39:12'),
(229, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 146', '2026-04-15 17:42:52'),
(230, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 147', '2026-04-16 11:58:54'),
(231, 1, 'admin', 'reorder', 'Slider Principal', 'Se reordenaron los elementos en Slider Principal', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 147', '2026-04-16 12:01:27'),
(232, 1, 'admin', 'reorder', 'Slider Principal', 'Se reordenaron los elementos en Slider Principal', '189.136.23.73', 'pc', 'Windows 10/11 / Edge 147', '2026-04-16 12:01:29'),
(233, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.31.31', 'pc', 'Windows 10/11 / Edge 147', '2026-04-16 15:01:25'),
(234, 1, 'admin', 'create', 'Usuarios', 'Se creó un nuevo registro en Usuarios: \"Sergio Huitron Gomez\"', '189.136.31.31', 'pc', 'Windows 10/11 / Edge 147', '2026-04-16 15:02:14'),
(235, 2, 'shuitron', 'login', 'Sistema', 'Inicio de sesion: shuitron', '189.136.31.31', 'celular', 'Android 10 (K) / Chrome 146', '2026-04-16 15:03:31'),
(236, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.31.31', 'pc', 'Windows 10/11 / Edge 147', '2026-04-16 15:47:39'),
(237, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.31.31', 'pc', 'Windows 10/11 / Edge 147', '2026-04-16 15:56:20'),
(238, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.31.31', 'pc', 'Windows 10/11 / Edge 147', '2026-04-16 17:01:04'),
(239, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.31.31', 'pc', 'Windows 10/11 / Edge 147', '2026-04-16 17:29:26'),
(240, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 10:51:04'),
(241, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 11:08:53'),
(242, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 11:51:04'),
(243, 1, 'admin', 'delete', 'Usuarios', 'Se eliminó el registro (Usuario ID 2) en Usuarios', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 11:51:32'),
(244, 1, 'admin', 'create', 'Usuarios', 'Se creó un nuevo registro en Usuarios: \"Sergio Huitron Gomez\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 11:53:16'),
(245, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 12:10:57'),
(246, 1, 'admin', 'upload_image_ajax', 'Tramites', 'Se realizó la acción \"upload_image_ajax\" en Tramites (ID 1)', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 12:14:15'),
(247, 1, 'admin', 'delete_image', 'Tramites', 'Se eliminó la imagen (ID 1) en Tramites', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 12:14:30'),
(248, 1, 'admin', 'reorder_images', 'Galeria', 'Se realizó la acción \"reorder_images\" en Galeria', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 12:18:23'),
(249, 1, 'admin', 'reorder_images', 'Galeria', 'Se realizó la acción \"reorder_images\" en Galeria', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 12:18:25'),
(250, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '172.225.14.173', 'celular', 'iPhone iOS 18.7 / Safari', '2026-04-17 12:26:32'),
(251, 1, 'admin', 'create', 'Usuarios', 'Se creó un nuevo registro en Usuarios: \"Patricia Victoria Ramírez Estrada\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 12:30:14'),
(252, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 12:39:00'),
(253, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '172.225.14.167', 'celular', 'iPhone iOS 18.7 / Safari', '2026-04-17 12:41:57'),
(254, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 12:47:21'),
(255, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 16)', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 12:48:25'),
(256, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 13:05:42'),
(257, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 16)', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 13:08:03'),
(258, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 16) en Programa Editar: \"Dirección de Servicios Júridicos Asistenciales e Igualdad de Género\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 13:09:00'),
(259, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 16)', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 13:12:56'),
(260, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 16)', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 13:13:41'),
(261, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 16)', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 13:15:57'),
(262, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 16)', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 13:18:50'),
(263, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 19) en Programa Editar: \"Unidad Municipal de Autismo\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 13:22:28'),
(264, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 19) en Programa Editar: \"Unidad Municipal de Autismo\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 13:22:36'),
(265, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '172.225.14.161', 'celular', 'iPhone iOS 18.7 / Safari', '2026-04-17 13:25:59'),
(266, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 13:31:57'),
(267, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '146.75.164.9', 'celular', 'iPhone iOS 18.7 / Safari', '2026-04-17 13:33:44'),
(268, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 14)', '172.225.14.164', 'celular', 'iPhone iOS 18.7 / Safari', '2026-04-17 13:36:46'),
(269, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 14)', '172.226.122.42', 'celular', 'iPhone iOS 18.7 / Safari', '2026-04-17 13:39:37'),
(270, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 14)', '172.226.122.46', 'celular', 'iPhone iOS 18.7 / Safari', '2026-04-17 13:41:14'),
(271, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 14)', '172.226.122.46', 'celular', 'iPhone iOS 18.7 / Safari', '2026-04-17 13:42:25'),
(272, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 14) en Programa Editar: \"Procuraduría Municipal de Protección a Niñas, Niños y Adolescentes\"', '172.226.122.46', 'celular', 'iPhone iOS 18.7 / Safari', '2026-04-17 13:43:29'),
(273, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 14)', '172.225.14.168', 'celular', 'iPhone iOS 18.7 / Safari', '2026-04-17 13:44:41'),
(274, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 14) en Programa Editar: \"Procuraduría Municipal de Protección a Niñas, Niños y Adolescentes\"', '172.226.122.45', 'celular', 'iPhone iOS 18.7 / Safari', '2026-04-17 13:45:44'),
(275, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 14)', '172.226.122.45', 'celular', 'iPhone iOS 18.7 / Safari', '2026-04-17 13:47:04'),
(276, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '172.225.103.41', 'celular', 'iPhone iOS 18.7 / Safari', '2026-04-17 13:51:10'),
(277, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-17 14:24:48'),
(278, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 14:32:07'),
(279, 1, 'admin', 'save_config', 'Slider Principal', 'Se realizó la acción \"save_config\" en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 14:36:19'),
(280, 1, 'admin', 'add', 'Slider Principal', 'Se agregó un nuevo elemento en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 14:37:20'),
(281, 1, 'admin', 'delete', 'Slider Principal', 'Se eliminó el registro (ID 9) en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 14:40:21'),
(282, 1, 'admin', 'add', 'Slider Principal', 'Se agregó un nuevo elemento en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 14:40:51'),
(283, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 14:47:29'),
(284, 1, 'admin', 'delete', 'Slider Principal', 'Se eliminó el registro (ID 10) en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 14:47:48'),
(285, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 15:07:23'),
(286, 1, 'admin', 'save_config', 'Slider Principal', 'Se realizó la acción \"save_config\" en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 15:07:33'),
(287, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 15:13:23'),
(288, 1, 'admin', 'save_config', 'Slider Principal', 'Se realizó la acción \"save_config\" en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 15:13:30'),
(289, 1, 'admin', 'save_config', 'Slider Principal', 'Se realizó la acción \"save_config\" en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 15:13:52'),
(290, 1, 'admin', 'save_config', 'Slider Principal', 'Se realizó la acción \"save_config\" en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 15:14:04');
INSERT INTO `admin_historial` (`id`, `user_id`, `username`, `accion`, `seccion`, `descripcion`, `ip`, `dispositivo`, `hostname`, `created_at`) VALUES
(291, 1, 'admin', 'save_config', 'Slider Comunica', 'Se realizó la acción \"save_config\" en Slider Comunica', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 15:14:54'),
(292, 4, 'comunicación', 'login', 'Sistema', 'Inicio de sesion: comunicación', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-17 15:16:30'),
(293, 4, 'comunicación', 'login', 'Sistema', 'Inicio de sesion: comunicación', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-17 15:20:19'),
(294, 4, 'comunicación', 'login', 'Sistema', 'Inicio de sesion: comunicación', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-17 15:23:37'),
(295, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 15:37:17'),
(296, 1, 'admin', 'edit', 'Slider Principal', 'Se modificó el registro (ID 8) en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-17 15:37:35'),
(297, 4, 'comunicación', 'login', 'Sistema', 'Inicio de sesion: comunicación', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-17 15:46:51'),
(298, 4, 'comunicación', 'add', 'Noticias', 'Se agregó un nuevo elemento en Noticias: \"2026-04-17\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-17 15:47:53'),
(299, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-17 15:49:15'),
(300, 4, 'comunicación', 'login', 'Sistema', 'Inicio de sesion: comunicación', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-17 15:53:31'),
(301, 4, 'comunicación', 'create_album', 'Galeria', 'Se creó el álbum \"taller tartas frutos rojos\" en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-17 15:57:24'),
(302, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-17 16:03:48'),
(303, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-17 16:04:39'),
(304, 4, 'comunicación', 'reorder_images', 'Galeria', 'Se realizó la acción \"reorder_images\" en Galeria', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-17 16:04:58'),
(305, 4, 'comunicación', 'delete_album', 'Galeria', 'Se eliminó el álbum (ID 1) en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-17 16:05:52'),
(306, 4, 'comunicación', 'login', 'Sistema', 'Inicio de sesion: comunicación', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-17 16:13:22'),
(307, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-20 11:28:02'),
(308, 1, 'admin', 'add', 'Slider Comunica', 'Se agregó un nuevo elemento en Slider Comunica: \"2026\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-20 11:29:41'),
(309, 1, 'admin', 'reorder', 'Slider Comunica', 'Se reordenaron los elementos en Slider Comunica', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-20 11:34:06'),
(310, 1, 'admin', 'reorder', 'Slider Comunica', 'Se reordenaron los elementos en Slider Comunica', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-20 11:34:09'),
(311, 1, 'admin', 'reorder', 'Slider Comunica', 'Se reordenaron los elementos en Slider Comunica', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-20 11:34:13'),
(312, 1, 'admin', 'reorder', 'Slider Comunica', 'Se reordenaron los elementos en Slider Comunica', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-20 11:34:17'),
(313, 1, 'admin', 'reorder', 'Slider Comunica', 'Se reordenaron los elementos en Slider Comunica', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-20 11:34:23'),
(314, 1, 'admin', 'reorder', 'Slider Comunica', 'Se reordenaron los elementos en Slider Comunica', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-20 11:34:25'),
(315, 1, 'admin', 'reorder', 'Slider Comunica', 'Se reordenaron los elementos en Slider Comunica', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-20 11:34:28'),
(316, 1, 'admin', 'reorder', 'Slider Comunica', 'Se reordenaron los elementos en Slider Comunica', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-20 11:34:30'),
(317, 1, 'admin', 'reorder', 'Slider Comunica', 'Se reordenaron los elementos en Slider Comunica', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-20 11:34:33'),
(318, 1, 'admin', 'save_config', 'Slider Comunica', 'Se realizó la acción \"save_config\" en Slider Comunica', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-20 11:35:02'),
(319, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-21 10:41:54'),
(320, 1, 'admin', 'create', 'Usuarios', 'Se creó un nuevo registro en Usuarios: \"Rene Alvarado Fonseca\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-21 10:44:33'),
(321, 5, 'contabilidad', 'login', 'Sistema', 'Inicio de sesion: contabilidad', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 10:46:17'),
(322, 5, 'contabilidad', 'create_block', 'Seac', 'Se realizó la acción \"create_block\" en Seac: \"2025\"', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 10:47:49'),
(323, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-21 10:53:15'),
(324, 1, 'admin', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-21 11:01:20'),
(325, 1, 'admin', 'delete_pdf', 'Seac', 'Se eliminó un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-21 11:01:31'),
(326, 5, 'contabilidad', 'login', 'Sistema', 'Inicio de sesion: contabilidad', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:01:45'),
(327, 5, 'contabilidad', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:02:22'),
(328, 5, 'contabilidad', 'create', 'Transparencia Dinamica', 'Se creó un nuevo registro en Transparencia Dinamica: \"Bloque prueba\"', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:10:30'),
(329, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-21 11:11:21'),
(330, 1, 'admin', 'create_block', 'Transparencia Seccion', 'Se realizó la acción \"create_block\" en Transparencia Seccion: \"2025\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-21 11:20:28'),
(331, 1, 'admin', 'delete_block', 'Transparencia Seccion', 'Se realizó la acción \"delete_block\" en Transparencia Seccion', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-21 11:20:35'),
(332, 5, 'contabilidad', 'create_block', 'Transparencia Seccion', 'Se realizó la acción \"create_block\" en Transparencia Seccion: \"2026\"', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:20:50'),
(333, 5, 'contabilidad', 'add_concepto', 'Transparencia Seccion', 'Se realizó la acción \"add_concepto\" en Transparencia Seccion: \"Cuenta\"', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:21:47'),
(334, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-21 11:26:23'),
(335, 5, 'contabilidad', 'upload_pdf', 'Transparencia Seccion', 'Se subió un PDF en Transparencia Seccion', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:27:03'),
(336, 1, 'admin', 'upload_pdf', 'Transparencia Seccion', 'Se subió un PDF en Transparencia Seccion', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-21 11:27:56'),
(337, 1, 'admin', 'upload_pdf', 'Transparencia Seccion', 'Se subió un PDF en Transparencia Seccion', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-21 11:30:56'),
(338, 1, 'admin', 'upload_concepto_pdf', 'Transparencia Seccion', 'Se realizó la acción \"upload_concepto_pdf\" en Transparencia Seccion', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-21 11:31:14'),
(339, 1, 'admin', 'delete_concepto', 'Transparencia Seccion', 'Se realizó la acción \"delete_concepto\" en Transparencia Seccion', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-21 11:31:28'),
(340, 1, 'admin', 'add_concepto', 'Transparencia Seccion', 'Se realizó la acción \"add_concepto\" en Transparencia Seccion: \"Cuenta\"', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-21 11:31:35'),
(341, 5, 'contabilidad', 'upload_concepto_pdf', 'Transparencia Seccion', 'Se realizó la acción \"upload_concepto_pdf\" en Transparencia Seccion', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:32:06'),
(342, 5, 'contabilidad', 'delete', 'Transparencia Dinamica', 'Se eliminó el registro (ID 1) en Transparencia Dinamica', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:32:39'),
(343, 5, 'contabilidad', 'login', 'Sistema', 'Inicio de sesion: contabilidad', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:45:15'),
(344, 5, 'contabilidad', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:45:38'),
(345, 5, 'contabilidad', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:46:21'),
(346, 5, 'contabilidad', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:46:39'),
(347, 5, 'contabilidad', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:46:54'),
(348, 5, 'contabilidad', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:47:07'),
(349, 5, 'contabilidad', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:47:24'),
(350, 5, 'contabilidad', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:47:40'),
(351, 5, 'contabilidad', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:47:58'),
(352, 5, 'contabilidad', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:48:16'),
(353, 5, 'contabilidad', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:48:32'),
(354, 5, 'contabilidad', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:48:51'),
(355, 5, 'contabilidad', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:49:05'),
(356, 5, 'contabilidad', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:49:19'),
(357, 5, 'contabilidad', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:49:34'),
(358, 5, 'contabilidad', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:49:47'),
(359, 5, 'contabilidad', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:50:09'),
(360, 5, 'contabilidad', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:50:41'),
(361, 5, 'contabilidad', 'upload_pdf', 'Seac', 'Se subió un PDF en Seac', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 149', '2026-04-21 11:52:32'),
(362, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 14:54:32'),
(363, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:20:51'),
(364, 1, 'admin', 'delete', 'Slider Principal', 'Se eliminó el registro (ID 8) en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:21:53'),
(365, 1, 'admin', 'delete', 'Slider Principal', 'Se eliminó el registro (ID 7) en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:21:56'),
(366, 1, 'admin', 'delete', 'Slider Principal', 'Se eliminó el registro (ID 5) en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:21:58'),
(367, 1, 'admin', 'delete', 'Slider Principal', 'Se eliminó el registro (ID 6) en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:22:01'),
(368, 1, 'admin', 'add', 'Slider Principal', 'Se agregó un nuevo elemento en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:22:58'),
(369, 1, 'admin', 'add', 'Slider Principal', 'Se agregó un nuevo elemento en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:23:53'),
(370, 1, 'admin', 'add', 'Slider Principal', 'Se agregó un nuevo elemento en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:24:55'),
(371, 1, 'admin', 'add', 'Slider Principal', 'Se agregó un nuevo elemento en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:25:54'),
(372, 1, 'admin', 'edit', 'Slider Principal', 'Se modificó el registro (ID 11) en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:26:08'),
(373, 1, 'admin', 'reorder', 'Slider Principal', 'Se reordenaron los elementos en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:26:12'),
(374, 1, 'admin', 'edit', 'Slider Principal', 'Se modificó el registro (ID 11) en Slider Principal', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:26:55'),
(375, 1, 'admin', 'edit', 'Direcciones', 'Se modificó el registro (ID 2) en Direcciones: \"Ofelia\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:32:06'),
(376, 1, 'admin', 'edit', 'Direcciones', 'Se modificó el registro (ID 3) en Direcciones: \"Oswaldo\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:32:38'),
(377, 1, 'admin', 'edit', 'Direcciones', 'Se modificó el registro (ID 4) en Direcciones: \"Abigail\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:34:44'),
(378, 1, 'admin', 'edit', 'Direcciones', 'Se modificó el registro (ID 5) en Direcciones: \"Zulma Guadalupe\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:36:03'),
(379, 1, 'admin', 'edit', 'Direcciones', 'Se modificó el registro (ID 6) en Direcciones: \"Mahelet Ruth\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:37:13'),
(380, 1, 'admin', 'edit', 'Direcciones', 'Se modificó el registro (ID 6) en Direcciones: \"Mahelet Ruth\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:37:43'),
(381, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:43:33'),
(382, 1, 'admin', 'edit', 'Direcciones', 'Se modificó el registro (ID 5) en Direcciones: \"Zulma Guadalupe\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:44:25'),
(383, 1, 'admin', 'edit', 'Direcciones', 'Se modificó el registro (ID 6) en Direcciones: \"Mahelet Ruth\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:47:49'),
(384, 1, 'admin', 'edit', 'Direcciones', 'Se modificó el registro (ID 7) en Direcciones: \"Johana Brick\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:50:18'),
(385, 1, 'admin', 'edit', 'Direcciones', 'Se modificó el registro (ID 8) en Direcciones: \"Alma Rosa\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-21 17:53:42'),
(386, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 09:46:39'),
(387, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 11:29:31'),
(388, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 14)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 11:32:09'),
(389, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 14)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 11:34:24'),
(390, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 14)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 11:37:04'),
(391, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 11:52:34'),
(392, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 14)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 11:57:30'),
(393, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 14)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 11:58:22'),
(394, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 14)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:00:01'),
(395, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 15) en Programa Editar: \"Dirección de Alimentación y Nutrición Familiar\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:01:49'),
(396, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:03:11'),
(397, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:04:05'),
(398, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:17:24'),
(399, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:18:39'),
(400, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:20:39'),
(401, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:21:47'),
(402, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:22:40'),
(403, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:27:26'),
(404, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:30:48'),
(405, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:31:39'),
(406, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:32:46'),
(407, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:37:18'),
(408, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:42:02'),
(409, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:42:54'),
(410, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:43:17'),
(411, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:51:16'),
(412, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:52:25'),
(413, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:52:44'),
(414, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:53:59'),
(415, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 12:57:51'),
(416, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:05:38'),
(417, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:08:40'),
(418, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 15) en Programa Editar: \"Dirección de Alimentación y Nutrición Familiar\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:09:30'),
(419, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 15)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:10:26'),
(420, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 16) en Programa Editar: \"Dirección de Servicios Jurídico-Asistenciales e Igualdad de Género\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:11:50'),
(421, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 16)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:13:13'),
(422, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 16)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:13:51'),
(423, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 16)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:14:26'),
(424, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 16)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:17:13'),
(425, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 16)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:20:24'),
(426, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 16)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:22:51'),
(427, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 17)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:25:15'),
(428, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 17)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:28:36'),
(429, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 17)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:32:11'),
(430, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:38:04'),
(431, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 17) en Programa Editar: \"DIF Cerca de Ti\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:38:21'),
(432, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:45:48'),
(433, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 17)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:48:38'),
(434, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 17)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:49:12'),
(435, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 17)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 13:49:41'),
(436, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 14:03:33'),
(437, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 17)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 14:06:41'),
(438, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 17)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 14:08:23'),
(439, 1, 'admin', 'delete_seccion', 'Programa Editar', 'Se realizó la acción \"delete_seccion\" en Programa Editar (ID 18)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 14:11:12'),
(440, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 18) en Programa Editar: \"Dirección de Atención a Personas con Discapacidad\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 14:11:15'),
(441, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 18)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 14:12:24'),
(442, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 18)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 14:13:57'),
(443, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 18)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 14:15:00'),
(444, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 18)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 14:16:14'),
(445, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 18)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 14:17:33'),
(446, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 14:25:41'),
(447, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 17:36:37'),
(448, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 16)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-22 17:36:48'),
(449, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 08:23:43'),
(450, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 09:46:45'),
(451, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 20)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 09:48:04'),
(452, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 20)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 09:48:46'),
(453, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 20)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 09:49:35'),
(454, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 22)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 09:51:33'),
(455, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 22)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 09:54:41'),
(456, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 22)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 09:55:36'),
(457, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 22)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 09:56:41'),
(458, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 22)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 09:57:54'),
(459, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 22)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 09:59:08'),
(460, 4, 'comunicación', 'login', 'Sistema', 'Inicio de sesion: comunicación', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 10:19:46'),
(461, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 10:30:41'),
(462, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 18) en Programa Editar: \"Dirección de Atención a Personas con Discapacidad\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 10:31:50'),
(463, 4, 'comunicación', 'login', 'Sistema', 'Inicio de sesion: comunicación', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 10:34:39'),
(464, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 18)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 10:35:25'),
(465, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 18)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 10:38:53'),
(466, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 18)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 10:39:38'),
(467, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 18)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 10:42:07'),
(468, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 18)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 10:44:40'),
(469, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 18)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 10:46:13'),
(470, 4, 'comunicación', 'login', 'Sistema', 'Inicio de sesion: comunicación', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 10:49:51'),
(471, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:01:10'),
(472, 1, 'admin', 'delete', 'Programas', 'Se eliminó el registro (ID 19) en Programas', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:03:00'),
(473, 4, 'comunicación', 'login', 'Sistema', 'Inicio de sesion: comunicación', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:05:06'),
(474, 4, 'comunicación', 'create_album', 'Galeria', 'Se creó el álbum \"Día de las niñas y los niños\" en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:05:56'),
(475, 4, 'comunicación', 'create_album', 'Galeria', 'Se creó el álbum \"Día de las niñas y los niños\" en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:06:42'),
(476, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 20)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:07:27'),
(477, 4, 'comunicación', 'create_album', 'Galeria', 'Se creó el álbum \"Día de las niñas y los niños\" en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:07:30'),
(478, 4, 'comunicación', 'create_album', 'Galeria', 'Se creó el álbum \"Día de las niñas y los niños\" en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:08:28'),
(479, 4, 'comunicación', 'create_album', 'Galeria', 'Se creó el álbum \"Día de las NIñas y los Niños\" en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:09:10'),
(480, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 20)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:10:58'),
(481, 4, 'comunicación', 'create_album', 'Galeria', 'Se creó el álbum \"Día de las niñas y los niños\" en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:11:37'),
(482, 4, 'comunicación', 'create_album', 'Galeria', 'Se creó el álbum \"Día de las niñas y los niños\" en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:12:54'),
(483, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 20)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:12:57'),
(484, 4, 'comunicación', 'create_album', 'Galeria', 'Se creó el álbum \"Día de las niños y los niños\" en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:13:48'),
(485, 4, 'comunicación', 'create_album', 'Galeria', 'Se creó el álbum \"día de las niñas y los niños\" en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:14:59'),
(486, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:16:16'),
(487, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 16)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:16:44'),
(488, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:19:04'),
(489, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 17)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:19:51'),
(490, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 14)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:22:46'),
(491, 4, 'comunicación', 'login', 'Sistema', 'Inicio de sesion: comunicación', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:38:06'),
(492, 4, 'comunicación', 'save_config', 'Noticias', 'Se realizó la acción \"save_config\" en Noticias', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:40:02'),
(493, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:40:29'),
(494, 4, 'comunicación', 'add', 'Noticias', 'Se agregó un nuevo elemento en Noticias: \"2026-04-27\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:41:47'),
(495, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 21)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:43:21'),
(496, 4, 'comunicación', 'delete_album', 'Galeria', 'Se eliminó el álbum (ID 4) en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:43:52'),
(497, 4, 'comunicación', 'delete_album', 'Galeria', 'Se eliminó el álbum (ID 11) en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:43:58'),
(498, 4, 'comunicación', 'delete_album', 'Galeria', 'Se eliminó el álbum (ID 9) en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:44:03'),
(499, 4, 'comunicación', 'delete_album', 'Galeria', 'Se eliminó el álbum (ID 10) en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:44:06'),
(500, 4, 'comunicación', 'delete_album', 'Galeria', 'Se eliminó el álbum (ID 8) en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:44:10'),
(501, 4, 'comunicación', 'delete_album', 'Galeria', 'Se eliminó el álbum (ID 7) en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:44:14'),
(502, 4, 'comunicación', 'delete_album', 'Galeria', 'Se eliminó el álbum (ID 6) en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:44:21'),
(503, 4, 'comunicación', 'delete_album', 'Galeria', 'Se eliminó el álbum (ID 5) en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:44:24'),
(504, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 21)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:44:25'),
(505, 4, 'comunicación', 'delete_album', 'Galeria', 'Se eliminó el álbum (ID 2) en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:44:34'),
(506, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 21)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:46:20'),
(507, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:46:21'),
(508, 4, 'comunicación', 'reorder_images', 'Galeria', 'Se realizó la acción \"reorder_images\" en Galeria', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:46:41'),
(509, 4, 'comunicación', 'reorder_images', 'Galeria', 'Se realizó la acción \"reorder_images\" en Galeria', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:46:46'),
(510, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 21)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:48:28'),
(511, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:54:45'),
(512, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:55:37'),
(513, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 21)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 11:56:11'),
(514, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:00:29'),
(515, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 22)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:01:31'),
(516, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 22)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:03:45'),
(517, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 22)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:06:01'),
(518, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 22)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:08:06'),
(519, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 22)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:11:00'),
(520, 4, 'comunicación', 'login', 'Sistema', 'Inicio de sesion: comunicación', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:11:40'),
(521, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-27 12:13:08'),
(522, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 22)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:13:34'),
(523, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 21)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:15:02'),
(524, 1, 'admin', 'edit', 'Programa Editar', 'Se modificó el registro (ID 22) en Programa Editar: \"Dirección de Atención al Adulto Mayor\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:17:09'),
(525, 4, 'comunicación', 'login', 'Sistema', 'Inicio de sesion: comunicación', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:18:23'),
(526, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-27 12:19:45'),
(527, 1, 'admin', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-27 12:20:23'),
(528, 1, 'admin', 'delete_image', 'Galeria', 'Se eliminó la imagen en Galeria', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-27 12:20:33'),
(529, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 22)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:21:52'),
(530, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:25:44'),
(531, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 22)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:26:12'),
(532, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 22)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:27:28'),
(533, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 22)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:27:51'),
(534, 1, 'admin', 'save_page', 'Programa Editar', 'Se realizó la acción \"save_page\" en Programa Editar (ID 22)', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:28:10'),
(535, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:37:06'),
(536, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 1) en Tramites: \"Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes\"', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:37:25'),
(537, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:55:39'),
(538, 1, 'admin', 'save_config', 'Autismo', 'Se realizó la acción \"save_config\" en Autismo', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:56:32'),
(539, 1, 'admin', 'save_config', 'Autismo', 'Se realizó la acción \"save_config\" en Autismo', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 12:57:20'),
(540, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-27 13:14:26'),
(541, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-27 13:24:45'),
(542, 1, 'admin', 'save_config', 'Autismo', 'Se realizó la acción \"save_config\" en Autismo', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-27 13:24:56'),
(543, 1, 'admin', 'save_config', 'Autismo', 'Se realizó la acción \"save_config\" en Autismo', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-27 13:26:16'),
(544, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-27 13:39:31'),
(545, 1, 'admin', 'save_paginas', 'Mantenimiento', 'Se realizó la acción \"save_paginas\" en Mantenimiento', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-27 13:41:23'),
(546, 1, 'admin', 'save_paginas', 'Mantenimiento', 'Se realizó la acción \"save_paginas\" en Mantenimiento', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-27 13:42:15'),
(547, 1, 'admin', 'save_paginas', 'Mantenimiento', 'Se realizó la acción \"save_paginas\" en Mantenimiento', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-27 13:45:44'),
(548, 1, 'admin', 'save_paginas', 'Mantenimiento', 'Se realizó la acción \"save_paginas\" en Mantenimiento', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-27 13:46:05'),
(549, 4, 'comunicación', 'login', 'Sistema', 'Inicio de sesion: comunicación', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 13:49:00'),
(550, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 13:49:58'),
(551, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 13:50:22'),
(552, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 13:50:51'),
(553, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 13:51:26'),
(554, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-27 13:53:55'),
(555, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-27 14:04:55'),
(556, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-27 14:12:37'),
(557, 1, 'admin', 'save_paginas', 'Mantenimiento', 'Se realizó la acción \"save_paginas\" en Mantenimiento', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-27 14:15:26'),
(558, 1, 'admin', 'save_paginas', 'Mantenimiento', 'Se realizó la acción \"save_paginas\" en Mantenimiento', '187.251.245.146', 'pc', 'Windows 10/11 / Edge 147', '2026-04-27 14:15:47'),
(559, 4, 'comunicación', 'login', 'Sistema', 'Inicio de sesion: comunicación', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 15:25:40'),
(560, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 15:30:36'),
(561, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 15:30:55'),
(562, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 15:31:16'),
(563, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 15:31:49'),
(564, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 15:32:12'),
(565, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 15:32:40'),
(566, 4, 'comunicación', 'login', 'Sistema', 'Inicio de sesion: comunicación', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 15:52:09'),
(567, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 15:53:23'),
(568, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 15:53:43'),
(569, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 15:54:17'),
(570, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 15:54:45'),
(571, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 15:55:08'),
(572, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 15:55:29'),
(573, 4, 'comunicación', 'add_image', 'Galeria', 'Se agregó una imagen al álbum en Galería', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-27 15:55:56');
INSERT INTO `admin_historial` (`id`, `user_id`, `username`, `accion`, `seccion`, `descripcion`, `ip`, `dispositivo`, `hostname`, `created_at`) VALUES
(574, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-28 17:07:16'),
(575, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '187.251.245.146', 'pc', 'Windows 10/11 / Chrome 147', '2026-04-28 18:13:24'),
(576, 5, 'contabilidad', 'login', 'Sistema', 'Inicio de sesion: contabilidad', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 150', '2026-04-29 13:06:40'),
(577, 5, 'contabilidad', 'edit_titulo', 'Pae', 'Se realizó la acción \"edit_titulo\" en Pae: \"1.- Programa Anual de Evaluaciones\"', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 150', '2026-04-29 13:09:59'),
(578, 5, 'contabilidad', 'add_anio', 'Pae', 'Se realizó la acción \"add_anio\" en Pae: \"2026\"', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 150', '2026-04-29 13:10:47'),
(579, 5, 'contabilidad', 'add_anio', 'Pae', 'Se realizó la acción \"add_anio\" en Pae: \"2026\"', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 150', '2026-04-29 13:11:16'),
(580, 5, 'contabilidad', 'add_anio', 'Pae', 'Se realizó la acción \"add_anio\" en Pae: \"2025\"', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 150', '2026-04-29 13:11:43'),
(581, 5, 'contabilidad', 'add_anio', 'Pae', 'Se realizó la acción \"add_anio\" en Pae: \"2025\"', '187.251.245.146', 'pc', 'Windows 10/11 / Firefox 150', '2026-04-29 13:12:19'),
(582, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '172.225.14.164', 'celular', 'iPhone iOS 18.7 / Safari', '2026-04-29 17:53:28'),
(583, 1, 'admin', 'edit', 'Tramites', 'Se modificó el registro (ID 4) en Tramites: \"Dirección de Atención a la Discapacidad\"', '104.28.50.17', 'celular', 'iPhone iOS 18.7 / Safari', '2026-04-29 17:54:06'),
(584, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.77.239', 'pc', 'Windows 10/11 / Edge 147', '2026-04-29 23:22:07'),
(585, 1, 'admin', 'login', 'Sistema', 'Inicio de sesion: admin', '189.136.77.239', 'pc', 'Windows 10/11 / Edge 147', '2026-04-29 23:49:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_permisos`
--

CREATE TABLE `admin_permisos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `seccion_file` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_permisos`
--

INSERT INTO `admin_permisos` (`id`, `user_id`, `seccion_file`) VALUES
(4, 3, 'institucion'),
(3, 3, 'programas'),
(5, 3, 'transparencia'),
(7, 4, 'galeria'),
(6, 4, 'noticias'),
(13, 5, 'conac'),
(9, 5, 'cuenta_publica'),
(14, 5, 'financiero'),
(12, 5, 'matrices_indicadores'),
(11, 5, 'pae'),
(10, 5, 'presupuesto_anual'),
(8, 5, 'seac'),
(15, 5, 'transparencia_dinamica');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `autismo_config`
--

CREATE TABLE `autismo_config` (
  `id` int(11) NOT NULL,
  `logo_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `texto_derecha` text COLLATE utf8mb4_unicode_ci,
  `texto_centro` text COLLATE utf8mb4_unicode_ci,
  `texto_inferior` text COLLATE utf8mb4_unicode_ci,
  `imagen_centro_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen_inferior_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `en_mantenimiento` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `autismo_config`
--

INSERT INTO `autismo_config` (`id`, `logo_path`, `texto_derecha`, `texto_centro`, `texto_inferior`, `imagen_centro_path`, `imagen_inferior_path`, `updated_at`, `en_mantenimiento`) VALUES
(1, 'uploads/images/UMA_SMA.png', '<p>El Centro de rehabilitaci&oacute;n e Integraci&oacute;n Social (CRIS) se a consolidado por la calidad en la atenci&oacute;n que brinda y el servicio humanista de su personal: m&eacute;dico especialista, m&eacute;dico general, enfermeras y terapeutas. El Centro de rehabilitaci&oacute;n e Integraci&oacute;n Social (CRIS) se a consolidado por la calidad en la atenci&oacute;n que brinda y el servicio humanista de su personal: m&eacute;dico especialista, m&eacute;dico general, enfermeras y terapeutas.</p>', '<p>El Centro de rehabilitaci&oacute;n e Integraci&oacute;n Social (CRIS) se a consolidado por la calidad en la atenci&oacute;n que brinda y el servicio humanista de su personal: m&eacute;dico especialista, m&eacute;dico general, enfermeras y terapeutas. El Centro de rehabilitaci&oacute;n e Integraci&oacute;n Social (CRIS) se a consolidado por la calidad en la atenci&oacute;n que brinda y el servicio humanista de su personal: m&eacute;dico especialista, m&eacute;dico general, enfermeras y terapeutas.</p>', '<p>El Centro de rehabilitaci&oacute;n e Integraci&oacute;n Social (CRIS) se a consolidado por la calidad en la atenci&oacute;n que brinda y el servicio humanista de su personal: m&eacute;dico especialista, m&eacute;dico general, enfermeras y terapeutas. El Centro de rehabilitaci&oacute;n e Integraci&oacute;n Social (CRIS) se a consolidado por la calidad en la atenci&oacute;n que brinda y el servicio humanista de su personal: m&eacute;dico especialista, m&eacute;dico general, enfermeras y terapeutas.</p>', 'uploads/images/top-view-kid-playing-with-colorful-game.jpg', 'uploads/images/top-view-kid-playing-with-colorful-game_1.jpg', '2026-04-27 13:26:16', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `avisos_privacidad`
--

CREATE TABLE `avisos_privacidad` (
  `id` int(11) NOT NULL,
  `titulo` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pdf_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `avisos_privacidad`
--

INSERT INTO `avisos_privacidad` (`id`, `titulo`, `pdf_path`, `orden`, `activo`) VALUES
(1, 'Aviso de Privacidad Integral para Servicios de Atención a Adultos Mayores', 'uploads/pdfs/a56b7e37e64dec817bad9ac3f66a29a2.pdf', 1, 1),
(2, 'Aviso de Privacidad Simplificado para Servicios de Atención a Adultos Mayores', 'uploads/pdfs/dd7a62a0a92f7418020850d5f23d6495.pdf', 2, 1),
(3, 'Aviso de Privacidad Integral para Servicios de Atención a la Discapacidad', 'uploads/pdfs/e84288ddc0a13031ddfeedf197f8d83c.pdf', 3, 1),
(4, 'Aviso de Privacidad Simplificado para Servicios de Atención a la Discapacidad', 'uploads/pdfs/a45beac12340ac69c48509edd0e744d7.pdf', 4, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `avisos_privacidad_config`
--

CREATE TABLE `avisos_privacidad_config` (
  `id` int(11) NOT NULL,
  `texto_aviso` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `avisos_privacidad_config`
--

INSERT INTO `avisos_privacidad_config` (`id`, `texto_aviso`, `updated_at`) VALUES
(1, 'Si consideras que el Sistema Municipal DIF de San Mateo Atenco no cumple con sus obligaciones de transparencia, puedes denunciarlo ante el INFOEM, a través del sitio: https://www.transparenciaestadodemexico.org.mx/denciu/denuncia/insert.page o bien, mediante escrito presentado físicamente ante la Unidad de Transparencia de este Organismo o por correo electrónico dirigido a la Dirección General Jurídica y de Verificación del propio órgano garante.', '2026-03-23 11:01:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conac_bloques`
--

CREATE TABLE `conac_bloques` (
  `id` int(11) NOT NULL,
  `anio` year(4) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `conac_bloques`
--

INSERT INTO `conac_bloques` (`id`, `anio`, `orden`) VALUES
(2, '2022', 1),
(3, '2023', 2),
(4, '2024', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conac_conceptos`
--

CREATE TABLE `conac_conceptos` (
  `id` int(11) NOT NULL,
  `bloque_id` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `nombre` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `conac_conceptos`
--

INSERT INTO `conac_conceptos` (`id`, `bloque_id`, `numero`, `nombre`, `orden`) VALUES
(2, 2, 1, '1.-Presupuesto de Egresos para el Ejercicio Fiscal 2022', 1),
(3, 2, 2, '2.- Iniciativa de Ley de Ingresos para el Ejercicio Fiscal 2022', 2),
(4, 2, 3, '3.- Calendario de Presupuesto de Egresos del Ejercicio Fiscal 2022', 3),
(5, 2, 4, '4.- Calendario de Ingresos del Ejercicio Fiscal 2022', 4),
(6, 2, 5, '5.- Montos pagados por ayudas y subsidios', 5),
(7, 2, 6, '6.- Relación de cuentas bancarias productivas específicas', 6),
(8, 2, 7, '7.- Ley de Ingresos', 7),
(9, 2, 8, '8.- Formato del ejercicio y destino de gasto federalizado y reintegros', 8),
(10, 2, 9, '9.- Relación de bienes que componen su patrimonio', 9),
(11, 2, 10, '10.- Formato de programas con recursos concurrente por orden de gobierno', 10),
(12, 3, 1, '1.-Adicional egresos', 1),
(13, 3, 2, '2.- Adicional ingresos', 2),
(14, 3, 3, '3.- Ayudas y Subsidios', 3),
(15, 3, 4, '4.- Calendario Egresos', 4),
(16, 3, 5, '5.- Calendario Ingresos', 5),
(17, 3, 6, '6.- Ctas Bancarias', 6),
(18, 3, 7, '7.- Dif. ciudadania', 7),
(19, 3, 8, '8.- Ejercicio y destino', 8),
(20, 3, 9, '9.- Formatos de Bienes', 9),
(21, 3, 10, '10.- Recurso recurrente', 10),
(22, 4, 1, '1.-Adicional de Ingresos', 1),
(23, 4, 2, '2.- Adicional egresos', 2),
(24, 4, 3, '3.- Ayudas y Subsidios', 3),
(25, 4, 4, '4.- Bienes Patrimoniales', 4),
(26, 4, 5, '5.- Calendario egresos', 5),
(27, 4, 6, '6.- Calendario Ingresos', 6),
(28, 4, 7, '7.- Cuentas Bancarias', 7),
(29, 4, 8, '8.- Difusion ciudadania', 8),
(30, 4, 9, '9.- Ejercicio y destino', 9),
(31, 4, 10, '10.- Recurso Recurrente', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conac_pdfs`
--

CREATE TABLE `conac_pdfs` (
  `id` int(11) NOT NULL,
  `bloque_id` int(11) NOT NULL,
  `concepto_id` int(11) NOT NULL,
  `trimestre` tinyint(4) NOT NULL COMMENT '1-4',
  `pdf_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `conac_pdfs`
--

INSERT INTO `conac_pdfs` (`id`, `bloque_id`, `concepto_id`, `trimestre`, `pdf_path`) VALUES
(2, 2, 2, 1, 'uploads/pdfs/20220524_182047__Adicional Egresos.pdf'),
(3, 2, 3, 1, 'uploads/pdfs/20220524_182114__Adicional Ingresos.pdf'),
(4, 2, 4, 1, 'uploads/pdfs/20220524_182135__calendario Egresos.pdf'),
(5, 2, 5, 1, 'uploads/pdfs/20220524_182200__calendario Ingresos.pdf'),
(6, 2, 6, 4, 'uploads/pdfs/20230130_162508__ayuda y subsidios.pdf'),
(7, 2, 6, 3, 'uploads/pdfs/20221025_154821__Ayudas y Subsidio.pdf'),
(8, 2, 6, 2, 'uploads/pdfs/20220808_100316__apoyos.pdf'),
(9, 2, 6, 1, 'uploads/pdfs/20220524_182223__coopera y ayudas.pdf'),
(10, 2, 7, 1, 'uploads/pdfs/20220524_182245__CTAS BANCARIAS.pdf'),
(11, 2, 8, 1, 'uploads/pdfs/20220524_182321__difu ciudadania.pdf'),
(12, 2, 9, 4, 'uploads/pdfs/20230130_162705__ejercicio y destino.pdf'),
(13, 2, 9, 3, 'uploads/pdfs/20221025_154915__ejercicio y destino.pdf'),
(14, 2, 9, 2, 'uploads/pdfs/20220808_100717__ejercicio.pdf'),
(15, 2, 9, 1, 'uploads/pdfs/20220524_182338__ejercicio y destino.pdf'),
(16, 2, 10, 4, 'uploads/pdfs/20230130_162911__Formato de Bienes.pdf'),
(17, 2, 10, 3, 'uploads/pdfs/20221025_154852__Bienes.pdf'),
(18, 2, 10, 2, 'uploads/pdfs/20220808_100600__Bienes dif.pdf'),
(19, 2, 10, 1, 'uploads/pdfs/20220524_182352__Formatos de Bienes- Org 2.pdf'),
(20, 2, 11, 4, 'uploads/pdfs/20230130_163059__Recurso recurrente.pdf'),
(21, 2, 11, 3, 'uploads/pdfs/20221025_154939__recurso recurrente.pdf'),
(22, 2, 11, 2, 'uploads/pdfs/20220808_100809__Recurso recurrente.pdf'),
(23, 2, 11, 1, 'uploads/pdfs/20220525_143732__Recurso recurrente.pdf'),
(24, 3, 12, 1, 'uploads/pdfs/Adicional egresos.pdf'),
(25, 3, 13, 1, 'uploads/pdfs/Adicional ingresos.pdf'),
(26, 3, 14, 4, 'uploads/pdfs/Ayudas y Subsidios.pdf'),
(27, 3, 14, 3, 'uploads/pdfs/20231024_155508__Formatos ayudas y subsidios.pdf'),
(28, 3, 14, 2, 'uploads/pdfs/20230731_162932__cooperaciones y ayuda.pdf'),
(29, 3, 14, 1, 'uploads/pdfs/Ayudas y Subsidios _1.pdf'),
(30, 3, 15, 1, 'uploads/pdfs/Calendario Egresos.pdf'),
(31, 3, 16, 1, 'uploads/pdfs/Calendario Ingresos.pdf'),
(32, 3, 17, 1, 'uploads/pdfs/Ctas Bancarias.pdf'),
(33, 3, 18, 1, 'uploads/pdfs/Dif. ciudadania.pdf'),
(34, 3, 19, 4, 'uploads/pdfs/Ejercicio y Destino.pdf'),
(35, 3, 19, 3, 'uploads/pdfs/20231024_155606__Formatos ejericio y destinno gsto fed.pdf'),
(36, 3, 19, 2, 'uploads/pdfs/20230731_162917__ejercicio y destino.pdf'),
(37, 3, 19, 1, 'uploads/pdfs/Ejercicio y destino _1.pdf'),
(38, 3, 20, 4, 'uploads/pdfs/Formatos de Bienes Patrimoniales.pdf'),
(39, 3, 20, 3, 'uploads/pdfs/20231024_155621__Formatos recurso recurrente.pdf'),
(40, 3, 20, 2, 'uploads/pdfs/20230731_162948__Recurso Recurrente.pdf'),
(41, 3, 20, 1, 'uploads/pdfs/Recurso recurrente.pdf'),
(42, 3, 21, 4, 'uploads/pdfs/Recurso Recurrente _1.pdf'),
(43, 3, 21, 3, 'uploads/pdfs/20231024_155621__Formatos recurso recurrente _1.pdf'),
(44, 3, 21, 2, 'uploads/pdfs/20230731_162948__Recurso Recurrente _1.pdf'),
(45, 3, 21, 1, 'uploads/pdfs/Recurso recurrente _2.pdf'),
(46, 4, 22, 2, 'uploads/pdfs/20240730_183233__Ayudas y Subsidios.pdf'),
(47, 4, 22, 1, 'uploads/pdfs/Adicional de Ingresos.pdf'),
(48, 4, 23, 1, 'uploads/pdfs/Adicional egresos_1.pdf'),
(49, 4, 24, 4, 'uploads/pdfs/20250508_163315__Ayudas y subsidio.pdf'),
(50, 4, 24, 3, 'uploads/pdfs/20241031_075056__Ayudas y subsidios.pdf'),
(51, 4, 24, 1, 'uploads/pdfs/Ayudas y Subsidios_1.pdf'),
(52, 4, 25, 3, 'uploads/pdfs/20241031_075109__Bienes Patrimoniales.pdf'),
(53, 4, 25, 2, 'uploads/pdfs/20240730_183139__Formatos de Bienes Patrimoniales.pdf'),
(54, 4, 25, 1, 'uploads/pdfs/Bienes Patrimoniales.pdf'),
(55, 4, 26, 1, 'uploads/pdfs/Calendario egresos.pdf'),
(56, 4, 27, 1, 'uploads/pdfs/Calendario Ingresos_1.pdf'),
(57, 4, 28, 1, 'uploads/pdfs/Cuentas Bancarias.pdf'),
(58, 4, 29, 1, 'uploads/pdfs/Difusion ciudadania.pdf'),
(59, 4, 30, 3, 'uploads/pdfs/20241031_075126__Ejercicio y destino.pdf'),
(60, 4, 30, 2, 'uploads/pdfs/20240730_183204__Ejercicio y destino.pdf'),
(61, 4, 30, 1, 'uploads/pdfs/Ejercicio y destino.pdf'),
(62, 4, 31, 3, 'uploads/pdfs/20241031_075142__Recursos Recurrentes.pdf'),
(63, 4, 31, 2, 'uploads/pdfs/20240730_183110__Recurso Recurrente.pdf'),
(64, 4, 31, 1, 'uploads/pdfs/Recurso Recurrente.pdf');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacto_config`
--

CREATE TABLE `contacto_config` (
  `id` int(11) NOT NULL,
  `titulo1` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT 'SERVICIOS MÉDICOS',
  `titulo2` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT 'CLASES Y TALLERES',
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `telefono` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `horario` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correo` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `contacto_config`
--

INSERT INTO `contacto_config` (`id`, `titulo1`, `titulo2`, `direccion`, `telefono`, `horario`, `correo`, `updated_at`) VALUES
(1, 'SERVICIOS MÉDICOS', 'CLASES Y TALLERES', 'Mariano Matamoros 310, Barrio de la Concepción CP 52105,\nSan Mateo Atenco, Méx.', '722 970 77 86', 'Horario de Lunes a Viernes\n8:00 am a 3:30 pm', 'adultomayor@difsanmateoatenco.gob.mx', '2026-04-10 01:15:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cp_bloques`
--

CREATE TABLE `cp_bloques` (
  `id` int(11) NOT NULL,
  `anio` year(4) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cp_bloques`
--

INSERT INTO `cp_bloques` (`id`, `anio`, `orden`) VALUES
(2, '2018', 1),
(3, '2019', 2),
(4, '2020', 3),
(5, '2021', 4),
(6, '2022', 5),
(7, '2023', 6),
(8, '2024', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cp_conceptos`
--

CREATE TABLE `cp_conceptos` (
  `id` int(11) NOT NULL,
  `titulo_id` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `nombre` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pdf_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cp_conceptos`
--

INSERT INTO `cp_conceptos` (`id`, `titulo_id`, `numero`, `nombre`, `pdf_path`, `orden`) VALUES
(3, 3, 1, '1.- Cuenta Pública', 'uploads/pdfs/cuenta publica 2018.pdf', 1),
(4, 4, 1, '1.- Información Complementaria', 'uploads/pdfs/INFORMACION COMPLEMENTARIA.pdf', 1),
(5, 4, 2, '2.- Información Contable', 'uploads/pdfs/INFORMACION CONTABLE.pdf', 2),
(6, 4, 3, '3.- Información Presupestaria', 'uploads/pdfs/INFORMACION PRESUPUESTARIA.pdf', 3),
(7, 4, 4, '4.- Información Programática', 'uploads/pdfs/INFORMACION PROGRAMATICA.pdf', 4),
(8, 5, 1, '1.- Anexo al Estado Situación Financiera', 'uploads/pdfs/ANEXO AL ESTADO SITUACION FINANC.pdf', 1),
(9, 5, 2, '2.- Construcciones en proceso', 'uploads/pdfs/CONSTRUCCIONES EN PROCESO.pdf', 2),
(10, 5, 3, '3.- Cuotas aportaciones ISSEMYM', 'uploads/pdfs/CUOTAS APORTACIONES ISSEMYM.pdf', 3),
(11, 5, 4, '4.- Clasificación de Servicios Personales por Categoria', 'uploads/pdfs/ESTADO ANALIT CLAS SER PERS.pdf', 4),
(12, 5, 5, '5.- Estado Analítico de la Deuda y Otros Pasivos', 'uploads/pdfs/ESTADO ANALITICO DE LA DEUDA.pdf', 5),
(13, 5, 6, '6.- Estado Analítico del Activo', 'uploads/pdfs/ESTADO ANALITICO DEL ACTIVO.pdf', 6),
(14, 5, 7, '7.- Estado de Actividades Comparativo', 'uploads/pdfs/ESTADO DE ACTIVIDADES COMPARATIVO.pdf', 7),
(15, 5, 8, '8.- Estado de Cambios en la Situación Financiera', 'uploads/pdfs/ESTADO DE CAMBIOS EN LA SIT FINAN.pdf', 8),
(16, 5, 9, '9.- Estado de Flujos de Efectivo', 'uploads/pdfs/ESTADO DE FLUJOS DE EFECTIVO.pdf', 9),
(17, 5, 10, '10.- Estado de Variación en la hacienda Pública', 'uploads/pdfs/ESTADO DE VARIACION DE LA HDA PUB.pdf', 10),
(18, 5, 11, '11.- Estado de Situación Financiera Comparativo', 'uploads/pdfs/ESTADO SITUACION FINANCIERA COMPARATIVO.pdf', 11),
(19, 5, 12, '12.- Informe de Remuneraciones', 'uploads/pdfs/INFORME DE REMUNERACIONES.pdf', 12),
(20, 5, 13, '13.- Notas a los Estados Financieros', 'uploads/pdfs/NOTAS FINANCIERAS.pdf', 13),
(21, 5, 14, '14.-Reporte de plazas ocupadas por Remuneraciones al Trabajo Personal', 'uploads/pdfs/REPORTE DE PLAZAS OCUPADAS.pdf', 14),
(22, 5, 15, '15.- Retenciones del Impuesto Sobre la Renta por Salarios, Honorarios y Arrendamiento', 'uploads/pdfs/RETENCIONES DE ISR.pdf', 15),
(23, 5, 16, '16.- Servicios Personales', 'uploads/pdfs/SERVICIOS PERSONALES.pdf', 16),
(24, 6, 1, '1.- Conciliación Físico-Contable de Bienes Inmuebles', 'uploads/pdfs/CONCILIACION BIENES INMUEBLES.pdf', 1),
(25, 6, 2, '2.- Conciliación Físico-Contable de Bienes Muebles', 'uploads/pdfs/CONCILIACION BIENES MUEBLES.pdf', 2),
(26, 6, 3, '3.- Depreciación', 'uploads/pdfs/DEPRECIACION.pdf', 3),
(27, 6, 4, '4.- Estado Analítico del Ejercicio del Presupuesto de Egresos Clasificación Administrativa', 'uploads/pdfs/ESTADO ANALITICO DE EGRESOS CLAS ADMINISTRATIVA.pdf', 4),
(28, 6, 5, '5.- Estado Analítico del Ejercicio del Presupuesto de Egresos Clasificación Económica', 'uploads/pdfs/ESTADO ANALITICO DE EGRESOS CLAS ECONOMICA.pdf', 5),
(29, 6, 6, '6.- Estado Analítico del Ejercicio del Presupuesto de Egresos Clasificación Funcional', 'uploads/pdfs/ESTADO ANALITICO DE EGRESOS CLAS FUNCIONAL.pdf', 6),
(30, 6, 7, '7.- Estado Analítico del Ejercicio del Presupuesto de Egresos Clasificación por Objeto del Gasto', 'uploads/pdfs/ESTADO ANALITICO DE EGRESOS.pdf', 7),
(31, 6, 8, '8.- Estado Analítico de Ingresos', 'uploads/pdfs/ESTADO ANALITICO DE INGRESOS.pdf', 8),
(32, 6, 9, '9.- Hoja de trabajo para la Conciliación Físico-Contable de Bienes Inmuebles', 'uploads/pdfs/HOJA DE TRABAJO BIENE INMUEBLES.pdf', 9),
(33, 6, 10, '10.- Hoja de trabajo para la Conciliación Físico-Contable de Bienes Muebles', 'uploads/pdfs/HOJA DE TRABAJO BIENES MUEBLES.pdf', 10),
(34, 6, 11, '11.- Inventario de Bienes Inmuebles', 'uploads/pdfs/INVENTARIO DE BIENES INMUEBLES.pdf', 11),
(35, 6, 12, '12.- Inventario de Bienes Muebles', 'uploads/pdfs/INVENTARIO DE BIENES MUEBLES.pdf', 12),
(36, 6, 13, '13.- Reporte de Altas y bajas de Bienes Inmuebles', 'uploads/pdfs/REPORTE ALTAS Y BAJAS INMUEBLES.pdf', 13),
(37, 6, 14, '14.-Reporte de Altas y bajas de Bienes Muebles', 'uploads/pdfs/REPORTE ALTAS Y BAJAS MUEBLES.pdf', 14),
(38, 7, 1, '1.- Conciliación Físico-Contable de Bienes Inmuebles', 'uploads/pdfs/CONCILIACION BIENES INMUEBLES_0.pdf', 1),
(39, 7, 2, '2.- Conciliación Físico-Contable de Bienes Muebles', 'uploads/pdfs/CONCILIACION BIENES MUEBLES_0.pdf', 2),
(40, 7, 3, '2.- Conciliación Físico-Contable de Bienes Muebles', 'uploads/pdfs/DEPRECIACION_0.pdf', 3),
(41, 7, 4, '4.- Estado Analítico del Ejercicio del Presupuesto de Egresos Clasificación Administrativa', 'uploads/pdfs/ESTADO ANALITICO DE EGRESOS CLAS ADMINISTRATIVA_0.pdf', 4),
(42, 7, 5, '5.- Estado Analítico del Ejercicio del Presupuesto de Egresos Clasificación Económica', 'uploads/pdfs/ESTADO ANALITICO DE EGRESOS CLAS ECONOMICA_0.pdf', 5),
(43, 7, 6, '6.- Estado Analítico del Ejercicio del Presupuesto de Egresos Clasificación Funcional', 'uploads/pdfs/ESTADO ANALITICO DE EGRESOS CLAS FUNCIONAL_0.pdf', 6),
(44, 7, 7, '7.- Estado Analítico del Ejercicio del Presupuesto de Egresos Clasificación por Objeto del Gasto', 'uploads/pdfs/ESTADO ANALITICO DE EGRESOS_0.pdf', 7),
(45, 7, 8, '8.- Estado Analítico de Ingresos', 'uploads/pdfs/ESTADO ANALITICO DE INGRESOS_0.pdf', 8),
(46, 7, 9, '9.- Hoja de trabajo para la Conciliación Físico-Contable de Bienes Inmuebles', 'uploads/pdfs/HOJA DE TRABAJO BIENE INMUEBLES_0.pdf', 9),
(47, 7, 10, '10.- Hoja de trabajo para la Conciliación Físico-Contable de Bienes Muebles', 'uploads/pdfs/HOJA DE TRABAJO BIENES MUEBLES_0.pdf', 10),
(48, 7, 11, '11.- Inventario de Bienes Inmuebles', 'uploads/pdfs/INVENTARIO DE BIENES INMUEBLES_0.pdf', 11),
(49, 7, 12, '12.- Inventario de Bienes Muebles', 'uploads/pdfs/INVENTARIO DE BIENES MUEBLES_0.pdf', 12),
(50, 7, 13, '13.- Reporte de Altas y bajas de Bienes Inmuebles', 'uploads/pdfs/REPORTE ALTAS Y BAJAS INMUEBLES_0.pdf', 13),
(51, 7, 14, '14.-Reporte de Altas y bajas de Bienes Muebles', 'uploads/pdfs/REPORTE ALTAS Y BAJAS MUEBLES_0.pdf', 14),
(52, 8, 1, 'MODULO 1', 'uploads/pdfs/20220525_113003__MODULO 1.pdf', 1),
(53, 8, 2, 'MODULO 2', 'uploads/pdfs/20220525_113130__MODULO 2.pdf', 2),
(54, 8, 3, 'MODULO 3 AVAN TRIMESTRAL', 'uploads/pdfs/20220525_113159__MODULO 3 AVAN TRIMESTRAL.pdf', 3),
(55, 8, 4, 'MODULO 3', 'uploads/pdfs/20220525_113233__MODULO 3.pdf', 4),
(56, 8, 5, 'MODULO 4', 'uploads/pdfs/20220525_113257__MODULO 4.pdf', 5),
(57, 9, 1, '1.- BPLDF2022', 'uploads/pdfs/BPLDF2022.pdf', 1),
(58, 9, 2, '2.-EAEPEDLDF2022', 'uploads/pdfs/EAEPEDLDF2022.pdf', 2),
(59, 9, 3, '3.- EAEPESLDF2022', 'uploads/pdfs/EAEPESLDF2022.pdf', 3),
(60, 9, 4, '4.- EAIDLDF2022', 'uploads/pdfs/EAIDLDF2022.pdf', 4),
(61, 9, 5, '5.- ESFCDLDF2022', 'uploads/pdfs/ESFCDLDF2022.pdf', 5),
(62, 10, 1, '1.- EAA2022', 'uploads/pdfs/EAA2022.pdf', 1),
(63, 10, 2, '2.-EAC2022', 'uploads/pdfs/EAC2022.pdf', 2),
(64, 10, 3, '3.- EADYOP2022', 'uploads/pdfs/EADYOP2022.pdf', 3),
(65, 10, 4, '4.- ECSF2022', 'uploads/pdfs/ECSF2022.pdf', 4),
(66, 10, 5, '5.- EFE2022', 'uploads/pdfs/EFE2022.pdf', 5),
(67, 10, 6, '6.- ESFC2022', 'uploads/pdfs/ESFC2022.pdf', 6),
(68, 10, 7, '7.- EVHP2022', 'uploads/pdfs/EVHP2022.pdf', 7),
(69, 10, 8, '8.- NESF2022', 'uploads/pdfs/NESF2022.pdf', 8),
(70, 11, 1, '1.- CFCBMeINM2022', 'uploads/pdfs/CFCBMeINM2022.pdf', 1),
(71, 11, 2, '2.- EAEPECA2022', 'uploads/pdfs/EAEPECA2022.pdf', 2),
(72, 11, 3, '3.- EAEPECF2022', 'uploads/pdfs/EAEPECF2022.pdf', 3),
(73, 11, 4, '4.- EAEPECTG2022', 'uploads/pdfs/EAEPECTG2022.pdf', 4),
(74, 11, 5, '4.- EAEPECTG2022', 'uploads/pdfs/EAEPEOG2022.pdf', 5),
(75, 11, 6, '6.- EAI2022', 'uploads/pdfs/EAI2022.pdf', 6),
(76, 11, 7, '7.- HTCFCBMeINM2022', 'uploads/pdfs/HTCFCBMeINM2022.pdf', 7),
(77, 11, 8, '8.- IBM2022', 'uploads/pdfs/IBM2022.pdf', 8),
(78, 11, 9, '9.- IBMINM2022', 'uploads/pdfs/IBMINM2022.pdf', 9),
(79, 11, 10, '10.- RAyBBINM2022', 'uploads/pdfs/RAyBBINM2022.pdf', 10),
(80, 11, 11, '11.- RAyBBM2022', 'uploads/pdfs/RAyBBM2022.pdf', 11),
(81, 12, 1, '1.- AGAMPA2022', NULL, 1),
(82, 12, 2, '2.- AM3041042022', NULL, 2),
(83, 12, 3, '3.- DRAPPR 032022ANF', NULL, 3),
(84, 12, 4, '4.- DRAPPR 032022DPYBF', NULL, 4),
(85, 12, 5, '5.- DRAPPR 082022 PSIC', NULL, 5),
(86, 12, 6, '6.- DRAPPR 102022', NULL, 6),
(87, 12, 7, '7.- DRAPPR OFI PSIC 082022', NULL, 7),
(88, 13, 1, '1.- AESF2022', 'uploads/pdfs/AESF2022.pdf', 1),
(89, 13, 2, '2.-AOGREA2022', 'uploads/pdfs/AOGREA2022.pdf', 2),
(90, 13, 3, '3.- CSEE2022', 'uploads/pdfs/CSEE2022.pdf', 3),
(91, 13, 4, '4.-ENDNET2022', 'uploads/pdfs/ENDNET2022.pdf', 4),
(92, 13, 5, '5.- FCyLP2022', 'uploads/pdfs/FCyLP2022.pdf', 5),
(93, 13, 6, '6.- FIFASAHEM2022', 'uploads/pdfs/FIFASAHEM2022.pdf', 6),
(94, 13, 7, '7.- FOyA2022', 'uploads/pdfs/FOyA2022.pdf', 7),
(95, 13, 8, '8.- IACP2022', 'uploads/pdfs/IACP2022.pdf', 8),
(96, 13, 9, '9.- INFPROGPILEJE2022', 'uploads/pdfs/INFPROGPILEJE2022.pdf', 9),
(97, 13, 10, '10.- INT DEUDA2022', 'uploads/pdfs/INT DEUDA2022.pdf', 10),
(98, 13, 11, '11.-IOPE2022', 'uploads/pdfs/IOPE2022.pdf', 11),
(99, 13, 12, '12.- ISERTP2022', 'uploads/pdfs/ISERTP2022.pdf', 12),
(100, 13, 13, '13.- ISR2022', NULL, 13),
(101, 13, 14, '14.-ISSEMYM2022', 'uploads/pdfs/ISSEMYM2022.pdf', 14),
(102, 13, 15, '15.- RDEE2022', 'uploads/pdfs/RDEE2022.pdf', 15),
(103, 13, 16, '16.- RPO2022', 'uploads/pdfs/RPO2022.pdf', 16),
(104, 14, 1, '1. Estados Financieros', 'uploads/pdfs/1. Estados Financieros.pdf', 1),
(105, 14, 2, '2. Disciplina Financiera', 'uploads/pdfs/2. Disciplina Financiera.pdf', 2),
(106, 14, 3, '3. Informacion Presupuestal', 'uploads/pdfs/3. Informacion Presupuestal.pdf', 3),
(108, 14, 4, '4. Informacion Prográmatica', NULL, 4),
(109, 14, 5, '5. Informacion Patrimonial', 'uploads/pdfs/5. Informacion Patrimonial.pdf', 5),
(110, 14, 6, '6. Informacion Complementaria', 'uploads/pdfs/6. Informacion Complementaria.pdf', 6),
(111, 14, 7, 'GCP30412023', 'uploads/pdfs/20241031_075230__GCP30412023.pdf', 7),
(112, 14, 8, 'Ind post fisc', 'uploads/pdfs/20241031_075244__Ind post fisc.pdf', 8),
(113, 14, 9, 'Inf pasi cont', 'uploads/pdfs/20241031_075301__Inf pasi cont.pdf', 9),
(114, 14, 10, 'rel esq burs', 'uploads/pdfs/20241031_075321__rel esq burs.pdf', 10),
(115, 14, 11, 'Relación ctas bancarias rec federal', 'uploads/pdfs/20241031_075336__Relación ctas bancarias rec federal.pdf', 11),
(116, 15, 1, '1.- BPLDF2024', 'uploads/pdfs/20251026_212844__BPLDF30412024.pdf', 1),
(117, 15, 2, '2.-EAEPEDLDF2024', 'uploads/pdfs/20251026_212916__EAEPEDLDF30412024.pdf', 2),
(118, 15, 3, '3.- EAEPESLDF2024', 'uploads/pdfs/20251026_213017__EAEPESPLDF30412024.pdf', 3),
(119, 15, 4, '4.- EAIDLDF2024', 'uploads/pdfs/20251026_213056__EAIDLDF30412024.pdf', 4),
(120, 15, 5, '5.- ESFCDLDF2024', 'uploads/pdfs/20251026_213122__ESFCDLDF30412024.pdf', 5),
(121, 16, 1, '1.- EAA2024', 'uploads/pdfs/20251026_214129__EAA30412024.pdf', 1),
(122, 16, 2, '2.-EAC2024', 'uploads/pdfs/20251026_214203__EAC30412024.pdf', 2),
(123, 16, 3, '3.- EADYOP2024', 'uploads/pdfs/20251026_214227__EADYOP30412024.pdf', 3),
(124, 16, 4, '4.- ECSF2024', 'uploads/pdfs/20251026_214250__ECSF30412024.pdf', 4),
(125, 16, 5, '5.- EFE2024', 'uploads/pdfs/20251026_214335__EFE30412024.pdf', 5),
(126, 16, 6, '6.- ESFC2024', 'uploads/pdfs/20251026_214404__ESFC30412024.pdf', 6),
(127, 16, 7, '7.- EVHP2024', 'uploads/pdfs/20251026_214420__EVHP30412024.pdf', 7),
(128, 16, 8, '8.- NESF2024', 'uploads/pdfs/20251026_214438__NESF30412024.pdf', 8),
(129, 17, 1, '1.- IBINM2024', 'uploads/pdfs/20251026_221511__IBINM30412024.pdf', 1),
(130, 17, 2, '2.- IBM2024', 'uploads/pdfs/20251026_221526__IBM30412024.pdf', 2),
(131, 17, 3, '3.- RAyBBINM2024', 'uploads/pdfs/20251026_221622__RAyBBINM30412024.pdf', 3),
(132, 17, 4, '4.- RAyBBM2024', 'uploads/pdfs/20251026_221646__RAyBBM30412024.pdf', 4),
(133, 18, 1, '1.- EAEPECA2024', 'uploads/pdfs/20251026_222241__EAEPECA30412024.pdf', 1),
(134, 18, 2, '2.- EAEPECF2024', 'uploads/pdfs/20251026_222300__EAEPECF30412024.pdf', 2),
(135, 18, 3, '3.- EAEPECTG2024', 'uploads/pdfs/20251026_222328__EAEPECTG30412024.pdf', 3),
(136, 18, 4, '4.- EAEPEOG2024', 'uploads/pdfs/20251026_222355__EAEPEOG30412024.pdf', 4),
(137, 18, 5, '5.- EAI2024', 'uploads/pdfs/20251026_222420__EAI30412024.pdf', 5),
(138, 19, 1, '1.- AGAMPA2024', 'uploads/pdfs/20251026_230938__AGAMPA30412024.pdf', 1),
(139, 19, 2, '2.- GCP2024', 'uploads/pdfs/20251026_230953__GCP30412024.pdf', 2),
(140, 19, 3, '3.- INFPROGPILEJE2024', 'uploads/pdfs/20251026_231017__INFPROGPILEJE30412024.pdf', 3),
(141, 20, 1, '1.- AESF2024', 'uploads/pdfs/20251026_232633__AESF30412024.pdf', 1),
(142, 20, 2, '2.- AOGREA2024', 'uploads/pdfs/20251026_232714__AOGREA30412024.pdf', 2),
(143, 20, 3, '3.- BCD2024', 'uploads/pdfs/20251026_232804__BCD30412024.pdf', 3),
(144, 20, 4, '4.- DVG2024', 'uploads/pdfs/20251026_233016__DVG30412024.pdf', 4),
(145, 20, 5, '5.- OCOF2024', 'uploads/pdfs/20251026_233037__OCOF30412024.pdf', 5),
(146, 20, 6, '6.- RDC2024', 'uploads/pdfs/20251026_233100__RDC30412024.pdf', 6),
(147, 20, 7, '7.- RDEE2024', 'uploads/pdfs/20251026_233143__RDEE30412024.pdf', 7),
(148, 20, 8, '8.- RPO2024', 'uploads/pdfs/20251026_233216__RPO30412024.pdf', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cp_titulos`
--

CREATE TABLE `cp_titulos` (
  `id` int(11) NOT NULL,
  `bloque_id` int(11) NOT NULL,
  `nombre` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cp_titulos`
--

INSERT INTO `cp_titulos` (`id`, `bloque_id`, `nombre`, `orden`) VALUES
(3, 2, 'CUENTA PÚBLICA 2018', 1),
(4, 3, 'CUENTA PÚBLICA 2019', 1),
(5, 4, 'CUENTA PÚBLICA 2020 MODULO 1', 1),
(6, 4, 'CUENTA PUBLIA 2020 MODULO 2', 2),
(7, 4, 'CUENTA PUBLIA 2020 MODULO 3', 3),
(8, 5, 'CUENTA PÚBLICA 2021', 1),
(9, 6, 'CUENTA PÚBLICA 2022 MODULO 1 DISCIPLINA FINANCIERA', 1),
(10, 6, 'CUENTA PÚBLICA 2022 MODULO 1 ESTADOS FINANCIEROS', 2),
(11, 6, 'CUENTA PÚBLICA 2022 MODULO 2 INFORMACIÓN PATRIMONIAL', 3),
(12, 6, 'CUENTA PÚBLICA 2022 MODULO 3 INFORMACIÓN PROGRAMÁTICA', 4),
(13, 6, 'CUENTA PÚBLICA 2022 MODULO 4', 5),
(14, 7, 'CUENTA PÚBLICA 2023', 1),
(15, 8, 'CUENTA PÚBLICA 2024 MODULO 1 DISCIPLINA FINANCIERA', 1),
(16, 8, 'CUENTA PÚBLICA 2024   MODULO 1 ESTADOS FINANCIEROS', 2),
(17, 8, 'CUENTA PÚBLICA 2024 MODULO 2 INFORMACIÓN PATRIMONIAL', 3),
(18, 8, 'CUENTA PÚBLICA 2024 MODULO 2 INFORMACIÓN PRESUPUESTAL', 4),
(19, 8, 'CUENTA PÚBLICA 2024 MODULO 3 INFORMACIÓN PROGRAMÁTICA', 5),
(20, 8, 'CUENTA PÚBLICA 2024 MODULO 4 INFORMACIÓN COMPLEMENTARIA', 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones`
--

CREATE TABLE `direcciones` (
  `id` int(11) NOT NULL,
  `departamento` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `cargo` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `imagen_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `direcciones`
--

INSERT INTO `direcciones` (`id`, `departamento`, `nombre`, `apellidos`, `cargo`, `imagen_path`, `orden`) VALUES
(1, 'DIRECCIÓN GENERAL', 'María del Rayo', 'González Villar', 'DIRECCIÓN GENERAL', 'uploads/images/e4aba6631002bd68d5f107f058e744dd.jpg', 1),
(2, 'DIRECCIÓN DE ADMINISTRACIÓN Y TESORERÍA', 'Ofelia', 'Mora Reséndiz', 'DIRECCIÓN DE ADMINISTRACIÓN Y TESORERÍA', 'uploads/images/73a1d980f55edb172ee464e324359e75.jpg', 2),
(3, 'DIRECCIÓN DE ALIMENTACIÓN Y NUTRICIÓN FAMILIAR', 'Oswaldo', 'Peña Becerril', 'DIRECCIÓN DE ALIMENTACIÓN Y NUTRICIÓN FAMILIAR', 'uploads/images/bfcec5c3b6cdf65a560ef779fcf60016.jpg', 3),
(4, 'DIRECCIÓN DE SERVICIOS JURÍDICO-ASISTENCIALES E IGUALDAD DE GÉNERO', 'Abigail', 'Legorreta de Paz', 'DIRECCIÓN DE SERVICIOS JURÍDICO-ASISTENCIALES E IGUALDAD DE GÉNERO', 'uploads/images/cd7d8cc7a1e9a71ae7d83d7329ad5ef5.jpg', 4),
(5, 'DIRECCIÓN DE ATENCIÓN A LA DISCAPACIDAD C.R.I.S.', 'Zulma Guadalupe', 'Chacon Villaseñor', 'DIRECCIÓN DE ATENCIÓN A LA DISCAPACIDAD', 'uploads/images/41051ba3824b1b2795ea3886ac259daf.jpg', 5),
(6, 'PROCURADURÍA MUNICIPAL DE PROTECCIÓN DE NIÑAS, NIÑOS Y ADOLESCENTES', 'Mahelet Ruth', 'Gutiérrez Flores', 'PROCURADURÍA MUNICIPAL DE PROTECCIÓN DE NIÑAS, NIÑOS Y ADOLESCENTES', 'uploads/images/30cafa1186da1ff7bf5d964ed3f87bbd.jpg', 6),
(7, 'DIRECCIÓN DE PREVENCIÓN Y BIENESTAR FAMILIAR', 'Johana Brick', 'González Escutia', 'DIRECCIÓN DE PREVENCIÓN Y BIENESTAR FAMILIAR', 'uploads/images/34ac554bef8e5880da895802bd285812.jpg', 7),
(8, 'DIRECCIÓN DE ATENCIÓN AL ADULTO MAYOR', 'Alma Rosa', 'González González', 'DIRECCIÓN DE ATENCIÓN AL ADULTO MAYOR', 'uploads/images/753354060220c2a46dd02530854941fc.jpg', 8),
(9, 'TITULAR DE LA CONTRALORÍA INTERNA', 'Miguel Ángel', 'Téllez Pérez', 'TITULAR DE LA CONTRALORÍA INTERNA', 'uploads/images/af5642829c6be5e2c53155040bc50e94.jpg', 9),
(10, 'TITULAR DE LA UIPPET', 'René', 'Alvarado Fonseca', 'TITULAR DE LA UIPPET', 'uploads/images/22997cb7cd83a5beb8bf94d08ee0a9fd.jpg', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fin_bloques`
--

CREATE TABLE `fin_bloques` (
  `id` int(11) NOT NULL,
  `anio` year(4) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `fin_bloques`
--

INSERT INTO `fin_bloques` (`id`, `anio`, `orden`) VALUES
(2, '2020', 1),
(3, '2021', 2),
(4, '2022', 3),
(5, '2023', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fin_conceptos`
--

CREATE TABLE `fin_conceptos` (
  `id` int(11) NOT NULL,
  `bloque_id` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `nombre` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pdf_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `fin_conceptos`
--

INSERT INTO `fin_conceptos` (`id`, `bloque_id`, `numero`, `nombre`, `pdf_path`, `orden`) VALUES
(2, 2, 1, 'Notas a los Estados Financieros', 'uploads/pdfs/20220525_113819__2. Notas a los Estados Financieros.pdf', 1),
(3, 2, 2, 'Estado Analitico de Ingresos Deatallado - LDF', 'uploads/pdfs/20220525_113847__2.2.4. EAID_2.pdf', 2),
(4, 2, 3, 'Estado Análitico del ejercicio del Presupuesto de Egresos Detallado - LDF', 'uploads/pdfs/20220525_113956__2.2.5. ClasificacionObjGasto_2.pdf', 3),
(5, 2, 4, 'Estado Análitico del Ejercicio del Presupuesto de Egresos Detallado - LDF Clasificación Administrativa', 'uploads/pdfs/20220525_114017__2.2.6. ClasificacionAdministrativa_1.pdf', 4),
(6, 2, 5, 'Estado Análitico del Ejercicio del Presupuesto de Egresos Detallado - LDF (Clasificación Funcional)', 'uploads/pdfs/20220525_114029__2.2.7. EdoAnaEjerPreEgreDet_1.pdf', 5),
(7, 2, 6, 'ESTADO DE VARIACIÓN EN LA HACIENDA PÚBLICA/PATRIMONIO', 'uploads/pdfs/20220525_114040__4. HaciendaPublicaPatrimonio_4.pdf', 6),
(8, 2, 7, 'ESTADO ANALITICO DEL ACTIVO', 'uploads/pdfs/20220525_114052__5. Edo Ana Act.pdf', 7),
(9, 2, 8, 'ESTADO DE CAMBIOS EN LA SITUACION FINANCIERA', 'uploads/pdfs/20220525_114104__7. EstadoCambiosSitFin_4.pdf', 8),
(10, 2, 9, 'ESTADO ANALÍTICO DEL EJERCICIO DEL PRESUPUESTO DE EGRESOS CLASIFICACIÓN ECONÓMICA (por Tipo de Gasto)', 'uploads/pdfs/20220525_114117__clasecoldf20.pdf', 9),
(11, 2, 10, 'GASTO POR CATEGORÍA PROGRAMÁTICA', 'uploads/pdfs/20220525_114135__gastocatpro20.pdf', 10),
(12, 2, 11, 'ESTADO DE SITUACIÓN FINANCIERA 2020', 'uploads/pdfs/20230202_103242__1. Edo_SitFin _1.pdf', 11),
(13, 3, 1, 'ESTADO ANALÍTICO DEL EJERCICIO DEL PRESUPUESTO DE EGRESOS CLASIFICACIÓN ECONÓMICA (por Tipo de Gasto)', 'uploads/pdfs/20220525_122513__clasecoldf21.pdf', 1),
(14, 3, 2, 'Estado Análitico del Ejercicio del Presupuesto de Egresos Detallado - LDF Clasificación Administrativa', 'uploads/pdfs/20220525_122525__EAEPECALDF3041202104.pdf', 2),
(15, 3, 3, 'Estado Análitico del Ejercicio del Presupuesto de Egresos Detallado - LDF (Clasificación Funcional)', 'uploads/pdfs/20220525_122536__EAEPECFLDF3041202104.pdf', 3),
(16, 3, 4, 'Estado Análitico del ejercicio del Presupuesto de Egresos Detallado - LDF Clasificación por Objeto del Gasto (Capítulo y concepto)', 'uploads/pdfs/20220525_122548__EAEPECOGLDF3041202104.pdf', 4),
(17, 3, 5, 'ESTADO ANALÍTICO DE INGRESOS', 'uploads/pdfs/20220525_122600__EAI3041202112.pdf', 5),
(18, 3, 6, 'ESTADO ANALITICO DEL ACTIVO', 'uploads/pdfs/20220525_122612__EDO ANA ACT 122021.pdf', 6),
(19, 3, 7, 'ESTADO DE CAMBIOS EN LA SITUACION FINANCIERA', 'uploads/pdfs/20220525_122630__EDO CAM SIT FIN 122021.pdf', 7),
(20, 3, 8, 'ESTADO DE VARIACIÓN EN LA HACIENDA PÚBLICA', 'uploads/pdfs/20220525_122630__EDO CAM SIT FIN 122021 _1.pdf', 8),
(21, 3, 9, 'GASTO POR CATEGORÍA PROGRAMÁTICA', 'uploads/pdfs/20220525_122657__gastocatpro21.pdf', 9),
(22, 3, 10, 'NOTAS DE DESGLOSE', 'uploads/pdfs/20220525_122716__NOTAS EDO FIN 122021.pdf', 10),
(23, 3, 11, 'ESTADO DE CAMBIOS EN LA SITUACION FINANCIERA 2021', 'uploads/pdfs/20230202_103551__EDO CAM SIT FIN 122021 _1.pdf', 11),
(24, 4, 1, 'clasificacioneconomicaportipodegasto', 'uploads/pdfs/30.clasificacioneconomicaportipodegasto.pdf', 1),
(25, 4, 2, 'clasificacionFuncional(FinalidadyFuncion)', 'uploads/pdfs/31.clasificacionFuncional_FinalidadyFuncion.pdf', 2),
(26, 4, 3, 'gastoporcategoriaprogramatica (2)', 'uploads/pdfs/32.gastoporcategoriaprogramatica _2.pdf', 3),
(27, 4, 4, 'clasAdmin', 'uploads/pdfs/33.clasAdmin.pdf', 4),
(28, 4, 5, 'EAI3041202212', 'uploads/pdfs/EAI3041202212.pdf', 5),
(29, 4, 6, 'EDO ACT 122022', 'uploads/pdfs/EDO ACT 122022.pdf', 6),
(30, 4, 7, 'EDO ANA ACT 122022', 'uploads/pdfs/EDO ANA ACT 122022.pdf', 7),
(31, 4, 8, 'EDO ANA DEU Y OTR PAS 122022', 'uploads/pdfs/EDO ANA DEU Y OTR PAS 122022.pdf', 8),
(32, 4, 9, 'EDO CAM SIT FIN 122022', 'uploads/pdfs/EDO CAM SIT FIN 122022.pdf', 9),
(33, 4, 10, 'EDO FLU EFE 122022', 'uploads/pdfs/EDO FLU EFE 122022.pdf', 10),
(34, 4, 11, 'EDO VAR HAC PUB 122022', 'uploads/pdfs/EDO VAR HAC PUB 122022.pdf', 11),
(35, 4, 12, 'EDOSIT122022', 'uploads/pdfs/EDOSIT122022.pdf', 12),
(36, 4, 13, 'NOTAS EDO FIN 122022', 'uploads/pdfs/NOTAS EDO FIN 122022.pdf', 13),
(37, 5, 1, 'clasificacioneconomicaportipodegasto', 'uploads/pdfs/1.clasificacioneconomicaportipodegasto.pdf', 1),
(38, 5, 2, 'clasificacionFuncional(FinalidadyFuncion)', 'uploads/pdfs/2.clasificacionFuncional_FinalidadyFuncion.pdf', 2),
(39, 5, 3, 'gastoporcategoriaprogramatica', 'uploads/pdfs/3.gastoporcategoriaprogramatica.pdf', 3),
(40, 5, 4, 'clasAdmin', 'uploads/pdfs/4.clasAdmin.pdf', 4),
(41, 5, 5, 'ESF3041202312', 'uploads/pdfs/5.ESF3041202312.pdf', 5),
(42, 5, 6, 'NEF3041202312', 'uploads/pdfs/6. NEF3041202312.pdf', 6),
(43, 5, 7, 'EA3041202312', 'uploads/pdfs/7.EA3041202312.pdf', 7),
(44, 5, 8, 'EAA3041202312', 'uploads/pdfs/8.EAA3041202312.pdf', 8),
(45, 5, 9, 'EADOP3041202312', 'uploads/pdfs/9.EADOP3041202312.pdf', 9),
(46, 5, 10, 'ECSF3041202312', 'uploads/pdfs/10.ECSF3041202312.pdf', 10),
(47, 5, 11, 'EVHP3041202312', 'uploads/pdfs/11.EVHP3041202312.pdf', 11),
(48, 5, 12, 'EAI3041202312', 'uploads/pdfs/12.EAI3041202312.pdf', 12),
(49, 5, 13, 'EFE3041202312', 'uploads/pdfs/12.EFE3041202312.pdf', 13),
(50, 5, 14, 'EAEPECOG3041202312', 'uploads/pdfs/13.EAEPECOG3041202312.pdf', 14);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `footer_config`
--

CREATE TABLE `footer_config` (
  `id` int(11) NOT NULL,
  `texto_inst` text COLLATE utf8mb4_unicode_ci,
  `horario` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `telefono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_facebook` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_twitter` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_instagram` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `footer_config`
--

INSERT INTO `footer_config` (`id`, `texto_inst`, `horario`, `direccion`, `telefono`, `email`, `url_facebook`, `url_twitter`, `url_instagram`, `updated_at`) VALUES
(1, 'El DIF San Mateo Atenco es un organismo público descentralizado comprometido con el bienestar social de las familias del municipio.', 'Lunes a Viernes: 9:00 am – 3:00 pm', 'Mariano Matamoros 310,\r\nBarrio de la Concepción, 52105\r\nSan Mateo Atenco, Méx.', '(722) 970 7786', 'presidencia@difsanmateoatenco.gob.mx', 'https://www.facebook.com/DIFSanMateoAtenco', 'https://x.com/DIFSMA', 'https://www.instagram.com/difsma_/', '2026-04-27 11:01:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `footer_links`
--

CREATE TABLE `footer_links` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#',
  `nueva_tab` tinyint(1) NOT NULL DEFAULT '0',
  `orden` int(11) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `footer_links`
--

INSERT INTO `footer_links` (`id`, `titulo`, `url`, `nueva_tab`, `orden`, `activo`) VALUES
(1, 'Inicio', 'index', 0, 1, 1),
(3, 'Noticias', 'comunicacion-social/noticias', 0, 3, 1),
(4, 'Transparencia', 'transparencia/SEAC', 0, 4, 1),
(9, 'Ubícanos', '__ubicacion__', 1, 9, 1),
(11, 'Nosotros', 'voluntariado', 0, 2, 1),
(14, 'Compras y adquisiciones', 'https://www.ipomex.org.mx/ipo3/lgt/indice/DIFSANMATEO.web', 1, 5, 1),
(16, 'Sistema de Gestión de Usuarios', 'https://www.saimex.org.mx/saimex/ciudadano/login.page', 1, 7, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `galeria_albumes`
--

CREATE TABLE `galeria_albumes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_album` date NOT NULL,
  `portada_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `galeria_albumes`
--

INSERT INTO `galeria_albumes` (`id`, `nombre`, `fecha_album`, `portada_path`, `activo`, `created_at`) VALUES
(3, 'Día de las niñas y los niños', '2026-04-27', 'uploads/images/WhatsApp Image 2026-04-27 at 10.58.57 AM _2.jpeg', 1, '2026-04-27 11:05:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `galeria_imagenes`
--

CREATE TABLE `galeria_imagenes` (
  `id` int(11) NOT NULL,
  `album_id` int(11) NOT NULL,
  `imagen_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `galeria_imagenes`
--

INSERT INTO `galeria_imagenes` (`id`, `album_id`, `imagen_path`, `orden`) VALUES
(9, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 10.58.57 AM _11.jpeg', 3),
(10, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 10.58.57 AM _10.jpeg', 4),
(11, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 10.58.57 AM _8.jpeg', 5),
(12, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 10.58.57 AM _7.jpeg', 6),
(13, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 10.58.57 AM _5.jpeg', 7),
(14, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 10.58.57 AM _4.jpeg', 8),
(15, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 10.58.57 AM _3.jpeg', 9),
(16, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 10.58.57 AM _1.jpeg', 2),
(17, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 10.58.57 AM.jpeg', 1),
(19, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 12.11.17 PM _1.jpeg', 10),
(20, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 12.11.17 PM _4.jpeg', 11),
(21, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 12.11.17 PM _2.jpeg', 12),
(22, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 12.11.17 PM.jpeg', 13),
(23, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 3.27.36 PM _1.jpeg', 14),
(24, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 3.27.36 PM.jpeg', 15),
(25, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 3.27.37 PM _2.jpeg', 16),
(26, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 3.27.37 PM _1.jpeg', 17),
(27, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 3.27.50 PM.jpeg', 18),
(28, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 3.27.37 PM.jpeg', 19),
(29, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 3.50.13 PM _1.jpeg', 20),
(30, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 3.50.14 PM _4.jpeg', 21),
(31, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 3.50.14 PM _3.jpeg', 22),
(32, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 3.50.14 PM _2.jpeg', 23),
(33, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 3.50.14 PM _5.jpeg', 24),
(34, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 3.50.14 PM _1.jpeg', 25),
(35, 3, 'uploads/images/WhatsApp Image 2026-04-27 at 3.50.14 PM.jpeg', 26);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `institucion_banner`
--

CREATE TABLE `institucion_banner` (
  `id` int(11) NOT NULL,
  `imagen_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'img/institucion.png',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `institucion_banner`
--

INSERT INTO `institucion_banner` (`id`, `imagen_path`, `updated_at`) VALUES
(1, 'img/institucion.png', '2026-03-19 18:15:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'IPv4 o IPv6',
  `attempted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `ip`, `attempted_at`) VALUES
(5, '189.136.43.62', '2026-03-26 00:43:27'),
(6, '189.136.52.49', '2026-03-27 03:50:15'),
(7, '189.136.77.239', '2026-04-29 23:21:51'),
(8, '189.136.77.239', '2026-04-29 23:21:54'),
(1, '189.203.114.243', '2026-03-25 19:55:59'),
(2, '189.203.114.243', '2026-03-25 19:56:05'),
(3, '189.203.114.243', '2026-03-25 19:56:18'),
(4, '189.203.114.243', '2026-03-25 19:56:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mantenimiento_config`
--

CREATE TABLE `mantenimiento_config` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Sitio en Mantenimiento',
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `correo_contacto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'presidencia@difsanmateoatenco.gob.mx',
  `tarjeta1_titulo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Tiempo estimado',
  `tarjeta1_texto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Breve interrupción',
  `tarjeta2_titulo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Mejoras de seguridad',
  `tarjeta2_texto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Actualizaciones del sistema',
  `tarjeta3_titulo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Nuevas funciones',
  `tarjeta3_texto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Próximamente disponibles',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mantenimiento_config`
--

INSERT INTO `mantenimiento_config` (`id`, `titulo`, `descripcion`, `correo_contacto`, `tarjeta1_titulo`, `tarjeta1_texto`, `tarjeta2_titulo`, `tarjeta2_texto`, `tarjeta3_titulo`, `tarjeta3_texto`, `updated_at`) VALUES
(1, 'Sitio en Mantenimiento', 'Estamos realizando mejoras en nuestro sitio web para ofrecerte una mejor experiencia. Regresaremos en breve con contenido actualizado.', 'presidencia@difsanmateoatenco.gob.mx', 'Tiempo estimado', 'Breve interrupción', 'Mejoras de seguridad', 'Actualizaciones del sistema', 'Nuevas funciones', 'Próximamente disponibles', '2026-04-27 13:38:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mantenimiento_paginas`
--

CREATE TABLE `mantenimiento_paginas` (
  `id` int(11) NOT NULL,
  `pagina_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pagina_nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grupo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Otros',
  `en_mantenimiento` tinyint(1) NOT NULL DEFAULT '0',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mantenimiento_paginas`
--

INSERT INTO `mantenimiento_paginas` (`id`, `pagina_key`, `pagina_nombre`, `grupo`, `en_mantenimiento`, `updated_at`) VALUES
(1, 'index', 'Página Principal', 'Inicio', 0, '2026-04-27 14:03:54'),
(2, 'presidencia', 'Presidencia', 'Acerca del DIF', 0, '2026-04-27 14:03:54'),
(3, 'direcciones', 'Direcciones', 'Acerca del DIF', 0, '2026-04-27 14:03:54'),
(4, 'organigrama', 'Organigrama', 'Acerca del DIF', 0, '2026-04-27 14:03:54'),
(5, 'autismo', 'Unidad Municipal de Autismo', 'Servicios', 1, '2026-04-27 14:15:47'),
(6, 'noticias', 'Noticias', 'Comunicación Social', 0, '2026-04-27 14:03:54'),
(7, 'galeria', 'Galería', 'Comunicación Social', 0, '2026-04-27 14:03:54'),
(8, 'voluntariado', 'Voluntariado', 'Voluntariado', 0, '2026-04-27 14:03:54'),
(9, 'seac', 'SEAC', 'Transparencia', 0, '2026-04-27 14:03:54'),
(10, 'cuenta_publica', 'Cuenta Pública', 'Transparencia', 0, '2026-04-27 14:03:54'),
(11, 'presupuesto_anual', 'Presupuesto Anual', 'Transparencia', 0, '2026-04-27 14:03:54'),
(12, 'pae', 'PAE', 'Transparencia', 0, '2026-04-27 14:03:54'),
(13, 'matrices_indicadores', 'Matrices de Indicadores', 'Transparencia', 0, '2026-04-27 14:03:54'),
(14, 'conac', 'CONAC', 'Transparencia', 0, '2026-04-27 14:03:54'),
(15, 'financiero', 'Financiero', 'Transparencia', 0, '2026-04-27 14:03:54'),
(16, 'avisos_privacidad', 'Avisos de Privacidad', 'Transparencia', 0, '2026-04-27 14:03:54'),
(33, 'tramite_PMPNNA', 'Trámite: Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes', 'Servicios', 0, '2026-04-27 14:15:47'),
(34, 'tramite_DAAM', 'Trámite: Dirección de Atención a Adultos Mayores', 'Servicios', 0, '2026-04-27 14:04:59'),
(35, 'tramite_DANF', 'Trámite: Dirección de Alimentación y Nutrición Familiar', 'Servicios', 0, '2026-04-27 14:04:59'),
(36, 'tramite_DAD', 'Trámite: Dirección de Atención a la Discapacidad', 'Servicios', 0, '2026-04-27 14:04:59'),
(37, 'tramite_DPAF', 'Trámite: Dirección de Prevención y Bienestar Familiar', 'Servicios', 0, '2026-04-27 14:04:59'),
(38, 'tramite_DSJAIG', 'Trámite: Dirección de Servicios Jurídicos – Asistenciales e Igualdad de Género', 'Servicios', 0, '2026-04-27 14:04:59'),
(39, 'tramite_DIFCDTI', 'Trámite: Caravana de Servicios DIF CERCA DE TI', 'Servicios', 0, '2026-04-27 14:04:59'),
(40, 'tramite_CAPC', 'Trámite: Coordinación de Atención a Pacientes con Cáncer', 'Servicios', 0, '2026-04-27 14:04:59'),
(41, 'programa_detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', 'Programa: Detección y prevención de niñas, niños y adolescentes en situación de calle', 'Programas', 0, '2026-04-27 14:04:59'),
(42, 'programa_prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', 'Programa: Prevención y erradicación del trabajo infantil', 'Programas', 0, '2026-04-27 14:04:59'),
(43, 'programa_prevenci-n-y-erradicaci-n-del-embarazo-adolescente-14', 'Programa: Promoción del respeto a los derechos laborales de los adolescentes trabajadores en edad permitida', 'Programas', 0, '2026-04-27 14:04:59'),
(44, 'programa_entrega-de-desayunos-fr-os-15', 'Programa: Entrega de desayunos escolares fríos', 'Programas', 0, '2026-04-27 14:04:59'),
(45, 'programa_entrega-de-desayunos-calientes-15', 'Programa: Entrega de desayunos escolares calientes', 'Programas', 0, '2026-04-27 14:04:59'),
(46, 'programa_creaci-n-de-huertos-escolares-y-familiares-15', 'Programa: Creación de huertos escolares y familiares', 'Programas', 0, '2026-04-27 14:04:59'),
(47, 'programa_programa-canasta-alimentaria-del-bienestar-15', 'Programa: Programa Canasta Alimentaria del Bienestar', 'Programas', 0, '2026-04-27 14:04:59'),
(48, 'programa_asesor-as-judiciales-16', 'Programa: Asesorías jurídicas', 'Programas', 0, '2026-04-27 14:04:59'),
(49, 'programa_procedimientos-judiciales-16', 'Programa: Procedimientos judiciales', 'Programas', 0, '2026-04-27 14:04:59'),
(50, 'programa_servicios-17', 'Programa: Servicios', 'Programas', 0, '2026-04-27 14:04:59'),
(51, 'programa_vacunas-y-medicamentos-17', 'Programa: Vacunas y medicamentos', 'Programas', 0, '2026-04-27 14:04:59'),
(52, 'programa_otros-17', 'Programa: Otros', 'Programas', 0, '2026-04-27 14:04:59'),
(53, 'programa_centro-de-rehabilitaci-n-e-integraci-n-social-cris-18', 'Programa: Centro de Rehabilitación e Integración Social CRIS', 'Programas', 0, '2026-04-27 14:04:59'),
(54, 'programa_cuarto-de-estimulaci-n-multisensorial-18', 'Programa: Cuarto de Estimulación Multisensorial', 'Programas', 0, '2026-04-27 14:04:59'),
(55, 'programa_terapias-18', 'Programa: Terapias', 'Programas', 0, '2026-04-27 14:04:59'),
(56, 'programa_actividades-culturales-y-recreativas-18', 'Programa: Actividades culturales y recreativas', 'Programas', 0, '2026-04-27 14:04:59'),
(57, 'programa_programas-y-talleres-18', 'Programa: Programas y talleres', 'Programas', 0, '2026-04-27 14:04:59'),
(58, 'programa_consultas-psicol-gicas-20', 'Programa: Consultas psicológicas', 'Programas', 0, '2026-04-27 14:04:59'),
(59, 'programa_talleres-20', 'Programa: Talleres', 'Programas', 0, '2026-04-27 14:04:59'),
(60, 'programa_jornadas-informativas-escolares-20', 'Programa: Jornadas informativas escolares', 'Programas', 0, '2026-04-27 14:04:59'),
(61, 'programa_jornadas-de-mastograf-as-21', 'Programa: Jornadas de mastografías', 'Programas', 0, '2026-04-27 14:04:59'),
(62, 'programa_carril-rosa-21', 'Programa: Carril Rosa', 'Programas', 0, '2026-04-27 14:04:59'),
(63, 'programa_orientaci-n-y-acompa-amiento-21', 'Programa: Orientación y acompañamiento.', 'Programas', 0, '2026-04-27 14:04:59'),
(64, 'programa_casas-de-d-a-22', 'Programa: Casas de Día', 'Programas', 0, '2026-04-27 14:04:59'),
(65, 'programa_atenci-n-integral-a-la-salud-22', 'Programa: Atención integral a la salud', 'Programas', 0, '2026-04-27 14:04:59'),
(66, 'programa_clases-y-talleres-22', 'Programa: Clases y talleres', 'Programas', 0, '2026-04-27 14:04:59'),
(67, 'programa_paseos-recreativos-22', 'Programa: Paseos recreativos', 'Programas', 0, '2026-04-27 14:04:59'),
(68, 'programa_visitas-culturales-22', 'Programa: Visitas culturales', 'Programas', 0, '2026-04-27 14:04:59'),
(69, 'programa_celebraciones-22', 'Programa: Celebraciones', 'Programas', 0, '2026-04-27 14:04:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mi_pdfs`
--

CREATE TABLE `mi_pdfs` (
  `id` int(11) NOT NULL,
  `anio` year(4) NOT NULL,
  `pdf_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mi_pdfs`
--

INSERT INTO `mi_pdfs` (`id`, `anio`, `pdf_path`, `orden`) VALUES
(3, '2018', 'uploads/pdfs/pbr-matriz-2018.pdf', 2018),
(4, '2019', 'uploads/pdfs/MIR.pdf', 2019),
(5, '2020', 'uploads/pdfs/MIR2020.pdf', 2020),
(6, '2021', NULL, 2021),
(7, '2022', NULL, 2022),
(8, '2023', NULL, 2023),
(9, '2024', NULL, 2024);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticias_imagenes`
--

CREATE TABLE `noticias_imagenes` (
  `id` int(11) NOT NULL,
  `imagen_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_noticia` date NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `noticias_imagenes`
--

INSERT INTO `noticias_imagenes` (`id`, `imagen_path`, `fecha_noticia`, `activo`, `created_at`) VALUES
(1, 'uploads/images/b73462b7f5740034f3600a3956dfedcc.png', '2026-03-19', 1, '2026-03-19 16:30:28'),
(2, 'uploads/images/2019bcb3e6e4a865ba7277c141e3c072.png', '2026-03-19', 1, '2026-03-19 16:30:37'),
(3, 'uploads/images/d86bdce00dfcce1c574721d9084541a6.png', '2026-03-19', 1, '2026-03-19 16:30:47'),
(4, 'uploads/images/69a5c58583157854db338c95948cb829.png', '2026-03-20', 1, '2026-03-19 22:11:04'),
(5, 'uploads/images/d3e04fbd06061ccadae05ed25e5e6a19.png', '2026-03-20', 1, '2026-03-19 22:11:10'),
(6, 'uploads/images/78b7ae8ca9101855d8495eb939b17a50.png', '2026-03-20', 1, '2026-03-19 22:11:16'),
(7, 'uploads/images/aa8a5a229e4e3cd1b093dc8cf4cc4c73.png', '2026-03-23', 1, '2026-03-23 08:59:41'),
(9, 'uploads/images/WhatsApp Image 2026-04-17 at 3.41.24 PM.jpeg', '2026-04-17', 1, '2026-04-17 15:47:53'),
(10, 'uploads/images/WhatsApp Image 2026-04-27 at 10.35.28 AM.jpeg', '2026-04-27', 1, '2026-04-27 11:41:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `organigrama`
--

CREATE TABLE `organigrama` (
  `id` int(11) NOT NULL,
  `pdf_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `titulo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Organigrama 2025-2027',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `organigrama`
--

INSERT INTO `organigrama` (`id`, `pdf_path`, `titulo`, `updated_at`) VALUES
(1, NULL, 'Organigrama 2025-2027', '2026-03-19 16:03:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pae_pdfs`
--

CREATE TABLE `pae_pdfs` (
  `id` int(11) NOT NULL,
  `titulo_id` int(11) NOT NULL,
  `anio` year(4) NOT NULL,
  `pdf_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pae_pdfs`
--

INSERT INTO `pae_pdfs` (`id`, `titulo_id`, `anio`, `pdf_path`) VALUES
(3, 1, '2018', NULL),
(4, 1, '2019', 'uploads/pdfs/pae-2019.pdf'),
(5, 1, '2020', 'uploads/pdfs/PROGRAMA ANUAL DE EVALUACIÓN 2020 SMDIFSMA_1.pdf'),
(6, 1, '2021', 'uploads/pdfs/PROGRAMA ANUAL DE EVALUACION 2021._0.pdf'),
(7, 1, '2022', 'uploads/pdfs/20220429_184223__PAE 2022.pdf'),
(8, 1, '2023', 'uploads/pdfs/20230428_143919__PAE 2023.pdf'),
(9, 1, '2024', 'uploads/pdfs/20240424_182229__EVALUACION_2024.pdf'),
(10, 2, '2018', NULL),
(11, 2, '2019', NULL),
(12, 2, '2020', NULL),
(13, 2, '2021', NULL),
(14, 2, '2022', 'uploads/pdfs/20220531_093358__Terminos de Referencia.pdf'),
(15, 2, '2023', NULL),
(16, 2, '2024', 'uploads/pdfs/20240530_114549__31052024_Terminos de Referencia.pdf'),
(17, 1, '2026', 'uploads/pdfs/Programa Anual de Evaluaciones 2026 DIF.pdf'),
(18, 2, '2026', 'uploads/pdfs/TdR Evaluación de Impacto Social 2026.pdf'),
(19, 1, '2025', NULL),
(20, 2, '2025', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pae_titulos`
--

CREATE TABLE `pae_titulos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pae_titulos`
--

INSERT INTO `pae_titulos` (`id`, `nombre`, `orden`) VALUES
(1, '1.- Programa Anual de Evaluaciones', 1),
(2, '2.- Términos de Referencia', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pa_bloques`
--

CREATE TABLE `pa_bloques` (
  `id` int(11) NOT NULL,
  `anio` year(4) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pa_bloques`
--

INSERT INTO `pa_bloques` (`id`, `anio`, `orden`) VALUES
(2, '2022', 1),
(3, '2023', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pa_conceptos`
--

CREATE TABLE `pa_conceptos` (
  `id` int(11) NOT NULL,
  `bloque_id` int(11) NOT NULL,
  `nombre` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pa_conceptos`
--

INSERT INTO `pa_conceptos` (`id`, `bloque_id`, `nombre`, `orden`) VALUES
(2, 2, '1.- Caratula de Egresos', 1),
(3, 2, '2.- Caratula de Ingresos', 2),
(4, 3, '1.- Caratula de Egresos', 1),
(5, 3, '2.- Caratula de Ingresos', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pa_pdfs`
--

CREATE TABLE `pa_pdfs` (
  `id` int(11) NOT NULL,
  `concepto_id` int(11) NOT NULL,
  `sub_anio` year(4) NOT NULL,
  `pdf_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pa_pdfs`
--

INSERT INTO `pa_pdfs` (`id`, `concepto_id`, `sub_anio`, `pdf_path`, `orden`) VALUES
(4, 2, '2018', 'uploads/pdfs/caratulas.pdf', 2018),
(5, 2, '2019', 'uploads/pdfs/caratula-de-ingresos-e-egresos.pdf', 2019),
(6, 2, '2020', 'uploads/pdfs/caratula-egresos.pdf', 2020),
(7, 2, '2021', 'uploads/pdfs/1. CARATULA DE INGRESOS_1.pdf', 2021),
(8, 2, '2022', 'uploads/pdfs/20220525_131637__5.CPE30412022.pdf', 2022),
(9, 3, '2018', NULL, 2018),
(10, 3, '2019', NULL, 2019),
(11, 3, '2020', 'uploads/pdfs/caratula-ingresos.pdf', 2020),
(12, 3, '2021', 'uploads/pdfs/2. CARATULA DE EGRESOS_1.pdf', 2021),
(13, 3, '2022', 'uploads/pdfs/20220525_131611__4.CPI30412022.pdf', 2022),
(14, 4, '2018', NULL, 2018),
(15, 4, '2019', NULL, 2019),
(16, 4, '2020', NULL, 2020),
(17, 4, '2021', NULL, 2021),
(18, 4, '2022', NULL, 2022),
(19, 4, '2023', 'uploads/pdfs/CPE30412023.pdf', 2023),
(20, 5, '2018', NULL, 2018),
(21, 5, '2019', NULL, 2019),
(22, 5, '2020', NULL, 2020),
(23, 5, '2021', NULL, 2021),
(24, 5, '2022', NULL, 2022),
(25, 5, '2023', 'uploads/pdfs/CPI30412023.pdf', 2023);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presidencia`
--

CREATE TABLE `presidencia` (
  `id` int(11) NOT NULL,
  `imagen_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `cargo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `url_facebook` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `presidencia`
--

INSERT INTO `presidencia` (`id`, `imagen_path`, `nombre`, `apellidos`, `cargo`, `descripcion`, `url_facebook`, `updated_at`) VALUES
(1, 'uploads/images/b58a2209d56e509931765daeaaee1300.jpg', 'Oscar Felipe', 'Muñiz Máynez', 'Presidente Honorario San Mateo Atenco', '<p class=\"MsoNormal\" style=\"text-align: justify;\">En el Sistema Municipal DIF de San Mateo Atenco, siguiendo el liderazgo sensible y amoroso de nuestra Presidenta Municipal, Ana Aurora Mu&ntilde;iz Neyra, cada d&iacute;a fortalecemos nuestro compromiso para atender a las familias que m&aacute;s lo necesitan <span style=\"mso-spacerun: yes;\">&nbsp;</span>y seguir siendo la mano amiga que se extiende en los momentos de mayor necesidad, para brindar el abrazo solidario y el acompa&ntilde;amiento protector que requieren nuestras ni&ntilde;as, ni&ntilde;os y adolescentes, las personas adultas mayores y quienes viven con alguna discapacidad, as&iacute; como quienes enfrentan el c&aacute;ncer o atraviesan situaciones dif&iacute;ciles. Diariamente ratificamos nuestro compromiso para construir familias sanas, integradas y felices.</p>\r\n<p class=\"MsoNormal\" style=\"text-align: justify;\">Somos una instituci&oacute;n familiarmente responsable que atiende la alimentaci&oacute;n, la salud f&iacute;sica y emocional, que promueve los derechos, que orienta y protege, que siembra la cultura de paz y que trabaja cada d&iacute;a por el pleno desarrollo de los atenquenses.</p>\r\n<p class=\"MsoNormal\" align=\"center\"><strong style=\"mso-bidi-font-weight: normal;\">POR LAS FAMILIAS ATENQUENSES</strong></p>\r\n<p class=\"MsoNormal\" align=\"center\"><strong style=\"mso-bidi-font-weight: normal;\">UNIDOS CON AMOR</strong></p>', 'https://www.facebook.com/DIFSanMateoAtenco', '2026-04-13 13:07:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programas`
--

CREATE TABLE `programas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `imagen_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen_link` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `programas`
--

INSERT INTO `programas` (`id`, `nombre`, `imagen_path`, `imagen_link`, `orden`, `activo`) VALUES
(14, 'Procuraduría Municipal de Protección a Niñas, Niños y Adolescentes', 'uploads/images/verde_procu_1_5x.png', 'tramites/PMPNNA', 1, 1),
(15, 'Dirección de Alimentación y Nutrición Familiar', 'uploads/images/rojo_alimentacion_1_5x.png', 'tramites/DANF', 2, 1),
(16, 'Dirección de Servicios Jurídico-Asistenciales e Igualdad de Género', 'uploads/images/amarillo_juridico_1_5x.png', 'tramites/DSJAIG', 3, 1),
(17, 'DIF Cerca de Ti', 'uploads/images/rosa_DIF_1_5x.png', '', 4, 1),
(18, 'Dirección de Atención a Personas con Discapacidad', 'uploads/images/morado_discapacidad_1_5x.png', 'tramites/DAD', 5, 1),
(20, 'Dirección de Prevención y Bienestar Familiar', 'uploads/images/morado_prevencion_bienestar_1_5x.png', 'tramites/DPAF', 7, 1),
(21, 'Coordinación de Atención a Pacientes con Cáncer', 'uploads/images/rosa_cancer_1_5x.png', NULL, 8, 1),
(22, 'Dirección de Atención al Adulto Mayor', 'uploads/images/verde_adultos_mayores_1_5x.png', 'tramites/DAAM', 9, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programas_secciones`
--

CREATE TABLE `programas_secciones` (
  `id` int(11) NOT NULL,
  `programa_id` int(11) NOT NULL,
  `titulo` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `programas_secciones`
--

INSERT INTO `programas_secciones` (`id`, `programa_id`, `titulo`, `slug`, `orden`) VALUES
(54, 14, 'Detección y prevención de niñas, niños y adolescentes en situación de calle', 'detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', 0),
(55, 14, 'Prevención y erradicación del trabajo infantil', 'prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', 1),
(56, 14, 'Promoción del respeto a los derechos laborales de los adolescentes trabajadores en edad permitida', 'prevenci-n-y-erradicaci-n-del-embarazo-adolescente-14', 2),
(57, 15, 'Entrega de desayunos escolares fríos', 'entrega-de-desayunos-fr-os-15', 0),
(58, 15, 'Entrega de desayunos escolares calientes', 'entrega-de-desayunos-calientes-15', 1),
(59, 15, 'Creación de huertos escolares y familiares', 'creaci-n-de-huertos-escolares-y-familiares-15', 2),
(60, 15, 'Programa Canasta Alimentaria del Bienestar', 'programa-canasta-alimentaria-del-bienestar-15', 3),
(61, 16, 'Asesorías jurídicas', 'asesor-as-judiciales-16', 0),
(62, 16, 'Procedimientos judiciales', 'procedimientos-judiciales-16', 1),
(63, 17, 'Servicios', 'servicios-17', 0),
(64, 17, 'Vacunas y medicamentos', 'vacunas-y-medicamentos-17', 1),
(65, 17, 'Otros', 'otros-17', 2),
(66, 18, 'Centro de Rehabilitación e Integración Social CRIS', 'centro-de-rehabilitaci-n-e-integraci-n-social-cris-18', 0),
(68, 18, 'Cuarto de Estimulación Multisensorial', 'cuarto-de-estimulaci-n-multisensorial-18', 1),
(69, 18, 'Terapias', 'terapias-18', 2),
(70, 18, 'Actividades culturales y recreativas', 'actividades-culturales-y-recreativas-18', 3),
(71, 18, 'Programas y talleres', 'programas-y-talleres-18', 4),
(72, 20, 'Consultas psicológicas', 'consultas-psicol-gicas-20', 0),
(73, 20, 'Talleres', 'talleres-20', 1),
(74, 20, 'Jornadas informativas escolares', 'jornadas-informativas-escolares-20', 2),
(75, 21, 'Jornadas de mastografías', 'jornadas-de-mastograf-as-21', 0),
(76, 21, 'Carril Rosa', 'carril-rosa-21', 1),
(77, 21, 'Orientación y acompañamiento.', 'orientaci-n-y-acompa-amiento-21', 2),
(78, 22, 'Casas de Día', 'casas-de-d-a-22', 0),
(79, 22, 'Atención integral a la salud', 'atenci-n-integral-a-la-salud-22', 1),
(80, 22, 'Clases y talleres', 'clases-y-talleres-22', 2),
(81, 22, 'Paseos recreativos', 'paseos-recreativos-22', 3),
(82, 22, 'Visitas culturales', 'visitas-culturales-22', 4),
(83, 22, 'Celebraciones', 'celebraciones-22', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programas_secciones_paginas`
--

CREATE TABLE `programas_secciones_paginas` (
  `id` int(11) NOT NULL,
  `seccion_id` int(11) NOT NULL,
  `imagen1_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `texto1` text COLLATE utf8mb4_unicode_ci,
  `imagen2_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `texto2` text COLLATE utf8mb4_unicode_ci,
  `c_titulo1` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_titulo2` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_direccion` text COLLATE utf8mb4_unicode_ci,
  `c_telefono` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_horario` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_correo` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_contacto` longtext COLLATE utf8mb4_unicode_ci,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `programas_secciones_paginas`
--

INSERT INTO `programas_secciones_paginas` (`id`, `seccion_id`, `imagen1_path`, `texto1`, `imagen2_path`, `texto2`, `c_titulo1`, `c_titulo2`, `c_direccion`, `c_telefono`, `c_horario`, `c_correo`, `c_contacto`, `updated_at`) VALUES
(1, 54, 'uploads/images/detec_preve_nin__as_2_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\"><span class=\"s10\"><span class=\"bumpedFont15\">Para </span></span><span class=\"s10\"><span class=\"bumpedFont15\">detectar y prevenir que ni&ntilde;as, ni&ntilde;os y adolescentes vivan en situaci&oacute;n de calle, la Procuradur&iacute;a Municipal de Protecci&oacute;n a Ni&ntilde;as, Ni&ntilde;os y Adolescentes realiza de manera peri&oacute;dica recorridos por las principales calles del municipio, lo que </span></span><span class=\"s10\"><span class=\"bumpedFont15\">permite</span></span><span class=\"s10\"><span class=\"bumpedFont15\"> identificar de manera oportuna a quienes se encuentran en condiciones de vulnerabilidad y de riesgo.</span></span></span></p>', 'uploads/images/detec_preve_nin__as_1.jpg', '<p class=\"s11\" style=\"text-align: justify;\"><span style=\"font-size: 12pt;\"><span class=\"s10\"><span class=\"bumpedFont15\">A fin de brindarles atenci&oacute;n integral, canalizarlos a los servicios correspondientes y, sobre todo, garantizar su protecci&oacute;n, restituyendo sus derechos y ofreci&eacute;ndoles alternativas que les permitan alejarse de la calle, en un esfuerzo preventivo y socialmente necesario para su bienestar y desarrollo.</span></span></span></p>', '', '', '', '', '', '', '', '2026-04-22 11:37:04'),
(2, 55, 'uploads/images/trabajo_inf_1_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">La Procuradur&iacute;a Municipal de Protecci&oacute;n a Ni&ntilde;as, Ni&ntilde;os y Adolescentes realiza operativos de manera regular en zonas de alta actividad econ&oacute;mica, con el prop&oacute;sito de detectar posibles casos de trabajo infantil y prevenir situaciones de mendicidad forzada, abuso o explotaci&oacute;n laboral. Estas acciones buscan proteger sus derechos, garantizar su bienestar y promover entornos seguros que favorezcan su desarrollo integral.</span></p>', 'uploads/images/trabajo_inf_2.jpg', '<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Por estas acciones contundentes en la prevenci&oacute;n y erradicaci&oacute;n del trabajo infantil, el Sistema para el Desarrollo Integral de la Familia de San Mateo Atenco ha obtenido, por cuatro a&ntilde;os consecutivos, el <strong>distintivo EDOM&Eacute;XSTI</strong>. Este reconocimiento estatal reafirma el compromiso de la instituci&oacute;n con la protecci&oacute;n de la ni&ntilde;ez y la consolidaci&oacute;n de un municipio libre de trabajo infantil.</span></p>', '', '', '', '', '', '', '', '2026-04-27 11:22:47'),
(3, 56, 'uploads/images/embarazo_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Para promover el respeto a los derechos laborales de las y los adolescentes en edad permitida para trabajar, la Procuradur&iacute;a Municipal de Protecci&oacute;n a Ni&ntilde;as, Ni&ntilde;os y Adolescentes mantiene una permanente jornada de reflexi&oacute;n y sensibilizaci&oacute;n sobre el tema.</span></p>', 'uploads/images/embarazo_2.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\"><span class=\"s10\"><span class=\"bumpedFont15\">A trav&eacute;s de visitas a escuelas, plazas comerciales y zonas de alta actividad econ&oacute;mica, as&iacute; como encuentros con grupos de servidores p&uacute;blicos, se fomenta la conciencia sobre la importancia de garantizar condiciones dignas, seguras y libres de </span></span><span class=\"s10\"><span class=\"bumpedFont15\">abuso, poniendo especial &eacute;nfasis en el respeto pleno a los derechos del adolescente trabajador.</span></span></span></p>', '', '', '', '', '', '', '', '2026-04-22 12:00:02'),
(4, 57, 'uploads/images/desayunos_frios.jpg', '<p class=\"s5\" style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">El programa Desayunos Escolares Fr&iacute;os asegura que estudiantes de preescolar y primaria en escuelas p&uacute;blicas reciban una alimentaci&oacute;n nutritiva y segura. Consiste en una dotaci&oacute;n diaria de leche descremada, cereal integral, fruta deshidratada y oleaginosas, componentes seleccionados para combatir la malnutrici&oacute;n y favorecer el desarrollo saludable de la ni&ntilde;ez en nuestro municipio.</span></p>', 'uploads/images/desayunos_frios_2.jpg', '<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Invitamos a las familias de San Mateo Atenco a mantenerse informadas para el inicio de cada ciclo escolar, periodo en el cual podr&aacute;n solicitar la incorporaci&oacute;n al programa a trav&eacute;s de sus autoridades educativas. Con esto, garantizamos que el apoyo alimentario llegue oportunamente a las y los alumnos que m&aacute;s lo requieren desde el primer d&iacute;a de clases.</span></p>', '', '', '', '', '', '', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">El programa Alimentaci&oacute;n Escolar para el Bienestar se lleva a cabo en coordinaci&oacute;n con el DIF Estado de M&eacute;xico.</span></p>', '2026-04-22 12:20:39'),
(5, 58, 'uploads/images/desayunos_calientes.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">El Desayuno Escolar Caliente est&aacute; integrado por insumos no perecederos del plato del bien comer que permitan el consumo diario de leche descremada y/o agua natural, un platillo fuerte que incluya verduras frescas, cereal integral, leguminosas y/o alimento de origen animal y fruta fresca. Esta combinaci&oacute;n asegura que los estudiantes reciban una alimentaci&oacute;n completa y balanceada, dise&ntilde;ada espec&iacute;ficamente para cubrir sus necesidades nutricionales y fortalecer su desarrollo integral dentro de las aulas.</span></p>', 'uploads/images/desayunos_calientes_2.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Este modelo funciona gracias a la participaci&oacute;n activa de la comunidad escolar: madres y padres de familia se organizan en comit&eacute;s internos para preparar los alimentos y gestionan el servicio mediante una cuota de recuperaci&oacute;n. Gracias a este esfuerzo conjunto, los alumnos disfrutan de men&uacute;s frescos y nutritivos diariamente. <br><br>Invitamos a las familias de San Mateo Atenco a mantenerse informadas para el inicio de cada ciclo escolar, periodo en el cual podr&aacute;n solicitar la incorporaci&oacute;n al programa a trav&eacute;s de sus autoridades educativas.<br></span></p>', '', '', '', '', '', '', '<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%; font-family: Montserrat;\">El programa Alimentaci&oacute;n Escolar para el Bienestar se lleva a cabo en coordinaci&oacute;n con el DIF Estado de M&eacute;xico.</span></p>', '2026-04-22 12:32:46'),
(6, 59, 'uploads/images/huertos_familiares_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Los <strong data-path-to-node=\"2,0\" data-index-in-node=\"4\">Huertos Familiares</strong> promueven el autoconsumo y la nutrici&oacute;n saludable en el hogar. El programa ofrece asesor&iacute;a t&eacute;cnica e insumos, como semillas o pl&aacute;ntulas, para que las familias produzcan sus propios alimentos frescos. Si te interesa mejorar la econom&iacute;a y salud de tu hogar, acude a nuestras instalaciones para recibir orientaci&oacute;n personalizada de nuestro promotor e iniciar tu proyecto.</span></p>', 'uploads/images/huertos_familiares_2.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Los <strong data-path-to-node=\"2,0\" data-index-in-node=\"4\">Huertos Escolares</strong> funcionan como espacios pr&aacute;cticos donde los estudiantes aprenden sobre nutrici&oacute;n y medio ambiente. El programa dota a las escuelas de semillas o pl&aacute;ntulas y capacitaci&oacute;n t&eacute;cnica para desarrollar sus propios cultivos. Para solicitar este proyecto, las autoridades escolares deben presentar un <em>oficio de solicitud dirigido al Presidente Honorario del Sistema Municipal DIF.</em></span></p>', '', '', '', '', '', '', '', '2026-04-22 12:53:59'),
(7, 60, 'uploads/images/canasta_alimentaria.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">En coordinaci&oacute;n con el DIF Estado de M&eacute;xico, realizamos la entrega de canastas alimentarias dise&ntilde;adas para garantizar el acceso y consumo de productos nutritivos. Este programa est&aacute; dirigido a personas de entre 6 y 64 a&ntilde;os de edad, con el objetivo de fortalecer la seguridad alimentaria y mejorar la calidad de vida de las familias en nuestro municipio.</span></p>', 'uploads/images/canasta_alimentaria_2.jpg', '<p style=\"text-align: justify;\" data-path-to-node=\"5\"><span style=\"font-size: 12pt;\">El programa enfoca sus esfuerzos en atender a los sectores m&aacute;s vulnerables de la poblaci&oacute;n, como:</span></p>\r\n<ul data-path-to-node=\"6\">\r\n<li style=\"font-size: 12pt; text-align: justify;\">\r\n<p data-path-to-node=\"6,0,0\"><span style=\"font-size: 11pt;\">Madres o padres solteros con hijos de hasta 12 a&ntilde;os.</span></p>\r\n</li>\r\n<li style=\"font-size: 11pt; text-align: justify;\">\r\n<p data-path-to-node=\"6,1,0\"><span style=\"font-size: 11pt;\">Mujeres embarazadas o en periodo de lactancia.</span></p>\r\n</li>\r\n<li style=\"font-size: 11pt; text-align: justify;\">\r\n<p data-path-to-node=\"6,2,0\"><span style=\"font-size: 11pt;\">Personas con padecimientos cr&oacute;nicos.</span></p>\r\n</li>\r\n<li style=\"font-size: 11pt; text-align: justify;\">\r\n<p data-path-to-node=\"6,3,0\"><span style=\"font-size: 11pt;\">Adultos mayores (de 60 a 64 a&ntilde;os).</span></p>\r\n</li>\r\n<li style=\"font-size: 11pt; text-align: justify;\"><span style=\"font-size: 11pt;\">Personas con discapacidad.</span></li>\r\n</ul>', '', '', '', '', '', '', '', '2026-04-22 13:10:26'),
(8, 61, 'uploads/images/asesoria_juridica_2.jpg', '<p><span style=\"font-size: 12pt;\">Se brindan asesor&iacute;as jur&iacute;dicas en materia familiar para garantizar la preservaci&oacute;n de los derechos de la poblaci&oacute;n originaria o residente en San Mateo Atenco.</span></p>', 'uploads/images/asesoria_juridica_1_1.jpg', '<ul>\r\n<li style=\"font-size: 12pt;\"><span style=\"font-size: 12pt;\">Patria potestad.</span></li>\r\n<li style=\"font-size: 12pt;\"><span style=\"font-size: 12pt;\">Guarda y custodia.</span></li>\r\n<li style=\"font-size: 12pt;\"><span style=\"font-size: 12pt;\">Pensi&oacute;n alimenticia.</span></li>\r\n<li style=\"font-size: 12pt;\"><span style=\"font-size: 12pt;\">R&eacute;gimen de convivencia familiar.</span></li>\r\n<li style=\"font-size: 12pt;\"><span style=\"font-size: 12pt;\">Divorcio por mutuo consentimiento e incausado.</span></li>\r\n</ul>', '', '', '', '', '', '', '<p><span style=\"font-size: 11pt;\">* Todos los servicios generan un costo m&iacute;nimo, seg&uacute;n el tabulador aprobado por la Junta de Gobierno del Organismo.</span></p>', '2026-04-27 11:16:44'),
(9, 62, 'uploads/images/procedimientos_judiciales_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Se llevan a cabo diversos procedimientos judiciales en materia familiar, tales como la rectificaci&oacute;n de actas del Registro Civil (nacimiento, matrimonio y defunci&oacute;n) y juicios de reconocimiento de paternidad.</span></p>', 'uploads/images/procedimientos_judiciales_2.jpg', '<p><span style=\"font-size: 12pt;\">Asimismo, procesos relativos a la guarda y custodia, pensi&oacute;n alimenticia, r&eacute;gimen de convivencia y solicitudes de divorcio, ya sea por mutuo consentimiento o incausado.</span></p>', '', '', '', '', '', '', '<p class=\"MsoListParagraph\" style=\"text-align: justify;\"><span style=\"font-size: 11pt; line-height: 107%; font-family: Montserrat;\">* Todos los servicios generan un costo m&iacute;nimo, seg&uacute;n el tabulador aprobado por la Junta de Gobierno del Organismo.</span></p>', '2026-04-22 17:36:48'),
(10, 63, 'uploads/images/servicios_2.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">A trav&eacute;s de la caravana&nbsp;DIF Cerca de Ti, el DIF San Mateo Atenco acerca servicios asistenciales directamente a cada barrio y colonia. Nuestras acciones est&aacute;n dise&ntilde;adas para atender prioritariamente a los grupos vulnerables, consolidando as&iacute; un gobierno familiarmente responsable que garantiza el bienestar en cada rinc&oacute;n del municipio.</span></p>', 'uploads/images/servicios_1_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Dentro de esta caravana, los ciudadanos pueden acceder a consultas de medicina general, gerontolog&iacute;a y odontolog&iacute;a, as&iacute; como apoyo nutricional. De manera complementaria, se brindan asesor&iacute;as en el &aacute;mbito psicol&oacute;gico y jur&iacute;dico, incluyendo asesor&iacute;a para aquellos ciudadanos que requieren realizar tr&aacute;mites de regularizaci&oacute;n de sus inmuebles.</span></p>', '', '', '', '', '', '', '', '2026-04-27 11:19:51'),
(11, 64, 'uploads/images/vacunas_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">La prevenci&oacute;n es el pilar de la caravana, por lo que se despliega un esquema de vacunaci&oacute;n y detecci&oacute;n oportuna de enfermedades. A trav&eacute;s de estas jornadas, se busca fortalecer el sistema inmunol&oacute;gico de la comunidad y detectar factores de riesgo de manera temprana, garantizando que el bienestar llegue a todos los rincones del municipio sin costo para el beneficiario.</span></p>', 'uploads/images/vacunas_2.jpg', '<p><span style=\"font-size: 12pt;\">Para asegurar un monitoreo integral, se realizan diversas pruebas r&aacute;pidas de laboratorio que permiten medir niveles de glucosa, colesterol y triglic&eacute;ridos. Asimismo, se aplican reactivos para la detecci&oacute;n de ant&iacute;geno prost&aacute;tico, hepatitis B y C, y COVID, proporcionando a los asistentes resultados inmediatos que son vitales para la toma de decisiones m&eacute;dicas preventivas.</span></p>', '', '', '', '', '', '', '', '2026-04-22 14:06:41'),
(12, 65, 'uploads/images/otros_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">M&aacute;s all&aacute; de la salud, la caravana integra programas sociales y de participaci&oacute;n comunitaria que enriquecen el tejido social. Los asistentes pueden informarse sobre actividades culturales y deportivas, as&iacute; como participar en iniciativas solidarias del Voluntariado, tales como el Tenderito DIF, el Trenzat&oacute;n y la recolecci&oacute;n de tapitas, promoviendo adem&aacute;s la adopci&oacute;n responsable de mascotas (perros y gatos) y ofreciendo cortes de cabello gratuitos.</span></p>', 'uploads/images/otros_2.jpg', '<p><span style=\"font-size: 12pt;\">La realizaci&oacute;n exitosa de estas jornadas es posible gracias a la estrecha colaboraci&oacute;n interinstitucional con organismos como el DIFEM, la EDAYO San Mateo Atenco, el IMEVIS y la Academia Lupita. Esta suma de esfuerzos entre el Gobierno Municipal y diversas instituciones educativas y estatales garantiza una atenci&oacute;n integral y de alta calidad para todos los habitantes de San Mateo Atenco.</span></p>', '', '', '', '', '', '', '', '2026-04-22 14:08:23'),
(13, 66, 'uploads/images/cris_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">El Centro de Rehabilitaci&oacute;n e Integraci&oacute;n Social se ha consolidado como un referente de salud gracias a la calidad de sus servicios y, sobre todo, al sentido humanista de su personal. Contamos con un equipo multidisciplinario de m&eacute;dicos especialistas, m&eacute;dico general, enfermeras y terapeutas dedicados a brindar una atenci&oacute;n digna y profesional a cada usuario.</span></p>', 'uploads/images/cris_2.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Nuestro compromiso fundamental es mejorar la calidad de vida de nuestros usuarios, ofreciendo un acompa&ntilde;amiento integral en sus procesos de recuperaci&oacute;n. En el CRIS, trabajamos diariamente para que cada paciente reciba un trato c&aacute;lido y eficiente que favorezca su bienestar f&iacute;sico y emocional dentro de un entorno seguro y profesional.</span></p>', '', '', '', '', '', '', '', '2026-04-27 10:35:25'),
(14, 68, 'uploads/images/cuarto_estimulacion_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Este espacio terap&eacute;utico de vanguardia est&aacute; dise&ntilde;ado para favorecer el bienestar f&iacute;sico, emocional y cognitivo de personas con discapacidad o necesidades espec&iacute;ficas. A trav&eacute;s de est&iacute;mulos controlados, el ambiente permite a los usuarios interactuar con su entorno de manera positiva, fortaleciendo sus capacidades en un entorno de calma y seguridad.</span></p>', 'uploads/images/cuarto_estimulacion_2.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Entre sus principales beneficios se encuentran la regulaci&oacute;n de estados emocionales, la mejora en los niveles de atenci&oacute;n y el desarrollo de habilidades motoras. Es una herramienta esencial que potencia la exploraci&oacute;n sensorial y el aprendizaje, permitiendo que cada persona avance a su propio ritmo en su proceso terap&eacute;utico.</span></p>', '', '', '', '', '', '', '', '2026-04-27 10:39:38'),
(15, 69, 'uploads/images/terapias_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">El CRIS ofrece servicios especializados en terapia f&iacute;sica, de lenguaje y ocupacional, dise&ntilde;ados para atender de manera puntual las necesidades de movilidad, comunicaci&oacute;n y autonom&iacute;a de nuestros usuarios. Estas terapias son fundamentales para que las personas puedan recuperar o fortalecer las funciones necesarias para su vida diaria.</span></p>', 'uploads/images/terapias_2.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Para garantizar un tratamiento adecuado, la prescripci&oacute;n de cada terapia se realiza tras una evaluaci&oacute;n detallada por parte de nuestro m&eacute;dico especialista en rehabilitaci&oacute;n. Este diagn&oacute;stico integral permite dise&ntilde;ar un plan de trabajo personalizado que asegura mejores resultados y un seguimiento puntual en la evoluci&oacute;n de cada paciente.</span></p>', '', '', '', '', '', '', '<p><span style=\"font-size: 11pt; line-height: 107%; font-family: Montserrat;\">* Todos los servicios generan un costo m&iacute;nimo, seg&uacute;n el tabulador aprobado por la Junta de Gobierno del Organismo.</span></p>', '2026-04-27 10:42:07'),
(16, 70, 'uploads/images/activi_recreativas_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">La integraci&oacute;n de los usuarios del CRIS en entornos culturales y recreativos es clave para fortalecer su desarrollo personal, emocional y social. Promovemos espacios de convivencia donde la participaci&oacute;n activa permite el reconocimiento de las capacidades individuales, eliminando barreras y fomentando un fuerte sentido de pertenencia.</span></p>', 'uploads/images/activi_recreativas_2.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">En el DIF San Mateo Atenco, impulsamos programas que abren oportunidades para que todas y todos puedan expresarse en igualdad de condiciones. Al fomentar el arte y el sano esparcimiento, construimos una sociedad m&aacute;s incluyente y solidaria, donde el talento y la alegr&iacute;a de vivir no conocen l&iacute;mites.</span></p>', '', '', '', '', '', '', '', '2026-04-27 10:44:40'),
(17, 71, 'uploads/images/programas_talleres_2.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Nuestros talleres est&aacute;n dise&ntilde;ados para brindar herramientas pr&aacute;cticas y emocionales tanto a los usuarios como a sus familias, promoviendo una inclusi&oacute;n integral. Contamos con programas de autoempleo para personas con discapacidad, enfocados en habilidades productivas que favorecen su independencia econ&oacute;mica y su incorporaci&oacute;n activa a la vida laboral.</span></p>', 'uploads/images/programas_talleres_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Asimismo, ofrecemos apoyo especializado mediante el taller para personas amputadas, orientado a la adaptaci&oacute;n y fortalecimiento f&iacute;sico, y el taller para personas cuidadoras. Este &uacute;ltimo es fundamental para brindar t&eacute;cnicas de cuidado integral y fomentar el autocuidado de quienes dedican su vida a la atenci&oacute;n de sus seres queridos.</span></p>', '', '', '', '', '', '', '', '2026-04-27 10:46:13'),
(18, 72, 'uploads/images/cosultas_pscologicas_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%; font-family: Montserrat; mso-fareast-font-family: Calibri; mso-fareast-theme-font: minor-latin; mso-bidi-font-family: \'Times New Roman\'; mso-bidi-theme-font: minor-bidi; mso-ansi-language: ES-MX; mso-fareast-language: EN-US; mso-bidi-language: AR-SA;\">Se brindan consultas psicol&oacute;gicas a ni&ntilde;as, ni&ntilde;os, adolescentes, adultos y personas adultas mayores con el prop&oacute;sito de favorecer el desarrollo de habilidades personales y prevenir enfermedades mentales.</span></p>', 'uploads/images/cosultas_pscologicas_2.jpg', '<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%; font-family: Montserrat;\">Se prioriza el inter&eacute;s superior de la ni&ntilde;ez y adolescencia, personas en situaci&oacute;n de vulnerabilidad dentro de dicho grupo poblacional, as&iacute; como v&iacute;ctimas de violencia. La atenci&oacute;n se brinda a la poblaci&oacute;n originaria o residente en San Mateo Atenco.</span></p>', '', '', '', '', '', '', '<p class=\"MsoListParagraph\" style=\"text-align: justify;\"><span style=\"font-size: 11pt; line-height: 107%; font-family: Montserrat;\">* Todos los servicios generan un costo m&iacute;nimo, seg&uacute;n el tabulador aprobado por la Junta de Gobierno del Organismo.</span></p>', '2026-04-27 11:07:27'),
(19, 73, 'uploads/images/talleres_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\"><span class=\"\">Creemos firmemente en el poder de la prevenci&oacute;n y en la importancia de aprender juntos.</span><span class=\"\"> Por eso,</span><span class=\"\"> organizamos talleres dise&ntilde;ados para brindar herramientas emocionales pr&aacute;cticas que fortalezcan los v&iacute;nculos familiares y promuevan un entorno de respeto y seguridad.</span><span class=\"\"> Estos espacios son una oportunidad para compartir experiencias y adquirir conocimientos que mejoran nuestra convivencia diaria.</span></span></p>', 'uploads/images/talleres_2.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\"><span class=\"\">Dentro de nuestra oferta de talleres,</span><span class=\"\"> abordamos temas fundamentales como la escuela para padres</span><span class=\"\">,</span><span class=\"\"> adem&aacute;s de educaci&oacute;n sexual orientada a la prevenci&oacute;n del embarazo adolescente y programas de autoempleo.</span><span class=\"\"> Cada sesi&oacute;n est&aacute; pensada para que las y los participantes se lleven habilidades &uacute;tiles que les permitan construir relaciones m&aacute;s sanas y una comunidad m&aacute;s consciente.</span></span></p>', '', '', '', '', '', '', '', '2026-04-27 11:10:58'),
(20, 74, 'uploads/images/jornadas_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\"><span class=\"\">La prevenci&oacute;n comienza desde el aula,</span><span class=\"\"> y por ello,</span><span class=\"\"> salimos al encuentro de nuestra comunidad estudiantil mediante jornadas informativas.</span><span class=\"\"> Estas actividades tienen como prop&oacute;sito sensibilizar a la poblaci&oacute;n escolar sobre la importancia del cuidado emocional,</span><span class=\"\"> proporcionando informaci&oacute;n clara y confiable que ayuda a detectar y prevenir riesgos a una edad temprana.</span></span></p>', 'uploads/images/jornadas_2.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\"><span class=\"\">En estas jornadas,</span><span class=\"\"> fomentamos la participaci&oacute;n activa de los alumnos y </span><span class=\"\">docentes para crear una red de apoyo s&oacute;lida.</span><span class=\"\"> Al llevar el DIF directamente a las escuelas,</span><span class=\"\"> buscamos que cada estudiante crezca en un ambiente de confianza,</span><span class=\"\"> donde la comunicaci&oacute;n abierta y el autocuidado sean siempre la herramienta principal para enfrentar los retos de la vida.</span></span></p>', '', '', '', '', '', '', '', '2026-04-27 11:12:57'),
(21, 78, 'uploads/images/casa_dia.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">El municipio pone a su disposici&oacute;n seis Casas de D&iacute;a, espacios dise&ntilde;ados especialmente para la convivencia, el cuidado y la atenci&oacute;n de nuestras personas adultas mayores. Estos centros son refugios de alegr&iacute;a donde se promueve un envejecimiento activo en un ambiente seguro y amigable.</span></p>', 'uploads/images/casa_dia_2.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Actualmente, contamos con ubicaciones estrat&eacute;gicas para que siempre tengan una opci&oacute;n cerca de su hogar. Pueden encontrarnos en los barrios de <strong data-path-to-node=\"6\" data-index-in-node=\"143\">Guadalupe, San Francisco, Santa Mar&iacute;a, San Isidro y La Concepci&oacute;n</strong>, as&iacute; como en la <strong data-path-to-node=\"6\" data-index-in-node=\"225\">colonia Reforma</strong>. &iexcl;Nuestras puertas est&aacute;n abiertas para recibirlos!</span></p>', '', '', '', '', '', '', '<p style=\"text-align: justify;\"><span style=\"font-size: 11pt;\">Las personas de 60 a&ntilde;os o m&aacute;s pueden afiliarse en su Casa de D&iacute;a m&aacute;s cercana presentando dos copias de su credencial de elector, dos fotograf&iacute;as tama&ntilde;o infantil y dos n&uacute;meros telef&oacute;nicos de un familiar o tutor responsable.</span></p>', '2026-04-27 12:28:10'),
(22, 79, 'uploads/images/foto_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">La salud es nuestra prioridad, por ello, ofrecemos servicios m&eacute;dicos especializados para garantizar su bienestar f&iacute;sico. Contamos con consultas de gerontolog&iacute;a, odontolog&iacute;a y medicina general, adem&aacute;s de servicios complementarios como quiropr&aacute;ctica y masoterapia para su comodidad.</span></p>', 'uploads/images/foto_2.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Entendemos que estar bien tambi&eacute;n implica cuidar la mente y la alimentaci&oacute;n. Por esta raz&oacute;n, brindamos acompa&ntilde;amiento profesional en psicolog&iacute;a y nutrici&oacute;n, asegurando que cada persona adulta mayor reciba una atenci&oacute;n completa, humana y adaptada a sus necesidades particulares.</span></p>', '', '', '', '', '', '', '', '2026-04-27 12:03:45'),
(23, 80, 'uploads/images/talleres.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Para mantener el cuerpo y la mente en movimiento, contamos con una amplia oferta de actividades recreativas y formativas. Invitamos a todas y todos nuestros adultos mayores a participar en nuestras clases de yoga, activaci&oacute;n f&iacute;sica, danza folcl&oacute;rica y coro, ideales para fortalecer la salud emocional y f&iacute;sica mientras se divierten.</span></p>', 'uploads/images/talleres_2_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Adem&aacute;s, fomentamos la creatividad y el aprendizaje de nuevos oficios a trav&eacute;s de talleres pr&aacute;cticos. Contamos con cursos de corte y confecci&oacute;n, cocina, pintura, bisuter&iacute;a, tejido y manualidades, permitiendo que nuestros adultos mayores sigan desarrollando sus talentos y habilidades manuales.</span></p>', '', '', '', '', '', '', '', '2026-04-27 12:06:01'),
(24, 81, 'uploads/images/paseos_1.jpg', '<p><span style=\"font-size: 12pt;\">La convivencia y la alegr&iacute;a de explorar nuevos lugares son fundamentales en esta etapa de la vida. Organizamos paseos recreativos a diversos puntos tur&iacute;sticos dentro del estado, permitiendo que nuestros beneficiarios disfruten de paisajes hermosos y momentos inolvidables.</span></p>', 'uploads/images/paseos_2.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Estos viajes est&aacute;n planeados con todas las medidas de seguridad y comodidad necesarias. El objetivo principal es fortalecer los lazos de amistad y ofrecer experiencias de esparcimiento que rompan con la rutina, llenando sus d&iacute;as de nuevas an&eacute;cdotas y mucha felicidad.</span></p>', '', '', '', '', '', '', '', '2026-04-27 12:08:06'),
(25, 82, 'uploads/images/visitas_culturales_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Fomentamos el enriquecimiento intelectual a trav&eacute;s de una cartelera de visitas culturales constantes. Llevamos a nuestros adultos mayores a recorrer museos emblem&aacute;ticos, as&iacute; como a disfrutar de funciones de teatro y cine, promoviendo el acceso a las artes y la cultura como un derecho fundamental.</span></p>', 'uploads/images/visitas_culturales_2.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Estas salidas no solo son entretenidas, sino que tambi&eacute;n estimulan la curiosidad y el di&aacute;logo. Al visitar lugares de inter&eacute;s cultural, buscamos que sigan conectados con la historia y las expresiones art&iacute;sticas de nuestra comunidad, sinti&eacute;ndose siempre parte activa de la sociedad.</span></p>', '', '', '', '', '', '', '', '2026-04-27 12:11:00'),
(26, 83, 'uploads/images/celebraciones_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">La vida se disfruta m&aacute;s cuando compartimos momentos especiales, por ello organizamos eventos y celebraciones tradicionales a lo largo del a&ntilde;o, llenos de m&uacute;sica, baile y sana convivencia.</span></p>', 'uploads/images/celebraciones_2.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Estas reuniones son el coraz&oacute;n de nuestra comunidad, donde honramos la sabidur&iacute;a y experiencia de nuestros adultos mayores. Queremos que cada evento sea una oportunidad para que se sientan valorados, queridos y, sobre todo, profundamente respetados.</span></p>', '', '', '', '', '', '', '', '2026-04-27 12:13:34'),
(27, 75, 'uploads/images/IMG_4145 2.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Para fortalecer la cultura de la prevenci&oacute;n en nuestro municipio, gestionamos mensualmente jornadas de mastograf&iacute;as gratuitas en colaboraci&oacute;n con la UNEME-DEDICAM. Este esfuerzo busca facilitar el acceso a estudios de diagn&oacute;stico oportuno, permitiendo que m&aacute;s mujeres puedan cuidar de su salud sin que el costo sea un impedimento.</span></p>', 'uploads/images/IMG_5262.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Invitamos a todas las mujeres atenquenses, especialmente a aquellas a partir de los 40 a&ntilde;os, a sumarse a este ejercicio de autocuidado y amor propio. Al asistir a estas jornadas, no solo recibes una atenci&oacute;n profesional y amable, sino que tambi&eacute;n te integras a una comunidad que prioriza la vida y el bienestar a trav&eacute;s de la detecci&oacute;n temprana.</span></p>', '', '', '', '', '', '', '', '2026-04-27 12:15:02'),
(28, 76, 'uploads/images/IMG_6690.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">El Carril Rosa, ubicado en la alberca de la Unidad Deportiva, es un espacio seguro y especializado dise&ntilde;ado exclusivamente para personas con antecedentes de c&aacute;ncer. Aqu&iacute;, los usuarios cuentan con la supervisi&oacute;n de expertos que gu&iacute;an cada movimiento para asegurar que la actividad f&iacute;sica sea terap&eacute;utica y reconfortante.</span></p>', 'uploads/images/IMG_6699.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Las actividades acu&aacute;ticas est&aacute;n enfocadas en disminuir el linfedema, mejorar la movilidad y fortalecer la conciencia corporal. Todo esto sucede en un ambiente relajante que no solo beneficia al cuerpo, sino que tambi&eacute;n brinda un importante apoyo emocional y un respiro de bienestar para cada participante.</span></p>', '', '', '', '', '', '', '', '2026-04-27 11:46:20'),
(29, 77, 'uploads/images/IMG_7469 3_1.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">A trav&eacute;s de la Coordinaci&oacute;n de Atenci&oacute;n a Pacientes con C&aacute;ncer, brindamos una mano amiga a quienes enfrentan este proceso. Nuestro prop&oacute;sito es acercar servicios esenciales a la poblaci&oacute;n mediante sesiones informativas que promueven la prevenci&oacute;n, adem&aacute;s de realizar visitas domiciliarias para acompa&ntilde;ar de cerca a los pacientes y sus familias.</span></p>', 'uploads/images/IMG_9981.jpg', '<p style=\"text-align: justify;\"><span style=\"font-size: 12pt;\">Complementamos este apoyo con programas permanentes de gran coraz&oacute;n: el Trenzat&oacute;n DIF, donde recolectamos trenzas para elaborar pelucas oncol&oacute;gicas, y nuestra campa&ntilde;a de recolecci&oacute;n de tapitas, <span style=\"font-size: 12.0pt; line-height: 107%; font-family: Montserrat; mso-fareast-font-family: Calibri; mso-fareast-theme-font: minor-latin; mso-bidi-font-family: \'Times New Roman\'; mso-bidi-theme-font: minor-bidi; mso-ansi-language: ES-MX; mso-fareast-language: EN-US; mso-bidi-language: AR-SA;\">que contribuye a la realizaci&oacute;n de tratamientos oncol&oacute;gicos para ni&ntilde;as y ni&ntilde;os.</span></span></p>', '', '', '', '', '', '', '', '2026-04-27 11:56:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seac_bloques`
--

CREATE TABLE `seac_bloques` (
  `id` int(11) NOT NULL,
  `anio` year(4) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `seac_bloques`
--

INSERT INTO `seac_bloques` (`id`, `anio`, `orden`) VALUES
(5, '2025', 1),
(6, '2024', 2),
(7, '2023', 3),
(8, '2022', 4),
(9, '2021', 5),
(10, '2020', 6),
(11, '2019', 7),
(12, '2018', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seac_conceptos`
--

CREATE TABLE `seac_conceptos` (
  `id` int(11) NOT NULL,
  `bloque_id` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `nombre` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `seac_conceptos`
--

INSERT INTO `seac_conceptos` (`id`, `bloque_id`, `numero`, `nombre`, `orden`) VALUES
(7, 5, 1, 'Estado de Situación Financiera', 1),
(8, 5, 2, 'Estado de Actividades', 2),
(9, 5, 3, 'Estado Analitico de Activo', 3),
(10, 5, 4, 'Estado Analítico de la Deuda y otros Pasivos', 4),
(11, 5, 5, 'Estado de Cambios en la Situación Financiera', 5),
(12, 5, 6, 'Estado de Flujos de Efectivo', 6),
(13, 5, 7, 'Estado de Variación en la Hacienda Pública', 7),
(14, 5, 8, 'Notas a los Estados Financieros', 8),
(15, 5, 9, 'Estado Analítico de Egresos de Clasificación por Objeto del Gasto', 9),
(16, 5, 10, 'Estado Analítico de Egresos de Clasificación Administrativa', 10),
(17, 5, 11, 'Estado Analítico de Egresos de Clasificación Funcional', 11),
(18, 5, 12, 'Estado Analítico de Egresos de Clasificación Programática', 12),
(19, 5, 13, 'Estado Analítico de Egresos de Clasificación Económica', 13),
(20, 5, 14, 'Estado Analítico de Ingresos', 14),
(21, 5, 15, 'Pasivos Contingentes', 15),
(22, 5, 16, 'Ayudas y Subsidios', 16),
(23, 5, 17, 'Inventario de Bienes Inmuebles', 17),
(24, 5, 18, 'Inventario de Bienes Muebles', 18),
(25, 5, 19, 'PbRM08b30412025', 19),
(26, 6, 1, 'Ayudas y Subsidios', 1),
(27, 6, 2, 'EA3041202403', 2),
(28, 6, 3, 'ESF3041202403', 3),
(29, 6, 4, 'EVHP3041202403', 4),
(30, 6, 5, 'ECSF3041202403', 5),
(31, 6, 6, 'EFE3041202403', 6),
(32, 6, 7, 'PASIVOS CONTING', 7),
(33, 6, 8, 'NEF3041202403', 8),
(34, 6, 9, 'EAA3041202403', 9),
(35, 6, 10, 'EADOP3041202403', 10),
(36, 6, 11, 'EAI3041202403', 11),
(37, 6, 12, 'Clas Administrativa', 12),
(38, 6, 13, 'Clas Econom', 13),
(39, 6, 14, 'Clas Obj Gsto', 14),
(40, 6, 15, 'Clas Func', 15),
(41, 6, 16, 'ENDEUDAMIENTO NETO', 16),
(42, 6, 17, 'IDP3041202403', 17),
(43, 6, 18, 'Gsto Cat Progra', 18),
(44, 6, 19, 'PbRM08b3041202403', 19),
(45, 6, 20, 'PROG Y PROY INVERSION', 20),
(46, 6, 21, 'manual de contabilidad', 21),
(47, 6, 1, 'A.1.1 La Lista de Cuentas está alineada al Plan de Cuentas emitido por el Consejo Nacional de Armonización Contable (CONAC)', 22),
(48, 6, 2, 'A.1.2 Cuenta con Manual de Contabilidad', 23),
(49, 6, 3, 'A.1.3 Dispone de la Matriz Devengado de Gastos', 24),
(50, 6, 4, 'A.1.4 Dispone de la Matriz Pagado de Gastos', 25),
(51, 6, 5, 'A.1.5 Dispone de la Matriz Ingresos Devengados', 26),
(52, 6, 6, 'A.1.6 Dispone de la Matriz Ingresos Recaudados', 27),
(53, 6, 7, 'A.2.1 Registra en cuentas específicas de activo los bienes muebles', 28),
(54, 6, 8, 'A.2.2 Registra en cuentas específicas de activo los bienes inmuebles', 29),
(55, 6, 9, 'A.2.3 Los bienes inmuebles se registran contablemente como mínimo a valor catastral', 30),
(56, 6, 10, 'A.2.4 Registra en cuentas específicas de activo la baja de bienes muebles  ', 31),
(57, 6, 11, 'A.2.5 Registra en cuentas específicas de activo la baja de bienes inmuebles', 32),
(58, 6, 12, 'A.2.6 Realiza el registro auxiliar de los bienes bajo su custodia, que sean inalienables e imprescriptibles (monumentos arqueológicos, artísticos e históricos)', 33),
(59, 6, 13, 'A.2.7 Registra contablemente las inversiones en bienes de dominio público', 34),
(60, 6, 14, 'A.2.8 Registra las obras en proceso en una cuenta contable específica de activo', 35),
(61, 6, 15, 'A.2.9 Registra en una cuenta de activo los derechos patrimoniales que tengan en los fideicomisos sin estructura orgánica, mandatos y contratos análogos', 36),
(62, 6, 16, 'A.2.11 Registra el gasto devengado conforme a lo señalado en la norma aprobada por el CONAC', 37),
(63, 6, 17, 'A.2.12 Registra el ingreso devengado conforme a lo señalado en la norma aprobada por el CONAC', 38),
(64, 6, 18, 'A.2.13 Mantiene registro histórico de sus operaciones en el Libro de Diario', 39),
(65, 6, 19, 'A.2.14 Mantiene registro histórico de sus operaciones en el Libro Mayor', 40),
(66, 6, 20, 'A.2.15 Mantiene registro histórico de sus operaciones en el Libro de Inventarios de Materias Primas, Materiales y Suministros para Producción', 41),
(67, 6, 21, 'A.2.16 Mantiene registro histórico de sus operaciones en el Libro de Almacén de Materiales y Suministros de Consumo', 42),
(68, 6, 22, 'A.2.17 Mantiene registro histórico de sus operaciones en el Libro de Inventarios de Bienes Muebles e Inmuebles', 43),
(69, 6, 23, 'A.2.18 Mantiene registro histórico de sus operaciones en el Libro de Balances', 44),
(70, 6, 24, 'A.2.19 Constituye provisiones', 45),
(71, 6, 25, 'A.2.20 Revisa y ajusta periódicamente las provisiones para mantener su vigencia  ', 46),
(72, 6, 26, 'A.2.24 Derivado del proceso de transición de una administración a otra, la administración entrante realiza el registro e inventario de los bienes que no se encuentren inventariados o están en proceso de registro', 47),
(73, 6, 27, 'A.2.25 Realiza los registros contables con base acumulativa para la obtención de la información presupuestaria y contable, mostrando los avances que permitan evaluar el ejercicio del gasto público y la captación del ingreso', 48),
(74, 6, 28, 'A.3.1 Expresa en los estados financieros los esquemas de pasivos, incluyendo los considerados deuda pública', 49),
(75, 6, 29, 'A.3.2 Genera el Estado de Actividades en forma periódica (mes, trimestre, anual, etc.), derivado de los procesos administrativos que operan en tiempo real y que generan registros automáticos y por única vez', 50),
(76, 6, 30, 'A.3.3 Genera el Estado de Situación Financiera en forma periódica (mes, trimestre, anual, etc.), derivado de los procesos administrativos que operan en tiempo real y que generan registros automáticos y por única vez', 51),
(77, 6, 31, 'A.3.4 Genera el Estado de Variación en la Hacienda Pública en forma periódica (mes, trimestre, anual, etc.), derivado de los procesos administrativos que operan en tiempo real y que generan registros automáticos y por única vez', 52),
(78, 6, 32, 'A.3.5 Genera el Estado de Cambios en la Situación Financiera en forma periódica (mes, trimestre, anual, etc.), derivado de los procesos administrativos que operan en tiempo real y que generan registros automáticos y por única vez', 53),
(79, 6, 33, 'A.3.6 Genera el Estado de Flujos de Efectivo en forma periódica (mes, trimestre, anual, etc.), derivado de los procesos administrativos que operan en tiempo real y que generan registros automáticos y por única vez', 54),
(80, 6, 34, 'A.3.7 Genera los Informes sobre Pasivos Contingentes en forma periódica (mes, trimestre, anual, etc.)', 55),
(81, 6, 35, 'A.3.8 Genera las Notas a los Estados Financieros en forma periódica (mes, trimestre, anual, etc.)', 56),
(82, 6, 36, 'A.3.9 Genera el Estado Analítico del Activo en forma periódica (mes, trimestre, anual, etc.), derivado de los procesos administrativos que operan en tiempo real y que generan registros automáticos y por única vez', 57),
(83, 6, 37, 'A.3.10 Genera el Estado Analítico de la Deuda y Otros Pasivos en forma periódica (mes, trimestre, anual, etc.), derivado de los procesos administrativos que operan en tiempo real y que generan registros automáticos y por única vez', 58),
(84, 6, 38, 'B.1.1 Cuenta con Clasificador por Rubros de Ingresos armonizado', 59),
(85, 6, 39, 'B. 1.2 Cuenta con Clasificador por Objeto del Gasto armonizado', 60),
(86, 6, 40, 'B.1.3 Cuenta con Clasificador Económico (por Tipo de Gasto) armonizado', 61),
(87, 6, 41, 'B.1.4 Cuenta con Clasificación Funcional armonizada', 62),
(88, 6, 42, 'B.1.5 Cuenta con Clasificación Programática armonizada', 63),
(89, 6, 43, 'B.1.6 Cuenta con Clasificación Administrativa armonizada', 64),
(90, 6, 44, 'B.1.7 Cuenta con Clasificador por Fuentes de Financiamiento armonizado', 65),
(91, 6, 45, 'B.2.1 Registra la etapa del Presupuesto de Egresos Aprobado', 66),
(92, 6, 46, 'B.2.2 Registra la etapa del Presupuesto de Egresos Modificado', 67),
(93, 6, 47, 'B.2.3 Registra la etapa del Presupuesto de Egresos Comprometido', 68),
(94, 6, 48, 'B.2.4 Registra la etapa del Presupuesto de Egresos Devengado', 69),
(95, 6, 49, 'B.2.5 Registra la etapa del Presupuesto de Egresos Ejercido', 70),
(96, 6, 50, 'B.2.6 Registra la etapa del Presupuesto de Egresos Pagado', 71),
(97, 6, 51, 'B.2.7 Registra la etapa del Presupuesto de Ingreso Estimado', 72),
(98, 6, 52, 'B.2.8 Registra la etapa del Presupuesto de Ingreso Modificado', 73),
(99, 6, 53, 'B.2.9 Registra la etapa del Presupuesto de Ingreso Devengado', 74),
(100, 6, 54, 'B.2.10 Registra la etapa del Presupuesto de Ingreso Recaudado', 75),
(101, 6, 55, 'B.2.11 Integra en forma automática el ejercicio presupuestario con la operación contable, a partir de la utilización del gasto devengado', 76),
(102, 6, 56, 'B.2.15 Operación de los procesos administrativos o subsistemas que permitan la emisión periódica (mes, trimestre, anual, etc.) de los estados financieros', 77),
(103, 6, 57, 'B.3.1 Genera el Estado Analítico de Ingresos en forma periódica (mes, trimestre, anual, etc.), derivados de los procesos administrativos que operan en tiempo real y que generan registros automáticos y por única vez', 78),
(104, 6, 58, 'B.3.2 Genera el Estado Analítico del Ejercicio del Presupuesto de Egresos con base en la Clasificación Administrativa en forma periódica (mes, trimestre, anual, etc.), derivado de los procesos administrativos que operan en tiempo real y que generan registros automáticos y por única vez', 79),
(105, 6, 59, 'B.3.3 Genera el Estado Analítico del Ejercicio del Presupuesto de Egresos con base en la Clasificación Económica (por Tipo de Gasto) en forma periódica (mes, trimestre, anual, etc.), derivado de los procesos administrativos que operan en tiempo real y que generan registros automáticos y por única vez', 80),
(106, 6, 60, 'B.3.4 Genera el Estado Analítico del Ejercicio del Presupuesto de Egresos con base en la Clasificación por Objeto de Gasto en forma periódica (mes, trimestre, anual, etc.), derivado de los procesos administrativos que operan en tiempo real y que generan registros automáticos y por única vez', 81),
(107, 6, 61, 'B.3.5 Genera el Estado Analítico del Ejercicio del Presupuesto de Egresos con base en la Clasificación Funcional en forma periódica (mes, trimestre, anual, etc.), derivado de los procesos administrativos que operan en tiempo real y que generan registros automáticos y por única vez', 82),
(108, 6, 62, 'B.3.6 Genera el Endeudamiento Neto', 83),
(109, 6, 63, 'B.3.7 Genera los Intereses de la Deuda', 84),
(110, 6, 64, 'B.4.1 Genera el Estado de Gasto por Categoría Programática en forma periódica (mes, trimestre, anual, etc.), derivados de los procesos administrativos que operan en tiempo real y que generan registros automáticos y por única vez', 85),
(111, 6, 65, 'B.4.2 Genera los Indicadores de Resultados', 86),
(112, 6, 66, 'B.4.3 Genera los Programas y Proyectos de Inversión', 87),
(113, 6, 67, 'C.1.1 Realiza el levantamiento físico del Inventario de Bienes Muebles', 88),
(114, 6, 68, 'C.1.2 Realiza el levantamiento físico del Inventario de Bienes Inmuebles', 89),
(115, 6, 69, 'C.1.3 El Inventario Físico de los Bienes Muebles e Inmuebles está debidamente conciliado con el registro contable', 90),
(116, 6, 70, 'C.1.4 Realiza el inventario físico de los bienes inalienables e imprescriptibles (monumentos arqueológicos, artísticos e históricos)', 91),
(117, 6, 71, 'C.1.5 Incluye dentro de 30 días hábiles en el Inventario Físico los Bienes Muebles que adquieren', 92),
(118, 7, 1, 'Catalogo Cuentas 2023', 1),
(119, 7, 2, 'Manual Contab Gaceta', 2),
(120, 7, 3, 'EGRESOS 062023', 3),
(121, 7, 4, 'EGRESOS 062023', 4),
(122, 7, 5, 'INGRESOS 062023', 5),
(123, 7, 6, 'INGRESOS 062023', 6),
(124, 7, 7, 'Reg Act bien muebles', 7),
(125, 7, 8, 'Reg Act bien inmuebles', 8),
(126, 7, 9, 'Bien inmueble val catast', 9),
(127, 7, 10, 'Reg baja muebles', 10),
(128, 7, 11, 'Reg baja inmuebles', 11),
(129, 7, 12, 'reg aux bie cust', 12),
(130, 7, 13, 'inv dom pu', 13),
(131, 7, 14, 'reg obr en proc', 14),
(132, 7, 15, 'reg act der patr', 15),
(133, 7, 16, 'EGRESOS 062023', 16),
(134, 7, 17, 'INGRESOS 062023', 17),
(135, 7, 18, 'DGP3041202306', 18),
(136, 7, 19, 'Balz comp Trim', 19),
(137, 7, 20, 'reg his sum prod', 20),
(138, 7, 21, 'reg hist alma sum cons', 21),
(139, 7, 22, 'Mant reg Inv BIen Mueb e Inm', 22),
(140, 7, 23, 'Mant reg lib balan', 23),
(141, 7, 24, 'cons prov', 24),
(142, 7, 25, 'reg prov', 25),
(143, 7, 26, 'Der proc de inven', 26),
(144, 7, 27, 'ECPI3041202306', 27),
(145, 7, 28, 'Exp edos finan deud', 28),
(146, 7, 29, 'EA3041202306', 29),
(147, 7, 30, 'ESF3041202306', 30),
(148, 7, 31, 'EVHP3041202306', 31),
(149, 7, 32, 'ECSF3041202306', 32),
(150, 7, 33, 'EFE3041202306', 33),
(151, 7, 34, 'inf sob pas cont', 34),
(152, 7, 35, 'NEF3041202306', 35),
(153, 7, 36, 'EAA3041202306', 36),
(154, 7, 37, 'EADOP3041202306', 37),
(155, 7, 38, 'PI30412023', 38),
(156, 7, 39, 'EAEPEClasificObjGasto', 39),
(157, 7, 40, 'clasificacioneconomicaportipodegasto', 40),
(158, 7, 41, 'clasificacionFuncional(FinalidadyFuncion)', 41),
(159, 7, 42, 'gastoporcategoriaprogramatica', 42),
(160, 7, 43, 'clasAdmin', 43),
(161, 7, 44, 'FuentesFinanciamiento', 44),
(162, 7, 45, '50. EGRESOS 062023', 45),
(163, 7, 51, 'INGR ESTIMADA', 46),
(164, 7, 52, '54.INGRESOS 062023', 47),
(165, 7, 55, 'Int conta pre deveng', 48),
(166, 7, 56, 'Proce emi finan', 49),
(167, 7, 57, 'EAI3041202306', 50),
(168, 7, 58, 'clasAdmin', 51),
(169, 7, 59, 'clasificacioneconomica', 52),
(170, 7, 60, 'EAEPEClasificObjGasto', 53),
(171, 7, 61, 'clasificacionFuncional(FinalidadyFuncion)', 54),
(172, 7, 62, 'end net', 55),
(173, 7, 63, 'inte deuda', 56),
(174, 7, 64, 'gastoporcategoriaprogramatica', 57),
(175, 7, 65, 'PbRM08b3041202306', 58),
(176, 7, 66, 'gen prog y proy inv', 59),
(177, 7, 67, 'Lev fisi inv b m', 60),
(178, 7, 68, 'IBI3041202306', 61),
(179, 7, 69, 'inv bien conc', 62),
(180, 7, 70, 'rea bien ina', 63),
(181, 7, 71, 'reg al mueb', 64),
(182, 7, 72, 'inv alta inmb', 65),
(183, 7, 73, 'reg ac ent rec', 66),
(184, 7, 74, 'Resultados lev inve.', 67),
(185, 7, 75, 'Cuentas inm', 68),
(186, 7, 76, 'Cuentas mueb', 69),
(187, 7, 77, 'ope pres', 70),
(188, 7, 78, 'coad cta pub', 71),
(189, 7, 79, 'pag elect', 72),
(190, 7, 81, 'EA3041202306', 73),
(191, 7, 82, 'ESF3041202306', 74),
(192, 7, 83, 'EVHP3041202306', 75),
(193, 7, 84, 'ECSF3041202306', 76),
(194, 7, 85, 'EFE3041202306', 77),
(195, 7, 86, 'inf pas cont', 78),
(196, 7, 87, 'NEF3041202306', 79),
(197, 7, 88, 'EAA3041202306', 80),
(198, 7, 89, 'EADOP3041202306', 81),
(199, 7, 90, 'EAI3041202306', 82),
(200, 7, 91, 'clasAdmin', 83),
(201, 7, 92, 'clasificacioneconomica', 84),
(202, 7, 93, 'EAEPEClasificObjGasto', 85),
(203, 7, 94, 'clasificacionFuncional(FinalidadyFuncion)', 86),
(204, 7, 95, 'pub end net', 87),
(205, 7, 96, 'pub int deu', 88),
(206, 7, 97, 'gastoporcategoriaprogramatica', 89),
(207, 7, 98, 'PbRM08b3041202306', 90),
(208, 7, 99, 'publ prog y proy inv', 91),
(209, 8, 1, 'Cuentas', 1),
(210, 8, 2, 'Cuentas (2)', 2),
(211, 8, 3, 'Cuentas (3)', 3),
(212, 8, 4, 'Cuentas (4)', 4),
(213, 8, 5, 'Cuentas (5)', 5),
(214, 8, 6, 'Cuentas (6)', 6),
(215, 8, 7, 'CTAS BIEN MUEB', 7),
(216, 8, 8, 'CTAS BIEN  INMU', 8),
(217, 8, 9, 'REGIS INMU', 9),
(218, 8, 10, 'BAJA_MUE', 10),
(219, 8, 11, 'BAJA-INM', 11),
(220, 8, 12, 'regi_bien_cus', 12),
(221, 8, 13, 'reg inve domi pub', 13),
(222, 8, 14, 'reg obras en proc', 14),
(223, 8, 15, '15 reg_dere_patri', 15),
(224, 8, 16, 'EGRESOS ', 16),
(225, 8, 17, 'INGRESOS ', 17),
(226, 8, 18, 'DR GRAL POL ', 18),
(227, 8, 19, 'BAL COM TRIM', 19),
(228, 8, 20, 'regist hist sum pro', 20),
(229, 8, 21, 'reg lib almac', 21),
(230, 8, 22, 'reg hist inven', 22),
(231, 8, 23, 'reg his ope bal', 23),
(232, 8, 24, 'const prov', 24),
(233, 8, 25, 'rev_provi', 25),
(234, 8, 26, 'IBM', 26),
(235, 8, 27, 'regi cont ingre', 27),
(236, 8, 28, 'edos finan pas deud', 28),
(237, 8, 29, 'EDO VAR HAC PUB ', 29),
(238, 8, 30, 'PID', 30),
(239, 8, 31, 'EAEPE', 31),
(240, 8, 32, 'EAEPECFLDF', 32),
(241, 8, 33, 'Programas', 33),
(242, 8, 34, 'EAEPECALDF', 34),
(243, 8, 35, 'FuentesFinanciamiento', 35),
(244, 8, 36, 'Cuentas (7)', 36),
(245, 8, 37, 'EGRESOS ', 37),
(246, 8, 38, 'INGRESOS ', 38),
(247, 8, 39, 'Lev Fisico Inventario', 39),
(248, 8, 40, 'IBI', 40),
(249, 8, 41, 'conci inventario', 41),
(250, 8, 42, 'inv_bien_ina', 42),
(251, 8, 43, 'inv trim bm', 43),
(252, 8, 44, 'inv tri bi', 44),
(253, 8, 45, 'trans_bien', 45),
(254, 8, 46, 'Resultados lev inve.', 46),
(255, 8, 47, 'Cuentas (8)', 47),
(256, 8, 48, 'Cuentas (9)', 48),
(257, 8, 49, 'con ope pres', 49),
(258, 8, 50, 'OFIC CTA PUB', 50),
(259, 8, 51, 'PAGO ELECTRO', 51),
(260, 8, 51, 'EDO ACT ', 52),
(261, 8, 53, 'EDOSIT', 53),
(262, 8, 54, 'EDO CAM SIT FIN', 54),
(263, 8, 55, 'EDO FLU EFE ', 55),
(264, 8, 56, 'inf_pas_cont', 56),
(265, 8, 57, 'NOTAS EDO FIN ', 57),
(266, 8, 58, 'EDO ANA ACT', 58),
(267, 8, 59, 'EDO ANA DEU Y OTR PAS ', 59),
(268, 8, 60, 'EAI', 60),
(269, 8, 61, 'clasAdmin', 61),
(270, 8, 62, 'clasificacioneconomicaportipodegasto', 62),
(271, 8, 63, 'EAEPEClasificObjGasto', 63),
(272, 8, 64, 'clasificacionFuncional(FinalidadyFunci', 64),
(273, 8, 65, 'end net', 65),
(274, 8, 66, 'int deu', 66),
(275, 8, 67, 'gastoporcategoriaprogramatica', 67),
(276, 8, 68, 'PbRM-08b', 68),
(277, 8, 69, 'prog_proy_inv', 69),
(278, 9, 1, 'Ayudas y subsidios', 1),
(279, 9, 2, 'Formato del ejercicio y destino de gasto federalizado y reintegros', 2),
(280, 9, 3, 'Relacion de Bienes que Componen el Patrimonio del Ente Publico', 3),
(281, 9, 4, 'Formato de Programas con Recursos Concurrente por Orden de Gobierno', 4),
(282, 9, 5, 'Relación de bienes que componen su patrimonio', 5),
(283, 9, 6, 'Calendario de Ingresos del Ejercicio Fiscal', 6),
(284, 9, 7, 'Calendario de Presupuesto de Egresos del Ejercicio Fiscal', 7),
(285, 9, 8, 'Formatos Anuales DIF ciuda', 8),
(286, 9, 9, 'Iniciativa de Ley de Ingresos para el Ejercicio Fiscal', 9),
(287, 9, 10, 'Presupuesto de Egresos para el Ejercicio Fiscal ', 10),
(288, 9, 11, 'Relación de cuentas bancarias productivas específicas', 11),
(289, 9, 12, ' ', 12),
(290, 9, 1, 'Cuentas', 13),
(291, 9, 2, 'Manual de Planeación y Presupuesto 2022', 14),
(292, 9, 3, 'Polizas de Ingresos', 15),
(293, 9, 4, 'Polizas de Egresos', 16),
(294, 9, 5, 'INGRESOS 122021', 17),
(295, 9, 6, 'INGRESOS 122021', 18),
(296, 9, 7, 'DIARIO 082021', 19),
(297, 9, 8, 'Registro de la donación de bienes inmuebles ', 20),
(298, 9, 9, 'I N V EN T A R I O D E B I E N E S I N M U E B L E S', 21),
(299, 9, 10, 'Invetario de bienes muebles dados de baja', 22),
(300, 9, 11, 'Baja de inmuebles ', 23),
(301, 9, 12, 'Reg_ bien_cust', 24),
(302, 9, 13, 'Inv_dom', 25),
(303, 9, 14, 'Obras en proceso', 26),
(304, 9, 15, 'Activo de derechos patrimoniales', 27),
(305, 9, 16, 'CLASIFICACION POR OBJETO DE GASTO (CAPITULO Y CONCEPTO', 28),
(306, 9, 17, 'ESTADO ANALÍTICO DE INGRESOS', 29),
(307, 9, 18, 'DIARIO GENERAL DE PÓLIZAS', 30),
(308, 9, 19, 'ANEXO AL ESTADO DE SITUACION FINANCIERA', 31),
(309, 9, 20, 'Registro historico materias primas', 32),
(310, 9, 21, 'Registro historico de consumos', 33),
(311, 9, 22, 'Registro historico de inventario de inmuebles', 34),
(312, 9, 23, 'Registro historico de operaciones balances', 35),
(313, 9, 24, 'Constituye  Proviciones', 36),
(314, 9, 25, 'Revision de proviciones', 37),
(315, 9, 26, 'Segundo Levantamiento de inmuebles', 38),
(316, 9, 27, 'Avance Presupuestal Egresos Global', 39),
(317, 9, 28, 'ESTADO COMPARATIVO PRESUPUESTAL DE INGRESOS', 40),
(318, 9, 29, 'Clasificación por Objeto del Gasto (Capítulo y concepto)', 41),
(319, 9, 30, 'CLASIFICACIÓN ECONÓMICA (por Tipo de Gasto)', 42),
(320, 9, 31, 'Estado Análitico del Ejercicio del Presupuesto de Egresos Detallado', 43),
(321, 9, 32, 'GASTO POR CATEGORÍA PROGRAMÁTICA', 44),
(322, 9, 33, 'Estado Análitico del Ejercicio del Presupuesto de Egresos Detallado - LDF', 45),
(323, 9, 34, 'Mantenimiento al Catalogo de Flujo', 46),
(324, 9, 35, 'EGRESOS 122021', 47),
(325, 9, 36, 'EGRESOS 122021', 48),
(326, 9, 37, 'EGRESOS 122021', 49),
(327, 9, 38, 'EGRESOS 122021', 50),
(328, 9, 39, 'EGRESOS 122021', 51),
(329, 9, 40, 'EGRESOS 122021', 52),
(330, 9, 41, 'INGRESOS 122021', 53),
(331, 9, 42, 'INGRESOS 122021', 54),
(332, 9, 43, 'INGRESOS 122021', 55),
(333, 9, 44, 'INGRESOS 122021', 56),
(334, 9, 45, 'EGRESOS 122021', 57),
(335, 9, 46, 'Lev. Fisico', 58),
(336, 9, 47, 'Lev FIsic inm', 59),
(337, 9, 48, 'inv fis conc', 60),
(338, 9, 49, 'bien_Ina', 61),
(339, 9, 50, 'R E P O R T E M E N S U A L D E M O V I M I E N T O S D E B I E N E S M U E B L E S', 62),
(340, 9, 51, 'R E P O R T E M E N S U A L D E M O V I M I E N T O S D E B I E N E S I N M U E B L E S RMMBI3041202112', 63),
(341, 9, 52, 'trans_adm', 64),
(342, 9, 53, 'Tran_bien_rec', 65),
(343, 9, 54, 'Cuentas Inm', 66),
(344, 9, 55, 'Cuentas mueb', 67),
(345, 9, 56, 'poliegre', 68),
(346, 9, 57, 'OFICIO CTA PUBL 20', 69),
(347, 9, 58, 'transf', 70),
(348, 10, 1, 'Ayudas y subsidios', 1),
(349, 10, 2, 'Formato del ejercicio y destino de gasto federalizado y reintegros', 2),
(350, 10, 3, 'Relacion de Bienes que Componen el Patrimonio del Ente Publico', 3),
(351, 10, 4, 'Formato de Programas con Recursos Concurrente por Orden de Gobierno', 4),
(352, 10, 5, 'Definición de Resultados de Evaluaciones de Recursos Federales', 5),
(353, 10, 6, 'Norma para Armonizar la Presentación de la Información Adicional a la Iniciativa de la Ley de Ingresos', 6),
(354, 10, 7, 'Norma para Armonizar la Presentación de la Información Adicional del Proyecto del Presupuesto de Egresos', 7),
(355, 10, 8, 'Norma para establecer la estructura de información de la relación de cuentas bancarias', 8),
(356, 10, 9, 'Norma para establecer la estructura del calendario de ingresos base mensual', 9),
(357, 10, 10, 'Norma para establecer la estructura del calendario del presupuesto de egresos base mensual', 10),
(358, 10, 11, 'Norma para la difución a la ciudadanía de la ley de ingresos y del presupuesto de egresos', 11),
(359, 10, 12, 'Difusión de resultados', 12),
(360, 10, 13, 'Estado Análitico del Ejercicio del Presupuesto de Egresos Detallado', 13),
(361, 10, 14, 'Formato de información de obligaciones pagadas o garantizadas con fondos federales', 14),
(362, 10, 15, 'Estado de cambios en la situación financiera', 15),
(363, 10, 16, 'Notas a los estados financieros', 16),
(364, 10, 17, 'Estado analitico del activo', 17),
(365, 10, 18, 'Estado analitico de ingresos', 18),
(366, 10, 19, 'Clasificacion Administrativa (por Tipo de Gasto)', 19),
(367, 10, 20, 'Clasificacion por Objecto del Gasto (Capitulo y Concepto)', 20),
(368, 10, 21, 'Clasificación economica (por Tipo de Gasto)', 21),
(369, 10, 22, 'Gasto por Categoria Programatica', 22),
(370, 10, 23, 'Relación de bienes que componen su patrimonio', 23),
(371, 10, 24, 'Formato del ejercicio y destino de gasto federalizado y reintegros', 24),
(372, 11, 1, 'Ayudas y subsidios', 1),
(373, 11, 2, 'Formato del ejercicio y destino de gasto federalizado y reintegros', 2),
(374, 11, 3, 'Relacion de Bienes que Componen el Patrimonio del Ente Publico', 3),
(375, 11, 4, 'Formato de Programas con Recursos Concurrente por Orden de Gobierno', 4),
(376, 11, 5, 'Definición de Resultados de Evaluaciones de Recursos Federales', 5),
(377, 11, 6, 'Norma para Armonizar la Presentación de la Información Adicional a la Iniciativa de la Ley de Ingresos', 6),
(378, 11, 7, 'Norma para Armonizar la Presentación de la Información Adicional del Proyecto del Presupuesto de Egresos', 7),
(379, 11, 8, 'Norma para establecer la estructura de información de la relación de cuentas bancarias', 8),
(380, 11, 9, 'Norma para establecer la estructura del calendario de ingresos base mensual', 9),
(381, 11, 10, 'Norma para establecer la estructura del calendario del presupuesto de egresos base mensual', 10),
(382, 11, 11, 'Norma para la difución a la ciudadanía de la ley de ingresos y del presupuesto de egresos', 11),
(383, 11, 12, 'Difusión de resultados', 12),
(384, 11, 13, 'Formato de información de obligaciones pagadas o garantizadas con fondos federales', 13),
(385, 11, 14, 'Estado de Actividades', 14),
(386, 11, 15, 'Estado de situación financiera', 15),
(387, 11, 16, 'Estado de variación en la hacienda pública/patrimonio', 16),
(388, 11, 17, 'Estado de cambios en la situación financiera', 17),
(389, 11, 18, 'Estado de flujos de efectivo', 18),
(390, 11, 19, 'Informe sobre pasivos contingentes', 19),
(391, 11, 20, 'Notas a los estados financieros', 20),
(392, 11, 21, 'Estado analitico del activo', 21),
(393, 11, 22, 'Estado analitico de la deuda y otros pasivos', 22),
(394, 11, 23, 'Estado analitico de ingresos', 23),
(395, 11, 24, 'Clasificacion Administrativa', 24),
(396, 11, 25, 'Clasificacion por Objecto del Gasto (Capitulo y Concepto)', 25),
(397, 11, 26, 'Clasificación economica (por Tipo de Gasto)', 26),
(398, 11, 27, 'Clasificacion Funcional (Finalidad y Funcion)', 27),
(399, 11, 28, 'Endeudamiento neto', 28),
(400, 11, 29, 'Intereses de la deuda', 29),
(401, 11, 30, 'Gasto por Categoria Programatica', 30),
(402, 11, 31, 'Programas y proyectos de inversion', 31),
(403, 12, 1, 'Presupuesto de Egresos para el Ejercicio Fiscal 2021', 1),
(404, 12, 2, 'Iniciativa de Ley de Ingresos para el Ejercicio Fiscal 2021', 2),
(405, 12, 3, 'Calendario de Presupuesto de Egresos del Ejercicio Fiscal 2021', 3),
(406, 12, 4, 'Calendario de Ingresos del Ejercicio Fiscal 2021', 4),
(407, 12, 5, 'Relación de cuentas bancarias productivas específicas Periodo (anual)', 5),
(408, 12, 6, 'Formatos anuales dif ciudanos', 6),
(409, 12, 7, 'Montos pagados por ayudas y subsidios', 7),
(410, 12, 8, 'Relación de bienes que componen su patrimonio', 8),
(411, 12, 9, 'Formato del ejercicio y destino de gasto federalizado y reintegros', 9),
(412, 12, 10, 'Formato de programas con recursos concurrente por orden de gobierno', 10),
(413, 12, 1, 'Estado de situacion financiera', 11),
(414, 12, 2, 'Estado de actividades', 12),
(415, 12, 3, 'Estado de variación en la hacienda publica', 13),
(416, 12, 4, 'Estado de cambios en la situacion financiera', 14),
(417, 12, 5, 'Pasivos Contingentes', 15),
(418, 12, 6, 'Notas a los Estados Financieros', 16),
(419, 12, 7, 'Estado Analitico del Activo', 17),
(420, 12, 8, 'Estado Flujo de Efectivo', 18),
(421, 12, 9, 'Estado Analitico de Deuda y Otros Pasivos', 19),
(422, 12, 10, 'Endeudamiento Neto', 20),
(423, 12, 11, 'Intereses de la Deuda', 21);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seac_pdfs`
--

CREATE TABLE `seac_pdfs` (
  `id` int(11) NOT NULL,
  `bloque_id` int(11) NOT NULL,
  `concepto_id` int(11) NOT NULL,
  `trimestre` tinyint(4) NOT NULL COMMENT '1-4',
  `pdf_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `seac_pdfs`
--

INSERT INTO `seac_pdfs` (`id`, `bloque_id`, `concepto_id`, `trimestre`, `pdf_path`) VALUES
(13, 5, 7, 1, 'PDF SEAC/2025/20250508_221252_]1.Estado de Situacion Financiera.pdf'),
(14, 5, 7, 2, 'PDF SEAC/2025/20250902_180003_]1.Estado de Situacion Financiera.pdf'),
(15, 5, 7, 3, 'PDF SEAC/2025/20251110_120803_]1.Estado de Situacion Financiera.pdf'),
(16, 5, 8, 1, 'PDF SEAC/2025/20250508_223803_]2.Estado de Actividades.pdf'),
(17, 5, 8, 2, 'PDF SEAC/2025/20250819_115504_]2.Estado de Actividades.pdf'),
(18, 5, 8, 3, 'PDF SEAC/2025/20251110_120917_]2.Estado de Actividades.pdf'),
(19, 5, 9, 1, 'PDF SEAC/2025/20250508_224017_]3.Estado Analitico de Activo.pdf'),
(20, 5, 9, 2, 'PDF SEAC/2025/20250819_115618_]3.Estado Analitico de Activo.pdf'),
(21, 5, 9, 3, 'PDF SEAC/2025/20251110_121012_]3.Estado Analitico de Activo.pdf'),
(22, 5, 10, 1, 'PDF SEAC/2025/20250508_224259_]4.Estado analitico de la Deuda y otros pasivos.pdf'),
(23, 5, 10, 2, 'PDF SEAC/2025/20250819_115812_]4.Estado analitico de la Deuda y otros pasivos.pdf'),
(24, 5, 10, 3, 'PDF SEAC/2025/20251110_121034_]4.Estado Analitico de la Deuda.pdf'),
(25, 5, 11, 1, 'PDF SEAC/2025/20250508_225154_]5.Estado de Cambios en la Situacion Financiera.pdf'),
(26, 5, 11, 2, 'PDF SEAC/2025/20250819_115920_]5.Estado de Cambios en la Situacion Financiera.pdf'),
(27, 5, 11, 3, 'PDF SEAC/2025/20251128_152508_]5.Estado de Cambios en la Situacion Financiera.pdf'),
(28, 5, 12, 1, 'PDF SEAC/2025/20250508_225401_]6.Estado de Flujos de Efectivo.pdf'),
(29, 5, 12, 2, 'PDF SEAC/2025/20250819_120018_]6.Estado de Flujos de Efectivo.pdf'),
(30, 5, 12, 3, 'PDF SEAC/2025/20251110_121133_]6. Estado de Flujo de Efectivo.pdf'),
(31, 5, 13, 1, 'PDF SEAC/2025/20250508_225522_]7.Estado de variacion en la Hacienda Publica.pdf'),
(32, 5, 13, 2, 'PDF SEAC/2025/20250819_120116_]7.Estado de variacion en la Hacienda Publica.pdf'),
(33, 5, 13, 3, 'PDF SEAC/2025/20251110_121201_]7. Estado de Variacion de la Deuda Publica.pdf'),
(34, 5, 14, 1, 'PDF SEAC/2025/20250508_225603_]8.Notas a los Estados Financieros.pdf'),
(35, 5, 14, 2, 'PDF SEAC/2025/20250819_120245_]8.Notas a los Estados Financieros.pdf'),
(36, 5, 14, 3, 'PDF SEAC/2025/20251110_121324_]8, Notas a los Estados Financieros.pdf'),
(37, 5, 15, 1, 'PDF SEAC/2025/20250527_164320_]9.Estado Analitico de Egresos Clasificacion por Objeto del gasto.pdf'),
(38, 5, 15, 2, 'PDF SEAC/2025/20250819_120415_]9.Estado Analitico de Egresos Clasificacion por Objeto del gasto.pdf'),
(39, 5, 15, 3, 'PDF SEAC/2025/20251110_121411_]9.Estado Analitico de Egresos Clasificacion por Objeto del gasto.pdf'),
(40, 5, 16, 1, 'PDF SEAC/2025/20250527_163951_]10.Estado Analitico de Egresos Clasificacion Administrativa.pdf'),
(41, 5, 16, 2, 'PDF SEAC/2025/20250819_120519_]10.Estado Analitico de Egresos Clasificacion Administrativa.pdf'),
(42, 5, 16, 3, 'PDF SEAC/2025/20251110_121609_]10.Estado Analitico de Egresos Clasificacion Administrativa.pdf'),
(43, 5, 17, 1, 'PDF SEAC/2025/20250527_163643_]11.Estado Analitico de Egresos Clasificacion Funcional.pdf'),
(44, 5, 17, 2, 'PDF SEAC/2025/20250902_180201_]11.Estado Analitico de Egresos Clasificacion Funcional.pdf'),
(45, 5, 17, 3, 'PDF SEAC/2025/20251110_123229_]11.Estado Analitico de Egresos Clasificacion Funcional.pdf'),
(46, 5, 18, 1, 'PDF SEAC/2025/20250527_163208_]12.Estado Analitico de Egresos Clasificacion Programatica.pdf'),
(47, 5, 18, 2, 'PDF SEAC/2025/20250819_120750_]12.Estado Analitico de Egresos Clasificacion Programatica.pdf'),
(48, 5, 18, 3, 'PDF SEAC/2025/20251110_123617_]12.Estado Analitico de Egresos Clasificacion Programatica.pdf'),
(49, 5, 19, 1, 'PDF SEAC/2025/20250527_162606_]14.Estado Analitico de Egresos Clasificacion Economica.pdf'),
(50, 5, 19, 2, 'PDF SEAC/2025/20250819_120852_]13.Estado Analitico de Egresos Clasificacion Economica.pdf'),
(51, 5, 19, 3, 'PDF SEAC/2025/20251110_124438_]13.Estado Analitico de Egresos Clasificacion Economica.pdf'),
(52, 5, 20, 1, 'PDF SEAC/2025/20250508_232614_]14.Estado Analitico de Ingresos.pdf'),
(53, 5, 20, 2, 'PDF SEAC/2025/20250819_120950_]14. Estado Analitico de Ingresos.pdf'),
(54, 5, 20, 3, 'PDF SEAC/2025/20251110_124509_]14. Estado Analitico de Ingresos.pdf'),
(55, 5, 21, 1, 'PDF SEAC/2025/20250508_232755_]15.Pasivos contigentes .pdf'),
(56, 5, 21, 2, 'PDF SEAC/2025/20250819_121052_]15.Pasivos contigentes.pdf'),
(57, 5, 21, 3, 'PDF SEAC/2025/20251110_124532_]15.Pasivos contigentes.pdf'),
(58, 5, 22, 1, 'PDF SEAC/2025/20250508_232932_]16.Ayudas y Subsidios.pdf'),
(59, 5, 22, 2, 'PDF SEAC/2025/20250819_121148_]16. Ayudas y subsidio.pdf'),
(60, 5, 22, 3, 'PDF SEAC/2025/20251110_124555_]16. Ayudas y subsidio.pdf'),
(61, 5, 23, 2, 'PDF SEAC/2025/20250819_121239_]Inventario de Bienes Inmuebles.pdf'),
(62, 5, 24, 2, 'PDF SEAC/2025/20250819_121339_]Inventario de Bienes Muebles.pdf'),
(63, 5, 25, 1, 'PDF SEAC/2025/20250508_233224_]PbRM08b3041202503.pdf'),
(64, 5, 25, 2, 'PDF SEAC/2025/20250902_180405_]PbRM08b3041202506.pdf'),
(65, 5, 25, 3, 'PDF SEAC/2025/20251110_124713_]PbRM08b3041202509.pdf'),
(66, 6, 26, 1, 'PDF SEAC/2024/1. Ayudas y Subsidios.pdf'),
(67, 6, 26, 2, 'PDF SEAC/2024/20240812_165658_]1. Cuentas.pdf'),
(68, 6, 26, 3, 'PDF SEAC/2024/20250508_163315_]Ayudas y subsidio.pdf'),
(69, 6, 27, 1, 'PDF SEAC/2024/2. EA3041202403.pdf'),
(70, 6, 27, 2, 'PDF SEAC/2024/20240812_193315_]2. manual de contabilidad.pdf'),
(71, 6, 27, 3, 'PDF SEAC/2024/20241031_075436_]2. EA3041202409.pdf'),
(72, 6, 28, 1, 'PDF SEAC/2024/3. ESF3041202403.pdf'),
(73, 6, 28, 2, 'PDF SEAC/2024/20240812_193450_]3.egresos 1.pdf'),
(74, 6, 28, 3, 'PDF SEAC/2024/20250508_171312_]ESF3041202412.pdf'),
(75, 6, 29, 1, 'PDF SEAC/2024/4. EVHP3041202403.pdf'),
(76, 6, 29, 2, 'PDF SEAC/2024/20240812_193801_]4. egresos 1.pdf'),
(77, 6, 29, 3, 'PDF SEAC/2024/20250508_171715_]EVHP3041202412.pdf'),
(78, 6, 30, 1, 'PDF SEAC/2024/5. ECSF3041202403.pdf'),
(79, 6, 30, 2, 'PDF SEAC/2024/20240812_193959_]5. ingresos 1.pdf'),
(80, 6, 30, 3, 'PDF SEAC/2024/20250508_172115_]ECSF3041202412.pdf'),
(81, 6, 31, 1, 'PDF SEAC/2024/6. EFE3041202403.pdf'),
(82, 6, 31, 2, 'PDF SEAC/2024/20240812_194107_]6. ingresos 1.pdf'),
(83, 6, 31, 3, 'PDF SEAC/2024/20250508_172336_]EFE3041202412.pdf'),
(84, 6, 32, 1, 'PDF SEAC/2024/7. PASIVOS CONTING.pdf'),
(85, 6, 32, 2, 'PDF SEAC/2024/20240812_194145_]7. regis bien muebles.pdf'),
(86, 6, 32, 3, 'PDF SEAC/2024/20250508_172537_]Pasivos contigentes .pdf'),
(87, 6, 33, 1, 'PDF SEAC/2024/8. NEF3041202403.pdf'),
(88, 6, 33, 2, 'PDF SEAC/2024/20240812_194236_]8. reg bienes inmuebles.pdf'),
(89, 6, 33, 3, 'PDF SEAC/2024/20250508_172944_]NEF3041202412.pdf'),
(90, 6, 34, 1, 'PDF SEAC/2024/9. EAA3041202403.pdf'),
(91, 6, 34, 2, 'PDF SEAC/2024/20240812_194346_]9. reg bienes inmuebles catastral.pdf'),
(92, 6, 34, 3, 'PDF SEAC/2024/20250508_173151_]EAA3041202412.pdf'),
(93, 6, 35, 1, 'PDF SEAC/2024/10. EADOP3041202403.pdf'),
(94, 6, 35, 2, 'PDF SEAC/2024/20241031_080105_]10. EADOP3041202409.pdf'),
(95, 6, 35, 3, 'PDF SEAC/2024/20250508_173353_]EADOP3041202412.pdf'),
(96, 6, 36, 1, 'PDF SEAC/2024/11. EAI3041202403.pdf'),
(97, 6, 36, 2, 'PDF SEAC/2024/20240812_194520_]11. baja inmuebles.pdf'),
(98, 6, 37, 1, 'PDF SEAC/2024/12. Clas Administrativa.pdf'),
(99, 6, 37, 2, 'PDF SEAC/2024/20240812_194602_]12. biene bajo cust.pdf'),
(100, 6, 37, 3, 'PDF SEAC/2024/20250508_173555_]Clasificacion Administrativa.pdf'),
(101, 6, 38, 1, 'PDF SEAC/2024/13. Clas Econom.pdf'),
(102, 6, 38, 2, 'PDF SEAC/2024/20240812_195001_]13. inv dom pub.pdf'),
(103, 6, 38, 3, 'PDF SEAC/2024/20250508_174438_]Clasificacion Economica.pdf'),
(104, 6, 39, 1, 'PDF SEAC/2024/14. Clas Obj Gsto.pdf'),
(105, 6, 39, 2, 'PDF SEAC/2024/20240812_195057_]14. obra en proc.pdf'),
(106, 6, 39, 3, 'PDF SEAC/2024/20250508_174652_]Clasificacion Objeto del gasto.pdf'),
(107, 6, 40, 1, 'PDF SEAC/2024/15. Clas Func.pdf'),
(108, 6, 40, 2, 'PDF SEAC/2024/20240812_195157_]15. act fidecim.pdf'),
(109, 6, 40, 3, 'PDF SEAC/2024/20250508_174902_]Clasificacion Funcional.pdf'),
(110, 6, 41, 1, 'PDF SEAC/2024/16. ENDEUDAMIENTO NETO.pdf'),
(111, 6, 41, 2, 'PDF SEAC/2024/20240812_195300_]16. egresos.pdf'),
(112, 6, 42, 1, 'PDF SEAC/2024/17. IDP3041202403.pdf'),
(113, 6, 42, 2, 'PDF SEAC/2024/20240812_195357_]17. ingresos.pdf'),
(114, 6, 43, 1, 'PDF SEAC/2024/18. Gsto Cat Progra.pdf'),
(115, 6, 43, 2, 'PDF SEAC/2024/20240812_195522_]18. DGP3041202406.pdf'),
(116, 6, 43, 3, 'PDF SEAC/2024/20250508_175203_]Clasificacion Programatica.pdf'),
(117, 6, 44, 1, 'PDF SEAC/2024/19. PbRM08b3041202403.pdf'),
(118, 6, 44, 2, 'PDF SEAC/2024/20240812_195615_]19. balan detallada.pdf'),
(119, 6, 44, 3, 'PDF SEAC/2024/20250508_220922_]PbRM08b3041202412.pdf'),
(120, 6, 45, 1, 'PDF SEAC/2024/20. PROG Y PROY INVERSION.pdf'),
(121, 6, 45, 2, 'PDF SEAC/2024/20240812_195713_]20. opr mat prim.pdf'),
(122, 6, 46, 1, 'PDF SEAC/2024/21. manual de contabilidad.pdf'),
(123, 6, 46, 2, 'PDF SEAC/2024/20240812_195801_]21. regis almacen.pdf'),
(144, 6, 68, 2, 'PDF SEAC/2024/20240812_200118_]22. man reg inv bienes.pdf'),
(145, 6, 69, 2, 'PDF SEAC/2024/20240812_200225_]23. libro balances.pdf'),
(146, 6, 70, 2, 'PDF SEAC/2024/20240812_200343_]24. const prov.pdf'),
(147, 6, 72, 2, 'PDF SEAC/2024/20240812_200600_]26. trans admis 1.pdf'),
(148, 6, 73, 2, 'PDF SEAC/2024/20240812_200705_]27. ECPI3041202406.pdf'),
(149, 6, 74, 2, 'PDF SEAC/2024/20240812_200816_]28. edos finan deuda.pdf'),
(150, 6, 75, 2, 'PDF SEAC/2024/20240812_200859_]29. EA3041202406.pdf'),
(151, 6, 76, 2, 'PDF SEAC/2024/20240812_200946_]30. ESF3041202406.pdf'),
(152, 6, 77, 2, 'PDF SEAC/2024/20240812_205415_]31. EVHP3041202406 1.pdf'),
(153, 6, 78, 2, 'PDF SEAC/2024/20240812_205506_]32. ECSF3041202406.pdf'),
(154, 6, 79, 2, 'PDF SEAC/2024/20240812_205538_]33. EFE3041202406.pdf'),
(155, 6, 80, 2, 'PDF SEAC/2024/20240812_205625_]34. pasiv cont.pdf'),
(156, 6, 81, 2, 'PDF SEAC/2024/20240812_205704_]35.NEF3041202406.pdf'),
(157, 6, 82, 2, 'PDF SEAC/2024/20240812_205747_]36. EAA3041202406 1.pdf'),
(158, 6, 83, 2, 'PDF SEAC/2024/20240812_205819_]37. EADOP3041202406.pdf'),
(159, 6, 84, 2, 'PDF SEAC/2024/20240812_210109_]38. PID30412024.pdf'),
(160, 6, 85, 2, 'PDF SEAC/2024/20240812_210149_]39. AEPECOG3041202406.pdf'),
(161, 6, 86, 2, 'PDF SEAC/2024/20240812_210221_]40. clas econo 1.pdf'),
(162, 6, 87, 2, 'PDF SEAC/2024/20240812_210250_]41. clas func 1.pdf'),
(163, 6, 88, 2, 'PDF SEAC/2024/20240812_210321_]42. gsto programa 1.pdf'),
(164, 6, 89, 2, 'PDF SEAC/2024/20240812_210355_]43. clas admini 1.pdf'),
(165, 6, 90, 2, 'PDF SEAC/2024/20240812_210440_]44. FuentesFinanciamiento 1.pdf'),
(166, 6, 91, 2, 'PDF SEAC/2024/20240813_124031_]45-50. egresos.pdf'),
(167, 6, 92, 2, 'PDF SEAC/2024/20240813_124057_]45-50. egresos.pdf'),
(168, 6, 93, 2, 'PDF SEAC/2024/20240813_124110_]45-50. egresos.pdf'),
(169, 6, 94, 2, 'PDF SEAC/2024/20240813_124126_]45-50. egresos.pdf'),
(170, 6, 95, 2, 'PDF SEAC/2024/20240813_124140_]45-50. egresos.pdf'),
(171, 6, 96, 2, 'PDF SEAC/2024/20240812_210518_]45-50. egresos.pdf'),
(172, 6, 97, 2, 'PDF SEAC/2024/20240813_125137_]51-54. ingresos.pdf'),
(173, 6, 98, 2, 'PDF SEAC/2024/20240813_125151_]51-54. ingresos.pdf'),
(174, 6, 99, 2, 'PDF SEAC/2024/20240813_125204_]51-54. ingresos.pdf'),
(175, 6, 100, 2, 'PDF SEAC/2024/20240813_125218_]51-54. ingresos.pdf'),
(176, 6, 101, 2, 'PDF SEAC/2024/20240813_125435_]55. inte gsto deven 1.pdf'),
(177, 6, 102, 2, 'PDF SEAC/2024/20240813_125510_]56. estados finan.pdf'),
(178, 6, 103, 2, 'PDF SEAC/2024/20240813_125528_]57. EAI3041202406.pdf'),
(179, 6, 104, 2, 'PDF SEAC/2024/20240813_125547_]58. clas admini 1.pdf'),
(180, 6, 105, 2, 'PDF SEAC/2024/20240813_125620_]59. clas econo 1.pdf'),
(181, 6, 106, 2, 'PDF SEAC/2024/20240813_125641_]60. clas ob gsto.pdf'),
(182, 6, 107, 2, 'PDF SEAC/2024/20240813_125656_]61. clas func 1.pdf'),
(183, 6, 108, 2, 'PDF SEAC/2024/20240813_125709_]62. ende neto 1.pdf'),
(184, 6, 109, 2, 'PDF SEAC/2024/20240813_125723_]63. int deuda 1.pdf'),
(185, 6, 110, 2, 'PDF SEAC/2024/20240813_125736_]64. gsto programa 1.pdf'),
(186, 6, 111, 2, 'PDF SEAC/2024/20240813_125843_]65. PbRM08b3041202406.pdf'),
(187, 6, 112, 2, 'PDF SEAC/2024/20240813_125909_]66. prog y pro inver 1.pdf'),
(188, 6, 113, 2, 'PDF SEAC/2024/20240813_125931_]67. LEVAN FIS BM.pdf'),
(189, 6, 114, 2, 'PDF SEAC/2024/20240813_130915_]68. IBI3041202406.pdf'),
(190, 6, 115, 2, 'PDF SEAC/2024/20240813_130939_]69. INV FIS CON.pdf'),
(191, 6, 116, 2, 'PDF SEAC/2024/20240813_131000_]70. biene inalienables 1.pdf'),
(192, 7, 118, 2, 'PDF SEAC/2023/1. Catalogo Cuentas 2023.pdf'),
(193, 7, 119, 2, 'PDF SEAC/2023/2. Manual Contab Gaceta.pdf'),
(194, 7, 120, 2, 'PDF SEAC/2023/3. EGRESOS 062023.pdf'),
(195, 7, 121, 2, 'PDF SEAC/2023/4. EGRESOS 062023.pdf'),
(196, 7, 122, 2, 'PDF SEAC/2023/5.INGRESOS 062023.pdf'),
(197, 7, 123, 2, 'PDF SEAC/2023/6. INGRESOS 062023.pdf'),
(198, 7, 124, 2, 'PDF SEAC/2023/7. Reg Act bien muebles.pdf'),
(199, 7, 125, 2, 'PDF SEAC/2023/8. Reg Act bien inmuebles.pdf'),
(200, 7, 126, 2, 'PDF SEAC/2023/10. Reg baja muebles.pdf'),
(201, 7, 127, 2, 'PDF SEAC/2023/10. Reg baja muebles.pdf'),
(202, 7, 128, 2, 'PDF SEAC/2023/11. Reg baja inmuebles.pdf'),
(203, 7, 129, 2, 'PDF SEAC/2023/12.reg aux bie cust.pdf'),
(204, 7, 130, 2, 'PDF SEAC/2023/13. inv dom pu.pdf'),
(205, 7, 131, 2, 'PDF SEAC/2023/14. reg obr en proc.pdf'),
(206, 7, 132, 2, 'PDF SEAC/2023/15. reg act der patr.pdf'),
(207, 7, 133, 2, 'PDF SEAC/2023/16. EGRESOS 062023.pdf'),
(208, 7, 134, 2, 'PDF SEAC/2023/17.INGRESOS 062023.pdf'),
(209, 7, 135, 2, 'PDF SEAC/2023/18.DGP3041202306.pdf'),
(210, 7, 136, 2, 'PDF SEAC/2023/19. Balz comp Trim.pdf'),
(211, 7, 137, 2, 'PDF SEAC/2023/20. reg his sum prod.pdf'),
(212, 7, 138, 2, 'PDF SEAC/2023/21. reg hist alma sum cons.pdf'),
(213, 7, 139, 2, 'PDF SEAC/2023/22. Mant reg Inv BIen Mueb e Inm.pdf'),
(214, 7, 140, 2, 'PDF SEAC/2023/23. Mant reg lib balan.pdf'),
(215, 7, 141, 2, 'PDF SEAC/2023/24. cons prov.pdf'),
(216, 7, 142, 2, 'PDF SEAC/2023/25.reg prov.pdf'),
(217, 7, 143, 2, 'PDF SEAC/2023/26. Der proc de inven.pdf'),
(218, 7, 144, 2, 'PDF SEAC/2023/27. ECPI3041202306.pdf'),
(219, 7, 145, 2, 'PDF SEAC/2023/28. Exp edos finan deud.pdf'),
(220, 7, 146, 1, 'PDF SEAC/2023/2. EDO ACT 032023.pdf'),
(221, 7, 146, 2, 'PDF SEAC/2023/29.EA3041202306.pdf'),
(222, 7, 146, 3, 'PDF SEAC/2023/20231030_154657_]2.EA3041202309.pdf'),
(223, 7, 147, 1, 'PDF SEAC/2023/3. EDOSIT032023.pdf'),
(224, 7, 147, 2, 'PDF SEAC/2023/30.ESF3041202306.pdf'),
(225, 7, 147, 3, 'PDF SEAC/2023/20231030_154730_]3.ESF3041202309.pdf'),
(226, 7, 148, 1, 'PDF SEAC/2023/4. EDO VAR HAC PUB 032023.pdf'),
(227, 7, 148, 2, 'PDF SEAC/2023/31. EVHP3041202306.pdf'),
(228, 7, 148, 3, 'PDF SEAC/2023/20231030_154744_]4.EVHP3041202309.pdf'),
(229, 7, 149, 1, 'PDF SEAC/2023/5. EDO CAM SIT FIN 032023.pdf'),
(230, 7, 149, 2, 'PDF SEAC/2023/32. ECSF3041202306.pdf'),
(231, 7, 149, 3, 'PDF SEAC/2023/20231030_154804_]5.ECSF3041202309.pdf'),
(232, 7, 150, 1, 'PDF SEAC/2023/6. EDO FLU EFE 032023.pdf'),
(233, 7, 150, 2, 'PDF SEAC/2023/33.EFE3041202306.pdf'),
(234, 7, 150, 3, 'PDF SEAC/2023/20231030_154824_]6.EFE3041202309.pdf'),
(235, 7, 151, 1, 'PDF SEAC/2023/7. pas cont.pdf'),
(236, 7, 151, 2, 'PDF SEAC/2023/34. inf sob pas cont.pdf'),
(237, 7, 151, 3, 'PDF SEAC/2023/20231030_154843_]7.inf_pasi_cont.pdf'),
(238, 7, 152, 1, 'PDF SEAC/2023/8. NEF3041202303.pdf'),
(239, 7, 152, 2, 'PDF SEAC/2023/35.NEF3041202306.pdf'),
(240, 7, 152, 3, 'PDF SEAC/2023/20231030_154901_]8.NEF3041202309.pdf'),
(241, 7, 153, 1, 'PDF SEAC/2023/9. EDO ANA ACT 032023.pdf'),
(242, 7, 153, 2, 'PDF SEAC/2023/36.EAA3041202306.pdf'),
(243, 7, 153, 3, 'PDF SEAC/2023/20231030_154918_]9.EAA3041202309.pdf'),
(244, 7, 154, 1, 'PDF SEAC/2023/10. EDO ANA DEU Y OTR PAS 032023.pdf'),
(245, 7, 154, 2, 'PDF SEAC/2023/37.EADOP3041202306.pdf'),
(246, 7, 154, 3, 'PDF SEAC/2023/20231030_154932_]10.EADOP3041202309.pdf'),
(247, 7, 155, 2, 'PDF SEAC/2023/38.PI30412023.pdf'),
(248, 7, 156, 1, 'PDF SEAC/2023/13.clasificacioneconomicaportipodegasto.pdf'),
(249, 7, 156, 2, 'PDF SEAC/2023/39.EAEPEClasificObjGasto.pdf'),
(250, 7, 156, 3, 'PDF SEAC/2023/20231129_090251_]14.EAEPEClasificObjGasto.pdf'),
(251, 7, 157, 2, 'PDF SEAC/2023/40.clasificacioneconomicaportipodegasto.pdf'),
(252, 7, 157, 3, 'PDF SEAC/2023/20231129_090048_]13.clasieconoportipodegasto.pdf'),
(253, 7, 158, 1, 'PDF SEAC/2023/15.clasificacionFuncional(FinalidadyFuncion).pdf'),
(254, 7, 158, 2, 'PDF SEAC/2023/41.clasificacionFuncional(FinalidadyFuncion).pdf'),
(255, 7, 158, 3, 'PDF SEAC/2023/20231129_090322_]15.clasificacionFuncional.pdf'),
(256, 7, 159, 2, 'PDF SEAC/2023/42.gastoporcategoriaprogramatica.pdf'),
(257, 7, 160, 1, 'PDF SEAC/2023/12.clasAdmin.pdf'),
(258, 7, 160, 2, 'PDF SEAC/2023/43.clasAdmin.pdf'),
(259, 7, 160, 3, 'PDF SEAC/2023/20231129_085904_]12.clasAdmin.pdf'),
(260, 7, 161, 2, 'PDF SEAC/2023/44.FuentesFinanciamiento.pdf'),
(261, 7, 162, 2, 'PDF SEAC/2023/45-50. EGRESOS 062023.pdf'),
(262, 7, 163, 2, 'PDF SEAC/2023/51. INGR ESTIMADA.pdf'),
(263, 7, 164, 2, 'PDF SEAC/2023/52-54.INGRESOS 062023.pdf'),
(264, 7, 165, 2, 'PDF SEAC/2023/55. Int conta pre deveng.pdf'),
(265, 7, 166, 2, 'PDF SEAC/2023/56. Proce emi finan.pdf'),
(266, 7, 167, 1, 'PDF SEAC/2023/11. EAI3041202303.pdf'),
(267, 7, 167, 2, 'PDF SEAC/2023/57.EAI3041202306.pdf'),
(268, 7, 167, 3, 'PDF SEAC/2023/20231030_154944_]11.EAI3041202309.pdf'),
(269, 7, 168, 2, 'PDF SEAC/2023/58.clasAdmin.pdf'),
(270, 7, 169, 2, 'PDF SEAC/2023/59.clasificacioneconomica.pdf'),
(271, 7, 170, 1, 'PDF SEAC/2023/14.EAEPEClasificObjGasto.pdf'),
(272, 7, 170, 2, 'PDF SEAC/2023/60.EAEPEClasificObjGasto.pdf'),
(273, 7, 170, 3, 'PDF SEAC/2023/20231129_090251_]14.EAEPEClasificObjGasto.pdf'),
(274, 7, 171, 2, 'PDF SEAC/2023/61.clasificacionFuncional(FinalidadyFuncion).pdf'),
(275, 7, 172, 1, 'PDF SEAC/2023/16. end neto.pdf'),
(276, 7, 172, 2, 'PDF SEAC/2023/62. end net.pdf'),
(277, 7, 172, 3, 'PDF SEAC/2023/20231030_155058_]16.endeneto.pdf'),
(278, 7, 173, 1, 'PDF SEAC/2023/17. int deuda.pdf'),
(279, 7, 173, 2, 'PDF SEAC/2023/63. inte deuda.pdf'),
(280, 7, 173, 3, 'PDF SEAC/2023/20231030_155112_]17.intdeuda.pdf'),
(281, 7, 174, 1, 'PDF SEAC/2023/18.gastoporcategoriaprogramatica.pdf'),
(282, 7, 174, 2, 'PDF SEAC/2023/64.gastoporcategoriaprogramatica.pdf'),
(283, 7, 174, 3, 'PDF SEAC/2023/20231129_090343_]18.gastoporcategoriaprogramatica.pdf'),
(284, 7, 175, 1, 'PDF SEAC/2023/19.PbRM08b3041202303.pdf'),
(285, 7, 175, 2, 'PDF SEAC/2023/65.PbRM08b3041202306.pdf'),
(286, 7, 175, 3, 'PDF SEAC/2023/20231030_155347_]19.PbRM08b3041202309.pdf'),
(287, 7, 176, 2, 'PDF SEAC/2023/66. gen prog y proy inv.pdf'),
(288, 7, 177, 2, 'PDF SEAC/2023/67. Lev fisi inv b m.pdf'),
(289, 7, 178, 2, 'PDF SEAC/2023/68.IBI3041202306.pdf'),
(290, 7, 179, 2, 'PDF SEAC/2023/69. inv bien conc.pdf'),
(291, 7, 180, 2, 'PDF SEAC/2023/70.rea bien ina.pdf'),
(292, 7, 181, 2, 'PDF SEAC/2023/71.reg al mueb.pdf'),
(293, 7, 182, 2, 'PDF SEAC/2023/72.inv alta inmb.pdf'),
(294, 7, 183, 2, 'PDF SEAC/2023/73.reg ac ent rec.pdf'),
(295, 7, 184, 2, 'PDF SEAC/2023/74.Resultados lev inve..pdf'),
(296, 7, 185, 2, 'PDF SEAC/2023/75.Cuentas inm.pdf'),
(297, 7, 186, 2, 'PDF SEAC/2023/76. Cuentas mueb.pdf'),
(298, 7, 187, 2, 'PDF SEAC/2023/77. ope pres.pdf'),
(299, 7, 188, 2, 'PDF SEAC/2023/78. coad cta pub.pdf'),
(300, 7, 189, 2, 'PDF SEAC/2023/79. pag elect.pdf'),
(301, 7, 190, 2, 'PDF SEAC/2023/81.EA3041202306.pdf'),
(302, 7, 191, 2, 'PDF SEAC/2023/82.ESF3041202306.pdf'),
(303, 7, 192, 2, 'PDF SEAC/2023/83. EVHP3041202306.pdf'),
(304, 7, 193, 2, 'PDF SEAC/2023/84. ECSF3041202306.pdf'),
(305, 7, 194, 2, 'PDF SEAC/2023/85.EFE3041202306.pdf'),
(306, 7, 195, 2, 'PDF SEAC/2023/86. inf pas cont.pdf'),
(307, 7, 196, 2, 'PDF SEAC/2023/87.NEF3041202306.pdf'),
(308, 7, 197, 2, 'PDF SEAC/2023/88.EAA3041202306.pdf'),
(309, 7, 198, 2, 'PDF SEAC/2023/89.EADOP3041202306.pdf'),
(310, 7, 199, 2, 'PDF SEAC/2023/90.EAI3041202306.pdf'),
(311, 7, 200, 2, 'PDF SEAC/2023/91.clasAdmin.pdf'),
(312, 7, 201, 2, 'PDF SEAC/2023/92.clasificacioneconomica.pdf'),
(313, 7, 202, 2, 'PDF SEAC/2023/93.EAEPEClasificObjGasto.pdf'),
(314, 7, 203, 2, 'PDF SEAC/2023/94.clasificacionFuncional(FinalidadyFuncion).pdf'),
(315, 7, 204, 2, 'PDF SEAC/2023/95. pub end net.pdf'),
(316, 7, 205, 2, 'PDF SEAC/2023/96. pub int deu.pdf'),
(317, 7, 206, 2, 'PDF SEAC/2023/97.gastoporcategoriaprogramatica.pdf'),
(318, 7, 207, 1, 'PDF SEAC/2023/20. PbRM08b3041202303.pdf'),
(319, 7, 207, 2, 'PDF SEAC/2023/98.PbRM08b3041202306.pdf'),
(320, 7, 207, 3, 'PDF SEAC/2023/20231030_155347_]19.PbRM08b3041202309.pdf'),
(321, 7, 208, 1, 'PDF SEAC/2023/20. pro y proy inve.pdf'),
(322, 7, 208, 2, 'PDF SEAC/2023/99. publ prog y proy inv.pdf'),
(323, 7, 208, 3, 'PDF SEAC/2023/20231030_155410_]20.progyproyinv.pdf'),
(324, 8, 209, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/1.Cuentas.pdf'),
(325, 8, 209, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/1.Cuentas.pdf'),
(326, 8, 210, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/2.oct141b.pdf'),
(327, 8, 210, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/2.oct141b.pdf'),
(328, 8, 211, 2, 'PDF SEAC/2022/20220811_163653_]3.Cuentas.pdf'),
(329, 8, 211, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/3.EGRESOS 122022.pdf'),
(330, 8, 212, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/4.Cuentas.pdf'),
(331, 8, 212, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/4.EGRESOS 122022.pdf'),
(332, 8, 213, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/5.Cuentas.pdf'),
(333, 8, 213, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/5. INGRESOS 122022 (1).pdf'),
(334, 8, 214, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/6.Cuentas.pdf'),
(335, 8, 214, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/6. INGRESOS 122022.pdf'),
(336, 8, 215, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/7. CTAS BIEN MUEB.pdf'),
(337, 8, 215, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/7. REG_ACT_BIENES_MUE.pdf'),
(338, 8, 216, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/8. CTAS BIEN  INMU.pdf'),
(339, 8, 216, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/8. REG_ACT_BIENES_INMUE.pdf'),
(340, 8, 217, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/9. REGIS INMU.pdf'),
(341, 8, 217, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/9.BIEN_INM_VAL_CAT.pdf'),
(342, 8, 218, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/10. BAJA_MUE.pdf'),
(343, 8, 218, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/10.REG_BAJ_BIEN_MUEB.pdf'),
(344, 8, 219, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/11. BAJA-INM.pdf'),
(345, 8, 219, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/11.REG_BAJ_BIEN_INMUEB.pdf'),
(346, 8, 220, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/12.regi_bien_cus.pdf'),
(347, 8, 220, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/12.REG_AUX_BIEN_CUST.pdf'),
(348, 8, 221, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/13.reg inve domi pub.pdf'),
(349, 8, 221, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/13. INV_DOM_PUB.pdf'),
(350, 8, 222, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/14. reg obras en proc.pdf'),
(351, 8, 222, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/14.REG_BIE_DOM_PUB.pdf'),
(352, 8, 223, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/15 reg_dere_patri.pdf'),
(353, 8, 223, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/15.REG_ACT_DER_PATR.pdf'),
(354, 8, 224, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/16.EGRESOS 062022.pdf'),
(355, 8, 224, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/16.EGRESOS.pdf'),
(356, 8, 225, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/17.INGRESOS 062022.pdf'),
(357, 8, 225, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/17.INGRESOS.pdf'),
(358, 8, 226, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/18.DR GRAL POL 062022.pdf'),
(359, 8, 226, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/18.DR GRAL POL 062022.pdf'),
(360, 8, 227, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/19. BAL COM TRIM.pdf'),
(361, 8, 227, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/19.m_balanzacdet.pdf'),
(362, 8, 228, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/20. regist hist sum pro.pdf'),
(363, 8, 228, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/20.REG_HIST_INV_MAT_PR.pdf'),
(364, 8, 229, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/21.reg lib almac.pdf'),
(365, 8, 229, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/21.REG_HIST_SUM_CONS.pdf'),
(366, 8, 230, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/22. reg hist inven.pdf'),
(367, 8, 230, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/22.REG_HIST_INV.pdf'),
(368, 8, 231, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/23. reg his ope bal.pdf'),
(369, 8, 231, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/23.MAN_REG_OPE_BALAN.pdf'),
(370, 8, 232, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/24. const prov.pdf'),
(371, 8, 232, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/24.Cons_Prov.pdf'),
(372, 8, 233, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/25. rev_provi.pdf'),
(373, 8, 233, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/25.Rev_Prov.pdf'),
(374, 8, 234, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/26.IBM3041202201.pdf'),
(375, 8, 234, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/26.IBM12.pdf'),
(376, 8, 235, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/27. regi cont ingre.pdf'),
(377, 8, 235, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/27.EDO COM ING 122022.pdf'),
(378, 8, 236, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/28. edos finan pas deud.pdf'),
(379, 8, 236, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/28.PID30412022.pdf'),
(380, 8, 237, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/31.EDO VAR HAC PUB 062022.pdf'),
(381, 8, 237, 2, 'PDF SEAC/2022/20221028_092959_]4.EDO VAR HAC PUB 092022.pdf'),
(382, 8, 237, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/29.EAEPEClasificObjGasto.pdf'),
(383, 8, 238, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/38.PID30412022.pdf'),
(384, 8, 238, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/30.clasificacioneconomicaportipodegasto.pdf'),
(385, 8, 239, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/39.EAEPE3041202206.pdf'),
(386, 8, 239, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/31.clasificacionFuncional(FinalidadyFuncion).pdf'),
(387, 8, 240, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/41.EAEPECFLDF3041202202.pdf'),
(388, 8, 240, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/32.gastoporcategoriaprogramatica (2).pdf'),
(389, 8, 241, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/42.Programas.pdf'),
(390, 8, 241, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/33.clasAdmin.pdf'),
(391, 8, 242, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/43.EAEPECALDF3041202202.pdf'),
(392, 8, 242, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/34.FuentesFinanciamiento.pdf'),
(393, 8, 243, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/44.FuentesFinanciamiento.pdf'),
(394, 8, 243, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/35.REG_PRES_EGRE_APRO.pdf'),
(395, 8, 244, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/45.Cuentas (7).pdf'),
(396, 8, 244, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/36.Avance Presupuestal Egresos 122022.pdf'),
(397, 8, 245, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/46-50.EGRESOS 062022.pdf'),
(398, 8, 245, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/37.Reg_Egre_Comp.pdf'),
(399, 8, 246, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/67.Lev Fisico Inventario.pdf'),
(400, 8, 246, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/39.Reg_Egre_Ejercido.pdf'),
(401, 8, 247, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/51-54.INGRESOS 062022.pdf'),
(402, 8, 247, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/38.Reg_Egre_Dev.pdf'),
(403, 8, 248, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/68.IBI3041202201.pdf'),
(404, 8, 248, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/40.Avance Presupuestal Egresos 122022.pdf'),
(405, 8, 249, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/69. conci inventario.pdf'),
(406, 8, 249, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/41. Reg_Ing_Est.pdf'),
(407, 8, 250, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/70.inv_bien_ina.pdf'),
(408, 8, 250, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/42.Avance Presupuestal Ingresos 122022.pdf'),
(409, 8, 251, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/71.inv trim bm.pdf'),
(410, 8, 251, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/43.Reg_Ing_Deven.pdf'),
(411, 8, 252, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/72.inv tri bi.pdf'),
(412, 8, 252, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/44.Reg_ing_reca.pdf'),
(413, 8, 253, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/73.trans_bien.pdf'),
(414, 8, 253, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/45.Int_for_aut_gsto_dev.pdf'),
(415, 8, 254, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/74.Resultados lev inve..pdf'),
(416, 8, 254, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/46.Lev Fisico Inventario Dic.pdf'),
(417, 8, 255, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/75.Cuentas (8).pdf'),
(418, 8, 255, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/47.IBI3041202202.pdf'),
(419, 8, 256, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/76.Cuentas (9).pdf'),
(420, 8, 256, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/48.Inv_fis_Bien_mue_inm_con.pdf'),
(421, 8, 257, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/76.Cuentas (9).pdf'),
(422, 8, 257, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/49.Bien_inalien.pdf'),
(423, 8, 258, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/78.OFIC CTA PUB.pdf'),
(424, 8, 258, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/50.RMMBM3041202212.pdf'),
(425, 8, 259, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/79.PAGO ELECTRO.pdf'),
(426, 8, 259, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/52.tran_bien.pdf'),
(427, 8, 259, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/29.EDO ACT 062022.pdf'),
(428, 8, 259, 3, 'PDF SEAC/2022/20221028_092747_]2.EDO ACT 092022.pdf'),
(430, 8, 261, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/56.EDOSIT062022.pdf'),
(431, 8, 261, 3, 'PDF SEAC/2022/20221028_092807_]3.EDOSIT092022.pdf'),
(432, 8, 261, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/53.Resultados lev inve..pdf'),
(433, 8, 262, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/32.EDO CAM SIT FIN 062022.pdf'),
(434, 8, 262, 3, 'PDF SEAC/2022/20221028_093013_]5.EDO CAM SIT FIN 092022.pdf'),
(435, 8, 262, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/54.Cuentas (8).pdf'),
(436, 8, 263, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/33.EDO FLU EFE 062022.pdf'),
(437, 8, 263, 3, 'PDF SEAC/2022/20221028_093028_]6.EDO FLU EFE 092022.pdf'),
(438, 8, 263, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/55.Cuentas (9).pdf'),
(439, 8, 264, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/34. pas cont.pdf'),
(440, 8, 264, 2, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/86.inf_pas_cont.pdf'),
(441, 8, 264, 3, 'PDF SEAC/2022/20221028_093044_]7.pas contn.pdf'),
(442, 8, 264, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/56.ope_pres.pdf'),
(443, 8, 265, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/35.NOTAS EDO FIN 062022.pdf'),
(444, 8, 265, 3, 'PDF SEAC/2022/20221028_093102_]8.NOTAS EDO FIN 092022.pdf'),
(445, 8, 265, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/57.OFIC CTA PUB21.pdf'),
(446, 8, 266, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/36.EDO ANA ACT 062022.pdf'),
(447, 8, 266, 3, 'PDF SEAC/2022/20221028_093132_]9.EDO ANA ACT 092022.pdf'),
(448, 8, 266, 4, 'PDF SEAC/2022/SEVAC_CUARTO_TRIMESTRE_2022/58.Pago_electr.pdf'),
(449, 8, 267, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/37.EDO ANA DEU Y OTR PAS 062022.pdf'),
(450, 8, 267, 3, 'PDF SEAC/2022/20221028_093149_]10.EDO ANA DEU Y OTR PAS 092022.pdf'),
(451, 8, 268, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/57.EAI3041202206.pdf'),
(452, 8, 268, 3, 'PDF SEAC/2022/20221028_093204_]11.EAI3041202209.pdf'),
(453, 8, 269, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/91.clasAdmin.pdf'),
(454, 8, 269, 3, 'PDF SEAC/2022/20221028_093220_]12.clasAdmin.pdf'),
(455, 8, 270, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/59.clasificacioneconomicaportipodegasto.pdf'),
(456, 8, 270, 3, 'PDF SEAC/2022/20221028_093236_]13.clasificacioneconomicaportipodegasto (1).pdf'),
(457, 8, 271, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/60.EAEPEClasificObjGasto.pdf'),
(458, 8, 271, 3, 'PDF SEAC/2022/20221028_093252_]14.EAEPEClasificObjGasto.pdf'),
(459, 8, 272, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/61.clasificacionFuncional(FinalidadyFuncion).pdf'),
(460, 8, 272, 3, 'PDF SEAC/2022/20221028_093329_]15.clasificacionFuncional(FinalidadyFuncion).pdf'),
(461, 8, 273, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/62.end_net.pdf'),
(462, 8, 273, 3, 'PDF SEAC/2022/20221028_093345_]16. endeud neto.pdf'),
(463, 8, 274, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/63.int deuda.pdf'),
(464, 8, 274, 3, 'PDF SEAC/2022/20221028_093429_]17. intereses deuda.pdf'),
(465, 8, 275, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/64.gastoporcategoriaprogramatica.pdf'),
(466, 8, 275, 3, 'PDF SEAC/2022/20221028_093458_]18.gastoporcategoriaprogramatica (2).pdf'),
(467, 8, 276, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/65.PbRM-08b202202.pdf'),
(468, 8, 276, 3, 'PDF SEAC/2022/20221028_093545_]19.ind resul.pdf'),
(469, 8, 277, 1, 'PDF SEAC/2022/SEVAC_SEGUNDO_TRIMESTRE_2022/66.prog_proy_inv.pdf'),
(470, 8, 277, 3, 'PDF SEAC/2022/20221028_093602_]20. prog y proy.pdf'),
(471, 9, 278, 1, 'PDF SEAC/2021/20220525_101930_]1.Cuentas.pdf'),
(472, 9, 278, 2, 'PDF SEAC/2021/SEGUNDO TRIMESTRE 202/Montos pagados por ayudas y subsidios.pdf'),
(473, 9, 278, 3, 'PDF SEAC/2021/TERCER TRIMESTRE 2021/Montos pagados por ayudas y subsidios.pdf'),
(474, 9, 278, 4, 'PDF SEAC/2021/CUARTO TRIMESTRE 202/Montos pagados por ayudas y subsidios.pdf'),
(475, 9, 279, 1, 'PDF SEAC/2021/20220525_101951_]2.Manual de Planeación y Presupuesto 2022.pdf'),
(476, 9, 279, 2, 'PDF SEAC/2021/SEGUNDO TRIMESTRE 202/Formato del ejercicio y destino de gasto federalizado y reintegros.pdf'),
(477, 9, 279, 3, 'PDF SEAC/2021/TERCER TRIMESTRE 2021/Formato del ejercicio y destino de gasto federalizado y reintegros.pdf'),
(478, 9, 279, 4, 'PDF SEAC/2021/CUARTO TRIMESTRE 202/Formato del ejercicio y destino de gasto federalizado y reintegros.pdf'),
(479, 9, 280, 2, 'PDF SEAC/2021/SEGUNDO TRIMESTRE 202/Relación de bienes que componen su patrimonio.pdf'),
(480, 9, 280, 3, 'PDF SEAC/2021/TERCER TRIMESTRE 2021/Relación de bienes que componen su patrimonio.pdf'),
(481, 9, 280, 4, 'PDF SEAC/2021/CUARTO TRIMESTRE 202/Relación de bienes que componen su patrimonio.pdf'),
(482, 9, 281, 1, 'PDF SEAC/2021/20220525_103651_]4. Polizas de Egresos.pdf'),
(483, 9, 281, 2, 'PDF SEAC/2021/SEGUNDO TRIMESTRE 202/Formato de programas con recursos concurrente por orden de gobierno.pdf'),
(484, 9, 281, 3, 'PDF SEAC/2021/TERCER TRIMESTRE 2021/Formato de programas con recursos concurrente por orden de gobierno.pdf'),
(485, 9, 281, 4, 'PDF SEAC/2021/CUARTO TRIMESTRE 202/Formato de programas con recursos concurrente por orden de gobierno.pdf'),
(486, 9, 282, 1, 'PDF SEAC/2021/20220525_103859_]5.INGRESOS 122021.pdf'),
(487, 9, 283, 1, 'PDF SEAC/2021/20220525_103915_]6.INGRESOS 122021.pdf'),
(488, 9, 284, 1, 'PDF SEAC/2021/20220525_103932_]7.DIARIO 082021.pdf'),
(489, 9, 285, 1, 'PDF SEAC/2021/20220525_103950_]8. Registro de la donaciÃ³n de bienes inmuebles.pdf'),
(490, 9, 286, 1, 'PDF SEAC/2021/20220525_104005_]9. I N V EN T A R I O D E B I E N E S I N M U E B L E S.pdf'),
(491, 9, 287, 1, 'PDF SEAC/2021/20220525_154052_]10. invetario de bienes muebles dados de baja.pdf'),
(492, 9, 288, 1, 'PDF SEAC/2021/20220525_104105_]11. Baja de inmuebles .pdf'),
(495, 9, 280, 1, 'PDF SEAC/2021/20220525_103845_]3. Polizas de Ingresos.pdf'),
(504, 9, 289, 1, 'PDF SEAC/2021/20220525_104122_]12.Reg_ bien_cust.pdf'),
(505, 9, 302, 1, 'PDF SEAC/2021/20220525_104139_]13.Inv_dom.pdf'),
(506, 9, 303, 1, 'PDF SEAC/2021/20220525_104220_]14.Obras en proceso.pdf'),
(507, 9, 304, 1, 'PDF SEAC/2021/20220525_104237_]15.Activo de derechos patrimoniales.pdf'),
(508, 9, 305, 1, 'PDF SEAC/2021/20220525_104254_]16.CLASIFICACION POR OBJETO DE GASTO (CAPITULO Y CONCEPTO.pdf'),
(509, 9, 306, 1, 'PDF SEAC/2021/20220525_104306_]17. ESTADO ANALÃTICO DE INGRESOS.pdf'),
(510, 9, 307, 1, 'PDF SEAC/2021/20220525_104321_]18. DIARIO GENERAL DE PÃ“LIZAS.pdf'),
(511, 9, 308, 1, 'PDF SEAC/2021/20220525_104339_]19. ANEXO AL ESTADO DE SITUACION FINANCIERA.pdf'),
(512, 9, 309, 1, 'PDF SEAC/2021/20220525_104357_]20. Registro historico materias primas.pdf'),
(513, 9, 310, 1, 'PDF SEAC/2021/20220525_104413_]21.Registro historico de consumos.pdf'),
(514, 9, 311, 1, 'PDF SEAC/2021/20220525_104441_]22. registro historico de inventario de inmuebles.pdf'),
(515, 9, 312, 1, 'PDF SEAC/2021/20220525_104507_]23.Registro historico de operaciones balances.pdf'),
(516, 9, 313, 1, 'PDF SEAC/2021/20220526_145512_]24.Constituye Proviciones.pdf'),
(517, 9, 314, 1, 'PDF SEAC/2021/20220525_104542_]25.Revision de proviciones.pdf'),
(518, 9, 315, 1, 'PDF SEAC/2021/20220525_104556_]26. Segundo Levantamiento de inmuebles.pdf'),
(519, 9, 316, 1, 'PDF SEAC/2021/20220525_104610_]27.Avance Presupuestal Egresos Global.pdf'),
(520, 9, 317, 1, 'PDF SEAC/2021/20220525_104700_]28.ESTADO COMPARATIVO PRESUPUESTAL DE INGRESOS.pdf'),
(521, 9, 318, 1, 'PDF SEAC/2021/20220525_104717_]29. ClasificaciÃ³n por Objeto del Gasto (CapÃ­tulo y concepto).pdf'),
(522, 9, 319, 1, 'PDF SEAC/2021/20220525_104733_]30. CLASIFICACIÃ“N ECONÃ“MICA (por Tipo de Gasto).pdf'),
(523, 9, 320, 1, 'PDF SEAC/2021/20220525_104754_]31. Estado AnÃ¡litico del Ejercicio del Presupuesto de Egresos Detallado.pdf'),
(524, 9, 321, 1, 'PDF SEAC/2021/20220525_104806_]32. GASTO POR CATEGORÃA PROGRAMÃTICA.pdf'),
(525, 9, 322, 1, 'PDF SEAC/2021/20220525_104823_]33. Estado AnÃ¡litico del Ejercicio del Presupuesto de Egresos Detallado - LDF.pdf'),
(526, 9, 323, 1, 'PDF SEAC/2021/20220525_104840_]34. Mantenimiento al Catalogo de Flujo.pdf'),
(527, 9, 324, 1, 'PDF SEAC/2021/20220525_104852_]35.EGRESOS 122021.pdf'),
(528, 9, 325, 1, 'PDF SEAC/2021/20220525_104905_]36.EGRESOS 122021.pdf'),
(529, 9, 326, 1, 'PDF SEAC/2021/20220525_104917_]37.EGRESOS 122021.pdf'),
(530, 9, 327, 1, 'PDF SEAC/2021/20220525_104933_]38.EGRESOS 122021.pdf'),
(531, 9, 328, 1, 'PDF SEAC/2021/20220525_104948_]39.EGRESOS 122021.pdf'),
(532, 9, 329, 1, 'PDF SEAC/2021/20220525_105009_]40.EGRESOS 122021.pdf'),
(533, 9, 330, 1, 'PDF SEAC/2021/20220525_105032_]41.INGRESOS 122021.pdf'),
(534, 9, 331, 1, 'PDF SEAC/2021/20220525_105046_]42.INGRESOS 122021.pdf'),
(535, 9, 332, 1, 'PDF SEAC/2021/20220525_105058_]43.INGRESOS 122021.pdf'),
(536, 9, 333, 1, 'PDF SEAC/2021/20220525_105116_]44.INGRESOS 122021.pdf'),
(537, 9, 334, 1, 'PDF SEAC/2021/20220525_105132_]45.EGRESOS 122021.pdf'),
(538, 9, 335, 1, 'PDF SEAC/2021/20220525_105152_]46. Lev. Fisico.pdf'),
(539, 9, 336, 1, 'PDF SEAC/2021/20220525_105226_]47. Lev FIsic inm.pdf'),
(540, 9, 337, 1, 'PDF SEAC/2021/20220525_105249_]48.inv fis conc.pdf'),
(541, 9, 338, 1, 'PDF SEAC/2021/20220525_105305_]49.bien_Ina.pdf'),
(542, 9, 339, 1, 'PDF SEAC/2021/20220525_105319_]50. R E P O R T E M E N S U A L D E M O V I M I E N T O S D E B I E N E S M U E B L E S.pdf'),
(543, 9, 340, 1, 'PDF SEAC/2021/20220525_105319_]50. R E P O R T E M E N S U A L D E M O V I M I E N T O S D E B I E N E S M U E B L E S.pdf'),
(544, 9, 341, 1, 'PDF SEAC/2021/20220525_105347_]52.trans_adm.pdf'),
(545, 9, 342, 1, 'PDF SEAC/2021/20220525_105408_]53.Tran_bien_rec.pdf'),
(546, 9, 343, 1, 'PDF SEAC/2021/20220525_105421_]54.Cuentas Inm.pdf'),
(547, 9, 344, 1, 'PDF SEAC/2021/20220525_105436_]55.Cuentas mueb.pdf'),
(548, 9, 345, 1, 'PDF SEAC/2021/20220525_105455_]56.poliegre.pdf'),
(549, 9, 346, 1, 'PDF SEAC/2021/20220525_105512_]57. OFICIO CTA PUBL 20.pdf'),
(550, 9, 347, 1, 'PDF SEAC/2021/20220525_105527_]58.transf.pdf'),
(551, 10, 348, 1, 'PDF SEAC/2020/PRIMER TRIMESTRE 2020/Art. 9, fracc. I, IX y XIV, 14 y 67, ultimo parrafo ayudas y subsidios_0.pdf'),
(552, 10, 348, 2, 'PDF SEAC/2020/SEGUNDO TRIMESTRE 2020/Art. 9, fracc. I, IX y XIV, 14 y 67, ultimo parrafo ayudas y subsidios.pdf'),
(553, 10, 348, 3, 'PDF SEAC/2020/TERCER TRIMESTRE 2020/Art. 9, fracc. I, IX y XIV, 14 y 67, ultimo parrafo ayudas y subsidios.pdf'),
(554, 10, 348, 4, 'PDF SEAC/2020/CUARTO TRIMESTRE 2020/COOPERACIONES Y AYUDAS.pdf'),
(555, 10, 349, 1, 'PDF SEAC/2020/PRIMER TRIMESTRE 2020/Art. 9 frac I y IX, 14 y 81. form. ejer. y dest. gsto. fede. y reinte_0.pdf'),
(556, 10, 349, 2, 'PDF SEAC/2020/SEGUNDO TRIMESTRE 2020/Art. 9 frac I y IX, 14 y 81. form. ejer. y dest. gsto. fede. y reinte (1).pdf'),
(557, 10, 349, 3, 'PDF SEAC/2020/TERCER TRIMESTRE 2020/Art. 9 frac I y IX, 14 y 81. form. ejer. y dest. gsto. fede. y reinte.pdf'),
(558, 10, 350, 1, 'PDF SEAC/2020/PRIMER TRIMESTRE 2020/Art. 9 frac I y IX, 14 y 81. form. ejer. y dest. gsto. fede. y reinte_0.pdf'),
(559, 10, 350, 2, 'PDF SEAC/2020/SEGUNDO TRIMESTRE 2020/Art. 9 frac I y IX, 14 y 81. form. ejer. y dest. gsto. fede. y reinte (1).pdf'),
(560, 10, 350, 3, 'PDF SEAC/2020/TERCER TRIMESTRE 2020/Art. 9, fracc. I, IX y XIV, 14 y 67, ultimo parrafo ayudas y subsidios.pdf'),
(561, 10, 351, 1, 'PDF SEAC/2020/PRIMER TRIMESTRE 2020/artÃ­culos 9, fracciones I y IX, 14 y 68, Ãºltimo pÃ¡rrafo, RECURSO RECURRENTE.pdf'),
(562, 10, 351, 2, 'PDF SEAC/2020/SEGUNDO TRIMESTRE 2020/art. 9, fracc. I y IX, 14 y 68, ult. parrafo. progra. rec. fede..pdf'),
(563, 10, 351, 3, 'PDF SEAC/2020/TERCER TRIMESTRE 2020/art. 9, fracc. I y IX, 14 y 68, ult. parrafo. progra. rec. fede..pdf'),
(564, 10, 351, 4, 'PDF SEAC/2020/CUARTO TRIMESTRE 2020/Recurso concurrente.pdf'),
(565, 10, 352, 1, 'PDF SEAC/2020/PRIMER TRIMESTRE 2020/artículos 9, fracciones I y IX, 14 y 79 Definición de resultados de evaluaciones de recursos Federales.pdf'),
(566, 10, 353, 1, 'PDF SEAC/2020/PRIMER TRIMESTRE 2020/Norma para armonizar la presentación de la información adicional a la iniciativa de la Ley de Ingresos..pdf'),
(567, 10, 354, 1, 'PDF SEAC/2020/PRIMER TRIMESTRE 2020/Norma para armonizar la presentación de la información adicional del Proyecto del Presupuesto de Egresos..pdf'),
(568, 10, 355, 1, 'PDF SEAC/2020/PRIMER TRIMESTRE 2020/Norma para establecer la estructura de información de la relación de cuentas bancarias.pdf'),
(569, 10, 356, 1, 'PDF SEAC/2020//PRIMER TRIMESTRE 2020/Norma para establecer la estructura del Calendario de Ingresos base mensual.pdf'),
(570, 10, 357, 1, 'PDF SEAC/2020/PRIMER TRIMESTRE 2020/Norma para establecer la estructura del Calendario del Presupuesto de Egresos base mensual.pdf'),
(571, 10, 358, 1, 'PDF SEAC/2020/PRIMER TRIMESTRE 2020/Norma para la difusión a la ciudadanía de la Ley de Ingresos y del Presupuesto de Egresos.pdf'),
(572, 10, 359, 2, 'PDF SEAC/2020/SEGUNDO TRIMESTRE 2020/Art. 9, fracc I y IX, 14,64 y 79 Difusión de Resultados.pdf'),
(573, 10, 359, 3, 'PDF SEAC/2020/TERCER TRIMESTRE 2020/Art. 9, fracc I y IX, 14,64 y 79 Difusión de Resultados.pdf'),
(574, 10, 360, 4, 'PDF SEAC/2020/CUARTO TRIMESTRE 2020/Clasificacion Funcional.pdf'),
(575, 10, 361, 2, 'PDF SEAC/2020/SEGUNDO TRIMESTRE 2020/art.78. Obliga. pag. fon. fede..pdf'),
(576, 10, 362, 4, 'PDF SEAC/2020/CUARTO TRIMESTRE 2020/EstadoCambiosSitFin(4).pdf'),
(577, 10, 363, 4, 'PDF SEAC/2020/CUARTO TRIMESTRE 2020/Notas a los Estados Financieros.pdf'),
(578, 10, 364, 4, 'PDF SEAC/2020/CUARTO TRIMESTRE 2020/Edo camb Sit fin.pdf'),
(579, 10, 365, 4, 'PDF SEAC/2020/CUARTO TRIMESTRE 2020/Edo Ana Ing Det.pdf'),
(580, 10, 366, 4, 'PDF SEAC/2020/CUARTO TRIMESTRE 2020/ClasificacionAdministrativa.pdf'),
(581, 10, 367, 4, 'PDF SEAC/2020/CUARTO TRIMESTRE 2020/ClasificacionObjGasto.pdf'),
(582, 10, 368, 4, 'PDF SEAC/2020/CUARTO TRIMESTRE 2020/clasificacioneconomicaportipodegasto.pdf'),
(583, 10, 369, 4, 'PDF SEAC/2020/CUARTO TRIMESTRE 2020/gastoporcategoriaprogramatica.pdf'),
(584, 10, 370, 4, 'PDF SEAC/2020/CUARTO TRIMESTRE 2020/Formatos de Bienes- Org.pdf'),
(585, 10, 371, 4, 'PDF SEAC/2020/CUARTO TRIMESTRE 2020/ejercicio y destino.pdf'),
(586, 11, 372, 1, 'PDF SEAC/2019/PRIMER TRIMESTRE 2019/art.-9,-fracc.-i,-ix-y-xiv,-14-y-67,-ultimo-parrafo-ayudas-y-subsidios9.pdf'),
(587, 11, 372, 2, 'PDF SEAC/2019/SEGUNDO TRIMESTRE 2019/art.-9,-fracc.-i,-ix-y-xiv,-14-y-67,-ultimo-parrafo-ayudas-y-subsidios10.pdf'),
(588, 11, 372, 4, 'PDF SEAC/2019/CUARTO TRIMESTRE 2019/Art. 9, fracc. I, IX y XIV, 14 y 67, ultimo parrafo ayudas y subsidios.pdf'),
(589, 11, 373, 1, 'PDF SEAC/2019/PRIMER TRIMESTRE 2019/art.-9-frac-i-y-ix,-14-y-81.-form.-ejer.-y-dest.-gsto.-fede.-y-reinte8.pdf'),
(590, 11, 373, 2, 'PDF SEAC/2019/SEGUNDO TRIMESTRE 2019/art.-9-frac-i-y-ix,-14-y-81.-form.-ejer.-y-dest.-gsto.-fede.-y-reinte9.pdf'),
(591, 11, 373, 4, 'PDF SEAC/2019/CUARTO TRIMESTRE 2019/Art. 9 frac I y IX, 14 y 81. form. ejer. y dest. gsto. fede. y reinte.pdf'),
(592, 11, 374, 1, 'PDF SEAC/2019/PRIMER TRIMESTRE 2019/art.-23-de-la-lgcg.-relacion-de-bienes-que-componen-el-patrimonio-del-ente-publico8.pdf'),
(593, 11, 374, 2, 'PDF SEAC/2019/SEGUNDO TRIMESTRE 2019/art.-23-de-la-lgcg.-relacion-de-bienes-que-componen-el-patrimonio-del-ente-publico9.pdf'),
(594, 11, 374, 4, 'PDF SEAC/2019/CUARTO TRIMESTRE 2019/Art. 23 de la LGCG. relacion de bienes que componen el patrimonio del ente publico.pdf'),
(595, 11, 375, 1, 'PDF SEAC/2019/PRIMER TRIMESTRE 2019/artículos-9,-fracciones-i-y-ix,-14-y-68,-último-párrafo,-recurso-recurrente.pdf'),
(596, 11, 375, 2, 'PDF SEAC/2019/SEGUNDO TRIMESTRE 2019/art.-9,-fracc.-i-y-ix,-14-y-68,-ult.-parrafo.-progra.-rec.-fede.8.pdf'),
(597, 11, 375, 4, 'PDF SEAC/2019/CUARTO TRIMESTRE 2019/art. 9, fracc. I y IX, 14 y 68, ult. parrafo. progra. rec. fede..pdf'),
(598, 11, 376, 1, 'PDF SEAC/2019/PRIMER TRIMESTRE 2019/artículos-9,-fracciones-i-y-ix,-14-y-79-definición-de-resultados-de-evaluaciones-de-recursos-federales.pdf'),
(599, 11, 376, 4, 'PDF SEAC/2019/CUARTO TRIMESTRE 2019/Art. 9, fracc I y IX, 14,64 y 79 Difusión de Resultados.pdf'),
(600, 11, 377, 1, 'PDF SEAC/2019/PRIMER TRIMESTRE 2019/norma-para-armonizar-la-presentación-de-la-información-adicional-a-la-iniciativa-de-la-ley-de-ingresos.2.pdf'),
(601, 11, 378, 1, 'PDF SEAC/2019/PRIMER TRIMESTRE 2019/norma-para-armonizar-la-presentación-de-la-información-adicional-del-proyecto-del-presupuesto-de-egresos.2.pdf'),
(602, 11, 379, 1, 'PDF SEAC/2019/PRIMER TRIMESTRE 2019/norma-para-establecer-la-estructura-del-calendario-de-ingresos-base-mensual2.pdf'),
(603, 11, 380, 1, 'PDF SEAC/2019/PRIMER TRIMESTRE 2019/norma-para-la-difusión-a-la-ciudadanía-de-la-ley-de-ingresos-y-del-presupuesto-de-egresos.2.pdf'),
(604, 11, 381, 1, 'PDF SEAC/2019/PRIMER TRIMESTRE 2019/art.-9,-fracc-i-y-ix,-14,64-y-79-difusión-de-resultados9.pdf'),
(605, 11, 382, 1, 'PDF SEAC/2019/PRIMER TRIMESTRE 2019/artículos-9,-fracciones-i-y-ix,-14-y-79-definición-de-resultados-de-evaluaciones-de-recursos-federales.pdf'),
(606, 11, 383, 2, 'PDF SEAC/2019/SEGUNDO TRIMESTRE 2019/art.-9,-fracc-i-y-ix,-14,64-y-79-difusión-de-resultados9.pdf'),
(607, 11, 384, 2, 'PDF SEAC/2019/SEGUNDO TRIMESTRE 2019/art.78.-obliga.-pag.-fon.-fede.7.pdf'),
(608, 11, 384, 4, 'PDF SEAC/2019/CUARTO TRIMESTRE 2019/art.78. Obliga. pag. fon. fede..pdf'),
(609, 11, 385, 3, 'PDF SEAC/2019/TERCER TRIMESTRE 2019/2.-m-edopaingegr2.pdf'),
(610, 11, 386, 3, 'PDF SEAC/2019/TERCER TRIMESTRE 2019/3.-m-edoposfin4.pdf'),
(611, 11, 387, 3, 'PDF SEAC/2019/TERCER TRIMESTRE 2019/4.-m-edohp4.pdf'),
(612, 11, 387, 4, 'PDF SEAC/2019/CUARTO TRIMESTRE 2019/4. m-edohp.pdf'),
(613, 11, 388, 3, 'PDF SEAC/2019/TERCER TRIMESTRE 2019/5.-m-edocamsitfin4.pdf'),
(614, 11, 388, 4, 'PDF SEAC/2019/CUARTO TRIMESTRE 2019/7. m-edocamsitfin.pdf'),
(615, 11, 389, 3, 'PDF SEAC/2019/TERCER TRIMESTRE 2019/6.-m-edoflujoefe4.pdf'),
(616, 11, 390, 3, 'PDF SEAC/2019/TERCER TRIMESTRE 2019/7.-inf-pasiv-cont.pdf'),
(617, 11, 391, 3, 'PDF SEAC/2019/TERCER TRIMESTRE 2019/8.-notas-a-los-estados-financieros4.pdf'),
(618, 11, 391, 4, 'PDF SEAC/2019/CUARTO TRIMESTRE 2019/2. Notas a los Estados Financieros.pdf'),
(619, 11, 392, 3, 'PDF SEAC/2019/TERCER TRIMESTRE 2019/9.-m-edoanaact4.pdf'),
(620, 11, 392, 4, 'PDF SEAC/2019/CUARTO TRIMESTRE 2019/5. m-edoanaact.pdf'),
(621, 11, 393, 3, 'PDF SEAC/2019/TERCER TRIMESTRE 2019/10.-e-analideupas4.pdf'),
(622, 11, 394, 3, 'PDF SEAC/2019/TERCER TRIMESTRE 2019/11.-e-analingrei4.pdf'),
(623, 11, 394, 4, 'PDF SEAC/2019/CUARTO TRIMESTRE 2019/11. anaingdet-ldf.pdf'),
(624, 11, 395, 3, 'PDF SEAC/2019/TERCER TRIMESTRE 2019/11c-admin-dg.pdf'),
(625, 11, 395, 4, 'PDF SEAC/2019/CUARTO TRIMESTRE 2019/13. clasadmin-ldf.pdf'),
(626, 11, 396, 3, 'PDF SEAC/2019/TERCER TRIMESTRE 2019/13.c-objgas.pdf'),
(627, 11, 396, 4, 'PDF SEAC/2019/CUARTO TRIMESTRE 2019/12. clasobjgas-ldf.pdf'),
(628, 11, 397, 3, 'PDF SEAC/2019/TERCER TRIMESTRE 2019/13.m-claeco.pdf'),
(629, 11, 397, 4, 'PDF SEAC/2019/CUARTO TRIMESTRE 2019/m-claeco.pdf'),
(630, 11, 398, 3, 'PDF SEAC/2019/TERCER TRIMESTRE 2019/14.c-finfun.pdf'),
(631, 11, 398, 4, 'PDF SEAC/2019/CUARTO TRIMESTRE 2019/14. clasfun-ldf.pdf'),
(632, 11, 399, 3, 'PDF SEAC/2019/TERCER TRIMESTRE 2019/16.-end-neto.pdf'),
(633, 11, 400, 3, 'PDF SEAC/2019/TERCER TRIMESTRE 2019/17.-int-de-la-deu.pdf'),
(634, 11, 401, 3, 'PDF SEAC/2019/TERCER TRIMESTRE 2019/18.m-gcatprogm2.pdf'),
(635, 11, 401, 4, 'PDF SEAC/2019/CUARTO TRIMESTRE 2019/m-gcatprogm.pdf'),
(636, 11, 402, 3, 'PDF SEAC/2019/TERCER TRIMESTRE 2019/20.-prog-y-proy-inver.pdf'),
(638, 5, 7, 4, 'uploads/pdfs/1.Estado de Situacion Financiera.pdf'),
(639, 5, 8, 4, 'uploads/pdfs/2.Estado de Actividades.pdf'),
(640, 5, 9, 4, 'uploads/pdfs/3.Estado Analitico de Activo.pdf'),
(641, 5, 10, 4, 'uploads/pdfs/4.Estado Analitico de la Deuda.pdf'),
(642, 5, 11, 4, 'uploads/pdfs/5.Estado de Cambios en la Situacion Financiera.pdf'),
(643, 5, 12, 4, 'uploads/pdfs/6. Estado de Flujo de Efectivo.pdf'),
(644, 5, 13, 4, 'uploads/pdfs/7. Estado de Variacion en la Hacienda Publica.pdf'),
(645, 5, 14, 4, 'uploads/pdfs/8. Notas a los Estados Financieros.pdf'),
(646, 5, 15, 4, 'uploads/pdfs/9.Estado Analitico de Egresos Clasificacion por Objeto del gasto.pdf'),
(647, 5, 16, 4, 'uploads/pdfs/10.Estado Analitico de Egresos Clasificacion Administrativa.pdf'),
(648, 5, 17, 4, 'uploads/pdfs/11.Estado Analitico de Egresos Clasificacion Funcional.pdf'),
(649, 5, 18, 4, 'uploads/pdfs/12.Estado Analitico de Egresos Clasificacion Programatica.pdf'),
(650, 5, 19, 4, 'uploads/pdfs/13.Estado Analitico de Egresos Clasificacion Economica.pdf'),
(651, 5, 20, 4, 'uploads/pdfs/14. Estado Analitico de Ingresos.pdf'),
(652, 5, 21, 4, 'uploads/pdfs/15.Pasivos contigentes.pdf'),
(653, 5, 22, 4, 'uploads/pdfs/16. Ayudas y subsidio.pdf'),
(654, 5, 23, 4, 'uploads/pdfs/IBI3041202512.pdf'),
(655, 5, 24, 4, 'uploads/pdfs/IBM3041202512.pdf'),
(656, 5, 25, 4, 'uploads/pdfs/PbRM08b3041202512.pdf');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios_galeria`
--

CREATE TABLE `servicios_galeria` (
  `id` int(11) NOT NULL,
  `imagen_path` varchar(500) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `slider_comunica`
--

CREATE TABLE `slider_comunica` (
  `id` int(11) NOT NULL,
  `imagen_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int(11) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `mes` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `anio` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `slider_comunica`
--

INSERT INTO `slider_comunica` (`id`, `imagen_path`, `orden`, `activo`, `mes`, `anio`, `created_at`) VALUES
(27, 'uploads/images/d08c583787c7c9178607facb4d572ba8.png', 5, 1, 3, 2026, '2026-03-26 22:50:05'),
(28, 'uploads/images/e724e59135b81a8d8a37dcc08096838c.jpg', 2, 1, 3, 2026, '2026-03-26 22:50:05'),
(29, 'uploads/images/2f5918f847ae67b370d1d1a1d380320f.jpg', 3, 1, 3, 2026, '2026-03-26 22:50:05'),
(30, 'uploads/images/df318348e2245afd9cd8926b5c230e99.jpg', 7, 1, 3, 2026, '2026-03-26 22:50:05'),
(31, 'uploads/images/58450a443f3fb1c4276b9fac7afd6c59.jpg', 8, 1, 3, 2026, '2026-03-26 22:50:05'),
(32, 'uploads/images/8ca991c58acd9efd3ed07d5029316e3a.jpg', 6, 1, 3, 2026, '2026-03-26 22:50:05'),
(33, 'uploads/images/87e4e0adca8560894711ffcbe00d1fb8.jpg', 9, 1, 3, 2026, '2026-03-26 22:50:05'),
(34, 'uploads/images/bb00ed7d533288f7cb4552a48d706eab.jpg', 4, 1, 3, 2026, '2026-03-26 22:50:05'),
(35, 'uploads/images/194276b134f82d8b3586eaaae3aec0d5.jpg', 1, 1, 3, 2026, '2026-03-26 22:50:05'),
(36, 'uploads/images/0142883d76afc886188a612179ae3262.png', 5, 1, 4, 2026, '2026-03-26 22:53:19'),
(37, 'uploads/images/cdd8b351632144ff52cf6647ed781564.jpg', 2, 1, 4, 2026, '2026-03-26 22:53:19'),
(38, 'uploads/images/27feccb0b7b5cd3980c35addd6db4a9f.jpg', 3, 1, 4, 2026, '2026-03-26 22:53:19'),
(39, 'uploads/images/80f556b6836a8c85aaf6c2fc43b3ac8b.jpg', 7, 1, 4, 2026, '2026-03-26 22:53:19'),
(40, 'uploads/images/dia_nin__o.jpg', 8, 1, 4, 2026, '2026-03-26 22:53:19'),
(41, 'uploads/images/fb7a574ffa983c397feb312c1ec20c3a.jpg', 6, 1, 4, 2026, '2026-03-26 22:53:19'),
(42, 'uploads/images/fc0a8b02b90dc2d6040428fd6e11fb6c.jpg', 9, 1, 4, 2026, '2026-03-26 22:53:19'),
(43, 'uploads/images/6d7e13eafe0694e43ab83e1da247fe84.jpg', 4, 1, 4, 2026, '2026-03-26 22:53:19'),
(44, 'uploads/images/36c1e52c97da70856554017bed57134d.jpg', 1, 1, 4, 2026, '2026-03-26 22:53:19'),
(45, 'uploads/images/asesoria_juridica.jpg', 1, 1, 5, 2026, '2026-04-20 11:29:41'),
(46, 'uploads/images/sin_tabaco.jpg', 10, 1, 5, 2026, '2026-04-20 11:29:41'),
(47, 'uploads/images/psicologo.jpg', 6, 1, 5, 2026, '2026-04-20 11:29:41'),
(48, 'uploads/images/enfermera.jpg', 5, 1, 5, 2026, '2026-04-20 11:29:41'),
(49, 'uploads/images/dia_naranja.jpg', 7, 1, 5, 2026, '2026-04-20 11:29:41'),
(50, 'uploads/images/dia_naranja_5.jpg', 8, 1, 5, 2026, '2026-04-20 11:29:41'),
(51, 'uploads/images/contador.jpg', 9, 1, 5, 2026, '2026-04-20 11:29:41'),
(52, 'uploads/images/carril_rosa.jpg', 4, 1, 5, 2026, '2026-04-20 11:29:41'),
(53, 'uploads/images/bulling.jpg', 2, 1, 5, 2026, '2026-04-20 11:29:41'),
(54, 'uploads/images/10_mayo.jpg', 3, 1, 5, 2026, '2026-04-20 11:29:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `slider_config`
--

CREATE TABLE `slider_config` (
  `id` int(11) NOT NULL,
  `seccion` varchar(50) NOT NULL,
  `autoplay_delay` int(11) NOT NULL DEFAULT '3000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `slider_config`
--

INSERT INTO `slider_config` (`id`, `seccion`, `autoplay_delay`) VALUES
(1, 'slider_principal', 6000),
(2, 'slider_comunica', 5200),
(3, 'noticias', 3000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `slider_principal`
--

CREATE TABLE `slider_principal` (
  `id` int(11) NOT NULL,
  `imagen_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('imagen','video') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'imagen',
  `orden` int(11) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `link_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `slider_principal`
--

INSERT INTO `slider_principal` (`id`, `imagen_path`, `tipo`, `orden`, `activo`, `link_url`, `created_at`) VALUES
(9, 'uploads/images/slaider_principal_1.png', 'imagen', 1, 1, 'tramites/DAD', '2026-04-21 17:22:58'),
(10, 'uploads/images/slaider_principal_2.png', 'imagen', 2, 1, 'tramites/DIFCDTI', '2026-04-21 17:23:53'),
(11, 'uploads/images/slaider_principal_3.png', 'imagen', 4, 1, 'mantenimiento', '2026-04-21 17:24:55'),
(12, 'uploads/images/slaider_principal_4.png', 'imagen', 3, 1, 'tramites/DAAM', '2026-04-21 17:25:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramites`
--

CREATE TABLE `tramites` (
  `id` int(11) NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'PMPNNA, DAAM, DANF, DAD, DPAF, DSJAIG',
  `titulo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `imagen_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contenido` longtext COLLATE utf8mb4_unicode_ci COMMENT 'HTML enriquecido (TinyMCE)',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tramites`
--

INSERT INTO `tramites` (`id`, `slug`, `titulo`, `imagen_path`, `contenido`, `updated_at`) VALUES
(1, 'PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes', NULL, '<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\">La Procuradur&iacute;a Municipal de Protecci&oacute;n a Ni&ntilde;as, Ni&ntilde;os y Adolescentes trabaja para que la ni&ntilde;ez atenquense se desarrolle en condiciones favorables.</span></p>\r\n<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\">Esta &aacute;rea dise&ntilde;a estrategias para combatir el maltrato y abuso infantil, para detectar y prevenir que ni&ntilde;as, ni&ntilde;os y adolescentes vivan en situaci&oacute;n de calle, as&iacute; como para prevenir y erradicar el trabajo infantil, el embarazo adolescente y para promover el respeto a los derechos laborales de los adolescentes trabajadores.</span></p>\r\n<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\">Como parte del Modelo AHORA, se trabaja para detectar ni&ntilde;as, ni&ntilde;os y adolescentes, v&iacute;ctimas de violencia familiar y escolar, a quienes se les brinda atenci&oacute;n m&eacute;dica, jur&iacute;dica y psicol&oacute;gica.</span></p>\r\n<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\">En el marco del Programa Abrazo, se ofrece atenci&oacute;n multidisciplinaria a ni&ntilde;as, ni&ntilde;os y adolescentes en situaci&oacute;n de orfandad y vulnerabilidad.</span></p>\r\n<p class=\"MsoNormal\" style=\"text-align: justify;\"><strong style=\"mso-bidi-font-weight: normal;\"><span style=\"font-size: 12.0pt; line-height: 107%;\">Para realizar una denuncia, puede asistir personalmente a las instalaciones o comunicarse al tel&eacute;fono 722 969 1577.</span></strong></p>', '2026-04-27 12:37:25'),
(2, 'DAAM', 'Dirección de Atención a Adultos Mayores', NULL, '<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\">La vida de las personas adultas mayores debe ser activa, digna y autosuficiente, por ello los servicios que se brindan en la Direcci&oacute;n de Atenci&oacute;n al Adulto Mayor, atienden sus necesidades b&aacute;sicas fomentando su bienestar integral y el respeto a sus derechos. </span></p>\r\n<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\">Actualmente, se cuenta con <strong style=\"mso-bidi-font-weight: normal;\">seis casas de d&iacute;a, </strong>&eacute;stas son las direcciones<strong style=\"mso-bidi-font-weight: normal;\">:</strong></span></p>\r\n<ul>\r\n<li class=\"MsoNormal\" style=\"text-align: justify; font-size: 12pt; line-height: 2;\"><!-- [if !supportLists]--><span style=\"font-size: 12pt;\"><span style=\"line-height: 107%;\">5 de Mayo 613, barrio&nbsp;<strong style=\"mso-bidi-font-weight: normal;\">La Concepci&oacute;n</strong></span></span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify; font-size: 12pt; line-height: 2;\"><span style=\"font-size: 12pt;\"><span style=\"text-indent: -18pt; line-height: 107%;\">Callej&oacute;n Calzada del Pante&oacute;n s/n, barrio <strong>Santa Mar&iacute;a</strong></span></span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify; font-size: 12pt; line-height: 2;\"><span style=\"font-size: 12pt;\">Vicente Villada s/n, barrio&nbsp;<strong style=\"text-indent: -18pt;\">San Francisco</strong></span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify; font-size: 12pt; line-height: 2;\"><span style=\"font-size: 12pt;\">16 de Septiembre s/n, barrio&nbsp;<strong style=\"text-indent: -18pt;\">Guadalupe</strong></span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify; font-size: 12pt; line-height: 2;\"><span style=\"font-size: 12pt;\"><span style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-language-override: normal; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; line-height: normal; font-family: \'Times New Roman\';\">&nbsp;</span><span style=\"text-indent: -18pt; line-height: 107%;\">Chapultepec 201, barrio <strong>San Isidro</strong></span></span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify; font-size: 12pt; line-height: 2;\"><span style=\"font-size: 12pt;\">Av. Benito Ju&aacute;rez s/n, esquina con 5 de Mayo, colonia&nbsp;<strong style=\"text-indent: -18pt;\">Reforma</strong><span style=\"text-indent: -18pt;\">.</span></span></li>\r\n</ul>\r\n<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\">Requisitos para afiliaci&oacute;n a las casas de d&iacute;a:</span></p>\r\n<ul>\r\n<li class=\"MsoNormal\" style=\"text-align: justify; font-size: 12pt; line-height: 2;\"><!-- [if !supportLists]--><span style=\"font-size: 12.0pt; line-height: 107%;\">Tener 60 a&ntilde;os o m&aacute;s.</span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify; font-size: 12pt; line-height: 2;\"><span style=\"font-size: 12pt;\">Dos copias de la credencial de elector (INE) al 200%</span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify; font-size: 12pt; line-height: 2;\"><span style=\"font-size: 12pt;\">Dos n&uacute;meros telef&oacute;nicos de un familiar o tutor responsable</span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify; font-size: 12pt; line-height: 2;\"><span style=\"font-size: 12pt;\">Dos fotograf&iacute;as tama&ntilde;o infantil en papel mate.</span></li>\r\n</ul>\r\n<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\">En las casas de d&iacute;a se ofrece atenci&oacute;n integral a la salud y para contribuir a la salud f&iacute;sica y emocional de los usuarios, se brindan clases y talleres, as&iacute; como paseos recreativos y culturales, asimismo, para fomentar la convivencia y el sentido de pertenencia, se celebran diversas festividades.</span></p>', '2026-04-14 11:12:43'),
(3, 'DANF', 'Dirección de Alimentación y Nutrición Familiar', NULL, '<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\">La Direcci&oacute;n de Alimentaci&oacute;n y Nutrici&oacute;n Familiar coordina acciones de apoyo alimentario directo mediante la distribuci&oacute;n de desayunos escolares y despensas, asimismo, <span style=\"mso-spacerun: yes;\">&nbsp;</span>promueve t&eacute;cnicas de producci&oacute;n para el autoconsumo, a trav&eacute;s del cultivo de huertos y<span style=\"mso-spacerun: yes;\">&nbsp; </span>orientaci&oacute;n nutricional familiar.</span></p>\r\n<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\">El apoyo alimentario directo consiste en la distribuci&oacute;n de desayunos escolares, que pueden ser fr&iacute;os o calientes, dise&ntilde;ados bajo criterios de calidad nutricional y que est&aacute;n dirigidos a las ni&ntilde;as, ni&ntilde;os y adolescentes que asisten a escuelas p&uacute;blicas de educaci&oacute;n b&aacute;sica ubicadas de nuestro municipio.</span></p>\r\n<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\">La entrega de canasta alimentaria contribuye al acceso y consumo de alimentos nutritivos de las personas de 6 hasta 64 a&ntilde;os, enfocada en personas en condiciones de vulnerabilidad.</span></p>\r\n<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\">Asimismo, esta Direcci&oacute;n promueve y asesora para el cultivo de huertos escolares y familiares, fomentando as&iacute;, el autoconsumo y la puesta en marcha de proyectos productivos sustentables.</span></p>', '2026-04-14 11:16:28'),
(4, 'DAD', 'Dirección de Atención a la Discapacidad', NULL, '<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\">La Direcci&oacute;n de Atenci&oacute;n a la Discapacidad, a trav&eacute;s del Centro de Rehabilitaci&oacute;n e Integraci&oacute;n Social CRIS, espacio comprometido con el cuidado integral de la poblaci&oacute;n con alg&uacute;n tipo de discapacidad, brinda atenci&oacute;n especializada y accesible que mejora su calidad de vida a trav&eacute;s de acciones de prevenci&oacute;n, diagn&oacute;stico, tratamiento y rehabilitaci&oacute;n; asimismo, se atiende de manera oportuna las necesidades de salud f&iacute;sica, emocional y social de nuestros usuarios.</span></p>\r\n<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\">El CRIS cuenta con un equipo de profesionales capacitados para trabajar de forma interdisciplinaria, poniendo a disposici&oacute;n de la poblaci&oacute;n los siguientes servicios:</span></p>\r\n<ul>\r\n<li class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\"><span style=\"font-size: 12.0pt;\">Especialista en rehabilitaci&oacute;n</span></span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\"><span style=\"font-size: 12.0pt;\">Planificaci&oacute;n familiar</span></span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\"><span style=\"font-size: 12.0pt;\">Otorrinolaringolog&iacute;a</span></span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\"><span style=\"font-size: 12.0pt;\">Aplicaci&oacute;n de vacunas</span></span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\"><span style=\"font-size: 12.0pt;\">Medicina general</span></span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\"><span style=\"font-size: 12.0pt;\">Terapia f&iacute;sica</span></span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\"><span style=\"font-size: 12.0pt;\">Psicolog&iacute;a</span></span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\"><span style=\"font-size: 12.0pt;\">Terapia de lenguaje</span></span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\"><span style=\"font-size: 12.0pt;\">Odontolog&iacute;a</span></span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\"><span style=\"font-size: 12.0pt;\">Terapia ocupacional</span></span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\"><span style=\"font-size: 12.0pt;\">Optometr&iacute;a</span></span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\"><span style=\"font-size: 12.0pt;\">Clases de Braille</span></span></li>\r\n<li class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\"><span style=\"font-size: 12.0pt;\">Nutrici&oacute;n</span></span></li>\r\n</ul>', '2026-04-29 17:54:06'),
(5, 'DPAF', 'Dirección de Prevención y Bienestar Familiar', NULL, '<p dir=\"ltr\">El prop&oacute;sito de la Direcci&oacute;n de Prevenci&oacute;n y Bienestar Familiar es fomentar la salud mental y emocional de las familias atenquenses mediante la promoci&oacute;n de entornos libres de adicciones y de espacios seguros para sanar y fortalecer v&iacute;nculos familiares a trav&eacute;s de programas preventivos y de atenci&oacute;n especializada:</p>\r\n<ul>\r\n<li dir=\"ltr\" aria-level=\"1\">\r\n<p dir=\"ltr\" role=\"presentation\">Consultas psicol&oacute;gicas y tanatol&oacute;gicas.</p>\r\n</li>\r\n<li dir=\"ltr\" aria-level=\"1\">\r\n<p dir=\"ltr\" role=\"presentation\">Taller Escuela para Padres.</p>\r\n</li>\r\n<li dir=\"ltr\" aria-level=\"1\">\r\n<p dir=\"ltr\" role=\"presentation\">Promoci&oacute;n del respeto a los derechos de ni&ntilde;as, ni&ntilde;os y adolescentes en entornos escolares y comunitarios para la prevenci&oacute;n de conductas de riesgo.</p>\r\n</li>\r\n<li dir=\"ltr\" aria-level=\"1\">\r\n<p dir=\"ltr\" role=\"presentation\">Prevenci&oacute;n del embarazo adolescente a trav&eacute;s de jornadas de educaci&oacute;n sexual,</p>\r\n</li>\r\n<li dir=\"ltr\" aria-level=\"1\">\r\n<p dir=\"ltr\" role=\"presentation\">Pl&aacute;ticas, talleres y capacitaci&oacute;n para el autoempleo, dirigidos a la autonom&iacute;a de las mujeres.</p>\r\n</li>\r\n<li dir=\"ltr\" aria-level=\"1\">\r\n<p dir=\"ltr\" role=\"presentation\">Servicios que apoyan el desarrollo emocional y la integraci&oacute;n con perspectiva de g&eacute;nero.</p>\r\n</li>\r\n</ul>\r\n<p style=\"text-align: left;\">&nbsp;</p>', '2026-04-14 11:22:51'),
(6, 'DSJAIG', 'Dirección de Servicios Jurídicos – Asistenciales e Igualdad de Género', NULL, '<p dir=\"ltr\">La Direcci&oacute;n de Servicios Jur&iacute;dico Asistenciales e Igualdad de G&eacute;nero, ofrece ASESOR&Iacute;AS JUR&Iacute;DICAS y PROCEDIMIENTOS JUDICIALES.</p>\r\n<p dir=\"ltr\">Las asesor&iacute;as jur&iacute;dicas que en materia familiar se brindan, permiten garantizar la preservaci&oacute;n de los derechos de la poblaci&oacute;n de San Mateo Atenco, originaria y/o residente en el territorio municipal:</p>\r\n<ul>\r\n<li dir=\"ltr\" aria-level=\"1\">\r\n<p dir=\"ltr\" role=\"presentation\">Patria potestad.</p>\r\n</li>\r\n<li dir=\"ltr\" aria-level=\"1\">\r\n<p dir=\"ltr\" role=\"presentation\">Guarda y custodia.</p>\r\n</li>\r\n<li dir=\"ltr\" aria-level=\"1\">\r\n<p dir=\"ltr\" role=\"presentation\">Pensi&oacute;n alimenticia.</p>\r\n</li>\r\n<li dir=\"ltr\" aria-level=\"1\">\r\n<p dir=\"ltr\" role=\"presentation\">R&eacute;gimen de convivencia familiar.</p>\r\n</li>\r\n<li dir=\"ltr\" aria-level=\"1\">\r\n<p dir=\"ltr\" role=\"presentation\">Divorcio por mutuo consentimiento e incausado.</p>\r\n</li>\r\n</ul>\r\n<p dir=\"ltr\">En cuanto a procedimientos judiciales, se realizan los siguientes juicios.</p>\r\n<ul>\r\n<li dir=\"ltr\" aria-level=\"1\">\r\n<p dir=\"ltr\" role=\"presentation\">Juicios de Rectificaci&oacute;n de actas del Registro Civil (nacimiento, matrimonio, defunci&oacute;n).</p>\r\n</li>\r\n</ul>\r\n<p style=\"text-align: left;\">&nbsp;</p>', '2026-04-13 12:53:56'),
(7, 'DIFCDTI', 'Caravana de Servicios DIF CERCA DE TI', NULL, '<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\">El compromiso del DIF San Mateo Atenco es acercar sus servicios asistenciales a las familias atenquenses, por ello, las Caravanas de Servicios DIF Cerca de Ti, constituyen una valiosa herramienta para contribuir con un gobierno familiarmente responsable. </span></p>\r\n<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\">A trav&eacute;s de estas caravanas, que llegan a todos los barrios y colonias del municipio, se ofrecen servicios de salud, asesor&iacute;as jur&iacute;dicas y para regularizaci&oacute;n de inmuebles; venta de vegetales org&aacute;nicos y pl&aacute;ntula, as&iacute; como orientaci&oacute;n para el cultivo de huertos; informaci&oacute;n sobre actividades culturales, deportivas y del Voluntariado, Tenderito DIF, cortes de cabello gratuitos y otros servicios. </span></p>\r\n<p class=\"MsoNormal\" style=\"text-align: justify;\"><span style=\"font-size: 12.0pt; line-height: 107%;\">Para su realizaci&oacute;n, se cuenta con el apoyo del DIFEM, de la Secretar&iacute;a del Trabajo, del Instituto Mexiquense de la Vivienda Social, de la Academia Lupita San Mateo y de las diferentes direcciones del Gobierno Municipal.</span></p>', '2026-04-14 11:26:06'),
(8, 'CAPC', 'Coordinación de Atención a Pacientes con Cáncer', NULL, '<p class=\"MsoNormal\"><span style=\"font-size: 12.0pt; line-height: 107%;\">La Coordinaci&oacute;n de Atenci&oacute;n a Pacientes con C&aacute;ncer tiene como prop&oacute;sito fundamental acercar servicios y acompa&ntilde;amiento a la poblaci&oacute;n a trav&eacute;s de la realizaci&oacute;n de jornadas de mastograf&iacute;as para la detecci&oacute;n oportuna del c&aacute;ncer de mama, sesiones informativas que promueven la prevenci&oacute;n y el conocimiento del padecimiento, visitas domiciliarias de acompa&ntilde;amiento a pacientes y sus familias, as&iacute; como la gesti&oacute;n y entrega de apoyos diversos.</span></p>\r\n<p class=\"MsoNormal\"><span style=\"font-size: 12.0pt; line-height: 107%;\">Para apoyar la rehabilitaci&oacute;n de las sobrevivientes del c&aacute;ncer de mama, se ofrecen sesiones especializadas de terapia en alberca, para lo cual se ha gestionado ante el IMCUFIDE la apertura del &ldquo;Carril Rosa&rdquo;, un espacio destinado a este fin.</span></p>\r\n<p class=\"MsoNormal\"><span style=\"font-size: 12.0pt; line-height: 107%;\">De manera permanente, se impulsan dos programas que fortalecen el apoyo a quienes enfrentan esta enfermedad:</span></p>\r\n<ul>\r\n<li class=\"MsoNormal\" style=\"font-size: 12pt; font-family: Montserrat, sans-serif;\"><span style=\"font-size: 12pt; line-height: 107%; font-family: Montserrat, sans-serif;\"><strong style=\"mso-bidi-font-weight: normal;\"><span style=\"line-height: 107%;\">Trenzat&oacute;n DIF</span></strong><span style=\"line-height: 107%;\">, mediante el cual se recolectan trenzas destinadas a la elaboraci&oacute;n de pelucas oncol&oacute;gicas</span></span></li>\r\n<li class=\"MsoNormal\" style=\"font-size: 12pt;\"><span style=\"font-size: 12pt;\"><!-- [if !supportLists]--><strong style=\"mso-bidi-font-weight: normal;\"><span style=\"line-height: 107%;\">Recolecci&oacute;n de tapitas</span></strong><span style=\"line-height: 107%;\">, que contribuye a la realizaci&oacute;n de tratamientos oncol&oacute;gicos para ni&ntilde;as y ni&ntilde;os.</span></span></li>\r\n</ul>\r\n<p class=\"MsoListParagraph\" style=\"text-indent: -18.0pt; mso-list: l0 level1 lfo1;\">&nbsp;</p>', '2026-04-14 11:27:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramites_galeria`
--

CREATE TABLE `tramites_galeria` (
  `id` int(11) NOT NULL,
  `tramite_id` int(11) NOT NULL,
  `imagen_path` varchar(500) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tramites_galeria`
--

INSERT INTO `tramites_galeria` (`id`, `tramite_id`, `imagen_path`, `orden`, `activo`) VALUES
(12, 1, 'uploads/images/1894b5ae-fcba-4cc8-b3c4-bbd32b9422cc.jpg', 1, 1),
(13, 1, 'uploads/images/2555483a-e42c-4019-952c-3aabbb875cf5.jpg', 2, 1),
(14, 1, 'uploads/images/PHOTO-2025-06-09-13-44-07 2.jpg', 3, 1),
(15, 1, 'uploads/images/56fab49d-5256-460b-bb90-ec4396d54f15.jpg', 4, 1),
(16, 1, 'uploads/images/2025-10-31 at 10.42.01 AM _1.jpg', 5, 1),
(17, 2, 'uploads/images/IMG_3073 2.jpg', 1, 1),
(18, 2, 'uploads/images/IMG_5082 2.jpg', 2, 1),
(19, 2, 'uploads/images/IMG_8161 2.jpg', 4, 1),
(20, 2, 'uploads/images/IMG_9211.jpg', 5, 1),
(21, 2, 'uploads/images/IMG_9880.jpg', 3, 1),
(22, 3, 'uploads/images/IMG_7865.jpg', 3, 1),
(23, 3, 'uploads/images/IMG_8647.jpg', 1, 1),
(24, 3, 'uploads/images/IMG_8870.jpg', 2, 1),
(25, 3, 'uploads/images/IMG_9790.jpg', 4, 1),
(26, 3, 'uploads/images/IMG_5836 2.jpg', 5, 1),
(27, 4, 'uploads/images/IMG_4600.jpg', 1, 1),
(28, 4, 'uploads/images/IMG_4953 2.jpg', 2, 1),
(29, 4, 'uploads/images/IMG_5303.jpg', 4, 1),
(30, 4, 'uploads/images/IMG_5539.jpg', 3, 1),
(31, 4, 'uploads/images/IMG_5594.jpg', 5, 1),
(32, 5, 'uploads/images/IMG_0135 2.jpg', 1, 1),
(33, 5, 'uploads/images/IMG_2760.jpg', 3, 1),
(34, 5, 'uploads/images/IMG_3378.jpg', 2, 1),
(35, 5, 'uploads/images/IMG_9017.jpg', 4, 1),
(36, 5, 'uploads/images/7adc2f29-0793-48a6-9817-35bb6ff2fd95.jpg', 5, 1),
(37, 7, 'uploads/images/IMG_3297 2.jpg', 1, 1),
(38, 7, 'uploads/images/IMG_3485 4.jpg', 2, 1),
(39, 7, 'uploads/images/IMG_7067.jpg', 4, 1),
(40, 7, 'uploads/images/IMG_7076.jpg', 5, 1),
(41, 7, 'uploads/images/IMG_7164.jpg', 3, 1),
(42, 8, 'uploads/images/IMG_0153.jpg', 1, 1),
(43, 8, 'uploads/images/IMG_4151 2.jpg', 2, 1),
(44, 8, 'uploads/images/IMG_6828 3.jpg', 4, 1),
(45, 8, 'uploads/images/IMG_7095 3.jpg', 5, 1),
(46, 8, 'uploads/images/IMG_7146.jpg', 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transparencia_items`
--

CREATE TABLE `transparencia_items` (
  `id` int(11) NOT NULL,
  `titulo` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `imagen_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `transparencia_items`
--

INSERT INTO `transparencia_items` (`id`, `titulo`, `url`, `imagen_path`, `orden`, `activo`) VALUES
(1, 'SISTEMA DE EVALUACIONES DE LA ARMONIZACIÓN CONTABLE', 'transparencia/SEAC.php', 'uploads/images/6c9f783e2be8e1dbb92388772114434f.png', 1, 1),
(2, 'CUENTA PÚBLICA', 'transparencia/cuenta_publica.php', 'uploads/images/bdd7e69bddbca5670e12b73e5048d3e9.png', 2, 1),
(3, 'PRESUPUESTO ANUAL', 'transparencia/presupuesto_anual.php', 'uploads/images/73061653ef3731372502a0e90fb78ca1.png', 3, 1),
(4, 'PAE', 'transparencia/pae.php', 'uploads/images/f4d2c4743f2361e0fbdf2f11e4c8528b.png', 4, 1),
(5, 'MATRICES DE INDICADORES', 'transparencia/matrices_indicadores.php', 'uploads/images/29109de597a7c3fec005464a599b73a9.png', 5, 1),
(6, 'SISTEMA DE PORTALES DE OBLIGACIONES DE TRANSPARENCIA (SIPOT)', '#', 'uploads/images/261511a2d20fc513c1ae7b385dd14075.png', 6, 1),
(7, 'CONAC', 'transparencia/conac.php', 'uploads/images/066529a261631822d260824f173a0887.png', 7, 1),
(8, 'FINANCIERO', 'transparencia/financiero.php', 'uploads/images/32042248308c5423b36691a4ed066d7b.png', 8, 1),
(9, 'ORGANIGRAMA', 'acerca-del-dif/organigrama.php', 'uploads/images/577c3fcb61d264a3830b6fc5371f1252.png', 9, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trans_bloques`
--

CREATE TABLE `trans_bloques` (
  `id` int(11) NOT NULL,
  `seccion_id` int(11) NOT NULL,
  `anio` year(4) NOT NULL,
  `orden` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trans_conceptos`
--

CREATE TABLE `trans_conceptos` (
  `id` int(11) NOT NULL,
  `seccion_id` int(11) NOT NULL,
  `bloque_id` int(11) DEFAULT NULL,
  `titulo_id` int(11) DEFAULT NULL,
  `nombre` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pdf_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orden` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trans_pdfs`
--

CREATE TABLE `trans_pdfs` (
  `id` int(11) NOT NULL,
  `seccion_id` int(11) NOT NULL,
  `concepto_id` int(11) DEFAULT NULL,
  `titulo_id` int(11) DEFAULT NULL,
  `anio` year(4) DEFAULT NULL,
  `trimestre` tinyint(4) DEFAULT NULL,
  `pdf_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orden` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trans_secciones`
--

CREATE TABLE `trans_secciones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plantilla` enum('seac','cuenta_publica','presupuesto_anual','pae','matrices','conac','financiero') COLLATE utf8mb4_unicode_ci NOT NULL,
  `icono` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'bi-file-earmark-text',
  `activo` tinyint(1) DEFAULT '1',
  `orden` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trans_titulos`
--

CREATE TABLE `trans_titulos` (
  `id` int(11) NOT NULL,
  `seccion_id` int(11) NOT NULL,
  `bloque_id` int(11) DEFAULT NULL,
  `nombre` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `visitor_analytics`
--

CREATE TABLE `visitor_analytics` (
  `id` bigint(20) NOT NULL,
  `session_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pagina` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referrer` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dispositivo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pc',
  `os` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `navegador` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `es_bot` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `visitor_analytics`
--

INSERT INTO `visitor_analytics` (`id`, `session_id`, `pagina`, `titulo`, `referrer`, `ip_hash`, `dispositivo`, `os`, `navegador`, `es_bot`, `created_at`) VALUES
(1, '8e5c4c7ac3b67bfb313d36362814f6c88250e372', '/', 'DIF San Mateo Atenco', NULL, '8b67659a7ab0fb24d5cb9b7347d9eedee9ad36656f704b7f6e7d169ae738acb8', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-16 15:49:24'),
(2, 'fbb7a050c743ba54409a542a558ecdd5b8e7dfbb', '/', 'DIF San Mateo Atenco', NULL, '8b67659a7ab0fb24d5cb9b7347d9eedee9ad36656f704b7f6e7d169ae738acb8', 'celular', 'iPhone iOS 26.4.0', 'Safari', 0, '2026-04-16 15:55:55'),
(3, 'bb97e2b0bd4f947bc4576fa5819febbd3faa99fc', '/', 'DIF San Mateo Atenco', NULL, '69b220c7328e92c01a5a284b731767919a6969428ea6938d4023f7f3a941e676', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-16 16:46:53'),
(4, 'bb97e2b0bd4f947bc4576fa5819febbd3faa99fc', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', '69b220c7328e92c01a5a284b731767919a6969428ea6938d4023f7f3a941e676', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-16 16:47:39'),
(5, 'bb97e2b0bd4f947bc4576fa5819febbd3faa99fc', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '69b220c7328e92c01a5a284b731767919a6969428ea6938d4023f7f3a941e676', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-16 16:49:06'),
(6, 'bb97e2b0bd4f947bc4576fa5819febbd3faa99fc', '/acerca-del-dif/direcciones', 'Direcciones — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', '69b220c7328e92c01a5a284b731767919a6969428ea6938d4023f7f3a941e676', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-16 16:49:20'),
(7, 'bb97e2b0bd4f947bc4576fa5819febbd3faa99fc', '/acerca-del-dif/organigrama', 'Organigrama — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/direcciones', '69b220c7328e92c01a5a284b731767919a6969428ea6938d4023f7f3a941e676', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-16 16:50:09'),
(8, 'bb97e2b0bd4f947bc4576fa5819febbd3faa99fc', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/organigrama', '69b220c7328e92c01a5a284b731767919a6969428ea6938d4023f7f3a941e676', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-16 16:50:16'),
(9, 'bb97e2b0bd4f947bc4576fa5819febbd3faa99fc', '/tramites/DAAM', 'Dirección de Atención a Adultos Mayores — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/PMPNNA', '69b220c7328e92c01a5a284b731767919a6969428ea6938d4023f7f3a941e676', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-16 16:50:27'),
(10, 'bb97e2b0bd4f947bc4576fa5819febbd3faa99fc', '/acerca-del-dif/direcciones', 'Direcciones — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DAAM', '69b220c7328e92c01a5a284b731767919a6969428ea6938d4023f7f3a941e676', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-16 16:51:37'),
(11, 'bb97e2b0bd4f947bc4576fa5819febbd3faa99fc', '/comunicacion-social/noticias', 'Noticias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/direcciones', '69b220c7328e92c01a5a284b731767919a6969428ea6938d4023f7f3a941e676', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-16 16:51:47'),
(12, 'bb97e2b0bd4f947bc4576fa5819febbd3faa99fc', '/tramites/DAAM', 'Dirección de Atención a Adultos Mayores — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/noticias', '69b220c7328e92c01a5a284b731767919a6969428ea6938d4023f7f3a941e676', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-16 16:52:00'),
(13, 'bb97e2b0bd4f947bc4576fa5819febbd3faa99fc', '/tramites/DANF', 'Dirección de Alimentación y Nutrición Familiar — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DAAM', '69b220c7328e92c01a5a284b731767919a6969428ea6938d4023f7f3a941e676', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-16 16:52:15'),
(14, 'bb97e2b0bd4f947bc4576fa5819febbd3faa99fc', '/voluntariado', 'Voluntariado — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/autismo', '69b220c7328e92c01a5a284b731767919a6969428ea6938d4023f7f3a941e676', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-16 16:53:22'),
(15, 'bb97e2b0bd4f947bc4576fa5819febbd3faa99fc', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/voluntariado', '69b220c7328e92c01a5a284b731767919a6969428ea6938d4023f7f3a941e676', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-16 16:53:34'),
(16, 'bb97e2b0bd4f947bc4576fa5819febbd3faa99fc', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/galeria', '69b220c7328e92c01a5a284b731767919a6969428ea6938d4023f7f3a941e676', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-16 16:53:54'),
(17, 'bb97e2b0bd4f947bc4576fa5819febbd3faa99fc', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/galeria', '69b220c7328e92c01a5a284b731767919a6969428ea6938d4023f7f3a941e676', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-16 16:54:59'),
(18, 'bb97e2b0bd4f947bc4576fa5819febbd3faa99fc', '/transparencia/cuenta_publica.php', 'Cuenta Pública — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '69b220c7328e92c01a5a284b731767919a6969428ea6938d4023f7f3a941e676', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-16 16:55:23'),
(19, 'bb97e2b0bd4f947bc4576fa5819febbd3faa99fc', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/cuenta_publica.php', '69b220c7328e92c01a5a284b731767919a6969428ea6938d4023f7f3a941e676', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-16 16:55:46'),
(20, 'fa0bfe93e4c5396ac036624cf44f42fdec910c2b', '/', 'DIF San Mateo Atenco', NULL, 'e9eeabba42d9349e82e4a46e098b48a57fa0d11242d743dcbcfa36715df09d20', 'pc', 'Windows 10/11', 'Chrome 147.0.7727.101', 0, '2026-04-17 06:15:50'),
(21, '810708a3d8c8ac2cc08666a1db01c64a46335013', '/', 'DIF San Mateo Atenco', NULL, 'b3ec410fc4763a263f1108985c2f17bd56cc88255bfb2ae9644b1871f364ffc8', 'pc', 'macOS 10.15.7', 'Chrome 147.0.7727.101', 0, '2026-04-17 07:07:28'),
(22, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 10:46:15'),
(23, '64c15b1a7c91b17cc11298efecc4ba39437a6c1f', '/', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'celular', 'iPhone iOS 26.4.1', 'Safari', 0, '2026-04-17 11:38:14'),
(24, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 11:58:41'),
(25, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/programas/seccion?slug=detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', 'Detección y prevención de niñas, niños y adolescentes en situación de calle — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 12:00:44'),
(26, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 12:12:40'),
(27, '673f320422d5449a583c7fee75d3535fda8e584f', '/', 'DIF San Mateo Atenco', NULL, 'ddf3e31dd9bc70a3c0d34e54796689d40a65584753ac23816205aec85581f8fe', 'pc', 'Linux', 'Chrome 138.0.0.0', 0, '2026-04-17 12:14:10'),
(28, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 12:14:19'),
(29, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 12:14:33'),
(30, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/autismo', 'Unidad Municipal de Autismo — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/PMPNNA', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 12:15:04'),
(31, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/voluntariado', 'Voluntariado — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/autismo', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 12:18:40'),
(32, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/transparencia/cuenta_publica', 'Cuenta Pública — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/voluntariado', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 12:20:22'),
(33, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/transparencia/SEAC', 'SEAC — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/cuenta_publica', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 12:20:39'),
(34, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/SEAC', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 12:34:02'),
(35, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/PMPNNA', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 12:38:18'),
(36, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías judiciales — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 12:46:02'),
(37, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías judiciales — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 12:47:56'),
(38, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías judiciales — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 12:48:33'),
(39, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías jurídicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:09:16'),
(40, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías jurídicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:13:01'),
(41, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías jurídicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:13:44'),
(42, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=asesor-as-judiciales-16', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:15:04'),
(43, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/programas/seccion?slug=procedimientos-judiciales-16', 'Procedimientos judiciales — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:15:14'),
(44, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías jurídicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:16:06'),
(45, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías jurídicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:18:59'),
(46, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/tramites/DSJAIG', 'Dirección de Servicios Jurídicos – Asistenciales e Igualdad de Género — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=asesor-as-judiciales-16', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:20:07'),
(47, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DSJAIG', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:20:14'),
(48, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías jurídicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:20:22'),
(49, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/tramites/DSJAIG', 'Dirección de Servicios Jurídicos – Asistenciales e Igualdad de Género — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=asesor-as-judiciales-16', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:21:09'),
(50, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DSJAIG', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:21:25'),
(51, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DSJAIG', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:22:51'),
(52, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/tramites/DSJAIG', 'Dirección de Servicios Jurídicos – Asistenciales e Igualdad de Género — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:29:16'),
(53, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías jurídicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:29:24'),
(54, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=asesor-as-judiciales-16', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:31:21'),
(55, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:31:29'),
(56, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/PMPNNA', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:32:23'),
(57, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/programas/seccion?slug=detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', 'Detección y prevención de niñas, niños y adolescentes en situación de calle — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:36:53'),
(58, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/programas/seccion?slug=detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', 'Detección y prevención de niñas, niños y adolescentes en situación de calle — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:39:43'),
(59, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:41:23'),
(60, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', 'Prevención y erradicación del trabajo infantil — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:41:33'),
(61, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', 'Prevención y erradicación del trabajo infantil — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:42:30'),
(62, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:44:48'),
(63, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:45:48'),
(64, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-embarazo-adolescente-14', 'Promoción del respeto a los derechos laborales de los adolescentes trabajadores en edad permitida — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:46:06'),
(65, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-embarazo-adolescente-14', 'Promoción del respeto a los derechos laborales de los adolescentes trabajadores en edad permitida — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:47:08'),
(66, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'Entrega de desayunos fríos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 13:51:19'),
(67, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 14:38:04'),
(68, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 14:38:18'),
(69, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 14:41:24'),
(70, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 15:13:57'),
(71, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 15:14:08'),
(72, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 15:37:41'),
(73, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/mantenimiento', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 15:37:47'),
(74, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 15:48:02'),
(75, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 15:59:34'),
(76, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 15:59:43'),
(77, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/galeria', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 16:02:14'),
(78, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/tramites/DSJAIG', 'Dirección de Servicios Jurídicos – Asistenciales e Igualdad de Género — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 16:05:07'),
(79, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DSJAIG', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 16:05:10'),
(80, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DSJAIG', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 16:05:59'),
(81, '00b687d67be8bc102d79cabe05d645ac5360861e', '/', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-17 16:14:18'),
(82, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/galeria', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 16:14:30'),
(83, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-17 16:14:52'),
(84, 'dbb49606ddf621a7763614fb3db875df376e0b85', '/', 'DIF San Mateo Atenco', NULL, '74d96679fc94678e700f576fcf94fc985e47dc8198ac58e068fade0f7fd360b6', 'celular', 'Android 8.0.0', 'Chrome 147.0.7727.101', 0, '2026-04-18 05:13:23'),
(85, '6e5bc54fb84934296acacf6b9750209fca496fcc', '/', 'DIF San Mateo Atenco', NULL, '620057df9484af12a3d0442db81560e36913b54e481505b61c9d71a61520eed2', 'celular', 'Android 8.0.0', 'Chrome 147.0.7727.101', 0, '2026-04-18 06:29:04'),
(86, '0a9e8b62ff620ffd7043c2702e2d635eeb42291e', '/', 'DIF San Mateo Atenco', NULL, 'b0a3eeef08e30f2b7f05778a891c999c7fb29c0b1044fe52761033db9f37cd37', 'pc', 'macOS 10.15.7', 'Chrome 147.0.7727.101', 0, '2026-04-19 04:23:14'),
(87, '089982da78cb864cf98fdd082a45e0681f0c4b32', '/', 'DIF San Mateo Atenco', NULL, '74d96679fc94678e700f576fcf94fc985e47dc8198ac58e068fade0f7fd360b6', 'pc', 'Windows 10/11', 'Chrome 147.0.7727.101', 0, '2026-04-19 05:23:32'),
(88, '0243c960b0868323eb82570732f312b826d9eb1f', '/', 'DIF San Mateo Atenco', NULL, '41a4826e040646e0bc2996ab3eae2156c07cf6cf2cc3058b526e95f20af56f9e', 'celular', 'iPhone iOS 14.6', 'Safari', 0, '2026-04-20 03:33:17'),
(89, 'e5012c2912243f453782db90059ddbe8354e8034', '/', 'DIF San Mateo Atenco', NULL, 'b137648fceb0a1ce09a3aa6e32ebbff24e67b5d07db5bc72e1a513ef8c23a83c', 'pc', 'Windows 10/11', 'Edge 122.0.0.0', 0, '2026-04-20 03:39:36'),
(90, 'b986d176396bc7112eb4ea08c24ca7000942c85a', '/', 'DIF San Mateo Atenco', NULL, 'ca54f3fe33efadc36044a14accfab94dbd1d344f1356df00251411d9e06f4f25', 'celular', 'Android 8.0.0', 'Chrome 147.0.7727.101', 0, '2026-04-20 04:29:52'),
(91, 'e06111bcfa75481622b0498387434bc742350dbb', '/', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-20 11:28:25'),
(92, 'e06111bcfa75481622b0498387434bc742350dbb', '/', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-20 11:41:08'),
(93, 'e06111bcfa75481622b0498387434bc742350dbb', '/autismo', 'Unidad Municipal de Autismo — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-20 11:44:22'),
(94, 'e06111bcfa75481622b0498387434bc742350dbb', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/autismo', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-20 11:44:37'),
(95, 'e06111bcfa75481622b0498387434bc742350dbb', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-20 11:48:10'),
(96, 'e06111bcfa75481622b0498387434bc742350dbb', '/comunicacion-social/noticias', 'Noticias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/galeria', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-20 11:48:30'),
(97, 'e06111bcfa75481622b0498387434bc742350dbb', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/noticias', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-20 11:48:34'),
(98, 'f24382d131022f4ade1137f4e9d26b6f847b91a1', '/', 'DIF San Mateo Atenco', NULL, 'f8facc4c476f2561fbadb241439f32eef1a308330b7c118fa7f57306de2a175e', 'pc', 'macOS 10.15.7', 'Chrome 147.0.7727.101', 0, '2026-04-21 02:15:13'),
(99, '9f2f8befd9d145115fb64bd72024907905d943f3', '/', 'DIF San Mateo Atenco', NULL, 'ea6bb453b2a76bac0a5e0bb8e5bd0cce92966ce54a9f4da47d629060edd8c838', 'pc', 'Windows 10/11', 'Chrome 147.0.7727.101', 0, '2026-04-21 03:25:13'),
(100, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/transparencia/SEAC', 'SEAC — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-21 10:47:23'),
(101, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/transparencia/SEAC', 'SEAC — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-21 10:49:34'),
(102, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/transparencia/SEAC', 'SEAC — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-21 11:02:30'),
(103, '2dd0cde781ee3eb478a63c9ff4316394dabc2bd9', '/transparencia/cuenta_publica', 'Cuenta Pública — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/SEAC', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-21 11:03:57'),
(104, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-21 11:04:40'),
(105, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/transparencia/presupuesto_anual', 'Presupuesto Anual — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-21 11:04:47'),
(106, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/transparencia/pae', 'Programa Anual de Evaluación — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/presupuesto_anual', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-21 11:06:16'),
(107, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/transparencia/matrices_indicadores', 'Matrices de Indicadores — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/pae', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-21 11:07:07'),
(108, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/transparencia/conac', 'CONAC — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/matrices_indicadores', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-21 11:07:33'),
(109, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/transparencia/financiero', 'Financiero — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/conac', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-21 11:08:08'),
(110, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/transparencia/financiero', 'Financiero — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/conac', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-21 11:10:38'),
(111, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/transparencia/seccion_dinamica?slug=bloque_prueba', 'Bloque prueba — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/financiero', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-21 11:10:50'),
(112, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/transparencia/seccion_dinamica?slug=bloque_prueba', 'Bloque prueba — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/financiero', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-21 11:21:04'),
(113, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/transparencia/seccion_dinamica?slug=bloque_prueba', 'Bloque prueba — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/financiero', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-21 11:21:56'),
(114, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/transparencia/seccion_dinamica?slug=bloque_prueba', 'Bloque prueba — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/financiero', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-21 11:27:02'),
(115, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/transparencia/seccion_dinamica?slug=bloque_prueba', 'Bloque prueba — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/financiero', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-21 11:27:12'),
(116, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/transparencia/seccion_dinamica?slug=bloque_prueba', 'Bloque prueba — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/financiero', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-21 11:32:14'),
(117, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/seccion_dinamica?slug=bloque_prueba', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-21 11:32:52'),
(118, '789ea824af2a43fb874be6cdb8c4d860e17c2534', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 12:25:34'),
(119, '789ea824af2a43fb874be6cdb8c4d860e17c2534', '/programas/seccion?slug=otros-17', 'Otros — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 14:36:14'),
(120, '789ea824af2a43fb874be6cdb8c4d860e17c2534', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=otros-17', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 14:36:17'),
(121, '789ea824af2a43fb874be6cdb8c4d860e17c2534', '/voluntariado', 'Voluntariado — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 14:51:52'),
(122, '789ea824af2a43fb874be6cdb8c4d860e17c2534', '/transparencia/avisos_privacidad', 'Avisos de Privacidad — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/voluntariado', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 14:55:32'),
(123, '5e8c79bddbf15fd6d2238aa697c627d5f8e9703d', '/mantenimiento', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/admin/avisos_privacidad', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 14:56:11'),
(124, '789ea824af2a43fb874be6cdb8c4d860e17c2534', '/transparencia/avisos_privacidad', 'Avisos de Privacidad — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/voluntariado', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 14:56:35'),
(125, '789ea824af2a43fb874be6cdb8c4d860e17c2534', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/avisos_privacidad', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 14:56:37'),
(126, '789ea824af2a43fb874be6cdb8c4d860e17c2534', '/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'Entrega de desayunos fríos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 14:59:01'),
(127, '9434910b16310f849c46d079c4696fc20753dca2', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 17:22:44'),
(128, '9434910b16310f849c46d079c4696fc20753dca2', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 17:23:12'),
(129, '9434910b16310f849c46d079c4696fc20753dca2', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 17:25:29'),
(130, '9434910b16310f849c46d079c4696fc20753dca2', '/mantenimiento', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 17:25:35'),
(131, '9434910b16310f849c46d079c4696fc20753dca2', '/mantenimiento', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 17:26:19'),
(132, '9434910b16310f849c46d079c4696fc20753dca2', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/mantenimiento', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 17:26:27'),
(133, '9434910b16310f849c46d079c4696fc20753dca2', '/autismo', 'Unidad Municipal de Autismo — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 17:26:38'),
(134, '9434910b16310f849c46d079c4696fc20753dca2', '/tramites/DIFCDTI', 'Caravana de Servicios DIF CERCA DE TI — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 17:27:03'),
(135, 'a5887c18bcc0ba21464e6ee5bb795c1f2f0898cb', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 17:28:21'),
(136, 'a5887c18bcc0ba21464e6ee5bb795c1f2f0898cb', '/mantenimiento', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 17:28:29'),
(137, 'd690394e8a03960d1c4cafe19e86ea677d9f3ef8', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 17:28:45'),
(138, 'd690394e8a03960d1c4cafe19e86ea677d9f3ef8', '/tramites/DAD', 'Dirección de Atención a la Discapacidad — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 17:28:49'),
(139, 'd690394e8a03960d1c4cafe19e86ea677d9f3ef8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DAD', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 17:28:52'),
(140, 'd690394e8a03960d1c4cafe19e86ea677d9f3ef8', '/acerca-del-dif/direcciones', 'Direcciones — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 17:34:55'),
(141, 'd690394e8a03960d1c4cafe19e86ea677d9f3ef8', '/acerca-del-dif/direcciones', 'Direcciones — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 17:37:23'),
(142, 'd690394e8a03960d1c4cafe19e86ea677d9f3ef8', '/acerca-del-dif/organigrama', 'Organigrama — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/direcciones', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 17:53:11'),
(143, 'd690394e8a03960d1c4cafe19e86ea677d9f3ef8', '/acerca-del-dif/direcciones', 'Direcciones — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/organigrama', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 17:53:33'),
(144, 'd690394e8a03960d1c4cafe19e86ea677d9f3ef8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/direcciones', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 17:58:35'),
(145, 'd690394e8a03960d1c4cafe19e86ea677d9f3ef8', '/programas/seccion?slug=jornadas-de-mastograf-as-21', 'Jornadas de mastografías — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 18:01:03'),
(146, 'd690394e8a03960d1c4cafe19e86ea677d9f3ef8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=jornadas-de-mastograf-as-21', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-21 18:01:05'),
(147, '78707b3ad8f38cc67ccf736591db2ef1327b7a47', '/', 'DIF San Mateo Atenco', NULL, 'f8facc4c476f2561fbadb241439f32eef1a308330b7c118fa7f57306de2a175e', 'celular', 'iPhone iOS 14.6', 'Safari', 0, '2026-04-22 01:25:46'),
(148, 'ae3ef5256684b4a3909cffcab9fac695835af7ce', '/', 'DIF San Mateo Atenco', NULL, 'd72fcec77054e04787592630edfec791e0ca16cf76801a7b874efacbccb59445', 'celular', 'Android 8.0.0', 'Chrome 147.0.7727.101', 0, '2026-04-22 02:41:25'),
(149, '00a777f5ed629532d1471d292b3adcc17de14bfa', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 09:44:28'),
(150, '00a777f5ed629532d1471d292b3adcc17de14bfa', '/mantenimiento', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 09:44:38'),
(151, '00a777f5ed629532d1471d292b3adcc17de14bfa', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/mantenimiento', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 09:44:48'),
(152, '383249a90ac81a5c937ce730948e5b3765600898', '/mantenimiento', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/admin/dashboard', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 09:47:49'),
(153, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 11:21:22'),
(154, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', 'Detección y prevención de niñas, niños y adolescentes en situación de calle — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 11:32:45'),
(155, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', 'Detección y prevención de niñas, niños y adolescentes en situación de calle — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 11:35:45'),
(156, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', 'Detección y prevención de niñas, niños y adolescentes en situación de calle — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 11:37:10'),
(157, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', 'Prevención y erradicación del trabajo infantil — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 11:37:33'),
(158, '9470933ce5e51d9568d683683face42460c8c052', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 11:37:55'),
(159, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', 'Prevención y erradicación del trabajo infantil — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 11:57:38');
INSERT INTO `visitor_analytics` (`id`, `session_id`, `pagina`, `titulo`, `referrer`, `ip_hash`, `dispositivo`, `os`, `navegador`, `es_bot`, `created_at`) VALUES
(160, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', 'Prevención y erradicación del trabajo infantil — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 11:58:25'),
(161, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 11:58:41'),
(162, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-embarazo-adolescente-14', 'Promoción del respeto a los derechos laborales de los adolescentes trabajadores en edad permitida — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 11:58:46'),
(163, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-embarazo-adolescente-14', 'Promoción del respeto a los derechos laborales de los adolescentes trabajadores en edad permitida — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:00:11'),
(164, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:01:54'),
(165, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'Entrega de desayunos escolares fríos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:01:58'),
(166, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'Entrega de desayunos escolares fríos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:03:18'),
(167, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:07:56'),
(168, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=consultas-psicol-gicas-20', 'Consultas psicológicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:08:05'),
(169, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=consultas-psicol-gicas-20', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:08:12'),
(170, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'Entrega de desayunos escolares fríos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:08:18'),
(171, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'Entrega de desayunos escolares fríos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:18:44'),
(172, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'Entrega de desayunos escolares fríos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:20:44'),
(173, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=consultas-psicol-gicas-20', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:23:10'),
(174, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=entrega-de-desayunos-calientes-15', 'Entrega de desayunos escolares calientes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:23:14'),
(175, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=entrega-de-desayunos-calientes-15', 'Entrega de desayunos escolares calientes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:30:52'),
(176, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=entrega-de-desayunos-calientes-15', 'Entrega de desayunos escolares calientes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:31:42'),
(177, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'Entrega de desayunos escolares fríos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:31:54'),
(178, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=entrega-de-desayunos-calientes-15', 'Entrega de desayunos escolares calientes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:32:52'),
(179, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=creaci-n-de-huertos-escolares-y-familiares-15', 'Creación de huertos escolares y familiares — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:36:54'),
(180, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=creaci-n-de-huertos-escolares-y-familiares-15', 'Creación de huertos escolares y familiares — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:36:59'),
(181, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=creaci-n-de-huertos-escolares-y-familiares-15', 'Creación de huertos escolares y familiares — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:38:22'),
(182, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=creaci-n-de-huertos-escolares-y-familiares-15', 'Creación de huertos escolares y familiares — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:42:06'),
(183, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=creaci-n-de-huertos-escolares-y-familiares-15', 'Creación de huertos escolares y familiares — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:42:57'),
(184, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=creaci-n-de-huertos-escolares-y-familiares-15', 'Creación de huertos escolares y familiares — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:43:20'),
(185, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=creaci-n-de-huertos-escolares-y-familiares-15', 'Creación de huertos escolares y familiares — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:52:29'),
(186, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=creaci-n-de-huertos-escolares-y-familiares-15', 'Creación de huertos escolares y familiares — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:54:02'),
(187, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=consultas-psicol-gicas-20', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:59:30'),
(188, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=programa-canasta-alimentaria-del-bienestar-15', 'Programa Canasta Alimentaria del Bienestar — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 12:59:33'),
(189, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=programa-canasta-alimentaria-del-bienestar-15', 'Programa Canasta Alimentaria del Bienestar — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:09:47'),
(190, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=programa-canasta-alimentaria-del-bienestar-15', 'Programa Canasta Alimentaria del Bienestar — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:10:28'),
(191, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=consultas-psicol-gicas-20', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:10:41'),
(192, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías jurídicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:12:07'),
(193, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías jurídicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:13:15'),
(194, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías jurídicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:13:55'),
(195, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=procedimientos-judiciales-16', 'Procedimientos judiciales — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:19:06'),
(196, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=procedimientos-judiciales-16', 'Procedimientos judiciales — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:20:31'),
(197, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=procedimientos-judiciales-16', 'Procedimientos judiciales — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:22:56'),
(198, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=servicios-17', 'Servicios — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:23:44'),
(199, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías jurídicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:23:51'),
(200, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=procedimientos-judiciales-16', 'Procedimientos judiciales — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:23:56'),
(201, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=servicios-17', 'Servicios — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:24:03'),
(202, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=servicios-17', 'Servicios — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:26:59'),
(203, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=vacunas-y-medicamentos-17', 'Vacunas y medicamentos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:27:35'),
(204, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=vacunas-y-medicamentos-17', 'Vacunas y medicamentos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:32:12'),
(205, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=otros-17', 'Otros — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:32:22'),
(206, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=servicios-17', 'Servicios — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:38:08'),
(207, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=servicios-17', 'Servicios — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:48:47'),
(208, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=servicios-17', 'Servicios — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:49:15'),
(209, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=servicios-17', 'Servicios — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:49:17'),
(210, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=servicios-17', 'Servicios — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:49:44'),
(211, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=servicios-17', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:51:08'),
(212, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'Entrega de desayunos escolares fríos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:51:32'),
(213, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', 'Detección y prevención de niñas, niños y adolescentes en situación de calle — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:52:20'),
(214, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', 'Prevención y erradicación del trabajo infantil — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:52:25'),
(215, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-embarazo-adolescente-14', 'Promoción del respeto a los derechos laborales de los adolescentes trabajadores en edad permitida — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:52:39'),
(216, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=servicios-17', 'Servicios — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 13:53:12'),
(217, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=servicios-17', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 14:03:20'),
(218, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=vacunas-y-medicamentos-17', 'Vacunas y medicamentos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 14:03:24'),
(219, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=vacunas-y-medicamentos-17', 'Vacunas y medicamentos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 14:08:27'),
(220, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=otros-17', 'Otros — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 14:09:57'),
(221, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=servicios-17', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 14:11:19'),
(222, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=centro-de-rehabilitaci-n-e-integraci-n-social-cris-18', 'Centro de Rehabilitación e Integración Social CRIS — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 14:13:08'),
(223, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=cuarto-de-estimulaci-n-multisensorial-18', 'Cuarto de Estimulación Multisensorial — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 14:14:04'),
(224, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=terapias-18', 'Terapias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 14:14:36'),
(225, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=terapias-18', 'Terapias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 14:15:08'),
(226, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=actividades-culturales-y-recreativas-18', 'Actividades culturales y recreativas. — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 14:15:53'),
(227, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=actividades-culturales-y-recreativas-18', 'Actividades culturales y recreativas. — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 14:16:20'),
(228, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=programas-y-talleres-18', 'Programas y talleres. — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 14:17:34'),
(229, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=programas-y-talleres-18', 'Programas y talleres. — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 14:17:39'),
(230, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=programas-y-talleres-18', 'Programas y talleres. — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 14:25:30'),
(231, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías jurídicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 14:26:28'),
(232, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=procedimientos-judiciales-16', 'Procedimientos judiciales — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 14:26:52'),
(233, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías jurídicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 17:27:49'),
(234, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', 'Detección y prevención de niñas, niños y adolescentes en situación de calle — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 17:28:08'),
(235, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', 'Prevención y erradicación del trabajo infantil — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 17:28:23'),
(236, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-embarazo-adolescente-14', 'Promoción del respeto a los derechos laborales de los adolescentes trabajadores en edad permitida — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 17:28:31'),
(237, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=procedimientos-judiciales-16', 'Procedimientos judiciales — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 17:28:38'),
(238, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías jurídicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 17:29:46'),
(239, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=servicios-17', 'Servicios — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 17:30:31'),
(240, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'Entrega de desayunos escolares fríos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 17:31:34'),
(241, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías jurídicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 17:32:05'),
(242, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', 'Prevención y erradicación del trabajo infantil — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 17:32:30'),
(243, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=procedimientos-judiciales-16', 'Procedimientos judiciales — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 17:34:39'),
(244, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=procedimientos-judiciales-16', 'Procedimientos judiciales — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 17:36:52'),
(245, 'fc07c3e84d4fb35290988f2b193e298c2a345b97', '/programas/seccion?slug=centro-de-rehabilitaci-n-e-integraci-n-social-cris-18', 'Centro de Rehabilitación e Integración Social CRIS — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-22 17:44:04'),
(246, 'a49046fae8883f675b67e848b431256be9a4bffa', '/', 'DIF San Mateo Atenco', NULL, '13c96fecb3c606ec05dd81a20749eb5f0b7085a408df04326b54d1923fc9cae6', 'pc', 'Windows 10/11', 'Chrome 147.0.7727.101', 0, '2026-04-23 00:35:06'),
(247, '64dfbfb11ea18279e10c02d925200068b443b487', '/', 'DIF San Mateo Atenco', NULL, 'bb4cd5ba98751c5f8089a9675832486dcc7b4497ba9822de7f6b4f6cc3535b28', 'pc', 'Windows 10/11', 'Chrome 147.0.7727.101', 0, '2026-04-23 01:34:18'),
(248, '6c463531df0fac323c07419eb196088aa66600b4', '/', 'DIF San Mateo Atenco', NULL, '52a1a14d6f48dc218af586212a868c3c0e74c2358c9973f3e887ffb83960a193', 'pc', 'Windows 10/11', 'Edge 122.0.0.0', 0, '2026-04-23 13:15:05'),
(249, '3800c12294d8c92bde27ee7887742a564cec759d', '/', 'DIF San Mateo Atenco', NULL, 'ff36e0cb35401023a24f4d25c756f3074bd22dbbffd3f65aae96e1e5362b774a', 'celular', 'iPhone iOS 14.6', 'Safari', 0, '2026-04-23 23:24:46'),
(250, '0e70bfcc403ec4e97d3782ba8781c608b4995c82', '/', 'DIF San Mateo Atenco', NULL, '7a1aa75bd072459454bb4ea4b5687069fd61381e63edf094b4bd76010167f2e7', 'celular', 'Android 8.0.0', 'Chrome 147.0.7727.101', 0, '2026-04-24 00:45:04'),
(251, '615e5ffa6fdde9eaa4d107a7fa0c7916278a80e7', '/', 'DIF San Mateo Atenco', NULL, '59ccab0be046b56cf7f854fe137f99ce6fba0b0bec4d60f49bf2e94fc832c8ff', 'pc', 'Windows 10/11', 'Chrome 147.0.7727.101', 0, '2026-04-24 22:16:21'),
(252, 'efa7ea90ec124d9810f1aa78d7c9ab5d8bf3db45', '/', 'DIF San Mateo Atenco', NULL, '1b097498af331f76e1f7e8e9393665045a6cdd77ffeabb73baac01e4ebff1c62', 'pc', 'Windows 10/11', 'Chrome 147.0.7727.101', 0, '2026-04-24 23:35:13'),
(253, 'e418e6c15528caa05d2841e9670d1f7f49825c67', '/', 'DIF San Mateo Atenco', NULL, '4af4a3dd055ba96e29d2bc263dba23b340e584ea9371e0cac56b49dddf80c87d', 'celular', 'Android 8.0.0', 'Chrome 147.0.7727.101', 0, '2026-04-25 21:41:10'),
(254, 'e5a0dbddfa0ce331ab817b65b5b7b9806ead5a5f', '/', 'DIF San Mateo Atenco', NULL, 'ea1dc283330168741b9b3e81a7a8c0439d1c75908b9e62cb38acde2a1550033e', 'celular', 'Android 8.0.0', 'Chrome 147.0.7727.101', 0, '2026-04-25 22:35:47'),
(255, '240740435d9e977aa45a45d670f1a2d3f22bc0ff', '/', 'DIF San Mateo Atenco', NULL, 'cfdc8f60e983a57c11911e850c86fbd145ab27ae7b83806a4bc97e0c0da44805', 'pc', 'Windows 10/11', 'Chrome 147.0.7727.101', 0, '2026-04-26 20:32:16'),
(256, '1ffd080da4c19231abff8d320bb82af97426be09', '/', 'DIF San Mateo Atenco', NULL, '071efb89090df062dff6b26ba62df98894d144a7f28bcc99c1cb6589034d3dfd', 'pc', 'macOS 10.15.7', 'Chrome 147.0.7727.101', 0, '2026-04-26 21:38:10'),
(257, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 08:23:00'),
(258, '488aa16999b39834c9cd918234e6edbf61d96f06', '/', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 10:14:34'),
(259, '488aa16999b39834c9cd918234e6edbf61d96f06', '/comunicacion-social/noticias', 'Noticias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 10:15:26'),
(260, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=centro-de-rehabilitaci-n-e-integraci-n-social-cris-18', 'Centro de Rehabilitación e Integración Social CRIS — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 10:32:51'),
(261, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=centro-de-rehabilitaci-n-e-integraci-n-social-cris-18', 'Centro de Rehabilitación e Integración Social CRIS — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 10:37:25'),
(262, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=cuarto-de-estimulaci-n-multisensorial-18', 'Cuarto de Estimulación Multisensorial — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 10:37:44'),
(263, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=cuarto-de-estimulaci-n-multisensorial-18', 'Cuarto de Estimulación Multisensorial — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 10:39:09'),
(264, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=terapias-18', 'Terapias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 10:39:32'),
(265, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=terapias-18', 'Terapias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 10:42:11'),
(266, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=actividades-culturales-y-recreativas-18', 'Actividades culturales y recreativas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 10:42:41'),
(267, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=actividades-culturales-y-recreativas-18', 'Actividades culturales y recreativas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 10:44:46'),
(268, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=programas-y-talleres-18', 'Programas y talleres — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 10:45:00'),
(269, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=programas-y-talleres-18', 'Programas y talleres — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 10:46:22'),
(270, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 10:56:05'),
(271, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:01:31'),
(272, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:01:43'),
(273, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:03:25'),
(274, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=consultas-psicol-gicas-20', 'Consultas psicológicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:05:02'),
(275, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=consultas-psicol-gicas-20', 'Consultas psicológicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:07:31'),
(276, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=talleres-20', 'Talleres — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:11:09'),
(277, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=jornadas-informativas-escolares-20', 'Jornadas informativas escolares — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:11:42'),
(278, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=jornadas-informativas-escolares-20', 'Jornadas informativas escolares — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:13:05'),
(279, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=jornadas-informativas-escolares-20', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:13:14'),
(280, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=jornadas-de-mastograf-as-21', 'Jornadas de mastografías — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:13:34'),
(281, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=jornadas-de-mastograf-as-21', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:16:49'),
(282, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías jurídicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:16:57'),
(283, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=asesor-as-judiciales-16', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:17:04'),
(284, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=servicios-17', 'Servicios — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:18:59'),
(285, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=servicios-17', 'Servicios — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:21:17'),
(286, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=servicios-17', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:21:24'),
(287, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:21:38'),
(288, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:21:42'),
(289, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', 'Detección y prevención de niñas, niños y adolescentes en situación de calle — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:21:52'),
(290, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', 'Prevención y erradicación del trabajo infantil — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:21:57'),
(291, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', 'Prevención y erradicación del trabajo infantil — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:23:23'),
(292, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-embarazo-adolescente-14', 'Promoción del respeto a los derechos laborales de los adolescentes trabajadores en edad permitida — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:23:30'),
(293, 'ac2a38e9b74c7af2b38c0fab613666b94d49dca6', '/', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'celular', 'iPhone iOS 26.4.1', 'Safari', 0, '2026-04-27 11:36:33'),
(294, 'ac2a38e9b74c7af2b38c0fab613666b94d49dca6', '/', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'celular', 'iPhone iOS 26.4.1', 'Safari', 0, '2026-04-27 11:40:16'),
(295, 'ac2a38e9b74c7af2b38c0fab613666b94d49dca6', '/', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'celular', 'iPhone iOS 26.4.1', 'Safari', 0, '2026-04-27 11:42:23'),
(296, 'ac2a38e9b74c7af2b38c0fab613666b94d49dca6', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'celular', 'iPhone iOS 26.4.1', 'Safari', 0, '2026-04-27 11:43:09'),
(297, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=servicios-17', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:43:28'),
(298, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=jornadas-de-mastograf-as-21', 'Jornadas de mastografías — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:43:31'),
(299, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=jornadas-de-mastograf-as-21', 'Jornadas de mastografías — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:44:27'),
(300, 'ac2a38e9b74c7af2b38c0fab613666b94d49dca6', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'celular', 'iPhone iOS 26.4.1', 'Safari', 0, '2026-04-27 11:44:54'),
(301, 'ac2a38e9b74c7af2b38c0fab613666b94d49dca6', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'celular', 'iPhone iOS 26.4.1', 'Safari', 0, '2026-04-27 11:44:56'),
(302, '8f64d84d80f591593efb45f987e3c5b8be623f6c', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:46:04'),
(303, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=carril-rosa-21', 'Carril Rosa — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:46:28'),
(304, 'ac2a38e9b74c7af2b38c0fab613666b94d49dca6', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'celular', 'iPhone iOS 26.4.1', 'Safari', 0, '2026-04-27 11:46:33'),
(305, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=orientaci-n-y-acompa-amiento-21', 'Orientación y acompañamiento. — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:46:41'),
(306, '53fa7814be53fc2491f4eae937d2ea8ba1d6ca78', '/', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:47:38'),
(307, '53fa7814be53fc2491f4eae937d2ea8ba1d6ca78', '/comunicacion-social/noticias', 'Noticias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:47:56'),
(308, '53fa7814be53fc2491f4eae937d2ea8ba1d6ca78', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/noticias', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:48:11');
INSERT INTO `visitor_analytics` (`id`, `session_id`, `pagina`, `titulo`, `referrer`, `ip_hash`, `dispositivo`, `os`, `navegador`, `es_bot`, `created_at`) VALUES
(309, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=orientaci-n-y-acompa-amiento-21', 'Orientación y acompañamiento. — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:49:02'),
(310, 'f744e64486b80fc206d4cf16b70086508c4f0b37', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:52:31'),
(311, 'f744e64486b80fc206d4cf16b70086508c4f0b37', '/mantenimiento', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:52:36'),
(312, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=orientaci-n-y-acompa-amiento-21', 'Orientación y acompañamiento. — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:56:18'),
(313, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=casas-de-d-a-22', 'Casas de día — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 11:57:28'),
(314, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=casas-de-d-a-22', 'Casas de día — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:00:29'),
(315, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=casas-de-d-a-22', 'Casas de día — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:01:34'),
(316, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=atenci-n-integral-a-la-salud-22', 'Atención integral a la salud — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:01:56'),
(317, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/seccion_dinamica?slug=bloque_prueba', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:03:04'),
(318, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/comunicacion-social/noticias', 'Noticias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:03:19'),
(319, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=atenci-n-integral-a-la-salud-22', 'Atención integral a la salud — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:03:48'),
(320, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=clases-y-talleres-22', 'Clases y talleres — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:03:59'),
(321, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/comunicacion-social/noticias', 'Noticias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:04:36'),
(322, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/noticias', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:04:57'),
(323, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=clases-y-talleres-22', 'Clases y talleres — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:06:05'),
(324, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/comunicacion-social/noticias', 'Noticias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:06:07'),
(325, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=paseos-recreativos-22', 'Paseos recreativos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:06:47'),
(326, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=paseos-recreativos-22', 'Paseos recreativos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:08:08'),
(327, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=visitas-culturales-22', 'Visitas culturales — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:08:21'),
(328, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=visitas-culturales-22', 'Visitas culturales — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:11:03'),
(329, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/comunicacion-social/noticias', 'Noticias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:11:05'),
(330, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/noticias', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:11:10'),
(331, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=celebraciones-22', 'Celebraciones — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:11:16'),
(332, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/comunicacion-social/noticias', 'Noticias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:11:31'),
(333, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=celebraciones-22', 'Celebraciones — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:13:37'),
(334, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/noticias', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:13:59'),
(335, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=servicios-17', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:17:18'),
(336, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=casas-de-d-a-22', 'Casas de Día — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:17:43'),
(337, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=casas-de-d-a-22', 'Casas de Día — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:21:57'),
(338, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=casas-de-d-a-22', 'Casas de Día — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:22:14'),
(339, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=casas-de-d-a-22', 'Casas de Día — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:26:16'),
(340, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=casas-de-d-a-22', 'Casas de Día — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:27:31'),
(341, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=casas-de-d-a-22', 'Casas de Día — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:27:54'),
(342, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/programas/seccion?slug=casas-de-d-a-22', 'Casas de Día — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:28:12'),
(343, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=casas-de-d-a-22', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:28:19'),
(344, '0c74f76b52b46a7465692bbca7e3aca6bc4d648d', '/mantenimiento', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/admin/galeria?album_id=3', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:28:29'),
(345, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/noticias', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:33:36'),
(346, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:33:48'),
(347, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/direcciones', 'Direcciones — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:33:51'),
(348, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/organigrama', 'Organigrama — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/direcciones', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:33:54'),
(349, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/organigrama', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:33:59'),
(350, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=casas-de-d-a-22', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:35:55'),
(351, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:36:53'),
(352, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/organigrama', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:37:18'),
(353, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/organigrama', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:37:26'),
(354, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:37:28'),
(355, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/tramites/DAAM', 'Dirección de Atención a Adultos Mayores — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/PMPNNA', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:37:32'),
(356, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/organigrama', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:39:20'),
(357, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:39:26'),
(358, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:39:33'),
(359, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:42:24'),
(360, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:43:06'),
(361, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:44:53'),
(362, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:45:10'),
(363, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:45:15'),
(364, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:45:19'),
(365, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/direcciones', 'Direcciones — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:45:24'),
(366, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/organigrama', 'Organigrama — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/direcciones', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:45:40'),
(367, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/organigrama', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:47:44'),
(368, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/organigrama', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:50:56'),
(369, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/tramites/DANF', 'Dirección de Alimentación y Nutrición Familiar — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DAAM', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:52:46'),
(370, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/organigrama', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:53:06'),
(371, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/organigrama', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:53:10'),
(372, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/tramites/DAD', 'Dirección de Atención a la Discapacidad — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DANF', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:53:10'),
(373, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:53:16'),
(374, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/direcciones', 'Direcciones — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:53:21'),
(375, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/tramites/DPAF', 'Dirección de Prevención y Bienestar Familiar — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DAD', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:53:32'),
(376, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/organigrama', 'Organigrama — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/direcciones', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:53:42'),
(377, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/organigrama', 'Organigrama — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/direcciones', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:53:49'),
(378, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/organigrama', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:53:52'),
(379, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/tramites/DSJAIG', 'Dirección de Servicios Jurídicos – Asistenciales e Igualdad de Género — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DPAF', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:54:17'),
(380, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/comunicacion-social/noticias', 'Noticias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/PMPNNA', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:54:21'),
(381, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/comunicacion-social/noticias', 'Noticias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/PMPNNA', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:54:32'),
(382, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/noticias', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:54:38'),
(383, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/tramites/DIFCDTI', 'Caravana de Servicios DIF CERCA DE TI — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DSJAIG', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:54:43'),
(384, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/transparencia/SEAC', 'SEAC — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/galeria', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:54:56'),
(385, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/tramites/DSJAIG', 'Dirección de Servicios Jurídicos – Asistenciales e Igualdad de Género — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DIFCDTI', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:54:57'),
(386, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/tramites/CAPC', 'Coordinación de Atención a Pacientes con Cáncer — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DSJAIG', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:55:04'),
(387, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/transparencia/SEAC', 'SEAC — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/galeria', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:55:07'),
(388, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/transparencia/avisos_privacidad', 'Avisos de Privacidad — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/SEAC', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:55:17'),
(389, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/autismo', 'Unidad Municipal de Autismo — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/CAPC', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:55:28'),
(390, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/voluntariado', 'Voluntariado — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/avisos_privacidad', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:55:28'),
(391, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/voluntariado', 'Voluntariado — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/avisos_privacidad', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:55:37'),
(392, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/voluntariado', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:56:56'),
(393, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:57:15'),
(394, 'e233ecfc5b0410e67030f080dec0ddd803784e03', '/voluntariado', 'Voluntariado — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/CAPC', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 12:57:36'),
(395, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:59:07'),
(396, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/tramites/DAD', 'Dirección de Atención a la Discapacidad — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 12:59:09'),
(397, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/tramites/DAD', 'Dirección de Atención a la Discapacidad — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:01:12'),
(398, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DAD', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:01:19'),
(399, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DAD', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:06:45'),
(400, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DAD', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:08:29'),
(401, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DAD', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:08:42'),
(402, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:08:48'),
(403, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:08:57'),
(404, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/direcciones', 'Direcciones — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:09:03'),
(405, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/direcciones', 'Direcciones — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:09:18'),
(406, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/organigrama', 'Organigrama — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/direcciones', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:09:25'),
(407, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/organigrama', 'Organigrama — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/direcciones', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:09:32'),
(408, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/organigrama', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:09:40'),
(409, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/organigrama', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:09:48'),
(410, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/tramites/DAAM', 'Dirección de Atención a Adultos Mayores — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/PMPNNA', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:09:57'),
(411, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/comunicacion-social/noticias', 'Noticias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DAAM', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:10:29'),
(412, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/comunicacion-social/noticias', 'Noticias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DAAM', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:10:36'),
(413, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/noticias', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:10:40'),
(414, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/noticias', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:10:48'),
(415, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/transparencia/SEAC', 'SEAC — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/galeria', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:10:53'),
(416, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/voluntariado', 'Voluntariado — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/SEAC', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:11:05'),
(417, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/voluntariado', 'Voluntariado — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/SEAC', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:13:56'),
(418, '072e70a023a3f95ca83429fe9cf9def57df64dd4', '/mantenimiento', 'En Mantenimiento — DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:14:34'),
(419, '53fa7814be53fc2491f4eae937d2ea8ba1d6ca78', '/', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 13:19:56'),
(420, '53fa7814be53fc2491f4eae937d2ea8ba1d6ca78', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 13:20:06'),
(421, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/voluntariado', 'Voluntariado — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/SEAC', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:22:26'),
(422, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/voluntariado', 'Voluntariado — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/SEAC', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:25:03'),
(423, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/autismo', 'Unidad Municipal de Autismo — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/voluntariado', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:25:10'),
(424, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/autismo', 'Unidad Municipal de Autismo — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/voluntariado', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:26:06'),
(425, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/autismo', 'Unidad Municipal de Autismo — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/voluntariado', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:26:09'),
(426, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/autismo', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/voluntariado', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:28:37'),
(427, 'c9587a912c520d270edea854c5d917bc32be802f', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 13:37:40'),
(428, 'c9587a912c520d270edea854c5d917bc32be802f', '/autismo', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 13:37:43'),
(429, 'c9587a912c520d270edea854c5d917bc32be802f', '/tramites/DIFCDTI', 'Caravana de Servicios DIF CERCA DE TI — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/autismo', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 13:37:53'),
(430, 'c9587a912c520d270edea854c5d917bc32be802f', '/acerca-del-dif/direcciones', 'Direcciones — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DIFCDTI', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 13:38:04'),
(431, 'c9587a912c520d270edea854c5d917bc32be802f', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/direcciones', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 13:38:28'),
(432, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/autismo', 'Unidad Municipal de Autismo — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/voluntariado', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:40:29'),
(433, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/autismo', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/voluntariado', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:41:28'),
(434, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/organigrama', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/autismo', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:42:22'),
(435, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/organigrama', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/autismo', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:45:29'),
(436, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/presidencia', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/organigrama', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:45:51'),
(437, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/direcciones', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:45:54'),
(438, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/direcciones', 'Direcciones — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:46:08'),
(439, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/direcciones', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:46:12'),
(440, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/acerca-del-dif/organigrama', 'Organigrama — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:46:14'),
(441, '8d9b72c9ec1e9be373faa28e08eec75d810cb34a', '/mantenimiento', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/admin/mantenimiento', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:47:00'),
(442, '53fa7814be53fc2491f4eae937d2ea8ba1d6ca78', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 13:52:03'),
(443, '53fa7814be53fc2491f4eae937d2ea8ba1d6ca78', '/comunicacion-social/noticias', 'Noticias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/galeria', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-27 13:52:24'),
(444, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/autismo', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/organigrama', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 13:54:22'),
(445, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/autismo', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 14:04:48'),
(446, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/tramites/PMPNNA', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 14:15:31'),
(447, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 14:15:50'),
(448, '8c6ee83d5f52fe8c1167f58bc1809f604c242da8', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/PMPNNA', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-27 14:16:02'),
(449, 'e958a7f535cbbb0f8eded3d32d43e32d6e17aaa5', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', '2e54545196a1ae00bb0d53af8d21a57750a51cfff91702f57e428e6fe36bc81b', 'celular', 'iPhone iOS 26.4.1', 'Safari', 0, '2026-04-27 19:05:33'),
(450, '61143bbf06a1253936f194abe427a1e2737d9636', '/', 'DIF San Mateo Atenco', NULL, '432ac610b10678fe70a39c2d749ea1eb7350e3af4e166f6f8099d4fc3d99f819', 'celular', 'Android 8.0.0', 'Chrome 147.0.7727.116', 0, '2026-04-27 19:31:03'),
(451, '99cbb0c208a86cbca29084215b1776a800dc5eb7', '/', 'DIF San Mateo Atenco', NULL, '6a989b9a5859045b01c7d1deb601722aa11dbb4d00533d9161cfaeb8f0c4db87', 'celular', 'Android 8.0.0', 'Chrome 147.0.7727.116', 0, '2026-04-27 20:48:16'),
(452, '82907ea52a551f13339772c945da14916558aa3f', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-28 17:08:05'),
(453, '82907ea52a551f13339772c945da14916558aa3f', '/programas/seccion?slug=terapias-18', 'Terapias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-28 17:08:37'),
(454, '7c789d6f77409f126cbbbf298c426029503051e9', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-28 18:14:36'),
(455, '7c789d6f77409f126cbbbf298c426029503051e9', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-28 18:14:48'),
(456, '0cc7e7dab0726f5bef4a917175109b15732e6c9f', '/', 'DIF San Mateo Atenco', NULL, '99547fdf71a2d29e6f3ef31cda74d9eb00c151df55e78eee6b7cba575a693aa9', 'pc', 'macOS 10.15.7', 'Chrome 147.0.7727.116', 0, '2026-04-28 18:27:49'),
(457, '5346b51a5dc90e9a4a301ed27e8a509d13f368ab', '/', 'DIF San Mateo Atenco', NULL, '01af1ea699cd233c8258a28a7a5b661d4d07907008aff8d0ebf5b1659302c632', 'pc', 'macOS 10.15.7', 'Chrome 147.0.7727.116', 0, '2026-04-28 20:01:18'),
(458, '4508e8ab32c76382522905de6b0ce97f9e021f69', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'celular', 'iPhone iOS 26.4.2', 'Safari', 0, '2026-04-29 10:57:44'),
(459, '19a568ad63d35453eb3402e2f596baafcc73b1bf', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-29 11:49:29'),
(460, '19a568ad63d35453eb3402e2f596baafcc73b1bf', '/transparencia/pae', 'Programa Anual de Evaluación — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-29 11:49:35'),
(461, '19a568ad63d35453eb3402e2f596baafcc73b1bf', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/pae', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-29 11:49:50'),
(462, '19a568ad63d35453eb3402e2f596baafcc73b1bf', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-29 11:50:07'),
(463, '19a568ad63d35453eb3402e2f596baafcc73b1bf', '/acerca-del-dif/direcciones', 'Direcciones — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-29 11:50:17'),
(464, '19a568ad63d35453eb3402e2f596baafcc73b1bf', '/autismo', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/direcciones', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-29 11:50:33'),
(465, '19a568ad63d35453eb3402e2f596baafcc73b1bf', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/autismo', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-29 11:50:41'),
(466, '19a568ad63d35453eb3402e2f596baafcc73b1bf', '/voluntariado', 'Voluntariado — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/galeria', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-29 11:50:45'),
(467, 'c349898d0c0ca847b881e5748593367ba90cc014', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-29 16:46:00');
INSERT INTO `visitor_analytics` (`id`, `session_id`, `pagina`, `titulo`, `referrer`, `ip_hash`, `dispositivo`, `os`, `navegador`, `es_bot`, `created_at`) VALUES
(468, 'c184b75025f3e38f23bd0f9f7cba9a665071e6ea', '/', 'DIF San Mateo Atenco', NULL, '1f2a4c3968b2423cbab96e0d86cbd1ad9a828a808b761f749a7d5319bc156861', 'pc', 'macOS 10.15.7', 'Chrome 147.0.7727.116', 0, '2026-04-29 17:29:20'),
(469, '362c821a6e336d07142c7ea858e277bf3fc1db30', '/index', 'DIF San Mateo Atenco', NULL, 'f174127ab4b3bbd539e6c178c43a0ecd1a3fca3a5c1827e9d9d16ba8f8ef5de4', 'celular', 'iPhone iOS 18.7', 'Safari', 0, '2026-04-29 17:33:23'),
(470, '362c821a6e336d07142c7ea858e277bf3fc1db30', '/tramites/DAAM', 'Dirección de Atención a Adultos Mayores — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'f174127ab4b3bbd539e6c178c43a0ecd1a3fca3a5c1827e9d9d16ba8f8ef5de4', 'celular', 'iPhone iOS 18.7', 'Safari', 0, '2026-04-29 17:33:42'),
(471, '362c821a6e336d07142c7ea858e277bf3fc1db30', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DAAM', 'd4fd5b8c0a548ddebe5604e7294f446e2705677e2ae5c067545a4dcd9f1480e9', 'celular', 'iPhone iOS 18.7', 'Safari', 0, '2026-04-29 17:34:48'),
(472, 'c349898d0c0ca847b881e5748593367ba90cc014', '/comunicacion-social/noticias', 'Noticias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-29 17:35:04'),
(473, 'c349898d0c0ca847b881e5748593367ba90cc014', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/noticias', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-29 17:35:06'),
(474, '9df0729e650e770bd076ffb43b31884de5f071f6', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:37:59'),
(475, '9df0729e650e770bd076ffb43b31884de5f071f6', '/mantenimiento', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:38:33'),
(476, '9df0729e650e770bd076ffb43b31884de5f071f6', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:39:54'),
(477, '9df0729e650e770bd076ffb43b31884de5f071f6', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:46:23'),
(478, '9df0729e650e770bd076ffb43b31884de5f071f6', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', 'Prevención y erradicación del trabajo infantil — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:46:37'),
(479, '9df0729e650e770bd076ffb43b31884de5f071f6', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:47:19'),
(480, '9df0729e650e770bd076ffb43b31884de5f071f6', '/tramites/DANF', 'Dirección de Alimentación y Nutrición Familiar — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:47:28'),
(481, '9df0729e650e770bd076ffb43b31884de5f071f6', '/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'Entrega de desayunos escolares fríos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:48:36'),
(482, '9df0729e650e770bd076ffb43b31884de5f071f6', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:49:08'),
(483, '9df0729e650e770bd076ffb43b31884de5f071f6', '/tramites/DANF', 'Dirección de Alimentación y Nutrición Familiar — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:49:15'),
(484, '9df0729e650e770bd076ffb43b31884de5f071f6', '/tramites/DSJAIG', 'Dirección de Servicios Jurídicos – Asistenciales e Igualdad de Género — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:49:26'),
(485, '9df0729e650e770bd076ffb43b31884de5f071f6', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías jurídicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:50:21'),
(486, '9df0729e650e770bd076ffb43b31884de5f071f6', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=asesor-as-judiciales-16', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:50:49'),
(487, '9df0729e650e770bd076ffb43b31884de5f071f6', '/programas/seccion?slug=servicios-17', 'Servicios — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:51:12'),
(488, '9df0729e650e770bd076ffb43b31884de5f071f6', '/tramites/DAD', 'Dirección de Atención a la Discapacidad — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:52:25'),
(489, '9df0729e650e770bd076ffb43b31884de5f071f6', '/tramites/DAD', 'Dirección de Atención a la Discapacidad — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:53:39'),
(490, '9df0729e650e770bd076ffb43b31884de5f071f6', '/tramites/DSJAIG', 'Dirección de Servicios Jurídicos – Asistenciales e Igualdad de Género — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:53:52'),
(491, '9df0729e650e770bd076ffb43b31884de5f071f6', '/tramites/DANF', 'Dirección de Alimentación y Nutrición Familiar — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:54:01'),
(492, '9df0729e650e770bd076ffb43b31884de5f071f6', '/tramites/DAD', 'Dirección de Atención a la Discapacidad — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:54:09'),
(493, '9df0729e650e770bd076ffb43b31884de5f071f6', '/programas/seccion?slug=cuarto-de-estimulaci-n-multisensorial-18', 'Cuarto de Estimulación Multisensorial — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:54:46'),
(494, '9df0729e650e770bd076ffb43b31884de5f071f6', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=cuarto-de-estimulaci-n-multisensorial-18', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:55:25'),
(495, '9df0729e650e770bd076ffb43b31884de5f071f6', '/tramites/DPAF', 'Dirección de Prevención y Bienestar Familiar — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:55:36'),
(496, '9df0729e650e770bd076ffb43b31884de5f071f6', '/tramites/DAAM', 'Dirección de Atención a Adultos Mayores — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:56:43'),
(497, '9df0729e650e770bd076ffb43b31884de5f071f6', '/acerca-del-dif/organigrama.php', 'Organigrama — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:58:08'),
(498, '9df0729e650e770bd076ffb43b31884de5f071f6', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/organigrama.php', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:58:22'),
(499, '9df0729e650e770bd076ffb43b31884de5f071f6', '/acerca-del-dif/direcciones', 'Direcciones — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:59:04'),
(500, '9df0729e650e770bd076ffb43b31884de5f071f6', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/direcciones', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:59:47'),
(501, '9df0729e650e770bd076ffb43b31884de5f071f6', '/comunicacion-social/noticias', 'Noticias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/PMPNNA', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 17:59:58'),
(502, '9df0729e650e770bd076ffb43b31884de5f071f6', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/noticias', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 18:00:03'),
(503, '9df0729e650e770bd076ffb43b31884de5f071f6', '/voluntariado', 'Voluntariado — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/galeria', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 18:00:20'),
(504, '9df0729e650e770bd076ffb43b31884de5f071f6', '/voluntariado', 'Voluntariado — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/voluntariado', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 18:02:36'),
(505, '9df0729e650e770bd076ffb43b31884de5f071f6', '/voluntariado', 'Voluntariado — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/voluntariado', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 18:02:45'),
(506, '9df0729e650e770bd076ffb43b31884de5f071f6', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/voluntariado', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 18:02:53'),
(507, '9df0729e650e770bd076ffb43b31884de5f071f6', '/transparencia/pae', 'Programa Anual de Evaluación — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 18:04:10'),
(508, '9df0729e650e770bd076ffb43b31884de5f071f6', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/pae', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 18:05:02'),
(509, 'c27c7cfef34469a859a25ad1760a54ddb6b70f65', '/index', 'DIF San Mateo Atenco', NULL, '3ed139e541bb5966e2a710d33b11e48f0f8ef7c695a1ef94a322404c48d08b84', 'celular', 'iPhone iOS 18.7', 'Safari', 0, '2026-04-29 18:09:21'),
(510, '632446fd9040d9c5afb19286dc98666f9fb38568', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-29 18:10:18'),
(511, '632446fd9040d9c5afb19286dc98666f9fb38568', '/voluntariado', 'Voluntariado — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-29 18:16:16'),
(512, '0e001cb4ae7d8c968167efe259f8fe1e32f16a4f', '/index', 'DIF San Mateo Atenco', NULL, 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-29 18:19:20'),
(513, '0e001cb4ae7d8c968167efe259f8fe1e32f16a4f', '/transparencia/pae.php', 'Programa Anual de Evaluación — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff493ed36aa7e53d5c137ddd583d0c084d62f12832782f3da9b976ed9e618d2a', 'pc', 'Windows 10/11', 'Chrome 147.0.0.0', 0, '2026-04-29 18:21:49'),
(514, 'bbd830a3377e9e1065a988b02de0c1d228435986', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', '4b8d0a3123776c7a2b7f3209edb0372cc5dc850c3feeeaf4f546f7039b1674f1', 'celular', 'iPhone iOS 26.4.2', 'Safari', 0, '2026-04-29 18:24:33'),
(515, 'c8ca16d4314d421d1a2a930a47cf3c185cee1a15', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', NULL, '4b8d0a3123776c7a2b7f3209edb0372cc5dc850c3feeeaf4f546f7039b1674f1', 'celular', 'iPhone iOS 26.4.2', 'Safari', 0, '2026-04-29 18:25:24'),
(516, 'c8ca16d4314d421d1a2a930a47cf3c185cee1a15', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/galeria', '4b8d0a3123776c7a2b7f3209edb0372cc5dc850c3feeeaf4f546f7039b1674f1', 'celular', 'iPhone iOS 26.4.2', 'Safari', 0, '2026-04-29 18:25:29'),
(517, 'c8ca16d4314d421d1a2a930a47cf3c185cee1a15', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/galeria', '4b8d0a3123776c7a2b7f3209edb0372cc5dc850c3feeeaf4f546f7039b1674f1', 'pc', 'macOS 10.15.7', 'Safari', 0, '2026-04-29 18:26:36'),
(518, 'c8ca16d4314d421d1a2a930a47cf3c185cee1a15', '/transparencia/SEAC', 'SEAC — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '4b8d0a3123776c7a2b7f3209edb0372cc5dc850c3feeeaf4f546f7039b1674f1', 'pc', 'macOS 10.15.7', 'Safari', 0, '2026-04-29 18:35:03'),
(519, 'c189d383f806790507a06354af1cf3d0f5d031e9', '/', 'DIF San Mateo Atenco', NULL, '412cc4c18a2f835a9a8fd1cf3662d6b13b3017bbefd8a15d9a4807c8d890e1ca', 'celular', 'Android 8.0.0', 'Chrome 147.0.7727.116', 0, '2026-04-29 18:36:57'),
(520, 'c8ca16d4314d421d1a2a930a47cf3c185cee1a15', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/transparencia/SEAC', '4b8d0a3123776c7a2b7f3209edb0372cc5dc850c3feeeaf4f546f7039b1674f1', 'pc', 'macOS 10.15.7', 'Safari', 0, '2026-04-29 18:40:03'),
(521, 'c8ca16d4314d421d1a2a930a47cf3c185cee1a15', '/acerca-del-dif/direcciones', 'Direcciones — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', '4b8d0a3123776c7a2b7f3209edb0372cc5dc850c3feeeaf4f546f7039b1674f1', 'pc', 'macOS 10.15.7', 'Safari', 0, '2026-04-29 18:40:17'),
(522, 'c8ca16d4314d421d1a2a930a47cf3c185cee1a15', '/acerca-del-dif/organigrama', 'Organigrama — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/direcciones', '4b8d0a3123776c7a2b7f3209edb0372cc5dc850c3feeeaf4f546f7039b1674f1', 'pc', 'macOS 10.15.7', 'Safari', 0, '2026-04-29 18:40:44'),
(523, 'c8ca16d4314d421d1a2a930a47cf3c185cee1a15', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/organigrama', '4b8d0a3123776c7a2b7f3209edb0372cc5dc850c3feeeaf4f546f7039b1674f1', 'pc', 'macOS 10.15.7', 'Safari', 0, '2026-04-29 18:41:08'),
(524, 'c8ca16d4314d421d1a2a930a47cf3c185cee1a15', '/autismo', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/PMPNNA', '4b8d0a3123776c7a2b7f3209edb0372cc5dc850c3feeeaf4f546f7039b1674f1', 'pc', 'macOS 10.15.7', 'Safari', 0, '2026-04-29 18:41:46'),
(525, 'c8ca16d4314d421d1a2a930a47cf3c185cee1a15', '/tramites/DIFCDTI', 'Caravana de Servicios DIF CERCA DE TI — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/PMPNNA', '4b8d0a3123776c7a2b7f3209edb0372cc5dc850c3feeeaf4f546f7039b1674f1', 'pc', 'macOS 10.15.7', 'Safari', 0, '2026-04-29 18:42:10'),
(526, 'c8ca16d4314d421d1a2a930a47cf3c185cee1a15', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DIFCDTI', '4b8d0a3123776c7a2b7f3209edb0372cc5dc850c3feeeaf4f546f7039b1674f1', 'pc', 'macOS 10.15.7', 'Safari', 0, '2026-04-29 18:42:23'),
(527, 'c8ca16d4314d421d1a2a930a47cf3c185cee1a15', '/voluntariado', 'Voluntariado — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/galeria', '4b8d0a3123776c7a2b7f3209edb0372cc5dc850c3feeeaf4f546f7039b1674f1', 'pc', 'macOS 10.15.7', 'Safari', 0, '2026-04-29 18:43:04'),
(528, '0ed160ebfae8bca3742d813ccbe738b308b4066c', '/voluntariado', 'Voluntariado — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/galeria', '4b8d0a3123776c7a2b7f3209edb0372cc5dc850c3feeeaf4f546f7039b1674f1', 'pc', 'macOS 10.15.7', 'Safari', 0, '2026-04-29 18:57:23'),
(529, '4fb77682a81c46afd1d73227f29a1511817b73ef', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', '4b8d0a3123776c7a2b7f3209edb0372cc5dc850c3feeeaf4f546f7039b1674f1', 'celular', 'iPhone iOS 26.4.2', 'Safari', 0, '2026-04-29 18:57:23'),
(530, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/', 'DIF San Mateo Atenco', NULL, '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:05:39'),
(531, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:06:25'),
(532, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/acerca-del-dif/direcciones', 'Direcciones — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:06:34'),
(533, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/acerca-del-dif/direcciones', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:06:47'),
(534, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/tramites/DAAM', 'Dirección de Atención a Adultos Mayores — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/PMPNNA', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:06:56'),
(535, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/tramites/DANF', 'Dirección de Alimentación y Nutrición Familiar — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DAAM', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:07:06'),
(536, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/tramites/DAD', 'Dirección de Atención a la Discapacidad — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DANF', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:07:15'),
(537, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/tramites/DPAF', 'Dirección de Prevención y Bienestar Familiar — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DAD', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:07:23'),
(538, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/comunicacion-social/noticias', 'Noticias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/DPAF', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:07:35'),
(539, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/voluntariado', 'Voluntariado — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/comunicacion-social/noticias', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:07:42'),
(540, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/voluntariado', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:07:49'),
(541, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:07:54'),
(542, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/tramites/PMPNNA', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:08:03'),
(543, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', 'Detección y prevención de niñas, niños y adolescentes en situación de calle — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:08:10'),
(544, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:08:18'),
(545, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', 'Prevención y erradicación del trabajo infantil — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:08:27'),
(546, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/index', 'DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:08:40'),
(547, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=detecci-n-y-prevenci-n-de-ni-as-ni-os-y-adolescentes-en-situaci-n-de-calle-14', 'Detección y prevención de niñas, niños y adolescentes en situación de calle — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:08:48'),
(548, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', 'Prevención y erradicación del trabajo infantil — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:09:07'),
(549, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-embarazo-adolescente-14', 'Promoción del respeto a los derechos laborales de los adolescentes trabajadores en edad permitida — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:09:17'),
(550, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'Entrega de desayunos escolares fríos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:09:47'),
(551, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=entrega-de-desayunos-calientes-15', 'Entrega de desayunos escolares calientes — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:10:01'),
(552, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=programa-canasta-alimentaria-del-bienestar-15', 'Programa Canasta Alimentaria del Bienestar — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:10:17'),
(553, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=asesor-as-judiciales-16', 'Asesorías jurídicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:10:27'),
(554, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=servicios-17', 'Servicios — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:10:44'),
(555, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=vacunas-y-medicamentos-17', 'Vacunas y medicamentos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:10:53'),
(556, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=otros-17', 'Otros — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:11:04'),
(557, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=centro-de-rehabilitaci-n-e-integraci-n-social-cris-18', 'Centro de Rehabilitación e Integración Social CRIS — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:11:16'),
(558, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=cuarto-de-estimulaci-n-multisensorial-18', 'Cuarto de Estimulación Multisensorial — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:11:24'),
(559, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=terapias-18', 'Terapias — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:11:37'),
(560, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=actividades-culturales-y-recreativas-18', 'Actividades culturales y recreativas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:11:45'),
(561, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=programas-y-talleres-18', 'Programas y talleres — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:11:52'),
(562, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=consultas-psicol-gicas-20', 'Consultas psicológicas — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:12:03'),
(563, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=talleres-20', 'Talleres — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:12:10'),
(564, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=jornadas-informativas-escolares-20', 'Jornadas informativas escolares — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:12:15'),
(565, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=jornadas-de-mastograf-as-21', 'Jornadas de mastografías — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:12:25'),
(566, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=carril-rosa-21', 'Carril Rosa — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:12:32'),
(567, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=orientaci-n-y-acompa-amiento-21', 'Orientación y acompañamiento. — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:12:42'),
(568, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=casas-de-d-a-22', 'Casas de Día — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:12:52'),
(569, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=clases-y-talleres-22', 'Clases y talleres — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:12:57'),
(570, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=paseos-recreativos-22', 'Paseos recreativos — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:13:07'),
(571, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=visitas-culturales-22', 'Visitas culturales — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:13:13'),
(572, '4be0f2bc5b545bb39caed4b9045d7577ccbaaa90', '/programas/seccion?slug=celebraciones-22', 'Celebraciones — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', '7b3121e29728e0200904efdf290b03a5ea9ecb4cc1271078d5c97af830820913', 'pc', 'macOS 10.15.7', 'Chrome 147.0.0.0', 0, '2026-04-29 19:13:19'),
(573, '3cffb60ca32bc7e06bb071ca184dd71179c83a2e', '/index', 'DIF San Mateo Atenco', NULL, 'ff9ce583b4732a944cf13351ec90052e5e299dce661527d2a30ff8f2c9724396', 'celular', 'iPhone iOS 18.7', 'Safari', 0, '2026-04-29 19:16:53'),
(574, 'c4e62f0c94b40fe5e7a579ad0088d1ccd8c127c1', '/index', 'DIF San Mateo Atenco', NULL, 'ff9ce583b4732a944cf13351ec90052e5e299dce661527d2a30ff8f2c9724396', 'celular', 'iPhone iOS 18.7', 'Safari', 0, '2026-04-29 22:17:33'),
(575, 'c4e62f0c94b40fe5e7a579ad0088d1ccd8c127c1', '/index', 'DIF San Mateo Atenco', NULL, 'ff9ce583b4732a944cf13351ec90052e5e299dce661527d2a30ff8f2c9724396', 'celular', 'iPhone iOS 18.7', 'Safari', 0, '2026-04-29 22:17:37'),
(576, 'c4e62f0c94b40fe5e7a579ad0088d1ccd8c127c1', '/index', 'DIF San Mateo Atenco', NULL, 'ff9ce583b4732a944cf13351ec90052e5e299dce661527d2a30ff8f2c9724396', 'celular', 'iPhone iOS 18.7', 'Safari', 0, '2026-04-29 22:17:39'),
(577, 'c4e62f0c94b40fe5e7a579ad0088d1ccd8c127c1', '/index', 'DIF San Mateo Atenco', NULL, 'ff9ce583b4732a944cf13351ec90052e5e299dce661527d2a30ff8f2c9724396', 'celular', 'iPhone iOS 18.7', 'Safari', 0, '2026-04-29 22:17:39'),
(578, 'c4e62f0c94b40fe5e7a579ad0088d1ccd8c127c1', '/index', 'DIF San Mateo Atenco', NULL, 'ff9ce583b4732a944cf13351ec90052e5e299dce661527d2a30ff8f2c9724396', 'celular', 'iPhone iOS 18.7', 'Safari', 0, '2026-04-29 22:17:40'),
(579, 'c4e62f0c94b40fe5e7a579ad0088d1ccd8c127c1', '/mantenimiento', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/index', 'ff9ce583b4732a944cf13351ec90052e5e299dce661527d2a30ff8f2c9724396', 'celular', 'iPhone iOS 18.7', 'Safari', 0, '2026-04-29 22:17:58'),
(580, '7a01182bf1304a3a2d84673cf43d867e75019ef1', '/', 'DIF San Mateo Atenco', NULL, 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 22:39:12'),
(581, '7a01182bf1304a3a2d84673cf43d867e75019ef1', '/', 'DIF San Mateo Atenco', NULL, 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 22:50:47'),
(582, '7a01182bf1304a3a2d84673cf43d867e75019ef1', '/', 'DIF San Mateo Atenco', NULL, 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 22:53:52'),
(583, '7a01182bf1304a3a2d84673cf43d867e75019ef1', '/', 'DIF San Mateo Atenco', NULL, 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 23:02:01'),
(584, '7a01182bf1304a3a2d84673cf43d867e75019ef1', '/mantenimiento', 'En Mantenimiento — DIF San Mateo Atenco', 'https://difsma.com.mx.difsanmateoatenco.gob.mx/', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 23:02:25'),
(585, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/', 'DIF San Mateo Atenco', NULL, 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 23:21:16'),
(586, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/', 'DIF San Mateo Atenco', NULL, 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 23:33:56'),
(587, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 23:39:40'),
(588, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/acerca-del-dif/direcciones', 'Direcciones — DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 23:39:45'),
(589, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/acerca-del-dif/organigrama', 'Organigrama — DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/acerca-del-dif/direcciones', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 23:39:54'),
(590, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/tramites/PMPNNA', 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes — DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/acerca-del-dif/organigrama', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 23:39:59'),
(591, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/comunicacion-social/noticias', 'Noticias — DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/tramites/PMPNNA', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 23:40:20'),
(592, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/transparencia/cuenta_publica', 'Cuenta Pública — DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/comunicacion-social/noticias', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 23:40:46'),
(593, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/transparencia/cuenta_publica', 'Cuenta Pública — DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/comunicacion-social/noticias', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 23:45:06'),
(594, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/transparencia/cuenta_publica', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 23:45:14'),
(595, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/acerca-del-dif/direcciones', 'Direcciones — DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 23:45:19'),
(596, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/index', 'DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/acerca-del-dif/direcciones', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 23:45:24'),
(597, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/transparencia/SEAC', 'SEAC — DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/index', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 23:45:38'),
(598, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/transparencia/SEAC', 'SEAC — DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/index', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 23:48:02'),
(599, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/index', 'DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/transparencia/SEAC', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-29 23:50:16'),
(600, '9dbdebe67cf05a67f7f7eb2760ae33e215791cbb', '/', 'DIF San Mateo Atenco', 'https://www.google.com/', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'celular', 'iPhone iOS 26.4.2', 'Safari', 0, '2026-04-30 00:03:44'),
(601, 'fe1c87151c8aea99b8e73b58ed79b646884bceca', '/', 'DIF San Mateo Atenco', NULL, 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'celular', 'iPhone iOS 26.4.2', 'Safari', 0, '2026-04-30 00:04:18'),
(602, 'fe1c87151c8aea99b8e73b58ed79b646884bceca', '/programas/seccion?slug=centro-de-rehabilitaci-n-e-integraci-n-social-cris-18', 'Centro de Rehabilitación e Integración Social CRIS — DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'celular', 'iPhone iOS 26.4.2', 'Safari', 0, '2026-04-30 00:04:59'),
(603, 'fe1c87151c8aea99b8e73b58ed79b646884bceca', '/index', 'DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/programas/seccion?slug=centro-de-rehabilitaci-n-e-integraci-n-social-cris-18', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'celular', 'iPhone iOS 26.4.2', 'Safari', 0, '2026-04-30 00:05:11'),
(604, 'fe1c87151c8aea99b8e73b58ed79b646884bceca', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', 'Prevención y erradicación del trabajo infantil — DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/index', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'celular', 'iPhone iOS 26.4.2', 'Safari', 0, '2026-04-30 00:05:21'),
(605, 'fe1c87151c8aea99b8e73b58ed79b646884bceca', '/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', 'Prevención y erradicación del trabajo infantil — DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/index', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'celular', 'iPhone iOS 26.4.2', 'Safari', 0, '2026-04-30 00:11:42'),
(606, 'fe1c87151c8aea99b8e73b58ed79b646884bceca', '/index', 'DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/programas/seccion?slug=prevenci-n-y-erradicaci-n-del-trabajo-infantil-14', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'celular', 'iPhone iOS 26.4.2', 'Safari', 0, '2026-04-30 00:11:58'),
(607, 'fe1c87151c8aea99b8e73b58ed79b646884bceca', '/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'Entrega de desayunos escolares fríos — DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/index', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'celular', 'iPhone iOS 26.4.2', 'Safari', 0, '2026-04-30 00:12:04'),
(608, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/programas/seccion?slug=consultas-psicol-gicas-20', 'Consultas psicológicas — DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/index', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-30 00:13:51'),
(609, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/index', 'DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/programas/seccion?slug=consultas-psicol-gicas-20', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-30 00:14:07'),
(610, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/comunicacion-social/galeria', 'Galería — DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/index', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-30 00:14:11'),
(611, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/index', 'DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/comunicacion-social/galeria', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-30 00:20:38'),
(612, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/index', 'DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/comunicacion-social/galeria', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-30 00:24:39'),
(613, 'cb15b40ddac298c1611184e011053dcd9f6d7d7b', '/index', 'DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/index', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'pc', 'Windows 10/11', 'Edge 147.0.0.0', 0, '2026-04-30 00:24:40'),
(614, 'fe1c87151c8aea99b8e73b58ed79b646884bceca', '/index', 'DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/programas/seccion?slug=entrega-de-desayunos-fr-os-15', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'celular', 'iPhone iOS 26.4.2', 'Safari', 0, '2026-04-30 00:24:53'),
(615, 'fe1c87151c8aea99b8e73b58ed79b646884bceca', '/acerca-del-dif/presidencia', 'Presidente DIF — DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/index', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'celular', 'iPhone iOS 26.4.2', 'Safari', 0, '2026-04-30 00:24:59'),
(616, 'fe1c87151c8aea99b8e73b58ed79b646884bceca', '/index', 'DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/acerca-del-dif/presidencia', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'celular', 'iPhone iOS 26.4.2', 'Safari', 0, '2026-04-30 00:25:02'),
(617, 'fe1c87151c8aea99b8e73b58ed79b646884bceca', '/voluntariado', 'Voluntariado — DIF San Mateo Atenco', 'https://difsanmateoatenco.gob.mx/index', 'd3052a1f019a42a3f1fcd71db890262cecf190c6f29849cc1d91770d8c81aa80', 'celular', 'iPhone iOS 26.4.2', 'Safari', 0, '2026-04-30 00:25:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `voluntariado_config`
--

CREATE TABLE `voluntariado_config` (
  `id` int(11) NOT NULL,
  `logo_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lema` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT 'UNIDOS SÍ, TENDEMOS LA MANO',
  `mision_titulo` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT '¿Qué es ser voluntario?',
  `mision_texto` text COLLATE utf8mb4_unicode_ci,
  `mision_subtitulo` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT '¿Cómo puedo aportar?',
  `mision_subtexto` text COLLATE utf8mb4_unicode_ci,
  `vision_texto` text COLLATE utf8mb4_unicode_ci,
  `valores_texto` text COLLATE utf8mb4_unicode_ci,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `voluntariado_config`
--

INSERT INTO `voluntariado_config` (`id`, `logo_path`, `lema`, `mision_titulo`, `mision_texto`, `mision_subtitulo`, `mision_subtexto`, `vision_texto`, `valores_texto`, `updated_at`) VALUES
(1, 'uploads/images/0dbc6805aa158e0b8ccd22c9ad78a85f.png', 'UNIDOS SÍ, TENDEMOS LA MANO', '¿Qué es ser voluntario?', 'Establecer un compromiso de ayuda con la población más necesitada, compartiendo su tiempo, talento y recursos de manera desinteresada. Ser voluntario es saber amar y estar dispuesto a llevar a la práctica el amor por el ser humano a través del servicio', '¿Cómo puedo aportar?', 'Se puede participar en estas actividades:\n- Realizar campañas de recaudación permanente de artículos en especie.\n- Vincular al sector privado para que apoye mediante donativos.\n- Distribuir los donativos entre los sectores más vulnerables.', 'Lograr una transformación social y solidaria generando acciones y servicios para todos los ciudadanos, construyendo una cultura de derechos para niñas, niños, adolescentes, jóvenes, adultos mayores, personas con discapacidad, mujeres y familias atenquenses más vulnerables.', 'Compromiso social\nSolidaridad\nResponsabilidad\nEmpatía', '2026-03-27 02:08:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `voluntariado_imagenes`
--

CREATE TABLE `voluntariado_imagenes` (
  `id` int(11) NOT NULL,
  `imagen_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int(11) DEFAULT '1',
  `activo` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `voluntariado_imagenes`
--

INSERT INTO `voluntariado_imagenes` (`id`, `imagen_path`, `orden`, `activo`) VALUES
(1, 'uploads/images/29a6b8a0180c70c827a2a0704270b111.jpeg', 2, 1),
(2, 'uploads/images/662a777022db9fb7c3a18d6c91e2e85a.jpeg', 5, 1),
(3, 'uploads/images/a6c9071af9f93bef73773780fc3e8a37.jpeg', 4, 1),
(4, 'uploads/images/f783601d5e36af946cac599d9c82a883.jpeg', 3, 1),
(5, 'uploads/images/7fd29a9c21c129cb129329703b7cbb86.jpg', 1, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_username` (`username`);

--
-- Indices de la tabla `admin_historial`
--
ALTER TABLE `admin_historial`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `admin_permisos`
--
ALTER TABLE `admin_permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_seccion` (`user_id`,`seccion_file`);

--
-- Indices de la tabla `autismo_config`
--
ALTER TABLE `autismo_config`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `avisos_privacidad`
--
ALTER TABLE `avisos_privacidad`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `avisos_privacidad_config`
--
ALTER TABLE `avisos_privacidad_config`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `conac_bloques`
--
ALTER TABLE `conac_bloques`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_conac_anio` (`anio`);

--
-- Indices de la tabla `conac_conceptos`
--
ALTER TABLE `conac_conceptos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_conac_concepto_bloque` (`bloque_id`);

--
-- Indices de la tabla `conac_pdfs`
--
ALTER TABLE `conac_pdfs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_conac_bloque_concepto_trim` (`bloque_id`,`concepto_id`,`trimestre`),
  ADD KEY `fk_conac_pdf_concepto` (`concepto_id`);

--
-- Indices de la tabla `contacto_config`
--
ALTER TABLE `contacto_config`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cp_bloques`
--
ALTER TABLE `cp_bloques`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cp_anio` (`anio`);

--
-- Indices de la tabla `cp_conceptos`
--
ALTER TABLE `cp_conceptos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cp_concepto_titulo` (`titulo_id`);

--
-- Indices de la tabla `cp_titulos`
--
ALTER TABLE `cp_titulos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cp_titulo_bloque` (`bloque_id`);

--
-- Indices de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `fin_bloques`
--
ALTER TABLE `fin_bloques`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_fin_anio` (`anio`);

--
-- Indices de la tabla `fin_conceptos`
--
ALTER TABLE `fin_conceptos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_fin_concepto_bloque` (`bloque_id`);

--
-- Indices de la tabla `footer_config`
--
ALTER TABLE `footer_config`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `footer_links`
--
ALTER TABLE `footer_links`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `galeria_albumes`
--
ALTER TABLE `galeria_albumes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `galeria_imagenes`
--
ALTER TABLE `galeria_imagenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_galeria_album` (`album_id`);

--
-- Indices de la tabla `institucion_banner`
--
ALTER TABLE `institucion_banner`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ip_time` (`ip`,`attempted_at`);

--
-- Indices de la tabla `mantenimiento_config`
--
ALTER TABLE `mantenimiento_config`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mantenimiento_paginas`
--
ALTER TABLE `mantenimiento_paginas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pagina_key` (`pagina_key`),
  ADD UNIQUE KEY `uk_pagina_key` (`pagina_key`);

--
-- Indices de la tabla `mi_pdfs`
--
ALTER TABLE `mi_pdfs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_mi_anio` (`anio`);

--
-- Indices de la tabla `noticias_imagenes`
--
ALTER TABLE `noticias_imagenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fecha` (`fecha_noticia`);

--
-- Indices de la tabla `organigrama`
--
ALTER TABLE `organigrama`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pae_pdfs`
--
ALTER TABLE `pae_pdfs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pae_titulo_anio` (`titulo_id`,`anio`);

--
-- Indices de la tabla `pae_titulos`
--
ALTER TABLE `pae_titulos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pa_bloques`
--
ALTER TABLE `pa_bloques`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pa_anio` (`anio`);

--
-- Indices de la tabla `pa_conceptos`
--
ALTER TABLE `pa_conceptos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pa_concepto_bloque` (`bloque_id`);

--
-- Indices de la tabla `pa_pdfs`
--
ALTER TABLE `pa_pdfs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pa_concepto_anio` (`concepto_id`,`sub_anio`);

--
-- Indices de la tabla `presidencia`
--
ALTER TABLE `presidencia`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `programas`
--
ALTER TABLE `programas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `programas_secciones`
--
ALTER TABLE `programas_secciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_programa_seccion` (`programa_id`);

--
-- Indices de la tabla `programas_secciones_paginas`
--
ALTER TABLE `programas_secciones_paginas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `seccion_id` (`seccion_id`);

--
-- Indices de la tabla `seac_bloques`
--
ALTER TABLE `seac_bloques`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_anio` (`anio`);

--
-- Indices de la tabla `seac_conceptos`
--
ALTER TABLE `seac_conceptos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_seac_concepto_bloque` (`bloque_id`);

--
-- Indices de la tabla `seac_pdfs`
--
ALTER TABLE `seac_pdfs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_bloque_concepto_trim` (`bloque_id`,`concepto_id`,`trimestre`),
  ADD KEY `fk_seac_concepto` (`concepto_id`);

--
-- Indices de la tabla `servicios_galeria`
--
ALTER TABLE `servicios_galeria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `slider_comunica`
--
ALTER TABLE `slider_comunica`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mes_anio` (`anio`,`mes`);

--
-- Indices de la tabla `slider_config`
--
ALTER TABLE `slider_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_seccion` (`seccion`);

--
-- Indices de la tabla `slider_principal`
--
ALTER TABLE `slider_principal`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tramites`
--
ALTER TABLE `tramites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_slug` (`slug`);

--
-- Indices de la tabla `tramites_galeria`
--
ALTER TABLE `tramites_galeria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tramite_id` (`tramite_id`);

--
-- Indices de la tabla `transparencia_items`
--
ALTER TABLE `transparencia_items`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `trans_bloques`
--
ALTER TABLE `trans_bloques`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_trans_bloque` (`seccion_id`,`anio`);

--
-- Indices de la tabla `trans_conceptos`
--
ALTER TABLE `trans_conceptos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seccion_id` (`seccion_id`),
  ADD KEY `bloque_id` (`bloque_id`),
  ADD KEY `titulo_id` (`titulo_id`);

--
-- Indices de la tabla `trans_pdfs`
--
ALTER TABLE `trans_pdfs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seccion_id` (`seccion_id`),
  ADD KEY `concepto_id` (`concepto_id`),
  ADD KEY `titulo_id` (`titulo_id`);

--
-- Indices de la tabla `trans_secciones`
--
ALTER TABLE `trans_secciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_trans_slug` (`slug`);

--
-- Indices de la tabla `trans_titulos`
--
ALTER TABLE `trans_titulos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seccion_id` (`seccion_id`),
  ADD KEY `bloque_id` (`bloque_id`);

--
-- Indices de la tabla `visitor_analytics`
--
ALTER TABLE `visitor_analytics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_session` (`session_id`),
  ADD KEY `idx_dispositivo` (`dispositivo`);

--
-- Indices de la tabla `voluntariado_config`
--
ALTER TABLE `voluntariado_config`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `voluntariado_imagenes`
--
ALTER TABLE `voluntariado_imagenes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `admin_historial`
--
ALTER TABLE `admin_historial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=586;

--
-- AUTO_INCREMENT de la tabla `admin_permisos`
--
ALTER TABLE `admin_permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `autismo_config`
--
ALTER TABLE `autismo_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `avisos_privacidad`
--
ALTER TABLE `avisos_privacidad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `avisos_privacidad_config`
--
ALTER TABLE `avisos_privacidad_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `conac_bloques`
--
ALTER TABLE `conac_bloques`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `conac_conceptos`
--
ALTER TABLE `conac_conceptos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `conac_pdfs`
--
ALTER TABLE `conac_pdfs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `contacto_config`
--
ALTER TABLE `contacto_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cp_bloques`
--
ALTER TABLE `cp_bloques`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `cp_conceptos`
--
ALTER TABLE `cp_conceptos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT de la tabla `cp_titulos`
--
ALTER TABLE `cp_titulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `fin_bloques`
--
ALTER TABLE `fin_bloques`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `fin_conceptos`
--
ALTER TABLE `fin_conceptos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `footer_config`
--
ALTER TABLE `footer_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `footer_links`
--
ALTER TABLE `footer_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `galeria_albumes`
--
ALTER TABLE `galeria_albumes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `galeria_imagenes`
--
ALTER TABLE `galeria_imagenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `institucion_banner`
--
ALTER TABLE `institucion_banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `mantenimiento_config`
--
ALTER TABLE `mantenimiento_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `mantenimiento_paginas`
--
ALTER TABLE `mantenimiento_paginas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=477;

--
-- AUTO_INCREMENT de la tabla `mi_pdfs`
--
ALTER TABLE `mi_pdfs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `noticias_imagenes`
--
ALTER TABLE `noticias_imagenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `organigrama`
--
ALTER TABLE `organigrama`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pae_pdfs`
--
ALTER TABLE `pae_pdfs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `pae_titulos`
--
ALTER TABLE `pae_titulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pa_bloques`
--
ALTER TABLE `pa_bloques`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `pa_conceptos`
--
ALTER TABLE `pa_conceptos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `pa_pdfs`
--
ALTER TABLE `pa_pdfs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `presidencia`
--
ALTER TABLE `presidencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `programas`
--
ALTER TABLE `programas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `programas_secciones`
--
ALTER TABLE `programas_secciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT de la tabla `programas_secciones_paginas`
--
ALTER TABLE `programas_secciones_paginas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `seac_bloques`
--
ALTER TABLE `seac_bloques`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `seac_conceptos`
--
ALTER TABLE `seac_conceptos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=424;

--
-- AUTO_INCREMENT de la tabla `seac_pdfs`
--
ALTER TABLE `seac_pdfs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=657;

--
-- AUTO_INCREMENT de la tabla `servicios_galeria`
--
ALTER TABLE `servicios_galeria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `slider_comunica`
--
ALTER TABLE `slider_comunica`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de la tabla `slider_config`
--
ALTER TABLE `slider_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `slider_principal`
--
ALTER TABLE `slider_principal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `tramites`
--
ALTER TABLE `tramites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tramites_galeria`
--
ALTER TABLE `tramites_galeria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `transparencia_items`
--
ALTER TABLE `transparencia_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `trans_bloques`
--
ALTER TABLE `trans_bloques`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `trans_conceptos`
--
ALTER TABLE `trans_conceptos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `trans_pdfs`
--
ALTER TABLE `trans_pdfs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `trans_secciones`
--
ALTER TABLE `trans_secciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `trans_titulos`
--
ALTER TABLE `trans_titulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `visitor_analytics`
--
ALTER TABLE `visitor_analytics`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=618;

--
-- AUTO_INCREMENT de la tabla `voluntariado_config`
--
ALTER TABLE `voluntariado_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `voluntariado_imagenes`
--
ALTER TABLE `voluntariado_imagenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `admin_permisos`
--
ALTER TABLE `admin_permisos`
  ADD CONSTRAINT `admin_permisos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admin` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `conac_conceptos`
--
ALTER TABLE `conac_conceptos`
  ADD CONSTRAINT `fk_conac_concepto_bloque` FOREIGN KEY (`bloque_id`) REFERENCES `conac_bloques` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `conac_pdfs`
--
ALTER TABLE `conac_pdfs`
  ADD CONSTRAINT `fk_conac_pdf_bloque` FOREIGN KEY (`bloque_id`) REFERENCES `conac_bloques` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_conac_pdf_concepto` FOREIGN KEY (`concepto_id`) REFERENCES `conac_conceptos` (`id`);

--
-- Filtros para la tabla `cp_conceptos`
--
ALTER TABLE `cp_conceptos`
  ADD CONSTRAINT `fk_cp_concepto_titulo` FOREIGN KEY (`titulo_id`) REFERENCES `cp_titulos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cp_titulos`
--
ALTER TABLE `cp_titulos`
  ADD CONSTRAINT `fk_cp_titulo_bloque` FOREIGN KEY (`bloque_id`) REFERENCES `cp_bloques` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `fin_conceptos`
--
ALTER TABLE `fin_conceptos`
  ADD CONSTRAINT `fk_fin_concepto_bloque` FOREIGN KEY (`bloque_id`) REFERENCES `fin_bloques` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `galeria_imagenes`
--
ALTER TABLE `galeria_imagenes`
  ADD CONSTRAINT `fk_galeria_album` FOREIGN KEY (`album_id`) REFERENCES `galeria_albumes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pae_pdfs`
--
ALTER TABLE `pae_pdfs`
  ADD CONSTRAINT `fk_pae_pdf_titulo` FOREIGN KEY (`titulo_id`) REFERENCES `pae_titulos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pa_conceptos`
--
ALTER TABLE `pa_conceptos`
  ADD CONSTRAINT `fk_pa_concepto_bloque` FOREIGN KEY (`bloque_id`) REFERENCES `pa_bloques` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pa_pdfs`
--
ALTER TABLE `pa_pdfs`
  ADD CONSTRAINT `fk_pa_pdf_concepto` FOREIGN KEY (`concepto_id`) REFERENCES `pa_conceptos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `programas_secciones`
--
ALTER TABLE `programas_secciones`
  ADD CONSTRAINT `fk_programa_seccion` FOREIGN KEY (`programa_id`) REFERENCES `programas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `programas_secciones_paginas`
--
ALTER TABLE `programas_secciones_paginas`
  ADD CONSTRAINT `fk_psp_seccion` FOREIGN KEY (`seccion_id`) REFERENCES `programas_secciones` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `seac_conceptos`
--
ALTER TABLE `seac_conceptos`
  ADD CONSTRAINT `fk_seac_concepto_bloque` FOREIGN KEY (`bloque_id`) REFERENCES `seac_bloques` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `seac_pdfs`
--
ALTER TABLE `seac_pdfs`
  ADD CONSTRAINT `fk_seac_bloque` FOREIGN KEY (`bloque_id`) REFERENCES `seac_bloques` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_seac_concepto` FOREIGN KEY (`concepto_id`) REFERENCES `seac_conceptos` (`id`);

--
-- Filtros para la tabla `trans_bloques`
--
ALTER TABLE `trans_bloques`
  ADD CONSTRAINT `trans_bloques_ibfk_1` FOREIGN KEY (`seccion_id`) REFERENCES `trans_secciones` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `trans_conceptos`
--
ALTER TABLE `trans_conceptos`
  ADD CONSTRAINT `trans_conceptos_ibfk_1` FOREIGN KEY (`seccion_id`) REFERENCES `trans_secciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trans_conceptos_ibfk_2` FOREIGN KEY (`bloque_id`) REFERENCES `trans_bloques` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trans_conceptos_ibfk_3` FOREIGN KEY (`titulo_id`) REFERENCES `trans_titulos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `trans_pdfs`
--
ALTER TABLE `trans_pdfs`
  ADD CONSTRAINT `trans_pdfs_ibfk_1` FOREIGN KEY (`seccion_id`) REFERENCES `trans_secciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trans_pdfs_ibfk_2` FOREIGN KEY (`concepto_id`) REFERENCES `trans_conceptos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trans_pdfs_ibfk_3` FOREIGN KEY (`titulo_id`) REFERENCES `trans_titulos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `trans_titulos`
--
ALTER TABLE `trans_titulos`
  ADD CONSTRAINT `trans_titulos_ibfk_1` FOREIGN KEY (`seccion_id`) REFERENCES `trans_secciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trans_titulos_ibfk_2` FOREIGN KEY (`bloque_id`) REFERENCES `trans_bloques` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
