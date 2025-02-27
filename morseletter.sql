-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 27, 2025 at 10:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web`
--

-- --------------------------------------------------------

--
-- Table structure for table `morseletter`
--

CREATE TABLE `morseletter` (
  `letterId` int(11) NOT NULL,
  `letter` varchar(1) DEFAULT NULL,
  `lengte` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `morseletter`
--

INSERT INTO `morseletter` (`letterId`, `letter`, `lengte`) VALUES
(1, 'a', 12),
(2, 'b', 2111),
(3, 'c', 2121),
(4, 'd', 211),
(5, 'e', 1),
(6, 'f', 1121),
(7, 'g', 221),
(8, 'h', 111),
(9, 'i', 11),
(10, 'j', 1222),
(11, 'k', 212),
(12, 'm', 22),
(13, 'n', 21),
(14, 'o', 222),
(15, 'p', 1221),
(16, 'q', 2212),
(17, 'r', 121),
(18, 's', 111),
(19, 't', 2),
(20, 'u', 112),
(21, 'v', 1112),
(22, 'w', 122),
(23, 'x', 2112),
(24, 'y', 2122),
(25, 'z', 2211),
(26, '1', 12222),
(27, '2', 11222),
(28, '2', 11122),
(29, '3', 11122),
(30, '4', 11112),
(31, '5', 11111),
(32, '6', 21111),
(33, '7', 22111),
(34, '8', 22211),
(35, '9', 22221),
(36, '0', 22222),
(37, '?', 112211),
(38, '!', 212122),
(39, '.', 121212),
(40, ',', 221122),
(41, ';', 212121),
(42, ':', 222111),
(43, '+', 12121),
(44, '-', 21112),
(45, '/', 21121),
(46, '=', 21112);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `morseletter`
--
ALTER TABLE `morseletter`
  ADD PRIMARY KEY (`letterId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `morseletter`
--
ALTER TABLE `morseletter`
  MODIFY `letterId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
