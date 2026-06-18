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
                    "info": {
                        "title": "Gym ERP API",
                        "version": "1.0.0",
                        "description": "Complete Gym Management System REST API — Members, Trainers, Plans, Memberships, Payments, Attendance, Diet Plans, Workout Plans, Equipment. JWT Authentication with role-based access."
                    },
                    "servers": [{ "url": "<?php echo esc_js($api_url); ?>" }],
                    "components": {
                        "securitySchemes": {
                            "bearerAuth": { "type": "http", "scheme": "bearer", "bearerFormat": "JWT", "description": "Enter your JWT token from /auth/login" }
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
                            "PaginatedResponse": {
                                "type": "object",
                                "properties": {
                                    "data": { "type": "array", "items": {} },
                                    "total": { "type": "integer" },
                                    "page": { "type": "integer" },
                                    "limit": { "type": "integer" },
                                    "pages": { "type": "integer" }
                                }
                            },
                            "Member": {
                                "type": "object",
                                "properties": {
                                    "id": { "type": "integer" },
                                    "member_id": { "type": "string", "example": "MEM-00001" },
                                    "name": { "type": "string" },
                                    "mobile": { "type": "string" },
                                    "email": { "type": "string" },
                                    "dob": { "type": "string", "format": "date" },
                                    "gender": { "type": "string" },
                                    "blood_group": { "type": "string" },
                                    "address": { "type": "string" },
                                    "emergency_contact_name": { "type": "string" },
                                    "emergency_contact_number": { "type": "string" },
                                    "join_date": { "type": "string", "format": "date" },
                                    "height_cm": { "type": "number" },
                                    "weight_kg": { "type": "number" },
                                    "medical_history": { "type": "string" },
                                    "status": { "type": "string", "example": "Active" },
                                    "created_at": { "type": "string" },
                                    "deleted_at": { "type": "string", "nullable": true }
                                }
                            },
                            "Trainer": {
                                "type": "object",
                                "properties": {
                                    "id": { "type": "integer" },
                                    "name": { "type": "string" },
                                    "mobile": { "type": "string" },
                                    "email": { "type": "string" },
                                    "specialization": { "type": "string" },
                                    "salary": { "type": "number" },
                                    "join_date": { "type": "string", "format": "date" },
                                    "status": { "type": "string" }
                                }
                            },
                            "Plan": {
                                "type": "object",
                                "properties": {
                                    "id": { "type": "integer" },
                                    "name": { "type": "string" },
                                    "duration_days": { "type": "integer" },
                                    "price": { "type": "number" },
                                    "description": { "type": "string" },
                                    "is_active": { "type": "integer" }
                                }
                            },
                            "Membership": {
                                "type": "object",
                                "properties": {
                                    "id": { "type": "integer" },
                                    "member_id": { "type": "integer" },
                                    "plan_id": { "type": "integer" },
                                    "trainer_id": { "type": "integer", "nullable": true },
                                    "start_date": { "type": "string", "format": "date" },
                                    "end_date": { "type": "string", "format": "date" },
                                    "status": { "type": "string" },
                                    "member_name": { "type": "string" },
                                    "member_code": { "type": "string" },
                                    "plan_name": { "type": "string" }
                                }
                            },
                            "Payment": {
                                "type": "object",
                                "properties": {
                                    "id": { "type": "integer" },
                                    "invoice_number": { "type": "string" },
                                    "member_id": { "type": "integer" },
                                    "membership_id": { "type": "integer", "nullable": true },
                                    "amount": { "type": "number" },
                                    "payment_date": { "type": "string", "format": "date" },
                                    "payment_mode": { "type": "string" },
                                    "status": { "type": "string" },
                                    "notes": { "type": "string", "nullable": true }
                                }
                            },
                            "DietPlan": {
                                "type": "object",
                                "properties": {
                                    "id": { "type": "integer" },
                                    "member_id": { "type": "integer" },
                                    "trainer_id": { "type": "integer", "nullable": true },
                                    "plan_details": { "type": "string" },
                                    "assigned_date": { "type": "string", "format": "date" },
                                    "notes": { "type": "string", "nullable": true }
                                }
                            },
                            "Attendance": {
                                "type": "object",
                                "properties": {
                                    "id": { "type": "integer" },
                                    "user_type": { "type": "string", "enum": ["Member", "Trainer"] },
                                    "reference_id": { "type": "integer" },
                                    "check_in": { "type": "string", "format": "date-time" },
                                    "check_out": { "type": "string", "format": "date-time", "nullable": true }
                                }
                            },
                            "WorkoutPlan": {
                                "type": "object",
                                "properties": {
                                    "id": { "type": "integer" },
                                    "member_id": { "type": "integer" },
                                    "trainer_id": { "type": "integer", "nullable": true },
                                    "title": { "type": "string" },
                                    "goal": { "type": "string" },
                                    "level": { "type": "string" },
                                    "monday": { "type": "string" },
                                    "tuesday": { "type": "string" },
                                    "wednesday": { "type": "string" },
                                    "thursday": { "type": "string" },
                                    "friday": { "type": "string" },
                                    "saturday": { "type": "string" },
                                    "sunday": { "type": "string" },
                                    "notes": { "type": "string" },
                                    "start_date": { "type": "string", "format": "date" },
                                    "end_date": { "type": "string", "format": "date" },
                                    "status": { "type": "string" }
                                }
                            },
                            "Equipment": {
                                "type": "object",
                                "properties": {
                                    "id": { "type": "integer" },
                                    "name": { "type": "string" },
                                    "category": { "type": "string" },
                                    "brand": { "type": "string" },
                                    "model_number": { "type": "string" },
                                    "serial_number": { "type": "string" },
                                    "purchase_date": { "type": "string", "format": "date" },
                                    "purchase_price": { "type": "number" },
                                    "warranty_expiry": { "type": "string", "format": "date" },
                                    "location": { "type": "string" },
                                    "condition_status": { "type": "string" },
                                    "last_maintenance_date": { "type": "string", "format": "date" },
                                    "next_maintenance_date": { "type": "string", "format": "date" },
                                    "maintenance_notes": { "type": "string" },
                                    "status": { "type": "string" }
                                }
                            }
                        }
                    },
                    "security": [{ "bearerAuth": [] }],
                    "tags": [
                        { "name": "Auth", "description": "Authentication — Login, Profile, Logout" },
                        { "name": "Dashboard", "description": "Dashboard statistics and analytics" },
                        { "name": "Members", "description": "Gym member management (CRUD)" },
                        { "name": "Trainers", "description": "Trainer management" },
                        { "name": "Plans", "description": "Membership plans / packages" },
                        { "name": "Memberships", "description": "Assign plans to members, track active/expiring" },
                        { "name": "Payments", "description": "Payment recording and invoice management" },
                        { "name": "Diet Plans", "description": "Diet plan assignment for members" },
                        { "name": "Attendance", "description": "Check-in / Check-out tracking" },
                        { "name": "Workout Plans", "description": "Weekly workout plan management (CRUD)" },
                        { "name": "Equipment", "description": "Equipment inventory and maintenance tracking" }
                    ],
                    "paths": {

                        "/auth/login": {
                            "post": {
                                "tags": ["Auth"],
                                "summary": "Login to Gym ERP",
                                "description": "Authenticate with username and password to receive a JWT token (valid for 7 days).",
                                "security": [],
                                "requestBody": {
                                    "required": true,
                                    "content": {
                                        "application/json": {
                                            "schema": {
                                                "type": "object",
                                                "required": ["username", "password"],
                                                "properties": {
                                                    "username": { "type": "string", "example": "gymadmin" },
                                                    "password": { "type": "string", "example": "123456" }
                                                }
                                            }
                                        }
                                    }
                                },
                                "responses": {
                                    "200": { "description": "Login successful — returns JWT token and user details" },
                                    "401": { "description": "Invalid credentials" },
                                    "403": { "description": "Account pending or blocked" }
                                }
                            }
                        },

                        "/auth/me": {
                            "get": {
                                "tags": ["Auth"],
                                "summary": "Get current user profile",
                                "description": "Returns the authenticated user's ID, username, display name, and role.",
                                "responses": {
                                    "200": { "description": "User profile" },
                                    "401": { "description": "Invalid or expired token" }
                                }
                            }
                        },

                        "/auth/logout": {
                            "post": {
                                "tags": ["Auth"],
                                "summary": "Logout",
                                "description": "Logs the logout action in activity logs. Note: Token is not invalidated server-side — delete it client-side.",
                                "responses": {
                                    "200": { "description": "Logged out successfully" }
                                }
                            }
                        },

                        "/dashboard/stats": {
                            "get": {
                                "tags": ["Dashboard"],
                                "summary": "Get dashboard statistics",
                                "description": "Returns summary stats (total members, trainers, today's attendance, today's revenue), expiring memberships (next 7 days), and recent 5 payments.",
                                "responses": {
                                    "200": { "description": "Dashboard data with summary, expiring_soon, and recent_payments" }
                                }
                            }
                        },

                        "/members": {
                            "get": {
                                "tags": ["Members"],
                                "summary": "List all members",
                                "description": "Paginated, searchable list of gym members. Search across member_id, name, mobile.",
                                "parameters": [
                                    { "name": "page", "in": "query", "schema": { "type": "integer", "default": 1 }, "description": "Page number" },
                                    { "name": "limit", "in": "query", "schema": { "type": "integer", "default": 10 }, "description": "Items per page" },
                                    { "name": "search", "in": "query", "schema": { "type": "string" }, "description": "Search by member_id, name, or mobile" },
                                    { "name": "orderby", "in": "query", "schema": { "type": "string", "enum": ["id", "name", "member_id"] }, "description": "Sort column" },
                                    { "name": "order", "in": "query", "schema": { "type": "string", "enum": ["ASC", "DESC"], "default": "DESC" }, "description": "Sort direction" }
                                ],
                                "responses": {
                                    "200": { "description": "Paginated member list with data, total, page, limit, pages" }
                                }
                            },
                            "post": {
                                "tags": ["Members"],
                                "summary": "Create a new member",
                                "description": "Creates a new gym member. Auto-generates a unique member_id (e.g., MEM-00001).",
                                "requestBody": {
                                    "required": true,
                                    "content": {
                                        "application/json": {
                                            "schema": {
                                                "type": "object",
                                                "required": ["name", "mobile"],
                                                "properties": {
                                                    "name": { "type": "string", "example": "Rahul Sharma" },
                                                    "mobile": { "type": "string", "example": "9876543210" },
                                                    "email": { "type": "string", "example": "rahul@email.com" },
                                                    "gender": { "type": "string", "example": "Male", "enum": ["Male", "Female", "Other"] },
                                                    "dob": { "type": "string", "format": "date", "example": "1995-08-15" },
                                                    "address": { "type": "string", "example": "123 Main Street, Jaipur" },
                                                    "height_cm": { "type": "number", "example": 175.5 },
                                                    "weight_kg": { "type": "number", "example": 78.0 },
                                                    "medical_history": { "type": "string", "example": "None" }
                                                }
                                            }
                                        }
                                    }
                                },
                                "responses": {
                                    "200": { "description": "Member created — returns id and member_id" },
                                    "400": { "description": "Name and Mobile required" }
                                }
                            }
                        },

                        "/members/{id}": {
                            "get": {
                                "tags": ["Members"],
                                "summary": "Get a single member",
                                "description": "Returns full details of a specific member by ID.",
                                "parameters": [
                                    { "name": "id", "in": "path", "required": true, "schema": { "type": "integer" }, "description": "Member ID" }
                                ],
                                "responses": {
                                    "200": { "description": "Member details" },
                                    "404": { "description": "Member not found" }
                                }
                            },
                            "put": {
                                "tags": ["Members"],
                                "summary": "Update a member",
                                "description": "Update member details. Send only the fields you want to change.",
                                "parameters": [
                                    { "name": "id", "in": "path", "required": true, "schema": { "type": "integer" }, "description": "Member ID" }
                                ],
                                "requestBody": {
                                    "required": true,
                                    "content": {
                                        "application/json": {
                                            "schema": {
                                                "type": "object",
                                                "properties": {
                                                    "name": { "type": "string" },
                                                    "mobile": { "type": "string" },
                                                    "email": { "type": "string" },
                                                    "gender": { "type": "string" },
                                                    "dob": { "type": "string", "format": "date" },
                                                    "address": { "type": "string" },
                                                    "height_cm": { "type": "number" },
                                                    "weight_kg": { "type": "number" },
                                                    "medical_history": { "type": "string" }
                                                }
                                            }
                                        }
                                    }
                                },
                                "responses": {
                                    "200": { "description": "Member updated" },
                                    "400": { "description": "Update failed" }
                                }
                            },
                            "delete": {
                                "tags": ["Members"],
                                "summary": "Delete a member (soft delete)",
                                "description": "Soft deletes a member by setting deleted_at timestamp.",
                                "parameters": [
                                    { "name": "id", "in": "path", "required": true, "schema": { "type": "integer" }, "description": "Member ID" }
                                ],
                                "responses": {
                                    "200": { "description": "Member deleted" },
                                    "400": { "description": "Delete failed" }
                                }
                            }
                        },

                        "/trainers": {
                            "get": {
                                "tags": ["Trainers"],
                                "summary": "List all trainers",
                                "description": "Paginated, searchable list. Search by name and specialization.",
                                "parameters": [
                                    { "name": "page", "in": "query", "schema": { "type": "integer", "default": 1 } },
                                    { "name": "limit", "in": "query", "schema": { "type": "integer", "default": 10 } },
                                    { "name": "search", "in": "query", "schema": { "type": "string" }, "description": "Search by name or specialization" },
                                    { "name": "orderby", "in": "query", "schema": { "type": "string", "enum": ["id", "name"] } },
                                    { "name": "order", "in": "query", "schema": { "type": "string", "enum": ["ASC", "DESC"] } }
                                ],
                                "responses": { "200": { "description": "Paginated trainer list" } }
                            },
                            "post": {
                                "tags": ["Trainers"],
                                "summary": "Create a new trainer",
                                "description": "Creates a trainer profile. join_date is auto-set to today.",
                                "requestBody": {
                                    "required": true,
                                    "content": {
                                        "application/json": {
                                            "schema": {
                                                "type": "object",
                                                "required": ["name"],
                                                "properties": {
                                                    "name": { "type": "string", "example": "Vikram Singh" },
                                                    "mobile": { "type": "string", "example": "9998887776" },
                                                    "email": { "type": "string", "example": "vikram@gym.local" },
                                                    "specialization": { "type": "string", "example": "Weight Training" },
                                                    "salary": { "type": "number", "example": 25000 }
                                                }
                                            }
                                        }
                                    }
                                },
                                "responses": {
                                    "200": { "description": "Trainer created — returns id" },
                                    "400": { "description": "Name required" }
                                }
                            }
                        },

                        "/trainers/{id}": {
                            "delete": {
                                "tags": ["Trainers"],
                                "summary": "Delete a trainer (soft delete)",
                                "parameters": [
                                    { "name": "id", "in": "path", "required": true, "schema": { "type": "integer" } }
                                ],
                                "responses": { "200": { "description": "Trainer deleted" } }
                            }
                        },

                        "/plans": {
                            "get": {
                                "tags": ["Plans"],
                                "summary": "List all membership plans",
                                "description": "Paginated list of gym plans. Search by name. Default plans: 1 Month (₹1000), 3 Months (₹2500), 6 Months (₹4500), 1 Year (₹8000).",
                                "parameters": [
                                    { "name": "page", "in": "query", "schema": { "type": "integer", "default": 1 } },
                                    { "name": "limit", "in": "query", "schema": { "type": "integer", "default": 10 } },
                                    { "name": "search", "in": "query", "schema": { "type": "string" }, "description": "Search by plan name" }
                                ],
                                "responses": { "200": { "description": "Paginated plan list" } }
                            },
                            "post": {
                                "tags": ["Plans"],
                                "summary": "Create a new plan",
                                "requestBody": {
                                    "required": true,
                                    "content": {
                                        "application/json": {
                                            "schema": {
                                                "type": "object",
                                                "required": ["name", "duration_days"],
                                                "properties": {
                                                    "name": { "type": "string", "example": "2 Months Bi-Monthly Plan" },
                                                    "duration_days": { "type": "integer", "example": 60 },
                                                    "price": { "type": "number", "example": 1800 },
                                                    "description": { "type": "string", "example": "Two-month membership with full gym access" }
                                                }
                                            }
                                        }
                                    }
                                },
                                "responses": {
                                    "200": { "description": "Plan created — returns id" },
                                    "400": { "description": "Name and Duration required" }
                                }
                            }
                        },

                        "/memberships": {
                            "get": {
                                "tags": ["Memberships"],
                                "summary": "List active memberships",
                                "description": "Returns all active memberships (auto-expires old ones first). Includes member_name, member_code, plan_name. Returns flat array (not paginated).",
                                "responses": { "200": { "description": "Array of active memberships with joined member and plan names" } }
                            },
                            "post": {
                                "tags": ["Memberships"],
                                "summary": "Assign plan to member",
                                "description": "Assigns a membership plan to a member. If amount_paid is provided, automatically creates a payment record with auto-generated invoice number.",
                                "requestBody": {
                                    "required": true,
                                    "content": {
                                        "application/json": {
                                            "schema": {
                                                "type": "object",
                                                "required": ["member_id", "plan_id", "start_date", "end_date"],
                                                "properties": {
                                                    "member_id": { "type": "integer", "example": 12, "description": "Member's internal id" },
                                                    "plan_id": { "type": "integer", "example": 2, "description": "Plan's id" },
                                                    "trainer_id": { "type": "integer", "example": 3, "description": "Optional trainer assignment" },
                                                    "start_date": { "type": "string", "format": "date", "example": "2026-06-18" },
                                                    "end_date": { "type": "string", "format": "date", "example": "2026-09-18" },
                                                    "amount_paid": { "type": "number", "example": 2500, "description": "If provided, auto-creates a payment record" },
                                                    "payment_mode": { "type": "string", "example": "UPI", "enum": ["Cash", "UPI", "Card", "Online"], "description": "Default: Cash" }
                                                }
                                            }
                                        }
                                    }
                                },
                                "responses": {
                                    "200": { "description": "Membership assigned — returns id" },
                                    "400": { "description": "Missing required fields" }
                                }
                            }
                        },

                        "/memberships/expiring": {
                            "get": {
                                "tags": ["Memberships"],
                                "summary": "Get expiring memberships (next 7 days)",
                                "description": "Returns active memberships expiring within the next 7 days. Includes member mobile number for contact.",
                                "responses": { "200": { "description": "Array of expiring memberships with member_name, member_code, plan_name, mobile" } }
                            }
                        },

                        "/payments": {
                            "get": {
                                "tags": ["Payments"],
                                "summary": "List all payments",
                                "description": "Paginated list of payment records. Search by invoice_number. Sort by invoice_number or payment_date.",
                                "parameters": [
                                    { "name": "page", "in": "query", "schema": { "type": "integer", "default": 1 } },
                                    { "name": "limit", "in": "query", "schema": { "type": "integer", "default": 10 } },
                                    { "name": "search", "in": "query", "schema": { "type": "string" }, "description": "Search by invoice_number" },
                                    { "name": "orderby", "in": "query", "schema": { "type": "string", "enum": ["id", "invoice_number", "payment_date"] } },
                                    { "name": "order", "in": "query", "schema": { "type": "string", "enum": ["ASC", "DESC"] } }
                                ],
                                "responses": { "200": { "description": "Paginated payment list" } }
                            },
                            "post": {
                                "tags": ["Payments"],
                                "summary": "Record a payment",
                                "description": "Records a standalone payment. Invoice number is auto-generated (INV-{UNIQID}).",
                                "requestBody": {
                                    "required": true,
                                    "content": {
                                        "application/json": {
                                            "schema": {
                                                "type": "object",
                                                "required": ["member_id", "amount"],
                                                "properties": {
                                                    "member_id": { "type": "integer", "example": 12 },
                                                    "amount": { "type": "number", "example": 2500 },
                                                    "membership_id": { "type": "integer", "example": 45, "description": "Optional related membership" },
                                                    "payment_date": { "type": "string", "format": "date", "example": "2026-06-18", "description": "Default: today" },
                                                    "payment_mode": { "type": "string", "example": "UPI", "enum": ["Cash", "UPI", "Card", "Online"], "description": "Default: Cash" }
                                                }
                                            }
                                        }
                                    }
                                },
                                "responses": {
                                    "200": { "description": "Payment recorded — returns id" },
                                    "400": { "description": "Member and Amount required" }
                                }
                            }
                        },

                        "/diet-plans": {
                            "get": {
                                "tags": ["Diet Plans"],
                                "summary": "List all diet plans",
                                "description": "Paginated list of assigned diet plans.",
                                "parameters": [
                                    { "name": "page", "in": "query", "schema": { "type": "integer", "default": 1 } },
                                    { "name": "limit", "in": "query", "schema": { "type": "integer", "default": 10 } }
                                ],
                                "responses": { "200": { "description": "Paginated diet plan list" } }
                            },
                            "post": {
                                "tags": ["Diet Plans"],
                                "summary": "Assign diet plan to member",
                                "description": "Assigns a text-based diet plan to a member. assigned_date is auto-set to today.",
                                "requestBody": {
                                    "required": true,
                                    "content": {
                                        "application/json": {
                                            "schema": {
                                                "type": "object",
                                                "required": ["member_id", "plan_details"],
                                                "properties": {
                                                    "member_id": { "type": "integer", "example": 12 },
                                                    "plan_details": { "type": "string", "example": "Breakfast: Oats + Banana\nLunch: Rice + Dal + Salad\nDinner: Grilled Chicken + Vegetables" },
                                                    "trainer_id": { "type": "integer", "example": 3, "description": "Optional trainer who created the plan" }
                                                }
                                            }
                                        }
                                    }
                                },
                                "responses": {
                                    "200": { "description": "Diet plan assigned — returns id" },
                                    "400": { "description": "Member and Plan Details required" }
                                }
                            }
                        },

                        "/attendance": {
                            "get": {
                                "tags": ["Attendance"],
                                "summary": "List attendance records",
                                "description": "Paginated attendance list. Search by user_type (Member or Trainer).",
                                "parameters": [
                                    { "name": "page", "in": "query", "schema": { "type": "integer", "default": 1 } },
                                    { "name": "limit", "in": "query", "schema": { "type": "integer", "default": 10 } },
                                    { "name": "search", "in": "query", "schema": { "type": "string", "enum": ["Member", "Trainer"] }, "description": "Filter by user type" }
                                ],
                                "responses": { "200": { "description": "Paginated attendance list" } }
                            },
                            "post": {
                                "tags": ["Attendance"],
                                "summary": "Mark check-in / check-out",
                                "description": "Smart toggle: 1st call = Check In, 2nd call = Check Out, 3rd call = Error (already checked out today).",
                                "requestBody": {
                                    "required": true,
                                    "content": {
                                        "application/json": {
                                            "schema": {
                                                "type": "object",
                                                "required": ["user_type", "reference_id"],
                                                "properties": {
                                                    "user_type": { "type": "string", "enum": ["Member", "Trainer"], "example": "Member" },
                                                    "reference_id": { "type": "integer", "example": 12, "description": "member.id or trainer.id" }
                                                }
                                            }
                                        }
                                    }
                                },
                                "responses": {
                                    "200": { "description": "Checked in or Checked out successfully" },
                                    "400": { "description": "Already checked out today / User Type and ID required" }
                                }
                            }
                        },

                        "/workout-plans": {
                            "get": {
                                "tags": ["Workout Plans"],
                                "summary": "List all workout plans",
                                "description": "Paginated list with member_name and trainer_name joined.",
                                "parameters": [
                                    { "name": "page", "in": "query", "schema": { "type": "integer", "default": 1 } },
                                    { "name": "limit", "in": "query", "schema": { "type": "integer", "default": 10 } }
                                ],
                                "responses": { "200": { "description": "Paginated workout plan list" } }
                            },
                            "post": {
                                "tags": ["Workout Plans"],
                                "summary": "Create a workout plan",
                                "description": "Creates a day-by-day weekly workout plan for a member.",
                                "requestBody": {
                                    "required": true,
                                    "content": {
                                        "application/json": {
                                            "schema": {
                                                "type": "object",
                                                "required": ["member_id", "title"],
                                                "properties": {
                                                    "member_id": { "type": "integer", "example": 12 },
                                                    "trainer_id": { "type": "integer", "example": 3 },
                                                    "title": { "type": "string", "example": "Beginner Muscle Building" },
                                                    "goal": { "type": "string", "example": "Muscle Gain", "description": "Default: General Fitness" },
                                                    "level": { "type": "string", "example": "Beginner", "enum": ["Beginner", "Intermediate", "Advanced"] },
                                                    "monday": { "type": "string", "example": "Chest: Bench Press 4x12" },
                                                    "tuesday": { "type": "string", "example": "Back: Deadlift 4x8" },
                                                    "wednesday": { "type": "string", "example": "Rest Day" },
                                                    "thursday": { "type": "string", "example": "Shoulders: OHP 4x10" },
                                                    "friday": { "type": "string", "example": "Legs: Squat 4x10" },
                                                    "saturday": { "type": "string", "example": "Arms: Bicep Curl 3x12" },
                                                    "sunday": { "type": "string", "example": "Rest Day" },
                                                    "notes": { "type": "string", "example": "Increase weight weekly" },
                                                    "start_date": { "type": "string", "format": "date", "example": "2026-06-18" },
                                                    "end_date": { "type": "string", "format": "date", "example": "2026-09-18" }
                                                }
                                            }
                                        }
                                    }
                                },
                                "responses": {
                                    "201": { "description": "Workout plan created — returns id" },
                                    "400": { "description": "Member and Title required" }
                                }
                            }
                        },

                        "/workout-plans/{id}": {
                            "get": {
                                "tags": ["Workout Plans"],
                                "summary": "Get a single workout plan",
                                "parameters": [
                                    { "name": "id", "in": "path", "required": true, "schema": { "type": "integer" } }
                                ],
                                "responses": {
                                    "200": { "description": "Workout plan details" },
                                    "404": { "description": "Not found" }
                                }
                            },
                            "put": {
                                "tags": ["Workout Plans"],
                                "summary": "Update a workout plan",
                                "description": "Send only the fields you want to change.",
                                "parameters": [
                                    { "name": "id", "in": "path", "required": true, "schema": { "type": "integer" } }
                                ],
                                "requestBody": {
                                    "required": true,
                                    "content": {
                                        "application/json": {
                                            "schema": {
                                                "type": "object",
                                                "properties": {
                                                    "title": { "type": "string" },
                                                    "goal": { "type": "string" },
                                                    "level": { "type": "string" },
                                                    "monday": { "type": "string" },
                                                    "tuesday": { "type": "string" },
                                                    "wednesday": { "type": "string" },
                                                    "thursday": { "type": "string" },
                                                    "friday": { "type": "string" },
                                                    "saturday": { "type": "string" },
                                                    "sunday": { "type": "string" },
                                                    "notes": { "type": "string" },
                                                    "start_date": { "type": "string", "format": "date" },
                                                    "end_date": { "type": "string", "format": "date" },
                                                    "status": { "type": "string", "enum": ["Active", "Inactive"] },
                                                    "member_id": { "type": "integer" },
                                                    "trainer_id": { "type": "integer" }
                                                }
                                            }
                                        }
                                    }
                                },
                                "responses": {
                                    "200": { "description": "Updated" },
                                    "404": { "description": "Not found" }
                                }
                            },
                            "delete": {
                                "tags": ["Workout Plans"],
                                "summary": "Delete a workout plan (soft delete)",
                                "parameters": [
                                    { "name": "id", "in": "path", "required": true, "schema": { "type": "integer" } }
                                ],
                                "responses": { "200": { "description": "Deleted" } }
                            }
                        },

                        "/equipment": {
                            "get": {
                                "tags": ["Equipment"],
                                "summary": "List all equipment",
                                "description": "Paginated, searchable list. Search by name, category, brand, location.",
                                "parameters": [
                                    { "name": "page", "in": "query", "schema": { "type": "integer", "default": 1 } },
                                    { "name": "limit", "in": "query", "schema": { "type": "integer", "default": 10 } },
                                    { "name": "search", "in": "query", "schema": { "type": "string" }, "description": "Search by name, category, brand, or location" }
                                ],
                                "responses": { "200": { "description": "Paginated equipment list" } }
                            },
                            "post": {
                                "tags": ["Equipment"],
                                "summary": "Add new equipment",
                                "requestBody": {
                                    "required": true,
                                    "content": {
                                        "application/json": {
                                            "schema": {
                                                "type": "object",
                                                "required": ["name"],
                                                "properties": {
                                                    "name": { "type": "string", "example": "Commercial Treadmill" },
                                                    "category": { "type": "string", "example": "Cardio", "description": "Default: General" },
                                                    "brand": { "type": "string", "example": "Life Fitness" },
                                                    "model_number": { "type": "string", "example": "T5-GO" },
                                                    "serial_number": { "type": "string", "example": "LF-2024-001" },
                                                    "purchase_date": { "type": "string", "format": "date", "example": "2025-01-15" },
                                                    "purchase_price": { "type": "number", "example": 250000 },
                                                    "warranty_expiry": { "type": "string", "format": "date", "example": "2028-01-15" },
                                                    "location": { "type": "string", "example": "Cardio Zone" },
                                                    "condition_status": { "type": "string", "example": "Good", "enum": ["Good", "Fair", "Poor", "Out of Service"] },
                                                    "maintenance_notes": { "type": "string", "example": "Regular belt check" }
                                                }
                                            }
                                        }
                                    }
                                },
                                "responses": {
                                    "201": { "description": "Equipment added — returns id" },
                                    "400": { "description": "Equipment name is required" }
                                }
                            }
                        },

                        "/equipment/summary": {
                            "get": {
                                "tags": ["Equipment"],
                                "summary": "Get equipment summary / stats",
                                "description": "Returns total count, breakdown by status and condition, total asset value, and maintenance due count.",
                                "responses": { "200": { "description": "Equipment summary with total_equipment, by_status, by_condition, total_value, maintenance_due" } }
                            }
                        },

                        "/equipment/{id}": {
                            "get": {
                                "tags": ["Equipment"],
                                "summary": "Get single equipment details",
                                "parameters": [
                                    { "name": "id", "in": "path", "required": true, "schema": { "type": "integer" } }
                                ],
                                "responses": {
                                    "200": { "description": "Equipment details" },
                                    "404": { "description": "Not found" }
                                }
                            },
                            "put": {
                                "tags": ["Equipment"],
                                "summary": "Update equipment",
                                "description": "Send only changed fields.",
                                "parameters": [
                                    { "name": "id", "in": "path", "required": true, "schema": { "type": "integer" } }
                                ],
                                "requestBody": {
                                    "required": true,
                                    "content": {
                                        "application/json": {
                                            "schema": {
                                                "type": "object",
                                                "properties": {
                                                    "name": { "type": "string" },
                                                    "category": { "type": "string" },
                                                    "brand": { "type": "string" },
                                                    "model_number": { "type": "string" },
                                                    "serial_number": { "type": "string" },
                                                    "purchase_date": { "type": "string", "format": "date" },
                                                    "purchase_price": { "type": "number" },
                                                    "warranty_expiry": { "type": "string", "format": "date" },
                                                    "location": { "type": "string" },
                                                    "condition_status": { "type": "string" },
                                                    "maintenance_notes": { "type": "string" },
                                                    "status": { "type": "string", "enum": ["Active", "Retired"] }
                                                }
                                            }
                                        }
                                    }
                                },
                                "responses": {
                                    "200": { "description": "Updated" },
                                    "404": { "description": "Not found" }
                                }
                            },
                            "delete": {
                                "tags": ["Equipment"],
                                "summary": "Delete equipment (soft delete)",
                                "parameters": [
                                    { "name": "id", "in": "path", "required": true, "schema": { "type": "integer" } }
                                ],
                                "responses": { "200": { "description": "Deleted" } }
                            }
                        },

                        "/equipment/{id}/maintenance": {
                            "post": {
                                "tags": ["Equipment"],
                                "summary": "Log maintenance for equipment",
                                "description": "Records a maintenance event. last_maintenance_date is automatically set to today.",
                                "parameters": [
                                    { "name": "id", "in": "path", "required": true, "schema": { "type": "integer" } }
                                ],
                                "requestBody": {
                                    "required": true,
                                    "content": {
                                        "application/json": {
                                            "schema": {
                                                "type": "object",
                                                "properties": {
                                                    "next_maintenance_date": { "type": "string", "format": "date", "example": "2026-11-01" },
                                                    "condition_status": { "type": "string", "example": "Good", "enum": ["Good", "Fair", "Poor"] },
                                                    "maintenance_notes": { "type": "string", "example": "Belt replaced, motor inspected." }
                                                }
                                            }
                                        }
                                    }
                                },
                                "responses": {
                                    "200": { "description": "Maintenance logged" },
                                    "404": { "description": "Equipment not found" }
                                }
                            }
                        }

                    }
                },
                dom_id: '#swagger-ui', deepLinking: true, presets: [ SwaggerUIBundle.presets.apis, SwaggerUIBundle.SwaggerUIStandalonePreset ], layout: "BaseLayout"
            });
        };
    </script>
</body>
</html>
