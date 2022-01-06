-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2022 at 04:06 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lingscars`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `adminName` varchar(128) DEFAULT NULL,
  `adminPassword` varchar(256) DEFAULT NULL,
  `lastLogin` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `adminName`, `adminPassword`, `lastLogin`) VALUES
(1, 'admin', 'G03abc-abc03G', '2022-01-06 12:21:38'),
(2, 'LPH', 'HelloWorld123-', '2022-01-06 22:33:17'),
(9, 'Yuki', 'Yuki123-', '2021-12-30 00:11:32'),
(10, 'James', 'Ja123-', NULL),
(16, 'anotherAdmin', 'aB#123', NULL),
(21, 'WBSD', 'aB#123', NULL),
(22, 'LastTest', 'aB#123', NULL),
(23, 'TestChart', 'aB#123', NULL),
(24, 'Dire', 'aB#123', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `brandName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `brandName`) VALUES
(1, 'Seat'),
(2, 'Skoda'),
(3, 'Volkswagen');

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `brandId` int(11) NOT NULL,
  `carModel` varchar(100) NOT NULL,
  `monthPrice` int(11) NOT NULL,
  `leaseTime` int(11) NOT NULL,
  `initialPay` int(11) NOT NULL,
  `carDesc` varchar(512) NOT NULL,
  `carImage` varchar(256) NOT NULL,
  `imagePath` varchar(512) NOT NULL,
  `dateAdded` datetime NOT NULL DEFAULT current_timestamp(),
  `dateEdited` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `brandId`, `carModel`, `monthPrice`, `leaseTime`, `initialPay`, `carDesc`, `carImage`, `imagePath`, `dateAdded`, `dateEdited`) VALUES
(1, 1, 'Arona', 228, 48, 9, '1.0 TSI SE Technology (95bhp) Hatchback 5dr Petrol Manual', 'transparent.png', '/img/car/seat_arona_1641480811/', '2022-01-05 18:41:05', '2022-01-05 18:41:05'),
(2, 1, 'Ibiza', 228, 36, 9, '1.0 TSI FR (95bhp) Hatchback 5dr Petrol Manual', 'transparent.png', '/img/car/seat_ibiza_1641480845/', '2022-01-06 22:54:05', '2022-01-06 22:54:05'),
(3, 2, 'Octavia Estate', 240, 24, 9, '1.0 TSI SE Technology (110bhp) (5 seats) Estate 5dr Petrol Manual', 'transparent.png', '/img/car/skoda_octavia_estate_1641481253/', '2022-01-06 23:00:53', '2022-01-06 23:00:53'),
(4, 3, 'Golf MK8', 263, 36, 9, '1.5 TSI Life (150bhp) Hatchback 5dr Petrol Manual', 'transparent.png', '/img/car/volkswagen_golf_mk8_1641481510/', '2022-01-06 23:05:10', '2022-01-06 23:05:10');

-- --------------------------------------------------------

--
-- Table structure for table `memberlog`
--

CREATE TABLE `memberlog` (
  `id` int(11) NOT NULL,
  `memberId` int(11) NOT NULL,
  `loginDate` datetime DEFAULT NULL,
  `logoutDate` datetime DEFAULT NULL,
  `duration` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `memberlog`
--

INSERT INTO `memberlog` (`id`, `memberId`, `loginDate`, `logoutDate`, `duration`) VALUES
(1, 1, '2021-12-28 21:54:56', '2021-12-28 21:55:36', 40),
(2, 3, '2021-12-28 21:48:09', '2021-12-28 21:48:13', 4),
(3, 2, '2021-12-28 21:55:48', '2021-12-28 21:56:16', 28),
(4, 4, '2021-12-28 21:48:40', '2021-12-28 21:48:46', 6),
(5, 5, '2021-12-28 21:49:24', '2021-12-28 21:51:14', 110),
(6, 6, '2021-12-28 21:54:23', '2021-12-28 21:54:41', 18);

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `email` varchar(256) NOT NULL,
  `countryCode` varchar(4) NOT NULL,
  `phoneNo` varchar(10) NOT NULL,
  `password` varchar(6) NOT NULL,
  `gender` varchar(6) NOT NULL,
  `state` varchar(30) NOT NULL,
  `registerDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `firstName`, `lastName`, `email`, `countryCode`, `phoneNo`, `password`, `gender`, `state`, `registerDate`) VALUES
(1, 'Pikk Heang', 'Lau', '75359@siswa.unimas.my', '+60', '168966984', 'aB#123', 'male', 'Sarawak', '2022-01-04 11:35:52'),
(2, 'Pei Ying', 'Chung', '77237@siswa.unimas.my', '+60', '109628509', '#Ba123', 'female', 'Sarawak', '2022-01-04 11:35:52'),
(3, 'James', 'Pill', 'ja@email.com', '+60', '192659162', '123aB#', 'male', 'Selangor', '2022-01-04 11:35:52'),
(4, 'Assa', 'Lisa', 'asalid@email.com', '+60', '119281621', 'aS#123', 'female', 'Negeri Sembilan', '2022-01-04 11:35:52'),
(5, 'Nice', 'Ara', 'asnice@email.com', '+60', '147397412', 'aB#123', 'male', 'Selangor', '2022-01-04 11:35:52'),
(6, 'Dwayne', 'Johnson', 'dj@email.com', '+60', '166668866', 'aB#123', 'male', 'Kuala Lumpur', '2022-01-04 11:35:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `adminName` (`adminName`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Car Brand` (`brandId`);

--
-- Indexes for table `memberlog`
--
ALTER TABLE `memberlog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserLog` (`memberId`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `memberlog`
--
ALTER TABLE `memberlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cars`
--
ALTER TABLE `cars`
  ADD CONSTRAINT `Car Brand` FOREIGN KEY (`brandId`) REFERENCES `cars` (`id`);

--
-- Constraints for table `memberlog`
--
ALTER TABLE `memberlog`
  ADD CONSTRAINT `UserLog` FOREIGN KEY (`memberId`) REFERENCES `members` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
