-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2024 at 12:12 PM
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
-- Database: `capstoneloginver2`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `user_id`, `action`, `timestamp`) VALUES
(1, 2, 'User registered', '2024-10-03 07:24:02'),
(2, 3, 'User registered', '2024-10-04 05:29:24'),
(3, 4, 'User registered', '2024-10-04 22:02:43'),
(4, 5, 'User registered', '2024-10-04 22:27:30'),
(5, 6, 'User registered', '2024-10-04 22:41:03'),
(6, 7, 'User registered', '2024-10-04 22:45:27'),
(7, 8, 'User registered', '2024-10-07 06:26:47'),
(8, 9, 'User registered', '2024-10-10 13:20:05'),
(9, 10, 'User registered', '2024-10-11 12:03:10'),
(10, 11, 'User registered', '2024-10-11 12:12:36'),
(11, 12, 'User registered', '2024-10-22 07:33:34'),
(12, 13, 'User registered', '2024-10-25 07:17:14'),
(13, 14, 'User registered', '2024-10-25 07:18:47'),
(14, 15, 'User registered', '2024-10-25 09:44:03');

-- --------------------------------------------------------

--
-- Table structure for table `customization_orders`
--

CREATE TABLE `customization_orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `size` enum('S','M','L','XL','XXL') NOT NULL,
  `customizations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`customizations`)),
  `front_text` varchar(255) DEFAULT NULL,
  `back_text` varchar(255) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('Pending','Processing','Completed','Cancelled','Delivery') DEFAULT 'Pending',
  `file_path` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customization_orders`
--

INSERT INTO `customization_orders` (`id`, `user_id`, `product_name`, `size`, `customizations`, `front_text`, `back_text`, `order_date`, `updated_at`, `status`, `file_path`) VALUES
(8, 2, 'Jersey', 'XL', NULL, 'sea', 'player5', '2024-11-15 06:19:12', '2024-11-15 06:19:12', 'Pending', './uploaded_files/bluejersey.glb'),
(9, 2, 'T-Shirt', 'XXL', NULL, 'pepper', 'spicychallengeme', '2024-11-15 06:20:10', '2024-11-15 06:20:10', 'Pending', './uploaded_files/tshirtred.glb'),
(10, 2, 'Hoodie', 'L', NULL, 'addidas', 'Tasdas', '2024-11-15 06:29:25', '2024-11-15 11:04:06', 'Completed', './uploaded_files/green.glb'),
(16, 2, 'T-Shirt', 'S', NULL, 'asdas', 'asdas', '2024-11-15 11:43:49', '2024-11-15 11:43:49', 'Pending', './uploaded_files/green jersey.glb'),
(17, 2, 'T-Shirt', 'M', '{\"front_text\":\"night\",\"back_text\":\"panda\"}', 'night', 'panda', '2024-11-16 13:40:00', '2024-11-16 13:40:00', 'Pending', 'uploads/triplang.glb'),
(18, 2, 'Hoodie', 'M', '{\"front_text\":\"ta\",\"back_text\":\"te\"}', 'ta', 'te', '2024-11-16 13:43:04', '2024-11-16 13:43:04', 'Pending', 'uploads/taptap.glb'),
(19, 2, 'T-Shirt', 'S', '{\"front_text\":\"\",\"back_text\":\"\"}', '', '', '2024-11-17 03:05:50', '2024-11-17 03:05:50', 'Pending', 'uploads/custom_model (5).glb');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `facebook_account` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `order_date`, `status`, `facebook_account`) VALUES
(14, 2, 11, '2024-10-25 17:16:16', 'Pending', 'lester'),
(15, 15, 13, '2024-10-25 17:46:07', 'Finished', 'Vincent Lee Tiongco'),
(16, 2, 10, '2024-10-26 17:53:50', 'Pending', 'nana'),
(18, 10, 21, '2024-11-02 20:05:37', 'Pending', 'xent lee');

-- --------------------------------------------------------

--
-- Table structure for table `predefined_questions`
--

CREATE TABLE `predefined_questions` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `predefined_questions`
--

INSERT INTO `predefined_questions` (`id`, `question`, `answer`) VALUES
(1, 'How to order?', 'To place an order, go to the shop and select buy now then please fill out additional information then confirm buy now'),
(2, 'What are your delivery options?', 'We deliver apparel at your house so please carefully make sure your address is right'),
(3, 'How can I contact customer support?', 'You can reach our facebook page'),
(4, 'How much is a jersey?', 'P500');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `size` enum('S','M','L','XL','XXL') DEFAULT NULL,
  `stock` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_url`, `date_created`, `date_updated`, `size`, `stock`) VALUES
(10, 'Gilas Pilipinas Jersey', 'Philippine Olympic Jersey', 499.00, 'uploads/gilas.jpg', '2024-10-10 22:51:32', '2024-11-15 18:58:12', 'XL', 0),
(11, 'Raptor Short ', 'Sublimation Short ', 499.00, 'uploads/red bear short.jpg', '2024-10-10 23:05:58', '2024-11-15 18:59:06', 'L', 0),
(13, 'Black Gold Short', 'Sublimation Short', 499.00, 'uploads/black orange.jpg', '2024-10-11 00:27:45', '2024-11-15 19:00:24', 'XL', 0),
(21, 'Grenn Sharp Short', 'Sublimation Short', 499.00, 'uploads/green short.jpg', '2024-10-26 19:08:09', '2024-11-15 19:00:57', 'L', 0),
(24, 'Council Brgy Jersey', 'Sublimation Barangay Jersey', 499.00, 'uploads/council.jpg', '2024-10-26 19:22:06', '2024-11-15 19:01:30', 'XL', 1),
(27, 'Central Toda Jersey', 'Sublimation For Toda\'s Jersey', 499.00, 'uploads/Central Toda.jpg', '2024-11-15 15:51:43', '2024-11-15 19:02:17', 'XL', 1),
(28, 'Comer\'s Warmer', 'Sublimation For Basketball Warmer', 499.00, 'uploads/comers.jpg', '2024-11-15 15:52:27', '2024-11-15 19:03:26', 'XL', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact_no` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `contact_no`, `address`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@example.com', '$2y$10$SApNx.JT5mUTb6a2yU6Oy.7i6QUOlMYS.sUWeFvXRN.rde7fD9pq2', NULL, NULL, 'admin', '2024-10-03 06:56:10'),
(2, 'UserCent', 'UserCent@gmail.com', '$2y$10$ScQLAyRo0/iqKjFMzsrRPecPR1b9sU34dfLurTeS2o3hcy403uhLi', '12345678912', 'Lubao', 'user', '2024-10-03 07:24:02'),
(3, 'adsasd', 'asdn@gmail.com', '$2y$10$l4RTS2patHBpcVSG.4Mf2OOPyg8CjKRWEGATon/0J.6yBPd2UceG6', '12345678912', '123123', 'user', '2024-10-04 05:29:24'),
(4, 'UserRamJay', 'UserRamJay@gmail.com', '$2y$10$.KbUKG32IZTk47lHVEyTuug3UdP3IXfBxlivYv2S.CVS6aezIRueK', '12345678913', 'FloridaBlanca', 'user', '2024-10-04 22:02:43'),
(5, 'UchihaTom', 'UchihaTom@yahoo.com', '$2y$10$HZ9DhJ4D8pd/Im/aqseWZuZNl9Z.YVdZK4basmk1OVfIhatGLdfaC', '09194399821', 'Konohagakure', 'user', '2024-10-04 22:27:30'),
(6, 'SenjuJerry', 'SenjuJerry@yahoo.com', '$2y$10$vVOu9mmNEquGced.PFHUqOTg5hxYjUua3QIF2EN.ZmhmrPWEKi/z6', '09856643781', 'Konohagakure', 'user', '2024-10-04 22:41:03'),
(7, 'AngryBird', 'AngryBird@yahoo.com', '$2y$10$Mzqqk0SLbw9BW/kicT29FuOzSPQ/ZslUiAwSCgNN/BzRd6Lk26iFW', '1234565432', 'Bird Island', 'user', '2024-10-04 22:45:27'),
(8, 'lee', 'lee@gmail.com', '$2y$10$HzrcdBNLmigyBocz.Dlkh.EcDgHIDojSxQeFyjvK9sM2GBtlGVRk.', '091123124324', 'aweqw', 'user', '2024-10-07 06:26:47'),
(9, 'lester', 'lester.kabilng@gmail.com', '$2y$10$0pr67fUtv3Q6m.PwvNLl3exkQLj0u3/pxP/U7HXOF0lPNSYFyGg3C', '', '', 'user', '2024-10-10 13:20:05'),
(10, 'kweenp', 'qwe@gmail.com', '$2y$10$ArLOGvTc4.9Zw9jIoWSkj.C1eKHjiOC.WNMSvIVHRax8boTH/PdNi', '091232312312', 'sp12', 'user', '2024-10-11 12:03:10'),
(11, 'jbrian', 'jb@gmail.com', '$2y$10$hpBWbnk8vzSpsyhGl8eli.urLfDsz.ZMaBZzkZoZMJ.sFw1sFU5Hu', '091123124324', 'asdasewq', 'user', '2024-10-11 12:12:36'),
(12, 'luffy05g0', 'asd@gmail.com', '$2y$10$/mUQasd6nmftYDqEakuwdOHc2r.qU.nR6yrz91ZHKU2nize3NMgNa', '12345678912', 'qweqwe', 'user', '2024-10-22 07:33:34'),
(13, 'kristinabagyo', 'kristinabagyo@email.com', '$2y$10$Rwg7dBHOqrVA7DGyVd2CM.J2KMbjLVtIy9bXlaSC0k23EMa8x/dLm', '12345678912', 'Sea', 'user', '2024-10-25 07:17:14'),
(14, 'saber', 'layla@gmail.com', '$2y$10$fC5xkjwEO8MG/FSXr6Y37.rtZenLRxDXRdXaxRYG7ZDGnGA1Gtw9O', '1234214324', 'sp1', 'user', '2024-10-25 07:18:47'),
(15, 'newuser', 'newuser@gmail.com', '$2y$10$4N6VXLlkZ21HPuy5SMezeOajBbpYKS1vP3eI4SByRw1GtGNlNJqGO', '0923232323', 'Lubao Pampanga ', 'user', '2024-10-25 09:44:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `customization_orders`
--
ALTER TABLE `customization_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `predefined_questions`
--
ALTER TABLE `predefined_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `customization_orders`
--
ALTER TABLE `customization_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `predefined_questions`
--
ALTER TABLE `predefined_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `customization_orders`
--
ALTER TABLE `customization_orders`
  ADD CONSTRAINT `customization_orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
