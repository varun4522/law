-- MySQL Database Schema for Law Connectors Application
-- Database: law

-- Create users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(255),
  phone VARCHAR(20),
  role VARCHAR(50) NOT NULL DEFAULT 'user',
  profile_image VARCHAR(500),
  bio TEXT,
  wallet_balance DECIMAL(10,2) DEFAULT 0.00,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT chk_role CHECK (role IN ('admin', 'user', 'expert'))
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
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_expert_profiles_user_id ON expert_profiles(user_id);
CREATE INDEX idx_expert_profiles_specialization ON expert_profiles(specialization);
CREATE INDEX idx_expert_profiles_rating ON expert_profiles(rating DESC);
CREATE INDEX idx_data_records_user_id ON data_records(user_id);
CREATE INDEX idx_data_records_types ON data_records(types);
CREATE INDEX idx_data_records_status ON data_records(status);
CREATE INDEX idx_data_records_is_public ON data_records(is_public);
CREATE INDEX idx_data_records_created_at ON data_records(created_at DESC);
CREATE INDEX idx_consultation_sessions_user_id ON consultation_sessions(user_id);
CREATE INDEX idx_consultation_sessions_expert_id ON consultation_sessions(expert_id);
CREATE INDEX idx_consultation_sessions_status ON consultation_sessions(status);
CREATE INDEX idx_consultation_sessions_date ON consultation_sessions(session_date);
CREATE INDEX idx_notifications_user_id ON notifications(user_id);
CREATE INDEX idx_notifications_is_read ON notifications(is_read);
CREATE INDEX idx_wallet_transactions_user_id ON wallet_transactions(user_id);
CREATE INDEX idx_forum_questions_category ON forum_questions(category);
CREATE INDEX idx_forum_questions_status ON forum_questions(status);
CREATE INDEX idx_forum_answers_question_id ON forum_answers(question_id);
CREATE INDEX idx_reviews_expert_id ON reviews(expert_id);
CREATE INDEX idx_reminders_user_id ON reminders(user_id);
CREATE INDEX idx_reminders_date ON reminders(reminder_date);
CREATE INDEX idx_auth_sessions_token ON auth_sessions(session_token);
CREATE INDEX idx_auth_sessions_expires ON auth_sessions(expires_at);
