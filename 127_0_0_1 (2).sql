-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 28, 2026 at 09:11 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ems_db`
--
CREATE DATABASE IF NOT EXISTS `ems_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ems_db`;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `ic_number` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `full_name`, `ic_number`, `email`, `phone`, `password`) VALUES
(1, 'WAN NURUL FARISHA BINTI MOHD RITHEER', '060620030010', 'admin@example.com', '011-30615625', 'admin123'),
(2, 'RANIA QAISARA BINTI ROBERT', '060526110276', 'ara26@gmail.com', '0134522164', '$ara4432');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcements_id` int(11) NOT NULL,
  `tittle` varchar(200) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(20) NOT NULL,
  `registration_id` int(20) NOT NULL,
  `attendance_status` varchar(30) NOT NULL,
  `check_in` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `registration_id`, `attendance_status`, `check_in`) VALUES
(1, 1, 'Absent', NULL),
(2, 2, 'Absent', NULL),
(3, 3, 'Absent', NULL),
(4, 4, 'Absent', '2026-08-10 18:13:45.000000'),
(5, 5, 'Present', '2026-08-11 16:25:10.000000'),
(6, 6, 'Present', '2026-08-10 18:06:59.000000'),
(7, 7, 'Absent', NULL),
(8, 8, 'Absent', NULL),
(9, 9, 'Absent', NULL),
(10, 10, 'Absent', NULL),
(11, 11, 'Absent', NULL),
(12, 12, 'Absent', NULL),
(13, 13, 'Absent', NULL),
(14, 14, 'Absent', NULL),
(15, 15, 'Absent', NULL),
(16, 16, 'Absent', NULL),
(17, 17, 'Absent', NULL),
(18, 18, 'Absent', NULL),
(19, 19, 'Absent', NULL),
(20, 20, 'Absent', NULL),
(21, 21, 'Absent', NULL),
(22, 22, 'Absent', NULL),
(23, 23, 'Absent', NULL),
(24, 24, 'Absent', NULL),
(25, 25, 'Absent', NULL),
(26, 26, 'Absent', NULL),
(27, 27, 'Absent', NULL),
(28, 28, 'Absent', NULL),
(29, 29, 'Absent', NULL),
(30, 30, 'Absent', NULL),
(31, 31, 'Absent', NULL),
(32, 32, 'Absent', NULL),
(33, 33, 'Absent', NULL),
(34, 34, 'Absent', NULL),
(35, 35, 'Absent', NULL),
(36, 36, 'Absent', NULL),
(37, 37, 'Absent', NULL),
(38, 38, 'Absent', NULL),
(39, 39, 'Absent', NULL),
(40, 40, 'Absent', NULL),
(41, 41, 'Absent', NULL),
(42, 42, 'Absent', NULL),
(43, 43, 'Absent', NULL),
(44, 44, 'Absent', NULL),
(45, 45, 'Absent', NULL),
(46, 46, 'Absent', NULL),
(47, 47, 'Absent', NULL),
(48, 48, 'Absent', NULL),
(49, 49, 'Absent', NULL),
(50, 50, 'Absent', NULL),
(51, 51, 'Absent', NULL),
(52, 52, 'Absent', NULL),
(53, 53, 'Absent', NULL),
(54, 54, 'Absent', NULL),
(55, 55, 'Present', '2026-08-24 18:44:14.000000'),
(56, 56, 'Absent', NULL),
(57, 57, 'Absent', NULL),
(58, 58, 'Present', '2026-08-27 15:58:32.000000'),
(59, 59, 'Absent', NULL),
(60, 60, 'Absent', NULL),
(61, 61, 'Absent', NULL),
(62, 62, 'Absent', NULL),
(63, 63, 'Absent', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `certificates_id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `certificates_no` varchar(100) NOT NULL,
  `issue_date` date NOT NULL,
  `certificate_file` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`certificates_id`, `registration_id`, `certificates_no`, `issue_date`, `certificate_file`) VALUES
(1, 5, 'JPP-2026-00005', '2026-08-20', 'certificate_5.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `event_title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `event_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `venue` varchar(200) NOT NULL,
  `quota` int(11) NOT NULL,
  `poster` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_title`, `description`, `event_date`, `start_time`, `end_time`, `venue`, `quota`, `poster`, `status`) VALUES
(1, 'Pelancaran Bulan Kemerdekaan ', 'Diwajibkan Sem 1 & 2', '2026-08-11', '08:00:00', '10:00:00', 'Dewan Jubli Perak ,PUO', 50, '1786092644_PBK.jpeg', 'Closed'),
(3, 'Majlis Pelancaran Minggu Kelab & Kebudayaan', 'Meraikan Pelbagai Budaya Serta Melihat Sketsa ', '2026-08-18', '10:30:00', '18:00:00', 'Dewan Warisan', 50, '1786093165_MKK.jpeg', 'Closed'),
(4, 'Pendaftaran Pelajar Degree', 'Pelajar Degree', '2026-08-21', '08:00:00', '17:00:00', 'Dewan Warisan', 40, '1786092577_PPD.jpeg', 'Open'),
(5, 'Kempen Derma Darah', 'Jom Derma Darah', '2026-08-28', '21:00:00', '15:00:00', 'Dewan Warisan', 25, '1786092558_KDD.jpeg', 'Closed'),
(6, 'Solat Hajat Perdana', 'Program Solat Hajat Perdana khusus buat para pelajar yang bakal menduduki peperiksaan  tidak lama lagi, serta untuk kesejahteraan seluruh pelajar.', '2026-10-06', '20:00:00', '21:30:00', 'Masjid Sultan Azlan Shah', 35, '1786092545_SHP.jpeg', 'Open'),
(7, 'Diploma Sec Intake', 'Pendaftaran pelajar sec intake', '2026-11-22', '07:30:00', '18:00:00', 'Dewan Jubli Perak', 20, '1786092532_PPSI.jpeg', 'Open'),
(9, 'Festival Malam Kebudayaan', 'Tema : Pakaian Tradisional', '2026-08-27', '15:00:00', '17:00:00', 'Stadium Indera Mulia', 60, '1787817165_FMK.jpeg', 'Closed'),
(10, 'Program Gotong-Royong Perdana', 'Program gotong-royong perdana bersama pelajar bagi membersihkan dan menceriakan kawasan kampus serta mengeratkan hubungan antara pelajar.', '2026-09-05', '08:00:00', '12:00:00', 'Dewan Warisan', 50, '1788451996_ChatGPT Image Sep 4, 2026, 12_12_26 AM.png', 'Open');

-- --------------------------------------------------------

--
-- Table structure for table `event_reminders`
--

CREATE TABLE `event_reminders` (
  `reminder_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reminder_type` varchar(50) NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exemption_memos`
--

CREATE TABLE `exemption_memos` (
  `memo_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `memo_file` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exemption_memos`
--

INSERT INTO `exemption_memos` (`memo_id`, `event_id`, `memo_file`, `created_at`) VALUES
(4, 3, '1787202176_MEMO_PENGECUALIAN_KULIAH_PROGRAM_BERSAMA_MSU_170826.pdf', '2026-08-20 05:02:56'),
(5, 9, '1787817704_1787201572_MEMO_PENGECUALIAN_KULIAH_PROGRAM_BERSAMA_MSU_170826.pdf', '2026-08-27 08:01:44');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notifications_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tittle` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `registration_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `qr_code` varchar(255) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(20) NOT NULL,
  `reminder_sent` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`registration_id`, `user_id`, `event_id`, `qr_code`, `registration_date`, `status`, `reminder_sent`) VALUES
(1, 2, 1, 'QR1785396021870', '2026-07-30 07:20:21', 'Registered', 0),
(2, 2, 3, 'QR1786091533986', '2026-08-07 08:32:13', 'Registered', 0),
(3, 4, 5, 'QR1786341748769', '2026-08-10 06:02:28', 'Registered', 0),
(4, 4, 4, 'QR1787293979954', '2026-08-21 07:43:56', 'Cancelled', 0),
(5, 4, 3, 'QR1786341791626', '2026-08-10 06:03:11', 'Registered', 0),
(6, 8, 6, 'QR1786343701895', '2026-08-10 06:35:01', 'Registered', 0),
(7, 8, 4, 'QR1786343729812', '2026-08-10 06:35:29', 'Registered', 0),
(8, 9, 4, 'QR1786436161724', '2026-08-11 08:16:01', 'Registered', 0),
(9, 9, 6, 'QR1786435892712', '2026-08-11 08:11:32', 'Registered', 0),
(10, 7, 6, 'QR1787249117626', '2026-08-20 18:05:17', 'Registered', 0),
(11, 7, 7, 'QR1787249128506', '2026-08-20 18:05:28', 'Registered', 0),
(12, 7, 3, 'QR1787249145570', '2026-08-20 18:05:45', 'Registered', 0),
(13, 8, 1, 'QR1787249188908', '2026-08-20 18:06:28', 'Registered', 0),
(14, 9, 1, 'QR1787249242970', '2026-08-20 18:07:22', 'Registered', 0),
(15, 9, 3, 'QR1787249248602', '2026-08-20 18:07:28', 'Registered', 0),
(16, 10, 5, 'QR1787249303923', '2026-08-20 18:08:23', 'Registered', 0),
(17, 10, 7, 'QR1787249313941', '2026-08-20 18:08:33', 'Registered', 0),
(18, 10, 3, 'QR1787249320427', '2026-08-20 18:08:40', 'Registered', 0),
(19, 11, 1, 'QR1787249417572', '2026-08-20 18:10:17', 'Registered', 0),
(20, 11, 3, 'QR1787249423189', '2026-08-20 18:10:23', 'Registered', 0),
(21, 11, 5, 'QR1787249430711', '2026-08-20 18:10:30', 'Registered', 0),
(22, 11, 7, 'QR1787249440772', '2026-08-20 18:10:40', 'Registered', 0),
(23, 11, 4, 'QR1787249449985', '2026-08-20 18:10:49', 'Registered', 0),
(24, 12, 7, 'QR1787249514365', '2026-08-20 18:11:54', 'Registered', 0),
(25, 12, 3, 'QR1787249521180', '2026-08-20 18:12:01', 'Registered', 0),
(26, 12, 1, 'QR1787249528761', '2026-08-20 18:12:08', 'Registered', 0),
(27, 13, 5, 'QR1787249603734', '2026-08-20 18:13:23', 'Registered', 0),
(28, 13, 1, 'QR1787249611803', '2026-08-20 18:13:31', 'Registered', 0),
(29, 14, 7, 'QR1787249662315', '2026-08-20 18:14:22', 'Registered', 0),
(30, 14, 1, 'QR1787249673545', '2026-08-20 18:14:33', 'Registered', 0),
(31, 14, 3, 'QR1787249679609', '2026-08-20 18:14:39', 'Registered', 0),
(32, 15, 7, 'QR1787249818715', '2026-08-20 18:16:58', 'Registered', 0),
(33, 15, 3, 'QR1787249824848', '2026-08-20 18:17:04', 'Registered', 0),
(34, 16, 7, 'QR1787249900916', '2026-08-20 18:18:20', 'Registered', 0),
(35, 16, 3, 'QR1787249909504', '2026-08-20 18:18:29', 'Registered', 0),
(36, 16, 1, 'QR1787249917228', '2026-08-20 18:18:37', 'Registered', 0),
(37, 16, 6, 'QR1787249923169', '2026-08-20 18:18:43', 'Registered', 0),
(38, 17, 4, 'QR1787250007304', '2026-08-20 18:20:07', 'Registered', 0),
(39, 17, 1, 'QR1787250013640', '2026-08-20 18:20:13', 'Registered', 0),
(40, 17, 3, 'QR1787250019591', '2026-08-20 18:20:19', 'Registered', 0),
(41, 17, 6, 'QR1787250030791', '2026-08-20 18:20:30', 'Registered', 0),
(42, 18, 7, 'QR1787250119792', '2026-08-20 18:21:59', 'Registered', 0),
(43, 18, 5, 'QR1787250134479', '2026-08-20 18:22:14', 'Registered', 0),
(44, 18, 3, 'QR1787250142990', '2026-08-20 18:22:22', 'Registered', 0),
(45, 18, 1, 'QR1787250147224', '2026-08-20 18:22:27', 'Registered', 0),
(46, 19, 1, 'QR1787250204899', '2026-08-20 18:23:24', 'Registered', 0),
(47, 19, 3, 'QR1787250209622', '2026-08-20 18:23:29', 'Registered', 0),
(48, 19, 5, 'QR1787250218968', '2026-08-20 18:23:38', 'Registered', 0),
(49, 19, 4, 'QR1787250229126', '2026-08-20 18:23:49', 'Registered', 0),
(50, 20, 7, 'QR1787250288612', '2026-08-20 18:24:48', 'Registered', 0),
(51, 20, 1, 'QR1787250298540', '2026-08-20 18:24:58', 'Registered', 0),
(52, 20, 3, 'QR1787250303894', '2026-08-20 18:25:03', 'Registered', 0),
(53, 4, 1, 'QR1787298219407', '2026-08-21 07:43:39', 'Registered', 0),
(54, 55, 1, 'QR1787300155115', '2026-08-21 08:15:55', 'Registered', 0),
(55, 4, 7, 'QR1787567142478', '2026-08-24 10:25:42', 'Registered', 0),
(56, 56, 5, 'QR20260825163622428', '2026-08-25 14:36:22', 'Registered', 0),
(57, 64, 1, 'QR1787816860350', '2026-08-27 07:48:48', 'Cancelled', 0),
(58, 64, 9, 'QR1787817417532', '2026-08-27 07:56:57', 'Registered', 0),
(59, 2, 10, 'QR1788452249988', '2026-09-04 11:16:26', 'Registered', 1),
(60, 61, 10, 'QR1788509348255', '2026-09-04 11:16:30', 'Registered', 1),
(61, 65, 10, 'QR1788521234290', '2026-09-04 11:27:14', 'Registered', 0),
(62, 4, 6, 'QR1788690038848', '2026-09-06 10:20:38', 'Registered', 0),
(63, 4, 10, 'QR1790576465406', '2026-09-28 06:21:05', 'Registered', 0);

-- --------------------------------------------------------

--
-- Table structure for table `survey_answers`
--

CREATE TABLE `survey_answers` (
  `answer_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `survey_answers`
--

INSERT INTO `survey_answers` (`answer_id`, `question_id`, `user_id`, `event_id`, `rating`, `submitted_at`) VALUES
(1, 1, 4, 3, 4, '2026-09-17 02:20:49'),
(2, 2, 4, 3, 4, '2026-09-17 02:20:49'),
(3, 3, 4, 3, 4, '2026-09-17 02:20:49'),
(4, 4, 4, 3, 4, '2026-09-17 02:20:49'),
(5, 5, 4, 3, 4, '2026-09-17 02:20:49'),
(6, 6, 4, 3, 4, '2026-09-17 02:20:49'),
(7, 7, 4, 3, 4, '2026-09-17 02:20:49'),
(8, 8, 4, 3, 4, '2026-09-17 02:20:49'),
(9, 9, 4, 3, 4, '2026-09-17 02:20:49'),
(10, 10, 4, 3, 4, '2026-09-17 02:20:49'),
(11, 11, 4, 3, 4, '2026-09-17 02:20:49'),
(12, 12, 4, 3, 4, '2026-09-17 02:20:49'),
(13, 13, 4, 3, 4, '2026-09-17 02:20:49'),
(14, 14, 4, 3, 4, '2026-09-17 02:20:49'),
(15, 15, 4, 3, 4, '2026-09-17 02:20:49'),
(16, 16, 4, 3, 4, '2026-09-17 02:20:49'),
(17, 17, 4, 3, 4, '2026-09-17 02:20:49'),
(18, 18, 4, 3, 4, '2026-09-17 02:20:49'),
(19, 19, 4, 3, 4, '2026-09-17 02:20:49'),
(20, 20, 4, 3, 4, '2026-09-17 02:20:49'),
(21, 21, 4, 3, 4, '2026-09-17 02:20:49'),
(22, 22, 4, 3, 4, '2026-09-17 02:20:49'),
(23, 23, 4, 3, 4, '2026-09-17 02:20:49'),
(24, 24, 4, 3, 4, '2026-09-17 02:20:49'),
(25, 25, 4, 3, 4, '2026-09-17 02:20:49'),
(26, 26, 4, 3, 4, '2026-09-17 02:20:49'),
(27, 27, 4, 3, 4, '2026-09-17 02:20:49');

-- --------------------------------------------------------

--
-- Table structure for table `survey_confirmations`
--

CREATE TABLE `survey_confirmations` (
  `confirmation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `survey_confirmations`
--

INSERT INTO `survey_confirmations` (`confirmation_id`, `user_id`, `event_id`, `submitted_at`) VALUES
(1, 4, 3, '2026-09-17 02:20:49');

-- --------------------------------------------------------

--
-- Table structure for table `survey_questions`
--

CREATE TABLE `survey_questions` (
  `question_id` int(11) NOT NULL,
  `section` varchar(150) NOT NULL,
  `question_number` int(11) NOT NULL,
  `question_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `survey_questions`
--

INSERT INTO `survey_questions` (`question_id`, `section`, `question_number`, `question_text`) VALUES
(1, 'A. Penilaian Program - Reaksi dan Pembelajaran', 1, 'Program yang dihadiri memberikan pengetahuan yang bermanfaat kepada saya.'),
(2, 'A. Penilaian Program - Reaksi dan Pembelajaran', 2, 'Kandungan program adalah sesuai dengan objektif yang ditetapkan.'),
(3, 'A. Penilaian Program - Reaksi dan Pembelajaran', 3, 'Program ini meningkatkan pengetahuan saya berkaitan dengan topik yang disampaikan.'),
(4, 'A. Penilaian Program - Reaksi dan Pembelajaran', 4, 'Pengetahuan yang diperolehi daripada program ini dapat digunakan dalam kehidupan seharian.'),
(5, 'B. Impak dari Segi Kemahiran', 5, 'Program ini memberikan kemahiran yang berguna kepada saya.'),
(6, 'B. Impak dari Segi Kemahiran', 6, 'Kemahiran yang diperolehi membantu saya dalam menyelesaikan masalah.'),
(7, 'B. Impak dari Segi Kemahiran', 7, 'Program ini meningkatkan kemahiran komunikasi dan kerjasama saya.'),
(8, 'B. Impak dari Segi Kemahiran', 8, 'Kemahiran yang diperolehi boleh diaplikasikan dalam aktiviti akademik dan organisasi.'),
(9, 'C. Hasil / Manfaat Program', 9, 'Program ini meningkatkan keyakinan diri saya.'),
(10, 'C. Hasil / Manfaat Program', 10, 'Program ini memberi manfaat kepada saya sebagai pelajar.'),
(11, 'C. Hasil / Manfaat Program', 11, 'Program yang dihadiri memberikan pengalaman yang berguna.'),
(12, 'C. Hasil / Manfaat Program', 12, 'Program ini membantu saya menjadi lebih produktif dalam aktiviti akademik atau organisasi.'),
(13, 'C. Hasil / Manfaat Program', 13, 'Program ini memberi impak positif kepada diri saya.'),
(14, 'C. Hasil / Manfaat Program', 14, 'Saya berpuas hati dengan keseluruhan program yang dianjurkan.'),
(15, 'D. Penyampaian Penceramah / Pengendali Program', 15, 'Objektif program telah tercapai.'),
(16, 'D. Penyampaian Penceramah / Pengendali Program', 16, 'Penyampaian penceramah atau pengendali program adalah tersusun dan mudah difahami.'),
(17, 'D. Penyampaian Penceramah / Pengendali Program', 17, 'Penceramah atau pengendali program mempunyai pengetahuan yang baik mengenai topik yang disampaikan.'),
(18, 'D. Penyampaian Penceramah / Pengendali Program', 18, 'Penceramah atau pengendali program berinteraksi dengan baik bersama peserta.'),
(19, 'D. Penyampaian Penceramah / Pengendali Program', 19, 'Aktiviti yang dijalankan sepanjang program adalah menarik dan bersesuaian.'),
(20, 'E. Pengurusan Program', 20, 'Proses pendaftaran program adalah mudah dan teratur.'),
(21, 'E. Pengurusan Program', 21, 'Maklumat berkaitan program disampaikan dengan jelas.'),
(22, 'E. Pengurusan Program', 22, 'Pengurusan masa sepanjang program adalah baik.'),
(23, 'E. Pengurusan Program', 23, 'Tempat dan kemudahan yang disediakan adalah sesuai.'),
(24, 'E. Pengurusan Program', 24, 'Urus setia memberikan kerjasama yang baik kepada peserta.'),
(25, 'F. Penilaian Keseluruhan', 25, 'Secara keseluruhannya, saya berpuas hati dengan program ini.'),
(26, 'F. Penilaian Keseluruhan', 26, 'Saya akan menyertai program anjuran JPP pada masa akan datang.'),
(27, 'F. Penilaian Keseluruhan', 27, 'Saya akan mencadangkan program seperti ini kepada pelajar lain.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `ic_number` varchar(20) NOT NULL,
  `registration_no` varchar(30) NOT NULL,
  `education_level` varchar(50) DEFAULT NULL,
  `semester` int(11) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `ic_number`, `registration_no`, `education_level`, `semester`, `phone`, `email`, `profile_picture`, `department`) VALUES
(2, 'Ahmad Qayyum ', '0605582140698', '01DIT24F2059', 'Diploma ', 2, '0111405789', 'amadqy@gmail.com', NULL, 'JKM'),
(4, 'RARA ATRISHA BINTI LAHRAM', '0602210100458', '01DIT24F1023', 'Diploma', 5, '0152451693', 'sfhnyluvv@gmail.com', 'user_4_1787566582.jpg', 'JP'),
(7, 'AISYAH ZAHRA ', '060415140233', '01DIT24F1516', 'Diploma', 3, '01112894415', 'ecah15@gmail.com', NULL, 'JKE'),
(8, 'MUHAMMAD AARYAN', '0511140143', '01DIT23F1211', 'Degree', 4, '019 3455 6123', 'aaryan14@gamil.com', NULL, 'JMSK'),
(9, 'AISYAH ZUNAH', '060822451123', '01DI24F1008', 'Degree', 2, '011128941111', 'ecah11@gmail.com', NULL, 'JKA'),
(10, 'NUR FATIHAH BINTI MISRAN', '060101101234', '01DIT24F1143', 'Diploma', 3, '01123456701', 'fatihah1143@testmail.com', NULL, 'JP'),
(11, 'VIKASH REDDEIYER A/P NADARAJAN', '060215101235', '01DIT24F1151', 'Degree', 2, '01123456702', 'vikash1151@testmail.com', NULL, 'JTMK'),
(12, 'JANANI A/P MARIAPPEN', '060330101236', '01DIT24F1158', 'Diploma', 4, '01123456703', 'janani1158@testmail.com', NULL, 'JKA'),
(13, 'YUN YONG YANG', '060445101237', '01DIT24F1162', 'Diploma', 4, '01123456704', 'yunyang1162@testmail.com', NULL, 'JKE'),
(14, 'RISHILLAN PERUMAL AL CHELLIAH', '060512101238', '01DIT24F1164', 'Diploma', 1, '01123456705', 'rishillan1164@testmail.com', NULL, 'JTMK'),
(15, 'MOHAMMAD IDRISH AMMAR BIN HAKBAR', '060623101239', '01DIT24F1166', 'Diploma', 1, '01123456706', 'ammar1166@testmail.com', NULL, 'JTMK'),
(16, 'NURHANAYATUL IMAN BINTI JULI', '060730101240', '01DIT24F1178', 'Diploma', 4, '01123456707', 'iman1178@testmail.com', NULL, 'JP'),
(17, 'NURIN DHAIMIRAH FAIHANAH BINTI MOHAMMAD ZAIDI', '060815101241', '01DIT24F1305', 'Degree', 3, '01123456708', 'nurin1305@testmail.com', NULL, 'JKE'),
(18, 'PUUPATHYSRI A/P KESAVAN', '060922101242', '01DIT24F1180', 'Diploma', 5, '01123456709', 'puupathy1180@testmail.com', NULL, 'JKA'),
(19, 'NUR DALILI BINTI AZAMAN', '061031101243', '01DIT24F1183', 'Diploma', 5, '01123456710', 'nlilyy@testmail.com', NULL, 'JKM'),
(20, 'GANGES MURUGAN', '061101101244', '01DIT24F1184', 'Diploma', 2, '01123456711', 'ganges1184@testmail.com', NULL, 'JKP'),
(21, 'AMMAR YUSUF BIN AHMAD TARMIZI', '061215101245', '01DIT24F1055', 'Diploma', 1, '01123456712', 'ammaryusuf1055@testmail.com', NULL, 'JKP'),
(22, 'IZ ZAHIRA BINTI RAMLEE', '061330101246', '01DIT24F1186', 'Diploma', 1, '01123456713', 'izzahira1186@testmail.com', NULL, 'JMSK'),
(23, 'JEREMIAH GREEN AL ROBERT GREEN', '061445101247', '01DIT24F1188', 'Degree', 4, '01123456714', 'jeremiah1188@testmail.com', NULL, 'JKP'),
(24, 'AYYMAN EUSOUFF BIN JAZIM HAZMAN', '061512101248', '01DIT24F1190', 'Diploma', 3, '01123456715', 'ayyman1190@testmail.com', NULL, 'JKP'),
(25, 'JIVANESH AL SUNDARAM', '061623101249', '01DIT24F1191', 'Degree', 5, '01123456716', 'jivanesh1191@testmail.com', NULL, 'JKE'),
(26, 'ARISHA ZULFAH BINTI ABDUL RAZAK', '061730101250', '01DIT24F1193', 'Degree', 5, '01123456717', 'arisha1193@testmail.com', NULL, 'JP'),
(27, 'MUHAMMAD ADAM DANIAL BIN MOHAMMAD RIDZUAN', '061815101251', '01DIT24F1196', 'Diploma', 1, '01123456718', 'adamdanial1196@testmail.com', NULL, 'JKM'),
(28, 'MUHAMMAD SYAHMI BIN MOHAMAD NAZRI', '061922101252', '01DIT24F1198', 'Diploma', 4, '01123456719', 'syahmi1198@testmail.com', NULL, 'JKE'),
(29, 'ALIF DANIEL BIN SUFFIAN', '062031101253', '01DIT24F1200', 'Degree', 1, '01123456720', 'alifdaniel1200@testmail.com', NULL, 'JMSK'),
(30, 'DHIEYA FASIHA BINTI AZMI', '062115101254', '01DIT24F1201', 'Diploma', 5, '01123456721', 'dhieya1201@testmail.com', NULL, 'JKA'),
(31, 'RANJENI A/P GOHILAKANNAN', '062230101255', '01DIT24F1202', 'Diploma', 1, '01123456722', 'ranjeni1202@testmail.com', NULL, 'JKM'),
(32, 'SHAFIQ ARIF BIN SHAMSUL ARIF', '062345101256', '01DIT24F1203', 'Diploma', 4, '01123456723', 'shafiq1203@testmail.com', NULL, 'JKA'),
(33, 'MUHAMMAD QABIL QAYYUM BIN MOHAMAD ZAPRI', '062412101257', '01DIT24F1204', 'Degree', 3, '01123456724', 'qabil1204@testmail.com', NULL, 'JTMK'),
(34, 'KAMARUL HAKIM BIN KAMARUL BAHRIN', '062523101258', '01DIT24F1205', 'Diploma', 1, '01123456725', 'hakim1205@testmail.com', NULL, 'JTMK'),
(35, 'NITIYANANTHADAS AL ANANDAN', '062630101259', '01DIT24F1206', 'Diploma', 5, '01123456726', 'nitiyan1206@testmail.com', NULL, 'JP'),
(36, 'MUHAMMAD DANISH ISHQAL BIN YUSRINZAM', '062715101260', '01DIT24F1207', 'Diploma', 5, '01123456727', 'danish1207@testmail.com', NULL, 'JKM'),
(37, 'AISHA ZUNAN BINTI ZOL @ ZOLKIFLI', '062822101261', '01DIT24F1208', 'Degree', 2, '01123456728', 'aishazunan1208@testmail.com', NULL, 'JMSK'),
(38, 'NURSYAMIEZA ATIQAH BINTI MAZLAN', '062931101262', '01DIT24F1209', 'Degree', 3, '01123456729', 'mieja16@testmail.com', NULL, 'JKA'),
(39, 'MUHAMMAD ISYRAF BIN MOHZANI', '063015101263', '01DIT24F1210', 'Diploma', 1, '01123456730', 'isyraf1210@testmail.com', NULL, 'JKP'),
(40, 'GOH JIAN QI', '063130101264', '01DIT24F1211', 'Degree', 5, '01123456731', 'jianqi1211@testmail.com', NULL, 'JTMK'),
(41, 'MOHAMMAD AMSYAR ADAM BIN SHARUDIN', '063245101265', '01DIT24F1220', 'Degree', 4, '01123456732', 'amsyar1220@testmail.com', NULL, 'JKE'),
(42, 'MUHAMMAD AZIB BIN MOHAMMAD HAIRUL NIZAM', '063312101266', '01DIT24F1223', 'Degree', 5, '01123456733', 'azib1223@testmail.com', NULL, 'JP'),
(43, 'ZAHIN HAFIZ BIN ZULKARNAN', '063423101267', '01DIT24F1226', 'Diploma', 4, '01123456734', 'zahin1226@testmail.com', NULL, 'JKE'),
(44, 'NURHUWAIDA BINTI ZAMRI', '063530101268', '01DIT24F1230', 'Degree', 1, '01123456735', 'waida07@testmail.com', NULL, 'JP'),
(45, 'SHAMEER DANIEL BIN SAFFUAN', '063615101269', '01DIT24F1232', 'Diploma', 2, '01123456736', 'shameer1232@testmail.com', NULL, 'JKP'),
(46, 'GANGASHINY A/P BALAMURUGAN', '063722101270', '01DIT24F1240', 'Diploma', 5, '01123456737', 'gangashiny1240@testmail.com', NULL, 'JKA'),
(47, 'MUHAMMAD FAIZ ASYRAF BIN ISSAHAMIAH', '063831101271', '	01DIT24F1246', 'Degree', 5, '01123456738', 'faiz1246@testmail.com', NULL, 'JKP'),
(48, 'MUHAMMAD HAZIQ BIN AZMAN', '064115101274', '01DIT24F1250', 'Diploma', 4, '01123456739', 'haziq1250@testmail.com', NULL, 'JMSK'),
(49, 'NURUL AMIRA BINTI ZAKARIA', '064223101275', '01DIT24F1252', 'Diploma', 2, '01123456740', 'amira1252@testmail.com', NULL, 'JKM'),
(50, 'MUHAMMAD IQBAL BIN FAUZI', '064512101278', '01DIT24F1258', 'Diploma', 3, '01123456743', 'iqbal1258@testmail.com', NULL, 'JKP'),
(51, 'NURUL NADIA BINTI RAHMAN', '064815101281', '01DIT24F1264', 'Degree', 5, '01123456746', 'nadia1264@testmail.com', NULL, 'JKE'),
(52, 'MUHAMMAD ARIF BIN ZAINAL', '065115101284', '01DIT24F1270', 'Diploma', 2, '01123456749', 'arif1270@testmail.com', NULL, 'JKM'),
(53, 'NURUL AIN BINTI KAMAL', '064623101279', '01DIT24F1260', 'Diploma', 1, '01123456744', 'ain1260@testmail.com', NULL, 'JKA'),
(54, 'VELVEROSA ALBERT BINTI MUHIZZIN', '065031101283', '01DIT24F1268', 'Degree', 3, '01123456748', 'vel24@testmail.com', NULL, 'JP'),
(55, 'MUHAMMAD DANIAL HAZIQ BIN YUSRIZAL', '060228070553', '01DKA24F1060', 'Diploma', 5, '0195708704', 'mdanialhaziq28@gmail.com', NULL, 'JKA'),
(56, 'WAN NUR AIN BT AZMI', '060620060652', '01DIT24F1231', 'Diploma', 5, '011-30615626', 'wn.farisha1@gmail.com', NULL, 'JTMK'),
(61, 'FARAH AMNI BT  RAUB', '060620060606', '01DIT24F1233', 'Diploma', 5, '011-30615620', 'wnnotes@gmail.com', NULL, 'JTMK'),
(62, 'FARAH  BT RIB', '060620064567', '01DIT24F1237', 'Diploma', 5, '01130615600', 'wnns@gmail.com', NULL, 'JKA'),
(63, 'WAN AMINAH BT AHMAD', '060524035638', '01DIT24F1111', 'Diploma', 5, '01130615600', 'wniss@gmail.com', NULL, 'JTMK'),
(64, 'MYA SAFIYA BINTI ZAID', '060818080734', '01DITS4F1080', 'Diploma', 2, '0123755417', 'mya08@gmail.com', 'user_64_1787816995.jpeg', 'JMSK'),
(65, 'WAN ALLYA BT MOHD RITHEER', '080726080912', '01DIT24F1021', 'Diploma', 1, '0199886339', 'wxnfxt1n@gmail.com', 'user_65_1788521679.png', 'JTMK');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcements_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`certificates_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `event_reminders`
--
ALTER TABLE `event_reminders`
  ADD PRIMARY KEY (`reminder_id`),
  ADD UNIQUE KEY `unique_reminder` (`event_id`,`user_id`,`reminder_type`);

--
-- Indexes for table `exemption_memos`
--
ALTER TABLE `exemption_memos`
  ADD PRIMARY KEY (`memo_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notifications_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`registration_id`);

--
-- Indexes for table `survey_answers`
--
ALTER TABLE `survey_answers`
  ADD PRIMARY KEY (`answer_id`);

--
-- Indexes for table `survey_confirmations`
--
ALTER TABLE `survey_confirmations`
  ADD PRIMARY KEY (`confirmation_id`),
  ADD UNIQUE KEY `unique_user_event` (`user_id`,`event_id`);

--
-- Indexes for table `survey_questions`
--
ALTER TABLE `survey_questions`
  ADD PRIMARY KEY (`question_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcements_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `certificates_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `event_reminders`
--
ALTER TABLE `event_reminders`
  MODIFY `reminder_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exemption_memos`
--
ALTER TABLE `exemption_memos`
  MODIFY `memo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notifications_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `survey_answers`
--
ALTER TABLE `survey_answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `survey_confirmations`
--
ALTER TABLE `survey_confirmations`
  MODIFY `confirmation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `survey_questions`
--
ALTER TABLE `survey_questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `exemption_memos`
--
ALTER TABLE `exemption_memos`
  ADD CONSTRAINT `exemption_memos_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE;
--
-- Database: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Table structure for table `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Table structure for table `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Table structure for table `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Table structure for table `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Table structure for table `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Table structure for table `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Table structure for table `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Dumping data for table `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"emss_db\",\"table\":\"admins\"},{\"db\":\"try\",\"table\":\"users\"},{\"db\":\"ems_db\",\"table\":\"admins\"},{\"db\":\"ems_db\",\"table\":\"users\"},{\"db\":\"ems_db\",\"table\":\"registrations\"},{\"db\":\"ems_db\",\"table\":\"notifications\"},{\"db\":\"ems_db\",\"table\":\"events\"},{\"db\":\"ems_db\",\"table\":\"certificates\"},{\"db\":\"ems_db\",\"table\":\"announcements\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Table structure for table `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Table structure for table `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Dumping data for table `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2026-07-30 07:04:04', '{\"Console\\/Mode\":\"collapse\",\"NavigationWidth\":324}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Table structure for table `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indexes for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indexes for table `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indexes for table `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indexes for table `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indexes for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indexes for table `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indexes for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indexes for table `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indexes for table `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indexes for table `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indexes for table `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indexes for table `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indexes for table `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
