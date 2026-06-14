<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FleetTrack Pro | Logistics Control Panel</title>
  <!-- Google Fonts Outfit -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="<?php echo plugin_dir_url(dirname(__FILE__)) . 'assets/fleet-style.css?v=' . (defined('FLEET_TRACK_VERSION') ? FLEET_TRACK_VERSION : time()); ?>">
</head>
<body>

  <!-- Top Notification Toast Container -->
  <div class="toast-container" id="toast-container"></div>

  <!-- 1. AUTHENTICATION LAYER -->
  <div id="auth-layer" class="auth-card-wrapper" style="display: none;">
    <div class="auth-card">
      <div class="auth-logo">
        <h2><span>FleetTrack</span> Pro</h2>
        <p>Logistics API Access Service</p>
      </div>

      <!-- Login Form -->
      <form id="panel-login-form">
        <h3 style="font-size: 1.25rem; margin-bottom: 1.25rem; font-weight:600;">Sign In</h3>
        <div class="form-group">
          <label for="login-username">Username or Email</label>
          <input type="text" id="login-username" class="form-input" placeholder="e.g. fleet_admin" required>
        </div>
        <div class="form-group">
          <label for="login-password">Password</label>
          <input type="password" id="login-password" class="form-input" placeholder="••••••••" required>
        </div>
        <button type="submit" class="auth-submit-btn" style="margin-top: 0.5rem;">Access Control Panel</button>
        <p class="auth-toggle-tip">
          Need an account? <a href="#" id="show-register-link">Register Here</a>
        </p>
      </form>

      <!-- Register Form -->
      <form id="panel-register-form" style="display: none;">
        <h3 style="font-size: 1.25rem; margin-bottom: 1.25rem; font-weight:600;">Create Account</h3>
        <div class="form-group">
          <label for="reg-username">Username</label>
          <input type="text" id="reg-username" class="form-input" placeholder="e.g. driver_seervi" required>
        </div>
        <div class="form-group">
          <label for="reg-name">Full Name</label>
          <input type="text" id="reg-name" class="form-input" placeholder="e.g. Ramesh Seervi" required>
        </div>
        <div class="form-group">
          <label for="reg-email">Email Address</label>
          <input type="email" id="reg-email" class="form-input" placeholder="e.g. ramesh@fleettrack.pro" required>
        </div>
        <div class="form-group">
          <label for="reg-password">Password</label>
          <input type="password" id="reg-password" class="form-input" placeholder="••••••••" required>
        </div>
        <div class="form-group">
          <label for="reg-role">Account Access Level</label>
          <select id="reg-role" class="form-input" required style="background-color: var(--bg-app);">
            <option value="fleet_super_admin">Super Admin (Full CRUD & Financials)</option>
            <option value="fleet_manager">Manager (Manage Drivers & Vehicles)</option>
            <option value="fleet_accountant">Accountant (Manage Expenses)</option>
            <option value="fleet_driver">Driver (View Assigned Trips & Documents)</option>
          </select>
        </div>
        <button type="submit" class="auth-submit-btn" style="margin-top: 0.5rem;">Register Account</button>
        <p class="auth-toggle-tip">
          Already have an account? <a href="#" id="show-login-link">Sign In</a>
        </p>
      </form>
    </div>
  </div>

  <!-- 2. MAIN PANEL LAYOUT -->
  <section class="admin-view-wrapper" id="panel-layer" style="display: none;">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <div>
        <div class="admin-logo">
          <span>FleetTrack</span> Pro
        </div>

        <nav class="admin-nav-links">
          <button class="admin-nav-link active" data-panel="overview-panel" id="nav-overview-btn">
            Overview Metrics
          </button>
          <button class="admin-nav-link" data-panel="vehicles-panel" id="nav-vehicles-btn">
            Vehicles List
          </button>
          <button class="admin-nav-link" data-panel="drivers-panel" id="nav-drivers-btn">
            Drivers List
          </button>
          <button class="admin-nav-link" data-panel="trips-panel" id="nav-trips-btn">
            Trips Registry
          </button>
          <button class="admin-nav-link" data-panel="expenses-panel" id="nav-expenses-btn">
            Fuel & Expenses
          </button>
          <button class="admin-nav-link" data-panel="reports-panel" id="nav-reports-btn">
            Reports & Profit/Loss
          </button>
        </nav>
      </div>

      <div>
        <!-- Logged user info summary -->
        <div style="padding: 1rem; border-top: 1px solid var(--border-color); font-size: 0.85rem; color: var(--text-secondary);">
          <div style="font-weight: 700; color: var(--text-primary);" id="user-display-name">Loading...</div>
          <div style="font-size: 0.75rem;" id="user-display-role">...</div>
        </div>

        <button class="admin-logout-btn" id="panel-logout-btn">
          Sign Out
        </button>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="admin-content-area">
      
      <!-- Top Connection status header -->
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div id="connection-status-badge" style="font-size: 0.85rem; font-weight:500; display:flex; align-items:center; gap:0.5rem; padding: 0.35rem 0.75rem; border-radius:99px; background: rgba(16, 185, 129, 0.1); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.2);">
          <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--success); display: inline-block;"></span>
          API Connection Stable
        </div>
      </div>

      <!-- PANEL 1: OVERVIEW METRICS -->
      <div class="admin-panel active" id="overview-panel">
        <div class="admin-panel-header">
          <h2>Overview Dashboard</h2>
          <p>Real-time fleet operations KPIs and analytics.</p>
        </div>

        <div class="admin-stats-grid">
          <div class="admin-panel-card">
            <h4 style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">Total Revenue</h4>
            <p id="stats-revenue" style="font-size:2.2rem; font-weight:800; color:var(--success); margin-top:0.5rem;">$0.00</p>
          </div>
          <div class="admin-panel-card">
            <h4 style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">Total Expenses</h4>
            <p id="stats-expenses" style="font-size:2.2rem; font-weight:800; color:var(--danger); margin-top:0.5rem;">$0.00</p>
          </div>
          <div class="admin-panel-card">
            <h4 style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">Net Profit</h4>
            <p id="stats-profit" style="font-size:2.2rem; font-weight:800; color:var(--primary); margin-top:0.5rem;">$0.00</p>
          </div>
        </div>

        <div class="admin-stats-grid">
          <div class="admin-panel-card" style="text-align: center;">
            <h4 style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">Total Vehicles</h4>
            <p id="stats-vehicles" style="font-size:2rem; font-weight:800; margin-top:0.5rem;">0</p>
          </div>
          <div class="admin-panel-card" style="text-align: center;">
            <h4 style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">Total Drivers</h4>
            <p id="stats-drivers" style="font-size:2rem; font-weight:800; margin-top:0.5rem;">0</p>
          </div>
          <div class="admin-panel-card" style="text-align: center;">
            <h4 style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">Total Trips</h4>
            <p id="stats-trips" style="font-size:2rem; font-weight:800; margin-top:0.5rem;">0</p>
          </div>
        </div>
      </div>

      <!-- PANEL 2: VEHICLES DIRECTORY -->
      <div class="admin-panel" id="vehicles-panel">
        <div class="admin-panel-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
          <div>
            <h2>Vehicles Inventory</h2>
            <p>Register, monitor, and update transport vehicles database.</p>
          </div>
          <button class="auth-submit-btn" id="add-vehicle-btn" style="width:auto; padding: 0.6rem 1.2rem;">+ Add Vehicle</button>
        </div>

        <div class="admin-panel-card">
          <div class="admin-table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Vehicle Number</th>
                  <th>Brand & Model</th>
                  <th>Type</th>
                  <th class="hide-mobile">Fuel Type</th>
                  <th class="hide-mobile">Permit Expiry</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="vehicles-list-tbody"></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- PANEL 3: DRIVERS DIRECTORY -->
      <div class="admin-panel" id="drivers-panel">
        <div class="admin-panel-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
          <div>
            <h2>Drivers Directory</h2>
            <p>Manage fleet operators, licenses, and joining profiles.</p>
          </div>
          <button class="auth-submit-btn" id="add-driver-btn" style="width:auto; padding: 0.6rem 1.2rem;">+ Add Driver</button>
        </div>

        <div class="admin-panel-card">
          <div class="admin-table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Operator Name</th>
                  <th>Phone Number</th>
                  <th class="hide-mobile">Email Address</th>
                  <th class="hide-mobile">License Number</th>
                  <th>Salary</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="drivers-list-tbody"></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- PANEL 4: TRIPS REGISTRY -->
      <div class="admin-panel" id="trips-panel">
        <div class="admin-panel-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
          <div>
            <h2>Trips Registry</h2>
            <p>Log transport journeys, odometer indexes, and revenue.</p>
          </div>
          <button class="auth-submit-btn" id="add-trip-btn" style="width:auto; padding: 0.6rem 1.2rem;">+ Create Trip</button>
        </div>

        <div class="admin-panel-card">
          <div class="admin-table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Vehicle</th>
                  <th>Driver</th>
                  <th class="hide-mobile">Route</th>
                  <th class="hide-mobile">Distance</th>
                  <th>Revenue</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="trips-list-tbody"></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- PANEL 5: EXPENSES -->
      <div class="admin-panel" id="expenses-panel">
        <div class="admin-panel-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
          <div>
            <h2>Fuel Logs & Expenses</h2>
            <p>Monitor operational costs and log specific fuel transactions.</p>
          </div>
          <div style="display:flex; gap: 0.5rem;">
            <button class="auth-submit-btn" id="log-fuel-btn" style="width:auto; padding: 0.6rem 1.2rem; background:transparent; border: 1px solid var(--border-color); color:var(--text-primary); box-shadow:none;">
              Log Fuel
            </button>
            <button class="auth-submit-btn" id="add-expense-btn" style="width:auto; padding: 0.6rem 1.2rem;">
              + Add Expense
            </button>
          </div>
        </div>

        <div class="admin-panel-card">
          <div class="admin-table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Vehicle</th>
                  <th>Category</th>
                  <th>Amount</th>
                  <th class="hide-mobile">Description</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="expenses-list-tbody"></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- PANEL 6: REPORTS & DYNAMIC ANALYSIS -->
      <div class="admin-panel" id="reports-panel">
        <div class="admin-panel-header">
          <h2>Financial Reports & P&L</h2>
          <p>Extract dynamic summaries of cost indicators, profitability, and cost per kilometer.</p>
        </div>

        <div class="admin-panel-card" style="margin-bottom: 2rem;">
          <form id="report-filter-form" style="display:flex; gap: 1rem; align-items: flex-end; flex-wrap:wrap;">
            <div class="form-group" style="margin-bottom:0; flex:1; min-width:150px;">
              <label for="rep-start-date">Start Date</label>
              <input type="date" id="rep-start-date" class="form-input" required>
            </div>
            <div class="form-group" style="margin-bottom:0; flex:1; min-width:150px;">
              <label for="rep-end-date">End Date</label>
              <input type="date" id="rep-end-date" class="form-input" required>
            </div>
            <button type="submit" class="auth-submit-btn" style="width:auto; padding:0.75rem 1.5rem;">Generate Report</button>
          </form>
        </div>

        <!-- Profit/Loss Statement Summary -->
        <div class="admin-panel-card" style="margin-bottom: 2rem;">
          <h3>Monthly Profit & Loss Breakdown</h3>
          <div class="admin-table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Month</th>
                  <th>Revenue</th>
                  <th>Expenses</th>
                  <th>Net Profit</th>
                </tr>
              </thead>
              <tbody id="report-pl-tbody"></tbody>
            </table>
          </div>
        </div>

        <!-- Vehicle Performance Report -->
        <div class="admin-panel-card">
          <h3>Vehicle Performance & CPK Summary</h3>
          <div class="admin-table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Vehicle Number</th>
                  <th>Trips</th>
                  <th>Distance (KM)</th>
                  <th>Revenue</th>
                  <th>Expenses</th>
                  <th>Net Profit</th>
                  <th>Cost / KM</th>
                </tr>
              </thead>
              <tbody id="report-vehicle-tbody"></tbody>
            </table>
          </div>
        </div>
      </div>

    </main>
  </section>

  <!-- 3. MODALS CONTAINER -->
  <div class="modal-overlay" id="modal-overlay">
    
    <!-- Vehicle Modal -->
    <div class="modal-content" id="vehicle-modal" style="display:none;">
      <button class="modal-close-btn" onclick="closeModal()">X</button>
      <div style="padding: 2.5rem;">
        <h3 id="vehicle-modal-title" style="margin-bottom:1.5rem; font-size:1.3rem;">Add Vehicle</h3>
        <form id="vehicle-form">
          <input type="hidden" id="vehicle-id">
          <div class="form-group">
            <label for="veh-number">Vehicle Number</label>
            <input type="text" id="veh-number" class="form-input" placeholder="e.g. MH-12-PQ-9999" required>
          </div>
          <div class="form-group">
            <label for="veh-type">Vehicle Type</label>
            <input type="text" id="veh-type" class="form-input" placeholder="e.g. Truck, Van, Tipper" required>
          </div>
          <div style="display:flex; gap:1rem;">
            <div class="form-group" style="flex:1;">
              <label for="veh-brand">Brand</label>
              <input type="text" id="veh-brand" class="form-input" required>
            </div>
            <div class="form-group" style="flex:1;">
              <label for="veh-model">Model</label>
              <input type="text" id="veh-model" class="form-input" required>
            </div>
          </div>
          <div style="display:flex; gap:1rem;">
            <div class="form-group" style="flex:1;">
              <label for="veh-year">Manufacturing Year</label>
              <input type="number" id="veh-year" class="form-input" required>
            </div>
            <div class="form-group" style="flex:1;">
              <label for="veh-fuel">Fuel Type</label>
              <select id="veh-fuel" class="form-input" style="background-color: var(--bg-app);">
                <option value="Diesel">Diesel</option>
                <option value="Petrol">Petrol</option>
                <option value="CNG">CNG</option>
                <option value="Electric">Electric</option>
              </select>
            </div>
          </div>
          <div style="display:flex; gap:1rem;">
            <div class="form-group" style="flex:1;">
              <label for="veh-insurance">Insurance Expiry</label>
              <input type="date" id="veh-insurance" class="form-input">
            </div>
            <div class="form-group" style="flex:1;">
              <label for="veh-permit">Permit Expiry</label>
              <input type="date" id="veh-permit" class="form-input">
            </div>
          </div>
          <button type="submit" class="auth-submit-btn" style="margin-top:1rem;">Save Vehicle</button>
        </form>
      </div>
    </div>

    <!-- Driver Modal -->
    <div class="modal-content" id="driver-modal" style="display:none;">
      <button class="modal-close-btn" onclick="closeModal()">X</button>
      <div style="padding: 2.5rem;">
        <h3 id="driver-modal-title" style="margin-bottom:1.5rem; font-size:1.3rem;">Add Driver</h3>
        <form id="driver-form">
          <input type="hidden" id="driver-id">
          <div class="form-group">
            <label for="drv-name">Driver Full Name</label>
            <input type="text" id="drv-name" class="form-input" required>
          </div>
          <div style="display:flex; gap:1rem;">
            <div class="form-group" style="flex:1;">
              <label for="drv-phone">Phone Number</label>
              <input type="text" id="drv-phone" class="form-input" required>
            </div>
            <div class="form-group" style="flex:1;">
              <label for="drv-email">Email Address</label>
              <input type="email" id="drv-email" class="form-input" required>
            </div>
          </div>
          <div class="form-group">
            <label for="drv-license">License Number</label>
            <input type="text" id="drv-license" class="form-input" required>
          </div>
          <div style="display:flex; gap:1rem;">
            <div class="form-group" style="flex:1;">
              <label for="drv-salary">Monthly Base Salary</label>
              <input type="number" id="drv-salary" class="form-input" required>
            </div>
            <div class="form-group" style="flex:1;">
              <label for="drv-license-exp">License Expiry</label>
              <input type="date" id="drv-license-exp" class="form-input">
            </div>
          </div>
          <button type="submit" class="auth-submit-btn" style="margin-top:1rem;">Save Driver</button>
        </form>
      </div>
    </div>

    <!-- Trip Modal -->
    <div class="modal-content" id="trip-modal" style="display:none;">
      <button class="modal-close-btn" onclick="closeModal()">X</button>
      <div style="padding: 2.5rem;">
        <h3 id="trip-modal-title" style="margin-bottom:1.5rem; font-size:1.3rem;">Create Trip</h3>
        <form id="trip-form">
          <input type="hidden" id="trip-id">
          <div class="form-group">
            <label for="trip-date">Trip Date</label>
            <input type="date" id="trip-date" class="form-input" required>
          </div>
          <div class="form-group">
            <label for="trip-vehicle">Vehicle</label>
            <select id="trip-vehicle" class="form-input" style="background-color: var(--bg-app);" required></select>
          </div>
          <div class="form-group">
            <label for="trip-driver">Driver</label>
            <select id="trip-driver" class="form-input" style="background-color: var(--bg-app);" required></select>
          </div>
          <div class="form-group">
            <label for="trip-route">Route</label>
            <select id="trip-route" class="form-input" style="background-color: var(--bg-app);" required></select>
          </div>
          <div style="display:flex; gap:1rem;">
            <div class="form-group" style="flex:1;">
              <label for="trip-start-km">Start KM</label>
              <input type="number" id="trip-start-km" class="form-input" required>
            </div>
            <div class="form-group" style="flex:1;">
              <label for="trip-end-km">End KM</label>
              <input type="number" id="trip-end-km" class="form-input" required>
            </div>
          </div>
          <div class="form-group">
            <label for="trip-revenue">Estimated Revenue</label>
            <input type="number" id="trip-revenue" class="form-input" required>
          </div>
          <div class="form-group">
            <label for="trip-status">Trip Status</label>
            <select id="trip-status" class="form-input" style="background-color: var(--bg-app);">
              <option value="PLANNED">PLANNED</option>
              <option value="ONGOING">ONGOING</option>
              <option value="COMPLETED">COMPLETED</option>
              <option value="CANCELLED">CANCELLED</option>
            </select>
          </div>
          <button type="submit" class="auth-submit-btn" style="margin-top:1rem;">Save Trip</button>
        </form>
      </div>
    </div>

    <!-- Expense Modal -->
    <div class="modal-content" id="expense-modal" style="display:none;">
      <button class="modal-close-btn" onclick="closeModal()">X</button>
      <div style="padding: 2.5rem;">
        <h3 id="expense-modal-title" style="margin-bottom:1.5rem; font-size:1.3rem;">Log Expense</h3>
        <form id="expense-form">
          <div class="form-group">
            <label for="exp-date">Expense Date</label>
            <input type="date" id="exp-date" class="form-input" required>
          </div>
          <div class="form-group">
            <label for="exp-vehicle">Related Vehicle</label>
            <select id="exp-vehicle" class="form-input" style="background-color: var(--bg-app);"></select>
          </div>
          <div class="form-group">
            <label for="exp-type">Expense Category</label>
            <select id="exp-type" class="form-input" style="background-color: var(--bg-app);" required>
              <option value="Maintenance">Maintenance</option>
              <option value="Toll">Toll Fees</option>
              <option value="Tyre">Tyre Replacement</option>
              <option value="Insurance">Insurance Renewal</option>
              <option value="Permit">Permits & Taxes</option>
              <option value="Salary">Driver Salary</option>
              <option value="Repair">Emergency Repairs</option>
              <option value="Parking">Parking Charges</option>
              <option value="Miscellaneous">Miscellaneous</option>
            </select>
          </div>
          <div class="form-group">
            <label for="exp-amount">Amount</label>
            <input type="number" id="exp-amount" class="form-input" required>
          </div>
          <div class="form-group">
            <label for="exp-desc">Description</label>
            <textarea id="exp-desc" class="form-input" rows="3"></textarea>
          </div>
          <button type="submit" class="auth-submit-btn" style="margin-top:1rem;">Save Expense</button>
        </form>
      </div>
    </div>

    <!-- Fuel Modal -->
    <div class="modal-content" id="fuel-modal" style="display:none;">
      <button class="modal-close-btn" onclick="closeModal()">X</button>
      <div style="padding: 2.5rem;">
        <h3 style="margin-bottom:1.5rem; font-size:1.3rem;">Log Fuel Transaction</h3>
        <form id="fuel-form">
          <div class="form-group">
            <label for="fuel-date">Purchase Date</label>
            <input type="date" id="fuel-date" class="form-input" required>
          </div>
          <div class="form-group">
            <label for="fuel-vehicle">Vehicle</label>
            <select id="fuel-vehicle" class="form-input" style="background-color: var(--bg-app);" required></select>
          </div>
          <div style="display:flex; gap:1rem;">
            <div class="form-group" style="flex:1;">
              <label for="fuel-qty">Quantity (Liters)</label>
              <input type="number" id="fuel-qty" class="form-input" step="0.01" required>
            </div>
            <div class="form-group" style="flex:1;">
              <label for="fuel-cost">Total Cost</label>
              <input type="number" id="fuel-cost" class="form-input" step="0.01" required>
            </div>
          </div>
          <div class="form-group">
            <label for="fuel-station">Gas Station Name</label>
            <input type="text" id="fuel-station" class="form-input" placeholder="e.g. Shell, HP Fuel">
          </div>
          <button type="submit" class="auth-submit-btn" style="margin-top:1rem;">Save Fuel Purchase</button>
        </form>
      </div>
    </div>

  </div>

  <script src="<?php echo plugin_dir_url(dirname(__FILE__)) . 'assets/fleet-dashboard.js?v=' . (defined('FLEET_TRACK_VERSION') ? FLEET_TRACK_VERSION : time()); ?>"></script>
</body>
</html>
