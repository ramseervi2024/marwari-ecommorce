# Pharmacy ERP API with Swagger UI

## Project Overview

Build a production-ready Pharmacy ERP as a custom WordPress Plugin.

The system will be used by:

* Retail Medical Stores
* Pharmacy Chains
* Hospital Pharmacies
* Wholesale Medicine Distributors
* Drug Stockists
* Medical Agencies
* Generic Medicine Stores
* Multi-Branch Pharmacy Businesses

The application must support complete pharmacy operations including:

* Medicine Stock Management
* Batch Tracking
* Expiry Tracking
* GST Billing
* Purchase Management
* Supplier Management
* Sales Management
* Prescription Management
* Inventory Audits
* Reports & Analytics

---

## Project Information

### Plugin Name

Pharmacy ERP API

### Dashboard URL

https://domain.com/pharmacy-management/

### Swagger API URL

https://domain.com/pharmacy-management-api-docs/

---

## Technology Stack

* WordPress Latest Version
* PHP 8+
* MySQL
* WordPress REST API
* JWT Authentication
* Swagger UI
* OpenAPI 3.0
* Composer
* PSR-4 Autoloading
* OOP Architecture

---

## System Roles

### Super Admin

Permissions:

* Full Access
* Dashboard
* Reports
* User Management

### Pharmacist

Permissions:

* Sales
* Billing
* Medicine Management
* Customers

### Purchase Manager

Permissions:

* Suppliers
* Purchases
* Inventory

### Store Manager

Permissions:

* Stock
* Expiry Management
* Reports

### Accountant

Permissions:

* Billing
* GST Reports
* Financial Reports

---

# Authentication Module

## APIs

POST /auth/register

POST /auth/login

POST /auth/logout

POST /auth/refresh-token

GET /auth/me

Requirements:

* JWT Authentication
* Password Hashing
* Role Based Authorization

---

# Dashboard Module

## API

GET /dashboard

### Dashboard Cards

* Total Medicines
* Current Stock Value
* Low Stock Medicines
* Expiring Medicines
* Today's Sales
* Monthly Revenue
* Pending Purchases
* GST Collected

### Dashboard Analytics

* Sales Trends
* Medicine Consumption
* Fast Moving Medicines
* Expiry Analysis
* Profitability Reports

---

# Medicine Management

## Database Table

pharmacy_medicines

### Fields

id

medicine_code

medicine_name

generic_name

brand_name

category

manufacturer

hsn_code

gst_percentage

unit

description

status

created_at

updated_at

### APIs

GET /medicines

GET /medicines/{id}

POST /medicines

PUT /medicines/{id}

DELETE /medicines/{id}

---

# Medicine Batch Management

## Database Table

pharmacy_batches

### Fields

id

medicine_id

batch_number

manufacture_date

expiry_date

purchase_price

mrp

selling_price

stock_quantity

created_at

updated_at

### APIs

GET /batches

POST /batches

PUT /batches/{id}

DELETE /batches/{id}

### Features

* Batch Tracking
* Batch Wise Stock
* Batch Wise Sales

---

# Stock Management

## Database Table

pharmacy_stock

### APIs

GET /stock

POST /stock

PUT /stock/{id}

DELETE /stock/{id}

### Features

* Real-Time Inventory
* Stock Adjustments
* Stock Transfers
* Stock History

---

# Expiry Management

## APIs

GET /expiry-alerts

GET /expired-stock

POST /expiry-notifications

### Features

* 30 Day Alert
* 60 Day Alert
* 90 Day Alert
* Expired Stock Reports
* Auto Notifications

---

# Supplier Management

## Database Table

pharmacy_suppliers

### Fields

id

supplier_code

supplier_name

contact_person

mobile

email

gst_number

address

status

created_at

updated_at

### APIs

GET /suppliers

POST /suppliers

PUT /suppliers/{id}

DELETE /suppliers/{id}

---

# Purchase Management

## Database Table

pharmacy_purchases

### Fields

id

purchase_number

supplier_id

invoice_number

purchase_date

subtotal

cgst

sgst

igst

grand_total

status

created_at

updated_at

### APIs

GET /purchases

POST /purchases

PUT /purchases/{id}

DELETE /purchases/{id}

### Features

* Purchase Orders
* GRN Management
* Supplier Returns

---

# Customer Management

## Database Table

pharmacy_customers

### APIs

GET /customers

POST /customers

PUT /customers/{id}

DELETE /customers/{id}

---

# Prescription Management

## Database Table

pharmacy_prescriptions

### APIs

GET /prescriptions

POST /prescriptions

PUT /prescriptions/{id}

DELETE /prescriptions/{id}

### Features

* Doctor Prescription Upload
* Prescription History
* Digital Prescription Storage

---

# Sales Management

## Database Table

pharmacy_sales

### Fields

id

invoice_number

customer_id

sale_date

subtotal

discount

cgst

sgst

igst

grand_total

payment_status

created_at

updated_at

### APIs

GET /sales

POST /sales

PUT /sales/{id}

DELETE /sales/{id}

---

# GST Billing Module

## Features

* GST Invoice Generation
* CGST Calculation
* SGST Calculation
* IGST Calculation
* HSN Code Support
* PDF Invoice

### APIs

POST /billing/generate

GET /billing/invoice/{id}

---

# Return Management

## APIs

GET /sales-returns

POST /sales-returns

GET /purchase-returns

POST /purchase-returns

### Features

* Customer Returns
* Supplier Returns
* Stock Adjustment

---

# Inventory Audit Module

## APIs

GET /inventory-audits

POST /inventory-audits

PUT /inventory-audits/{id}

DELETE /inventory-audits/{id}

---

# Barcode Module

## APIs

POST /barcode/generate

GET /barcode/{id}

### Features

* Medicine Barcode
* Barcode Scanner
* Label Printing

---

# Reports Module

### APIs

GET /reports/sales

GET /reports/purchases

GET /reports/gst

GET /reports/stock

GET /reports/expiry

GET /reports/profit-loss

GET /reports/fast-moving

GET /reports/slow-moving

GET /reports/suppliers

GET /reports/inventory-value

---

# Notifications Module

### Channels

* Email
* SMS
* WhatsApp

### Features

* Expiry Alerts
* Low Stock Alerts
* Purchase Alerts
* Billing Alerts

---

# Media Upload Module

### API

POST /media/upload

### Supported Files

* JPG
* PNG
* PDF
* XLSX
* CSV

### Maximum Size

20 MB

Store files in WordPress Media Library.

---

# Swagger Documentation

### URL

/pharmacy-management-api-docs

### Requirements

* OpenAPI 3.0
* JWT Authentication
* Bearer Token Support
* Request Examples
* Response Examples
* Try It Out Support

---

# Database Tables

wp_pharmacy_medicines

wp_pharmacy_batches

wp_pharmacy_stock

wp_pharmacy_suppliers

wp_pharmacy_purchases

wp_pharmacy_customers

wp_pharmacy_prescriptions

wp_pharmacy_sales

wp_pharmacy_returns

wp_pharmacy_inventory_audits

wp_pharmacy_documents

wp_pharmacy_activity_logs

---

# Security Requirements

Implement:

* JWT Authentication
* Input Validation
* SQL Injection Protection
* XSS Protection
* CSRF Protection
* Prepared Statements
* Request Sanitization

---

# Project Structure

pharmacy-management/

├── pharmacy-management.php

├── composer.json

├── routes/

├── controllers/

├── services/

├── repositories/

├── middleware/

├── models/

├── database/

├── swagger/

├── assets/

├── views/

├── uploads/

├── logs/

└── tests/

---

# Deliverables

1. WordPress Plugin
2. Database Migrations
3. JWT Authentication
4. Medicine APIs
5. Batch Tracking APIs
6. Stock Management APIs
7. Purchase APIs
8. Supplier APIs
9. Sales APIs
10. GST Billing APIs
11. Expiry Alert APIs
12. Reports APIs
13. Dashboard APIs
14. Swagger UI
15. OpenAPI Documentation
16. Validation Layer
17. Installation Guide
18. Postman Collection
19. Production Deployment Guide
20. Mobile App APIs

The code must be scalable, modular, secure, production-ready and follow WordPress Coding Standards and SOLID Principles.
