-- Inventory Management System Database Tables
-- This file contains all tables needed for the inventory management system

-- Suppliers table
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Locations table
CREATE TABLE IF NOT EXISTS `locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('warehouse','pharmacy','emergency','icu','storage') DEFAULT 'storage',
  `capacity` int(11) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Medicines table
CREATE TABLE IF NOT EXISTS `medicines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `generic_name` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `manufacturer` varchar(255) DEFAULT NULL,
  `strength` varchar(50) DEFAULT NULL,
  `form` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barcode` (`barcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Purchase Orders table
CREATE TABLE IF NOT EXISTS `purchase_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `expected_delivery` date DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','approved','ordered','delivered','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `supplier_id` (`supplier_id`),
  KEY `created_by` (`created_by`),
  KEY `approved_by` (`approved_by`),
  CONSTRAINT `purchase_orders_supplier_fk` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_orders_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_approved_by_fk` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Purchase Order Items table
CREATE TABLE IF NOT EXISTS `purchase_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_order_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `purchase_order_id` (`purchase_order_id`),
  KEY `medicine_id` (`medicine_id`),
  CONSTRAINT `purchase_order_items_order_fk` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_order_items_medicine_fk` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Stock table (updated to use location_id)
CREATE TABLE IF NOT EXISTS `stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `medicine_id` int(11) NOT NULL,
  `batch_number` varchar(50) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `unit` varchar(20) DEFAULT 'units',
  `expiry_date` date DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `min_stock` int(11) DEFAULT 0,
  `max_stock` int(11) DEFAULT 0,
  `purchase_price` decimal(10,2) DEFAULT 0.00,
  `selling_price` decimal(10,2) DEFAULT 0.00,
  `supplier_id` int(11) DEFAULT NULL,
  `status` enum('in_stock','low_stock','critical','out_of_stock') DEFAULT 'in_stock',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `medicine_id` (`medicine_id`),
  KEY `location_id` (`location_id`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `stock_medicine_fk` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_location_fk` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_supplier_fk` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Stock Movements table
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_id` int(11) NOT NULL,
  `movement_type` enum('in','out','transfer','adjustment') NOT NULL,
  `quantity` int(11) NOT NULL,
  `reference_type` enum('purchase','sale','transfer','adjustment','expiry') DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `from_location_id` int(11) DEFAULT NULL,
  `to_location_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `stock_id` (`stock_id`),
  KEY `from_location_id` (`from_location_id`),
  KEY `to_location_id` (`to_location_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `stock_movements_stock_fk` FOREIGN KEY (`stock_id`) REFERENCES `stock` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_movements_from_location_fk` FOREIGN KEY (`from_location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_to_location_fk` FOREIGN KEY (`to_location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample locations
INSERT INTO `locations` (`name`, `description`, `type`, `capacity`, `address`, `status`) VALUES
('Warehouse A', 'Main storage warehouse for bulk inventory', 'warehouse', 10000, 'Building A, Floor 1', 'active'),
('Warehouse B', 'Secondary warehouse for overflow storage', 'warehouse', 8000, 'Building B, Floor 1', 'active'),
('Main Pharmacy', 'Primary pharmacy for patient dispensing', 'pharmacy', 2000, 'Ground Floor, Main Building', 'active'),
('Emergency Room', 'Emergency department pharmacy', 'emergency', 500, 'Emergency Department', 'active'),
('ICU Pharmacy', 'Intensive care unit medication storage', 'icu', 300, 'ICU Floor', 'active');

-- Insert sample data
INSERT INTO `suppliers` (`name`, `contact_person`, `email`, `phone`, `address`, `city`, `country`, `status`) VALUES
('MedSupply Co.', 'John Smith', 'john@medsupply.com', '+1-555-0101', '123 Medical Street', 'New York', 'USA', 'active'),
('PharmaDirect', 'Sarah Johnson', 'sarah@pharmadirect.com', '+1-555-0102', '456 Pharma Avenue', 'Los Angeles', 'USA', 'active'),
('HealthCare Solutions', 'Mike Wilson', 'mike@healthcare.com', '+1-555-0103', '789 Health Boulevard', 'Chicago', 'USA', 'active'),
('ABC Pharma', 'Lisa Davis', 'lisa@abcpharma.com', '+1-555-0104', '321 Drug Lane', 'Houston', 'USA', 'active'),
('XYZ Labs', 'Tom Brown', 'tom@xyzlabs.com', '+1-555-0105', '654 Lab Road', 'Phoenix', 'USA', 'active');

INSERT INTO `medicines` (`name`, `generic_name`, `category`, `manufacturer`, `strength`, `form`, `description`, `barcode`, `status`) VALUES
('Paracetamol 500mg', 'Acetaminophen', 'Analgesic', 'ABC Pharma', '500mg', 'Tablet', 'Pain reliever and fever reducer', '1234567890123', 'active'),
('Ibuprofen 400mg', 'Ibuprofen', 'Analgesic', 'HealthPlus', '400mg', 'Tablet', 'Anti-inflammatory pain reliever', '1234567890124', 'active'),
('Amoxicillin 250mg', 'Amoxicillin', 'Antibiotic', 'XYZ Labs', '250mg', 'Capsule', 'Broad-spectrum antibiotic', '1234567890125', 'active'),
('Aspirin 100mg', 'Acetylsalicylic Acid', 'Analgesic', 'MediCorp', '100mg', 'Tablet', 'Pain reliever and anti-inflammatory', '1234567890126', 'active'),
('Omeprazole 20mg', 'Omeprazole', 'Proton Pump Inhibitor', 'MedCorp', '20mg', 'Capsule', 'Reduces stomach acid production', '1234567890127', 'active'),
('Cetirizine 10mg', 'Cetirizine', 'Antihistamine', 'AllerCare', '10mg', 'Tablet', 'Relieves allergy symptoms', '1234567890128', 'active'),
('Vitamin C 500mg', 'Ascorbic Acid', 'Vitamin', 'NutriCare', '500mg', 'Tablet', 'Immune system support', '1234567890129', 'active'),
('Metformin 500mg', 'Metformin', 'Antidiabetic', 'DiabCare', '500mg', 'Tablet', 'Type 2 diabetes management', '1234567890130', 'active');

-- Insert sample purchase orders
INSERT INTO `purchase_orders` (`order_number`, `supplier_id`, `order_date`, `expected_delivery`, `total_amount`, `status`, `notes`, `created_by`) VALUES
('PO-2024-001', 1, '2024-01-15', '2024-01-22', 87.50, 'delivered', 'Urgent order for emergency stock', 1),
('PO-2024-002', 2, '2024-01-18', '2024-01-25', 240.00, 'ordered', 'Regular monthly order', 1),
('PO-2024-003', 3, '2024-01-20', '2024-01-27', 245.00, 'pending', 'Waiting for approval', 1);

-- Insert sample purchase order items
INSERT INTO `purchase_order_items` (`purchase_order_id`, `medicine_id`, `quantity`, `unit_price`, `total_price`) VALUES
(1, 1, 100, 0.50, 50.00),
(1, 2, 50, 0.75, 37.50),
(2, 3, 200, 1.20, 240.00),
(3, 4, 150, 0.30, 45.00),
(3, 6, 100, 2.00, 200.00);

-- Insert sample stock data (updated to use location_id)
INSERT INTO `stock` (`medicine_id`, `batch_number`, `quantity`, `unit`, `expiry_date`, `location_id`, `min_stock`, `max_stock`, `purchase_price`, `selling_price`, `supplier_id`, `status`) VALUES
(1, 'B001-2024', 1500, 'Tablets', '2025-12-31', 1, 100, 2000, 4.50, 5.99, 1, 'in_stock'),
(2, 'B002-2024', 75, 'Tablets', '2024-06-30', 2, 50, 500, 6.25, 8.25, 2, 'low_stock'),
(3, 'B003-2024', 300, 'Capsules', '2026-03-15', 3, 75, 1000, 9.50, 12.50, 2, 'in_stock'),
(4, 'B004-2024', 45, 'Tablets', '2024-08-15', 4, 60, 800, 2.50, 3.50, 4, 'low_stock'),
(5, 'B005-2024', 200, 'Capsules', '2024-05-20', 5, 40, 600, 14.25, 18.75, 3, 'critical'),
(6, 'B006-2024', 850, 'Tablets', '2025-10-15', 1, 200, 1500, 12.00, 15.99, 5, 'in_stock'),
(7, 'B007-2024', 120, 'Tablets', '2024-09-01', 3, 50, 500, 5.99, 7.99, 5, 'in_stock'),
(8, 'B008-2024', 180, 'Tablets', '2025-07-20', 2, 100, 800, 18.00, 22.50, 5, 'in_stock');
