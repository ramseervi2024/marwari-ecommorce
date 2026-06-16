# Hotel ERP API with Swagger UI

## Project Overview

Build a production-ready Hotel ERP as a custom WordPress Plugin.

The system will be used by:

* Hotels
* Resorts
* Lodges
* Service Apartments
* Guest Houses
* Boutique Hotels
* Business Hotels
* Hotel Chains
* Holiday Resorts

The application must support complete hotel operations including:

* Room Booking
* Reservation Management
* Check-In / Check-Out
* Housekeeping
* Guest Management
* Restaurant Management
* Billing & Invoicing
* Payment Management
* Staff Management
* Reports & Analytics

---

## Project Information

### Plugin Name

Hotel ERP API

### Dashboard URL

https://domain.com/hotel-management/

### Swagger API URL

https://domain.com/hotel-management-api-docs/

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

### Hotel Manager

Permissions:

* Reservations
* Guest Management
* Billing
* Reports

### Receptionist

Permissions:

* Check-In
* Check-Out
* Room Booking
* Guest Registration

### Housekeeping Staff

Permissions:

* Room Cleaning
* Maintenance Requests
* Room Status Updates

### Restaurant Manager

Permissions:

* Restaurant Orders
* Billing
* Inventory

### Accountant

Permissions:

* Payments
* Invoices
* GST Reports

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

* Total Rooms
* Occupied Rooms
* Available Rooms
* Today's Check-Ins
* Today's Check-Outs
* Restaurant Sales
* Pending Payments
* Monthly Revenue

### Dashboard Analytics

* Occupancy Rate
* Revenue Trends
* Booking Sources
* Room Performance
* Guest Retention

---

# Room Management

## Database Table

hotel_rooms

### Fields

id

room_number

room_type

floor

capacity

price_per_night

status

description

created_at

updated_at

### Room Status

* Available
* Occupied
* Reserved
* Maintenance
* Cleaning

### APIs

GET /rooms

POST /rooms

PUT /rooms/{id}

DELETE /rooms/{id}

---

# Room Type Management

## APIs

GET /room-types

POST /room-types

PUT /room-types/{id}

DELETE /room-types/{id}

### Examples

* Standard Room
* Deluxe Room
* Executive Room
* Suite Room
* Family Room

---

# Reservation Management

## Database Table

hotel_reservations

### Fields

id

reservation_number

guest_id

room_id

check_in_date

check_out_date

total_amount

booking_source

status

created_at

updated_at

### Status

* Pending
* Confirmed
* Checked-In
* Checked-Out
* Cancelled

### APIs

GET /reservations

POST /reservations

PUT /reservations/{id}

DELETE /reservations/{id}

---

# Guest Management

## Database Table

hotel_guests

### Fields

id

guest_code

guest_name

mobile

email

address

city

state

country

id_proof_type

id_proof_number

created_at

updated_at

### APIs

GET /guests

POST /guests

PUT /guests/{id}

DELETE /guests/{id}

### Features

* Guest History
* Repeat Guest Tracking
* Document Upload

---

# Check-In / Check-Out Module

## APIs

POST /check-in

POST /check-out

GET /checkin-history

### Features

* Digital Registration
* Room Allocation
* Guest Verification

---

# Housekeeping Management

## Database Table

hotel_housekeeping

### Fields

id

room_id

assigned_to

cleaning_date

status

remarks

created_at

updated_at

### Status

* Pending
* In Progress
* Completed

### APIs

GET /housekeeping

POST /housekeeping

PUT /housekeeping/{id}

DELETE /housekeeping/{id}

---

# Maintenance Management

## APIs

GET /maintenance

POST /maintenance

PUT /maintenance/{id}

DELETE /maintenance/{id}

### Features

* Room Repairs
* Asset Tracking
* Maintenance History

---

# Restaurant Management

## Database Table

hotel_restaurant_orders

### Fields

id

order_number

guest_id

room_id

table_number

order_date

total_amount

status

created_at

updated_at

### APIs

GET /restaurant/orders

POST /restaurant/orders

PUT /restaurant/orders/{id}

DELETE /restaurant/orders/{id}

### Features

* Room Service Orders
* Restaurant Billing
* Kitchen Order Tracking

---

# Menu Management

## APIs

GET /restaurant/menu

POST /restaurant/menu

PUT /restaurant/menu/{id}

DELETE /restaurant/menu/{id}

---

# Billing Management

## Database Table

hotel_billing

### APIs

GET /billing

POST /billing

PUT /billing/{id}

DELETE /billing/{id}

### Features

* Room Charges
* Restaurant Charges
* Laundry Charges
* GST Billing
* PDF Invoice

---

# Payment Management

## Database Table

hotel_payments

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

# Laundry Management

## APIs

GET /laundry

POST /laundry

PUT /laundry/{id}

DELETE /laundry/{id}

### Features

* Guest Laundry Orders
* Billing Integration

---

# Staff Management

## APIs

GET /staff

POST /staff

PUT /staff/{id}

DELETE /staff/{id}

---

# Reports Module

### APIs

GET /reports/bookings

GET /reports/occupancy

GET /reports/revenue

GET /reports/guests

GET /reports/housekeeping

GET /reports/restaurant

GET /reports/payments

GET /reports/gst

GET /reports/staff

GET /reports/profit-loss

---

# Guest Portal

### Features

* Online Booking
* Booking History
* Invoice Download
* Service Requests
* Restaurant Orders

### APIs

GET /portal/dashboard

GET /portal/bookings

GET /portal/invoices

---

# Notification Module

### Channels

* Email
* SMS
* WhatsApp

### Features

* Booking Confirmation
* Check-In Reminder
* Payment Reminder
* Guest Feedback Request

---

# Media Upload Module

### API

POST /media/upload

### Supported Files

* JPG
* PNG
* PDF
* XLSX

### Maximum Size

20 MB

Store files in WordPress Media Library.

---

# Swagger Documentation

### URL

/hotel-management-api-docs

### Requirements

* OpenAPI 3.0
* JWT Authentication
* Bearer Token Support
* Request Examples
* Response Examples
* Try It Out Support

---

# Database Tables

wp_hotel_rooms

wp_hotel_room_types

wp_hotel_reservations

wp_hotel_guests

wp_hotel_housekeeping

wp_hotel_maintenance

wp_hotel_restaurant_orders

wp_hotel_menu

wp_hotel_billing

wp_hotel_payments

wp_hotel_laundry

wp_hotel_staff

wp_hotel_documents

wp_hotel_activity_logs

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

hotel-management/

├── hotel-management.php

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
4. Room Management APIs
5. Reservation APIs
6. Guest APIs
7. Housekeeping APIs
8. Restaurant APIs
9. Billing APIs
10. Payment APIs
11. Reports APIs
12. Dashboard APIs
13. Guest Portal APIs
14. Swagger UI
15. OpenAPI Documentation
16. Validation Layer
17. Installation Guide
18. Postman Collection
19. Production Deployment Guide
20. Mobile App APIs

The code must be scalable, modular, secure, production-ready and follow WordPress Coding Standards and SOLID Principles.
