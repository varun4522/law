<?php
/**
 * Payment Configuration
 * Manual Payment Setup for Law Connectors
 */

// Payment Methods
define('PAYTM_NUMBER', '7206959166');
define('PAYTM_NAME', 'Law Connectors');

// Payment Details
define('UPI_ID', '7206959166@paytm');
define('PAYMENT_NOTE', 'Law Connectors - Session Payment');

// Payment Status
define('PAYMENT_PENDING', 'pending');
define('PAYMENT_COMPLETED', 'completed');
define('PAYMENT_FAILED', 'failed');
define('PAYMENT_VERIFIED', 'verified');

// Minimum and Maximum Amounts
define('MIN_PAYMENT', 100);
define('MAX_PAYMENT', 50000);

/**
 * Get Payment Details
 */
function getPaymentDetails() {
    return [
        'paytm_number' => PAYTM_NUMBER,
        'upi_id' => UPI_ID,
        'payment_name' => PAYTM_NAME,
        'payment_note' => PAYMENT_NOTE,
        'qr_text' => 'Scan QR code or Pay to: ' . PAYTM_NUMBER
    ];
}

/**
 * Generate Payment Reference Number
 */
function generatePaymentReference() {
    return 'LC' . date('Ymd') . rand(1000, 9999);
}
