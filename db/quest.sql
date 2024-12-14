-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2024 at 02:41 PM
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
-- Database: `quest`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Mathematics', 'Test your knowledge of basic and advanced mathematical concepts', '2024-12-11 10:46:58', '2024-12-11 10:46:58'),
(2, 'Science', 'Explore various scientific disciplines from physics to biology', '2024-12-11 10:46:58', '2024-12-11 10:46:58'),
(3, 'Computer Programming', 'Questions about programming languages, algorithms, and software development', '2024-12-11 10:46:58', '2024-12-11 10:46:58'),
(4, 'History', 'Journey through different periods of world history', '2024-12-11 10:46:58', '2024-12-11 10:46:58'),
(5, 'Geography', 'Test your knowledge of world geography, countries, and landmarks', '2024-12-11 10:46:58', '2024-12-11 10:46:58');

-- --------------------------------------------------------

--
-- Table structure for table `leaderboard`
--

CREATE TABLE `leaderboard` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `high_score` int(11) NOT NULL,
  `achieved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leaderboard`
--

INSERT INTO `leaderboard` (`id`, `user_id`, `quiz_id`, `high_score`, `achieved_at`) VALUES
(1, 1, 4, 0, '2024-12-11 10:48:01'),
(2, 1, 5, 0, '2024-12-11 10:49:28'),
(3, 1, 13, 0, '2024-12-11 10:56:44'),
(4, 1, 1, 0, '2024-12-11 11:02:48'),
(5, 2, 13, 0, '2024-12-11 12:30:47'),
(6, 2, 1, 1, '2024-12-11 12:47:17'),
(7, 2, 4, 2, '2024-12-11 13:03:38'),
(9, 2, 5, 3, '2024-12-11 13:27:53'),
(10, 2, 13, 2, '2024-12-11 21:04:38'),
(11, 2, 7, 3, '2024-12-11 21:13:08'),
(12, 2, 13, 2, '2024-12-11 22:11:57'),
(13, 2, 5, 3, '2024-12-11 22:12:18');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`options`)),
  `correct_option` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `options`, `correct_option`, `created_at`, `updated_at`) VALUES
(1, 1, 'Solve for x: 2x + 5 = 13', '[\"x = 3\", \"x = 4\", \"x = 5\", \"x = 6\"]', 2, '2024-12-11 10:46:59', '2024-12-11 10:46:59'),
(2, 1, 'What is the value of y in: y - 8 = 12?', '[\"y = 4\", \"y = 20\", \"y = 16\", \"y = 24\"]', 2, '2024-12-11 10:46:59', '2024-12-11 10:46:59'),
(3, 1, 'Simplify: 3(x + 2) - 2x', '[\"x + 6\", \"5x + 2\", \"x + 2\", \"3x + 6\"]', 1, '2024-12-11 10:46:59', '2024-12-11 10:46:59'),
(4, 4, 'What is the SI unit of force?', '[\"Watt\", \"Newton\", \"Joule\", \"Pascal\"]', 2, '2024-12-11 10:46:59', '2024-12-11 10:46:59'),
(5, 4, 'Which of these is NOT a type of simple machine?', '[\"Lever\", \"Pulley\", \"Transistor\", \"Wedge\"]', 3, '2024-12-11 10:46:59', '2024-12-11 10:46:59'),
(6, 4, 'What is the formula for calculating velocity?', '[\"Distance × Time\", \"Distance ÷ Time\", \"Time ÷ Distance\", \"Mass × Acceleration\"]', 2, '2024-12-11 10:46:59', '2024-12-11 10:46:59'),
(7, 7, 'Which of the following is a correct way to create a list in Python?', '[\"array(1, 2, 3)\", \"{1, 2, 3}\", \"[1, 2, 3]\", \"(1, 2, 3)\"]', 3, '2024-12-11 10:46:59', '2024-12-11 10:46:59'),
(8, 7, 'What is the output of print(type(5.0))?', '[\"<class \'int\'>\", \"<class \'float\'>\", \"<class \'number\'>\", \"<class \'double\'>\"]', 2, '2024-12-11 10:46:59', '2024-12-11 10:46:59'),
(9, 7, 'Which operator is used for exponentiation in Python?', '[\"^\", \"**\", \"^^\", \"#\"]', 2, '2024-12-11 10:46:59', '2024-12-11 10:46:59'),
(10, 13, 'What is the capital of Australia?', '[\"Sydney\", \"Melbourne\", \"Canberra\", \"Perth\"]', 3, '2024-12-11 10:46:59', '2024-12-11 10:46:59'),
(11, 13, 'Which city is the capital of Brazil?', '[\"Rio de Janeiro\", \"São Paulo\", \"Brasília\", \"Salvador\"]', 3, '2024-12-11 10:46:59', '2024-12-11 10:46:59'),
(12, 13, 'What is the capital city of Japan?', '[\"Osaka\", \"Kyoto\", \"Tokyo\", \"Yokohama\"]', 3, '2024-12-11 10:46:59', '2024-12-11 10:46:59'),
(13, 5, 'What is the atomic number of Carbon?', '[\"4\", \"6\", \"8\", \"12\"]', 2, '2024-12-11 10:46:59', '2024-12-11 10:46:59'),
(14, 5, 'Which of these is a noble gas?', '[\"Nitrogen\", \"Oxygen\", \"Helium\", \"Hydrogen\"]', 3, '2024-12-11 10:46:59', '2024-12-11 10:46:59'),
(15, 5, 'What is the chemical symbol for Gold?', '[\"Ag\", \"Au\", \"Fe\", \"Cu\"]', 2, '2024-12-11 10:46:59', '2024-12-11 10:46:59'),
(16, 12, 'What period is commonly referred to as the Middle Ages?', '[\"500-1500 CE\", \"0-500 CE\", \"1500-2000 CE\", \"1000-1200 CE\"]', 1, '2024-12-11 10:46:59', '2024-12-11 10:46:59'),
(17, 12, 'Which system of social organization was prevalent in medieval Europe?', '[\"Democracy\", \"Feudalism\", \"Communism\", \"Capitalism\"]', 2, '2024-12-11 10:46:59', '2024-12-11 10:46:59'),
(18, 12, 'What was the primary language of medieval European scholars?', '[\"French\", \"English\", \"Latin\", \"Greek\"]', 3, '2024-12-11 10:46:59', '2024-12-11 10:46:59');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `duration_minutes` int(11) NOT NULL DEFAULT 3 COMMENT 'Duration of the quiz in minutes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `category_id`, `name`, `description`, `created_at`, `updated_at`, `duration_minutes`) VALUES
(1, 1, 'Basic Algebra', 'Practice fundamental algebraic concepts and equations', '2024-12-11 10:46:58', '2024-12-11 10:46:58', 3),
(2, 1, 'Geometry Fundamentals', 'Test your knowledge of shapes, angles, and spatial relationships', '2024-12-11 10:46:58', '2024-12-11 10:46:58', 3),
(3, 1, 'Trigonometry Basics', 'Understanding sine, cosine, and triangular relationships', '2024-12-11 10:46:58', '2024-12-11 10:46:58', 3),
(4, 2, 'Basic Physics', 'Understanding motion, forces, and energy', '2024-12-11 10:46:59', '2024-12-11 10:46:59', 3),
(5, 2, 'Chemistry 101', 'Introduction to chemical elements and reactions', '2024-12-11 10:46:59', '2024-12-11 10:46:59', 3),
(6, 2, 'Human Biology', 'Learn about human anatomy and biological systems', '2024-12-11 10:46:59', '2024-12-11 10:46:59', 3),
(7, 3, 'Python Basics', 'Introduction to Python programming language', '2024-12-11 10:46:59', '2024-12-11 10:46:59', 3),
(8, 3, 'JavaScript Fundamentals', 'Core concepts of JavaScript programming', '2024-12-11 10:46:59', '2024-12-11 10:46:59', 3),
(9, 3, 'SQL Essentials', 'Database queries and management basics', '2024-12-11 10:46:59', '2024-12-11 10:46:59', 3),
(10, 4, 'Ancient Civilizations', 'Explore the earliest human societies and their developments', '2024-12-11 10:46:59', '2024-12-11 10:46:59', 3),
(11, 4, 'World War II', 'Major events and figures of the Second World War', '2024-12-11 10:46:59', '2024-12-11 10:46:59', 3),
(12, 4, 'Medieval Europe', 'Life and events in European middle ages', '2024-12-11 10:46:59', '2024-12-11 10:46:59', 3),
(13, 5, 'World Capitals', 'Test your knowledge of capital cities around the world', '2024-12-11 10:46:59', '2024-12-11 10:46:59', 3),
(14, 5, 'Natural Wonders', 'Famous geographical landmarks and natural phenomena', '2024-12-11 10:46:59', '2024-12-11 10:46:59', 3),
(15, 5, 'Countries and Borders', 'International boundaries and neighboring nations', '2024-12-11 10:46:59', '2024-12-11 10:46:59', 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `fname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `role` int(11) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `fname`, `lname`, `role`, `registration_date`) VALUES
(1, 'recipeowner@gmail.com', '$2y$10$5bTYbRq18j57ID.puYqRTOWuS1Xv95.F/n0HaOdEuD.uPfgfFm4xi', 'Kirk', 'Kudoto', 2, '2024-12-10 13:18:02'),
(2, 'e.akordor@gmail.com', '$2y$10$vF.PLjJg3OKkbZDJQq2VcO8CvqTmH2Vpri0kyyVJO9IG2yvquFxtu', 'Eunice', 'Akordor', 2, '2024-12-11 13:29:35'),
(3, 'admin@gmail.com', '$2y$10$n1EOBXRWGXPJ.OVluvlxRuevrzRV8KE9NKISlZK.Z7jor0INjcTE2', 'Admin', 'User', 1, '2024-12-11 13:39:43');

-- --------------------------------------------------------

--
-- Table structure for table `user_progress`
--

CREATE TABLE `user_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_progress`
--

INSERT INTO `user_progress` (`id`, `user_id`, `quiz_id`, `score`, `completed_at`) VALUES
(1, 1, 4, 0, '2024-12-11 10:48:01'),
(2, 1, 5, 0, '2024-12-11 10:49:28'),
(3, 1, 13, 0, '2024-12-11 10:56:44'),
(4, 1, 1, 0, '2024-12-11 11:02:48'),
(5, 2, 13, 0, '2024-12-11 12:30:47'),
(6, 2, 1, 1, '2024-12-11 12:47:17'),
(7, 2, 4, 2, '2024-12-11 13:03:38'),
(9, 2, 5, 3, '2024-12-11 13:27:52'),
(10, 2, 13, 2, '2024-12-11 21:04:38'),
(11, 2, 7, 3, '2024-12-11 21:13:08'),
(12, 2, 13, 2, '2024-12-11 22:11:57'),
(13, 2, 5, 3, '2024-12-11 22:12:18');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_option` int(11) DEFAULT NULL,
  `time_spent` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `user_id`, `quiz_id`, `question_id`, `selected_option`, `time_spent`) VALUES
(1, 1, 4, 4, 1, 0),
(2, 1, 4, 5, 2, 0),
(3, 1, 4, 6, 0, 0),
(4, 1, 5, 13, 1, 0),
(5, 1, 5, 14, 2, 0),
(6, 1, 5, 15, 1, 0),
(7, 1, 13, 10, 0, 0),
(8, 1, 13, 11, 2, 0),
(9, 1, 13, 12, 2, 0),
(10, 1, 1, 1, 1, 0),
(11, 1, 1, 2, 1, 0),
(12, 1, 1, 3, 0, 0),
(13, 2, 13, 10, 0, 0),
(14, 2, 13, 11, 2, 0),
(15, 2, 13, 12, 2, 0),
(16, 2, 1, 1, 1, 0),
(17, 2, 1, 2, 2, 0),
(18, 2, 1, 3, 0, 0),
(19, 2, 4, 4, 2, 0),
(20, 2, 4, 5, 3, 0),
(21, 2, 4, 6, 1, 0),
(22, 2, 7, 7, 3, 0),
(23, 2, 7, 8, 2, 0),
(24, 2, 7, 9, 2, 0),
(25, 2, 5, 13, 2, 0),
(26, 2, 5, 14, 3, 0),
(27, 2, 5, 15, 2, 0),
(28, 2, 13, 10, 1, 0),
(29, 2, 13, 11, 3, 0),
(30, 2, 13, 12, 3, 0),
(31, 2, 7, 7, 3, 0),
(32, 2, 7, 8, 2, 0),
(33, 2, 7, 9, 2, 0),
(34, 2, 13, 10, 1, 0),
(35, 2, 13, 11, 3, 0),
(36, 2, 13, 12, 3, 0),
(37, 2, 5, 13, 2, 0),
(38, 2, 5, 14, 3, 0),
(39, 2, 5, 15, 2, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `leaderboard`
--
ALTER TABLE `leaderboard`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_quiz_category` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `idx_user_quiz` (`user_id`,`quiz_id`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `idx_user_sessions` (`user_id`,`quiz_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `leaderboard`
--
ALTER TABLE `leaderboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `leaderboard`
--
ALTER TABLE `leaderboard`
  ADD CONSTRAINT `leaderboard_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leaderboard_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_progress_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_sessions_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_sessions_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
