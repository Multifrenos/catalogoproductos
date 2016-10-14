-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 14, 2016 at 12:13 PM
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
-- Table structure for table `referenciascruzadas`
--

CREATE TABLE IF NOT EXISTS `referenciascruzadas` (
  `linea` int(11) NOT NULL,
  `RefProveedor` text COLLATE utf8_spanish_ci NOT NULL,
  `Fabr_Recambio` text COLLATE utf8_spanish_ci NOT NULL,
  `Ref_Fabricante` text COLLATE utf8_spanish_ci NOT NULL,
  `Estado` text COLLATE utf8_spanish_ci NOT NULL,
  `RecambioID` int(11) NOT NULL,
  `IdFabricanteRec` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `referenciascruzadas`
--
ALTER TABLE `referenciascruzadas`
 ADD UNIQUE KEY `linea` (`linea`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
