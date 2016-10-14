-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 14, 2016 at 12:14 PM
-- Server version: 5.5.50-0+deb8u1
-- PHP Version: 5.6.24-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `importarrecambios`
--

-- --------------------------------------------------------

--
-- Table structure for table `referenciascversiones`
--

CREATE TABLE IF NOT EXISTS `referenciascversiones` (
  `RefProveedor` text CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `MarcaDescrip` text CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `ModeloVersion` text CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `VersionAcabado` text CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `kw` int(2) NOT NULL,
  `cv` int(3) NOT NULL,
  `Cm3` int(4) NOT NULL,
  `Ncilindros` int(2) NOT NULL,
  `FechaInici` date NOT NULL,
  `FechaFinal` text CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `TipoCombustible` text CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
