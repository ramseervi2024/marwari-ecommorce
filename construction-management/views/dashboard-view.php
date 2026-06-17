<?php
/**
 * Construction ERP Dashboard View Template
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
    <title>Global Construction ERP - Dashboard</title>
    <!-- Modern Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #070a13;
            --card-bg: rgba(13, 19, 33, 0.75);
            --glass-border: rgba(255, 255, 255, 0.06);
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            
            --accent-orange: #f97316;
            --accent-blue: #2563eb;
            --accent-purple: #7c3aed;
            --accent-green: #10b981;
            --accent-yellow: #eab308;
            --accent-teal: #0d9488;
            --accent-red: #ef4444;
            
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
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(249, 115, 22, 0.1) 0%, rgba(7, 10, 19, 0) 70%);
            top: -150px;
            right: -150px;
            z-index: -1;
            pointer-events: none;
        }

        .ambient-glow-left {
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.08) 0%, rgba(7, 10, 19, 0) 70%);
            bottom: -100px;
            left: -100px;
            z-index: -1;
            pointer-events: none;
        }

        /* AUTH LOGIN SCREEN */
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
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            transition: var(--transition-smooth);
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .auth-logo h2 {
            font-size: 26px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--text-main), var(--accent-orange));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .auth-logo p {
            color: var(--text-muted);
            font-size: 13px;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 18px;
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
            border-color: var(--accent-orange);
            box-shadow: 0 0 10px rgba(249, 115, 22, 0.2);
            background: rgba(255, 255, 255, 0.05);
        }

        .auth-submit-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--accent-orange), var(--accent-purple));
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
            box-shadow: 0 8px 20px rgba(249, 115, 22, 0.2);
        }

        .auth-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(249, 115, 22, 0.3);
        }

        .demo-credentials-box {
            background: rgba(255, 255, 255, 0.02);
            border: 1px dashed var(--glass-border);
            border-radius: 16px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .demo-credentials-box h4 {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }

        .demo-roles-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .demo-role-btn {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            padding: 8px;
            color: var(--text-main);
            font-family: inherit;
            font-size: 10px;
            font-weight: 500;
            cursor: pointer;
            text-align: center;
            transition: var(--transition-smooth);
        }

        .demo-role-btn:hover {
            background: rgba(249, 115, 22, 0.1);
            border-color: var(--accent-orange);
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
            color: var(--accent-orange);
            text-decoration: none;
            font-weight: 500;
        }

        /* MAIN APP CONTAINER */
        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: rgba(10, 14, 23, 0.95);
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
            margin-bottom: 35px;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent-orange), var(--accent-purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            color: #fff;
        }

        .brand span {
            font-size: 17px;
            font-weight: 700;
            letter-spacing: 0.5px;
            background: linear-gradient(to right, #ffffff, #e5e7eb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .menu-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 6px;
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
            background: rgba(249, 115, 22, 0.08);
            color: var(--text-main);
            border-left: 3px solid var(--accent-orange);
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

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--accent-orange);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            flex-shrink: 0;
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

        /* Workspace Main Section */
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
            margin-bottom: 30px;
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

        /* DASHBOARD COUNTERS GRID */
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
            padding: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: var(--transition-smooth);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            border-color: rgba(249, 115, 22, 0.2);
        }

        .stat-details h3 {
            font-size: 24px;
            font-weight: 700;
            margin-top: 4px;
        }

        .stat-details p {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        /* Charts trend layout */
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
            font-size: 15px;
            font-weight: 600;
        }

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
            width: 36px;
            border-radius: 6px 6px 0 0;
            background: linear-gradient(to top, var(--accent-orange), var(--accent-purple));
            transition: height 1s ease;
            position: relative;
            height: 0px;
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
            white-space: nowrap;
            max-width: 90px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* List layout panels */
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
            margin-bottom: 30px;
        }

        .table-header-row {
            padding: 20px 24px;
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table-header-row h3 {
            font-size: 16px;
            font-weight: 600;
        }

        .table-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn {
            background: linear-gradient(135deg, var(--accent-orange), var(--accent-purple));
            border: none;
            border-radius: 10px;
            padding: 9px 18px;
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
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.25);
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

        .badge-active, .badge-approved, .badge-paid, .badge-completed {
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--accent-green);
        }

        .badge-pending, .badge-planning, .badge-unpaid {
            background: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 158, 11, 0.2);
            color: var(--accent-yellow);
        }

        .badge-blocked, .badge-cancelled, .badge-overdue {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--accent-red);
        }

        /* Modals layout */
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

        /* Toasts alerts */
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
            border-left: 4px solid var(--accent-red);
        }

        /* Form Layouts inside modals */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 15px;
        }

        .form-grid-full {
            grid-column: span 2;
        }

        select.form-input {
            appearance: none;
            background-image: url("data:image/svg+xml;utf8,<svg fill='white' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/><path d='M0 0h24v24H0z' fill='none'/></svg>");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 18px;
            padding-right: 35px;
        }
    </style>
</head>
<body>
    <div class="ambient-glow"></div>
    <div class="ambient-glow-left"></div>
    <div class="toast-box" id="toast-box"></div>

    <!-- 1. AUTH SCREEN -->
    <div class="auth-container" id="auth-screen">
        <div class="auth-card">
            <div class="auth-logo">
                <h2>Global Construction ERP</h2>
                <p>Enterprise Headless Portal & Costing Engine</p>
            </div>
            
            <div class="demo-credentials-box">
                <h4>Seeded Demo Accounts (Auto-fill)</h4>
                <div class="demo-roles-grid">
                    <button class="demo-role-btn" onclick="fillCredentials('constsuperadmin', '123456')">
                        <span class="demo-role-title">Admin</span>
                        <span class="demo-role-user">constsuperadmin</span>
                    </button>
                    <button class="demo-role-btn" onclick="fillCredentials('constprojectmanager', 'pmtest123')">
                        <span class="demo-role-title">Manager</span>
                        <span class="demo-role-user">constprojectm...</span>
                    </button>
                    <button class="demo-role-btn" onclick="fillCredentials('constsiteengineer', 'engineertest123')">
                        <span class="demo-role-title">Engineer</span>
                        <span class="demo-role-user">constsiteeng...</span>
                    </button>
                    <button class="demo-role-btn" onclick="fillCredentials('constpurchasemanager', 'purchasetest123')">
                        <span class="demo-role-title">Purchase</span>
                        <span class="demo-role-user">constpurchas...</span>
                    </button>
                    <button class="demo-role-btn" onclick="fillCredentials('constcontractor', 'contractortest123')">
                        <span class="demo-role-title">Contractor</span>
                        <span class="demo-role-user">constcontrac...</span>
                    </button>
                    <button class="demo-role-btn" onclick="fillCredentials('constaccountant', 'accountanttest123')">
                        <span class="demo-role-title">Accountant</span>
                        <span class="demo-role-user">constaccount...</span>
                    </button>
                </div>
            </div>

            <form id="login-form" onsubmit="handleLogin(event)">
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" class="form-input" placeholder="e.g. constsuperadmin" required>
                </div>
                <div class="form-group" id="password-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" class="form-input" placeholder="••••••••" required>
                </div>
                <button type="submit" class="auth-submit-btn" id="login-submit-btn">Login & Launch ERP</button>
            </form>
        </div>
    </div>

    <!-- 2. MAIN APP DASHBOARD SCREEN -->
    <div class="app-container" id="app-screen" style="display: none;">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <div>
                <div class="brand">
                    <div class="brand-icon">C</div>
                    <span>Construction ERP</span>
                </div>
                
                <ul class="menu-list">
                    <li>
                        <button class="menu-item active" onclick="switchTab('dashboard')">
                            <span>📊</span> Dashboard Overview
                        </button>
                    </li>
                    <li>
                        <button class="menu-item" onclick="switchTab('projects')">
                            <span>🏗️</span> Projects & Milestones
                        </button>
                    </li>
                    <li>
                        <button class="menu-item" onclick="switchTab('materials')">
                            <span>🧱</span> Inventory & Purchases
                        </button>
                    </li>
                    <li>
                        <button class="menu-item" onclick="switchTab('expenses')">
                            <span>💸</span> Site Expenses
                        </button>
                    </li>
                    <li>
                        <button class="menu-item" onclick="switchTab('labour')">
                            <span>👷</span> Labour & Contractors
                        </button>
                    </li>
                    <li>
                        <button class="menu-item" onclick="switchTab('costing')">
                            <span>⚖️</span> Costing & Profitability
                        </button>
                    </li>
                </ul>
            </div>

            <div class="user-profile-wrapper">
                <div class="user-profile">
                    <div class="user-profile-inner">
                        <div class="avatar" id="user-avatar">A</div>
                        <div class="user-info">
                            <h4 id="user-display-name">Super Admin</h4>
                            <p id="user-role-label">construction_super_admin</p>
                        </div>
                    </div>
                </div>
                <button class="logout-btn" onclick="handleLogout()">
                    <span>🚪</span> Sign Out
                </button>
            </div>
        </div>

        <!-- Main panel content workspace -->
        <div class="main-panel">
            <div class="header-section">
                <div class="title-group">
                    <h1 id="panel-title">Dashboard Overview</h1>
                    <p id="panel-subtitle">Welcome back to the construction cockpit portal.</p>
                </div>
                <div class="badge-live">
                    <span class="live-dot"></span>
                    API Mode: <span id="api-status-text">Live</span>
                </div>
            </div>

            <!-- DASHBOARD PANEL -->
            <div id="tab-dashboard" class="tab-panel active">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-details">
                            <p>Active Projects</p>
                            <h3 id="stat-active-projects">0</h3>
                        </div>
                        <div class="stat-icon" style="background: rgba(37, 99, 235, 0.1); color: var(--accent-blue);">🏗️</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-details">
                            <p>Labour Count</p>
                            <h3 id="stat-labour-headcount">0</h3>
                        </div>
                        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--accent-green);">👷</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-details">
                            <p>Inventory Value</p>
                            <h3 id="stat-inventory-value">₹0</h3>
                        </div>
                        <div class="stat-icon" style="background: rgba(234, 179, 8, 0.1); color: var(--accent-yellow);">🧱</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-details">
                            <p>Monthly Revenue</p>
                            <h3 id="stat-monthly-revenue">₹0</h3>
                        </div>
                        <div class="stat-icon" style="background: rgba(124, 58, 237, 0.1); color: var(--accent-purple);">💰</div>
                    </div>
                </div>

                <div class="charts-row">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Project Budgets vs Costing Analysis</h3>
                            <span style="font-size: 12px; color: var(--text-muted);">Budget vs Cost (In Lakhs)</span>
                        </div>
                        <div class="simulated-bar-chart" id="dashboard-bar-chart">
                            <!-- Populated dynamically -->
                        </div>
                    </div>
                    
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Monthly Profit Summary</h3>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 10px;">
                            <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--glass-border); padding-bottom: 8px;">
                                <span style="color: var(--text-muted);">Billed Invoices</span>
                                <span id="summary-billed" style="font-weight: 600;">₹0</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--glass-border); padding-bottom: 8px;">
                                <span style="color: var(--text-muted);">Purchase Orders</span>
                                <span id="summary-purchases" style="font-weight: 600; color: var(--accent-orange);">₹0</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--glass-border); padding-bottom: 8px;">
                                <span style="color: var(--text-muted);">Site Expenses</span>
                                <span id="summary-expenses" style="font-weight: 600; color: var(--accent-red);">₹0</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--glass-border); padding-bottom: 8px;">
                                <span style="color: var(--text-muted);">Labour Payroll</span>
                                <span id="summary-payroll" style="font-weight: 600; color: var(--accent-yellow);">₹0</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding-top: 5px;">
                                <span style="font-weight: bold;">Net Monthly Profit</span>
                                <span id="summary-net-profit" style="font-weight: bold; color: var(--accent-green);">₹0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PROJECTS PANEL -->
            <div id="tab-projects" class="tab-panel">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Projects Listing</h3>
                        <div class="table-controls">
                            <button class="btn" onclick="openModal('project')">➕ New Project</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Project Name</th>
                                <th>Client</th>
                                <th>Type</th>
                                <th>Est. Cost</th>
                                <th>Actual Cost</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="projects-table-body">
                            <!-- Populated dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Milestones Tracking</h3>
                        <div class="table-controls">
                            <button class="btn" onclick="openModal('milestone')">➕ New Milestone</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Milestone Name</th>
                                <th>Planned Date</th>
                                <th>Actual Date</th>
                                <th>Completion %</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="milestones-table-body">
                            <!-- Populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MATERIALS PANEL -->
            <div id="tab-materials" class="tab-panel">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Materials Inventory</h3>
                        <div class="table-controls">
                            <button class="btn" onclick="openModal('material')">➕ New Material</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Material Name</th>
                                <th>Unit</th>
                                <th>Available Qty</th>
                                <th>Minimum Stock</th>
                                <th>Purchase Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="materials-table-body">
                            <!-- Populated dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Material Purchases (P.O. Ledger)</h3>
                        <div class="table-controls">
                            <button class="btn" onclick="openModal('purchase')">🛒 Create Purchase PO</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Qty Ordered</th>
                                <th>Unit Rate</th>
                                <th>GST (18%)</th>
                                <th>Total Cost</th>
                                <th>PO Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="purchases-table-body">
                            <!-- Populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- EXPENSES PANEL -->
            <div id="tab-expenses" class="tab-panel">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Site Expenses Logging</h3>
                        <div class="table-controls">
                            <button class="btn" onclick="openModal('expense')">💸 Log Site Expense</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Expense Type</th>
                                <th>Amount</th>
                                <th>Expense Date</th>
                                <th>Description</th>
                                <th>Approver</th>
                            </tr>
                        </thead>
                        <tbody id="expenses-table-body">
                            <!-- Populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- LABOUR PANEL -->
            <div id="tab-labour" class="tab-panel">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Labour Workforce Heads</h3>
                        <div class="table-controls">
                            <button class="btn" onclick="openModal('labour')">➕ Add Worker</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Employee Code</th>
                                <th>Name</th>
                                <th>Trade</th>
                                <th>Daily Wage</th>
                                <th>Attendance Status</th>
                            </tr>
                        </thead>
                        <tbody id="labour-table-body">
                            <!-- Populated dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Payroll Summary Sheets</h3>
                        <div class="table-controls">
                            <button class="btn" onclick="openModal('payroll')">⚙️ Generate Payroll Slip</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Worker Name</th>
                                <th>Period</th>
                                <th>Days Worked</th>
                                <th>Reg. Earnings</th>
                                <th>OT Earnings</th>
                                <th>Total Pay</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="payroll-table-body">
                            <!-- Populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- COSTING PANEL -->
            <div id="tab-costing" class="tab-panel">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Financial Costing Breakdown (Calculated by Engine)</h3>
                        <div class="table-controls">
                            <button class="btn btn-secondary" onclick="loadCostingReports()">🔄 Recalculate Engine</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Project Code</th>
                                <th>Project Name</th>
                                <th>Estimated Value</th>
                                <th>Actual Cost</th>
                                <th>Variance</th>
                                <th>% Consumed</th>
                                <th>Profit Margin</th>
                            </tr>
                        </thead>
                        <tbody id="costing-table-body">
                            <!-- Populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- MODALS SECTION -->
    
    <!-- 1. PROJECT CREATE MODAL -->
    <div class="modal-overlay" id="modal-project">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('project')">✖</button>
            <h3 style="margin-bottom:15px;">Register New Construction Project</h3>
            <form onsubmit="submitForm(event, 'projects')">
                <div class="form-group">
                    <label>Project Name</label>
                    <input type="text" name="project_name" class="form-input" placeholder="e.g. Skyline Residency" required>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Client Name</label>
                        <input type="text" name="client_name" class="form-input" placeholder="e.g. RERA Developers">
                    </div>
                    <div class="form-group">
                        <label>Project Type</label>
                        <select name="project_type" class="form-input">
                            <option value="Residential Complexes">Residential Complexes</option>
                            <option value="Commercial Projects">Commercial Projects</option>
                            <option value="Industrial Projects">Industrial Projects</option>
                            <option value="Civil Infrastructure">Civil Infrastructure</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Location Site</label>
                    <input type="text" name="location" class="form-input" placeholder="e.g. Sector 10, Noida">
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Estimated Cost Budget (INR)</label>
                        <input type="number" name="estimated_cost" class="form-input" placeholder="e.g. 15000000" required>
                    </div>
                    <div class="form-group">
                        <label>Project Manager</label>
                        <input type="text" name="project_manager" class="form-input" placeholder="e.g. PM Sharma">
                    </div>
                </div>
                <button type="submit" class="btn" style="width:100%; margin-top:10px; padding:12px;">Create Project File</button>
            </form>
        </div>
    </div>

    <!-- 2. EXPENSE LOG MODAL -->
    <div class="modal-overlay" id="modal-expense">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('expense')">✖</button>
            <h3 style="margin-bottom:15px;">Log Site Expense Voucher</h3>
            <form onsubmit="submitForm(event, 'site-expenses')">
                <div class="form-group">
                    <label>Linked Project ID</label>
                    <select name="project_id" id="expense-project-select" class="form-input" required>
                        <!-- Filled dynamically -->
                    </select>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Expense Type</label>
                        <select name="expense_type" class="form-input" required>
                            <option value="Fuel">Fuel</option>
                            <option value="Equipment Rent">Equipment Rent</option>
                            <option value="Site Maintenance">Site Maintenance</option>
                            <option value="Electricity">Electricity</option>
                            <option value="Water Supply">Water Supply</option>
                            <option value="Transportation">Transportation</option>
                            <option value="Accommodation">Labour Accommodation</option>
                            <option value="Miscellaneous">Miscellaneous</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Voucher Amount (INR)</label>
                        <input type="number" name="amount" class="form-input" placeholder="e.g. 45000" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description details</label>
                    <textarea name="description" class="form-input" rows="3" placeholder="Diesel fuel for backup power..."></textarea>
                </div>
                <button type="submit" class="btn" style="width:100%; margin-top:10px; padding:12px;">Log Expense Voucher</button>
            </form>
        </div>
    </div>

    <!-- 3. LABOUR CREATE MODAL -->
    <div class="modal-overlay" id="modal-labour">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('labour')">✖</button>
            <h3 style="margin-bottom:15px;">Add Worker Profile</h3>
            <form onsubmit="submitForm(event, 'labours')">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-input" placeholder="e.g. Ramu Yadav" required>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Work Trade Specialization</label>
                        <select name="trade" class="form-input" required>
                            <option value="Mason">Mason</option>
                            <option value="Carpenter">Carpenter</option>
                            <option value="Electrician">Electrician</option>
                            <option value="Plumber">Plumber</option>
                            <option value="Painter">Painter</option>
                            <option value="Helper">General Helper</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Daily Wage Rate (INR)</label>
                        <input type="number" name="daily_wage" class="form-input" placeholder="e.g. 600" required>
                    </div>
                </div>
                <button type="submit" class="btn" style="width:100%; margin-top:10px; padding:12px;">Add Worker File</button>
            </form>
        </div>
    </div>

    <!-- MOCK SIMULATION ENGINE DATA FOR CLIENTS -->
    <script>
        const MOCK_DATA = {
            cards: {
                active_projects: 2,
                completed_projects: 1,
                today_site_expenses: 35000.00,
                labour_headcount: 12,
                inventory_value: 692000.00,
                pending_payments: 588200.00,
                monthly_revenue: 10000000.00,
                monthly_profit: 8765200.00
            },
            projects: [
                { id: 1, project_code: "PRJ-2026-001", project_name: "Marwari Heights Complex", client_name: "Marwari Realties Ltd", project_type: "Residential Complexes", estimated_cost: 50000000.00, actual_cost: 12500000.00, status: "Active" },
                { id: 2, project_code: "PRJ-2026-002", project_name: "Metro Plaza Commercial Mall", client_name: "Metro Infra Developers", project_type: "Commercial Projects", estimated_cost: 120000000.00, actual_cost: 0.00, status: "Planning" }
            ],
            milestones: [
                { id: 1, project_id: 1, milestone_name: "Foundation & Excavation Completion", planned_date: "2026-04-15", actual_date: "2026-04-20", completion_percentage: 100.00, status: "Completed" },
                { id: 2, project_id: 1, milestone_name: "Ground Floor Slab Pouring", planned_date: "2026-08-30", actual_date: null, completion_percentage: 45.00, status: "In-Progress" }
            ],
            materials: [
                { id: 1, material_code: "MAT-CEM-OPC", material_name: "UltraTech OPC 53 Grade Cement", unit: "Bags", available_quantity: 1500.00, minimum_stock: 500.00, purchase_price: 420.00, status: "ACTIVE" },
                { id: 2, material_code: "MAT-STL-12M", material_name: "Tata Tiscon TMT Steel Rebars 12mm", unit: "Tonnes", available_quantity: 25.00, minimum_stock: 5.00, purchase_price: 62000.00, status: "ACTIVE" }
            ],
            purchases: [
                { id: 1, purchase_order_number: "PO-2026-0001", quantity: 1000.00, rate: 410.00, gst_amount: 73800.00, total_amount: 483800.00, purchase_date: "2026-05-10", status: "Approved" }
            ],
            expenses: [
                { id: 1, expense_type: "Equipment Rent", amount: 120000.00, expense_date: "2026-06-01", description: "Monthly rental for JCB excavator & Concrete mixer truck", approved_by: "Admin" },
                { id: 2, expense_type: "Fuel", amount: 35000.00, expense_date: "2026-06-14", description: "Diesel fuel purchase for generators", approved_by: "PM" }
            ],
            labour: [
                { id: 1, employee_code: "LAB-2026-0001", name: "Ramu Yadav", trade: "Mason", daily_wage: 650.00, attendance_status: "PRESENT" },
                { id: 2, employee_code: "LAB-2026-0002", name: "Shyam Lal", trade: "Helper", daily_wage: 450.00, attendance_status: "PRESENT" }
            ],
            payroll: [
                { id: 1, name: "Ramu Yadav", period: "2026-06-01 to 2026-06-07", total_days_worked: 6, regular_earnings: 3900.00, overtime_earnings: 487.50, total_earnings: 4387.50, payment_status: "Paid" }
            ]
        };

        let activeToken = localStorage.getItem('construction_auth_token') || null;
        let currentUser = JSON.parse(localStorage.getItem('construction_current_user')) || null;
        let activeTab = 'dashboard';
        let simulationMode = false;

        // Auto-fill login credentials box
        function fillCredentials(username, password) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
        }

        // Show toast alert
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-box');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `<span>${type === 'success' ? '✔️' : '❌'}</span> <span>${message}</span>`;
            container.appendChild(toast);
            
            // Trigger transition reflow
            setTimeout(() => toast.classList.add('show'), 50);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Handle Login Submission
        async function handleLogin(event) {
            event.preventDefault();
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const btn = document.getElementById('login-submit-btn');

            btn.disabled = true;
            btn.innerText = 'Connecting to REST API...';

            try {
                // Fetch credentials via REST URL
                const response = await fetch('/wp-json/construction-management/v1/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password })
                });
                
                const res = await response.json();
                
                if (res.success) {
                    activeToken = res.data.token;
                    currentUser = res.data.user;
                    localStorage.setItem('construction_auth_token', activeToken);
                    localStorage.setItem('construction_current_user', JSON.stringify(currentUser));
                    
                    showToast('Authenticated via WordPress JWT successfully.', 'success');
                    simulationMode = false;
                    launchDashboard();
                } else {
                    throw new Error(res.message || 'Login failed.');
                }
            } catch (err) {
                console.warn('API error, falling back to simulated credentials:', err.message);
                
                // Demo credentials verification in fallback mode
                const validDemos = {
                    'constsuperadmin': '123456',
                    'constprojectmanager': 'pmtest123',
                    'constsiteengineer': 'engineertest123',
                    'constpurchasemanager': 'purchasetest123',
                    'constcontractor': 'contractortest123',
                    'constaccountant': 'accountanttest123'
                };

                if (validDemos[username] && validDemos[username] === password) {
                    activeToken = 'simulated_jwt_token_payload';
                    currentUser = {
                        username: username,
                        name: username.replace('const', 'Construction '),
                        role: `construction_${username.replace('const', '').replace(/([A-Z])/g, '_$1').toLowerCase()}`
                    };
                    simulationMode = true;
                    showToast('Launched in Simulated Sandbox Mode.', 'success');
                    launchDashboard();
                } else {
                    showToast('Invalid credentials. Check seeded codes.', 'error');
                }
            } finally {
                btn.disabled = false;
                btn.innerText = 'Login & Launch ERP';
            }
        }

        // Switch Tabs panels
        function switchTab(tabId) {
            activeTab = tabId;
            
            // Active state side buttons
            document.querySelectorAll('.menu-item').forEach(btn => {
                btn.classList.remove('active');
            });
            event.currentTarget.classList.add('active');

            // Active state panels
            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.remove('active');
            });
            
            const targetPanel = document.getElementById(`tab-${tabId}`);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }

            // Adjust titles
            const titles = {
                'dashboard': 'Dashboard Overview',
                'projects': 'Projects & Milestones',
                'materials': 'Inventory & Material Purchases',
                'expenses': 'Site Expenses',
                'labour': 'Labour Workers & Contractors',
                'costing': 'Project Costing Engine'
            };
            document.getElementById('panel-title').innerText = titles[tabId] || 'Dashboard';
            
            loadTabData(tabId);
        }

        // Open/Close Modals
        function openModal(id) {
            const overlay = document.getElementById(`modal-${id}`);
            if (overlay) overlay.classList.add('show');
        }

        function closeModal(id) {
            const overlay = document.getElementById(`modal-${id}`);
            if (overlay) overlay.classList.remove('show');
        }

        // Fill selects for projects dynamically in forms
        function populateProjectSelects() {
            const select = document.getElementById('expense-project-select');
            if (!select) return;
            
            select.innerHTML = '';
            
            const projects = simulationMode ? MOCK_DATA.projects : [];
            projects.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.innerText = `${p.project_code} - ${p.project_name}`;
                select.appendChild(opt);
            });
        }

        // Load Tab Data
        async function loadTabData(tabId) {
            if (simulationMode) {
                renderSimulatedData(tabId);
                return;
            }

            const headers = { 'Authorization': `Bearer ${activeToken}` };
            
            try {
                if (tabId === 'dashboard') {
                    const res = await (await fetch('/wp-json/construction-management/v1/dashboard', { headers })).json();
                    if (res.success) {
                        updateDashboardStats(res.data.cards);
                        renderChart(res.data.analytics.project_progress);
                        updateMonthlySummary(res.data.analytics.monthly_breakdown);
                    }
                } else if (tabId === 'projects') {
                    const resProj = await (await fetch('/wp-json/construction-management/v1/projects', { headers })).json();
                    const resMilestone = await (await fetch('/wp-json/construction-management/v1/milestones', { headers })).json();
                    if (resProj.success) renderProjectsTable(resProj.data.data);
                    if (resMilestone.success) renderMilestonesTable(resMilestone.data.data);
                } else if (tabId === 'materials') {
                    const resMat = await (await fetch('/wp-json/construction-management/v1/materials', { headers })).json();
                    const resPurch = await (await fetch('/wp-json/construction-management/v1/purchases', { headers })).json();
                    if (resMat.success) renderMaterialsTable(resMat.data.data);
                    if (resPurch.success) renderPurchasesTable(resPurch.data.data);
                } else if (tabId === 'expenses') {
                    const resExp = await (await fetch('/wp-json/construction-management/v1/site-expenses', { headers })).json();
                    if (resExp.success) renderExpensesTable(resExp.data.data);
                } else if (tabId === 'labour') {
                    const resLab = await (await fetch('/wp-json/construction-management/v1/labours', { headers })).json();
                    const resPay = await (await fetch('/wp-json/construction-management/v1/payroll', { headers })).json();
                    if (resLab.success) renderLabourTable(resLab.data.data);
                    if (resPay.success) renderPayrollTable(resPay.data.data);
                } else if (tabId === 'costing') {
                    loadCostingReports();
                }
            } catch (err) {
                console.error('API load error, switching to simulation mode:', err.message);
                simulationMode = true;
                document.getElementById('api-status-text').innerText = 'Simulated Sandbox';
                document.getElementById('api-status-text').style.color = 'var(--accent-yellow)';
                renderSimulatedData(tabId);
            }
        }

        // Render Tables from array values
        function renderProjectsTable(arr) {
            const body = document.getElementById('projects-table-body');
            body.innerHTML = arr.map(p => `
                <tr>
                    <td><strong>${p.project_code}</strong></td>
                    <td>${p.project_name}</td>
                    <td>${p.client_name || '-'}</td>
                    <td>${p.project_type || '-'}</td>
                    <td>₹${parseFloat(p.estimated_cost).toLocaleString()}</td>
                    <td>₹${parseFloat(p.actual_cost).toLocaleString()}</td>
                    <td><span class="badge badge-${p.status.toLowerCase()}">${p.status}</span></td>
                </tr>
            `).join('');
        }

        function renderMilestonesTable(arr) {
            const body = document.getElementById('milestones-table-body');
            body.innerHTML = arr.map(m => `
                <tr>
                    <td>${m.milestone_name}</td>
                    <td>${m.planned_date || '-'}</td>
                    <td>${m.actual_date || '-'}</td>
                    <td>${parseFloat(m.completion_percentage)}%</td>
                    <td><span class="badge badge-${m.status.toLowerCase()}">${m.status}</span></td>
                </tr>
            `).join('');
        }

        function renderMaterialsTable(arr) {
            const body = document.getElementById('materials-table-body');
            body.innerHTML = arr.map(m => `
                <tr>
                    <td><strong>${m.material_code}</strong></td>
                    <td>${m.material_name}</td>
                    <td>${m.unit}</td>
                    <td>${parseFloat(m.available_quantity)}</td>
                    <td>${parseFloat(m.minimum_stock)}</td>
                    <td>₹${parseFloat(m.purchase_price)}</td>
                    <td><span class="badge badge-${m.status.toLowerCase()}">${m.status}</span></td>
                </tr>
            `).join('');
        }

        function renderPurchasesTable(arr) {
            const body = document.getElementById('purchases-table-body');
            body.innerHTML = arr.map(p => `
                <tr>
                    <td><strong>${p.purchase_order_number}</strong></td>
                    <td>${parseFloat(p.quantity)}</td>
                    <td>₹${parseFloat(p.rate).toLocaleString()}</td>
                    <td>₹${parseFloat(p.gst_amount).toLocaleString()}</td>
                    <td>₹${parseFloat(p.total_amount).toLocaleString()}</td>
                    <td>${p.purchase_date}</td>
                    <td><span class="badge badge-${p.status.toLowerCase()}">${p.status}</span></td>
                </tr>
            `).join('');
        }

        function renderExpensesTable(arr) {
            const body = document.getElementById('expenses-table-body');
            body.innerHTML = arr.map(e => `
                <tr>
                    <td><strong>${e.expense_type}</strong></td>
                    <td>₹${parseFloat(e.amount).toLocaleString()}</td>
                    <td>${e.expense_date}</td>
                    <td>${e.description || '-'}</td>
                    <td>${e.approved_by || '-'}</td>
                </tr>
            `).join('');
        }

        function renderLabourTable(arr) {
            const body = document.getElementById('labour-table-body');
            body.innerHTML = arr.map(l => `
                <tr>
                    <td><strong>${l.employee_code}</strong></td>
                    <td>${l.name}</td>
                    <td>${l.trade}</td>
                    <td>₹${parseFloat(l.daily_wage)}</td>
                    <td><span class="badge badge-${l.attendance_status.toLowerCase()}">${l.attendance_status}</span></td>
                </tr>
            `).join('');
        }

        function renderPayrollTable(arr) {
            const body = document.getElementById('payroll-table-body');
            body.innerHTML = arr.map(p => `
                <tr>
                    <td><strong>${p.name || 'Worker'}</strong></td>
                    <td>${p.period}</td>
                    <td>${p.total_days_worked} Days</td>
                    <td>₹${parseFloat(p.regular_earnings).toLocaleString()}</td>
                    <td>₹${parseFloat(p.overtime_earnings).toLocaleString()}</td>
                    <td>₹${parseFloat(p.total_earnings).toLocaleString()}</td>
                    <td><span class="badge badge-${p.payment_status.toLowerCase()}">${p.payment_status}</span></td>
                </tr>
            `).join('');
        }

        async function loadCostingReports() {
            if (simulationMode) {
                const body = document.getElementById('costing-table-body');
                body.innerHTML = `
                    <tr>
                        <td><strong>PRJ-2026-001</strong></td>
                        <td>Marwari Heights Complex</td>
                        <td>₹5,00,00,000</td>
                        <td>₹1,25,00,000</td>
                        <td>₹3,75,00,000</td>
                        <td>25%</td>
                        <td><span class="badge badge-approved" style="color:var(--accent-green)">87.65% Margin</span></td>
                    </tr>
                `;
                return;
            }
            
            try {
                const res = await (await fetch('/wp-json/construction-management/v1/reports/profitability', {
                    headers: { 'Authorization': `Bearer ${activeToken}` }
                })).json();
                if (res.success) {
                    const body = document.getElementById('costing-table-body');
                    body.innerHTML = res.data.map(r => `
                        <tr>
                            <td><strong>${r.project_code}</strong></td>
                            <td>${r.project_name}</td>
                            <td>₹${parseFloat(r.estimated_value).toLocaleString()}</td>
                            <td>₹${parseFloat(r.total_cost).toLocaleString()}</td>
                            <td>₹${(parseFloat(r.estimated_value) - parseFloat(r.total_cost)).toLocaleString()}</td>
                            <td>${r.estimated_value > 0 ? round((r.total_cost / r.estimated_value)*100, 2) : 0}%</td>
                            <td><span class="badge badge-approved">${r.profit_margin_percentage}% Margin</span></td>
                        </tr>
                    `).join('');
                }
            } catch (err) {
                console.error(err);
            }
        }

        // Render Simulated Fallback sandbox details
        function renderSimulatedData(tabId) {
            populateProjectSelects();
            if (tabId === 'dashboard') {
                updateDashboardStats(MOCK_DATA.cards);
                renderChart([
                    { project_name: 'Marwari Heights', estimated_cost: 50000000, actual_cost: 12500000 },
                    { project_name: 'Metro Mall Plaza', estimated_cost: 120000000, actual_cost: 45000000 }
                ]);
                updateMonthlySummary({
                    revenue: 10000000,
                    expenses: { purchases: 483800, site_expenses: 155000, payroll: 4387.50, total: 643187.50 },
                    profit: 9356812.50
                });
            } else if (tabId === 'projects') {
                renderProjectsTable(MOCK_DATA.projects);
                renderMilestonesTable(MOCK_DATA.milestones);
            } else if (tabId === 'materials') {
                renderMaterialsTable(MOCK_DATA.materials);
                renderPurchasesTable(MOCK_DATA.purchases);
            } else if (tabId === 'expenses') {
                renderExpensesTable(MOCK_DATA.expenses);
            } else if (tabId === 'labour') {
                renderLabourTable(MOCK_DATA.labour);
                renderPayrollTable(MOCK_DATA.payroll);
            } else if (tabId === 'costing') {
                loadCostingReports();
            }
        }

        // Chart render helper
        function renderChart(arr) {
            const chart = document.getElementById('dashboard-bar-chart');
            chart.innerHTML = '';
            
            arr.forEach(proj => {
                const budgetLakhs = Math.round(proj.estimated_cost / 100000);
                const costLakhs = Math.round(proj.actual_cost / 100000);
                
                const percentage = proj.estimated_cost > 0 ? (proj.actual_cost / proj.estimated_cost) * 100 : 0;
                
                const col = document.createElement('div');
                col.className = 'bar-container';
                col.innerHTML = `
                    <div style="display:flex; gap: 4px; align-items:flex-end;">
                        <div class="chart-bar" style="height: ${Math.min(180, budgetLakhs * 1.2)}px; background:rgba(255,255,255,0.06); border: 1px solid var(--glass-border);" data-value="${budgetLakhs}L"></div>
                        <div class="chart-bar" style="height: ${Math.min(180, costLakhs * 1.2)}px; background:linear-gradient(to top, var(--accent-orange), var(--accent-red));" data-value="${costLakhs}L"></div>
                    </div>
                    <div class="bar-label">${proj.project_name}</div>
                `;
                chart.appendChild(col);
            });
        }

        // Update top-level dashboard metrics
        function updateDashboardStats(cards) {
            document.getElementById('stat-active-projects').innerText = cards.active_projects;
            document.getElementById('stat-labour-headcount').innerText = cards.labour_headcount;
            document.getElementById('stat-inventory-value').innerText = `₹${parseFloat(cards.inventory_value).toLocaleString()}`;
            document.getElementById('stat-monthly-revenue').innerText = `₹${parseFloat(cards.monthly_revenue).toLocaleString()}`;
        }

        function updateMonthlySummary(m) {
            document.getElementById('summary-billed').innerText = `₹${parseFloat(m.revenue).toLocaleString()}`;
            document.getElementById('summary-purchases').innerText = `₹${parseFloat(m.expenses.purchases).toLocaleString()}`;
            document.getElementById('summary-expenses').innerText = `₹${parseFloat(m.expenses.site_expenses).toLocaleString()}`;
            document.getElementById('summary-payroll').innerText = `₹${parseFloat(m.expenses.payroll).toLocaleString()}`;
            document.getElementById('summary-net-profit').innerText = `₹${parseFloat(m.profit).toLocaleString()}`;
        }

        // Handle Add submission forms inside modals
        async function submitForm(event, endpoint) {
            event.preventDefault();
            const form = event.target;
            const data = {};
            
            new FormData(form).forEach((val, key) => {
                data[key] = val;
            });

            if (simulationMode) {
                showToast('Record created in sandbox state.', 'success');
                form.reset();
                
                // Add to mock dataset dynamically to keep the sandbox UI feeling alive!
                if (endpoint === 'projects') {
                    const newProj = {
                        id: MOCK_DATA.projects.length + 1,
                        project_code: `PRJ-2026-00${MOCK_DATA.projects.length + 1}`,
                        project_name: data.project_name,
                        client_name: data.client_name,
                        project_type: data.project_type,
                        estimated_cost: parseFloat(data.estimated_cost),
                        actual_cost: 0,
                        status: 'Active'
                    };
                    MOCK_DATA.projects.push(newProj);
                    MOCK_DATA.cards.active_projects++;
                    closeModal('project');
                } else if (endpoint === 'site-expenses') {
                    const newExp = {
                        id: MOCK_DATA.expenses.length + 1,
                        expense_type: data.expense_type,
                        amount: parseFloat(data.amount),
                        expense_date: data.expense_date || '2026-06-17',
                        description: data.description,
                        approved_by: 'PM'
                    };
                    MOCK_DATA.expenses.push(newExp);
                    MOCK_DATA.cards.today_site_expenses += parseFloat(data.amount);
                    closeModal('expense');
                } else if (endpoint === 'labours') {
                    const newLab = {
                        id: MOCK_DATA.labour.length + 1,
                        employee_code: `LAB-2026-000${MOCK_DATA.labour.length + 1}`,
                        name: data.name,
                        trade: data.trade,
                        daily_wage: parseFloat(data.daily_wage),
                        attendance_status: 'PRESENT'
                    };
                    MOCK_DATA.labour.push(newLab);
                    MOCK_DATA.cards.labour_headcount++;
                    closeModal('labour');
                }

                renderSimulatedData(activeTab);
                return;
            }

            try {
                const response = await fetch(`/wp-json/construction-management/v1/${endpoint}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${activeToken}`
                    },
                    body: JSON.stringify(data)
                });
                const res = await response.json();
                if (res.success) {
                    showToast('Record registered successfully.', 'success');
                    form.reset();
                    // Close matching modal
                    if (endpoint === 'projects') closeModal('project');
                    if (endpoint === 'site-expenses') closeModal('expense');
                    if (endpoint === 'labours') closeModal('labour');
                    
                    loadTabData(activeTab);
                } else {
                    showToast(res.message || 'Operation failed.', 'error');
                }
            } catch (err) {
                showToast(err.message, 'error');
            }
        }

        // Launch Dashboard Dashboard View
        function launchDashboard() {
            document.getElementById('auth-screen').style.display = 'none';
            document.getElementById('app-screen').style.display = 'flex';
            
            // Set profile details
            document.getElementById('user-avatar').innerText = currentUser.name.charAt(0);
            document.getElementById('user-display-name').innerText = currentUser.name;
            document.getElementById('user-role-label').innerText = currentUser.role;

            populateProjectSelects();
            loadTabData('dashboard');
        }

        // Sign out
        function handleLogout() {
            localStorage.removeItem('construction_auth_token');
            localStorage.removeItem('construction_current_user');
            activeToken = null;
            currentUser = null;
            
            document.getElementById('app-screen').style.display = 'none';
            document.getElementById('auth-screen').style.display = 'flex';
            showToast('Signed out successfully.');
        }

        // Auto launch if token exists
        window.addEventListener('load', () => {
            if (activeToken && currentUser) {
                launchDashboard();
            }
        });
    </script>
</body>
</html>
