-- Add plain password column to users table
-- WARNING: Storing plain text passwords is NOT recommended for production
-- This is for testing/development purposes only

ALTER TABLE users ADD COLUMN plain_password VARCHAR(255) NULL AFTER password;

-- Verify the new column was added
DESCRIBE users;

-- Optional: Add index if you need to search by plain_password
-- ALTER TABLE users ADD INDEX idx_plain_password (plain_password);
