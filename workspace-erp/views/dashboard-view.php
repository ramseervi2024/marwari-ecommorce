<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurbis Workspace ERP Dashboard</title>
    <!-- Modern Premium Typography -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #f1f5f9;
            --bg-secondary: rgba(255, 255, 255, 0.85);
            --bg-card: rgba(255, 255, 255, 0.65);
            --accent-blue: #2563eb;
            --accent-purple: #7c3aed;
            --accent-pink: #db2777;
            --accent-emerald: #059669;
            --text-main: #0f172a;
            --text-muted: #475569;
            --glass-border: rgba(15, 23, 42, 0.08);
            --glass-shadow: rgba(15, 23, 42, 0.06);
            --border-hover: rgba(15, 23, 42, 0.16);
        }

        body.dark-mode {
            --bg-primary: #080c14;
            --bg-secondary: rgba(13, 20, 35, 0.8);
            --bg-card: rgba(30, 41, 59, 0.45);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --glass-border: rgba(255, 255, 255, 0.06);
            --glass-shadow: rgba(0, 0, 0, 0.5);
            --border-hover: rgba(255, 255, 255, 0.12);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-main);
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(37, 99, 235, 0.04) 0%, transparent 45%),
                radial-gradient(circle at 85% 85%, rgba(124, 58, 237, 0.04) 0%, transparent 45%);
            background-attachment: fixed;
            min-height: 100vh;
            transition: background-color 0.3s, color 0.3s, background-image 0.3s;
        }

        body.dark-mode {
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(37, 99, 235, 0.08) 0%, transparent 45%),
                radial-gradient(circle at 85% 85%, rgba(124, 58, 237, 0.08) 0%, transparent 45%);
        }

        /* Toast notifications */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .toast {
            background: rgba(15, 23, 42, 0.95);
            border: 1px solid var(--accent-blue);
            color: #fff;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            backdrop-filter: blur(10px);
            font-size: 14px;
            min-width: 300px;
            transform: translateX(120%);
            transition: transform 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }
        .toast.show {
            transform: translateX(0);
        }
        .toast.success { border-color: var(--accent-emerald); }
        .toast.error { border-color: var(--accent-pink); }

        /* Auth Screen Layer */
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .auth-card {
            background: var(--bg-secondary);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            width: 100%;
            max-width: 540px;
            padding: 40px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.7);
        }
        .auth-logo {
            text-align: center;
            margin-bottom: 25px;
        }
        .auth-logo h2 {
            font-weight: 700;
            font-size: 28px;
            background: linear-gradient(135deg, #60a5fa, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .auth-logo p {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 6px;
        }
        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .form-group label {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
        }
        .form-input, .form-select, .form-textarea {
            background: var(--bg-card);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            padding: 12px 16px;
            color: var(--text-main);
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
            width: 100%;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--accent-blue);
        }
        .form-select option {
            background: var(--bg-secondary);
            color: var(--text-main);
        }
        .auth-submit-btn {
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            color: #fff;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transition: all 0.2s;
        }
        .auth-submit-btn:hover {
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5);
            transform: translateY(-1px);
        }
        
        /* Pre-populated credentials */
        .demo-roles-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .demo-role-btn {
            background: var(--bg-card);
            border: 1px solid var(--glass-border);
            padding: 10px;
            border-radius: 10px;
            text-align: left;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 3px;
            transition: all 0.2s;
        }
        .demo-role-btn:hover {
            background: rgba(37, 99, 235, 0.08);
            border-color: var(--accent-blue);
        }
        .demo-role-title {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-main);
        }
        .demo-role-user {
            font-size: 10px;
            color: var(--text-muted);
        }

        /* App Layout */
        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Glassmorphism */
        .sidebar {
            width: 270px;
            background: rgba(10, 15, 30, 0.85);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--glass-border);
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: fixed;
            height: 100vh;
            z-index: 100;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 22px;
            background: linear-gradient(135deg, #60a5fa, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 30px;
        }

        .brand-icon {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
            font-size: 18px;
        }

        .menu-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
            overflow-y: auto;
            max-height: calc(100vh - 200px);
            padding-right: 5px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
            outline: none;
            transition: all 0.2s;
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
            border-left: 3px solid var(--accent-blue);
        }

        .user-profile-wrapper {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
            margin-top: 10px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            border: 1px solid var(--glass-border);
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-pink));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
        }

        .user-info h4 {
            font-size: 13px;
            font-weight: 600;
        }

        .user-info p {
            font-size: 11px;
            color: var(--text-muted);
        }

        .logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px;
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        /* Main Content Panel */
        .main-panel {
            flex-grow: 1;
            padding: 40px;
            margin-left: 270px;
            min-height: 100vh;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }

        .title-group h1 {
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 4px;
            background: linear-gradient(to right, #0f172a, #334155);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        body.dark-mode .title-group h1 {
            background: linear-gradient(to right, #ffffff, #cbd5e1);
        }

        .title-group p {
            color: var(--text-muted);
            font-size: 14px;
        }

        .badge-live {
            background: rgba(5, 150, 105, 0.15);
            border: 1px solid var(--accent-emerald);
            color: var(--accent-emerald);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            background-color: var(--accent-emerald);
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(5, 150, 105, 0.5); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(5, 150, 105, 0); }
            100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(5, 150, 105, 0); }
        }

        /* Tab panels */
        .tab-panel {
            display: none;
            animation: fadeIn 0.4s ease;
        }
        .tab-panel.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 24px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: var(--bg-card);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-shadow: 0 8px 32px var(--glass-shadow);
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, var(--accent-blue), var(--accent-purple));
            opacity: 0;
            transition: opacity 0.3s;
        }

        .stat-card:hover::before { opacity: 1; }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.5);
            border-color: var(--border-hover);
        }

        .card-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--accent-blue);
        }

        .stat-card:nth-child(2) .card-icon { color: var(--accent-purple); }
        .stat-card:nth-child(3) .card-icon { color: var(--accent-pink); }
        .stat-card:nth-child(4) .card-icon { color: var(--accent-emerald); }

        .card-label {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .card-value {
            font-size: 28px;
            font-weight: 700;
        }

        /* Charts Section */
        .charts-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-bottom: 35px;
        }

        .chart-box {
            background: var(--bg-secondary);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px var(--glass-shadow);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .chart-header h3 {
            font-size: 18px;
            font-weight: 600;
        }

        .chart-canvas {
            width: 100%;
            height: 250px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            position: relative;
            padding-top: 20px;
        }

        .chart-bar-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-grow: 1;
            gap: 8px;
        }

        .chart-bar {
            width: 32px;
            background: linear-gradient(180deg, var(--accent-purple), var(--accent-blue));
            border-radius: 6px 6px 0 0;
            transition: height 1s ease-out;
            position: relative;
        }

        .chart-bar:hover {
            filter: brightness(1.2);
        }

        .chart-bar-value {
            position: absolute;
            top: -24px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 11px;
            font-weight: 600;
            color: var(--text-main);
        }

        .chart-bar-label {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Notice Board List */
        .notice-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .notice-item {
            padding: 16px;
            background: var(--bg-card);
            border: 1px solid var(--glass-border);
            border-left: 4px solid var(--accent-purple);
            border-radius: 12px;
            font-size: 14px;
        }

        .notice-item h5 {
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--text-main);
        }

        .notice-item p {
            color: var(--text-muted);
            line-height: 1.4;
            font-size: 13px;
        }

        .notice-date {
            font-size: 11px;
            color: var(--accent-purple);
            margin-top: 6px;
            font-weight: 500;
        }

        /* Tables & Lists */
        .table-container {
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 26px;
            box-shadow: 0 8px 32px var(--glass-shadow);
            margin-bottom: 30px;
        }
        .table-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
        }
        .table-header-row h3 {
            font-size: 18px;
            font-weight: 600;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            text-align: left;
        }
        .data-table th {
            padding: 14px 16px;
            border-bottom: 1px solid var(--glass-border);
            color: var(--text-muted);
            font-weight: 500;
        }
        .data-table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--glass-border);
            color: var(--text-main);
        }
        .data-table tr:hover td {
            background: var(--glass-border);
        }
        
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
        }
        .badge.active { background: rgba(5, 150, 105, 0.15); color: var(--accent-emerald); }
        .badge.pending { background: rgba(217, 119, 6, 0.15); color: #d97706; }
        .badge.closed { background: rgba(148, 163, 184, 0.15); color: var(--text-muted); }

        .status-select-inline {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-color);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 4px 8px;
            border-radius: 6px;
            outline: none;
            cursor: pointer;
            font-size: 11px;
            font-weight: 600;
        }
        .status-select-inline option {
            background-color: #1e1b4b;
            color: #fff;
        }
        .dark-mode .status-select-inline option {
            background-color: #0f172a;
        }

        .btn {
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn:hover {
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: rgba(255,255,255,0.06);
            color: var(--text-main);
            border: 1px solid var(--glass-border);
        }
        .btn-secondary:hover { background: rgba(255,255,255,0.12); }

        /* Modal Backdrop */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(5, 5, 10, 0.85);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.active {
            display: flex;
        }
        .modal-card {
            background: var(--bg-secondary);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            width: 100%;
            max-width: 520px;
            padding: 30px;
            box-shadow: 0 15px 50px var(--glass-shadow);
            position: relative;
        }
        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 22px;
            cursor: pointer;
            outline: none;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text-main);
        }

        @media (max-width: 1024px) {
            .charts-row { grid-template-columns: 1fr; }
            .sidebar { display: none; }
            .main-panel { margin-left: 0; padding: 20px; }
        }
    </style>
</head>
<body>
    <!-- Toast alerts -->
    <div class="toast-container" id="toast-box"></div>

    <!-- 1. AUTH SCREEN CONTAINER -->
    <div class="auth-container" id="auth-screen">
        <div class="auth-card">
            <div class="auth-logo">
                <h2>Aurbis Workspace Management</h2>
                <p>Enterprise Office Operations & Billing Portal</p>
            </div>

            <!-- Prefilled credentials helpers -->
            <div style="margin-bottom: 25px;">
                <h5 style="font-size: 12px; color: #60a5fa; font-weight:600; margin-bottom: 8px;">Select a role to autofill test credentials:</h5>
                <div class="demo-roles-grid">
                    <button class="demo-role-btn" onclick="prefillUser('workspace_superadmin', '123456')">
                        <span class="demo-role-title">Super Admin</span>
                        <span class="demo-role-user">workspace_superadmin</span>
                    </button>
                    <button class="demo-role-btn" onclick="prefillUser('workspace_sales', 'salespass123')">
                        <span class="demo-role-title">Sales Manager</span>
                        <span class="demo-role-user">workspace_sales</span>
                    </button>
                    <button class="demo-role-btn" onclick="prefillUser('workspace_facility', 'facilitypass123')">
                        <span class="demo-role-title">Facility Manager</span>
                        <span class="demo-role-user">workspace_facility</span>
                    </button>
                    <button class="demo-role-btn" onclick="prefillUser('workspace_finance', 'financepass123')">
                        <span class="demo-role-title">Finance Manager</span>
                        <span class="demo-role-user">workspace_finance</span>
                    </button>
                </div>
            </div>

            <form id="login-form">
                <div class="form-group">
                    <label>Username or Email Address</label>
                    <input type="text" id="username-field" class="form-input" placeholder="e.g. workspace_superadmin" required>
                </div>
                <div class="form-group" id="password-group">
                    <label>Password</label>
                    <input type="password" id="password-field" class="form-input" placeholder="••••••••" required>
                </div>
                <button type="submit" class="auth-submit-btn">Secure Login</button>
            </form>
        </div>
    </div>

    <!-- 2. MAIN APPLICATION CONTAINER -->
    <div class="app-container" id="app-layout" style="display: none;">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div>
                <div class="brand">
                    <div class="brand-icon">A</div>
                    <span>Aurbis ERP</span>
                </div>
                <ul class="menu-list">
                    <li><button class="menu-item active" onclick="switchTab('dashboard')">Dashboard</button></li>
                    <li><button class="menu-item" onclick="switchTab('crm')">CRM & Leads</button></li>
                    <li><button class="menu-item" onclick="switchTab('workspaces')">Inventory / Workspace</button></li>
                    <li><button class="menu-item" onclick="switchTab('visitors')">Visitors & Passes</button></li>
                    <li><button class="menu-item" onclick="switchTab('tickets')">Facility & Support</button></li>
                    <li><button class="menu-item" onclick="switchTab('community')">Community & Events</button></li>
                    <li><button class="menu-item" onclick="switchTab('sustainability')">Sustainability (ESG)</button></li>
                    <li><button class="menu-item" onclick="switchTab('billing')">Invoices & Billing</button></li>
                    <li><button class="menu-item" onclick="switchTab('hr')">HR & Workforce</button></li>
                    <li><button class="menu-item" onclick="switchTab('assets')">Assets & Inventory</button></li>
                    <li><button class="menu-item" onclick="switchTab('vendors')">Vendor Management</button></li>
                    <li><button class="menu-item" onclick="switchTab('smartbuilding')">Smart Buildings</button></li>
                    <li><button class="menu-item" onclick="switchTab('reports')">Reports & Analytics</button></li>
                    <li><button class="menu-item" onclick="switchTab('smtp')">Email Settings</button></li>
                </ul>
            </div>
            
            <div class="user-profile-wrapper">
                <div class="user-profile">
                    <div class="avatar" id="user-avatar">A</div>
                    <div class="user-info">
                        <h4 id="user-display-name">Super Admin</h4>
                        <p id="user-display-role">workspace_super_admin</p>
                    </div>
                </div>
                <button class="logout-btn" onclick="handleLogout()">Log Out</button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-panel">
            <header class="header-section">
                <div class="title-group">
                    <h1 id="page-title">Executive Dashboard</h1>
                    <p id="page-subtitle">Real-time indicators across regions</p>
                </div>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <button class="btn btn-secondary" id="theme-toggle-btn" onclick="toggleTheme()" style="padding: 8px 14px; font-size: 12px; margin: 0; display: inline-flex; align-items: center;">🌙 Dark Mode</button>
                    <div class="badge-live">
                        <span class="live-dot"></span>
                        <span>Live Portal</span>
                    </div>
                </div>
            </header>

            <!-- Dashboard Tab -->
            <section id="tab-dashboard" class="tab-panel active">
                <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 25px; background: rgba(255,255,255,0.02); padding: 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                    <span style="align-self: center; font-size: 12px; color: var(--text-muted); font-weight: 600; margin-right: 10px;">Quick Access Modules:</span>
                    <button class="btn" style="padding: 6px 12px; font-size: 12px; background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));" onclick="switchTab('crm')">CRM & Leads</button>
                    <button class="btn" style="padding: 6px 12px; font-size: 12px; background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));" onclick="switchTab('workspaces')">Workspace Registry</button>
                    <button class="btn" style="padding: 6px 12px; font-size: 12px; background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));" onclick="switchTab('visitors')">Visitors Log</button>
                    <button class="btn" style="padding: 6px 12px; font-size: 12px; background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));" onclick="switchTab('tickets')">Facility Support</button>
                    <button class="btn" style="padding: 6px 12px; font-size: 12px; background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));" onclick="switchTab('community')">Community Feed</button>
                    <button class="btn" style="padding: 6px 12px; font-size: 12px; background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));" onclick="switchTab('sustainability')">Sustainability ESG</button>
                    <button class="btn" style="padding: 6px 12px; font-size: 12px; background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));" onclick="switchTab('billing')">Revenue Invoices</button>
                    <button class="btn" style="padding: 6px 12px; font-size: 12px; background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));" onclick="switchTab('hr')">HR & Workforce</button>
                    <button class="btn" style="padding: 6px 12px; font-size: 12px; background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));" onclick="switchTab('assets')">Assets Registry</button>
                    <button class="btn" style="padding: 6px 12px; font-size: 12px; background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));" onclick="switchTab('vendors')">Vendor Management</button>
                    <button class="btn" style="padding: 6px 12px; font-size: 12px; background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));" onclick="switchTab('smartbuilding')">Smart Buildings</button>
                    <button class="btn" style="padding: 6px 12px; font-size: 12px; background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));" onclick="switchTab('reports')">Reports & Analytics</button>
                    <button class="btn" style="padding: 6px 12px; font-size: 12px; background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));" onclick="switchTab('smtp')">Mail Delivery</button>
                </div>
                <div class="cards-grid">
                    <div class="stat-card" onclick="switchTab('workspaces')" style="cursor: pointer;">
                        <div class="card-icon">🏛️</div>
                        <span class="card-label">Total Buildings</span>
                        <div class="card-value" id="card-buildings">0</div>
                    </div>
                    <div class="stat-card" onclick="switchTab('crm')" style="cursor: pointer;">
                        <div class="card-icon">👥</div>
                        <span class="card-label">Total Tenants</span>
                        <div class="card-value" id="card-tenants">0</div>
                    </div>
                    <div class="stat-card" onclick="switchTab('workspaces')" style="cursor: pointer;">
                        <div class="card-icon">🪑</div>
                        <span class="card-label">Occupancy Rate</span>
                        <div class="card-value" id="card-occupancy">0%</div>
                    </div>
                    <div class="stat-card" onclick="switchTab('billing')" style="cursor: pointer;">
                        <div class="card-icon">💳</div>
                        <span class="card-label">Monthly Revenue</span>
                        <div class="card-value" id="card-revenue">₹0</div>
                    </div>
                    <div class="stat-card" onclick="switchTab('tickets')" style="cursor: pointer;">
                        <div class="card-icon">🛠️</div>
                        <span class="card-label">Open Tickets</span>
                        <div class="card-value" id="card-tickets">0</div>
                    </div>
                    <div class="stat-card" onclick="switchTab('visitors')" style="cursor: pointer;">
                        <div class="card-icon">📋</div>
                        <span class="card-label">Total Visitors Log</span>
                        <div class="card-value" id="card-visitors">0</div>
                    </div>
                    <div class="stat-card" onclick="switchTab('community')" style="cursor: pointer;">
                        <div class="card-icon">📢</div>
                        <span class="card-label">Announcements</span>
                        <div class="card-value" id="card-announcements">0</div>
                    </div>
                    <div class="stat-card" onclick="switchTab('sustainability')" style="cursor: pointer;">
                        <div class="card-icon">🌱</div>
                        <span class="card-label">ESG carbon metrics</span>
                        <div class="card-value" id="card-esg">0</div>
                    </div>
                </div>

                <div class="charts-row">
                    <div class="chart-box">
                        <div class="chart-header">
                            <h3>Monthly Billing Revenue (₹)</h3>
                        </div>
                        <div class="chart-canvas" id="revenue-chart">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>
                    <div class="chart-box">
                        <div class="chart-header">
                            <h3>ESG carbon metrics</h3>
                        </div>
                        <div class="notice-list">
                            <div class="notice-item">
                                <h5>Sustainability ESG Score</h5>
                                <p>Currently assessed ESG rating stands at 85/100 (Silver Grade EDGE compliance).</p>
                                <div class="notice-date">Score: 85</div>
                            </div>
                            <div class="notice-item">
                                <h5>Active Announcements</h5>
                                <p>Scheduled Power Backup Maintenance June 25th in Tower Alpha.</p>
                                <div class="notice-date">June 2026</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CRM Tab -->
            <section id="tab-crm" class="tab-panel">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Inquiries & Leads Pipeline</h3>
                        <button class="btn" onclick="openLeadModal()">+ New Lead Inquiry</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Company</th>
                                <th>Contact</th>
                                <th>Seats Required</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="crm-leads-table-body">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Workspaces Tab -->
            <section id="tab-workspaces" class="tab-panel">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Buildings Registry</h3>
                        <button class="btn" onclick="openBuildingModal()">+ Add Building</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Amenities</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="buildings-table-body">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="table-container" style="margin-top: 30px;">
                    <div class="table-header-row">
                        <h3>Book meeting room</h3>
                        <button class="btn" onclick="openBookingModal()">Schedule Booking</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Meeting Room</th>
                                <th>Booked For</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Purpose / Attendees</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="bookings-table-body">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Visitors Tab -->
            <section id="tab-visitors" class="tab-panel">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Active Visitor Log</h3>
                        <button class="btn" onclick="openVisitorModal()">+ Pre-Register Visitor</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Pass Code</th>
                                <th>Visitor Name</th>
                                <th>Mobile</th>
                                <th>Host Employee</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="visitors-table-body">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Tickets / Support Tab -->
            <section id="tab-tickets" class="tab-panel">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Maintenance Tickets</h3>
                        <button class="btn" onclick="openTicketModal()">Raise Service Ticket</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Ticket No</th>
                                <th>Title</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tickets-table-body">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Community & Events Tab -->
            <section id="tab-community" class="tab-panel">
                <div style="display: flex; flex-direction: column; gap: 30px;">
                    <div class="table-container">
                        <div class="table-header-row">
                            <h3>Announcements</h3>
                            <button class="btn" onclick="openAnnouncementModal()">+ Publish Announcement</button>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Audience</th>
                                    <th>Published</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="announcements-table-body">
                                <!-- Loaded dynamically -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="table-container">
                        <div class="table-header-row">
                            <h3>Community Events</h3>
                            <button class="btn" onclick="openEventModal()">+ Schedule Event</button>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Event Title</th>
                                    <th>Date</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="events-table-body">
                                <!-- Loaded dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Sustainability Tab -->
            <section id="tab-sustainability" class="tab-panel">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Energy & ESG Monitoring Readings</h3>
                        <button class="btn" onclick="openSustainabilityModal()">Log Energy Reading</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Building</th>
                                <th>Reading Date</th>
                                <th>Consumption (kWh)</th>
                                <th>Estimated Cost (₹)</th>
                                <th>Energy Source</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="energy-table-body">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Billing Tab -->
            <section id="tab-billing" class="tab-panel">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Invoices Database</h3>
                        <button class="btn" onclick="openInvoiceModal()">+ Generate Invoice</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>Client</th>
                                <th>Billing Type</th>
                                <th>Billing Month</th>
                                <th>Total Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="invoices-table-body">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="table-container" style="margin-top: 30px;">
                    <div class="table-header-row">
                        <h3>Payments Log</h3>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Payment ID</th>
                                <th>Invoice ID</th>
                                <th>Client ID</th>
                                <th>Amount</th>
                                <th>Payment Date</th>
                                <th>Method</th>
                                <th>Transaction ID</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="payments-table-body">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- HR & Workforce Tab -->
            <section id="tab-hr" class="tab-panel">
                <div class="table-container" style="margin-bottom: 30px;">
                    <div class="table-header-row">
                        <h3>Employee Directory</h3>
                        <button class="btn" onclick="openEmployeeModal()">+ Add Employee</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Mobile</th>
                                <th>Shift</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="employees-table-body">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Attendance Log</h3>
                        <button class="btn" onclick="openAttendanceModal()">+ Log Attendance</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Status</th>
                                <th>Remarks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="attendance-table-body">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Assets & Inventory Tab -->
            <section id="tab-assets" class="tab-panel">
                <div class="table-container" style="margin-bottom: 30px;">
                    <div class="table-header-row">
                        <h3>Asset Registry</h3>
                        <button class="btn" onclick="openAssetModal()">+ Register Asset</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Asset Name</th>
                                <th>Category</th>
                                <th>Location (Building)</th>
                                <th>Purchase Cost</th>
                                <th>Current Value</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="assets-table-body">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Asset Allocations</h3>
                        <button class="btn" onclick="openAllocationModal()">+ Allocate Asset</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Allocated To</th>
                                <th>Client Company</th>
                                <th>Allocated Date</th>
                                <th>Return Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="allocations-table-body">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Vendor Management Tab -->
            <section id="tab-vendors" class="tab-panel">
                <div class="table-container" style="margin-bottom: 30px;">
                    <div class="table-header-row">
                        <h3>Vendor Registry</h3>
                        <button class="btn" onclick="openVendorModal()">+ Register Vendor</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Vendor Name</th>
                                <th>Company Name</th>
                                <th>Service Type</th>
                                <th>Contact Person</th>
                                <th>Mobile</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="vendors-table-body">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Vendor Payments Log</h3>
                        <button class="btn" onclick="openVendorPaymentModal()">+ Log Payment</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Vendor</th>
                                <th>Amount (₹)</th>
                                <th>Payment Date</th>
                                <th>Method</th>
                                <th>Reference</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="vendor-payments-table-body">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Smart Buildings Tab -->
            <section id="tab-smartbuilding" class="tab-panel">
                <div class="table-container" style="margin-bottom: 30px;">
                    <div class="table-header-row">
                        <h3>IoT Devices</h3>
                        <button class="btn" onclick="openDeviceModal()">+ Register IoT Device</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Device Name</th>
                                <th>Type</th>
                                <th>Building</th>
                                <th>Floor</th>
                                <th>Serial Number</th>
                                <th>Manufacturer</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="devices-table-body">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="charts-row">
                    <div class="table-container" style="margin-bottom: 0;">
                        <div class="table-header-row">
                            <h3>Live Sensor Data</h3>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Device ID/Name</th>
                                    <th>Sensor Type</th>
                                    <th>Value</th>
                                    <th>Unit</th>
                                    <th>Recorded At</th>
                                </tr>
                            </thead>
                            <tbody id="sensors-table-body">
                                <!-- Loaded dynamically -->
                            </tbody>
                        </table>
                    </div>

                    <div class="table-container" style="margin-bottom: 0;">
                        <div class="table-header-row">
                            <h3>Access Gate Logs</h3>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Person Name</th>
                                    <th>Type</th>
                                    <th>Access Point</th>
                                    <th>Access Type</th>
                                    <th>Recorded At</th>
                                </tr>
                            </thead>
                            <tbody id="access-logs-table-body">
                                <!-- Loaded dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Reports & Analytics Tab -->
            <section id="tab-reports" class="tab-panel">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px;">
                    <div class="stat-card" style="padding: 30px;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                            <div>
                                <h4 style="font-size: 20px; font-weight: 600;">Monthly Revenue Report</h4>
                                <span style="font-size: 13px; color: var(--text-muted);">Lease invoice revenue, utility allocations, & outstanding collections</span>
                            </div>
                            <span class="card-icon">💳</span>
                        </div>
                        <div style="margin-bottom: 25px; line-height: 1.8;">
                            <p style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">Total Invoiced Revenue:</span> <strong id="report-total-revenue">₹0.00</strong></p>
                            <p style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">Total Payments Received:</span> <strong id="report-payments-received" style="color: var(--accent-emerald);">₹0.00</strong></p>
                            <p style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">Outstanding Balance:</span> <strong id="report-outstanding-balance" style="color: var(--accent-pink);">₹0.00</strong></p>
                            <p style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">Active Leases Count:</span> <strong id="report-active-leases">0</strong></p>
                        </div>
                        <button class="btn" onclick="printReport('revenue')">Print Full Revenue Report</button>
                    </div>

                    <div class="stat-card" style="padding: 30px;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                            <div>
                                <h4 style="font-size: 20px; font-weight: 600;">Workspace Occupancy & Capacity</h4>
                                <span style="font-size: 13px; color: var(--text-muted);">Occupancy rate indicators and space utilization metrics</span>
                            </div>
                            <span class="card-icon">🏛️</span>
                        </div>
                        <div style="margin-bottom: 25px; line-height: 1.8;">
                            <p style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">Total Desk Capacity:</span> <strong id="report-total-capacity">0</strong></p>
                            <p style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">Allocated Seats:</span> <strong id="report-allocated-seats">0</strong></p>
                            <p style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">Occupancy Percentage:</span> <strong id="report-occupancy-rate" style="color: var(--accent-blue);">0%</strong></p>
                            <p style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">Average Hot Desk Turnover:</span> <strong>1.8x / day</strong></p>
                        </div>
                        <button class="btn" onclick="printReport('occupancy')">Print Occupancy Summary</button>
                    </div>

                    <div class="stat-card" style="padding: 30px;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                            <div>
                                <h4 style="font-size: 20px; font-weight: 600;">Facility SLA & Tickets Report</h4>
                                <span style="font-size: 13px; color: var(--text-muted);">Resolution metrics, ticket volume, & SLA compliance</span>
                            </div>
                            <span class="card-icon">🛠️</span>
                        </div>
                        <div style="margin-bottom: 25px; line-height: 1.8;">
                            <p style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">Total Tickets Opened:</span> <strong id="report-total-tickets">0</strong></p>
                            <p style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">Tickets Resolved:</span> <strong id="report-resolved-tickets" style="color: var(--accent-emerald);">0</strong></p>
                            <p style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">SLA Compliance Rate:</span> <strong id="report-sla-compliance" style="color: var(--accent-emerald);">0%</strong></p>
                            <p style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">Avg Resolution Time:</span> <strong>3.5 hours</strong></p>
                        </div>
                        <button class="btn" onclick="printReport('tickets')">Print Support SLA Log</button>
                    </div>

                    <div class="stat-card" style="padding: 30px;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                            <div>
                                <h4 style="font-size: 20px; font-weight: 600;">ESG Sustainability Metrics</h4>
                                <span style="font-size: 13px; color: var(--text-muted);">Environmental logs, water footprints & ESG reporting</span>
                            </div>
                            <span class="card-icon">🌱</span>
                        </div>
                        <div style="margin-bottom: 25px; line-height: 1.8;">
                            <p style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">CO2 Offset (Estimated):</span> <strong id="report-carbon-reduction">0 kg</strong></p>
                            <p style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">Average Energy Efficiency:</span> <strong id="report-energy-efficiency" style="color: var(--accent-purple);">0%</strong></p>
                            <p style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">Waste Recycling Rate:</span> <strong id="report-recycling-rate" style="color: var(--accent-emerald);">0%</strong></p>
                            <p style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">Renewable Energy Source:</span> <strong>Grid + Solar</strong></p>
                        </div>
                        <button class="btn" onclick="printReport('esg')">Print ESG Report</button>
                    </div>
                </div>
            </section>

            <!-- SMTP Settings Tab -->
            <section id="tab-smtp" class="tab-panel">
                <div class="table-container" style="max-width: 600px;">
                    <div class="table-header-row">
                        <h3>SMTP Delivery Configuration</h3>
                    </div>
                    <form id="smtp-settings-form">
                        <div class="form-group">
                            <label>From Email</label>
                            <input type="email" id="smtp-from-email" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>From Name</label>
                            <input type="text" id="smtp-from-name" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>SMTP Host</label>
                            <input type="text" id="smtp-host" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>SMTP Port</label>
                            <input type="text" id="smtp-port" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>SMTP Username</label>
                            <input type="text" id="smtp-username" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>SMTP Password</label>
                            <input type="password" id="smtp-password" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Email Verification Template</label>
                            <textarea id="smtp-template" class="form-textarea" rows="4"></textarea>
                        </div>
                        <button type="submit" class="btn" style="margin-top: 10px;">Save Settings</button>
                    </form>
                </div>
            </section>
        </main>
    </div>

    <!-- Modals Layer -->
    <!-- 1. CRM Lead Modal -->
    <div class="modal-overlay" id="modal-crm">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('crm')">&times;</button>
            <h3 class="modal-title" id="crm-modal-title">New Lead Inquiry</h3>
            <form id="lead-form">
                <input type="hidden" id="lead-id">
                <div class="form-group">
                    <label>Company Name</label>
                    <input type="text" id="lead-company" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Contact Person</label>
                    <input type="text" id="lead-contact" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="lead-email" class="form-input">
                </div>
                <div class="form-group">
                    <label>Seats Required</label>
                    <input type="number" id="lead-seats" class="form-input" value="10" required>
                </div>
                <button type="submit" class="btn" style="width: 100%;">Save Lead Record</button>
            </form>
        </div>
    </div>

    <!-- 2. Meeting Room Booking Modal -->
    <div class="modal-overlay" id="modal-booking">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('booking')">&times;</button>
            <h3 class="modal-title" id="booking-modal-title">Book Meeting Room</h3>
            <form id="booking-form">
                <input type="hidden" id="book-id">
                <div class="form-group">
                    <label>Select Meeting Room</label>
                    <select id="book-room-id" class="form-select" required>
                        <!-- Loaded dynamically -->
                    </select>
                </div>
                <div class="form-group">
                    <label>Booked For (Client)</label>
                    <select id="book-client-id" class="form-select">
                        <option value="">-- Internal / Self (No Client) --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Purpose</label>
                    <input type="text" id="book-purpose" class="form-input" placeholder="e.g. Client Pitch">
                </div>
                <div class="form-group">
                    <label>Attendees</label>
                    <input type="number" id="book-attendees" class="form-input" value="2" min="1">
                </div>
                <div class="form-group">
                    <label>Booking Date</label>
                    <input type="date" id="book-date" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Start Time</label>
                    <input type="time" id="book-start" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>End Time</label>
                    <input type="time" id="book-end" class="form-input" required>
                </div>
                <button type="submit" class="btn" style="width: 100%;">Confirm Booking</button>
            </form>
        </div>
    </div>

    <!-- 3. Ticket Modal -->
    <div class="modal-overlay" id="modal-ticket">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('ticket')">&times;</button>
            <h3 class="modal-title" id="ticket-modal-title">Raise Service Ticket</h3>
            <form id="ticket-form">
                <input type="hidden" id="ticket-id">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" id="ticket-title" class="form-input" required placeholder="e.g. Leak in Cabin GF">
                </div>
                <div class="form-group">
                    <label>Priority</label>
                    <select id="ticket-priority" class="form-select">
                        <option value="LOW">Low</option>
                        <option value="MEDIUM" selected>Medium</option>
                        <option value="HIGH">High</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="ticket-description" class="form-textarea" rows="3"></textarea>
                </div>
                <button type="submit" class="btn" style="width: 100%;">Submit Ticket</button>
            </form>
        </div>
    </div>

    <!-- 4. Sustainability Reading Modal -->
    <div class="modal-overlay" id="modal-sustainability">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('sustainability')">&times;</button>
            <h3 class="modal-title" id="sus-modal-title">Log Energy Reading</h3>
            <form id="sustainability-form">
                <input type="hidden" id="sus-id">
                <div class="form-group">
                    <label>Select Building</label>
                    <select id="sus-building" class="form-select" required>
                        <!-- Loaded dynamically -->
                    </select>
                </div>
                <div class="form-group">
                    <label>Reading Date</label>
                    <input type="date" id="sus-date" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Energy Source</label>
                    <select id="sus-source" class="form-select" required>
                        <option value="GRID">Grid</option>
                        <option value="SOLAR">Solar</option>
                        <option value="WIND">Wind</option>
                        <option value="GENERATOR">Generator</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Consumption (kWh)</label>
                    <input type="number" step="0.01" id="sus-consumption" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Estimated Cost (₹)</label>
                    <input type="number" step="0.01" id="sus-cost" class="form-input" placeholder="e.g. 1500.00">
                </div>
                <button type="submit" class="btn" style="width: 100%;">Log Energy Reading</button>
            </form>
        </div>
    </div>

    <!-- 5. Visitor Registration Modal -->
    <div class="modal-overlay" id="modal-visitor">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('visitor')">&times;</button>
            <h3 class="modal-title" id="visitor-modal-title">Pre-Register Visitor Pass</h3>
            <form id="visitor-form">
                <input type="hidden" id="vis-id">
                <div class="form-group">
                    <label>Visitor Full Name</label>
                    <input type="text" id="vis-name" class="form-input" required placeholder="e.g. John Doe">
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" id="vis-mobile" class="form-input" required placeholder="e.g. +91 9999988888">
                </div>
                <div class="form-group">
                    <label>Company / Organization</label>
                    <input type="text" id="vis-company" class="form-input" placeholder="e.g. Google">
                </div>
                <div class="form-group">
                    <label>Purpose of Visit</label>
                    <input type="text" id="vis-purpose" class="form-input" placeholder="e.g. Client Meeting">
                </div>
                <div class="form-group">
                    <label>Host Employee Name</label>
                    <input type="text" id="vis-host" class="form-input" placeholder="e.g. Ramesh Seervi">
                </div>
                <button type="submit" class="btn" style="width: 100%;">Save Visitor Details</button>
            </form>
        </div>
    </div>

    <!-- 6. Announcement Modal -->
    <div class="modal-overlay" id="modal-announcement">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('announcement')">&times;</button>
            <h3 class="modal-title" id="ann-modal-title">Publish New Announcement</h3>
            <form id="announcement-form">
                <input type="hidden" id="ann-id">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" id="ann-title" class="form-input" required placeholder="e.g. Office Closed on Sunday">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="ann-description" class="form-textarea" rows="4" placeholder="Announcement details..."></textarea>
                </div>
                <button type="submit" class="btn" style="width: 100%;">Publish Announcement</button>
            </form>
        </div>
    </div>

    <!-- 7. Event Modal -->
    <div class="modal-overlay" id="modal-event">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('event')">&times;</button>
            <h3 class="modal-title" id="evt-modal-title">Schedule Community Event</h3>
            <form id="event-form">
                <input type="hidden" id="evt-id">
                <div class="form-group">
                    <label>Event Title</label>
                    <input type="text" id="evt-title" class="form-input" required placeholder="e.g. Pitch Night">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="evt-description" class="form-textarea" rows="3" placeholder="Event description..."></textarea>
                </div>
                <div class="form-group">
                    <label>Event Date & Time</label>
                    <input type="datetime-local" id="evt-date" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" id="evt-location" class="form-input" required placeholder="e.g. Rooftop Lounge">
                </div>
                <button type="submit" class="btn" style="width: 100%;">Schedule Event</button>
            </form>
        </div>
    </div>

    <!-- 10. Building Modal -->
    <div class="modal-overlay" id="modal-building">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('building')">&times;</button>
            <h3 class="modal-title" id="building-modal-title">Add New Building</h3>
            <form id="building-form">
                <input type="hidden" id="building-id">
                <div class="form-group">
                    <label>Building Name</label>
                    <input type="text" id="build-name" class="form-input" required placeholder="e.g. Tower Gamma">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea id="build-address" class="form-textarea" rows="2" placeholder="e.g. 500 Tech Park Road"></textarea>
                </div>
                <div class="form-group">
                    <label>City</label>
                    <input type="text" id="build-city" class="form-input" value="Bangalore" required>
                </div>
                <div class="form-group">
                    <label>State</label>
                    <input type="text" id="build-state" class="form-input" value="Karnataka" required>
                </div>
                <div class="form-group">
                    <label>Total Floors</label>
                    <input type="number" id="build-floors" class="form-input" value="5" required>
                </div>
                <div class="form-group">
                    <label>Total Seats</label>
                    <input type="number" id="build-seats" class="form-input" value="100" required>
                </div>
                <div class="form-group">
                    <label>Amenities</label>
                    <input type="text" id="build-amenities" class="form-input" placeholder="e.g. Parking, Cafeteria">
                </div>
                <button type="submit" class="btn" style="width: 100%;">Save Building Details</button>
            </form>
        </div>
    </div>

    <!-- 8. Invoice Modal -->
    <div class="modal-overlay" id="modal-invoice">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('invoice')">&times;</button>
            <h3 class="modal-title" id="invoice-modal-title">Generate Revenue Invoice</h3>
            <form id="invoice-form">
                <input type="hidden" id="inv-id">
                <div class="form-group">
                    <label>Select Client</label>
                    <select id="inv-client-id" class="form-select" required>
                        <!-- Loaded dynamically -->
                    </select>
                </div>
                <div class="form-group">
                    <label>Billing Type</label>
                    <select id="inv-billing-type" class="form-select" required>
                        <option value="LEASE" selected>Lease / Rent</option>
                        <option value="SEAT">Seat-Based</option>
                        <option value="UTILITY">Utility Charges</option>
                        <option value="MAINTENANCE">Maintenance Charges</option>
                        <option value="SERVICES">Additional Service Charges</option>
                        <option value="PARKING">Parking Charges</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Billing Month (YYYY-MM)</label>
                    <input type="month" id="inv-billing-month" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Base Amount (₹)</label>
                    <input type="number" step="0.01" id="inv-base-amount" class="form-input" placeholder="e.g. 100000" required>
                </div>
                <div class="form-group">
                    <label>GST Percentage (%)</label>
                    <input type="number" step="0.01" id="inv-gst-percentage" class="form-input" value="18.00" required>
                </div>
                <div class="form-group">
                    <label>GST Amount (₹)</label>
                    <input type="number" step="0.01" id="inv-gst-amount" class="form-input" disabled value="0.00">
                </div>
                <div class="form-group">
                    <label>Total Amount (₹)</label>
                    <input type="number" step="0.01" id="inv-total-amount" class="form-input" disabled value="0.00">
                </div>
                <div class="form-group">
                    <label>Due Date</label>
                    <input type="date" id="inv-due-date" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Notes / Remarks</label>
                    <textarea id="inv-notes" class="form-textarea" rows="2" placeholder="Internal or client notes..."></textarea>
                </div>
                <button type="submit" class="btn" style="width: 100%;">Generate Invoice</button>
            </form>
        </div>
    </div>

    <!-- 9. Record Payment Modal -->
    <div class="modal-overlay" id="modal-payment">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('payment')">&times;</button>
            <h3 class="modal-title">Record Payment</h3>
            <form id="payment-form">
                <input type="hidden" id="pay-invoice-id">
                <div class="form-group">
                    <label>Invoice Number</label>
                    <input type="text" id="pay-invoice-no" class="form-input" disabled>
                </div>
                <div class="form-group">
                    <label>Total Outstanding Amount (₹)</label>
                    <input type="text" id="pay-invoice-amount" class="form-input" disabled>
                </div>
                <div class="form-group">
                    <label>Amount Paid (₹)</label>
                    <input type="number" step="0.01" id="pay-amount" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Payment Date</label>
                    <input type="date" id="pay-date" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Payment Method</label>
                    <select id="pay-method" class="form-select" required>
                        <option value="BANK_TRANSFER" selected>Bank Transfer (IMPS/NEFT/RTGS)</option>
                        <option value="CASH">Cash Payment</option>
                        <option value="CARD">Debit / Credit Card</option>
                        <option value="ONLINE">UPI / Net Banking</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Transaction ID / Reference Number</label>
                    <input type="text" id="pay-txn-id" class="form-input" placeholder="e.g. TXN1234567890" required>
                </div>
                <button type="submit" class="btn" style="width: 100%;">Record Payment</button>
            </form>
        </div>
    </div>

    <!-- 11. Invoice Details & History Modal -->
    <div class="modal-overlay" id="modal-invoice-detail">
        <div class="modal-card" style="max-width: 800px; width: 95%; background: #0f172a; color: #fff;">
            <button class="modal-close" style="color: #fff;" onclick="closeModal('invoice-detail')">&times;</button>
            
            <div id="invoice-print-container" style="background: #1e293b; padding: 30px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); margin-top: 15px;">
                <!-- Header -->
                <div style="display: flex; justify-content: space-between; border-bottom: 2px solid rgba(255,255,255,0.1); padding-bottom: 20px; margin-bottom: 25px;">
                    <div>
                        <h2 style="margin: 0; color: #fff; font-size: 24px;">Aurbis Space Management</h2>
                        <p style="margin: 5px 0 0 0; color: #94a3b8; font-size: 13px;">Premium Workspace & Facility Solutions</p>
                    </div>
                    <div style="text-align: right;">
                        <h2 style="margin: 0; color: #818cf8; font-size: 24px; font-weight: 700;">INVOICE</h2>
                        <p style="margin: 5px 0 0 0; color: #94a3b8; font-size: 13px; font-weight: bold;" id="det-inv-no">INV-XXXX-XXXX</p>
                    </div>
                </div>

                <!-- Info Grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                    <div>
                        <h4 style="margin: 0 0 10px 0; color: #38bdf8; text-transform: uppercase; font-size: 11px; letter-spacing: 1px;">Billed From</h4>
                        <p style="margin: 0; font-size: 13px; font-weight: 600; color: #fff;">Aurbis Space Management Pvt Ltd</p>
                        <p style="margin: 4px 0 0 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">
                            500 Tech Park Road, Outer Ring Road,<br/>
                            Bangalore, Karnataka - 560103<br/>
                            GSTIN: 29AAFCA8832R1ZX
                        </p>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 10px 0; color: #38bdf8; text-transform: uppercase; font-size: 11px; letter-spacing: 1px;">Billed To</h4>
                        <p style="margin: 0; font-size: 13px; font-weight: 600; color: #fff;" id="det-client-name">Client Name</p>
                        <p style="margin: 4px 0 0 0; font-size: 12px; color: #94a3b8; line-height: 1.5;" id="det-client-details">
                            Loading details...
                        </p>
                    </div>
                </div>

                <!-- Invoice Meta Details -->
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 15px; border-radius: 8px; margin-bottom: 30px;">
                    <div>
                        <span style="font-size: 11px; color: #94a3b8; display: block; margin-bottom: 5px;">Billing Month</span>
                        <strong style="font-size: 13px; color: #fff;" id="det-billing-month">-</strong>
                    </div>
                    <div>
                        <span style="font-size: 11px; color: #94a3b8; display: block; margin-bottom: 5px;">Billing Type</span>
                        <strong style="font-size: 13px; color: #fff;" id="det-billing-type">-</strong>
                    </div>
                    <div>
                        <span style="font-size: 11px; color: #94a3b8; display: block; margin-bottom: 5px;">Due Date</span>
                        <strong style="font-size: 13px; color: #fff;" id="det-due-date">-</strong>
                    </div>
                    <div>
                        <span style="font-size: 11px; color: #94a3b8; display: block; margin-bottom: 5px;">Payment Status</span>
                        <span id="det-status-badge" class="badge pending" style="display: inline-block;">PENDING</span>
                    </div>
                </div>

                <!-- Pricing Table -->
                <h4 style="margin: 0 0 10px 0; color: #38bdf8; text-transform: uppercase; font-size: 11px; letter-spacing: 1px;">Amount Breakdown</h4>
                <table class="data-table" style="margin-bottom: 25px; width: 100%;">
                    <thead>
                        <tr>
                            <th style="color: #fff; background: #334155;">Description</th>
                            <th style="text-align: right; color: #fff; background: #334155;">Base Amount</th>
                            <th style="text-align: right; color: #fff; background: #334155;">GST Rate</th>
                            <th style="text-align: right; color: #fff; background: #334155;">GST Amount</th>
                            <th style="text-align: right; color: #fff; background: #334155;">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="det-description" style="font-weight: 500; background: transparent; border-bottom: 1px solid rgba(255,255,255,0.05); color: #fff;">Workspace Lease / Services</td>
                            <td id="det-base-amount" style="text-align: right; background: transparent; border-bottom: 1px solid rgba(255,255,255,0.05); color: #fff;">₹0.00</td>
                            <td id="det-gst-pct" style="text-align: right; background: transparent; border-bottom: 1px solid rgba(255,255,255,0.05); color: #fff;">18%</td>
                            <td id="det-gst-amount" style="text-align: right; background: transparent; border-bottom: 1px solid rgba(255,255,255,0.05); color: #fff;">₹0.00</td>
                            <td id="det-total-amount" style="text-align: right; font-weight: 700; color: #818cf8; background: transparent; border-bottom: 1px solid rgba(255,255,255,0.05);">₹0.00</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Notes -->
                <div style="margin-bottom: 30px;" id="det-notes-wrapper">
                    <h4 style="margin: 0 0 5px 0; color: #fff; font-size: 12px; font-weight: 600;">Remarks / Notes:</h4>
                    <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5; font-style: italic;" id="det-notes">-</p>
                </div>

                <!-- Payment History log -->
                <h4 style="margin: 0 0 10px 0; color: #38bdf8; text-transform: uppercase; font-size: 11px; letter-spacing: 1px;">Invoice Payments History</h4>
                <table class="data-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="color: #fff; background: #334155;">Payment ID</th>
                            <th style="color: #fff; background: #334155;">Payment Date</th>
                            <th style="color: #fff; background: #334155;">Method</th>
                            <th style="color: #fff; background: #334155;">Transaction ID</th>
                            <th style="text-align: right; color: #fff; background: #334155;">Amount Paid</th>
                            <th style="color: #fff; background: #334155;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="det-payment-history-body">
                        <!-- Loaded dynamically -->
                    </tbody>
                </table>
            </div>

            <!-- Print Actions -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 25px;">
                <button class="btn btn-secondary" onclick="closeModal('invoice-detail')">Close</button>
                <button class="btn" style="background: linear-gradient(135deg, #06b6d4, #3b82f6);" onclick="printInvoicePDF()">Download PDF / Print</button>
            </div>
        </div>
    </div>

    <!-- 12. Employee Profile Modal -->
    <div class="modal-overlay" id="modal-employee">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('employee')">&times;</button>
            <h3 class="modal-title" id="employee-modal-title">New Employee Profile</h3>
            <form id="employee-form">
                <input type="hidden" id="employee-id">
                <div class="form-group">
                    <label>Employee Code</label>
                    <input type="text" id="employee-code" class="form-input" placeholder="e.g. EMP-003" required>
                </div>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" id="employee-name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <select id="employee-department" class="form-select">
                        <option value="Facilities">Facilities</option>
                        <option value="Security">Security</option>
                        <option value="IT Support">IT Support</option>
                        <option value="HR & Admin">HR & Admin</option>
                        <option value="Finance">Finance</option>
                        <option value="Sales">Sales</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Designation</label>
                    <input type="text" id="employee-designation" class="form-input">
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" id="employee-mobile" class="form-input">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="employee-email" class="form-input">
                </div>
                <div class="form-group">
                    <label>Joining Date</label>
                    <input type="date" id="employee-joining" class="form-input">
                </div>
                <div class="form-group">
                    <label>Monthly Salary (₹)</label>
                    <input type="number" id="employee-salary" class="form-input" value="0">
                </div>
                <div class="form-group">
                    <label>Work Shift</label>
                    <select id="employee-shift" class="form-select">
                        <option value="DAY">Day Shift</option>
                        <option value="NIGHT">Night Shift</option>
                        <option value="SHIFT_A">Shift A</option>
                        <option value="SHIFT_B">Shift B</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Assigned Building</label>
                    <select id="employee-building" class="form-select employee-building-select">
                        <option value="">No Building Assigned</option>
                    </select>
                </div>
                <button type="submit" class="btn" style="margin-top: 10px;">Save Profile</button>
            </form>
        </div>
    </div>

    <!-- 13. Attendance Log Modal -->
    <div class="modal-overlay" id="modal-attendance">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('attendance')">&times;</button>
            <h3 class="modal-title" id="attendance-modal-title">Log Attendance Record</h3>
            <form id="attendance-form">
                <input type="hidden" id="attendance-id">
                <div class="form-group">
                    <label>Employee</label>
                    <select id="attendance-employee" class="form-select" required>
                        <!-- Loaded dynamically -->
                    </select>
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" id="attendance-date" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Check In Time</label>
                    <input type="text" id="attendance-checkin" class="form-input" placeholder="e.g. 09:00:00">
                </div>
                <div class="form-group">
                    <label>Check Out Time</label>
                    <input type="text" id="attendance-checkout" class="form-input" placeholder="e.g. 18:00:00">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select id="attendance-status" class="form-select">
                        <option value="PRESENT">Present</option>
                        <option value="ABSENT">Absent</option>
                        <option value="LATE">Late</option>
                        <option value="LEAVE">On Leave</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Remarks / Notes</label>
                    <textarea id="attendance-remarks" class="form-textarea" rows="2"></textarea>
                </div>
                <button type="submit" class="btn" style="margin-top: 10px;">Log Attendance</button>
            </form>
        </div>
    </div>

    <!-- 14. Asset Registration Modal -->
    <div class="modal-overlay" id="modal-asset">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('asset')">&times;</button>
            <h3 class="modal-title" id="asset-modal-title">Register Enterprise Asset</h3>
            <form id="asset-form">
                <input type="hidden" id="asset-id">
                <div class="form-group">
                    <label>Asset Code</label>
                    <input type="text" id="asset-code" class="form-input" placeholder="e.g. AST-003">
                </div>
                <div class="form-group">
                    <label>Asset Name</label>
                    <input type="text" id="asset-name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select id="asset-category" class="form-select">
                        <option value="IT Equipment">IT Equipment</option>
                        <option value="Furniture">Furniture</option>
                        <option value="HVAC">HVAC System</option>
                        <option value="Safety & Security">Safety & Security</option>
                        <option value="Pantry Appliances">Pantry Appliances</option>
                        <option value="General">General</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Building</label>
                    <select id="asset-building" class="form-select asset-building-select" required>
                        <!-- Loaded dynamically -->
                    </select>
                </div>
                <div class="form-group">
                    <label>Floor</label>
                    <select id="asset-floor" class="form-select asset-floor-select">
                        <option value="">Select Floor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Purchase Date</label>
                    <input type="date" id="asset-purchase-date" class="form-input">
                </div>
                <div class="form-group">
                    <label>Purchase Cost (₹)</label>
                    <input type="number" id="asset-purchase-cost" class="form-input" value="0">
                </div>
                <div class="form-group">
                    <label>Current Net Value (₹)</label>
                    <input type="number" id="asset-current-value" class="form-input" value="0">
                </div>
                <div class="form-group">
                    <label>Warranty Expiry</label>
                    <input type="date" id="asset-warranty" class="form-input">
                </div>
                <button type="submit" class="btn" style="margin-top: 10px;">Register Asset</button>
            </form>
        </div>
    </div>

    <!-- 15. Asset Allocation Modal -->
    <div class="modal-overlay" id="modal-allocation">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('allocation')">&times;</button>
            <h3 class="modal-title" id="allocation-modal-title">Log Asset Allocation</h3>
            <form id="allocation-form">
                <input type="hidden" id="allocation-id">
                <div class="form-group">
                    <label>Asset</label>
                    <select id="allocation-asset" class="form-select" required>
                        <!-- Loaded dynamically -->
                    </select>
                </div>
                <div class="form-group">
                    <label>Allocated To Name (Individual)</label>
                    <input type="text" id="allocation-to" class="form-input">
                </div>
                <div class="form-group">
                    <label>Allocated Client (Company)</label>
                    <select id="allocation-client" class="form-select">
                        <option value="">No Client (Internal Allocation)</option>
                        <!-- Loaded dynamically -->
                    </select>
                </div>
                <div class="form-group">
                    <label>Allocation Location (Building)</label>
                    <select id="allocation-building" class="form-select allocation-building-select">
                        <!-- Loaded dynamically -->
                    </select>
                </div>
                <div class="form-group">
                    <label>Floor</label>
                    <select id="allocation-floor" class="form-select allocation-floor-select">
                        <option value="">Select Floor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Allocation Date</label>
                    <input type="date" id="allocation-date" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Expected Return Date</label>
                    <input type="date" id="allocation-return" class="form-input">
                </div>
                <button type="submit" class="btn" style="margin-top: 10px;">Allocate Asset</button>
            </form>
        </div>
    </div>

    <!-- 16. Vendor Registration Modal -->
    <div class="modal-overlay" id="modal-vendor">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('vendor')">&times;</button>
            <h3 class="modal-title" id="vendor-modal-title">Register Service Vendor</h3>
            <form id="vendor-form">
                <input type="hidden" id="vendor-id">
                <div class="form-group">
                    <label>Vendor Representative Name</label>
                    <input type="text" id="vendor-name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Company / Corporate Name</label>
                    <input type="text" id="vendor-company" class="form-input">
                </div>
                <div class="form-group">
                    <label>Service Type</label>
                    <select id="vendor-service" class="form-select">
                        <option value="Housekeeping">Housekeeping</option>
                        <option value="Security">Security Guard Services</option>
                        <option value="HVAC Maintenance">HVAC & ACMV Maintenance</option>
                        <option value="Pest Control">Pest Control</option>
                        <option value="Catering & Pantry">Catering & Pantry Operations</option>
                        <option value="IT & Networking">IT Infrastructure & Networking</option>
                        <option value="General Support">General Maintenance Support</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Contact Person Mobile</label>
                    <input type="text" id="vendor-mobile" class="form-input">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="vendor-email" class="form-input">
                </div>
                <div class="form-group">
                    <label>GST Number</label>
                    <input type="text" id="vendor-gst" class="form-input">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea id="vendor-address" class="form-textarea" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Contract Start Date</label>
                    <input type="date" id="vendor-start" class="form-input">
                </div>
                <div class="form-group">
                    <label>Contract End Date</label>
                    <input type="date" id="vendor-end" class="form-input">
                </div>
                <div class="form-group">
                    <label>SLA Terms Summary</label>
                    <textarea id="vendor-sla" class="form-textarea" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Quality Rating (1.0 to 5.0)</label>
                    <input type="number" id="vendor-rating" class="form-input" min="1" max="5" step="0.1" value="5.0">
                </div>
                <button type="submit" class="btn" style="margin-top: 10px;">Register Vendor</button>
            </form>
        </div>
    </div>

    <!-- 17. Vendor Payment Modal -->
    <div class="modal-overlay" id="modal-vendor-payment">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('vendor-payment')">&times;</button>
            <h3 class="modal-title" id="vendor-payment-modal-title">Log Vendor Outflow Payment</h3>
            <form id="vendor-payment-form">
                <input type="hidden" id="vendor-payment-id">
                <div class="form-group">
                    <label>Vendor Representative / Company</label>
                    <select id="vendor-payment-vendor" class="form-select" required>
                        <!-- Loaded dynamically -->
                    </select>
                </div>
                <div class="form-group">
                    <label>Payment Amount (₹)</label>
                    <input type="number" id="vendor-payment-amount" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Payment Date</label>
                    <input type="date" id="vendor-payment-date" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Payment Method</label>
                    <select id="vendor-payment-method" class="form-select">
                        <option value="UPI">UPI / Digital Wallets</option>
                        <option value="BANK_TRANSFER">Bank Transfer (NEFT/RTGS)</option>
                        <option value="CHEQUE">Cheque Clearance</option>
                        <option value="CASH">Cash Disbursements</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Transaction / Reference Code</label>
                    <input type="text" id="vendor-payment-reference" class="form-input" placeholder="e.g. TXN982736412">
                </div>
                <div class="form-group">
                    <label>Payment Description</label>
                    <textarea id="vendor-payment-description" class="form-textarea" rows="2"></textarea>
                </div>
                <button type="submit" class="btn" style="margin-top: 10px;">Log Payment</button>
            </form>
        </div>
    </div>

    <!-- 18. IoT Device Registration Modal -->
    <div class="modal-overlay" id="modal-device">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('device')">&times;</button>
            <h3 class="modal-title" id="device-modal-title">Register IoT Sensor / Gate Device</h3>
            <form id="device-form">
                <input type="hidden" id="device-id">
                <div class="form-group">
                    <label>Device Name / Tag</label>
                    <input type="text" id="device-name" class="form-input" placeholder="e.g. Temp Sensor - Floor 2" required>
                </div>
                <div class="form-group">
                    <label>Device Type</label>
                    <select id="device-type" class="form-select">
                        <option value="TEMPERATURE_SENSOR">Temperature Sensor</option>
                        <option value="AIR_QUALITY">Air Quality (PM2.5 / CO2)</option>
                        <option value="OCCUPANCY_SENSOR">Occupancy Counter Sensor</option>
                        <option value="ACCESS_CONTROL">Access Gate Gatekeeper</option>
                        <option value="SMART_LIGHTING">Smart Lighting Module</option>
                        <option value="THERMOSTAT">Smart Thermostat Controller</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Installed Building</label>
                    <select id="device-building" class="form-select device-building-select" required>
                        <!-- Loaded dynamically -->
                    </select>
                </div>
                <div class="form-group">
                    <label>Installed Floor</label>
                    <select id="device-floor" class="form-select device-floor-select">
                        <option value="">Select Floor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Hardware Serial Number</label>
                    <input type="text" id="device-serial" class="form-input">
                </div>
                <div class="form-group">
                    <label>Manufacturer</label>
                    <input type="text" id="device-manufacturer" class="form-input">
                </div>
                <div class="form-group">
                    <label>Installed Date</label>
                    <input type="date" id="device-installed" class="form-input">
                </div>
                <button type="submit" class="btn" style="margin-top: 10px;">Register Device</button>
            </form>
        </div>
    </div>

    <!-- Javascript Portal Scripts -->
    <script>
        const API_URL = window.location.origin + '/wp-json/workspace-erp/v1';

        // Prefill credentials helper
        function prefillUser(username, password) {
            document.getElementById('username-field').value = username;
            document.getElementById('password-field').value = password;
        }

        // Show Toast Notifications
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-box');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerText = message;
            container.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 50);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Open/Close Modals
        function openModal(name) {
            document.getElementById(`modal-${name}`).classList.add('active');
        }
        function closeModal(name) {
            document.getElementById(`modal-${name}`).classList.remove('active');
        }

        // Switch Active navigation tab
        function switchTab(tabName) {
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.menu-item').forEach(m => m.classList.remove('active'));

            document.getElementById(`tab-${tabName}`).classList.add('active');
            
            // Highlight matching sidebar button
            const buttons = Array.from(document.querySelectorAll('.menu-item'));
            const target = buttons.find(b => b.getAttribute('onclick').includes(tabName));
            if (target) target.classList.add('active');

            // Set Page Title
            const titleGroup = document.getElementById('page-title');
            const subtitleGroup = document.getElementById('page-subtitle');
            if (tabName === 'dashboard') {
                titleGroup.innerText = 'Executive Dashboard';
                subtitleGroup.innerText = 'Real-time indicators across regions';
                loadDashboardData();
            } else if (tabName === 'crm') {
                titleGroup.innerText = 'CRM & Leads Management';
                subtitleGroup.innerText = 'Acquisitions & conversion pipelines';
                loadLeads();
            } else if (tabName === 'workspaces') {
                titleGroup.innerText = 'Workspace & Properties';
                subtitleGroup.innerText = 'Inventories, seat allocations, and meeting rooms';
                loadWorkspaceData();
            } else if (tabName === 'visitors') {
                titleGroup.innerText = 'Visitors & Guest Passes';
                subtitleGroup.innerText = 'Track guest check-ins, pre-approvals, and generate day passes';
                loadVisitors();
            } else if (tabName === 'tickets') {
                titleGroup.innerText = 'Facility Management & Tickets';
                subtitleGroup.innerText = 'Service logs & preventive scheduling';
                loadTickets();
            } else if (tabName === 'community') {
                titleGroup.innerText = 'Community, News & Events';
                subtitleGroup.innerText = 'Broadcast announcements and organize community social gatherings';
                loadCommunityData();
            } else if (tabName === 'sustainability') {
                titleGroup.innerText = 'Sustainability Monitoring';
                subtitleGroup.innerText = 'Energy logs, water footprints & ESG reporting';
                loadSustainability();
            } else if (tabName === 'billing') {
                titleGroup.innerText = 'Revenue Invoices';
                subtitleGroup.innerText = 'Client leases, payments & utility invoice schedules';
                loadBilling();
            } else if (tabName === 'hr') {
                titleGroup.innerText = 'HR & Workforce';
                subtitleGroup.innerText = 'Manage employee records, shifts, and log daily attendance logs';
                loadHr();
            } else if (tabName === 'assets') {
                titleGroup.innerText = 'Assets & Inventory';
                subtitleGroup.innerText = 'Register workplace hardware assets and log allocations';
                loadAssets();
            } else if (tabName === 'vendors') {
                titleGroup.innerText = 'Vendor Management';
                subtitleGroup.innerText = 'Track external utility service vendors and payout schedules';
                loadVendors();
            } else if (tabName === 'smartbuilding') {
                titleGroup.innerText = 'Smart Buildings';
                subtitleGroup.innerText = 'IoT devices monitoring, gate access pass, and sensor telemetry';
                loadSmartBuilding();
            } else if (tabName === 'reports') {
                titleGroup.innerText = 'Reports & Analytics';
                subtitleGroup.innerText = 'Analytical summary reports of billing revenue, space, support, and ESG';
                loadReports();
            } else if (tabName === 'smtp') {
                titleGroup.innerText = 'Email Delivery Configurations';
                subtitleGroup.innerText = 'Global email templates & verification codes settings';
                loadSmtp();
            }
        }

        // Login Handler
        document.getElementById('login-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const username = document.getElementById('username-field').value;
            const password = document.getElementById('password-field').value;

            try {
                const res = await fetch(`${API_URL}/auth/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password })
                });
                const data = await res.json();
                
                if (data.success) {
                    localStorage.setItem('workspace_jwt_token', data.data.token);
                    showToast('Logged in successfully!');
                    
                    // Render Profile
                    document.getElementById('user-avatar').innerText = data.data.user.name.charAt(0).toUpperCase();
                    document.getElementById('user-display-name').innerText = data.data.user.name;
                    document.getElementById('user-display-role').innerText = data.data.user.role;

                    // Transition Views
                    document.getElementById('auth-screen').style.display = 'none';
                    document.getElementById('app-layout').style.display = 'flex';

                    switchTab('dashboard');
                } else {
                    showToast(data.message || 'Login failed. Invalid credentials.', 'error');
                }
            } catch (err) {
                showToast('API Connection Error.', 'error');
            }
        });

        // Logout Handler
        function handleLogout() {
            localStorage.removeItem('workspace_jwt_token');
            document.getElementById('app-layout').style.display = 'none';
            document.getElementById('auth-screen').style.display = 'flex';
            showToast('Logged out successfully.');
        }

        // Helper to perform authenticated GET requests
        async function apiGet(endpoint) {
            const token = localStorage.getItem('workspace_jwt_token');
            const res = await fetch(`${API_URL}${endpoint}`, {
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
            });
            if (res.status === 401) {
                handleLogout();
                throw new Error('Unauthorized');
            }
            return await res.json();
        }

        // Helper to perform authenticated POST requests
        async function apiPost(endpoint, body) {
            const token = localStorage.getItem('workspace_jwt_token');
            const res = await fetch(`${API_URL}${endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify(body)
            });
            if (res.status === 401) {
                handleLogout();
                throw new Error('Unauthorized');
            }
            return await res.json();
        }

        // Helper to perform authenticated PUT requests
        async function apiPut(endpoint, body) {
            const token = localStorage.getItem('workspace_jwt_token');
            const res = await fetch(`${API_URL}${endpoint}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify(body)
            });
            if (res.status === 401) {
                handleLogout();
                throw new Error('Unauthorized');
            }
            return await res.json();
        }

        // Helper to perform authenticated DELETE requests
        async function apiDelete(endpoint) {
            const token = localStorage.getItem('workspace_jwt_token');
            const res = await fetch(`${API_URL}${endpoint}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
            });
            if (res.status === 401) {
                handleLogout();
                throw new Error('Unauthorized');
            }
            return await res.json();
        }

        // 1. Dashboard Module
        async function loadDashboardData() {
            try {
                const res = await apiGet('/dashboard');
                if (res.success) {
                    const cards = res.data.cards;
                    document.getElementById('card-buildings').innerText = cards.total_buildings;
                    document.getElementById('card-tenants').innerText = cards.total_tenants;
                    document.getElementById('card-occupancy').innerText = `${cards.occupancy_rate}%`;
                    document.getElementById('card-revenue').innerText = `₹${parseFloat(cards.monthly_revenue).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    document.getElementById('card-tickets').innerText = cards.open_tickets;
                    document.getElementById('card-visitors').innerText = cards.total_visitors;
                    document.getElementById('card-announcements').innerText = cards.total_announcements;
                    document.getElementById('card-esg').innerText = cards.esg_score;

                    // Render Revenue Trends SVG Chart
                    const chartCanvas = document.getElementById('revenue-chart');
                    chartCanvas.innerHTML = '';
                    const trends = res.data.charts.revenue_trends;
                    
                    trends.forEach(t => {
                        const barContainer = document.createElement('div');
                        barContainer.className = 'chart-bar-container';

                        const bar = document.createElement('div');
                        bar.className = 'chart-bar';
                        // Max value range roughly 300,000
                        const heightPercent = Math.min(100, Math.max(10, (t.revenue / 300000) * 100));
                        bar.style.height = `${heightPercent}%`;

                        const valueSpan = document.createElement('span');
                        valueSpan.className = 'chart-bar-value';
                        valueSpan.innerText = `₹${Math.round(t.revenue / 1000)}k`;
                        bar.appendChild(valueSpan);

                        const labelSpan = document.createElement('span');
                        labelSpan.className = 'chart-bar-label';
                        labelSpan.innerText = t.month.substring(5);

                        barContainer.appendChild(bar);
                        barContainer.appendChild(labelSpan);
                        chartCanvas.appendChild(barContainer);
                    });
                }
            } catch (err) {
                console.error(err);
            }
        }

        let currentLeads = [];
        async function loadLeads() {
            try {
                const res = await apiGet('/crm/leads');
                if (res.success) {
                    currentLeads = res.data.data;
                    const tbody = document.getElementById('crm-leads-table-body');
                    tbody.innerHTML = '';
                    currentLeads.forEach(lead => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${lead.lead_code}</td>
                            <td>${lead.company_name}</td>
                            <td>${lead.contact_person}</td>
                            <td>${lead.seats_required}</td>
                            <td>
                                <select class="status-select-inline" onchange="confirmLeadStatusChange(${lead.id}, this, '${lead.status}')">
                                    <option value="NEW" ${lead.status === 'NEW' ? 'selected' : ''}>New</option>
                                    <option value="CONTACTED" ${lead.status === 'CONTACTED' ? 'selected' : ''}>Contacted</option>
                                    <option value="QUALIFIED" ${lead.status === 'QUALIFIED' ? 'selected' : ''}>Qualified</option>
                                    <option value="LOST" ${lead.status === 'LOST' ? 'selected' : ''}>Lost</option>
                                    <option value="WON" ${lead.status === 'WON' ? 'selected' : ''}>Won</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openLeadModal(${lead.id})">Edit</button>
                                <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="deleteLead(${lead.id})">Delete</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (err) {
                showToast('Failed to load leads', 'error');
            }
        }

        async function confirmLeadStatusChange(id, selectElement, oldStatus) {
            const newStatus = selectElement.value;
            if (newStatus === oldStatus) return;
            if (!confirm(`Are you sure you want to change the lead status from "${oldStatus}" to "${newStatus}"?`)) {
                selectElement.value = oldStatus;
                return;
            }
            try {
                const res = await apiPut(`/crm/leads/${id}`, { status: newStatus });
                if (res.success) {
                    showToast('Lead status updated successfully!');
                    loadLeads();
                } else {
                    showToast(res.message, 'error');
                    selectElement.value = oldStatus;
                }
            } catch (err) {
                showToast('Failed to update lead status.', 'error');
                selectElement.value = oldStatus;
            }
        }

        function openLeadModal(leadId = null) {
            document.getElementById('lead-form').reset();
            if (leadId) {
                const lead = currentLeads.find(l => l.id == leadId);
                if (lead) {
                    document.getElementById('crm-modal-title').innerText = 'Edit Lead Inquiry';
                    document.getElementById('lead-id').value = lead.id;
                    document.getElementById('lead-company').value = lead.company_name;
                    document.getElementById('lead-contact').value = lead.contact_person;
                    document.getElementById('lead-email').value = lead.email;
                    document.getElementById('lead-seats').value = lead.seats_required;
                }
            } else {
                document.getElementById('crm-modal-title').innerText = 'New Lead Inquiry';
                document.getElementById('lead-id').value = '';
            }
            openModal('crm');
        }

        async function deleteLead(id) {
            if (!confirm('Are you sure you want to delete this lead?')) return;
            try {
                const res = await apiDelete(`/crm/leads/${id}`);
                if (res.success) {
                    showToast('Lead deleted successfully!');
                    loadLeads();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to delete lead.', 'error');
            }
        }

        // Create/Update Lead submission handler
        document.getElementById('lead-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const leadId = document.getElementById('lead-id').value;
            const company_name = document.getElementById('lead-company').value;
            const contact_person = document.getElementById('lead-contact').value;
            const email = document.getElementById('lead-email').value;
            const seats_required = parseInt(document.getElementById('lead-seats').value);

            try {
                let res;
                if (leadId) {
                    res = await apiPut(`/crm/leads/${leadId}`, { company_name, contact_person, email, seats_required });
                } else {
                    res = await apiPost('/crm/leads', { company_name, contact_person, email, seats_required });
                }

                if (res.success) {
                    showToast(leadId ? 'Lead updated successfully!' : 'Lead inquiry added successfully!');
                    closeModal('crm');
                    loadLeads();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to submit lead', 'error');
            }
        });

        // 3. Workspace Module
        let currentBuildings = [];
        let currentBookings = [];
        let currentMeetingRooms = [];
        let currentClients = [];

        function getClientName(clientId) {
            if (!clientId) return 'Internal / Self';
            const client = currentClients.find(c => c.id == clientId);
            return client ? `${client.company_name} (${client.client_code})` : `Client #${clientId}`;
        }

        async function loadWorkspaceData() {
            try {
                // Pre-load clients
                const clientRes = await apiGet('/clients');
                if (clientRes.success && clientRes.data && clientRes.data.data) {
                    currentClients = clientRes.data.data;
                }

                // Pre-load meeting rooms first to map names
                const roomRes = await apiGet('/workspaces/meeting-rooms');
                if (roomRes.success) {
                    currentMeetingRooms = roomRes.data.data;
                }

                const buildRes = await apiGet('/workspaces/buildings');
                if (buildRes.success) {
                    currentBuildings = buildRes.data.data;
                    const tbody = document.getElementById('buildings-table-body');
                    tbody.innerHTML = '';
                    currentBuildings.forEach(b => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${b.id}</td>
                            <td>${b.building_name}</td>
                            <td>${b.city}, ${b.state}</td>
                            <td>${b.amenities || 'N/A'}</td>
                            <td>
                                <select class="status-select-inline" onchange="confirmBuildingStatusChange(${b.id}, this, '${b.status}')">
                                    <option value="ACTIVE" ${b.status === 'ACTIVE' ? 'selected' : ''}>Active</option>
                                    <option value="INACTIVE" ${b.status === 'INACTIVE' ? 'selected' : ''}>Inactive</option>
                                    <option value="MAINTENANCE" ${b.status === 'MAINTENANCE' ? 'selected' : ''}>Maintenance</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openBuildingModal(${b.id})">Edit</button>
                                <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="deleteBuilding(${b.id})">Delete</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }

                const bookRes = await apiGet('/workspaces/bookings');
                if (bookRes.success) {
                    currentBookings = bookRes.data.data;
                    const tbody = document.getElementById('bookings-table-body');
                    tbody.innerHTML = '';
                    currentBookings.forEach(bk => {
                        const room = currentMeetingRooms.find(r => r.id == bk.room_id);
                        const roomName = room ? room.room_name : `Room #${bk.room_id}`;
                        const clientName = getClientName(bk.client_id);
                        const purposeText = bk.purpose ? `${bk.purpose} (${bk.attendees || 2} pax)` : `${bk.attendees || 2} pax`;
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${roomName}</td>
                            <td>${clientName}</td>
                            <td>${bk.booking_date}</td>
                            <td>${bk.start_time} - ${bk.end_time}</td>
                            <td>${purposeText}</td>
                            <td>
                                <select class="status-select-inline" onchange="confirmBookingStatusChange(${bk.id}, this, '${bk.status}')">
                                    <option value="CONFIRMED" ${bk.status === 'CONFIRMED' ? 'selected' : ''}>Confirmed</option>
                                    <option value="PENDING" ${bk.status === 'PENDING' ? 'selected' : ''}>Pending</option>
                                    <option value="CANCELLED" ${bk.status === 'CANCELLED' ? 'selected' : ''}>Cancelled</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openBookingModal(${bk.id})">Edit</button>
                                <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="deleteBooking(${bk.id})">Cancel</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (err) {
                showToast('Failed to load workspace inventory', 'error');
            }
        }

        async function confirmBuildingStatusChange(id, selectElement, oldStatus) {
            const newStatus = selectElement.value;
            if (newStatus === oldStatus) return;
            if (!confirm(`Are you sure you want to change the building status from "${oldStatus}" to "${newStatus}"?`)) {
                selectElement.value = oldStatus;
                return;
            }
            try {
                const res = await apiPut(`/workspaces/buildings/${id}`, { status: newStatus });
                if (res.success) {
                    showToast('Building status updated successfully!');
                    loadWorkspaceData();
                } else {
                    showToast(res.message, 'error');
                    selectElement.value = oldStatus;
                }
            } catch (err) {
                showToast('Failed to update building status.', 'error');
                selectElement.value = oldStatus;
            }
        }

        async function confirmBookingStatusChange(id, selectElement, oldStatus) {
            const newStatus = selectElement.value;
            if (newStatus === oldStatus) return;
            if (!confirm(`Are you sure you want to change the meeting room booking status from "${oldStatus}" to "${newStatus}"?`)) {
                selectElement.value = oldStatus;
                return;
            }
            try {
                const res = await apiPut(`/workspaces/bookings/${id}`, { status: newStatus });
                if (res.success) {
                    showToast('Booking status updated successfully!');
                    loadWorkspaceData();
                } else {
                    showToast(res.message, 'error');
                    selectElement.value = oldStatus;
                }
            } catch (err) {
                showToast('Failed to update booking status.', 'error');
                selectElement.value = oldStatus;
            }
        }

        // Building Modals handlers
        function openBuildingModal(buildingId = null) {
            document.getElementById('building-form').reset();
            if (buildingId) {
                const b = currentBuildings.find(item => item.id == buildingId);
                if (b) {
                    document.getElementById('building-modal-title').innerText = 'Edit Building';
                    document.getElementById('building-id').value = b.id;
                    document.getElementById('build-name').value = b.building_name;
                    document.getElementById('build-address').value = b.address || '';
                    document.getElementById('build-city').value = b.city || 'Bangalore';
                    document.getElementById('build-state').value = b.state || 'Karnataka';
                    document.getElementById('build-floors').value = b.total_floors || 1;
                    document.getElementById('build-seats').value = b.total_seats || 0;
                    document.getElementById('build-amenities').value = b.amenities || '';
                }
            } else {
                document.getElementById('building-modal-title').innerText = 'Add New Building';
                document.getElementById('building-id').value = '';
            }
            openModal('building');
        }

        async function deleteBuilding(id) {
            if (!confirm('Are you sure you want to delete this building?')) return;
            try {
                const res = await apiDelete(`/workspaces/buildings/${id}`);
                if (res.success) {
                    showToast('Building deleted successfully!');
                    loadWorkspaceData();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to delete building.', 'error');
            }
        }

        document.getElementById('building-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('building-id').value;
            const building_name = document.getElementById('build-name').value;
            const address = document.getElementById('build-address').value;
            const city = document.getElementById('build-city').value;
            const state = document.getElementById('build-state').value;
            const total_floors = parseInt(document.getElementById('build-floors').value);
            const total_seats = parseInt(document.getElementById('build-seats').value);
            const amenities = document.getElementById('build-amenities').value;

            const payload = { building_name, address, city, state, total_floors, total_seats, amenities };

            try {
                let res;
                if (id) {
                    res = await apiPut(`/workspaces/buildings/${id}`, payload);
                } else {
                    res = await apiPost('/workspaces/buildings', payload);
                }

                if (res.success) {
                    showToast(id ? 'Building updated successfully!' : 'Building created successfully!');
                    closeModal('building');
                    loadWorkspaceData();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to save building details.', 'error');
            }
        });

        // Booking Modal handlers
        async function openBookingModal(bookingId = null) {
            try {
                document.getElementById('booking-form').reset();
                const roomSelect = document.getElementById('book-room-id');
                roomSelect.innerHTML = '<option value="">Loading rooms...</option>';
                const clientSelect = document.getElementById('book-client-id');
                clientSelect.innerHTML = '<option value="">Loading clients...</option>';

                const roomRes = await apiGet('/workspaces/meeting-rooms');
                if (roomRes.success && roomRes.data && roomRes.data.data) {
                    currentMeetingRooms = roomRes.data.data;
                    roomSelect.innerHTML = '<option value="" disabled selected>-- Select Meeting Room --</option>';
                    currentMeetingRooms.forEach(r => {
                        const option = document.createElement('option');
                        option.value = r.id;
                        option.text = `${r.room_name} (Floor ${r.floor_id || 1})`;
                        roomSelect.appendChild(option);
                    });
                } else {
                    roomSelect.innerHTML = '<option value="">No rooms found</option>';
                }

                if (currentClients.length === 0) {
                    const clientRes = await apiGet('/clients');
                    if (clientRes.success && clientRes.data && clientRes.data.data) {
                        currentClients = clientRes.data.data;
                    }
                }
                clientSelect.innerHTML = '<option value="">-- Internal / Self (No Client) --</option>';
                currentClients.forEach(c => {
                    const option = document.createElement('option');
                    option.value = c.id;
                    option.text = `${c.company_name} (${c.client_code})`;
                    clientSelect.appendChild(option);
                });

                if (bookingId) {
                    const bk = currentBookings.find(item => item.id == bookingId);
                    if (bk) {
                        document.getElementById('booking-modal-title').innerText = 'Edit Meeting Room Booking';
                        document.getElementById('book-id').value = bk.id;
                        document.getElementById('book-room-id').value = bk.room_id;
                        document.getElementById('book-client-id').value = bk.client_id || '';
                        document.getElementById('book-purpose').value = bk.purpose || '';
                        document.getElementById('book-attendees').value = bk.attendees || 2;
                        document.getElementById('book-date').value = bk.booking_date;
                        document.getElementById('book-start').value = bk.start_time;
                        document.getElementById('book-end').value = bk.end_time;
                    }
                } else {
                    document.getElementById('booking-modal-title').innerText = 'Book Meeting Room';
                    document.getElementById('book-id').value = '';
                    // Default to current date
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById('book-date').value = today;
                }
                openModal('booking');
            } catch (err) {
                showToast('Failed to retrieve booking options.', 'error');
            }
        }

        async function deleteBooking(id) {
            if (!confirm('Are you sure you want to cancel this booking?')) return;
            try {
                const res = await apiDelete(`/workspaces/bookings/${id}`);
                if (res.success) {
                    showToast('Booking cancelled successfully!');
                    loadWorkspaceData();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to cancel booking.', 'error');
            }
        }

        document.getElementById('booking-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('book-id').value;
            const room_id = parseInt(document.getElementById('book-room-id').value);
            const client_id = document.getElementById('book-client-id').value ? parseInt(document.getElementById('book-client-id').value) : null;
            const purpose = document.getElementById('book-purpose').value;
            const attendees = parseInt(document.getElementById('book-attendees').value) || 2;
            const booking_date = document.getElementById('book-date').value;
            const start_time = document.getElementById('book-start').value;
            const end_time = document.getElementById('book-end').value;

            const payload = { room_id, client_id, booking_date, start_time, end_time, purpose, attendees };

            try {
                let res;
                if (id) {
                    res = await apiPut(`/workspaces/bookings/${id}`, payload);
                } else {
                    res = await apiPost('/workspaces/bookings', payload);
                }

                if (res.success) {
                    showToast(id ? 'Booking updated successfully!' : 'Meeting Room booked successfully!');
                    closeModal('booking');
                    loadWorkspaceData();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to save booking.', 'error');
            }
        });

        // 4. Facility Module
        let currentTickets = [];

        async function loadTickets() {
            try {
                const res = await apiGet('/facility/tickets');
                if (res.success) {
                    currentTickets = res.data.data;
                    const tbody = document.getElementById('tickets-table-body');
                    tbody.innerHTML = '';
                    currentTickets.forEach(t => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${t.ticket_no}</td>
                            <td>${t.title}</td>
                            <td>${t.priority}</td>
                            <td>
                                <select class="status-select-inline" onchange="confirmTicketStatusChange(${t.id}, this, '${t.status}')">
                                    <option value="OPEN" ${t.status === 'OPEN' ? 'selected' : ''}>Open</option>
                                    <option value="IN_PROGRESS" ${t.status === 'IN_PROGRESS' ? 'selected' : ''}>In Progress</option>
                                    <option value="RESOLVED" ${t.status === 'RESOLVED' ? 'selected' : ''}>Resolved</option>
                                    <option value="CLOSED" ${t.status === 'CLOSED' ? 'selected' : ''}>Closed</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openTicketModal(${t.id})">Edit</button>
                                <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="deleteTicket(${t.id})">Delete</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (err) {
                showToast('Failed to load tickets', 'error');
            }
        }

        async function confirmTicketStatusChange(id, selectElement, oldStatus) {
            const newStatus = selectElement.value;
            if (newStatus === oldStatus) return;
            if (!confirm(`Are you sure you want to change the ticket status from "${oldStatus}" to "${newStatus}"?`)) {
                selectElement.value = oldStatus;
                return;
            }
            try {
                const res = await apiPut(`/facility/tickets/${id}`, { status: newStatus });
                if (res.success) {
                    showToast('Ticket status updated successfully!');
                    loadTickets();
                } else {
                    showToast(res.message, 'error');
                    selectElement.value = oldStatus;
                }
            } catch (err) {
                showToast('Failed to update ticket status.', 'error');
                selectElement.value = oldStatus;
            }
        }

        function openTicketModal(ticketId = null) {
            document.getElementById('ticket-form').reset();
            if (ticketId) {
                const t = currentTickets.find(item => item.id == ticketId);
                if (t) {
                    document.getElementById('ticket-modal-title').innerText = 'Edit Service Ticket';
                    document.getElementById('ticket-id').value = t.id;
                    document.getElementById('ticket-title').value = t.title || '';
                    document.getElementById('ticket-priority').value = t.priority || 'MEDIUM';
                    document.getElementById('ticket-description').value = t.description || '';
                }
            } else {
                document.getElementById('ticket-modal-title').innerText = 'Raise Service Ticket';
                document.getElementById('ticket-id').value = '';
            }
            openModal('ticket');
        }

        async function deleteTicket(id) {
            if (!confirm('Are you sure you want to delete this service ticket?')) return;
            try {
                const res = await apiDelete(`/facility/tickets/${id}`);
                if (res.success) {
                    showToast('Ticket deleted successfully!');
                    loadTickets();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to delete ticket.', 'error');
            }
        }

        // Raise ticket handler
        document.getElementById('ticket-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('ticket-id').value;
            const title = document.getElementById('ticket-title').value;
            const priority = document.getElementById('ticket-priority').value;
            const description = document.getElementById('ticket-description').value;

            try {
                let res;
                if (id) {
                    res = await apiPut(`/facility/tickets/${id}`, { title, priority, description });
                } else {
                    res = await apiPost('/facility/tickets', { title, priority, description });
                }
                if (res.success) {
                    showToast(id ? 'Ticket updated successfully!' : 'Service ticket submitted successfully!');
                    closeModal('ticket');
                    loadTickets();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to save ticket details', 'error');
            }
        });

        // 5. Sustainability Module
        let currentEnergyReadings = [];

        async function loadSustainability() {
            try {
                // Ensure buildings are loaded
                if (currentBuildings.length === 0) {
                    const buildRes = await apiGet('/workspaces/buildings');
                    if (buildRes.success) {
                        currentBuildings = buildRes.data.data;
                    }
                }

                const res = await apiGet('/sustainability/energy');
                if (res.success) {
                    currentEnergyReadings = res.data.data;
                    const tbody = document.getElementById('energy-table-body');
                    tbody.innerHTML = '';
                    currentEnergyReadings.forEach(en => {
                        const b = currentBuildings.find(item => item.id == en.building_id);
                        const buildingName = b ? b.building_name : `Building #${en.building_id}`;
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${buildingName}</td>
                            <td>${en.reading_date}</td>
                            <td>${parseFloat(en.consumption_kwh).toLocaleString()} kWh</td>
                            <td>₹${parseFloat(en.cost || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                            <td>
                                <select class="status-select-inline" onchange="confirmEnergySourceChange(${en.id}, this, '${en.source}')">
                                    <option value="GRID" ${en.source === 'GRID' ? 'selected' : ''}>Grid</option>
                                    <option value="SOLAR" ${en.source === 'SOLAR' ? 'selected' : ''}>Solar</option>
                                    <option value="WIND" ${en.source === 'WIND' ? 'selected' : ''}>Wind</option>
                                    <option value="GENERATOR" ${en.source === 'GENERATOR' ? 'selected' : ''}>Generator</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openSustainabilityModal(${en.id})">Edit</button>
                                <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="deleteEnergy(${en.id})">Delete</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (err) {
                showToast('Failed to load ESG energy metrics', 'error');
            }
        }

        async function confirmEnergySourceChange(id, selectElement, oldSource) {
            const newSource = selectElement.value;
            if (newSource === oldSource) return;
            if (!confirm(`Are you sure you want to change the energy source from "${oldSource}" to "${newSource}"?`)) {
                selectElement.value = oldSource;
                return;
            }
            try {
                const res = await apiPut(`/sustainability/energy/${id}`, { source: newSource });
                if (res.success) {
                    showToast('Energy source updated successfully!');
                    loadSustainability();
                } else {
                    showToast(res.message, 'error');
                    selectElement.value = oldSource;
                }
            } catch (err) {
                showToast('Failed to update energy source.', 'error');
                selectElement.value = oldSource;
            }
        }

        async function openSustainabilityModal(readingId = null) {
            try {
                document.getElementById('sustainability-form').reset();
                const buildSelect = document.getElementById('sus-building');
                buildSelect.innerHTML = '<option value="">Loading buildings...</option>';

                // Fetch buildings if not loaded
                if (currentBuildings.length === 0) {
                    const buildRes = await apiGet('/workspaces/buildings');
                    if (buildRes.success) {
                        currentBuildings = buildRes.data.data;
                    }
                }

                buildSelect.innerHTML = '<option value="" disabled selected>-- Select Building --</option>';
                currentBuildings.forEach(b => {
                    const option = document.createElement('option');
                    option.value = b.id;
                    option.text = b.building_name;
                    buildSelect.appendChild(option);
                });

                if (readingId) {
                    const en = currentEnergyReadings.find(item => item.id == readingId);
                    if (en) {
                        document.getElementById('sus-modal-title').innerText = 'Edit Energy Reading';
                        document.getElementById('sus-id').value = en.id;
                        document.getElementById('sus-building').value = en.building_id;
                        document.getElementById('sus-date').value = en.reading_date || '';
                        document.getElementById('sus-source').value = en.source || 'GRID';
                        document.getElementById('sus-consumption').value = en.consumption_kwh || '';
                        document.getElementById('sus-cost').value = en.cost || '';
                    }
                } else {
                    document.getElementById('sus-modal-title').innerText = 'Log Energy Reading';
                    document.getElementById('sus-id').value = '';
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById('sus-date').value = today;
                    document.getElementById('sus-source').value = 'GRID';
                    document.getElementById('sus-cost').value = '';
                }
                openModal('sustainability');
            } catch (err) {
                showToast('Failed to retrieve buildings list.', 'error');
            }
        }

        async function deleteEnergy(id) {
            if (!confirm('Are you sure you want to delete this energy reading?')) return;
            try {
                const res = await apiDelete(`/sustainability/energy/${id}`);
                if (res.success) {
                    showToast('Energy reading deleted successfully!');
                    loadSustainability();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to delete energy reading.', 'error');
            }
        }

        // Log energy handler
        document.getElementById('sustainability-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('sus-id').value;
            const building_id = parseInt(document.getElementById('sus-building').value);
            const reading_date = document.getElementById('sus-date').value;
            const source = document.getElementById('sus-source').value;
            const consumption_kwh = parseFloat(document.getElementById('sus-consumption').value);
            const cost = document.getElementById('sus-cost').value ? parseFloat(document.getElementById('sus-cost').value) : 0.00;

            const payload = { building_id, reading_date, source, consumption_kwh, cost };

            try {
                let res;
                if (id) {
                    res = await apiPut(`/sustainability/energy/${id}`, payload);
                } else {
                    res = await apiPost('/sustainability/energy', payload);
                }
                if (res.success) {
                    showToast(id ? 'Energy reading updated!' : 'Energy consumption reading logged!');
                    closeModal('sustainability');
                    loadSustainability();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to save energy reading.', 'error');
            }
        });

        // 6. Billing Module
        let currentInvoices = [];
        let currentPayments = [];

        async function loadBilling() {
            try {
                // Pre-load clients
                if (currentClients.length === 0) {
                    const clientRes = await apiGet('/clients');
                    if (clientRes.success && clientRes.data && clientRes.data.data) {
                        currentClients = clientRes.data.data;
                    }
                }

                const res = await apiGet('/billing/invoices');
                if (res.success) {
                    currentInvoices = res.data.data;
                    const tbody = document.getElementById('invoices-table-body');
                    tbody.innerHTML = '';
                    currentInvoices.forEach(inv => {
                        const tr = document.createElement('tr');
                        
                        let badgeClass = 'pending';
                        if (inv.status === 'PAID') badgeClass = 'active';

                        const clientName = getClientName(inv.client_id);

                        let actionBtn = '';
                        actionBtn += `<button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openInvoiceDetailModal(${inv.id})">View Details</button>`;
                        if (inv.status === 'PENDING') {
                            actionBtn += `<button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openPaymentModal(${inv.id}, '${inv.invoice_no}', ${inv.total_amount})">Record Payment</button>`;
                        }
                        actionBtn += `
                            <button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openInvoiceModal(${inv.id})">Edit</button>
                            <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="deleteInvoice(${inv.id})">Delete</button>
                        `;

                        tr.innerHTML = `
                            <td><strong>${inv.invoice_no}</strong></td>
                            <td>${clientName}</td>
                            <td>${inv.billing_type}</td>
                            <td>${inv.billing_month}</td>
                            <td>₹${parseFloat(inv.total_amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                            <td>${inv.due_date}</td>
                            <td>
                                <select class="status-select-inline" onchange="confirmInvoiceStatusChange(${inv.id}, this, '${inv.status}')">
                                    <option value="PENDING" ${inv.status === 'PENDING' ? 'selected' : ''}>Pending</option>
                                    <option value="PAID" ${inv.status === 'PAID' ? 'selected' : ''}>Paid</option>
                                    <option value="CANCELLED" ${inv.status === 'CANCELLED' ? 'selected' : ''}>Cancelled</option>
                                </select>
                            </td>
                            <td>${actionBtn}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                }

                // Load payments log
                const payRes = await apiGet('/billing/payments');
                if (payRes.success) {
                    currentPayments = payRes.data.data;
                    const tbody = document.getElementById('payments-table-body');
                    tbody.innerHTML = '';
                    currentPayments.forEach(pay => {
                        const targetInv = currentInvoices.find(i => i.id == pay.invoice_id);
                        const invNo = targetInv ? targetInv.invoice_no : `Invoice #${pay.invoice_id}`;
                        const clientName = getClientName(pay.client_id);
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${pay.id}</td>
                            <td><strong>${invNo}</strong></td>
                            <td>${clientName}</td>
                            <td>₹${parseFloat(pay.amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                            <td>${pay.payment_date}</td>
                            <td>${pay.payment_method}</td>
                            <td><strong>${pay.transaction_id}</strong></td>
                            <td><span class="badge active">${pay.status}</span></td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (err) {
                showToast('Failed to load invoices or payments', 'error');
            }
        }

        async function confirmInvoiceStatusChange(id, selectElement, oldStatus) {
            const newStatus = selectElement.value;
            if (newStatus === oldStatus) return;
            if (!confirm(`Are you sure you want to change the invoice status from "${oldStatus}" to "${newStatus}"?`)) {
                selectElement.value = oldStatus;
                return;
            }
            try {
                const res = await apiPut(`/billing/invoices/${id}`, { status: newStatus });
                if (res.success) {
                    showToast('Invoice status updated successfully!');
                    loadBilling();
                } else {
                    showToast(res.message, 'error');
                    selectElement.value = oldStatus;
                }
            } catch (err) {
                showToast('Failed to update invoice status.', 'error');
                selectElement.value = oldStatus;
            }
        }

        async function openInvoiceDetailModal(invoiceId) {
            try {
                // Ensure clients are loaded
                if (currentClients.length === 0) {
                    const clientRes = await apiGet('/clients');
                    if (clientRes.success) {
                        currentClients = clientRes.data.data;
                    }
                }

                // Find the invoice in state
                const inv = currentInvoices.find(i => i.id == invoiceId);
                if (!inv) {
                    showToast('Invoice not found', 'error');
                    return;
                }

                // Set metadata values
                document.getElementById('det-inv-no').innerText = inv.invoice_no;
                document.getElementById('det-billing-month').innerText = inv.billing_month || 'N/A';
                document.getElementById('det-billing-type').innerText = inv.billing_type || 'N/A';
                document.getElementById('det-due-date').innerText = inv.due_date || 'N/A';

                // Status Badge styling
                const badge = document.getElementById('det-status-badge');
                badge.innerText = inv.status;
                badge.className = 'badge ' + (inv.status === 'PAID' ? 'active' : 'pending');

                // Client Details
                const client = currentClients.find(c => c.id == inv.client_id);
                if (client) {
                    document.getElementById('det-client-name').innerText = client.company_name;
                    document.getElementById('det-client-details').innerHTML = `
                        Code: ${client.client_code}<br/>
                        Contact Person: ${client.contact_person}<br/>
                        Email: ${client.email || 'N/A'}<br/>
                        Phone: ${client.mobile || 'N/A'}
                    `;
                } else {
                    document.getElementById('det-client-name').innerText = `Client #${inv.client_id}`;
                    document.getElementById('det-client-details').innerText = 'Client information not available';
                }

                // Description
                document.getElementById('det-description').innerText = `${inv.billing_type} Services - Month: ${inv.billing_month}`;
                document.getElementById('det-base-amount').innerText = '₹' + parseFloat(inv.base_amount || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById('det-gst-pct').innerText = (inv.gst_percentage || 18) + '%';
                document.getElementById('det-gst-amount').innerText = '₹' + parseFloat(inv.gst_amount || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById('det-total-amount').innerText = '₹' + parseFloat(inv.total_amount || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

                // Notes
                if (inv.notes && inv.notes.trim() !== '') {
                    document.getElementById('det-notes-wrapper').style.display = 'block';
                    document.getElementById('det-notes').innerText = inv.notes;
                } else {
                    document.getElementById('det-notes-wrapper').style.display = 'none';
                }

                // Populate Payment History
                const payBody = document.getElementById('det-payment-history-body');
                payBody.innerHTML = '';
                
                const matchedPayments = currentPayments.filter(p => p.invoice_id == invoiceId);
                if (matchedPayments.length > 0) {
                    matchedPayments.forEach(p => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td style="background: transparent; border-bottom: 1px solid rgba(255,255,255,0.05); color: #fff;">${p.id}</td>
                            <td style="background: transparent; border-bottom: 1px solid rgba(255,255,255,0.05); color: #fff;">${p.payment_date}</td>
                            <td style="background: transparent; border-bottom: 1px solid rgba(255,255,255,0.05); color: #fff;">${p.payment_method}</td>
                            <td style="background: transparent; border-bottom: 1px solid rgba(255,255,255,0.05); color: #fff;"><strong>${p.transaction_id}</strong></td>
                            <td style="text-align: right; background: transparent; border-bottom: 1px solid rgba(255,255,255,0.05); color: #fff;">₹${parseFloat(p.amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                            <td style="background: transparent; border-bottom: 1px solid rgba(255,255,255,0.05); color: #fff;"><span class="badge active">${p.status}</span></td>
                        `;
                        payBody.appendChild(tr);
                    });
                } else {
                    payBody.innerHTML = `
                        <tr>
                            <td colspan="6" style="text-align: center; color: #94a3b8; font-style: italic; background: transparent;">No payment records found for this invoice.</td>
                        </tr>
                    `;
                }

                openModal('invoice-detail');
            } catch (err) {
                showToast('Failed to load invoice details modal', 'error');
            }
        }

        function printInvoicePDF() {
            const printContent = document.getElementById('invoice-print-container').innerHTML;
            const printWindow = window.open('', '_blank', 'width=900,height=800');
            
            printWindow.document.write(`
                <html>
                <head>
                    <title>Invoice Summary - Aurbis ERP</title>
                    <style>
                        body {
                            font-family: 'Inter', system-ui, sans-serif;
                            background: #ffffff;
                            color: #1e293b;
                            padding: 40px;
                            font-size: 14px;
                            line-height: 1.5;
                        }
                        h2, h4 { color: #0f172a; margin: 0; }
                        p { margin: 5px 0; color: #475569; }
                        strong { color: #0f172a; }
                        .badge {
                            display: inline-block;
                            padding: 4px 8px;
                            font-size: 11px;
                            font-weight: 700;
                            border-radius: 6px;
                            text-transform: uppercase;
                        }
                        .badge.active { background: #dcfce7; color: #15803d; }
                        .badge.pending { background: #fef3c7; color: #b45309; }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 15px;
                            font-size: 13px;
                        }
                        th {
                            background: #f1f5f9;
                            color: #475569;
                            font-weight: 600;
                            text-align: left;
                            padding: 10px;
                            border-bottom: 2px solid #e2e8f0;
                        }
                        td {
                            padding: 10px;
                            border-bottom: 1px solid #e2e8f0;
                            color: #334155;
                        }
                        @media print {
                            body { padding: 0; }
                            button { display: none; }
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                    <script>
                        window.onload = function() {
                            window.print();
                        }
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }

        // Open Invoice Modal and fetch clients
        async function openInvoiceModal(invoiceId = null) {
            try {
                // Clear and initialize form
                document.getElementById('invoice-form').reset();
                
                // Populate Client dropdown
                const clientSelect = document.getElementById('inv-client-id');
                clientSelect.innerHTML = '<option value="">Loading clients...</option>';
                
                const clientRes = await apiGet('/clients');
                if (clientRes.success && clientRes.data && clientRes.data.data) {
                    clientSelect.innerHTML = '<option value="" disabled selected>-- Select Client --</option>';
                    clientRes.data.data.forEach(c => {
                        const option = document.createElement('option');
                        option.value = c.id;
                        option.text = `${c.company_name} (${c.client_code})`;
                        clientSelect.appendChild(option);
                    });
                } else {
                    clientSelect.innerHTML = '<option value="">No clients found</option>';
                }

                if (invoiceId) {
                    const inv = currentInvoices.find(item => item.id == invoiceId);
                    if (inv) {
                        document.getElementById('invoice-modal-title').innerText = 'Edit Revenue Invoice';
                        document.getElementById('inv-id').value = inv.id;
                        document.getElementById('inv-client-id').value = inv.client_id;
                        document.getElementById('inv-billing-type').value = inv.billing_type || 'LEASE';
                        document.getElementById('inv-billing-month').value = inv.billing_month || '';
                        document.getElementById('inv-base-amount').value = inv.base_amount || 0;
                        document.getElementById('inv-gst-percentage').value = inv.gst_percentage || '18.00';
                        document.getElementById('inv-gst-amount').value = inv.gst_amount || '0.00';
                        document.getElementById('inv-total-amount').value = inv.total_amount || '0.00';
                        document.getElementById('inv-due-date').value = inv.due_date || '';
                        document.getElementById('inv-notes').value = inv.notes || '';
                    }
                } else {
                    document.getElementById('invoice-modal-title').innerText = 'Generate Revenue Invoice';
                    document.getElementById('inv-id').value = '';
                    
                    // Pre-fill Billing Month with current YYYY-MM
                    const today = new Date();
                    const year = today.getFullYear();
                    const month = String(today.getMonth() + 1).padStart(2, '0');
                    document.getElementById('inv-billing-month').value = `${year}-${month}`;
                    
                    // Pre-fill Due Date with +10 days
                    const dueDate = new Date();
                    dueDate.setDate(today.getDate() + 10);
                    const dueYear = dueDate.getFullYear();
                    const dueMonth = String(dueDate.getMonth() + 1).padStart(2, '0');
                    const dueDateString = String(dueDate.getDate()).padStart(2, '0');
                    document.getElementById('inv-due-date').value = `${dueYear}-${dueMonth}-${dueDateString}`;
                    
                    document.getElementById('inv-gst-percentage').value = "18.00";
                    document.getElementById('inv-gst-amount').value = "0.00";
                    document.getElementById('inv-total-amount').value = "0.00";
                }
                
                openModal('invoice');
            } catch (err) {
                showToast('Failed to fetch clients list.', 'error');
            }
        }

        // Auto calculation logic for GST & Total Amount
        function calculateInvoiceAmounts() {
            const baseAmount = parseFloat(document.getElementById('inv-base-amount').value) || 0;
            const gstPercentage = parseFloat(document.getElementById('inv-gst-percentage').value) || 0;
            
            const gstAmount = (baseAmount * gstPercentage) / 100;
            const totalAmount = baseAmount + gstAmount;
            
            document.getElementById('inv-gst-amount').value = gstAmount.toFixed(2);
            document.getElementById('inv-total-amount').value = totalAmount.toFixed(2);
        }

        document.getElementById('inv-base-amount').addEventListener('input', calculateInvoiceAmounts);
        document.getElementById('inv-gst-percentage').addEventListener('input', calculateInvoiceAmounts);

        // Submit generated invoice
        document.getElementById('invoice-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('inv-id').value;
            const client_id = parseInt(document.getElementById('inv-client-id').value);
            const billing_type = document.getElementById('inv-billing-type').value;
            const billing_month = document.getElementById('inv-billing-month').value;
            const base_amount = parseFloat(document.getElementById('inv-base-amount').value);
            const gst_percentage = parseFloat(document.getElementById('inv-gst-percentage').value);
            const due_date = document.getElementById('inv-due-date').value;
            const notes = document.getElementById('inv-notes').value;

            if (!client_id) {
                showToast('Please select a client.', 'error');
                return;
            }

            const payload = {
                client_id,
                billing_type,
                billing_month,
                base_amount,
                gst_percentage,
                due_date,
                notes
            };

            try {
                let res;
                if (id) {
                    res = await apiPut(`/billing/invoices/${id}`, payload);
                } else {
                    res = await apiPost('/billing/invoices', payload);
                }

                if (res.success) {
                    showToast(id ? 'Invoice updated successfully!' : 'Invoice generated successfully!');
                    closeModal('invoice');
                    loadBilling();
                } else {
                    showToast(res.message || 'Failed to save invoice.', 'error');
                }
            } catch (err) {
                showToast('API Connection Error.', 'error');
            }
        });

        // Open Payment Modal
        function openPaymentModal(invoiceId, invoiceNo, totalAmount) {
            document.getElementById('payment-form').reset();
            document.getElementById('pay-invoice-id').value = invoiceId;
            document.getElementById('pay-invoice-no').value = invoiceNo;
            document.getElementById('pay-invoice-amount').value = `₹${parseFloat(totalAmount).toLocaleString()}`;
            document.getElementById('pay-amount').value = totalAmount;

            // Pre-fill Payment Date with today's date
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const date = String(today.getDate()).padStart(2, '0');
            document.getElementById('pay-date').value = `${year}-${month}-${date}`;

            // Auto-generate transaction ID suggestion
            document.getElementById('pay-txn-id').value = 'TXN-MAN-' + Math.random().toString(36).substr(2, 9).toUpperCase();

            openModal('payment');
        }

        // Submit recorded payment
        document.getElementById('payment-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const invoiceId = document.getElementById('pay-invoice-id').value;
            const amount = parseFloat(document.getElementById('pay-amount').value);
            const payment_date = document.getElementById('pay-date').value;
            const payment_method = document.getElementById('pay-method').value;
            const transaction_id = document.getElementById('pay-txn-id').value;

            try {
                const res = await apiPost(`/billing/invoices/${invoiceId}/pay`, {
                    amount,
                    payment_date,
                    payment_method,
                    transaction_id
                });

                if (res.success) {
                    showToast('Payment recorded successfully!');
                    closeModal('payment');
                    loadBilling();
                    // Also refresh dashboard stats if visible
                    loadDashboardData();
                } else {
                    showToast(res.message || 'Failed to record payment.', 'error');
                }
            } catch (err) {
                showToast('API Connection Error.', 'error');
            }
        });

        // 8. Visitors Module
        let currentVisitors = [];

        async function loadVisitors() {
            try {
                const res = await apiGet('/visitors');
                if (res.success) {
                    currentVisitors = res.data.data;
                    const tbody = document.getElementById('visitors-table-body');
                    tbody.innerHTML = '';
                    currentVisitors.forEach(v => {
                        const tr = document.createElement('tr');
                        
                        let badgeClass = 'pending';
                        if (v.status === 'APPROVED' || v.status === 'CHECKED_IN') badgeClass = 'active';
                        if (v.status === 'CHECKED_OUT' || v.status === 'CANCELLED') badgeClass = 'closed';

                        tr.innerHTML = `
                            <td><strong>${v.pass_code || 'N/A'}</strong></td>
                            <td>${v.visitor_name}</td>
                            <td>${v.mobile}</td>
                            <td>${v.host_name || 'N/A'} / <span style="font-size:12px; color:var(--text-muted);">${v.company || 'Personal'}</span></td>
                            <td>
                                <select class="status-select-inline" onchange="confirmVisitorStatusChange(${v.id}, this, '${v.status}')">
                                    <option value="PENDING" ${v.status === 'PENDING' ? 'selected' : ''}>Pending</option>
                                    <option value="CHECKED_IN" ${v.status === 'CHECKED_IN' ? 'selected' : ''}>Checked In</option>
                                    <option value="CHECKED_OUT" ${v.status === 'CHECKED_OUT' ? 'selected' : ''}>Checked Out</option>
                                    <option value="CANCELLED" ${v.status === 'CANCELLED' ? 'selected' : ''}>Cancelled</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openVisitorModal(${v.id})">Edit</button>
                                <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="deleteVisitor(${v.id})">Delete</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (err) {
                showToast('Failed to load visitor logs', 'error');
            }
        }

        async function confirmVisitorStatusChange(id, selectElement, oldStatus) {
            const newStatus = selectElement.value;
            if (newStatus === oldStatus) return;
            if (!confirm(`Are you sure you want to change the visitor status from "${oldStatus}" to "${newStatus}"?`)) {
                selectElement.value = oldStatus;
                return;
            }
            try {
                const res = await apiPut(`/visitors/${id}`, { status: newStatus });
                if (res.success) {
                    showToast('Visitor status updated successfully!');
                    loadVisitors();
                } else {
                    showToast(res.message, 'error');
                    selectElement.value = oldStatus;
                }
            } catch (err) {
                showToast('Failed to update visitor status.', 'error');
                selectElement.value = oldStatus;
            }
        }

        function openVisitorModal(visitorId = null) {
            document.getElementById('visitor-form').reset();
            if (visitorId) {
                const v = currentVisitors.find(item => item.id == visitorId);
                if (v) {
                    document.getElementById('visitor-modal-title').innerText = 'Edit Visitor Details';
                    document.getElementById('vis-id').value = v.id;
                    document.getElementById('vis-name').value = v.visitor_name || '';
                    document.getElementById('vis-mobile').value = v.mobile || '';
                    document.getElementById('vis-company').value = v.company || '';
                    document.getElementById('vis-purpose').value = v.visit_purpose || '';
                    document.getElementById('vis-host').value = v.host_name || '';
                }
            } else {
                document.getElementById('visitor-modal-title').innerText = 'Pre-Register Visitor Pass';
                document.getElementById('vis-id').value = '';
            }
            openModal('visitor');
        }

        async function deleteVisitor(id) {
            if (!confirm('Are you sure you want to delete this visitor record?')) return;
            try {
                const res = await apiDelete(`/visitors/${id}`);
                if (res.success) {
                    showToast('Visitor record deleted successfully!');
                    loadVisitors();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to delete visitor record.', 'error');
            }
        }

        async function handleVisitorStatus(id, status) {
            try {
                const res = await apiPut(`/visitors/${id}`, { status });
                if (res.success) {
                    showToast(`Visitor status updated to ${status}!`);
                    loadVisitors();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to update visitor status', 'error');
            }
        }

        document.getElementById('visitor-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('vis-id').value;
            const visitor_name = document.getElementById('vis-name').value;
            const mobile = document.getElementById('vis-mobile').value;
            const company = document.getElementById('vis-company').value;
            const visit_purpose = document.getElementById('vis-purpose').value;
            const host_name = document.getElementById('vis-host').value;

            try {
                let res;
                if (id) {
                    res = await apiPut(`/visitors/${id}`, { visitor_name, mobile, company, visit_purpose, host_name });
                } else {
                    res = await apiPost('/visitors', { visitor_name, mobile, company, visit_purpose, host_name });
                }
                if (res.success) {
                    showToast(id ? 'Visitor details updated successfully!' : 'Visitor registered successfully!');
                    closeModal('visitor');
                    loadVisitors();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to save visitor details', 'error');
            }
        });

        // 9. Community Module
        let currentAnnouncements = [];
        let currentEvents = [];

        async function loadCommunityData() {
            try {
                const annRes = await apiGet('/community/announcements');
                if (annRes.success) {
                    currentAnnouncements = annRes.data.data;
                    const tbody = document.getElementById('announcements-table-body');
                    tbody.innerHTML = '';
                    currentAnnouncements.forEach(ann => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td><strong>${ann.title}</strong><br/><span style="font-size:12px; color:var(--text-muted);">${ann.description || ''}</span></td>
                            <td>${ann.target_audience}</td>
                            <td>${ann.created_at.substring(0, 10)}</td>
                            <td>
                                <select class="status-select-inline" onchange="confirmAnnouncementStatusChange(${ann.id}, this, '${ann.status}')">
                                    <option value="ACTIVE" ${ann.status === 'ACTIVE' ? 'selected' : ''}>Active</option>
                                    <option value="ARCHIVED" ${ann.status === 'ARCHIVED' ? 'selected' : ''}>Archived</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openAnnouncementModal(${ann.id})">Edit</button>
                                <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="deleteAnnouncement(${ann.id})">Delete</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }

                const evRes = await apiGet('/community/events');
                if (evRes.success) {
                    currentEvents = evRes.data.data;
                    const tbody = document.getElementById('events-table-body');
                    tbody.innerHTML = '';
                    currentEvents.forEach(ev => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td><strong>${ev.title}</strong><br/><span style="font-size:12px; color:var(--text-muted);">${ev.description || ''}</span></td>
                            <td>${ev.event_date}</td>
                            <td>${ev.location}</td>
                            <td>
                                <select class="status-select-inline" onchange="confirmEventStatusChange(${ev.id}, this, '${ev.status}')">
                                    <option value="UPCOMING" ${ev.status === 'UPCOMING' ? 'selected' : ''}>Upcoming</option>
                                    <option value="ONGOING" ${ev.status === 'ONGOING' ? 'selected' : ''}>Ongoing</option>
                                    <option value="COMPLETED" ${ev.status === 'COMPLETED' ? 'selected' : ''}>Completed</option>
                                    <option value="CANCELLED" ${ev.status === 'CANCELLED' ? 'selected' : ''}>Cancelled</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openEventModal(${ev.id})">Edit</button>
                                <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="deleteEvent(${ev.id})">Delete</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (err) {
                showToast('Failed to load community feed', 'error');
            }
        }

        async function confirmAnnouncementStatusChange(id, selectElement, oldStatus) {
            const newStatus = selectElement.value;
            if (newStatus === oldStatus) return;
            if (!confirm(`Are you sure you want to change the announcement status from "${oldStatus}" to "${newStatus}"?`)) {
                selectElement.value = oldStatus;
                return;
            }
            try {
                const res = await apiPut(`/community/announcements/${id}`, { status: newStatus });
                if (res.success) {
                    showToast('Announcement status updated successfully!');
                    loadCommunityData();
                } else {
                    showToast(res.message, 'error');
                    selectElement.value = oldStatus;
                }
            } catch (err) {
                showToast('Failed to update announcement status.', 'error');
                selectElement.value = oldStatus;
            }
        }

        async function confirmEventStatusChange(id, selectElement, oldStatus) {
            const newStatus = selectElement.value;
            if (newStatus === oldStatus) return;
            if (!confirm(`Are you sure you want to change the event status from "${oldStatus}" to "${newStatus}"?`)) {
                selectElement.value = oldStatus;
                return;
            }
            try {
                const res = await apiPut(`/community/events/${id}`, { status: newStatus });
                if (res.success) {
                    showToast('Event status updated successfully!');
                    loadCommunityData();
                } else {
                    showToast(res.message, 'error');
                    selectElement.value = oldStatus;
                }
            } catch (err) {
                showToast('Failed to update event status.', 'error');
                selectElement.value = oldStatus;
            }
        }

        function openAnnouncementModal(annId = null) {
            document.getElementById('announcement-form').reset();
            if (annId) {
                const ann = currentAnnouncements.find(item => item.id == annId);
                if (ann) {
                    document.getElementById('ann-modal-title').innerText = 'Edit Announcement';
                    document.getElementById('ann-id').value = ann.id;
                    document.getElementById('ann-title').value = ann.title || '';
                    document.getElementById('ann-description').value = ann.description || '';
                }
            } else {
                document.getElementById('ann-modal-title').innerText = 'Publish New Announcement';
                document.getElementById('ann-id').value = '';
            }
            openModal('announcement');
        }

        async function deleteAnnouncement(id) {
            if (!confirm('Are you sure you want to delete this announcement?')) return;
            try {
                const res = await apiDelete(`/community/announcements/${id}`);
                if (res.success) {
                    showToast('Announcement deleted successfully!');
                    loadCommunityData();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to delete announcement.', 'error');
            }
        }

        function openEventModal(evtId = null) {
            document.getElementById('event-form').reset();
            if (evtId) {
                const ev = currentEvents.find(item => item.id == evtId);
                if (ev) {
                    document.getElementById('evt-modal-title').innerText = 'Edit Event';
                    document.getElementById('evt-id').value = ev.id;
                    document.getElementById('evt-title').value = ev.title || '';
                    document.getElementById('evt-description').value = ev.description || '';
                    
                    let dt = ev.event_date;
                    if (dt && dt.includes(' ')) {
                        dt = dt.replace(' ', 'T').substring(0, 16);
                    }
                    document.getElementById('evt-date').value = dt || '';
                    document.getElementById('evt-location').value = ev.location || '';
                }
            } else {
                document.getElementById('evt-modal-title').innerText = 'Schedule Community Event';
                document.getElementById('evt-id').value = '';
            }
            openModal('event');
        }

        async function deleteEvent(id) {
            if (!confirm('Are you sure you want to delete this event?')) return;
            try {
                const res = await apiDelete(`/community/events/${id}`);
                if (res.success) {
                    showToast('Event deleted successfully!');
                    loadCommunityData();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to delete event.', 'error');
            }
        }

        document.getElementById('announcement-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('ann-id').value;
            const title = document.getElementById('ann-title').value;
            const description = document.getElementById('ann-description').value;

            try {
                let res;
                if (id) {
                    res = await apiPut(`/community/announcements/${id}`, { title, description });
                } else {
                    res = await apiPost('/community/announcements', { title, description });
                }
                if (res.success) {
                    showToast(id ? 'Announcement updated successfully!' : 'Announcement published successfully!');
                    closeModal('announcement');
                    loadCommunityData();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to save announcement', 'error');
            }
        });

        document.getElementById('event-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('evt-id').value;
            const title = document.getElementById('evt-title').value;
            const description = document.getElementById('evt-description').value;
            const event_date = document.getElementById('evt-date').value;
            const location = document.getElementById('evt-location').value;

            try {
                let res;
                if (id) {
                    res = await apiPut(`/community/events/${id}`, { title, description, event_date, location });
                } else {
                    res = await apiPost('/community/events', { title, description, event_date, location });
                }
                if (res.success) {
                    showToast(id ? 'Event updated successfully!' : 'Event scheduled successfully!');
                    closeModal('event');
                    loadCommunityData();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to save event', 'error');
            }
        });

        // 7. SMTP Configuration
        async function loadSmtp() {
            try {
                const res = await apiGet('/auth/smtp');
                if (res.success) {
                    const smtp = res.data;
                    document.getElementById('smtp-from-email').value = smtp.from_email || '';
                    document.getElementById('smtp-from-name').value = smtp.from_name || '';
                    document.getElementById('smtp-host').value = smtp.smtp_host || '';
                    document.getElementById('smtp-port').value = smtp.smtp_port || '';
                    document.getElementById('smtp-username').value = smtp.smtp_username || '';
                    document.getElementById('smtp-password').value = smtp.smtp_password || '';
                    document.getElementById('smtp-template').value = smtp.template || '';
                }
            } catch (err) {
                showToast('Failed to load SMTP settings', 'error');
            }
        }

        // Save SMTP configuration
        document.getElementById('smtp-settings-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const from_email = document.getElementById('smtp-from-email').value;
            const from_name = document.getElementById('smtp-from-name').value;
            const smtp_host = document.getElementById('smtp-host').value;
            const smtp_port = document.getElementById('smtp-port').value;
            const smtp_username = document.getElementById('smtp-username').value;
            const smtp_password = document.getElementById('smtp-password').value;
            const template = document.getElementById('smtp-template').value;

            try {
                const res = await apiPost('/auth/smtp', { from_email, from_name, smtp_host, smtp_port, smtp_username, smtp_password, template });
                if (res.success) {
                    showToast('SMTP configuration settings saved successfully!');
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Saving failed.', 'error');
            }
        });

        // ==========================================
        // 10. HR & Workforce Module
        // ==========================================
        let currentEmployees = [];
        let currentAttendance = [];

        async function loadHr() {
            try {
                // Ensure buildings are loaded
                if (currentBuildings.length === 0) {
                    const buildRes = await apiGet('/workspaces/buildings?limit=100');
                    if (buildRes.success) {
                        currentBuildings = buildRes.data.data;
                    }
                }

                // Load Employees
                const empRes = await apiGet('/hr/employees?limit=100');
                if (empRes.success) {
                    currentEmployees = empRes.data.data;
                    const tbody = document.getElementById('employees-table-body');
                    tbody.innerHTML = '';
                    currentEmployees.forEach(emp => {
                        const building = currentBuildings.find(b => b.id == emp.building_id);
                        const buildingName = building ? building.building_name : 'No Building Assigned';
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td><strong>${emp.employee_code}</strong></td>
                            <td>${emp.name}<br/><span style="font-size:12px; color:var(--text-muted);">${emp.designation || ''}</span></td>
                            <td>${emp.department || 'General'}</td>
                            <td>${emp.designation || 'Staff'}</td>
                            <td>${emp.mobile || 'N/A'}</td>
                            <td>${emp.shift || 'DAY'}</td>
                            <td>
                                <select class="status-select-inline" onchange="confirmEmployeeStatusChange(${emp.id}, this, '${emp.status}')">
                                    <option value="ACTIVE" ${emp.status === 'ACTIVE' ? 'selected' : ''}>Active</option>
                                    <option value="INACTIVE" ${emp.status === 'INACTIVE' ? 'selected' : ''}>Inactive</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openEmployeeModal(${emp.id})">Edit</button>
                                <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="deleteEmployee(${emp.id})">Delete</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }

                // Load Attendance
                const attRes = await apiGet('/hr/attendance?limit=100');
                if (attRes.success) {
                    currentAttendance = attRes.data.data;
                    const tbody = document.getElementById('attendance-table-body');
                    tbody.innerHTML = '';
                    currentAttendance.forEach(att => {
                        const emp = currentEmployees.find(e => e.id == att.employee_id);
                        const empName = emp ? emp.name : `Employee ID: ${att.employee_id}`;
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td><strong>${empName}</strong></td>
                            <td>${att.attendance_date}</td>
                            <td>${att.check_in || 'N/A'}</td>
                            <td>${att.check_out || 'N/A'}</td>
                            <td>
                                <select class="status-select-inline" onchange="confirmAttendanceStatusChange(${att.id}, this, '${att.status}')">
                                    <option value="PRESENT" ${att.status === 'PRESENT' ? 'selected' : ''}>Present</option>
                                    <option value="ABSENT" ${att.status === 'ABSENT' ? 'selected' : ''}>Absent</option>
                                    <option value="LATE" ${att.status === 'LATE' ? 'selected' : ''}>Late</option>
                                    <option value="LEAVE" ${att.status === 'LEAVE' ? 'selected' : ''}>Leave</option>
                                </select>
                            </td>
                            <td>${att.remarks || ''}</td>
                            <td>
                                <button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openAttendanceModal(${att.id})">Edit</button>
                                <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="deleteAttendance(${att.id})">Delete</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (err) {
                showToast('Failed to load HR and employee records', 'error');
            }
        }

        async function confirmEmployeeStatusChange(id, selectElement, oldStatus) {
            const newStatus = selectElement.value;
            if (newStatus === oldStatus) return;
            if (!confirm(`Are you sure you want to change the employee status from "${oldStatus}" to "${newStatus}"?`)) {
                selectElement.value = oldStatus;
                return;
            }
            try {
                const res = await apiPut(`/hr/employees/${id}`, { status: newStatus });
                if (res.success) {
                    showToast('Employee status updated successfully!');
                    loadHr();
                } else {
                    showToast(res.message, 'error');
                    selectElement.value = oldStatus;
                }
            } catch (err) {
                showToast('Failed to update employee status.', 'error');
                selectElement.value = oldStatus;
            }
        }

        async function confirmAttendanceStatusChange(id, selectElement, oldStatus) {
            const newStatus = selectElement.value;
            if (newStatus === oldStatus) return;
            if (!confirm(`Are you sure you want to change the attendance status from "${oldStatus}" to "${newStatus}"?`)) {
                selectElement.value = oldStatus;
                return;
            }
            try {
                const res = await apiPut(`/hr/attendance/${id}`, { status: newStatus });
                if (res.success) {
                    showToast('Attendance status updated successfully!');
                    loadHr();
                } else {
                    showToast(res.message, 'error');
                    selectElement.value = oldStatus;
                }
            } catch (err) {
                showToast('Failed to update attendance status.', 'error');
                selectElement.value = oldStatus;
            }
        }

        async function openEmployeeModal(empId = null) {
            document.getElementById('employee-form').reset();
            
            // Pop building dropdown
            const bSelect = document.getElementById('employee-building');
            bSelect.innerHTML = '<option value="">No Building Assigned</option>';
            if (currentBuildings.length === 0) {
                const buildRes = await apiGet('/workspaces/buildings?limit=100');
                if (buildRes.success) currentBuildings = buildRes.data.data;
            }
            currentBuildings.forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.id;
                opt.innerText = b.building_name;
                bSelect.appendChild(opt);
            });

            if (empId) {
                const emp = currentEmployees.find(e => e.id == empId);
                if (emp) {
                    document.getElementById('employee-modal-title').innerText = 'Edit Employee Profile';
                    document.getElementById('employee-id').value = emp.id;
                    document.getElementById('employee-code').value = emp.employee_code || '';
                    document.getElementById('employee-name').value = emp.name || '';
                    document.getElementById('employee-department').value = emp.department || 'Facilities';
                    document.getElementById('employee-designation').value = emp.designation || '';
                    document.getElementById('employee-mobile').value = emp.mobile || '';
                    document.getElementById('employee-email').value = emp.email || '';
                    document.getElementById('employee-joining').value = emp.joining_date || '';
                    document.getElementById('employee-salary').value = emp.salary || 0;
                    document.getElementById('employee-shift').value = emp.shift || 'DAY';
                    document.getElementById('employee-building').value = emp.building_id || '';
                }
            } else {
                document.getElementById('employee-modal-title').innerText = 'New Employee Profile';
                document.getElementById('employee-id').value = '';
                document.getElementById('employee-code').value = 'EMP-' + Math.floor(100 + Math.random() * 900);
            }
            openModal('employee');
        }

        document.getElementById('employee-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('employee-id').value;
            const employee_code = document.getElementById('employee-code').value;
            const name = document.getElementById('employee-name').value;
            const department = document.getElementById('employee-department').value;
            const designation = document.getElementById('employee-designation').value;
            const mobile = document.getElementById('employee-mobile').value;
            const email = document.getElementById('employee-email').value;
            const joining_date = document.getElementById('employee-joining').value;
            const salary = document.getElementById('employee-salary').value;
            const shift = document.getElementById('employee-shift').value;
            const building_id = document.getElementById('employee-building').value;

            try {
                let res;
                const payload = { employee_code, name, department, designation, mobile, email, joining_date, salary, shift, building_id };
                if (id) {
                    res = await apiPut(`/hr/employees/${id}`, payload);
                } else {
                    res = await apiPost('/hr/employees', payload);
                }
                if (res.success) {
                    showToast(id ? 'Employee profile updated!' : 'Employee registered successfully!');
                    closeModal('employee');
                    loadHr();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to save employee profile', 'error');
            }
        });

        async function deleteEmployee(id) {
            if (!confirm('Are you sure you want to delete this employee profile?')) return;
            try {
                const res = await apiDelete(`/hr/employees/${id}`);
                if (res.success) {
                    showToast('Employee profile deleted successfully');
                    loadHr();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Error deleting employee.', 'error');
            }
        }

        async function openAttendanceModal(attId = null) {
            document.getElementById('attendance-form').reset();
            
            // Pop employees dropdown
            const empSelect = document.getElementById('attendance-employee');
            empSelect.innerHTML = '<option value="">Select Employee</option>';
            if (currentEmployees.length === 0) {
                const empRes = await apiGet('/hr/employees?limit=100');
                if (empRes.success) currentEmployees = empRes.data.data;
            }
            currentEmployees.forEach(e => {
                const opt = document.createElement('option');
                opt.value = e.id;
                opt.innerText = `${e.name} (${e.employee_code})`;
                empSelect.appendChild(opt);
            });

            document.getElementById('attendance-date').value = new Date().toISOString().substring(0, 10);

            if (attId) {
                const att = currentAttendance.find(a => a.id == attId);
                if (att) {
                    document.getElementById('attendance-modal-title').innerText = 'Edit Attendance Record';
                    document.getElementById('attendance-id').value = att.id;
                    document.getElementById('attendance-employee').value = att.employee_id;
                    document.getElementById('attendance-date').value = att.attendance_date;
                    document.getElementById('attendance-checkin').value = att.check_in || '';
                    document.getElementById('attendance-checkout').value = att.check_out || '';
                    document.getElementById('attendance-status').value = att.status || 'PRESENT';
                    document.getElementById('attendance-remarks').value = att.remarks || '';
                }
            } else {
                document.getElementById('attendance-modal-title').innerText = 'Log Attendance Record';
                document.getElementById('attendance-id').value = '';
            }
            openModal('attendance');
        }

        document.getElementById('attendance-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('attendance-id').value;
            const employee_id = document.getElementById('attendance-employee').value;
            const attendance_date = document.getElementById('attendance-date').value;
            const check_in = document.getElementById('attendance-checkin').value;
            const check_out = document.getElementById('attendance-checkout').value;
            const status = document.getElementById('attendance-status').value;
            const remarks = document.getElementById('attendance-remarks').value;

            try {
                let res;
                const payload = { employee_id, attendance_date, check_in, check_out, status, remarks };
                if (id) {
                    res = await apiPut(`/hr/attendance/${id}`, payload);
                } else {
                    res = await apiPost('/hr/attendance', payload);
                }
                if (res.success) {
                    showToast('Attendance record logged successfully!');
                    closeModal('attendance');
                    loadHr();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to log attendance', 'error');
            }
        });

        async function deleteAttendance(id) {
            if (!confirm('Are you sure you want to delete this attendance record?')) return;
            try {
                const res = await apiDelete(`/hr/attendance/${id}`);
                if (res.success) {
                    showToast('Attendance record deleted.');
                    loadHr();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Error deleting attendance.', 'error');
            }
        }

        // ==========================================
        // 11. Assets & Inventory Module
        // ==========================================
        let currentAssets = [];
        let currentAllocations = [];

        async function loadAssets() {
            try {
                if (currentBuildings.length === 0) {
                    const buildRes = await apiGet('/workspaces/buildings?limit=100');
                    if (buildRes.success) currentBuildings = buildRes.data.data;
                }

                // Load Assets
                const assetRes = await apiGet('/assets?limit=100');
                if (assetRes.success) {
                    currentAssets = assetRes.data.data;
                    const tbody = document.getElementById('assets-table-body');
                    tbody.innerHTML = '';
                    currentAssets.forEach(ast => {
                        const building = currentBuildings.find(b => b.id == ast.building_id);
                        const buildingName = building ? building.building_name : 'N/A';
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td><strong>${ast.asset_code}</strong></td>
                            <td>${ast.asset_name}</td>
                            <td>${ast.category || 'General'}</td>
                            <td>${buildingName}</td>
                            <td>₹${parseFloat(ast.purchase_cost || 0).toLocaleString()}</td>
                            <td>₹${parseFloat(ast.current_value || 0).toLocaleString()}</td>
                            <td>
                                <select class="status-select-inline" onchange="confirmAssetStatusChange(${ast.id}, this, '${ast.status}')">
                                    <option value="ACTIVE" ${ast.status === 'ACTIVE' ? 'selected' : ''}>Active</option>
                                    <option value="MAINTENANCE" ${ast.status === 'MAINTENANCE' ? 'selected' : ''}>Maintenance</option>
                                    <option value="RETIRED" ${ast.status === 'RETIRED' ? 'selected' : ''}>Retired</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openAssetModal(${ast.id})">Edit</button>
                                <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="deleteAsset(${ast.id})">Delete</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }

                // Load Allocations
                const allocRes = await apiGet('/assets/allocations?limit=100');
                if (allocRes.success) {
                    currentAllocations = allocRes.data.data;
                    const tbody = document.getElementById('allocations-table-body');
                    tbody.innerHTML = '';
                    currentAllocations.forEach(alc => {
                        const asset = currentAssets.find(a => a.id == alc.asset_id);
                        const assetName = asset ? asset.asset_name : `Asset ID: ${alc.asset_id}`;
                        const client = currentClients.find(c => c.id == alc.client_id);
                        const clientName = client ? client.company_name : 'Internal (Aurbis)';
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td><strong>${assetName}</strong></td>
                            <td>${alc.allocated_to || 'N/A'}</td>
                            <td>${clientName}</td>
                            <td>${alc.allocated_date}</td>
                            <td>${alc.return_date || 'Ongoing'}</td>
                            <td>
                                <select class="status-select-inline" onchange="confirmAllocationStatusChange(${alc.id}, this, '${alc.status}')">
                                    <option value="ALLOCATED" ${alc.status === 'ALLOCATED' ? 'selected' : ''}>Allocated</option>
                                    <option value="RETURNED" ${alc.status === 'RETURNED' ? 'selected' : ''}>Returned</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openAllocationModal(${alc.id})">Edit</button>
                                <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="deleteAllocation(${alc.id})">Delete</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (err) {
                showToast('Failed to load assets data.', 'error');
            }
        }

        async function confirmAssetStatusChange(id, selectElement, oldStatus) {
            const newStatus = selectElement.value;
            if (newStatus === oldStatus) return;
            if (!confirm(`Are you sure you want to change the asset status from "${oldStatus}" to "${newStatus}"?`)) {
                selectElement.value = oldStatus;
                return;
            }
            try {
                const res = await apiPut(`/assets/${id}`, { status: newStatus });
                if (res.success) {
                    showToast('Asset status updated successfully!');
                    loadAssets();
                } else {
                    showToast(res.message, 'error');
                    selectElement.value = oldStatus;
                }
            } catch (err) {
                showToast('Failed to update asset status.', 'error');
                selectElement.value = oldStatus;
            }
        }

        async function confirmAllocationStatusChange(id, selectElement, oldStatus) {
            const newStatus = selectElement.value;
            if (newStatus === oldStatus) return;
            if (!confirm(`Are you sure you want to change the asset allocation status from "${oldStatus}" to "${newStatus}"?`)) {
                selectElement.value = oldStatus;
                return;
            }
            try {
                const res = await apiPut(`/assets/allocations/${id}`, { status: newStatus });
                if (res.success) {
                    showToast('Asset allocation status updated!');
                    loadAssets();
                } else {
                    showToast(res.message, 'error');
                    selectElement.value = oldStatus;
                }
            } catch (err) {
                showToast('Failed to update allocation status.', 'error');
                selectElement.value = oldStatus;
            }
        }

        async function openAssetModal(astId = null) {
            document.getElementById('asset-form').reset();
            
            // Pop building dropdown
            const bSelect = document.getElementById('asset-building');
            bSelect.innerHTML = '<option value="">Select Building</option>';
            currentBuildings.forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.id;
                opt.innerText = b.building_name;
                bSelect.appendChild(opt);
            });

            // Bind dynamic floor loading
            const fSelect = document.getElementById('asset-floor');
            fSelect.innerHTML = '<option value="">Select Floor</option>';

            if (astId) {
                const ast = currentAssets.find(a => a.id == astId);
                if (ast) {
                    document.getElementById('asset-modal-title').innerText = 'Edit Enterprise Asset';
                    document.getElementById('asset-id').value = ast.id;
                    document.getElementById('asset-code').value = ast.asset_code || '';
                    document.getElementById('asset-name').value = ast.asset_name || '';
                    document.getElementById('asset-category').value = ast.category || 'General';
                    document.getElementById('asset-building').value = ast.building_id || '';
                    document.getElementById('asset-purchase-date').value = ast.purchase_date || '';
                    document.getElementById('asset-purchase-cost').value = ast.purchase_cost || 0;
                    document.getElementById('asset-current-value').value = ast.current_value || 0;
                    document.getElementById('asset-warranty').value = ast.warranty_expiry || '';

                    // Load matching floors
                    if (ast.building_id) {
                        const res = await apiGet(`/workspaces/floors?limit=100`);
                        if (res.success) {
                            const filtered = res.data.data.filter(f => f.building_id == ast.building_id);
                            filtered.forEach(f => {
                                const opt = document.createElement('option');
                                opt.value = f.id;
                                opt.innerText = f.floor_name;
                                if (f.id == ast.floor_id) opt.selected = true;
                                fSelect.appendChild(opt);
                            });
                        }
                    }
                }
            } else {
                document.getElementById('asset-modal-title').innerText = 'Register Enterprise Asset';
                document.getElementById('asset-id').value = '';
                document.getElementById('asset-code').value = 'AST-' + Math.floor(1000 + Math.random() * 9000);
            }

            // Bind triggers
            document.getElementById('asset-building').addEventListener('change', async () => {
                const bid = document.getElementById('asset-building').value;
                fSelect.innerHTML = '<option value="">Select Floor</option>';
                if (!bid) return;
                const res = await apiGet(`/workspaces/floors?limit=100`);
                if (res.success) {
                    res.data.data.filter(f => f.building_id == bid).forEach(f => {
                        const opt = document.createElement('option');
                        opt.value = f.id;
                        opt.innerText = f.floor_name;
                        fSelect.appendChild(opt);
                    });
                }
            });

            openModal('asset');
        }

        document.getElementById('asset-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('asset-id').value;
            const asset_code = document.getElementById('asset-code').value;
            const asset_name = document.getElementById('asset-name').value;
            const category = document.getElementById('asset-category').value;
            const building_id = document.getElementById('asset-building').value;
            const floor_id = document.getElementById('asset-floor').value;
            const purchase_date = document.getElementById('asset-purchase-date').value;
            const purchase_cost = document.getElementById('asset-purchase-cost').value;
            const current_value = document.getElementById('asset-current-value').value;
            const warranty_expiry = document.getElementById('asset-warranty').value;

            try {
                let res;
                const payload = { asset_code, asset_name, category, building_id, floor_id, purchase_date, purchase_cost, current_value, warranty_expiry };
                if (id) {
                    res = await apiPut(`/assets/${id}`, payload);
                } else {
                    res = await apiPost('/assets', payload);
                }
                if (res.success) {
                    showToast(id ? 'Asset details updated!' : 'Asset registered successfully!');
                    closeModal('asset');
                    loadAssets();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to save asset', 'error');
            }
        });

        async function deleteAsset(id) {
            if (!confirm('Are you sure you want to delete this asset?')) return;
            try {
                const res = await apiDelete(`/assets/${id}`);
                if (res.success) {
                    showToast('Asset record deleted');
                    loadAssets();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Error deleting asset.', 'error');
            }
        }

        async function openAllocationModal(alcId = null) {
            document.getElementById('allocation-form').reset();
            
            // Pop Asset, Client, Building selects
            const aSelect = document.getElementById('allocation-asset');
            aSelect.innerHTML = '<option value="">Select Asset</option>';
            currentAssets.forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.id;
                opt.innerText = `${a.asset_name} (${a.asset_code})`;
                aSelect.appendChild(opt);
            });

            const cSelect = document.getElementById('allocation-client');
            cSelect.innerHTML = '<option value="">No Client (Internal Allocation)</option>';
            currentClients.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.innerText = c.company_name;
                cSelect.appendChild(opt);
            });

            const bSelect = document.getElementById('allocation-building');
            bSelect.innerHTML = '<option value="">Select Building</option>';
            currentBuildings.forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.id;
                opt.innerText = b.building_name;
                bSelect.appendChild(opt);
            });

            const fSelect = document.getElementById('allocation-floor');
            fSelect.innerHTML = '<option value="">Select Floor</option>';

            document.getElementById('allocation-date').value = new Date().toISOString().substring(0, 10);

            if (alcId) {
                const alc = currentAllocations.find(a => a.id == alcId);
                if (alc) {
                    document.getElementById('allocation-modal-title').innerText = 'Edit Asset Allocation';
                    document.getElementById('allocation-id').value = alc.id;
                    document.getElementById('allocation-asset').value = alc.asset_id;
                    document.getElementById('allocation-to').value = alc.allocated_to || '';
                    document.getElementById('allocation-client').value = alc.client_id || '';
                    document.getElementById('allocation-building').value = alc.building_id || '';
                    document.getElementById('allocation-date').value = alc.allocated_date || '';
                    document.getElementById('allocation-return').value = alc.return_date || '';

                    if (alc.building_id) {
                        const res = await apiGet(`/workspaces/floors?limit=100`);
                        if (res.success) {
                            res.data.data.filter(f => f.building_id == alc.building_id).forEach(f => {
                                const opt = document.createElement('option');
                                opt.value = f.id;
                                opt.innerText = f.floor_name;
                                if (f.id == alc.floor_id) opt.selected = true;
                                fSelect.appendChild(opt);
                            });
                        }
                    }
                }
            } else {
                document.getElementById('allocation-modal-title').innerText = 'Log Asset Allocation';
                document.getElementById('allocation-id').value = '';
            }

            document.getElementById('allocation-building').addEventListener('change', async () => {
                const bid = document.getElementById('allocation-building').value;
                fSelect.innerHTML = '<option value="">Select Floor</option>';
                if (!bid) return;
                const res = await apiGet(`/workspaces/floors?limit=100`);
                if (res.success) {
                    res.data.data.filter(f => f.building_id == bid).forEach(f => {
                        const opt = document.createElement('option');
                        opt.value = f.id;
                        opt.innerText = f.floor_name;
                        fSelect.appendChild(opt);
                    });
                }
            });

            openModal('allocation');
        }

        document.getElementById('allocation-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('allocation-id').value;
            const asset_id = document.getElementById('allocation-asset').value;
            const allocated_to = document.getElementById('allocation-to').value;
            const client_id = document.getElementById('allocation-client').value;
            const building_id = document.getElementById('allocation-building').value;
            const floor_id = document.getElementById('allocation-floor').value;
            const allocated_date = document.getElementById('allocation-date').value;
            const return_date = document.getElementById('allocation-return').value;

            try {
                let res;
                const payload = { asset_id, allocated_to, client_id, building_id, floor_id, allocated_date, return_date };
                if (id) {
                    res = await apiPut(`/assets/allocations/${id}`, payload);
                } else {
                    res = await apiPost('/assets/allocations', payload);
                }
                if (res.success) {
                    showToast('Asset allocation details logged!');
                    closeModal('allocation');
                    loadAssets();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to log allocation', 'error');
            }
        });

        async function deleteAllocation(id) {
            if (!confirm('Are you sure you want to delete this allocation record?')) return;
            try {
                const res = await apiDelete(`/assets/allocations/${id}`);
                if (res.success) {
                    showToast('Allocation record deleted');
                    loadAssets();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Error deleting allocation.', 'error');
            }
        }

        // ==========================================
        // 12. Vendor Management Module
        // ==========================================
        let currentVendors = [];
        let currentVendorPayments = [];

        async function loadVendors() {
            try {
                const vendorRes = await apiGet('/vendors?limit=100');
                if (vendorRes.success) {
                    currentVendors = vendorRes.data.data;
                    const tbody = document.getElementById('vendors-table-body');
                    tbody.innerHTML = '';
                    currentVendors.forEach(vend => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td><strong>${vend.vendor_name}</strong></td>
                            <td>${vend.company_name || 'N/A'}</td>
                            <td>${vend.service_type || 'General'}</td>
                            <td>${vend.contact_person || 'N/A'}<br/><span style="font-size:12px; color:var(--text-muted);">${vend.mobile || ''}</span></td>
                            <td>${vend.mobile || 'N/A'}</td>
                            <td>⭐ ${vend.rating || '5.0'}</td>
                            <td>
                                <select class="status-select-inline" onchange="confirmVendorStatusChange(${vend.id}, this, '${vend.status}')">
                                    <option value="ACTIVE" ${vend.status === 'ACTIVE' ? 'selected' : ''}>Active</option>
                                    <option value="INACTIVE" ${vend.status === 'INACTIVE' ? 'selected' : ''}>Inactive</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openVendorModal(${vend.id})">Edit</button>
                                <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="deleteVendor(${vend.id})">Delete</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }

                const payRes = await apiGet('/vendors/payments?limit=100');
                if (payRes.success) {
                    currentVendorPayments = payRes.data.data;
                    const tbody = document.getElementById('vendor-payments-table-body');
                    tbody.innerHTML = '';
                    currentVendorPayments.forEach(pay => {
                        const vendor = currentVendors.find(v => v.id == pay.vendor_id);
                        const vendorName = vendor ? vendor.vendor_name : `Vendor ID: ${pay.vendor_id}`;
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td><strong>${vendorName}</strong></td>
                            <td>₹${parseFloat(pay.amount).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                            <td>${pay.payment_date}</td>
                            <td>${pay.payment_method}</td>
                            <td>${pay.reference_no || 'N/A'}</td>
                            <td><span class="badge active">${pay.status || 'PAID'}</span></td>
                            <td>
                                <button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openVendorPaymentModal(${pay.id})">Edit</button>
                                <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="deleteVendorPayment(${pay.id})">Delete</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (err) {
                showToast('Failed to load vendors data.', 'error');
            }
        }

        async function confirmVendorStatusChange(id, selectElement, oldStatus) {
            const newStatus = selectElement.value;
            if (newStatus === oldStatus) return;
            if (!confirm(`Are you sure you want to change the vendor status from "${oldStatus}" to "${newStatus}"?`)) {
                selectElement.value = oldStatus;
                return;
            }
            try {
                const res = await apiPut(`/vendors/${id}`, { status: newStatus });
                if (res.success) {
                    showToast('Vendor status updated successfully!');
                    loadVendors();
                } else {
                    showToast(res.message, 'error');
                    selectElement.value = oldStatus;
                }
            } catch (err) {
                showToast('Failed to update vendor status.', 'error');
                selectElement.value = oldStatus;
            }
        }

        function openVendorModal(vendId = null) {
            document.getElementById('vendor-form').reset();
            if (vendId) {
                const v = currentVendors.find(item => item.id == vendId);
                if (v) {
                    document.getElementById('vendor-modal-title').innerText = 'Edit Service Vendor';
                    document.getElementById('vendor-id').value = v.id;
                    document.getElementById('vendor-name').value = v.vendor_name || '';
                    document.getElementById('vendor-company').value = v.company_name || '';
                    document.getElementById('vendor-service').value = v.service_type || 'Housekeeping';
                    document.getElementById('vendor-mobile').value = v.mobile || '';
                    document.getElementById('vendor-email').value = v.email || '';
                    document.getElementById('vendor-gst').value = v.gst_number || '';
                    document.getElementById('vendor-address').value = v.address || '';
                    document.getElementById('vendor-start').value = v.contract_start || '';
                    document.getElementById('vendor-end').value = v.contract_end || '';
                    document.getElementById('vendor-sla').value = v.sla_terms || '';
                    document.getElementById('vendor-rating').value = v.rating || 5.0;
                }
            } else {
                document.getElementById('vendor-modal-title').innerText = 'Register Service Vendor';
                document.getElementById('vendor-id').value = '';
            }
            openModal('vendor');
        }

        document.getElementById('vendor-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('vendor-id').value;
            const vendor_name = document.getElementById('vendor-name').value;
            const company_name = document.getElementById('vendor-company').value;
            const service_type = document.getElementById('vendor-service').value;
            const mobile = document.getElementById('vendor-mobile').value;
            const email = document.getElementById('vendor-email').value;
            const gst_number = document.getElementById('vendor-gst').value;
            const address = document.getElementById('vendor-address').value;
            const contract_start = document.getElementById('vendor-start').value;
            const contract_end = document.getElementById('vendor-end').value;
            const sla_terms = document.getElementById('vendor-sla').value;
            const rating = document.getElementById('vendor-rating').value;

            try {
                let res;
                const payload = { vendor_name, company_name, service_type, mobile, email, gst_number, address, contract_start, contract_end, sla_terms, rating };
                if (id) {
                    res = await apiPut(`/vendors/${id}`, payload);
                } else {
                    res = await apiPost('/vendors', payload);
                }
                if (res.success) {
                    showToast(id ? 'Vendor details updated!' : 'Vendor registered successfully!');
                    closeModal('vendor');
                    loadVendors();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to save vendor details', 'error');
            }
        });

        async function deleteVendor(id) {
            if (!confirm('Are you sure you want to delete this vendor registry?')) return;
            try {
                const res = await apiDelete(`/vendors/${id}`);
                if (res.success) {
                    showToast('Vendor record deleted successfully');
                    loadVendors();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Error deleting vendor.', 'error');
            }
        }

        async function openVendorPaymentModal(payId = null) {
            document.getElementById('vendor-payment-form').reset();
            
            // Pop vendors list
            const vSelect = document.getElementById('vendor-payment-vendor');
            vSelect.innerHTML = '<option value="">Select Vendor</option>';
            if (currentVendors.length === 0) {
                const vendorRes = await apiGet('/vendors?limit=100');
                if (vendorRes.success) currentVendors = vendorRes.data.data;
            }
            currentVendors.forEach(v => {
                const opt = document.createElement('option');
                opt.value = v.id;
                opt.innerText = `${v.vendor_name} (${v.company_name || 'Individual'})`;
                vSelect.appendChild(opt);
            });

            document.getElementById('vendor-payment-date').value = new Date().toISOString().substring(0, 10);

            if (payId) {
                const pay = currentVendorPayments.find(p => p.id == payId);
                if (pay) {
                    document.getElementById('vendor-payment-modal-title').innerText = 'Edit Vendor Payment Log';
                    document.getElementById('vendor-payment-id').value = pay.id;
                    document.getElementById('vendor-payment-vendor').value = pay.vendor_id;
                    document.getElementById('vendor-payment-amount').value = pay.amount;
                    document.getElementById('vendor-payment-date').value = pay.payment_date;
                    document.getElementById('vendor-payment-method').value = pay.payment_method || 'UPI';
                    document.getElementById('vendor-payment-reference').value = pay.reference_no || '';
                    document.getElementById('vendor-payment-description').value = pay.description || '';
                }
            } else {
                document.getElementById('vendor-payment-modal-title').innerText = 'Log Vendor Outflow Payment';
                document.getElementById('vendor-payment-id').value = '';
            }
            openModal('vendor-payment');
        }

        document.getElementById('vendor-payment-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('vendor-payment-id').value;
            const vendor_id = document.getElementById('vendor-payment-vendor').value;
            const amount = document.getElementById('vendor-payment-amount').value;
            const payment_date = document.getElementById('vendor-payment-date').value;
            const payment_method = document.getElementById('vendor-payment-method').value;
            const reference_no = document.getElementById('vendor-payment-reference').value;
            const description = document.getElementById('vendor-payment-description').value;

            try {
                let res;
                const payload = { vendor_id, amount, payment_date, payment_method, reference_no, description };
                if (id) {
                    res = await apiPut(`/vendors/payments/${id}`, payload);
                } else {
                    res = await apiPost('/vendors/payments', payload);
                }
                if (res.success) {
                    showToast('Vendor outflow payment saved!');
                    closeModal('vendor-payment');
                    loadVendors();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to save vendor payment log', 'error');
            }
        });

        async function deleteVendorPayment(id) {
            if (!confirm('Are you sure you want to delete this payment record?')) return;
            try {
                const res = await apiDelete(`/vendors/payments/${id}`);
                if (res.success) {
                    showToast('Payment record deleted successfully');
                    loadVendors();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Error deleting payment.', 'error');
            }
        }

        // ==========================================
        // 13. Smart Buildings Module (IoT)
        // ==========================================
        let currentDevices = [];
        let currentSensors = [];
        let currentAccessLogs = [];

        async function loadSmartBuilding() {
            try {
                if (currentBuildings.length === 0) {
                    const buildRes = await apiGet('/workspaces/buildings?limit=100');
                    if (buildRes.success) currentBuildings = buildRes.data.data;
                }

                // Load IoT Devices
                const devRes = await apiGet('/smartbuilding/devices?limit=100');
                if (devRes.success) {
                    currentDevices = devRes.data.data;
                    const tbody = document.getElementById('devices-table-body');
                    tbody.innerHTML = '';
                    currentDevices.forEach(d => {
                        const building = currentBuildings.find(b => b.id == d.building_id);
                        const buildingName = building ? building.building_name : 'Global';
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td><strong>${d.device_name}</strong></td>
                            <td>${d.device_type}</td>
                            <td>${buildingName}</td>
                            <td>Floor ${d.floor_id || '0'}</td>
                            <td>${d.serial_number || 'N/A'}</td>
                            <td>${d.manufacturer || 'N/A'}</td>
                            <td>
                                <select class="status-select-inline" onchange="confirmDeviceStatusChange(${d.id}, this, '${d.status}')">
                                    <option value="ACTIVE" ${d.status === 'ACTIVE' ? 'selected' : ''}>Active</option>
                                    <option value="INACTIVE" ${d.status === 'INACTIVE' ? 'selected' : ''}>Inactive</option>
                                    <option value="OFFLINE" ${d.status === 'OFFLINE' ? 'selected' : ''}>Offline</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openDeviceModal(${d.id})">Edit</button>
                                <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="deleteDevice(${d.id})">Delete</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }

                // Load Live Sensor Readings
                const sensRes = await apiGet('/smartbuilding/sensors?limit=10');
                if (sensRes.success) {
                    currentSensors = sensRes.data.data;
                    const tbody = document.getElementById('sensors-table-body');
                    tbody.innerHTML = '';
                    currentSensors.forEach(s => {
                        const dev = currentDevices.find(d => d.id == s.device_id);
                        const devName = dev ? dev.device_name : `Device ${s.device_id}`;
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td><strong>${devName}</strong></td>
                            <td>${s.sensor_type}</td>
                            <td style="font-weight: 600; color: var(--accent-emerald);">${s.value}</td>
                            <td>${s.unit || ''}</td>
                            <td>${s.recorded_at}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                }

                // Load Gate Access Logs
                const logRes = await apiGet('/smartbuilding/access-logs?limit=15');
                if (logRes.success) {
                    currentAccessLogs = logRes.data.data;
                    const tbody = document.getElementById('access-logs-table-body');
                    tbody.innerHTML = '';
                    currentAccessLogs.forEach(l => {
                        const badgeColor = l.access_type === 'ENTRY' ? 'var(--accent-emerald)' : 'var(--accent-pink)';
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td><strong>${l.person_name}</strong></td>
                            <td>${l.person_type}</td>
                            <td>${l.access_point}</td>
                            <td><span style="font-weight:700; color:${badgeColor};">${l.access_type}</span> <span style="font-size:12px; color:var(--text-muted);">(${l.method})</span></td>
                            <td>${l.recorded_at}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (err) {
                showToast('Failed to load smart building logs.', 'error');
            }
        }

        async function confirmDeviceStatusChange(id, selectElement, oldStatus) {
            const newStatus = selectElement.value;
            if (newStatus === oldStatus) return;
            if (!confirm(`Are you sure you want to change the device status from "${oldStatus}" to "${newStatus}"?`)) {
                selectElement.value = oldStatus;
                return;
            }
            try {
                const res = await apiPut(`/smartbuilding/devices/${id}`, { status: newStatus });
                if (res.success) {
                    showToast('IoT device status saved!');
                    loadSmartBuilding();
                } else {
                    showToast(res.message, 'error');
                    selectElement.value = oldStatus;
                }
            } catch (err) {
                showToast('Failed to update device status.', 'error');
                selectElement.value = oldStatus;
            }
        }

        async function openDeviceModal(devId = null) {
            document.getElementById('device-form').reset();
            
            // Pop building dropdown
            const bSelect = document.getElementById('device-building');
            bSelect.innerHTML = '<option value="">Select Building</option>';
            currentBuildings.forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.id;
                opt.innerText = b.building_name;
                bSelect.appendChild(opt);
            });

            const fSelect = document.getElementById('device-floor');
            fSelect.innerHTML = '<option value="">Select Floor</option>';

            if (devId) {
                const d = currentDevices.find(item => item.id == devId);
                if (d) {
                    document.getElementById('device-modal-title').innerText = 'Edit IoT Sensor';
                    document.getElementById('device-id').value = d.id;
                    document.getElementById('device-name').value = d.device_name || '';
                    document.getElementById('device-type').value = d.device_type || 'TEMPERATURE_SENSOR';
                    document.getElementById('device-building').value = d.building_id || '';
                    document.getElementById('device-serial').value = d.serial_number || '';
                    document.getElementById('device-manufacturer').value = d.manufacturer || '';
                    document.getElementById('device-installed').value = d.installed_date || '';

                    if (d.building_id) {
                        const res = await apiGet(`/workspaces/floors?limit=100`);
                        if (res.success) {
                            res.data.data.filter(f => f.building_id == d.building_id).forEach(f => {
                                const opt = document.createElement('option');
                                opt.value = f.id;
                                opt.innerText = f.floor_name;
                                if (f.id == d.floor_id) opt.selected = true;
                                fSelect.appendChild(opt);
                            });
                        }
                    }
                }
            } else {
                document.getElementById('device-modal-title').innerText = 'Register IoT Sensor Device';
                document.getElementById('device-id').value = '';
            }

            document.getElementById('device-building').addEventListener('change', async () => {
                const bid = document.getElementById('device-building').value;
                fSelect.innerHTML = '<option value="">Select Floor</option>';
                if (!bid) return;
                const res = await apiGet(`/workspaces/floors?limit=100`);
                if (res.success) {
                    res.data.data.filter(f => f.building_id == bid).forEach(f => {
                        const opt = document.createElement('option');
                        opt.value = f.id;
                        opt.innerText = f.floor_name;
                        fSelect.appendChild(opt);
                    });
                }
            });

            openModal('device');
        }

        document.getElementById('device-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('device-id').value;
            const device_name = document.getElementById('device-name').value;
            const device_type = document.getElementById('device-type').value;
            const building_id = document.getElementById('device-building').value;
            const floor_id = document.getElementById('device-floor').value;
            const serial_number = document.getElementById('device-serial').value;
            const manufacturer = document.getElementById('device-manufacturer').value;
            const installed_date = document.getElementById('device-installed').value;

            try {
                let res;
                const payload = { device_name, device_type, building_id, floor_id, serial_number, manufacturer, installed_date };
                if (id) {
                    res = await apiPut(`/smartbuilding/devices/${id}`, payload);
                } else {
                    res = await apiPost('/smartbuilding/devices', payload);
                }
                if (res.success) {
                    showToast(id ? 'IoT device updated!' : 'IoT device registered!');
                    closeModal('device');
                    loadSmartBuilding();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Failed to save device information', 'error');
            }
        });

        async function deleteDevice(id) {
            if (!confirm('Are you sure you want to delete this IoT device?')) return;
            try {
                const res = await apiDelete(`/smartbuilding/devices/${id}`);
                if (res.success) {
                    showToast('IoT device deleted');
                    loadSmartBuilding();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Error deleting device.', 'error');
            }
        }

        // ==========================================
        // 14. Reports & Analytics Module
        // ==========================================
        let reportsData = {};

        async function loadReports() {
            try {
                // 1. Revenue
                const revRes = await apiGet('/reports/revenue');
                if (revRes.success) {
                    reportsData.revenue = revRes.data;
                    document.getElementById('report-total-revenue').innerText = `₹${parseFloat(revRes.data.total_revenue).toLocaleString(undefined, {minimumFractionDigits: 2})}`;
                    document.getElementById('report-payments-received').innerText = `₹${parseFloat(revRes.data.payments_completed).toLocaleString(undefined, {minimumFractionDigits: 2})}`;
                    document.getElementById('report-outstanding-balance').innerText = `₹${parseFloat(revRes.data.outstanding).toLocaleString(undefined, {minimumFractionDigits: 2})}`;
                    document.getElementById('report-active-leases').innerText = '2 Leases';
                }

                // 2. Occupancy
                const occRes = await apiGet('/reports/occupancy');
                if (occRes.success) {
                    reportsData.occupancy = occRes.data;
                    document.getElementById('report-total-capacity').innerText = occRes.data.total_capacity + ' Desks';
                    document.getElementById('report-allocated-seats').innerText = occRes.data.allocated_seats + ' Seats';
                    document.getElementById('report-occupancy-rate').innerText = occRes.data.occupancy_percentage + '%';
                }

                // 3. Support Tickets
                const tktRes = await apiGet('/reports/tickets');
                if (tktRes.success) {
                    reportsData.tickets = tktRes.data;
                    document.getElementById('report-total-tickets').innerText = tktRes.data.total_tickets + ' Tickets';
                    document.getElementById('report-resolved-tickets').innerText = tktRes.data.resolved_tickets;
                    document.getElementById('report-sla-compliance').innerText = tktRes.data.sla_compliance_percentage + '%';
                }

                // 4. Sustainability & ESG
                const esgRes = await apiGet('/reports/esg');
                if (esgRes.success) {
                    reportsData.esg = esgRes.data;
                    document.getElementById('report-carbon-reduction').innerText = esgRes.data.carbon_footprint_reduction_kg + ' kg CO2';
                    document.getElementById('report-energy-efficiency').innerText = esgRes.data.energy_efficiency_percent + '%';
                    document.getElementById('report-recycling-rate').innerText = esgRes.data.recycling_rate_percent + '%';
                }
            } catch (err) {
                showToast('Failed to load report analytical logs.', 'error');
            }
        }

        function printReport(type) {
            let title = '';
            let htmlContent = '';

            if (type === 'revenue') {
                title = 'Aurbis ERP - Corporate Revenue Report';
                htmlContent = `
                    <div style="font-family:'Outfit',sans-serif; padding:40px; color:#1e293b;">
                        <h1 style="color:#0f172a; margin-bottom:5px;">Aurbis Space Management</h1>
                        <p style="color:#64748b; margin-top:0; font-size:14px;">Monthly Financial Operations & Revenue Log Summary</p>
                        <hr style="border:none; border-top:1px solid #e2e8f0; margin:20px 0;"/>
                        
                        <h2 style="font-size:18px; color:#2563eb;">Revenue Parameters Breakdown</h2>
                        <table style="width:100%; border-collapse:collapse; margin-top:15px; font-size:14px;">
                            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:12px 0; color:#64748b;">Target Month</td><td style="text-align:right; font-weight:700;">${reportsData.revenue?.billing_month || 'Current Month'}</td></tr>
                            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:12px 0; color:#64748b;">Total Billed / Revenue</td><td style="text-align:right; font-weight:700;">₹${parseFloat(reportsData.revenue?.total_revenue || 0).toLocaleString()}</td></tr>
                            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:12px 0; color:#64748b;">Completed Client Settlements</td><td style="text-align:right; color:#16a34a; font-weight:700;">₹${parseFloat(reportsData.revenue?.payments_completed || 0).toLocaleString()}</td></tr>
                            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:12px 0; color:#64748b;">Pending Outstanding Balance</td><td style="text-align:right; color:#dc2626; font-weight:700;">₹${parseFloat(reportsData.revenue?.outstanding || 0).toLocaleString()}</td></tr>
                        </table>
                    </div>
                `;
            } else if (type === 'occupancy') {
                title = 'Aurbis ERP - Workspace Occupancy Summary';
                htmlContent = `
                    <div style="font-family:'Outfit',sans-serif; padding:40px; color:#1e293b;">
                        <h1 style="color:#0f172a; margin-bottom:5px;">Aurbis Space Management</h1>
                        <p style="color:#64748b; margin-top:0; font-size:14px;">Workspace Capacity & Allocation Log Summary</p>
                        <hr style="border:none; border-top:1px solid #e2e8f0; margin:20px 0;"/>
                        
                        <h2 style="font-size:18px; color:#2563eb;">Space Utilization Analytics</h2>
                        <table style="width:100%; border-collapse:collapse; margin-top:15px; font-size:14px;">
                            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:12px 0; color:#64748b;">Total Desks Capacity</td><td style="text-align:right; font-weight:700;">${reportsData.occupancy?.total_capacity || 0} Desks</td></tr>
                            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:12px 0; color:#64748b;">Allocated Corporate Seats</td><td style="text-align:right; font-weight:700;">${reportsData.occupancy?.allocated_seats || 0} Seats</td></tr>
                            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:12px 0; color:#64748b;">Net Capacity Utilization Rate</td><td style="text-align:right; color:#2563eb; font-weight:700;">${reportsData.occupancy?.occupancy_percentage || 0}%</td></tr>
                        </table>
                    </div>
                `;
            } else if (type === 'tickets') {
                title = 'Aurbis ERP - Support Tickets SLA Resolution Log';
                htmlContent = `
                    <div style="font-family:'Outfit',sans-serif; padding:40px; color:#1e293b;">
                        <h1 style="color:#0f172a; margin-bottom:5px;">Aurbis Space Management</h1>
                        <p style="color:#64748b; margin-top:0; font-size:14px;">Facility Support SLA Compliance Log Summary</p>
                        <hr style="border:none; border-top:1px solid #e2e8f0; margin:20px 0;"/>
                        
                        <h2 style="font-size:18px; color:#2563eb;">SLA Resolution Performance Parameters</h2>
                        <table style="width:100%; border-collapse:collapse; margin-top:15px; font-size:14px;">
                            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:12px 0; color:#64748b;">Total Tickets Raised</td><td style="text-align:right; font-weight:700;">${reportsData.tickets?.total_tickets || 0} Tickets</td></tr>
                            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:12px 0; color:#64748b;">Tickets Resolved</td><td style="text-align:right; font-weight:700;">${reportsData.tickets?.resolved_tickets || 0}</td></tr>
                            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:12px 0; color:#64748b;">SLA Deadlines Compliance</td><td style="text-align:right; color:#16a34a; font-weight:700;">${reportsData.tickets?.sla_compliance_percentage || 0}%</td></tr>
                        </table>
                    </div>
                `;
            } else if (type === 'esg') {
                title = 'Aurbis ERP - ESG & Sustainability Operations Report';
                htmlContent = `
                    <div style="font-family:'Outfit',sans-serif; padding:40px; color:#1e293b;">
                        <h1 style="color:#0f172a; margin-bottom:5px;">Aurbis Space Management</h1>
                        <p style="color:#64748b; margin-top:0; font-size:14px;">ESG Footprints & Environmental Conservation Summary</p>
                        <hr style="border:none; border-top:1px solid #e2e8f0; margin:20px 0;"/>
                        
                        <h2 style="font-size:18px; color:#2563eb;">Sustainability Metrics</h2>
                        <table style="width:100%; border-collapse:collapse; margin-top:15px; font-size:14px;">
                            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:12px 0; color:#64748b;">Estimated CO2 Emissions Avoided</td><td style="text-align:right; font-weight:700;">${reportsData.esg?.carbon_footprint_reduction_kg || 0} kg CO2</td></tr>
                            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:12px 0; color:#64748b;">Average Energy Efficiency Rate</td><td style="text-align:right; color:#7c3aed; font-weight:700;">${reportsData.esg?.energy_efficiency_percent || 0}%</td></tr>
                            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:12px 0; color:#64748b;">Waste Recycling Percentage</td><td style="text-align:right; color:#16a34a; font-weight:700;">${reportsData.esg?.recycling_rate_percent || 0}%</td></tr>
                        </table>
                    </div>
                `;
            }

            const win = window.open('', '_blank', 'width=800,height=600');
            win.document.write(`
                <html>
                <head>
                    <title>${title}</title>
                    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
                    <style>
                        body { margin:0; font-family:'Outfit',sans-serif; }
                        @media print {
                            .no-print { display:none; }
                        }
                    </style>
                </head>
                <body>
                    <div class="no-print" style="background:#f1f5f9; padding:15px; border-bottom:1px solid #cbd5e1; text-align:right;">
                        <button onclick="window.print()" style="background:#2563eb; color:#fff; border:none; padding:10px 20px; font-size:14px; font-weight:600; border-radius:6px; cursor:pointer;">Print / Save PDF</button>
                    </div>
                    \${htmlContent}
                </body>
                </html>
            `);
            win.document.close();
        }

        // Theme Toggle Function
        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('workspace_theme', isDark ? 'dark' : 'light');
            document.getElementById('theme-toggle-btn').innerText = isDark ? '☀️ Light Mode' : '🌙 Dark Mode';
        }

        // On DOM Load: Restore Theme and Auto-Login Check
        window.addEventListener('DOMContentLoaded', async () => {
            // Restore theme
            const savedTheme = localStorage.getItem('workspace_theme') || 'light';
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-mode');
                document.getElementById('theme-toggle-btn').innerText = '☀️ Light Mode';
            } else {
                document.body.classList.remove('dark-mode');
                document.getElementById('theme-toggle-btn').innerText = '🌙 Dark Mode';
            }

            // Auto login check
            const token = localStorage.getItem('workspace_jwt_token');
            if (token) {
                try {
                    const data = await apiGet('/auth/me');
                    if (data.success) {
                        // Render Profile
                        document.getElementById('user-avatar').innerText = data.data.name.charAt(0).toUpperCase();
                        document.getElementById('user-display-name').innerText = data.data.name;
                        document.getElementById('user-display-role').innerText = data.data.role;

                        // Transition Views
                        document.getElementById('auth-screen').style.display = 'none';
                        document.getElementById('app-layout').style.display = 'flex';

                        switchTab('dashboard');
                        showToast('Welcome back, ' + data.data.name + '!');
                    } else {
                        localStorage.removeItem('workspace_jwt_token');
                    }
                } catch (err) {
                    localStorage.removeItem('workspace_jwt_token');
                    console.warn('Auto-login session expired or invalid:', err);
                }
            }
        });
    </script>
</body>
</html>
