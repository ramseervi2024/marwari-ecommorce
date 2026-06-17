# Service Business ERP - System Overview

An enterprise-grade, decoupled, Object-Oriented Programming (OOP) based WordPress plugin that exposes REST API endpoints for a complete Service Business ecosystem. It features role-based access control, lead tracking, quotation generators, service scheduling & dispatch jobs, technician workloads, Annual Maintenance Contracts (AMC), billing invoices, payments logs, and SMTP configuration integrations.

---

## 1. System Role Credentials & Capability Matrix

The database migrations provision default testing accounts with `123456` password credentials. Role definitions restrict or allow specific modular actions as detailed below:

| Username | System Role | Display Name | Permissions/Capabilities |
| :--- | :--- | :--- | :--- |
| **`ssuperadmin`** | `service_super_admin` | Service Super Admin | Full administrative control of leads, quotes, jobs, AMCs, invoices, payments, settings, and users |
| **`smanager`** | `service_manager` | John Service Manager | Manage leads, quotations, jobs, AMC contracts, invoices, and payments |
| **`stechnician`** | `service_technician` | Ravi Technician | Restricted access to view assigned jobs and update job execution statuses & work notes |
| **`scustomercare`** | `service_customer_care` | Neelam Customer Care | Manage incoming customer leads and register AMC contracts |
| **`saccountant`** | `service_accountant` | Aakash Accountant | Manage customer quotes, billing invoices, payment logs, and financial reports |

---

## 2. Core Custom Database Tables

Eight custom tables prefixed with `wp_ser_` are automatically created on plugin activation:

1. **`wp_ser_leads`**: Customer leads repository tracking contact details, statuses, and requirements.
2. **`wp_ser_quotations`**: Quotation documents listing totals, quotation dates, and status info.
3. **`wp_ser_quotation_items`**: Service line-items inside quotations (descriptions, prices, and quantities).
4. **`wp_ser_jobs`**: Job schedule sheets tracking tasks, address location details, dispatch technician ID, and field notes.
5. **`wp_ser_amc`**: Annual Maintenance Contracts logs tracking duration dates, pricing values, and status.
6. **`wp_ser_invoices`**: Billing documents generated from finished jobs or AMC contracts.
7. **`wp_ser_payments`**: Log of payments received (auto-updates Invoice status to "Paid" or "Partially Paid").
8. **`wp_ser_activity_logs`**: System audit logs auditing operations and SMTP mail errors.

---

## 3. REST API Routes Reference

All API routes require authentication headers: `Authorization: Bearer <JWT_TOKEN>`.

### Authentication & Users
* `POST /auth/register` (Initiate user registration)
* `POST /auth/register/verify` (Verify registration code via OTP)
* `POST /auth/login/initiate` (Passwordless login OTP generation)
* `POST /auth/login` (Authenticate credentials or OTP to get JWT)
* `GET /auth/me` (Retrieve active profile details)
* `POST /auth/logout` (Invalidate active refresh tokens)
* `GET /auth/users` (List employees)
* `POST /auth/users/status` (Update approval/blocked user status)
* `DELETE /auth/users/{id}` (Delete employee account)

### Leads Management
* `GET`, `POST` `/leads` (List / Create customer lead)
* `GET`, `PUT`, `DELETE` `/leads/{id}` (Read / Update / Delete lead)

### Quotations & Estimates
* `GET`, `POST` `/quotations` (List / Create quotation with line items)
* `GET`, `PUT`, `DELETE` `/quotations/{id}` (Read / Update / Delete quotation)

### Job Scheduling & Dispatch
* `GET`, `POST` `/jobs` (List / Create scheduling dispatch job)
* `GET`, `PUT`, `DELETE` `/jobs/{id}` (Read / Update / Delete job)
  * *Note: Technicians can only access their assigned jobs and update Status and Work Notes.*

### AMC Contract Registry
* `GET`, `POST` `/amc` (List / Create AMC contract)
* `GET`, `PUT`, `DELETE` `/amc/{id}` (Read / Update / Delete contract)

### Billing & Payments
* `GET`, `POST` `/invoices` (List / Create invoice, completing linked jobs)
* `GET`, `PUT`, `DELETE` `/invoices/{id}` (Read / Update / Delete invoice)
* `GET`, `POST` `/payments` (List / Create payment logs)
* `GET`, `DELETE` `/payments/{id}` (Read / Delete payment logs)

### SMTP & System Settings
* `GET`, `POST` `/auth/smtp` (Read / Save system SMTP configuration)
* `POST` `/auth/smtp/test` (Send a test connectivity email)
* `GET` `/dashboard` (Retrieve general statistics KPIs, tech workloads, and analytics)

---

## 4. UI Dashboard & Swagger Playground URLs

After activation, the plugin handles rewrite routes for client execution:

* **Interactive Client Dashboard View**: `http://<your-wordpress-site>/service-management/`
  * Fully custom glassmorphic layout.
  * Preserves active tab selection on page reload (`localStorage.setItem('ser_active_tab')`).
  * Features a persistent dual Light Mode/Dark Mode theme switcher (Light Mode by default).
  * Employs inline head-checking script to prevent unauthenticated layout flash (FOUC).
  
* **Swagger OpenAPI Documentation Playground**: `http://<your-wordpress-site>/service-management-api-docs/`
  * Complete API documentation UI that lets you execute and test every single endpoint live.
