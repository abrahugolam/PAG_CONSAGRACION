-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-06-2025 a las 06:52:59
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `consagracion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

CREATE TABLE `asistencia` (
  `id` int(11) NOT NULL,
  `integrante_id` int(11) DEFAULT NULL,
  `fecha` date NOT NULL,
  `presente` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asistencia`
--

INSERT INTO `asistencia` (`id`, `integrante_id`, `fecha`, `presente`) VALUES
(1, 1, '2025-06-23', 1),
(2, 2, '2025-06-23', 1),
(3, 3, '2025-06-23', 1),
(4, 1, '2025-06-23', 1),
(5, 2, '2025-06-23', 1),
(6, 3, '2025-06-23', 1),
(7, 2, '2025-06-23', 1),
(8, 4, '2025-06-23', 1),
(9, 2, '2025-06-23', 1),
(10, 3, '2025-06-23', 1),
(11, 5, '2025-06-23', 1),
(12, 4, '2025-06-23', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encargados_consagracion`
--

CREATE TABLE `encargados_consagracion` (
  `id` int(5) UNSIGNED ZEROFILL NOT NULL,
  `nombres_apellidos` varchar(100) NOT NULL,
  `localidad` enum('Arequipa','Ayaviri','Callao','Chiclayo','Familias','Lima Norte','Lima Centro','Lima Sur','Lima Este','Juliaca','Piura','Provincia') NOT NULL,
  `escuela_formacion` enum('Escuela de Maria','San Lorenzo','Lectio Divina') NOT NULL,
  `grupo_id` int(11) NOT NULL,
  `funcion` enum('Encargado','Apoyo 1','Apoyo 2') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `encargados_consagracion`
--

INSERT INTO `encargados_consagracion` (`id`, `nombres_apellidos`, `localidad`, `escuela_formacion`, `grupo_id`, `funcion`) VALUES
(00001, 'Karina Lazo', 'Lima Centro', 'Escuela de Maria', 2, 'Encargado'),
(00002, 'Analia Sanchez Solari', 'Provincia', 'Escuela de Maria', 2, 'Encargado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos_consagracion`
--

CREATE TABLE `grupos_consagracion` (
  `id` int(11) NOT NULL,
  `nombre_grupo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupos_consagracion`
--

INSERT INTO `grupos_consagracion` (`id`, `nombre_grupo`, `descripcion`) VALUES
(1, 'Consagración Lima Centro', 'Coanfgracion de Lima CEntro'),
(2, 'Grupo Perpetuo Socorro', 'Grupo de consagración en la parroquia Perpetuo socorro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `integrantes`
--

CREATE TABLE `integrantes` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `grupo_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `integrantes`
--

INSERT INTO `integrantes` (`id`, `nombre_completo`, `grupo_id`) VALUES
(1, 'hugo Quiñones', 1),
(2, 'Karla Soto Collazos', 1),
(3, 'Julio Juanito Quiñones Sanchez', 1),
(4, 'Emiliano Martinez', 2),
(5, 'Abraham SAnchez', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `integrante_id` (`integrante_id`);

--
-- Indices de la tabla `encargados_consagracion`
--
ALTER TABLE `encargados_consagracion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grupo_id` (`grupo_id`);

--
-- Indices de la tabla `grupos_consagracion`
--
ALTER TABLE `grupos_consagracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `integrantes`
--
ALTER TABLE `integrantes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grupo_id` (`grupo_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `encargados_consagracion`
--
ALTER TABLE `encargados_consagracion`
  MODIFY `id` int(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `grupos_consagracion`
--
ALTER TABLE `grupos_consagracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `integrantes`
--
ALTER TABLE `integrantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD CONSTRAINT `asistencia_ibfk_1` FOREIGN KEY (`integrante_id`) REFERENCES `integrantes` (`id`);

--
-- Filtros para la tabla `encargados_consagracion`
--
ALTER TABLE `encargados_consagracion`
  ADD CONSTRAINT `encargados_consagracion_ibfk_1` FOREIGN KEY (`grupo_id`) REFERENCES `grupos_consagracion` (`id`);

--
-- Filtros para la tabla `integrantes`
--
ALTER TABLE `integrantes`
  ADD CONSTRAINT `integrantes_ibfk_1` FOREIGN KEY (`grupo_id`) REFERENCES `grupos_consagracion` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
