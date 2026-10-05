<?php

class AppForge_Product_Controller {
    
    public function register_routes( $namespace ) {
        register_rest_route( $namespace, '/categories', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_categories' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( $namespace, '/products', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_products' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( $namespace, '/products/(?P<id>\d+)', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_product' ),
            'permission_callback' => '__return_true',
        ) );
    }

    public function get_categories( $request ) {
        $terms = get_terms( array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
        ) );
        return new WP_REST_Response( $terms, 200 );
    }

    public function get_products( $request ) {
        $args = array(
            'status' => 'publish',
            'limit' => -1,
        );
        $products = wc_get_products( $args );
        $data = array();
        foreach ( $products as $product ) {
            $data[] = $product->get_data();
        }
        return new WP_REST_Response( $data, 200 );
    }

    public function get_product( $request ) {
        $product_id = $request['id'];
        $product = wc_get_product( $product_id );
        if ( ! $product ) {
            return new WP_Error( 'no_product', 'Invalid product ID', array( 'status' => 404 ) );
        }
        return new WP_REST_Response( $product->get_data(), 200 );
    }
}
