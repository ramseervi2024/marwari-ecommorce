<?php

class AppForge_Order_Controller {
    
    public function register_routes( $namespace ) {
        register_rest_route( $namespace, '/orders', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_orders' ),
            'permission_callback' => 'is_user_logged_in',
        ) );

        register_rest_route( $namespace, '/orders/(?P<id>\d+)', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_order' ),
            'permission_callback' => 'is_user_logged_in',
        ) );
    }

    public function get_orders( $request ) {
        $user_id = get_current_user_id();
        $orders = wc_get_orders( array(
            'customer' => $user_id,
            'limit' => -1,
        ) );
        $data = array();
        foreach ( $orders as $order ) {
            $data[] = $order->get_data();
        }
        return new WP_REST_Response( $data, 200 );
    }

    public function get_order( $request ) {
        $order_id = $request['id'];
        $order = wc_get_order( $order_id );
        
        if ( ! $order ) {
            return new WP_Error( 'no_order', 'Invalid order ID', array( 'status' => 404 ) );
        }
        
        // Ensure user can only view their own order
        if ( $order->get_customer_id() !== get_current_user_id() ) {
            return new WP_Error( 'unauthorized', 'You are not allowed to view this order', array( 'status' => 403 ) );
        }
        
        return new WP_REST_Response( $order->get_data(), 200 );
    }
}
