<?php require_once 'lib/db.php'; requireAuth(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet - Law Connectors</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'Inter', sans-serif; background: #fff; color: #0a0a0a;}
        
        /* Navbar */
        .navbar {background: #0a0a0a; padding: 15px 0; position: sticky; top: 0; z-index: 100;}
        .nav-container {max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;}
        .logo {font-family: 'Playfair Display', serif; font-size: 24px; color: #fff; font-weight: 700; text-decoration: none;}
        .nav-links {display: flex; gap: 0; align-items: center; flex-wrap: wrap;}
        .nav-links a {color: #fff; text-decoration: none; padding: 10px 20px; font-size: 14px; font-weight: 500; transition: all 0.2s; border-radius: 2px;}
        .nav-links a:hover {background: #1a1a1a;}
        .nav-links a.active {background: #fff; color: #0a0a0a;}
        
        /* Container */
        .container {max-width: 1200px; margin: 0 auto; padding: 40px 20px;}
        
        /* Page Header */
        .page-header {margin-bottom: 40px;}
        .page-header h1 {font-family: 'Playfair Display', serif; font-size: 42px; color: #0a0a0a; margin-bottom: 10px;}
        .page-header p {color: #888; font-size: 16px;}
        
        /* Wallet Card */
        .wallet-card {background: #0a0a0a; color: #fff; padding: 40px; border-radius: 4px; margin-bottom: 30px;}
        .wallet-card .balance-label {font-size: 14px; color: #888; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;}
        .wallet-card .balance-amount {font-size: 48px; font-weight: 700; margin-bottom: 25px;}
        .wallet-card .btn {background: #fff; color: #0a0a0a; border: none; padding: 12px 28px; border-radius: 2px; cursor: pointer; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;}
        .wallet-card .btn:hover {background: #f5f5f5;}
        
        /* Content Box */
        .content-box {background: #fafafa; padding: 30px; border-radius: 4px; margin-bottom: 25px; border: 1px solid #e8e8e4;}
        .content-box h2 {font-family: 'Playfair Display', serif; font-size: 24px; color: #0a0a0a; margin-bottom: 20px;}
        
        /* Transaction List */
        .transaction-item {display: flex; justify-content: space-between; align-items: center; padding: 20px 0; border-bottom: 1px solid #e8e8e4;}
        .transaction-item:last-child {border-bottom: none;}
        .transaction-info {display: flex; align-items: center; gap: 15px; flex: 1;}
        .transaction-icon {width: 40px; height: 40px; border-radius: 2px; display: flex; align-items: center; justify-content: center; font-size: 16px;}
        .credit-icon {background: #0a0a0a; color: #fff;}
        .debit-icon {background: #f5f5f3; color: #0a0a0a;}
        .transaction-details {flex: 1;}
        .transaction-title {font-weight: 600; color: #0a0a0a; margin-bottom: 4px;}
        .transaction-date {font-size: 13px; color: #888;}
        .transaction-amount {font-size: 18px; font-weight: 700;}
        .credit-amount {color: #0a0a0a;}
        .debit-amount {color: #888;}
        
        /* Modal */
        .modal {display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; padding: 20px;}
        .modal.active {display: flex;}
        .modal-content {background: #fff; border-radius: 4px; padding: 40px; max-width: 600px; width: 100%; max-height: 90vh; overflow-y: auto;}
        .modal-header {margin-bottom: 30px;}
        .modal-header h2 {font-family: 'Playfair Display', serif; font-size: 28px; color: #0a0a0a; margin-bottom: 10px;}
        .modal-header p {color: #888; font-size: 14px;}
        
        /* Payment Instructions */
        .payment-info {background: #fafafa; padding: 25px; border-radius: 4px; margin-bottom: 25px; border: 1px solid #e8e8e4;}
        .payment-step {margin-bottom: 20px;}
        .payment-step:last-child {margin-bottom: 0;}
        .payment-step-number {display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: #0a0a0a; color: #fff; border-radius: 50%; font-size: 14px; font-weight: 700; margin-right: 12px;}
        .payment-step-text {display: inline-block; vertical-align: middle; color: #0a0a0a; font-size: 14px;}
        .paytm-number {background: #0a0a0a; color: #fff; padding: 15px 20px; border-radius: 4px; font-size: 20px; font-weight: 700; text-align: center; margin: 20px 0; letter-spacing: 2px;}
        .paytm-number small {display: block; font-size: 12px; color: #888; letter-spacing: 1px; margin-bottom: 5px; font-weight: 400;}
        
        /* Form */
        .form-group {margin-bottom: 25px;}
        .form-group label {display: block; margin-bottom: 8px; font-weight: 600; color: #0a0a0a; font-size: 14px;}
        .form-group input, .form-group select, .form-group textarea {width: 100%; padding: 12px 16px; border: 1px solid #ddd; border-radius: 2px; font-size: 14px; font-family: 'Inter', sans-serif;}
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {outline: none; border-color: #0a0a0a;}
        
        /* Buttons */
        .btn {padding: 12px 28px; border: none; border-radius: 2px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; font-family: 'Inter', sans-serif;}
        .btn-primary {background: #0a0a0a; color: #fff;}
        .btn-primary:hover {background: #1a1a1a;}
        .btn-secondary {background: #f5f5f3; color: #0a0a0a;}
        .btn-secondary:hover {background: #e8e8e4;}
        .btn-group {display: flex; gap: 10px; margin-top: 25px;}
        
        /* Status Badge */
        .status-badge {display: inline-block; padding: 4px 12px; border-radius: 2px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;}
        .status-pending {background: #fff3cd; color: #856404;}
        .status-completed {background: #d4edda; color: #155724;}
        .status-verified {background: #0a0a0a; color: #fff;}
        .status-failed {background: #f8d7da; color: #721c24;}
        
        @media (max-width: 768px) {
            .navbar-container {
                padding: 12px 16px;
            }
            .logo {
                font-size: 18px;
            }
            .nav-links {
                display: none;
            }
            .container {
                padding: 20px 16px;
            }
            .page-header h1 {
                font-size: 26px;
            }
            .wallet-card {
                padding: 24px 20px;
            }
            .balance-amount {
                font-size: 32px;
            }
            .modal-content {
                padding: 24px 20px;
                margin: 0 16px;
            }
            .modal-header h2 {
                font-size: 22px;
            }
            .paytm-number {
                font-size: 18px;
                padding: 12px 16px;
            }
            .btn-group {
                flex-direction: column;
            }
            .btn-group .btn {
                width: 100%;
            }
            .page-header h1 {font-size: 32px;}
            .wallet-card {padding: 25px;}
            .wallet-card .balance-amount {font-size: 36px;}
            .modal-content {padding: 25px;}
            .nav-container {flex-direction: column; align-items: stretch;}
            .nav-links {flex-direction: column; width: 100%;}
            .nav-links a {text-align: center;}
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="mainhome.php" class="logo">Law Connectors</a>
            <div class="nav-links">
                <a href="mainhome.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="experts.php"><i class="fas fa-users"></i> Experts</a>
                <a href="sessions.php"><i class="fas fa-calendar"></i> Sessions</a>
                <a href="wallet.php" class="active"><i class="fas fa-wallet"></i> Wallet</a>
                <a href="lib/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1>My Wallet</h1>
            <p>Manage your funds and view transaction history</p>
        </div>

        <!-- Wallet Balance Card -->
        <div class="wallet-card">
            <div class="balance-label">Available Balance</div>
            <div class="balance-amount">₹<span id="walletBalance">0.00</span></div>
            <button class="btn" onclick="openAddFundsModal()"><i class="fas fa-plus"></i> Add Funds</button>
        </div>

        <!-- Transaction History -->
        <div class="content-box">
            <h2>Transaction History</h2>
            <div id="transactionsContainer">
                <p style="text-align: center; padding: 40px; color: #888;">Loading transactions...</p>
            </div>
        </div>
    </div>

    <!-- Add Funds Modal -->
    <div id="addFundsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add Funds</h2>
                <p>Recharge your wallet using Paytm</p>
            </div>

            <div id="step1" class="payment-step-container">
                <form id="addFundsForm">
                    <div class="form-group">
                        <label>Amount (₹)</label>
                        <input type="number" id="amount" min="100" max="50000" step="0.01" required placeholder="Enter amount (Min: ₹100)">
                    </div>
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary" style="flex: 1;"><i class="fas fa-arrow-right"></i> Continue</button>
                        <button type="button" class="btn btn-secondary" onclick="closeAddFundsModal()">Cancel</button>
                    </div>
                </form>
            </div>

            <div id="step2" class="payment-step-container" style="display: none;">
                <div class="payment-info">
                    <div class="payment-step">
                        <span class="payment-step-number">1</span>
                        <span class="payment-step-text">Send payment to the Paytm number below</span>
                    </div>
                    <div class="paytm-number">
                        <small>PAYTM NUMBER</small>
                        <div id="paytmNumber">7206959166</div>
                    </div>
                    <div class="payment-step">
                        <span class="payment-step-number">2</span>
                        <span class="payment-step-text">Note your payment reference: <strong id="paymentRef"></strong></span>
                    </div>
                    <div class="payment-step">
                        <span class="payment-step-number">3</span>
                        <span class="payment-step-text">Enter your transaction ID and upload screenshot below</span>
                    </div>
                </div>

                <form id="verifyPaymentForm">
                    <input type="hidden" id="paymentId" value="">
                    <div class="form-group">
                        <label>Transaction ID / UTR Number</label>
                        <input type="text" id="transactionId" required placeholder="Enter transaction ID from Paytm">
                    </div>
                    <div class="form-group">
                        <label>Payment Screenshot</label>
                        <input type="file" id="paymentScreenshot" accept="image/*" required>
                        <small style="color: #888; display: block; margin-top: 5px;">Upload a clear screenshot of your payment</small>
                    </div>
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary" style="flex: 1;"><i class="fas fa-check"></i> Submit for Verification</button>
                        <button type="button" class="btn btn-secondary" onclick="closeAddFundsModal()">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentPaymentData = null;

        async function loadWalletBalance() {
            try {
                const response = await fetch('lib/student/get_wallet_balance.php');
                const result = await response.json();
                
                if (result.data) {
                    document.getElementById('walletBalance').textContent = parseFloat(result.data.balance).toFixed(2);
                }
            } catch (error) {
                console.error('Error loading balance:', error);
            }
        }

        async function loadTransactions() {
            try {
                const response = await fetch('lib/student/get_wallet_transactions.php');
                const result = await response.json();

                if (result.error) throw new Error(result.error);

                displayTransactions(result.data || []);
            } catch (error) {
                document.getElementById('transactionsContainer').innerHTML = '<p style="text-align: center; color: #888; padding: 40px;">Error loading transactions</p>';
            }
        }

        function displayTransactions(transactions) {
            const container = document.getElementById('transactionsContainer');
            
            if (transactions.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #888; padding: 40px;">No transactions yet</p>';
                return;
            }

            let html = '';
            transactions.forEach(t => {
                const isCredit = t.transaction_type === 'credit' || t.transaction_type === 'refund';
                const icon = isCredit ? '<i class="fas fa-arrow-down"></i>' : '<i class="fas fa-arrow-up"></i>';
                const iconClass = isCredit ? 'credit-icon' : 'debit-icon';
                const amountClass = isCredit ? 'credit-amount' : 'debit-amount';
                const sign = isCredit ? '+' : '-';
                const date = new Date(t.created_at).toLocaleString('en-IN', { 
                    day: 'numeric', 
                    month: 'short', 
                    year: 'numeric', 
                    hour: '2-digit', 
                    minute: '2-digit' 
                });

                html += `
                    <div class="transaction-item">
                        <div class="transaction-info">
                            <div class="transaction-icon ${iconClass}">${icon}</div>
                            <div class="transaction-details">
                                <div class="transaction-title">${t.description}</div>
                                <div class="transaction-date">${date}</div>
                            </div>
                        </div>
                        <div class="transaction-amount ${amountClass}">${sign}₹${parseFloat(t.amount).toFixed(2)}</div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        function openAddFundsModal() {
            document.getElementById('addFundsModal').classList.add('active');
            document.getElementById('step1').style.display = 'block';
            document.getElementById('step2').style.display = 'none';
            document.getElementById('addFundsForm').reset();
        }

        function closeAddFundsModal() {
            document.getElementById('addFundsModal').classList.remove('active');
            currentPaymentData = null;
        }

        document.getElementById('addFundsForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const amount = parseFloat(document.getElementById('amount').value);

            if (amount < 100) {
                alert('Minimum amount is ₹100');
                return;
            }

            if (amount > 50000) {
                alert('Maximum amount is ₹50,000');
                return;
            }

            try {
                const response = await fetch('lib/student/add_wallet_funds.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({amount: amount})
                });

                const result = await response.json();

                if (result.error) {
                    alert('Error: ' + result.error);
                } else {
                    currentPaymentData = result.data;
                    document.getElementById('paymentId').value = result.data.payment_id;
                    document.getElementById('paymentRef').textContent = result.data.payment_reference;
                    document.getElementById('paytmNumber').textContent = result.data.paytm_number;
                    
                    document.getElementById('step1').style.display = 'none';
                    document.getElementById('step2').style.display = 'block';
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
                console.error(error);
            }
        });

        document.getElementById('verifyPaymentForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const paymentId = document.getElementById('paymentId').value;
            const transactionId = document.getElementById('transactionId').value;
            const screenshotFile = document.getElementById('paymentScreenshot').files[0];

            if (!screenshotFile) {
                alert('Please upload payment screenshot');
                return;
            }

            // Convert screenshot to base64
            const reader = new FileReader();
            reader.onload = async function(event) {
                const screenshot = event.target.result;

                try {
                    const response = await fetch('lib/payment/verify_payment.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            payment_id: paymentId,
                            transaction_id: transactionId,
                            screenshot: screenshot
                        })
                    });

                    const result = await response.json();

                    if (result.error) {
                        alert('Error: ' + result.error);
                    } else {
                        alert('Payment submitted successfully! Your wallet will be credited after admin verification.');
                        closeAddFundsModal();
                        loadWalletBalance();
                        loadTransactions();
                    }
                } catch (error) {
                    alert('An error occurred. Please try again.');
                    console.error(error);
                }
            };

            reader.readAsDataURL(screenshotFile);
        });

        window.addEventListener('load', () => {
            loadWalletBalance();
            loadTransactions();
        });

        // Close modal when clicking outside
        document.getElementById('addFundsModal').addEventListener('click', (e) => {
            if (e.target.id === 'addFundsModal') {
                closeAddFundsModal();
            }
        });
    </script>

    <?php include 'includes/bottom_nav.php'; ?>
</body>
</html>
