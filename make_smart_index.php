<?php
$html = file_get_contents('index.html');
$router = <<<PHP
<?php
// Smart Router for RPS Digital World
\$request_uri = \$_SERVER['REQUEST_URI'];
\$path = parse_url(\$request_uri, PHP_URL_PATH);

// Check if the request is trying to access the root domain (the home page)
if ( \$path === '/' || \$path === '/index.php' || \$path === '' ) {
    
    // Check if it's NOT a WordPress API call
    if ( !isset(\$_GET['rest_route']) ) {
        // Output the static HTML landing page
?>
PHP;

$footer = <<<PHP
<?php
        exit;
    }
}

// For ALL other requests (REST API, wp-admin, plugins), load WordPress normally
define( 'WP_USE_THEMES', true );
require __DIR__ . '/wp-blog-header.php';
PHP;

file_put_contents('index.php', $router . "\n" . $html . "\n" . $footer);
echo "Smart index.php created successfully.";
