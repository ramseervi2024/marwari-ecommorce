<?php
$html = file_get_contents(__DIR__ . '/index.html');
$html_404 = file_get_contents(__DIR__ . '/main/404.html');

$header = <<<'PHP'
<?php
/**
 * RPS Digital World - Production Router & WordPress Bridge
 * Handles Homepage, 404 Routing, and WordPress API / Admin seamlessly.
 */

$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($request_uri, PHP_URL_PATH);

// Helper function to render the custom branded 404 page
function rps_render_404() {
    http_response_code(404);
    if ( file_exists(__DIR__ . '/main/404.html') ) {
        include __DIR__ . '/main/404.html';
        exit;
    }
?>
PHP;

$mid1 = <<<'PHP'
<?php
    exit;
}

// 1. Check if the request is trying to access the root domain (the home page)
if ( ($path === '/' || $path === '/index.php' || $path === '') && !isset($_GET['rest_route']) ) {
    // Output the static HTML landing page
?>
PHP;

$mid2 = <<<'PHP'
<?php
    exit;
}

// 2. Check if a request to /main/ was redirected here by Apache
// (If the physical file existed in /main/, Apache would have served it statically without hitting index.php.
// If it reached index.php with /main/, it is a 404 Not Found!)
if ( strpos($path, '/main/') === 0 ) {
    rps_render_404();
}

// 3. For ALL other requests (REST API, wp-admin, plugins, or unknown URLs):
// Load WordPress environment
if ( file_exists(__DIR__ . '/wp-load.php') ) {
    $wp_did_header = true;
    require_once __DIR__ . '/wp-load.php';
    wp();

    // If WordPress determined this is a 404 (no post/page/query found)
    if ( is_404() ) {
        rps_render_404();
    }

    // Otherwise, load WordPress template/theme/admin normally
    require_once ABSPATH . WPINC . '/template-loader.php';
    exit;
}

// Fallback if wp-load.php does not exist
rps_render_404();
PHP;

$final_index_php = $header . "\n" . $html_404 . "\n" . $mid1 . "\n" . $html . "\n" . $mid2 . "\n";

file_put_contents(__DIR__ . '/index.php', $final_index_php);
echo "New smart index.php generated successfully. Total bytes: " . strlen($final_index_php) . "\n";
