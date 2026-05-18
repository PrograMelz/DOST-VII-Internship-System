-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2026 at 10:16 AM
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
-- Database: `internship_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Rommel Del Rosario', 'mel@gmail.com', NULL, '$2y$12$4G.9Vqi3S9W1So5dMeIa8Ozwv8t10wnCUKAn0/tCgocOv.RmjJlcS', NULL, NULL, '2026-03-09 08:20:36');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_logs`
--

CREATE TABLE `attendance_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `intern_id` bigint(20) UNSIGNED NOT NULL,
  `scan_time` datetime NOT NULL,
  `scan_type` enum('IN','OUT') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance_logs`
--

INSERT INTO `attendance_logs` (`id`, `intern_id`, `scan_time`, `scan_type`) VALUES
(1, 1, '2026-03-02 08:00:00', 'IN'),
(2, 1, '2026-03-02 12:00:00', 'OUT'),
(3, 1, '2026-03-02 13:00:00', 'IN'),
(4, 1, '2026-03-02 17:00:00', 'OUT'),
(39, 2, '2026-03-16 07:42:33', 'IN'),
(41, 2, '2026-03-16 12:00:00', 'OUT'),
(43, 2, '2026-03-16 12:55:24', 'IN'),
(45, 2, '2026-03-16 17:00:00', 'OUT'),
(47, 2, '2026-03-17 07:00:00', 'IN'),
(48, 2, '2026-03-25 08:35:16', 'IN'),
(117, 1, '2026-03-17 07:00:00', 'IN'),
(118, 1, '2026-03-17 12:00:00', 'OUT'),
(119, 1, '2026-03-17 13:00:00', 'IN'),
(120, 1, '2026-03-17 18:00:00', 'OUT'),
(137, 1, '2026-04-01 07:00:00', 'IN'),
(138, 1, '2026-04-01 12:00:00', 'OUT'),
(139, 1, '2026-04-01 13:00:00', 'IN'),
(140, 1, '2026-04-01 18:00:00', 'OUT'),
(141, 1, '2026-04-06 07:00:00', 'IN'),
(142, 1, '2026-04-06 12:00:00', 'OUT'),
(143, 1, '2026-04-06 13:00:00', 'IN'),
(144, 1, '2026-04-06 18:00:00', 'OUT'),
(145, 1, '2026-04-07 07:00:00', 'IN'),
(146, 1, '2026-04-07 12:00:00', 'OUT'),
(147, 1, '2026-04-07 13:00:00', 'IN'),
(148, 1, '2026-04-07 18:00:00', 'OUT'),
(149, 1, '2026-03-03 07:47:00', 'IN'),
(150, 1, '2026-03-03 12:00:00', 'OUT'),
(151, 1, '2026-03-03 13:00:00', 'IN'),
(152, 1, '2026-03-03 17:02:00', 'OUT'),
(157, 1, '2026-03-04 07:51:00', 'IN'),
(158, 1, '2026-03-04 12:00:00', 'OUT'),
(159, 1, '2026-03-04 12:55:00', 'IN'),
(160, 1, '2026-03-04 17:00:00', 'OUT'),
(161, 1, '2026-03-05 07:50:00', 'IN'),
(162, 1, '2026-03-05 12:00:00', 'OUT'),
(163, 1, '2026-03-05 13:00:00', 'IN'),
(164, 1, '2026-03-05 17:10:00', 'OUT'),
(165, 1, '2026-03-06 07:45:00', 'IN'),
(166, 1, '2026-03-06 12:02:00', 'OUT'),
(167, 1, '2026-03-06 12:45:00', 'IN'),
(168, 1, '2026-03-06 17:00:00', 'OUT'),
(169, 1, '2026-03-09 07:55:00', 'IN'),
(170, 1, '2026-03-09 12:00:00', 'OUT'),
(171, 1, '2026-03-09 12:35:00', 'IN'),
(172, 1, '2026-03-09 17:00:00', 'OUT'),
(173, 1, '2026-03-10 07:56:00', 'IN'),
(174, 1, '2026-03-10 12:00:00', 'OUT'),
(175, 1, '2026-03-10 12:30:00', 'IN'),
(176, 1, '2026-03-10 18:00:00', 'OUT'),
(177, 1, '2026-03-11 06:40:00', 'IN'),
(178, 1, '2026-03-11 12:00:00', 'OUT'),
(179, 1, '2026-03-11 12:30:00', 'IN'),
(180, 1, '2026-03-11 18:00:00', 'OUT'),
(181, 1, '2026-03-12 06:59:00', 'IN'),
(182, 1, '2026-03-12 12:00:00', 'OUT'),
(183, 1, '2026-03-12 12:49:00', 'IN'),
(184, 1, '2026-03-12 18:02:00', 'OUT'),
(185, 1, '2026-03-16 07:00:00', 'IN'),
(186, 1, '2026-03-16 12:03:00', 'OUT'),
(187, 1, '2026-03-16 13:00:00', 'IN'),
(188, 1, '2026-03-16 18:00:00', 'OUT'),
(189, 1, '2026-03-18 06:58:00', 'IN'),
(190, 1, '2026-03-18 12:00:00', 'OUT'),
(191, 1, '2026-03-18 13:00:00', 'IN'),
(192, 1, '2026-03-18 18:00:00', 'OUT'),
(193, 1, '2026-03-19 06:55:00', 'IN'),
(194, 1, '2026-03-19 12:00:00', 'OUT'),
(195, 1, '2026-03-19 13:00:00', 'IN'),
(196, 1, '2026-03-19 18:00:00', 'OUT'),
(201, 1, '2026-03-23 07:47:00', 'IN'),
(202, 1, '2026-03-23 12:01:00', 'OUT'),
(203, 1, '2026-03-23 13:00:00', 'IN'),
(204, 1, '2026-03-23 17:05:00', 'OUT'),
(209, 1, '2026-03-24 07:57:00', 'IN'),
(210, 1, '2026-03-24 12:04:00', 'OUT'),
(211, 1, '2026-03-24 13:00:00', 'IN'),
(212, 1, '2026-03-24 17:05:00', 'OUT'),
(213, 1, '2026-03-25 08:00:00', 'IN'),
(214, 1, '2026-03-25 12:00:00', 'OUT'),
(215, 1, '2026-03-25 12:57:00', 'IN'),
(216, 1, '2026-03-25 17:00:00', 'OUT'),
(217, 1, '2026-03-26 07:58:00', 'IN'),
(218, 1, '2026-03-26 12:00:00', 'OUT'),
(219, 1, '2026-03-26 13:00:00', 'IN'),
(220, 1, '2026-03-26 18:00:00', 'OUT'),
(221, 1, '2026-03-30 06:59:00', 'IN'),
(222, 1, '2026-03-30 12:00:00', 'OUT'),
(223, 1, '2026-03-30 13:00:00', 'IN'),
(224, 1, '2026-03-30 18:00:00', 'OUT'),
(225, 1, '2026-03-31 06:55:00', 'IN'),
(226, 1, '2026-03-31 12:00:00', 'OUT'),
(227, 1, '2026-03-31 13:00:00', 'IN'),
(228, 1, '2026-03-31 18:00:00', 'OUT');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-boost.roster.scan', 'a:2:{s:6:\"roster\";O:21:\"Laravel\\Roster\\Roster\":3:{s:13:\"\0*\0approaches\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:7:{i:0;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^12.0\";s:10:\"\0*\0package\";E:37:\"Laravel\\Roster\\Enums\\Packages:LARAVEL\";s:14:\"\0*\0packageName\";s:17:\"laravel/framework\";s:10:\"\0*\0version\";s:7:\"12.53.0\";s:6:\"\0*\0dev\";b:0;}i:1;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:7:\"v0.3.13\";s:10:\"\0*\0package\";E:37:\"Laravel\\Roster\\Enums\\Packages:PROMPTS\";s:14:\"\0*\0packageName\";s:15:\"laravel/prompts\";s:10:\"\0*\0version\";s:6:\"0.3.13\";s:6:\"\0*\0dev\";b:0;}i:2;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:6:\"v0.5.9\";s:10:\"\0*\0package\";E:33:\"Laravel\\Roster\\Enums\\Packages:MCP\";s:14:\"\0*\0packageName\";s:11:\"laravel/mcp\";s:10:\"\0*\0version\";s:5:\"0.5.9\";s:6:\"\0*\0dev\";b:1;}i:3;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.24\";s:10:\"\0*\0package\";E:34:\"Laravel\\Roster\\Enums\\Packages:PINT\";s:14:\"\0*\0packageName\";s:12:\"laravel/pint\";s:10:\"\0*\0version\";s:6:\"1.27.1\";s:6:\"\0*\0dev\";b:1;}i:4;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.41\";s:10:\"\0*\0package\";E:34:\"Laravel\\Roster\\Enums\\Packages:SAIL\";s:14:\"\0*\0packageName\";s:12:\"laravel/sail\";s:10:\"\0*\0version\";s:6:\"1.53.0\";s:6:\"\0*\0dev\";b:1;}i:5;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^11.5.3\";s:10:\"\0*\0package\";E:37:\"Laravel\\Roster\\Enums\\Packages:PHPUNIT\";s:14:\"\0*\0packageName\";s:15:\"phpunit/phpunit\";s:10:\"\0*\0version\";s:7:\"11.5.55\";s:6:\"\0*\0dev\";b:1;}i:6;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:10:\"\0*\0package\";E:41:\"Laravel\\Roster\\Enums\\Packages:TAILWINDCSS\";s:14:\"\0*\0packageName\";s:11:\"tailwindcss\";s:10:\"\0*\0version\";s:5:\"4.2.1\";s:6:\"\0*\0dev\";b:1;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:21:\"\0*\0nodePackageManager\";E:43:\"Laravel\\Roster\\Enums\\NodePackageManager:NPM\";}s:9:\"timestamp\";i:1773626891;}', 1773713291);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `holiday_date` date NOT NULL,
  `holiday_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `holidays`
--

INSERT INTO `holidays` (`id`, `holiday_date`, `holiday_name`) VALUES
(2, '2026-03-20', 'Eid al-Fitr'),
(3, '2026-05-01', 'Labor Day'),
(9, '2026-03-13', 'No Work'),
(11, '2026-03-27', 'No Work'),
(12, '2026-04-03', 'No Work'),
(14, '2026-04-10', 'No Work'),
(15, '2026-04-17', 'No Work'),
(16, '2026-04-24', 'No Work'),
(17, '2026-05-08', 'No Work'),
(18, '2026-05-15', 'No Work'),
(19, '2026-05-22', 'No Work'),
(20, '2026-05-29', 'No Work'),
(21, '2026-06-05', 'No Work'),
(22, '2026-06-12', 'No Work'),
(23, '2026-03-20', 'No Work'),
(24, '2026-04-02', 'Maundy Thursday'),
(25, '2026-04-03', 'Good Friday'),
(26, '2026-04-04', 'Black Saturday'),
(27, '2026-04-05', 'Easter Sunday'),
(28, '2026-04-09', 'The Day of Valor'),
(29, '2026-05-01', 'No Work'),
(30, '2026-05-27', 'Eid al-Adha'),
(31, '2026-05-28', 'Eid al-Adha Day 2'),
(32, '2026-06-12', 'Independence Day'),
(33, '2026-06-17', 'Amun Jadid');

-- --------------------------------------------------------

--
-- Table structure for table `interns`
--

CREATE TABLE `interns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `qr_code` varchar(20) NOT NULL,
  `att_code` varchar(4) DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `school_id` varchar(50) NOT NULL,
  `school_name` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `birthday` date NOT NULL,
  `sex` enum('male','female') NOT NULL,
  `ojt_hours_required` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `interns`
--

INSERT INTO `interns` (`id`, `username`, `password_hash`, `qr_code`, `att_code`, `first_name`, `middle_name`, `last_name`, `school_id`, `school_name`, `email`, `phone_number`, `birthday`, `sex`, `ojt_hours_required`, `created_at`) VALUES
(1, 'mel2004', '$2y$12$.lwjxYEge96lFGorD14AMuuYVsrPEcAIB/i6aOmmur.KpasBCEQz2', 'INTRNiwrJ219', NULL, 'Rommel', 'Mesares', 'Del Rosario', '2022-63047', 'Eastern Visayas State University Dulag Campus', 'rommel.delrosario@evsu.edu.ph', '09858609832', '2004-03-07', 'male', 486, '2026-03-09 14:27:21'),
(2, 'user_69af6d5e19659', '$2y$12$3xAWpMYugoD34Uxy4tO9QOgwjY3.CNUHsNerZ8o2nMfgV43Nog.5.', 'qr_69af6d5f2b05c', NULL, 'Kristoeffer Rey', 'Ubarco', 'Rovembe', '2022-63104', 'Eastern Visayas State University Dulag Campus', 'toeff@gmail.com', '09858609832', '1999-01-22', 'male', 486, '2026-03-10 09:01:19'),
(6, 'Kimjay2004_ydo', '$2y$12$Ca4ft5ualyQRN7OIzao.POfe/3aBkxKpE5xbEGXC66T8ojfvf8uN.', 'INTRNyqWE97108', NULL, 'Kimjay', 'Setosta', 'Maceda', '2022-63073', 'Eastern Visayas State University Dulag Campus', 'kimjay.maceda@evsu.edu.ph', '09858609821', '2004-01-16', 'male', 486, '2026-03-16 11:08:48');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '0001_01_01_000003_create_interns_table', 1),
(5, '0001_01_01_000004_create_attendance_logs_table', 1),
(6, '0001_01_01_000005_create_system_logs_table', 1),
(7, '0001_01_01_000006_create_system_settings_table', 1),
(8, '0001_01_01_000007_create_holidays_table', 1),
(9, '2026_03_10_000007_add_text_colors_to_system_settings_table', 2),
(10, '2026_03_13_000001_add_ojt_hours_required_to_interns_table', 3),
(11, '2026_03_14_065709_add_att_code_to_interns_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1MXfhZkuIex3KhqUt4C3wK8jAzpCCjaWJ4GF7Zks', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiV0NqNDRXR2JTSHdPSFlCRTdKTjhCOVFoSk9NMHJGYnN1S3ZLZXpuRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9pbnRlcm4vZm9yZ290LXBhc3N3b3JkIjtzOjU6InJvdXRlIjtzOjIyOiJpbnRlcm4uZm9yZ290LXBhc3N3b3JkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777249751),
('mbsWFItBCauhR92VdxhGLJUmEhlATBrk35uf21we', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZW1nSzU0ZW94Y3NqbUNheDM0OENzWXNiM2tvdzNIUGZvRHd3R2t5diI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTk4OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZHRyP2FtX29mZmljaWFsX2Fycml2YWw9MDglM0EwMCZhbV9vZmZpY2lhbF9kZXBhcnR1cmU9MTIlM0EwMCZpbmNoYXJnZT1HYXJ5JTIwVmFsZW5jaWFubyZpbnRlcm5faWQ9MSZtb250aD0zJnBtX29mZmljaWFsX2Fycml2YWw9MTMlM0EwMCZwbV9vZmZpY2lhbF9kZXBhcnR1cmU9MTclM0EwMCZ5ZWFyPTIwMjYiO3M6NToicm91dGUiO3M6MTI6ImR0ci5nZW5lcmF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6OToiaW50ZXJuX2lkIjtpOjE7fQ==', 1779090396),
('wx50iCoMGpwSVQai6ilcZ2EmttddCCQkXkjN4pB6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaTJzOFJybkhNM2FaNGs1OUpkdlpUNkdrVHg2c0lmWnpWRnJ4RWFUZCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL2ludGVybi8xIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9pbnRlcm4vbG9naW4iO3M6NToicm91dGUiO3M6MTI6ImludGVybi5sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1775635510);

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_type` enum('admin','intern') NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `system_short_name` varchar(20) NOT NULL,
  `system_long_name` varchar(150) NOT NULL,
  `primary_color` varchar(7) NOT NULL,
  `secondary_color` varchar(7) DEFAULT NULL,
  `button_color` varchar(7) DEFAULT NULL,
  `heading_text_color` varchar(7) DEFAULT NULL,
  `body_text_color` varchar(7) DEFAULT NULL,
  `system_logo` varchar(255) DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `system_short_name`, `system_long_name`, `primary_color`, `secondary_color`, `button_color`, `heading_text_color`, `body_text_color`, `system_logo`, `updated_at`) VALUES
(1, 'DOST VII - IAS', 'DOST VII Intern Attendance System', '#005494', '#fff4e0', '#750000', '#750000', '#000000', 'storage/system-logos/vHTR1UUq83sNOQUily5VG9ZfQBmXalsQqzX7n5RF.png', '2026-03-09 14:02:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_email_unique` (`email`);

--
-- Indexes for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendance_logs_intern_id_scan_time_index` (`intern_id`,`scan_time`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `interns`
--
ALTER TABLE `interns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `interns_username_unique` (`username`),
  ADD UNIQUE KEY `interns_qr_code_unique` (`qr_code`),
  ADD UNIQUE KEY `interns_email_unique` (`email`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `interns`
--
ALTER TABLE `interns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD CONSTRAINT `attendance_logs_intern_id_foreign` FOREIGN KEY (`intern_id`) REFERENCES `interns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `admin` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
