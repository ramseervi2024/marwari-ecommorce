# Salon & Spa ERP API with Swagger UI

## Project Overview

Build a production-ready Salon & Spa ERP as a custom WordPress Plugin.

The system will be used by:

* Beauty Salons
* Spa Centers
* Hair Salons
* Unisex Salons
* Wellness Centers
* Skin Care Clinics
* Nail Studios
* Beauty Franchise Chains
* Luxury Spa Resorts

The application must support complete salon and spa operations including:

* Appointment Management
* Customer Management
* Service Packages
* Staff Management
* Staff Commission Management
* Inventory Management
* Billing & GST
* Membership Programs
* Loyalty Programs
* Reports & Analytics

---

## Project Information

### Plugin Name

Salon Spa ERP API

### Dashboard URL

https://domain.com/salon-management/

### Swagger API URL

https://domain.com/salon-management-api-docs/

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

### Salon Manager

Permissions:

* Appointments
* Staff Management
* Billing
* Inventory
* Reports

### Receptionist

Permissions:

* Appointment Booking
* Customer Registration
* Billing

### Beautician / Therapist

Permissions:

* View Assigned Appointments
* Service Updates
* Commission Tracking

### Accountant

Permissions:

* Payments
* GST Reports
* Commission Reports

### Customer

Permissions:

* Online Booking
* Package Details
* Appointment History

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

* Total Customers
* Today's Appointments
* Completed Services
* Active Packages
* Staff Performance
* Inventory Value
* Monthly Revenue
* Pending Payments

### Dashboard Analytics

* Revenue Trends
* Appointment Trends
* Customer Retention
* Staff Productivity
* Service Popularity

---

# Customer Management

## Database Table

salon_customers

### Fields

id

customer_code

customer_name

mobile

email

gender

date_of_birth

address

membership_status

created_at

updated_at

### APIs

GET /customers

POST /customers

PUT /customers/{id}

DELETE /customers/{id}

---

# Appointment Management

## Database Table

salon_appointments

### Fields

id

appointment_number

customer_id

staff_id

service_id

appointment_date

appointment_time

status

remarks

created_at

updated_at

### Status

* Booked
* Confirmed
* In Progress
* Completed
* Cancelled
* No Show

### APIs

GET /appointments

POST /appointments

PUT /appointments/{id}

DELETE /appointments/{id}

### Features

* Online Booking
* Appointment Calendar
* SMS Reminders
* WhatsApp Reminders

---

# Service Management

## Database Table

salon_services

### Fields

id

service_name

category

duration

price

description

status

created_at

updated_at

### Categories

* Hair Cut
* Hair Color
* Facial
* Spa
* Massage
* Nail Care
* Skin Treatment
* Makeup

### APIs

GET /services

POST /services

PUT /services/{id}

DELETE /services/{id}

---

# Package Management

## Database Table

salon_packages

### Fields

id

package_name

package_price

validity_days

included_services

description

status

created_at

updated_at

### APIs

GET /packages

POST /packages

PUT /packages/{id}

DELETE /packages/{id}

### Features

* Combo Packages
* Membership Packages
* Seasonal Offers

---

# Membership Management

## APIs

GET /memberships

POST /memberships

PUT /memberships/{id}

DELETE /memberships/{id}

### Features

* Gold Membership
* Platinum Membership
* VIP Membership
* Discount Benefits

---

# Staff Management

## Database Table

salon_staff

### Fields

id

staff_code

staff_name

designation

specialization

mobile

email

salary

joining_date

status

created_at

updated_at

### APIs

GET /staff

POST /staff

PUT /staff/{id}

DELETE /staff/{id}

---

# Staff Commission Management

## Database Table

salon_staff_commissions

### Fields

id

staff_id

appointment_id

service_amount

commission_percentage

commission_amount

payment_status

created_at

updated_at

### APIs

GET /commissions

POST /commissions

PUT /commissions/{id}

DELETE /commissions/{id}

### Features

* Service Based Commission
* Product Sales Commission
* Monthly Commission Reports

---

# Inventory Management

## Database Table

salon_inventory

### Fields

id

product_name

category

stock_quantity

minimum_stock

purchase_price

selling_price

supplier_id

created_at

updated_at

### APIs

GET /inventory

POST /inventory

PUT /inventory/{id}

DELETE /inventory/{id}

### Features

* Product Consumption Tracking
* Low Stock Alerts
* Inventory Reports

---

# Supplier Management

## APIs

GET /suppliers

POST /suppliers

PUT /suppliers/{id}

DELETE /suppliers/{id}

---

# Billing Management

## Database Table

salon_billing

### APIs

GET /billing

POST /billing

PUT /billing/{id}

DELETE /billing/{id}

### Features

* GST Billing
* Package Billing
* Product Billing
* PDF Invoices

---

# Payment Management

## Database Table

salon_payments

### APIs

GET /payments

POST /payments

PUT /payments/{id}

DELETE /payments/{id}

### Payment Methods

* Cash
* UPI
* Card
* Wallet
* Net Banking

---

# Loyalty Program Management

## APIs

GET /loyalty

POST /loyalty

PUT /loyalty/{id}

DELETE /loyalty/{id}

### Features

* Reward Points
* Cashback Offers
* Referral Benefits

---

# Reports Module

### APIs

GET /reports/appointments

GET /reports/revenue

GET /reports/services

GET /reports/staff

GET /reports/commissions

GET /reports/customers

GET /reports/inventory

GET /reports/packages

GET /reports/payments

GET /reports/gst

---

# Customer Portal

### Features

* Online Appointment Booking
* Membership Details
* Package Balance
* Payment History
* Reward Points

### APIs

GET /portal/dashboard

GET /portal/appointments

GET /portal/packages

GET /portal/payments

---

# Notification Module

### Channels

* Email
* SMS
* WhatsApp

### Features

* Appointment Reminders
* Birthday Wishes
* Package Expiry Alerts
* Membership Renewal Alerts
* Promotional Campaigns

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

/salon-management-api-docs

### Requirements

* OpenAPI 3.0
* JWT Authentication
* Bearer Token Support
* Request Examples
* Response Examples
* Try It Out Support

---

# Database Tables

wp_salon_customers

wp_salon_appointments

wp_salon_services

wp_salon_packages

wp_salon_memberships

wp_salon_staff

wp_salon_staff_commissions

wp_salon_inventory

wp_salon_suppliers

wp_salon_billing

wp_salon_payments

wp_salon_loyalty

wp_salon_documents

wp_salon_activity_logs

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

salon-management/

├── salon-management.php

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
4. Appointment APIs
5. Customer APIs
6. Service APIs
7. Package APIs
8. Staff APIs
9. Commission APIs
10. Inventory APIs
11. Billing APIs
12. Payment APIs
13. Dashboard APIs
14. Reports APIs
15. Customer Portal APIs
16. Swagger UI
17. OpenAPI Documentation
18. Validation Layer
19. Installation Guide
20. Postman Collection
21. Production Deployment Guide
22. Mobile App APIs

The code must be scalable, modular, secure, production-ready and follow WordPress Coding Standards and SOLID Principles.
