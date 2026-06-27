-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 05-10-2023 a las 16:03:42
-- Versión del servidor: 5.7.33
-- Versión de PHP: 8.1.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `shop`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `person_type` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `second_name` varchar(255) DEFAULT NULL,
  `first_lastname` varchar(255) DEFAULT NULL,
  `second_lastname` varchar(255) DEFAULT NULL,
  `document_type` varchar(255) DEFAULT NULL,
  `document_number` varchar(255) DEFAULT NULL,
  `company_razon` varchar(255) DEFAULT NULL,
  `company_type` varchar(255) DEFAULT NULL,
  `company_email` varchar(255) DEFAULT NULL,
  `company_actividad` varchar(255) DEFAULT NULL,
  `company_phone` varchar(255) DEFAULT NULL,
  `company_document_type` varchar(255) DEFAULT NULL,
  `company_document_number` varchar(255) DEFAULT NULL,
  `documento_file` varchar(255) DEFAULT NULL,
  `camara_file` varchar(255) DEFAULT NULL,
  `rut_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id`, `user_id`, `person_type`, `first_name`, `second_name`, `first_lastname`, `second_lastname`, `document_type`, `document_number`, `company_razon`, `company_type`, `company_email`, `company_actividad`, `company_phone`, `company_document_type`, `company_document_number`, `documento_file`, `camara_file`, `rut_file`, `created_at`, `updated_at`, `deleted_at`) VALUES
(11, 39, 'Juridical', 'Test', '', 'Test', 'Test', 'C.C', '2141241', 'Test', 'S.A.S', 'Test@test.com', 'Test', '+57 1 466 56556225', 'C.C', '21412412', 'download_1696514104.pdf', 'download_1696514104.pdf', 'download_1696514104.pdf', '2023-10-05 17:55:04', '2023-10-05 17:55:04', NULL),
(13, 29, 'Juridical', 'Test', NULL, 'Test', NULL, 'C.C', 'Test', 'plasticos LC', 'S.A.S', 'Test@test.com', 'Test', '+57 1 464 6565627', NULL, 'Test', '', '', '', '2023-10-05 18:23:37', '2023-10-05 19:36:45', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
