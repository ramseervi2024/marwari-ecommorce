// Marwari E-Commerce - Main Logic & Application State

// UI helper to show toast notifications globally
window.showToast = function(message, type = 'success') {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `<div class="toast-message">${message}</div>`;
  container.appendChild(toast);

  // Auto remove toast after 4 seconds
  setTimeout(() => {
    toast.style.animation = 'fadeOut 0.5s ease forwards';
    setTimeout(() => toast.remove(), 500);
  }, 4000);
};
// Initial Seed Data
const DEFAULT_PRODUCTS = [
  {
    id: "prod-1",
    name: "Royal Jaipuri Silk Bandhani Saree",
    category: "Royal Apparel",
    price: 8499,
    description: "Experience the royal heritage of Rajasthan with this premium pure silk Bandhani Saree. Hand-dyed by traditional artisans in Jaipur using the classic tie-dye technique, featuring intricate golden zari borders and elegant motifs perfect for festive occasions.",
    image: "https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=600&q=80",
    badge: "Bestseller"
  },
  {
    id: "prod-2",
    name: "Classic Navy Blue Royal Jodhpuri Suit",
    category: "Royal Apparel",
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
    category: "Silver Jewellery",
    price: 4999,
    description: "Exquisite traditional earrings featuring intricate Meenakari (enamel) artwork hand-painted on pure sterling silver. Suspended with premium freshwater pearls and detailed with delicate filigree work inspired by Royal Rajasthani palaces.",
    image: "https://images.unsplash.com/photo-1630019852942-f89202989a59?auto=format&fit=crop&w=600&q=80",
    badge: "Handmade"
  },
  {
    id: "prod-5",
    name: "Traditional Camel Leather Mojaris",
    category: "Marwari Mojari",
    price: 1899,
    description: "Authentic Marwari Mojaris made from genuine, double-tanned camel leather. Hand-stitched with premium silk and golden zari threads. Designed with a soft cushioned sole for comfort and durability while maintaining absolute style.",
    image: "https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=600&q=80",
    badge: "Artisan Leather"
  },
  {
    id: "prod-6",
    name: "Premium Saffron & Cardamom Kesaria Peda",
    category: "Food & Spices",
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
  },
  {
    id: "prod-8",
    name: "Authentic Bikaneri Bhujia & Namkeen Hamper",
    category: "Food & Spices",
    price: 599,
    description: "Crispy, savory, and spicy Bikaneri Bhujia prepared with moth bean flour and aromatic spices from the deserts of Bikaner. A true staple of Marwari hospitality, served in a royal festive gift box.",
    image: "https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?auto=format&fit=crop&w=600&q=80",
    badge: "Authentic GI"
  },
  {
    id: "prod-9",
    name: "Shekhawati Carved Teakwood Wall Mirror",
    category: "Home & Décor",
    price: 6499,
    description: "Hand-carved by master woodworkers in Shekhawati, this antique jharokha-style mirror frame features intricate archways and distressed finish that brings timeless palace architecture to your sanctuary.",
    image: "https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=600&q=80",
    badge: "Hand-Carved"
  },
  {
    id: "prod-10",
    name: "Royal Kundan Polki Choker Necklace",
    category: "Silver Jewellery",
    price: 15999,
    description: "An opulent bridal necklace set adorned with uncut polki stones set in pure silver foil, finished with emerald drops and meenakari enameling on the reverse side. A prized family heirloom.",
    image: "https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?auto=format&fit=crop&w=600&q=80",
    badge: "Royal Bridal"
  },
  {
    id: "prod-11",
    name: "Hand-Block Printed Chanderi Silk Dupatta",
    category: "Royal Apparel",
    price: 1899,
    description: "Woven in sheer Chanderi silk with delicate golden zari border, hand-stamped using traditional Bagru wooden blocks with organic vegetable dyes.",
    image: "https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=600&q=80",
    badge: "Organic Dyes"
  },
  {
    id: "prod-12",
    name: "Antique Brass Royal Peacock Lamp (Diya)",
    category: "Home & Décor",
    price: 2899,
    description: "Heavy solid brass oil lamp hand-cast using the lost-wax technique by artisan coppersmiths. Featuring an intricately sculpted royal peacock crest.",
    image: "https://images.unsplash.com/photo-1582738411706-bfc8e691d1c2?auto=format&fit=crop&w=600&q=80",
    badge: "Solid Brass"
  },
  {
    id: "prod-13",
    name: "Famous Mathania Red Chilli Powder & Spices",
    category: "Food & Spices",
    price: 649,
    description: "World-renowned Mathania red chillies from Jodhpur, stone-ground without adulteration. Imparts deep crimson color and rich, smoky, pungent warmth to royal curries.",
    image: "https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=600&q=80",
    badge: "Pure Organic"
  },
  {
    id: "prod-14",
    name: "Hand-Embroidered Marwari Gota Patti Odhani",
    category: "Royal Apparel",
    price: 4299,
    description: "Traditional Rajasthani festive odhani embellished with authentic pure silver gota ribbons applique work stitched by women artisans in rural Rajasthan.",
    image: "https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=600&q=80",
    badge: "Artisan Zari"
  },
  {
    id: "prod-15",
    name: "Makrana Marble Inlay Pietra Dura Coasters",
    category: "Home & Décor",
    price: 1499,
    description: "Set of four pristine white Makrana marble coasters embedded with lapis lazuli and malachite semi-precious stone inlays, echoing royal Taj Mahal artistry.",
    image: "https://images.unsplash.com/photo-1615529182904-14819c35db37?auto=format&fit=crop&w=600&q=80",
    badge: "Makrana Marble"
  },
  {
    id: "prod-16",
    name: "Royal Rajputana Sterling Silver Kada",
    category: "Silver Jewellery",
    price: 5499,
    description: "Heavy 92.5 sterling silver cuff kada featuring carved lion-head finials, symbolizing courage and valor of the royal Rajput warriors.",
    image: "https://images.unsplash.com/photo-1605100804763-247f67b3557e?auto=format&fit=crop&w=600&q=80",
    badge: "925 Silver"
  },
  {
    id: "prod-17",
    name: "Traditional Handcrafted Kathputli Puppet Pair",
    category: "Handicrafts",
    price: 999,
    description: "Hand-carved mango wood puppets dressed in vibrant Rajasthani tie-dye fabrics and sequin embroidery, depicting the folklore of King Amar Singh Rathore.",
    image: "https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&w=600&q=80",
    badge: "Folk Puppet"
  },
  {
    id: "prod-18",
    name: "Royal Kesariya Mawa Ghewar Festive Box",
    category: "Food & Spices",
    price: 1199,
    description: "Crispy honeycomb disc pastry made with pure Desi cow ghee, soaked in saffron sugar syrup and generously topped with rich reduced mawa and silver vark.",
    image: "https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=600&q=80",
    badge: "Desi Ghee"
  },
  {
    id: "prod-19",
    name: "Traditional Dabu Hand-Dyed Indigo Kurta",
    category: "Royal Apparel",
    price: 2399,
    description: "Breathable pure organic cotton kurta crafted using ancient mud-resist Dabu printing and natural indigo vat fermentation technique from Akola village.",
    image: "https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?auto=format&fit=crop&w=600&q=80",
    badge: "Natural Indigo"
  },
  {
    id: "prod-20",
    name: "Imperial Udaipur Heritage Silver Peacock Box",
    category: "Handicrafts",
    price: 7899,
    description: "An extraordinary masterwork created by royal silversmiths of Udaipur. Crafted with hand-carved floral repoussé engraving and a majestic perched peacock on the lid, lined with royal maroon velvet. Perfect for royal keepsakes and luxury heirloom gifting.",
    image: "https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?auto=format&fit=crop&w=600&q=80",
    badge: "Royal Masterpiece"
  }
];

const DEFAULT_USERS = [
  { username: "admin", email: "admin@marwari.com", password: "123456", name: "Marwari Admin", role: "admin", phone: "9876543210", status: "active" },
  { username: "user", email: "user@gmail.com", password: "password123", name: "Ramesh Seervi", role: "user", phone: "9001122334", status: "active", addresses: [{ id: "addr-1", label: "Home Base", street: "12 Heritage Lane", city: "Jodhpur", zip: "342001", default: true }] }
];

// Global Application State Manager
class AppState {
  constructor() {
    const DEFAULT_CATEGORIES = [
      { id: "cat-1", name: "Royal Apparel", slug: "Apparel", image: "https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=400&q=80" },
      { id: "cat-2", name: "Handicrafts", slug: "Handicrafts", image: "https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=400&q=80" },
      { id: "cat-3", name: "Silver Jewellery", slug: "Jewelry", image: "https://images.unsplash.com/photo-1630019852942-f89202989a59?auto=format&fit=crop&w=400&q=80" },
      { id: "cat-4", name: "Marwari Mojari", slug: "Apparel", image: "https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=400&q=80" },
      { id: "cat-5", name: "Sweets & Spices", slug: "Sweets & Spices", image: "https://images.unsplash.com/photo-1587314168485-3236d6710814?auto=format&fit=crop&w=400&q=80" },
      { id: "cat-6", name: "Home & Décor", slug: "Home & Décor", image: "https://images.unsplash.com/photo-1583847268964-b28dc8f51f92?auto=format&fit=crop&w=400&q=80" },
      { id: "cat-7", name: "Art & Collectibles", slug: "Handicrafts", image: "https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?auto=format&fit=crop&w=400&q=80" }
    ];

    const DEFAULT_COUPONS = [
      { id: "cp-1", code: "MARWARI10", type: "percentage", amount: 10, expiry: "2026-12-31" },
      { id: "cp-2", code: "ROYAL500", type: "flat", amount: 500, expiry: "2026-12-31" }
    ];

    this.products = this.loadLocalStorage("marwari_products_v7", DEFAULT_PRODUCTS);
    if (!this.products || this.products.length < DEFAULT_PRODUCTS.length) {
      this.products = DEFAULT_PRODUCTS;
      localStorage.setItem("marwari_products_v7", JSON.stringify(DEFAULT_PRODUCTS));
    }
    this.users = this.loadLocalStorage("marwari_users", DEFAULT_USERS);
    this.categories = this.loadLocalStorage("marwari_categories_v5", DEFAULT_CATEGORIES);
    this.coupons = this.loadLocalStorage("marwari_coupons", DEFAULT_COUPONS);
    this.orders = this.loadLocalStorage("marwari_orders", []);
    this.cart = this.loadLocalStorage("marwari_cart", []);
    this.currentUser = this.loadLocalStorage("marwari_session", null);
    this.activeCoupon = null;
  }

  loadLocalStorage(key, defaultValue) {
    const data = localStorage.getItem(key);
    return data ? JSON.parse(data) : defaultValue;
  }

  saveState() {
    // Mirror current user's profile details & addresses back to the users list
    if (this.currentUser) {
      const idx = this.users.findIndex(u => u.email.toLowerCase() === this.currentUser.email.toLowerCase());
      if (idx > -1) {
        this.users[idx] = { ...this.users[idx], ...this.currentUser };
      }
    }
    localStorage.setItem("marwari_products_v7", JSON.stringify(this.products));
    localStorage.setItem("marwari_users", JSON.stringify(this.users));
    localStorage.setItem("marwari_orders", JSON.stringify(this.orders));
    localStorage.setItem("marwari_cart", JSON.stringify(this.cart));
    localStorage.setItem("marwari_session", JSON.stringify(this.currentUser));
    localStorage.setItem("marwari_categories_v5", JSON.stringify(this.categories));
    localStorage.setItem("marwari_coupons", JSON.stringify(this.coupons));

    // Sync state to WordPress Backend APIs for Mobile App integration
    try {
      const baseApi = window.location.origin + '/wp-json/wp-ecommerce/v1';
      fetch(baseApi + '/products', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(this.products) }).catch(()=>{});
      fetch(baseApi + '/categories', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(this.categories) }).catch(()=>{});
      fetch(baseApi + '/users', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(this.users) }).catch(()=>{});
      fetch(baseApi + '/coupons', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(this.coupons) }).catch(()=>{});
      fetch(baseApi + '/orders', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(this.orders) }).catch(()=>{});
    } catch(e) {}
  }

  // Cart Management
  addToCart(productId, quantity = 1) {
    if (!this.currentUser) {
      if (typeof showToast !== 'undefined') {
        showToast("Please login or register to add items to your cart", "warning");
      }
      if (typeof switchView !== 'undefined') {
        switchView('auth');
      }
      return false;
    }
    
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
    this.activeCoupon = null;
    this.saveState();
  }

  getCartTotal() {
    return this.cart.reduce((total, item) => total + (item.product.price * item.quantity), 0);
  }

  getCartCount() {
    return this.cart.reduce((count, item) => count + item.quantity, 0);
  }

  // User Authentication
  validateEmailLogin(emailOrUsername, password) {
    const userExists = this.users.some(u => 
      (u.email.toLowerCase() === emailOrUsername.toLowerCase() ||
      (u.username && u.username.toLowerCase() === emailOrUsername.toLowerCase()))
    );

    if (!userExists) {
      return { success: false, message: "User not found. Please register." };
    }

    const user = this.users.find(u =>
      (u.email.toLowerCase() === emailOrUsername.toLowerCase() ||
        (u.username && u.username.toLowerCase() === emailOrUsername.toLowerCase())) &&
      u.password === password
    );

    if (user) {
      if (user.status === "blocked") {
        return { success: false, message: "Your account is blocked. Please contact support." };
      }
      return { success: true, user: user };
    }
    return { success: false, message: "Invalid password" };
  }

  loginWithEmail(emailOrUsername, password) {
    const validation = this.validateEmailLogin(emailOrUsername, password);
    if (!validation.success) return validation;

    const user = validation.user;
    this.currentUser = { ...user };
    
    // Simulate JWT Token generation
    const jwtToken = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9." + btoa(JSON.stringify({email: user.email, role: user.role, exp: Date.now() + 86400000})) + ".signature";
    localStorage.setItem('wp_ecommerce_jwt', jwtToken);
    
    this.saveState();
    return { success: true, user: this.currentUser, token: jwtToken };
  }

  loginWithOTP(phone, otpInput, generatedOTP) {
    if (!otpInput || otpInput !== generatedOTP) {
      return { success: false, message: "Incorrect OTP. Please enter the code displayed." };
    }

    let user = this.users.find(u => u.phone === phone);
    if (!user) {
      user = {
        username: phone,
        email: `${phone}@marwari.com`,
        password: "otp-login-pwd",
        name: `Guest ${phone.slice(-4)}`,
        role: "user",
        phone: phone,
        status: "active",
        addresses: []
      };
      this.users.push(user);
    }

    if (user.status === "blocked") {
      return { success: false, message: "Your account is blocked. Please contact support." };
    }

    this.currentUser = { ...user };
    this.saveState();
    return { success: true, user: this.currentUser };
  }

  validateSignup(email) {
    const exists = this.users.some(u => u.email.toLowerCase() === email.toLowerCase());
    if (exists) {
      return { success: false, message: "Email already exists." };
    }
    return { success: true };
  }

  signup(name, phone, email, password) {
    const validation = this.validateSignup(email);
    if (!validation.success) return validation;

    const newUser = {
      username: email.split("@")[0],
      email: email,
      phone: phone,
      password: password,
      name: name,
      role: "user",
      status: "active",
      addresses: [],
      created_at: new Date().toISOString()
    };

    this.users.push(newUser);
    this.currentUser = { ...newUser };
    
    // Simulate JWT Token generation
    const jwtToken = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9." + btoa(JSON.stringify({email: newUser.email, role: newUser.role, exp: Date.now() + 86400000})) + ".signature";
    localStorage.setItem('wp_ecommerce_jwt', jwtToken);
    
    this.saveState();
    return { success: true, user: newUser, token: jwtToken };
  }

  logout() {
    this.currentUser = null;
    this.cart = [];
    this.activeCoupon = null;
    localStorage.removeItem('wp_ecommerce_jwt');
    this.saveState();
    if (typeof navigateTo !== 'undefined') {
      navigateTo('/ecommerce/website');
    } else {
      const isLocalFile = window.location.protocol === 'file:';
      window.location.href = isLocalFile ? 'index.html' : '/ecommerce/website';
    }
  }

  // Coupon Logic
  applyCoupon(code) {
    const coupon = this.coupons.find(c => c.code.toUpperCase() === code.trim().toUpperCase());
    if (!coupon) {
      return { success: false, message: "Invalid coupon code." };
    }
    const today = new Date().toISOString().split('T')[0];
    if (coupon.expiry && coupon.expiry < today) {
      return { success: false, message: "This coupon code has expired." };
    }
    this.activeCoupon = coupon;
    return { success: true, coupon };
  }

  getDiscountAmount() {
    if (!this.activeCoupon) return 0;
    const subtotal = this.getCartTotal();
    if (this.activeCoupon.type === "percentage") {
      return Math.round((subtotal * this.activeCoupon.amount) / 100);
    } else if (this.activeCoupon.type === "flat") {
      return Math.min(subtotal, this.activeCoupon.amount);
    }
    return 0;
  }

  getGrandTotal() {
    return Math.max(0, this.getCartTotal() - this.getDiscountAmount());
  }
}

// Instantiate Global State
const app = new AppState();

// Global OTP state tracking
let generatedOTPCode = null;

// UI Initialization & Controller
function initApp() {
  const isSuperpanelPage = window.location.pathname.includes('superpanel') || window.location.pathname.includes('admin');

  // Theme initialization
  initTheme();

  // Populate drop-downs for categories
  populateCategorySelects();

  if (isSuperpanelPage) {
    // Check if logged-in user is admin
    // TEMPORARY BYPASS FOR WORDPRESS DEMO
    // if (!app.currentUser || app.currentUser.role !== 'admin') {
    //   showToast("Access Denied: Admins Only", "danger");
    //   sessionStorage.setItem('openAdminLogin', 'true');
    //   const isLocalFile = window.location.protocol === 'file:';
    //   window.location.href = isLocalFile ? 'index.html' : '/ecommerce/website';
    //   return;
    // }

    // Initialize Admin views
    renderAdminDashboard();
    setupEventListeners();
  } else {
    // Initialize Storefront views
    renderStorefrontCategories();
    renderDynamicNav();
    updateNavBarState();
    setupEventListeners();
    seedSampleOrders();

    // Check if redirecting from admin page due to unauthenticated state
    if (sessionStorage.getItem('openAdminLogin') === 'true') {
      sessionStorage.removeItem('openAdminLogin');
      switchView('auth');
      showToast("Please log in as Administrator.", "warning");
    }

    // Trigger local history/hash routing (e.g. for Account tab)
    handleRouting();
  }
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initApp);
} else {
  initApp();
}

// History / Routing Event Listeners
window.addEventListener("popstate", () => {
  const isSuperpanelPage = window.location.pathname.includes('superpanel');
  if (!isSuperpanelPage) {
    handleRouting();
  }
});

window.addEventListener("hashchange", () => {
  const isSuperpanelPage = window.location.pathname.includes('superpanel');
  if (!isSuperpanelPage) {
    handleRouting();
  }
});

// Seed sample orders for representation in reports & overview
function seedSampleOrders() {
  if (app.orders.length === 0) {
    app.orders = [
      {
        id: "ord-9k2l8x1",
        userEmail: "user@gmail.com",
        items: [
          { product: app.products[0], quantity: 1 },
          { product: app.products[5], quantity: 2 }
        ],
        total: 10297,
        status: "Delivered",
        date: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000).toISOString(),
        shippingDetails: {
          name: "Ramesh Seervi",
          phone: "9001122334",
          address: "12 Heritage Lane",
          city: "Jodhpur",
          zip: "342001"
        }
      },
      {
        id: "ord-8n1j9y2",
        userEmail: "user@gmail.com",
        items: [
          { product: app.products[1], quantity: 1 }
        ],
        total: 12999,
        status: "Pending",
        date: new Date().toISOString(),
        shippingDetails: {
          name: "Ramesh Seervi",
          phone: "9001122334",
          address: "12 Heritage Lane",
          city: "Jodhpur",
          zip: "342001"
        }
      }
    ];
    app.saveState();
  }
}

// Theme Management
function initTheme() {
  const isLight = localStorage.getItem("light_mode") !== "false"; // Default to light mode
  const themeIcon = document.getElementById("theme-icon");
  if (isLight) {
    document.body.classList.add("light-mode");
    if (themeIcon) {
      themeIcon.innerHTML = `
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
      `;
    }
  } else {
    if (themeIcon) {
      themeIcon.innerHTML = `
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
      `;
    }
  }
}

function toggleTheme() {
  const body = document.body;
  body.classList.toggle("light-mode");
  const isLight = body.classList.contains("light-mode");
  localStorage.setItem("light_mode", isLight);

  const icon = document.getElementById("theme-icon");
  if (icon) {
    if (isLight) {
      icon.innerHTML = `
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
      `;
      showToast("Switched to Royal Light Theme");
    } else {
      icon.innerHTML = `
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
      `;
      showToast("Switched to Luxury Dark Theme");
    }
  }
}

// Render dynamic secondary nav
function renderDynamicNav() {
  const navContainer = document.getElementById("dynamic-nav");
  if (!navContainer) return;

  // Start with Home and Shop
  let html = `
    <li style="color:var(--primary); border-bottom:2px solid var(--primary); padding-bottom:0.25rem; cursor:pointer;" onclick="switchView('shop')">Home</li>
    <li style="cursor:pointer; display:flex; align-items:center; gap:0.25rem;" onclick="switchView('shop')">Shop <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:-2px;"><path d="m6 9 6 6 6-6"/></svg></li>
  `;

  // Dynamically append categories
  app.categories.forEach(cat => {
    html += `<li style="cursor:pointer; color:var(--text-secondary);" onclick="document.querySelector('.category-tab[data-category=\\'${cat.name}\\']')?.click();">${cat.name}</li>`;
  });

  // End with Our Story
  html += `
    <li style="cursor:pointer; color:var(--text-secondary);">Our Story</li>
  `;

  navContainer.innerHTML = html;
}


// Render dynamic category tabs
function renderStorefrontCategories() {
  const container = document.querySelector(".category-tabs");
  if (!container) return;

  const activeTab = container.querySelector(".category-card.active");
  const activeCategory = activeTab ? activeTab.dataset.category : "All";

  let html = "";
  app.categories.forEach(cat => {
    html += `
      <div class="category-card ${activeCategory === cat.slug ? 'active' : ''}" data-category="${cat.slug}" style="cursor:pointer; display:flex; flex-direction:column; gap:1rem; transition: transform 0.2s;">
        <img src="${cat.image || 'https://images.unsplash.com/photo-1596450514735-111a2fe02935?auto=format&fit=crop&w=400&q=80'}" alt="${cat.name}" style="width:100%; aspect-ratio:1/1; object-fit:cover; border-radius:12px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);">
        <div>
          <h3 style="font-size:1.1rem; font-weight:600; color:var(--text-primary); margin:0 0 0.25rem 0;">${cat.name}</h3>
          <span style="color:#b45309; font-size:0.85rem; font-weight:500;">Explore &rarr;</span>
        </div>
      </div>
    `;
  });
  container.innerHTML = html;

  const catTabs = container.querySelectorAll(".category-card");
  const searchInput = document.getElementById("search-input");
  catTabs.forEach(tab => {
    tab.addEventListener("click", (e) => {
      catTabs.forEach(t => t.classList.remove("active"));
      tab.classList.add("active");

      const category = tab.dataset.category;
      const searchVal = searchInput ? searchInput.value : "";
      renderStorefront(category, searchVal);
    });
  });
}

function populateCategorySelects() {
  const selectIds = ["new-pcategory", "edit-pcategory"];
  selectIds.forEach(id => {
    const select = document.getElementById(id);
    if (select) {
      select.innerHTML = "";
      app.categories.forEach(cat => {
        select.innerHTML += `<option value="${cat.slug}">${cat.name}</option>`;
      });
    }
  });
}

// Render Main Storefront Products
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
        <svg viewBox="0 0 24 24" style="margin: 0 auto 1.5rem; display: block;" xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <h3 style="font-family: var(--font-body); font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">No products found</h3>
        <p>Try matching another category, title, or keyword.</p>
      </div>
    `;
    return;
  }

  filtered.forEach(p => {
    const card = document.createElement("div");
    card.className = "product-card";
    const originalPrice = p.price + Math.floor(p.price * 0.15); // Add ~15% for original price display
    card.innerHTML = `
      <div style="border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; background: white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); height: 100%; display: flex; flex-direction: column;">
        <div style="position: relative; width: 100%; aspect-ratio: 1/1; overflow: hidden; background: #f8fafc;">
          <span style="position: absolute; top: 0.75rem; left: 0.75rem; background: #b91c1c; color: white; padding: 0.25rem 0.5rem; font-size: 0.65rem; font-weight: 700; border-radius: 4px; z-index: 2;">10% OFF</span>
          <button style="position: absolute; top: 0.75rem; right: 0.75rem; background: white; border: none; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); cursor: pointer; z-index: 2; color: #94a3b8;">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
          </button>
          <img src="${p.image}" alt="${p.name}" loading="lazy" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div style="padding: 1.25rem; display: flex; flex-direction: column; flex: 1;">
          <div style="font-size: 0.65rem; font-weight: 700; letter-spacing: 1px; color: #64748b; text-transform: uppercase; margin-bottom: 0.35rem;">${p.category}</div>
          <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0 0 0.5rem 0; line-height: 1.3;">${p.name}</h3>
          <div style="display: flex; align-items: center; gap: 0.25rem; margin-bottom: 0.75rem;">
             <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#fbbf24" stroke="#fbbf24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
             <span style="font-size: 0.85rem; font-weight: 600; color: #334155; margin-left: 2px;">4.8</span> 
             <span style="font-size: 0.8rem; color: #94a3b8;">(134)</span>
          </div>
          <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-bottom: 1.25rem; margin-top: auto;">
             <span style="font-size: 1.25rem; font-weight: 800; color: #991b1b;">₹${p.price.toLocaleString("en-IN")}</span>
             <span style="font-size: 0.85rem; font-weight: 500; color: #94a3b8; text-decoration: line-through;">₹${originalPrice.toLocaleString("en-IN")}</span>
          </div>
          <button class="add-cart-btn" data-id="${p.id}" style="width: 100%; background: #991b1b; color: white; border: none; padding: 0.75rem; border-radius: 6px; font-weight: 600; display: flex; justify-content: center; align-items: center; gap: 0.5rem; cursor: pointer;">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
            Add To Cart
          </button>
        </div>
      </div>
    `;

    card.addEventListener("click", (e) => {
      if (e.target.closest(".add-cart-btn")) {
        e.stopPropagation();
        const id = e.target.closest(".add-cart-btn").dataset.id;
        const success = app.addToCart(id);
        if (success) {
          updateCartUI();
          showToast("Added item to your Cart");
        }
        return;
      }
      renderProductDetailPage(p);
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
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
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
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2M10 11v6M14 11v6"/></svg>
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

// Authentication Modal and Dropdown menu state
function updateNavBarState() {
  const profileContainer = document.getElementById("user-menu-container");
  if (!profileContainer) return;

  if (app.currentUser) {
    profileContainer.innerHTML = `
      <button class="btn-primary" id="profile-menu-trigger">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span>${app.currentUser.name}</span>
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
      </button>
      <div class="profile-dropdown" id="profile-dropdown-menu">
        <div class="dropdown-header">
          <h4>${app.currentUser.name}</h4>
          <p>${app.currentUser.email}</p>
        </div>
        ${app.currentUser.role === 'admin' ? `
          <button class="dropdown-item" id="admin-view-btn">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
            Admin Dashboard
          </button>
        ` : `
          <button class="dropdown-item" id="account-view-btn">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            My Account
          </button>
        `}
        <button class="dropdown-item" id="back-to-shop-btn">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7M5 12h14"/></svg>
          Browse Store
        </button>
        <button class="dropdown-item danger" id="logout-btn">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
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

    const logoutBtn = document.getElementById("logout-btn");
    logoutBtn.addEventListener("click", () => {
      app.logout();
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

    const accountViewBtn = document.getElementById("account-view-btn");
    if (accountViewBtn) {
      accountViewBtn.addEventListener("click", () => {
        switchView("account");
        menu.classList.remove("active");
      });
    }

    const backShopBtn = document.getElementById("back-to-shop-btn");
    backShopBtn.addEventListener("click", () => {
      switchView("shop");
      menu.classList.remove("active");
    });

  } else {
    profileContainer.innerHTML = `
      <button class="btn-primary" id="open-auth-btn">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
        <span>Login / Register</span>
      </button>
    `;

    const openAuth = document.getElementById("open-auth-btn");
    openAuth.addEventListener("click", () => {
      switchView('auth');
    });
  }
}

// Switch between Main Shop, Admin Dashboard, Account/Dashboard, Product Detail, and Auth
function switchView(view, updateHistory = true, productId = null) {
  const homeView = document.getElementById("home-view");
  const heroSection = document.getElementById("hero-section");
  const catSection = document.getElementById("categories-section");
  const shopSection = document.getElementById("shop-section");
  const adminView = document.getElementById("admin-view");
  const accountView = document.getElementById("account-view");
  const detailView = document.getElementById("product-detail-view");
  const authView = document.getElementById("auth-view");
  const mainNavbar = document.querySelector('.navbar');
  const noticeBar = document.querySelector('.notice-bar');
  const footer = document.querySelector('.main-footer') || document.querySelector('footer');

  // Hide all primary views first to guarantee NO content bleeding
  if (homeView) homeView.style.display = "none";
  if (heroSection) heroSection.style.display = "none";
  if (catSection) catSection.style.display = "none";
  if (shopSection) shopSection.style.display = "none";
  if (detailView) detailView.style.display = "none";
  if (authView) authView.style.display = "none";
  if (accountView) {
    accountView.style.display = "none";
    accountView.classList.remove("active");
  }

  if (view === "shop") {
    if (homeView) homeView.style.display = "block";
    if (heroSection) heroSection.style.display = "block";
    if (catSection) catSection.style.display = "block";
    if (shopSection) shopSection.style.display = "block";
    if (mainNavbar) mainNavbar.style.display = 'block';
    if (noticeBar) noticeBar.style.display = 'flex';
    if (footer) footer.style.display = 'block';

    renderStorefront();
    if (updateHistory) {
      updateURLState('/ecommerce/website');
    }
    window.scrollTo({ top: 0, behavior: 'instant' });
  } else if (view === "admin") {
    const isLocalFile = window.location.protocol === 'file:';
    window.location.href = isLocalFile ? 'superpanel.html' : '/ecommerce/admin';
  } else if (view === "account" || view === "dashboard") {
    if (!app.currentUser) {
      showToast("Please sign in to access your dashboard", "warning");
      switchView("auth", updateHistory);
      return;
    }
    if (accountView) {
      accountView.style.display = "block";
      accountView.classList.add("active");
    }
    if (mainNavbar) mainNavbar.style.display = 'block';
    if (noticeBar) noticeBar.style.display = 'flex';
    if (footer) footer.style.display = 'block';
    renderAccountDashboard();
    if (updateHistory) {
      updateURLState('/ecommerce/dashboard');
    }
    window.scrollTo({ top: 0, behavior: 'instant' });
  } else if (view === "product-detail") {
    if (detailView) detailView.style.display = "block";
    if (mainNavbar) mainNavbar.style.display = 'block';
    if (noticeBar) noticeBar.style.display = 'flex';
    if (footer) footer.style.display = 'block';

    if (updateHistory && productId) {
      const cleanId = String(productId).replace('prod-', '');
      updateURLState(`/ecommerce/product/${cleanId}`);
    }
    window.scrollTo({ top: 0, behavior: 'instant' });
  } else if (view === "auth") {
    if (app.currentUser) {
      if (!updateHistory) {
        window.location.href = "/ecommerce/website";
      } else {
        switchView("shop", updateHistory);
      }
      return;
    }
    if (mainNavbar) mainNavbar.style.display = 'none';
    if (noticeBar) noticeBar.style.display = 'none';
    if (footer) footer.style.display = 'none';

    if (authView) authView.style.display = "flex";

    const path = window.location.pathname;
    const hash = window.location.hash;
    const isRegister = path.includes('/register') || hash.includes('register');
    app.switchAuthTab(isRegister ? 'signup' : 'signin', updateHistory);
  }
}

// Function to switch between Login and Register tabs on dedicated auth page
app.switchAuthTab = function(type, updateHistory = true) {
  const isRegister = type === 'signup' || type === 'register';
  const title = document.getElementById("auth-view-title");
  const emailForm = document.getElementById("auth-email-form");
  const signupForm = document.getElementById("auth-signup-form");

  if (isRegister) {
    if (title) title.innerText = "Create Account";
    if (emailForm) emailForm.style.display = "none";
    if (signupForm) signupForm.style.display = "block";
    if (updateHistory) updateURLState('/ecommerce/register');
  } else {
    if (title) title.innerText = "Login";
    if (signupForm) signupForm.style.display = "none";
    if (emailForm) emailForm.style.display = "block";
    if (updateHistory) updateURLState('/ecommerce/login');
  }
};

function getRouteBase() {
  if (window.location.protocol === 'file:') return '';
  return window.location.pathname.startsWith('/ecommerce') ? '/ecommerce' : '';
}

function updateURLState(targetPath) {
  const isLocalFile = window.location.protocol === 'file:';
  if (isLocalFile) {
    if (targetPath.includes('/admin')) {
      window.location.hash = 'admin';
    } else if (targetPath.includes('/dashboard') || targetPath.includes('/account')) {
      window.location.hash = 'dashboard';
    } else if (targetPath.includes('/register')) {
      window.location.hash = 'register';
    } else if (targetPath.includes('/login')) {
      window.location.hash = 'login';
    } else if (targetPath.includes('/product/')) {
      const id = targetPath.split('/product/')[1].split('?')[0];
      window.location.hash = `product/${id}`;
    } else {
      window.location.hash = '';
    }
  } else {
    let fullPath = targetPath;
    const base = getRouteBase();
    if (base === '/ecommerce' && !fullPath.startsWith('/ecommerce')) {
      fullPath = '/ecommerce' + (fullPath.startsWith('/') ? fullPath : '/' + fullPath);
    }
    if (window.location.pathname !== fullPath) {
      window.history.pushState({ view: fullPath }, "", fullPath);
    }
  }
}

function navigateTo(route) {
  const isLocalFile = window.location.protocol === 'file:';
  if (isLocalFile) {
    if (route.includes('/admin')) window.location.hash = 'admin';
    else if (route.includes('/dashboard') || route.includes('/account')) window.location.hash = 'dashboard';
    else if (route.includes('/register')) window.location.hash = 'register';
    else if (route.includes('/login')) window.location.hash = 'login';
    else if (route.includes('/product/')) window.location.hash = `product/${route.split('/product/')[1]}`;
    else window.location.hash = '';
    handleRouting();
    return;
  }
  updateURLState(route);
  handleRouting();
}
window.navigateTo = navigateTo;

function handleRouting() {
  const isLocalFile = window.location.protocol === 'file:';
  const path = window.location.pathname;
  const hash = window.location.hash;
  const searchParams = new URLSearchParams(window.location.search);

  if (isLocalFile) {
    if (hash === '#admin' || hash === '#superpanel' || hash === '#/superpanel') {
      window.location.href = 'superpanel.html';
      return;
    }
    if (hash === '#dashboard' || hash === '#account' || hash === '#/account') {
      if (app.currentUser) {
        switchView('account', false);
      } else {
        showToast("Please sign in to access your dashboard", "info");
        switchView('auth', false);
        app.switchAuthTab('signin', false);
      }
    } else if (hash.includes('register')) {
      switchView('auth', false);
      app.switchAuthTab('signup', false);
    } else if (hash.includes('login')) {
      switchView('auth', false);
      app.switchAuthTab('signin', false);
    } else if (hash.includes('product') || searchParams.has('id')) {
      let targetId = searchParams.get('id');
      if (!targetId && hash.includes('product/')) {
        targetId = hash.split('product/')[1].split('?')[0];
      } else if (!targetId && hash.includes('id=')) {
        targetId = hash.split('id=')[1].split('&')[0];
      }
      resolveAndRenderProduct(targetId || '20');
    } else {
      switchView('shop', false);
    }
  } else {
    // Web routing
    if (path.endsWith('/admin') || path.endsWith('/admin/') || path.includes('/superpanel')) {
      window.location.href = '/ecommerce/admin';
      return;
    }

    if (path.includes('/register')) {
      switchView('auth', false);
      app.switchAuthTab('signup', false);
    } else if (path.includes('/login')) {
      switchView('auth', false);
      app.switchAuthTab('signin', false);
    } else if (path.includes('/dashboard') || path.includes('/account')) {
      if (app.currentUser) {
        if (app.currentUser.role === 'admin') {
          window.location.href = '/ecommerce/admin';
        } else {
          switchView('account', false);
        }
      } else {
        showToast("Please sign in to access your dashboard", "info");
        switchView('auth', false);
        app.switchAuthTab('signin', false);
      }
    } else if (path.includes('/product') || searchParams.has('id')) {
      let targetId = null;
      if (path.includes('/product/')) {
        targetId = path.split('/product/')[1].split('/')[0].split('?')[0];
      } else if (searchParams.has('id')) {
        targetId = searchParams.get('id');
      }
      resolveAndRenderProduct(targetId || '20');
    } else {
      // Default to storefront website
      switchView('shop', false);
    }
  }
}

function resolveAndRenderProduct(targetId) {
  let p = null;
  if (targetId) {
    p = app.products.find(prod => 
      String(prod.id).toLowerCase() === String(targetId).toLowerCase() ||
      String(prod.id) === `prod-${targetId}` ||
      String(prod.id).replace('prod-', '') === String(targetId)
    );
    if (!p && !isNaN(parseInt(targetId))) {
      const idx = parseInt(targetId);
      p = app.products[idx - 1] || app.products[idx];
    }
  }
  if (!p) p = app.products[0];
  if (p) {
    renderProductDetailPage(p, false);
  } else {
    switchView('shop', false);
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

// Product Details Page Render
function renderProductDetailPage(product, updateHistory = true) {
  const detailView = document.getElementById("product-detail-view");
  if (!detailView) return;

  // Find related products in the same category
  const relatedProducts = app.products
    .filter(p => p.category === product.category && p.id !== product.id)
    .slice(0, 4); // max 4

  const originalPrice = product.price + Math.floor(product.price * 0.15);

  let relatedHTML = '';
  if (relatedProducts.length > 0) {
    relatedHTML = `
      <div style="margin-top: 4rem; padding-top: 3rem; border-top: 1px solid var(--border-color);">
        <h3 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 2rem;">Recommended from ${product.category}</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;" id="related-products-grid">
          <!-- Populated below -->
        </div>
      </div>
    `;
  }

  detailView.innerHTML = `
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 2rem;">
      <!-- Breadcrumb -->
      <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.5rem;">
        <a href="javascript:void(0)" onclick="switchView('shop')" style="color: #b45309; text-decoration: none; font-weight: 500;">Home</a> 
        <span style="color: #cbd5e1;">&rsaquo;</span> 
        <a href="javascript:void(0)" onclick="switchView('shop'); setTimeout(() => document.querySelector(\`.category-card[data-category='\${product.category}']\`)?.click(), 100);" style="color: #64748b; text-decoration: none;">${product.category}</a> 
        <span style="color: #cbd5e1;">&rsaquo;</span> 
        <span style="color: #0f172a; font-weight: 600;">${product.name}</span>
      </div>

      <!-- Product Area -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; background: white; padding: 3rem; border-radius: 24px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); align-items: start;">
        <!-- Image Gallery with Zoom Container -->
        <div class="product-zoom-container" style="border-radius: 16px; overflow: hidden; background: #f8fafc; border: 1px solid var(--border-color); position: relative; height: 500px; cursor: crosshair; display: flex; align-items: center; justify-content: center;">
          <img src="${product.image}" alt="${product.name}" class="product-zoom-img" style="width: 100%; height: 100%; object-fit: contain; transition: transform 0.15s ease-out;">
          <div style="position: absolute; bottom: 1rem; left: 1rem; background: rgba(15,23,42,0.75); backdrop-filter: blur(4px); color: white; padding: 0.35rem 0.75rem; border-radius: 99px; font-size: 0.75rem; font-weight: 500; display: flex; align-items: center; gap: 0.35rem; pointer-events: none; z-index: 5;">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
            Hover to Zoom (2x)
          </div>
        </div>

        <!-- Info -->
        <div style="display: flex; flex-direction: column;">
          <div style="font-size: 0.75rem; font-weight: 700; letter-spacing: 2px; color: #b45309; text-transform: uppercase; margin-bottom: 0.5rem;">${product.category}</div>
          <h1 style="font-size: 2.25rem; font-weight: 800; color: #0f172a; margin: 0 0 1rem 0; line-height: 1.2;">${product.name}</h1>
          
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
             <div style="display:flex; gap:0.1rem;">
               ${[1,2,3,4,5].map(() => `<svg viewBox="0 0 24 24" width="18" height="18" fill="#fbbf24" stroke="#fbbf24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`).join('')}
             </div>
             <span style="font-size: 0.95rem; color: #64748b; margin-left: 0.5rem;">(134 Reviews)</span>
             <span style="font-size: 0.75rem; font-weight: 600; background: #ecfdf5; color: #059669; padding: 0.2rem 0.6rem; border-radius: 99px; margin-left: 0.5rem;">Verified Authentic</span>
          </div>

          <div style="display: flex; align-items: baseline; gap: 1rem; margin-bottom: 1.5rem;">
             <span style="font-size: 2.5rem; font-weight: 800; color: #991b1b;">₹${product.price.toLocaleString("en-IN")}</span>
             <span style="font-size: 1.25rem; font-weight: 500; color: #94a3b8; text-decoration: line-through;">₹${originalPrice.toLocaleString("en-IN")}</span>
             <span style="background: #fef2f2; color: #991b1b; padding: 0.25rem 0.6rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">15% OFF</span>
          </div>

          <p style="font-size: 1.05rem; line-height: 1.7; color: #475569; margin-bottom: 2rem;">
            ${product.description}
          </p>

          <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; background: #f8fafc; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; height: 50px;">
              <button class="qty-dec" style="width: 44px; height: 100%; background: none; border: none; font-size: 1.25rem; cursor: pointer; color: #64748b; font-weight: 600;">-</button>
              <span class="qty-val" style="width: 44px; text-align: center; font-weight: 700; font-size: 1.1rem; color: #0f172a;">1</span>
              <button class="qty-inc" style="width: 44px; height: 100%; background: none; border: none; font-size: 1.25rem; cursor: pointer; color: #64748b; font-weight: 600;">+</button>
            </div>
            <button class="add-detail-cart" style="flex: 1; background: #831843; color: white; border: none; height: 50px; border-radius: 8px; font-weight: 600; font-size: 1.05rem; display: flex; justify-content: center; align-items: center; gap: 0.75rem; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(131,24,67,0.2);">
              <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
              Add to Bag
            </button>
          </div>
          
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; color: #64748b; font-size: 0.85rem;">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#b45309" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              Pan-India Delivery (3-5 Days)
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: #64748b; font-size: 0.85rem;">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#b45309" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              100% Authentic Guaranteed
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: #64748b; font-size: 0.85rem;">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#b45309" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
              7 Days Hassle-Free Returns
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: #64748b; font-size: 0.85rem;">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#b45309" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><path d="M2 10h20"/></svg>
              Secure Online Payments
            </div>
          </div>
        </div>
      </div>
      
      ${relatedHTML}
    </div>
  `;

  // Quantity controls
  let qty = 1;
  const qtyVal = detailView.querySelector(".qty-val");
  
  // Image Zoom logic (Smooth cursor tracking & 2.2x magnification)
  const zoomContainer = detailView.querySelector('.product-zoom-container');
  const zoomImg = detailView.querySelector('.product-zoom-img');
  if (zoomContainer && zoomImg) {
    zoomContainer.addEventListener('mousemove', (e) => {
      const rect = zoomContainer.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      const xPercent = (x / rect.width) * 100;
      const yPercent = (y / rect.height) * 100;
      zoomImg.style.transformOrigin = `${xPercent}% ${yPercent}%`;
      zoomImg.style.transform = 'scale(2.2)';
      zoomImg.style.objectFit = 'cover';
    });
    zoomContainer.addEventListener('mouseleave', () => {
      zoomImg.style.transformOrigin = 'center';
      zoomImg.style.transform = 'scale(1)';
      zoomImg.style.objectFit = 'contain';
    });
  }

  detailView.querySelector(".qty-dec").addEventListener("click", () => {
    if (qty > 1) {
      qty--;
      qtyVal.innerText = qty;
    }
  });
  detailView.querySelector(".qty-inc").addEventListener("click", () => {
    qty++;
    qtyVal.innerText = qty;
  });

  // Add to cart
  detailView.querySelector(".add-detail-cart").addEventListener("click", () => {
    const success = app.addToCart(product.id, qty);
    if (success) {
      updateCartUI();
      showToast(`Added ${qty} item(s) to Cart`);
    }
  });

  // Render related products
  if (relatedProducts.length > 0) {
    const relatedGrid = detailView.querySelector("#related-products-grid");
    relatedProducts.forEach(p => {
      const card = document.createElement("div");
      card.className = "product-card";
      card.style.cursor = "pointer";
      const oPrice = p.price + Math.floor(p.price * 0.15);
      card.innerHTML = `
        <div style="border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; background: white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); height: 100%; display: flex; flex-direction: column;">
          <div style="position: relative; width: 100%; aspect-ratio: 1/1; overflow: hidden; background: #f8fafc;">
            <img src="${p.image}" alt="${p.name}" loading="lazy" style="width: 100%; height: 100%; object-fit: cover;">
          </div>
          <div style="padding: 1.25rem; display: flex; flex-direction: column; flex: 1;">
            <div style="font-size: 0.65rem; font-weight: 700; letter-spacing: 1px; color: #64748b; text-transform: uppercase; margin-bottom: 0.35rem;">${p.category}</div>
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0 0 0.5rem 0; line-height: 1.3;">${p.name}</h3>
            <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-bottom: 1.25rem; margin-top: auto;">
               <span style="font-size: 1.25rem; font-weight: 800; color: #991b1b;">₹${p.price.toLocaleString("en-IN")}</span>
               <span style="font-size: 0.85rem; font-weight: 500; color: #94a3b8; text-decoration: line-through;">₹${oPrice.toLocaleString("en-IN")}</span>
            </div>
            <button class="add-cart-btn" data-id="${p.id}" style="width: 100%; background: #991b1b; color: white; border: none; padding: 0.75rem; border-radius: 6px; font-weight: 600; display: flex; justify-content: center; align-items: center; gap: 0.5rem; cursor: pointer;">
              <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
              Add To Cart
            </button>
          </div>
        </div>
      `;
      card.addEventListener("click", (e) => {
        if (e.target.closest(".add-cart-btn")) {
          e.stopPropagation();
          const success = app.addToCart(p.id);
          if (success) {
            updateCartUI();
            showToast("Added item to your Cart");
          }
          return;
        }
        renderProductDetailPage(p, true);
      });
      relatedGrid.appendChild(card);
    });
  }
  
  switchView('product-detail', updateHistory, product.id);
}

// ==========================================
// ACCOUNT DASHBOARD VIEW
// ==========================================
function renderAccountDashboard() {
  if (!app.currentUser) return;

  // 1. Fill profile details
  document.getElementById("profile-name").value = app.currentUser.name;
  document.getElementById("profile-email").value = app.currentUser.email;
  document.getElementById("profile-phone").value = app.currentUser.phone;
  document.getElementById("profile-password").value = "";

  // 2. Render addresses
  renderAddressesList();

  // 3. Render user order history
  renderUserOrdersHistory();
}

function renderAddressesList() {
  const container = document.getElementById("addresses-list");
  if (!container) return;

  container.innerHTML = "";
  const addresses = app.currentUser.addresses || [];

  if (addresses.length === 0) {
    container.innerHTML = `<p style="color:var(--text-muted); font-size:0.9rem;">No shipping destinations registered yet.</p>`;
    return;
  }

  addresses.forEach(addr => {
    const card = document.createElement("div");
    card.className = `address-card ${addr.default ? 'default' : ''}`;
    card.innerHTML = `
      <div>
        <div style="font-weight:700; display:flex; align-items:center;">
          ${addr.label}
          ${addr.default ? `<span class="address-badge">Default</span>` : ''}
        </div>
        <div style="margin-top:0.4rem; font-size:0.85rem; color:var(--text-secondary);">
          ${addr.street}<br>${addr.city} - ${addr.zip}
        </div>
      </div>
      <div style="display:flex; gap:0.5rem;">
        ${!addr.default ? `<button onclick="setDefaultAddress('${addr.id}')" class="badge-status pending" style="cursor:pointer; font-size:0.75rem; padding: 0.15rem 0.5rem;">Set Default</button>` : ''}
        <button onclick="editAddress('${addr.id}')" class="badge-status pending" style="cursor:pointer; font-size:0.75rem; padding: 0.15rem 0.5rem; background:rgba(251,191,36,0.05); color:var(--primary);">Edit</button>
        <button onclick="deleteAddress('${addr.id}')" class="badge-status pending" style="cursor:pointer; font-size:0.75rem; padding: 0.15rem 0.5rem; background:rgba(239,68,68,0.05); color:var(--danger);">Delete</button>
      </div>
    `;
    container.appendChild(card);
  });
}

function openAddAddressForm() {
  document.getElementById("address-form-title").innerText = "Add New Shipping Destination";
  document.getElementById("address-id").value = "";
  document.getElementById("addr-label").value = "";
  document.getElementById("addr-street").value = "";
  document.getElementById("addr-city").value = "Jodhpur";
  document.getElementById("addr-zip").value = "";
  document.getElementById("addr-default").checked = (app.currentUser.addresses || []).length === 0;

  document.getElementById("address-form-container").style.display = "block";
  document.getElementById("address-form-container").scrollIntoView({ behavior: 'smooth' });
}

function closeAddressForm() {
  document.getElementById("address-form-container").style.display = "none";
}

function editAddress(id) {
  const addr = app.currentUser.addresses.find(a => a.id === id);
  if (!addr) return;

  document.getElementById("address-form-title").innerText = "Edit Shipping Address";
  document.getElementById("address-id").value = addr.id;
  document.getElementById("addr-label").value = addr.label;
  document.getElementById("addr-street").value = addr.street;
  document.getElementById("addr-city").value = addr.city;
  document.getElementById("addr-zip").value = addr.zip;
  document.getElementById("addr-default").checked = addr.default || false;

  document.getElementById("address-form-container").style.display = "block";
  document.getElementById("address-form-container").scrollIntoView({ behavior: 'smooth' });
}

function deleteAddress(id) {
  if (confirm("Are you sure you want to remove this address?")) {
    app.currentUser.addresses = app.currentUser.addresses.filter(a => a.id !== id);
    app.saveState();
    showToast("Address deleted successfully");
    renderAddressesList();
  }
}

function setDefaultAddress(id) {
  app.currentUser.addresses.forEach(a => a.default = (a.id === id));
  app.saveState();
  showToast("Default address updated");
  renderAddressesList();
}

function renderUserOrdersHistory() {
  const container = document.getElementById("account-orders-history-list");
  if (!container) return;

  const myOrders = app.orders.filter(o => o.userEmail === app.currentUser.email);
  if (myOrders.length === 0) {
    container.innerHTML = `
      <div style="text-align:center; padding: 4rem 1rem; background: var(--bg-card); border-radius:20px; border:1px solid var(--border-color); color:var(--text-muted);">
        <svg viewBox="0 0 24 24" style="margin: 0 auto 1rem; display:block;" xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        <h3 style="font-family: var(--font-body); font-weight:600; margin-bottom:0.5rem; color:var(--text-primary);">No Orders Found</h3>
        <p>You haven't placed any acquisitions yet. Browse our storefront to check royal treasures!</p>
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
        <div style="display:flex; align-items:center; gap:0.5rem;">
          <span class="badge-status ${o.status.toLowerCase()}">${o.status}</span>
          <button class="badge-status pending" style="cursor:pointer;" onclick="openOrderInvoiceModal('${o.id}')">Invoice</button>
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
        <span style="font-size:0.85rem; color:var(--text-secondary);">Shipment Address: ${o.shippingDetails.street}, ${o.shippingDetails.city}</span>
        <span style="font-weight:800; font-size:1.1rem; color:var(--primary);">Total: ₹${o.total.toLocaleString("en-IN")}</span>
      </div>
    `;
    container.appendChild(card);
  });
}

function openOrderInvoiceModal(orderId) {
  const o = app.orders.find(item => item.id === orderId);
  if (!o) return;

  const modalContent = document.getElementById("invoice-details-content");
  if (!modalContent) return;

  const subtotal = o.items.reduce((acc, item) => acc + (item.product.price * item.quantity), 0);
  const discount = subtotal - o.total;

  modalContent.innerHTML = `
    <div style="text-align: center; border-bottom: 2px dashed var(--border-color); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
      <h2 style="font-family: var(--font-heading); color: var(--primary); font-size: 1.8rem; letter-spacing:1px;">MĀRWĀRI</h2>
      <p style="font-size:0.8rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:2px; margin-top:0.25rem;">Royal Heritage of Rajasthan</p>
      <h3 style="font-size: 1.25rem; font-weight: 700; margin-top: 1rem; color: var(--text-primary);">Acquisition Invoice</h3>
    </div>
    
    <div style="display:flex; justify-content:space-between; font-size:0.85rem; margin-bottom:1.5rem; color:var(--text-secondary);">
      <div>
        <strong>Invoice To:</strong><br>
        Name: ${o.shippingDetails.name}<br>
        Phone: ${o.shippingDetails.phone}<br>
        Address: ${o.shippingDetails.street}, ${o.shippingDetails.city} - ${o.shippingDetails.zip}
      </div>
      <div style="text-align:right;">
        <strong>Invoice Details:</strong><br>
        Order ID: #${o.id.toUpperCase()}<br>
        Date: ${new Date(o.date).toLocaleDateString("en-IN")}<br>
        Status: <span style="font-weight:600; text-transform:uppercase;">${o.status}</span>
      </div>
    </div>

    <table style="width:100%; border-collapse:collapse; margin-bottom:1.5rem; font-size:0.9rem;">
      <thead>
        <tr style="border-bottom:1px solid var(--border-color);">
          <th style="text-align:left; padding:0.5rem; color:var(--text-muted);">Acquisition Treasure</th>
          <th style="text-align:center; padding:0.5rem; color:var(--text-muted);">Qty</th>
          <th style="text-align:right; padding:0.5rem; color:var(--text-muted);">Rate</th>
          <th style="text-align:right; padding:0.5rem; color:var(--text-muted);">Total</th>
        </tr>
      </thead>
      <tbody>
        ${o.items.map(item => `
          <tr style="border-bottom:1px solid rgba(255,255,255,0.02);">
            <td style="padding:0.75rem 0.5rem; font-weight:600;">${item.product.name}</td>
            <td style="padding:0.75rem 0.5rem; text-align:center;">${item.quantity}</td>
            <td style="padding:0.75rem 0.5rem; text-align:right;">₹${item.product.price.toLocaleString("en-IN")}</td>
            <td style="padding:0.75rem 0.5rem; text-align:right; font-weight:700;">₹${(item.product.price * item.quantity).toLocaleString("en-IN")}</td>
          </tr>
        `).join("")}
      </tbody>
    </table>

    <div style="display:flex; flex-direction:column; gap:0.5rem; align-items:flex-end; border-top: 1px solid var(--border-color); padding-top:1rem; font-size:0.9rem;">
      <div style="display:flex; width:220px; justify-content:space-between;">
        <span style="color:var(--text-muted);">Subtotal:</span>
        <span style="font-weight:600;">₹${subtotal.toLocaleString("en-IN")}</span>
      </div>
      ${discount > 0 ? `
        <div style="display:flex; width:220px; justify-content:space-between; color:var(--success);">
          <span>Royal Coupon discount:</span>
          <span>-₹${discount.toLocaleString("en-IN")}</span>
        </div>
      ` : ''}
      <div style="display:flex; width:220px; justify-content:space-between; font-size:1.15rem; font-weight:800; border-top:1px dashed var(--border-color); padding-top:0.5rem;">
        <span>Grand Total:</span>
        <span style="color:var(--primary);">₹${o.total.toLocaleString("en-IN")}</span>
      </div>
    </div>

    <div style="text-align:center; font-size:0.8rem; color:var(--text-muted); margin-top:2rem; border-top: 1px dashed var(--border-color); padding-top: 1rem;">
      <p>Padharo Mhare Des. Thank you for supporting Rajasthan heritage and artisans.</p>
    </div>
  `;

  openModal("order-details-modal");
}

// ==========================================
// ADMIN DASHBOARD CONTROLS
// ==========================================
function renderAdminDashboard() {
  // 1. Statistics Computation
  const activeOrders = app.orders.filter(o => o.status !== "Cancelled");
  const totalSales = activeOrders.reduce((total, o) => total + o.total, 0);
  const productsCount = app.products.length;
  const totalOrders = app.orders.length;
  const customersCount = app.users.filter(u => u.role === "user").length;

  const statsContainer = document.getElementById("admin-stats-container");
  if (statsContainer) {
    statsContainer.innerHTML = `
      <div class="stat-card">
        <div class="stat-icon" style="background: #ede9fe; color: #8b5cf6;">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="stat-details">
          <h4>Total Revenue</h4>
          <p>₹${totalSales.toLocaleString("en-IN")}</p>
          <div style="display:flex; align-items:center; gap:0.5rem; margin-top:0.25rem;">
            <span style="color: #10b981; font-size: 0.75rem; font-weight: 600;">↑ 12.5%</span> 
            <span style="font-size: 0.7rem; color: var(--text-muted);">vs last 30 days</span>
          </div>
        </div>
        <div class="stat-chart" style="margin-left: auto;">
            <svg width="60" height="25" viewBox="0 0 60 25" fill="none" stroke="#8b5cf6" stroke-width="2"><path d="M0,20 Q10,25 20,15 T40,10 T60,5" stroke-linecap="round"/></svg>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background: #e0f2fe; color: #3b82f6;">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
        </div>
        <div class="stat-details">
          <h4>Total Orders</h4>
          <p>${totalOrders}</p>
          <div style="display:flex; align-items:center; gap:0.5rem; margin-top:0.25rem;">
            <span style="color: #10b981; font-size: 0.75rem; font-weight: 600;">↑ 8.4%</span> 
            <span style="font-size: 0.7rem; color: var(--text-muted);">vs last 30 days</span>
          </div>
        </div>
        <div class="stat-chart" style="margin-left: auto;">
            <svg width="60" height="25" viewBox="0 0 60 25" fill="none" stroke="#3b82f6" stroke-width="2"><path d="M0,15 Q10,5 20,20 T40,15 T60,5" stroke-linecap="round"/></svg>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background: #dcfce3; color: #22c55e;">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /></svg>
        </div>
        <div class="stat-details">
          <h4>Customers</h4>
          <p>${customersCount}</p>
          <div style="display:flex; align-items:center; gap:0.5rem; margin-top:0.25rem;">
            <span style="color: #10b981; font-size: 0.75rem; font-weight: 600;">↑ 14.2%</span> 
            <span style="font-size: 0.7rem; color: var(--text-muted);">vs last 30 days</span>
          </div>
        </div>
        <div class="stat-chart" style="margin-left: auto;">
            <svg width="60" height="25" viewBox="0 0 60 25" fill="none" stroke="#22c55e" stroke-width="2"><path d="M0,25 Q15,10 25,20 T45,5 T60,0" stroke-linecap="round"/></svg>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background: #ffedd5; color: #f97316;">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/><path d="M12 7.5V12l3 3"/></svg>
        </div>
        <div class="stat-details">
          <h4>Products</h4>
          <p>${productsCount}</p>
          <div style="display:flex; align-items:center; gap:0.5rem; margin-top:0.25rem;">
            <span style="color: #ef4444; font-size: 0.75rem; font-weight: 600;">⚠ 6 low stock</span>
          </div>
        </div>
        <div class="stat-chart" style="margin-left: auto;">
            <svg width="60" height="25" viewBox="0 0 60 25" fill="none" stroke="#f97316" stroke-width="2"><path d="M0,10 Q10,20 20,5 T40,20 T60,10" stroke-linecap="round"/></svg>
        </div>
      </div>
    `;
  }

  // 2. Render Overview Recent Orders & Best Sellers
  renderOverviewWidgets();

  // 3. Render Admin panels
  renderAdminProductsList();
  renderAdminOrdersList();
  renderAdminCustomersList();
  renderAdminCategoriesList();
  renderAdminCouponsList();
  renderAdminReports();
}

function renderOverviewWidgets() {
  const recentOrdersTable = document.getElementById("overview-recent-orders");
  if (recentOrdersTable) {
    recentOrdersTable.innerHTML = "";
    const sortedOrders = [...app.orders].sort((a, b) => new Date(b.date) - new Date(a.date)).slice(0, 5);

    if (sortedOrders.length === 0) {
      recentOrdersTable.innerHTML = `<tr><td colspan="4" style="text-align:center; color:var(--text-muted); padding:1rem;">No recent orders.</td></tr>`;
    } else {
      sortedOrders.forEach(o => {
        recentOrdersTable.innerHTML += `
          <tr>
            <td><a href="#" onclick="openOrderInvoiceModal('${o.id}'); return false;" style="font-weight:600; color:var(--primary);">#${o.id.slice(-6).toUpperCase()}</a></td>
            <td>${o.shippingDetails.name}</td>
            <td>₹${o.total.toLocaleString("en-IN")}</td>
            <td><span class="badge-status ${o.status.toLowerCase()}">${o.status}</span></td>
          </tr>
        `;
      });
    }
  }

  const bestSellersTable = document.getElementById("overview-best-sellers");
  if (bestSellersTable) {
    bestSellersTable.innerHTML = "";
    // Simulate best sellers by grabbing first 3 products
    const bestSellers = app.products.slice(0, 4);
    bestSellers.forEach(p => {
      bestSellersTable.innerHTML += `
        <tr>
          <td>
            <div class="admin-prod-cell">
              <img src="${p.image}" alt="${p.name}">
              <span>${p.name}</span>
            </div>
          </td>
          <td>${p.category}</td>
          <td>₹${p.price.toLocaleString("en-IN")}</td>
        </tr>
      `;
    });
  }
}

function renderAdminProductsList() {
  const prodTableBody = document.getElementById("admin-products-list-table");
  if (!prodTableBody) return;

  prodTableBody.innerHTML = "";
  if (app.products.length === 0) {
    prodTableBody.innerHTML = `<tr><td colspan="5" style="text-align:center; color:var(--text-muted); padding:2rem;">No products in catalog.</td></tr>`;
    return;
  }

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
      <td>${p.badge ? `<span class="badge-status pending" style="background:rgba(251,191,36,0.15); color:var(--primary);">${p.badge}</span>` : '<span style="color:var(--text-muted); font-size:0.8rem;">None</span>'}</td>
      <td>
        <div style="display:flex; gap:0.5rem;">
          <button class="action-icon-btn edit" data-id="${p.id}" title="Edit Product">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
          </button>
          <button class="action-icon-btn delete" data-id="${p.id}" title="Delete Product">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2M10 11v6M14 11v6"/></svg>
          </button>
        </div>
      </td>
    `;

    row.querySelector(".edit").addEventListener("click", () => {
      openEditProductModal(p.id);
    });

    row.querySelector(".delete").addEventListener("click", () => {
      if (confirm(`Are you sure you want to delete "${p.name}"?`)) {
        app.products = app.products.filter(item => item.id !== p.id);
        app.saveState();
        showToast("Product deleted successfully");
        renderAdminDashboard();
        renderStorefront();
      }
    });

    prodTableBody.appendChild(row);
  });
}

function renderAdminOrdersList() {
  const ordersTableBody = document.getElementById("admin-orders-list-table");
  if (!ordersTableBody) return;

  ordersTableBody.innerHTML = "";
  if (app.orders.length === 0) {
    ordersTableBody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding:2rem;">No orders placed yet.</td></tr>`;
    return;
  }

  app.orders.forEach(o => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td><a href="#" onclick="openOrderInvoiceModal('${o.id}'); return false;" style="font-weight:600; color:var(--primary);">#${o.id.slice(-6).toUpperCase()}</a></td>
      <td>
        <div style="font-weight:600;">${o.shippingDetails.name}</div>
        <div style="font-size:0.75rem; color:var(--text-secondary);">${o.shippingDetails.phone}</div>
      </td>
      <td>${o.items.map(item => `${item.product.name} (x${item.quantity})`).join(", ")}</td>
      <td>₹${o.total.toLocaleString("en-IN")}</td>
      <td>
        <select class="badge-status ${o.status.toLowerCase()} status-select" data-id="${o.id}" style="border: 1px solid var(--border-color); background:var(--bg-app); cursor:pointer;">
          <option value="Pending" ${o.status === 'Pending' ? 'selected' : ''}>Pending</option>
          <option value="Processing" ${o.status === 'Processing' ? 'selected' : ''}>Processing</option>
          <option value="Shipped" ${o.status === 'Shipped' ? 'selected' : ''}>Shipped</option>
          <option value="Delivered" ${o.status === 'Delivered' ? 'selected' : ''}>Delivered</option>
          <option value="Cancelled" ${o.status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
        </select>
      </td>
      <td>
        <div style="display:flex; gap:0.5rem;">
          <button class="badge-status pending invoice-view-btn" onclick="openOrderInvoiceModal('${o.id}')" style="cursor:pointer; font-size:0.75rem; padding: 0.15rem 0.5rem;">Invoice</button>
          ${o.status !== 'Cancelled' && o.status !== 'Delivered' ? `
            <button class="badge-status pending cancel-order-btn" data-id="${o.id}" style="cursor:pointer; font-size:0.75rem; padding: 0.15rem 0.5rem; background:rgba(239,68,68,0.05); color:var(--danger); border-color:rgba(239,68,68,0.1);">Cancel</button>
          ` : ''}
        </div>
      </td>
    `;

    // Dropdown Status Change Handler
    const statusSelect = row.querySelector(".status-select");
    statusSelect.addEventListener("change", (e) => {
      const order = app.orders.find(item => item.id === o.id);
      if (order) {
        order.status = e.target.value;
        app.saveState();
        showToast(`Order status updated to ${e.target.value}`);
        renderAdminDashboard();
      }
    });

    // Cancel Order button
    const cancelBtn = row.querySelector(".cancel-order-btn");
    if (cancelBtn) {
      cancelBtn.addEventListener("click", () => {
        if (confirm("Are you sure you want to cancel this order?")) {
          const order = app.orders.find(item => item.id === o.id);
          if (order) {
            order.status = "Cancelled";
            app.saveState();
            showToast("Order cancelled successfully");
            renderAdminDashboard();
          }
        }
      });
    }

    ordersTableBody.appendChild(row);
  });
}

function renderAdminCustomersList(filterQuery = "") {
  const container = document.getElementById("admin-customers-list-table");
  if (!container) return;

  container.innerHTML = "";
  // Get all user accounts (excluding admin itself)
  const customers = app.users.filter(u => u.role === "user");
  const query = filterQuery.toLowerCase().trim();

  const filtered = customers.filter(c =>
    c.name.toLowerCase().includes(query) ||
    c.email.toLowerCase().includes(query) ||
    c.phone.includes(query)
  );

  if (filtered.length === 0) {
    container.innerHTML = `<tr><td colspan="5" style="text-align:center; color:var(--text-muted); padding:2rem;">No customers found.</td></tr>`;
    return;
  }

  filtered.forEach(c => {
    const row = document.createElement("tr");
    const isBlocked = c.status === "blocked";
    row.innerHTML = `
      <td style="font-weight:600;">${c.name}</td>
      <td>${c.email}</td>
      <td>+91 ${c.phone}</td>
      <td>
        <span class="badge-status ${isBlocked ? 'cancelled' : 'completed'}">
          ${isBlocked ? 'Blocked' : 'Active'}
        </span>
      </td>
      <td>
        <button class="badge-status pending toggle-block-btn" data-email="${c.email}" style="cursor:pointer; font-size:0.75rem; padding: 0.15rem 0.5rem; ${isBlocked ? 'background:rgba(16,185,129,0.05); color:var(--success); border-color:var(--success);' : 'background:rgba(239,68,68,0.05); color:var(--danger); border-color:var(--danger);'}">
          ${isBlocked ? 'Activate' : 'Block User'}
        </button>
      </td>
    `;

    row.querySelector(".toggle-block-btn").addEventListener("click", () => {
      const user = app.users.find(u => u.email === c.email);
      if (user) {
        user.status = user.status === "blocked" ? "active" : "blocked";
        // Force logout if blocked and currently logged in
        if (app.currentUser && app.currentUser.email === user.email && user.status === "blocked") {
          app.logout();
          updateNavBarState();
          switchView("shop");
        }
        app.saveState();
        showToast(`User status updated to ${user.status}`);
        renderAdminDashboard();
      }
    });

    container.appendChild(row);
  });
}

function renderAdminCategoriesList() {
  const container = document.getElementById("admin-categories-list-table");
  if (!container) return;

  container.innerHTML = "";
  app.categories.forEach(cat => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td style="font-weight:600; display:flex; align-items:center; gap:1rem;">
        <img src="${cat.image || 'https://images.unsplash.com/photo-1596450514735-111a2fe02935?auto=format&fit=crop&w=40&q=80'}" style="width:40px; height:40px; object-fit:cover; border-radius:4px; border:1px solid var(--border-color);" alt="${cat.name}">
        ${cat.name}
      </td>
      <td><code>${cat.slug}</code></td>
      <td style="display:flex; gap:0.5rem;">
        <button class="action-icon-btn edit-cat" data-id="${cat.id}">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
        </button>
        <button class="action-icon-btn delete delete-cat" data-id="${cat.id}">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2M10 11v6M14 11v6"/></svg>
        </button>
      </td>
    `;

    row.querySelector(".edit-cat").addEventListener("click", () => {
      document.getElementById("edit-cat-old-slug").value = cat.slug;
      document.getElementById("edit-cat-name").value = cat.name;
      document.getElementById("edit-cat-slug").value = cat.slug;
      document.getElementById("edit-cat-image").value = cat.image || "";
      openModal("edit-category-modal");
    });

    row.querySelector(".delete-cat").addEventListener("click", () => {
      // Prevent deleting if default or in use
      if (app.categories.length <= 1) {
        showToast("At least one category must remain in database", "danger");
        return;
      }
      if (confirm(`Delete category "${cat.name}"? Products in this category will not be deleted but storefront filter lists will update.`)) {
        app.categories = app.categories.filter(c => c.id !== cat.id);
        app.saveState();
        showToast("Category deleted successfully");
        renderAdminDashboard();
        renderStorefrontCategories();
        populateCategorySelects();
      }
    });

    container.appendChild(row);
  });
}

function renderAdminCouponsList() {
  const container = document.getElementById("admin-coupons-list-table");
  if (!container) return;

  container.innerHTML = "";
  if (app.coupons.length === 0) {
    container.innerHTML = `<tr><td colspan="5" style="text-align:center; color:var(--text-muted); padding:1rem;">No discount campaigns launched.</td></tr>`;
    return;
  }

  app.coupons.forEach(c => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td style="font-weight:700; color:var(--primary); font-family:monospace; font-size:1rem;">${c.code}</td>
      <td style="text-transform:capitalize;">${c.type}</td>
      <td>${c.type === "percentage" ? `${c.amount}%` : `₹${c.amount}`}</td>
      <td>${c.expiry}</td>
      <td>
        <button class="action-icon-btn delete delete-cp" data-id="${c.id}">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2M10 11v6M14 11v6"/></svg>
        </button>
      </td>
    `;

    row.querySelector(".delete-cp").addEventListener("click", () => {
      if (confirm(`Remove coupon campaign "${c.code}"?`)) {
        app.coupons = app.coupons.filter(item => item.id !== c.id);
        app.saveState();
        showToast("Coupon removed successfully");
        renderAdminDashboard();
      }
    });

    container.appendChild(row);
  });
}

function renderAdminReports() {
  const activeOrders = app.orders.filter(o => o.status !== "Cancelled");

  // 1. Average Order Value
  const aov = activeOrders.length > 0
    ? Math.round(activeOrders.reduce((acc, o) => acc + o.total, 0) / activeOrders.length)
    : 0;
  const aovEl = document.getElementById("report-aov");
  if (aovEl) aovEl.innerText = `₹${aov.toLocaleString("en-IN")}`;

  // 2. Net Revenue
  const revenue = activeOrders.reduce((acc, o) => acc + o.total, 0);
  const netRevEl = document.getElementById("report-net-revenue");
  if (netRevEl) netRevEl.innerText = `₹${revenue.toLocaleString("en-IN")}`;

  // 3. Customer Growth
  const userGrowth = app.users.filter(u => u.role === "user").length;
  const custGrowthEl = document.getElementById("report-customer-growth");
  if (custGrowthEl) custGrowthEl.innerText = userGrowth;

  // 4. Revenue By Category
  const categoryRevMap = {};
  app.categories.forEach(cat => categoryRevMap[cat.slug] = { name: cat.name, sales: 0 });

  activeOrders.forEach(o => {
    o.items.forEach(item => {
      const catSlug = item.product.category;
      const amount = item.product.price * item.quantity;
      if (categoryRevMap[catSlug]) {
        categoryRevMap[catSlug].sales += amount;
      } else {
        categoryRevMap[catSlug] = { name: catSlug, sales: amount };
      }
    });
  });

  const catListContainer = document.getElementById("report-category-list");
  if (catListContainer) {
    catListContainer.innerHTML = "";
    const totalCategorySales = Object.values(categoryRevMap).reduce((acc, val) => acc + val.sales, 0) || 1;

    Object.values(categoryRevMap).forEach(cat => {
      const pct = Math.round((cat.sales / totalCategorySales) * 100);
      catListContainer.innerHTML += `
        <div class="report-bar-container">
          <div class="report-bar-label">
            <span>${cat.name}</span>
            <span>₹${cat.sales.toLocaleString("en-IN")} (${pct}%)</span>
          </div>
          <div class="report-bar-track">
            <div class="report-bar-fill" style="width: ${pct}%;"></div>
          </div>
        </div>
      `;
    });
  }

  // 5. Analytical text report
  const analyticsContainer = document.getElementById("report-sales-analytics");
  if (analyticsContainer) {
    const totalItems = activeOrders.reduce((acc, o) => acc + o.items.reduce((sum, i) => sum + i.quantity, 0), 0);
    const completedOrders = activeOrders.filter(o => o.status === "Delivered").length;
    const pendingOrders = activeOrders.filter(o => o.status === "Pending" || o.status === "Processing" || o.status === "Shipped").length;

    analyticsContainer.innerHTML = `
      <div style="font-size:0.9rem; line-height:1.75; display:flex; flex-direction:column; gap:0.6rem;">
        <div style="display:flex; justify-content:space-between; border-bottom:1px solid rgba(255,255,255,0.02); padding-bottom:0.4rem;">
          <span style="color:var(--text-muted);">Total Treasures Sold:</span>
          <span style="font-weight:700;">${totalItems} Items</span>
        </div>
        <div style="display:flex; justify-content:space-between; border-bottom:1px solid rgba(255,255,255,0.02); padding-bottom:0.4rem;">
          <span style="color:var(--text-muted);">Successful Deliveries:</span>
          <span style="font-weight:700; color:var(--success);">${completedOrders} Completed</span>
        </div>
        <div style="display:flex; justify-content:space-between; border-bottom:1px solid rgba(255,255,255,0.02); padding-bottom:0.4rem;">
          <span style="color:var(--text-muted);">Active Pending Shipments:</span>
          <span style="font-weight:700; color:var(--primary);">${pendingOrders} Orders</span>
        </div>
        <div style="display:flex; justify-content:space-between; border-bottom:1px solid rgba(255,255,255,0.02); padding-bottom:0.4rem;">
          <span style="color:var(--text-muted);">Launch Coupon Campaigns:</span>
          <span style="font-weight:700;">${app.coupons.length} Active Codes</span>
        </div>
        <div style="display:flex; justify-content:space-between; font-weight:700;">
          <span style="color:var(--text-muted);">Shop Status:</span>
          <span style="color:var(--success);">Royal Gateways Open</span>
        </div>
      </div>
    `;
  }
}

// Modals Open Triggers
function openAddProductModal() {
  populateCategorySelects();
  document.getElementById("admin-add-product-form-modal").reset();
  openModal("add-product-modal");
}

function openEditProductModal(productId) {
  const p = app.products.find(item => item.id === productId);
  if (!p) return;

  populateCategorySelects();
  document.getElementById("edit-pid").value = p.id;
  document.getElementById("edit-pname").value = p.name;
  document.getElementById("edit-pcategory").value = p.category;
  document.getElementById("edit-pprice").value = p.price;
  document.getElementById("edit-pimage").value = p.image;
  document.getElementById("edit-pbadge").value = p.badge || "";
  document.getElementById("edit-pdesc").value = p.description;

  openModal("edit-product-modal");
}

function openAddCategoryModal() {
  document.getElementById("admin-add-category-form").reset();
  openModal("add-category-modal");
}

function openAddCouponModal() {
  document.getElementById("admin-add-coupon-form").reset();
  openModal("add-coupon-modal");
}

// ==========================================
// EVENT LISTENERS SETUP
// ==========================================
function setupEventListeners() {
  // Theme Toggle Click
  const themeToggle = document.getElementById("theme-toggle");
  if (themeToggle) {
    themeToggle.addEventListener("click", toggleTheme);
  }

  // Cart Drawer open/close
  const cartTrigger = document.getElementById("cart-trigger");
  const cartOverlay = document.getElementById("cart-drawer-overlay");
  const cartDrawer = document.getElementById("cart-drawer");
  const cartClose = document.getElementById("cart-close");

  const closeCartFn = () => {
    cartOverlay.classList.remove("active");
    cartDrawer.classList.remove("active");
    setTimeout(() => {
      cartOverlay.style.display = "none";
    }, 300);
  };

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

  if (cartClose) cartClose.addEventListener("click", closeCartFn);
  if (cartOverlay) cartOverlay.addEventListener("click", closeCartFn);

  // Search input change handler
  const searchInput = document.getElementById("search-input");
  if (searchInput) {
    searchInput.addEventListener("input", (e) => {
      const activeTab = document.querySelector(".category-card.active");
      const category = activeTab ? activeTab.dataset.category : "All";
      renderStorefront(category, e.target.value);
      
      // Optionally scroll to products section if user is typing
      if (e.target.value.length > 0) {
        const shopSection = document.getElementById("shop-section");
        if (shopSection) {
          const rect = shopSection.getBoundingClientRect();
          if (rect.top > window.innerHeight || rect.bottom < 0) {
            shopSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }
      }
    });
  }

  // Auth modal Form Tabs
  const authTabBtns = document.querySelectorAll(".auth-tab-btn");
  const authEmailForm = document.getElementById("auth-email-form");
  const authPhoneForm = document.getElementById("auth-phone-form");
  const authSignupForm = document.getElementById("auth-signup-form");

  authTabBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      authTabBtns.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");

      const type = btn.dataset.tab;
      authEmailForm.style.display = type === "email" ? "block" : "none";
      authPhoneForm.style.display = type === "phone" ? "block" : "none";
      authSignupForm.style.display = type === "signup" ? "block" : "none";
    });
  });

  // Login via Email submission
  if (authEmailForm) {
    authEmailForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const email = document.getElementById("login-email").value.trim();
      const password = document.getElementById("login-password").value;

      const res = app.loginWithEmail(email, password);
      if (res.success) {
        showToast(`Welcome back, ${res.user.name}!`);
        updateNavBarState();
        authEmailForm.reset();

        if (res.user.role === 'admin') {
          navigateTo('/ecommerce/admin');
        } else {
          navigateTo('/ecommerce/website');
        }
      } else {
        showToast(res.message, "danger");
      }
    });
  }

  // Mobile login OTP display and verification
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

      if (phone === "9876543210") {
        generatedOTPCode = "123456";
      } else {
        generatedOTPCode = Math.floor(1000 + Math.random() * 9000).toString();
      }

      otpSection.style.display = "block";
      sendOtpBtn.innerText = "Resend OTP";
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
        showToast(`Welcome back, ${res.user.name}!`);
        updateNavBarState();
        generatedOTPCode = null;
        if (otpSection) otpSection.style.display = "none";
        if (sendOtpBtn) sendOtpBtn.innerText = "Send OTP";
        document.getElementById("login-otp").value = "";

        if (res.user.role === 'admin') {
          navigateTo('/ecommerce/admin');
        } else {
          navigateTo('/ecommerce/website');
        }
      } else {
        showToast(res.message, "danger");
      }
    });
  }

  // Register via Signup Submission
  if (authSignupForm) {
    authSignupForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const name = document.getElementById("signup-name").value.trim();
      const email = document.getElementById("signup-email").value.trim();
      const phoneInput = document.getElementById("signup-phone");
      const phone = phoneInput ? phoneInput.value.trim() : "9876543210";
      const password = document.getElementById("signup-password").value;

      const res = app.signup(name, phone, email, password);
      if (res.success) {
        showToast(`Account created successfully! Welcome, ${res.user.name}`);
        updateNavBarState();
        authSignupForm.reset();
        navigateTo('/ecommerce/website');
      } else {
        showToast(res.message, "danger");
      }
    });
  }

  // Profile update form submission
  const profileForm = document.getElementById("profile-update-form");
  if (profileForm) {
    profileForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const name = document.getElementById("profile-name").value.trim();
      const phone = document.getElementById("profile-phone").value.trim();
      const newPwd = document.getElementById("profile-password").value;

      app.currentUser.name = name;
      app.currentUser.phone = phone;
      if (newPwd) {
        app.currentUser.password = newPwd;
      }
      app.saveState();
      showToast("Profile settings saved successfully");
      updateNavBarState();
      renderAccountDashboard();
    });
  }

  // Address add/edit form submission
  const addressForm = document.getElementById("account-address-form");
  if (addressForm) {
    addressForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const id = document.getElementById("address-id").value;
      const label = document.getElementById("addr-label").value.trim();
      const street = document.getElementById("addr-street").value.trim();
      const city = document.getElementById("addr-city").value.trim();
      const zip = document.getElementById("addr-zip").value.trim();
      const isDefault = document.getElementById("addr-default").checked;

      if (!app.currentUser.addresses) {
        app.currentUser.addresses = [];
      }

      if (isDefault) {
        app.currentUser.addresses.forEach(a => a.default = false);
      }

      if (id) {
        // Edit Address
        const addrIndex = app.currentUser.addresses.findIndex(a => a.id === id);
        if (addrIndex > -1) {
          app.currentUser.addresses[addrIndex] = { id, label, street, city, zip, default: isDefault };
        }
      } else {
        // Add Address
        const newId = "addr-" + Math.random().toString(36).substr(2, 9);
        app.currentUser.addresses.push({ id: newId, label, street, city, zip, default: isDefault });
      }

      app.saveState();
      showToast("Address saved successfully");
      addressForm.reset();
      closeAddressForm();
      renderAddressesList();
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
        switchView('auth');
        return;
      }

      // Populate checkout fields
      document.getElementById("checkout-name").value = app.currentUser.name;
      document.getElementById("checkout-phone").value = app.currentUser.phone || "";

      // Attempt to find default address
      const defAddr = (app.currentUser.addresses || []).find(a => a.default);
      if (defAddr) {
        document.getElementById("checkout-address").value = defAddr.street;
        document.getElementById("checkout-city").value = defAddr.city;
        document.getElementById("checkout-zip").value = defAddr.zip;
      } else {
        document.getElementById("checkout-address").value = "";
        document.getElementById("checkout-city").value = "Jodhpur";
        document.getElementById("checkout-zip").value = "";
      }

      // Set discount summary visibility to none
      document.getElementById("checkout-discount-summary").style.display = "none";
      document.getElementById("checkout-coupon").value = "";
      app.activeCoupon = null;

      closeCartFn();
      openModal("checkout-modal");
    });
  }

  // Apply Coupon code at checkout
  const applyCouponBtn = document.getElementById("apply-coupon-btn");
  if (applyCouponBtn) {
    applyCouponBtn.addEventListener("click", () => {
      const code = document.getElementById("checkout-coupon").value.trim();
      if (!code) {
        showToast("Please enter a coupon code", "danger");
        return;
      }

      const res = app.applyCoupon(code);
      if (res.success) {
        showToast(`Coupon "${res.coupon.code}" applied successfully!`);

        const subtotal = app.getCartTotal();
        const discount = app.getDiscountAmount();
        const grandTotal = app.getGrandTotal();

        document.getElementById("checkout-subtotal-val").innerText = `₹${subtotal.toLocaleString("en-IN")}`;
        document.getElementById("checkout-discount-val").innerText = `-₹${discount.toLocaleString("en-IN")}`;
        document.getElementById("checkout-total-val").innerText = `₹${grandTotal.toLocaleString("en-IN")}`;
        document.getElementById("checkout-discount-summary").style.display = "block";
      } else {
        showToast(res.message, "danger");
      }
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
        total: app.getGrandTotal(), // Reads coupon-subtracted total
        status: "Pending",
        date: new Date().toISOString(),
        shippingDetails: {
          name: document.getElementById("checkout-name").value.trim(),
          phone: document.getElementById("checkout-phone").value.trim(),
          address: document.getElementById("checkout-address").value.trim(),
          street: document.getElementById("checkout-address").value.trim(), // backwards compatibility
          city: document.getElementById("checkout-city").value.trim(),
          zip: document.getElementById("checkout-zip").value.trim()
        }
      };

      app.orders.push(order);
      app.clearCart();
      app.saveState();

      closeModal();
      showToast("Order placed successfully! Padharo Mhare Des.");
      updateCartUI();

      setTimeout(() => {
        switchView("account");
        document.querySelector('[data-tab="account-orders"]').click();
      }, 500);
    });
  }

  // Add Product form submission modal
  const addProductFormModal = document.getElementById("admin-add-product-form-modal");
  if (addProductFormModal) {
    addProductFormModal.addEventListener("submit", (e) => {
      e.preventDefault();

      const newProd = {
        id: "prod-" + Math.random().toString(36).substr(2, 9),
        name: document.getElementById("new-pname").value.trim(),
        category: document.getElementById("new-pcategory").value,
        price: parseFloat(document.getElementById("new-pprice").value),
        description: document.getElementById("new-pdesc").value.trim(),
        image: document.getElementById("new-pimage").value.trim() || "https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=600&q=80",
        badge: document.getElementById("new-pbadge").value.trim() || null
      };

      app.products.push(newProd);
      app.saveState();

      showToast(`Product "${newProd.name}" added successfully`);
      closeModal();
      renderAdminDashboard();
      renderStorefront();
    });
  }

  // Edit Product form submission modal
  const editProductForm = document.getElementById("admin-edit-product-form");
  if (editProductForm) {
    editProductForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const id = document.getElementById("edit-pid").value;

      const pIndex = app.products.findIndex(item => item.id === id);
      if (pIndex > -1) {
        app.products[pIndex].name = document.getElementById("edit-pname").value.trim();
        app.products[pIndex].category = document.getElementById("edit-pcategory").value;
        app.products[pIndex].price = parseFloat(document.getElementById("edit-pprice").value);
        app.products[pIndex].image = document.getElementById("edit-pimage").value.trim();
        app.products[pIndex].badge = document.getElementById("edit-pbadge").value.trim() || null;
        app.products[pIndex].description = document.getElementById("edit-pdesc").value.trim();

        app.saveState();
        showToast("Product updated successfully");
        closeModal();
        renderAdminDashboard();
        renderStorefront();
      }
    });
  }

  // Category Add form submission modal
  const addCategoryForm = document.getElementById("admin-add-category-form");
  if (addCategoryForm) {
    addCategoryForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const name = document.getElementById("cat-name-input").value.trim();
      const slug = document.getElementById("cat-slug-input").value.trim();
      const image = document.getElementById("cat-image-input").value.trim() || "https://images.unsplash.com/photo-1596450514735-111a2fe02935?auto=format&fit=crop&w=400&q=80";

      const exists = app.categories.some(c => c.slug.toLowerCase() === slug.toLowerCase());
      if (exists) {
        showToast("A category with this slug already exists", "danger");
        return;
      }

      app.categories.push({
        id: "cat-" + Math.random().toString(36).substr(2, 9),
        name,
        slug,
        image
      });
      app.saveState();
      showToast(`Category "${name}" added successfully`);
      closeModal();
      renderAdminDashboard();
      renderStorefrontCategories();
      populateCategorySelects();
    });
  }

  const editCategoryForm = document.getElementById("admin-edit-category-form");
  if (editCategoryForm) {
    editCategoryForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const oldSlug = document.getElementById("edit-cat-old-slug").value;
      const newName = document.getElementById("edit-cat-name").value.trim();
      const newSlug = document.getElementById("edit-cat-slug").value.trim().toLowerCase().replace(/ /g, '-');
      const newImage = document.getElementById("edit-cat-image").value.trim();

      const catIndex = app.categories.findIndex(c => c.slug === oldSlug);
      if (catIndex > -1) {
        // Update product categories if slug changed
        if (oldSlug !== newSlug) {
          const exists = app.categories.some(c => c.slug === newSlug);
          if (exists) {
            showToast("A category with this slug already exists", "danger");
            return;
          }
          app.products.forEach(p => {
            if (p.category === oldSlug) p.category = newSlug;
          });
        }
        
        app.categories[catIndex].name = newName;
        app.categories[catIndex].slug = newSlug;
        app.categories[catIndex].image = newImage;
        
        app.saveState();
        showToast("Category updated successfully");
        closeModal();
        renderAdminDashboard();
        renderStorefrontCategories();
        populateCategorySelects();
      }
    });
  }

  // Coupon Add form submission modal
  const addCouponForm = document.getElementById("admin-add-coupon-form");
  if (addCouponForm) {
    addCouponForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const code = document.getElementById("coupon-code-input").value.trim().toUpperCase();
      const type = document.getElementById("coupon-type-input").value;
      const amount = parseFloat(document.getElementById("coupon-amount-input").value);
      const expiry = document.getElementById("coupon-expiry-input").value;

      const exists = app.coupons.some(c => c.code === code);
      if (exists) {
        showToast("A coupon with this code already exists", "danger");
        return;
      }

      app.coupons.push({
        id: "cp-" + Math.random().toString(36).substr(2, 9),
        code,
        type,
        amount,
        expiry
      });
      app.saveState();
      showToast(`Coupon "${code}" published successfully`);
      closeModal();
      renderAdminDashboard();
    });
  }

  // Customer search input
  const custSearchInput = document.getElementById("admin-customer-search");
  if (custSearchInput) {
    custSearchInput.addEventListener("input", (e) => {
      renderAdminCustomersList(e.target.value);
    });
  }

  // Settings form submit
  const settingsForm = document.getElementById("admin-settings-form");
  if (settingsForm) {
    settingsForm.addEventListener("submit", (e) => {
      e.preventDefault();
      showToast("Shop configurations updated successfully!");
    });
  }

  // Admin Sidebar switching logic
  const adminNavLinks = document.querySelectorAll(".admin-nav-link");
  adminNavLinks.forEach(link => {
    link.addEventListener("click", () => {
      adminNavLinks.forEach(l => l.classList.remove("active"));
      link.classList.add("active");

      const panelId = link.dataset.panel;
      document.querySelectorAll(".admin-panel").forEach(p => p.classList.remove("active"));
      const activePanel = document.getElementById(panelId);
      if (activePanel) {
        activePanel.classList.add("active");
      }
    });
  });

  // Account dashboard tabs sub-switching logic
  const accountTabLinks = document.querySelectorAll(".account-tab-link");
  accountTabLinks.forEach(link => {
    link.addEventListener("click", () => {
      accountTabLinks.forEach(l => l.classList.remove("active"));
      link.classList.add("active");

      const tabId = link.dataset.tab;
      document.querySelectorAll(".account-panel").forEach(p => p.classList.remove("active"));
      const activePanel = document.getElementById(tabId);
      if (activePanel) {
        activePanel.classList.add("active");
      }
    });
  });

  // Superpanel sidebar Back to shop button (superpanel.html specific)
  const isSuperpanel = window.location.pathname.includes('superpanel');
  if (isSuperpanel || document.getElementById("admin-overview")) {
    const adminBackShopBtn = document.getElementById("back-to-shop-btn");
    if (adminBackShopBtn) {
      adminBackShopBtn.addEventListener("click", () => {
        app.logout();
        showToast("Logged out of Super Panel");
        const isLocalFile = window.location.protocol === 'file:';
        window.location.href = isLocalFile ? 'index.html' : '/';
      });
    }

    // Dashboard Period Dropdown Logic
    const periodBtn = document.getElementById("dashboard-period-btn");
    const periodDropdown = document.getElementById("dashboard-period-dropdown");
    if (periodBtn && periodDropdown) {
      periodBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        const isHidden = periodDropdown.style.display === "none";
        periodDropdown.style.display = isHidden ? "flex" : "none";
      });

      document.querySelectorAll(".period-option").forEach(btn => {
        btn.addEventListener("click", (e) => {
          document.getElementById("dashboard-period-text").innerText = e.target.innerText;
          periodDropdown.style.display = "none";
          // We can call renderAdminDashboard() if we had date filtering, but it's fine as a visual update
        });
      });

      document.addEventListener("click", () => {
        periodDropdown.style.display = "none";
      });
    }
  }
}

// Global modal background closing
window.onclick = function (event) {
  const overlay = document.getElementById("modal-overlay-container");
  if (event.target === overlay) {
    closeModal();
  }
};


// Native WordPress Image Upload Function
async function uploadImageToWP(fileInput, targetInputId) {
  const file = fileInput.files[0];
  if (!file) return;

  const targetInput = document.getElementById(targetInputId);
  const originalPlaceholder = targetInput.placeholder;
  targetInput.placeholder = "Uploading...";
  targetInput.value = "";
  if (window.showToast) window.showToast("Uploading image...", "success");

  const formData = new FormData();
  formData.append('file', file);

  try {
    const res = await fetch('/wp-json/wp-ecommerce/v1/upload', {
      method: 'POST',
      body: formData
    });
    
    const data = await res.json();
    if (data.success) {
      targetInput.value = data.url;
      if (window.showToast) window.showToast("Image uploaded successfully!");
    } else {
      if (window.showToast) window.showToast("Upload failed: " + (data.message || "Unknown error"), "danger");
      targetInput.placeholder = originalPlaceholder;
    }
  } catch (err) {
    if (window.showToast) window.showToast("Upload failed: " + err.message, "danger");
    targetInput.placeholder = originalPlaceholder;
  }
}
