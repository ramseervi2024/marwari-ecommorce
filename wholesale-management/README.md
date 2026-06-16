# Wholesale Distribution ERP API with Swagger UI

## Project Overview

Build a production-ready Wholesale Distribution ERP as a custom WordPress Plugin.

The system will be used by:

* FMCG Distributors
* Pharmaceutical Distributors
* Electronics Distributors
* Food & Beverage Distributors
* Textile Wholesalers
* Building Material Suppliers
* Automobile Spare Parts Distributors
* Consumer Goods Wholesalers

The application must support complete wholesale distribution operations including:

* Dealer Management
* Dealer Orders
* Dynamic Pricing
* Inventory Management
* Delivery Management
* Credit Limit Management
* Payment Collection
* Sales Representatives
* Route Management
* Reports & Analytics

---

## Project Information

### Plugin Name

Wholesale Distribution ERP API

### Dashboard URL

https://domain.com/wholesale-management/

### Swagger API URL

https://domain.com/wholesale-management-api-docs/

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
* Global Dashboard
* Reports
* User Management
* Pricing Control

### Distribution Manager

Permissions:

* Dealer Management
* Orders
* Deliveries
* Credit Approvals

### Sales Executive

Permissions:

* Dealer Visits
* Order Booking
* Payment Collection

### Warehouse Manager

Permissions:

* Inventory
* Dispatch
* Stock Transfers

### Accountant

Permissions:

* Payments
* Outstanding Reports
* Credit Management

### Dealer

Permissions:

* Place Orders
* View Outstanding
* Download Invoices
* Payment History

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

* Total Dealers
* Today's Orders
* Pending Deliveries
* Outstanding Amount
* Available Stock
* Monthly Sales
* Collections Received
* Credit Utilization

### Dashboard Analytics

* Dealer Sales Analysis
* Product Performance
* Outstanding Trends
* Territory Performance
* Revenue Analysis

---

# Dealer Management

## Database Table

wholesale_dealers

### Fields

id

dealer_code

dealer_name

owner_name

mobile

email

gst_number

address

city

state

credit_limit

available_credit

status

created_at

updated_at

### APIs

GET /dealers

POST /dealers

PUT /dealers/{id}

DELETE /dealers/{id}

---

# Product Management

## Database Table

wholesale_products

### Fields

id

sku

barcode

product_name

category

brand

purchase_price

mrp

selling_price

gst_percentage

status

created_at

updated_at

### APIs

GET /products

POST /products

PUT /products/{id}

DELETE /products/{id}

---

# Pricing Management

## Database Table

wholesale_pricing

### Fields

id

product_id

dealer_category

special_price

discount_percentage

effective_date

expiry_date

created_at

updated_at

### Features

* Dealer Wise Pricing
* Quantity Discounts
* Promotional Pricing
* Scheme Management

### APIs

GET /pricing

POST /pricing

PUT /pricing/{id}

DELETE /pricing/{id}

---

# Dealer Order Management

## Database Table

wholesale_orders

### Fields

id

order_number

dealer_id

order_date

total_amount

discount_amount

gst_amount

net_amount

order_status

created_at

updated_at

### Order Status

* Draft
* Confirmed
* Packed
* Dispatched
* Delivered
* Cancelled

### APIs

GET /orders

GET /orders/{id}

POST /orders

PUT /orders/{id}

DELETE /orders/{id}

---

# Sales Representative Management

## Database Table

wholesale_sales_reps

### Fields

id

employee_code

full_name

mobile

email

territory

target_amount

status

created_at

updated_at

### APIs

GET /sales-reps

POST /sales-reps

PUT /sales-reps/{id}

DELETE /sales-reps/{id}

---

# Route Management

## Database Table

wholesale_routes

### APIs

GET /routes

POST /routes

PUT /routes/{id}

DELETE /routes/{id}

### Features

* Dealer Route Planning
* Beat Management
* Visit Scheduling

---

# Inventory Management

## Database Table

wholesale_inventory

### Fields

id

product_id

warehouse_id

available_stock

reserved_stock

damaged_stock

minimum_stock

created_at

updated_at

### APIs

GET /inventory

POST /inventory

PUT /inventory/{id}

DELETE /inventory/{id}

### Features

* Real-Time Inventory
* Low Stock Alerts
* Batch Tracking

---

# Warehouse Management

## Database Table

wholesale_warehouses

### APIs

GET /warehouses

POST /warehouses

PUT /warehouses/{id}

DELETE /warehouses/{id}

---

# Dispatch Management

## Database Table

wholesale_dispatches

### Fields

id

dispatch_number

order_id

vehicle_number

driver_name

dispatch_date

status

created_at

updated_at

### APIs

GET /dispatches

POST /dispatches

PUT /dispatches/{id}

DELETE /dispatches/{id}

### Features

* Delivery Planning
* Vehicle Assignment
* Delivery Tracking

---

# Credit Limit Management

## Database Table

wholesale_credit_limits

### Fields

id

dealer_id

credit_limit

used_credit

available_credit

approval_status

created_at

updated_at

### APIs

GET /credit-limits

POST /credit-limits

PUT /credit-limits/{id}

DELETE /credit-limits/{id}

### Features

* Credit Approval Workflow
* Auto Credit Blocking
* Overdue Monitoring

---

# Payment Collection Management

## Database Table

wholesale_payments

### Fields

id

receipt_number

dealer_id

invoice_id

payment_date

amount

payment_method

reference_number

status

created_at

updated_at

### Payment Methods

* Cash
* UPI
* NEFT
* RTGS
* Cheque
* Card

### APIs

GET /payments

POST /payments

PUT /payments/{id}

DELETE /payments/{id}

---

# Outstanding Management

## APIs

GET /outstandings

POST /outstandings

PUT /outstandings/{id}

DELETE /outstandings/{id}

### Features

* Aging Reports
* Overdue Tracking
* Collection Follow-Ups

---

# Purchase Management

## APIs

GET /purchases

POST /purchases

PUT /purchases/{id}

DELETE /purchases/{id}

---

# Supplier Management

## Database Table

wholesale_suppliers

### APIs

GET /suppliers

POST /suppliers

PUT /suppliers/{id}

DELETE /suppliers/{id}

---

# Billing Management

## Database Table

wholesale_billing

### APIs

GET /billing

POST /billing

PUT /billing/{id}

DELETE /billing/{id}

### Features

* GST Billing
* E-Invoice Ready
* PDF Invoice
* Credit Notes
* Debit Notes

---

# Reports Module

### APIs

GET /reports/dealers

GET /reports/orders

GET /reports/sales

GET /reports/collections

GET /reports/outstanding

GET /reports/inventory

GET /reports/dispatches

GET /reports/gst

GET /reports/targets

GET /reports/profit-loss

---

# Dealer Portal

### Features

* Online Order Placement
* Outstanding View
* Payment History
* Invoice Downloads
* Scheme Details

### APIs

GET /portal/dashboard

GET /portal/orders

GET /portal/payments

GET /portal/invoices

---

# Notification Module

### Channels

* Email
* SMS
* WhatsApp
* Push Notifications

### Features

* Order Confirmation
* Dispatch Alerts
* Payment Reminders
* Outstanding Alerts
* Scheme Notifications

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

/wholesale-management-api-docs

### Requirements

* OpenAPI 3.0
* JWT Authentication
* Bearer Token Support
* Request Examples
* Response Examples
* Try It Out Support

---

# Database Tables

wp_wholesale_dealers

wp_wholesale_products

wp_wholesale_pricing

wp_wholesale_orders

wp_wholesale_sales_reps

wp_wholesale_routes

wp_wholesale_inventory

wp_wholesale_warehouses

wp_wholesale_dispatches

wp_wholesale_credit_limits

wp_wholesale_payments

wp_wholesale_outstandings

wp_wholesale_suppliers

wp_wholesale_billing

wp_wholesale_activity_logs

wp_wholesale_documents

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

wholesale-management/

├── wholesale-management.php

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
4. Dealer APIs
5. Order APIs
6. Pricing APIs
7. Credit Limit APIs
8. Inventory APIs
9. Dispatch APIs
10. Payment APIs
11. Billing APIs
12. Dashboard APIs
13. Reports APIs
14. Dealer Portal APIs
15. Swagger UI
16. OpenAPI Documentation
17. Validation Layer
18. Installation Guide
19. Postman Collection
20. Production Deployment Guide
21. Mobile Salesman App APIs
22. Dealer Ordering App APIs

The code must be scalable, modular, secure, production-ready and follow WordPress Coding Standards and SOLID Principles.
