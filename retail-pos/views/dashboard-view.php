<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Retail POS ERP - Premium Control Panel</title>
    <!-- Google Fonts Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome for Premium Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js for beautiful graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            /* Sleek HSL color tokens for dark premium mode */
            --bg-base: hsl(220, 15%, 8%);
            --bg-surface: hsl(220, 15%, 13%);
            --bg-card: hsl(220, 15%, 16%);
            --text-primary: hsl(220, 10%, 95%);
            --text-secondary: hsl(220, 8%, 70%);
            --text-muted: hsl(220, 6%, 50%);
            
            --primary: hsl(263, 85%, 65%);
            --primary-hover: hsl(263, 85%, 72%);
            --primary-glow: hsla(263, 85%, 65%, 0.15);
            
            --accent-success: hsl(142, 72%, 50%);
            --accent-warning: hsl(38, 92%, 50%);
            --accent-danger: hsl(350, 89%, 60%);
            --accent-info: hsl(200, 95%, 60%);
            
            --border-color: hsl(220, 12%, 22%);
            --border-hover: hsl(220, 12%, 30%);
            
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 20px;
            --font-family: 'Outfit', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --transition-smooth: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-base);
            color: var(--text-primary);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* 1. Glassmorphic Backdrop Overlay / Loaders */
        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: var(--bg-base);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: opacity 0.5s ease;
        }
        .loader-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid var(--border-color);
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* 2. Login View Style */
        #login-view {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            background: radial-gradient(circle at 10% 20%, var(--primary-glow) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, hsla(142, 72%, 50%, 0.05) 0%, transparent 40%),
                        var(--bg-base);
        }
        .login-card {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 440px;
            padding: 40px;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent-info));
        }
        .logo-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-icon {
            font-size: 3rem;
            background: linear-gradient(135deg, var(--primary), var(--accent-info));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
        }
        .logo-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .logo-header p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 5px;
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
        }
        .form-input {
            width: 100%;
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            color: var(--text-primary);
            font-family: var(--font-family);
            font-size: 0.95rem;
            transition: var(--transition-smooth);
        }
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }
        .btn {
            width: 100%;
            background-color: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: var(--radius-sm);
            padding: 14px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }
        .btn-outline {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
        }
        .btn-outline:hover {
            background: var(--border-color);
        }
        .btn-secondary {
            background-color: var(--bg-card);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        .btn-secondary:hover {
            background-color: var(--border-color);
        }
        .btn-danger {
            background-color: var(--accent-danger);
        }
        .btn-danger:hover {
            background-color: hsl(350, 89%, 68%);
        }
        .preset-badge-group {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 15px;
            justify-content: center;
        }
        .preset-badge {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 6px 10px;
            font-size: 0.75rem;
            border-radius: var(--radius-sm);
            cursor: pointer;
            color: var(--text-secondary);
            transition: var(--transition-smooth);
        }
        .preset-badge:hover {
            border-color: var(--primary);
            color: var(--text-primary);
            background: var(--primary-glow);
        }

        /* 3. Main Dashboard Shell Style */
        #dashboard-shell {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styling */
        aside.sidebar {
            width: 260px;
            background-color: var(--bg-surface);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            transition: var(--transition-smooth);
        }
        .sidebar-brand {
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border-color);
        }
        .sidebar-brand i {
            font-size: 1.8rem;
            color: var(--primary);
        }
        .sidebar-brand h2 {
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.2px;
        }
        .sidebar-menu {
            list-style: none;
            padding: 15px 12px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            overflow-y: auto;
            flex-grow: 1;
        }
        .sidebar-item a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            border-radius: var(--radius-sm);
            transition: var(--transition-smooth);
        }
        .sidebar-item a:hover, .sidebar-item.active a {
            color: var(--text-primary);
            background-color: var(--bg-card);
        }
        .sidebar-item.active a {
            background-color: var(--primary-glow);
            color: var(--primary);
            box-shadow: inset 4px 0 0 var(--primary);
        }
        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .user-avatar {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .avatar-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--accent-info));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
            color: #fff;
        }
        .user-details h4 {
            font-size: 0.85rem;
            font-weight: 600;
        }
        .user-details span {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: capitalize;
        }
        .logout-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1.1rem;
            transition: var(--transition-smooth);
        }
        .logout-btn:hover {
            color: var(--accent-danger);
        }

        /* Content Area */
        main.content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            max-height: 100vh;
        }
        header.top-nav {
            height: 70px;
            background-color: var(--bg-surface);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
        }
        .page-title h3 {
            font-size: 1.25rem;
            font-weight: 600;
        }
        .scanner-simulator-group {
            display: flex;
            align-items: center;
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 6px 12px;
            width: 320px;
            gap: 8px;
            transition: var(--transition-smooth);
        }
        .scanner-simulator-group:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }
        .scanner-simulator-group i {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        .scanner-simulator-group input {
            background: transparent;
            border: none;
            color: var(--text-primary);
            font-family: var(--font-family);
            font-size: 0.85rem;
            width: 100%;
        }
        .scanner-simulator-group input:focus {
            outline: none;
        }

        /* 4. Tab Panels Layout */
        .tab-panel {
            padding: 30px;
            display: none;
        }
        .tab-panel.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* 5. Metrics Cards Grid */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .metric-card {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            transition: var(--transition-smooth);
        }
        .metric-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }
        .metric-info h5 {
            color: var(--text-secondary);
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .metric-info h3 {
            font-size: 1.8rem;
            font-weight: 700;
        }
        .metric-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-sm);
            background: var(--bg-card);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: var(--primary);
        }
        .metric-card.success .metric-icon { color: var(--accent-success); }
        .metric-card.warning .metric-icon { color: var(--accent-warning); }
        .metric-card.danger .metric-icon { color: var(--accent-danger); }

        /* 6. POS Layout Grid */
        .pos-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 24px;
            height: calc(100vh - 160px);
        }
        .catalog-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 20px;
            overflow: hidden;
        }
        .cart-container {
            display: flex;
            flex-direction: column;
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 20px;
            overflow: hidden;
        }

        .catalog-filter-bar {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .catalog-search {
            flex-grow: 1;
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            color: var(--text-primary);
            font-family: var(--font-family);
        }
        .catalog-search:focus {
            outline: none;
            border-color: var(--primary);
        }
        .catalog-select {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 10px;
            color: var(--text-primary);
            font-family: var(--font-family);
        }

        .catalog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 15px;
            overflow-y: auto;
            flex-grow: 1;
            padding-right: 5px;
        }
        .product-item {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 12px;
            cursor: pointer;
            transition: var(--transition-smooth);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }
        .product-item:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }
        .product-item-image {
            width: 100%;
            height: 90px;
            border-radius: var(--radius-sm);
            background-color: var(--bg-surface);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            overflow: hidden;
        }
        .product-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-item-image i {
            font-size: 2rem;
            color: var(--text-muted);
        }
        .product-item h4 {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 5px;
            line-height: 1.2;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .product-item-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }
        .product-item-price {
            font-weight: 700;
            color: var(--primary);
            font-size: 0.95rem;
        }
        .product-item-stock {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        .product-item-stock.out {
            color: var(--accent-danger);
            font-weight: 600;
        }

        /* Cart Styling */
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .cart-items {
            flex-grow: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding-right: 5px;
            margin-bottom: 15px;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
        }
        .cart-item-info h4 {
            font-size: 0.85rem;
            font-weight: 600;
        }
        .cart-item-info span {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        .cart-item-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .cart-item-qty {
            display: flex;
            align-items: center;
            gap: 8px;
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 4px 8px;
        }
        .cart-item-qty button {
            background: none;
            border: none;
            color: var(--text-primary);
            cursor: pointer;
            font-size: 0.85rem;
        }
        .cart-item-total {
            font-weight: 600;
            width: 70px;
            text-align: right;
            font-size: 0.9rem;
        }
        .cart-item-remove {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            transition: var(--transition-smooth);
        }
        .cart-item-remove:hover {
            color: var(--accent-danger);
        }

        /* Cart Footer Calculations */
        .cart-totals {
            border-top: 1px solid var(--border-color);
            padding-top: 15px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }
        .totals-row.grand {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-primary);
            border-top: 1px dashed var(--border-color);
            padding-top: 8px;
            margin-top: 4px;
        }
        .loyalty-widget {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .loyalty-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }
        .loyalty-slider-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
        }
        .loyalty-slider {
            flex-grow: 1;
            accent-color: var(--primary);
        }

        /* Table Design */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        table.data-table th {
            background-color: var(--bg-card);
            color: var(--text-secondary);
            padding: 16px 20px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
        }
        table.data-table td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.9rem;
            color: var(--text-primary);
        }
        table.data-table tbody tr:hover {
            background-color: var(--bg-card);
        }
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge.badge-success { background-color: hsla(142, 72%, 50%, 0.15); color: var(--accent-success); }
        .badge.badge-warning { background-color: hsla(38, 92%, 50%, 0.15); color: var(--accent-warning); }
        .badge.badge-danger { background-color: hsla(350, 89%, 60%, 0.15); color: var(--accent-danger); }
        .badge.badge-info { background-color: hsla(200, 95%, 60%, 0.15); color: var(--accent-info); }

        /* Actions Bar */
        .actions-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .actions-right {
            display: flex;
            gap: 10px;
        }

        /* 7. Modal Designs */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: var(--transition-smooth);
        }
        .modal.open {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-content {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 600px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
        }
        .modal-close {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.5rem;
            cursor: pointer;
            transition: var(--transition-smooth);
        }
        .modal-close:hover {
            color: var(--accent-danger);
        }

        /* Invoice Modal Thermal Print */
        #invoice-print-content {
            background: white;
            color: black;
            padding: 20px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            width: 80mm;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        #invoice-print-content * {
            color: black !important;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 15px;
        }
        .receipt-divider {
            border-bottom: 1px dashed black;
            margin: 10px 0;
        }
        .receipt-table {
            width: 100%;
            border-collapse: collapse;
        }
        .receipt-table th {
            text-align: left;
            border-bottom: 1px dashed black;
            padding-bottom: 5px;
        }
        .receipt-table td {
            padding: 5px 0;
        }

        /* 8. Notification Toast */
        .toast-container {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 9999;
        }
        .toast {
            background-color: var(--bg-card);
            border-left: 4px solid var(--primary);
            border-radius: var(--radius-sm);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
            color: var(--text-primary);
            padding: 16px 20px;
            min-width: 300px;
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(120%);
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        .toast.show {
            transform: translateX(0);
        }
        .toast.success { border-left-color: var(--accent-success); }
        .toast.error { border-left-color: var(--accent-danger); }
        .toast.warning { border-left-color: var(--accent-warning); }

        /* Form split */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* Analytical Dashboard Section */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
        }
        .chart-card {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 24px;
        }
        .chart-card h4 {
            margin-bottom: 15px;
            font-size: 1rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .chart-container-canvas {
            height: 280px;
            position: relative;
        }

        /* Print Override */
        @media print {
            body * {
                visibility: hidden;
            }
            #invoice-print-content, #invoice-print-content * {
                visibility: visible;
            }
            #invoice-print-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- 1. Page Loader -->
    <div id="page-loader">
        <div class="loader-spinner"></div>
        <h2 style="font-weight: 600; letter-spacing: -0.5px;">Global Retail POS Systems</h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 5px;">Configuring database layers & dashboard services...</p>
    </div>

    <!-- 2. Login View Screen -->
    <div id="login-view" style="display: none;">
        <div class="login-card">
            <div class="logo-header">
                <i class="fa-solid fa-cash-register logo-icon"></i>
                <h1>Welcome Back</h1>
                <p>Global Retail Point of Sale ERP</p>
            </div>
            
            <form id="login-form">
                <div class="form-group">
                    <label class="form-label">Username or Email</label>
                    <input type="text" id="login-username" class="form-input" placeholder="Enter username or email" required>
                </div>
                
                <div class="form-group" id="login-password-group">
                    <label class="form-label">Password</label>
                    <input type="password" id="login-password" class="form-input" placeholder="Enter password (optional if requesting OTP)">
                </div>

                <div class="form-group" id="login-otp-group" style="display: none;">
                    <label class="form-label">6-Digit Email Verification Code (OTP)</label>
                    <input type="text" id="login-otp" class="form-input" placeholder="Enter 6-digit verification code" maxlength="6">
                </div>
                
                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <button type="submit" id="btn-login-submit" class="btn">Login with Password</button>
                    <button type="button" id="btn-send-otp" class="btn btn-outline" style="white-space: nowrap;">Request OTP Code</button>
                </div>
            </form>

            <div style="text-align: center; margin-top: 20px;">
                <p style="color: var(--text-muted); font-size: 0.85rem;">Preset testing roles:</p>
                <div class="preset-badge-group">
                    <div class="preset-badge" onclick="fillPreset('possuperadmin', '123456')">Super Admin</div>
                    <div class="preset-badge" onclick="fillPreset('pos_manager', 'managerpass123')">Manager</div>
                    <div class="preset-badge" onclick="fillPreset('pos_cashier', 'cashierpass123')">Cashier</div>
                    <div class="preset-badge" onclick="fillPreset('pos_inventory', 'inventorypass123')">Inventory</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. SPA Main View Shell -->
    <div id="dashboard-shell" style="display: none;">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <i class="fa-solid fa-shop"></i>
                <h2>Global Retail POS</h2>
            </div>
            
            <ul class="sidebar-menu">
                <li class="sidebar-item active" data-tab="pos-billing">
                    <a href="#"><i class="fa-solid fa-calculator"></i> POS Billing</a>
                </li>
                <li class="sidebar-item" data-tab="dashboard-overview">
                    <a href="#"><i class="fa-solid fa-chart-line"></i> Dashboard KPIs</a>
                </li>
                <li class="sidebar-item" data-tab="products-catalog">
                    <a href="#"><i class="fa-solid fa-box"></i> Products Catalog</a>
                </li>
                <li class="sidebar-item" data-tab="inventory-mgmt">
                    <a href="#"><i class="fa-solid fa-warehouse"></i> Inventory Stock</a>
                </li>
                <li class="sidebar-item" data-tab="purchases-orders">
                    <a href="#"><i class="fa-solid fa-file-invoice"></i> restock Orders (PO)</a>
                </li>
                <li class="sidebar-item" data-tab="expenses-tracker">
                    <a href="#"><i class="fa-solid fa-receipt"></i> Daily Expenses</a>
                </li>
                <li class="sidebar-item" data-tab="customers-loyalty">
                    <a href="#"><i class="fa-solid fa-users"></i> Customers & Loyalty</a>
                </li>
                <li class="sidebar-item" data-tab="suppliers-mgmt">
                    <a href="#"><i class="fa-solid fa-truck-field"></i> Suppliers Directory</a>
                </li>
                <li class="sidebar-item" data-tab="reports-analytics">
                    <a href="#"><i class="fa-solid fa-chart-pie"></i> Business Reports</a>
                </li>
                <li class="sidebar-item admin-only" data-tab="settings-admin" style="display: none;">
                    <a href="#"><i class="fa-solid fa-gears"></i> Admin & Mail Settings</a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <div class="user-avatar">
                    <div class="avatar-circle" id="user-avatar-initials">SA</div>
                    <div class="user-details">
                        <h4 id="user-display-name">POS Operator</h4>
                        <span id="user-display-role">Cashier</span>
                    </div>
                </div>
                <button class="logout-btn" id="btn-logout" title="Sign Out">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </div>
        </aside>

        <!-- Main Content Panel Wrapper -->
        <main class="content">
            <!-- Top Navbar controls -->
            <header class="top-nav">
                <div class="page-title">
                    <h3 id="current-tab-title">POS Billing Checkout</h3>
                </div>

                <!-- Barcode Scanner Simulation Input -->
                <div class="scanner-simulator-group" title="Type SKU or barcode & hit Enter to simulate quick barcode scanning.">
                    <i class="fa-solid fa-barcode"></i>
                    <input type="text" id="barcode-scanner-simulator" placeholder="Scan barcode / type SKU & Enter...">
                </div>
            </header>

            <!-- TAB 1: POS Billing Interface -->
            <div id="panel-pos-billing" class="tab-panel active">
                <div class="pos-grid">
                    <!-- Left: Catalog and Product selectors -->
                    <div class="catalog-container">
                        <div class="catalog-filter-bar">
                            <input type="text" id="pos-catalog-search" class="catalog-search" placeholder="Search product name or SKU...">
                            <select id="pos-category-filter" class="catalog-select">
                                <option value="">All Categories</option>
                            </select>
                            <select id="pos-brand-filter" class="catalog-select">
                                <option value="">All Brands</option>
                            </select>
                        </div>
                        <div class="catalog-grid" id="pos-catalog-grid">
                            <!-- Populated dynamically -->
                        </div>
                    </div>

                    <!-- Right: Shopping Cart and Checkout math -->
                    <div class="cart-container">
                        <div class="cart-header">
                            <h3 style="font-size: 1.1rem; font-weight:600;"><i class="fa-solid fa-cart-shopping"></i> Sales Basket</h3>
                            <button class="btn btn-outline" style="padding: 6px 12px; font-size: 0.8rem;" onclick="clearCart()">Clear Basket</button>
                        </div>
                        
                        <!-- Customer Attachment Widget -->
                        <div class="loyalty-widget">
                            <div class="loyalty-row">
                                <span style="font-size: 0.85rem; font-weight:600; color: var(--text-secondary);">Select Customer</span>
                                <button class="btn" style="padding: 4px 8px; font-size:0.75rem; width:auto;" onclick="openAddCustomerModal()"><i class="fa-solid fa-plus"></i> New</button>
                            </div>
                            <select id="cart-customer-select" class="form-input" style="padding: 8px; font-size: 0.85rem; margin-top:5px;">
                                <option value="">Walk-in Customer (No points)</option>
                            </select>
                            
                            <div id="cart-loyalty-details" style="display: none; margin-top: 10px; border-top: 1px solid var(--border-color); padding-top: 8px;">
                                <div class="loyalty-row">
                                    <span style="font-size:0.8rem; color:var(--text-secondary);">Loyalty Balance:</span>
                                    <span style="font-weight: 700; color: var(--accent-success);" id="cart-customer-points">0 Points</span>
                                </div>
                                <div class="loyalty-slider-container">
                                    <input type="range" id="loyalty-redeem-slider" class="loyalty-slider" min="0" max="0" value="0">
                                    <span style="font-size: 0.8rem; font-weight:600;" id="loyalty-redeem-display">Redeem: 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Cart listing -->
                        <div class="cart-items" id="cart-items-list">
                            <!-- Populated dynamically -->
                        </div>

                        <!-- Computations -->
                        <div class="cart-totals">
                            <div class="totals-row">
                                <span>Subtotal (Excl. GST)</span>
                                <span id="cart-subtotal">₹0.00</span>
                            </div>
                            <div class="totals-row">
                                <span>Tax Amount (GST 18%)</span>
                                <span id="cart-gst">₹0.00</span>
                            </div>
                            <div class="totals-row">
                                <span>Redeemable Discounts</span>
                                <span id="cart-discount" style="color: var(--accent-danger);">-₹0.00</span>
                            </div>
                            <div class="totals-row">
                                <label style="font-size: 0.85rem;">Payment Method</label>
                                <select id="cart-payment-method" class="form-input" style="padding: 6px 12px; font-size:0.85rem; width:150px;">
                                    <option value="Cash">Cash</option>
                                    <option value="UPI">UPI / QR</option>
                                    <option value="Card">Card Swipe</option>
                                </select>
                            </div>
                            <div class="totals-row grand">
                                <span>Total Payable</span>
                                <span id="cart-grand-total">₹0.00</span>
                            </div>

                            <button class="btn" id="btn-checkout" style="margin-top: 15px; padding: 16px;" onclick="processCheckout()">
                                <i class="fa-solid fa-print"></i> Book & Print Receipt
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Dashboard Overview KPIs -->
            <div id="panel-dashboard-overview" class="tab-panel">
                <div class="metrics-grid" id="dashboard-metrics-container">
                    <!-- Populated dynamically -->
                </div>

                <div class="charts-grid">
                    <div class="chart-card">
                        <h4>Monthly Revenue Trends</h4>
                        <div class="chart-container-canvas">
                            <canvas id="salesTrendsChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-card">
                        <h4>Top 5 Products Sold</h4>
                        <div class="chart-container-canvas">
                            <canvas id="topProductsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: Products Catalog -->
            <div id="panel-products-catalog" class="tab-panel">
                <div class="actions-header">
                    <div class="actions-left">
                        <input type="text" id="product-search-input" class="form-input" placeholder="Search catalog..." style="width:250px;">
                    </div>
                    <div class="actions-right">
                        <button class="btn btn-outline" onclick="openCategoriesModal()"><i class="fa-solid fa-tags"></i> Categories</button>
                        <button class="btn btn-outline" onclick="openBrandsModal()"><i class="fa-solid fa-copyright"></i> Brands</button>
                        <button class="btn" onclick="openAddProductModal()"><i class="fa-solid fa-plus"></i> Add Product</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Barcode</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Unit Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="products-table-body">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 4: Inventory Stock -->
            <div id="panel-inventory-mgmt" class="tab-panel">
                <div class="metrics-grid">
                    <div class="metric-card warning" onclick="loadLowStock()">
                        <div class="metric-info">
                            <h5 style="margin-bottom: 2px;">Low Stock warnings</h5>
                            <h3 id="inv-kpi-low-stock">0</h3>
                            <p style="font-size: 0.75rem; color:var(--text-secondary); margin-top:5px;">Show Warning Checklist</p>
                        </div>
                        <div class="metric-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    </div>
                    <div class="metric-card danger" onclick="loadOutOfStock()">
                        <div class="metric-info">
                            <h5 style="margin-bottom: 2px;">Out of Stock</h5>
                            <h3 id="inv-kpi-out-of-stock">0</h3>
                            <p style="font-size: 0.75rem; color:var(--text-secondary); margin-top:5px;">Show Empty Catalog list</p>
                        </div>
                        <div class="metric-icon"><i class="fa-solid fa-circle-xmark"></i></div>
                    </div>
                    <div class="metric-card success" onclick="loadAllInventory()">
                        <div class="metric-info">
                            <h5>Tracked Inventory items</h5>
                            <h3 id="inv-kpi-total">0</h3>
                        </div>
                        <div class="metric-icon"><i class="fa-solid fa-warehouse"></i></div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Product Name</th>
                                <th>Available Stock</th>
                                <th>Damaged Stock</th>
                                <th>Min Safety Level</th>
                                <th>Reorder Level</th>
                                <th>Status Alert</th>
                                <th>Adjustment</th>
                            </tr>
                        </thead>
                        <tbody id="inventory-table-body">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 5: Restock Orders (PO) -->
            <div id="panel-purchases-orders" class="tab-panel">
                <div class="actions-header">
                    <h2>Purchase Orders Catalog</h2>
                    <button class="btn" onclick="openAddPurchaseModal()"><i class="fa-solid fa-file-invoice"></i> Book Supplier restock (PO)</button>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Supplier Name</th>
                                <th>Restocked Product</th>
                                <th>Qty</th>
                                <th>Unit Cost</th>
                                <th>GST Tax</th>
                                <th>Grand Total</th>
                                <th>Purchase Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="purchases-table-body">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 6: Expenses Tracker -->
            <div id="panel-expenses-tracker" class="tab-panel">
                <div class="actions-header">
                    <h2>Operating Expense Sheets</h2>
                    <button class="btn" onclick="openAddExpenseModal()"><i class="fa-solid fa-plus"></i> Record Daily Expense</button>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Expense Type</th>
                                <th>Amount</th>
                                <th>Details / Remarks</th>
                                <th>Logged At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="expenses-table-body">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 7: Customers & Loyalty -->
            <div id="panel-customers-loyalty" class="tab-panel">
                <div class="actions-header">
                    <h2>Loyalty & Customers registry</h2>
                    <button class="btn" onclick="openAddCustomerModal()"><i class="fa-solid fa-user-plus"></i> Register Customer</button>
                </div>

                <div class="table-responsive" style="margin-bottom: 30px;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Customer Name</th>
                                <th>Mobile Number</th>
                                <th>Email</th>
                                <th>GSTIN</th>
                                <th>Loyalty Balance</th>
                                <th>Total Purchases</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="customers-table-body">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>

                <h2>Loyalty Points Activity Ledger</h2>
                <div class="table-responsive" style="margin-top: 15px;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date / Time</th>
                                <th>Code</th>
                                <th>Customer Name</th>
                                <th>Type</th>
                                <th>Points change</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="loyalty-ledger-body">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 8: Suppliers -->
            <div id="panel-suppliers-mgmt" class="tab-panel">
                <div class="actions-header">
                    <h2>Suppliers registries</h2>
                    <button class="btn" onclick="openAddSupplierModal()"><i class="fa-solid fa-plus"></i> Add New Supplier</button>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Supplier Name</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>GSTIN</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="suppliers-table-body">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 9: Business Reports -->
            <div id="panel-reports-analytics" class="tab-panel">
                <div class="actions-header">
                    <h2>Business Performance Reports</h2>
                </div>
                
                <div class="charts-grid" style="margin-bottom: 30px;">
                    <!-- Monthly P&L Chart -->
                    <div class="chart-card">
                        <h4>Profit & Loss monthly Statement</h4>
                        <div class="chart-container-canvas">
                            <canvas id="profitLossChart"></canvas>
                        </div>
                    </div>

                    <!-- GST Report Table -->
                    <div class="chart-card">
                        <h4>GST Tax Return Filings Summary</h4>
                        <div class="table-responsive" style="margin-top: 10px; border:none; background:transparent;">
                            <table class="data-table" style="font-size:0.8rem;">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Taxable Base</th>
                                        <th>CGST (9%)</th>
                                        <th>SGST (9%)</th>
                                        <th>Total Tax</th>
                                    </tr>
                                </thead>
                                <tbody id="reports-gst-body">
                                    <!-- Dynamic data -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 10: Admin Settings -->
            <div id="panel-settings-admin" class="tab-panel">
                <div class="charts-grid">
                    <!-- SMTP Setup -->
                    <div class="chart-card">
                        <h4 style="border-bottom: 1px solid var(--border-color); padding-bottom: 10px; margin-bottom: 20px;">SMTP Email Configuration Settings</h4>
                        <form id="smtp-settings-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">SMTP Enabled</label>
                                    <select id="smtp-enabled" class="form-input">
                                        <option value="no">No (Use WordPress PHP Mail)</option>
                                        <option value="yes">Yes (Use External SMTP)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Encryption type</label>
                                    <select id="smtp-encryption" class="form-input">
                                        <option value="tls">TLS (Port 587)</option>
                                        <option value="ssl">SSL (Port 465)</option>
                                        <option value="none">None</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">SMTP Host</label>
                                    <input type="text" id="smtp-host" class="form-input" placeholder="smtp.mailtrap.io">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">SMTP Port</label>
                                    <input type="text" id="smtp-port" class="form-input" placeholder="587">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Username</label>
                                    <input type="text" id="smtp-username" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Password</label>
                                    <input type="password" id="smtp-password" class="form-input">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Sender Email address</label>
                                    <input type="email" id="smtp-from-email" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Sender Name</label>
                                    <input type="text" id="smtp-from-name" class="form-input">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email OTP Subject</label>
                                <input type="text" id="email-subject" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email OTP Message Template</label>
                                <textarea id="email-template" class="form-input" rows="4"></textarea>
                            </div>
                            <button type="submit" class="btn">Save SMTP Configurations</button>
                        </form>
                        
                        <div style="border-top: 1px solid var(--border-color); padding-top: 20px; margin-top: 20px;">
                            <h5>Test SMTP Mailer Dispatcher</h5>
                            <div class="form-row" style="margin-top:10px;">
                                <input type="email" id="smtp-test-email" class="form-input" placeholder="Enter recipient email address">
                                <button class="btn btn-outline" onclick="triggerTestEmail()">Send Test Mail</button>
                            </div>
                        </div>
                    </div>

                    <!-- User Approvals -->
                    <div class="chart-card">
                        <h4 style="border-bottom: 1px solid var(--border-color); padding-bottom: 10px; margin-bottom: 20px;">User accounts & status controls</h4>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Operator</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="settings-users-body">
                                    <!-- Dynamic user rows -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- TOAST NOTIFICATIONS CONTAINERS -->
    <div class="toast-container" id="toast-container"></div>

    <!-- MODAL 1: ADD PRODUCT -->
    <div class="modal" id="modal-product">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="product-modal-title">Add New Product</h3>
                <button class="modal-close" onclick="closeModal('modal-product')">&times;</button>
            </div>
            <form id="product-form">
                <input type="hidden" id="product-id">
                <div class="form-group">
                    <label class="form-label">Product Name *</label>
                    <input type="text" id="prod-name" class="form-input" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">SKU (Auto-Generated if blank)</label>
                        <input type="text" id="prod-sku" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Barcode (Auto-Generated if blank)</label>
                        <input type="text" id="prod-barcode" class="form-input">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select id="prod-category" class="form-input"></select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Brand</label>
                        <select id="prod-brand" class="form-input"></select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Purchase Price (Cost) *</label>
                        <input type="number" step="0.01" id="prod-cost" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Selling Price (Inc. GST) *</label>
                        <input type="number" step="0.01" id="prod-retail" class="form-input" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">GST Tax Percentage (%)</label>
                        <input type="number" step="0.01" id="prod-gst" class="form-input" value="18.00">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Initial Quantity In Stock</label>
                        <input type="number" step="0.01" id="prod-stock" class="form-input" value="0.00">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Minimum Stock Level alert</label>
                        <input type="number" step="0.01" id="prod-min-stock" class="form-input" value="5.00">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Measurement Unit</label>
                        <input type="text" id="prod-unit" class="form-input" value="PCS">
                    </div>
                </div>
                <button type="submit" class="btn">Save Product</button>
            </form>
        </div>
    </div>

    <!-- MODAL 2: ADD CUSTOMER -->
    <div class="modal" id="modal-customer">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Register Customer</h3>
                <button class="modal-close" onclick="closeModal('modal-customer')">&times;</button>
            </div>
            <form id="customer-form">
                <div class="form-group">
                    <label class="form-label">Customer Name *</label>
                    <input type="text" id="cust-name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Mobile Number *</label>
                    <input type="text" id="cust-mobile" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" id="cust-email" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">GST Number (Optional)</label>
                    <input type="text" id="cust-gst" class="form-input" placeholder="24AAAAA1111A1Z1">
                </div>
                <div class="form-group">
                    <label class="form-label">Home Address</label>
                    <textarea id="cust-address" class="form-input" rows="3"></textarea>
                </div>
                <button type="submit" class="btn">Register Customer</button>
            </form>
        </div>
    </div>

    <!-- MODAL 3: INVENTORY ADJUSTMENT -->
    <div class="modal" id="modal-adjust">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Manual Inventory Stock Adjustment</h3>
                <button class="modal-close" onclick="closeModal('modal-adjust')">&times;</button>
            </div>
            <form id="adjust-form">
                <input type="hidden" id="adjust-product-id">
                <div class="form-group">
                    <label class="form-label">Product Name</label>
                    <input type="text" id="adjust-product-name" class="form-input" readonly>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Actual Stock Quantity *</label>
                        <input type="number" step="0.01" id="adjust-stock" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Damaged Stock Quantity</label>
                        <input type="number" step="0.01" id="adjust-damaged" class="form-input" value="0.00">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Remarks / Audit Note *</label>
                    <input type="text" id="adjust-remarks" class="form-input" placeholder="e.g. Stocktake audit check" required>
                </div>
                <button type="submit" class="btn">Commit Stock Adjustment</button>
            </form>
        </div>
    </div>

    <!-- MODAL 4: BOOK RESTOCK (PO) -->
    <div class="modal" id="modal-purchase">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Book Restock Purchase Order</h3>
                <button class="modal-close" onclick="closeModal('modal-purchase')">&times;</button>
            </div>
            <form id="purchase-form">
                <div class="form-group">
                    <label class="form-label">Supplier Directory *</label>
                    <select id="po-supplier" class="form-input" required></select>
                </div>
                <div class="form-group">
                    <label class="form-label">Select Product to Restock *</label>
                    <select id="po-product" class="form-input" required></select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Purchase Quantity *</label>
                        <input type="number" step="0.01" id="po-qty" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Unit Purchase Cost (Excl. GST) *</label>
                        <input type="number" step="0.01" id="po-cost" class="form-input" required>
                    </div>
                </div>
                <button type="submit" class="btn">Book Purchase Order & Restock</button>
            </form>
        </div>
    </div>

    <!-- MODAL 5: EXPENSE RECORD -->
    <div class="modal" id="modal-expense">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Log Petty Cash / Operating Expense</h3>
                <button class="modal-close" onclick="closeModal('modal-expense')">&times;</button>
            </div>
            <form id="expense-form">
                <div class="form-group">
                    <label class="form-label">Expense Category / Type *</label>
                    <input type="text" id="exp-type" class="form-input" placeholder="e.g. Electricity, Stationery, Rent" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Expense Amount (₹) *</label>
                        <input type="number" step="0.01" id="exp-amount" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Expense Date *</label>
                        <input type="date" id="exp-date" class="form-input" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Details / Payment Description</label>
                    <textarea id="exp-details" class="form-input" rows="3"></textarea>
                </div>
                <button type="submit" class="btn">Record Expense</button>
            </form>
        </div>
    </div>

    <!-- MODAL 6: ADD SUPPLIER -->
    <div class="modal" id="modal-supplier">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Supplier Profile</h3>
                <button class="modal-close" onclick="closeModal('modal-supplier')">&times;</button>
            </div>
            <form id="supplier-form">
                <div class="form-group">
                    <label class="form-label">Supplier Name *</label>
                    <input type="text" id="sup-name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Mobile Number *</label>
                    <input type="text" id="sup-mobile" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" id="sup-email" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">GSTIN Number</label>
                    <input type="text" id="sup-gst" class="form-input" placeholder="e.g. 24BBBBB2222B2Z2">
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea id="sup-address" class="form-input" rows="3"></textarea>
                </div>
                <button type="submit" class="btn">Create Supplier Profile</button>
            </form>
        </div>
    </div>

    <!-- MODAL 7: THERMAL RECEIPT PRINT PREVIEW -->
    <div class="modal" id="modal-invoice-print">
        <div class="modal-content" style="background:#f3f4f6; max-width: 90mm; padding: 20px;">
            <div class="modal-header" style="border:none; margin-bottom: 10px;">
                <h4 style="color:black;">Thermal Receipt Print</h4>
                <button class="modal-close" onclick="closeModal('modal-invoice-print')">&times;</button>
            </div>
            
            <div id="invoice-print-content">
                <!-- Receipt details populated on checkout -->
            </div>

            <div style="display:flex; gap:10px; margin-top:20px;">
                <button class="btn" onclick="window.print()">Print Invoice (PDF)</button>
                <button class="btn btn-secondary" onclick="closeModal('modal-invoice-print')">Close</button>
            </div>
        </div>
    </div>

    <!-- MODAL 8: BARCODE GENERATOR POPUP -->
    <div class="modal" id="modal-barcode-print">
        <div class="modal-content" style="max-width: 400px; text-align:center;">
            <div class="modal-header">
                <h3>Product Barcode Preview</h3>
                <button class="modal-close" onclick="closeModal('modal-barcode-print')">&times;</button>
            </div>
            
            <div id="barcode-vector-preview-container" style="background: white; padding: 25px; border-radius: var(--radius-sm); border:1px solid var(--border-color); display:flex; align-items:center; justify-content:center; margin-bottom: 20px;">
                <!-- Vector SVG barcode -->
            </div>

            <button class="btn" id="btn-print-barcode-preview">Print / Download SVG Barcode</button>
        </div>
    </div>

    <!-- MODAL 9: MANAGE CATEGORIES -->
    <div class="modal" id="modal-categories">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3>Manage Product Categories</h3>
                <button class="modal-close" onclick="closeModal('modal-categories')">&times;</button>
            </div>
            
            <form id="add-category-form" style="display:flex; gap:10px; margin-bottom: 20px;">
                <input type="text" id="new-category-name" class="form-input" placeholder="Category Name" required>
                <button type="submit" class="btn" style="width:auto; white-space:nowrap;">Add Category</button>
            </form>

            <div class="table-responsive" style="max-height: 300px; overflow-y:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="modal-categories-list-body">
                        <!-- Dynamic rows -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL 10: MANAGE BRANDS -->
    <div class="modal" id="modal-brands">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3>Manage Product Brands</h3>
                <button class="modal-close" onclick="closeModal('modal-brands')">&times;</button>
            </div>
            
            <form id="add-brand-form" style="display:flex; gap:10px; margin-bottom: 20px;">
                <input type="text" id="new-brand-name" class="form-input" placeholder="Brand Name" required>
                <button type="submit" class="btn" style="width:auto; white-space:nowrap;">Add Brand</button>
            </form>

            <div class="table-responsive" style="max-height: 300px; overflow-y:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Brand Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="modal-brands-list-body">
                        <!-- Dynamic rows -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC CLIENT SIDE -->
    <script>
        // Endpoint constants
        const API_NAMESPACE = '/wp-json/retail-pos/v1';

        // State Store
        let token = localStorage.getItem('pos_token') || '';
        let user = JSON.parse(localStorage.getItem('pos_user')) || null;
        let cart = [];
        let selectedCustomerId = null;
        let selectedCustomerObj = null;
        let redeemedDiscount = 0.00;

        // Chart reference pointers
        let salesTrendsChart = null;
        let topProductsChart = null;
        let profitLossChart = null;

        // Initialize App
        window.addEventListener('DOMContentLoaded', () => {
            initApp();
        });

        function initApp() {
            if (token && user) {
                // Verify session
                apiFetch('/auth/me', 'GET')
                    .then(res => {
                        if (res.success) {
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

        // Display panels switching logic
        function showLogin() {
            document.getElementById('page-loader').style.display = 'none';
            document.getElementById('login-view').style.display = 'flex';
            document.getElementById('dashboard-shell').style.display = 'none';
        }

        function showShell() {
            document.getElementById('page-loader').style.display = 'none';
            document.getElementById('login-view').style.display = 'none';
            document.getElementById('dashboard-shell').style.display = 'flex';
            
            // Set User Details
            document.getElementById('user-display-name').innerText = user.name;
            document.getElementById('user-display-role').innerText = user.role.replace('pos_', '').replace('_', ' ');
            document.getElementById('user-avatar-initials').innerText = user.name.substring(0, 2).toUpperCase();

            // Set Admin privileges
            if (user.role === 'pos_super_admin' || user.role === 'administrator') {
                const adminMenus = document.querySelectorAll('.admin-only');
                adminMenus.forEach(el => el.style.display = 'block');
            }

            // Setup Navigation tabs
            setupTabs();
            // Start in POS Billing
            switchTab('pos-billing');
            
            // Setup barcode scanners
            setupBarcodeScannerSimulator();
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

            // Set navbar title
            document.getElementById('current-tab-title').innerText = targetItem ? targetItem.innerText : 'POS Control Panel';

            // Trigger tab specific loading
            switch(tabName) {
                case 'pos-billing':
                    loadPosCatalog();
                    loadCustomersList();
                    break;
                case 'dashboard-overview':
                    loadDashboardStats();
                    break;
                case 'products-catalog':
                    loadProductsCatalog();
                    break;
                case 'inventory-mgmt':
                    loadAllInventory();
                    break;
                case 'purchases-orders':
                    loadPurchaseOrders();
                    break;
                case 'expenses-tracker':
                    loadExpenses();
                    break;
                case 'customers-loyalty':
                    loadCustomersLoyalty();
                    break;
                case 'suppliers-mgmt':
                    loadSuppliers();
                    break;
                case 'reports-analytics':
                    loadBusinessReports();
                    break;
                case 'settings-admin':
                    loadAdminSettings();
                    break;
            }
        }

        // Generic API Fetch Wrapper
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
                        token = res.data.access_token;
                        user = res.data.user;
                        localStorage.setItem('pos_token', token);
                        localStorage.setItem('pos_user', JSON.stringify(user));
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
            apiFetch('/auth/logout', 'POST').finally(() => {
                token = '';
                user = null;
                localStorage.removeItem('pos_token');
                localStorage.removeItem('pos_user');
                showLogin();
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

        // --- TOAST SERVICE ---
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            let icon = 'fa-check-circle';
            if (type === 'error') icon = 'fa-times-circle';
            if (type === 'warning') icon = 'fa-exclamation-triangle';

            toast.innerHTML = `<i class="fa-solid ${icon}"></i><span>${message}</span>`;
            container.appendChild(toast);
            
            // Reflow
            toast.offsetHeight;
            toast.classList.add('show');

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Modal Helpers
        function openModal(id) {
            document.getElementById(id).classList.add('open');
        }
        function closeModal(id) {
            document.getElementById(id).classList.remove('open');
        }

        // --- TAB 1: POS BILLING & CART LOGIC ---
        let posProducts = [];
        let categories = [];
        let brands = [];

        function loadPosCatalog() {
            // Load Categories
            apiFetch('/categories', 'GET').then(res => {
                if (res.success) {
                    categories = res.data.data;
                    const catSelects = [
                        document.getElementById('pos-category-filter'),
                        document.getElementById('prod-category')
                    ];
                    catSelects.forEach(sel => {
                        if (sel) {
                            sel.innerHTML = sel.id.includes('filter') ? '<option value="">All Categories</option>' : '<option value="">Select Category</option>';
                            categories.forEach(c => {
                                sel.innerHTML += `<option value="${c.id}">${c.name}</option>`;
                            });
                        }
                    });
                }
            });

            // Load Brands
            apiFetch('/brands', 'GET').then(res => {
                if (res.success) {
                    brands = res.data.data;
                    const brandSelects = [
                        document.getElementById('pos-brand-filter'),
                        document.getElementById('prod-brand')
                    ];
                    brandSelects.forEach(sel => {
                        if (sel) {
                            sel.innerHTML = sel.id.includes('filter') ? '<option value="">All Brands</option>' : '<option value="">Select Brand</option>';
                            brands.forEach(b => {
                                sel.innerHTML += `<option value="${b.id}">${b.name}</option>`;
                            });
                        }
                    });
                }
            });

            // Load Products
            apiFetch('/products?limit=100', 'GET').then(res => {
                if (res.success) {
                    posProducts = res.data.data;
                    renderCatalog();
                }
            });
        }

        // Catalog Filter Listeners
        document.getElementById('pos-catalog-search').addEventListener('input', renderCatalog);
        document.getElementById('pos-category-filter').addEventListener('change', renderCatalog);
        document.getElementById('pos-brand-filter').addEventListener('change', renderCatalog);

        function renderCatalog() {
            const search = document.getElementById('pos-catalog-search').value.toLowerCase();
            const catFilter = document.getElementById('pos-category-filter').value;
            const brandFilter = document.getElementById('pos-brand-filter').value;
            const grid = document.getElementById('pos-catalog-grid');

            grid.innerHTML = '';

            const filtered = posProducts.filter(p => {
                const matchesSearch = p.product_name.toLowerCase().includes(search) || p.sku.toLowerCase().includes(search) || p.barcode.includes(search);
                const matchesCategory = !catFilter || parseInt(p.category_id) === parseInt(catFilter);
                const matchesBrand = !brandFilter || parseInt(p.brand_id) === parseInt(brandFilter);
                return matchesSearch && matchesCategory && matchesBrand;
            });

            if (filtered.length === 0) {
                grid.innerHTML = `<div style="grid-column: 1/-1; text-align:center; padding:40px; color:var(--text-muted);">No products match the filters.</div>`;
                return;
            }

            filtered.forEach(p => {
                const isOutOfStock = parseFloat(p.stock_quantity) <= 0;
                grid.innerHTML += `
                    <div class="product-item" onclick="${isOutOfStock ? '' : `addToCart(${p.id})`}">
                        <div class="product-item-image">
                            <i class="fa-solid fa-box"></i>
                        </div>
                        <h4>${p.product_name}</h4>
                        <div class="product-item-meta">
                            <span class="product-item-price">₹${parseFloat(p.selling_price).toFixed(2)}</span>
                            <span class="product-item-stock ${isOutOfStock ? 'out' : ''}">${isOutOfStock ? 'OUT' : `${parseFloat(p.stock_quantity)} ${p.unit}`}</span>
                        </div>
                    </div>
                `;
            });
        }

        // Cart Actions
        function addToCart(productId) {
            const product = posProducts.find(p => p.id === productId);
            if (!product) return;

            const existing = cart.find(item => item.product_id === productId);
            const qtyInCart = existing ? existing.quantity : 0;

            if (qtyInCart + 1 > parseFloat(product.stock_quantity)) {
                showToast("Insufficient stock. Cannot add more items.", "error");
                return;
            }

            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push({
                    product_id: product.id,
                    product_name: product.product_name,
                    sku: product.sku,
                    barcode: product.barcode,
                    selling_price: parseFloat(product.selling_price),
                    gst_percentage: parseFloat(product.gst_percentage),
                    quantity: 1,
                    unit: product.unit
                });
            }

            updateCartUi();
        }

        function updateCartUi() {
            const list = document.getElementById('cart-items-list');
            list.innerHTML = '';

            let subtotal = 0;
            let gstTotal = 0;
            let total = 0;

            cart.forEach(item => {
                // GST calculations (Inclusive pricing)
                // Price_Without_GST = Selling_Price / (1 + GST_Percentage/100)
                const priceWithoutGst = item.selling_price / (1 + (item.gst_percentage / 100));
                const itemGst = item.selling_price - priceWithoutGst;

                const itemSubtotal = priceWithoutGst * item.quantity;
                const itemGstAmount = itemGst * item.quantity;
                const itemTotal = item.selling_price * item.quantity;

                subtotal += itemSubtotal;
                gstTotal += itemGstAmount;
                total += itemTotal;

                list.innerHTML += `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <h4>${item.product_name}</h4>
                            <span>₹${item.selling_price.toFixed(2)} | GST ${item.gst_percentage}%</span>
                        </div>
                        <div class="cart-item-actions">
                            <div class="cart-item-qty">
                                <button onclick="changeQty(${item.product_id}, -1)">&minus;</button>
                                <span style="font-weight:600; min-width:20px; text-align:center;">${item.quantity}</span>
                                <button onclick="changeQty(${item.product_id}, 1)">&plus;</button>
                            </div>
                            <span class="cart-item-total">₹${itemTotal.toFixed(2)}</span>
                            <button class="cart-item-remove" onclick="removeFromCart(${item.product_id})"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </div>
                `;
            });

            // Adjust Loyalty Points values if customer attached
            const loyaltyDetail = document.getElementById('cart-loyalty-details');
            const slider = document.getElementById('loyalty-redeem-slider');
            if (selectedCustomerId && selectedCustomerObj) {
                loyaltyDetail.style.display = 'block';
                const maxPointsRedeemable = Math.min((selectedCustomerObj.loyalty_points || 0), Math.floor(total));
                slider.max = maxPointsRedeemable;
                document.getElementById('cart-customer-points').innerText = `${selectedCustomerObj.loyalty_points || 0} Points`;
                
                // Keep values in range
                if (parseInt(slider.value) > maxPointsRedeemable) {
                    slider.value = maxPointsRedeemable;
                }
                redeemedDiscount = parseInt(slider.value);
                document.getElementById('loyalty-redeem-display').innerText = `Redeem: ${redeemedDiscount}`;
            } else {
                loyaltyDetail.style.display = 'none';
                slider.value = 0;
                redeemedDiscount = 0;
            }

            const grandTotal = Math.max(0, total - redeemedDiscount);

            document.getElementById('cart-subtotal').innerText = `₹${subtotal.toFixed(2)}`;
            document.getElementById('cart-gst').innerText = `₹${gstTotal.toFixed(2)}`;
            document.getElementById('cart-discount').innerText = `-₹${redeemedDiscount.toFixed(2)}`;
            document.getElementById('cart-grand-total').innerText = `₹${grandTotal.toFixed(2)}`;
        }

        function changeQty(productId, change) {
            const item = cart.find(i => i.product_id === productId);
            if (!item) return;

            const product = posProducts.find(p => p.id === productId);
            if (change > 0 && item.quantity + change > parseFloat(product.stock_quantity)) {
                showToast("Insufficient stock.", "error");
                return;
            }

            item.quantity += change;
            if (item.quantity <= 0) {
                removeFromCart(productId);
            } else {
                updateCartUi();
            }
        }

        function removeFromCart(productId) {
            cart = cart.filter(i => i.product_id !== productId);
            updateCartUi();
        }

        function clearCart() {
            cart = [];
            updateCartUi();
        }

        // Barcode scanning simulator handler
        function setupBarcodeScannerSimulator() {
            const sim = document.getElementById('barcode-scanner-simulator');
            sim.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    const val = sim.value.trim();
                    if (!val) return;

                    sim.value = '';
                    // Lookup barcode / SKU endpoint
                    apiFetch(`/products/barcode/${val}`, 'GET')
                        .then(res => {
                            if (res.success) {
                                showToast(`Scanned product: ${res.data.product_name}`, "success");
                                addToCart(res.data.id);
                            } else {
                                showToast(`Product not found with code: ${val}`, "error");
                            }
                        });
                }
            });
        }

        // Customers list loaders for Cart
        function loadCustomersList() {
            apiFetch('/customers?limit=200', 'GET').then(res => {
                if (res.success) {
                    const select = document.getElementById('cart-customer-select');
                    select.innerHTML = '<option value="">Walk-in Customer (No points)</option>';
                    res.data.data.forEach(c => {
                        select.innerHTML += `<option value="${c.id}">${c.name} (${c.mobile})</option>`;
                    });

                    // Add change listener
                    select.onchange = () => {
                        const val = select.value;
                        if (val) {
                            selectedCustomerId = parseInt(val);
                            selectedCustomerObj = res.data.data.find(c => c.id === selectedCustomerId);
                        } else {
                            selectedCustomerId = null;
                            selectedCustomerObj = null;
                        }
                        updateCartUi();
                    };
                }
            });
        }

        // Loyalty points redemption range listener
        document.getElementById('loyalty-redeem-slider').addEventListener('input', (e) => {
            redeemedDiscount = parseInt(e.target.value);
            document.getElementById('loyalty-redeem-display').innerText = `Redeem: ${redeemedDiscount}`;
            updateCartUi();
        });

        // Checkout booking
        function processCheckout() {
            if (cart.length === 0) {
                showToast("Please add items to cart.", "error");
                return;
            }

            const payment = document.getElementById('cart-payment-method').value;
            const checkoutPayload = {
                items: cart.map(i => ({ product_id: i.product_id, quantity: i.quantity })),
                discount: redeemedDiscount,
                payment_method: payment
            };
            if (selectedCustomerId) {
                checkoutPayload.customer_id = selectedCustomerId;
            }

            // Lock checkout button
            const btn = document.getElementById('btn-checkout');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';

            apiFetch('/sales', 'POST', checkoutPayload)
                .then(res => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-print"></i> Book & Print Receipt';

                    if (res.success) {
                        showToast("Sale transaction completed successfully.", "success");
                        // Render print receipt preview
                        renderReceiptPreview(res.data);
                        // Reset
                        clearCart();
                        selectedCustomerId = null;
                        selectedCustomerObj = null;
                        document.getElementById('cart-customer-select').value = '';
                        loadPosCatalog(); // reload stock counts
                    } else {
                        showToast(res.message, "error");
                    }
                });
        }

        function renderReceiptPreview(saleObj) {
            const printContent = document.getElementById('invoice-print-content');
            
            // Build items rows
            let itemLines = '';
            cart.forEach(item => {
                const sub = item.selling_price * item.quantity;
                itemLines += `
                    <tr>
                        <td style="padding: 4px 0;">${item.product_name}<br><small>${item.quantity} x ₹${item.selling_price.toFixed(2)}</small></td>
                        <td style="text-align: right; vertical-align: top; padding: 4px 0;">₹${sub.toFixed(2)}</td>
                    </tr>
                `;
            });

            // Calculate GST splits
            const subtotalVal = parseFloat(saleObj.subtotal);
            const gstVal = parseFloat(saleObj.gst_amount);
            const cgstVal = gstVal / 2;
            const sgstVal = gstVal / 2;

            printContent.innerHTML = `
                <div class="receipt-header">
                    <h3 style="margin: 0; font-size:16px;">GLOBAL RETAIL STORE</h3>
                    <p style="margin: 3px 0;">100 Feet Ring Road, Ahmedabad</p>
                    <p style="margin: 3px 0;">GSTIN: 24AAAAA1111A1Z1</p>
                </div>
                <div class="receipt-divider"></div>
                <table style="width:100%; margin-bottom: 10px;">
                    <tr>
                        <td><strong>Invoice:</strong> ${saleObj.invoice_number}</td>
                        <td style="text-align:right;"><strong>Date:</strong> ${saleObj.invoice_date.substring(0, 10)}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Customer:</strong> ${selectedCustomerObj ? selectedCustomerObj.name : 'Walk-in Customer'}</td>
                    </tr>
                </table>
                <div class="receipt-divider"></div>
                <table class="receipt-table">
                    <thead>
                        <tr>
                            <th style="font-size:11px;">Item Description</th>
                            <th style="text-align: right; font-size:11px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemLines}
                    </tbody>
                </table>
                <div class="receipt-divider"></div>
                <table style="width: 100%; font-size: 11px;">
                    <tr>
                        <td>Subtotal (Excl. Tax)</td>
                        <td style="text-align:right;">₹${subtotalVal.toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>CGST (9%)</td>
                        <td style="text-align:right;">₹${cgstVal.toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>SGST (9%)</td>
                        <td style="text-align:right;">₹${sgstVal.toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>Discount Deducted</td>
                        <td style="text-align:right;">-₹${parseFloat(saleObj.discount).toFixed(2)}</td>
                    </tr>
                    <tr style="font-size: 13px; font-weight: bold;">
                        <td style="padding-top:5px;">Grand Total</td>
                        <td style="text-align:right; padding-top:5px;">₹${parseFloat(saleObj.total_amount).toFixed(2)}</td>
                    </tr>
                </table>
                <div class="receipt-divider"></div>
                <div style="text-align:center; margin-top: 15px; font-size:11px;">
                    <p>Payment Mode: ${saleObj.payment_method}</p>
                    <p style="margin-top: 5px; font-weight: bold;">Thank You for Shopping!</p>
                </div>
            `;

            openModal('modal-invoice-print');
        }

        // --- TAB 2: KPIs & CHART LOGIC ---
        function loadDashboardStats() {
            apiFetch('/dashboard', 'GET').then(res => {
                if (res.success) {
                    const data = res.data;
                    const container = document.getElementById('dashboard-metrics-container');

                    container.innerHTML = `
                        <div class="metric-card">
                            <div class="metric-info">
                                <h5>Today's Sales Revenue</h5>
                                <h3>₹${parseFloat(data.cards.today_sales).toFixed(2)}</h3>
                            </div>
                            <div class="metric-icon"><i class="fa-solid fa-indian-rupee-sign"></i></div>
                        </div>
                        <div class="metric-card success">
                            <div class="metric-info">
                                <h5>Net profit today</h5>
                                <h3>₹${parseFloat(data.cards.today_profit).toFixed(2)}</h3>
                            </div>
                            <div class="metric-icon"><i class="fa-solid fa-wallet"></i></div>
                        </div>
                        <div class="metric-card warning">
                            <div class="metric-info">
                                <h5>Low stock products</h5>
                                <h3>${data.cards.low_stock_count} Items</h3>
                            </div>
                            <div class="metric-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        </div>
                        <div class="metric-card info">
                            <div class="metric-info">
                                <h5>Total Customers</h5>
                                <h3>${data.cards.total_customers} Users</h3>
                            </div>
                            <div class="metric-icon"><i class="fa-solid fa-users"></i></div>
                        </div>
                    `;

                    // Generate / Update Charts
                    renderDashboardCharts(data.charts);
                }
            });
        }

        function renderDashboardCharts(chartsData) {
            // Destroy previous charts if active
            if (salesTrendsChart) salesTrendsChart.destroy();
            if (topProductsChart) topProductsChart.destroy();

            // Sales Trends Chart
            const salesLabels = chartsData.sales_trends.map(t => t.label);
            const salesValues = chartsData.sales_trends.map(t => parseFloat(t.value));

            const ctx1 = document.getElementById('salesTrendsChart').getContext('2d');
            salesTrendsChart = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: salesLabels,
                    datasets: [{
                        label: 'Sales Amount (₹)',
                        data: salesValues,
                        borderColor: 'hsl(263, 85%, 65%)',
                        backgroundColor: 'rgba(124, 58, 237, 0.15)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { grid: { color: 'hsl(220, 12%, 22%)' }, ticks: { color: 'hsl(220, 8%, 70%)' } },
                        x: { grid: { display: false }, ticks: { color: 'hsl(220, 8%, 70%)' } }
                    }
                }
            });

            // Top Products Chart
            const prodLabels = chartsData.top_products.map(p => p.label);
            const prodValues = chartsData.top_products.map(p => parseFloat(p.value));

            const ctx2 = document.getElementById('topProductsChart').getContext('2d');
            topProductsChart = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: prodLabels,
                    datasets: [{
                        label: 'Quantities Sold',
                        data: prodValues,
                        backgroundColor: 'hsl(200, 95%, 60%)',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { grid: { color: 'hsl(220, 12%, 22%)' }, ticks: { color: 'hsl(220, 8%, 70%)' } },
                        x: { grid: { display: false }, ticks: { color: 'hsl(220, 8%, 70%)' } }
                    }
                }
            });
        }

        // --- TAB 3: PRODUCTS CATALOG LISTING & ACTIONS ---
        function loadProductsCatalog() {
            apiFetch('/products?limit=100', 'GET').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('products-table-body');
                    tbody.innerHTML = '';
                    res.data.data.forEach(p => {
                        tbody.innerHTML += `
                            <tr>
                                <td><code>${p.sku}</code></td>
                                <td>
                                    <button class="btn btn-outline" style="padding: 4px 8px; font-size:0.75rem;" onclick="viewBarcode('${p.barcode}')">
                                        <i class="fa-solid fa-barcode"></i> ${p.barcode}
                                    </button>
                                </td>
                                <td><strong>${p.product_name}</strong></td>
                                <td>${p.category_name || '-'}</td>
                                <td>₹${parseFloat(p.selling_price).toFixed(2)}</td>
                                <td>${parseFloat(p.stock_quantity)} ${p.unit}</td>
                                <td><span class="badge ${p.status === 'ACTIVE' ? 'badge-success' : 'badge-danger'}">${p.status}</span></td>
                                <td>
                                    <button class="btn btn-secondary" style="padding: 6px 10px; width:auto; font-size:0.8rem;" onclick="openEditProductModal(${p.id})">Edit</button>
                                    <button class="btn btn-danger" style="padding: 6px 10px; width:auto; font-size:0.8rem;" onclick="deleteProduct(${p.id})">Delete</button>
                                </td>
                            </tr>
                        `;
                    });
                }
            });
        }

        // Edit/Add Product Handler
        function openAddProductModal() {
            document.getElementById('product-form').reset();
            document.getElementById('product-id').value = '';
            document.getElementById('product-modal-title').innerText = 'Add New Product';
            openModal('modal-product');
        }

        function openEditProductModal(id) {
            apiFetch(`/products/${id}`, 'GET').then(res => {
                if (res.success) {
                    const p = res.data;
                    document.getElementById('product-id').value = p.id;
                    document.getElementById('prod-name').value = p.product_name;
                    document.getElementById('prod-sku').value = p.sku;
                    document.getElementById('prod-barcode').value = p.barcode;
                    document.getElementById('prod-category').value = p.category_id || '';
                    document.getElementById('prod-brand').value = p.brand_id || '';
                    document.getElementById('prod-cost').value = p.purchase_price;
                    document.getElementById('prod-retail').value = p.selling_price;
                    document.getElementById('prod-gst').value = p.gst_percentage;
                    document.getElementById('prod-stock').value = p.stock_quantity;
                    document.getElementById('prod-min-stock').value = p.minimum_stock;
                    document.getElementById('prod-unit').value = p.unit;
                    document.getElementById('product-modal-title').innerText = 'Edit Product Details';
                    openModal('modal-product');
                }
            });
        }

        document.getElementById('product-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const id = document.getElementById('product-id').value;
            const payload = {
                product_name: document.getElementById('prod-name').value,
                sku: document.getElementById('prod-sku').value,
                barcode: document.getElementById('prod-barcode').value,
                category_id: document.getElementById('prod-category').value ? parseInt(document.getElementById('prod-category').value) : null,
                brand_id: document.getElementById('prod-brand').value ? parseInt(document.getElementById('prod-brand').value) : null,
                purchase_price: parseFloat(document.getElementById('prod-cost').value),
                selling_price: parseFloat(document.getElementById('prod-retail').value),
                gst_percentage: parseFloat(document.getElementById('prod-gst').value),
                stock_quantity: parseFloat(document.getElementById('prod-stock').value),
                minimum_stock: parseFloat(document.getElementById('prod-min-stock').value),
                unit: document.getElementById('prod-unit').value
            };

            const method = id ? 'PUT' : 'POST';
            const endpoint = id ? `/products/${id}` : '/products';

            apiFetch(endpoint, method, payload).then(res => {
                if (res.success) {
                    showToast(id ? "Product updated." : "Product added to catalog.", "success");
                    closeModal('modal-product');
                    loadProductsCatalog();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        function deleteProduct(id) {
            if (confirm("Are you sure you want to soft delete this product?")) {
                apiFetch(`/products/${id}`, 'DELETE').then(res => {
                    if (res.success) {
                        showToast("Product deleted successfully.", "success");
                        loadProductsCatalog();
                    } else {
                        showToast(res.message, "error");
                    }
                });
            }
        }

        // View vector barcode Code-39 SVG modal
        function viewBarcode(code) {
            apiFetch('/products/barcode/generate', 'POST', { code: code })
                .then(res => {
                    if (res.success) {
                        const preview = document.getElementById('barcode-vector-preview-container');
                        preview.innerHTML = res.data.svg;
                        
                        document.getElementById('btn-print-barcode-preview').onclick = () => {
                            // Open SVG in separate window to print
                            const win = window.open();
                            win.document.write(res.data.svg);
                            win.print();
                        };

                        openModal('modal-barcode-print');
                    } else {
                        showToast(res.message, "error");
                    }
                });
        }

        // --- CATEGORIES & BRANDS MANAGEMENT LOGIC ---
        function openCategoriesModal() {
            loadCategoriesListModal();
            openModal('modal-categories');
        }

        function loadCategoriesListModal() {
            apiFetch('/categories?limit=100', 'GET').then(res => {
                if (res.success) {
                    categories = res.data.data;
                    
                    // Render Category dropdown options inside product forms
                    const catSelects = [
                        document.getElementById('pos-category-filter'),
                        document.getElementById('prod-category')
                    ];
                    catSelects.forEach(sel => {
                        if (sel) {
                            sel.innerHTML = sel.id.includes('filter') ? '<option value="">All Categories</option>' : '<option value="">Select Category</option>';
                            categories.forEach(c => {
                                sel.innerHTML += `<option value="${c.id}">${c.name}</option>`;
                            });
                        }
                    });

                    // Render list inside modal table
                    const tbody = document.getElementById('modal-categories-list-body');
                    tbody.innerHTML = '';
                    categories.forEach(c => {
                        tbody.innerHTML += `
                            <tr>
                                <td><strong>${c.name}</strong></td>
                                <td>
                                    <button class="btn btn-danger" style="padding: 4px 8px; font-size:0.75rem; width:auto;" onclick="deleteCategory(${c.id})">Delete</button>
                                </td>
                            </tr>
                        `;
                    });
                }
            });
        }

        document.getElementById('add-category-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('new-category-name').value.trim();
            if (!name) return;

            apiFetch('/categories', 'POST', { name: name }).then(res => {
                if (res.success) {
                    showToast("Category created successfully.", "success");
                    document.getElementById('new-category-name').value = '';
                    loadCategoriesListModal();
                    // Also refresh pos catalog filter if active
                    if (document.getElementById('panel-pos-billing').classList.contains('active')) {
                        loadPosCatalog();
                    }
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        function deleteCategory(id) {
            if (confirm("Delete this category?")) {
                apiFetch(`/categories/${id}`, 'DELETE').then(res => {
                    if (res.success) {
                        showToast("Category deleted successfully.", "success");
                        loadCategoriesListModal();
                    } else {
                        showToast(res.message, "error");
                    }
                });
            }
        }

        function openBrandsModal() {
            loadBrandsListModal();
            openModal('modal-brands');
        }

        function loadBrandsListModal() {
            apiFetch('/brands?limit=100', 'GET').then(res => {
                if (res.success) {
                    brands = res.data.data;
                    
                    // Render Brand dropdown options inside product forms
                    const brandSelects = [
                        document.getElementById('pos-brand-filter'),
                        document.getElementById('prod-brand')
                    ];
                    brandSelects.forEach(sel => {
                        if (sel) {
                            sel.innerHTML = sel.id.includes('filter') ? '<option value="">All Brands</option>' : '<option value="">Select Brand</option>';
                            brands.forEach(b => {
                                sel.innerHTML += `<option value="${b.id}">${b.name}</option>`;
                            });
                        }
                    });

                    // Render list inside modal table
                    const tbody = document.getElementById('modal-brands-list-body');
                    tbody.innerHTML = '';
                    brands.forEach(b => {
                        tbody.innerHTML += `
                            <tr>
                                <td><strong>${b.name}</strong></td>
                                <td>
                                    <button class="btn btn-danger" style="padding: 4px 8px; font-size:0.75rem; width:auto;" onclick="deleteBrand(${b.id})">Delete</button>
                                </td>
                            </tr>
                        `;
                    });
                }
            });
        }

        document.getElementById('add-brand-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('new-brand-name').value.trim();
            if (!name) return;

            apiFetch('/brands', 'POST', { name: name }).then(res => {
                if (res.success) {
                    showToast("Brand created successfully.", "success");
                    document.getElementById('new-brand-name').value = '';
                    loadBrandsListModal();
                    // Also refresh pos catalog filter if active
                    if (document.getElementById('panel-pos-billing').classList.contains('active')) {
                        loadPosCatalog();
                    }
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        function deleteBrand(id) {
            if (confirm("Delete this brand?")) {
                apiFetch(`/brands/${id}`, 'DELETE').then(res => {
                    if (res.success) {
                        showToast("Brand deleted successfully.", "success");
                        loadBrandsListModal();
                    } else {
                        showToast(res.message, "error");
                    }
                });
            }
        }

        // --- TAB 4: INVENTORY MANAGEMENT SECTION ---
        function loadAllInventory() {
            apiFetch('/inventory?limit=100', 'GET').then(res => {
                if (res.success) {
                    renderInventoryTable(res.data.data);
                    
                    // Set inventory summary alerts count
                    const lowStockCount = res.data.data.filter(i => parseFloat(i.available_stock) <= parseFloat(i.minimum_stock)).length;
                    const outOfStockCount = res.data.data.filter(i => parseFloat(i.available_stock) <= 0).length;
                    
                    document.getElementById('inv-kpi-low-stock').innerText = lowStockCount;
                    document.getElementById('inv-kpi-out-of-stock').innerText = outOfStockCount;
                    document.getElementById('inv-kpi-total').innerText = res.data.total;
                }
            });
        }

        function loadLowStock() {
            apiFetch('/inventory/low-stock', 'GET').then(res => {
                if (res.success) {
                    renderInventoryTable(res.data);
                    showToast("Showing low stock warning warnings.", "warning");
                }
            });
        }

        function loadOutOfStock() {
            apiFetch('/inventory/out-of-stock', 'GET').then(res => {
                if (res.success) {
                    renderInventoryTable(res.data);
                    showToast("Showing out of stock warnings.", "error");
                }
            });
        }

        function renderInventoryTable(data) {
            const tbody = document.getElementById('inventory-table-body');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; color:var(--text-muted);">No stock issues found! All levels adequate.</td></tr>';
                return;
            }

            data.forEach(i => {
                const avail = parseFloat(i.available_stock);
                const min = parseFloat(i.minimum_stock);
                let badge = '<span class="badge badge-success">adequate</span>';
                
                if (avail <= 0) {
                    badge = '<span class="badge badge-danger">Out of stock</span>';
                } else if (avail <= min) {
                    badge = '<span class="badge badge-warning">Low stock</span>';
                }

                tbody.innerHTML += `
                    <tr>
                        <td><code>${i.sku}</code></td>
                        <td><strong>${i.product_name}</strong></td>
                        <td style="font-weight:700;">${avail} ${i.unit}</td>
                        <td>${parseFloat(i.damaged_stock)} ${i.unit}</td>
                        <td>${min} ${i.unit}</td>
                        <td>${parseFloat(i.reorder_level)} ${i.unit}</td>
                        <td>${badge}</td>
                        <td>
                            <button class="btn btn-outline" style="padding: 6px 10px; font-size:0.75rem;" onclick="openAdjustModal(${i.product_id}, '${i.product_name.replace(/'/g, "\\'")}', ${avail}, ${parseFloat(i.damaged_stock)})">
                                <i class="fa-solid fa-arrows-spin"></i> Adjust
                            </button>
                        </td>
                    </tr>
                `;
            });
        }

        // Adjust modal handlers
        function openAdjustModal(prodId, name, avail, damaged) {
            document.getElementById('adjust-product-id').value = prodId;
            document.getElementById('adjust-product-name').value = name;
            document.getElementById('adjust-stock').value = avail;
            document.getElementById('adjust-damaged').value = damaged;
            document.getElementById('adjust-remarks').value = '';
            openModal('modal-adjust');
        }

        document.getElementById('adjust-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                product_id: parseInt(document.getElementById('adjust-product-id').value),
                available_stock: parseFloat(document.getElementById('adjust-stock').value),
                damaged_stock: parseFloat(document.getElementById('adjust-damaged').value),
                remarks: document.getElementById('adjust-remarks').value
            };

            apiFetch('/inventory/adjust', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Inventory adjusted and activity logged.", "success");
                    closeModal('modal-adjust');
                    loadAllInventory();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // --- TAB 5: PURCHASE ORDERS ---
        function loadPurchaseOrders() {
            apiFetch('/purchases?limit=100', 'GET').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('purchases-table-body');
                    tbody.innerHTML = '';
                    res.data.data.forEach(po => {
                        tbody.innerHTML += `
                            <tr>
                                <td><code>${po.po_number}</code></td>
                                <td><strong>${po.supplier_name}</strong></td>
                                <td>${po.product_name}<br><small>SKU: ${po.sku}</small></td>
                                <td>${parseFloat(po.quantity)}</td>
                                <td>₹${parseFloat(po.purchase_price).toFixed(2)}</td>
                                <td>₹${parseFloat(po.gst_amount).toFixed(2)}</td>
                                <td style="font-weight:700;">₹${parseFloat(po.total_amount).toFixed(2)}</td>
                                <td>${po.purchase_date.substring(0, 10)}</td>
                                <td><span class="badge ${po.status === 'RECEIVED' ? 'badge-success' : 'badge-danger'}">${po.status}</span></td>
                                <td>
                                    ${po.status === 'RECEIVED' ? `<button class="btn btn-danger" style="padding: 4px 8px; font-size:0.75rem;" onclick="voidPurchaseOrder(${po.id})">Void</button>` : '-'}
                                </td>
                            </tr>
                        `;
                    });
                }
            });
        }

        function openAddPurchaseModal() {
            // Load Suppliers dropdown
            apiFetch('/suppliers?limit=100', 'GET').then(res => {
                if (res.success) {
                    const sel = document.getElementById('po-supplier');
                    sel.innerHTML = '<option value="">Select Supplier</option>';
                    res.data.data.forEach(s => {
                        sel.innerHTML += `<option value="${s.id}">${s.supplier_name}</option>`;
                    });
                }
            });

            // Load Products dropdown
            apiFetch('/products?limit=100', 'GET').then(res => {
                if (res.success) {
                    const sel = document.getElementById('po-product');
                    sel.innerHTML = '<option value="">Select Product</option>';
                    res.data.data.forEach(p => {
                        sel.innerHTML += `<option value="${p.id}">${p.product_name} (${p.sku})</option>`;
                    });
                }
            });

            document.getElementById('purchase-form').reset();
            openModal('modal-purchase');
        }

        document.getElementById('purchase-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                supplier_id: parseInt(document.getElementById('po-supplier').value),
                product_id: parseInt(document.getElementById('po-product').value),
                quantity: parseFloat(document.getElementById('po-qty').value),
                purchase_price: parseFloat(document.getElementById('po-cost').value)
            };

            apiFetch('/purchases', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("PO booked, stock automatically incremented.", "success");
                    closeModal('modal-purchase');
                    loadPurchaseOrders();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        function voidPurchaseOrder(id) {
            if (confirm("Voiding PO will decrement stock numbers. Proceed?")) {
                apiFetch(`/purchases/${id}`, 'DELETE').then(res => {
                    if (res.success) {
                        showToast("Purchase order cancelled.", "success");
                        loadPurchaseOrders();
                    } else {
                        showToast(res.message, "error");
                    }
                });
            }
        }

        // --- TAB 6: EXPENSES SECTION ---
        function loadExpenses() {
            apiFetch('/expenses?limit=100', 'GET').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('expenses-table-body');
                    tbody.innerHTML = '';
                    res.data.data.forEach(exp => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${exp.expense_date}</td>
                                <td><strong>${exp.expense_type}</strong></td>
                                <td style="font-weight:700; color:var(--accent-danger);">₹${parseFloat(exp.amount).toFixed(2)}</td>
                                <td>${exp.details || '-'}</td>
                                <td>${exp.created_at.substring(11, 16)}</td>
                                <td>
                                    <button class="btn btn-danger" style="padding: 4px 8px; font-size:0.75rem;" onclick="deleteExpense(${exp.id})">Delete</button>
                                </td>
                            </tr>
                        `;
                    });
                }
            });
        }

        function openAddExpenseModal() {
            document.getElementById('expense-form').reset();
            document.getElementById('exp-date').value = new Date().toISOString().substring(0, 10);
            openModal('modal-expense');
        }

        document.getElementById('expense-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                expense_type: document.getElementById('exp-type').value,
                amount: parseFloat(document.getElementById('exp-amount').value),
                expense_date: document.getElementById('exp-date').value,
                details: document.getElementById('exp-details').value
            };

            apiFetch('/expenses', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Expense logged successfully.", "success");
                    closeModal('modal-expense');
                    loadExpenses();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        function deleteExpense(id) {
            if (confirm("Delete this expense record?")) {
                apiFetch(`/expenses/${id}`, 'DELETE').then(res => {
                    if (res.success) {
                        showToast("Expense record deleted.", "success");
                        loadExpenses();
                    } else {
                        showToast(res.message, "error");
                    }
                });
            }
        }

        // --- TAB 7: CUSTOMERS & LOYALTY LEDGER ---
        function loadCustomersLoyalty() {
            // Customers
            apiFetch('/customers?limit=100', 'GET').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('customers-table-body');
                    tbody.innerHTML = '';
                    res.data.data.forEach(c => {
                        tbody.innerHTML += `
                            <tr>
                                <td><code>${c.customer_code}</code></td>
                                <td><strong>${c.name}</strong></td>
                                <td>${c.mobile}</td>
                                <td>${c.email || '-'}</td>
                                <td>${c.gst_number || '-'}</td>
                                <td style="font-weight:700; color:var(--accent-success);">${c.loyalty_points} pts</td>
                                <td>₹${parseFloat(c.total_purchases).toFixed(2)}</td>
                                <td>
                                    <button class="btn btn-secondary" style="padding:4px 8px; font-size:0.75rem;" onclick="deleteCustomer(${c.id})">Delete</button>
                                </td>
                            </tr>
                        `;
                    });
                }
            });

            // Loyalty Ledger
            apiFetch('/loyalty?limit=100', 'GET').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('loyalty-ledger-body');
                    tbody.innerHTML = '';
                    res.data.data.forEach(l => {
                        const isEarned = l.transaction_type === 'EARNED';
                        tbody.innerHTML += `
                            <tr>
                                <td>${l.created_at}</td>
                                <td><code>${l.customer_code}</code></td>
                                <td><strong>${l.customer_name}</strong></td>
                                <td><span class="badge ${isEarned ? 'badge-success' : 'badge-danger'}">${l.transaction_type}</span></td>
                                <td style="font-weight:700; color:${isEarned ? 'var(--accent-success)' : 'var(--accent-danger)'}">${l.points > 0 ? '+' : ''}${l.points} pts</td>
                                <td>${l.remarks || '-'}</td>
                            </tr>
                        `;
                    });
                }
            });
        }

        function openAddCustomerModal() {
            document.getElementById('customer-form').reset();
            openModal('modal-customer');
        }

        document.getElementById('customer-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                name: document.getElementById('cust-name').value,
                mobile: document.getElementById('cust-mobile').value,
                email: document.getElementById('cust-email').value,
                gst_number: document.getElementById('cust-gst').value,
                address: document.getElementById('cust-address').value
            };

            apiFetch('/customers', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Customer registered successfully.", "success");
                    closeModal('modal-customer');
                    
                    // Reload customer options
                    if (document.getElementById('panel-pos-billing').classList.contains('active')) {
                        loadCustomersList();
                    } else {
                        loadCustomersLoyalty();
                    }
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        function deleteCustomer(id) {
            if (confirm("Delete customer registry? (All history remains intact)")) {
                apiFetch(`/customers/${id}`, 'DELETE').then(res => {
                    if (res.success) {
                        showToast("Customer deleted.", "success");
                        loadCustomersLoyalty();
                    } else {
                        showToast(res.message, "error");
                    }
                });
            }
        }

        // --- TAB 8: SUPPLIERS DIRECTORY ---
        function loadSuppliers() {
            apiFetch('/suppliers?limit=100', 'GET').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('suppliers-table-body');
                    tbody.innerHTML = '';
                    res.data.data.forEach(s => {
                        tbody.innerHTML += `
                            <tr>
                                <td><strong>${s.supplier_name}</strong></td>
                                <td>${s.mobile}</td>
                                <td>${s.email || '-'}</td>
                                <td>${s.gst_number || '-'}</td>
                                <td>${s.address || '-'}</td>
                                <td><span class="badge badge-success">${s.status}</span></td>
                                <td>
                                    <button class="btn btn-danger" style="padding:4px 8px; font-size:0.75rem;" onclick="deleteSupplier(${s.id})">Delete</button>
                                </td>
                            </tr>
                        `;
                    });
                }
            });
        }

        function openAddSupplierModal() {
            document.getElementById('supplier-form').reset();
            openModal('modal-supplier');
        }

        document.getElementById('supplier-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                supplier_name: document.getElementById('sup-name').value,
                mobile: document.getElementById('sup-mobile').value,
                email: document.getElementById('sup-email').value,
                gst_number: document.getElementById('sup-gst').value,
                address: document.getElementById('sup-address').value
            };

            apiFetch('/suppliers', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("Supplier catalog record added.", "success");
                    closeModal('modal-supplier');
                    loadSuppliers();
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        function deleteSupplier(id) {
            if (confirm("Delete supplier profile?")) {
                apiFetch(`/suppliers/${id}`, 'DELETE').then(res => {
                    if (res.success) {
                        showToast("Supplier profile deleted.", "success");
                        loadSuppliers();
                    } else {
                        showToast(res.message, "error");
                    }
                });
            }
        }

        // --- TAB 9: REPORTS ANALYTICS & GST ---
        function loadBusinessReports() {
            // Monthly Profit & Loss Graph
            apiFetch('/reports/profit-loss', 'GET').then(res => {
                if (res.success) {
                    renderProfitLossChart(res.data);
                }
            });

            // GST Returns Summary
            apiFetch('/reports/gst', 'GET').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('reports-gst-body');
                    tbody.innerHTML = '';
                    res.data.forEach(r => {
                        tbody.innerHTML += `
                            <tr>
                                <td><strong>${r.month}</strong></td>
                                <td>₹${parseFloat(r.taxable_amount).toFixed(2)}</td>
                                <td>₹${parseFloat(r.cgst).toFixed(2)}</td>
                                <td>₹${parseFloat(r.sgst).toFixed(2)}</td>
                                <td style="font-weight:700; color:var(--primary);">₹${parseFloat(r.gst_total).toFixed(2)}</td>
                            </tr>
                        `;
                    });
                }
            });
        }

        function renderProfitLossChart(reportData) {
            if (profitLossChart) profitLossChart.destroy();

            const labels = reportData.map(r => r.month);
            const revenues = reportData.map(r => r.sales_revenue);
            const profits = reportData.map(r => r.net_profit);

            const ctx = document.getElementById('profitLossChart').getContext('2d');
            profitLossChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Gross Revenue (₹)',
                            data: revenues,
                            backgroundColor: 'rgba(124, 58, 237, 0.5)',
                            borderColor: 'hsl(263, 85%, 65%)',
                            borderWidth: 1
                        },
                        {
                            label: 'Net Profits (₹)',
                            data: profits,
                            backgroundColor: 'rgba(16, 185, 129, 0.6)',
                            borderColor: 'hsl(142, 72%, 50%)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { grid: { color: 'hsl(220, 12%, 22%)' }, ticks: { color: 'hsl(220, 8%, 70%)' } },
                        x: { grid: { display: false }, ticks: { color: 'hsl(220, 8%, 70%)' } }
                    }
                }
            });
        }

        // --- TAB 10: ADMIN PANEL CONFIGURATION ---
        function loadAdminSettings() {
            // Load Users Approval Table
            apiFetch('/auth/users', 'GET').then(res => {
                if (res.success) {
                    const tbody = document.getElementById('settings-users-body');
                    tbody.innerHTML = '';
                    res.data.forEach(u => {
                        const statusClass = u.status === 'APPROVED' ? 'badge-success' : (u.status === 'BLOCKED' ? 'badge-danger' : 'badge-warning');
                        tbody.innerHTML += `
                            <tr>
                                <td>
                                    <strong>${u.name}</strong><br>
                                    <small>${u.username} | ${u.email}</small>
                                </td>
                                <td><code style="text-transform:uppercase;">${u.role.replace('pos_', '')}</code></td>
                                <td><span class="badge ${statusClass}">${u.status}</span></td>
                                <td>
                                    <select class="catalog-select" style="padding:4px; font-size:0.75rem;" onchange="changeUserStatus(${u.id}, this.value)">
                                        <option value="">Change Status</option>
                                        <option value="APPROVED">APPROVE</option>
                                        <option value="HOLD">HOLD</option>
                                        <option value="BLOCKED">BLOCK</option>
                                    </select>
                                    <button class="btn btn-danger" style="padding: 4px 8px; font-size:0.75rem; width:auto;" onclick="deleteUserAccount(${u.id})">Delete</button>
                                </td>
                            </tr>
                        `;
                    });
                }
            });

            // Load SMTP Settings options
            apiFetch('/auth/smtp', 'GET').then(res => {
                if (res.success) {
                    const s = res.data;
                    document.getElementById('smtp-enabled').value = s.smtp_enabled;
                    document.getElementById('smtp-encryption').value = s.smtp_encryption;
                    document.getElementById('smtp-host').value = s.smtp_host;
                    document.getElementById('smtp-port').value = s.smtp_port;
                    document.getElementById('smtp-username').value = s.smtp_username;
                    document.getElementById('smtp-password').value = s.smtp_password;
                    document.getElementById('smtp-from-email').value = s.from_email;
                    document.getElementById('smtp-from-name').value = s.from_name;
                    document.getElementById('email-subject').value = s.subject;
                    document.getElementById('email-template').value = s.template;
                }
            });
        }

        // Save SMTP settings handler
        document.getElementById('smtp-settings-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const payload = {
                smtp_enabled: document.getElementById('smtp-enabled').value,
                smtp_encryption: document.getElementById('smtp-encryption').value,
                smtp_host: document.getElementById('smtp-host').value,
                smtp_port: document.getElementById('smtp-port').value,
                smtp_username: document.getElementById('smtp-username').value,
                smtp_password: document.getElementById('smtp-password').value,
                from_email: document.getElementById('smtp-from-email').value,
                from_name: document.getElementById('smtp-from-name').value,
                subject: document.getElementById('email-subject').value,
                template: document.getElementById('email-template').value
            };

            apiFetch('/auth/smtp', 'POST', payload).then(res => {
                if (res.success) {
                    showToast("SMTP email service configurations saved successfully.", "success");
                } else {
                    showToast(res.message, "error");
                }
            });
        });

        // Trigger Test Email
        function triggerTestEmail() {
            const email = document.getElementById('smtp-test-email').value.trim();
            if (!email) {
                showToast("Please enter a valid recipient test email.", "error");
                return;
            }

            showToast("Dispatching test mail...", "warning");
            apiFetch('/auth/smtp/test', 'POST', { test_email: email })
                .then(res => {
                    if (res.success) {
                        showToast(res.message, "success");
                    } else {
                        showToast(res.message, "error");
                    }
                });
        }

        // User Account Status Adjustments
        function changeUserStatus(userId, statusVal) {
            if (!statusVal) return;
            apiFetch('/auth/users/status', 'POST', { user_id: userId, status: statusVal })
                .then(res => {
                    if (res.success) {
                        showToast("User status updated successfully.", "success");
                        loadAdminSettings();
                    } else {
                        showToast(res.message, "error");
                    }
                });
        }

        function deleteUserAccount(userId) {
            if (confirm("Permanently delete this user operator account?")) {
                apiFetch(`/auth/users/${userId}`, 'DELETE')
                    .then(res => {
                        if (res.success) {
                            showToast("User operator account deleted.", "success");
                            loadAdminSettings();
                        } else {
                            showToast(res.message, "error");
                        }
                    });
            }
        }
    </script>
</body>
</html>
