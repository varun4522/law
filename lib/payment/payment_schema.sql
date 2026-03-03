-- Add payments table for manual payment tracking
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_type VARCHAR(50) NOT NULL DEFAULT 'wallet_recharge', -- wallet_recharge, session_payment
    payment_method VARCHAR(50) NOT NULL DEFAULT 'paytm', -- paytm, upi, bank_transfer
    payment_reference VARCHAR(100) UNIQUE NOT NULL,
    transaction_id VARCHAR(200),
    reference_id INT, -- session_id or other reference
    status VARCHAR(20) NOT NULL DEFAULT 'pending', -- pending, completed, verified, failed
    screenshot TEXT, -- Base64 encoded payment screenshot
    admin_notes TEXT,
    verified_by INT,
    verified_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_payments (user_id, status),
    INDEX idx_payment_ref (payment_reference),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
