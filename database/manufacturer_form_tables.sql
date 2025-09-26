-- Create manufacturers table
CREATE TABLE IF NOT EXISTS `manufacturers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `contact_person` varchar(255),
  `phone` varchar(50),
  `email` varchar(255),
  `address` text,
  `website` varchar(255),
  `country` varchar(100),
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name` (`name`),
  KEY `idx_status` (`status`),
  KEY `idx_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create medicine_forms table
CREATE TABLE IF NOT EXISTS `medicine_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `unit_type` varchar(50) DEFAULT 'unit',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name` (`name`),
  KEY `idx_status` (`status`),
  KEY `idx_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default medicine forms
INSERT INTO `medicine_forms` (`name`, `description`, `unit_type`) VALUES
('Tablet', 'Solid dosage form', 'tablet'),
('Capsule', 'Solid dosage form in gelatin shell', 'capsule'),
('Syrup', 'Liquid oral dosage form', 'ml'),
('Injection', 'Parenteral dosage form', 'vial'),
('Cream', 'Semi-solid topical preparation', 'tube'),
('Ointment', 'Semi-solid topical preparation', 'tube'),
('Drops', 'Liquid dosage form for eyes/ears', 'ml'),
('Inhaler', 'Pressurized metered dose inhaler', 'puff'),
('Powder', 'Dry powder form', 'sachet'),
('Gel', 'Semi-solid gel preparation', 'tube'),
('Patch', 'Transdermal patch', 'patch'),
('Suppository', 'Rectal/vaginal dosage form', 'suppository'),
('Lotion', 'Liquid topical preparation', 'ml'),
('Spray', 'Pressurized spray form', 'spray'),
('Lozenge', 'Solid dosage form for throat', 'lozenge')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Insert some default manufacturers
INSERT INTO `manufacturers` (`name`, `description`, `country`) VALUES
('Pfizer Inc.', 'American multinational pharmaceutical corporation', 'USA'),
('Johnson & Johnson', 'American multinational corporation', 'USA'),
('Novartis AG', 'Swiss multinational pharmaceutical corporation', 'Switzerland'),
('Roche Holding AG', 'Swiss multinational healthcare company', 'Switzerland'),
('Merck & Co.', 'American multinational pharmaceutical company', 'USA'),
('GlaxoSmithKline', 'British multinational pharmaceutical company', 'UK'),
('Sanofi', 'French multinational pharmaceutical company', 'France'),
('Bayer AG', 'German multinational pharmaceutical company', 'Germany'),
('Abbott Laboratories', 'American multinational medical devices company', 'USA'),
('Bristol Myers Squibb', 'American multinational pharmaceutical company', 'USA')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
