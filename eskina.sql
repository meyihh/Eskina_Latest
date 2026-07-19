-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2026 at 07:24 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eskina`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remember_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `full_name`, `email`, `contact`, `address`, `username`, `password`, `created_at`, `remember_token`) VALUES
(3, 'Mae Cambri', 'isseihyoudou0614@gmail.com', '09477177432', '7th Avenue', 'Mae', '$2y$10$dN4ODy5NjTMJ6NZ1TbiCFe9Vyf5LBfSIIXpZcSitgNoqzKiXSHAbW', '2025-09-29 10:11:35', NULL),
(10, 'Iankyron', 'chaniankyron.bsit@gmail.com', '1234567890', '123 Main Street, Springfield', 'johndoe', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '2025-11-04 10:22:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `slug`, `name`) VALUES
(1, 'classics-section', 'Classics'),
(2, 'specials-section', 'Specials'),
(3, 'iceblendedcoffee', 'Iced Blended Coffee Based'),
(4, 'iceblendedcream', 'Iced Blended Cream Based'),
(5, 'tea', 'Tea'),
(6, 'refreshers', 'Refreshers'),
(7, 'anticoffee', 'Anti-Coffee'),
(8, 'extras', 'Extras'),
(9, 'ricebowls', 'Rice Bowls'),
(10, 'munchies', 'Munchies'),
(11, 'pasta', 'Pasta'),
(12, 'wraps', 'Wraps and Sandwiches');

-- --------------------------------------------------------

--
-- Table structure for table `dtr_logs`
--

CREATE TABLE `dtr_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time_in` datetime DEFAULT NULL,
  `photo_in` varchar(255) DEFAULT NULL,
  `time_out` datetime DEFAULT NULL,
  `photo_out` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dtr_logs`
--

INSERT INTO `dtr_logs` (`id`, `user_id`, `time_in`, `photo_in`, `time_out`, `photo_out`, `created_at`) VALUES
(27, 3, '2025-09-29 18:12:03', 'uploads/dtr/3_1759140723_selfie.png', '2025-09-29 18:12:11', 'uploads/dtr/3_1759140731_selfie.png', '2025-09-29 10:12:03'),
(28, 3, '2025-09-30 10:37:10', 'uploads/dtr/3_1759199830_selfie.png', '2025-09-30 10:37:21', 'uploads/dtr/3_1759199841_selfie.png', '2025-09-30 02:37:10');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `order_type` enum('DINE-IN','TAKE-OUT','ONLINE') NOT NULL DEFAULT 'DINE-IN',
  `customer_name` varchar(100) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `points` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `payment_method`, `order_type`, `customer_name`, `total_price`, `created_at`, `points`) VALUES
(198, 'CASH', 'ONLINE', 'Ian', 250.00, '2025-09-05 04:40:27', 1),
(199, 'GCASH', 'DINE-IN', 'Mae', 250.00, '2025-09-05 04:41:05', 0),
(200, 'CASH', 'TAKE-OUT', 'Kyron Bless', 250.00, '2025-09-05 04:41:31', 0),
(201, 'GCASH', 'ONLINE', 'Ian', 410.00, '2025-09-05 04:42:22', 1),
(202, 'CASH', 'TAKE-OUT', 'Bless', 1590.00, '2025-09-06 11:38:06', 0),
(203, 'CASH', 'DINE-IN', 'Ian', 1070.00, '2025-09-06 11:42:18', 0),
(204, 'CASH', 'ONLINE', 'Mae', 780.00, '2025-09-30 01:46:38', 3),
(205, 'CASH', 'DINE-IN', 'Allysa', 250.00, '2025-09-30 01:47:38', 0),
(206, 'CASH', 'TAKE-OUT', 'Ian', 910.00, '2025-09-30 01:48:00', 0),
(207, 'CASH', 'DINE-IN', 'Someone', 460.00, '2025-09-30 01:48:30', 0),
(208, 'CASH', 'DINE-IN', 'Someone', 160.00, '2025-09-30 01:48:41', 0),
(209, 'CASH', 'TAKE-OUT', 'Someone', 360.00, '2025-09-30 01:48:57', 0),
(210, 'CASH', 'DINE-IN', 'Ian', 260.00, '2025-09-30 01:49:30', 0),
(211, 'CASH', 'DINE-IN', 'mae', 160.00, '2025-09-30 02:15:53', 0),
(212, 'CASH', 'DINE-IN', 'allysa', 430.00, '2025-09-30 02:20:58', 0),
(213, 'CASH', 'DINE-IN', 'allysa', 160.00, '2025-09-30 02:21:08', 0),
(214, 'CASH', 'DINE-IN', 'allysa', 120.00, '2025-09-30 02:21:18', 0),
(215, 'CASH', 'DINE-IN', 'mae', 300.00, '2025-09-30 02:21:50', 0),
(216, 'CASH', 'DINE-IN', 'just', 130.00, '2025-09-30 02:22:04', 0),
(217, 'CASH', 'DINE-IN', 'allysa', 460.00, '2025-09-30 02:32:47', 0),
(218, 'CASH', 'DINE-IN', 'Mae', 160.00, '2025-10-01 06:46:50', 0),
(219, 'CASH', 'DINE-IN', 'Ian', 140.00, '2025-10-02 07:51:54', 1),
(220, 'CASH', 'DINE-IN', 'Ian', 300.00, '2025-10-02 10:25:33', 0),
(221, 'CASH', 'DINE-IN', 'Ian', 130.00, '2025-10-02 10:48:16', 0),
(222, 'CASH', 'DINE-IN', 'mae', 160.00, '2025-10-03 10:13:00', 0),
(223, 'CASH', 'DINE-IN', 'Ian', 140.00, '2025-10-03 12:08:08', 1),
(224, 'GCASH', 'DINE-IN', 'allysa', 140.00, '2025-10-23 03:45:09', 1),
(225, 'CASH', 'DINE-IN', 'mae', 255.00, '2025-10-23 03:46:00', 2),
(226, 'CASH', 'DINE-IN', 'Someone', 140.00, '2025-10-23 03:46:13', 1),
(227, 'CASH', 'DINE-IN', 'Ian', 130.00, '2025-10-23 10:48:09', 1),
(228, 'CASH', 'DINE-IN', 'mae', 140.00, '2025-10-31 03:18:22', 1),
(229, 'CASH', 'DINE-IN', 'mae', 130.00, '2025-11-03 07:46:07', 1),
(230, 'CASH', 'DINE-IN', 'Maeganda', 140.00, '2025-11-04 07:13:26', 1),
(231, 'CASH', 'DINE-IN', 'Ian', 600.00, '2025-11-04 07:13:50', 6),
(232, 'CASH', 'DINE-IN', 'allysa', 300.00, '2025-11-04 07:54:51', 3),
(233, 'CASH', 'DINE-IN', 'Ally', 130.00, '2025-11-04 11:37:38', 1),
(234, 'CASH', 'DINE-IN', 'mae', 120.00, '2025-11-04 12:09:42', 1),
(235, 'CASH', 'TAKE-OUT', 'Ally', 140.00, '2025-11-04 12:10:44', 1),
(236, 'GCASH', 'DINE-IN', 'asdw', 560.00, '2025-11-04 12:41:03', 5),
(237, 'CASH', 'DINE-IN', 'mae', 680.00, '2025-11-05 01:23:34', 6),
(238, 'CASH', 'DINE-IN', 'Ian', 140.00, '2025-11-05 01:32:12', 1),
(239, 'CASH', 'DINE-IN', 'allysa', 170.00, '2025-11-05 01:45:51', 1),
(240, 'CASH', 'DINE-IN', 'Ian', 130.00, '2025-11-05 02:10:44', 1),
(241, 'CASH', 'DINE-IN', 'Mae Cuite', 120.00, '2025-11-05 02:11:16', 1),
(242, 'CASH', 'DINE-IN', 'Ian', 120.00, '2025-11-05 02:20:04', 1),
(243, 'CASH', 'DINE-IN', 'Mae Cuite', 120.00, '2025-11-05 02:20:30', 1),
(244, 'CASH', 'DINE-IN', 'Mae Cuite', 120.00, '2025-11-05 02:20:49', 1),
(245, 'CASH', 'DINE-IN', 'Mae', 120.00, '2025-11-05 02:21:15', 1),
(246, 'CASH', 'DINE-IN', 'Ian', 120.00, '2025-11-05 02:21:36', 1),
(247, 'CASH', 'DINE-IN', 'Mae', 140.00, '2025-11-05 02:21:58', 1),
(248, 'CASH', 'DINE-IN', 'Mae Cuite', 120.00, '2025-11-05 02:28:22', 1),
(249, 'CASH', 'DINE-IN', 'Mae Cuite', 140.00, '2025-11-05 03:36:32', 1),
(250, 'CASH', 'TAKE-OUT', 'Mae Cuite', 130.00, '2025-11-05 03:36:50', 1),
(251, 'CASH', 'TAKE-OUT', 'Mae Cuite', 130.00, '2025-11-05 03:37:14', 1),
(252, 'CASH', 'DINE-IN', 'Mae', 140.00, '2025-11-05 03:37:59', 1),
(253, 'CASH', 'TAKE-OUT', 'Mae Cuite', 140.00, '2025-11-05 03:42:43', 1),
(254, 'CASH', 'DINE-IN', 'allysa', 140.00, '2025-11-05 03:43:58', 1),
(255, 'CASH', 'TAKE-OUT', 'Mae', 140.00, '2025-11-05 04:08:01', 1),
(256, 'CASH', 'DINE-IN', 'Mae', 140.00, '2025-11-05 04:11:51', 1),
(257, 'CASH', 'DINE-IN', 'Mae Cuite', 130.00, '2025-11-05 04:12:26', 1),
(258, 'CASH', 'TAKE-OUT', 'Mae Cuite', 120.00, '2025-11-05 04:12:40', 1),
(259, 'CASH', 'DINE-IN', 'Mae', 120.00, '2025-11-05 04:12:58', 1),
(260, 'CASH', 'TAKE-OUT', 'Mae Cuite', 140.00, '2025-11-05 04:13:10', 1),
(261, 'CASH', 'DINE-IN', 'Mae', 140.00, '2025-11-05 04:13:55', 1),
(262, 'CASH', 'TAKE-OUT', 'Mae Cuite', 140.00, '2025-11-05 04:14:19', 1),
(263, 'CASH', 'DINE-IN', 'Mae Cuite', 200.00, '2025-11-05 04:19:38', 2),
(264, 'CASH', 'TAKE-OUT', 'Mae Cuite', 140.00, '2025-11-05 04:19:56', 1),
(265, 'CASH', 'DINE-IN', 'Mae Cuite', 140.00, '2025-11-05 04:20:20', 1),
(266, 'CASH', 'DINE-IN', 'Mae Cuite', 140.00, '2025-11-05 04:20:31', 1),
(267, 'CASH', 'DINE-IN', 'allysa', 140.00, '2025-11-05 04:20:53', 1),
(268, 'CASH', 'DINE-IN', 'Mae Cuite', 140.00, '2025-11-05 04:25:27', 1),
(269, 'CASH', 'TAKE-OUT', 'Mae Cuite', 120.00, '2025-11-05 04:26:09', 1),
(270, 'CASH', 'DINE-IN', 'Mae Cuite', 120.00, '2025-11-05 04:28:01', 1),
(271, 'CASH', 'DINE-IN', 'Mae', 120.00, '2025-11-05 04:30:49', 1),
(272, 'CASH', 'DINE-IN', 'Mae Cuite', 120.00, '2025-11-05 04:31:12', 1),
(273, 'CASH', 'TAKE-OUT', 'Mae Cuite', 120.00, '2025-11-05 04:35:49', 1),
(274, 'CASH', 'DINE-IN', 'Mae Cuite', 120.00, '2025-11-05 04:37:18', 1),
(275, 'CASH', 'DINE-IN', '', 140.00, '2025-11-05 04:41:51', 1),
(276, 'CASH', 'DINE-IN', 'MAE', 130.00, '2025-11-05 04:51:17', 1),
(277, 'CASH', 'DINE-IN', 'Mae', 310.00, '2025-11-05 05:00:51', 3),
(278, 'CASH', 'DINE-IN', 'Mae', 270.00, '2025-11-06 08:43:47', 2),
(279, 'CASH', 'DINE-IN', 'mae', 140.00, '2025-11-06 08:46:18', 1),
(280, 'CASH', 'DINE-IN', 'Ian', 5015.00, '2025-11-06 10:09:41', 50),
(281, 'CASH', 'DINE-IN', 'Ian', 130.00, '2025-11-06 12:59:12', 1),
(282, 'CASH', 'DINE-IN', 'Allysa', 140.00, '2025-11-06 12:59:33', 1),
(283, 'CASH', 'DINE-IN', 'ian', 120.00, '2025-11-06 13:06:07', 1),
(284, 'CASH', 'DINE-IN', 'basta', 120.00, '2025-11-06 13:07:05', 1),
(285, 'CASH', 'DINE-IN', 'hala', 130.00, '2025-11-06 13:07:31', 1),
(286, 'CASH', 'DINE-IN', 'Mae', 300.00, '2025-11-06 13:34:48', 3),
(287, 'CASH', 'DINE-IN', 'EYAN', 180.00, '2025-11-06 13:39:42', 1),
(288, 'CASH', 'DINE-IN', 'HELLO', 130.00, '2025-11-06 14:50:07', 1),
(289, 'CASH', 'DINE-IN', 'IAN', 1910.00, '2025-11-06 15:15:58', 19),
(290, 'CASH', 'DINE-IN', 'Carl', 330.00, '2025-11-06 16:13:08', 3),
(291, 'CASH', 'DINE-IN', 'Mae Cuite', 170.00, '2025-11-06 16:14:09', 1),
(292, 'CASH', 'DINE-IN', 'Mae', 130.00, '2025-11-06 16:14:36', 1),
(293, 'CASH', 'DINE-IN', 'carl', 130.00, '2025-11-06 16:15:50', 1),
(294, 'CASH', 'DINE-IN', 'hays', 130.00, '2025-11-06 16:16:28', 1),
(295, 'CASH', 'DINE-IN', 'hoi', 140.00, '2025-11-06 16:17:18', 1),
(296, 'CASH', 'DINE-IN', 'Lei', 340.00, '2025-11-07 00:23:51', 3),
(297, 'CASH', 'DINE-IN', 'Ian Mylove', 140.00, '2025-11-07 00:30:00', 1),
(298, 'CASH', 'TAKE-OUT', 'HALA', 160.00, '2025-11-07 00:30:15', 1),
(299, 'CASH', 'DINE-IN', 'Allysa', 160.00, '2025-11-07 00:48:17', 1),
(300, 'CASH', 'DINE-IN', 'Bless', 250.00, '2025-11-07 00:48:36', 2),
(301, 'CASH', 'DINE-IN', 'Mae', 150.00, '2025-11-07 00:48:54', 1),
(302, 'CASH', 'DINE-IN', 'Manoza', 140.00, '2025-11-07 00:49:12', 1),
(303, 'CASH', 'DINE-IN', 'Hays', 320.00, '2025-11-07 00:49:38', 3),
(304, 'CASH', 'DINE-IN', 'lo', 320.00, '2025-11-07 00:51:08', 3),
(305, 'CASH', 'DINE-IN', 'hi', 160.00, '2025-11-07 00:51:19', 1),
(306, 'CASH', 'DINE-IN', 'jkansfaa', 160.00, '2025-11-07 00:51:33', 1),
(307, 'CASH', 'DINE-IN', 'try', 120.00, '2025-11-07 00:51:50', 1),
(308, 'CASH', 'DINE-IN', 'mae', 130.00, '2025-11-07 02:37:32', 1),
(309, 'CASH', 'DINE-IN', 'Mae', 160.00, '2025-11-07 07:42:49', 1),
(310, 'CASH', 'DINE-IN', 'ian', 440.00, '2025-11-08 03:11:45', 4),
(311, 'CASH', 'DINE-IN', 'ian', 140.00, '2025-11-08 03:11:55', 1),
(312, 'CASH', 'DINE-IN', 'dfghj', 330.00, '2025-11-08 09:12:08', 3),
(313, 'CASH', 'DINE-IN', 'mae', 130.00, '2025-11-08 09:24:13', 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `status` enum('PREPARING','DONE') NOT NULL DEFAULT 'PREPARING',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_name`, `price`, `quantity`, `status`, `created_at`) VALUES
(412, 193, 'Hazelnut', 160.00, 1, 'DONE', '2025-09-05 03:28:28'),
(413, 193, 'French Vanilla', 160.00, 1, 'DONE', '2025-09-05 03:28:28'),
(414, 193, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-09-05 03:28:28'),
(415, 193, 'Cafe Latte', 130.00, 1, 'DONE', '2025-09-05 03:28:28'),
(416, 194, 'Cafe Latte', 130.00, 1, 'DONE', '2025-09-05 03:29:58'),
(417, 194, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-09-05 03:29:58'),
(418, 194, 'French Vanilla', 160.00, 1, 'DONE', '2025-09-05 03:29:58'),
(419, 194, 'Hazelnut', 160.00, 1, 'DONE', '2025-09-05 03:29:58'),
(420, 195, 'Caramel Macchiato', 170.00, 1, 'DONE', '2025-09-05 04:24:33'),
(421, 196, 'Cafe Ilustrado', 140.00, 2, 'PREPARING', '2025-09-05 04:31:29'),
(422, 196, 'French Vanilla', 160.00, 2, 'PREPARING', '2025-09-05 04:31:29'),
(423, 196, 'Cafe Latte', 130.00, 1, 'PREPARING', '2025-09-05 04:31:29'),
(424, 197, 'Cafe Latte', 130.00, 1, 'DONE', '2025-09-05 04:39:13'),
(425, 197, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-09-05 04:39:13'),
(426, 197, 'French Vanilla', 160.00, 2, 'DONE', '2025-09-05 04:39:13'),
(427, 197, 'Hazelnut', 160.00, 1, 'DONE', '2025-09-05 04:39:13'),
(428, 197, 'Roasted Almond', 160.00, 1, 'DONE', '2025-09-05 04:39:13'),
(429, 198, 'Americano', 120.00, 1, 'PREPARING', '2025-09-05 04:40:27'),
(430, 198, 'Cafe Latte', 130.00, 1, 'PREPARING', '2025-09-05 04:40:27'),
(431, 199, 'Americano', 120.00, 1, 'PREPARING', '2025-09-05 04:41:05'),
(432, 199, 'Cafe Latte', 130.00, 1, 'PREPARING', '2025-09-05 04:41:05'),
(433, 200, 'Americano', 120.00, 1, 'PREPARING', '2025-09-05 04:41:31'),
(434, 200, 'Cafe Latte', 130.00, 1, 'PREPARING', '2025-09-05 04:41:31'),
(435, 201, 'Cafe Latte', 130.00, 1, 'PREPARING', '2025-09-05 04:42:22'),
(436, 201, 'Cafe Ilustrado', 140.00, 2, 'PREPARING', '2025-09-05 04:42:22'),
(437, 202, 'Cafe Latte', 130.00, 1, 'DONE', '2025-09-06 11:38:06'),
(438, 202, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-09-06 11:38:06'),
(439, 202, 'French Vanilla', 160.00, 2, 'DONE', '2025-09-06 11:38:06'),
(440, 202, 'Hazelnut', 160.00, 1, 'DONE', '2025-09-06 11:38:06'),
(441, 202, 'Roasted Almond', 160.00, 1, 'DONE', '2025-09-06 11:38:06'),
(442, 202, 'Caramel Macchiato', 170.00, 1, 'DONE', '2025-09-06 11:38:06'),
(443, 202, 'Salted Caramel', 170.00, 1, 'DONE', '2025-09-06 11:38:06'),
(444, 202, 'White Choco Mocha', 170.00, 1, 'DONE', '2025-09-06 11:38:06'),
(445, 202, 'Mocha', 170.00, 1, 'DONE', '2025-09-06 11:38:06'),
(446, 203, 'French Vanilla', 160.00, 2, 'DONE', '2025-09-06 11:42:18'),
(447, 203, 'Hazelnut', 160.00, 2, 'DONE', '2025-09-06 11:42:18'),
(448, 203, 'Roasted Almond', 160.00, 1, 'DONE', '2025-09-06 11:42:18'),
(449, 203, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-09-06 11:42:18'),
(450, 203, 'Cafe Latte', 130.00, 1, 'DONE', '2025-09-06 11:42:18'),
(451, 204, 'French Vanilla', 160.00, 2, 'PREPARING', '2025-09-30 01:46:38'),
(452, 204, 'Hazelnut', 160.00, 1, 'PREPARING', '2025-09-30 01:46:38'),
(453, 204, 'Roasted Almond', 160.00, 1, 'PREPARING', '2025-09-30 01:46:38'),
(454, 204, 'Cafe Ilustrado', 140.00, 1, 'PREPARING', '2025-09-30 01:46:38'),
(455, 205, 'Americano', 120.00, 1, 'DONE', '2025-09-30 01:47:38'),
(456, 205, 'Cafe Latte', 130.00, 1, 'DONE', '2025-09-30 01:47:38'),
(457, 206, 'Cafe Latte', 130.00, 7, 'PREPARING', '2025-09-30 01:48:00'),
(458, 207, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-09-30 01:48:30'),
(459, 207, 'French Vanilla', 160.00, 1, 'DONE', '2025-09-30 01:48:30'),
(460, 207, 'Hazelnut', 160.00, 1, 'DONE', '2025-09-30 01:48:30'),
(461, 208, 'French Vanilla', 160.00, 1, 'DONE', '2025-09-30 01:48:41'),
(462, 209, 'Sea Salt Caramel', 180.00, 1, 'PREPARING', '2025-09-30 01:48:57'),
(463, 209, 'Java Chip', 180.00, 1, 'PREPARING', '2025-09-30 01:48:57'),
(464, 210, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-09-30 01:49:30'),
(465, 210, 'Grilled Cheese', 120.00, 1, 'DONE', '2025-09-30 01:49:30'),
(466, 211, 'French Vanilla', 160.00, 1, 'DONE', '2025-09-30 02:15:53'),
(467, 212, 'Cafe Latte', 130.00, 1, 'PREPARING', '2025-09-30 02:20:58'),
(468, 212, 'Cafe Ilustrado', 140.00, 1, 'PREPARING', '2025-09-30 02:20:58'),
(469, 212, 'French Vanilla', 160.00, 1, 'PREPARING', '2025-09-30 02:20:58'),
(470, 213, 'French Vanilla', 160.00, 1, 'PREPARING', '2025-09-30 02:21:08'),
(471, 214, 'Americano', 120.00, 1, 'DONE', '2025-09-30 02:21:18'),
(472, 215, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-09-30 02:21:50'),
(473, 215, 'Hazelnut', 160.00, 1, 'DONE', '2025-09-30 02:21:50'),
(474, 216, 'Cafe Latte', 130.00, 1, 'DONE', '2025-09-30 02:22:04'),
(475, 217, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-09-30 02:32:47'),
(476, 217, 'French Vanilla', 160.00, 1, 'DONE', '2025-09-30 02:32:47'),
(477, 217, 'Hazelnut', 160.00, 1, 'DONE', '2025-09-30 02:32:47'),
(478, 218, 'French Vanilla', 160.00, 1, 'PREPARING', '2025-10-01 06:46:50'),
(479, 219, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-10-02 07:51:54'),
(480, 220, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-10-02 10:25:33'),
(481, 220, 'French Vanilla', 160.00, 1, 'DONE', '2025-10-02 10:25:33'),
(482, 221, 'Cafe Latte', 130.00, 1, 'DONE', '2025-10-02 10:48:17'),
(483, 222, 'French Vanilla', 160.00, 1, 'DONE', '2025-10-03 10:13:00'),
(484, 223, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-10-03 12:08:08'),
(485, 224, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-10-23 03:45:09'),
(486, 225, 'French Vanilla', 160.00, 1, 'PREPARING', '2025-10-23 03:46:00'),
(487, 225, 'Soda Lemon / Raspberry', 95.00, 1, 'PREPARING', '2025-10-23 03:46:00'),
(488, 226, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-10-23 03:46:13'),
(489, 227, 'Cafe Latte', 130.00, 1, 'DONE', '2025-10-23 10:48:09'),
(490, 228, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-10-31 03:18:22'),
(491, 229, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-03 07:46:07'),
(492, 230, 'Cafe Ilustrado', 140.00, 1, 'PREPARING', '2025-11-04 07:13:26'),
(493, 231, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-04 07:13:50'),
(494, 231, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-04 07:13:50'),
(495, 231, 'Caramel Macchiato', 170.00, 1, 'DONE', '2025-11-04 07:13:50'),
(496, 231, 'French Vanilla', 160.00, 1, 'DONE', '2025-11-04 07:13:50'),
(497, 232, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-04 07:54:51'),
(498, 232, 'Caramel Macchiato', 170.00, 1, 'DONE', '2025-11-04 07:54:51'),
(499, 233, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-04 11:37:38'),
(500, 234, 'Americano', 120.00, 1, 'DONE', '2025-11-04 12:09:42'),
(501, 235, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-04 12:10:44'),
(502, 236, 'Cafe Latte', 130.00, 3, 'DONE', '2025-11-04 12:41:03'),
(503, 236, 'Caramel Macchiato', 170.00, 1, 'DONE', '2025-11-04 12:41:03'),
(504, 237, 'Cafe Ilustrado', 140.00, 2, 'DONE', '2025-11-05 01:23:34'),
(505, 237, 'Bacon', 150.00, 1, 'DONE', '2025-11-05 01:23:34'),
(506, 237, 'Baked Lasagna', 250.00, 1, 'DONE', '2025-11-05 01:23:34'),
(507, 238, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-05 01:32:12'),
(508, 239, 'Caramel Macchiato', 170.00, 1, 'DONE', '2025-11-05 01:45:51'),
(509, 240, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-05 02:10:44'),
(510, 241, 'Americano', 120.00, 1, 'DONE', '2025-11-05 02:11:16'),
(511, 242, 'Americano', 120.00, 1, 'DONE', '2025-11-05 02:20:04'),
(512, 243, 'Americano', 120.00, 1, 'DONE', '2025-11-05 02:20:30'),
(513, 244, 'Americano', 120.00, 1, 'DONE', '2025-11-05 02:20:49'),
(514, 245, 'Americano', 120.00, 1, 'DONE', '2025-11-05 02:21:15'),
(515, 246, 'Americano', 120.00, 1, 'DONE', '2025-11-05 02:21:36'),
(516, 247, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-05 02:21:58'),
(517, 248, 'Americano', 120.00, 1, 'DONE', '2025-11-05 02:28:22'),
(518, 249, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-05 03:36:32'),
(519, 250, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-05 03:36:50'),
(520, 251, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-05 03:37:14'),
(521, 252, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-05 03:37:59'),
(522, 253, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-05 03:42:43'),
(523, 254, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-05 03:43:58'),
(524, 255, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-05 04:08:01'),
(525, 256, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-05 04:11:51'),
(526, 257, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-05 04:12:26'),
(527, 258, 'Americano', 120.00, 1, 'DONE', '2025-11-05 04:12:40'),
(528, 259, 'Americano', 120.00, 1, 'DONE', '2025-11-05 04:12:58'),
(529, 260, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-05 04:13:10'),
(530, 261, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-05 04:13:55'),
(531, 262, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-05 04:14:19'),
(532, 263, 'Creamy Chicken Alfredo', 200.00, 1, 'DONE', '2025-11-05 04:19:38'),
(533, 264, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-05 04:19:56'),
(534, 265, 'Cafe Ilustrado', 140.00, 1, 'PREPARING', '2025-11-05 04:20:20'),
(535, 266, 'Cafe Ilustrado', 140.00, 1, 'PREPARING', '2025-11-05 04:20:31'),
(536, 267, 'Cafe Ilustrado', 140.00, 1, 'PREPARING', '2025-11-05 04:20:53'),
(537, 268, 'Cafe Ilustrado', 140.00, 1, 'PREPARING', '2025-11-05 04:25:27'),
(538, 269, 'Americano', 120.00, 1, 'PREPARING', '2025-11-05 04:26:09'),
(539, 270, 'Americano', 120.00, 1, 'PREPARING', '2025-11-05 04:28:01'),
(540, 271, 'Americano', 120.00, 1, 'PREPARING', '2025-11-05 04:30:49'),
(541, 272, 'Americano', 120.00, 1, 'PREPARING', '2025-11-05 04:31:12'),
(542, 273, 'Americano', 120.00, 1, 'PREPARING', '2025-11-05 04:35:49'),
(543, 274, 'Americano', 120.00, 1, 'PREPARING', '2025-11-05 04:37:18'),
(544, 275, 'Cafe Ilustrado', 140.00, 1, 'PREPARING', '2025-11-05 04:41:51'),
(545, 276, 'Cafe Latte', 130.00, 1, 'PREPARING', '2025-11-05 04:51:17'),
(546, 277, 'Cafe Latte', 130.00, 1, 'PREPARING', '2025-11-05 05:00:51'),
(547, 277, 'Cinderella Latte', 180.00, 1, 'PREPARING', '2025-11-05 05:00:51'),
(548, 278, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-06 08:43:47'),
(549, 278, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-06 08:43:47'),
(550, 279, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-06 08:46:18'),
(551, 280, 'Caramel Macchiato', 170.00, 2, 'DONE', '2025-11-06 10:09:41'),
(552, 280, 'Cafe Ilustrado', 140.00, 2, 'DONE', '2025-11-06 10:09:41'),
(553, 280, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-06 10:09:41'),
(554, 280, 'French Vanilla', 160.00, 3, 'DONE', '2025-11-06 10:09:41'),
(555, 280, 'Hazelnut', 160.00, 1, 'DONE', '2025-11-06 10:09:41'),
(556, 280, 'Mocha', 170.00, 1, 'DONE', '2025-11-06 10:09:41'),
(557, 280, 'Roasted Almond', 160.00, 1, 'DONE', '2025-11-06 10:09:41'),
(558, 280, 'Coffee Jelly', 180.00, 1, 'DONE', '2025-11-06 10:09:41'),
(559, 280, 'Java Chip', 180.00, 1, 'DONE', '2025-11-06 10:09:41'),
(560, 280, 'Sea Salt Caramel', 180.00, 1, 'DONE', '2025-11-06 10:09:41'),
(561, 280, 'Biscoff Cream', 200.00, 1, 'DONE', '2025-11-06 10:09:41'),
(562, 280, 'Blueberries and Cream', 170.00, 1, 'DONE', '2025-11-06 10:09:41'),
(563, 280, 'Chocnut', 160.00, 1, 'DONE', '2025-11-06 10:09:41'),
(564, 280, 'Cookies and Cream', 160.00, 2, 'DONE', '2025-11-06 10:09:41'),
(565, 280, 'Pure Matcha', 160.00, 1, 'DONE', '2025-11-06 10:09:41'),
(566, 280, 'Strawberries and Cream', 170.00, 1, 'DONE', '2025-11-06 10:09:41'),
(567, 280, 'Chamomile', 140.00, 1, 'DONE', '2025-11-06 10:09:41'),
(568, 280, 'Earl Grey', 140.00, 1, 'DONE', '2025-11-06 10:09:41'),
(569, 280, 'English Breakfast', 140.00, 1, 'DONE', '2025-11-06 10:09:41'),
(570, 280, 'Pure Peppermint', 140.00, 1, 'DONE', '2025-11-06 10:09:41'),
(571, 280, 'Blue Lemonade', 150.00, 1, 'DONE', '2025-11-06 10:09:41'),
(572, 280, 'Blueberry Ade', 120.00, 1, 'DONE', '2025-11-06 10:09:41'),
(573, 280, 'Lychee Ade', 120.00, 1, 'DONE', '2025-11-06 10:09:41'),
(574, 280, 'Honey Lemon Soda Tea', 130.00, 1, 'DONE', '2025-11-06 10:09:41'),
(575, 280, 'Mango-Biscus Bliss', 160.00, 1, 'DONE', '2025-11-06 10:09:41'),
(576, 280, 'Soda Lemon / Raspberry', 95.00, 1, 'DONE', '2025-11-06 10:09:41'),
(577, 280, 'Raspberry Ade', 120.00, 1, 'DONE', '2025-11-06 10:09:41'),
(578, 280, 'Peach Mango Ade', 120.00, 1, 'DONE', '2025-11-06 10:09:41'),
(579, 281, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-06 12:59:12'),
(580, 282, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-06 12:59:33'),
(581, 283, 'Americano', 120.00, 1, 'DONE', '2025-11-06 13:06:07'),
(582, 284, 'Americano', 120.00, 1, 'DONE', '2025-11-06 13:07:05'),
(583, 285, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-06 13:07:31'),
(584, 286, 'French Vanilla', 160.00, 1, 'DONE', '2025-11-06 13:34:48'),
(585, 286, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-06 13:34:48'),
(586, 287, 'Cheesy Beef Quesadilla', 180.00, 1, 'DONE', '2025-11-06 13:39:42'),
(587, 288, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-06 14:50:07'),
(588, 289, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-06 15:15:58'),
(589, 289, 'French Vanilla', 160.00, 2, 'DONE', '2025-11-06 15:15:58'),
(590, 289, 'Caramel Macchiato', 170.00, 1, 'DONE', '2025-11-06 15:15:58'),
(591, 289, 'Hazelnut', 160.00, 1, 'DONE', '2025-11-06 15:15:58'),
(592, 289, 'Mocha', 170.00, 1, 'DONE', '2025-11-06 15:15:58'),
(593, 289, 'Roasted Almond', 160.00, 1, 'DONE', '2025-11-06 15:15:58'),
(594, 289, 'Salted Caramel', 170.00, 1, 'DONE', '2025-11-06 15:15:58'),
(595, 289, 'Café Con Miel', 160.00, 1, 'DONE', '2025-11-06 15:15:58'),
(596, 289, 'Calamansi Expresso', 140.00, 1, 'DONE', '2025-11-06 15:15:58'),
(597, 289, 'Cinderella Latte', 180.00, 1, 'DONE', '2025-11-06 15:15:58'),
(598, 289, 'Einspanner', 150.00, 1, 'DONE', '2025-11-06 15:15:58'),
(599, 290, 'French Vanilla', 160.00, 1, 'DONE', '2025-11-06 16:13:08'),
(600, 290, 'Caramel Macchiato', 170.00, 1, 'DONE', '2025-11-06 16:13:08'),
(601, 291, 'Caramel Macchiato', 170.00, 1, 'DONE', '2025-11-06 16:14:09'),
(602, 292, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-06 16:14:36'),
(603, 293, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-06 16:15:50'),
(604, 294, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-06 16:16:28'),
(605, 295, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-06 16:17:18'),
(606, 296, 'Caramel Macchiato', 170.00, 2, 'DONE', '2025-11-07 00:23:51'),
(607, 297, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-07 00:30:00'),
(608, 298, 'French Vanilla', 160.00, 1, 'DONE', '2025-11-07 00:30:15'),
(609, 299, 'Café Con Miel', 160.00, 1, 'DONE', '2025-11-07 00:48:17'),
(610, 300, 'Baked Lasagna', 250.00, 1, 'DONE', '2025-11-07 00:48:36'),
(611, 301, 'Bacon', 150.00, 1, 'DONE', '2025-11-07 00:48:54'),
(612, 302, 'Chamomile', 140.00, 1, 'DONE', '2025-11-07 00:49:12'),
(613, 303, 'Chocnut', 160.00, 1, 'DONE', '2025-11-07 00:49:38'),
(614, 303, 'Cookies and Cream', 160.00, 1, 'DONE', '2025-11-07 00:49:38'),
(615, 304, 'Americano', 120.00, 1, 'DONE', '2025-11-07 00:51:08'),
(616, 304, 'Creamy Chicken Alfredo', 200.00, 1, 'DONE', '2025-11-07 00:51:08'),
(617, 305, 'Fish Fillet', 160.00, 1, 'DONE', '2025-11-07 00:51:19'),
(618, 306, 'Chocnut', 160.00, 1, 'DONE', '2025-11-07 00:51:33'),
(619, 307, 'Raspberry Ade', 120.00, 1, 'DONE', '2025-11-07 00:51:50'),
(620, 308, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-07 02:37:32'),
(621, 309, 'French Vanilla', 160.00, 1, 'DONE', '2025-11-07 07:42:49'),
(622, 310, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-08 03:11:45'),
(623, 310, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-08 03:11:45'),
(624, 310, 'Caramel Macchiato', 170.00, 1, 'DONE', '2025-11-08 03:11:45'),
(625, 311, 'Cafe Ilustrado', 140.00, 1, 'DONE', '2025-11-08 03:11:55'),
(626, 312, 'Caramel Macchiato', 170.00, 1, 'DONE', '2025-11-08 09:12:08'),
(627, 312, 'Roasted Almond', 160.00, 1, 'DONE', '2025-11-08 09:12:08'),
(628, 313, 'Cafe Latte', 130.00, 1, 'DONE', '2025-11-08 09:24:13');

-- --------------------------------------------------------

--
-- Table structure for table `otp_verification`
--

CREATE TABLE `otp_verification` (
  `email` varchar(255) NOT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `meta` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `best_seller` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `meta`, `price`, `image`, `best_seller`) VALUES
(5, 1, 'Americano', 'Hot | Iced', 120.00, NULL, 0),
(6, 1, 'Cafe Latte', 'Hot | Iced', 130.00, NULL, 0),
(7, 1, 'Cafe Ilustrado', 'Hot | Iced', 140.00, NULL, 1),
(8, 1, 'French Vanilla', 'Hot | Iced', 160.00, NULL, 0),
(9, 1, 'Hazelnut', 'Hot | Iced', 160.00, NULL, 0),
(10, 1, 'Roasted Almond', 'Hot | Iced', 160.00, NULL, 1),
(11, 1, 'Mocha', 'Hot | Iced', 170.00, NULL, 0),
(12, 1, 'White Choco Mocha', 'Hot | Iced', 170.00, NULL, 0),
(13, 1, 'Salted Caramel', 'Hot | Iced', 170.00, NULL, 0),
(14, 1, 'Caramel Macchiato', 'Hot | Iced', 170.00, NULL, 1),
(15, 2, 'Calamansi Expresso', 'Hot | Iced', 140.00, NULL, 0),
(16, 2, 'Einspanner', 'Hot | Iced', 150.00, NULL, 0),
(17, 2, 'Matcha-presso Fusion', 'Hot | Iced', 160.00, NULL, 0),
(18, 2, 'Café Con Miel', 'Hot | Iced', 160.00, NULL, 0),
(19, 2, 'Iced Shaken Oatmilk Latte', 'Iced', 170.00, NULL, 1),
(20, 2, 'Cinderella Latte', 'Hot | Iced', 180.00, NULL, 0),
(21, 3, 'Coffee Jelly', NULL, 180.00, NULL, 1),
(22, 3, 'Sea Salt Caramel', NULL, 180.00, NULL, 0),
(23, 3, 'Java Chip', NULL, 180.00, NULL, 1),
(24, 4, 'Cookies and Cream', NULL, 160.00, NULL, 1),
(25, 4, 'Pure Matcha', NULL, 160.00, NULL, 0),
(26, 4, 'Chocnut', NULL, 160.00, NULL, 0),
(27, 4, 'Strawberries and Cream', NULL, 170.00, NULL, 1),
(28, 4, 'Blueberries and Cream', NULL, 170.00, NULL, 0),
(29, 4, 'Biscoff Cream', NULL, 200.00, NULL, 0),
(30, 5, 'Earl Grey', NULL, 140.00, NULL, 0),
(31, 5, 'English Breakfast', NULL, 140.00, NULL, 0),
(32, 5, 'Chamomile', NULL, 140.00, NULL, 0),
(33, 5, 'Pure Peppermint', NULL, 140.00, NULL, 0),
(34, 6, 'Blueberry Ade', NULL, 120.00, NULL, 0),
(35, 6, 'Lychee Ade', NULL, 120.00, NULL, 0),
(36, 6, 'Peach Mango Ade', NULL, 120.00, NULL, 0),
(37, 6, 'Raspberry Ade', NULL, 120.00, NULL, 0),
(38, 6, 'Honey Lemon Soda Tea', NULL, 130.00, NULL, 0),
(39, 6, 'Blue Lemonade', NULL, 150.00, NULL, 0),
(40, 6, 'Mango-Biscus Bliss', NULL, 160.00, NULL, 0),
(41, 6, 'Soda Lemon / Raspberry', NULL, 95.00, NULL, 0),
(42, 7, 'Matcha Latte', 'Hot | Iced', 0.00, NULL, 0),
(43, 7, 'Strawberry Milk', NULL, 145.00, NULL, 0),
(44, 7, 'Blueberry Milk', NULL, 145.00, NULL, 0),
(45, 7, 'Matcha-Berry Fusion', NULL, 160.00, NULL, 0),
(46, 7, 'Sikwate', 'Hot | Iced', 165.00, NULL, 0),
(47, 8, 'Espresso Shot', NULL, 40.00, NULL, 0),
(48, 8, 'Syrup', NULL, 30.00, NULL, 0),
(49, 8, 'Sauce', NULL, 30.00, NULL, 0),
(50, 8, 'Whipped Cream', NULL, 30.00, NULL, 0),
(51, 8, 'Oatmilk', NULL, 40.00, NULL, 0),
(52, 8, 'Soya', NULL, 25.00, NULL, 0),
(53, 8, 'Sinkers', NULL, 25.00, NULL, 0),
(54, 8, 'Mineral Water', NULL, 30.00, NULL, 0),
(55, 9, 'Spam', NULL, 130.00, NULL, 0),
(56, 9, 'Corned Beef', NULL, 130.00, NULL, 0),
(57, 9, 'Hungarian', NULL, 150.00, NULL, 0),
(58, 9, 'Tocino', NULL, 150.00, NULL, 0),
(59, 9, 'Tapa', NULL, 150.00, NULL, 1),
(60, 9, 'Bacon', NULL, 150.00, NULL, 0),
(61, 9, 'Garlic Chicken Parmesan', NULL, 170.00, NULL, 1),
(62, 9, 'Honey Chicken Sriracha', NULL, 170.00, NULL, 1),
(63, 9, 'Fish Fillet', NULL, 160.00, NULL, 0),
(64, 9, 'Lechon Kawali', NULL, 190.00, NULL, 1),
(65, 10, 'Fries Solo', NULL, 80.00, NULL, 0),
(66, 10, 'Fries Duo', NULL, 140.00, NULL, 1),
(67, 10, 'Chick and Chips', NULL, 210.00, NULL, 1),
(68, 10, 'Chessy Potato Croquettes', NULL, 220.00, NULL, 1),
(69, 11, 'Pesto Basilico', NULL, 195.00, NULL, 0),
(70, 11, 'Creamy Chicken Alfredo', NULL, 200.00, NULL, 1),
(71, 11, 'Umami Truffle Delight', NULL, 220.00, NULL, 0),
(72, 11, 'Baked Lasagna', NULL, 250.00, NULL, 0),
(73, 12, 'Grilled Cheese', NULL, 120.00, NULL, 1),
(74, 12, 'Double Cheese Ham', NULL, 130.00, NULL, 0),
(75, 12, 'Cheesy Quesadilla', NULL, 135.00, NULL, 0),
(76, 12, 'Cheesy Beef Quesadilla', NULL, 180.00, '0', 1),
(81, 0, 'Biscoff', '', 130.00, 'uploads/1759112563_Bini Maloi Wallpaper Part 2#bini #binimaloi #maloi….jpg', 1),
(82, 0, 'Biscoff', '', 130.00, 'uploads/1759112700_Bini Maloi Wallpaper Part 2#bini #binimaloi #maloi….jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','barista') NOT NULL DEFAULT 'barista',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remember_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `contact`, `address`, `username`, `password`, `role`, `created_at`, `remember_token`) VALUES
(30, 'Chan Ian Kyron', 'ianzkyronz@gmail.com', '09477177432', '7th Avemue', 'admin', '$2y$10$dppmTxKg.Asp2LnIq5mP0O/vG4ff7qUkbKyJ8bJJo3wKUHg0UYKvO', 'admin', '2025-09-24 15:41:49', NULL),
(32, '', 'cambriblessmae5@gmail.com', NULL, NULL, 'barista', '$2y$10$hDlDbtiwfs5.GFQDXNZznu3Fp.hKwz.oL3XO1HJukfFsnvnujpq5q', 'barista', '2025-09-29 01:11:32', NULL),
(34, '', 'degraciaallysa.bsit@gmail.com', NULL, NULL, 'Allysa', '$2y$10$Qp0dw7jQ3ExBPsMmAq/UG.7Zlb3P7G0BeR5DPBQp2e4H7HX8g5Fx.', 'barista', '2025-09-29 07:33:48', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `dtr_logs`
--
ALTER TABLE `dtr_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `otp_verification`
--
ALTER TABLE `otp_verification`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `dtr_logs`
--
ALTER TABLE `dtr_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=314;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=629;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dtr_logs`
--
ALTER TABLE `dtr_logs`
  ADD CONSTRAINT `dtr_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
