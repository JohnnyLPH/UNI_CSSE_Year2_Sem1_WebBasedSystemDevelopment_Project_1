-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2022 at 06:25 AM
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
(1, 'admin', '$2y$10$YVeg46P7ezx1SQB8TcrFBexLFiQ6P7pxxTejduyk0d9jxRl6.7VPK', '2022-01-13 13:10:14'),
(2, 'LPH', '$2y$10$eREyR0w.G453niOeJP9d0O1BPjsK6GRWtmaswKi.kzoN4BNroD7c.', '2022-01-13 13:15:06'),
(9, 'Yuki', '$2y$10$imUkUkgDE2Tw7/LvblSa6eh7NimHKQJPXqsuMBZyfBoQeQP0EjLDi', '2022-01-13 12:58:56'),
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
  `phone` varchar(10) NOT NULL,
  `password` varchar(6) NOT NULL,
  `gender` varchar(6) NOT NULL,
  `state` varchar(30) NOT NULL,
  `registerDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `firstName`, `lastName`, `email`, `countryCode`, `phone`, `password`, `gender`, `state`, `registerDate`) VALUES
(1, 'Pikk Heang', 'Lau', '75359@siswa.unimas.my', '+60', '168966984', 'aB#123', 'male', 'Sarawak', '2021-09-04 11:35:52'),
(2, 'Pei Ying', 'Chung', '77237@siswa.unimas.my', '+60', '109628509', '#Ba123', 'female', 'Sarawak', '2022-01-04 11:35:52'),
(3, 'James', 'Pill', 'ja@email.com', '+60', '192659162', '123aB#', 'male', 'Selangor', '2022-01-04 11:35:52'),
(4, 'Assa', 'Lisa', 'asalid@email.com', '+60', '119281621', 'aS#123', 'female', 'Negeri Sembilan', '2022-01-04 11:35:52'),
(5, 'Nice', 'Ara', 'asnice@email.com', '+60', '147397412', 'aB#123', 'male', 'Selangor', '2022-01-04 11:35:52'),
(6, 'Dwayne', 'Johnson', 'dj@email.com', '+60', '166668866', 'aB#123', 'male', 'Kuala Lumpur', '2022-01-04 11:35:52'),
(9, 'Dave', 'Jobs', 'daveJ@email.com', '+60', '169283926', 'aB#123', 'male', 'Sarawak', '2022-01-10 15:36:38');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `memberId` int(11) NOT NULL,
  `stages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{}' CHECK (json_valid(`stages`)),
  `editable` tinyint(1) NOT NULL DEFAULT 1,
  `type` tinyint(4) NOT NULL,
  `business` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{}' CHECK (json_valid(`business`)),
  `fullName` varchar(30) DEFAULT NULL,
  `personal` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{}' CHECK (json_valid(`personal`)),
  `residentialAddress` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{}' CHECK (json_valid(`residentialAddress`)),
  `job` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{}' CHECK (json_valid(`job`)),
  `bank` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{}' CHECK (json_valid(`bank`)),
  `preferredDelivery` date DEFAULT NULL,
  `orderStatus` tinyint(4) NOT NULL,
  `orderStatusMessage` text DEFAULT NULL,
  `proposalDate` datetime DEFAULT NULL,
  `reviewDate` datetime DEFAULT NULL,
  `confirmDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `memberId`, `stages`, `editable`, `type`, `business`, `fullName`, `personal`, `residentialAddress`, `job`, `bank`, `preferredDelivery`, `orderStatus`, `orderStatusMessage`, `proposalDate`, `reviewDate`, `confirmDate`) VALUES
(1, 1, '{\"2\": 1, \"3\": 1, \"4\": 1, \"5\": 1, \"6\": 1}', 1, 0, '{}', NULL, '{\"fName\":\"John\",\"lName\":\"Doe\",\"email\":\"john@doe.com\",\"phone\":\"1234567890\",\"gender\":1,\"dob\":\"1999-01-15\"}', '{\"add1\":\"Senate House\",\"add2\":\"Malet St\",\"city\":\"London\",\"postcode\":\"WC1E7HU\",\"status\":1,\"livedYrs\":1,\"livedMths\":3}', '{\"title\":\"Software Engineer\",\"company\":\"Computer Solutions\",\"add1\":\"Senate House\",\"add2\":\"Malet St\",\"city\":\"London\",\"postcode\":\"WC1E7HU\",\"telephone\":\"1234567890\",\"companyDescription\":\"Create Software\",\"salary\":10000,\"incomeDescription\":\"No additional bonus\",\"workedYrs\":3,\"workedMths\":6}', '{\"bankName\":\"HSBC in Covent Garden\",\"add1\":\"16 King Street\",\"add2\":\"Covent Garden\",\"city\":\"London\",\"postcode\":\"WC2E8JF\",\"sortCode\":\"400409\",\"accountName\":\"John Doe\",\"accountNum\":\"31926819\",\"accountYr\":2020}', '2023-11-01', 0, NULL, '2022-01-13 00:00:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `memberId` int(11) NOT NULL,
  `carId` int(11) NOT NULL,
  `orderId` int(11) NOT NULL,
  `transactionDate` datetime NOT NULL DEFAULT current_timestamp(),
  `creditCard` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{}',
  `receipt` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `memberId`, `carId`, `orderId`, `transactionDate`, `creditCard`, `receipt`) VALUES
(1, 1, 1, 1, '2021-09-10 10:13:11', '{\"cardNo\":\"1234123412341234\", \"paymentAmount\":\"2052\"}', 'testReceipt');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `memberlog`
--
ALTER TABLE `memberlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  ADD CONSTRAINT `Member's Log` FOREIGN KEY (`memberId`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`memberId`) REFERENCES `members` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
