-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 14, 2016 at 11:59 AM
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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `recambios`
--
ALTER TABLE `recambios`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `recambios`
--
ALTER TABLE `recambios`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3131;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
