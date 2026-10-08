<?php

class WP_ECommerce_API {

    public function init() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    public function register_routes() {
        // Core CRUD entities
        $routes = ['products', 'categories', 'orders', 'users', 'coupons'];
        foreach ($routes as $route) {
            register_rest_route( 'wp-ecommerce/v1', '/' . $route, array(
                array(
                    'methods'  => 'GET',
                    'callback' => array( $this, 'get_entity' ),
                    'permission_callback' => '__return_true',
                ),
                array(
                    'methods'  => 'POST',
                    'callback' => array( $this, 'update_entity' ),
                    'permission_callback' => '__return_true',
                )
            ) );
        }

        // Single Product Detail
        register_rest_route( 'wp-ecommerce/v1', '/products/(?P<id>[a-zA-Z0-9_-]+)', array(
            'methods'  => 'GET',
            'callback' => array( $this, 'get_single_product' ),
            'permission_callback' => '__return_true',
        ) );

        // Unified Mobile Home Feed
        register_rest_route( 'wp-ecommerce/v1', '/home', array(
            'methods'  => 'GET',
            'callback' => array( $this, 'get_home_feed' ),
            'permission_callback' => '__return_true',
        ) );

        // Customer Authentication Endpoints
        register_rest_route( 'wp-ecommerce/v1', '/auth/login', array(
            'methods'  => 'POST',
            'callback' => array( $this, 'handle_login' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( 'wp-ecommerce/v1', '/auth/register', array(
            'methods'  => 'POST',
            'callback' => array( $this, 'handle_register' ),
            'permission_callback' => '__return_true',
        ) );

        // OpenAPI / Swagger JSON Specification
        register_rest_route( 'wp-ecommerce/v1', '/swagger', array(
            'methods'  => 'GET',
            'callback' => array( $this, 'get_swagger_spec' ),
            'permission_callback' => '__return_true',
        ) );
        register_rest_route( 'wp-ecommerce/v1', '/openapi.json', array(
            'methods'  => 'GET',
            'callback' => array( $this, 'get_swagger_spec' ),
            'permission_callback' => '__return_true',
        ) );

        // Media Upload
        register_rest_route( 'wp-ecommerce/v1', '/upload', array(
            'methods'  => 'POST',
            'callback' => array( $this, 'handle_upload' ),
            'permission_callback' => '__return_true',
        ) );
    }

    public function get_entity( $request ) {
        $route = str_replace('/wp-ecommerce/v1/', '', $request->get_route());
        $route = explode('?', $route)[0];
        $route = trim($route, '/');
        $data = get_option('marwari_api_' . $route, array());
        
        // If empty, supply default seed if products or categories
        if (empty($data)) {
            $data = $this->get_default_seed($route);
        }

        return new WP_REST_Response( $data, 200 );
    }

    public function get_single_product( $request ) {
        $id = $request->get_param('id');
        $products = get_option('marwari_api_products', array());
        if (empty($products)) {
            $products = $this->get_default_seed('products');
        }

        $found = null;
        foreach ($products as $p) {
            if ($p['id'] === $id || $p['id'] === 'prod-' . $id || str_replace('prod-', '', $p['id']) === (string)$id) {
                $found = $p;
                break;
            }
        }

        if (!$found && is_numeric($id)) {
            $idx = intval($id) - 1;
            if (isset($products[$idx])) {
                $found = $products[$idx];
            }
        }

        if ($found) {
            return new WP_REST_Response( array( 'product' => $found ), 200 );
        }

        return new WP_REST_Response( array( 'success' => false, 'message' => 'Product not found' ), 404 );
    }

    public function get_home_feed( $request ) {
        $products = get_option('marwari_api_products', array());
        if (empty($products)) $products = $this->get_default_seed('products');

        $categories = get_option('marwari_api_categories', array());
        if (empty($categories)) $categories = $this->get_default_seed('categories');

        $feed = array(
            'banners' => array(
                array(
                    'id' => 'b-1',
                    'title' => 'The Royal Heritage of Rajasthan',
                    'subtitle' => 'Handcrafted by Master Artisans of Marwar',
                    'image' => 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?auto=format&fit=crop&w=1200&q=80',
                    'action_url' => '/category/Royal Apparel'
                )
            ),
            'categories' => $categories,
            'featured_products' => array_slice($products, 0, 8),
            'heritage_cities' => array(
                array('name' => 'Jodhpur', 'specialty' => 'Royal Bandhgala & Mojaris', 'image' => 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&w=400&q=80'),
                array('name' => 'Jaipur', 'specialty' => 'Blue Pottery & Bandhani', 'image' => 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=400&q=80'),
                array('name' => 'Udaipur', 'specialty' => 'Silver & Meenakari Art', 'image' => 'https://images.unsplash.com/photo-1630019852942-f89202989a59?auto=format&fit=crop&w=400&q=80'),
                array('name' => 'Bikaner', 'specialty' => 'Sweets, Spices & Bhujia', 'image' => 'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?auto=format&fit=crop&w=400&q=80')
            )
        );

        return new WP_REST_Response( $feed, 200 );
    }

    public function handle_login( $request ) {
        $params = $request->get_json_params();
        $email = isset($params['email']) ? sanitize_email($params['email']) : '';
        $password = isset($params['password']) ? $params['password'] : '';

        $users = get_option('marwari_api_users', array());
        if (empty($users)) $users = $this->get_default_seed('users');

        $user = null;
        foreach ($users as $u) {
            if ((strtolower($u['email']) === strtolower($email) || strtolower($u['username']) === strtolower($email)) && $u['password'] === $password) {
                $user = $u;
                break;
            }
        }

        if ($user) {
            $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.' . base64_encode(json_encode(array('email' => $user['email'], 'role' => $user['role'], 'exp' => time() + 86400))) . '.signature';
            return new WP_REST_Response( array( 'success' => true, 'token' => $token, 'user' => $user ), 200 );
        }

        return new WP_REST_Response( array( 'success' => false, 'message' => 'Invalid email or password' ), 401 );
    }

    public function handle_register( $request ) {
        $params = $request->get_json_params();
        $email = isset($params['email']) ? sanitize_email($params['email']) : '';
        $name = isset($params['name']) ? sanitize_text_field($params['name']) : '';
        $phone = isset($params['phone']) ? sanitize_text_field($params['phone']) : '';
        $password = isset($params['password']) ? $params['password'] : '';

        if (empty($email) || empty($password)) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Email and password required' ), 400 );
        }

        $users = get_option('marwari_api_users', array());
        if (empty($users)) $users = $this->get_default_seed('users');

        foreach ($users as $u) {
            if (strtolower($u['email']) === strtolower($email)) {
                return new WP_REST_Response( array( 'success' => false, 'message' => 'Email already registered' ), 400 );
            }
        }

        $newUser = array(
            'username' => sanitize_user(explode('@', $email)[0]),
            'email' => $email,
            'name' => $name,
            'phone' => $phone,
            'password' => $password,
            'role' => 'user',
            'status' => 'active',
            'addresses' => array()
        );

        $users[] = $newUser;
        update_option('marwari_api_users', $users);

        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.' . base64_encode(json_encode(array('email' => $newUser['email'], 'role' => 'user', 'exp' => time() + 86400))) . '.signature';
        return new WP_REST_Response( array( 'success' => true, 'token' => $token, 'user' => $newUser ), 201 );
    }

    public function get_swagger_spec( $request ) {
        $swagger_file = WP_ECOMMERCE_PLUGIN_DIR . 'swagger.json';
        if (file_exists($swagger_file)) {
            $json = json_decode(file_get_contents($swagger_file), true);
            return new WP_REST_Response( $json, 200 );
        }
        return new WP_REST_Response( array( 'error' => 'Swagger specification not found' ), 404 );
    }

    public function update_entity( $request ) {
        $route = str_replace('/wp-ecommerce/v1/', '', $request->get_route());
        $route = explode('?', $route)[0];
        $route = trim($route, '/');
        $data = $request->get_json_params();
        update_option('marwari_api_' . $route, $data);
        return new WP_REST_Response( array('success' => true, 'updated' => $route), 200 );
    }

    public function handle_upload( $request ) {
        if ( empty( $_FILES ) ) {
            return new WP_Error( 'no_files', 'No files found', array( 'status' => 400 ) );
        }

        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );

        $uploaded_file = $_FILES['file'];
        $upload_overrides = array( 'test_form' => false );

        $movefile = wp_handle_upload( $uploaded_file, $upload_overrides );

        if ( $movefile && ! isset( $movefile['error'] ) ) {
            return new WP_REST_Response( array( 'success' => true, 'url' => $movefile['url'] ), 200 );
        } else {
            return new WP_Error( 'upload_error', $movefile['error'], array( 'status' => 500 ) );
        }
    }

    private function get_default_seed($type) {
        if ($type === 'products') {
            return array(
                array("id" => "prod-1", "name" => "Royal Jaipuri Silk Bandhani Saree", "category" => "Royal Apparel", "price" => 8499, "stock" => 15, "status" => "active", "image" => "https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=600&q=80", "badge" => "Bestseller"),
                array("id" => "prod-2", "name" => "Classic Navy Blue Royal Jodhpuri Suit", "category" => "Royal Apparel", "price" => 12999, "stock" => 8, "status" => "active", "image" => "https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&w=600&q=80", "badge" => "Royal Exclusive"),
                array("id" => "prod-3", "name" => "Handcrafted Gold-Leaf Jaipuri Quilt", "category" => "Handicrafts", "price" => 3499, "stock" => 25, "status" => "active", "image" => "https://images.unsplash.com/photo-1583847268964-b28dc8f51f92?auto=format&fit=crop&w=600&q=80", "badge" => "100% Cotton"),
                array("id" => "prod-4", "name" => "Pure Silver Meenakari Pearl Jhumkas", "category" => "Silver Jewellery", "price" => 4999, "stock" => 12, "status" => "active", "image" => "https://images.unsplash.com/photo-1630019852942-f89202989a59?auto=format&fit=crop&w=600&q=80", "badge" => "Handmade"),
                array("id" => "prod-5", "name" => "Traditional Camel Leather Mojaris", "category" => "Marwari Mojari", "price" => 1899, "stock" => 20, "status" => "active", "image" => "https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=600&q=80", "badge" => "Artisan Leather"),
                array("id" => "prod-6", "name" => "Premium Saffron & Cardamom Kesaria Peda", "category" => "Food & Spices", "price" => 899, "stock" => 40, "status" => "active", "image" => "https://images.unsplash.com/photo-1587314168485-3236d6710814?auto=format&fit=crop&w=600&q=80", "badge" => "Freshly Made"),
                array("id" => "prod-7", "name" => "Jaipur Traditional Blue Pottery Vase", "category" => "Handicrafts", "price" => 2299, "stock" => 0, "status" => "active", "image" => "https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=600&q=80", "badge" => "Heritage Art"),
                array("id" => "prod-20", "name" => "Imperial Udaipur Heritage Silver Peacock Box", "category" => "Handicrafts", "price" => 7899, "stock" => 5, "status" => "active", "image" => "https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?auto=format&fit=crop&w=600&q=80", "badge" => "Royal Masterpiece")
            );
        }
        if ($type === 'categories') {
            return array(
                array("id" => "cat-1", "name" => "Royal Apparel", "slug" => "Royal Apparel", "image" => "https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=400&q=80"),
                array("id" => "cat-2", "name" => "Handicrafts", "slug" => "Handicrafts", "image" => "https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=400&q=80"),
                array("id" => "cat-3", "name" => "Silver Jewellery", "slug" => "Silver Jewellery", "image" => "https://images.unsplash.com/photo-1630019852942-f89202989a59?auto=format&fit=crop&w=400&q=80"),
                array("id" => "cat-4", "name" => "Marwari Mojari", "slug" => "Marwari Mojari", "image" => "https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=400&q=80"),
                array("id" => "cat-5", "name" => "Food & Spices", "slug" => "Food & Spices", "image" => "https://images.unsplash.com/photo-1587314168485-3236d6710814?auto=format&fit=crop&w=400&q=80"),
                array("id" => "cat-6", "name" => "Home & Décor", "slug" => "Home & Décor", "image" => "https://images.unsplash.com/photo-1583847268964-b28dc8f51f92?auto=format&fit=crop&w=400&q=80"),
                array("id" => "cat-7", "name" => "Art & Collectibles", "slug" => "Art & Collectibles", "image" => "https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?auto=format&fit=crop&w=400&q=80")
            );
        }
        if ($type === 'users') {
            return array(
                array("username" => "admin", "email" => "admin@marwari.com", "password" => "123456", "name" => "Marwari Admin", "role" => "admin", "phone" => "9876543210", "status" => "active", "verified" => true),
                array("username" => "user", "email" => "user@gmail.com", "password" => "password123", "name" => "Ramesh Seervi", "role" => "user", "phone" => "9001122334", "status" => "active", "verified" => true, "addresses" => array(
                    array("id" => "addr-1", "label" => "Primary Palace", "street" => "12 Heritage Lane, Paota", "city" => "Jodhpur", "state" => "Rajasthan", "zip" => "342001", "default" => true),
                    array("id" => "addr-2", "label" => "City Haveli", "street" => "45 Nai Sarak, Clock Tower", "city" => "Jodhpur", "state" => "Rajasthan", "zip" => "342002", "default" => false)
                ))
            );
        }
        if ($type === 'coupons') {
            return array(
                array("id" => "cp-1", "code" => "WELCOME10", "type" => "percentage", "amount" => 10, "usage_limit" => 100, "used_count" => 14, "expiry" => "2027-12-31"),
                array("id" => "cp-2", "code" => "ROYAL500", "type" => "flat", "amount" => 500, "usage_limit" => 50, "used_count" => 8, "expiry" => "2027-06-30"),
                array("id" => "cp-3", "code" => "MARWARI20", "type" => "percentage", "amount" => 20, "usage_limit" => 200, "used_count" => 35, "expiry" => "2027-12-31")
            );
        }
        return array();
    }
}
