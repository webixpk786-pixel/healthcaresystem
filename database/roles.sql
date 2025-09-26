-- Hospital Roles
INSERT INTO `roles` (`name`, `role_type`, `system_type`, `description`, `status`, `created_at`, `id_deleted`) VALUES
('Hospital Admin', 'admin', 'hospital', 'Manages hospital system operations and staff', 1, NOW(), 0),
('Doctor', 'medical', 'hospital', 'Diagnoses and treats patients', 1, NOW(), 0),
('Nurse', 'medical', 'hospital', 'Provides patient care and assistance to doctors', 1, NOW(), 0),
('Receptionist', 'support', 'hospital', 'Handles patient appointments and front desk tasks', 1, NOW(), 0),
('Lab Technician', 'medical', 'hospital', 'Conducts diagnostic tests in the laboratory', 1, NOW(), 0),
('Radiologist', 'medical', 'hospital', 'Performs and interprets radiology tests', 1, NOW(), 0),
('Surgeon', 'medical', 'hospital', 'Performs surgical operations', 1, NOW(), 0),
('Billing Staff', 'finance', 'hospital', 'Manages billing and insurance claims', 1, NOW(), 0),
('Patient', 'user', 'hospital', 'Accesses their medical records and appointments', 1, NOW(), 0),
('Pharmacist', 'medical', 'hospital', 'Dispenses medications and manages prescriptions', 1, NOW(), 0);

-- Pharmacy Roles
INSERT INTO `roles` (`name`, `role_type`, `system_type`, `description`, `status`, `created_at`, `id_deleted`) VALUES
('Pharmacy Admin', 'admin', 'pharmacy', 'Oversees the pharmacy operations and inventory',1, NOW(), 0),
('Pharmacist', 'medical', 'pharmacy', 'Dispenses medications and advises on usage',1, NOW(), 0),
('Pharmacy Assistant', 'support', 'pharmacy', 'Assists pharmacist and manages customer service',1, NOW(), 0),
('Inventory Manager', 'logistics', 'pharmacy', 'Manages stock and medication inventory', 1, NOW(), 0),
('Cashier', 'finance', 'pharmacy', 'Handles transactions and billing at the pharmacy',1, NOW(), 0),
('Customer', 'user', 'pharmacy', 'Accesses pharmacy services and orders medications', 1, NOW(), 0),
('Delivery Staff', 'logistics', 'pharmacy', 'Delivers medications to customers',1, NOW(), 0);
