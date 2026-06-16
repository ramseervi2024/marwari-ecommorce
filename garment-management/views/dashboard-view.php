<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Garment & Textile Management ERP Portal">
    <title>Garment & Textile ERP Portal</title>
    <!-- Modern Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg-primary: #060814;
            --bg-surface: #0b0f24;
            --bg-glass: rgba(11, 15, 36, 0.85);
            --border-glass: rgba(255, 255, 255, 0.08);
            --primary: #00b4d8;
            --primary-hover: #0077b6;
            --accent: #7209b7;
            --danger: #ff0054;
            --warning: #ffb703;
            --success: #38b000;
            --text: #f3f5f9;
            --text-muted: #8d99ae;
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
            background: radial-gradient(circle at 10% 20%, rgba(0, 180, 216, 0.2) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(114, 9, 183, 0.1) 0%, transparent 40%);
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
            box-shadow: 0 0 10px rgba(0, 180, 216, 0.2);
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
            box-shadow: 0 8px 20px rgba(0, 180, 216, 0.4);
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
            background: rgba(0, 180, 216, 0.15);
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
            background: rgba(0, 180, 216, 0.1);
        }

        .sidebar-item.active {
            background: linear-gradient(135deg, rgba(0, 180, 216, 0.15) 0%, rgba(114, 9, 183, 0.05) 100%);
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
            background: rgba(255, 0, 84, 0.1);
            border: 1px solid rgba(255, 0, 84, 0.2);
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
            box-shadow: 0 0 12px rgba(255, 0, 84, 0.4);
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
        .status-badge.in-progress { background: rgba(0, 180, 216, 0.15); color: var(--primary); }
        .status-badge.cancelled { background: rgba(255, 0, 84, 0.15); color: var(--danger); }

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

        .gmt-plan-btn {
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
        .gmt-plan-btn:hover {
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
                <h2>Garment & Textile ERP</h2>
                <p>Enterprise Production & Apparel Coordinator Portal</p>
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
                    <span class="preset-badge" onclick="fillPreset('garmentsuperadmin', '123456')">Super Admin</span>
                    <span class="preset-badge" onclick="fillPreset('gmt_production', 'productionpass123')">Production Mgr</span>
                    <span class="preset-badge" onclick="fillPreset('gmt_inventory', 'inventorypass123')">Inventory Mgr</span>
                    <span class="preset-badge" onclick="fillPreset('gmt_supervisor', 'supervisorpass123')">Supervisor</span>
                    <span class="preset-badge" onclick="fillPreset('gmt_quality', 'qualitypass123')">Quality Inspector</span>
                    <span class="preset-badge" onclick="fillPreset('gmt_dispatch', 'dispatchpass123')">Dispatch Mgr</span>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN SHELL PORTAL -->
    <div id="dashboard-shell">
        <aside>
            <div class="brand-section">
                <div class="brand-logo">GMT</div>
                <div class="brand-name">Garment ERP</div>
            </div>
            
            <nav>
                <div class="nav-category">Main Operations</div>
                <div class="sidebar-item active" data-tab="dashboard-overview">Dashboard Overview</div>
                <div class="sidebar-item" data-tab="sales-orders">Sales Orders & BOM</div>
                <div class="sidebar-item" data-tab="fabrics-accessories">Fabric & Accessories</div>
                <div class="sidebar-item" data-tab="production-cutting">Production Line (Cutting)</div>
                <div class="sidebar-item" data-tab="production-stitching">Production Line (Stitching)</div>
                <div class="sidebar-item" data-tab="production-finishing">Production Line (Finishing)</div>
                <div class="sidebar-item" data-tab="workers-payroll">Workers & Payroll</div>
                <div class="sidebar-item" data-tab="quality-wastage">Quality & Wastage</div>
                <div class="sidebar-item" data-tab="logistics-dispatch">Logistics & Dispatch</div>

                <div class="nav-category">System Admin</div>
                <div class="sidebar-item admin-only" data-tab="diagnostics-users" style="display: none;">Diagnostics & Users</div>
            </nav>

            <div class="user-profile-section">
                <div class="avatar" id="user-avatar-initials">GA</div>
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
                    <p>Live Garment and Apparel Manufacturing System</p>
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
                        <span class="kpi-title">Active Orders</span>
                        <span class="kpi-value" id="kpi-active-orders">0</span>
                        <span class="kpi-detail">Production Queue</span>
                    </div>
                    <div class="kpi-card">
                        <span class="kpi-title">Fabric Stock</span>
                        <span class="kpi-value" id="kpi-fabric-meters">0m</span>
                        <span class="kpi-detail">Total Available</span>
                    </div>
                    <div class="kpi-card">
                        <span class="kpi-title">Daily Production</span>
                        <span class="kpi-value" id="kpi-production-today">0</span>
                        <span class="kpi-detail">Pcs Completed Today</span>
                    </div>
                    <div class="kpi-card">
                        <span class="kpi-title">Workers Present</span>
                        <span class="kpi-value" id="kpi-workers-present">0</span>
                        <span class="kpi-detail">On Floor Today</span>
                    </div>
                </div>

                <div class="gmt-grid">
                    <div class="dashboard-content-panel">
                        <div class="panel-header">
                            <h3>Stitching Output Trends</h3>
                        </div>
                        <canvas id="production-chart" style="max-height: 250px; width:100%;"></canvas>
                    </div>
                    <div class="dashboard-content-panel">
                        <div class="panel-header">
                            <h3>Departmental Alerts</h3>
                        </div>
                        <div style="display:flex; flex-direction:column; gap:12px;">
                            <div style="background:rgba(0, 180, 216, 0.1); border:1px solid rgba(0, 180, 216, 0.2); padding:15px; border-radius:12px; display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <h4 style="color:var(--primary); font-size:14px; font-weight:700;">Low Fabric Stocks</h4>
                                    <p style="font-size:12px; color:var(--text-muted); margin-top:2px;">Under 50 meters limit</p>
                                </div>
                                <span style="background:var(--primary); color:#000; font-weight:800; font-size:12px; padding:4px 8px; border-radius:6px;" id="badge-low-fabric">0</span>
                            </div>
                            
                            <div style="background:rgba(255, 0, 84, 0.1); border:1px solid rgba(255, 0, 84, 0.2); padding:15px; border-radius:12px; display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <h4 style="color:var(--danger); font-size:14px; font-weight:700;">QC Rejections</h4>
                                    <p style="font-size:12px; color:var(--text-muted); margin-top:2px;">Pieces rejected</p>
                                </div>
                                <span style="background:var(--danger); color:#fff; font-weight:800; font-size:12px; padding:4px 8px; border-radius:6px;" id="badge-qc-rejections">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. SALES ORDERS & BOM TAB -->
            <div class="tab-panel" id="tab-sales-orders">
                <div class="panel-header" style="margin-bottom:20px;">
                    <h3 style="font-size:20px;">Customer Sales Orders & Style BOM</h3>
                    <div style="display:flex; gap:10px;">
                        <button class="gmt-plan-btn" onclick="openModal('modal-create-order')">+ Add Sales Order</button>
                        <button class="gmt-plan-btn" style="background:var(--accent);" onclick="openModal('modal-create-bom')">+ Create BOM</button>
                    </div>
                </div>

                <div class="data-table-card">
                    <h4 style="margin-bottom: 15px;">Active Customer Orders</h4>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order Number</th>
                                <th>Customer</th>
                                <th>Product Details</th>
                                <th>Style Code</th>
                                <th>Quantity</th>
                                <th>Delivery Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="orders-list-tbody">
                            <!-- Populated dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="data-table-card">
                    <h4 style="margin-bottom: 15px;">Style Bill of Materials formulations</h4>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Style/Product ID</th>
                                <th>Fabric Requirements</th>
                                <th>Accessories List</th>
                                <th>Est. Unit Cost</th>
                            </tr>
                        </thead>
                        <tbody id="bom-list-tbody">
                            <!-- Populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. FABRIC & ACCESSORIES STOCK -->
            <div class="tab-panel" id="tab-fabrics-accessories">
                <div class="panel-header">
                    <h3 style="font-size:20px;">Raw Stocks (Fabrics & Accessories)</h3>
                    <div style="display:flex; gap:10px;">
                        <button class="gmt-plan-btn" onclick="openModal('modal-create-fabric')">+ Add Fabric</button>
                        <button class="gmt-plan-btn" onclick="openModal('modal-create-accessory')">+ Add Accessory</button>
                        <button class="gmt-plan-btn" style="background:var(--accent);" onclick="openModal('modal-create-po')">+ restock PO</button>
                    </div>
                </div>

                <div class="gmt-grid" style="grid-template-columns: 1fr 1fr;">
                    <div class="data-table-card">
                        <h4 style="margin-bottom: 15px;">Fabric Roll Stock</h4>
                        <table class="data-table">
                            <thead>
                                <th>Code</th>
                                <th>Fabric Name</th>
                                <th>GSM / Width</th>
                                <th>Available (Meters)</th>
                                <th>Unit Cost</th>
                            </thead>
                            <tbody id="fabric-list-tbody"></tbody>
                        </table>
                    </div>
                    <div class="data-table-card">
                        <h4 style="margin-bottom: 15px;">Accessories Stock Bin</h4>
                        <table class="data-table">
                            <thead>
                                <th>Accessory Name</th>
                                <th>Category</th>
                                <th>Available Qty</th>
                                <th>Unit Cost</th>
                            </thead>
                            <tbody id="accessory-list-tbody"></tbody>
                        </table>
                    </div>
                </div>

                <div class="data-table-card">
                    <h4 style="margin-bottom: 15px;">Procurement Restock POs</h4>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Supplier</th>
                                <th>Item details</th>
                                <th>Quantity</th>
                                <th>Total Cost</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="po-list-tbody"></tbody>
                    </table>
                </div>
            </div>

            <!-- 4. PRODUCTION LINE (CUTTING) -->
            <div class="tab-panel" id="tab-production-cutting">
                <div class="panel-header">
                    <h3 style="font-size:20px;">Fabric Cutting Department</h3>
                    <button class="gmt-plan-btn" onclick="openModal('modal-create-cutting')">+ Record Cutting Run</button>
                </div>

                <div class="data-table-card">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Cutting Number</th>
                                <th>Order Number</th>
                                <th>Fabric Utilized</th>
                                <th>Layers Count</th>
                                <th>Planned Pcs</th>
                                <th>Actual Pcs</th>
                                <th>Wastage (Meters)</th>
                                <th>Operator</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="cutting-list-tbody"></tbody>
                    </table>
                </div>
            </div>

            <!-- 5. PRODUCTION LINE (STITCHING) -->
            <div class="tab-panel" id="tab-production-stitching">
                <div class="panel-header">
                    <h3 style="font-size:20px;">Stitching Assembly Line</h3>
                    <button class="gmt-plan-btn" onclick="openModal('modal-create-stitching')">+ Record Stitching Batch</button>
                </div>

                <div class="data-table-card">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Batch Number</th>
                                <th>Order Details</th>
                                <th>Stitcher (Worker)</th>
                                <th>Machine ID</th>
                                <th>Target Pcs</th>
                                <th>Completions</th>
                                <th>Rejections</th>
                                <th>Production Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="stitching-list-tbody"></tbody>
                    </table>
                </div>
            </div>

            <!-- 6. PRODUCTION LINE (FINISHING) -->
            <div class="tab-panel" id="tab-production-finishing">
                <div class="panel-header">
                    <h3 style="font-size:20px;">Apparel Finishing (Folding & Pressing)</h3>
                    <button class="gmt-plan-btn" onclick="openModal('modal-create-finishing')">+ Record Finishing Log</button>
                </div>

                <div class="data-table-card">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Batch Code</th>
                                <th>Order Details</th>
                                <th>Process Type</th>
                                <th>Batch Qty</th>
                                <th>Completed</th>
                                <th>Defects Found</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="finishing-list-tbody"></tbody>
                    </table>
                </div>
            </div>

            <!-- 7. WORKERS & PAYROLL -->
            <div class="tab-panel" id="tab-workers-payroll">
                <div class="panel-header">
                    <h3 style="font-size:20px;">Worker Rosters & Payroll Calculations</h3>
                    <div style="display:flex; gap:10px;">
                        <button class="gmt-plan-btn" onclick="openModal('modal-create-worker')">+ Add New Worker</button>
                        <button class="gmt-plan-btn" style="background:var(--accent);" onclick="openModal('modal-create-payroll')">+ Process Payroll</button>
                    </div>
                </div>

                <div class="gmt-grid">
                    <div class="data-table-card">
                        <h4 style="margin-bottom: 15px;">Worker Attendance & Wages</h4>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Emp Code</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Wage Type</th>
                                    <th>Rate Structure</th>
                                    <th>Attendance</th>
                                </tr>
                            </thead>
                            <tbody id="workers-list-tbody"></tbody>
                        </table>
                    </div>

                    <div class="data-table-card">
                        <h4 style="margin-bottom: 15px;">Payroll Slip Runs</h4>
                        <div style="display:flex; flex-direction:column; gap:12px;" id="payroll-slips-container">
                            <!-- Dynamic slip list -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- 8. QUALITY & WASTAGE -->
            <div class="tab-panel" id="tab-quality-wastage">
                <div class="gmt-grid" style="grid-template-columns: 1fr 1fr;">
                    <div class="data-table-card">
                        <div class="panel-header">
                            <h4>Quality Inspections Log</h4>
                            <button class="gmt-plan-btn" onclick="openModal('modal-create-quality')">+ Add QC Inspection</button>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>QC Number</th>
                                    <th>Batch / Order</th>
                                    <th>Approved</th>
                                    <th>Rejected</th>
                                    <th>Defect Type</th>
                                </tr>
                            </thead>
                            <tbody id="quality-list-tbody"></tbody>
                        </table>
                    </div>

                    <div class="data-table-card">
                        <div class="panel-header">
                            <h4>Material Wastage Analytics</h4>
                            <button class="gmt-plan-btn" style="background:var(--danger);" onclick="openModal('modal-create-wastage')">+ Log Wastage</button>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Dept</th>
                                    <th>Material</th>
                                    <th>Quantity</th>
                                    <th>Cost Impact</th>
                                </tr>
                            </thead>
                            <tbody id="wastage-list-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 9. LOGISTICS & DISPATCH -->
            <div class="tab-panel" id="tab-logistics-dispatch">
                <div class="panel-header">
                    <h3 style="font-size:20px;">Shipping Logistics Dispatches</h3>
                    <button class="gmt-plan-btn" onclick="openModal('modal-create-dispatch')">+ Record Dispatch</button>
                </div>

                <div class="data-table-card">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Dispatch Number</th>
                                <th>Order Number</th>
                                <th>Shipped Qty</th>
                                <th>Carrier Company</th>
                                <th>tracking Code</th>
                                <th>Dispatch Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="dispatch-list-tbody"></tbody>
                    </table>
                </div>
            </div>

            <!-- 10. DIAGNOSTICS & USERS (Super Admin) -->
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
                                    <th>status</th>
                                    <th>action</th>
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
                                <select class="form-input" id="smtp-encryption" style="background:#0b0f24; color:#fff;">
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
                                <input type="text" class="form-input" id="smtp-from-name" placeholder="Garment ERP">
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
    <!-- 1. Add Sales Order -->
    <div class="modal-overlay" id="modal-create-order">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-order')">×</button>
            <h3 style="margin-bottom: 20px;">Record Sales Order</h3>
            <form id="form-create-order">
                <div class="form-group">
                    <label>Order Number</label>
                    <input type="text" class="form-input" id="order-num" required>
                </div>
                <div class="form-group">
                    <label>Customer Name</label>
                    <input type="text" class="form-input" id="order-cust" required>
                </div>
                <div class="form-group">
                    <label>Product Description</label>
                    <input type="text" class="form-input" id="order-prod" required>
                </div>
                <div class="form-group">
                    <label>Style Code</label>
                    <input type="text" class="form-input" id="order-style" required>
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" class="form-input" id="order-qty" required>
                </div>
                <div class="form-group">
                    <label>Unit Price</label>
                    <input type="number" class="form-input" id="order-price" required>
                </div>
                <div class="form-group">
                    <label>Delivery Date</label>
                    <input type="date" class="form-input" id="order-delivery" required>
                </div>
                <button type="submit" class="btn">Register Sales Order</button>
            </form>
        </div>
    </div>

    <!-- 2. Create BOM -->
    <div class="modal-overlay" id="modal-create-bom">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-bom')">×</button>
            <h3 style="margin-bottom: 20px;">Formulate Style BOM</h3>
            <form id="form-create-bom">
                <div class="form-group">
                    <label>Style Code / Product ID</label>
                    <input type="text" class="form-input" id="bom-product-id" required>
                </div>
                <div class="form-group">
                    <label>Fabric Roll ID</label>
                    <select class="form-input" id="bom-fabric-id" style="background:#0b0f24; color:#fff;" required></select>
                </div>
                <div class="form-group">
                    <label>Fabric Meters (Per piece)</label>
                    <input type="number" step="0.01" class="form-input" id="bom-fabric-req" required>
                </div>
                <div class="form-group">
                    <label>Accessories Requirements (JSON String)</label>
                    <textarea class="form-input" id="bom-acc-req" style="height:60px; resize:none;" required>[{"name":"Polyester Button","qty":3}]</textarea>
                </div>
                <div class="form-group">
                    <label>Estimated Cost (INR)</label>
                    <input type="number" class="form-input" id="bom-est-cost" required>
                </div>
                <button type="submit" class="btn">Formulate BOM Details</button>
            </form>
        </div>
    </div>

    <!-- 3. Add Fabric -->
    <div class="modal-overlay" id="modal-create-fabric">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-fabric')">×</button>
            <h3 style="margin-bottom: 20px;">Add Fabric Roll stock</h3>
            <form id="form-create-fabric">
                <div class="form-group">
                    <label>Fabric Code</label>
                    <input type="text" class="form-input" id="fab-code" required>
                </div>
                <div class="form-group">
                    <label>Fabric Name</label>
                    <input type="text" class="form-input" id="fab-name" required>
                </div>
                <div class="form-group">
                    <label>Fabric Type</label>
                    <input type="text" class="form-input" id="fab-type" required>
                </div>
                <div class="form-group">
                    <label>Color</label>
                    <input type="text" class="form-input" id="fab-color" required>
                </div>
                <div class="form-group">
                    <label>GSM</label>
                    <input type="number" class="form-input" id="fab-gsm" required>
                </div>
                <div class="form-group">
                    <label>Width (Inches)</label>
                    <input type="number" step="0.1" class="form-input" id="fab-width" required>
                </div>
                <div class="form-group">
                    <label>Available Meters</label>
                    <input type="number" class="form-input" id="fab-meters" required>
                </div>
                <div class="form-group">
                    <label>Cost Per Meter (INR)</label>
                    <input type="number" class="form-input" id="fab-cost" required>
                </div>
                <button type="submit" class="btn">Add Fabric Roll</button>
            </form>
        </div>
    </div>

    <!-- 4. Add Accessory -->
    <div class="modal-overlay" id="modal-create-accessory">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-accessory')">×</button>
            <h3 style="margin-bottom: 20px;">Register Accessory Item</h3>
            <form id="form-create-accessory">
                <div class="form-group">
                    <label>Accessory Name</label>
                    <input type="text" class="form-input" id="acc-name" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" class="form-input" id="acc-category" placeholder="buttons, zippers, threads" required>
                </div>
                <div class="form-group">
                    <label>Available Quantity</label>
                    <input type="number" class="form-input" id="acc-qty" required>
                </div>
                <div class="form-group">
                    <label>Measurement Unit</label>
                    <input type="text" class="form-input" id="acc-unit" placeholder="PCS, BOX" required>
                </div>
                <div class="form-group">
                    <label>Cost Per Unit (INR)</label>
                    <input type="number" step="0.01" class="form-input" id="acc-cost" required>
                </div>
                <button type="submit" class="btn">Register Accessory</button>
            </form>
        </div>
    </div>

    <!-- 5. Restock PO -->
    <div class="modal-overlay" id="modal-create-po">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-po')">×</button>
            <h3 style="margin-bottom: 20px;">Book Purchase Restocking PO</h3>
            <form id="form-create-po">
                <div class="form-group">
                    <label>PO Number</label>
                    <input type="text" class="form-input" id="po-num" required>
                </div>
                <div class="form-group">
                    <label>B2B Supplier</label>
                    <select class="form-input" id="po-supplier-id" style="background:#0b0f24; color:#fff;" required></select>
                </div>
                <div class="form-group">
                    <label>Item Type</label>
                    <select class="form-input" id="po-item-type" style="background:#0b0f24; color:#fff;" required>
                        <option value="FABRIC">Fabric</option>
                        <option value="ACCESSORY">Accessory</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Restock Item</label>
                    <select class="form-input" id="po-item-id" style="background:#0b0f24; color:#fff;" required></select>
                </div>
                <div class="form-group">
                    <label>PO Quantity</label>
                    <input type="number" class="form-input" id="po-qty" required>
                </div>
                <div class="form-group">
                    <label>Purchase Rate (INR)</label>
                    <input type="number" class="form-input" id="po-rate" required>
                </div>
                <button type="submit" class="btn">Book Restocking PO</button>
            </form>
        </div>
    </div>

    <!-- 6. Record Cutting -->
    <div class="modal-overlay" id="modal-create-cutting">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-cutting')">×</button>
            <h3 style="margin-bottom: 20px;">Record Fabric Cutting Run</h3>
            <form id="form-create-cutting">
                <div class="form-group">
                    <label>Cutting Run Number</label>
                    <input type="text" class="form-input" id="cut-num" required>
                </div>
                <div class="form-group">
                    <label>Sales Order Reference</label>
                    <select class="form-input" id="cut-order-id" style="background:#0b0f24; color:#fff;" required></select>
                </div>
                <div class="form-group">
                    <label>Fabric Roll utilized</label>
                    <select class="form-input" id="cut-fabric-id" style="background:#0b0f24; color:#fff;" required></select>
                </div>
                <div class="form-group">
                    <label>Layers Cut Count</label>
                    <input type="number" class="form-input" id="cut-layers" required>
                </div>
                <div class="form-group">
                    <label>Planned Pcs Output</label>
                    <input type="number" class="form-input" id="cut-plan-pcs" required>
                </div>
                <div class="form-group">
                    <label>Operator Name</label>
                    <input type="text" class="form-input" id="cut-operator" required>
                </div>
                <button type="submit" class="btn">Record Cutting Run</button>
            </form>
        </div>
    </div>

    <!-- 7. Record Stitching -->
    <div class="modal-overlay" id="modal-create-stitching">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-stitching')">×</button>
            <h3 style="margin-bottom: 20px;">Record Stitching Batch</h3>
            <form id="form-create-stitching">
                <div class="form-group">
                    <label>Production Batch Code</label>
                    <input type="text" class="form-input" id="stitch-batch" required>
                </div>
                <div class="form-group">
                    <label>Sales Order Reference</label>
                    <select class="form-input" id="stitch-order-id" style="background:#0b0f24; color:#fff;" required></select>
                </div>
                <div class="form-group">
                    <label>Assigned Operator (Worker)</label>
                    <select class="form-input" id="stitch-worker-id" style="background:#0b0f24; color:#fff;" required></select>
                </div>
                <div class="form-group">
                    <label>Stitching Machine ID</label>
                    <select class="form-input" id="stitch-machine-id" style="background:#0b0f24; color:#fff;" required></select>
                </div>
                <div class="form-group">
                    <label>Target Pieces</label>
                    <input type="number" class="form-input" id="stitch-target" required>
                </div>
                <button type="submit" class="btn">Register Stitching Batch</button>
            </form>
        </div>
    </div>

    <!-- 8. Record Finishing -->
    <div class="modal-overlay" id="modal-create-finishing">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-finishing')">×</button>
            <h3 style="margin-bottom: 20px;">Record Apparel Finishing</h3>
            <form id="form-create-finishing">
                <div class="form-group">
                    <label>Batch Number</label>
                    <input type="text" class="form-input" id="finish-batch" required>
                </div>
                <div class="form-group">
                    <label>Sales Order Reference</label>
                    <select class="form-input" id="finish-order-id" style="background:#0b0f24; color:#fff;" required></select>
                </div>
                <div class="form-group">
                    <label>Process Step</label>
                    <select class="form-input" id="finish-process" style="background:#0b0f24; color:#fff;" required>
                        <option value="Ironing">Ironing & Pressing</option>
                        <option value="Folding">Apparel Folding</option>
                        <option value="Packing">Packing & Bagging</option>
                        <option value="Labeling">Label tagging</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" class="form-input" id="finish-qty" required>
                </div>
                <button type="submit" class="btn">Record Finishing Step</button>
            </form>
        </div>
    </div>

    <!-- 9. Add Worker -->
    <div class="modal-overlay" id="modal-create-worker">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-worker')">×</button>
            <h3 style="margin-bottom: 20px;">Add factory worker profile</h3>
            <form id="form-create-worker">
                <div class="form-group">
                    <label>Employee Code</label>
                    <input type="text" class="form-input" id="worker-code" required>
                </div>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" class="form-input" id="worker-name" required>
                </div>
                <div class="form-group">
                    <label>Mobile Contact</label>
                    <input type="text" class="form-input" id="worker-mobile" required>
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <select class="form-input" id="worker-dept" style="background:#0b0f24; color:#fff;" required>
                        <option value="Cutting">Cutting</option>
                        <option value="Stitching">Stitching</option>
                        <option value="Finishing">Finishing</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Designation</label>
                    <input type="text" class="form-input" id="worker-designation" required>
                </div>
                <div class="form-group">
                    <label>Wage Category</label>
                    <select class="form-input" id="worker-wage-type" style="background:#0b0f24; color:#fff;" required>
                        <option value="MONTHLY">Monthly Salary</option>
                        <option value="DAILY">Daily Wage</option>
                        <option value="PIECE_RATE">Piece Rate Payments</option>
                    </select>
                </div>
                <div class="form-group" id="worker-sal-group">
                    <label>Monthly Salary (INR)</label>
                    <input type="number" class="form-input" id="worker-salary">
                </div>
                <div class="form-group" id="worker-wage-group" style="display:none;">
                    <label>Daily Wage Rate (INR)</label>
                    <input type="number" class="form-input" id="worker-wage">
                </div>
                <button type="submit" class="btn">Add Worker Profile</button>
            </form>
        </div>
    </div>

    <!-- 10. Process Payroll -->
    <div class="modal-overlay" id="modal-create-payroll">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-payroll')">×</button>
            <h3 style="margin-bottom: 20px;">Process Worker Wages payroll</h3>
            <form id="form-create-payroll">
                <div class="form-group">
                    <label>Target Employee</label>
                    <select class="form-input" id="pay-worker-id" style="background:#0b0f24; color:#fff;" required></select>
                </div>
                <div class="form-group">
                    <label>Payroll Month & Year</label>
                    <input type="text" class="form-input" id="pay-month" placeholder="06-2026" required>
                </div>
                <div class="form-group">
                    <label>Base Wage (INR)</label>
                    <input type="number" class="form-input" id="pay-base" required>
                </div>
                <div class="form-group">
                    <label>Allowances (INR)</label>
                    <input type="number" class="form-input" id="pay-allowance" required>
                </div>
                <div class="form-group">
                    <label>Deductions (INR)</label>
                    <input type="number" class="form-input" id="pay-deductions" required>
                </div>
                <button type="submit" class="btn">Generate Wage Slip</button>
            </form>
        </div>
    </div>

    <!-- 11. QC Inspection -->
    <div class="modal-overlay" id="modal-create-quality">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-quality')">×</button>
            <h3 style="margin-bottom: 20px;">Submit Quality Inspection Check</h3>
            <form id="form-create-quality">
                <div class="form-group">
                    <label>Inspection Report number</label>
                    <input type="text" class="form-input" id="qc-num" required>
                </div>
                <div class="form-group">
                    <label>Sales Order Reference</label>
                    <select class="form-input" id="qc-order-id" style="background:#0b0f24; color:#fff;" required></select>
                </div>
                <div class="form-group">
                    <label>Production Batch Code</label>
                    <input type="text" class="form-input" id="qc-batch" required>
                </div>
                <div class="form-group">
                    <label>Approved Pieces Qty</label>
                    <input type="number" class="form-input" id="qc-approved" required>
                </div>
                <div class="form-group">
                    <label>Rejected Pieces Qty</label>
                    <input type="number" class="form-input" id="qc-rejected" required>
                </div>
                <div class="form-group">
                    <label>Defect Classification</label>
                    <select class="form-input" id="qc-defect-type" style="background:#0b0f24; color:#fff;">
                        <option value="None">None</option>
                        <option value="Stitching Defect">Stitching Defect</option>
                        <option value="Fabric Defect">Fabric Defect</option>
                        <option value="Color Variation">Color Variation</option>
                        <option value="Measurement Defect">Measurement Defect</option>
                        <option value="Printing Defect">Printing Defect</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Inspector Remarks</label>
                    <textarea class="form-input" id="qc-remarks" style="height:60px; resize:none;"></textarea>
                </div>
                <button type="submit" class="btn">Record QC Inspection</button>
            </form>
        </div>
    </div>

    <!-- 12. Log Wastage -->
    <div class="modal-overlay" id="modal-create-wastage">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-wastage')">×</button>
            <h3 style="margin-bottom: 20px;">Log Material Wastage scrap</h3>
            <form id="form-create-wastage">
                <div class="form-group">
                    <label>Wastage Department</label>
                    <select class="form-input" id="waste-dept" style="background:#0b0f24; color:#fff;" required>
                        <option value="Cutting">Cutting Room</option>
                        <option value="Stitching">Stitching Floor</option>
                        <option value="Finishing">Finishing Press</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Material Category</label>
                    <select class="form-input" id="waste-mat-type" style="background:#0b0f24; color:#fff;" required>
                        <option value="Fabric">Fabric scrap</option>
                        <option value="Accessories">Accessories damages</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Wastage Quantity</label>
                    <input type="number" step="0.01" class="form-input" id="waste-qty" required>
                </div>
                <div class="form-group">
                    <label>Reason Description</label>
                    <input type="text" class="form-input" id="waste-reason" required>
                </div>
                <div class="form-group">
                    <label>Estimated Cost Impact (INR)</label>
                    <input type="number" class="form-input" id="waste-cost" required>
                </div>
                <button type="submit" class="btn">Log Material Wastage</button>
            </form>
        </div>
    </div>

    <!-- 13. Record Dispatch -->
    <div class="modal-overlay" id="modal-create-dispatch">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-create-dispatch')">×</button>
            <h3 style="margin-bottom: 20px;">Log Shipments Dispatch</h3>
            <form id="form-create-dispatch">
                <div class="form-group">
                    <label>Dispatch Invoice Number</label>
                    <input type="text" class="form-input" id="disp-num" required>
                </div>
                <div class="form-group">
                    <label>Sales Order Reference</label>
                    <select class="form-input" id="disp-order-id" style="background:#0b0f24; color:#fff;" required></select>
                </div>
                <div class="form-group">
                    <label>Customer Name</label>
                    <input type="text" class="form-input" id="disp-customer" required>
                </div>
                <div class="form-group">
                    <label>Shipped Quantity</label>
                    <input type="number" class="form-input" id="disp-qty" required>
                </div>
                <div class="form-group">
                    <label>Carrier Transport Company</label>
                    <input type="text" class="form-input" id="disp-carrier" required>
                </div>
                <div class="form-group">
                    <label>Logistics Tracking Code</label>
                    <input type="text" class="form-input" id="disp-tracking" required>
                </div>
                <button type="submit" class="btn">Approve Dispatch Shipment</button>
            </form>
        </div>
    </div>

    <!-- CLIENT SPA SCRIPT LOGICS -->
    <script>
        const baseUrl = '/wp-json/garment-management/v1';
        let currentUser = null;
        let productionChart = null;

        // UI Core Helpers
        function showToast(message, type = "info") {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `<span>${message}</span><span style="cursor:pointer; margin-left:15px; opacity:0.8;" onclick="this.parentElement.remove()">×</span>`;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 4500);
        }

        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function fillPreset(username, password) {
            document.getElementById('login-username').value = username;
            document.getElementById('login-password').value = password;
            document.getElementById('login-otp-group').style.display = 'none';
            document.getElementById('login-pass-group').style.display = 'block';
            document.getElementById('btn-login-submit').innerText = 'Login with Password';
        }

        // Clock display
        setInterval(() => {
            const now = new Date();
            document.getElementById('clock-display').innerText = now.toTimeString().split(' ')[0];
        }, 1000);

        // Core Fetch Wrapper
        function apiFetch(endpoint, method = 'GET', body = null) {
            const token = localStorage.getItem('gmt_access_token');
            const headers = {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };
            if (token) {
                headers['Authorization'] = `Bearer ${token}`;
            }

            const options = { method, headers };
            if (body) {
                options.body = JSON.stringify(body);
            }

            return fetch(`${baseUrl}${endpoint}`, options)
                .then(res => {
                    if (res.status === 401) {
                        // Attempt refresh token
                        const refresh = localStorage.getItem('gmt_refresh_token');
                        if (refresh && endpoint !== '/auth/refresh-token') {
                            return fetch(`${baseUrl}/auth/refresh-token`, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ refresh_token: refresh })
                            })
                            .then(r => r.json())
                            .then(refreshRes => {
                                if (refreshRes.success) {
                                    localStorage.setItem('gmt_access_token', refreshRes.data.token);
                                    localStorage.setItem('gmt_refresh_token', refreshRes.data.refresh_token);
                                    return apiFetch(endpoint, method, body); // retry original
                                } else {
                                    handleForceLogout();
                                    throw new Error("Session expired.");
                                }
                            });
                        } else {
                            handleForceLogout();
                            throw new Error("Authorization missing.");
                        }
                    }
                    return res.json();
                });
        }

        // Force Logout
        function handleForceLogout() {
            localStorage.removeItem('gmt_access_token');
            localStorage.removeItem('gmt_refresh_token');
            document.getElementById('dashboard-shell').style.display = 'none';
            document.getElementById('login-view').style.display = 'flex';
            document.getElementById('page-loader').style.display = 'none';
            currentUser = null;
        }

        // Initialize Application Session
        document.addEventListener('DOMContentLoaded', () => {
            const token = localStorage.getItem('gmt_access_token');
            if (token) {
                apiFetch('/auth/me')
                    .then(res => {
                        if (res.success) {
                            currentUser = res.data;
                            setupAuthorizedUi();
                        } else {
                            handleForceLogout();
                        }
                    })
                    .catch(() => handleForceLogout());
            } else {
                handleForceLogout();
            }

            // Bind tab switching
            document.querySelectorAll('.sidebar-item').forEach(item => {
                item.addEventListener('click', (e) => {
                    const tab = item.getAttribute('data-tab');
                    switchTab(tab);
                });
            });

            // Bind worker wage display toggles
            document.getElementById('worker-wage-type').addEventListener('change', (e) => {
                const val = e.target.value;
                if (val === 'MONTHLY') {
                    document.getElementById('worker-sal-group').style.display = 'block';
                    document.getElementById('worker-wage-group').style.display = 'none';
                } else {
                    document.getElementById('worker-sal-group').style.display = 'none';
                    document.getElementById('worker-wage-group').style.display = 'block';
                }
            });
        });

        // Switch Tabs Layout
        function switchTab(tab) {
            document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove('active'));
            const activeSidebar = document.querySelector(`.sidebar-item[data-tab="${tab}"]`);
            if (activeSidebar) activeSidebar.classList.add('active');

            document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
            
            const targetPanel = document.getElementById(`tab-${tab}`);
            if (targetPanel) {
                targetPanel.classList.add('active');
                document.getElementById('current-tab-title').innerText = activeSidebar.innerText;
                localStorage.setItem('garment_active_tab', tab);
                loadTabData(tab);
            }
        }

        // Populate Authorized UI layouts
        function setupAuthorizedUi() {
            document.getElementById('login-view').style.display = 'none';
            document.getElementById('dashboard-shell').style.display = 'flex';
            document.getElementById('page-loader').style.display = 'none';

            document.getElementById('user-display-name').innerText = currentUser.name;
            document.getElementById('user-display-role').innerText = currentUser.role.replace('garment_', '').replace('_', ' ');
            document.getElementById('user-avatar-initials').innerText = currentUser.name.split(' ').map(n=>n[0]).join('').toUpperCase();

            // Check admin settings diagnostic
            if (currentUser.role === 'administrator' || currentUser.role === 'garment_super_admin') {
                document.querySelectorAll('.admin-only').forEach(el => el.style.display = 'flex');
            } else {
                document.querySelectorAll('.admin-only').forEach(el => el.style.display = 'none');
            }

            // Restore active tab
            const savedTab = localStorage.getItem('garment_active_tab') || 'dashboard-overview';
            switchTab(savedTab);
            fetchCatalogOptions();
        }

        // Fetch Selection drop-downs parameters
        function fetchCatalogOptions() {
            // Populate fabric list fields in forms
            apiFetch('/fabric').then(res => {
                if (res.success) {
                    const fields = ['bom-fabric-id', 'cut-fabric-id', 'po-item-id'];
                    fields.forEach(fid => {
                        const el = document.getElementById(fid);
                        if (el) {
                            el.innerHTML = res.data.map(f => `<option value="${f.id}">${f.fabric_name} (${f.color}) - ${f.available_meters}m</option>`).join('');
                        }
                    });
                }
            });

            // Populate orders list fields in forms
            apiFetch('/order').then(res => {
                if (res.success) {
                    const fields = ['cut-order-id', 'stitch-order-id', 'finish-order-id', 'qc-order-id', 'disp-order-id'];
                    fields.forEach(fid => {
                        const el = document.getElementById(fid);
                        if (el) {
                            el.innerHTML = res.data.map(o => `<option value="${o.id}">${o.order_number} (${o.product_name})</option>`).join('');
                        }
                    });
                }
            });

            // Populate workers list fields in forms
            apiFetch('/worker').then(res => {
                if (res.success) {
                    const fields = ['stitch-worker-id', 'pay-worker-id'];
                    fields.forEach(fid => {
                        const el = document.getElementById(fid);
                        if (el) {
                            el.innerHTML = res.data.map(w => `<option value="${w.id}">${w.name} (${w.employee_code})</option>`).join('');
                        }
                    });
                }
            });

            // Populate machines list fields in forms
            apiFetch('/machine').then(res => {
                if (res.success) {
                    const el = document.getElementById('stitch-machine-id');
                    if (el) {
                        el.innerHTML = res.data.map(m => `<option value="${m.id}">${m.machine_name} (${m.machine_code})</option>`).join('');
                    }
                }
            });

            // Populate suppliers list fields in forms
            apiFetch('/supplier').then(res => {
                if (res.success) {
                    const el = document.getElementById('po-supplier-id');
                    if (el) {
                        el.innerHTML = res.data.map(s => `<option value="${s.id}">${s.supplier_name}</option>`).join('');
                    }
                }
            });
        }

        // Routing Load tab specific queries
        function loadTabData(tab) {
            switch(tab) {
                case 'dashboard-overview':
                    fetchDashboardOverviewData();
                    break;
                case 'sales-orders':
                    fetchSalesOrdersData();
                    break;
                case 'fabrics-accessories':
                    fetchFabricsAccessoriesData();
                    break;
                case 'production-cutting':
                    fetchProductionCuttingData();
                    break;
                case 'production-stitching':
                    fetchProductionStitchingData();
                    break;
                case 'production-finishing':
                    fetchProductionFinishingData();
                    break;
                case 'workers-payroll':
                    fetchWorkersPayrollData();
                    break;
                case 'quality-wastage':
                    fetchQualityWastageData();
                    break;
                case 'logistics-dispatch':
                    fetchLogisticsDispatchData();
                    break;
                case 'diagnostics-users':
                    fetchDiagnosticsAdminData();
                    break;
            }
        }

        // TAB 1: Dashboard Overview
        function fetchDashboardOverviewData() {
            apiFetch('/dashboard').then(res => {
                if (res.success) {
                    const cnt = res.data.counters;
                    document.getElementById('kpi-active-orders').innerText = cnt.active_orders;
                    document.getElementById('kpi-fabric-meters').innerText = `${Math.round(cnt.fabric_stock)}m`;
                    document.getElementById('kpi-production-today').innerText = cnt.production_today;
                    document.getElementById('kpi-workers-present').innerText = cnt.workers_present;
                    document.getElementById('badge-low-fabric').innerText = cnt.low_stock_fabrics;
                    document.getElementById('badge-qc-rejections').innerText = cnt.rejected_products;

                    renderDashboardCharts(res.data.trends);
                }
            });
        }

        function renderDashboardCharts(trends) {
            const ctx = document.getElementById('production-chart').getContext('2d');
            if (productionChart) productionChart.destroy();

            productionChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: trends.production.labels,
                    datasets: [{
                        label: 'Garments Stitched',
                        data: trends.production.data,
                        borderColor: '#00b4d8',
                        backgroundColor: 'rgba(0, 180, 216, 0.15)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#8d99ae' } },
                        x: { grid: { display: false }, ticks: { color: '#8d99ae' } }
                    }
                }
            });
        }

        // TAB 2: Sales Orders & BOM
        function fetchSalesOrdersData() {
            apiFetch('/order').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('orders-list-tbody');
                    tbody.innerHTML = res.data.map(o => `
                        <tr>
                            <td><strong>${o.order_number}</strong></td>
                            <td>${o.customer_name}</td>
                            <td>${o.product_name}</td>
                            <td><span style="color:var(--primary); font-weight:700;">${o.style_code}</span></td>
                            <td>${o.quantity} pcs</td>
                            <td>${o.delivery_date.split(' ')[0]}</td>
                            <td><span class="status-badge ${o.status.toLowerCase().replace(' ', '-')}">${o.status}</span></td>
                            <td>
                                <select class="form-input" style="padding:4px 8px; font-size:12px; background:#0b0f24; width:auto;" onchange="updateOrderStatus(${o.id}, this.value)">
                                    <option value="">Update Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="In Production">In Production</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Dispatched">Dispatched</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </td>
                        </tr>
                    `).join('');
                }
            });

            apiFetch('/bom').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('bom-list-tbody');
                    tbody.innerHTML = res.data.map(b => `
                        <tr>
                            <td><strong>${b.style_code}</strong></td>
                            <td>${b.fabric_requirement} meters / piece</td>
                            <td>${JSON.stringify(b.accessories_requirement)}</td>
                            <td>₹${b.estimated_cost}</td>
                        </tr>
                    `).join('');
                }
            });
        }

        function updateOrderStatus(id, status) {
            if (!status) return;
            apiFetch(`/order/${id}`, 'PUT', { status: status })
                .then(res => {
                    if (res.success) {
                        showToast("Sales order status updated successfully", "success");
                        fetchSalesOrdersData();
                    } else {
                        showToast(res.message, "error");
                    }
                });
        }

        // TAB 3: Fabrics & Accessories
        function fetchFabricsAccessoriesData() {
            apiFetch('/fabric').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('fabric-list-tbody');
                    tbody.innerHTML = res.data.map(f => `
                        <tr>
                            <td><strong>${f.fabric_code}</strong></td>
                            <td>${f.fabric_name} (${f.color})</td>
                            <td>${f.gsm} GSM / ${f.width}"</td>
                            <td><span style="color:var(--primary); font-weight:700;">${f.available_meters}m</span></td>
                            <td>₹${f.cost_per_meter}</td>
                        </tr>
                    `).join('');
                }
            });

            apiFetch('/accessory').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('accessory-list-tbody');
                    tbody.innerHTML = res.data.map(a => `
                        <tr>
                            <td><strong>${a.accessory_name}</strong></td>
                            <td><span class="status-badge" style="background:rgba(255,255,255,0.05); color:#fff;">${a.category}</span></td>
                            <td><span style="color:var(--success); font-weight:700;">${a.available_quantity} ${a.unit}</span></td>
                            <td>₹${a.cost_per_unit}</td>
                        </tr>
                    `).join('');
                }
            });

            apiFetch('/purchase').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('po-list-tbody');
                    tbody.innerHTML = res.data.map(p => `
                        <tr>
                            <td><strong>${p.po_number}</strong></td>
                            <td>Supplier ID: ${p.supplier_id}</td>
                            <td>${p.item_type} ID: ${p.item_id}</td>
                            <td>${p.quantity}</td>
                            <td>₹${p.total_amount}</td>
                            <td>${p.purchase_date.split(' ')[0]}</td>
                            <td><span class="status-badge ${p.status.toLowerCase()}">${p.status}</span></td>
                        </tr>
                    `).join('');
                }
            });
        }

        // TAB 4: Cutting
        function fetchProductionCuttingData() {
            apiFetch('/cutting').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('cutting-list-tbody');
                    tbody.innerHTML = res.data.map(c => `
                        <tr>
                            <td><strong>${c.cutting_number}</strong></td>
                            <td>Order ID: ${c.order_id}</td>
                            <td>Fabric ID: ${c.fabric_id}</td>
                            <td>${c.layers} layers</td>
                            <td>${c.planned_pieces} pcs</td>
                            <td>${c.actual_pieces} pcs</td>
                            <td>${c.wastage_meters}m</td>
                            <td>${c.operator_name}</td>
                            <td><span class="status-badge ${c.status.toLowerCase()}">${c.status}</span></td>
                            <td>
                                ${c.status !== 'COMPLETED' ? `
                                    <button class="gmt-plan-btn" style="padding:4px 8px; font-size:11px;" onclick="completeCutting(${c.id})">Complete</button>
                                ` : '-'}
                            </td>
                        </tr>
                    `).join('');
                }
            });
        }

        function completeCutting(id) {
            const actual = prompt("Enter actual pieces cut output:");
            if (actual === null) return;
            const wastage = prompt("Enter wastage fabric in meters:");
            if (wastage === null) return;

            apiFetch(`/cutting/${id}`, 'PUT', { 
                status: 'COMPLETED',
                actual_pieces: parseFloat(actual) || 0,
                wastage_meters: parseFloat(wastage) || 0
            }).then(res => {
                if (res.success) {
                    showToast("Fabric cut completed. Available stock decremented.", "success");
                    fetchProductionCuttingData();
                } else {
                    showToast(res.message, "error");
                }
            });
        }

        // TAB 5: Stitching
        function fetchProductionStitchingData() {
            apiFetch('/stitching').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('stitching-list-tbody');
                    tbody.innerHTML = res.data.map(s => `
                        <tr>
                            <td><strong>${s.production_batch}</strong></td>
                            <td>Order ID: ${s.order_id}</td>
                            <td>Worker ID: ${s.worker_id}</td>
                            <td>Machine: ${s.machine_id || 'None'}</td>
                            <td>${s.target_quantity} pcs</td>
                            <td>${s.completed_quantity} pcs</td>
                            <td>${s.rejected_quantity} pcs</td>
                            <td>${s.production_date.split(' ')[0]}</td>
                            <td><span class="status-badge ${s.status.toLowerCase()}">${s.status}</span></td>
                            <td>
                                ${s.status !== 'COMPLETED' ? `
                                    <button class="gmt-plan-btn" style="padding:4px 8px; font-size:11px;" onclick="completeStitching(${s.id})">Complete Run</button>
                                ` : '-'}
                            </td>
                        </tr>
                    `).join('');
                }
            });
        }

        function completeStitching(id) {
            const completed = prompt("Enter completed stitched pieces output:");
            if (completed === null) return;
            const rejected = prompt("Enter rejected/defective pieces count:");
            if (rejected === null) return;

            apiFetch(`/stitching/${id}`, 'PUT', { 
                status: 'COMPLETED',
                completed_quantity: parseFloat(completed) || 0,
                rejected_quantity: parseFloat(rejected) || 0
            }).then(res => {
                if (res.success) {
                    showToast("Stitching run details saved.", "success");
                    fetchProductionStitchingData();
                } else {
                    showToast(res.message, "error");
                }
            });
        }

        // TAB 6: Finishing
        function fetchProductionFinishingData() {
            apiFetch('/finishing').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('finishing-list-tbody');
                    tbody.innerHTML = res.data.map(f => `
                        <tr>
                            <td><strong>${f.batch_number}</strong></td>
                            <td>Order ID: ${f.order_id}</td>
                            <td>${f.process_type}</td>
                            <td>${f.quantity} pcs</td>
                            <td>${f.completed_quantity} pcs</td>
                            <td>${f.defects_found} defects</td>
                            <td><span class="status-badge ${f.status.toLowerCase()}">${f.status}</span></td>
                            <td>
                                ${f.status !== 'COMPLETED' ? `
                                    <button class="gmt-plan-btn" style="padding:4px 8px; font-size:11px;" onclick="completeFinishing(${f.id})">Complete</button>
                                ` : '-'}
                            </td>
                        </tr>
                    `).join('');
                }
            });
        }

        function completeFinishing(id) {
            const completed = prompt("Enter completed finished pieces:");
            if (completed === null) return;
            const defects = prompt("Enter defects found:");
            if (defects === null) return;

            apiFetch(`/finishing/${id}`, 'PUT', { 
                status: 'COMPLETED',
                completed_quantity: parseFloat(completed) || 0,
                defects_found: parseFloat(defects) || 0
            }).then(res => {
                if (res.success) {
                    showToast("Finishing step completed successfully", "success");
                    fetchProductionFinishingData();
                } else {
                    showToast(res.message, "error");
                }
            });
        }

        // TAB 7: Workers & Payroll
        function fetchWorkersPayrollData() {
            apiFetch('/worker').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('workers-list-tbody');
                    tbody.innerHTML = res.data.map(w => `
                        <tr>
                            <td><strong>${w.employee_code}</strong></td>
                            <td>${w.name}</td>
                            <td>${w.department} - ${w.designation}</td>
                            <td><span class="status-badge" style="background:rgba(255,255,255,0.05); color:#fff;">${w.salary_type}</span></td>
                            <td>₹${w.salary_type === 'MONTHLY' ? w.monthly_salary + '/mo' : w.daily_wage + '/day'}</td>
                            <td>
                                <button class="gmt-plan-btn" style="padding:4px 8px; font-size:11px; background:${w.attendance_status === 'PRESENT' ? 'var(--success)' : 'var(--danger)'}" onclick="toggleAttendance(${w.id}, '${w.attendance_status === 'PRESENT' ? 'ABSENT' : 'PRESENT'}')">
                                    ${w.attendance_status}
                                </button>
                            </td>
                        </tr>
                    `).join('');
                }
            });

            apiFetch('/payroll').then(res => {
                if (res.success) {
                    const container = document.getElementById('payroll-slips-container');
                    container.innerHTML = res.data.map(p => `
                        <div style="background:rgba(255,255,255,0.02); border:1px solid var(--border-glass); padding:15px; border-radius:12px; display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <h4 style="font-size:14px; font-weight:700;">Worker ID: ${p.worker_id} (Month: ${p.month_year})</h4>
                                <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">Base: ₹${p.base_salary} | Allowances: ₹${p.allowance} | Deduct: ₹${p.deductions}</p>
                                <p style="font-size:13px; color:var(--primary); font-weight:800; margin-top:2px;">Net Salary: ₹${p.net_salary}</p>
                            </div>
                            <span class="status-badge ${p.payment_status.toLowerCase()}">${p.payment_status}</span>
                        </div>
                    `).join('');
                }
            });
        }

        function toggleAttendance(id, state) {
            apiFetch(`/worker/${id}`, 'PUT', { attendance_status: state })
                .then(res => {
                    if (res.success) {
                        showToast("Worker attendance roster updated.", "success");
                        fetchWorkersPayrollData();
                    }
                });
        }

        // TAB 8: Quality & Wastage
        function fetchQualityWastageData() {
            apiFetch('/quality').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('quality-list-tbody');
                    tbody.innerHTML = res.data.map(q => `
                        <tr>
                            <td><strong>${q.inspection_number}</strong></td>
                            <td>Batch: ${q.batch_number} (Order #${q.order_id})</td>
                            <td><span style="color:var(--success); font-weight:700;">${q.approved_quantity} approved</span></td>
                            <td><span style="color:var(--danger); font-weight:700;">${q.rejected_quantity} rejected</span></td>
                            <td><span class="status-badge error">${q.defect_type}</span></td>
                        </tr>
                    `).join('');
                }
            });

            apiFetch('/wastage').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('wastage-list-tbody');
                    tbody.innerHTML = res.data.map(w => `
                        <tr>
                            <td><strong>${w.department}</strong></td>
                            <td>${w.material_type}</td>
                            <td>${w.quantity} units</td>
                            <td><span style="color:var(--danger); font-weight:700;">-₹${w.cost_impact}</span></td>
                        </tr>
                    `).join('');
                }
            });
        }

        // TAB 9: Logistics & Dispatch
        function fetchLogisticsDispatchData() {
            apiFetch('/dispatch').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('dispatch-list-tbody');
                    tbody.innerHTML = res.data.map(d => `
                        <tr>
                            <td><strong>${d.dispatch_number}</strong></td>
                            <td>Order ID: ${d.order_id}</td>
                            <td>${d.quantity} pcs</td>
                            <td>${d.transport_company}</td>
                            <td><code>${d.tracking_number}</code></td>
                            <td>${d.dispatch_date.split(' ')[0]}</td>
                            <td><span class="status-badge ${d.status.toLowerCase()}">${d.status}</span></td>
                            <td>
                                ${d.status !== 'COMPLETED' ? `
                                    <button class="gmt-plan-btn" style="padding:4px 8px; font-size:11px;" onclick="completeDispatch(${d.id})">Delivered</button>
                                ` : '-'}
                            </td>
                        </tr>
                    `).join('');
                }
            });
        }

        function completeDispatch(id) {
            apiFetch(`/dispatch/${id}`, 'PUT', { status: 'COMPLETED' })
                .then(res => {
                    if (res.success) {
                        showToast("Shipment delivered successfully", "success");
                        fetchLogisticsDispatchData();
                    } else {
                        showToast(res.message, "error");
                    }
                });
        }

        // TAB 10: Diagnostics Admin
        function fetchDiagnosticsAdminData() {
            apiFetch('/auth/users').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('admin-users-tbody');
                    tbody.innerHTML = res.data.map(u => `
                        <tr>
                            <td><strong>${u.name} (${u.username})</strong></td>
                            <td>${u.email}</td>
                            <td><span class="status-badge" style="background:rgba(255,255,255,0.05); color:#fff;">${u.role.replace('garment_', '').replace('_', ' ')}</span></td>
                            <td><span class="status-badge ${u.status.toLowerCase()}">${u.status}</span></td>
                            <td>
                                ${u.status === 'PENDING' ? `
                                    <button class="gmt-plan-btn" style="padding: 4px 8px; font-size:11px; background:var(--success);" onclick="updateUserStatus(${u.id}, 'APPROVED')">Approve</button>
                                ` : `
                                    <button class="gmt-plan-btn" style="padding: 4px 8px; font-size:11px; background:var(--warning); color:#000;" onclick="updateUserStatus(${u.id}, 'HOLD')">Suspend</button>
                                `}
                                <button class="gmt-plan-btn" style="padding: 4px 8px; font-size:11px; background:var(--danger);" onclick="deleteUserAccount(${u.id})">Delete</button>
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

        // SUBMIT FORM EVENTS LOGIC
        // 1. Create Order
        document.getElementById('form-create-order').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                order_number: document.getElementById('order-num').value,
                customer_name: document.getElementById('order-cust').value,
                product_name: document.getElementById('order-prod').value,
                style_code: document.getElementById('order-style').value,
                quantity: parseFloat(document.getElementById('order-qty').value),
                unit_price: parseFloat(document.getElementById('order-price').value),
                delivery_date: document.getElementById('order-delivery').value
            };
            apiFetch('/order', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Sales order registered successfully", "success");
                    closeModal('modal-create-order');
                    document.getElementById('form-create-order').reset();
                    fetchSalesOrdersData();
                    fetchCatalogOptions();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 2. Create BOM
        document.getElementById('form-create-bom').addEventListener('submit', (e) => {
            e.preventDefault();
            let parsedAcc = [];
            try {
                parsedAcc = JSON.parse(document.getElementById('bom-acc-req').value);
            } catch (err) {
                showToast("Invalid accessories JSON. Use array format: []", "error");
                return;
            }

            const payload = {
                product_id: document.getElementById('bom-product-id').value,
                fabric_id: parseInt(document.getElementById('bom-fabric-id').value),
                fabric_requirement: parseFloat(document.getElementById('bom-fabric-req').value),
                accessories_requirement: parsedAcc,
                estimated_cost: parseFloat(document.getElementById('bom-est-cost').value)
            };
            apiFetch('/bom', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("BOM formulation registered successfully", "success");
                    closeModal('modal-create-bom');
                    document.getElementById('form-create-bom').reset();
                    fetchSalesOrdersData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 3. Create Fabric
        document.getElementById('form-create-fabric').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                fabric_code: document.getElementById('fab-code').value,
                fabric_name: document.getElementById('fab-name').value,
                fabric_type: document.getElementById('fab-type').value,
                color: document.getElementById('fab-color').value,
                gsm: parseInt(document.getElementById('fab-gsm').value),
                width: parseFloat(document.getElementById('fab-width').value),
                available_meters: parseFloat(document.getElementById('fab-meters').value),
                cost_per_meter: parseFloat(document.getElementById('fab-cost').value)
            };
            apiFetch('/fabric', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Fabric stock roll added.", "success");
                    closeModal('modal-create-fabric');
                    document.getElementById('form-create-fabric').reset();
                    fetchFabricsAccessoriesData();
                    fetchCatalogOptions();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 4. Create Accessory
        document.getElementById('form-create-accessory').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                accessory_name: document.getElementById('acc-name').value,
                category: document.getElementById('acc-category').value,
                available_quantity: parseFloat(document.getElementById('acc-qty').value),
                unit: document.getElementById('acc-unit').value,
                cost_per_unit: parseFloat(document.getElementById('acc-cost').value)
            };
            apiFetch('/accessory', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Accessory registered successfully", "success");
                    closeModal('modal-create-accessory');
                    document.getElementById('form-create-accessory').reset();
                    fetchFabricsAccessoriesData();
                    fetchCatalogOptions();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 5. Restock PO
        document.getElementById('form-create-po').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                po_number: document.getElementById('po-num').value,
                supplier_id: parseInt(document.getElementById('po-supplier-id').value),
                item_type: document.getElementById('po-item-type').value,
                item_id: parseInt(document.getElementById('po-item-id').value),
                quantity: parseFloat(document.getElementById('po-qty').value),
                rate: parseFloat(document.getElementById('po-rate').value),
                total_amount: parseFloat(document.getElementById('po-qty').value) * parseFloat(document.getElementById('po-rate').value),
                purchase_date: new Date().toISOString()
            };
            apiFetch('/purchase', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("PO RESTOCK booked successfully.", "success");
                    closeModal('modal-create-po');
                    document.getElementById('form-create-po').reset();
                    fetchFabricsAccessoriesData();
                    fetchCatalogOptions();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 6. Record Cutting Run
        document.getElementById('form-create-cutting').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                cutting_number: document.getElementById('cut-num').value,
                order_id: parseInt(document.getElementById('cut-order-id').value),
                fabric_id: parseInt(document.getElementById('cut-fabric-id').value),
                layers: parseInt(document.getElementById('cut-layers').value),
                planned_pieces: parseFloat(document.getElementById('cut-plan-pcs').value),
                operator_name: document.getElementById('cut-operator').value,
                status: 'PENDING'
            };
            apiFetch('/cutting', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Cutting run registered successfully", "success");
                    closeModal('modal-create-cutting');
                    document.getElementById('form-create-cutting').reset();
                    fetchProductionCuttingData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 7. Record Stitching Run
        document.getElementById('form-create-stitching').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                production_batch: document.getElementById('stitch-batch').value,
                order_id: parseInt(document.getElementById('stitch-order-id').value),
                worker_id: parseInt(document.getElementById('stitch-worker-id').value),
                machine_id: parseInt(document.getElementById('stitch-machine-id').value),
                target_quantity: parseFloat(document.getElementById('stitch-target').value),
                production_date: new Date().toISOString()
            };
            apiFetch('/stitching', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Stitching batch recorded", "success");
                    closeModal('modal-create-stitching');
                    document.getElementById('form-create-stitching').reset();
                    fetchProductionStitchingData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 8. Record Finishing Step
        document.getElementById('form-create-finishing').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                batch_number: document.getElementById('finish-batch').value,
                order_id: parseInt(document.getElementById('finish-order-id').value),
                process_type: document.getElementById('finish-process').value,
                quantity: parseFloat(document.getElementById('finish-qty').value)
            };
            apiFetch('/finishing', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Finishing log created successfully", "success");
                    closeModal('modal-create-finishing');
                    document.getElementById('form-create-finishing').reset();
                    fetchProductionFinishingData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 9. Add Worker
        document.getElementById('form-create-worker').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                employee_code: document.getElementById('worker-code').value,
                name: document.getElementById('worker-name').value,
                mobile: document.getElementById('worker-mobile').value,
                department: document.getElementById('worker-dept').value,
                designation: document.getElementById('worker-designation').value,
                salary_type: document.getElementById('worker-wage-type').value,
                daily_wage: parseFloat(document.getElementById('worker-wage').value) || 0,
                monthly_salary: parseFloat(document.getElementById('worker-salary').value) || 0
            };
            apiFetch('/worker', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Worker registered successfully", "success");
                    closeModal('modal-create-worker');
                    document.getElementById('form-create-worker').reset();
                    fetchWorkersPayrollData();
                    fetchCatalogOptions();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 10. Process Payroll
        document.getElementById('form-create-payroll').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                worker_id: parseInt(document.getElementById('pay-worker-id').value),
                month_year: document.getElementById('pay-month').value,
                base_salary: parseFloat(document.getElementById('pay-base').value),
                allowance: parseFloat(document.getElementById('pay-allowance').value),
                deductions: parseFloat(document.getElementById('pay-deductions').value),
                net_salary: parseFloat(document.getElementById('pay-base').value) + parseFloat(document.getElementById('pay-allowance').value) - parseFloat(document.getElementById('pay-deductions').value)
            };
            apiFetch('/payroll', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("wage slip processed successfully", "success");
                    closeModal('modal-create-payroll');
                    document.getElementById('form-create-payroll').reset();
                    fetchWorkersPayrollData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 11. Add Quality Inspection
        document.getElementById('form-create-quality').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                inspection_number: document.getElementById('qc-num').value,
                order_id: parseInt(document.getElementById('qc-order-id').value),
                batch_number: document.getElementById('qc-batch').value,
                approved_quantity: parseFloat(document.getElementById('qc-approved').value),
                rejected_quantity: parseFloat(document.getElementById('qc-rejected').value),
                defect_type: document.getElementById('qc-defect-type').value,
                remarks: document.getElementById('qc-remarks').value,
                inspection_date: new Date().toISOString()
            };
            apiFetch('/quality', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("QC report registered successfully", "success");
                    closeModal('modal-create-quality');
                    document.getElementById('form-create-quality').reset();
                    fetchQualityWastageData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 12. Log Wastage
        document.getElementById('form-create-wastage').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                department: document.getElementById('waste-dept').value,
                material_type: document.getElementById('waste-mat-type').value,
                quantity: parseFloat(document.getElementById('waste-qty').value),
                reason: document.getElementById('waste-reason').value,
                cost_impact: parseFloat(document.getElementById('waste-cost').value)
            };
            apiFetch('/wastage', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Wastage statement logged.", "success");
                    closeModal('modal-create-wastage');
                    document.getElementById('form-create-wastage').reset();
                    fetchQualityWastageData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 13. Record Dispatch
        document.getElementById('form-create-dispatch').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                dispatch_number: document.getElementById('disp-num').value,
                order_id: parseInt(document.getElementById('disp-order-id').value),
                customer_name: document.getElementById('disp-customer').value,
                quantity: parseFloat(document.getElementById('disp-qty').value),
                transport_company: document.getElementById('disp-carrier').value,
                tracking_number: document.getElementById('disp-tracking').value,
                dispatch_date: new Date().toISOString()
            };
            apiFetch('/dispatch', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Dispatch shipment approved.", "success");
                    closeModal('modal-create-dispatch');
                    document.getElementById('form-create-dispatch').reset();
                    fetchLogisticsDispatchData();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // 14. Save SMTP Settings
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

        // 15. Send test email
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

        // OTP / Login Form handling
        let loginUsername = '';
        document.getElementById('btn-send-otp').addEventListener('click', () => {
            const val = document.getElementById('login-username').value;
            if (!val) {
                showToast("Enter username or email address first", "warning");
                return;
            }
            loginUsername = val;
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
                    localStorage.setItem('gmt_access_token', res.data.token);
                    localStorage.setItem('gmt_refresh_token', res.data.refresh_token);
                    currentUser = res.data.user;
                    setupAuthorizedUi();
                    showToast("Welcome back to Garment ERP portal", "success");
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
    </script>
</body>
</html>
