# Wholesale Distribution ERP - Operations & Integration Guide

This guide provides a comprehensive overview of the **Wholesale Distribution ERP API** WordPress plugin, including its architectural design, role-based access control, test credentials, and client endpoints workflow.

---

## 1. Plugin Contents & Modules

The plugin exposes a WordPress REST API under the `/wp-json/wholesale/v1` namespace.

| Module | Core Functionality | Database Table |
| :--- | :--- | :--- |
| **Authentication** | JWT secure token registration, login, logout, and token rotation. | Standard `wp_users` |
| **Dashboard** | Aggregated stats (total dealers, today's orders, pending dispatches, revenue). | - |
| **Dealers** | Manage wholesale dealers, contact profiles, credit and locations. | `wp_wholesale_dealers` |
| **Products** | Manage product catalog, SKUs, barcode tracking, pricing, and HSN codes. | `wp_wholesale_products` |
| **Pricing** | Manage special dealer-wise pricing rules and discounts. | `wp_wholesale_pricing` |
| **Orders** | Manage dealer orders, lines/items, and order status lifecycle. | `wp_wholesale_orders` & `wp_wholesale_order_items` |
| **Sales Reps** | Manage sales representatives, assigned territories, and targets. | `wp_wholesale_sales_reps` |
| **Routes** | Manage daily visit planning (beats) and routes. | `wp_wholesale_routes` |
| **Inventory** | Track stock levels across different warehouse locations. | `wp_wholesale_inventory` |
| **Warehouses** | Manage warehouses, codes, locations, and managers. | `wp_wholesale_warehouses` |
| **Dispatches** | Track dispatches, vehicle numbers, drivers, and delivery status. | `wp_wholesale_dispatches` |
| **Credit Limits** | Manage credit limits, approvals, used credit, and credit blocking. | `wp_wholesale_credit_limits` |
| **Payments** | Record collections, payments received, payment mode, and references. | `wp_wholesale_payments` |
| **Outstandings** | Track unpaid invoices, dues, balance, and overdue days. | `wp_wholesale_outstandings` |
| **Suppliers** | Manage product suppliers and contact details. | `wp_wholesale_suppliers` |
| **Purchases** | Record purchase orders and warehouse stock intake. | `wp_wholesale_purchases` |
| **Billing** | Generate invoices, calculate tax subtotal, and track status. | `wp_wholesale_billing` |
| **Reports** | 10 specialized reports for sales, collections, GST, targets, P&L. | - |
| **Dealer Portal** | Dealer self-service dashboard, ordering, payments, and invoices. | - |
| **Media Upload** | Upload document attachments to WordPress media library. | `wp_wholesale_documents` |

---

## 2. Authentication & JWT Login Flow

The plugin secures REST endpoints via **JWT (JSON Web Token)** using the standard `HS256` encryption algorithm.

### Default Client Test Credentials

During plugin activation, standard mock user accounts are generated automatically for testing:

| Username | Password | Assigned Role | Capabilities / Permissions |
| :--- | :--- | :--- | :--- |
| `wholesale_admin` | `admin123` | `wholesale_super_admin` | Full control over system configurations, pricing, and users. |
| `wholesale_manager` | `manager123` | `wholesale_dist_manager` | Manage dealers, orders, dispatches, and credit approvals. |
| `wholesale_sales` | `sales123` | `wholesale_sales_exec` | Record dealer orders, track routes, and collect payments. |
| `wholesale_accountant` | `accounts123` | `wholesale_accountant` | Record payments, audit outstandings, and manage credit. |

### Authentication Endpoints

#### Log In to Retrieve Tokens
* **Endpoint**: `POST /wp-json/wholesale/v1/auth/login`
* **Request Payload**:
  ```json
  {
    "username": "wholesale_admin",
    "password": "admin123"
  }
  ```

---

## 3. Swagger UI Documentation

Access the interactive visual Swagger UI playground to execute mock requests and inspect response schemas:
* **Playground URL**: `/wholesale-management-api-docs/`

---

## 4. Modern Operations Dashboard

The plugin serves a modern, decoupled Single Page Application (SPA) dashboard for live wholesale distribution management:
* **Dashboard URL**: `/wholesale-management/`
* **Features**: Live metrics, recent orders tracking, inventory notifications, and quick action panels. Styled with a premium theme including charts and clean layouts.
