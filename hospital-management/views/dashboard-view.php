<?php
/**
 * Hospital ERP Dashboard View Template
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
    <title>Global Hospital ERP - Dashboard</title>
    <!-- Modern Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #090d16;
            --card-bg: rgba(17, 24, 39, 0.7);
            --glass-border: rgba(255, 255, 255, 0.06);
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            
            --accent-blue: #3b82f6;
            --accent-purple: #8b5cf6;
            --accent-pink: #ec4899;
            --accent-green: #10b981;
            --accent-yellow: #f59e0b;
            --accent-teal: #14b8a6;
            
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* Ambient Glow Backgrounds */
        .ambient-glow {
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.15) 0%, rgba(9, 13, 22, 0) 70%);
            top: -100px;
            right: -100px;
            z-index: -1;
            pointer-events: none;
        }

        /* 1. AUTH LOGIN & REGISTER CONTAINER */
        .auth-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
        }

        .auth-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            transition: var(--transition-smooth);
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-logo h2 {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--text-main), var(--accent-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .auth-logo p {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 12px 16px;
            color: var(--text-main);
            font-family: inherit;
            font-size: 14px;
            transition: var(--transition-smooth);
            outline: none;
        }

        .form-input:focus {
            border-color: var(--accent-purple);
            box-shadow: 0 0 10px rgba(139, 92, 246, 0.2);
            background: rgba(255, 255, 255, 0.05);
        }

        .auth-submit-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            border: none;
            border-radius: 12px;
            padding: 14px;
            color: #fff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-smooth);
            margin-top: 10px;
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.2);
        }

        .auth-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(59, 130, 246, 0.3);
        }

        .auth-submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .demo-credentials-box {
            background: rgba(255, 255, 255, 0.02);
            border: 1px dashed var(--glass-border);
            border-radius: 16px;
            padding: 15px;
            margin-bottom: 25px;
        }

        .demo-roles-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-top: 10px;
        }

        .demo-role-btn {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            padding: 8px;
            color: var(--text-main);
            font-family: inherit;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            text-align: center;
            transition: var(--transition-smooth);
        }

        .demo-role-btn:hover {
            background: rgba(139, 92, 246, 0.1);
            border-color: var(--accent-purple);
        }

        .demo-role-title {
            display: block;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .demo-role-user {
            color: var(--text-muted);
            font-size: 9px;
        }

        .auth-toggle-tip {
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 20px;
        }

        .auth-toggle-tip a {
            color: var(--accent-blue);
            text-decoration: none;
            font-weight: 500;
        }

        /* 2. MAIN APP SCREEN */
        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Navigation */
        .sidebar {
            width: 280px;
            background: rgba(10, 15, 30, 0.9);
            border-right: 1px solid var(--glass-border);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 30px 20px;
            flex-shrink: 0;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent-pink), var(--accent-purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            color: #fff;
        }

        .brand span {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.5px;
            background: linear-gradient(to right, #ffffff, #d1d5db);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .menu-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .menu-item {
            width: 100%;
            background: transparent;
            border: none;
            border-radius: 12px;
            padding: 12px 16px;
            color: var(--text-muted);
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: var(--transition-smooth);
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(139, 92, 246, 0.08);
            color: var(--text-main);
            border-left: 3px solid var(--accent-purple);
            padding-left: 13px;
        }

        .user-profile-wrapper {
            margin-top: auto;
            border-top: 1px solid var(--glass-border);
            padding-top: 20px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.02);
            padding: 12px;
            border-radius: 16px;
            margin-bottom: 15px;
        }

        .user-profile-inner {
            display: flex;
            align-items: center;
            gap: 10px;
            overflow: hidden;
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--accent-purple);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            flex-shrink: 0;
        }

        .user-info {
            overflow: hidden;
        }

        .user-info h4 {
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-info p {
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        .logout-btn {
            width: 100%;
            background: rgba(239, 68, 68, 0.05);
            border: 1px solid rgba(239, 68, 68, 0.15);
            border-radius: 12px;
            padding: 10px;
            color: #ef4444;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: var(--transition-smooth);
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.15);
        }

        /* Main Workspace Panel */
        .main-panel {
            flex-grow: 1;
            padding: 40px;
            overflow-y: auto;
            max-height: 100vh;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }

        .title-group h1 {
            font-size: 26px;
            font-weight: 700;
        }

        .title-group p {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 4px;
        }

        .badge-live {
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--accent-green);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent-green);
            box-shadow: 0 0 8px var(--accent-green);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.9); opacity: 0.6; }
            50% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.6; }
        }

        /* 3. DASHBOARD STATS CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: var(--transition-smooth);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .stat-details h3 {
            font-size: 28px;
            font-weight: 700;
            margin-top: 4px;
        }

        .stat-details p {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        /* Chart Trends / Analytics Section */
        .charts-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 24px;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-header h3 {
            font-size: 16px;
            font-weight: 600;
        }

        /* Simulated Animated Charts */
        .simulated-bar-chart {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            height: 200px;
            padding: 10px 0;
            border-bottom: 1px solid var(--glass-border);
        }

        .bar-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-grow: 1;
        }

        .chart-bar {
            width: 32px;
            border-radius: 6px 6px 0 0;
            background: linear-gradient(to top, var(--accent-purple), var(--accent-blue));
            transition: height 1s ease;
            position: relative;
        }

        .chart-bar::after {
            content: attr(data-value);
            position: absolute;
            top: -22px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 10px;
            font-weight: 600;
            color: var(--text-main);
        }

        .bar-label {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 8px;
            text-align: center;
        }

        /* List styling & general grids */
        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }

        .table-container {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .table-header-row {
            padding: 20px 24px;
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header-row h3 {
            font-size: 16px;
            font-weight: 600;
        }

        .table-controls {
            display: flex;
            gap: 10px;
        }

        .btn {
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            border: none;
            border-radius: 10px;
            padding: 8px 16px;
            color: #fff;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-smooth);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .data-table th {
            padding: 16px 24px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--glass-border);
        }

        .data-table td {
            padding: 16px 24px;
            font-size: 14px;
            border-bottom: 1px solid var(--glass-border);
            color: var(--text-main);
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover td {
            background: rgba(255, 255, 255, 0.01);
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-active, .badge-paid, .badge-approved {
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--accent-green);
        }

        .badge-pending {
            background: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 158, 11, 0.2);
            color: var(--accent-yellow);
        }

        .badge-cancelled, .badge-blocked {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        /* 4. MODALS CONTAINER & CRUDS SHEET */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 100;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-card {
            background: var(--bg-dark);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 35px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            position: relative;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }

        .modal-overlay.show .modal-card {
            transform: translateY(0);
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            color: var(--text-muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-smooth);
        }

        .modal-close:hover {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.08);
        }

        /* Toast Popup Alerts */
        .toast-box {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 200;
        }

        .toast {
            background: #111827;
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 16px 20px;
            color: var(--text-main);
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast.success {
            border-left: 4px solid var(--accent-green);
        }

        .toast.error {
            border-left: 4px solid #ef4444;
        }
    </style>
</head>
<body>
    <div class="ambient-glow"></div>
    <div class="toast-box" id="toast-box"></div>

    <!-- 1. AUTH SCREEN -->
    <div class="auth-container" id="auth-screen">
        <div class="auth-card">
            <div class="auth-logo">
                <h2>Global Hospital ERP</h2>
                <p>Advanced Clinical Portal & Management System</p>
            </div>
            
            <div class="demo-credentials-box" id="credentials-helper-container">
                <h5 style="font-size: 12px; color: var(--accent-blue); font-weight:600; margin-bottom: 8px;">Demo Fast Login Credentials</h5>
                <div class="demo-roles-grid">
                    <button class="demo-role-btn" onclick="prefillUser('hospitalsuperadmin', '123456')">
                        <span class="demo-role-title">Admin</span>
                        <span class="demo-role-user">hospitalsuperadmin</span>
                    </button>
                    <button class="demo-role-btn" onclick="prefillUser('hospital_doctor', 'doctorpass123')">
                        <span class="demo-role-title">Doctor</span>
                        <span class="demo-role-user">hospital_doctor</span>
                    </button>
                    <button class="demo-role-btn" onclick="prefillUser('hospital_receptionist', 'receptionistpass123')">
                        <span class="demo-role-title">Receptionist</span>
                        <span class="demo-role-user">receptionist</span>
                    </button>
                    <button class="demo-role-btn" onclick="prefillUser('hospital_pharmacist', 'pharmacistpass123')">
                        <span class="demo-role-title">Pharmacist</span>
                        <span class="demo-role-user">pharmacist</span>
                    </button>
                    <button class="demo-role-btn" onclick="prefillUser('hospital_lab_technician', 'labpass123')">
                        <span class="demo-role-title">Lab Tech</span>
                        <span class="demo-role-user">labtech</span>
                    </button>
                    <button class="demo-role-btn" onclick="prefillUser('hospital_patient', 'patientpass123')">
                        <span class="demo-role-title">Patient</span>
                        <span class="demo-role-user">patient</span>
                    </button>
                </div>
            </div>

            <form id="login-form" onsubmit="handleUserLogin(event)">
                <div class="form-group">
                    <label>Username / Email</label>
                    <input type="text" id="username" class="form-input" placeholder="Select role above or type..." oninput="checkLoginType()" required>
                </div>
                <div class="form-group" id="login-pass-group" style="display: none;">
                    <label>Password</label>
                    <input type="password" id="password" class="form-input" placeholder="••••••••">
                </div>
                <div class="form-group" id="login-otp-group" style="display: none;">
                    <label>6-Digit Login Verification Code</label>
                    <input type="text" id="login-otp" class="form-input" placeholder="e.g. 6-digit OTP code" maxlength="6">
                </div>
                <button type="submit" id="login-submit-btn" class="auth-submit-btn">Login</button>
                <p class="auth-toggle-tip">
                    Register a new patient? <a href="#" onclick="showRegister(event)">Click here</a>
                </p>
            </form>

            <form id="register-form" style="display: none;" onsubmit="handleUserRegister(event)">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" id="reg-username" class="form-input" placeholder="e.g. amitser" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="reg-email" class="form-input" placeholder="e.g. amit@gmail.com" required>
                </div>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" id="reg-name" class="form-input" placeholder="e.g. Amit Patel" required>
                </div>
                <div class="form-group">
                    <label>Account Role Type</label>
                    <select id="reg-role" class="form-input" style="background: #111827;" required>
                        <option value="hospital_patient">Patient</option>
                        <option value="hospital_doctor">Doctor</option>
                        <option value="hospital_receptionist">Receptionist</option>
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

    <!-- 2. MAIN APP CONTENT CONTAINER -->
    <div class="app-container" id="app-screen" style="display: none;">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div>
                <div class="brand">
                    <div class="brand-icon">H</div>
                    <span>Global Hospital ERP</span>
                </div>
                <ul class="menu-list" id="sidebar-menu-list">
                    <li id="menu-dashboard"><button class="menu-item active" onclick="switchTab('dashboard')">Dashboard</button></li>
                    <li id="menu-patients"><button class="menu-item" onclick="switchTab('patients')">Patients Registry</button></li>
                    <li id="menu-doctors"><button class="menu-item" onclick="switchTab('doctors')">Doctors Catalog</button></li>
                    <li id="menu-appointments"><button class="menu-item" onclick="switchTab('appointments')">Appointments</button></li>
                    <li id="menu-opd"><button class="menu-item" onclick="switchTab('opd')">OPD Visits</button></li>
                    <li id="menu-ipd"><button class="menu-item" onclick="switchTab('ipd')">IPD Admissions</button></li>
                    <li id="menu-billing"><button class="menu-item" onclick="switchTab('billing')">Billing & Invoices</button></li>
                    <li id="menu-pharmacy"><button class="menu-item" onclick="switchTab('pharmacy')">Pharmacy Catalog</button></li>
                    <li id="menu-laboratory"><button class="menu-item" onclick="switchTab('laboratory')">Lab Reports</button></li>
                    <li id="menu-medical"><button class="menu-item" onclick="switchTab('medical')">My Electronic Records</button></li>
                    <li id="menu-approvals"><button class="menu-item" onclick="switchTab('approvals')">User Approvals</button></li>
                    <li id="menu-apidocs"><button class="menu-item" onclick="switchTab('apidocs')">Portal APIs Docs</button></li>
                </ul>
            </div>
            
            <div class="user-profile-wrapper">
                <div class="user-profile">
                    <div class="user-profile-inner">
                        <div class="avatar" id="profile-avatar">AD</div>
                        <div class="user-info">
                            <h4 id="profile-name">Admin Portal</h4>
                            <p id="profile-role">Super Admin</p>
                        </div>
                    </div>
                </div>
                <button class="logout-btn" onclick="executeLogout()">Sign Out</button>
            </div>
        </aside>

        <!-- Main Workspace -->
        <main class="main-panel">
            <header class="header-section">
                <div class="title-group">
                    <h1 id="tab-title-header">Hospital Dashboard</h1>
                    <p id="tab-subtitle-header">Overview of clinical statistics and appointments status</p>
                </div>
                <div class="badge-live">
                    <span class="live-dot"></span> Live Hospital Server
                </div>
            </header>

            <!-- TAB 1: DASHBOARD CONTENT -->
            <div class="tab-panel active" id="tab-dashboard">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-details">
                            <h3 id="stat-patients">0</h3>
                            <p>Total Patients</p>
                        </div>
                        <div class="stat-icon" style="background: rgba(59, 130, 246, 0.08); color: var(--accent-blue);">👥</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-details">
                            <h3 id="stat-appointments">0</h3>
                            <p>Today's Appointments</p>
                        </div>
                        <div class="stat-icon" style="background: rgba(139, 92, 246, 0.08); color: var(--accent-purple);">📅</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-details">
                            <h3 id="stat-opd">0</h3>
                            <p>OPD visits</p>
                        </div>
                        <div class="stat-icon" style="background: rgba(20, 184, 166, 0.08); color: var(--accent-teal);">🩺</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-details">
                            <h3 id="stat-revenue">₹0</h3>
                            <p>Today's Revenue</p>
                        </div>
                        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.08); color: var(--accent-green);">💰</div>
                    </div>
                </div>

                <div class="charts-row">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Revenue Trends (Last 6 Months)</h3>
                        </div>
                        <div class="simulated-bar-chart" id="revenue-chart-bars">
                            <!-- Injected bars -->
                        </div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Bed Occupancy (IPD Wards)</h3>
                        </div>
                        <div id="ward-distribution-data" style="display:flex; flex-direction:column; gap:12px; margin-top:20px;">
                            <!-- Ward levels -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: PATIENTS REGISTRY -->
            <div class="tab-panel" id="tab-patients">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Patient Registry</h3>
                        <div class="table-controls" id="patient-actions-wrapper">
                            <button class="btn" onclick="openCreateModal('patient')">+ Register Patient</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Patient Code</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Mobile</th>
                                <th>Blood Group</th>
                                <th>Status</th>
                                <th class="actions-header-column">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="patients-table-body">
                            <!-- Rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: DOCTORS CATALOG -->
            <div class="tab-panel" id="tab-doctors">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Doctors Directory</h3>
                        <div class="table-controls" id="doctor-actions-wrapper">
                            <button class="btn" onclick="openCreateModal('doctor')">+ Add Doctor</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Specialization</th>
                                <th>Fee</th>
                                <th>Experience</th>
                                <th>Status</th>
                                <th class="actions-header-column">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="doctors-table-body">
                            <!-- Rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 4: APPOINTMENTS LOG -->
            <div class="tab-panel" id="tab-appointments">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Appointments Log</h3>
                        <div class="table-controls" id="appointment-actions-wrapper">
                            <button class="btn" onclick="openCreateModal('appointment')">+ Book Appointment</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th class="actions-header-column">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="appointments-table-body">
                            <!-- Rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 5: OPD VISITS -->
            <div class="tab-panel" id="tab-opd">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>OPD Consultations</h3>
                        <button class="btn" onclick="openCreateModal('opd')">+ Log OPD Visit</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Symptoms</th>
                                <th>Diagnosis</th>
                                <th>Consultation Fee</th>
                                <th class="actions-header-column">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="opd-table-body">
                            <!-- Rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 6: IPD ADMISSIONS -->
            <div class="tab-panel" id="tab-ipd">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>IPD Admissions</h3>
                        <button class="btn" onclick="openCreateModal('ipd')">+ Admit Patient</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Admission Date</th>
                                <th>Ward</th>
                                <th>Room / Bed</th>
                                <th>Status</th>
                                <th class="actions-header-column">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="ipd-table-body">
                            <!-- Rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 7: BILLING & INVOICES -->
            <div class="tab-panel" id="tab-billing">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Billing & Invoices</h3>
                        <button class="btn" onclick="openCreateModal('billing')">+ Generate Bill</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Invoice Number</th>
                                <th>Patient</th>
                                <th>Billed Total</th>
                                <th>Amount Paid</th>
                                <th>Due Balance</th>
                                <th>Status</th>
                                <th class="actions-header-column">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="billing-table-body">
                            <!-- Rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 8: PHARMACY CATALOG -->
            <div class="tab-panel" id="tab-pharmacy">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Pharmacy Inventory</h3>
                        <button class="btn" onclick="openCreateModal('pharmacy')">+ Add Medicine</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Medicine Name</th>
                                <th>Batch No</th>
                                <th>Manufacturer</th>
                                <th>Stock Qty</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
                                <th class="actions-header-column">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="pharmacy-table-body">
                            <!-- Rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 9: LAB REPORTS -->
            <div class="tab-panel" id="tab-laboratory">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Laboratory Reports</h3>
                        <button class="btn" onclick="openCreateModal('laboratory')">+ Add Lab Report</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Test Name</th>
                                <th>Report Link</th>
                                <th>Remarks</th>
                                <th class="actions-header-column">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="laboratory-table-body">
                            <!-- Rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 10: MY ELECTRONIC RECORDS (PATIENT ONLY) -->
            <div class="tab-panel" id="tab-medical">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                    <div class="table-container">
                        <div class="table-header-row"><h3>My Prescriptions</h3></div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Doctor</th>
                                    <th>Medication details</th>
                                    <th>Dosage / Instructions</th>
                                </tr>
                            </thead>
                            <tbody id="my-prescriptions-body"></tbody>
                        </table>
                    </div>
                    <div class="table-container">
                        <div class="table-header-row"><h3>My Lab Results</h3></div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Test Name</th>
                                    <th>Remarks / Findings</th>
                                    <th>Report File</th>
                                </tr>
                            </thead>
                            <tbody id="my-lab-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 11: USER APPROVALS -->
            <div class="tab-panel" id="tab-approvals">
                <div class="table-container">
                    <div class="table-header-row"><h3>User Portal Registrations</h3></div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="approvals-table-body"></tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 12: PORTAL APIS DOC (SMTP SETTINGS) -->
            <div class="tab-panel" id="tab-apidocs">
                <div class="table-container" style="padding:30px;">
                    <h3 style="margin-bottom: 20px;">Email Verification Settings</h3>
                    
                    <form id="smtp-settings-form" onsubmit="saveSmtpSettings(event)" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                        <div class="form-group">
                            <label for="smtp-from-email">Sender Email (From Email)</label>
                            <input type="email" id="smtp-from-email" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="smtp-from-name">Sender Name Label</label>
                            <input type="text" id="smtp-from-name" class="form-input" required>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="smtp-subject">Subject Line</label>
                            <input type="text" id="smtp-subject" class="form-input" required>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="smtp-template">Body Template (Requires {name} and {otp})</label>
                            <textarea id="smtp-template" class="form-input" style="min-height: 120px; font-family: monospace;" required></textarea>
                        </div>

                        <!-- Custom SMTP Checkbox -->
                        <div class="form-group" style="grid-column: span 2; border-top: 1px solid var(--glass-border); padding-top: 20px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" id="smtp-enabled" onchange="toggleSmtpFields()" style="width: 18px; height: 18px; cursor: pointer;">
                                <label for="smtp-enabled" style="font-size: 14px; font-weight: 600; cursor: pointer; color: #fff; margin-bottom: 0;">Enable Custom SMTP Server Routing</label>
                            </div>
                        </div>

                        <div id="smtp-details-section" style="grid-column: span 2; display: none; grid-template-columns: repeat(2, 1fr); gap: 20px; border: 1px solid var(--glass-border); padding: 20px; border-radius: 12px;">
                            <div class="form-group">
                                <label for="smtp-host">SMTP Host Server</label>
                                <input type="text" id="smtp-host" class="form-input">
                            </div>
                            <div class="form-group">
                                <label for="smtp-port">SMTP Port</label>
                                <input type="text" id="smtp-port" class="form-input">
                            </div>
                            <div class="form-group">
                                <label for="smtp-username">SMTP Username</label>
                                <input type="text" id="smtp-username" class="form-input">
                            </div>
                            <div class="form-group">
                                <label for="smtp-password">SMTP Password</label>
                                <input type="password" id="smtp-password" class="form-input">
                            </div>
                            <div class="form-group" style="grid-column: span 2;">
                                <label for="smtp-encryption">Connection Security Type</label>
                                <select id="smtp-encryption" class="form-input" style="background: #111827;">
                                    <option value="tls">STARTTLS (Port 587)</option>
                                    <option value="ssl">SSL/TLS (Port 465)</option>
                                    <option value="none">None (Plain / Localhost)</option>
                                </select>
                            </div>
                        </div>

                        <div style="grid-column: span 2; display: flex; justify-content: flex-end;">
                            <button type="submit" id="smtp-submit-btn" class="auth-submit-btn" style="width: auto; padding: 12px 30px;">Save Email Settings</button>
                        </div>
                    </form>

                    <!-- Tester Card -->
                    <div style="margin-top: 30px; border-top: 1px solid var(--glass-border); padding-top: 25px; background: rgba(239, 68, 68, 0.02); border: 1px dashed rgba(239, 68, 68, 0.2); padding: 20px; border-radius: 12px;">
                        <h5>Diagnostics Mail Connection Tester</h5>
                        <p style="color: var(--text-muted); font-size:12px; margin-bottom:15px;">Send a live test verification message to any destination mailbox.</p>
                        <div style="display:flex; gap:15px; align-items:flex-end;">
                            <div class="form-group" style="flex:1; margin-bottom:0;">
                                <label for="smtp-test-email">Recipient Address</label>
                                <input type="email" id="smtp-test-email" class="form-input" style="padding:10px;" placeholder="e.g. yourname@gmail.com">
                            </div>
                            <button type="button" id="smtp-test-btn" class="btn" onclick="sendTestEmail()" style="height:42px;">Send Test</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL OVERLAYS (CRUD Forms) -->
    <div class="modal-overlay" id="crud-modal">
        <div class="modal-card">
            <button class="modal-close" onclick="closeCrudModal()">✖</button>
            <h3 id="modal-title" style="margin-bottom: 20px;">Register New Record</h3>
            <form id="crud-form" onsubmit="handleFormSubmit(event)">
                <input type="hidden" id="entity-type">
                <input type="hidden" id="entity-id">
                <div id="dynamic-form-fields" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; max-height: 450px; overflow-y: auto; padding-right: 5px;">
                    <!-- Fields dynamically injected -->
                </div>
                <button type="submit" class="auth-submit-btn" style="margin-top: 25px;">Save Record</button>
            </form>
        </div>
    </div>

    <!-- SCRIPT APPLICATION CONTROLLER -->
    <script>
        const API_URL = '/wp-json/hospital-management/v1';
        let authToken = localStorage.getItem('hospital_jwt_token') || '';
        let currentUser = null;

        // Prefill auth details
        function prefillUser(username, password) {
            showLogin();
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
            checkLoginType();
            toast(`Prefilled as ${username.replace('hospital_', '').toUpperCase()}! Click Login.`, 'success');
        }

        function checkLoginType() {
            const usernameInput = document.getElementById('username');
            const passGroup = document.getElementById('login-pass-group');
            const passwordInput = document.getElementById('password');
            const otpGroup = document.getElementById('login-otp-group');
            const otpInput = document.getElementById('login-otp');
            const submitBtn = document.getElementById('login-submit-btn');

            const val = usernameInput.value.trim().toLowerCase();
            const demoUsers = ['hospitalsuperadmin', 'hospital_doctor', 'hospital_receptionist', 'hospital_pharmacist', 'hospital_lab_technician', 'hospital_patient'];

            if (val === '') {
                passGroup.style.display = 'none';
                passwordInput.required = false;
                otpGroup.style.display = 'none';
                otpInput.required = false;
                submitBtn.innerText = 'Login';
            } else if (demoUsers.includes(val)) {
                passGroup.style.display = 'block';
                passwordInput.required = true;
                otpGroup.style.display = 'none';
                otpInput.required = false;
                submitBtn.innerText = 'Login';
            } else {
                passGroup.style.display = 'none';
                passwordInput.required = false;
                passwordInput.value = '';
                if (otpGroup.style.display === 'none') {
                    submitBtn.innerText = 'Request Verification OTP';
                } else {
                    submitBtn.innerText = 'Verify & Login';
                }
            }
        }

        function showRegister(e) {
            if (e) e.preventDefault();
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-form').style.display = 'block';
            document.getElementById('credentials-helper-container').style.display = 'none';
        }

        function showLogin(e) {
            if (e) e.preventDefault();
            document.getElementById('login-form').style.display = 'block';
            document.getElementById('register-form').style.display = 'none';
            document.getElementById('credentials-helper-container').style.display = 'block';
            checkLoginType();
        }

        // Handle user registration request
        function handleUserRegister(e) {
            e.preventDefault();
            const otpGroup = document.getElementById('reg-otp-group');
            const otpInput = document.getElementById('reg-otp');
            const submitBtn = document.getElementById('reg-submit-btn');

            const u = document.getElementById('reg-username').value;
            const em = document.getElementById('reg-email').value;
            const n = document.getElementById('reg-name').value;
            const r = document.getElementById('reg-role').value;

            if (otpGroup.style.display === 'none') {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Sending OTP...';

                fetch(`${API_URL}/auth/register`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username: u, email: em, name: n, role: r })
                })
                .then(res => {
                    if (!res.ok) return res.json().then(b => { throw new Error(b.message || 'OTP sending failed'); });
                    return res.json();
                })
                .then(() => {
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
                const otpVal = otpInput.value;
                submitBtn.disabled = true;
                submitBtn.innerText = 'Verifying...';

                fetch(`${API_URL}/auth/register/verify`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: em, otp: otpVal })
                })
                .then(res => {
                    if (!res.ok) return res.json().then(b => { throw new Error(b.message || 'Verification failed'); });
                    return res.json();
                })
                .then(() => {
                    toast('Verification successful! Account created. Wait for admin approval.', 'success');
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

        // Handle user login request
        function handleUserLogin(e) {
            e.preventDefault();
            const usernameInput = document.getElementById('username').value;
            const passwordInput = document.getElementById('password').value;
            const otpGroup = document.getElementById('login-otp-group');
            const otpInput = document.getElementById('login-otp');
            const submitBtn = document.getElementById('login-submit-btn');

            const demoUsers = ['hospitalsuperadmin', 'hospital_doctor', 'hospital_receptionist', 'hospital_pharmacist', 'hospital_lab_technician', 'hospital_patient'];

            if (demoUsers.includes(usernameInput)) {
                // Password based login
                submitBtn.disabled = true;
                submitBtn.innerText = 'Logging in...';
                
                fetch(`${API_URL}/auth/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username: usernameInput, password: passwordInput })
                })
                .then(res => {
                    if (!res.ok) return res.json().then(b => { throw new Error(b.message || 'Login failed'); });
                    return res.json();
                })
                .then(body => {
                    authToken = body.data.token;
                    localStorage.setItem('hospital_jwt_token', authToken);
                    currentUser = body.data.user;
                    toast('Authentication successful!', 'success');
                    showAppScreen();
                })
                .catch(err => {
                    toast(err.message, 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Login';
                });
            } else {
                // OTP based login flow
                if (otpGroup.style.display === 'none') {
                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Sending OTP...';
                    
                    fetch(`${API_URL}/auth/login/initiate`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ username: usernameInput })
                    })
                    .then(res => {
                        if (!res.ok) return res.json().then(b => { throw new Error(b.message || 'OTP delivery failed'); });
                        return res.json();
                    })
                    .then(() => {
                        toast('Verification OTP sent to your registered email address!', 'success');
                        otpGroup.style.display = 'block';
                        otpInput.required = true;
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'Verify & Login';
                    })
                    .catch(err => {
                        toast(err.message, 'error');
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'Request Verification OTP';
                    });
                } else {
                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Verifying...';
                    
                    fetch(`${API_URL}/auth/login`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ username: usernameInput, otp: otpInput.value })
                    })
                    .then(res => {
                        if (!res.ok) return res.json().then(b => { throw new Error(b.message || 'Invalid code'); });
                        return res.json();
                    })
                    .then(body => {
                        authToken = body.data.token;
                        localStorage.setItem('hospital_jwt_token', authToken);
                        currentUser = body.data.user;
                        toast('Authentication successful!', 'success');
                        showAppScreen();
                    })
                    .catch(err => {
                        toast(err.message, 'error');
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'Verify & Login';
                    });
                }
            }
        }

        // Toggle app panel transitions
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
                executeLogout();
            });
        }

        function showAppScreen() {
            document.getElementById('auth-screen').style.display = 'none';
            document.getElementById('app-screen').style.display = 'flex';
            
            // Set User profile badge
            document.getElementById('profile-avatar').innerText = currentUser.name.substring(0, 2).toUpperCase();
            document.getElementById('profile-name').innerText = currentUser.name;
            document.getElementById('profile-role').innerText = currentUser.role.replace('hospital_', '').replace('_', ' ').toUpperCase();
            
            configureUIPermissions();
            
            // Auto switch to dashboard or last active tab
            const activeTab = localStorage.getItem('hospital_active_tab') || 'dashboard';
            switchTab(activeTab);
        }

        function executeLogout() {
            fetch(`${API_URL}/auth/logout`, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .finally(() => {
                authToken = '';
                currentUser = null;
                localStorage.removeItem('hospital_jwt_token');
                document.getElementById('auth-screen').style.display = 'flex';
                document.getElementById('app-screen').style.display = 'none';
                showLogin();
            });
        }

        // Configure sidebar tabs visibility by logged role
        function configureUIPermissions() {
            if (!currentUser) return;
            const role = currentUser.role;

            const menuMapping = {
                'administrator': ['dashboard', 'patients', 'doctors', 'appointments', 'opd', 'ipd', 'billing', 'pharmacy', 'laboratory', 'approvals', 'apidocs'],
                'hospital_super_admin': ['dashboard', 'patients', 'doctors', 'appointments', 'opd', 'ipd', 'billing', 'pharmacy', 'laboratory', 'approvals', 'apidocs'],
                'hospital_doctor': ['dashboard', 'patients', 'appointments', 'opd', 'laboratory'],
                'hospital_receptionist': ['dashboard', 'patients', 'appointments', 'billing'],
                'hospital_pharmacist': ['dashboard', 'pharmacy'],
                'hospital_lab_technician': ['dashboard', 'laboratory'],
                'hospital_patient': ['dashboard', 'medical']
            };

            const visibleMenus = menuMapping[role] || ['dashboard'];
            const menus = ['dashboard', 'patients', 'doctors', 'appointments', 'opd', 'ipd', 'billing', 'pharmacy', 'laboratory', 'medical', 'approvals', 'apidocs'];
            
            menus.forEach(menu => {
                const el = document.getElementById(`menu-${menu}`);
                if (el) {
                    el.style.display = visibleMenus.includes(menu) ? 'block' : 'none';
                }
            });

            // Adjust specific action write privileges
            const writeRoles = ['administrator', 'hospital_super_admin', 'hospital_receptionist'];
            const patientActions = document.getElementById('patient-actions-wrapper');
            const doctorActions = document.getElementById('doctor-actions-wrapper');
            const appointmentActions = document.getElementById('appointment-actions-wrapper');

            if (patientActions) patientActions.style.display = writeRoles.includes(role) ? 'block' : 'none';
            if (doctorActions) doctorActions.style.display = ['administrator', 'hospital_super_admin'].includes(role) ? 'block' : 'none';
            if (appointmentActions) appointmentActions.style.display = writeRoles.includes(role) || role === 'hospital_patient' ? 'block' : 'none';

            // Hide/show actions columns in tables
            const hasActions = ['administrator', 'hospital_super_admin', 'hospital_receptionist', 'hospital_pharmacist', 'hospital_lab_technician'].includes(role);
            document.querySelectorAll('.actions-header-column').forEach(el => {
                el.style.display = hasActions ? 'table-cell' : 'none';
            });
        }

        // Switch workspace tabs and load dynamic datasets
        function switchTab(tabName) {
            localStorage.setItem('hospital_active_tab', tabName);
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.menu-item').forEach(m => m.classList.remove('active'));
            
            const targetPanel = document.getElementById(`tab-${tabName}`);
            if (targetPanel) targetPanel.classList.add('active');

            // Set active sidebar button
            const buttons = Array.from(document.querySelectorAll('.menu-item'));
            const activeBtn = buttons.find(b => b.innerText.toLowerCase().includes(tabName.toLowerCase()) || (tabName === 'medical' && b.innerText.toLowerCase().includes('records')));
            if (activeBtn) activeBtn.classList.add('active');

            const headerTitle = document.getElementById('tab-title-header');
            const headerSubtitle = document.getElementById('tab-subtitle-header');

            headerTitle.innerText = tabName.charAt(0).toUpperCase() + tabName.slice(1) + (tabName === 'dashboard' ? '' : ' Management');
            headerSubtitle.innerText = `Inspect, add, update, and search active hospital ${tabName} records.`;

            if (tabName === 'dashboard') {
                headerTitle.innerText = "Hospital Dashboard";
                headerSubtitle.innerText = "Overview of clinical statistics and appointments status";
                loadDashboardData();
            } else if (tabName === 'patients') {
                loadPatients();
            } else if (tabName === 'doctors') {
                loadDoctors();
            } else if (tabName === 'appointments') {
                loadAppointments();
            } else if (tabName === 'opd') {
                loadOpd();
            } else if (tabName === 'ipd') {
                loadIpd();
            } else if (tabName === 'billing') {
                loadBilling();
            } else if (tabName === 'pharmacy') {
                loadPharmacy();
            } else if (tabName === 'laboratory') {
                loadLaboratory();
            } else if (tabName === 'medical') {
                headerTitle.innerText = "My Electronic Health Records";
                headerSubtitle.innerText = "Access your detailed prescriptions, outpatient consults, and clinical testing reports.";
                loadMedicalRecords();
            } else if (tabName === 'approvals') {
                loadApprovals();
            } else if (tabName === 'apidocs') {
                headerTitle.innerText = "Portal SMTP Configurations";
                headerSubtitle.innerText = "Configure and test custom email server routing rules.";
                loadSmtpSettings();
            }
        }

        // LOAD DATASETS FROM REST ENDPOINTS
        function loadDashboardData() {
            fetch(`${API_URL}/dashboard`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const data = body.data;
                document.getElementById('stat-patients').innerText = data.cards.total_patients;
                document.getElementById('stat-appointments').innerText = data.cards.today_appointments;
                document.getElementById('stat-opd').innerText = data.cards.opd_patients;
                document.getElementById('stat-revenue').innerText = '₹' + data.cards.today_revenue;

                // Render revenue bars
                const barContainer = document.getElementById('revenue-chart-bars');
                barContainer.innerHTML = '';
                
                if (data.charts.revenue_trends.length === 0) {
                    barContainer.innerHTML = '<p style="color:var(--text-muted); font-size:12px; margin:auto;">No revenue trends logged yet.</p>';
                } else {
                    const maxVal = Math.max(...data.charts.revenue_trends.map(t => parseFloat(t.value) || 1));
                    data.charts.revenue_trends.forEach(trend => {
                        const val = parseFloat(trend.value) || 0;
                        const pct = maxVal > 0 ? (val / maxVal) * 100 : 0;
                        
                        const wrapper = document.createElement('div');
                        wrapper.className = 'bar-container';
                        wrapper.innerHTML = `
                            <div class="chart-bar" style="height: ${pct}%" data-value="₹${val}"></div>
                            <div class="bar-label">${trend.label}</div>
                        `;
                        barContainer.appendChild(wrapper);
                    });
                }

                // Render IPD Ward occupied beds progress bars
                const wardContainer = document.getElementById('ward-distribution-data');
                wardContainer.innerHTML = '';
                
                if (data.charts.patient_growth.length === 0) {
                    wardContainer.innerHTML = '<p style="color:var(--text-muted); font-size:12px; margin:auto;">No active admissions.</p>';
                } else {
                    // Seed standard ward capacities
                    const capacities = { 'ICU': 10, 'General': 30, 'Deluxe': 5, 'Semi-Private': 15 };
                    
                    fetch(`${API_URL}/ipd?status=ADMITTED`, {
                        headers: { 'Authorization': `Bearer ${authToken}` }
                    })
                    .then(res => res.json())
                    .then(ipdBody => {
                        const admitted = ipdBody.data.data || [];
                        const occupied = { 'ICU': 0, 'General': 0, 'Deluxe': 0, 'Semi-Private': 0 };
                        
                        admitted.forEach(a => {
                            if (occupied[a.ward] !== undefined) occupied[a.ward]++;
                        });
                        
                        Object.keys(capacities).forEach(wardName => {
                            const count = occupied[wardName];
                            const cap = capacities[wardName];
                            const pct = Math.min(100, (count / cap) * 100);
                            
                            const div = document.createElement('div');
                            div.innerHTML = `
                                <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:4px;">
                                    <span>${wardName} Ward</span>
                                    <span>${count} / ${cap} Beds Occupied</span>
                                </div>
                                <div style="width:100%; height:6px; background:rgba(255,255,255,0.03); border-radius:3px; overflow:hidden;">
                                    <div style="width:${pct}%; height:100%; background:var(--accent-purple); border-radius:3px;"></div>
                                </div>
                            `;
                            wardContainer.appendChild(div);
                        });
                    });
                }
            })
            .catch(() => toast('Error fetching dashboard metrics.', 'error'));
        }

        function loadPatients() {
            fetch(`${API_URL}/patients`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const tbody = document.getElementById('patients-table-body');
                tbody.innerHTML = '';
                const patients = body.data.data || [];
                
                patients.forEach(pat => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><strong>${pat.patient_code}</strong></td>
                        <td>${pat.name}</td>
                        <td>${pat.gender || '-'}</td>
                        <td>${pat.mobile || '-'}</td>
                        <td>${pat.blood_group || '-'}</td>
                        <td><span class="badge badge-active">${pat.status}</span></td>
                        <td class="actions-header-column">
                            <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;" onclick="openEditModal('patient', ${pat.id})">Edit</button>
                            <button class="btn btn-danger" style="padding:4px 8px; font-size:11px;" onclick="deleteRecord('patients', ${pat.id}, loadPatients)">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
                configureUIPermissions();
            });
        }

        function loadDoctors() {
            fetch(`${API_URL}/doctors`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const tbody = document.getElementById('doctors-table-body');
                tbody.innerHTML = '';
                const doctors = body.data.data || [];

                doctors.forEach(doc => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><strong>${doc.doctor_code}</strong></td>
                        <td>${doc.name}</td>
                        <td>${doc.specialization}</td>
                        <td>₹${doc.consultation_fee}</td>
                        <td>${doc.experience} Years</td>
                        <td><span class="badge badge-active">${doc.status}</span></td>
                        <td class="actions-header-column">
                            <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;" onclick="openEditModal('doctor', ${doc.id})">Edit</button>
                            <button class="btn btn-danger" style="padding:4px 8px; font-size:11px;" onclick="deleteRecord('doctors', ${doc.id}, loadDoctors)">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
                configureUIPermissions();
            });
        }

        function loadAppointments() {
            fetch(`${API_URL}/appointments`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const tbody = document.getElementById('appointments-table-body');
                tbody.innerHTML = '';
                const appts = body.data.data || [];

                appts.forEach(apt => {
                    let statusClass = 'badge-pending';
                    if (apt.status === 'Completed') statusClass = 'badge-paid';
                    if (apt.status === 'Cancelled') statusClass = 'badge-cancelled';

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><strong>${apt.patient_name}</strong> (${apt.patient_code})</td>
                        <td>${apt.doctor_name}</td>
                        <td>${apt.appointment_date}</td>
                        <td>${apt.appointment_time}</td>
                        <td>${apt.appointment_type}</td>
                        <td><span class="badge ${statusClass}">${apt.status}</span></td>
                        <td class="actions-header-column">
                            <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;" onclick="openEditModal('appointment', ${apt.id})">Edit</button>
                            <button class="btn btn-danger" style="padding:4px 8px; font-size:11px;" onclick="deleteRecord('appointments', ${apt.id}, loadAppointments)">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
                configureUIPermissions();
            });
        }

        function loadOpd() {
            fetch(`${API_URL}/opd`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const tbody = document.getElementById('opd-table-body');
                tbody.innerHTML = '';
                const records = body.data.data || [];

                records.forEach(o => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><strong>${o.patient_name}</strong> (${o.patient_code})</td>
                        <td>${o.doctor_name}</td>
                        <td>${o.visit_date}</td>
                        <td>${o.symptoms || '-'}</td>
                        <td>${o.diagnosis || '-'}</td>
                        <td>₹${o.consultation_fee}</td>
                        <td class="actions-header-column">
                            <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;" onclick="openEditModal('opd', ${o.id})">Edit</button>
                            <button class="btn btn-danger" style="padding:4px 8px; font-size:11px;" onclick="deleteRecord('opd', ${o.id}, loadOpd)">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
                configureUIPermissions();
            });
        }

        function loadIpd() {
            fetch(`${API_URL}/ipd`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const tbody = document.getElementById('ipd-table-body');
                tbody.innerHTML = '';
                const records = body.data.data || [];

                records.forEach(i => {
                    const statusClass = i.status === 'ADMITTED' ? 'badge-pending' : 'badge-paid';
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><strong>${i.patient_name}</strong> (${i.patient_code})</td>
                        <td>${i.doctor_name}</td>
                        <td>${i.admission_date}</td>
                        <td>${i.ward} Ward</td>
                        <td>Room ${i.room_number || '-'} / Bed ${i.bed_number || '-'}</td>
                        <td><span class="badge ${statusClass}">${i.status}</span></td>
                        <td class="actions-header-column">
                            <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;" onclick="openEditModal('ipd', ${i.id})">Edit</button>
                            <button class="btn btn-danger" style="padding:4px 8px; font-size:11px;" onclick="deleteRecord('ipd', ${i.id}, loadIpd)">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
                configureUIPermissions();
            });
        }

        function loadBilling() {
            fetch(`${API_URL}/billing`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const tbody = document.getElementById('billing-table-body');
                tbody.innerHTML = '';
                const bills = body.data.data || [];

                bills.forEach(b => {
                    let statusClass = 'badge-pending';
                    if (b.status === 'PAID') statusClass = 'badge-paid';
                    
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><strong>${b.bill_number}</strong></td>
                        <td>${b.patient_name} (${b.patient_code})</td>
                        <td>₹${b.total_amount}</td>
                        <td>₹${b.paid_amount}</td>
                        <td>₹${b.due_amount}</td>
                        <td><span class="badge ${statusClass}">${b.status}</span></td>
                        <td class="actions-header-column">
                            <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;" onclick="openEditModal('billing', ${b.id})">Edit</button>
                            <button class="btn btn-danger" style="padding:4px 8px; font-size:11px;" onclick="deleteRecord('billing', ${b.id}, loadBilling)">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
                configureUIPermissions();
            });
        }

        function loadPharmacy() {
            fetch(`${API_URL}/pharmacy`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const tbody = document.getElementById('pharmacy-table-body');
                tbody.innerHTML = '';
                const medicines = body.data.data || [];

                medicines.forEach(m => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><strong>${m.medicine_name}</strong></td>
                        <td>${m.batch_number}</td>
                        <td>${m.manufacturer || '-'}</td>
                        <td>${m.quantity} Units</td>
                        <td>${m.expiry_date}</td>
                        <td><span class="badge badge-active">${m.status}</span></td>
                        <td class="actions-header-column">
                            <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;" onclick="openEditModal('pharmacy', ${m.id})">Edit</button>
                            <button class="btn btn-danger" style="padding:4px 8px; font-size:11px;" onclick="deleteRecord('pharmacy', ${m.id}, loadPharmacy)">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
                configureUIPermissions();
            });
        }

        function loadLaboratory() {
            fetch(`${API_URL}/laboratory`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const tbody = document.getElementById('laboratory-table-body');
                tbody.innerHTML = '';
                const reports = body.data.data || [];

                reports.forEach(r => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><strong>${r.patient_name}</strong> (${r.patient_code})</td>
                        <td>${r.doctor_name}</td>
                        <td>${r.test_name} (${r.test_code})</td>
                        <td><a href="${r.report_file}" style="color:var(--accent-blue); text-decoration:none;" target="_blank">View Report PDF</a></td>
                        <td>${r.remarks || '-'}</td>
                        <td class="actions-header-column">
                            <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;" onclick="openEditModal('laboratory', ${r.id})">Edit</button>
                            <button class="btn btn-danger" style="padding:4px 8px; font-size:11px;" onclick="deleteRecord('laboratory', ${r.id}, loadLaboratory)">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
                configureUIPermissions();
            });
        }

        function loadMedicalRecords() {
            fetch(`${API_URL}/medical-records`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const data = body.data;

                // Load prescriptions
                const presTbody = document.getElementById('my-prescriptions-body');
                presTbody.innerHTML = '';
                
                data.prescriptions.forEach(p => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><strong>${p.doctor_name}</strong></td>
                        <td>${p.medicine}</td>
                        <td>${p.dosage} / ${p.duration}<br><small style="color:var(--text-muted)">${p.instructions}</small></td>
                    `;
                    presTbody.appendChild(tr);
                });

                // Load lab results
                const labTbody = document.getElementById('my-lab-body');
                labTbody.innerHTML = '';

                data.lab_reports.forEach(r => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><strong>${r.test_name}</strong> (${r.test_code})</td>
                        <td>${r.remarks}</td>
                        <td><a href="${r.report_file}" style="color:var(--accent-blue); text-decoration:none;" target="_blank">Download PDF</a></td>
                    `;
                    labTbody.appendChild(tr);
                });
            })
            .catch(() => toast('Failed to retrieve medical records.', 'error'));
        }

        function loadApprovals() {
            fetch(`${API_URL}/auth/users`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const tbody = document.getElementById('approvals-table-body');
                tbody.innerHTML = '';
                const users = body.data || [];

                users.forEach(user => {
                    let actionButtons = '';
                    if (user.status === 'PENDING') {
                        actionButtons = `
                            <button class="btn" style="padding:4px 8px; font-size:11px; background:var(--accent-green);" onclick="changeApprovalStatus(${user.id}, 'APPROVED')">Approve</button>
                            <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;" onclick="changeApprovalStatus(${user.id}, 'HOLD')">Hold</button>
                            <button class="btn btn-danger" style="padding:4px 8px; font-size:11px;" onclick="changeApprovalStatus(${user.id}, 'BLOCKED')">Block</button>
                        `;
                    } else {
                        actionButtons = `
                            <span style="font-size:11px; color:var(--text-muted); margin-right: 10px;">Status: ${user.status}</span>
                            <button class="btn btn-danger" style="padding:4px 8px; font-size:11px;" onclick="deleteUserRecord(${user.id})">Delete</button>
                        `;
                    }

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><strong>${user.username}</strong></td>
                        <td>${user.email}</td>
                        <td>${user.name}</td>
                        <td>${user.role.replace('hospital_', '').toUpperCase()}</td>
                        <td><span class="badge ${user.status === 'APPROVED' ? 'badge-approved' : 'badge-pending'}">${user.status}</span></td>
                        <td style="text-align: right;">${actionButtons}</td>
                    `;
                    tbody.appendChild(tr);
                });
            });
        }

        function changeApprovalStatus(userId, status) {
            fetch(`${API_URL}/auth/users/status`, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}`
                },
                body: JSON.stringify({ user_id: userId, status: status })
            })
            .then(res => res.json())
            .then(() => {
                toast(`User status updated to ${status}!`, 'success');
                loadApprovals();
            });
        }

        function deleteUserRecord(userId) {
            if (!confirm('Are you sure you want to permanently delete this user?')) return;
            fetch(`${API_URL}/auth/users/${userId}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(() => {
                toast('User account permanently deleted!', 'success');
                loadApprovals();
            });
        }

        function deleteRecord(endpoint, id, reloadCallback) {
            if (!confirm('Are you sure you want to delete this record?')) return;
            fetch(`${API_URL}/${endpoint}/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                if (body.success) {
                    toast('Record deleted successfully.', 'success');
                    reloadCallback();
                } else {
                    toast(body.message, 'error');
                }
            });
        }

        // LOAD / SAVE SMTP EMAIL SETTINGS
        function loadSmtpSettings() {
            fetch(`${API_URL}/auth/smtp`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const data = body.data;
                document.getElementById('smtp-from-email').value = data.from_email || '';
                document.getElementById('smtp-from-name').value = data.from_name || '';
                document.getElementById('smtp-subject').value = data.subject || '';
                document.getElementById('smtp-template').value = data.template || '';
                
                const enabledCheckbox = document.getElementById('smtp-enabled');
                enabledCheckbox.checked = (data.smtp_enabled === 'yes');
                
                document.getElementById('smtp-host').value = data.smtp_host || '';
                document.getElementById('smtp-port').value = data.smtp_port || '587';
                document.getElementById('smtp-username').value = data.smtp_username || '';
                document.getElementById('smtp-password').value = data.smtp_password || '';
                
                if (data.smtp_encryption) {
                    document.getElementById('smtp-encryption').value = data.smtp_encryption;
                }
                
                toggleSmtpFields();
            });
        }

        function toggleSmtpFields() {
            const checkbox = document.getElementById('smtp-enabled');
            const detailsSection = document.getElementById('smtp-details-section');
            if (checkbox && detailsSection) {
                detailsSection.style.display = checkbox.checked ? 'grid' : 'none';
            }
        }

        function saveSmtpSettings(e) {
            e.preventDefault();
            const btn = document.getElementById('smtp-submit-btn');
            btn.disabled = true;
            btn.innerText = 'Saving...';

            const payload = {
                from_email: document.getElementById('smtp-from-email').value,
                from_name: document.getElementById('smtp-from-name').value,
                subject: document.getElementById('smtp-subject').value,
                template: document.getElementById('smtp-template').value,
                smtp_enabled: document.getElementById('smtp-enabled').checked ? 'yes' : 'no',
                smtp_host: document.getElementById('smtp-host').value,
                smtp_port: document.getElementById('smtp-port').value,
                smtp_username: document.getElementById('smtp-username').value,
                smtp_password: document.getElementById('smtp-password').value,
                smtp_encryption: document.getElementById('smtp-encryption').value
            };

            fetch(`${API_URL}/auth/smtp`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}`
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(body => {
                if (body.success) {
                    toast('Email settings saved successfully!', 'success');
                } else {
                    toast(body.message, 'error');
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerText = 'Save Email Settings';
            });
        }

        function sendTestEmail() {
            const emailInput = document.getElementById('smtp-test-email');
            const testEmail = emailInput.value.trim();
            if (!testEmail) {
                toast('Please enter a valid recipient email address.', 'error');
                return;
            }

            const btn = document.getElementById('smtp-test-btn');
            btn.disabled = true;
            btn.innerText = 'Sending...';

            fetch(`${API_URL}/auth/smtp/test`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}`
                },
                body: JSON.stringify({ test_email: testEmail })
            })
            .then(res => res.json())
            .then(body => {
                if (body.success) {
                    toast(body.message || 'Test email sent successfully!', 'success');
                } else {
                    toast(body.message, 'error');
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerText = 'Send Test';
            });
        }

        // CRUD MODAL HANDLERS
        function closeCrudModal() {
            document.getElementById('crud-modal').classList.remove('show');
        }

        function openCreateModal(type) {
            document.getElementById('entity-type').value = type;
            document.getElementById('entity-id').value = '';
            document.getElementById('modal-title').innerText = `Add New ${type.toUpperCase()}`;
            
            const container = document.getElementById('dynamic-form-fields');
            container.innerHTML = '';

            // Render form inputs dynamically by type
            if (type === 'patient') {
                container.innerHTML = `
                    <div class="form-group"><label>Patient Name*</label><input type="text" id="form-name" class="form-input" required></div>
                    <div class="form-group"><label>Gender</label><select id="form-gender" class="form-input" style="background:#111827;"><option value="Male">Male</option><option value="Female">Female</option><option value="Other">Other</option></select></div>
                    <div class="form-group"><label>DOB</label><input type="date" id="form-dob" class="form-input"></div>
                    <div class="form-group"><label>Mobile</label><input type="text" id="form-mobile" class="form-input"></div>
                    <div class="form-group"><label>Email</label><input type="email" id="form-email" class="form-input"></div>
                    <div class="form-group"><label>Blood Group</label><input type="text" id="form-blood_group" class="form-input" placeholder="e.g. O+"></div>
                    <div class="form-group" style="grid-column: span 2;"><label>Address</label><textarea id="form-address" class="form-input" style="min-height:70px;"></textarea></div>
                    <div class="form-group" style="grid-column: span 2;"><label>Emergency Contact</label><input type="text" id="form-emergency_contact" class="form-input"></div>
                `;
            } else if (type === 'doctor') {
                container.innerHTML = `
                    <div class="form-group"><label>Doctor Name*</label><input type="text" id="form-name" class="form-input" required></div>
                    <div class="form-group"><label>Specialization*</label><input type="text" id="form-specialization" class="form-input" required></div>
                    <div class="form-group"><label>Qualification</label><input type="text" id="form-qualification" class="form-input"></div>
                    <div class="form-group"><label>Mobile*</label><input type="text" id="form-mobile" class="form-input" required></div>
                    <div class="form-group"><label>Email*</label><input type="email" id="form-email" class="form-input" required></div>
                    <div class="form-group"><label>Fee (INR)*</label><input type="number" id="form-consultation_fee" class="form-input" required></div>
                    <div class="form-group"><label>Experience (Years)</label><input type="number" id="form-experience" class="form-input"></div>
                `;
            } else if (type === 'appointment') {
                // We fetch the patient and doctor lists to build dropdown selects
                container.innerHTML = `
                    <div class="form-group"><label>Patient ID*</label><input type="number" id="form-patient_id" class="form-input" required></div>
                    <div class="form-group"><label>Doctor ID*</label><input type="number" id="form-doctor_id" class="form-input" required></div>
                    <div class="form-group"><label>Appointment Date*</label><input type="date" id="form-appointment_date" class="form-input" required></div>
                    <div class="form-group"><label>Appointment Time*</label><input type="time" id="form-appointment_time" class="form-input" required></div>
                    <div class="form-group" style="grid-column:span 2;"><label>Type</label><select id="form-appointment_type" class="form-input" style="background:#111827;"><option value="General">General</option><option value="Telemedicine">Telemedicine</option><option value="Followup">Follow-Up</option></select></div>
                `;
            } else if (type === 'opd') {
                container.innerHTML = `
                    <div class="form-group"><label>Patient ID*</label><input type="number" id="form-patient_id" class="form-input" required></div>
                    <div class="form-group"><label>Doctor ID*</label><input type="number" id="form-doctor_id" class="form-input" required></div>
                    <div class="form-group"><label>Visit Date*</label><input type="date" id="form-visit_date" class="form-input" required></div>
                    <div class="form-group"><label>Consultation Fee*</label><input type="number" id="form-consultation_fee" class="form-input" required></div>
                    <div class="form-group" style="grid-column:span 2;"><label>Symptoms</label><textarea id="form-symptoms" class="form-input"></textarea></div>
                    <div class="form-group" style="grid-column:span 2;"><label>Diagnosis</label><textarea id="form-diagnosis" class="form-input"></textarea></div>
                    <div class="form-group" style="grid-column:span 2;"><label>Prescription</label><textarea id="form-prescription" class="form-input"></textarea></div>
                `;
            } else if (type === 'ipd') {
                container.innerHTML = `
                    <div class="form-group"><label>Patient ID*</label><input type="number" id="form-patient_id" class="form-input" required></div>
                    <div class="form-group"><label>Doctor ID*</label><input type="number" id="form-doctor_id" class="form-input" required></div>
                    <div class="form-group"><label>Admission Date*</label><input type="datetime-local" id="form-admission_date" class="form-input" required></div>
                    <div class="form-group"><label>Ward Name</label><select id="form-ward" class="form-input" style="background:#111827;"><option value="General">General</option><option value="ICU">ICU</option><option value="Deluxe">Deluxe</option><option value="Semi-Private">Semi-Private</option></select></div>
                    <div class="form-group"><label>Room Number</label><input type="text" id="form-room_number" class="form-input"></div>
                    <div class="form-group"><label>Bed Number</label><input type="text" id="form-bed_number" class="form-input"></div>
                `;
            } else if (type === 'billing') {
                container.innerHTML = `
                    <div class="form-group"><label>Patient ID*</label><input type="number" id="form-patient_id" class="form-input" required></div>
                    <div class="form-group"><label>Consultation Charges*</label><input type="number" id="form-consultation_charges" class="form-input" required></div>
                    <div class="form-group"><label>Room Charges</label><input type="number" id="form-room_charges" class="form-input"></div>
                    <div class="form-group"><label>Lab Charges</label><input type="number" id="form-lab_charges" class="form-input"></div>
                    <div class="form-group"><label>Medicine Charges</label><input type="number" id="form-medicine_charges" class="form-input"></div>
                    <div class="form-group"><label>Discount Amount</label><input type="number" id="form-discount" class="form-input"></div>
                    <div class="form-group"><label>Tax (%)</label><input type="number" id="form-tax" class="form-input"></div>
                    <div class="form-group"><label>Paid Amount</label><input type="number" id="form-paid_amount" class="form-input"></div>
                `;
            } else if (type === 'pharmacy') {
                container.innerHTML = `
                    <div class="form-group" style="grid-column:span 2;"><label>Medicine Name*</label><input type="text" id="form-medicine_name" class="form-input" required></div>
                    <div class="form-group"><label>Batch Number*</label><input type="text" id="form-batch_number" class="form-input" required></div>
                    <div class="form-group"><label>Manufacturer</label><input type="text" id="form-manufacturer" class="form-input"></div>
                    <div class="form-group"><label>Purchase Price</label><input type="number" id="form-purchase_price" class="form-input"></div>
                    <div class="form-group"><label>Selling Price</label><input type="number" id="form-selling_price" class="form-input"></div>
                    <div class="form-group"><label>Stock Quantity*</label><input type="number" id="form-quantity" class="form-input" required></div>
                    <div class="form-group"><label>Expiry Date*</label><input type="date" id="form-expiry_date" class="form-input" required></div>
                `;
            } else if (type === 'laboratory') {
                container.innerHTML = `
                    <div class="form-group"><label>Patient ID*</label><input type="number" id="form-patient_id" class="form-input" required></div>
                    <div class="form-group"><label>Doctor ID*</label><input type="number" id="form-doctor_id" class="form-input" required></div>
                    <div class="form-group"><label>Test ID*</label><input type="number" id="form-test_id" class="form-input" required></div>
                    <div class="form-group" style="grid-column:span 2;"><label>Report File PDF URL</label><input type="text" id="form-report_file" class="form-input"></div>
                    <div class="form-group" style="grid-column:span 2;"><label>Remarks</label><textarea id="form-remarks" class="form-input"></textarea></div>
                `;
            }

            document.getElementById('crud-modal').classList.add('show');
        }

        // Preload record for edits
        function openEditModal(type, id) {
            document.getElementById('entity-type').value = type;
            document.getElementById('entity-id').value = id;
            document.getElementById('modal-title').innerText = `Edit ${type.toUpperCase()} Record #${id}`;
            
            openCreateModal(type);
            
            let fetchUrl = `${API_URL}/${type === 'pharmacy' ? 'pharmacy' : type + 's'}/${id}`;
            if (type === 'opd') fetchUrl = `${API_URL}/opd/${id}`;
            if (type === 'ipd') fetchUrl = `${API_URL}/ipd/${id}`;
            if (type === 'billing') fetchUrl = `${API_URL}/billing/${id}`;
            if (type === 'laboratory') fetchUrl = `${API_URL}/laboratory/${id}`;

            fetch(fetchUrl, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(body => {
                const record = body.data;
                // Prepopulate form fields
                Object.keys(record).forEach(key => {
                    const el = document.getElementById(`form-${key}`);
                    if (el) el.value = record[key] || '';
                });
            });
        }

        // Submit newly created / edited records
        function handleFormSubmit(e) {
            e.preventDefault();
            const type = document.getElementById('entity-type').value;
            const id = document.getElementById('entity-id').value;

            const payload = {};
            const inputs = document.getElementById('dynamic-form-fields').querySelectorAll('.form-input');
            inputs.forEach(input => {
                const key = input.id.replace('form-', '');
                payload[key] = input.value;
            });

            const method = id ? 'PUT' : 'POST';
            
            let fetchUrl = `${API_URL}/${type === 'pharmacy' ? 'pharmacy' : type + 's'}`;
            if (type === 'opd') fetchUrl = `${API_URL}/opd`;
            if (type === 'ipd') fetchUrl = `${API_URL}/ipd`;
            if (type === 'billing') fetchUrl = `${API_URL}/billing`;
            if (type === 'laboratory') fetchUrl = `${API_URL}/laboratory`;
            
            if (id) fetchUrl += `/${id}`;

            fetch(fetchUrl, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}`
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(body => {
                if (body.success) {
                    toast(id ? 'Record updated successfully!' : 'Record created successfully!', 'success');
                    closeCrudModal();
                    
                    // Reload active tab data
                    if (type === 'patient') loadPatients();
                    if (type === 'doctor') loadDoctors();
                    if (type === 'appointment') loadAppointments();
                    if (type === 'opd') loadOpd();
                    if (type === 'ipd') loadIpd();
                    if (type === 'billing') loadBilling();
                    if (type === 'pharmacy') loadPharmacy();
                    if (type === 'laboratory') loadLaboratory();
                } else {
                    toast(body.message, 'error');
                }
            })
            .catch(() => toast('Server error: Failed to save record.', 'error'));
        }

        // Toast alert trigger
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

        // Verification & Bootstrapping
        window.addEventListener('DOMContentLoaded', () => {
            if (authToken) {
                verifySession();
            } else {
                document.getElementById('auth-screen').style.display = 'flex';
                document.getElementById('app-screen').style.display = 'none';
                showLogin();
            }
        });
    </script>
</body>
</html>
