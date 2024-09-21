-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 21, 2024 at 06:34 AM
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
-- Database: `bookingroomdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `empID` varchar(10) NOT NULL,
  `empFname` varchar(50) NOT NULL,
  `empLname` varchar(50) NOT NULL,
  `empUserName` varchar(50) NOT NULL,
  `empPassword` varchar(50) NOT NULL,
  `empGender` varchar(1) NOT NULL,
  `empBdate` date NOT NULL,
  `empPhone` varchar(10) NOT NULL,
  `empCountLock` int(1) NOT NULL,
  `emp_depID` varchar(10) NOT NULL,
  `emp_roleID` varchar(10) NOT NULL,
  `emp_stalD` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`empID`),
  ADD KEY `Roles.roleID` (`emp_roleID`),
  ADD KEY `Department.depID` (`emp_depID`),
  ADD KEY `Status.staID` (`emp_stalD`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `Department.depID` FOREIGN KEY (`emp_depID`) REFERENCES `department` (`depID`),
  ADD CONSTRAINT `Roles.roleID` FOREIGN KEY (`emp_roleID`) REFERENCES `role` (`roleID`),
  ADD CONSTRAINT `Status.staID` FOREIGN KEY (`emp_stalD`) REFERENCES `status` (`staID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
