<?php
/**
 * Inventory Management ERP Dashboard View Template
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
    <title>Inventory Management ERP - Dashboard</title>
    <!-- Prevent flash of unauthenticated screens and set light theme default -->
    <script>
        (function() {
            var token = localStorage.getItem('inv_auth_token');
            var user = localStorage.getItem('inv_current_user');
            if (token && user) {
                document.write('<style>#authSection { display: none !important; } #appSection { display: flex !important; }</style>');
            }
            // Sync dark mode style before CSS loads
            var isDark = localStorage.getItem('inv_dark_mode') === 'true';
            if (isDark) {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-body: #f1f5f9;
            --bg-card: rgba(255, 255, 255, 0.85);
            --border-glass: rgba(0, 0, 0, 0.08);
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            
            --color-brand: #0284c7;
            --color-brand-hover: #0369a1;
            
            --accent-success: #16a34a;
            --accent-danger: #dc2626;
            --accent-warning: #ea580c;
            --accent-info: #0284c7;
            
            --sidebar-width: 260px;
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-premium: 0 10px 30px -10px rgba(0,0,0,0.08);
        }

        html.dark-mode {
            --bg-body: #090d16;
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
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient background glow decoration */
        .ambient-glow {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(2, 132, 199, 0.08) 0%, rgba(2, 132, 199, 0) 70%);
            top: -150px;
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
            z-index: 5;
        }

        .auth-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 460px;
            box-shadow: var(--shadow-premium);
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-logo h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--color-brand);
            letter-spacing: -0.5px;
        }

        .auth-logo p {
            color: var(--text-secondary);
            font-size: 13px;
            margin-top: 6px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            background: rgba(0, 0, 0, 0.02);
            border: 1px solid var(--border-glass);
            border-radius: 10px;
            padding: 11px 15px;
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
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.18);
            background: #ffffff;
        }
        
        .dark-mode .form-input:focus, .dark-mode .form-select:focus, .dark-mode .form-textarea:focus {
            background: #111827;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: var(--transition-smooth);
            font-family: inherit;
            gap: 8px;
        }

        .btn-primary {
            background: var(--color-brand);
            color: #ffffff;
        }

        .btn-primary:hover {
            background: var(--color-brand-hover);
        }

        .btn-secondary {
            background: rgba(0, 0, 0, 0.04);
            color: var(--text-primary);
            border: 1px solid var(--border-glass);
        }

        .dark-mode .btn-secondary {
            background: rgba(255, 255, 255, 0.04);
        }

        .btn-secondary:hover {
            background: rgba(0, 0, 0, 0.08);
        }

        .btn-danger {
            background: var(--accent-danger);
            color: #ffffff;
        }

        .btn-full {
            width: 100%;
        }

        .auth-toggle {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: var(--text-secondary);
        }

        .auth-toggle span {
            color: var(--color-brand);
            cursor: pointer;
            font-weight: 600;
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
            padding: 28px 18px;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 10;
            overflow-y: auto;
        }

        .sidebar-logo {
            padding: 0 12px;
            margin-bottom: 30px;
        }

        .sidebar-logo h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--color-brand);
            letter-spacing: -0.5px;
        }

        .sidebar-menu {
            list-style: none;
            flex: 1;
        }

        .sidebar-item {
            margin-bottom: 6px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 11px 15px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
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
            padding: 35px;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
            gap: 20px;
        }

        .header-title h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .header-title p {
            color: var(--text-secondary);
            font-size: 13px;
            margin-top: 4px;
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
            border-radius: 10px;
            padding: 6px 14px;
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
            font-weight: 700;
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
            font-weight: 500;
        }

        /* 3. KPI STATS CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 24px;
            box-shadow: var(--shadow-premium);
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px -10px rgba(0,0,0,0.12);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }

        .stat-title {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(2, 132, 199, 0.1);
            color: var(--color-brand);
            font-weight: 700;
        }

        .stat-value {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 700;
        }

        /* 4. DATA TABLES AND SECTIONS */
        .content-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 28px;
            box-shadow: var(--shadow-premium);
            margin-bottom: 30px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .card-title h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 700;
        }

        .card-title p {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 3px;
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
            padding: 14px 18px;
            border-bottom: 2px solid var(--border-glass);
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 14px 18px;
            border-bottom: 1px solid var(--border-glass);
            color: var(--text-primary);
            vertical-align: middle;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-success { background: rgba(22, 163, 74, 0.1); color: var(--accent-success); }
        .badge-danger { background: rgba(220, 38, 38, 0.1); color: var(--accent-danger); }
        .badge-warning { background: rgba(234, 88, 12, 0.1); color: var(--accent-warning); }
        .badge-info { background: rgba(2, 132, 199, 0.1); color: var(--accent-info); }

        /* 5. POPUP MODALS */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(5px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 20px;
        }

        .modal-content {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 24px;
            width: 100%;
            max-width: 600px;
            padding: 35px;
            position: relative;
            box-shadow: var(--shadow-premium);
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .modal-title {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 700;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            color: var(--text-secondary);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-smooth);
        }

        .modal-close:hover {
            color: var(--text-primary);
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 12px;
            padding: 14px 24px;
            box-shadow: var(--shadow-premium);
            display: none;
            align-items: center;
            gap: 12px;
            z-index: 200;
            animation: slideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            font-size: 14px;
            font-weight: 600;
        }

        @keyframes slideIn {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Dynamic items addition (Inward/Outward, PO, Transfers, Audits) */
        .dynamic-table {
            width: 100%;
            margin-top: 15px;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .dynamic-table th, .dynamic-table td {
            padding: 8px;
            text-align: left;
        }

        .dynamic-table select, .dynamic-table input {
            width: 100%;
            padding: 8px;
            font-size: 13px;
        }

        /* Grid for layouts */
        .layout-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
            gap: 25px;
        }

        .tab-panel {
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="ambient-glow"></div>

    <!-- 1. AUTHENTICATION SECTION -->
    <section id="authSection" class="auth-container">
        <!-- SIGN IN CARD -->
        <div class="auth-card" id="loginCard">
            <div class="auth-logo">
                <h2>Inventory Management ERP</h2>
                <p>Sign in to your client account panel</p>
            </div>
            <form id="loginForm">
                <div class="form-group">
                    <label>Username or Email</label>
                    <input type="text" id="loginUsername" class="form-input" required placeholder="e.g. isuperadmin">
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
                <p>Register for Inventory Portal</p>
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
                        <option value="inventory_manager">Inventory Manager</option>
                        <option value="inventory_purchase_manager">Purchase Manager</option>
                        <option value="inventory_warehouse_staff">Warehouse Staff</option>
                        <option value="inventory_auditor">Auditor</option>
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
                <h1>Inventory ERP</h1>
            </div>
            <ul class="sidebar-menu">
                <li class="sidebar-item">
                    <a class="sidebar-link active" data-tab="overview">
                        <span>Overview</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="products">
                        <span>Products</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="warehouses">
                        <span>Warehouses</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="stock">
                        <span>Stock Ledger</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="inward-outward">
                        <span>Inward / Outward</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="purchase-orders">
                        <span>Purchase Orders</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="grn">
                        <span>GRN Receipt</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="transfers">
                        <span>Warehouse Transfers</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="audits">
                        <span>Physical Audits</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="damaged">
                        <span>Damaged Stock</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="suppliers">
                        <span>Suppliers</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="barcodes">
                        <span>Barcodes & QR</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="reports">
                        <span>Reports</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" data-tab="users">
                        <span>User Management</span>
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
                    <h2 id="currentTabTitle">Overview</h2>
                    <p id="currentTabSubtitle">Monitor stock values, low alerts, and quick activities</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="toggleTheme()" id="themeToggleBtn">Dark Mode</button>
                    <div class="user-profile">
                        <div class="user-avatar" id="avatarInitial">I</div>
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
                    <!-- Loaded dynamically -->
                </div>
                <div class="layout-grid">
                    <!-- Warehouse utilization -->
                    <div class="content-card">
                        <div class="card-header">
                            <div class="card-title">
                                <h3>Warehouse Stock Distribution</h3>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table" id="whUtilizationTable">
                                <thead>
                                    <tr>
                                        <th>Warehouse</th>
                                        <th>Total Stock Qty</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Purchase orders totals trend -->
                    <div class="content-card">
                        <div class="card-header">
                            <div class="card-title">
                                <h3>Recent PO Financial Trends</h3>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table" id="poTrendTable">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Total PO Amount</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PRODUCTS TAB -->
            <div id="tab-products" class="tab-panel" style="display: none;">
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Product Catalog Registry</h3>
                        </div>
                        <button class="btn btn-primary" onclick="openModal('productModal')">Add Product</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="productsTable">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Price (Buy/Sell)</th>
                                    <th>Min/Max Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- WAREHOUSES TAB -->
            <div id="tab-warehouses" class="tab-panel" style="display: none;">
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Warehouse Facilities</h3>
                        </div>
                        <button class="btn btn-primary" onclick="openModal('warehouseModal')">Add Warehouse</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="warehousesTable">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Warehouse Name</th>
                                    <th>Location</th>
                                    <th>Manager Name</th>
                                    <th>Capacity</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- STOCK LEDGER TAB -->
            <div id="tab-stock" class="tab-panel" style="display: none;">
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>General Stock Balances</h3>
                        </div>
                        <button class="btn btn-primary" onclick="openModal('stockAdjustModal')">Stock Adjust</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="stockTable">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Product Name</th>
                                    <th>Warehouse</th>
                                    <th>Available Stock</th>
                                    <th>Reserved Stock</th>
                                    <th>Damaged Stock</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- INWARD / OUTWARD TAB -->
            <div id="tab-inward-outward" class="tab-panel" style="display: none;">
                <div class="layout-grid">
                    <div class="content-card">
                        <div class="card-header">
                            <div class="card-title">
                                <h3>Stock Inward Receipts</h3>
                            </div>
                            <button class="btn btn-primary" onclick="openModal('inwardModal')">Log Inward</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table" id="inwardTable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Ref Type</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="content-card">
                        <div class="card-header">
                            <div class="card-title">
                                <h3>Stock Outward Consumption</h3>
                            </div>
                            <button class="btn btn-primary" onclick="openModal('outwardModal')">Log Outward</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table" id="outwardTable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Ref Type</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PURCHASE ORDERS TAB -->
            <div id="tab-purchase-orders" class="tab-panel" style="display: none;">
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Supplier Purchase Orders</h3>
                        </div>
                        <button class="btn btn-primary" onclick="openModal('poModal')">Create PO</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="poTable">
                            <thead>
                                <tr>
                                    <th>PO #</th>
                                    <th>Supplier</th>
                                    <th>Order Date</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- GRN TAB -->
            <div id="tab-grn" class="tab-panel" style="display: none;">
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Goods Receipt Notes (GRN) Registry</h3>
                        </div>
                        <button class="btn btn-primary" onclick="openModal('grnModal')">Receive PO Goods (GRN)</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="grnTable">
                            <thead>
                                <tr>
                                    <th>GRN #</th>
                                    <th>PO #</th>
                                    <th>Receive Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TRANSFERS TAB -->
            <div id="tab-transfers" class="tab-panel" style="display: none;">
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Warehouse Stock Transfers</h3>
                        </div>
                        <button class="btn btn-primary" onclick="openModal('transferModal')">New Transfer</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="transfersTable">
                            <thead>
                                <tr>
                                    <th>Transfer #</th>
                                    <th>From Warehouse</th>
                                    <th>To Warehouse</th>
                                    <th>Transfer Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- PHYSICAL AUDITS TAB -->
            <div id="tab-audits" class="tab-panel" style="display: none;">
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Physical Stock Audits</h3>
                        </div>
                        <button class="btn btn-primary" onclick="openModal('auditModal')">New Audit Schedule</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="auditsTable">
                            <thead>
                                <tr>
                                    <th>Audit #</th>
                                    <th>Warehouse</th>
                                    <th>Audit Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- DAMAGED STOCK TAB -->
            <div id="tab-damaged" class="tab-panel" style="display: none;">
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Damaged Inventory Log</h3>
                        </div>
                        <button class="btn btn-primary" onclick="openModal('damageModal')">Report Damage</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="damagedTable">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Warehouse</th>
                                    <th>Quantity</th>
                                    <th>Report Date</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SUPPLIERS TAB -->
            <div id="tab-suppliers" class="tab-panel" style="display: none;">
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Supplier Registry</h3>
                        </div>
                        <button class="btn btn-primary" onclick="openModal('supplierModal')">Add Supplier</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="suppliersTable">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Supplier Name</th>
                                    <th>Contact Person</th>
                                    <th>Mobile & Email</th>
                                    <th>GST Number</th>
                                    <th>Rating</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- BARCODES AND QR TAB -->
            <div id="tab-barcodes" class="tab-panel" style="display: none;">
                <div class="layout-grid">
                    <div class="content-card">
                        <div class="card-header">
                            <div class="card-title">
                                <h3>Generate Scanning Labels</h3>
                            </div>
                        </div>
                        <form id="labelForm">
                            <div class="form-group">
                                <label>Select Product</label>
                                <select id="labelProductSelect" class="form-select" required></select>
                            </div>
                            <div class="form-group">
                                <label>Label Type</label>
                                <select id="labelTypeSelect" class="form-select">
                                    <option value="barcode">UPC Barcode</option>
                                    <option value="qrcode">QR Code</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Generate Label</button>
                        </form>
                        <div id="labelPreviewArea" style="margin-top:20px; text-align:center; display:none;">
                            <h4 id="labelProductTitle" style="font-size:14px; margin-bottom:10px;"></h4>
                            <img id="labelPreviewImg" src="" style="max-width:180px; padding:10px; background:#fff; border:1px solid var(--border-glass); border-radius:10px;" alt="Label">
                        </div>
                    </div>

                    <div class="content-card">
                        <div class="card-header">
                            <div class="card-title">
                                <h3>Mock Scan Lookup Playground</h3>
                            </div>
                        </div>
                        <form id="scanLookupForm">
                            <div class="form-group">
                                <label>Enter Barcode or QR code text</label>
                                <input type="text" id="scanText" class="form-input" required placeholder="e.g. 8901030753645 or INV-SKU-PROD-INV-001">
                            </div>
                            <button type="submit" class="btn btn-secondary">Simulate Scan Lookup</button>
                        </form>
                        <div id="scanResultArea" style="margin-top:20px; display:none;" class="form-group">
                            <label>Lookup Result:</label>
                            <div id="scanResultDetails" style="padding:15px; background:rgba(0,0,0,0.02); border:1px solid var(--border-glass); border-radius:10px; font-size:13px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- REPORTS TAB -->
            <div id="tab-reports" class="tab-panel" style="display: none;">
                <!-- Stock Valuation Report -->
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Warehouse Inventory Valuation Report</h3>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="reportValuationTable">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Unit</th>
                                    <th>Purchase Cost</th>
                                    <th>Total Stock</th>
                                    <th>Total Valuation</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- Low Stock Report -->
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Critical Low Stock Threshold Alerts</h3>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="reportLowStockTable">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Product Name</th>
                                    <th>Min Required</th>
                                    <th>Current Available</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- Stock Movements Log -->
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Historical Stock Movement Ledger (Receipts & Consumption)</h3>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="reportMovementsTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Product Name</th>
                                    <th>Warehouse</th>
                                    <th>Qty</th>
                                    <th>Reference details</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- Audit Variances -->
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Physical Stock Audit Variance Auditing Reports</h3>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="reportAuditsTable">
                            <thead>
                                <tr>
                                    <th>Audit #</th>
                                    <th>Date</th>
                                    <th>Warehouse</th>
                                    <th>Product SKU</th>
                                    <th>System Qty</th>
                                    <th>Physical Qty</th>
                                    <th>Variance</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- USER MANAGEMENT TAB -->
            <div id="tab-users" class="tab-panel" style="display: none;">
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Approved Employee Roles</h3>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="usersTable">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>System Role</th>
                                    <th>Status</th>
                                    <th>Approval Controls</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SMTP SETTINGS TAB -->
            <div id="tab-settings" class="tab-panel" style="display: none;">
                <div class="layout-grid">
                    <div class="content-card">
                        <div class="card-header">
                            <div class="card-title">
                                <h3>SMTP Transporter Configuration</h3>
                            </div>
                        </div>
                        <form id="smtpSettingsForm">
                            <div class="form-group">
                                <label>Enable Custom SMTP</label>
                                <select id="smtpEnabled" class="form-select">
                                    <option value="no">Disabled (WordPress Default)</option>
                                    <option value="yes">Enabled</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>SMTP Host</label>
                                <input type="text" id="smtpHost" class="form-input" placeholder="smtp.gmail.com">
                            </div>
                            <div class="form-group">
                                <label>SMTP Port</label>
                                <input type="text" id="smtpPort" class="form-input" placeholder="587">
                            </div>
                            <div class="form-group">
                                <label>SMTP Username</label>
                                <input type="text" id="smtpUsername" class="form-input" placeholder="you@gmail.com">
                            </div>
                            <div class="form-group">
                                <label>SMTP Password</label>
                                <input type="password" id="smtpPassword" class="form-input" placeholder="******">
                            </div>
                            <div class="form-group">
                                <label>Encryption</label>
                                <select id="smtpEncryption" class="form-select">
                                    <option value="tls">TLS (Recommended)</option>
                                    <option value="ssl">SSL</option>
                                    <option value="none">None</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>From Sender Email</label>
                                <input type="email" id="smtpFromEmail" class="form-input" placeholder="noreply@domain.com">
                            </div>
                            <div class="form-group">
                                <label>From Sender Name</label>
                                <input type="text" id="smtpFromName" class="form-input" placeholder="Inventory ERP">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Configuration</button>
                        </form>
                    </div>

                    <div class="content-card">
                        <div class="card-header">
                            <div class="card-title">
                                <h3>Test SMTP Connection</h3>
                            </div>
                        </div>
                        <form id="smtpTestForm">
                            <div class="form-group">
                                <label>Recipient Email Address</label>
                                <input type="email" id="smtpTestEmail" class="form-input" required placeholder="test@recipient.com">
                            </div>
                            <button type="submit" class="btn btn-secondary">Send Connection Test Mail</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </section>

    <!-- 3. POPUP MODALS -->

    <!-- Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Create Product Profile</h3>
                <button class="modal-close" onclick="closeModal('productModal')">&times;</button>
            </div>
            <form id="productForm">
                <div class="form-group">
                    <label>SKU (Stock Keeping Unit) *</label>
                    <input type="text" id="pSku" class="form-input" required placeholder="PROD-INV-XXXX">
                </div>
                <div class="form-group">
                    <label>Barcode ID (Optional)</label>
                    <input type="text" id="pBarcode" class="form-input" placeholder="E.g. UPC Code">
                </div>
                <div class="form-group">
                    <label>Product Name *</label>
                    <input type="text" id="pName" class="form-input" required placeholder="E.g. Steel Racks">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" id="pCategory" class="form-input" placeholder="E.g. Equipment">
                </div>
                <div class="form-group">
                    <label>Brand</label>
                    <input type="text" id="pBrand" class="form-input" placeholder="E.g. Brand Name">
                </div>
                <div class="form-group">
                    <label>Unit of Measure</label>
                    <input type="text" id="pUnit" class="form-input" placeholder="PCS, SETS, KG">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label>Purchase Price</label>
                        <input type="number" step="0.01" id="pBuyPrice" class="form-input" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Selling Price</label>
                        <input type="number" step="0.01" id="pSellPrice" class="form-input" placeholder="0.00">
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label>Min Alert Level</label>
                        <input type="number" id="pMinStock" class="form-input" placeholder="10">
                    </div>
                    <div class="form-group">
                        <label>Max Storage limit</label>
                        <input type="number" id="pMaxStock" class="form-input" placeholder="1000">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Save Product</button>
            </form>
        </div>
    </div>

    <!-- Warehouse Modal -->
    <div id="warehouseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Register Warehouse Facility</h3>
                <button class="modal-close" onclick="closeModal('warehouseModal')">&times;</button>
            </div>
            <form id="warehouseForm">
                <div class="form-group">
                    <label>Warehouse Code *</label>
                    <input type="text" id="wCode" class="form-input" required placeholder="WH-MUM-01">
                </div>
                <div class="form-group">
                    <label>Warehouse Name *</label>
                    <input type="text" id="wName" class="form-input" required placeholder="Main Mumbai Hub">
                </div>
                <div class="form-group">
                    <label>Location Area Address</label>
                    <input type="text" id="wLocation" class="form-input">
                </div>
                <div class="form-group">
                    <label>Manager Name</label>
                    <input type="text" id="wManager" class="form-input">
                </div>
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" id="wContact" class="form-input">
                </div>
                <div class="form-group">
                    <label>Max Volume capacity</label>
                    <input type="number" id="wCapacity" class="form-input" placeholder="10000">
                </div>
                <button type="submit" class="btn btn-primary btn-full">Create Warehouse</button>
            </form>
        </div>
    </div>

    <!-- Stock Adjust Modal -->
    <div id="stockAdjustModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Manual Stock Balance Adjustment</h3>
                <button class="modal-close" onclick="closeModal('stockAdjustModal')">&times;</button>
            </div>
            <form id="stockAdjustForm">
                <div class="form-group">
                    <label>Select Product</label>
                    <select id="adjProductSelect" class="form-select" required></select>
                </div>
                <div class="form-group">
                    <label>Select Warehouse</label>
                    <select id="adjWarehouseSelect" class="form-select" required></select>
                </div>
                <div class="form-group">
                    <label>New Available Stock Count</label>
                    <input type="number" id="adjAvailable" class="form-input" required placeholder="0">
                </div>
                <div class="form-group">
                    <label>New Reserved Stock Count</label>
                    <input type="number" id="adjReserved" class="form-input" placeholder="0">
                </div>
                <div class="form-group">
                    <label>New Damaged Stock Count</label>
                    <input type="number" id="adjDamaged" class="form-input" placeholder="0">
                </div>
                <button type="submit" class="btn btn-primary btn-full">Adjust Stock</button>
            </form>
        </div>
    </div>

    <!-- Inward Modal -->
    <div id="inwardModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Record Stock Inward</h3>
                <button class="modal-close" onclick="closeModal('inwardModal')">&times;</button>
            </div>
            <form id="inwardForm">
                <div class="form-group">
                    <label>Inward Date</label>
                    <input type="date" id="inDate" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Receipt Reference Details</label>
                    <input type="text" id="inRef" class="form-input" placeholder="e.g. Manual Adjustment">
                </div>
                <div class="form-group">
                    <label>Remarks</label>
                    <input type="text" id="inRemarks" class="form-input">
                </div>
                <div class="form-group">
                    <label>Item Lines</label>
                    <table class="dynamic-table" id="inwardLinesTable">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Warehouse</th>
                                <th>Qty</th>
                                <th>Batch</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><select class="form-select in-prod" required></select></td>
                                <td><select class="form-select in-wh" required></select></td>
                                <td><input type="number" class="form-input in-qty" required style="width:80px;"></td>
                                <td><input type="text" class="form-input in-batch" style="width:100px;"></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-secondary" onclick="addInwardLineRow()">+ Add Row</button>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Post Inward</button>
            </form>
        </div>
    </div>

    <!-- Outward Modal -->
    <div id="outwardModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Record Outward Issues</h3>
                <button class="modal-close" onclick="closeModal('outwardModal')">&times;</button>
            </div>
            <form id="outwardForm">
                <div class="form-group">
                    <label>Outward Date</label>
                    <input type="date" id="outDate" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Reference Details</label>
                    <input type="text" id="outRef" class="form-input" placeholder="e.g. Sales Issue">
                </div>
                <div class="form-group">
                    <label>Remarks</label>
                    <input type="text" id="outRemarks" class="form-input">
                </div>
                <div class="form-group">
                    <label>Item Lines</label>
                    <table class="dynamic-table" id="outwardLinesTable">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Warehouse</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><select class="form-select out-prod" required></select></td>
                                <td><select class="form-select out-wh" required></select></td>
                                <td><input type="number" class="form-input out-qty" required style="width:80px;"></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-secondary" onclick="addOutwardLineRow()">+ Add Row</button>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Post Outward</button>
            </form>
        </div>
    </div>

    <!-- PO Modal -->
    <div id="poModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Create Supplier Purchase Order</h3>
                <button class="modal-close" onclick="closeModal('poModal')">&times;</button>
            </div>
            <form id="poForm">
                <div class="form-group">
                    <label>Supplier *</label>
                    <select id="poSupplierSelect" class="form-select" required></select>
                </div>
                <div class="form-group">
                    <label>Order Date</label>
                    <input type="date" id="poDate" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Items Order Lines</label>
                    <table class="dynamic-table" id="poLinesTable">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price (Per Unit)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><select class="form-select po-prod" required></select></td>
                                <td><input type="number" class="form-input po-qty" required style="width:80px;"></td>
                                <td><input type="number" step="0.01" class="form-input po-price" style="width:100px;"></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-secondary" onclick="addPoLineRow()">+ Add Row</button>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Submit PO</button>
            </form>
        </div>
    </div>

    <!-- GRN Modal -->
    <div id="grnModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Receive PO Goods (GRN)</h3>
                <button class="modal-close" onclick="closeModal('grnModal')">&times;</button>
            </div>
            <form id="grnForm">
                <div class="form-group">
                    <label>Select Purchase Order (Pending/Approved) *</label>
                    <select id="grnPoSelect" class="form-select" required onchange="loadPoItemsForGrn()"></select>
                </div>
                <div class="form-group">
                    <label>Receiving Warehouse *</label>
                    <select id="grnWarehouseSelect" class="form-select" required></select>
                </div>
                <div class="form-group">
                    <label>Receive Date</label>
                    <input type="date" id="grnDate" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Ordered Items Checklist</label>
                    <table class="dynamic-table" id="grnItemsChecklist">
                        <thead>
                            <tr>
                                <th>Product SKU</th>
                                <th>Ordered Qty</th>
                                <th>Received Qty</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Post GRN Receipt</button>
            </form>
        </div>
    </div>

    <!-- Transfer Modal -->
    <div id="transferModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Warehouse Stock Transfer</h3>
                <button class="modal-close" onclick="closeModal('transferModal')">&times;</button>
            </div>
            <form id="transferForm">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label>Source Warehouse *</label>
                        <select id="tFromWh" class="form-select" required></select>
                    </div>
                    <div class="form-group">
                        <label>Destination Warehouse *</label>
                        <select id="tToWh" class="form-select" required></select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Transfer Date</label>
                    <input type="date" id="tDate" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Instant Complete?</label>
                    <select id="tStatus" class="form-select">
                        <option value="Pending">No (Requires manager verification at destination)</option>
                        <option value="Completed">Yes (Instantly shift stock)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Items list</label>
                    <table class="dynamic-table" id="transferLinesTable">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><select class="form-select t-prod" required></select></td>
                                <td><input type="number" class="form-input t-qty" required style="width:100px;"></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-secondary" onclick="addTransferLineRow()">+ Add Row</button>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Initiate Transfer</button>
            </form>
        </div>
    </div>

    <!-- Audit Modal -->
    <div id="auditModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Schedule Physical Audit</h3>
                <button class="modal-close" onclick="closeModal('auditModal')">&times;</button>
            </div>
            <form id="auditForm">
                <div class="form-group">
                    <label>Auditing Warehouse *</label>
                    <select id="aWarehouseSelect" class="form-select" required onchange="loadProductsForAudit()"></select>
                </div>
                <div class="form-group">
                    <label>Audit Date</label>
                    <input type="date" id="aDate" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Counted Products Checklist</label>
                    <table class="dynamic-table" id="auditItemsChecklist">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Physical Counted Qty</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Post Audit Schedule</button>
            </form>
        </div>
    </div>

    <!-- Damage Modal -->
    <div id="damageModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Report Stock Damage</h3>
                <button class="modal-close" onclick="closeModal('damageModal')">&times;</button>
            </div>
            <form id="damageForm">
                <div class="form-group">
                    <label>Select Product *</label>
                    <select id="dProduct" class="form-select" required></select>
                </div>
                <div class="form-group">
                    <label>Select Warehouse *</label>
                    <select id="dWarehouse" class="form-select" required></select>
                </div>
                <div class="form-group">
                    <label>Damaged Quantity *</label>
                    <input type="number" id="dQty" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Report Date</label>
                    <input type="date" id="dDate" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Remarks / Root cause</label>
                    <input type="text" id="dRemarks" class="form-input">
                </div>
                <button type="submit" class="btn btn-primary btn-full">Log Damaged Stock</button>
            </form>
        </div>
    </div>

    <!-- Supplier Modal -->
    <div id="supplierModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Register Supplier</h3>
                <button class="modal-close" onclick="closeModal('supplierModal')">&times;</button>
            </div>
            <form id="supplierForm">
                <div class="form-group">
                    <label>Supplier Name *</label>
                    <input type="text" id="sName" class="form-input" required placeholder="E.g. Global Logistical Solutions Ltd">
                </div>
                <div class="form-group">
                    <label>Contact Person</label>
                    <input type="text" id="sPerson" class="form-input">
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" id="sMobile" class="form-input">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="sEmail" class="form-input">
                </div>
                <div class="form-group">
                    <label>GST Number</label>
                    <input type="text" id="sGst" class="form-input">
                </div>
                <div class="form-group">
                    <label>Corporate Address</label>
                    <input type="text" id="sAddress" class="form-input">
                </div>
                <button type="submit" class="btn btn-primary btn-full">Register Supplier</button>
            </form>
        </div>
    </div>

    <!-- Toast Notification Element -->
    <div id="toastNotification" class="toast">
        <span id="toastIcon">ℹ️</span>
        <span id="toastMessage">Default message</span>
    </div>

    <!-- 4. CORE JAVASCRIPT LOGIC -->
    <script>
        // Global variables
        var apiBase = '/wp-json/inventory-management/v1';
        var productsCache = [];
        var warehousesCache = [];
        var suppliersCache = [];
        var pendingPoCache = [];

        // Dynamic adding row handlers
        function addInwardLineRow() {
            var row = `<tr>
                <td><select class="form-select in-prod" required>${getProductOptions()}</select></td>
                <td><select class="form-select in-wh" required>${getWarehouseOptions()}</select></td>
                <td><input type="number" class="form-input in-qty" required style="width:80px;"></td>
                <td><input type="text" class="form-input in-batch" style="width:100px;"></td>
            </tr>`;
            document.querySelector('#inwardLinesTable tbody').insertAdjacentHTML('beforeend', row);
        }

        function addOutwardLineRow() {
            var row = `<tr>
                <td><select class="form-select out-prod" required>${getProductOptions()}</select></td>
                <td><select class="form-select out-wh" required>${getWarehouseOptions()}</select></td>
                <td><input type="number" class="form-input out-qty" required style="width:80px;"></td>
            </tr>`;
            document.querySelector('#outwardLinesTable tbody').insertAdjacentHTML('beforeend', row);
        }

        function addPoLineRow() {
            var row = `<tr>
                <td><select class="form-select po-prod" required>${getProductOptions()}</select></td>
                <td><input type="number" class="form-input po-qty" required style="width:80px;"></td>
                <td><input type="number" step="0.01" class="form-input po-price" style="width:100px;"></td>
            </tr>`;
            document.querySelector('#poLinesTable tbody').insertAdjacentHTML('beforeend', row);
        }

        function addTransferLineRow() {
            var row = `<tr>
                <td><select class="form-select t-prod" required>${getProductOptions()}</select></td>
                <td><input type="number" class="form-input t-qty" required style="width:100px;"></td>
            </tr>`;
            document.querySelector('#transferLinesTable tbody').insertAdjacentHTML('beforeend', row);
        }

        function getProductOptions() {
            return productsCache.map(p => `<option value="${p.id}">${p.sku} - ${p.product_name}</option>`).join('');
        }

        function getWarehouseOptions() {
            return warehousesCache.map(w => `<option value="${w.id}">${w.warehouse_name} (${w.warehouse_code})</option>`).join('');
        }

        // Fetch wrapper
        function apiFetch(path, options) {
            options = options || {};
            options.headers = options.headers || {};
            options.headers['Content-Type'] = 'application/json';
            
            var token = localStorage.getItem('inv_auth_token');
            if (token) {
                options.headers['Authorization'] = 'Bearer ' + token;
            }

            return fetch(apiBase + path, options).then(res => {
                if (res.status === 401) {
                    // Unauthorized - trigger logout
                    logout();
                    throw new Error('Unauthorized session.');
                }
                return res.json().then(data => {
                    if (!res.ok) {
                        throw new Error(data.message || 'API request failed.');
                    }
                    return data;
                });
            });
        }

        // Toasts
        function showToast(msg, type) {
            type = type || 'info';
            var toast = document.getElementById('toastNotification');
            var icon = document.getElementById('toastIcon');
            var message = document.getElementById('toastMessage');

            message.textContent = msg;
            if (type === 'success') {
                icon.textContent = '✅';
                toast.style.borderColor = 'var(--accent-success)';
            } else if (type === 'error') {
                icon.textContent = '❌';
                toast.style.borderColor = 'var(--accent-danger)';
            } else {
                icon.textContent = 'ℹ️';
                toast.style.borderColor = 'var(--color-brand)';
            }

            toast.style.display = 'flex';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }

        // Modal triggers
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
            // populate date fields
            var today = new Date().toISOString().split('T')[0];
            if (id === 'inwardModal') document.getElementById('inDate').value = today;
            if (id === 'outwardModal') document.getElementById('outDate').value = today;
            if (id === 'poModal') document.getElementById('poDate').value = today;
            if (id === 'grnModal') document.getElementById('grnDate').value = today;
            if (id === 'transferModal') document.getElementById('tDate').value = today;
            if (id === 'auditModal') document.getElementById('aDate').value = today;
            if (id === 'damageModal') document.getElementById('dDate').value = today;
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        // Auth form screens
        function toggleAuthCards(card) {
            document.getElementById('loginCard').style.display = card === 'login' ? 'block' : 'none';
            document.getElementById('registerCard').style.display = card === 'register' ? 'block' : 'none';
            document.getElementById('verifyCard').style.display = card === 'verify' ? 'block' : 'none';
        }

        // Auth Logic
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var username = document.getElementById('loginUsername').value;
            var pass = document.getElementById('loginPassword').value;
            var otp = document.getElementById('loginOtp').value;
            
            var payload = { username: username };
            if (otp) {
                payload.otp = otp;
            } else if (pass) {
                payload.password = pass;
            }

            // If neither passcode nor OTP is visible, we initiate passwordless login
            if (!pass && !otp && document.getElementById('loginOtpGroup').style.display === 'none') {
                apiFetch('/auth/login/initiate', {
                    method: 'POST',
                    body: JSON.stringify({ username_or_email: username })
                }).then(res => {
                    showToast('OTP Login verification code sent to: ' + res.data.email, 'success');
                    document.getElementById('loginOtpGroup').style.display = 'block';
                    document.getElementById('loginSubmitBtn').textContent = 'Confirm OTP & Sign In';
                }).catch(err => {
                    showToast(err.message, 'error');
                });
            } else {
                // Perform complete login
                apiFetch('/auth/login', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                }).then(res => {
                    localStorage.setItem('inv_auth_token', res.data.token);
                    localStorage.setItem('inv_current_user', JSON.stringify(res.data.user));
                    showToast('Welcome, logged in successfully.', 'success');
                    
                    document.getElementById('authSection').style.display = 'none';
                    document.getElementById('appSection').style.display = 'flex';
                    initApp();
                }).catch(err => {
                    showToast(err.message, 'error');
                });
            }
        });

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var payload = {
                username: document.getElementById('regUsername').value,
                email: document.getElementById('regEmail').value,
                name: document.getElementById('regName').value,
                role: document.getElementById('regRole').value
            };

            apiFetch('/auth/register', {
                method: 'POST',
                body: JSON.stringify(payload)
            }).then(res => {
                showToast('OTP sent. Verify your email to complete registration.', 'success');
                localStorage.setItem('inv_register_email', payload.email);
                toggleAuthCards('verify');
            }).catch(err => {
                showToast(err.message, 'error');
            });
        });

        document.getElementById('verifyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var payload = {
                email: localStorage.getItem('inv_register_email'),
                otp: document.getElementById('verifyOtp').value
            };

            apiFetch('/auth/register/verify', {
                method: 'POST',
                body: JSON.stringify(payload)
            }).then(res => {
                showToast('Registration successful. Awaiting administrator approval.', 'success');
                toggleAuthCards('login');
            }).catch(err => {
                showToast(err.message, 'error');
            });
        });

        function logout() {
            apiFetch('/auth/logout', { method: 'POST' }).finally(() => {
                localStorage.removeItem('inv_auth_token');
                localStorage.removeItem('inv_current_user');
                document.getElementById('authSection').style.display = 'flex';
                document.getElementById('appSection').style.display = 'none';
            });
        }

        // Theme management
        function toggleTheme() {
            var html = document.documentElement;
            var btn = document.getElementById('themeToggleBtn');
            if (html.classList.contains('dark-mode')) {
                html.classList.remove('dark-mode');
                localStorage.setItem('inv_dark_mode', 'false');
                btn.textContent = 'Dark Mode';
            } else {
                html.classList.add('dark-mode');
                localStorage.setItem('inv_dark_mode', 'true');
                btn.textContent = 'Light Mode';
            }
        }

        // Sidebar Navigation
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var tab = this.getAttribute('data-tab');
                switchTab(tab);
            });
        });

        function switchTab(tab) {
            document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');

            var activeLink = document.querySelector(`.sidebar-link[data-tab="${tab}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }

            var panel = document.getElementById(`tab-${tab}`);
            if (panel) {
                panel.style.display = 'block';
            }

            // Update title
            var title = tab.charAt(0).toUpperCase() + tab.slice(1);
            if (tab === 'inward-outward') title = 'Inward / Outward Stock Receipts';
            if (tab === 'purchase-orders') title = 'Purchase Orders';
            if (tab === 'grn') title = 'Goods Receipt Notes';
            if (tab === 'damaged') title = 'Damaged Inventory';
            if (tab === 'barcodes') title = 'Barcodes & QR Labels';
            
            document.getElementById('currentTabTitle').textContent = title;
            document.getElementById('currentTabSubtitle').textContent = `Manage and track ${title.toLowerCase()}`;

            localStorage.setItem('inv_active_tab', tab);
            loadTabContent(tab);
        }

        // Initialize application data
        function initApp() {
            var user = JSON.parse(localStorage.getItem('inv_current_user') || '{}');
            document.getElementById('profileName').textContent = user.name || 'User';
            document.getElementById('profileRole').textContent = user.role ? user.role.replace('inventory_', '').toUpperCase() : 'STAFF';
            document.getElementById('avatarInitial').textContent = (user.name || 'I').charAt(0).toUpperCase();

            // Synced light/dark toggle text
            var btn = document.getElementById('themeToggleBtn');
            if (document.documentElement.classList.contains('dark-mode')) {
                btn.textContent = 'Light Mode';
            } else {
                btn.textContent = 'Dark Mode';
            }

            // Sync Caches
            Promise.all([
                apiFetch('/products'),
                apiFetch('/warehouses'),
                apiFetch('/suppliers')
            ]).then(([p, w, s]) => {
                productsCache = p.data.data || [];
                warehousesCache = w.data.data || [];
                suppliersCache = s.data.data || [];
                
                // Populate selects
                var prodSelects = ['adjProductSelect', 'dProduct', 'labelProductSelect'];
                prodSelects.forEach(selId => {
                    var el = document.getElementById(selId);
                    if (el) el.innerHTML = getProductOptions();
                });

                var whSelects = ['adjWarehouseSelect', 'grnWarehouseSelect', 'tFromWh', 'tToWh', 'aWarehouseSelect', 'dWarehouse'];
                whSelects.forEach(selId => {
                    var el = document.getElementById(selId);
                    if (el) el.innerHTML = getWarehouseOptions();
                });

                var supSelects = ['poSupplierSelect'];
                supSelects.forEach(selId => {
                    var el = document.getElementById(selId);
                    if (el) el.innerHTML = suppliersCache.map(s => `<option value="${s.id}">${s.supplier_name}</option>`).join('');
                });

                // Load initial tab
                var savedTab = localStorage.getItem('inv_active_tab') || 'overview';
                switchTab(savedTab);
            }).catch(err => {
                showToast(err.message, 'error');
            });
        }

        // Load tab contents
        function loadTabContent(tab) {
            if (tab === 'overview') {
                apiFetch('/dashboard').then(res => {
                    var cards = res.data.cards;
                    document.getElementById('dashboardStats').innerHTML = `
                        <div class="stat-card">
                            <div class="stat-header">
                                <span class="stat-title">Products</span>
                                <div class="stat-icon">📦</div>
                            </div>
                            <div class="stat-value">${cards.total_products}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-header">
                                <span class="stat-title">Warehouses</span>
                                <div class="stat-icon">🏢</div>
                            </div>
                            <div class="stat-value">${cards.total_warehouses}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-header">
                                <span class="stat-title">Stock Valuation</span>
                                <div class="stat-icon">💰</div>
                            </div>
                            <div class="stat-value">₹${parseFloat(cards.total_stock_value).toLocaleString('en-IN', {minimumFractionDigits: 2})}</div>
                        </div>
                        <div class="stat-card" style="border-color: ${cards.low_stock_items > 0 ? 'var(--accent-warning)' : 'var(--border-glass)'};">
                            <div class="stat-header">
                                <span class="stat-title">Low Stock Alerts</span>
                                <div class="stat-icon" style="color:var(--accent-warning);">⚠️</div>
                            </div>
                            <div class="stat-value">${cards.low_stock_items}</div>
                        </div>
                    `;

                    // Populate utilization
                    var utilRows = (res.data.analytics.warehouse_utilization || []).map(row => `
                        <tr>
                            <td>${row.warehouse_name}</td>
                            <td><strong>${row.total_stock} Units</strong></td>
                        </tr>
                    `).join('');
                    document.querySelector('#whUtilizationTable tbody').innerHTML = utilRows || '<tr><td colspan="2">No active data</td></tr>';

                    // Populate trends
                    var trendRows = (res.data.analytics.purchase_trends || []).map(row => `
                        <tr>
                            <td>${row.month}</td>
                            <td>₹${parseFloat(row.total_val).toLocaleString('en-IN', {minimumFractionDigits:2})}</td>
                        </tr>
                    `).join('');
                    document.querySelector('#poTrendTable tbody').innerHTML = trendRows || '<tr><td colspan="2">No PO trend recorded</td></tr>';
                });
            }

            if (tab === 'products') {
                apiFetch('/products').then(res => {
                    var rows = (res.data.data || []).map(row => `
                        <tr>
                            <td><strong>${row.sku}</strong></td>
                            <td>${row.product_name}</td>
                            <td>${row.category}</td>
                            <td>₹${row.purchase_price} / ₹${row.selling_price}</td>
                            <td>Min: ${row.minimum_stock} / Max: ${row.maximum_stock}</td>
                            <td><span class="badge badge-success">${row.status}</span></td>
                            <td>
                                <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;" onclick="deleteProduct(${row.id})">Delete</button>
                            </td>
                        </tr>
                    `).join('');
                    document.querySelector('#productsTable tbody').innerHTML = rows || '<tr><td colspan="7">No products added.</td></tr>';
                });
            }

            if (tab === 'warehouses') {
                apiFetch('/warehouses').then(res => {
                    var rows = (res.data.data || []).map(row => `
                        <tr>
                            <td><strong>${row.warehouse_code}</strong></td>
                            <td>${row.warehouse_name}</td>
                            <td>${row.location || 'N/A'}</td>
                            <td>${row.manager_name || 'N/A'}</td>
                            <td>${row.capacity} Sets</td>
                            <td><span class="badge badge-success">${row.status}</span></td>
                        </tr>
                    `).join('');
                    document.querySelector('#warehousesTable tbody').innerHTML = rows || '<tr><td colspan="6">No warehouses added.</td></tr>';
                });
            }

            if (tab === 'stock') {
                apiFetch('/inventory').then(res => {
                    var rows = (res.data.data || []).map(row => `
                        <tr>
                            <td><strong>${row.sku}</strong></td>
                            <td>${row.product_name}</td>
                            <td>${row.warehouse_name} (${row.warehouse_code})</td>
                            <td><strong>${row.available_stock} ${row.unit}</strong></td>
                            <td>${row.reserved_stock}</td>
                            <td><span style="color:var(--accent-danger); font-weight:600;">${row.damaged_stock}</span></td>
                        </tr>
                    `).join('');
                    document.querySelector('#stockTable tbody').innerHTML = rows || '<tr><td colspan="6">No stock ledger balances recorded.</td></tr>';
                });
            }

            if (tab === 'inward-outward') {
                apiFetch('/stock-inward').then(res => {
                    var rows = (res.data.data || []).map(row => `
                        <tr>
                            <td>${row.inward_date}</td>
                            <td><span class="badge badge-info">${row.reference_type}</span></td>
                            <td>${row.remarks}</td>
                        </tr>
                    `).join('');
                    document.querySelector('#inwardTable tbody').innerHTML = rows || '<tr><td colspan="3">No inward logs.</td></tr>';
                });

                apiFetch('/stock-outward').then(res => {
                    var rows = (res.data.data || []).map(row => `
                        <tr>
                            <td>${row.outward_date}</td>
                            <td><span class="badge badge-warning">${row.reference_type}</span></td>
                            <td>${row.remarks}</td>
                        </tr>
                    `).join('');
                    document.querySelector('#outwardTable tbody').innerHTML = rows || '<tr><td colspan="3">No outward logs.</td></tr>';
                });
            }

            if (tab === 'purchase-orders') {
                apiFetch('/purchase-orders').then(res => {
                    var rows = (res.data.data || []).map(row => {
                        var badgeClass = 'badge-warning';
                        if (row.status === 'Completed' || row.status === 'Approved') badgeClass = 'badge-success';
                        if (row.status === 'Rejected' || row.status === 'Cancelled') badgeClass = 'badge-danger';

                        var approveBtn = row.status === 'Pending' ? `
                            <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;" onclick="updatePoStatus(${row.id}, 'Approved')">Approve</button>
                            <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;" onclick="updatePoStatus(${row.id}, 'Rejected')">Reject</button>
                        ` : '';

                        return `
                            <tr>
                                <td><strong>${row.po_number}</strong></td>
                                <td>${row.supplier_name}</td>
                                <td>${row.order_date}</td>
                                <td>₹${parseFloat(row.total_amount).toFixed(2)}</td>
                                <td><span class="badge ${badgeClass}">${row.status}</span></td>
                                <td>
                                    ${approveBtn}
                                    <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;" onclick="deletePo(${row.id})">Delete</button>
                                </td>
                            </tr>
                        `;
                    }).join('');
                    document.querySelector('#poTable tbody').innerHTML = rows || '<tr><td colspan="6">No purchase orders listed.</td></tr>';
                });
            }

            if (tab === 'grn') {
                apiFetch('/grn').then(res => {
                    var rows = (res.data.data || []).map(row => `
                        <tr>
                            <td><strong>${row.grn_number}</strong></td>
                            <td>${row.po_number}</td>
                            <td>${row.receive_date}</td>
                            <td><span class="badge badge-success">${row.status}</span></td>
                        </tr>
                    `).join('');
                    document.querySelector('#grnTable tbody').innerHTML = rows || '<tr><td colspan="4">No receipt notes verified.</td></tr>';
                });

                // Load PO selects for GRN dropdown
                apiFetch('/purchase-orders').then(res => {
                    pendingPoCache = (res.data.data || []).filter(po => po.status === 'Pending' || po.status === 'Approved');
                    document.getElementById('grnPoSelect').innerHTML = `<option value="">Select PO...</option>` + pendingPoCache.map(po => `<option value="${po.id}">${po.po_number} (${po.supplier_name})</option>`).join('');
                });
            }

            if (tab === 'transfers') {
                apiFetch('/transfers').then(res => {
                    var rows = (res.data.data || []).map(row => {
                        var completeBtn = row.status === 'Pending' ? `<button class="btn btn-primary" style="padding:4px 8px; font-size:11px;" onclick="completeTransfer(${row.id})">Receive Stock</button>` : '';
                        return `
                            <tr>
                                <td><strong>${row.transfer_number}</strong></td>
                                <td>${row.from_warehouse_name}</td>
                                <td>${row.to_warehouse_name}</td>
                                <td>${row.transfer_date}</td>
                                <td><span class="badge ${row.status === 'Completed' ? 'badge-success' : 'badge-warning'}">${row.status}</span></td>
                                <td>
                                    ${completeBtn}
                                    <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;" onclick="deleteTransfer(${row.id})">Delete</button>
                                </td>
                            </tr>
                        `;
                    }).join('');
                    document.querySelector('#transfersTable tbody').innerHTML = rows || '<tr><td colspan="6">No transfers recorded.</td></tr>';
                });
            }

            if (tab === 'audits') {
                apiFetch('/audits').then(res => {
                    var rows = (res.data.data || []).map(row => {
                        var reconcileBtn = row.status === 'Pending' ? `<button class="btn btn-primary" style="padding:4px 8px; font-size:11px;" onclick="reconcileAudit(${row.id})">Finalize & Reconcile</button>` : '';
                        return `
                            <tr>
                                <td><strong>${row.audit_number}</strong></td>
                                <td>${row.warehouse_name}</td>
                                <td>${row.audit_date}</td>
                                <td><span class="badge ${row.status === 'Completed' ? 'badge-success' : 'badge-warning'}">${row.status}</span></td>
                                <td>${reconcileBtn}</td>
                            </tr>
                        `;
                    }).join('');
                    document.querySelector('#auditsTable tbody').innerHTML = rows || '<tr><td colspan="5">No audits scheduled.</td></tr>';
                });
            }

            if (tab === 'damaged') {
                apiFetch('/damaged-stock').then(res => {
                    var rows = (res.data.data || []).map(row => {
                        var dispositionControl = row.status === 'Reported' ? `
                            <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;" onclick="updateDamageStatus(${row.id}, 'Scrapped')">Scrap</button>
                            <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;" onclick="updateDamageStatus(${row.id}, 'Repaired')">Repair</button>
                        ` : '';
                        return `
                            <tr>
                                <td><strong>${row.sku}</strong> - ${row.product_name}</td>
                                <td>${row.warehouse_name}</td>
                                <td><strong>${row.quantity} Units</strong></td>
                                <td>${row.report_date}</td>
                                <td><span class="badge ${row.status === 'Repaired' ? 'badge-success' : 'badge-danger'}">${row.status}</span></td>
                                <td>${row.remarks || 'None'}</td>
                                <td>${dispositionControl}</td>
                            </tr>
                        `;
                    }).join('');
                    document.querySelector('#damagedTable tbody').innerHTML = rows || '<tr><td colspan="7">No damaged stock reports.</td></tr>';
                });
            }

            if (tab === 'suppliers') {
                apiFetch('/suppliers').then(res => {
                    var rows = (res.data.data || []).map(row => `
                        <tr>
                            <td><strong>${row.supplier_code}</strong></td>
                            <td>${row.supplier_name}</td>
                            <td>${row.contact_person || 'N/A'}</td>
                            <td>${row.mobile} <br> <span style="font-size:11px; color:var(--text-secondary);">${row.email}</span></td>
                            <td>${row.gst_number || 'N/A'}</td>
                            <td>⭐ <strong>${row.rating}</strong></td>
                        </tr>
                    `).join('');
                    document.querySelector('#suppliersTable tbody').innerHTML = rows || '<tr><td colspan="6">No suppliers registered.</td></tr>';
                });
            }

            if (tab === 'reports') {
                apiFetch('/reports/stock-valuation').then(res => {
                    var rows = (res.data || []).map(row => `
                        <tr>
                            <td><strong>${row.sku}</strong></td>
                            <td>${row.product_name}</td>
                            <td>${row.category}</td>
                            <td>${row.unit}</td>
                            <td>₹${parseFloat(row.purchase_price).toFixed(2)}</td>
                            <td>${row.total_available_stock}</td>
                            <td><strong>₹${parseFloat(row.total_valuation).toLocaleString('en-IN', {minimumFractionDigits:2})}</strong></td>
                        </tr>
                    `).join('');
                    document.querySelector('#reportValuationTable tbody').innerHTML = rows || '<tr><td colspan="7">No data.</td></tr>';
                });

                apiFetch('/reports/low-stock').then(res => {
                    var rows = (res.data || []).map(row => `
                        <tr style="background:rgba(234, 88, 12, 0.05);">
                            <td><strong>${row.sku}</strong></td>
                            <td>${row.product_name}</td>
                            <td>${row.minimum_stock}</td>
                            <td><strong style="color:var(--accent-warning);">${row.total_available_stock}</strong></td>
                        </tr>
                    `).join('');
                    document.querySelector('#reportLowStockTable tbody').innerHTML = rows || '<tr><td colspan="4">No low stock alerts!</td></tr>';
                });

                apiFetch('/reports/stock-movements').then(res => {
                    var rows = (res.data || []).map(row => `
                        <tr>
                            <td>${row.movement_date}</td>
                            <td><span class="badge ${row.movement_type === 'INWARD' ? 'badge-info' : 'badge-warning'}">${row.movement_type}</span></td>
                            <td><strong>${row.sku}</strong> - ${row.product_name}</td>
                            <td>${row.warehouse_name}</td>
                            <td><strong>${row.quantity}</strong></td>
                            <td><em>${row.reference_type} (${row.remarks || 'None'})</em></td>
                        </tr>
                    `).join('');
                    document.querySelector('#reportMovementsTable tbody').innerHTML = rows || '<tr><td colspan="6">No movements registered.</td></tr>';
                });

                apiFetch('/reports/audit-variances').then(res => {
                    var rows = (res.data || []).map(row => `
                        <tr>
                            <td><strong>${row.audit_number}</strong></td>
                            <td>${row.audit_date}</td>
                            <td>${row.warehouse_name}</td>
                            <td>${row.product_name} (${row.sku})</td>
                            <td>${row.system_quantity}</td>
                            <td>${row.physical_quantity}</td>
                            <td><strong style="color: ${row.variance < 0 ? 'var(--accent-danger)' : 'var(--accent-success)'};">${row.variance}</strong></td>
                        </tr>
                    `).join('');
                    document.querySelector('#reportAuditsTable tbody').innerHTML = rows || '<tr><td colspan="7">No reconciled audit variances logs.</td></tr>';
                });
            }

            if (tab === 'users') {
                apiFetch('/auth/users').then(res => {
                    var rows = (res.data || []).map(row => {
                        var statusClass = 'badge-success';
                        if (row.status === 'BLOCKED') statusClass = 'badge-danger';
                        if (row.status === 'PENDING') statusClass = 'badge-warning';

                        var actionBtns = `
                            <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;" onclick="updateUserApproval(${row.id}, 'APPROVED')">Approve</button>
                            <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;" onclick="updateUserApproval(${row.id}, 'BLOCKED')">Block</button>
                            <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px; background:var(--accent-danger); color:#fff;" onclick="deleteUser(${row.id})">Delete</button>
                        `;

                        return `
                            <tr>
                                <td><strong>${row.username}</strong></td>
                                <td>${row.name}</td>
                                <td>${row.email}</td>
                                <td>${row.role.replace('inventory_', '').toUpperCase()}</td>
                                <td><span class="badge ${statusClass}">${row.status}</span></td>
                                <td>${actionBtns}</td>
                            </tr>
                        `;
                    }).join('');
                    document.querySelector('#usersTable tbody').innerHTML = rows || '<tr><td colspan="6">No employees registered.</td></tr>';
                });
            }

            if (tab === 'settings') {
                apiFetch('/auth/smtp').then(res => {
                    var settings = res.data;
                    document.getElementById('smtpEnabled').value = settings.smtp_enabled;
                    document.getElementById('smtpHost').value = settings.smtp_host;
                    document.getElementById('smtpPort').value = settings.smtp_port;
                    document.getElementById('smtpUsername').value = settings.smtp_username;
                    document.getElementById('smtpPassword').value = '******';
                    document.getElementById('smtpEncryption').value = settings.smtp_encryption;
                    document.getElementById('smtpFromEmail').value = settings.smtp_from_email;
                    document.getElementById('smtpFromName').value = settings.smtp_from_name;
                });
            }
        }

        // Submitting forms
        document.getElementById('productForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var payload = {
                sku: document.getElementById('pSku').value,
                barcode: document.getElementById('pBarcode').value,
                product_name: document.getElementById('pName').value,
                category: document.getElementById('pCategory').value,
                brand: document.getElementById('pBrand').value,
                unit: document.getElementById('pUnit').value,
                purchase_price: document.getElementById('pBuyPrice').value,
                selling_price: document.getElementById('pSellPrice').value,
                minimum_stock: document.getElementById('pMinStock').value,
                maximum_stock: document.getElementById('pMaxStock').value
            };

            apiFetch('/products', {
                method: 'POST',
                body: JSON.stringify(payload)
            }).then(() => {
                showToast('Product profile created successfully.', 'success');
                closeModal('productModal');
                document.getElementById('productForm').reset();
                apiFetch('/products').then(res => {
                    productsCache = res.data.data || [];
                    loadTabContent('products');
                });
            }).catch(err => showToast(err.message, 'error'));
        });

        document.getElementById('warehouseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var payload = {
                warehouse_code: document.getElementById('wCode').value,
                warehouse_name: document.getElementById('wName').value,
                location: document.getElementById('wLocation').value,
                manager_name: document.getElementById('wManager').value,
                contact_number: document.getElementById('wContact').value,
                capacity: document.getElementById('wCapacity').value
            };

            apiFetch('/warehouses', {
                method: 'POST',
                body: JSON.stringify(payload)
            }).then(() => {
                showToast('Warehouse registered successfully.', 'success');
                closeModal('warehouseModal');
                document.getElementById('warehouseForm').reset();
                apiFetch('/warehouses').then(res => {
                    warehousesCache = res.data.data || [];
                    loadTabContent('warehouses');
                });
            }).catch(err => showToast(err.message, 'error'));
        });

        document.getElementById('stockAdjustForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var payload = {
                product_id: document.getElementById('adjProductSelect').value,
                warehouse_id: document.getElementById('adjWarehouseSelect').value,
                available_stock: document.getElementById('adjAvailable').value,
                reserved_stock: document.getElementById('adjReserved').value,
                damaged_stock: document.getElementById('adjDamaged').value
            };

            apiFetch('/inventory', {
                method: 'POST',
                body: JSON.stringify(payload)
            }).then(() => {
                showToast('Stock levels manually adjusted.', 'success');
                closeModal('stockAdjustModal');
                loadTabContent('stock');
            }).catch(err => showToast(err.message, 'error'));
        });

        document.getElementById('inwardForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var items = [];
            var prods = document.querySelectorAll('.in-prod');
            var whs = document.querySelectorAll('.in-wh');
            var qtys = document.querySelectorAll('.in-qty');
            var batches = document.querySelectorAll('.in-batch');

            for (var i = 0; i < prods.length; i++) {
                if (prods[i].value && qtys[i].value) {
                    items.push({
                        product_id: prods[i].value,
                        warehouse_id: whs[i].value,
                        quantity: qtys[i].value,
                        batch_number: batches[i].value
                    });
                }
            }

            var payload = {
                inward_date: document.getElementById('inDate').value,
                reference_type: document.getElementById('inRef').value || 'Manual Receipt',
                remarks: document.getElementById('inRemarks').value,
                items: items
            };

            apiFetch('/stock-inward', {
                method: 'POST',
                body: JSON.stringify(payload)
            }).then(() => {
                showToast('Stock inward receipt logged.', 'success');
                closeModal('inwardModal');
                document.getElementById('inwardForm').reset();
                document.querySelector('#inwardLinesTable tbody').innerHTML = `
                    <tr>
                        <td><select class="form-select in-prod" required>${getProductOptions()}</select></td>
                        <td><select class="form-select in-wh" required>${getWarehouseOptions()}</select></td>
                        <td><input type="number" class="form-input in-qty" required style="width:80px;"></td>
                        <td><input type="text" class="form-input in-batch" style="width:100px;"></td>
                    </tr>
                `;
                loadTabContent('inward-outward');
            }).catch(err => showToast(err.message, 'error'));
        });

        document.getElementById('outwardForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var items = [];
            var prods = document.querySelectorAll('.out-prod');
            var whs = document.querySelectorAll('.out-wh');
            var qtys = document.querySelectorAll('.out-qty');

            for (var i = 0; i < prods.length; i++) {
                if (prods[i].value && qtys[i].value) {
                    items.push({
                        product_id: prods[i].value,
                        warehouse_id: whs[i].value,
                        quantity: qtys[i].value
                    });
                }
            }

            var payload = {
                outward_date: document.getElementById('outDate').value,
                reference_type: document.getElementById('outRef').value || 'Manual Issue',
                remarks: document.getElementById('outRemarks').value,
                items: items
            };

            apiFetch('/stock-outward', {
                method: 'POST',
                body: JSON.stringify(payload)
            }).then(() => {
                showToast('Stock outward issue logged.', 'success');
                closeModal('outwardModal');
                document.getElementById('outwardForm').reset();
                document.querySelector('#outwardLinesTable tbody').innerHTML = `
                    <tr>
                        <td><select class="form-select out-prod" required>${getProductOptions()}</select></td>
                        <td><select class="form-select out-wh" required>${getWarehouseOptions()}</select></td>
                        <td><input type="number" class="form-input out-qty" required style="width:80px;"></td>
                    </tr>
                `;
                loadTabContent('inward-outward');
            }).catch(err => showToast(err.message, 'error'));
        });

        document.getElementById('poForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var items = [];
            var prods = document.querySelectorAll('.po-prod');
            var qtys = document.querySelectorAll('.po-qty');
            var prices = document.querySelectorAll('.po-price');

            for (var i = 0; i < prods.length; i++) {
                if (prods[i].value && qtys[i].value) {
                    items.push({
                        product_id: prods[i].value,
                        quantity: qtys[i].value,
                        price: prices[i].value || undefined
                    });
                }
            }

            var payload = {
                supplier_id: document.getElementById('poSupplierSelect').value,
                order_date: document.getElementById('poDate').value,
                items: items
            };

            apiFetch('/purchase-orders', {
                method: 'POST',
                body: JSON.stringify(payload)
            }).then(() => {
                showToast('Purchase order created successfully.', 'success');
                closeModal('poModal');
                document.getElementById('poForm').reset();
                document.querySelector('#poLinesTable tbody').innerHTML = `
                    <tr>
                        <td><select class="form-select po-prod" required>${getProductOptions()}</select></td>
                        <td><input type="number" class="form-input po-qty" required style="width:80px;"></td>
                        <td><input type="number" step="0.01" class="form-input po-price" style="width:100px;"></td>
                    </tr>
                `;
                loadTabContent('purchase-orders');
            }).catch(err => showToast(err.message, 'error'));
        });

        // Load items list when selecting PO for GRN
        function loadPoItemsForGrn() {
            var poId = document.getElementById('grnPoSelect').value;
            if (!poId) {
                document.querySelector('#grnItemsChecklist tbody').innerHTML = '';
                return;
            }

            apiFetch('/purchase-orders/' + poId).then(res => {
                var items = res.data.items || [];
                var rows = items.map(item => `
                    <tr>
                        <td>
                            <strong>${item.sku}</strong> - ${item.product_name}
                            <input type="hidden" class="grn-prod-id" value="${item.product_id}">
                        </td>
                        <td>${item.quantity} ${item.unit}</td>
                        <td>
                            <input type="number" class="form-input grn-recv-qty" required value="${item.quantity}" style="width:100px;">
                        </td>
                    </tr>
                `).join('');
                document.querySelector('#grnItemsChecklist tbody').innerHTML = rows;
            });
        }

        document.getElementById('grnForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var items = [];
            var ids = document.querySelectorAll('.grn-prod-id');
            var qtys = document.querySelectorAll('.grn-recv-qty');

            for (var i = 0; i < ids.length; i++) {
                items.push({
                    product_id: ids[i].value,
                    quantity_received: qtys[i].value
                });
            }

            var payload = {
                po_id: document.getElementById('grnPoSelect').value,
                warehouse_id: document.getElementById('grnWarehouseSelect').value,
                receive_date: document.getElementById('grnDate').value,
                items: items
            };

            apiFetch('/grn', {
                method: 'POST',
                body: JSON.stringify(payload)
            }).then(() => {
                showToast('Goods Receipt note created. Inventory updated.', 'success');
                closeModal('grnModal');
                document.getElementById('grnForm').reset();
                document.querySelector('#grnItemsChecklist tbody').innerHTML = '';
                loadTabContent('grn');
            }).catch(err => showToast(err.message, 'error'));
        });

        document.getElementById('transferForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var items = [];
            var prods = document.querySelectorAll('.t-prod');
            var qtys = document.querySelectorAll('.t-qty');

            for (var i = 0; i < prods.length; i++) {
                if (prods[i].value && qtys[i].value) {
                    items.push({
                        product_id: prods[i].value,
                        quantity: qtys[i].value
                    });
                }
            }

            var payload = {
                from_warehouse_id: document.getElementById('tFromWh').value,
                to_warehouse_id: document.getElementById('tToWh').value,
                transfer_date: document.getElementById('tDate').value,
                status: document.getElementById('tStatus').value,
                items: items
            };

            apiFetch('/transfers', {
                method: 'POST',
                body: JSON.stringify(payload)
            }).then(() => {
                showToast('Warehouse stock transfer scheduled.', 'success');
                closeModal('transferModal');
                document.getElementById('transferForm').reset();
                document.querySelector('#transferLinesTable tbody').innerHTML = `
                    <tr>
                        <td><select class="form-select t-prod" required>${getProductOptions()}</select></td>
                        <td><input type="number" class="form-input t-qty" required style="width:100px;"></td>
                    </tr>
                `;
                loadTabContent('transfers');
            }).catch(err => showToast(err.message, 'error'));
        });

        // Load products list for Audit
        function loadProductsForAudit() {
            var rows = productsCache.map(p => `
                <tr>
                    <td>
                        <strong>${p.sku}</strong> - ${p.product_name}
                        <input type="hidden" class="audit-prod-id" value="${p.id}">
                    </td>
                    <td>
                        <input type="number" class="form-input audit-phys-qty" required placeholder="Counted qty" style="width:120px;">
                    </td>
                </tr>
            `).join('');
            document.querySelector('#auditItemsChecklist tbody').innerHTML = rows;
        }

        document.getElementById('auditForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var items = [];
            var ids = document.querySelectorAll('.audit-prod-id');
            var qtys = document.querySelectorAll('.audit-phys-qty');

            for (var i = 0; i < ids.length; i++) {
                if (qtys[i].value !== '') {
                    items.push({
                        product_id: ids[i].value,
                        physical_quantity: qtys[i].value
                    });
                }
            }

            var payload = {
                warehouse_id: document.getElementById('aWarehouseSelect').value,
                audit_date: document.getElementById('aDate').value,
                items: items
            };

            apiFetch('/audits', {
                method: 'POST',
                body: JSON.stringify(payload)
            }).then(() => {
                showToast('Physical audit logged successfully.', 'success');
                closeModal('auditModal');
                document.getElementById('auditForm').reset();
                document.querySelector('#auditItemsChecklist tbody').innerHTML = '';
                loadTabContent('audits');
            }).catch(err => showToast(err.message, 'error'));
        });

        document.getElementById('damageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var payload = {
                product_id: document.getElementById('dProduct').value,
                warehouse_id: document.getElementById('dWarehouse').value,
                quantity: document.getElementById('dQty').value,
                report_date: document.getElementById('dDate').value,
                remarks: document.getElementById('dRemarks').value
            };

            apiFetch('/damaged-stock', {
                method: 'POST',
                body: JSON.stringify(payload)
            }).then(() => {
                showToast('Damaged stock logged.', 'success');
                closeModal('damageModal');
                document.getElementById('damageForm').reset();
                loadTabContent('damaged');
            }).catch(err => showToast(err.message, 'error'));
        });

        document.getElementById('supplierForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var payload = {
                supplier_name: document.getElementById('sName').value,
                contact_person: document.getElementById('sPerson').value,
                mobile: document.getElementById('sMobile').value,
                email: document.getElementById('sEmail').value,
                gst_number: document.getElementById('sGst').value,
                address: document.getElementById('sAddress').value
            };

            apiFetch('/suppliers', {
                method: 'POST',
                body: JSON.stringify(payload)
            }).then(() => {
                showToast('Supplier registered successfully.', 'success');
                closeModal('supplierModal');
                document.getElementById('supplierForm').reset();
                apiFetch('/suppliers').then(res => {
                    suppliersCache = res.data.data || [];
                    loadTabContent('suppliers');
                });
            }).catch(err => showToast(err.message, 'error'));
        });

        // Barcode / QR actions
        document.getElementById('labelForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var pid = document.getElementById('labelProductSelect').value;
            var type = document.getElementById('labelTypeSelect').value;
            var path = '/' + type + '/generate';

            apiFetch(path, {
                method: 'POST',
                body: JSON.stringify({ product_id: pid })
            }).then(res => {
                document.getElementById('labelProductTitle').textContent = res.data.product_name + ' (' + res.data.sku + ')';
                document.getElementById('labelPreviewImg').src = res.data.label_url;
                document.getElementById('labelPreviewArea').style.display = 'block';
                showToast('Label details loaded.', 'success');
            }).catch(err => showToast(err.message, 'error'));
        });

        document.getElementById('scanLookupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var code = document.getElementById('scanText').value;
            // Detect if barcode (digits) or QR (string SKU)
            var type = isNaN(code.replace('INV-SKU-', '')) ? 'qrcode' : 'barcode';
            var path = '/' + type + '/' + encodeURIComponent(code);

            apiFetch(path).then(res => {
                var p = res.data;
                document.getElementById('scanResultDetails').innerHTML = `
                    <strong>SKU:</strong> ${p.sku}<br>
                    <strong>Product:</strong> ${p.product_name}<br>
                    <strong>Category:</strong> ${p.category}<br>
                    <strong>Prices:</strong> Buying: ₹${p.purchase_price} | Selling: ₹${p.selling_price}<br>
                    <strong>Alert levels:</strong> Min: ${p.minimum_stock} | Max: ${p.maximum_stock}
                `;
                document.getElementById('scanResultArea').style.display = 'block';
                showToast('Product record resolved.', 'success');
            }).catch(err => {
                document.getElementById('scanResultDetails').innerHTML = `<span style="color:var(--accent-danger); font-weight:600;">Error: ${err.message}</span>`;
                document.getElementById('scanResultArea').style.display = 'block';
                showToast(err.message, 'error');
            });
        });

        // SMTP settings and test SMTP forms
        document.getElementById('smtpSettingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var payload = {
                smtp_enabled: document.getElementById('smtpEnabled').value,
                smtp_host: document.getElementById('smtpHost').value,
                smtp_port: document.getElementById('smtpPort').value,
                smtp_username: document.getElementById('smtpUsername').value,
                smtp_password: document.getElementById('smtpPassword').value,
                smtp_encryption: document.getElementById('smtpEncryption').value,
                smtp_from_email: document.getElementById('smtpFromEmail').value,
                smtp_from_name: document.getElementById('smtpFromName').value
            };

            apiFetch('/auth/smtp', {
                method: 'POST',
                body: JSON.stringify(payload)
            }).then(() => {
                showToast('SMTP configuration updated successfully.', 'success');
            }).catch(err => showToast(err.message, 'error'));
        });

        document.getElementById('smtpTestForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var email = document.getElementById('smtpTestEmail').value;

            apiFetch('/auth/smtp/test', {
                method: 'POST',
                body: JSON.stringify({ test_email: email })
            }).then(() => {
                showToast('Test email dispatched. Check SMTP logs.', 'success');
            }).catch(err => showToast(err.message, 'error'));
        });

        // Modular action handlers
        function deleteProduct(id) {
            if (confirm('Delete this product permanently?')) {
                apiFetch('/products/' + id, { method: 'DELETE' }).then(() => {
                    showToast('Product soft-deleted.', 'success');
                    apiFetch('/products').then(res => {
                        productsCache = res.data.data || [];
                        loadTabContent('products');
                    });
                }).catch(err => showToast(err.message, 'error'));
            }
        }

        function updatePoStatus(id, status) {
            apiFetch('/purchase-orders/' + id, {
                method: 'PUT',
                body: JSON.stringify({ status: status })
            }).then(() => {
                showToast('Purchase Order status updated to ' + status, 'success');
                loadTabContent('purchase-orders');
            }).catch(err => showToast(err.message, 'error'));
        }

        function deletePo(id) {
            if (confirm('Delete this purchase order?')) {
                apiFetch('/purchase-orders/' + id, { method: 'DELETE' }).then(() => {
                    showToast('Purchase order deleted.', 'success');
                    loadTabContent('purchase-orders');
                }).catch(err => showToast(err.message, 'error'));
            }
        }

        function completeTransfer(id) {
            apiFetch('/transfers/' + id + '/status', {
                method: 'PUT',
                body: JSON.stringify({ status: 'Completed' })
            }).then(() => {
                showToast('Transfer stocks reconciled and received.', 'success');
                loadTabContent('transfers');
            }).catch(err => showToast(err.message, 'error'));
        }

        function deleteTransfer(id) {
            if (confirm('Delete this transfer log?')) {
                apiFetch('/transfers/' + id, { method: 'DELETE' }).then(() => {
                    showToast('Transfer record deleted.', 'success');
                    loadTabContent('transfers');
                }).catch(err => showToast(err.message, 'error'));
            }
        }

        function reconcileAudit(id) {
            apiFetch('/audits/' + id, {
                method: 'PUT',
                body: JSON.stringify({ status: 'Completed' })
            }).then(() => {
                showToast('Inventory stocks reconciled based on audit variances.', 'success');
                loadTabContent('audits');
            }).catch(err => showToast(err.message, 'error'));
        }

        function updateDamageStatus(id, status) {
            apiFetch('/damaged-stock/' + id, {
                method: 'PUT',
                body: JSON.stringify({ status: status })
            }).then(() => {
                showToast('Stock damage disposition updated to ' + status, 'success');
                loadTabContent('damaged');
            }).catch(err => showToast(err.message, 'error'));
        }

        function updateUserApproval(uid, status) {
            apiFetch('/auth/users/status', {
                method: 'POST',
                body: JSON.stringify({ user_id: uid, status: status })
            }).then(() => {
                showToast('User state updated successfully.', 'success');
                loadTabContent('users');
            }).catch(err => showToast(err.message, 'error'));
        }

        function deleteUser(uid) {
            if (confirm('Permanently delete user profile?')) {
                apiFetch('/auth/users/' + uid, { method: 'DELETE' }).then(() => {
                    showToast('User deleted successfully.', 'success');
                    loadTabContent('users');
                }).catch(err => showToast(err.message, 'error'));
            }
        }

        // Initialize on page load
        window.addEventListener('DOMContentLoaded', () => {
            var token = localStorage.getItem('inv_auth_token');
            var user = localStorage.getItem('inv_current_user');
            
            if (token && user) {
                document.getElementById('authSection').style.display = 'none';
                document.getElementById('appSection').style.display = 'flex';
                initApp();
            } else {
                document.getElementById('authSection').style.display = 'flex';
                document.getElementById('appSection').style.display = 'none';
            }
        });
    </script>
</body>
</html>
