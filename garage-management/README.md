# Automobile Garage ERP API with Swagger UI

## Project Overview

Build a production-ready Automobile Garage ERP as a custom WordPress Plugin.

The system will be used by:

* Automobile Service Centers
* Car Garages
* Bike Service Centers
* Multi-Brand Workshops
* Authorized Service Centers
* Fleet Maintenance Workshops
* Truck Service Centers
* Vehicle Repair Shops

The application must support complete garage operations including:

* Customer Management
* Vehicle Management
* Job Card Management
* Service Estimation
* Spare Parts Inventory
* Mechanic Management
* Service History
* Billing & GST
* Warranty Tracking
* Reports & Analytics

---

## Project Information

### Plugin Name

Automobile Garage ERP API

### Dashboard URL

https://domain.com/garage-management/

### Swagger API URL

https://domain.com/garage-management-api-docs/

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
* Settings
* User Management

### Service Manager

Permissions:

* Job Cards
* Estimates
* Mechanics
* Service Reports

### Service Advisor

Permissions:

* Customer Registration
* Vehicle Inspection
* Estimate Creation

### Mechanic

Permissions:

* Assigned Jobs
* Job Updates
* Service Completion

### Store Manager

Permissions:

* Spare Parts Inventory
* Purchase Orders
* Stock Management

### Accountant

Permissions:

* Billing
* GST Reports
* Payments

### Customer

Permissions:

* Service History
* Estimates
* Invoices

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

* Total Vehicles
* Active Job Cards
* Pending Deliveries
* Spare Parts Value
* Today's Revenue
* Pending Estimates
* Active Mechanics
* Monthly Service Orders

### Dashboard Analytics

* Service Revenue Trends
* Mechanic Productivity
* Vehicle Service Trends
* Spare Parts Consumption
* Customer Retention

---

# Customer Management

## Database Table

garage_customers

### Fields

id

customer_code

customer_name

mobile

email

address

city

state

created_at

updated_at

### APIs

GET /customers

POST /customers

PUT /customers/{id}

DELETE /customers/{id}

---

# Vehicle Management

## Database Table

garage_vehicles

### Fields

id

customer_id

vehicle_number

vehicle_type

brand

model

year

engine_number

chassis_number

fuel_type

odometer_reading

created_at

updated_at

### Vehicle Types

* Car
* Bike
* Truck
* Bus
* Commercial Vehicle

### APIs

GET /vehicles

POST /vehicles

PUT /vehicles/{id}

DELETE /vehicles/{id}

---

# Job Card Management

## Database Table

garage_job_cards

### Fields

id

job_card_number

customer_id

vehicle_id

advisor_id

mechanic_id

complaints

inspection_notes

job_status

created_at

updated_at

### Job Status

* Open
* In Progress
* Waiting For Parts
* Completed
* Delivered
* Cancelled

### APIs

GET /job-cards

GET /job-cards/{id}

POST /job-cards

PUT /job-cards/{id}

DELETE /job-cards/{id}

---

# Vehicle Inspection Module

## APIs

GET /inspections

POST /inspections

PUT /inspections/{id}

DELETE /inspections/{id}

### Features

* Pre-Service Inspection
* Photo Upload
* Damage Marking
* Customer Approval

---

# Estimate Management

## Database Table

garage_estimates

### Fields

id

estimate_number

job_card_id

labour_cost

parts_cost

tax_amount

discount

total_amount

approval_status

created_at

updated_at

### APIs

GET /estimates

POST /estimates

PUT /estimates/{id}

DELETE /estimates/{id}

### Features

* Estimate Approval
* WhatsApp Estimate Sharing
* PDF Estimate

---

# Spare Parts Management

## Database Table

garage_spare_parts

### Fields

id

part_code

part_name

category

purchase_price

selling_price

stock_quantity

minimum_stock

supplier_id

created_at

updated_at

### APIs

GET /spare-parts

POST /spare-parts

PUT /spare-parts/{id}

DELETE /spare-parts/{id}

### Features

* Stock Tracking
* Low Stock Alerts
* Barcode Support

---

# Spare Parts Usage Module

## APIs

GET /parts-usage

POST /parts-usage

PUT /parts-usage/{id}

DELETE /parts-usage/{id}

### Features

* Job Card Wise Parts Usage
* Inventory Deduction

---

# Mechanic Management

## Database Table

garage_mechanics

### Fields

id

employee_code

mechanic_name

mobile

specialization

salary

joining_date

status

created_at

updated_at

### APIs

GET /mechanics

POST /mechanics

PUT /mechanics/{id}

DELETE /mechanics/{id}

---

# Service History Module

## Database Table

garage_service_history

### APIs

GET /service-history

GET /service-history/{vehicle_id}

### Features

* Complete Vehicle History
* Previous Repairs
* Warranty Records

---

# Purchase Management

## APIs

GET /purchases

POST /purchases

PUT /purchases/{id}

DELETE /purchases/{id}

### Features

* Spare Parts Procurement
* Supplier Management

---

# Supplier Management

## Database Table

garage_suppliers

### APIs

GET /suppliers

POST /suppliers

PUT /suppliers/{id}

DELETE /suppliers/{id}

---

# Warranty Management

## APIs

GET /warranties

POST /warranties

PUT /warranties/{id}

DELETE /warranties/{id}

### Features

* Service Warranty
* Spare Part Warranty

---

# Billing Management

## Database Table

garage_billing

### APIs

GET /billing

POST /billing

PUT /billing/{id}

DELETE /billing/{id}

### Features

* GST Billing
* Labour Charges
* Parts Charges
* PDF Invoice

---

# Payment Management

## Database Table

garage_payments

### APIs

GET /payments

POST /payments

PUT /payments/{id}

DELETE /payments/{id}

### Payment Methods

* Cash
* UPI
* Credit Card
* Debit Card
* Net Banking

---

# Reports Module

### APIs

GET /reports/job-cards

GET /reports/revenue

GET /reports/parts

GET /reports/mechanics

GET /reports/service-history

GET /reports/customers

GET /reports/inventory

GET /reports/gst

GET /reports/warranty

GET /reports/profit-loss

---

# Customer Portal

### Features

* Service History
* Job Status Tracking
* Estimate Approval
* Invoice Download
* Service Reminders

### APIs

GET /portal/dashboard

GET /portal/service-history

GET /portal/invoices

---

# Notification Module

### Channels

* SMS
* WhatsApp
* Email

### Features

* Service Updates
* Estimate Approval Requests
* Vehicle Ready Alerts
* Service Due Reminders

---

# Media Upload Module

### API

POST /media/upload

### Supported Files

* JPG
* PNG
* PDF
* MP4
* XLSX

### Maximum Size

20 MB

Store files in WordPress Media Library.

---

# Swagger Documentation

### URL

/garage-management-api-docs

### Requirements

* OpenAPI 3.0
* JWT Authentication
* Bearer Token Support
* Request Examples
* Response Examples
* Try It Out Support

---

# Database Tables

wp_garage_customers

wp_garage_vehicles

wp_garage_job_cards

wp_garage_inspections

wp_garage_estimates

wp_garage_spare_parts

wp_garage_parts_usage

wp_garage_mechanics

wp_garage_service_history

wp_garage_suppliers

wp_garage_purchases

wp_garage_warranties

wp_garage_billing

wp_garage_payments

wp_garage_documents

wp_garage_activity_logs

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

garage-management/

├── garage-management.php

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
4. Customer APIs
5. Vehicle APIs
6. Job Card APIs
7. Estimate APIs
8. Spare Parts APIs
9. Mechanic APIs
10. Billing APIs
11. Payment APIs
12. Service History APIs
13. Dashboard APIs
14. Reports APIs
15. Customer Portal APIs
16. Swagger UI
17. OpenAPI Documentation
18. Validation Layer
19. Installation Guide
20. Postman Collection
21. Production Deployment Guide
22. Mobile Mechanic App APIs

The code must be scalable, modular, secure, production-ready and follow WordPress Coding Standards and SOLID Principles.
