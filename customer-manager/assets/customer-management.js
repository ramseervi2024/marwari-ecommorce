// Customer Management Panel Application Logic

// Global state
let apiBaseUrl = localStorage.getItem('marwari_customer_api_url') || 'https://rpsdigitalworld.store/wp-json/customer-manager/v1';
let token = localStorage.getItem('marwari_customer_api_token') || null;
let currentUser = null;
try {
  const savedUser = localStorage.getItem('marwari_customer_api_user');
  if (savedUser) {
    currentUser = JSON.parse(savedUser);
  }
} catch (e) {
  console.error("Failed to parse user details", e);
}

let customersList = [];
let pagination = { total: 0, page: 1, limit: 10, pages: 0 };
let filters = {
  search: '',
  status: 'all',
  limit: 10,
  page: 1,
  sort: 'first_name',
  order: 'asc'
};

// UI helper to show toast notifications
function showToast(message, type = 'success') {
  const container = document.getElementById('toast-container');
  if (!container) return;

  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `
    <div class="toast-message">${message}</div>
  `;
  container.appendChild(toast);

  // Auto remove toast after 4 seconds
  setTimeout(() => {
    toast.style.animation = 'fadeOut 0.5s ease forwards';
    setTimeout(() => toast.remove(), 500);
  }, 4000);
}

// Check connection to WP API
async function checkApiConnection() {
  const badge = document.getElementById('connection-status-badge');
  try {
    const res = await fetch(`${apiBaseUrl}/customers`, { method: 'OPTIONS' });
    badge.className = 'connection-status';
    badge.innerHTML = `
      <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--success); display: inline-block;"></span>
      API Connection Stable
    `;
    return true;
  } catch (err) {
    badge.className = 'connection-status offline';
    badge.innerHTML = `
      <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--danger); display: inline-block;"></span>
      API Connection Offline
    `;
    return false;
  }
}

// Wrapper for REST requests
async function makeRequest(endpoint, options = {}) {
  options.headers = options.headers || {};
  if (!(options.body instanceof FormData)) {
    options.headers['Content-Type'] = 'application/json';
  }
  if (token) {
    options.headers['Authorization'] = `Bearer ${token}`;
  }

  const response = await fetch(`${apiBaseUrl}${endpoint}`, options);

  // Handle unauthorized or expired session
  if ((response.status === 401 || response.status === 403) && 
      !endpoint.startsWith('/auth/login') && 
      !endpoint.startsWith('/auth/register')) {
    handleLogout();
    showToast('Session expired or permissions modified. Please sign in.', 'danger');
    throw new Error('Unauthorized');
  }

  // Handle binary exports
  const contentType = response.headers.get('content-type');
  if (contentType && contentType.includes('text/csv')) {
    return response.blob();
  }

  const result = await response.json();
  if (!response.ok) {
    const errorMsg = result.message || 'REST API request failed.';
    throw new Error(errorMsg);
  }
  return result;
}

// Theme handling
function initTheme() {
  const isLight = localStorage.getItem("light_mode") === "true";
  const body = document.body;
  if (isLight) {
    body.classList.add("light-mode");
  } else {
    body.classList.remove("light-mode");
  }
}

function toggleTheme() {
  const body = document.body;
  body.classList.toggle("light-mode");
  const isLight = body.classList.contains("light-mode");
  localStorage.setItem("light_mode", isLight);
  showToast(isLight ? "Switched to Royal Light Theme" : "Switched to Luxury Dark Theme", 'info');
}

// View switches
function showLayer(layer) {
  const authLayer = document.getElementById('auth-layer');
  const panelLayer = document.getElementById('panel-layer');

  if (layer === 'auth') {
    authLayer.style.display = 'flex';
    panelLayer.style.display = 'none';
  } else {
    authLayer.style.display = 'none';
    panelLayer.style.display = 'block';
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
    if (l.dataset.panel === panelId) {
      l.classList.add('active');
    } else {
      l.classList.remove('active');
    }
  });

  // Load panel-specific data
  if (panelId === 'overview-panel') {
    fetchDashboardStats();
  } else if (panelId === 'customers-panel') {
    fetchCustomers();
  }
}

// Render dynamic user details and configure sidebar roles permissions
function configureRoleAccess() {
  if (!currentUser) return;

  const role = currentUser.role || 'api_viewer';
  const nameBox = document.getElementById('user-display-name');
  const roleBox = document.getElementById('user-display-role');
  const avatarBox = document.getElementById('user-avatar-initials');

  nameBox.textContent = currentUser.name || currentUser.username;
  roleBox.textContent = role.replace('api_', '').toUpperCase();
  avatarBox.textContent = (currentUser.name || currentUser.username || 'U').charAt(0).toUpperCase();

  // Settings Details
  document.getElementById('details-username').textContent = `${currentUser.name} (${currentUser.username})`;
  document.getElementById('details-api-url').textContent = apiBaseUrl;

  const caps = [];
  if (role === 'api_super_admin' || role === 'administrator') caps.push('Full Dashboard', 'Customer CRUD', 'Import CSV', 'Export CSV');
  else if (role === 'api_manager') caps.push('Customer Read/Write', 'Modal Add & Edit');
  else caps.push('Customer Directory Read-Only');
  document.getElementById('details-capabilities').textContent = caps.join(', ');

  // Sidebar link visibility
  const overviewBtn = document.getElementById('sidebar-overview-btn');
  const hasStatsAccess = (role === 'api_super_admin' || role === 'administrator');

  if (hasStatsAccess) {
    overviewBtn.style.display = 'flex';
  } else {
    overviewBtn.style.display = 'none';
  }

  // Directory buttons visibility
  const addBtn = document.getElementById('add-customer-trigger-btn');
  const expBtn = document.getElementById('export-csv-btn');
  const impBtn = document.getElementById('import-csv-trigger-btn');

  const canEdit = (role === 'api_super_admin' || role === 'api_manager' || role === 'administrator');
  const canBulk = (role === 'api_super_admin' || role === 'administrator');

  addBtn.style.display = canEdit ? 'flex' : 'none';
  expBtn.style.display = canBulk ? 'flex' : 'none';
  impBtn.style.display = canBulk ? 'flex' : 'none';

  // Default view
  if (hasStatsAccess) {
    showPanel('overview-panel');
  } else {
    showPanel('customers-panel');
  }
}

// Auth functions
async function handleLogin(e) {
  e.preventDefault();
  const username = document.getElementById('login-username').value.trim();
  const password = document.getElementById('login-user-pass').value;

  try {
    const res = await makeRequest('/auth/login', {
      method: 'POST',
      body: JSON.stringify({ username, password })
    });

    if (res.success && res.data.token) {
      token = res.data.token;
      currentUser = res.data.user;
      localStorage.setItem('marwari_customer_api_token', token);
      localStorage.setItem('marwari_customer_api_user', JSON.stringify(currentUser));
      
      showToast(`Welcome to Marwari Clients Panel, ${currentUser.name}!`);
      showLayer('panel');
      configureRoleAccess();
      document.getElementById('panel-login-form').reset();
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
      showToast('API User Registered Successfully! Please sign in.');
      document.getElementById('panel-register-form').reset();
      document.getElementById('show-login-link').click();
    }
  } catch (err) {
    showToast(err.message, 'danger');
  }
}

function handleLogout() {
  token = null;
  currentUser = null;
  localStorage.removeItem('marwari_customer_api_token');
  localStorage.removeItem('marwari_customer_api_user');
  showLayer('auth');
}

// Fetch dashboard stats
async function fetchDashboardStats() {
  try {
    const res = await makeRequest('/dashboard/stats');
    if (res.success && res.data) {
      document.getElementById('stats-total').textContent = res.data.totalCustomers;
      document.getElementById('stats-active').textContent = res.data.activeCustomers;
      document.getElementById('stats-inactive').textContent = res.data.inactiveCustomers;
    }
  } catch (err) {
    console.error("Failed to load dashboard metrics", err);
    document.getElementById('stats-total').textContent = 'N/A';
    document.getElementById('stats-active').textContent = 'N/A';
    document.getElementById('stats-inactive').textContent = 'N/A';
  }
}

// Fetch Customers List
async function fetchCustomers() {
  const tbody = document.getElementById('customers-list-tbody');
  renderSkeletons(tbody);

  // Map filters to query params
  const params = new URLSearchParams();
  params.append('page', filters.page);
  params.append('limit', filters.limit);
  if (filters.search) params.append('search', filters.search);
  if (filters.status !== 'all') params.append('status', filters.status);
  params.append('sort', filters.sort);
  params.append('order', filters.order);

  try {
    const res = await makeRequest(`/customers?${params.toString()}`);
    if (res.success && res.data) {
      customersList = res.data.customers || [];
      pagination = res.data.pagination || { total: 0, page: 1, limit: 10, pages: 1 };
      renderCustomersTable();
      renderPaginationControls();
    }
  } catch (err) {
    showToast(err.message, 'danger');
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:var(--danger); padding:2rem;">Failed to retrieve customer records.</td></tr>`;
  }
}

function renderSkeletons(target) {
  target.innerHTML = '';
  for (let i = 0; i < 5; i++) {
    const tr = document.createElement('tr');
    tr.className = 'skeleton-row';
    tr.innerHTML = `
      <td><div class="skeleton-line" style="width: 120px;"></div></td>
      <td><div class="skeleton-line" style="width: 180px;"></div></td>
      <td><div class="skeleton-line" style="width: 100px;"></div></td>
      <td><div class="skeleton-line" style="width: 140px;"></div></td>
      <td><div class="skeleton-line" style="width: 60px;"></div></td>
      <td><div class="skeleton-line" style="width: 80px;"></div></td>
    `;
    target.appendChild(tr);
  }
}

function renderCustomersTable() {
  const tbody = document.getElementById('customers-list-tbody');
  tbody.innerHTML = '';

  if (customersList.length === 0) {
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding:2rem;">No matching client records found.</td></tr>`;
    return;
  }

  const role = currentUser.role || 'api_viewer';
  const canEdit = (role === 'api_super_admin' || role === 'api_manager' || role === 'administrator');
  const canDelete = (role === 'api_super_admin' || role === 'administrator');

  customersList.forEach(c => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td style="font-weight:600; color:var(--text-primary);">${c.first_name} ${c.last_name}</td>
      <td>${c.email}</td>
      <td>${c.phone}</td>
      <td>${c.city ? `${c.city}, ${c.state || ''}` : 'N/A'}</td>
      <td>
        <span class="badge-status ${c.status.toLowerCase() === 'active' ? 'completed' : 'pending'}">
          ${c.status}
        </span>
      </td>
      <td>
        <div class="table-actions">
          ${canEdit ? `
            <button class="table-btn edit-cust-btn" data-id="${c.id}" title="Edit Profile">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
              </svg>
            </button>
          ` : ''}
          ${canDelete ? `
            <button class="table-btn danger delete-cust-btn" data-id="${c.id}" title="Soft Delete">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2M10 11v6M14 11v6"/>
              </svg>
            </button>
          ` : ''}
          ${!canEdit && !canDelete ? `<span style="font-size:0.8rem; color:var(--text-muted);">Read-only</span>` : ''}
        </div>
      </td>
    `;

    // Wire up events
    if (canEdit) {
      tr.querySelector('.edit-cust-btn').addEventListener('click', () => openCustomerModal(c.id));
    }
    if (canDelete) {
      tr.querySelector('.delete-cust-btn').addEventListener('click', () => handleDeleteCustomer(c.id, `${c.first_name} ${c.last_name}`));
    }

    tbody.appendChild(tr);
  });
}

function renderPaginationControls() {
  const container = document.getElementById('pagination-buttons-container');
  const info = document.getElementById('pagination-info');
  container.innerHTML = '';

  const start = (pagination.page - 1) * pagination.limit + 1;
  const end = Math.min(pagination.page * pagination.limit, pagination.total);

  info.textContent = pagination.total > 0 
    ? `Showing ${start} to ${end} of ${pagination.total} entries` 
    : 'Showing 0 to 0 of 0 entries';

  if (pagination.pages <= 1) return;

  // Previous btn
  const prevBtn = document.createElement('button');
  prevBtn.className = 'pagination-btn';
  prevBtn.disabled = pagination.page === 1;
  prevBtn.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor"
      stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <path d="m15 18-6-6 6-6"/>
    </svg>
  `;
  prevBtn.addEventListener('click', () => {
    filters.page = pagination.page - 1;
    fetchCustomers();
  });
  container.appendChild(prevBtn);

  // Page numbers
  for (let i = 1; i <= pagination.pages; i++) {
    const pageBtn = document.createElement('button');
    pageBtn.className = `pagination-btn ${pagination.page === i ? 'active' : ''}`;
    pageBtn.textContent = i;
    pageBtn.addEventListener('click', () => {
      filters.page = i;
      fetchCustomers();
    });
    container.appendChild(pageBtn);
  }

  // Next btn
  const nextBtn = document.createElement('button');
  nextBtn.className = 'pagination-btn';
  nextBtn.disabled = pagination.page === pagination.pages;
  nextBtn.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor"
      stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <path d="m9 18 6-6-6-6"/>
    </svg>
  `;
  nextBtn.addEventListener('click', () => {
    filters.page = pagination.page + 1;
    fetchCustomers();
  });
  container.appendChild(nextBtn);
}

// Modal CRUD Handlers
function openCustomerModal(id = null) {
  const overlay = document.getElementById('modal-overlay-container');
  const modal = document.getElementById('customer-crud-modal');
  const title = document.getElementById('crud-modal-title');
  const form = document.getElementById('customer-details-form');
  
  form.reset();
  document.getElementById('customer-id-input').value = '';

  overlay.style.display = 'flex';
  overlay.classList.add('active');
  modal.style.display = 'block';

  if (id) {
    title.textContent = 'Edit Royal Customer Details';
    const c = customersList.find(item => item.id === id);
    if (c) {
      document.getElementById('customer-id-input').value = c.id;
      document.getElementById('cust-first-name').value = c.first_name;
      document.getElementById('cust-last-name').value = c.last_name;
      document.getElementById('cust-email').value = c.email;
      document.getElementById('cust-phone').value = c.phone;
      document.getElementById('cust-address').value = c.address || '';
      document.getElementById('cust-city').value = c.city || '';
      document.getElementById('cust-state').value = c.state || '';
      document.getElementById('cust-country').value = c.country || '';
      document.getElementById('cust-zip').value = c.postal_code || '';
      document.getElementById('cust-status').value = c.status || 'ACTIVE';
    }
  } else {
    title.textContent = 'Add New Customer Profile';
  }
}

function closeModals() {
  const overlay = document.getElementById('modal-overlay-container');
  overlay.classList.remove('active');
  overlay.style.display = 'none';
  document.getElementById('customer-crud-modal').style.display = 'none';
  document.getElementById('csv-import-results-modal').style.display = 'none';
}

async function handleSaveCustomer(e) {
  e.preventDefault();
  const id = document.getElementById('customer-id-input').value;
  const payload = {
    first_name: document.getElementById('cust-first-name').value.trim(),
    last_name: document.getElementById('cust-last-name').value.trim(),
    email: document.getElementById('cust-email').value.trim(),
    phone: document.getElementById('cust-phone').value.trim(),
    address: document.getElementById('cust-address').value.trim(),
    city: document.getElementById('cust-city').value.trim(),
    state: document.getElementById('cust-state').value.trim(),
    country: document.getElementById('cust-country').value.trim(),
    postal_code: document.getElementById('cust-zip').value.trim(),
    status: document.getElementById('cust-status').value
  };

  try {
    let res;
    if (id) {
      // Update
      res = await makeRequest(`/customers/${id}`, {
        method: 'PUT',
        body: JSON.stringify(payload)
      });
      showToast('Customer record updated successfully!');
    } else {
      // Create
      res = await makeRequest('/customers', {
        method: 'POST',
        body: JSON.stringify(payload)
      });
      showToast('Customer profile registered successfully!');
    }

    if (res.success) {
      closeModals();
      fetchCustomers();
    }
  } catch (err) {
    showToast(err.message, 'danger');
  }
}

async function handleDeleteCustomer(id, name) {
  if (confirm(`Are you absolutely sure you want to delete customer "${name}"?`)) {
    try {
      const res = await makeRequest(`/customers/${id}`, { method: 'DELETE' });
      if (res.success) {
        showToast(`Customer "${name}" was deleted successfully.`);
        fetchCustomers();
      }
    } catch (err) {
      showToast(err.message, 'danger');
    }
  }
}

// CSV Export
async function handleExportCSV() {
  try {
    const blob = await makeRequest('/customers/export', { method: 'GET' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.style.display = 'none';
    a.href = url;
    a.download = `customers-export-${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    showToast('Customer directory exported successfully.');
  } catch (err) {
    showToast('Failed to export CSV database.', 'danger');
  }
}

// CSV Import
async function handleImportCSV(e) {
  const file = e.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('file', file);

  // Clear file input so same file can be imported again
  e.target.value = '';

  showToast('Uploading and parsing CSV...', 'info');

  try {
    const res = await makeRequest('/customers/import', {
      method: 'POST',
      body: formData
    });

    if (res.success && res.data) {
      // Display Modal results
      document.getElementById('import-success-count').textContent = res.data.imported_count;
      document.getElementById('import-failed-count').textContent = res.data.failed_count;

      const errList = document.getElementById('import-errors-list');
      const errWrapper = document.getElementById('import-errors-wrapper');
      errList.innerHTML = '';

      if (res.data.errors && res.data.errors.length > 0) {
        res.data.errors.forEach(err => {
          const div = document.createElement('div');
          div.className = 'import-error-item';
          div.textContent = err;
          errList.appendChild(div);
        });
        errWrapper.style.display = 'block';
      } else {
        errWrapper.style.display = 'none';
      }

      // Close loading, open results modal
      const overlay = document.getElementById('modal-overlay-container');
      const resultsModal = document.getElementById('csv-import-results-modal');
      
      overlay.style.display = 'flex';
      overlay.classList.add('active');
      resultsModal.style.display = 'block';

      fetchCustomers();
    }
  } catch (err) {
    showToast(err.message, 'danger');
  }
}

// Initialize Application UI Controls
function initEventListeners() {
  // Theme button
  document.getElementById('theme-toggle-btn').addEventListener('click', toggleTheme);

  // Login toggles
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

  // Auth submit
  document.getElementById('panel-login-form').addEventListener('submit', handleLogin);
  document.getElementById('panel-register-form').addEventListener('submit', handleRegister);
  document.getElementById('panel-logout-btn').addEventListener('click', () => {
    handleLogout();
    showToast('Logged out of session.');
  });

  // Sidebar links
  document.querySelectorAll('.admin-nav-link').forEach(link => {
    link.addEventListener('click', () => {
      showPanel(link.dataset.panel);
    });
  });

  // Save Settings Config
  document.getElementById('api-config-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const newUrl = document.getElementById('config-base-url').value.trim();
    const oldUrl = apiBaseUrl;
    apiBaseUrl = newUrl;
    
    showToast('Testing new API Connection...', 'info');
    const stable = await checkApiConnection();
    if (stable) {
      localStorage.setItem('marwari_customer_api_url', newUrl);
      document.getElementById('auth-endpoint-preview').textContent = newUrl;
      showToast('Settings saved. API connection is stable!');
    } else {
      apiBaseUrl = oldUrl;
      document.getElementById('config-base-url').value = oldUrl;
      showToast('Failed to connect to the new API path. Reverting.', 'danger');
    }
  });

  // Search input with debounce
  let searchTimeout = null;
  document.getElementById('customer-search-input').addEventListener('input', (e) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      filters.search = e.target.value.trim();
      filters.page = 1;
      fetchCustomers();
    }, 400);
  });

  // Filter Status Buttons
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      filters.status = btn.dataset.status;
      filters.page = 1;
      fetchCustomers();
    });
  });

  // Page limit
  document.getElementById('page-limit-select').addEventListener('change', (e) => {
    filters.limit = parseInt(e.target.value);
    filters.page = 1;
    fetchCustomers();
  });

  // Sorting columns
  const nameHeader = document.getElementById('th-name');
  const emailHeader = document.getElementById('th-email');

  nameHeader.addEventListener('click', () => {
    toggleSort('first_name', nameHeader, emailHeader);
  });

  emailHeader.addEventListener('click', () => {
    toggleSort('email', emailHeader, nameHeader);
  });

  function toggleSort(field, clickedHeader, otherHeader) {
    if (filters.sort === field) {
      filters.order = filters.order === 'asc' ? 'desc' : 'asc';
    } else {
      filters.sort = field;
      filters.order = 'asc';
    }

    clickedHeader.querySelector('span').textContent = filters.order === 'asc' ? '▲' : '▼';
    otherHeader.querySelector('span').textContent = '↕';

    filters.page = 1;
    fetchCustomers();
  }

  // Modals closing
  document.getElementById('close-crud-modal-btn').addEventListener('click', closeModals);
  document.getElementById('close-import-modal-btn').addEventListener('click', closeModals);
  document.getElementById('import-ok-btn').addEventListener('click', closeModals);
  
  // Close on backdrop overlay click
  window.addEventListener('click', (e) => {
    const overlay = document.getElementById('modal-overlay-container');
    if (e.target === overlay) {
      closeModals();
    }
  });

  // Add Customer modal trigger
  document.getElementById('add-customer-trigger-btn').addEventListener('click', () => openCustomerModal());

  // Customer form details save
  document.getElementById('customer-details-form').addEventListener('submit', handleSaveCustomer);

  // Bulk Import and Export
  document.getElementById('export-csv-btn').addEventListener('click', handleExportCSV);
  
  const fileInput = document.getElementById('csv-file-input');
  document.getElementById('import-csv-trigger-btn').addEventListener('click', () => fileInput.click());
  fileInput.addEventListener('change', handleImportCSV);
}

// App Initialization
async function initApp() {
  initTheme();
  initEventListeners();
  
  document.getElementById('auth-endpoint-preview').textContent = apiBaseUrl;
  document.getElementById('config-base-url').value = apiBaseUrl;

  const stable = await checkApiConnection();

  if (token) {
    try {
      showToast('Authenticating active session...', 'info');
      const res = await makeRequest('/auth/me');
      if (res.success && res.data) {
        currentUser = res.data;
        localStorage.setItem('marwari_customer_api_user', JSON.stringify(currentUser));
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
