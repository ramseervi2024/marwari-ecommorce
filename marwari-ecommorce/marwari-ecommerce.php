<?php
/**
 * Plugin Name: Marwari E-Commerce Storefront
 * Plugin URI: https://marwari-ecommorce.rpsdigitalworld.store/
 * Description: Premium heritage e-commerce storefront for Marwari products, featuring custom database tables, user authentication, and admin dashboards.
 * Version: 1.0.0
 * Author: Mārwāri E-Commerce Team
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define Database Table Names
global $wpdb;
define( 'MARWARI_PRODUCTS_TABLE', $wpdb->prefix . 'marwari_products' );
define( 'MARWARI_ORDERS_TABLE', $wpdb->prefix . 'marwari_orders' );

// 1. Plugin Activation: Create Custom Tables & Seed Products
register_activation_hook( __FILE__, 'marwari_ecommerce_activate' );
function marwari_ecommerce_activate() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // Products Table Schema
    $sql_products = "CREATE TABLE " . MARWARI_PRODUCTS_TABLE . " (
        id varchar(50) NOT NULL,
        name varchar(255) NOT NULL,
        category varchar(100) NOT NULL,
        price decimal(10,2) NOT NULL,
        description text NOT NULL,
        image text NOT NULL,
        badge varchar(50) DEFAULT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    dbDelta( $sql_products );

    // Orders Table Schema
    $sql_orders = "CREATE TABLE " . MARWARI_ORDERS_TABLE . " (
        id varchar(50) NOT NULL,
        user_email varchar(100) NOT NULL,
        items text NOT NULL,
        total decimal(10,2) NOT NULL,
        status varchar(50) NOT NULL DEFAULT 'Pending',
        date datetime NOT NULL,
        shipping_details text NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    dbDelta( $sql_orders );

    // Seed products if table is empty
    marwari_ecommerce_seed_products();

    // Setup initial WP roles/users for demo compatibility if they don't exist
    marwari_ecommerce_setup_demo_users();
}

// Seed Initial Products
function marwari_ecommerce_seed_products() {
    global $wpdb;
    $count = $wpdb->get_var( "SELECT COUNT(*) FROM " . MARWARI_PRODUCTS_TABLE );
    
    if ( $count == 0 ) {
        $seed_products = array(
            array(
                'id' => 'prod-1',
                'name' => 'Royal Jaipuri Silk Bandhani Saree',
                'category' => 'Apparel',
                'price' => 8499.00,
                'description' => 'Experience the royal heritage of Rajasthan with this premium pure silk Bandhani Saree. Hand-dyed by traditional artisans in Jaipur using the classic tie-dye technique, featuring intricate golden zari borders and elegant motifs perfect for festive occasions.',
                'image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=600&q=80',
                'badge' => 'Bestseller'
            ),
            array(
                'id' => 'prod-2',
                'name' => 'Classic Navy Blue Royal Jodhpuri Suit',
                'category' => 'Apparel',
                'price' => 12999.00,
                'description' => 'An epitome of sophistication, this Jodhpuri Suit is tailored to perfection. Crafted from premium wool-blend fabric, it features a bandhgala collar, structured shoulders, and brass crest buttons. Represents the true legacy of Marwari aristocracy.',
                'image' => 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&w=600&q=80',
                'badge' => 'Royal Exclusive'
            ),
            array(
                'id' => 'prod-3',
                'name' => 'Handcrafted Gold-Leaf Jaipuri Quilt',
                'category' => 'Handicrafts',
                'price' => 3499.00,
                'description' => 'This world-famous Jaipuri Razai (quilt) is incredibly lightweight yet provides exceptional warmth. Made of 100% pure organic cotton and filled with carded cotton, it is detailed with traditional gold-leaf hand-block printing.',
                'image' => 'https://images.unsplash.com/photo-1583847268964-b28dc8f51f92?auto=format&fit=crop&w=600&q=80',
                'badge' => '100% Cotton'
            ),
            array(
                'id' => 'prod-4',
                'name' => 'Pure Silver Meenakari Pearl Jhumkas',
                'category' => 'Jewelry',
                'price' => 4999.00,
                'description' => 'Exquisite traditional earrings featuring intricate Meenakari (enamel) artwork hand-painted on pure sterling silver. Suspended with premium freshwater pearls and detailed with delicate filigree work inspired by Royal Rajasthani palaces.',
                'image' => 'https://images.unsplash.com/photo-1630019852942-f89202989a59?auto=format&fit=crop&w=600&q=80',
                'badge' => 'Handmade'
            ),
            array(
                'id' => 'prod-5',
                'name' => 'Traditional Camel Leather Mojaris',
                'category' => 'Apparel',
                'price' => 1899.00,
                'description' => 'Authentic Marwari Mojaris made from genuine, double-tanned camel leather. Hand-stitched with premium silk and golden zari threads. Designed with a soft cushioned sole for comfort and durability while maintaining absolute style.',
                'image' => 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=600&q=80',
                'badge' => 'Artisan Leather'
            ),
            array(
                'id' => 'prod-6',
                'name' => 'Premium Saffron & Cardamom Kesaria Peda',
                'category' => 'Sweets & Spices',
                'price' => 899.00,
                'description' => 'Indulge in the true taste of Marwar. Our Kesaria Peda is cooked slowly using fresh condensed milk (khoya), flavored with organic Kashmiri saffron (kesar) and ground green cardamom, garnished with premium sliced pistachios and almonds.',
                'image' => 'https://images.unsplash.com/photo-1587314168485-3236d6710814?auto=format&fit=crop&w=600&q=80',
                'badge' => 'Freshly Made'
            ),
            array(
                'id' => 'prod-7',
                'name' => 'Jaipur Traditional Blue Pottery Vase',
                'category' => 'Handicrafts',
                'price' => 2299.00,
                'description' => 'Add a touch of elegance to your home with this authentic Jaipur Blue Pottery decorative vase. Crafted with Egyptian paste clay, hand-painted with cobalt blue dye in traditional floral motifs, and glazed to a premium shine.',
                'image' => 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=600&q=80',
                'badge' => 'Heritage Art'
            )
        );

        foreach ( $seed_products as $p ) {
            $wpdb->insert( MARWARI_PRODUCTS_TABLE, $p );
        }
    }
}

// Setup Demo Users on Activation
function marwari_ecommerce_setup_demo_users() {
    // Add Demo User
    if ( ! email_exists( 'user@gmail.com' ) && ! username_exists( 'user' ) ) {
        $user_id = wp_create_user( 'user', 'password123', 'user@gmail.com' );
        if ( ! is_wp_error( $user_id ) ) {
            wp_update_user( array(
                'ID' => $user_id,
                'display_name' => 'Ramesh Seervi',
                'first_name' => 'Ramesh',
                'last_name' => 'Seervi'
            ) );
            update_user_meta( $user_id, 'billing_phone', '9001122334' );
        }
    }

    // Add Demo Admin (or update password if exists)
    if ( ! email_exists( 'admin@gmail.com' ) && ! username_exists( 'admin' ) ) {
        $admin_id = wp_create_user( 'admin', '123456', 'admin@gmail.com' );
        if ( ! is_wp_error( $admin_id ) ) {
            $user_obj = new WP_User( $admin_id );
            $user_obj->set_role( 'administrator' );
            wp_update_user( array(
                'ID' => $admin_id,
                'display_name' => 'Marwari Admin',
                'first_name' => 'Marwari',
                'last_name' => 'Admin'
            ) );
            update_user_meta( $admin_id, 'billing_phone', '9876543210' );
        }
    }
}

// Auto-reset admin password to 123456 (runs once per version update)
add_action( 'init', 'marwari_ecommerce_ensure_admin_password' );
function marwari_ecommerce_ensure_admin_password() {
    $version_key = 'marwari_admin_pw_version';
    $current_version = '2.4.0';
    
    if ( get_option( $version_key ) === $current_version ) return;
    
    $admin = get_user_by( 'email', 'admin@gmail.com' );
    if ( $admin ) {
        wp_set_password( '123456', $admin->ID );
    }
    update_option( $version_key, $current_version );
}

// 2. Enqueue Assets (style.css & app.js)
add_action( 'wp_enqueue_scripts', 'marwari_ecommerce_enqueue_assets' );
function marwari_ecommerce_enqueue_assets() {
    global $post;
    if ( ! is_a( $post, 'WP_Post' ) ) return;

    $is_shop  = has_shortcode( $post->post_content, 'marwari_storefront' );
    $is_admin = has_shortcode( $post->post_content, 'marwari_admin_panel' );

    if ( $is_shop || $is_admin ) {
        // Enqueue Style (v2.4.0)
        wp_enqueue_style( 'marwari-style', plugin_dir_url( __FILE__ ) . 'style.css', array(), '2.4.0' );

        // Enqueue Script (v2.4.0)
        wp_enqueue_script( 'marwari-app', plugin_dir_url( __FILE__ ) . 'app.js', array(), '2.4.0', true );

        // Localize: pass API settings + page mode
        wp_localize_script( 'marwari-app', 'wpApiSettings', array(
            'root'     => esc_url_raw( rest_url() ),
            'nonce'    => wp_create_nonce( 'wp_rest' ),
            'pageMode' => $is_admin ? 'admin' : 'shop'
        ) );
    }
}

// 3. Storefront UI Shortcode: [marwari_storefront]
add_shortcode( 'marwari_storefront', 'marwari_ecommerce_render_storefront' );
function marwari_ecommerce_render_storefront() {
    ob_start();
    ?>
    <div class="marwari-ecommerce-wrapper">
        <!-- Top Notification Toast Container -->
        <div class="toast-container" id="toast-container"></div>

        <!-- Royal Navigation Header -->
        <header class="navbar">
            <div class="container nav-container">
                <a href="#" class="logo">
                    <span>Mārwāri</span> E-Commerce
                </a>
                
                <!-- Search Bar -->
                <div class="search-bar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <input type="text" id="search-input" placeholder="Search for royal heritage products...">
                </div>

                <!-- Actions (Cart, Theme, Auth Profile) -->
                <div class="nav-actions">
                    <!-- Theme Toggle -->
                    <button class="nav-btn" id="theme-toggle" title="Switch Theme">
                        <span id="theme-icon"></span>
                    </button>

                    <!-- Cart Trigger -->
                    <button class="nav-btn" id="cart-trigger" title="View Cart">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                        <span class="cart-count" style="display: none;">0</span>
                    </button>

                    <!-- Authentication Profile / Login Menu -->
                    <div class="user-profile-menu" id="user-menu-container">
                        <!-- Dynamically populated by app.js -->
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero" id="hero-section">
            <div class="container hero-slider">
                <div class="hero-content">
                    <span class="hero-tag">Padharo Mhare Des</span>
                    <h1>Authentic <span>Marwari</span> Treasures</h1>
                    <p>Explore high-end Jodhpuri suits, hand-dyed Jaipur silk sarees, detailed silver jewelry, and mouthwatering traditional delicacies direct from Rajasthan's master craftsmen.</p>
                    <button class="btn-primary" onclick="document.getElementById('shop-section').scrollIntoView({behavior: 'smooth'})">
                        Explore Collection
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>
                </div>
                <div class="hero-image">
                    <img src="https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&w=600&q=80" alt="Royal Jodhpuri Suit Banner">
                </div>
            </div>
        </section>

        <!-- Categories Filter Tab Slider -->
        <section class="categories-section" id="categories-section">
            <div class="container">
                <div class="category-tabs">
                    <button class="category-tab active" data-category="All">All Treasures</button>
                    <button class="category-tab" data-category="Apparel">Royal Apparel</button>
                    <button class="category-tab" data-category="Handicrafts">Jaipur Handicrafts</button>
                    <button class="category-tab" data-category="Jewelry">Silver Jewelry</button>
                    <button class="category-tab" data-category="Sweets & Spices">Sweets & Spices</button>
                </div>
            </div>
        </section>

        <!-- Products Main Storefront Grid -->
        <section class="shop-section" id="shop-section">
            <div class="container">
                <div class="section-title">
                    <h2>Our Curated <span>Heritage Collection</span></h2>
                </div>
                <div class="products-grid" id="products-container">
                    <!-- Dynamically Populated -->
                </div>
            </div>
        </section>

        <!-- Admin View Dashboard Wrapper -->
        <section class="container admin-view-wrapper" id="admin-view">
            <div class="admin-header">
                <div class="admin-header-title">
                    <h2>Administrative Dashboard</h2>
                    <p>Manage product inventories, track orders, and view sales statistics.</p>
                </div>
                <button class="btn-primary" onclick="switchView('shop')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7M5 12h14"/></svg>
                    Back to Store
                </button>
            </div>

            <!-- Administrative Statistics -->
            <div class="admin-stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <div class="stat-details">
                        <h4>Total Revenue</h4>
                        <p id="stat-sales">₹0</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/><path d="M12 7.5V12l3 3"/></svg>
                    </div>
                    <div class="stat-details">
                        <h4>Live Products</h4>
                        <p id="stat-products">0</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    </div>
                    <div class="stat-details">
                        <h4>Total Orders</h4>
                        <p id="stat-orders">0</p>
                    </div>
                </div>
            </div>

            <!-- Admin Main Layout -->
            <div class="admin-main-layout">
                <div class="admin-panel-card">
                    <h3>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                        Add Royal Product
                    </h3>
                    <form id="admin-add-product-form">
                        <div class="form-group">
                            <label for="admin-pname">Product Name</label>
                            <input type="text" id="admin-pname" class="form-input" placeholder="e.g. Royal Marwari Jodhpuri Suit" required>
                        </div>
                        <div class="form-group">
                            <label for="admin-pcategory">Category</label>
                            <select id="admin-pcategory" class="form-input" required>
                                <option value="Apparel">Royal Apparel</option>
                                <option value="Handicrafts">Jaipur Handicrafts</option>
                                <option value="Jewelry">Silver Jewelry</option>
                                <option value="Sweets & Spices">Sweets & Spices</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="admin-pprice">Price (INR)</label>
                            <input type="number" id="admin-pprice" class="form-input" placeholder="e.g. 12999" required min="1">
                        </div>
                        <div class="form-group">
                            <label for="admin-pimage">Image URL</label>
                            <input type="url" id="admin-pimage" class="form-input" placeholder="https://images.unsplash.com/..." required>
                        </div>
                        <div class="form-group">
                            <label for="admin-pbadge">Badge Label (Optional)</label>
                            <input type="text" id="admin-pbadge" class="form-input" placeholder="e.g. Bestseller, New">
                        </div>
                        <div class="form-group">
                            <label for="admin-pdesc">Product Description</label>
                            <textarea id="admin-pdesc" class="form-input" rows="4" placeholder="Describe craftsmanship..." required></textarea>
                        </div>
                        <button type="submit" class="auth-submit-btn">Publish Product to Store</button>
                    </form>
                </div>

                <div style="display:flex; flex-direction:column; gap:2.5rem;">
                    <!-- Catalog list -->
                    <div class="admin-panel-card">
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/><path d="M12 7.5V12l3 3"/></svg>
                            Manage Product Catalog
                        </h3>
                        <div class="admin-table-container">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="admin-products-table"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Orders Table -->
                    <div class="admin-panel-card">
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                            Customer Orders Queue
                        </h3>
                        <p style="font-size:0.75rem; color:var(--text-muted); margin-bottom:1rem; margin-top:-1rem;">* Click on 'Pending' button to mark status as 'Completed'.</p>
                        <div class="admin-table-container">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Items Ordered</th>
                                        <th>Total Price</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="admin-orders-table"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- User Order History View Container -->
        <section class="container orders-view-wrapper" id="orders-view">
            <div class="admin-header">
                <div class="admin-header-title">
                    <h2>Your Purchase History</h2>
                    <p>Track delivery status and view invoices of your royal acquisitions.</p>
                </div>
                <button class="btn-primary" onclick="switchView('shop')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7M5 12h14"/></svg>
                    Back to Store
                </button>
            </div>
            <div class="orders-grid" id="orders-history-list"></div>
        </section>

        <!-- Footer Section -->
        <footer>
            <div class="container footer-grid">
                <div class="footer-col">
                    <h3><span>Mārwāri</span> E-Commerce</h3>
                    <p style="max-width: 250px;">Preserving and presenting the glorious Royal heritage of Marwar region through curated premium artifacts and traditional apparel.</p>
                </div>
                <div class="footer-col">
                    <h3>Heritage Hubs</h3>
                    <ul class="footer-links">
                        <li><a href="#">Jodhpur Royal Court</a></li>
                        <li><a href="#">Jaipur Blue Artisans</a></li>
                        <li><a href="#">Bikaner Spices Union</a></li>
                        <li><a href="#">Udaipur Silver Smiths</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Policy Details</h3>
                    <ul class="footer-links">
                        <li><a href="#">Authenticity Certificate</a></li>
                        <li><a href="#">Handicraft Insurance</a></li>
                        <li><a href="#">Global Royal Shipments</a></li>
                        <li><a href="#">Return & Exchange Policy</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Contact Darbar</h3>
                    <p style="margin-bottom: 0.5rem;">Royal Heritage Lane, Fort Area<br>Jodhpur, Rajasthan - 342001</p>
                    <p>Email: padharo@marwari.com<br>Phone: +91 291 2740001</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Mārwāri E-Commerce. Crafted with Royal Pride for Rajasthan Heritage.</p>
            </div>
        </footer>

        <!-- CART SLIDE DRAWER -->
        <div class="cart-drawer-overlay" id="cart-drawer-overlay"></div>
        <div class="cart-drawer" id="cart-drawer">
            <div class="cart-drawer-header">
                <h3>Royal Bag <span class="cart-count">(0)</span></h3>
                <button class="cart-close-btn" id="cart-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="cart-drawer-body" id="cart-drawer-list"></div>
            <div class="cart-drawer-footer">
                <div class="cart-summary-line">
                    <span>Bag Subtotal</span>
                    <span id="cart-subtotal">₹0</span>
                </div>
                <div class="cart-summary-line">
                    <span>Royal Shipping</span>
                    <span style="color: var(--success); font-weight: 600;">Complimentary</span>
                </div>
                <div class="cart-summary-line total">
                    <span>Grand Total</span>
                    <span id="cart-total">₹0</span>
                </div>
                <button class="checkout-btn" id="checkout-trigger-btn">
                    Proceed to Checkout
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        <!-- SHARED MODALS BACKDROP CONTAINER -->
        <div class="modal-overlay" id="modal-overlay-container">
            <!-- 1. Product Detail Modal -->
            <div class="modal-content large product-detail-modal-layout" id="product-detail-modal"></div>

            <!-- 2. Authentication Modal -->
            <div class="modal-content small" id="auth-modal">
                <button class="modal-close-btn" onclick="closeModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
                <div class="auth-tabs">
                    <button class="auth-tab-btn active" data-tab="login">Login</button>
                    <button class="auth-tab-btn" data-tab="register">Create Account</button>
                    <button class="auth-tab-btn" data-tab="admin-panel">E-Commerce Panel</button>
                </div>
                <div class="auth-form-container">
                    <!-- Login Form (Email Only — code sent to email) -->
                    <form id="auth-login-form">
                        <div class="form-group">
                            <label for="login-email">Email Address</label>
                            <input type="email" id="login-email" class="form-input" placeholder="your@email.com" required>
                        </div>
                        <button type="submit" class="auth-submit-btn">Send Login Code</button>
                    </form>

                    <!-- Registration Form (No password) -->
                    <form id="auth-register-form" style="display: none;">
                        <div class="form-group">
                            <label for="register-name">Full Name</label>
                            <input type="text" id="register-name" class="form-input" placeholder="e.g. Ramesh Seervi" required>
                        </div>
                        <div class="form-group">
                            <label for="register-email">Email Address</label>
                            <input type="email" id="register-email" class="form-input" placeholder="your@email.com" required>
                        </div>
                        <div class="form-group">
                            <label for="register-phone">Mobile Number</label>
                            <div style="display: flex; gap: 0.5rem;">
                                <span class="form-input" style="width: auto; background: var(--bg-app); border-color: var(--border-color); display: flex; align-items: center; justify-content: center; font-weight: 600;">+91</span>
                                <input type="tel" id="register-phone" class="form-input" placeholder="98765 43210" pattern="[0-9]{10}" required>
                            </div>
                        </div>
                        <button type="submit" class="auth-submit-btn">Create Account & Send Code</button>
                    </form>

                    <!-- Email Verification Code Form (shown after login or registration) -->
                    <form id="auth-verify-form" style="display: none;">
                        <div style="text-align: center; padding: 0.5rem 0 1rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" stroke="var(--primary)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.5rem;"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            <h3 style="font-size: 1.1rem; margin-bottom: 0.3rem;">Check Your Email</h3>
                            <p style="font-size: 0.85rem; color: var(--text-secondary);" id="verify-email-hint">We sent a 6-digit verification code to your email.</p>
                        </div>
                        <div class="form-group">
                            <label for="verify-code">Verification Code</label>
                            <input type="text" id="verify-code" class="form-input verify-code-input" placeholder="000000" maxlength="6" pattern="[0-9]{6}" required autocomplete="one-time-code" inputmode="numeric" style="text-align: center; font-size: 1.5rem; letter-spacing: 0.8rem; font-weight: 700;">
                        </div>
                        <button type="submit" class="auth-submit-btn">Verify & Login</button>
                        <button type="button" class="auth-resend-btn" id="resend-code-btn" style="display: block; width: 100%; margin-top: 0.75rem; padding: 0.5rem; background: none; color: var(--text-secondary); font-size: 0.85rem; border: 1px dashed var(--border-color); border-radius: 8px; cursor: pointer;">Resend Verification Code</button>
                    </form>

                    <!-- Admin Panel Login Form (email + password) -->
                    <form id="auth-admin-form" style="display: none;">
                        <div style="text-align: center; padding: 0.5rem 0 1rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" stroke="var(--primary)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.5rem;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            <h3 style="font-size: 1.1rem; margin-bottom: 0.3rem;">Admin Access</h3>
                            <p style="font-size: 0.85rem; color: var(--text-secondary);">Enter admin credentials to access the dashboard</p>
                        </div>
                        <div class="form-group">
                            <label for="admin-email">Admin Email</label>
                            <input type="email" id="admin-email" class="form-input" placeholder="admin@gmail.com" required>
                        </div>
                        <div class="form-group">
                            <label for="admin-password">Password</label>
                            <input type="password" id="admin-password" class="form-input" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="auth-submit-btn">Access Dashboard</button>
                    </form>
                </div>
            </div>

            <!-- 3. Checkout details Form Modal -->
            <div class="modal-content" id="checkout-modal">
                <button class="modal-close-btn" onclick="closeModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
                <div style="padding: 2rem;">
                    <h3 style="font-size: 1.4rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Royal Shipping Details</h3>
                    <form id="checkout-payment-form">
                        <div class="form-group">
                            <label for="checkout-name">Recipient Name</label>
                            <input type="text" id="checkout-name" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="checkout-phone">Contact Number</label>
                            <input type="tel" id="checkout-phone" class="form-input" required pattern="[0-9]{10}">
                        </div>
                        <div class="form-group">
                            <label for="checkout-address">Delivery Address (Darbar/House/Street)</label>
                            <input type="text" id="checkout-address" class="form-input" placeholder="e.g. Royal Villas, Road No. 4" required>
                        </div>
                        <div style="display: flex; gap: 1rem;">
                            <div class="form-group" style="flex: 1.2;">
                                <label for="checkout-city">City</label>
                                <input type="text" id="checkout-city" class="form-input" value="Jodhpur" required>
                            </div>
                            <div class="form-group" style="flex: 0.8;">
                                <label for="checkout-zip">Pin Code</label>
                                <input type="text" id="checkout-zip" class="form-input" placeholder="342001" required pattern="[0-9]{6}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="checkout-payment-method">Payment Protocol</label>
                            <select id="checkout-payment-method" class="form-input" required>
                                <option value="Complimentary Cash On Delivery">Cash On Delivery (Royal Complimentary)</option>
                                <option value="Royal Card">Royal Card Privilege (Simulated)</option>
                            </select>
                        </div>
                        <button type="submit" class="auth-submit-btn">Place Order & Ship</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// 4. REST API: Authenticate our endpoints from cookies, bypassing WordPress nonce requirement.
// Priority 99 runs BEFORE WordPress's rest_cookie_check_errors (priority 100), preventing
// it from resetting the user to 0 when nonce is stale/cached.
add_filter( 'rest_authentication_errors', 'marwari_ecommerce_rest_auth', 99 );
function marwari_ecommerce_rest_auth( $result ) {
    // If another plugin already handled auth, pass through
    if ( $result !== null ) {
        return $result;
    }

    // Detect if this request is for our plugin's REST namespace
    $paths = array();
    if ( isset( $_SERVER['REQUEST_URI'] ) ) {
        $paths[] = $_SERVER['REQUEST_URI'];
        $paths[] = urldecode( $_SERVER['REQUEST_URI'] );
    }
    if ( isset( $_GET['rest_route'] ) ) {
        $paths[] = $_GET['rest_route'];
        $paths[] = urldecode( $_GET['rest_route'] );
    }
    if ( isset( $_SERVER['PATH_INFO'] ) ) {
        $paths[] = $_SERVER['PATH_INFO'];
        $paths[] = urldecode( $_SERVER['PATH_INFO'] );
    }

    $is_our_route = false;
    foreach ( $paths as $path ) {
        if ( strpos( $path, '/marwari-ecom/' ) !== false ) {
            $is_our_route = true;
            break;
        }
    }

    if ( ! $is_our_route ) {
        return $result; // Not our route — let WordPress handle normally
    }

    // For our routes: authenticate directly from the browser's auth cookie (no nonce needed)
    $cookie_user_id = wp_validate_auth_cookie( '', 'logged_in' );
    if ( $cookie_user_id ) {
        wp_set_current_user( $cookie_user_id );
    }

    // Return true = "auth passed, no errors".
    // This short-circuits rest_cookie_check_errors at priority 100,
    // preventing it from calling wp_set_current_user(0).
    return true;
}

add_action( 'rest_api_init', 'marwari_ecommerce_register_rest_endpoints' );
function marwari_ecommerce_register_rest_endpoints() {
    $ns = 'marwari-ecom/v1';

    // Products endpoints
    register_rest_route( $ns, '/products', array(
        array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => 'marwari_ecommerce_get_products',
            'permission_callback' => '__return_true',
        ),
        array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => 'marwari_ecommerce_create_product',
            'permission_callback' => 'marwari_ecommerce_admin_permissions_check',
        )
    ));

    register_rest_route( $ns, '/products/(?P<id>[a-zA-Z0-9\-]+)', array(
        'methods'             => WP_REST_Server::DELETABLE,
        'callback'            => 'marwari_ecommerce_delete_product',
        'permission_callback' => 'marwari_ecommerce_admin_permissions_check',
    ));

    // Auth endpoints
    register_rest_route( $ns, '/auth/login', array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'marwari_ecommerce_login_user',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $ns, '/auth/current', array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'marwari_ecommerce_get_current_user',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $ns, '/auth/debug', array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'marwari_ecommerce_debug_auth',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $ns, '/auth/logout', array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'marwari_ecommerce_logout_user',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $ns, '/auth/register', array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'marwari_ecommerce_register_user',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $ns, '/auth/verify-email', array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'marwari_ecommerce_verify_email_code',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $ns, '/auth/resend-code', array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'marwari_ecommerce_resend_verification_code',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $ns, '/auth/admin-login', array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'marwari_ecommerce_admin_direct_login',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $ns, '/admin/customers', array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'marwari_ecommerce_get_customers',
        'permission_callback' => 'marwari_ecommerce_admin_permissions_check',
    ));

    // Orders endpoints
    register_rest_route( $ns, '/orders', array(
        array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => 'marwari_ecommerce_get_orders',
            'permission_callback' => 'marwari_ecommerce_logged_in_permissions_check',
        ),
        array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => 'marwari_ecommerce_create_order',
            'permission_callback' => 'marwari_ecommerce_logged_in_permissions_check',
        )
    ));

    register_rest_route( $ns, '/orders/(?P<id>[a-zA-Z0-9\-]+)/status', array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'marwari_ecommerce_update_order_status',
        'permission_callback' => 'marwari_ecommerce_admin_permissions_check',
    ));
}

// 5. REST API: Permission Callbacks
function marwari_ecommerce_admin_permissions_check() {
    return current_user_can( 'manage_options' );
}

function marwari_ecommerce_logged_in_permissions_check() {
    return is_user_logged_in();
}

// 6. REST API Callback Functions

// A. Products Functions
function marwari_ecommerce_get_products() {
    global $wpdb;
    $results = $wpdb->get_results( "SELECT * FROM " . MARWARI_PRODUCTS_TABLE, ARRAY_A );
    
    // Format float values
    foreach ( $results as &$r ) {
        $r['price'] = floatval( $r['price'] );
    }
    return rest_ensure_response( $results );
}

function marwari_ecommerce_create_product( WP_REST_Request $request ) {
    global $wpdb;
    $params = $request->get_json_params();

    if ( empty( $params['name'] ) || empty( $params['price'] ) || empty( $params['category'] ) || empty( $params['description'] ) ) {
        return new WP_Error( 'missing_fields', 'Please fill in all required fields.', array( 'status' => 400 ) );
    }

    $id = 'prod-' . uniqid();
    $data = array(
        'id'          => $id,
        'name'        => sanitize_text_field( $params['name'] ),
        'category'    => sanitize_text_field( $params['category'] ),
        'price'       => floatval( $params['price'] ),
        'description' => sanitize_textarea_field( $params['description'] ),
        'image'       => esc_url_raw( $params['image'] ),
        'badge'       => !empty( $params['badge'] ) ? sanitize_text_field( $params['badge'] ) : null
    );

    $inserted = $wpdb->insert( MARWARI_PRODUCTS_TABLE, $data );

    if ( ! $inserted ) {
        return new WP_Error( 'db_error', 'Unable to insert product in database.', array( 'status' => 500 ) );
    }

    return rest_ensure_response( $data );
}

function marwari_ecommerce_delete_product( WP_REST_Request $request ) {
    global $wpdb;
    $id = sanitize_text_field( $request->get_param( 'id' ) );

    $deleted = $wpdb->delete( MARWARI_PRODUCTS_TABLE, array( 'id' => $id ) );

    if ( ! $deleted ) {
        return new WP_Error( 'not_found', 'Product not found or already deleted.', array( 'status' => 404 ) );
    }

    return rest_ensure_response( array( 'success' => true, 'message' => 'Product deleted' ) );
}

// B. Authentication Functions
function marwari_ecommerce_login_user( WP_REST_Request $request ) {
    $params = $request->get_json_params();
    $email = sanitize_email( $params['email'] ?? '' );

    if ( empty( $email ) ) {
        return new WP_Error( 'missing_email', 'Please enter your email address.', array( 'status' => 400 ) );
    }

    // Check if user exists
    $user = get_user_by( 'email', $email );
    if ( ! $user ) {
        return new WP_Error( 'user_not_found', 'No account found with this email. Please create an account first.', array( 'status' => 404 ) );
    }

    // Generate 6-digit code and send via email
    $code = strval( wp_rand( 100000, 999999 ) );
    set_transient( 'marwari_email_code_' . $email, $code, 600 ); // 10 min

    $subject = 'Your Mārwāri E-Commerce Login Code';
    $message  = "Hello {$user->display_name},\n\n";
    $message .= "Your login verification code is:\n\n";
    $message .= "    {$code}\n\n";
    $message .= "This code expires in 10 minutes.\n\n";
    $message .= "If you did not request this, please ignore this email.\n\n";
    $message .= "— Mārwāri E-Commerce Team";

    $headers = array( 'Content-Type: text/plain; charset=UTF-8' );
    wp_mail( $email, $subject, $message, $headers );

    return rest_ensure_response( array(
        'success'       => true,
        'requiresCode'  => true,
        'email'         => $email,
        'message'       => 'Verification code sent to your email.'
    ) );
}

function marwari_ecommerce_get_current_user() {
    if ( ! is_user_logged_in() ) {
        return rest_ensure_response( null );
    }

    $user = wp_get_current_user();
    $role = in_array( 'administrator', (array) $user->roles ) ? 'admin' : 'user';
    $phone = get_user_meta( $user->ID, 'billing_phone', true );

    return rest_ensure_response( array(
        'email' => $user->user_email,
        'name'  => $user->display_name,
        'role'  => $role,
        'phone' => $phone ? $phone : ''
    ) );
}

function marwari_ecommerce_logout_user() {
    wp_logout();
    return rest_ensure_response( array( 'success' => true ) );
}

// B2. Registration with Email Verification Code (no password required)
function marwari_ecommerce_register_user( WP_REST_Request $request ) {
    $params = $request->get_json_params();
    $name     = sanitize_text_field( $params['name'] ?? '' );
    $email    = sanitize_email( $params['email'] ?? '' );
    $phone    = sanitize_text_field( $params['phone'] ?? '' );

    // Validate inputs
    if ( empty( $name ) || empty( $email ) ) {
        return new WP_Error( 'missing_fields', 'Name and email are required.', array( 'status' => 400 ) );
    }
    if ( email_exists( $email ) ) {
        return new WP_Error( 'email_exists', 'An account with this email already exists. Please use Login instead.', array( 'status' => 409 ) );
    }

    // Create WordPress user with auto-generated password (login is via email code only)
    $username = sanitize_user( strtolower( explode( '@', $email )[0] ) . '_' . wp_rand( 100, 999 ) );
    $password = wp_generate_password( 24, true, true ); // Random secure password
    $user_id = wp_create_user( $username, $password, $email );

    if ( is_wp_error( $user_id ) ) {
        return new WP_Error( 'registration_failed', 'Could not create account: ' . $user_id->get_error_message(), array( 'status' => 500 ) );
    }

    // Update user profile
    wp_update_user( array(
        'ID'           => $user_id,
        'display_name' => $name,
        'first_name'   => explode( ' ', $name )[0],
        'last_name'    => count( explode( ' ', $name ) ) > 1 ? explode( ' ', $name, 2 )[1] : ''
    ) );
    if ( ! empty( $phone ) ) {
        update_user_meta( $user_id, 'billing_phone', $phone );
    }

    // Generate 6-digit verification code and store as transient (10 min lifespan)
    $code = strval( wp_rand( 100000, 999999 ) );
    set_transient( 'marwari_email_code_' . $email, $code, 600 );

    // Send verification code via email
    $subject = 'Your Mārwāri E-Commerce Verification Code';
    $message  = "Hello {$name},\n\n";
    $message .= "Welcome to Mārwāri E-Commerce! Your verification code is:\n\n";
    $message .= "    {$code}\n\n";
    $message .= "This code expires in 10 minutes.\n\n";
    $message .= "If you did not create this account, please ignore this email.\n\n";
    $message .= "— Mārwāri E-Commerce Team";

    $headers = array( 'Content-Type: text/plain; charset=UTF-8' );
    wp_mail( $email, $subject, $message, $headers );

    return rest_ensure_response( array(
        'success' => true,
        'message' => 'Account created. Verification code sent to your email.',
        'email'   => $email
    ) );
}

function marwari_ecommerce_verify_email_code( WP_REST_Request $request ) {
    $params = $request->get_json_params();
    $email = sanitize_email( $params['email'] ?? '' );
    $code  = sanitize_text_field( $params['code'] ?? '' );

    if ( empty( $email ) || empty( $code ) ) {
        return new WP_Error( 'missing_fields', 'Email and verification code are required.', array( 'status' => 400 ) );
    }

    // Check the stored transient code
    $saved_code = get_transient( 'marwari_email_code_' . $email );

    if ( ! $saved_code || $saved_code !== $code ) {
        return new WP_Error( 'invalid_code', 'The verification code is incorrect or has expired.', array( 'status' => 401 ) );
    }

    // Code is correct — clear it
    delete_transient( 'marwari_email_code_' . $email );

    // Find the user
    $user = get_user_by( 'email', $email );
    if ( ! $user ) {
        return new WP_Error( 'user_not_found', 'No account found with this email.', array( 'status' => 404 ) );
    }

    // Sign in the user
    wp_clear_auth_cookie();
    wp_set_current_user( $user->ID );
    wp_set_auth_cookie( $user->ID, true );

    $role  = in_array( 'administrator', (array) $user->roles ) ? 'admin' : 'user';
    $phone = get_user_meta( $user->ID, 'billing_phone', true );

    return rest_ensure_response( array(
        'success' => true,
        'user'    => array(
            'email' => $user->user_email,
            'name'  => $user->display_name,
            'role'  => $role,
            'phone' => $phone ? $phone : ''
        ),
        'nonce'   => wp_create_nonce( 'wp_rest' )
    ) );
}

function marwari_ecommerce_resend_verification_code( WP_REST_Request $request ) {
    $params = $request->get_json_params();
    $email = sanitize_email( $params['email'] ?? '' );

    if ( empty( $email ) ) {
        return new WP_Error( 'missing_email', 'Email is required.', array( 'status' => 400 ) );
    }

    $user = get_user_by( 'email', $email );
    if ( ! $user ) {
        return new WP_Error( 'user_not_found', 'No account found with this email.', array( 'status' => 404 ) );
    }

    // Generate new code
    $code = strval( wp_rand( 100000, 999999 ) );
    set_transient( 'marwari_email_code_' . $email, $code, 600 );

    // Send code via email
    $subject = 'Your New Mārwāri E-Commerce Verification Code';
    $message  = "Hello {$user->display_name},\n\n";
    $message .= "Your new verification code is:\n\n";
    $message .= "    {$code}\n\n";
    $message .= "This code expires in 10 minutes.\n\n";
    $message .= "— Mārwāri E-Commerce Team";

    $headers = array( 'Content-Type: text/plain; charset=UTF-8' );
    wp_mail( $email, $subject, $message, $headers );

    return rest_ensure_response( array(
        'success' => true,
        'message' => 'A new verification code has been sent to your email.'
    ) );
}

// C. Orders Functions
function marwari_ecommerce_get_orders() {
    global $wpdb;
    $user = wp_get_current_user();

    if ( in_array( 'administrator', (array) $user->roles ) ) {
        // Admins see all orders
        $results = $wpdb->get_results( "SELECT * FROM " . MARWARI_ORDERS_TABLE . " ORDER BY date DESC", ARRAY_A );
    } else {
        // Standard user sees their own orders
        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM " . MARWARI_ORDERS_TABLE . " WHERE user_email = %s ORDER BY date DESC",
            $user->user_email
        ), ARRAY_A );
    }

    // Decode JSON strings into objects
    foreach ( $results as &$r ) {
        $r['items'] = json_decode( $r['items'] );
        $r['total'] = floatval( $r['total'] );
        $r['shippingDetails'] = json_decode( $r['shipping_details'] );
        unset( $r['shipping_details'] ); // Standardize key names
    }

    return rest_ensure_response( $results );
}

function marwari_ecommerce_create_order( WP_REST_Request $request ) {
    global $wpdb;
    $params = $request->get_json_params();
    $user = wp_get_current_user();

    if ( empty( $params['items'] ) || empty( $params['total'] ) || empty( $params['shippingDetails'] ) ) {
        return new WP_Error( 'missing_fields', 'Checkout request contains incomplete cart parameters.', array( 'status' => 400 ) );
    }

    $id = 'ord-' . uniqid();
    $data = array(
        'id'               => $id,
        'user_email'       => $user->user_email,
        'items'            => json_encode( $params['items'] ),
        'total'            => floatval( $params['total'] ),
        'status'           => 'Pending',
        'date'             => current_time( 'mysql' ),
        'shipping_details' => json_encode( $params['shippingDetails'] )
    );

    $inserted = $wpdb->insert( MARWARI_ORDERS_TABLE, $data );

    if ( ! $inserted ) {
        return new WP_Error( 'db_error', 'Failed to save order transaction to WordPress database.', array( 'status' => 500 ) );
    }

    // Format response matching front-end schema
    $data['items'] = $params['items'];
    $data['shippingDetails'] = $params['shippingDetails'];
    unset( $data['shipping_details'] );
    unset( $data['user_email'] );
    $data['userEmail'] = $user->user_email;

    return rest_ensure_response( $data );
}

function marwari_ecommerce_update_order_status( WP_REST_Request $request ) {
    global $wpdb;
    $id = sanitize_text_field( $request->get_param( 'id' ) );
    $params = $request->get_json_params();
    $status = !empty( $params['status'] ) ? sanitize_text_field( $params['status'] ) : 'Completed';

    $updated = $wpdb->update(
        MARWARI_ORDERS_TABLE,
        array( 'status' => $status ),
        array( 'id' => $id )
    );

    if ( $updated === false ) {
        return new WP_Error( 'db_error', 'Failed to update order status.', array( 'status' => 500 ) );
    }

    return rest_ensure_response( array( 'success' => true, 'status' => $status ) );
}

// D0. Get All Customers (Admin Only)
function marwari_ecommerce_get_customers() {
    global $wpdb;
    
    $users = get_users( array(
        'role__not_in' => array( 'administrator' ),
        'orderby'      => 'registered',
        'order'        => 'DESC'
    ) );

    $customers = array();
    foreach ( $users as $u ) {
        $phone = get_user_meta( $u->ID, 'billing_phone', true );
        $order_count = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM " . MARWARI_ORDERS_TABLE . " WHERE user_email = %s",
            $u->user_email
        ) );

        $customers[] = array(
            'name'       => $u->display_name,
            'email'      => $u->user_email,
            'phone'      => $phone ? $phone : '—',
            'orders'     => intval( $order_count ),
            'registered' => $u->user_registered
        );
    }

    return rest_ensure_response( $customers );
}

// D. Admin Direct Login (uses wp_check_password, bypasses wp_authenticate hooks)
function marwari_ecommerce_admin_direct_login( WP_REST_Request $request ) {
    $params = $request->get_json_params();
    $email    = sanitize_email( $params['email'] ?? '' );
    $password = $params['password'] ?? '';

    if ( empty( $email ) || empty( $password ) ) {
        return new WP_Error( 'missing_fields', 'Email and password are required.', array( 'status' => 400 ) );
    }

    $user = get_user_by( 'email', $email );
    if ( ! $user ) {
        return new WP_Error( 'invalid_credentials', 'Invalid email or password.', array( 'status' => 401 ) );
    }

    // Direct password check (no hooks/filters that cause permission issues)
    if ( ! wp_check_password( $password, $user->user_pass, $user->ID ) ) {
        return new WP_Error( 'invalid_credentials', 'Invalid email or password.', array( 'status' => 401 ) );
    }

    // Must be admin
    if ( ! in_array( 'administrator', (array) $user->roles ) ) {
        return new WP_Error( 'not_admin', 'Access denied. Admin privileges required.', array( 'status' => 403 ) );
    }

    // Log in
    wp_clear_auth_cookie();
    wp_set_current_user( $user->ID );
    wp_set_auth_cookie( $user->ID, true );

    $phone = get_user_meta( $user->ID, 'billing_phone', true );

    return rest_ensure_response( array(
        'success' => true,
        'user'    => array(
            'email' => $user->user_email,
            'name'  => $user->display_name,
            'role'  => 'admin',
            'phone' => $phone ? $phone : ''
        ),
        'nonce'   => wp_create_nonce( 'wp_rest' )
    ) );
}

// 5. Admin Panel Shortcode: [marwari_admin_panel] — dedicated admin dashboard
add_shortcode( 'marwari_admin_panel', 'marwari_ecommerce_render_admin_panel' );
function marwari_ecommerce_render_admin_panel() {
    ob_start();
    ?>
    <div class="marwari-ecommerce-wrapper admin-dashboard-page">
        <div class="toast-container" id="toast-container"></div>

        <!-- Admin Header -->
        <header class="navbar">
            <div class="container nav-container">
                <a href="/shop/" class="logo"><span>Mārwāri</span> Dashboard</a>
                <div class="nav-actions">
                    <button class="nav-btn" id="theme-toggle" title="Switch Theme"><span id="theme-icon"></span></button>
                    <div class="user-profile-menu" id="user-menu-container"></div>
                </div>
            </div>
        </header>

        <!-- Admin Login Screen (shown when not logged in) -->
        <div class="admin-login-screen" id="admin-login-screen">
            <div class="admin-login-card">
                <div class="admin-login-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" stroke="var(--primary)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <h2>Admin Dashboard</h2>
                    <p>Secure login for authorized administrators only</p>
                </div>
                <form id="admin-login-form">
                    <div class="form-group">
                        <label for="admin-login-email">Admin Email</label>
                        <input type="email" id="admin-login-email" class="form-input" placeholder="admin@gmail.com" required>
                    </div>
                    <div class="form-group">
                        <label for="admin-login-password">Password</label>
                        <input type="password" id="admin-login-password" class="form-input" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="auth-submit-btn" id="admin-login-btn">Access Dashboard</button>
                </form>
            </div>
        </div>

        <!-- Admin Dashboard Content (shown after login) -->
        <div class="admin-dashboard-content" id="admin-dashboard-content" style="display:none;">
            <div class="container" style="padding-top:2rem; padding-bottom:4rem;">
                <!-- Welcome Banner -->
                <div class="admin-welcome-banner">
                    <div>
                        <h2 id="admin-welcome-name">Welcome, Admin</h2>
                        <p>Here's your store performance overview</p>
                    </div>
                    <a href="/shop/" class="btn-primary" style="text-decoration:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7M5 12h14"/></svg>
                        Visit Store
                    </a>
                </div>

                <!-- Stats Grid -->
                <div class="admin-stats-grid" style="margin-top:1.5rem;">
                    <div class="stat-card">
                        <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
                        <div class="stat-details"><h4>Total Revenue</h4><p id="dash-stat-revenue">₹0</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg></div>
                        <div class="stat-details"><h4>Products</h4><p id="dash-stat-products">0</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg></div>
                        <div class="stat-details"><h4>Orders</h4><p id="dash-stat-orders">0</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                        <div class="stat-details"><h4>Customers</h4><p id="dash-stat-customers">0</p></div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="admin-charts-row">
                    <div class="admin-panel-card">
                        <h3>📊 Revenue Overview</h3>
                        <div class="chart-container" id="revenue-chart"></div>
                    </div>
                    <div class="admin-panel-card">
                        <h3>📦 Order Status</h3>
                        <div class="chart-container" id="order-status-chart"></div>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="admin-panel-card" style="margin-top:2rem;">
                    <h3>🛒 All Customer Orders</h3>
                    <p style="font-size:0.75rem; color:var(--text-muted); margin-bottom:1rem;">Click on 'Pending' to mark as 'Completed'.</p>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead><tr><th>Order ID</th><th>Customer</th><th>Items</th><th>Total</th><th>Date</th><th>Status</th></tr></thead>
                            <tbody id="dash-orders-table"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="admin-panel-card" style="margin-top:2rem;">
                    <h3>📋 Product Catalog</h3>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Actions</th></tr></thead>
                            <tbody id="dash-products-table"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Add Product Form -->
                <div class="admin-panel-card" style="margin-top:2rem;">
                    <h3>➕ Add New Product</h3>
                    <form id="admin-add-product-form">
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                            <div class="form-group"><label for="admin-pname">Product Name</label><input type="text" id="admin-pname" class="form-input" required></div>
                            <div class="form-group"><label for="admin-pcategory">Category</label><select id="admin-pcategory" class="form-input" required><option value="Apparel">Royal Apparel</option><option value="Handicrafts">Handicrafts</option><option value="Jewelry">Jewelry</option><option value="Sweets & Spices">Sweets & Spices</option></select></div>
                            <div class="form-group"><label for="admin-pprice">Price (₹)</label><input type="number" id="admin-pprice" class="form-input" required min="1"></div>
                            <div class="form-group"><label for="admin-pimage">Image URL</label><input type="url" id="admin-pimage" class="form-input"></div>
                            <div class="form-group"><label for="admin-pbadge">Badge</label><input type="text" id="admin-pbadge" class="form-input" placeholder="e.g. Bestseller"></div>
                        </div>
                        <div class="form-group" style="margin-top:0.5rem;"><label for="admin-pdesc">Description</label><textarea id="admin-pdesc" class="form-input" rows="3" required></textarea></div>
                        <button type="submit" class="auth-submit-btn" style="margin-top:1rem;">Publish Product</button>
                    </form>
                </div>

                <!-- Customers Table -->
                <div class="admin-panel-card" style="margin-top:2rem;">
                    <h3>👥 Registered Customers</h3>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Orders</th></tr></thead>
                            <tbody id="dash-customers-table"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <div class="footer-bottom">
                <p>&copy; 2026 Mārwāri E-Commerce Admin Dashboard. Authorized Access Only.</p>
            </div>
        </footer>
    </div>
    <?php
    return ob_get_clean();
}

// 6. Intercept template load and render a clean storefront directly to bypass theme header/footer
add_action( 'template_redirect', 'marwari_ecommerce_direct_template_redirect' );
function marwari_ecommerce_direct_template_redirect() {
    global $post;
    if ( ! is_singular() || ! is_a( $post, 'WP_Post' ) ) return;

    $is_shop  = has_shortcode( $post->post_content, 'marwari_storefront' );
    $is_admin = has_shortcode( $post->post_content, 'marwari_admin_panel' );

    if ( $is_shop || $is_admin ) {
        $shortcode = $is_admin ? '[marwari_admin_panel]' : '[marwari_storefront]';
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo( 'charset' ); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <?php wp_head(); ?>
        </head>
        <body <?php body_class(); ?>>
            <?php echo do_shortcode( $shortcode ); ?>
            <?php wp_footer(); ?>
        </body>
        </html>
        <?php
        exit;
    }
}
