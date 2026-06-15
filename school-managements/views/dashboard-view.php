<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management Dashboard - Portal</title>
    <!-- Modern Premium Typography -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0b0f19;
            --bg-secondary: rgba(17, 24, 39, 0.7);
            --bg-card: rgba(31, 41, 55, 0.5);
            --accent-blue: #3b82f6;
            --accent-purple: #8b5cf6;
            --accent-pink: #ec4899;
            --accent-emerald: #10b981;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --glass-border: rgba(255, 255, 255, 0.08);
            --glass-shadow: rgba(0, 0, 0, 0.4);
            --border-hover: rgba(255, 255, 255, 0.15);
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
                radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(139, 92, 246, 0.08) 0%, transparent 40%);
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
            background: rgba(17, 24, 39, 0.9);
            border: 1px solid var(--accent-blue);
            color: #fff;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            backdrop-filter: blur(10px);
            font-size: 14px;
            min-width: 280px;
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
            backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            width: 100%;
            max-width: 440px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.6);
        }
        .auth-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .auth-logo h2 {
            font-weight: 700;
            font-size: 24px;
            background: linear-gradient(135deg, #60a5fa, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .auth-logo p {
            color: var(--text-muted);
            font-size: 13px;
            margin-top: 4px;
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
        .form-input {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            padding: 12px 16px;
            color: #fff;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-input:focus {
            border-color: var(--accent-blue);
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
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        .auth-submit-btn:hover {
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.45);
            transform: translateY(-1px);
        }
        .auth-toggle-tip {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: var(--text-muted);
        }
        .auth-toggle-tip a {
            color: var(--accent-blue);
            text-decoration: none;
            font-weight: 500;
        }
        .auth-toggle-tip a:hover {
            text-decoration: underline;
        }
        .demo-credentials {
            background: rgba(255,255,255,0.02);
            border: 1px dashed var(--glass-border);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 12px;
        }
        .demo-credentials h5 {
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--accent-blue);
        }
        .demo-btn {
            background: rgba(59, 130, 246, 0.1);
            color: var(--accent-blue);
            border: 1px solid rgba(59, 130, 246, 0.2);
            padding: 4px 8px;
            border-radius: 6px;
            cursor: pointer;
            margin-left: 8px;
            font-size: 10px;
            font-weight: 600;
        }

        /* App Layout */
        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Glassmorphism */
        .sidebar {
            width: 260px;
            background: rgba(10, 15, 30, 0.8);
            backdrop-filter: blur(16px);
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
            font-size: 20px;
            background: linear-gradient(135deg, #60a5fa, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 40px;
        }

        .brand-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
        }

        .menu-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
            outline: none;
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
            border-left: 3px solid var(--accent-blue);
        }

        .user-profile {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            border: 1px solid var(--glass-border);
        }
        .user-profile-inner {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-pink));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
        }

        .user-info h4 {
            font-size: 14px;
            font-weight: 600;
        }

        .user-info p {
            font-size: 11px;
            color: var(--text-muted);
        }

        .logout-icon-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 18px;
            outline: none;
        }
        .logout-icon-btn:hover {
            color: var(--accent-pink);
        }

        /* Main Content Panel */
        .main-panel {
            flex-grow: 1;
            padding: 40px;
            margin-left: 260px; /* offset sidebar fixed */
            min-height: 100vh;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }

        .title-group h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 4px;
            background: linear-gradient(to right, #ffffff, #d1d5db);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .title-group p {
            color: var(--text-muted);
            font-size: 14px;
        }

        .badge-live {
            background: rgba(16, 185, 129, 0.1);
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
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
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
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: var(--bg-secondary);
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
            box-shadow: 0 12px 40px rgba(0,0,0,0.45);
            border-color: var(--border-hover);
        }

        .card-icon {
            width: 48px;
            height: 48px;
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
            font-size: 26px;
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
            position: relative;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            padding-top: 20px;
        }

        /* Simulated SVG Charts */
        .svg-chart {
            width: 100%;
            height: 100%;
        }

        /* Notice Board List */
        .notice-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .notice-item {
            padding: 16px;
            background: rgba(255, 255, 255, 0.02);
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
        }

        .notice-date {
            font-size: 11px;
            color: var(--accent-purple);
            margin-top: 6px;
        }

        /* Table Components styling */
        .table-container {
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 8px 32px var(--glass-shadow);
            margin-bottom: 30px;
        }
        .table-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .table-header-row h3 {
            font-size: 20px;
            font-weight: 600;
        }
        .table-controls {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .data-table th {
            text-align: left;
            padding: 14px 16px;
            border-bottom: 1px solid var(--glass-border);
            color: var(--text-muted);
            font-weight: 500;
        }
        .data-table td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            color: var(--text-main);
        }
        .data-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }
        .badge.active { background: rgba(16, 185, 129, 0.15); color: var(--accent-emerald); }
        .badge.inactive { background: rgba(239, 68, 68, 0.15); color: var(--accent-pink); }
        
        .action-icon-btn {
            background: transparent;
            border: 1px solid var(--glass-border);
            color: var(--text-muted);
            width: 28px;
            height: 28px;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 4px;
        }
        .action-icon-btn:hover {
            border-color: var(--accent-blue);
            color: var(--text-main);
        }

        /* Quick Action Toolbar */
        .quick-actions {
            background: rgba(255,255,255,0.01);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 24px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .quick-actions h4 {
            font-size: 16px;
            font-weight: 600;
        }

        .btn-group {
            display: flex;
            gap: 12px;
        }

        .btn {
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn:hover {
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: rgba(255,255,255,0.06);
            color: var(--text-main);
            border: 1px solid var(--glass-border);
        }

        .btn-secondary:hover { background: rgba(255,255,255,0.1); }

        /* CRUD Modal Backdrop overlay */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(5, 5, 10, 0.8);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .modal-card {
            background: var(--bg-primary);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.6);
            position: relative;
        }
        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 20px;
            cursor: pointer;
            outline: none;
        }

        @media (max-width: 992px) {
            .charts-row { grid-template-columns: 1fr; }
            .sidebar { display: none; }
            .main-panel { margin-left: 0; padding: 20px; }
        }
    </style>
</head>
<body>
    <!-- Toast Message alerts -->
    <div class="toast-container" id="toast-box"></div>

    <!-- 1. AUTH SCREEN CONTAINER -->
    <div class="auth-container" id="auth-screen">
        <div class="auth-card">
            <div class="auth-logo">
                <h2>Global School ERP</h2>
                <p>Custom JWT School Management API Service</p>
            </div>

            <!-- Prefilled credentials helper -->
            <div class="demo-credentials">
                <h5>Demo Quick Access</h5>
                <p>Login with administrative privileges:</p>
                <div style="margin-top: 8px; display:flex; justify-content:space-between; align-items:center;">
                    <span>User: <strong>school_admin</strong></span>
                    <button class="demo-btn" onclick="prefillDemoLogin()">Prefill Login</button>
                </div>
            </div>

            <form id="login-form">
                <div class="form-group">
                    <label>Username / Email</label>
                    <input type="text" id="username" class="form-input" placeholder="e.g. school_admin" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="password" class="form-input" placeholder="••••••••" required>
                </div>
                <button type="submit" class="auth-submit-btn">Authorize & Login</button>
            </form>
        </div>
    </div>

    <!-- 2. MAIN APP CONTAINER -->
    <div class="app-container" id="app-screen" style="display: none;">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div>
                <div class="brand">
                    <div class="brand-icon">S</div>
                    <span>Global School ERP</span>
                </div>
                <ul class="menu-list">
                    <li><button class="menu-item active" onclick="switchTab('dashboard')">Dashboard</button></li>
                    <li><button class="menu-item" onclick="switchTab('students')">Students</button></li>
                    <li><button class="menu-item" onclick="switchTab('teachers')">Teachers</button></li>
                    <li><button class="menu-item" onclick="switchTab('attendance')">Attendance</button></li>
                    <li><button class="menu-item" onclick="switchTab('timetable')">Timetable</button></li>
                    <li><button class="menu-item" onclick="switchTab('fees')">Fees Module</button></li>
                    <li><button class="menu-item" onclick="switchTab('library')">Library</button></li>
                    <li><button class="menu-item" onclick="switchTab('transport')">Transport</button></li>
                </ul>
            </div>
            
            <div class="user-profile">
                <div class="user-profile-inner">
                    <div class="avatar" id="profile-avatar">AD</div>
                    <div class="user-info">
                        <h4 id="profile-name">Admin Portal</h4>
                        <p id="profile-role">Super Administrator</p>
                    </div>
                </div>
                <button class="logout-icon-btn" onclick="logout()" title="Sign Out">✖</button>
            </div>
        </aside>

        <!-- Main Workspace -->
        <main class="main-panel">
            <!-- Top Header -->
            <header class="header-section">
                <div class="title-group">
                    <h1 id="tab-title-header">School Management Overview</h1>
                    <p id="tab-subtitle-header">Live insights, statistical charts, and management workflows dashboard</p>
                </div>
                <div class="badge-live">
                    <span class="live-dot"></span> Live Analytics Ready
                </div>
            </header>

            <!-- TAB 1: DASHBOARD -->
            <div class="tab-panel active" id="tab-dashboard">
                <!-- Statistical Cards -->
                <section class="cards-grid">
                    <div class="stat-card">
                        <div class="card-icon">🎓</div>
                        <div class="card-label">Total Active Students</div>
                        <div class="card-value" id="card-total-students">1,248</div>
                    </div>
                    <div class="stat-card">
                        <div class="card-icon">👨‍🏫</div>
                        <div class="card-label">Certified Teachers</div>
                        <div class="card-value" id="card-total-teachers">84</div>
                    </div>
                    <div class="stat-card">
                        <div class="card-icon">💵</div>
                        <div class="card-label">Monthly Fees Collected</div>
                        <div class="card-value" id="card-monthly-fees">$42,560</div>
                    </div>
                    <div class="stat-card">
                        <div class="card-icon">📅</div>
                        <div class="card-label">Today's Attendance (Present)</div>
                        <div class="card-value" id="card-today-attendance">1</div>
                    </div>
                </section>

                <!-- Interactive Chart Rows -->
                <section class="charts-row">
                    <!-- Admissions Trend Chart -->
                    <div class="chart-box">
                        <div class="chart-header">
                            <h3>Student Admissions & Fees Collection Trends (Last 6 Months)</h3>
                            <span style="font-size: 12px; color: var(--text-muted);">Real-time aggregated stats</span>
                        </div>
                        <div class="chart-canvas">
                            <svg class="svg-chart" viewBox="0 0 500 200" preserveAspectRatio="none">
                                <defs>
                                    <linearGradient id="blueGrad" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.4"/>
                                        <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.0"/>
                                    </linearGradient>
                                    <linearGradient id="purpleGrad" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#8b5cf6" stop-opacity="0.4"/>
                                        <stop offset="100%" stop-color="#8b5cf6" stop-opacity="0.0"/>
                                    </linearGradient>
                                </defs>
                                <line x1="0" y1="50" x2="500" y2="50" stroke="rgba(255,255,255,0.05)" stroke-width="1" />
                                <line x1="0" y1="100" x2="500" y2="100" stroke="rgba(255,255,255,0.05)" stroke-width="1" />
                                <line x1="0" y1="150" x2="500" y2="150" stroke="rgba(255,255,255,0.05)" stroke-width="1" />
                                
                                <path d="M 0 160 Q 100 120 200 130 T 400 80 L 500 60 L 500 200 L 0 200 Z" fill="url(#blueGrad)" />
                                <path d="M 0 160 Q 100 120 200 130 T 400 80 L 500 60" fill="none" stroke="var(--accent-blue)" stroke-width="3" />
                                
                                <path d="M 0 180 Q 100 140 200 150 T 400 100 L 500 90 L 500 200 L 0 200 Z" fill="url(#purpleGrad)" />
                                <path d="M 0 180 Q 100 140 200 150 T 400 100 L 500 90" fill="none" stroke="var(--accent-purple)" stroke-width="3" />
                                
                                <circle cx="200" cy="130" r="5" fill="#3b82f6" stroke="#fff" stroke-width="1.5" />
                                <circle cx="400" cy="80" r="5" fill="#3b82f6" stroke="#fff" stroke-width="1.5" />
                                <circle cx="200" cy="150" r="5" fill="#8b5cf6" stroke="#fff" stroke-width="1.5" />
                                <circle cx="400" cy="100" r="5" fill="#8b5cf6" stroke="#fff" stroke-width="1.5" />
                            </svg>
                        </div>
                    </div>

                    <!-- Notice Board -->
                    <div class="chart-box">
                        <div class="chart-header">
                            <h3>Notice Board</h3>
                        </div>
                        <div class="notice-list" id="notice-board-container">
                            <div class="notice-item">
                                <h5>Summer Holidays Announcement</h5>
                                <p>School campuses will remain closed for summer recess from June 20 to July 10, 2026.</p>
                                <div class="notice-date">Just now</div>
                            </div>
                            <div class="notice-item" style="border-left-color: var(--accent-pink);">
                                <h5>Annual Science Exhibition</h5>
                                <p>Students will present their science models in the main auditorium. Parents are invited.</p>
                                <div class="notice-date">2 hours ago</div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- TAB 2: STUDENTS -->
            <div class="tab-panel" id="tab-students">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Student Registry</h3>
                        <div class="table-controls">
                            <button class="btn" onclick="openCreateModal('student')">+ Add Student</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Adm No</th>
                                <th>Roll</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="students-table-body">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: TEACHERS -->
            <div class="tab-panel" id="tab-teachers">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Teacher Directory</h3>
                        <div class="table-controls">
                            <button class="btn" onclick="openCreateModal('teacher')">+ Add Teacher</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Emp Code</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Qualification</th>
                                <th>Salary</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="teachers-table-body">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 4: ATTENDANCE -->
            <div class="tab-panel" id="tab-attendance">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Student Attendance Registry</h3>
                        <div class="table-controls">
                            <button class="btn btn-secondary" onclick="toast('Mocking attendance update...', 'success')">Trigger Day Refresh</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Log ID</th>
                                <th>Student ID</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="attendance-table-body">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 5: TIMETABLE -->
            <div class="tab-panel" id="tab-timetable">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Schedules and Lecture Hours</h3>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Schedule Reference</th>
                                <th>Day</th>
                                <th>Configuration Parameters</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="timetable-table-body">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 6: FEES -->
            <div class="tab-panel" id="tab-fees">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Fees Billed & Collected Registry</h3>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Billed Amount</th>
                                <th>Payment Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="fees-table-body">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 7: LIBRARY -->
            <div class="tab-panel" id="tab-library">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Library Book Circulation</h3>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Book ID</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>ISBN</th>
                                <th>Loan Status</th>
                            </tr>
                        </thead>
                        <tbody id="library-table-body">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 8: TRANSPORT -->
            <div class="tab-panel" id="tab-transport">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Bus Routes and Fleet Allocations</h3>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Route</th>
                                <th>Source</th>
                                <th>Destination</th>
                                <th>Bus Plate</th>
                                <th>Driver Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="transport-table-body">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Action Panel -->
            <footer class="quick-actions" style="margin-top: 30px;">
                <div>
                    <h4>Interactive API & Portal Operations</h4>
                    <p style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">Inspect backend capabilities using interactive Swagger tool and Postman specs</p>
                </div>
                <div class="btn-group">
                    <a href="/school-management-api-docs" class="btn" target="_blank">Open Swagger API Docs</a>
                    <button class="btn btn-secondary" onclick="testApiConnection()">Test API Connection</button>
                </div>
            </footer>
        </main>
    </div>

    <!-- MODAL OVERLAYS -->
    <div class="modal-overlay" id="crud-modal">
        <div class="modal-card">
            <button class="modal-close" onclick="closeCrudModal()">✖</button>
            <h3 id="modal-title" style="font-size: 20px; margin-bottom: 20px;">Add Entry</h3>
            <form id="crud-form" onsubmit="handleFormSubmit(event)">
                <input type="hidden" id="entity-type">
                <div id="dynamic-form-fields">
                    <!-- Fields injected dynamically -->
                </div>
                <button type="submit" class="auth-submit-btn" style="margin-top: 15px;">Save Entry</button>
            </form>
        </div>
    </div>

    <!-- JavaScript REST Logic -->
    <script>
        const API_URL = '/wp-json/school-management/v1';
        let authToken = localStorage.getItem('school_jwt_token') || '';
        let currentUser = null;

        // Prefill credential fields
        function prefillDemoLogin() {
            document.getElementById('username').value = 'school_admin';
            document.getElementById('password').value = 'adminpass123';
            toast('Prefilled credentials! Click login.', 'success');
        }

        // Switch panel tabs
        function switchTab(tabName) {
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.menu-item').forEach(m => m.classList.remove('active'));
            
            const targetPanel = document.getElementById(`tab-${tabName}`);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }

            // Find clicked menu button
            const buttons = Array.from(document.querySelectorAll('.menu-item'));
            const activeBtn = buttons.find(b => b.innerText.toLowerCase() === tabName.toLowerCase() || (tabName === 'timetable' && b.innerText.toLowerCase() === 'timetable') || (tabName === 'fees' && b.innerText.toLowerCase().includes('fees')));
            if (activeBtn) activeBtn.classList.add('active');

            // Set headers
            const headerTitle = document.getElementById('tab-title-header');
            const headerSubtitle = document.getElementById('tab-subtitle-header');
            
            headerTitle.innerText = tabName.charAt(0).toUpperCase() + tabName.slice(1) + " Management";
            headerSubtitle.innerText = `Inspect, add, update, and search active school ${tabName} records.`;

            // Load data
            if (tabName === 'dashboard') {
                headerTitle.innerText = "School Management Overview";
                headerSubtitle.innerText = "Live insights, statistical charts, and management workflows dashboard";
                loadDashboardData();
            } else if (tabName === 'students') {
                loadStudents();
            } else if (tabName === 'teachers') {
                loadTeachers();
            } else if (tabName === 'attendance') {
                loadAttendance();
            } else if (tabName === 'timetable') {
                loadTimetable();
            } else if (tabName === 'fees') {
                loadFees();
            } else if (tabName === 'library') {
                loadLibrary();
            } else if (tabName === 'transport') {
                loadTransport();
            }
        }

        // Toast alert helper
        function toast(message, type = 'success') {
            const box = document.getElementById('toast-box');
            const div = document.createElement('div');
            div.className = `toast ${type}`;
            div.innerText = message;
            box.appendChild(div);
            
            setTimeout(() => div.classList.add('show'), 50);
            setTimeout(() => {
                div.classList.remove('show');
                setTimeout(() => div.remove(), 300);
            }, 3000);
        }

        // Validate API state
        function testApiConnection() {
            fetch(`${API_URL}/dashboard`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => {
                if (res.ok) {
                    toast('API connection stable and authenticated!', 'success');
                } else {
                    toast('Authentication verification failed.', 'error');
                }
            })
            .catch(() => toast('API server unreachable.', 'error'));
        }

        // Prefill on page loaded
        window.addEventListener('DOMContentLoaded', () => {
            if (authToken) {
                verifySession();
            } else {
                showAuthScreen();
            }
        });

        function verifySession() {
            fetch(`${API_URL}/auth/me`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => {
                if (res.ok) return res.json();
                throw new Error('Expired session');
            })
            .then(body => {
                currentUser = body.data;
                showAppScreen();
            })
            .catch(() => {
                logout();
            });
        }

        function showAuthScreen() {
            document.getElementById('auth-screen').style.display = 'flex';
            document.getElementById('app-screen').style.display = 'none';
        }

        function showAppScreen() {
            document.getElementById('auth-screen').style.display = 'none';
            document.getElementById('app-screen').style.display = 'flex';
            
            // Set User card
            if (currentUser) {
                document.getElementById('profile-name').innerText = currentUser.name;
                document.getElementById('profile-role').innerText = currentUser.role.replace('school_', '').replace('_', ' ').toUpperCase();
                document.getElementById('profile-avatar').innerText = currentUser.name.split(' ').map(n=>n[0]).join('').toUpperCase().substring(0, 2);
            }
            
            switchTab('dashboard');
        }

        function logout() {
            localStorage.removeItem('school_jwt_token');
            authToken = '';
            currentUser = null;
            showAuthScreen();
            toast('Logged out successfully', 'success');
        }

        // Login Handler
        document.getElementById('login-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const u = document.getElementById('username').value;
            const p = document.getElementById('password').value;

            fetch(`${API_URL}/auth/login`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username: u, password: p })
            })
            .then(res => {
                if (!res.ok) throw new Error('Invalid login credentials.');
                return res.json();
            })
            .then(body => {
                authToken = body.data.token;
                localStorage.setItem('school_jwt_token', authToken);
                currentUser = body.data.user;
                toast('Logged in successfully!', 'success');
                showAppScreen();
            })
            .catch(err => {
                toast(err.message, 'error');
            });
        });

        // Load dashboard stats
        function loadDashboardData() {
            fetch(`${API_URL}/dashboard`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const c = body.data.cards;
                document.getElementById('card-total-students').innerText = c.total_students;
                document.getElementById('card-total-teachers').innerText = c.total_teachers;
                document.getElementById('card-monthly-fees').innerText = '$' + Number(c.monthly_fee_collection).toLocaleString();
                document.getElementById('card-today-attendance').innerText = c.today_attendance.Present || 0;
            })
            .catch(() => {});
        }

        // Load Students List
        function loadStudents() {
            fetch(`${API_URL}/students`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const tbody = document.getElementById('students-table-body');
                tbody.innerHTML = '';
                if (body.data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;">No students found</td></tr>';
                    return;
                }
                body.data.data.forEach(student => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${student.admission_no}</td>
                        <td>${student.roll_no || '-'}</td>
                        <td>${student.first_name} ${student.last_name}</td>
                        <td>${student.email || '-'}</td>
                        <td>${student.mobile || '-'}</td>
                        <td><span class="badge active">${student.status}</span></td>
                        <td>
                            <button class="action-icon-btn" onclick="deleteRecord('students', ${student.id}, loadStudents)">🗑</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(() => {});
        }

        // Load Teachers List
        function loadTeachers() {
            fetch(`${API_URL}/teachers`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const tbody = document.getElementById('teachers-table-body');
                tbody.innerHTML = '';
                if (body.data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;">No teachers registered</td></tr>';
                    return;
                }
                body.data.data.forEach(t => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${t.employee_code}</td>
                        <td>${t.name}</td>
                        <td>${t.email}</td>
                        <td>${t.mobile}</td>
                        <td>${t.qualification || '-'}</td>
                        <td>$${Number(t.salary).toLocaleString()}</td>
                        <td><span class="badge active">${t.status}</span></td>
                        <td>
                            <button class="action-icon-btn" onclick="deleteRecord('teachers', ${t.id}, loadTeachers)">🗑</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(() => {});
        }

        // Load Student Attendance
        function loadAttendance() {
            fetch(`${API_URL}/attendance/students`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const tbody = document.getElementById('attendance-table-body');
                tbody.innerHTML = '';
                if (body.data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No attendance logged</td></tr>';
                    return;
                }
                body.data.data.forEach(log => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>#${log.id}</td>
                        <td>Student #${log.student_id}</td>
                        <td>${log.attendance_date}</td>
                        <td><span class="badge active">${log.status}</span></td>
                        <td>${log.remarks || '-'}</td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(() => {});
        }

        // Load Timetable
        function loadTimetable() {
            fetch(`${API_URL}/timetable`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const tbody = document.getElementById('timetable-table-body');
                tbody.innerHTML = '';
                if (body.data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">No timetable registered</td></tr>';
                    return;
                }
                body.data.data.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.title}</td>
                        <td>${row.details?.day || 'Monday'}</td>
                        <td>Slots count: ${(row.details?.slots || []).length}</td>
                        <td><span class="badge active">${row.status}</span></td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(() => {});
        }

        // Load Fees
        function loadFees() {
            fetch(`${API_URL}/fees/collections`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const tbody = document.getElementById('fees-table-body');
                tbody.innerHTML = '';
                if (body.data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No fee collection records</td></tr>';
                    return;
                }
                body.data.data.forEach(fee => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>#${fee.id}</td>
                        <td>${fee.title}</td>
                        <td>$${Number(fee.amount).toLocaleString()}</td>
                        <td>${fee.payment_method || 'Razorpay'}</td>
                        <td><span class="badge active">PAID</span></td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(() => {});
        }

        // Load Library
        function loadLibrary() {
            fetch(`${API_URL}/library/books`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const tbody = document.getElementById('library-table-body');
                tbody.innerHTML = '';
                if (body.data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No library books found</td></tr>';
                    return;
                }
                body.data.data.forEach(book => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>#${book.id}</td>
                        <td>${book.title}</td>
                        <td>${book.author}</td>
                        <td>${book.isbn || '-'}</td>
                        <td><span class="badge active">${book.status}</span></td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(() => {});
        }

        // Load Transport Routing
        function loadTransport() {
            fetch(`${API_URL}/transport`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const tbody = document.getElementById('transport-table-body');
                tbody.innerHTML = '';
                if (body.data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No transport routes mapped</td></tr>';
                    return;
                }
                body.data.data.forEach(route => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${route.route_name}</td>
                        <td>${route.source}</td>
                        <td>${route.destination}</td>
                        <td>${route.vehicle_number}</td>
                        <td>${route.driver_name}</td>
                        <td><span class="badge active">${route.status}</span></td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(() => {});
        }

        // Generic delete action handler
        function deleteRecord(endpoint, id, callback) {
            if (!confirm('Are you sure you want to remove this entry?')) return;
            fetch(`${API_URL}/${endpoint}/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => {
                if (res.ok) {
                    toast('Record deleted successfully!', 'success');
                    callback();
                } else {
                    toast('Failed to delete record.', 'error');
                }
            })
            .catch(() => toast('Server error.', 'error'));
        }

        // CRUD Modal control
        function openCreateModal(type) {
            document.getElementById('entity-type').value = type;
            const title = document.getElementById('modal-title');
            const fields = document.getElementById('dynamic-form-fields');
            fields.innerHTML = '';

            if (type === 'student') {
                title.innerText = "Register Student";
                fields.innerHTML = `
                    <div class="form-group">
                        <label>Admission Number</label>
                        <input type="text" id="s-adm" class="form-input" placeholder="e.g. ADM2026102" required>
                    </div>
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" id="s-first" class="form-input" placeholder="e.g. John" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" id="s-last" class="form-input" placeholder="e.g. Doe" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" id="s-email" class="form-input" placeholder="e.g. john@student.erp">
                    </div>
                `;
            } else if (type === 'teacher') {
                title.innerText = "Add Teacher Record";
                fields.innerHTML = `
                    <div class="form-group">
                        <label>Employee Code</label>
                        <input type="text" id="t-code" class="form-input" placeholder="e.g. EMP1020" required>
                    </div>
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" id="t-name" class="form-input" placeholder="e.g. Dr. Robert Carter" required>
                    </div>
                    <div class="form-group">
                        <label>Mobile Number</label>
                        <input type="text" id="t-mobile" class="form-input" placeholder="e.g. 9876543210" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" id="t-email" class="form-input" placeholder="e.g. robert@school.erp" required>
                    </div>
                `;
            }

            document.getElementById('crud-modal').style.display = 'flex';
        }

        function closeCrudModal() {
            document.getElementById('crud-modal').style.display = 'none';
        }

        function handleFormSubmit(e) {
            e.preventDefault();
            const type = document.getElementById('entity-type').value;

            if (type === 'student') {
                const body = {
                    admission_no: document.getElementById('s-adm').value,
                    first_name: document.getElementById('s-first').value,
                    last_name: document.getElementById('s-last').value,
                    email: document.getElementById('s-email').value
                };
                
                fetch(`${API_URL}/students`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${authToken}`
                    },
                    body: JSON.stringify(body)
                })
                .then(res => {
                    if (res.ok) {
                        toast('Student registered successfully!', 'success');
                        closeCrudModal();
                        loadStudents();
                    } else {
                        toast('Failed to register student.', 'error');
                    }
                })
                .catch(() => toast('Server error.', 'error'));
            } else if (type === 'teacher') {
                const body = {
                    employee_code: document.getElementById('t-code').value,
                    name: document.getElementById('t-name').value,
                    mobile: document.getElementById('t-mobile').value,
                    email: document.getElementById('t-email').value
                };

                fetch(`${API_URL}/teachers`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${authToken}`
                    },
                    body: JSON.stringify(body)
                })
                .then(res => {
                    if (res.ok) {
                        toast('Teacher created successfully!', 'success');
                        closeCrudModal();
                        loadTeachers();
                    } else {
                        toast('Failed to create teacher record.', 'error');
                    }
                })
                .catch(() => toast('Server error.', 'error'));
            }
        }
    </script>
</body>
</html>
