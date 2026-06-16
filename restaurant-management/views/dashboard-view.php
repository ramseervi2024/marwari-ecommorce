<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Restaurant Management ERP Portal">
    <title>Global Restaurant ERP POS Portal</title>
    <!-- Modern Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg-primary: #0f0c1b;
            --bg-surface: #17132a;
            --bg-glass: rgba(23, 19, 42, 0.75);
            --border-glass: rgba(255, 255, 255, 0.08);
            --primary: #9d4edd;
            --primary-hover: #7b2cbf;
            --accent: #00f5d4;
            --danger: #ff5d8f;
            --warning: #ffb703;
            --success: #38b000;
            --text: #f3f0fc;
            --text-muted: #a097c4;
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
            background: radial-gradient(circle at 10% 20%, rgba(90, 24, 154, 0.2) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(0, 245, 212, 0.1) 0%, transparent 40%);
        }

        .auth-card {
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            backdrop-filter: blur(20px);
            padding: 40px;
            border-radius: 24px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-header {
            text-align: center;
            margin-bottom: 30px;
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
            box-shadow: 0 0 10px rgba(157, 78, 221, 0.2);
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
            box-shadow: 0 8px 20px rgba(157, 78, 221, 0.4);
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
            background: rgba(157, 78, 221, 0.15);
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
            background: rgba(157, 78, 221, 0.1);
        }

        .sidebar-item.active {
            background: linear-gradient(135deg, rgba(157, 78, 221, 0.15) 0%, rgba(0, 245, 212, 0.05) 100%);
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
            background: rgba(255, 93, 143, 0.1);
            border: 1px solid rgba(255, 93, 143, 0.2);
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
            box-shadow: 0 0 12px rgba(255, 93, 143, 0.4);
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
            color: var(--accent);
        }

        /* Dine-In POS Billing Split layouts */
        .billing-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
            align-items: start;
        }

        .billing-menu-catalog {
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 24px;
        }

        .catalog-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .category-filter-row {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .filter-btn {
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border-glass);
            color: var(--text-muted);
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            transition: var(--transition);
        }

        .filter-btn:hover, .filter-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .dishes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 15px;
            max-height: 500px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .dish-card {
            background: rgba(255,255,255,0.02);
            border: 1px solid var(--border-glass);
            border-radius: 14px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .dish-card:hover {
            transform: translateY(-3px);
            border-color: var(--primary);
            background: rgba(157, 78, 221, 0.05);
        }

        .dish-image-placeholder {
            width: 100%;
            height: 90px;
            background: linear-gradient(135deg, rgba(157,78,221,0.2) 0%, rgba(0,245,212,0.1) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 12px;
        }

        .dish-name {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 4px;
        }

        .dish-price {
            font-size: 15px;
            font-weight: 800;
            color: var(--accent);
        }

        .billing-cart-panel {
            background: var(--bg-surface);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            max-height: 680px;
        }

        .cart-header h3 {
            font-size: 18px;
            font-weight: 700;
        }

        .cart-items-list {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            min-height: 200px;
            max-height: 300px;
            border-bottom: 1px solid var(--border-glass);
            padding-bottom: 15px;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255,255,255,0.02);
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid var(--border-glass);
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-name {
            font-size: 13px;
            font-weight: 600;
        }

        .cart-item-price {
            font-size: 11px;
            color: var(--accent);
        }

        .qty-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qty-btn {
            width: 24px;
            height: 24px;
            background: rgba(255,255,255,0.06);
            border: none;
            color: #fff;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qty-btn:hover {
            background: var(--primary);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: var(--text-muted);
        }

        .summary-total {
            border-top: 1px solid var(--border-glass);
            padding-top: 12px;
            font-size: 18px;
            font-weight: 800;
            color: #fff;
        }

        /* KDS Kitchen columns cards */
        .kds-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .kds-column {
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 20px;
            min-height: 500px;
        }

        .kds-column-header {
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border-glass);
            padding-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .kds-column-title {
            font-weight: 700;
            font-size: 15px;
        }

        .kds-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border-glass);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .kds-card-header {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            font-weight: 700;
            color: var(--primary);
        }

        .kds-item-line {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
        }

        /* Table Coordinate status grid */
        .table-coord-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 20px;
        }

        .table-coord-card {
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 16px;
            padding: 24px 16px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .table-coord-card:hover {
            transform: scale(1.03);
        }

        .table-icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 800;
            color: #fff;
        }

        .table-status-available { background: var(--success); }
        .table-status-occupied { background: var(--primary); }
        .table-status-reserved { background: var(--warning); }
        .table-status-cleaning { background: var(--text-muted); }

        /* Tables and CRUD forms lists */
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
            transform: translateX(120%);
            transition: var(--transition);
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast.success { border-left-color: var(--success); }
        .toast.error { border-left-color: var(--danger); }
        .toast.warning { border-left-color: var(--warning); }
    </style>
</head>
<body>

    <!-- TOAST CONTAINER -->
    <div id="toast-container"></div>

    <!-- PAGE LOADER -->
    <div id="page-loader">
        <div class="spinner"></div>
        <p style="font-weight: 600; color: var(--text-muted);">Initializing Restaurant ERP...</p>
    </div>

    <!-- LOGIN PANEL -->
    <div id="login-view">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Restaurant ERP</h2>
                <p>Welcome back! Please authenticate below.</p>
            </div>
            
            <form id="login-form">
                <div class="form-group">
                    <label for="login-username">Operator Username / Email</label>
                    <input type="text" id="login-username" class="form-input" required placeholder="e.g. restsuperadmin">
                </div>

                <div class="form-group" id="login-password-group">
                    <label for="login-password">Access Password</label>
                    <input type="password" id="login-password" class="form-input" placeholder="e.g. 123456">
                </div>

                <div class="form-group" id="login-otp-group" style="display: none;">
                    <label for="login-otp">Email verification OTP</label>
                    <input type="text" id="login-otp" class="form-input" placeholder="Enter 6-digit code">
                </div>

                <button type="submit" id="btn-login-submit" class="btn">Login</button>
            </form>

            <button id="btn-send-otp" class="btn" style="background: transparent; border: 1px solid var(--primary); margin-top: 15px; padding: 10px;">Request Login Code via Email</button>

            <div style="margin-top: 30px; border-top: 1px solid var(--border-glass); padding-top: 15px;">
                <p style="font-size: 11px; color: var(--text-muted); font-weight:700;">TEST ACCOUNTS PRESETS:</p>
                <div style="display: flex; flex-wrap: wrap;">
                    <span class="preset-badge" onclick="fillPreset('restsuperadmin', '123456')">Super Admin</span>
                    <span class="preset-badge" onclick="fillPreset('rest_manager', 'managerpass123')">Manager</span>
                    <span class="preset-badge" onclick="fillPreset('rest_cashier', 'cashierpass123')">Cashier</span>
                    <span class="preset-badge" onclick="fillPreset('rest_chef', 'chefpass123')">Chef</span>
                    <span class="preset-badge" onclick="fillPreset('rest_waiter', 'waiterpass123')">Waiter</span>
                    <span class="preset-badge" onclick="fillPreset('rest_delivery', 'deliverypass123')">Delivery</span>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN PORTAL CONTAINER -->
    <div id="dashboard-shell">
        <aside>
            <div class="brand-section">
                <div class="brand-logo">R</div>
                <div class="brand-name">Resto ERP</div>
            </div>
            
            <nav>
                <div class="nav-category">Main Console</div>
                <div class="sidebar-item" data-tab="pos-billing">POS Billing</div>
                <div class="sidebar-item" data-tab="kitchen-kds">Kitchen KDS</div>
                <div class="sidebar-item" data-tab="tables-map">Tables Map</div>

                <div class="nav-category">Management</div>
                <div class="sidebar-item" data-tab="menu-dishes">Dishes Catalog</div>
                <div class="sidebar-item" data-tab="stock-inventory">Ingredients Stock</div>
                <div class="sidebar-item" data-tab="deliveries-jobs">Delivery Jobs</div>
                <div class="sidebar-item" data-tab="staff-shifts">Shifts Scheduler</div>

                <div class="nav-category">Finance & Admin</div>
                <div class="sidebar-item" data-tab="expenses-pl">Expenses & reports</div>
                <div class="sidebar-item admin-only" data-tab="diagnostics-admin" style="display: none;">Diagnostics & Users</div>
            </nav>

            <div class="user-profile-section">
                <div class="avatar" id="user-avatar-initials">AD</div>
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
                    <h1 id="current-tab-title">POS Billing</h1>
                    <p>Live Restaurant POS Management System Portal</p>
                </div>
                <div class="header-right">
                    <span style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-glass); padding: 8px 16px; border-radius: 8px; font-size:13px; font-weight:600;">
                        Live Time: <span id="clock-display">00:00:00</span>
                    </span>
                </div>
            </header>

            <!-- 1. POS BILLING TAB -->
            <div id="panel-pos-billing" class="tab-panel">
                <div class="billing-container">
                    <div class="billing-menu-catalog">
                        <div class="catalog-header">
                            <h4 style="font-weight:700;">F&B Dishes Menu</h4>
                            <input type="text" id="billing-search-dish" class="form-input" style="width:220px; padding:8px 12px; font-size:13px;" placeholder="Search by name/code...">
                        </div>
                        <div class="category-filter-row" id="billing-category-filters">
                            <!-- Injected -->
                        </div>
                        <div class="dishes-grid" id="billing-dishes-grid">
                            <!-- Injected -->
                        </div>
                    </div>

                    <div class="billing-cart-panel">
                        <div class="cart-header">
                            <h3>Dine-in Order Basket</h3>
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <label>Dine-in Table</label>
                            <select id="cart-table-select" class="form-input" style="background:#17132a;">
                                <option value="">Select Table...</option>
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <label>Customer Name</label>
                            <input type="text" id="cart-customer-name" class="form-input" placeholder="e.g. Walk-in Guest">
                        </div>

                        <div class="cart-items-list" id="cart-items-list">
                            <div style="text-align:center; padding:40px 0; color:var(--text-muted); font-size:13px;">Basket is currently empty.</div>
                        </div>

                        <div style="display:flex; flex-direction:column; gap:8px;">
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span id="cart-subtotal">₹0.00</span>
                            </div>
                            <div class="summary-row">
                                <span>Tax (GST 5%)</span>
                                <span id="cart-tax">₹0.00</span>
                            </div>
                            <div class="summary-row">
                                <span>Discount</span>
                                <input type="number" id="cart-discount-input" class="form-input" style="width:80px; padding:4px 8px; text-align:right; font-size:12px; margin-bottom:0;" value="0" min="0">
                            </div>
                            <div class="summary-row summary-total">
                                <span>Total Payable</span>
                                <span id="cart-total">₹0.00</span>
                            </div>
                        </div>

                        <button id="btn-place-order" class="btn" style="background:var(--primary);">Book Table Order</button>
                    </div>
                </div>
            </div>

            <!-- 2. KITCHEN KDS TAB -->
            <div id="panel-kitchen-kds" class="tab-panel">
                <div class="kds-row">
                    <div class="kds-column">
                        <div class="kds-column-header">
                            <span class="kds-column-title" style="color:var(--danger);">Pending Queue</span>
                            <span id="kds-pending-count" style="font-weight:800; font-size:13px;">0</span>
                        </div>
                        <div id="kds-pending-list"></div>
                    </div>
                    <div class="kds-column">
                        <div class="kds-column-header">
                            <span class="kds-column-title" style="color:var(--warning);">Preparing in Kitchen</span>
                            <span id="kds-preparing-count" style="font-weight:800; font-size:13px;">0</span>
                        </div>
                        <div id="kds-preparing-list"></div>
                    </div>
                    <div class="kds-column">
                        <div class="kds-column-header">
                            <span class="kds-column-title" style="color:var(--accent);">Ready for Service</span>
                            <span id="kds-ready-count" style="font-weight:800; font-size:13px;">0</span>
                        </div>
                        <div id="kds-ready-list"></div>
                    </div>
                    <div class="kds-column">
                        <div class="kds-column-header">
                            <span class="kds-column-title" style="color:var(--success);">Served & Paid</span>
                            <span id="kds-served-count" style="font-weight:800; font-size:13px;">0</span>
                        </div>
                        <div id="kds-served-list"></div>
                    </div>
                </div>
            </div>

            <!-- 3. TABLES MAP TAB -->
            <div id="panel-tables-map" class="tab-panel">
                <div style="display:flex; justify-content:space-between; margin-bottom:20px; align-items:center;">
                    <h4 style="font-weight:700;">Dine-in Floors Layout Map</h4>
                    <button class="btn" style="width:auto; padding:10px 20px; font-size:13px;" onclick="openTableModal()">Register Dine-In Table</button>
                </div>
                <div class="table-coord-grid" id="tables-coord-grid">
                    <!-- Tables map cards -->
                </div>
            </div>

            <!-- 4. DISHES CATALOG TAB -->
            <div id="panel-menu-dishes" class="tab-panel">
                <div style="display:flex; justify-content:space-between; margin-bottom:20px; align-items:center;">
                    <h4 style="font-weight:700;">Restaurant Menu Registry</h4>
                    <div style="display:flex; gap:10px;">
                        <button class="btn" style="width:auto; padding:10px 20px; font-size:13px; background:rgba(255,255,255,0.06); border:1px solid var(--border-glass);" onclick="openCategoryModal()">Manage Categories</button>
                        <button class="btn" style="width:auto; padding:10px 20px; font-size:13px;" onclick="openDishModal()">Add New Dish Item</button>
                    </div>
                </div>
                <div class="data-table-card">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Dish Name</th>
                                <th>Category</th>
                                <th>Preparation Time</th>
                                <th>Tax (GST)</th>
                                <th>Selling Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="dishes-list-body">
                            <!-- Injected -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 5. INGREDIENTS STOCK TAB -->
            <div id="panel-stock-inventory" class="tab-panel">
                <div style="display:flex; justify-content:space-between; margin-bottom:20px; align-items:center;">
                    <h4 style="font-weight:700;">Ingredients Inventory Tracking</h4>
                    <div style="display:flex; gap:10px;">
                        <button class="btn" style="width:auto; padding:10px 20px; font-size:13px; background:rgba(255,255,255,0.06); border:1px solid var(--border-glass);" onclick="openSupplierModal()">Suppliers Directory</button>
                        <button class="btn" style="width:auto; padding:10px 20px; font-size:13px; background:rgba(255,255,255,0.06); border:1px solid var(--border-glass);" onclick="openPurchaseModal()">Log Restocking Order</button>
                        <button class="btn" style="width:auto; padding:10px 20px; font-size:13px;" onclick="openIngredientModal()">Add Raw Ingredient</button>
                    </div>
                </div>
                <div class="data-table-card">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Ingredient Name</th>
                                <th>Current Stock</th>
                                <th>Minimum Stock Level</th>
                                <th>Unit</th>
                                <th>Purchase Cost</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="ingredients-list-body">
                            <!-- Injected -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 6. DELIVERY JOBS TAB -->
            <div id="panel-deliveries-jobs" class="tab-panel">
                <div style="display:flex; justify-content:space-between; margin-bottom:20px; align-items:center;">
                    <h4 style="font-weight:700;">Home & Online Delivery orders</h4>
                    <button class="btn" style="width:auto; padding:10px 20px; font-size:13px;" onclick="openDeliveryModal()">Assign Delivery Partner</button>
                </div>
                <div class="data-table-card">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order Number</th>
                                <th>Customer Address</th>
                                <th>Delivery Partner</th>
                                <th>Delivery Fee</th>
                                <th>Delivery Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="deliveries-list-body">
                            <!-- Injected -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 7. SHIFTS SCHEDULER TAB -->
            <div id="panel-staff-shifts" class="tab-panel">
                <div style="display:flex; justify-content:space-between; margin-bottom:20px; align-items:center;">
                    <h4 style="font-weight:700;">Employee shifts logs & attendance</h4>
                    <button class="btn" style="width:auto; padding:10px 20px; font-size:13px;" onclick="openStaffModal()">Schedule Employee Shift</button>
                </div>
                <div class="data-table-card">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Position/Role</th>
                                <th>Shift Timing</th>
                                <th>Salary Rate</th>
                                <th>Attendance status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="staff-list-body">
                            <!-- Injected -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 8. EXPENSES & REPORTS TAB -->
            <div id="panel-expenses-pl" class="tab-panel">
                <div class="kpi-grid">
                    <div class="kpi-card">
                        <span class="kpi-title">Monthly Operating Overhead</span>
                        <span class="kpi-value" id="rep-overhead-val">₹0.00</span>
                    </div>
                    <div class="kpi-card">
                        <span class="kpi-title">Monthly Ingredient Restock Cost</span>
                        <span class="kpi-value" id="rep-restock-val">₹0.00</span>
                    </div>
                    <div class="kpi-card">
                        <span class="kpi-title">Net Sales Income</span>
                        <span class="kpi-value" id="rep-revenue-val" style="color:var(--accent);">₹0.00</span>
                    </div>
                    <div class="kpi-card">
                        <span class="kpi-title">Net Profit/Loss Margin</span>
                        <span class="kpi-value" id="rep-profit-val" style="color:var(--success);">₹0.00</span>
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; margin-bottom:20px; align-items:center;">
                    <h4 style="font-weight:700;">Operating Expenses & Petty cash logs</h4>
                    <button class="btn" style="width:auto; padding:10px 20px; font-size:13px;" onclick="openExpenseModal()">Log petty expense</button>
                </div>
                <div class="data-table-card">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Voucher ID</th>
                                <th>Expense Category</th>
                                <th>Amount Paid</th>
                                <th>Voucher Description</th>
                                <th>Timestamp</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="expenses-list-body">
                            <!-- Injected -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 9. DIAGNOSTICS & ADMIN TAB -->
            <div id="panel-diagnostics-admin" class="tab-panel">
                <div class="data-table-card">
                    <h4 style="font-weight:700; margin-bottom:20px;">Operator approvals</h4>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Change Status</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody id="users-list-body">
                            <!-- Injected -->
                        </tbody>
                    </table>
                </div>

                <div class="data-table-card" style="margin-top:30px;">
                    <h4 style="font-weight:700; margin-bottom:20px;">SMTP Mail settings diagnostic panel</h4>
                    <form id="smtp-config-form">
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                            <div class="form-group">
                                <label>SMTP Host</label>
                                <input type="text" id="smtp-host" class="form-input" placeholder="smtp.mailtrap.io">
                            </div>
                            <div class="form-group">
                                <label>SMTP Port</label>
                                <input type="text" id="smtp-port" class="form-input" placeholder="587">
                            </div>
                            <div class="form-group">
                                <label>SMTP Username</label>
                                <input type="text" id="smtp-username" class="form-input" placeholder="SMTP User">
                            </div>
                            <div class="form-group">
                                <label>SMTP Password</label>
                                <input type="password" id="smtp-password" class="form-input" placeholder="SMTP Password">
                            </div>
                            <div class="form-group">
                                <label>From Email</label>
                                <input type="email" id="smtp-from-email" class="form-input" placeholder="noreply@restaurant.com">
                            </div>
                            <div class="form-group">
                                <label>From Name</label>
                                <input type="text" id="smtp-from-name" class="form-input" placeholder="Global Restaurant POS">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Mail Encryption</label>
                            <select id="smtp-encryption" class="form-input" style="background:#17132a;">
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                                <option value="none">None</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Enable SMTP Service</label>
                            <select id="smtp-enabled" class="form-input" style="background:#17132a;">
                                <option value="no">Disabled (Local Server default)</option>
                                <option value="yes">Enabled</option>
                            </select>
                        </div>

                        <button type="submit" class="btn" style="width:auto; padding:10px 30px;">Save settings</button>
                    </form>

                    <div style="margin-top:30px; border-top:1px solid var(--border-glass); padding-top:20px;">
                        <h5 style="font-weight:700; margin-bottom:10px;">Diagnostic connection tester</h5>
                        <div style="display:flex; gap:10px; align-items:flex-end;">
                            <div class="form-group" style="flex:1; margin-bottom:0;">
                                <label>Recipient Email</label>
                                <input type="email" id="smtp-test-email" class="form-input" placeholder="test@domain.com">
                            </div>
                            <button type="button" class="btn" style="width:auto; padding:12px 30px;" onclick="sendTestEmail()">Send test email</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL OVERLAYS (CRUD POPUPS) -->
    <!-- 1. DINE-IN TABLE MODAL -->
    <div class="modal-overlay" id="modal-table">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-table')">✖</button>
            <h4 style="margin-bottom:20px; font-weight:700;" id="table-modal-title">Add Table Coordinates</h4>
            <form id="table-form">
                <input type="hidden" id="table-id">
                <div class="form-group">
                    <label>Table Number / Code</label>
                    <input type="text" id="table-number" class="form-input" required placeholder="e.g. Table 6">
                </div>
                <div class="form-group">
                    <label>Capacity</label>
                    <input type="number" id="table-capacity" class="form-input" required value="4">
                </div>
                <div class="form-group">
                    <label>Floor Location</label>
                    <input type="text" id="table-floor" class="form-input" value="Ground">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select id="table-status" class="form-input" style="background:#17132a;">
                        <option value="Available">Available</option>
                        <option value="Occupied">Occupied</option>
                        <option value="Reserved">Reserved</option>
                        <option value="Cleaning">Cleaning</option>
                    </select>
                </div>
                <button type="submit" class="btn">Save table</button>
            </form>
        </div>
    </div>

    <!-- 2. CATEGORY MODAL -->
    <div class="modal-overlay" id="modal-category">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-category')">✖</button>
            <h4 style="margin-bottom:20px; font-weight:700;">Manage Categories</h4>
            <form id="category-form" style="margin-bottom:20px;">
                <div class="form-group">
                    <label>New Category Name</label>
                    <input type="text" id="category-name" class="form-input" required placeholder="e.g. Mains">
                </div>
                <button type="submit" class="btn">Add category</button>
            </form>
            <div style="max-height: 200px; overflow-y:auto;">
                <table class="data-table" style="font-size:12px;">
                    <thead>
                        <tr>
                            <th>Category ID</th>
                            <th>Name</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody id="modal-categories-list"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 3. DISH ITEM MODAL -->
    <div class="modal-overlay" id="modal-dish">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-dish')">✖</button>
            <h4 style="margin-bottom:20px; font-weight:700;" id="dish-modal-title">Register Menu Item</h4>
            <form id="dish-form">
                <input type="hidden" id="dish-id">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label>Item Code</label>
                        <input type="text" id="dish-code" class="form-input" required placeholder="e.g. M005">
                    </div>
                    <div class="form-group">
                        <label>Dish Name</label>
                        <input type="text" id="dish-name" class="form-input" required placeholder="e.g. Paneer Kadai">
                    </div>
                </div>
                <div class="form-group">
                    <label>Menu Category</label>
                    <select id="dish-category" class="form-input" style="background:#17132a;" required>
                        <!-- Categories list -->
                    </select>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label>Price (₹)</label>
                        <input type="number" id="dish-price" class="form-input" required placeholder="Selling price">
                    </div>
                    <div class="form-group">
                        <label>Preparation Time (min)</label>
                        <input type="number" id="dish-prep-time" class="form-input" required value="15">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="dish-description" class="form-input" style="resize:none; height:60px;" placeholder="Brief details..."></textarea>
                </div>
                <div class="form-group">
                    <label>Upload Media File</label>
                    <input type="file" id="dish-file" class="form-input" style="padding: 8px;">
                </div>
                <button type="submit" class="btn">Save dish item</button>
            </form>
        </div>
    </div>

    <!-- 4. DYNAMIC RECIPE FORM MODAL -->
    <div class="modal-overlay" id="modal-recipe">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-recipe')">✖</button>
            <h4 style="margin-bottom:20px; font-weight:700;">Formulate Recipe</h4>
            <p id="recipe-dish-title" style="font-size:13px; color:var(--text-muted); margin-bottom:15px;"></p>
            <form id="recipe-form" style="margin-bottom:20px;">
                <input type="hidden" id="recipe-menu-item-id">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label>Select Ingredient</label>
                        <select id="recipe-ingredient-select" class="form-input" style="background:#17132a;">
                            <!-- Ingredients list -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantity Required</label>
                        <input type="number" step="0.0001" id="recipe-quantity" class="form-input" placeholder="e.g. 0.2000 (200g)">
                    </div>
                </div>
                <button type="submit" class="btn">Add ingredient to recipe</button>
            </form>
            <div style="max-height: 200px; overflow-y:auto;">
                <table class="data-table" style="font-size:12px;">
                    <thead>
                        <tr>
                            <th>Ingredient Name</th>
                            <th>Qty Required</th>
                            <th>Unit</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody id="modal-recipe-ingredients-list"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 5. INGREDIENT MODAL -->
    <div class="modal-overlay" id="modal-ingredient">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-ingredient')">✖</button>
            <h4 style="margin-bottom:20px; font-weight:700;" id="ingredient-modal-title">Register Raw Ingredient</h4>
            <form id="ingredient-form">
                <input type="hidden" id="ingredient-id">
                <div class="form-group">
                    <label>Ingredient Name</label>
                    <input type="text" id="ingredient-name" class="form-input" required placeholder="e.g. Paneer">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label>Measuring Unit</label>
                        <input type="text" id="ingredient-unit" class="form-input" required placeholder="e.g. kg">
                    </div>
                    <div class="form-group">
                        <label>Current Stock</label>
                        <input type="number" step="0.01" id="ingredient-stock" class="form-input" required value="0.00">
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label>Minimum Stock Level</label>
                        <input type="number" step="0.01" id="ingredient-min-stock" class="form-input" required value="1.00">
                    </div>
                    <div class="form-group">
                        <label>Purchase Price per Unit (₹)</label>
                        <input type="number" step="0.01" id="ingredient-price" class="form-input" required value="0.00">
                    </div>
                </div>
                <div class="form-group">
                    <label>Preferred Supplier</label>
                    <select id="ingredient-supplier" class="form-input" style="background:#17132a;">
                        <!-- Suppliers list -->
                    </select>
                </div>
                <button type="submit" class="btn">Save ingredient</button>
            </form>
        </div>
    </div>

    <!-- 6. SUPPLIER MODAL -->
    <div class="modal-overlay" id="modal-supplier">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-supplier')">✖</button>
            <h4 style="margin-bottom:20px; font-weight:700;">B2B Raw Suppliers Directory</h4>
            <form id="supplier-form" style="margin-bottom:20px;">
                <input type="hidden" id="supplier-id">
                <div class="form-group">
                    <label>Supplier Name</label>
                    <input type="text" id="supplier-name" class="form-input" required placeholder="e.g. Fresh F&B Grocers">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label>Mobile Contact</label>
                        <input type="text" id="supplier-mobile" class="form-input" required placeholder="e.g. 9876543210">
                    </div>
                    <div class="form-group">
                        <label>GST Number</label>
                        <input type="text" id="supplier-gst" class="form-input" placeholder="e.g. 27ABCDE1234F1Z5">
                    </div>
                </div>
                <div class="form-group">
                    <label>Supplier Address</label>
                    <input type="text" id="supplier-address" class="form-input" placeholder="Market Yard, Pune">
                </div>
                <button type="submit" class="btn">Register supplier</button>
            </form>
            <div style="max-height: 200px; overflow-y:auto;">
                <table class="data-table" style="font-size:12px;">
                    <thead>
                        <tr>
                            <th>Supplier Name</th>
                            <th>Contact</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody id="modal-suppliers-list"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 7. RESTOCKING PURCHASE ORDER MODAL -->
    <div class="modal-overlay" id="modal-purchase">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-purchase')">✖</button>
            <h4 style="margin-bottom:20px; font-weight:700;">Purchase Restocking Transaction</h4>
            <form id="purchase-form">
                <div class="form-group">
                    <label>Select Supplier</label>
                    <select id="purchase-supplier-select" class="form-input" style="background:#17132a;" required>
                        <!-- Suppliers list -->
                    </select>
                </div>
                <div class="form-group">
                    <label>Select Ingredient</label>
                    <select id="purchase-ingredient-select" class="form-input" style="background:#17132a;" required>
                        <!-- Ingredients list -->
                    </select>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label>Replenish Quantity</label>
                        <input type="number" step="0.01" id="purchase-quantity" class="form-input" required placeholder="Qty to increase">
                    </div>
                    <div class="form-group">
                        <label>Purchase price/Unit (₹)</label>
                        <input type="number" step="0.01" id="purchase-price" class="form-input" required placeholder="Voucher item price">
                    </div>
                </div>
                <button type="submit" class="btn">Log & replenish stock</button>
            </form>
        </div>
    </div>

    <!-- 8. ASSIGN DELIVERY MODAL -->
    <div class="modal-overlay" id="modal-delivery">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-delivery')">✖</button>
            <h4 style="margin-bottom:20px; font-weight:700;">Assign Delivery Executive</h4>
            <form id="delivery-form">
                <div class="form-group">
                    <label>Select Completed order</label>
                    <select id="delivery-order-select" class="form-input" style="background:#17132a;" required>
                        <!-- Orders list -->
                    </select>
                </div>
                <div class="form-group">
                    <label>Delivery Executive Partner</label>
                    <input type="text" id="delivery-partner" class="form-input" required value="Zomato / Swiggy Executive">
                </div>
                <div class="form-group">
                    <label>Customer Shipping Address</label>
                    <textarea id="delivery-address" class="form-input" style="resize:none; height:60px;" required placeholder="Street address details..."></textarea>
                </div>
                <button type="submit" class="btn">Assign delivery job</button>
            </form>
        </div>
    </div>

    <!-- 9. STAFF SHIFT MODAL -->
    <div class="modal-overlay" id="modal-staff">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-staff')">✖</button>
            <h4 style="margin-bottom:20px; font-weight:700;" id="staff-modal-title">Schedule Staff Shift</h4>
            <form id="staff-form">
                <input type="hidden" id="staff-id">
                <div class="form-group">
                    <label>Employee Name</label>
                    <input type="text" id="staff-name" class="form-input" required placeholder="e.g. Chef Sanjay">
                </div>
                <div class="form-group">
                    <label>Position / Role</label>
                    <input type="text" id="staff-role" class="form-input" required placeholder="e.g. Head Chef">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label>Shift Start Time</label>
                        <input type="text" id="staff-start" class="form-input" required value="09:00">
                    </div>
                    <div class="form-group">
                        <label>Shift End Time</label>
                        <input type="text" id="staff-end" class="form-input" required value="17:00">
                    </div>
                </div>
                <div class="form-group">
                    <label>Salary Rate per Shift (₹)</label>
                    <input type="number" id="staff-salary" class="form-input" required value="800">
                </div>
                <button type="submit" class="btn">Schedule shift</button>
            </form>
        </div>
    </div>

    <!-- 10. EXPENSE MODAL -->
    <div class="modal-overlay" id="modal-expense">
        <div class="modal-card">
            <button class="modal-close" onclick="closeModal('modal-expense')">✖</button>
            <h4 style="margin-bottom:20px; font-weight:700;">Log petty overhead cash</h4>
            <form id="expense-form">
                <div class="form-group">
                    <label>Expense category</label>
                    <select id="expense-type" class="form-input" style="background:#17132a;" required>
                        <option value="Rent">Rent</option>
                        <option value="Electricity">Electricity</option>
                        <option value="Gas cylinder">Gas cylinder</option>
                        <option value="Salary payout">Salary payout</option>
                        <option value="Internet connection">Internet connection</option>
                        <option value="Maintenance and repair">Maintenance and repair</option>
                        <option value="Miscellaneous">Miscellaneous</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Overhead cost Amount (₹)</label>
                    <input type="number" id="expense-amount" class="form-input" required placeholder="₹ to payout">
                </div>
                <div class="form-group">
                    <label>Voucher Description</label>
                    <input type="text" id="expense-desc" class="form-input" placeholder="Voucher payment details...">
                </div>
                <button type="submit" class="btn">Payout expense</button>
            </form>
        </div>
    </div>

    <!-- 11. UPI QR SCANNERS POPUP -->
    <div class="modal-overlay" id="modal-upi">
        <div class="modal-card" style="text-align:center; max-width:320px;">
            <button class="modal-close" onclick="closeModal('modal-upi')">✖</button>
            <h4 style="margin-bottom:15px; font-weight:700;">Scan UPI QR Code</h4>
            <div style="background: white; padding: 15px; border-radius: 12px; display: inline-block; margin-bottom: 15px;">
                <!-- Standard QR code simulation using a dynamic image generator -->
                <img id="upi-qr-image" src="" alt="UPI QR Scanner" style="width:200px; height:200px; display:block;">
            </div>
            <p id="upi-amount-display" style="font-weight:700; font-size:18px; margin-bottom:15px; color:var(--accent);">₹0.00</p>
            <button class="btn" style="background:var(--success);" onclick="confirmUpiPayment()">Confirm payment received</button>
        </div>
    </div>

    <!-- 12. THERMAL INVOICE PRINT PREVIEW MODAL -->
    <div class="modal-overlay" id="modal-thermal-invoice">
        <div class="modal-card" style="max-width:380px; font-family:'Courier New', monospace; color:black; background:white; padding:20px; border-radius:4px; box-shadow:0 0 10px rgba(0,0,0,0.5);">
            <button class="modal-close" onclick="closeModal('modal-thermal-invoice')" style="color:black; font-weight:800;">✖</button>
            <div id="thermal-receipt-content" style="font-size:12px; line-height:1.4;">
                <!-- thermal layout receipt -->
            </div>
            <button class="btn" style="margin-top:20px; font-family:inherit; background:black; color:white; border-radius:2px;" onclick="window.print()">Print receipt template</button>
        </div>
    </div>

    <!-- APPLICATION LOGIC IN JAVASCRIPT -->
    <script>
        const API_NAMESPACE = '/wp-json/restaurant-management/v1';

        // State Store
        let token = localStorage.getItem('restaurant_token') || '';
        let user = null;
        try {
            const savedUser = localStorage.getItem('restaurant_user');
            if (savedUser) {
                user = JSON.parse(savedUser);
            }
        } catch (e) {
            console.error("Failed to parse user session", e);
        }

        let cart = [];
        let categories = [];
        let menuItems = [];
        let tables = [];
        let suppliers = [];
        let ingredients = [];

        let currentUpiOrderId = null;

        // Initialize clock display
        setInterval(() => {
            const now = new Date();
            document.getElementById('clock-display').innerText = now.toTimeString().split(' ')[0];
        }, 1000);

        window.addEventListener('DOMContentLoaded', () => {
            initApp();
        });

        function initApp() {
            if (token) {
                apiFetch('/auth/me', 'GET')
                    .then(res => {
                        if (res.success) {
                            user = res.data;
                            localStorage.setItem('restaurant_user', JSON.stringify(user));
                            showShell();
                        } else {
                            logout();
                        }
                    })
                    .catch(() => logout());
            } else {
                showLogin();
            }
        }

        function showLogin() {
            document.getElementById('page-loader').style.display = 'none';
            document.getElementById('login-view').style.display = 'flex';
            document.getElementById('dashboard-shell').style.display = 'none';
        }

        function showShell() {
            document.getElementById('page-loader').style.display = 'none';
            document.getElementById('login-view').style.display = 'none';
            document.getElementById('dashboard-shell').style.display = 'flex';

            document.getElementById('user-display-name').innerText = user.name;
            document.getElementById('user-display-role').innerText = user.role.replace('restaurant_', '').replace('_', ' ');
            document.getElementById('user-avatar-initials').innerText = user.name.substring(0, 2).toUpperCase();

            // Set Admin privileges
            if (user.role === 'restaurant_super_admin' || user.role === 'administrator') {
                const adminMenus = document.querySelectorAll('.admin-only');
                adminMenus.forEach(el => el.style.display = 'block');
            }

            setupTabs();
            // Start in last active tab or default to POS Billing
            const activeTab = localStorage.getItem('restaurant_active_tab') || 'pos-billing';
            switchTab(activeTab);
        }

        function setupTabs() {
            const items = document.querySelectorAll('.sidebar-item');
            items.forEach(item => {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    const tabName = item.getAttribute('data-tab');
                    switchTab(tabName);
                });
            });
        }

        function switchTab(tabName) {
            const items = document.querySelectorAll('.sidebar-item');
            const panels = document.querySelectorAll('.tab-panel');
            
            items.forEach(el => el.classList.remove('active'));
            panels.forEach(el => el.classList.remove('active'));

            const targetItem = document.querySelector(`.sidebar-item[data-tab="${tabName}"]`);
            if (targetItem) targetItem.classList.add('active');

            const targetPanel = document.getElementById(`panel-${tabName}`);
            if (targetPanel) targetPanel.classList.add('active');

            document.getElementById('current-tab-title').innerText = targetItem ? targetItem.innerText : 'Restaurant Management Panel';

            // Save active tab
            localStorage.setItem('restaurant_active_tab', tabName);

            // Trigger tab specific loading
            switch(tabName) {
                case 'pos-billing':
                    loadBillingDishes();
                    break;
                case 'kitchen-kds':
                    loadKitchenKdsQueue();
                    break;
                case 'tables-map':
                    loadTablesFloorMap();
                    break;
                case 'menu-dishes':
                    loadDishesRegistry();
                    break;
                case 'stock-inventory':
                    loadStockInventory();
                    break;
                case 'deliveries-jobs':
                    loadDeliveriesList();
                    break;
                case 'staff-shifts':
                    loadStaffList();
                    break;
                case 'expenses-pl':
                    loadExpensesAndOverhead();
                    break;
                case 'diagnostics-admin':
                    loadDiagnosticsAdmin();
                    break;
            }
        }

        // apiFetch Helper Wrapper
        async function apiFetch(endpoint, method = 'GET', body = null) {
            let url = API_NAMESPACE + endpoint;
            if (token) {
                const separator = url.includes('?') ? '&' : '?';
                url += separator + 'token=' + encodeURIComponent(token);
            }
            const headers = {
                'Content-Type': 'application/json'
            };
            if (token) {
                headers['Authorization'] = 'Bearer ' + token;
                headers['X-Authorization'] = 'Bearer ' + token;
            }

            const config = {
                method: method,
                headers: headers
            };

            if (body && method !== 'GET') {
                config.body = JSON.stringify(body);
            }

            try {
                const response = await fetch(url, config);
                const data = await response.json();
                return data;
            } catch (err) {
                console.error("Fetch Exception: ", err);
                return { success: false, message: 'Server communication error.' };
            }
        }

        // --- AUTH SECTION ---
        const loginForm = document.getElementById('login-form');
        const btnSendOtp = document.getElementById('btn-send-otp');
        const loginPasswordGroup = document.getElementById('login-password-group');
        const loginOtpGroup = document.getElementById('login-otp-group');
        let isOtpMode = false;

        btnSendOtp.addEventListener('click', () => {
            const username = document.getElementById('login-username').value;
            if (!username) {
                showToast("Please enter username or email first.", "error");
                return;
            }

            btnSendOtp.innerText = 'Sending...';
            btnSendOtp.disabled = true;

            apiFetch('/auth/login/initiate', 'POST', { username: username })
                .then(res => {
                    if (res.success) {
                        showToast("OTP sent to your registered email.", "success");
                        loginPasswordGroup.style.display = 'none';
                        loginOtpGroup.style.display = 'block';
                        document.getElementById('btn-login-submit').innerText = 'Verify & Login';
                        isOtpMode = true;
                    } else {
                        showToast("Failed to initiate OTP: " + res.message, "error");
                        btnSendOtp.innerText = 'Request OTP Code';
                        btnSendOtp.disabled = false;
                    }
                });
        });

        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const username = document.getElementById('login-username').value;
            const password = document.getElementById('login-password').value;
            const otp = document.getElementById('login-otp').value;

            const payload = { username: username };
            if (isOtpMode) {
                payload.otp = otp;
            } else {
                payload.password = password;
            }

            apiFetch('/auth/login', 'POST', payload)
                .then(res => {
                    if (res.success) {
                        token = res.data.token;
                        user = res.data.user;
                        localStorage.setItem('restaurant_token', token);
                        localStorage.setItem('restaurant_user', JSON.stringify(user));
                        showToast("Access Granted. Logging in...", "success");
                        showShell();
                    } else {
                        showToast(res.message, "error");
                    }
                });
        });

        document.getElementById('btn-logout').addEventListener('click', () => {
            logout();
        });

        function logout() {
            // Clear client session immediately so logout is instant and never gets stuck
            token = '';
            user = null;
            localStorage.removeItem('restaurant_token');
            localStorage.removeItem('restaurant_user');
            localStorage.removeItem('restaurant_active_tab');
            showLogin();

            // Asynchronously notify server in background
            apiFetch('/auth/logout', 'POST').catch(err => {
                console.log("Logged out locally. Server logout notification failed: ", err);
            });
        }

        function fillPreset(username, password) {
            document.getElementById('login-username').value = username;
            document.getElementById('login-password').value = password;
            loginPasswordGroup.style.display = 'block';
            loginOtpGroup.style.display = 'none';
            document.getElementById('btn-login-submit').innerText = 'Login with Password';
            isOtpMode = false;
            btnSendOtp.innerText = 'Request OTP Code';
            btnSendOtp.disabled = false;
        }

        // --- POS BILLING TERMINAL ---
        function loadBillingDishes() {
            // Fetch Tables list for selection
            apiFetch('/tables', 'GET').then(res => {
                if (res.success) {
                    tables = res.data;
                    const sel = document.getElementById('cart-table-select');
                    sel.innerHTML = '<option value="">Select Table...</option>';
                    tables.forEach(t => {
                        sel.innerHTML += `<option value="${t.id}">${t.table_number} (${t.floor} - ${t.capacity} pax)</option>`;
                    });
                }
            });

            // Fetch Categories list
            apiFetch('/categories', 'GET').then(res => {
                if (res.success) {
                    categories = res.data;
                    const row = document.getElementById('billing-category-filters');
                    row.innerHTML = '<button class="filter-btn active" onclick="filterBillingCategory(0)">All Items</button>';
                    categories.forEach(c => {
                        row.innerHTML += `<button class="filter-btn" onclick="filterBillingCategory(${c.id})">${c.name}</button>`;
                    });
                }
            });

            // Fetch menu
            apiFetch('/menu', 'GET').then(res => {
                if (res.success) {
                    menuItems = res.data;
                    renderBillingCatalog(menuItems);
                }
            });
        }

        function renderBillingCatalog(items) {
            const grid = document.getElementById('billing-dishes-grid');
            grid.innerHTML = '';
            if (items.length === 0) {
                grid.innerHTML = '<div style="grid-column: span 3; text-align:center; padding:20px; color:var(--text-muted);">No dishes in this category.</div>';
                return;
            }
            items.forEach(dish => {
                const card = document.createElement('div');
                card.className = 'dish-card';
                card.onclick = () => addToCart(dish);
                
                const imgSrc = dish.image ? dish.image : '';
                const imgTag = imgSrc ? `<img src="${imgSrc}" style="width:100%; height:90px; object-fit:cover; border-radius:10px; margin-bottom:12px;">` : `<div class="dish-image-placeholder">${dish.item_code}</div>`;
                
                card.innerHTML = `
                    ${imgTag}
                    <div class="dish-name">${dish.item_name}</div>
                    <div class="dish-price">₹${parseFloat(dish.price).toFixed(2)}</div>
                `;
                grid.appendChild(card);
            });
        }

        function filterBillingCategory(catId) {
            const btns = document.querySelectorAll('#billing-category-filters .filter-btn');
            btns.forEach(b => b.classList.remove('active'));
            event.target.classList.add('active');

            if (catId === 0) {
                renderBillingCatalog(menuItems);
            } else {
                const filtered = menuItems.filter(item => intval(item.category_id) === catId);
                renderBillingCatalog(filtered);
            }
        }

        // Cart POS
        function addToCart(dish) {
            const existing = cart.find(i => i.menu_item_id === dish.id);
            if (existing) {
                existing.quantity++;
            } else {
                cart.push({
                    menu_item_id: dish.id,
                    item_name: dish.item_name,
                    price: parseFloat(dish.price),
                    tax_percentage: parseFloat(dish.tax_percentage || 5.00),
                    quantity: 1
                });
            }
            renderCart();
        }

        function updateCartQty(idx, change) {
            cart[idx].quantity += change;
            if (cart[idx].quantity <= 0) {
                cart.splice(idx, 1);
            }
            renderCart();
        }

        function renderCart() {
            const list = document.getElementById('cart-items-list');
            list.innerHTML = '';
            if (cart.length === 0) {
                list.innerHTML = '<div style="text-align:center; padding:40px 0; color:var(--text-muted); font-size:13px;">Basket is empty.</div>';
                document.getElementById('cart-subtotal').innerText = '₹0.00';
                document.getElementById('cart-tax').innerText = '₹0.00';
                document.getElementById('cart-total').innerText = '₹0.00';
                return;
            }

            let subtotal = 0;
            let tax = 0;

            cart.forEach((item, idx) => {
                const item_sub = item.price * item.quantity;
                const item_tax = item_sub * (item.tax_percentage / 100);
                subtotal += item_sub;
                tax += item_tax;

                const div = document.createElement('div');
                div.className = 'cart-item';
                div.innerHTML = `
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.item_name}</div>
                        <div class="cart-item-price">₹${item.price.toFixed(2)}</div>
                    </div>
                    <div class="qty-controls">
                        <button class="qty-btn" onclick="updateCartQty(${idx}, -1)">-</button>
                        <span style="font-weight:700; font-size:13px;">${item.quantity}</span>
                        <button class="qty-btn" onclick="updateCartQty(${idx}, 1)">+</button>
                    </div>
                `;
                list.appendChild(div);
            });

            const discount = parseFloat(document.getElementById('cart-discount-input').value || 0.00);
            const total = (subtotal + tax) - discount;

            document.getElementById('cart-subtotal').innerText = `₹${subtotal.toFixed(2)}`;
            document.getElementById('cart-tax').innerText = `₹${tax.toFixed(2)}`;
            document.getElementById('cart-total').innerText = `₹${total.toFixed(2)}`;
        }

        document.getElementById('cart-discount-input').addEventListener('input', renderCart);

        // Place Table Order
        document.getElementById('btn-place-order').addEventListener('click', () => {
            const tableId = document.getElementById('cart-table-select').value;
            const customerName = document.getElementById('cart-customer-name').value || 'Guest';
            const discount = parseFloat(document.getElementById('cart-discount-input').value || 0.00);

            if (cart.length === 0) {
                showToast("Please add items to cart first.", "warning");
                return;
            }

            const payload = {
                table_id: tableId ? parseInt(tableId) : null,
                customer_name: customerName,
                discount: discount,
                order_items: cart,
                status: 'Pending'
            };

            apiFetch('/orders', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Order booked and KDS notified!", "success");
                    cart = [];
                    renderCart();
                    document.getElementById('cart-customer-name').value = '';
                    document.getElementById('cart-table-select').value = '';
                    document.getElementById('cart-discount-input').value = '0';
                    switchTab('kitchen-kds');
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // --- KITCHEN KDS ---
        function loadKitchenKdsQueue() {
            apiFetch('/orders', 'GET').then(res => {
                if (res.success) {
                    renderKdsColumns(res.data);
                }
            });
        }

        function renderKdsColumns(orders) {
            const lists = {
                'Pending': document.getElementById('kds-pending-list'),
                'Preparing': document.getElementById('kds-preparing-list'),
                'Ready': document.getElementById('kds-ready-list'),
                'Completed': document.getElementById('kds-served-list')
            };

            // Clear lists
            for (let key in lists) {
                lists[key].innerHTML = '';
                document.getElementById(`kds-${key.toLowerCase()}-count`).innerText = '0';
            }

            let counts = { 'Pending': 0, 'Preparing': 0, 'Ready': 0, 'Completed': 0 };

            orders.forEach(order => {
                const status = order.status;
                if (!lists[status]) return; // Skip Cancelled, etc.

                counts[status]++;
                
                const card = document.createElement('div');
                card.className = 'kds-card';

                let itemsHtml = '';
                order.order_items.forEach(item => {
                    itemsHtml += `<div class="kds-item-line"><span>${item.item_name}</span><span>x${item.quantity}</span></div>`;
                });

                let actionButton = '';
                if (status === 'Pending') {
                    actionButton = `<button class="btn" style="padding:6px; font-size:11px;" onclick="updateKdsStatus(${order.id}, 'Preparing')">Start Cooking</button>`;
                } else if (status === 'Preparing') {
                    actionButton = `<button class="btn" style="padding:6px; font-size:11px; background:var(--warning);" onclick="updateKdsStatus(${order.id}, 'Ready')">Mark Ready</button>`;
                } else if (status === 'Ready') {
                    actionButton = `<button class="btn" style="padding:6px; font-size:11px; background:var(--success);" onclick="triggerPOSCheckout(${order.id})">Bill Checkout</button>`;
                } else if (status === 'Completed') {
                    actionButton = `<button class="btn" style="padding:6px; font-size:11px; background:black; color:white;" onclick="printThermalInvoice(${order.id})">Print Receipt</button>`;
                }

                card.innerHTML = `
                    <div class="kds-card-header">
                        <span>${order.order_number}</span>
                        <span>${order.customer_name}</span>
                    </div>
                    <div style="font-size:11px; color:var(--text-muted);">Dine-In Table: ${order.table_id ? 'Table ' + order.table_id : 'Takeaway'}</div>
                    <div style="border-top:1px dashed var(--border-glass); padding-top:8px; margin-top:5px;">
                        ${itemsHtml}
                    </div>
                    <div style="margin-top:10px; display:flex; justify-content:flex-end;">
                        ${actionButton}
                    </div>
                `;
                lists[status].appendChild(card);
            });

            for (let key in counts) {
                document.getElementById(`kds-${key.toLowerCase()}-count`).innerText = counts[key];
            }
        }

        function updateKdsStatus(orderId, newStatus) {
            apiFetch(`/orders/${orderId}`, 'PUT', { status: newStatus }).then(res => {
                if (res.success) {
                    showToast(`Order status updated to ${newStatus}`, "success");
                    loadKitchenKdsQueue();
                } else {
                    showToast(res.message, "error");
                }
            });
        }

        // --- POS BILLING CHECKOUT ---
        function triggerPOSCheckout(orderId) {
            currentUpiOrderId = orderId;
            apiFetch(`/orders/${orderId}`, 'GET').then(res => {
                if (res.success) {
                    const order = res.data;
                    
                    // Open select payment method confirmation
                    const method = confirm(`Checkout Invoice for ${order.order_number}?\nTotal amount payable: ₹${parseFloat(order.total_amount).toFixed(2)}\n\nClick OK for UPI QR code payment, CANCEL for standard Cash payment.`);
                    if (method) {
                        // UPI Payment Scan
                        const upiAmt = parseFloat(order.total_amount).toFixed(2);
                        document.getElementById('upi-amount-display').innerText = `₹${upiAmt}`;
                        
                        // Set QR simulation source URL
                        document.getElementById('upi-qr-image').src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=upi://pay?pa=restaurant@ybl%26am=${upiAmt}%26tn=${order.order_number}`;
                        openModal('modal-upi');
                    } else {
                        // Cash Checkout
                        processInvoiceCheckout(orderId, 'Cash');
                    }
                }
            });
        }

        function confirmUpiPayment() {
            closeModal('modal-upi');
            if (currentUpiOrderId) {
                processInvoiceCheckout(currentUpiOrderId, 'UPI');
            }
        }

        function processInvoiceCheckout(orderId, paymentMethod) {
            apiFetch('/billing', 'POST', {
                order_id: orderId,
                payment_method: paymentMethod
            }).then(res => {
                if (res.success) {
                    showToast("Payment confirmed! Invoice printed.", "success");
                    loadKitchenKdsQueue();
                } else {
                    showToast(res.message, "error");
                }
            });
        }

        function printThermalInvoice(orderId) {
            apiFetch(`/orders/${orderId}`, 'GET').then(oRes => {
                if (oRes.success) {
                    const order = oRes.data;
                    apiFetch('/billing', 'GET').then(bRes => {
                        let invoiceNum = 'INV-' + order.order_number.replace('ORD-', '');
                        if (bRes.success) {
                            const matched = bRes.data.find(inv => intval(inv.order_id) === orderId);
                            if (matched) invoiceNum = matched.invoice_number;
                        }

                        let itemsRows = '';
                        order.order_items.forEach(item => {
                            itemsRows += `${item.item_name.padEnd(20)} x${item.quantity.toString().padEnd(3)} ${parseFloat(item.total).toFixed(2).padStart(8)}\n`;
                        });

                        const thermalHtml = `
<pre style="font-family: inherit; font-size: 11px;">
       GLOBAL RESTO POS CAFE       
     Market Yard, Pune, India      
         GSTIN: 27ABCDE1234F       
-----------------------------------
Receipt: ${invoiceNum}
Date: ${order.updated_at}
Table: ${order.table_id ? 'Table ' + order.table_id : 'Takeaway'}
Waiter: Operator
-----------------------------------
ITEMS                QTY    TOTAL  
${itemsRows}-----------------------------------
Subtotal:                ${parseFloat(order.subtotal).toFixed(2).padStart(10)}
GST Tax (5%):            ${parseFloat(order.tax).toFixed(2).padStart(10)}
Discount:                ${parseFloat(order.discount).toFixed(2).padStart(10)}
-----------------------------------
TOTAL PAYABLE:           ${parseFloat(order.total_amount).toFixed(2).padStart(10)}
-----------------------------------
     THANK YOU FOR VISITING!       
  Powered by Custom Restaurant ERP 
</pre>
                        `;
                        document.getElementById('thermal-receipt-content').innerHTML = thermalHtml;
                        openModal('modal-thermal-invoice');
                    });
                }
            });
        }

        // --- TABLES COORDINATOR FLOOR MAP ---
        function loadTablesFloorMap() {
            apiFetch('/tables', 'GET').then(res => {
                if (res.success) {
                    renderTablesCoordMap(res.data);
                }
            });
        }

        function renderTablesCoordMap(data) {
            const grid = document.getElementById('tables-coord-grid');
            grid.innerHTML = '';
            data.forEach(t => {
                const card = document.createElement('div');
                card.className = 'table-coord-card';
                card.onclick = () => editTable(t);
                
                const statClass = 'table-icon-box table-status-' + t.status.toLowerCase();

                card.innerHTML = `
                    <div class="${statClass}">${t.capacity}</div>
                    <h5 style="font-weight:700;">${t.table_number}</h5>
                    <div style="font-size:11px; color:var(--text-muted); margin-top:5px;">${t.floor} | ${t.status}</div>
                `;
                grid.appendChild(card);
            });
        }

        function openTableModal() {
            document.getElementById('table-form').reset();
            document.getElementById('table-id').value = '';
            document.getElementById('table-modal-title').innerText = 'Add Table Coordinates';
            openModal('modal-table');
        }

        function editTable(t) {
            document.getElementById('table-id').value = t.id;
            document.getElementById('table-number').value = t.table_number;
            document.getElementById('table-capacity').value = t.capacity;
            document.getElementById('table-floor').value = t.floor;
            document.getElementById('table-status').value = t.status;
            document.getElementById('table-modal-title').innerText = 'Edit Table Details';
            openModal('modal-table');
        }

        document.getElementById('table-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const id = document.getElementById('table-id').value;
            const payload = {
                table_number: document.getElementById('table-number').value,
                capacity: parseInt(document.getElementById('table-capacity').value),
                floor: document.getElementById('table-floor').value,
                status: document.getElementById('table-status').value
            };

            const method = id ? 'PUT' : 'POST';
            const endpoint = id ? `/tables/${id}` : '/tables';

            apiFetch(endpoint, method, payload).then(res => {
                if (res.success) {
                    showToast("Table coordinates saved successfully.", "success");
                    closeModal('modal-table');
                    loadTablesFloorMap();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // --- DISHES REGISTRY ---
        function loadDishesRegistry() {
            // Load category selectors inside forms
            apiFetch('/categories', 'GET').then(res => {
                if (res.success) {
                    const sel = document.getElementById('dish-category');
                    sel.innerHTML = '';
                    res.data.forEach(c => {
                        sel.innerHTML += `<option value="${c.id}">${c.name}</option>`;
                    });
                }
            });

            // Load items
            apiFetch('/menu', 'GET').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('dishes-list-body');
                    tbody.innerHTML = '';
                    if (res.data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="8">No dish items registered yet.</td></tr>';
                        return;
                    }
                    res.data.forEach(dish => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td style="font-weight:700; color:var(--primary);">${dish.item_code}</td>
                            <td>${dish.item_name}</td>
                            <td>${dish.category_id}</td>
                            <td>${dish.preparation_time} min</td>
                            <td>${dish.tax_percentage}%</td>
                            <td style="font-weight:700; color:var(--accent);">₹${parseFloat(dish.price).toFixed(2)}</td>
                            <td><span style="background:rgba(255,255,255,0.06); padding:4px 8px; border-radius:4px; font-size:11px;">${dish.status}</span></td>
                            <td>
                                <button onclick="formulateRecipe(${dish.id}, '${dish.item_name}')" style="color:var(--accent); background:transparent; border:none; margin-right:10px; cursor:pointer;">Recipe</button>
                                <button onclick="editDish(${dish.id})" style="color:var(--primary); background:transparent; border:none; margin-right:10px; cursor:pointer;">Edit</button>
                                <button onclick="deleteDish(${dish.id})" style="color:var(--danger); background:transparent; border:none; cursor:pointer;">Delete</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            });
        }

        function openDishModal() {
            document.getElementById('dish-form').reset();
            document.getElementById('dish-id').value = '';
            document.getElementById('dish-modal-title').innerText = 'Register Menu Item';
            openModal('modal-dish');
        }

        function editDish(id) {
            apiFetch(`/menu/${id}`, 'GET').then(res => {
                if (res.success) {
                    const dish = res.data;
                    document.getElementById('dish-id').value = dish.id;
                    document.getElementById('dish-code').value = dish.item_code;
                    document.getElementById('dish-name').value = dish.item_name;
                    document.getElementById('dish-category').value = dish.category_id;
                    document.getElementById('dish-price').value = dish.price;
                    document.getElementById('dish-prep-time').value = dish.preparation_time;
                    document.getElementById('dish-description').value = dish.description || '';
                    document.getElementById('dish-modal-title').innerText = 'Edit Menu Item';
                    openModal('modal-dish');
                }
            });
        }

        document.getElementById('dish-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const id = document.getElementById('dish-id').value;
            const fileInput = document.getElementById('dish-file');
            
            const payload = {
                item_code: document.getElementById('dish-code').value,
                item_name: document.getElementById('dish-name').value,
                category_id: parseInt(document.getElementById('dish-category').value),
                price: parseFloat(document.getElementById('dish-price').value),
                preparation_time: parseInt(document.getElementById('dish-prep-time').value),
                description: document.getElementById('dish-description').value
            };

            const submitSave = (imageUrl = '') => {
                if (imageUrl) payload.image = imageUrl;

                const method = id ? 'PUT' : 'POST';
                const endpoint = id ? `/menu/${id}` : '/menu';

                apiFetch(endpoint, method, payload).then(res => {
                    if (res.success) {
                        showToast("Dish item registry updated.", "success");
                        closeModal('modal-dish');
                        loadDishesRegistry();
                    } else {
                        showToast(res.message, "error");
                    }
                });
            };

            if (fileInput.files.length > 0) {
                // Upload image first
                const formData = new FormData();
                formData.append('file', fileInput.files[0]);
                
                let uploadUrl = API_NAMESPACE + '/media/upload';
                if (token) {
                    uploadUrl += '?token=' + encodeURIComponent(token);
                }

                fetch(uploadUrl, {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'X-Authorization': 'Bearer ' + token
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        submitSave(res.data.url);
                    } else {
                        showToast("Image upload failed: " + res.message, "error");
                    }
                });
            } else {
                submitSave();
            }
        });

        function deleteDish(id) {
            if (confirm("Are you sure you want to delete this dish item?")) {
                apiFetch(`/menu/${id}`, 'DELETE').then(res => {
                    if (res.success) {
                        showToast("Dish deleted.", "success");
                        loadDishesRegistry();
                    } else {
                        showToast(res.message, "error");
                    }
                });
            }
        }

        // --- FOOD RECIPES FORMULARY MODAL ---
        function formulateRecipe(menuItemId, itemName) {
            document.getElementById('recipe-menu-item-id').value = menuItemId;
            document.getElementById('recipe-dish-title').innerText = `Formulating ingredient composition for: ${itemName}`;
            
            // Load ingredients select list
            apiFetch('/inventory', 'GET').then(res => {
                if (res.success) {
                    const sel = document.getElementById('recipe-ingredient-select');
                    sel.innerHTML = '';
                    res.data.forEach(ing => {
                        sel.innerHTML += `<option value="${ing.id}">${ing.ingredient_name} (${ing.unit})</option>`;
                    });
                }
            });

            // Load recipe ingredients list table
            apiFetch(`/recipes/${menuItemId}`, 'GET').then(res => {
                if (res.success) {
                    renderRecipeIngredients(res.data);
                }
            });

            openModal('modal-recipe');
        }

        function renderRecipeIngredients(items) {
            const tbody = document.getElementById('modal-recipe-ingredients-list');
            tbody.innerHTML = '';
            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4">No ingredients linked yet.</td></tr>';
                return;
            }
            items.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${item.ingredient_name}</td>
                    <td>${item.quantity_required}</td>
                    <td>${item.unit}</td>
                    <td><button onclick="removeRecipeIngredient(${item.ingredient_id})" style="color:var(--danger); background:transparent; border:none; cursor:pointer;">Remove</button></td>
                `;
                tbody.appendChild(tr);
            });
        }

        document.getElementById('recipe-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const menuItemId = intval(document.getElementById('recipe-menu-item-id').value);
            const ingredientId = intval(document.getElementById('recipe-ingredient-select').value);
            const qty = floatval(document.getElementById('recipe-quantity').value);

            if (!qty) {
                showToast("Please enter a valid quantity.", "warning");
                return;
            }

            // Load current lists
            apiFetch(`/recipes/${menuItemId}`, 'GET').then(res => {
                if (res.success) {
                    let list = res.data;
                    const existingIdx = list.findIndex(i => intval(i.ingredient_id) === ingredientId);
                    if (existingIdx !== -1) {
                        list[existingIdx].quantity_required = qty;
                    } else {
                        list.push({
                            ingredient_id: ingredientId,
                            quantity_required: qty
                        });
                    }

                    // Save all back
                    apiFetch('/recipes', 'POST', {
                        menu_item_id: menuItemId,
                        ingredients: list
                    }).then(sRes => {
                        if (sRes.success) {
                            showToast("Recipe ingredient updated.", "success");
                            renderRecipeIngredients(sRes.data);
                            document.getElementById('recipe-quantity').value = '';
                        }
                    });
                }
            });
        });

        function removeRecipeIngredient(ingredientId) {
            const menuItemId = intval(document.getElementById('recipe-menu-item-id').value);
            apiFetch(`/recipes/${menuItemId}`, 'GET').then(res => {
                if (res.success) {
                    const list = res.data.filter(i => intval(i.ingredient_id) !== ingredientId);
                    apiFetch('/recipes', 'POST', {
                        menu_item_id: menuItemId,
                        ingredients: list
                    }).then(sRes => {
                        if (sRes.success) {
                            showToast("Ingredient removed from recipe.", "success");
                            renderRecipeIngredients(sRes.data);
                        }
                    });
                }
            });
        }

        // --- MANAGING CATEGORIES MODAL ---
        function openCategoryModal() {
            loadModalCategoriesList();
            openModal('modal-category');
        }

        function loadModalCategoriesList() {
            apiFetch('/categories', 'GET').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('modal-categories-list');
                    tbody.innerHTML = '';
                    res.data.forEach(c => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${c.id}</td>
                                <td>${c.name}</td>
                                <td><button onclick="deleteCategory(${c.id})" style="color:var(--danger); background:transparent; border:none; cursor:pointer;">✖</button></td>
                            </tr>
                        `;
                    });
                }
            });
        }

        document.getElementById('category-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('category-name').value;
            apiFetch('/categories', 'POST', { name: name }).then(res => {
                if (res.success) {
                    showToast("Category registered.", "success");
                    document.getElementById('category-name').value = '';
                    loadModalCategoriesList();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        function deleteCategory(id) {
            if (confirm("Delete this category?")) {
                apiFetch(`/categories/${id}`, 'DELETE').then(res => {
                    if (res.success) {
                        showToast("Category deleted.", "success");
                        loadModalCategoriesList();
                    } else {
                        showToast(res.message, "error");
                    }
                });
            }
        }

        // --- INGREDIENTS INVENTORY ---
        function loadStockInventory() {
            // Load supplier dropdown lists
            apiFetch('/suppliers', 'GET').then(res => {
                if (res.success) {
                    suppliers = res.data;
                    const sel = document.getElementById('ingredient-supplier');
                    sel.innerHTML = '<option value="">No Supplier Assigned</option>';
                    suppliers.forEach(s => {
                        sel.innerHTML += `<option value="${s.id}">${s.supplier_name}</option>`;
                    });
                }
            });

            // Load list
            apiFetch('/inventory', 'GET').then(res => {
                if (res.success) {
                    ingredients = res.data;
                    const tbody = document.getElementById('ingredients-list-body');
                    tbody.innerHTML = '';
                    if (ingredients.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="7">No raw ingredients cataloged.</td></tr>';
                        return;
                    }
                    ingredients.forEach(ing => {
                        const lowStock = floatval(ing.current_stock) <= floatval(ing.minimum_stock);
                        const stockColor = lowStock ? 'var(--danger)' : 'var(--success)';
                        const statusBadge = lowStock ? `<span style="background:rgba(255,93,143,0.15); color:var(--danger); padding:4px 8px; border-radius:4px; font-size:11px;">LOW STOCK</span>` : `<span style="background:rgba(255,255,255,0.06); padding:4px 8px; border-radius:4px; font-size:11px;">Active</span>`;

                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td style="font-weight:700;">${ing.ingredient_name}</td>
                            <td style="font-weight:800; color:${stockColor};">${parseFloat(ing.current_stock).toFixed(2)}</td>
                            <td>${parseFloat(ing.minimum_stock).toFixed(2)}</td>
                            <td>${ing.unit}</td>
                            <td>₹${parseFloat(ing.purchase_price).toFixed(2)}</td>
                            <td>${statusBadge}</td>
                            <td>
                                <button onclick="editIngredient(${ing.id})" style="color:var(--primary); background:transparent; border:none; margin-right:10px; cursor:pointer;">Edit</button>
                                <button onclick="deleteIngredient(${ing.id})" style="color:var(--danger); background:transparent; border:none; cursor:pointer;">Delete</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            });
        }

        function openIngredientModal() {
            document.getElementById('ingredient-form').reset();
            document.getElementById('ingredient-id').value = '';
            document.getElementById('ingredient-modal-title').innerText = 'Register Raw Ingredient';
            openModal('modal-ingredient');
        }

        function editIngredient(id) {
            const ing = ingredients.find(i => i.id === id);
            if (ing) {
                document.getElementById('ingredient-id').value = ing.id;
                document.getElementById('ingredient-name').value = ing.ingredient_name;
                document.getElementById('ingredient-unit').value = ing.unit;
                document.getElementById('ingredient-stock').value = ing.current_stock;
                document.getElementById('ingredient-min-stock').value = ing.minimum_stock;
                document.getElementById('ingredient-price').value = ing.purchase_price;
                document.getElementById('ingredient-supplier').value = ing.supplier_id || '';
                document.getElementById('ingredient-modal-title').innerText = 'Edit Ingredient Details';
                openModal('modal-ingredient');
            }
        }

        document.getElementById('ingredient-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const id = document.getElementById('ingredient-id').value;
            const payload = {
                ingredient_name: document.getElementById('ingredient-name').value,
                unit: document.getElementById('ingredient-unit').value,
                current_stock: parseFloat(document.getElementById('ingredient-stock').value),
                minimum_stock: floatval(document.getElementById('ingredient-min-stock').value),
                purchase_price: parseFloat(document.getElementById('ingredient-price').value),
                supplier_id: document.getElementById('ingredient-supplier').value ? parseInt(document.getElementById('ingredient-supplier').value) : null
            };

            const method = id ? 'PUT' : 'POST';
            const endpoint = id ? `/inventory/${id}` : '/inventory';

            apiFetch(endpoint, method, payload).then(res => {
                if (res.success) {
                    showToast("Ingredient details updated.", "success");
                    closeModal('modal-ingredient');
                    loadStockInventory();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        function deleteIngredient(id) {
            if (confirm("Delete this ingredient from catalog?")) {
                apiFetch(`/inventory/${id}`, 'DELETE').then(res => {
                    if (res.success) {
                        showToast("Ingredient deleted.", "success");
                        loadStockInventory();
                    } else {
                        showToast(res.message, "error");
                    }
                });
            }
        }

        // --- B2B SUPPLIERS MODAL LIST ---
        function openSupplierModal() {
            loadModalSuppliersList();
            openModal('modal-supplier');
        }

        function loadModalSuppliersList() {
            apiFetch('/suppliers', 'GET').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('modal-suppliers-list');
                    tbody.innerHTML = '';
                    res.data.forEach(s => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${s.supplier_name}</td>
                                <td>${s.mobile}</td>
                                <td><button onclick="deleteSupplier(${s.id})" style="color:var(--danger); background:transparent; border:none; cursor:pointer;">✖</button></td>
                            </tr>
                        `;
                    });
                }
            });
        }

        document.getElementById('supplier-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                supplier_name: document.getElementById('supplier-name').value,
                mobile: document.getElementById('supplier-mobile').value,
                gst_number: document.getElementById('supplier-gst').value,
                address: document.getElementById('supplier-address').value
            };

            apiFetch('/suppliers', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Supplier details registered.", "success");
                    document.getElementById('supplier-form').reset();
                    loadModalSuppliersList();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        function deleteSupplier(id) {
            if (confirm("Remove supplier registration?")) {
                apiFetch(`/suppliers/${id}`, 'DELETE').then(res => {
                    if (res.success) {
                        showToast("Supplier removed.", "success");
                        loadModalSuppliersList();
                    } else {
                        showToast(res.message, "error");
                    }
                });
            }
        }

        // --- REPLENISH RESTOCK ORDER MODAL ---
        function openPurchaseModal() {
            // Load selectors
            const sSel = document.getElementById('purchase-supplier-select');
            sSel.innerHTML = '';
            suppliers.forEach(s => {
                sSel.innerHTML += `<option value="${s.id}">${s.supplier_name}</option>`;
            });

            const iSel = document.getElementById('purchase-ingredient-select');
            iSel.innerHTML = '';
            ingredients.forEach(i => {
                iSel.innerHTML += `<option value="${i.id}">${i.ingredient_name} (Current: ${i.current_stock})</option>`;
            });

            document.getElementById('purchase-quantity').value = '';
            document.getElementById('purchase-price').value = '';

            openModal('modal-purchase');
        }

        document.getElementById('purchase-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const supplierId = intval(document.getElementById('purchase-supplier-select').value);
            const ingredientId = intval(document.getElementById('purchase-ingredient-select').value);
            const qty = floatval(document.getElementById('purchase-quantity').value);
            const price = floatval(document.getElementById('purchase-price').value);

            const payload = {
                supplier_id: supplierId,
                status: 'Completed',
                purchase_items: [
                    {
                        ingredient_id: ingredientId,
                        quantity: qty,
                        price: price
                    }
                ]
            };

            apiFetch('/purchases', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Replenish PO logged. Stock updated.", "success");
                    closeModal('modal-purchase');
                    loadStockInventory();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // --- DELIVERY DISPATCH TRACKING ---
        function loadDeliveriesList() {
            apiFetch('/deliveries', 'GET').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('deliveries-list-body');
                    tbody.innerHTML = '';
                    if (res.data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6">No delivery orders logged.</td></tr>';
                        return;
                    }
                    res.data.forEach(del => {
                        const actionBtn = del.delivery_status !== 'Delivered' ? `<button class="btn" style="padding:6px; font-size:11px;" onclick="advanceDeliveryStatus(${del.id}, '${del.delivery_status}')">Advance Status</button>` : `<span style="font-size:11px; color:var(--success); font-weight:700;">Delivered</span>`;
                        
                        tbody.innerHTML += `
                            <tr>
                                <td>Order #${del.order_id}</td>
                                <td>${del.customer_address}</td>
                                <td>${del.delivery_partner}</td>
                                <td>₹${parseFloat(del.delivery_charge).toFixed(2)}</td>
                                <td><span style="background:rgba(255,255,255,0.06); padding:4px 8px; border-radius:4px; font-size:11px;">${del.delivery_status}</span></td>
                                <td>${actionBtn}</td>
                            </tr>
                        `;
                    });
                }
            });
        }

        function openDeliveryModal() {
            // Load KDS orders which are Completed / Ready
            apiFetch('/orders', 'GET').then(res => {
                if (res.success) {
                    const oSel = document.getElementById('delivery-order-select');
                    oSel.innerHTML = '';
                    res.data.forEach(o => {
                        oSel.innerHTML += `<option value="${o.id}">${o.order_number} (${o.customer_name})</option>`;
                    });
                }
            });

            document.getElementById('delivery-address').value = '';
            openModal('modal-delivery');
        }

        document.getElementById('delivery-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                order_id: intval(document.getElementById('delivery-order-select').value),
                delivery_partner: document.getElementById('delivery-partner').value,
                customer_address: document.getElementById('delivery-address').value,
                delivery_charge: 40.00
            };

            apiFetch('/deliveries', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Delivery dispatch job assigned.", "success");
                    closeModal('modal-delivery');
                    loadDeliveriesList();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        function advanceDeliveryStatus(id, currentStatus) {
            let next = 'Delivered';
            if (currentStatus === 'Assigned') next = 'Picked Up';
            else if (currentStatus === 'Picked Up') next = 'Out For Delivery';
            else if (currentStatus === 'Out For Delivery') next = 'Delivered';

            apiFetch(`/deliveries/${id}`, 'PUT', { delivery_status: next }).then(res => {
                if (res.success) {
                    showToast(`Delivery status advanced to ${next}`, "success");
                    loadDeliveriesList();
                }
            });
        }

        // --- SHIFTS SCHEDULER ---
        function loadStaffList() {
            apiFetch('/staff', 'GET').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('staff-list-body');
                    tbody.innerHTML = '';
                    if (res.data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6">No employees scheduled.</td></tr>';
                        return;
                    }
                    res.data.forEach(st => {
                        const tr = document.createElement('tr');
                        const isPresent = st.attendance_status === 'Present';
                        const checkBtn = `<input type="checkbox" ${isPresent ? 'checked' : ''} onchange="toggleStaffAttendance(${st.id}, this)">`;

                        tr.innerHTML = `
                            <td style="font-weight:700;">${st.name}</td>
                            <td>${st.role}</td>
                            <td>${st.shift_start} - ${st.shift_end}</td>
                            <td>₹${parseFloat(st.salary).toFixed(2)}</td>
                            <td><span style="margin-right:10px;">${st.attendance_status}</span></td>
                            <td>
                                <span style="margin-right:15px;">Mark Present: ${checkBtn}</span>
                                <button onclick="deleteStaff(${st.id})" style="color:var(--danger); background:transparent; border:none; cursor:pointer;">Delete</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            });
        }

        function openStaffModal() {
            document.getElementById('staff-form').reset();
            document.getElementById('staff-id').value = '';
            document.getElementById('staff-modal-title').innerText = 'Schedule Staff Shift';
            openModal('modal-staff');
        }

        document.getElementById('staff-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const id = document.getElementById('staff-id').value;
            const payload = {
                name: document.getElementById('staff-name').value,
                role: document.getElementById('staff-role').value,
                shift_start: document.getElementById('staff-start').value,
                shift_end: document.getElementById('staff-end').value,
                salary: parseFloat(document.getElementById('staff-salary').value)
            };

            const method = id ? 'PUT' : 'POST';
            const endpoint = id ? `/staff/${id}` : '/staff';

            apiFetch(endpoint, method, payload).then(res => {
                if (res.success) {
                    showToast("Shift schedule updated.", "success");
                    closeModal('modal-staff');
                    loadStaffList();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        function toggleStaffAttendance(id, cb) {
            const status = cb.checked ? 'Present' : 'Absent';
            apiFetch(`/staff/${id}`, 'PUT', { attendance_status: status }).then(res => {
                if (res.success) {
                    showToast(`Staff marked ${status}.`, "success");
                    loadStaffList();
                }
            });
        }

        function deleteStaff(id) {
            if (confirm("Delete shift registry?")) {
                apiFetch(`/staff/${id}`, 'DELETE').then(res => {
                    if (res.success) {
                        showToast("Shift cleared.", "success");
                        loadStaffList();
                    }
                });
            }
        }

        // --- EXPENSES LOGS & REPORTS ---
        function loadExpensesAndOverhead() {
            // Load dashboard KPI reports first
            apiFetch('/reports/profit-loss', 'GET').then(res => {
                if (res.success) {
                    const r = res.data;
                    document.getElementById('rep-overhead-val').innerText = `₹${parseFloat(r.expenses_overhead).toFixed(2)}`;
                    document.getElementById('rep-restock-val').innerText = `₹${parseFloat(r.expenses_restock).toFixed(2)}`;
                    document.getElementById('rep-revenue-val').innerText = `₹${parseFloat(r.revenue).toFixed(2)}`;
                    document.getElementById('rep-profit-val').innerText = `₹${parseFloat(r.net_profit).toFixed(2)}`;
                }
            });

            // Load list
            apiFetch('/expenses', 'GET').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('expenses-list-body');
                    tbody.innerHTML = '';
                    if (res.data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6">No operating expenses logged.</td></tr>';
                        return;
                    }
                    res.data.forEach(exp => {
                        tbody.innerHTML += `
                            <tr>
                                <td>#${exp.id}</td>
                                <td>${exp.expense_type}</td>
                                <td style="font-weight:700; color:var(--danger);">₹${parseFloat(exp.amount).toFixed(2)}</td>
                                <td>${exp.description || 'N/A'}</td>
                                <td>${exp.created_at}</td>
                                <td><button onclick="deleteExpense(${exp.id})" style="color:var(--danger); background:transparent; border:none; cursor:pointer;">Delete</button></td>
                            </tr>
                        `;
                    });
                }
            });
        }

        function openExpenseModal() {
            document.getElementById('expense-form').reset();
            openModal('modal-expense');
        }

        document.getElementById('expense-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                expense_type: document.getElementById('expense-type').value,
                amount: parseFloat(document.getElementById('expense-amount').value),
                description: document.getElementById('expense-desc').value
            };

            apiFetch('/expenses', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Expense logged successfully.", "success");
                    closeModal('modal-expense');
                    loadExpensesAndOverhead();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        function deleteExpense(id) {
            if (confirm("Remove expense entry?")) {
                apiFetch(`/expenses/${id}`, 'DELETE').then(res => {
                    if (res.success) {
                        showToast("Expense log deleted.", "success");
                        loadExpensesAndOverhead();
                    }
                });
            }
        }

        // --- DIAGNOSTICS & SYSTEM USERS ADMIN ---
        function loadDiagnosticsAdmin() {
            // Load user operators list
            apiFetch('/auth/users', 'GET').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('users-list-body');
                    tbody.innerHTML = '';
                    res.data.forEach(u => {
                        const color = u.status === 'APPROVED' ? 'var(--success)' : (u.status === 'HOLD' ? 'var(--warning)' : 'var(--danger)');
                        
                        tbody.innerHTML += `
                            <tr>
                                <td style="font-weight:700;">${u.username}</td>
                                <td>${u.email}</td>
                                <td>${u.role.replace('restaurant_', '')}</td>
                                <td style="font-weight:700; color:${color};">${u.status}</td>
                                <td>
                                    <select onchange="updateUserStatus(${u.id}, this.value)" style="background:#0f0c1b; border:1px solid var(--border-glass); color:white; padding:4px; font-size:12px;">
                                        <option value="">Choose Status...</option>
                                        <option value="APPROVED">Approve</option>
                                        <option value="HOLD">Hold</option>
                                        <option value="BLOCKED">Block</option>
                                    </select>
                                </td>
                                <td><button onclick="deleteUserOperator(${u.id})" style="color:var(--danger); background:transparent; border:none; cursor:pointer;">Delete</button></td>
                            </tr>
                        `;
                    });
                }
            });

            // Load SMTP settings
            apiFetch('/auth/smtp', 'GET').then(res => {
                if (res.success) {
                    const s = res.data;
                    document.getElementById('smtp-host').value = s.smtp_host || '';
                    document.getElementById('smtp-port').value = s.smtp_port || '';
                    document.getElementById('smtp-username').value = s.smtp_username || '';
                    document.getElementById('smtp-password').value = s.smtp_password || '';
                    document.getElementById('smtp-from-email').value = s.from_email || '';
                    document.getElementById('smtp-from-name').value = s.from_name || '';
                    document.getElementById('smtp-encryption').value = s.smtp_encryption || 'tls';
                    document.getElementById('smtp-enabled').value = s.smtp_enabled || 'no';
                }
            });
        }

        document.getElementById('smtp-config-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                smtp_host: document.getElementById('smtp-host').value,
                smtp_port: document.getElementById('smtp-port').value,
                smtp_username: document.getElementById('smtp-username').value,
                smtp_password: document.getElementById('smtp-password').value,
                from_email: document.getElementById('smtp-from-email').value,
                from_name: document.getElementById('smtp-from-name').value,
                smtp_encryption: document.getElementById('smtp-encryption').value,
                smtp_enabled: document.getElementById('smtp-enabled').value,
                template: "Hello {name},\n\nYour 6-digit verification code is: {otp}\n\nThis code is valid for 15 minutes.\n\nThank you!"
            };

            apiFetch('/auth/smtp', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("SMTP configuration saved.", "success");
                    loadDiagnosticsAdmin();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        function sendTestEmail() {
            const email = document.getElementById('smtp-test-email').value;
            if (!email) {
                showToast("Please enter recipient email.", "warning");
                return;
            }

            showToast("Sending test email...", "warning");
            apiFetch('/auth/smtp/test', 'POST', { test_email: email }).then(res => {
                if (res.success) {
                    showToast("Test email sent successfully!", "success");
                } else {
                    showToast("Test failed: " + res.message, "error");
                }
            });
        }

        function updateUserStatus(userId, statusVal) {
            if (!statusVal) return;
            apiFetch('/auth/users/status', 'POST', { user_id: userId, status: statusVal }).then(res => {
                if (res.success) {
                    showToast(`User status updated to ${statusVal}`, "success");
                    loadDiagnosticsAdmin();
                } else {
                    showToast(res.message, "error");
                }
            });
        }

        function deleteUserOperator(userId) {
            if (confirm("Delete user permanently?")) {
                apiFetch(`/auth/users/${userId}`, 'DELETE').then(res => {
                    if (res.success) {
                        showToast("Operator deleted.", "success");
                        loadDiagnosticsAdmin();
                    } else {
                        showToast(res.message, "error");
                    }
                });
            }
        }

        // --- GLOBAL OVERLAYS & MODALS MANAGER ---
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        // --- TOAST SERVICE ---
        function showToast(message, type = 'success') {
            const box = document.getElementById('toast-container');
            const div = document.createElement('div');
            div.className = `toast ${type}`;
            div.innerHTML = `
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" style="margin-left:15px; background:transparent; border:none; color:var(--text-muted); cursor:pointer;">✖</button>
            `;
            box.appendChild(div);
            
            setTimeout(() => div.classList.add('show'), 50);
            setTimeout(() => {
                div.classList.remove('show');
                setTimeout(() => div.remove(), 300);
            }, 3000);
        }

        // Quick cast helpers
        function intval(val) {
            const res = parseInt(val);
            return isNaN(res) ? 0 : res;
        }

        function floatval(val) {
            const res = parseFloat(val);
            return isNaN(res) ? 0.00 : res;
        }
    </script>
</body>
</html>
