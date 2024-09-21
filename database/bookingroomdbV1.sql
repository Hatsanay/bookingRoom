-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 21, 2024 at 01:36 PM
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
-- Table structure for table `accessroom`
--

CREATE TABLE `accessroom` (
  `accessId` varchar(10) NOT NULL,
  `accessDate` datetime NOT NULL,
  `access_empId` varchar(10) NOT NULL,
  `access_reserveID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `building`
--

CREATE TABLE `building` (
  `BuiID` varchar(10) NOT NULL,
  `BuiName` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `depID` varchar(10) NOT NULL,
  `depName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`depID`, `depName`) VALUES
('DEP0000001', 'งานทะเบียน');

-- --------------------------------------------------------

--
-- Table structure for table `duration`
--

CREATE TABLE `duration` (
  `durationID` varchar(10) NOT NULL,
  `DurationStartTime` datetime NOT NULL,
  `DurationEndTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`empID`, `empFname`, `empLname`, `empUserName`, `empPassword`, `empGender`, `empBdate`, `empPhone`, `empCountLock`, `emp_depID`, `emp_roleID`, `emp_stalD`) VALUES
('EMP0000001', 'หัสนัย', 'หม้อยา', 'admin', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'M', '2014-07-02', '0980300822', 0, 'DEP0000001', 'ROL0000001', 'STA0000003'),
('EMP0000002', 'ภูธเนศ', 'รำเภย', 'user1', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'F', '2014-07-02', '0980965844', 0, 'DEP0000001', 'ROL0000002', 'STA0000003');

-- --------------------------------------------------------

--
-- Table structure for table `floor`
--

CREATE TABLE `floor` (
  `floorID` varchar(10) NOT NULL,
  `floorName` varchar(2) NOT NULL,
  `BuiID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lock`
--

CREATE TABLE `lock` (
  `LockID` varchar(10) NOT NULL,
  `LockDate` datetime NOT NULL,
  `Lock_empID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reserveidtailcancel`
--

CREATE TABLE `reserveidtailcancel` (
  `Detail` text NOT NULL,
  `RDC.reserveID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reserveroom`
--

CREATE TABLE `reserveroom` (
  `reserveID` varchar(10) NOT NULL,
  `reservelWillDate` date NOT NULL,
  `reservelDetail` text NOT NULL,
  `reservelQrcode` varchar(255) NOT NULL,
  `reservel_empID` varchar(10) NOT NULL,
  `reservel_durationID` varchar(10) NOT NULL,
  `reservel_roomID` varchar(10) NOT NULL,
  `reservel_staID` varchar(10) NOT NULL,
  `reservel_BookingstatusID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `roleID` varchar(10) NOT NULL,
  `roleName` varchar(50) NOT NULL,
  `roleaccess` varchar(100) NOT NULL,
  `role_staID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`roleID`, `roleName`, `roleaccess`, `role_staID`) VALUES
('ROL0000001', 'ผู้ดูแลระบบ', '1111111', 'STA0000003'),
('ROL0000002', 'พนักงานทั่วไป', '1111000', 'STA0000003');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `roomID` varchar(10) NOT NULL,
  `roomName` varchar(20) NOT NULL,
  `roomCapacity` int(4) NOT NULL,
  `roomDetail` text NOT NULL,
  `room_floorID` varchar(10) NOT NULL,
  `room_roomtyptID` varchar(10) NOT NULL,
  `room_staID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roomtypt`
--

CREATE TABLE `roomtypt` (
  `roomtyptID` varchar(10) NOT NULL,
  `roomtypName` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `staID` varchar(10) NOT NULL,
  `staName` varchar(50) NOT NULL,
  `sta_statypID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`staID`, `staName`, `sta_statypID`) VALUES
('STA0000001', 'ปฏิบัติงาน', 'STT0000001'),
('STA0000002', 'สิ้นสุดการปฏิบัติงาน', 'STT0000001'),
('STA0000003', 'ใช้งาน', 'STT0000002'),
('STA0000004', 'ไม่ใช้งาน', 'STT0000002'),
('STA0000005', 'อนุมัติ', 'STT0000003'),
('STA0000006', 'ไม่อนุมัติ', 'STT0000003'),
('STA0000007', 'จอง', 'STT0000004'),
('STA0000008', 'ยกเลิกการจอง', 'STT0000004');

-- --------------------------------------------------------

--
-- Table structure for table `statustype`
--

CREATE TABLE `statustype` (
  `statypID` varchar(10) NOT NULL,
  `statypName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `statustype`
--

INSERT INTO `statustype` (`statypID`, `statypName`) VALUES
('STT0000001', 'ปฎิบัติงาน'),
('STT0000002', 'การใช้งาน'),
('STT0000003', 'การอนุมัติ'),
('STT0000004', 'การจอง');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accessroom`
--
ALTER TABLE `accessroom`
  ADD PRIMARY KEY (`accessId`),
  ADD KEY `Employee.empID` (`access_empId`),
  ADD KEY `ReserveID` (`access_reserveID`);

--
-- Indexes for table `building`
--
ALTER TABLE `building`
  ADD PRIMARY KEY (`BuiID`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`depID`);

--
-- Indexes for table `duration`
--
ALTER TABLE `duration`
  ADD PRIMARY KEY (`durationID`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`empID`),
  ADD KEY `Roles.roleID` (`emp_roleID`),
  ADD KEY `Department.depID` (`emp_depID`),
  ADD KEY `Status.staID` (`emp_stalD`);

--
-- Indexes for table `floor`
--
ALTER TABLE `floor`
  ADD PRIMARY KEY (`floorID`),
  ADD KEY `Builing.BuiID` (`BuiID`);

--
-- Indexes for table `lock`
--
ALTER TABLE `lock`
  ADD PRIMARY KEY (`LockID`);

--
-- Indexes for table `reserveidtailcancel`
--
ALTER TABLE `reserveidtailcancel`
  ADD PRIMARY KEY (`RDC.reserveID`);

--
-- Indexes for table `reserveroom`
--
ALTER TABLE `reserveroom`
  ADD PRIMARY KEY (`reserveID`),
  ADD KEY `Employee.durationID` (`reservel_empID`),
  ADD KEY `Duration.empID` (`reservel_durationID`),
  ADD KEY `Room.roomID` (`reservel_roomID`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`roleID`),
  ADD KEY `Status.stalD` (`role_staID`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`roomID`),
  ADD KEY `Floor.floorID` (`room_floorID`),
  ADD KEY `Roomtypt.roomtyptID` (`room_roomtyptID`);

--
-- Indexes for table `roomtypt`
--
ALTER TABLE `roomtypt`
  ADD PRIMARY KEY (`roomtyptID`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`staID`),
  ADD KEY `StatusType.statypID` (`sta_statypID`);

--
-- Indexes for table `statustype`
--
ALTER TABLE `statustype`
  ADD PRIMARY KEY (`statypID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accessroom`
--
ALTER TABLE `accessroom`
  ADD CONSTRAINT `Employee.empID` FOREIGN KEY (`access_empId`) REFERENCES `employee` (`empID`),
  ADD CONSTRAINT `ReserveID` FOREIGN KEY (`access_reserveID`) REFERENCES `reserveroom` (`reserveID`);

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `Department.depID` FOREIGN KEY (`emp_depID`) REFERENCES `department` (`depID`),
  ADD CONSTRAINT `Roles.roleID` FOREIGN KEY (`emp_roleID`) REFERENCES `role` (`roleID`),
  ADD CONSTRAINT `Status.staID` FOREIGN KEY (`emp_stalD`) REFERENCES `status` (`staID`);

--
-- Constraints for table `floor`
--
ALTER TABLE `floor`
  ADD CONSTRAINT `Builing.BuiID` FOREIGN KEY (`BuiID`) REFERENCES `building` (`BuiID`);

--
-- Constraints for table `reserveroom`
--
ALTER TABLE `reserveroom`
  ADD CONSTRAINT `Duration.empID` FOREIGN KEY (`reservel_durationID`) REFERENCES `employee` (`empID`),
  ADD CONSTRAINT `Employee.durationID` FOREIGN KEY (`reservel_empID`) REFERENCES `duration` (`durationID`),
  ADD CONSTRAINT `Room.roomID` FOREIGN KEY (`reservel_roomID`) REFERENCES `room` (`roomID`);

--
-- Constraints for table `role`
--
ALTER TABLE `role`
  ADD CONSTRAINT `Status.stalD` FOREIGN KEY (`role_staID`) REFERENCES `status` (`staID`);

--
-- Constraints for table `room`
--
ALTER TABLE `room`
  ADD CONSTRAINT `Floor.floorID` FOREIGN KEY (`room_floorID`) REFERENCES `floor` (`floorID`),
  ADD CONSTRAINT `Roomtypt.roomtyptID` FOREIGN KEY (`room_roomtyptID`) REFERENCES `roomtypt` (`roomtyptID`);

--
-- Constraints for table `status`
--
ALTER TABLE `status`
  ADD CONSTRAINT `StatusType.statypID` FOREIGN KEY (`sta_statypID`) REFERENCES `statustype` (`statypID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
