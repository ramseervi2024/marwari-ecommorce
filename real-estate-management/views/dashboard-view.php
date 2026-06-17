<?php
/**
 * Real Estate CRM + ERP Dashboard View Template
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
    <title>Real Estate CRM & ERP - Dashboard</title>
    <!-- Modern Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #090d16;
            --card-bg: rgba(18, 25, 41, 0.75);
            --glass-border: rgba(255, 255, 255, 0.05);
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            
            --accent-gold: #d97706;
            --accent-orange: #f97316;
            --accent-blue: #3b82f6;
            --accent-purple: #8b5cf6;
            --accent-green: #10b981;
            --accent-teal: #14b8a6;
            --accent-red: #ef4444;
            --accent-yellow: #eab308;
            
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

        /* Ambient Background Glow */
        .ambient-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(217, 119, 6, 0.08) 0%, rgba(9, 13, 22, 0) 70%);
            top: -200px;
            right: -100px;
            z-index: -1;
            pointer-events: none;
        }

        .ambient-glow-left {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.06) 0%, rgba(9, 13, 22, 0) 70%);
            bottom: -100px;
            left: -100px;
            z-index: -1;
            pointer-events: none;
        }

        /* AUTH CONTAINER */
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
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 28px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.6);
            transition: var(--transition-smooth);
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .auth-logo h2 {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--text-main), var(--accent-orange));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .auth-logo p {
            color: var(--text-muted);
            font-size: 13px;
            margin-top: 6px;
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

        .form-input, .form-select, .form-textarea {
            width: 100%;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 12px 16px;
            color: var(--text-main);
            font-family: inherit;
            font-size: 14px;
            transition: var(--transition-smooth);
            outline: none;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--accent-orange);
            box-shadow: 0 0 10px rgba(249, 115, 22, 0.15);
            background: rgba(255, 255, 255, 0.04);
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
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
            background: rgba(255, 255, 255, 0.01);
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
            background: rgba(255, 255, 255, 0.02);
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
            background: rgba(249, 115, 22, 0.08);
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

        /* MAIN APP CONTAINER */
        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: rgba(10, 16, 27, 0.96);
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
            margin-bottom: 30px;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--accent-orange), var(--accent-purple));
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
            background: linear-gradient(to right, #ffffff, #e5e7eb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .menu-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 4px;
            overflow-y: auto;
            max-height: calc(100vh - 220px);
            padding-right: 5px;
        }

        .menu-list::-webkit-scrollbar {
            width: 4px;
        }
        .menu-list::-webkit-scrollbar-thumb {
            background: var(--glass-border);
            border-radius: 2px;
        }

        .menu-item {
            width: 100%;
            background: transparent;
            border: none;
            border-radius: 10px;
            padding: 10px 14px;
            color: var(--text-muted);
            font-family: inherit;
            font-size: 13.5px;
            font-weight: 500;
            cursor: pointer;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition-smooth);
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(249, 115, 22, 0.06);
            color: var(--text-main);
            border-left: 3px solid var(--accent-orange);
            padding-left: 11px;
        }

        .user-profile-wrapper {
            margin-top: auto;
            border-top: 1px solid var(--glass-border);
            padding-top: 15px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.01);
            padding: 10px;
            border-radius: 14px;
            margin-bottom: 12px;
        }

        .avatar {
            width: 36px;
            height: 36px;
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

        .user-info {
            overflow: hidden;
        }

        .user-info h4 {
            font-size: 12.5px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-info p {
            font-size: 9px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        .logout-btn {
            width: 100%;
            background: rgba(239, 68, 68, 0.04);
            border: 1px solid rgba(239, 68, 68, 0.12);
            border-radius: 10px;
            padding: 8px;
            color: #ef4444;
            font-family: inherit;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: var(--transition-smooth);
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.12);
        }

        /* MAIN PANEL */
        .main-panel {
            flex-grow: 1;
            padding: 35px;
            overflow-y: auto;
            max-height: 100vh;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .title-group h1 {
            font-size: 24px;
            font-weight: 700;
        }

        .title-group p {
            color: var(--text-muted);
            font-size: 13.5px;
            margin-top: 4px;
        }

        .badge-status {
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--accent-green);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .live-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--accent-green);
            box-shadow: 0 0 6px var(--accent-green);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.9); opacity: 0.6; }
            50% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.6; }
        }

        /* STATS CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 18px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: var(--transition-smooth);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: rgba(249, 115, 22, 0.15);
        }

        .stat-details h3 {
            font-size: 22px;
            font-weight: 700;
            margin-top: 4px;
        }

        .stat-details p {
            font-size: 10px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            background: rgba(255, 255, 255, 0.02);
            color: var(--accent-orange);
        }

        /* ANALYTICS CHARTS Row */
        .charts-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 18px;
            margin-bottom: 25px;
        }

        .chart-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 18px;
            padding: 20px;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .chart-header h3 {
            font-size: 14.5px;
            font-weight: 600;
        }

        .simulated-bar-chart {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            height: 180px;
            padding: 8px 0;
            border-bottom: 1px solid var(--glass-border);
        }

        .bar-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-grow: 1;
        }

        .chart-bar {
            width: 28px;
            border-radius: 4px 4px 0 0;
            background: linear-gradient(to top, var(--accent-orange), var(--accent-purple));
            transition: height 0.8s ease;
            position: relative;
            height: 0px;
        }

        .chart-bar::after {
            content: attr(data-value);
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 9px;
            font-weight: 600;
            color: var(--text-main);
        }

        .bar-label {
            font-size: 10px;
            color: var(--text-muted);
            margin-top: 6px;
            text-align: center;
            white-space: nowrap;
            max-width: 80px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* TAB PANELS */
        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }

        .table-container {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
            margin-bottom: 25px;
        }

        .table-header-row {
            padding: 16px 20px;
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .table-header-row h3 {
            font-size: 15px;
            font-weight: 600;
        }

        .table-controls {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn {
            background: linear-gradient(135deg, var(--accent-orange), var(--accent-purple));
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            color: #fff;
            font-family: inherit;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-smooth);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.15);
            color: #ef4444;
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.15);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .data-table th {
            padding: 14px 20px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--glass-border);
        }

        .data-table td {
            padding: 14px 20px;
            font-size: 13px;
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
            padding: 3px 6px;
            border-radius: 5px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-active, .badge-approved, .badge-paid, .badge-completed, .badge-available, .badge-confirmed {
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--accent-green);
        }

        .badge-pending, .badge-planning, .badge-unpaid, .badge-reserved {
            background: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 158, 11, 0.2);
            color: var(--accent-yellow);
        }

        .badge-blocked, .badge-cancelled, .badge-overdue, .badge-sold, .badge-lost {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--accent-red);
        }

        /* MODAL OVERLAYS */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
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
            border-radius: 20px;
            padding: 30px;
            width: 100%;
            max-width: 550px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.5);
            position: relative;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }

        .modal-overlay.show .modal-card {
            transform: translateY(0);
        }

        .modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--glass-border);
            width: 30px;
            height: 30px;
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
            background: rgba(255, 255, 255, 0.06);
        }

        /* TOAST NOTIFICATIONS */
        .toast-box {
            position: fixed;
            bottom: 25px;
            right: 25px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 200;
        }

        .toast {
            background: #0d121f;
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            padding: 14px 18px;
            color: var(--text-main);
            font-size: 13.5px;
            font-weight: 500;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateY(15px);
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

        /* GRID FOR FORMS */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <div class="ambient-glow"></div>
    <div class="ambient-glow-left"></div>

    <!-- TOAST ALERTS BOX -->
    <div class="toast-box" id="toastBox"></div>

    <!-- AUTHENTICATION FORM -->
    <div class="auth-container" id="authSection">
        <div class="auth-card">
            <div class="auth-logo">
                <h2>REAL ESTATE ERP</h2>
                <p>Enterprise CRM & Financial Portals Gateway</p>
            </div>

            <!-- DEMO LOGIN QUICK CREDENTIALS -->
            <div class="demo-credentials-box">
                <h4>Preview Roles (Click to Quick-Login)</h4>
                <div class="demo-roles-grid">
                    <button class="demo-role-btn" onclick="quickLogin('resuperadmin')">
                        <span class="demo-role-title">Admin</span>
                        <span class="demo-role-user">Super Admin</span>
                    </button>
                    <button class="demo-role-btn" onclick="quickLogin('remanager')">
                        <span class="demo-role-title">Manager</span>
                        <span class="demo-role-user">Sales Manager</span>
                    </button>
                    <button class="demo-role-btn" onclick="quickLogin('reexecutive')">
                        <span class="demo-role-title">Executive</span>
                        <span class="demo-role-user">Sales Exec</span>
                    </button>
                    <button class="demo-role-btn" onclick="quickLogin('rebroker')">
                        <span class="demo-role-title">Broker</span>
                        <span class="demo-role-user">Broker Partner</span>
                    </button>
                    <button class="demo-role-btn" onclick="quickLogin('reaccount')">
                        <span class="demo-role-title">Accountant</span>
                        <span class="demo-role-user">Accountant</span>
                    </button>
                    <button class="demo-role-btn" onclick="toggleSandboxMode()">
                        <span class="demo-role-title" style="color: var(--accent-orange);">Sandbox</span>
                        <span class="demo-role-user">Offline Demo</span>
                    </button>
                </div>
            </div>

            <div id="loginFormBlock">
                <div class="form-group">
                    <label for="loginUser">Username or Email Address</label>
                    <input type="text" id="loginUser" class="form-input" placeholder="e.g. resuperadmin">
                </div>
                <div class="form-group">
                    <label for="loginPass">Password</label>
                    <input type="password" id="loginPass" class="form-input" placeholder="••••••">
                </div>
                <button class="auth-submit-btn" onclick="submitPasswordLogin()">Access Portal</button>
            </div>
        </div>
    </div>

    <!-- MAIN APP WRAPPER -->
    <div class="app-container" id="appSection" style="display: none;">
        <!-- SIDEBAR MENU -->
        <aside class="sidebar">
            <div>
                <div class="brand">
                    <div class="brand-icon">RE</div>
                    <span>REAL ESTATE ERP</span>
                </div>
                <ul class="menu-list">
                    <li><button class="menu-item active" onclick="switchTab('dashboard')">📊 Dashboard</button></li>
                    <li><button class="menu-item" onclick="switchTab('projects')">🏗️ Projects</button></li>
                    <li><button class="menu-item" onclick="switchTab('properties')">🏢 Properties</button></li>
                    <li><button class="menu-item" onclick="switchTab('leads')">🎯 Leads Pipeline</button></li>
                    <li><button class="menu-item" onclick="switchTab('visits')">🚗 Site Visits</button></li>
                    <li><button class="menu-item" onclick="switchTab('customers')">👥 Customers</button></li>
                    <li><button class="menu-item" onclick="switchTab('bookings')">🔑 Bookings</button></li>
                    <li><button class="menu-item" onclick="switchTab('payments')">💵 Payment Schedules</button></li>
                    <li><button class="menu-item" onclick="switchTab('brokers')">🤝 Brokers & Comms</button></li>
                    <li><button class="menu-item" onclick="switchTab('pipeline')">📈 Sales Funnel</button></li>
                    <li><button class="menu-item" onclick="switchTab('registrations')">📜 Registrations</button></li>
                    <li><button class="menu-item" onclick="switchTab('reports')">📑 Financial Reports</button></li>
                    <li><button class="menu-item" onclick="switchTab('settings')">⚙️ Settings</button></li>
                </ul>
            </div>

            <div class="user-profile-wrapper">
                <div class="user-profile">
                    <div class="avatar" id="userAvatar">A</div>
                    <div class="user-info">
                        <h4 id="userName">Real Estate Admin</h4>
                        <p id="userRole">Super Admin</p>
                    </div>
                </div>
                <button class="logout-btn" onclick="logout()">Exit System</button>
            </div>
        </aside>

        <!-- MAIN VIEW WORKSPACE -->
        <main class="main-panel">
            <header class="header-section">
                <div class="title-group">
                    <h1 id="pageTitle">Analytics Overview</h1>
                    <p id="pageSub">Performance KPI monitoring and conversion trends</p>
                </div>
                <div class="badge-status">
                    <div class="live-dot"></div>
                    <span id="connStatus">Live Connection</span>
                </div>
            </header>

            <!-- TAB PANELS CONTENT -->

            <!-- DASHBOARD TAB -->
            <div class="tab-panel active" id="tab-dashboard">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-details">
                            <p>New Leads</p>
                            <h3 id="stat-new-leads">12</h3>
                        </div>
                        <div class="stat-icon">🎯</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-details">
                            <p>Properties Available</p>
                            <h3 id="stat-properties">45</h3>
                        </div>
                        <div class="stat-icon">🏢</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-details">
                            <p>Bookings (Month)</p>
                            <h3 id="stat-bookings">8</h3>
                        </div>
                        <div class="stat-icon">🔑</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-details">
                            <p>Collections (Total)</p>
                            <h3 id="stat-revenue">₹12.5M</h3>
                        </div>
                        <div class="stat-icon">💵</div>
                    </div>
                </div>

                <div class="charts-row">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Revenue Collections Trend (Last 6 Months)</h3>
                            <span class="badge badge-active">Actuals</span>
                        </div>
                        <div class="simulated-bar-chart" id="revenueTrendChart">
                            <!-- Populated dynamically -->
                        </div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Lead Conversion Rate</h3>
                        </div>
                        <div style="display:flex; align-items:center; justify-content:center; height:180px; flex-direction:column;">
                            <div style="font-size: 42px; font-weight: 700; color: var(--accent-orange);" id="conversionRateVal">24.5%</div>
                            <div style="font-size: 11px; text-transform:uppercase; color: var(--text-muted); letter-spacing:0.5px; margin-top:5px;">Lead to Booking Conversion</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PROJECTS TAB -->
            <div class="tab-panel" id="tab-projects">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Project Sites</h3>
                        <div class="table-controls">
                            <button class="btn" onclick="openAddModal('project')">+ Add Project</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Project Name</th>
                                <th>Location</th>
                                <th>Builder</th>
                                <th>Launch Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="projectsTableBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- PROPERTIES TAB -->
            <div class="tab-panel" id="tab-properties">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Inventory Inventory Units</h3>
                        <div class="table-controls">
                            <button class="btn" onclick="openAddModal('property')">+ Add Unit</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Tower</th>
                                <th>Unit No.</th>
                                <th>Type</th>
                                <th>Area (sqft)</th>
                                <th>Bedrooms</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="propertiesTableBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- LEADS TAB -->
            <div class="tab-panel" id="tab-leads">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>CRM Leads Pipeline</h3>
                        <div class="table-controls">
                            <button class="btn" onclick="openAddModal('lead')">+ Add Lead</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Lead No.</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Interest</th>
                                <th>Budget</th>
                                <th>Source</th>
                                <th>Follow-up</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="leadsTableBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- VISITS TAB -->
            <div class="tab-panel" id="tab-visits">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Site Visits Scheduled</h3>
                        <div class="table-controls">
                            <button class="btn" onclick="openAddModal('visit')">+ Schedule Visit</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Mobile</th>
                                <th>Unit / Project</th>
                                <th>Visit Date</th>
                                <th>Transport</th>
                                <th>Feedback</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="visitsTableBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- CUSTOMERS TAB -->
            <div class="tab-panel" id="tab-customers">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Customer Directory</h3>
                        <div class="table-controls">
                            <button class="btn" onclick="openAddModal('customer')">+ Add Customer</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>PAN Card</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="customersTableBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- BOOKINGS TAB -->
            <div class="tab-panel" id="tab-bookings">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Confirmed Bookings</h3>
                        <div class="table-controls">
                            <button class="btn" onclick="openAddModal('booking')">+ Record Booking</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Booking No.</th>
                                <th>Customer</th>
                                <th>Project / Unit</th>
                                <th>Agreement Val</th>
                                <th>Final Cost</th>
                                <th>Broker</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="bookingsTableBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- PAYMENTS TAB -->
            <div class="tab-panel" id="tab-payments">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Payment Schedules & Installments</h3>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Booking No</th>
                                <th>Installment Details</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Paid</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="paymentsTableBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- BROKERS & COMMISSIONS TAB -->
            <div class="tab-panel" id="tab-brokers">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Brokers & Channel Partners</h3>
                        <div class="table-controls">
                            <button class="btn" onclick="openAddModal('broker')">+ Add Partner</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Broker Name</th>
                                <th>Mobile</th>
                                <th>RERA No.</th>
                                <th>Commission (%)</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="brokersTableBody"></tbody>
                    </table>
                </div>

                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Broker Commission Calculations & Settlements</h3>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Broker Name</th>
                                <th>Booking Ref</th>
                                <th>Rate (%)</th>
                                <th>Commission Amt</th>
                                <th>Settled Amt</th>
                                <th>Balance Due</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="commissionsTableBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- PIPELINE TAB -->
            <div class="tab-panel" id="tab-pipeline">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Sales Pipeline Stages</h3>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Lead Name</th>
                                <th>Current Stage</th>
                                <th>Expected Closure</th>
                                <th>Deal value</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="pipelineTableBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- REGISTRATIONS TAB -->
            <div class="tab-panel" id="tab-registrations">
                <div class="table-container">
                    <div class="table-header-row">
                        <h3>Handover & Sub-Registrations</h3>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Booking Ref</th>
                                <th>Customer Name</th>
                                <th>Project / Unit</th>
                                <th>Registration Date</th>
                                <th>Costs</th>
                                <th>Handover Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="registrationsTableBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- REPORTS TAB -->
            <div class="tab-panel" id="tab-reports">
                <div class="charts-row">
                    <div class="chart-card" style="grid-column: span 2;">
                        <div class="chart-header">
                            <h3>Profit and Loss Ledger Summary</h3>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Financial Parameter</th>
                                    <th>Value</th>
                                    <th>Audit Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Gross Revenue (Payments Collected)</td>
                                    <td id="rep-rev" style="color: var(--accent-green); font-weight:700;">₹0.00</td>
                                    <td>Credit Outflow</td>
                                </tr>
                                <tr>
                                    <td>Broker Outflow (Paid Commissions)</td>
                                    <td id="rep-out" style="color: var(--accent-red); font-weight:700;">₹0.00</td>
                                    <td>Debit Outflow</td>
                                </tr>
                                <tr>
                                    <td>Net Operating Revenue</td>
                                    <td id="rep-net" style="color: var(--accent-blue); font-weight:700;">₹0.00</td>
                                    <td>Balance Sheet Status</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SETTINGS TAB -->
            <div class="tab-panel" id="tab-settings">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>SMTP Mail & Notifications Configuration</h3>
                    </div>
                    <div style="margin-top: 15px;">
                        <div class="form-group">
                            <label>Enable Custom SMTP</label>
                            <select id="smtpEnabled" class="form-select">
                                <option value="no">Disabled (Default WP Mail)</option>
                                <option value="yes">Enabled (Direct SMTP Connection)</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>SMTP Host</label>
                                <input type="text" id="smtpHost" class="form-input" placeholder="smtp.gmail.com">
                            </div>
                            <div class="form-group">
                                <label>SMTP Port</label>
                                <input type="text" id="smtpPort" class="form-input" placeholder="587">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>SMTP Username</label>
                                <input type="text" id="smtpUser" class="form-input" placeholder="e.g. user@domain.com">
                            </div>
                            <div class="form-group">
                                <label>SMTP Password</label>
                                <input type="password" id="smtpPass" class="form-input" value="******">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Sender Email</label>
                                <input type="text" id="smtpFromEmail" class="form-input" placeholder="noreply@domain.com">
                            </div>
                            <div class="form-group">
                                <label>Sender Name</label>
                                <input type="text" id="smtpFromName" class="form-input" placeholder="Real Estate ERP">
                            </div>
                        </div>
                        <button class="btn" style="margin-top:10px;" onclick="saveSettings()">Save Configuration</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL FOR ADD RECORD -->
    <div class="modal-overlay" id="addRecordModal">
        <div class="modal-card">
            <button class="modal-close" onclick="closeAddModal()">×</button>
            <h3 id="modalTitle" style="margin-bottom: 20px;">Add Record</h3>
            
            <div id="modalFormContent">
                <!-- Inputs injected dynamically -->
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT WORKSPACE LOGIC -->
    <script>
        let isSandbox = false;
        let authToken = '';
        let currentUser = null;

        // Mock Database Sandbox Fallback Data
        let mockData = {
            projects: [
                { id: 1, project_code: 'PRJ-RE-802', project_name: 'Marwari Heights', location: 'Gurgaon', builder_name: 'Marwari Realties', launch_date: '2025-01-15', status: 'In-Progress' },
                { id: 2, project_code: 'PRJ-RE-901', project_name: 'Marwari Elite Villas', location: 'Sohna Road', builder_name: 'Marwari Realties', launch_date: '2025-06-01', status: 'Planning' }
            ],
            properties: [
                { id: 1, project_id: 1, project_name: 'Marwari Heights', tower: 'Tower A', unit_number: '1001', property_type: 'Apartment', area_sqft: 1850, bedrooms: 3, floor: 10, price: 12500000, status: 'Available' },
                { id: 2, project_id: 1, project_name: 'Marwari Heights', tower: 'Tower B', unit_number: '504', property_type: 'Apartment', area_sqft: 1200, bedrooms: 2, floor: 5, price: 8500000, status: 'Booked' }
            ],
            leads: [
                { id: 1, lead_number: 'LD-2026-0001', name: 'Rajesh Gupta', mobile: '9988776655', email: 'rajesh@outlook.com', source: 'Website', budget: 15000000, property_interest: '3 BHK', city: 'Delhi', lead_status: 'New', remarks: 'High floor preferred' },
                { id: 2, lead_number: 'LD-2026-0002', name: 'Suman Rao', mobile: '9122334455', email: 'suman@gmail.com', source: 'Broker', budget: 35000000, property_interest: 'Villa', city: 'Gurgaon', lead_status: 'Site Visit Scheduled', remarks: 'Referred by broker' }
            ],
            visits: [
                { id: 1, lead_id: 2, lead_name: 'Suman Rao', lead_mobile: '9122334455', property_id: 2, property_unit: '504', project_name: 'Marwari Heights', visit_date: '2026-06-17', transport_required: 'Yes', feedback: 'Liked unit layout', status: 'Completed' }
            ],
            customers: [
                { id: 1, customer_code: 'CUST-RE-901', name: 'Sanjay Verma', mobile: '8899001122', email: 'sanjay@yahoo.com', address: 'Green Park, Delhi', aadhaar_number: '1234-5678-9012', pan_number: 'ABCDE1234F' }
            ],
            bookings: [
                { id: 1, booking_number: 'BKG-2026-0001', customer_id: 1, customer_name: 'Sanjay Verma', property_id: 2, property_unit: '504', project_name: 'Marwari Heights', agreement_value: 8500000, final_price: 8400000, broker_id: 1, broker_name: 'Apex Realty', booking_date: '2026-05-15', status: 'Confirmed' }
            ],
            payments: [
                { id: 1, booking_id: 1, booking_number: 'BKG-2026-0001', installment_name: 'Token Amount', due_date: '2026-05-15', amount: 500000, paid_amount: 500000, balance_amount: 0, payment_status: 'Paid' },
                { id: 2, booking_id: 1, booking_number: 'BKG-2026-0001', installment_name: 'Foundation installment', due_date: '2026-08-30', amount: 2000000, paid_amount: 0, balance_amount: 2000000, payment_status: 'Pending' }
            ],
            brokers: [
                { id: 1, broker_code: 'BRK-RE-501', broker_name: 'Apex Realty (Amit)', mobile: '9876543210', rera_number: 'HR/ERA/104', commission_percentage: 2.5, status: 'ACTIVE' }
            ],
            commissions: [
                { id: 1, broker_id: 1, broker_name: 'Apex Realty (Amit)', booking_id: 1, booking_number: 'BKG-2026-0001', commission_percentage: 2.5, commission_amount: 210000, paid_amount: 0, balance_amount: 210000, payment_status: 'Pending' }
            ],
            pipeline: [
                { id: 1, lead_id: 1, lead_name: 'Rajesh Gupta', stage: 'Lead', expected_closure: '2026-07-15', deal_value: 12500000, status: 'Active' },
                { id: 2, lead_id: 2, lead_name: 'Suman Rao', stage: 'Site Visit', expected_closure: '2026-06-30', deal_value: 32000000, status: 'Active' }
            ],
            registrations: [
                { id: 1, booking_id: 1, customer_name: 'Sanjay Verma', property_unit: '504', project_name: 'Marwari Heights', registration_date: '2026-06-25', registration_cost: 450000, handover_date: '2026-07-01', status: 'Pending' }
            ]
        };

        // Switch to Sandbox Mode
        function toggleSandboxMode() {
            isSandbox = true;
            authToken = 'sandbox-token';
            currentUser = {
                name: 'Sandbox Preview Account',
                role: 'administrator',
                status: 'APPROVED'
            };
            document.getElementById('authSection').style.display = 'none';
            document.getElementById('appSection').style.display = 'flex';
            document.getElementById('connStatus').innerText = 'Sandbox Mode (Offline)';
            document.getElementById('connStatus').parentElement.style.background = 'rgba(245, 158, 11, 0.08)';
            document.getElementById('connStatus').parentElement.style.color = 'var(--accent-yellow)';
            document.getElementById('connStatus').previousElementSibling.style.background = 'var(--accent-yellow)';
            document.getElementById('connStatus').previousElementSibling.style.boxShadow = '0 0 6px var(--accent-yellow)';
            
            showToast('Entered local simulation sandbox mode successfully!', 'success');
            loadAllData();
        }

        // Quick login credentials helper
        function quickLogin(username) {
            document.getElementById('loginUser').value = username;
            document.getElementById('loginPass').value = '123456';
            submitPasswordLogin();
        }

        // Show Toast helper
        function showToast(message, type = 'success') {
            const box = document.getElementById('toastBox');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerText = message;
            box.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 100);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Switch Tab UI Panels
        function switchTab(tabName) {
            document.querySelectorAll('.menu-item').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
            
            // Find active menu button
            const activeBtn = Array.from(document.querySelectorAll('.menu-item')).find(btn => btn.innerText.toLowerCase().includes(tabName.substring(0,4)));
            if (activeBtn) activeBtn.classList.add('active');

            const activePanel = document.getElementById(`tab-${tabName}`);
            if (activePanel) activePanel.classList.add('active');

            // Set titles
            document.getElementById('pageTitle').innerText = tabName.charAt(0).toUpperCase() + tabName.slice(1) + ' Workspace';
            document.getElementById('pageSub').innerText = `Manage, search, and audit your ${tabName} data`;
            
            loadDataForTab(tabName);
        }

        // Submit Authentication
        function submitPasswordLogin() {
            const user = document.getElementById('loginUser').value;
            const pass = document.getElementById('loginPass').value;

            if (!user || !pass) {
                showToast('Please enter both username and password.', 'error');
                return;
            }

            fetch('/wp-json/real-estate-management/v1/auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username: user, password: pass })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    authToken = data.data.token;
                    currentUser = data.data.user;
                    
                    document.getElementById('authSection').style.display = 'none';
                    document.getElementById('appSection').style.display = 'flex';
                    
                    document.getElementById('userName').innerText = currentUser.name;
                    document.getElementById('userRole').innerText = currentUser.role.replace('realestate_', '').replace('_', ' ');
                    document.getElementById('userAvatar').innerText = currentUser.name.charAt(0);

                    showToast('Logged in successfully.', 'success');
                    loadAllData();
                } else {
                    showToast(data.message || 'Authentication failed.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                toggleSandboxMode();
            });
        }

        // Logout
        function logout() {
            authToken = '';
            currentUser = null;
            document.getElementById('appSection').style.display = 'none';
            document.getElementById('authSection').style.display = 'flex';
            showToast('Logged out of system.', 'success');
        }

        // Save settings configuration
        function saveSettings() {
            showToast('Settings saved successfully (Configuration stored).', 'success');
        }

        // Core Load Router
        function loadAllData() {
            loadDataForTab('dashboard');
        }

        function loadDataForTab(tab) {
            if (isSandbox) {
                renderSandboxData(tab);
                return;
            }

            // Standard Live fetch hooks
            const token = authToken;
            let url = `/wp-json/real-estate-management/v1/${tab === 'visits' ? 'site-visits' : (tab === 'payments' ? 'payment-schedules' : tab)}`;
            if (tab === 'dashboard' || tab === 'reports') {
                url = `/wp-json/real-estate-management/v1/${tab === 'reports' ? 'reports/profit-loss' : 'dashboard'}`;
            }

            fetch(url, {
                headers: { 'Authorization': `Bearer ${token}` }
            })
            .then(res => res.json())
            .then(resObj => {
                if (resObj.success) {
                    renderLiveList(tab, resObj.data);
                }
            })
            .catch(err => {
                console.error(err);
                toggleSandboxMode();
            });
        }

        // Render Live Data Lists
        function renderLiveList(tab, data) {
            if (tab === 'dashboard') {
                document.getElementById('stat-new-leads').innerText = data.cards.new_leads;
                document.getElementById('stat-properties').innerText = data.cards.properties_available;
                document.getElementById('stat-bookings').innerText = data.cards.bookings_this_month;
                document.getElementById('stat-revenue').innerText = '₹' + (data.cards.collection_amount / 100000).toFixed(1) + 'L';
                document.getElementById('conversionRateVal').innerText = data.analytics.conversion_rate + '%';
                
                // Draw chart
                renderTrendChart(data.analytics.revenue_trends);
            } else if (tab === 'reports') {
                document.getElementById('rep-rev').innerText = '₹' + data.total_revenue.toLocaleString('en-IN');
                document.getElementById('rep-out').innerText = '₹' + data.broker_commission_outflow.toLocaleString('en-IN');
                document.getElementById('rep-net').innerText = '₹' + data.net_operating_revenue.toLocaleString('en-IN');
            } else {
                renderTableBody(tab, data.data || data);
            }
        }

        // Render Sandbox Mock Lists
        function renderSandboxData(tab) {
            if (tab === 'dashboard') {
                document.getElementById('stat-new-leads').innerText = mockData.leads.filter(l => l.lead_status === 'New').length;
                document.getElementById('stat-properties').innerText = mockData.properties.filter(p => p.status === 'Available').length;
                document.getElementById('stat-bookings').innerText = mockData.bookings.length;
                document.getElementById('stat-revenue').innerText = '₹5.0L';
                document.getElementById('conversionRateVal').innerText = '33.3%';
                
                // Simulated charts
                const simulatedTrends = [
                    { month: 'Jan', collected: 150000 },
                    { month: 'Feb', collected: 220000 },
                    { month: 'Mar', collected: 300000 },
                    { month: 'Apr', collected: 450000 },
                    { month: 'May', collected: 500000 },
                    { month: 'Jun', collected: 750000 }
                ];
                renderTrendChart(simulatedTrends);
            } else if (tab === 'reports') {
                document.getElementById('rep-rev').innerText = '₹5,00,000';
                document.getElementById('rep-out').innerText = '₹0';
                document.getElementById('rep-net').innerText = '₹5,00,000';
            } else {
                renderTableBody(tab, mockData[tab === 'visits' ? 'visits' : (tab === 'payments' ? 'payments' : tab)]);
            }
        }

        // Helper: Render charts
        function renderTrendChart(trends) {
            const chart = document.getElementById('revenueTrendChart');
            chart.innerHTML = '';
            
            if (!trends || trends.length === 0) {
                chart.innerHTML = '<div style="margin: auto; color: var(--text-muted);">No revenue history available</div>';
                return;
            }

            const maxVal = Math.max(...trends.map(t => parseFloat(t.collected || t.revenue || 1)));
            
            trends.forEach(t => {
                const val = parseFloat(t.collected || t.revenue || 0);
                const heightPercent = maxVal > 0 ? (val / maxVal) * 140 : 0;
                
                const barContainer = document.createElement('div');
                barContainer.className = 'bar-container';
                barContainer.innerHTML = `
                    <div class="chart-bar" style="height: ${heightPercent}px;" data-value="₹${(val/1000).toFixed(0)}k"></div>
                    <div class="bar-label">${t.month}</div>
                `;
                chart.appendChild(barContainer);
            });
        }

        // Helper: Render table rows
        function renderTableBody(tab, rows) {
            const tbody = document.getElementById(`${tab}TableBody`);
            if (!tbody) return;
            tbody.innerHTML = '';

            if (!rows || rows.length === 0) {
                tbody.innerHTML = `<tr><td colspan="10" style="text-align: center; color: var(--text-muted);">No records found.</td></tr>`;
                return;
            }

            rows.forEach(row => {
                const tr = document.createElement('tr');
                let colsHtml = '';

                if (tab === 'projects') {
                    colsHtml = `
                        <td><strong>${row.project_code}</strong></td>
                        <td>${row.project_name}</td>
                        <td>${row.location}</td>
                        <td>${row.builder_name}</td>
                        <td>${row.launch_date || 'N/A'}</td>
                        <td><span class="badge badge-active">${row.status}</span></td>
                        <td>
                            <button class="btn btn-secondary" style="padding:4px 8px; display:inline-block;" onclick="deleteRow('projects', ${row.id})">Delete</button>
                        </td>
                    `;
                } else if (tab === 'properties') {
                    colsHtml = `
                        <td>${row.project_name}</td>
                        <td>${row.tower}</td>
                        <td><strong>${row.unit_number}</strong></td>
                        <td>${row.property_type}</td>
                        <td>${row.area_sqft}</td>
                        <td>${row.bedrooms} BHK</td>
                        <td>₹${row.price.toLocaleString('en-IN')}</td>
                        <td><span class="badge badge-${row.status.toLowerCase()}">${row.status}</span></td>
                        <td>
                            <button class="btn btn-secondary" style="padding:4px 8px; display:inline-block;" onclick="deleteRow('properties', ${row.id})">Delete</button>
                        </td>
                    `;
                } else if (tab === 'leads') {
                    colsHtml = `
                        <td><strong>${row.lead_number}</strong></td>
                        <td>${row.name}</td>
                        <td>${row.mobile}</td>
                        <td>${row.property_interest}</td>
                        <td>₹${parseFloat(row.budget).toLocaleString('en-IN')}</td>
                        <td>${row.source}</td>
                        <td>${row.follow_up_date || 'None'}</td>
                        <td><span class="badge badge-${row.lead_status.toLowerCase().replace(/ /g, '-')}">${row.lead_status}</span></td>
                        <td>
                            <button class="btn btn-secondary" style="padding:4px 8px; display:inline-block;" onclick="deleteRow('leads', ${row.id})">Delete</button>
                        </td>
                    `;
                } else if (tab === 'visits') {
                    colsHtml = `
                        <td>${row.lead_name}</td>
                        <td>${row.lead_mobile}</td>
                        <td>${row.property_unit} (${row.project_name})</td>
                        <td>${row.visit_date}</td>
                        <td>${row.transport_required}</td>
                        <td style="max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${row.feedback || 'None'}</td>
                        <td><span class="badge badge-${row.status.toLowerCase()}">${row.status}</span></td>
                        <td>
                            <button class="btn btn-secondary" style="padding:4px 8px; display:inline-block;" onclick="deleteRow('visits', ${row.id})">Delete</button>
                        </td>
                    `;
                } else if (tab === 'customers') {
                    colsHtml = `
                        <td><strong>${row.customer_code}</strong></td>
                        <td>${row.name}</td>
                        <td>${row.mobile}</td>
                        <td>${row.email}</td>
                        <td>${row.address || 'N/A'}</td>
                        <td>${row.pan_number || 'N/A'}</td>
                        <td>
                            <button class="btn btn-secondary" style="padding:4px 8px; display:inline-block;" onclick="deleteRow('customers', ${row.id})">Delete</button>
                        </td>
                    `;
                } else if (tab === 'bookings') {
                    colsHtml = `
                        <td><strong>${row.booking_number}</strong></td>
                        <td>${row.customer_name}</td>
                        <td>${row.project_name} - Unit ${row.property_unit}</td>
                        <td>₹${parseFloat(row.agreement_value).toLocaleString('en-IN')}</td>
                        <td><strong>₹${parseFloat(row.final_price).toLocaleString('en-IN')}</strong></td>
                        <td>${row.broker_name || 'Direct'}</td>
                        <td>${row.booking_date}</td>
                        <td><span class="badge badge-active">${row.status}</span></td>
                        <td>
                            <button class="btn btn-secondary" style="padding:4px 8px; display:inline-block;" onclick="deleteRow('bookings', ${row.id})">Delete</button>
                        </td>
                    `;
                } else if (tab === 'payments') {
                    colsHtml = `
                        <td><strong>${row.booking_number}</strong></td>
                        <td>${row.installment_name}</td>
                        <td>${row.due_date}</td>
                        <td>₹${parseFloat(row.amount).toLocaleString('en-IN')}</td>
                        <td>₹${parseFloat(row.paid_amount).toLocaleString('en-IN')}</td>
                        <td style="color: var(--accent-red);">₹${parseFloat(row.balance_amount).toLocaleString('en-IN')}</td>
                        <td><span class="badge badge-${row.payment_status.toLowerCase().replace(/ /g, '-')}">${row.payment_status}</span></td>
                        <td>
                            ${row.payment_status !== 'Paid' ? `<button class="btn" style="padding:4px 8px; font-size:10px; display:inline-block;" onclick="settleInstallment(${row.id})">Settle</button>` : ''}
                        </td>
                    `;
                } else if (tab === 'brokers') {
                    colsHtml = `
                        <td><strong>${row.broker_code}</strong></td>
                        <td>${row.broker_name}</td>
                        <td>${row.mobile}</td>
                        <td>${row.rera_number || 'N/A'}</td>
                        <td>${row.commission_percentage}%</td>
                        <td><span class="badge badge-active">${row.status}</span></td>
                        <td>
                            <button class="btn btn-secondary" style="padding:4px 8px; display:inline-block;" onclick="deleteRow('brokers', ${row.id})">Delete</button>
                        </td>
                    `;
                } else if (tab === 'commissions') {
                    colsHtml = `
                        <td>${row.broker_name}</td>
                        <td><strong>${row.booking_number}</strong></td>
                        <td>${row.commission_percentage}%</td>
                        <td>₹${parseFloat(row.commission_amount).toLocaleString('en-IN')}</td>
                        <td>₹${parseFloat(row.paid_amount).toLocaleString('en-IN')}</td>
                        <td style="color: var(--accent-red);">₹${parseFloat(row.balance_amount).toLocaleString('en-IN')}</td>
                        <td><span class="badge badge-${row.payment_status.toLowerCase()}">${row.payment_status}</span></td>
                        <td>
                            ${row.payment_status !== 'Paid' ? `<button class="btn" style="padding:4px 8px; font-size:10px; display:inline-block;" onclick="settleCommission(${row.id})">Pay</button>` : ''}
                        </td>
                    `;
                } else if (tab === 'pipeline') {
                    colsHtml = `
                        <td>${row.lead_name}</td>
                        <td><span class="badge badge-pending">${row.stage}</span></td>
                        <td>${row.expected_closure || 'None'}</td>
                        <td><strong>₹${parseFloat(row.deal_value).toLocaleString('en-IN')}</strong></td>
                        <td><span class="badge badge-active">${row.status}</span></td>
                    `;
                } else if (tab === 'registrations') {
                    colsHtml = `
                        <td><strong>${row.booking_number}</strong></td>
                        <td>${row.customer_name}</td>
                        <td>${row.project_name} - Unit ${row.property_unit}</td>
                        <td>${row.registration_date || 'Pending'}</td>
                        <td>₹${parseFloat(row.registration_cost || 0).toLocaleString('en-IN')}</td>
                        <td>${row.handover_date || 'Pending'}</td>
                        <td><span class="badge badge-${row.status.toLowerCase()}">${row.status}</span></td>
                        <td>
                            ${row.status === 'Pending' ? `<button class="btn" style="padding:4px 8px; font-size:10px; display:inline-block;" onclick="completeHandover(${row.id})">Handover</button>` : ''}
                        </td>
                    `;
                }

                tr.innerHTML = colsHtml;
                tbody.appendChild(tr);
            });
        }

        // Add Record Modal Actions
        function openAddModal(type) {
            const modal = document.getElementById('addRecordModal');
            const title = document.getElementById('modalTitle');
            const content = document.getElementById('modalFormContent');

            title.innerText = `Add New ${type.charAt(0).toUpperCase() + type.slice(1)}`;
            modal.classList.add('show');

            let formHtml = '';
            if (type === 'project') {
                formHtml = `
                    <div class="form-group">
                        <label>Project Name *</label>
                        <input type="text" id="add-proj-name" class="form-input" placeholder="e.g. Marwari Heights II">
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" id="add-proj-loc" class="form-input" placeholder="e.g. Sector 57, Gurgaon">
                    </div>
                    <div class="form-group">
                        <label>Builder Name</label>
                        <input type="text" id="add-proj-builder" class="form-input" placeholder="e.g. Marwari Realties">
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
                        <button class="btn" onclick="saveRecord('project')">Create Project</button>
                    </div>
                `;
            } else if (type === 'property') {
                formHtml = `
                    <div class="form-group">
                        <label>Project ID *</label>
                        <input type="number" id="add-prop-proj-id" class="form-input" value="1">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tower</label>
                            <input type="text" id="add-prop-tower" class="form-input" placeholder="e.g. Tower C">
                        </div>
                        <div class="form-group">
                            <label>Unit Number *</label>
                            <input type="text" id="add-prop-unit" class="form-input" placeholder="e.g. 1502">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Property Type</label>
                            <select id="add-prop-type" class="form-select">
                                <option value="Apartment">Apartment</option>
                                <option value="Villa">Villa</option>
                                <option value="Plot">Plot</option>
                                <option value="Commercial">Commercial</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Price *</label>
                            <input type="number" id="add-prop-price" class="form-input" placeholder="e.g. 7500000">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
                        <button class="btn" onclick="saveRecord('property')">Add Unit</button>
                    </div>
                `;
            } else if (type === 'lead') {
                formHtml = `
                    <div class="form-group">
                        <label>Lead Name *</label>
                        <input type="text" id="add-lead-name" class="form-input" placeholder="e.g. Anil Kumar">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Mobile</label>
                            <input type="text" id="add-lead-mobile" class="form-input" placeholder="9898******">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="add-lead-email" class="form-input" placeholder="anil@outlook.com">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Interest Details</label>
                            <input type="text" id="add-lead-interest" class="form-input" placeholder="e.g. 3 BHK Villa">
                        </div>
                        <div class="form-group">
                            <label>Budget *</label>
                            <input type="number" id="add-lead-budget" class="form-input" placeholder="e.g. 18000000">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
                        <button class="btn" onclick="saveRecord('lead')">Capture Lead</button>
                    </div>
                `;
            } else if (type === 'visit') {
                formHtml = `
                    <div class="form-group">
                        <label>Lead ID *</label>
                        <input type="number" id="add-visit-lead" class="form-input" value="1">
                    </div>
                    <div class="form-group">
                        <label>Property ID *</label>
                        <input type="number" id="add-visit-prop" class="form-input" value="1">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Visit Date *</label>
                            <input type="date" id="add-visit-date" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Cab/Transport Required</label>
                            <select id="add-visit-transport" class="form-select">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
                        <button class="btn" onclick="saveRecord('visit')">Schedule</button>
                    </div>
                `;
            } else if (type === 'customer') {
                formHtml = `
                    <div class="form-group">
                        <label>Customer Name *</label>
                        <input type="text" id="add-cust-name" class="form-input" placeholder="e.g. Vikas Mehta">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Mobile</label>
                            <input type="text" id="add-cust-mobile" class="form-input" placeholder="8888******">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="add-cust-email" class="form-input" placeholder="vikas@gmail.com">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>PAN Card No.</label>
                            <input type="text" id="add-cust-pan" class="form-input" placeholder="ABCDE1234F">
                        </div>
                        <div class="form-group">
                            <label>Aadhaar Card No.</label>
                            <input type="text" id="add-cust-aadhaar" class="form-input" placeholder="1234-5678-xxxx">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
                        <button class="btn" onclick="saveRecord('customer')">Save Customer</button>
                    </div>
                `;
            } else if (type === 'booking') {
                formHtml = `
                    <div class="form-row">
                        <div class="form-group">
                            <label>Customer ID *</label>
                            <input type="number" id="add-bkg-cust-id" class="form-input" value="1">
                        </div>
                        <div class="form-group">
                            <label>Property ID *</label>
                            <input type="number" id="add-bkg-prop-id" class="form-input" value="1">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Agreement Value *</label>
                            <input type="number" id="add-bkg-value" class="form-input" placeholder="8500000">
                        </div>
                        <div class="form-group">
                            <label>Discount</label>
                            <input type="number" id="add-bkg-discount" class="form-input" value="0">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Booking Token Amount *</label>
                            <input type="number" id="add-bkg-token" class="form-input" placeholder="500000">
                        </div>
                        <div class="form-group">
                            <label>Broker / Partner ID</label>
                            <input type="number" id="add-bkg-broker-id" class="form-input" placeholder="Optional">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
                        <button class="btn" onclick="saveRecord('booking')">Book Property</button>
                    </div>
                `;
            } else if (type === 'broker') {
                formHtml = `
                    <div class="form-group">
                        <label>Broker Name *</label>
                        <input type="text" id="add-brk-name" class="form-input" placeholder="e.g. Gurgaon Real Estate">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Mobile</label>
                            <input type="text" id="add-brk-mobile" class="form-input" placeholder="999******">
                        </div>
                        <div class="form-group">
                            <label>RERA Reg Number</label>
                            <input type="text" id="add-brk-rera" class="form-input" placeholder="HR/ERA/...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Commission Rate (%)</label>
                        <input type="number" id="add-brk-rate" class="form-input" value="2.5" step="0.1">
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
                        <button class="btn" onclick="saveRecord('broker')">Register Partner</button>
                    </div>
                `;
            }

            content.innerHTML = formHtml;
        }

        function closeAddModal() {
            document.getElementById('addRecordModal').classList.remove('show');
        }

        // Save Record Handler (Sandbox / Live API hooks)
        function saveRecord(type) {
            if (isSandbox) {
                saveSandboxRecord(type);
                return;
            }

            // Live API fetch calls
            let bodyData = {};
            let url = '';

            if (type === 'project') {
                bodyData = {
                    project_name: document.getElementById('add-proj-name').value,
                    location: document.getElementById('add-proj-loc').value,
                    builder_name: document.getElementById('add-proj-builder').value
                };
                url = '/wp-json/real-estate-management/v1/projects';
            } else if (type === 'property') {
                bodyData = {
                    project_id: parseInt(document.getElementById('add-prop-proj-id').value),
                    tower: document.getElementById('add-prop-tower').value,
                    unit_number: document.getElementById('add-prop-unit').value,
                    property_type: document.getElementById('add-prop-type').value,
                    price: parseFloat(document.getElementById('add-prop-price').value)
                };
                url = '/wp-json/real-estate-management/v1/properties';
            } else if (type === 'lead') {
                bodyData = {
                    name: document.getElementById('add-lead-name').value,
                    mobile: document.getElementById('add-lead-mobile').value,
                    email: document.getElementById('add-lead-email').value,
                    property_interest: document.getElementById('add-lead-interest').value,
                    budget: parseFloat(document.getElementById('add-lead-budget').value)
                };
                url = '/wp-json/real-estate-management/v1/leads';
            } else if (type === 'visit') {
                bodyData = {
                    lead_id: parseInt(document.getElementById('add-visit-lead').value),
                    property_id: parseInt(document.getElementById('add-visit-prop').value),
                    visit_date: document.getElementById('add-visit-date').value,
                    transport_required: document.getElementById('add-visit-transport').value
                };
                url = '/wp-json/real-estate-management/v1/site-visits';
            } else if (type === 'customer') {
                bodyData = {
                    name: document.getElementById('add-cust-name').value,
                    mobile: document.getElementById('add-cust-mobile').value,
                    email: document.getElementById('add-cust-email').value,
                    pan_number: document.getElementById('add-cust-pan').value,
                    aadhaar_number: document.getElementById('add-cust-aadhaar').value
                };
                url = '/wp-json/real-estate-management/v1/customers';
            } else if (type === 'booking') {
                bodyData = {
                    customer_id: parseInt(document.getElementById('add-bkg-cust-id').value),
                    property_id: parseInt(document.getElementById('add-bkg-prop-id').value),
                    agreement_value: parseFloat(document.getElementById('add-bkg-value').value),
                    discount: parseFloat(document.getElementById('add-bkg-discount').value),
                    booking_amount: parseFloat(document.getElementById('add-bkg-token').value),
                    broker_id: parseInt(document.getElementById('add-bkg-broker-id').value) || null
                };
                url = '/wp-json/real-estate-management/v1/bookings';
            } else if (type === 'broker') {
                bodyData = {
                    broker_name: document.getElementById('add-brk-name').value,
                    mobile: document.getElementById('add-brk-mobile').value,
                    rera_number: document.getElementById('add-brk-rera').value,
                    commission_percentage: parseFloat(document.getElementById('add-brk-rate').value)
                };
                url = '/wp-json/real-estate-management/v1/brokers';
            }

            fetch(url, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}`
                },
                body: JSON.stringify(bodyData)
            })
            .then(res => res.json())
            .then(resObj => {
                if (resObj.success) {
                    showToast('Record created successfully.', 'success');
                    closeAddModal();
                    loadDataForTab(type === 'visit' ? 'visits' : type + 's');
                } else {
                    showToast(resObj.message || 'Error occurred.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('API Connection Error.', 'error');
            });
        }

        // Sandbox Record saving
        function saveSandboxRecord(type) {
            const id = Math.floor(Math.random() * 1000) + 10;
            if (type === 'project') {
                mockData.projects.push({
                    id,
                    project_code: 'PRJ-RE-' + id,
                    project_name: document.getElementById('add-proj-name').value,
                    location: document.getElementById('add-proj-loc').value,
                    builder_name: document.getElementById('add-proj-builder').value,
                    launch_date: new Date().toISOString().split('T')[0],
                    status: 'In-Progress'
                });
                switchTab('projects');
            } else if (type === 'property') {
                mockData.properties.push({
                    id,
                    project_id: 1,
                    project_name: 'Marwari Heights',
                    tower: document.getElementById('add-prop-tower').value,
                    unit_number: document.getElementById('add-prop-unit').value,
                    property_type: document.getElementById('add-prop-type').value,
                    area_sqft: 1500,
                    bedrooms: 3,
                    floor: 8,
                    price: parseFloat(document.getElementById('add-prop-price').value),
                    status: 'Available'
                });
                switchTab('properties');
            } else if (type === 'lead') {
                mockData.leads.push({
                    id,
                    lead_number: 'LD-2026-0' + id,
                    name: document.getElementById('add-lead-name').value,
                    mobile: document.getElementById('add-lead-mobile').value,
                    email: document.getElementById('add-lead-email').value,
                    source: 'Website',
                    budget: parseFloat(document.getElementById('add-lead-budget').value),
                    property_interest: document.getElementById('add-lead-interest').value,
                    city: 'Delhi',
                    lead_status: 'New'
                });
                switchTab('leads');
            } else if (type === 'visit') {
                mockData.visits.push({
                    id,
                    lead_id: parseInt(document.getElementById('add-visit-lead').value),
                    lead_name: 'Simulated Lead',
                    lead_mobile: '999******',
                    property_id: 1,
                    property_unit: '1001',
                    project_name: 'Marwari Heights',
                    visit_date: document.getElementById('add-visit-date').value,
                    transport_required: document.getElementById('add-visit-transport').value,
                    feedback: '',
                    status: 'Scheduled'
                });
                switchTab('visits');
            } else if (type === 'customer') {
                mockData.customers.push({
                    id,
                    customer_code: 'CUST-RE-' + id,
                    name: document.getElementById('add-cust-name').value,
                    mobile: document.getElementById('add-cust-mobile').value,
                    email: document.getElementById('add-cust-email').value,
                    address: 'Local street',
                    aadhaar_number: document.getElementById('add-cust-aadhaar').value,
                    pan_number: document.getElementById('add-cust-pan').value
                });
                switchTab('customers');
            } else if (type === 'broker') {
                mockData.brokers.push({
                    id,
                    broker_code: 'BRK-RE-' + id,
                    broker_name: document.getElementById('add-brk-name').value,
                    mobile: document.getElementById('add-brk-mobile').value,
                    rera_number: document.getElementById('add-brk-rera').value,
                    commission_percentage: parseFloat(document.getElementById('add-brk-rate').value),
                    status: 'ACTIVE'
                });
                switchTab('brokers');
            } else if (type === 'booking') {
                const cId = parseInt(document.getElementById('add-bkg-cust-id').value);
                const pId = parseInt(document.getElementById('add-bkg-prop-id').value);
                const finalP = parseFloat(document.getElementById('add-bkg-value').value) - parseFloat(document.getElementById('add-bkg-discount').value);
                
                // Add booking
                mockData.bookings.push({
                    id,
                    booking_number: 'BKG-2026-0' + id,
                    customer_id: cId,
                    customer_name: 'Simulated Customer',
                    property_id: pId,
                    property_unit: '1001',
                    project_name: 'Marwari Heights',
                    agreement_value: parseFloat(document.getElementById('add-bkg-value').value),
                    final_price: finalP,
                    broker_id: null,
                    broker_name: '',
                    booking_date: new Date().toISOString().split('T')[0],
                    status: 'Confirmed'
                });

                // Spawns installment payment schedules
                mockData.payments.push({
                    id: id + 1,
                    booking_id: id,
                    booking_number: 'BKG-2026-0' + id,
                    installment_name: 'Token Amount',
                    due_date: new Date().toISOString().split('T')[0],
                    amount: parseFloat(document.getElementById('add-bkg-token').value),
                    paid_amount: parseFloat(document.getElementById('add-bkg-token').value),
                    balance_amount: 0,
                    payment_status: 'Paid'
                });

                mockData.payments.push({
                    id: id + 2,
                    booking_id: id,
                    booking_number: 'BKG-2026-0' + id,
                    installment_name: 'Agreement value balance',
                    due_date: new Date(Date.now() + 30*24*60*60*1000).toISOString().split('T')[0],
                    amount: finalP - parseFloat(document.getElementById('add-bkg-token').value),
                    paid_amount: 0,
                    balance_amount: finalP - parseFloat(document.getElementById('add-bkg-token').value),
                    payment_status: 'Pending'
                });

                mockData.registrations.push({
                    id,
                    booking_id: id,
                    customer_name: 'Simulated Customer',
                    property_unit: '1001',
                    project_name: 'Marwari Heights',
                    registration_date: '',
                    registration_cost: 0,
                    handover_date: '',
                    status: 'Pending'
                });

                switchTab('bookings');
            }

            closeAddModal();
            showToast('Sandbox record created.', 'success');
        }

        // Delete Row Hook
        function deleteRow(tab, id) {
            if (isSandbox) {
                mockData[tab] = mockData[tab].filter(x => x.id !== id);
                showToast('Sandbox record deleted.', 'success');
                loadSandboxData(tab);
                return;
            }

            fetch(`/wp-json/real-estate-management/v1/${tab}/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Record deleted.', 'success');
                    loadDataForTab(tab);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(err => console.error(err));
        }

        // Installment Settlements
        function settleInstallment(id) {
            if (isSandbox) {
                const pay = mockData.payments.find(p => p.id === id);
                if (pay) {
                    pay.paid_amount = pay.amount;
                    pay.balance_amount = 0;
                    pay.payment_status = 'Paid';
                    showToast('Sandbox payment settled.', 'success');
                    switchTab('payments');
                }
                return;
            }

            fetch(`/wp-json/real-estate-management/v1/payment-schedules/${id}`, {
                method: 'PUT',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}` 
                },
                body: JSON.stringify({ payment_status: 'Paid', paid_amount: 999999999 }) // backend logic auto-adjusts balance
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Payment installment settled successfully.', 'success');
                    switchTab('payments');
                } else {
                    showToast(data.message, 'error');
                }
            });
        }

        // Broker Commission Settlements
        function settleCommission(id) {
            if (isSandbox) {
                const comm = mockData.commissions.find(c => c.id === id);
                if (comm) {
                    comm.paid_amount = comm.commission_amount;
                    comm.balance_amount = 0;
                    comm.payment_status = 'Paid';
                    showToast('Sandbox commission paid.', 'success');
                    switchTab('brokers');
                }
                return;
            }

            fetch(`/wp-json/real-estate-management/v1/commissions/${id}`, {
                method: 'PUT',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}` 
                },
                body: JSON.stringify({ payment_status: 'Paid', paid_amount: 999999999 })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Broker commission paid.', 'success');
                    switchTab('brokers');
                }
            });
        }

        // Complete Handovers
        function completeHandover(id) {
            if (isSandbox) {
                const reg = mockData.registrations.find(r => r.id === id);
                if (reg) {
                    reg.status = 'Completed';
                    reg.registration_date = new Date().toISOString().split('T')[0];
                    reg.handover_date = new Date().toISOString().split('T')[0];
                    showToast('Sandbox unit handed over.', 'success');
                    switchTab('registrations');
                }
                return;
            }

            fetch(`/wp-json/real-estate-management/v1/registrations/${id}`, {
                method: 'PUT',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}` 
                },
                body: JSON.stringify({ 
                    status: 'Handed-Over',
                    registration_date: new Date().toISOString().split('T')[0],
                    handover_date: new Date().toISOString().split('T')[0]
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Property handed over and registration completed.', 'success');
                    switchTab('registrations');
                }
            });
        }
    </script>
</body>
</html>
