-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 27, 2025 at 05:14 PM
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
  `c_sem` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_course`
--

INSERT INTO `tb_course` (`c_code`, `c_name`, `c_credit`, `c_sem`) VALUES
('SECI1143', 'Probability & Statistical Data', 3, '2023/2024-2'),
('SECJ1023', 'Programming Technique 2', 3, '2023/2024-2'),
('SECJ1033', 'Programming Technique 1', 3, '2023/2024-1'),
('SECJ2013', 'Data Structure and Algorithm', 3, '2024/2025-1'),
('SECP2523', 'Database (WBL)', 3, '2024/2025-1'),
('SECP3204', 'Software Engineering (WBL)', 3, '2024/2025-1'),
('SECP3723', 'System Development Technology', 3, '2024/2025-1'),
('SECR1213', 'Network Communication', 3, '2024/2025-1'),
('ULRF2672', 'Archery', 2, '2024/2025-1');

-- --------------------------------------------------------

--
-- Table structure for table `tb_registration`
--

CREATE TABLE `tb_registration` (
  `r_tid` int(11) NOT NULL COMMENT 'This is the transaction ID',
  `r_student` varchar(10) NOT NULL,
  `r_course` varchar(10) NOT NULL,
  `r_section` int(11) NOT NULL,
  `r_sem` varchar(11) NOT NULL,
  `r_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_registration`
--

INSERT INTO `tb_registration` (`r_tid`, `r_student`, `r_course`, `r_section`, `r_sem`, `r_status`) VALUES
(2, 'S002', 'SECJ1033', 1, '2024/2025-1', 2),
(6, 'S001', 'SECJ2013', 2, '2024/2025-1', 2),
(7, 'S001', 'SECP2523', 3, '2024/2025-1', 2),
(8, 'S001', 'SECJ2013', 2, '2024/2025-1', 3),
(11, 'S003', 'SECP3723', 4, '2024/2025-1', 2),
(12, 'S003', 'SECP3204', 9, '2024/2025-1', 2),
(13, 'S003', 'SECP2523', 3, '2024/2025-1', 2),
(14, 'S003', 'SECJ2013', 2, '2024/2025-1', 2),
(15, 'S003', 'SECJ1033', 1, '2024/2025-1', 3),
(16, 'S003', 'ULRF2672', 12, '2024/2025-1', 2),
(17, 'S003', 'SECI1143', 15, '2023/2024-2', 2),
(18, 'S003', 'SECJ1023', 17, '2023/2024-2', 2);

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
-- Table structure for table `tb_section`
--

CREATE TABLE `tb_section` (
  `s_id` int(11) NOT NULL,
  `s_course_code` varchar(10) NOT NULL,
  `s_number` varchar(5) NOT NULL,
  `s_maxstudent` int(11) NOT NULL,
  `s_sem` varchar(20) NOT NULL,
  `s_lecturer` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_section`
--

INSERT INTO `tb_section` (`s_id`, `s_course_code`, `s_number`, `s_maxstudent`, `s_sem`, `s_lecturer`) VALUES
(1, 'SECJ1033', '01', 350, '2024/2025-1', 'L001'),
(2, 'SECJ2013', '01', 300, '2024/2025-1', 'L001'),
(3, 'SECP2523', '01', 60, '2024/2025-1', 'L002'),
(4, 'SECP3723', '01', 60, '2024/2025-1', 'L002'),
(8, 'SECP3204', '01', 30, '2024/2025-1', 'L004'),
(9, 'SECP3204', '02', 30, '2024/2025-1', 'L005'),
(10, 'SECR1213', '01', 25, '2024/2025-1', 'L006'),
(11, 'SECR1213', '02', 28, '2024/2025-1', 'L005'),
(12, 'ULRF2672', '01', 23, '2024/2025-1', 'L001'),
(13, 'ULRF2672', '02', 28, '2024/2025-1', 'L002'),
(14, 'SECI1143', '01', 28, '2023/2024-2', 'L004'),
(15, 'SECI1143', '02', 30, '2023/2024-2', 'L005'),
(16, 'SECJ1023', '01', 28, '2023/2024-2', 'L006'),
(17, 'SECJ1023', '02', 30, '2023/2024-2', 'L001');

-- --------------------------------------------------------

--
-- Table structure for table `tb_settings`
--

CREATE TABLE `tb_settings` (
  `id` int(11) NOT NULL,
  `active_semester` varchar(20) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_settings`
--

INSERT INTO `tb_settings` (`id`, `active_semester`, `updated_at`) VALUES
(1, '2024/2025-1', '2025-01-23 17:38:52'),
(2, '2023/2024-2', '2025-01-27 16:00:48');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `u_sno` varchar(10) NOT NULL,
  `u_pwd` varchar(255) DEFAULT NULL,
  `u_name` varchar(50) NOT NULL,
  `u_contact` varchar(20) DEFAULT NULL,
  `u_state` varchar(20) NOT NULL,
  `u_req` timestamp NULL DEFAULT NULL,
  `u_email` varchar(30) NOT NULL,
  `u_utype` int(11) NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`u_sno`, `u_pwd`, `u_name`, `u_contact`, `u_state`, `u_req`, `u_email`, `u_utype`, `session_id`, `last_activity`) VALUES
('111', '123456', 'Haza', '11', 'Johor', NULL, '111@gmail.com', 3, '3c1agudqgdk86egfodj80bm6lj', '2025-01-27 15:31:45'),
('A001', '$2y$10$SWnJAmk6MImdTUsKqItSI.N43RC6ulqMgADRIdTcFNG7LL2rkhmrO', '001 Admin Name', '01112345678', 'W.P. Kuala Lumpur', '2025-01-27 07:08:46', '001@admin.com', 3, '3c1agudqgdk86egfodj80bm6lj', '2025-01-27 23:55:17'),
('L001', '123456', 'Aina Abdul', '191111111', 'Johor', '2023-01-31 16:00:00', 'aina@gmail.com', 1, '3c1agudqgdk86egfodj80bm6lj', '2025-01-27 15:17:27'),
('L002', '123456', 'Fazura Abdul', '172222222', 'Kelantan', '2023-09-30 16:00:00', 'faz@gmail.com', 1, NULL, NULL),
('L004', '004Lecturer', '004 Lec Name', '0121234567', 'Johor', '2025-01-27 04:55:26', '004@gmail.com', 1, '3c1agudqgdk86egfodj80bm6lj', '2025-01-27 05:58:17'),
('L005', '005Lecturer', '005 Lec Name', '0151234567', 'Sarawak', '2025-01-27 05:24:45', '005@gmail.com', 1, '3c1agudqgdk86egfodj80bm6lj', '2025-01-28 00:13:36'),
('L006', '$2y$10$EGm5qfiC5V7vepNcLApYBuBEAaUvDfajDDoBnwZQDgyxJdoQaiQ..', '006 Lec Name', '0161234567', 'Sabah', '2025-01-27 05:34:19', '006@gmail.com', 1, '3c1agudqgdk86egfodj80bm6lj', '2025-01-27 15:30:30'),
('S001', '123456', 'Fatah Abdul', '0151234567', 'Melaka', '2024-01-31 16:00:00', 'fat@gmail.com', 2, '3c1agudqgdk86egfodj80bm6lj', '2025-01-28 00:01:01'),
('S002', '123456', 'Kam Abdul', '124444444', 'Selangor', '2024-09-30 16:00:00', 'kam@gmail.com', 2, '3c1agudqgdk86egfodj80bm6lj', '2025-01-27 23:55:01'),
('S003', '$2y$10$zp1u5Pt4u5z5q3uMKkw0DOfomVdyVIxY0caRFnOky0vBPZps4kZda', 'WOO MAO', '011-70740302', 'Selangor', '2025-01-27 08:34:23', 'wooshuan@graduate.utm.my', 2, '3c1agudqgdk86egfodj80bm6lj', '2025-01-28 00:01:24');

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
(3, 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_course`
--
ALTER TABLE `tb_course`
  ADD PRIMARY KEY (`c_code`);

--
-- Indexes for table `tb_registration`
--
ALTER TABLE `tb_registration`
  ADD PRIMARY KEY (`r_tid`),
  ADD KEY `r_student` (`r_student`),
  ADD KEY `r_status` (`r_status`),
  ADD KEY `r_course` (`r_course`),
  ADD KEY `r_section` (`r_section`);

--
-- Indexes for table `tb_regstatus`
--
ALTER TABLE `tb_regstatus`
  ADD PRIMARY KEY (`s_id`);

--
-- Indexes for table `tb_section`
--
ALTER TABLE `tb_section`
  ADD PRIMARY KEY (`s_id`),
  ADD KEY `s_course_code` (`s_course_code`),
  ADD KEY `s_lecturer` (`s_lecturer`);

--
-- Indexes for table `tb_settings`
--
ALTER TABLE `tb_settings`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `r_tid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'This is the transaction ID', AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tb_section`
--
ALTER TABLE `tb_section`
  MODIFY `s_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tb_settings`
--
ALTER TABLE `tb_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_registration`
--
ALTER TABLE `tb_registration`
  ADD CONSTRAINT `tb_registration_ibfk_1` FOREIGN KEY (`r_student`) REFERENCES `tb_user` (`u_sno`),
  ADD CONSTRAINT `tb_registration_ibfk_2` FOREIGN KEY (`r_status`) REFERENCES `tb_regstatus` (`s_id`),
  ADD CONSTRAINT `tb_registration_ibfk_3` FOREIGN KEY (`r_course`) REFERENCES `tb_course` (`c_code`),
  ADD CONSTRAINT `tb_registration_ibfk_4` FOREIGN KEY (`r_section`) REFERENCES `tb_section` (`s_id`);

--
-- Constraints for table `tb_section`
--
ALTER TABLE `tb_section`
  ADD CONSTRAINT `tb_section_ibfk_1` FOREIGN KEY (`s_course_code`) REFERENCES `tb_course` (`c_code`),
  ADD CONSTRAINT `tb_section_ibfk_2` FOREIGN KEY (`s_lecturer`) REFERENCES `tb_user` (`u_sno`);

--
-- Constraints for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD CONSTRAINT `tb_user_ibfk_1` FOREIGN KEY (`u_utype`) REFERENCES `tb_utype` (`t_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
