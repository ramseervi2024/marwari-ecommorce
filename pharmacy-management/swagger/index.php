<?php
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__DIR__, 4) . '/');
    require_once ABSPATH . 'wp-load.php';
}
$site_url = get_site_url();
$api_url  = $site_url . '/wp-json/pharmacy/v1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pharmacy ERP API Documentation</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui.css" />
    <style>
        body { margin: 0; padding: 0; background-color: #f8fafc; }
        .swagger-ui .topbar { display: none; }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui-bundle.js"></script>
    <script>
        const spec = {
            "openapi": "3.0.0",
            "info": {
                "title": "Pharmacy ERP API",
                "version": "1.0.0",
                "description": "API documentation for the Pharmacy ERP system."
            },
            "servers": [
                { "url": "<?php echo esc_js($api_url); ?>" }
            ],
            "components": {
                "securitySchemes": {
                    "bearerAuth": {
                        "type": "http",
                        "scheme": "bearer",
                        "bearerFormat": "JWT"
                    }
                }
            },
            "security": [
                { "bearerAuth": [] }
            ],
            "paths": {
                "/auth/login": {
                    "post": {
                        "summary": "Login to Pharmacy ERP",
                        "security": [],
                        "requestBody": {
                            "content": {
                                "application/json": {
                                    "schema": {
                                        "type": "object",
                                        "properties": {
                                            "username": { "type": "string" },
                                            "password": { "type": "string" }
                                        }
                                    }
                                }
                            }
                        },
                        "responses": {
                            "200": { "description": "Successful login" }
                        }
                    }
                },
                "/dashboard/stats": {
                    "get": {
                        "summary": "Get dashboard statistics",
                        "responses": {
                            "200": { "description": "Stats retrieved successfully" }
                        }
                    }
                },
                "/medicines": {
                    "get": {
                        "summary": "List medicines",
                        "responses": { "200": { "description": "Successful" } }
                    },
                    "post": {
                        "summary": "Create medicine",
                        "responses": { "200": { "description": "Successful" } }
                    }
                },
                "/bills": {
                    "get": {
                        "summary": "List bills",
                        "responses": { "200": { "description": "Successful" } }
                    },
                    "post": {
                        "summary": "Create bill",
                        "responses": { "200": { "description": "Successful" } }
                    }
                }
            }
        };

        window.onload = () => {
            window.ui = SwaggerUIBundle({
                spec: spec,
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [ SwaggerUIBundle.presets.apis, SwaggerUIBundle.SwaggerUIStandalonePreset ],
                layout: "BaseLayout"
            });
        };
    </script>
</body>
</html>
