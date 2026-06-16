# Agriculture / Mandi ERP API with Swagger UI

## Project Overview

Build a production-ready Agriculture & Mandi ERP as a custom WordPress Plugin.

The system will be used by:

* Agricultural Mandis
* APMC Markets
* Grain Traders
* Commission Agents (Arhtiya)
* Rice Mills
* Cotton Traders
* Vegetable Markets
* Wholesale Buyers
* Farmer Producer Organizations (FPOs)
* Agri Procurement Companies

The application must support complete mandi operations including:

* Farmer Management
* Crop Procurement
* Weighbridge Integration
* Mandi Billing
* Stock Management
* Payment Management
* Commission Management
* Transport Management
* Reports & Analytics

---

## Project Information

### Plugin Name

Agriculture Mandi ERP API

### Dashboard URL

https://domain.com/agriculture-management/

### Swagger API URL

https://domain.com/agriculture-management-api-docs/

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
* Settings

### Mandi Manager

Permissions:

* Farmer Management
* Purchases
* Stock
* Billing

### Procurement Officer

Permissions:

* Crop Purchase
* Farmer Registration
* Weighing Records

### Accountant

Permissions:

* Payments
* GST Reports
* Commission Reports

### Store Manager

Permissions:

* Stock Management
* Dispatch Management

### Farmer

Permissions:

* Sale History
* Payment Status
* Receipts

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

* Total Farmers
* Today's Procurement
* Total Crop Stock
* Pending Payments
* Total Dispatches
* Monthly Purchases
* Total Revenue
* Commission Earned

### Dashboard Analytics

* Crop Procurement Trends
* Farmer Transactions
* Stock Movement
* Revenue Analysis
* Commodity Performance

---

# Farmer Management

## Database Table

agri_farmers

### Fields

id

farmer_code

farmer_name

father_name

mobile

aadhaar_number

village

taluk

district

state

bank_name

account_number

ifsc_code

created_at

updated_at

### APIs

GET /farmers

GET /farmers/{id}

POST /farmers

PUT /farmers/{id}

DELETE /farmers/{id}

---

# Crop Management

## Database Table

agri_crops

### Fields

id

crop_code

crop_name

crop_category

unit

market_rate

status

created_at

updated_at

### Examples

* Wheat
* Rice
* Cotton
* Groundnut
* Maize
* Sugarcane
* Soybean
* Onion

### APIs

GET /crops

POST /crops

PUT /crops/{id}

DELETE /crops/{id}

---

# Crop Purchase Management

## Database Table

agri_crop_purchases

### Fields

id

purchase_number

farmer_id

crop_id

purchase_date

quantity

rate

gross_amount

commission

net_amount

status

created_at

updated_at

### APIs

GET /crop-purchases

POST /crop-purchases

PUT /crop-purchases/{id}

DELETE /crop-purchases/{id}

---

# Weighbridge Management

## Database Table

agri_weighbridge

### Fields

id

weighment_number

vehicle_number

farmer_id

crop_id

gross_weight

tare_weight

net_weight

weighment_date

created_at

updated_at

### APIs

GET /weighbridge

POST /weighbridge

PUT /weighbridge/{id}

DELETE /weighbridge/{id}

### Features

* Digital Weighbridge Integration
* Weight Slip Generation
* Weight Audit Trail

---

# Mandi Billing Module

## Database Table

agri_mandi_bills

### Fields

id

bill_number

purchase_id

gross_amount

market_fee

commission_fee

hamali_charges

transport_charges

other_charges

net_payable

created_at

updated_at

### APIs

GET /mandi-bills

POST /mandi-bills

PUT /mandi-bills/{id}

DELETE /mandi-bills/{id}

### Features

* Auto Bill Generation
* Mandi Tax Calculation
* PDF Bill Printing

---

# Farmer Payment Management

## Database Table

agri_payments

### Fields

id

payment_number

farmer_id

purchase_id

payment_date

amount

payment_method

status

created_at

updated_at

### Payment Methods

* Cash
* Bank Transfer
* UPI
* Cheque

### APIs

GET /payments

POST /payments

PUT /payments/{id}

DELETE /payments/{id}

---

# Commission Agent Management

## Database Table

agri_commission_agents

### Fields

id

agent_name

mobile

commission_percentage

status

created_at

updated_at

### APIs

GET /agents

POST /agents

PUT /agents/{id}

DELETE /agents/{id}

---

# Stock Management

## Database Table

agri_stock

### Fields

id

crop_id

warehouse_id

available_quantity

reserved_quantity

unit

created_at

updated_at

### APIs

GET /stock

POST /stock

PUT /stock/{id}

DELETE /stock/{id}

### Features

* Real-Time Stock
* Warehouse Wise Stock
* Stock Valuation

---

# Warehouse Management

## Database Table

agri_warehouses

### APIs

GET /warehouses

POST /warehouses

PUT /warehouses/{id}

DELETE /warehouses/{id}

---

# Dispatch Management

## Database Table

agri_dispatches

### Fields

id

dispatch_number

crop_id

customer_id

vehicle_number

dispatch_date

quantity

status

created_at

updated_at

### APIs

GET /dispatches

POST /dispatches

PUT /dispatches/{id}

DELETE /dispatches/{id}

---

# Transport Management

## APIs

GET /transport

POST /transport

PUT /transport/{id}

DELETE /transport/{id}

### Features

* Vehicle Tracking
* Freight Calculation
* Driver Records

---

# Buyer Management

## Database Table

agri_buyers

### APIs

GET /buyers

POST /buyers

PUT /buyers/{id}

DELETE /buyers/{id}

---

# GST Billing Module

## APIs

GET /gst-invoices

POST /gst-invoices

PUT /gst-invoices/{id}

DELETE /gst-invoices/{id}

### Features

* GST Invoices
* E-Invoice Ready
* PDF Export

---

# Reports Module

### APIs

GET /reports/farmers

GET /reports/crops

GET /reports/purchases

GET /reports/weighments

GET /reports/payments

GET /reports/stock

GET /reports/dispatches

GET /reports/commission

GET /reports/gst

GET /reports/profit-loss

---

# Farmer Portal

### Features

* Sale History
* Payment History
* Crop Records
* Invoice Download

### APIs

GET /portal/dashboard

GET /portal/sales

GET /portal/payments

---

# Notification Module

### Channels

* SMS
* WhatsApp
* Email

### Features

* Payment Alerts
* Purchase Confirmations
* Dispatch Notifications
* Market Rate Updates

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

/agriculture-management-api-docs

### Requirements

* OpenAPI 3.0
* JWT Authentication
* Bearer Token Support
* Request Examples
* Response Examples
* Try It Out Support

---

# Database Tables

wp_agri_farmers

wp_agri_crops

wp_agri_crop_purchases

wp_agri_weighbridge

wp_agri_mandi_bills

wp_agri_payments

wp_agri_commission_agents

wp_agri_stock

wp_agri_warehouses

wp_agri_dispatches

wp_agri_buyers

wp_agri_documents

wp_agri_activity_logs

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

agriculture-management/

├── agriculture-management.php

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
4. Farmer APIs
5. Crop Purchase APIs
6. Weighbridge APIs
7. Stock APIs
8. Warehouse APIs
9. Dispatch APIs
10. Mandi Billing APIs
11. Payment APIs
12. Dashboard APIs
13. Reports APIs
14. Farmer Portal APIs
15. Swagger UI
16. OpenAPI Documentation
17. Validation Layer
18. Installation Guide
19. Postman Collection
20. Production Deployment Guide
21. Mobile App APIs

The code must be scalable, modular, secure, production-ready and follow WordPress Coding Standards and SOLID Principles.
