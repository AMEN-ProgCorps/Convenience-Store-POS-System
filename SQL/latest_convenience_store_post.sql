-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2025 at 02:52 PM
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
-- Database: `convenience_store_post`
--

-- --------------------------------------------------------

--
-- Stand-in structure for view `all_accounts`
-- (See below for the actual view)
--
CREATE TABLE `all_accounts` (
`id` varchar(10)
,`name` varchar(255)
,`password` varchar(255)
,`phone_number` varchar(15)
,`email` varchar(100)
,`role` enum('Admin','Cashier','Manager')
);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`) VALUES
(1, 'Beverages'),
(2, 'Food & Snacks'),
(3, 'Personal Care'),
(4, 'Household Items'),
(5, 'Groceries'),
(6, 'Other Essentials');

-- --------------------------------------------------------

--
-- Table structure for table `customer_accounts`
--

CREATE TABLE `customer_accounts` (
  `customer_id` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_accounts`
--

INSERT INTO `customer_accounts` (`customer_id`, `name`, `password`, `phone_number`, `email`) VALUES
('C101', 'guest_account', 'password123', '0192222', 'guest@example.com'),
('C102', 'Bleak098', 'MacCloud', '09774751322', 'cameorn@gmail.com');

--
-- Triggers `customer_accounts`
--
DELIMITER $$
CREATE TRIGGER `before_insert_customer_accounts` BEFORE INSERT ON `customer_accounts` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(customer_id, 2) AS UNSIGNED)), 100) + 1 INTO next_id FROM customer_accounts;
    SET NEW.customer_id = CONCAT('C', next_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `discount_id` int(11) NOT NULL,
  `valid_from` date NOT NULL,
  `valid_till` date NOT NULL,
  `description` varchar(250) NOT NULL,
  `discount_percentage` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discounts`
--

INSERT INTO `discounts` (`discount_id`, `valid_from`, `valid_till`, `description`, `discount_percentage`) VALUES
(1, '2023-10-01', '2026-10-28', 'kiss muna', 10.00),
(2, '2023-11-01', '2023-11-30', 'November Discount', 15.00),
(3, '2023-12-01', '2023-12-31', 'December Discount', 20.00);

-- --------------------------------------------------------

--
-- Table structure for table `employee_accounts`
--

CREATE TABLE `employee_accounts` (
  `employee_id` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` enum('Admin','Cashier','Manager') NOT NULL,
  `store_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_accounts`
--

INSERT INTO `employee_accounts` (`employee_id`, `name`, `role`, `store_name`, `password`) VALUES
('E1', 'John Doe', 'Cashier', 'Main Store', 'securepass'),
('E2', 'Administrator', 'Admin', 'Main Store', 'admin');

--
-- Triggers `employee_accounts`
--
DELIMITER $$
CREATE TRIGGER `before_insert_employee_accounts` BEFORE INSERT ON `employee_accounts` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(employee_id, 2) AS UNSIGNED)), 0) + 1 INTO next_id FROM employee_accounts;
    SET NEW.employee_id = CONCAT('E', next_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_records`
--

CREATE TABLE `inventory_records` (
  `record_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `employee_id` varchar(10) DEFAULT NULL,
  `quantity_change` int(11) NOT NULL,
  `change_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_records`
--

INSERT INTO `inventory_records` (`record_id`, `product_id`, `employee_id`, `quantity_change`, `change_date`) VALUES
(1, 8, 'E2', 1, '2025-05-27 19:16:02'),
(2, 6, 'E2', 1, '2025-05-27 20:45:01');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` varchar(10) DEFAULT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,5) NOT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `payment_type` enum('cash','e-wallet') NOT NULL,
  `order_status` enum('pending','completed','cancelled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `order_date`, `total_amount`, `discount_id`, `payment_type`, `order_status`) VALUES
(2, 'C102', '2025-05-23 18:13:37', 280.00000, NULL, 'cash', 'cancelled'),
(3, 'C102', '2025-05-23 18:18:13', 490.00000, 1, 'cash', 'completed'),
(4, 'C102', '2025-05-24 15:12:46', 39.20000, NULL, 'cash', 'cancelled'),
(5, 'C102', '2025-05-24 15:54:40', 2026.08000, 1, 'cash', 'cancelled'),
(6, 'C102', '2025-05-24 15:58:31', 380.80000, NULL, 'cash', 'cancelled'),
(7, 'C101', '2025-05-24 21:26:13', 89.60000, NULL, 'cash', 'cancelled'),
(8, 'C102', '2025-05-26 23:32:20', 302.40000, 1, 'cash', 'cancelled'),
(9, 'C102', '2025-05-26 23:35:25', 372.96000, 1, 'cash', 'cancelled'),
(10, 'C102', '2025-05-26 23:35:43', 201.60000, 1, 'cash', 'cancelled'),
(11, 'C102', '2025-05-26 23:40:00', 112.00000, NULL, 'cash', 'cancelled'),
(12, 'C102', '2025-05-26 23:40:07', 112.00000, NULL, 'cash', 'cancelled'),
(13, 'C102', '2025-05-26 23:40:27', 112.00000, NULL, 'cash', 'cancelled'),
(14, 'C102', '2025-05-26 23:43:54', 168.00000, NULL, 'cash', 'cancelled'),
(15, 'C101', '2025-05-26 23:44:16', 78.40000, NULL, 'cash', 'cancelled'),
(16, 'C101', '2025-05-27 19:16:32', 24.64000, NULL, 'cash', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `total_ammount` decimal(10,5) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_item`
--

INSERT INTO `order_item` (`order_id`, `product_id`, `total_ammount`, `quantity`, `unit_price`) VALUES
(2, 2, 250.00000, 5, 50.00000),
(3, 5, 240.00000, 6, 40.00000),
(3, 2, 250.00000, 5, 50.00000),
(4, 3, 35.00000, 1, 35.00000),
(5, 6, 60.00000, 1, 60.00000),
(5, 4, 1950.00000, 13, 150.00000),
(6, 1, 340.00000, 17, 20.00000),
(7, 1, 80.00000, 4, 20.00000),
(8, 2, 300.00000, 6, 50.00000),
(9, 2, 300.00000, 6, 50.00000),
(9, 3, 70.00000, 2, 35.00000),
(10, 2, 200.00000, 4, 50.00000),
(11, 2, 100.00000, 2, 50.00000),
(12, 2, 100.00000, 2, 50.00000),
(13, 2, 100.00000, 2, 50.00000),
(14, 2, 150.00000, 3, 50.00000),
(15, 3, 70.00000, 2, 35.00000),
(16, 8, 11.00000, 1, 11.00000),
(16, 7, 11.00000, 11, 1.00000);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `stock_level` int(11) NOT NULL,
  `price` decimal(10,5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `name`, `stock_level`, `price`) VALUES
(1, 1, 'Bottled Water', 79, 20.00000),
(2, 2, 'Potato Chips', 70, 50.00000),
(3, 3, 'Soap', 95, 35.00000),
(4, 4, 'Dishwashing Liquid', 87, 150.00000),
(5, 5, 'Rice', 94, 40.00000),
(6, 6, 'Condoms', 96, 60.00000),
(7, 1, '1q', 11, 1.00000),
(8, 1, '111', 1, 11.00000);

-- --------------------------------------------------------

--
-- Structure for view `all_accounts`
--
DROP TABLE IF EXISTS `all_accounts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `all_accounts`  AS SELECT `customer_accounts`.`customer_id` AS `id`, `customer_accounts`.`name` AS `name`, `customer_accounts`.`password` AS `password`, `customer_accounts`.`phone_number` AS `phone_number`, `customer_accounts`.`email` AS `email`, NULL AS `role` FROM `customer_accounts`union all select `employee_accounts`.`employee_id` AS `id`,`employee_accounts`.`name` AS `name`,`employee_accounts`.`password` AS `password`,NULL AS `phone_number`,NULL AS `email`,`employee_accounts`.`role` AS `role` from `employee_accounts`  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `customer_accounts`
--
ALTER TABLE `customer_accounts`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`discount_id`);

--
-- Indexes for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `inventory_records`
--
ALTER TABLE `inventory_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `discount_id` (`discount_id`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventory_records`
--
ALTER TABLE `inventory_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory_records`
--
ALTER TABLE `inventory_records`
  ADD CONSTRAINT `inventory_records_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `inventory_records_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employee_accounts` (`employee_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer_accounts` (`customer_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`discount_id`);

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
