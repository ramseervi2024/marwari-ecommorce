<?php
if (!defined('ABSPATH')) exit;
$site_url = get_site_url();
$api_base = $site_url . '/wp-json/pharmacy/v1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pharmacy ERP</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
:root {
  --bg-body:#f1f5f9; --bg-card:#ffffff; --bg-sidebar:#ffffff; --bg-hover:#f8fafc;
  --text-main:#0f172a; --text-muted:#64748b; --border:#e2e8f0;
  --primary:#0ea5e9; --primary-hover:#0284c7; --primary-light:#e0f2fe;
  --success:#10b981; --success-light:#d1fae5;
  --danger:#ef4444; --danger-light:#fee2e2;
  --warning:#f59e0b; --warning-light:#fef3c7;
  --shadow-sm:0 1px 2px 0 rgba(0,0,0,0.05);
  --shadow-md:0 4px 6px -1px rgba(0,0,0,0.1),0 2px 4px -1px rgba(0,0,0,0.06);
  --radius:12px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:var(--bg-body);color:var(--text-main);height:100vh;overflow:hidden;}
::-webkit-scrollbar{width:6px;height:6px}
::-webkit-scrollbar-track{background:var(--bg-body)}
::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:3px}
::-webkit-scrollbar-thumb:hover{background:#94a3b8}

/* Layout */
#app{display:flex;height:100%}
.sidebar{width:260px;background:var(--bg-sidebar);border-right:1px solid var(--border);display:flex;flex-direction:column;z-index:100;}
.main{flex:1;display:flex;flex-direction:column;overflow:hidden;}
.topbar{height:64px;background:var(--bg-card);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 24px;justify-content:space-between;box-shadow:var(--shadow-sm);}
.content{flex:1;padding:24px;overflow-y:auto;}

/* Sidebar */
.brand{height:64px;display:flex;align-items:center;padding:0 20px;border-bottom:1px solid var(--border);gap:12px;}
.brand-icon{width:36px;height:36px;background:var(--primary-light);color:var(--primary);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;}
.brand-text{font-weight:800;font-size:1.1rem;color:var(--text-main);}
.nav-menu{flex:1;padding:16px 12px;overflow-y:auto;}
.nav-item{display:flex;align-items:center;gap:12px;padding:10px 14px;color:var(--text-muted);font-weight:600;font-size:0.9rem;border-radius:8px;cursor:pointer;margin-bottom:4px;transition:0.2s;}
.nav-item:hover{background:var(--bg-hover);color:var(--primary);}
.nav-item.active{background:var(--primary-light);color:var(--primary);}
.user-card{padding:16px;border-top:1px solid var(--border);display:flex;align-items:center;gap:12px;background:var(--bg-sidebar);}
.user-avatar{width:36px;height:36px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;}

/* Cards */
.card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow-sm);margin-bottom:20px;}
.card-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;}
.card-title{font-size:1.1rem;font-weight:700;color:var(--text-main);}

/* Grid & KPI */
.grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:24px;}
.kpi-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:16px;}
.kpi-icon{width:50px;height:50px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;}
.kpi-val{font-size:1.5rem;font-weight:800;line-height:1;margin-bottom:4px;}
.kpi-label{font-size:0.85rem;color:var(--text-muted);font-weight:500;}

/* Buttons */
.btn{padding:8px 16px;border-radius:8px;font-family:inherit;font-weight:600;font-size:0.85rem;cursor:pointer;border:1px solid transparent;transition:all 0.2s;display:inline-flex;align-items:center;gap:6px;}
.btn-primary{background:var(--primary);color:#fff;}
.btn-primary:hover{background:var(--primary-hover);box-shadow:0 4px 12px rgba(14,165,233,0.3);}
.btn-secondary{background:#fff;border-color:var(--border);color:var(--text-main);}
.btn-secondary:hover{background:var(--bg-hover);}
.btn-danger{background:var(--danger-light);color:var(--danger);}
.btn-danger:hover{background:var(--danger);}
.btn-sm{padding:6px 12px;font-size:0.8rem;}

/* Table */
.table-wrap{overflow-x:auto;}
table{width:100%;border-collapse:collapse;font-size:0.85rem;}
th{text-align:left;padding:12px 16px;color:var(--text-muted);font-weight:600;border-bottom:2px solid var(--border);background:var(--bg-hover);white-space:nowrap;}
td{padding:12px 16px;border-bottom:1px solid var(--border);vertical-align:middle;}
tr:hover td{background:var(--bg-hover);}

/* Status Badges */
.badge{padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;display:inline-block;}
.badge-success{background:var(--success-light);color:#065f46;}
.badge-danger{background:var(--danger-light);color:#991b1b;}
.badge-warning{background:var(--warning-light);color:#92400e;}
.badge-primary{background:var(--primary-light);color:#075985;}

/* Forms */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.form-group{display:flex;flex-direction:column;gap:6px;}
.form-group.full{grid-column:1/-1;}
label{font-size:0.85rem;font-weight:600;color:var(--text-main);}
input,select,textarea{padding:10px 14px;border:1px solid var(--border);border-radius:8px;font-family:inherit;font-size:0.9rem;outline:none;transition:border-color 0.2s;}
input:focus,select:focus,textarea:focus{border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light);}

/* Modals */
.modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,0.5);display:flex;align-items:center;justify-content:center;z-index:1000;opacity:0;pointer-events:none;transition:0.2s;}
.modal-overlay.show{opacity:1;pointer-events:all;}
.modal{background:var(--bg-card);border-radius:var(--radius);width:100%;max-width:600px;max-height:90vh;overflow-y:auto;box-shadow:var(--shadow-md);transform:translateY(20px);transition:0.2s;}
.modal-overlay.show .modal{transform:translateY(0);}
.modal-header{padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;}
.modal-title{font-weight:700;font-size:1.1rem;}
.modal-close{background:none;border:none;font-size:1.5rem;color:var(--text-muted);cursor:pointer;}
.modal-body{padding:24px;}
.modal-footer{padding:20px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:12px;}

/* Login */
.login-page{position:fixed;inset:0;z-index:9999;background:var(--bg-body);display:flex;align-items:center;justify-content:center;}
.login-card{background:var(--bg-card);padding:40px;border-radius:24px;box-shadow:var(--shadow-md);width:100%;max-width:400px;}
.login-logo{text-align:center;margin-bottom:30px;}
.login-logo-icon{width:64px;height:64px;background:var(--primary-light);color:var(--primary);font-size:2rem;display:flex;align-items:center;justify-content:center;border-radius:16px;margin:0 auto 16px;}

.alert{padding:12px 16px;border-radius:8px;font-size:0.85rem;margin-bottom:16px;}
.alert-error{background:var(--danger-light);color:#991b1b;border:1px solid #fca5a5;}

/* dynamic lists */
.item-row{display:grid;grid-template-columns:3fr 1fr 1fr 1fr 1fr auto;gap:10px;align-items:end;margin-bottom:10px;padding:10px;border:1px solid var(--border);border-radius:8px;background:var(--bg-hover);}
</style>
</head>
<body>

<!-- Login -->
<div id="login-page" class="login-page" style="display:none">
  <div class="login-card">
    <div class="login-logo">
      <div class="login-logo-icon">💊</div>
      <h2 style="color:var(--text-main);font-weight:800;">Pharmacy ERP</h2>
      <p style="color:var(--text-muted);font-size:0.85rem;">Management System</p>
    </div>
    <div id="login-alert"></div>
    <div style="display:flex;flex-direction:column;gap:16px;">
      <div class="form-group">
        <label>Username</label>
        <input type="text" id="l-user" placeholder="pharmadmin">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" id="l-pass" placeholder="••••••">
      </div>
      <button class="btn btn-primary" style="justify-content:center;padding:12px;font-size:1rem;" onclick="doLogin()">Sign In</button>
    </div>
    <div style="margin-top:20px;padding:12px;background:var(--bg-hover);border-radius:8px;font-size:0.8rem;">
      <strong>Demo:</strong><br>
      Admin: pharmadmin / 123456<br>
      Staff: pharmastaff / 123456
    </div>
  </div>
</div>

<!-- App -->
<div id="app" style="display:none">
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-icon">💊</div>
      <div class="brand-text">Pharmacy ERP</div>
    </div>
    <nav class="nav-menu">
      <div class="nav-item active" data-sec="overview" onclick="nav('overview')">🏠 Overview</div>
      <div class="nav-item" data-sec="dashboard" onclick="nav('dashboard')">📊 Dashboard</div>
      <div class="nav-item" data-sec="billing" onclick="nav('billing')">🧾 Billing (POS)</div>
      <div class="nav-item" data-sec="medicines" onclick="nav('medicines')">💊 Medicines</div>
      <div class="nav-item" data-sec="batches" onclick="nav('batches')">📦 Stock & Batches</div>
      <div class="nav-item" data-sec="purchases" onclick="nav('purchases')">🛒 Purchases</div>
      <div class="nav-item" data-sec="suppliers" onclick="nav('suppliers')">🏢 Suppliers</div>
      <div class="nav-item" data-sec="reports" onclick="nav('reports')">📈 Reports</div>
      <div class="nav-item" data-sec="settings" onclick="nav('settings')">⚙️ Settings</div>
      <div class="nav-item" onclick="window.open('<?php echo esc_js($site_url); ?>/pharmacy-erp-docs','_blank')">📘 API Docs</div>
    </nav>
    <div class="user-card">
      <div class="user-avatar" id="u-ava">A</div>
      <div>
        <div style="font-weight:700;font-size:0.9rem;" id="u-name">Admin</div>
        <div style="color:var(--text-muted);font-size:0.75rem;" id="u-role">Role</div>
      </div>
    </div>
  </aside>
  <main class="main">
    <header class="topbar">
      <h2 id="top-title" style="font-size:1.2rem;font-weight:700;">Dashboard</h2>
      <div><button class="btn btn-secondary btn-sm" onclick="doLogout()">Logout</button></div>
    </header>
    <div class="content" id="content"></div>
  </main>
</div>

<!-- Modal -->
<div class="modal-overlay" id="modal" onclick="if(event.target===this)closeModal()">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="m-title">Modal</div>
      <button class="modal-close" onclick="closeModal()">×</button>
    </div>
    <div class="modal-body" id="m-body"></div>
  </div>
</div>

<script>
const API = '<?php echo esc_js($api_base); ?>';
let token = localStorage.getItem('ph_token') || '';
let currentUser = null;
let state = {
  medicines:[], suppliers:[], batches:[],
  billItems:[], purchaseItems:[]
};

async function api(method, path, body=null) {
  const opts = { method, headers:{'Content-Type':'application/json','Authorization':'Bearer '+token} };
  if(body) opts.body = JSON.stringify(body);
  try {
    const r = await fetch(API+path, opts);
    return r.json();
  } catch(e) { return {success:false, message:e.message}; }
}

async function doLogin() {
  const u=document.getElementById('l-user').value, p=document.getElementById('l-pass').value;
  const res = await api('POST','/auth/login',{username:u,password:p});
  if(res.success) {
    token = res.data.token; localStorage.setItem('ph_token',token);
    currentUser = res.data.user;
    initApp();
  } else {
    document.getElementById('login-alert').innerHTML = `<div class="alert alert-error">${res.message}</div>`;
  }
}
async function doLogout() {
  await api('POST','/auth/logout');
  token=''; localStorage.removeItem('ph_token');
  document.getElementById('app').style.display='none';
  document.getElementById('login-page').style.display='flex';
}
async function checkAuth() {
  if(!token) return document.getElementById('login-page').style.display='flex';
  const res = await api('GET','/auth/me');
  if(res.success) { currentUser = res.data; initApp(); }
  else { document.getElementById('login-page').style.display='flex'; }
}
function initApp() {
  document.getElementById('login-page').style.display='none';
  document.getElementById('app').style.display='flex';
  document.getElementById('u-ava').textContent = currentUser.name.charAt(0).toUpperCase();
  document.getElementById('u-name').textContent = currentUser.name;
  document.getElementById('u-role').textContent = currentUser.role.replace('pharmacy_','');
  nav('overview');
  loadGlobals();
}

async function loadGlobals() {
  const [m, s] = await Promise.all([
    api('GET','/medicines?limit=1000'),
    api('GET','/suppliers?limit=1000')
  ]);
  if(m.success) state.medicines = m.data.data;
  if(s.success) state.suppliers = s.data.data;
}

function nav(sec) {
  document.querySelectorAll('.nav-item').forEach(el=>el.classList.remove('active'));
  document.querySelector(`[data-sec="${sec}"]`).classList.add('active');
  document.getElementById('top-title').textContent = sec.charAt(0).toUpperCase() + sec.slice(1);
  document.getElementById('content').innerHTML = 'Loading...';
  if(sections[sec]) sections[sec]();
}

function openModal(title, html) {
  document.getElementById('m-title').textContent=title;
  document.getElementById('m-body').innerHTML=html;
  document.getElementById('modal').classList.add('show');
}
function closeModal() { document.getElementById('modal').classList.remove('show'); }

const sections = {};

/* OVERVIEW */
sections.overview = async () => {
  const res = await api('GET','/dashboard/stats');
  if(!res.success) return document.getElementById('content').innerHTML = 'Error loading stats';
  const {summary, expiry_alerts, low_stock, recent_bills} = res.data;
  
  document.getElementById('content').innerHTML = `
    <div class="grid-4">
      <div class="kpi-card"><div class="kpi-icon" style="background:var(--primary-light);color:var(--primary)">💰</div><div><div class="kpi-val">₹${summary.revenue_today}</div><div class="kpi-label">Today's Revenue</div></div></div>
      <div class="kpi-card"><div class="kpi-icon" style="background:var(--success-light);color:var(--success)">🧾</div><div><div class="kpi-val">${summary.bills_today}</div><div class="kpi-label">Bills Today</div></div></div>
      <div class="kpi-card"><div class="kpi-icon" style="background:var(--danger-light);color:var(--danger)">⚠️</div><div><div class="kpi-val">${summary.expiry_alerts_count}</div><div class="kpi-label">Expiry Alerts</div></div></div>
      <div class="kpi-card"><div class="kpi-icon" style="background:var(--warning-light);color:var(--warning)">📉</div><div><div class="kpi-val">${summary.low_stock_count}</div><div class="kpi-label">Low Stock Items</div></div></div>
    </div>
    
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
      <div class="card">
        <div class="card-header"><div class="card-title">Expiring Soon (30 Days)</div></div>
        <div class="table-wrap"><table>
          <thead><tr><th>Medicine</th><th>Batch</th><th>Expiry</th><th>Qty</th></tr></thead>
          <tbody>${expiry_alerts.map(a=>`<tr><td>${a.medicine_name}</td><td>${a.batch_number}</td><td><span class="badge badge-danger">${a.expiry_date}</span></td><td>${a.available_qty}</td></tr>`).join('')}</tbody>
        </table></div>
      </div>
      <div class="card">
        <div class="card-header"><div class="card-title">Low Stock Alerts</div></div>
        <div class="table-wrap"><table>
          <thead><tr><th>Medicine</th><th>Current Stock</th><th>Reorder Level</th></tr></thead>
          <tbody>${low_stock.map(l=>`<tr><td>${l.name}</td><td><span class="badge badge-warning">${l.current_stock}</span></td><td>${l.reorder_level}</td></tr>`).join('')}</tbody>
        </table></div>
      </div>
    </div>
  `;
};

/* DASHBOARD ANALYTICS */
sections.dashboard = async () => {
  const res = await api('GET','/dashboard/stats');
  if(!res.success) return document.getElementById('content').innerHTML = 'Error loading stats';
  const {summary, revenue_chart, recent_bills} = res.data;
  
  document.getElementById('content').innerHTML = `
    <div class="grid-4">
      <div class="kpi-card"><div class="kpi-icon" style="background:var(--primary-light);color:var(--primary)">💊</div><div><div class="kpi-val">${summary.total_medicines}</div><div class="kpi-label">Total Medicines</div></div></div>
      <div class="kpi-card"><div class="kpi-icon" style="background:var(--success-light);color:var(--success)">🏢</div><div><div class="kpi-val">${summary.total_suppliers}</div><div class="kpi-label">Total Suppliers</div></div></div>
      <div class="kpi-card"><div class="kpi-icon" style="background:var(--warning-light);color:var(--warning)">💰</div><div><div class="kpi-val">₹${summary.revenue_today}</div><div class="kpi-label">Today's Revenue</div></div></div>
      <div class="kpi-card"><div class="kpi-icon" style="background:var(--danger-light);color:var(--danger)">🧾</div><div><div class="kpi-val">${summary.bills_today}</div><div class="kpi-label">Bills Today</div></div></div>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">
      <div class="card">
        <div class="card-header"><div class="card-title">Recent Bills</div></div>
        <div class="table-wrap"><table>
          <thead><tr><th>Bill #</th><th>Date</th><th>Customer</th><th>Total</th></tr></thead>
          <tbody>${recent_bills.map(b=>`<tr><td>${b.bill_number}</td><td>${b.bill_date}</td><td>${b.customer_name}</td><td>₹${b.grand_total}</td></tr>`).join('')}</tbody>
        </table></div>
      </div>
      <div class="card">
        <div class="card-header"><div class="card-title">30-Day Revenue Summary</div></div>
        <div class="table-wrap"><table>
          <thead><tr><th>Date</th><th>Bills</th><th>Revenue</th></tr></thead>
          <tbody>${revenue_chart.slice(-5).map(r=>`<tr><td>${r.date}</td><td>${r.bills}</td><td>₹${r.revenue}</td></tr>`).join('')}</tbody>
        </table></div>
      </div>
    </div>
  `;
};

/* MEDICINES */
sections.medicines = async () => {
  const res = await api('GET','/medicines?limit=100');
  if(!res.success) return;
  document.getElementById('content').innerHTML = `
    <div class="card">
      <div class="card-header">
        <div class="card-title">Medicines Master</div>
        <button class="btn btn-primary" onclick="addMedicine()">+ Add Medicine</button>
      </div>
      <div class="table-wrap"><table>
        <thead><tr><th>Code</th><th>Name</th><th>Generic</th><th>Unit</th><th>MRP</th><th>Stock</th><th>Actions</th></tr></thead>
        <tbody>${res.data.data.map(m=>`<tr>
          <td>${m.medicine_code}</td><td><strong>${m.name}</strong></td><td>${m.generic_name}</td>
          <td>${m.unit}</td><td>₹${m.mrp}</td><td><span class="badge ${m.current_stock<=m.reorder_level?'badge-warning':'badge-success'}">${m.current_stock}</span></td>
          <td><button class="btn btn-sm btn-danger" onclick="delMed(${m.id})">Del</button></td>
        </tr>`).join('')}</tbody>
      </table></div>
    </div>
  `;
};
window.addMedicine = () => {
  openModal('Add Medicine', `
    <div class="form-grid">
      <div class="form-group"><label>Code</label><input type="text" id="m-code"></div>
      <div class="form-group"><label>Name</label><input type="text" id="m-name"></div>
      <div class="form-group"><label>Generic Name</label><input type="text" id="m-gen"></div>
      <div class="form-group"><label>Unit</label><select id="m-unit"><option>Strip</option><option>Bottle</option><option>Injection</option></select></div>
      <div class="form-group"><label>MRP</label><input type="number" id="m-mrp" value="0"></div>
      <div class="form-group"><label>GST Rate %</label><input type="number" id="m-gst" value="12"></div>
      <div class="form-group full"><button class="btn btn-primary" onclick="saveMed()">Save Medicine</button></div>
    </div>
  `);
};
window.saveMed = async () => {
  const d = { medicine_code:document.getElementById('m-code').value, name:document.getElementById('m-name').value, generic_name:document.getElementById('m-gen').value, unit:document.getElementById('m-unit').value, mrp:document.getElementById('m-mrp').value, gst_rate:document.getElementById('m-gst').value };
  await api('POST','/medicines', d); closeModal(); loadGlobals(); nav('medicines');
};
window.delMed = async (id) => { if(confirm('Delete?')){ await api('DELETE','/medicines/'+id); nav('medicines'); } };

/* BATCHES */
sections.batches = async () => {
  const res = await api('GET','/batches?limit=100');
  if(!res.success) return;
  document.getElementById('content').innerHTML = `
    <div class="card">
      <div class="card-header"><div class="card-title">Stock Batches</div></div>
      <div class="table-wrap"><table>
        <thead><tr><th>Medicine</th><th>Batch #</th><th>Expiry</th><th>MRP</th><th>Available Qty</th></tr></thead>
        <tbody>${res.data.data.map(b=>`<tr>
          <td>${b.medicine_name}</td><td>${b.batch_number}</td>
          <td><span class="badge ${new Date(b.expiry_date)<new Date()?'badge-danger':'badge-primary'}">${b.expiry_date}</span></td>
          <td>₹${b.mrp}</td><td><strong>${b.available_qty}</strong></td>
        </tr>`).join('')}</tbody>
      </table></div>
    </div>
  `;
};

/* SUPPLIERS */
sections.suppliers = async () => {
  const res = await api('GET','/suppliers');
  if(!res.success) return;
  document.getElementById('content').innerHTML = `
    <div class="card">
      <div class="card-header">
        <div class="card-title">Suppliers</div>
        <button class="btn btn-primary" onclick="addSupplier()">+ Add Supplier</button>
      </div>
      <div class="table-wrap"><table>
        <thead><tr><th>Code</th><th>Name</th><th>Contact</th><th>Mobile</th><th>GSTIN</th></tr></thead>
        <tbody>${res.data.data.map(s=>`<tr><td>${s.supplier_code}</td><td><strong>${s.name}</strong></td><td>${s.contact_person}</td><td>${s.mobile}</td><td>${s.gstin}</td></tr>`).join('')}</tbody>
      </table></div>
    </div>
  `;
};
window.addSupplier = () => {
  openModal('Add Supplier', `
    <div class="form-grid">
      <div class="form-group"><label>Code</label><input type="text" id="s-code"></div>
      <div class="form-group"><label>Name</label><input type="text" id="s-name"></div>
      <div class="form-group"><label>Contact Person</label><input type="text" id="s-contact"></div>
      <div class="form-group"><label>Mobile</label><input type="text" id="s-mob"></div>
      <div class="form-group"><label>GSTIN</label><input type="text" id="s-gst"></div>
      <div class="form-group full"><button class="btn btn-primary" onclick="saveSup()">Save Supplier</button></div>
    </div>
  `);
};
window.saveSup = async () => {
  const d = { supplier_code:document.getElementById('s-code').value, name:document.getElementById('s-name').value, contact_person:document.getElementById('s-contact').value, mobile:document.getElementById('s-mob').value, gstin:document.getElementById('s-gst').value };
  await api('POST','/suppliers', d); closeModal(); loadGlobals(); nav('suppliers');
};

/* PURCHASES */
sections.purchases = async () => {
  const res = await api('GET','/purchases');
  if(!res.success) return;
  document.getElementById('content').innerHTML = `
    <div class="card">
      <div class="card-header"><div class="card-title">Purchase Orders</div><button class="btn btn-primary" onclick="addPurchase()">+ New Purchase</button></div>
      <div class="table-wrap"><table>
        <thead><tr><th>PO Number</th><th>Date</th><th>Supplier</th><th>Total</th><th>Status</th></tr></thead>
        <tbody>${res.data.data.map(p=>`<tr><td>${p.purchase_number}</td><td>${p.purchase_date}</td><td>${p.supplier_name}</td><td>₹${p.grand_total}</td><td><span class="badge badge-success">${p.status}</span></td></tr>`).join('')}</tbody>
      </table></div>
    </div>
  `;
};
window.addPurchase = () => {
  state.purchaseItems = [];
  openModal('New Purchase', `
    <div class="form-grid">
      <div class="form-group"><label>Supplier</label><select id="p-sup">${state.suppliers.map(s=>`<option value="${s.id}">${s.name}</option>`).join('')}</select></div>
      <div class="form-group"><label>Invoice No</label><input type="text" id="p-inv"></div>
    </div>
    <div style="margin-top:20px;">
      <h4>Items</h4>
      <div id="p-items-list"></div>
      <button class="btn btn-secondary btn-sm" style="margin-top:10px" onclick="addPurItem()">+ Add Item</button>
    </div>
    <div class="form-group" style="margin-top:20px"><label>Total</label><input type="number" id="p-total" value="0"></div>
    <div style="margin-top:20px;"><button class="btn btn-primary" onclick="savePurchase()">Complete Purchase & Add Stock</button></div>
  `);
};
window.addPurItem = () => {
  state.purchaseItems.push({med_id:'', batch:'', exp:'', qty:1, price:0});
  renderPurItems();
};
window.renderPurItems = () => {
  document.getElementById('p-items-list').innerHTML = state.purchaseItems.map((it,i)=>`
    <div class="item-row">
      <select onchange="state.purchaseItems[${i}].med_id=this.value"><option value="">Select Med</option>${state.medicines.map(m=>`<option value="${m.id}" ${m.id==it.med_id?'selected':''}>${m.name}</option>`).join('')}</select>
      <input type="text" placeholder="Batch" value="${it.batch}" onchange="state.purchaseItems[${i}].batch=this.value">
      <input type="date" value="${it.exp}" onchange="state.purchaseItems[${i}].exp=this.value">
      <input type="number" placeholder="Qty" value="${it.qty}" onchange="state.purchaseItems[${i}].qty=this.value">
      <input type="number" placeholder="Price" value="${it.price}" onchange="state.purchaseItems[${i}].price=this.value">
    </div>
  `).join('');
};
window.savePurchase = async () => {
  const items = state.purchaseItems.map(it=>({medicine_id:it.med_id, batch_number:it.batch, expiry_date:it.exp, quantity:it.qty, purchase_price:it.price, total:it.qty*it.price}));
  const data = { supplier_id: document.getElementById('p-sup').value, invoice_number: document.getElementById('p-inv').value, grand_total: document.getElementById('p-total').value, status: 'Received', items: items };
  await api('POST','/purchases', data); closeModal(); nav('purchases');
};

/* BILLING */
sections.billing = async () => {
  const res = await api('GET','/bills');
  if(!res.success) return;
  document.getElementById('content').innerHTML = `
    <div class="card">
      <div class="card-header"><div class="card-title">Billing (POS)</div><button class="btn btn-primary" onclick="addBill()">+ New Bill</button></div>
      <div class="table-wrap"><table>
        <thead><tr><th>Bill #</th><th>Date</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
        <tbody>${res.data.data.map(b=>`<tr><td>${b.bill_number}</td><td>${b.bill_date}</td><td>${b.customer_name}<br><small>${b.customer_mobile}</small></td><td>₹${b.grand_total}</td><td><span class="badge badge-success">${b.status}</span></td></tr>`).join('')}</tbody>
      </table></div>
    </div>
  `;
};
window.addBill = () => {
  state.billItems = [];
  openModal('New Bill (POS)', `
    <div class="form-grid">
      <div class="form-group"><label>Customer Name</label><input type="text" id="b-cname" value="Walk-in Customer"></div>
      <div class="form-group"><label>Mobile</label><input type="text" id="b-cmob"></div>
      <div class="form-group"><label>Doctor Name</label><input type="text" id="b-doc"></div>
    </div>
    <div style="margin-top:20px;">
      <h4>Medicines</h4>
      <div id="b-items-list"></div>
      <button class="btn btn-secondary btn-sm" style="margin-top:10px" onclick="addBillItem()">+ Add Medicine</button>
    </div>
    <div class="form-grid" style="margin-top:20px">
      <div class="form-group"><label>Grand Total</label><input type="number" id="b-total" value="0"></div>
      <div class="form-group" style="align-items:flex-start;justify-content:flex-end;"><button class="btn btn-primary" onclick="saveBill()">Generate Bill</button></div>
    </div>
  `);
};
window.addBillItem = () => { state.billItems.push({med_id:'', qty:1, price:0}); renderBillItems(); };
window.renderBillItems = () => {
  document.getElementById('b-items-list').innerHTML = state.billItems.map((it,i)=>`
    <div class="item-row" style="grid-template-columns:3fr 1fr 1fr">
      <select onchange="state.billItems[${i}].med_id=this.value"><option value="">Select Med</option>${state.medicines.map(m=>`<option value="${m.id}" ${m.id==it.med_id?'selected':''}>${m.name} (₹${m.mrp})</option>`).join('')}</select>
      <input type="number" placeholder="Qty" value="${it.qty}" onchange="state.billItems[${i}].qty=this.value; calcBill()">
      <input type="number" placeholder="Unit Price" value="${it.price}" onchange="state.billItems[${i}].price=this.value; calcBill()">
    </div>
  `).join('');
};
window.calcBill = () => {
  let tot = 0; state.billItems.forEach(it=>{ tot += (it.qty*it.price); });
  document.getElementById('b-total').value = tot;
};
window.saveBill = async () => {
  const items = state.billItems.map(it=>({medicine_id:it.med_id, quantity:it.qty, unit_price:it.price, total:it.qty*it.price}));
  const data = { customer_name: document.getElementById('b-cname').value, customer_mobile: document.getElementById('b-cmob').value, doctor_name: document.getElementById('b-doc').value, grand_total: document.getElementById('b-total').value, status: 'Paid', items: items };
  await api('POST','/bills', data); closeModal(); nav('billing');
};

/* REPORTS */
sections.reports = async () => {
  const res = await api('GET','/dashboard/stats');
  if(!res.success) return;
  const {revenue_chart} = res.data;
  
  document.getElementById('content').innerHTML = `
    <div class="card">
      <div class="card-header"><div class="card-title">Daily Revenue Report (Last 30 Days)</div></div>
      <div class="table-wrap"><table>
        <thead><tr><th>Date</th><th>Total Bills</th><th>Total Revenue</th></tr></thead>
        <tbody>${revenue_chart.map(r=>`<tr><td>${r.date}</td><td>${r.bills}</td><td><strong style="color:var(--success)">₹${r.revenue}</strong></td></tr>`).join('')}</tbody>
      </table></div>
    </div>
  `;
};

/* SETTINGS */
sections.settings = async () => {
  const res = await api('GET','/settings/smtp');
  if(!res.success) return;
  const s = res.data;
  
  document.getElementById('content').innerHTML = `
    <div class="card" style="max-width:600px">
      <div class="card-header"><div class="card-title">SMTP Settings</div></div>
      <div class="form-grid">
        <div class="form-group"><label>Enable SMTP</label><select id="st-en"><option value="yes" ${s.smtp_enabled==='yes'?'selected':''}>Yes</option><option value="no" ${s.smtp_enabled==='no'?'selected':''}>No</option></select></div>
        <div class="form-group"><label>SMTP Host</label><input type="text" id="st-host" value="${s.smtp_host}"></div>
        <div class="form-group"><label>SMTP Port</label><input type="text" id="st-port" value="${s.smtp_port}"></div>
        <div class="form-group"><label>Encryption</label><select id="st-enc"><option value="tls" ${s.smtp_encryption==='tls'?'selected':''}>TLS</option><option value="ssl" ${s.smtp_encryption==='ssl'?'selected':''}>SSL</option><option value="none" ${s.smtp_encryption==='none'?'selected':''}>None</option></select></div>
        <div class="form-group"><label>SMTP Username</label><input type="text" id="st-user" value="${s.smtp_username}"></div>
        <div class="form-group"><label>SMTP Password</label><input type="password" id="st-pass" value="******"></div>
        <div class="form-group"><label>From Email</label><input type="text" id="st-frome" value="${s.smtp_from_email}"></div>
        <div class="form-group"><label>From Name</label><input type="text" id="st-fromn" value="${s.smtp_from_name}"></div>
        <div class="form-group full" style="margin-top:10px;"><button class="btn btn-primary" onclick="saveSettings()">Save Settings</button></div>
      </div>
    </div>
  `;
};
window.saveSettings = async () => {
  const d = {
    smtp_enabled:document.getElementById('st-en').value,
    smtp_host:document.getElementById('st-host').value,
    smtp_port:document.getElementById('st-port').value,
    smtp_encryption:document.getElementById('st-enc').value,
    smtp_username:document.getElementById('st-user').value,
    smtp_password:document.getElementById('st-pass').value,
    smtp_from_email:document.getElementById('st-frome').value,
    smtp_from_name:document.getElementById('st-fromn').value
  };
  const r = await api('PUT','/settings/smtp', d);
  if(r.success) alert('Settings saved successfully!');
};

checkAuth();
</script>
</body>
</html>
