# Technical Overview: Construction ERP API Plugin

Welcome to the **Construction ERP API** plugin. This document provides a high-level developer and administrator guide to understand the system design, components, database schemas, authentication mechanisms, and UI interfaces.

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
                                   │  (15 custom tables)  │
                                   └──────────────────────┘
```

- **Autoloader**: Dynamically maps classes in the `ConstructionManagementApi` namespace to physical source files using a custom PSR-4 autoloader in [construction-management-api.php](file:///Users/rameshseervi/Desktop/wordpress-erp-plugin/construction-management/construction-management-api.php).
- **Repositories**: Standardize DB transactions. The [BaseRepository](file:///Users/rameshseervi/Desktop/wordpress-erp-plugin/construction-management/repositories/BaseRepository.php) encapsulates all basic CRUD operations, parameter binding, search, pagination, and soft deletion. Specific entity repositories inherit this behavior and add bespoke query logics.
- **Controllers**: Handle HTTP REST requests under `/wp-json/construction-management/v1/`. They sanitize input, enforce business logic via service classes, and return uniform JSON payloads.
- **Middleware**: Intercepts REST routes. [AuthMiddleware](file:///Users/rameshseervi/Desktop/wordpress-erp-plugin/construction-management/middleware/AuthMiddleware.php) parses and validates the JWT authorization header, and [RoleMiddleware](file:///Users/rameshseervi/Desktop/wordpress-erp-plugin/construction-management/middleware/RoleMiddleware.php) asserts that the authenticated user possesses the required capabilities.

---

## 2. Authentication & Verification Workflows
Authentication uses secure, passwordless **One-Time Passwords (OTP)** combined with short-lived JSON Web Tokens (JWT) for API requests:

```
[User Login Request] ──(Email)──> [Generate 6-digit OTP] ──> [Send Email (SMTP)]
                                                                    │
                                                                    ▼
[User Authenticated] <──(Issue Access/Refresh Tokens)── [Verify OTP Code Input]
```

- **OTP Generation & Validity**: Generated OTPs are stored securely in transient data linked to user credentials. They expire automatically after 15 minutes.
- **JWT Issuance**: Successful verification returns an `access_token` (valid for 1 hour) and a `refresh_token` (valid for 7 days) signed using a secure secret key generated uniquely on plugin activation.
- **SMTP & Delivery Controls**:
   - Implements global custom SMTP mail servers utilizing PHPMailer integration.
   - Automatically overrides and formats `wp_mail` headers to ensure domain compliance and prevent DMARC failures.
   - Incorporates self-healing filters on options verification to resolve configuration errors.
- **Diagnostics Tester**: Supports live diagnostics in the dashboard to test SMTP settings instantly.

---

## 3. Database Schema Design (15 Tables)
The plugin registers 15 custom MySQL tables prefixed with the active WordPress database prefix (e.g. `wp_construction_`):

1. **`projects`**: Stores primary construction site details, client name, type, location, managers, estimated and actual costs.
2. **`milestones`**: Project stage completions with planned dates, actual dates, and completion percentages.
3. **`materials`**: Inventory database tracking unit descriptions, rates, available quantity, minimum alert margins.
4. **`purchases`**: Purchase orders (PO) detailing materials, quantities, suppliers, GST additions, and PO status approvals.
5. **`suppliers`**: Supplier directories listing contacts, GST numbers, ratings, and active statuses.
6. **`site_expenses`**: Registers site overhead expenses like fuel, rentals, maintenance, electricity, utility bills, and approvals.
7. **`contractors`**: Contractor registry tracking specialties, contractor value, mobile/email, and status checks.
8. **`labours`**: Workforce database tracking trade roles (Mason, Helper, Electrician), daily wages, and active assigned project.
9. **`attendance`**: Workforce daily logs tracking attendance (Present, Half-day, Absent), working and overtime hours.
10. **`payroll`**: Auto-calculated weekly or monthly payroll logs, adding regular and overtime earnings (1.5x hourly rate) based on logged attendance.
11. **`progress`**: Delays and progress update logs with categories, planned vs actual percentages, and photos.
12. **`equipment`**: Machine inventories tracking backhoes, concrete mixers, cranes, rental pricing, and maintenance dates.
13. **`billing`**: Invoice registries containing milestone references, GST amounts, bill amounts, and status checks.
14. **`documents`**: Document attachment reference mapping linking physical PDF scans or drawings from the WP Media Library to corresponding projects/purchases/contractors.
15. **`activity_logs`**: Auditing logs tracking user authentication, settings changes, and security events.

---

## 4. Frontend Single-Page Application (SPA) Dashboard
Served directly at `/construction-management/`, the dashboard is an interactive, dark-themed dashboard client built with:
- **Responsive Layout**: Fluid sidebar navigation built for desktop, tablet, and mobile views.
- **Unified Modular Interface**: Renders charts, lists, and forms dynamically using AJAX/Fetch without full-page reloads.
- **Live Widgets**:
   - Global metrics counter cards.
   - Simulated dynamic CSS bar charts showing budget vs actual allocations.
   - Interactive payroll generator.
- **Embedded Swagger UI API docs**: Integrated directly at `/construction-management-api-docs/` allowing developers to test every single endpoint with live inputs.

---

## 5. Security Protocols
The codebase implements industry-standard safety practices to defend clinical databases:
- **Prepared Statements**: All database operations use `$wpdb->prepare` to guarantee protection against SQL injection.
- **REST Sanitization**: Every REST API route utilizes data validation and sanitization filters (`sanitize_text_field`, `filter_var`, etc.).
- **Capabilities Matrix**: Routes are protected by capability checks corresponding to custom roles:
   - `manage_construction`, `manage_users`, `manage_projects`, `manage_materials`, `manage_expenses`, `manage_contractors`, `manage_labour`, `manage_progress`, `manage_equipment`, `manage_billing`, `view_reports`, `view_dashboard`
- **Output Escaping**: Data returned in HTML templates or inputs are escaped using `esc_html`, `esc_attr`, or `esc_url`.
