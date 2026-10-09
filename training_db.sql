-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2026 at 01:18 PM
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
-- Database: `training_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `class_code` varchar(20) NOT NULL,
  `schedule` varchar(100) DEFAULT NULL,
  `instructor` varchar(100) DEFAULT NULL,
  `slots` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `course_id`, `class_code`, `schedule`, `instructor`, `slots`) VALUES
(1, 1, 'IPT101-SEC1', 'Mon/Wed 9:00 AM - 11:00 AM', 'Prof. John Doe', 19),
(2, 1, 'IPT101-SEC2', 'Tue/Thu 1:00 PM - 3:00 PM', 'Prof. Jane Smith', 0),
(3, 2, 'WEB201-SEC1', 'Fri 8:00 AM - 12:00 PM', 'Prof. Alex Rivera', 15),
(4, 1, 'WD-SEC-A', 'Mon/Wed 9:00 AM - 12:00 PM', 'Prof. John Doe', 15),
(5, 1, 'WD-SEC-B', 'Tue/Thu 1:00 PM - 4:00 PM', 'Prof. Jane Smith', 10),
(6, 2, 'DB-SEC-A', 'Mon/Wed 1:00 PM - 4:00 PM', 'Dr. Alan Turing', 20),
(7, 3, 'PHP-SEC-A', 'Friday 8:00 AM - 5:00 PM', 'Prof. Grace Hopper', 5),
(8, 6, 'PDC10-3A', 'Tue/Thu 9am-11am', 'Dr. Adriane Brent Castro', 67);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_code`, `course_name`, `description`) VALUES
(1, 'IPT101', 'Integrative Programming and Technology', 'Advanced multi-table architecture and PDO integration.'),
(2, 'WEB201', 'Web Development Fundamentals', 'Client and server-side web application development.'),
(3, 'CS101', 'Web Development Fundamentals', 'Introduction to HTML, CSS, JavaScript, and PHP development.'),
(4, 'CS102', 'Database Management Systems', 'Relational database design, SQL queries, and PDO integration.'),
(5, 'CS103', 'Advanced PHP Architecture', 'Object-oriented programming, design patterns, and MVC architectures.'),
(6, 'PDC10', 'Prompt Engineering', 'Learn about Prompt Engineering and Agentic Workflows');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `enrollment_date`, `status`) VALUES
(1, 1, 2, '2026-10-09 10:58:06', 'cancelled'),
(2, 1, 2, '2026-10-09 11:06:22', 'active'),
(3, 2, 1, '2026-10-09 11:07:05', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `full_name`, `email`, `phone`, `created_at`) VALUES
(1, 'Riejed Aniko L. Macatula', 'macatula.riejedaniko@student.auf.edu.ph', '09123456789', '2026-10-09 10:58:06'),
(2, 'Student 2', 'sample@email.edu.ph', '09123456789', '2026-10-09 11:07:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD UNIQUE KEY `course_code` (`course_code`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
