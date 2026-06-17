<?php
if (!defined('ABSPATH')) { exit; }
$site_url = get_site_url();
$api_base = $site_url . '/wp-json/hr-management/v1';
$plugin_url = plugin_dir_url(dirname(__FILE__));
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>HR & Payroll ERP – Dashboard</title>
<meta name="description" content="Premium HR & Payroll ERP Dashboard. Manage employees, attendance, leaves, payroll, PF/ESI, payslips, and documents in one place." />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
<style>
/* ═══════════════════════════════════════════════════════════════
   DESIGN TOKENS – Light Mode by default, Dark Mode toggled via [data-theme="dark"]
═══════════════════════════════════════════════════════════════ */
:root {
  --bg-primary:    #f0f4f8;
  --bg-secondary:  #ffffff;
  --bg-tertiary:   #e8edf5;
  --bg-sidebar:    #1e2a3a;
  --bg-sidebar-hover: #2d3e52;
  --bg-sidebar-active:#3b82f6;
  --text-primary:  #0f172a;
  --text-secondary:#475569;
  --text-muted:    #94a3b8;
  --text-sidebar:  #cbd5e1;
  --text-sidebar-active:#ffffff;
  --border:        rgba(15,23,42,0.08);
  --shadow-sm:     0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
  --shadow-md:     0 4px 16px rgba(0,0,0,0.08);
  --shadow-lg:     0 12px 40px rgba(0,0,0,0.12);
  --blue:          #3b82f6;
  --blue-dark:     #2563eb;
  --green:         #10b981;
  --amber:         #f59e0b;
  --red:           #ef4444;
  --purple:        #8b5cf6;
  --cyan:          #06b6d4;
  --radius-sm:     8px;
  --radius-md:     12px;
  --radius-lg:     16px;
  --sidebar-w:     260px;
  --header-h:      64px;
  --transition:    all 0.22s cubic-bezier(.4,0,.2,1);
}
[data-theme="dark"] {
  --bg-primary:    #0d1117;
  --bg-secondary:  #161b2e;
  --bg-tertiary:   #1e2535;
  --bg-sidebar:    #111827;
  --bg-sidebar-hover:#1f2d42;
  --text-primary:  #f1f5f9;
  --text-secondary:#94a3b8;
  --text-muted:    #64748b;
  --border:        rgba(255,255,255,0.07);
  --shadow-sm:     0 1px 3px rgba(0,0,0,0.3);
  --shadow-md:     0 4px 16px rgba(0,0,0,0.4);
  --shadow-lg:     0 12px 40px rgba(0,0,0,0.5);
}

/* ── RESET ── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%;font-family:'Inter',sans-serif;font-size:14px;line-height:1.5;background:var(--bg-primary);color:var(--text-primary);transition:background .3s,color .3s}
a{color:inherit;text-decoration:none}
ul,ol{list-style:none}
button{cursor:pointer;border:none;background:none;font-family:inherit}
input,select,textarea{font-family:inherit}
img{max-width:100%}

/* ── AUTH SCREEN ── */
#auth-screen{display:flex;align-items:center;justify-content:center;min-height:100vh;background:linear-gradient(135deg,#e0f0ff 0%,#f0f4ff 50%,#e8f5ff 100%);position:fixed;inset:0;z-index:9999}
[data-theme="dark"] #auth-screen{background:linear-gradient(135deg,#0a0f1e 0%,#0d1117 60%,#111827 100%)}
.auth-card{background:var(--bg-secondary);border:1px solid var(--border);border-radius:var(--radius-lg);padding:40px 36px;width:100%;max-width:420px;box-shadow:var(--shadow-lg)}
.auth-logo{text-align:center;margin-bottom:28px}
.auth-logo .icon{width:56px;height:56px;background:linear-gradient(135deg,#3b82f6,#06b6d4);border-radius:14px;display:inline-flex;align-items:center;justify-content:center;font-size:26px;margin-bottom:12px}
.auth-logo h1{font-size:22px;font-weight:800;color:var(--text-primary)}
.auth-logo p{font-size:13px;color:var(--text-secondary);margin-top:4px}
.auth-tabs{display:flex;gap:4px;background:var(--bg-tertiary);border-radius:var(--radius-sm);padding:4px;margin-bottom:24px}
.auth-tab{flex:1;padding:8px;text-align:center;border-radius:6px;font-size:13px;font-weight:500;color:var(--text-secondary);transition:var(--transition);cursor:pointer}
.auth-tab.active{background:var(--bg-secondary);color:var(--text-primary);box-shadow:var(--shadow-sm);font-weight:600}
.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:12px;font-weight:600;color:var(--text-secondary);margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px}
.form-control{width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-primary);color:var(--text-primary);font-size:14px;transition:var(--transition);outline:none}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(59,130,246,.15)}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:10px 20px;border-radius:var(--radius-sm);font-size:14px;font-weight:600;transition:var(--transition);cursor:pointer;white-space:nowrap}
.btn-primary{background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;border:none;box-shadow:0 2px 8px rgba(59,130,246,.35)}
.btn-primary:hover{background:linear-gradient(135deg,#2563eb,#1d4ed8);transform:translateY(-1px);box-shadow:0 4px 14px rgba(59,130,246,.45)}
.btn-primary:active{transform:translateY(0)}
.btn-block{width:100%;justify-content:center}
.btn-sm{padding:7px 14px;font-size:13px}
.btn-outline{background:transparent;border:1.5px solid var(--border);color:var(--text-primary)}
.btn-outline:hover{border-color:var(--blue);color:var(--blue);background:rgba(59,130,246,.05)}
.btn-danger{background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;border:none}
.btn-danger:hover{opacity:.9;transform:translateY(-1px)}
.btn-success{background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none}
.btn-success:hover{opacity:.9;transform:translateY(-1px)}
.btn-amber{background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border:none}
.hint-row{display:flex;gap:6px;flex-wrap:wrap;margin-top:12px}
.hint-chip{padding:5px 10px;background:var(--bg-tertiary);border-radius:20px;font-size:11px;color:var(--text-secondary);cursor:pointer;border:1px solid var(--border);transition:var(--transition)}
.hint-chip:hover{background:var(--blue);color:#fff;border-color:var(--blue)}
.auth-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#dc2626;border-radius:var(--radius-sm);padding:10px 14px;font-size:13px;margin-bottom:16px;display:none}

/* ── APP SHELL ── */
#app-shell{display:none;height:100vh;overflow:hidden}
#app-shell.visible{display:flex}

/* ── SIDEBAR ── */
.sidebar{width:var(--sidebar-w);background:var(--bg-sidebar);display:flex;flex-direction:column;height:100vh;position:fixed;left:0;top:0;bottom:0;z-index:100;transition:transform .3s cubic-bezier(.4,0,.2,1)}
.sidebar-header{padding:20px 16px;border-bottom:1px solid rgba(255,255,255,.07)}
.sidebar-brand{display:flex;align-items:center;gap:10px}
.sidebar-brand .icon{width:36px;height:36px;background:linear-gradient(135deg,#3b82f6,#06b6d4);border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.sidebar-brand span{font-size:14px;font-weight:700;color:#fff}
.sidebar-brand small{display:block;font-size:11px;font-weight:400;color:#64748b}
.sidebar-nav{flex:1;overflow-y:auto;padding:12px 8px}
.sidebar-nav::-webkit-scrollbar{width:4px}
.sidebar-nav::-webkit-scrollbar-track{background:transparent}
.sidebar-nav::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:2px}
.sidebar-section{margin-bottom:8px}
.sidebar-section-label{font-size:10px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.8px;padding:8px 12px 4px}
.nav-item{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;color:var(--text-sidebar);font-size:13px;font-weight:500;cursor:pointer;transition:var(--transition);margin-bottom:2px;position:relative}
.nav-item:hover{background:var(--bg-sidebar-hover);color:#fff}
.nav-item.active{background:var(--bg-sidebar-active);color:#fff;box-shadow:0 2px 8px rgba(59,130,246,.35)}
.nav-item .icon{font-size:16px;width:20px;text-align:center;flex-shrink:0}
.nav-item .badge{margin-left:auto;background:rgba(239,68,68,.85);color:#fff;font-size:10px;font-weight:700;padding:2px 6px;border-radius:10px;min-width:18px;text-align:center}
.sidebar-footer{padding:12px 8px;border-top:1px solid rgba(255,255,255,.07)}
.user-card{display:flex;align-items:center;gap:10px;padding:10px;border-radius:var(--radius-sm);background:rgba(255,255,255,.05);cursor:pointer;transition:var(--transition)}
.user-card:hover{background:rgba(255,255,255,.1)}
.user-avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#8b5cf6);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#fff;flex-shrink:0}
.user-name{font-size:13px;font-weight:600;color:#e2e8f0}
.user-role{font-size:11px;color:#64748b}
.user-logout{margin-left:auto;color:#64748b;font-size:18px;transition:var(--transition)}
.user-logout:hover{color:#ef4444}

/* ── MAIN CONTENT ── */
.main-content{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;height:100vh;overflow:hidden;min-width:0}
.topbar{height:var(--header-h);background:var(--bg-secondary);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 24px;gap:16px;flex-shrink:0;box-shadow:var(--shadow-sm)}
.topbar-title{font-size:18px;font-weight:700;color:var(--text-primary)}
.topbar-subtitle{font-size:13px;color:var(--text-muted);margin-left:4px}
.topbar-right{margin-left:auto;display:flex;align-items:center;gap:10px}
.theme-toggle{width:36px;height:36px;border-radius:var(--radius-sm);background:var(--bg-tertiary);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:17px;cursor:pointer;transition:var(--transition)}
.theme-toggle:hover{background:var(--blue);color:#fff;border-color:var(--blue)}
.page-content{flex:1;overflow-y:auto;padding:24px;background:var(--bg-primary)}
.page-content::-webkit-scrollbar{width:6px}
.page-content::-webkit-scrollbar-track{background:transparent}
.page-content::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}

/* ── TAB PANELS ── */
.tab-panel{display:none}
.tab-panel.active{display:block}

/* ── STATS GRID ── */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px}
.stat-card{background:var(--bg-secondary);border:1px solid var(--border);border-radius:var(--radius-md);padding:20px;box-shadow:var(--shadow-sm);transition:var(--transition);position:relative;overflow:hidden}
.stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:var(--radius-md) var(--radius-md) 0 0}
.stat-card.blue::before{background:linear-gradient(90deg,#3b82f6,#06b6d4)}
.stat-card.green::before{background:linear-gradient(90deg,#10b981,#34d399)}
.stat-card.amber::before{background:linear-gradient(90deg,#f59e0b,#fbbf24)}
.stat-card.red::before{background:linear-gradient(90deg,#ef4444,#f87171)}
.stat-card.purple::before{background:linear-gradient(90deg,#8b5cf6,#a78bfa)}
.stat-card:hover{box-shadow:var(--shadow-md);transform:translateY(-2px)}
.stat-icon{font-size:28px;margin-bottom:10px}
.stat-value{font-size:28px;font-weight:800;color:var(--text-primary);line-height:1}
.stat-label{font-size:12px;color:var(--text-muted);margin-top:4px;font-weight:500}
.stat-sub{font-size:11px;color:var(--text-muted);margin-top:6px}
.stat-sub span{color:var(--green);font-weight:600}

/* ── CARDS ── */
.card{background:var(--bg-secondary);border:1px solid var(--border);border-radius:var(--radius-md);box-shadow:var(--shadow-sm)}
.card-header{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.card-title{font-size:15px;font-weight:700;color:var(--text-primary)}
.card-body{padding:20px}
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px}
@media(max-width:900px){.two-col{grid-template-columns:1fr}}

/* ── TABLE ── */
.table-wrapper{overflow-x:auto;border-radius:var(--radius-md)}
table{width:100%;border-collapse:collapse;font-size:13px}
thead tr{background:var(--bg-tertiary)}
th{padding:11px 14px;text-align:left;font-size:11px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
td{padding:11px 14px;border-bottom:1px solid var(--border);color:var(--text-primary);vertical-align:middle}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:var(--bg-tertiary)}
.table-empty{text-align:center;padding:32px;color:var(--text-muted);font-size:14px}

/* ── BADGES ── */
.badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap}
.badge-green{background:rgba(16,185,129,.12);color:#059669}
.badge-amber{background:rgba(245,158,11,.12);color:#b45309}
.badge-red{background:rgba(239,68,68,.12);color:#dc2626}
.badge-blue{background:rgba(59,130,246,.12);color:#2563eb}
.badge-purple{background:rgba(139,92,246,.12);color:#7c3aed}
.badge-gray{background:rgba(100,116,139,.12);color:#475569}

/* ── MODAL ── */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center;padding:16px}
.modal-overlay.open{display:flex}
.modal{background:var(--bg-secondary);border:1px solid var(--border);border-radius:var(--radius-lg);width:100%;max-width:520px;max-height:90vh;overflow-y:auto;box-shadow:var(--shadow-lg);animation:modal-in .22s ease}
@keyframes modal-in{from{opacity:0;transform:scale(.97) translateY(10px)}to{opacity:1;transform:none}}
.modal-header{padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.modal-title{font-size:16px;font-weight:700;color:var(--text-primary)}
.modal-close{font-size:20px;color:var(--text-muted);cursor:pointer;line-height:1;padding:4px;transition:var(--transition)}
.modal-close:hover{color:var(--red)}
.modal-body{padding:24px}
.modal-footer{padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.form-hint{font-size:11px;color:var(--text-muted);margin-top:4px}
select.form-control option{background:var(--bg-secondary)}

/* ── TOAST ── */
#toast-container{position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none}
.toast{background:var(--bg-secondary);border:1px solid var(--border);border-radius:var(--radius-md);padding:12px 16px;box-shadow:var(--shadow-lg);display:flex;align-items:flex-start;gap:10px;max-width:320px;font-size:13px;animation:toast-in .25s ease;pointer-events:auto}
@keyframes toast-in{from{opacity:0;transform:translateX(40px)}to{opacity:1;transform:none}}
.toast.success .toast-icon::before{content:'✓';color:var(--green)}
.toast.error .toast-icon::before{content:'✕';color:var(--red)}
.toast.info .toast-icon::before{content:'ℹ';color:var(--blue)}
.toast-icon{font-weight:700;font-size:14px}
.toast-msg{flex:1;color:var(--text-primary);line-height:1.4}

/* ── ATTENDANCE CARD ── */
.checkin-card{background:linear-gradient(135deg,#1e40af,#1d4ed8);border-radius:var(--radius-lg);padding:28px;color:#fff;margin-bottom:24px;position:relative;overflow:hidden}
.checkin-card::after{content:'🕐';position:absolute;right:24px;bottom:16px;font-size:60px;opacity:.15}
.checkin-time{font-size:36px;font-weight:800;letter-spacing:-1px}
.checkin-date{font-size:14px;opacity:.75;margin-top:2px}
.checkin-status{margin-top:16px;display:flex;align-items:center;gap:10px}
.checkin-dot{width:10px;height:10px;border-radius:50%;background:#4ade80;box-shadow:0 0 0 3px rgba(74,222,128,.3);animation:pulse 2s infinite}
@keyframes pulse{0%,100%{box-shadow:0 0 0 3px rgba(74,222,128,.3)}50%{box-shadow:0 0 0 6px rgba(74,222,128,.1)}}
.checkin-btns{margin-top:20px;display:flex;gap:12px}
.btn-checkin{background:rgba(255,255,255,.2);color:#fff;border:1.5px solid rgba(255,255,255,.35);backdrop-filter:blur(8px);padding:10px 22px;border-radius:var(--radius-sm);font-weight:600;font-size:14px;transition:var(--transition)}
.btn-checkin:hover{background:rgba(255,255,255,.3)}
.btn-checkin.checkout{background:rgba(239,68,68,.3);border-color:rgba(239,68,68,.5)}
.btn-checkin.checkout:hover{background:rgba(239,68,68,.5)}

/* ── LEAVE BALANCE PILLS ── */
.leave-pills{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:24px}
.leave-pill{background:var(--bg-secondary);border:1px solid var(--border);border-radius:var(--radius-md);padding:14px 18px;flex:1;min-width:120px;text-align:center;box-shadow:var(--shadow-sm)}
.leave-pill-num{font-size:26px;font-weight:800;color:var(--blue)}
.leave-pill-label{font-size:11px;color:var(--text-muted);font-weight:500;margin-top:2px}

/* ── SEARCH & FILTERS ── */
.toolbar{display:flex;align-items:center;gap:10px;margin-bottom:16px;flex-wrap:wrap}
.search-box{flex:1;min-width:200px;position:relative}
.search-box input{width:100%;padding:9px 14px 9px 36px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-secondary);color:var(--text-primary);font-size:13px;outline:none;transition:var(--transition)}
.search-box input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(59,130,246,.12)}
.search-box::before{content:'🔍';position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:14px;pointer-events:none}
.filter-select{padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-secondary);color:var(--text-primary);font-size:13px;outline:none;cursor:pointer;transition:var(--transition)}
.filter-select:focus{border-color:var(--blue)}

/* ── PAGINATION ── */
.pagination{display:flex;align-items:center;gap:6px;justify-content:flex-end;margin-top:16px}
.page-btn{width:32px;height:32px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:var(--bg-secondary);color:var(--text-secondary);font-size:13px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:var(--transition)}
.page-btn:hover,.page-btn.active{background:var(--blue);color:#fff;border-color:var(--blue)}
.page-info{font-size:12px;color:var(--text-muted);margin:0 6px}

/* ── MISC ── */
.section-title{font-size:16px;font-weight:700;color:var(--text-primary);margin-bottom:16px}
.loader{display:inline-block;width:18px;height:18px;border:2.5px solid var(--border);border-top-color:var(--blue);border-radius:50%;animation:spin .7s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.empty-state{text-align:center;padding:48px 24px;color:var(--text-muted)}
.empty-state .icon{font-size:48px;margin-bottom:12px;display:block}
.empty-state p{font-size:15px}
.skeleton{background:linear-gradient(90deg,var(--bg-tertiary) 25%,var(--border) 50%,var(--bg-tertiary) 75%);background-size:200% 100%;animation:skel 1.4s infinite;border-radius:6px;height:14px;margin-bottom:8px}
@keyframes skel{0%{background-position:200% 0}100%{background-position:-200% 0}}

/* ── PAYSLIP PREVIEW ── */
.payslip-preview{font-size:13px;line-height:1.7}
.payslip-preview .ph{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px dashed var(--border)}
.payslip-preview .ph:last-child{border:none;font-weight:700;font-size:15px;padding-top:12px}

/* ── RESPONSIVE ── */
@media(max-width:768px){
  .sidebar{transform:translateX(-100%)}
  .sidebar.open{transform:none}
  .main-content{margin-left:0}
  .stats-grid{grid-template-columns:1fr 1fr}
  .form-row{grid-template-columns:1fr}
}
.hamburger{display:none;width:36px;height:36px;border-radius:var(--radius-sm);background:var(--bg-tertiary);border:1px solid var(--border);font-size:18px;align-items:center;justify-content:center}
@media(max-width:768px){.hamburger{display:flex}}
.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:99}
.sidebar-overlay.open{display:block}
</style>
</head>
<body>
<!-- ANTI-FOUC: Restore theme and check session BEFORE render -->
<script>
(function(){
  var t = localStorage.getItem('hr_theme') || 'light';
  document.documentElement.setAttribute('data-theme', t);
})();
</script>

<!-- ── AUTH SCREEN ─────────────────────────────────────────────────── -->
<div id="auth-screen">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="icon">🏢</div>
      <h1>HR & Payroll ERP</h1>
      <p>Sign in to access your dashboard</p>
    </div>
    <div class="auth-tabs">
      <div class="auth-tab active" id="tab-login" onclick="switchAuthTab('login')">Login</div>
      <div class="auth-tab" id="tab-register" onclick="switchAuthTab('register')">Register</div>
    </div>
    <div id="auth-error" class="auth-error"></div>

    <!-- LOGIN FORM -->
    <form id="login-form" onsubmit="doLogin(event)">
      <div class="form-group">
        <label>Username</label>
        <input id="login-username" class="form-control" type="text" placeholder="e.g. hsuperadmin" autocomplete="username" required />
      </div>
      <div class="form-group">
        <label>Password</label>
        <input id="login-password" class="form-control" type="password" placeholder="••••••••" autocomplete="current-password" required />
      </div>
      <button type="submit" class="btn btn-primary btn-block" id="login-btn">
        <span>Sign In</span>
      </button>
      <div class="hint-row">
        <span style="font-size:11px;color:var(--text-muted);width:100%;margin-top:4px">Demo accounts (password: 123456):</span>
        <span class="hint-chip" onclick="fillLogin('hsuperadmin')">🔑 Super Admin</span>
        <span class="hint-chip" onclick="fillLogin('hmanager')">👤 Manager</span>
        <span class="hint-chip" onclick="fillLogin('haccountant')">💼 Accountant</span>
        <span class="hint-chip" onclick="fillLogin('hemployee')">🙋 Employee</span>
      </div>
    </form>

    <!-- REGISTER FORM -->
    <form id="register-form" style="display:none" onsubmit="doRegister(event)">
      <div class="form-row">
        <div class="form-group">
          <label>Username</label>
          <input id="reg-username" class="form-control" type="text" placeholder="username" required />
        </div>
        <div class="form-group">
          <label>Display Name</label>
          <input id="reg-name" class="form-control" type="text" placeholder="Full name" />
        </div>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input id="reg-email" class="form-control" type="email" placeholder="email@company.com" required />
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Password</label>
          <input id="reg-password" class="form-control" type="password" placeholder="••••••••" required />
        </div>
        <div class="form-group">
          <label>Role</label>
          <select id="reg-role" class="form-control">
            <option value="hr_employee">Employee</option>
            <option value="hr_accountant">Accountant</option>
            <option value="hr_manager">Manager</option>
            <option value="hr_super_admin">Super Admin</option>
          </select>
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Create Account</button>
    </form>
  </div>
</div>

<!-- ── APP SHELL ─────────────────────────────────────────────────────── -->
<div id="app-shell">
  <!-- Sidebar Overlay (mobile) -->
  <div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

  <!-- SIDEBAR -->
  <nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-brand">
        <div class="icon">🏢</div>
        <div>
          <span>HR & Payroll ERP</span>
          <small>Management System</small>
        </div>
      </div>
    </div>
    <div class="sidebar-nav">
      <div class="sidebar-section">
        <div class="sidebar-section-label">Overview</div>
        <div class="nav-item active" data-tab="dashboard" onclick="goTab('dashboard')">
          <span class="icon">📊</span> Dashboard
        </div>
      </div>
      <div class="sidebar-section">
        <div class="sidebar-section-label">HR Management</div>
        <div class="nav-item admin-only" data-tab="employees" onclick="goTab('employees')">
          <span class="icon">👥</span> Employees
        </div>
        <div class="nav-item" data-tab="attendance" onclick="goTab('attendance')">
          <span class="icon">🕐</span> Attendance
        </div>
        <div class="nav-item" data-tab="leaves" onclick="goTab('leaves')">
          <span class="icon">📅</span> Leave Requests
          <span class="badge" id="pending-leaves-badge" style="display:none">0</span>
        </div>
      </div>
      <div class="sidebar-section">
        <div class="sidebar-section-label">Payroll</div>
        <div class="nav-item payroll-only" data-tab="salaries" onclick="goTab('salaries')">
          <span class="icon">💰</span> Salary Structures
        </div>
        <div class="nav-item" data-tab="payslips" onclick="goTab('payslips')">
          <span class="icon">📄</span> Payslips
        </div>
      </div>
      <div class="sidebar-section">
        <div class="sidebar-section-label">Files & Logs</div>
        <div class="nav-item" data-tab="documents" onclick="goTab('documents')">
          <span class="icon">📁</span> Documents
        </div>
        <div class="nav-item admin-only" data-tab="activity" onclick="goTab('activity')">
          <span class="icon">📋</span> Activity Logs
        </div>
      </div>
      <div class="sidebar-section admin-only">
        <div class="sidebar-section-label">Settings</div>
        <div class="nav-item" data-tab="settings" onclick="goTab('settings')">
          <span class="icon">⚙️</span> SMTP Settings
        </div>
      </div>
    </div>
    <div class="sidebar-footer">
      <div class="user-card" id="user-card">
        <div class="user-avatar" id="user-avatar">U</div>
        <div>
          <div class="user-name" id="user-name-display">Loading...</div>
          <div class="user-role" id="user-role-display">—</div>
        </div>
        <span class="user-logout" title="Logout" onclick="doLogout()">⏻</span>
      </div>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
  <div class="main-content">
    <!-- TOPBAR -->
    <div class="topbar">
      <button class="hamburger" onclick="openSidebar()">☰</button>
      <div>
        <span class="topbar-title" id="topbar-title">Dashboard</span>
        <span class="topbar-subtitle" id="topbar-date"></span>
      </div>
      <div class="topbar-right">
        <a href="<?php echo esc_url($site_url); ?>/hr-management-api-docs/" target="_blank" class="btn btn-outline btn-sm" title="API Docs">📚 API Docs</a>
        <div class="theme-toggle" id="theme-toggle-btn" onclick="toggleTheme()" title="Toggle theme">🌙</div>
      </div>
    </div>

    <!-- PAGE CONTENT -->
    <div class="page-content" id="page-content">

      <!-- ═══════ DASHBOARD TAB ═══════ -->
      <div class="tab-panel active" id="panel-dashboard">
        <div id="dashboard-loading" style="text-align:center;padding:40px"><div class="loader"></div><p style="margin-top:12px;color:var(--text-muted)">Loading dashboard…</p></div>
        <div id="dashboard-content" style="display:none"></div>
      </div>

      <!-- ═══════ EMPLOYEES TAB ═══════ -->
      <div class="tab-panel" id="panel-employees">
        <div class="toolbar">
          <div class="search-box"><input type="text" id="emp-search" placeholder="Search employees…" oninput="loadEmployees()" /></div>
          <select class="filter-select" id="emp-dept-filter" onchange="loadEmployees()">
            <option value="">All Departments</option>
            <option>Administration</option><option>Human Resources</option>
            <option>Finance & Accounts</option><option>Engineering</option>
            <option>Sales</option><option>Operations</option>
          </select>
          <select class="filter-select" id="emp-status-filter" onchange="loadEmployees()">
            <option value="">All Status</option>
            <option value="ACTIVE">Active</option>
            <option value="SUSPENDED">Suspended</option>
          </select>
          <button class="btn btn-primary btn-sm" onclick="openModal('modal-add-employee')">+ Add Employee</button>
        </div>
        <div class="card">
          <div class="card-header">
            <span class="card-title">Employee Directory</span>
            <span id="emp-count" style="font-size:12px;color:var(--text-muted)"></span>
          </div>
          <div class="table-wrapper">
            <table id="employees-table">
              <thead><tr><th>#</th><th>Name</th><th>Department</th><th>Designation</th><th>Joining</th><th>PF No.</th><th>Status</th><th>Actions</th></tr></thead>
              <tbody id="employees-tbody"><tr><td colspan="8" class="table-empty"><div class="loader"></div></td></tr></tbody>
            </table>
          </div>
          <div style="padding:12px 20px"><div class="pagination" id="emp-pagination"></div></div>
        </div>
      </div>

      <!-- ═══════ ATTENDANCE TAB ═══════ -->
      <div class="tab-panel" id="panel-attendance">
        <!-- Check-in card (employee view) -->
        <div id="checkin-card-wrap" class="checkin-card">
          <div class="checkin-time" id="live-clock">--:--:--</div>
          <div class="checkin-date" id="today-date"></div>
          <div class="checkin-status" id="checkin-status-line"><span class="checkin-dot"></span><span id="checkin-status-text">Loading…</span></div>
          <div class="checkin-btns">
            <button class="btn-checkin" id="btn-checkin" onclick="doCheckIn()">✅ Check In</button>
            <button class="btn-checkin checkout" id="btn-checkout" onclick="doCheckOut()" style="display:none">🔴 Check Out</button>
          </div>
        </div>
        <div class="toolbar">
          <div class="search-box"><input type="text" id="att-emp-id" placeholder="Employee ID…" oninput="loadAttendance()" /></div>
          <input type="date" class="filter-select" id="att-date-filter" onchange="loadAttendance()" />
          <select class="filter-select" id="att-status-filter" onchange="loadAttendance()">
            <option value="">All Status</option>
            <option>Present</option><option>Late</option><option>Half Day</option><option>Absent</option>
          </select>
          <button class="btn btn-outline btn-sm admin-only" onclick="openModal('modal-manual-attendance')">Manual Entry</button>
        </div>
        <div class="card">
          <div class="card-header"><span class="card-title">Attendance Records</span></div>
          <div class="table-wrapper">
            <table>
              <thead><tr><th>#</th><th>Employee</th><th>Date</th><th>Check In</th><th>Check Out</th><th>Hours</th><th>Status</th><th>Notes</th></tr></thead>
              <tbody id="attendance-tbody"><tr><td colspan="8" class="table-empty"><div class="loader"></div></td></tr></tbody>
            </table>
          </div>
          <div style="padding:12px 20px"><div class="pagination" id="att-pagination"></div></div>
        </div>
      </div>

      <!-- ═══════ LEAVES TAB ═══════ -->
      <div class="tab-panel" id="panel-leaves">
        <div id="leave-balance-wrap" class="leave-pills">
          <div class="leave-pill"><div class="leave-pill-num" id="lb-casual">—</div><div class="leave-pill-label">Casual Leaves</div></div>
          <div class="leave-pill"><div class="leave-pill-num" id="lb-medical">—</div><div class="leave-pill-label">Medical Leaves</div></div>
          <div class="leave-pill"><div class="leave-pill-num" id="lb-earned">—</div><div class="leave-pill-label">Earned Leaves</div></div>
          <div class="leave-pill"><div class="leave-pill-num" id="lb-unpaid" style="color:var(--red)">—</div><div class="leave-pill-label">Unpaid Leaves</div></div>
        </div>
        <div class="toolbar">
          <select class="filter-select" id="leave-status-filter" onchange="loadLeaves()">
            <option value="">All Status</option>
            <option>Pending</option><option>Approved</option><option>Rejected</option>
          </select>
          <select class="filter-select" id="leave-type-filter" onchange="loadLeaves()">
            <option value="">All Types</option>
            <option>Casual</option><option>Medical</option><option>Earned</option><option>Unpaid</option>
          </select>
          <button class="btn btn-primary btn-sm" onclick="openModal('modal-apply-leave')">+ Apply Leave</button>
        </div>
        <div class="card">
          <div class="card-header"><span class="card-title">Leave Requests</span></div>
          <div class="table-wrapper">
            <table>
              <thead><tr><th>#</th><th>Employee</th><th>Type</th><th>From</th><th>To</th><th>Days</th><th>Reason</th><th>Status</th><th>Actions</th></tr></thead>
              <tbody id="leaves-tbody"><tr><td colspan="9" class="table-empty"><div class="loader"></div></td></tr></tbody>
            </table>
          </div>
          <div style="padding:12px 20px"><div class="pagination" id="leave-pagination"></div></div>
        </div>
      </div>

      <!-- ═══════ SALARIES TAB ═══════ -->
      <div class="tab-panel" id="panel-salaries">
        <div class="toolbar">
          <div class="search-box"><input type="text" id="sal-search" placeholder="Search…" oninput="loadSalaries()" /></div>
          <button class="btn btn-primary btn-sm payroll-only" onclick="openModal('modal-upsert-salary')">+ Setup Salary</button>
        </div>
        <div class="card">
          <div class="card-header"><span class="card-title">Salary Structures</span></div>
          <div class="table-wrapper">
            <table>
              <thead><tr><th>#</th><th>Employee</th><th>Dept</th><th>Base</th><th>Allowances</th><th>PF</th><th>ESI</th><th>Deductions</th><th>Net Pay</th><th>Status</th><th>Actions</th></tr></thead>
              <tbody id="salaries-tbody"><tr><td colspan="11" class="table-empty"><div class="loader"></div></td></tr></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ═══════ PAYSLIPS TAB ═══════ -->
      <div class="tab-panel" id="panel-payslips">
        <div class="toolbar">
          <select class="filter-select" id="ps-month-filter" onchange="loadPayslips()">
            <option value="">All Months</option>
            <?php
            $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            foreach ($months as $m) { echo "<option>$m</option>"; }
            ?>
          </select>
          <select class="filter-select" id="ps-year-filter" onchange="loadPayslips()">
            <option value="">All Years</option>
            <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--) { echo "<option>$y</option>"; } ?>
          </select>
          <select class="filter-select" id="ps-status-filter" onchange="loadPayslips()">
            <option value="">All Status</option>
            <option>Generated</option><option>Paid</option>
          </select>
          <button class="btn btn-primary btn-sm payroll-only" onclick="openModal('modal-generate-payslip')">+ Generate Payslip</button>
        </div>
        <div class="card">
          <div class="card-header"><span class="card-title">Payslips</span></div>
          <div class="table-wrapper">
            <table>
              <thead><tr><th>#</th><th>Employee</th><th>Month</th><th>Year</th><th>Base</th><th>Net Pay</th><th>PF</th><th>ESI</th><th>Status</th><th>Actions</th></tr></thead>
              <tbody id="payslips-tbody"><tr><td colspan="10" class="table-empty"><div class="loader"></div></td></tr></tbody>
            </table>
          </div>
          <div style="padding:12px 20px"><div class="pagination" id="ps-pagination"></div></div>
        </div>
      </div>

      <!-- ═══════ DOCUMENTS TAB ═══════ -->
      <div class="tab-panel" id="panel-documents">
        <div class="toolbar">
          <div class="search-box"><input type="text" id="doc-search" placeholder="Search documents…" oninput="loadDocuments()" /></div>
          <select class="filter-select" id="doc-type-filter" onchange="loadDocuments()">
            <option value="">All Types</option>
            <option>ID Proof</option><option>Address Proof</option><option>Educational Certificate</option>
            <option>Experience Letter</option><option>Bank Details</option><option>PAN Card</option><option>Aadhaar Card</option>
          </select>
          <button class="btn btn-primary btn-sm" onclick="openModal('modal-add-document')">+ Add Document</button>
        </div>
        <div class="card">
          <div class="card-header"><span class="card-title">Employee Documents</span></div>
          <div class="table-wrapper">
            <table>
              <thead><tr><th>#</th><th>Employee</th><th>Document Name</th><th>Type</th><th>URL</th><th>Status</th><th>Uploaded</th><th>Actions</th></tr></thead>
              <tbody id="documents-tbody"><tr><td colspan="8" class="table-empty"><div class="loader"></div></td></tr></tbody>
            </table>
          </div>
          <div style="padding:12px 20px"><div class="pagination" id="doc-pagination"></div></div>
        </div>
      </div>

      <!-- ═══════ ACTIVITY LOGS TAB ═══════ -->
      <div class="tab-panel" id="panel-activity">
        <div class="card">
          <div class="card-header">
            <span class="card-title">System Activity Logs</span>
            <button class="btn btn-outline btn-sm" onclick="loadActivityLogs()">🔄 Refresh</button>
          </div>
          <div class="table-wrapper">
            <table>
              <thead><tr><th>#</th><th>User</th><th>Action</th><th>Details</th><th>IP Address</th><th>Time</th></tr></thead>
              <tbody id="activity-tbody"><tr><td colspan="6" class="table-empty"><div class="loader"></div></td></tr></tbody>
            </table>
          </div>
          <div style="padding:12px 20px"><div class="pagination" id="act-pagination"></div></div>
        </div>
      </div>

      <!-- ═══════ SETTINGS TAB ═══════ -->
      <div class="tab-panel" id="panel-settings">
        <div class="card" style="max-width:600px">
          <div class="card-header"><span class="card-title">⚙️ SMTP Email Settings</span></div>
          <div class="card-body">
            <form id="smtp-form" onsubmit="saveSmtp(event)">
              <div class="form-group">
                <label>Enable SMTP</label>
                <select id="smtp-enabled" class="form-control">
                  <option value="no">No (use WordPress default)</option>
                  <option value="yes">Yes (custom SMTP)</option>
                </select>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>SMTP Host</label>
                  <input id="smtp-host" class="form-control" type="text" placeholder="smtp.gmail.com" />
                </div>
                <div class="form-group">
                  <label>Port</label>
                  <input id="smtp-port" class="form-control" type="number" value="587" />
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Username</label>
                  <input id="smtp-user" class="form-control" type="text" placeholder="your@email.com" />
                </div>
                <div class="form-group">
                  <label>Password</label>
                  <input id="smtp-pass" class="form-control" type="password" placeholder="App password" />
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Encryption</label>
                  <select id="smtp-enc" class="form-control">
                    <option value="tls">TLS</option>
                    <option value="ssl">SSL</option>
                    <option value="none">None</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>From Name</label>
                  <input id="smtp-from-name" class="form-control" type="text" value="HR & Payroll ERP" />
                </div>
              </div>
              <div class="form-group">
                <label>From Email</label>
                <input id="smtp-from-email" class="form-control" type="email" placeholder="noreply@company.com" />
              </div>
              <button type="submit" class="btn btn-primary">💾 Save Settings</button>
            </form>
          </div>
        </div>
      </div>

    </div><!-- /page-content -->
  </div><!-- /main-content -->
</div><!-- /app-shell -->

<!-- ═══════════════════════════════════════════════════════════════════
     MODALS
═══════════════════════════════════════════════════════════════════ -->

<!-- Add Employee Modal -->
<div class="modal-overlay" id="modal-add-employee">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">👥 Add New Employee</span>
      <span class="modal-close" onclick="closeModal('modal-add-employee')">✕</span>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group"><label>Username</label><input id="ae-username" class="form-control" placeholder="jdoe" /></div>
        <div class="form-group"><label>Display Name</label><input id="ae-name" class="form-control" placeholder="John Doe" /></div>
      </div>
      <div class="form-group"><label>Email</label><input id="ae-email" class="form-control" type="email" placeholder="john@company.com" /></div>
      <div class="form-row">
        <div class="form-group"><label>Password</label><input id="ae-password" class="form-control" type="password" placeholder="••••••••" /></div>
        <div class="form-group"><label>Role</label>
          <select id="ae-role" class="form-control">
            <option value="hr_employee">Employee</option>
            <option value="hr_accountant">Accountant</option>
            <option value="hr_manager">Manager</option>
            <option value="hr_super_admin">Super Admin</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Department</label><input id="ae-dept" class="form-control" placeholder="Engineering" /></div>
        <div class="form-group"><label>Designation</label><input id="ae-desg" class="form-control" placeholder="Software Engineer" /></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Date of Joining</label><input id="ae-doj" class="form-control" type="date" /></div>
        <div class="form-group"><label>PF Number</label><input id="ae-pf" class="form-control" placeholder="MH/BAN/0012345" /></div>
      </div>
      <div class="form-group"><label>ESI Number</label><input id="ae-esi" class="form-control" placeholder="31001234560011001" /></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('modal-add-employee')">Cancel</button>
      <button class="btn btn-primary" onclick="doAddEmployee()">Add Employee</button>
    </div>
  </div>
</div>

<!-- Edit Employee Modal -->
<div class="modal-overlay" id="modal-edit-employee">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">✏️ Edit Employee</span>
      <span class="modal-close" onclick="closeModal('modal-edit-employee')">✕</span>
    </div>
    <div class="modal-body">
      <input type="hidden" id="edit-emp-id" />
      <div class="form-row">
        <div class="form-group"><label>Department</label><input id="edit-dept" class="form-control" /></div>
        <div class="form-group"><label>Designation</label><input id="edit-desg" class="form-control" /></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Date of Joining</label><input id="edit-doj" class="form-control" type="date" /></div>
        <div class="form-group"><label>Status</label>
          <select id="edit-status" class="form-control"><option value="ACTIVE">Active</option><option value="SUSPENDED">Suspended</option></select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>PF Number</label><input id="edit-pf" class="form-control" /></div>
        <div class="form-group"><label>ESI Number</label><input id="edit-esi" class="form-control" /></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('modal-edit-employee')">Cancel</button>
      <button class="btn btn-primary" onclick="doEditEmployee()">Save Changes</button>
    </div>
  </div>
</div>

<!-- Apply Leave Modal -->
<div class="modal-overlay" id="modal-apply-leave">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">📅 Apply for Leave</span>
      <span class="modal-close" onclick="closeModal('modal-apply-leave')">✕</span>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group"><label>Leave Type</label>
          <select id="leave-type" class="form-control">
            <option>Casual</option><option>Medical</option><option>Earned</option><option>Unpaid</option>
          </select>
        </div>
        <div class="form-group"><label>From Date</label><input id="leave-from" class="form-control" type="date" /></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>To Date</label><input id="leave-to" class="form-control" type="date" /></div>
        <div class="form-group"><label>Days (auto)</label><input id="leave-days" class="form-control" type="number" readonly style="background:var(--bg-tertiary)" /></div>
      </div>
      <div class="form-group"><label>Reason</label><textarea id="leave-reason" class="form-control" rows="3" placeholder="Reason for leave…"></textarea></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('modal-apply-leave')">Cancel</button>
      <button class="btn btn-primary" onclick="doApplyLeave()">Submit Request</button>
    </div>
  </div>
</div>

<!-- Leave Approve/Reject Action Modal -->
<div class="modal-overlay" id="modal-leave-action">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title" id="leave-action-title">Leave Action</span>
      <span class="modal-close" onclick="closeModal('modal-leave-action')">✕</span>
    </div>
    <div class="modal-body">
      <input type="hidden" id="la-id" />
      <input type="hidden" id="la-action" />
      <div class="form-group"><label>Comments</label><textarea id="la-comments" class="form-control" rows="3" placeholder="Optional comments…"></textarea></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('modal-leave-action')">Cancel</button>
      <button class="btn btn-primary" id="la-confirm-btn" onclick="confirmLeaveAction()">Confirm</button>
    </div>
  </div>
</div>

<!-- Upsert Salary Modal -->
<div class="modal-overlay" id="modal-upsert-salary">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">💰 Setup / Update Salary</span>
      <span class="modal-close" onclick="closeModal('modal-upsert-salary')">✕</span>
    </div>
    <div class="modal-body">
      <div class="form-group"><label>Employee ID</label><input id="sal-emp-id" class="form-control" type="number" placeholder="Enter employee ID" /></div>
      <div class="form-row">
        <div class="form-group"><label>Base Salary (₹)</label><input id="sal-base" class="form-control" type="number" step="0.01" oninput="calcSalary()" /></div>
        <div class="form-group"><label>Allowances (₹)</label><input id="sal-allowances" class="form-control" type="number" step="0.01" value="0" oninput="calcSalary()" /></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Other Deductions (₹)</label><input id="sal-deductions" class="form-control" type="number" step="0.01" value="0" oninput="calcSalary()" /></div>
        <div class="form-group"><label>PF (12% auto)</label><input id="sal-pf" class="form-control" type="number" step="0.01" placeholder="Auto-calculated" /></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>ESI (0.75% auto)</label><input id="sal-esi" class="form-control" type="number" step="0.01" placeholder="Auto-calculated" /></div>
        <div class="form-group"><label>Net Salary (₹)</label><input id="sal-net" class="form-control" readonly style="background:var(--bg-tertiary);font-weight:700;color:var(--green)" /></div>
      </div>
      <p class="form-hint">💡 Leave PF/ESI blank for auto-calculation: PF = 12% of base, ESI = 0.75% of gross.</p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('modal-upsert-salary')">Cancel</button>
      <button class="btn btn-primary" onclick="doUpsertSalary()">Save Salary</button>
    </div>
  </div>
</div>

<!-- Generate Payslip Modal -->
<div class="modal-overlay" id="modal-generate-payslip">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">📄 Generate Payslip</span>
      <span class="modal-close" onclick="closeModal('modal-generate-payslip')">✕</span>
    </div>
    <div class="modal-body">
      <div class="form-group"><label>Employee ID</label><input id="gp-emp-id" class="form-control" type="number" placeholder="Enter employee ID" /></div>
      <div class="form-row">
        <div class="form-group"><label>Month</label>
          <select id="gp-month" class="form-control">
            <?php foreach ($months as $m) { echo "<option>$m</option>"; } ?>
          </select>
        </div>
        <div class="form-group"><label>Year</label>
          <select id="gp-year" class="form-control">
            <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--) { echo "<option>$y</option>"; } ?>
          </select>
        </div>
      </div>
      <p class="form-hint">Salary breakdown will be pulled from the employee's salary structure. Override PF/ESI below if needed.</p>
      <div class="form-row">
        <div class="form-group"><label>Override PF (₹)</label><input id="gp-pf" class="form-control" type="number" step="0.01" placeholder="Optional override" /></div>
        <div class="form-group"><label>Override ESI (₹)</label><input id="gp-esi" class="form-control" type="number" step="0.01" placeholder="Optional override" /></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('modal-generate-payslip')">Cancel</button>
      <button class="btn btn-success" onclick="doGeneratePayslip()">⚡ Generate</button>
    </div>
  </div>
</div>

<!-- View Payslip Modal -->
<div class="modal-overlay" id="modal-view-payslip">
  <div class="modal" style="max-width:480px">
    <div class="modal-header">
      <span class="modal-title">📄 Payslip Preview</span>
      <span class="modal-close" onclick="closeModal('modal-view-payslip')">✕</span>
    </div>
    <div class="modal-body" id="payslip-preview-content"></div>
    <div class="modal-footer"><button class="btn btn-outline" onclick="closeModal('modal-view-payslip')">Close</button></div>
  </div>
</div>

<!-- Add Document Modal -->
<div class="modal-overlay" id="modal-add-document">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">📁 Add Document</span>
      <span class="modal-close" onclick="closeModal('modal-add-document')">✕</span>
    </div>
    <div class="modal-body">
      <div class="form-group admin-only"><label>Employee ID (Admin: specify, Employee: auto)</label><input id="doc-emp-id" class="form-control" type="number" placeholder="Leave blank to use your own" /></div>
      <div class="form-group"><label>Document Name</label><input id="doc-name" class="form-control" placeholder="Aadhaar Card Copy" required /></div>
      <div class="form-row">
        <div class="form-group"><label>Document Type</label>
          <select id="doc-type" class="form-control">
            <option>ID Proof</option><option>Address Proof</option><option>Educational Certificate</option>
            <option>Experience Letter</option><option>Bank Details</option><option>PAN Card</option><option>Aadhaar Card</option>
          </select>
        </div>
        <div class="form-group"><label>File URL</label><input id="doc-url" class="form-control" type="url" placeholder="https://…" /></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('modal-add-document')">Cancel</button>
      <button class="btn btn-primary" onclick="doAddDocument()">Add Document</button>
    </div>
  </div>
</div>

<!-- Manual Attendance Modal -->
<div class="modal-overlay" id="modal-manual-attendance">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">🕐 Manual Attendance Entry</span>
      <span class="modal-close" onclick="closeModal('modal-manual-attendance')">✕</span>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group"><label>Employee ID</label><input id="ma-emp-id" class="form-control" type="number" /></div>
        <div class="form-group"><label>Date</label><input id="ma-date" class="form-control" type="date" /></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Check In Time</label><input id="ma-checkin" class="form-control" type="time" /></div>
        <div class="form-group"><label>Check Out Time</label><input id="ma-checkout" class="form-control" type="time" /></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Status</label>
          <select id="ma-status" class="form-control"><option>Present</option><option>Late</option><option>Half Day</option><option>Absent</option></select>
        </div>
        <div class="form-group"><label>Notes</label><input id="ma-notes" class="form-control" placeholder="Optional…" /></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('modal-manual-attendance')">Cancel</button>
      <button class="btn btn-primary" onclick="doManualAttendance()">Submit</button>
    </div>
  </div>
</div>

<!-- Toast Container -->
<div id="toast-container"></div>

<!-- ═══════════════════════════════════════════════════════════════════
     JAVASCRIPT
═══════════════════════════════════════════════════════════════════ -->
<script>
const API = '<?php echo esc_js($api_base); ?>';
const LS  = { TOKEN: 'hr_auth_token', USER: 'hr_current_user', TAB: 'hr_active_tab', THEME: 'hr_theme' };

// ── Helpers ──
const $  = id => document.getElementById(id);
const qs = sel => document.querySelector(sel);
const fmt2 = n => String(n).padStart(2,'0');
const fmtMoney = n => '₹' + parseFloat(n||0).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});
const fmtDate = d => d ? new Date(d).toLocaleDateString('en-IN',{day:'2-digit',month:'short',year:'numeric'}) : '—';
const fmtDateTime = d => d ? new Date(d).toLocaleString('en-IN',{day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'}) : '—';
const daysBetween = (a,b) => { if(!a||!b) return 0; const d1=new Date(a),d2=new Date(b); return Math.max(1,Math.ceil((d2-d1)/(86400000))+1); };
const token = () => localStorage.getItem(LS.TOKEN);
const currentUser = () => { try { return JSON.parse(localStorage.getItem(LS.USER)||'{}'); } catch { return {}; } };
const hasCap = cap => { const u = currentUser(); return u.capabilities && u.capabilities.includes(cap); };
const isAdminOrManager = () => hasCap('manage_employees') || hasCap('manage_leaves');

// ── API wrapper ──
async function apiFetch(path, opts={}) {
    const res = await fetch(API + path, {
        headers: { 'Content-Type':'application/json', 'Authorization':'Bearer '+(token()||'') },
        ...opts,
    });
    const json = await res.json();
    if (res.status === 401) { doLogout(); return null; }
    return json;
}

// ── Toast ──
function toast(msg, type='info', dur=3500) {
    const c = $('toast-container');
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.innerHTML = `<span class="toast-icon"></span><span class="toast-msg">${msg}</span>`;
    c.appendChild(el);
    setTimeout(() => el.remove(), dur);
}

// ── Modal ──
function openModal(id) { $(id).classList.add('open'); }
function closeModal(id) { $(id).classList.remove('open'); }
document.addEventListener('keydown', e => { if(e.key==='Escape') document.querySelectorAll('.modal-overlay.open').forEach(m=>m.classList.remove('open')); });

// ── Theme ──
function toggleTheme() {
    const cur = document.documentElement.getAttribute('data-theme');
    const next = cur === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem(LS.THEME, next);
    $('theme-toggle-btn').textContent = next === 'dark' ? '☀️' : '🌙';
}
(function initTheme(){
    const t = localStorage.getItem(LS.THEME)||'light';
    document.documentElement.setAttribute('data-theme',t);
    const btn = $('theme-toggle-btn');
    if(btn) btn.textContent = t==='dark'?'☀️':'🌙';
})();

// ── Auth ──
function switchAuthTab(tab) {
    $('login-form').style.display   = tab==='login'    ? '' : 'none';
    $('register-form').style.display = tab==='register' ? '' : 'none';
    document.querySelectorAll('.auth-tab').forEach(t => t.classList.toggle('active', t.id===`tab-${tab}`));
}
function fillLogin(u) { $('login-username').value=u; $('login-password').value='123456'; }

async function doLogin(e) {
    e.preventDefault();
    const btn = $('login-btn');
    btn.innerHTML = '<span class="loader"></span>';
    btn.disabled = true;
    const err = $('auth-error');
    err.style.display = 'none';
    try {
        const res = await fetch(API+'/auth/login', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({ username:$('login-username').value, password:$('login-password').value })
        });
        const json = await res.json();
        if (!json.success) { err.textContent = json.message||'Login failed'; err.style.display='block'; return; }
        localStorage.setItem(LS.TOKEN, json.data.token);
        localStorage.setItem(LS.USER, JSON.stringify(json.data.user));
        bootApp();
    } catch(ex) {
        err.textContent = 'Network error. Check your WordPress installation.';
        err.style.display = 'block';
    } finally { btn.innerHTML='<span>Sign In</span>'; btn.disabled=false; }
}

async function doRegister(e) {
    e.preventDefault();
    const res = await fetch(API+'/auth/register', {
        method:'POST', headers:{'Content-Type':'application/json'},
        body: JSON.stringify({ username:$('reg-username').value, email:$('reg-email').value, password:$('reg-password').value, display_name:$('reg-name').value, role:$('reg-role').value })
    });
    const json = await res.json();
    if (!json.success) { toast(json.message||'Registration failed','error'); return; }
    toast('Account created! You can now login.','success');
    switchAuthTab('login');
}

function doLogout() {
    apiFetch('/auth/logout', {method:'POST'}).finally(()=>{
        [LS.TOKEN, LS.USER].forEach(k => localStorage.removeItem(k));
        location.reload();
    });
}

// ── Boot App ──
function bootApp() {
    $('auth-screen').style.display  = 'none';
    $('app-shell').className = 'visible';

    const u = currentUser();
    $('user-name-display').textContent = u.display_name || u.username || 'User';
    $('user-role-display').textContent = (u.role||'').replace('hr_','').replace('_',' ');
    $('user-avatar').textContent = (u.display_name||u.username||'U')[0].toUpperCase();

    // Role-based sidebar visibility
    if (!isAdminOrManager()) {
        document.querySelectorAll('.admin-only').forEach(el => el.style.display='none');
    }
    if (!hasCap('manage_payroll')) {
        document.querySelectorAll('.payroll-only').forEach(el => el.style.display='none');
    }

    // Topbar date
    const now = new Date();
    $('topbar-date').textContent = '— ' + now.toLocaleDateString('en-IN',{weekday:'long',day:'numeric',month:'long',year:'numeric'});

    // Restore tab
    const savedTab = localStorage.getItem(LS.TAB) || 'dashboard';
    goTab(savedTab, true);

    // Live clock
    setInterval(()=>{
        const n=new Date();
        const el=$('live-clock');
        if(el) el.textContent=`${fmt2(n.getHours())}:${fmt2(n.getMinutes())}:${fmt2(n.getSeconds())}`;
    }, 1000);

    // Pending leaves badge
    loadPendingLeaveCount();
}

// ── Check session on load ──
(function(){
    if (token()) { bootApp(); }
})();

// ── Tab navigation ──
const TAB_TITLES = {
    dashboard:'Dashboard', employees:'Employees', attendance:'Attendance',
    leaves:'Leave Requests', salaries:'Salary Structures', payslips:'Payslips',
    documents:'Documents', activity:'Activity Logs', settings:'SMTP Settings'
};
const TAB_LOADERS = {
    dashboard: loadDashboard,
    employees: loadEmployees,
    attendance: ()=>{ loadAttendance(); initCheckInCard(); },
    leaves: loadLeaves,
    salaries: loadSalaries,
    payslips: loadPayslips,
    documents: loadDocuments,
    activity: loadActivityLogs,
    settings: ()=>{},
};
function goTab(tab, skipSave=false) {
    document.querySelectorAll('.tab-panel').forEach(p=>p.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('active'));
    const panel = $('panel-'+tab);
    if (!panel) return;
    panel.classList.add('active');
    const navEl = qs(`.nav-item[data-tab="${tab}"]`);
    if (navEl) navEl.classList.add('active');
    $('topbar-title').textContent = TAB_TITLES[tab] || tab;
    if (!skipSave) localStorage.setItem(LS.TAB, tab);
    if (TAB_LOADERS[tab]) TAB_LOADERS[tab]();
    closeSidebar();
}

// ── Mobile Sidebar ──
function openSidebar()  { $('sidebar').classList.add('open'); $('sidebar-overlay').classList.add('open'); }
function closeSidebar() { $('sidebar').classList.remove('open'); $('sidebar-overlay').classList.remove('open'); }

// ════════════════════════════════════════════════════════════════
// DASHBOARD
// ════════════════════════════════════════════════════════════════
async function loadDashboard() {
    $('dashboard-loading').style.display = 'block';
    $('dashboard-content').style.display = 'none';
    const res = await apiFetch('/dashboard/stats');
    if (!res || !res.success) { $('dashboard-loading').innerHTML='<p style="color:var(--red)">Failed to load dashboard.</p>'; return; }
    $('dashboard-loading').style.display = 'none';
    const d = res.data;
    const isAdmin = !!d.summary;
    let html = '';
    if (isAdmin) {
        const s = d.summary;
        html += `<div class="stats-grid">
          <div class="stat-card blue"><div class="stat-icon">👥</div><div class="stat-value">${s.total_employees}</div><div class="stat-label">Total Employees</div></div>
          <div class="stat-card green"><div class="stat-icon">✅</div><div class="stat-value">${s.present_today}</div><div class="stat-label">Present Today</div></div>
          <div class="stat-card amber"><div class="stat-icon">⏰</div><div class="stat-value">${s.late_today}</div><div class="stat-label">Late Today</div></div>
          <div class="stat-card red"><div class="stat-icon">❌</div><div class="stat-value">${s.absent_today}</div><div class="stat-label">Absent Today</div></div>
          <div class="stat-card purple"><div class="stat-icon">📅</div><div class="stat-value">${s.pending_leaves}</div><div class="stat-label">Pending Leaves</div></div>
          <div class="stat-card green"><div class="stat-icon">💰</div><div class="stat-value">${fmtMoney(s.monthly_gross_payout)}</div><div class="stat-label">Paid This Month (${s.current_month})</div></div>
        </div>`;
        html += `<div class="two-col">
          <div class="card">
            <div class="card-header"><span class="card-title">🕐 Today's Check-ins</span></div>
            <div class="table-wrapper"><table><thead><tr><th>Employee</th><th>Dept</th><th>Check In</th><th>Status</th></tr></thead><tbody>
            ${(d.recent_checkins||[]).length===0 ? '<tr><td colspan="4" class="table-empty">No check-ins yet today</td></tr>' :
              (d.recent_checkins||[]).map(r=>`<tr><td>${r.employee_name||'—'}</td><td>${r.department||'—'}</td><td>${r.check_in||'—'}</td><td><span class="badge ${r.status==='Late'?'badge-amber':r.status==='Present'?'badge-green':'badge-gray'}">${r.status}</span></td></tr>`).join('')}
            </tbody></table></div>
          </div>
          <div class="card">
            <div class="card-header"><span class="card-title">📅 Pending Leave Requests</span></div>
            <div class="table-wrapper"><table><thead><tr><th>Employee</th><th>Type</th><th>From</th><th>To</th><th>Action</th></tr></thead><tbody>
            ${(d.pending_leave_requests||[]).length===0 ? '<tr><td colspan="5" class="table-empty">No pending requests</td></tr>' :
              (d.pending_leave_requests||[]).map(r=>`<tr><td>${r.employee_name||'—'}</td><td>${r.leave_type}</td><td>${fmtDate(r.start_date)}</td><td>${fmtDate(r.end_date)}</td>
              <td><button class="btn btn-success btn-sm" onclick="openLeaveAction(${r.id},'approve')">✓</button> <button class="btn btn-danger btn-sm" onclick="openLeaveAction(${r.id},'reject')">✕</button></td></tr>`).join('')}
            </tbody></table></div>
          </div>
        </div>`;
    } else {
        const s = d;
        const emp = s.employee||{};
        html += `<div class="stats-grid">
          <div class="stat-card green"><div class="stat-icon">📆</div><div class="stat-value">${s.days_present_this_month||0}</div><div class="stat-label">Days Present This Month</div></div>
          <div class="stat-card blue"><div class="stat-icon">💰</div><div class="stat-value">${s.salary ? fmtMoney(s.salary.net_salary) : '—'}</div><div class="stat-label">Net Salary</div></div>
          <div class="stat-card amber"><div class="stat-icon">📅</div><div class="stat-value">${s.pending_leaves||0}</div><div class="stat-label">Pending Leaves</div></div>
          <div class="stat-card purple"><div class="stat-icon">🏢</div><div class="stat-value text-sm" style="font-size:16px">${emp.designation||'—'}</div><div class="stat-label">${emp.department||''}</div></div>
        </div>
        <div class="card" style="margin-bottom:20px">
          <div class="card-header"><span class="card-title">📋 Recent Payslips</span></div>
          <div class="table-wrapper"><table><thead><tr><th>Month</th><th>Year</th><th>Base</th><th>Net Pay</th><th>Status</th></tr></thead><tbody>
          ${(s.recent_payslips||[]).length===0 ? '<tr><td colspan="5" class="table-empty">No payslips yet</td></tr>' :
            (s.recent_payslips).map(p=>`<tr><td>${p.month}</td><td>${p.year}</td><td>${fmtMoney(p.base_salary)}</td><td>${fmtMoney(p.net_salary)}</td><td><span class="badge ${p.status==='Paid'?'badge-green':'badge-blue'}">${p.status}</span></td></tr>`).join('')}
          </tbody></table></div>
        </div>`;
    }
    $('dashboard-content').innerHTML = html;
    $('dashboard-content').style.display = 'block';
}

// ════════════════════════════════════════════════════════════════
// EMPLOYEES
// ════════════════════════════════════════════════════════════════
let empPage = 1;
async function loadEmployees(page) {
    if (page) empPage = page;
    const search = $('emp-search').value;
    const dept   = $('emp-dept-filter').value;
    const status = $('emp-status-filter').value;
    const qs = new URLSearchParams({ page:empPage, limit:15, ...(search&&{search}), ...(dept&&{department:dept}), ...(status&&{status}) });
    const res = await apiFetch('/employees?'+qs);
    const tbody = $('employees-tbody');
    if (!res||!res.success) { tbody.innerHTML='<tr><td colspan="8" class="table-empty" style="color:var(--red)">Failed to load employees</td></tr>'; return; }
    const { data, total } = res.data;
    $('emp-count').textContent = `${total} records`;
    tbody.innerHTML = data.length===0 ? '<tr><td colspan="8" class="table-empty">No employees found</td></tr>' :
        data.map(e=>`<tr>
          <td>${e.id}</td>
          <td><strong>${e.name}</strong><br><small style="color:var(--text-muted)">${e.email}</small></td>
          <td>${e.department||'—'}</td>
          <td>${e.designation||'—'}</td>
          <td>${fmtDate(e.date_of_joining)}</td>
          <td style="font-size:12px">${e.pf_number||'—'}</td>
          <td><span class="badge ${e.status==='ACTIVE'?'badge-green':'badge-red'}">${e.status}</span></td>
          <td><button class="btn btn-outline btn-sm" onclick="openEditEmployee(${JSON.stringify(e).replace(/"/g,'&quot;')})">✏️</button>
              <button class="btn btn-danger btn-sm" style="margin-left:4px" onclick="doDeleteEmployee(${e.id},'${e.name}')">🗑</button></td>
        </tr>`).join('');
    renderPagination('emp-pagination', res.data, loadEmployees);
}
function openEditEmployee(e) {
    if(typeof e==='string') e=JSON.parse(e);
    $('edit-emp-id').value=e.id; $('edit-dept').value=e.department||''; $('edit-desg').value=e.designation||'';
    $('edit-doj').value=e.date_of_joining||''; $('edit-status').value=e.status||'ACTIVE';
    $('edit-pf').value=e.pf_number||''; $('edit-esi').value=e.esi_number||'';
    openModal('modal-edit-employee');
}
async function doAddEmployee() {
    const payload = { username:$('ae-username').value, email:$('ae-email').value, password:$('ae-password').value, display_name:$('ae-name').value, role:$('ae-role').value, department:$('ae-dept').value, designation:$('ae-desg').value, date_of_joining:$('ae-doj').value, pf_number:$('ae-pf').value, esi_number:$('ae-esi').value };
    const res = await apiFetch('/auth/register',{method:'POST',body:JSON.stringify(payload)});
    if(!res||!res.success){toast(res?.message||'Failed','error');return;}
    toast('Employee added!','success'); closeModal('modal-add-employee'); loadEmployees();
}
async function doEditEmployee() {
    const id = $('edit-emp-id').value;
    const payload = { department:$('edit-dept').value, designation:$('edit-desg').value, date_of_joining:$('edit-doj').value, status:$('edit-status').value, pf_number:$('edit-pf').value, esi_number:$('edit-esi').value };
    const res = await apiFetch(`/employees/${id}`,{method:'PUT',body:JSON.stringify(payload)});
    if(!res||!res.success){toast(res?.message||'Failed','error');return;}
    toast('Employee updated!','success'); closeModal('modal-edit-employee'); loadEmployees();
}
async function doDeleteEmployee(id, name) {
    if(!confirm(`Delete employee "${name}"? This will soft-delete their profile.`)) return;
    const res = await apiFetch(`/employees/${id}`,{method:'DELETE'});
    if(!res||!res.success){toast(res?.message||'Failed','error');return;}
    toast('Employee deleted.','success'); loadEmployees();
}

// ════════════════════════════════════════════════════════════════
// ATTENDANCE
// ════════════════════════════════════════════════════════════════
let attPage=1;
async function initCheckInCard() {
    const now = new Date();
    const el = $('today-date');
    if(el) el.textContent = now.toLocaleDateString('en-IN',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
    const u = currentUser();
    const res = await apiFetch('/attendance?limit=1&date='+now.toISOString().split('T')[0]);
    if (!res||!res.success) return;
    const rec = (res.data.data||[])[0];
    const statusEl=$('checkin-status-text'), btnIn=$('btn-checkin'), btnOut=$('btn-checkout');
    if (!statusEl) return;
    if (!rec) {
        statusEl.textContent = 'Not checked in yet'; btnIn.style.display=''; btnOut.style.display='none';
    } else if (rec.check_in && !rec.check_out) {
        statusEl.textContent = `Checked in at ${rec.check_in} · ${rec.status}`; btnIn.style.display='none'; btnOut.style.display='';
    } else if (rec.check_in && rec.check_out) {
        statusEl.textContent = `Completed · ${rec.total_hours}h · ${rec.status}`; btnIn.style.display='none'; btnOut.style.display='none';
    }
}
async function doCheckIn() {
    const res = await apiFetch('/attendance/check-in',{method:'POST',body:JSON.stringify({})});
    if(!res||!res.success){toast(res?.message||'Check-in failed','error');return;}
    toast(`Checked in at ${res.data.check_in} · ${res.data.status}`,'success');
    initCheckInCard(); loadAttendance();
}
async function doCheckOut() {
    const res = await apiFetch('/attendance/check-out',{method:'POST',body:JSON.stringify({})});
    if(!res||!res.success){toast(res?.message||'Check-out failed','error');return;}
    toast(`Checked out · Total: ${res.data.total_hours}h · ${res.data.status}`,'success');
    initCheckInCard(); loadAttendance();
}
async function loadAttendance(page) {
    if(page) attPage=page;
    const params = new URLSearchParams({ page:attPage, limit:20 });
    const empId = $('att-emp-id')?.value; if(empId) params.set('employee_id',empId);
    const dt = $('att-date-filter')?.value; if(dt) params.set('date',dt);
    const st = $('att-status-filter')?.value; if(st) params.set('status',st);
    const res = await apiFetch('/attendance?'+params);
    const tbody = $('attendance-tbody');
    if(!res||!res.success){tbody.innerHTML='<tr><td colspan="8" class="table-empty" style="color:var(--red)">Failed</td></tr>';return;}
    const {data} = res.data;
    tbody.innerHTML = data.length===0 ? '<tr><td colspan="8" class="table-empty">No records found</td></tr>' :
        data.map(r=>`<tr>
          <td>${r.id}</td><td>${r.employee_id}</td><td>${fmtDate(r.date)}</td>
          <td>${r.check_in||'—'}</td><td>${r.check_out||'—'}</td>
          <td><strong>${r.total_hours||'0.00'}h</strong></td>
          <td><span class="badge ${r.status==='Present'?'badge-green':r.status==='Late'?'badge-amber':r.status==='Half Day'?'badge-blue':'badge-red'}">${r.status}</span></td>
          <td style="font-size:12px">${r.notes||'—'}</td>
        </tr>`).join('');
    renderPagination('att-pagination', res.data, loadAttendance);
}
async function doManualAttendance() {
    const payload = { employee_id:parseInt($('ma-emp-id').value), date:$('ma-date').value, check_in:$('ma-checkin').value, check_out:$('ma-checkout').value, status:$('ma-status').value, notes:$('ma-notes').value };
    const res = await apiFetch('/attendance/check-in',{method:'POST',body:JSON.stringify(payload)});
    if(!res||!res.success){toast(res?.message||'Failed','error');return;}
    toast('Attendance recorded.','success'); closeModal('modal-manual-attendance'); loadAttendance();
}

// ════════════════════════════════════════════════════════════════
// LEAVES
// ════════════════════════════════════════════════════════════════
let leavePage=1;
async function loadLeaveBalance() {
    const u = currentUser();
    const empRes = await apiFetch('/employees?limit=1');
    // Try to find current user's employee_id
    const res = await apiFetch('/dashboard/stats');
    if(res&&res.success&&res.data.leave_balances) {
        const lb=res.data.leave_balances;
        $('lb-casual').textContent=lb.casual_leaves??'—';
        $('lb-medical').textContent=lb.medical_leaves??'—';
        $('lb-earned').textContent=lb.earned_leaves??'—';
        $('lb-unpaid').textContent=lb.unpaid_leaves??'0';
    }
}
async function loadLeaves(page) {
    if(page) leavePage=page;
    loadLeaveBalance();
    const params = new URLSearchParams({page:leavePage,limit:15});
    const st=$('leave-status-filter')?.value; if(st) params.set('status',st);
    const lt=$('leave-type-filter')?.value; if(lt) params.set('leave_type',lt);
    const res = await apiFetch('/leaves?'+params);
    const tbody=$('leaves-tbody');
    if(!res||!res.success){tbody.innerHTML='<tr><td colspan="9" class="table-empty" style="color:var(--red)">Failed</td></tr>';return;}
    const {data}=res.data;
    tbody.innerHTML=data.length===0?'<tr><td colspan="9" class="table-empty">No leave requests found</td></tr>':
        data.map(r=>{
            const days=daysBetween(r.start_date,r.end_date);
            const canApprove=isAdminOrManager()&&r.status==='Pending';
            return `<tr>
              <td>${r.id}</td><td>${r.employee_name||r.employee_id}</td>
              <td><span class="badge badge-blue">${r.leave_type}</span></td>
              <td>${fmtDate(r.start_date)}</td><td>${fmtDate(r.end_date)}</td>
              <td>${days}</td><td style="font-size:12px;max-width:120px">${r.reason||'—'}</td>
              <td><span class="badge ${r.status==='Approved'?'badge-green':r.status==='Rejected'?'badge-red':'badge-amber'}">${r.status}</span></td>
              <td>${canApprove?`<button class="btn btn-success btn-sm" onclick="openLeaveAction(${r.id},'approve')">✓ Approve</button> <button class="btn btn-danger btn-sm" onclick="openLeaveAction(${r.id},'reject')">✕</button>`:'—'}</td>
            </tr>`;
        }).join('');
    renderPagination('leave-pagination',res.data,loadLeaves);
}
$('leave-from')?.addEventListener('change',()=>{ const lb=$('leave-from').value,le=$('leave-to').value; if(lb&&le) $('leave-days').value=daysBetween(lb,le); });
$('leave-to')?.addEventListener('change',()=>{ const lb=$('leave-from').value,le=$('leave-to').value; if(lb&&le) $('leave-days').value=daysBetween(lb,le); });
async function doApplyLeave() {
    const payload={leave_type:$('leave-type').value,start_date:$('leave-from').value,end_date:$('leave-to').value,reason:$('leave-reason').value};
    if(!payload.start_date||!payload.end_date){toast('Please select dates','error');return;}
    const res=await apiFetch('/leaves',{method:'POST',body:JSON.stringify(payload)});
    if(!res||!res.success){toast(res?.message||'Failed','error');return;}
    toast('Leave request submitted!','success'); closeModal('modal-apply-leave'); loadLeaves();
}
function openLeaveAction(id,action) {
    $('la-id').value=id; $('la-action').value=action;
    $('leave-action-title').textContent=action==='approve'?'✅ Approve Leave':'❌ Reject Leave';
    const btn=$('la-confirm-btn'); btn.className='btn btn-sm '+(action==='approve'?'btn-success':'btn-danger');
    btn.textContent=action==='approve'?'Approve':'Reject';
    $('la-comments').value=''; openModal('modal-leave-action');
}
async function confirmLeaveAction() {
    const id=$('la-id').value, action=$('la-action').value;
    const res=await apiFetch(`/leaves/${id}/${action}`,{method:'POST',body:JSON.stringify({comments:$('la-comments').value})});
    if(!res||!res.success){toast(res?.message||'Failed','error');return;}
    toast(`Leave ${action}d successfully.`,'success'); closeModal('modal-leave-action'); loadLeaves(); loadDashboard();
}
async function loadPendingLeaveCount() {
    if(!isAdminOrManager()) return;
    const res=await apiFetch('/leaves?status=Pending&limit=1');
    if(!res||!res.success) return;
    const cnt=res.data.total||0;
    const badge=$('pending-leaves-badge');
    badge.textContent=cnt; badge.style.display=cnt>0?'':'none';
}

// ════════════════════════════════════════════════════════════════
// SALARIES
// ════════════════════════════════════════════════════════════════
function calcSalary() {
    const base=parseFloat($('sal-base').value)||0, allow=parseFloat($('sal-allowances').value)||0, ded=parseFloat($('sal-deductions').value)||0;
    const gross=base+allow;
    const pf=parseFloat($('sal-pf').value)||+(base*0.12).toFixed(2);
    const esi=parseFloat($('sal-esi').value)||+(gross*0.0075).toFixed(2);
    const net=gross-(ded+pf+esi);
    $('sal-net').value=net.toFixed(2);
}
async function loadSalaries() {
    const res=await apiFetch('/payroll/salaries?limit=50');
    const tbody=$('salaries-tbody');
    if(!res||!res.success){tbody.innerHTML='<tr><td colspan="11" class="table-empty" style="color:var(--red)">Access denied or failed</td></tr>';return;}
    const {data}=res.data;
    tbody.innerHTML=data.length===0?'<tr><td colspan="11" class="table-empty">No salary structures</td></tr>':
        data.map(s=>`<tr>
          <td>${s.id}</td><td><strong>${s.employee_name||'#'+s.employee_id}</strong></td><td>${s.department||'—'}</td>
          <td>${fmtMoney(s.base_salary)}</td><td>${fmtMoney(s.allowances)}</td>
          <td>${fmtMoney(s.pf_contribution)}</td><td>${fmtMoney(s.esi_contribution)}</td>
          <td>${fmtMoney(s.deductions)}</td><td><strong style="color:var(--green)">${fmtMoney(s.net_salary)}</strong></td>
          <td><span class="badge ${s.status==='Active'?'badge-green':'badge-red'}">${s.status}</span></td>
          <td><button class="btn btn-outline btn-sm" onclick="prefillSalary(${s.employee_id},${s.base_salary},${s.allowances},${s.deductions},${s.pf_contribution},${s.esi_contribution})">✏️</button></td>
        </tr>`).join('');
}
function prefillSalary(eid,base,allow,ded,pf,esi){$('sal-emp-id').value=eid;$('sal-base').value=base;$('sal-allowances').value=allow;$('sal-deductions').value=ded;$('sal-pf').value=pf;$('sal-esi').value=esi;calcSalary();openModal('modal-upsert-salary');}
async function doUpsertSalary() {
    const payload={employee_id:parseInt($('sal-emp-id').value),base_salary:parseFloat($('sal-base').value),allowances:parseFloat($('sal-allowances').value)||0,deductions:parseFloat($('sal-deductions').value)||0};
    if($('sal-pf').value) payload.pf_contribution=parseFloat($('sal-pf').value);
    if($('sal-esi').value) payload.esi_contribution=parseFloat($('sal-esi').value);
    if(!payload.employee_id){toast('Employee ID required','error');return;}
    const res=await apiFetch('/payroll/salaries',{method:'POST',body:JSON.stringify(payload)});
    if(!res||!res.success){toast(res?.message||'Failed','error');return;}
    toast('Salary saved!','success'); closeModal('modal-upsert-salary'); loadSalaries();
}

// ════════════════════════════════════════════════════════════════
// PAYSLIPS
// ════════════════════════════════════════════════════════════════
let psPage=1;
async function loadPayslips(page){
    if(page) psPage=page;
    const params=new URLSearchParams({page:psPage,limit:20});
    const m=$('ps-month-filter')?.value; if(m) params.set('month',m);
    const y=$('ps-year-filter')?.value; if(y) params.set('year',y);
    const st=$('ps-status-filter')?.value; if(st) params.set('status',st);
    const res=await apiFetch('/payroll/payslips?'+params);
    const tbody=$('payslips-tbody');
    if(!res||!res.success){tbody.innerHTML='<tr><td colspan="10" class="table-empty" style="color:var(--red)">Failed</td></tr>';return;}
    const {data}=res.data;
    tbody.innerHTML=data.length===0?'<tr><td colspan="10" class="table-empty">No payslips found</td></tr>':
        data.map(p=>`<tr>
          <td>${p.id}</td><td>${p.employee_name||'#'+p.employee_id}</td>
          <td>${p.month}</td><td>${p.year}</td>
          <td>${fmtMoney(p.base_salary)}</td><td><strong style="color:var(--green)">${fmtMoney(p.net_salary)}</strong></td>
          <td>${fmtMoney(p.pf_deduction)}</td><td>${fmtMoney(p.esi_deduction)}</td>
          <td><span class="badge ${p.status==='Paid'?'badge-green':'badge-blue'}">${p.status}</span></td>
          <td style="display:flex;gap:4px">
            <button class="btn btn-outline btn-sm" onclick="viewPayslip(${p.id})">👁</button>
            ${p.status!=='Paid'&&hasCap('manage_payroll')?`<button class="btn btn-success btn-sm" onclick="markPaid(${p.id})">Mark Paid</button>`:''}
            ${hasCap('manage_payroll')&&p.status!=='Paid'?`<button class="btn btn-danger btn-sm" onclick="deletePayslip(${p.id})">🗑</button>`:''}
          </td>
        </tr>`).join('');
    renderPagination('ps-pagination',res.data,loadPayslips);
}
async function viewPayslip(id){
    const res=await apiFetch(`/payroll/payslips/${id}`);
    if(!res||!res.success){toast('Failed to load payslip','error');return;}
    const p=res.data;
    $('payslip-preview-content').innerHTML=`
      <div class="payslip-preview">
        <div style="text-align:center;margin-bottom:16px"><strong style="font-size:16px">🏢 HR & Payroll ERP</strong><br><small style="color:var(--text-muted)">Official Payslip</small></div>
        <div class="ph"><span>Employee</span><span>${p.employee_name||'—'}</span></div>
        <div class="ph"><span>Department</span><span>${p.department||'—'}</span></div>
        <div class="ph"><span>Designation</span><span>${p.designation||'—'}</span></div>
        <div class="ph"><span>PF Number</span><span>${p.pf_number||'—'}</span></div>
        <div class="ph"><span>ESI Number</span><span>${p.esi_number||'—'}</span></div>
        <div class="ph"><span>Pay Period</span><span>${p.month} ${p.year}</span></div>
        <div class="ph"><span>Basic Salary</span><span>${fmtMoney(p.base_salary)}</span></div>
        <div class="ph"><span>Allowances</span><span>${fmtMoney(p.allowances)}</span></div>
        <div class="ph"><span>Gross Pay</span><span>${fmtMoney((+p.base_salary)+(+p.allowances))}</span></div>
        <div class="ph"><span>PF Deduction</span><span style="color:var(--red)">− ${fmtMoney(p.pf_deduction)}</span></div>
        <div class="ph"><span>ESI Deduction</span><span style="color:var(--red)">− ${fmtMoney(p.esi_deduction)}</span></div>
        <div class="ph"><span>Other Deductions</span><span style="color:var(--red)">− ${fmtMoney(p.deductions)}</span></div>
        <div class="ph"><span style="color:var(--green)">Net Pay</span><span style="color:var(--green)">${fmtMoney(p.net_salary)}</span></div>
        <div style="text-align:center;margin-top:12px"><span class="badge ${p.status==='Paid'?'badge-green':'badge-blue'}">${p.status}</span></div>
      </div>`;
    openModal('modal-view-payslip');
}
async function markPaid(id){
    if(!confirm('Mark this payslip as Paid?')) return;
    const res=await apiFetch(`/payroll/payslips/${id}/mark-paid`,{method:'PUT'});
    if(!res||!res.success){toast(res?.message||'Failed','error');return;}
    toast('Payslip marked as Paid!','success'); loadPayslips();
}
async function deletePayslip(id){
    if(!confirm('Delete this payslip?')) return;
    const res=await apiFetch(`/payroll/payslips/${id}`,{method:'DELETE'});
    if(!res||!res.success){toast(res?.message||'Failed','error');return;}
    toast('Payslip deleted.','success'); loadPayslips();
}
async function doGeneratePayslip(){
    const payload={employee_id:parseInt($('gp-emp-id').value),month:$('gp-month').value,year:parseInt($('gp-year').value)};
    if($('gp-pf').value) payload.pf_deduction=parseFloat($('gp-pf').value);
    if($('gp-esi').value) payload.esi_deduction=parseFloat($('gp-esi').value);
    if(!payload.employee_id){toast('Employee ID required','error');return;}
    const res=await apiFetch('/payroll/payslips/generate',{method:'POST',body:JSON.stringify(payload)});
    if(!res||!res.success){toast(res?.message||'Failed','error');return;}
    toast('Payslip generated!','success'); closeModal('modal-generate-payslip'); loadPayslips();
}

// ════════════════════════════════════════════════════════════════
// DOCUMENTS
// ════════════════════════════════════════════════════════════════
let docPage=1;
async function loadDocuments(page){
    if(page) docPage=page;
    const params=new URLSearchParams({page:docPage,limit:20});
    const s=$('doc-search')?.value; if(s) params.set('search',s);
    const t=$('doc-type-filter')?.value; if(t) params.set('document_type',t);
    const res=await apiFetch('/documents?'+params);
    const tbody=$('documents-tbody');
    if(!res||!res.success){tbody.innerHTML='<tr><td colspan="8" class="table-empty" style="color:var(--red)">Failed</td></tr>';return;}
    const {data}=res.data;
    tbody.innerHTML=data.length===0?'<tr><td colspan="8" class="table-empty">No documents found</td></tr>':
        data.map(d=>`<tr>
          <td>${d.id}</td><td>${d.employee_name||'#'+d.employee_id}</td>
          <td><strong>${d.document_name}</strong></td><td>${d.document_type}</td>
          <td>${d.file_url?`<a href="${d.file_url}" target="_blank" class="btn btn-outline btn-sm">View 🔗</a>`:'—'}</td>
          <td><span class="badge ${d.status==='Verified'?'badge-green':d.status==='Pending Verification'?'badge-amber':d.status==='Active'?'badge-blue':'badge-gray'}">${d.status}</span></td>
          <td>${fmtDateTime(d.uploaded_at)}</td>
          <td style="display:flex;gap:4px">
            ${isAdminOrManager()?`<button class="btn btn-success btn-sm" onclick="verifyDoc(${d.id})">✓</button>`:''}
            <button class="btn btn-danger btn-sm" onclick="deleteDoc(${d.id})">🗑</button>
          </td>
        </tr>`).join('');
    renderPagination('doc-pagination',res.data,loadDocuments);
}
async function doAddDocument(){
    const payload={document_name:$('doc-name').value,document_type:$('doc-type').value,file_url:$('doc-url').value};
    const eid=$('doc-emp-id').value; if(eid) payload.employee_id=parseInt(eid);
    if(!payload.document_name){toast('Document name required','error');return;}
    const res=await apiFetch('/documents',{method:'POST',body:JSON.stringify(payload)});
    if(!res||!res.success){toast(res?.message||'Failed','error');return;}
    toast('Document added!','success'); closeModal('modal-add-document'); loadDocuments();
}
async function verifyDoc(id){
    const res=await apiFetch(`/documents/${id}`,{method:'PUT',body:JSON.stringify({status:'Verified'})});
    if(!res||!res.success){toast('Failed','error');return;}
    toast('Document verified!','success'); loadDocuments();
}
async function deleteDoc(id){
    if(!confirm('Delete this document record?')) return;
    const res=await apiFetch(`/documents/${id}`,{method:'DELETE'});
    if(!res||!res.success){toast('Failed','error');return;}
    toast('Document deleted.','success'); loadDocuments();
}

// ════════════════════════════════════════════════════════════════
// ACTIVITY LOGS
// ════════════════════════════════════════════════════════════════
let actPage=1;
async function loadActivityLogs(page){
    if(page) actPage=page;
    const res=await apiFetch(`/dashboard/activity-logs?page=${actPage}&limit=30`);
    const tbody=$('activity-tbody');
    if(!res||!res.success){tbody.innerHTML='<tr><td colspan="6" class="table-empty" style="color:var(--red)">Access denied</td></tr>';return;}
    const {data}=res.data;
    tbody.innerHTML=data.length===0?'<tr><td colspan="6" class="table-empty">No logs found</td></tr>':
        data.map(l=>`<tr>
          <td>${l.id}</td><td>${l.username||'System'}</td>
          <td><span class="badge badge-blue">${l.action}</span></td>
          <td style="font-size:12px;max-width:200px">${l.details||'—'}</td>
          <td>${l.ip_address||'—'}</td><td>${fmtDateTime(l.created_at)}</td>
        </tr>`).join('');
    renderPagination('act-pagination',res.data,loadActivityLogs);
}

// ════════════════════════════════════════════════════════════════
// SMTP SETTINGS
// ════════════════════════════════════════════════════════════════
async function saveSmtp(e){
    e.preventDefault();
    const payload={smtp_enabled:$('smtp-enabled').value,smtp_host:$('smtp-host').value,smtp_port:$('smtp-port').value,smtp_username:$('smtp-user').value,smtp_password:$('smtp-pass').value,smtp_encryption:$('smtp-enc').value,smtp_from_name:$('smtp-from-name').value,smtp_from_email:$('smtp-from-email').value};
    const res=await apiFetch('/auth/smtp-settings',{method:'POST',body:JSON.stringify(payload)});
    if(!res||!res.success){toast(res?.message||'Failed','error');return;}
    toast('SMTP settings saved!','success');
}

// ════════════════════════════════════════════════════════════════
// PAGINATION HELPER
// ════════════════════════════════════════════════════════════════
function renderPagination(containerId, paginatedData, loadFn){
    const el=$(containerId); if(!el) return;
    const {page,pages,total,limit}=paginatedData;
    if(pages<=1){el.innerHTML='';return;}
    let html=`<span class="page-info">Showing ${Math.min((page-1)*limit+1,total)}–${Math.min(page*limit,total)} of ${total}</span>`;
    html+=`<button class="page-btn" ${page===1?'disabled':''} onclick="${loadFn.name}(${page-1})">‹</button>`;
    for(let i=Math.max(1,page-2);i<=Math.min(pages,page+2);i++) {
        html+=`<button class="page-btn ${i===page?'active':''}" onclick="${loadFn.name}(${i})">${i}</button>`;
    }
    html+=`<button class="page-btn" ${page===pages?'disabled':''} onclick="${loadFn.name}(${page+1})">›</button>`;
    el.innerHTML=html;
}
</script>
</body>
</html>
