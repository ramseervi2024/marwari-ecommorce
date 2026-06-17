# HR & Payroll ERP – Plugin Overview

> **Plugin Slug**: `hr-management`  
> **Version**: 1.0.0  
> **Author**: Ramesh Seervi  
> **REST Namespace**: `hr-management/v1`  
> **Dashboard URL**: `/hr-management/`  
> **API Docs URL**: `/hr-management-api-docs/`

---

## 📌 What This Plugin Does

The **HR & Payroll ERP** is a fully self-contained, decoupled WordPress plugin that transforms any WordPress installation into a complete human resources and payroll management system. It exposes a clean REST API backend and a premium glassmorphic single-page dashboard frontend accessible at `/hr-management/`.

**Core capabilities:**
- Employee profile management (department, designation, PF/ESI, joining date)
- Daily attendance tracking with auto Late/Half Day detection
- Leave request lifecycle (apply → approve/reject → balance deduction)
- Salary structure setup with auto PF (12%) and ESI (0.75%) calculation
- Monthly payslip generation with PF/ESI override support
- Employee document registry with verification workflow
- Role-based access with 4 HR-specific roles
- JWT-authenticated REST API
- Interactive Swagger UI playground
- SMTP email configuration
- Activity audit logs

---

## 🗂️ Plugin Directory Structure

```
hr-management/
├── hr-management.php          ← Main bootstrap (plugin header, autoloader, CORS, routes)
├── composer.json              ← PSR-4 namespace config
│
├── database/
│   └── Migrations.php         ← DB schema creation, role setup, seed data
│
├── services/
│   ├── JwtService.php         ← JWT encode/decode
│   └── AuthService.php        ← Login, register, OTP, activity logging
│
├── middleware/
│   ├── AuthMiddleware.php     ← Bearer token validation
│   └── RoleMiddleware.php     ← Capability-based access control
│
├── repositories/
│   ├── BaseRepository.php     ← Generic CRUD + pagination + search
│   ├── EmployeeRepository.php ← Employee-specific queries
│   ├── AttendanceRepository.php
│   ├── LeaveRepository.php    ← Leave balance management
│   ├── SalaryRepository.php
│   ├── PayslipRepository.php
│   └── DocumentRepository.php
│
├── controllers/
│   ├── BaseController.php     ← success()/error() response wrappers
│   ├── AuthController.php     ← Login, register, me, change-password, logout, SMTP
│   ├── EmployeeController.php ← Employee CRUD
│   ├── AttendanceController.php ← Check-in/out, manual entry
│   ├── LeaveController.php    ← Apply/approve/reject/balance
│   ├── PayrollController.php  ← Salary setup + payslip generation
│   ├── DocumentController.php ← Document CRUD + verification
│   └── DashboardController.php ← Aggregated stats + activity logs
│
├── routes/
│   ├── auth.php
│   ├── employee.php
│   ├── attendance.php
│   ├── leave.php
│   ├── payroll.php
│   ├── document.php
│   └── dashboard.php
│
├── swagger/
│   ├── swagger.json           ← OpenAPI 3.0.3 specification
│   └── index.php              ← Swagger UI dark-themed playground
│
├── views/
│   └── dashboard-view.php     ← Full SPA frontend dashboard
│
└── hr_management_overview.md  ← This file
```

---

## 🗄️ Database Schema (8 Tables)

All tables use the `wp_hr_` prefix.

| Table | Purpose | Key Columns |
|---|---|---|
| `wp_hr_employees` | Extended employee profiles | `user_id`, `department`, `designation`, `pf_number`, `esi_number`, `status` |
| `wp_hr_attendance` | Daily check-in/out | `employee_id`, `date`, `check_in`, `check_out`, `total_hours`, `status` |
| `wp_hr_leaves` | Leave requests | `employee_id`, `leave_type`, `start_date`, `end_date`, `status`, `approved_by` |
| `wp_hr_leave_balances` | YTD leave allowances | `employee_id`, `casual_leaves`, `medical_leaves`, `earned_leaves`, `unpaid_leaves` |
| `wp_hr_salaries` | Monthly salary profiles | `employee_id`, `base_salary`, `allowances`, `deductions`, `pf_contribution`, `esi_contribution`, `net_salary` |
| `wp_hr_payslips` | Generated payslips | `employee_id`, `month`, `year`, `base_salary`, `pf_deduction`, `esi_deduction`, `net_salary`, `status` |
| `wp_hr_documents` | Employee documents | `employee_id`, `document_name`, `document_type`, `file_url`, `status` |
| `wp_hr_activity_logs` | Audit trail | `user_id`, `action`, `details`, `ip_address`, `created_at` |

### Attendance Status Logic

| Condition | Status |
|---|---|
| Check-in before 10:00 AM | **Present** |
| Check-in after 10:00 AM | **Late** |
| Total hours < 4 at check-out | **Half Day** |
| No check-in record | **Absent** (tracked separately) |

### PF/ESI Auto-Calculation

```
PF  = Basic Salary × 12%
ESI = Gross Salary × 0.75%
Net = (Basic + Allowances) - (Deductions + PF + ESI)
```

---

## 👥 Roles & Capabilities

| Capability | Super Admin | Manager | Accountant | Employee |
|---|:---:|:---:|:---:|:---:|
| `manage_employees` | ✅ | ❌ | ❌ | ❌ |
| `manage_hr_users` | ✅ | ❌ | ❌ | ❌ |
| `manage_attendance` | ✅ | ✅ | ❌ | ❌ |
| `manage_leaves` | ✅ | ✅ | ❌ | ❌ |
| `manage_payroll` | ✅ | ❌ | ✅ | ❌ |
| `manage_documents` | ✅ | ✅ | ❌ | ❌ |
| `view_hr_dashboard` | ✅ | ✅ | ✅ | ✅ |
| `view_hr_reports` | ✅ | ✅ | ✅ | ❌ |
| `view_own_payroll` | ✅ | ✅ | ✅ | ✅ |
| `manage_own_attendance` | ✅ | ✅ | ✅ | ✅ |
| `manage_own_leaves` | ✅ | ✅ | ✅ | ✅ |
| `manage_own_documents` | ✅ | ✅ | ✅ | ✅ |

### Default Seeded Test Accounts

| Username | Password | Role |
|---|---|---|
| `hsuperadmin` | `123456` | HR Super Admin |
| `hmanager` | `123456` | HR Manager |
| `haccountant` | `123456` | HR Accountant |
| `hemployee` | `123456` | HR Employee |

---

## 🔌 REST API Endpoints

**Base URL**: `{site_url}/wp-json/hr-management/v1`

### Authentication

| Method | Endpoint | Auth | Description |
|---|---|:---:|---|
| POST | `/auth/login` | ❌ | Get JWT token |
| POST | `/auth/register` | ❌ | Create HR user account |
| POST | `/auth/refresh` | ❌ | Refresh expired token |
| GET | `/auth/me` | ✅ | Get current user profile |
| POST | `/auth/change-password` | ✅ | Change password |
| POST | `/auth/logout` | ✅ | Invalidate token |
| GET | `/auth/activity-logs` | ✅ | View logs |
| POST | `/auth/smtp-settings` | ✅ | Update SMTP config |

### Employees

| Method | Endpoint | Capability | Description |
|---|---|---|---|
| GET | `/employees` | authenticated | List with filters/pagination |
| GET | `/employees/{id}` | authenticated | Single employee |
| PUT | `/employees/{id}` | `manage_employees` | Update profile |
| DELETE | `/employees/{id}` | `manage_hr_users` | Soft delete |

### Attendance

| Method | Endpoint | Auth | Description |
|---|---|:---:|---|
| GET | `/attendance` | ✅ | List (role-filtered) |
| POST | `/attendance/check-in` | ✅ | Record check-in |
| POST | `/attendance/check-out` | ✅ | Record check-out |
| GET | `/attendance/{id}` | ✅ | Single record |
| PUT | `/attendance/{id}` | `manage_attendance` | Manual override |
| DELETE | `/attendance/{id}` | `manage_attendance` | Delete record |

### Leaves

| Method | Endpoint | Auth | Description |
|---|---|:---:|---|
| GET | `/leaves` | ✅ | List (role-filtered) |
| POST | `/leaves` | ✅ | Submit leave request |
| GET | `/leaves/{id}` | ✅ | Single request |
| DELETE | `/leaves/{id}` | ✅ | Cancel own request |
| POST | `/leaves/{id}/approve` | `manage_leaves` | Approve + deduct balance |
| POST | `/leaves/{id}/reject` | `manage_leaves` | Reject request |
| GET | `/leaves/balance/{employee_id}` | ✅ | Get leave balance |

### Payroll

| Method | Endpoint | Capability | Description |
|---|---|---|---|
| GET | `/payroll/salaries` | `manage_payroll` | List all salary structures |
| POST | `/payroll/salaries` | `manage_payroll` | Create/update salary |
| GET | `/payroll/salaries/{employee_id}` | authenticated | View salary |
| GET | `/payroll/payslips` | authenticated | List payslips (role-filtered) |
| POST | `/payroll/payslips/generate` | `manage_payroll` | Generate payslip |
| GET | `/payroll/payslips/{id}` | authenticated | View payslip |
| PUT | `/payroll/payslips/{id}/mark-paid` | `manage_payroll` | Mark as Paid |
| DELETE | `/payroll/payslips/{id}` | `manage_payroll` | Delete draft payslip |

### Documents

| Method | Endpoint | Auth | Description |
|---|---|:---:|---|
| GET | `/documents` | ✅ | List (role-filtered) |
| POST | `/documents` | ✅ | Register document |
| GET | `/documents/{id}` | ✅ | Single document |
| PUT | `/documents/{id}` | `manage_documents` | Update/verify |
| DELETE | `/documents/{id}` | `manage_documents` | Delete |

### Dashboard

| Method | Endpoint | Auth | Description |
|---|---|:---:|---|
| GET | `/dashboard/stats` | ✅ | Role-based KPI metrics |
| GET | `/dashboard/activity-logs` | `manage_hr_users` | Audit trail |

---

## 💻 Dashboard Frontend Features

**URL**: `{site_url}/hr-management/`

| Feature | Admin/Manager | Accountant | Employee |
|---|:---:|:---:|:---:|
| Company-wide attendance stats | ✅ | ❌ | ❌ |
| Employee directory CRUD | ✅ | ❌ | ❌ |
| Live check-in / check-out card | ✅ | ✅ | ✅ |
| Attendance logs (all) | ✅ | ❌ | own only |
| Leave approval workflow | ✅ | ❌ | own only |
| Leave balance display | ✅ | ✅ | ✅ |
| Salary structure management | ✅ | ✅ | ❌ |
| Payslip generation | ✅ | ✅ | own only |
| Payslip preview + mark paid | ✅ | ✅ | view only |
| Document registry | ✅ | ❌ | own only |
| Document verification | ✅ | ❌ | ❌ |
| Activity log viewer | ✅ | ❌ | ❌ |
| SMTP settings | ✅ | ❌ | ❌ |
| Light/Dark mode toggle | ✅ | ✅ | ✅ |

### Session & State Persistence

| Key | Storage | Purpose |
|---|---|---|
| `hr_auth_token` | `localStorage` | JWT bearer token — survives refresh |
| `hr_current_user` | `localStorage` | User profile JSON — survives refresh |
| `hr_active_tab` | `localStorage` | Active sidebar tab — restored on reload |
| `hr_theme` | `localStorage` | `light` or `dark` — loaded before render (anti-FOUC) |

---

## 🔐 Security Architecture

- **JWT Tokens**: HS256 signed with a server-side secret. Tokens expire in 24 hours; refresh tokens expire in 30 days.
- **Capability checks**: Every protected route checks WordPress capabilities via `RoleMiddleware`.
- **CORS**: Configured globally for `GET, POST, PUT, DELETE, OPTIONS` with `Authorization` header support.
- **Input sanitization**: All user input sanitized with `sanitize_text_field()`, `esc_url_raw()`, and `intval()`.
- **SQL injection prevention**: All queries use `$wpdb->prepare()`.
- **Soft deletes**: Employee records are soft-deleted (`deleted_at` timestamp) rather than permanently removed.

---

## 🚀 Installation & Activation

1. Copy the `hr-management/` folder to `wp-content/plugins/`
2. Activate from **WP Admin → Plugins**
3. Plugin automatically:
   - Creates 8 database tables
   - Registers 4 custom roles
   - Seeds 4 demo accounts
   - Registers rewrite rules for the dashboard and Swagger UI
4. Visit `{site_url}/hr-management/` to access the dashboard
5. Visit `{site_url}/hr-management-api-docs/` for API documentation

> **Note**: If the dashboard returns 404, go to **WP Admin → Settings → Permalinks** and click **Save Changes** to flush rewrite rules.

---

## 📧 SMTP Configuration

Configure SMTP via the dashboard **Settings** tab or directly through WordPress options:

| Option Key | Description |
|---|---|
| `hr_smtp_enabled` | `yes` or `no` |
| `hr_smtp_host` | SMTP server hostname |
| `hr_smtp_port` | Port (587 for TLS, 465 for SSL) |
| `hr_smtp_username` | SMTP username/email |
| `hr_smtp_password` | SMTP password/app password |
| `hr_smtp_encryption` | `tls`, `ssl`, or `none` |
| `hr_smtp_from_email` | Sender email address |
| `hr_smtp_from_name` | Sender display name |

---

## 📋 Quick API Test (cURL)

```bash
# 1. Login
curl -s -X POST {SITE_URL}/wp-json/hr-management/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"hsuperadmin","password":"123456"}' | jq .

# 2. Get Dashboard Stats (replace TOKEN)
curl -s {SITE_URL}/wp-json/hr-management/v1/dashboard/stats \
  -H "Authorization: Bearer TOKEN" | jq .

# 3. Check In
curl -s -X POST {SITE_URL}/wp-json/hr-management/v1/attendance/check-in \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{}' | jq .

# 4. Generate Payslip
curl -s -X POST {SITE_URL}/wp-json/hr-management/v1/payroll/payslips/generate \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"employee_id":1,"month":"June","year":2026}' | jq .
```
