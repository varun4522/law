-- Test Users for Law Connectors
-- Updated to include both hashed and plain passwords
-- WARNING: Plain passwords stored for testing purposes only

-- Student: email: student@test.com, password: 1-Student1
-- Expert: email: expert@test.com, password: 2-Expert2
-- Admin: email: admin@test.com, password: 3-Admin3

INSERT INTO users (email, password, plain_password, full_name, role, created_at) VALUES
-- Student account (role 1)
('student@test.com', '$2y$10$pXbz.8TY8GdVbPgDV5VLCuH.1vKx7B.KE7C.n0V0vLt/t1lYZ1sEi', '1-Student1', 'Test Student', 1, NOW()),

-- Expert account (role 2)
('expert@test.com', '$2y$10$rL0V.8VdH6gZc6QdV5VLCuH.1vKx7B.KE7C.n0V0vLt/t1lYZ1sEi', '2-Expert2', 'Test Expert', 2, NOW()),

-- Admin account (role 3)
('admin@test.com', '$2y$10$sM1W.9WeI7hZd7ReW6WLCuH.1vKx7B.KE7C.n0V0vLt/t1lYZ1sEi', '3-Admin3', 'Test Admin', 3, NOW());

-- Login credentials:
-- Student: student@test.com / 1-Student1
-- Expert: expert@test.com / 2-Expert2
-- Admin: admin@test.com / 3-Admin3
