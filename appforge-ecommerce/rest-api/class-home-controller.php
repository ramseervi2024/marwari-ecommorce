<?php

class AppForge_Home_Controller {
    
    public function register_routes( $namespace ) {
        register_rest_route( $namespace, '/home', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_home_data' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( $namespace, '/config', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_config' ),
            'permission_callback' => '__return_true',
        ) );
    }

    public function get_home_data( $request ) {
        // Mock data as per spec
        $data = array(
            'banners' => array(),
            'categories' => array(),
            'featuredProducts' => array(),
            'bestSellingProducts' => array(),
            'newProducts' => array(),
            'saleProducts' => array()
        );
        return new WP_REST_Response( $data, 200 );
    }

    public function get_config( $request ) {
        // Mock config as per spec
        $data = array(
            'appName' => 'AppForge Store',
            'currency' => 'INR',
            'currencySymbol' => '₹',
            'primaryColor' => '#000000',
            'secondaryColor' => '#FFFFFF',
            'logo' => 'https://example.com/logo.png',
            'enableWishlist' => true,
            'enableReviews' => true,
            'enableCod' => true,
            'enablePushNotifications' => true
        );
        return new WP_REST_Response( $data, 200 );
    }
}
