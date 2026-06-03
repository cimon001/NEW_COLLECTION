-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2026 at 01:02 PM
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
-- Database: `new_collection_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `size` varchar(10) DEFAULT 'M',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'Babu Bhaiya', 'bhaiyababu687q@gmail.com', 'Return Request', 'bbbhjyujujtyjtyjyuj', '2026-03-30 16:25:27');

-- --------------------------------------------------------

--
-- Table structure for table `custom_designs`
--

CREATE TABLE `custom_designs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `image_front` varchar(500) DEFAULT NULL,
  `image_back` varchar(500) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `total_earnings` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `custom_designs`
--

INSERT INTO `custom_designs` (`id`, `user_id`, `title`, `description`, `image_front`, `image_back`, `price`, `status`, `total_earnings`, `created_at`) VALUES
(1, 6, 'flower design', 'black rose', 'front_6_1774957988.png', 'back_6_1774957988.png', 500.00, 'rejected', 25.00, '2026-03-31 11:53:08');

-- --------------------------------------------------------

--
-- Table structure for table `design_orders`
--

CREATE TABLE `design_orders` (
  `id` int(11) NOT NULL,
  `design_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `profit_amount` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `design_orders`
--

INSERT INTO `design_orders` (`id`, `design_id`, `buyer_id`, `profit_amount`, `created_at`) VALUES
(1, 1, 2, 0.00, '2026-04-02 13:07:16'),
(2, 1, 2, 25.00, '2026-04-02 13:07:59');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(500) NOT NULL,
  `type` varchar(50) DEFAULT 'general',
  `is_read` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 2, '???? Welcome to NEW_COLLECTION, Babu Bhaiya! Start shopping now!', 'welcome', 1, '2026-03-22 04:39:52'),
(2, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-22 04:39:58'),
(3, 2, '????️ Your order #NC00001 has been placed successfully!', 'order', 1, '2026-03-22 05:25:27'),
(4, 2, '???? Your order #NC00001 is now being processed!', 'order', 1, '2026-03-22 05:32:42'),
(5, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-22 12:44:59'),
(6, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-23 12:06:09'),
(11, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-23 15:01:23'),
(12, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-23 15:09:21'),
(13, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-23 15:09:36'),
(15, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-23 15:11:27'),
(16, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-23 15:28:22'),
(18, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-23 15:28:51'),
(19, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-24 03:30:29'),
(20, 2, '????️ Your order #NC00002 has been placed successfully!', 'order', 1, '2026-03-24 04:49:39'),
(21, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-24 11:20:44'),
(22, 2, '????️ Your order #NC00003 has been placed successfully!', 'order', 1, '2026-03-24 11:24:31'),
(23, 2, '????️ Your order #NC00004 has been placed successfully!', 'order', 1, '2026-03-24 11:25:58'),
(24, 2, '????️ Your order #NC00005 has been placed successfully!', 'order', 1, '2026-03-24 11:26:36'),
(25, 2, '????️ Your order #NC00006 has been placed successfully!', 'order', 1, '2026-03-24 11:28:48'),
(26, 2, '???? Your order #NC00006 is now being processed!', 'order', 1, '2026-03-24 11:30:14'),
(27, 2, '❌ Your order #NC00004 has been cancelled. Contact us for help.', 'cancelled', 1, '2026-03-24 11:30:20'),
(28, 2, '???? Your order #NC00003 is now being processed!', 'order', 1, '2026-03-24 11:30:24'),
(29, 2, '???? Your order #NC00002 is now being processed!', 'order', 1, '2026-03-24 11:30:27'),
(30, 2, '????️ Your order #NC00007 has been placed successfully!', 'order', 1, '2026-03-24 11:31:28'),
(31, 2, '✅ Your order #NC00007 has been delivered! Enjoy your purchase!', 'delivered', 1, '2026-03-24 11:31:45'),
(32, 2, '????️ Your order #NC00008 has been placed successfully!', 'order', 1, '2026-03-24 11:32:43'),
(33, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-24 13:51:02'),
(34, 2, '????️ Your order #NC00009 has been placed successfully!', 'order', 1, '2026-03-24 14:27:45'),
(35, 2, '????️ Your order #NC00010 has been placed successfully!', 'order', 1, '2026-03-24 14:28:15'),
(36, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-24 14:35:21'),
(37, 2, '✅ Your order #NC00008 has been delivered! Enjoy your purchase!', 'delivered', 1, '2026-03-24 14:35:49'),
(38, 2, '???? Your order #NC00010 is now being processed!', 'order', 1, '2026-03-24 14:35:52'),
(39, 2, '???? Your order #NC00009 is now being processed!', 'order', 1, '2026-03-24 14:35:54'),
(40, 2, '???? Your order #NC00003 is now being processed!', 'order', 1, '2026-03-24 14:49:26'),
(41, 2, '???? Your order #NC00002 is now being processed!', 'order', 1, '2026-03-24 14:49:34'),
(42, 2, '???? Your order #NC00010 is now being processed!', 'order', 1, '2026-03-24 14:50:31'),
(43, 2, '????️ Your order #NC00011 has been placed successfully!', 'order', 1, '2026-03-24 14:51:23'),
(44, 2, '✅ Your order #NC00011 has been delivered! Enjoy your purchase!', 'delivered', 1, '2026-03-24 14:58:39'),
(45, 2, '???? Your order #NC00011 is now being processed!', 'order', 1, '2026-03-24 14:58:41'),
(46, 2, '???? Your order #NC00010 is now being processed!', 'order', 1, '2026-03-24 15:00:14'),
(47, 2, '????️ Your order #NC00012 has been placed successfully!', 'order', 1, '2026-03-24 15:07:17'),
(48, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-29 03:29:46'),
(49, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-29 08:55:17'),
(50, 2, '????️ Your order #NC00013 has been placed successfully!', 'order', 1, '2026-03-29 08:55:29'),
(51, 2, '????️ Your order #NC00014 has been placed successfully!', 'order', 1, '2026-03-29 08:56:22'),
(52, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-30 03:51:09'),
(53, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-30 04:05:16'),
(54, 2, '✅ Your order #NC00014 has been delivered! Enjoy your purchase!', 'delivered', 1, '2026-03-30 04:06:21'),
(55, 2, '???? Your order #NC00013 is now being processed!', 'order', 1, '2026-03-30 04:06:26'),
(56, 2, '???? Your order #NC00012 is now being processed!', 'order', 1, '2026-03-30 04:06:29'),
(57, 2, '✅ Your order #NC00011 has been delivered! Enjoy your purchase!', 'delivered', 1, '2026-03-30 04:06:32'),
(58, 2, '✅ Your order #NC00010 has been delivered! Enjoy your purchase!', 'delivered', 1, '2026-03-30 04:06:36'),
(59, 2, '???? Your order #NC00003 is now being processed!', 'order', 1, '2026-03-30 04:06:41'),
(60, 2, '✅ Your order #NC00002 has been delivered! Enjoy your purchase!', 'delivered', 1, '2026-03-30 04:06:45'),
(61, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-30 14:51:18'),
(62, 2, '????️ buy  3 product only just 3999 , order NOW', 'offer', 1, '2026-03-30 14:52:02'),
(65, 2, '✅ Your order #NC00014 has been delivered! Enjoy your purchase!', 'delivered', 1, '2026-03-30 15:52:19'),
(66, 2, '✅ Your order #NC00014 has been delivered! Enjoy your purchase!', 'delivered', 1, '2026-03-30 15:52:21'),
(67, 2, '✅ Your order #NC00014 has been delivered! Enjoy your purchase!', 'delivered', 1, '2026-03-30 15:52:21'),
(68, 2, '✅ Your order #NC00014 has been delivered! Enjoy your purchase!', 'delivered', 1, '2026-03-30 15:52:22'),
(69, 2, '✅ Your order #NC00014 has been delivered! Enjoy your purchase!', 'delivered', 1, '2026-03-30 15:52:22'),
(70, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-31 03:00:40'),
(71, 2, '????️ Your order #NC00015 has been placed successfully!', 'order', 1, '2026-03-31 06:08:36'),
(72, 2, '???? Your order #NC00015 is now being processed!', 'order', 1, '2026-03-31 06:09:19'),
(73, 2, '✅ Your order #NC00015 has been delivered! Enjoy your purchase!', 'delivered', 1, '2026-03-31 06:09:31'),
(74, 2, '???? Your order #NC00015 is now being processed!', 'order', 1, '2026-03-31 07:44:33'),
(75, 2, '???? Your order #NC00015 has been shipped! On the way!', 'order', 1, '2026-03-31 07:44:36'),
(76, 2, '✅ Your order #NC00015 has been delivered! Enjoy your purchase!', 'delivered', 1, '2026-03-31 07:44:42'),
(77, 2, '???? Your order #NC00004 has been shipped! On the way!', 'order', 1, '2026-03-31 07:44:51'),
(78, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-31 10:06:37'),
(81, 6, '???? Welcome back, ishav!', 'welcome', 0, '2026-03-31 10:59:36'),
(82, 6, '???? Welcome back, ishav!', 'welcome', 0, '2026-03-31 11:27:58'),
(83, 6, '???? Welcome back, ishav!', 'welcome', 0, '2026-03-31 11:30:23'),
(84, 6, '???? Welcome back, ishav!', 'welcome', 0, '2026-03-31 11:50:50'),
(85, 6, '???? Your design \"flower design\" has been submitted! We will review it within 24-48 hours.', 'general', 0, '2026-03-31 11:53:08'),
(86, 6, '???? Welcome back, ishav!', 'welcome', 0, '2026-03-31 11:59:35'),
(87, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-31 12:01:10'),
(88, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-31 12:23:42'),
(89, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-03-31 12:44:46'),
(90, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-04-02 13:06:20'),
(91, 6, '✅ Your design \"flower design\" has been approved! Price set: ₹0', 'general', 0, '2026-04-02 13:06:50'),
(92, 2, '???? Your custom order for \"flower design\" has been placed! We will contact you soon.', 'order', 1, '2026-04-02 13:07:16'),
(93, 6, '???? Someone ordered your design \"flower design\"! You earned ₹0!', 'offer', 0, '2026-04-02 13:07:16'),
(94, 6, '✅ Your design \"flower design\" has been approved! Price set: ₹500', 'general', 0, '2026-04-02 13:07:46'),
(95, 2, '???? Your custom order for \"flower design\" has been placed! We will contact you soon.', 'order', 1, '2026-04-02 13:07:59'),
(96, 6, '???? Someone ordered your design \"flower design\"! You earned ₹25!', 'offer', 0, '2026-04-02 13:07:59'),
(97, 6, '✅ Your design \"flower design\" has been approved! Price set: ₹500', 'general', 0, '2026-04-02 13:08:13'),
(98, 6, '✅ Your design \"flower design\" has been approved! Price set: ₹500', 'general', 0, '2026-04-02 13:38:56'),
(99, 2, '????️ Your order #NC00017 has been placed successfully!', 'order', 1, '2026-04-02 13:42:41'),
(100, 2, '????️ Your order #NC00018 has been placed successfully!', 'order', 1, '2026-04-02 13:43:45'),
(101, 2, '???? Your order #NC00005 has been shipped! On the way!', 'order', 1, '2026-04-02 14:38:29'),
(102, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-04-03 14:46:30'),
(103, 2, '???? Your order #NC00018 has been shipped! On the way!', 'order', 1, '2026-04-03 15:37:27'),
(104, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-04-04 11:24:04'),
(105, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-04-08 12:33:17'),
(106, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-04-19 11:14:24'),
(107, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-04-19 12:51:34'),
(108, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-04-19 12:55:25'),
(109, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-04-20 02:41:52'),
(110, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-04-20 12:24:24'),
(111, 2, '❌ Your order #NC00017 has been cancelled. Contact us for help.', 'cancelled', 1, '2026-04-20 12:25:40'),
(113, 2, '???? Your order #NC00018 has been shipped! On the way!', 'order', 1, '2026-04-20 13:11:20'),
(114, 2, '???? Your order #NC00018 has been shipped! On the way!', 'order', 1, '2026-04-20 13:11:47'),
(115, 2, '???? Your order #NC00018 is now being processed!', 'order', 1, '2026-04-20 13:11:55'),
(116, 2, '???? Your order #NC00018 is now being processed!', 'order', 1, '2026-04-20 13:19:34'),
(117, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-04-22 02:31:11'),
(118, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-04-22 02:47:01'),
(119, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-04-22 03:07:39'),
(120, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-04-22 03:09:32'),
(121, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-04-22 12:25:52'),
(122, 2, '????️ Your order #NC00019 has been placed successfully!', 'order', 1, '2026-04-22 14:09:08'),
(123, 2, '❌ Your order #NC00019 has been cancelled successfully.', 'cancelled', 1, '2026-04-22 14:12:48'),
(124, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-04-22 15:11:50'),
(125, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-04-25 12:30:16'),
(126, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-04-30 05:38:34'),
(127, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-05-10 12:48:11'),
(128, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-05-15 05:03:15'),
(129, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-05-16 14:06:07'),
(130, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-05-16 14:08:46'),
(131, 2, '????️ Your order #NC00020 has been placed successfully!', 'order', 1, '2026-05-16 15:08:02'),
(132, 6, '❌ Your design \"flower design\" was not approved. Contact us for more info.', 'general', 0, '2026-05-16 15:39:57'),
(133, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-05-17 13:28:43'),
(134, 2, '????️ Your order #NC00021 has been placed successfully!', 'order', 1, '2026-05-17 15:28:37'),
(135, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-05-25 10:47:46'),
(136, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-05-25 10:55:23'),
(137, 2, '???? Your order #NC00013 has been shipped! On the way!', 'order', 1, '2026-05-25 10:59:41'),
(138, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-05-25 11:16:17'),
(139, 2, '????️ Your order #NC00022 has been placed successfully!', 'order', 1, '2026-05-25 11:30:26'),
(140, 2, '????️ Your order #NC00023 has been placed successfully!', 'order', 1, '2026-05-25 11:31:22'),
(141, 2, '????️ Your order #NC00024 has been placed successfully!', 'order', 1, '2026-05-25 11:32:08'),
(142, 2, '???? Your order #NC00019 is now being processed!', 'order', 1, '2026-05-25 11:35:29'),
(143, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-05-25 11:52:16'),
(144, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-05-25 12:06:03'),
(145, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-05-25 12:12:51'),
(146, 2, '????️ Your order #NC00025 has been placed successfully!', 'order', 1, '2026-05-25 12:15:42'),
(147, 2, '???? Your order #NC00025 is now being processed!', 'order', 1, '2026-05-25 12:17:13'),
(148, 2, '????️ Your order #NC00026 has been placed successfully!', 'order', 1, '2026-05-25 12:18:21'),
(149, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-05-25 12:21:32'),
(150, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 1, '2026-05-25 12:24:41'),
(151, 2, '????️ Your order #NC00027 has been placed successfully!', 'order', 0, '2026-05-25 12:27:08'),
(152, 2, '???? Your order #NC00027 is now being processed!', 'order', 0, '2026-05-25 12:28:35'),
(153, 2, '✅ Your order #NC00027 has been delivered! Enjoy your purchase!', 'delivered', 0, '2026-05-25 12:29:22'),
(154, 2, '???? Welcome back, Babu Bhaiya!', 'welcome', 0, '2026-06-03 09:17:17'),
(155, 2, '????️ Your order #NC00028 has been placed successfully!', 'order', 0, '2026-06-03 09:20:30');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT 'Cash on Delivery',
  `shipping_address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `payment_method`, `shipping_address`, `phone`, `created_at`) VALUES
(1, 2, 2998.00, 'processing', 'Cash on Delivery', 'Phillaur, Jalandhar, Punjab - 144410', '8837894309', '2026-03-22 05:25:27'),
(2, 2, 8391.00, 'delivered', 'Cash on Delivery', 'PHILAUR/NEAR PEPSI FACTORY, PHILLAUR, Punjab - 33333', '8837894309', '2026-03-24 04:49:39'),
(3, 2, 998.00, 'processing', 'Cash on Delivery', 'Phillaur, Jalandhar, Punjab - 144410', '8837894309', '2026-03-24 11:24:31'),
(4, 2, 998.00, 'shipped', 'Cash on Delivery', 'PHILAUR/NEAR PEPSI FACTORY, Jalandhar, Punjab - 144410', '8837894309', '2026-03-24 11:25:58'),
(5, 2, 8091.00, 'shipped', 'Cash on Delivery', 'PHILAUR/NEAR PEPSI FACTORY, Jalandhar, Punjab - 144410', '8837894309', '2026-03-24 11:26:36'),
(6, 2, 9889.00, 'processing', 'Cash on Delivery', 'Phillaur, Jalandhar, Punjab - 144410', '8837894309', '2026-03-24 11:28:48'),
(7, 2, 30566.00, 'delivered', 'Cash on Delivery', 'PHILAUR/NEAR PEPSI FACTORY, PHILLAUR, Punjab - 144410', '8837894309', '2026-03-24 11:31:28'),
(8, 2, 20677.00, 'delivered', 'Cash on Delivery', 'PHILAUR/NEAR PEPSI FACTORY, PHILLAUR, Punjab - 144410', '8837894309', '2026-03-24 11:32:43'),
(9, 2, 998.00, 'processing', 'Cash on Delivery', 'nawanshahr, railway road , karyam, nawanshahr, Punjab - 145410', '8837894309', '2026-03-24 14:27:45'),
(10, 2, 998.00, 'delivered', 'Cash on Delivery', 'nawanshahr, railway road , karyam, nawanshahr, Punjab - 145410', '8837894309', '2026-03-24 14:28:15'),
(11, 2, 8091.00, 'delivered', 'Cash on Delivery', 'PHILAUR/NEAR PEPSI FACTORY, PHILLAUR, Punjab - 144410', '8837894309', '2026-03-24 14:51:23'),
(12, 2, 4995.00, 'processing', 'Cash on Delivery', 'PHILAUR/NEAR PEPSI FACTORY, PHILLAUR, Punjab - 144410', '8837894309', '2026-03-24 15:07:17'),
(13, 2, 998.00, 'shipped', 'Cash on Delivery', 'Phillaur, Jalandhar, Punjab - 144410', '8837894309', '2026-03-29 08:55:29'),
(14, 2, 998.00, 'delivered', 'Cash on Delivery', 'PHILAUR/NEAR PEPSI FACTORY, PHILLAUR, Punjab - 144410', '8837894309', '2026-03-29 08:56:21'),
(15, 2, 84806.00, 'delivered', 'Cash on Delivery', 'Phillaur, Jalandhar, Punjab - 144410', '8837894309', '2026-03-31 06:08:36'),
(17, 2, 998.00, 'cancelled', 'Cash on Delivery', 'Nawanshahr, PHILLAUR, Gujarat - 144514', '8837894309', '2026-04-02 13:42:41'),
(18, 2, 998.00, 'processing', 'Cash on Delivery', 'PHILAUR/NEAR PEPSI FACTORY, Jalandhar, Uttar Pradesh - 144514', '8837894309', '2026-04-02 13:43:45'),
(19, 2, 20172.00, 'processing', 'Cash on Delivery', 'house no/365, Nawanshahr, Punjab - 144514', '8837894309', '2026-04-22 14:09:08'),
(20, 2, 2497.00, 'pending', 'Cash on Delivery', 'Phillaur, Jalandhar, Punjab - 144410', '8837894309', '2026-05-16 15:08:01'),
(21, 2, 108349.00, 'pending', 'Cash on Delivery', 'nawanshar, Nawanshahr, Punjab - 144510', '8837894309', '2026-05-17 15:28:36'),
(22, 2, 8193.00, 'pending', 'Cash on Delivery', 'phillaur, Phillaur, Punjab - 144410', '8837894309', '2026-05-25 11:30:26'),
(23, 2, 998.00, 'pending', 'Cash on Delivery', 'PHILAUR/NEAR PEPSI FACTOry, Phillaur, Punjab - 144410', '8837894309', '2026-05-25 11:31:22'),
(24, 2, 998.00, 'pending', 'Cash on Delivery', 'Phillaur, Phillaur, Punjab - 144410', '8837894309', '2026-05-25 11:32:08'),
(25, 2, 3596.00, 'processing', 'Cash on Delivery', 'PHILAUR/NEAR PEPSI FACTORY, Phillaur, Punjab - 144410', '8837894309', '2026-05-25 12:15:42'),
(26, 2, 2398.00, 'pending', 'Cash on Delivery', 'PHILAUR/NEAR PEPSI FACTORY, Phillaur, Punjab - 144410', '8837894309', '2026-05-25 12:18:21'),
(27, 2, 3996.00, 'delivered', 'Cash on Delivery', 'PHILAUR/NEAR PEPSI FACTORY, Phillaur, Punjab - 144410', '8837894309', '2026-05-25 12:27:08'),
(28, 2, 4495.00, 'pending', 'Cash on Delivery', 'Phillaur, Phillaur, Punjab - 144410', '8837894309', '2026-06-03 09:20:30');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `size` varchar(10) DEFAULT 'M',
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `size`, `price`, `created_at`) VALUES
(1, 1, 1, 2, 'S', 1499.00, '2026-03-22 05:25:27'),
(2, 2, 5, 1, 'M', 999.00, '2026-03-24 04:49:39'),
(3, 2, 25, 6, 'M', 899.00, '2026-03-24 04:49:39'),
(4, 2, 23, 2, 'M', 999.00, '2026-03-24 04:49:39'),
(5, 3, 30, 1, 'S', 899.00, '2026-03-24 11:24:31'),
(6, 4, 28, 1, 'S', 899.00, '2026-03-24 11:25:58'),
(7, 5, 30, 1, 'M', 899.00, '2026-03-24 11:26:36'),
(8, 5, 29, 1, 'M', 899.00, '2026-03-24 11:26:36'),
(9, 5, 28, 1, 'M', 899.00, '2026-03-24 11:26:36'),
(10, 5, 31, 1, 'M', 899.00, '2026-03-24 11:26:36'),
(11, 5, 27, 5, 'S', 899.00, '2026-03-24 11:26:36'),
(12, 6, 31, 11, 'L', 899.00, '2026-03-24 11:28:48'),
(13, 7, 30, 34, 'L', 899.00, '2026-03-24 11:31:28'),
(14, 8, 29, 23, 'S', 899.00, '2026-03-24 11:32:43'),
(15, 9, 30, 1, 'S', 899.00, '2026-03-24 14:27:45'),
(16, 10, 30, 1, 'S', 899.00, '2026-03-24 14:28:15'),
(17, 11, 29, 1, 'S', 899.00, '2026-03-24 14:51:23'),
(18, 11, 18, 8, 'S', 899.00, '2026-03-24 14:51:23'),
(19, 12, 2, 5, 'XL', 999.00, '2026-03-24 15:07:17'),
(20, 13, 30, 1, 'S', 899.00, '2026-03-29 08:55:29'),
(21, 14, 30, 1, 'M', 899.00, '2026-03-29 08:56:22'),
(22, 15, 30, 51, 'M', 899.00, '2026-03-31 06:08:36'),
(23, 15, 29, 1, 'M', 899.00, '2026-03-31 06:08:36'),
(24, 15, 28, 2, 'M', 899.00, '2026-03-31 06:08:36'),
(25, 15, 24, 1, 'M', 1199.00, '2026-03-31 06:08:36'),
(26, 15, 25, 5, 'M', 899.00, '2026-03-31 06:08:36'),
(27, 15, 30, 34, 'S', 899.00, '2026-03-31 06:08:36'),
(30, 17, 30, 1, 'S', 899.00, '2026-04-02 13:42:41'),
(31, 18, 30, 1, 'S', 899.00, '2026-04-02 13:43:45'),
(32, 19, 21, 6, 'M', 799.00, '2026-04-22 14:09:08'),
(33, 19, 20, 20, 'M', 699.00, '2026-04-22 14:09:08'),
(34, 19, 7, 2, 'S', 699.00, '2026-04-22 14:09:08'),
(35, 20, 30, 1, 'M', 899.00, '2026-05-16 15:08:01'),
(36, 20, 29, 1, 'M', 899.00, '2026-05-16 15:08:01'),
(37, 20, 7, 1, 'M', 699.00, '2026-05-16 15:08:02'),
(38, 21, 6, 40, 'M', 699.00, '2026-05-17 15:28:36'),
(39, 21, 7, 41, 'M', 699.00, '2026-05-17 15:28:36'),
(40, 21, 4, 30, 'M', 699.00, '2026-05-17 15:28:36'),
(41, 21, 33, 40, 'M', 769.00, '2026-05-17 15:28:37'),
(42, 22, 31, 3, 'M', 899.00, '2026-05-25 11:30:26'),
(43, 22, 2, 1, 'S', 999.00, '2026-05-25 11:30:26'),
(44, 22, 9, 2, 'S', 1499.00, '2026-05-25 11:30:26'),
(45, 22, 9, 1, 'L', 1499.00, '2026-05-25 11:30:26'),
(46, 23, 31, 1, 'S', 899.00, '2026-05-25 11:31:22'),
(47, 24, 30, 1, 'S', 899.00, '2026-05-25 11:32:08'),
(48, 25, 30, 3, 'M', 899.00, '2026-05-25 12:15:42'),
(49, 25, 29, 1, 'M', 899.00, '2026-05-25 12:15:42'),
(50, 26, 31, 1, 'L', 899.00, '2026-05-25 12:18:21'),
(51, 26, 9, 1, 'S', 1499.00, '2026-05-25 12:18:21'),
(52, 27, 2, 4, 'S', 999.00, '2026-05-25 12:27:08'),
(53, 28, 31, 2, 'S', 899.00, '2026-06-03 09:20:30'),
(54, 28, 27, 1, 'S', 899.00, '2026-06-03 09:20:30'),
(55, 28, 27, 2, 'M', 899.00, '2026-06-03 09:20:30');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `image_back` varchar(500) DEFAULT NULL,
  `image_detail1` varchar(500) DEFAULT NULL,
  `image_detail2` varchar(500) DEFAULT NULL,
  `video` varchar(500) DEFAULT NULL,
  `highlights` text DEFAULT NULL,
  `sizes` varchar(100) DEFAULT 'S,M,L,XL,XXL',
  `stock` int(11) DEFAULT 0,
  `badge` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `old_price`, `category`, `image`, `image_back`, `image_detail1`, `image_detail2`, `video`, `highlights`, `sizes`, `stock`, `badge`, `created_at`) VALUES
(1, 'Hellfire Beast Hoodie', 'A bold statement hoodie designed for those who embrace power and style. Featuring a fiery beast-inspired graphic with premium fabric, this hoodie delivers both comfort and an aggressive streetwear edge. Perfect for everyday wear with a standout look.', 1499.00, 1999.00, 'hoodie', 'images/hoodies/h21.png', '', 'images/hoodies/h21_dtl1.png', '', '', '???? Bold Fire Design – Eye-catching hellfire beast graphic ???? Premium Fabric – Soft, durable & comfortable feel ❄️ All-Season Wear – Perfect for winter & cool evenings ???? Relaxed Fit – Stylish streetwear look with comfort ⚡ Statement Piece – Stand out with dark, powerful vibes', 'S,M,L,XL,XXL', 10, 'NEW IN', '2026-03-22 05:18:02'),
(2, 'LIME GREEN HOODIE', 'A fresh, premium hoodie made with soft cotton fleece and ultra-comfy inner lining. Designed for everyday comfort with a clean, modern look.', 999.00, 1299.00, 'hoodie', 'images/hoodies/hoodie5.png', 'images/hoodies/hoodie5_back.png', 'images/hoodies/hoodie5_detail1.png', 'images/hoodies/hoodie5_detail2.png', 'images/hoodies/hoodie5_video.mp4', '???? Fresh Lime Green – Clean & stylish look, ???? Soft Cotton Fleece – Warm & durable, ❄️ Comfort Lining – Extra soft feel, ???? Perfect Fit – Ribbed cuffs & pocket, ⚡ Minimal Design – Premium finish', 'S,M,L,XL,XXL', 5, 'HOT', '2026-03-22 12:51:38'),
(3, 'Spider Armor Hoodie', 'A bold superhero-inspired hoodie with a sleek spider design and premium fabric. Built for comfort, durability, and a powerful streetwear look.', 1299.00, 1799.00, 'hoodie', 'images/hoodies/hoodie4.png', 'images/hoodies/hoodie4_back.png', 'images/hoodies/hoodie4_detail1.png', 'images/hoodies/hoodie4_detail2.png', 'images/hoodies/hoodie4_video.mp4', '????️ Spider Design – Bold & eye-catching graphic, ???? Premium Fabric – Soft & durable feel, ???? Comfortable Fit – Perfect for daily wear, ⚡ Superhero Style – Strong, modern look, ???? Statement Piece – Stand out effortlessly', 'S,M,L,XL,XXL', 5, '', '2026-03-22 12:56:22'),
(4, 'White Spidy Hoodie', 'A clean and minimal hoodie featuring a sleek spider logo on premium fabric. Designed for everyday comfort with a modern, effortless streetwear style.', 699.00, 999.00, 'hoodie', 'images/uploads/1779031414_ChatGPTImageMay17202608_47_13PM.png', 'images/hoodies/hoodie3_back.png', 'images/hoodies/hoodie3_detail1.png', 'images/hoodies/hoodie3_detail2.png', 'images/hoodies/hoodie3_video.mp4', '????️ Minimal Spider Logo – Clean & stylish look, ???? Premium Fabric – Soft & durable feel, ???? Pure White Design – Fresh & versatile, ???? Comfortable Fit – Perfect for daily wear, ⚡ Modern Streetwear – Simple yet premium vibe', 'S,M,L,XL,XXL', 54, 'NEW IN', '2026-03-22 13:00:54'),
(5, 'NAVY BLUE EMBROADERED', 'A sleek and futuristic hoodie designed with premium fabric and a bold modern pattern. Built for comfort, style, and everyday performance with a clean streetwear edge.', 999.00, 1499.00, 'hoodie', 'images/hoodies/hoodie2.png', 'images/hoodies/hoodie2_back.png', 'images/hoodies/hoodie2_detail1.png', 'images/hoodies/hoodie2_detail2.png', '', '???? Futuristic Design – Bold and modern pattern, ???? Premium Fabric – Soft & durable feel, ???? Comfortable Fit – Ideal for daily wear, ⚡ Sporty Look – Clean & dynamic style, ???? Premium Finish – Stylish and standout', 'S,M,L,XL,XXL', 20, 'SALE', '2026-03-22 13:04:38'),
(6, 'BLACK SPIDY ', 'A bold minimal hoodie featuring a striking red spider logo on deep black fabric. Designed for comfort, durability, and a powerful modern streetwear look.', 699.00, 999.00, 'hoodie', 'images/hoodies/hoodie1.png', 'images/hoodies/hoodie1_back.png', 'images/hoodies/hoodie1_detail1.png', 'images/hoodies/hoodie1_detail2', 'images/hoodies/hoodie1_video.mp4', '????️ Red Spider Logo – Clean yet eye-catching design, ???? Premium Fabric – Soft & long-lasting, ???? Black Minimal Look – Sleek & versatile, ???? Comfortable Fit – Perfect for daily wear, ⚡ Statement Style – Simple but powerful vibe', 'S,M,L,XL,XXL', 10, 'HOT', '2026-03-22 13:07:51'),
(7, 'SKY BLUE SPIDY', 'A cool and minimal hoodie featuring a sleek spider logo on a soft sky blue base. Designed for everyday comfort with a fresh, modern streetwear vibe.', 699.00, 999.00, 'hoodie', 'images/uploads/1779031356_spidyskyblue.png', 'images/hoodies/h6_back.png', 'images/hoodies/h6_dtl1.png', '', '', '????️ Minimal Spider Logo – Clean & subtle design, ???? Sky Blue Color – Fresh & stylish look, ???? Premium Fabric – Soft & durable feel, ???? Comfortable Fit – Perfect for daily wear, ⚡ Modern Style – Simple yet standout', 'S,M,L,XL,XXL', 55, 'NEW IN', '2026-03-22 13:11:46'),
(8, 'Black Embroadered hoodie', 'A bold and futuristic hoodie designed with a sleek modern pattern and premium fabric. Built for comfort, durability, and a strong streetwear presence with a sporty edge.', 999.00, 1499.00, 'hoodie', 'images/hoodies/h7.png', 'images/hoodies/h7_bk.png', 'images/hoodies/h7_dt1.png', '', '', '⚡ Futuristic Design – Bold and dynamic pattern, ???? Premium Fabric – Soft & long-lasting, ???? Black Base – Sleek and versatile, ???? Comfortable Fit – Perfect for daily wear, ???? Premium Finish – Stylish and standout', 'S,M,L,XL,XXL', 15, 'NEW IN', '2026-03-22 13:14:57'),
(9, 'Storm Beast Hoodie', 'A bold statement hoodie featuring a lightning-inspired beast design on a deep black base. Crafted with premium fabric for comfort, durability, and a powerful streetwear presence.', 1499.00, 1999.00, 'hoodie', 'images/hoodies/h8.png', 'images/hoodies/h8_bk.png', 'images/hoodies/h8_dtl1.png', 'images/hoodies/h8_dtl2.png', '', '⚡ Lightning Beast Design – Bold & electrifying graphic, ???? Premium Fabric – Soft & durable feel, ???? Black Base – Sleek and versatile, ???? Comfortable Fit – Perfect for daily wear, ???? Statement Style – Strong and standout look', 'S,M,L,XL,XXL', 12, 'HOT', '2026-03-22 13:19:26'),
(10, 'Dark Aura Hoodie', 'A premium hoodie featuring a subtle beast-inspired design on a deep dark base. Crafted with high-quality fabric for superior comfort, durability, and a refined luxury streetwear loo', 1499.00, 1999.00, 'hoodie', 'images/hoodies/h10.png', '', 'images/hoodies/h10_dtl1.png', '', '', '⚡ Subtle Beast Design – Clean yet powerful graphic, ???? Premium Cotton Blend – Soft & long-lasting, ???? Dark Luxury Look – Sleek and versatile, ???? Comfortable Fit – Perfect for daily wear, ???? Fine Detailing – High-quality stitching & print', 'S,M,L,XL,XXL', 32, 'HOT', '2026-03-22 13:25:35'),
(11, 'Storm Pulse Hoodie', 'A bold hoodie featuring an electrifying neon-inspired beast design on a deep blue base. Crafted with premium fabric for superior comfort, durability, and a striking streetwear look.', 1499.00, 1999.00, 'hoodie', 'images/hoodies/h9.png', '', 'images/hoodies/h9_dtl1.png', '', '', '⚡ Neon Lightning Design – Bold & glowing graphic, ???? Premium Cotton Blend – Soft & durable feel, ???? Deep Blue Base – Stylish and modern look, ???? Comfortable Fit – Perfect for daily wear, ???? Premium Finish – High-quality print & stitching', 'S,M,L,XL,XXL', 5, 'NEW IN', '2026-03-22 13:29:05'),
(12, 'Grey Specter Hoodie', 'A refined hoodie featuring a subtle lightning-inspired beast design on a sleek grey base. Crafted with premium fabric for superior comfort, durability, and a clean luxury streetwear look.', 1499.00, 1999.00, 'hoodie', 'images/hoodies/h11.png', '', 'images/hoodies/h11_dtl1.png', '', '', '⚡ Subtle Lightning Design – Clean yet powerful graphic, ???? Premium Cotton Blend – Soft & durable feel, ???? Grey Minimal Look – Classy and versatile, ???? Comfortable Fit – Perfect for daily wear, ???? Premium Finish – High-quality print & stitching', 'S,M,L,XL,XXL', 15, '', '2026-03-22 13:32:40'),
(13, 'Dark Spirit Hoodie)', 'A powerful hoodie featuring a glowing spectral beast design with intense eyes on a deep black base. Crafted for comfort and durability, delivering a bold and mysterious streetwear statement.', 1499.00, 1999.00, 'hoodie', 'images/hoodies/h12.png', '', 'images/hoodies/h12_dtl1.png', '', '', '????️ Glowing Eyes Design – Intense and eye-catching look, ⚡ Spectral Beast Graphic – Dark and mystical vibe, ???? Premium Fabric – Soft & durable feel, ???? Black Base – Sleek and versatile, ???? Statement Style – Bold and unique presence', 'S,M,L,XL,XXL', 2, 'SALE', '2026-03-22 13:35:17'),
(14, 'Ghost Vein Hoodie', 'A sleek hoodie featuring a bold yet minimal beast-inspired line design on a clean ivory base. Crafted for comfort, durability, and a refined streetwear look.', 1499.00, 1999.00, 'hoodie', 'images/hoodies/h13.png', '', 'images/hoodies/h13_dtl1.png', '', '', '⚡ Minimal Beast Design – Clean & sharp graphic, ???? Ivory White Base – Fresh and premium look, ???? Premium Fabric – Soft & durable feel, ???? Comfortable Fit – Perfect for daily wear, ???? Modern Style – Simple yet standout', 'S,M,L,XL,XXL', 4, 'HOT', '2026-03-22 13:38:15'),
(15, 'dark neon purple hoodie', 'A bold hoodie featuring a neon purple beast-inspired design on a deep black base. Built for comfort, durability, and a powerful modern streetwear look.', 1499.00, 1999.00, 'hoodie', 'images/hoodies/h14.png', '', 'images/hoodies/h14_dtl1.png', '', '', '⚡ Neon Purple Design – Bold & eye-catching graphic, ???? Premium Fabric – Soft & long-lasting, ???? Black Base – Sleek and versatile, ???? Comfortable Fit – Perfect for daily wear, ???? Statement Style – Strong and standout look', 'S,M,L,XL,XXL', 16, 'SALE', '2026-03-22 13:41:48'),
(16, 'Dark Root Hoodie', 'A sleek all-black hoodie featuring a subtle shadow-inspired beast design. Crafted with premium fabric for ultimate comfort, durability, and a refined dark streetwear look.', 1499.00, 1999.00, 'hoodie', 'images/hoodies/h15.png', '', 'images/hoodie/h15_dtl1.png', '', '', '⚫ Shadow Beast Design – Subtle yet powerful graphic, ???? Premium Fabric – Soft & long-lasting, ???? All Black Look – Clean and versatile, ???? Comfortable Fit – Perfect for daily wear, ???? Luxury Finish – Minimal and premium vibe', 'S,M,L,XL,XXL', 30, 'NEW IN', '2026-03-22 13:45:32'),
(17, 'Lava Surge Hoodie', 'A bold hoodie featuring a molten lava-inspired cracked design with fiery glow effects. Crafted with premium fabric for comfort, durability, and a powerful streetwear statement.', 1599.00, 2259.00, 'hoodie', 'images/hoodies/h16.png', '', 'images/hoodies/h16_dtl1.png', '', '', '???? Lava Crack Design – Intense and eye-catching graphic, ⚡ Fiery Glow Effect – Bold molten look, ???? Premium Fabric – Soft & durable feel, ???? Comfortable Fit – Perfect for daily wear, ???? Statement Style – Strong and standout presence', 'S,M,L,XL,XXL', 50, '', '2026-03-22 13:52:26'),
(18, 'Black Crack Hoodie', 'A sleek black hoodie featuring a sharp cracked-lightning design for a bold yet minimal look. Crafted with premium fabric for comfort, durability, and a refined streetwear style.', 899.00, 1499.00, 'hoodie', 'images/hoodies/h17.png', '', '', '', '', 'Bhai ye hoodie ???? minimal lovers ke liye perfect piece hai — classy + modern ???? Agar chahe to main tere sab hoodies ko categories me divide karke (Fire / Neon / Dark /', 'S,M,L,XL,XXL', 25, 'SALE', '2026-03-22 13:55:32'),
(19, 'Classy Cream Hoodie', 'A clean and modern hoodie featuring a bold abstract sigil design on a premium neutral base. Built for comfort, durability, and a refined minimal streetwear look.', 699.00, 999.00, 'hoodie', 'images/hoodies/h18.png', '', 'images/hoodies/h18_dtl1.png', 'images/hoodies/h18_dtl2.png', '', '???? Abstract Sigil Design – Bold & unique symbol, ???? Premium Fabric – Soft & long-lasting, ???? Neutral Beige Tone – Clean and versatile, ???? Comfortable Fit – Perfect for daily wear, ???? Minimal Style – Simple yet premium vibe', 'S,M,L,XL,XXL', 25, 'NEW IN', '2026-03-22 13:58:22'),
(20, 'Tribal Void Hoodie', 'A refined hoodie featuring a bold tribal-inspired sigil design on a clean neutral base. Crafted with premium fabric for comfort, durability, and a modern luxury streetwear look.', 699.00, 999.00, 'hoodie', 'images/hoodies/h19.png', '', 'images/hoodies/h19_dtl1.png', '', '', '???? Tribal Sigil Design – Sharp & unique graphic, ???? Neutral Base – Clean and versatile style, ???? Premium Fabric – Soft & long-lasting, ???? Comfortable Fit – Perfect for daily wear, ???? Luxury Minimal – Subtle yet standout vibe', 'S,M,L,XL,XXL', 25, 'NEW IN', '2026-03-22 14:00:41'),
(21, 'Black Tree Design Hoodie', '', 799.00, 999.00, 'hoodie', 'images/hoodies/h20.png', '', '', '', '', '', 'S,M,L,XL,XXL', 10, 'NEW IN', '2026-03-22 14:02:09'),
(22, 'Crimson Pulse Hoodie', 'A bold and electrifying hoodie featuring a glowing crimson pulse design that represents raw energy and power. Crafted with premium fabric for comfort, durability, and a striking streetwear presence.', 2299.00, 3999.00, 'hoodie', 'images/hoodies/h22.png', '', '', '', '', '❤️ Crimson Pulse Design – Eye-catching energy effect, ???? Premium Fabric – Soft & durable, ⚡ High Detail Print – Sharp and long-lasting, ???? Comfortable Fit – Everyday wear ready, ???? Bold Street Style – Standout look', 'S,M,L,XL,XXL', 100, 'NEW IN', '2026-03-22 14:06:22'),
(23, 'Classic Black Jacket', 'A sleek and timeless leather jacket designed for a bold yet refined look. Crafted with premium finish and structured fit, perfect for elevating everyday style with a luxury edge.', 999.00, 1299.00, 'jacket', 'images/jackets/jacket1.png', '', '', '', '', '???? Premium Leather Finish – Smooth & high-quality look, ???? Durable Stitching – Built to last, ⚡ Minimal Design – Clean and versatile style, ???? Structured Fit – Sharp modern silhouette, ???? Front Zip Closure – Easy and stylish wear', 'S,M,L,XL,XXL', 23, 'HOT', '2026-03-22 14:09:41'),
(24, 'Black Leather Jacket', 'A sleek and timeless leather jacket designed for a bold yet refined look. Crafted with premium finish and structured fit, perfect for elevating everyday style with a luxury edge.', 1199.00, 1499.00, 'jacket', 'images/jackets/jacket2.png', '', '', '', '', '???? Premium Leather Finish – Smooth & high-quality look, ???? Durable Stitching – Built to last, ⚡ Minimal Design – Clean and versatile style, ???? Structured Fit – Sharp modern silhouette, ???? Front Zip Closure – Easy and stylish wear', 'S,M,L,XL,XXL', 12, 'NEW IN', '2026-03-22 14:11:14'),
(25, 'Crimson Royale Leather Jacket', 'A bold and premium leather jacket in a rich crimson tone, designed to make a powerful style statement. Crafted with a sleek finish and structured fit, perfect for elevating your streetwear to a luxury level.', 899.00, 1299.00, 'jacket', 'images/jackets/jacket3.png', 'images/jackets/jacket3_back.png', '', '', '', '❤️ Rich Crimson Finish – Premium and eye-catching color, ???? High-Quality Leather – Smooth & durable, ⚡ Minimal Luxury Design – Clean yet bold look, ???? Tailored Fit – Sharp and modern silhouette, ???? Front Zip Closure – Stylish & functional', 'S,M,L,XL,XXL', 22, 'NEW IN', '2026-03-22 14:14:43'),
(26, 'Brown Leather Jacket', 'A timeless leather jacket with a rich rustic finish, inspired by vintage biker aesthetics. Designed with premium craftsmanship and durable structure, perfect for a bold and confident everyday look.', 1299.00, 1599.00, 'jacket', 'images/jackets/jacket4.png', 'images/jackets/jacket4_back.png', 'images/jackets/jacket4_detail1.png', 'images/jackets/jacket4_detail2.png', '', '???? Rustic Leather Finish – Rich vintage look, ???? Premium Craftsmanship – Strong & durable build, ⚡ Classic Button Style – Retro-inspired design, ???? Structured Fit – Sharp and masculine silhouette, ???? Timeless Appeal – Never goes out of style', 'S,M,L,XL,XXL', 33, 'HOT', '2026-03-22 14:19:13'),
(27, 'Rage Titan Anime Hoodie', 'Unleash unstoppable energy with this bold anime-inspired hoodie featuring a minimal front graphic and an explosive back design. Built for those who carry raw power, intensity, and fearless street attitude.', 899.00, 1299.00, 'hoodie', 'images/hoodies/a1.png', 'images/hoodies/a1_bk.png', 'images/hoodies/a1_dt1.png', 'images/hoodies/a1_dt2.png', '', '???? Explosive Back Artwork – High-impact anime rage design ???? Minimal Front Print – Clean & aesthetic balance ???? Premium Fabric – Soft, durable & long-lasting ???? Street Fit – Relaxed oversized comfort ⚡ High-Detail Print – Sharp lines & bold contrast', 'S,M,L,XL,XXL', 32, 'NEW IN', '2026-03-23 15:15:46'),
(28, 'Hunter Anime X Hoodie', 'A bold anime-inspired hoodie featuring a striking back graphic with glowing eyes and dark energy aesthetics. Designed for those who love edgy streetwear with a powerful and unique visual impact.', 899.00, 1299.00, 'hoodie', 'images/hoodies/a2.png', 'images/hoodies/a2_bk.png', 'images/hoodies/a2_dt1.png', 'images/hoodies/a2_dt2.png', '', '????️ Anime Back Print – Eye-catching high-detail character design, ⚡ Glow Effect Eyes – Intense and powerful look, ???? Minimal Front (XX Logo) – Clean and stylish contrast, ???? Premium Fabric – Soft & durable for daily wear, ???? Streetwear Fit – Perfect oversized modern vibe', 'S,M,L,XL,XXL', 12, 'NEW IN', '2026-03-24 04:14:16'),
(29, 'Upper Moon Elite Anime Hoodie', 'A powerful anime-inspired hoodie featuring a bold front eye design and an intense back character graphic. Built for those who want a dark, elite streetwear look with strong visual impact.', 899.00, 1399.00, 'hoodie', 'images/hoodies/a3.png', 'images/hoodies/a3_bk.png', 'images/hoodies/a3_dt1.png', 'images/hoodies/a3_dt2.png', '', '????️ Front Eye Design – Unique and attention-grabbing, ???? Anime Back Print – High-detail character artwork, ???? Dark Aesthetic – Bold and premium street vibe, ???? Premium Fabric – Soft & durable quality, ???? Oversized Fit – Trendy and comfortable wear', 'S,M,L,XL,XXL', 23, 'NEW IN', '2026-03-24 04:17:52'),
(30, 'Black Bull x Anime hoodie', 'A bold anime-inspired hoodie featuring a fierce samurai-style character on the back with a clean minimal front design. Built for those who carry attitude and power in their style.', 899.00, 1399.00, 'hoodie', 'images/hoodies/a4.png', 'images/hoodies/a4_bk.png', 'images/hoodies/a4_dt1.png', 'images/hoodies/a4_dt2.png', '', '⚔️ Samurai Back Print – High-detail intense character artwork, ???? Minimal Front Design – Clean and stylish look, ???? Dark Street Aesthetic – Bold and edgy vibe, ???? Premium Fabric – Soft & durable quality, ???? Oversized Fit – Comfortable and trendy wear', 'S,M,L,XL,XXL', 34, 'NEW IN', '2026-03-24 04:22:12'),
(31, 'Gojo Cursed  Anime Hoodie', 'A powerful anime-inspired hoodie featuring a striking front eye symbol with Japanese text and an intense back character design. Built for those who embrace dark energy, dominance, and fearless street style.', 899.00, 1299.00, 'hoodie', 'images/hoodies/a5.png', 'images/hoodies/a5_bk.png', 'images/hoodies/a5_dt1.png', 'images/hoodies/a5_dt2.png', '', '????️ Eye Symbol Front – Unique and mysterious design, ???? Japanese Text Detail – Adds authentic anime vibe, ⚡ Intense Back Print – Bold high-detail character artwork, ???? Premium Fabric – Soft & durable quality, ???? Oversized Fit – Comfortable modern streetwear style', 'S,M,L,XL,XXL', 33, 'HOT', '2026-03-24 04:25:27'),
(33, 'spidy red hoodie', 'feel comfort , look different like a gentle men .', 769.00, 1299.00, 'hoodie', 'images/uploads/1779031553_ChatGPTImageMay17202608_47_22PM.png', '', '', '', '', 'look iconic ', 'S,M,L,XL,XXL', 45, 'NEW IN', '2026-05-17 15:25:53');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `review`, `created_at`) VALUES
(1, 1, 2, 5, 'good comfort,good quality', '2026-03-22 05:24:14'),
(2, 31, 2, 5, 'wrm comfort and good price', '2026-03-31 03:32:39'),
(3, 14, 2, 5, 'nice colour and comfort', '2026-03-31 03:37:03'),
(4, 9, 2, 4, 'iconic design', '2026-05-25 11:10:57'),
(5, 33, 2, 3, 'something', '2026-05-25 12:18:02'),
(6, 2, 2, 4, 'something', '2026-05-25 12:26:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_admin` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `otp`, `is_verified`, `phone`, `address`, `is_admin`, `created_at`) VALUES
(2, 'Babu Bhaiya', 'bhaiyababu687q@gmail.com', '$2y$10$7o/kWfmQdgn.jhf2fTbqculhQk.0y3tRt7uBjbWRyBpjggxwZ8Gie', NULL, 1, '8837894309', 'nawanshahr , railway road, karyam', 2, '2026-03-22 04:39:52'),
(6, 'ishav', 'mpoke4200@gmail.com', '$2y$10$/qf6U3EkXk5pMOlLNZ5nyuCyqe8Eias5LQnZpWu4Oj6M5uDEpPsRu', NULL, 1, '8837894309', NULL, 0, '2026-03-31 10:58:32'),
(9, 'youngboy', 'youngai588@gmail.com', '$2y$10$c1SDCeiPAyv2hWvDjNC8n.X04a7zvdaXK/WXThb56ADMnZfe0AmJW', NULL, 1, '+918837894309', NULL, 0, '2026-05-16 10:55:57'),
(10, 'botoe', 'iammaniklal@gmail.com', '$2y$10$l7uuozbsdkQgvclaM5fCL.jS3fdyyxjyS3d5ZIxm5FJvGpyXIdfJ2', NULL, 1, '+918837894309', NULL, 0, '2026-05-16 11:03:00'),
(13, 'YT Boy', 'yahoohoo334@gmail.com', '$2y$10$KA6razjmGNsE/VSQQ.nbnuMlxk5wy4NqoQU9bMkC4OszG6IEEONqu', NULL, 1, '+919382999143', NULL, 0, '2026-05-16 13:22:25');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(7, 2, 28, '2026-03-24 04:45:47'),
(8, 2, 29, '2026-03-24 04:45:49'),
(11, 2, 31, '2026-03-31 04:39:47'),
(18, 2, 7, '2026-04-22 14:08:24'),
(20, 2, 9, '2026-05-25 12:26:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `custom_designs`
--
ALTER TABLE `custom_designs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `design_orders`
--
ALTER TABLE `design_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `design_id` (`design_id`),
  ADD KEY `buyer_id` (`buyer_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `custom_designs`
--
ALTER TABLE `custom_designs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `design_orders`
--
ALTER TABLE `design_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `custom_designs`
--
ALTER TABLE `custom_designs`
  ADD CONSTRAINT `custom_designs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `design_orders`
--
ALTER TABLE `design_orders`
  ADD CONSTRAINT `design_orders_ibfk_1` FOREIGN KEY (`design_id`) REFERENCES `custom_designs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `design_orders_ibfk_2` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
