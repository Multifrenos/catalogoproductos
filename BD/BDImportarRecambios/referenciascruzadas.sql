-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 11-03-2017 a las 01:17:18
-- Versión del servidor: 5.5.54-0ubuntu0.14.04.1
-- Versión de PHP: 5.5.9-1ubuntu4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `importarrecambios`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `referenciascruzadas`
--

CREATE TABLE IF NOT EXISTS `referenciascruzadas` (
  `linea` int(11) NOT NULL,
  `RefProveedor` text COLLATE utf8_spanish_ci NOT NULL,
  `Fabr_Recambio` text COLLATE utf8_spanish_ci NOT NULL,
  `Ref_Fabricante` text COLLATE utf8_spanish_ci NOT NULL,
  `Estado` text COLLATE utf8_spanish_ci NOT NULL,
  `RecambioID` int(11) NOT NULL,
  `IdFabricaCruzado` int(11) NOT NULL,
  UNIQUE KEY `linea` (`linea`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
