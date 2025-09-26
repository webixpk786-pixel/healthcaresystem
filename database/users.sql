INSERT INTO `users` (
  `role_id`, `username`, `email`, `password_hash`, `auth_key`, `password_reset_token`,
  `first_name`, `last_name`, `gender`, `dob`, `cnic`, `phone`, `alternate_phone`,
  `address`, `city`, `country`, `status`, `profile_image`, `last_login_at`, `created_at`, `updated_at`, `id_deleted`, `role`
) VALUES
-- hospital_admin
(1, 'admin_ali', 'ali.admin@example.com', 'hashed_password_1', 'authkey1', NULL, 'Ali', 'Khan', 'Male', '1975-03-15', '35202-1234567-1', '+923001234567', NULL, '123 Main St', 'Karachi', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'hospital_admin'),
(1, 'admin_fatima', 'fatima.admin@example.com', 'hashed_password_2', 'authkey2', NULL, 'Fatima', 'Saeed', 'Female', '1980-07-22', '42201-7654321-3', '+923212345678', NULL, '45 Shahrah-e-Faisal', 'Karachi', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'hospital_admin'),

-- doctor
(2, 'dr_ahmed', 'ahmed.doctor@example.com', 'hashed_password_3', 'authkey3', NULL, 'Ahmed', 'Raza', 'Male', '1985-05-10', '35202-9876543-2', '+923001112233', NULL, '789 Medical Rd', 'Lahore', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'doctor'),
(2, 'dr_sadia', 'sadia.doctor@example.com', 'hashed_password_4', 'authkey4', NULL, 'Sadia', 'Javed', 'Female', '1987-11-30', '61101-2345678-7', '+923334445556', NULL, '56 Gulberg', 'Lahore', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'doctor'),

-- nurse
(3, 'nurse_umar', 'umar.nurse@example.com', 'hashed_password_5', 'authkey5', NULL, 'Umar', 'Iqbal', 'Male', '1990-09-12', '35202-8765432-9', '+923005556677', NULL, '22 Aziz Ave', 'Islamabad', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'nurse'),
(3, 'nurse_hina', 'hina.nurse@example.com', 'hashed_password_6', 'authkey6', NULL, 'Hina', 'Khan', 'Female', '1992-02-20', '61101-7654321-4', '+923336667788', NULL, '77 Blue Area', 'Islamabad', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'nurse'),

-- receptionist
(4, 'reception_ali', 'ali.reception@example.com', 'hashed_password_7', 'authkey7', NULL, 'Ali', 'Shah', 'Male', '1988-06-25', '35202-3344556-5', '+923009998877', NULL, '11 Station Rd', 'Faisalabad', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'receptionist'),
(4, 'reception_sara', 'sara.reception@example.com', 'hashed_password_8', 'authkey8', NULL, 'Sara', 'Bano', 'Female', '1991-04-18', '42201-1122334-6', '+923312345679', NULL, '89 College Rd', 'Faisalabad', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'receptionist'),

-- lab_technician
(5, 'lab_ahmed', 'ahmed.lab@example.com', 'hashed_password_9', 'authkey9', NULL, 'Ahmed', 'Malik', 'Male', '1984-12-08', '35202-9988776-8', '+923002233445', NULL, '34 Lab St', 'Multan', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'lab_technician'),
(5, 'lab_nida', 'nida.lab@example.com', 'hashed_password_10', 'authkey10', NULL, 'Nida', 'Iqbal', 'Female', '1989-08-19', '61101-4433221-2', '+923334455667', NULL, '12 Science Park', 'Multan', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'lab_technician'),

-- radiologist
(6, 'radio_ahmed', 'ahmed.radio@example.com', 'hashed_password_11', 'authkey11', NULL, 'Ahmed', 'Nawaz', 'Male', '1983-07-03', '35202-5544332-1', '+923001122334', NULL, '90 Hospital Rd', 'Peshawar', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'radiologist'),
(6, 'radio_sania', 'sania.radio@example.com', 'hashed_password_12', 'authkey12', NULL, 'Sania', 'Khan', 'Female', '1986-03-27', '42201-6677889-3', '+923335577889', NULL, '23 City Center', 'Peshawar', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'radiologist'),

-- surgeon
(7, 'surgeon_faisal', 'faisal.surgeon@example.com', 'hashed_password_13', 'authkey13', NULL, 'Faisal', 'Iqbal', 'Male', '1982-10-14', '35202-2233445-6', '+923009988776', NULL, '45 Surgery Rd', 'Quetta', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'surgeon'),
(7, 'surgeon_ameen', 'ameen.surgeon@example.com', 'hashed_password_14', 'authkey14', NULL, 'Ameen', 'Hussain', 'Male', '1979-01-30', '42201-5566778-4', '+923331122334', NULL, '67 Medical St', 'Quetta', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'surgeon'),

-- billing_staff
(8, 'billing_maria', 'maria.billing@example.com', 'hashed_password_15', 'authkey15', NULL, 'Maria', 'Shah', 'Female', '1987-09-05', '35202-7788990-7', '+923003344556', NULL, '55 Finance Rd', 'Karachi', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'billing_staff'),
(8, 'billing_ahmed', 'ahmed.billing@example.com', 'hashed_password_16', 'authkey16', NULL, 'Ahmed', 'Farooq', 'Male', '1990-11-12', '42201-8899001-5', '+923334455778', NULL, '34 Billing St', 'Karachi', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'billing_staff'),

-- patient
(9, 'patient_umer', 'umer.patient@example.com', 'hashed_password_17', 'authkey17', NULL, 'Umer', 'Farooq', 'Male', '1995-07-21', '35202-1122334-8', '+923001234890', NULL, '89 Patient Lane', 'Lahore', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'patient'),
(9, 'patient_sana', 'sana.patient@example.com', 'hashed_password_18', 'authkey18', NULL, 'Sana', 'Khan', 'Female', '1993-02-14', '42201-4455667-9', '+923334455990', NULL, '123 Health St', 'Lahore', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'patient'),

-- pharmacist (hospital)
(10, 'pharma_ali', 'ali.pharma@example.com', 'hashed_password_19', 'authkey19', NULL, 'Ali', 'Raza', 'Male', '1986-05-17', '35202-9988775-3', '+923001112244', NULL, '34 Pharma Rd', 'Karachi', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'pharmacist'),
(10, 'pharma_sara', 'sara.pharma@example.com', 'hashed_password_20', 'authkey20', NULL, 'Sara', 'Aslam', 'Female', '1988-12-05', '42201-2233445-2', '+923334455221', NULL, '55 Pharma St', 'Karachi', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'pharmacist'),

-- pharmacy_admin
(11, 'pharmacy_admin_ali', 'ali.pharmacyadmin@example.com', 'hashed_password_21', 'authkey21', NULL, 'Ali', 'Saeed', 'Male', '1978-03-10', '35202-5566778-5', '+923001223344', NULL, '22 Pharmacy Rd', 'Islamabad', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'pharmacy_admin'),
(11, 'pharmacy_admin_fatima', 'fatima.pharmacyadmin@example.com', 'hashed_password_22', 'authkey22', NULL, 'Fatima', 'Nawaz', 'Female', '1980-08-15', '42201-6677889-7', '+923334477889', NULL, '34 Pharmacy St', 'Islamabad', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'pharmacy_admin'),

-- pharmacy_assistant
(12, 'pharma_assistant_umar', 'umar.assistant@example.com', 'hashed_password_23', 'authkey23', NULL, 'Umar', 'Khan', 'Male', '1990-06-22', '35202-7788990-4', '+923005566778', NULL, '89 Assistant Rd', 'Faisalabad', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'pharmacy_assistant'),
(12, 'pharma_assistant_nida', 'nida.assistant@example.com', 'hashed_password_24', 'authkey24', NULL, 'Nida', 'Iqbal', 'Female', '1992-09-19', '42201-8899001-6', '+923336677889', NULL, '45 Assistant St', 'Faisalabad', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'pharmacy_assistant'),

-- inventory_manager
(13, 'inventory_ahmed', 'ahmed.inventory@example.com', 'hashed_password_25', 'authkey25', NULL, 'Ahmed', 'Malik', 'Male', '1985-04-11', '35202-2233445-3', '+923002244556', NULL, '12 Inventory Rd', 'Multan', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'inventory_manager'),
(13, 'inventory_sania', 'sania.inventory@example.com', 'hashed_password_26', 'authkey26', NULL, 'Sania', 'Nawaz', 'Female', '1987-11-23', '42201-3344556-4', '+923335577890', NULL, '34 Inventory St', 'Multan', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'inventory_manager'),

-- cashier
(14, 'cashier_umer', 'umer.cashier@example.com', 'hashed_password_27', 'authkey27', NULL, 'Umer', 'Farooq', 'Male', '1991-01-17', '35202-4455667-2', '+923001122334', NULL, '67 Cashier Rd', 'Peshawar', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'cashier'),
(14, 'cashier_saba', 'saba.cashier@example.com', 'hashed_password_28', 'authkey28', NULL, 'Saba', 'Khan', 'Female', '1993-05-29', '42201-5566778-3', '+923334455667', NULL, '89 Cashier St', 'Peshawar', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'cashier'),

-- customer
(15, 'customer_ali', 'ali.customer@example.com', 'hashed_password_29', 'authkey29', NULL, 'Ali', 'Shah', 'Male', '1989-03-21', '35202-6677889-1', '+923003344556', NULL, '34 Customer Rd', 'Quetta', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'customer'),
(15, 'customer_fatima', 'fatima.customer@example.com', 'hashed_password_30', 'authkey30', NULL, 'Fatima', 'Bano', 'Female', '1990-12-12', '42201-7788990-2', '+923334466778', NULL, '56 Customer St', 'Quetta', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'customer'),

-- delivery_staff
(16, 'delivery_ahmed', 'ahmed.delivery@example.com', 'hashed_password_31', 'authkey31', NULL, 'Ahmed', 'Iqbal', 'Male', '1988-07-14', '35202-8899001-3', '+923001133445', NULL, '78 Delivery Rd', 'Karachi', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'delivery_staff'),
(16, 'delivery_sana', 'sana.delivery@example.com', 'hashed_password_32', 'authkey32', NULL, 'Sana', 'Malik', 'Female', '1991-09-08', '42201-9900112-4', '+923335577889', NULL, '90 Delivery St', 'Karachi', 'Pakistan', 1, NULL, NOW(), NOW(), NOW(), 0, 'delivery_staff');
