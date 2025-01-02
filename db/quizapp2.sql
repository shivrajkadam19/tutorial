-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2024 at 11:01 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quizapp2`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `AdminID` int(11) NOT NULL,
  `UserName` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`AdminID`, `UserName`, `Email`, `Password`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'admin1', 'admin1@test.com', 'password1', 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(2, 'admin2', 'admin2@test.com', 'password2', 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31');

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `CourseID` int(11) NOT NULL,
  `CourseName` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`CourseID`, `CourseName`, `Description`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'Mathematics', 'Advanced Math course', 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(2, 'Science', 'General science curriculum', 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(3, 'History', 'World history lessons', 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(4, 'Computer Science', 'Basics of CS', 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(5, 'Literature', 'English and world literature', 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31');

--
-- Triggers `course`
--
DELIMITER $$
CREATE TRIGGER `before_course_inactivation` BEFORE UPDATE ON `course` FOR EACH ROW BEGIN
  IF NEW.IsActive = 0 THEN
    UPDATE `subject` SET `IsActive` = 0 WHERE `CourseID` = NEW.CourseID;
    UPDATE `topic` SET `IsActive` = 0 WHERE `SubjectID` IN (SELECT `SubjectID` FROM `subject` WHERE `CourseID` = NEW.CourseID);
    UPDATE `quiz` SET `IsActive` = 0 WHERE `TopicID` IN (SELECT `TopicID` FROM `topic` WHERE `SubjectID` IN (SELECT `SubjectID` FROM `subject` WHERE `CourseID` = NEW.CourseID));
    UPDATE `question` SET `IsActive` = 0 WHERE `QuizID` IN (SELECT `QuizID` FROM `quiz` WHERE `TopicID` IN (SELECT `TopicID` FROM `topic` WHERE `SubjectID` IN (SELECT `SubjectID` FROM `subject` WHERE `CourseID` = NEW.CourseID)));
    UPDATE `options` SET `IsActive` = 0 WHERE `QuestionID` IN (SELECT `QuestionID` FROM `question` WHERE `QuizID` IN (SELECT `QuizID` FROM `quiz` WHERE `TopicID` IN (SELECT `TopicID` FROM `topic` WHERE `SubjectID` IN (SELECT `SubjectID` FROM `subject` WHERE `CourseID` = NEW.CourseID))));
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `OptionID` int(11) NOT NULL,
  `QuestionID` int(11) NOT NULL,
  `OptionText_EN` text NOT NULL,
  `OptionText_HI` text NOT NULL,
  `IsCorrect` tinyint(1) NOT NULL DEFAULT 0,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`OptionID`, `QuestionID`, `OptionText_EN`, `OptionText_HI`, `IsCorrect`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 1, '2', '2', 1, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(2, 1, '3', '3', 0, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(3, 2, 'x=4', 'x=4', 1, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(4, 2, 'x=3', 'x=3', 0, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(5, 3, 'Law of Inertia', 'जड़त्व का नियम', 1, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(6, 3, 'Law of Gravity', 'गुरुत्वाकर्षण का नियम', 0, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(7, 4, 'Resistance to motion', 'गति का प्रतिरोध', 1, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(8, 4, 'Gravitational pull', 'गुरुत्वाकर्षण खिंचाव', 0, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(9, 5, '1939', '1939', 1, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(10, 5, '1945', '1945', 0, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(11, 6, 'Winston Churchill', 'विंस्टन चर्चिल', 1, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(12, 6, 'Franklin D. Roosevelt', 'फ्रैंकलिन डी. रूजवेल्ट', 0, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(13, 7, 'Named memory location', 'नामित मेमोरी स्थान', 1, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(14, 7, 'A function', 'एक फंक्शन', 0, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(15, 8, 'Whole numbers', 'पूर्णांक', 1, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(16, 8, 'Characters', 'अक्षर', 0, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(17, 9, 'William Shakespeare', 'विलियम शेक्सपीयर', 1, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(18, 9, 'John Milton', 'जॉन मिल्टन', 0, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(19, 10, 'Two-line stanza', 'दो पंक्तियों की कविता', 1, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(20, 10, 'Four-line stanza', 'चार पंक्तियों की कविता', 0, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31');

-- --------------------------------------------------------

--
-- Table structure for table `options_test`
--

CREATE TABLE `options_test` (
  `OptionID` int(11) NOT NULL,
  `QuestionID` int(11) NOT NULL,
  `OptionText_EN` text NOT NULL,
  `OptionText_HI` text NOT NULL,
  `IsCorrect` tinyint(1) NOT NULL DEFAULT 0,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `options_test`
--

INSERT INTO `options_test` (`OptionID`, `QuestionID`, `OptionText_EN`, `OptionText_HI`, `IsCorrect`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 1, 'πr²', 'πr²', 1, 1, '2024-12-17 09:02:31', '2024-12-17 09:02:31'),
(2, 1, '2πr', '2πr', 0, 1, '2024-12-17 09:02:31', '2024-12-17 09:02:31'),
(3, 2, 'πr²h', 'πr²h', 1, 1, '2024-12-17 09:02:31', '2024-12-17 09:02:31'),
(4, 2, '2πrh', '2πrh', 0, 1, '2024-12-17 09:02:31', '2024-12-17 09:02:31');

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE `question` (
  `QuestionID` int(11) NOT NULL,
  `QuizID` int(11) NOT NULL,
  `QuestionText_EN` text NOT NULL,
  `QuestionText_HI` text NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`QuestionID`, `QuizID`, `QuestionText_EN`, `QuestionText_HI`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 1, 'What is the slope of y=2x+3?', 'y=2x+3 का ढलान क्या है?', 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(2, 1, 'Solve: 3x = 12', 'हल करें: 3x = 12', 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(3, 2, 'State Newton\'s First Law', 'न्यूटन का पहला नियम बताइए', 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(4, 2, 'What is inertia?', 'जड़त्व क्या है?', 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(5, 3, 'When did WWII begin?', 'WWII कब शुरू हुआ?', 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(6, 3, 'Who was the Prime Minister of Britain during WWII?', 'WWII के दौरान ब्रिटेन के प्रधानमंत्री कौन थे?', 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(7, 4, 'What is a variable?', 'वेरिएबल क्या है?', 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(8, 4, 'What is an integer data type?', 'इंटीजर डेटा टाइप क्या है?', 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(9, 5, 'Who wrote Sonnet 18?', 'सॉनेट 18 किसने लिखा?', 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(10, 5, 'What is a couplet in poetry?', 'कविता में कपलेट क्या है?', 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31');

-- --------------------------------------------------------

--
-- Table structure for table `question_test`
--

CREATE TABLE `question_test` (
  `QuestionID` int(11) NOT NULL,
  `TestID` int(11) NOT NULL,
  `QuestionText_EN` text NOT NULL,
  `QuestionText_HI` text NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question_test`
--

INSERT INTO `question_test` (`QuestionID`, `TestID`, `QuestionText_EN`, `QuestionText_HI`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 1, 'What is the area of a circle?', 'वृत्त का क्षेत्रफल क्या है?', 1, '2024-12-17 09:02:31', '2024-12-17 09:02:31'),
(2, 2, 'What is the formula for the volume of a cylinder?', 'सिलिंडर का आयतन का सूत्र क्या है?', 1, '2024-12-17 09:02:31', '2024-12-19 09:55:31');

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

CREATE TABLE `quiz` (
  `QuizID` int(11) NOT NULL,
  `QuizName` varchar(255) NOT NULL,
  `TopicID` int(11) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`QuizID`, `QuizName`, `TopicID`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'Algebra Basics Quiz', 1, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(2, 'Newton\'s Laws Quiz', 2, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(3, 'WWII Overview Quiz', 3, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(4, 'Intro to Programming Quiz', 4, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(5, 'Sonnets Quiz', 5, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31');

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `SubjectID` int(11) NOT NULL,
  `SubjectName` varchar(255) NOT NULL,
  `CourseID` int(11) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`SubjectID`, `SubjectName`, `CourseID`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'Algebra', 1, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(2, 'Physics', 2, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(3, 'World War II', 3, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(4, 'Programming Fundamentals', 4, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(5, 'Poetry', 5, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(6, 'geometry', 1, 1, '2024-12-17 15:09:57', '2024-12-17 15:09:57');

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE `test` (
  `TestID` int(11) NOT NULL,
  `TestName` varchar(255) NOT NULL,
  `SubjectID` int(11) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`TestID`, `TestName`, `SubjectID`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'mathematics 1', 1, 1, '2024-12-19 09:53:59', '2024-12-19 09:53:59'),
(2, 'phytsics 1', 2, 1, '2024-12-19 09:54:39', '2024-12-19 09:54:39');

-- --------------------------------------------------------

--
-- Table structure for table `topic`
--

CREATE TABLE `topic` (
  `TopicID` int(11) NOT NULL,
  `TopicName` varchar(255) NOT NULL,
  `SubjectID` int(11) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topic`
--

INSERT INTO `topic` (`TopicID`, `TopicName`, `SubjectID`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'Linear Equations', 1, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(2, 'Newtonian Mechanics', 2, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(3, 'Causes of WWII', 3, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(4, 'Variables and Data Types', 4, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(5, 'Shakespearean Sonnets', 5, 1, '2024-12-17 14:32:31', '2024-12-17 14:32:31'),
(6, 'circle', 6, 1, '2024-12-17 15:12:31', '2024-12-17 15:12:31'),
(7, 'rectangle', 6, 1, '2024-12-17 15:12:42', '2024-12-17 15:12:42');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `UserName` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`AdminID`),
  ADD UNIQUE KEY `UserName` (`UserName`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`CourseID`),
  ADD KEY `idx_course_name` (`CourseName`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`OptionID`),
  ADD KEY `QuestionID` (`QuestionID`);

--
-- Indexes for table `options_test`
--
ALTER TABLE `options_test`
  ADD PRIMARY KEY (`OptionID`),
  ADD KEY `QuestionID` (`QuestionID`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`QuestionID`),
  ADD KEY `QuizID` (`QuizID`);

--
-- Indexes for table `question_test`
--
ALTER TABLE `question_test`
  ADD PRIMARY KEY (`QuestionID`),
  ADD KEY `TestID` (`TestID`);

--
-- Indexes for table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`QuizID`),
  ADD KEY `TopicID` (`TopicID`),
  ADD KEY `idx_quiz_name` (`QuizName`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`SubjectID`),
  ADD KEY `CourseID` (`CourseID`),
  ADD KEY `idx_subject_name` (`SubjectName`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`TestID`),
  ADD KEY `SubjectID` (`SubjectID`);

--
-- Indexes for table `topic`
--
ALTER TABLE `topic`
  ADD PRIMARY KEY (`TopicID`),
  ADD KEY `SubjectID` (`SubjectID`),
  ADD KEY `idx_topic_name` (`TopicName`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `AdminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `CourseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `OptionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
  MODIFY `QuestionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `question_test`
--
ALTER TABLE `question_test`
  MODIFY `QuestionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `quiz`
--
ALTER TABLE `quiz`
  MODIFY `QuizID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `SubjectID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `TestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `topic`
--
ALTER TABLE `topic`
  MODIFY `TopicID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_ibfk_1` FOREIGN KEY (`QuestionID`) REFERENCES `question` (`QuestionID`) ON DELETE CASCADE;

--
-- Constraints for table `options_test`
--
ALTER TABLE `options_test`
  ADD CONSTRAINT `options_test_ibfk_1` FOREIGN KEY (`QuestionID`) REFERENCES `question_test` (`QuestionID`) ON DELETE CASCADE;

--
-- Constraints for table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `question_ibfk_1` FOREIGN KEY (`QuizID`) REFERENCES `quiz` (`QuizID`) ON DELETE CASCADE;

--
-- Constraints for table `question_test`
--
ALTER TABLE `question_test`
  ADD CONSTRAINT `question_test_ibfk_1` FOREIGN KEY (`TestID`) REFERENCES `test` (`TestID`) ON DELETE CASCADE;

--
-- Constraints for table `quiz`
--
ALTER TABLE `quiz`
  ADD CONSTRAINT `quiz_ibfk_1` FOREIGN KEY (`TopicID`) REFERENCES `topic` (`TopicID`) ON DELETE CASCADE;

--
-- Constraints for table `subject`
--
ALTER TABLE `subject`
  ADD CONSTRAINT `subject_ibfk_1` FOREIGN KEY (`CourseID`) REFERENCES `course` (`CourseID`) ON DELETE CASCADE;

--
-- Constraints for table `test`
--
ALTER TABLE `test`
  ADD CONSTRAINT `test_ibfk_1` FOREIGN KEY (`SubjectID`) REFERENCES `subject` (`SubjectID`) ON DELETE CASCADE;

--
-- Constraints for table `topic`
--
ALTER TABLE `topic`
  ADD CONSTRAINT `topic_ibfk_1` FOREIGN KEY (`SubjectID`) REFERENCES `subject` (`SubjectID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
