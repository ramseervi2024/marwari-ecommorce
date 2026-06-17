<?php
/**
 * GST Billing & Accounting ERP Dashboard View Template
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GST Billing & Accounting ERP - Dashboard</title>
    <!-- Prevent flash of unauthenticated screens and set light theme default -->
    <script>
        (function() {
            var token = localStorage.getItem('ac_auth_token');
            var user = localStorage.getItem('ac_current_user');
            if (token && user) {
                document.write('<style>#authSection { display: none !important; } #appSection { display: flex !important; }</style>');
            }
            // Sync dark mode style before CSS loads
            var isDark = localStorage.getItem('ac_dark_mode') === 'true';
            if (isDark) {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-body: #f8fafc;
            --bg-card: rgba(255, 255, 255, 0.85);
            --border-glass: rgba(0, 0, 0, 0.08);
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            
            --color-brand: #0284c7;
            --color-brand-hover: #0369a1;
            
            --accent-success: #16a34a;
            --accent-danger: #dc2626;
            --accent-warning: #ea580c;
            
            --sidebar-width: 260px;
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-premium: 0 10px 30px -10px rgba(0,0,0,0.08);
        }

        html.dark-mode {
            --bg-body: #0b0f19;
            --bg-card: rgba(17, 24, 39, 0.85);
            --border-glass: rgba(255, 255, 255, 0.08);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --shadow-premium: 0 10px 30px -10px rgba(0,0,0,0.5);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: background-color 0.3s ease;
        }

        /* Ambient background decorations */
        .ambient-glow {
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(2, 132, 199, 0.05) 0%, rgba(2, 132, 199, 0) 70%);
            top: -100px;
            right: -100px;
            z-index: -1;
            pointer-events: none;
        }

        /* 1. AUTHENTICATION SECTION */
        .auth-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
        }

        .auth-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 20px;
            padding: 35px;
            width: 100%;
            max-width: 440px;
            box-shadow: var(--shadow-premium);
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .auth-logo h2 {
            font-size: 24px;
            font-weight: 700;
            color: var(--color-brand);
        }

        .auth-logo p {
            color: var(--text-secondary);
            font-size: 13px;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            background: rgba(0, 0, 0, 0.02);
            border: 1px solid var(--border-glass);
            border-radius: 8px;
            padding: 10px 14px;
            color: var(--text-primary);
            font-family: inherit;
            font-size: 14px;
            outline: none;
            transition: var(--transition-smooth);
        }

        .dark-mode .form-input, .dark-mode .form-select, .dark-mode .form-textarea {
            background: rgba(255, 255, 255, 0.02);
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--color-brand);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: var(--transition-smooth);
            font-family: inherit;
        }

        .btn-primary {
            background: var(--color-brand);
            color: #ffffff;
        }

        .btn-primary:hover {
            background: var(--color-brand-hover);
        }

        .btn-secondary {
            background: rgba(0, 0, 0, 0.05);
            color: var(--text-primary);
            border: 1px solid var(--border-glass);
        }

        .dark-mode .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
        }

        .btn-secondary:hover {
            background: rgba(0, 0, 0, 0.1);
        }

        .btn-full {
            width: 100%;
        }

        .auth-toggle {
            text-align: center;
            margin-top: 15px;
            font-size: 13px;
            color: var(--text-secondary);
        }

        .auth-toggle span {
            color: var(--color-brand);
            cursor: pointer;
            font-weight: 500;
        }

        /* 2. APP CONTAINER & LAYOUT */
        #appSection {
            display: none;
            min-height: 100vh;
            flex-direction: row;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-card);
            border-right: 1px solid var(--border-glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 10;
        }

        .sidebar-logo {
            padding: 0 12px;
            margin-bottom: 28px;
        }

        .sidebar-logo h1 {
            font-size: 18px;
            font-weight: 700;
            color: var(--color-brand);
        }

        .sidebar-menu {
            list-style: none;
            overflow-y: auto;
            flex: 1;
        }

        .sidebar-item {
            margin-bottom: 5px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition-smooth);
        }

        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(2, 132, 199, 0.1);
            color: var(--color-brand);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 30px;
            display: flex;
            flex-direction: column;
            min-width: 0; /* Prevents flex items from breaking layout */
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header-title h2 {
            font-size: 22px;
            font-weight: 700;
        }

        .header-title p {
            color: var(--text-secondary);
            font-size: 13px;
            margin-top: 3px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 8px;
            padding: 6px 12px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--color-brand);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        .user-info {
            font-size: 13px;
        }

        .user-name {
            font-weight: 600;
        }

        .user-role {
            color: var(--text-secondary);
            font-size: 11px;
        }

        /* 3. KPI STATS CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--shadow-premium);
            transition: var(--transition-smooth);
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .stat-title {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .stat-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(2, 132, 199, 0.1);
            color: var(--color-brand);
        }

        .stat-value {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        /* 4. DATA TABLES */
        .content-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--shadow-premium);
            margin-bottom: 25px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-title h3 {
            font-size: 16px;
            font-weight: 600;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .table th {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-glass);
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 13px;
        }

        .table td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-glass);
            color: var(--text-primary);
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 50px;
        }

        .badge-success { background: rgba(22, 163, 74, 0.1); color: var(--accent-success); }
        .badge-danger { background: rgba(220, 38, 38, 0.1); color: var(--accent-danger); }
        .badge-warning { background: rgba(234, 88, 12, 0.1); color: var(--accent-warning); }

        /* 5. POPUP MODALS */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 20px;
        }

        .modal-content {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            padding: 30px;
            position: relative;
            box-shadow: var(--shadow-premium);
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 700;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text-secondary);
            cursor: pointer;
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 10px;
            padding: 12px 20px;
            box-shadow: var(--shadow-premium);
            display: none;
            align-items: center;
            gap: 10px;
            z-index: 200;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateY(100px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Form dynamic lists (for invoices/bills items) */
        .invoice-items-table {
            width: 100%;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .invoice-items-table th, .invoice-items-table td {
            padding: 5px;
        }

        .invoice-items-table select, .invoice-items-table input {
            width: 100%;
            padding: 6px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="ambient-glow"></div>

    <!-- 1. AUTH SECTION -->
    <section id="authSection" class="auth-container">
        <!-- SIGN IN CARD -->
        <div class="auth-card" id="loginCard">
            <div class="auth-logo">
                <h2>GST Accounting ERP</h2>
                <p>Sign in to your client account panel</p>
            </div>
            <form id="loginForm">
                <div class="form-group">
                    <label>Username or Email</label>
                    <input type="text" id="loginUsername" class="form-input" required placeholder="e.g. asuperadmin">
                </div>
                <div class="form-group">
                    <label>Password (or leave empty for OTP)</label>
                    <input type="password" id="loginPassword" class="form-input" placeholder="••••••••">
                </div>
                <div class="form-group" style="display: none;" id="loginOtpGroup">
                    <label>Verification OTP Code</label>
                    <input type="text" id="loginOtp" class="form-input" placeholder="6-digit code">
                </div>
                <button type="submit" class="btn btn-primary btn-full" id="loginSubmitBtn">Sign In</button>
                <div class="auth-toggle">
                    Don't have an account? <span onclick="toggleAuthCards('register')">Register</span>
                </div>
            </form>
        </div>

        <!-- SIGN UP CARD -->
        <div class="auth-card" id="registerCard" style="display: none;">
            <div class="auth-logo">
                <h2>Create Account</h2>
                <p>Register for Accounting Portal</p>
            </div>
            <form id="registerForm">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" id="regUsername" class="form-input" required placeholder="e.g. jsmith">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="regEmail" class="form-input" required placeholder="name@company.com">
                </div>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" id="regName" class="form-input" required placeholder="John Smith">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select id="regRole" class="form-select">
                        <option value="accounting_accountant">Accountant</option>
                        <option value="accounting_sales_executive">Sales Executive</option>
                        <option value="accounting_purchase_manager">Purchase Manager</option>
                        <option value="accounting_auditor">Auditor</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Send OTP Code</button>
                <div class="auth-toggle">
                    Already registered? <span onclick="toggleAuthCards('login')">Sign In</span>
                </div>
            </form>
        </div>

        <!-- REGISTRATION VERIFICATION CARD -->
        <div class="auth-card" id="verifyCard" style="display: none;">
            <div class="auth-logo">
                <h2>Verify Email</h2>
                <p>We've sent a 6-digit verification code</p>
            </div>
            <form id="verifyForm">
                <div class="form-group">
                    <label>OTP Code</label>
                    <input type="text" id="verifyOtp" class="form-input" required placeholder="123456">
                </div>
                <button type="submit" class="btn btn-primary btn-full">Complete Registration</button>
            </form>
        </div>
    </section>

    <!-- 2. MAIN APPLICATION SECTION -->
    <section id="appSection">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <h1>Accounting ERP</h1>
            </div>
            <ul class="sidebar-menu">
                <li class="sidebar-item">
                    <a class="sidebar-link active" data-tab="overview">
                        <span>Dashboard Overview</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="sales">
                        <span>Sales Invoices</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="purchases">
                        <span>Purchase Bills</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="expenses">
                        <span>Expenses</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="double-entry">
                        <span>Double Entry Ledger</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="gstr">
                        <span>GSTR Returns</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="einvoices">
                        <span>E-Invoices & Bills</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="inventory">
                        <span>Inventory Levels</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="payments">
                        <span>Payments & Cash</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="customers">
                        <span>Customers & Vendors</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="settings">
                        <span>SMTP Settings</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- MAIN DASHBOARD CONTENT AREA -->
        <main class="main-content">
            <!-- HEADER -->
            <header class="header">
                <div class="header-title">
                    <h2 id="currentTabTitle">Dashboard Overview</h2>
                    <p id="currentTabSubtitle">Monitor financial metrics, sales, and GST summaries</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="toggleTheme()" id="themeToggleBtn">Dark Mode</button>
                    <div class="user-profile">
                        <div class="user-avatar" id="avatarInitial">A</div>
                        <div class="user-info">
                            <div class="user-name" id="profileName">User Name</div>
                            <div class="user-role" id="profileRole">Role</div>
                        </div>
                    </div>
                    <button class="btn btn-secondary" onclick="logout()">Logout</button>
                </div>
            </header>

            <!-- TABS PANELS -->

            <!-- OVERVIEW TAB -->
            <div id="tab-overview" class="tab-panel">
                <div class="stats-grid" id="dashboardStats">
                    <!-- KPI values loaded via REST -->
                </div>
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Key Financial Analytics</h3>
                        </div>
                    </div>
                    <p style="color: var(--text-secondary); font-size: 14px;">Live sales trends, purchase values, and profit parameters computed dynamically.</p>
                </div>
            </div>

            <!-- SALES INVOICES TAB -->
            <div id="tab-sales" class="tab-panel" style="display: none;">
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Sales Invoices Register</h3>
                        </div>
                        <button class="btn btn-primary" onclick="openModal('salesModal')">Add Sales Invoice</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="salesTable">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Total Amount</th>
                                    <th>Tax Component</th>
                                    <th>Payment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- PURCHASE BILLS TAB -->
            <div id="tab-purchases" class="tab-panel" style="display: none;">
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Purchase Bills / Goods Receipts</h3>
                        </div>
                        <button class="btn btn-primary" onclick="openModal('purchaseModal')">Add Purchase Bill</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="purchaseTable">
                            <thead>
                                <tr>
                                    <th>Bill #</th>
                                    <th>Vendor</th>
                                    <th>Date</th>
                                    <th>Total Amount</th>
                                    <th>GST Paid</th>
                                    <th>Payment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- EXPENSES TAB -->
            <div id="tab-expenses" class="tab-panel" style="display: none;">
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Recorded Expense Vouchers</h3>
                        </div>
                        <button class="btn btn-primary" onclick="openModal('expenseModal')">Add Expense</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="expenseTable">
                            <thead>
                                <tr>
                                    <th>Expense Type</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- DOUBLE ENTRY LEDGER TAB -->
            <div id="tab-double-entry" class="tab-panel" style="display: none;">
                <div class="content-card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Chart of Accounts & Ledgers</h3>
                        </div>
                        <button class="btn btn-primary" onclick="openModal('journalModal')">Post Journal Entry</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="accountsTable">
                            <thead>
                                <tr>
                                    <th>Account Code</th>
                                    <th>Account Name</th>
                                    <th>Type</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>General Ledger Ledger lines</h3>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="ledgerTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Account</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Reference</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- GSTR RETURNS TAB -->
            <div id="tab-gstr" class="tab-panel" style="display: none;">
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>GSTR Return Status & Summary Reports</h3>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="gstTable">
                            <thead>
                                <tr>
                                    <th>Tax Period</th>
                                    <th>Source</th>
                                    <th>Tax Type</th>
                                    <th>Taxable Value</th>
                                    <th>GST Amount</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- E-INVOICES & WAY BILLS TAB -->
            <div id="tab-einvoices" class="tab-panel" style="display: none;">
                <div class="content-card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Registered E-Invoices</h3>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="einvoiceTable">
                            <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>IRN Number</th>
                                    <th>Ack Number</th>
                                    <th>Ack Date</th>
                                    <th>QR Code</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>E-Way Bills Registry</h3>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="ewaybillTable">
                            <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>E-Way Bill #</th>
                                    <th>Vehicle Number</th>
                                    <th>Transporter</th>
                                    <th>Distance</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- INVENTORY LEVELS TAB -->
            <div id="tab-inventory" class="tab-panel" style="display: none;">
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Inventory & Stock Quantities</h3>
                        </div>
                        <button class="btn btn-primary" onclick="openModal('inventoryModal')">Stock Adjustment</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="inventoryTable">
                            <thead>
                                <tr>
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th>Quantity Available</th>
                                    <th>Minimum Limit</th>
                                    <th>Warehouse</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- PAYMENTS TAB -->
            <div id="tab-payments" class="tab-panel" style="display: none;">
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Voucher Payments & Customer Collections</h3>
                        </div>
                        <button class="btn btn-primary" onclick="openModal('paymentModal')">Record Payment Voucher</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="paymentTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Party Name</th>
                                    <th>Mode</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- CUSTOMERS & VENDORS TAB -->
            <div id="tab-customers" class="tab-panel" style="display: none;">
                <div class="content-card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Customers List</h3>
                        </div>
                        <button class="btn btn-primary" onclick="openModal('customerModal')">Add Customer</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="customersTable">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Customer Name</th>
                                    <th>Mobile</th>
                                    <th>GSTIN</th>
                                    <th>Outstanding</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Vendors List</h3>
                        </div>
                        <button class="btn btn-primary" onclick="openModal('vendorModal')">Add Vendor</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="vendorsTable">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Vendor Name</th>
                                    <th>Mobile</th>
                                    <th>GSTIN</th>
                                    <th>Outstanding</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SETTINGS / SMTP TAB -->
            <div id="tab-settings" class="tab-panel" style="display: none;">
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>SMTP Verification Mailer Settings</h3>
                        </div>
                    </div>
                    <form id="smtpSettingsForm">
                        <div class="form-group">
                            <label>Enable Custom SMTP</label>
                            <select id="smtpEnabled" class="form-select">
                                <option value="no">No (Use default PHP Mail)</option>
                                <option value="yes">Yes (Use SMTP Server)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>SMTP Host</label>
                            <input type="text" id="smtpHost" class="form-input" placeholder="smtp.mailtrap.io">
                        </div>
                        <div class="form-group">
                            <label>SMTP Port</label>
                            <input type="text" id="smtpPort" class="form-input" placeholder="587">
                        </div>
                        <div class="form-group">
                            <label>SMTP Username</label>
                            <input type="text" id="smtpUsername" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>SMTP Password</label>
                            <input type="password" id="smtpPassword" class="form-input" placeholder="******">
                        </div>
                        <div class="form-group">
                            <label>Encryption</label>
                            <select id="smtpEncryption" class="form-select">
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                                <option value="none">None</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save SMTP Settings</button>
                    </form>
                </div>
            </div>

        </main>
    </section>

    <!-- 3. POPUP MODALS WINDOWS -->

    <!-- CUSTOMER POPUP MODAL -->
    <div class="modal" id="customerModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Create Customer Profile</div>
                <button class="modal-close" onclick="closeModal('customerModal')">&times;</button>
            </div>
            <form id="customerForm">
                <div class="form-group">
                    <label>Customer Name *</label>
                    <input type="text" id="custName" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" id="custMobile" class="form-input">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="custEmail" class="form-input">
                </div>
                <div class="form-group">
                    <label>GSTIN Number</label>
                    <input type="text" id="custGst" class="form-input" placeholder="27AAACA1234B1Z0">
                </div>
                <div class="form-group">
                    <label>State</label>
                    <input type="text" id="custState" class="form-input" placeholder="Maharashtra">
                </div>
                <div class="form-group">
                    <label>Credit Limit</label>
                    <input type="number" id="custCredit" class="form-input" value="100000">
                </div>
                <button type="submit" class="btn btn-primary btn-full">Save Customer</button>
            </form>
        </div>
    </div>

    <!-- VENDOR POPUP MODAL -->
    <div class="modal" id="vendorModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Create Vendor Profile</div>
                <button class="modal-close" onclick="closeModal('vendorModal')">&times;</button>
            </div>
            <form id="vendorForm">
                <div class="form-group">
                    <label>Vendor Name *</label>
                    <input type="text" id="vendName" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" id="vendMobile" class="form-input">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="vendEmail" class="form-input">
                </div>
                <div class="form-group">
                    <label>GSTIN Number</label>
                    <input type="text" id="vendGst" class="form-input" placeholder="27AAACT9988D1Z2">
                </div>
                <div class="form-group">
                    <label>State</label>
                    <input type="text" id="vendState" class="form-input" placeholder="Maharashtra">
                </div>
                <button type="submit" class="btn btn-primary btn-full">Save Vendor</button>
            </form>
        </div>
    </div>

    <!-- SALES INVOICE MODAL -->
    <div class="modal" id="salesModal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <div class="modal-title">Create Sales Invoice</div>
                <button class="modal-close" onclick="closeModal('salesModal')">&times;</button>
            </div>
            <form id="salesForm">
                <div class="form-group">
                    <label>Select Customer *</label>
                    <select id="salesCustomer" class="form-select" required></select>
                </div>
                <div class="form-group">
                    <label>Invoice Date</label>
                    <input type="date" id="salesDate" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Discount Value</label>
                    <input type="number" id="salesDiscount" class="form-input" value="0">
                </div>
                
                <div style="margin-bottom:15px;">
                    <label style="font-size:13px; font-weight:500; color:var(--text-secondary);">Invoice Lines</label>
                    <table class="invoice-items-table" id="salesItemsTable">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th style="width: 80px;">Qty</th>
                                <th style="width: 100px;">Price</th>
                                <th style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamic invoice items added here -->
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-secondary" onclick="addInvoiceItemRow('salesItemsTable')">Add Line Item</button>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Generate Invoice</button>
            </form>
        </div>
    </div>

    <!-- PURCHASE BILL MODAL -->
    <div class="modal" id="purchaseModal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <div class="modal-title">Record Purchase Bill</div>
                <button class="modal-close" onclick="closeModal('purchaseModal')">&times;</button>
            </div>
            <form id="purchaseForm">
                <div class="form-group">
                    <label>Select Vendor *</label>
                    <select id="purchaseVendor" class="form-select" required></select>
                </div>
                <div class="form-group">
                    <label>Bill Date</label>
                    <input type="date" id="purchaseDate" class="form-input" required>
                </div>
                
                <div style="margin-bottom:15px;">
                    <label style="font-size:13px; font-weight:500; color:var(--text-secondary);">Bill Lines</label>
                    <table class="invoice-items-table" id="purchaseItemsTable">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th style="width: 80px;">Qty</th>
                                <th style="width: 100px;">Price</th>
                                <th style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamic items added here -->
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-secondary" onclick="addInvoiceItemRow('purchaseItemsTable')">Add Line Item</button>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Save Bill</button>
            </form>
        </div>
    </div>

    <!-- EXPENSE MODAL -->
    <div class="modal" id="expenseModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Record Expense</div>
                <button class="modal-close" onclick="closeModal('expenseModal')">&times;</button>
            </div>
            <form id="expenseForm">
                <div class="form-group">
                    <label>Expense Type/Category *</label>
                    <input type="text" id="expType" class="form-input" placeholder="e.g. Office Stationery, Internet" required>
                </div>
                <div class="form-group">
                    <label>Amount (INR) *</label>
                    <input type="number" id="expAmount" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Expense Date</label>
                    <input type="date" id="expDate" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="expDesc" class="form-textarea" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Save Expense</button>
            </form>
        </div>
    </div>

    <!-- JOURNAL ENTRY MODAL -->
    <div class="modal" id="journalModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Post Journal Entry</div>
                <button class="modal-close" onclick="closeModal('journalModal')">&times;</button>
            </div>
            <form id="journalForm">
                <div class="form-group">
                    <label>Debit Account *</label>
                    <select id="journalDebit" class="form-select" required></select>
                </div>
                <div class="form-group">
                    <label>Credit Account *</label>
                    <select id="journalCredit" class="form-select" required></select>
                </div>
                <div class="form-group">
                    <label>Amount *</label>
                    <input type="number" id="journalAmount" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Transaction Date</label>
                    <input type="date" id="journalDate" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="journalDesc" class="form-textarea" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Post Journal</button>
            </form>
        </div>
    </div>

    <!-- PAYMENT MODAL -->
    <div class="modal" id="paymentModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Record Payment Voucher</div>
                <button class="modal-close" onclick="closeModal('paymentModal')">&times;</button>
            </div>
            <form id="paymentForm">
                <div class="form-group">
                    <label>Voucher Type *</label>
                    <select id="payType" class="form-select" onchange="syncPaymentEntityOptions()">
                        <option value="Collection">Collection (From Customer)</option>
                        <option value="Payment">Payment (To Vendor)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Party *</label>
                    <select id="payEntityId" class="form-select" required></select>
                </div>
                <div class="form-group">
                    <label>Amount *</label>
                    <input type="number" id="payAmount" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Payment Mode</label>
                    <select id="payMode" class="form-select">
                        <option value="Bank">Bank / HDFC A/C</option>
                        <option value="Cash">Cash Account</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Save Voucher</button>
            </form>
        </div>
    </div>

    <!-- INVENTORY ADJUSTMENT MODAL -->
    <div class="modal" id="inventoryModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Adjust Stock Levels</div>
                <button class="modal-close" onclick="closeModal('inventoryModal')">&times;</button>
            </div>
            <form id="inventoryForm">
                <div class="form-group">
                    <label>Product *</label>
                    <select id="invItemId" class="form-select" required></select>
                </div>
                <div class="form-group">
                    <label>Actual Stock Count *</label>
                    <input type="number" id="invQty" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Minimum Stock Alert Level</label>
                    <input type="number" id="invMin" class="form-input" value="10">
                </div>
                <div class="form-group">
                    <label>Warehouse</label>
                    <input type="text" id="invWarehouse" class="form-input" value="Default Warehouse">
                </div>
                <button type="submit" class="btn btn-primary btn-full">Save Adjustments</button>
            </form>
        </div>
    </div>

    <!-- TOAST NOTIFICATION BOX -->
    <div class="toast" id="toastBox">
        <span id="toastMessage">Done!</span>
    </div>

    <!-- JAVASCRIPT LOGIC CLIENT -->
    <script>
        const API_URL = window.location.origin + '/wp-json/accounting-management/v1';

        // Local cache storage lists
        let globalCustomersList = [];
        let globalVendorsList = [];
        let globalItemsList = [];
        let globalAccountsList = [];

        // Application State Load
        window.addEventListener('DOMContentLoaded', () => {
            initForms();
            const token = localStorage.getItem('ac_auth_token');
            if (token) {
                loadAuthenticatedApp();
            } else {
                toggleAuthCards('login');
            }
        });

        // 1. DYNAMIC NAVIGATION TABS WITH LOCALSTORAGE PERSISTENCE
        function initTabs() {
            const tabs = document.querySelectorAll('.sidebar-link');
            tabs.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault();
                    const target = tab.getAttribute('data-tab');
                    switchTab(target);
                });
            });

            // Restore active tab state from localStorage
            const savedTab = localStorage.getItem('ac_active_tab') || 'overview';
            switchTab(savedTab);
        }

        function switchTab(tabId) {
            // Update active states in Sidebar links
            document.querySelectorAll('.sidebar-link').forEach(link => {
                if (link.getAttribute('data-tab') === tabId) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });

            // Hide all panels
            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.style.display = 'none';
            });

            // Show target panel
            const activePanel = document.getElementById('tab-' + tabId);
            if (activePanel) {
                activePanel.style.display = 'block';
            }

            // Set Header Titles
            const titleMap = {
                overview: { title: 'Dashboard Overview', sub: 'Monitor financial metrics, sales, and GST summaries' },
                sales: { title: 'Sales Invoices', sub: 'View, generate, and record sales transactions' },
                purchases: { title: 'Purchase Bills', sub: 'Manage inward inventory supplies and vendor payments' },
                expenses: { title: 'Expenses', sub: 'Record miscellaneous expenditures' },
                'double-entry': { title: 'Double Entry Ledger', sub: 'Check Chart of Accounts and post journals' },
                gstr: { title: 'GSTR Returns', sub: 'Automated GST filing summaries' },
                einvoices: { title: 'E-Invoices & E-Way Bills', sub: 'IRN Ack registers and vehicle transit details' },
                inventory: { title: 'Inventory Levels', sub: 'Real-time warehouse stock values' },
                payments: { title: 'Payments & Collections', sub: 'Manage banking and cash adjustments' },
                customers: { title: 'Customers & Vendors', sub: 'Create and update client accounts' },
                settings: { title: 'SMTP Settings', sub: 'Configure credentials for authentication verification emails' }
            };

            const headerTitle = document.getElementById('currentTabTitle');
            const headerSub = document.getElementById('currentTabSubtitle');
            if (headerTitle && titleMap[tabId]) {
                headerTitle.textContent = titleMap[tabId].title;
                headerSub.textContent = titleMap[tabId].sub;
            }

            // Store active tab
            localStorage.setItem('ac_active_tab', tabId);

            // Fetch dynamic tab data
            loadTabData(tabId);
        }

        // Fetch Tab data helper
        function loadTabData(tabId) {
            switch(tabId) {
                case 'overview':
                    fetchDashboardData();
                    break;
                case 'sales':
                    fetchSalesInvoices();
                    break;
                case 'purchases':
                    fetchPurchaseBills();
                    break;
                case 'expenses':
                    fetchExpenses();
                    break;
                case 'double-entry':
                    fetchAccounts();
                    fetchLedger();
                    break;
                case 'gstr':
                    fetchGstSummary();
                    break;
                case 'einvoices':
                    fetchEInvoices();
                    fetchEWaybills();
                    break;
                case 'inventory':
                    fetchInventory();
                    break;
                case 'payments':
                    fetchPayments();
                    break;
                case 'customers':
                    fetchCustomers();
                    fetchVendors();
                    break;
                case 'settings':
                    fetchSmtpSettings();
                    break;
            }
        }

        // 2. THEME SWITCHING (LIGHT BY DEFAULT)
        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark-mode');
            localStorage.setItem('ac_dark_mode', isDark);
            document.getElementById('themeToggleBtn').textContent = isDark ? 'Light Mode' : 'Dark Mode';
        }

        // Apply visual button text on load
        if (localStorage.getItem('ac_dark_mode') === 'true') {
            document.getElementById('themeToggleBtn').textContent = 'Light Mode';
        }

        // 3. TOAST NOTIFICATIONS
        function showToast(msg, isError = false) {
            const toast = document.getElementById('toastBox');
            const toastMsg = document.getElementById('toastMessage');
            toastMsg.textContent = msg;
            toast.style.background = isError ? '#fee2e2' : '#dcfce7';
            toast.style.color = isError ? '#991b1b' : '#166534';
            toast.style.border = isError ? '1px solid #fca5a5' : '1px solid #86efac';
            toast.style.display = 'flex';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }

        // 4. API UTILITIES WITH AUTO TOKEN ROTATION
        function apiRequest(endpoint, method = 'GET', data = null) {
            const token = localStorage.getItem('ac_auth_token');
            const headers = {
                'Content-Type': 'application/json'
            };
            if (token) {
                headers['Authorization'] = 'Bearer ' + token;
            }

            const config = { method, headers };
            if (data) {
                config.body = JSON.stringify(data);
            }

            return fetch(API_URL + endpoint, config)
                .then(res => {
                    if (res.status === 401 && token) {
                        // Token expired or invalid, clear session and log out
                        logout();
                        throw new Error('Session expired. Please log in again.');
                    }
                    return res.json();
                });
        }

        // 5. AUTHENTICATION & LOGIN FLOW
        function toggleAuthCards(card) {
            document.getElementById('loginCard').style.display = (card === 'login') ? 'block' : 'none';
            document.getElementById('registerCard').style.display = (card === 'register') ? 'block' : 'none';
            document.getElementById('verifyCard').style.display = (card === 'verify') ? 'block' : 'none';
        }

        function initForms() {
            // SIGN IN FORM
            const loginForm = document.getElementById('loginForm');
            loginForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const username = document.getElementById('loginUsername').value;
                const password = document.getElementById('loginPassword').value;
                const otp = document.getElementById('loginOtp').value;
                const submitBtn = document.getElementById('loginSubmitBtn');

                const payload = { username };
                if (otp) {
                    payload.otp = otp;
                } else if (password) {
                    payload.password = password;
                }

                if (!password && !otp) {
                    // Send passwordless login request to send OTP
                    apiRequest('/auth/login/initiate', 'POST', { username_or_email: username })
                        .then(res => {
                            if (res.success) {
                                showToast('Verification code sent to your email.');
                                document.getElementById('loginOtpGroup').style.display = 'block';
                                submitBtn.textContent = 'Verify OTP & Log In';
                            } else {
                                showToast(res.message, true);
                            }
                        })
                        .catch(err => showToast(err.message, true));
                } else {
                    // Sign in session verify
                    apiRequest('/auth/login', 'POST', payload)
                        .then(res => {
                            if (res.success) {
                                localStorage.setItem('ac_auth_token', res.data.token);
                                localStorage.setItem('ac_current_user', JSON.stringify(res.data.user));
                                showToast('Welcome back, login successful!');
                                document.getElementById('authSection').style.display = 'none';
                                document.getElementById('appSection').style.display = 'flex';
                                loadAuthenticatedApp();
                            } else {
                                showToast(res.message, true);
                            }
                        })
                        .catch(err => showToast(err.message, true));
                }
            });

            // SIGN UP FORM
            const registerForm = document.getElementById('registerForm');
            registerForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const username = document.getElementById('regUsername').value;
                const email = document.getElementById('regEmail').value;
                const name = document.getElementById('regName').value;
                const role = document.getElementById('regRole').value;

                apiRequest('/auth/register', 'POST', { username, email, name, role })
                    .then(res => {
                        if (res.success) {
                            showToast('Verification code sent to your email.');
                            toggleAuthCards('verify');
                            // Save email in hidden context for verification form check
                            document.getElementById('verifyForm').dataset.email = email;
                        } else {
                            showToast(res.message, true);
                        }
                    })
                    .catch(err => showToast(err.message, true));
            });

            // OTP REGISTRATION COMPLETE
            const verifyForm = document.getElementById('verifyForm');
            verifyForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const email = verifyForm.dataset.email;
                const otp = document.getElementById('verifyOtp').value;

                apiRequest('/auth/register/verify', 'POST', { email, otp })
                    .then(res => {
                        if (res.success) {
                            showToast('Registration complete! Please log in.');
                            toggleAuthCards('login');
                        } else {
                            showToast(res.message, true);
                        }
                    })
                    .catch(err => showToast(err.message, true));
            });

            // CUSTOMER CREATE FORM
            document.getElementById('customerForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const payload = {
                    customer_name: document.getElementById('custName').value,
                    mobile: document.getElementById('custMobile').value,
                    email: document.getElementById('custEmail').value,
                    gst_number: document.getElementById('custGst').value,
                    state: document.getElementById('custState').value,
                    credit_limit: parseFloat(document.getElementById('custCredit').value || 0.00)
                };

                apiRequest('/customers', 'POST', payload)
                    .then(res => {
                        if (res.success) {
                            showToast('Customer created successfully.');
                            closeModal('customerModal');
                            fetchCustomers();
                        } else {
                            showToast(res.message, true);
                        }
                    })
                    .catch(err => showToast(err.message, true));
            });

            // VENDOR CREATE FORM
            document.getElementById('vendorForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const payload = {
                    vendor_name: document.getElementById('vendName').value,
                    mobile: document.getElementById('vendMobile').value,
                    email: document.getElementById('vendEmail').value,
                    gst_number: document.getElementById('vendGst').value,
                    state: document.getElementById('vendState').value
                };

                apiRequest('/vendors', 'POST', payload)
                    .then(res => {
                        if (res.success) {
                            showToast('Vendor created successfully.');
                            closeModal('vendorModal');
                            fetchVendors();
                        } else {
                            showToast(res.message, true);
                        }
                    })
                    .catch(err => showToast(err.message, true));
            });

            // SALES INVOICE FORM
            document.getElementById('salesForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const rows = document.querySelectorAll('#salesItemsTable tbody tr');
                const items = [];
                rows.forEach(row => {
                    items.push({
                        item_id: parseInt(row.querySelector('.line-item').value),
                        quantity: parseInt(row.querySelector('.line-qty').value),
                        price: parseFloat(row.querySelector('.line-price').value)
                    });
                });

                const payload = {
                    customer_id: parseInt(document.getElementById('salesCustomer').value),
                    invoice_date: document.getElementById('salesDate').value,
                    discount: parseFloat(document.getElementById('salesDiscount').value || 0.00),
                    items
                };

                apiRequest('/sales', 'POST', payload)
                    .then(res => {
                        if (res.success) {
                            showToast('Sales Invoice generated!');
                            closeModal('salesModal');
                            fetchSalesInvoices();
                        } else {
                            showToast(res.message, true);
                        }
                    })
                    .catch(err => showToast(err.message, true));
            });

            // PURCHASE BILL FORM
            document.getElementById('purchaseForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const rows = document.querySelectorAll('#purchaseItemsTable tbody tr');
                const items = [];
                rows.forEach(row => {
                    items.push({
                        item_id: parseInt(row.querySelector('.line-item').value),
                        quantity: parseInt(row.querySelector('.line-qty').value),
                        price: parseFloat(row.querySelector('.line-price').value)
                    });
                });

                const payload = {
                    vendor_id: parseInt(document.getElementById('purchaseVendor').value),
                    purchase_date: document.getElementById('purchaseDate').value,
                    items
                };

                apiRequest('/purchases', 'POST', payload)
                    .then(res => {
                        if (res.success) {
                            showToast('Purchase Bill recorded successfully.');
                            closeModal('purchaseModal');
                            fetchPurchaseBills();
                        } else {
                            showToast(res.message, true);
                        }
                    })
                    .catch(err => showToast(err.message, true));
            });

            // EXPENSE FORM
            document.getElementById('expenseForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const payload = {
                    expense_type: document.getElementById('expType').value,
                    amount: parseFloat(document.getElementById('expAmount').value),
                    expense_date: document.getElementById('expDate').value,
                    description: document.getElementById('expDesc').value
                };

                apiRequest('/expenses', 'POST', payload)
                    .then(res => {
                        if (res.success) {
                            showToast('Expense recorded successfully.');
                            closeModal('expenseModal');
                            fetchExpenses();
                        } else {
                            showToast(res.message, true);
                        }
                    })
                    .catch(err => showToast(err.message, true));
            });

            // JOURNAL POST FORM
            document.getElementById('journalForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const payload = {
                    debit_account: parseInt(document.getElementById('journalDebit').value),
                    credit_account: parseInt(document.getElementById('journalCredit').value),
                    amount: parseFloat(document.getElementById('journalAmount').value),
                    transaction_date: document.getElementById('journalDate').value,
                    description: document.getElementById('journalDesc').value
                };

                apiRequest('/journals', 'POST', payload)
                    .then(res => {
                        if (res.success) {
                            showToast('Journal Entry Posted!');
                            closeModal('journalModal');
                            fetchAccounts();
                            fetchLedger();
                        } else {
                            showToast(res.message, true);
                        }
                    })
                    .catch(err => showToast(err.message, true));
            });

            // PAYMENT FORM
            document.getElementById('paymentForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const type = document.getElementById('payType').value;
                const payload = {
                    payment_type: type,
                    entity_type: (type === 'Collection') ? 'Customer' : 'Vendor',
                    entity_id: parseInt(document.getElementById('payEntityId').value),
                    amount: parseFloat(document.getElementById('payAmount').value),
                    payment_mode: document.getElementById('payMode').value,
                    payment_date: new Date().toISOString().split('T')[0]
                };

                apiRequest('/payment', 'POST', payload)
                    .then(res => {
                        if (res.success) {
                            showToast('Payment voucher saved successfully.');
                            closeModal('paymentModal');
                            fetchPayments();
                        } else {
                            showToast(res.message, true);
                        }
                    })
                    .catch(err => showToast(err.message, true));
            });

            // INVENTORY ADJUSTMENT FORM
            document.getElementById('inventoryForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const payload = {
                    item_id: parseInt(document.getElementById('invItemId').value),
                    stock_quantity: parseInt(document.getElementById('invQty').value),
                    minimum_stock: parseInt(document.getElementById('invMin').value),
                    warehouse: document.getElementById('invWarehouse').value
                };

                apiRequest('/inventory/adjust', 'POST', payload)
                    .then(res => {
                        if (res.success) {
                            showToast('Inventory level synchronized successfully.');
                            closeModal('inventoryModal');
                            fetchInventory();
                        } else {
                            showToast(res.message, true);
                        }
                    })
                    .catch(err => showToast(err.message, true));
            });

            // SMTP SETTINGS FORM
            document.getElementById('smtpSettingsForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const payload = {
                    smtp_enabled: document.getElementById('smtpEnabled').value,
                    smtp_host: document.getElementById('smtpHost').value,
                    smtp_port: document.getElementById('smtpPort').value,
                    smtp_username: document.getElementById('smtpUsername').value,
                    smtp_password: document.getElementById('smtpPassword').value,
                    smtp_encryption: document.getElementById('smtpEncryption').value
                };

                apiRequest('/auth/smtp', 'POST', payload)
                    .then(res => {
                        if (res.success) {
                            showToast('SMTP Settings updated.');
                        } else {
                            showToast(res.message, true);
                        }
                    })
                    .catch(err => showToast(err.message, true));
            });
        }

        // Load details for Authenticated User Session
        function loadAuthenticatedApp() {
            initTabs();
            // Load user profile details
            const user = JSON.parse(localStorage.getItem('ac_current_user') || '{}');
            document.getElementById('profileName').textContent = user.name || user.username;
            document.getElementById('profileRole').textContent = user.role.replace('accounting_', '').toUpperCase();
            document.getElementById('avatarInitial').textContent = (user.name || user.username || 'A')[0].toUpperCase();

            // Populate all master selections
            preloadDropdownMasters();
        }

        function logout() {
            apiRequest('/auth/logout', 'POST')
                .finally(() => {
                    localStorage.removeItem('ac_auth_token');
                    localStorage.removeItem('ac_current_user');
                    showToast('Logged out successfully.');
                    document.getElementById('authSection').style.display = 'flex';
                    document.getElementById('appSection').style.display = 'none';
                    toggleAuthCards('login');
                });
        }

        // 6. PRELOAD MASTER DROPDOWNS
        function preloadDropdownMasters() {
            // Load Customers list
            apiRequest('/customers?limit=100').then(res => {
                if (res.success) {
                    globalCustomersList = res.data.data;
                    populateSelect('salesCustomer', globalCustomersList, 'id', 'customer_name');
                    syncPaymentEntityOptions();
                }
            });

            // Load Vendors list
            apiRequest('/vendors?limit=100').then(res => {
                if (res.success) {
                    globalVendorsList = res.data.data;
                    populateSelect('purchaseVendor', globalVendorsList, 'id', 'vendor_name');
                }
            });

            // Load Items list
            apiRequest('/items?limit=100').then(res => {
                if (res.success) {
                    globalItemsList = res.data.data;
                    populateSelect('invItemId', globalItemsList, 'id', 'item_name');
                }
            });

            // Load Accounts list
            apiRequest('/accounts?limit=100').then(res => {
                if (res.success) {
                    globalAccountsList = res.data.data;
                    populateSelect('journalDebit', globalAccountsList, 'id', 'account_name', (acc) => `${acc.account_code} - ${acc.account_name}`);
                    populateSelect('journalCredit', globalAccountsList, 'id', 'account_name', (acc) => `${acc.account_code} - ${acc.account_name}`);
                }
            });
        }

        function populateSelect(selectId, items, valKey, textKey, formatFn = null) {
            const select = document.getElementById(selectId);
            if (!select) return;
            select.innerHTML = '<option value="">-- Select Option --</option>';
            items.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item[valKey];
                opt.textContent = formatFn ? formatFn(item) : item[textKey];
                select.appendChild(opt);
            });
        }

        function syncPaymentEntityOptions() {
            const payType = document.getElementById('payType').value;
            const items = (payType === 'Collection') ? globalCustomersList : globalVendorsList;
            const textKey = (payType === 'Collection') ? 'customer_name' : 'vendor_name';
            populateSelect('payEntityId', items, 'id', textKey);
        }

        // 7. TAB DATA RENDER LOADS

        // DASHBOARD OVERVIEW RENDER
        function fetchDashboardData() {
            apiRequest('/dashboard')
                .then(res => {
                    if (res.success) {
                        const cards = res.data.cards;
                        const container = document.getElementById('dashboardStats');
                        container.innerHTML = `
                            <div class="stat-card">
                                <div class="stat-header">
                                    <span class="stat-title">Today's Sales</span>
                                </div>
                                <div class="stat-value">₹${parseFloat(cards.today_sales).toFixed(2)}</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-header">
                                    <span class="stat-title">Monthly Sales</span>
                                </div>
                                <div class="stat-value">₹${parseFloat(cards.monthly_sales).toFixed(2)}</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-header">
                                    <span class="stat-title">Total Purchases</span>
                                </div>
                                <div class="stat-value">₹${parseFloat(cards.total_purchases).toFixed(2)}</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-header">
                                    <span class="stat-title">Operating Expenses</span>
                                </div>
                                <div class="stat-value">₹${parseFloat(cards.total_expenses).toFixed(2)}</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-header">
                                    <span class="stat-title">GST Payable Liability</span>
                                </div>
                                <div class="stat-value" style="color: var(--accent-danger);">₹${parseFloat(cards.gst_payable).toFixed(2)}</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-header">
                                    <span class="stat-title">GST Input Tax Credit</span>
                                </div>
                                <div class="stat-value" style="color: var(--accent-success);">₹${parseFloat(cards.gst_receivable).toFixed(2)}</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-header">
                                    <span class="stat-title">Net Operating Profit</span>
                                </div>
                                <div class="stat-value" style="color: var(--accent-success);">₹${parseFloat(cards.net_profit).toFixed(2)}</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-header">
                                    <span class="stat-title">Outstanding Collections</span>
                                </div>
                                <div class="stat-value">₹${parseFloat(cards.outstanding_collections).toFixed(2)}</div>
                            </div>
                        `;
                    }
                });
        }

        // SALES INVOICES LIST
        function fetchSalesInvoices() {
            apiRequest('/sales')
                .then(res => {
                    if (res.success) {
                        const tbody = document.querySelector('#salesTable tbody');
                        tbody.innerHTML = '';
                        res.data.data.forEach(inv => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><strong>${inv.invoice_number}</strong></td>
                                <td>${inv.customer_name}</td>
                                <td>${inv.invoice_date}</td>
                                <td>₹${parseFloat(inv.total_amount).toFixed(2)}</td>
                                <td>₹${(parseFloat(inv.cgst) + parseFloat(inv.sgst) + parseFloat(inv.igst)).toFixed(2)}</td>
                                <td><span class="badge ${inv.payment_status === 'Paid' ? 'badge-success' : 'badge-warning'}">${inv.payment_status}</span></td>
                                <td>
                                    <button class="btn btn-secondary" style="padding: 4px 8px; font-size:11px;" onclick="generateEInvoice(${inv.id})">E-Invoice</button>
                                    <button class="btn btn-secondary" style="padding: 4px 8px; font-size:11px;" onclick="generateEWaybillPrompt(${inv.id})">E-Way Bill</button>
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                });
        }

        // PURCHASE BILLS LIST
        function fetchPurchaseBills() {
            apiRequest('/purchases')
                .then(res => {
                    if (res.success) {
                        const tbody = document.querySelector('#purchaseTable tbody');
                        tbody.innerHTML = '';
                        res.data.data.forEach(bill => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><strong>${bill.purchase_number}</strong></td>
                                <td>${bill.vendor_name}</td>
                                <td>${bill.purchase_date}</td>
                                <td>₹${parseFloat(bill.total_amount).toFixed(2)}</td>
                                <td>₹${(parseFloat(bill.cgst) + parseFloat(bill.sgst) + parseFloat(bill.igst)).toFixed(2)}</td>
                                <td><span class="badge ${bill.payment_status === 'Paid' ? 'badge-success' : 'badge-warning'}">${bill.payment_status}</span></td>
                                <td>
                                    <button class="btn btn-secondary" style="padding: 4px 8px; font-size:11px;" onclick="deleteBill(${bill.id})">Delete</button>
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                });
        }

        // EXPENSES RENDER
        function fetchExpenses() {
            apiRequest('/expenses')
                .then(res => {
                    if (res.success) {
                        const tbody = document.querySelector('#expenseTable tbody');
                        tbody.innerHTML = '';
                        res.data.data.forEach(exp => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${exp.expense_type}</td>
                                <td>₹${parseFloat(exp.amount).toFixed(2)}</td>
                                <td>${exp.expense_date}</td>
                                <td>${exp.description}</td>
                                <td>
                                    <button class="btn btn-secondary" style="padding: 4px 8px; font-size:11px;" onclick="deleteExpense(${exp.id})">Delete</button>
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                });
        }

        // CHART OF ACCOUNTS RENDER
        function fetchAccounts() {
            apiRequest('/accounts?limit=100')
                .then(res => {
                    if (res.success) {
                        const tbody = document.querySelector('#accountsTable tbody');
                        tbody.innerHTML = '';
                        res.data.data.forEach(acc => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><strong>${acc.account_code}</strong></td>
                                <td>${acc.account_name}</td>
                                <td>${acc.account_type}</td>
                                <td>₹${parseFloat(acc.balance).toFixed(2)}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                });
        }

        // GENERAL LEDGER ENTRIES
        function fetchLedger() {
            apiRequest('/ledger?limit=100')
                .then(res => {
                    if (res.success) {
                        const tbody = document.querySelector('#ledgerTable tbody');
                        tbody.innerHTML = '';
                        res.data.data.forEach(ent => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${ent.entry_date}</td>
                                <td><strong>${ent.account_code} - ${ent.account_name}</strong></td>
                                <td><span class="badge ${ent.transaction_type === 'DEBIT' ? 'badge-success' : 'badge-danger'}">${ent.transaction_type}</span></td>
                                <td>₹${parseFloat(ent.amount).toFixed(2)}</td>
                                <td>${ent.reference_type} #${ent.reference_id}</td>
                                <td>${ent.description}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                });
        }

        // GSTR RETURNS SUMMARY
        function fetchGstSummary() {
            apiRequest('/gst?limit=100')
                .then(res => {
                    if (res.success) {
                        const tbody = document.querySelector('#gstTable tbody');
                        tbody.innerHTML = '';
                        res.data.data.forEach(tax => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><strong>${tax.tax_period}</strong></td>
                                <td>${tax.invoice_type}</td>
                                <td>${tax.gst_type}</td>
                                <td>₹${parseFloat(tax.taxable_amount).toFixed(2)}</td>
                                <td>₹${parseFloat(tax.gst_amount).toFixed(2)}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                });
        }

        // E-INVOICES
        function fetchEInvoices() {
            apiRequest('/einvoice')
                .then(res => {
                    if (res.success) {
                        const tbody = document.querySelector('#einvoiceTable tbody');
                        tbody.innerHTML = '';
                        res.data.data.forEach(ein => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>Sales Invoice ID: #${ein.invoice_id}</td>
                                <td style="word-break: break-all; font-size:12px;"><code>${ein.irn_number}</code></td>
                                <td>${ein.ack_number}</td>
                                <td>${ein.ack_date}</td>
                                <td><img src="${ein.qr_code}" alt="QR" width="64" height="64" style="border: 1px solid var(--border-glass);"></td>
                                <td><span class="badge badge-success">${ein.status}</span></td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                });
        }

        // E-WAY BILLS
        function fetchEWaybills() {
            apiRequest('/ewaybill')
                .then(res => {
                    if (res.success) {
                        const tbody = document.querySelector('#ewaybillTable tbody');
                        tbody.innerHTML = '';
                        res.data.data.forEach(ewb => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>Sales Invoice ID: #${ewb.invoice_id}</td>
                                <td><strong>${ewb.eway_bill_number}</strong></td>
                                <td>${ewb.vehicle_number}</td>
                                <td>${ewb.transporter_name}</td>
                                <td>${ewb.distance} KM</td>
                                <td><span class="badge badge-success">${ewb.status}</span></td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                });
        }

        // INVENTORY levels
        function fetchInventory() {
            apiRequest('/inventory')
                .then(res => {
                    if (res.success) {
                        const tbody = document.querySelector('#inventoryTable tbody');
                        tbody.innerHTML = '';
                        res.data.data.forEach(inv => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${inv.item_code}</td>
                                <td>${inv.item_name}</td>
                                <td><strong>${inv.stock_quantity}</strong></td>
                                <td>${inv.minimum_stock} Alert</td>
                                <td>${inv.warehouse}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                });
        }

        // PAYMENTS register
        function fetchPayments() {
            apiRequest('/payment')
                .then(res => {
                    if (res.success) {
                        const tbody = document.querySelector('#paymentTable tbody');
                        tbody.innerHTML = '';
                        res.data.data.forEach(pay => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${pay.payment_date}</td>
                                <td><span class="badge ${pay.payment_type === 'Collection' ? 'badge-success' : 'badge-warning'}">${pay.payment_type}</span></td>
                                <td>${pay.entity_name} (${pay.entity_type})</td>
                                <td>${pay.payment_mode}</td>
                                <td>₹${parseFloat(pay.amount).toFixed(2)}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                });
        }

        // CUSTOMERS list
        function fetchCustomers() {
            apiRequest('/customers')
                .then(res => {
                    if (res.success) {
                        const tbody = document.querySelector('#customersTable tbody');
                        tbody.innerHTML = '';
                        res.data.data.forEach(cust => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><strong>${cust.customer_code}</strong></td>
                                <td>${cust.customer_name}</td>
                                <td>${cust.mobile}</td>
                                <td>${cust.gst_number || 'N/A'}</td>
                                <td>₹${parseFloat(cust.outstanding_amount).toFixed(2)}</td>
                                <td>
                                    <button class="btn btn-secondary" style="padding: 4px 8px; font-size:11px;" onclick="deleteCustomer(${cust.id})">Delete</button>
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                });
        }

        // VENDORS list
        function fetchVendors() {
            apiRequest('/vendors')
                .then(res => {
                    if (res.success) {
                        const tbody = document.querySelector('#vendorsTable tbody');
                        tbody.innerHTML = '';
                        res.data.data.forEach(vend => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><strong>${vend.vendor_code}</strong></td>
                                <td>${vend.vendor_name}</td>
                                <td>${vend.mobile}</td>
                                <td>${vend.gst_number || 'N/A'}</td>
                                <td>₹${parseFloat(vend.outstanding_amount).toFixed(2)}</td>
                                <td>
                                    <button class="btn btn-secondary" style="padding: 4px 8px; font-size:11px;" onclick="deleteVendor(${vend.id})">Delete</button>
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                });
        }

        // SMTP settings fetch
        function fetchSmtpSettings() {
            apiRequest('/auth/smtp')
                .then(res => {
                    if (res.success) {
                        document.getElementById('smtpEnabled').value = res.data.smtp_enabled;
                        document.getElementById('smtpHost').value = res.data.smtp_host;
                        document.getElementById('smtpPort').value = res.data.smtp_port;
                        document.getElementById('smtpUsername').value = res.data.smtp_username;
                        document.getElementById('smtpEncryption').value = res.data.smtp_encryption;
                    }
                });
        }

        // 8. ACTIONS AND TRIGGER BUTTONS

        function deleteBill(id) {
            if (confirm('Delete purchase bill? This will adjust inventory and revert accounting balances.')) {
                apiRequest('/purchases/' + id, 'DELETE')
                    .then(res => {
                        if (res.success) {
                            showToast('Bill deleted.');
                            fetchPurchaseBills();
                        }
                    });
            }
        }

        function deleteExpense(id) {
            if (confirm('Delete expense voucher?')) {
                apiRequest('/expenses/' + id, 'DELETE')
                    .then(res => {
                        if (res.success) {
                            showToast('Expense deleted.');
                            fetchExpenses();
                        }
                    });
            }
        }

        function deleteCustomer(id) {
            if (confirm('Delete customer profile?')) {
                apiRequest('/customers/' + id, 'DELETE')
                    .then(res => {
                        if (res.success) {
                            showToast('Customer deleted.');
                            fetchCustomers();
                        }
                    });
            }
        }

        function deleteVendor(id) {
            if (confirm('Delete vendor profile?')) {
                apiRequest('/vendors/' + id, 'DELETE')
                    .then(res => {
                        if (res.success) {
                            showToast('Vendor deleted.');
                            fetchVendors();
                        }
                    });
            }
        }

        function generateEInvoice(invoiceId) {
            apiRequest('/einvoice/generate', 'POST', { invoice_id: invoiceId })
                .then(res => {
                    if (res.success) {
                        showToast('E-Invoice generated successfully!');
                        fetchSalesInvoices();
                    } else {
                        showToast(res.message, true);
                    }
                });
        }

        function generateEWaybillPrompt(invoiceId) {
            const vehicle = prompt('Enter Vehicle Number (e.g. MH-12-PQ-1234):');
            if (!vehicle) return;
            const transporter = prompt('Enter Transporter Name:');
            if (!transporter) return;

            apiRequest('/ewaybill/generate', 'POST', {
                invoice_id: invoiceId,
                vehicle_number: vehicle,
                transporter_name: transporter,
                distance: 150
            }).then(res => {
                if (res.success) {
                    showToast('E-Way Bill registered!');
                    fetchSalesInvoices();
                } else {
                    showToast(res.message, true);
                }
            });
        }

        // 9. MODAL HANDLERS
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
            
            // Context specific setups
            if (modalId === 'salesModal') {
                document.getElementById('salesDate').value = new Date().toISOString().split('T')[0];
                document.querySelector('#salesItemsTable tbody').innerHTML = '';
                addInvoiceItemRow('salesItemsTable');
            } else if (modalId === 'purchaseModal') {
                document.getElementById('purchaseDate').value = new Date().toISOString().split('T')[0];
                document.querySelector('#purchaseItemsTable tbody').innerHTML = '';
                addInvoiceItemRow('purchaseItemsTable');
            } else if (modalId === 'expenseModal') {
                document.getElementById('expDate').value = new Date().toISOString().split('T')[0];
            } else if (modalId === 'journalModal') {
                document.getElementById('journalDate').value = new Date().toISOString().split('T')[0];
            }
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Dynamic Line Item inserts for Invoices/Bills
        function addInvoiceItemRow(tableId) {
            const tbody = document.querySelector('#' + tableId + ' tbody');
            const tr = document.createElement('tr');
            
            // Build item option list
            let options = '<option value="">-- Choose Item --</option>';
            globalItemsList.forEach(item => {
                options += `<option value="${item.id}">${item.item_name} (₹${item.selling_price})</option>`;
            });

            tr.innerHTML = `
                <td>
                    <select class="form-select line-item" required onchange="syncLinePrice(this)">
                        ${options}
                    </select>
                </td>
                <td><input type="number" class="form-input line-qty" value="1" min="1" required></td>
                <td><input type="number" class="form-input line-price" step="0.01" required></td>
                <td><button type="button" class="btn btn-secondary" style="padding: 2px 6px;" onclick="this.closest('tr').remove()">&times;</button></td>
            `;
            tbody.appendChild(tr);
        }

        function syncLinePrice(selectEl) {
            const row = selectEl.closest('tr');
            const itemId = parseInt(selectEl.value);
            const item = globalItemsList.find(i => i.id === itemId);
            if (item) {
                // Determine if parent is sales or purchase
                const tableId = selectEl.closest('table').id;
                const price = (tableId === 'salesItemsTable') ? item.selling_price : item.purchase_price;
                row.querySelector('.line-price').value = price;
            }
        }
    </script>
</body>
</html>
