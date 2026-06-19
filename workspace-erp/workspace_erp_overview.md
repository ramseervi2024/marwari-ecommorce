# Workspace ERP API - Operations & Integration Guide

This guide provides a comprehensive overview of the **Aurbis Workspace Management ERP API** WordPress plugin, including its architectural design, role-based access control, test credentials, and client endpoints workflow.

---

## 1. Plugin Contents & Modules

The plugin exposes a WordPress REST API under the `/wp-json/workspace-erp/v1` namespace.

| Module | Core Functionality | Database Table |
| :--- | :--- | :--- |
| **Authentication** | JWT secure token registration, login, logout, and token rotation. | Standard `wp_users` & `wp_usermeta` |
| **Dashboard** | Metrics cards (Total Buildings, Tenants, Occupancy, Revenue, ESG score) and charts. | Dashboard calculations aggregate table values |
| **CRM & Leads** | Capture inquiries, broker entries, opportunities, proposals, and visits. | `wp_workspace_leads`, `_opportunities`, `_proposals`, `_site_visits` |
| **Clients** | Master database for companies, lease contracts, and renewal dates. | `wp_workspace_clients` |
| **Workspace** | Property registry: Buildings, Floors, Cabins, Desks, and Meeting Rooms. | `wp_workspace_buildings`, `_floors`, `_workspaces`, `_seats`, `_meeting_rooms` |
| **Occupancy** | Real-time seat allocation, utilization rates, and check-in tracking. | `wp_workspace_occupancy` |
| **Visitor** | QR passes, check-in registration, and employee host pre-approval. | `wp_workspace_visitors`, `_visitor_passes` |
| **Facility** | Helpdesk tickets, technician assignments, work orders, SLAs, and schedules. | `wp_workspace_tickets`, `_work_orders`, `_maintenance_schedule` |
| **Asset** | Equipment register, warranties, depreciation, and movements. | `wp_workspace_assets`, `_asset_allocations`, `_asset_movements` |
| **Vendor** | Register suppliers, coordinate contracts, and log service history payments. | `wp_workspace_vendors`, `_vendor_payments` |
| **Billing** | Leases invoices generation, Razorpay integration, receipts, credit notes. | `wp_workspace_invoices`, `_payments`, `_receipts`, `_credit_notes` |
| **Sustainability** | Track ESG reading logs for energy (kWh), water (liters), waste, and carbon. | `wp_workspace_energy_usage`, `_water_usage`, `_waste_management`, `_carbon_tracking` |
| **Smart Building** | IoT devices integration, live sensor metrics, and access gate logs. | `wp_workspace_iot_devices`, `_sensor_data`, `_access_logs` |
| **HR & Workforce** | Employees register, payroll, shift rosters, leaves, and attendance logs. | `wp_workspace_employees`, `_attendance`, `_leaves`, `_shifts` |
| **Community** | Announcements broadcasting, community events calendar, and feedback. | `wp_workspace_announcements`, `_events`, `_service_requests` |
| **Mobile App** | Optimized routes for Aurbis One mobile app (dashboard, room booking, visitors). | Custom mobile-optimized controller views |
| **Reports** | Analytics dashboards for Revenue, Occupancy, SLA compliance, and ESG reports. | Aggregated report endpoints |
| **Notifications** | Transaction logs (Email, SMS, Push, WhatsApp alerts). | `wp_workspace_notifications` |
| **Audit Logs** | Tracks administrator actions, login histories, and changes. | `wp_workspace_activity_logs` |

---

## 2. Authentication & JWT Login Flow

The plugin secures REST endpoints via **JWT (JSON Web Token)** using the standard `HS256` encryption algorithm.

```mermaid
sequenceDiagram
    participant Frontend as Aurbis One App / React / Portal
    participant API as Workspace ERP API
    participant WP as WordPress Core
    
    Frontend->>API: POST /auth/register (username, email, password, role)
    API->>WP: Create WordPress user with workspace role
    API-->>Frontend: 201 Created
    
    Frontend->>API: POST /auth/login (username, password)
    API->>WP: Verify credentials
    API-->>Frontend: 200 OK (Access Token + Refresh Token)
    
    Frontend->>API: GET /dashboard (Authorization: Bearer <Access Token>)
    API->>API: Validate Signature and Expiration
    API-->>Frontend: 200 OK (Dashboard Statistics)
```

### Default Client Test Credentials

During plugin activation, standard mock user accounts are generated automatically for testing:

| Username | Password | Assigned Role | Capabilities / Permissions |
| :--- | :--- | :--- | :--- |
| `workspace_superadmin` | `123456` | `workspace_super_admin` | Full control over settings, users, approvals, and financials |
| `workspace_sales` | `salespass123` | `workspace_sales_manager` | Manage Leads, CRM, Clients database, and pipelines |
| `workspace_facility` | `facilitypass123` | `workspace_facility_manager` | Manage Maintenance Tickets, Vendors, Assets, and Visitors |
| `workspace_finance` | `financepass123` | `workspace_finance_manager` | Generate Leases Invoices, Track Payments and Revenue Reports |
| `workspace_hr` | `hrpass123` | `workspace_hr_manager` | Manage Employees Records, Attendance Logs, and Leaves |
| `workspace_tenant` | `tenantpass123` | `workspace_tenant_admin` | Manage Tenant Employees, Visitors Pre-approval, and Room Bookings |
| `workspace_employee` | `employeepass123` | `workspace_tenant_employee` | Schedule Meeting Rooms, Raise Helpdesk Tickets, and View Invoices |
| `workspace_security` | `securitypass123` | `workspace_security_staff` | Verify Visitor QR Passes and Check In/Out Entry |
| `workspace_vendor_user` | `vendorpass123` | `workspace_vendor` | View Assigned Work Orders and Update Status |

---

## 3. Role-Based Access Control Matrix (RBAC)

REST routes require specific capability headers validated by the JWT middleware:

| Capability / Permission | Super Admin | Sales | Facility | Finance | HR | Tenant Admin | Tenant Employee | Security | Vendor |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **Manage Users & Settings** | Yes | No | No | No | No | No | No | No | No |
| **Manage CRM & Leads** | Yes | Yes | No | No | No | No | No | No | No |
| **Manage Clients** | Yes | Yes | No | No | No | No | No | No | No |
| **Manage Properties/Workspaces** | Yes | No | No | No | No | No | No | No | No |
| **Manage Facilities / Work Orders** | Yes | No | Yes | No | No | No | No | No | No |
| **Manage Billing & Invoices** | Yes | No | No | Yes | No | No | No | No | No |
| **Manage Sustainability (ESG)** | Yes | No | No | No | No | No | No | No | No |
| **Manage HR / Attendance** | Yes | No | No | No | Yes | No | No | No | No |
| **Manage Visitors Check-in** | Yes | No | Yes | No | No | Yes | Yes | Yes | No |
| **View Dashboard & Reports** | Yes | Yes | Yes | Yes | Yes | Yes | Yes | No | No |

---

## 4. Mobile App REST API Reference

The plugin exposes separate, fully functional mobile routes for the **Aurbis One** application, automatically filtering records by the authenticated tenant employee's identity and company:

* **GET `/mobile/dashboard`**: Fetch dashboard summary metrics (notifications count, active bookings count, pending visitors count, outstanding invoices sum).
* **GET `/mobile/bookings`**: Fetch booking lists.
* **POST `/mobile/meeting-room-booking`**: Reserve a cabin/meeting room slot.
* **GET `/mobile/visitors`**: Fetch registered visitor requests.
* **POST `/mobile/visitors`**: Add a pre-approved visitor request.
* **POST `/mobile/visitors/{id}/approve`**: Approve or reject check-in requests.
* **GET `/mobile/invoices`**: Fetch invoice lease totals and utility charges.
* **POST `/mobile/invoices/{id}/pay`**: Process simulated payment via Razorpay.
* **GET `/mobile/service-requests`**: Fetch helpdesk support tickets.
* **POST `/mobile/service-request`**: Raise a facility service ticket.
* **GET `/mobile/announcements`**: Fetch community bulletins.
* **GET `/mobile/events`**: Fetch community events calendar.
* **GET `/mobile/meeting-rooms`**: List available rooms.

---

## 5. Administrative & Operations REST API Reference

These endpoints support full administrative capabilities, required by designated roles (e.g. HR Manager, Facility Manager, Finance Manager, and Super Admin):

### 5.1 HR & Workforce
* **GET `/hr/employees`**: Retrieve directory list of all employee records.
* **POST `/hr/employees`**: Create a new employee profile (employee code, department, salary, shift, status).
* **PUT `/hr/employees/{id}`**: Update employee details, designations, and status inline.
* **DELETE `/hr/employees/{id}`**: Soft delete an employee record.
* **GET `/hr/attendance`**: Retrieve full attendance logs.
* **POST `/hr/attendance`**: Create daily attendance check-in/out records.
* **PUT `/hr/attendance/{id}`**: Edit attendance times or change presence status.
* **DELETE `/hr/attendance/{id}`**: Delete attendance record entries.

### 5.2 Assets & Inventory
* **GET `/assets`**: Retrieve list of property assets.
* **POST `/assets`**: Register a new equipment asset (asset code, category, current value).
* **PUT `/assets/{id}`**: Update asset location, values, and lifecycle status.
* **DELETE `/assets/{id}`**: Remove asset record from the system.
* **GET `/assets/allocations`**: Retrieve logs of asset allocations.
* **POST `/assets/allocations`**: Allocate an asset to a client company or employee.
* **PUT `/assets/allocations/{id}`**: Edit allocation date periods and statuses.
* **DELETE `/assets/allocations/{id}`**: Cancel or delete an asset allocation record.

### 5.3 Vendor Management
* **GET `/vendors`**: Retrieve full supplier vendor registry.
* **POST `/vendors`**: Register a new vendor supplier profile.
* **PUT `/vendors/{id}`**: Update vendor contact details, services, rating, and status.
* **DELETE `/vendors/{id}`**: Soft delete vendor entries.
* **GET `/vendors/payments`**: Retrieve all logs of vendor payments.
* **POST `/vendors/payments`**: Add a vendor payment transaction.
* **PUT `/vendors/payments/{id}`**: Update payment amounts or completion statuses.
* **DELETE `/vendors/payments/{id}`**: Remove a vendor payment record.

### 5.4 Smart Buildings (IoT)
* **GET `/smartbuilding/devices`**: List all registered IoT devices.
* **POST `/smartbuilding/devices`**: Register a new IoT device (device type, manufacturer, serial number).
* **PUT `/smartbuilding/devices/{id}`**: Update device details or connection status.
* **DELETE `/smartbuilding/devices/{id}`**: Delete registered device records.
* **GET `/smartbuilding/sensors`**: Fetch recorded environment sensor logs (temperature, humidity, etc.).
* **GET `/smartbuilding/access-logs`**: Fetch live gate access card logs.

### 5.5 Reports & Analytics
* **GET `/reports/revenue`**: Generate revenue report statistics.
* **GET `/reports/occupancy`**: Generate property occupancy capacity calculations.
* **GET `/reports/tickets`**: Retrieve helpdesk ticket SLA compliance levels.
* **GET `/reports/esg`**: Fetch sustainability ESG metrics (carbon offsets, recycling rates, energy usage).

---

## 6. Swagger UI Documentation

Access the interactive visual Swagger UI playground to execute mock requests and inspect response schemas:
* **Playground URL**: `/workspace-erp-docs` (Redirects to swagger templates index)

---

## 7. Modern Operations Dashboard Portal

The plugin serves a modern, glassmorphic executive dashboard:
* **Dashboard URL**: `/workspace-erp`
* **Features**: Live metrics cards, dynamic SVG trend charts, modals for creating leads, tickets, and booking rooms, and SMTP settings editor.
