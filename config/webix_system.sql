-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 07, 2025 at 05:02 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webix_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` enum('Create','View','Update','Delete','Login','Logout','Other') NOT NULL,
  `location` varchar(100) NOT NULL COMMENT 'Table Name or Module Name',
  `record_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_deleted` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `location`, `record_id`, `description`, `ip_address`, `user_agent`, `created_at`, `is_deleted`) VALUES
(1, 1, 'View', 'Users Management', NULL, 'viewed users list', '39.40.28.216', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-28 22:46:00', 0),
(2, 1, 'Logout', 'System', NULL, 'logged out', NULL, NULL, '2025-07-28 22:46:03', 0),
(3, 1, 'Login', 'System', NULL, 'logged in', '39.40.28.216', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-28 22:46:04', 0),
(4, 1, 'View', 'Users Management', NULL, 'viewed users list', '39.40.28.216', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-28 22:48:18', 0),
(5, 1, 'View', 'Users Management', NULL, 'viewed users list', '39.40.28.216', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-28 22:48:24', 0),
(6, 1, 'View', 'Users Management', NULL, 'viewed users list', '39.40.28.216', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-28 22:48:29', 0),
(7, 1, 'Logout', 'System', NULL, 'logged out', NULL, NULL, '2025-07-28 22:48:48', 0),
(8, 1, 'Login', 'System', NULL, 'logged in', '39.40.46.37', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-29 22:25:39', 0),
(9, 1, 'View', 'Users Management', NULL, 'viewed users list', '39.40.46.37', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-29 22:26:04', 0),
(10, 1, 'View', 'Users Management', NULL, 'viewed users list', NULL, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-29 22:27:32', 0),
(11, 1, 'View', 'Users Management', NULL, 'viewed users list', NULL, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-29 22:30:25', 0),
(12, 1, 'Logout', 'System', NULL, 'logged out', NULL, NULL, '2025-07-29 22:30:35', 0),
(13, 1, 'Login', 'System', NULL, 'logged in', '39.40.46.37', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-29 23:29:52', 0),
(14, 1, 'View', 'Users Management', NULL, 'viewed users list', '39.40.46.37', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-29 23:30:12', 0),
(15, 1, 'Logout', 'System', NULL, 'logged out', NULL, NULL, '2025-07-29 23:30:31', 0),
(16, 1, 'Login', 'System', NULL, 'logged in', '39.40.46.37', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-29 23:35:32', 0),
(17, 1, 'View', 'Users Management', NULL, 'viewed users list', '39.40.46.37', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-29 23:35:54', 0),
(18, 1, 'View', 'Users Management', NULL, 'viewed users list', '39.40.46.37', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-29 23:37:26', 0),
(19, 1, 'View', 'Users Management', NULL, 'viewed users list', NULL, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-29 23:39:03', 0),
(20, 1, 'View', 'Users Management', NULL, 'viewed users list', NULL, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-29 23:39:04', 0),
(21, 1, 'View', 'Users Management', NULL, 'viewed users list', NULL, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-29 23:39:05', 0),
(22, 1, 'View', 'Users Management', NULL, 'viewed users list', '39.40.46.37', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-29 23:39:12', 0),
(23, 1, 'View', 'Users Management', NULL, 'viewed users list', '39.40.46.37', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-29 23:39:17', 0),
(24, 1, 'View', 'Users Management', NULL, 'viewed users list', '39.40.46.37', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-29 23:45:10', 0),
(25, 1, 'View', 'Users Management', NULL, 'viewed users list', '39.40.46.37', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-29 23:48:12', 0),
(26, 1, 'Delete', 'Users Management', NULL, 'deleted user', '39.40.46.37', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-30 00:01:06', 0),
(27, 1, 'View', 'Users Management', NULL, 'viewed users list', '39.40.46.37', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-30 00:01:07', 0),
(28, 1, 'Logout', 'System', NULL, 'logged out', NULL, NULL, '2025-07-30 00:04:38', 0),
(29, 1, 'Login', 'System', NULL, 'logged in', '39.40.46.37', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-30 00:04:39', 0),
(30, 1, 'Logout', 'System', NULL, 'logged out', NULL, NULL, '2025-07-30 16:05:50', 0),
(31, 1, 'Login', 'System', NULL, 'logged in', '39.40.50.84', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-30 18:28:40', 0),
(32, 1, 'Logout', 'System', NULL, 'logged out', NULL, NULL, '2025-07-30 18:54:46', 0),
(33, 1, 'Login', 'System', NULL, 'logged in', '39.40.50.84', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-30 19:00:17', 0),
(36, 1, 'Update', 'Users Management', NULL, 'updated status for role id 1 to 0', '39.40.50.84', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-30 20:23:57', 0),
(37, 1, 'Update', 'Users Management', NULL, 'edited role Admin', '39.40.26.238', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-30 21:21:13', 0),
(38, 1, 'Update', 'Users Management', NULL, 'updated status for role id 1 to 1', '206.84.169.30', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-31 09:37:40', 0),
(39, 1, 'Update', 'Users Management', NULL, 'edited role Admin', '206.84.169.30', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-31 09:39:07', 0),
(40, 1, 'Update', 'Users Management', NULL, 'edited role Admin', '206.84.169.30', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-31 09:39:11', 0),
(41, 1, 'Update', 'Users Management', NULL, 'edited role Admin', '206.84.169.30', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-31 09:39:18', 0),
(42, 1, 'Update', 'Users Management', NULL, 'edited role Admin', '206.84.169.30', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-31 09:39:45', 0),
(43, 1, 'Update', 'Users Management', NULL, 'edited role Admin', '206.84.169.30', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-31 09:39:56', 0),
(44, 1, 'Logout', 'System', NULL, 'logged out', NULL, NULL, '2025-07-31 09:40:45', 0),
(45, 1, 'Login', 'System', NULL, 'logged in', '39.58.142.82', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-31 18:42:25', 0),
(46, 1, 'Update', 'Users Management', NULL, 'updated status for role id 1 to 0', '39.58.142.82', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-31 18:42:52', 0),
(47, 1, 'Update', 'Users Management', NULL, 'updated status for role id 1 to 1', '39.58.142.82', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-31 18:43:15', 0),
(48, 1, 'Update', 'Users Management', NULL, 'edited role Admin', '39.58.142.82', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-31 18:43:19', 0),
(49, 1, 'Update', 'Users Management', NULL, 'edited role Admin', '154.192.244.27', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-01 09:13:13', 0),
(50, 1, 'Logout', 'System', NULL, 'logged out', NULL, NULL, '2025-08-01 09:25:20', 0),
(51, 1, 'Login', 'System', NULL, 'logged in', '154.192.244.27', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-01 10:46:24', 0),
(52, 1, 'Logout', 'System', NULL, 'logged out', NULL, NULL, '2025-08-01 10:56:42', 0),
(53, 1, 'Login', 'System', NULL, 'logged in', '39.40.57.66', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-01 20:13:09', 0),
(54, 1, 'Update', 'Users Management', NULL, 'updated permissions for role 1 module 1', '39.40.23.120', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-01 20:53:46', 0),
(55, 1, 'Update', 'Users Management', NULL, 'updated permissions for role 1 module 1', '39.40.23.120', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-01 20:53:47', 0),
(56, 1, 'Update', 'Users Management', NULL, 'updated permissions for role 1 module 1', '39.40.23.120', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-01 20:53:47', 0),
(57, 1, 'Update', 'Users Management', NULL, 'updated permissions for role 1 module 1', '39.40.23.120', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-01 20:53:48', 0),
(58, 1, 'Login', 'System', NULL, 'logged in', '39.40.30.188', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 19:53:05', 0),
(59, 1, 'Logout', 'System', NULL, 'logged out', NULL, NULL, '2025-08-06 20:42:33', 0);

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `app_color` varchar(50) DEFAULT 'default',
  `theme_mode` varchar(20) DEFAULT 'light',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`id`, `user_id`, `app_color`, `theme_mode`, `created_at`, `updated_at`) VALUES
(1, 1, 'theme-cyan', 'dark', '2025-07-07 13:15:55', '2025-07-10 14:11:03');

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `role` enum('unknown','admin','teacher','student','parent','staff') NOT NULL,
  `login_time` datetime NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `remarks` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `username`, `role`, `login_time`, `ip_address`, `user_agent`, `status`, `remarks`) VALUES
(3, NULL, 'superadmin@example.com', 'unknown', '2025-06-29 14:15:31', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 0, 'Username not found'),
(4, 1, 'admin', 'admin', '2025-06-29 14:20:31', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 0, 'Incorrect password'),
(5, 1, 'admin', 'admin', '2025-06-29 14:46:18', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 1, 'Login successful'),
(6, 1, 'admin', 'admin', '2025-06-29 20:13:33', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 1, 'Login successful'),
(7, 1, 'admin', 'admin', '2025-06-30 13:37:43', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 1, 'Login successful'),
(8, 1, 'admin', 'admin', '2025-06-30 17:17:53', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 1, 'Login successful'),
(9, 1, 'admin', 'admin', '2025-07-01 09:51:35', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 1, 'Login successful'),
(10, 1, 'admin', 'admin', '2025-07-01 17:43:53', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 1, 'Login successful'),
(11, 1, 'admin', 'admin', '2025-07-01 17:51:09', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 1, 'Login successful'),
(12, 1, 'admin', 'admin', '2025-07-01 20:02:24', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 1, 'Login successful'),
(13, 1, 'admin', 'admin', '2025-07-03 09:00:52', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 1, 'Login successful');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `react_icon` text DEFAULT NULL,
  `color` text DEFAULT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `current_status` int(11) NOT NULL DEFAULT 1 COMMENT '1. Active\r\n2. Maintenance\r\n3. Restricted',
  `description` text DEFAULT NULL,
  `last_updated_at` timestamp NULL DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_deleted` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `name`, `link`, `icon`, `react_icon`, `color`, `parent_id`, `sort_order`, `is_active`, `current_status`, `description`, `last_updated_at`, `created_at`, `updated_at`, `id_deleted`) VALUES
(1, 'Users', 'users', 'fa fa-users-cog', 'MdPeople', '#e6eaff', NULL, 1, 1, 1, 'Manage users, roles, and permissions across the system.', '2025-07-23 15:08:54', '2025-06-29 19:35:04', '2025-07-24 19:10:21', 0),
(2, 'Academic', 'academics', 'fa fa-book', 'MdSchool', '#e6f7f7', NULL, 1, 1, 1, 'Academic records, courses, and student management.', '2025-07-23 15:08:54', '2025-07-10 18:49:11', '2025-07-24 19:10:27', 0),
(3, 'LMS', 'lms', 'fa fa-graduation-cap', 'MdLibraryBooks', '#f6eaff', NULL, 2, 1, 1, 'Learning Management System for online education.', '2025-07-23 15:08:54', '2025-07-10 18:49:11', '2025-07-24 19:10:31', 0),
(4, 'Finance', 'finance', 'fa fa-dollar-sign', 'MdAttachMoney', '#eafff6', NULL, 3, 1, 1, 'Financial operations, billing, and reporting.', '2025-07-23 15:08:54', '2025-07-10 18:49:11', '2025-07-24 19:10:37', 0),
(5, 'HRM', 'hrm', 'fa fa-users', 'MdBusinessCenter', '#fffbe6', NULL, 4, 1, 1, 'Customer Relationship Management and communication.', '2025-07-23 15:08:54', '2025-07-10 18:49:11', '2025-07-24 19:10:42', 0),
(6, 'CRM', 'crm', 'fa fa-address-book', 'MdBusinessCenter', '#eafffa', NULL, 5, 1, 1, 'Customer Relationship Management and communication.', '2025-07-23 15:08:54', '2025-07-10 18:49:11', '2025-07-24 19:10:50', 0),
(7, 'Config', 'config', 'fa fa-cogs', 'MdSettings', '#f6f6fa', NULL, 6, 1, 1, 'System settings and administrative tools.', '2025-07-23 15:08:54', '2025-07-10 18:49:11', '2025-07-24 19:10:57', 0),
(8, 'Users', 'users', 'fa fa-user', NULL, NULL, 1, 2, 1, 1, NULL, NULL, '2025-06-29 19:37:01', '2025-07-21 23:51:32', 0),
(9, 'Roles & Permissions', 'permissions', 'fa fa-user-shield', NULL, NULL, 1, 1, 1, 1, NULL, NULL, '2025-06-29 19:37:01', '2025-07-27 14:34:58', 0),
(17, 'Students', 'students', 'fa fa-user-graduate', NULL, NULL, 2, 1, 1, 1, NULL, NULL, '2025-07-10 18:56:48', '2025-07-28 10:36:21', 0),
(18, 'Teachers', 'teachers', 'fa fa-chalkboard-teacher', NULL, NULL, 2, 2, 1, 1, NULL, NULL, '2025-07-10 18:56:48', '2025-07-28 10:36:24', 0),
(19, 'Courses', 'courses', 'fa fa-book-open', NULL, NULL, 2, 3, 1, 1, NULL, NULL, '2025-07-10 18:56:48', '2025-07-28 10:36:34', 0),
(20, 'Subjects', 'subjects', 'fa fa-layer-group', NULL, NULL, 2, 4, 1, 1, NULL, NULL, '2025-07-10 18:56:48', '2025-07-28 10:36:37', 0),
(21, 'Batches', 'batches', 'fa fa-users-class', NULL, NULL, 2, 5, 1, 1, NULL, NULL, '2025-07-10 18:56:48', '2025-07-28 10:36:41', 0),
(22, 'Classes', 'classes', 'fa fa-school', NULL, NULL, 2, 6, 1, 1, NULL, NULL, '2025-07-10 18:56:48', '2025-07-28 10:36:46', 0),
(23, 'Enrollment', 'enrollment', 'fa fa-user-plus', NULL, NULL, 2, 7, 1, 1, NULL, NULL, '2025-07-10 18:56:48', '2025-07-28 10:36:50', 0),
(24, 'Exams', 'exmans', 'fa fa-pencil-alt', NULL, NULL, 2, 8, 1, 1, NULL, NULL, '2025-07-10 18:56:48', '2025-07-28 10:36:53', 0),
(25, 'Results', 'results', 'fa fa-chart-line', NULL, NULL, 2, 9, 1, 1, NULL, NULL, '2025-07-10 18:56:48', '2025-07-28 10:36:56', 0),
(26, 'Attendance', 'attendance', 'fa fa-calendar-check', NULL, NULL, 2, 10, 1, 1, NULL, NULL, '2025-07-10 18:56:48', '2025-07-28 10:37:00', 0),
(27, 'Online Courses', 'online-courses', 'fa fa-play-circle', NULL, NULL, 3, 1, 1, 1, NULL, NULL, '2025-07-10 18:57:51', '2025-07-28 10:37:11', 0),
(28, 'Assignments', 'assignments', 'fa fa-tasks', NULL, NULL, 3, 2, 1, 1, NULL, NULL, '2025-07-10 18:57:51', '2025-07-28 10:37:15', 0),
(29, 'Submissions', 'submissions', 'fa fa-upload', NULL, NULL, 3, 3, 1, 1, NULL, NULL, '2025-07-10 18:57:51', '2025-07-28 10:37:18', 0),
(30, 'Quizzes', 'quizzes', 'fa fa-question-circle', NULL, NULL, 3, 4, 1, 1, NULL, NULL, '2025-07-10 18:57:51', '2025-07-28 10:37:24', 0),
(31, 'Certificates', 'certificates', 'fa fa-certificate', NULL, NULL, 3, 5, 1, 1, NULL, NULL, '2025-07-10 18:57:51', '2025-07-28 10:37:29', 0),
(32, 'Fees', 'fees', 'fa fa-coins', NULL, NULL, 4, 1, 1, 1, NULL, NULL, '2025-07-10 18:58:38', '2025-07-28 10:13:33', 0),
(33, 'Invoices', 'invoices', 'fa fa-file-invoice', NULL, NULL, 4, 2, 1, 1, NULL, NULL, '2025-07-10 18:58:38', '2025-07-28 10:33:24', 0),
(34, 'Payments', 'payments', 'fa fa-credit-card', NULL, NULL, 4, 3, 1, 1, NULL, NULL, '2025-07-10 18:58:38', '2025-07-28 10:33:27', 0),
(35, 'Expenses', 'expenses', 'fa fa-receipt', NULL, NULL, 4, 4, 1, 1, NULL, NULL, '2025-07-10 18:58:38', '2025-07-28 10:33:30', 0),
(36, 'Vendors', 'vendors', 'fa fa-truck', NULL, NULL, 4, 5, 1, 1, NULL, NULL, '2025-07-10 18:58:38', '2025-07-28 10:33:34', 0),
(37, 'Employees', 'employees', 'fa fa-id-card', NULL, NULL, 5, 1, 1, 1, NULL, NULL, '2025-07-10 18:59:05', '2025-07-28 10:33:38', 0),
(38, 'Attendance', 'attendance', 'fa fa-user-check', NULL, NULL, 5, 2, 1, 1, NULL, NULL, '2025-07-10 18:59:05', '2025-07-28 10:33:41', 0),
(39, 'Leaves', 'leaves', 'fa fa-plane-departure', NULL, NULL, 5, 3, 1, 1, NULL, NULL, '2025-07-10 18:59:05', '2025-07-28 10:33:43', 0),
(40, 'Payroll', 'payroll', 'fa fa-money-check-alt', NULL, NULL, 5, 4, 1, 1, NULL, NULL, '2025-07-10 18:59:05', '2025-07-28 10:33:47', 0),
(41, 'Reviews', 'reviews', 'fa fa-star', NULL, NULL, 5, 5, 1, 1, NULL, NULL, '2025-07-10 18:59:05', '2025-07-28 10:33:51', 0),
(42, 'Leads', 'leads', 'fa fa-bullseye', NULL, NULL, 6, 1, 1, 1, NULL, NULL, '2025-07-10 18:59:33', '2025-07-28 10:12:03', 0),
(43, 'Clients', 'clients', 'fa fa-handshake', NULL, NULL, 6, 2, 1, 1, NULL, NULL, '2025-07-10 18:59:33', '2025-07-28 10:12:06', 0),
(44, 'Tickets', 'tickets', 'fa fa-ticket-alt', NULL, NULL, 6, 3, 1, 1, NULL, NULL, '2025-07-10 18:59:33', '2025-07-28 10:13:07', 0),
(45, 'Campaigns', 'campaigns', 'fa fa-mail-bulk', NULL, NULL, 6, 4, 1, 1, NULL, NULL, '2025-07-10 18:59:33', '2025-07-28 10:12:20', 0),
(46, 'Users', 'userss', 'fa fa-user-shield', NULL, NULL, 7, 1, 1, 1, NULL, NULL, '2025-07-10 19:00:24', '2025-07-28 10:12:26', 0),
(47, 'Roles & Permissions', 'role-permissions', 'fa fa-lock', NULL, NULL, 7, 2, 1, 1, NULL, NULL, '2025-07-10 19:00:24', '2025-07-28 10:12:36', 0),
(48, 'Settings', 'settings', 'fa fa-sliders-h', NULL, NULL, 7, 3, 1, 1, NULL, NULL, '2025-07-10 19:00:24', '2025-07-28 10:12:39', 0),
(49, 'Audit Logs', 'audit-logs', 'fa fa-history', NULL, NULL, 7, 4, 1, 1, NULL, NULL, '2025-07-10 19:00:24', '2025-07-28 10:12:44', 0);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_deleted` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `status`, `created_at`, `id_deleted`) VALUES
(1, 'Admin', 'Full access to the system modules', 1, '2025-06-30 10:32:25', 0),
(2, 'Teacher', 'Access to student and academic records', 1, '2025-06-30 10:32:25', 0),
(3, 'Student', 'Limited access to personal records and grades', 1, '2025-06-30 10:32:25', 0),
(4, 'Parent', 'Access to child’s records', 1, '2025-06-30 10:32:25', 0),
(5, 'Staff', 'Access to staff management', 1, '2025-06-30 10:32:25', 0),
(6, 'Finance', 'Access to All Financial modules and record', 1, '2025-07-03 09:02:40', 0);

--
-- Triggers `roles`
--
DELIMITER $$
CREATE TRIGGER `after_role_insert` AFTER INSERT ON `roles` FOR EACH ROW BEGIN
    INSERT INTO role_module_permissions (role_id, module_id, can_view, can_create, can_edit, can_delete)
    SELECT NEW.id, m.id, FALSE, FALSE, FALSE, FALSE
    FROM modules m
    WHERE m.id_deleted = 0;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `role_module_permissions`
--

CREATE TABLE `role_module_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `can_view` tinyint(1) DEFAULT 0,
  `can_create` tinyint(1) DEFAULT 0,
  `can_edit` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_module_permissions`
--

INSERT INTO `role_module_permissions` (`id`, `role_id`, `module_id`, `can_view`, `can_create`, `can_edit`, `can_delete`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-08-01 20:53:48'),
(2, 1, 2, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-22 22:30:42'),
(3, 1, 3, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-22 22:30:42'),
(4, 1, 4, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-22 22:30:42'),
(5, 1, 5, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-22 22:30:42'),
(6, 1, 6, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-22 22:30:42'),
(7, 1, 7, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-23 21:11:34'),
(8, 1, 8, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-22 21:06:24'),
(9, 1, 9, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:29:04'),
(10, 1, 17, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:25'),
(11, 1, 18, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(12, 1, 19, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(13, 1, 20, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(14, 1, 21, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(15, 1, 22, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(16, 1, 23, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(17, 1, 24, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(18, 1, 25, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(19, 1, 26, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(20, 1, 27, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(21, 1, 28, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(22, 1, 29, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(23, 1, 30, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(24, 1, 31, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(25, 1, 32, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(26, 1, 33, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(27, 1, 34, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(28, 1, 35, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(29, 1, 36, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(30, 1, 37, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(31, 1, 38, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(32, 1, 39, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(33, 1, 40, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(34, 1, 41, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(35, 1, 42, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(36, 1, 43, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(37, 1, 44, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(38, 1, 45, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(39, 1, 46, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(40, 1, 47, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(41, 1, 48, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(42, 1, 49, 1, 0, 0, 0, '2025-07-22 20:58:08', '2025-07-26 16:47:31'),
(64, 2, 1, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(65, 2, 2, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(66, 2, 3, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(67, 2, 4, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(68, 2, 5, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(69, 2, 6, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(70, 2, 7, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(71, 2, 8, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(72, 2, 9, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(73, 2, 17, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(74, 2, 18, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(75, 2, 19, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(76, 2, 20, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(77, 2, 21, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(78, 2, 22, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(79, 2, 23, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(80, 2, 24, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(81, 2, 25, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(82, 2, 26, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(83, 2, 27, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(84, 2, 28, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(85, 2, 29, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(86, 2, 30, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(87, 2, 31, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(88, 2, 32, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(89, 2, 33, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(90, 2, 34, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(91, 2, 35, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(92, 2, 36, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(93, 2, 37, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(94, 2, 38, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(95, 2, 39, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(96, 2, 40, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(97, 2, 41, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(98, 2, 42, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(99, 2, 43, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(100, 2, 44, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(101, 2, 45, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(102, 2, 46, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(103, 2, 47, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(104, 2, 48, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(105, 2, 49, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(127, 3, 1, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(128, 3, 2, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(129, 3, 3, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(130, 3, 4, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(131, 3, 5, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(132, 3, 6, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(133, 3, 7, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(134, 3, 8, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(135, 3, 9, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(136, 3, 17, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(137, 3, 18, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(138, 3, 19, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(139, 3, 20, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(140, 3, 21, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(141, 3, 22, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(142, 3, 23, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(143, 3, 24, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(144, 3, 25, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(145, 3, 26, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(146, 3, 27, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(147, 3, 28, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(148, 3, 29, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(149, 3, 30, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(150, 3, 31, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(151, 3, 32, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(152, 3, 33, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(153, 3, 34, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(154, 3, 35, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(155, 3, 36, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(156, 3, 37, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(157, 3, 38, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(158, 3, 39, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(159, 3, 40, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(160, 3, 41, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(161, 3, 42, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(162, 3, 43, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(163, 3, 44, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(164, 3, 45, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(165, 3, 46, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(166, 3, 47, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(167, 3, 48, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(168, 3, 49, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(190, 4, 1, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(191, 4, 2, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(192, 4, 3, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(193, 4, 4, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(194, 4, 5, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(195, 4, 6, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(196, 4, 7, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(197, 4, 8, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(198, 4, 9, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(199, 4, 17, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(200, 4, 18, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(201, 4, 19, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(202, 4, 20, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(203, 4, 21, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(204, 4, 22, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(205, 4, 23, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(206, 4, 24, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(207, 4, 25, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(208, 4, 26, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(209, 4, 27, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(210, 4, 28, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(211, 4, 29, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(212, 4, 30, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(213, 4, 31, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(214, 4, 32, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(215, 4, 33, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(216, 4, 34, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(217, 4, 35, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(218, 4, 36, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(219, 4, 37, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(220, 4, 38, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(221, 4, 39, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(222, 4, 40, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(223, 4, 41, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(224, 4, 42, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(225, 4, 43, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(226, 4, 44, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(227, 4, 45, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(228, 4, 46, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(229, 4, 47, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(230, 4, 48, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(231, 4, 49, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(253, 5, 1, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(254, 5, 2, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(255, 5, 3, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(256, 5, 4, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(257, 5, 5, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(258, 5, 6, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(259, 5, 7, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(260, 5, 8, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(261, 5, 9, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(262, 5, 17, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(263, 5, 18, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(264, 5, 19, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(265, 5, 20, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(266, 5, 21, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(267, 5, 22, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(268, 5, 23, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(269, 5, 24, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(270, 5, 25, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(271, 5, 26, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(272, 5, 27, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(273, 5, 28, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(274, 5, 29, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(275, 5, 30, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(276, 5, 31, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(277, 5, 32, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(278, 5, 33, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(279, 5, 34, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(280, 5, 35, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(281, 5, 36, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(282, 5, 37, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(283, 5, 38, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(284, 5, 39, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(285, 5, 40, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(286, 5, 41, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(287, 5, 42, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(288, 5, 43, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(289, 5, 44, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(290, 5, 45, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(291, 5, 46, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(292, 5, 47, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(293, 5, 48, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(294, 5, 49, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(316, 6, 1, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(317, 6, 2, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(318, 6, 3, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(319, 6, 4, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(320, 6, 5, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(321, 6, 6, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(322, 6, 7, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(323, 6, 8, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(324, 6, 9, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(325, 6, 17, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(326, 6, 18, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(327, 6, 19, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(328, 6, 20, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(329, 6, 21, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(330, 6, 22, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(331, 6, 23, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(332, 6, 24, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(333, 6, 25, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(334, 6, 26, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(335, 6, 27, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(336, 6, 28, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(337, 6, 29, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(338, 6, 30, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(339, 6, 31, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(340, 6, 32, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(341, 6, 33, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(342, 6, 34, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(343, 6, 35, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(344, 6, 36, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(345, 6, 37, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(346, 6, 38, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(347, 6, 39, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(348, 6, 40, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(349, 6, 41, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(350, 6, 42, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(351, 6, 43, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(352, 6, 44, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(353, 6, 45, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(354, 6, 46, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(355, 6, 47, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(356, 6, 48, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31'),
(357, 6, 49, 1, 0, 0, 0, '2025-07-22 20:58:45', '2025-07-26 16:47:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `auth_key` varchar(32) DEFAULT NULL,
  `password_reset_token` varchar(100) DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `cnic` varchar(25) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `alternate_phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `role` enum('admin','teacher','student','parent','staff') NOT NULL DEFAULT 'student',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `profile_image` varchar(255) DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_deleted` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `username`, `email`, `password_hash`, `auth_key`, `password_reset_token`, `first_name`, `last_name`, `gender`, `dob`, `cnic`, `phone`, `alternate_phone`, `address`, `city`, `country`, `role`, `status`, `profile_image`, `last_login_at`, `created_at`, `updated_at`, `id_deleted`) VALUES
(1, 1, 'admin', 'admin@school.pk', '123', NULL, NULL, 'Qamar', 'Ali', 'Male', '1985-06-12', '35202-1234567-1', '03001234567', NULL, 'Main Boulevard, Lahore', 'Lahore', 'Pakistan', 'admin', 1, NULL, '2025-08-06 19:53:05', '2025-06-29 14:13:09', '2025-08-06 19:53:05', 0),
(2, 2, 'teacher.ahmed', 'ahmed@school.pk', '123', NULL, NULL, 'Muhammad', 'Ahmed', 'Male', '1980-03-22', '35201-9876543-2', '03011234567', NULL, 'Gulshan-e-Iqbal', 'Karachi', 'Pakistan', 'teacher', 1, NULL, '2025-07-26 14:49:23', '2025-06-29 14:13:09', '2025-07-30 00:02:26', 0),
(3, 2, 'teacher.sadia', 'sadia@school.pk', '123', NULL, NULL, 'Sadia', 'Khan', 'Female', '1988-07-15', '35203-4444444-3', '03211234567', NULL, 'Satellite Town', 'Rawalpindi', 'Pakistan', 'teacher', 1, NULL, NULL, '2025-06-29 14:13:09', '2025-07-18 20:00:00', 0),
(4, 3, 'student.ali', 'ali@student.pk', '123', NULL, NULL, 'Ali', 'Raza', 'Male', '2007-10-05', NULL, '03451234567', NULL, 'Model Town', 'Lahore', 'Pakistan', 'student', 1, NULL, NULL, '2025-06-29 14:13:09', '2025-07-18 20:00:00', 0),
(5, 3, 'student.aisha', 'aisha@student.pk', '123', NULL, NULL, 'Aisha', 'Bibi', 'Female', '2008-03-12', NULL, '03021234567', NULL, 'University Road', 'Peshawar', 'Pakistan', 'student', 1, NULL, NULL, '2025-06-29 14:13:09', '2025-07-18 20:00:00', 0),
(6, 4, 'parent.junaid', 'junaid@parent.pk', '123', NULL, NULL, 'Junaid', 'Farooq', 'Male', '1975-09-25', '37406-3456789-2', '03331234567', NULL, 'Saddar Bazaar', 'Faisalabad', 'Pakistan', 'parent', 1, NULL, NULL, '2025-06-29 14:13:09', '2025-07-18 20:00:00', 0),
(7, 5, 'staff.nasir', 'nasir@staff.pk', '123', NULL, NULL, 'Nasir', 'Mehmood', 'Male', '1990-01-18', '37401-2222222-1', '03111234567', NULL, 'Civil Lines', 'Multan', 'Pakistan', 'staff', 1, NULL, NULL, '2025-06-29 14:13:09', '2025-07-18 20:00:00', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `role_module_permissions`
--
ALTER TABLE `role_module_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `app_settings`
--
ALTER TABLE `app_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `role_module_permissions`
--
ALTER TABLE `role_module_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=358;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
