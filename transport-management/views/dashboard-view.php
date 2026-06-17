<?php
/**
 * Transport Logistics ERP Dashboard View Template
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
    <title>Transport & Logistics ERP - Dashboard</title>
    <!-- Prevent flash of unauthenticated login screen & set theme -->
    <script>
        (function() {
            var token = localStorage.getItem('tr_auth_token');
            var user = localStorage.getItem('tr_current_user');
            if (token && user) {
                document.write('<style>#authSection { display: none !important; } #appSection { display: flex !important; }</style>');
            }
            var theme = localStorage.getItem('tr_theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            } else {
                document.documentElement.classList.remove('dark-mode');
            }
        })();
    </script>
    <!-- Modern Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            /* LIGHT MODE (DEFAULT) */
            --bg-dark: #f8fafc;
            --card-bg: rgba(255, 255, 255, 0.8);
            --glass-border: rgba(15, 23, 42, 0.08);
            --text-main: #0f172a;
            --text-muted: #64748b;
            
            --accent-blue: #0284c7;
            --accent-green: #16a34a;
            --accent-yellow: #ca8a04;
            --accent-red: #dc2626;
            --accent-purple: #9333ea;
            --accent-teal: #0d9488;
            
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            
            --sidebar-bg: rgba(241, 245, 249, 0.95);
            --ambient-glow-1: radial-gradient(circle, rgba(14, 165, 233, 0.12) 0%, rgba(248, 250, 252, 0) 70%);
            --ambient-glow-2: radial-gradient(circle, rgba(168, 85, 247, 0.1) 0%, rgba(248, 250, 252, 0) 70%);
            --toast-bg: rgba(255, 255, 255, 0.95);
            --gps-bg: radial-gradient(circle, #f1f5f9 0%, #e2e8f0 100%);
            --gps-panel-bg: rgba(255, 255, 255, 0.95);
            --gps-grid-color: rgba(15, 23, 42, 0.04);
            --gps-route-color: rgba(14, 165, 233, 0.2);
            --shadow-primary: 0 10px 20px rgba(15, 23, 42, 0.05);
            --shadow-large: 0 30px 60px rgba(15, 23, 42, 0.1);
            --modal-overlay: rgba(15, 23, 42, 0.6);
            --row-border: rgba(15, 23, 42, 0.04);
            --row-hover: rgba(15, 23, 42, 0.01);
            --form-bg: rgba(15, 23, 42, 0.02);
            --form-focus-bg: rgba(255, 255, 255, 0.8);
            --auth-logo-gradient: linear-gradient(135deg, #0f172a, #0284c7);
        }

        .dark-mode {
            /* DARK MODE OVERRIDES */
            --bg-dark: #070b13;
            --card-bg: rgba(13, 21, 37, 0.75);
            --glass-border: rgba(255, 255, 255, 0.05);
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            
            --accent-blue: #0ea5e9;
            --accent-green: #10b981;
            --accent-yellow: #f59e0b;
            --accent-red: #ef4444;
            --accent-purple: #a855f7;
            --accent-teal: #14b8a6;
            
            --sidebar-bg: rgba(10, 16, 28, 0.85);
            --ambient-glow-1: radial-gradient(circle, rgba(14, 165, 233, 0.06) 0%, rgba(7, 11, 19, 0) 70%);
            --ambient-glow-2: radial-gradient(circle, rgba(168, 85, 247, 0.05) 0%, rgba(7, 11, 19, 0) 70%);
            --toast-bg: rgba(13, 21, 37, 0.9);
            --gps-bg: radial-gradient(circle, #0e1b30 0%, #060b13 100%);
            --gps-panel-bg: rgba(10, 16, 28, 0.9);
            --gps-grid-color: rgba(255, 255, 255, 0.03);
            --gps-route-color: rgba(14, 165, 233, 0.15);
            --shadow-primary: 0 10px 20px rgba(0, 0, 0, 0.2);
            --shadow-large: 0 30px 60px rgba(0, 0, 0, 0.7);
            --modal-overlay: rgba(4, 7, 13, 0.8);
            --row-border: rgba(255, 255, 255, 0.02);
            --row-hover: rgba(255, 255, 255, 0.01);
            --form-bg: rgba(255, 255, 255, 0.02);
            --form-focus-bg: rgba(255, 255, 255, 0.04);
            --auth-logo-gradient: linear-gradient(135deg, #f3f4f6, #0ea5e9);
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

        /* Background glows */
        .ambient-glow {
            position: absolute;
            width: 700px;
            height: 700px;
            border-radius: 50%;
            background: var(--ambient-glow-1);
            top: -250px;
            right: -100px;
            z-index: -1;
            pointer-events: none;
        }

        .ambient-glow-left {
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: var(--ambient-glow-2);
            bottom: -150px;
            left: -150px;
            z-index: -1;
            pointer-events: none;
        }

        /* AUTH LAYER */
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
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            box-shadow: var(--shadow-large);
            transition: var(--transition-smooth);
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .auth-logo h2 {
            font-size: 26px;
            font-weight: 700;
            background: var(--auth-logo-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .auth-logo p {
            color: var(--text-muted);
            font-size: 13px;
            margin-top: 6px;
        }

        .demo-credentials-box {
            background: var(--form-bg);
            border: 1px solid var(--glass-border);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 24px;
        }

        .demo-credentials-box h4 {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .demo-roles-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .demo-role-btn {
            background: var(--form-bg);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            padding: 10px;
            cursor: pointer;
            text-align: center;
            transition: var(--transition-smooth);
        }

        .demo-role-btn:hover {
            background: rgba(14, 165, 233, 0.08);
            border-color: var(--accent-blue);
        }

        .demo-role-title {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-main);
        }

        .demo-role-user {
            display: block;
            font-size: 9px;
            color: var(--text-muted);
            margin-top: 2px;
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
            background: var(--form-bg);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            padding: 12px 16px;
            color: var(--text-main);
            font-family: inherit;
            font-size: 14px;
            transition: var(--transition-smooth);
            outline: none;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 10px rgba(14, 165, 233, 0.15);
            background: var(--form-focus-bg);
        }

        .auth-submit-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            border: none;
            border-radius: 10px;
            padding: 14px;
            color: #fff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-smooth);
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.2);
        }

        .auth-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(14, 165, 233, 0.3);
        }

        /* MAIN APP */
        .app-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 270px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--glass-border);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 24px 16px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 10;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
            padding-left: 8px;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            color: #fff;
        }

        .brand span {
            font-weight: 700;
            font-size: 16px;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, var(--text-main), #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .menu-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .menu-item {
            width: 100%;
            background: transparent;
            border: none;
            border-radius: 8px;
            padding: 10px 12px;
            color: var(--text-muted);
            text-align: left;
            font-family: inherit;
            font-size: 13.5px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition-smooth);
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(14, 165, 233, 0.08);
            color: var(--accent-blue);
        }

        .user-profile-wrapper {
            border-top: 1px solid var(--glass-border);
            padding-top: 20px;
            margin-top: 20px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(14, 165, 233, 0.15);
            color: var(--accent-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
        }

        .user-info h4 {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
        }

        .user-info p {
            font-size: 10px;
            color: var(--text-muted);
            margin-top: 2px;
            text-transform: capitalize;
        }

        .logout-btn {
            width: 100%;
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.15);
            border-radius: 8px;
            color: var(--accent-red);
            padding: 10px;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-smooth);
        }

        .logout-btn:hover {
            background: var(--accent-red);
            color: #fff;
        }

        /* MAIN PANEL */
        .main-panel {
            margin-left: 270px;
            flex-grow: 1;
            padding: 40px;
            max-width: calc(100% - 270px);
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
            font-size: 13.5px;
            color: var(--text-muted);
            margin-top: 6px;
        }

        .badge-status {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--form-bg);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 8px 14px;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent-green);
            box-shadow: 0 0 6px var(--accent-green);
        }

        .badge-status span {
            font-size: 12px;
            font-weight: 600;
        }

        /* TOAST ALERT */
        .toast-box {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast {
            background: var(--toast-bg);
            border: 1px solid var(--glass-border);
            border-left: 4px solid var(--accent-blue);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            padding: 16px 24px;
            color: var(--text-main);
            font-size: 13.5px;
            font-weight: 500;
            box-shadow: var(--shadow-large);
            min-width: 280px;
            transform: translateX(120%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .toast.success { border-left-color: var(--accent-green); }
        .toast.error { border-left-color: var(--accent-red); }
        .toast.show { transform: translateX(0); }

        /* TAB PANELS */
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

        /* KPI STAT CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 18px;
            padding: 24px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-primary);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-blue), var(--accent-purple));
        }

        .stat-label {
            font-size: 12.5px;
            color: var(--text-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 26px;
            font-weight: 700;
            margin-top: 10px;
            background: var(--auth-logo-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ANALYTICS PLOTS / LAYOUTS */
        .layout-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .card-panel {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 18px;
            padding: 24px;
            box-shadow: var(--shadow-primary);
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .panel-header h3 {
            font-size: 16px;
            font-weight: 600;
        }

        /* MOCK GPS MAP SIMULATOR */
        .gps-map-container {
            width: 100%;
            height: 250px;
            border-radius: 12px;
            background: var(--gps-bg);
            border: 1px solid var(--glass-border);
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .gps-grid {
            position: absolute;
            width: 100%;
            height: 100%;
            background-size: 30px 30px;
            background-image: 
                linear-gradient(to right, var(--gps-grid-color) 1px, transparent 1px),
                linear-gradient(to bottom, var(--gps-grid-color) 1px, transparent 1px);
        }

        .gps-route-line {
            position: absolute;
            width: 70%;
            height: 2px;
            background: var(--gps-route-color);
            top: 50%;
            left: 15%;
            transform: rotate(-15deg);
        }

        .gps-active-line {
            position: absolute;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-blue), var(--accent-purple));
            top: 50%;
            left: 15%;
            width: 0%;
            transform: rotate(-15deg);
            transition: width 1s linear;
        }

        .gps-pin {
            position: absolute;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: rgba(14, 165, 233, 0.2);
            border: 2px solid var(--accent-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            top: 50%;
            left: 15%;
            transform: translate(-12px, -12px);
            z-index: 2;
        }

        .gps-pin::after {
            content: '';
            width: 8px;
            height: 8px;
            background: var(--accent-blue);
            border-radius: 50%;
        }

        .gps-pin.truck {
            border-color: var(--accent-purple);
            background: rgba(168, 85, 247, 0.2);
            z-index: 3;
            transition: left 1s linear, top 1s linear;
        }

        .gps-pin.truck::after {
            background: var(--accent-purple);
        }

        .gps-pin.dest {
            border-color: var(--accent-green);
            background: rgba(16, 185, 129, 0.2);
            left: 80%;
            top: 32%;
        }

        .gps-pin.dest::after {
            background: var(--accent-green);
        }

        .gps-status-panel {
            position: absolute;
            bottom: 12px;
            left: 12px;
            background: var(--gps-panel-bg);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 11px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 180px;
        }

        /* TABLES */
        .workspace-table-wrapper {
            overflow-x: auto;
            margin-top: 15px;
        }

        .workspace-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .workspace-table th {
            font-size: 11.5px;
            text-transform: uppercase;
            color: var(--text-muted);
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--glass-border);
        }

        .workspace-table td {
            font-size: 13.5px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--row-border);
            color: var(--text-main);
        }

        .workspace-table tbody tr:hover {
            background: var(--row-hover);
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-badge.active, .status-badge.confirmed, .status-badge.delivered, .status-badge.paid {
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.15);
            color: var(--accent-green);
        }

        .status-badge.pending, .status-badge.intransit, .status-badge.in_service {
            background: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 158, 11, 0.15);
            color: var(--accent-yellow);
        }

        .status-badge.assigned, .status-badge.scheduled {
            background: rgba(14, 165, 233, 0.08);
            border: 1px solid rgba(14, 165, 233, 0.15);
            color: var(--accent-blue);
        }

        .status-badge.cancelled, .status-badge.failed, .status-badge.unpaid {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.15);
            color: var(--accent-red);
        }

        /* ACTIONS & BUTTONS */
        .btn {
            background: var(--form-bg);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            color: var(--text-main);
            padding: 8px 16px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn:hover {
            background: rgba(14, 165, 233, 0.08);
            border-color: var(--accent-blue);
            color: var(--accent-blue);
        }

        .btn-primary {
            background: var(--accent-blue);
            border-color: var(--accent-blue);
            color: #fff;
        }

        .btn-primary:hover {
            background: rgba(14, 165, 233, 0.85);
            color: #fff;
        }

        .action-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-box {
            position: relative;
            max-width: 300px;
            width: 100%;
        }

        .search-input {
            width: 100%;
            background: var(--form-bg);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            padding: 10px 16px;
            color: var(--text-main);
            font-family: inherit;
            font-size: 13px;
            outline: none;
        }

        .search-input:focus {
            border-color: var(--accent-blue);
            background: var(--form-focus-bg);
        }

        /* MODAL DIALOGS */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--modal-overlay);
            backdrop-filter: blur(8px);
            z-index: 50;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-content {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            width: 100%;
            max-width: 600px;
            padding: 30px;
            box-shadow: var(--shadow-large);
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            border-bottom: 1px solid var(--glass-border);
            padding-bottom: 12px;
        }

        .modal-header h3 {
            font-size: 16px;
            font-weight: 600;
        }

        .modal-close {
            background: transparent;
            border: none;
            font-size: 20px;
            color: var(--text-muted);
            cursor: pointer;
        }

        .modal-close:hover {
            color: var(--text-main);
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        /* Trend charts */
        .trend-chart-box {
            height: 250px;
            display: flex;
            align-items: flex-end;
            gap: 15px;
            padding-top: 20px;
            border-bottom: 1px solid var(--glass-border);
            position: relative;
        }

        .chart-bar-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
            justify-content: flex-end;
        }

        .chart-bar {
            width: 100%;
            max-width: 40px;
            background: linear-gradient(to top, var(--accent-blue), var(--accent-purple));
            border-radius: 6px 6px 0 0;
            transition: height 0.5s ease;
            position: relative;
        }

        .chart-bar:hover {
            filter: brightness(1.2);
        }

        .chart-val-label {
            position: absolute;
            top: -22px;
            font-size: 10px;
            font-weight: 700;
            color: var(--text-main);
            width: 100%;
            text-align: center;
        }

        .chart-axis-label {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 8px;
            text-align: center;
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
                <h2>TRANSPORT LOGISTICS ERP</h2>
                <p>Enterprise Fleet Management & Dispatch Gateway</p>
            </div>

            <!-- DEMO LOGIN QUICK CREDENTIALS -->
            <div class="demo-credentials-box">
                <h4>Preview Roles (Click to Quick-Login)</h4>
                <div class="demo-roles-grid">
                    <button class="demo-role-btn" onclick="quickLogin('tsuperadmin')">
                        <span class="demo-role-title">Admin</span>
                        <span class="demo-role-user">Super Admin</span>
                    </button>
                    <button class="demo-role-btn" onclick="quickLogin('tfleetmgr')">
                        <span class="demo-role-title">Fleet Mgr</span>
                        <span class="demo-role-user">Fleet Controller</span>
                    </button>
                    <button class="demo-role-btn" onclick="quickLogin('topsmgr')">
                        <span class="demo-role-title">Operations</span>
                        <span class="demo-role-user">Ops Manager</span>
                    </button>
                    <button class="demo-role-btn" onclick="quickLogin('tdriver1')">
                        <span class="demo-role-title">Driver</span>
                        <span class="demo-role-user">Amit Kumar</span>
                    </button>
                    <button class="demo-role-btn" onclick="quickLogin('taccountant')">
                        <span class="demo-role-title">Accountant</span>
                        <span class="demo-role-user">Finance Dept</span>
                    </button>
                    <button class="demo-role-btn" onclick="toggleSandboxMode()">
                        <span class="demo-role-title" style="color: var(--accent-yellow);">Sandbox</span>
                        <span class="demo-role-user">Offline Simulation</span>
                    </button>
                </div>
            </div>

            <div id="loginFormBlock">
                <div class="form-group">
                    <label for="loginUser">Username or Email Address</label>
                    <input type="text" id="loginUser" class="form-input" placeholder="e.g. tsuperadmin">
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
                <div class="brand" style="justify-content: space-between; align-items: center; display: flex; width: 100%;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="brand-icon">TL</div>
                        <span>TRANSPORT ERP</span>
                    </div>
                    <button class="theme-toggle" onclick="toggleTheme()" onmouseover="this.style.color='var(--accent-blue)'; this.style.transform='scale(1.15)'" onmouseout="this.style.color='var(--text-muted)'; this.style.transform='none'" style="background: transparent; border: none; font-size: 16px; cursor: pointer; color: var(--text-muted); transition: var(--transition-smooth); outline: none;" title="Toggle Light/Dark Theme">☀️</button>
                </div>
                <ul class="menu-list">
                    <li><button class="menu-item active" onclick="switchTab('dashboard')">📊 Dashboard</button></li>
                    <li><button class="menu-item" onclick="switchTab('vehicles')">🚛 Fleet Vehicles</button></li>
                    <li><button class="menu-item" onclick="switchTab('drivers')">👥 Drivers</button></li>
                    <li><button class="menu-item" onclick="switchTab('routes')">🗺️ Transit Routes</button></li>
                    <li><button class="menu-item" onclick="switchTab('trips')">📦 Trips Dispatch</button></li>
                    <li><button class="menu-item" onclick="switchTab('deliveries')">📍 Delivery Tracking</button></li>
                    <li><button class="menu-item" onclick="switchTab('fuel')">⛽ Fuel Logs</button></li>
                    <li><button class="menu-item" onclick="switchTab('maintenance')">🔧 Maintenance</button></li>
                    <li><button class="menu-item" onclick="switchTab('salaries')">💵 Salaries</button></li>
                    <li><button class="menu-item" onclick="switchTab('challans')">🎫 Challan Fines</button></li>
                    <li><button class="menu-item" onclick="switchTab('expenses')">💸 Expenses</button></li>
                    <li><button class="menu-item" onclick="switchTab('customers')">🏢 Customers</button></li>
                    <li><button class="menu-item" onclick="switchTab('billing')">🧾 Freight Billing</button></li>
                    <li><button class="menu-item" onclick="switchTab('reports')">📑 Financial Reports</button></li>
                    <li><button class="menu-item" onclick="switchTab('settings')">⚙️ Settings</button></li>
                </ul>
            </div>

            <div class="user-profile-wrapper">
                <div class="user-profile">
                    <div class="avatar" id="userAvatar">A</div>
                    <div class="user-info">
                        <h4 id="userName">Transport Admin</h4>
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
                    <h1 id="pageTitle">Logistics Overview</h1>
                    <p id="pageSub">Performance KPI monitoring and delivery tracking trends</p>
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
                        <div class="stat-label">Active Vehicles</div>
                        <div class="stat-value" id="stat-active-vehicles">0</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Active Trips</div>
                        <div class="stat-value" id="stat-active-trips">0</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Deliveries Today</div>
                        <div class="stat-value" id="stat-deliveries-today">0</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Fuel Expenses</div>
                        <div class="stat-value" id="stat-fuel-expenses">₹0.00</div>
                    </div>
                </div>

                <div class="layout-grid">
                    <div class="card-panel">
                        <div class="panel-header">
                            <h3>Monthly Revenue Collections</h3>
                        </div>
                        <div class="trend-chart-box" id="revenueTrendChart">
                            <!-- Injected bars -->
                        </div>
                    </div>
                    <div class="card-panel">
                        <div class="panel-header">
                            <h3>Operational Efficiency</h3>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 20px; margin-top: 10px;">
                            <div>
                                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px;">
                                    <span>Fleet Utilization</span>
                                    <span id="utilizationVal">0%</span>
                                </div>
                                <div style="width: 100%; height: 8px; background: rgba(255,255,255,0.05); border-radius: 4px; overflow: hidden;">
                                    <div id="utilizationBar" style="height: 100%; width: 0%; background: var(--accent-blue); transition: var(--transition-smooth);"></div>
                                </div>
                            </div>
                            <div>
                                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px;">
                                    <span>Delivery Success Rate</span>
                                    <span id="successRateVal">0%</span>
                                </div>
                                <div style="width: 100%; height: 8px; background: rgba(255,255,255,0.05); border-radius: 4px; overflow: hidden;">
                                    <div id="successRateBar" style="height: 100%; width: 0%; background: var(--accent-green); transition: var(--transition-smooth);"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VEHICLES TAB -->
            <div class="tab-panel" id="tab-vehicles">
                <div class="action-row">
                    <div class="search-box">
                        <input type="text" placeholder="Search vehicles..." class="search-input" oninput="handleSearch('vehicles', this.value)">
                    </div>
                    <button class="btn btn-primary" onclick="openCreateModal('vehicles')">➕ Add Vehicle</button>
                </div>
                <div class="card-panel">
                    <div class="workspace-table-wrapper">
                        <table class="workspace-table">
                            <thead>
                                <tr>
                                    <th>Vehicle Number</th>
                                    <th>Type</th>
                                    <th>Model</th>
                                    <th>Insurance Expiry</th>
                                    <th>Permit Expiry</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-vehicles"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- DRIVERS TAB -->
            <div class="tab-panel" id="tab-drivers">
                <div class="action-row">
                    <div class="search-box">
                        <input type="text" placeholder="Search drivers..." class="search-input" oninput="handleSearch('drivers', this.value)">
                    </div>
                    <button class="btn btn-primary" onclick="openCreateModal('drivers')">➕ Add Driver</button>
                </div>
                <div class="card-panel">
                    <div class="workspace-table-wrapper">
                        <table class="workspace-table">
                            <thead>
                                <tr>
                                    <th>Driver Code</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>License Number</th>
                                    <th>Salary Type</th>
                                    <th>Fixed Salary</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-drivers"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ROUTES TAB -->
            <div class="tab-panel" id="tab-routes">
                <div class="action-row">
                    <div class="search-box">
                        <input type="text" placeholder="Search routes..." class="search-input" oninput="handleSearch('routes', this.value)">
                    </div>
                    <button class="btn btn-primary" onclick="openCreateModal('routes')">➕ Add Route</button>
                </div>
                <div class="card-panel">
                    <div class="workspace-table-wrapper">
                        <table class="workspace-table">
                            <thead>
                                <tr>
                                    <th>Route Code</th>
                                    <th>Source</th>
                                    <th>Destination</th>
                                    <th>Distance (KM)</th>
                                    <th>Est. Time</th>
                                    <th>Toll Charges</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-routes"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TRIPS TAB -->
            <div class="tab-panel" id="tab-trips">
                <div class="action-row">
                    <div class="search-box">
                        <input type="text" placeholder="Search trips..." class="search-input" oninput="handleSearch('trips', this.value)">
                    </div>
                    <button class="btn btn-primary" onclick="openCreateModal('trips')">➕ Dispatch Trip</button>
                </div>
                <div class="card-panel">
                    <div class="workspace-table-wrapper">
                        <table class="workspace-table">
                            <thead>
                                <tr>
                                    <th>Trip Number</th>
                                    <th>Vehicle ID</th>
                                    <th>Driver ID</th>
                                    <th>Route ID</th>
                                    <th>Client</th>
                                    <th>Loading Pt</th>
                                    <th>Unloading Pt</th>
                                    <th>Freight Value</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-trips"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- DELIVERIES TAB -->
            <div class="tab-panel" id="tab-deliveries">
                <div class="layout-grid">
                    <div class="card-panel">
                        <div class="panel-header">
                            <h3>GPS Live Route Tracking Simulator</h3>
                        </div>
                        <div class="gps-map-container">
                            <div class="gps-grid"></div>
                            <div class="gps-route-line"></div>
                            <div class="gps-active-line" id="gpsActiveLine"></div>
                            <div class="gps-pin source"></div>
                            <div class="gps-pin dest"></div>
                            <div class="gps-pin truck" id="simulatedTruck"></div>
                            <div class="gps-status-panel">
                                <div><strong>Tracking Code:</strong> <span id="mapTrackingCode">TRK-9908129</span></div>
                                <div><strong>Client:</strong> <span id="mapClientName">Tata Steel Ltd</span></div>
                                <div><strong>Current Position:</strong> <span id="mapCoords">18.6278° N, 73.8131° E</span></div>
                                <div><strong>Speed:</strong> <span id="mapSpeed">65 km/h</span></div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary" id="startSimBtn" onclick="startGpsSimulation()">⚡ Start Simulation</button>
                            <button class="btn" onclick="resetGpsSimulation()">Reset</button>
                        </div>
                    </div>
                    <div class="card-panel">
                        <div class="panel-header">
                            <h3>Deliveries Registry</h3>
                        </div>
                        <div class="workspace-table-wrapper">
                            <table class="workspace-table">
                                <thead>
                                    <tr>
                                        <th>Tracking Code</th>
                                        <th>Client</th>
                                        <th>Status</th>
                                        <th>POD Doc</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="table-body-deliveries"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FUEL TAB -->
            <div class="tab-panel" id="tab-fuel">
                <div class="action-row">
                    <div class="search-box">
                        <input type="text" placeholder="Search fuel station..." class="search-input" oninput="handleSearch('fuel', this.value)">
                    </div>
                    <button class="btn btn-primary" onclick="openCreateModal('fuel')">➕ Log Fuel Fill</button>
                </div>
                <div class="card-panel">
                    <div class="workspace-table-wrapper">
                        <table class="workspace-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Vehicle ID</th>
                                    <th>Station</th>
                                    <th>Liters</th>
                                    <th>Rate/L</th>
                                    <th>Total Cost</th>
                                    <th>Odometer</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-fuel"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- MAINTENANCE TAB -->
            <div class="tab-panel" id="tab-maintenance">
                <div class="action-row">
                    <div class="search-box">
                        <input type="text" placeholder="Search service center..." class="search-input" oninput="handleSearch('maintenance', this.value)">
                    </div>
                    <button class="btn btn-primary" onclick="openCreateModal('maintenance')">➕ Log Maintenance</button>
                </div>
                <div class="card-panel">
                    <div class="workspace-table-wrapper">
                        <table class="workspace-table">
                            <thead>
                                <tr>
                                    <th>Vehicle ID</th>
                                    <th>Service Type</th>
                                    <th>Center</th>
                                    <th>Cost</th>
                                    <th>Date</th>
                                    <th>Next Due</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-maintenance"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SALARIES TAB -->
            <div class="tab-panel" id="tab-salaries">
                <div class="action-row">
                    <div class="search-box">
                        <input type="text" placeholder="Search month..." class="search-input" oninput="handleSearch('salaries', this.value)">
                    </div>
                    <button class="btn btn-primary" onclick="openCreateModal('salaries')">➕ Run Payroll Calculation</button>
                </div>
                <div class="card-panel">
                    <div class="workspace-table-wrapper">
                        <table class="workspace-table">
                            <thead>
                                <tr>
                                    <th>Driver ID</th>
                                    <th>Month</th>
                                    <th>Fixed Salary</th>
                                    <th>Trip Bonuses</th>
                                    <th>Allowances</th>
                                    <th>Deductions</th>
                                    <th>Total Salary</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-salaries"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- CHALLANS TAB -->
            <div class="tab-panel" id="tab-challans">
                <div class="action-row">
                    <div class="search-box">
                        <input type="text" placeholder="Search challan number..." class="search-input" oninput="handleSearch('challans', this.value)">
                    </div>
                    <button class="btn btn-primary" onclick="openCreateModal('challans')">➕ Log Fine Ticket</button>
                </div>
                <div class="card-panel">
                    <div class="workspace-table-wrapper">
                        <table class="workspace-table">
                            <thead>
                                <tr>
                                    <th>Challan Number</th>
                                    <th>Vehicle ID</th>
                                    <th>Driver ID</th>
                                    <th>Violation Type</th>
                                    <th>Fine Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-challans"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- EXPENSES TAB -->
            <div class="tab-panel" id="tab-expenses">
                <div class="action-row">
                    <div class="search-box">
                        <input type="text" placeholder="Search expense type..." class="search-input" oninput="handleSearch('expenses', this.value)">
                    </div>
                    <button class="btn btn-primary" onclick="openCreateModal('expenses')">➕ Log Expense</button>
                </div>
                <div class="card-panel">
                    <div class="workspace-table-wrapper">
                        <table class="workspace-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Trip ID</th>
                                    <th>Expense Type</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-expenses"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- CUSTOMERS TAB -->
            <div class="tab-panel" id="tab-customers">
                <div class="action-row">
                    <div class="search-box">
                        <input type="text" placeholder="Search customers..." class="search-input" oninput="handleSearch('customers', this.value)">
                    </div>
                    <button class="btn btn-primary" onclick="openCreateModal('customers')">➕ Add Customer</button>
                </div>
                <div class="card-panel">
                    <div class="workspace-table-wrapper">
                        <table class="workspace-table">
                            <thead>
                                <tr>
                                    <th>Customer Code</th>
                                    <th>Company Name</th>
                                    <th>Contact Person</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>GST Number</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-customers"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- BILLING TAB -->
            <div class="tab-panel" id="tab-billing">
                <div class="action-row">
                    <div class="search-box">
                        <input type="text" placeholder="Search invoice..." class="search-input" oninput="handleSearch('billing', this.value)">
                    </div>
                    <button class="btn btn-primary" onclick="openCreateModal('billing')">➕ Generate GST Invoice</button>
                </div>
                <div class="card-panel">
                    <div class="workspace-table-wrapper">
                        <table class="workspace-table">
                            <thead>
                                <tr>
                                    <th>Invoice Number</th>
                                    <th>Trip ID</th>
                                    <th>Customer ID</th>
                                    <th>Freight Cost</th>
                                    <th>Fuel Surcharge</th>
                                    <th>GST Amount (18%)</th>
                                    <th>Total Cost</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-billing"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- REPORTS TAB -->
            <div class="tab-panel" id="tab-reports">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-label">Total Billing Collections</div>
                        <div class="stat-value" id="report-total-revenue">₹119,870.00</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Total Operational Costs</div>
                        <div class="stat-value" id="report-total-expenses" style="color: var(--accent-red);">₹41,554.10</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Net Operating Profits</div>
                        <div class="stat-value" id="report-net-profit" style="color: var(--accent-green);">₹78,315.90</div>
                    </div>
                </div>

                <div class="layout-grid">
                    <div class="card-panel" style="grid-column: span 2;">
                        <div class="panel-header">
                            <h3>Profit and Loss Ledger Overview</h3>
                        </div>
                        <div class="workspace-table-wrapper">
                            <table class="workspace-table">
                                <thead>
                                    <tr>
                                        <th>Financial Ledger Item</th>
                                        <th>Revenue Inflow (₹)</th>
                                        <th>Outflow Expenses (₹)</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Basic Freight Billing</td>
                                        <td id="ledger-freight-inflow">₹110,000.00</td>
                                        <td>-</td>
                                        <td>Total logistics dispatch invoiced values.</td>
                                    </tr>
                                    <tr>
                                        <td>Fuel Surcharges</td>
                                        <td id="ledger-surcharge-inflow">₹1,500.00</td>
                                        <td>-</td>
                                        <td>Direct client diesel premium additions.</td>
                                    </tr>
                                    <tr>
                                        <td>Diesel & Fuel Expenses</td>
                                        <td>-</td>
                                        <td id="ledger-fuel-outflow">₹8,054.10</td>
                                        <td>Automobile fueling invoices registered.</td>
                                    </tr>
                                    <tr>
                                        <td>Fleet Maintenance Costs</td>
                                        <td>-</td>
                                        <td id="ledger-maintenance-outflow">₹8,500.00</td>
                                        <td>Lubricants, tire services, filter changes.</td>
                                    </tr>
                                    <tr>
                                        <td>Driver Salaries</td>
                                        <td>-</td>
                                        <td id="ledger-salaries-outflow">₹25,000.00</td>
                                        <td>Paid driver salary payroll distributions.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SETTINGS TAB -->
            <div class="tab-panel" id="tab-settings">
                <div class="card-panel" style="max-width: 600px;">
                    <div class="panel-header">
                        <h3>Configuration Settings</h3>
                    </div>
                    <div class="form-group">
                        <label for="smtpHost">SMTP Server Host</label>
                        <input type="text" id="smtpHost" class="form-input" placeholder="smtp.gmail.com">
                    </div>
                    <div class="form-group">
                        <label for="smtpPort">SMTP Server Port</label>
                        <input type="text" id="smtpPort" class="form-input" placeholder="587">
                    </div>
                    <div class="form-group">
                        <label for="smtpUser">SMTP Username</label>
                        <input type="text" id="smtpUser" class="form-input" placeholder="username@gmail.com">
                    </div>
                    <div class="form-group">
                        <label for="smtpPass">SMTP Password</label>
                        <input type="password" id="smtpPass" class="form-input" value="******">
                    </div>
                    <button class="btn btn-primary" onclick="saveSettings()">Save Settings</button>
                </div>
            </div>
        </main>
    </div>

    <!-- GENERAL ENTITY CREATION MODALS -->
    
    <!-- VEHICLE MODAL -->
    <div class="modal" id="modal-vehicles">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Vehicle Registration</h3>
                <button class="modal-close" onclick="closeModal('vehicles')">&times;</button>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label for="createVehicleNum">Vehicle Number</label>
                    <input type="text" id="createVehicleNum" class="form-input" placeholder="e.g. MH-12-GQ-4321">
                </div>
                <div class="form-group">
                    <label for="createVehicleType">Vehicle Type</label>
                    <select id="createVehicleType" class="form-select">
                        <option value="Container">Container</option>
                        <option value="Truck">Truck</option>
                        <option value="Trailer">Trailer</option>
                        <option value="Tempo">Tempo</option>
                        <option value="Van">Van</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="createVehicleModel">Vehicle Model</label>
                <input type="text" id="createVehicleModel" class="form-input" placeholder="e.g. Tata Prima 4925.S">
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button class="btn" onclick="closeModal('vehicles')">Cancel</button>
                <button class="btn btn-primary" onclick="submitCreate('vehicles')">Register Vehicle</button>
            </div>
        </div>
    </div>

    <!-- DRIVER MODAL -->
    <div class="modal" id="modal-drivers">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Log Driver Profile</h3>
                <button class="modal-close" onclick="closeModal('drivers')">&times;</button>
            </div>
            <div class="form-group">
                <label for="createDriverName">Driver Full Name</label>
                <input type="text" id="createDriverName" class="form-input" placeholder="e.g. Sube Singh">
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label for="createDriverMobile">Mobile Number</label>
                    <input type="text" id="createDriverMobile" class="form-input" placeholder="9988776655">
                </div>
                <div class="form-group">
                    <label for="createDriverLic">License Number</label>
                    <input type="text" id="createDriverLic" class="form-input" placeholder="DL-XXXX-YYYY">
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label for="createDriverSalaryType">Salary Structure</label>
                    <select id="createDriverSalaryType" class="form-select">
                        <option value="fixed">Fixed Monthly</option>
                        <option value="per_trip">Per Trip Rate</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="createDriverSalaryVal">Amount (₹)</label>
                    <input type="number" id="createDriverSalaryVal" class="form-input" placeholder="25000">
                </div>
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button class="btn" onclick="closeModal('drivers')">Cancel</button>
                <button class="btn btn-primary" onclick="submitCreate('drivers')">Create Profile</button>
            </div>
        </div>
    </div>

    <!-- ROUTE MODAL -->
    <div class="modal" id="modal-routes">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Configure Route</h3>
                <button class="modal-close" onclick="closeModal('routes')">&times;</button>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label for="createRouteSrc">Source City</label>
                    <input type="text" id="createRouteSrc" class="form-input" placeholder="Mumbai">
                </div>
                <div class="form-group">
                    <label for="createRouteDest">Destination City</label>
                    <input type="text" id="createRouteDest" class="form-input" placeholder="Pune">
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label for="createRouteDistance">Distance (KM)</label>
                    <input type="number" id="createRouteDistance" class="form-input" placeholder="150">
                </div>
                <div class="form-group">
                    <label for="createRouteToll">Toll Charges (₹)</label>
                    <input type="number" id="createRouteToll" class="form-input" placeholder="320">
                </div>
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button class="btn" onclick="closeModal('routes')">Cancel</button>
                <button class="btn btn-primary" onclick="submitCreate('routes')">Add Route</button>
            </div>
        </div>
    </div>

    <!-- TRIP DISPATCH MODAL -->
    <div class="modal" id="modal-trips">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Dispatch New Trip Load</h3>
                <button class="modal-close" onclick="closeModal('trips')">&times;</button>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label for="createTripVeh">Vehicle</label>
                    <select id="createTripVeh" class="form-select"></select>
                </div>
                <div class="form-group">
                    <label for="createTripDriver">Driver</label>
                    <select id="createTripDriver" class="form-select"></select>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label for="createTripRoute">Route</label>
                    <select id="createTripRoute" class="form-select"></select>
                </div>
                <div class="form-group">
                    <label for="createTripFreight">Freight Rate (₹)</label>
                    <input type="number" id="createTripFreight" class="form-input" placeholder="45000">
                </div>
            </div>
            <div class="form-group">
                <label for="createTripCustomer">Client Company Name</label>
                <input type="text" id="createTripCustomer" class="form-input" placeholder="e.g. Tata Steel Ltd">
            </div>
            <div class="form-group">
                <label for="createTripUnloading">Delivery Destination Address</label>
                <input type="text" id="createTripUnloading" class="form-input" placeholder="Plot 42, MIDC Pune">
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button class="btn" onclick="closeModal('trips')">Cancel</button>
                <button class="btn btn-primary" onclick="submitCreate('trips')">Dispatch</button>
            </div>
        </div>
    </div>

    <!-- FUEL LOG MODAL -->
    <div class="modal" id="modal-fuel">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Log Fuel Purchase</h3>
                <button class="modal-close" onclick="closeModal('fuel')">&times;</button>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label for="createFuelVeh">Vehicle</label>
                    <select id="createFuelVeh" class="form-select"></select>
                </div>
                <div class="form-group">
                    <label for="createFuelStation">Station Name</label>
                    <input type="text" id="createFuelStation" class="form-input" placeholder="HP Pump Expressway">
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label for="createFuelQty">Liters Quantity</label>
                    <input type="number" id="createFuelQty" class="form-input" placeholder="80.5">
                </div>
                <div class="form-group">
                    <label for="createFuelRate">Rate per Liter (₹)</label>
                    <input type="number" id="createFuelRate" class="form-input" placeholder="94.20">
                </div>
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button class="btn" onclick="closeModal('fuel')">Cancel</button>
                <button class="btn btn-primary" onclick="submitCreate('fuel')">Save Log</button>
            </div>
        </div>
    </div>

    <!-- BILLING MODAL -->
    <div class="modal" id="modal-billing">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Generate Invoice</h3>
                <button class="modal-close" onclick="closeModal('billing')">&times;</button>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label for="createBillTrip">Select Completed Trip</label>
                    <select id="createBillTrip" class="form-select"></select>
                </div>
                <div class="form-group">
                    <label for="createBillClient">Select Client Company</label>
                    <select id="createBillClient" class="form-select"></select>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label for="createBillFreight">Freight Base Rate (₹)</label>
                    <input type="number" id="createBillFreight" class="form-input" placeholder="45000">
                </div>
                <div class="form-group">
                    <label for="createBillSurcharge">Fuel Surcharge Premium (₹)</label>
                    <input type="number" id="createBillSurcharge" class="form-input" placeholder="1500">
                </div>
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button class="btn" onclick="closeModal('billing')">Cancel</button>
                <button class="btn btn-primary" onclick="submitCreate('billing')">Issue Invoice</button>
            </div>
        </div>
    </div>

    <!-- MOCK PDF PRINT VIEW DIALOG -->
    <div class="modal" id="modal-invoice-pdf">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3>GST Freight Tax Invoice PDF</h3>
                <button class="modal-close" onclick="closeModal('invoice-pdf')">&times;</button>
            </div>
            <div id="pdfContentBlock" style="border: 1px solid var(--glass-border); border-radius: 12px; background: #fff; margin-bottom: 20px;">
                <!-- PDF HTML code -->
            </div>
            <div style="text-align: right;">
                <button class="btn" onclick="closeModal('invoice-pdf')">Close</button>
                <button class="btn btn-primary" onclick="printPdfMock()">🖨️ Print Receipt</button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT WORKSPACE LOGIC -->
    <script>
        let isSandbox = localStorage.getItem('tr_is_sandbox') === 'true';
        let authToken = localStorage.getItem('tr_auth_token') || '';
        let currentUser = null;
        try {
            const storedUser = localStorage.getItem('tr_current_user');
            if (storedUser) {
                currentUser = JSON.parse(storedUser);
            }
        } catch (e) {
            console.error('Error parsing stored user:', e);
        }

        // Mock Database Sandbox Fallback Data
        let mockData = {
            vehicles: [
                { id: 1, vehicle_number: 'MH-12-GQ-4321', vehicle_type: 'Container', vehicle_model: 'Tata Prima 4925.S', registration_number: 'REG-MH-827389', insurance_expiry: '2027-01-15', permit_expiry: '2027-05-20', status: 'ACTIVE' },
                { id: 2, vehicle_number: 'HR-55-AA-1122', vehicle_type: 'Truck', vehicle_model: 'Ashok Leyland Ecomet', registration_number: 'REG-HR-998811', insurance_expiry: '2026-09-12', permit_expiry: '2027-02-10', status: 'ACTIVE' },
                { id: 3, vehicle_number: 'KA-03-MM-7788', vehicle_type: 'Van', vehicle_model: 'Mahindra Bolero Pik-Up', registration_number: 'REG-KA-112233', insurance_expiry: '2026-11-05', permit_expiry: '2026-10-30', status: 'IN_SERVICE' }
            ],
            drivers: [
                { id: 1, driver_code: 'DRV-001', name: 'Amit Kumar', mobile: '9988776655', license_number: 'DL-MH12-20150927', license_expiry: '2030-05-14', salary_type: 'fixed', fixed_salary: 25000, status: 'ACTIVE' },
                { id: 2, driver_code: 'DRV-002', name: 'Rajesh Patil', mobile: '9876543210', license_number: 'DL-GJ01-20188273', license_expiry: '2028-11-12', salary_type: 'per_trip', fixed_salary: 0, status: 'ACTIVE' }
            ],
            routes: [
                { id: 1, route_code: 'RTE-MUM-PUN', source: 'Mumbai', destination: 'Pune', distance_km: 150, estimated_time: '3.5 Hours', toll_charges: 320.00, status: 'ACTIVE' },
                { id: 2, route_code: 'RTE-DEL-JAI', source: 'Delhi', destination: 'Jaipur', distance_km: 270, estimated_time: '5 Hours', toll_charges: 540.00, status: 'ACTIVE' }
            ],
            trips: [
                { id: 1, trip_number: 'TRIP-2026-0001', vehicle_id: 1, driver_id: 1, route_id: 1, customer_name: 'Tata Steel Ltd', loading_point: 'JNPT Port Mumbai', unloading_point: 'Chinchwad Depot Pune', trip_start_date: '2026-06-15', trip_end_date: '2026-06-16', freight_amount: 45000, status: 'Delivered' },
                { id: 2, trip_number: 'TRIP-2026-0002', vehicle_id: 2, driver_id: 2, route_id: 2, customer_name: 'Marwari Traders', loading_point: 'S Nagar Delhi', unloading_point: 'Mansarovar Jaipur', trip_start_date: '2026-06-17', trip_end_date: null, freight_amount: 65000, status: 'In Transit' }
            ],
            deliveries: [
                { id: 1, trip_id: 1, tracking_number: 'TRK-9908129', customer_name: 'Tata Steel Ltd', delivery_address: 'Plot 42, G-Block MIDC Chinchwad Pune', delivery_status: 'Delivered', latitude: '18.6278', longitude: '73.8131', proof_of_delivery: 'pod-mock.pdf' },
                { id: 2, trip_id: 2, tracking_number: 'TRK-1122883', customer_name: 'Marwari Traders', delivery_address: 'Mansarovar Ind Area, Jaipur', delivery_status: 'In Transit', latitude: '27.2038', longitude: '75.8012', proof_of_delivery: '' }
            ],
            fuel: [
                { id: 1, vehicle_id: 1, trip_id: 1, fuel_station: 'HP Pump Expressway', fuel_quantity: 85.50, rate_per_liter: 94.20, total_cost: 8054.10, odometer_reading: 14520, fuel_date: '2026-06-15' }
            ],
            maintenance: [
                { id: 1, vehicle_id: 1, maintenance_type: 'Oil Change', description: 'Engine oil filter changes', service_center: 'Tata Panvel Center', cost: 8500, service_date: '2026-05-10', next_service_date: '2026-09-10', status: 'Completed' }
            ],
            salaries: [
                { id: 1, driver_id: 1, salary_month: '2026-06', fixed_salary: 25000, trip_bonus: 500, allowance: 0, deduction: 0, total_salary: 25500, payment_status: 'Paid' }
            ],
            challans: [
                { id: 1, vehicle_id: 1, driver_id: 1, challan_number: 'CH-2026-88912', challan_type: 'Overloading', challan_amount: 5000, challan_date: '2026-06-16', payment_status: 'Pending', remarks: 'Expressway weigh plaza fine' }
            ],
            expenses: [
                { id: 1, trip_id: 1, expense_type: 'Toll', amount: 320, expense_date: '2026-06-15', description: ' Expressway toll fees' }
            ],
            customers: [
                { id: 1, customer_code: 'CUST-001', company_name: 'Tata Steel Ltd', contact_person: 'Sanjay Bhatia', mobile: '9892019283', email: 'logistics@tatasteel.com', gst_number: '20AAACT1234F1Z5' },
                { id: 2, customer_code: 'CUST-002', company_name: 'Marwari Traders', contact_person: 'Ramesh Marwari', mobile: '9320192837', email: 'sales@marwari.com', gst_number: '07AAAAM8829K2Z2' }
            ],
            billing: [
                { id: 1, invoice_number: 'INV-2026-0001', trip_id: 1, customer_id: 1, freight_amount: 45000, fuel_surcharge: 1500, gst_amount: 8370, total_amount: 54870, payment_status: 'Paid', invoice_date: '2026-06-16' }
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
            localStorage.setItem('re_is_sandbox', 'true');
            localStorage.setItem('re_auth_token', authToken);
            localStorage.setItem('re_current_user', JSON.stringify(currentUser));

            document.getElementById('authSection').style.display = 'none';
            document.getElementById('appSection').style.display = 'flex';
            
            document.getElementById('userName').innerText = currentUser.name;
            document.getElementById('userRole').innerText = currentUser.role.replace('transport_', '').replace('_', ' ');
            document.getElementById('userAvatar').innerText = currentUser.name.charAt(0);

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
            
            const activeBtn = Array.from(document.querySelectorAll('.menu-item')).find(btn => btn.innerText.toLowerCase().includes(tabName.substring(0,4)));
            if (activeBtn) activeBtn.classList.add('active');

            const activePanel = document.getElementById(`tab-${tabName}`);
            if (activePanel) activePanel.classList.add('active');

            document.getElementById('pageTitle').innerText = tabName.charAt(0).toUpperCase() + tabName.slice(1) + ' Workspace';
            document.getElementById('pageSub').innerText = `Manage, search, and audit your ${tabName} records`;
            
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

            fetch('/wp-json/transport-management/v1/auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username: user, password: pass })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    isSandbox = false;
                    authToken = data.data.token;
                    currentUser = data.data.user;

                    localStorage.setItem('re_is_sandbox', 'false');
                    localStorage.setItem('re_auth_token', authToken);
                    localStorage.setItem('re_current_user', JSON.stringify(currentUser));
                    
                    document.getElementById('authSection').style.display = 'none';
                    document.getElementById('appSection').style.display = 'flex';
                    
                    document.getElementById('userName').innerText = currentUser.name;
                    document.getElementById('userRole').innerText = currentUser.role.replace('transport_', '').replace('_', ' ');
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
            isSandbox = false;
            localStorage.removeItem('re_is_sandbox');
            localStorage.removeItem('re_auth_token');
            localStorage.removeItem('re_current_user');
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

            const token = authToken;
            let url = `/wp-json/transport-management/v1/${tab === 'salaries' ? 'driver-salary' : tab}`;
            if (tab === 'dashboard' || tab === 'reports') {
                url = `/wp-json/transport-management/v1/${tab === 'reports' ? 'reports/profit-loss' : 'dashboard'}`;
            }

            fetch(url, {
                headers: { 'Authorization': `Bearer ${token}` }
            })
            .then(res => res.json())
            .then(resObj => {
                if (resObj.success) {
                    if (tab === 'dashboard') {
                        renderDashboardStats(resObj.data);
                    } else if (tab === 'reports') {
                        renderReportsStats(resObj.data);
                    } else {
                        renderLiveList(tab, resObj.data.data || resObj.data);
                    }
                }
            })
            .catch(err => {
                console.error(err);
                toggleSandboxMode();
            });
        }

        // Render dashboard values
        function renderDashboardStats(data) {
            document.getElementById('stat-active-vehicles').innerText = data.cards.active_vehicles;
            document.getElementById('stat-active-trips').innerText = data.cards.active_trips;
            document.getElementById('stat-deliveries-today').innerText = data.cards.deliveries_today;
            document.getElementById('stat-fuel-expenses').innerText = '₹' + parseFloat(data.cards.fuel_expenses).toLocaleString('en-IN', { maximumFractionDigits: 1 });
            
            document.getElementById('utilizationVal').innerText = data.analytics.fleet_utilization + '%';
            document.getElementById('utilizationBar').style.width = data.analytics.fleet_utilization + '%';
            
            document.getElementById('successRateVal').innerText = data.analytics.delivery_success_rate + '%';
            document.getElementById('successRateBar').style.width = data.analytics.delivery_success_rate + '%';

            renderTrendChart(data.analytics.revenue_trends);
        }

        // Render Reports statistics
        function renderReportsStats(data) {
            document.getElementById('report-total-revenue').innerText = '₹' + parseFloat(data.revenue.billing_collections).toLocaleString('en-IN');
            document.getElementById('report-total-expenses').innerText = '₹' + parseFloat(data.expenses.total_expenses).toLocaleString('en-IN');
            document.getElementById('report-net-profit').innerText = '₹' + parseFloat(data.net_profit).toLocaleString('en-IN');

            document.getElementById('ledger-freight-inflow').innerText = '₹' + parseFloat(data.revenue.billing_collections).toLocaleString('en-IN');
            document.getElementById('ledger-fuel-outflow').innerText = '₹' + parseFloat(data.expenses.fuel).toLocaleString('en-IN');
            document.getElementById('ledger-maintenance-outflow').innerText = '₹' + parseFloat(data.expenses.maintenance).toLocaleString('en-IN');
            document.getElementById('ledger-salaries-outflow').innerText = '₹' + parseFloat(data.expenses.driver_salaries).toLocaleString('en-IN');
        }

        // Draw dynamic bar chart
        function renderTrendChart(trends) {
            const chart = document.getElementById('revenueTrendChart');
            chart.innerHTML = '';

            let data = trends && trends.length > 0 ? trends : [
                { month: 'Jan 2026', amount: 45000 },
                { month: 'Feb 2026', amount: 60000 },
                { month: 'Mar 2026', amount: 80000 },
                { month: 'Apr 2026', amount: 55000 },
                { month: 'May 2026', amount: 95000 },
                { month: 'Jun 2026', amount: 119870 }
            ];

            const maxAmount = Math.max(...data.map(d => parseFloat(d.amount || d.collected || 0)), 1);

            data.forEach(item => {
                const wrapper = document.createElement('div');
                wrapper.className = 'chart-bar-wrapper';
                
                const val = parseFloat(item.amount || item.collected || 0);
                const pct = (val / maxAmount) * 75; // max 75% height

                const bar = document.createElement('div');
                bar.className = 'chart-bar';
                bar.style.height = `${pct}%`;

                const valLabel = document.createElement('span');
                valLabel.className = 'chart-val-label';
                valLabel.innerText = '₹' + (val / 1000).toFixed(0) + 'K';
                bar.appendChild(valLabel);

                const axisLabel = document.createElement('span');
                axisLabel.className = 'chart-axis-label';
                axisLabel.innerText = item.month;

                wrapper.appendChild(bar);
                wrapper.appendChild(axisLabel);
                chart.appendChild(wrapper);
            });
        }

        // List Renderers
        function renderLiveList(tab, rows) {
            const tbody = document.getElementById(`table-body-${tab}`);
            if (!tbody) return;
            tbody.innerHTML = '';

            if (rows.length === 0) {
                tbody.innerHTML = `<tr><td colspan="10" style="text-align: center; color: var(--text-muted);">No records found.</td></tr>`;
                return;
            }

            rows.forEach(row => {
                let tr = document.createElement('tr');
                if (tab === 'vehicles') {
                    tr.innerHTML = `
                        <td><strong>${row.vehicle_number}</strong></td>
                        <td>${row.vehicle_type}</td>
                        <td>${row.vehicle_model}</td>
                        <td>${row.insurance_expiry || 'N/A'}</td>
                        <td>${row.permit_expiry || 'N/A'}</td>
                        <td><span class="status-badge ${row.status.toLowerCase().replace('_','') === 'active' ? 'active' : 'pending'}">${row.status}</span></td>
                        <td>
                            <button class="btn" style="padding: 4px 8px; font-size: 11px;" onclick="deleteRecord('vehicles', ${row.id})">🗑️ Delete</button>
                        </td>
                    `;
                } else if (tab === 'drivers') {
                    tr.innerHTML = `
                        <td><strong>${row.driver_code}</strong></td>
                        <td>${row.name}</td>
                        <td>${row.mobile}</td>
                        <td>${row.license_number}</td>
                        <td>${row.salary_type}</td>
                        <td>₹${row.fixed_salary || row.per_trip_salary}</td>
                        <td><span class="status-badge active">${row.status}</span></td>
                        <td>
                            <button class="btn" style="padding: 4px 8px; font-size: 11px;" onclick="deleteRecord('drivers', ${row.id})">🗑️ Delete</button>
                        </td>
                    `;
                } else if (tab === 'routes') {
                    tr.innerHTML = `
                        <td><strong>${row.route_code}</strong></td>
                        <td>${row.source}</td>
                        <td>${row.destination}</td>
                        <td>${row.distance_km} KM</td>
                        <td>${row.estimated_time}</td>
                        <td>₹${row.toll_charges}</td>
                        <td><span class="status-badge active">${row.status}</span></td>
                        <td>
                            <button class="btn" style="padding: 4px 8px; font-size: 11px;" onclick="deleteRecord('routes', ${row.id})">🗑️ Delete</button>
                        </td>
                    `;
                } else if (tab === 'trips') {
                    tr.innerHTML = `
                        <td><strong>${row.trip_number}</strong></td>
                        <td>Veh #${row.vehicle_id}</td>
                        <td>Drv #${row.driver_id}</td>
                        <td>Rte #${row.route_id}</td>
                        <td>${row.customer_name}</td>
                        <td>${row.loading_point}</td>
                        <td>${row.unloading_point}</td>
                        <td>₹${row.freight_amount}</td>
                        <td><span class="status-badge ${row.status.toLowerCase()}">${row.status}</span></td>
                        <td>
                            ${row.status !== 'Delivered' ? `<button class="btn btn-primary" style="padding: 4px 8px; font-size: 11px;" onclick="completeTrip(${row.id})">🏁 Deliver</button>` : ''}
                            <button class="btn" style="padding: 4px 8px; font-size: 11px;" onclick="deleteRecord('trips', ${row.id})">🗑️ Delete</button>
                        </td>
                    `;
                } else if (tab === 'deliveries') {
                    tr.innerHTML = `
                        <td><strong>${row.tracking_number}</strong></td>
                        <td>${row.customer_name}</td>
                        <td><span class="status-badge ${row.delivery_status.toLowerCase().replace(' ','')}">${row.delivery_status}</span></td>
                        <td>${row.proof_of_delivery ? `<a href="#" style="color: var(--accent-green); text-decoration: none;" onclick="showToast('Downloading POD file...')">📄 View POD</a>` : 'Pending'}</td>
                        <td>
                            <button class="btn" style="padding: 4px 8px; font-size: 11px;" onclick="loadGpsTracker('${row.tracking_number}', '${row.customer_name}')">🗺️ Track</button>
                            ${row.delivery_status !== 'Delivered' ? `<button class="btn" style="padding: 4px 8px; font-size: 11px; border-color: var(--accent-green); color: var(--accent-green);" onclick="uploadPodMock(${row.id})">📤 Upload POD</button>` : ''}
                        </td>
                    `;
                } else if (tab === 'fuel') {
                    tr.innerHTML = `
                        <td>${row.fuel_date}</td>
                        <td>Veh #${row.vehicle_id}</td>
                        <td>${row.fuel_station}</td>
                        <td>${row.fuel_quantity} L</td>
                        <td>₹${row.rate_per_liter}</td>
                        <td><strong>₹${row.total_cost}</strong></td>
                        <td>${row.odometer_reading} KM</td>
                        <td>
                            <button class="btn" style="padding: 4px 8px; font-size: 11px;" onclick="deleteRecord('fuel', ${row.id})">🗑️ Delete</button>
                        </td>
                    `;
                } else if (tab === 'maintenance') {
                    tr.innerHTML = `
                        <td>Veh #${row.vehicle_id}</td>
                        <td>${row.maintenance_type}</td>
                        <td>${row.service_center}</td>
                        <td>₹${row.cost}</td>
                        <td>${row.service_date}</td>
                        <td>${row.next_service_date || 'N/A'}</td>
                        <td><span class="status-badge active">${row.status}</span></td>
                        <td>
                            <button class="btn" style="padding: 4px 8px; font-size: 11px;" onclick="deleteRecord('maintenance', ${row.id})">🗑️ Delete</button>
                        </td>
                    `;
                } else if (tab === 'salaries') {
                    tr.innerHTML = `
                        <td>Drv #${row.driver_id}</td>
                        <td>${row.salary_month}</td>
                        <td>₹${row.fixed_salary}</td>
                        <td>₹${row.trip_bonus}</td>
                        <td>₹${row.allowance}</td>
                        <td>₹${row.deduction}</td>
                        <td><strong>₹${row.total_salary}</strong></td>
                        <td><span class="status-badge ${row.payment_status.toLowerCase()}">${row.payment_status}</span></td>
                        <td>
                            ${row.payment_status !== 'Paid' ? `<button class="btn btn-primary" style="padding: 4px 8px; font-size: 11px;" onclick="paySalary(${row.id})">💳 Settle</button>` : ''}
                        </td>
                    `;
                } else if (tab === 'challans') {
                    tr.innerHTML = `
                        <td><strong>${row.challan_number}</strong></td>
                        <td>Veh #${row.vehicle_id}</td>
                        <td>Drv #${row.driver_id}</td>
                        <td>${row.challan_type}</td>
                        <td>₹${row.challan_amount}</td>
                        <td>${row.challan_date}</td>
                        <td><span class="status-badge ${row.payment_status.toLowerCase()}">${row.payment_status}</span></td>
                        <td>
                            ${row.payment_status !== 'Paid' ? `<button class="btn btn-primary" style="padding: 4px 8px; font-size: 11px;" onclick="payChallan(${row.id})">💳 Pay</button>` : ''}
                        </td>
                    `;
                } else if (tab === 'expenses') {
                    tr.innerHTML = `
                        <td>${row.expense_date}</td>
                        <td>Trip #${row.trip_id || 'N/A'}</td>
                        <td>${row.expense_type}</td>
                        <td><strong>₹${row.amount}</strong></td>
                        <td>${row.description}</td>
                        <td>
                            <button class="btn" style="padding: 4px 8px; font-size: 11px;" onclick="deleteRecord('expenses', ${row.id})">🗑️ Delete</button>
                        </td>
                    `;
                } else if (tab === 'customers') {
                    tr.innerHTML = `
                        <td><strong>${row.customer_code}</strong></td>
                        <td>${row.company_name}</td>
                        <td>${row.contact_person}</td>
                        <td>${row.mobile}</td>
                        <td>${row.email}</td>
                        <td>${row.gst_number || 'N/A'}</td>
                        <td>
                            <button class="btn" style="padding: 4px 8px; font-size: 11px;" onclick="deleteRecord('customers', ${row.id})">🗑️ Delete</button>
                        </td>
                    `;
                } else if (tab === 'billing') {
                    tr.innerHTML = `
                        <td><strong>${row.invoice_number}</strong></td>
                        <td>Trip #${row.trip_id}</td>
                        <td>Cust #${row.customer_id}</td>
                        <td>₹${row.freight_amount}</td>
                        <td>₹${row.fuel_surcharge}</td>
                        <td>₹${row.gst_amount}</td>
                        <td><strong>₹${row.total_amount}</strong></td>
                        <td><span class="status-badge ${row.payment_status.toLowerCase()}">${row.payment_status}</span></td>
                        <td>
                            <button class="btn" style="padding: 4px 8px; font-size: 11px;" onclick="openInvoicePdf(${row.id})">📄 Invoice PDF</button>
                            ${row.payment_status !== 'Paid' ? `<button class="btn" style="padding: 4px 8px; font-size: 11px; border-color: var(--accent-green); color: var(--accent-green);" onclick="settleInvoice(${row.id})">💳 Settle</button>` : ''}
                        </td>
                    `;
                }
                tbody.appendChild(tr);
            });
        }

        // Render Offline Sandbox lists
        function renderSandboxData(tab) {
            if (tab === 'dashboard') {
                const activeCount = mockData.vehicles.filter(v => v.status === 'ACTIVE').length;
                const activeTripsCount = mockData.trips.filter(t => t.status !== 'Delivered').length;
                const deliveriesTodayCount = mockData.deliveries.filter(d => d.delivery_status === 'Delivered').length;
                const totalFuelCost = mockData.fuel.reduce((a, b) => a + parseFloat(b.total_cost), 0);
                
                renderDashboardStats({
                    cards: {
                        active_vehicles: activeCount,
                        active_trips: activeTripsCount,
                        deliveries_today: deliveriesTodayCount,
                        fuel_expenses: totalFuelCost
                    },
                    analytics: {
                        fleet_utilization: Math.round((activeCount / mockData.vehicles.length) * 100),
                        delivery_success_rate: Math.round((deliveriesTodayCount / mockData.deliveries.length) * 100),
                        revenue_trends: []
                    }
                });
            } else if (tab === 'reports') {
                const totalRev = mockData.billing.filter(b => b.payment_status === 'Paid').reduce((sum, b) => sum + b.total_amount, 0) || 119870.00;
                const fuelCost = mockData.fuel.reduce((sum, f) => sum + f.total_cost, 0) || 8054.10;
                const maintenanceCost = mockData.maintenance.reduce((sum, m) => sum + m.cost, 0) || 8500.00;
                const salaryCost = mockData.salaries.filter(s => s.payment_status === 'Paid').reduce((sum, s) => sum + s.total_salary, 0) || 25500.00;
                
                renderReportsStats({
                    revenue: { billing_collections: totalRev },
                    expenses: {
                        fuel: fuelCost,
                        maintenance: maintenanceCost,
                        driver_salaries: salaryCost,
                        total_expenses: fuelCost + maintenanceCost + salaryCost
                    },
                    net_profit: totalRev - (fuelCost + maintenanceCost + salaryCost)
                });
            } else {
                renderLiveList(tab, mockData[tab]);
            }
        }

        // Delete records
        function deleteRecord(tab, id) {
            if (isSandbox) {
                mockData[tab] = mockData[tab].filter(item => item.id !== id);
                showToast('Record deleted (Sandbox mode)', 'success');
                renderSandboxData(tab);
                return;
            }

            fetch(`/wp-json/transport-management/v1/${tab === 'salaries' ? 'driver-salary' : tab}/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${authToken}` }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Record deleted successfully.', 'success');
                    loadDataForTab(tab);
                }
            });
        }

        // Modals triggers
        function openCreateModal(tab) {
            const modal = document.getElementById(`modal-${tab}`);
            if (modal) {
                modal.style.display = 'flex';
                // Populate selectors if required
                if (tab === 'trips') {
                    populateTripSelectors();
                } else if (tab === 'fuel') {
                    populateSelector('createFuelVeh', mockData.vehicles, 'vehicle_number', 'id');
                } else if (tab === 'billing') {
                    populateSelector('createBillTrip', mockData.trips.filter(t => t.status === 'Delivered'), 'trip_number', 'id');
                    populateSelector('createBillClient', mockData.customers, 'company_name', 'id');
                }
            }
        }

        function closeModal(tab) {
            const modal = document.getElementById(`modal-${tab}`);
            if (modal) modal.style.display = 'none';
        }

        function populateSelector(elementId, dataset, labelKey, valueKey) {
            const select = document.getElementById(elementId);
            if (!select) return;
            select.innerHTML = '';
            dataset.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item[valueKey];
                opt.innerText = item[labelKey];
                select.appendChild(opt);
            });
        }

        function populateTripSelectors() {
            populateSelector('createTripVeh', mockData.vehicles, 'vehicle_number', 'id');
            populateSelector('createTripDriver', mockData.drivers, 'name', 'id');
            populateSelector('createTripRoute', mockData.routes, 'route_code', 'id');
        }

        // Create form submissions
        function submitCreate(tab) {
            let data = {};
            if (tab === 'vehicles') {
                data = {
                    vehicle_number: document.getElementById('createVehicleNum').value,
                    vehicle_type: document.getElementById('createVehicleType').value,
                    vehicle_model: document.getElementById('createVehicleModel').value
                };
            } else if (tab === 'drivers') {
                data = {
                    name: document.getElementById('createDriverName').value,
                    mobile: document.getElementById('createDriverMobile').value,
                    license_number: document.getElementById('createDriverLic').value,
                    salary_type: document.getElementById('createDriverSalaryType').value,
                    fixed_salary: parseFloat(document.getElementById('createDriverSalaryVal').value || 0),
                    per_trip_salary: parseFloat(document.getElementById('createDriverSalaryVal').value || 0)
                };
            } else if (tab === 'routes') {
                data = {
                    source: document.getElementById('createRouteSrc').value,
                    destination: document.getElementById('createRouteDest').value,
                    distance_km: parseInt(document.getElementById('createRouteDistance').value || 0),
                    toll_charges: parseFloat(document.getElementById('createRouteToll').value || 0),
                    estimated_time: '4 Hours'
                };
            } else if (tab === 'trips') {
                data = {
                    vehicle_id: parseInt(document.getElementById('createTripVeh').value),
                    driver_id: parseInt(document.getElementById('createTripDriver').value),
                    route_id: parseInt(document.getElementById('createTripRoute').value),
                    freight_amount: parseFloat(document.getElementById('createTripFreight').value || 0),
                    customer_name: document.getElementById('createTripCustomer').value,
                    loading_point: 'Loading point',
                    unloading_point: document.getElementById('createTripUnloading').value,
                    trip_start_date: new Date().toISOString().split('T')[0]
                };
            } else if (tab === 'fuel') {
                data = {
                    vehicle_id: parseInt(document.getElementById('createFuelVeh').value),
                    fuel_station: document.getElementById('createFuelStation').value,
                    fuel_quantity: parseFloat(document.getElementById('createFuelQty').value || 0),
                    rate_per_liter: parseFloat(document.getElementById('createFuelRate').value || 0),
                    fuel_date: new Date().toISOString().split('T')[0]
                };
            } else if (tab === 'billing') {
                data = {
                    trip_id: parseInt(document.getElementById('createBillTrip').value),
                    customer_id: parseInt(document.getElementById('createBillClient').value),
                    freight_amount: parseFloat(document.getElementById('createBillFreight').value || 0),
                    fuel_surcharge: parseFloat(document.getElementById('createBillSurcharge').value || 0),
                    invoice_date: new Date().toISOString().split('T')[0]
                };
            }

            if (isSandbox) {
                data.id = mockData[tab].length + 1;
                // Add specific logic for codes/derived keys in sandbox
                if (tab === 'vehicles') {
                    data.status = 'ACTIVE';
                } else if (tab === 'drivers') {
                    data.driver_code = 'DRV-' + Math.floor(1000 + Math.random() * 9000);
                    data.status = 'ACTIVE';
                } else if (tab === 'routes') {
                    data.route_code = 'RTE-' + data.source.substring(0,3).toUpperCase() + '-' + data.destination.substring(0,3).toUpperCase();
                    data.status = 'ACTIVE';
                } else if (tab === 'trips') {
                    data.trip_number = 'TRIP-2026-' + Math.floor(1000 + Math.random() * 9000);
                    data.status = 'Assigned';
                    
                    // Auto-spawn delivery in sandbox
                    mockData.deliveries.push({
                        id: mockData.deliveries.length + 1,
                        trip_id: data.id,
                        tracking_number: 'TRK-' + Math.floor(1000000 + Math.random() * 9000000),
                        customer_name: data.customer_name,
                        delivery_address: data.unloading_point,
                        delivery_status: 'Picked Up',
                        latitude: '19.0760',
                        longitude: '72.8777',
                        proof_of_delivery: ''
                    });
                } else if (tab === 'fuel') {
                    data.total_cost = data.fuel_quantity * data.rate_per_liter;
                    data.odometer_reading = 15000;
                } else if (tab === 'billing') {
                    const basic = data.freight_amount;
                    const premium = data.fuel_surcharge;
                    data.invoice_number = 'INV-2026-' + Math.floor(1000 + Math.random() * 9000);
                    data.gst_amount = (basic + premium) * 0.18;
                    data.total_amount = basic + premium + data.gst_amount;
                    data.payment_status = 'Unpaid';
                }

                mockData[tab].push(data);
                showToast('Record created (Sandbox Simulation Mode)', 'success');
                closeModal(tab);
                renderSandboxData(tab);
                return;
            }

            fetch(`/wp-json/transport-management/v1/${tab === 'salaries' ? 'driver-salary' : tab}`, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}`
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(resObj => {
                if (resObj.success) {
                    showToast('Record registered successfully.', 'success');
                    closeModal(tab);
                    loadDataForTab(tab);
                } else {
                    showToast(resObj.message || 'Error occurred.', 'error');
                }
            });
        }

        // Settle actions
        function completeTrip(id) {
            if (isSandbox) {
                const trip = mockData.trips.find(t => t.id === id);
                if (trip) {
                    trip.status = 'Delivered';
                    trip.trip_end_date = new Date().toISOString().split('T')[0];
                    // Update matching delivery status
                    const del = mockData.deliveries.find(d => d.trip_id === id);
                    if (del) del.delivery_status = 'Delivered';

                    showToast('Trip delivered in simulation sandbox.', 'success');
                    renderSandboxData('trips');
                }
                return;
            }

            fetch(`/wp-json/transport-management/v1/trips/${id}`, {
                method: 'PUT',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}` 
                },
                body: JSON.stringify({ status: 'Delivered', trip_end_date: new Date().toISOString().split('T')[0] })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Trip marked as delivered.', 'success');
                    loadDataForTab('trips');
                }
            });
        }

        function settleInvoice(id) {
            if (isSandbox) {
                const bill = mockData.billing.find(b => b.id === id);
                if (bill) {
                    bill.payment_status = 'Paid';
                    showToast('Invoice settled.', 'success');
                    renderSandboxData('billing');
                }
                return;
            }

            fetch(`/wp-json/transport-management/v1/billing/${id}`, {
                method: 'PUT',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}` 
                },
                body: JSON.stringify({ payment_status: 'Paid' })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Invoice marked as paid.', 'success');
                    loadDataForTab('billing');
                }
            });
        }

        function paySalary(id) {
            if (isSandbox) {
                const sal = mockData.salaries.find(s => s.id === id);
                if (sal) {
                    sal.payment_status = 'Paid';
                    showToast('Salary paid.', 'success');
                    renderSandboxData('salaries');
                }
                return;
            }

            fetch(`/wp-json/transport-management/v1/driver-salary/${id}`, {
                method: 'PUT',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}` 
                },
                body: JSON.stringify({ payment_status: 'Paid' })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Salary payment marked as paid.', 'success');
                    loadDataForTab('salaries');
                }
            });
        }

        function payChallan(id) {
            if (isSandbox) {
                const chal = mockData.challans.find(c => c.id === id);
                if (chal) {
                    chal.payment_status = 'Paid';
                    showToast('Challan paid.', 'success');
                    renderSandboxData('challans');
                }
                return;
            }

            fetch(`/wp-json/transport-management/v1/challans/${id}`, {
                method: 'PUT',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}` 
                },
                body: JSON.stringify({ payment_status: 'Paid' })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Challan paid successfully.', 'success');
                    loadDataForTab('challans');
                }
            });
        }

        // GPS Tracker functions
        let gpsInterval = null;
        function loadGpsTracker(trackingCode, client) {
            document.getElementById('mapTrackingCode').innerText = trackingCode;
            document.getElementById('mapClientName').innerText = client;
            resetGpsSimulation();
            showToast(`Loaded GPS details for tracking: ${trackingCode}`, 'success');
        }

        function startGpsSimulation() {
            const truck = document.getElementById('simulatedTruck');
            const line = document.getElementById('gpsActiveLine');
            const coordsLabel = document.getElementById('mapCoords');
            const speedLabel = document.getElementById('mapSpeed');

            const startBtn = document.getElementById('startSimBtn');
            startBtn.disabled = true;

            let percentage = 0;
            const startLat = 19.0760;
            const startLng = 72.8777;
            const destLat = 18.5204;
            const destLng = 73.8567; // Pune coords

            if (gpsInterval) clearInterval(gpsInterval);

            gpsInterval = setInterval(() => {
                percentage += 5;
                if (percentage > 100) {
                    clearInterval(gpsInterval);
                    showToast('Truck arrived at unloading terminal.', 'success');
                    startBtn.disabled = false;
                    return;
                }

                // Move dot
                const currentLeft = 15 + (percentage * 0.65); // 15% to 80%
                const currentTop = 50 - (percentage * 0.18); // 50% to 32%
                
                truck.style.left = `${currentLeft}%`;
                truck.style.top = `${currentTop}%`;
                line.style.width = `${percentage * 0.65}%`;

                // Update coordinates
                const latDiff = (destLat - startLat) * (percentage / 100);
                const lngDiff = (destLng - startLng) * (percentage / 100);
                const currentLat = (startLat + latDiff).toFixed(4);
                const currentLng = (startLng + lngDiff).toFixed(4);

                coordsLabel.innerText = `${currentLat}° N, ${currentLng}° E`;
                speedLabel.innerText = `${Math.floor(55 + Math.random() * 20)} km/h`;

            }, 500);
        }

        function resetGpsSimulation() {
            if (gpsInterval) clearInterval(gpsInterval);
            const truck = document.getElementById('simulatedTruck');
            const line = document.getElementById('gpsActiveLine');
            
            truck.style.left = '15%';
            truck.style.top = '50%';
            line.style.width = '0%';
            
            document.getElementById('mapCoords').innerText = '19.0760° N, 72.8777° E';
            document.getElementById('mapSpeed').innerText = '0 km/h';
            document.getElementById('startSimBtn').disabled = false;
        }

        // Upload POD document simulator
        function uploadPodMock(deliveryId) {
            if (isSandbox) {
                const del = mockData.deliveries.find(d => d.id === deliveryId);
                if (del) {
                    del.proof_of_delivery = 'pod-uploaded.pdf';
                    del.delivery_status = 'Delivered';
                    // Update matching trip status
                    const trip = mockData.trips.find(t => t.id === del.trip_id);
                    if (trip) {
                        trip.status = 'Delivered';
                        trip.trip_end_date = new Date().toISOString().split('T')[0];
                    }
                    showToast('POD file uploaded. Delivery completed in sandbox.', 'success');
                    renderSandboxData('deliveries');
                }
                return;
            }

            // In live connection upload simulated
            fetch(`/wp-json/transport-management/v1/deliveries/${deliveryId}`, {
                method: 'PUT',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}` 
                },
                body: JSON.stringify({ 
                    delivery_status: 'Delivered', 
                    proof_of_delivery: 'https://domain.com/wp-content/uploads/pod-delivered.pdf' 
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('POD document uploaded successfully.', 'success');
                    loadDataForTab('deliveries');
                }
            });
        }

        // Invoice HTML PDF previews
        function openInvoicePdf(invoiceId) {
            const bill = mockData.billing.find(b => b.id === invoiceId);
            if (!bill) return;

            const pdfHtml = `
            <div style="font-family: monospace; padding: 25px; color: #1e293b; background: #fff; line-height: 1.5;">
                <div style="text-align: center; margin-bottom: 20px;">
                    <h2 style="margin: 0; color: #0284c7;">TAX INVOICE - TRANSPORT ERP</h2>
                    <span style="font-size: 11px; color: #64748b;">GST Reg No: 20AAACT1234F1Z5</span>
                </div>
                <hr style="border: 0; border-top: 1px dashed #cbd5e1; margin: 15px 0;"/>
                <div style="font-size: 12px; margin-bottom: 15px;">
                    <p><strong>Invoice Number:</strong> ${bill.invoice_number}</p>
                    <p><strong>Invoice Date:</strong> ${bill.invoice_date}</p>
                    <p><strong>Client:</strong> Customer #${bill.customer_id}</p>
                    <p><strong>Payment Status:</strong> ${bill.payment_status}</p>
                </div>
                <table style="width: 100%; font-size: 12px; text-align: left; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e2e8f0;"><th style="padding: 8px 0;">Item Description</th><th style="text-align: right; padding: 8px 0;">Cost (₹)</th></tr>
                    </thead>
                    <tbody>
                        <tr><td style="padding: 8px 0;">Basic Freight Charges (Trip #${bill.trip_id})</td><td style="text-align: right; padding: 8px 0;">${bill.freight_amount.toFixed(2)}</td></tr>
                        <tr><td style="padding: 8px 0;">Fuel Premium Surcharge</td><td style="text-align: right; padding: 8px 0;">${bill.fuel_surcharge.toFixed(2)}</td></tr>
                        <tr style="border-top: 1px solid #f1f5f9;"><td style="padding: 8px 0; font-weight: bold;">IGST Service Tax (18%)</td><td style="text-align: right; padding: 8px 0; font-weight: bold;">${bill.gst_amount.toFixed(2)}</td></tr>
                        <tr style="border-top: 2px solid #cbd5e1; font-size: 14px; font-weight: bold; color: #0f172a;">
                            <td style="padding: 10px 0;">Total Amount Due</td>
                            <td style="text-align: right; padding: 10px 0;">${bill.total_amount.toFixed(2)}</td>
                        </tr>
                    </tbody>
                </table>
            </div>`;

            document.getElementById('pdfContentBlock').innerHTML = pdfHtml;
            document.getElementById('modal-invoice-pdf').style.display = 'flex';
        }

        function printPdfMock() {
            showToast('Document printed successfully.', 'success');
            closeModal('invoice-pdf');
        }

        // Search functions
        function handleSearch(tab, query) {
            if (isSandbox) {
                const results = mockData[tab].filter(item => {
                    return Object.values(item).some(val => 
                        val.toString().toLowerCase().includes(query.toLowerCase())
                    );
                });
                renderLiveList(tab, results);
            }
        }

        // Theme toggle functions
        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark-mode');
            localStorage.setItem('tr_theme', isDark ? 'dark' : 'light');
            const btn = document.querySelector('.theme-toggle');
            if (btn) {
                btn.textContent = isDark ? '🌙' : '☀️';
            }
        }

        function initializeTheme() {
            const theme = localStorage.getItem('tr_theme') || 'light';
            const btn = document.querySelector('.theme-toggle');
            if (btn) {
                btn.textContent = theme === 'dark' ? '🌙' : '☀️';
            }
        }

        // Restore active session on page refresh
        function restoreSession() {
            initializeTheme();
            if (authToken && currentUser) {
                document.getElementById('authSection').style.display = 'none';
                document.getElementById('appSection').style.display = 'flex';
                
                document.getElementById('userName').innerText = currentUser.name || '';
                document.getElementById('userRole').innerText = (currentUser.role || '').replace('transport_', '').replace('_', ' ');
                document.getElementById('userAvatar').innerText = (currentUser.name || 'U').charAt(0);
                
                if (isSandbox) {
                    document.getElementById('connStatus').innerText = 'Sandbox Mode (Offline)';
                    document.getElementById('connStatus').parentElement.style.background = 'rgba(245, 158, 11, 0.08)';
                    document.getElementById('connStatus').parentElement.style.color = 'var(--accent-yellow)';
                    document.getElementById('connStatus').previousElementSibling.style.background = 'var(--accent-yellow)';
                    document.getElementById('connStatus').previousElementSibling.style.boxShadow = '0 0 6px var(--accent-yellow)';
                } else {
                    document.getElementById('connStatus').innerText = 'Live Connection';
                    document.getElementById('connStatus').parentElement.style.background = '';
                    document.getElementById('connStatus').parentElement.style.color = '';
                    document.getElementById('connStatus').previousElementSibling.style.background = '';
                    document.getElementById('connStatus').previousElementSibling.style.boxShadow = '';
                }
                
                loadAllData();
            } else {
                document.getElementById('authSection').style.display = 'flex';
                document.getElementById('appSection').style.display = 'none';
            }
        }

        restoreSession();
    </script>
</body>
</html>
