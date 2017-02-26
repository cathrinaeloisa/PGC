-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 22, 2017 at 05:24 AM
-- Server version: 10.1.19-MariaDB
-- PHP Version: 5.6.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pentagas`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customerID` varchar(9) NOT NULL,
  `customerType` varchar(15) NOT NULL,
  `name` varchar(45) NOT NULL,
  `deliveryAddress` varchar(65) NOT NULL,
  `contactNum` int(11) NOT NULL,
  `emailAddress` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customerID`, `customerType`, `name`, `deliveryAddress`, `contactNum`, `emailAddress`) VALUES
('100-00001', 'Medical', 'The Medical City', 'Ortigas Avenue, Pasig City, Metro Manila', 9881000, 'mail@themedicalcity.com');

-- --------------------------------------------------------

--
-- Table structure for table `cylinderrefillaudit`
--

CREATE TABLE `cylinderrefillaudit` (
  `refillAuditID` varchar(10) CHARACTER SET latin1 NOT NULL,
  `cylinderID` varchar(7) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cylinderrequest`
--

CREATE TABLE `cylinderrequest` (
  `requestID` int(5) NOT NULL,
  `orderID` varchar(11) NOT NULL,
  `gasID` varchar(6) NOT NULL,
  `quantity` int(3) NOT NULL,
  `isCompleted` tinyint(4) NOT NULL,
  `requestedBy` int(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cylinders`
--

CREATE TABLE `cylinders` (
  `cylinderID` varchar(7) NOT NULL,
  `gasID` varchar(6) NOT NULL,
  `cylinderStatusID` int(3) NOT NULL,
  `dateAcquired` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cylinders`
--

INSERT INTO `cylinders` (`cylinderID`, `gasID`, `cylinderStatusID`, `dateAcquired`) VALUES
('0001-44', '100-02', 402, '2017-01-31'),
('0002-44', '100-04', 401, '2017-02-06'),
('0003-44', '100-04', 401, '2017-02-06'),
('0004-44', '100-04', 401, '2017-02-06'),
('0005-44', '100-04', 401, '2017-02-06'),
('0006-44', '100-04', 401, '2017-02-06'),
('0007-44', '100-04', 401, '2017-02-06'),
('0008-44', '100-04', 401, '2017-02-06'),
('0009-44', '100-04', 406, '2017-02-06'),
('0010-44', '100-04', 401, '2017-02-06'),
('0011-44', '100-04', 402, '2017-02-06'),
('0012-44', '200-01', 409, '2017-02-09'),
('0013-44', '200-01', 409, '2017-02-09'),
('0014-44', '200-01', 409, '2017-02-09'),
('0015-44', '200-01', 402, '2017-02-09'),
('0016-44', '200-01', 402, '2017-02-09'),
('0017-44', '200-01', 402, '2017-02-09'),
('0018-44', '200-01', 409, '2017-02-09'),
('0019-44', '200-01', 409, '2017-02-09'),
('0020-44', '200-01', 409, '2017-02-09'),
('0021-44', '200-01', 409, '2017-02-09'),
('0022-44', '200-02', 401, '2017-02-09'),
('0023-44', '200-02', 401, '2017-02-09'),
('0024-44', '200-02', 401, '2017-02-09'),
('0025-44', '200-02', 401, '2017-02-09'),
('0026-44', '200-02', 401, '2017-02-09'),
('0027-44', '200-02', 401, '2017-02-09'),
('0028-44', '200-02', 401, '2017-02-09'),
('0029-44', '200-02', 406, '2017-02-09'),
('0030-44', '200-02', 403, '2017-02-09'),
('0031-44', '200-02', 402, '2017-02-09');

-- --------------------------------------------------------

--
-- Table structure for table `cylinderstatus`
--

CREATE TABLE `cylinderstatus` (
  `cylinderStatusID` int(3) NOT NULL,
  `cylinderStatusDescription` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cylinderstatus`
--

INSERT INTO `cylinderstatus` (`cylinderStatusID`, `cylinderStatusDescription`) VALUES
(401, 'Available'),
(402, 'Empty'),
(403, 'Damaged'),
(404, 'In Repair'),
(405, 'Repaired'),
(406, 'Dispatched'),
(407, 'No Longer In Use'),
(408, 'Lost'),
(409, 'Reserved');

-- --------------------------------------------------------

--
-- Table structure for table `deliverydetails`
--

CREATE TABLE `deliverydetails` (
  `deliveryDetailsID` varchar(16) NOT NULL,
  `orderDetailsID` varchar(13) NOT NULL,
  `cylinderID` varchar(7) DEFAULT NULL,
  `deliveryDate` date NOT NULL,
  `pickedupdate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `gaspricingaudit`
--

CREATE TABLE `gaspricingaudit` (
  `auditID` int(7) NOT NULL,
  `gasID` varchar(6) NOT NULL,
  `price` decimal(7,2) NOT NULL,
  `auditDate` date NOT NULL,
  `remarks` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gaspricingaudit`
--

INSERT INTO `gaspricingaudit` (`auditID`, `gasID`, `price`, `auditDate`, `remarks`) VALUES
(7000001, '100-01', '100.00', '2017-01-31', NULL),
(7000002, '200-01', '200.00', '2017-01-31', NULL),
(7000003, '100-02', '1000.00', '2017-01-31', NULL),
(7000004, '100-01', '300.00', '2017-01-31', NULL),
(7000006, '200-01', '500.00', '2017-02-03', NULL),
(7000007, '100-01', '1000.00', '2017-02-04', NULL),
(7000008, '200-01', '5000.00', '2017-02-04', NULL),
(7000009, '100-01', '2000.00', '2017-02-04', NULL),
(7000010, '100-02', '15000.00', '2017-02-04', NULL),
(7000011, '200-01', '5000.00', '2017-02-06', NULL),
(7000012, '200-01', '5000.00', '2017-02-06', NULL),
(7000013, '200-01', '1200.00', '2017-02-06', NULL),
(7000014, '200-01', '1200.00', '2017-02-06', NULL),
(7000015, '200-01', '1200.00', '2017-02-06', NULL),
(7000016, '200-02', '1200.00', '2017-02-06', NULL),
(7000017, '100-03', '3000.00', '2017-02-06', NULL),
(7000018, '100-04', '5000.00', '2017-02-06', NULL),
(7000019, '100-01', '120.00', '2017-02-16', 'Price change'),
(7000020, '100-04', '200.00', '2017-02-16', 'Price remarks'),
(7000021, '100-02', '200.00', '2017-02-16', 'Demand'),
(7000022, '100-03', '100.00', '2017-02-16', 'Demand'),
(7000023, '200-01', '200.00', '2017-02-16', 'Demand'),
(7000024, '100-03', '1000.00', '2017-02-16', 'New price'),
(7000025, '200-01', '3000.00', '2017-02-16', 'New price'),
(7000026, '100-03', '10000.00', '2017-02-16', 'New price\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `gastype`
--

CREATE TABLE `gastype` (
  `gasID` varchar(6) NOT NULL,
  `gasName` varchar(45) NOT NULL,
  `gasType` varchar(15) NOT NULL,
  `isSpecialGas` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gastype`
--

INSERT INTO `gastype` (`gasID`, `gasName`, `gasType`, `isSpecialGas`) VALUES
('100-01', 'Oxygen', 'Medical', 0),
('100-02', 'Carbon Dioxide', 'Medical', 1),
('100-03', 'Carbon Monoxide', 'Medical', 1),
('100-04', 'Nitrous', 'Medical', 1),
('200-01', 'Oxygen', 'Technical', 0),
('200-02', 'Carbon Monoxide', 'Technical', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
--

CREATE TABLE `orderdetails` (
  `orderDetailsID` varchar(13) NOT NULL,
  `gasID` varchar(6) NOT NULL,
  `orderID` varchar(11) NOT NULL,
  `quantity` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `orderID` varchar(11) NOT NULL,
  `userID` int(7) NOT NULL,
  `orderStatusID` int(3) NOT NULL,
  `customerID` varchar(9) NOT NULL,
  `orderDate` date DEFAULT NULL,
  `contactPerson` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `orderstatus`
--

CREATE TABLE `orderstatus` (
  `orderStatusID` int(3) NOT NULL,
  `orderStatusDescription` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orderstatus`
--

INSERT INTO `orderstatus` (`orderStatusID`, `orderStatusDescription`) VALUES
(801, 'Completed'),
(802, 'Processing Orders'),
(803, 'Cancelled'),
(804, 'Pending'),
(805, 'Waiting for Dispatching');

-- --------------------------------------------------------

--
-- Table structure for table `useraccounts`
--

CREATE TABLE `useraccounts` (
  `userID` int(7) NOT NULL,
  `userTypeID` int(3) NOT NULL,
  `username` varchar(45) NOT NULL,
  `name` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `useraccounts`
--

INSERT INTO `useraccounts` (`userID`, `userTypeID`, `username`, `name`, `password`) VALUES
(6000001, 101, 'miggysongco', 'Geraldine Songco', '12345'),
(6000002, 102, 'cathytan', 'Cathrina Eloisa', '*0B6C60C00F4778D0C1A6E08CFDC98890F48BC69F'),
(6000003, 103, 'johnpatrick', 'Patrick Pineda', '*00A51F3F48415C7D4E8908980D443C29C69B60C9'),
(6000004, 104, 'hannieking', 'Hannie King', '*00A51F3F48415C7D4E8908980D443C29C69B60C9'),
(6000005, 105, 'yanimagtoto', 'Yani Magtoto', '*00A51F3F48415C7D4E8908980D443C29C69B60C9'),
(6000013, 101, 'iketan', 'Ike', '*00A51F3F48415C7D4E8908980D443C29C69B60C9'),
(6000015, 103, 'patrickgan', 'Patrick Gan', '*00A51F3F48415C7D4E8908980D443C29C69B60C9'),
(6000016, 101, 'cathtan', 'Catherine Tan', '*00A51F3F48415C7D4E8908980D443C29C69B60C9'),
(6000017, 103, 'billclark', 'Bill Clark', '*00A51F3F48415C7D4E8908980D443C29C69B60C9'),
(6000019, 106, 'mariamiggy', 'Geraldine Songco', '*00A51F3F48415C7D4E8908980D443C29C69B60C9');

-- --------------------------------------------------------

--
-- Table structure for table `usertypes`
--

CREATE TABLE `usertypes` (
  `userTypeID` int(3) NOT NULL,
  `userTypeDescription` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `usertypes`
--

INSERT INTO `usertypes` (`userTypeID`, `userTypeDescription`) VALUES
(101, 'Administrative Manager'),
(102, 'Sales and Marketing Manager'),
(103, 'Billing Clerk'),
(104, 'Cylinder Control Clerk'),
(105, 'Dispatcher'),
(106, 'Production Manager');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customerID`);

--
-- Indexes for table `cylinderrefillaudit`
--
ALTER TABLE `cylinderrefillaudit`
  ADD PRIMARY KEY (`refillAuditID`),
  ADD KEY `cylinderID` (`cylinderID`);

--
-- Indexes for table `cylinderrequest`
--
ALTER TABLE `cylinderrequest`
  ADD PRIMARY KEY (`requestID`),
  ADD KEY `fk_cylinderrequest01_idx` (`orderID`),
  ADD KEY `fk_cylinderrequest02_idx` (`gasID`),
  ADD KEY `fk_cylinderrequest03_idx` (`requestedBy`);

--
-- Indexes for table `cylinders`
--
ALTER TABLE `cylinders`
  ADD PRIMARY KEY (`cylinderID`),
  ADD KEY `gasTypeID` (`gasID`),
  ADD KEY `cylinderStatusID` (`cylinderStatusID`);

--
-- Indexes for table `cylinderstatus`
--
ALTER TABLE `cylinderstatus`
  ADD PRIMARY KEY (`cylinderStatusID`);

--
-- Indexes for table `deliverydetails`
--
ALTER TABLE `deliverydetails`
  ADD PRIMARY KEY (`deliveryDetailsID`),
  ADD KEY `cylinderID` (`cylinderID`),
  ADD KEY `orderDetailsID` (`orderDetailsID`);

--
-- Indexes for table `gaspricingaudit`
--
ALTER TABLE `gaspricingaudit`
  ADD PRIMARY KEY (`auditID`),
  ADD KEY `gasTypeID` (`gasID`),
  ADD KEY `gasTypeID_2` (`gasID`);

--
-- Indexes for table `gastype`
--
ALTER TABLE `gastype`
  ADD PRIMARY KEY (`gasID`);

--
-- Indexes for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD PRIMARY KEY (`orderDetailsID`),
  ADD KEY `cylinderID` (`gasID`),
  ADD KEY `orderID` (`orderID`),
  ADD KEY `orderID_2` (`orderID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orderID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `orderStatusID` (`orderStatusID`),
  ADD KEY `customerID` (`customerID`);

--
-- Indexes for table `orderstatus`
--
ALTER TABLE `orderstatus`
  ADD PRIMARY KEY (`orderStatusID`);

--
-- Indexes for table `useraccounts`
--
ALTER TABLE `useraccounts`
  ADD PRIMARY KEY (`userID`),
  ADD KEY `userTypeID` (`userTypeID`);

--
-- Indexes for table `usertypes`
--
ALTER TABLE `usertypes`
  ADD PRIMARY KEY (`userTypeID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gaspricingaudit`
--
ALTER TABLE `gaspricingaudit`
  MODIFY `auditID` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7000027;
--
-- AUTO_INCREMENT for table `useraccounts`
--
ALTER TABLE `useraccounts`
  MODIFY `userID` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6000020;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `cylinderrefillaudit`
--
ALTER TABLE `cylinderrefillaudit`
  ADD CONSTRAINT `fk_cylinders02` FOREIGN KEY (`cylinderID`) REFERENCES `cylinders` (`cylinderID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `cylinderrequest`
--
ALTER TABLE `cylinderrequest`
  ADD CONSTRAINT `fk_cylinderrequest01` FOREIGN KEY (`orderID`) REFERENCES `orders` (`orderID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_cylinderrequest02` FOREIGN KEY (`gasID`) REFERENCES `gastype` (`gasID`),
  ADD CONSTRAINT `fk_cylinderrequest03` FOREIGN KEY (`requestedBy`) REFERENCES `orders` (`userID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `cylinders`
--
ALTER TABLE `cylinders`
  ADD CONSTRAINT `fk_cylinderstatus01` FOREIGN KEY (`cylinderStatusID`) REFERENCES `cylinderstatus` (`cylinderStatusID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_gastype01` FOREIGN KEY (`gasID`) REFERENCES `gastype` (`gasID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `deliverydetails`
--
ALTER TABLE `deliverydetails`
  ADD CONSTRAINT `fk_cylinders03` FOREIGN KEY (`cylinderID`) REFERENCES `cylinders` (`cylinderID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orderDetails01` FOREIGN KEY (`orderDetailsID`) REFERENCES `orderdetails` (`orderDetailsID`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `gaspricingaudit`
--
ALTER TABLE `gaspricingaudit`
  ADD CONSTRAINT `fk_gastype02` FOREIGN KEY (`gasID`) REFERENCES `gastype` (`gasID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD CONSTRAINT `fk_gastype03` FOREIGN KEY (`gasID`) REFERENCES `gastype` (`gasID`),
  ADD CONSTRAINT `fk_orders01` FOREIGN KEY (`orderID`) REFERENCES `orders` (`orderID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_customers01` FOREIGN KEY (`customerID`) REFERENCES `customers` (`customerID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_employee01` FOREIGN KEY (`userID`) REFERENCES `useraccounts` (`userID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_orderStatus01` FOREIGN KEY (`orderStatusID`) REFERENCES `orderstatus` (`orderStatusID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `useraccounts`
--
ALTER TABLE `useraccounts`
  ADD CONSTRAINT `fk_usertypes01` FOREIGN KEY (`userTypeID`) REFERENCES `usertypes` (`userTypeID`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
