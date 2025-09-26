INSERT INTO `modules` 
(`name`, `type`,  `link`, `icon`, `react_icon`, `color`, `parent_id`, `sort_order`, `is_active`, `current_status`, `description`, `last_updated_at`, `created_at`, `updated_at`, `id_deleted`) 
VALUES
('Pharmacy Admin','pharmacy', '/pharmacy/admin', 'cog', 'FaCogs', '#4F46E5', 0, 1, 1, 1, 'Administration and settings for pharmacy', NOW(), NOW(), NOW(), 0),
('Pharmacy Inventory','pharmacy', '/pharmacy/inventory', 'boxes', 'FaBoxes', '#10B981', 0, 2, 1, 1, 'Inventory and stock management', NOW(), NOW(), NOW(), 0),
('Pharmacy Sales & Billing', 'pharmacy', '/pharmacy/sales', 'cash-register', 'FaCashRegister', '#22C55E', 0, 3, 1, 1, 'Sales and billing system', NOW(), NOW(), NOW(), 0),
('Pharmacist Services', 'pharmacy', '/pharmacy/pharmacist', 'stethoscope', 'FaStethoscope', '#9333EA', 0, 4, 1, 1, 'Pharmacist professional services', NOW(), NOW(), NOW(), 0),
('Pharmacy Customer Portal', 'pharmacy', '/pharmacy/customer', 'user', 'FaUser', '#06B6D4', 0, 5, 1, 1, 'Customer-facing portal', NOW(), NOW(), NOW(), 0),
('Pharmacy Reports','pharmacy', '/pharmacy/reports', 'chart-line', 'FaChartLine', '#8B5CF6', 0, 6, 1, 1, 'Analytics and reporting', NOW(), NOW(), NOW(), 0);


--  Pharmacy Inventory Id 25
INSERT INTO `modules`
(`name`, `link`, `icon`, `react_icon`, `color`, `parent_id`, `sort_order`, `is_active`, `current_status`, `description`, `last_updated_at`, `created_at`, `updated_at`, `id_deleted`)
VALUES
('Medicine Catalog', '/pharmacy/inventory/catalog', 'fas fa-pills', 'FaPills', '#6EE7B7', 25, 1, 1, 1, 'List of medicines with details', NOW(), NOW(), NOW(), 0),
('Stock Management', '/pharmacy/inventory/stock', 'fas fa-boxes-stacked', 'FaBoxesStacked', '#34D399', 25, 2, 1, 1, 'Track and update stock levels', NOW(), NOW(), NOW(), 0),
('Expiry Alerts', '/pharmacy/inventory/expiry', 'fas fa-triangle-exclamation', 'FaTriangleExclamation', '#FCA5A5', 25, 3, 1, 1, 'Notifications for near-expiry medicines', NOW(), NOW(), NOW(), 0),
('Purchase Orders', '/pharmacy/inventory/orders', 'fas fa-cart-shopping', 'FaShoppingCart', '#FDBA74', 25, 4, 1, 1, 'Manage supplier purchase orders', NOW(), NOW(), NOW(), 0);
