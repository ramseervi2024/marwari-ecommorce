# Inventory Management ERP - System Overview

An enterprise-grade, decoupled, Object-Oriented Programming (OOP) based WordPress plugin that exposes REST API endpoints for a complete inventory ecosystem. It features role-based access control, barcode/QR code mapping, physical audits reconciliation, Goods Receipt Notes (GRN) receipts, and warehouse-to-warehouse stock transfers.

---

## 1. System Role Credentials & Capability Matrix

The database migrations provision default testing accounts with `123456` password credentials. Role definitions restrict or allow specific modular actions as detailed below:

| Username | System Role | Display Name | Permissions/Capabilities |
| :--- | :--- | :--- | :--- |
| **`isuperadmin`** | `inventory_super_admin` | Inventory Super Admin | Full administrative control of inventory, settings, users, and warehouses |
| **`imanager`** | `inventory_manager` | John Inventory Manager | Product profiles, warehouses, physical audits, damage logs, and reports |
| **`ipurchasemgr`** | `inventory_purchase_manager` | Sarah Purchase | Supplier directories, purchase orders (PO), and goods receipt notes (GRN) |
| **`istaff`** | `inventory_warehouse_staff` | Mike Warehouse | Inwards, outwards, and warehouse-to-warehouse stock transfers |
| **`iauditor`** | `inventory_auditor` | Rajesh Auditor | Read-only access to dashboard statistics and valuation reports |

---

## 2. Core Custom Database Tables

Eighteen custom tables prefixed with `wp_inv_` are automatically created on plugin activation:

1. **`wp_inv_products`**: Core product catalog profiles (SKU, brand, prices, barcode value, unit).
2. **`wp_inv_warehouses`**: Storage facility centers (manager, contact, maximum capacity).
3. **`wp_inv_stock`**: Real-time stock ledger (available, reserved, and damaged stock tracking).
4. **`wp_inv_suppliers`**: Supplier profiles registry (rating, contact person, mobile, gst_number).
5. **`wp_inv_purchase_orders` & `wp_inv_po_items`**: Purchase order logs & product line-items.
6. **`wp_inv_grn` & `wp_inv_grn_items`**: Goods Receipt Notes matching orders received against POs.
7. **`wp_inv_stock_inward` & `wp_inv_inward_items`**: Transaction log for receipt of stocks.
8. **`wp_inv_stock_outward` & `wp_inv_outward_items`**: Transaction log for consumption/issues of stocks.
9. **`wp_inv_transfers` & `wp_inv_transfer_items`**: Warehouse-to-warehouse transfers.
10. **`wp_inv_audits` & `wp_inv_audit_items`**: Scheduled physical stock counts and variance reconciliation.
11. **`wp_inv_damaged_stock`**: Damaged stock reporting and scrapped/repaired tracking.
12. **`wp_inv_activity_logs`**: System audit logs recording actions done by users.

---

## 3. REST API Routes Reference

All API routes require authentication headers: `Authorization: Bearer <JWT_TOKEN>`.

### Authentication & Users
* `POST /auth/register` (Initiate user registration)
* `POST /auth/register/verify` (Verify registration code via OTP)
* `POST /auth/login/initiate` (Passwordless login otp generation)
* `POST /auth/login` (Authenticate credentials or OTP to get JWT)
* `GET /auth/me` (Retrieve active profile details)
* `POST /auth/logout` (Invalidate active refresh tokens)
* `GET /auth/users` (List employees)
* `POST /auth/users/status` (Update approval/blocked user status)

### Catalog & Warehouses
* `GET`, `POST` `/products` (List / Create product profile)
* `GET`, `PUT`, `DELETE` `/products/{id}` (Read / Update / Delete product)
* `GET`, `POST` `/warehouses` (List / Create warehouse facility)
* `GET`, `PUT`, `DELETE` `/warehouses/{id}` (Read / Update / Delete warehouse)

### Stock Adjustments & Logistics
* `GET` `/inventory` (General stock ledger list)
* `POST` `/inventory` (Direct manual stock count adjust)
* `GET`, `POST` `/stock-inward` (Inward receipts)
* `GET`, `POST` `/stock-outward` (Outward issues/consumption)
* `GET`, `POST` `/transfers` (Warehouse-to-warehouse transfers)
* `PUT` `/transfers/{id}/status` (Verify and complete transfers, auto-shifting stock)

### Procurement & Goods Inflow
* `GET`, `POST` `/purchase-orders` (Procurement orders)
* `PUT` `/purchase-orders/{id}` (Update PO workflow: Approved, Completed, Cancelled)
* `GET`, `POST` `/grn` (Record Goods Receipt, auto-updating stock ledger and matching PO quantities)

### Barcodes & QR Scanning
* `POST` `/barcode/generate` (Generate barcode tag label URLs)
* `GET` `/barcode/{code}` (UPC scanner lookup resolution)
* `POST` `/qrcode/generate` (Generate QR tag label URLs)
* `GET` `/qrcode/{code}` (QR code scanner lookup resolution)

### Audits & Reconciliations
* `GET`, `POST` `/audits` (Log physical stock counts)
* `PUT` `/audits/{id}` (Finalize audit count, auto-reconciling system stock ledger based on variance)
* `GET`, `POST` `/damaged-stock` (Log damaged products)
* `PUT` `/damaged-stock/{id}` (Set damage disposition: Scrapped, Repaired)

---

## 4. UI Dashboard & Swagger Playground URLs

After activation, the plugin rewrites URLs for headless execution:

* **Interactive Client Dashboard View**: `http://<your-wordpress-site>/inventory-management/`
  * Fully custom glassmorphic layout.
  * Preserves active tab selection on page reload (`localStorage.setItem('inv_active_tab')`).
  * Features a persistent dual Light Mode/Dark Mode theme switcher (Light Mode by default).
  * Employs inline head-checking script to prevent unauthenticated layout flash (FOUC).
  
* **Swagger OpenAPI Documentation Playground**: `http://<your-wordpress-site>/inventory-management-api-docs/`
  * Complete API documentation UI that lets you execute and test every single endpoint live.
