<?php
/**
 * Plugin Name:       AppForge E-Commerce
 * Description:       Custom plugin for AppForge E-Commerce extending WooCommerce for the mobile app and website.
 * Version:           1.0.0
 * Author:            AppForge
 * Text Domain:       appforge-ecommerce
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'APPFORGE_VERSION', '1.0.0' );
define( 'APPFORGE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Include core plugin class
require_once APPFORGE_PLUGIN_DIR . 'includes/class-plugin.php';

function run_appforge_ecommerce() {
    $plugin = new AppForge_Plugin();
    $plugin->init();
}

// Ensure WooCommerce is active before running
add_action( 'plugins_loaded', function() {
    if ( class_exists( 'WooCommerce' ) ) {
        run_appforge_ecommerce();
    } else {
        add_action( 'admin_notices', function() {
            echo '<div class="error"><p><strong>AppForge E-Commerce</strong> requires WooCommerce to be installed and active.</p></div>';
        });
    }
});
