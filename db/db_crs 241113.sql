-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 13, 2024 at 06:03 AM
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
-- Database: `db_crs`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_course`
--

CREATE TABLE `tb_course` (
  `c_code` varchar(10) NOT NULL,
  `c_name` varchar(30) NOT NULL,
  `c_credit` int(11) NOT NULL,
  `c_lec` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_course`
--

INSERT INTO `tb_course` (`c_code`, `c_name`, `c_credit`, `c_lec`) VALUES
('SECJ1033', 'Programming Technique 1', 3, 'L002'),
('SECP3723', 'System Development Technology', 3, 'L001');

-- --------------------------------------------------------

--
-- Table structure for table `tb_registration`
--

CREATE TABLE `tb_registration` (
  `r_tid` int(11) NOT NULL COMMENT 'This is the transaction ID',
  `r_student` varchar(10) NOT NULL,
  `r_course` varchar(10) NOT NULL,
  `r_sem` varchar(11) NOT NULL,
  `r_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_registration`
--

INSERT INTO `tb_registration` (`r_tid`, `r_student`, `r_course`, `r_sem`, `r_status`) VALUES
(1, 'S001', 'SECP3723', '2024/2025-1', 1),
(2, 'S002', 'SECJ1033', '2024/2025-1', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tb_regstatus`
--

CREATE TABLE `tb_regstatus` (
  `s_id` int(11) NOT NULL,
  `s_desc` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_regstatus`
--

INSERT INTO `tb_regstatus` (`s_id`, `s_desc`) VALUES
(1, 'Received'),
(2, 'Approved'),
(3, 'Rejected');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `u_sno` varchar(10) NOT NULL,
  `u_pwd` varchar(20) NOT NULL,
  `u_name` varchar(50) NOT NULL,
  `u_contact` int(11) NOT NULL,
  `u_state` varchar(20) NOT NULL,
  `u_req` timestamp NULL DEFAULT NULL,
  `u_email` varchar(30) NOT NULL,
  `u_utype` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`u_sno`, `u_pwd`, `u_name`, `u_contact`, `u_state`, `u_req`, `u_email`, `u_utype`) VALUES
('111', '123456', 'Haza', 11, 'Johor', NULL, '111@gmail.com', 3),
('L001', '123456', 'Aina Abdul', 191111111, 'Johor', '2023-01-31 16:00:00', 'aina@gmail.com', 1),
('L002', '123456', 'Fazura Abdul', 172222222, 'Kelantan', '2023-09-30 16:00:00', 'faz@gmail.com', 1),
('S001', '123456', 'Fatah Abdul', 133333333, 'Melaka', '2024-01-31 16:00:00', 'fat@gmail.com', 2),
('S002', '123456', 'Kam Abdul', 124444444, 'Selangor', '2024-09-30 16:00:00', 'kam@gmail.com', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tb_utype`
--

CREATE TABLE `tb_utype` (
  `t_id` int(11) NOT NULL,
  `t_desc` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_utype`
--

INSERT INTO `tb_utype` (`t_id`, `t_desc`) VALUES
(1, 'Lecturer'),
(2, 'Student'),
(3, 'IT Staff');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_course`
--
ALTER TABLE `tb_course`
  ADD PRIMARY KEY (`c_code`),
  ADD KEY `c_lec` (`c_lec`);

--
-- Indexes for table `tb_registration`
--
ALTER TABLE `tb_registration`
  ADD PRIMARY KEY (`r_tid`),
  ADD KEY `r_student` (`r_student`),
  ADD KEY `r_status` (`r_status`),
  ADD KEY `r_course` (`r_course`);

--
-- Indexes for table `tb_regstatus`
--
ALTER TABLE `tb_regstatus`
  ADD PRIMARY KEY (`s_id`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`u_sno`),
  ADD KEY `u_utype` (`u_utype`);

--
-- Indexes for table `tb_utype`
--
ALTER TABLE `tb_utype`
  ADD PRIMARY KEY (`t_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_registration`
--
ALTER TABLE `tb_registration`
  MODIFY `r_tid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'This is the transaction ID', AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_course`
--
ALTER TABLE `tb_course`
  ADD CONSTRAINT `tb_course_ibfk_1` FOREIGN KEY (`c_lec`) REFERENCES `tb_user` (`u_sno`);

--
-- Constraints for table `tb_registration`
--
ALTER TABLE `tb_registration`
  ADD CONSTRAINT `tb_registration_ibfk_1` FOREIGN KEY (`r_student`) REFERENCES `tb_user` (`u_sno`),
  ADD CONSTRAINT `tb_registration_ibfk_2` FOREIGN KEY (`r_status`) REFERENCES `tb_regstatus` (`s_id`),
  ADD CONSTRAINT `tb_registration_ibfk_3` FOREIGN KEY (`r_course`) REFERENCES `tb_course` (`c_code`);

--
-- Constraints for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD CONSTRAINT `tb_user_ibfk_1` FOREIGN KEY (`u_utype`) REFERENCES `tb_utype` (`t_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
