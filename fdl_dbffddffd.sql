-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 24, 2026 at 07:57 AM
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
-- Database: `fdl_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(6) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$91moizmJQydYJLIRsvBaMuvO.Ixx3nKQZhvO74JiZr45VvN5hi3Mi', '2026-02-24 04:53:17');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(6) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `game_type` enum('Datewise','Timewise') DEFAULT 'Datewise',
  `time_slot` varchar(100) DEFAULT NULL,
  `interval_mins` int(11) DEFAULT 30,
  `logo_path` varchar(255) DEFAULT NULL,
  `last_modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `name`, `game_type`, `time_slot`, `interval_mins`, `logo_path`, `last_modified`) VALUES
(1, 'RJ Delhi', 'Timewise', NULL, 15, 'logo_1771909669.png', '2026-02-24 05:07:49'),
(2, 'RJ Mumbai', 'Timewise', NULL, 30, 'logo_1771912487.png', '2026-02-24 05:54:47'),
(3, 'RJ Kolkata', 'Timewise', NULL, 60, 'logo_1771914474.png', '2026-02-24 06:27:54'),
(4, 'RJ Hyderabad', 'Timewise', NULL, 60, 'logo_1771914505.png', '2026-02-24 06:28:25'),
(5, 'Sharjah', 'Datewise', NULL, 1440, 'logo_1771914689.png', '2026-02-24 06:31:29'),
(7, '4 Minar', 'Datewise', NULL, 1440, 'logo_1771915206.png', '2026-02-24 06:40:06'),
(8, 'Gurgaon', 'Datewise', NULL, 1140, 'logo_1771915257.png', '2026-02-24 06:40:57'),
(9, 'Kalyan', 'Datewise', NULL, 1440, 'logo_1771915281.png', '2026-02-24 06:41:21'),
(10, 'Desawar', 'Datewise', NULL, 1440, 'logo_1771915316.png', '2026-02-24 06:41:56'),
(11, 'Faridabad', 'Datewise', NULL, 1440, 'logo_1771915336.png', '2026-02-24 06:42:16'),
(12, 'Ghaziabad', 'Datewise', NULL, 1440, 'logo_1771915373.png', '2026-02-24 06:42:53'),
(13, 'Gali', 'Datewise', NULL, 1440, 'logo_1771915418.png', '2026-02-24 06:43:38');

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(6) UNSIGNED NOT NULL,
  `game_id` int(6) UNSIGNED DEFAULT NULL,
  `lock_date` date NOT NULL,
  `lock_time` time NOT NULL,
  `lock_value` varchar(50) NOT NULL,
  `last_modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `game_id`, `lock_date`, `lock_time`, `lock_value`, `last_modified`) VALUES
(1, 1, '2026-02-24', '11:30:00', '13', '2026-02-24 05:56:20'),
(2, 2, '2026-02-24', '11:30:00', '32', '2026-02-24 05:55:15'),
(3, 1, '2026-02-24', '11:45:00', '12', '2026-02-24 06:24:52'),
(4, 1, '2026-02-24', '12:00:00', '72', '2026-02-24 06:24:30'),
(5, 2, '2026-02-24', '12:00:00', '69', '2026-02-24 06:31:38'),
(6, 3, '2026-02-24', '13:00:00', '39', '2026-02-24 06:36:36'),
(7, 4, '2026-02-24', '13:00:00', '57', '2026-02-24 06:36:58'),
(8, 1, '2026-02-24', '12:15:00', '16', '2026-02-24 06:45:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
