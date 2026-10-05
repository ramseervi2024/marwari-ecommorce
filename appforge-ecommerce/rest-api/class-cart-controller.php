<?php

class AppForge_Cart_Controller {
    
    public function register_routes( $namespace ) {
        register_rest_route( $namespace, '/cart', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_cart' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( $namespace, '/cart/items', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'add_item' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( $namespace, '/cart/items/(?P<id>\w+)', array(
            'methods'             => 'PUT',
            'callback'            => array( $this, 'update_item' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( $namespace, '/cart/items/(?P<id>\w+)', array(
            'methods'             => 'DELETE',
            'callback'            => array( $this, 'delete_item' ),
            'permission_callback' => '__return_true',
        ) );
    }

    public function get_cart( $request ) {
        if ( null === WC()->cart ) {
            wc_load_cart();
        }
        $cart = WC()->cart->get_cart();
        return new WP_REST_Response( $cart, 200 );
    }

    public function add_item( $request ) {
        if ( null === WC()->cart ) {
            wc_load_cart();
        }
        $product_id = sanitize_text_field( $request->get_param( 'product_id' ) );
        $quantity = sanitize_text_field( $request->get_param( 'quantity' ) ) ?: 1;
        
        $added = WC()->cart->add_to_cart( $product_id, $quantity );
        if ( $added ) {
            return new WP_REST_Response( array( 'success' => true, 'cart_key' => $added ), 200 );
        }
        return new WP_Error( 'add_failed', 'Could not add to cart', array( 'status' => 400 ) );
    }

    public function update_item( $request ) {
        if ( null === WC()->cart ) {
            wc_load_cart();
        }
        $cart_item_key = $request['id'];
        $quantity = sanitize_text_field( $request->get_param( 'quantity' ) );
        
        $updated = WC()->cart->set_quantity( $cart_item_key, $quantity );
        return new WP_REST_Response( array( 'success' => $updated ), 200 );
    }

    public function delete_item( $request ) {
        if ( null === WC()->cart ) {
            wc_load_cart();
        }
        $cart_item_key = $request['id'];
        $removed = WC()->cart->remove_cart_item( $cart_item_key );
        return new WP_REST_Response( array( 'success' => $removed ), 200 );
    }
}
