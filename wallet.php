<?php require_once 'lib/db.php'; requireAuth(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet - Law Connectors</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px;}
        .container {max-width: 1200px; margin: 0 auto;}
        .header {background: white; padding: 20px 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;}
        .logo {font-size: 24px; font-weight: bold; color: #667eea; display: flex; align-items: center; gap: 10px;}
        .nav-buttons {display: flex; gap: 10px; flex-wrap: wrap;}
        .btn {padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;}
        .btn-primary {background: #667eea; color: white;}
        .btn-primary:hover {background: #5568d3;}
        .btn-secondary {background: #f0f0f0; color: #333;}
        .btn-success {background: #28a745; color: white;}
        .wallet-card {background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px; border-radius: 20px; color: white; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);}
        .balance-label {font-size: 16px; opacity: 0.9; margin-bottom: 10px;}
        .balance-amount {font-size: 48px; font-weight: bold;}
        .wallet-actions {display: flex; gap: 15px; margin-top: 25px;}
        .content-box {background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 25px;}
        .transaction-item {display: flex; justify-content: space-between; align-items: center; padding: 15px; border-bottom: 1px solid #f0f0f0;}
        .transaction-info {display: flex; align-items: center; gap: 15px;}
        .transaction-icon {width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;}
        .credit-icon {background: #d4edda; color: #28a745;}
        .debit-icon {background: #f8d7da; color: #dc3545;}
        .transaction-amount {font-size: 18px; font-weight: 600;}
        .credit-amount {color: #28a745;}
        .debit-amount {color: #dc3545;}
        .modal {display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;}
        .modal.active {display: flex;}
        .modal-content {background: white; border-radius: 15px; padding: 30px; max-width: 500px; width: 100%;}
        .form-group {margin-bottom: 20px;}
        .form-group label {display: block; margin-bottom: 8px; font-weight: 600; color: #333;}
        .form-group input {width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;}
        @media (max-width: 768px) {.wallet-card {padding: 25px;} .balance-amount {font-size: 36px;}}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><i class="fas fa-wallet"></i> Law Connectors - Wallet</div>
            <div class="nav-buttons">
                <a href="mainhome.php" class="btn btn-secondary"><i class="fas fa-home"></i> Dashboard</a>
                <a href="experts.php" class="btn btn-secondary"><i class="fas fa-users"></i> Experts</a>
                <a href="sessions.php" class="btn btn-secondary"><i class="fas fa-calendar"></i> Sessions</a>
            </div>
        </div>

        <div class="wallet-card">
            <div class="balance-label">Current Balance</div>
            <div class="balance-amount">₹<span id="walletBalance">0.00</span></div>
            <div class="wallet-actions">
                <button class="btn btn-success" onclick="openAddFundsModal()"><i class="fas fa-plus"></i> Add Funds</button>
            </div>
        </div>

        <div class="content-box">
            <h2 style="margin-bottom: 20px; color: #333;">Transaction History</h2>
            <div id="transactionsContainer">
                <p style="text-align: center; padding: 40px; color: #999;">Loading transactions...</p>
            </div>
        </div>
    </div>

    <!-- Add Funds Modal -->
    <div id="addFundsModal" class="modal">
        <div class="modal-content">
            <h2 style="margin-bottom: 20px;">Add Funds to Wallet</h2>
            <form id="addFundsForm">
                <div class="form-group">
                    <label>Amount (₹)</label>
                    <input type="number" id="amount" min="100" step="0.01" required placeholder="Enter amount">
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;"><i class="fas fa-check"></i> Add Funds</button>
                    <button type="button" class="btn btn-secondary" onclick="closeAddFundsModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        async function loadWalletBalance() {
            try {
                const response = await fetch('lib/get_wallet_balance.php');
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
                const response = await fetch('lib/get_wallet_transactions.php');
                const result = await response.json();

                if (result.error) throw new Error(result.error);

                displayTransactions(result.data || []);
            } catch (error) {
                document.getElementById('transactionsContainer').innerHTML = '<p style="text-align: center; color: #dc3545;">Error loading transactions</p>';
            }
        }

        function displayTransactions(transactions) {
            const container = document.getElementById('transactionsContainer');
            
            if (transactions.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #999;">No transactions yet</p>';
                return;
            }

            let html = '';
            transactions.forEach(t => {
                const isCredit = t.transaction_type === 'credit' || t.transaction_type === 'refund';
                const icon = isCredit ? '<i class="fas fa-arrow-down"></i>' : '<i class="fas fa-arrow-up"></i>';
                const iconClass = isCredit ? 'credit-icon' : 'debit-icon';
                const amountClass = isCredit ? 'credit-amount' : 'debit-amount';
                const sign = isCredit ? '+' : '-';
                const date = new Date(t.created_at).toLocaleString();

                html += `
                    <div class="transaction-item">
                        <div class="transaction-info">
                            <div class="transaction-icon ${iconClass}">${icon}</div>
                            <div>
                                <div style="font-weight: 600; color: #333;">${t.description}</div>
                                <div style="font-size: 13px; color: #999;">${date}</div>
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
        }

        function closeAddFundsModal() {
            document.getElementById('addFundsModal').classList.remove('active');
        }

        document.getElementById('addFundsForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const amount = document.getElementById('amount').value;

            try {
                const response = await fetch('lib/add_wallet_funds.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({amount: parseFloat(amount)})
                });

                const result = await response.json();

                if (result.error) {
                    alert('Error: ' + result.error);
                } else {
                    alert('Funds added successfully!');
                    closeAddFundsModal();
                    document.getElementById('addFundsForm').reset();
                    loadWalletBalance();
                    loadTransactions();
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        });

        window.addEventListener('load', () => {
            loadWalletBalance();
            loadTransactions();
        });
    </script>
</body>
</html>
