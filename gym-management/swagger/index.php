<?php
if (!defined('ABSPATH')) { define('ABSPATH', dirname(__DIR__, 4) . '/'); require_once ABSPATH . 'wp-load.php'; }
$site_url = get_site_url();
$api_url  = $site_url . '/wp-json/gym/v1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gym ERP API Documentation</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui.css" />
    <style>body { margin: 0; padding: 0; background-color: #f8fafc; } .swagger-ui .topbar { display: none; }</style>
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui-bundle.js"></script>
    <script>
        window.onload = () => {
            window.ui = SwaggerUIBundle({
                spec: {
                    "openapi": "3.0.0",
                    "info": { "title": "Gym ERP API", "version": "1.0.0" },
                    "servers": [ { "url": "<?php echo esc_js($api_url); ?>" } ],
                    "components": { "securitySchemes": { "bearerAuth": { "type": "http", "scheme": "bearer", "bearerFormat": "JWT" } } },
                    "security": [ { "bearerAuth": [] } ],
                    "paths": {
                        "/auth/login": { "post": { "summary": "Login to Gym ERP", "responses": { "200": { "description": "Success" } } } },
                        "/dashboard/stats": { "get": { "summary": "Get dashboard stats", "responses": { "200": { "description": "Success" } } } },
                        "/members": { "get": { "summary": "List members" }, "post": { "summary": "Create member" } }
                    }
                },
                dom_id: '#swagger-ui', deepLinking: true, presets: [ SwaggerUIBundle.presets.apis, SwaggerUIBundle.SwaggerUIStandalonePreset ], layout: "BaseLayout"
            });
        };
    </script>
</body>
</html>
