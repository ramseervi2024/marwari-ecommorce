<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mārwāri E-Commerce | Customer Management Panel</title>
  <!-- SEO Meta Tags -->
  <meta name="description" content="Manage your customers, view stats, and import/export CSV contacts with JWT auth.">
  <meta name="author" content="Mārwāri E-Commerce Team">
  <link rel="stylesheet" href="<?php echo plugin_dir_url(dirname(dirname(__FILE__)) . '/customer-manager.php') . 'assets/style.css?v=' . (defined('CUSTOMER_MANAGER_VERSION') ? CUSTOMER_MANAGER_VERSION : time()); ?>">
  <style>
    /* Specific styles for Customer Management Panel */
    .connection-status {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.85rem;
      font-weight: 500;
      padding: 0.35rem 0.75rem;
      border-radius: 99px;
      background: rgba(16, 185, 129, 0.1);
      color: var(--success);
      border: 1px solid rgba(16, 185, 129, 0.2);
    }
    .connection-status.offline {
      background: rgba(239, 68, 68, 0.1);
      color: var(--danger);
      border: 1px solid rgba(239, 68, 68, 0.2);
    }
    .auth-card-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
      background: radial-gradient(circle at 50% 50%, var(--primary-glow) 0%, var(--bg-app) 100%);
    }
    .auth-card {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 24px;
      width: 100%;
      max-width: 440px;
      padding: 2.5rem;
      box-shadow: var(--shadow);
    }
    .auth-logo {
      text-align: center;
      margin-bottom: 2rem;
    }
    .auth-logo h2 {
      font-family: var(--font-heading);
      font-size: 2rem;
      font-weight: 800;
    }
    .auth-logo h2 span {
      color: var(--primary);
      text-shadow: 0 0 10px var(--primary-glow);
    }
    .auth-logo p {
      color: var(--text-secondary);
      font-size: 0.9rem;
      margin-top: 0.25rem;
    }
    .auth-toggle-tip {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 0.85rem;
      color: var(--text-secondary);
    }
    .auth-toggle-tip a {
      color: var(--primary);
      font-weight: 600;
    }
    .auth-toggle-tip a:hover {
      text-decoration: underline;
    }
    .api-config-badge {
      display: block;
      font-size: 0.75rem;
      color: var(--text-muted);
      background: rgba(255, 255, 255, 0.03);
      padding: 0.5rem;
      border-radius: 8px;
      border: 1px dashed var(--border-color);
      margin-bottom: 1.25rem;
      word-break: break-all;
    }
    .role-badge {
      display: inline-block;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      padding: 0.2rem 0.6rem;
      border-radius: 4px;
      letter-spacing: 0.5px;
    }
    .role-badge.super-admin {
      background: rgba(251, 191, 36, 0.15);
      color: var(--primary);
      border: 1px solid rgba(251, 191, 36, 0.3);
    }
    .role-badge.manager {
      background: rgba(16, 185, 129, 0.15);
      color: var(--success);
      border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .role-badge.viewer {
      background: rgba(148, 163, 184, 0.15);
      color: var(--text-secondary);
      border: 1px solid rgba(148, 163, 184, 0.3);
    }
    .action-header-row {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }
    .action-buttons-group {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    .skeleton-row td {
      padding: 1.25rem 1rem;
    }
    .skeleton-line {
      height: 14px;
      background: linear-gradient(90deg, rgba(255,255,255,0.03) 25%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.03) 75%);
      background-size: 200% 100%;
      animation: loading-skeleton 1.5s infinite;
      border-radius: 4px;
    }
    @keyframes loading-skeleton {
      0% { background-position: 200% 0; }
      100% { background-position: -200% 0; }
    }
    .pagination-wrapper {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1.5rem;
      padding-top: 1rem;
      border-top: 1px solid var(--border-color);
    }
    .pagination-buttons {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .pagination-btn {
      min-width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 8px;
      border: 1px solid var(--border-color);
      font-size: 0.85rem;
      font-weight: 500;
      background: var(--bg-card);
      transition: var(--transition);
    }
    .pagination-btn:hover:not(:disabled) {
      border-color: var(--primary);
      color: var(--primary);
      background: var(--primary-glow);
    }
    .pagination-btn.active {
      background: var(--primary);
      color: #000;
      border-color: var(--primary);
    }
    .pagination-btn:disabled {
      opacity: 0.4;
      cursor: not-allowed;
    }
    .import-file-input {
      display: none;
    }
    .import-errors-container {
      margin-top: 1rem;
      padding: 1rem;
      background: rgba(239, 68, 68, 0.05);
      border: 1px dashed var(--danger);
      border-radius: 12px;
      max-height: 150px;
      overflow-y: auto;
    }
    .import-errors-title {
      font-size: 0.85rem;
      font-weight: 700;
      color: var(--danger);
      margin-bottom: 0.5rem;
    }
    .import-error-item {
      font-size: 0.8rem;
      color: var(--text-secondary);
      margin-bottom: 0.25rem;
      padding-left: 0.5rem;
      border-left: 2px solid var(--danger);
    }
    .table-actions {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .table-btn {
      width: 32px;
      height: 32px;
      border-radius: 6px;
      border: 1px solid var(--border-color);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--text-secondary);
      transition: var(--transition);
    }
    .table-btn:hover {
      background: var(--primary-glow);
      border-color: var(--primary);
      color: var(--primary);
    }
    .table-btn.danger:hover {
      background: rgba(239, 68, 68, 0.1);
      border-color: var(--danger);
      color: var(--danger);
    }
    .nav-user-info {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.75rem 1rem;
      border-bottom: 1px solid var(--border-color);
      margin-bottom: 1rem;
    }
    .nav-user-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary), var(--accent));
      color: #000;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1rem;
    }
    .nav-user-details {
      display: flex;
      flex-direction: column;
    }
    .nav-user-name {
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--text-primary);
    }
    .nav-user-role {
      font-size: 0.7rem;
      color: var(--text-secondary);
    }
    /* Filter container */
    .filter-tabs {
      display: flex;
      gap: 0.5rem;
    }
    .filter-btn {
      padding: 0.4rem 1rem;
      font-size: 0.85rem;
      border-radius: 99px;
      border: 1px solid var(--border-color);
      background: var(--bg-card);
      font-weight: 500;
    }
    .filter-btn:hover {
      border-color: var(--primary);
      color: var(--primary);
    }
    .mobile-only-logout {
      display: none;
    }
    @media (max-width: 767px) {
      .admin-view-wrapper.active {
        display: flex !important;
        flex-direction: column !important;
        min-height: 100vh !important;
        padding: 0 !important;
        gap: 0 !important;
      }
      .admin-sidebar {
        border-bottom: 1px solid var(--border-color) !important;
        padding: 1.25rem !important;
        height: auto !important;
        position: static !important;
      }
      .admin-logo {
        margin-bottom: 1rem !important;
        text-align: center !important;
      }
      .nav-user-info {
        justify-content: center !important;
        margin-bottom: 1rem !important;
        padding-bottom: 0.75rem !important;
      }
      .admin-nav-links {
        flex-direction: row !important;
        overflow-x: auto !important;
        gap: 0.5rem !important;
        padding-bottom: 0.5rem !important;
        margin-bottom: 0 !important;
        justify-content: flex-start !important;
        width: 100% !important;
      }
      .admin-nav-link {
        width: auto !important;
        padding: 0.6rem 1rem !important;
        font-size: 0.85rem !important;
      }
      .admin-logout-btn {
        display: none !important;
      }
      .mobile-only-logout {
        display: flex !important;
      }
      .admin-content-area {
        padding: 1.25rem !important;
      }
      .admin-panel-header h2 {
        font-size: 1.6rem !important;
      }
      .action-header-row {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 1rem !important;
      }
      .search-bar {
        max-width: 100% !important;
        width: 100% !important;
      }
      .filter-tabs {
        width: 100% !important;
        justify-content: space-between !important;
      }
      .filter-btn {
        flex: 1 !important;
        text-align: center !important;
      }
      .action-buttons-group {
        width: 100% !important;
        flex-wrap: wrap !important;
        gap: 0.5rem !important;
      }
      .action-buttons-group .btn-primary {
        flex: 1 !important;
        justify-content: center !important;
        font-size: 0.85rem !important;
        padding: 0.6rem 1rem !important;
      }
      .pagination-wrapper {
        flex-direction: column !important;
        gap: 1rem !important;
        align-items: center !important;
      }
      .auth-card {
        padding: 1.5rem !important;
        border-radius: 16px !important;
      }
    }
  </style>
</head>

<body>

  <!-- Top Notification Toast Container -->
  <div class="toast-container" id="toast-container"></div>

  <!-- AUTHENTICATION LAYER -->
  <div id="auth-layer" class="auth-card-wrapper" style="display: none;">
    <div class="auth-card">
      <div class="auth-logo">
        <h2><span>Mārwāri</span> Portal</h2>
        <p>Customer Management API Service</p>
      </div>

      <!-- Connection config preview -->
      <div class="api-config-badge">
        <strong>API Endpoint:</strong> <span id="auth-endpoint-preview">...</span>
      </div>

      <!-- Login Form -->
      <form id="panel-login-form">
        <h3 style="font-size: 1.25rem; margin-bottom: 1.25rem; font-family: var(--font-body); font-weight:600;">Sign In</h3>
        <div class="form-group">
          <label for="login-username">Username or Email</label>
          <input type="text" id="login-username" class="form-input" placeholder="e.g. admin" required>
        </div>
        <div class="form-group">
          <label for="login-user-pass">Password</label>
          <input type="password" id="login-user-pass" class="form-input" placeholder="••••••••" required>
        </div>
        <button type="submit" class="auth-submit-btn" style="margin-top: 0.5rem;">Access Management Panel</button>
        <p class="auth-toggle-tip">
          Don't have an API account? <a href="#" id="show-register-link">Register Here</a>
        </p>
      </form>

      <!-- Register Form -->
      <form id="panel-register-form" style="display: none;">
        <h3 style="font-size: 1.25rem; margin-bottom: 1.25rem; font-family: var(--font-body); font-weight:600;">Create API Account</h3>
        <div class="form-group">
          <label for="reg-username">Username</label>
          <input type="text" id="reg-username" class="form-input" placeholder="e.g. ramesh_seervi" required>
        </div>
        <div class="form-group">
          <label for="reg-name">Full Name</label>
          <input type="text" id="reg-name" class="form-input" placeholder="e.g. Ramesh Seervi" required>
        </div>
        <div class="form-group">
          <label for="reg-email">Email Address</label>
          <input type="email" id="reg-email" class="form-input" placeholder="e.g. ramesh@example.com" required>
        </div>
        <div class="form-group">
          <label for="reg-password">Password</label>
          <input type="password" id="reg-password" class="form-input" placeholder="••••••••" required>
        </div>
        <div class="form-group">
          <label for="reg-role">Access Authorization Level</label>
          <select id="reg-role" class="form-input" required>
            <option value="api_super_admin">Super Admin (Full CRUD, Import/Export, Stats)</option>
            <option value="api_manager">Manager (Create, Edit, View)</option>
            <option value="api_viewer">Viewer (View Only)</option>
          </select>
        </div>
        <button type="submit" class="auth-submit-btn" style="margin-top: 0.5rem;">Register Account</button>
        <p class="auth-toggle-tip">
          Already have an account? <a href="#" id="show-login-link">Sign In</a>
        </p>
      </form>
    </div>
  </div>

  <!-- PANEL LAYER -->
  <section class="admin-view-wrapper" id="panel-layer" style="display: none;">
    <!-- Side Navigation Bar -->
    <aside class="admin-sidebar">
      <div class="admin-logo">
        <span>Mārwāri</span> Clients
      </div>

      <!-- Current Logged User Info -->
      <div class="nav-user-info" id="nav-user-info-box">
        <div class="nav-user-avatar" id="user-avatar-initials">U</div>
        <div class="nav-user-details">
          <span class="nav-user-name" id="user-display-name">Loading...</span>
          <span class="nav-user-role" id="user-display-role">...</span>
        </div>
      </div>

      <nav class="admin-nav-links">
        <button class="admin-nav-link active" data-panel="overview-panel" id="sidebar-overview-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="7" height="9" x="3" y="3" rx="1" />
            <rect width="7" height="5" x="14" y="3" rx="1" />
            <rect width="7" height="9" x="14" y="12" rx="1" />
            <rect width="7" height="5" x="3" y="16" rx="1" />
          </svg>
          Overview Statistics
        </button>
        <button class="admin-nav-link" data-panel="customers-panel">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
          </svg>
          Customer Directory
        </button>
        <button class="admin-nav-link" data-panel="settings-panel">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3" />
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
          </svg>
          Connection Config
        </button>
      </nav>

      <button class="admin-logout-btn" id="panel-logout-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor"
          stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" />
        </svg>
        Sign Out
      </button>
    </aside>

    <!-- Main Content Area -->
    <main class="admin-content-area">
      
      <!-- Top Actions Banner -->
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <div class="connection-status" id="connection-status-badge">
          <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--success); display: inline-block;"></span>
          API Connection Stable
        </div>
        
        <!-- Actions Wrapper -->
        <div style="display:flex; align-items:center; gap:0.5rem;">
          <!-- Theme Switch Toggle -->
          <button class="nav-btn" id="theme-toggle-btn" title="Toggle Theme" style="border-radius:12px; width:40px; height:40px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor"
              stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="4" />
              <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" />
            </svg>
          </button>
          
          <!-- Mobile Logout Button -->
          <button class="nav-btn mobile-only-logout" id="mobile-logout-btn" title="Sign Out" style="border-radius:12px; width:40px; height:40px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor"
              stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" />
            </svg>
          </button>
        </div>
      </div>

      <!-- PANEL 1: OVERVIEW -->
      <div class="admin-panel active" id="overview-panel">
        <div class="admin-panel-header">
          <h2>Overview Dashboard</h2>
          <p>Real-time analytics and statistics retrieved from WordPress API.</p>
        </div>

        <div class="admin-stats-grid" style="margin-top: 1.5rem;">
          <div class="admin-panel-card" style="padding:1.5rem; text-align:center;">
            <h4 style="text-transform:uppercase; font-size:0.75rem; color:var(--text-muted); letter-spacing:1px;">Total Customers</h4>
            <p id="stats-total" style="font-size:2.5rem; font-weight:800; color:var(--primary); margin-top:0.5rem;">...</p>
          </div>
          <div class="admin-panel-card" style="padding:1.5rem; text-align:center;">
            <h4 style="text-transform:uppercase; font-size:0.75rem; color:var(--text-muted); letter-spacing:1px;">Active Members</h4>
            <p id="stats-active" style="font-size:2.5rem; font-weight:800; color:var(--success); margin-top:0.5rem;">...</p>
          </div>
          <div class="admin-panel-card" style="padding:1.5rem; text-align:center;">
            <h4 style="text-transform:uppercase; font-size:0.75rem; color:var(--text-muted); letter-spacing:1px;">Inactive Members</h4>
            <p id="stats-inactive" style="font-size:2.5rem; font-weight:800; color:var(--accent); margin-top:0.5rem;">...</p>
          </div>
        </div>

        <div class="admin-panel-card" style="margin-top: 2rem;">
          <h3>WordPress Environment Details</h3>
          <div style="display:flex; flex-direction:column; gap: 0.75rem; margin-top: 1rem; color:var(--text-secondary); font-size: 0.95rem;">
            <div><strong>Backend API URL:</strong> <code id="details-api-url">...</code></div>
            <div><strong>Active User Account:</strong> <span id="details-username">...</span></div>
            <div><strong>Authorized Capabilities:</strong> <span id="details-capabilities" style="color:var(--primary);">...</span></div>
          </div>
        </div>
      </div>

      <!-- PANEL 2: CUSTOMERS DIRECTORY -->
      <div class="admin-panel" id="customers-panel">
        <div class="admin-panel-header">
          <div style="display:flex; justify-content:space-between; align-items:center; width:100%; flex-wrap:wrap; gap:1rem;">
            <div>
              <h2>Customer Directory</h2>
              <p>Search, filter, paginate, and manage the client records database.</p>
            </div>
            <div class="action-buttons-group">
              <button class="btn-primary" id="add-customer-trigger-btn" style="padding: 0.6rem 1.2rem; display:none;">
                + Add Customer
              </button>
              
              <!-- Export CSV Button -->
              <button class="btn-primary" id="export-csv-btn" style="background:transparent; border:1px solid var(--border-color); color:var(--text-primary); box-shadow:none; padding: 0.6rem 1.2rem; display:none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor"
                  stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                </svg>
                Export CSV
              </button>

              <!-- Import CSV Trigger -->
              <button class="btn-primary" id="import-csv-trigger-btn" style="background:transparent; border:1px solid var(--border-color); color:var(--text-primary); box-shadow:none; padding: 0.6rem 1.2rem; display:none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor"
                  stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                </svg>
                Import CSV
              </button>
              <input type="file" id="csv-file-input" class="import-file-input" accept=".csv">
            </div>
          </div>
        </div>

        <div class="admin-panel-card" style="margin-top: 1.5rem;">
          <!-- Search & Filter Controls -->
          <div class="action-header-row">
            <div class="search-bar" style="max-width: 320px;">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor"
                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
              </svg>
              <input type="text" id="customer-search-input" placeholder="Search first/last name, email...">
            </div>

            <div style="display:flex; align-items:center; gap: 1rem; flex-wrap:wrap;">
              <div class="filter-tabs">
                <button class="filter-btn active" data-status="all">All</button>
                <button class="filter-btn" data-status="ACTIVE">Active</button>
                <button class="filter-btn" data-status="INACTIVE">Inactive</button>
              </div>

              <div style="display:flex; align-items:center; gap:0.5rem; color:var(--text-secondary); font-size:0.85rem;">
                <label for="page-limit-select">Show:</label>
                <select id="page-limit-select" class="form-input" style="padding:0.25rem 0.5rem; border-radius:8px; width:70px;">
                  <option value="5">5</option>
                  <option value="10" selected>10</option>
                  <option value="20">20</option>
                  <option value="50">50</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Customer Database Table -->
          <div class="admin-table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th style="cursor: pointer;" id="th-name">Customer Name <span id="sort-name-indicator">↕</span></th>
                  <th style="cursor: pointer;" id="th-email">Email <span id="sort-email-indicator">↕</span></th>
                  <th class="hide-mobile">Phone Number</th>
                  <th class="hide-mobile">Location</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="customers-list-tbody">
                <!-- Dynamically populated or skeleton loading rows -->
              </tbody>
            </table>
          </div>

          <!-- Pagination row -->
          <div class="pagination-wrapper">
            <div style="font-size:0.85rem; color:var(--text-secondary);" id="pagination-info">
              Showing 0 to 0 of 0 entries
            </div>
            <div class="pagination-buttons" id="pagination-buttons-container">
              <!-- Dynamically populated -->
            </div>
          </div>
        </div>
      </div>

      <!-- PANEL 3: SETTINGS -->
      <div class="admin-panel" id="settings-panel">
        <div class="admin-panel-header">
          <h2>WordPress API Connection Config</h2>
          <p>Configure the connection endpoints to sync with the Customer Manager backend.</p>
        </div>

        <div class="admin-panel-card" style="margin-top: 1.5rem; max-width: 600px;">
          <h3>Connection Parameters</h3>
          <form id="api-config-form" style="margin-top: 1rem;">
            <div class="form-group">
              <label for="config-base-url">REST API Base Path URL</label>
              <input type="url" id="config-base-url" class="form-input" value="https://rpsdigitalworld.store/wp-json/customer-manager/v1" required>
              <div class="form-tip">Must point to the custom REST namespace registered in WordPress.</div>
            </div>
            <button type="submit" class="auth-submit-btn" style="margin-top: 1rem;">Save & Test Connection</button>
          </form>
        </div>
      </div>

    </main>
  </section>

  <!-- SHARED MODALS BACKDROP CONTAINER -->
  <div class="modal-overlay" id="modal-overlay-container">
    
    <!-- 1. Add / Edit Customer Modal -->
    <div class="modal-content" id="customer-crud-modal" style="display:none;">
      <button class="modal-close-btn" id="close-crud-modal-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor"
          stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 6 6 18M6 6l12 12" />
        </svg>
      </button>
      <div style="padding: 2rem;">
        <h3 id="crud-modal-title" style="font-size: 1.4rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
          Add Customer
        </h3>
        <form id="customer-details-form">
          <input type="hidden" id="customer-id-input">
          
          <div style="display:flex; gap:1rem;">
            <div class="form-group" style="flex:1;">
              <label for="cust-first-name">First Name</label>
              <input type="text" id="cust-first-name" class="form-input" required minlength="2">
            </div>
            <div class="form-group" style="flex:1;">
              <label for="cust-last-name">Last Name</label>
              <input type="text" id="cust-last-name" class="form-input" required minlength="2">
            </div>
          </div>

          <div style="display:flex; gap:1rem;">
            <div class="form-group" style="flex:1.2;">
              <label for="cust-email">Email Address</label>
              <input type="email" id="cust-email" class="form-input" required>
            </div>
            <div class="form-group" style="flex:0.8;">
              <label for="cust-phone">Phone Number</label>
              <input type="text" id="cust-phone" class="form-input" required>
            </div>
          </div>

          <div class="form-group">
            <label for="cust-address">Street Address</label>
            <input type="text" id="cust-address" class="form-input" placeholder="e.g. Heritage Row 44">
          </div>

          <div style="display:flex; gap:1rem;">
            <div class="form-group" style="flex:1.2;">
              <label for="cust-city">City</label>
              <input type="text" id="cust-city" class="form-input" placeholder="e.g. Jodhpur">
            </div>
            <div class="form-group" style="flex:0.8;">
              <label for="cust-zip">Postal Code</label>
              <input type="text" id="cust-zip" class="form-input" placeholder="e.g. 342001">
            </div>
          </div>

          <div style="display:flex; gap:1rem;">
            <div class="form-group" style="flex:1;">
              <label for="cust-state">State</label>
              <input type="text" id="cust-state" class="form-input" placeholder="e.g. Rajasthan">
            </div>
            <div class="form-group" style="flex:1;">
              <label for="cust-country">Country</label>
              <input type="text" id="cust-country" class="form-input" placeholder="e.g. India">
            </div>
          </div>

          <div class="form-group">
            <label for="cust-status">Status</label>
            <select id="cust-status" class="form-input">
              <option value="ACTIVE">ACTIVE</option>
              <option value="INACTIVE">INACTIVE</option>
            </select>
          </div>

          <button type="submit" class="auth-submit-btn" id="crud-submit-btn" style="margin-top:0.5rem;">Save Customer</button>
        </form>
      </div>
    </div>

    <!-- 2. Import CSV Progress Modal -->
    <div class="modal-content" id="csv-import-results-modal" style="display:none; max-width: 500px;">
      <button class="modal-close-btn" id="close-import-modal-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor"
          stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 6 6 18M6 6l12 12" />
        </svg>
      </button>
      <div style="padding: 2rem;">
        <h3 style="font-size: 1.4rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
          CSV Import Results
        </h3>
        
        <div style="display:flex; flex-direction:column; gap:0.75rem;">
          <div style="display:flex; justify-content:space-between; font-size:1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
            <span>Successfully Imported:</span>
            <span id="import-success-count" style="color:var(--success); font-weight:700;">0</span>
          </div>
          <div style="display:flex; justify-content:space-between; font-size:1rem; padding-bottom: 0.5rem;">
            <span>Failed Records:</span>
            <span id="import-failed-count" style="color:var(--danger); font-weight:700;">0</span>
          </div>
          
          <div id="import-errors-wrapper" style="display:none;">
            <div class="import-errors-title">Encountered Issues:</div>
            <div class="import-errors-container" id="import-errors-list">
              <!-- Row-by-row error details -->
            </div>
          </div>
        </div>
        
        <button class="auth-submit-btn" id="import-ok-btn" style="margin-top: 1.5rem;">Acknowledge</button>
      </div>
    </div>

  </div>

  <script src="<?php echo plugin_dir_url(dirname(dirname(__FILE__)) . '/customer-manager.php') . 'assets/customer-management.js?v=' . (defined('CUSTOMER_MANAGER_VERSION') ? CUSTOMER_MANAGER_VERSION : time()); ?>"></script>
</body>

</html>
