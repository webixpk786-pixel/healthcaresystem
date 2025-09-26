-- Healthcare System Modules
-- This file contains all modules for the Healthcare System with proper hierarchy


-- System Administration Modules (Parent)
INSERT INTO `modules` (`name`, `link`, `icon`, `react_icon`, `color`, `parent_id`, `sort_order`, `is_active`, `current_status`, `description`, `created_at`, `updated_at`, `id_deleted`) VALUES
('Users', 'users', 'fa fa-users-cog', 'MdPeople', '#e6eaff', NULL, 1, 1, 1, 'Manage users, roles, and permissions across the system.', NOW(),NOW(), NOW(), 0),
('System Administration', 'system', 'fa fa-cogs', 'MdSettings', '#e74c3c', NULL, 1, 1, 1, 'Core system administration and configuration', '2025-07-23 15:08:54', '2025-06-29 19:35:04', '2025-08-31 00:42:27', 0),
('User Management', 'users', 'fa fa-users', 'MdPeople', '#3498db', NULL, 2, 1, 1, 'Manage users, roles, and permissions across the system', '2025-07-23 15:08:54', '2025-06-29 19:35:04', '2025-08-31 00:42:27', 0),
('Hospital Management', 'hospital', 'fa fa-hospital', 'MdLocalHospital', '#2ecc71', NULL, 3, 1, 1, 'Complete hospital operations and patient management', '2025-07-23 15:08:54', '2025-06-29 19:35:04', '2025-08-31 00:42:27', 0),
('Pharmacy Management', 'pharmacy', 'fa fa-pills', 'MdLocalPharmacy', '#f39c12', NULL, 4, 1, 1, 'Pharmacy operations and medication management', '2025-07-23 15:08:54', '2025-06-29 19:35:04', '2025-08-31 00:42:27', 0),
('Reports & Analytics', 'reports', 'fa fa-chart-bar', 'MdAnalytics', '#9b59b6', NULL, 5, 1, 1, 'Comprehensive reporting and analytics dashboard', '2025-07-23 15:08:54', '2025-06-29 19:35:04', '2025-08-31 00:42:27', 0),
('Settings', 'settings', 'fa fa-cog', 'MdSettings', '#34495e', NULL, 6, 1, 1, 'System configuration and settings management', '2025-07-23 15:08:54', '2025-06-29 19:35:04', '2025-08-31 00:42:27', 0);

-- Get the parent module IDs for sub-modules
SET @system_admin_id = (SELECT id FROM modules WHERE link = 'system' AND parent_id IS NULL);
SET @user_mgmt_id = (SELECT id FROM modules WHERE link = 'users' AND parent_id IS NULL);
SET @hospital_id = (SELECT id FROM modules WHERE link = 'hospital' AND parent_id IS NULL);
SET @pharmacy_id = (SELECT id FROM modules WHERE link = 'pharmacy' AND parent_id IS NULL);
SET @reports_id = (SELECT id FROM modules WHERE link = 'reports' AND parent_id IS NULL);
SET @settings_id = (SELECT id FROM modules WHERE link = 'settings' AND parent_id IS NULL);

-- System Administration Sub-modules
INSERT INTO `modules` (`name`, `link`, `icon`, `react_icon`, `color`, `parent_id`, `sort_order`, `is_active`, `current_status`, `description`, `created_at`, `updated_at`, `id_deleted`) VALUES
('System Dashboard', 'dashboard', 'fa fa-tachometer-alt', 'MdDashboard', '#e74c3c', @system_admin_id, 1, 1, 1, 'Main system dashboard with key metrics', NOW(), NOW(), 0),
('System Logs', 'logs', 'fa fa-list-alt', 'MdList', '#e67e22', @system_admin_id, 2, 1, 1, 'System activity logs and audit trails', NOW(), NOW(), 0),
('Backup & Restore', 'backup', 'fa fa-database', 'MdBackup', '#95a5a6', @system_admin_id, 3, 1, 1, 'Database backup and restore operations', NOW(), NOW(), 0),
('System Health', 'health', 'fa fa-heartbeat', 'MdHealthAndSafety', '#e74c3c', @system_admin_id, 4, 1, 1, 'System health monitoring and diagnostics', NOW(), NOW(), 0);

-- User Management Sub-modules
INSERT INTO `modules` (`name`, `link`, `icon`, `react_icon`, `color`, `parent_id`, `sort_order`, `is_active`, `current_status`, `description`, `created_at`, `updated_at`, `id_deleted`) VALUES
('Users', 'users', 'fa fa-user', 'MdPerson', '#3498db', @user_mgmt_id, 1, 1, 1, 'Manage system users and accounts', NOW(), NOW(), 0),
('Roles & Permissions', 'roles', 'fa fa-user-shield', 'MdSecurity', '#2980b9', @user_mgmt_id, 2, 1, 1, 'Manage user roles and access permissions', NOW(), NOW(), 0),
('System Modules', 'systemmodules', 'fa fa-puzzle-piece', 'MdExtension', '#5dade2', @user_mgmt_id, 3, 1, 1, 'Manage system modules and their status', NOW(), NOW(), 0),
('User Activity', 'activity', 'fa fa-clock', 'MdAccessTime', '#85c1e9', @user_mgmt_id, 4, 1, 1, 'Track user activity and login history', NOW(), NOW(), 0);

-- Hospital Management Sub-modules
INSERT INTO `modules` (`name`, `link`, `icon`, `react_icon`, `color`, `parent_id`, `sort_order`, `is_active`, `current_status`, `description`, `created_at`, `updated_at`, `id_deleted`) VALUES
('Patient Management', 'patients', 'fa fa-user-injured', 'MdPerson', '#2ecc71', @hospital_id, 1, 1, 1, 'Comprehensive patient registration and management', NOW(), NOW(), 0),
('Appointments', 'appointments', 'fa fa-calendar-check', 'MdEvent', '#27ae60', @hospital_id, 2, 1, 1, 'Schedule and manage patient appointments', NOW(), NOW(), 0),
('Medical Records', 'medical-records', 'fa fa-file-medical', 'MdDescription', '#16a085', @hospital_id, 3, 1, 1, 'Patient medical records and history', NOW(), NOW(), 0),
('Laboratory', 'laboratory', 'fa fa-flask', 'MdScience', '#1abc9c', @hospital_id, 4, 1, 1, 'Laboratory tests and results management', NOW(), NOW(), 0),
('Radiology', 'radiology', 'fa fa-x-ray', 'MdRadiology', '#3498db', @hospital_id, 5, 1, 1, 'Radiology tests and imaging management', NOW(), NOW(), 0),
('Pharmacy (Hospital)', 'hospital-pharmacy', 'fa fa-pills', 'MdLocalPharmacy', '#e67e22', @hospital_id, 6, 1, 1, 'Hospital pharmacy and medication dispensing', NOW(), NOW(), 0),
('Billing & Insurance', 'billing', 'fa fa-credit-card', 'MdPayment', '#f39c12', @hospital_id, 7, 1, 1, 'Patient billing and insurance management', NOW(), NOW(), 0),
('Surgery Management', 'surgery', 'fa fa-procedures', 'MdMedicalServices', '#e74c3c', @hospital_id, 8, 1, 1, 'Surgical procedures and operation theater management', NOW(), NOW(), 0),
('Ward Management', 'wards', 'fa fa-bed', 'MdHotel', '#9b59b6', @hospital_id, 9, 1, 1, 'Hospital wards and bed management', NOW(), NOW(), 0),
('Emergency Services', 'emergency', 'fa fa-ambulance', 'MdEmergency', '#e74c3c', @hospital_id, 10, 1, 1, 'Emergency department and urgent care', NOW(), NOW(), 0),
('Staff Management', 'staff', 'fa fa-user-md', 'MdMedicalServices', '#34495e', @hospital_id, 11, 1, 1, 'Hospital staff and personnel management', NOW(), NOW(), 0),
('Inventory Management', 'inventory', 'fa fa-boxes', 'MdInventory', '#95a5a6', @hospital_id, 12, 1, 1, 'Medical supplies and equipment inventory', NOW(), NOW(), 0);

-- Pharmacy Management Sub-modules
INSERT INTO `modules` (`name`, `link`, `icon`, `react_icon`, `color`, `parent_id`, `sort_order`, `is_active`, `current_status`, `description`, `created_at`, `updated_at`, `id_deleted`) VALUES
('Medication Management', 'medications', 'fa fa-pills', 'MdMedication', '#f39c12', @pharmacy_id, 1, 1, 1, 'Medication catalog and drug information', NOW(), NOW(), 0),
('Inventory Management', 'pharmacy-inventory', 'fa fa-boxes', 'MdInventory', '#e67e22', @pharmacy_id, 2, 1, 1, 'Pharmacy inventory and stock management', NOW(), NOW(), 0),
('Prescriptions', 'prescriptions', 'fa fa-prescription', 'MdReceipt', '#d35400', @pharmacy_id, 3, 1, 1, 'Prescription management and dispensing', NOW(), NOW(), 0),
('Customer Management', 'customers', 'fa fa-users', 'MdPeople', '#f39c12', @pharmacy_id, 4, 1, 1, 'Pharmacy customer and patient management', NOW(), NOW(), 0),
('Sales & Billing', 'pharmacy-sales', 'fa fa-cash-register', 'MdPointOfSale', '#e67e22', @pharmacy_id, 5, 1, 1, 'Pharmacy sales and billing operations', NOW(), NOW(), 0),
('Supplier Management', 'suppliers', 'fa fa-truck', 'MdLocalShipping', '#d35400', @pharmacy_id, 6, 1, 1, 'Pharmaceutical supplier management', NOW(), NOW(), 0),
('Delivery Management', 'delivery', 'fa fa-shipping-fast', 'MdDeliveryDining', '#f39c12', @pharmacy_id, 7, 1, 1, 'Medication delivery and logistics', NOW(), NOW(), 0),
('Pharmacy Staff', 'pharmacy-staff', 'fa fa-user-md', 'MdPerson', '#e67e22', @pharmacy_id, 8, 1, 1, 'Pharmacy staff and personnel management', NOW(), NOW(), 0);

-- Reports & Analytics Sub-modules
INSERT INTO `modules` (`name`, `link`, `icon`, `react_icon`, `color`, `parent_id`, `sort_order`, `is_active`, `current_status`, `description`, `created_at`, `updated_at`, `id_deleted`) VALUES
('Hospital Reports', 'hospital-reports', 'fa fa-chart-line', 'MdAnalytics', '#9b59b6', @reports_id, 1, 1, 1, 'Hospital performance and patient reports', NOW(), NOW(), 0),
('Pharmacy Reports', 'pharmacy-reports', 'fa fa-chart-pie', 'MdPieChart', '#8e44ad', @reports_id, 2, 1, 1, 'Pharmacy sales and inventory reports', NOW(), NOW(), 0),
('Financial Reports', 'financial-reports', 'fa fa-chart-bar', 'MdBarChart', '#7d3c98', @reports_id, 3, 1, 1, 'Financial performance and revenue reports', NOW(), NOW(), 0),
('Analytics Dashboard', 'analytics', 'fa fa-chart-area', 'MdDashboard', '#6c3483', @reports_id, 4, 1, 1, 'Advanced analytics and insights', NOW(), NOW(), 0),
('Custom Reports', 'custom-reports', 'fa fa-file-alt', 'MdDescription', '#5b2c6f', @reports_id, 5, 1, 1, 'Create and manage custom reports', NOW(), NOW(), 0);

-- Settings Sub-modules
INSERT INTO `modules` (`name`, `link`, `icon`, `react_icon`, `color`, `parent_id`, `sort_order`, `is_active`, `current_status`, `description`, `created_at`, `updated_at`, `id_deleted`) VALUES
('General Settings', 'general-settings', 'fa fa-cog', 'MdSettings', '#34495e', @settings_id, 1, 1, 1, 'General system configuration', NOW(), NOW(), 0),
('Email Settings', 'email-settings', 'fa fa-envelope', 'MdEmail', '#2c3e50', @settings_id, 2, 1, 1, 'Email configuration and notifications', NOW(), NOW(), 0),
('Security Settings', 'security-settings', 'fa fa-shield-alt', 'MdSecurity', '#2c3e50', @settings_id, 3, 1, 1, 'Security and authentication settings', NOW(), NOW(), 0),
('Backup Settings', 'backup-settings', 'fa fa-database', 'MdBackup', '#34495e', @settings_id, 4, 1, 1, 'Backup and recovery configuration', NOW(), NOW(), 0),
('Integration Settings', 'integration-settings', 'fa fa-plug', 'MdExtension', '#2c3e50', @settings_id, 5, 1, 1, 'Third-party integrations and APIs', NOW(), NOW(), 0),
('Language Settings', 'language-settings', 'fa fa-language', 'MdLanguage', '#34495e', @settings_id, 6, 1, 1, 'Multi-language support configuration', NOW(), NOW(), 0);

-- Additional Hospital Sub-modules (Specialized Departments)
INSERT INTO `modules` (`name`, `link`, `icon`, `react_icon`, `color`, `parent_id`, `sort_order`, `is_active`, `current_status`, `description`, `created_at`, `updated_at`, `id_deleted`) VALUES
('Cardiology', 'cardiology', 'fa fa-heartbeat', 'MdFavorite', '#e74c3c', @hospital_id, 13, 1, 1, 'Cardiology department and heart care', NOW(), NOW(), 0),
('Neurology', 'neurology', 'fa fa-brain', 'MdPsychology', '#9b59b6', @hospital_id, 14, 1, 1, 'Neurology department and brain care', NOW(), NOW(), 0),
('Orthopedics', 'orthopedics', 'fa fa-bone', 'MdAccessibility', '#f39c12', @hospital_id, 15, 1, 1, 'Orthopedics and bone care', NOW(), NOW(), 0),
('Pediatrics', 'pediatrics', 'fa fa-baby', 'MdChildCare', '#3498db', @hospital_id, 16, 1, 1, 'Pediatrics and child care', NOW(), NOW(), 0),
('Gynecology', 'gynecology', 'fa fa-female', 'MdPregnantWoman', '#e91e63', @hospital_id, 17, 1, 1, 'Gynecology and women health', NOW(), NOW(), 0),
('Dermatology', 'dermatology', 'fa fa-allergies', 'MdHealing', '#ff9800', @hospital_id, 18, 1, 1, 'Dermatology and skin care', NOW(), NOW(), 0),
('Ophthalmology', 'ophthalmology', 'fa fa-eye', 'MdVisibility', '#2196f3', @hospital_id, 19, 1, 1, 'Ophthalmology and eye care', NOW(), NOW(), 0),
('ENT', 'ent', 'fa fa-ear', 'MdHearing', '#4caf50', @hospital_id, 20, 1, 1, 'Ear, Nose, and Throat department', NOW(), NOW(), 0),
('Dental', 'dental', 'fa fa-tooth', 'MdDentistry', '#00bcd4', @hospital_id, 21, 1, 1, 'Dental care and oral health', NOW(), NOW(), 0),
('Psychiatry', 'psychiatry', 'fa fa-brain', 'MdPsychology', '#9c27b0', @hospital_id, 22, 1, 1, 'Psychiatry and mental health', NOW(), NOW(), 0),
('Physiotherapy', 'physiotherapy', 'fa fa-walking', 'MdDirectionsWalk', '#ff5722', @hospital_id, 23, 1, 1, 'Physiotherapy and rehabilitation', NOW(), NOW(), 0),
('Nutrition', 'nutrition', 'fa fa-apple-alt', 'MdRestaurant', '#4caf50', @hospital_id, 24, 1, 1, 'Nutrition and dietary services', NOW(), NOW(), 0);

-- Additional Pharmacy Sub-modules
INSERT INTO `modules` (`name`, `link`, `icon`, `react_icon`, `color`, `parent_id`, `sort_order`, `is_active`, `current_status`, `description`, `created_at`, `updated_at`, `id_deleted`) VALUES
('Drug Interactions', 'drug-interactions', 'fa fa-exclamation-triangle', 'MdWarning', '#f44336', @pharmacy_id, 9, 1, 1, 'Drug interaction checking and alerts', NOW(), NOW(), 0),
('Prescription History', 'prescription-history', 'fa fa-history', 'MdHistory', '#607d8b', @pharmacy_id, 10, 1, 1, 'Patient prescription history tracking', NOW(), NOW(), 0),
('Medication Alerts', 'medication-alerts', 'fa fa-bell', 'MdNotifications', '#ff9800', @pharmacy_id, 11, 1, 1, 'Medication alerts and reminders', NOW(), NOW(), 0),
('Pharmacy Analytics', 'pharmacy-analytics', 'fa fa-chart-line', 'MdTrendingUp', '#2196f3', @pharmacy_id, 12, 1, 1, 'Pharmacy performance analytics', NOW(), NOW(), 0);

-- Additional System Modules
INSERT INTO `modules` (`name`, `link`, `icon`, `react_icon`, `color`, `parent_id`, `sort_order`, `is_active`, `current_status`, `description`, `created_at`, `updated_at`, `id_deleted`) VALUES
('API Management', 'api', 'fa fa-code', 'MdCode', '#2c3e50', @system_admin_id, 5, 1, 1, 'API endpoints and integration management', NOW(), NOW(), 0),
('System Updates', 'updates', 'fa fa-download', 'MdSystemUpdate', '#27ae60', @system_admin_id, 6, 1, 1, 'System updates and version management', NOW(), NOW(), 0),
('Data Import/Export', 'data-io', 'fa fa-exchange-alt', 'MdSwapHoriz', '#8e44ad', @system_admin_id, 7, 1, 1, 'Data import and export functionality', NOW(), NOW(), 0),
('System Maintenance', 'maintenance', 'fa fa-tools', 'MdBuild', '#e67e22', @system_admin_id, 8, 1, 1, 'System maintenance and optimization', NOW(), NOW(), 0);

-- Additional User Management Modules
INSERT INTO `modules` (`name`, `link`, `icon`, `react_icon`, `color`, `parent_id`, `sort_order`, `is_active`, `current_status`, `description`, `created_at`, `updated_at`, `id_deleted`) VALUES
('User Groups', 'user-groups', 'fa fa-layer-group', 'MdGroup', '#3498db', @user_mgmt_id, 5, 1, 1, 'Manage user groups and team assignments', NOW(), NOW(), 0),
('Access Control', 'access-control', 'fa fa-lock', 'MdLock', '#2c3e50', @user_mgmt_id, 6, 1, 1, 'Advanced access control and security', NOW(), NOW(), 0),
('User Sessions', 'sessions', 'fa fa-clock', 'MdAccessTime', '#95a5a6', @user_mgmt_id, 7, 1, 1, 'Active user sessions and management', NOW(), NOW(), 0);

-- Additional Reports Modules
INSERT INTO `modules` (`name`, `link`, `icon`, `react_icon`, `color`, `parent_id`, `sort_order`, `is_active`, `current_status`, `description`, `created_at`, `updated_at`, `id_deleted`) VALUES
('Staff Reports', 'staff-reports', 'fa fa-user-chart', 'MdPerson', '#34495e', @reports_id, 6, 1, 1, 'Staff performance and productivity reports', NOW(), NOW(), 0),
('Inventory Reports', 'inventory-reports', 'fa fa-box-chart', 'MdInventory', '#95a5a6', @reports_id, 7, 1, 1, 'Inventory and stock level reports', NOW(), NOW(), 0),
('Quality Reports', 'quality-reports', 'fa fa-award', 'MdEmojiEvents', '#f1c40f', @reports_id, 8, 1, 1, 'Quality assurance and compliance reports', NOW(), NOW(), 0),
('Audit Reports', 'audit-reports', 'fa fa-search', 'MdSearch', '#2c3e50', @reports_id, 9, 1, 1, 'System audit and compliance reports', NOW(), NOW(), 0);

-- Additional Settings Modules
INSERT INTO `modules` (`name`, `link`, `icon`, `react_icon`, `color`, `parent_id`, `sort_order`, `is_active`, `current_status`, `description`, `created_at`, `updated_at`, `id_deleted`) VALUES
('Notification Settings', 'notification-settings', 'fa fa-bell', 'MdNotifications', '#e74c3c', @settings_id, 7, 1, 1, 'System notification preferences', NOW(), NOW(), 0),
('Display Settings', 'display-settings', 'fa fa-desktop', 'MdDesktopWindows', '#3498db', @settings_id, 8, 1, 1, 'Display and interface customization', NOW(), NOW(), 0),
('Data Settings', 'data-settings', 'fa fa-database', 'MdStorage', '#2c3e50', @settings_id, 9, 1, 1, 'Data retention and privacy settings', NOW(), NOW(), 0),
('Performance Settings', 'performance-settings', 'fa fa-tachometer-alt', 'MdSpeed', '#f39c12', @settings_id, 10, 1, 1, 'System performance optimization', NOW(), NOW(), 0);
