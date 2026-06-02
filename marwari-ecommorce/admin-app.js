/* ===== ADMIN PANEL — Standalone Application ===== */
(function () {
  "use strict";

  const API_ROOT = (typeof wpApiSettings !== "undefined" && wpApiSettings.root)
    ? wpApiSettings.root + "marwari-ecom/v1"
    : "/wp-json/marwari-ecom/v1";
  let NONCE = (typeof wpApiSettings !== "undefined") ? wpApiSettings.nonce : "";

  // ===== API Helper =====
  async function apiFetch(endpoint, options = {}) {
    const url = `${API_ROOT}${endpoint}`;
    const headers = { "Content-Type": "application/json", ...options.headers };
    if (NONCE) headers["X-WP-Nonce"] = NONCE;

    const res = await fetch(url, {
      credentials: "same-origin",
      ...options,
      headers,
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || `API Error (${res.status})`);
    return data;
  }

  // ===== Toast =====
  function showToast(message, type = "success") {
    const container = document.getElementById("toast-container");
    if (!container) return;
    const toast = document.createElement("div");
    toast.className = `toast ${type}`;
    toast.innerHTML = `<span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => { toast.style.opacity = "0"; setTimeout(() => toast.remove(), 300); }, 3500);
  }

  // ===== DOM Ready =====
  document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("admin-login-form");
    const loginScreen = document.getElementById("admin-login-screen");
    const adminLayout = document.getElementById("admin-layout");

    // ===== DATA =====
    let allProducts = [];
    let allOrders = [];
    let allCustomers = [];

    if (!loginForm || !loginScreen || !adminLayout) return;

    // Check saved session
    const saved = localStorage.getItem("marwari_admin_session");
    if (saved) {
      try {
        const session = JSON.parse(saved);
        if (session && session.role === "admin") {
          showDashboard(session);
          return;
        }
      } catch (e) { /* ignore */ }
    }

    // Login Handler
    loginForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const email = document.getElementById("admin-login-email").value.trim();
      const password = document.getElementById("admin-login-password").value;
      const btn = document.getElementById("admin-login-btn");

      btn.textContent = "Authenticating...";
      btn.disabled = true;

      try {
        const res = await apiFetch("/auth/admin-login", {
          method: "POST",
          body: JSON.stringify({ email, password }),
        });

        if (res.success) {
          if (res.nonce) NONCE = res.nonce;
          localStorage.setItem("marwari_admin_session", JSON.stringify(res.user));
          showToast(`Welcome, ${res.user.name}!`);
          showDashboard(res.user);
        }
      } catch (err) {
        showToast(err.message || "Login failed", "danger");
      }

      btn.textContent = "Access Dashboard";
      btn.disabled = false;
    });

    // ===== SHOW DASHBOARD =====
    async function showDashboard(user) {
      loginScreen.style.display = "none";
      adminLayout.style.display = "flex";

      // Topbar user
      const topbarUser = document.getElementById("admin-topbar-user");
      if (topbarUser) {
        topbarUser.innerHTML = `<span style="font-weight:600; color:var(--primary);">👤 ${user.name}</span>`;
      }

      setupSidebar();
      setupLogout();

      // Load all data
      await loadAllData();
    }



    async function loadAllData() {
      try {
        const [products, orders, customers] = await Promise.all([
          apiFetch("/products"),
          apiFetch("/orders"),
          apiFetch("/admin/customers"),
        ]);
        allProducts = products;
        allOrders = orders;
        allCustomers = customers;

        renderDashboard();
        renderOrders();
        renderProducts();
        renderCustomers();
        populateNotifCustomers();
        loadNotifHistory();
      } catch (err) {
        showToast("Failed to load data: " + err.message, "danger");
      }
    }

    // ===== SIDEBAR NAVIGATION =====
    function setupSidebar() {
      const links = document.querySelectorAll(".sidebar-link[data-section]");
      const sections = document.querySelectorAll(".admin-section");
      const pageTitle = document.getElementById("admin-page-title");
      const sidebar = document.getElementById("admin-sidebar");

      const titles = {
        dashboard: "Dashboard",
        orders: "All Orders",
        products: "Products",
        customers: "All Customers",
        notifications: "Send Notification",
      };

      links.forEach((link) => {
        link.addEventListener("click", () => {
          const section = link.dataset.section;

          links.forEach((l) => l.classList.remove("active"));
          link.classList.add("active");

          sections.forEach((s) => s.classList.remove("active"));
          const target = document.getElementById("section-" + section);
          if (target) target.classList.add("active");

          if (pageTitle) pageTitle.textContent = titles[section] || "Dashboard";
          if (sidebar) sidebar.classList.remove("open");
        });
      });

      // Mobile toggle
      const toggleBtn = document.getElementById("sidebar-toggle");
      if (toggleBtn && sidebar) {
        toggleBtn.addEventListener("click", () => sidebar.classList.toggle("open"));
      }
    }

    function setupLogout() {
      const logoutBtn = document.getElementById("admin-logout-btn");
      if (logoutBtn) {
        logoutBtn.addEventListener("click", () => {
          localStorage.removeItem("marwari_admin_session");
          location.reload();
        });
      }
    }

    // ===== 1. DASHBOARD =====
    function renderDashboard() {
      const revenue = allOrders.reduce((s, o) => s + (o.total || 0), 0);
      setText("dash-stat-revenue", `₹${revenue.toLocaleString("en-IN")}`);
      setText("dash-stat-products", allProducts.length);
      setText("dash-stat-orders", allOrders.length);
      setText("dash-stat-customers", allCustomers.length);

      renderRevenueChart();
      renderOrderStatusChart();
    }

    function renderRevenueChart() {
      const container = document.getElementById("revenue-chart");
      if (!container) return;
      const monthData = {};
      const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

      allOrders.forEach((o) => {
        const d = new Date(o.date);
        const key = `${months[d.getMonth()]} ${d.getFullYear().toString().slice(-2)}`;
        monthData[key] = (monthData[key] || 0) + (o.total || 0);
      });

      const entries = Object.entries(monthData).slice(-7);
      if (entries.length === 0) {
        container.innerHTML = '<p style="color:var(--text-muted); text-align:center; padding:3rem;">No revenue data yet</p>';
        return;
      }

      const maxVal = Math.max(...entries.map((e) => e[1]), 1);
      container.innerHTML = entries
        .map(([label, val]) => {
          const h = Math.max((val / maxVal) * 200, 8);
          return `<div class="chart-bar-group"><span class="chart-bar-value">₹${(val / 1000).toFixed(1)}k</span><div class="chart-bar" style="height:${h}px;"></div><span class="chart-bar-label">${label}</span></div>`;
        })
        .join("");
    }

    function renderOrderStatusChart() {
      const container = document.getElementById("order-status-chart");
      if (!container) return;
      const pending = allOrders.filter((o) => o.status === "Pending").length;
      const completed = allOrders.filter((o) => o.status === "Completed").length;
      const total = allOrders.length || 1;
      const r = 60, c = 2 * Math.PI * r;
      const cDash = (completed / total) * c;
      const pDash = (pending / total) * c;

      container.innerHTML = `<div class="donut-chart-wrapper">
        <svg width="160" height="160" viewBox="0 0 160 160">
          <circle cx="80" cy="80" r="${r}" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="20"/>
          <circle cx="80" cy="80" r="${r}" fill="none" stroke="#10b981" stroke-width="20" stroke-dasharray="${cDash} ${c - cDash}" stroke-dashoffset="0" transform="rotate(-90 80 80)" stroke-linecap="round"/>
          <circle cx="80" cy="80" r="${r}" fill="none" stroke="#fbbf24" stroke-width="20" stroke-dasharray="${pDash} ${c - pDash}" stroke-dashoffset="${-cDash}" transform="rotate(-90 80 80)" stroke-linecap="round"/>
          <text x="80" y="78" text-anchor="middle" fill="var(--text-primary)" font-size="22" font-weight="700">${total}</text>
          <text x="80" y="98" text-anchor="middle" fill="var(--text-muted)" font-size="11">Total</text>
        </svg>
        <div class="donut-legend">
          <div class="donut-legend-item"><div class="donut-legend-dot" style="background:#10b981;"></div> Completed (${completed})</div>
          <div class="donut-legend-item"><div class="donut-legend-dot" style="background:#fbbf24;"></div> Pending (${pending})</div>
        </div>
      </div>`;
    }

    // ===== 2. ORDERS =====
    function renderOrders() {
      const tbody = document.getElementById("dash-orders-table");
      if (!tbody) return;

      if (allOrders.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding:2rem;">No orders yet</td></tr>';
        return;
      }

      tbody.innerHTML = allOrders
        .map((o) => {
          const items = Array.isArray(o.items) ? o.items.map((i) => `${i.name} ×${i.quantity}`).join(", ") : "—";
          const date = new Date(o.date).toLocaleDateString("en-IN", { day: "2-digit", month: "short", year: "2-digit" });
          const statusClass = o.status === "Completed" ? "completed" : "pending";
          const clickAttr = o.status === "Pending" ? `onclick="window.__updateOrder('${o.id}')"` : "";

          return `<tr>
            <td style="font-family:monospace; font-size:0.75rem;">#${(o.id || "").slice(-6).toUpperCase()}</td>
            <td>${o.user_email || o.userEmail || "—"}</td>
            <td style="font-size:0.8rem; max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${items}</td>
            <td style="font-weight:600;">₹${(o.total || 0).toLocaleString("en-IN")}</td>
            <td style="font-size:0.8rem;">${date}</td>
            <td><span class="status-badge ${statusClass}" ${clickAttr}>${o.status}</span></td>
          </tr>`;
        })
        .join("");
    }

    window.__updateOrder = async function (orderId) {
      try {
        await apiFetch(`/orders/${orderId}/status`, {
          method: "POST",
          body: JSON.stringify({ status: "Completed" }),
        });
        showToast("Order marked as Completed!");
        await loadAllData();
      } catch (err) {
        showToast(err.message, "danger");
      }
    };

    // ===== 3. PRODUCTS =====
    function renderProducts() {
      const tbody = document.getElementById("dash-products-table");
      if (!tbody) return;

      if (allProducts.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; color:var(--text-muted); padding:2rem;">No products yet</td></tr>';
        return;
      }

      tbody.innerHTML = allProducts
        .map(
          (p) => `<tr>
          <td><div style="display:flex; align-items:center; gap:0.75rem;">
            <img src="${p.image}" alt="${p.name}" style="width:40px; height:40px; border-radius:8px; object-fit:cover;">
            <span style="font-weight:500;">${p.name}</span>
          </div></td>
          <td><span style="background:var(--primary-glow); color:var(--primary); padding:0.2rem 0.6rem; border-radius:20px; font-size:0.75rem; font-weight:600;">${p.category}</span></td>
          <td style="font-weight:600;">₹${parseFloat(p.price).toLocaleString("en-IN")}</td>
          <td><button onclick="window.__deleteProduct('${p.id}')" style="background:var(--danger); color:#fff; border:none; padding:0.4rem 0.8rem; border-radius:8px; cursor:pointer; font-size:0.75rem; font-weight:600;">Delete</button></td>
        </tr>`
        )
        .join("");
    }

    window.__deleteProduct = async function (id) {
      if (!confirm("Delete this product?")) return;
      try {
        await apiFetch(`/products/${id}`, { method: "DELETE" });
        showToast("Product deleted");
        await loadAllData();
      } catch (err) {
        showToast(err.message, "danger");
      }
    };

    // Add Product
    const addForm = document.getElementById("admin-add-product-form");
    if (addForm) {
      addForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const btn = addForm.querySelector("button[type='submit']");
        btn.textContent = "Publishing...";
        btn.disabled = true;

        const product = {
          name: document.getElementById("admin-pname").value.trim(),
          category: document.getElementById("admin-pcategory").value,
          price: parseFloat(document.getElementById("admin-pprice").value),
          description: document.getElementById("admin-pdesc").value.trim(),
          image: document.getElementById("admin-pimage").value.trim() || "https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=600&q=80",
          badge: document.getElementById("admin-pbadge").value.trim() || null,
        };

        try {
          await apiFetch("/products", { method: "POST", body: JSON.stringify(product) });
          showToast("Product published!");
          addForm.reset();
          await loadAllData();
        } catch (err) {
          showToast(err.message, "danger");
        }

        btn.textContent = "Publish Product";
        btn.disabled = false;
      });
    }

    // ===== 4. CUSTOMERS =====
    function renderCustomers() {
      const tbody = document.getElementById("dash-customers-table");
      if (!tbody) return;

      if (allCustomers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:var(--text-muted); padding:2rem;">No customers yet</td></tr>';
        return;
      }

      tbody.innerHTML = allCustomers
        .map((c) => {
          const regDate = new Date(c.registered).toLocaleDateString("en-IN", { day: "2-digit", month: "short", year: "2-digit" });
          return `<tr>
            <td style="font-weight:500;">${c.name}</td>
            <td style="font-size:0.85rem;">${c.email}</td>
            <td>${c.phone || "—"}</td>
            <td style="font-weight:600; text-align:center;">${c.orders || 0}</td>
            <td style="font-size:0.8rem;">${regDate}</td>
          </tr>`;
        })
        .join("");
    }

    // ===== 5. SEND NOTIFICATION =====
    function populateNotifCustomers() {
      const select = document.getElementById("notif-customer-select");
      if (!select) return;

      // Keep the "All Customers" option, clear the rest
      select.innerHTML = '<option value="all">📢 All Customers</option>';
      allCustomers.forEach((c) => {
        select.innerHTML += `<option value="${c.email}">${c.name} (${c.email})</option>`;
      });
    }

    // Notification Form
    const notifForm = document.getElementById("admin-notif-form");
    if (notifForm) {
      notifForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const btn = notifForm.querySelector("button[type='submit']");
        const customerSelect = document.getElementById("notif-customer-select");
        const subject = document.getElementById("notif-subject").value.trim();
        const message = document.getElementById("notif-message").value.trim();
        const target = customerSelect.value;

        if (!subject || !message) {
          showToast("Please fill in subject and message", "danger");
          return;
        }

        btn.textContent = "Sending...";
        btn.disabled = true;

        try {
          await apiFetch("/notifications/send", {
            method: "POST",
            body: JSON.stringify({ target, subject, message }),
          });
          showToast("Notification sent successfully!");
          notifForm.reset();
          populateNotifCustomers(); // re-populate
          loadNotifHistory();
        } catch (err) {
          showToast(err.message, "danger");
        }

        btn.textContent = "Send Notification";
        btn.disabled = false;
      });
    }

    async function loadNotifHistory() {
      const container = document.getElementById("notif-history");
      if (!container) return;

      try {
        const notifs = await apiFetch("/notifications");
        if (!notifs || notifs.length === 0) {
          container.innerHTML = '<p style="color:var(--text-muted); font-size:0.85rem;">No notifications sent yet.</p>';
          return;
        }

        container.innerHTML = notifs
          .slice(0, 20)
          .map((n) => {
            const date = new Date(n.date).toLocaleDateString("en-IN", { day: "2-digit", month: "short", year: "2-digit", hour: "2-digit", minute: "2-digit" });
            return `<div class="notif-history-item">
              <h4>📩 ${n.subject}</h4>
              <p>${n.message}</p>
              <div class="notif-meta">To: ${n.target === "all" ? "All Customers" : n.target} · ${date}</div>
            </div>`;
          })
          .join("");
      } catch (err) {
        container.innerHTML = '<p style="color:var(--text-muted); font-size:0.85rem;">Could not load history.</p>';
      }
    }

    // ===== Utility =====
    function setText(id, text) {
      const el = document.getElementById(id);
      if (el) el.textContent = text;
    }
  });
})();
