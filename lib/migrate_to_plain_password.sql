-- Update Users Table to use Plain Password (No Hashing)
-- WARNING: Storing plain text passwords is NOT recommended for production

-- Step 1: If plain_password column doesn't exist, add it
-- ALTER TABLE users ADD COLUMN plain_password VARCHAR(255) NULL AFTER password;

-- Step 2: If you have existing data in password column, migrate it to plain_password
-- UPDATE users SET plain_password = password WHERE plain_password IS NULL;

-- Step 3: Drop the old encrypted password column (if it exists)
-- ALTER TABLE users DROP COLUMN password;

-- Now your users table uses plain_password for authentication

-- Verify the structure:
DESCRIBE users;

-- Insert test users with plain passwords:
INSERT INTO users (email, plain_password, full_name, role, created_at) VALUES
('student@test.com', '1-Student1', 'Test Student', 1, NOW()),
('expert@test.com', '2-Expert2', 'Test Expert', 2, NOW()),
('admin@test.com', '3-Admin3', 'Test Admin', 3, NOW());

-- Login credentials:
-- Student: student@test.com / 1-Student1
-- Expert: expert@test.com / 2-Expert2
-- Admin: admin@test.com / 3-Admin3
