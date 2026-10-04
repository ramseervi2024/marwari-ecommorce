<?php
/**
 * Plugin Name: Force 404 Redirect to Home
 * Description: Automatically redirects any wrong URL or 404 Not Found error back to the main landing page.
 * Version: 1.0
 * Author: RPS Digital World
 */

add_action('template_redirect', function () {
    if (is_404()) {
        wp_redirect(home_url('/'), 301);
        exit;
    }
});
