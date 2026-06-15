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
            max-width: 500px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.6);
        }
        .auth-logo {
            text-align: center;
            margin-bottom: 25px;
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
        
        /* Grid of test users for quick selection */
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
        }
        .demo-role-btn:hover {
            background: rgba(59, 130, 246, 0.08);
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

        .user-profile-wrapper {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
            margin-top: 10px;
        }

        .logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 14px;
            background: rgba(239, 68, 68, 0.12);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.25);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            outline: none;
        }
        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.22);
            border-color: rgba(239, 68, 68, 0.45);
            color: #f87171;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.12);
        }
        .logout-btn-icon {
            width: 15px;
            height: 15px;
            stroke-width: 2.5;
        }

        /* Custom Checklist Widget styles */
        .checklist-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 15px;
        }
        .checklist-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--text-main);
            cursor: pointer;
        }
        .checklist-item input[type="checkbox"] {
            accent-color: var(--accent-purple);
            cursor: pointer;
            width: 16px;
            height: 16px;
        }
        .checklist-item.checked span {
            text-decoration: line-through;
            color: var(--text-muted);
        }

        /* Homework list & planner styles */
        .homework-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 15px;
        }
        .homework-item {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            padding: 12px;
            font-size: 13px;
        }
        .homework-item h5 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--text-main);
        }
        .homework-item p {
            color: var(--text-muted);
            margin-bottom: 8px;
            line-height: 1.4;
        }
        .homework-meta {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: var(--text-muted);
        }
        .homework-badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 600;
        }
        .homework-badge.active { background: rgba(59, 130, 246, 0.15); color: var(--accent-blue); }
        .homework-badge.submitted { background: rgba(16, 185, 129, 0.15); color: var(--accent-emerald); }

        .dashboard-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 15px;
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

            <!-- Prefilled credentials helpers -->
            <div style="margin-bottom: 20px;">
                <h5 style="font-size: 12px; color: var(--accent-blue); font-weight:600; margin-bottom: 8px;">Quick-Access Account Select</h5>
                <div class="demo-roles-grid">
                    <button class="demo-role-btn" onclick="prefillUser('schoolsuperadmin', '123456')">
                        <span class="demo-role-title">Super Admin</span>
                        <span class="demo-role-user">schoolsuperadmin</span>
                    </button>
                    <button class="demo-role-btn" onclick="prefillUser('school_principal', 'principalpass123')">
                        <span class="demo-role-title">Principal</span>
                        <span class="demo-role-user">school_principal</span>
                    </button>
                    <button class="demo-role-btn" onclick="prefillUser('school_teacher', 'teacherpass123')">
                        <span class="demo-role-title">Teacher</span>
                        <span class="demo-role-user">school_teacher</span>
                    </button>
                    <button class="demo-role-btn" onclick="prefillUser('school_accountant', 'accountantpass123')">
                        <span class="demo-role-title">Accountant</span>
                        <span class="demo-role-user">school_accountant</span>
                    </button>
                    <button class="demo-role-btn" onclick="prefillUser('school_parent', 'parentpass123')">
                        <span class="demo-role-title">Parent</span>
                        <span class="demo-role-user">school_parent</span>
                    </button>
                    <button class="demo-role-btn" onclick="prefillUser('school_student', 'studentpass123')">
                        <span class="demo-role-title">Student</span>
                        <span class="demo-role-user">school_student</span>
                    </button>
                </div>
            </div>

            <form id="login-form">
                <div class="form-group">
                    <label>Username / Email</label>
                    <input type="text" id="username" class="form-input" placeholder="Select a role above or type..." oninput="checkLoginType()" required>
                </div>
                <div class="form-group" id="login-pass-group">
                    <label>Password</label>
                    <input type="password" id="password" class="form-input" placeholder="••••••••">
                </div>
                <div class="form-group" id="login-otp-group" style="display: none;">
                    <label>6-Digit Login Code</label>
                    <input type="text" id="login-otp" class="form-input" placeholder="e.g. 6-digit OTP code" maxlength="6">
                </div>
                <button type="submit" id="login-submit-btn" class="auth-submit-btn">Authorize & Login</button>
                <p class="auth-toggle-tip">
                    Don't have an account? <a href="#" onclick="showRegister(event)">Register here</a>
                </p>
            </form>

            <form id="register-form" style="display: none;" onsubmit="handleUserRegister(event)">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" id="reg-username" class="form-input" placeholder="e.g. principal_carter" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="reg-email" class="form-input" placeholder="e.g. carter@school.erp" required>
                </div>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" id="reg-name" class="form-input" placeholder="e.g. Dr. Robert Carter" required>
                </div>
                <div class="form-group">
                    <label>Account Role Type</label>
                    <select id="reg-role" class="form-input" style="background: #111827;" required>
                        <option value="school_super_admin">Super Admin</option>
                        <option value="school_principal">Principal</option>
                        <option value="school_teacher">Teacher</option>
                        <option value="school_accountant">Accountant</option>
                        <option value="school_parent">Parent</option>
                        <option value="school_student">Student</option>
                    </select>
                </div>
                <div class="form-group" id="reg-otp-group" style="display: none;">
                    <label>6-Digit Verification Code</label>
                    <input type="text" id="reg-otp" class="form-input" placeholder="e.g. 6-digit OTP code" maxlength="6">
                </div>
                <button type="submit" id="reg-submit-btn" class="auth-submit-btn">Register Account</button>
                <p class="auth-toggle-tip">
                    Already have an account? <a href="#" onclick="showLogin(event)">Log in here</a>
                </p>
            </form>
        </div>
    </div>

    <!-- 3. PENDING APPROVAL SCREEN -->
    <div class="auth-container" id="pending-screen" style="display: none;">
        <div class="auth-card" style="text-align: center;">
            <div class="auth-logo">
                <h2>Access Pending Approval</h2>
                <p>Global School ERP</p>
            </div>
            <div style="margin: 30px 0; display: flex; flex-direction: column; align-items: center; gap: 15px;">
                <div class="live-dot" style="width: 20px; height: 20px; background-color: var(--accent-pink);"></div>
                <h3 id="pending-title" style="font-size: 18px; font-weight: 600;">Account Under Review</h3>
                <p id="pending-message" style="color: var(--text-muted); font-size: 14px; line-height: 1.5;">
                    Soon school_super_admin will approve and you will be having access of your panel.
                </p>
            </div>
            <button class="auth-submit-btn" onclick="logout()" style="background: rgba(239, 68, 68, 0.2); border: 1px solid var(--accent-pink); box-shadow: none;">Log Out / Back</button>
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
                <ul class="menu-list" id="sidebar-menu-list">
                    <li id="menu-dashboard"><button class="menu-item active" onclick="switchTab('dashboard')">Dashboard</button></li>
                    <li id="menu-students"><button class="menu-item" onclick="switchTab('students')">Students</button></li>
                    <li id="menu-teachers"><button class="menu-item" onclick="switchTab('teachers')">Teachers</button></li>
                    <li id="menu-attendance"><button class="menu-item" onclick="switchTab('attendance')">Attendance</button></li>
                    <li id="menu-timetable"><button class="menu-item" onclick="switchTab('timetable')">Timetable</button></li>
                    <li id="menu-fees"><button class="menu-item" onclick="switchTab('fees')">Fees Module</button></li>
                    <li id="menu-library"><button class="menu-item" onclick="switchTab('library')">Library</button></li>
                    <li id="menu-transport"><button class="menu-item" onclick="switchTab('transport')">Transport</button></li>
                    <li id="menu-approvals"><button class="menu-item" onclick="switchTab('approvals')">User Approvals</button></li>
                    <li id="menu-apidocs"><button class="menu-item" onclick="switchTab('apidocs')">Portal APIs Doc</button></li>
                </ul>
            </div>
            
            <div class="user-profile-wrapper">
                <div class="user-profile">
                    <div class="user-profile-inner">
                        <div class="avatar" id="profile-avatar">AD</div>
                        <div class="user-info">
                            <h4 id="profile-name">Admin Portal</h4>
                            <p id="profile-role">Super Administrator</p>
                        </div>
                    </div>
                </div>
                <button class="logout-btn" onclick="confirmLogout()">
                    <svg class="logout-btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                    Sign Out
                </button>
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
                <div id="dashboard-dynamic-content">
                    <!-- Dynamic role-based dashboard injected here -->
                </div>
            </div>

            <!-- TAB 2: STUDENTS -->
            <div class="tab-panel" id="tab-students">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Student Registry</h3>
                        <div class="table-controls" id="student-actions-wrapper">
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
                                <th class="actions-header-column">Actions</th>
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
                        <div class="table-controls" id="teacher-actions-wrapper">
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
                                <th class="actions-header-column">Actions</th>
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

            <!-- TAB 9: APPROVALS -->
            <div class="tab-panel" id="tab-approvals">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Requested User Roles & Registration Approvals</h3>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Registered At</th>
                                <th style="text-align: right; width: 180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="approvals-table-body">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 10: PORTAL APIs DOC -->
            <div class="tab-panel" id="tab-apidocs">
                <div class="table-container" style="padding: 30px;">
                    <h3 style="font-size: 22px; font-weight: 600; margin-bottom: 12px; background: linear-gradient(to right, #ffffff, #d1d5db); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Portal APIs Doc & Integration Specs</h3>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 25px; line-height: 1.6;">
                        Inspect, test, and learn about the backend capabilities of the custom REST API plugin using the interactive Swagger UI and developer documentation.
                    </p>
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--glass-border); padding: 24px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <h4 style="font-size: 16px; font-weight: 600;">Interactive Swagger API Documentation</h4>
                            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Run active request queries, verify schema models, and inspect parameters in real-time.</p>
                        </div>
                        <div class="btn-group">
                            <a href="/school-management-api-docs" class="btn" target="_blank">Open Swagger API Docs</a>
                            <button class="btn btn-secondary" onclick="testApiConnection()">Test API Connection</button>
                        </div>
                    </div>

                    <!-- SMTP Settings Panel -->
                    <div style="margin-top: 35px; border-top: 1px solid var(--glass-border); padding-top: 30px;">
                        <h4 style="font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #fff;">SMTP Email Server Configuration</h4>
                        <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 20px; line-height: 1.5;">
                            Configure mail server SMTP details to send real verification OTP codes to registered users during signup.
                        </p>
                        
                        <form id="smtp-settings-form" onsubmit="saveSmtpSettings(event)" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                            <div class="form-group">
                                <label for="smtp-host">SMTP Host</label>
                                <input type="text" id="smtp-host" class="form-input" placeholder="e.g. smtp.gmail.com" required>
                            </div>
                            <div class="form-group">
                                <label for="smtp-port">SMTP Port</label>
                                <input type="text" id="smtp-port" class="form-input" placeholder="e.g. 587" required>
                            </div>
                            <div class="form-group">
                                <label for="smtp-secure">Encryption / Secure Connection</label>
                                <select id="smtp-secure" class="form-input" style="background: rgb(20, 20, 30);">
                                    <option value="tls">TLS (Recommended for port 587)</option>
                                    <option value="ssl">SSL (Recommended for port 465)</option>
                                    <option value="none">None</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="smtp-auth">Authentication Required</label>
                                <select id="smtp-auth" class="form-input" style="background: rgb(20, 20, 30);">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="smtp-username">SMTP Username / Email Address</label>
                                <input type="text" id="smtp-username" class="form-input" placeholder="e.g. rameshseervi242628@gmail.com" required>
                            </div>
                            <div class="form-group">
                                <label for="smtp-password">SMTP Password / App Password</label>
                                <input type="password" id="smtp-password" class="form-input" placeholder="••••••••" required>
                            </div>
                            <div class="form-group">
                                <label for="smtp-from-email">Sender Email Address (From Email)</label>
                                <input type="email" id="smtp-from-email" class="form-input" placeholder="e.g. no-reply@yourdomain.com" required>
                            </div>
                            <div class="form-group">
                                <label for="smtp-from-name">Sender Label (FromName)</label>
                                <input type="text" id="smtp-from-name" class="form-input" placeholder="e.g. Global School ERP" required>
                            </div>
                            
                            <div style="grid-column: span 2; display: flex; justify-content: flex-end; margin-top: 10px;">
                                <button type="submit" id="smtp-submit-btn" class="auth-submit-btn" style="width: auto; padding: 12px 30px; margin-top: 0;">Save SMTP Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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

    <!-- LOGOUT CONFIRMATION MODAL -->
    <div class="modal-overlay" id="logout-confirm-modal" style="display: none;">
        <div class="modal-card" style="max-width: 400px; text-align: center;">
            <div style="font-size: 48px; margin-bottom: 15px;">👋</div>
            <h3 style="font-size: 20px; margin-bottom: 12px; color: var(--text-main);">Confirm Sign Out</h3>
            <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 24px; line-height: 1.5;">Are you sure you want to sign out from Global School ERP? You will need to sign in again to access the portal.</p>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button class="btn btn-secondary" onclick="closeLogoutModal()" style="flex: 1; padding: 12px; margin: 0; justify-content: center;">Cancel</button>
                <button class="btn" onclick="executeLogout()" style="flex: 1; padding: 12px; margin: 0; background: linear-gradient(135deg, var(--accent-pink), var(--accent-purple)); justify-content: center;">Sign Out</button>
            </div>
        </div>
    </div>

    <!-- JavaScript REST Logic -->
    <script>
        const API_URL = '/wp-json/school-management/v1';
        let authToken = localStorage.getItem('school_jwt_token') || '';
        let currentUser = null;

        // Check login type (demo vs OTP based)
        function checkLoginType() {
            const usernameInput = document.getElementById('username');
            const passGroup = document.getElementById('login-pass-group');
            const passwordInput = document.getElementById('password');
            const otpGroup = document.getElementById('login-otp-group');
            const otpInput = document.getElementById('login-otp');
            const submitBtn = document.getElementById('login-submit-btn');

            const val = usernameInput.value.trim().toLowerCase();
            const demoUsers = [
                'schoolsuperadmin', 'school_principal', 'school_teacher', 'school_accountant', 'school_parent', 'school_student',
                'admin@school.erp', 'principal@school.erp', 'teacher@school.erp', 'accountant@school.erp', 'parent@school.erp', 'student@school.erp'
            ];

            if (demoUsers.includes(val)) {
                // Demo accounts: Password required
                passGroup.style.display = 'block';
                passwordInput.required = true;
                otpGroup.style.display = 'none';
                otpInput.required = false;
                submitBtn.innerText = 'Authorize & Login';
            } else {
                // Other accounts: OTP required, password hidden
                passGroup.style.display = 'none';
                passwordInput.required = false;
                passwordInput.value = '';
                
                if (otpGroup.style.display === 'none') {
                    submitBtn.innerText = 'Request Login Code';
                } else {
                    submitBtn.innerText = 'Verify & Login';
                }
            }
        }

        // Prefill credential fields based on select
        function prefillUser(username, password) {
            showLogin();
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
            checkLoginType();
            toast(`Prefilled as ${username.replace('school_', '').toUpperCase()}! Click login.`, 'success');
        }

        // Toggle Auth views
        function showRegister(e) {
            if (e) e.preventDefault();
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-form').style.display = 'block';
            
            // Reset OTP fields
            document.getElementById('reg-otp-group').style.display = 'none';
            document.getElementById('reg-otp').required = false;
            document.getElementById('reg-otp').value = '';
            document.getElementById('reg-submit-btn').innerText = 'Register Account';
        }

        function showLogin(e) {
            if (e) e.preventDefault();
            document.getElementById('login-form').style.display = 'block';
            document.getElementById('register-form').style.display = 'none';
            
            // Reset OTP fields
            document.getElementById('login-otp-group').style.display = 'none';
            document.getElementById('login-otp').required = false;
            document.getElementById('login-otp').value = '';
            document.getElementById('login-submit-btn').innerText = 'Authorize & Login';
            checkLoginType();
        }

        // User registration handler
        function handleUserRegister(e) {
            e.preventDefault();
            
            const otpGroup = document.getElementById('reg-otp-group');
            const otpInput = document.getElementById('reg-otp');
            const submitBtn = document.getElementById('reg-submit-btn');
            
            const u = document.getElementById('reg-username').value;
            const em = document.getElementById('reg-email').value;
            const n = document.getElementById('reg-name').value;
            const r = document.getElementById('reg-role').value;
 
            // Phase 1: OTP initiation
            if (otpGroup.style.display === 'none') {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Sending OTP...';
                
                fetch(`${API_URL}/auth/register`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username: u, email: em, name: n, role: r })
                })
                .then(res => {
                    if (!res.ok) {
                        return res.json().then(body => { throw new Error(body.message || 'OTP sending failed'); });
                    }
                    return res.json();
                })
                .then(body => {
                    toast('Verification OTP code sent to your email. Check inbox!', 'success');
                    otpGroup.style.display = 'block';
                    otpInput.required = true;
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Verify & Register';
                })
                .catch(err => {
                    toast(err.message, 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Register Account';
                });
            } else {
                // Phase 2: Verify and Register
                const otpVal = otpInput.value;
                if (!otpVal || otpVal.length < 6) {
                    toast('Please enter the 6-digit OTP code sent to your email.', 'error');
                    return;
                }
                
                submitBtn.disabled = true;
                submitBtn.innerText = 'Verifying...';
                
                fetch(`${API_URL}/auth/register/verify`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: em, otp: otpVal })
                })
                .then(res => {
                    if (!res.ok) {
                        return res.json().then(body => { throw new Error(body.message || 'OTP verification failed'); });
                    }
                    return res.json();
                })
                .then(body => {
                    toast('Verification successful! Account created. Please wait for super admin approval.', 'success');
                    showLogin();
                    document.getElementById('username').value = u;
                })
                .catch(err => {
                    toast(err.message, 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Verify & Register';
                });
            }
        }

        // Switch panel tabs
        function switchTab(tabName) {
            localStorage.setItem('school_active_tab', tabName);
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
            } else if (tabName === 'approvals') {
                loadApprovals();
            } else if (tabName === 'apidocs') {
                headerTitle.innerText = "Portal APIs Doc";
                headerSubtitle.innerText = "Developer integration guides, Swagger endpoints list, and connectivity checks";
                loadSmtpSettings();
            }
        }

        // Adjust visible sidebar elements and CRUD permissions based on logged role
        function configureUIPermissions() {
            if (!currentUser) return;
            const role = currentUser.role;

            // Define visible menus for roles
            const menuMapping = {
                'administrator': ['dashboard', 'students', 'teachers', 'attendance', 'timetable', 'fees', 'library', 'transport', 'approvals', 'apidocs'],
                'school_super_admin': ['dashboard', 'students', 'teachers', 'attendance', 'timetable', 'fees', 'library', 'transport', 'approvals', 'apidocs'],
                'school_principal': ['dashboard', 'students', 'teachers', 'attendance', 'timetable', 'library', 'transport'],
                'school_teacher': ['dashboard', 'students', 'attendance', 'timetable'],
                'school_accountant': ['dashboard', 'teachers', 'fees'],
                'school_parent': ['dashboard', 'attendance', 'timetable', 'fees'],
                'school_student': ['dashboard', 'attendance', 'timetable', 'library']
            };

            const visibleMenus = menuMapping[role] || ['dashboard'];

            // Show/hide menu items
            const menus = ['dashboard', 'students', 'teachers', 'attendance', 'timetable', 'fees', 'library', 'transport', 'approvals', 'apidocs'];
            menus.forEach(menu => {
                const el = document.getElementById(`menu-${menu}`);
                if (el) {
                    if (visibleMenus.includes(menu)) {
                        el.style.display = 'block';
                    } else {
                        el.style.display = 'none';
                    }
                }
            });

            // Adjust CRUD Buttons
            const writePrivilegeRoles = ['administrator', 'school_super_admin', 'school_principal'];
            const studentActions = document.getElementById('student-actions-wrapper');
            const teacherActions = document.getElementById('teacher-actions-wrapper');

            if (studentActions) {
                studentActions.style.display = writePrivilegeRoles.includes(role) ? 'block' : 'none';
            }
            if (teacherActions) {
                teacherActions.style.display = writePrivilegeRoles.includes(role) ? 'block' : 'none';
            }

            // Adjust Action column in student/teacher tables
            const hideActions = !writePrivilegeRoles.includes(role);
            document.querySelectorAll('.actions-header-column').forEach(el => {
                el.style.display = hideActions ? 'none' : 'table-cell';
            });

            // Toggle notice add button for Super Admin / Admin
            const addNoticeBtn = document.getElementById('add-notice-btn');
            if (addNoticeBtn) {
                addNoticeBtn.style.display = ['administrator', 'school_super_admin'].includes(role) ? 'inline-block' : 'none';
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
            document.getElementById('pending-screen').style.display = 'none';
        }

        function showAppScreen() {
            if (!currentUser) return;

            // Check if user is approved
            const status = currentUser.status || 'APPROVED';
            if (status !== 'APPROVED') {
                document.getElementById('auth-screen').style.display = 'none';
                document.getElementById('app-screen').style.display = 'none';
                document.getElementById('pending-screen').style.display = 'flex';

                const titleEl = document.getElementById('pending-title');
                const msgEl = document.getElementById('pending-message');

                if (status === 'BLOCKED') {
                    titleEl.innerText = "Account Blocked";
                    msgEl.innerText = "Your account has been blocked by the school_super_admin. Please contact support.";
                } else if (status === 'HOLD') {
                    titleEl.innerText = "Account On Hold";
                    msgEl.innerText = "Your account is currently on hold. Soon school_super_admin will approve and you will be having access of your panel.";
                } else {
                    titleEl.innerText = "Access Pending Approval";
                    msgEl.innerText = "Soon school_super_admin will approve and you will be having access of your panel.";
                }
                return;
            }

            document.getElementById('auth-screen').style.display = 'none';
            document.getElementById('pending-screen').style.display = 'none';
            document.getElementById('app-screen').style.display = 'flex';
            
            // Set User card
            document.getElementById('profile-name').innerText = currentUser.name;
            document.getElementById('profile-role').innerText = currentUser.role.replace('school_', '').replace('_', ' ').toUpperCase();
            document.getElementById('profile-avatar').innerText = currentUser.name.split(' ').map(n=>n[0]).join('').toUpperCase().substring(0, 2);
            
            configureUIPermissions();
            
            // Restore active tab or default to dashboard
            const menuMapping = {
                'administrator': ['dashboard', 'students', 'teachers', 'attendance', 'timetable', 'fees', 'library', 'transport', 'approvals', 'apidocs'],
                'school_super_admin': ['dashboard', 'students', 'teachers', 'attendance', 'timetable', 'fees', 'library', 'transport', 'approvals', 'apidocs'],
                'school_principal': ['dashboard', 'students', 'teachers', 'attendance', 'timetable', 'library', 'transport'],
                'school_teacher': ['dashboard', 'students', 'attendance', 'timetable'],
                'school_accountant': ['dashboard', 'teachers', 'fees'],
                'school_parent': ['dashboard', 'attendance', 'timetable', 'fees'],
                'school_student': ['dashboard', 'attendance', 'timetable', 'library']
            };
            const role = currentUser.role;
            const allowedTabs = menuMapping[role] || ['dashboard'];

            let activeTab = localStorage.getItem('school_active_tab') || 'dashboard';
            if (!allowedTabs.includes(activeTab)) {
                activeTab = 'dashboard';
            }

            switchTab(activeTab);
        }

        function confirmLogout() {
            document.getElementById('logout-confirm-modal').style.display = 'flex';
        }

        function closeLogoutModal() {
            document.getElementById('logout-confirm-modal').style.display = 'none';
        }

        function executeLogout() {
            localStorage.removeItem('school_jwt_token');
            localStorage.removeItem('school_active_tab');
            authToken = '';
            currentUser = null;
            window.location.reload();
        }

        function logout() {
            executeLogout();
        }

        // Login Handler
        document.getElementById('login-form').addEventListener('submit', (e) => {
            e.preventDefault();
            
            const usernameInput = document.getElementById('username');
            const passGroup = document.getElementById('login-pass-group');
            const passwordInput = document.getElementById('password');
            const otpGroup = document.getElementById('login-otp-group');
            const otpInput = document.getElementById('login-otp');
            const submitBtn = document.getElementById('login-submit-btn');

            const u = usernameInput.value.trim();
            const p = passwordInput.value;
            const otpVal = otpInput.value.trim();

            const demoUsers = [
                'schoolsuperadmin', 'school_principal', 'school_teacher', 'school_accountant', 'school_parent', 'school_student',
                'admin@school.erp', 'principal@school.erp', 'teacher@school.erp', 'accountant@school.erp', 'parent@school.erp', 'student@school.erp'
            ];

            const isDemo = demoUsers.includes(u.toLowerCase());

            if (isDemo) {
                // Phase 1: Demo Password Auth
                submitBtn.disabled = true;
                submitBtn.innerText = 'Authorizing...';
                
                fetch(`${API_URL}/auth/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username: u, password: p })
                })
                .then(res => {
                    if (!res.ok) return res.json().then(b => { throw new Error(b.message || 'Invalid login credentials.'); });
                    return res.json();
                })
                .then(body => {
                    authToken = body.data.token;
                    localStorage.setItem('school_jwt_token', authToken);
                    currentUser = body.data.user;
                    toast(`Logged in as ${currentUser.role.replace('school_', '').toUpperCase()}!`, 'success');
                    showAppScreen();
                    submitBtn.disabled = false;
                })
                .catch(err => {
                    toast(err.message, 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Authorize & Login';
                });
            } else {
                // Passwordless OTP Flow
                if (otpGroup.style.display === 'none') {
                    // Phase 1: Request OTP
                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Sending Code...';

                    fetch(`${API_URL}/auth/login/initiate`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ username: u })
                    })
                    .then(res => {
                        if (!res.ok) return res.json().then(b => { throw new Error(b.message || 'Failed to send login code.'); });
                        return res.json();
                    })
                    .then(body => {
                        toast('Login verification code sent to your email. Check inbox!', 'success');
                        otpGroup.style.display = 'block';
                        otpInput.required = true;
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'Verify & Login';
                    })
                    .catch(err => {
                        toast(err.message, 'error');
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'Request Login Code';
                    });
                } else {
                    // Phase 2: Verify OTP and Login
                    if (!otpVal || otpVal.length < 6) {
                        toast('Please enter the 6-digit login verification code.', 'error');
                        return;
                    }

                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Verifying...';

                    fetch(`${API_URL}/auth/login`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ username: u, otp: otpVal })
                    })
                    .then(res => {
                        if (!res.ok) return res.json().then(b => { throw new Error(b.message || 'Verification failed. Invalid code.'); });
                        return res.json();
                    })
                    .then(body => {
                        authToken = body.data.token;
                        localStorage.setItem('school_jwt_token', authToken);
                        currentUser = body.data.user;
                        toast(`Logged in as ${currentUser.role.replace('school_', '').toUpperCase()}!`, 'success');
                        showAppScreen();
                        submitBtn.disabled = false;
                    })
                    .catch(err => {
                        toast(err.message, 'error');
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'Verify & Login';
                    });
                }
            }
        });

        // Helper to draw dynamic SVG line charts
        function drawLineChartSvg(data, xKey, yKey, colorGradId, strokeColor) {
            if (!data || data.length === 0) {
                return `<div style="color:var(--text-muted);font-size:13px;text-align:center;padding:50px 0;">No trend data available.</div>`;
            }
            
            const width = 500;
            const height = 150;
            const padding = 25;
            const chartWidth = width - 2 * padding;
            const chartHeight = height - 2 * padding;
            
            const yValues = data.map(d => parseFloat(d[yKey]) || 0);
            const maxY = Math.max(...yValues, 10);
            const minY = 0;
            
            const points = data.map((d, index) => {
                const x = padding + (index / (data.length - 1 || 1)) * chartWidth;
                const val = parseFloat(d[yKey]) || 0;
                const y = padding + chartHeight - ((val - minY) / (maxY - minY)) * chartHeight;
                let label = d[xKey];
                if (label && label.includes('-')) {
                    const parts = label.split('-');
                    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                    const mIdx = parseInt(parts[1]) - 1;
                    if (mIdx >= 0 && mIdx < 12) {
                        label = monthNames[mIdx] + ' ' + parts[0].slice(-2);
                    }
                }
                return { x, y, label, value: val };
            });
            
            let pathD = `M ${points[0].x} ${points[0].y}`;
            for (let i = 1; i < points.length; i++) {
                pathD += ` L ${points[i].x} ${points[i].y}`;
            }
            
            let areaD = `${pathD} L ${points[points.length - 1].x} ${height - padding} L ${points[0].x} ${height - padding} Z`;
            
            return `
                <svg class="svg-chart" viewBox="0 0 ${width} ${height}" preserveAspectRatio="none" style="width: 100%; height: 100%;">
                    <defs>
                        <linearGradient id="${colorGradId}" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="${strokeColor}" stop-opacity="0.3"/>
                            <stop offset="100%" stop-color="${strokeColor}" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <line x1="${padding}" y1="${padding}" x2="${width - padding}" y2="${padding}" stroke="rgba(255,255,255,0.05)" stroke-width="1" />
                    <line x1="${padding}" y1="${padding + chartHeight/2}" x2="${width - padding}" y2="${padding + chartHeight/2}" stroke="rgba(255,255,255,0.05)" stroke-width="1" />
                    <line x1="${padding}" y1="${padding + chartHeight}" x2="${width - padding}" y2="${padding + chartHeight}" stroke="rgba(255,255,255,0.08)" stroke-width="1" />
                    
                    <path d="${areaD}" fill="url(#${colorGradId})" />
                    <path d="${pathD}" fill="none" stroke="${strokeColor}" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    
                    ${points.map(p => `
                        <circle cx="${p.x}" cy="${p.y}" r="4.5" fill="${strokeColor}" stroke="#fff" stroke-width="1.5">
                            <title>${p.label}: ${p.value}</title>
                        </circle>
                    `).join('')}
                </svg>
                <div style="display:flex; justify-content:space-between; padding:0 ${padding}px; margin-top:8px; font-size:11px; color:var(--text-muted);">
                    ${points.map(p => `<span>${p.label}</span>`).join('')}
                </div>
            `;
        }

        // Helper to draw dynamic SVG bar charts for Exam Performance
        function drawExamBarChartSvg(data, examKey, avgKey, fillGradientId, barColor) {
            if (!data || data.length === 0) {
                return `<div style="color:var(--text-muted);font-size:13px;text-align:center;padding:50px 0;">No exam averages available.</div>`;
            }
            
            const width = 500;
            const height = 150;
            const padding = 25;
            const chartWidth = width - 2 * padding;
            const chartHeight = height - 2 * padding;
            
            const maxY = 100;
            const minY = 0;
            const barWidth = Math.max(15, (chartWidth / data.length) - 15);
            
            return `
                <svg class="svg-chart" viewBox="0 0 ${width} ${height}" preserveAspectRatio="none" style="width: 100%; height: 100%;">
                    <defs>
                        <linearGradient id="${fillGradientId}" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="${barColor}" stop-opacity="0.85"/>
                            <stop offset="100%" stop-color="${barColor}" stop-opacity="0.3"/>
                        </linearGradient>
                    </defs>
                    <line x1="${padding}" y1="${padding}" x2="${width - padding}" y2="${padding}" stroke="rgba(255,255,255,0.05)" stroke-width="1" />
                    <line x1="${padding}" y1="${padding + chartHeight/2}" x2="${width - padding}" y2="${padding + chartHeight/2}" stroke="rgba(255,255,255,0.05)" stroke-width="1" />
                    <line x1="${padding}" y1="${padding + chartHeight}" x2="${width - padding}" y2="${padding + chartHeight}" stroke="rgba(255,255,255,0.08)" stroke-width="1" />
                    
                    ${data.map((d, index) => {
                        const val = parseFloat(d[avgKey]) || 0;
                        const x = padding + index * (chartWidth / data.length) + (chartWidth / data.length - barWidth)/2;
                        const barHeight = (val / maxY) * chartHeight;
                        const y = padding + chartHeight - barHeight;
                        return `
                            <rect x="${x}" y="${y}" width="${barWidth}" height="${barHeight}" rx="4" fill="url(#${fillGradientId})">
                                <title>${d[examKey]}: ${val.toFixed(1)}%</title>
                            </rect>
                        `;
                    }).join('')}
                </svg>
                <div style="display:flex; justify-content:space-between; padding:0 ${padding}px; margin-top:8px; font-size:11px; color:var(--text-muted);">
                    ${data.map(d => `<span style="width:${chartWidth / data.length}px; text-align:center; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${d[examKey]}</span>`).join('')}
                </div>
            `;
        }

        // Generic checklist helper (stored in localStorage)
        function toggleDashboardChecklist(checkbox, id) {
            const key = `school_checklist_${id}`;
            localStorage.setItem(key, checkbox.checked ? '1' : '0');
            checkbox.closest('.checklist-item').classList.toggle('checked', checkbox.checked);
        }

        function loadChecklistState() {
            document.querySelectorAll('.checklist-item input[type="checkbox"]').forEach(cb => {
                const key = `school_checklist_${cb.id}`;
                const val = localStorage.getItem(key);
                if (val === '1') {
                    cb.checked = true;
                    cb.closest('.checklist-item').classList.add('checked');
                } else {
                    cb.checked = false;
                    cb.closest('.checklist-item').classList.remove('checked');
                }
            });
        }

        // Load dashboard pending registrations
        function loadDashboardPendingApprovals() {
            const tbody = document.getElementById('dashboard-pending-approvals-tbody');
            if (!tbody) return;
            
            fetch(`${API_URL}/auth/users`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                tbody.innerHTML = '';
                const pendingUsers = (body.data || []).filter(u => u.status === 'PENDING' && u.id !== currentUser.id);
                
                if (pendingUsers.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding:20px;">No pending registration requests.</td></tr>';
                    return;
                }
                
                pendingUsers.forEach(user => {
                    const roleName = user.role.replace('school_', '').replace('_', ' ').toUpperCase();
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${user.username}</td>
                        <td>${user.email || '-'}</td>
                        <td>${user.name}</td>
                        <td><span class="badge active" style="background:rgba(59,130,246,0.1); color:var(--accent-blue);">${roleName}</span></td>
                        <td>${user.registered_at ? new Date(user.registered_at).toLocaleDateString() : '-'}</td>
                        <td style="text-align: right;">
                            <button class="action-icon-btn" onclick="changeApprovalStatus(${user.id}, 'APPROVED')" title="Approve" style="border-color: var(--accent-emerald); color: var(--accent-emerald); padding:2px 8px; font-size:12px;">✓ Approve</button>
                            <button class="action-icon-btn" onclick="changeApprovalStatus(${user.id}, 'BLOCKED')" title="Block/Reject" style="border-color: var(--accent-pink); color: var(--accent-pink); padding:2px 8px; font-size:12px; margin-left:6px;">🚫 Block</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(() => {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:var(--text-pink);">Failed to fetch pending requests.</td></tr>';
            });
        }

        // Homework Planner Handlers for Teachers
        function toggleHomeworkForm() {
            const el = document.getElementById('teacher-homework-form-container');
            if (el) {
                el.style.display = el.style.display === 'none' ? 'block' : 'none';
            }
        }

        function postTeacherHomework(e) {
            e.preventDefault();
            const body = {
                title: document.getElementById('th-title').value,
                description: document.getElementById('th-desc').value,
                class_id: document.getElementById('th-class').value,
                subject_id: 1, // Default subject id
                due_date: document.getElementById('th-duedate').value
            };
            
            fetch(`${API_URL}/homework`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}`
                },
                body: JSON.stringify(body)
            })
            .then(res => {
                if (res.ok) {
                    toast('Homework published successfully!', 'success');
                    toggleHomeworkForm();
                    loadDashboardHomeworkList();
                } else {
                    toast('Failed to publish homework.', 'error');
                }
            })
            .catch(() => toast('Server error.', 'error'));
        }

        function loadDashboardHomeworkList() {
            const container = document.getElementById('dashboard-homework-list');
            if (!container) return;
            
            fetch(`${API_URL}/homework`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                container.innerHTML = '';
                const list = body.data.data || [];
                if (list.length === 0) {
                    container.innerHTML = '<div style="color:var(--text-muted); font-size:12px; text-align:center; padding:20px;">No homework assignments found.</div>';
                    return;
                }
                
                list.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'homework-item';
                    div.style.marginBottom = '10px';
                    
                    const details = item.details || {};
                    const dueDateStr = details.due_date ? new Date(details.due_date).toLocaleDateString() : 'No due date';
                    
                    let actionButton = '';
                    if (currentUser.role === 'school_teacher') {
                        actionButton = `<button class="action-icon-btn" onclick="deleteRecord('homework', ${item.id}, loadDashboardHomeworkList)" style="border-color:#ef4444; color:#ef4444; font-size:11px; padding:2px 6px;">🗑 Delete</button>`;
                    } else if (currentUser.role === 'school_student') {
                        actionButton = `<button class="btn" style="padding:4px 10px; font-size:11px; height:auto; margin:0;" onclick="openSubmissionModal(${item.id})">📤 Submit Work</button>`;
                    }
                    
                    div.innerHTML = `
                        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                            <h5 style="margin:0; font-size:13px; font-weight:600;">${item.title}</h5>
                            ${actionButton}
                        </div>
                        <p style="margin:6px 0 8px; font-size:12px; color:var(--text-muted); line-height:1.4;">${details.description || 'No description provided'}</p>
                        <div class="homework-meta">
                            <span>Due: ${dueDateStr}</span>
                            <span>Class: Grade ${details.class_id || 10}</span>
                        </div>
                    `;
                    container.appendChild(div);
                });
            })
            .catch(() => {
                container.innerHTML = '<div style="color:var(--text-pink); font-size:12px; text-align:center; padding:20px;">Error loading homework list.</div>';
            });
        }

        // Student submission handlers
        function openSubmissionModal(hwId) {
            const el = document.getElementById('student-submission-modal');
            if (el) {
                document.getElementById('sub-hw-id').value = hwId;
                el.style.display = 'flex';
            }
        }
        function closeSubmissionModal() {
            const el = document.getElementById('student-submission-modal');
            if (el) el.style.display = 'none';
        }
        function submitStudentHomework(e) {
            e.preventDefault();
            const hwId = document.getElementById('sub-hw-id').value;
            const fileUrl = document.getElementById('sub-file-url').value;
            
            const body = {
                homework_id: hwId,
                student_id: currentUser.id,
                file_url: fileUrl
            };
            
            fetch(`${API_URL}/homework/submit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}`
                },
                body: JSON.stringify(body)
            })
            .then(res => {
                if (res.ok) {
                    toast('Homework submitted successfully!', 'success');
                    closeSubmissionModal();
                    loadDashboardHomeworkList();
                } else {
                    toast('Failed to submit homework.', 'error');
                }
            })
            .catch(() => toast('Server error.', 'error'));
        }

        // Dynamic Role-Specific Dashboard Renderer
        function renderRoleDashboard(role, data) {
            const container = document.getElementById('dashboard-dynamic-content');
            if (!container) return;
            
            const cards = data.cards;
            const charts = data.charts;

            let badgeHtml = '';
            let dashboardTitle = '';
            
            if (role === 'administrator' || role === 'school_super_admin') {
                badgeHtml = `<span class="live-dot" style="background-color: var(--accent-blue);"></span> System Administrator`;
                dashboardTitle = `School Management Overview`;
            } else if (role === 'school_principal') {
                badgeHtml = `<span class="live-dot" style="background-color: var(--accent-purple);"></span> Academic Operations`;
                dashboardTitle = `Principal's Command Dashboard`;
            } else if (role === 'school_teacher') {
                badgeHtml = `<span class="live-dot" style="background-color: var(--accent-emerald);"></span> Educator Hub`;
                dashboardTitle = `Teacher's Classroom Center`;
            } else if (role === 'school_accountant') {
                badgeHtml = `<span class="live-dot" style="background-color: #f59e0b;"></span> Financial Auditor`;
                dashboardTitle = `Finance & Billing Ledger`;
            } else if (role === 'school_student') {
                badgeHtml = `<span class="live-dot" style="background-color: var(--accent-blue);"></span> Learning Workspace`;
                dashboardTitle = `My Academic Dashboard`;
            } else if (role === 'school_parent') {
                badgeHtml = `<span class="live-dot" style="background-color: var(--accent-pink);"></span> Parent Portal`;
                dashboardTitle = `Parent Care & Progress Center`;
            }

            const liveBadge = document.querySelector('.badge-live');
            if (liveBadge) {
                liveBadge.innerHTML = badgeHtml;
            }
            const pageHeaderTitle = document.getElementById('tab-title-header');
            if (pageHeaderTitle && localStorage.getItem('school_active_tab') === 'dashboard') {
                pageHeaderTitle.innerText = dashboardTitle;
            }

            let dashboardHtml = '';

            if (role === 'administrator' || role === 'school_super_admin') {
                const formattedFees = '$' + Number(cards.monthly_fee_collection).toLocaleString();
                dashboardHtml = `
                    <section class="cards-grid">
                        <div class="stat-card">
                            <div class="card-icon">🎓</div>
                            <div class="card-label">Total Active Students</div>
                            <div class="card-value" style="color:var(--accent-blue);">${cards.total_students}</div>
                        </div>
                        <div class="stat-card">
                            <div class="card-icon">👨‍🏫</div>
                            <div class="card-label">Certified Faculty</div>
                            <div class="card-value" style="color:var(--accent-purple);">${cards.total_teachers}</div>
                        </div>
                        <div class="stat-card">
                            <div class="card-icon">💵</div>
                            <div class="card-label">Monthly Collections</div>
                            <div class="card-value" style="color:var(--accent-emerald);">${formattedFees}</div>
                        </div>
                        <div class="stat-card">
                            <div class="card-icon">📅</div>
                            <div class="card-label">Today's Presence (Students)</div>
                            <div class="card-value" style="color:var(--accent-pink);">${cards.today_attendance.Present || 0}</div>
                        </div>
                    </section>
                    
                    <section class="charts-row">
                        <div class="chart-box">
                            <div class="chart-header">
                                <h3>Student Admissions Trend</h3>
                                <span style="font-size: 12px; color: var(--text-muted);">Aggregated student registrations</span>
                            </div>
                            <div class="chart-canvas" style="height:170px;">
                                ${drawLineChartSvg(charts.admission_trends, 'month', 'count', 'blueGradDynamic', 'var(--accent-blue)')}
                            </div>
                        </div>
                        
                        <div class="chart-box">
                            <div class="chart-header" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                <h3 style="margin: 0;">Notice Board</h3>
                                <button id="add-notice-btn" class="btn" style="padding: 6px 14px; font-size: 12px; height: auto; margin: 0; display: inline-block;" onclick="openCreateModal('notice')">+ Add Notice</button>
                            </div>
                            <div class="notice-list" id="notice-board-container" style="max-height: 200px; overflow-y: auto;">
                                <!-- Notices loaded dynamically -->
                            </div>
                        </div>
                    </section>

                    <section class="charts-row" style="margin-top: 30px;">
                        <div class="chart-box" style="flex: 1 1 100%;">
                            <div class="chart-header">
                                <h3>Pending Registration Approvals</h3>
                                <span style="font-size: 12px; color: var(--text-muted);">Quick system approval module</span>
                            </div>
                            <div class="table-container" style="margin-top: 15px; background: transparent; padding: 0; border: none; box-shadow: none;">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Name</th>
                                            <th>Role</th>
                                            <th>Date Requested</th>
                                            <th style="text-align: right;">Quick Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dashboard-pending-approvals-tbody">
                                        <tr><td colspan="6" style="text-align:center;color:var(--text-muted);">Loading pending accounts...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                `;
                container.innerHTML = dashboardHtml;
                loadDashboardPendingApprovals();
            } else if (role === 'school_principal') {
                dashboardHtml = `
                    <section class="cards-grid">
                        <div class="stat-card">
                            <div class="card-icon">🎓</div>
                            <div class="card-label">Active Student Body</div>
                            <div class="card-value" style="color:var(--accent-blue);">${cards.total_students}</div>
                        </div>
                        <div class="stat-card">
                            <div class="card-icon">👨‍🏫</div>
                            <div class="card-label">Total Faculty</div>
                            <div class="card-value" style="color:var(--accent-purple);">${cards.total_teachers}</div>
                        </div>
                        <div class="stat-card">
                            <div class="card-icon">🏫</div>
                            <div class="card-label">Active Classrooms</div>
                            <div class="card-value" style="color:var(--accent-emerald);">${cards.active_classes}</div>
                        </div>
                        <div class="stat-card">
                            <div class="card-icon">📝</div>
                            <div class="card-label">Upcoming Exams</div>
                            <div class="card-value" style="color:var(--accent-pink);">${cards.upcoming_exams}</div>
                        </div>
                    </section>
                    
                    <section class="charts-row">
                        <div class="chart-box">
                            <div class="chart-header">
                                <h3>School Exam Performance Index</h3>
                                <span style="font-size: 12px; color: var(--text-muted);">Average score percentages across exams</span>
                            </div>
                            <div class="chart-canvas" style="height:170px;">
                                ${drawExamBarChartSvg(charts.exam_performance, 'exam_name', 'average_percentage', 'purpleGradDynamic', 'var(--accent-purple)')}
                            </div>
                        </div>
                        
                        <div class="chart-box">
                            <div class="chart-header">
                                <h3 style="margin: 0;">Notice Board</h3>
                            </div>
                            <div class="notice-list" id="notice-board-container" style="max-height: 200px; overflow-y: auto;">
                                <!-- Notices loaded dynamically -->
                            </div>
                        </div>
                    </section>

                    <section class="charts-row" style="margin-top: 30px;">
                        <div class="chart-box" style="flex: 1;">
                            <div class="chart-header">
                                <h3>Principal's Operations Checklist</h3>
                                <span style="font-size: 12px; color: var(--text-muted);">Academic checklist tracking</span>
                            </div>
                            <div class="checklist-container">
                                <label class="checklist-item" id="p-item-1">
                                    <input type="checkbox" id="pr-chk-1" onchange="toggleDashboardChecklist(this, 'pr-chk-1')">
                                    <span>Approve monthly timetable drafts</span>
                                </label>
                                <label class="checklist-item" id="p-item-2">
                                    <input type="checkbox" id="pr-chk-2" onchange="toggleDashboardChecklist(this, 'pr-chk-2')">
                                    <span>Perform faculty evaluation walk-throughs</span>
                                </label>
                                <label class="checklist-item" id="p-item-3">
                                    <input type="checkbox" id="pr-chk-3" onchange="toggleDashboardChecklist(this, 'pr-chk-3')">
                                    <span>Coordinate parent-teacher association schedules</span>
                                </label>
                                <label class="checklist-item" id="p-item-4">
                                    <input type="checkbox" id="pr-chk-4" onchange="toggleDashboardChecklist(this, 'pr-chk-4')">
                                    <span>Review scholarship & fee-concession structures</span>
                                </label>
                            </div>
                        </div>
                        <div class="chart-box" style="flex: 1;">
                            <div class="chart-header">
                                <h3>Active Grade Classrooms</h3>
                                <span style="font-size: 12px; color: var(--text-muted);">Live school grades index</span>
                            </div>
                            <div style="margin-top: 15px; display:flex; flex-direction:column; gap:8px;" id="principal-classes-list">
                                <div class="homework-item" style="padding:10px;">
                                    <h5 style="margin:0; font-size:13px;">Grade 10 - Section A</h5>
                                    <div class="homework-meta" style="margin-top:5px;"><span>Class ID: #1</span><span>Supervised Class</span></div>
                                </div>
                                <div class="homework-item" style="padding:10px;">
                                    <h5 style="margin:0; font-size:13px;">Grade 11 - Section A</h5>
                                    <div class="homework-meta" style="margin-top:5px;"><span>Class ID: #2</span><span>Science & Tech stream</span></div>
                                </div>
                                <div class="homework-item" style="padding:10px;">
                                    <h5 style="margin:0; font-size:13px;">Grade 12 - Section A</h5>
                                    <div class="homework-meta" style="margin-top:5px;"><span>Class ID: #3</span><span>Commerce stream</span></div>
                                </div>
                            </div>
                        </div>
                    </section>
                `;
                container.innerHTML = dashboardHtml;
                loadChecklistState();
            } else if (role === 'school_teacher') {
                dashboardHtml = `
                    <section class="cards-grid">
                        <div class="stat-card">
                            <div class="card-icon">📚</div>
                            <div class="card-label">Assigned Subjects</div>
                            <div class="card-value" style="color:var(--accent-blue);">3</div>
                        </div>
                        <div class="stat-card">
                            <div class="card-icon">🎓</div>
                            <div class="card-label">Supervised Students</div>
                            <div class="card-value" style="color:var(--accent-purple);">${cards.total_students}</div>
                        </div>
                        <div class="stat-card">
                            <div class="card-icon">📅</div>
                            <div class="card-label">Present Today</div>
                            <div class="card-value" style="color:var(--accent-emerald);">${cards.today_attendance.Present || 0}</div>
                        </div>
                        <div class="stat-card">
                            <div class="card-icon">📝</div>
                            <div class="card-label">Upcoming Exams</div>
                            <div class="card-value" style="color:var(--accent-pink);">${cards.upcoming_exams}</div>
                        </div>
                    </section>
                    
                    <section class="charts-row">
                        <div class="chart-box">
                            <div class="chart-header">
                                <h3>Student Performance Indexes</h3>
                                <span style="font-size: 12px; color: var(--text-muted);">Exam averages in registered subjects</span>
                            </div>
                            <div class="chart-canvas" style="height:170px;">
                                ${drawExamBarChartSvg(charts.exam_performance, 'exam_name', 'average_percentage', 'emeraldGradDynamic', 'var(--accent-emerald)')}
                            </div>
                        </div>
                        
                        <div class="chart-box">
                            <div class="chart-header">
                                <h3 style="margin: 0;">Notice Board</h3>
                            </div>
                            <div class="notice-list" id="notice-board-container" style="max-height: 200px; overflow-y: auto;">
                                <!-- Notices loaded dynamically -->
                            </div>
                        </div>
                    </section>

                    <section class="charts-row" style="margin-top: 30px;">
                        <div class="chart-box" style="flex: 1.2;">
                            <div class="chart-header" style="display:flex; justify-content:space-between; align-items:center;">
                                <h3>Homework Assignments & Lesson Plans</h3>
                                <button class="btn" style="padding:6px 14px; font-size:12px; height:auto; margin:0;" onclick="toggleHomeworkForm()">+ Create Homework</button>
                            </div>
                            
                            <div id="teacher-homework-form-container" class="homework-item" style="display:none; margin-top:15px; border-color:var(--accent-blue);">
                                <h5 style="margin-bottom:12px; color:var(--accent-blue);">Post Homework Assignment</h5>
                                <form onsubmit="postTeacherHomework(event)" class="dashboard-form">
                                    <div class="form-group" style="margin-bottom:8px;">
                                        <label style="font-size:11px; margin-bottom:4px;">Assignment Title</label>
                                        <input type="text" id="th-title" class="form-input" style="padding:6px; font-size:12px;" placeholder="e.g. Chapter 4 Practice Exercises" required>
                                    </div>
                                    <div class="form-group" style="margin-bottom:8px;">
                                        <label style="font-size:11px; margin-bottom:4px;">Description / Instructions</label>
                                        <textarea id="th-desc" class="form-input" style="padding:6px; font-size:12px; min-height:60px;" placeholder="Solve questions 1-10 on page 42" required></textarea>
                                    </div>
                                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px;">
                                        <div class="form-group" style="margin-bottom:8px;">
                                            <label style="font-size:11px; margin-bottom:4px;">Classroom (Grade)</label>
                                            <select id="th-class" class="form-input" style="padding:6px; font-size:12px; background:#111827;" required>
                                                <option value="1">Grade 10</option>
                                                <option value="2">Grade 11</option>
                                                <option value="3">Grade 12</option>
                                            </select>
                                        </div>
                                        <div class="form-group" style="margin-bottom:8px;">
                                            <label style="font-size:11px; margin-bottom:4px;">Due Date</label>
                                            <input type="date" id="th-duedate" class="form-input" style="padding:6px; font-size:12px;" required>
                                        </div>
                                    </div>
                                    <div style="display:flex; gap:8px; justify-content:flex-end;">
                                        <button type="button" class="btn btn-secondary" style="padding:6px 12px; font-size:12px;" onclick="toggleHomeworkForm()">Cancel</button>
                                        <button type="submit" class="btn" style="padding:6px 12px; font-size:12px;">Publish</button>
                                    </div>
                                </form>
                            </div>

                            <div class="homework-list" id="dashboard-homework-list">
                                <div style="color:var(--text-muted); font-size:12px; text-align:center; padding:20px;">Fetching homework...</div>
                            </div>
                        </div>
                        
                        <div class="chart-box" style="flex: 0.8;">
                            <div class="chart-header">
                                <h3>Teacher Class Schedule</h3>
                                <span style="font-size: 12px; color: var(--text-muted);">Daily lecture slots</span>
                            </div>
                            <div class="homework-list">
                                <div class="homework-item" style="border-left: 3px solid var(--accent-blue);">
                                    <h5>09:00 AM - 10:00 AM</h5>
                                    <p style="margin: 0; font-size: 12px;">Mathematics (Grade 10 - Sec A)</p>
                                </div>
                                <div class="homework-item" style="border-left: 3px solid var(--accent-purple);">
                                    <h5>10:15 AM - 11:15 AM</h5>
                                    <p style="margin: 0; font-size: 12px;">Advanced Algebra (Grade 11 - Sec A)</p>
                                </div>
                                <div class="homework-item" style="border-left: 3px solid var(--accent-pink);">
                                    <h5>01:00 PM - 02:00 PM</h5>
                                    <p style="margin: 0; font-size: 12px;">Geometry Workshop (Grade 12 - Sec A)</p>
                                </div>
                            </div>
                        </div>
                    </section>
                `;
                container.innerHTML = dashboardHtml;
                loadDashboardHomeworkList();
            } else if (role === 'school_accountant') {
                const formattedCollected = '$' + Number(cards.monthly_fee_collection).toLocaleString();
                const formattedPending = '$' + Number(cards.pending_fees).toLocaleString();
                dashboardHtml = `
                    <section class="cards-grid">
                        <div class="stat-card">
                            <div class="card-icon">💵</div>
                            <div class="card-label">Monthly Collected Fees</div>
                            <div class="card-value" style="color:var(--accent-emerald);">${formattedCollected}</div>
                        </div>
                        <div class="stat-card">
                            <div class="card-icon">💰</div>
                            <div class="card-label">Outstanding Receivables</div>
                            <div class="card-value" style="color:var(--accent-pink);">${formattedPending}</div>
                        </div>
                        <div class="stat-card">
                            <div class="card-icon">👨‍👩‍👧</div>
                            <div class="card-label">Parents Registry</div>
                            <div class="card-value" style="color:var(--accent-blue);">${cards.total_parents}</div>
                        </div>
                        <div class="stat-card">
                            <div class="card-icon">🏫</div>
                            <div class="card-label">Billing Classrooms</div>
                            <div class="card-value" style="color:var(--accent-purple);">${cards.active_classes}</div>
                        </div>
                    </section>
                    
                    <section class="charts-row">
                        <div class="chart-box">
                            <div class="chart-header">
                                <h3>Monthly Fee Collections Trend</h3>
                                <span style="font-size: 12px; color: var(--text-muted);">Collections ledger history</span>
                            </div>
                            <div class="chart-canvas" style="height:170px;">
                                ${drawLineChartSvg(charts.fee_trends, 'month', 'collected', 'emeraldGradDynamic', 'var(--accent-emerald)')}
                            </div>
                        </div>
                        
                        <div class="chart-box">
                            <div class="chart-header">
                                <h3 style="margin: 0;">Notice Board</h3>
                            </div>
                            <div class="notice-list" id="notice-board-container" style="max-height: 200px; overflow-y: auto;">
                                <!-- Notices loaded dynamically -->
                            </div>
                        </div>
                    </section>

                    <section class="charts-row" style="margin-top: 30px;">
                        <div class="chart-box" style="flex: 1.2;">
                            <div class="chart-header">
                                <h3>Recent Transactions Overview</h3>
                                <span style="font-size: 12px; color: var(--text-muted);">Recent billing collections</span>
                            </div>
                            <div style="margin-top: 15px; display:flex; flex-direction:column; gap:8px;" id="accountant-recent-payments">
                                <div class="homework-item" style="padding:10px; display:flex; justify-content:space-between; align-items:center;">
                                    <div>
                                        <h5 style="margin:0; font-size:13px;">John Doe (Grade 10)</h5>
                                        <div class="homework-meta" style="margin-top:3px;"><span>Receipt ID: #RE1029</span><span>Paid via CASH</span></div>
                                    </div>
                                    <div style="color:var(--accent-emerald); font-weight:700; font-size:14px;">$250.00</div>
                                </div>
                                <div class="homework-item" style="padding:10px; display:flex; justify-content:space-between; align-items:center;">
                                    <div>
                                        <h5 style="margin:0; font-size:13px;">Sarah Jenkins (Grade 11)</h5>
                                        <div class="homework-meta" style="margin-top:3px;"><span>Receipt ID: #RE1030</span><span>Paid via CARD</span></div>
                                    </div>
                                    <div style="color:var(--accent-emerald); font-weight:700; font-size:14px;">$350.00</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="chart-box" style="flex: 0.8;">
                            <div class="chart-header">
                                <h3>Accountant's Financial Checklist</h3>
                                <span style="font-size: 12px; color: var(--text-muted);">Audit tracking</span>
                            </div>
                            <div class="checklist-container">
                                <label class="checklist-item" id="ac-item-1">
                                    <input type="checkbox" id="ac-chk-1" onchange="toggleDashboardChecklist(this, 'ac-chk-1')">
                                    <span>Reconcile monthly bank statement</span>
                                </label>
                                <label class="checklist-item" id="ac-item-2">
                                    <input type="checkbox" id="ac-chk-2" onchange="toggleDashboardChecklist(this, 'ac-chk-2')">
                                    <span>Verify staff payroll & disbursements</span>
                                </label>
                                <label class="checklist-item" id="ac-item-3">
                                    <input type="checkbox" id="ac-chk-3" onchange="toggleDashboardChecklist(this, 'ac-chk-3')">
                                    <span>Dispatch fee overdue alerts to parents</span>
                                </label>
                            </div>
                        </div>
                    </section>
                `;
                container.innerHTML = dashboardHtml;
                loadChecklistState();
            } else if (role === 'school_student') {
                const formattedPending = cards.pending_fees > 0 ? '$' + Number(cards.pending_fees).toLocaleString() : '$0';
                dashboardHtml = `
                    <section class="cards-grid">
                        <div class="stat-card">
                            <div class="card-icon">📅</div>
                            <div class="card-label">My Attendance Rate</div>
                            <div class="card-value" style="color:var(--accent-blue);">95.4%</div>
                        </div>
                        <div class="stat-card">
                            <div class="card-icon">💵</div>
                            <div class="card-label">Outstanding Fees</div>
                            <div class="card-value" style="color:var(--accent-pink);">${formattedPending}</div>
                        </div>
                        <div class="stat-card">
                            <div class="card-icon">🏆</div>
                            <div class="card-label">My Performance Average</div>
                            <div class="card-value" style="color:var(--accent-purple);">84.2%</div>
                        </div>
                        <div class="stat-card">
                            <div class="card-icon">📚</div>
                            <div class="card-label">Library Books Checked Out</div>
                            <div class="card-value" style="color:var(--accent-emerald);">3</div>
                        </div>
                    </section>
                    
                    <section class="charts-row">
                        <div class="chart-box">
                            <div class="chart-header">
                                <h3>My Term Exam Performance</h3>
                                <span style="font-size: 12px; color: var(--text-muted);">Scores in last term exams</span>
                            </div>
                            <div class="chart-canvas" style="height:170px;">
                                ${drawExamBarChartSvg(charts.exam_performance, 'exam_name', 'average_percentage', 'blueGradDynamic', 'var(--accent-blue)')}
                            </div>
                        </div>
                        
                        <div class="chart-box">
                            <div class="chart-header">
                                <h3 style="margin: 0;">Notice Board</h3>
                            </div>
                            <div class="notice-list" id="notice-board-container" style="max-height: 200px; overflow-y: auto;">
                                <!-- Notices loaded dynamically -->
                            </div>
                        </div>
                    </section>

                    <section class="charts-row" style="margin-top: 30px;">
                        <div class="chart-box" style="flex: 1.2;">
                            <div class="chart-header">
                                <h3>My Homework & Assignments</h3>
                                <span style="font-size: 12px; color: var(--text-muted);">Assigned homework checklist</span>
                            </div>
                            <div class="homework-list" id="dashboard-homework-list">
                                <div style="color:var(--text-muted); font-size:12px; text-align:center; padding:20px;">Loading assignments...</div>
                            </div>
                        </div>
                        
                        <div class="chart-box" style="flex: 0.8;">
                            <div class="chart-header">
                                <h3>My Daily Period Schedule</h3>
                                <span style="font-size: 12px; color: var(--text-muted);">Class timetable today</span>
                            </div>
                            <div class="homework-list">
                                <div class="homework-item" style="border-left: 3px solid var(--accent-blue);">
                                    <h5>09:00 AM - 10:00 AM</h5>
                                    <p style="margin: 0; font-size: 12px;">Mathematics (Grade 10 - Room 102)</p>
                                </div>
                                <div class="homework-item" style="border-left: 3px solid var(--accent-purple);">
                                    <h5>10:15 AM - 11:15 AM</h5>
                                    <p style="margin: 0; font-size: 12px;">Physics Laboratory (Room 205)</p>
                                </div>
                                <div class="homework-item" style="border-left: 3px solid var(--accent-pink);">
                                    <h5>11:30 AM - 12:30 PM</h5>
                                    <p style="margin: 0; font-size: 12px;">History & Social Studies (Room 101)</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Student Submission Overlay -->
                    <div id="student-submission-modal" class="modal-overlay" style="display:none;">
                        <div class="modal-card" style="max-width:400px;">
                            <button class="modal-close" onclick="closeSubmissionModal()">✖</button>
                            <h3 style="font-size:18px; margin-bottom:15px; color:var(--accent-blue);">Submit Homework</h3>
                            <form onsubmit="submitStudentHomework(event)" class="dashboard-form">
                                <input type="hidden" id="sub-hw-id">
                                <div class="form-group">
                                    <label>Attachment URL / File link</label>
                                    <input type="text" id="sub-file-url" class="form-input" placeholder="e.g. Google Drive link or file URL" required>
                                </div>
                                <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:10px;">
                                    <button type="button" class="btn btn-secondary" onclick="closeSubmissionModal()">Cancel</button>
                                    <button type="submit" class="btn">Submit Work</button>
                                </div>
                            </form>
                        </div>
                    </div>
                `;
                container.innerHTML = dashboardHtml;
                loadDashboardHomeworkList();
            } else if (role === 'school_parent') {
                const formattedPending = '$' + Number(cards.pending_fees).toLocaleString();
                dashboardHtml = `
                    <section class="cards-grid">
                        <div class="stat-card">
                            <div class="card-icon">📅</div>
                            <div class="card-label">Child's Attendance Rate</div>
                            <div class="card-value" style="color:var(--accent-blue);">96.2%</div>
                        </div>
                        <div class="stat-card">
                            <div class="card-icon">💵</div>
                            <div class="card-label">Outstanding Fees</div>
                            <div class="card-value" style="color:var(--accent-pink);">${formattedPending}</div>
                        </div>
                        <div class="stat-card">
                            <div class="card-icon">🏆</div>
                            <div class="card-label">Child's Exam Average</div>
                            <div class="card-value" style="color:var(--accent-purple);">88.5%</div>
                        </div>
                        <div class="stat-card">
                            <div class="card-icon">📚</div>
                            <div class="card-label">Library Books Borrowed</div>
                            <div class="card-value" style="color:var(--accent-emerald);">2</div>
                        </div>
                    </section>
                    
                    <section class="charts-row">
                        <div class="chart-box">
                            <div class="chart-header">
                                <h3>Child's Exam Progress</h3>
                                <span style="font-size: 12px; color: var(--text-muted);">Student terminal score metrics</span>
                            </div>
                            <div class="chart-canvas" style="height:170px;">
                                ${drawExamBarChartSvg(charts.exam_performance, 'exam_name', 'average_percentage', 'pinkGradDynamic', 'var(--accent-pink)')}
                            </div>
                        </div>
                        
                        <div class="chart-box">
                            <div class="chart-header">
                                <h3 style="margin: 0;">Notice Board</h3>
                            </div>
                            <div class="notice-list" id="notice-board-container" style="max-height: 200px; overflow-y: auto;">
                                <!-- Notices loaded dynamically -->
                            </div>
                        </div>
                    </section>

                    <section class="charts-row" style="margin-top: 30px;">
                        <div class="chart-box" style="flex: 1.1;">
                            <div class="chart-header">
                                <h3>Child's Daily Subject Timetable</h3>
                                <span style="font-size: 12px; color: var(--text-muted);">Period slots today</span>
                            </div>
                            <div class="homework-list" style="margin-top:15px;">
                                <div class="homework-item" style="border-left: 3px solid var(--accent-blue);">
                                    <h5>09:00 AM - 10:00 AM</h5>
                                    <p style="margin: 0; font-size: 12px;">Mathematics (Grade 10 - Dr. Robert Carter)</p>
                                </div>
                                <div class="homework-item" style="border-left: 3px solid var(--accent-purple);">
                                    <h5>10:15 AM - 11:15 AM</h5>
                                    <p style="margin: 0; font-size: 12px;">Physics Laboratory (Room 205)</p>
                                </div>
                                <div class="homework-item" style="border-left: 3px solid var(--accent-pink);">
                                    <h5>11:30 AM - 12:30 PM</h5>
                                    <p style="margin: 0; font-size: 12px;">History & Social Studies (Room 101)</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="chart-box" style="flex: 0.9;">
                            <div class="chart-header">
                                <h3>Teacher Directory & Support</h3>
                                <span style="font-size: 12px; color: var(--text-muted);">Quick advisor contacts</span>
                            </div>
                            <div class="homework-list" id="parent-teacher-directory">
                                <div class="homework-item">
                                    <h5 style="margin-bottom:4px;">Dr. Robert Carter</h5>
                                    <p style="margin: 0; font-size: 11px; color:var(--text-muted);">Class Tutor / Math Dept</p>
                                    <div style="font-size:11px; color:var(--accent-blue); margin-top:6px;">robert.carter@school.erp</div>
                                </div>
                                <div class="homework-item">
                                    <h5 style="margin-bottom:4px;">School Support Helpdesk</h5>
                                    <p style="margin: 0; font-size: 11px; color:var(--text-muted);">Administration / Operations</p>
                                    <div style="font-size:11px; color:var(--accent-blue); margin-top:6px;">support@school.erp</div>
                                </div>
                            </div>
                        </div>
                    </section>
                `;
                container.innerHTML = dashboardHtml;
            }

            loadNotices();
        }

        // Load dashboard stats
        function loadDashboardData() {
            fetch(`${API_URL}/dashboard`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                renderRoleDashboard(currentUser.role, body.data);
            })
            .catch(() => {});
        }

        // Fetch notices from the server and display them
        function loadNotices() {
            fetch(`${API_URL}/notices`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const container = document.getElementById('notice-board-container');
                if (!container) return;
                container.innerHTML = '';
                if (!body.data || !body.data.data || body.data.data.length === 0) {
                    container.innerHTML = '<div style="color:var(--text-muted);font-size:13px;text-align:center;padding:20px;">No notices posted yet.</div>';
                    return;
                }
                
                // Colors list for borders
                const colors = ['var(--accent-blue)', 'var(--accent-pink)', 'var(--accent-emerald)', '#f59e0b'];

                body.data.data.forEach((notice, idx) => {
                    const div = document.createElement('div');
                    div.className = 'notice-item';
                    div.style.borderLeftColor = colors[idx % colors.length];
                    
                    const dateStr = notice.created_at ? new Date(notice.created_at).toLocaleDateString() : 'Just now';
                    
                    div.innerHTML = `
                        <h5>${notice.title}</h5>
                        <p>${notice.description || ''}</p>
                        <div class="notice-date">${dateStr}</div>
                    `;
                    container.appendChild(div);
                });
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
                const hideActions = !['administrator', 'school_super_admin', 'school_principal'].includes(currentUser.role);
                body.data.data.forEach(student => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${student.admission_no}</td>
                        <td>${student.roll_no || '-'}</td>
                        <td>${student.first_name} ${student.last_name}</td>
                        <td>${student.email || '-'}</td>
                        <td>${student.mobile || '-'}</td>
                        <td><span class="badge active">${student.status}</span></td>
                        <td class="actions-header-column" style="display: ${hideActions ? 'none' : 'table-cell'};">
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
                const hideActions = !['administrator', 'school_super_admin', 'school_principal'].includes(currentUser.role);
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
                        <td class="actions-header-column" style="display: ${hideActions ? 'none' : 'table-cell'};">
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
            } else if (type === 'notice') {
                title.innerText = "Post Notice to Board";
                fields.innerHTML = `
                    <div class="form-group">
                        <label>Notice Title</label>
                        <input type="text" id="n-title" class="form-input" placeholder="e.g. Summer Vacation Announcement" required>
                    </div>
                    <div class="form-group">
                        <label>Details / Description</label>
                        <textarea id="n-desc" class="form-input" placeholder="Enter notice details here..." style="min-height: 100px; resize: vertical;" required></textarea>
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
            } else if (type === 'notice') {
                const body = {
                    title: document.getElementById('n-title').value,
                    description: document.getElementById('n-desc').value
                };

                fetch(`${API_URL}/notices`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${authToken}`
                    },
                    body: JSON.stringify(body)
                })
                .then(res => {
                    if (res.ok) {
                        toast('Notice posted to board successfully!', 'success');
                        closeCrudModal();
                        loadNotices();
                    } else {
                        toast('Failed to post notice.', 'error');
                    }
                })
                .catch(() => toast('Server error.', 'error'));
            }
        }

        // Load approvals list for Super Admin
        function loadApprovals() {
            fetch(`${API_URL}/auth/users`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => {
                if (!res.ok) throw new Error('Failed to load user list.');
                return res.json();
            })
            .then(body => {
                const tbody = document.getElementById('approvals-table-body');
                tbody.innerHTML = '';
                if (!body.data || body.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;">No users registered yet.</td></tr>';
                    return;
                }
                body.data.forEach(user => {
                    const tr = document.createElement('tr');
                    
                    // Style badges based on user status
                    let badgeStyle = "padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block;";
                    if (user.status === 'APPROVED') {
                        badgeStyle += " background: rgba(16, 185, 129, 0.15); color: var(--accent-emerald);";
                    } else if (user.status === 'PENDING') {
                        badgeStyle += " background: rgba(59, 130, 246, 0.15); color: var(--accent-blue);";
                    } else if (user.status === 'HOLD') {
                        badgeStyle += " background: rgba(245, 158, 11, 0.15); color: #f59e0b;";
                    } else if (user.status === 'BLOCKED') {
                        badgeStyle += " background: rgba(239, 68, 68, 0.15); color: var(--accent-pink);";
                    }

                    const isSelf = user.id === currentUser.id;
                    const actionButtons = isSelf ? `<em>Current User</em>` : `
                        <button class="action-icon-btn" onclick="changeApprovalStatus(${user.id}, 'APPROVED')" title="Approve" style="border-color: var(--accent-emerald); color: var(--accent-emerald);">✓</button>
                        <button class="action-icon-btn" onclick="changeApprovalStatus(${user.id}, 'HOLD')" title="Hold" style="border-color: #f59e0b; color: #f59e0b;">⏳</button>
                        <button class="action-icon-btn" onclick="changeApprovalStatus(${user.id}, 'BLOCKED')" title="Block" style="border-color: var(--accent-pink); color: var(--accent-pink);">🚫</button>
                        <button class="action-icon-btn" onclick="deleteUserRecord(${user.id})" title="Delete" style="border-color: #ef4444; color: #ef4444;">🗑</button>
                    `;

                    // Format role name nicely
                    const roleName = user.role.replace('school_', '').replace('_', ' ').toUpperCase();

                    tr.innerHTML = `
                        <td>${user.username}</td>
                        <td>${user.email || '-'}</td>
                        <td>${user.name}</td>
                        <td>${roleName}</td>
                        <td><span style="${badgeStyle}">${user.status}</span></td>
                        <td>${user.registered_at ? new Date(user.registered_at).toLocaleDateString() : '-'}</td>
                        <td style="text-align: right;">
                            ${actionButtons}
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(err => {
                toast(err.message, 'error');
            });
        }

        // Change status of a registered user
        function changeApprovalStatus(userId, status) {
            fetch(`${API_URL}/auth/users/status`, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}`
                },
                body: JSON.stringify({ user_id: userId, status: status })
            })
            .then(res => {
                if (!res.ok) return res.json().then(b => { throw new Error(b.message || 'Failed to update user status'); });
                return res.json();
            })
            .then(body => {
                toast(`User status updated to ${status}!`, 'success');
                loadApprovals();
            })
            .catch(err => {
                toast(err.message, 'error');
            });
        }

        // Permanently delete a registered user
        function deleteUserRecord(userId) {
            if (!confirm('Are you sure you want to permanently delete this user?')) return;
            fetch(`${API_URL}/auth/users/${userId}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => {
                if (!res.ok) return res.json().then(b => { throw new Error(b.message || 'Failed to delete user'); });
                return res.json();
            })
            .then(body => {
                toast('User account permanently deleted!', 'success');
                loadApprovals();
            })
            .catch(err => {
                toast(err.message, 'error');
            });
        }

        // Fetch and load SMTP settings into the dashboard form
        function loadSmtpSettings() {
            fetch(`${API_URL}/auth/smtp`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => {
                if (!res.ok) throw new Error('Failed to load SMTP settings.');
                return res.json();
            })
            .then(body => {
                const data = body.data;
                document.getElementById('smtp-host').value = data.host || '';
                document.getElementById('smtp-port').value = data.port || '587';
                document.getElementById('smtp-secure').value = data.secure || 'tls';
                document.getElementById('smtp-auth').value = data.auth || 'yes';
                document.getElementById('smtp-username').value = data.username || '';
                document.getElementById('smtp-password').value = data.password || '';
                document.getElementById('smtp-from-email').value = data.from_email || 'rameshseervi242628@gmail.com';
                document.getElementById('smtp-from-name').value = data.from_name || 'Global School ERP';
            })
            .catch(err => {
                toast(err.message, 'error');
            });
        }

        // Save custom SMTP settings via REST API
        function saveSmtpSettings(e) {
            e.preventDefault();
            const btn = document.getElementById('smtp-submit-btn');
            btn.disabled = true;
            btn.innerText = 'Saving...';

            const payload = {
                host: document.getElementById('smtp-host').value,
                port: document.getElementById('smtp-port').value,
                secure: document.getElementById('smtp-secure').value,
                auth: document.getElementById('smtp-auth').value,
                username: document.getElementById('smtp-username').value,
                password: document.getElementById('smtp-password').value,
                from_email: document.getElementById('smtp-from-email').value,
                from_name: document.getElementById('smtp-from-name').value
            };

            fetch(`${API_URL}/auth/smtp`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}`
                },
                body: JSON.stringify(payload)
            })
            .then(res => {
                if (!res.ok) return res.json().then(b => { throw new Error(b.message || 'Failed to save settings'); });
                return res.json();
            })
            .then(body => {
                toast('SMTP Configuration saved successfully!', 'success');
                btn.disabled = false;
                btn.innerText = 'Save SMTP Settings';
            })
            .catch(err => {
                toast(err.message, 'error');
                btn.disabled = false;
                btn.innerText = 'Save SMTP Settings';
            });
        }
    </script>
</body>
</html>
