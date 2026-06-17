<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Jewellery ERP & Shop Management Portal">
    <title>Jewellery ERP Management Portal</title>
    <!-- Modern Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg-primary: #060714;
            --bg-surface: #0b0e26;
            --bg-glass: rgba(11, 14, 38, 0.85);
            --border-glass: rgba(255, 215, 0, 0.08);
            --primary: #ffd700; /* Metallic Gold */
            --primary-hover: #e6c200;
            --accent: #b8860b; /* Goldenrod */
            --danger: #ff4d6d;
            --warning: #ffb703;
            --success: #4ade80;
            --text: #f3f4f6;
            --text-muted: #9ca3af;
            --sidebar-width: 260px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
            scrollbar-width: thin;
            scrollbar-color: var(--primary) var(--bg-surface);
        }

        *::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        *::-webkit-scrollbar-track {
            background: var(--bg-surface);
        }
        *::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }

        body {
            background: var(--bg-primary);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* PAGE LOADER */
        #page-loader {
            position: fixed;
            inset: 0;
            background: var(--bg-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            flex-direction: column;
            gap: 20px;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid var(--border-glass);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* AUTH SCREEN */
        #login-view {
            min-height: 100vh;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: radial-gradient(circle at 10% 20%, rgba(255, 215, 0, 0.1) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(184, 134, 11, 0.05) 0%, transparent 40%);
        }

        .auth-card {
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            backdrop-filter: blur(20px);
            padding: 40px;
            border-radius: 24px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .auth-header h2 {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #fff 0%, var(--primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }

        .auth-header p {
            color: var(--text-muted);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
        }

        .form-input {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border-glass);
            padding: 12px 16px;
            border-radius: 12px;
            color: #fff;
            font-size: 15px;
            outline: none;
            transition: var(--transition);
        }

        .form-input:focus {
            border-color: var(--primary);
            background: rgba(255,255,255,0.08);
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.2);
        }

        .btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border: none;
            color: #000;
            font-weight: 700;
            padding: 14px;
            border-radius: 12px;
            font-size: 16px;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 215, 0, 0.4);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .preset-badge {
            display: inline-block;
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--border-glass);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 11px;
            margin-right: 8px;
            margin-top: 8px;
            cursor: pointer;
            color: var(--text-muted);
            transition: var(--transition);
        }

        .preset-badge:hover {
            background: rgba(255, 215, 0, 0.15);
            border-color: var(--primary);
            color: #fff;
        }

        /* PORTAL SHELL */
        #dashboard-shell {
            display: none;
            min-height: 100vh;
        }

        /* SIDEBAR */
        aside {
            width: var(--sidebar-width);
            background: var(--bg-surface);
            border-right: 1px solid var(--border-glass);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
        }

        .brand-section {
            padding: 24px;
            border-bottom: 1px solid var(--border-glass);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: #000;
        }

        .brand-name {
            font-weight: 700;
            font-size: 18px;
            color: #fff;
            letter-spacing: 0.5px;
        }

        nav {
            padding: 20px 12px;
            flex: 1;
            overflow-y: auto;
        }

        .nav-category {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin: 15px 12px 8px;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 4px;
            cursor: pointer;
            transition: var(--transition);
        }

        .sidebar-item:hover, .sidebar-item.active {
            color: #fff;
            background: rgba(255, 215, 0, 0.1);
        }

        .sidebar-item.active {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.15) 0%, rgba(184, 134, 11, 0.05) 100%);
            border-left: 3px solid var(--primary);
        }

        .user-profile-section {
            padding: 20px;
            border-top: 1px solid var(--border-glass);
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255,255,255,0.02);
        }

        .avatar {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #000;
        }

        .user-info {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: capitalize;
        }

        #btn-logout {
            background: rgba(255, 77, 109, 0.1);
            border: 1px solid rgba(255, 77, 109, 0.2);
            color: var(--danger);
            cursor: pointer;
            padding: 8px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        #btn-logout:hover {
            background: var(--danger);
            color: #fff;
            box-shadow: 0 0 12px rgba(255, 77, 109, 0.4);
            transform: scale(1.05);
        }

        #btn-logout svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* MAIN CONTENT AREA */
        main {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 30px;
            min-width: 0;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-glass);
            padding-bottom: 20px;
        }

        .header-left h1 {
            font-size: 26px;
            font-weight: 800;
            color: #fff;
        }

        .header-left p {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 4px;
        }

        /* TABS VIEWS Panels */
        .tab-panel {
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .tab-panel.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* KPI Cards Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .kpi-card {
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            padding: 24px;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .kpi-title {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: var(--text-muted);
        }

        .kpi-value {
            font-size: 28px;
            font-weight: 800;
            color: #fff;
        }

        .kpi-detail {
            font-size: 12px;
            color: var(--primary);
        }

        /* Forms Layout Grid */
        .gmt-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .gmt-grid {
                grid-template-columns: 1fr;
            }
        }

        .dashboard-content-panel {
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 30px;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .panel-header h3 {
            font-size: 18px;
            font-weight: 700;
        }

        /* Tables and CRUD lists */
        .data-table-card {
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 30px;
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .data-table th, .data-table td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-glass);
            font-size: 14px;
        }

        .data-table th {
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }

        .data-table tr:hover td {
            background: rgba(255,255,255,0.01);
        }

        /* MODAL DIALOGS */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-glass);
            padding: 30px;
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            animation: modalScale 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
        }

        @keyframes modalScale {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .modal-close {
            float: right;
            cursor: pointer;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-size: 18px;
        }

        /* Alerts banner service */
        #toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast {
            background: var(--bg-surface);
            border-left: 4px solid var(--primary);
            border-right: 1px solid var(--border-glass);
            border-top: 1px solid var(--border-glass);
            border-bottom: 1px solid var(--border-glass);
            padding: 16px 24px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            font-size: 14px;
            font-weight: 500;
            min-width: 280px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: toastIn 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }

        @keyframes toastIn {
            from { transform: translateX(100px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .toast.success { border-left-color: var(--success); }
        .toast.error { border-left-color: var(--danger); }
        .toast.warning { border-left-color: var(--warning); }

        /* Badge status indicators */
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-badge.active { background: rgba(74, 222, 128, 0.15); color: var(--success); }
        .status-badge.pending { background: rgba(255, 183, 3, 0.15); color: var(--warning); }
        .status-badge.completed { background: rgba(74, 222, 128, 0.15); color: var(--success); }
        .status-badge.in-progress { background: rgba(255, 215, 0, 0.15); color: var(--primary); }
        .status-badge.cancelled { background: rgba(255, 77, 109, 0.15); color: var(--danger); }
        .status-badge.sold { background: rgba(255,255,255,0.08); color: var(--text-muted); }

        .btn-action {
            background: var(--primary);
            color: #000;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        .btn-action:hover {
            background: var(--primary-hover);
        }
    </style>
</head>
<body>

    <!-- PAGE LOADER -->
    <div id="page-loader">
        <div class="spinner"></div>
        <p style="color: var(--text-muted); font-size:14px; font-weight:600; letter-spacing:0.5px;">Authenticating ERP Session...</p>
    </div>

    <!-- TOAST CONTAINER -->
    <div id="toast-container"></div>

    <!-- AUTHENTICATION VIEW -->
    <div id="login-view">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Jewellery ERP Portal</h2>
                <p>Enterprise Bullion & Ornaments Coordinator</p>
            </div>
            
            <form id="login-form">
                <div class="form-group">
                    <label>Username / Email Address</label>
                    <input type="text" class="form-input" id="login-username" placeholder="Enter username or email" required>
                </div>
                
                <div class="form-group" id="login-pass-group">
                    <label>Account Password</label>
                    <input type="password" class="form-input" id="login-password" placeholder="••••••••">
                </div>

                <div class="form-group" id="login-otp-group" style="display: none;">
                    <label>6-Digit Verification OTP</label>
                    <input type="text" class="form-input" id="login-otp" placeholder="123456" maxlength="6">
                </div>

                <button type="submit" class="btn" id="btn-login-submit" style="margin-bottom: 12px;">Login with Password</button>
                <button type="button" class="btn" id="btn-send-otp" style="background: transparent; border: 1px solid var(--border-glass); color: var(--text-muted);">Request OTP Code</button>
            </form>

            <div style="margin-top: 25px; border-top: 1px solid var(--border-glass); padding-top: 20px;">
                <p style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">Click to login with Test operator badges:</p>
                <div style="margin-top: 5px;">
                    <span class="preset-badge" onclick="fillPreset('jewelsuperadmin', '123456')">Super Admin</span>
                    <span class="preset-badge" onclick="fillPreset('jwl_manager', 'managerpass123')">Store Mgr</span>
                    <span class="preset-badge" onclick="fillPreset('jwl_sales', 'salespass123')">Sales Exec</span>
                    <span class="preset-badge" onclick="fillPreset('jwl_supervisor', 'supervisorpass123')">Karigar Supervisor</span>
                    <span class="preset-badge" onclick="fillPreset('jwl_accountant', 'accountpass123')">Accountant</span>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN SHELL PORTAL -->
    <div id="dashboard-shell">
        <aside>
            <div class="brand-section">
                <div class="brand-logo">JWL</div>
                <div class="brand-name">Jewellery ERP</div>
            </div>
            
            <nav>
                <div class="nav-category">Main Operations</div>
                <div class="sidebar-item active" data-tab="dashboard-overview">Dashboard Overview</div>
                <div class="sidebar-item" data-tab="sales-billing">Sales & Billing Terminal</div>
                <div class="sidebar-item" data-tab="bullion-stock">Bullion & Jewellery Stock</div>
                <div class="sidebar-item" data-tab="karigar-work">Karigar & Job Work</div>
                <div class="sidebar-item" data-tab="repairs-custom">Repair & Custom Bookings</div>
                <div class="sidebar-item" data-tab="buybacks-exchange">Buyback & Exchange</div>
                <div class="sidebar-item" data-tab="diamonds-audit">Diamond & Audit Logs</div>

                <div class="nav-category">System Admin</div>
                <div class="sidebar-item admin-only" data-tab="diagnostics-users" style="display: none;">Diagnostics & Users</div>
            </nav>

            <div class="user-profile-section">
                <div class="avatar" id="user-avatar-initials">JS</div>
                <div class="user-info">
                    <div class="user-name" id="user-display-name">Super Admin</div>
                    <div class="user-role" id="user-display-role">super_admin</div>
                </div>
                <button id="btn-logout" title="Log Out">
                    <svg viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
                    </svg>
                </button>
            </div>
        </aside>

        <main>
            <header>
                <div class="header-left">
                    <h1 id="current-tab-title">Dashboard Overview</h1>
                    <p>Live Gold, Silver Bullion and Finished Ornaments System</p>
                </div>
                <div class="header-right">
                    <span style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-glass); padding: 8px 16px; border-radius: 8px; font-size:13px; font-weight:600;">
                        Live Clock: <span id="clock-display">00:00:00</span>
                    </span>
                </div>
            </header>

            <!-- TABS PANELS LIST -->
            <!-- 1. DASHBOARD OVERVIEW -->
            <div class="tab-panel active" id="tab-dashboard-overview">
                <div class="kpi-grid">
                    <div class="kpi-card">
                        <span class="kpi-title">Gold Stock (Raw)</span>
                        <span class="kpi-value" id="kpi-gold-stock">0g</span>
                        <span class="kpi-detail">Main Vault</span>
                    </div>
                    <div class="kpi-card">
                        <span class="kpi-title">Silver Stock (Raw)</span>
                        <span class="kpi-value" id="kpi-silver-stock">0g</span>
                        <span class="kpi-detail">Main Vault</span>
                    </div>
                    <div class="kpi-card">
                        <span class="kpi-title">Daily Sales</span>
                        <span class="kpi-value" id="kpi-daily-sales">₹0</span>
                        <span class="kpi-detail">Completed Today</span>
                    </div>
                    <div class="kpi-card">
                        <span class="kpi-title">Active Karigars</span>
                        <span class="kpi-value" id="kpi-active-karigars">0</span>
                        <span class="kpi-detail">Assigned Workshops</span>
                    </div>
                </div>

                <div class="gmt-grid">
                    <div class="dashboard-content-panel">
                        <div class="panel-header">
                            <h3>Sales Distribution by Metal Category</h3>
                        </div>
                        <canvas id="sales-chart" style="max-height: 250px; width:100%;"></canvas>
                    </div>
                    <div class="dashboard-content-panel">
                        <div class="panel-header">
                            <h3>Pending Status Alerts</h3>
                        </div>
                        <div style="display:flex; flex-direction:column; gap:12px;">
                            <div style="background:rgba(255, 215, 0, 0.1); border:1px solid rgba(255, 215, 0, 0.2); padding:15px; border-radius:12px; display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <h4 style="color:var(--primary); font-size:14px; font-weight:700;">Active Repair Orders</h4>
                                    <p style="font-size:12px; color:var(--text-muted); margin-top:2px;">In repair cycle</p>
                                </div>
                                <span style="background:var(--primary); color:#000; font-weight:800; font-size:12px; padding:4px 8px; border-radius:6px;" id="badge-active-repairs">0</span>
                            </div>
                            
                            <div style="background:rgba(74, 222, 128, 0.1); border:1px solid rgba(74, 222, 128, 0.2); padding:15px; border-radius:12px; display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <h4 style="color:var(--success); font-size:14px; font-weight:700;">Custom Bookings</h4>
                                    <p style="font-size:12px; color:var(--text-muted); margin-top:2px;">Awaiting completions</p>
                                </div>
                                <span style="background:var(--success); color:#000; font-weight:800; font-size:12px; padding:4px 8px; border-radius:6px;" id="badge-active-custom">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. SALES & BILLING TERMINAL -->
            <div class="tab-panel" id="tab-sales-billing">
                <div class="panel-header">
                    <h3>GST Invoicing Terminal</h3>
                </div>

                <div class="gmt-grid" style="grid-template-columns: 1fr;">
                    <div class="dashboard-content-panel">
                        <form id="form-create-bill">
                            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:20px; margin-bottom:20px;">
                                <div class="form-group">
                                    <label>Invoice Number</label>
                                    <input type="text" class="form-input" id="bill-invoice-number" placeholder="Leave empty for auto-generate">
                                </div>
                                <div class="form-group">
                                    <label>Select Customer</label>
                                    <select class="form-input" id="bill-customer-id" style="background:#0b0e26; color:#fff;" required>
                                        <option value="">-- Choose Customer --</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Select Finished Ornament</label>
                                    <select class="form-input" id="bill-product-id" style="background:#0b0e26; color:#fff;" required>
                                        <option value="">-- Select Active Item --</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Gross Weight (Grams)</label>
                                    <input type="number" step="0.001" class="form-input" id="bill-gross-weight" readonly>
                                </div>
                            </div>

                            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:20px; margin-bottom:20px;">
                                <div class="form-group">
                                    <label>Net Weight (Grams)</label>
                                    <input type="number" step="0.001" class="form-input" id="bill-net-weight" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Gold Rate per Gram (22K)</label>
                                    <input type="number" step="0.01" class="form-input" id="bill-gold-rate" value="6250.00" oninput="calculateBillTotals()">
                                </div>
                                <div class="form-group">
                                    <label>Silver Rate per Gram (925)</label>
                                    <input type="number" step="0.01" class="form-input" id="bill-silver-rate" value="75.00" oninput="calculateBillTotals()">
                                </div>
                                <div class="form-group">
                                    <label>Making Charges per Gram / Flat</label>
                                    <input type="number" step="0.01" class="form-input" id="bill-making-charges" placeholder="Calculated from item" oninput="calculateBillTotals()">
                                </div>
                            </div>

                            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:20px; margin-bottom:20px;">
                                <div class="form-group">
                                    <label>Stone Charges (Flat)</label>
                                    <input type="number" step="0.01" class="form-input" id="bill-stone-charges" value="0" oninput="calculateBillTotals()">
                                </div>
                                <div class="form-group">
                                    <label>Discount Amount (₹)</label>
                                    <input type="number" step="0.01" class="form-input" id="bill-discount" value="0" oninput="calculateBillTotals()">
                                </div>
                                <div class="form-group">
                                    <label>Payment Method</label>
                                    <select class="form-input" id="bill-payment-method" style="background:#0b0e26; color:#fff;">
                                        <option value="CASH">Cash</option>
                                        <option value="UPI">UPI / Digital</option>
                                        <option value="CARD">Credit/Debit Card</option>
                                        <option value="EXCHANGE">Old Exchange</option>
                                    </select>
                                </div>
                            </div>

                            <div style="background:rgba(255,255,255,0.02); border:1px solid var(--border-glass); padding:20px; border-radius:12px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <h4 style="font-size:13px; color:var(--text-muted); text-transform:uppercase;">Invoice Costing Summary</h4>
                                    <p style="font-size:24px; font-weight:800; color:var(--primary); margin-top:5px;" id="bill-total-text">₹0.00</p>
                                </div>
                                <div style="text-align:right; font-size:13px; color:var(--text-muted);">
                                    <div>Subtotal: <span id="bill-subtotal-text">₹0.00</span></div>
                                    <div style="margin-top:2px;">GST (3%): <span id="bill-gst-text">₹0.00</span></div>
                                </div>
                            </div>

                            <button type="submit" class="btn">Generate & Print GST Invoice</button>
                        </form>
                    </div>
                </div>

                <div class="data-table-card">
                    <h4 style="margin-bottom: 15px;">Generated Sales Invoices</h4>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Invoice Number</th>
                                <th>Date</th>
                                <th>Gross Weight</th>
                                <th>Net Weight</th>
                                <th>GST (3%)</th>
                                <th>Total Bill Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="billing-list-tbody"></tbody>
                    </table>
                </div>
            </div>

            <!-- 3. BULLION & JEWELLERY STOCK -->
            <div class="tab-panel" id="tab-bullion-stock">
                <div class="panel-header">
                    <h3>Metal Bullion & Finished Ornaments</h3>
                    <div style="display:flex; gap:10px;">
                        <button class="btn-action" onclick="openModal('modal-create-bullion')">+ Add Raw Metal</button>
                        <button class="btn-action" onclick="openModal('modal-create-ornament')">+ Add Finished Item</button>
                    </div>
                </div>

                <div class="gmt-grid" style="grid-template-columns: 1fr 1.5fr;">
                    <div class="data-table-card">
                        <h4 style="margin-bottom: 15px;">Bullion Raw Stocks (Grams)</h4>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Metal</th>
                                    <th>Purity</th>
                                    <th>Weight</th>
                                    <th>Rate/g</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody id="bullion-list-tbody"></tbody>
                        </table>
                    </div>

                    <div class="data-table-card">
                        <h4 style="margin-bottom: 15px;">Finished Ornament Inventory</h4>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Barcode</th>
                                    <th>Name</th>
                                    <th>Metal/Purity</th>
                                    <th>Weights (G/N)</th>
                                    <th>Hallmark</th>
                                    <th>Selling Price</th>
                                    <th>Status</th>
                                    <th>Label</th>
                                </tr>
                            </thead>
                            <tbody id="ornaments-list-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 4. KARIGAR & JOB WORK -->
            <div class="tab-panel" id="tab-karigar-work">
                <div class="panel-header">
                    <h3>Craftsmen (Karigar) Assignments</h3>
                    <div style="display:flex; gap:10px;">
                        <button class="btn-action" onclick="openModal('modal-create-karigar')">+ Add Karigar</button>
                        <button class="btn-action" onclick="openModal('modal-create-job')">+ Assign Job Work</button>
                    </div>
                </div>

                <div class="gmt-grid">
                    <div class="data-table-card">
                        <h4 style="margin-bottom: 15px;">Craftsmen Specialists</h4>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Specialization</th>
                                    <th>Per Gram Rate</th>
                                    <th>Daily wage</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="karigars-list-tbody"></tbody>
                        </table>
                    </div>

                    <div class="data-table-card">
                        <h4 style="margin-bottom: 15px;">Job Allocations Status</h4>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Job #</th>
                                    <th>Karigar</th>
                                    <th>Allocated Weight</th>
                                    <th>Labor cost</th>
                                    <th>Expected Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="jobs-list-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 5. REPAIR & CUSTOM BOOKINGS -->
            <div class="tab-panel" id="tab-repairs-custom">
                <div class="panel-header">
                    <h3>Repairs and Custom Orders Bookings</h3>
                    <div style="display:flex; gap:10px;">
                        <button class="btn-action" onclick="openModal('modal-create-repair')">+ Log Repair Intake</button>
                        <button class="btn-action" onclick="openModal('modal-create-custom-booking')">+ Book Custom Design</button>
                    </div>
                </div>

                <div class="gmt-grid" style="grid-template-columns: 1fr 1fr;">
                    <div class="data-table-card">
                        <h4 style="margin-bottom: 15px;">Customer Repairs Intake Log</h4>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Rep #</th>
                                    <th>Ornament</th>
                                    <th>Weight Received</th>
                                    <th>Cost Estimate</th>
                                    <th>Status</th>
                                    <th>Delivery Action</th>
                                </tr>
                            </thead>
                            <tbody id="repairs-list-tbody"></tbody>
                        </table>
                    </div>

                    <div class="data-table-card">
                        <h4 style="margin-bottom: 15px;">Custom Orders Booking & Advances</h4>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Design Preview</th>
                                    <th>Metal Est.</th>
                                    <th>Advance Paid</th>
                                    <th>Delivery</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="custom-bookings-list-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 6. BUYBACK & EXCHANGE -->
            <div class="tab-panel" id="tab-buybacks-exchange">
                <div class="panel-header">
                    <h3>Old Metal Buyback & Exchanges</h3>
                    <button class="btn-action" onclick="openModal('modal-create-buyback')">+ Log Old Metal Exchange</button>
                </div>

                <div class="data-table-card">
                    <h4 style="margin-bottom: 15px;">Buyback Exchange Valuation Ledger</h4>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Transaction #</th>
                                <th>Customer ID</th>
                                <th>Metal</th>
                                <th>Purity</th>
                                <th>Weight</th>
                                <th>Valuation Rate/g</th>
                                <th>Net Payout Payout</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="buyback-list-tbody"></tbody>
                    </table>
                </div>
            </div>

            <!-- 7. DIAMONDS & AUDIT LOGS -->
            <div class="tab-panel" id="tab-diamonds-audit">
                <div class="panel-header">
                    <h3>Diamond Indices & Stock Audit Variances</h3>
                    <div style="display:flex; gap:10px;">
                        <button class="btn-action" onclick="openModal('modal-create-diamond')">+ Add Diamond</button>
                        <button class="btn-action" onclick="openModal('modal-create-audit')">+ Log Stock Audit Variance</button>
                    </div>
                </div>

                <div class="gmt-grid" style="grid-template-columns: 1fr 1fr;">
                    <div class="data-table-card">
                        <h4 style="margin-bottom: 15px;">Diamond Stocks Carat Register</h4>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Shape</th>
                                    <th>Carat</th>
                                    <th>Clarity / Color</th>
                                    <th>Cert Code</th>
                                    <th>Selling Price</th>
                                </tr>
                            </thead>
                            <tbody id="diamonds-list-tbody"></tbody>
                        </table>
                    </div>

                    <div class="data-table-card">
                        <h4 style="margin-bottom: 15px;">Physical Inventory Audits Log</h4>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Audit #</th>
                                    <th>Auditor</th>
                                    <th>System Weight</th>
                                    <th>Physical Count</th>
                                    <th>Variance</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="audit-list-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 8. DIAGNOSTICS & USERS (Super Admin) -->
            <div class="tab-panel" id="tab-diagnostics-users">
                <div class="gmt-grid">
                    <div class="data-table-card">
                        <h4 style="margin-bottom: 20px;">Portal operators Registries</h4>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>ERP Role</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="admin-users-tbody"></tbody>
                        </table>
                    </div>

                    <!-- SMTP Diagnostics -->
                    <div class="dashboard-content-panel">
                        <h4 style="margin-bottom: 20px;">SMTP Diagnostic Agent settings</h4>
                        <form id="form-admin-smtp">
                            <div class="form-group">
                                <label>SMTP Host Address</label>
                                <input type="text" class="form-input" id="smtp-host" placeholder="mail.domain.com">
                            </div>
                            <div class="form-group">
                                <label>SMTP Port</label>
                                <input type="text" class="form-input" id="smtp-port" placeholder="587">
                            </div>
                            <div class="form-group">
                                <label>SMTP Username</label>
                                <input type="text" class="form-input" id="smtp-user" placeholder="noreply@domain.com">
                            </div>
                            <div class="form-group">
                                <label>SMTP Password</label>
                                <input type="password" class="form-input" id="smtp-pass" placeholder="••••••••">
                            </div>
                            <div class="form-group">
                                <label>Security Type</label>
                                <select class="form-input" id="smtp-encryption" style="background:#0b0e26; color:#fff;">
                                    <option value="tls">TLS</option>
                                    <option value="ssl">SSL</option>
                                    <option value="none">None</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>From Sender Email</label>
                                <input type="email" class="form-input" id="smtp-from-email" placeholder="noreply@domain.com">
                            </div>
                            <div class="form-group">
                                <label>From Display Name</label>
                                <input type="text" class="form-input" id="smtp-from-name" placeholder="Jewellery ERP">
                            </div>
                            <div class="form-group">
                                <label>Verification Mail Template</label>
                                <textarea class="form-input" id="smtp-template" style="height:100px; resize:none;"></textarea>
                            </div>

                            <button type="submit" class="btn" style="margin-bottom:15px;">Save Configurations</button>

                            <div style="border-top: 1px solid var(--border-glass); padding-top: 15px;">
                                <label>Send Diagnostic Test Mail</label>
                                <input type="email" class="form-input" id="smtp-test-email" placeholder="test@recipient.com" style="margin-bottom:10px;">
                                <button type="button" class="btn" id="btn-smtp-test" style="background:transparent; border: 1px solid var(--primary); color:var(--primary);">Send Test Mail</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL DIALOGS INDEX -->
    <!-- 1. Add Bullion Stock -->
    <div class="modal-overlay" id="modal-create-bullion">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-bullion')">×</button>
            <h3 style="margin-bottom: 20px;">Record Bullion Metal</h3>
            <form id="form-create-bullion">
                <div class="form-group">
                    <label>Metal Type</label>
                    <select class="form-input" id="bullion-type" style="background:#0b0e26; color:#fff;" required>
                        <option value="Gold">Gold</option>
                        <option value="Silver">Silver</option>
                        <option value="Platinum">Platinum</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Purity Standard</label>
                    <input type="text" class="form-input" id="bullion-purity" placeholder="22K, 24K, 925" required>
                </div>
                <div class="form-group">
                    <label>Total Weight (Grams)</label>
                    <input type="number" step="0.001" class="form-input" id="bullion-weight" required>
                </div>
                <div class="form-group">
                    <label>Rate per Gram (₹)</label>
                    <input type="number" step="0.01" class="form-input" id="bullion-rate" required>
                </div>
                <div class="form-group">
                    <label>Storage Vault Location</label>
                    <input type="text" class="form-input" id="bullion-location" placeholder="Main Safe">
                </div>
                <button type="submit" class="btn">Add Bullion Record</button>
            </form>
        </div>
    </div>

    <!-- 2. Add Finished Ornament -->
    <div class="modal-overlay" id="modal-create-ornament">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-ornament')">×</button>
            <h3 style="margin-bottom: 20px;">Add Finished Ornament</h3>
            <form id="form-create-ornament">
                <div class="form-group">
                    <label>Barcode / Tag Number</label>
                    <input type="text" class="form-input" id="ornament-barcode" required>
                </div>
                <div class="form-group">
                    <label>SKU / Serial</label>
                    <input type="text" class="form-input" id="ornament-sku">
                </div>
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" class="form-input" id="ornament-name" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" class="form-input" id="ornament-category" placeholder="Necklace, Ring, chain" required>
                </div>
                <div class="form-group">
                    <label>Metal Type</label>
                    <select class="form-input" id="ornament-type" style="background:#0b0e26; color:#fff;" required>
                        <option value="Gold">Gold</option>
                        <option value="Silver">Silver</option>
                        <option value="Platinum">Platinum</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Purity</label>
                    <input type="text" class="form-input" id="ornament-purity" placeholder="22K, 18K" required>
                </div>
                <div class="form-group">
                    <label>Gross Weight (Grams)</label>
                    <input type="number" step="0.001" class="form-input" id="ornament-gross" required>
                </div>
                <div class="form-group">
                    <label>Stone Weight (Grams)</label>
                    <input type="number" step="0.001" class="form-input" id="ornament-stone" value="0">
                </div>
                <div class="form-group">
                    <label>Making Charges (Per Net Gram or Flat)</label>
                    <input type="number" step="0.01" class="form-input" id="ornament-making" value="450.00" required>
                </div>
                <div class="form-group">
                    <label>Selling Base Price (Flat estimation, optional)</label>
                    <input type="number" step="0.01" class="form-input" id="ornament-selling" value="0">
                </div>
                <div class="form-group">
                    <label>Hallmark ID</label>
                    <input type="text" class="form-input" id="ornament-hallmark">
                </div>
                <button type="submit" class="btn">Register Ornament</button>
            </form>
        </div>
    </div>

    <!-- 3. Add Karigar -->
    <div class="modal-overlay" id="modal-create-karigar">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-karigar')">×</button>
            <h3 style="margin-bottom: 20px;">Add Karigar</h3>
            <form id="form-create-karigar">
                <div class="form-group">
                    <label>Karigar Code</label>
                    <input type="text" class="form-input" id="karigar-code" placeholder="KARI-001">
                </div>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" class="form-input" id="karigar-name" required>
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" class="form-input" id="karigar-mobile">
                </div>
                <div class="form-group">
                    <label>Specialization Department</label>
                    <input type="text" class="form-input" id="karigar-spec" placeholder="Gold setting, Silver work" required>
                </div>
                <div class="form-group">
                    <label>Per Gram Wage Rate (₹)</label>
                    <input type="number" step="0.01" class="form-input" id="karigar-rate" value="120.00">
                </div>
                <div class="form-group">
                    <label>Daily Base Wage (₹)</label>
                    <input type="number" step="0.01" class="form-input" id="karigar-daily" value="0">
                </div>
                <button type="submit" class="btn">Register Karigar</button>
            </form>
        </div>
    </div>

    <!-- 4. Assign Job Work -->
    <div class="modal-overlay" id="modal-create-job">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-job')">×</button>
            <h3 style="margin-bottom: 20px;">Assign Job Work</h3>
            <form id="form-create-job">
                <div class="form-group">
                    <label>Job Number</label>
                    <input type="text" class="form-input" id="job-number" placeholder="JOB-1234">
                </div>
                <div class="form-group">
                    <label>Select Karigar</label>
                    <select class="form-input" id="job-karigar-id" style="background:#0b0e26; color:#fff;" required>
                        <option value="">-- Select Karigar --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Select Ornament Product ID</label>
                    <select class="form-input" id="job-product-id" style="background:#0b0e26; color:#fff;" required>
                        <option value="">-- Choose Item --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Allocated Metal Weight (Grams)</label>
                    <input type="number" step="0.001" class="form-input" id="job-weight" required>
                </div>
                <div class="form-group">
                    <label>Expected Completion Date</label>
                    <input type="date" class="form-input" id="job-expected" required>
                </div>
                <div class="form-group">
                    <label>Labor Wages Cost (Optional, auto-calculates)</label>
                    <input type="number" step="0.01" class="form-input" id="job-labor" value="0">
                </div>
                <button type="submit" class="btn">Dispatch Workshop Job</button>
            </form>
        </div>
    </div>

    <!-- 5. Log Repair Intake -->
    <div class="modal-overlay" id="modal-create-repair">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-repair')">×</button>
            <h3 style="margin-bottom: 20px;">Log Repair Intake</h3>
            <form id="form-create-repair">
                <div class="form-group">
                    <label>Repair Booking Code</label>
                    <input type="text" class="form-input" id="rep-code" placeholder="REP-12345">
                </div>
                <div class="form-group">
                    <label>Select Customer</label>
                    <select class="form-input" id="rep-customer-id" style="background:#0b0e26; color:#fff;" required>
                        <option value="">-- Choose Customer --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Ornament Description</label>
                    <input type="text" class="form-input" id="rep-desc" placeholder="Gold Chain 22K (Broken Link)" required>
                </div>
                <div class="form-group">
                    <label>Issue Description Details</label>
                    <textarea class="form-input" id="rep-issue" placeholder="Rejoin broken link, Polish required"></textarea>
                </div>
                <div class="form-group">
                    <label>Received Weight (Grams)</label>
                    <input type="number" step="0.001" class="form-input" id="rep-weight" required>
                </div>
                <div class="form-group">
                    <label>Estimated Repair Cost (₹)</label>
                    <input type="number" step="0.01" class="form-input" id="rep-cost" required>
                </div>
                <div class="form-group">
                    <label>Expected Delivery Date</label>
                    <input type="date" class="form-input" id="rep-delivery" required>
                </div>
                <button type="submit" class="btn">Log Repair Intake</button>
            </form>
        </div>
    </div>

    <!-- 6. Book Custom Design -->
    <div class="modal-overlay" id="modal-create-custom-booking">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-custom-booking')">×</button>
            <h3 style="margin-bottom: 20px;">Book Custom Design</h3>
            <form id="form-create-custom">
                <div class="form-group">
                    <label>Select Customer</label>
                    <select class="form-input" id="custom-customer-id" style="background:#0b0e26; color:#fff;" required>
                        <option value="">-- Select Customer --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Upload Design Reference Sketch (JPG, PNG)</label>
                    <input type="file" class="form-input" id="custom-file" style="padding: 8px 16px;">
                    <input type="hidden" id="custom-design-url" value="">
                </div>
                <div class="form-group">
                    <label>Metal Type & Purity Standard</label>
                    <input type="text" class="form-input" id="custom-purity" placeholder="Gold 22K, Silver 925" required>
                </div>
                <div class="form-group">
                    <label>Estimated Weight (Grams)</label>
                    <input type="number" step="0.001" class="form-input" id="custom-weight" required>
                </div>
                <div class="form-group">
                    <label>Advance Payment Paid (₹)</label>
                    <input type="number" step="0.01" class="form-input" id="custom-advance" required>
                </div>
                <div class="form-group">
                    <label>Target Delivery Date</label>
                    <input type="date" class="form-input" id="custom-delivery" required>
                </div>
                <button type="submit" class="btn">Book Order & Pay Advance</button>
            </form>
        </div>
    </div>

    <!-- 7. Log Old Metal Exchange -->
    <div class="modal-overlay" id="modal-create-buyback">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-buyback')">×</button>
            <h3 style="margin-bottom: 20px;">Log Old Metal Buyback</h3>
            <form id="form-create-buyback">
                <div class="form-group">
                    <label>Select Customer</label>
                    <select class="form-input" id="buyback-customer-id" style="background:#0b0e26; color:#fff;" required>
                        <option value="">-- Choose Customer --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Metal Type</label>
                    <select class="form-input" id="buyback-metal" style="background:#0b0e26; color:#fff;" required>
                        <option value="Gold">Gold</option>
                        <option value="Silver">Silver</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Purity / Karat Verification</label>
                    <input type="text" class="form-input" id="buyback-purity" placeholder="22K, 18K" required>
                </div>
                <div class="form-group">
                    <label>Net Metal Weight (Grams)</label>
                    <input type="number" step="0.001" class="form-input" id="buyback-weight" required>
                </div>
                <div class="form-group">
                    <label>Exchange Rate per Gram (₹)</label>
                    <input type="number" step="0.01" class="form-input" id="buyback-rate" required>
                </div>
                <button type="submit" class="btn">Record Buyback Valuation</button>
            </form>
        </div>
    </div>

    <!-- 8. Add Diamond -->
    <div class="modal-overlay" id="modal-create-diamond">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-diamond')">×</button>
            <h3 style="margin-bottom: 20px;">Register Diamond</h3>
            <form id="form-create-diamond">
                <div class="form-group">
                    <label>Diamond Code</label>
                    <input type="text" class="form-input" id="diamond-code" placeholder="DIA-001" required>
                </div>
                <div class="form-group">
                    <label>Shape</label>
                    <input type="text" class="form-input" id="diamond-shape" placeholder="Round Brilliant, Princess" required>
                </div>
                <div class="form-group">
                    <label>Carat Weight (ct)</label>
                    <input type="number" step="0.001" class="form-input" id="diamond-carat" required>
                </div>
                <div class="form-group">
                    <label>Clarity</label>
                    <input type="text" class="form-input" id="diamond-clarity" placeholder="VVS1, VS2" required>
                </div>
                <div class="form-group">
                    <label>Color Grade</label>
                    <input type="text" class="form-input" id="diamond-color" placeholder="D, F, G" required>
                </div>
                <div class="form-group">
                    <label>Certificate Code (GIA / IGI)</label>
                    <input type="text" class="form-input" id="diamond-cert">
                </div>
                <div class="form-group">
                    <label>Purchase Valuation Cost (₹)</label>
                    <input type="number" step="0.01" class="form-input" id="diamond-purchase" required>
                </div>
                <div class="form-group">
                    <label>Selling Listing Price (₹)</label>
                    <input type="number" step="0.01" class="form-input" id="diamond-selling" required>
                </div>
                <button type="submit" class="btn">Add Diamond Record</button>
            </form>
        </div>
    </div>

    <!-- 9. Log Stock Audit Variance -->
    <div class="modal-overlay" id="modal-create-audit">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-audit')">×</button>
            <h3 style="margin-bottom: 20px;">Log Stock Variance Audit</h3>
            <form id="form-create-audit">
                <div class="form-group">
                    <label>Audit Checklist Code</label>
                    <input type="text" class="form-input" id="audit-code" placeholder="AUD-99" required>
                </div>
                <div class="form-group">
                    <label>Auditor Inspector Name</label>
                    <input type="text" class="form-input" id="audit-inspector" required>
                </div>
                <div class="form-group">
                    <label>Select Finished Ornament</label>
                    <select class="form-input" id="audit-item-id" style="background:#0b0e26; color:#fff;" required>
                        <option value="">-- Choose Ornament --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Physical Weight Measured (Grams)</label>
                    <input type="number" step="0.001" class="form-input" id="audit-physical" required>
                </div>
                <button type="submit" class="btn">Submit Audit Log</button>
            </form>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script>
        const baseUrl = '<?php echo esc_url_raw(rest_url("jewellery-management/v1")); ?>';
        let currentUser = null;
        let chartInstance = null;

        // Populate test presets login values
        function fillPreset(username, password) {
            document.getElementById('login-username').value = username;
            document.getElementById('login-password').value = password;
            document.getElementById('login-pass-group').style.display = 'block';
            document.getElementById('login-otp-group').style.display = 'none';
            document.getElementById('btn-login-submit').innerText = 'Login with Password';
            document.getElementById('login-otp').value = '';
        }

        // Modals management
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }
        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        // Toast alerting notification
        function showToast(message, type = "success") {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <span>${message}</span>
                <span style="margin-left:15px; cursor:pointer;" onclick="this.parentElement.remove()">×</span>
            `;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        }

        // Live Clock
        setInterval(() => {
            const now = new Date();
            document.getElementById('clock-display').innerText = now.toLocaleTimeString();
        }, 1000);

        // API Fetch helper
        function apiFetch(endpoint, method = 'GET', body = null) {
            const token = localStorage.getItem('jwl_access_token');
            const headers = {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };
            if (token) {
                headers['Authorization'] = 'Bearer ' + token;
            }

            const options = { method, headers };
            if (body && (method === 'POST' || method === 'PUT')) {
                options.body = JSON.stringify(body);
            }

            return fetch(baseUrl + endpoint, options)
                .then(res => {
                    if (res.status === 401) {
                        // Attempt token refresh
                        const refresh = localStorage.getItem('jwl_refresh_token');
                        if (refresh && endpoint !== '/auth/refresh-token') {
                            return fetch(baseUrl + '/auth/refresh-token', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ refresh_token: refresh })
                            })
                            .then(r => r.json())
                            .then(r => {
                                if (r.success) {
                                    localStorage.setItem('jwl_access_token', r.data.token);
                                    localStorage.setItem('jwl_refresh_token', r.data.refresh_token);
                                    return apiFetch(endpoint, method, body);
                                } else {
                                    handleForceLogout();
                                    throw new Error("Expired session");
                                }
                            });
                        } else {
                            handleForceLogout();
                            throw new Error("Expired session");
                        }
                    }
                    return res.json();
                });
        }

        function handleForceLogout() {
            localStorage.removeItem('jwl_access_token');
            localStorage.removeItem('jwl_refresh_token');
            document.getElementById('dashboard-shell').style.display = 'none';
            document.getElementById('login-view').style.display = 'flex';
            document.getElementById('page-loader').style.display = 'none';
            currentUser = null;
        }

        // Session Bootstrapper onload
        window.addEventListener('load', () => {
            const token = localStorage.getItem('jwl_access_token');
            if (!token) {
                handleForceLogout();
            } else {
                apiFetch('/auth/me')
                    .then(res => {
                        document.getElementById('page-loader').style.display = 'none';
                        if (res.success) {
                            currentUser = res.data;
                            setupAuthorizedUi();
                        } else {
                            handleForceLogout();
                        }
                    })
                    .catch(() => {
                        document.getElementById('page-loader').style.display = 'none';
                        handleForceLogout();
                    });
            }
        });

        // Setup UI for authorized operators
        function setupAuthorizedUi() {
            document.getElementById('login-view').style.display = 'none';
            document.getElementById('dashboard-shell').style.display = 'flex';

            // User Info binds
            document.getElementById('user-display-name').innerText = currentUser.name;
            document.getElementById('user-display-role').innerText = currentUser.role.replace('jewel_', '').replace('_', ' ');
            document.getElementById('user-avatar-initials').innerText = currentUser.name.split(' ').map(n=>n[0]).join('').substring(0,2).toUpperCase();

            // Admin gate
            if (currentUser.role === 'jewel_super_admin' || currentUser.role === 'administrator') {
                const adminTabs = document.querySelectorAll('.admin-only');
                adminTabs.forEach(t => t.style.display = 'flex');
            }

            // Restore active tab
            const storedTab = localStorage.getItem('jwl_active_tab') || 'dashboard-overview';
            switchTab(storedTab);
        }

        // Sidebar Navigation Tab switching
        const sidebarItems = document.querySelectorAll('.sidebar-item');
        sidebarItems.forEach(item => {
            item.addEventListener('click', () => {
                const targetTab = item.getAttribute('data-tab');
                switchTab(targetTab);
            });
        });

        function switchTab(tabId) {
            sidebarItems.forEach(i => i.classList.remove('active'));
            const activeItem = Array.from(sidebarItems).find(i => i.getAttribute('data-tab') === tabId);
            if (activeItem) {
                activeItem.classList.add('active');
            }

            const panels = document.querySelectorAll('.tab-panel');
            panels.forEach(p => p.classList.remove('active'));

            const targetPanel = document.getElementById(`tab-${tabId}`);
            if (targetPanel) {
                targetPanel.classList.add('active');
                document.getElementById('current-tab-title').innerText = activeItem ? activeItem.innerText : 'ERP Dashboard';
                localStorage.setItem('jwl_active_tab', tabId);
                triggerTabFetch(tabId);
            }
        }

        // Dynamic API Fetch triggers per tab focus
        function triggerTabFetch(tabId) {
            // General setup lookups needed
            fetchCommonLookups();

            switch (tabId) {
                case 'dashboard-overview':
                    fetchDashboardOverviewData();
                    break;
                case 'sales-billing':
                    fetchSalesBillingData();
                    break;
                case 'bullion-stock':
                    fetchBullionOrnamentsData();
                    break;
                case 'karigar-work':
                    fetchKarigarWorkData();
                    break;
                case 'repairs-custom':
                    fetchRepairsCustomData();
                    break;
                case 'buybacks-exchange':
                    fetchBuybackExchangeData();
                    break;
                case 'diamonds-audit':
                    fetchDiamondsAuditData();
                    break;
                case 'diagnostics-users':
                    fetchDiagnosticsUsersData();
                    break;
            }
        }

        // Fetch common lookup variables (Customers list, active inventory list)
        let globalCustomersList = [];
        let globalInventoryList = [];
        let globalKarigarsList = [];

        function fetchCommonLookups() {
            // Customers dropdown options
            apiFetch('/customer').then(res => {
                if (res.success) {
                    globalCustomersList = res.data;
                    const selects = ['bill-customer-id', 'rep-customer-id', 'custom-customer-id', 'buyback-customer-id'];
                    selects.forEach(selId => {
                        const selectEl = document.getElementById(selId);
                        if (selectEl) {
                            const val = selectEl.value;
                            selectEl.innerHTML = '<option value="">-- Choose Customer --</option>' + 
                                globalCustomersList.map(c => `<option value="${c.id}">${c.name} (${c.customer_code})</option>`).join('');
                            if (val) selectEl.value = val;
                        }
                    });
                }
            });

            // Ornaments options
            apiFetch('/inventory').then(res => {
                if (res.success) {
                    globalInventoryList = res.data;
                    // Active ornaments list for billing dropdown
                    const activeOrnaments = globalInventoryList.filter(o => o.status === 'ACTIVE');
                    
                    const billSelect = document.getElementById('bill-product-id');
                    if (billSelect) {
                        const val = billSelect.value;
                        billSelect.innerHTML = '<option value="">-- Select Active Item --</option>' +
                            activeOrnaments.map(o => `<option value="${o.id}">${o.product_name} [${o.barcode}] (${o.gross_weight}g)</option>`).join('');
                        if (val) billSelect.value = val;
                    }

                    // Job product selection options
                    const jobSelect = document.getElementById('job-product-id');
                    if (jobSelect) {
                        const val = jobSelect.value;
                        jobSelect.innerHTML = '<option value="">-- Choose Item --</option>' +
                            activeOrnaments.map(o => `<option value="${o.id}">${o.product_name} [${o.barcode}]</option>`).join('');
                        if (val) jobSelect.value = val;
                    }

                    // Audit options
                    const auditSelect = document.getElementById('audit-item-id');
                    if (auditSelect) {
                        const val = auditSelect.value;
                        auditSelect.innerHTML = '<option value="">-- Choose Ornament --</option>' +
                            globalInventoryList.map(o => `<option value="${o.id}">${o.product_name} [${o.barcode}]</option>`).join('');
                        if (val) auditSelect.value = val;
                    }
                }
            });

            // Karigars dropdown options
            apiFetch('/karigar').then(res => {
                if (res.success) {
                    globalKarigarsList = res.data;
                    const jobKarigarSelect = document.getElementById('job-karigar-id');
                    if (jobKarigarSelect) {
                        const val = jobKarigarSelect.value;
                        jobKarigarSelect.innerHTML = '<option value="">-- Select Karigar --</option>' +
                            globalKarigarsList.filter(k => k.status === 'ACTIVE').map(k => `<option value="${k.id}">${k.name} (${k.karigar_code})</option>`).join('');
                        if (val) jobKarigarSelect.value = val;
                    }
                }
            });
        }

        // Dropdowns events
        document.getElementById('bill-product-id').addEventListener('change', (e) => {
            const prodId = parseInt(e.target.value);
            const item = globalInventoryList.find(o => o.id === prodId);
            if (item) {
                document.getElementById('bill-gross-weight').value = item.gross_weight;
                document.getElementById('bill-net-weight').value = item.net_weight;
                document.getElementById('bill-making-charges').value = item.making_charges;
                calculateBillTotals();
            }
        });

        // 3% standard GST invoice totals calculator
        function calculateBillTotals() {
            const netWeight = parseFloat(document.getElementById('bill-net-weight').value) || 0;
            const goldRate = parseFloat(document.getElementById('bill-gold-rate').value) || 0;
            const silverRate = parseFloat(document.getElementById('bill-silver-rate').value) || 0;
            const makingRateOrFlat = parseFloat(document.getElementById('bill-making-charges').value) || 0;
            const stone = parseFloat(document.getElementById('bill-stone-charges').value) || 0;
            const discount = parseFloat(document.getElementById('bill-discount').value) || 0;

            const prodId = parseInt(document.getElementById('bill-product-id').value);
            const item = globalInventoryList.find(o => o.id === prodId);
            
            const rate = (item && item.metal_type === 'Silver') ? silverRate : goldRate;
            const metalVal = netWeight * rate;

            // making charges calculation
            let making = makingRateOrFlat;
            if (makingRateOrFlat > 0 && makingRateOrFlat < 5000) {
                making = netWeight * makingRateOrFlat;
            }

            const subtotal = metalVal + making + stone - discount;
            const finalSubtotal = subtotal > 0 ? subtotal : 0;
            const gst = finalSubtotal * 0.03;
            const total = finalSubtotal + gst;

            document.getElementById('bill-subtotal-text').innerText = '₹' + finalSubtotal.toFixed(2);
            document.getElementById('bill-gst-text').innerText = '₹' + gst.toFixed(2);
            document.getElementById('bill-total-text').innerText = '₹' + total.toFixed(2);
        }

        // TAB 1: Dashboard overview stats & analytical charts
        function fetchDashboardOverviewData() {
            apiFetch('/dashboard').then(res => {
                if (res.success) {
                    const counters = res.data.counters;
                    document.getElementById('kpi-gold-stock').innerText = counters.gold_stock_g.toFixed(1) + 'g';
                    document.getElementById('kpi-silver-stock').innerText = counters.silver_stock_g.toFixed(1) + 'g';
                    document.getElementById('kpi-daily-sales').innerText = '₹' + counters.daily_sales.toLocaleString();
                    document.getElementById('kpi-active-karigars').innerText = counters.active_karigars;
                    
                    document.getElementById('badge-active-repairs').innerText = counters.active_repairs;
                    document.getElementById('badge-active-custom').innerText = counters.active_custom_orders;

                    // Chart trends binds
                    const trends = res.data.trends;
                    renderDashboardCharts(trends);
                }
            });
        }

        function renderDashboardCharts(trends) {
            if (chartInstance) {
                chartInstance.destroy();
            }

            const ctx = document.getElementById('sales-chart').getContext('2d');
            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: trends.sales.labels,
                    datasets: [{
                        label: 'Sales Revenue (INR)',
                        data: trends.sales.data,
                        backgroundColor: '#ffd700',
                        borderColor: '#b8860b',
                        borderWidth: 1,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            grid: { color: 'rgba(255, 215, 0, 0.05)' },
                            ticks: { color: '#9ca3af' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#9ca3af' }
                        }
                    }
                }
            });
        }

        // TAB 2: Sales & Billing
        function fetchSalesBillingData() {
            apiFetch('/billing').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('billing-list-tbody');
                    tbody.innerHTML = res.data.map(b => `
                        <tr>
                            <td><strong>${b.invoice_number}</strong></td>
                            <td>${b.invoice_date.split(' ')[0]}</td>
                            <td>${b.gross_weight}g</td>
                            <td>${b.net_weight}g</td>
                            <td>₹${b.gst_amount}</td>
                            <td><span style="color:var(--primary); font-weight:700;">₹${parseFloat(b.total_amount).toLocaleString()}</span></td>
                            <td>${b.payment_method}</td>
                            <td><span class="status-badge ${b.status.toLowerCase()}">${b.status}</span></td>
                        </tr>
                    `).join('');
                }
            });
        }

        // TAB 3: Bullion & Finished stock
        function fetchBullionOrnamentsData() {
            apiFetch('/metal-stock').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('bullion-list-tbody');
                    tbody.innerHTML = res.data.map(m => `
                        <tr>
                            <td><strong>${m.metal_type}</strong></td>
                            <td>${m.purity}</td>
                            <td>${m.weight}g</td>
                            <td>₹${m.rate_per_gram}</td>
                            <td>₹${parseFloat(m.total_value).toLocaleString()}</td>
                        </tr>
                    `).join('');
                }
            });

            apiFetch('/inventory').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('ornaments-list-tbody');
                    tbody.innerHTML = res.data.map(o => `
                        <tr>
                            <td><code>${o.barcode}</code></td>
                            <td><strong>${o.product_name}</strong></td>
                            <td>${o.metal_type} ${o.purity}</td>
                            <td>Gross: ${o.gross_weight}g / Net: ${o.net_weight}g</td>
                            <td>${o.hallmark_number || '-'}</td>
                            <td>₹${parseFloat(o.selling_price).toLocaleString()}</td>
                            <td><span class="status-badge ${o.status.toLowerCase()}">${o.status}</span></td>
                            <td>
                                <button class="btn-action" style="padding:4px 8px; font-size:11px;" onclick="printBarcodeLabel(${o.id})">Print</button>
                            </td>
                        </tr>
                    `).join('');
                }
            });
        }

        function printBarcodeLabel(itemId) {
            apiFetch(`/barcode/print/${itemId}`).then(res => {
                if (res.success) {
                    const cfg = res.data;
                    alert(`Label dispatched to printer queue:
Barcode: ${cfg.barcode}
Product: ${cfg.product_name}
Weight (G/N): ${cfg.gross_weight} / ${cfg.net_weight}
Hallmark: ${cfg.hallmark}
Width/Height: ${cfg.label_width_mm}mm x ${cfg.label_height_mm}mm @ ${cfg.dpi} DPI`);
                } else {
                    showToast(res.message, "error");
                }
            });
        }

        // TAB 4: Karigars & Jobs
        function fetchKarigarWorkData() {
            apiFetch('/karigar').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('karigars-list-tbody');
                    tbody.innerHTML = res.data.map(k => `
                        <tr>
                            <td><strong>${k.karigar_code}</strong></td>
                            <td>${k.name}</td>
                            <td>${k.specialization}</td>
                            <td>₹${k.per_gram_rate}/g</td>
                            <td>₹${k.daily_rate}/day</td>
                            <td><span class="status-badge ${k.status.toLowerCase()}">${k.status}</span></td>
                        </tr>
                    `).join('');
                }
            });

            apiFetch('/job-work').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('jobs-list-tbody');
                    tbody.innerHTML = res.data.map(j => `
                        <tr>
                            <td><strong>${j.job_number}</strong></td>
                            <td>Karigar ID: ${j.karigar_id}</td>
                            <td>${j.metal_weight}g</td>
                            <td>₹${j.labor_cost}</td>
                            <td>${j.expected_completion ? j.expected_completion.split(' ')[0] : 'N/A'}</td>
                            <td><span class="status-badge ${j.status.toLowerCase()}">${j.status}</span></td>
                            <td>
                                ${j.status !== 'Completed' && j.status !== 'Delivered' ? `
                                    <button class="btn-action" style="padding:4px 8px; font-size:11px;" onclick="completeKarigarJob(${j.id})">Complete</button>
                                ` : '-'}
                            </td>
                        </tr>
                    `).join('');
                }
            });
        }

        function completeKarigarJob(jobId) {
            if (confirm("Mark this karigar job assignment as completed?")) {
                apiFetch(`/job-work/${jobId}`, 'PUT', { status: 'Completed' })
                    .then(res => {
                        if (res.success) {
                            showToast("Job assignment completed successfully", "success");
                            fetchKarigarWorkData();
                        } else {
                            showToast(res.message, "error");
                        }
                    });
            }
        }

        // TAB 5: Repairs & Custom
        function fetchRepairsCustomData() {
            apiFetch('/repair').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('repairs-list-tbody');
                    tbody.innerHTML = res.data.map(r => `
                        <tr>
                            <td><strong>${r.repair_number}</strong></td>
                            <td>${r.product_description}</td>
                            <td>${r.received_weight}g</td>
                            <td>₹${r.repair_cost}</td>
                            <td><span class="status-badge ${r.status.toLowerCase()}">${r.status}</span></td>
                            <td>
                                ${r.status !== 'Delivered' ? `
                                    <button class="btn-action" style="padding:4px 8px; font-size:11px; background:var(--success); color:#000;" onclick="deliverRepairOrder(${r.id})">Deliver</button>
                                ` : '-'}
                            </td>
                        </tr>
                    `).join('');
                }
            });

            apiFetch('/custom-order').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('custom-bookings-list-tbody');
                    tbody.innerHTML = res.data.map(c => `
                        <tr>
                            <td><strong>${c.order_number}</strong></td>
                            <td>
                                ${c.design_reference ? `<img src="${c.design_reference}" style="max-height:40px; border-radius:4px; border:1px solid var(--border-glass);">` : '<span style="font-size:11px; color:var(--text-muted);">No sketch</span>'}
                            </td>
                            <td>${c.metal_type} (${c.weight_estimate}g)</td>
                            <td>₹${c.advance_amount.toLocaleString()}</td>
                            <td>${c.delivery_date ? c.delivery_date.split(' ')[0] : 'N/A'}</td>
                            <td><span class="status-badge ${c.status.toLowerCase()}">${c.status}</span></td>
                        </tr>
                    `).join('');
                }
            });
        }

        function deliverRepairOrder(id) {
            apiFetch(`/repair/${id}`, 'PUT', { status: 'Delivered' })
                .then(res => {
                    if (res.success) {
                        showToast("Repair item marked as delivered to customer", "success");
                        fetchRepairsCustomData();
                    }
                });
        }

        // TAB 6: Buyback
        function fetchBuybackExchangeData() {
            apiFetch('/buyback').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('buyback-list-tbody');
                    tbody.innerHTML = res.data.map(b => `
                        <tr>
                            <td><strong>${b.transaction_number}</strong></td>
                            <td>Customer ID: ${b.customer_id}</td>
                            <td>${b.metal_type}</td>
                            <td>${b.purity}</td>
                            <td>${b.weight}g</td>
                            <td>₹${b.rate_per_gram}</td>
                            <td><span style="color:var(--danger); font-weight:700;">₹${parseFloat(b.payout_amount).toLocaleString()}</span></td>
                            <td>${b.created_at.split(' ')[0]}</td>
                        </tr>
                    `).join('');
                }
            });
        }

        // TAB 7: Diamonds & Audits
        function fetchDiamondsAuditData() {
            apiFetch('/diamond').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('diamonds-list-tbody');
                    tbody.innerHTML = res.data.map(d => `
                        <tr>
                            <td><strong>${d.diamond_code}</strong></td>
                            <td>${d.shape}</td>
                            <td>${d.carat} ct</td>
                            <td>${d.clarity} / ${d.color}</td>
                            <td><code>${d.certificate_number || 'None'}</code></td>
                            <td>₹${parseFloat(d.selling_price).toLocaleString()}</td>
                        </tr>
                    `).join('');
                }
            });

            apiFetch('/inventory-audit').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('audit-list-tbody');
                    tbody.innerHTML = res.data.map(a => `
                        <tr>
                            <td><strong>${a.audit_number}</strong></td>
                            <td>${a.auditor_name}</td>
                            <td>${a.system_qty}g</td>
                            <td>${a.physical_qty}g</td>
                            <td><span style="color:${parseFloat(a.variance) < 0 ? 'var(--danger)' : 'var(--success)'}">${a.variance}g</span></td>
                            <td><span class="status-badge ${a.status.toLowerCase()}">${a.status}</span></td>
                            <td>
                                ${a.status === 'PENDING' ? `
                                    <button class="btn-action" style="padding:4px 8px; font-size:11px;" onclick="adjustAuditStock(${a.id})">Adjust Stock</button>
                                ` : '-'}
                            </td>
                        </tr>
                    `).join('');
                }
            });
        }

        function adjustAuditStock(auditId) {
            if (confirm("Approve stock reconciliation adjustment and modify active inventories weights?")) {
                apiFetch(`/inventory-audit/${auditId}`, 'PUT', { status: 'ADJUSTED' })
                    .then(res => {
                        if (res.success) {
                            showToast("Audit stock reconciliations performed successfully.", "success");
                            fetchDiamondsAuditData();
                        } else {
                            showToast(res.message, "error");
                        }
                    });
            }
        }

        // TAB 8: Diagnostics & Admin
        function fetchDiagnosticsUsersData() {
            apiFetch('/auth/users').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('admin-users-tbody');
                    tbody.innerHTML = res.data.map(u => `
                        <tr>
                            <td><strong>${u.name} (${u.username})</strong></td>
                            <td>${u.email}</td>
                            <td><span class="status-badge" style="background:rgba(255,255,255,0.05); color:#fff;">${u.role.replace('jewel_', '').replace('_', ' ')}</span></td>
                            <td><span class="status-badge ${u.status.toLowerCase()}">${u.status}</span></td>
                            <td>
                                ${u.status === 'PENDING' ? `
                                    <button class="btn-action" style="padding: 4px 8px; font-size:11px; background:var(--success); color:#000;" onclick="updateUserStatus(${u.id}, 'APPROVED')">Approve</button>
                                ` : `
                                    <button class="btn-action" style="padding: 4px 8px; font-size:11px; background:var(--warning); color:#000;" onclick="updateUserStatus(${u.id}, 'HOLD')">Suspend</button>
                                `}
                                <button class="btn-action" style="padding: 4px 8px; font-size:11px; background:var(--danger); color:#fff;" onclick="deleteUserAccount(${u.id})">Delete</button>
                            </td>
                        </tr>
                    `).join('');
                }
            });

            // Fetch SMTP Options config
            apiFetch('/auth/smtp').then(res => {
                if (res.success) {
                    document.getElementById('smtp-host').value = res.data.smtp_host;
                    document.getElementById('smtp-port').value = res.data.smtp_port;
                    document.getElementById('smtp-user').value = res.data.smtp_username;
                    document.getElementById('smtp-pass').value = res.data.smtp_password;
                    document.getElementById('smtp-encryption').value = res.data.smtp_encryption;
                    document.getElementById('smtp-from-email').value = res.data.from_email;
                    document.getElementById('smtp-from-name').value = res.data.from_name;
                    document.getElementById('smtp-template').value = res.data.template;
                }
            });
        }

        function updateUserStatus(userId, status) {
            apiFetch('/auth/users/status', 'POST', { user_id: userId, status: status })
                .then(res => {
                    if (res.success) {
                        showToast(`Operator status updated to ${status}`, "success");
                        fetchDiagnosticsUsersData();
                    } else {
                        showToast(res.message, "error");
                    }
                });
        }

        function deleteUserAccount(id) {
            if (confirm("Are you sure you want to permanently delete this user operator?")) {
                apiFetch(`/auth/users/${id}`, 'DELETE')
                    .then(res => {
                        if (res.success) {
                            showToast("Operator deleted successfully", "success");
                            fetchDiagnosticsUsersData();
                        } else {
                            showToast(res.message, "error");
                        }
                    });
            }
        }

        // SUBMIT FORM EVENTS LOGIC
        // 1. Authenticate password submit
        document.getElementById('login-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const username = document.getElementById('login-username').value;
            const pass = document.getElementById('login-password').value;
            const otp = document.getElementById('login-otp').value;

            const payload = { username };
            if (otp) {
                payload.otp = otp;
            } else {
                payload.password = pass;
            }

            document.getElementById('btn-login-submit').innerText = 'Authenticating...';

            fetch(`${baseUrl}/auth/login`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(res => {
                document.getElementById('btn-login-submit').innerText = otp ? 'Verify & Login' : 'Login with Password';
                if (res.success) {
                    localStorage.setItem('jwl_access_token', res.data.token);
                    localStorage.setItem('jwl_refresh_token', res.data.refresh_token);
                    currentUser = res.data.user;
                    setupAuthorizedUi();
                    showToast("Welcome back to Jewellery ERP portal", "success");
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // OTP login request
        document.getElementById('btn-send-otp').addEventListener('click', () => {
            const val = document.getElementById('login-username').value;
            if (!val) {
                showToast("Enter username or email address first", "warning");
                return;
            }
            document.getElementById('btn-send-otp').innerText = 'Sending OTP...';
            
            fetch(`${baseUrl}/auth/login/initiate`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username: val })
            })
            .then(res => res.json())
            .then(res => {
                document.getElementById('btn-send-otp').innerText = 'Request OTP Code';
                if (res.success) {
                    showToast(res.message, "success");
                    document.getElementById('login-pass-group').style.display = 'none';
                    document.getElementById('login-otp-group').style.display = 'block';
                    document.getElementById('btn-login-submit').innerText = 'Verify & Login';
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // Logout
        document.getElementById('btn-logout').addEventListener('click', () => {
            handleForceLogout();
            showToast("Successfully signed out.", "success");
        });

        // 2. Generate invoice
        document.getElementById('form-create-bill').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                invoice_number: document.getElementById('bill-invoice-number').value || undefined,
                customer_id: parseInt(document.getElementById('bill-customer-id').value),
                product_id: parseInt(document.getElementById('bill-product-id').value),
                gross_weight: parseFloat(document.getElementById('bill-gross-weight').value),
                net_weight: parseFloat(document.getElementById('bill-net-weight').value),
                gold_rate: parseFloat(document.getElementById('bill-gold-rate').value),
                silver_rate: parseFloat(document.getElementById('bill-silver-rate').value),
                making_charges: parseFloat(document.getElementById('bill-making-charges').value),
                stone_charges: parseFloat(document.getElementById('bill-stone-charges').value),
                discount: parseFloat(document.getElementById('bill-discount').value),
                payment_method: document.getElementById('bill-payment-method').value
            };
            apiFetch('/billing', 'POST', payload).then(res => {
                if (res.success) {
                    showToast(`Invoice generated: ${res.data.invoice_number}`, "success");
                    document.getElementById('form-create-bill').reset();
                    fetchSalesBillingData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 3. Add Raw metal bullion
        document.getElementById('form-create-bullion').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                metal_type: document.getElementById('bullion-type').value,
                purity: document.getElementById('bullion-purity').value,
                weight: parseFloat(document.getElementById('bullion-weight').value),
                rate_per_gram: parseFloat(document.getElementById('bullion-rate').value),
                location: document.getElementById('bullion-location').value
            };
            apiFetch('/metal-stock', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Bullion metal registered successfully", "success");
                    closeModal('modal-create-bullion');
                    document.getElementById('form-create-bullion').reset();
                    fetchBullionOrnamentsData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 4. Register Ornament
        document.getElementById('form-create-ornament').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                barcode: document.getElementById('ornament-barcode').value,
                sku: document.getElementById('ornament-sku').value,
                product_name: document.getElementById('ornament-name').value,
                category: document.getElementById('ornament-category').value,
                metal_type: document.getElementById('ornament-type').value,
                purity: document.getElementById('ornament-purity').value,
                gross_weight: parseFloat(document.getElementById('ornament-gross').value),
                stone_weight: parseFloat(document.getElementById('ornament-stone').value),
                making_charges: parseFloat(document.getElementById('ornament-making').value),
                selling_price: parseFloat(document.getElementById('ornament-selling').value),
                hallmark_number: document.getElementById('ornament-hallmark').value
            };
            apiFetch('/inventory', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Finished ornament added to active vaults", "success");
                    closeModal('modal-create-ornament');
                    document.getElementById('form-create-ornament').reset();
                    fetchBullionOrnamentsData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 5. Add Karigar
        document.getElementById('form-create-karigar').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                karigar_code: document.getElementById('karigar-code').value || undefined,
                name: document.getElementById('karigar-name').value,
                mobile: document.getElementById('karigar-mobile').value,
                specialization: document.getElementById('karigar-spec').value,
                per_gram_rate: parseFloat(document.getElementById('karigar-rate').value),
                daily_rate: parseFloat(document.getElementById('karigar-daily').value)
            };
            apiFetch('/karigar', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Karigar registered successfully", "success");
                    closeModal('modal-create-karigar');
                    document.getElementById('form-create-karigar').reset();
                    fetchKarigarWorkData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 6. Assign Job Work
        document.getElementById('form-create-job').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                job_number: document.getElementById('job-number').value || undefined,
                karigar_id: parseInt(document.getElementById('job-karigar-id').value),
                product_id: parseInt(document.getElementById('job-product-id').value),
                metal_weight: parseFloat(document.getElementById('job-weight').value),
                expected_completion: document.getElementById('job-expected').value,
                labor_cost: parseFloat(document.getElementById('job-labor').value) || undefined
            };
            apiFetch('/job-work', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Job assignment registered successfully", "success");
                    closeModal('modal-create-job');
                    document.getElementById('form-create-job').reset();
                    fetchKarigarWorkData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 7. Log Repair Intake
        document.getElementById('form-create-repair').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                repair_number: document.getElementById('rep-code').value || undefined,
                customer_id: parseInt(document.getElementById('rep-customer-id').value),
                product_description: document.getElementById('rep-desc').value,
                issue_description: document.getElementById('rep-issue').value,
                received_weight: parseFloat(document.getElementById('rep-weight').value),
                repair_cost: parseFloat(document.getElementById('rep-cost').value),
                expected_delivery: document.getElementById('rep-delivery').value
            };
            apiFetch('/repair', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Repair intake logged.", "success");
                    closeModal('modal-create-repair');
                    document.getElementById('form-create-repair').reset();
                    fetchRepairsCustomData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 8. Design Upload Media and Custom Booking Book
        document.getElementById('custom-file').addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);

            const token = localStorage.getItem('jwl_access_token');
            const headers = { 'X-Requested-With': 'XMLHttpRequest' };
            if (token) headers['Authorization'] = 'Bearer ' + token;

            fetch(baseUrl + '/media/upload', {
                method: 'POST',
                headers,
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    document.getElementById('custom-design-url').value = res.data.url;
                    showToast("Design sketch uploaded successfully", "success");
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        document.getElementById('form-create-custom').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                customer_id: parseInt(document.getElementById('custom-customer-id').value),
                design_reference: document.getElementById('custom-design-url').value,
                purity: document.getElementById('custom-purity').value,
                weight_estimate: parseFloat(document.getElementById('custom-weight').value),
                advance_amount: parseFloat(document.getElementById('custom-advance').value),
                delivery_date: document.getElementById('custom-delivery').value
            };
            apiFetch('/custom-order', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Custom order design booked successfully", "success");
                    closeModal('modal-create-custom-booking');
                    document.getElementById('form-create-custom').reset();
                    document.getElementById('custom-design-url').value = '';
                    fetchRepairsCustomData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 9. Log Buyback
        document.getElementById('form-create-buyback').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                customer_id: parseInt(document.getElementById('buyback-customer-id').value),
                metal_type: document.getElementById('buyback-metal').value,
                purity: document.getElementById('buyback-purity').value,
                weight: parseFloat(document.getElementById('buyback-weight').value),
                rate_per_gram: parseFloat(document.getElementById('buyback-rate').value)
            };
            apiFetch('/buyback', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Buyback/exchange rate calculator logged successfully", "success");
                    closeModal('modal-create-buyback');
                    document.getElementById('form-create-buyback').reset();
                    fetchBuybackExchangeData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 10. Register Diamond
        document.getElementById('form-create-diamond').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                diamond_code: document.getElementById('diamond-code').value,
                shape: document.getElementById('diamond-shape').value,
                carat: parseFloat(document.getElementById('diamond-carat').value),
                clarity: document.getElementById('diamond-clarity').value,
                color: document.getElementById('diamond-color').value,
                certificate_number: document.getElementById('diamond-cert').value,
                purchase_price: parseFloat(document.getElementById('diamond-purchase').value),
                selling_price: parseFloat(document.getElementById('diamond-selling').value)
            };
            apiFetch('/diamond', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Diamond added to index register", "success");
                    closeModal('modal-create-diamond');
                    document.getElementById('form-create-diamond').reset();
                    fetchDiamondsAuditData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 11. Log Audit
        document.getElementById('form-create-audit').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                audit_number: document.getElementById('audit-code').value,
                auditor_name: document.getElementById('audit-inspector').value,
                item_id: parseInt(document.getElementById('audit-item-id').value),
                physical_qty: parseFloat(document.getElementById('audit-physical').value)
            };
            apiFetch('/inventory-audit', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Stock variance checklist submitted.", "success");
                    closeModal('modal-create-audit');
                    document.getElementById('form-create-audit').reset();
                    fetchDiamondsAuditData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 12. Save SMTP Configurations
        document.getElementById('form-admin-smtp').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                smtp_host: document.getElementById('smtp-host').value,
                smtp_port: document.getElementById('smtp-port').value,
                smtp_username: document.getElementById('smtp-user').value,
                smtp_password: document.getElementById('smtp-pass').value,
                smtp_encryption: document.getElementById('smtp-encryption').value,
                from_email: document.getElementById('smtp-from-email').value,
                from_name: document.getElementById('smtp-from-name').value,
                template: document.getElementById('smtp-template').value,
                smtp_enabled: 'yes'
            };
            apiFetch('/auth/smtp', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("SMTP diagnostics configuration saved successfully", "success");
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // Send test email
        document.getElementById('btn-smtp-test').addEventListener('click', () => {
            const email = document.getElementById('smtp-test-email').value;
            if (!email) {
                showToast("Enter test recipient email address", "warning");
                return;
            }
            document.getElementById('btn-smtp-test').innerText = 'Sending Diagnostic...';
            apiFetch('/auth/smtp/test', 'POST', { test_email: email }).then(res => {
                document.getElementById('btn-smtp-test').innerText = 'Send Test Mail';
                if (res.success) {
                    showToast(res.message, "success");
                } else {
                    showToast(res.message, "error");
                }
            });
        });
    </script>
</body>
</html>
