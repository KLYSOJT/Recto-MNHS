-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 18, 2026 at 01:26 AM
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
-- Database: `rmnhs`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE IF NOT EXISTS `announcement` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `image` varchar(255) NOT NULL,
  `announcement_posts` text NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `image` varchar(255) NOT NULL,
  `news_posts` text NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `featured_videos`
--

CREATE TABLE IF NOT EXISTS `featured_videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `description` text,
  `filename` varchar(255) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `school_memorandum`
--

CREATE TABLE IF NOT EXISTS `school_memorandum` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `description` text,
  `file` varchar(255),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `division_memorandum`
--

CREATE TABLE IF NOT EXISTS `division_memorandum` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `description` text,
  `file` varchar(255),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `deped_order`
--

CREATE TABLE IF NOT EXISTS `deped_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `description` text,
  `file` varchar(255),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `research`
--

CREATE TABLE IF NOT EXISTS `research` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `grade` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL,
  `year` varchar(4) NOT NULL,
  `category` varchar(100) NOT NULL,
  `image` varchar(255),
  `file` varchar(255),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transparency`
--

CREATE TABLE IF NOT EXISTS `transparency` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `description` text,
  `file` varchar(255),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learning_materials`
--

CREATE TABLE IF NOT EXISTS `learning_materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `grade` varchar(20) NOT NULL,
  `subject` varchar(150) NOT NULL,
  `file` varchar(255) NOT NULL,
  `path` varchar(500) NOT NULL,
  `filesize` int(11) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `organizational_structure`
--

CREATE TABLE IF NOT EXISTS `organizational_structure` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `department` varchar(100) NOT NULL UNIQUE,
  `mime` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `pdf` varchar(255) DEFAULT NULL,
  `pdf_mime` varchar(100) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sample Data for Testing
--

INSERT INTO `announcement` (`image`, `announcement_posts`, `created_at`) VALUES
('', 'Welcome to RMNHS! This is a sample announcement. Etiam et mi pulvinar eros interdum porttitor id sit amet nunc. Sed sollicitudin mi nisi, sed euismod turpis pellentesque in.', NOW()),
('', 'Important: School events coming this month. Please check your email for updates and guidelines.', NOW() - INTERVAL 1 DAY);

INSERT INTO `news` (`image`, `news_posts`, `created_at`) VALUES
('', 'Badminton Championship held last week with amazing performances from all participants.', NOW()),
('', 'Dance Sports Competition - Higit 45 na mag-aaral ang lalahok sa RMNHS Dancesports Competition.', NOW() - INTERVAL 1 DAY);

COMMIT;
  
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


-- Migration: Add PDF support to organizational_structure table
-- Run this SQL in phpMyAdmin or MySQL console to add PDF columns

ALTER TABLE `organizational_structure` 
ADD COLUMN `pdf` VARCHAR(255) DEFAULT NULL AFTER `image`,
ADD COLUMN `pdf_mime` VARCHAR(100) DEFAULT NULL AFTER `pdf`;

-- These columns will store:
-- pdf: The filename of the PDF document
-- pdf_mime: The MIME type of the PDF (usually application/pdf)

-- --------------------------------------------------------

-- Table structure for table `recognized_organization`
--

CREATE TABLE IF NOT EXISTS `recognized_organization` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `org_name` varchar(255) NOT NULL,
  `date_established` date DEFAULT NULL,
  `adviser_name` varchar(255) DEFAULT NULL,
  `mime` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `pdf` varchar(255) DEFAULT NULL,
  `pdf_mime` varchar(100) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
