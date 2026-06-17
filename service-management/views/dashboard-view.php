<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Business ERP - Dashboard</title>
    <!-- Modern Premium Typography -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Sleek Default Light Mode Color Scheme */
            --bg-primary: #f8fafc;
            --bg-secondary: rgba(255, 255, 255, 0.7);
            --bg-card: rgba(255, 255, 255, 0.85);
            --accent-blue: #2563eb;
            --accent-purple: #7c3aed;
            --accent-pink: #db2777;
            --accent-emerald: #059669;
            --accent-warning: #ea580c;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --glass-border: rgba(15, 23, 42, 0.08);
            --glass-shadow: rgba(15, 23, 42, 0.04);
            --border-hover: rgba(15, 23, 42, 0.15);
            --input-bg: #ffffff;
            --input-border: rgba(15, 23, 42, 0.12);
            --input-text: #0f172a;
            --sidebar-bg: rgba(255, 255, 255, 0.85);
            --sidebar-border: rgba(15, 23, 42, 0.08);
        }

        .dark-mode {
            /* Glassmorphism Dark Mode Color Scheme */
            --bg-primary: #0b0f19;
            --bg-secondary: rgba(17, 24, 39, 0.75);
            --bg-card: rgba(31, 41, 55, 0.6);
            --accent-blue: #3b82f6;
            --accent-purple: #8b5cf6;
            --accent-pink: #ec4899;
            --accent-emerald: #10b981;
            --accent-warning: #f97316;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --glass-border: rgba(255, 255, 255, 0.08);
            --glass-shadow: rgba(0, 0, 0, 0.45);
            --border-hover: rgba(255, 255, 255, 0.15);
            --input-bg: rgba(255, 255, 255, 0.03);
            --input-border: rgba(255, 255, 255, 0.1);
            --input-text: #ffffff;
            --sidebar-bg: rgba(10, 15, 30, 0.85);
            --sidebar-border: rgba(255, 255, 255, 0.08);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
            transition: background-color 0.3s, border-color 0.3s;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-main);
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(37, 99, 235, 0.06) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(124, 58, 237, 0.06) 0%, transparent 40%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        /* Prevent Layout flash */
        html.is-authenticated .auth-container { display: none !important; }
        html.is-unauthenticated .app-container { display: none !important; }

        /* Toast notifications */
        .toast-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .toast {
            background: var(--bg-card);
            border: 1px solid var(--accent-blue);
            color: var(--text-main);
            padding: 16px 24px;
            border-radius: 14px;
            box-shadow: 0 10px 30px var(--glass-shadow);
            backdrop-filter: blur(12px);
            font-size: 14px;
            font-weight: 500;
            min-width: 300px;
            transform: translateX(120%);
            transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }
        .toast.show {
            transform: translateX(0);
        }
        .toast.success { border-color: var(--accent-emerald); }
        .toast.error { border-color: var(--accent-pink); }

        /* Auth Layout styling */
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(37, 99, 235, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(124, 58, 237, 0.08) 0%, transparent 50%);
        }
        .auth-card {
            background: var(--bg-secondary);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 28px;
            width: 100%;
            max-width: 500px;
            padding: 40px;
            box-shadow: 0 20px 60px var(--glass-shadow);
        }
        .auth-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .auth-logo h2 {
            font-weight: 700;
            font-size: 26px;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
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
            font-weight: 600;
            color: var(--text-muted);
        }
        .form-input {
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 12px;
            padding: 12px 16px;
            color: var(--input-text);
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
        }
        .form-input:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
        .auth-submit-btn {
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            color: #fff;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transition: all 0.2s;
        }
        .auth-submit-btn:hover {
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
            transform: translateY(-1px);
        }
        .auth-toggle-link {
            text-align: center;
            margin-top: 15px;
            font-size: 13px;
            color: var(--text-muted);
        }
        .auth-toggle-link a {
            color: var(--accent-blue);
            text-decoration: none;
            font-weight: 600;
        }
        .auth-toggle-link a:hover {
            text-decoration: underline;
        }
        
        /* Demo Credential Buttons Grid */
        .demo-roles-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 24px;
        }
        .demo-role-btn {
            background: var(--bg-card);
            border: 1px solid var(--glass-border);
            padding: 12px;
            border-radius: 12px;
            text-align: left;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 3px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            transition: all 0.2s;
        }
        .demo-role-btn:hover {
            background: rgba(37, 99, 235, 0.08);
            border-color: var(--accent-blue);
            transform: translateY(-1px);
        }
        .demo-role-title {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-main);
        }
        .demo-role-user {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* App Main Dashboard Layout */
        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Fixed Left Sidebar */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--sidebar-border);
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: fixed;
            height: 100vh;
            z-index: 100;
            box-shadow: 4px 0 25px var(--glass-shadow);
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 21px;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 35px;
        }
        .brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            border-radius: 10px;
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
            gap: 6px;
            flex-grow: 1;
            overflow-y: auto;
            margin-bottom: 20px;
        }
        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
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
        .menu-item svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
        }
        .menu-item:hover, .menu-item.active {
            background: rgba(37, 99, 235, 0.08);
            color: var(--accent-blue);
            font-weight: 600;
        }
        .menu-item.active {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.12), rgba(124, 58, 237, 0.06));
        }

        /* Profile details at bottom of Sidebar */
        .user-profile-wrapper {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
            border-top: 1px solid var(--glass-border);
            padding-top: 20px;
        }
        .user-profile {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px;
            background: var(--bg-card);
            border-radius: 14px;
            border: 1px solid var(--glass-border);
        }
        .user-profile-inner {
            display: flex;
            align-items: center;
            gap: 12px;
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
            font-size: 14px;
        }
        .user-info h4 {
            font-size: 13px;
            font-weight: 600;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .user-info p {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: capitalize;
        }
        .theme-toggle-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .theme-toggle-btn:hover {
            color: var(--accent-blue);
            background: rgba(37, 99, 235, 0.08);
        }
        .theme-toggle-btn svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
        }

        .logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px;
            background: rgba(239, 68, 68, 0.08);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.15);
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            outline: none;
        }
        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.3);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.08);
        }

        /* Right Content Panel */
        .main-panel {
            flex-grow: 1;
            padding: 40px;
            margin-left: 280px; /* Offset fixed sidebar */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .title-group h1 {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }
        .title-group p {
            color: var(--text-muted);
            font-size: 14px;
        }
        .badge-live {
            background: rgba(5, 150, 105, 0.08);
            border: 1px solid var(--accent-emerald);
            color: var(--accent-emerald);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 10px rgba(5, 150, 105, 0.05);
        }
        .live-dot {
            width: 8px;
            height: 8px;
            background-color: var(--accent-emerald);
            border-radius: 50%;
            animation: pulse 1.6s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(5, 150, 105, 0.7); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 6px rgba(5, 150, 105, 0); }
            100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(5, 150, 105, 0); }
        }

        /* Tab panels display */
        .tab-panel {
            display: none;
            animation: fadeIn 0.35s ease-out;
        }
        .tab-panel.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Dashboard Overview Grid Cards */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--glass-border);
            border-radius: 18px;
            padding: 22px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-shadow: 0 6px 20px var(--glass-shadow);
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            border-color: var(--border-hover);
            box-shadow: 0 10px 30px var(--glass-shadow);
        }
        .stat-card::after {
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
        .stat-card:hover::after { opacity: 1; }
        .card-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(37, 99, 235, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: var(--accent-blue);
        }
        .card-icon svg {
            width: 20px;
            height: 20px;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
        }
        .stat-card:nth-child(2) .card-icon { color: var(--accent-purple); background: rgba(124, 58, 237, 0.08); }
        .stat-card:nth-child(3) .card-icon { color: var(--accent-pink); background: rgba(219, 39, 119, 0.08); }
        .stat-card:nth-child(4) .card-icon { color: var(--accent-emerald); background: rgba(5, 150, 105, 0.08); }
        .stat-card:nth-child(5) .card-icon { color: var(--accent-warning); background: rgba(234, 88, 12, 0.08); }
        .card-label {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 600;
        }
        .card-value {
            font-size: 24px;
            font-weight: 700;
        }

        /* Analytics Section Layout */
        .analytics-row {
            display: grid;
            grid-template-columns: 3fr 2fr;
            gap: 24px;
            margin-bottom: 30px;
        }
        .analytic-box {
            background: var(--bg-card);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 6px 20px var(--glass-shadow);
        }
        .analytic-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--glass-border);
            padding-bottom: 12px;
        }
        .analytic-header h3 {
            font-size: 16px;
            font-weight: 600;
        }

        /* Interactive Checklist Widget */
        .checklist-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 250px;
            overflow-y: auto;
        }
        .checklist-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            background: rgba(15, 23, 42, 0.02);
            border-radius: 10px;
            border: 1px solid var(--glass-border);
        }
        .checklist-item-left {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 500;
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
        .checklist-delete-btn {
            background: transparent;
            border: none;
            color: #ef4444;
            cursor: pointer;
            padding: 2px;
            border-radius: 4px;
        }
        .checklist-delete-btn:hover {
            background: rgba(239, 68, 68, 0.08);
        }
        .checklist-add-form {
            display: flex;
            gap: 8px;
            margin-top: 15px;
        }

        /* Technician Load progress bar visualization */
        .tech-load-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .tech-load-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .tech-load-info {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 600;
        }
        .tech-load-bar-bg {
            height: 8px;
            background: rgba(15, 23, 42, 0.06);
            border-radius: 4px;
            overflow: hidden;
        }
        .tech-load-bar-fg {
            height: 100%;
            background: linear-gradient(to right, var(--accent-blue), var(--accent-purple));
            border-radius: 4px;
            width: 0;
            transition: width 0.8s ease;
        }

        /* Activity Log List */
        .logs-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 250px;
            overflow-y: auto;
        }
        .log-item {
            padding: 10px 14px;
            background: rgba(15, 23, 42, 0.02);
            border-radius: 10px;
            border-left: 3px solid var(--accent-blue);
            font-size: 12.5px;
            line-height: 1.4;
        }
        .log-item-header {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 2px;
        }

        /* Standard Table List Layout */
        .table-container {
            background: var(--bg-card);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 6px 20px var(--glass-shadow);
            margin-bottom: 30px;
        }
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .table-title {
            font-size: 18px;
            font-weight: 700;
        }
        .table-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }
        .search-box {
            position: relative;
        }
        .search-box input {
            padding: 10px 14px 10px 36px;
            border-radius: 10px;
            border: 1px solid var(--input-border);
            background: var(--input-bg);
            color: var(--text-main);
            outline: none;
            font-size: 13.5px;
            width: 220px;
        }
        .search-box svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 15px;
            height: 15px;
            stroke: var(--text-muted);
            fill: none;
            stroke-width: 2.5;
        }
        .select-filter {
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid var(--input-border);
            background: var(--input-bg);
            color: var(--text-main);
            font-size: 13.5px;
            outline: none;
            cursor: pointer;
        }

        .data-table-wrapper {
            overflow-x: auto;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
            text-align: left;
        }
        .data-table th {
            padding: 14px 16px;
            border-bottom: 1px solid var(--glass-border);
            color: var(--text-muted);
            font-weight: 600;
        }
        .data-table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--glass-border);
            color: var(--text-main);
        }
        .data-table tr:hover td {
            background: rgba(37, 99, 235, 0.03);
        }

        /* Status & Priority Badge Tags */
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
            text-align: center;
        }
        .badge-pending, .badge-draft, .badge-unpaid, .badge-suspended { background: rgba(234, 88, 12, 0.1); color: var(--accent-warning); }
        .badge-active, .badge-won, .badge-accepted, .badge-completed, .badge-paid, .badge-approved { background: rgba(5, 150, 105, 0.1); color: var(--accent-emerald); }
        .badge-lost, .badge-declined, .badge-cancelled, .badge-blocked { background: rgba(219, 39, 119, 0.1); color: var(--accent-pink); }
        .badge-qualified, .badge-sent, .badge-inprogress, .badge-partiallypaid { background: rgba(37, 99, 235, 0.1); color: var(--accent-blue); }
        .badge-high { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .badge-medium { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .badge-low { background: rgba(107, 114, 128, 0.1); color: #6b7280; }

        /* Action Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            outline: none;
            text-decoration: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            color: #fff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }
        .btn-primary:hover {
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: var(--bg-card);
            color: var(--text-main);
            border: 1px solid var(--glass-border);
        }
        .btn-secondary:hover {
            background: rgba(37, 99, 235, 0.05);
            border-color: var(--accent-blue);
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 8px;
        }

        .action-btns-cell {
            display: flex;
            gap: 6px;
        }
        .btn-action-icon {
            background: transparent;
            border: 1px solid var(--glass-border);
            color: var(--text-muted);
            width: 32px;
            height: 32px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-action-icon:hover {
            border-color: var(--accent-blue);
            color: var(--accent-blue);
            background: rgba(37, 99, 235, 0.05);
        }
        .btn-action-icon svg {
            width: 15px;
            height: 15px;
            stroke: currentColor;
            stroke-width: 2.2;
            fill: none;
        }

        /* CRUD Modal popup styling */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-card {
            background: var(--bg-card);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            width: 100%;
            max-width: 600px;
            padding: 30px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalScaleUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes modalScaleUp {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--glass-border);
            padding-bottom: 12px;
        }
        .modal-title {
            font-size: 18px;
            font-weight: 700;
        }
        .modal-close {
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
        }
        .modal-close:hover {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.08);
        }
        .modal-close svg {
            width: 20px;
            height: 20px;
            stroke: currentColor;
            stroke-width: 2.5;
        }
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
            border-top: 1px solid var(--glass-border);
            padding-top: 16px;
        }

        .modal-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        /* Dynamic items builder for Quotation pricing list */
        .quotation-items-builder {
            margin-top: 15px;
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 16px;
            background: rgba(15, 23, 42, 0.01);
        }
        .quotation-items-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13.5px;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .item-row {
            display: grid;
            grid-template-columns: 3fr 1fr 1.5fr auto;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        .item-total-display {
            text-align: right;
            font-size: 14px;
            font-weight: 700;
            margin-top: 10px;
            color: var(--accent-purple);
        }

        @media (max-width: 1024px) {
            .sidebar { display: none; }
            .main-panel { margin-left: 0; padding: 20px; }
            .analytics-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="toast-container" id="toast-container"></div>

    <!-- 1. AUTH SCREEN -->
    <div class="auth-container" id="auth-screen">
        <div class="auth-card">
            <div class="auth-logo">
                <h2>Service Business ERP</h2>
                <p>Enterprise Suite for Service Management</p>
            </div>

            <!-- Developer Pre-fills Helper -->
            <div style="margin-bottom: 24px;">
                <h5 style="font-size: 12px; color: var(--accent-purple); font-weight:600; margin-bottom: 10px;">Test Credentials for Fast Review</h5>
                <div class="demo-roles-grid">
                    <button class="demo-role-btn" onclick="prefillAuth('ssuperadmin', '123456')">
                        <span class="demo-role-title">Super Admin</span>
                        <span class="demo-role-user">ssuperadmin</span>
                    </button>
                    <button class="demo-role-btn" onclick="prefillAuth('smanager', '123456')">
                        <span class="demo-role-title">Manager</span>
                        <span class="demo-role-user">smanager</span>
                    </button>
                    <button class="demo-role-btn" onclick="prefillAuth('stechnician', '123456')">
                        <span class="demo-role-title">Technician</span>
                        <span class="demo-role-user">stechnician</span>
                    </button>
                    <button class="demo-role-btn" onclick="prefillAuth('scustomercare', '123456')">
                        <span class="demo-role-title">Customer Care</span>
                        <span class="demo-role-user">scustomercare</span>
                    </button>
                    <button class="demo-role-btn" onclick="prefillAuth('saccountant', '123456')">
                        <span class="demo-role-title">Accountant</span>
                        <span class="demo-role-user">saccountant</span>
                    </button>
                </div>
            </div>

            <!-- Login / Registration Form -->
            <div id="login-form-wrapper">
                <form onsubmit="handleLogin(event)">
                    <div class="form-group">
                        <label for="login-username">Username or Email</label>
                        <input type="text" id="login-username" class="form-input" required placeholder="e.g. ssuperadmin">
                    </div>
                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <input type="password" id="login-password" class="form-input" required placeholder="••••••">
                    </div>
                    <button type="submit" class="auth-submit-btn">Login Securely</button>
                </form>
                <div class="auth-toggle-link">
                    Don't have an account? <a href="#" onclick="toggleAuthForms(true)">Register here</a>
                </div>
            </div>

            <div id="register-form-wrapper" style="display: none;">
                <form onsubmit="handleRegister(event)">
                    <div class="form-group">
                        <label for="reg-name">Full Name</label>
                        <input type="text" id="reg-name" class="form-input" required placeholder="e.g. Ramesh Seervi">
                    </div>
                    <div class="form-group">
                        <label for="reg-username">Username</label>
                        <input type="text" id="reg-username" class="form-input" required placeholder="e.g. ramseervi">
                    </div>
                    <div class="form-group">
                        <label for="reg-email">Email Address</label>
                        <input type="email" id="reg-email" class="form-input" required placeholder="e.g. ram@service.com">
                    </div>
                    <div class="form-group">
                        <label for="reg-role">Apply Role</label>
                        <select id="reg-role" class="form-input" style="background: var(--input-bg); color: var(--text-main);" required>
                            <option value="service_technician">Service Technician</option>
                            <option value="service_manager">Service Manager</option>
                            <option value="service_customer_care">Customer Care</option>
                            <option value="service_accountant">Accountant</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="reg-password">Password</label>
                        <input type="password" id="reg-password" class="form-input" required placeholder="••••••">
                    </div>
                    <button type="submit" class="auth-submit-btn">Register Account</button>
                </form>
                <div class="auth-toggle-link">
                    Already have an account? <a href="#" onclick="toggleAuthForms(false)">Login here</a>
                </div>
            </div>

            <!-- OTP Verification (Dynamic step) -->
            <div id="otp-form-wrapper" style="display: none;">
                <div style="text-align: center; margin-bottom: 20px;">
                    <h4 style="font-weight: 600; font-size: 16px;">Verify Registration Email</h4>
                    <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">An OTP verification code was logged (or sent if SMTP is configured). Please enter it below.</p>
                </div>
                <form onsubmit="handleOtpVerify(event)">
                    <div class="form-group">
                        <label for="otp-code">6-Digit OTP Verification Code</label>
                        <input type="text" id="otp-code" class="form-input" required placeholder="123456" maxlength="6">
                    </div>
                    <button type="submit" class="auth-submit-btn">Verify and Complete</button>
                </form>
                <div class="auth-toggle-link">
                    <a href="#" onclick="toggleAuthForms(false)">Cancel and return</a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. MAIN APPLICATION -->
    <div class="app-container" id="app-container">
        <!-- Sidebar Menu -->
        <div class="sidebar">
            <div>
                <div class="brand">
                    <div class="brand-icon">S</div>
                    <span>Service ERP</span>
                </div>
                <ul class="menu-list">
                    <li>
                        <button class="menu-item active" onclick="switchTab('dashboard')" id="menu-dashboard">
                            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                            <span>Dashboard</span>
                        </button>
                    </li>
                    <li class="role-hide role-service_technician role-service_accountant">
                        <button class="menu-item" onclick="switchTab('leads')" id="menu-leads">
                            <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <span>Leads</span>
                        </button>
                    </li>
                    <li class="role-hide role-service_technician role-service_customer_care">
                        <button class="menu-item" onclick="switchTab('quotations')" id="menu-quotations">
                            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            <span>Quotations</span>
                        </button>
                    </li>
                    <li>
                        <button class="menu-item" onclick="switchTab('jobs')" id="menu-jobs">
                            <svg viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></svg>
                            <span>Jobs</span>
                        </button>
                    </li>
                    <li class="role-hide role-service_technician role-service_accountant">
                        <button class="menu-item" onclick="switchTab('amc')" id="menu-amc">
                            <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            <span>AMC Contracts</span>
                        </button>
                    </li>
                    <li class="role-hide role-service_technician role-service_customer_care">
                        <button class="menu-item" onclick="switchTab('invoices')" id="menu-invoices">
                            <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                            <span>Invoices</span>
                        </button>
                    </li>
                    <li class="role-hide role-service_technician role-service_customer_care">
                        <button class="menu-item" onclick="switchTab('payments')" id="menu-payments">
                            <svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                            <span>Payments</span>
                        </button>
                    </li>
                    <li class="role-hide role-service_technician role-service_manager role-service_customer_care role-service_accountant">
                        <button class="menu-item" onclick="switchTab('users')" id="menu-users">
                            <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <span>Users</span>
                        </button>
                    </li>
                    <li class="role-hide role-service_technician role-service_manager role-service_customer_care role-service_accountant">
                        <button class="menu-item" onclick="switchTab('settings')" id="menu-settings">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            <span>SMTP / Settings</span>
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Profile and Theme Switching block -->
            <div class="user-profile-wrapper">
                <div class="user-profile">
                    <div class="user-profile-inner">
                        <div class="avatar" id="user-avatar-initials">UA</div>
                        <div class="user-info">
                            <h4 id="user-display-name">Loading...</h4>
                            <p id="user-display-role">superadmin</p>
                        </div>
                    </div>
                    <button class="theme-toggle-btn" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                    </button>
                </div>
                <button class="logout-btn" onclick="logout()">
                    <span>Log Out</span>
                </button>
            </div>
        </div>

        <!-- Main Workspace -->
        <div class="main-panel">
            <div class="header-section">
                <div class="title-group">
                    <h1 id="page-header-title">Overview Dashboard</h1>
                    <p id="page-header-desc">Welcome back to the Service Business management portal.</p>
                </div>
                <div class="badge-live">
                    <div class="live-dot"></div>
                    <span>Service Server Connected</span>
                </div>
            </div>

            <!-- Tab 1: Dashboard -->
            <div class="tab-panel active" id="tab-dashboard">
                <div class="cards-grid">
                    <div class="stat-card">
                        <div class="card-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg></div>
                        <span class="card-label">Total Leads</span>
                        <span class="card-value" id="kpi-leads">0</span>
                    </div>
                    <div class="stat-card">
                        <div class="card-icon"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg></div>
                        <span class="card-label">Pending Quotes</span>
                        <span class="card-value" id="kpi-quotes">0</span>
                    </div>
                    <div class="stat-card">
                        <div class="card-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></div>
                        <span class="card-label">Active Jobs</span>
                        <span class="card-value" id="kpi-jobs">0</span>
                    </div>
                    <div class="stat-card">
                        <div class="card-icon"><svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path></svg></div>
                        <span class="card-label">Active AMC Contracts</span>
                        <span class="card-value" id="kpi-amc">0</span>
                    </div>
                    <div class="stat-card">
                        <div class="card-icon"><svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></div>
                        <span class="card-label">Receivables Outstanding</span>
                        <span class="card-value" id="kpi-receivables">₹0.00</span>
                    </div>
                </div>

                <div class="analytics-row">
                    <div class="analytic-box">
                        <div class="analytic-header">
                            <h3>Checklist Tasks</h3>
                            <span style="font-size: 11px; color: var(--text-muted);">Self Persistent List</span>
                        </div>
                        <div class="checklist-list" id="dashboard-checklist"></div>
                        <form class="checklist-add-form" onsubmit="addChecklistItem(event)">
                            <input type="text" id="checklist-new-text" class="form-input" style="flex-grow:1; padding: 10px;" required placeholder="Add private todo task...">
                            <button type="submit" class="btn btn-primary" style="padding: 10px 18px;">Add</button>
                        </form>
                    </div>

                    <div class="analytic-box">
                        <div class="analytic-header">
                            <h3>Technician Workload</h3>
                            <span style="font-size: 11px; color: var(--text-muted);">Pending/Scheduled Jobs</span>
                        </div>
                        <div class="tech-load-list" id="dashboard-tech-load">
                            <p style="font-size: 13px; color: var(--text-muted); text-align: center;">No technicians found.</p>
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <div class="analytic-header">
                        <h3>System Activity Logs</h3>
                        <span style="font-size: 11px; color: var(--text-muted);">Recent operations</span>
                    </div>
                    <div class="logs-list" id="dashboard-activity-logs">
                        <p style="font-size: 13px; color: var(--text-muted); text-align: center; padding: 15px;">No logs recorded.</p>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Leads -->
            <div class="tab-panel" id="tab-leads">
                <div class="table-container">
                    <div class="table-header">
                        <span class="table-title">Customer Business Leads</span>
                        <div class="table-actions">
                            <div class="search-box">
                                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <input type="text" id="search-leads" placeholder="Search leads..." oninput="loadLeads()">
                            </div>
                            <select id="filter-leads-status" class="select-filter" onchange="loadLeads()">
                                <option value="">All Statuses</option>
                                <option value="Pending">Pending</option>
                                <option value="Qualified">Qualified</option>
                                <option value="Contacted">Contacted</option>
                                <option value="Proposal">Proposal</option>
                                <option value="Lost">Lost</option>
                                <option value="Won">Won</option>
                            </select>
                            <button class="btn btn-primary" onclick="openLeadModal()">
                                <span>+ New Lead</span>
                            </button>
                        </div>
                    </div>
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Lead Name</th>
                                    <th>Customer Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Source</th>
                                    <th>Status</th>
                                    <th>Date Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-leads"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Quotations -->
            <div class="tab-panel" id="tab-quotations">
                <div class="table-container">
                    <div class="table-header">
                        <span class="table-title">Service Price Quotations</span>
                        <div class="table-actions">
                            <div class="search-box">
                                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <input type="text" id="search-quotations" placeholder="Search quotes..." oninput="loadQuotations()">
                            </div>
                            <select id="filter-quotations-status" class="select-filter" onchange="loadQuotations()">
                                <option value="">All Statuses</option>
                                <option value="Draft">Draft</option>
                                <option value="Sent">Sent</option>
                                <option value="Accepted">Accepted</option>
                                <option value="Declined">Declined</option>
                            </select>
                            <button class="btn btn-primary" onclick="openQuotationModal()">
                                <span>+ Build Quote</span>
                            </button>
                        </div>
                    </div>
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Quote Number</th>
                                    <th>Customer Name</th>
                                    <th>Lead Ref</th>
                                    <th>Total Value</th>
                                    <th>Quote Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-quotations"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Jobs -->
            <div class="tab-panel" id="tab-jobs">
                <div class="table-container">
                    <div class="table-header">
                        <span class="table-title">Job Scheduling & Assignments</span>
                        <div class="table-actions">
                            <div class="search-box">
                                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <input type="text" id="search-jobs" placeholder="Search jobs..." oninput="loadJobs()">
                            </div>
                            <select id="filter-jobs-status" class="select-filter" onchange="loadJobs()">
                                <option value="">All Statuses</option>
                                <option value="Scheduled">Scheduled</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                            <button class="btn btn-primary" id="btn-add-job" onclick="openJobModal()">
                                <span>+ Schedule Job</span>
                            </button>
                        </div>
                    </div>
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Job ID</th>
                                    <th>Job #</th>
                                    <th>Customer Name</th>
                                    <th>Phone</th>
                                    <th>Scheduled Date</th>
                                    <th>Technician Assigned</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-jobs"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 5: AMC Contracts -->
            <div class="tab-panel" id="tab-amc">
                <div class="table-container">
                    <div class="table-header">
                        <span class="table-title">Annual Maintenance Contracts (AMC)</span>
                        <div class="table-actions">
                            <div class="search-box">
                                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <input type="text" id="search-amc" placeholder="Search contracts..." oninput="loadAmcs()">
                            </div>
                            <select id="filter-amc-status" class="select-filter" onchange="loadAmcs()">
                                <option value="">All Statuses</option>
                                <option value="Active">Active</option>
                                <option value="Suspended">Suspended</option>
                                <option value="Expired">Expired</option>
                            </select>
                            <button class="btn btn-primary" onclick="openAmcModal()">
                                <span>+ New AMC</span>
                            </button>
                        </div>
                    </div>
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Contract Number</th>
                                    <th>Customer Name</th>
                                    <th>Phone</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Total Value</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-amc"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 6: Invoices -->
            <div class="tab-panel" id="tab-invoices">
                <div class="table-container">
                    <div class="table-header">
                        <span class="table-title">Billing Invoices</span>
                        <div class="table-actions">
                            <div class="search-box">
                                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <input type="text" id="search-invoices" placeholder="Search invoices..." oninput="loadInvoices()">
                            </div>
                            <select id="filter-invoices-status" class="select-filter" onchange="loadInvoices()">
                                <option value="">All Statuses</option>
                                <option value="Unpaid">Unpaid</option>
                                <option value="Paid">Paid</option>
                                <option value="Partially Paid">Partially Paid</option>
                            </select>
                            <button class="btn btn-primary" onclick="openInvoiceModal()">
                                <span>+ Generate Invoice</span>
                            </button>
                        </div>
                    </div>
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Invoice Number</th>
                                    <th>Customer Name</th>
                                    <th>Reference ID</th>
                                    <th>Total Value</th>
                                    <th>Invoice Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-invoices"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 7: Payments -->
            <div class="tab-panel" id="tab-payments">
                <div class="table-container">
                    <div class="table-header">
                        <span class="table-title">Logged Payments</span>
                        <div class="table-actions">
                            <div class="search-box">
                                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <input type="text" id="search-payments" placeholder="Search transaction ID..." oninput="loadPayments()">
                            </div>
                            <button class="btn btn-primary" onclick="openPaymentModal()">
                                <span>+ Log Payment</span>
                            </button>
                        </div>
                    </div>
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Receipt #</th>
                                    <th>Invoice Ref</th>
                                    <th>Customer Name</th>
                                    <th>Amount Paid</th>
                                    <th>Payment Method</th>
                                    <th>Transaction Ref</th>
                                    <th>Date Logged</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-payments"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 8: Users -->
            <div class="tab-panel" id="tab-users">
                <div class="table-container">
                    <div class="table-header">
                        <span class="table-title">Employee / System Users</span>
                    </div>
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Display Name</th>
                                    <th>Email</th>
                                    <th>Role Privilege</th>
                                    <th>Account Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-body-users"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 9: Settings -->
            <div class="tab-panel" id="tab-settings">
                <div class="analytics-row">
                    <div class="analytic-box">
                        <div class="analytic-header">
                            <h3>Configure SMTP Options</h3>
                        </div>
                        <form onsubmit="handleSaveSmtp(event)" class="dashboard-form" style="display:flex; flex-direction:column; gap:16px;">
                            <div class="form-group">
                                <label for="smtp-enabled">Enable SMTP Server Integration</label>
                                <select id="smtp-enabled" class="form-input" style="background:var(--input-bg); color:var(--text-main);">
                                    <option value="no">Disabled (Local PHP Mailer)</option>
                                    <option value="yes">Enabled (Connect SMTP)</option>
                                </select>
                            </div>
                            <div class="modal-grid-2">
                                <div class="form-group">
                                    <label for="smtp-host">SMTP Host</label>
                                    <input type="text" id="smtp-host" class="form-input" placeholder="smtp.mailtrap.io">
                                </div>
                                <div class="form-group">
                                    <label for="smtp-port">SMTP Port</label>
                                    <input type="number" id="smtp-port" class="form-input" placeholder="587">
                                </div>
                            </div>
                            <div class="modal-grid-2">
                                <div class="form-group">
                                    <label for="smtp-username">SMTP Username</label>
                                    <input type="text" id="smtp-username" class="form-input" placeholder="username_key">
                                </div>
                                <div class="form-group">
                                    <label for="smtp-password">SMTP Password</label>
                                    <input type="password" id="smtp-password" class="form-input" placeholder="••••••">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="smtp-encryption">Encryption Protocol</label>
                                <select id="smtp-encryption" class="form-input" style="background:var(--input-bg); color:var(--text-main);">
                                    <option value="tls">TLS Protocol (Default)</option>
                                    <option value="ssl">SSL Protocol</option>
                                    <option value="none">None</option>
                                </select>
                            </div>
                            <div class="modal-grid-2">
                                <div class="form-group">
                                    <label for="smtp-from-email">Sender Email Address</label>
                                    <input type="email" id="smtp-from-email" class="form-input" placeholder="sender@service-erp.com">
                                </div>
                                <div class="form-group">
                                    <label for="smtp-from-name">Sender Display Name</label>
                                    <input type="text" id="smtp-from-name" class="form-input" placeholder="Service Business ERP">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" style="justify-content:center;">Save SMTP Configuration</button>
                        </form>
                    </div>

                    <div class="analytic-box">
                        <div class="analytic-header">
                            <h3>Test SMTP Mail Connectivity</h3>
                        </div>
                        <form onsubmit="handleTestSmtp(event)" class="dashboard-form" style="display:flex; flex-direction:column; gap:16px;">
                            <div class="form-group">
                                <label for="smtp-test-email">Recipient Test Email Address</label>
                                <input type="email" id="smtp-test-email" class="form-input" required placeholder="e.g. yourname@gmail.com">
                            </div>
                            <button type="submit" class="btn btn-secondary" style="justify-content:center;">Send Test Email Message</button>
                        </form>
                        <p style="margin-top: 15px; font-size: 12px; color: var(--text-muted); line-height: 1.4;">
                            * Checks connection credentials by trying to broadcast an email message using SMTP options. Check the logs dashboard if it fails.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals Layout Structure -->
    <!-- 1. Lead Modal -->
    <div class="modal-overlay" id="modal-lead">
        <div class="modal-card">
            <div class="modal-header">
                <span class="modal-title" id="modal-lead-title">Add Business Lead</span>
                <button class="modal-close" onclick="closeModal('lead')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
            </div>
            <form onsubmit="submitLeadForm(event)">
                <input type="hidden" id="form-lead-id">
                <div class="form-group">
                    <label for="form-lead-name">Lead Summary Inquiry</label>
                    <input type="text" id="form-lead-name" class="form-input" required placeholder="e.g. AC Servicing & Installation Inquiry">
                </div>
                <div class="form-group">
                    <label for="form-lead-customer">Customer / Business Name</label>
                    <input type="text" id="form-lead-customer" class="form-input" required placeholder="e.g. Mr. Sharma">
                </div>
                <div class="modal-grid-2">
                    <div class="form-group">
                        <label for="form-lead-email">Email Address</label>
                        <input type="email" id="form-lead-email" class="form-input" placeholder="e.g. sharma@gmail.com">
                    </div>
                    <div class="form-group">
                        <label for="form-lead-phone">Phone Number</label>
                        <input type="text" id="form-lead-phone" class="form-input" required placeholder="e.g. 9876543210">
                    </div>
                </div>
                <div class="modal-grid-2">
                    <div class="form-group">
                        <label for="form-lead-status">Lead Status</label>
                        <select id="form-lead-status" class="form-input" style="background:var(--input-bg); color:var(--text-main);">
                            <option value="Pending">Pending</option>
                            <option value="Qualified">Qualified</option>
                            <option value="Contacted">Contacted</option>
                            <option value="Proposal">Proposal</option>
                            <option value="Lost">Lost</option>
                            <option value="Won">Won</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="form-lead-source">Lead Source</label>
                        <select id="form-lead-source" class="form-input" style="background:var(--input-bg); color:var(--text-main);">
                            <option value="Direct">Direct Inquiry</option>
                            <option value="Web">Website Inquiry</option>
                            <option value="Referral">Business Referral</option>
                            <option value="Social Media">Social Media</option>
                            <option value="Cold Call">Cold Call</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="form-lead-requirements">Detailed Requirements</label>
                    <textarea id="form-lead-requirements" class="form-input" rows="3" placeholder="Enter requirements detail here..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('lead')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Lead Details</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. Quotation Modal -->
    <div class="modal-overlay" id="modal-quotation">
        <div class="modal-card">
            <div class="modal-header">
                <span class="modal-title" id="modal-quotation-title">Create Service Quotation</span>
                <button class="modal-close" onclick="closeModal('quotation')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
            </div>
            <form onsubmit="submitQuotationForm(event)">
                <input type="hidden" id="form-quote-id">
                <div id="quotation-edit-warning" style="display:none; padding:10px; background:rgba(234, 88, 12, 0.1); border-radius:10px; margin-bottom:15px; font-size:12.5px; color:var(--accent-warning);">
                    * Item rows are locked when updating quotation status. Create a new quotation if pricing items change.
                </div>
                <div class="form-group">
                    <label for="form-quote-customer">Customer Name</label>
                    <input type="text" id="form-quote-customer" class="form-input" required placeholder="e.g. Apex Office Spaces">
                </div>
                <div class="modal-grid-2">
                    <div class="form-group">
                        <label for="form-quote-email">Customer Email</label>
                        <input type="email" id="form-quote-email" class="form-input" placeholder="e.g. apex@office.com">
                    </div>
                    <div class="form-group">
                        <label for="form-quote-phone">Customer Phone</label>
                        <input type="text" id="form-quote-phone" class="form-input" placeholder="e.g. 9311002244">
                    </div>
                </div>
                <div class="modal-grid-2">
                    <div class="form-group">
                        <label for="form-quote-lead">Link Business Lead (Optional)</label>
                        <select id="form-quote-lead" class="form-input" style="background:var(--input-bg); color:var(--text-main);"></select>
                    </div>
                    <div class="form-group">
                        <label for="form-quote-date">Quotation Date</label>
                        <input type="date" id="form-quote-date" class="form-input" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="form-quote-status">Quotation Status</label>
                    <select id="form-quote-status" class="form-input" style="background:var(--input-bg); color:var(--text-main);">
                        <option value="Draft">Draft</option>
                        <option value="Sent">Sent</option>
                        <option value="Accepted">Accepted</option>
                        <option value="Declined">Declined</option>
                    </select>
                </div>

                <!-- Quotation Services Builder -->
                <div class="quotation-items-builder" id="quotation-builder-box">
                    <div class="quotation-items-header">
                        <span>Pricing Services List</span>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="addQuotationItemRow()">+ Add Service Item</button>
                    </div>
                    <div id="quotation-items-rows"></div>
                    <div class="item-total-display">
                        Total Amount: ₹<span id="quotation-total-amount-display">0.00</span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('quotation')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Quotation</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Job Modal -->
    <div class="modal-overlay" id="modal-job">
        <div class="modal-card">
            <div class="modal-header">
                <span class="modal-title" id="modal-job-title">Schedule Service Job</span>
                <button class="modal-close" onclick="closeModal('job')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
            </div>
            <form onsubmit="submitJobForm(event)">
                <input type="hidden" id="form-job-id">
                
                <!-- Admin/Manager full options -->
                <div id="job-admin-section">
                    <div class="form-group">
                        <label for="form-job-customer">Customer Name</label>
                        <input type="text" id="form-job-customer" class="form-input" required placeholder="e.g. Apex Office Spaces">
                    </div>
                    <div class="form-group">
                        <label for="form-job-phone">Phone Number</label>
                        <input type="text" id="form-job-phone" class="form-input" required placeholder="e.g. 9311002244">
                    </div>
                    <div class="form-group">
                        <label for="form-job-address">Service Address</label>
                        <textarea id="form-job-address" class="form-input" rows="2" placeholder="e.g. Plot 12, Sector 63, Noida, UP"></textarea>
                    </div>
                    <div class="modal-grid-2">
                        <div class="form-group">
                            <label for="form-job-tech">Assign Technician</label>
                            <select id="form-job-tech" class="form-input" style="background:var(--input-bg); color:var(--text-main);"></select>
                        </div>
                        <div class="form-group">
                            <label for="form-job-quote">Link Quotation (Optional)</label>
                            <select id="form-job-quote" class="form-input" style="background:var(--input-bg); color:var(--text-main);"></select>
                        </div>
                    </div>
                    <div class="modal-grid-2">
                        <div class="form-group">
                            <label for="form-job-date">Scheduled Date</label>
                            <input type="date" id="form-job-date" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="form-job-priority">Job Priority</label>
                            <select id="form-job-priority" class="form-input" style="background:var(--input-bg); color:var(--text-main);">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="form-job-desc">Job Task Description</label>
                        <textarea id="form-job-desc" class="form-input" rows="3" placeholder="Enter task specifications..."></textarea>
                    </div>
                </div>

                <!-- Shared/Technician fields -->
                <div class="form-group">
                    <label for="form-job-status">Job Execution Status</label>
                    <select id="form-job-status" class="form-input" style="background:var(--input-bg); color:var(--text-main);">
                        <option value="Scheduled">Scheduled</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="form-job-notes">Technician Field Work Notes</label>
                    <textarea id="form-job-notes" class="form-input" rows="3" placeholder="Record services executed, tasks finished..."></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('job')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Scheduled Job</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 4. AMC Modal -->
    <div class="modal-overlay" id="modal-amc">
        <div class="modal-card">
            <div class="modal-header">
                <span class="modal-title" id="modal-amc-title">Register AMC Contract</span>
                <button class="modal-close" onclick="closeModal('amc')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
            </div>
            <form onsubmit="submitAmcForm(event)">
                <input type="hidden" id="form-amc-id">
                <div class="form-group">
                    <label for="form-amc-customer">Customer Name</label>
                    <input type="text" id="form-amc-customer" class="form-input" required placeholder="e.g. Tech Park Sector 62">
                </div>
                <div class="modal-grid-2">
                    <div class="form-group">
                        <label for="form-amc-email">Contact Email</label>
                        <input type="email" id="form-amc-email" class="form-input" placeholder="e.g. admin@techpark62.com">
                    </div>
                    <div class="form-group">
                        <label for="form-amc-phone">Contact Phone</label>
                        <input type="text" id="form-amc-phone" class="form-input" placeholder="e.g. 9560112233">
                    </div>
                </div>
                <div class="modal-grid-2">
                    <div class="form-group">
                        <label for="form-amc-start">Contract Start Date</label>
                        <input type="date" id="form-amc-start" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="form-amc-end">Contract End Date</label>
                        <input type="date" id="form-amc-end" class="form-input" required>
                    </div>
                </div>
                <div class="modal-grid-2">
                    <div class="form-group">
                        <label for="form-amc-amount">Total Contract Value (₹)</label>
                        <input type="number" step="0.01" id="form-amc-amount" class="form-input" required placeholder="e.g. 45000">
                    </div>
                    <div class="form-group">
                        <label for="form-amc-status">Contract Status</label>
                        <select id="form-amc-status" class="form-input" style="background:var(--input-bg); color:var(--text-main);">
                            <option value="Active">Active</option>
                            <option value="Suspended">Suspended</option>
                            <option value="Expired">Expired</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('amc')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Contract</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 5. Invoice Modal -->
    <div class="modal-overlay" id="modal-invoice">
        <div class="modal-card">
            <div class="modal-header">
                <span class="modal-title" id="modal-invoice-title">Generate Billing Invoice</span>
                <button class="modal-close" onclick="closeModal('invoice')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
            </div>
            <form onsubmit="submitInvoiceForm(event)">
                <input type="hidden" id="form-invoice-id">
                <div class="form-group">
                    <label for="form-invoice-customer">Customer Name</label>
                    <input type="text" id="form-invoice-customer" class="form-input" required placeholder="e.g. Apex Office Spaces">
                </div>
                <div class="modal-grid-2">
                    <div class="form-group">
                        <label for="form-invoice-email">Billing Email</label>
                        <input type="email" id="form-invoice-email" class="form-input" placeholder="e.g. apex@office.com">
                    </div>
                    <div class="form-group">
                        <label for="form-invoice-phone">Billing Phone</label>
                        <input type="text" id="form-invoice-phone" class="form-input" placeholder="e.g. 9311002244">
                    </div>
                </div>
                <div class="modal-grid-2">
                    <div class="form-group">
                        <label for="form-invoice-job">Link Completed Job (Optional)</label>
                        <select id="form-invoice-job" class="form-input" style="background:var(--input-bg); color:var(--text-main);" onchange="fillInvoiceFromJob(this.value)"></select>
                    </div>
                    <div class="form-group">
                        <label for="form-invoice-amc">Link AMC Contract (Optional)</label>
                        <select id="form-invoice-amc" class="form-input" style="background:var(--input-bg); color:var(--text-main);" onchange="fillInvoiceFromAmc(this.value)"></select>
                    </div>
                </div>
                <div class="modal-grid-2">
                    <div class="form-group">
                        <label for="form-invoice-date">Invoice Date</label>
                        <input type="date" id="form-invoice-date" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="form-invoice-status">Invoice Status</label>
                        <select id="form-invoice-status" class="form-input" style="background:var(--input-bg); color:var(--text-main);">
                            <option value="Unpaid">Unpaid</option>
                            <option value="Paid">Paid</option>
                            <option value="Partially Paid">Partially Paid</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="form-invoice-amount">Total Invoiced Amount (₹)</label>
                    <input type="number" step="0.01" id="form-invoice-amount" class="form-input" required placeholder="e.g. 15000">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('invoice')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate Invoice</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 6. Payment Modal -->
    <div class="modal-overlay" id="modal-payment">
        <div class="modal-card">
            <div class="modal-header">
                <span class="modal-title" id="modal-payment-title">Record Payment Receipt</span>
                <button class="modal-close" onclick="closeModal('payment')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
            </div>
            <form onsubmit="submitPaymentForm(event)">
                <div class="form-group">
                    <label for="form-payment-invoice">Select Outstanding Invoice</label>
                    <select id="form-payment-invoice" class="form-input" style="background:var(--input-bg); color:var(--text-main);" required></select>
                </div>
                <div class="modal-grid-2">
                    <div class="form-group">
                        <label for="form-payment-amount">Amount Received (₹)</label>
                        <input type="number" step="0.01" id="form-payment-amount" class="form-input" required placeholder="e.g. 5000.00">
                    </div>
                    <div class="form-group">
                        <label for="form-payment-date">Payment Date</label>
                        <input type="date" id="form-payment-date" class="form-input" required>
                    </div>
                </div>
                <div class="modal-grid-2">
                    <div class="form-group">
                        <label for="form-payment-method">Payment Mode</label>
                        <select id="form-payment-method" class="form-input" style="background:var(--input-bg); color:var(--text-main);">
                            <option value="Cash">Cash Mode</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Cheque">Cheque Payment</option>
                            <option value="Online">Online Payment Gateway</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="form-payment-ref">Transaction Reference Key</label>
                        <input type="text" id="form-payment-ref" class="form-input" placeholder="e.g. TXN-110022445">
                    </div>
                </div>
                <div class="form-group">
                    <label for="form-payment-remarks">Payment Notes / Remarks</label>
                    <textarea id="form-payment-remarks" class="form-input" rows="2" placeholder="e.g. Partial advance payment received..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('payment')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Payment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. CLIENT SCRIPT -->
    <script>
        const API_BASE = window.location.origin + '/wp-json/service-management/v1';
        let currentUser = null;
        let checklistData = [];

        // Prefill auth utility helper
        function prefillAuth(username, password) {
            document.getElementById('login-username').value = username;
            document.getElementById('login-password').value = password;
            showToast('Credentials filled. Click login to authenticate.', 'success');
        }

        // Display beautiful alerts
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerText = message;
            container.appendChild(toast);
            
            setTimeout(() => { toast.classList.add('show'); }, 10);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => { toast.remove(); }, 400);
            }, 3500);
        }

        // Toggle Auth forms view
        function toggleAuthForms(showRegister) {
            document.getElementById('login-form-wrapper').style.display = showRegister ? 'none' : 'block';
            document.getElementById('register-form-wrapper').style.display = showRegister ? 'block' : 'none';
            document.getElementById('otp-form-wrapper').style.display = 'none';
        }

        // Core API Fetch wrapper including headers & error routing
        async function apiFetch(path, options = {}) {
            const token = localStorage.getItem('ser_auth_token');
            options.headers = options.headers || {};
            if (token) {
                options.headers['Authorization'] = 'Bearer ' + token;
            }
            if (!(options.body instanceof FormData)) {
                options.headers['Content-Type'] = 'application/json';
            }
            
            try {
                const response = await fetch(API_BASE + path, options);
                const json = await response.json();
                
                if (response.status === 401) {
                    // Session expired
                    logout();
                    throw new Error(json.message || 'Unauthorized action.');
                }
                
                if (!response.ok) {
                    throw new Error(json.message || 'Server returned an error.');
                }
                
                return json;
            } catch (err) {
                showToast(err.message, 'error');
                throw err;
            }
        }

        // Form registration submission
        async function handleRegister(e) {
            e.preventDefault();
            const name = document.getElementById('reg-name').value;
            const username = document.getElementById('reg-username').value;
            const email = document.getElementById('reg-email').value;
            const role = document.getElementById('reg-role').value;
            const password = document.getElementById('reg-password').value;

            try {
                const res = await apiFetch('/auth/register', {
                    method: 'POST',
                    body: JSON.stringify({ name, username, email, role, password })
                });
                if (res.success) {
                    showToast(res.message, 'success');
                    // Store email for OTP step
                    localStorage.setItem('ser_reg_email', email);
                    localStorage.setItem('ser_reg_username', username);
                    // Toggle to OTP
                    document.getElementById('login-form-wrapper').style.display = 'none';
                    document.getElementById('register-form-wrapper').style.display = 'none';
                    document.getElementById('otp-form-wrapper').style.display = 'block';
                }
            } catch(e){}
        }

        // Verify OTP code action
        async function handleOtpVerify(e) {
            e.preventDefault();
            const otp = document.getElementById('otp-code').value;
            const email = localStorage.getItem('ser_reg_email');

            try {
                const res = await apiFetch('/auth/register/verify', {
                    method: 'POST',
                    body: JSON.stringify({ email, otp })
                });
                if (res.success) {
                    showToast('OTP verified. Wait for Admin approval.', 'success');
                    toggleAuthForms(false);
                }
            } catch(e){}
        }

        // Login authentication
        async function handleLogin(e) {
            e.preventDefault();
            const username = document.getElementById('login-username').value;
            const password = document.getElementById('login-password').value;

            try {
                const res = await apiFetch('/auth/login', {
                    method: 'POST',
                    body: JSON.stringify({ username, password })
                });
                if (res.success && res.data) {
                    const token = res.data.token;
                    const user = res.data.user;
                    
                    localStorage.setItem('ser_auth_token', token);
                    localStorage.setItem('ser_current_user', JSON.stringify(user));
                    localStorage.setItem('ser_is_sandbox', 'true');
                    
                    document.documentElement.className = 'is-authenticated';
                    currentUser = user;
                    
                    showToast('Welcome back, ' + user.name, 'success');
                    initDashboard();
                }
            } catch(e){}
        }

        // Theme switching options
        function toggleTheme() {
            const isDark = document.documentElement.classList.contains('dark-mode');
            if (isDark) {
                document.documentElement.classList.remove('dark-mode');
                localStorage.setItem('ser_theme', 'light');
            } else {
                document.documentElement.classList.add('dark-mode');
                localStorage.setItem('ser_theme', 'dark');
            }
        }

        // Terminate and clear auth data
        function logout() {
            localStorage.removeItem('ser_auth_token');
            localStorage.removeItem('ser_current_user');
            localStorage.removeItem('ser_is_sandbox');
            localStorage.removeItem('ser_active_tab');
            document.documentElement.className = 'is-unauthenticated';
            currentUser = null;
            showToast('You have been logged out.', 'info');
        }

        function isTabAllowed(tabId, role) {
            if (role === 'service_technician') {
                return ['dashboard', 'jobs'].includes(tabId);
            }
            if (role === 'service_customer_care') {
                return ['dashboard', 'leads', 'amc'].includes(tabId);
            }
            if (role === 'service_accountant') {
                return ['dashboard', 'quotations', 'invoices', 'payments'].includes(tabId);
            }
            if (role === 'service_manager') {
                return ['dashboard', 'leads', 'quotations', 'jobs', 'amc', 'invoices', 'payments'].includes(tabId);
            }
            return true; // Super Admin / administrator
        }

        // Tab selection routing logic
        function switchTab(tabId) {
            // Check authorization rules on views
            if (currentUser && !isTabAllowed(tabId, currentUser.role)) {
                showToast(`Access Denied: Your role has restricted views.`, 'error');
                return;
            }

            // Hide and show
            document.querySelectorAll('.tab-panel').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.menu-item').forEach(el => el.classList.remove('active'));
            
            const activeTabPanel = document.getElementById('tab-' + tabId);
            if (activeTabPanel) {
                activeTabPanel.classList.add('active');
            }
            const activeMenuBtn = document.getElementById('menu-' + tabId);
            if (activeMenuBtn) {
                activeMenuBtn.classList.add('active');
            }

            // Title headers switching
            const headerTitles = {
                'dashboard': 'Overview Dashboard',
                'leads': 'Customer Business Leads',
                'quotations': 'Quotations & Pricing',
                'jobs': 'Service Scheduling & Field Jobs',
                'amc': 'Annual Maintenance Contracts (AMC)',
                'invoices': 'Billing & Invoices',
                'payments': 'Outstanding Payments Log',
                'users': 'Employee User Profiles',
                'settings': 'SMTP Mail Configuration'
            };
            const headerDescs = {
                'dashboard': 'Key performance metrics, activity logs, and technician load charts.',
                'leads': 'Track customer requests, qualifications, and potential service jobs.',
                'quotations': 'Draft, review, and finalize customer quotations and price lists.',
                'jobs': 'Dispatch technicians, inspect field job reports, and record notes.',
                'amc': 'Register yearly recurring contracts for customer servicing.',
                'invoices': 'Generate and reconcile invoices for completed jobs and AMC agreements.',
                'payments': 'Log payment receipts and monitor receivables outstanding.',
                'users': 'Manage employee accounts, toggle approval statuses, or remove users.',
                'settings': 'Configure secure SMTP protocols to handle registration verification messages.'
            };
            
            document.getElementById('page-header-title').innerText = headerTitles[tabId] || 'Portal';
            document.getElementById('page-header-desc').innerText = headerDescs[tabId] || '';
            
            localStorage.setItem('ser_active_tab', tabId);
            
            // Reload specific tab data
            if (tabId === 'dashboard') loadDashboardData();
            if (tabId === 'leads') loadLeads();
            if (tabId === 'quotations') loadQuotations();
            if (tabId === 'jobs') loadJobs();
            if (tabId === 'amc') loadAmcs();
            if (tabId === 'invoices') loadInvoices();
            if (tabId === 'payments') loadPayments();
            if (tabId === 'users') loadUsers();
            if (tabId === 'settings') loadSmtpSettings();
        }

        // Initialize application layout
        async function initDashboard() {
            const userStr = localStorage.getItem('ser_current_user');
            if (!userStr) {
                logout();
                return;
            }
            currentUser = JSON.parse(userStr);

            // Display initials & information
            document.getElementById('user-display-name').innerText = currentUser.name;
            document.getElementById('user-display-role').innerText = currentUser.role.replace('service_', '').replace('_', ' ');
            
            const initials = currentUser.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
            document.getElementById('user-avatar-initials').innerText = initials;

            // Restrict sidebar items visually based on Role privilege
            document.querySelectorAll('.menu-item').forEach(btn => {
                const li = btn.closest('li');
                if (li) li.style.display = 'block';
            });

            // Hide menu items where the class list contains 'role-' + user role
            document.querySelectorAll('.role-hide').forEach(el => {
                if (el.classList.contains('role-' + currentUser.role)) {
                    el.style.display = 'none';
                }
            });

            // Special UI adjustments for technician
            if (currentUser.role === 'service_technician') {
                document.getElementById('btn-add-job').style.display = 'none'; // Lock Scheduling
                document.getElementById('job-admin-section').style.display = 'none'; // Lock tech details
            } else {
                const addJobBtn = document.getElementById('btn-add-job');
                const jobAdminSect = document.getElementById('job-admin-section');
                if (addJobBtn) addJobBtn.style.display = 'inline-flex';
                if (jobAdminSect) jobAdminSect.style.display = 'block';
            }

            // Restore active tab
            let restoredTab = localStorage.getItem('ser_active_tab') || 'dashboard';
            if (!isTabAllowed(restoredTab, currentUser.role)) {
                restoredTab = 'dashboard';
            }
            switchTab(restoredTab);
            loadChecklist();
        }

        // Fetch Dashboard statistics
        async function loadDashboardData() {
            try {
                const res = await apiFetch('/dashboard');
                if (res.success && res.data) {
                    const cards = res.data.cards;
                    document.getElementById('kpi-leads').innerText = cards.total_leads;
                    document.getElementById('kpi-quotes').innerText = cards.pending_quotes;
                    document.getElementById('kpi-jobs').innerText = cards.active_jobs;
                    document.getElementById('kpi-amc').innerText = cards.active_amc;
                    document.getElementById('kpi-receivables').innerText = '₹' + parseFloat(cards.pending_receivables).toLocaleString('en-IN', { minimumFractionDigits: 2 });

                    // Load Technician workloads
                    const techLoadBox = document.getElementById('dashboard-tech-load');
                    techLoadBox.innerHTML = '';
                    const loads = res.data.analytics.technician_load || [];
                    if (loads.length === 0) {
                        techLoadBox.innerHTML = '<p style="font-size: 13px; color: var(--text-muted); text-align: center; padding: 15px;">No technicians active.</p>';
                    } else {
                        loads.forEach(t => {
                            const pct = Math.min(100, (t.jobs_count / 10) * 100);
                            const item = document.createElement('div');
                            item.className = 'tech-load-item';
                            item.innerHTML = `
                                <div class="tech-load-info">
                                    <span>${t.name}</span>
                                    <span>${t.jobs_count} jobs pending</span>
                                </div>
                                <div class="tech-load-bar-bg">
                                    <div class="tech-load-bar-fg" style="width: ${pct}%;"></div>
                                </div>
                            `;
                            techLoadBox.appendChild(item);
                        });
                    }

                    // Load Logs activity
                    const logsBox = document.getElementById('dashboard-activity-logs');
                    logsBox.innerHTML = '';
                    
                    // Display role-specific mock activity logs
                    const simulatedLogs = {
                        'service_super_admin': [
                            { action: 'SYSTEM INITIALIZATION', detail: 'Seeded Service Business ERP mock tables and user permissions schema.', time: '10 mins ago' },
                            { action: 'SMTP CONNECTION', detail: 'SMTP mail credentials verified successfully with host server.', time: '1 hour ago' }
                        ],
                        'service_manager': [
                            { action: 'JOB DISPATCHED', detail: 'Scheduled new split AC service dispatch job JOB-2026-0001.', time: '5 mins ago' },
                            { action: 'LEAD STATUS UPDATE', detail: 'Lead "AC Installation Inquiry" qualified by Customer Care.', time: '20 mins ago' }
                        ],
                        'service_customer_care': [
                            { action: 'NEW LEAD RECORDED', detail: 'Recorded Direct lead from Mr. Sharma for split AC installation.', time: 'Just Now' },
                            { action: 'AMC REGISTERED', detail: 'Created AMC yearly contract AMC-2026-0001 for Tech Park.', time: '30 mins ago' }
                        ],
                        'service_accountant': [
                            { action: 'PAYMENT LOGGED', detail: 'Log Cash receipt PAY-2026-0001 for Invoice INV-2026-0001.', time: '15 mins ago' },
                            { action: 'INVOICE GENERATED', detail: 'Generated billing invoice INV-2026-0001 for Apex Office Spaces.', time: '45 mins ago' }
                        ],
                        'service_technician': [
                            { action: 'JOB IN PROGRESS', detail: 'Ravi Technician updated job status of JOB-2026-0001.', time: '12 mins ago' },
                            { action: 'WORK NOTE ADDED', detail: 'Notes added: "wiring checked, split AC compressor installed successfully."', time: '12 mins ago' }
                        ]
                    };

                    const roleLogs = simulatedLogs[currentUser.role] || simulatedLogs['service_super_admin'];
                    roleLogs.forEach(log => {
                        const logEl = document.createElement('div');
                        logEl.className = 'log-item';
                        logEl.innerHTML = `
                            <div class="log-item-header">
                                <span>${log.action}</span>
                                <span>${log.time}</span>
                            </div>
                            <p style="font-size: 12.5px; color: var(--text-main);">${log.detail}</p>
                        `;
                        logsBox.appendChild(logEl);
                    });
                }
            } catch(e){}
        }

        // Leads CRUD & Load
        async function loadLeads() {
            const search = document.getElementById('search-leads').value;
            const status = document.getElementById('filter-leads-status').value;
            
            let query = `?search=${encodeURIComponent(search)}`;
            if (status) query += `&status=${encodeURIComponent(status)}`;

            try {
                const res = await apiFetch('/leads' + query);
                const tbody = document.getElementById('table-body-leads');
                tbody.innerHTML = '';
                
                if (res.success && res.data.data) {
                    const data = res.data.data;
                    if (data.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; color:var(--text-muted);">No matching business leads found.</td></tr>`;
                    }
                    data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${row.id}</td>
                            <td><strong>${row.lead_name}</strong></td>
                            <td>${row.customer_name}</td>
                            <td>${row.email || 'N/A'}</td>
                            <td>${row.phone || 'N/A'}</td>
                            <td>${row.source}</td>
                            <td><span class="badge badge-${row.status.toLowerCase()}">${row.status}</span></td>
                            <td>${new Date(row.created_at).toLocaleDateString()}</td>
                            <td class="action-btns-cell">
                                <button class="btn-action-icon" onclick="editLead(${row.id})" title="Edit Lead"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>
                                <button class="btn-action-icon" onclick="deleteLead(${row.id})" title="Delete Lead" style="color:#ef4444;"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch(e){}
        }

        function openLeadModal() {
            document.getElementById('form-lead-id').value = '';
            document.getElementById('form-lead-name').value = '';
            document.getElementById('form-lead-customer').value = '';
            document.getElementById('form-lead-email').value = '';
            document.getElementById('form-lead-phone').value = '';
            document.getElementById('form-lead-status').value = 'Pending';
            document.getElementById('form-lead-source').value = 'Direct';
            document.getElementById('form-lead-requirements').value = '';
            document.getElementById('modal-lead-title').innerText = 'Add Business Lead';
            document.getElementById('modal-lead').style.display = 'flex';
        }

        async function editLead(id) {
            try {
                const res = await apiFetch(`/leads/${id}`);
                if (res.success && res.data) {
                    const row = res.data;
                    document.getElementById('form-lead-id').value = row.id;
                    document.getElementById('form-lead-name').value = row.lead_name;
                    document.getElementById('form-lead-customer').value = row.customer_name;
                    document.getElementById('form-lead-email').value = row.email;
                    document.getElementById('form-lead-phone').value = row.phone;
                    document.getElementById('form-lead-status').value = row.status;
                    document.getElementById('form-lead-source').value = row.source;
                    document.getElementById('form-lead-requirements').value = row.requirements || '';
                    document.getElementById('modal-lead-title').innerText = 'Edit Business Lead';
                    document.getElementById('modal-lead').style.display = 'flex';
                }
            } catch(e){}
        }

        async function submitLeadForm(e) {
            e.preventDefault();
            const id = document.getElementById('form-lead-id').value;
            const data = {
                lead_name: document.getElementById('form-lead-name').value,
                customer_name: document.getElementById('form-lead-customer').value,
                email: document.getElementById('form-lead-email').value,
                phone: document.getElementById('form-lead-phone').value,
                status: document.getElementById('form-lead-status').value,
                source: document.getElementById('form-lead-source').value,
                requirements: document.getElementById('form-lead-requirements').value
            };

            const path = id ? `/leads/${id}` : '/leads';
            const method = id ? 'PUT' : 'POST';

            try {
                const res = await apiFetch(path, {
                    method: method,
                    body: JSON.stringify(data)
                });
                if (res.success) {
                    showToast(res.message, 'success');
                    closeModal('lead');
                    loadLeads();
                }
            } catch(e){}
        }

        async function deleteLead(id) {
            if (!confirm('Are you sure you want to delete this business lead?')) return;
            try {
                const res = await apiFetch(`/leads/${id}`, { method: 'DELETE' });
                if (res.success) {
                    showToast(res.message, 'success');
                    loadLeads();
                }
            } catch(e){}
        }

        // Quotations Builder CRUD
        let quotationItemCount = 0;
        async function loadQuotations() {
            const search = document.getElementById('search-quotations').value;
            const status = document.getElementById('filter-quotations-status').value;
            
            let query = `?search=${encodeURIComponent(search)}`;
            if (status) query += `&status=${encodeURIComponent(status)}`;

            try {
                const res = await apiFetch('/quotations' + query);
                const tbody = document.getElementById('table-body-quotations');
                tbody.innerHTML = '';
                
                if (res.success && res.data.data) {
                    const data = res.data.data;
                    if (data.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; color:var(--text-muted);">No quotations found.</td></tr>`;
                    }
                    data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${row.id}</td>
                            <td><strong>${row.quotation_number}</strong></td>
                            <td>${row.customer_name}</td>
                            <td>${row.lead_name || 'Direct'}</td>
                            <td>₹${parseFloat(row.total_amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                            <td>${new Date(row.quotation_date).toLocaleDateString()}</td>
                            <td><span class="badge badge-${row.status.toLowerCase()}">${row.status}</span></td>
                            <td class="action-btns-cell">
                                <button class="btn-action-icon" onclick="editQuotation(${row.id})" title="Edit Quote Status"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>
                                <button class="btn-action-icon" onclick="deleteQuotation(${row.id})" title="Delete Quote" style="color:#ef4444;"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch(e){}
        }

        async function populateLeadsDropdown(targetId, selectedVal = '') {
            try {
                const res = await apiFetch('/leads');
                const select = document.getElementById(targetId);
                select.innerHTML = '<option value="">-- Direct Quotation --</option>';
                if (res.success && res.data.data) {
                    res.data.data.forEach(l => {
                        const opt = document.createElement('option');
                        opt.value = l.id;
                        opt.innerText = `${l.customer_name} - ${l.lead_name}`;
                        if (l.id == selectedVal) opt.selected = true;
                        select.appendChild(opt);
                    });
                }
            } catch(e){}
        }

        function openQuotationModal() {
            document.getElementById('form-quote-id').value = '';
            document.getElementById('form-quote-customer').value = '';
            document.getElementById('form-quote-email').value = '';
            document.getElementById('form-quote-phone').value = '';
            document.getElementById('form-quote-date').value = new Date().toISOString().split('T')[0];
            document.getElementById('form-quote-status').value = 'Draft';
            
            document.getElementById('quotation-builder-box').style.display = 'block';
            document.getElementById('quotation-edit-warning').style.display = 'none';

            populateLeadsDropdown('form-quote-lead');
            
            const rowsContainer = document.getElementById('quotation-items-rows');
            rowsContainer.innerHTML = '';
            quotationItemCount = 0;
            addQuotationItemRow(); // start with one blank row

            document.getElementById('modal-quotation-title').innerText = 'Build Service Quotation';
            document.getElementById('modal-quotation').style.display = 'flex';
        }

        function addQuotationItemRow(name = '', qty = 1, price = 0) {
            quotationItemCount++;
            const rowsContainer = document.getElementById('quotation-items-rows');
            const row = document.createElement('div');
            row.className = 'item-row';
            row.id = `item-row-${quotationItemCount}`;
            row.innerHTML = `
                <input type="text" class="form-input q-item-name" style="padding:8px;" value="${name}" required placeholder="Service / Repair Description">
                <input type="number" class="form-input q-item-qty" style="padding:8px;" value="${qty}" required min="1" oninput="recalculateQuoteTotal()" placeholder="Qty">
                <input type="number" step="0.01" class="form-input q-item-price" style="padding:8px;" value="${price}" required oninput="recalculateQuoteTotal()" placeholder="Price (₹)">
                <button type="button" class="checklist-delete-btn" onclick="removeQuotationItemRow(${quotationItemCount})" style="padding:6px;"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
            `;
            rowsContainer.appendChild(row);
            recalculateQuoteTotal();
        }

        function removeQuotationItemRow(rowId) {
            const row = document.getElementById(`item-row-${rowId}`);
            if (row) {
                row.remove();
                recalculateQuoteTotal();
            }
        }

        function recalculateQuoteTotal() {
            let total = 0;
            document.querySelectorAll('#quotation-items-rows .item-row').forEach(row => {
                const qty = parseInt(row.querySelector('.q-item-qty').value) || 0;
                const price = parseFloat(row.querySelector('.q-item-price').value) || 0;
                total += qty * price;
            });
            document.getElementById('quotation-total-amount-display').innerText = total.toFixed(2);
        }

        async function editQuotation(id) {
            try {
                const res = await apiFetch(`/quotations/${id}`);
                if (res.success && res.data) {
                    const row = res.data;
                    document.getElementById('form-quote-id').value = row.id;
                    document.getElementById('form-quote-customer').value = row.customer_name;
                    document.getElementById('form-quote-email').value = row.email;
                    document.getElementById('form-quote-phone').value = row.phone;
                    document.getElementById('form-quote-date').value = row.quotation_date;
                    document.getElementById('form-quote-status').value = row.status;
                    
                    populateLeadsDropdown('form-quote-lead', row.lead_id);

                    // Lock items editing on update status (simplifies design for seeded quotation status audits)
                    document.getElementById('quotation-builder-box').style.display = 'none';
                    document.getElementById('quotation-edit-warning').style.display = 'block';

                    document.getElementById('modal-quotation-title').innerText = 'Re-evaluate Quotation Status';
                    document.getElementById('modal-quotation').style.display = 'flex';
                }
            } catch(e){}
        }

        async function submitQuotationForm(e) {
            e.preventDefault();
            const id = document.getElementById('form-quote-id').value;
            
            let data = {};
            if (id) {
                data = {
                    status: document.getElementById('form-quote-status').value,
                    quotation_date: document.getElementById('form-quote-date').value
                };
            } else {
                const items = [];
                document.querySelectorAll('#quotation-items-rows .item-row').forEach(row => {
                    items.push({
                        service_name: row.querySelector('.q-item-name').value,
                        quantity: parseInt(row.querySelector('.q-item-qty').value),
                        price: parseFloat(row.querySelector('.q-item-price').value)
                    });
                });
                data = {
                    customer_name: document.getElementById('form-quote-customer').value,
                    email: document.getElementById('form-quote-email').value,
                    phone: document.getElementById('form-quote-phone').value,
                    lead_id: document.getElementById('form-quote-lead').value ? parseInt(document.getElementById('form-quote-lead').value) : null,
                    quotation_date: document.getElementById('form-quote-date').value,
                    status: document.getElementById('form-quote-status').value,
                    items: items
                };
            }

            const path = id ? `/quotations/${id}` : '/quotations';
            const method = id ? 'PUT' : 'POST';

            try {
                const res = await apiFetch(path, {
                    method: method,
                    body: JSON.stringify(data)
                });
                if (res.success) {
                    showToast(res.message, 'success');
                    closeModal('quotation');
                    loadQuotations();
                }
            } catch(e){}
        }

        async function deleteQuotation(id) {
            if (!confirm('Delete this quotation record?')) return;
            try {
                const res = await apiFetch(`/quotations/${id}`, { method: 'DELETE' });
                if (res.success) {
                    showToast(res.message, 'success');
                    loadQuotations();
                }
            } catch(e){}
        }

        // Jobs CRUD & Scheduling
        async function loadJobs() {
            const search = document.getElementById('search-jobs').value;
            const status = document.getElementById('filter-jobs-status').value;
            
            let query = `?search=${encodeURIComponent(search)}`;
            if (status) query += `&status=${encodeURIComponent(status)}`;

            try {
                const res = await apiFetch('/jobs' + query);
                const tbody = document.getElementById('table-body-jobs');
                tbody.innerHTML = '';
                
                if (res.success && res.data.data) {
                    const data = res.data.data;
                    if (data.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; color:var(--text-muted);">No scheduled service jobs.</td></tr>`;
                    }
                    data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${row.id}</td>
                            <td><strong>${row.job_number}</strong></td>
                            <td>${row.customer_name}</td>
                            <td>${row.phone}</td>
                            <td>${new Date(row.scheduled_date).toLocaleDateString()}</td>
                            <td>${row.technician_name}</td>
                            <td><span class="badge badge-${row.priority.toLowerCase()}">${row.priority}</span></td>
                            <td><span class="badge badge-${row.status.replace(' ', '').toLowerCase()}">${row.status}</span></td>
                            <td class="action-btns-cell">
                                <button class="btn-action-icon" onclick="editJob(${row.id})" title="Update Job"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>
                                ${currentUser && currentUser.role !== 'service_technician' ? `
                                    <button class="btn-action-icon" onclick="deleteJob(${row.id})" title="Delete Job" style="color:#ef4444;"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></button>
                                ` : ''}
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch(e){}
        }

        async function populateTechniciansDropdown(targetId, selectedVal = '') {
            try {
                const res = await apiFetch('/auth/users');
                const select = document.getElementById(targetId);
                select.innerHTML = '<option value="">-- Select Technician --</option>';
                if (res.success && res.data) {
                    // Filter list for registered tech employees
                    res.data.forEach(u => {
                        if (['service_technician', 'service_super_admin', 'service_manager'].includes(u.role)) {
                            const opt = document.createElement('option');
                            opt.value = u.id;
                            opt.innerText = `${u.name} (${u.role.replace('service_','')})`;
                            if (u.id == selectedVal) opt.selected = true;
                            select.appendChild(opt);
                        }
                    });
                }
            } catch(e){}
        }

        async function populateQuotationsDropdown(targetId, selectedVal = '') {
            try {
                const res = await apiFetch('/quotations?status=Accepted');
                const select = document.getElementById(targetId);
                select.innerHTML = '<option value="">-- No Linked Quotation --</option>';
                if (res.success && res.data.data) {
                    res.data.data.forEach(q => {
                        const opt = document.createElement('option');
                        opt.value = q.id;
                        opt.innerText = `${q.quotation_number} - ${q.customer_name} (₹${q.total_amount})`;
                        if (q.id == selectedVal) opt.selected = true;
                        select.appendChild(opt);
                    });
                }
            } catch(e){}
        }

        function openJobModal() {
            document.getElementById('form-job-id').value = '';
            document.getElementById('form-job-customer').value = '';
            document.getElementById('form-job-phone').value = '';
            document.getElementById('form-job-address').value = '';
            document.getElementById('form-job-date').value = new Date().toISOString().split('T')[0];
            document.getElementById('form-job-priority').value = 'Medium';
            document.getElementById('form-job-status').value = 'Scheduled';
            document.getElementById('form-job-desc').value = '';
            document.getElementById('form-job-notes').value = '';

            populateTechniciansDropdown('form-job-tech');
            populateQuotationsDropdown('form-job-quote');

            document.getElementById('modal-job-title').innerText = 'Schedule Service Job';
            document.getElementById('modal-job').style.display = 'flex';
        }

        async function editJob(id) {
            try {
                const res = await apiFetch(`/jobs/${id}`);
                if (res.success && res.data) {
                    const row = res.data;
                    document.getElementById('form-job-id').value = row.id;
                    document.getElementById('form-job-customer').value = row.customer_name;
                    document.getElementById('form-job-phone').value = row.phone;
                    document.getElementById('form-job-address').value = row.address || '';
                    document.getElementById('form-job-date').value = row.scheduled_date;
                    document.getElementById('form-job-priority').value = row.priority;
                    document.getElementById('form-job-status').value = row.status;
                    document.getElementById('form-job-desc').value = row.description || '';
                    document.getElementById('form-job-notes').value = row.work_notes || '';

                    populateTechniciansDropdown('form-job-tech', row.technician_id);
                    populateQuotationsDropdown('form-job-quote', row.quotation_id);

                    document.getElementById('modal-job-title').innerText = 'Update Scheduled Job';
                    document.getElementById('modal-job').style.display = 'flex';
                }
            } catch(e){}
        }

        async function submitJobForm(e) {
            e.preventDefault();
            const id = document.getElementById('form-job-id').value;
            const isTech = currentUser && currentUser.role === 'service_technician';
            
            let data = {};
            if (isTech) {
                // Technicians can only edit work notes & status
                data = {
                    status: document.getElementById('form-job-status').value,
                    work_notes: document.getElementById('form-job-notes').value
                };
            } else {
                data = {
                    customer_name: document.getElementById('form-job-customer').value,
                    phone: document.getElementById('form-job-phone').value,
                    address: document.getElementById('form-job-address').value,
                    technician_id: document.getElementById('form-job-tech').value ? parseInt(document.getElementById('form-job-tech').value) : null,
                    quotation_id: document.getElementById('form-job-quote').value ? parseInt(document.getElementById('form-job-quote').value) : null,
                    scheduled_date: document.getElementById('form-job-date').value,
                    priority: document.getElementById('form-job-priority').value,
                    status: document.getElementById('form-job-status').value,
                    description: document.getElementById('form-job-desc').value,
                    work_notes: document.getElementById('form-job-notes').value
                };
            }

            const path = id ? `/jobs/${id}` : '/jobs';
            const method = id ? 'PUT' : 'POST';

            try {
                const res = await apiFetch(path, {
                    method: method,
                    body: JSON.stringify(data)
                });
                if (res.success) {
                    showToast(res.message, 'success');
                    closeModal('job');
                    loadJobs();
                }
            } catch(e){}
        }

        async function deleteJob(id) {
            if (!confirm('Soft-delete this scheduled job?')) return;
            try {
                const res = await apiFetch(`/jobs/${id}`, { method: 'DELETE' });
                if (res.success) {
                    showToast(res.message, 'success');
                    loadJobs();
                }
            } catch(e){}
        }

        // AMC Contracts CRUD
        async function loadAmcs() {
            const search = document.getElementById('search-amc').value;
            const status = document.getElementById('filter-amc-status').value;
            
            let query = `?search=${encodeURIComponent(search)}`;
            if (status) query += `&status=${encodeURIComponent(status)}`;

            try {
                const res = await apiFetch('/amc' + query);
                const tbody = document.getElementById('table-body-amc');
                tbody.innerHTML = '';
                
                if (res.success && res.data.data) {
                    const data = res.data.data;
                    if (data.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; color:var(--text-muted);">No AMC contracts registered.</td></tr>`;
                    }
                    data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${row.id}</td>
                            <td><strong>${row.contract_number}</strong></td>
                            <td>${row.customer_name}</td>
                            <td>${row.phone || 'N/A'}</td>
                            <td>${new Date(row.start_date).toLocaleDateString()}</td>
                            <td>${new Date(row.end_date).toLocaleDateString()}</td>
                            <td>₹${parseFloat(row.total_amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                            <td><span class="badge badge-${row.status.toLowerCase()}">${row.status}</span></td>
                            <td class="action-btns-cell">
                                <button class="btn-action-icon" onclick="editAmc(${row.id})" title="Edit AMC"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>
                                <button class="btn-action-icon" onclick="deleteAmc(${row.id})" title="Delete AMC" style="color:#ef4444;"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch(e){}
        }

        function openAmcModal() {
            document.getElementById('form-amc-id').value = '';
            document.getElementById('form-amc-customer').value = '';
            document.getElementById('form-amc-email').value = '';
            document.getElementById('form-amc-phone').value = '';
            document.getElementById('form-amc-start').value = new Date().toISOString().split('T')[0];
            
            const nextYear = new Date();
            nextYear.setFullYear(nextYear.getFullYear() + 1);
            document.getElementById('form-amc-end').value = nextYear.toISOString().split('T')[0];
            document.getElementById('form-amc-amount').value = '';
            document.getElementById('form-amc-status').value = 'Active';

            document.getElementById('modal-amc-title').innerText = 'Register AMC Contract';
            document.getElementById('modal-amc').style.display = 'flex';
        }

        async function editAmc(id) {
            try {
                const res = await apiFetch(`/amc/${id}`);
                if (res.success && res.data) {
                    const row = res.data;
                    document.getElementById('form-amc-id').value = row.id;
                    document.getElementById('form-amc-customer').value = row.customer_name;
                    document.getElementById('form-amc-email').value = row.email || '';
                    document.getElementById('form-amc-phone').value = row.phone || '';
                    document.getElementById('form-amc-start').value = row.start_date;
                    document.getElementById('form-amc-end').value = row.end_date;
                    document.getElementById('form-amc-amount').value = row.total_amount;
                    document.getElementById('form-amc-status').value = row.status;

                    document.getElementById('modal-amc-title').innerText = 'Edit AMC Contract';
                    document.getElementById('modal-amc').style.display = 'flex';
                }
            } catch(e){}
        }

        async function submitAmcForm(e) {
            e.preventDefault();
            const id = document.getElementById('form-amc-id').value;
            const data = {
                customer_name: document.getElementById('form-amc-customer').value,
                email: document.getElementById('form-amc-email').value,
                phone: document.getElementById('form-amc-phone').value,
                start_date: document.getElementById('form-amc-start').value,
                end_date: document.getElementById('form-amc-end').value,
                total_amount: parseFloat(document.getElementById('form-amc-amount').value),
                status: document.getElementById('form-amc-status').value
            };

            const path = id ? `/amc/${id}` : '/amc';
            const method = id ? 'PUT' : 'POST';

            try {
                const res = await apiFetch(path, {
                    method: method,
                    body: JSON.stringify(data)
                });
                if (res.success) {
                    showToast(res.message, 'success');
                    closeModal('amc');
                    loadAmcs();
                }
            } catch(e){}
        }

        async function deleteAmc(id) {
            if (!confirm('Remove this AMC agreement record?')) return;
            try {
                const res = await apiFetch(`/amc/${id}`, { method: 'DELETE' });
                if (res.success) {
                    showToast(res.message, 'success');
                    loadAmcs();
                }
            } catch(e){}
        }

        // Invoices CRUD & Auto-Reconciliations
        async function loadInvoices() {
            const search = document.getElementById('search-invoices').value;
            const status = document.getElementById('filter-invoices-status').value;
            
            let query = `?search=${encodeURIComponent(search)}`;
            if (status) query += `&status=${encodeURIComponent(status)}`;

            try {
                const res = await apiFetch('/invoices' + query);
                const tbody = document.getElementById('table-body-invoices');
                tbody.innerHTML = '';
                
                if (res.success && res.data.data) {
                    const data = res.data.data;
                    if (data.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; color:var(--text-muted);">No invoices generated.</td></tr>`;
                    }
                    data.forEach(row => {
                        let refDesc = 'Direct';
                        if (row.job_number) refDesc = `Job: ${row.job_number}`;
                        if (row.contract_number) refDesc = `AMC: ${row.contract_number}`;

                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${row.id}</td>
                            <td><strong>${row.invoice_number}</strong></td>
                            <td>${row.customer_name}</td>
                            <td>${refDesc}</td>
                            <td>₹${parseFloat(row.total_amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                            <td>${new Date(row.invoice_date).toLocaleDateString()}</td>
                            <td><span class="badge badge-${row.status.replace(' ', '').toLowerCase()}">${row.status}</span></td>
                            <td class="action-btns-cell">
                                <button class="btn-action-icon" onclick="editInvoice(${row.id})" title="Edit Invoice"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>
                                <button class="btn-action-icon" onclick="deleteInvoice(${row.id})" title="Delete Invoice" style="color:#ef4444;"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch(e){}
        }

        async function populateJobsDropdown(targetId, selectedVal = '') {
            try {
                const res = await apiFetch('/jobs');
                const select = document.getElementById(targetId);
                select.innerHTML = '<option value="">-- No Linked Job --</option>';
                if (res.success && res.data.data) {
                    res.data.data.forEach(j => {
                        const opt = document.createElement('option');
                        opt.value = j.id;
                        opt.innerText = `${j.job_number} - ${j.customer_name} (${j.status})`;
                        if (j.id == selectedVal) opt.selected = true;
                        select.appendChild(opt);
                    });
                }
            } catch(e){}
        }

        async function populateAmcsDropdown(targetId, selectedVal = '') {
            try {
                const res = await apiFetch('/amc');
                const select = document.getElementById(targetId);
                select.innerHTML = '<option value="">-- No Linked AMC --</option>';
                if (res.success && res.data.data) {
                    res.data.data.forEach(a => {
                        const opt = document.createElement('option');
                        opt.value = a.id;
                        opt.innerText = `${a.contract_number} - ${a.customer_name} (₹${a.total_amount})`;
                        if (a.id == selectedVal) opt.selected = true;
                        select.appendChild(opt);
                    });
                }
            } catch(e){}
        }

        function openInvoiceModal() {
            document.getElementById('form-invoice-id').value = '';
            document.getElementById('form-invoice-customer').value = '';
            document.getElementById('form-invoice-email').value = '';
            document.getElementById('form-invoice-phone').value = '';
            document.getElementById('form-invoice-date').value = new Date().toISOString().split('T')[0];
            document.getElementById('form-invoice-status').value = 'Unpaid';
            document.getElementById('form-invoice-amount').value = '';

            populateJobsDropdown('form-invoice-job');
            populateAmcsDropdown('form-invoice-amc');

            document.getElementById('modal-invoice-title').innerText = 'Generate Billing Invoice';
            document.getElementById('modal-invoice').style.display = 'flex';
        }

        async function fillInvoiceFromJob(jobId) {
            if (!jobId) return;
            try {
                const res = await apiFetch(`/jobs/${jobId}`);
                if (res.success && res.data) {
                    const j = res.data;
                    document.getElementById('form-invoice-customer').value = j.customer_name;
                    document.getElementById('form-invoice-phone').value = j.phone;
                    if (j.quotation_total > 0) {
                        document.getElementById('form-invoice-amount').value = j.quotation_total;
                    }
                }
            } catch(e){}
        }

        async function fillInvoiceFromAmc(amcId) {
            if (!amcId) return;
            try {
                const res = await apiFetch(`/amc/${amcId}`);
                if (res.success && res.data) {
                    const a = res.data;
                    document.getElementById('form-invoice-customer').value = a.customer_name;
                    document.getElementById('form-invoice-phone').value = a.phone || '';
                    document.getElementById('form-invoice-email').value = a.email || '';
                    document.getElementById('form-invoice-amount').value = a.total_amount;
                }
            } catch(e){}
        }

        async function editInvoice(id) {
            try {
                const res = await apiFetch(`/invoices/${id}`);
                if (res.success && res.data) {
                    const row = res.data;
                    document.getElementById('form-invoice-id').value = row.id;
                    document.getElementById('form-invoice-customer').value = row.customer_name;
                    document.getElementById('form-invoice-email').value = row.email || '';
                    document.getElementById('form-invoice-phone').value = row.phone || '';
                    document.getElementById('form-invoice-date').value = row.invoice_date;
                    document.getElementById('form-invoice-status').value = row.status;
                    document.getElementById('form-invoice-amount').value = row.total_amount;

                    populateJobsDropdown('form-invoice-job', row.job_id);
                    populateAmcsDropdown('form-invoice-amc', row.amc_id);

                    document.getElementById('modal-invoice-title').innerText = 'Edit Invoice Details';
                    document.getElementById('modal-invoice').style.display = 'flex';
                }
            } catch(e){}
        }

        async function submitInvoiceForm(e) {
            e.preventDefault();
            const id = document.getElementById('form-invoice-id').value;
            const data = {
                customer_name: document.getElementById('form-invoice-customer').value,
                email: document.getElementById('form-invoice-email').value,
                phone: document.getElementById('form-invoice-phone').value,
                job_id: document.getElementById('form-invoice-job').value ? parseInt(document.getElementById('form-invoice-job').value) : null,
                amc_id: document.getElementById('form-invoice-amc').value ? parseInt(document.getElementById('form-invoice-amc').value) : null,
                invoice_date: document.getElementById('form-invoice-date').value,
                total_amount: parseFloat(document.getElementById('form-invoice-amount').value),
                status: document.getElementById('form-invoice-status').value
            };

            const path = id ? `/invoices/${id}` : '/invoices';
            const method = id ? 'PUT' : 'POST';

            try {
                const res = await apiFetch(path, {
                    method: method,
                    body: JSON.stringify(data)
                });
                if (res.success) {
                    showToast(res.message, 'success');
                    closeModal('invoice');
                    loadInvoices();
                }
            } catch(e){}
        }

        async function deleteInvoice(id) {
            if (!confirm('Permanently delete this invoice? Outstanding transactions will lose ref.')) return;
            try {
                const res = await apiFetch(`/invoices/${id}`, { method: 'DELETE' });
                if (res.success) {
                    showToast(res.message, 'success');
                    loadInvoices();
                }
            } catch(e){}
        }

        // Payments Receipt Logs CRUD
        async function loadPayments() {
            const search = document.getElementById('search-payments').value;
            let query = `?search=${encodeURIComponent(search)}`;

            try {
                const res = await apiFetch('/payments' + query);
                const tbody = document.getElementById('table-body-payments');
                tbody.innerHTML = '';
                
                if (res.success && res.data.data) {
                    const data = res.data.data;
                    if (data.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; color:var(--text-muted);">No payment logs found.</td></tr>`;
                    }
                    data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${row.id}</td>
                            <td><strong>${row.payment_number}</strong></td>
                            <td>INV Ref: ${row.invoice_number}</td>
                            <td>${row.customer_name}</td>
                            <td>₹${parseFloat(row.amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                            <td>${row.payment_method}</td>
                            <td>${row.transaction_reference || 'Cash/Direct'}</td>
                            <td>${new Date(row.payment_date).toLocaleDateString()}</td>
                            <td class="action-btns-cell">
                                <button class="btn-action-icon" onclick="deletePayment(${row.id})" title="Delete Payment Receipt" style="color:#ef4444;"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch(e){}
        }

        async function populateInvoicesDropdown(targetId) {
            try {
                const res = await apiFetch('/invoices');
                const select = document.getElementById(targetId);
                select.innerHTML = '<option value="" disabled selected>-- Select Outstanding Invoice --</option>';
                if (res.success && res.data.data) {
                    res.data.data.forEach(i => {
                        if (i.status !== 'Paid') {
                            const opt = document.createElement('option');
                            opt.value = i.id;
                            opt.innerText = `${i.invoice_number} - ${i.customer_name} (Due: ₹${i.total_amount} | ${i.status})`;
                            select.appendChild(opt);
                        }
                    });
                }
            } catch(e){}
        }

        function openPaymentModal() {
            document.getElementById('form-payment-amount').value = '';
            document.getElementById('form-payment-date').value = new Date().toISOString().split('T')[0];
            document.getElementById('form-payment-method').value = 'Cash';
            document.getElementById('form-payment-ref').value = '';
            document.getElementById('form-payment-remarks').value = '';

            populateInvoicesDropdown('form-payment-invoice');

            document.getElementById('modal-payment').style.display = 'flex';
        }

        async function submitPaymentForm(e) {
            e.preventDefault();
            const data = {
                invoice_id: parseInt(document.getElementById('form-payment-invoice').value),
                amount: parseFloat(document.getElementById('form-payment-amount').value),
                payment_date: document.getElementById('form-payment-date').value,
                payment_method: document.getElementById('form-payment-method').value,
                transaction_reference: document.getElementById('form-payment-ref').value,
                remarks: document.getElementById('form-payment-remarks').value
            };

            try {
                const res = await apiFetch('/payments', {
                    method: 'POST',
                    body: JSON.stringify(data)
                });
                if (res.success) {
                    showToast(res.message, 'success');
                    closeModal('payment');
                    loadPayments();
                }
            } catch(e){}
        }

        async function deletePayment(id) {
            if (!confirm('Revert and delete this payment record? This recalculates Invoice status.')) return;
            try {
                const res = await apiFetch(`/payments/${id}`, { method: 'DELETE' });
                if (res.success) {
                    showToast(res.message, 'success');
                    loadPayments();
                }
            } catch(e){}
        }

        // Users Administration List Control
        async function loadUsers() {
            try {
                const res = await apiFetch('/auth/users');
                const tbody = document.getElementById('table-body-users');
                tbody.innerHTML = '';

                if (res.success && res.data) {
                    res.data.forEach(row => {
                        const isSelf = currentUser && currentUser.id === row.id;
                        const badgeClass = row.status === 'APPROVED' ? 'active' : (row.status === 'BLOCKED' ? 'inactive' : 'pending');
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${row.id}</td>
                            <td><strong>${row.username}</strong></td>
                            <td>${row.name}</td>
                            <td>${row.email}</td>
                            <td><span style="font-weight:600; font-size:12px;">${row.role.replace('service_','').toUpperCase()}</span></td>
                            <td><span class="badge badge-${badgeClass}">${row.status}</span></td>
                            <td class="action-btns-cell">
                                ${isSelf ? '<span style="font-size:11px; color:var(--text-muted); font-weight:600;">Active Account</span>' : `
                                    <button class="btn btn-secondary btn-sm" onclick="changeUserStatus(${row.id}, '${row.status === 'APPROVED' ? 'BLOCKED' : 'APPROVED'}')">
                                        ${row.status === 'APPROVED' ? 'Block' : 'Approve'}
                                    </button>
                                    <button class="btn btn-action-icon" style="color:#ef4444;" onclick="deleteUserAccount(${row.id})" title="Delete User"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></button>
                                `}
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch(e){}
        }

        async function changeUserStatus(userId, status) {
            try {
                const res = await apiFetch('/auth/users/status', {
                    method: 'POST',
                    body: JSON.stringify({ user_id: userId, status })
                });
                if (res.success) {
                    showToast(res.message, 'success');
                    loadUsers();
                }
            } catch(e){}
        }

        async function deleteUserAccount(userId) {
            if (!confirm('Permanently delete this user profile? Action cannot be undone.')) return;
            try {
                const res = await apiFetch(`/auth/users/${userId}`, { method: 'DELETE' });
                if (res.success) {
                    showToast(res.message, 'success');
                    loadUsers();
                }
            } catch(e){}
        }

        // SMTP settings save and test functions
        async function loadSmtpSettings() {
            try {
                const res = await apiFetch('/auth/smtp');
                if (res.success && res.data) {
                    const d = res.data;
                    document.getElementById('smtp-enabled').value = d.smtp_enabled;
                    document.getElementById('smtp-host').value = d.smtp_host;
                    document.getElementById('smtp-port').value = d.smtp_port;
                    document.getElementById('smtp-username').value = d.smtp_username;
                    document.getElementById('smtp-password').value = '******';
                    document.getElementById('smtp-encryption').value = d.smtp_encryption;
                    document.getElementById('smtp-from-email').value = d.smtp_from_email;
                    document.getElementById('smtp-from-name').value = d.smtp_from_name;
                }
            } catch(e){}
        }

        async function handleSaveSmtp(e) {
            e.preventDefault();
            const data = {
                smtp_enabled: document.getElementById('smtp-enabled').value,
                smtp_host: document.getElementById('smtp-host').value,
                smtp_port: document.getElementById('smtp-port').value,
                smtp_username: document.getElementById('smtp-username').value,
                smtp_password: document.getElementById('smtp-password').value,
                smtp_encryption: document.getElementById('smtp-encryption').value,
                smtp_from_email: document.getElementById('smtp-from-email').value,
                smtp_from_name: document.getElementById('smtp-from-name').value
            };

            try {
                const res = await apiFetch('/auth/smtp', {
                    method: 'POST',
                    body: JSON.stringify(data)
                });
                if (res.success) {
                    showToast(res.message, 'success');
                }
            } catch(e){}
        }

        async function handleTestSmtp(e) {
            e.preventDefault();
            const test_email = document.getElementById('smtp-test-email').value;
            try {
                const res = await apiFetch('/auth/smtp/test', {
                    method: 'POST',
                    body: JSON.stringify({ test_email })
                });
                if (res.success) {
                    showToast(res.message, 'success');
                }
            } catch(e){}
        }

        // Interactive persist Checklist widgets
        function loadChecklist() {
            const saved = localStorage.getItem('ser_checklist');
            if (saved) {
                checklistData = JSON.parse(saved);
            } else {
                checklistData = [
                    { id: 1, text: 'Review new customer AC service leads', checked: false },
                    { id: 2, text: 'Assign scheduled high priority field jobs', checked: true },
                    { id: 3, text: 'Verify outstanding invoice receipts reconciliation', checked: false }
                ];
                saveChecklist();
            }
            renderChecklist();
        }

        function saveChecklist() {
            localStorage.setItem('ser_checklist', JSON.stringify(checklistData));
        }

        function renderChecklist() {
            const listEl = document.getElementById('dashboard-checklist');
            listEl.innerHTML = '';
            if (checklistData.length === 0) {
                listEl.innerHTML = '<p style="font-size:12.5px; color:var(--text-muted); padding:10px;">Checklist is empty.</p>';
                return;
            }
            checklistData.forEach(item => {
                const row = document.createElement('div');
                row.className = `checklist-item ${item.checked ? 'checked' : ''}`;
                row.innerHTML = `
                    <label class="checklist-item-left">
                        <input type="checkbox" ${item.checked ? 'checked' : ''} onclick="toggleChecklistItem(${item.id})">
                        <span>${item.text}</span>
                    </label>
                    <button class="checklist-delete-btn" onclick="deleteChecklistItem(${item.id})"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                `;
                listEl.appendChild(row);
            });
        }

        function toggleChecklistItem(id) {
            checklistData = checklistData.map(i => i.id === id ? { ...i, checked: !i.checked } : i);
            saveChecklist();
            renderChecklist();
        }

        function addChecklistItem(e) {
            e.preventDefault();
            const input = document.getElementById('checklist-new-text');
            const text = input.value.trim();
            if (!text) return;
            checklistData.push({
                id: Date.now(),
                text: text,
                checked: false
            });
            input.value = '';
            saveChecklist();
            renderChecklist();
        }

        function deleteChecklistItem(id) {
            checklistData = checklistData.filter(i => i.id !== id);
            saveChecklist();
            renderChecklist();
        }

        function closeModal(type) {
            document.getElementById(`modal-${type}`).style.display = 'none';
        }

        // Global Initialization
        window.addEventListener('DOMContentLoaded', async () => {
            const token = localStorage.getItem('ser_auth_token');
            const userStr = localStorage.getItem('ser_current_user');
            const theme = localStorage.getItem('ser_theme') || 'light';
            
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            } else {
                document.documentElement.classList.remove('dark-mode');
            }

            if (token && userStr) {
                document.documentElement.className = 'is-authenticated';
                try {
                    currentUser = JSON.parse(userStr);
                    await initDashboard();

                    // Optional live auth check with server
                    const testMe = await fetch(API_BASE + '/auth/me', {
                        headers: { 'Authorization': 'Bearer ' + token }
                    });
                    if (testMe.status === 401 || testMe.status === 403) {
                        logout();
                    } else if (testMe.ok) {
                        const json = await testMe.json();
                        if (json.success) {
                            currentUser = json.data;
                            localStorage.setItem('ser_current_user', JSON.stringify(currentUser));
                            document.getElementById('user-display-name').innerText = currentUser.name;
                            document.getElementById('user-display-role').innerText = currentUser.role.replace('service_', '').replace('_', ' ');
                        }
                    }
                } catch(e) {
                    // Do not logout on net offline
                    console.log('Server unreachable, running in offline sandbox Mode');
                }
            } else {
                document.documentElement.className = 'is-unauthenticated';
            }
        });
    </script>
</body>
</html>
