# Gym Management API - Operations & Integration Guide

This guide provides a comprehensive overview of the **Gym Management API** WordPress plugin, including its architectural design, role-based access control, test credentials, and client endpoints workflow.

---

## 1. Plugin Contents & Modules

The plugin exposes a WordPress REST API under the `/wp-json/gym/v1` namespace.

| Module | Core Functionality | Database Table |
| :--- | :--- | :--- |
| **Authentication** | JWT secure token registration, login, logout, and token rotation. | Standard `wp_users` |
| **Members** | Manage gym members, profiles, BMI/physical details. | `wp_gym_members` |
| **Trainers** | Manage trainer profiles, specializations, and salary. | `wp_gym_trainers` |
| **Plans** | Catalog of gym membership packages (1 Month, 3 Months, Annual). | `wp_gym_plans` |
| **Memberships** | Assign plans to members, manage renewals, and track expiration dates. | `wp_gym_memberships` |
| **Payments** | Record member payments, create invoices, and generate revenue stats. | `wp_gym_payments` |
| **Diet Plans** | Assign customized text-based dietary plans to members. | `wp_gym_diet_plans` |
| **Attendance** | Daily check-in/check-out logs for members and trainers. | `wp_gym_attendance` |
| **Activity Logs** | Audit trails for secure actions. | `wp_gym_activity_logs` |

---

## 2. Authentication & JWT Login Flow

The plugin secures REST endpoints via **JWT (JSON Web Token)** using the standard `HS256` encryption algorithm.

### Default Client Test Credentials

During plugin activation, standard mock user accounts are generated automatically for testing:

| Username | Password | Assigned Role | Capabilities / Permissions |
| :--- | :--- | :--- | :--- |
| `gymadmin` | `123456` | `gym_admin` | Full control over plans, memberships, revenue, and trainers. |
| `gymstaff` | `123456` | `gym_staff` | Manage member attendance, record payments, and view active memberships. |

### Authentication Endpoints

#### Log In to Retrieve Tokens
* **Endpoint**: `POST /wp-json/gym/v1/auth/login`
* **Request Payload**:
  ```json
  {
    "username": "gymadmin",
    "password": "123456"
  }
  ```

---

## 3. Swagger UI Documentation

Access the interactive visual Swagger UI playground to execute mock requests and inspect response schemas:
* **Playground URL**: `/gym-management-docs/`

---

## 4. Modern Operations Dashboard

The plugin serves a modern, decoupled Single Page Application (SPA) dashboard for live gym management:
* **Dashboard URL**: `/gym-management/`
* **Features**: Live metrics on active members, today's attendance, revenue, expiring memberships, and full CRUD interfaces for the gym catalog. Styled with a premium Lite Theme incorporating primary blue tones and clean spacing similar to the school and pharmacy ERPs.
