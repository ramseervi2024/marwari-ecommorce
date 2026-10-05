<?php

class AppForge_Plugin {

    public function init() {
        $this->load_dependencies();
        $this->register_api_routes();
        $this->setup_admin();
    }

    private function load_dependencies() {
        require_once APPFORGE_PLUGIN_DIR . 'rest-api/class-routes.php';
        require_once APPFORGE_PLUGIN_DIR . 'rest-api/class-home-controller.php';
        require_once APPFORGE_PLUGIN_DIR . 'rest-api/class-auth-controller.php';
        require_once APPFORGE_PLUGIN_DIR . 'rest-api/class-product-controller.php';
        require_once APPFORGE_PLUGIN_DIR . 'rest-api/class-cart-controller.php';
        require_once APPFORGE_PLUGIN_DIR . 'rest-api/class-order-controller.php';
    }

    private function register_api_routes() {
        $routes = new AppForge_REST_Routes();
        add_action( 'rest_api_init', array( $routes, 'register_routes' ) );
    }

    private function setup_admin() {
        // Admin setup logic
    }
}
