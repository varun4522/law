-- MySQL Database Schema for Law Connectors Application
-- Database: law

CREATE DATABASE IF NOT EXISTS law;
USE law;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(255),
  phone VARCHAR(20),
  `role` TINYINT NOT NULL DEFAULT 1 COMMENT '1=student 2=expert 3=admin',
  profile_image VARCHAR(500),
  bio TEXT,
  wallet_balance DECIMAL(10,2) DEFAULT 0.00,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create expert_profiles table for expert specific information
CREATE TABLE IF NOT EXISTS expert_profiles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  specialization VARCHAR(255),
  experience_years INT,
  language VARCHAR(255),
  availability_status VARCHAR(50) DEFAULT 'available',
  hourly_rate DECIMAL(10,2),
  rating DECIMAL(3,2) DEFAULT 0.00,
  total_reviews INT DEFAULT 0,
  total_sessions INT DEFAULT 0,
  verification_status VARCHAR(50) DEFAULT 'pending',
  probono_participation TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT chk_availability CHECK (availability_status IN ('available', 'busy', 'offline')),
  CONSTRAINT chk_verification CHECK (verification_status IN ('pending', 'verified', 'rejected'))
);

-- Create data_records table
CREATE TABLE IF NOT EXISTS data_records (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  types VARCHAR(100) NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  content TEXT,
  file_path VARCHAR(500),
  status VARCHAR(50) DEFAULT 'draft',
  is_public TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by_role VARCHAR(50),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT chk_status CHECK (status IN ('draft', 'published', 'archived'))
);

-- Create consultation_sessions table
CREATE TABLE IF NOT EXISTS consultation_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  expert_id INT NOT NULL,
  session_date DATETIME NOT NULL,
  duration INT DEFAULT 60,
  session_type VARCHAR(50),
  status VARCHAR(50) DEFAULT 'pending',
  amount DECIMAL(10,2),
  commission DECIMAL(10,2),
  notes TEXT,
  rating INT,
  review TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (expert_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT chk_session_status CHECK (status IN ('pending', 'confirmed', 'completed', 'cancelled', 'locked')),
  CONSTRAINT chk_session_type CHECK (session_type IN ('video', 'audio', 'chat', 'in-person'))
);

-- Create notifications table
CREATE TABLE IF NOT EXISTS notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  type VARCHAR(50),
  is_read TINYINT(1) DEFAULT 0,
  link VARCHAR(500),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT chk_notification_type CHECK (type IN ('system', 'session', 'payment', 'message', 'reminder'))
);

-- Create wallet_transactions table
CREATE TABLE IF NOT EXISTS wallet_transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  transaction_type VARCHAR(50),
  amount DECIMAL(10,2) NOT NULL,
  description TEXT,
  reference_id VARCHAR(255),
  status VARCHAR(50) DEFAULT 'completed',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT chk_transaction_type CHECK (transaction_type IN ('credit', 'debit', 'refund', 'commission')),
  CONSTRAINT chk_transaction_status CHECK (status IN ('pending', 'completed', 'failed'))
);

-- Create forum_questions table (Anonymous Ask a Lawyer)
CREATE TABLE IF NOT EXISTS forum_questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  title VARCHAR(500) NOT NULL,
  question TEXT NOT NULL,
  category VARCHAR(100),
  is_anonymous TINYINT(1) DEFAULT 0,
  views INT DEFAULT 0,
  status VARCHAR(50) DEFAULT 'open',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT chk_forum_status CHECK (status IN ('open', 'answered', 'closed'))
);

-- Create forum_answers table
CREATE TABLE IF NOT EXISTS forum_answers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  question_id INT NOT NULL,
  user_id INT NOT NULL,
  answer TEXT NOT NULL,
  is_helpful INT DEFAULT 0,
  is_best_answer TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (question_id) REFERENCES forum_questions(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create reviews table
CREATE TABLE IF NOT EXISTS reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  expert_id INT NOT NULL,
  user_id INT NOT NULL,
  session_id INT,
  rating INT NOT NULL,
  review TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (expert_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (session_id) REFERENCES consultation_sessions(id) ON DELETE SET NULL,
  CONSTRAINT chk_rating CHECK (rating BETWEEN 1 AND 5)
);

-- Create reminders table
CREATE TABLE IF NOT EXISTS reminders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  reminder_date DATETIME NOT NULL,
  is_sent TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create probono_club table
CREATE TABLE IF NOT EXISTS probono_club (
  id INT AUTO_INCREMENT PRIMARY KEY,
  expert_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  category VARCHAR(100),
  status VARCHAR(50) DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (expert_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT chk_probono_status CHECK (status IN ('active', 'inactive', 'completed'))
);

-- Create auth sessions table for user sessions
CREATE TABLE IF NOT EXISTS auth_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  session_token VARCHAR(255) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  expires_at TIMESTAMP NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);
CREATE INDEX IF NOT EXISTS idx_expert_profiles_user_id ON expert_profiles(user_id);
CREATE INDEX IF NOT EXISTS idx_expert_profiles_specialization ON expert_profiles(specialization);
CREATE INDEX IF NOT EXISTS idx_expert_profiles_rating ON expert_profiles(rating DESC);
CREATE INDEX IF NOT EXISTS idx_data_records_user_id ON data_records(user_id);
CREATE INDEX IF NOT EXISTS idx_data_records_types ON data_records(types);
CREATE INDEX IF NOT EXISTS idx_data_records_status ON data_records(status);
CREATE INDEX IF NOT EXISTS idx_data_records_is_public ON data_records(is_public);
CREATE INDEX IF NOT EXISTS idx_data_records_created_at ON data_records(created_at DESC);
CREATE INDEX IF NOT EXISTS idx_consultation_sessions_user_id ON consultation_sessions(user_id);
CREATE INDEX IF NOT EXISTS idx_consultation_sessions_expert_id ON consultation_sessions(expert_id);
CREATE INDEX IF NOT EXISTS idx_consultation_sessions_status ON consultation_sessions(status);
CREATE INDEX IF NOT EXISTS idx_consultation_sessions_date ON consultation_sessions(session_date);
CREATE INDEX IF NOT EXISTS idx_notifications_user_id ON notifications(user_id);
CREATE INDEX IF NOT EXISTS idx_notifications_is_read ON notifications(is_read);
CREATE INDEX IF NOT EXISTS idx_wallet_transactions_user_id ON wallet_transactions(user_id);
CREATE INDEX IF NOT EXISTS idx_forum_questions_category ON forum_questions(category);
CREATE INDEX IF NOT EXISTS idx_forum_questions_status ON forum_questions(status);
CREATE INDEX IF NOT EXISTS idx_forum_answers_question_id ON forum_answers(question_id);
CREATE INDEX IF NOT EXISTS idx_reviews_expert_id ON reviews(expert_id);
CREATE INDEX IF NOT EXISTS idx_reminders_user_id ON reminders(user_id);
CREATE INDEX IF NOT EXISTS idx_reminders_date ON reminders(reminder_date);
CREATE INDEX IF NOT EXISTS idx_auth_sessions_token ON auth_sessions(session_token);
CREATE INDEX IF NOT EXISTS idx_auth_sessions_expires ON auth_sessions(expires_at);

-- Advanced Features Tables

-- Favorite experts table (for students)
CREATE TABLE IF NOT EXISTS favorite_experts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  expert_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (expert_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_favorite (user_id, expert_id)
);

-- Expert availability schedule
CREATE TABLE IF NOT EXISTS expert_availability (
  id INT AUTO_INCREMENT PRIMARY KEY,
  expert_id INT NOT NULL,
  day_of_week VARCHAR(20) NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  is_available TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (expert_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Session documents (uploaded by users/experts)
CREATE TABLE IF NOT EXISTS session_documents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id INT NOT NULL,
  uploaded_by INT NOT NULL,
  title VARCHAR(255),
  description TEXT,
  file_path VARCHAR(500) NOT NULL,
  file_size INT,
  file_type VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (session_id) REFERENCES consultation_sessions(id) ON DELETE CASCADE,
  FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Session templates (for experts)
CREATE TABLE IF NOT EXISTS session_templates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  expert_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  category VARCHAR(100) DEFAULT 'general',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (expert_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Expert certifications
CREATE TABLE IF NOT EXISTS expert_certifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  expert_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  type VARCHAR(100) DEFAULT 'other',
  issuer VARCHAR(255),
  issue_date DATE,
  file_path VARCHAR(500),
  verification_status VARCHAR(50) DEFAULT 'pending',
  verified_at TIMESTAMP NULL,
  verified_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (expert_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT chk_cert_verification CHECK (verification_status IN ('pending', 'verified', 'rejected'))
);

-- Disputes table
CREATE TABLE IF NOT EXISTS disputes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  session_id INT,
  reason TEXT NOT NULL,
  description TEXT,
  status VARCHAR(50) DEFAULT 'pending',
  refund_amount DECIMAL(10,2) DEFAULT 0.00,
  admin_notes TEXT,
  resolved_at TIMESTAMP NULL,
  resolved_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (session_id) REFERENCES consultation_sessions(id) ON DELETE SET NULL,
  FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT chk_dispute_status CHECK (status IN ('pending', 'resolved', 'rejected'))
);

-- System logs (audit trail)
CREATE TABLE IF NOT EXISTS system_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  action VARCHAR(100) NOT NULL,
  details TEXT,
  ip_address VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Content reports (for forum moderation)
CREATE TABLE IF NOT EXISTS content_reports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  content_type VARCHAR(50) NOT NULL,
  content_id INT NOT NULL,
  reported_by INT NOT NULL,
  reason VARCHAR(255) NOT NULL,
  description TEXT,
  status VARCHAR(50) DEFAULT 'pending',
  resolution TEXT,
  admin_notes TEXT,
  resolved_at TIMESTAMP NULL,
  resolved_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (reported_by) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT chk_report_status CHECK (status IN ('pending', 'resolved', 'dismissed')),
  CONSTRAINT chk_content_type CHECK (content_type IN ('question', 'answer'))
);

-- Platform settings
CREATE TABLE IF NOT EXISTS platform_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_value TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add status column to users table (for activate/deactivate)
ALTER TABLE users ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'active';

-- Drop old string-based role CHECK constraint if it exists (MariaDB syntax)
ALTER TABLE users DROP CONSTRAINT IF EXISTS chk_role;

-- ── Migrate role to numeric (run once on existing databases) ──────────────────
-- If your users table already has string roles, run these to convert them:
-- UPDATE users SET role = 1 WHERE role = 'user';
-- UPDATE users SET role = 2 WHERE role = 'expert';
-- UPDATE users SET role = 3 WHERE role = 'admin';
-- ALTER TABLE users MODIFY COLUMN role TINYINT NOT NULL DEFAULT 1 COMMENT '1=student 2=expert 3=admin';
-- ─────────────────────────────────────────────────────────────────────────────

-- Create indexes for new tables
CREATE INDEX IF NOT EXISTS idx_favorite_experts_user ON favorite_experts(user_id);
CREATE INDEX IF NOT EXISTS idx_favorite_experts_expert ON favorite_experts(expert_id);
CREATE INDEX IF NOT EXISTS idx_expert_availability_expert ON expert_availability(expert_id);
CREATE INDEX IF NOT EXISTS idx_expert_availability_day ON expert_availability(day_of_week);
CREATE INDEX IF NOT EXISTS idx_session_documents_session ON session_documents(session_id);
CREATE INDEX IF NOT EXISTS idx_session_documents_uploader ON session_documents(uploaded_by);
CREATE INDEX IF NOT EXISTS idx_session_templates_expert ON session_templates(expert_id);
CREATE INDEX IF NOT EXISTS idx_expert_certifications_expert ON expert_certifications(expert_id);
CREATE INDEX IF NOT EXISTS idx_expert_certifications_status ON expert_certifications(verification_status);
CREATE INDEX IF NOT EXISTS idx_disputes_user ON disputes(user_id);
CREATE INDEX IF NOT EXISTS idx_disputes_status ON disputes(status);
CREATE INDEX IF NOT EXISTS idx_system_logs_user ON system_logs(user_id);
CREATE INDEX IF NOT EXISTS idx_system_logs_action ON system_logs(action);
CREATE INDEX IF NOT EXISTS idx_system_logs_created ON system_logs(created_at DESC);
CREATE INDEX IF NOT EXISTS idx_content_reports_status ON content_reports(status);
CREATE INDEX IF NOT EXISTS idx_content_reports_type ON content_reports(content_type, content_id);

-- Insert default platform settings
INSERT INTO platform_settings (setting_key, setting_value) 
VALUES ('commission_rate', '15') 
ON DUPLICATE KEY UPDATE setting_key = setting_key;
