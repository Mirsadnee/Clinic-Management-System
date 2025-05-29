-- Add admin user
INSERT INTO users (name, email, password, role, status, created_at) 
VALUES (
    'Admin User',
    'admin@clinic.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'admin',
    'active',
    NOW()
);

-- Add doctor user
INSERT INTO users (name, email, password, role, clinic_id, status, created_at) 
VALUES (
    'Dr. John Smith',
    'doctor@clinic.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'doctor',
    1, -- Assuming clinic_id 1 exists
    'active',
    NOW()
); 