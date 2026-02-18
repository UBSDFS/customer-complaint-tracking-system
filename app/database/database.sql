-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 17, 2026 at 03:17 PM
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
-- Database: `complaint_system`
--
CREATE DATABASE IF NOT EXISTS `complaint_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `complaint_system`;

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `complaint_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `tech_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `complaint_type_id` int(11) NOT NULL,
  `complaint_resolution_date` date DEFAULT NULL,
  `status` enum('open','assigned','in_progress','resolved') NOT NULL DEFAULT 'open',
  `details` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`complaint_id`, `customer_id`, `tech_id`, `product_id`, `complaint_type_id`, `complaint_resolution_date`, `status`, `details`, `image_path`) VALUES
(2, 7, 6, 1, 1, NULL, 'assigned', 'My pc is overheating!', '/customer-complaint-tracking-system/public/uploads/complaints/c_7_1771337564.png'),
(3, 7, NULL, 1, 2, NULL, 'open', 'I want a replacement pc!', NULL),
(4, 8, 6, 1, 3, NULL, 'assigned', 'I was overcharged for my phone bill!', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `complaint_types`
--

CREATE TABLE `complaint_types` (
  `complaint_type_id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaint_types`
--

INSERT INTO `complaint_types` (`complaint_type_id`, `name`, `description`) VALUES
(1, 'Product Defect', 'Product arrived broken or fails under normal use'),
(2, 'Warranty Claim', 'Customer needs help filing or using warranty'),
(3, 'Billing Issue', 'Incorrect charges, refunds, or invoice problems');

-- --------------------------------------------------------

--
-- Table structure for table `customer_profiles`
--

CREATE TABLE `customer_profiles` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `street_address` varchar(100) NOT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(2) NOT NULL,
  `zip` varchar(5) NOT NULL,
  `phone` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_profiles`
--

INSERT INTO `customer_profiles` (`user_id`, `first_name`, `last_name`, `street_address`, `city`, `state`, `zip`, `phone`) VALUES
(7, 'Customer', 'Customer', '123 Road Road', 'Norfolk', 'VA', '12345', '3333333333'),
(8, 'Bob', 'Jones', '124 Road Street', 'Richmond', 'VA', '12342', '4444444444');

-- --------------------------------------------------------

--
-- Table structure for table `employee_profiles`
--

CREATE TABLE `employee_profiles` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone_ext` varchar(10) DEFAULT NULL,
  `level` enum('tech','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_profiles`
--

INSERT INTO `employee_profiles` (`user_id`, `first_name`, `last_name`, `phone_ext`, `level`) VALUES
(1, 'Admin', 'Admin', '1111111111', 'admin'),
(6, 'Tech', 'Tech', '2222222222', 'tech');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`) VALUES
(1, 'Laptop', 'Personal Portable Computer'),
(2, 'Cell Phone', 'Personal Cellular Phone'),
(3, 'House Phone', 'Shared Static Phone'),
(4, 'Router/Modem', 'Internet and Networking Devices'),
(5, 'Desktop PC', 'Static Desktop PC');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('customer','tech','admin') NOT NULL DEFAULT 'customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `role`) VALUES
(1, 'admin@example.com', '$2y$10$o5NFqRCDzkqtJDbk25DOCum6mlzeKjur4E76tQvirllu8b8KM/5UG', 'admin'),
(6, 'tech@example.com', '$2y$10$IseOvFffQGOBPMiokGjKguuOd2P542hRpSPo25yek.VgOuEmBuMHq', 'tech'),
(7, 'customer@example.com', '$2y$10$Q6PWCAH4lFMncbDcV7WA1Ooa139J4lYRJc6.hvC1deWetrEGaTw0C', 'customer'),
(8, 'bobjones@email.com', '$2y$10$8XCUDOrS.SpF6pAdcBujnOxkKKa1mdpxxdYiecmX2YS3FSQef.5te', 'customer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`complaint_id`),
  ADD KEY `fk_complaints_customer` (`customer_id`),
  ADD KEY `fk_complaints_tech` (`tech_id`),
  ADD KEY `fk_complaints_product` (`product_id`),
  ADD KEY `fk_complaints_type` (`complaint_type_id`);

--
-- Indexes for table `complaint_types`
--
ALTER TABLE `complaint_types`
  ADD PRIMARY KEY (`complaint_type_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `customer_profiles`
--
ALTER TABLE `customer_profiles`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `employee_profiles`
--
ALTER TABLE `employee_profiles`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `complaint_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `complaint_types`
--
ALTER TABLE `complaint_types`
  MODIFY `complaint_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `fk_complaints_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_complaints_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `fk_complaints_tech` FOREIGN KEY (`tech_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_complaints_type` FOREIGN KEY (`complaint_type_id`) REFERENCES `complaint_types` (`complaint_type_id`);

--
-- Constraints for table `customer_profiles`
--
ALTER TABLE `customer_profiles`
  ADD CONSTRAINT `fk_customer_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_profiles`
--
ALTER TABLE `employee_profiles`
  ADD CONSTRAINT `fk_employee_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
