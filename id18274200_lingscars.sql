-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 17, 2022 at 01:58 PM
-- Server version: 10.5.12-MariaDB
-- PHP Version: 7.3.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `id18274200_lingscars`
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
(1, 'admin', '$2y$10$ptLn05BMkrq9jjd7WV6Le.AFCDLN2TdPhyYt9XQ96gMw/LiQxzatm', '2022-01-16 09:07:07'),
(2, 'LPH', '$2y$10$VZr8bW7ALTsfMTT7d4cvpuaMhEJG74NiuihY92II4aOHlY64wN6qC', '2022-01-17 13:37:19'),
(9, 'Yuki', '$2y$10$1If4/Vcdi/gmNCK3Gf4y/eEzx6Y0Gj2l3uOxYhczXcW5A4qGEhcJe', '2022-01-17 13:40:03'),
(10, 'James', '$2y$10$L83B22yVT0qUuHojH7yc0.nQ1EpoUnSChAPXo/6qL40eeaEirLEOe', '2022-01-13 12:59:15'),
(16, 'anotherAdmin', '$2y$10$EFo/GGgKKW9nugzBLQbUVuBdK0Br5diKBBSGBIj8TgfYIBUt7E81K', NULL),
(21, 'WBSD', '$2y$10$EFo/GGgKKW9nugzBLQbUVuBdK0Br5diKBBSGBIj8TgfYIBUt7E81K', NULL),
(22, 'LastTest', '$2y$10$EFo/GGgKKW9nugzBLQbUVuBdK0Br5diKBBSGBIj8TgfYIBUt7E81K', NULL),
(23, 'TestChart', '$2y$10$EFo/GGgKKW9nugzBLQbUVuBdK0Br5diKBBSGBIj8TgfYIBUt7E81K', NULL),
(24, 'Dire', '$2y$10$EFo/GGgKKW9nugzBLQbUVuBdK0Br5diKBBSGBIj8TgfYIBUt7E81K', NULL),
(26, 'TestQueryString', '$2y$10$EFo/GGgKKW9nugzBLQbUVuBdK0Br5diKBBSGBIj8TgfYIBUt7E81K', NULL),
(27, 'anotherNewAdmin', '$2y$10$EFo/GGgKKW9nugzBLQbUVuBdK0Br5diKBBSGBIj8TgfYIBUt7E81K', NULL),
(29, 'anotherNewAdmin2', '$2y$10$EFo/GGgKKW9nugzBLQbUVuBdK0Br5diKBBSGBIj8TgfYIBUt7E81K', '2022-01-13 12:59:27');

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
(3, 'Volkswagen'),
(4, 'Peugeot'),
(5, 'Vauxhall'),
(6, 'Mazda'),
(7, 'Suzuki'),
(8, 'Citroen'),
(9, 'Hyundai'),
(10, 'Cupra'),
(11, 'Audi');

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
(1, 1, 'Arona', 228, 48, 9, '1.0 TSI SE Technology (95bhp) Hatchback 5dr Petrol Manual', 'transparent.png', '/img/car/seat_arona_1641480811/', '2022-01-05 18:41:05', '2022-01-08 15:29:33'),
(2, 1, 'Ibiza', 228, 36, 9, '1.0 TSI FR (95bhp) Hatchback 5dr Petrol Manual', 'transparent.png', '/img/car/seat_ibiza_1641480845/', '2022-01-06 22:54:05', '2022-01-06 22:54:05'),
(3, 2, 'Octavia Estate', 240, 24, 9, '1.0 TSI SE Technology (110bhp) (5 seats) Estate 5dr Petrol Manual', 'transparent.png', '/img/car/skoda_octavia_estate_1641481253/', '2022-01-06 23:00:53', '2022-01-06 23:00:53'),
(4, 3, 'Golf MK8', 263, 36, 9, '1.5 TSI Life (150bhp) Hatchback 5dr Petrol Manual', 'transparent.png', '/img/car/volkswagen_golf_mk8_1641481510/', '2022-01-06 23:05:10', '2022-01-06 23:05:10'),
(5, 4, 'Partner Van', 265, 18, 3, '1.5 1000 BlueHDi 100 Professional Premium (102bhp) (3 seats) Van Diesel Manual', 'transparent.png', '/img/car/peugeot_partner_van_1641482723/', '2022-01-06 23:25:23', '2022-01-06 23:25:23'),
(6, 5, 'Combo Cargo Van', 280, 36, 6, '1.5 2300 D ps H1 Dynamic (100bhp) Van Diesel Manual', 'transparent.png', '/img/car/vauxhall_combo_cargo_van_1641482891/', '2022-01-06 23:28:11', '2022-01-06 23:28:11'),
(7, 6, 'MX-30', 281, 36, 9, '35.5 107kW SE-L Lux kWh 35.5kWh (145bhp) Hatchback 5dr Electric Automatic', 'transparent.png', '/img/car/mazda_mx-30_1641523361/', '2022-01-07 10:42:41', '2022-01-08 15:46:37'),
(8, 7, 'Swace', 293, 36, 6, '1.8 Hybrid SZ5 (122bhp) Estate 5dr Petrol/electric CVT', 'transparent.png', '/img/car/suzuki_swace_1641523446/', '2022-01-07 10:44:06', '2022-01-07 10:44:06'),
(9, 8, 'e-C4', 304, 36, 9, '100kW Sense Plus 50kWh Hatchback 5dr Electric Automatic', 'transparent.png', '/img/car/citroen_e-c4_1641523675/', '2022-01-07 10:47:55', '2022-01-08 15:48:41'),
(10, 9, 'Ioniq', 304, 36, 9, '100kW Premium SE 38kWh (136bhp) Hatchback 5dr 1cc', 'transparent.png', '/img/car/hyundai_ioniq_1641523942/', '2022-01-07 10:52:22', '2022-01-11 20:34:40'),
(11, 9, 'Santa Fe Estate', 347, 18, 9, '1.6 TGDi Hybrid Premium (230bhp) Estate 5dr Petrol/electric Automatic', 'transparent.png', '/img/car/hyundai_santa_fe_estate_1641524078/', '2022-01-07 10:54:38', '2022-01-07 10:54:38'),
(13, 10, 'Leon', 352, 48, 9, '2.0 TSI VZ2 (245bhp) Hatchback 5dr Petrol DSG', 'transparent.png', '/img/car/cupra_leon_1641526302/', '2022-01-07 11:31:42', '2022-01-07 11:31:42'),
(14, 11, 'Q2 EState', 383, 36, 9, '30 TFSI Technik (110bhp) Estate 5dr Petrol Manual', 'transparent.png', '/img/car/audi_q2_estate_1641526403/', '2022-01-07 11:33:23', '2022-01-07 11:33:23'),
(15, 3, 'Tiguan Allspace Estate', 384, 36, 9, '1.5 TSI Life (150bhp) Estate 5dr Petrol DSG', 'transparent.png', '/img/car/volkswagen_tiguan_allspace_estate_1641526597/', '2022-01-07 11:36:37', '2022-01-07 11:36:37'),
(16, 9, 'Tucson', 391, 36, 9, '1.6 TGDi Hybrid 2WD Ultimate (230bhp) Estate 5dr Petrol/electric Automatic', 'transparent.png', '/img/car/hyundai_tucson_1641526663/', '2022-01-07 11:37:43', '2022-01-07 11:37:43'),
(17, 2, 'Superb Estate', 394, 36, 6, '1.4 TSI iV SE Technology DSG (218bhp) (5 seats) Estate 5dr Petrol/plugin Elec Hybrid', 'transparent.png', '/img/car/skoda_superb_estate_1641526757/', '2022-01-07 11:39:17', '2022-01-07 11:39:17'),
(19, 11, 'A4 Estate', 478, 36, 9, '2.0 35 TFSI (150bhp) Technik Estate 5dr 1984cc', 'transparent.png', '/img/car/audi_a4_estate_1641527000/', '2022-01-07 11:43:20', '2022-01-08 15:29:13');

-- --------------------------------------------------------

--
-- Table structure for table `leasedCars`
--

CREATE TABLE `leasedCars` (
  `id` int(11) NOT NULL,
  `memberId` int(11) NOT NULL,
  `orderId` int(11) NOT NULL,
  `carId` int(11) NOT NULL,
  `paymentMthsCompleted` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 1,
  `statusMessage` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `leaseDate` datetime NOT NULL DEFAULT current_timestamp(),
  `returnDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `leasedCars`
--

INSERT INTO `leasedCars` (`id`, `memberId`, `orderId`, `carId`, `paymentMthsCompleted`, `status`, `statusMessage`, `leaseDate`, `returnDate`) VALUES
(1, 18, 5, 9, 9, 1, NULL, '2022-01-17 13:38:52', NULL),
(2, 18, 5, 7, 9, 1, NULL, '2022-01-17 13:38:52', NULL);

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
(7, 12, '2022-01-14 14:09:17', '2022-01-14 14:14:17', 300),
(8, 16, '2022-01-15 07:42:17', '2022-01-15 07:50:09', 472),
(9, 17, '2022-01-17 13:25:20', '2022-01-17 13:30:20', 300),
(12, 14, '2022-01-17 09:51:15', '2022-01-17 09:56:15', 300),
(13, 18, '2022-01-17 13:38:24', '2022-01-17 13:43:24', 300);

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `email` varchar(256) NOT NULL,
  `countryCode` varchar(4) NOT NULL DEFAULT '+44',
  `phone` varchar(11) NOT NULL,
  `password` varchar(256) NOT NULL,
  `gender` varchar(1) NOT NULL,
  `state` varchar(30) DEFAULT NULL,
  `registerDate` datetime NOT NULL DEFAULT current_timestamp(),
  `dob` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `firstName`, `lastName`, `email`, `countryCode`, `phone`, `password`, `gender`, `state`, `registerDate`, `dob`) VALUES
(12, 'Yuki', 'Chung', 'yuki@email.com', '+44', '1234512345', '$2y$10$KQeZ04rqq7u0epVDqBQV5Ov0E9PrJdUxt5.hceBl508rGQsvp2JWO', '2', 'UK-04', '2022-01-14 12:27:51', '2000-08-01'),
(13, 'Leong', 'Weehong', '75405@siswa.unimas.my', '+44', '123456789', '$2y$10$HbZUhAnY8tofY01wU9gAnOoypIa5EzdnqxEc3FNmBtaEpRreajz5q', '1', 'UK-01', '2022-01-15 06:23:54', '2021-12-10'),
(14, 'Leong', 'Weehong', 'lweehong99@gmail.com', '+44', '123456789', '$2y$10$9OasH9rZ4PFGJHUp8HbrG.A87HmCvB1lYb8byFx3awwUir4HVvRcy', '1', 'UK-03', '2022-01-15 06:26:42', '2021-12-30'),
(15, 'Leong', 'Weehong', 'jzleong@outlook.com', '+44', '123456789', '$2y$10$Zf.jAAFAdKrWRcVKXaazEek2EP3Qv2PUGTn2CS2i1gvJ20Dz8B2hm', '1', 'UK-02', '2022-01-15 06:42:01', '2022-01-06'),
(16, 'Johnny', 'Lau', 'johnnyl516@hotmail.com', '+44', '123456789', '$2y$10$aMYmfhK8t72bISIuYWHazePVlR.quqJxlR81DeuxlUwIyzPUHFxpu', '1', 'UK-01', '2022-01-15 07:42:02', '2000-05-16'),
(17, 'Yuki', 'Chung', '77237@siswa.unimas.my', '+44', '111222333', '$2y$10$gayu6XkfwFcCh/hQI1zjgOBcNfKB4M6w8o/CYaBRRVdWSIzrIOkqi', '2', 'UK-02', '2022-01-15 08:38:07', '2021-08-08'),
(18, 'Test', 'Last', '75359@siswa.unimas.my', '+44', '123412341', '$2y$10$GBgbEV1GKVzsGWRuzmN79uliZy/kDNzkaYLaZNUrZP.BH4rq/5Y9S', '1', 'UK-01', '2022-01-17 12:01:53', '0002-02-02');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `memberId` int(11) NOT NULL,
  `stages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{"1":1}',
  `editable` tinyint(1) NOT NULL DEFAULT 1,
  `type` tinyint(4) NOT NULL,
  `fullName` varchar(30) DEFAULT NULL,
  `carsId` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{}' CHECK (json_valid(`carsId`)),
  `leasedCarsId` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT '{}' CHECK (json_valid(`leasedCarsId`)),
  `personal` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{}' CHECK (json_valid(`personal`)),
  `residentialAddress` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{}' CHECK (json_valid(`residentialAddress`)),
  `job` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{}' CHECK (json_valid(`job`)),
  `company` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{}' CHECK (json_valid(`company`)),
  `bank` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{}' CHECK (json_valid(`bank`)),
  `preferredDelivery` date DEFAULT NULL,
  `orderStatus` tinyint(4) NOT NULL DEFAULT 4,
  `orderStatusMessage` text DEFAULT NULL,
  `proposalDate` datetime DEFAULT NULL,
  `reviewDate` datetime DEFAULT NULL,
  `confirmDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `memberId`, `stages`, `editable`, `type`, `fullName`, `carsId`, `leasedCarsId`, `personal`, `residentialAddress`, `job`, `company`, `bank`, `preferredDelivery`, `orderStatus`, `orderStatusMessage`, `proposalDate`, `reviewDate`, `confirmDate`) VALUES
(3, 17, '{\"1\":1}', 1, 2, NULL, '{\"9\":1,\"10\":1}', '{}', '{}', '{}', '{}', '{}', '{}', NULL, 4, NULL, NULL, NULL, NULL),
(5, 18, '{\"1\": 1, \"2\": 1, \"3\": 1, \"4\": 1, \"5\": 1, \"6\": 1}', 0, 1, NULL, '{\"9\":1,\"7\":1}', '{\"1\":9,\"2\":7}', '{\"firstName\":\"First\",\"lastName\":\"Last\",\"email\":\"75359@siswa.unimas.my\",\"phone\":\"1213412341\",\"gender\":1,\"dob\":\"1946-02-02\"}', '{\"add1\":\"Address 1\",\"add2\":\"Address 2\",\"city\":\"The Town\",\"postcode\":\"12345\",\"status\":1,\"livedYrs\":9,\"livedMths\":0}', '{\"title\":\"Working At The Company\",\"salary\":99999,\"incomeDescription\":\"Nothing to say\",\"workedYrs\":9,\"workedMths\":false}', '{\"name\":\"Company\",\"add1\":\"C Address 1\",\"add2\":\"C Address 2\",\"city\":\"C Town\",\"postcode\":\"12341\",\"email\":\"cEmail@email.com\",\"telephone\":\"123412341\",\"description\":\"A company\"}', '{\"name\":\"Bank Name\",\"add1\":\"Bank A 1\",\"add2\":\"Bank A 2\",\"city\":\"Bank Town\",\"postcode\":\"34123\",\"sortCode\":\"123123\",\"accountName\":\"Test Name\",\"accountNum\":\"1234123\",\"accountYr\":2000}', '2022-12-01', 7, '', '2022-01-17 12:05:02', '2022-01-17 12:07:51', '2022-01-17 13:38:52');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `memberId` int(11) NOT NULL,
  `leasedCars` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{}' CHECK (json_valid(`leasedCars`)),
  `orderId` int(11) NOT NULL,
  `transactionDate` datetime NOT NULL DEFAULT current_timestamp(),
  `creditCard` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{}',
  `amount` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `memberId`, `leasedCars`, `orderId`, `transactionDate`, `creditCard`, `amount`) VALUES
(4, 18, '{\"1\":{\"carId\":9,\"MthsPaid\":\"9\"},\"2\":{\"carId\":7,\"MthsPaid\":\"9\"}}', 5, '2022-01-17 13:38:52', '{\"name\":\"Dwayne Johnson\",\"number\":\"12345123\",\"expiry\":\"12\\/30\",\"cvv\":1234}', 5265);

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
-- Indexes for table `leasedCars`
--
ALTER TABLE `leasedCars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `memberId` (`memberId`),
  ADD KEY `orderId` (`orderId`);

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
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `memberId` (`memberId`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `leasedCars`
--
ALTER TABLE `leasedCars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `memberlog`
--
ALTER TABLE `memberlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cars`
--
ALTER TABLE `cars`
  ADD CONSTRAINT `Car Brand` FOREIGN KEY (`brandId`) REFERENCES `brands` (`id`);

--
-- Constraints for table `leasedCars`
--
ALTER TABLE `leasedCars`
  ADD CONSTRAINT `leasedCars_ibfk_1` FOREIGN KEY (`memberId`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leasedCars_ibfk_2` FOREIGN KEY (`orderId`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `memberlog`
--
ALTER TABLE `memberlog`
  ADD CONSTRAINT `Member's Log` FOREIGN KEY (`memberId`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`memberId`) REFERENCES `members` (`id`) ON DELETE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
