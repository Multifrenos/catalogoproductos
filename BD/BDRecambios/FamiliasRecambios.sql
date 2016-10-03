-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 03, 2016 at 12:41 PM
-- Server version: 5.5.52-0+deb8u1
-- PHP Version: 5.6.24-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `Recambios`
--

-- --------------------------------------------------------

--
-- Table structure for table `FamiliasRecambios`
--

CREATE TABLE IF NOT EXISTS `FamiliasRecambios` (
`id` int(11) NOT NULL,
  `id_Padre` int(11) NOT NULL,
  `Familia_es` text CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `FamiliasRecambios`
--

INSERT INTO `FamiliasRecambios` (`id`, `id_Padre`, `Familia_es`) VALUES
(1, 0, 'Filtros'),
(4, 7, 'Rodamientos'),
(5, 7, 'Juntas Homocinética / Cardán'),
(7, 0, 'Dirección / Suspensión'),
(9, 0, 'Frenos'),
(14, 9, 'Pastillas'),
(15, 9, 'Discos'),
(16, 1, 'Filtros Aceite'),
(17, 9, 'Sensores Frenos'),
(18, 9, 'Zapatas'),
(19, 9, 'Tambores'),
(20, 1, 'Filtros Aire'),
(21, 1, 'Filtros Combustible'),
(22, 1, 'Filtros Habitaculo'),
(23, 7, 'Amortiguadores'),
(24, 7, 'Brazos Suspensión'),
(25, 7, 'Suspensión'),
(26, 0, 'Piezas Motor'),
(27, 26, 'Kit de Distribución'),
(28, 26, 'Bombas'),
(29, 26, 'Poleas');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `FamiliasRecambios`
--
ALTER TABLE `FamiliasRecambios`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `FamiliasRecambios`
--
ALTER TABLE `FamiliasRecambios`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=30;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
