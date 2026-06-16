# Gym & Fitness ERP API with Swagger UI

## Project Overview

Build a production-ready Gym & Fitness ERP as a custom WordPress Plugin.

The system will be used by:

* Gyms
* Fitness Centers
* CrossFit Centers
* Yoga Studios
* Zumba Classes
* Personal Training Centers
* Fitness Franchises
* Health Clubs
* Sports Academies

The application must support complete fitness center operations including:

* Member Management
* Membership Plans
* Membership Renewals
* Trainer Management
* Attendance Tracking
* Diet Plans
* Workout Plans
* Payments
* Billing
* Reports & Analytics

---

## Project Information

### Plugin Name

Gym Fitness ERP API

### Dashboard URL

https://domain.com/gym-management/

### Swagger API URL

https://domain.com/gym-management-api-docs/

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

### Gym Manager

Permissions:

* Members
* Trainers
* Membership Plans
* Renewals
* Payments

### Trainer

Permissions:

* Assigned Members
* Attendance
* Workout Plans
* Diet Plans
* Progress Tracking

### Receptionist

Permissions:

* New Registrations
* Membership Renewals
* Attendance
* Payments

### Member

Permissions:

* View Membership
* Attendance History
* Diet Plans
* Workout Plans
* Payments

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

* Total Members
* Active Memberships
* Expiring Memberships
* Today's Attendance
* Total Trainers
* Monthly Revenue
* Pending Payments
* New Registrations

### Dashboard Analytics

* Membership Growth
* Attendance Trends
* Revenue Trends
* Trainer Performance
* Member Retention Rate

---

# Member Management

## Database Table

gym_members

### Fields

id

member_code

full_name

gender

date_of_birth

mobile

email

address

height

weight

blood_group

fitness_goal

joining_date

status

created_at

updated_at

### APIs

GET /members

GET /members/{id}

POST /members

PUT /members/{id}

DELETE /members/{id}

---

# Membership Plan Management

## Database Table

gym_membership_plans

### Fields

id

plan_name

duration

price

description

status

created_at

updated_at

### Examples

* Monthly Plan
* Quarterly Plan
* Half-Yearly Plan
* Annual Plan

### APIs

GET /membership-plans

POST /membership-plans

PUT /membership-plans/{id}

DELETE /membership-plans/{id}

---

# Membership Management

## Database Table

gym_memberships

### Fields

id

member_id

plan_id

start_date

end_date

amount

payment_status

membership_status

created_at

updated_at

### Status

* Active
* Expired
* Suspended
* Cancelled

### APIs

GET /memberships

POST /memberships

PUT /memberships/{id}

DELETE /memberships/{id}

---

# Membership Renewal Module

## APIs

GET /renewals

POST /renewals

PUT /renewals/{id}

DELETE /renewals/{id}

### Features

* Auto Renewal Reminders
* Expiry Alerts
* Renewal History

---

# Trainer Management

## Database Table

gym_trainers

### Fields

id

trainer_code

trainer_name

specialization

mobile

email

experience

salary

status

created_at

updated_at

### APIs

GET /trainers

POST /trainers

PUT /trainers/{id}

DELETE /trainers/{id}

---

# Member Trainer Assignment

## APIs

GET /trainer-assignments

POST /trainer-assignments

PUT /trainer-assignments/{id}

DELETE /trainer-assignments/{id}

---

# Attendance Management

## Database Table

gym_attendance

### Fields

id

member_id

attendance_date

check_in_time

check_out_time

created_at

updated_at

### APIs

GET /attendance

POST /attendance

PUT /attendance/{id}

DELETE /attendance/{id}

### Features

* QR Attendance
* RFID Attendance
* Biometric Integration
* Mobile Check-In

---

# Workout Plan Management

## Database Table

gym_workout_plans

### Fields

id

member_id

trainer_id

workout_type

exercise_details

duration

remarks

created_at

updated_at

### APIs

GET /workout-plans

POST /workout-plans

PUT /workout-plans/{id}

DELETE /workout-plans/{id}

---

# Diet Plan Management

## Database Table

gym_diet_plans

### Fields

id

member_id

trainer_id

diet_goal

meal_plan

calories

remarks

created_at

updated_at

### APIs

GET /diet-plans

POST /diet-plans

PUT /diet-plans/{id}

DELETE /diet-plans/{id}

---

# Body Measurement Tracking

## Database Table

gym_measurements

### Fields

id

member_id

weight

height

body_fat_percentage

chest

waist

biceps

measurement_date

created_at

updated_at

### APIs

GET /measurements

POST /measurements

PUT /measurements/{id}

DELETE /measurements/{id}

### Features

* Progress Tracking
* Fitness Goals
* Weight Charts

---

# Payment Management

## Database Table

gym_payments

### Fields

id

member_id

membership_id

payment_date

amount

payment_method

transaction_reference

status

created_at

updated_at

### Payment Methods

* Cash
* UPI
* Card
* Net Banking

### APIs

GET /payments

POST /payments

PUT /payments/{id}

DELETE /payments/{id}

---

# Invoice Management

## APIs

GET /invoices

POST /invoices

PUT /invoices/{id}

DELETE /invoices/{id}

### Features

* GST Invoice
* PDF Invoice
* Email Invoice

---

# Supplement Sales Module

## APIs

GET /supplements

POST /supplements

PUT /supplements/{id}

DELETE /supplements/{id}

### Features

* Protein Sales
* Supplement Inventory
* Sales Tracking

---

# Class Management

## APIs

GET /classes

POST /classes

PUT /classes/{id}

DELETE /classes/{id}

### Features

* Yoga Classes
* Zumba Classes
* Group Training
* Fitness Sessions

---

# Reports Module

### APIs

GET /reports/members

GET /reports/attendance

GET /reports/revenue

GET /reports/renewals

GET /reports/trainers

GET /reports/payments

GET /reports/fitness-progress

GET /reports/class-attendance

GET /reports/supplement-sales

GET /reports/profit-loss

---

# Member Portal

### Features

* Membership Details
* Attendance History
* Workout Plans
* Diet Plans
* Progress Reports
* Payment History

### APIs

GET /portal/dashboard

GET /portal/workouts

GET /portal/diets

GET /portal/payments

---

# Notification Module

### Channels

* Email
* SMS
* WhatsApp
* Push Notifications

### Features

* Membership Expiry Alerts
* Renewal Reminders
* Workout Notifications
* Payment Reminders
* Birthday Wishes

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

/gym-management-api-docs

### Requirements

* OpenAPI 3.0
* JWT Authentication
* Bearer Token Support
* Request Examples
* Response Examples
* Try It Out Support

---

# Database Tables

wp_gym_members

wp_gym_membership_plans

wp_gym_memberships

wp_gym_renewals

wp_gym_trainers

wp_gym_attendance

wp_gym_workout_plans

wp_gym_diet_plans

wp_gym_measurements

wp_gym_payments

wp_gym_invoices

wp_gym_classes

wp_gym_supplements

wp_gym_activity_logs

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

gym-management/

├── gym-management.php

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
4. Member APIs
5. Membership APIs
6. Trainer APIs
7. Attendance APIs
8. Workout APIs
9. Diet Plan APIs
10. Payment APIs
11. Reports APIs
12. Dashboard APIs
13. Member Portal APIs
14. Swagger UI
15. OpenAPI Documentation
16. Validation Layer
17. Installation Guide
18. Postman Collection
19. Production Deployment Guide
20. Mobile App APIs

The code must be scalable, modular, secure, production-ready and follow WordPress Coding Standards and SOLID Principles.
