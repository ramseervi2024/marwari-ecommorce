<?php

class WP_ECommerce_API {

    public function init() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    public function register_routes() {
        register_rest_route( 'wp-ecommerce/v1', '/products', array(
            'methods'  => 'GET',
            'callback' => array( $this, 'get_products' ),
            'permission_callback' => '__return_true', // In production, add proper permissions
        ) );
    }

    public function get_products( $request ) {
        // Fetch products from database
        $products = array(
            array(
                'id' => 1,
                'name' => 'Sample Product 1',
                'price' => '19.99'
            ),
            array(
                'id' => 2,
                'name' => 'Sample Product 2',
                'price' => '29.99'
            )
        );

        return new WP_REST_Response( $products, 200 );
    }
}
