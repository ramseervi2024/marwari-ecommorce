<?php

class AppForge_Auth_Controller {
    
    public function register_routes( $namespace ) {
        register_rest_route( $namespace, '/auth/login', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'login' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( $namespace, '/auth/register', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'register' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( $namespace, '/me', array(
            'methods'             => array('GET', 'PUT'),
            'callback'            => array( $this, 'me' ),
            'permission_callback' => 'is_user_logged_in',
        ) );
    }

    public function login( $request ) {
        $creds = array(
            'user_login'    => sanitize_text_field( $request->get_param( 'username' ) ),
            'user_password' => sanitize_text_field( $request->get_param( 'password' ) ),
            'remember'      => true
        );

        $user = wp_signon( $creds, false );

        if ( is_wp_error( $user ) ) {
            return new WP_Error( 'login_failed', $user->get_error_message(), array( 'status' => 401 ) );
        }

        return new WP_REST_Response( array( 'success' => true, 'user_id' => $user->ID, 'token' => wp_create_nonce('wp_rest') ), 200 );
    }

    public function register( $request ) {
        $email = sanitize_email( $request->get_param( 'email' ) );
        $password = sanitize_text_field( $request->get_param( 'password' ) );
        
        $user_id = wc_create_new_customer( $email, $email, $password );
        
        if ( is_wp_error( $user_id ) ) {
            return new WP_Error( 'registration_failed', $user_id->get_error_message(), array( 'status' => 400 ) );
        }
        
        return new WP_REST_Response( array( 'success' => true, 'user_id' => $user_id ), 201 );
    }

    public function me( $request ) {
        $user = wp_get_current_user();
        if ( $request->get_method() === 'PUT' ) {
            // Update user data logic here
            return new WP_REST_Response( array( 'success' => true ), 200 );
        }
        return new WP_REST_Response( $user->to_array(), 200 );
    }
}
