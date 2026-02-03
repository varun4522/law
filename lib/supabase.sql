-- Create a profiles table to store user roles
CREATE TABLE profiles (
  id UUID PRIMARY KEY REFERENCES auth.users(id) ON DELETE CASCADE,
  email VARCHAR(255) NOT NULL UNIQUE,
  full_name VARCHAR(255),
  role VARCHAR(50) NOT NULL CHECK (role IN ('admin', 'user', 'expert')),
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW()
);

-- Create a main data table with types column
CREATE TABLE data_records (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id UUID NOT NULL REFERENCES auth.users(id) ON DELETE CASCADE,
  types VARCHAR(100) NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  content TEXT,
  status VARCHAR(50) DEFAULT 'draft' CHECK (status IN ('draft', 'published', 'archived')),
  is_public BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW(),
  created_by_role VARCHAR(50)
);

-- Enable Row Level Security (RLS)
ALTER TABLE profiles ENABLE ROW LEVEL SECURITY;
ALTER TABLE data_records ENABLE ROW LEVEL SECURITY;

-- ========== POLICIES FOR PROFILES TABLE ==========

-- Admin can view all profiles
CREATE POLICY "Admin can view all profiles"
ON profiles
FOR SELECT
USING (
  auth.uid() IN (
    SELECT id FROM profiles WHERE role = 'admin'
  )
);

-- Users can view their own profile
CREATE POLICY "Users can view own profile"
ON profiles
FOR SELECT
USING (auth.uid() = id);

-- Users can update their own profile
CREATE POLICY "Users can update own profile"
ON profiles
FOR UPDATE
USING (auth.uid() = id)
WITH CHECK (auth.uid() = id AND role = 'user');

-- Admin can update any profile
CREATE POLICY "Admin can update all profiles"
ON profiles
FOR UPDATE
USING (
  auth.uid() IN (
    SELECT id FROM profiles WHERE role = 'admin'
  )
);

-- Admin can delete any profile
CREATE POLICY "Admin can delete all profiles"
ON profiles
FOR DELETE
USING (
  auth.uid() IN (
    SELECT id FROM profiles WHERE role = 'admin'
  )
);

-- ========== POLICIES FOR DATA_RECORDS TABLE ==========

-- Users can view their own records
CREATE POLICY "Users can view own records"
ON data_records
FOR SELECT
USING (auth.uid() = user_id);

-- Users can view public records
CREATE POLICY "Users can view public records"
ON data_records
FOR SELECT
USING (is_public = TRUE);

-- Experts can view all records (for analysis)
CREATE POLICY "Experts can view all records"
ON data_records
FOR SELECT
USING (
  auth.uid() IN (
    SELECT id FROM profiles WHERE role = 'expert'
  )
);

-- Admin can view all records
CREATE POLICY "Admin can view all records"
ON data_records
FOR SELECT
USING (
  auth.uid() IN (
    SELECT id FROM profiles WHERE role = 'admin'
  )
);

-- Users can insert their own records
CREATE POLICY "Users can insert own records"
ON data_records
FOR INSERT
WITH CHECK (
  auth.uid() = user_id AND
  auth.uid() IN (SELECT id FROM profiles WHERE role IN ('user', 'expert'))
);

-- Experts can insert records
CREATE POLICY "Experts can insert records"
ON data_records
FOR INSERT
WITH CHECK (
  auth.uid() IN (SELECT id FROM profiles WHERE role = 'expert')
);

-- Admin can insert records for anyone
CREATE POLICY "Admin can insert records"
ON data_records
FOR INSERT
WITH CHECK (
  auth.uid() IN (SELECT id FROM profiles WHERE role = 'admin')
);

-- Users can update their own records
CREATE POLICY "Users can update own records"
ON data_records
FOR UPDATE
USING (auth.uid() = user_id)
WITH CHECK (auth.uid() = user_id);

-- Experts can update all records
CREATE POLICY "Experts can update all records"
ON data_records
FOR UPDATE
USING (
  auth.uid() IN (SELECT id FROM profiles WHERE role = 'expert')
);

-- Admin can update all records
CREATE POLICY "Admin can update all records"
ON data_records
FOR UPDATE
USING (
  auth.uid() IN (SELECT id FROM profiles WHERE role = 'admin')
);

-- Users can delete their own records
CREATE POLICY "Users can delete own records"
ON data_records
FOR DELETE
USING (auth.uid() = user_id);

-- Experts can delete all records
CREATE POLICY "Experts can delete all records"
ON data_records
FOR DELETE
USING (
  auth.uid() IN (SELECT id FROM profiles WHERE role = 'expert')
);

-- Admin can delete all records
CREATE POLICY "Admin can delete all records"
ON data_records
FOR DELETE
USING (
  auth.uid() IN (SELECT id FROM profiles WHERE role = 'admin')
);

-- ========== CREATE INDEXES FOR PERFORMANCE ==========
CREATE INDEX idx_profiles_role ON profiles(role);
CREATE INDEX idx_profiles_email ON profiles(email);
CREATE INDEX idx_data_records_user_id ON data_records(user_id);
CREATE INDEX idx_data_records_types ON data_records(types);
CREATE INDEX idx_data_records_status ON data_records(status);
CREATE INDEX idx_data_records_is_public ON data_records(is_public);
CREATE INDEX idx_data_records_created_at ON data_records(created_at DESC);

-- ========== TRIGGERS FOR UPDATING TIMESTAMP ==========
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_profiles_updated_at
BEFORE UPDATE ON profiles
FOR EACH ROW
EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_data_records_updated_at
BEFORE UPDATE ON data_records
FOR EACH ROW
EXECUTE FUNCTION update_updated_at_column();

-- ========== OPTIONAL: Create a roles enum type ==========
CREATE TYPE user_role AS ENUM ('admin', 'user', 'expert');

-- ========== OPTIONAL: Create an audit log table ==========
CREATE TABLE audit_log (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id UUID NOT NULL REFERENCES auth.users(id),
  action VARCHAR(50) NOT NULL,
  table_name VARCHAR(100) NOT NULL,
  record_id UUID,
  old_values JSONB,
  new_values JSONB,
  created_at TIMESTAMP DEFAULT NOW()
);

-- Enable RLS on audit_log
ALTER TABLE audit_log ENABLE ROW LEVEL SECURITY;

-- Only admin can view audit logs
CREATE POLICY "Admin can view audit logs"
ON audit_log
FOR SELECT
USING (
  auth.uid() IN (
    SELECT id FROM profiles WHERE role = 'admin'
  )
);
