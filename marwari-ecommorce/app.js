// Marwari E-Commerce - Main Logic & Application State

// Initial Seed Data
const DEFAULT_PRODUCTS = [
  {
    id: "prod-1",
    name: "Royal Jaipuri Silk Bandhani Saree",
    category: "Apparel",
    price: 8499,
    description: "Experience the royal heritage of Rajasthan with this premium pure silk Bandhani Saree. Hand-dyed by traditional artisans in Jaipur using the classic tie-dye technique, featuring intricate golden zari borders and elegant motifs perfect for festive occasions.",
    image: "https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=600&q=80",
    badge: "Bestseller"
  },
  {
    id: "prod-2",
    name: "Classic Navy Blue Royal Jodhpuri Suit",
    category: "Apparel",
    price: 12999,
    description: "An epitome of sophistication, this Jodhpuri Suit is tailored to perfection. Crafted from premium wool-blend fabric, it features a bandhgala collar, structured shoulders, and brass crest buttons. Represents the true legacy of Marwari aristocracy.",
    image: "https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&w=600&q=80",
    badge: "Royal Exclusive"
  },
  {
    id: "prod-3",
    name: "Handcrafted Gold-Leaf Jaipuri Quilt",
    category: "Handicrafts",
    price: 3499,
    description: "This world-famous Jaipuri Razai (quilt) is incredibly lightweight yet provides exceptional warmth. Made of 100% pure organic cotton and filled with carded cotton, it is detailed with traditional gold-leaf hand-block printing.",
    image: "https://images.unsplash.com/photo-1583847268964-b28dc8f51f92?auto=format&fit=crop&w=600&q=80",
    badge: "100% Cotton"
  },
  {
    id: "prod-4",
    name: "Pure Silver Meenakari Pearl Jhumkas",
    category: "Jewelry",
    price: 4999,
    description: "Exquisite traditional earrings featuring intricate Meenakari (enamel) artwork hand-painted on pure sterling silver. Suspended with premium freshwater pearls and detailed with delicate filigree work inspired by Royal Rajasthani palaces.",
    image: "https://images.unsplash.com/photo-1630019852942-f89202989a59?auto=format&fit=crop&w=600&q=80",
    badge: "Handmade"
  },
  {
    id: "prod-5",
    name: "Traditional Camel Leather Mojaris",
    category: "Apparel",
    price: 1899,
    description: "Authentic Marwari Mojaris made from genuine, double-tanned camel leather. Hand-stitched with premium silk and golden zari threads. Designed with a soft cushioned sole for comfort and durability while maintaining absolute style.",
    image: "https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=600&q=80",
    badge: "Artisan Leather"
  },
  {
    id: "prod-6",
    name: "Premium Saffron & Cardamom Kesaria Peda",
    category: "Sweets & Spices",
    price: 899,
    description: "Indulge in the true taste of Marwar. Our Kesaria Peda is cooked slowly using fresh condensed milk (khoya), flavored with organic Kashmiri saffron (kesar) and ground green cardamom, garnished with premium sliced pistachios and almonds.",
    image: "https://images.unsplash.com/photo-1587314168485-3236d6710814?auto=format&fit=crop&w=600&q=80",
    badge: "Freshly Made"
  },
  {
    id: "prod-7",
    name: "Jaipur Traditional Blue Pottery Vase",
    category: "Handicrafts",
    price: 2299,
    description: "Add a touch of elegance to your home with this authentic Jaipur Blue Pottery decorative vase. Crafted with Egyptian paste clay, hand-painted with cobalt blue dye in traditional floral motifs, and glazed to a premium shine.",
    image: "https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=600&q=80",
    badge: "Heritage Art"
  }
];

const DEFAULT_USERS = [
  { email: "admin@gmail.com", password: "password123", name: "Marwari Admin", role: "admin", phone: "9876543210" },
  { email: "user@gmail.com", password: "password123", name: "Ramesh Seervi", role: "user", phone: "9001122334" }
];

// Global Application State Manager
class AppState {
  constructor() {
    this.products = this.loadLocalStorage("marwari_products", DEFAULT_PRODUCTS);
    this.users = this.loadLocalStorage("marwari_users", DEFAULT_USERS);
    this.orders = this.loadLocalStorage("marwari_orders", []);
    this.cart = this.loadLocalStorage("marwari_cart", []);
    this.currentUser = this.loadLocalStorage("marwari_session", null);
  }

  loadLocalStorage(key, defaultValue) {
    const data = localStorage.getItem(key);
    return data ? JSON.parse(data) : defaultValue;
  }

  saveState() {
    localStorage.setItem("marwari_products", JSON.stringify(this.products));
    localStorage.setItem("marwari_users", JSON.stringify(this.users));
    localStorage.setItem("marwari_orders", JSON.stringify(this.orders));
    localStorage.setItem("marwari_cart", JSON.stringify(this.cart));
    localStorage.setItem("marwari_session", JSON.stringify(this.currentUser));
  }

  // Cart Management
  addToCart(productId, quantity = 1) {
    const product = this.products.find(p => p.id === productId);
    if (!product) return false;

    const existingItem = this.cart.find(item => item.product.id === productId);
    if (existingItem) {
      existingItem.quantity += quantity;
    } else {
      this.cart.push({ product, quantity });
    }
    this.saveState();
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
      this.saveState();
      return true;
    }
    return false;
  }

  removeFromCart(productId) {
    this.cart = this.cart.filter(item => item.product.id !== productId);
    this.saveState();
  }

  clearCart() {
    this.cart = [];
    this.saveState();
  }

  getCartTotal() {
    return this.cart.reduce((total, item) => total + (item.product.price * item.quantity), 0);
  }

  getCartCount() {
    return this.cart.reduce((count, item) => count + item.quantity, 0);
  }

  // User Authentication
  loginWithEmail(email, password) {
    const user = this.users.find(u => u.email.toLowerCase() === email.toLowerCase() && u.password === password);
    if (user) {
      this.currentUser = { email: user.email, name: user.name, role: user.role, phone: user.phone };
      this.saveState();
      return { success: true, user: this.currentUser };
    }
    return { success: false, message: "Invalid email or password" };
  }

  loginWithOTP(phone, otpInput, generatedOTP) {
    if (!otpInput || otpInput !== generatedOTP) {
      return { success: false, message: "Incorrect OTP. Please enter the code displayed." };
    }

    // Check if user exists, else register a new user
    let user = this.users.find(u => u.phone === phone);
    if (!user) {
      user = {
        email: `${phone}@marwari.com`,
        password: "otp-login-pwd",
        name: `Guest ${phone.slice(-4)}`,
        role: "user",
        phone: phone
      };
      this.users.push(user);
    }

    this.currentUser = { email: user.email, name: user.name, role: user.role, phone: user.phone };
    this.saveState();
    return { success: true, user: this.currentUser };
  }

  logout() {
    this.currentUser = null;
    this.cart = []; // Optional: Clear cart on logout
    this.saveState();
  }
}

// Instantiate Global State
const app = new AppState();

// Global OTP state tracking
let generatedOTPCode = null;

// UI Initialization & Controller
document.addEventListener("DOMContentLoaded", () => {
  // Theme initialization
  initTheme();

  // Initial Product Display
  renderStorefront();

  // Set Nav/Auth States
  updateNavBarState();

  // Setup Event Listeners
  setupEventListeners();
});

// Theme Management
function initTheme() {
  const isLight = localStorage.getItem("light_mode") === "true";
  if (isLight) {
    document.body.classList.add("light-mode");
    document.getElementById("theme-icon").innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
    `; // Show moon icon if in light mode (to switch back to dark)
  } else {
    document.getElementById("theme-icon").innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
    `; // Show sun icon if in dark mode
  }
}

function toggleTheme() {
  const body = document.body;
  body.classList.toggle("light-mode");
  const isLight = body.classList.contains("light-mode");
  localStorage.setItem("light_mode", isLight);

  const icon = document.getElementById("theme-icon");
  if (isLight) {
    icon.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
    `;
    showToast("Switched to Royal Light Theme");
  } else {
    icon.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
    `;
    showToast("Switched to Luxury Dark Theme");
  }
}

// Render Main Storefront Products
function renderStorefront(category = "All", query = "") {
  const container = document.getElementById("products-container");
  if (!container) return;

  container.innerHTML = "";

  // Filter products by category and search query
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
        <p>Try matching another category, title, or keyword.</p>
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
          <span class="product-price">₹${p.price.toLocaleString("en-IN")}</span>
          <button class="add-cart-btn" data-id="${p.id}" title="Quick Add to Cart">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5v14"/></svg>
          </button>
        </div>
      </div>
    `;

    // Add Click listener to open detail modal
    card.addEventListener("click", (e) => {
      // Avoid opening modal if adding to cart directly
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
  // Counts
  const count = app.getCartCount();
  const badges = document.querySelectorAll(".cart-count");
  badges.forEach(b => {
    b.innerText = count;
    b.style.display = count > 0 ? "flex" : "none";
  });

  // Drawer list
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
        <span class="cart-item-price">₹${item.product.price.toLocaleString("en-IN")}</span>
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

    // Bind quantity handlers
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

// Authentication Modal and Dropdown menu state
function updateNavBarState() {
  const profileContainer = document.getElementById("user-menu-container");
  if (!profileContainer) return;

  if (app.currentUser) {
    // Logged In state
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

    // Dropdown toggle
    const trigger = document.getElementById("profile-menu-trigger");
    const menu = document.getElementById("profile-dropdown-menu");
    trigger.addEventListener("click", (e) => {
      e.stopPropagation();
      menu.classList.toggle("active");
    });

    // Sub-buttons
    const logoutBtn = document.getElementById("logout-btn");
    logoutBtn.addEventListener("click", () => {
      app.logout();
      showToast("Logged out successfully");
      updateNavBarState();
      // Reset view to storefront if logged out from admin/orders
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

    const backShopBtn = document.getElementById("back-to-shop-btn");
    backShopBtn.addEventListener("click", () => {
      switchView("shop");
      menu.classList.remove("active");
    });

  } else {
    // Logged Out state
    profileContainer.innerHTML = `
      <button class="btn-primary" id="open-auth-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
        <span>Login</span>
      </button>
    `;

    const openAuth = document.getElementById("open-auth-btn");
    openAuth.addEventListener("click", () => {
      openModal("auth-modal");
    });
  }
}

// Switch between Main Shop, Admin Dashboard, and Order History
function switchView(view) {
  const heroSection = document.getElementById("hero-section");
  const catSection = document.getElementById("categories-section");
  const shopSection = document.getElementById("shop-section");
  const adminView = document.getElementById("admin-view");
  const ordersView = document.getElementById("orders-view");

  // Deactivate all
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

// Modal Handlers
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
  const overlay = document.getElementById("modal-overlay-container");
  overlay.classList.remove("active");
}

// Product Details Modal Renders
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
        <span>₹${product.price.toLocaleString("en-IN")}</span>
        ${product.badge ? `<span class="product-badge" style="position:static;">${product.badge}</span>` : ''}
      </div>
      <p class="detail-desc">${product.description}</p>
      <div class="detail-actions">
        <div class="quantity-control">
          <button class="quantity-btn dec-btn">-</button>
          <span class="quantity-value">1</span>
          <button class="quantity-btn inc-btn">+</button>
        </div>
        <button class="btn-primary add-detail-cart" style="flex-grow:1; justify-content:center;">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
          Add to Bag
        </button>
      </div>
    </div>
  `;

  // Bind Events
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

// Admin Dashboard Controls
function renderAdminDashboard() {
  // Statistics
  const totalSales = app.orders.reduce((total, o) => total + o.total, 0);
  const productsCount = app.products.length;
  const totalOrders = app.orders.length;

  document.getElementById("stat-sales").innerText = `₹${totalSales.toLocaleString("en-IN")}`;
  document.getElementById("stat-products").innerText = productsCount;
  document.getElementById("stat-orders").innerText = totalOrders;

  // Render Product Table list
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
        <td>₹${p.price.toLocaleString("en-IN")}</td>
        <td>
          <button class="action-icon-btn delete" data-id="${p.id}" title="Delete Product">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2M10 11v6M14 11v6"/></svg>
          </button>
        </td>
      `;

      row.querySelector(".delete").addEventListener("click", () => {
        if (confirm(`Are you sure you want to delete "${p.name}"?`)) {
          app.products = app.products.filter(item => item.id !== p.id);
          app.saveState();
          showToast("Product deleted successfully");
          renderAdminDashboard();
        }
      });

      prodTableBody.appendChild(row);
    });
  }

  // Render Orders Queue table
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
        <td>₹${o.total.toLocaleString("en-IN")}</td>
        <td>
          <button class="badge-status ${o.status.toLowerCase()}" data-id="${o.id}">
            ${o.status}
          </button>
        </td>
      `;

      // Status update handler (Pending -> Completed)
      const statusBtn = row.querySelector(".badge-status");
      statusBtn.addEventListener("click", () => {
        const order = app.orders.find(item => item.id === o.id);
        if (order && order.status === "Pending") {
          order.status = "Completed";
          app.saveState();
          showToast("Order status updated to Completed");
          renderAdminDashboard();
        }
      });

      ordersTableBody.appendChild(row);
    });
  }
}

// User Orders History View
function renderOrdersHistory() {
  const container = document.getElementById("orders-history-list");
  if (!container) return;

  const myOrders = app.orders.filter(o => o.userEmail === app.currentUser.email);
  if (myOrders.length === 0) {
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
  myOrders.forEach(o => {
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
                <div style="font-size:0.8rem; color:var(--text-secondary);">Qty: ${item.quantity} x ₹${item.product.price}</div>
              </div>
            </div>
            <div style="font-weight:700;">₹${(item.product.price * item.quantity).toLocaleString("en-IN")}</div>
          </div>
        `).join("")}
      </div>
      <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
        <span style="font-size:0.85rem; color:var(--text-secondary);">Shipment Address: ${o.shippingDetails.address}</span>
        <span style="font-weight:800; font-size:1.1rem; color:var(--primary);">Total: ₹${o.total.toLocaleString("en-IN")}</span>
      </div>
    `;
    container.appendChild(card);
  });
}

// Global Toast System
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

  // Auto remove after 3s
  setTimeout(() => {
    toast.style.animation = "slideDown 0.3s forwards, fadeOut 0.3s forwards";
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// Event Listeners setup
function setupEventListeners() {
  // Theme toggle click
  const themeToggle = document.getElementById("theme-toggle");
  if (themeToggle) {
    themeToggle.addEventListener("click", toggleTheme);
  }

  // Cart Drawer open/close
  const cartTrigger = document.getElementById("cart-trigger");
  const cartOverlay = document.getElementById("cart-drawer-overlay");
  const cartDrawer = document.getElementById("cart-drawer");
  const cartClose = document.getElementById("cart-close");

  if (cartTrigger) {
    cartTrigger.addEventListener("click", () => {
      updateCartUI();
      cartOverlay.style.display = "block";
      setTimeout(() => {
        cartOverlay.classList.add("active");
        cartDrawer.classList.add("active");
      }, 50);
    });
  }

  const closeCartFn = () => {
    cartOverlay.classList.remove("active");
    cartDrawer.classList.remove("active");
    setTimeout(() => {
      cartOverlay.style.display = "none";
    }, 300);
  };

  if (cartClose) cartClose.addEventListener("click", closeCartFn);
  if (cartOverlay) cartOverlay.addEventListener("click", closeCartFn);

  // Search input change handler
  const searchInput = document.getElementById("search-input");
  if (searchInput) {
    searchInput.addEventListener("input", (e) => {
      const activeTab = document.querySelector(".category-tab.active");
      const category = activeTab ? activeTab.dataset.category : "All";
      renderStorefront(category, e.target.value);
    });
  }

  // Category Tab navigation clicks
  const catTabs = document.querySelectorAll(".category-tab");
  catTabs.forEach(tab => {
    tab.addEventListener("click", (e) => {
      catTabs.forEach(t => t.classList.remove("active"));
      tab.classList.add("active");

      const category = tab.dataset.category;
      const searchVal = searchInput ? searchInput.value : "";
      renderStorefront(category, searchVal);
    });
  });

  // Auth modal Tabs
  const authTabBtns = document.querySelectorAll(".auth-tab-btn");
  const authEmailForm = document.getElementById("auth-email-form");
  const authPhoneForm = document.getElementById("auth-phone-form");

  authTabBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      authTabBtns.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");

      const type = btn.dataset.tab;
      if (type === "email") {
        authEmailForm.style.display = "block";
        authPhoneForm.style.display = "none";
      } else {
        authEmailForm.style.display = "none";
        authPhoneForm.style.display = "block";
      }
    });
  });

  // Email login form submit handler
  if (authEmailForm) {
    authEmailForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const email = document.getElementById("login-email").value.trim();
      const password = document.getElementById("login-password").value;

      const res = app.loginWithEmail(email, password);
      if (res.success) {
        showToast(`Welcome back, ${res.user.name}!`);
        updateNavBarState();
        closeModal();

        // If logged in as admin, redirect to Admin View
        if (res.user.role === 'admin') {
          switchView("admin");
        }
      } else {
        showToast(res.message, "danger");
      }
    });
  }

  // Mobile login OTP display and mock handler
  const sendOtpBtn = document.getElementById("send-otp-btn");
  const otpSection = document.getElementById("otp-verification-section");
  const otpPhoneInput = document.getElementById("login-phone");

  if (sendOtpBtn) {
    sendOtpBtn.addEventListener("click", () => {
      const phone = otpPhoneInput.value.trim();
      if (!phone || phone.length < 10) {
        showToast("Please enter a valid 10-digit mobile number", "danger");
        return;
      }

      // Generate a mock 4 digit OTP code
      generatedOTPCode = Math.floor(1000 + Math.random() * 9000).toString();

      // Reveal OTP input box
      otpSection.style.display = "block";
      sendOtpBtn.innerText = "Resend OTP";

      // Alert the OTP using Toast so the user can easily see it
      alert(`[Demo OTP Service] Your OTP code to log in is: ${generatedOTPCode}`);
      showToast(`OTP code sent to +91 ${phone}`);
    });
  }

  if (authPhoneForm) {
    authPhoneForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const phone = otpPhoneInput.value.trim();
      const otpInput = document.getElementById("login-otp").value.trim();

      if (!generatedOTPCode) {
        showToast("Please click 'Send OTP' first", "danger");
        return;
      }

      const res = app.loginWithOTP(phone, otpInput, generatedOTPCode);
      if (res.success) {
        showToast(`Welcome to Marwari E-Commerce!`);
        updateNavBarState();
        closeModal();
        // Reset states
        generatedOTPCode = null;
        otpSection.style.display = "none";
        sendOtpBtn.innerText = "Send OTP";
        document.getElementById("login-otp").value = "";
      } else {
        showToast(res.message, "danger");
      }
    });
  }

  // Checkout trigger
  const checkoutTrigger = document.getElementById("checkout-trigger-btn");
  if (checkoutTrigger) {
    checkoutTrigger.addEventListener("click", () => {
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

      // Populate shipping details form in Checkout Modal if user exists
      document.getElementById("checkout-name").value = app.currentUser.name;
      document.getElementById("checkout-phone").value = app.currentUser.phone || "";

      closeCartFn();
      openModal("checkout-modal");
    });
  }

  // Checkout Form Submission
  const checkoutForm = document.getElementById("checkout-payment-form");
  if (checkoutForm) {
    checkoutForm.addEventListener("submit", (e) => {
      e.preventDefault();

      const order = {
        id: "ord-" + Math.random().toString(36).substr(2, 9),
        userEmail: app.currentUser.email,
        items: [...app.cart],
        total: app.getCartTotal(),
        status: "Pending",
        date: new Date().toISOString(),
        shippingDetails: {
          name: document.getElementById("checkout-name").value.trim(),
          phone: document.getElementById("checkout-phone").value.trim(),
          address: document.getElementById("checkout-address").value.trim(),
          city: document.getElementById("checkout-city").value.trim(),
          zip: document.getElementById("checkout-zip").value.trim()
        }
      };

      // Save order
      app.orders.push(order);
      app.clearCart();
      app.saveState();

      closeModal();
      showToast("Order placed successfully! Padharo Mhare Des.");
      updateCartUI();

      // Show success animation overlay or modal
      setTimeout(() => {
        // Switch view to orders history to verify
        switchView("orders");
      }, 500);
    });
  }

  // Add Product form handler (Admin)
  const addProductForm = document.getElementById("admin-add-product-form");
  if (addProductForm) {
    addProductForm.addEventListener("submit", (e) => {
      e.preventDefault();

      const newProd = {
        id: "prod-" + Math.random().toString(36).substr(2, 9),
        name: document.getElementById("admin-pname").value.trim(),
        category: document.getElementById("admin-pcategory").value,
        price: parseFloat(document.getElementById("admin-pprice").value),
        description: document.getElementById("admin-pdesc").value.trim(),
        image: document.getElementById("admin-pimage").value.trim() || "https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=600&q=80",
        badge: document.getElementById("admin-pbadge").value.trim() || null
      };

      app.products.push(newProd);
      app.saveState();

      showToast(`Product "${newProd.name}" added successfully`);
      addProductForm.reset();
      renderAdminDashboard();
    });
  }
}

// Global modal background closing
window.onclick = function (event) {
  const overlay = document.getElementById("modal-overlay-container");
  if (event.target === overlay) {
    closeModal();
  }
};
