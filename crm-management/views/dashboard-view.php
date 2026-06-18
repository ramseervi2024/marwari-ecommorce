<?php
if (!defined('ABSPATH')) {
    exit;
}
$site_url = get_site_url();
$api_base = $site_url . '/wp-json/crm/v1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CRM ERP — Dashboard</title>
<meta name="description" content="CRM ERP Management System - Full overview, Leads, Deals, Pipeline, Invoices, Reports and more.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
:root {
  --bg-primary:#0d0f1a;--bg-secondary:#131624;--bg-card:#1a1d2e;--bg-card-hover:#1f2235;
  --bg-input:#0f1117;--border:#2a2d3e;--border-light:#3a3d50;
  --text-primary:#e2e8f0;--text-secondary:#94a3b8;--text-muted:#64748b;
  --accent:#7c3aed;--accent-light:#8b5cf6;--accent-glow:rgba(124,58,237,0.2);
  --green:#10b981;--green-glow:rgba(16,185,129,0.15);
  --red:#ef4444;--orange:#f59e0b;--blue:#3b82f6;--cyan:#06b6d4;--pink:#ec4899;
  --sidebar-w:258px;--topbar-h:64px;--radius:12px;--radius-lg:16px;
  --shadow:0 4px 24px rgba(0,0,0,0.4);--transition:0.2s cubic-bezier(0.4,0,0.2,1);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%;font-family:'Inter',sans-serif;background:var(--bg-primary);color:var(--text-primary);overflow:hidden}
::-webkit-scrollbar{width:5px;height:5px}
::-webkit-scrollbar-track{background:var(--bg-primary)}
::-webkit-scrollbar-thumb{background:var(--border-light);border-radius:3px}
::-webkit-scrollbar-thumb:hover{background:var(--accent-light)}

/* ── LAYOUT ── */
#app{display:flex;height:100vh}
.sidebar{width:var(--sidebar-w);background:var(--bg-secondary);border-right:1px solid var(--border);display:flex;flex-direction:column;flex-shrink:0;overflow:hidden;transition:width var(--transition);position:relative;z-index:100}
.sidebar.collapsed{width:64px}
.main{flex:1;display:flex;flex-direction:column;overflow:hidden}
.topbar{height:var(--topbar-h);background:var(--bg-secondary);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 24px;gap:16px;flex-shrink:0}
.content{flex:1;overflow-y:auto;padding:24px}

/* ── SIDEBAR ── */
.sidebar-brand{padding:18px 16px;display:flex;align-items:center;gap:12px;border-bottom:1px solid var(--border);min-height:var(--topbar-h);cursor:pointer}
.brand-icon{width:36px;height:36px;border-radius:10px;flex-shrink:0;background:linear-gradient(135deg,var(--accent) 0%,#a855f7 100%);display:flex;align-items:center;justify-content:center;font-size:1.1rem;box-shadow:0 0 20px var(--accent-glow)}
.brand-text{font-size:.95rem;font-weight:700;white-space:nowrap;overflow:hidden;background:linear-gradient(135deg,#e2e8f0,#a78bfa);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.brand-sub{font-size:.62rem;color:var(--text-muted);margin-top:1px;white-space:nowrap;overflow:hidden}
.sidebar-nav{flex:1;overflow-y:auto;padding:10px 8px}
.nav-section{margin-bottom:4px}
.nav-section-label{font-size:.62rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;padding:8px 12px 3px;white-space:nowrap;overflow:hidden}
.sidebar.collapsed .nav-section-label{opacity:0;height:0;padding:0}
.nav-item{display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:8px;cursor:pointer;transition:all var(--transition);font-size:.82rem;font-weight:500;color:var(--text-secondary);white-space:nowrap;overflow:hidden;position:relative}
.nav-item:hover{background:var(--bg-card);color:var(--text-primary)}
.nav-item.active{background:var(--accent-glow);color:var(--accent-light);border:1px solid rgba(124,58,237,.3)}
.nav-item .icon{font-size:.95rem;flex-shrink:0;width:20px;text-align:center}
.sidebar.collapsed .label{display:none}
.sidebar.collapsed .nav-section-label{display:none}
.sidebar-footer{padding:10px 8px;border-top:1px solid var(--border)}
.user-card{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;background:var(--bg-card);cursor:pointer;overflow:hidden}
.user-avatar{width:34px;height:34px;border-radius:50%;flex-shrink:0;background:linear-gradient(135deg,var(--accent) 0%,var(--cyan) 100%);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem}
.user-name{font-size:.8rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.user-role{font-size:.66rem;color:var(--text-muted);white-space:nowrap;text-transform:capitalize}
.sidebar.collapsed .user-name,.sidebar.collapsed .user-role{display:none}

/* ── TOPBAR ── */
.topbar-toggle{background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:1.2rem;padding:8px;border-radius:8px}
.topbar-toggle:hover{background:var(--bg-card);color:var(--text-primary)}
.topbar-title{font-size:1.05rem;font-weight:700;flex:1}
.topbar-actions{display:flex;align-items:center;gap:8px}

/* ── BUTTONS ── */
.btn{padding:8px 16px;border-radius:8px;border:none;cursor:pointer;font-family:inherit;font-size:.8rem;font-weight:600;transition:all var(--transition);display:inline-flex;align-items:center;gap:6px}
.btn-primary{background:linear-gradient(135deg,var(--accent),#a855f7);color:#fff}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 8px 24px var(--accent-glow)}
.btn-secondary{background:var(--bg-card);color:var(--text-secondary);border:1px solid var(--border)}
.btn-secondary:hover{background:var(--bg-card-hover);color:var(--text-primary)}
.btn-danger{background:rgba(239,68,68,.15);color:var(--red);border:1px solid rgba(239,68,68,.3)}
.btn-danger:hover{background:rgba(239,68,68,.25)}
.btn-success{background:rgba(16,185,129,.15);color:var(--green);border:1px solid rgba(16,185,129,.3)}
.btn-success:hover{background:rgba(16,185,129,.25)}
.btn-sm{padding:5px 10px;font-size:.73rem}
.btn-icon{padding:8px}

/* ── CARDS ── */
.card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:20px;transition:all var(--transition)}
.card:hover{border-color:var(--border-light)}
.card-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
.card-title{font-size:.92rem;font-weight:700}
.card-subtitle{font-size:.75rem;color:var(--text-muted);margin-top:2px}

/* ── KPI CARDS ── */
.kpi-grid{display:grid;gap:14px;margin-bottom:22px}
.kpi-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:18px;position:relative;overflow:hidden;transition:all var(--transition)}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--kpi-color,var(--accent))}
.kpi-card:hover{transform:translateY(-2px);box-shadow:var(--shadow);border-color:var(--border-light)}
.kpi-icon{width:42px;height:42px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;background:var(--kpi-bg,var(--accent-glow));margin-bottom:10px}
.kpi-value{font-size:1.7rem;font-weight:800;line-height:1}
.kpi-label{font-size:.75rem;color:var(--text-muted);margin-top:4px;font-weight:500}
.kpi-change{font-size:.7rem;color:var(--green);margin-top:5px}

/* ── TABLE ── */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:.8rem}
th{color:var(--text-muted);font-weight:600;text-align:left;padding:10px 12px;border-bottom:1px solid var(--border);font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
td{padding:9px 12px;border-bottom:1px solid var(--border);vertical-align:middle}
tr:hover td{background:var(--bg-card-hover)}
tr:last-child td{border-bottom:none}

/* ── STATUS ── */
.status{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:20px;font-size:.7rem;font-weight:600;white-space:nowrap}
.status::before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor}
.status-new,.status-scheduled{color:var(--cyan);background:rgba(6,182,212,.1)}
.status-active,.status-approved,.status-won,.status-paid,.status-completed,.status-success{color:var(--green);background:rgba(16,185,129,.1)}
.status-contacted,.status-sent,.status-pending,.status-prospecting,.status-call,.status-bank-transfer,.status-upi,.status-online{color:var(--orange);background:rgba(245,158,11,.1)}
.status-lost,.status-rejected,.status-blocked,.status-failed,.status-overdue{color:var(--red);background:rgba(239,68,68,.1)}
.status-negotiation,.status-qualification,.status-proposal,.status-whatsapp,.status-in-progress{color:var(--blue);background:rgba(59,130,246,.1)}
.status-draft,.status-hold,.status-email,.status-cash,.status-cheque{color:var(--text-secondary);background:var(--bg-card-hover)}
.status-partial,.status-interested{color:var(--pink);background:rgba(236,72,153,.1)}
.status-meeting,.status-follow-up,.status-high{color:#a855f7;background:rgba(168,85,247,.1)}
.status-urgent,.status-sms{color:var(--red);background:rgba(239,68,68,.1)}
.status-low{color:var(--green);background:rgba(16,185,129,.1)}
.status-medium{color:var(--orange);background:rgba(245,158,11,.1)}

/* ── FORMS ── */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.form-group{display:flex;flex-direction:column;gap:5px}
.form-group.full{grid-column:1/-1}
label{font-size:.76rem;font-weight:600;color:var(--text-secondary)}
input,select,textarea{background:var(--bg-input);border:1px solid var(--border);border-radius:8px;color:var(--text-primary);font-family:inherit;font-size:.83rem;padding:8px 12px;transition:border-color var(--transition);outline:none;width:100%}
input:focus,select:focus,textarea:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow)}
select option{background:var(--bg-card)}
textarea{resize:vertical;min-height:78px}
.form-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:18px;padding-top:14px;border-top:1px solid var(--border)}

/* ── MODAL ── */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.7);backdrop-filter:blur(4px);z-index:1000;display:flex;align-items:center;justify-content:center;padding:20px;opacity:0;pointer-events:none;transition:opacity var(--transition)}
.modal-overlay.show{opacity:1;pointer-events:all}
.modal{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);width:100%;max-width:640px;max-height:90vh;overflow-y:auto;transform:translateY(20px) scale(.98);transition:all var(--transition)}
.modal-overlay.show .modal{transform:translateY(0) scale(1)}
.modal-header{padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.modal-title{font-size:.95rem;font-weight:700}
.modal-close{background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:1.2rem;padding:4px 8px;border-radius:6px}
.modal-close:hover{background:var(--bg-card-hover);color:var(--text-primary)}
.modal-body{padding:24px}

/* ── SEARCH ── */
.search-bar{position:relative}
.search-bar input{padding-left:34px}
.search-bar::before{content:'🔍';position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:.75rem;pointer-events:none}

/* ── PAGINATION ── */
.pagination{display:flex;align-items:center;gap:5px;margin-top:14px}
.page-btn{padding:5px 10px;border-radius:6px;border:1px solid var(--border);background:var(--bg-card);color:var(--text-secondary);cursor:pointer;font-size:.75rem;font-family:inherit;transition:all var(--transition)}
.page-btn:hover{border-color:var(--accent);color:var(--text-primary)}
.page-btn.active{background:var(--accent);border-color:var(--accent);color:#fff}
.page-btn:disabled{opacity:.4;cursor:not-allowed}

/* ── KANBAN ── */
.kanban-board{display:flex;gap:14px;overflow-x:auto;padding-bottom:8px;min-height:400px}
.kanban-col{flex:0 0 250px;background:var(--bg-secondary);border:1px solid var(--border);border-radius:var(--radius);padding:12px}
.kanban-col-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
.kanban-col-title{font-size:.8rem;font-weight:700}
.kanban-count{background:var(--bg-card);padding:2px 7px;border-radius:10px;font-size:.7rem;font-weight:700}
.kanban-col-value{font-size:.7rem;color:var(--text-muted);margin-top:1px}
.kanban-items{display:flex;flex-direction:column;gap:7px;min-height:60px}
.kanban-card{background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:11px;cursor:grab;transition:all var(--transition)}
.kanban-card:hover{border-color:var(--accent);box-shadow:0 4px 16px var(--accent-glow)}
.kanban-card.dragging{opacity:.4;cursor:grabbing}
.kanban-card-title{font-size:.8rem;font-weight:600;margin-bottom:3px}
.kanban-card-company{font-size:.7rem;color:var(--text-muted)}
.kanban-card-value{font-size:.82rem;font-weight:700;color:var(--green);margin-top:7px}
.kanban-card-prob{font-size:.66rem;color:var(--text-muted)}
.kanban-col.drag-over{background:var(--accent-glow);border-color:var(--accent)}

/* ── ALERTS ── */
.alert{padding:11px 14px;border-radius:8px;font-size:.8rem;margin-bottom:14px}
.alert-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#fca5a5}
.alert-success{background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#6ee7b7}

/* ── SPINNER ── */
.spinner{display:inline-block;width:20px;height:20px;border:2px solid var(--border);border-top-color:var(--accent);border-radius:50%;animation:spin .7s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.loading-state{display:flex;align-items:center;justify-content:center;min-height:200px;flex-direction:column;gap:12px;color:var(--text-muted)}

/* ── SECTION HEADER ── */
.section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:12px}
.section-title{font-size:1.15rem;font-weight:800}
.section-filters{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
select.filter{padding:6px 10px;font-size:.76rem;min-width:110px}

/* ── LOGIN ── */
.login-page{
  position:fixed;inset:0;z-index:9999;
  display:flex;align-items:center;justify-content:center;
  background:var(--bg-primary);padding:20px;overflow-y:auto;
}
.login-page .login-bg{
  position:fixed;inset:0;pointer-events:none;overflow:hidden;
}
.login-page .orb{
  position:absolute;border-radius:50%;filter:blur(80px);opacity:.3;
  animation:orb-float 8s ease-in-out infinite;
}
.login-page .orb1{width:400px;height:400px;background:radial-gradient(#7c3aed,#4f46e5);top:-100px;left:-100px;animation-delay:0s}
.login-page .orb2{width:300px;height:300px;background:radial-gradient(#06b6d4,#3b82f6);bottom:-80px;right:-80px;animation-delay:3s}
.login-page .orb3{width:200px;height:200px;background:radial-gradient(#ec4899,#8b5cf6);top:50%;left:50%;transform:translate(-50%,-50%);animation-delay:6s}
@keyframes orb-float{
  0%,100%{transform:translateY(0) scale(1)}
  50%{transform:translateY(-20px) scale(1.05)}
}
.login-card{
  position:relative;z-index:1;
  background:rgba(26,29,46,0.85);
  border:1px solid rgba(124,58,237,.35);
  border-radius:24px;padding:40px;
  width:100%;max-width:440px;
  box-shadow:0 0 0 1px rgba(255,255,255,.05),0 24px 80px rgba(0,0,0,.6),0 0 60px rgba(124,58,237,.12);
  backdrop-filter:blur(20px);
}
.login-logo{text-align:center;margin-bottom:28px}
.login-logo-icon{
  width:64px;height:64px;border-radius:18px;margin:0 auto 12px;
  background:linear-gradient(135deg,#7c3aed,#a855f7);
  display:flex;align-items:center;justify-content:center;font-size:1.8rem;
  box-shadow:0 8px 32px rgba(124,58,237,.4);
}
.login-logo h1{font-size:1.6rem;font-weight:800;background:linear-gradient(135deg,#f8fafc,#c4b5fd);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.login-logo p{color:var(--text-muted);font-size:.78rem;margin-top:4px;letter-spacing:.3px}
.login-divider{display:flex;align-items:center;gap:10px;margin:6px 0}
.login-divider::before,.login-divider::after{content:'';flex:1;height:1px;background:var(--border)}
.login-divider span{font-size:.7rem;color:var(--text-muted);white-space:nowrap}
.login-form{display:flex;flex-direction:column;gap:12px}
.login-input-wrap{position:relative}
.login-input-wrap .input-icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:.85rem;pointer-events:none}
.login-input-wrap input{padding-left:36px;background:rgba(15,17,23,.7);border:1px solid var(--border)}
.login-input-wrap input:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow)}
.login-btn-main{
  width:100%;padding:13px;justify-content:center;font-size:.9rem;
  background:linear-gradient(135deg,#7c3aed,#a855f7);
  box-shadow:0 4px 24px rgba(124,58,237,.4);
  border-radius:10px;
}
.login-btn-main:hover{transform:translateY(-2px);box-shadow:0 8px 32px rgba(124,58,237,.5)}
.demo-creds{
  background:rgba(15,17,23,.6);border:1px solid rgba(255,255,255,.06);
  border-radius:10px;padding:14px;margin-top:16px;
}
.demo-creds h4{font-size:.68rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;text-align:center}
.demo-row{display:grid;grid-template-columns:1fr auto;align-items:center;padding:5px 0;border-bottom:1px solid rgba(255,255,255,.04)}
.demo-row:last-child{border-bottom:none}
.demo-row .role{color:var(--text-muted);font-size:.72rem}
.demo-row .creds{
  color:var(--accent-light);font-weight:600;cursor:pointer;font-size:.72rem;
  background:rgba(124,58,237,.1);border:1px solid rgba(124,58,237,.2);
  padding:2px 8px;border-radius:5px;transition:all var(--transition);
}
.demo-row .creds:hover{background:rgba(124,58,237,.25);color:#fff}

/* ── CHARTS ── */
.charts-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:22px}
.chart-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:18px}
.chart-card canvas{max-height:220px}

/* ── TABS ── */
.tabs{display:flex;gap:3px;background:var(--bg-secondary);padding:4px;border-radius:10px;margin-bottom:18px;width:fit-content;flex-wrap:wrap}
.tab{padding:6px 14px;border-radius:7px;cursor:pointer;font-size:.78rem;font-weight:600;color:var(--text-muted);transition:all var(--transition);border:none;background:none;font-family:inherit}
.tab.active{background:var(--bg-card);color:var(--text-primary);box-shadow:0 2px 8px rgba(0,0,0,.3)}

/* ── OVERVIEW MINI CARDS ── */
.overview-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px}
.overview-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:16px;position:relative;overflow:hidden;transition:all var(--transition);cursor:default}
.overview-card::after{content:attr(data-section);position:absolute;bottom:8px;right:10px;font-size:.65rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px}
.overview-card:hover{border-color:var(--border-light);transform:translateY(-1px)}
.ov-icon{font-size:1.6rem;margin-bottom:8px}
.ov-value{font-size:1.5rem;font-weight:800;line-height:1}
.ov-label{font-size:.72rem;color:var(--text-muted);margin-top:3px}

/* ── ACTIVITY LOG ── */
.activity-feed{display:flex;flex-direction:column;gap:0}
.activity-item{display:flex;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);align-items:flex-start}
.activity-item:last-child{border-bottom:none}
.activity-dot{width:30px;height:30px;border-radius:50%;background:var(--accent-glow);border:1px solid var(--accent);display:flex;align-items:center;justify-content:center;font-size:.75rem;flex-shrink:0}
.activity-body{flex:1;min-width:0}
.activity-action{font-size:.8rem;font-weight:600;color:var(--text-primary)}
.activity-detail{font-size:.73rem;color:var(--text-muted);margin-top:1px;word-break:break-word}
.activity-time{font-size:.68rem;color:var(--text-muted);white-space:nowrap;margin-top:3px}

/* ── EMPTY STATE ── */
.empty-state{text-align:center;padding:50px 20px}
.empty-icon{font-size:2.8rem;margin-bottom:10px}
.empty-title{font-size:.95rem;font-weight:700;margin-bottom:5px}
.empty-text{font-size:.8rem;color:var(--text-muted)}

/* ── RESPONSIVE ── */
@media(max-width:1200px){.overview-grid{grid-template-columns:repeat(2,1fr)}.charts-grid{grid-template-columns:1fr}}
@media(max-width:768px){.sidebar{position:fixed;top:0;left:0;bottom:0;z-index:200;transform:translateX(-100%)}.sidebar.mobile-open{transform:translateX(0)}.form-grid{grid-template-columns:1fr}}
</style>
</head>
<body>
<!-- ───── LOGIN PAGE (fixed overlay, always on top) ───── -->
<div id="login-page" class="login-page" style="display:none">
  <div class="login-bg">
    <div class="orb orb1"></div>
    <div class="orb orb2"></div>
    <div class="orb orb3"></div>
  </div>
  <div class="login-card">
    <div class="login-logo">
      <div class="login-logo-icon">🚀</div>
      <h1>CRM ERP</h1>
      <p>Enterprise Customer Relationship Management</p>
    </div>
    <div id="login-alert"></div>
    <div class="login-form">
      <div class="form-group">
        <label style="font-size:.75rem;font-weight:600;color:var(--text-secondary)">Username or Email</label>
        <div class="login-input-wrap">
          <span class="input-icon">👤</span>
          <input type="text" id="login-username" placeholder="csuperadmin" autocomplete="username">
        </div>
      </div>
      <div class="form-group">
        <label style="font-size:.75rem;font-weight:600;color:var(--text-secondary)">Password</label>
        <div class="login-input-wrap">
          <span class="input-icon">🔒</span>
          <input type="password" id="login-password" placeholder="••••••••" autocomplete="current-password">
        </div>
      </div>
      <button class="btn btn-primary login-btn-main" id="login-btn" onclick="doLogin()">Sign In →</button>
    </div>
    <div class="demo-creds">
      <h4>✨ Quick Login — Demo Accounts</h4>
      <div class="demo-row"><span class="role">🔑 Super Admin</span><span class="creds" onclick="fillCreds('csuperadmin','123456')">csuperadmin / 123456</span></div>
      <div class="demo-row"><span class="role">📊 Sales Manager</span><span class="creds" onclick="fillCreds('cmanager','123456')">cmanager / 123456</span></div>
      <div class="demo-row"><span class="role">💼 Sales Executive</span><span class="creds" onclick="fillCreds('cexecutive','123456')">cexecutive / 123456</span></div>
      <div class="demo-row"><span class="role">📞 Telecaller</span><span class="creds" onclick="fillCreds('ctelecaller','123456')">ctelecaller / 123456</span></div>
      <div class="demo-row"><span class="role">🏢 Customer</span><span class="creds" onclick="fillCreds('ccustomer','123456')">ccustomer / 123456</span></div>
    </div>
  </div>
</div>

<div id="app">

  <!-- ───── SIDEBAR ───── -->
  <aside class="sidebar" id="sidebar" style="display:none">
    <div class="sidebar-brand" onclick="toggleSidebar()">
      <div class="brand-icon">🚀</div>
      <div><div class="brand-text">CRM ERP</div><div class="brand-sub">Management System</div></div>
    </div>
    <nav class="sidebar-nav">

      <div class="nav-section">
        <div class="nav-section-label">Main</div>
        <div class="nav-item active" data-section="overview" onclick="navigate('overview')"><span class="icon">🏠</span><span class="label">Overview</span></div>
        <div class="nav-item" data-section="dashboard" onclick="navigate('dashboard')"><span class="icon">📊</span><span class="label">Dashboard</span></div>
      </div>

      <div class="nav-section">
        <div class="nav-section-label">Sales</div>
        <div class="nav-item" data-section="leads" onclick="navigate('leads')"><span class="icon">👥</span><span class="label">Leads</span></div>
        <div class="nav-item" data-section="followups" onclick="navigate('followups')"><span class="icon">📞</span><span class="label">Follow-Ups</span></div>
        <div class="nav-item" data-section="tasks" onclick="navigate('tasks')"><span class="icon">✅</span><span class="label">Tasks</span></div>
        <div class="nav-item" data-section="pipeline" onclick="navigate('pipeline')"><span class="icon">🎯</span><span class="label">Pipeline</span></div>
      </div>

      <div class="nav-section">
        <div class="nav-section-label">Business</div>
        <div class="nav-item" data-section="quotations" onclick="navigate('quotations')"><span class="icon">📄</span><span class="label">Quotations</span></div>
        <div class="nav-item" data-section="customers" onclick="navigate('customers')"><span class="icon">🏢</span><span class="label">Customers</span></div>
        <div class="nav-item" data-section="deals" onclick="navigate('deals')"><span class="icon">💰</span><span class="label">Deals</span></div>
      </div>

      <div class="nav-section">
        <div class="nav-section-label">Finance</div>
        <div class="nav-item" data-section="invoices" onclick="navigate('invoices')"><span class="icon">🧾</span><span class="label">Invoices</span></div>
        <div class="nav-item" data-section="payments" onclick="navigate('payments')"><span class="icon">💳</span><span class="label">Payments</span></div>
      </div>

      <div class="nav-section">
        <div class="nav-section-label">Communications</div>
        <div class="nav-item" data-section="whatsapp" onclick="navigate('whatsapp')"><span class="icon">💬</span><span class="label">WhatsApp</span></div>
        <div class="nav-item" data-section="meetings" onclick="navigate('meetings')"><span class="icon">🤝</span><span class="label">Meetings</span></div>
      </div>

      <div class="nav-section">
        <div class="nav-section-label">Files & Logs</div>
        <div class="nav-item" data-section="documents" onclick="navigate('documents')"><span class="icon">📁</span><span class="label">Documents</span></div>
        <div class="nav-item" data-section="activity" onclick="navigate('activity')"><span class="icon">🕐</span><span class="label">Activity Log</span></div>
      </div>

      <div class="nav-section">
        <div class="nav-section-label">Analytics</div>
        <div class="nav-item" data-section="reports" onclick="navigate('reports')"><span class="icon">📈</span><span class="label">Reports</span></div>
      </div>

      <div class="nav-section">
        <div class="nav-section-label">System</div>
        <div class="nav-item" data-section="users" onclick="navigate('users')"><span class="icon">👤</span><span class="label">Users</span></div>
        <div class="nav-item" data-section="settings" onclick="navigate('settings')"><span class="icon">⚙️</span><span class="label">Settings</span></div>
        <div class="nav-item" onclick="window.open('<?php echo esc_js($site_url); ?>/crm-management-api-docs/','_blank')"><span class="icon">📘</span><span class="label">API Docs</span></div>
      </div>

    </nav>
    <div class="sidebar-footer">
      <div class="user-card" onclick="navigate('settings')">
        <div class="user-avatar" id="user-avatar-sidebar">?</div>
        <div>
          <div class="user-name" id="user-name-sidebar">Loading…</div>
          <div class="user-role" id="user-role-sidebar">—</div>
        </div>
      </div>
    </div>
  </aside>

  <!-- ───── MAIN AREA ───── -->
  <main class="main" id="main-area" style="display:none">
    <div class="topbar">
      <button class="topbar-toggle btn-icon" onclick="toggleSidebar()">☰</button>
      <div class="topbar-title" id="topbar-title">Overview</div>
      <div class="topbar-actions">
        <span id="topbar-user" style="font-size:.78rem;color:var(--text-muted)"></span>
        <button class="btn btn-secondary btn-sm" onclick="doLogout()">Logout</button>
      </div>
    </div>
    <div class="content" id="content">
      <div class="loading-state"><div class="spinner"></div><span>Loading…</span></div>
    </div>
  </main>
</div><!-- #app -->

<!-- ───── MODAL ───── -->
<div class="modal-overlay" id="modal" onclick="if(event.target===this)closeModal()">
  <div class="modal" id="modal-box">
    <div class="modal-header">
      <div class="modal-title" id="modal-title">Modal</div>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body" id="modal-body"></div>
  </div>
</div>

<script>
const API = '<?php echo esc_js($api_base); ?>';
let authToken = localStorage.getItem('crm_token') || '';
let currentUser = null;
let currentSection = 'overview';
let chartInstances = {};
let state = {
  leads:{page:1,search:'',status:''},
  followups:{page:1,status:''},
  tasks:{page:1,status:''},
  quotations:{page:1,status:''},
  customers:{page:1,search:''},
  deals:{page:1,stage:''},
  invoices:{page:1,status:''},
  payments:{page:1},
  documents:{page:1,search:''},
  activity:{page:1}
};

/* ─── API ─── */
async function api(method, path, body=null) {
  const opts = { method, headers: { 'Content-Type':'application/json', 'Authorization':'Bearer '+authToken } };
  if (body) opts.body = JSON.stringify(body);
  try {
    const res = await fetch(API + path, opts);
    return res.json();
  } catch(e) {
    return { success: false, message: 'Network error: ' + e.message };
  }
}

/* ─── AUTH ─── */
function fillCreds(u,p){ document.getElementById('login-username').value=u; document.getElementById('login-password').value=p; }

async function doLogin() {
  const u = document.getElementById('login-username').value.trim();
  const p = document.getElementById('login-password').value.trim();
  const btn = document.getElementById('login-btn');
  const alertEl = document.getElementById('login-alert');
  if (!u||!p){ showAlert(alertEl,'Username and password are required.'); return; }
  btn.disabled=true; btn.innerHTML='<div class="spinner"></div>&nbsp;Signing in…';
  const res = await api('POST','/auth/login',{username:u,password:p});
  btn.disabled=false; btn.innerHTML='Sign In';
  if (!res||!res.success){ showAlert(alertEl, res?.message||'Login failed'); return; }
  authToken = res.data.token;
  localStorage.setItem('crm_token', authToken);
  currentUser = res.data.user;
  showApp();
}

async function doLogout() {
  await api('POST','/auth/logout').catch(()=>{});
  authToken=''; currentUser=null;
  localStorage.removeItem('crm_token');
  showLogin();
}

async function checkAuth() {
  if (!authToken){ showLogin(); return; }
  const res = await api('GET','/auth/me');
  if (!res||!res.success){ showLogin(); return; }
  currentUser = res.data;
  showApp();
}

function showLogin(){
  document.getElementById('login-page').style.display='flex';
  document.getElementById('sidebar').style.display='none';
  document.getElementById('main-area').style.display='none';
}
function showApp(){
  document.getElementById('login-page').style.display='none';
  document.getElementById('sidebar').style.display='flex';
  document.getElementById('main-area').style.display='flex';
  updateUserUI();
  navigate('overview');
}

function updateUserUI() {
  if (!currentUser) return;
  const initials = (currentUser.name||currentUser.username||'?').charAt(0).toUpperCase();
  const roleLabel = (currentUser.role||'').replace('crm_','').replace(/_/g,' ');
  document.getElementById('user-avatar-sidebar').textContent = initials;
  document.getElementById('user-name-sidebar').textContent = currentUser.name||currentUser.username;
  document.getElementById('user-role-sidebar').textContent = roleLabel;
  document.getElementById('topbar-user').textContent = (currentUser.name||currentUser.username) + ' — ' + roleLabel;
}

/* ─── NAVIGATION ─── */
const sectionTitles = {
  overview:'Overview', dashboard:'Dashboard Analytics', leads:'Leads', followups:'Follow-Ups',
  tasks:'Tasks', pipeline:'Sales Pipeline', quotations:'Quotations', customers:'Customers',
  deals:'Deals', invoices:'Invoices', payments:'Payments', whatsapp:'WhatsApp',
  meetings:'Meetings', documents:'Documents', activity:'Activity Log', reports:'Reports',
  users:'User Management', settings:'Settings'
};

function navigate(section) {
  currentSection = section;
  document.querySelectorAll('.nav-item[data-section]').forEach(el=>el.classList.remove('active'));
  const el = document.querySelector(`[data-section="${section}"]`);
  if (el) el.classList.add('active');
  document.getElementById('topbar-title').textContent = sectionTitles[section]||section;
  document.getElementById('content').innerHTML = '<div class="loading-state"><div class="spinner"></div><span>Loading…</span></div>';
  if (sections[section]) sections[section]();
  else document.getElementById('content').innerHTML = '<div class="empty-state"><div class="empty-icon">🚧</div><div class="empty-title">Coming Soon</div></div>';
}

function toggleSidebar() { document.getElementById('sidebar').classList.toggle('collapsed'); }

/* ─── HELPERS ─── */
function showAlert(el, msg, type='error') {
  if (!el) return;
  el.innerHTML = `<div class="alert alert-${type}">${msg}</div>`;
  setTimeout(()=>{ if(el) el.innerHTML=''; }, 4500);
}
function openModal(title, html) { document.getElementById('modal-title').textContent=title; document.getElementById('modal-body').innerHTML=html; document.getElementById('modal').classList.add('show'); }
function closeModal() { document.getElementById('modal').classList.remove('show'); }

function statusBadge(s) {
  if (!s) return '<span style="color:var(--text-muted)">—</span>';
  const cls = s.toLowerCase().replace(/[\s\/+]/g,'-');
  return `<span class="status status-${cls}">${s}</span>`;
}

function renderPagination(page, pages, onPageFn) {
  if (!pages || pages <= 1) return '';
  let html = '<div class="pagination">';
  html += `<button class="page-btn" ${page<=1?'disabled':''} onclick="${onPageFn}(${page-1})">← Prev</button>`;
  const start=Math.max(1,page-2), end=Math.min(pages,page+2);
  if(start>1) html+=`<button class="page-btn" onclick="${onPageFn}(1)">1</button>${start>2?'<span style="color:var(--text-muted);padding:0 4px">…</span>':''}`;
  for(let i=start;i<=end;i++) html+=`<button class="page-btn ${i===page?'active':''}" onclick="${onPageFn}(${i})">${i}</button>`;
  if(end<pages) html+=`${end<pages-1?'<span style="color:var(--text-muted);padding:0 4px">…</span>':''}<button class="page-btn" onclick="${onPageFn}(${pages})">${pages}</button>`;
  html += `<button class="page-btn" ${page>=pages?'disabled':''} onclick="${onPageFn}(${page+1})">Next →</button></div>`;
  return html;
}

function createChart(id, type, labels, datasets, extraOpts={}) {
  if (chartInstances[id]) { chartInstances[id].destroy(); delete chartInstances[id]; }
  const ctx = document.getElementById(id);
  if (!ctx) return;
  const scaleOpts = (type==='bar'||type==='line') ? { x:{ticks:{color:'#64748b'},grid:{color:'#2a2d3e'}}, y:{ticks:{color:'#64748b'},grid:{color:'#2a2d3e'}} } : {};
  chartInstances[id] = new Chart(ctx, {
    type, data:{labels, datasets},
    options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{labels:{color:'#94a3b8',font:{size:10}}} }, scales:scaleOpts, ...extraOpts }
  });
}

function fmtCurrency(v) { return '₹' + parseFloat(v||0).toLocaleString('en-IN'); }
function fmtNum(v) { return parseInt(v||0).toLocaleString('en-IN'); }

const activityIcons = { LEAD_CREATE:'👥', LEAD_UPDATE:'✏️', LEAD_DELETE:'🗑️', LOGIN:'🔑', LOGOUT:'🚪', QUOTATION_CREATE:'📄', QUOTATION_UPDATE:'✏️', QUOTATION_DELETE:'🗑️', DEAL_CREATE:'💰', DEAL_UPDATE:'✏️', FOLLOWUP_CREATE:'📞', TASK_CREATE:'✅', MEDIA_UPLOAD:'📁', SMTP_CONFIG_UPDATE:'⚙️', USER_STATUS_CHANGE:'👤', USER_DELETE:'🗑️', MAIL_FAILED:'❌', DEFAULT:'🔔' };

/* ══════════════════════════════════════════════
   SECTIONS
══════════════════════════════════════════════ */
const sections = {};

/* ─── OVERVIEW (Home) ─── */
sections.overview = async function() {
  // Fetch summary stats, leads count, customers count, invoices count, deals count in parallel
  const [statsRes, leadsRes, custRes, invRes, dealRes, docsRes] = await Promise.all([
    api('GET', '/dashboard/stats'),
    api('GET', '/leads?limit=1'),
    api('GET', '/customers?limit=1'),
    api('GET', '/invoices?limit=1'),
    api('GET', '/deals?limit=1'),
    api('GET', '/documents?limit=1'),
  ]);

  const s = statsRes?.data?.summary || {};
  const recentFollowups = statsRes?.data?.recent_followups || [];
  const funnel = statsRes?.data?.funnel || {};

  const overviewCards = [
    { icon:'👥', label:'Total Leads',    value: fmtNum(leadsRes?.data?.total),    section:'leads',     color:'var(--cyan)' },
    { icon:'🏢', label:'Customers',      value: fmtNum(custRes?.data?.total),      section:'customers', color:'var(--green)' },
    { icon:'💰', label:'Active Deals',   value: fmtNum(dealRes?.data?.total),      section:'deals',     color:'var(--accent-light)' },
    { icon:'🧾', label:'Invoices',       value: fmtNum(invRes?.data?.total),       section:'invoices',  color:'var(--orange)' },
    { icon:'📄', label:'Quotations',     value: fmtNum(s.quotes_sent||0)+' Sent',  section:'quotations',color:'var(--blue)' },
    { icon:'📁', label:'Documents',      value: fmtNum(docsRes?.data?.total),      section:'documents', color:'var(--pink)' },
    { icon:'🏆', label:'Deals Won',      value: fmtNum(s.deals_won||0),            section:'deals',     color:'var(--green)' },
    { icon:'💳', label:'Monthly Revenue',value: fmtCurrency(s.monthly_revenue||0), section:'payments',  color:'var(--pink)' },
  ];

  const kpiCards = [
    { icon:'📞', label:'Follow-Ups Today', value:fmtNum(s.followups_today||0), color:'var(--orange)', bg:'rgba(245,158,11,.12)' },
    { icon:'🆕', label:'New Leads',        value:fmtNum(s.new_leads||0),        color:'var(--cyan)',   bg:'rgba(6,182,212,.12)' },
    { icon:'📊', label:'Conversion Rate',  value:(s.conversion_rate||0)+'%',    color:'var(--accent-light)', bg:'var(--accent-glow)' },
    { icon:'❌', label:'Deals Lost',       value:fmtNum(s.deals_lost||0),       color:'var(--red)',    bg:'rgba(239,68,68,.12)' },
  ];

  document.getElementById('content').innerHTML = `
    <div style="margin-bottom:6px;color:var(--text-muted);font-size:.82rem;">
      👋 Welcome back, <strong style="color:var(--text-primary)">${currentUser?.name||currentUser?.username||'User'}</strong> — ${new Date().toLocaleDateString('en-IN',{weekday:'long',year:'numeric',month:'long',day:'numeric'})}
    </div>

    <!-- 8 module overview cards -->
    <div class="overview-grid" style="grid-template-columns:repeat(4,1fr);margin-top:16px">
      ${overviewCards.map(c=>`
        <div class="overview-card" onclick="navigate('${c.section}')" style="cursor:pointer;border-left:3px solid ${c.color};">
          <div class="ov-icon">${c.icon}</div>
          <div class="ov-value" style="color:${c.color}">${c.value}</div>
          <div class="ov-label">${c.label}</div>
        </div>
      `).join('')}
    </div>

    <!-- 4 daily KPI cards -->
    <div class="kpi-grid" style="grid-template-columns:repeat(4,1fr)">
      ${kpiCards.map(k=>`
        <div class="kpi-card" style="--kpi-color:${k.color};--kpi-bg:${k.bg}">
          <div class="kpi-icon">${k.icon}</div>
          <div class="kpi-value">${k.value}</div>
          <div class="kpi-label">${k.label}</div>
        </div>
      `).join('')}
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:18px">
      <!-- Today's Follow-Ups -->
      <div class="card">
        <div class="card-header">
          <div><div class="card-title">📞 Today's Follow-Ups</div><div class="card-subtitle">${new Date().toLocaleDateString('en-IN')}</div></div>
          <button class="btn btn-secondary btn-sm" onclick="navigate('followups')">View All</button>
        </div>
        ${recentFollowups.length===0
          ? '<div class="empty-state" style="padding:30px 0"><div class="empty-icon">🎉</div><div class="empty-title">All clear for today!</div></div>'
          : `<div class="table-wrap"><table>
              <thead><tr><th>Lead</th><th>Company</th><th>Type</th><th>Time</th><th>Status</th></tr></thead>
              <tbody>${recentFollowups.map(f=>`
                <tr>
                  <td><strong>${f.first_name||''} ${f.last_name||''}</strong></td>
                  <td>${f.company_name||'—'}</td>
                  <td>${statusBadge(f.communication_type)}</td>
                  <td>${f.followup_time||'—'}</td>
                  <td>${statusBadge(f.status)}</td>
                </tr>`).join('')}
              </tbody>
            </table></div>`}
      </div>

      <!-- Pipeline Funnel mini chart -->
      <div class="card">
        <div class="card-header"><div class="card-title">🎯 Pipeline Funnel</div><button class="btn btn-secondary btn-sm" onclick="navigate('pipeline')">Kanban</button></div>
        <canvas id="ov-funnel-chart" height="200"></canvas>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="card" style="margin-top:18px">
      <div class="card-header"><div class="card-title">⚡ Quick Actions</div></div>
      <div style="display:flex;gap:10px;flex-wrap:wrap">
        <button class="btn btn-primary" onclick="navigate('leads');setTimeout(showLeadForm,300)">+ New Lead</button>
        <button class="btn btn-secondary" onclick="navigate('followups');setTimeout(showFollowupForm,300)">+ Follow-Up</button>
        <button class="btn btn-secondary" onclick="navigate('tasks');setTimeout(showTaskForm,300)">+ Task</button>
        <button class="btn btn-secondary" onclick="navigate('quotations');setTimeout(showQuotationForm,300)">+ Quotation</button>
        <button class="btn btn-secondary" onclick="navigate('meetings');setTimeout(showMeetingForm,300)">+ Meeting</button>
        <button class="btn btn-success" onclick="navigate('payments');setTimeout(showPaymentForm,300)">+ Record Payment</button>
        <button class="btn btn-secondary" onclick="navigate('reports')">📈 Reports</button>
        <button class="btn btn-secondary" onclick="navigate('activity')">🕐 Activity Log</button>
      </div>
    </div>
  `;

  // Funnel chart
  const funnelLabels = Object.keys(funnel);
  const funnelData = Object.values(funnel);
  if (funnelLabels.length) {
    createChart('ov-funnel-chart', 'doughnut', funnelLabels, [{
      data: funnelData,
      backgroundColor: ['#7c3aed','#3b82f6','#f59e0b','#10b981','#06b6d4','#ef4444'],
      hoverOffset: 4
    }]);
  }
};

/* ─── DASHBOARD ANALYTICS ─── */
sections.dashboard = async function() {
  const res = await api('GET', '/dashboard/stats');
  if (!res?.success) { document.getElementById('content').innerHTML='<div class="alert alert-error">Failed to load stats.</div>'; return; }
  const s = res.data.summary || {};
  const funnel = res.data.funnel || {};
  const recent = res.data.recent_followups || [];

  const kpis = [
    { icon:'👥', label:'Total Leads',      value:fmtNum(s.total_leads),     color:'var(--cyan)',         bg:'rgba(6,182,212,.12)' },
    { icon:'✨', label:'New Leads',         value:fmtNum(s.new_leads),        color:'var(--accent-light)', bg:'var(--accent-glow)' },
    { icon:'📞', label:'Follow-Ups Today', value:fmtNum(s.followups_today),  color:'var(--orange)',       bg:'rgba(245,158,11,.12)' },
    { icon:'📄', label:'Quotes Sent',      value:fmtNum(s.quotes_sent),      color:'var(--blue)',         bg:'rgba(59,130,246,.12)' },
    { icon:'🏆', label:'Deals Won',         value:fmtNum(s.deals_won),        color:'var(--green)',        bg:'rgba(16,185,129,.12)' },
    { icon:'❌', label:'Deals Lost',        value:fmtNum(s.deals_lost),       color:'var(--red)',          bg:'rgba(239,68,68,.12)' },
    { icon:'💰', label:'Monthly Revenue',  value:fmtCurrency(s.monthly_revenue), color:'var(--pink)',    bg:'rgba(236,72,153,.12)' },
    { icon:'📊', label:'Conversion Rate',  value:(s.conversion_rate||0)+'%', color:'var(--cyan)',         bg:'rgba(6,182,212,.12)' },
  ];

  document.getElementById('content').innerHTML = `
    <div class="kpi-grid" style="grid-template-columns:repeat(4,1fr)">
      ${kpis.map(k=>`<div class="kpi-card" style="--kpi-color:${k.color};--kpi-bg:${k.bg}"><div class="kpi-icon">${k.icon}</div><div class="kpi-value">${k.value}</div><div class="kpi-label">${k.label}</div></div>`).join('')}
    </div>
    <div class="charts-grid">
      <div class="chart-card"><div class="card-header"><div class="card-title">Sales Funnel by Stage</div></div><canvas id="db-funnel" height="220"></canvas></div>
      <div class="chart-card"><div class="card-header"><div class="card-title">Deal Stage Distribution</div></div><canvas id="db-stages" height="220"></canvas></div>
    </div>
    <div class="card">
      <div class="card-header">
        <div><div class="card-title">📞 Today's Follow-Ups</div><div class="card-subtitle">Scheduled for ${new Date().toLocaleDateString('en-IN')}</div></div>
        <button class="btn btn-primary btn-sm" onclick="navigate('followups')">View All</button>
      </div>
      ${recent.length===0
        ? '<div class="empty-state" style="padding:30px 0"><div class="empty-icon">🎉</div><div class="empty-title">No follow-ups scheduled today</div></div>'
        : `<div class="table-wrap"><table>
            <thead><tr><th>Lead</th><th>Company</th><th>Type</th><th>Time</th><th>Next Follow-Up</th><th>Status</th></tr></thead>
            <tbody>${recent.map(f=>`<tr><td><strong>${f.first_name||''} ${f.last_name||''}</strong></td><td>${f.company_name||'—'}</td><td>${statusBadge(f.communication_type)}</td><td>${f.followup_time||'—'}</td><td>${f.next_followup_date||'—'}</td><td>${statusBadge(f.status)}</td></tr>`).join('')}</tbody>
          </table></div>`}
    </div>`;

  const fkeys = Object.keys(funnel), fvals = Object.values(funnel);
  const palette = ['#7c3aed','#3b82f6','#f59e0b','#10b981','#06b6d4','#ef4444'];
  createChart('db-funnel','bar',fkeys,[{label:'Deals',data:fvals,backgroundColor:palette.map(c=>c+'cc'),borderRadius:6,borderSkipped:false}]);
  createChart('db-stages','doughnut',fkeys,[{data:fvals,backgroundColor:palette,hoverOffset:4}]);
};

/* ─── LEADS ─── */
sections.leads = async function() {
  const s = state.leads;
  const params = `?page=${s.page}&limit=15&order=DESC${s.search?'&search='+encodeURIComponent(s.search):''}${s.status?'&lead_status='+encodeURIComponent(s.status):''}`;
  const res = await api('GET', '/leads'+params);
  const data = res?.data || { data:[], total:0, page:1, pages:1 };

  document.getElementById('content').innerHTML = `
    <div class="section-header">
      <div class="section-title">Leads <span style="color:var(--text-muted);font-weight:400;font-size:.8rem">(${fmtNum(data.total)})</span></div>
      <div class="section-filters">
        <div class="search-bar"><input type="text" id="lead-search" placeholder="Search leads…" value="${s.search}" oninput="state.leads.search=this.value;state.leads.page=1;sections.leads()"></div>
        <select class="filter" onchange="state.leads.status=this.value;state.leads.page=1;sections.leads()">
          <option value="" ${!s.status?'selected':''}>All Status</option>
          ${['New','Contacted','Interested','Follow-Up','Quotation Sent','Negotiation','Won','Lost'].map(st=>`<option value="${st}" ${s.status===st?'selected':''}>${st}</option>`).join('')}
        </select>
        <button class="btn btn-primary btn-sm" onclick="showLeadForm()">+ New Lead</button>
      </div>
    </div>
    <div class="card">
      <div class="table-wrap"><table>
        <thead><tr><th>#</th><th>Name</th><th>Company</th><th>Mobile</th><th>Email</th><th>Source</th><th>Status</th><th>Assigned</th><th>Actions</th></tr></thead>
        <tbody>
          ${data.data.length===0 ? '<tr><td colspan="9" style="text-align:center;padding:40px;color:var(--text-muted)">No leads found</td></tr>' :
          data.data.map(l=>`<tr>
            <td style="color:var(--accent-light);font-weight:600">${l.lead_number}</td>
            <td><strong>${l.first_name} ${l.last_name}</strong><br><span style="color:var(--text-muted);font-size:.7rem">${l.city||''}${l.city&&l.state?', ':''}${l.state||''}</span></td>
            <td>${l.company_name||'—'}</td>
            <td>${l.mobile||'—'}</td>
            <td style="font-size:.75rem;color:var(--text-muted)">${l.email||'—'}</td>
            <td>${statusBadge(l.lead_source)}</td>
            <td>${statusBadge(l.lead_status)}</td>
            <td>${l.assigned_name||'—'}</td>
            <td style="white-space:nowrap">
              <button class="btn btn-secondary btn-sm" onclick='showLeadForm(${JSON.stringify(l).replace(/'/g,"\\'")})'  title="Edit">✏️</button>
              <button class="btn btn-danger btn-sm" onclick="deleteLead(${l.id},'${l.lead_number}')" title="Delete">🗑️</button>
            </td>
          </tr>`).join('')}
        </tbody>
      </table></div>
      ${renderPagination(data.page, data.pages, 'p=>{state.leads.page=p;sections.leads()}')}
    </div>`;
};

function showLeadForm(lead=null) {
  const isEdit = !!lead;
  const sources = ['Website','Facebook','Google Ads','LinkedIn','Referral','WhatsApp','Walk-In','Cold Calling'];
  const statuses = ['New','Contacted','Interested','Follow-Up','Quotation Sent','Negotiation','Won','Lost'];
  openModal(isEdit?'Edit Lead':'New Lead', `
    <div id="lf-alert"></div>
    <div class="form-grid">
      <div class="form-group"><label>First Name *</label><input id="lf-first_name" value="${lead?.first_name||''}" placeholder="John"></div>
      <div class="form-group"><label>Last Name</label><input id="lf-last_name" value="${lead?.last_name||''}" placeholder="Doe"></div>
      <div class="form-group"><label>Company</label><input id="lf-company_name" value="${lead?.company_name||''}" placeholder="Acme Corp"></div>
      <div class="form-group"><label>Mobile *</label><input id="lf-mobile" value="${lead?.mobile||''}" placeholder="+919876543210"></div>
      <div class="form-group"><label>Email *</label><input id="lf-email" type="email" value="${lead?.email||''}" placeholder="john@acme.com"></div>
      <div class="form-group"><label>Website</label><input id="lf-website" value="${lead?.website||''}" placeholder="https://acme.com"></div>
      <div class="form-group"><label>Lead Source</label><select id="lf-lead_source">${sources.map(s=>`<option value="${s}" ${lead?.lead_source===s?'selected':''}>${s}</option>`).join('')}</select></div>
      <div class="form-group"><label>Industry</label><input id="lf-industry" value="${lead?.industry||''}" placeholder="Technology"></div>
      <div class="form-group"><label>City</label><input id="lf-city" value="${lead?.city||''}" placeholder="Mumbai"></div>
      <div class="form-group"><label>State</label><input id="lf-state" value="${lead?.state||''}" placeholder="Maharashtra"></div>
      <div class="form-group"><label>Status</label><select id="lf-lead_status">${statuses.map(s=>`<option value="${s}" ${lead?.lead_status===s?'selected':''}>${s}</option>`).join('')}</select></div>
      <div class="form-group full"><label>Remarks</label><textarea id="lf-remarks" placeholder="Enter remarks…">${lead?.remarks||''}</textarea></div>
    </div>
    <div class="form-actions">
      <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
      <button class="btn btn-primary" onclick="saveLead(${lead?.id||'null'})">${isEdit?'Update Lead':'Create Lead'}</button>
    </div>`);
}
async function saveLead(id) {
  const fields = ['first_name','last_name','company_name','mobile','email','website','lead_source','industry','city','state','lead_status','remarks'];
  const body = {};
  fields.forEach(f=>{ body[f]=document.getElementById('lf-'+f)?.value||''; });
  if (!body.first_name||!body.email||!body.mobile){ showAlert(document.getElementById('lf-alert'),'First name, email and mobile required.'); return; }
  const res = await api(id?'PUT':'POST', id?`/leads/${id}`:'/leads', body);
  if (!res?.success){ showAlert(document.getElementById('lf-alert'), res?.message||'Save failed.'); return; }
  closeModal(); sections.leads();
}
async function deleteLead(id, num) {
  if (!confirm(`Delete lead ${num}?`)) return;
  const res = await api('DELETE',`/leads/${id}`);
  if (res?.success) sections.leads();
}

/* ─── FOLLOW-UPS ─── */
sections.followups = async function() {
  const s = state.followups;
  const res = await api('GET', `/followups?page=${s.page}&limit=15&order=DESC${s.status?'&status='+s.status:''}`);
  const data = res?.data || { data:[], total:0, pages:1 };
  document.getElementById('content').innerHTML = `
    <div class="section-header">
      <div class="section-title">Follow-Ups <span style="color:var(--text-muted);font-size:.8rem">(${fmtNum(data.total)})</span></div>
      <div class="section-filters">
        <select class="filter" onchange="state.followups.status=this.value;state.followups.page=1;sections.followups()">
          <option value="">All Status</option>
          ${['Pending','Completed','Cancelled'].map(st=>`<option value="${st}" ${s.status===st?'selected':''}>${st}</option>`).join('')}
        </select>
        <button class="btn btn-primary btn-sm" onclick="showFollowupForm()">+ New Follow-Up</button>
      </div>
    </div>
    <div class="card">
      <div class="table-wrap"><table>
        <thead><tr><th>Lead</th><th>Company</th><th>Date</th><th>Time</th><th>Type</th><th>Remarks</th><th>Next F/U</th><th>Status</th><th></th></tr></thead>
        <tbody>${data.data.length===0?'<tr><td colspan="9" style="text-align:center;padding:40px;color:var(--text-muted)">No follow-ups found</td></tr>':
        data.data.map(f=>`<tr>
          <td><strong>${f.lead_name||f.lead_id||'—'}</strong></td>
          <td>${f.company_name||'—'}</td>
          <td>${f.followup_date||'—'}</td>
          <td>${f.followup_time||'—'}</td>
          <td>${statusBadge(f.communication_type)}</td>
          <td style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${f.remarks||'—'}</td>
          <td>${f.next_followup_date||'—'}</td>
          <td>${statusBadge(f.status)}</td>
          <td><button class="btn btn-danger btn-sm" onclick="deleteFollowup(${f.id})">🗑️</button></td>
        </tr>`).join('')}</tbody>
      </table></div>
      ${renderPagination(data.page, data.pages, 'p=>{state.followups.page=p;sections.followups()}')}
    </div>`;
};
async function showFollowupForm() {
  const lr = await api('GET','/leads?limit=100&order=DESC');
  const leads = lr?.data?.data||[];
  openModal('New Follow-Up',`
    <div id="fu-alert"></div>
    <div class="form-grid">
      <div class="form-group"><label>Lead *</label><select id="fu-lead_id">${leads.map(l=>`<option value="${l.id}">${l.first_name} ${l.last_name} (${l.lead_number})</option>`).join('')}</select></div>
      <div class="form-group"><label>Communication Type</label><select id="fu-communication_type">${['Call','WhatsApp','Email','Meeting','SMS'].map(t=>`<option>${t}</option>`).join('')}</select></div>
      <div class="form-group"><label>Follow-Up Date *</label><input type="date" id="fu-followup_date" value="${new Date().toISOString().split('T')[0]}"></div>
      <div class="form-group"><label>Time</label><input type="time" id="fu-followup_time" value="12:00"></div>
      <div class="form-group"><label>Next Follow-Up Date</label><input type="date" id="fu-next_followup_date"></div>
      <div class="form-group"><label>Status</label><select id="fu-status"><option>Pending</option><option>Completed</option><option>Cancelled</option></select></div>
      <div class="form-group full"><label>Remarks</label><textarea id="fu-remarks" placeholder="Follow-up notes…"></textarea></div>
    </div>
    <div class="form-actions"><button class="btn btn-secondary" onclick="closeModal()">Cancel</button><button class="btn btn-primary" onclick="saveFollowup()">Save</button></div>`);
}
async function saveFollowup() {
  const body = { lead_id:parseInt(document.getElementById('fu-lead_id').value), followup_date:document.getElementById('fu-followup_date').value, followup_time:document.getElementById('fu-followup_time').value+':00', communication_type:document.getElementById('fu-communication_type').value, remarks:document.getElementById('fu-remarks').value, next_followup_date:document.getElementById('fu-next_followup_date').value||null, status:document.getElementById('fu-status').value };
  const res = await api('POST','/followups',body);
  if (!res?.success){ showAlert(document.getElementById('fu-alert'),res?.message||'Failed'); return; }
  closeModal(); sections.followups();
}
async function deleteFollowup(id) { if(!confirm('Delete follow-up?'))return; await api('DELETE',`/followups/${id}`); sections.followups(); }

/* ─── TASKS ─── */
sections.tasks = async function() {
  const s = state.tasks;
  const res = await api('GET', `/tasks?page=${s.page}&limit=15&order=DESC${s.status?'&status='+s.status:''}`);
  const data = res?.data || { data:[], total:0, pages:1 };
  document.getElementById('content').innerHTML = `
    <div class="section-header">
      <div class="section-title">Tasks <span style="color:var(--text-muted);font-size:.8rem">(${fmtNum(data.total)})</span></div>
      <div class="section-filters">
        <select class="filter" onchange="state.tasks.status=this.value;state.tasks.page=1;sections.tasks()">
          <option value="">All Status</option>
          ${['Pending','In Progress','Completed','Cancelled'].map(st=>`<option value="${st}" ${s.status===st?'selected':''}>${st}</option>`).join('')}
        </select>
        <button class="btn btn-primary btn-sm" onclick="showTaskForm()">+ New Task</button>
      </div>
    </div>
    <div class="card">
      <div class="table-wrap"><table>
        <thead><tr><th>Title</th><th>Due Date</th><th>Priority</th><th>Status</th><th>Assigned</th><th>Actions</th></tr></thead>
        <tbody>${data.data.length===0?'<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)">No tasks</td></tr>':
        data.data.map(t=>`<tr>
          <td><strong>${t.title}</strong>${t.description?`<br><span style="font-size:.7rem;color:var(--text-muted)">${t.description.substring(0,60)}</span>`:''}</td>
          <td>${t.due_date}</td>
          <td>${statusBadge(t.priority)}</td>
          <td>${statusBadge(t.status)}</td>
          <td>${t.assigned_name||'—'}</td>
          <td style="white-space:nowrap">
            <button class="btn btn-success btn-sm" onclick="updateTaskStatus(${t.id},'Completed')" title="Mark Complete">✓</button>
            <button class="btn btn-danger btn-sm" onclick="deleteTask(${t.id})">🗑️</button>
          </td>
        </tr>`).join('')}</tbody>
      </table></div>
      ${renderPagination(data.page, data.pages, 'p=>{state.tasks.page=p;sections.tasks()}')}
    </div>`;
};
async function showTaskForm() {
  openModal('New Task',`
    <div id="tf-alert"></div>
    <div class="form-grid">
      <div class="form-group full"><label>Title *</label><input id="tf-title" placeholder="Task title…"></div>
      <div class="form-group"><label>Due Date *</label><input type="date" id="tf-due_date" value="${new Date().toISOString().split('T')[0]}"></div>
      <div class="form-group"><label>Priority</label><select id="tf-priority"><option>Low</option><option selected>Medium</option><option>High</option><option>Urgent</option></select></div>
      <div class="form-group"><label>Status</label><select id="tf-status"><option>Pending</option><option>In Progress</option><option>Completed</option></select></div>
      <div class="form-group full"><label>Description</label><textarea id="tf-description" placeholder="Task details…"></textarea></div>
    </div>
    <div class="form-actions"><button class="btn btn-secondary" onclick="closeModal()">Cancel</button><button class="btn btn-primary" onclick="saveTask()">Create Task</button></div>`);
}
async function saveTask() {
  const body = { title:document.getElementById('tf-title').value, due_date:document.getElementById('tf-due_date').value, priority:document.getElementById('tf-priority').value, status:document.getElementById('tf-status').value, description:document.getElementById('tf-description').value };
  if (!body.title||!body.due_date){ showAlert(document.getElementById('tf-alert'),'Title and due date required.'); return; }
  const res = await api('POST','/tasks',body);
  if (!res?.success){ showAlert(document.getElementById('tf-alert'),res?.message||'Failed'); return; }
  closeModal(); sections.tasks();
}
async function updateTaskStatus(id,status) { await api('PUT',`/tasks/${id}`,{status}); sections.tasks(); }
async function deleteTask(id) { if(!confirm('Delete task?'))return; await api('DELETE',`/tasks/${id}`); sections.tasks(); }

/* ─── PIPELINE (KANBAN) ─── */
sections.pipeline = async function() {
  const res = await api('GET','/pipeline');
  const cols = res?.data||[];
  const stageColors = { Prospecting:'#94a3b8', Qualification:'var(--cyan)', Proposal:'var(--blue)', Negotiation:'var(--orange)', Won:'var(--green)', Lost:'var(--red)' };
  document.getElementById('content').innerHTML = `
    <div class="section-header">
      <div class="section-title">Sales Pipeline <span style="color:var(--text-muted);font-size:.8rem">(Drag to move stages)</span></div>
      <button class="btn btn-primary btn-sm" onclick="navigate('deals');setTimeout(showDealForm,300)">+ New Deal</button>
    </div>
    <div class="kanban-board">
      ${cols.map(col=>`
        <div class="kanban-col" data-stage="${col.stage}" ondragover="event.preventDefault();this.classList.add('drag-over')" ondragleave="this.classList.remove('drag-over')" ondrop="dropDeal(event,'${col.stage}')">
          <div class="kanban-col-header">
            <div>
              <div class="kanban-col-title" style="color:${stageColors[col.stage]||'inherit'}">${col.stage}</div>
              <div class="kanban-col-value">${fmtCurrency(col.total_value||0)}</div>
            </div>
            <span class="kanban-count">${col.count}</span>
          </div>
          <div class="kanban-items">
            ${col.items.map(d=>`
              <div class="kanban-card" draggable="true" data-id="${d.id}" ondragstart="dragStart(event)" ondragend="dragEnd(event)">
                <div class="kanban-card-title">${d.first_name||''} ${d.last_name||''}</div>
                <div class="kanban-card-company">${d.company_name||'—'}</div>
                <div class="kanban-card-value">${fmtCurrency(d.deal_value)}</div>
                <div class="kanban-card-prob">Probability: ${d.probability}%</div>
              </div>`).join('')}
            ${col.items.length===0?'<div style="color:var(--text-muted);font-size:.75rem;text-align:center;padding:16px 0">Empty</div>':''}
          </div>
        </div>`).join('')}
    </div>`;
};
function dragStart(e){ e.currentTarget.classList.add('dragging'); e.dataTransfer.setData('text/plain',e.currentTarget.dataset.id); }
function dragEnd(e){ e.currentTarget.classList.remove('dragging'); document.querySelectorAll('.kanban-col').forEach(c=>c.classList.remove('drag-over')); }
async function dropDeal(e,newStage) {
  e.preventDefault(); e.currentTarget.classList.remove('drag-over');
  const id = e.dataTransfer.getData('text/plain');
  const probMap = { Prospecting:10, Qualification:30, Proposal:50, Negotiation:70, Won:100, Lost:0 };
  await api('PUT',`/deals/${id}`,{ deal_stage:newStage, probability:probMap[newStage]||50 });
  sections.pipeline();
}

/* ─── QUOTATIONS ─── */
sections.quotations = async function() {
  const s = state.quotations;
  const res = await api('GET',`/quotations?page=${s.page}&limit=15&order=DESC${s.status?'&status='+s.status:''}`);
  const data = res?.data || { data:[], total:0, pages:1 };
  document.getElementById('content').innerHTML = `
    <div class="section-header">
      <div class="section-title">Quotations <span style="color:var(--text-muted);font-size:.8rem">(${fmtNum(data.total)})</span></div>
      <div class="section-filters">
        <select class="filter" onchange="state.quotations.status=this.value;state.quotations.page=1;sections.quotations()">
          <option value="">All Status</option>
          ${['Draft','Sent','Accepted','Rejected','Expired'].map(st=>`<option value="${st}" ${s.status===st?'selected':''}>${st}</option>`).join('')}
        </select>
        <button class="btn btn-primary btn-sm" onclick="showQuotationForm()">+ New Quotation</button>
      </div>
    </div>
    <div class="card">
      <div class="table-wrap"><table>
        <thead><tr><th>#</th><th>Lead</th><th>Company</th><th>Date</th><th>Valid Until</th><th>Grand Total</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>${data.data.length===0?'<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">No quotations</td></tr>':
        data.data.map(q=>`<tr>
          <td style="color:var(--accent-light);font-weight:600">${q.quotation_number}</td>
          <td>${q.lead_name||'—'}</td>
          <td>${q.company_name||'—'}</td>
          <td>${q.quotation_date}</td>
          <td>${q.valid_until}</td>
          <td style="font-weight:700;color:var(--green)">${fmtCurrency(q.grand_total)}</td>
          <td>${statusBadge(q.status)}</td>
          <td style="white-space:nowrap">
            <button class="btn btn-secondary btn-sm" onclick="updateQStatus(${q.id})">Status</button>
            <button class="btn btn-danger btn-sm" onclick="deleteQuotation(${q.id},'${q.quotation_number}')">🗑️</button>
          </td>
        </tr>`).join('')}</tbody>
      </table></div>
      ${renderPagination(data.page, data.pages, 'p=>{state.quotations.page=p;sections.quotations()}')}
    </div>`;
};
async function showQuotationForm() {
  const lr = await api('GET','/leads?limit=100&order=DESC');
  const leads = lr?.data?.data||[];
  openModal('New Quotation',`
    <div id="qf-alert"></div>
    <div class="form-grid">
      <div class="form-group"><label>Lead *</label><select id="qf-lead_id">${leads.map(l=>`<option value="${l.id}">${l.first_name} ${l.last_name} (${l.lead_number})</option>`).join('')}</select></div>
      <div class="form-group"><label>Status</label><select id="qf-status"><option>Draft</option><option>Sent</option></select></div>
      <div class="form-group"><label>Quotation Date *</label><input type="date" id="qf-quotation_date" value="${new Date().toISOString().split('T')[0]}"></div>
      <div class="form-group"><label>Valid Until *</label><input type="date" id="qf-valid_until"></div>
      <div class="form-group"><label>Subtotal (₹)</label><input type="number" id="qf-subtotal" value="0" oninput="calcQTotal()"></div>
      <div class="form-group"><label>Discount (₹)</label><input type="number" id="qf-discount" value="0" oninput="calcQTotal()"></div>
      <div class="form-group"><label>Tax Amount (₹)</label><input type="number" id="qf-tax_amount" value="0" oninput="calcQTotal()"></div>
      <div class="form-group"><label>Grand Total (₹)</label><input type="number" id="qf-grand_total" value="0" readonly style="opacity:.6"></div>
    </div>
    <div class="form-actions"><button class="btn btn-secondary" onclick="closeModal()">Cancel</button><button class="btn btn-primary" onclick="saveQuotation()">Create Quotation</button></div>`);
}
function calcQTotal(){ const s=parseFloat(document.getElementById('qf-subtotal')?.value||0),d=parseFloat(document.getElementById('qf-discount')?.value||0),t=parseFloat(document.getElementById('qf-tax_amount')?.value||0); const el=document.getElementById('qf-grand_total'); if(el) el.value=(s-d+t).toFixed(2); }
async function saveQuotation() {
  const body = { lead_id:parseInt(document.getElementById('qf-lead_id').value), quotation_date:document.getElementById('qf-quotation_date').value, valid_until:document.getElementById('qf-valid_until').value, subtotal:parseFloat(document.getElementById('qf-subtotal').value), discount:parseFloat(document.getElementById('qf-discount').value), tax_amount:parseFloat(document.getElementById('qf-tax_amount').value), status:document.getElementById('qf-status').value };
  if (!body.quotation_date||!body.valid_until){ showAlert(document.getElementById('qf-alert'),'Dates required.'); return; }
  const res = await api('POST','/quotations',body);
  if (!res?.success){ showAlert(document.getElementById('qf-alert'),res?.message||'Failed'); return; }
  closeModal(); sections.quotations();
}
async function updateQStatus(id) {
  const status = prompt('New status:\nDraft, Sent, Accepted, Rejected, Expired');
  if (!status) return;
  await api('PUT',`/quotations/${id}`,{status:status.trim()});
  sections.quotations();
}
async function deleteQuotation(id, num) { if(!confirm(`Delete quotation ${num}?`))return; await api('DELETE',`/quotations/${id}`); sections.quotations(); }

/* ─── CUSTOMERS ─── */
sections.customers = async function() {
  const s = state.customers;
  const res = await api('GET',`/customers?page=${s.page}&limit=15${s.search?'&search='+encodeURIComponent(s.search):''}`);
  const data = res?.data || { data:[], total:0, pages:1 };
  document.getElementById('content').innerHTML = `
    <div class="section-header">
      <div class="section-title">Customers <span style="color:var(--text-muted);font-size:.8rem">(${fmtNum(data.total)})</span></div>
      <div class="section-filters">
        <div class="search-bar"><input type="text" placeholder="Search customers…" value="${s.search}" oninput="state.customers.search=this.value;state.customers.page=1;sections.customers()"></div>
        <button class="btn btn-primary btn-sm" onclick="showCustomerForm()">+ New Customer</button>
      </div>
    </div>
    <div class="card">
      <div class="table-wrap"><table>
        <thead><tr><th>Code</th><th>Company</th><th>Contact</th><th>Mobile</th><th>Email</th><th>City</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>${data.data.length===0?'<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">No customers</td></tr>':
        data.data.map(c=>`<tr>
          <td style="color:var(--accent-light);font-weight:600">${c.customer_code}</td>
          <td><strong>${c.company_name}</strong></td>
          <td>${c.contact_person||'—'}</td>
          <td>${c.mobile||'—'}</td>
          <td style="font-size:.75rem;color:var(--text-muted)">${c.email||'—'}</td>
          <td>${c.city||'—'}</td>
          <td>${statusBadge(c.status)}</td>
          <td><button class="btn btn-danger btn-sm" onclick="deleteCustomer(${c.id},'${c.customer_code}')">🗑️</button></td>
        </tr>`).join('')}</tbody>
      </table></div>
      ${renderPagination(data.page, data.pages, 'p=>{state.customers.page=p;sections.customers()}')}
    </div>`;
};
async function showCustomerForm() {
  openModal('New Customer',`
    <div id="cf-alert"></div>
    <div class="form-grid">
      <div class="form-group"><label>Company Name *</label><input id="cf-company_name" placeholder="Acme Corporation"></div>
      <div class="form-group"><label>Contact Person</label><input id="cf-contact_person" placeholder="John Doe"></div>
      <div class="form-group"><label>Mobile *</label><input id="cf-mobile" placeholder="+919876543210"></div>
      <div class="form-group"><label>Email *</label><input type="email" id="cf-email" placeholder="contact@acme.com"></div>
      <div class="form-group"><label>GST Number</label><input id="cf-gst_number" placeholder="27AAAAA1111A1Z1"></div>
      <div class="form-group"><label>City</label><input id="cf-city" placeholder="Mumbai"></div>
      <div class="form-group"><label>State</label><input id="cf-state" placeholder="Maharashtra"></div>
      <div class="form-group"><label>Status</label><select id="cf-status"><option>Active</option><option>Inactive</option></select></div>
      <div class="form-group full"><label>Address</label><textarea id="cf-address" placeholder="Full address…"></textarea></div>
    </div>
    <div class="form-actions"><button class="btn btn-secondary" onclick="closeModal()">Cancel</button><button class="btn btn-primary" onclick="saveCustomer()">Create Customer</button></div>`);
}
async function saveCustomer() {
  const fields=['company_name','contact_person','mobile','email','gst_number','city','state','status','address'];
  const body={};
  fields.forEach(f=>{body[f]=document.getElementById('cf-'+f)?.value||'';});
  if(!body.company_name||!body.mobile||!body.email){showAlert(document.getElementById('cf-alert'),'Company, mobile, email required.');return;}
  const res=await api('POST','/customers',body);
  if(!res?.success){showAlert(document.getElementById('cf-alert'),res?.message||'Failed');return;}
  closeModal();sections.customers();
}
async function deleteCustomer(id,code){if(!confirm(`Delete customer ${code}?`))return;await api('DELETE',`/customers/${id}`);sections.customers();}

/* ─── DEALS ─── */
sections.deals = async function() {
  const s = state.deals;
  const res = await api('GET',`/deals?page=${s.page}&limit=15&order=DESC${s.stage?'&deal_stage='+s.stage:''}`);
  const data = res?.data || { data:[], total:0, pages:1 };
  document.getElementById('content').innerHTML = `
    <div class="section-header">
      <div class="section-title">Deals <span style="color:var(--text-muted);font-size:.8rem">(${fmtNum(data.total)})</span></div>
      <div class="section-filters">
        <select class="filter" onchange="state.deals.stage=this.value;state.deals.page=1;sections.deals()">
          <option value="">All Stages</option>
          ${['Prospecting','Qualification','Proposal','Negotiation','Won','Lost'].map(st=>`<option value="${st}" ${s.stage===st?'selected':''}>${st}</option>`).join('')}
        </select>
        <button class="btn btn-primary btn-sm" onclick="showDealForm()">+ New Deal</button>
      </div>
    </div>
    <div class="card">
      <div class="table-wrap"><table>
        <thead><tr><th>#</th><th>Lead</th><th>Value</th><th>Stage</th><th>Prob.</th><th>Expected Close</th><th>Assigned</th><th>Actions</th></tr></thead>
        <tbody>${data.data.length===0?'<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">No deals</td></tr>':
        data.data.map(d=>`<tr>
          <td style="color:var(--accent-light);font-weight:600">${d.deal_number}</td>
          <td><strong>${d.lead_name||'Unknown'}</strong><br><span style="color:var(--text-muted);font-size:.7rem">${d.company_name||''}</span></td>
          <td style="font-weight:700;color:var(--green)">${fmtCurrency(d.deal_value)}</td>
          <td>${statusBadge(d.deal_stage)}</td>
          <td>${d.probability}%</td>
          <td>${d.expected_close_date||'—'}</td>
          <td>${d.assigned_name||'—'}</td>
          <td style="white-space:nowrap">
            <button class="btn btn-secondary btn-sm" onclick="moveDealStage(${d.id})">Move</button>
            <button class="btn btn-danger btn-sm" onclick="deleteDeal(${d.id},'${d.deal_number}')">🗑️</button>
          </td>
        </tr>`).join('')}</tbody>
      </table></div>
      ${renderPagination(data.page, data.pages, 'p=>{state.deals.page=p;sections.deals()}')}
    </div>`;
};
async function showDealForm() {
  const lr = await api('GET','/leads?limit=100&order=DESC');
  const leads = lr?.data?.data||[];
  openModal('New Deal',`
    <div id="df-alert"></div>
    <div class="form-grid">
      <div class="form-group"><label>Lead *</label><select id="df-lead_id">${leads.map(l=>`<option value="${l.id}">${l.first_name} ${l.last_name} (${l.lead_number})</option>`).join('')}</select></div>
      <div class="form-group"><label>Deal Value (₹) *</label><input type="number" id="df-deal_value" placeholder="50000"></div>
      <div class="form-group"><label>Deal Stage</label><select id="df-deal_stage"><option>Prospecting</option><option>Qualification</option><option>Proposal</option><option>Negotiation</option><option>Won</option><option>Lost</option></select></div>
      <div class="form-group"><label>Probability (%)</label><input type="number" id="df-probability" value="10" min="0" max="100"></div>
      <div class="form-group full"><label>Expected Close Date</label><input type="date" id="df-expected_close_date"></div>
    </div>
    <div class="form-actions"><button class="btn btn-secondary" onclick="closeModal()">Cancel</button><button class="btn btn-primary" onclick="saveDeal()">Create Deal</button></div>`);
}
async function saveDeal() {
  const body={lead_id:parseInt(document.getElementById('df-lead_id').value),deal_value:parseFloat(document.getElementById('df-deal_value').value),deal_stage:document.getElementById('df-deal_stage').value,probability:parseInt(document.getElementById('df-probability').value),expected_close_date:document.getElementById('df-expected_close_date').value||null};
  if(!body.deal_value){showAlert(document.getElementById('df-alert'),'Deal value required.');return;}
  const res=await api('POST','/deals',body);
  if(!res?.success){showAlert(document.getElementById('df-alert'),res?.message||'Failed');return;}
  closeModal();sections.deals();
}
async function moveDealStage(id){
  const stages=['Prospecting','Qualification','Proposal','Negotiation','Won','Lost'];
  const stage=prompt('Move to stage:\n'+stages.join(', '));
  if(!stage||!stages.includes(stage.trim()))return;
  const probMap={Prospecting:10,Qualification:30,Proposal:50,Negotiation:70,Won:100,Lost:0};
  await api('PUT',`/deals/${id}`,{deal_stage:stage.trim(),probability:probMap[stage.trim()]||50});
  sections.deals();
}
async function deleteDeal(id,num){if(!confirm(`Delete deal ${num}?`))return;await api('DELETE',`/deals/${id}`);sections.deals();}

/* ─── INVOICES ─── */
sections.invoices = async function() {
  const s = state.invoices;
  const res = await api('GET',`/invoices?page=${s.page}&limit=15&order=DESC${s.status?'&status='+s.status:''}`);
  const data = res?.data || { data:[], total:0, pages:1 };
  document.getElementById('content').innerHTML = `
    <div class="section-header">
      <div class="section-title">Invoices <span style="color:var(--text-muted);font-size:.8rem">(${fmtNum(data.total)})</span></div>
      <div class="section-filters">
        <select class="filter" onchange="state.invoices.status=this.value;state.invoices.page=1;sections.invoices()">
          <option value="">All Status</option>
          ${['Unpaid','Partial','Paid','Overdue'].map(st=>`<option value="${st}" ${s.status===st?'selected':''}>${st}</option>`).join('')}
        </select>
      </div>
    </div>
    <div class="card">
      <div class="table-wrap"><table>
        <thead><tr><th>#</th><th>Customer</th><th>Invoice Date</th><th>Due Date</th><th>Grand Total</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>${data.data.length===0?'<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted)">No invoices</td></tr>':
        data.data.map(i=>`<tr>
          <td style="color:var(--accent-light);font-weight:600">${i.invoice_number}</td>
          <td><strong>${i.company_name||'Unknown'}</strong></td>
          <td>${i.invoice_date}</td>
          <td>${i.due_date}</td>
          <td style="font-weight:700;color:var(--green)">${fmtCurrency(i.grand_total)}</td>
          <td>${statusBadge(i.status)}</td>
          <td><button class="btn btn-danger btn-sm" onclick="deleteInvoice(${i.id},'${i.invoice_number}')">🗑️</button></td>
        </tr>`).join('')}</tbody>
      </table></div>
      ${renderPagination(data.page, data.pages, 'p=>{state.invoices.page=p;sections.invoices()}')}
    </div>`;
};
async function deleteInvoice(id,num){if(!confirm(`Delete invoice ${num}?`))return;await api('DELETE',`/invoices/${id}`);sections.invoices();}

/* ─── PAYMENTS ─── */
sections.payments = async function() {
  const res = await api('GET',`/payments?page=${state.payments.page}&limit=15`);
  const data = res?.data || { data:[], total:0, pages:1 };
  document.getElementById('content').innerHTML = `
    <div class="section-header">
      <div class="section-title">Payments <span style="color:var(--text-muted);font-size:.8rem">(${fmtNum(data.total)})</span></div>
      <button class="btn btn-primary btn-sm" onclick="showPaymentForm()">+ Record Payment</button>
    </div>
    <div class="card">
      <div class="table-wrap"><table>
        <thead><tr><th>Invoice #</th><th>Date</th><th>Amount</th><th>Mode</th><th>Reference</th><th>Status</th></tr></thead>
        <tbody>${data.data.length===0?'<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)">No payments</td></tr>':
        data.data.map(p=>`<tr>
          <td style="color:var(--accent-light)">${p.invoice_number||'#'+p.invoice_id}</td>
          <td>${p.payment_date}</td>
          <td style="font-weight:700;color:var(--green)">${fmtCurrency(p.amount)}</td>
          <td>${statusBadge(p.payment_mode)}</td>
          <td style="color:var(--text-muted)">${p.transaction_reference||'—'}</td>
          <td>${statusBadge(p.status)}</td>
        </tr>`).join('')}</tbody>
      </table></div>
      ${renderPagination(data.page, data.pages, 'p=>{state.payments.page=p;sections.payments()}')}
    </div>`;
};
async function showPaymentForm() {
  const ir = await api('GET','/invoices?status=Unpaid&limit=100');
  const invs = ir?.data?.data||[];
  openModal('Record Payment',`
    <div id="pf-alert"></div>
    <div class="form-grid">
      <div class="form-group"><label>Invoice *</label><select id="pf-invoice_id">${invs.length?invs.map(i=>`<option value="${i.id}">${i.invoice_number} — ${fmtCurrency(i.grand_total)}</option>`).join(''):'<option>No unpaid invoices</option>'}</select></div>
      <div class="form-group"><label>Amount (₹) *</label><input type="number" id="pf-amount" placeholder="Enter amount paid"></div>
      <div class="form-group"><label>Payment Date *</label><input type="date" id="pf-payment_date" value="${new Date().toISOString().split('T')[0]}"></div>
      <div class="form-group"><label>Payment Mode</label><select id="pf-payment_mode"><option>Bank Transfer</option><option>Cash</option><option>Cheque</option><option>UPI</option><option>Credit Card</option><option>Online</option></select></div>
      <div class="form-group full"><label>Transaction Reference</label><input id="pf-transaction_reference" placeholder="UTR / Cheque No. / Ref ID"></div>
    </div>
    <div class="form-actions"><button class="btn btn-secondary" onclick="closeModal()">Cancel</button><button class="btn btn-primary" onclick="savePayment()">Record Payment</button></div>`);
}
async function savePayment() {
  const body={invoice_id:parseInt(document.getElementById('pf-invoice_id').value),amount:parseFloat(document.getElementById('pf-amount').value),payment_date:document.getElementById('pf-payment_date').value,payment_mode:document.getElementById('pf-payment_mode').value,transaction_reference:document.getElementById('pf-transaction_reference').value};
  if(!body.amount||!body.payment_date){showAlert(document.getElementById('pf-alert'),'Amount and date required.');return;}
  const res=await api('POST','/payments',body);
  if(!res?.success){showAlert(document.getElementById('pf-alert'),res?.message||'Failed');return;}
  closeModal();sections.payments();
}

/* ─── WHATSAPP ─── */
sections.whatsapp = async function() {
  const res = await api('GET','/whatsapp/history');
  const logs = res?.data?.data||res?.data||[];
  document.getElementById('content').innerHTML = `
    <div class="section-header">
      <div class="section-title">WhatsApp Messages</div>
      <button class="btn btn-primary btn-sm" onclick="showWhatsAppForm()">+ Send Message</button>
    </div>
    <div class="card">
      ${logs.length===0?'<div class="empty-state"><div class="empty-icon">💬</div><div class="empty-title">No WhatsApp messages yet</div></div>':
      `<div class="table-wrap"><table>
        <thead><tr><th>Lead</th><th>Recipient</th><th>Message</th><th>Status</th><th>Sent By</th><th>Date</th><th>Action</th></tr></thead>
        <tbody>${logs.map(w=>`<tr>
          <td>${w.lead_id?'#'+w.lead_id:'—'}</td>
          <td style="font-weight:600">${w.recipient_number}</td>
          <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${w.message}</td>
          <td>${statusBadge(w.status)}</td>
          <td>${w.sender_name||'—'}</td>
          <td>${(w.created_at||'').split(' ')[0]}</td>
          <td><a href="https://wa.me/${(w.recipient_number||'').replace(/[^0-9]/g,'')}" target="_blank" class="btn btn-success btn-sm">Open WA</a></td>
        </tr>`).join('')}</tbody>
      </table></div>`}
    </div>`;
};
async function showWhatsAppForm() {
  const lr = await api('GET','/leads?limit=100&order=DESC');
  const leads = lr?.data?.data||[];
  openModal('Send WhatsApp Message',`
    <div id="wa-alert"></div>
    <div class="form-grid">
      <div class="form-group"><label>Lead (optional)</label><select id="wa-lead_id"><option value="">— Select Lead —</option>${leads.map(l=>`<option value="${l.id}" data-mobile="${l.mobile}">${l.first_name} ${l.last_name} | ${l.mobile}</option>`).join('')}</select></div>
      <div class="form-group"><label>Recipient Number *</label><input id="wa-recipient_number" placeholder="+919876543210"></div>
      <div class="form-group full"><label>Message *</label><textarea id="wa-message" rows="4" placeholder="Hello, following up on your inquiry…"></textarea></div>
    </div>
    <div class="form-actions"><button class="btn btn-secondary" onclick="closeModal()">Cancel</button><button class="btn btn-primary" onclick="sendWhatsApp()">💬 Send</button></div>`);
  document.getElementById('wa-lead_id').onchange = function(){
    const opt = this.options[this.selectedIndex];
    const mobile = opt.dataset.mobile;
    if(mobile) document.getElementById('wa-recipient_number').value = mobile;
  };
}
async function sendWhatsApp() {
  const body={lead_id:parseInt(document.getElementById('wa-lead_id').value)||null,recipient_number:document.getElementById('wa-recipient_number').value,message:document.getElementById('wa-message').value};
  if(!body.recipient_number||!body.message){showAlert(document.getElementById('wa-alert'),'Number and message required.');return;}
  const res=await api('POST','/whatsapp/send',body);
  if(!res?.success){showAlert(document.getElementById('wa-alert'),res?.message||'Failed');return;}
  const waLink=res.data?.wa_link;
  if(waLink) window.open(waLink,'_blank');
  closeModal();sections.whatsapp();
}

/* ─── MEETINGS ─── */
sections.meetings = async function() {
  const res = await api('GET','/meetings?limit=20');
  const data = res?.data||{data:[]};
  const meetings = data.data||data||[];
  document.getElementById('content').innerHTML = `
    <div class="section-header">
      <div class="section-title">Meetings</div>
      <button class="btn btn-primary btn-sm" onclick="showMeetingForm()">+ Schedule Meeting</button>
    </div>
    <div class="card">
      ${meetings.length===0?'<div class="empty-state"><div class="empty-icon">🤝</div><div class="empty-title">No meetings scheduled</div></div>':
      `<div class="table-wrap"><table>
        <thead><tr><th>Title</th><th>Lead</th><th>Date</th><th>Time</th><th>Host</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>${meetings.map(m=>`<tr>
          <td><strong>${m.title}</strong>${m.notes?`<br><span style="font-size:.7rem;color:var(--text-muted)">${m.notes.substring(0,40)}</span>`:''}</td>
          <td>${m.lead_name||'#'+m.lead_id}</td>
          <td>${m.meeting_date}</td>
          <td>${m.meeting_time||'—'}</td>
          <td>${m.host_name||'—'}</td>
          <td>${statusBadge(m.status)}</td>
          <td><button class="btn btn-danger btn-sm" onclick="deleteMeeting(${m.id})">🗑️</button></td>
        </tr>`).join('')}</tbody>
      </table></div>`}
    </div>`;
};
async function showMeetingForm() {
  const lr = await api('GET','/leads?limit=100&order=DESC');
  const leads = lr?.data?.data||[];
  openModal('Schedule Meeting',`
    <div id="mf-alert"></div>
    <div class="form-grid">
      <div class="form-group full"><label>Title *</label><input id="mf-title" placeholder="Client Demo / Strategy Call"></div>
      <div class="form-group"><label>Lead *</label><select id="mf-lead_id">${leads.map(l=>`<option value="${l.id}">${l.first_name} ${l.last_name} (${l.lead_number})</option>`).join('')}</select></div>
      <div class="form-group"><label>Status</label><select id="mf-status"><option>Scheduled</option><option>Completed</option><option>Cancelled</option></select></div>
      <div class="form-group"><label>Meeting Date *</label><input type="date" id="mf-meeting_date" value="${new Date().toISOString().split('T')[0]}"></div>
      <div class="form-group"><label>Meeting Time</label><input type="time" id="mf-meeting_time" value="10:00"></div>
      <div class="form-group full"><label>Notes / Agenda</label><textarea id="mf-notes" placeholder="Meeting agenda, participants…"></textarea></div>
    </div>
    <div class="form-actions"><button class="btn btn-secondary" onclick="closeModal()">Cancel</button><button class="btn btn-primary" onclick="saveMeeting()">Schedule Meeting</button></div>`);
}
async function saveMeeting() {
  const body={lead_id:parseInt(document.getElementById('mf-lead_id').value),title:document.getElementById('mf-title').value,meeting_date:document.getElementById('mf-meeting_date').value,meeting_time:document.getElementById('mf-meeting_time').value+':00',status:document.getElementById('mf-status').value,notes:document.getElementById('mf-notes').value};
  if(!body.title||!body.meeting_date){showAlert(document.getElementById('mf-alert'),'Title and date required.');return;}
  const res=await api('POST','/meetings',body);
  if(!res?.success){showAlert(document.getElementById('mf-alert'),res?.message||'Failed');return;}
  closeModal();sections.meetings();
}
async function deleteMeeting(id){if(!confirm('Delete meeting?'))return;await api('DELETE',`/meetings/${id}`);sections.meetings();}

/* ─── DOCUMENTS ─── */
sections.documents = async function() {
  const s = state.documents;
  const params = `?page=${s.page}&limit=15${s.search?'&search='+encodeURIComponent(s.search):''}`;
  const res = await api('GET','/documents'+params);
  const data = res?.data || { data:[], total:0, pages:1 };
  document.getElementById('content').innerHTML = `
    <div class="section-header">
      <div class="section-title">Documents <span style="color:var(--text-muted);font-size:.8rem">(${fmtNum(data.total)})</span></div>
      <div class="section-filters">
        <div class="search-bar"><input type="text" placeholder="Search documents…" value="${s.search||''}" oninput="state.documents.search=this.value;state.documents.page=1;sections.documents()"></div>
        <button class="btn btn-secondary btn-sm" onclick="showDocumentForm()">+ Register URL</button>
        <button class="btn btn-primary btn-sm" onclick="showMediaUpload()">📁 Upload File</button>
      </div>
    </div>
    <div class="card">
      <div class="table-wrap"><table>
        <thead><tr><th>Document Name</th><th>Lead</th><th>Customer</th><th>File</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>${data.data.length===0?'<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted)">No documents found</td></tr>':
        data.data.map(d=>`<tr>
          <td><strong>${d.document_name}</strong></td>
          <td>${d.lead_id?'#'+d.lead_id:'—'}</td>
          <td>${d.customer_id?'#'+d.customer_id:'—'}</td>
          <td>${d.file_url?`<a href="${d.file_url}" target="_blank" class="btn btn-secondary btn-sm">📄 View</a>`:'—'}</td>
          <td>${statusBadge(d.status||'Active')}</td>
          <td>${(d.created_at||'').split(' ')[0]}</td>
          <td><button class="btn btn-danger btn-sm" onclick="deleteDocument(${d.id})">🗑️</button></td>
        </tr>`).join('')}</tbody>
      </table></div>
      ${renderPagination(data.page, data.pages, 'p=>{state.documents.page=p;sections.documents()}')}
    </div>`;
};
async function showDocumentForm() {
  const lr = await api('GET','/leads?limit=100&order=DESC');
  const leads = lr?.data?.data||[];
  const cr = await api('GET','/customers?limit=100');
  const customers = cr?.data?.data||[];
  openModal('Register Document (by URL)',`
    <div id="docf-alert"></div>
    <div class="form-grid">
      <div class="form-group full"><label>Document Name *</label><input id="docf-document_name" placeholder="Contract Agreement, NDA, Invoice copy…"></div>
      <div class="form-group"><label>Lead</label><select id="docf-lead_id"><option value="">— None —</option>${leads.map(l=>`<option value="${l.id}">${l.first_name} ${l.last_name} (${l.lead_number})</option>`).join('')}</select></div>
      <div class="form-group"><label>Customer</label><select id="docf-customer_id"><option value="">— None —</option>${customers.map(c=>`<option value="${c.id}">${c.company_name}</option>`).join('')}</select></div>
      <div class="form-group full"><label>File URL *</label><input id="docf-file_url" placeholder="https://drive.google.com/file/…"></div>
    </div>
    <div class="form-actions"><button class="btn btn-secondary" onclick="closeModal()">Cancel</button><button class="btn btn-primary" onclick="saveDocument()">Save Document</button></div>`);
}
async function saveDocument() {
  const body={document_name:document.getElementById('docf-document_name').value,file_url:document.getElementById('docf-file_url').value,lead_id:parseInt(document.getElementById('docf-lead_id').value)||null,customer_id:parseInt(document.getElementById('docf-customer_id').value)||null};
  if(!body.document_name||!body.file_url){showAlert(document.getElementById('docf-alert'),'Name and URL required.');return;}
  const res=await api('POST','/documents',body);
  if(!res?.success){showAlert(document.getElementById('docf-alert'),res?.message||'Failed');return;}
  closeModal();sections.documents();
}
async function showMediaUpload() {
  openModal('Upload File to WordPress Media Library',`
    <div id="media-alert"></div>
    <p style="font-size:.8rem;color:var(--text-muted);margin-bottom:16px">Supports: JPG, PNG, PDF, DOC, DOCX, XLS, XLSX — Max 20MB</p>
    <div class="form-grid">
      <div class="form-group full"><label>Select File *</label><input type="file" id="media-file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx" style="padding:8px"></div>
      <div class="form-group full"><label>Document Name</label><input id="media-doc_name" placeholder="Leave blank to use filename"></div>
    </div>
    <div id="media-progress" style="display:none;margin-top:12px">
      <div style="background:var(--border);border-radius:4px;height:6px;overflow:hidden">
        <div id="media-bar" style="background:var(--accent);height:100%;width:0%;transition:width .3s"></div>
      </div>
      <div id="media-status" style="font-size:.75rem;color:var(--text-muted);margin-top:6px">Uploading…</div>
    </div>
    <div class="form-actions"><button class="btn btn-secondary" onclick="closeModal()">Cancel</button><button class="btn btn-primary" onclick="uploadMedia()">📤 Upload</button></div>`);
}
async function uploadMedia() {
  const fileInput = document.getElementById('media-file');
  if (!fileInput?.files?.length) { showAlert(document.getElementById('media-alert'),'Please select a file.'); return; }
  const file = fileInput.files[0];
  const docName = document.getElementById('media-doc_name')?.value||'';
  const formData = new FormData();
  formData.append('file', file);
  if (docName) formData.append('document_name', docName);
  document.getElementById('media-progress').style.display='block';
  document.getElementById('media-bar').style.width='40%';
  try {
    const res = await fetch(API+'/media/upload', { method:'POST', headers:{'Authorization':'Bearer '+authToken}, body:formData });
    document.getElementById('media-bar').style.width='100%';
    const data = await res.json();
    if (data?.success) {
      document.getElementById('media-status').textContent='✅ Uploaded: '+data.data.url;
      setTimeout(()=>{ closeModal(); sections.documents(); }, 1500);
    } else {
      document.getElementById('media-status').textContent='Error: '+(data?.message||'Upload failed');
      document.getElementById('media-bar').style.background='var(--red)';
    }
  } catch(e) {
    document.getElementById('media-status').textContent='Network error: '+e.message;
  }
}
async function deleteDocument(id){if(!confirm('Delete document?'))return;await api('DELETE',`/documents/${id}`);sections.documents();}

/* ─── ACTIVITY LOG ─── */
sections.activity = async function() {
  const s = state.activity;
  const res = await api('GET',`/dashboard/activity-logs?page=${s.page}&limit=30`);
  const data = res?.data || { data:[], total:0, pages:1 };
  const logs = data.data||[];
  document.getElementById('content').innerHTML = `
    <div class="section-header">
      <div class="section-title">Activity Log <span style="color:var(--text-muted);font-size:.8rem">(${fmtNum(data.total)} events)</span></div>
      <div class="section-filters">
        <button class="btn btn-secondary btn-sm" onclick="sections.activity()">🔄 Refresh</button>
      </div>
    </div>
    <div class="card">
      ${logs.length===0?'<div class="empty-state"><div class="empty-icon">🕐</div><div class="empty-title">No activity recorded yet</div></div>':
      `<div class="activity-feed">
        ${logs.map(log=>{
          const icon = activityIcons[log.action_type]||activityIcons.DEFAULT;
          const timeAgo = log.created_at ? new Date(log.created_at).toLocaleString('en-IN',{day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'}) : '—';
          return `<div class="activity-item">
            <div class="activity-dot">${icon}</div>
            <div class="activity-body">
              <div class="activity-action">${log.username||'System'}</div>
              <div class="activity-detail">${log.action_type||'ACTION'} — ${log.description||''}</div>
              <div class="activity-time">${timeAgo}</div>
            </div>
          </div>`;
        }).join('')}
      </div>`}
      ${renderPagination(data.page, data.pages, 'p=>{state.activity.page=p;sections.activity()}')}
    </div>`;
};

/* ─── REPORTS ─── */
sections.reports = async function() {
  document.getElementById('content').innerHTML = `
    <div class="section-header"><div class="section-title">Reports & Analytics</div></div>
    <div class="tabs">
      <button class="tab active" onclick="loadReport('leads',this)">📊 Leads</button>
      <button class="tab" onclick="loadReport('revenue',this)">💰 Revenue</button>
      <button class="tab" onclick="loadReport('conversion',this)">📈 Conversion</button>
      <button class="tab" onclick="loadReport('sources',this)">🌐 Sources</button>
      <button class="tab" onclick="loadReport('team',this)">👥 Team</button>
      <button class="tab" onclick="loadReport('forecast',this)">🔮 Forecast</button>
    </div>
    <div id="report-content"><div class="loading-state"><div class="spinner"></div></div></div>`;
  loadReport('leads');
};
async function loadReport(type, tabEl=null) {
  document.querySelectorAll('.tab').forEach(t=>t.classList.remove('active'));
  if(tabEl) tabEl.classList.add('active');
  const rEl = document.getElementById('report-content');
  if(!rEl) return;
  rEl.innerHTML='<div class="loading-state"><div class="spinner"></div></div>';

  if(type==='leads') {
    const res=await api('GET','/reports/leads');
    const d=res?.data||{};
    rEl.innerHTML=`<div class="charts-grid">
      <div class="chart-card"><div class="card-header"><div class="card-title">Leads by Status</div></div><canvas id="rc-status" height="220"></canvas></div>
      <div class="chart-card"><div class="card-header"><div class="card-title">Daily New Leads Trend</div></div><canvas id="rc-trend" height="220"></canvas></div>
    </div>`;
    const byStatus=d.by_status||[];
    createChart('rc-status','pie',byStatus.map(s=>s.lead_status),[{data:byStatus.map(s=>s.count),backgroundColor:['#7c3aed','#3b82f6','#f59e0b','#10b981','#06b6d4','#ef4444','#ec4899','#64748b']}]);
    const daily=d.daily_trend||[];
    createChart('rc-trend','line',daily.map(d=>d.date),[{label:'New Leads',data:daily.map(d=>d.count),borderColor:'#7c3aed',backgroundColor:'rgba(124,58,237,.1)',fill:true,tension:.4}]);
  } else if(type==='revenue') {
    const res=await api('GET','/reports/revenue');
    const d=res?.data||{};
    rEl.innerHTML=`<div style="display:flex;gap:14px;margin-bottom:18px">
      <div class="kpi-card" style="flex:1;--kpi-color:var(--green)"><div class="kpi-icon">💰</div><div class="kpi-value">${fmtCurrency(d.total_revenue||0)}</div><div class="kpi-label">Total Revenue ${d.year||''}</div></div>
      <div class="kpi-card" style="flex:1;--kpi-color:var(--cyan)"><div class="kpi-icon">📅</div><div class="kpi-value">${fmtCurrency(d.avg_monthly||0)}</div><div class="kpi-label">Avg Monthly</div></div>
    </div>
    <div class="chart-card"><div class="card-header"><div class="card-title">Monthly Revenue Trend</div></div><canvas id="rc-rev" height="260"></canvas></div>`;
    const monthly=d.monthly||[];
    createChart('rc-rev','bar',monthly.map(m=>m.month),[{label:'Revenue (₹)',data:monthly.map(m=>parseFloat(m.revenue||0)),backgroundColor:'rgba(16,185,129,.7)',borderRadius:6,borderSkipped:false}]);
  } else if(type==='conversion') {
    const res=await api('GET','/reports/conversion-rate');
    const d=res?.data||{};
    rEl.innerHTML=`<div class="kpi-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px">
      <div class="kpi-card" style="--kpi-color:var(--cyan)"><div class="kpi-icon">👥</div><div class="kpi-value">${d.total_leads||0}</div><div class="kpi-label">Total Leads</div></div>
      <div class="kpi-card" style="--kpi-color:var(--green)"><div class="kpi-icon">🏆</div><div class="kpi-value">${d.lead_conversion||0}%</div><div class="kpi-label">Lead→Deal</div></div>
      <div class="kpi-card" style="--kpi-color:var(--accent-light)"><div class="kpi-icon">💰</div><div class="kpi-value">${d.deal_conversion||0}%</div><div class="kpi-label">Deal Win Rate</div></div>
    </div>`;
  } else if(type==='sources') {
    const res=await api('GET','/reports/lead-sources');
    const d=res?.data||{};
    const sources=d.sources||[];
    rEl.innerHTML=`<div class="charts-grid">
      <div class="chart-card"><div class="card-header"><div class="card-title">Lead Sources</div></div><canvas id="rc-src" height="250"></canvas></div>
      <div class="card"><div class="card-header"><div class="card-title">Source Breakdown</div></div>
        <div class="table-wrap"><table><thead><tr><th>Source</th><th>Count</th><th>Share</th></tr></thead>
        <tbody>${sources.map(s=>`<tr><td>${s.lead_source}</td><td>${s.count}</td><td><div style="display:flex;align-items:center;gap:8px"><div style="flex:1;height:5px;background:var(--border);border-radius:3px;overflow:hidden"><div style="width:${s.percentage}%;height:100%;background:var(--accent-light)"></div></div><span style="font-size:.75rem">${s.percentage}%</span></div></td></tr>`).join('')}</tbody>
        </table></div>
      </div>
    </div>`;
    createChart('rc-src','doughnut',sources.map(s=>s.lead_source),[{data:sources.map(s=>s.count),backgroundColor:['#7c3aed','#3b82f6','#f59e0b','#10b981','#06b6d4','#ef4444','#ec4899','#64748b'],hoverOffset:4}]);
  } else if(type==='team') {
    const res=await api('GET','/reports/team-performance');
    const d=res?.data||{};
    rEl.innerHTML=`<div class="card"><div class="table-wrap"><table>
      <thead><tr><th>Name</th><th>Role</th><th>Leads</th><th>Deals Won</th><th>Revenue</th></tr></thead>
      <tbody>${(d.performance||[]).map(p=>`<tr>
        <td><strong>${p.name}</strong></td>
        <td>${statusBadge(p.role)}</td>
        <td>${p.leads_assigned}</td>
        <td><span style="color:var(--green);font-weight:700">${p.deals_won}</span></td>
        <td style="color:var(--green);font-weight:700">${fmtCurrency(p.revenue_value)}</td>
      </tr>`).join('')}</tbody>
    </table></div></div>`;
  } else if(type==='forecast') {
    const res=await api('GET','/reports/forecast');
    const d=res?.data||{};
    rEl.innerHTML=`<div style="display:flex;gap:14px;margin-bottom:18px">
      <div class="kpi-card" style="flex:1;--kpi-color:var(--accent-light)"><div class="kpi-icon">🔮</div><div class="kpi-value">${fmtCurrency(d.total_forecast||0)}</div><div class="kpi-label">Weighted Forecast</div></div>
      <div class="kpi-card" style="flex:1;--kpi-color:var(--blue)"><div class="kpi-icon">📊</div><div class="kpi-value">${d.open_deals_count||0}</div><div class="kpi-label">Open Deals</div></div>
    </div>
    <div class="chart-card"><div class="card-header"><div class="card-title">Monthly Revenue Forecast</div></div><canvas id="rc-forecast" height="250"></canvas></div>`;
    const monthly=d.monthly_forecast||[];
    createChart('rc-forecast','bar',monthly.map(m=>m.month),[{label:'Forecast (₹)',data:monthly.map(m=>parseFloat(m.forecast_value||0)),backgroundColor:'rgba(139,92,246,.7)',borderRadius:6}]);
  }
}

/* ─── USERS ─── */
sections.users = async function() {
  const res = await api('GET','/auth/users');
  const users = res?.data||[];
  document.getElementById('content').innerHTML = `
    <div class="section-header"><div class="section-title">User Management <span style="color:var(--text-muted);font-size:.8rem">(${users.length} users)</span></div></div>
    <div class="card">
      <div class="table-wrap"><table>
        <thead><tr><th>Username</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>${users.length===0?'<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)">No CRM users found</td></tr>':
        users.map(u=>`<tr>
          <td style="color:var(--accent-light);font-weight:600">@${u.username}</td>
          <td><strong>${u.name}</strong></td>
          <td style="color:var(--text-muted);font-size:.75rem">${u.email}</td>
          <td>${statusBadge(u.role)}</td>
          <td>${statusBadge(u.status)}</td>
          <td style="white-space:nowrap">
            <button class="btn btn-secondary btn-sm" onclick="changeUserStatus(${u.id},'${u.username}')">Status</button>
            ${currentUser?.id!=u.id?`<button class="btn btn-danger btn-sm" onclick="deleteUser(${u.id},'${u.username}')">🗑️</button>`:''}
          </td>
        </tr>`).join('')}</tbody>
      </table></div>
    </div>`;
};
async function changeUserStatus(id,username) {
  const status=prompt(`New status for @${username}:\nAPPROVED, PENDING, BLOCKED, HOLD`);
  if(!status||!['APPROVED','PENDING','BLOCKED','HOLD'].includes(status.toUpperCase()))return;
  await api('POST','/auth/users/status',{user_id:id,status:status.toUpperCase()});
  sections.users();
}
async function deleteUser(id,username) {
  if(!confirm(`Permanently delete @${username}?`))return;
  await api('DELETE',`/auth/users/${id}`);
  sections.users();
}

/* ─── SETTINGS ─── */
sections.settings = async function() {
  const res = await api('GET','/auth/smtp');
  const smtp = res?.data||{};
  document.getElementById('content').innerHTML = `
    <div class="section-header"><div class="section-title">Settings</div></div>
    <div style="display:grid;grid-template-columns:1fr 380px;gap:18px;align-items:start">
      <div class="card">
        <div class="card-header"><div class="card-title">⚙️ SMTP Email Configuration</div></div>
        <div id="smtp-alert"></div>
        <div class="form-grid">
          <div class="form-group"><label>SMTP Enabled</label><select id="smtp-enabled"><option value="no" ${smtp.smtp_enabled!=='yes'?'selected':''}>Disabled</option><option value="yes" ${smtp.smtp_enabled==='yes'?'selected':''}>Enabled</option></select></div>
          <div class="form-group"><label>SMTP Host</label><input id="smtp-host" value="${smtp.smtp_host||''}" placeholder="smtp.gmail.com"></div>
          <div class="form-group"><label>SMTP Port</label><input id="smtp-port" value="${smtp.smtp_port||'587'}" placeholder="587"></div>
          <div class="form-group"><label>Encryption</label><select id="smtp-encryption"><option value="tls" ${smtp.smtp_encryption==='tls'?'selected':''}>TLS (587)</option><option value="ssl" ${smtp.smtp_encryption==='ssl'?'selected':''}>SSL (465)</option><option value="none" ${smtp.smtp_encryption==='none'?'selected':''}>None</option></select></div>
          <div class="form-group"><label>Username / Email</label><input id="smtp-username" value="${smtp.smtp_username||''}" placeholder="your@gmail.com"></div>
          <div class="form-group"><label>Password / App Password</label><input type="password" id="smtp-password" placeholder="Leave blank to keep existing"></div>
          <div class="form-group"><label>From Email</label><input id="smtp-from_email" value="${smtp.smtp_from_email||''}" placeholder="noreply@domain.com"></div>
          <div class="form-group"><label>From Name</label><input id="smtp-from_name" value="${smtp.smtp_from_name||'CRM ERP'}" placeholder="CRM ERP"></div>
        </div>
        <div class="form-actions" style="justify-content:space-between">
          <div style="display:flex;gap:8px;align-items:center">
            <input id="smtp-test-email" style="width:190px" placeholder="test@example.com">
            <button class="btn btn-secondary" onclick="testSmtp()">Send Test</button>
          </div>
          <button class="btn btn-primary" onclick="saveSmtp()">Save SMTP</button>
        </div>
      </div>
      <div>
        <div class="card">
          <div class="card-header"><div class="card-title">👤 My Account</div></div>
          <div style="display:flex;gap:14px;align-items:center;padding:4px 0">
            <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--cyan));display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:800;flex-shrink:0">${(currentUser?.name||'?').charAt(0).toUpperCase()}</div>
            <div>
              <div style="font-size:1rem;font-weight:700">${currentUser?.name||'Unknown'}</div>
              <div style="color:var(--text-muted);font-size:.78rem">${currentUser?.email||''}</div>
              <div style="margin-top:5px">${statusBadge(currentUser?.role||'')}</div>
            </div>
          </div>
        </div>
        <div class="card" style="margin-top:14px">
          <div class="card-header"><div class="card-title">🔗 Quick Links</div></div>
          <div style="display:flex;flex-direction:column;gap:8px">
            <button class="btn btn-secondary" style="justify-content:flex-start" onclick="window.open('<?php echo esc_js($site_url); ?>/crm-management-api-docs/','_blank')">📘 API Documentation</button>
            <button class="btn btn-secondary" style="justify-content:flex-start" onclick="navigate('activity')">🕐 Activity Log</button>
            <button class="btn btn-secondary" style="justify-content:flex-start" onclick="navigate('users')">👤 User Management</button>
            <button class="btn btn-danger" style="justify-content:flex-start" onclick="doLogout()">🚪 Logout</button>
          </div>
        </div>
      </div>
    </div>`;
};
async function saveSmtp() {
  const body={smtp_enabled:document.getElementById('smtp-enabled').value,smtp_host:document.getElementById('smtp-host').value,smtp_port:document.getElementById('smtp-port').value,smtp_encryption:document.getElementById('smtp-encryption').value,smtp_username:document.getElementById('smtp-username').value,smtp_from_email:document.getElementById('smtp-from_email').value,smtp_from_name:document.getElementById('smtp-from_name').value};
  const pass=document.getElementById('smtp-password').value;
  if(pass) body.smtp_password=pass;
  const res=await api('POST','/auth/smtp',body);
  showAlert(document.getElementById('smtp-alert'),res?.success?'SMTP settings saved successfully.':(res?.message||'Failed'),res?.success?'success':'error');
}
async function testSmtp() {
  const email=document.getElementById('smtp-test-email').value;
  if(!email){alert('Enter test email address.');return;}
  const res=await api('POST','/auth/smtp/test',{test_email:email});
  showAlert(document.getElementById('smtp-alert'),res?.success?'✅ Test email sent! Check your inbox.':(res?.message||'Failed'),res?.success?'success':'error');
}

/* ─── INIT ─── */
document.getElementById('login-password').addEventListener('keydown', e=>{ if(e.key==='Enter') doLogin(); });
checkAuth();
</script>
</body>
</html>
