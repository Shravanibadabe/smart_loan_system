-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 10, 2026 at 06:45 AM
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
-- Database: `loan_ai_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(11) NOT NULL,
  `income` int(11) DEFAULT NULL,
  `loan_amount` int(11) DEFAULT NULL,
  `credit_history` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `risk_level` varchar(20) DEFAULT NULL,
  `dependents` int(5) DEFAULT NULL,
  `education` varchar(50) DEFAULT NULL,
  `employment` varchar(50) DEFAULT NULL,
  `annual_income` float DEFAULT NULL,
  `loan_duration` int(5) DEFAULT NULL,
  `cibil_score` int(5) DEFAULT NULL,
  `assets` float DEFAULT NULL,
  `emi` float DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_id` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `income`, `loan_amount`, `credit_history`, `status`, `risk_level`, `dependents`, `education`, `employment`, `annual_income`, `loan_duration`, `cibil_score`, `assets`, `emi`, `created_at`, `customer_name`, `customer_id`) VALUES
(1, 60000, 200000, 1, 'Loan Approved - Low Risk (92%)', 'Low', 2, 'Graduate', 'Salaried', NULL, 36, 820, 500000, 6200, '2026-01-05 04:50:30', 'Rahul Sharma', 'CUST1001'),
(2, 45000, 180000, 1, 'Loan Approved - Medium Risk (74%)', 'Medium', 1, 'Graduate', 'Salaried', NULL, 24, 710, 200000, 7500, '2026-01-15 06:00:20', 'Priya Singh', 'CUST1002'),
(3, 30000, 150000, 0, 'Loan Rejected - High Risk (48%)', 'High', 3, 'Not Graduate', 'Self-Employed', NULL, 36, 590, 100000, 5200, '2026-02-10 09:52:10', 'Amit Patel', 'CUST1003'),
(4, 75000, 250000, 1, 'Loan Approved - Low Risk (95%)', 'Low', 1, 'Graduate', 'Salaried', NULL, 48, 850, 700000, 6800, '2026-02-18 03:42:55', 'Sneha Verma', 'CUST1004'),
(5, 50000, 300000, 1, 'Loan Approved - Medium Risk (70%)', 'Medium', 2, 'Graduate', 'Self-Employed', NULL, 60, 680, 250000, 7200, '2026-03-03 09:15:10', 'Karan Mehta', 'CUST1005'),
(6, 28000, 120000, 0, 'Loan Rejected - High Risk (40%)', 'High', 4, 'Not Graduate', 'Self-Employed', NULL, 24, 560, 80000, 4800, '2026-03-11 04:40:40', 'Neha Gupta', 'CUST1006'),
(7, 90000, 400000, 1, 'Loan Approved - Low Risk (93%)', 'Low', 1, 'Graduate', 'Salaried', NULL, 60, 870, 900000, 9000, '2026-04-04 10:48:20', 'Rohit Kulkarni', 'CUST1007'),
(8, 38000, 160000, 1, 'Loan Approved - Medium Risk (72%)', 'Medium', 2, 'Graduate', 'Self-Employed', NULL, 36, 700, 150000, 5400, '2026-04-09 06:42:12', 'Anjali Nair', 'CUST1008'),
(9, 22000, 90000, 0, 'Loan Rejected - High Risk (35%)', 'High', 3, 'Not Graduate', 'Self-Employed', NULL, 18, 520, 50000, 3200, '2026-05-01 13:15:25', 'Vikas Reddy', 'CUST1009'),
(10, 65000, 220000, 1, 'Loan Approved - Low Risk (91%)', 'Low', 1, 'Graduate', 'Salaried', NULL, 36, 810, 450000, 6100, '2026-05-14 06:25:42', 'Pooja Desai', 'CUST1010'),
(11, 48000, 190000, 1, 'Loan Approved - Medium Risk (76%)', 'Medium', 2, 'Graduate', 'Salaried', NULL, 24, 690, 210000, 7000, '2026-06-06 09:00:18', 'Arjun Kapoor', 'CUST1011'),
(12, 27000, 110000, 0, 'Loan Rejected - High Risk (42%)', 'High', 3, 'Not Graduate', 'Self-Employed', NULL, 24, 580, 90000, 4500, '2026-06-18 05:10:50', 'Meera Iyer', 'CUST1012'),
(13, 72000, 270000, 1, 'Loan Approved - Low Risk (94%)', 'Low', 2, 'Graduate', 'Salaried', NULL, 48, 840, 650000, 7200, '2026-07-07 11:50:30', 'Sanjay Mishra', 'CUST1013'),
(14, 39000, 170000, 1, 'Loan Approved - Medium Risk (71%)', 'Medium', 2, 'Graduate', 'Self-Employed', NULL, 36, 705, 180000, 5600, '2026-07-19 07:20:40', 'Kavita Joshi', 'CUST1014'),
(15, 25000, 100000, 0, 'Loan Rejected - High Risk (38%)', 'High', 4, 'Not Graduate', 'Self-Employed', NULL, 18, 540, 60000, 3500, '2026-08-03 13:40:05', 'Manoj Tiwari', 'CUST1015'),
(16, 400000, 40000, 1, 'Loan Approved - Medium Risk (85%)', 'Low', 3, 'Graduate', 'Salaried', NULL, 12, 950, 1000000, 3488.79, '2026-03-09 17:50:10', 'Shravani Badabe', 'CUST4806'),
(17, 55000, 200000, 1, 'Loan Approved - Low Risk (95%)', 'Low', 2, 'Graduate', 'Salaried', NULL, 36, 780, 400000, 6313.51, '2026-03-09 18:07:55', 'Rajesh Kumar', 'CUST6977');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
