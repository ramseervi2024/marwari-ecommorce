# CRM ERP API with Swagger UI

## Project Overview

Build a production-ready CRM ERP as a custom WordPress Plugin.

The system will be used by:

* IT Companies
* Digital Marketing Agencies
* Real Estate Companies
* Manufacturing Companies
* Service Businesses
* Consultants
* B2B Sales Teams
* SaaS Companies
* Startups
* Enterprises

The application must support complete customer relationship management operations including:

* Lead Management
* Follow-Up Management
* Sales Pipeline
* Quotation Management
* Customer Management
* Deal Management
* Task Management
* WhatsApp Reminders
* Email Tracking
* Reports & Analytics

---

## Project Information

### Plugin Name

CRM ERP API

### Dashboard URL

https://domain.com/crm-management/

### Swagger API URL

https://domain.com/crm-management-api-docs/

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

### Sales Manager

Permissions:

* Leads
* Quotations
* Pipeline
* Reports
* Team Management

### Sales Executive

Permissions:

* Leads
* Follow-Ups
* Tasks
* Quotations

### Telecaller

Permissions:

* Lead Calling
* Follow-Ups
* Notes

### Customer

Permissions:

* View Quotations
* View Invoices
* Communication History

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

* Total Leads
* New Leads
* Follow-Ups Today
* Quotations Sent
* Deals Won
* Deals Lost
* Monthly Revenue
* Sales Target Achievement

### Dashboard Analytics

* Lead Conversion Rate
* Sales Funnel
* Revenue Trends
* Team Performance
* Lead Sources Analysis

---

# Lead Management

## Database Table

crm_leads

### Fields

id

lead_number

first_name

last_name

company_name

mobile

email

website

lead_source

industry

city

state

assigned_to

lead_status

remarks

created_at

updated_at

### Lead Sources

* Website
* Facebook
* Google Ads
* LinkedIn
* Referral
* WhatsApp
* Walk-In
* Cold Calling

### Lead Status

* New
* Contacted
* Interested
* Follow-Up
* Quotation Sent
* Negotiation
* Won
* Lost

### APIs

GET /leads

GET /leads/{id}

POST /leads

PUT /leads/{id}

DELETE /leads/{id}

---

# Follow-Up Management

## Database Table

crm_followups

### Fields

id

lead_id

followup_date

followup_time

communication_type

remarks

next_followup_date

status

created_at

updated_at

### Communication Types

* Call
* WhatsApp
* Email
* Meeting
* SMS

### APIs

GET /followups

POST /followups

PUT /followups/{id}

DELETE /followups/{id}

### Features

* Follow-Up Calendar
* Automated Reminders
* Follow-Up History

---

# Task Management

## APIs

GET /tasks

POST /tasks

PUT /tasks/{id}

DELETE /tasks/{id}

### Features

* Task Assignment
* Due Date Tracking
* Team Tasks

---

# Quotation Management

## Database Table

crm_quotations

### Fields

id

quotation_number

lead_id

quotation_date

valid_until

subtotal

discount

tax_amount

grand_total

status

created_at

updated_at

### Status

* Draft
* Sent
* Accepted
* Rejected
* Expired

### APIs

GET /quotations

GET /quotations/{id}

POST /quotations

PUT /quotations/{id}

DELETE /quotations/{id}

### Features

* PDF Quotation
* Email Quotation
* WhatsApp Quotation
* Digital Approval

---

# Customer Management

## Database Table

crm_customers

### Fields

id

customer_code

company_name

contact_person

mobile

email

gst_number

address

city

state

status

created_at

updated_at

### APIs

GET /customers

POST /customers

PUT /customers/{id}

DELETE /customers/{id}

---

# Deal Management

## Database Table

crm_deals

### Fields

id

deal_number

lead_id

customer_id

deal_value

expected_close_date

deal_stage

probability

assigned_to

created_at

updated_at

### Deal Stages

* Prospecting
* Qualification
* Proposal
* Negotiation
* Won
* Lost

### APIs

GET /deals

POST /deals

PUT /deals/{id}

DELETE /deals/{id}

---

# Sales Pipeline Management

## API

GET /pipeline

POST /pipeline

PUT /pipeline/{id}

### Features

* Kanban Board
* Drag & Drop Pipeline
* Stage Tracking
* Conversion Analysis

### Pipeline Stages

* Lead
* Qualified
* Meeting Scheduled
* Proposal Sent
* Negotiation
* Closed Won
* Closed Lost

---

# WhatsApp Integration Module

## APIs

POST /whatsapp/send

POST /whatsapp/template

GET /whatsapp/history

### Features

* Lead Follow-Up Reminders
* Quotation Sharing
* Automated Messages
* Bulk Messaging
* Template Messages

---

# Email Marketing Module

## APIs

POST /email/send

POST /email/campaign

GET /email/history

### Features

* Lead Nurturing
* Campaign Tracking
* Open Rate Tracking
* Click Tracking

---

# Call Log Management

## APIs

GET /call-logs

POST /call-logs

PUT /call-logs/{id}

DELETE /call-logs/{id}

### Features

* Call Recording Reference
* Call Notes
* Duration Tracking

---

# Meeting Management

## APIs

GET /meetings

POST /meetings

PUT /meetings/{id}

DELETE /meetings/{id}

### Features

* Meeting Scheduling
* Calendar Integration
* Meeting Notes

---

# Invoice Management

## APIs

GET /invoices

POST /invoices

PUT /invoices/{id}

DELETE /invoices/{id}

### Features

* GST Invoices
* Payment Tracking

---

# Payment Tracking

## APIs

GET /payments

POST /payments

PUT /payments/{id}

DELETE /payments/{id}

---

# Reports Module

### APIs

GET /reports/leads

GET /reports/followups

GET /reports/quotations

GET /reports/deals

GET /reports/pipeline

GET /reports/revenue

GET /reports/team-performance

GET /reports/lead-sources

GET /reports/conversion-rate

GET /reports/forecast

---

# Customer Portal

### Features

* View Quotations
* View Invoices
* Approve Quotations
* Download Documents

### APIs

GET /portal/dashboard

GET /portal/quotations

GET /portal/invoices

---

# Notification Module

### Channels

* Email
* SMS
* WhatsApp
* Push Notifications

### Features

* Follow-Up Reminders
* Task Reminders
* Deal Updates
* Quotation Alerts
* Revenue Notifications

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

### Maximum Size

20 MB

Store files in WordPress Media Library.

---

# Swagger Documentation

### URL

/crm-management-api-docs

### Requirements

* OpenAPI 3.0
* JWT Authentication
* Bearer Token Support
* Request Examples
* Response Examples
* Try It Out Support

---

# Database Tables

wp_crm_leads

wp_crm_followups

wp_crm_tasks

wp_crm_quotations

wp_crm_customers

wp_crm_deals

wp_crm_pipeline

wp_crm_call_logs

wp_crm_meetings

wp_crm_invoices

wp_crm_payments

wp_crm_whatsapp_logs

wp_crm_email_logs

wp_crm_documents

wp_crm_activity_logs

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

crm-management/

├── crm-management.php

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
4. Lead APIs
5. Follow-Up APIs
6. Quotation APIs
7. Deal APIs
8. Sales Pipeline APIs
9. WhatsApp APIs
10. Customer APIs
11. Reports APIs
12. Dashboard APIs
13. Customer Portal APIs
14. Swagger UI
15. OpenAPI Documentation
16. Validation Layer
17. Installation Guide
18. Postman Collection
19. Production Deployment Guide
20. Mobile App APIs

The code must be scalable, modular, secure, production-ready and follow WordPress Coding Standards and SOLID Principles.
