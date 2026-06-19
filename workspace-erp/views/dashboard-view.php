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
                                <th>Room ID</th>
                                <th>Client ID</th>
                                <th>Date</th>
                                <th>Time</th>
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
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
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
                                <th>Building ID</th>
                                <th>Reading Date</th>
                                <th>Consumption (kWh)</th>
                                <th>Source</th>
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
                            <td><span class="badge active">${lead.status}</span></td>
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

        async function loadWorkspaceData() {
            try {
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
                            <td><span class="badge active">${b.status}</span></td>
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
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${bk.room_id}</td>
                            <td>${bk.client_id || 'Self'}</td>
                            <td>${bk.booking_date}</td>
                            <td>${bk.start_time} - ${bk.end_time}</td>
                            <td><span class="badge active">${bk.status}</span></td>
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
        function openBookingModal(bookingId = null) {
            document.getElementById('booking-form').reset();
            if (bookingId) {
                const bk = currentBookings.find(item => item.id == bookingId);
                if (bk) {
                    document.getElementById('booking-modal-title').innerText = 'Edit Meeting Room Booking';
                    document.getElementById('book-id').value = bk.id;
                    document.getElementById('book-room-id').value = bk.room_id;
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
            const booking_date = document.getElementById('book-date').value;
            const start_time = document.getElementById('book-start').value;
            const end_time = document.getElementById('book-end').value;

            const payload = { room_id, booking_date, start_time, end_time };

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
                            <td><span class="badge active">${t.status}</span></td>
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
                const res = await apiGet('/sustainability/energy');
                if (res.success) {
                    currentEnergyReadings = res.data.data;
                    const tbody = document.getElementById('energy-table-body');
                    tbody.innerHTML = '';
                    currentEnergyReadings.forEach(en => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${en.building_id}</td>
                            <td>${en.reading_date}</td>
                            <td>${en.consumption_kwh}</td>
                            <td><span class="badge active">${en.source}</span></td>
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

        function openSustainabilityModal(readingId = null) {
            document.getElementById('sustainability-form').reset();
            if (readingId) {
                const en = currentEnergyReadings.find(item => item.id == readingId);
                if (en) {
                    document.getElementById('sus-modal-title').innerText = 'Edit Energy Reading';
                    document.getElementById('sus-id').value = en.id;
                    document.getElementById('sus-building').value = en.building_id || 1;
                    document.getElementById('sus-consumption').value = en.consumption_kwh || '';
                }
            } else {
                document.getElementById('sus-modal-title').innerText = 'Log Energy Reading';
                document.getElementById('sus-id').value = '';
            }
            openModal('sustainability');
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
            const consumption_kwh = parseFloat(document.getElementById('sus-consumption').value);

            try {
                let res;
                if (id) {
                    res = await apiPut(`/sustainability/energy/${id}`, { building_id, consumption_kwh });
                } else {
                    res = await apiPost('/sustainability/energy', { building_id, consumption_kwh });
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

        async function loadBilling() {
            try {
                const res = await apiGet('/billing/invoices');
                if (res.success) {
                    currentInvoices = res.data.data;
                    const tbody = document.getElementById('invoices-table-body');
                    tbody.innerHTML = '';
                    currentInvoices.forEach(inv => {
                        const tr = document.createElement('tr');
                        
                        let badgeClass = 'pending';
                        if (inv.status === 'PAID') badgeClass = 'active';

                        let actionBtn = '';
                        if (inv.status === 'PENDING') {
                            actionBtn += `<button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openPaymentModal(${inv.id}, '${inv.invoice_no}', ${inv.total_amount})">Record Payment</button>`;
                        }
                        actionBtn += `
                            <button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openInvoiceModal(${inv.id})">Edit</button>
                            <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="deleteInvoice(${inv.id})">Delete</button>
                        `;

                        tr.innerHTML = `
                            <td>${inv.invoice_no}</td>
                            <td>${inv.billing_type}</td>
                            <td>${inv.billing_month}</td>
                            <td>₹${parseFloat(inv.total_amount).toLocaleString()}</td>
                            <td>${inv.due_date}</td>
                            <td><span class="badge ${badgeClass}">${inv.status}</span></td>
                            <td>${actionBtn}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                }

                // Load payments log
                const payRes = await apiGet('/billing/payments');
                if (payRes.success) {
                    const tbody = document.getElementById('payments-table-body');
                    tbody.innerHTML = '';
                    payRes.data.data.forEach(pay => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${pay.id}</td>
                            <td>Invoice #${pay.invoice_id}</td>
                            <td>Client #${pay.client_id}</td>
                            <td>₹${parseFloat(pay.amount).toLocaleString()}</td>
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

                        let actionButtons = '';
                        if (v.status === 'PENDING') {
                            actionButtons += `
                                <button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="handleVisitorStatus(${v.id}, 'CHECKED_IN')">Check-In</button>
                                <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="handleVisitorStatus(${v.id}, 'CANCELLED')">Cancel</button>
                            `;
                        } else if (v.status === 'CHECKED_IN') {
                            actionButtons += `
                                <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="handleVisitorStatus(${v.id}, 'CHECKED_OUT')">Check-Out</button>
                            `;
                        }
                        
                        actionButtons += `
                            <button class="btn" style="padding: 4px 8px; font-size: 11px; margin-right: 5px;" onclick="openVisitorModal(${v.id})">Edit</button>
                            <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="deleteVisitor(${v.id})">Delete</button>
                        `;

                        tr.innerHTML = `
                            <td><strong>${v.pass_code || 'N/A'}</strong></td>
                            <td>${v.visitor_name}</td>
                            <td>${v.mobile}</td>
                            <td>${v.host_name || 'N/A'} / <span style="font-size:12px; color:var(--text-muted);">${v.company || 'Personal'}</span></td>
                            <td><span class="badge ${badgeClass}">${v.status}</span></td>
                            <td>${actionButtons}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (err) {
                showToast('Failed to load visitor logs', 'error');
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
                            <td><span class="badge active">${ev.status}</span></td>
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
