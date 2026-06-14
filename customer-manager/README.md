# WordPress Customer Management API with Swagger UI

## Project Overview

Create a custom WordPress plugin called **Customer Manager API**.

The plugin should expose REST APIs for Customer CRUD operations and provide Swagger/OpenAPI documentation.

This project will be used as a Headless CMS backend for React or Angular applications.

---

## Technology Stack

* WordPress Latest Version
* PHP 8+
* MySQL
* WordPress REST API
* JWT Authentication
* Swagger UI (OpenAPI 3.0)
* PSR-4 Structure
* OOP Architecture

---

## Plugin Name

Customer Manager API

---

## Features

### Authentication

Implement JWT Authentication.

Endpoints:

POST /auth/login

POST /auth/register

POST /auth/refresh-token

POST /auth/logout

GET /auth/me

Requirements:

* Secure password hashing
* JWT token generation
* JWT validation middleware
* Role-based authorization

---

## Roles

### Super Admin

Can:

* Create Customers
* Update Customers
* Delete Customers
* View Customers
* Export Customers
* Access Dashboard

### Manager

Can:

* Create Customers
* Update Customers
* View Customers

### Viewer

Can:

* View Customers only

---

## Customer Module

Customer Table Fields:

id
first_name
last_name
email
phone
address
city
state
country
postal_code
status
created_at
updated_at

Status values:

ACTIVE
INACTIVE

---

## Customer APIs

### Get All Customers

GET /customers

Features:

* Pagination
* Sorting
* Searching
* Filtering

Query Parameters:

page
limit
search
sort
order

Example:

GET /customers?page=1&limit=10

---

### Get Customer By ID

GET /customers/{id}

---

### Create Customer

POST /customers

Request:

{
"first_name":"Ramesh",
"last_name":"Seervi",
"email":"[ramesh@gmail.com](mailto:ramesh@gmail.com)",
"phone":"9876543210",
"city":"Bangalore"
}

---

### Update Customer

PUT /customers/{id}

---

### Delete Customer

DELETE /customers/{id}

Soft Delete preferred.

---

## Dashboard APIs

GET /dashboard/stats

Response:

{
"totalCustomers":100,
"activeCustomers":90,
"inactiveCustomers":10
}

---

## Database

Create table automatically on plugin activation.

Table Name:

wp_customers

Requirements:

* Auto Increment ID
* Indexes on Email and Phone
* Timestamps
* Soft Delete Support

---

## Swagger Documentation

Implement OpenAPI 3.0

Swagger UI URL:

/customer-api-docs

Requirements:

* All endpoints documented
* Request schemas
* Response schemas
* Authentication support
* Bearer Token authorization

Features:

* Try It Out
* Execute API
* Request Examples
* Response Examples

---

## Project Structure

customer-manager/

├── customer-manager.php

├── composer.json

├── routes/

│ ├── auth.php

│ ├── customer.php

│ └── dashboard.php

├── controllers/

│ ├── AuthController.php

│ ├── CustomerController.php

│ └── DashboardController.php

├── services/

│ ├── AuthService.php

│ ├── CustomerService.php

│ └── JwtService.php

├── middleware/

│ ├── AuthMiddleware.php

│ └── RoleMiddleware.php

├── repositories/

│ └── CustomerRepository.php

├── models/

│ └── Customer.php

├── database/

│ └── migrations.php

├── swagger/

│ ├── swagger.json

│ └── index.php

├── views/

├── assets/

└── logs/

---

## Coding Standards

Requirements:

* OOP Only
* SOLID Principles
* Repository Pattern
* Service Layer Pattern
* Dependency Injection
* Exception Handling
* Validation Layer
* WordPress Coding Standards

---

## Security

Implement:

* JWT Authentication
* Input Validation
* SQL Injection Protection
* XSS Protection
* CSRF Protection
* Sanitization
* Prepared Statements

---

## Validation Rules

Email:

* Required
* Unique

Phone:

* Required
* Unique

First Name:

* Required
* Minimum 2 Characters

Last Name:

* Required
* Minimum 2 Characters

---

## API Response Format

Success:

{
"success": true,
"message": "Customer created successfully",
"data": {}
}

Error:

{
"success": false,
"message": "Validation failed",
"errors": []
}

---

## React/Angular Compatibility

All APIs must be consumable from:

* React
* Angular
* React Native
* Mobile Apps

Enable CORS configuration.

---

## Additional Features

Implement:

* Customer Export CSV
* Customer Import CSV
* Audit Logs
* Activity Tracking
* Pagination Metadata
* Global Error Handler

---

## Deliverables

Generate complete production-ready code including:

1. WordPress Plugin
2. Database Migration
3. JWT Authentication
4. Customer CRUD APIs
5. Swagger UI
6. OpenAPI Documentation
7. Middleware
8. Validation
9. Role Management
10. README Documentation
11. Installation Guide
12. Sample Postman Collection

Code should be production-ready and follow WordPress best practices.
