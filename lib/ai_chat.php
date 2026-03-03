<?php
require_once 'db.php';

header('Content-Type: application/json');

$user = requireAuth();

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['message'])) {
    sendErrorResponse('Message is required');
}

$userMessage = trim($data['message']);

if (empty($userMessage)) {
    sendErrorResponse('Message cannot be empty');
}

try {
    // Simulate AI response based on keywords
    $aiResponse = generateAIResponse($userMessage);
    
    // Store conversation in database (optional)
    $conn = getDBConnection();
    $stmt = $conn->prepare("
        INSERT INTO data_records (user_id, title, types, content, description)
        VALUES (?, ?, 'ai_chat', ?, ?)
    ");
    $stmt->execute([
        $user['id'],
        'AI Chat - ' . date('Y-m-d H:i:s'),
        json_encode(['user' => $userMessage, 'ai' => $aiResponse]),
        substr($userMessage, 0, 100)
    ]);
    
    sendSuccessResponse([
        'response' => $aiResponse,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    sendErrorResponse('Error processing request: ' . $e->getMessage());
}

function generateAIResponse($message) {
    $message = strtolower($message);
    
    // Legal topic responses
    if (strpos($message, 'divorce') !== false || strpos($message, 'marriage') !== false) {
        return "For divorce or marriage-related matters, you'll need to consider:\n\n1. **Grounds for Divorce**: Based on Indian law, you can file for divorce on grounds like cruelty, adultery, desertion, or mutual consent.\n2. **Documentation**: Marriage certificate, proof of residence, and evidence supporting your grounds.\n3. **Alimony**: Either spouse may claim maintenance depending on their financial situation.\n4. **Child Custody**: Courts prioritize the child's welfare when deciding custody.\n\nI recommend consulting with a family law expert for your specific case. Would you like me to connect you with an expert?";
    }
    
    if (strpos($message, 'property') !== false || strpos($message, 'real estate') !== false) {
        return "For property or real estate matters:\n\n1. **Property Rights**: In India, property can be acquired through purchase, inheritance, or gift.\n2. **Documentation**: Ensure proper sale deed, title deed, and registration.\n3. **Disputes**: Property disputes can be resolved through civil courts or arbitration.\n4. **Succession**: Property inheritance follows the Hindu Succession Act or respective personal laws.\n\nFor detailed guidance on your property issue, I suggest booking a consultation with a property law expert.";
    }
    
    if (strpos($message, 'criminal') !== false || strpos($message, 'fir') !== false || strpos($message, 'police') !== false) {
        return "Regarding criminal matters:\n\n1. **FIR (First Information Report)**: Can be filed at any police station. If police refuse, you can approach the Magistrate.\n2. **Arrest**: Know your rights - you can't be detained without arrest memo and you have right to legal representation.\n3. **Bail**: Available for most offenses except serious crimes. Can be applied for at police station, Magistrate, or Sessions Court.\n4. **Legal Rights**: Right to remain silent, right to lawyer, right to be informed of charges.\n\nFor immediate legal assistance in criminal matters, please consult with a criminal law expert through our platform.";
    }
    
    if (strpos($message, 'contract') !== false || strpos($message, 'agreement') !== false) {
        return "For contract and agreement matters:\n\n1. **Valid Contract**: Must have offer, acceptance, consideration, and legal purpose.\n2. **Written vs Oral**: Written contracts are easier to enforce, though oral contracts are valid.\n3. **Breach**: If one party fails to fulfill obligations, you can seek damages or specific performance.\n4. **Important Clauses**: Include termination, dispute resolution, payment terms, and confidentiality.\n\nWould you like to schedule a consultation with a corporate law expert to review your contract?";
    }
    
    if (strpos($message, 'tenant') !== false || strpos($message, 'landlord') !== false || strpos($message, 'rent') !== false) {
        return "For landlord-tenant issues:\n\n1. **Rent Agreement**: Should be in writing and registered if longer than 11 months.\n2. **Eviction**: Landlords can evict tenants only on grounds specified in rent control laws (non-payment, personal use, etc.).\n3. **Security Deposit**: Typically 2-3 months rent, must be returned within specified period after vacating.\n4. **Rights**: Tenants have right to peaceful possession; landlords have right to timely rent.\n\nFor specific rental disputes, I recommend consulting with a property law expert.";
    }
    
    if (strpos($message, 'cheque') !== false || strpos($message, 'payment') !== false || strpos($message, 'bounce') !== false) {
        return "For cheque bounce cases:\n\n1. **Negotiable Instruments Act**: Cheque bounce is a criminal offense under Section 138.\n2. **Legal Notice**: Must send notice within 30 days of receiving information about dishonor.\n3. **Complaint**: Can be filed within 1 month of expiry of 15-day notice period.\n4. **Penalty**: Up to 2 years imprisonment and/or monetary penalty.\n\nThis is time-sensitive. Please consult with a legal expert immediately if you're dealing with a cheque bounce case.";
    }
    
    if (strpos($message, 'consumer') !== false || strpos($message, 'defect') !== false || strpos($message, 'refund') !== false) {
        return "For consumer rights issues:\n\n1. **Consumer Protection Act**: Protects against defective goods, deficient services, and unfair trade practices.\n2. **Complaint Process**: Can file complaint in District Forum (claims up to ₹1 crore), State Commission, or National Commission.\n3. **Remedy**: Can claim replacement, refund, compensation, or removal of defects.\n4. **Time Limit**: Complaint must be filed within 2 years from cause of action.\n\nFor consumer dispute resolution, consider consulting our legal experts.";
    }
    
    if (strpos($message, 'will') !== false || strpos($message, 'inheritance') !== false || strpos($message, 'succession') !== false) {
        return "For will and succession matters:\n\n1. **Making a Will**: Any person above 18 years of age and of sound mind can make a will.\n2. **Registration**: Not mandatory but recommended for authenticity.\n3. **Succession**: In absence of will, property distributed according to succession laws.\n4. **Challenging a Will**: Can be challenged on grounds of undue influence, fraud, or lack of testamentary capacity.\n\nFor drafting or executing a will, please consult with our succession law experts.";
    }
    
    if (strpos($message, 'cyber') !== false || strpos($message, 'online') !== false || strpos($message, 'fraud') !== false) {
        return "For cyber crime and online fraud:\n\n1. **IT Act**: Covers cyber crimes like hacking, identity theft, phishing, cyber stalking.\n2. **Reporting**: File complaint at cybercrime.gov.in or nearest cyber cell.\n3. **Evidence**: Preserve all digital evidence - screenshots, emails, transaction records.\n4. **Banking Fraud**: Report to bank immediately and file FIR within 24 hours.\n\nCyber crimes require immediate action. Please contact law enforcement and consult with a cyber law expert.";
    }
    
    // Default legal advice
    if (strpos($message, 'lawyer') !== false || strpos($message, 'attorney') !== false || strpos($message, 'expert') !== false) {
        return "I can help you find the right legal expert! Our platform has verified lawyers specializing in:\n\n- Family Law\n- Property Law\n- Criminal Law\n- Corporate Law\n- Consumer Protection\n- Cyber Law\n- Tax Law\n\nWould you like to browse our expert directory or should I help you with a specific legal question first?";
    }
    
    // General response
    return "I'm your AI legal assistant. While I can provide general legal information, please note that this is not a substitute for professional legal advice.\n\nI can help you with:\n- Understanding legal concepts and procedures\n- General guidance on Indian law\n- Connecting you with expert lawyers\n- Navigating the legal system\n\nCould you please provide more details about your legal concern? For example:\n- What type of legal issue are you facing? (Family, Property, Criminal, Contract, etc.)\n- What specific question do you have?\n- Do you need immediate consultation with a lawyer?\n\nFeel free to ask about any legal matter, and I'll do my best to guide you!";
}
