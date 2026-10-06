<?php

class WP_ECommerce_API {

    public function init() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    public function register_routes() {
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

        register_rest_route( 'wp-ecommerce/v1', '/upload', array(
            'methods'  => 'POST',
            'callback' => array( $this, 'handle_upload' ),
            'permission_callback' => '__return_true',
        ) );
    }

    public function get_entity( $request ) {
        $route = str_replace('/wp-ecommerce/v1/', '', $request->get_route());
        $data = get_option('marwari_api_' . $route, array());
        return new WP_REST_Response( $data, 200 );
    }

    public function update_entity( $request ) {
        $route = str_replace('/wp-ecommerce/v1/', '', $request->get_route());
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
}
