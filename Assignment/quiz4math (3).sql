-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 14, 2024 at 01:58 PM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quiz4math`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievement`
--

DROP TABLE IF EXISTS `achievement`;
CREATE TABLE IF NOT EXISTS `achievement` (
  `achievement_id` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL,
  `display` tinyint(1) NOT NULL,
  PRIMARY KEY (`achievement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `achievement`
--

INSERT INTO `achievement` (`achievement_id`, `name`, `description`, `display`) VALUES
('ach_1', 'Nice try', 'With trial and error, you will succeed.', 1),
('ach_2', 'Gifted', 'Dont be restless', 1),
('ach_3', 'First Try', 'Congrats', 1);

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `admin_id` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `email` varchar(50) NOT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `password`, `email`) VALUES
('admin_1', 'admin1', 'admin1', 'admin1');

-- --------------------------------------------------------

--
-- Table structure for table `instructor`
--

DROP TABLE IF EXISTS `instructor`;
CREATE TABLE IF NOT EXISTS `instructor` (
  `instructor_id` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `level` int UNSIGNED NOT NULL DEFAULT '1',
  `balance` int UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`instructor_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `instructor`
--

INSERT INTO `instructor` (`instructor_id`, `name`, `email`, `password`, `level`, `balance`) VALUES
('I_1', 'Irvin', 'irvin@mail.com', 'irvin', 1, 400);

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

DROP TABLE IF EXISTS `question`;
CREATE TABLE IF NOT EXISTS `question` (
  `question_id` varchar(100) NOT NULL,
  `question` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `option_a` varchar(50) NOT NULL,
  `option_b` varchar(50) NOT NULL,
  `option_c` varchar(50) NOT NULL,
  `option_d` varchar(50) NOT NULL,
  `correct_answer` varchar(50) NOT NULL,
  `quiz_id` varchar(100) NOT NULL,
  PRIMARY KEY (`question_id`),
  KEY `quiz_id` (`quiz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

DROP TABLE IF EXISTS `quiz`;
CREATE TABLE IF NOT EXISTS `quiz` (
  `quiz_id` varchar(100) NOT NULL,
  `title` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `admin_id` varchar(100) DEFAULT NULL,
  `instructor_id` varchar(100) DEFAULT NULL,
  `role` varchar(20) NOT NULL,
  `currency` int NOT NULL,
  `question` varchar(50) NOT NULL,
  PRIMARY KEY (`quiz_id`),
  KEY `admin_id` (`admin_id`),
  KEY `instructor_id` (`instructor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`quiz_id`, `title`, `category`, `admin_id`, `instructor_id`, `role`, `currency`, `question`) VALUES
('Q_01', 'level2', 'Division', NULL, 'I_1', '', 400, '15'),
('Q_02', 'level 3', 'Linear', NULL, 'I_1', '', 400, '10'),
('Q_03', 'level 4', 'Multiply', NULL, 'I_1', '', 1000, '15'),
('Q_04', 'level 5', 'Quadratic', NULL, 'I_1', '', 400, '10'),
('Q_05', 'level 6', 'Multiply', NULL, 'I_1\r\n', '', 100, '10');

-- --------------------------------------------------------

--
-- Table structure for table `quizanswer`
--

DROP TABLE IF EXISTS `quizanswer`;
CREATE TABLE IF NOT EXISTS `quizanswer` (
  `answer_id` varchar(100) NOT NULL,
  `attempt_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `question_id` varchar(100) NOT NULL,
  `answer` varchar(10) NOT NULL,
  `is_correct` tinyint(1) NOT NULL,
  `test_id` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`answer_id`),
  KEY `attempt_id` (`attempt_id`),
  KEY `question_id` (`question_id`),
  KEY `test_id` (`test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizattempt`
--

DROP TABLE IF EXISTS `quizattempt`;
CREATE TABLE IF NOT EXISTS `quizattempt` (
  `attempt_id` varchar(100) NOT NULL,
  `quiz_id` varchar(100) NOT NULL,
  `student_id` varchar(100) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `final_score` int UNSIGNED NOT NULL,
  PRIMARY KEY (`attempt_id`),
  KEY `quiz_id` (`quiz_id`),
  KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop`
--

DROP TABLE IF EXISTS `shop`;
CREATE TABLE IF NOT EXISTS `shop` (
  `item_id` varchar(100) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL,
  `price` int UNSIGNED NOT NULL,
  `category` varchar(20) NOT NULL,
  `display` tinyint(1) NOT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `shop`
--

INSERT INTO `shop` (`item_id`, `title`, `description`, `price`, `category`, `display`) VALUES
('item_1', 'Frost Theme', 'Add a frosty look to your theme!', 300, 'Theme', 1),
('item_2', 'Grass', 'Add a grassy look to your theme!', 600, 'Theme', 1),
('item_3', 'Midnight', 'Has a lot of purple and blue!', 700, 'Theme', 1);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `student_id` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `level` int UNSIGNED NOT NULL DEFAULT '1',
  `balance` int UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`student_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `name`, `email`, `password`, `level`, `balance`) VALUES
('S_1', 'Justin', 'justin@mail.com', 'justin', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `testattempt`
--

DROP TABLE IF EXISTS `testattempt`;
CREATE TABLE IF NOT EXISTS `testattempt` (
  `test_id` varchar(100) NOT NULL,
  `admin_id` varchar(100) DEFAULT NULL,
  `instructor_id` varchar(100) DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `final_score` int UNSIGNED NOT NULL,
  `quiz_id` varchar(100) NOT NULL,
  PRIMARY KEY (`test_id`),
  KEY `testattempt_ibfk_1` (`admin_id`),
  KEY `testattempt_ibfk_2` (`instructor_id`),
  KEY `testattempt_ibfk_3` (`quiz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `testattempt`
--

INSERT INTO `testattempt` (`test_id`, `admin_id`, `instructor_id`, `start_time`, `end_time`, `final_score`, `quiz_id`) VALUES
('T_01', NULL, 'I_1', '2024-12-14 11:36:30', '2024-12-14 11:36:39', 5, 'Q_02'),
('T_02', NULL, 'I_1', '2024-12-14 11:36:51', '2024-12-14 11:36:53', 5, 'Q_02'),
('T_03', NULL, 'I_1', '2024-12-14 11:38:21', '2024-12-14 11:38:24', 5, 'Q_02'),
('T_04', NULL, 'I_1', '2024-12-14 11:38:26', '2024-12-14 11:38:35', 3, 'Q_02');

-- --------------------------------------------------------

--
-- Table structure for table `title`
--

DROP TABLE IF EXISTS `title`;
CREATE TABLE IF NOT EXISTS `title` (
  `title_id` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL,
  `rarity` varchar(50) NOT NULL,
  `unlock_requirement` varchar(100) NOT NULL,
  `display` tinyint(1) NOT NULL,
  PRIMARY KEY (`title_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `title`
--

INSERT INTO `title` (`title_id`, `name`, `description`, `rarity`, `unlock_requirement`, `display`) VALUES
('title_1', 'Perfect', 'Amazing!', 'Rare', 'Answer all question correctly', 1),
('title_2', 'Professor', 'Brilliant!', 'Common', 'Created a quiz', 1);

-- --------------------------------------------------------

--
-- Table structure for table `userachievement`
--

DROP TABLE IF EXISTS `userachievement`;
CREATE TABLE IF NOT EXISTS `userachievement` (
  `userachievement_id` varchar(100) NOT NULL,
  `instructor_id` varchar(100) DEFAULT NULL,
  `student_id` varchar(100) DEFAULT NULL,
  `achievement_id` varchar(100) NOT NULL,
  PRIMARY KEY (`userachievement_id`),
  KEY `achievement_id` (`achievement_id`),
  KEY `instructor_id` (`instructor_id`),
  KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `useritem`
--

DROP TABLE IF EXISTS `useritem`;
CREATE TABLE IF NOT EXISTS `useritem` (
  `useritem_id` varchar(100) NOT NULL,
  `student_id` varchar(199) DEFAULT NULL,
  `instructor_id` varchar(100) DEFAULT NULL,
  `item_id` varchar(100) NOT NULL,
  `is_equipped` tinyint(1) NOT NULL,
  PRIMARY KEY (`useritem_id`),
  KEY `instructor_id` (`instructor_id`),
  KEY `student_id` (`student_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usertitle`
--

DROP TABLE IF EXISTS `usertitle`;
CREATE TABLE IF NOT EXISTS `usertitle` (
  `usertitle_id` varchar(100) NOT NULL,
  `student_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `instructor_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `title_id` varchar(100) NOT NULL,
  `is_equipped` tinyint(1) NOT NULL,
  PRIMARY KEY (`usertitle_id`),
  KEY `instructor_id` (`instructor_id`),
  KEY `student_id` (`student_id`),
  KEY `title_id` (`title_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `fk_quiz` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`quiz_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `question_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`quiz_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quiz`
--
ALTER TABLE `quiz`
  ADD CONSTRAINT `quiz_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quizanswer`
--
ALTER TABLE `quizanswer`
  ADD CONSTRAINT `quizanswer_ibfk_1` FOREIGN KEY (`attempt_id`) REFERENCES `quizattempt` (`attempt_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `quizanswer_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `question` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `quizanswer_ibfk_3` FOREIGN KEY (`test_id`) REFERENCES `testattempt` (`test_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quizattempt`
--
ALTER TABLE `quizattempt`
  ADD CONSTRAINT `quizattempt_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`quiz_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `quizattempt_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `testattempt`
--
ALTER TABLE `testattempt`
  ADD CONSTRAINT `testattempt_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `testattempt_ibfk_2` FOREIGN KEY (`instructor_id`) REFERENCES `instructor` (`instructor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `testattempt_ibfk_3` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`quiz_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `userachievement`
--
ALTER TABLE `userachievement`
  ADD CONSTRAINT `userachievement_ibfk_1` FOREIGN KEY (`achievement_id`) REFERENCES `achievement` (`achievement_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `userachievement_ibfk_2` FOREIGN KEY (`instructor_id`) REFERENCES `instructor` (`instructor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `userachievement_ibfk_3` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `useritem`
--
ALTER TABLE `useritem`
  ADD CONSTRAINT `useritem_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `instructor` (`instructor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `useritem_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `useritem_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `shop` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `usertitle`
--
ALTER TABLE `usertitle`
  ADD CONSTRAINT `usertitle_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `instructor` (`instructor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usertitle_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usertitle_ibfk_3` FOREIGN KEY (`title_id`) REFERENCES `title` (`title_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
