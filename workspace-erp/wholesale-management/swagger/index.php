<?php
if (!defined('ABSPATH')) { 
    define('ABSPATH', dirname(__DIR__, 4) . '/'); 
    require_once ABSPATH . 'wp-load.php'; 
}
$site_url = get_site_url();
$api_url  = $site_url . '/wp-json/wholesale/v1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wholesale ERP API Documentation</title>
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
        window.onload = () => {
            window.ui = SwaggerUIBundle({
                spec: {
                    "openapi": "3.0.0",
                    "info": {
                        "title": "Wholesale Distribution ERP API",
                        "version": "1.0.0",
                        "description": "Production-ready Wholesale Distribution ERP REST API — Dealers, Orders, Pricing, Inventory, Dispatch, Credit Limits, Payments, Billing, Reports, and Dealer Portal. JWT Authentication with role-based access."
                    },
                    "servers": [{ "url": "<?php echo esc_js($api_url); ?>" }],
                    "components": {
                        "securitySchemes": {
                            "bearerAuth": { 
                                "type": "http", 
                                "scheme": "bearer", 
                                "bearerFormat": "JWT", 
                                "description": "Enter your JWT token from /auth/login" 
                            }
                        },
                        "schemas": {
                            "SuccessResponse": {
                                "type": "object",
                                "properties": {
                                    "success": { "type": "boolean", "example": true },
                                    "message": { "type": "string" },
                                    "data": { "type": "object" }
                                }
                            },
                            "ErrorResponse": {
                                "type": "object",
                                "properties": {
                                    "success": { "type": "boolean", "example": false },
                                    "message": { "type": "string" },
                                    "data": { "type": "array", "items": {} }
                                }
                            },
                            "Dealer": {
                                "type": "object",
                                "properties": {
                                    "id": { "type": "integer" },
                                    "dealer_code": { "type": "string", "example": "DLR-00001" },
                                    "dealer_name": { "type": "string" },
                                    "owner_name": { "type": "string" },
                                    "mobile": { "type": "string" },
                                    "email": { "type": "string" },
                                    "gst_number": { "type": "string" },
                                    "address": { "type": "string" },
                                    "city": { "type": "string" },
                                    "state": { "type": "string" },
                                    "credit_limit": { "type": "number" },
                                    "available_credit": { "type": "number" },
                                    "status": { "type": "string", "example": "Active" }
                                }
                            },
                            "Product": {
                                "type": "object",
                                "properties": {
                                    "id": { "type": "integer" },
                                    "sku": { "type": "string" },
                                    "barcode": { "type": "string" },
                                    "product_name": { "type": "string" },
                                    "category": { "type": "string" },
                                    "brand": { "type": "string" },
                                    "purchase_price": { "type": "number" },
                                    "mrp": { "type": "number" },
                                    "selling_price": { "type": "number" },
                                    "gst_percentage": { "type": "number" }
                                }
                            }
                        }
                    },
                    "security": [{ "bearerAuth": [] }],
                    "tags": [
                        { "name": "Auth", "description": "User Authentication" },
                        { "name": "Dashboard", "description": "ERP Dashboard stats" },
                        { "name": "Dealers", "description": "Dealers CRUD" },
                        { "name": "Products", "description": "Products CRUD" },
                        { "name": "Orders", "description": "Orders CRUD" },
                        { "name": "Reports", "description": "Analytical reports" },
                        { "name": "Portal", "description": "Dealer self-service portal" },
                        { "name": "Media", "description": "Document Upload" }
                    ],
                    "paths": {
                        "/auth/login": {
                            "post": {
                                "tags": ["Auth"],
                                "summary": "Login to ERP",
                                "security": [],
                                "requestBody": {
                                    "required": true,
                                    "content": {
                                        "application/json": {
                                            "schema": {
                                                "type": "object",
                                                "required": ["username", "password"],
                                                "properties": {
                                                    "username": { "type": "string", "example": "wholesale_admin" },
                                                    "password": { "type": "string", "example": "admin123" }
                                                }
                                            }
                                        }
                                    }
                                },
                                "responses": {
                                    "200": { "description": "Login successful" }
                                }
                            }
                        },
                        "/auth/register": {
                            "post": {
                                "tags": ["Auth"],
                                "summary": "Register a new user",
                                "security": [],
                                "requestBody": {
                                    "required": true,
                                    "content": {
                                        "application/json": {
                                            "schema": {
                                                "type": "object",
                                                "required": ["username", "password", "email"],
                                                "properties": {
                                                    "username": { "type": "string" },
                                                    "password": { "type": "string" },
                                                    "email": { "type": "string" },
                                                    "role": { "type": "string", "default": "wholesale_dealer" }
                                                }
                                            }
                                        }
                                    }
                                },
                                "responses": {
                                    "200": { "description": "User registered" }
                                }
                            }
                        },
                        "/dashboard": {
                            "get": {
                                "tags": ["Dashboard"],
                                "summary": "Get dashboard metrics",
                                "responses": {
                                    "200": { "description": "Dashboard statistics summary" }
                                }
                            }
                        },
                        "/dealers": {
                            "get": {
                                "tags": ["Dealers"],
                                "summary": "List all dealers",
                                "responses": {
                                    "200": { "description": "Dealers list" }
                                }
                            },
                            "post": {
                                "tags": ["Dealers"],
                                "summary": "Create a dealer",
                                "requestBody": {
                                    "required": true,
                                    "content": {
                                        "application/json": {
                                            "schema": {
                                                "type": "object",
                                                "required": ["dealer_name"],
                                                "properties": {
                                                    "dealer_name": { "type": "string" },
                                                    "email": { "type": "string" },
                                                    "mobile": { "type": "string" }
                                                }
                                            }
                                        }
                                    }
                                },
                                "responses": {
                                    "200": { "description": "Dealer created" }
                                }
                            }
                        },
                        "/dealers/{id}": {
                            "get": {
                                "tags": ["Dealers"],
                                "summary": "Get dealer by ID",
                                "parameters": [
                                    { "name": "id", "in": "path", "required": true, "schema": { "type": "integer" } }
                                ],
                                "responses": {
                                    "200": { "description": "Dealer details" }
                                }
                            },
                            "put": {
                                "tags": ["Dealers"],
                                "summary": "Update dealer",
                                "parameters": [
                                    { "name": "id", "in": "path", "required": true, "schema": { "type": "integer" } }
                                ],
                                "responses": {
                                    "200": { "description": "Dealer updated" }
                                }
                            },
                            "delete": {
                                "tags": ["Dealers"],
                                "summary": "Delete dealer",
                                "parameters": [
                                    { "name": "id", "in": "path", "required": true, "schema": { "type": "integer" } }
                                ],
                                "responses": {
                                    "200": { "description": "Dealer deleted" }
                                }
                            }
                        },
                        "/products": {
                            "get": {
                                "tags": ["Products"],
                                "summary": "List all products",
                                "responses": {
                                    "200": { "description": "Products list" }
                                }
                            }
                        },
                        "/orders": {
                            "get": {
                                "tags": ["Orders"],
                                "summary": "List all orders",
                                "responses": {
                                    "200": { "description": "Orders list" }
                                }
                            },
                            "post": {
                                "tags": ["Orders"],
                                "summary": "Create a new order",
                                "responses": {
                                    "200": { "description": "Order created" }
                                }
                            }
                        },
                        "/reports/sales": {
                            "get": {
                                "tags": ["Reports"],
                                "summary": "Get sales performance report",
                                "responses": {
                                    "200": { "description": "Sales analysis" }
                                }
                            }
                        },
                        "/portal/dashboard": {
                            "get": {
                                "tags": ["Portal"],
                                "summary": "Get dealer portal dashboard data",
                                "responses": {
                                    "200": { "description": "Dealer dashboard info" }
                                }
                            }
                        },
                        "/media/upload": {
                            "post": {
                                "tags": ["Media"],
                                "summary": "Upload a document to Media Library",
                                "requestBody": {
                                    "content": {
                                        "multipart/form-data": {
                                            "schema": {
                                                "type": "object",
                                                "properties": {
                                                    "file": { "type": "string", "format": "binary" }
                                                }
                                            }
                                        }
                                    }
                                },
                                "responses": {
                                    "200": { "description": "File uploaded successfully" }
                                }
                            }
                        }
                    }
                },
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIBundle.presets.html
                ],
                layout: "BaseLayout"
            });
        };
    </script>
</body>
</html>
