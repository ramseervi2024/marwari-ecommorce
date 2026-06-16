# Technical Overview: Hospital & Clinic ERP API Plugin

Welcome to the **Hospital & Clinic ERP API** plugin. This document provides a high-level developer and administrator guide to understand the system design, components, database schemas, authentication mechanisms, and UI interfaces.

---

## 1. Architectural Blueprint
The plugin is built with a highly decoupled, modern Object-Oriented Programming (OOP) PHP design structure adhering to WordPress standards. It avoids mixing DB queries, REST endpoint registration, and business logic by using a strict **Controller-Repository-Service** pattern:

```
                  ┌──────────────────────┐
                  │    WordPress REST    │
                  │  Routing Register    │
                  └──────────┬───────────┘
                             │
                             ▼
                  ┌──────────────────────┐
                  │   Auth / Role Check  │
                  │      Middleware      │
                  └──────────┬───────────┘
                             │
                             ▼
                  ┌──────────────────────┐
                  │   REST Controller    │
                  │   Request Handlers   │
                  └──────────┬───────────┘
                             │
            ┌────────────────┴────────────────┐
            ▼                                 ▼
┌──────────────────────┐           ┌──────────────────────┐
│  Services (Business  │           │   Data Repositories  │
│  Logic, JWT, OTP)    │           │   (SQL Abstractions) │
└──────────────────────┘           └──────────┬───────────┘
                                              │
                                              ▼
                                   ┌──────────────────────┐
                                   │  Custom DB Schema    │
                                   │  (13 custom tables)  │
                                   └──────────────────────┘
```

- **Autoloader**: Dynamically maps classes in the `HospitalManagementApi` namespace to physical source files using a custom PSR-4 autoloader in [hospital-management-api.php](file:///Users/rameshseervi/Desktop/wordpress-erp-plugin/hospital-management/hospital-management-api.php).
- **Repositories**: Standardize DB transactions. The [BaseRepository](file:///Users/rameshseervi/Desktop/wordpress-erp-plugin/hospital-management/repositories/BaseRepository.php) encapsulates all basic CRUD operations, parameter binding, search, pagination, and soft deletion. Specific entity repositories inherit this behavior and add bespoke query logics.
- **Controllers**: Handle HTTP REST requests under `/wp-json/hospital-management/v1/`. They sanitize input, enforce business logic via service classes, and return uniform JSON payloads.
- **Middleware**: Intercepts REST routes. [AuthMiddleware](file:///Users/rameshseervi/Desktop/wordpress-erp-plugin/hospital-management/middleware/AuthMiddleware.php) parses and validates the JWT authorization header, and [RoleMiddleware](file:///Users/rameshseervi/Desktop/wordpress-erp-plugin/hospital-management/middleware/RoleMiddleware.php) asserts that the authenticated user possesses the required capabilities.

---

## 2. Authentication & Verification Workflows
Authentication uses secure, passwordless **One-Time Passwords (OTP)** combined with short-lived JSON Web Tokens (JWT) for API requests:

```
[User Login Request] ──(Email)──> [Generate 6-digit OTP] ──> [Send Email (SMTP)]
                                                                    │
                                                                    ▼
[User Authenticated] <──(Issue Access/Refresh Tokens)── [Verify OTP Code Input]
```

- **OTP Generation & Validity**: Generated OTPs are stored securely in user metadata with a timestamp. They expire automatically after 15 minutes.
- **JWT Issuance**: Successful verification returns an `access_token` (valid for 1 hour) and a `refresh_token` (valid for 14 days) signed using a secure secret key generated uniquely on plugin activation.
- **SMTP & Delivery Controls**:
  - Implements global custom SMTP mail servers utilizing PHPMailer integration.
  - Automatically overrides and formats `wp_mail` headers to ensure domain compliance and prevent DMARC failures.
  - Incorporates self-healing filters on options verification to resolve configuration errors.
- **Diagnostics Tester**: Supports live diagnostics in the dashboard to test SMTP settings instantly.

---

## 3. Database Schema Design (13 Tables)
The plugin registers 13 custom MySQL tables prefixed with the active WordPress database prefix (e.g. `wp_hospital_`):

1. **`patients`**: Stores primary patient demographics, insurance numbers, and emergency contact details.
2. **`doctors`**: Tracks professional specialties, experience, and consultation fees.
3. **`appointments`**: Manages visit schedules, status states, and remarks.
4. **`opd`**: Out-patient logs containing visit symptoms, clinical diagnostics, and checkup fees.
5. **`ipd`**: In-patient admissions tracking wards (ICU, Deluxe, Private), room numbers, bed allocations, and discharge times.
6. **`prescriptions`**: Records of prescribed drugs, dosages, directions, and durations.
7. **`billing`**: Detailed invoices tracking itemized fees, taxes, discounts, amounts paid, and due statuses.
8. **`pharmacy`**: Drug store logs containing batches, purchase costs, sale prices, stock counts, and expiry dates.
9. **`lab_tests`**: Catalog reference of lab diagnostic tests and baseline pricing.
10. **`lab_reports`**: Pathology/radiology test results, comments, and uploaded PDF paths.
11. **`schedules`**: Shift patterns detailing weekly availability schedules for doctors.
12. **`documents`**: Document repository linking physical file attachments inside the WP Media Library to corresponding patients or lab reports.
13. **`activity_logs`**: Auditing logs tracking user authentication, settings changes, and security events.

---

## 4. Frontend Single-Page Application (SPA) Dashboard
Served directly at `/hospital-management/`, the dashboard is an interactive, dark-themed dashboard client built with:
- **Responsive Layout**: Fluid sidebar navigation built for desktop, tablet, and mobile views.
- **Unified Modular Interface**: Renders charts, lists, and forms dynamically using AJAX/Fetch without full-page reloads.
- **Live Widgets**:
  - Global metrics counter cards.
  - Interactive calendar schedules.
  - Diagnostic SMTP email tester widget.
  - Audit trail activity viewer.
- **Embedded Swagger UI API docs**: Integrated directly at `/hospital-management-api-docs/` allowing developers to test every single endpoint with live inputs.

---

## 5. Security Protocols
The codebase implements industry-standard safety practices to defend clinical databases:
- **Prepared Statements**: All database operations use `$wpdb->prepare` to guarantee protection against SQL injection.
- **REST Sanitization**: Every REST API route utilizes data validation and sanitization filters (`sanitize_text_field`, `filter_var`, etc.).
- **Capabilities Matrix**: Routes are protected by capability checks corresponding to custom roles:
  - `manage_hospital`, `manage_users`, `manage_patients`, `manage_doctors`, `manage_appointments`, `manage_opd_ipd`, `manage_billing`, `manage_pharmacy`, `manage_laboratory`, `view_reports`, `view_dashboard`
- **Output Escaping**: Data returned in HTML templates or inputs are escaped using `esc_html`, `esc_attr`, or `esc_url`.
