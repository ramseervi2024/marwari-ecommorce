// FleetTrack Pro Dashboard Client-Side Logic
let apiBaseUrl = 'https://rpsdigitalworld.store/wp-json/fleet-track/v1';
let token = localStorage.getItem('fleet_api_token') || null;
let currentUser = null;

try {
  const savedUser = localStorage.getItem('fleet_api_user');
  if (savedUser) {
    currentUser = JSON.parse(savedUser);
  }
} catch (e) {
  console.error("Failed to parse user session", e);
}

// UI notification helper
function showToast(message, type = 'success') {
  const container = document.getElementById('toast-container');
  if (!container) return;

  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `
    <div class="toast-message">${message}</div>
  `;
  container.appendChild(toast);

  setTimeout(() => {
    toast.style.animation = 'fadeOut 0.5s ease forwards';
    setTimeout(() => toast.remove(), 500);
  }, 4000);
}

// Make REST request wrapper
async function makeRequest(endpoint, options = {}) {
  options.headers = options.headers || {};
  options.headers['Content-Type'] = 'application/json';
  if (token) {
    options.headers['Authorization'] = `Bearer ${token}`;
  }

  const response = await fetch(`${apiBaseUrl}${endpoint}`, options);

  if ((response.status === 401 || response.status === 403) && !endpoint.startsWith('/auth/')) {
    handleLogout();
    showToast('Session expired. Please sign in.', 'danger');
    throw new Error('Unauthorized');
  }

  const result = await response.json();
  if (!response.ok) {
    const errorMsg = result.message || 'API request failed.';
    throw new Error(errorMsg);
  }
  return result;
}

// View Switches
function showLayer(layer) {
  const authLayer = document.getElementById('auth-layer');
  const panelLayer = document.getElementById('panel-layer');

  if (layer === 'auth') {
    authLayer.style.display = 'flex';
    panelLayer.style.display = 'none';
    panelLayer.classList.remove('active');
  } else {
    authLayer.style.display = 'none';
    panelLayer.style.display = '';
    panelLayer.classList.add('active');
  }
}

function showPanel(panelId) {
  const panels = document.querySelectorAll('.admin-panel');
  panels.forEach(p => p.classList.remove('active'));

  const targetPanel = document.getElementById(panelId);
  if (targetPanel) {
    targetPanel.classList.add('active');
  }

  const links = document.querySelectorAll('.admin-nav-link');
  links.forEach(l => {
    if (l.getAttribute('data-panel') === panelId) {
      l.classList.add('active');
    } else {
      l.classList.remove('active');
    }
  });

  // Load panel specific data
  if (panelId === 'overview-panel') {
    fetchDashboardStats();
  } else if (panelId === 'vehicles-panel') {
    fetchVehicles();
  } else if (panelId === 'drivers-panel') {
    fetchDrivers();
  } else if (panelId === 'trips-panel') {
    fetchTrips();
  } else if (panelId === 'expenses-panel') {
    fetchExpenses();
  }
}

// Render dynamic user info and restrict capabilities
function configureRoleAccess() {
  if (!currentUser) return;
  
  const role = currentUser.role || 'fleet_driver';
  document.getElementById('user-display-name').textContent = currentUser.name;
  document.getElementById('user-display-role').textContent = role.replace('fleet_', '').toUpperCase();

  // Hide nav panels for Driver role
  const driverOnly = (role === 'fleet_driver');
  const isAccountant = (role === 'fleet_accountant');
  
  document.getElementById('nav-overview-btn').style.display = driverOnly ? 'none' : 'flex';
  document.getElementById('nav-reports-btn').style.display = (driverOnly || isAccountant) ? 'none' : 'flex';
  
  // Show vehicles and drivers links
  document.getElementById('nav-vehicles-btn').style.display = driverOnly ? 'none' : 'flex';
  document.getElementById('nav-drivers-btn').style.display = driverOnly ? 'none' : 'flex';

  // Default panel view
  if (driverOnly) {
    showPanel('trips-panel');
  } else {
    showPanel('overview-panel');
  }
}

// Authentication Logic
async function handleLogin(e) {
  e.preventDefault();
  const username = document.getElementById('login-username').value.trim();
  const password = document.getElementById('login-password').value;

  try {
    const res = await makeRequest('/auth/login', {
      method: 'POST',
      body: JSON.stringify({ username, password })
    });

    if (res.success && res.data.token) {
      token = res.data.token;
      currentUser = res.data.user;
      localStorage.setItem('fleet_api_token', token);
      localStorage.setItem('fleet_api_user', JSON.stringify(currentUser));
      
      showToast(`Welcome back, ${currentUser.name}!`);
      showLayer('panel');
      configureRoleAccess();
    }
  } catch (err) {
    showToast(err.message, 'danger');
  }
}

async function handleRegister(e) {
  e.preventDefault();
  const username = document.getElementById('reg-username').value.trim();
  const name = document.getElementById('reg-name').value.trim();
  const email = document.getElementById('reg-email').value.trim();
  const password = document.getElementById('reg-password').value;
  const role = document.getElementById('reg-role').value;

  try {
    const res = await makeRequest('/auth/register', {
      method: 'POST',
      body: JSON.stringify({ username, name, email, password, role })
    });

    if (res.success) {
      showToast('Account registered successfully! Please sign in.');
      document.getElementById('panel-register-form').style.display = 'none';
      document.getElementById('panel-login-form').style.display = 'block';
    }
  } catch (err) {
    showToast(err.message, 'danger');
  }
}

function handleLogout() {
  token = null;
  currentUser = null;
  localStorage.removeItem('fleet_api_token');
  localStorage.removeItem('fleet_api_user');
  showLayer('auth');
}

// Fetch dashboard KPIs
async function fetchDashboardStats() {
  try {
    const res = await makeRequest('/dashboard');
    if (res.success && res.data.cards) {
      const c = res.data.cards;
      document.getElementById('stats-revenue').textContent = `$${c.total_revenue.toFixed(2)}`;
      document.getElementById('stats-expenses').textContent = `$${c.total_expenses.toFixed(2)}`;
      document.getElementById('stats-profit').textContent = `$${c.total_profit.toFixed(2)}`;
      document.getElementById('stats-vehicles').textContent = c.total_vehicles;
      document.getElementById('stats-drivers').textContent = c.total_drivers;
      document.getElementById('stats-trips').textContent = c.total_trips;
    }
  } catch (err) {
    showToast('Failed to load KPIs summary.', 'danger');
  }
}

// Vehicles CRUD
async function fetchVehicles() {
  const tbody = document.getElementById('vehicles-list-tbody');
  tbody.innerHTML = '<tr><td colspan="7">Loading vehicles...</td></tr>';
  
  try {
    const res = await makeRequest('/vehicles');
    if (res.success && res.data.data) {
      tbody.innerHTML = '';
      if (res.data.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7">No vehicles registered.</td></tr>';
        return;
      }
      res.data.data.forEach(v => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td style="font-weight:700; color:var(--primary);">${v.vehicle_number}</td>
          <td>${v.vehicle_brand} ${v.vehicle_model}</td>
          <td>${v.vehicle_type}</td>
          <td class="hide-mobile">${v.fuel_type}</td>
          <td class="hide-mobile">${v.permit_expiry || 'N/A'}</td>
          <td><span class="badge-status ${v.status.toLowerCase()}">${v.status}</span></td>
          <td>
            <button onclick="editVehicle(${v.id})" style="color:var(--primary); margin-right:8px;">Edit</button>
            <button onclick="deleteVehicle(${v.id})" style="color:var(--danger);">Delete</button>
          </td>
        `;
        tbody.appendChild(tr);
      });
    }
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="7">Failed to load vehicles.</td></tr>';
  }
}

// Drivers CRUD
async function fetchDrivers() {
  const tbody = document.getElementById('drivers-list-tbody');
  tbody.innerHTML = '<tr><td colspan="7">Loading drivers...</td></tr>';
  
  try {
    const res = await makeRequest('/drivers');
    if (res.success && res.data.data) {
      tbody.innerHTML = '';
      if (res.data.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7">No drivers registered.</td></tr>';
        return;
      }
      res.data.data.forEach(d => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td style="font-weight:700;">${d.name}</td>
          <td>${d.phone}</td>
          <td class="hide-mobile">${d.email}</td>
          <td class="hide-mobile">${d.license_number}</td>
          <td>$${parseFloat(d.salary).toFixed(2)}</td>
          <td><span class="badge-status ${d.status.toLowerCase()}">${d.status}</span></td>
          <td>
            <button onclick="editDriver(${d.id})" style="color:var(--primary); margin-right:8px;">Edit</button>
            <button onclick="deleteDriver(${d.id})" style="color:var(--danger);">Delete</button>
          </td>
        `;
        tbody.appendChild(tr);
      });
    }
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="7">Failed to load drivers.</td></tr>';
  }
}

// Trips CRUD
async function fetchTrips() {
  const tbody = document.getElementById('trips-list-tbody');
  tbody.innerHTML = '<tr><td colspan="8">Loading trips registry...</td></tr>';
  
  try {
    const res = await makeRequest('/trips');
    if (res.success && res.data.data) {
      tbody.innerHTML = '';
      if (res.data.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8">No trips registered.</td></tr>';
        return;
      }
      res.data.data.forEach(t => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${t.trip_date}</td>
          <td style="font-weight:700;">${t.vehicle_number || 'N/A'}</td>
          <td>${t.driver_name || 'N/A'}</td>
          <td class="hide-mobile">${t.route_name || 'N/A'}</td>
          <td class="hide-mobile">${parseFloat(t.distance_travelled).toFixed(1)} KM</td>
          <td style="color:var(--success); font-weight:700;">$${parseFloat(t.revenue).toFixed(2)}</td>
          <td><span class="badge-status ${t.status.toLowerCase()}">${t.status}</span></td>
          <td>
            <button onclick="editTrip(${t.id})" style="color:var(--primary); margin-right:8px;">Update</button>
            <button onclick="deleteTrip(${t.id})" style="color:var(--danger);">Delete</button>
          </td>
        `;
        tbody.appendChild(tr);
      });
    }
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="8">Failed to load trips.</td></tr>';
  }
}

// Expenses & Fuel Logs
async function fetchExpenses() {
  const tbody = document.getElementById('expenses-list-tbody');
  tbody.innerHTML = '<tr><td colspan="6">Loading expenses...</td></tr>';
  
  try {
    const res = await makeRequest('/expenses');
    if (res.success && res.data.data) {
      tbody.innerHTML = '';
      if (res.data.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6">No expenses logged.</td></tr>';
        return;
      }
      res.data.data.forEach(e => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${e.expense_date}</td>
          <td>${e.vehicle_number || 'Global'}</td>
          <td><span class="badge-status pending">${e.expense_type}</span></td>
          <td style="color:var(--danger); font-weight:700;">$${parseFloat(e.amount).toFixed(2)}</td>
          <td class="hide-mobile">${e.description || 'N/A'}</td>
          <td>
            <button onclick="deleteExpense(${e.id})" style="color:var(--danger);">Delete</button>
          </td>
        `;
        tbody.appendChild(tr);
      });
    }
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="6">Failed to load expenses.</td></tr>';
  }
}

// Modal closing helper
function closeModal() {
  document.getElementById('modal-overlay').classList.remove('active');
  document.querySelectorAll('.modal-content').forEach(m => m.style.display = 'none');
}

// Open modals helper
function openModal(id) {
  document.getElementById('modal-overlay').classList.add('active');
  document.getElementById(id).style.display = 'block';
}

// CRUD Operations Action triggers
window.editVehicle = async function(id) {
  try {
    const res = await makeRequest(`/vehicles/${id}`);
    if (res.success && res.data) {
      const v = res.data;
      document.getElementById('vehicle-id').value = v.id;
      document.getElementById('veh-number').value = v.vehicle_number;
      document.getElementById('veh-type').value = v.vehicle_type;
      document.getElementById('veh-brand').value = v.vehicle_brand;
      document.getElementById('veh-model').value = v.vehicle_model;
      document.getElementById('veh-year').value = v.vehicle_year;
      document.getElementById('veh-fuel').value = v.fuel_type;
      document.getElementById('veh-insurance').value = v.insurance_expiry || '';
      document.getElementById('veh-permit').value = v.permit_expiry || '';
      
      document.getElementById('vehicle-modal-title').textContent = 'Edit Vehicle';
      openModal('vehicle-modal');
    }
  } catch (err) {
    showToast(err.message, 'danger');
  }
};

window.deleteVehicle = async function(id) {
  if (confirm('Delete this vehicle?')) {
    try {
      const res = await makeRequest(`/vehicles/${id}`, { method: 'DELETE' });
      if (res.success) {
        showToast('Vehicle deleted successfully');
        fetchVehicles();
      }
    } catch (err) {
      showToast(err.message, 'danger');
    }
  }
};

window.editDriver = async function(id) {
  try {
    const res = await makeRequest(`/drivers/${id}`);
    if (res.success && res.data) {
      const d = res.data;
      document.getElementById('driver-id').value = d.id;
      document.getElementById('drv-name').value = d.name;
      document.getElementById('drv-phone').value = d.phone;
      document.getElementById('drv-email').value = d.email;
      document.getElementById('drv-license').value = d.license_number;
      document.getElementById('drv-salary').value = d.salary;
      document.getElementById('drv-license-exp').value = d.license_expiry || '';
      
      document.getElementById('driver-modal-title').textContent = 'Edit Driver';
      openModal('driver-modal');
    }
  } catch (err) {
    showToast(err.message, 'danger');
  }
};

window.deleteDriver = async function(id) {
  if (confirm('Delete this driver operator?')) {
    try {
      const res = await makeRequest(`/drivers/${id}`, { method: 'DELETE' });
      if (res.success) {
        showToast('Driver deleted successfully');
        fetchDrivers();
      }
    } catch (err) {
      showToast(err.message, 'danger');
    }
  }
};

// Populate selectors for trips modal
async function populateSelectors() {
  const vehSel = document.getElementById('trip-vehicle');
  const drvSel = document.getElementById('trip-driver');
  const routeSel = document.getElementById('trip-route');
  
  // Reusable selector for expense logs
  const expVehSel = document.getElementById('exp-vehicle');
  const fuelVehSel = document.getElementById('fuel-vehicle');
  
  try {
    // Vehicles
    const vRes = await makeRequest('/vehicles');
    vehSel.innerHTML = '';
    expVehSel.innerHTML = '<option value="">No Vehicle (Global)</option>';
    fuelVehSel.innerHTML = '';
    if (vRes.success && vRes.data.data) {
      vRes.data.data.forEach(v => {
        const op = `<option value="${v.id}">${v.vehicle_number} (${v.vehicle_brand})</option>`;
        vehSel.innerHTML += op;
        expVehSel.innerHTML += op;
        fuelVehSel.innerHTML += op;
      });
    }
    
    // Drivers
    const dRes = await makeRequest('/drivers');
    drvSel.innerHTML = '';
    if (dRes.success && dRes.data.data) {
      dRes.data.data.forEach(d => {
        drvSel.innerHTML += `<option value="${d.id}">${d.name}</option>`;
      });
    }

    // Routes
    const rRes = await makeRequest('/routes');
    routeSel.innerHTML = '';
    if (rRes.success && rRes.data.data) {
      rRes.data.data.forEach(r => {
        routeSel.innerHTML += `<option value="${r.id}">${r.route_name} (${r.distance_km} KM)</option>`;
      });
    }
  } catch (e) {
    console.error("Failed to populate dropdown selectors", e);
  }
}

window.editTrip = async function(id) {
  try {
    await populateSelectors();
    const res = await makeRequest(`/trips/${id}`);
    if (res.success && res.data) {
      const t = res.data;
      document.getElementById('trip-id').value = t.id;
      document.getElementById('trip-date').value = t.trip_date;
      document.getElementById('trip-vehicle').value = t.vehicle_id;
      document.getElementById('trip-driver').value = t.driver_id;
      document.getElementById('trip-route').value = t.route_id;
      document.getElementById('trip-start-km').value = t.start_km;
      document.getElementById('trip-end-km').value = t.end_km;
      document.getElementById('trip-revenue').value = t.revenue;
      document.getElementById('trip-status').value = t.status;
      
      document.getElementById('trip-modal-title').textContent = 'Update Trip Status';
      openModal('trip-modal');
    }
  } catch (err) {
    showToast(err.message, 'danger');
  }
};

window.deleteTrip = async function(id) {
  if (confirm('Delete this trip log?')) {
    try {
      const res = await makeRequest(`/trips/${id}`, { method: 'DELETE' });
      if (res.success) {
        showToast('Trip log deleted successfully');
        fetchTrips();
      }
    } catch (err) {
      showToast(err.message, 'danger');
    }
  }
};

window.deleteExpense = async function(id) {
  if (confirm('Remove this expense entry?')) {
    try {
      const res = await makeRequest(`/expenses/${id}`, { method: 'DELETE' });
      if (res.success) {
        showToast('Expense log removed successfully');
        fetchExpenses();
      }
    } catch (err) {
      showToast(err.message, 'danger');
    }
  }
};

// Form Saves handlers
async function saveVehicle(e) {
  e.preventDefault();
  const id = document.getElementById('vehicle-id').value;
  const payload = {
    vehicle_number: document.getElementById('veh-number').value.trim(),
    vehicle_type: document.getElementById('veh-type').value.trim(),
    vehicle_brand: document.getElementById('veh-brand').value.trim(),
    vehicle_model: document.getElementById('veh-model').value.trim(),
    vehicle_year: parseInt(document.getElementById('veh-year').value),
    fuel_type: document.getElementById('veh-fuel').value,
    insurance_expiry: document.getElementById('veh-insurance').value || null,
    permit_expiry: document.getElementById('veh-permit').value || null
  };

  try {
    const method = id ? 'PUT' : 'POST';
    const endpoint = id ? `/vehicles/${id}` : '/vehicles';
    const res = await makeRequest(endpoint, {
      method: method,
      body: JSON.stringify(payload)
    });

    if (res.success) {
      showToast(id ? 'Vehicle details updated!' : 'Vehicle registered successfully!');
      closeModal();
      fetchVehicles();
    }
  } catch (err) {
    showToast(err.message, 'danger');
  }
}

async function saveDriver(e) {
  e.preventDefault();
  const id = document.getElementById('driver-id').value;
  const payload = {
    name: document.getElementById('drv-name').value.trim(),
    phone: document.getElementById('drv-phone').value.trim(),
    email: document.getElementById('drv-email').value.trim(),
    license_number: document.getElementById('drv-license').value.trim(),
    salary: parseFloat(document.getElementById('drv-salary').value),
    license_expiry: document.getElementById('drv-license-exp').value || null
  };

  try {
    const method = id ? 'PUT' : 'POST';
    const endpoint = id ? `/drivers/${id}` : '/drivers';
    const res = await makeRequest(endpoint, {
      method: method,
      body: JSON.stringify(payload)
    });

    if (res.success) {
      showToast(id ? 'Driver details updated!' : 'Driver operator registered successfully!');
      closeModal();
      fetchDrivers();
    }
  } catch (err) {
    showToast(err.message, 'danger');
  }
}

async function saveTrip(e) {
  e.preventDefault();
  const id = document.getElementById('trip-id').value;
  const payload = {
    trip_date: document.getElementById('trip-date').value,
    vehicle_id: parseInt(document.getElementById('trip-vehicle').value),
    driver_id: parseInt(document.getElementById('trip-driver').value),
    route_id: parseInt(document.getElementById('trip-route').value),
    start_km: parseFloat(document.getElementById('trip-start-km').value),
    end_km: parseFloat(document.getElementById('trip-end-km').value),
    revenue: parseFloat(document.getElementById('trip-revenue').value),
    status: document.getElementById('trip-status').value
  };

  try {
    const method = id ? 'PUT' : 'POST';
    const endpoint = id ? `/trips/${id}` : '/trips';
    const res = await makeRequest(endpoint, {
      method: method,
      body: JSON.stringify(payload)
    });

    if (res.success) {
      showToast(id ? 'Trip status updated!' : 'Trip registered successfully!');
      closeModal();
      fetchTrips();
    }
  } catch (err) {
    showToast(err.message, 'danger');
  }
}

async function saveExpense(e) {
  e.preventDefault();
  const veh = document.getElementById('exp-vehicle').value;
  const payload = {
    expense_date: document.getElementById('exp-date').value,
    vehicle_id: veh ? parseInt(veh) : null,
    expense_type: document.getElementById('exp-type').value,
    amount: parseFloat(document.getElementById('exp-amount').value),
    description: document.getElementById('exp-desc').value.trim()
  };

  try {
    const res = await makeRequest('/expenses', {
      method: 'POST',
      body: JSON.stringify(payload)
    });

    if (res.success) {
      showToast('Expense logged successfully!');
      closeModal();
      fetchExpenses();
    }
  } catch (err) {
    showToast(err.message, 'danger');
  }
}

async function saveFuel(e) {
  e.preventDefault();
  const payload = {
    fuel_date: document.getElementById('fuel-date').value,
    vehicle_id: parseInt(document.getElementById('fuel-vehicle').value),
    fuel_quantity: parseFloat(document.getElementById('fuel-qty').value),
    fuel_cost: parseFloat(document.getElementById('fuel-cost').value),
    fuel_station: document.getElementById('fuel-station').value.trim()
  };

  try {
    const res = await makeRequest('/fuel', {
      method: 'POST',
      body: JSON.stringify(payload)
    });

    if (res.success) {
      showToast('Fuel log transaction recorded!');
      closeModal();
      fetchExpenses();
    }
  } catch (err) {
    showToast(err.message, 'danger');
  }
}

// Generate Reports
async function generateReport(e) {
  e.preventDefault();
  const start = document.getElementById('rep-start-date').value;
  const end = document.getElementById('rep-end-date').value;

  const plTbody = document.getElementById('report-pl-tbody');
  const vehTbody = document.getElementById('report-vehicle-tbody');
  
  plTbody.innerHTML = '<tr><td colspan="4">Generating statement...</td></tr>';
  vehTbody.innerHTML = '<tr><td colspan="7">Generating metrics...</td></tr>';

  try {
    // 1. P&L
    const plRes = await makeRequest(`/reports/profit-loss?start_date=${start}&end_date=${end}`);
    if (plRes.success && plRes.data) {
      plTbody.innerHTML = '';
      if (plRes.data.length === 0) {
        plTbody.innerHTML = '<tr><td colspan="4">No entries in date range.</td></tr>';
      } else {
        plRes.data.forEach(item => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td style="font-weight:700;">${item.month}</td>
            <td style="color:var(--success); font-weight:700;">$${item.revenue.toFixed(2)}</td>
            <td style="color:var(--danger); font-weight:700;">$${item.expenses.toFixed(2)}</td>
            <td style="color:var(--primary); font-weight:800;">$${item.profit.toFixed(2)}</td>
          `;
          plTbody.appendChild(tr);
        });
      }
    }

    // 2. Vehicle Report
    const vRes = await makeRequest(`/reports/vehicle?start_date=${start}&end_date=${end}`);
    if (vRes.success && vRes.data) {
      vehTbody.innerHTML = '';
      if (vRes.data.length === 0) {
        vehTbody.innerHTML = '<tr><td colspan="7">No data available.</td></tr>';
      } else {
        vRes.data.forEach(item => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td style="font-weight:700; color:var(--primary);">${item.vehicle_number}</td>
            <td>${item.trips_count}</td>
            <td>${item.total_distance_km.toFixed(1)} KM</td>
            <td style="color:var(--success);">$${item.revenue.toFixed(2)}</td>
            <td style="color:var(--danger);">$${item.expenses.toFixed(2)}</td>
            <td style="font-weight:700;">$${item.profit.toFixed(2)}</td>
            <td style="color:var(--text-muted); font-weight:700;">$${item.cost_per_km.toFixed(2)} / KM</td>
          `;
          vehTbody.appendChild(tr);
        });
      }
    }
  } catch (err) {
    showToast(err.message, 'danger');
  }
}

// Bind event listeners
function initEventListeners() {
  document.getElementById('panel-login-form').addEventListener('submit', handleLogin);
  document.getElementById('panel-register-form').addEventListener('submit', handleRegister);
  
  document.getElementById('show-register-link').addEventListener('click', (e) => {
    e.preventDefault();
    document.getElementById('panel-login-form').style.display = 'none';
    document.getElementById('panel-register-form').style.display = 'block';
  });

  document.getElementById('show-login-link').addEventListener('click', (e) => {
    e.preventDefault();
    document.getElementById('panel-register-form').style.display = 'none';
    document.getElementById('panel-login-form').style.display = 'block';
  });

  document.getElementById('panel-logout-btn').addEventListener('click', () => {
    handleLogout();
    showToast('Signed out of session.');
  });

  // Sidebar navigations
  document.querySelectorAll('.admin-nav-link').forEach(link => {
    link.addEventListener('click', () => {
      showPanel(link.getAttribute('data-panel'));
    });
  });

  // Modals Open
  document.getElementById('add-vehicle-btn').addEventListener('click', () => {
    document.getElementById('vehicle-form').reset();
    document.getElementById('vehicle-id').value = '';
    document.getElementById('vehicle-modal-title').textContent = 'Add Vehicle';
    openModal('vehicle-modal');
  });

  document.getElementById('add-driver-btn').addEventListener('click', () => {
    document.getElementById('driver-form').reset();
    document.getElementById('driver-id').value = '';
    document.getElementById('driver-modal-title').textContent = 'Add Driver';
    openModal('driver-modal');
  });

  document.getElementById('add-trip-btn').addEventListener('click', async () => {
    document.getElementById('trip-form').reset();
    document.getElementById('trip-id').value = '';
    document.getElementById('trip-modal-title').textContent = 'Create Trip';
    await populateSelectors();
    openModal('trip-modal');
  });

  document.getElementById('add-expense-btn').addEventListener('click', async () => {
    document.getElementById('expense-form').reset();
    await populateSelectors();
    openModal('expense-modal');
  });

  document.getElementById('log-fuel-btn').addEventListener('click', async () => {
    document.getElementById('fuel-form').reset();
    await populateSelectors();
    openModal('fuel-modal');
  });

  // Form submits
  document.getElementById('vehicle-form').addEventListener('submit', saveVehicle);
  document.getElementById('driver-form').addEventListener('submit', saveDriver);
  document.getElementById('trip-form').addEventListener('submit', saveTrip);
  document.getElementById('expense-form').addEventListener('submit', saveExpense);
  document.getElementById('fuel-form').addEventListener('submit', saveFuel);
  
  // Reports form
  document.getElementById('report-filter-form').addEventListener('submit', generateReport);
}

// App Initialization
async function initApp() {
  initEventListeners();

  // Set default report date parameters (last 30 days)
  const today = new Date().toISOString().split('T')[0];
  const lastMonth = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
  document.getElementById('rep-start-date').value = lastMonth;
  document.getElementById('rep-end-date').value = today;

  if (token) {
    try {
      const res = await makeRequest('/auth/me');
      if (res.success && res.data) {
        currentUser = res.data;
        showLayer('panel');
        configureRoleAccess();
      }
    } catch (err) {
      handleLogout();
    }
  } else {
    showLayer('auth');
  }
}

document.addEventListener('DOMContentLoaded', initApp);
