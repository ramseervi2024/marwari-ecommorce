// Marwari E-Commerce - WordPress API Logic & Application State

class WordPressAppState {
  constructor() {
    // Read localized WordPress settings
    const hasWpSettings = typeof wpApiSettings !== 'undefined';
    this.apiRoot = hasWpSettings ? wpApiSettings.root + 'marwari-ecom/v1' : '';
    this.nonce = hasWpSettings ? wpApiSettings.nonce : '';

    // In-memory active states
    this.products = [];
    this.orders = [];
    // Restore user session from localStorage so UI shows login state immediately on refresh
    this.currentUser = this.loadLocalStorage("marwari_session", null);

    // Cart is maintained in client LocalStorage until checkout
    this.cart = this.loadLocalStorage("marwari_cart", []);
  }

  loadLocalStorage(key, defaultValue) {
    const data = localStorage.getItem(key);
    return data ? JSON.parse(data) : defaultValue;
  }

  saveLocalStorage(key, data) {
    localStorage.setItem(key, JSON.stringify(data));
  }

  // General Fetch API Helper
  async apiFetch(endpoint, options = {}) {
    const url = `${this.apiRoot}${endpoint}`;

    const headers = {
      'Content-Type': 'application/json',
      ...options.headers
    };

    if (this.nonce) {
      headers['X-WP-Nonce'] = this.nonce;
    }

    // Include credentials (cookies) to support WordPress native cookie authentication
    const fetchOptions = {
      credentials: 'same-origin',
      ...options,
      headers
    };

    try {
      const response = await fetch(url, fetchOptions);
      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || `API Error (Status ${response.status})`);
      }
      return data;
    } catch (error) {
      console.error(`WordPress API Fetch Error on ${endpoint}:`, error);
      throw error;
    }
  }

  // Load initial configurations from WordPress
  async initSession() {
    try {
      // 1. Always fetch products
      this.products = await this.apiFetch('/products');
    } catch (e) {
      console.warn("Failed to load products:", e.message);
    }

    // 2. If we have a cached session, fetch orders silently
    if (this.currentUser) {
      try {
        this.orders = await this.apiFetch('/orders');
      } catch (e) {
        console.warn("Failed to load orders (session may have expired):", e.message);
      }
    }

    // 3. Silently verify session with server in background — never force logout
    //    Only update localStorage if server confirms a valid session
    try {
      const serverUser = await this.apiFetch('/auth/current');
      if (serverUser) {
        this.currentUser = serverUser;
        this.saveLocalStorage("marwari_session", serverUser);
        // If we didn't already load orders, load them now
        if (this.orders.length === 0) {
          this.orders = await this.apiFetch('/orders');
        }
      }
      // NOTE: if serverUser is null, we do NOT clear the session.
      // Session is ONLY cleared by the logout button or browser data clear.
    } catch (e) {
      // Server check failed silently — keep the cached localStorage session
      console.warn("Background session check failed, keeping cached session.", e.message);
    }
  }

  // Cart Local Operations
  addToCart(productId, quantity = 1) {
    const product = this.products.find(p => p.id === productId);
    if (!product) return false;

    const existingItem = this.cart.find(item => item.product.id === productId);
    if (existingItem) {
      existingItem.quantity += quantity;
    } else {
      this.cart.push({ product, quantity });
    }
    this.saveLocalStorage("marwari_cart", this.cart);
    return true;
  }

  updateCartQuantity(productId, quantity) {
    const itemIndex = this.cart.findIndex(item => item.product.id === productId);
    if (itemIndex > -1) {
      if (quantity <= 0) {
        this.cart.splice(itemIndex, 1);
      } else {
        this.cart[itemIndex].quantity = quantity;
      }
      this.saveLocalStorage("marwari_cart", this.cart);
      return true;
    }
    return false;
  }

  removeFromCart(productId) {
    this.cart = this.cart.filter(item => item.product.id !== productId);
    this.saveLocalStorage("marwari_cart", this.cart);
  }

  clearCart() {
    this.cart = [];
    this.saveLocalStorage("marwari_cart", this.cart);
  }

  getCartTotal() {
    return this.cart.reduce((total, item) => total + (item.product.price * item.quantity), 0);
  }

  getCartCount() {
    return this.cart.reduce((count, item) => count + item.quantity, 0);
  }

  // Authentication REST Operations — Email + OTP Code (no passwords)
  async loginWithEmail(email) {
    try {
      const res = await this.apiFetch('/auth/login', {
        method: 'POST',
        body: JSON.stringify({ email })
      });
      return res;
    } catch (e) {
      return { success: false, message: e.message };
    }
  }

  async registerUser(name, email, phone) {
    try {
      const res = await this.apiFetch('/auth/register', {
        method: 'POST',
        body: JSON.stringify({ name, email, phone })
      });
      return res;
    } catch (e) {
      return { success: false, message: e.message };
    }
  }

  async verifyEmailCode(email, code) {
    try {
      const res = await this.apiFetch('/auth/verify-email', {
        method: 'POST',
        body: JSON.stringify({ email, code })
      });
      if (res.success) {
        this.currentUser = res.user;
        this.saveLocalStorage("marwari_session", res.user);
        if (res.nonce) this.nonce = res.nonce;
        this.orders = await this.apiFetch('/orders');
        return { success: true, user: this.currentUser };
      }
      return { success: false, message: "Verification failed" };
    } catch (e) {
      return { success: false, message: e.message };
    }
  }

  async resendCode(email) {
    try {
      const res = await this.apiFetch('/auth/resend-code', {
        method: 'POST',
        body: JSON.stringify({ email })
      });
      return res;
    } catch (e) {
      return { success: false, message: e.message };
    }
  }

  async logout() {
    try {
      await this.apiFetch('/auth/logout', { method: 'POST' });
    } catch (e) {
      console.warn("Logout request failed, cleaning local state anyway:", e.message);
    }
    this.currentUser = null;
    this.orders = [];
    localStorage.removeItem("marwari_session"); // Clear persisted session
    this.clearCart();
  }

  // Products Database Modifications (Admin)
  async addProduct(product) {
    const res = await this.apiFetch('/products', {
      method: 'POST',
      body: JSON.stringify(product)
    });
    this.products.push(res);
    return res;
  }

  async deleteProduct(productId) {
    await this.apiFetch(`/products/${productId}`, {
      method: 'DELETE'
    });
    this.products = this.products.filter(p => p.id !== productId);
  }

  // Orders REST Actions
  async submitCheckoutOrder(shippingDetails) {
    const res = await this.apiFetch('/orders', {
      method: 'POST',
      body: JSON.stringify({
        items: this.cart,
        total: this.getCartTotal(),
        shippingDetails
      })
    });
    this.orders.unshift(res);
    this.clearCart();
    return res;
  }

  async updateOrderStatus(orderId, status) {
    const res = await this.apiFetch(`/orders/${orderId}/status`, {
      method: 'POST',
      body: JSON.stringify({ status })
    });
    const order = this.orders.find(o => o.id === orderId);
    if (order) {
      order.status = res.status;
    }
    return res;
  }
}

const app = new WordPressAppState();

// UI Initialization & Controller
document.addEventListener("DOMContentLoaded", async () => {
  initTheme();

  // Immediately render navbar from cached localStorage session (no flicker on refresh)
  updateNavBarState();

  // Show loading skeleton placeholders
  showStorefrontLoading();

  // Initialize DB data and verify server session
  await app.initSession();

  // Re-render navbar and storefront with confirmed server state
  renderStorefront();
  updateNavBarState();
  setupEventListeners();
  initCustomerNotifications();

  // Admin Mode: If this is the /admin/ page, handle admin-specific behavior
  const pageMode = (typeof wpApiSettings !== 'undefined' && wpApiSettings.pageMode) ? wpApiSettings.pageMode : 'shop';
  if (pageMode === 'admin') {
    if (app.currentUser && app.currentUser.role === 'admin') {
      // Already logged in as admin — go directly to dashboard
      switchView("admin");
    } else if (app.currentUser) {
      // Logged in but not admin
      showToast("Access denied. Admin credentials required.", "danger");
    } else {
      // Not logged in — auto-open login modal
      openModal("auth-modal");
    }
  }
});

function initTheme() {
  const isLight = localStorage.getItem("light_mode") === "true";
  const icon = document.getElementById("theme-icon");
  if (!icon) return;

  if (isLight) {
    document.body.classList.add("light-mode");
    icon.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
    `;
  } else {
    document.body.classList.remove("light-mode");
    icon.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
    `;
  }
}

function toggleTheme() {
  const body = document.body;
  body.classList.toggle("light-mode");
  const isLight = body.classList.contains("light-mode");
  localStorage.setItem("light_mode", isLight);
  initTheme();
  showToast(isLight ? "Switched to Royal Light Theme" : "Switched to Luxury Dark Theme");
}

// Show animated skeletons while retrieving database entries
function showStorefrontLoading() {
  const container = document.getElementById("products-container");
  if (!container) return;

  container.innerHTML = "";
  for (let i = 0; i < 4; i++) {
    const card = document.createElement("div");
    card.className = "product-card skeleton-card";
    card.style.opacity = "0.6";
    card.innerHTML = `
      <div style="width: 100%; padding-top: 85%; background: var(--border-color); animation: pulse 1.5s infinite;"></div>
      <div style="padding: 1.5rem;">
        <div style="width: 40%; height: 12px; background: var(--border-color); margin-bottom: 0.8rem;"></div>
        <div style="width: 85%; height: 20px; background: var(--border-color); margin-bottom: 0.5rem;"></div>
        <div style="width: 100%; height: 35px; background: var(--border-color); margin-bottom: 1.2rem;"></div>
        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 1rem;">
          <div style="width: 30%; height: 20px; background: var(--border-color);"></div>
          <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--border-color);"></div>
        </div>
      </div>
    `;
    container.appendChild(card);
  }
}

// Render Products catalog
function renderStorefront(category = "All", query = "") {
  const container = document.getElementById("products-container");
  if (!container) return;

  container.innerHTML = "";

  let filtered = app.products;
  if (category !== "All") {
    filtered = filtered.filter(p => p.category === category);
  }
  if (query) {
    const q = query.toLowerCase();
    filtered = filtered.filter(p => p.name.toLowerCase().includes(q) || p.description.toLowerCase().includes(q));
  }

  if (filtered.length === 0) {
    container.innerHTML = `
      <div style="grid-column: 1/-1; text-align: center; padding: 4rem 1rem; color: var(--text-muted);">
        <svg style="margin: 0 auto 1.5rem; display: block;" xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <h3 style="font-family: var(--font-body); font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">No products found</h3>
        <p>Try searching another heritage category or title.</p>
      </div>
    `;
    return;
  }

  filtered.forEach(p => {
    const card = document.createElement("div");
    card.className = "product-card";
    card.innerHTML = `
      <div class="product-image-container">
        ${p.badge ? `<span class="product-badge">${p.badge}</span>` : ''}
        <img src="${p.image}" alt="${p.name}" loading="lazy">
      </div>
      <div class="product-info">
        <span class="product-category">${p.category}</span>
        <h3 class="product-name" title="${p.name}">${p.name}</h3>
        <p class="product-description-snippet">${p.description}</p>
        <div class="product-footer">
          <span class="product-price">₹${parseFloat(p.price).toLocaleString("en-IN")}</span>
          <button class="add-cart-btn" data-id="${p.id}" title="Quick Add to Cart">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5v14"/></svg>
          </button>
        </div>
      </div>
    `;

    // Add Click listener to open detail modal
    card.addEventListener("click", (e) => {
      if (e.target.closest(".add-cart-btn")) {
        e.stopPropagation();
        const id = e.target.closest(".add-cart-btn").dataset.id;
        app.addToCart(id);
        updateCartUI();
        showToast("Added item to your Cart");
        return;
      }
      openProductDetailModal(p);
    });

    container.appendChild(card);
  });
}

// Update Cart Badge and Drawer items
function updateCartUI() {
  const count = app.getCartCount();
  const badges = document.querySelectorAll(".cart-count");
  badges.forEach(b => {
    b.innerText = count;
    b.style.display = count > 0 ? "flex" : "none";
  });

  const drawerBody = document.getElementById("cart-drawer-list");
  if (!drawerBody) return;

  if (app.cart.length === 0) {
    drawerBody.innerHTML = `
      <div class="empty-cart-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
        <p style="font-weight: 500;">Your shopping cart is empty</p>
        <p style="font-size: 0.85rem; margin-top: 0.25rem;">Start exploring the royal items of Marwar!</p>
      </div>
    `;
    document.getElementById("cart-subtotal").innerText = "₹0";
    document.getElementById("cart-total").innerText = "₹0";
    return;
  }

  drawerBody.innerHTML = "";
  app.cart.forEach(item => {
    const itemEl = document.createElement("div");
    itemEl.className = "cart-item";
    itemEl.innerHTML = `
      <img class="cart-item-img" src="${item.product.image}" alt="${item.product.name}">
      <div class="cart-item-info">
        <h4 class="cart-item-name">${item.product.name}</h4>
        <span class="cart-item-price">₹${parseFloat(item.product.price).toLocaleString("en-IN")}</span>
        <div class="cart-item-actions">
          <div class="cart-item-qty">
            <button class="cart-item-qty-btn decrease" data-id="${item.product.id}">-</button>
            <span class="cart-item-qty-val">${item.quantity}</span>
            <button class="cart-item-qty-btn increase" data-id="${item.product.id}">+</button>
          </div>
          <button class="cart-item-remove" data-id="${item.product.id}">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2M10 11v6M14 11v6"/></svg>
            Remove
          </button>
        </div>
      </div>
    `;

    itemEl.querySelector(".decrease").addEventListener("click", () => {
      app.updateCartQuantity(item.product.id, item.quantity - 1);
      updateCartUI();
    });

    itemEl.querySelector(".increase").addEventListener("click", () => {
      app.updateCartQuantity(item.product.id, item.quantity + 1);
      updateCartUI();
    });

    itemEl.querySelector(".cart-item-remove").addEventListener("click", () => {
      app.removeFromCart(item.product.id);
      updateCartUI();
      showToast("Removed item from cart");
    });

    drawerBody.appendChild(itemEl);
  });

  const total = app.getCartTotal();
  document.getElementById("cart-subtotal").innerText = `₹${total.toLocaleString("en-IN")}`;
  document.getElementById("cart-total").innerText = `₹${total.toLocaleString("en-IN")}`;
}

// Update login state trigger menus
function updateNavBarState() {
  const profileContainer = document.getElementById("user-menu-container");
  if (!profileContainer) return;

  if (app.currentUser) {
    profileContainer.innerHTML = `
      <button class="btn-primary" id="profile-menu-trigger">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span>${app.currentUser.name}</span>
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
      </button>
      <div class="profile-dropdown" id="profile-dropdown-menu">
        <div class="dropdown-header">
          <h4>${app.currentUser.name}</h4>
          <p>${app.currentUser.email}</p>
        </div>
        ${app.currentUser.role === 'admin' ? `
          <button class="dropdown-item" id="admin-view-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
            Admin Dashboard
          </button>
        ` : `
          <button class="dropdown-item" id="order-history-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Order History
          </button>
        `}
        <button class="dropdown-item" id="back-to-shop-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7M5 12h14"/></svg>
          Browse Store
        </button>
        <button class="dropdown-item danger" id="logout-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
          Logout Session
        </button>
      </div>
    `;

    const trigger = document.getElementById("profile-menu-trigger");
    const menu = document.getElementById("profile-dropdown-menu");
    trigger.addEventListener("click", (e) => {
      e.stopPropagation();
      menu.classList.toggle("active");
    });

    document.getElementById("logout-btn").addEventListener("click", async () => {
      showLoadingButton(document.getElementById("logout-btn"), "Logging out...");
      await app.logout();
      showToast("Logged out successfully");
      updateNavBarState();
      switchView("shop");
    });

    const adminViewBtn = document.getElementById("admin-view-btn");
    if (adminViewBtn) {
      adminViewBtn.addEventListener("click", () => {
        switchView("admin");
        menu.classList.remove("active");
      });
    }

    const orderHistoryBtn = document.getElementById("order-history-btn");
    if (orderHistoryBtn) {
      orderHistoryBtn.addEventListener("click", () => {
        switchView("orders");
        menu.classList.remove("active");
      });
    }

    document.getElementById("back-to-shop-btn").addEventListener("click", () => {
      switchView("shop");
      menu.classList.remove("active");
    });

  } else {
    profileContainer.innerHTML = `
      <button class="btn-primary" id="open-auth-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
        <span>Login</span>
      </button>
    `;

    document.getElementById("open-auth-btn").addEventListener("click", () => {
      openModal("auth-modal");
    });
  }
  refreshCustomerNotifications();
}

// Switch views
function switchView(view) {
  const heroSection = document.getElementById("hero-section");
  const catSection = document.getElementById("categories-section");
  const shopSection = document.getElementById("shop-section");
  const adminView = document.getElementById("admin-view");
  const ordersView = document.getElementById("orders-view");

  heroSection.style.display = "none";
  catSection.style.display = "none";
  shopSection.style.display = "none";
  adminView.classList.remove("active");
  ordersView.classList.remove("active");

  if (view === "shop") {
    heroSection.style.display = "block";
    catSection.style.display = "block";
    shopSection.style.display = "block";
    renderStorefront();
  } else if (view === "admin") {
    if (!app.currentUser || app.currentUser.role !== "admin") {
      showToast("Access Denied: Admins Only", "danger");
      switchView("shop");
      return;
    }
    adminView.classList.add("active");
    renderAdminDashboard();
  } else if (view === "orders") {
    if (!app.currentUser) {
      showToast("Please log in to view orders", "danger");
      switchView("shop");
      return;
    }
    ordersView.classList.add("active");
    renderOrdersHistory();
  }
}

// Global Loading Button Helper
function showLoadingButton(btn, text = "Loading...") {
  btn.disabled = true;
  btn.dataset.originalHtml = btn.innerHTML;
  btn.innerHTML = `<span class="loading-spinner"></span> ${text}`;
}

function restoreButton(btn) {
  btn.disabled = false;
  if (btn.dataset.originalHtml) {
    btn.innerHTML = btn.dataset.originalHtml;
  }
}

// Modal actions
function openModal(modalId) {
  const overlay = document.getElementById("modal-overlay-container");
  const modals = document.querySelectorAll(".modal-content");

  modals.forEach(m => m.style.display = "none");
  const target = document.getElementById(modalId);
  if (target) {
    target.style.display = target.classList.contains("product-detail-modal-layout") ? "flex" : "block";
    overlay.classList.add("active");
  }
}

function closeModal() {
  document.getElementById("modal-overlay-container").classList.remove("active");
}

// Product Details Modal
function openProductDetailModal(product) {
  const detailModal = document.getElementById("product-detail-modal");
  if (!detailModal) return;

  detailModal.innerHTML = `
    <button class="modal-close-btn" onclick="closeModal()">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
    </button>
    <div class="detail-img-pane">
      <img src="${product.image}" alt="${product.name}">
    </div>
    <div class="detail-info-pane">
      <span class="detail-category">${product.category}</span>
      <h2 class="detail-name">${product.name}</h2>
      <div class="detail-price">
        <span>₹${parseFloat(product.price).toLocaleString("en-IN")}</span>
        ${product.badge ? `<span class="product-badge" style="position:static;">${product.badge}</span>` : ''}
      </div>
      <p class="detail-desc">${product.description}</p>
      <div class="detail-actions">
        <div class="quantity-control">
          <button class="quantity-btn dec-btn">-</button>
          <span class="quantity-value">1</span>
          <button class="quantity-btn inc-btn">+</button>
        </div>
        <button class="btn-primary add-detail-cart" style="flex-grow: 1; justify-content: center;">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
          Add to Bag
        </button>
      </div>
    </div>
  `;

  let qty = 1;
  const qtyVal = detailModal.querySelector(".quantity-value");
  detailModal.querySelector(".dec-btn").addEventListener("click", () => {
    if (qty > 1) {
      qty--;
      qtyVal.innerText = qty;
    }
  });

  detailModal.querySelector(".inc-btn").addEventListener("click", () => {
    qty++;
    qtyVal.innerText = qty;
  });

  detailModal.querySelector(".add-detail-cart").addEventListener("click", () => {
    app.addToCart(product.id, qty);
    updateCartUI();
    closeModal();
    showToast(`Added ${qty} item(s) to Cart`);
  });

  openModal("product-detail-modal");
}

// Admin Panel Dashboard rendering
function renderAdminDashboard() {
  const totalSales = app.orders.reduce((total, o) => total + o.total, 0);
  const productsCount = app.products.length;
  const totalOrders = app.orders.length;

  document.getElementById("stat-sales").innerText = `₹${totalSales.toLocaleString("en-IN")}`;
  document.getElementById("stat-products").innerText = productsCount;
  document.getElementById("stat-orders").innerText = totalOrders;

  const prodTableBody = document.getElementById("admin-products-table");
  if (prodTableBody) {
    prodTableBody.innerHTML = "";
    app.products.forEach(p => {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>
          <div class="admin-prod-cell">
            <img src="${p.image}" alt="${p.name}">
            <span>${p.name}</span>
          </div>
        </td>
        <td>${p.category}</td>
        <td>₹${parseFloat(p.price).toLocaleString("en-IN")}</td>
        <td>
          <button class="action-icon-btn delete" data-id="${p.id}" title="Delete Product">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2M10 11v6M14 11v6"/></svg>
          </button>
        </td>
      `;

      row.querySelector(".delete").addEventListener("click", async (e) => {
        const btn = e.currentTarget;
        if (confirm(`Are you sure you want to delete "${p.name}"?`)) {
          showLoadingButton(btn, "");
          try {
            await app.deleteProduct(p.id);
            showToast("Product deleted successfully");
            renderAdminDashboard();
          } catch (err) {
            showToast(err.message, "danger");
            restoreButton(btn);
          }
        }
      });

      prodTableBody.appendChild(row);
    });
  }

  const ordersTableBody = document.getElementById("admin-orders-table");
  if (ordersTableBody) {
    ordersTableBody.innerHTML = "";
    if (app.orders.length === 0) {
      ordersTableBody.innerHTML = `<tr><td colspan="5" style="text-align:center; color:var(--text-muted); padding:2rem;">No orders placed yet.</td></tr>`;
      return;
    }

    app.orders.forEach(o => {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>#${o.id.slice(-6).toUpperCase()}</td>
        <td>
          <div style="font-weight:600;">${o.shippingDetails.name}</div>
          <div style="font-size:0.75rem; color:var(--text-secondary);">${o.shippingDetails.phone}</div>
        </td>
        <td>${o.items.map(item => `${item.product.name} (x${item.quantity})`).join(", ")}</td>
        <td>₹${parseFloat(o.total).toLocaleString("en-IN")}</td>
        <td>
          <button class="badge-status ${o.status.toLowerCase()}" data-id="${o.id}">
            ${o.status}
          </button>
        </td>
      `;

      const statusBtn = row.querySelector(".badge-status");
      statusBtn.addEventListener("click", async () => {
        if (o.status === "Pending") {
          showLoadingButton(statusBtn, "");
          try {
            await app.updateOrderStatus(o.id, "Completed");
            showToast("Order status updated to Completed");
            renderAdminDashboard();
          } catch (err) {
            showToast(err.message, "danger");
            restoreButton(statusBtn);
          }
        }
      });

      ordersTableBody.appendChild(row);
    });
  }
}

// User order histories
function renderOrdersHistory() {
  const container = document.getElementById("orders-history-list");
  if (!container) return;

  if (app.orders.length === 0) {
    container.innerHTML = `
      <div style="text-align:center; padding: 4rem 1rem; background: var(--bg-card); border-radius:20px; border:1px solid var(--border-color); color:var(--text-muted);">
        <svg style="margin: 0 auto 1rem; display:block;" xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        <h3 style="font-family: var(--font-body); font-weight:600; margin-bottom:0.5rem; color:var(--text-primary);">No Orders Found</h3>
        <p>You haven't placed any orders yet. Browse our storefront to purchase products!</p>
      </div>
    `;
    return;
  }

  container.innerHTML = "";
  app.orders.forEach(o => {
    const card = document.createElement("div");
    card.className = "order-history-card";
    card.innerHTML = `
      <div class="order-history-header">
        <div>
          <h4>Order ID: #${o.id.slice(-6).toUpperCase()}</h4>
          <p>Placed on: ${new Date(o.date).toLocaleDateString("en-IN", { year: 'numeric', month: 'short', day: 'numeric' })}</p>
        </div>
        <div>
          <span class="badge-status ${o.status.toLowerCase()}">${o.status}</span>
        </div>
      </div>
      <div style="display:flex; flex-direction:column; gap:0.75rem;">
        ${o.items.map(item => `
          <div class="order-history-item">
            <div style="display:flex; align-items:center; gap:0.75rem;">
              <img src="${item.product.image}" alt="${item.product.name}" style="width:48px; height:48px; border-radius:6px; object-fit:cover; border: 1px solid var(--border-color);">
              <div>
                <div style="font-weight:600; font-size:0.9rem;">${item.product.name}</div>
                <div style="font-size:0.8rem; color:var(--text-secondary);">Qty: ${item.quantity} x ₹${parseFloat(item.product.price).toLocaleString("en-IN")}</div>
              </div>
            </div>
            <div style="font-weight:700;">₹${(item.product.price * item.quantity).toLocaleString("en-IN")}</div>
          </div>
        `).join("")}
      </div>
      <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
        <span style="font-size:0.85rem; color:var(--text-secondary);">Shipment Address: ${o.shippingDetails.address}, ${o.shippingDetails.city}</span>
        <span style="font-weight:800; font-size:1.1rem; color:var(--primary);">Total: ₹${parseFloat(o.total).toLocaleString("en-IN")}</span>
      </div>
    `;
    container.appendChild(card);
  });
}

// Toast Notification popup
function showToast(message, type = "success") {
  const container = document.getElementById("toast-container");
  if (!container) return;

  const toast = document.createElement("div");
  toast.className = `toast ${type}`;

  const icon = type === "success"
    ? `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="${app.currentUser && app.currentUser.role === 'admin' ? 'var(--primary)' : 'var(--success)'}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m22 4-10 10.01-3-3"/></svg>`
    : `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="var(--danger)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>`;

  toast.innerHTML = `
    ${icon}
    <span class="toast-message">${message}</span>
  `;

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.animation = "slideDown 0.3s forwards, fadeOut 0.3s forwards";
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// Customer Notifications logic
let customerNotifs = [];

async function refreshCustomerNotifications() {
  const notifTrigger = document.getElementById("notif-trigger");
  const notifCount = document.getElementById("notif-count");
  if (!notifTrigger) return;

  if (!app.currentUser) {
    notifTrigger.style.display = "none";
    if (notifCount) notifCount.style.display = "none";
    return;
  }

  // Show the bell icon
  notifTrigger.style.display = "inline-block";

  try {
    const notifs = await app.apiFetch("/notifications/my");
    customerNotifs = notifs;

    // Get read notifications list
    let readIds = [];
    try {
      const saved = localStorage.getItem("marwari_read_notifications");
      readIds = saved ? JSON.parse(saved) : [];
    } catch (e) { /* ignore */ }

    // Count unread
    const unread = notifs.filter(n => n.id && !readIds.includes(n.id));

    if (notifCount) {
      if (unread.length > 0) {
        notifCount.textContent = unread.length;
        notifCount.style.display = "flex";
      } else {
        notifCount.style.display = "none";
      }
    }
  } catch (err) {
    console.error("Failed to load customer notifications:", err);
  }
}

function initCustomerNotifications() {
  const notifTrigger = document.getElementById("notif-trigger");
  if (!notifTrigger) return;

  notifTrigger.addEventListener("click", () => {
    openModal("notifications-modal");

    // Mark all as read
    const readIds = customerNotifs.map(n => n.id).filter(Boolean);
    localStorage.setItem("marwari_read_notifications", JSON.stringify(readIds));

    // Reset badge count
    const notifCount = document.getElementById("notif-count");
    if (notifCount) notifCount.style.display = "none";

    // Render list
    const listContainer = document.getElementById("customer-notif-list");
    if (!listContainer) return;

    if (customerNotifs.length === 0) {
      listContainer.innerHTML = `<p style="color: var(--text-secondary); font-size: 0.9rem; text-align: center; padding: 2rem 0;">No notifications yet</p>`;
    } else {
      listContainer.innerHTML = customerNotifs.map(n => {
        const date = new Date(n.date).toLocaleString("en-IN", {
          day: "2-digit",
          month: "short",
          hour: "2-digit",
          minute: "2-digit",
        });
        return `
          <div class="notif-item" style="padding: 0.85rem; border-radius: 12px; background: var(--bg-card); border: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 0.35rem; transition: transform 0.2s ease;">
            <div style="font-weight: 600; color: var(--text-primary); font-size: 0.95rem; display: flex; align-items: center; gap: 0.5rem;">
              <span style="color: var(--primary);">✦</span> ${n.subject}
            </div>
            <div style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.45; white-space: pre-wrap;">${n.message}</div>
            <div style="color: var(--text-muted); font-size: 0.72rem; align-self: flex-end; margin-top: 0.2rem;">${date}</div>
          </div>
        `;
      }).join("");
    }
  });

  // Fetch immediately
  refreshCustomerNotifications();

  // Poll for new notifications every 15 seconds
  setInterval(refreshCustomerNotifications, 15000);
}

// Setup Event Listeners
function setupEventListeners() {
  document.getElementById("theme-toggle").addEventListener("click", toggleTheme);

  const cartTrigger = document.getElementById("cart-trigger");
  const cartOverlay = document.getElementById("cart-drawer-overlay");
  const cartDrawer = document.getElementById("cart-drawer");
  const cartClose = document.getElementById("cart-close");

  cartTrigger.addEventListener("click", () => {
    updateCartUI();
    cartOverlay.style.display = "block";
    setTimeout(() => {
      cartOverlay.classList.add("active");
      cartDrawer.classList.add("active");
    }, 50);
  });

  const closeCartFn = () => {
    cartOverlay.classList.remove("active");
    cartDrawer.classList.remove("active");
    setTimeout(() => {
      cartOverlay.style.display = "none";
    }, 300);
  };

  cartClose.addEventListener("click", closeCartFn);
  cartOverlay.addEventListener("click", closeCartFn);

  const searchInput = document.getElementById("search-input");
  searchInput.addEventListener("input", (e) => {
    const activeTab = document.querySelector(".category-tab.active");
    const category = activeTab ? activeTab.dataset.category : "All";
    renderStorefront(category, e.target.value);
  });

  const catTabs = document.querySelectorAll(".category-tab");
  catTabs.forEach(tab => {
    tab.addEventListener("click", (e) => {
      catTabs.forEach(t => t.classList.remove("active"));
      tab.classList.add("active");

      const category = tab.dataset.category;
      renderStorefront(category, searchInput.value);
    });
  });

  // Auth Modal Tab Switching
  const authTabBtns = document.querySelectorAll(".auth-tab-btn");
  const authLoginForm = document.getElementById("auth-login-form");
  const authRegisterForm = document.getElementById("auth-register-form");
  const authVerifyForm = document.getElementById("auth-verify-form");
  const authAdminForm = document.getElementById("auth-admin-form");

  let pendingVerifyEmail = null; // Track which email is being verified

  authTabBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      authTabBtns.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");

      const tab = btn.dataset.tab;
      authLoginForm.style.display = tab === "login" ? "block" : "none";
      authRegisterForm.style.display = tab === "register" ? "block" : "none";
      authVerifyForm.style.display = "none"; // Always hide verify when switching tabs
      if (authAdminForm) authAdminForm.style.display = tab === "admin-panel" ? "block" : "none";
    });
  });

  // Helper: Show verify screen
  function showVerifyScreen(email) {
    pendingVerifyEmail = email;
    authLoginForm.style.display = "none";
    authRegisterForm.style.display = "none";
    authVerifyForm.style.display = "block";
    document.getElementById("verify-email-hint").textContent = `We sent a 6-digit code to ${email}`;
    document.getElementById("verify-code").value = "";
    document.getElementById("verify-code").focus();
  }

  // Login Form Submit — email only, sends verification code
  authLoginForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const email = document.getElementById("login-email").value.trim();
    const submitBtn = authLoginForm.querySelector(".auth-submit-btn");

    showLoadingButton(submitBtn, "Sending Code...");
    const res = await app.loginWithEmail(email);
    restoreButton(submitBtn);

    if (res.success && res.requiresCode) {
      showToast("Verification code sent to your email!");
      showVerifyScreen(res.email);
    } else if (!res.success) {
      showToast(res.message, "danger");
    }
  });

  // Registration Form Submit — no password
  authRegisterForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const name = document.getElementById("register-name").value.trim();
    const email = document.getElementById("register-email").value.trim();
    const phone = document.getElementById("register-phone").value.trim();
    const submitBtn = authRegisterForm.querySelector(".auth-submit-btn");

    showLoadingButton(submitBtn, "Creating Account...");
    const res = await app.registerUser(name, email, phone);
    restoreButton(submitBtn);

    if (res.success) {
      showToast("Account created! Check your email for the verification code.");
      showVerifyScreen(email);
    } else {
      showToast(res.message, "danger");
    }
  });

  // Email Verification Form Submit
  authVerifyForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const code = document.getElementById("verify-code").value.trim();
    const submitBtn = authVerifyForm.querySelector(".auth-submit-btn");

    if (!pendingVerifyEmail) {
      showToast("Please register first", "danger");
      return;
    }

    showLoadingButton(submitBtn, "Verifying...");
    const res = await app.verifyEmailCode(pendingVerifyEmail, code);
    restoreButton(submitBtn);

    if (res.success) {
      showToast(`Welcome to Mārwāri E-Commerce, ${res.user.name}!`);
      updateNavBarState();
      closeModal();
      pendingVerifyEmail = null;

      // Reset forms
      authRegisterForm.reset();
      authVerifyForm.style.display = "none";
      authLoginForm.style.display = "block";
      authTabBtns.forEach(b => b.classList.remove("active"));
      authTabBtns[0].classList.add("active");

      // Admin page: auto-switch to admin dashboard after login
      const currentPageMode = (typeof wpApiSettings !== 'undefined' && wpApiSettings.pageMode) ? wpApiSettings.pageMode : 'shop';
      if (currentPageMode === 'admin' && res.user.role === 'admin') {
        switchView("admin");
      } else if (currentPageMode === 'admin' && res.user.role !== 'admin') {
        showToast("You are not an admin. Redirecting to shop...", "danger");
        setTimeout(() => { window.location.href = '/shop/'; }, 2000);
      }
    } else {
      showToast(res.message, "danger");
    }
  });

  // Resend Code Button
  document.getElementById("resend-code-btn").addEventListener("click", async () => {
    if (!pendingVerifyEmail) return;
    const btn = document.getElementById("resend-code-btn");
    btn.textContent = "Sending...";
    btn.disabled = true;

    const res = await app.resendCode(pendingVerifyEmail);

    if (res.success) {
      showToast("New verification code sent to your email!");
    } else {
      showToast(res.message || "Failed to resend code", "danger");
    }

    btn.textContent = "Resend Verification Code";
    btn.disabled = false;
  });

  // Admin Panel Login Form Submit
  if (authAdminForm) {
    authAdminForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const email = document.getElementById("admin-email").value.trim();
      const password = document.getElementById("admin-password").value;
      const submitBtn = authAdminForm.querySelector(".auth-submit-btn");

      showLoadingButton(submitBtn, "Authenticating...");

      try {
        const res = await app.apiFetch('/auth/admin-login', {
          method: 'POST',
          body: JSON.stringify({ email, password })
        });

        restoreButton(submitBtn);

        if (res.success) {
          app.currentUser = res.user;
          app.saveLocalStorage("marwari_session", res.user);
          if (res.nonce) app.nonce = res.nonce;
          app.orders = await app.apiFetch('/orders');

          showToast(`Welcome, ${res.user.name}! Loading dashboard...`);
          updateNavBarState();
          closeModal();

          // Switch to admin dashboard view
          switchView("admin");
        }
      } catch (err) {
        restoreButton(submitBtn);
        showToast(err.message || "Admin login failed", "danger");
      }
    });
  }

  document.getElementById("checkout-trigger-btn").addEventListener("click", () => {
    if (app.cart.length === 0) {
      showToast("Your cart is empty", "danger");
      return;
    }

    if (!app.currentUser) {
      showToast("Please log in to purchase products", "danger");
      closeCartFn();
      openModal("auth-modal");
      return;
    }

    document.getElementById("checkout-name").value = app.currentUser.name;
    document.getElementById("checkout-phone").value = app.currentUser.phone || "";

    closeCartFn();
    openModal("checkout-modal");
  });

  const checkoutForm = document.getElementById("checkout-payment-form");
  checkoutForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const submitBtn = checkoutForm.querySelector(".auth-submit-btn");

    const shippingDetails = {
      name: document.getElementById("checkout-name").value.trim(),
      phone: document.getElementById("checkout-phone").value.trim(),
      address: document.getElementById("checkout-address").value.trim(),
      city: document.getElementById("checkout-city").value.trim(),
      zip: document.getElementById("checkout-zip").value.trim()
    };

    showLoadingButton(submitBtn, "Placing Order...");
    try {
      await app.submitCheckoutOrder(shippingDetails);
      restoreButton(submitBtn);
      closeModal();
      showToast("Order placed successfully! Padharo Mhare Des.");
      updateCartUI();

      setTimeout(() => {
        switchView("orders");
      }, 500);
    } catch (err) {
      showToast(err.message, "danger");
      restoreButton(submitBtn);
    }
  });

  const addProductForm = document.getElementById("admin-add-product-form");
  if (addProductForm) {
    addProductForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const submitBtn = addProductForm.querySelector("button[type='submit']");

      const product = {
        name: document.getElementById("admin-pname").value.trim(),
        category: document.getElementById("admin-pcategory").value,
        price: parseFloat(document.getElementById("admin-pprice").value),
        description: document.getElementById("admin-pdesc").value.trim(),
        image: document.getElementById("admin-pimage").value.trim() || "https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=600&q=80",
        badge: document.getElementById("admin-pbadge").value.trim() || null
      };

      showLoadingButton(submitBtn, "Publishing...");
      try {
        await app.addProduct(product);
        restoreButton(submitBtn);
        showToast(`Product "${product.name}" published successfully`);
        addProductForm.reset();
        renderAdminDashboard();
      } catch (err) {
        showToast(err.message, "danger");
        restoreButton(submitBtn);
      }
    });
  }
}

window.onclick = function (event) {
  const overlay = document.getElementById("modal-overlay-container");
  if (event.target === overlay) {
    closeModal();
  }
};

// ===== ADMIN DASHBOARD MODULE (Sidebar Layout) =====
(function () {
  const pageMode = (typeof wpApiSettings !== 'undefined' && wpApiSettings.pageMode) ? wpApiSettings.pageMode : 'shop';
  if (pageMode !== 'admin') return;

  document.addEventListener("DOMContentLoaded", () => {
    initTheme();

    const loginForm = document.getElementById("admin-login-form");
    const loginScreen = document.getElementById("admin-login-screen");
    const adminLayout = document.getElementById("admin-layout");

    if (!loginForm) return;

    // Check saved admin session
    const savedSession = localStorage.getItem("marwari_session");
    if (savedSession) {
      try {
        const session = JSON.parse(savedSession);
        if (session && session.role === 'admin') {
          app.currentUser = session;
          showDashboard(session);
          return;
        }
      } catch (e) { }
    }

    // Admin Login
    loginForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const email = document.getElementById("admin-login-email").value.trim();
      const password = document.getElementById("admin-login-password").value;
      const submitBtn = document.getElementById("admin-login-btn");

      submitBtn.textContent = "Authenticating...";
      submitBtn.disabled = true;

      try {
        const res = await app.apiFetch('/auth/admin-login', {
          method: 'POST',
          body: JSON.stringify({ email, password })
        });

        if (res.success) {
          app.currentUser = res.user;
          app.saveLocalStorage("marwari_session", res.user);
          if (res.nonce) app.nonce = res.nonce;
          showToast(`Welcome, ${res.user.name}!`);
          showDashboard(res.user);
        }
      } catch (err) {
        showToast(err.message || "Login failed", "danger");
      }

      submitBtn.textContent = "Access Dashboard";
      submitBtn.disabled = false;
    });

    async function showDashboard(user) {
      loginScreen.style.display = "none";
      adminLayout.style.display = "flex";

      // Topbar user
      const topbarUser = document.getElementById("admin-topbar-user");
      if (topbarUser) {
        topbarUser.innerHTML = `<span style="font-weight:600; color:var(--primary);">👤 ${user.name}</span>`;
      }

      // Sidebar navigation
      const sidebarLinks = document.querySelectorAll(".sidebar-link[data-section]");
      const sections = document.querySelectorAll(".admin-section");
      const pageTitle = document.getElementById("admin-page-title");
      const sidebar = document.getElementById("admin-sidebar");

      sidebarLinks.forEach(link => {
        link.addEventListener("click", () => {
          const section = link.dataset.section;

          // Update active states
          sidebarLinks.forEach(l => l.classList.remove("active"));
          link.classList.add("active");

          // Show section
          sections.forEach(s => s.classList.remove("active"));
          const target = document.getElementById("section-" + section);
          if (target) target.classList.add("active");

          // Update title
          const titles = { dashboard: "Dashboard", orders: "Orders", products: "Products", "add-product": "Add Product", customers: "Customers" };
          if (pageTitle) pageTitle.textContent = titles[section] || "Dashboard";

          // Close mobile sidebar
          if (sidebar) sidebar.classList.remove("open");
        });
      });

      // Mobile sidebar toggle
      const toggleBtn = document.getElementById("sidebar-toggle");
      if (toggleBtn && sidebar) {
        toggleBtn.addEventListener("click", () => {
          sidebar.classList.toggle("open");
        });
      }

      // Logout
      const logoutBtn = document.getElementById("admin-logout-btn");
      if (logoutBtn) {
        logoutBtn.addEventListener("click", async () => {
          localStorage.removeItem("marwari_session");
          app.currentUser = null;
          try { await app.apiFetch('/auth/logout', { method: 'POST' }); } catch (e) { }
          location.reload();
        });
      }

      // Load data
      try {
        const [products, orders, customers] = await Promise.all([
          app.apiFetch('/products'),
          app.apiFetch('/orders'),
          app.apiFetch('/admin/customers')
        ]);

        renderDashStats(products, orders, customers);
        renderRevenueChart(orders);
        renderOrderStatusChart(orders);
        renderDashOrders(orders);
        renderDashProducts(products);
        renderDashCustomers(customers);
      } catch (err) {
        showToast("Failed to load data: " + err.message, "danger");
      }
    }

    function renderDashStats(products, orders, customers) {
      const revenue = orders.reduce((sum, o) => sum + (o.total || 0), 0);
      document.getElementById("dash-stat-revenue").textContent = `₹${revenue.toLocaleString('en-IN')}`;
      document.getElementById("dash-stat-products").textContent = products.length;
      document.getElementById("dash-stat-orders").textContent = orders.length;
      document.getElementById("dash-stat-customers").textContent = customers.length;
    }

    function renderRevenueChart(orders) {
      const container = document.getElementById("revenue-chart");
      if (!container) return;
      const monthData = {};
      const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      orders.forEach(o => {
        const d = new Date(o.date);
        const key = `${monthNames[d.getMonth()]} ${d.getFullYear().toString().slice(-2)}`;
        monthData[key] = (monthData[key] || 0) + (o.total || 0);
      });
      const entries = Object.entries(monthData).slice(-7);
      const maxVal = Math.max(...entries.map(e => e[1]), 1);
      if (entries.length === 0) {
        container.innerHTML = '<p style="color:var(--text-muted); text-align:center; width:100%; align-self:center;">No revenue data yet</p>';
        return;
      }
      container.innerHTML = entries.map(([label, val]) => {
        const height = Math.max((val / maxVal) * 200, 8);
        return `<div class="chart-bar-group"><span class="chart-bar-value">₹${(val / 1000).toFixed(1)}k</span><div class="chart-bar" style="height:${height}px;"></div><span class="chart-bar-label">${label}</span></div>`;
      }).join('');
    }

    function renderOrderStatusChart(orders) {
      const container = document.getElementById("order-status-chart");
      if (!container) return;
      const pending = orders.filter(o => o.status === 'Pending').length;
      const completed = orders.filter(o => o.status === 'Completed').length;
      const total = orders.length || 1;
      const radius = 60, circumference = 2 * Math.PI * radius;
      const completedDash = (completed / total) * circumference;
      const pendingDash = (pending / total) * circumference;
      container.innerHTML = `<div class="donut-chart-wrapper"><svg width="160" height="160" viewBox="0 0 160 160"><circle cx="80" cy="80" r="${radius}" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="20"/><circle cx="80" cy="80" r="${radius}" fill="none" stroke="#10b981" stroke-width="20" stroke-dasharray="${completedDash} ${circumference - completedDash}" stroke-dashoffset="0" transform="rotate(-90 80 80)" stroke-linecap="round"/><circle cx="80" cy="80" r="${radius}" fill="none" stroke="#fbbf24" stroke-width="20" stroke-dasharray="${pendingDash} ${circumference - pendingDash}" stroke-dashoffset="${-completedDash}" transform="rotate(-90 80 80)" stroke-linecap="round"/><text x="80" y="78" text-anchor="middle" fill="var(--text-primary)" font-size="22" font-weight="700" font-family="var(--font-body)">${total}</text><text x="80" y="98" text-anchor="middle" fill="var(--text-muted)" font-size="11" font-family="var(--font-body)">Total</text></svg><div class="donut-legend"><div class="donut-legend-item"><div class="donut-legend-dot" style="background:#10b981;"></div> Completed (${completed})</div><div class="donut-legend-item"><div class="donut-legend-dot" style="background:#fbbf24;"></div> Pending (${pending})</div></div></div>`;
    }

    function renderDashOrders(orders) {
      const tbody = document.getElementById("dash-orders-table");
      if (!tbody) return;
      if (orders.length === 0) { tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding:2rem;">No orders yet</td></tr>'; return; }
      tbody.innerHTML = orders.map(o => {
        const items = Array.isArray(o.items) ? o.items.map(i => `${i.name} ×${i.quantity}`).join(', ') : '—';
        const date = new Date(o.date).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: '2-digit' });
        const statusStyle = o.status === 'Completed' ? 'color:#10b981;' : 'color:#fbbf24; cursor:pointer;';
        const statusClick = o.status === 'Pending' ? `onclick="window.dashUpdateOrderStatus('${o.id}')"` : '';
        return `<tr><td style="font-family:monospace; font-size:0.75rem;">${o.id}</td><td>${o.user_email || '—'}</td><td style="font-size:0.8rem; max-width:200px; overflow:hidden; text-overflow:ellipsis;">${items}</td><td style="font-weight:600;">₹${(o.total || 0).toLocaleString('en-IN')}</td><td style="font-size:0.8rem;">${date}</td><td><span style="font-weight:600; ${statusStyle}" ${statusClick}>${o.status}</span></td></tr>`;
      }).join('');
    }

    window.dashUpdateOrderStatus = async function (orderId) {
      try {
        await app.apiFetch(`/orders/${orderId}/status`, { method: 'POST', body: JSON.stringify({ status: 'Completed' }) });
        showToast("Order marked as Completed!");
        const [products, orders, customers] = await Promise.all([app.apiFetch('/products'), app.apiFetch('/orders'), app.apiFetch('/admin/customers')]);
        renderDashStats(products, orders, customers);
        renderRevenueChart(orders);
        renderOrderStatusChart(orders);
        renderDashOrders(orders);
      } catch (err) { showToast(err.message, "danger"); }
    };

    function renderDashProducts(products) {
      const tbody = document.getElementById("dash-products-table");
      if (!tbody) return;
      if (products.length === 0) { tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; color:var(--text-muted); padding:2rem;">No products</td></tr>'; return; }
      tbody.innerHTML = products.map(p => `<tr><td style="display:flex; align-items:center; gap:0.75rem;"><img src="${p.image}" alt="${p.name}" style="width:40px; height:40px; border-radius:8px; object-fit:cover;"><span style="font-weight:500;">${p.name}</span></td><td><span style="background:var(--primary-glow); color:var(--primary); padding:0.2rem 0.6rem; border-radius:20px; font-size:0.75rem; font-weight:600;">${p.category}</span></td><td style="font-weight:600;">₹${parseFloat(p.price).toLocaleString('en-IN')}</td><td><button onclick="window.dashDeleteProduct('${p.id}')" style="background:var(--danger); color:white; border:none; padding:0.4rem 0.8rem; border-radius:8px; cursor:pointer; font-size:0.75rem; font-weight:600;">Delete</button></td></tr>`).join('');
    }

    window.dashDeleteProduct = async function (productId) {
      if (!confirm("Delete this product?")) return;
      try {
        await app.apiFetch(`/products/${productId}`, { method: 'DELETE' });
        showToast("Product deleted");
        const products = await app.apiFetch('/products');
        renderDashProducts(products);
        document.getElementById("dash-stat-products").textContent = products.length;
      } catch (err) { showToast(err.message, "danger"); }
    };

    function renderDashCustomers(customers) {
      const tbody = document.getElementById("dash-customers-table");
      if (!tbody) return;
      if (customers.length === 0) { tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:var(--text-muted); padding:2rem;">No customers yet</td></tr>'; return; }
      tbody.innerHTML = customers.map(c => {
        const regDate = new Date(c.registered).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: '2-digit' });
        return `<tr><td style="font-weight:500;">${c.name}</td><td style="font-size:0.85rem;">${c.email}</td><td>${c.phone}</td><td style="font-weight:600; text-align:center;">${c.orders}</td><td style="font-size:0.8rem;">${regDate}</td></tr>`;
      }).join('');
    }

    // Add Product Form
    const addForm = document.getElementById("admin-add-product-form");
    if (addForm) {
      addForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const submitBtn = addForm.querySelector("button[type='submit']");
        submitBtn.textContent = "Publishing...";
        submitBtn.disabled = true;
        const product = {
          name: document.getElementById("admin-pname").value.trim(),
          category: document.getElementById("admin-pcategory").value,
          price: parseFloat(document.getElementById("admin-pprice").value),
          description: document.getElementById("admin-pdesc").value.trim(),
          image: document.getElementById("admin-pimage").value.trim() || "https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=600&q=80",
          badge: document.getElementById("admin-pbadge").value.trim() || null
        };
        try {
          await app.apiFetch('/products', { method: 'POST', body: JSON.stringify(product) });
          showToast("Product published!");
          addForm.reset();
          const products = await app.apiFetch('/products');
          renderDashProducts(products);
          document.getElementById("dash-stat-products").textContent = products.length;
        } catch (err) { showToast(err.message, "danger"); }
        submitBtn.textContent = "Publish Product";
        submitBtn.disabled = false;
      });
    }
  });
})();

