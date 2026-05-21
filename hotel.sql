-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-05-2026 a las 21:12:51
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `hotel`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Estandar', 'Básica'),
(2, 'Doble', 'Dos camas'),
(3, 'Suite', 'Lujo'),
(4, 'Familiar', 'Grande');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos`
--

CREATE TABLE `documentos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `documentos`
--

INSERT INTO `documentos` (`id`, `nombre`) VALUES
(1, 'Tarjeta de identidad'),
(2, 'Cédula de ciudadanía'),
(3, 'Cédula extranjera'),
(4, 'Pasaporte');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado`
--

CREATE TABLE `estado` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado`
--

INSERT INTO `estado` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Disponible', 'Habitación libre'),
(2, 'Ocupado', 'En uso'),
(3, 'Mantenimiento', 'No disponible');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habitaciones`
--

CREATE TABLE `habitaciones` (
  `id` int(11) NOT NULL,
  `numero_camas` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `estado_id` int(11) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `max_persona` int(11) DEFAULT NULL,
  `aseo` tinyint(1) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `habitaciones`
--

INSERT INTO `habitaciones` (`id`, `numero_camas`, `descripcion`, `estado_id`, `categoria_id`, `precio`, `max_persona`, `aseo`, `numero`) VALUES
(11, 1, 'Habitación sencilla', 1, 1, 80000.00, 1, 1, '101'),
(12, 1, 'Habitación sencilla', 1, 1, 80000.00, 1, 1, '102'),
(13, 1, 'Habitación sencilla', 1, 1, 80000.00, 1, 1, '103'),
(14, 2, 'Habitación doble', 1, 2, 120000.00, 2, 1, '201'),
(15, 2, 'Habitación doble', 1, 2, 120000.00, 2, 1, '202'),
(16, 2, 'Habitación familiar', 1, 4, 300000.00, 4, 1, '301'),
(17, 3, 'Habitación familiar', 1, 4, 320000.00, 5, 1, '302'),
(18, 2, 'Suite con jacuzzi', 1, 3, 250000.00, 2, 1, '401'),
(19, 2, 'Suite con jacuzzi', 1, 3, 250000.00, 2, 1, '402'),
(20, 1, 'Habitación económica', 1, 1, 60000.00, 1, 0, '501'),
(21, 1, 'Habitación económica', 1, 1, 60000.00, 1, 0, '502');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_final` date DEFAULT NULL,
  `habitacion_id` int(11) DEFAULT NULL,
  `n_personas` int(11) DEFAULT NULL,
  `estado_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id`, `fecha_inicio`, `fecha_final`, `habitacion_id`, `n_personas`, `estado_id`, `user_id`, `precio`) VALUES
(1, '2026-05-07', '2026-05-15', 14, 1, 2, 3, 960000.00),
(2, '2026-05-07', '2026-05-09', 20, 1, 2, 3, 120000.00),
(3, '2026-05-11', '2026-05-15', 17, 1, 2, 3, 1280000.00),
(4, '2026-05-17', '2026-05-24', 20, 1, 1, 3, 420000.00),
(5, '2026-05-12', '2026-05-17', 20, 1, 1, 3, 300000.00),
(6, '2026-05-21', '2026-05-24', 15, 1, 2, 7, 360000.00),
(7, '2026-05-14', '2026-05-22', 20, 2, 2, 7, 480000.00),
(8, '2026-05-14', '2026-05-24', 14, 1, 2, 7, 1200000.00),
(9, '2026-05-14', '2026-05-20', 21, 1, 2, 7, 360000.00),
(10, '2026-05-14', '2026-05-16', 21, 1, 2, 7, 120000.00),
(11, '2026-05-14', '2026-05-16', 21, 1, 2, 7, 120000.00),
(12, '2026-05-14', '2026-05-17', 15, 1, 2, 7, 360000.00),
(13, '2026-05-14', '2026-05-17', 15, 1, 2, 7, 360000.00),
(14, '2026-05-14', '2026-05-17', 15, 1, 2, 7, 360000.00),
(15, '2026-05-14', '2026-05-17', 15, 1, 2, 7, 360000.00),
(16, '2026-05-14', '2026-05-17', 15, 1, 2, 7, 360000.00),
(17, '2026-05-14', '2026-05-21', 17, 1, 1, 7, 2240000.00),
(18, '2026-05-14', '2026-05-21', 17, 1, 1, 7, 2240000.00),
(19, '2026-05-21', '2026-05-23', 14, 1, 1, 5, 240000.00),
(20, '2026-05-21', '2026-05-23', 20, 2, 1, 7, 120000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `tipo_documento_id` int(11) NOT NULL,
  `documento` bigint(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `tipo_documento_id`, `documento`, `nombre`, `apellido`, `email`, `contraseña`, `fecha_registro`, `created_at`, `updated_at`) VALUES
(1, 2, 1104941435, 'Maicol', 'Quevedo', 'maicolquevedo29@gmail.com', '$2y$10$FMSuow/B3dddIfet8W8aEedONhlkS3x68mmoWXT3RqmsZwPbReCUG', '2026-05-21 18:54:50', '2026-05-21 18:54:50', '2026-05-21 18:54:50'),
(2, 2, 1104941435, 'Maicol', 'Quevedo', 'maicolquevedo29@gmail.com', '$2y$10$SuEAiDuyLawNRriB36Sb2eFbCU/y70M.yz5Mn17riGyYWL7sSDLau', '2026-05-21 18:55:22', '2026-05-21 18:55:22', '2026-05-21 18:55:22'),
(3, 2, 1104941435, 'Maicol', 'Quevedo', 'maicolquevedo29@gmail.com', '$2y$10$1e8bCPK2hDP9FYxCFjZ1xOM13O9qIRADVgCs8DyYXLe6R.6jTcD..', '2026-05-21 18:56:31', '2026-05-21 18:56:31', '2026-05-21 18:56:31'),
(4, 2, 1104941435, 'Maicol', 'Quevedo', 'maicolquevedo29@gmail.com', '$2y$10$vLfrny6OFo9jN/rGpbNdI.2xrTOBBmlnz0aNewFgpiXzsNWso8peu', '2026-05-21 18:59:38', '2026-05-21 18:59:38', '2026-05-21 18:59:38'),
(5, 2, 12121212, 'Juan', 'Guzman', 'juandavidguzman0612@gmail.com', '$2y$10$WYqyN9SZnkkjW4NrMgABjOfHjoDrnSyUHeNnCgFiKxCu1IBnhUCgy', '2026-05-21 19:01:46', '2026-05-21 19:01:46', '2026-05-21 19:01:46'),
(6, 2, 12121212, 'Juan', 'Guzman', 'juandavidguzman0612@gmail.com', '$2y$10$fw5wJ8xUYTTaAozYhGPYLO6wBETSwWms1D9DYQkbM2e2TUeK7Z226', '2026-05-21 19:01:51', '2026-05-21 19:01:51', '2026-05-21 19:01:51'),
(7, 2, 1105840343, 'William', 'Duarte', 'adsosenawd@gmail.com', '$2y$10$aaeoL03OC.BAMKCWLl1RvOsXs1DtLA0DR3Iz9iWSD3ATG4cz9tQQW', '2026-05-21 19:06:57', '2026-05-21 19:06:57', '2026-05-21 19:06:57'),
(8, 2, 1105840343, 'William', 'Duarte', 'adsosenawd@gmail.com', '$2y$10$yRTJ2hgV6Tvmc0jGOXVkU.wySlUH7c77.ZNy8jWVJQzSpiQhB1VlW', '2026-05-21 19:07:02', '2026-05-21 19:07:02', '2026-05-21 19:07:02');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estado`
--
ALTER TABLE `estado`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_habitacion_estado` (`estado_id`),
  ADD KEY `fk_habitacion_categoria` (`categoria_id`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reserva_habitacion` (`habitacion_id`),
  ADD KEY `fk_reserva_estado` (`estado_id`),
  ADD KEY `fk_reserva_usuario` (`user_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuarios_documento_fk` (`tipo_documento_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `estado`
--
ALTER TABLE `estado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  ADD CONSTRAINT `fk_habitacion_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  ADD CONSTRAINT `fk_habitacion_estado` FOREIGN KEY (`estado_id`) REFERENCES `estado` (`id`);

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `fk_reserva_estado` FOREIGN KEY (`estado_id`) REFERENCES `estado` (`id`),
  ADD CONSTRAINT `fk_reserva_habitacion` FOREIGN KEY (`habitacion_id`) REFERENCES `habitaciones` (`id`),
  ADD CONSTRAINT `fk_reserva_usuario` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_documento_fk` FOREIGN KEY (`tipo_documento_id`) REFERENCES `documentos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
