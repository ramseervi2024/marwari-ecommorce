# 🏭 Wholesale Distribution ERP — WordPress Plugin

> A **production-ready** Wholesale Distribution ERP built as a custom WordPress Plugin, exposing a fully-documented REST API with Swagger UI.

![WordPress](https://img.shields.io/badge/WordPress-Latest-21759B?logo=wordpress&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql&logoColor=white)
![JWT](https://img.shields.io/badge/Auth-JWT-000000?logo=jsonwebtokens&logoColor=white)
![OpenAPI](https://img.shields.io/badge/API-OpenAPI%203.0-6BA539?logo=openapiinitiative&logoColor=white)
![License](https://img.shields.io/badge/License-Proprietary-red)

---

## 📋 Table of Contents

- [Project Overview](#-project-overview)
- [Technology Stack](#-technology-stack)
- [Project URLs](#-project-urls)
- [System Roles](#-system-roles)
- [Modules & APIs](#-modules--apis)
  - [Authentication](#1-authentication-module)
  - [Dashboard](#2-dashboard-module)
  - [Dealer Management](#3-dealer-management)
  - [Product Management](#4-product-management)
  - [Pricing Management](#5-pricing-management)
  - [Dealer Order Management](#6-dealer-order-management)
  - [Sales Representative Management](#7-sales-representative-management)
  - [Route Management](#8-route-management)
  - [Inventory Management](#9-inventory-management)
  - [Warehouse Management](#10-warehouse-management)
  - [Dispatch Management](#11-dispatch-management)
  - [Credit Limit Management](#12-credit-limit-management)
  - [Payment Collection](#13-payment-collection-management)
  - [Outstanding Management](#14-outstanding-management)
  - [Purchase Management](#15-purchase-management)
  - [Supplier Management](#16-supplier-management)
  - [Billing Management](#17-billing-management)
  - [Reports Module](#18-reports-module)
  - [Dealer Portal](#19-dealer-portal)
  - [Notification Module](#20-notification-module)
  - [Media Upload](#21-media-upload-module)
- [Swagger Documentation](#-swagger-documentation)
- [Database Tables](#-database-tables)
- [Project Structure](#-project-structure)
- [Security Requirements](#-security-requirements)
- [Deliverables](#-deliverables)

---

## 🌐 Project Overview

This plugin powers complete **wholesale distribution operations** for a wide range of industries:

| Industry |
|----------|
| FMCG Distributors |
| Pharmaceutical Distributors |
| Electronics Distributors |
| Food & Beverage Distributors |
| Textile Wholesalers |
| Building Material Suppliers |
| Automobile Spare Parts Distributors |
| Consumer Goods Wholesalers |

**Core capabilities include:**

- Dealer Management & Ordering
- Dynamic Pricing & Scheme Management
- Inventory & Warehouse Management
- Delivery & Dispatch Management
- Credit Limit & Outstanding Management
- Payment Collection
- Sales Representative & Route Management
- Reports & Analytics
- Dealer Self-Service Portal

---

## 🛠 Technology Stack

| Technology | Purpose |
|------------|---------|
| WordPress (Latest) | Core Platform |
| PHP 8+ | Backend Language |
| MySQL | Database |
| WordPress REST API | API Framework |
| JWT Authentication | Secure Auth |
| Swagger UI | API Documentation |
| OpenAPI 3.0 | API Specification |
| Composer | Dependency Management |
| PSR-4 Autoloading | Class Autoloading |
| OOP Architecture | Code Structure |

---

## 🔗 Project URLs

| Resource | URL |
|----------|-----|
| Dashboard | `https://domain.com/wholesale-management/` |
| Swagger API Docs | `https://domain.com/wholesale-management-api-docs/` |

---

## 👥 System Roles

| Role | Permissions |
|------|-------------|
| **Super Admin** | Full Access · Global Dashboard · Reports · User Management · Pricing Control |
| **Distribution Manager** | Dealer Management · Orders · Deliveries · Credit Approvals |
| **Sales Executive** | Dealer Visits · Order Booking · Payment Collection |
| **Warehouse Manager** | Inventory · Dispatch · Stock Transfers |
| **Accountant** | Payments · Outstanding Reports · Credit Management |
| **Dealer** | Place Orders · View Outstanding · Download Invoices · Payment History |

---

## 📦 Modules & APIs

> **Base URL:** `/wp-json/wholesale/v1`

---

### 1. Authentication Module

**Requirements:** JWT Authentication · Password Hashing · Role-Based Authorization

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/auth/register` | Register a new user |
| `POST` | `/auth/login` | Login and receive JWT token |
| `POST` | `/auth/logout` | Invalidate session |
| `POST` | `/auth/refresh-token` | Refresh JWT token |
| `GET` | `/auth/me` | Get authenticated user info |

---

### 2. Dashboard Module

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/dashboard` | Get full dashboard data |

**Dashboard Cards:**
- Total Dealers · Today's Orders · Pending Deliveries · Outstanding Amount
- Available Stock · Monthly Sales · Collections Received · Credit Utilization

**Analytics:**
- Dealer Sales Analysis · Product Performance · Outstanding Trends · Territory Performance · Revenue Analysis

---

### 3. Dealer Management

**Table:** `wp_wholesale_dealers`

| Field | Type |
|-------|------|
| `id` | Primary Key |
| `dealer_code` | Unique Code |
| `dealer_name` | String |
| `owner_name` | String |
| `mobile` | String |
| `email` | String |
| `gst_number` | String |
| `address` | Text |
| `city` | String |
| `state` | String |
| `credit_limit` | Decimal |
| `available_credit` | Decimal |
| `status` | Enum |
| `created_at` / `updated_at` | Timestamps |

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/dealers` | List all dealers |
| `POST` | `/dealers` | Create a dealer |
| `PUT` | `/dealers/{id}` | Update dealer |
| `DELETE` | `/dealers/{id}` | Delete dealer |

---

### 4. Product Management

**Table:** `wp_wholesale_products`

| Field | Type |
|-------|------|
| `id` | Primary Key |
| `sku` | Unique Code |
| `barcode` | String |
| `product_name` | String |
| `category` | String |
| `brand` | String |
| `purchase_price` | Decimal |
| `mrp` | Decimal |
| `selling_price` | Decimal |
| `gst_percentage` | Decimal |
| `status` | Enum |
| `created_at` / `updated_at` | Timestamps |

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/products` | List all products |
| `POST` | `/products` | Add a product |
| `PUT` | `/products/{id}` | Update product |
| `DELETE` | `/products/{id}` | Delete product |

---

### 5. Pricing Management

**Table:** `wp_wholesale_pricing`

**Features:** Dealer Wise Pricing · Quantity Discounts · Promotional Pricing · Scheme Management

| Field | Type |
|-------|------|
| `id` | Primary Key |
| `product_id` | Foreign Key |
| `dealer_category` | String |
| `special_price` | Decimal |
| `discount_percentage` | Decimal |
| `effective_date` | Date |
| `expiry_date` | Date |
| `created_at` / `updated_at` | Timestamps |

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/pricing` | List pricing rules |
| `POST` | `/pricing` | Create pricing rule |
| `PUT` | `/pricing/{id}` | Update pricing rule |
| `DELETE` | `/pricing/{id}` | Delete pricing rule |

---

### 6. Dealer Order Management

**Table:** `wp_wholesale_orders`

**Order Statuses:** `Draft` → `Confirmed` → `Packed` → `Dispatched` → `Delivered` / `Cancelled`

| Field | Type |
|-------|------|
| `id` | Primary Key |
| `order_number` | Unique |
| `dealer_id` | Foreign Key |
| `order_date` | Date |
| `total_amount` | Decimal |
| `discount_amount` | Decimal |
| `gst_amount` | Decimal |
| `net_amount` | Decimal |
| `order_status` | Enum |
| `created_at` / `updated_at` | Timestamps |

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/orders` | List all orders |
| `GET` | `/orders/{id}` | Get a single order |
| `POST` | `/orders` | Create an order |
| `PUT` | `/orders/{id}` | Update order |
| `DELETE` | `/orders/{id}` | Cancel/delete order |

---

### 7. Sales Representative Management

**Table:** `wp_wholesale_sales_reps`

| Field | Type |
|-------|------|
| `id` | Primary Key |
| `employee_code` | Unique |
| `full_name` | String |
| `mobile` | String |
| `email` | String |
| `territory` | String |
| `target_amount` | Decimal |
| `status` | Enum |
| `created_at` / `updated_at` | Timestamps |

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/sales-reps` | List sales reps |
| `POST` | `/sales-reps` | Add a sales rep |
| `PUT` | `/sales-reps/{id}` | Update sales rep |
| `DELETE` | `/sales-reps/{id}` | Delete sales rep |

---

### 8. Route Management

**Table:** `wp_wholesale_routes`

**Features:** Dealer Route Planning · Beat Management · Visit Scheduling

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/routes` | List routes |
| `POST` | `/routes` | Create a route |
| `PUT` | `/routes/{id}` | Update route |
| `DELETE` | `/routes/{id}` | Delete route |

---

### 9. Inventory Management

**Table:** `wp_wholesale_inventory`

**Features:** Real-Time Inventory · Low Stock Alerts · Batch Tracking

| Field | Type |
|-------|------|
| `id` | Primary Key |
| `product_id` | Foreign Key |
| `warehouse_id` | Foreign Key |
| `available_stock` | Integer |
| `reserved_stock` | Integer |
| `damaged_stock` | Integer |
| `minimum_stock` | Integer |
| `created_at` / `updated_at` | Timestamps |

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/inventory` | List inventory |
| `POST` | `/inventory` | Add stock entry |
| `PUT` | `/inventory/{id}` | Update stock |
| `DELETE` | `/inventory/{id}` | Remove entry |

---

### 10. Warehouse Management

**Table:** `wp_wholesale_warehouses`

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/warehouses` | List warehouses |
| `POST` | `/warehouses` | Add a warehouse |
| `PUT` | `/warehouses/{id}` | Update warehouse |
| `DELETE` | `/warehouses/{id}` | Delete warehouse |

---

### 11. Dispatch Management

**Table:** `wp_wholesale_dispatches`

**Features:** Delivery Planning · Vehicle Assignment · Delivery Tracking

| Field | Type |
|-------|------|
| `id` | Primary Key |
| `dispatch_number` | Unique |
| `order_id` | Foreign Key |
| `vehicle_number` | String |
| `driver_name` | String |
| `dispatch_date` | Date |
| `status` | Enum |
| `created_at` / `updated_at` | Timestamps |

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/dispatches` | List dispatches |
| `POST` | `/dispatches` | Create dispatch |
| `PUT` | `/dispatches/{id}` | Update dispatch |
| `DELETE` | `/dispatches/{id}` | Delete dispatch |

---

### 12. Credit Limit Management

**Table:** `wp_wholesale_credit_limits`

**Features:** Credit Approval Workflow · Auto Credit Blocking · Overdue Monitoring

| Field | Type |
|-------|------|
| `id` | Primary Key |
| `dealer_id` | Foreign Key |
| `credit_limit` | Decimal |
| `used_credit` | Decimal |
| `available_credit` | Decimal |
| `approval_status` | Enum |
| `created_at` / `updated_at` | Timestamps |

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/credit-limits` | List credit limits |
| `POST` | `/credit-limits` | Set credit limit |
| `PUT` | `/credit-limits/{id}` | Update limit |
| `DELETE` | `/credit-limits/{id}` | Remove limit |

---

### 13. Payment Collection Management

**Table:** `wp_wholesale_payments`

**Payment Methods:** Cash · UPI · NEFT · RTGS · Cheque · Card

| Field | Type |
|-------|------|
| `id` | Primary Key |
| `receipt_number` | Unique |
| `dealer_id` | Foreign Key |
| `invoice_id` | Foreign Key |
| `payment_date` | Date |
| `amount` | Decimal |
| `payment_method` | Enum |
| `reference_number` | String |
| `status` | Enum |
| `created_at` / `updated_at` | Timestamps |

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/payments` | List payments |
| `POST` | `/payments` | Record a payment |
| `PUT` | `/payments/{id}` | Update payment |
| `DELETE` | `/payments/{id}` | Delete payment |

---

### 14. Outstanding Management

**Table:** `wp_wholesale_outstandings`

**Features:** Aging Reports · Overdue Tracking · Collection Follow-Ups

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/outstandings` | View outstandings |
| `POST` | `/outstandings` | Create outstanding entry |
| `PUT` | `/outstandings/{id}` | Update entry |
| `DELETE` | `/outstandings/{id}` | Delete entry |

---

### 15. Purchase Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/purchases` | List purchases |
| `POST` | `/purchases` | Create purchase |
| `PUT` | `/purchases/{id}` | Update purchase |
| `DELETE` | `/purchases/{id}` | Delete purchase |

---

### 16. Supplier Management

**Table:** `wp_wholesale_suppliers`

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/suppliers` | List suppliers |
| `POST` | `/suppliers` | Add supplier |
| `PUT` | `/suppliers/{id}` | Update supplier |
| `DELETE` | `/suppliers/{id}` | Delete supplier |

---

### 17. Billing Management

**Table:** `wp_wholesale_billing`

**Features:** GST Billing · E-Invoice Ready · PDF Invoice · Credit Notes · Debit Notes

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/billing` | List invoices |
| `POST` | `/billing` | Create invoice |
| `PUT` | `/billing/{id}` | Update invoice |
| `DELETE` | `/billing/{id}` | Delete invoice |

---

### 18. Reports Module

| Endpoint | Report |
|----------|--------|
| `GET /reports/dealers` | Dealer-wise Sales Report |
| `GET /reports/orders` | Orders Report |
| `GET /reports/sales` | Sales Performance Report |
| `GET /reports/collections` | Collections Report |
| `GET /reports/outstanding` | Outstanding Report |
| `GET /reports/inventory` | Inventory Report |
| `GET /reports/dispatches` | Dispatch Report |
| `GET /reports/gst` | GST Report |
| `GET /reports/targets` | Sales Target Report |
| `GET /reports/profit-loss` | Profit & Loss Report |

---

### 19. Dealer Portal

**Features:** Online Order Placement · Outstanding View · Payment History · Invoice Downloads · Scheme Details

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/portal/dashboard` | Dealer dashboard |
| `GET` | `/portal/orders` | Dealer's orders |
| `GET` | `/portal/payments` | Dealer's payments |
| `GET` | `/portal/invoices` | Dealer's invoices |

---

### 20. Notification Module

**Channels:** Email · SMS · WhatsApp · Push Notifications

**Triggers:**
- Order Confirmation
- Dispatch Alerts
- Payment Reminders
- Outstanding Alerts
- Scheme Notifications

---

### 21. Media Upload Module

| Endpoint | Description |
|----------|-------------|
| `POST /media/upload` | Upload a file to WordPress Media Library |

**Supported Formats:** JPG · PNG · PDF · XLSX · CSV  
**Maximum File Size:** 20 MB

---

## 📖 Swagger Documentation

| Item | Details |
|------|---------|
| URL | `/wholesale-management-api-docs` |
| Spec | OpenAPI 3.0 |
| Auth | JWT Bearer Token |
| Features | Request & Response Examples · Try It Out |

---

## 🗄 Database Tables

All tables use the WordPress table prefix `wp_`.

| Table Name |
|------------|
| `wp_wholesale_dealers` |
| `wp_wholesale_products` |
| `wp_wholesale_pricing` |
| `wp_wholesale_orders` |
| `wp_wholesale_sales_reps` |
| `wp_wholesale_routes` |
| `wp_wholesale_inventory` |
| `wp_wholesale_warehouses` |
| `wp_wholesale_dispatches` |
| `wp_wholesale_credit_limits` |
| `wp_wholesale_payments` |
| `wp_wholesale_outstandings` |
| `wp_wholesale_suppliers` |
| `wp_wholesale_billing` |
| `wp_wholesale_activity_logs` |
| `wp_wholesale_documents` |

---

## 📁 Project Structure

```
wholesale-management/
├── wholesale-management.php    # Plugin entry point
├── composer.json               # Composer dependencies
├── routes/                     # API route definitions
├── controllers/                # Request handlers
├── services/                   # Business logic layer
├── repositories/               # Database abstraction layer
├── middleware/                 # Auth & request middleware
├── models/                     # Data models
├── database/                   # Migrations & seeders
├── swagger/                    # OpenAPI spec files
├── assets/                     # JS, CSS assets
├── views/                      # Admin views / templates
├── uploads/                    # File uploads
├── logs/                       # Application logs
└── tests/                      # Unit & integration tests
```

---

## 🔒 Security Requirements

| Requirement | Details |
|-------------|---------|
| JWT Authentication | Bearer token on every protected route |
| Input Validation | Server-side validation on all inputs |
| SQL Injection Protection | WordPress `$wpdb->prepare()` / Prepared Statements |
| XSS Protection | Output escaping with `esc_html()`, `esc_attr()` |
| CSRF Protection | WordPress nonces |
| Request Sanitization | `sanitize_*` WordPress functions |

---

## ✅ Deliverables

| # | Deliverable |
|---|-------------|
| 1 | WordPress Plugin |
| 2 | Database Migrations |
| 3 | JWT Authentication |
| 4 | Dealer APIs |
| 5 | Order APIs |
| 6 | Pricing APIs |
| 7 | Credit Limit APIs |
| 8 | Inventory APIs |
| 9 | Dispatch APIs |
| 10 | Payment APIs |
| 11 | Billing APIs |
| 12 | Dashboard APIs |
| 13 | Reports APIs |
| 14 | Dealer Portal APIs |
| 15 | Swagger UI |
| 16 | OpenAPI Documentation |
| 17 | Validation Layer |
| 18 | Installation Guide |
| 19 | Postman Collection |
| 20 | Production Deployment Guide |
| 21 | Mobile Salesman App APIs |
| 22 | Dealer Ordering App APIs |

---

> **Code Standards:** The codebase must be **scalable, modular, secure, and production-ready**, following [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/) and **SOLID Principles**.
