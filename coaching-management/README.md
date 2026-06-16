# Coaching Institute ERP API with Swagger UI

## Project Overview

Build a production-ready Coaching Institute ERP as a custom WordPress Plugin.

The system will be used by:

* Coaching Institutes
* Tuition Centers
* Competitive Exam Academies
* NEET Coaching Centers
* JEE Coaching Institutes
* UPSC Academies
* Spoken English Institutes
* Computer Training Centers
* Skill Development Institutes

The application must support complete coaching institute operations including:

* Student Management
* Batch Management
* Fees Management
* Attendance Tracking
* Test & Exam Management
* Lead Management
* WhatsApp Notifications
* Faculty Management
* Study Material Management
* Reports & Analytics

---

## Project Information

### Plugin Name

Coaching Institute ERP API

### Dashboard URL

https://domain.com/coaching-management/

### Swagger API URL

https://domain.com/coaching-management-api-docs/

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

* Full System Access
* Dashboard
* Reports
* User Management
* Settings

### Institute Manager

Permissions:

* Students
* Batches
* Fees
* Faculty
* Reports

### Faculty

Permissions:

* Attendance
* Tests
* Marks Entry
* Study Materials

### Counselor

Permissions:

* Lead Management
* Admissions
* Follow-Ups

### Accountant

Permissions:

* Fees Collection
* Refunds
* Financial Reports

### Student

Permissions:

* Attendance
* Test Results
* Study Materials
* Fee Receipts

### Parent

Permissions:

* Attendance Reports
* Fee Status
* Performance Reports

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

* Total Students
* Active Batches
* Today's Attendance
* Pending Fees
* Total Leads
* Admissions This Month
* Upcoming Tests
* Monthly Revenue

### Dashboard Analytics

* Admission Trends
* Fee Collection Analysis
* Student Attendance Trends
* Lead Conversion Rate
* Faculty Performance

---

# Student Management

## Database Table

coaching_students

### Fields

id

student_code

student_name

father_name

mother_name

mobile

email

course

batch_id

joining_date

status

created_at

updated_at

### APIs

GET /students

POST /students

PUT /students/{id}

DELETE /students/{id}

---

# Batch Management

## Database Table

coaching_batches

### Fields

id

batch_code

batch_name

course_name

faculty_id

start_date

end_date

timing

capacity

status

created_at

updated_at

### APIs

GET /batches

POST /batches

PUT /batches/{id}

DELETE /batches/{id}

---

# Course Management

## Database Table

coaching_courses

### APIs

GET /courses

POST /courses

PUT /courses/{id}

DELETE /courses/{id}

### Features

* Course Fees
* Course Duration
* Syllabus Management

---

# Lead Management

## Database Table

coaching_leads

### Fields

id

lead_name

mobile

email

course_interest

source

follow_up_date

status

created_at

updated_at

### Lead Status

* New
* Contacted
* Follow-Up
* Interested
* Admitted
* Rejected

### APIs

GET /leads

POST /leads

PUT /leads/{id}

DELETE /leads/{id}

---

# Admission Management

## APIs

GET /admissions

POST /admissions

PUT /admissions/{id}

DELETE /admissions/{id}

### Features

* Admission Workflow
* Student Registration
* Enrollment Management

---

# Faculty Management

## Database Table

coaching_faculty

### Fields

id

employee_code

faculty_name

mobile

email

specialization

joining_date

salary

status

created_at

updated_at

### APIs

GET /faculty

POST /faculty

PUT /faculty/{id}

DELETE /faculty/{id}

---

# Attendance Management

## Database Table

coaching_attendance

### APIs

GET /attendance

POST /attendance

PUT /attendance/{id}

DELETE /attendance/{id}

### Features

* Student Attendance
* Faculty Attendance
* Biometric Ready
* QR Attendance

---

# Fees Management

## Database Table

coaching_fees

### Fields

id

receipt_number

student_id

fee_type

amount

paid_amount

balance_amount

payment_date

status

created_at

updated_at

### APIs

GET /fees

POST /fees

PUT /fees/{id}

DELETE /fees/{id}

### Features

* Installment Plans
* Due Reminders
* Online Payments
* Fee Receipts

---

# Test & Examination Management

## Database Table

coaching_tests

### Fields

id

test_name

batch_id

test_date

total_marks

status

created_at

updated_at

### APIs

GET /tests

POST /tests

PUT /tests/{id}

DELETE /tests/{id}

---

# Marks Management

## Database Table

coaching_marks

### APIs

GET /marks

POST /marks

PUT /marks/{id}

DELETE /marks/{id}

### Features

* Subject Wise Marks
* Rank Generation
* Performance Reports

---

# Study Material Management

## Database Table

coaching_materials

### APIs

GET /materials

POST /materials

PUT /materials/{id}

DELETE /materials/{id}

### Features

* PDF Notes
* Videos
* Assignments
* Downloads

---

# Homework & Assignment Module

## APIs

GET /assignments

POST /assignments

PUT /assignments/{id}

DELETE /assignments/{id}

### Features

* Submission Tracking
* Faculty Review

---

# WhatsApp Notification Module

### Features

* Admission Confirmation
* Fee Due Reminders
* Attendance Alerts
* Test Schedule Notifications
* Result Announcements
* Parent Updates

### APIs

POST /notifications/whatsapp

GET /notifications/history

---

# Parent Portal

### Features

* Student Attendance
* Fee Status
* Marks Reports
* Homework Tracking

### APIs

GET /parent/dashboard

GET /parent/attendance

GET /parent/results

---

# Student Portal

### Features

* Attendance
* Results
* Study Materials
* Assignments
* Fee Receipts

### APIs

GET /student/dashboard

GET /student/results

GET /student/materials

---

# Reports Module

### APIs

GET /reports/admissions

GET /reports/fees

GET /reports/attendance

GET /reports/results

GET /reports/leads

GET /reports/faculty

GET /reports/revenue

GET /reports/student-performance

GET /reports/batch-performance

GET /reports/profit-loss

---

# Media Upload Module

### API

POST /media/upload

### Supported Files

* JPG
* PNG
* PDF
* DOCX
* XLSX
* MP4

### Maximum Size

50 MB

Store files in WordPress Media Library.

---

# Swagger Documentation

### URL

/coaching-management-api-docs

### Requirements

* OpenAPI 3.0
* JWT Authentication
* Bearer Token Support
* Request Examples
* Response Examples
* Try It Out Support

---

# Database Tables

wp_coaching_students

wp_coaching_batches

wp_coaching_courses

wp_coaching_leads

wp_coaching_admissions

wp_coaching_faculty

wp_coaching_attendance

wp_coaching_fees

wp_coaching_tests

wp_coaching_marks

wp_coaching_materials

wp_coaching_assignments

wp_coaching_notifications

wp_coaching_activity_logs

wp_coaching_documents

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

# Deliverables

1. WordPress Plugin
2. Database Migrations
3. JWT Authentication
4. Student APIs
5. Batch APIs
6. Attendance APIs
7. Fees APIs
8. Lead APIs
9. Test APIs
10. Marks APIs
11. WhatsApp APIs
12. Dashboard APIs
13. Reports APIs
14. Parent Portal APIs
15. Student Portal APIs
16. Swagger UI
17. OpenAPI Documentation
18. Validation Layer
19. Installation Guide
20. Postman Collection
21. Mobile Student App APIs
22. Online Learning APIs

The code must be scalable, modular, secure, production-ready and follow WordPress Coding Standards and SOLID Principles.
