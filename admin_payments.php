<?php require_once 'lib/db.php'; 
$user = requireAuth(); 
if ($user['role'] !== 'admin') {
    header('Location: mainhome.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Verification - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'Inter', sans-serif; background: #fff; color: #0a0a0a;}
        
        .navbar {background: #0a0a0a; padding: 15px 0; position: sticky; top: 0; z-index: 100;}
        .nav-container {max-width: 1400px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center;}
        .logo {font-family: 'Playfair Display', serif; font-size: 24px; color: #fff; font-weight: 700; text-decoration: none;}
        .nav-links {display: flex; gap: 0; align-items: center;}
        .nav-links a {color: #fff; text-decoration: none; padding: 10px 20px; font-size: 14px; font-weight: 500; transition: all 0.2s; border-radius: 2px;}
        .nav-links a:hover {background: #1a1a1a;}
        .nav-links a.active {background: #fff; color: #0a0a0a;}
        
        .container {max-width: 1400px; margin: 0 auto; padding: 40px 20px;}
        
        .page-header {margin-bottom: 40px;}
        .page-header h1 {font-family: 'Playfair Display', serif; font-size: 42px; color: #0a0a0a; margin-bottom: 10px;}
        .page-header p {color: #888; font-size: 16px;}
        
        .stats-grid {display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;}
        .stat-card {background: #fafafa; padding: 25px; border-radius: 4px; border: 1px solid #e8e8e4;}
        .stat-card .stat-label {font-size: 14px; color: #888; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;}
        .stat-card .stat-value {font-size: 32px; font-weight: 700; color: #0a0a0a;}
        
        .payments-table {background: #fafafa; border-radius: 4px; overflow: hidden; border: 1px solid #e8e8e4;}
        .table-header {background: #0a0a0a; color: #fff; padding: 20px; font-weight: 600;}
        
        .payment-row {padding: 20px; border-bottom: 1px solid #e8e8e4; display: grid; grid-template-columns: 1fr 2fr 1fr 1fr 1fr 150px; gap: 15px; align-items: center;}
        .payment-row:last-child {border-bottom: none;}
        .payment-row:hover {background: #f5f5f5;}
        
        .user-info .name {font-weight: 600; color: #0a0a0a; margin-bottom: 4px;}
        .user-info .email {font-size: 13px; color: #888;}
        
        .payment-details .amount {font-weight: 700; color: #0a0a0a; margin-bottom: 4px;}
        .payment-details .ref {font-size: 13px; color: #888;}
        .payment-details .time {font-size: 12px; color: #888;}
        
        .status-badge {display: inline-block; padding: 4px 12px; border-radius: 2px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;}
        .status-pending {background: #fff3cd; color: #856404;}
        .status-completed {background: #cce5ff; color: #004085;}
        .status-verified {background: #d4edda; color: #155724;}
        .status-failed {background: #f8d7da; color: #721c24;}
        
        .btn {padding: 8px 16px; border: none; border-radius: 2px; cursor: pointer; font-size: 13px; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px;}
        .btn-approve {background: #0a0a0a; color: #fff;}
        .btn-approve:hover {background: #1a1a1a;}
        .btn-reject {background: #f5f5f3; color: #0a0a0a;}
        .btn-reject:hover {background: #e8e8e4;}
        .btn-view {background: #fafafa; color: #0a0a0a; border: 1px solid #e8e8e4;}
        .btn-view:hover {background: #f5f5f5;}
        
        .action-buttons {display: flex; gap: 8px; flex-direction: column;}
        
        .modal {display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; padding: 20px;}
        .modal.active {display: flex;}
        .modal-content {background: #fff; border-radius: 4px; padding: 40px; max-width: 800px; width: 100%; max-height: 90vh; overflow-y: auto;}
        .modal-header {margin-bottom: 30px;}
        .modal-header h2 {font-family: 'Playfair Display', serif; font-size: 28px; color: #0a0a0a; margin-bottom: 10px;}
        
        .detail-grid {display: grid; grid-template-columns: 150px 1fr; gap: 15px; margin-bottom: 25px;}
        .detail-label {font-weight: 600; color: #888; font-size: 14px;}
        .detail-value {color: #0a0a0a;}
        
        .screenshot-preview {max-width: 100%; border-radius: 4px; margin: 20px 0; border: 1px solid #e8e8e4;}
        
        .form-group {margin-bottom: 20px;}
        .form-group label {display: block; margin-bottom: 8px; font-weight: 600; color: #0a0a0a; font-size: 14px;}
        .form-group textarea {width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 2px; font-family: 'Inter', sans-serif; font-size: 14px; min-height: 100px;}
        
        .btn-group {display: flex; gap: 10px; margin-top: 25px;}
        
        @media (max-width: 1200px) {
            .payment-row {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            .action-buttons {
                flex-direction: row;
            }
        }
        
        @media (max-width: 768px) {
            .navbar {
                padding: 12px 16px;
            }
            .logo {
                font-size: 16px;
            }
            .nav-links {
                flex-wrap: wrap;
                gap: 8px;
            }
            .nav-links a {
                font-size: 12px;
                padding: 6px 10px;
            }
            .container {
                padding: 20px 16px;
            }
            .page-header h1 {
                font-size: 24px;
            }
            .page-header p {
                font-size: 13px;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            .stat-card {
                padding: 16px;
            }
            .stat-value {
                font-size: 24px;
            }
            .stat-label {
                font-size: 11px;
            }
            .section-card {
                padding: 20px 16px;
            }
            .section-card h2 {
                font-size: 18px;
            }
            .filter-tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .filter-tab {
                font-size: 13px;
                padding: 8px 16px;
                white-space: nowrap;
            }
            .payment-row {
                padding: 16px;
            }
            .payment-info h4 {
                font-size: 14px;
            }
            .payment-detail {
                font-size: 12px;
            }
            .action-buttons {
                flex-direction: column;
                gap: 8px;
            }
            .btn {
                font-size: 12px;
                padding: 8px 12px;
            }
            .modal-content {
                padding: 20px 16px;
                margin: 0 16px;
            }
            .modal-header h3 {
                font-size: 20px;
            }
            .info-group label {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="admin.php" class="logo">Law Connectors Admin</a>
            <div class="nav-links">
                <a href="admin.php"><i class="fas fa-dashboard"></i> Dashboard</a>
                <a href="admin_payments.php" class="active"><i class="fas fa-money-bill"></i> Payments</a>
                <a href="mainhome.php"><i class="fas fa-home"></i> Main Site</a>
                <a href="lib/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Payment Verification</h1>
            <p>Review and approve pending payment transactions</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Pending Review</div>
                <div class="stat-value" id="pendingCount">0</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Awaiting Screenshot</div>
                <div class="stat-value" id="awaitingCount">0</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Verified Today</div>
                <div class="stat-value" id="verifiedCount">0</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Amount Pending</div>
                <div class="stat-value" id="pendingAmount">₹0</div>
            </div>
        </div>

        <div class="payments-table">
            <div class="table-header">
                <i class="fas fa-receipt"></i> Pending Payments
            </div>
            <div id="paymentsContainer" style="padding: 40px; text-align: center; color: #888;">
                Loading payments...
            </div>
        </div>
    </div>

    <!-- Payment Details Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Payment Details</h2>
            </div>
            <div id="paymentDetailsContainer"></div>
        </div>
    </div>

    <script>
        let payments = [];

        async function loadPayments() {
            try {
                const response = await fetch('lib/admin/get_pending_payments.php');
                const result = await response.json();

                if (result.error) throw new Error(result.error);

                payments = result.data || [];
                updateStats();
                displayPayments();
            } catch (error) {
                console.error('Error loading payments:', error);
                document.getElementById('paymentsContainer').innerHTML = 
                    '<p style="color: #888;">Error loading payments</p>';
            }
        }

        function updateStats() {
            const pending = payments.filter(p => p.status === 'completed');
            const awaiting = payments.filter(p => p.status === 'pending');
            const today = new Date().toDateString();
            const verified = payments.filter(p => 
                p.status === 'verified' && 
                new Date(p.verified_at).toDateString() === today
            );
            
            const totalPending = pending.reduce((sum, p) => sum + parseFloat(p.amount), 0);

            document.getElementById('pendingCount').textContent = pending.length;
            document.getElementById('awaitingCount').textContent = awaiting.length;
            document.getElementById('verifiedCount').textContent = verified.length;
            document.getElementById('pendingAmount').textContent = '₹' + totalPending.toFixed(2);
        }

        function displayPayments() {
            const container = document.getElementById('paymentsContainer');

            if (payments.length === 0) {
                container.innerHTML = '<p style="padding: 40px; text-align: center; color: #888;">No pending payments</p>';
                return;
            }

            let html = '';
            payments.forEach(payment => {
                const statusClass = `status-${payment.status}`;
                const date = new Date(payment.created_at).toLocaleString('en-IN', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                html += `
                    <div class="payment-row">
                        <div class="user-info">
                            <div class="name">${payment.full_name}</div>
                            <div class="email">${payment.email}</div>
                        </div>
                        <div class="payment-details">
                            <div class="amount">₹${parseFloat(payment.amount).toFixed(2)}</div>
                            <div class="ref">Ref: ${payment.payment_reference}</div>
                            ${payment.transaction_id ? `<div class="ref">TXN: ${payment.transaction_id}</div>` : ''}
                            <div class="time">${date}</div>
                        </div>
                        <div>${payment.payment_method.toUpperCase()}</div>
                        <div><span class="status-badge ${statusClass}">${payment.status}</span></div>
                        <div>${payment.verified_by_name || '-'}</div>
                        <div class="action-buttons">
                            <button class="btn btn-view" onclick="viewPayment(${payment.id})">
                                <i class="fas fa-eye"></i> View
                            </button>
                            ${payment.status === 'completed' ? `
                                <button class="btn btn-approve" onclick="approvePayment(${payment.id})">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            ` : ''}
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        function viewPayment(paymentId) {
            const payment = payments.find(p => p.id === paymentId);
            if (!payment) return;

            const date = new Date(payment.created_at).toLocaleString('en-IN');

            let html = `
                <div class="detail-grid">
                    <div class="detail-label">User</div>
                    <div class="detail-value">${payment.full_name} (${payment.email})</div>
                    
                    <div class="detail-label">Amount</div>
                    <div class="detail-value">₹${parseFloat(payment.amount).toFixed(2)}</div>
                    
                    <div class="detail-label">Reference</div>
                    <div class="detail-value">${payment.payment_reference}</div>
                    
                    <div class="detail-label">Transaction ID</div>
                    <div class="detail-value">${payment.transaction_id || 'Not provided'}</div>
                    
                    <div class="detail-label">Status</div>
                    <div class="detail-value"><span class="status-badge status-${payment.status}">${payment.status}</span></div>
                    
                    <div class="detail-label">Date</div>
                    <div class="detail-value">${date}</div>
                </div>
            `;

            if (payment.screenshot) {
                html += `
                    <div>
                        <div class="detail-label" style="margin-bottom: 10px;">Payment Screenshot</div>
                        <img src="${payment.screenshot}" class="screenshot-preview" alt="Payment Screenshot">
                    </div>
                `;
            }

            if (payment.status === 'completed') {
                html += `
                    <form id="verifyForm">
                        <div class="form-group">
                            <label>Admin Notes (Optional)</label>
                            <textarea id="adminNotes" placeholder="Add any notes about this payment..."></textarea>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-approve" onclick="processPayment(${payment.id}, 'approve')" style="flex: 1;">
                                <i class="fas fa-check"></i> Approve Payment
                            </button>
                            <button type="button" class="btn btn-reject" onclick="processPayment(${payment.id}, 'reject')">
                                <i class="fas fa-times"></i> Reject
                            </button>
                            <button type="button" class="btn btn-view" onclick="closeModal()">Close</button>
                        </div>
                    </form>
                `;
            } else {
                html += `
                    <div class="btn-group">
                        <button type="button" class="btn btn-view" onclick="closeModal()" style="flex: 1;">Close</button>
                    </div>
                `;
            }

            document.getElementById('paymentDetailsContainer').innerHTML = html;
            document.getElementById('paymentModal').classList.add('active');
        }

        async function approvePayment(paymentId) {
            viewPayment(paymentId);
        }

        async function processPayment(paymentId, action) {
            const notes = document.getElementById('adminNotes')?.value || '';

            if (action === 'approve' && !confirm('Approve this payment and credit wallet?')) {
                return;
            }

            if (action === 'reject' && !confirm('Reject this payment? This cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch('lib/admin/verify_payment_admin.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        payment_id: paymentId,
                        action: action,
                        admin_notes: notes
                    })
                });

                const result = await response.json();

                if (result.error) {
                    alert('Error: ' + result.error);
                } else {
                    alert(result.message);
                    closeModal();
                    loadPayments();
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
                console.error(error);
            }
        }

        function closeModal() {
            document.getElementById('paymentModal').classList.remove('active');
        }

        document.getElementById('paymentModal').addEventListener('click', (e) => {
            if (e.target.id === 'paymentModal') {
                closeModal();
            }
        });

        window.addEventListener('load', loadPayments);
        
        // Auto-refresh every 30 seconds
        setInterval(loadPayments, 30000);
    </script>
</body>
</html>
