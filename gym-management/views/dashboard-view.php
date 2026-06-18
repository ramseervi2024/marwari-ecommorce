<?php
if (!defined('ABSPATH')) exit;
$site_url = get_site_url();
$api_base = $site_url . '/wp-json/gym/v1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gym ERP</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
  --bg-body:#f1f5f9; --bg-card:#ffffff; --bg-sidebar:#ffffff; --bg-hover:#f8fafc;
  --text-main:#0f172a; --text-muted:#64748b; --border:#e2e8f0;
  --primary:#4f46e5; --primary-hover:#4338ca; --primary-light:#e0e7ff;
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
.btn-primary:hover{background:var(--primary-hover);box-shadow:0 4px 12px rgba(79,70,229,0.3);}
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
.badge-primary{background:var(--primary-light);color:#3730a3;}

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
</style>
</head>
<body>

<!-- Login -->
<div id="login-page" class="login-page" style="display:none">
  <div class="login-card">
    <div class="login-logo">
      <div class="login-logo-icon">🏋️</div>
      <h2 style="color:var(--text-main);font-weight:800;">Gym ERP</h2>
      <p style="color:var(--text-muted);font-size:0.85rem;">Fitness Management</p>
    </div>
    <div id="login-alert"></div>
    <div style="display:flex;flex-direction:column;gap:16px;">
      <div class="form-group"><label>Username</label><input type="text" id="l-user" placeholder="gymadmin"></div>
      <div class="form-group"><label>Password</label><input type="password" id="l-pass" placeholder="••••••"></div>
      <button class="btn btn-primary" style="justify-content:center;padding:12px;font-size:1rem;" onclick="doLogin()">Sign In</button>
    </div>
    <div style="margin-top:20px;padding:12px;background:var(--bg-hover);border-radius:8px;font-size:0.8rem;">
      <strong>Demo:</strong><br>Admin: gymadmin / 123456<br>Staff: gymstaff / 123456
    </div>
  </div>
</div>

<!-- App -->
<div id="app" style="display:none">
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-icon">🏋️</div>
      <div class="brand-text">Gym ERP</div>
    </div>
    <nav class="nav-menu">
      <div class="nav-item active" data-sec="overview" onclick="nav('overview')">🏠 Overview</div>
      <div class="nav-item" data-sec="members" onclick="nav('members')">👥 Members</div>
      <div class="nav-item" data-sec="memberships" onclick="nav('memberships')">🎟️ Memberships</div>
      <div class="nav-item" data-sec="plans" onclick="nav('plans')">🏷️ Plans</div>
      <div class="nav-item" data-sec="trainers" onclick="nav('trainers')">💪 Trainers</div>
      <div class="nav-item" data-sec="diet_plans" onclick="nav('diet_plans')">🥗 Diet Plans</div>
      <div class="nav-item" data-sec="attendance" onclick="nav('attendance')">📅 Attendance</div>
      <div class="nav-item" data-sec="payments" onclick="nav('payments')">💰 Payments</div>
      <div class="nav-item" onclick="window.open('<?php echo esc_js($site_url); ?>/gym-management-docs','_blank')">📘 API Docs</div>
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
      <h2 id="top-title" style="font-size:1.2rem;font-weight:700;">Overview</h2>
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
let token = localStorage.getItem('gym_token') || '';
let currentUser = null;
let state = { members:[], trainers:[], plans:[] };

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
    token = res.data.token; localStorage.setItem('gym_token',token);
    currentUser = res.data.user;
    initApp();
  } else {
    document.getElementById('login-alert').innerHTML = `<div class="alert alert-error">${res.message}</div>`;
  }
}
async function doLogout() {
  await api('POST','/auth/logout');
  token=''; localStorage.removeItem('gym_token');
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
  document.getElementById('u-role').textContent = currentUser.role.replace('gym_','');
  nav('overview');
  loadGlobals();
}

async function loadGlobals() {
  const [m, t, p] = await Promise.all([
    api('GET','/members?limit=1000'),
    api('GET','/trainers?limit=1000'),
    api('GET','/plans?limit=1000')
  ]);
  if(m.success) state.members = m.data.data;
  if(t.success) state.trainers = t.data.data;
  if(p.success) state.plans = p.data.data;
}

function nav(sec) {
  document.querySelectorAll('.nav-item').forEach(el=>el.classList.remove('active'));
  document.querySelector(`[data-sec="${sec}"]`).classList.add('active');
  document.getElementById('top-title').textContent = sec.replace('_',' ').replace(/\b\w/g, c=>c.toUpperCase());
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
  const {summary, expiring_soon, recent_payments} = res.data;
  
  document.getElementById('content').innerHTML = `
    <div class="grid-4">
      <div class="kpi-card"><div class="kpi-icon" style="background:var(--primary-light);color:var(--primary)">👥</div><div><div class="kpi-val">${summary.total_members}</div><div class="kpi-label">Active Members</div></div></div>
      <div class="kpi-card"><div class="kpi-icon" style="background:var(--success-light);color:var(--success)">📅</div><div><div class="kpi-val">${summary.attendance_today}</div><div class="kpi-label">Today's Attendance</div></div></div>
      <div class="kpi-card"><div class="kpi-icon" style="background:var(--warning-light);color:var(--warning)">💰</div><div><div class="kpi-val">₹${summary.revenue_today}</div><div class="kpi-label">Today's Revenue</div></div></div>
      <div class="kpi-card"><div class="kpi-icon" style="background:var(--danger-light);color:var(--danger)">⚠️</div><div><div class="kpi-val">${expiring_soon.length}</div><div class="kpi-label">Expiring Soon</div></div></div>
    </div>
    
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
      <div class="card">
        <div class="card-header"><div class="card-title">Expiring Memberships (Next 7 Days)</div></div>
        <div class="table-wrap"><table>
          <thead><tr><th>Member</th><th>Plan</th><th>Expiry Date</th></tr></thead>
          <tbody>${expiring_soon.map(a=>`<tr><td>${a.member_name}</td><td>${a.plan_name}</td><td><span class="badge badge-danger">${a.end_date}</span></td></tr>`).join('')}</tbody>
        </table></div>
      </div>
      <div class="card">
        <div class="card-header"><div class="card-title">Recent Payments</div></div>
        <div class="table-wrap"><table>
          <thead><tr><th>Invoice</th><th>Member</th><th>Amount</th></tr></thead>
          <tbody>${recent_payments.map(p=>`<tr><td>${p.invoice_number}</td><td>${p.member_name}</td><td><strong style="color:var(--success)">₹${p.amount}</strong></td></tr>`).join('')}</tbody>
        </table></div>
      </div>
    </div>
  `;
};

/* MEMBERS */
sections.members = async () => {
  const res = await api('GET','/members?limit=100');
  if(!res.success) return;
  document.getElementById('content').innerHTML = `
    <div class="card">
      <div class="card-header"><div class="card-title">Gym Members</div><button class="btn btn-primary" onclick="addMember()">+ Add Member</button></div>
      <div class="table-wrap"><table>
        <thead><tr><th>ID</th><th>Name</th><th>Mobile</th><th>Gender</th><th>Actions</th></tr></thead>
        <tbody>${res.data.data.map(m=>`<tr>
          <td>${m.member_id}</td><td><strong>${m.name}</strong></td><td>${m.mobile}</td><td>${m.gender}</td>
          <td><button class="btn btn-sm btn-danger" onclick="delMember(${m.id})">Del</button></td>
        </tr>`).join('')}</tbody>
      </table></div>
    </div>
  `;
};
window.addMember = () => {
  openModal('Add Member', `
    <div class="form-grid">
      <div class="form-group"><label>Name</label><input type="text" id="m-name"></div>
      <div class="form-group"><label>Mobile</label><input type="text" id="m-mob"></div>
      <div class="form-group"><label>Gender</label><select id="m-gender"><option>Male</option><option>Female</option></select></div>
      <div class="form-group"><label>Date of Birth</label><input type="date" id="m-dob"></div>
      <div class="form-group"><label>Height (cm)</label><input type="number" id="m-height"></div>
      <div class="form-group"><label>Weight (kg)</label><input type="number" id="m-weight"></div>
      <div class="form-group full"><label>Medical History</label><textarea id="m-med"></textarea></div>
      <div class="form-group full"><button class="btn btn-primary" onclick="saveMember()">Save Member</button></div>
    </div>
  `);
};
window.saveMember = async () => {
  const d = { name:document.getElementById('m-name').value, mobile:document.getElementById('m-mob').value, gender:document.getElementById('m-gender').value, dob:document.getElementById('m-dob').value, height_cm:document.getElementById('m-height').value, weight_kg:document.getElementById('m-weight').value, medical_history:document.getElementById('m-med').value };
  await api('POST','/members', d); closeModal(); loadGlobals(); nav('members');
};
window.delMember = async (id) => { if(confirm('Delete?')){ await api('DELETE','/members/'+id); nav('members'); } };

/* TRAINERS */
sections.trainers = async () => {
  const res = await api('GET','/trainers?limit=100');
  if(!res.success) return;
  document.getElementById('content').innerHTML = `
    <div class="card">
      <div class="card-header"><div class="card-title">Trainers</div><button class="btn btn-primary" onclick="addTrainer()">+ Add Trainer</button></div>
      <div class="table-wrap"><table>
        <thead><tr><th>Name</th><th>Mobile</th><th>Specialization</th><th>Actions</th></tr></thead>
        <tbody>${res.data.data.map(t=>`<tr><td><strong>${t.name}</strong></td><td>${t.mobile}</td><td>${t.specialization}</td><td><button class="btn btn-sm btn-danger" onclick="delTrainer(${t.id})">Del</button></td></tr>`).join('')}</tbody>
      </table></div>
    </div>
  `;
};
window.addTrainer = () => {
  openModal('Add Trainer', `
    <div class="form-grid">
      <div class="form-group"><label>Name</label><input type="text" id="t-name"></div>
      <div class="form-group"><label>Mobile</label><input type="text" id="t-mob"></div>
      <div class="form-group"><label>Specialization</label><input type="text" id="t-spec"></div>
      <div class="form-group"><label>Salary</label><input type="number" id="t-sal" value="0"></div>
      <div class="form-group full"><button class="btn btn-primary" onclick="saveTrainer()">Save Trainer</button></div>
    </div>
  `);
};
window.saveTrainer = async () => {
  const d = { name:document.getElementById('t-name').value, mobile:document.getElementById('t-mob').value, specialization:document.getElementById('t-spec').value, salary:document.getElementById('t-sal').value };
  await api('POST','/trainers', d); closeModal(); loadGlobals(); nav('trainers');
};
window.delTrainer = async (id) => { if(confirm('Delete?')){ await api('DELETE','/trainers/'+id); nav('trainers'); } };

/* PLANS */
sections.plans = async () => {
  const res = await api('GET','/plans?limit=100');
  if(!res.success) return;
  document.getElementById('content').innerHTML = `
    <div class="card">
      <div class="card-header"><div class="card-title">Membership Plans</div><button class="btn btn-primary" onclick="addPlan()">+ Add Plan</button></div>
      <div class="table-wrap"><table>
        <thead><tr><th>Plan Name</th><th>Duration (Days)</th><th>Price</th></tr></thead>
        <tbody>${res.data.data.map(p=>`<tr><td><strong>${p.name}</strong></td><td>${p.duration_days}</td><td>₹${p.price}</td></tr>`).join('')}</tbody>
      </table></div>
    </div>
  `;
};
window.addPlan = () => {
  openModal('Add Plan', `
    <div class="form-grid">
      <div class="form-group"><label>Plan Name</label><input type="text" id="p-name"></div>
      <div class="form-group"><label>Duration (Days)</label><input type="number" id="p-days" value="30"></div>
      <div class="form-group"><label>Price (₹)</label><input type="number" id="p-price" value="0"></div>
      <div class="form-group full"><button class="btn btn-primary" onclick="savePlan()">Save Plan</button></div>
    </div>
  `);
};
window.savePlan = async () => {
  const d = { name:document.getElementById('p-name').value, duration_days:document.getElementById('p-days').value, price:document.getElementById('p-price').value };
  await api('POST','/plans', d); closeModal(); loadGlobals(); nav('plans');
};

/* MEMBERSHIPS */
sections.memberships = async () => {
  const res = await api('GET','/memberships');
  if(!res.success) return;
  document.getElementById('content').innerHTML = `
    <div class="card">
      <div class="card-header"><div class="card-title">Active Memberships</div><button class="btn btn-primary" onclick="assignMembership()">+ Assign/Renew</button></div>
      <div class="table-wrap"><table>
        <thead><tr><th>Member</th><th>Plan</th><th>Start Date</th><th>End Date</th><th>Status</th></tr></thead>
        <tbody>${res.data.data.map(m=>`<tr><td><strong>${m.member_name}</strong><br><small>${m.member_code}</small></td><td>${m.plan_name}</td><td>${m.start_date}</td><td><span class="badge badge-primary">${m.end_date}</span></td><td><span class="badge badge-success">${m.status}</span></td></tr>`).join('')}</tbody>
      </table></div>
    </div>
  `;
};
window.assignMembership = () => {
  openModal('Assign Membership', `
    <div class="form-grid">
      <div class="form-group"><label>Member</label><select id="ms-mem">${state.members.map(m=>`<option value="${m.id}">${m.name} (${m.member_id})</option>`).join('')}</select></div>
      <div class="form-group"><label>Plan</label><select id="ms-plan" onchange="updPlanDates()"><option value="">Select Plan</option>${state.plans.map(p=>`<option value="${p.id}" data-days="${p.duration_days}" data-price="${p.price}">${p.name} (₹${p.price})</option>`).join('')}</select></div>
      <div class="form-group"><label>Trainer (Optional)</label><select id="ms-train"><option value="">None</option>${state.trainers.map(t=>`<option value="${t.id}">${t.name}</option>`).join('')}</select></div>
      <div class="form-group"><label>Start Date</label><input type="date" id="ms-start" onchange="updPlanDates()" value="${new Date().toISOString().split('T')[0]}"></div>
      <div class="form-group"><label>End Date</label><input type="date" id="ms-end" readonly></div>
      <div class="form-group"><label>Amount Paid (₹)</label><input type="number" id="ms-amt" value="0"></div>
      <div class="form-group full"><button class="btn btn-primary" onclick="saveMembership()">Activate Membership & Record Payment</button></div>
    </div>
  `);
};
window.updPlanDates = () => {
  const sel = document.getElementById('ms-plan');
  if(!sel.value) return;
  const opt = sel.options[sel.selectedIndex];
  const days = parseInt(opt.getAttribute('data-days'));
  document.getElementById('ms-amt').value = opt.getAttribute('data-price');
  const start = document.getElementById('ms-start').value;
  if(start) {
    const d = new Date(start); d.setDate(d.getDate() + days);
    document.getElementById('ms-end').value = d.toISOString().split('T')[0];
  }
};
window.saveMembership = async () => {
  const d = { member_id:document.getElementById('ms-mem').value, plan_id:document.getElementById('ms-plan').value, trainer_id:document.getElementById('ms-train').value, start_date:document.getElementById('ms-start').value, end_date:document.getElementById('ms-end').value, amount_paid:document.getElementById('ms-amt').value };
  await api('POST','/memberships', d); closeModal(); nav('memberships');
};

/* PAYMENTS */
sections.payments = async () => {
  const res = await api('GET','/payments?limit=100');
  if(!res.success) return;
  document.getElementById('content').innerHTML = `
    <div class="card">
      <div class="card-header"><div class="card-title">Payments</div></div>
      <div class="table-wrap"><table>
        <thead><tr><th>Invoice</th><th>Date</th><th>Amount</th><th>Mode</th></tr></thead>
        <tbody>${res.data.data.map(p=>`<tr><td>${p.invoice_number}</td><td>${p.payment_date}</td><td><strong style="color:var(--success)">₹${p.amount}</strong></td><td>${p.payment_mode}</td></tr>`).join('')}</tbody>
      </table></div>
    </div>
  `;
};

/* DIET PLANS */
sections.diet_plans = async () => {
  const res = await api('GET','/diet-plans?limit=100');
  if(!res.success) return;
  document.getElementById('content').innerHTML = `
    <div class="card">
      <div class="card-header"><div class="card-title">Diet Plans</div><button class="btn btn-primary" onclick="addDiet()">+ Assign Diet</button></div>
      <div class="table-wrap"><table>
        <thead><tr><th>Date Assigned</th><th>Plan Details</th></tr></thead>
        <tbody>${res.data.data.map(d=>`<tr><td>${d.assigned_date}</td><td style="white-space:pre-wrap;">${d.plan_details}</td></tr>`).join('')}</tbody>
      </table></div>
    </div>
  `;
};
window.addDiet = () => {
  openModal('Assign Diet Plan', `
    <div class="form-grid">
      <div class="form-group"><label>Member</label><select id="d-mem">${state.members.map(m=>`<option value="${m.id}">${m.name}</option>`).join('')}</select></div>
      <div class="form-group full"><label>Diet Plan Details</label><textarea id="d-details" rows="6" placeholder="Breakfast:\nLunch:\nDinner:"></textarea></div>
      <div class="form-group full"><button class="btn btn-primary" onclick="saveDiet()">Assign Diet Plan</button></div>
    </div>
  `);
};
window.saveDiet = async () => {
  await api('POST','/diet-plans', {member_id:document.getElementById('d-mem').value, plan_details:document.getElementById('d-details').value}); closeModal(); nav('diet_plans');
};

/* ATTENDANCE */
sections.attendance = async () => {
  const res = await api('GET','/attendance?limit=50');
  if(!res.success) return;
  document.getElementById('content').innerHTML = `
    <div class="card">
      <div class="card-header"><div class="card-title">Attendance</div><button class="btn btn-primary" onclick="markAtt()">Check In/Out</button></div>
      <div class="table-wrap"><table>
        <thead><tr><th>Type</th><th>Ref ID</th><th>Check In</th><th>Check Out</th></tr></thead>
        <tbody>${res.data.data.map(a=>`<tr><td><span class="badge badge-primary">${a.user_type}</span></td><td>${a.reference_id}</td><td>${a.check_in}</td><td>${a.check_out||'-'}</td></tr>`).join('')}</tbody>
      </table></div>
    </div>
  `;
};
window.markAtt = () => {
  openModal('Mark Attendance', `
    <div class="form-grid">
      <div class="form-group"><label>User Type</label><select id="a-type" onchange="updAttList()"><option>Member</option><option>Trainer</option></select></div>
      <div class="form-group"><label>Select Person</label><select id="a-ref"></select></div>
      <div class="form-group full"><button class="btn btn-primary" onclick="saveAtt()">Check In / Check Out</button></div>
    </div>
  `);
  updAttList();
};
window.updAttList = () => {
  const typ = document.getElementById('a-type').value;
  document.getElementById('a-ref').innerHTML = (typ==='Member' ? state.members : state.trainers).map(x=>`<option value="${x.id}">${x.name}</option>`).join('');
};
window.saveAtt = async () => {
  const r = await api('POST','/attendance', {user_type:document.getElementById('a-type').value, reference_id:document.getElementById('a-ref').value});
  alert(r.message); closeModal(); nav('attendance');
};

checkAuth();
</script>
</body>
</html>
