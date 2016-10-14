-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 14, 2016 at 11:53 AM
-- Server version: 5.5.50-0+deb8u1
-- PHP Version: 5.6.24-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `recambios`
--

-- --------------------------------------------------------

--
-- Table structure for table `cruces_referencias`
--

CREATE TABLE IF NOT EXISTS `cruces_referencias` (
`id` int(11) NOT NULL,
  `idReferenciaCruz` int(11) NOT NULL,
  `idRecambio` int(11) NOT NULL,
  `idFabricanteCruz` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fabricantes_recambios`
--

CREATE TABLE IF NOT EXISTS `fabricantes_recambios` (
`id` int(11) NOT NULL,
  `Nombre` text CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `Descripcion` varchar(100) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=394 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `familias_recambios`
--

CREATE TABLE IF NOT EXISTS `familias_recambios` (
`id` int(11) NOT NULL,
  `id_Padre` int(11) NOT NULL,
  `Familia_es` text CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `recambios`
--

CREATE TABLE IF NOT EXISTS `recambios` (
`id` int(11) NOT NULL,
  `Descripcion` text NOT NULL,
  `coste` decimal(11,2) NOT NULL,
  `margen` int(3) NOT NULL,
  `iva` int(2) NOT NULL,
  `pvp` decimal(11,2) NOT NULL,
  `IDFabricante` int(11) NOT NULL,
  `FechaActualiza` date NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3131 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `recamb_familias`
--

CREATE TABLE IF NOT EXISTS `recamb_familias` (
`id` int(11) NOT NULL,
  `IdRecambio` int(111) NOT NULL,
  `IdFamilia` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3131 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `referenciascruzadas`
--

CREATE TABLE IF NOT EXISTS `referenciascruzadas` (
`id` int(11) NOT NULL,
  `RecambioID` int(11) NOT NULL,
  `IdFabricanteCru` int(11) NOT NULL,
  `RefFabricanteCru` text CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3131 DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cruces_referencias`
--
ALTER TABLE `cruces_referencias`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fabricantes_recambios`
--
ALTER TABLE `fabricantes_recambios`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `familias_recambios`
--
ALTER TABLE `familias_recambios`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recambios`
--
ALTER TABLE `recambios`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recamb_familias`
--
ALTER TABLE `recamb_familias`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `referenciascruzadas`
--
ALTER TABLE `referenciascruzadas`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cruces_referencias`
--
ALTER TABLE `cruces_referencias`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fabricantes_recambios`
--
ALTER TABLE `fabricantes_recambios`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=394;
--
-- AUTO_INCREMENT for table `familias_recambios`
--
ALTER TABLE `familias_recambios`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `recambios`
--
ALTER TABLE `recambios`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3131;
--
-- AUTO_INCREMENT for table `recamb_familias`
--
ALTER TABLE `recamb_familias`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3131;
--
-- AUTO_INCREMENT for table `referenciascruzadas`
--
ALTER TABLE `referenciascruzadas`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3131;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
