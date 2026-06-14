# FleetTrack Pro - Fleet Management System API with Swagger UI

## Project Overview

Create a custom WordPress plugin called **FleetTrack Pro API**.

The plugin should provide a complete Fleet Management System for transport, logistics, delivery, and trucking businesses.

The system will be used as a Headless CMS backend for React, Angular, React Native, and Mobile Applications.

The application must allow administrators to manage:

* Vehicles
* Drivers
* Routes
* Trips
* Fuel Expenses
* Maintenance Expenses
* Toll Expenses
* Driver Salaries
* Vehicle Documents
* Profit & Loss Reports
* Dashboard Analytics

The system must automatically calculate operational costs and profitability.

---

# Technology Stack

* WordPress Latest Version
* PHP 8+
* MySQL
* WordPress REST API
* JWT Authentication
* Swagger UI (OpenAPI 3.0)
* PSR-4 Structure
* OOP Architecture
* Repository Pattern
* Service Layer Pattern

---

# Plugin Name

FleetTrack Pro API

---

# Authentication Module

Implement JWT Authentication.

## APIs

POST /auth/register

POST /auth/login

POST /auth/logout

POST /auth/refresh-token

GET /auth/me

Requirements:

* JWT Token Authentication
* Password Hashing
* Role-Based Authorization
* Refresh Tokens
* Login Activity Logs

---

# User Roles

## Super Admin

Can:

* Manage Everything
* Dashboard Access
* User Management
* Vehicle Management
* Driver Management
* Route Management
* Expense Management
* Reports
* Export Data

## Fleet Manager

Can:

* Manage Vehicles
* Manage Drivers
* Manage Trips
* Manage Expenses
* View Reports

## Accountant

Can:

* View Vehicles
* View Drivers
* Manage Expenses
* View Financial Reports

## Driver

Can:

* View Assigned Trips
* Update Trip Status
* Upload Documents

---

# Vehicle Module

## Vehicle Fields

id

vehicle_number

vehicle_type

vehicle_brand

vehicle_model

vehicle_year

fuel_type

capacity

insurance_expiry

fitness_expiry

permit_expiry

status

created_at

updated_at

## Vehicle APIs

GET /vehicles

GET /vehicles/{id}

POST /vehicles

PUT /vehicles/{id}

DELETE /vehicles/{id}

---

# Driver Module

## Driver Fields

id

name

phone

email

license_number

license_expiry

salary

joining_date

status

created_at

updated_at

## Driver APIs

GET /drivers

GET /drivers/{id}

POST /drivers

PUT /drivers/{id}

DELETE /drivers/{id}

---

# Route Module

## Route Fields

id

route_name

source

destination

distance_km

estimated_time

status

created_at

updated_at

## Route APIs

GET /routes

GET /routes/{id}

POST /routes

PUT /routes/{id}

DELETE /routes/{id}

---

# Trip Module

## Trip Fields

id

vehicle_id

driver_id

route_id

trip_date

start_km

end_km

distance_travelled

revenue

status

created_at

updated_at

## Trip APIs

GET /trips

GET /trips/{id}

POST /trips

PUT /trips/{id}

DELETE /trips/{id}

---

# Expense Management Module

## Expense Types

Fuel

Maintenance

Toll

Tyre

Insurance

Permit

Salary

Repair

Parking

Miscellaneous

## Expense Fields

id

vehicle_id

driver_id

trip_id

expense_type

amount

expense_date

description

created_at

updated_at

---

## Expense APIs

GET /expenses

GET /expenses/{id}

POST /expenses

PUT /expenses/{id}

DELETE /expenses/{id}

---

# Fuel Management

## Fuel Fields

id

vehicle_id

trip_id

fuel_quantity

fuel_cost

fuel_price_per_liter

fuel_station

fuel_date

created_at

updated_at

## APIs

GET /fuel

POST /fuel

PUT /fuel/{id}

DELETE /fuel/{id}

---

# Automatic Cost Calculations

System should automatically calculate:

## Vehicle Monthly Expense

Fuel Cost

* Maintenance Cost

* Insurance Cost

* Toll Cost

* Permit Cost

* Salary Cost

* Other Expenses

= Total Expense

---

## Vehicle Profit Calculation

Trip Revenue

* Total Expenses

= Net Profit

---

## Cost Per Kilometer

Total Expenses

/ Total Distance Travelled

= Cost Per KM

---

## Driver Performance

Total Trips

Total Distance

Total Revenue

Average Revenue Per Trip

Expense Ratio

---

# Dashboard Module

GET /dashboard

Dashboard Cards:

* Total Vehicles
* Active Vehicles
* Total Drivers
* Active Drivers
* Total Trips
* Total Revenue
* Total Expenses
* Total Profit

---

# Dashboard Analytics

Revenue Trend

Expense Trend

Fuel Consumption Trend

Vehicle Utilization

Driver Performance

Top Profitable Vehicles

Top Expense Vehicles

Monthly Profit Loss

---

# Reports Module

## Financial Reports

GET /reports/profit-loss

GET /reports/revenue

GET /reports/expenses

GET /reports/fuel

GET /reports/vehicle

GET /reports/driver

GET /reports/trips

---

# Document Management

Vehicle Documents:

* RC
* Insurance
* Permit
* Fitness Certificate
* Pollution Certificate

Driver Documents:

* Driving License
* Aadhaar
* PAN
* Medical Certificate

---

# Media Upload Module

POST /media/upload

Supported Files:

* JPG
* JPEG
* PNG
* WEBP
* PDF

Maximum Upload Size:

20 MB

Store documents in WordPress Media Library.

---

# Swagger Documentation

OpenAPI 3.0

URL:

/fleettrack-api-docs

Requirements:

* JWT Authentication Support
* Request Examples
* Response Examples
* Try It Out
* API Testing
* File Upload Testing

---

# Database

Create tables automatically during plugin activation.

Tables:

wp_fleet_vehicles

wp_fleet_drivers

wp_fleet_routes

wp_fleet_trips

wp_fleet_expenses

wp_fleet_fuel

wp_fleet_documents

wp_fleet_activity_logs

---

# Security

Implement:

* JWT Authentication
* Input Validation
* SQL Injection Protection
* XSS Protection
* CSRF Protection
* Sanitization
* Prepared Statements
* Role Permissions

---

# API Response Format

Success

{
"success": true,
"message": "Operation successful",
"data": {}
}

Error

{
"success": false,
"message": "Validation failed",
"errors": []
}

---

# React/Angular Compatibility

Frontend Support:

* React
* Angular
* React Native
* Flutter

Enable:

* CORS
* JWT Authentication
* Refresh Tokens

---

# Additional Features

* Export CSV
* Import CSV
* Audit Logs
* Activity Tracking
* Pagination
* Search
* Filters
* Global Error Handler
* Email Notifications
* Vehicle Expiry Alerts
* Insurance Expiry Alerts
* License Expiry Alerts
* Maintenance Reminders

---

# Deliverables

Generate complete production-ready code including:

1. WordPress Plugin
2. Database Migrations
3. JWT Authentication
4. Vehicle Management APIs
5. Driver Management APIs
6. Route Management APIs
7. Trip Management APIs
8. Expense Management APIs
9. Fuel Management APIs
10. Dashboard APIs
11. Analytics APIs
12. Swagger UI
13. OpenAPI Documentation
14. File Upload APIs
15. Reports APIs
16. Middleware
17. Validation Layer
18. Role Management
19. Installation Guide
20. Sample Postman Collection

Code should be enterprise-grade, scalable, production-ready, and follow WordPress coding standards.
