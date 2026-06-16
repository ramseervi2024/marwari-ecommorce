<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manufacturing Management ERP Portal">
    <title>Global Manufacturing ERP Portal</title>
    <!-- Modern Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg-primary: #0f0c1b;
            --bg-surface: #17132a;
            --bg-glass: rgba(23, 19, 42, 0.75);
            --border-glass: rgba(255, 255, 255, 0.08);
            --primary: #9d4edd;
            --primary-hover: #7b2cbf;
            --accent: #00f5d4;
            --danger: #ff5d8f;
            --warning: #ffb703;
            --success: #38b000;
            --text: #f3f0fc;
            --text-muted: #a097c4;
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
            background: radial-gradient(circle at 10% 20%, rgba(157, 78, 221, 0.2) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(0, 245, 212, 0.1) 0%, transparent 40%);
        }

        .auth-card {
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            backdrop-filter: blur(20px);
            padding: 40px;
            border-radius: 24px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
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
            box-shadow: 0 0 10px rgba(157, 78, 221, 0.2);
        }

        .btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
            border: none;
            color: #fff;
            padding: 14px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(157, 78, 221, 0.4);
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
            background: rgba(157, 78, 221, 0.15);
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
            color: #fff;
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
            background: rgba(157, 78, 221, 0.1);
        }

        .sidebar-item.active {
            background: linear-gradient(135deg, rgba(157, 78, 221, 0.15) 0%, rgba(0, 245, 212, 0.05) 100%);
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
            color: #fff;
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
            background: rgba(255, 93, 143, 0.1);
            border: 1px solid rgba(255, 93, 143, 0.2);
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
            box-shadow: 0 0 12px rgba(255, 93, 143, 0.4);
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
            color: var(--accent);
        }

        /* Forms Layout Grid */
        .mfg-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .mfg-grid {
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
        .status-badge.active { background: rgba(56, 176, 0, 0.15); color: var(--success); }
        .status-badge.pending { background: rgba(255, 183, 3, 0.15); color: var(--warning); }
        .status-badge.completed { background: rgba(56, 176, 0, 0.15); color: var(--success); }
        .status-badge.in-progress { background: rgba(157, 78, 221, 0.15); color: var(--primary); }
        .status-badge.cancelled { background: rgba(255, 93, 143, 0.15); color: var(--danger); }

        .progress-bar-container {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
            margin-top: 6px;
        }
        .progress-bar {
            height: 100%;
            background: var(--primary);
            border-radius: 10px;
        }

        .mfg-plan-btn {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        .mfg-plan-btn:hover {
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
                <h2>Manufacturing ERP</h2>
                <p>Global Industrial Production Coordinator Portal</p>
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
                    <span class="preset-badge" onclick="fillPreset('mfgsuperadmin', '123456')">Super Admin</span>
                    <span class="preset-badge" onclick="fillPreset('mfg_production', 'productionpass123')">Production Mgr</span>
                    <span class="preset-badge" onclick="fillPreset('mfg_purchase', 'purchasepass123')">Purchase Mgr</span>
                    <span class="preset-badge" onclick="fillPreset('mfg_store', 'storepass123')">Store Mgr</span>
                    <span class="preset-badge" onclick="fillPreset('mfg_quality', 'qualitypass123')">Quality Inspector</span>
                    <span class="preset-badge" onclick="fillPreset('mfg_dispatch', 'dispatchpass123')">Dispatch Mgr</span>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN SHELL PORTAL -->
    <div id="dashboard-shell">
        <aside>
            <div class="brand-section">
                <div class="brand-logo">MFG</div>
                <div class="brand-name">MFG ERP</div>
            </div>
            
            <nav>
                <div class="nav-category">Main Operations</div>
                <div class="sidebar-item active" data-tab="dashboard-overview">Dashboard overview</div>
                <div class="sidebar-item" data-tab="production-bom">Production & BOM</div>
                <div class="sidebar-item" data-tab="raw-stock-po">Raw Materials & POs</div>
                <div class="sidebar-item" data-tab="outsourced-jobs">Job Work (Outsourced)</div>
                <div class="sidebar-item" data-tab="machinery-repairs">Machinery & Repairs</div>
                <div class="sidebar-item" data-tab="quality-inspections">Quality Inspector</div>
                <div class="sidebar-item" data-tab="logistics-dispatch">Logistics & Dispatch</div>

                <div class="nav-category">System Admin</div>
                <div class="sidebar-item admin-only" data-tab="diagnostics-users" style="display: none;">Diagnostics & Users</div>
            </nav>

            <div class="user-profile-section">
                <div class="avatar" id="user-avatar-initials">AD</div>
                <div class="user-info">
                    <div class="user-name" id="user-display-name">Super Admin</div>
                    <div class="user-role" id="user-display-role">superadmin</div>
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
                    <p>Live Industrial Manufacturing Management ERP</p>
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
                        <span class="kpi-title">Production Today</span>
                        <span class="kpi-value" id="kpi-production-today">0</span>
                        <span class="kpi-detail">Units Produced</span>
                    </div>
                    <div class="kpi-card">
                        <span class="kpi-title">Pending Work Orders</span>
                        <span class="kpi-value" id="kpi-pending-wo">0</span>
                        <span class="kpi-detail">Scheduled Jobs</span>
                    </div>
                    <div class="kpi-card">
                        <span class="kpi-title">Raw Stocks Value</span>
                        <span class="kpi-value" id="kpi-raw-value">₹0.00</span>
                        <span class="kpi-detail">Current Valuation</span>
                    </div>
                    <div class="kpi-card">
                        <span class="kpi-title">Monthly Revenue</span>
                        <span class="kpi-value" id="kpi-monthly-rev">₹0.00</span>
                        <span class="kpi-detail">Dispatched Orders</span>
                    </div>
                </div>

                <div class="mfg-grid">
                    <div class="dashboard-content-panel">
                        <div class="panel-header">
                            <h3>Production Volume Trends</h3>
                        </div>
                        <canvas id="production-chart" style="max-height: 250px; width:100%;"></canvas>
                    </div>
                    <div class="dashboard-content-panel">
                        <div class="panel-header">
                            <h3>Machinery & Inventory Alerts</h3>
                        </div>
                        <div style="display:flex; flex-direction:column; gap:12px;">
                            <div style="background:rgba(255, 93, 143, 0.1); border:1px solid rgba(255,93,143,0.2); padding:15px; border-radius:12px; display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <h4 style="color:var(--danger); font-size:14px; font-weight:700;">Low Stock Materials</h4>
                                    <p style="font-size:12px; color:var(--text-muted); margin-top:2px;">Reorder immediately</p>
                                </div>
                                <span style="background:var(--danger); color:#fff; font-weight:800; font-size:12px; padding:4px 8px; border-radius:6px;" id="badge-low-stock">0</span>
                            </div>
                            
                            <div style="background:rgba(255, 183, 3, 0.1); border:1px solid rgba(255,183,3,0.2); padding:15px; border-radius:12px; display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <h4 style="color:var(--warning); font-size:14px; font-weight:700;">Machines Due Repair</h4>
                                    <p style="font-size:12px; color:var(--text-muted); margin-top:2px;">Scheduled within 10 days</p>
                                </div>
                                <span style="background:var(--warning); color:#000; font-weight:800; font-size:12px; padding:4px 8px; border-radius:6px;" id="badge-machines-due">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. PRODUCTION & BOM TAB -->
            <div class="tab-panel" id="tab-production-bom">
                <div class="panel-header" style="margin-bottom:20px;">
                    <h3 style="font-size:20px;">Bill of Materials & Production Scheduling</h3>
                    <div style="display:flex; gap:10px;">
                        <button class="mfg-plan-btn" onclick="openModal('modal-create-bom')">Create BOM Formula</button>
                        <button class="mfg-plan-btn" onclick="openModal('modal-create-plan')">Create Production Plan</button>
                        <button class="mfg-plan-btn" onclick="openModal('modal-create-wo')">Create Work Order</button>
                    </div>
                </div>

                <div class="mfg-grid">
                    <div>
                        <div class="dashboard-content-panel">
                            <h3 style="margin-bottom:15px;">Active Work Orders Scheduling</h3>
                            <div style="overflow-x:auto;">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>WO Number</th>
                                            <th>Product Name</th>
                                            <th>Target Qty</th>
                                            <th>Start Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="wo-list-tbody">
                                        <!-- Dynamic JS injection -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="dashboard-content-panel">
                            <h3 style="margin-bottom:15px;">Active Production Plans</h3>
                            <div style="overflow-x:auto;">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Plan Number</th>
                                            <th>Product Code</th>
                                            <th>Target Qty</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="plan-list-tbody">
                                        <!-- Dynamic JS injection -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="dashboard-content-panel">
                            <h3 style="margin-bottom:15px;">BOM Formulations Catalog</h3>
                            <div id="bom-list-container" style="display:flex; flex-direction:column; gap:10px;">
                                <!-- Dynamic JS injection -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. RAW STOCK & POs TAB -->
            <div class="tab-panel" id="tab-raw-stock-po">
                <div class="panel-header" style="margin-bottom:20px;">
                    <h3 style="font-size:20px;">Raw Material Levels & Restocking POs</h3>
                    <div style="display:flex; gap:10px;">
                        <button class="mfg-plan-btn" onclick="openModal('modal-create-material')">Create Material</button>
                        <button class="mfg-plan-btn" onclick="openModal('modal-create-supplier')">Add Supplier</button>
                        <button class="mfg-plan-btn" onclick="openModal('modal-create-po')">Book Purchase Order</button>
                    </div>
                </div>

                <div class="mfg-grid">
                    <div class="dashboard-content-panel">
                        <h3 style="margin-bottom:15px;">Raw Materials stock status</h3>
                        <div style="overflow-x:auto;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Material Name</th>
                                        <th>Current Stock</th>
                                        <th>Safety stock Level</th>
                                        <th>Purchase Cost</th>
                                    </tr>
                                </thead>
                                <tbody id="raw-stock-tbody">
                                    <!-- Dynamic JS injection -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="dashboard-content-panel">
                        <h3 style="margin-bottom:15px;">Restocking PO Logs</h3>
                        <div style="display:flex; flex-direction:column; gap:10px; max-height: 500px; overflow-y:auto;" id="po-list-container">
                            <!-- Dynamic PO elements -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. OUTSOURCED JOBS TAB -->
            <div class="tab-panel" id="tab-outsourced-jobs">
                <div class="panel-header" style="margin-bottom:20px;">
                    <h3 style="font-size:20px;">Outsourced Manufacturing & Job Work</h3>
                    <button class="mfg-plan-btn" onclick="openModal('modal-create-job')">Book Job Work Dispatch</button>
                </div>

                <div class="dashboard-content-panel">
                    <h3 style="margin-bottom:15px;">Active Outsource Vendor Jobs</h3>
                    <div style="overflow-x:auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Job Number</th>
                                    <th>Vendor / Supplier</th>
                                    <th>Product Name</th>
                                    <th>Qty Send</th>
                                    <th>Job Cost</th>
                                    <th>Expected Return</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="job-work-tbody">
                                <!-- Dynamic JS injection -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 5. MACHINERY & REPAIRS TAB -->
            <div class="tab-panel" id="tab-machinery-repairs">
                <div class="panel-header" style="margin-bottom:20px;">
                    <h3 style="font-size:20px;">Factory Machinery & Repair Schedules</h3>
                    <button class="mfg-plan-btn" onclick="openModal('modal-create-machine')">Register Machinery</button>
                </div>

                <div class="mfg-grid">
                    <div class="dashboard-content-panel">
                        <h3 style="margin-bottom:15px;">Industrial Machines catalog</h3>
                        <div style="overflow-x:auto;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Machine Code</th>
                                        <th>Machine Name</th>
                                        <th>Capacity</th>
                                        <th>Maintenance Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="machine-list-tbody">
                                    <!-- Dynamic JS injection -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="dashboard-content-panel">
                        <h3 style="margin-bottom:15px;">Log Machine Production run</h3>
                        <form id="form-log-production">
                            <div class="form-group">
                                <label>Work Order ID</label>
                                <select class="form-input" id="log-wo-id" required></select>
                            </div>
                            <div class="form-group">
                                <label>Finished Product ID</label>
                                <select class="form-input" id="log-product-id" required></select>
                            </div>
                            <div class="form-group">
                                <label>Quantity Produced</label>
                                <input type="number" step="0.01" class="form-input" id="log-qty" required>
                            </div>
                            <div class="form-group">
                                <label>Total Production Cost (INR)</label>
                                <input type="number" class="form-input" id="log-cost" value="1200" required>
                            </div>
                            <div class="form-group">
                                <label>Active Machinery</label>
                                <select class="form-input" id="log-machine-id" required></select>
                            </div>
                            <div class="form-group">
                                <label>Operator / Supervisor Name</label>
                                <input type="text" class="form-input" id="log-operator" placeholder="e.g. Ramesh Seervi" required>
                            </div>
                            <button type="submit" class="btn">Register Machine Run Output</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 6. QUALITY INSPECTOR TAB -->
            <div class="tab-panel" id="tab-quality-inspections">
                <div class="panel-header" style="margin-bottom:20px;">
                    <h3 style="font-size:20px;">Quality Control & Inspections</h3>
                </div>

                <div class="mfg-grid">
                    <div class="dashboard-content-panel">
                        <h3 style="margin-bottom:15px;">Inspection Reports Database</h3>
                        <div style="overflow-x:auto;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Inspect No</th>
                                        <th>WO Reference</th>
                                        <th>Product Name</th>
                                        <th>Approved Qty</th>
                                        <th>Rejected Qty</th>
                                        <th>Inspection Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="quality-list-tbody">
                                    <!-- Dynamic JS injection -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="dashboard-content-panel">
                        <h3 style="margin-bottom:15px;">Record Quality Check</h3>
                        <form id="form-quality-check">
                            <div class="form-group">
                                <label>Inspection Number</label>
                                <input type="text" class="form-input" id="qc-number" placeholder="e.g. QC-001" required>
                            </div>
                            <div class="form-group">
                                <label>Work Order Reference</label>
                                <select class="form-input" id="qc-wo-id" required></select>
                            </div>
                            <div class="form-group">
                                <label>Product Catalog</label>
                                <select class="form-input" id="qc-product-id" required></select>
                            </div>
                            <div class="form-group">
                                <label>Approved Units (To finished Stock)</label>
                                <input type="number" step="0.01" class="form-input" id="qc-approved" required>
                            </div>
                            <div class="form-group">
                                <label>Rejected Defective Units</label>
                                <input type="number" step="0.01" class="form-input" id="qc-rejected" required>
                            </div>
                            <div class="form-group">
                                <label>Inspector Remarks / Defect Code</label>
                                <textarea class="form-input" id="qc-remarks" placeholder="Add defects description notes..." rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn" style="background:linear-gradient(135deg, var(--accent) 0%, var(--primary) 100%); color:#000; font-weight:800;">Register Inspection Results</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 7. LOGISTICS & DISPATCH TAB -->
            <div class="tab-panel" id="tab-logistics-dispatch">
                <div class="panel-header" style="margin-bottom:20px;">
                    <h3 style="font-size:20px;">Finished Goods Logistics & Dispatch</h3>
                    <button class="mfg-plan-btn" onclick="openModal('modal-create-dispatch')">Dispatch Product Shipment</button>
                </div>

                <div class="mfg-grid">
                    <div class="dashboard-content-panel">
                        <h3 style="margin-bottom:15px;">Finished Goods stock balances</h3>
                        <div style="overflow-x:auto;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Product Code</th>
                                        <th>Product Name</th>
                                        <th>Finished Quantity</th>
                                        <th>Warehouse Storage</th>
                                        <th>Selling Price</th>
                                    </tr>
                                </thead>
                                <tbody id="finished-stock-tbody">
                                    <!-- Dynamic FG injection -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="dashboard-content-panel">
                        <h3 style="margin-bottom:15px;">Logged Shipments Dispatches</h3>
                        <div style="display:flex; flex-direction:column; gap:10px; max-height:500px; overflow-y:auto;" id="dispatch-list-container">
                            <!-- Dynamic dispatches -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- 8. SYSTEM ADMIN & DIAGNOSTICS TAB -->
            <div class="tab-panel" id="tab-diagnostics-users">
                <div class="panel-header" style="margin-bottom:20px;">
                    <h3 style="font-size:20px;">Diagnostics & Operator Approvals</h3>
                </div>

                <div class="mfg-grid">
                    <div class="dashboard-content-panel">
                        <h3 style="margin-bottom:15px;">Factory Operators Registry</h3>
                        <div style="overflow-x:auto;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Operator</th>
                                        <th>Email</th>
                                        <th>Requested Role</th>
                                        <th>Status</th>
                                        <th>Toggle action</th>
                                    </tr>
                                </thead>
                                <tbody id="admin-users-tbody">
                                    <!-- Dynamic users list -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="dashboard-content-panel">
                        <h3 style="margin-bottom:15px;">SMTP Mail configuration settings</h3>
                        <form id="form-admin-smtp">
                            <div class="form-group">
                                <label>Hostinger SMTP Host</label>
                                <input type="text" class="form-input" id="smtp-host" placeholder="smtp.hostinger.com">
                            </div>
                            <div class="form-group">
                                <label>Port</label>
                                <input type="text" class="form-input" id="smtp-port" placeholder="587">
                            </div>
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" class="form-input" id="smtp-user" placeholder="no-reply@domain.com">
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" class="form-input" id="smtp-pass" placeholder="••••••••">
                            </div>
                            <div class="form-group">
                                <label>SMTP Security</label>
                                <select class="form-input" id="smtp-encryption">
                                    <option value="tls">TLS</option>
                                    <option value="ssl">SSL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Sender Address (From Email)</label>
                                <input type="email" class="form-input" id="smtp-from-email" placeholder="no-reply@domain.com">
                            </div>
                            <div class="form-group">
                                <label>Sender Label (FromName)</label>
                                <input type="text" class="form-input" id="smtp-from-name" placeholder="Global Manufacturing ERP">
                            </div>
                            
                            <div class="form-group">
                                <label>Activation Mail Template</label>
                                <textarea class="form-input" id="smtp-template" rows="3"></textarea>
                            </div>

                            <button type="submit" class="btn" style="margin-bottom:12px;">Save SMTP Configurations</button>
                        </form>
                        
                        <div style="border-top:1px solid var(--border-glass); margin-top:20px; padding-top:20px;">
                            <h4 style="font-size:13px; font-weight:700; margin-bottom:12px;">Test SMTP dispatch diagnostics</h4>
                            <div class="form-group">
                                <input type="email" class="form-input" id="smtp-test-email" placeholder="Recipient address...">
                            </div>
                            <button type="button" class="btn" id="btn-smtp-test" style="background:rgba(0, 245, 212, 0.1); border:1px solid rgba(0, 245, 212, 0.2); color:var(--accent);">Send Diagnostic Test Mail</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL WINDOWS LIST -->
    <!-- A. Create BOM Modal -->
    <div class="modal-overlay" id="modal-create-bom">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-bom')">&times;</button>
            <h3 style="margin-bottom:20px;">Register BOM Formula</h3>
            <form id="form-create-bom">
                <div class="form-group">
                    <label>Product Code / Name</label>
                    <select class="form-input" id="bom-product-id" required></select>
                </div>
                <div class="form-group">
                    <label>Raw Material Input</label>
                    <select class="form-input" id="bom-material-id" required></select>
                </div>
                <div class="form-group">
                    <label>Required Quantity</label>
                    <input type="number" step="0.0001" class="form-input" id="bom-qty" required>
                </div>
                <div class="form-group">
                    <label>Unit</label>
                    <input type="text" class="form-input" id="bom-unit" placeholder="e.g. KG, LTR, PCS" required>
                </div>
                <button type="submit" class="btn">Save BOM Formulation</button>
            </form>
        </div>
    </div>

    <!-- B. Create Plan Modal -->
    <div class="modal-overlay" id="modal-create-plan">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-plan')">&times;</button>
            <h3 style="margin-bottom:20px;">Create Production Plan</h3>
            <form id="form-create-plan">
                <div class="form-group">
                    <label>Plan reference Number</label>
                    <input type="text" class="form-input" id="plan-number" placeholder="e.g. PLAN-001" required>
                </div>
                <div class="form-group">
                    <label>Product</label>
                    <select class="form-input" id="plan-product-id" required></select>
                </div>
                <div class="form-group">
                    <label>Target planned Quantity</label>
                    <input type="number" class="form-input" id="plan-qty" required>
                </div>
                <div class="form-group">
                    <label>Priority</label>
                    <select class="form-input" id="plan-priority">
                        <option value="HIGH">HIGH</option>
                        <option value="MEDIUM">MEDIUM</option>
                        <option value="LOW">LOW</option>
                    </select>
                </div>
                <button type="submit" class="btn">Create Production Schedule</button>
            </form>
        </div>
    </div>

    <!-- C. Create Work Order Modal -->
    <div class="modal-overlay" id="modal-create-wo">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-wo')">&times;</button>
            <h3 style="margin-bottom:20px;">Create Work Order</h3>
            <form id="form-create-wo">
                <div class="form-group">
                    <label>Work Order Number</label>
                    <input type="text" class="form-input" id="wo-number" placeholder="e.g. WO-001" required>
                </div>
                <div class="form-group">
                    <label>Product Code</label>
                    <select class="form-input" id="wo-product-id" required></select>
                </div>
                <div class="form-group">
                    <label>Volume Quantity</label>
                    <input type="number" class="form-input" id="wo-qty" required>
                </div>
                <button type="submit" class="btn">Create Work Order</button>
            </form>
        </div>
    </div>

    <!-- D. Create Raw Material Modal -->
    <div class="modal-overlay" id="modal-create-material">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-material')">&times;</button>
            <h3 style="margin-bottom:20px;">Add Raw Material Input</h3>
            <form id="form-create-material">
                <div class="form-group">
                    <label>Material SKU Code</label>
                    <input type="text" class="form-input" id="mat-code" placeholder="e.g. RAW-STL-101" required>
                </div>
                <div class="form-group">
                    <label>Material Name</label>
                    <input type="text" class="form-input" id="mat-name" placeholder="Stainless Steel Sheet" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" class="form-input" id="mat-category" placeholder="Metals" required>
                </div>
                <div class="form-group">
                    <label>Stock Unit</label>
                    <input type="text" class="form-input" id="mat-unit" placeholder="KG" required>
                </div>
                <div class="form-group">
                    <label>Minimum Stock Safety</label>
                    <input type="number" class="form-input" id="mat-min" required>
                </div>
                <div class="form-group">
                    <label>Initial stock balance</label>
                    <input type="number" class="form-input" id="mat-stock" required>
                </div>
                <div class="form-group">
                    <label>Estimated purchase rate (INR)</label>
                    <input type="number" class="form-input" id="mat-price" required>
                </div>
                <button type="submit" class="btn">Register Raw Material</button>
            </form>
        </div>
    </div>

    <!-- E. Add Supplier Modal -->
    <div class="modal-overlay" id="modal-create-supplier">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-supplier')">&times;</button>
            <h3 style="margin-bottom:20px;">Add Supplier Profile</h3>
            <form id="form-create-supplier">
                <div class="form-group">
                    <label>Supplier / Company Name</label>
                    <input type="text" class="form-input" id="sup-name" required>
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" class="form-input" id="sup-mobile" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" class="form-input" id="sup-email" required>
                </div>
                <div class="form-group">
                    <label>GSTIN Identifier</label>
                    <input type="text" class="form-input" id="sup-gst" placeholder="07AAAAA1111A1Z1" required>
                </div>
                <button type="submit" class="btn">Save Supplier Profile</button>
            </form>
        </div>
    </div>

    <!-- F. Add PO Modal -->
    <div class="modal-overlay" id="modal-create-po">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-po')">&times;</button>
            <h3 style="margin-bottom:20px;">Book Purchase Order</h3>
            <form id="form-create-po">
                <div class="form-group">
                    <label>PO Number Reference</label>
                    <input type="text" class="form-input" id="po-number" placeholder="e.g. PO-001" required>
                </div>
                <div class="form-group">
                    <label>Vendor / Supplier</label>
                    <select class="form-input" id="po-supplier-id" required></select>
                </div>
                <div class="form-group">
                    <label>Raw Material Code</label>
                    <select class="form-input" id="po-material-id" required></select>
                </div>
                <div class="form-group">
                    <label>Quantity to order</label>
                    <input type="number" class="form-input" id="po-qty" required>
                </div>
                <div class="form-group">
                    <label>Rate per unit (INR)</label>
                    <input type="number" class="form-input" id="po-rate" placeholder="Leave blank to use default">
                </div>
                <button type="submit" class="btn">Submit Purchase Order</button>
            </form>
        </div>
    </div>

    <!-- G. Add Job Work Modal -->
    <div class="modal-overlay" id="modal-create-job">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-job')">&times;</button>
            <h3 style="margin-bottom:20px;">Book Outsourced Job Work</h3>
            <form id="form-create-job">
                <div class="form-group">
                    <label>Job Number Reference</label>
                    <input type="text" class="form-input" id="job-number" placeholder="e.g. JW-001" required>
                </div>
                <div class="form-group">
                    <label>Outsource Vendor</label>
                    <select class="form-input" id="job-vendor-id" required></select>
                </div>
                <div class="form-group">
                    <label>Target Product</label>
                    <select class="form-input" id="job-product-id" required></select>
                </div>
                <div class="form-group">
                    <label>Quantity Dispatch</label>
                    <input type="number" class="form-input" id="job-qty" required>
                </div>
                <div class="form-group">
                    <label>Outsource Job Cost (INR)</label>
                    <input type="number" class="form-input" id="job-cost" value="450" required>
                </div>
                <button type="submit" class="btn">Dispatch Job Work</button>
            </form>
        </div>
    </div>

    <!-- H. Register Machine Modal -->
    <div class="modal-overlay" id="modal-create-machine">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-machine')">&times;</button>
            <h3 style="margin-bottom:20px;">Register Industrial Machine</h3>
            <form id="form-create-machine">
                <div class="form-group">
                    <label>Machine Code</label>
                    <input type="text" class="form-input" id="mac-code" placeholder="e.g. MAC-CNC-04" required>
                </div>
                <div class="form-group">
                    <label>Machine Name</label>
                    <input type="text" class="form-input" id="mac-name" placeholder="Laser Cutting Machine" required>
                </div>
                <div class="form-group">
                    <label>Capacity</label>
                    <input type="text" class="form-input" id="mac-capacity" placeholder="e.g. 50 cuts/min" required>
                </div>
                <button type="submit" class="btn">Register Machine</button>
            </form>
        </div>
    </div>

    <!-- I. Book Dispatch Modal -->
    <div class="modal-overlay" id="modal-create-dispatch">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-dispatch')">&times;</button>
            <h3 style="margin-bottom:20px;">Dispatch Finished Goods Shipment</h3>
            <form id="form-create-dispatch">
                <div class="form-group">
                    <label>Dispatch Document Number</label>
                    <input type="text" class="form-input" id="disp-number" placeholder="e.g. DSP-001" required>
                </div>
                <div class="form-group">
                    <label>Customer ID</label>
                    <input type="number" class="form-input" id="disp-cust-id" value="101" required>
                </div>
                <div class="form-group">
                    <label>Product</label>
                    <select class="form-input" id="disp-product-id" required></select>
                </div>
                <div class="form-group">
                    <label>Quantity to dispatch</label>
                    <input type="number" class="form-input" id="disp-qty" required>
                </div>
                <div class="form-group">
                    <label>Shipment Vehicle Number</label>
                    <input type="text" class="form-input" id="disp-vehicle" placeholder="e.g. DL-1CA-1234" required>
                </div>
                <div class="form-group">
                    <label>Driver Name</label>
                    <input type="text" class="form-input" id="disp-driver" placeholder="e.g. Ramesh Kumar" required>
                </div>
                <button type="submit" class="btn">Book Dispatch Shipment</button>
            </form>
        </div>
    </div>

    <!-- DYNAMIC JAVASCRIPT LOGIC -->
    <script>
        const API_NAMESPACE = '/wp-json/manufacturing-management/v1';
        let token = localStorage.getItem('mfg_token') || '';
        let user = null;
        try {
            const cachedUser = localStorage.getItem('mfg_user');
            if (cachedUser) {
                user = JSON.parse(cachedUser);
            }
        } catch (e) {
            console.error("Cached user JSON corrupt.");
        }

        // Global arrays
        let rawMaterials = [];
        let finishedGoods = [];
        let workOrders = [];
        let suppliers = [];
        let machines = [];

        // Initialize clock display
        setInterval(() => {
            const now = new Date();
            document.getElementById('clock-display').innerText = now.toTimeString().split(' ')[0];
        }, 1000);

        window.addEventListener('DOMContentLoaded', () => {
            initApp();
        });

        function initApp() {
            if (token) {
                apiFetch('/auth/me', 'GET')
                    .then(res => {
                        if (res.success) {
                            user = res.data;
                            localStorage.setItem('mfg_user', JSON.stringify(user));
                            showShell();
                        } else {
                            logout();
                        }
                    })
                    .catch(() => logout());
            } else {
                showLogin();
            }
        }

        function showLogin() {
            document.getElementById('page-loader').style.display = 'none';
            document.getElementById('login-view').style.display = 'flex';
            document.getElementById('dashboard-shell').style.display = 'none';
        }

        function showShell() {
            document.getElementById('page-loader').style.display = 'none';
            document.getElementById('login-view').style.display = 'none';
            document.getElementById('dashboard-shell').style.display = 'flex';

            document.getElementById('user-display-name').innerText = user.name;
            document.getElementById('user-display-role').innerText = user.role.replace('mfg_', '').replace('_', ' ');
            document.getElementById('user-avatar-initials').innerText = user.name.substring(0, 2).toUpperCase();

            // Set Admin privileges
            if (user.role === 'mfg_super_admin' || user.role === 'administrator') {
                const adminMenus = document.querySelectorAll('.admin-only');
                adminMenus.forEach(el => el.style.display = 'block');
            }

            setupTabs();
            // Start in last active tab or default to dashboard overview
            const activeTab = localStorage.getItem('mfg_active_tab') || 'dashboard-overview';
            switchTab(activeTab);

            // Fetch initial options
            fetchCatalogOptions();
        }

        function setupTabs() {
            const items = document.querySelectorAll('.sidebar-item');
            items.forEach(item => {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    const tabName = item.getAttribute('data-tab');
                    switchTab(tabName);
                });
            });
        }

        function switchTab(tabName) {
            const items = document.querySelectorAll('.sidebar-item');
            const panels = document.querySelectorAll('.tab-panel');
            
            items.forEach(el => el.classList.remove('active'));
            panels.forEach(el => el.classList.remove('active'));

            const targetItem = document.querySelector(`.sidebar-item[data-tab="${tabName}"]`);
            if (targetItem) targetItem.classList.add('active');

            const targetPanel = document.getElementById(`tab-${tabName}`);
            if (targetPanel) targetPanel.classList.add('active');

            document.getElementById('current-tab-title').innerText = tabName.replace('-', ' ').toUpperCase();
            localStorage.setItem('mfg_active_tab', tabName);

            // Refresh specific tables
            if (tabName === 'dashboard-overview') fetchDashboardStats();
            if (tabName === 'production-bom') fetchProductionBomData();
            if (tabName === 'raw-stock-po') fetchRawStockPoData();
            if (tabName === 'outsourced-jobs') fetchOutsourceJobsData();
            if (tabName === 'machinery-repairs') fetchMachineryRepairsData();
            if (tabName === 'quality-inspections') fetchQualityInspectionsData();
            if (tabName === 'logistics-dispatch') fetchLogisticsDispatchData();
            if (tabName === 'diagnostics-users') fetchDiagnosticsAdminData();
        }

        // apiFetch Helper Wrapper
        async function apiFetch(endpoint, method = 'GET', body = null) {
            let url = API_NAMESPACE + endpoint;
            if (token) {
                const separator = url.includes('?') ? '&' : '?';
                url += separator + 'token=' + encodeURIComponent(token);
            }
            const headers = {
                'Content-Type': 'application/json'
            };
            if (token) {
                headers['Authorization'] = 'Bearer ' + token;
                headers['X-Authorization'] = 'Bearer ' + token;
            }

            const config = {
                method: method,
                headers: headers
            };

            if (body && method !== 'GET') {
                config.body = JSON.stringify(body);
            }

            try {
                const response = await fetch(url, config);
                const data = await response.json();
                return data;
            } catch (err) {
                console.error("Fetch Exception: ", err);
            }
        }

        // Auth Submit handler
        let isOtpMode = false;
        const loginPasswordGroup = document.getElementById('login-pass-group');
        const loginOtpGroup = document.getElementById('login-otp-group');
        const btnSendOtp = document.getElementById('btn-send-otp');

        btnSendOtp.addEventListener('click', () => {
            const username = document.getElementById('login-username').value;
            if (!username) {
                showToast("Please enter username or email to send code.", "warning");
                return;
            }
            btnSendOtp.innerText = 'Sending Code...';
            btnSendOtp.disabled = true;

            apiFetch('/auth/login/initiate', 'POST', { username: username })
                .then(res => {
                    if (res.success) {
                        showToast(res.message, "success");
                        loginPasswordGroup.style.display = 'none';
                        loginOtpGroup.style.display = 'block';
                        document.getElementById('btn-login-submit').innerText = 'Login with OTP';
                        isOtpMode = true;
                    } else {
                        showToast(res.message, "error");
                        btnSendOtp.innerText = 'Request OTP Code';
                        btnSendOtp.disabled = false;
                    }
                });
        });

        document.getElementById('login-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const username = document.getElementById('login-username').value;
            const payload = { username: username };
            
            if (isOtpMode) {
                payload.otp = document.getElementById('login-otp').value;
            } else {
                payload.password = document.getElementById('login-password').value;
            }

            apiFetch('/auth/login', 'POST', payload)
                .then(res => {
                    if (res.success) {
                        token = res.data.token;
                        user = res.data.user;
                        localStorage.setItem('mfg_token', token);
                        localStorage.setItem('mfg_user', JSON.stringify(user));
                        showToast("Access Granted. Logging in...", "success");
                        showShell();
                    } else {
                        showToast(res.message, "error");
                    }
                });
        });

        document.getElementById('btn-logout').addEventListener('click', () => {
            logout();
        });

        function logout() {
            // Clear client session immediately so logout is instant and never gets stuck
            token = '';
            user = null;
            localStorage.removeItem('mfg_token');
            localStorage.removeItem('mfg_user');
            localStorage.removeItem('mfg_active_tab');
            showLogin();

            // Asynchronously notify server in background
            apiFetch('/auth/logout', 'POST').catch(err => {
                console.log("Logged out locally. Server logout notification failed: ", err);
            });
        }

        function fillPreset(username, password) {
            document.getElementById('login-username').value = username;
            document.getElementById('login-password').value = password;
            loginPasswordGroup.style.display = 'block';
            loginOtpGroup.style.display = 'none';
            document.getElementById('btn-login-submit').innerText = 'Login with Password';
            isOtpMode = false;
            btnSendOtp.innerText = 'Request OTP Code';
            btnSendOtp.disabled = false;
        }

        // Modal triggers
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }
        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        // Toast message banner
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const el = document.createElement('div');
            el.className = `toast ${type}`;
            el.innerHTML = `<span>${message}</span><button style="background:transparent; border:none; color:#fff; cursor:pointer;" onclick="this.parentElement.remove()">&times;</button>`;
            container.appendChild(el);
            setTimeout(() => el.remove(), 4000);
        }

        // Dropdowns Catalog fetcher
        function fetchCatalogOptions() {
            apiFetch('/raw-materials').then(res => { if (res.success) rawMaterials = res.data; populateDropdowns(); });
            apiFetch('/finished-goods').then(res => { if (res.success) finishedGoods = res.data; populateDropdowns(); });
            apiFetch('/suppliers').then(res => { if (res.success) suppliers = res.data; populateDropdowns(); });
            apiFetch('/machines').then(res => { if (res.success) machines = res.data; populateDropdowns(); });
        }

        function populateDropdowns() {
            // Populate Products
            const productDropdowns = ['bom-product-id', 'plan-product-id', 'wo-product-id', 'log-product-id', 'qc-product-id', 'job-product-id', 'disp-product-id'];
            productDropdowns.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.innerHTML = finishedGoods.map(p => `<option value="${p.id}">${p.product_name} (${p.product_code})</option>`).join('');
                }
            });

            // Populate Materials
            const rawDropdowns = ['bom-material-id', 'po-material-id'];
            rawDropdowns.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.innerHTML = rawMaterials.map(m => `<option value="${m.id}">${m.material_name} (${m.material_code})</option>`).join('');
                }
            });

            // Populate Suppliers
            const supplierDropdowns = ['po-supplier-id', 'job-vendor-id'];
            supplierDropdowns.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.innerHTML = suppliers.map(s => `<option value="${s.id}">${s.supplier_name}</option>`).join('');
                }
            });

            // Populate Machines
            const macEl = document.getElementById('log-machine-id');
            if (macEl) {
                macEl.innerHTML = machines.map(m => `<option value="${m.id}">${m.machine_name} (${m.machine_code})</option>`).join('');
            }
        }

        // Fetch Dashboard stats
        let dashboardChartInstance = null;
        function fetchDashboardStats() {
            apiFetch('/dashboard').then(res => {
                if (res.success) {
                    const c = res.data.counters;
                    document.getElementById('kpi-production-today').innerText = c.production_today;
                    document.getElementById('kpi-pending-wo').innerText = c.pending_wo;
                    document.getElementById('kpi-raw-value').innerText = '₹' + c.production_cost.toFixed(2);
                    document.getElementById('kpi-monthly-rev').innerText = '₹' + c.monthly_revenue.toFixed(2);
                    
                    document.getElementById('badge-low-stock').innerText = c.low_stock_raw;
                    document.getElementById('badge-machines-due').innerText = c.machines_maintenance;

                    // Load Production trends chart
                    const ctx = document.getElementById('production-chart').getContext('2d');
                    if (dashboardChartInstance) {
                        dashboardChartInstance.destroy();
                    }
                    dashboardChartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: res.data.trends.production.labels,
                            datasets: [{
                                label: 'Production Units',
                                data: res.data.trends.production.data,
                                borderColor: '#9d4edd',
                                backgroundColor: 'rgba(157, 78, 221, 0.1)',
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#a097c4' } },
                                x: { grid: { display: false }, ticks: { color: '#a097c4' } }
                            }
                        }
                    });
                }
            });
        }

        // TAB 2: Production and BOM Fetcher
        function fetchProductionBomData() {
            // Fetch Work Orders
            apiFetch('/work-orders').then(res => {
                if (res.success) {
                    workOrders = res.data;
                    const tbody = document.getElementById('wo-list-tbody');
                    tbody.innerHTML = workOrders.map(wo => `
                        <tr>
                            <td><strong>${wo.work_order_number}</strong></td>
                            <td>${wo.product_name}</td>
                            <td>${wo.quantity} units</td>
                            <td>${wo.start_date || 'Not Started'}</td>
                            <td><span class="status-badge ${wo.status.toLowerCase().replace(' ', '-')}">${wo.status}</span></td>
                            <td>
                                ${wo.status === 'PENDING' ? `
                                    <button class="mfg-plan-btn" style="padding: 4px 8px; font-size:11px;" onclick="updateWorkOrderStatus(${wo.id}, 'In Progress')">Start Run</button>
                                ` : ''}
                                ${wo.status === 'In Progress' ? `
                                    <button class="mfg-plan-btn" style="padding: 4px 8px; font-size:11px; background:var(--success);" onclick="updateWorkOrderStatus(${wo.id}, 'Completed')">Complete</button>
                                ` : ''}
                            </td>
                        </tr>
                    `).join('');

                    // Populate machine run Work order dropdown
                    const select = document.getElementById('log-wo-id');
                    if (select) {
                        select.innerHTML = workOrders.map(w => `<option value="${w.id}">${w.work_order_number}</option>`).join('');
                    }
                    // Populate Quality check Work order dropdown
                    const qSelect = document.getElementById('qc-wo-id');
                    if (qSelect) {
                        qSelect.innerHTML = workOrders.map(w => `<option value="${w.id}">${w.work_order_number}</option>`).join('');
                    }
                }
            });

            // Fetch Plans
            apiFetch('/production-plans').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('plan-list-tbody');
                    tbody.innerHTML = res.data.map(p => `
                        <tr>
                            <td><strong>${p.plan_number}</strong></td>
                            <td>${p.product_code}</td>
                            <td>${p.planned_quantity} units</td>
                            <td><span class="status-badge" style="background:rgba(255,255,255,0.05); color:#fff;">${p.priority}</span></td>
                            <td><span class="status-badge ${p.status.toLowerCase()}">${p.status}</span></td>
                        </tr>
                    `).join('');
                }
            });

            // Fetch BOM Formulations
            apiFetch('/bom').then(res => {
                if (res.success) {
                    const container = document.getElementById('bom-list-container');
                    container.innerHTML = res.data.map(b => `
                        <div style="background:rgba(255,255,255,0.02); border:1px solid var(--border-glass); padding:12px; border-radius:10px;">
                            <h4 style="font-size:13px; font-weight:700;">Formulation ID: ${b.id}</h4>
                            <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">Requires: <span style="color:var(--accent); font-weight:700;">${b.required_quantity} ${b.unit}</span> of ${b.material_name} (${b.material_code})</p>
                        </div>
                    `).join('');
                }
            });
        }

        // Action: Start/Complete Work Order
        function updateWorkOrderStatus(id, status) {
            apiFetch(`/work-orders/${id}`, 'PUT', { status: status })
                .then(res => {
                    if (res.success) {
                        showToast(`Work order status updated to ${status}`, "success");
                        fetchProductionBomData();
                        fetchCatalogOptions();
                    } else {
                        showToast(res.message, "error");
                    }
                });
        }

        // TAB 3: Raw stocks & POs
        function fetchRawStockPoData() {
            apiFetch('/raw-materials').then(res => {
                if (res.success) {
                    rawMaterials = res.data;
                    const tbody = document.getElementById('raw-stock-tbody');
                    tbody.innerHTML = rawMaterials.map(m => {
                        const safetyPercent = Math.min((m.current_stock / m.minimum_stock) * 100, 100);
                        return `
                            <tr>
                                <td><strong>${m.material_code}</strong></td>
                                <td>${m.material_name}</td>
                                <td><span style="color:${m.current_stock <= m.minimum_stock ? 'var(--danger)' : 'var(--success)'}; font-weight:700;">${m.current_stock} ${m.unit}</span></td>
                                <td>
                                    <div style="font-size:12px; display:flex; justify-content:space-between;">
                                        <span>Min: ${m.minimum_stock}</span>
                                    </div>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar" style="width: ${safetyPercent}%; background:${m.current_stock <= m.minimum_stock ? 'var(--danger)' : 'var(--success)'}"></div>
                                    </div>
                                </td>
                                <td>₹${m.purchase_price}</td>
                            </tr>
                        `;
                    }).join('');
                }
            });

            apiFetch('/purchases').then(res => {
                if (res.success) {
                    const container = document.getElementById('po-list-container');
                    container.innerHTML = res.data.map(p => `
                        <div style="background:rgba(255,255,255,0.02); border:1px solid var(--border-glass); padding:15px; border-radius:12px; display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <h4 style="font-size:14px; font-weight:700;">PO: ${p.po_number}</h4>
                                <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">Qty Ordered: ${p.quantity} units | Total: ₹${p.total_amount}</p>
                            </div>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <span class="status-badge ${p.status.toLowerCase()}">${p.status}</span>
                                ${p.status === 'PENDING' ? `
                                    <button class="mfg-plan-btn" style="padding: 4px 8px; font-size:11px; background:var(--success);" onclick="completePurchaseOrder(${p.id})">Approve & Rec</button>
                                ` : ''}
                            </div>
                        </div>
                    `).join('');
                }
            });
        }

        function completePurchaseOrder(id) {
            apiFetch(`/purchases/${id}`, 'PUT', { status: 'COMPLETED' })
                .then(res => {
                    if (res.success) {
                        showToast("Purchase completed. Material stock replenished.", "success");
                        fetchRawStockPoData();
                        fetchCatalogOptions();
                    } else {
                        showToast(res.message, "error");
                    }
                });
        }

        // TAB 4: Outsource jobs
        function fetchOutsourceJobsData() {
            apiFetch('/job-work').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('job-work-tbody');
                    tbody.innerHTML = res.data.map(j => `
                        <tr>
                            <td><strong>${j.job_work_number}</strong></td>
                            <td>${j.vendor_name}</td>
                            <td>${j.product_name}</td>
                            <td>${j.quantity} units</td>
                            <td>₹${j.job_cost}</td>
                            <td>${j.expected_return_date || 'N/A'}</td>
                            <td><span class="status-badge ${j.status.toLowerCase().replace(' ', '-')}">${j.status}</span></td>
                            <td>
                                ${j.status === 'PENDING' ? `
                                    <button class="mfg-plan-btn" style="padding: 4px 8px; font-size:11px; background:var(--success);" onclick="completeJobWork(${j.id})">Receive Output</button>
                                ` : ''}
                            </td>
                        </tr>
                    `).join('');
                }
            });
        }

        function completeJobWork(id) {
            apiFetch(`/job-work/${id}`, 'PUT', { status: 'Completed' })
                .then(res => {
                    if (res.success) {
                        showToast("Outsource Job completed. Finished product stock incremented.", "success");
                        fetchOutsourceJobsData();
                        fetchCatalogOptions();
                    } else {
                        showToast(res.message, "error");
                    }
                });
        }

        // TAB 5: Machinery & Repairs
        function fetchMachineryRepairsData() {
            apiFetch('/machines').then(res => {
                if (res.success) {
                    machines = res.data;
                    const tbody = document.getElementById('machine-list-tbody');
                    tbody.innerHTML = machines.map(m => `
                        <tr>
                            <td><strong>${m.machine_code}</strong></td>
                            <td>${m.machine_name}</td>
                            <td>${m.capacity}</td>
                            <td>${m.maintenance_due || 'N/A'}</td>
                            <td><span class="status-badge ${m.status.toLowerCase()}">${m.status}</span></td>
                            <td>
                                ${m.status === 'ACTIVE' ? `
                                    <button class="mfg-plan-btn" style="padding:4px 8px; font-size:11px; background:var(--warning); color:#000;" onclick="toggleMachineStatus(${m.id}, 'MAINTENANCE')">Breakdown / Maint</button>
                                ` : `
                                    <button class="mfg-plan-btn" style="padding:4px 8px; font-size:11px; background:var(--success);" onclick="toggleMachineStatus(${m.id}, 'ACTIVE')">Fix Machinery</button>
                                `}
                            </td>
                        </tr>
                    `).join('');
                }
            });
        }

        function toggleMachineStatus(id, status) {
            apiFetch(`/machines/${id}`, 'PUT', { status: status })
                .then(res => {
                    if (res.success) {
                        showToast(`Machine status updated to ${status}`, "success");
                        fetchMachineryRepairsData();
                    } else {
                        showToast(res.message, "error");
                    }
                });
        }

        // TAB 6: Quality checks
        function fetchQualityInspectionsData() {
            apiFetch('/quality').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('quality-list-tbody');
                    tbody.innerHTML = res.data.map(q => `
                        <tr>
                            <td><strong>${q.inspection_number}</strong></td>
                            <td>${q.work_order_number}</td>
                            <td>${q.product_name}</td>
                            <td><span style="color:var(--success); font-weight:700;">${q.approved_quantity} approved</span></td>
                            <td><span style="color:var(--danger); font-weight:700;">${q.rejected_quantity} rejected</span></td>
                            <td>${q.inspection_date}</td>
                            <td><span class="status-badge ${q.status.toLowerCase()}">${q.status}</span></td>
                        </tr>
                    `).join('');
                }
            });
        }

        // TAB 7: Logistics & Dispatches
        function fetchLogisticsDispatchData() {
            apiFetch('/finished-goods').then(res => {
                if (res.success) {
                    finishedGoods = res.data;
                    const tbody = document.getElementById('finished-stock-tbody');
                    tbody.innerHTML = finishedGoods.map(f => `
                        <tr>
                            <td><strong>${f.product_code}</strong></td>
                            <td>${f.product_name}</td>
                            <td><span style="color:var(--accent); font-weight:700;">${f.quantity} units</span></td>
                            <td>${f.warehouse}</td>
                            <td>₹${f.selling_price}</td>
                        </tr>
                    `).join('');
                }
            });

            apiFetch('/dispatch').then(res => {
                if (res.success) {
                    const container = document.getElementById('dispatch-list-container');
                    container.innerHTML = res.data.map(d => `
                        <div style="background:rgba(255,255,255,0.02); border:1px solid var(--border-glass); padding:15px; border-radius:12px; display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <h4 style="font-size:14px; font-weight:700;">Shipment: ${d.dispatch_number}</h4>
                                <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">Dispatched ${d.quantity} units of product ID ${d.product_id}</p>
                                <p style="font-size:11px; color:var(--accent); margin-top:2px;">Vehicle: ${d.vehicle_number} | Driver: ${d.driver_name}</p>
                            </div>
                            <span class="status-badge ${d.status.toLowerCase()}">${d.status}</span>
                        </div>
                    `).join('');
                }
            });
        }

        // TAB 8: Diagnostics Users
        function fetchDiagnosticsAdminData() {
            apiFetch('/auth/users').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('admin-users-tbody');
                    tbody.innerHTML = res.data.map(u => `
                        <tr>
                            <td><strong>${u.name} (${u.username})</strong></td>
                            <td>${u.email}</td>
                            <td><span class="status-badge" style="background:rgba(255,255,255,0.05); color:#fff;">${u.role.replace('mfg_', '').replace('_', ' ')}</span></td>
                            <td><span class="status-badge ${u.status.toLowerCase()}">${u.status}</span></td>
                            <td>
                                ${u.status === 'PENDING' ? `
                                    <button class="mfg-plan-btn" style="padding: 4px 8px; font-size:11px; background:var(--success);" onclick="updateUserStatus(${u.id}, 'APPROVED')">Approve</button>
                                ` : `
                                    <button class="mfg-plan-btn" style="padding: 4px 8px; font-size:11px; background:var(--warning); color:#000;" onclick="updateUserStatus(${u.id}, 'HOLD')">Suspend</button>
                                `}
                                <button class="mfg-plan-btn" style="padding: 4px 8px; font-size:11px; background:var(--danger);" onclick="deleteUserAccount(${u.id})">Delete</button>
                            </td>
                        </tr>
                    `).join('');
                }
            });

            // Fetch SMTP options
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
                        fetchDiagnosticsAdminData();
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
                            fetchDiagnosticsAdminData();
                        } else {
                            showToast(res.message, "error");
                        }
                    });
            }
        }

        // SUBMIT ACTIONS LIST
        // 1. Create BOM
        document.getElementById('form-create-bom').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                product_id: document.getElementById('bom-product-id').value,
                material_id: document.getElementById('bom-material-id').value,
                required_quantity: document.getElementById('bom-qty').value,
                unit: document.getElementById('bom-unit').value
            };
            apiFetch('/bom', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("BOM formula registered successfully", "success");
                    closeModal('modal-create-bom');
                    fetchProductionBomData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 2. Create Plan
        document.getElementById('form-create-plan').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                plan_number: document.getElementById('plan-number').value,
                product_id: document.getElementById('plan-product-id').value,
                planned_quantity: document.getElementById('plan-qty').value,
                priority: document.getElementById('plan-priority').value
            };
            apiFetch('/production-plans', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Production plan created successfully", "success");
                    closeModal('modal-create-plan');
                    fetchProductionBomData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 3. Create Work Order
        document.getElementById('form-create-wo').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                work_order_number: document.getElementById('wo-number').value,
                product_id: document.getElementById('wo-product-id').value,
                quantity: document.getElementById('wo-qty').value
            };
            apiFetch('/work-orders', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Work order created successfully", "success");
                    closeModal('modal-create-wo');
                    fetchProductionBomData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 4. Create Material
        document.getElementById('form-create-material').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                material_code: document.getElementById('mat-code').value,
                material_name: document.getElementById('mat-name').value,
                category: document.getElementById('mat-category').value,
                unit: document.getElementById('mat-unit').value,
                minimum_stock: document.getElementById('mat-min').value,
                current_stock: document.getElementById('mat-stock').value,
                purchase_price: document.getElementById('mat-price').value
            };
            apiFetch('/raw-materials', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Raw material added successfully", "success");
                    closeModal('modal-create-material');
                    fetchRawStockPoData();
                    fetchCatalogOptions();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 5. Add Supplier
        document.getElementById('form-create-supplier').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                supplier_name: document.getElementById('sup-name').value,
                mobile: document.getElementById('sup-mobile').value,
                email: document.getElementById('sup-email').value,
                gst_number: document.getElementById('sup-gst').value
            };
            apiFetch('/suppliers', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Supplier registered successfully", "success");
                    closeModal('modal-create-supplier');
                    fetchRawStockPoData();
                    fetchCatalogOptions();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 6. Book Purchase Order
        document.getElementById('form-create-po').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                po_number: document.getElementById('po-number').value,
                supplier_id: document.getElementById('po-supplier-id').value,
                material_id: document.getElementById('po-material-id').value,
                quantity: document.getElementById('po-qty').value,
                rate: document.getElementById('po-rate').value
            };
            apiFetch('/purchases', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Purchase order registered", "success");
                    closeModal('modal-create-po');
                    fetchRawStockPoData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 7. Book Job Work
        document.getElementById('form-create-job').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                job_work_number: document.getElementById('job-number').value,
                vendor_id: document.getElementById('job-vendor-id').value,
                product_id: document.getElementById('job-product-id').value,
                quantity: document.getElementById('job-qty').value,
                job_cost: document.getElementById('job-cost').value
            };
            apiFetch('/job-work', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Outsource job work dispatched successfully", "success");
                    closeModal('modal-create-job');
                    fetchOutsourceJobsData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 8. Log machine production output
        document.getElementById('form-log-production').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                work_order_id: document.getElementById('log-wo-id').value,
                product_id: document.getElementById('log-product-id').value,
                quantity_produced: document.getElementById('log-qty').value,
                production_cost: document.getElementById('log-cost').value,
                machine_id: document.getElementById('log-machine-id').value,
                operator: document.getElementById('log-operator').value
            };
            apiFetch('/production', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Machine output run logged successfully", "success");
                    document.getElementById('form-log-production').reset();
                    fetchMachineryRepairsData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 9. Register Quality Inspection
        document.getElementById('form-quality-check').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                inspection_number: document.getElementById('qc-number').value,
                work_order_id: document.getElementById('qc-wo-id').value,
                product_id: document.getElementById('qc-product-id').value,
                approved_quantity: document.getElementById('qc-approved').value,
                rejected_quantity: document.getElementById('qc-rejected').value,
                remarks: document.getElementById('qc-remarks').value
            };
            apiFetch('/quality', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Quality inspection recorded. Finished goods catalog updated.", "success");
                    document.getElementById('form-quality-check').reset();
                    fetchQualityInspectionsData();
                    fetchCatalogOptions();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 10. Book Dispatch
        document.getElementById('form-create-dispatch').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                dispatch_number: document.getElementById('disp-number').value,
                customer_id: document.getElementById('disp-cust-id').value,
                product_id: document.getElementById('disp-product-id').value,
                quantity: document.getElementById('disp-qty').value,
                vehicle_number: document.getElementById('disp-vehicle').value,
                driver_name: document.getElementById('disp-driver').value
            };
            apiFetch('/dispatch', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Goods dispatched. Stock balance decremented.", "success");
                    closeModal('modal-create-dispatch');
                    fetchLogisticsDispatchData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 11. Save SMTP settings
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
                    showToast("SMTP configurations saved.", "success");
                    fetchDiagnosticsAdminData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 12. Send Test Mail
        document.getElementById('btn-smtp-test').addEventListener('click', () => {
            const email = document.getElementById('smtp-test-email').value;
            if (!email) {
                showToast("Enter test recipient email address", "warning");
                return;
            }
            document.getElementById('btn-smtp-test').innerText = 'Sending Diagnostic...';
            apiFetch('/auth/smtp/test', 'POST', { test_email: email }).then(res => {
                document.getElementById('btn-smtp-test').innerText = 'Send Diagnostic Test Mail';
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
