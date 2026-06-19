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
            --bg-primary: #080c14;
            --bg-secondary: rgba(13, 20, 35, 0.75);
            --bg-card: rgba(30, 41, 59, 0.45);
            --accent-blue: #2563eb;
            --accent-purple: #7c3aed;
            --accent-pink: #db2777;
            --accent-emerald: #059669;
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
                radial-gradient(circle at 15% 15%, rgba(37, 99, 235, 0.1) 0%, transparent 45%),
                radial-gradient(circle at 85% 85%, rgba(124, 58, 237, 0.1) 0%, transparent 45%);
            background-attachment: fixed;
            min-height: 100vh;
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
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            padding: 12px 16px;
            color: #fff;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
            width: 100%;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--accent-blue);
        }
        .form-select option {
            background: #0d1423;
            color: #fff;
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
            background: rgba(255, 255, 255, 0.03);
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
            color: #fff;
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
            background: linear-gradient(to right, #ffffff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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
            background: rgba(255,255,255,0.03);
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
            color: #fff;
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
            background: rgba(255, 255, 255, 0.015);
            border-radius: 12px;
            border-left: 4px solid var(--accent-purple);
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
            border-bottom: 1px solid rgba(255, 255, 255, 0.02);
            color: var(--text-main);
        }
        .data-table tr:hover td {
            background: rgba(255, 255, 255, 0.015);
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
            background: #090e18;
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            width: 100%;
            max-width: 520px;
            padding: 30px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.6);
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
            background: linear-gradient(to right, #fff, var(--text-muted));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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
                    <li><button class="menu-item" onclick="switchTab('tickets')">Facility & Support</button></li>
                    <li><button class="menu-item" onclick="switchTab('sustainability')">Sustainability (ESG)</button></li>
                    <li><button class="menu-item" onclick="switchTab('billing')">Invoices & Billing</button></li>
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
                <div class="badge-live">
                    <span class="live-dot"></span>
                    <span>Live Portal</span>
                </div>
            </header>

            <!-- Dashboard Tab -->
            <section id="tab-dashboard" class="tab-panel active">
                <div class="cards-grid">
                    <div class="stat-card">
                        <div class="card-icon">🏛️</div>
                        <span class="card-label">Total Buildings</span>
                        <div class="card-value" id="card-buildings">0</div>
                    </div>
                    <div class="stat-card">
                        <div class="card-icon">👥</div>
                        <span class="card-label">Total Tenants</span>
                        <div class="card-value" id="card-tenants">0</div>
                    </div>
                    <div class="stat-card">
                        <div class="card-icon">🪑</div>
                        <span class="card-label">Occupancy Rate</span>
                        <div class="card-value" id="card-occupancy">0%</div>
                    </div>
                    <div class="stat-card">
                        <div class="card-icon">💳</div>
                        <span class="card-label">Monthly Revenue</span>
                        <div class="card-value" id="card-revenue">₹0</div>
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
                        <button class="btn" onclick="openModal('crm')">+ New Lead Inquiry</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Company</th>
                                <th>Contact</th>
                                <th>Seats Required</th>
                                <th>Status</th>
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
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Amenities</th>
                                <th>Status</th>
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
                        <button class="btn" onclick="openModal('booking')">Schedule Booking</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Room ID</th>
                                <th>Client ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="bookings-table-body">
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
                        <button class="btn" onclick="openModal('ticket')">Raise Service Ticket</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Ticket No</th>
                                <th>Title</th>
                                <th>Priority</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="tickets-table-body">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Sustainability Tab -->
            <section id="tab-sustainability" class="tab-panel">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Energy & ESG Monitoring Readings</h3>
                        <button class="btn" onclick="openModal('sustainability')">Log Energy Reading</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Building ID</th>
                                <th>Reading Date</th>
                                <th>Consumption (kWh)</th>
                                <th>Source</th>
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
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>Billing Type</th>
                                <th>Billing Month</th>
                                <th>Total Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="invoices-table-body">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
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
            <h3 class="modal-title">New Lead Inquiry</h3>
            <form id="lead-form">
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
                <button type="submit" class="btn" style="width: 100%;">Create Lead Record</button>
            </form>
        </div>
    </div>

    <!-- 2. Meeting Room Booking Modal -->
    <div class="modal-overlay" id="modal-booking">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('booking')">&times;</button>
            <h3 class="modal-title">Book Meeting Room</h3>
            <form id="booking-form">
                <div class="form-group">
                    <label>Room ID</label>
                    <input type="number" id="book-room-id" class="form-input" value="1" required>
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
            <h3 class="modal-title">Raise Service Ticket</h3>
            <form id="ticket-form">
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
            <h3 class="modal-title">Log Energy Reading</h3>
            <form id="sustainability-form">
                <div class="form-group">
                    <label>Building ID</label>
                    <input type="number" id="sus-building" class="form-input" value="1" required>
                </div>
                <div class="form-group">
                    <label>Consumption (kWh)</label>
                    <input type="number" step="0.01" id="sus-consumption" class="form-input" required>
                </div>
                <button type="submit" class="btn" style="width: 100%;">Log Energy reading</button>
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
            } else if (tabName === 'tickets') {
                titleGroup.innerText = 'Facility Management & Tickets';
                subtitleGroup.innerText = 'Service logs & preventive scheduling';
                loadTickets();
            } else if (tabName === 'sustainability') {
                titleGroup.innerText = 'Sustainability Monitoring';
                subtitleGroup.innerText = 'Energy logs, water footprints & ESG reporting';
                loadSustainability();
            } else if (tabName === 'billing') {
                titleGroup.innerText = 'Revenue Invoices';
                subtitleGroup.innerText = 'Client leases, payments & utility invoice schedules';
                loadBilling();
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
                    document.getElementById('card-revenue').innerText = `₹${parseFloat(cards.monthly_revenue).toLocaleString()}`;

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

        // 2. CRM Leads Module
        async function loadLeads() {
            try {
                const res = await apiGet('/crm/leads');
                if (res.success) {
                    const tbody = document.getElementById('crm-leads-table-body');
                    tbody.innerHTML = '';
                    res.data.data.forEach(lead => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${lead.lead_code}</td>
                            <td>${lead.company_name}</td>
                            <td>${lead.contact_person}</td>
                            <td>${lead.seats_required}</td>
                            <td><span class="badge active">${lead.status}</span></td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (err) {
                showToast('Failed to load leads', 'error');
            }
        }

        // Create Lead submission handler
        document.getElementById('lead-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const company_name = document.getElementById('lead-company').value;
            const contact_person = document.getElementById('lead-contact').value;
            const email = document.getElementById('lead-email').value;
            const seats_required = parseInt(document.getElementById('lead-seats').value);

            try {
                const res = await apiPost('/crm/leads', { company_name, contact_person, email, seats_required });
                if (res.success) {
                    showToast('Lead inquiry added successfully!');
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
        async function loadWorkspaceData() {
            try {
                const buildRes = await apiGet('/workspaces/buildings');
                if (buildRes.success) {
                    const tbody = document.getElementById('buildings-table-body');
                    tbody.innerHTML = '';
                    buildRes.data.data.forEach(b => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${b.id}</td>
                            <td>${b.building_name}</td>
                            <td>${b.city}, ${b.state}</td>
                            <td>${b.amenities || 'N/A'}</td>
                            <td><span class="badge active">${b.status}</span></td>
                        `;
                        tbody.appendChild(tr);
                    });
                }

                const bookRes = await apiGet('/workspaces/bookings');
                if (bookRes.success) {
                    const tbody = document.getElementById('bookings-table-body');
                    tbody.innerHTML = '';
                    bookRes.data.data.forEach(bk => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${bk.room_id}</td>
                            <td>${bk.client_id || 'Self'}</td>
                            <td>${bk.booking_date}</td>
                            <td>${bk.start_time} - ${bk.end_time}</td>
                            <td><span class="badge active">${bk.status}</span></td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (err) {
                showToast('Failed to load workspace inventory', 'error');
            }
        }

        // Room booking handler
        document.getElementById('booking-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const room_id = parseInt(document.getElementById('book-room-id').value);
            const booking_date = document.getElementById('book-date').value;
            const start_time = document.getElementById('book-start').value;
            const end_time = document.getElementById('book-end').value;

            try {
                const res = await apiPost('/workspaces/bookings', { room_id, booking_date, start_time, end_time });
                if (res.success) {
                    showToast('Meeting Room booked successfully!');
                    closeModal('booking');
                    loadWorkspaceData();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Booking failed.', 'error');
            }
        });

        // 4. Facility Module
        async function loadTickets() {
            try {
                const res = await apiGet('/facility/tickets');
                if (res.success) {
                    const tbody = document.getElementById('tickets-table-body');
                    tbody.innerHTML = '';
                    res.data.data.forEach(t => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${t.ticket_no}</td>
                            <td>${t.title}</td>
                            <td>${t.priority}</td>
                            <td><span class="badge active">${t.status}</span></td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (err) {
                showToast('Failed to load tickets', 'error');
            }
        }

        // Raise ticket handler
        document.getElementById('ticket-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const title = document.getElementById('ticket-title').value;
            const priority = document.getElementById('ticket-priority').value;
            const description = document.getElementById('ticket-description').value;

            try {
                const res = await apiPost('/facility/tickets', { title, priority, description });
                if (res.success) {
                    showToast('Service ticket submitted successfully!');
                    closeModal('ticket');
                    loadTickets();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Submission failed.', 'error');
            }
        });

        // 5. Sustainability Module
        async function loadSustainability() {
            try {
                const res = await apiGet('/sustainability/energy');
                if (res.success) {
                    const tbody = document.getElementById('energy-table-body');
                    tbody.innerHTML = '';
                    res.data.data.forEach(en => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${en.building_id}</td>
                            <td>${en.reading_date}</td>
                            <td>${en.consumption_kwh}</td>
                            <td><span class="badge active">${en.source}</span></td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (err) {
                showToast('Failed to load ESG energy metrics', 'error');
            }
        }

        // Log energy handler
        document.getElementById('sustainability-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const building_id = parseInt(document.getElementById('sus-building').value);
            const consumption_kwh = parseFloat(document.getElementById('sus-consumption').value);

            try {
                const res = await apiPost('/sustainability/energy', { building_id, consumption_kwh });
                if (res.success) {
                    showToast('Energy consumption reading logged!');
                    closeModal('sustainability');
                    loadSustainability();
                } else {
                    showToast(res.message, 'error');
                }
            } catch (err) {
                showToast('Logging failed.', 'error');
            }
        });

        // 6. Billing Module
        async function loadBilling() {
            try {
                const res = await apiGet('/billing/invoices');
                if (res.success) {
                    const tbody = document.getElementById('invoices-table-body');
                    tbody.innerHTML = '';
                    res.data.data.forEach(inv => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${inv.invoice_no}</td>
                            <td>${inv.billing_type}</td>
                            <td>${inv.billing_month}</td>
                            <td>₹${parseFloat(inv.total_amount).toLocaleString()}</td>
                            <td>${inv.due_date}</td>
                            <td><span class="badge pending">${inv.status}</span></td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (err) {
                showToast('Failed to load invoices', 'error');
            }
        }

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
    </script>
</body>
</html>
