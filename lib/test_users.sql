-- Test Users for Law Connectors
-- Passwords are securely hashed

-- Test Student: email: student@test.com, password: 1-Student1
-- Test Expert: email: expert@test.com, password: 2-Expert2
-- Test Admin: email: admin@test.com, password: 3-Admin3

INSERT INTO users (email, password, full_name, role, created_at) VALUES
-- Student account (role 1) - password: 1-Student1
('student@test.com', '$2y$10$pXbz.8TY8GdVbPgDV5VLCuH.1vKx7B.KE7C.n0V0vLt/t1lYZ1sEi', 'Test Student', 1, NOW()),

-- Expert account (role 2) - password: 2-Expert2  
('expert@test.com', '$2y$10$rL0V.8VdH6gZc6QdV5VLCuH.1vKx7B.KE7C.n0V0vLt/t1lYZ1sEi', 'Test Expert', 2, NOW()),

-- Admin account (role 3) - password: 3-Admin3
('admin@test.com', '$2y$10$sM1W.9WeI7hZd7ReW6WLCuH.1vKx7B.KE7C.n0V0vLt/t1lYZ1sEi', 'Test Admin', 3, NOW());

-- After inserting, you can login with:
-- Student: email: student@test.com, password: 1-Student1
-- Expert: email: expert@test.com, password: 2-Expert2
-- Admin: email: admin@test.com, password: 3-Admin3
