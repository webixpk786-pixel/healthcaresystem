-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 10, 2025 at 07:29 PM
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
(1, 'Users', 'users', 'fa fa-users-cog', 'MdPeople', '#e6eaff', NULL, 1, 1, 1, 'Manage users, roles, and permissions across the system.', '2025-07-23 15:08:54', '2025-06-29 19:35:04', '2025-08-09 18:04:45', 0),
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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
