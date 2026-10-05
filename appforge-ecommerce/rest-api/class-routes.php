<?php

class AppForge_REST_Routes {

    const NAMESPACE = 'appforge/v1';

    public function register_routes() {
        $home_controller = new AppForge_Home_Controller();
        $home_controller->register_routes( self::NAMESPACE );

        $auth_controller = new AppForge_Auth_Controller();
        $auth_controller->register_routes( self::NAMESPACE );

        $product_controller = new AppForge_Product_Controller();
        $product_controller->register_routes( self::NAMESPACE );

        $cart_controller = new AppForge_Cart_Controller();
        $cart_controller->register_routes( self::NAMESPACE );

        $order_controller = new AppForge_Order_Controller();
        $order_controller->register_routes( self::NAMESPACE );
    }
}
