# Aurbis One - Mobile App API Integration & Screen Specification Guide

This document serves as the developer handbook for integrating the **Aurbis One Mobile App** (built on React Native) with the WordPress ERP backend. It details the design, contents, interactive states, and REST APIs for each mobile screen.

---

## 1. Architectural Overview & Authentication

The Aurbis One mobile application interacts with the WordPress backend through REST API routes exposed under the namespace:
`https://yourdomain.com/wp-json/workspace-erp/v1`

### JWT Authentication Flow
Every request to the mobile API endpoints requires a JSON Web Token (JWT) in the `Authorization` header to authenticate the user and map their permissions.

#### Request Headers:
```http
Authorization: Bearer <jwt_access_token>
Content-Type: application/json
Accept: application/json
```

---

## 2. Screen Specifications & API Mappings

### 2.1 Login Screen

The landing screen of the mobile application allows employees and tenants to sign into the Aurbis One ecosystem.

#### UI & Screen Contents:
- **Branding Header**: Aurbis One Logo and tagline.
- **Form Fields**:
  - `Username / Email` (Text Input)
  - `Password` (Secure Text Input)
- **Interactive Controls**:
  - `Login Button` (Primary action, displays loader on press)
  - `Forgot Password` (Link redirecting to reset password screen)
  - `Help / Contact Admin` (Quick support footer)

#### API Integration:
* **Endpoint**: `POST /auth/login`
* **Request Payload**:
  ```json
  {
    "username": "workspace_employee",
    "password": "employeepass123"
  }
  ```
* **Success Response (200 OK)**:
  ```json
  {
    "success": true,
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "user_email": "employee@technova.com",
    "user_nicename": "workspace_employee",
    "user_display_name": "TechNova Employee",
    "roles": ["workspace_tenant_employee"]
  }
  ```

---

### 2.2 Dashboard / Home Screen

The dashboard acts as the command center for the tenant employee, presenting summarized stats, real-time counters, and quick actions.

#### UI & Screen Contents:
- **Welcome Section**: User avatar, greeting (e.g., "Hello, TechNova Employee!"), and notification badge.
- **Metric Cards Grid**:
  - **Active Bookings**: Displays count of confirmed upcoming meeting room bookings.
  - **Pending Visitors**: Displays count of visitor approvals currently awaiting action.
  - **Outstanding Invoices**: Sum of unpaid client invoices.
  - **Notifications Alerts**: Simple counter of unread community/system notifications.
- **Quick Action Row**:
  - `Book Room` button (Redirects to Meeting Room list)
  - `Pre-approve Visitor` button (Redirects to visitor registration form)
  - `Raise Ticket` button (Redirects to Support Ticket form)
  - `Announcements` widget (Quick link to the latest community posts)

#### API Integration:
* **Endpoint**: `GET /mobile/dashboard`
* **Success Response (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Mobile dashboard metrics fetched",
    "data": {
      "notifications_count": 3,
      "active_bookings": 1,
      "pending_visitors": 1,
      "outstanding_invoices_sum": 250750.00
    }
  }
  ```

---

### 2.3 Meeting Room Booking Screen

Enables users to search, filter, and book meeting rooms, conference halls, or cabins inside their workspace buildings.

#### UI & Screen Contents:
- **Select Room Dropdown**: Fetch and choose from a list of available rooms.
- **Booking Form Inputs**:
  - `Booking Date` (Calendar picker)
  - `Start Time` (Time picker)
  - `End Time` (Time picker)
  - `Purpose` (Short text input, e.g., "Sprint Planning")
  - `Attendees` (Numeric stepper / input, defaults to 2)
- **Confirm Booking Button**: Submits reservation, displaying a success animation upon confirmation.

#### API Integration:

##### 1. Fetching Available Rooms:
* **Endpoint**: `GET /mobile/meeting-rooms`
* **Success Response (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Meeting rooms fetched",
    "data": [
      {
        "id": 1,
        "room_name": "Boardroom A - Floor 3"
      },
      {
        "id": 2,
        "room_name": "Creative Studio - Floor 2"
      },
      {
        "id": 3,
        "room_name": "Cabin 3B - Executive Suite"
      }
    ]
  }
  ```

##### 2. Submitting Booking Request:
* **Endpoint**: `POST /mobile/meeting-room-booking`
* **Request Payload**:
  ```json
  {
    "room_id": 3,
    "booking_date": "2026-06-21",
    "start_time": "14:00:00",
    "end_time": "15:00:00",
    "purpose": "Monthly Review Meeting",
    "attendees": 5
  }
  ```
* **Success Response (201 Created)**:
  ```json
  {
    "success": true,
    "message": "Meeting Room booked via mobile",
    "data": {
      "id": 22,
      "room_id": 3,
      "client_id": 1,
      "booked_by": 5,
      "booking_date": "2026-06-21",
      "start_time": "14:00:00",
      "end_time": "15:00:00",
      "purpose": "Monthly Review Meeting",
      "attendees": 5,
      "status": "CONFIRMED",
      "created_at": "2026-06-19 18:45:00",
      "updated_at": "2026-06-19 18:45:00"
    }
  }
  ```

---

### 2.4 My Bookings Screen

Lists historical and upcoming meeting room reservations created by the authenticated employee.

#### UI & Screen Contents:
- **Tabs**: `Upcoming Bookings` and `Past History`.
- **Booking List Cards**:
  - Room name and capacity.
  - Formatted date and time slot (e.g., "June 21, 2026: 02:00 PM - 03:00 PM").
  - Status Badge: `CONFIRMED` (Green), `COMPLETED` (Blue), or `CANCELLED` (Red).

#### API Integration:
* **Endpoint**: `GET /mobile/bookings`
* **Success Response (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Bookings fetched",
    "data": [
      {
        "id": 22,
        "room_name": "Cabin 3B - Executive Suite",
        "booking_date": "2026-06-21",
        "start_time": "14:00:00",
        "end_time": "15:00:00",
        "purpose": "Monthly Review Meeting",
        "status": "CONFIRMED"
      }
    ]
  }
  ```

---

### 2.5 Visitor Registration & Pre-Approval Screen

Allows hosts to register upcoming business visitors, contractors, or interviewees in advance. This auto-generates visitor passes with secure entry codes.

#### UI & Screen Contents:
- **List View**: Displays registered visitors, their statuses (`PENDING`, `APPROVED`, `CHECKED_IN`, `CHECKED_OUT`), and verification codes.
- **Registration Form fields**:
  - `Visitor Name` (Text input, Required)
  - `Mobile Number` (Text input, Required)
  - `Email Address` (Text input)
  - `Company Name` (Text input)
  - `Visit Purpose` (Text input, e.g., "Vendor Negotiation")
  - `Host Name` (Defaults to active user name)
- **Submit Button**: registers visitor, redirects to details screen with the generated QR pass details.

#### API Integration:

##### 1. Register a New Visitor:
* **Endpoint**: `POST /mobile/visitors`
* **Request Payload**:
  ```json
  {
    "visitor_name": "Jane Smith",
    "mobile": "+91 9876543210",
    "email": "jane@acme.com",
    "company": "Acme Corp",
    "visit_purpose": "Product Demonstration",
    "host_name": "TechNova Host"
  }
  ```
* **Success Response (201 Created)**:
  ```json
  {
    "success": true,
    "message": "Visitor registered via mobile successfully",
    "data": {
      "id": 15,
      "visitor_name": "Jane Smith",
      "company": "Acme Corp",
      "mobile": "+91 9876543210",
      "email": "jane@acme.com",
      "visit_purpose": "Product Demonstration",
      "host_client_id": 1,
      "host_name": "TechNova Host",
      "pass_code": "VIS-A8D3F90B",
      "status": "PENDING",
      "created_at": "2026-06-19 18:45:00",
      "updated_at": "2026-06-19 18:45:00"
    }
  }
  ```

##### 2. Approve/Reject Visitor Check-in (Host Authorization):
Tenant Admins or Hosts can approve or reject security gate check-in alerts directly from the mobile app.
* **Endpoint**: `POST /mobile/visitors/{id}/approve`
* **Request Payload**:
  ```json
  {
    "status": "APPROVED"
  }
  ```
* **Success Response (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Visitor status updated to APPROVED successfully",
    "data": {
      "id": 15,
      "visitor_name": "Jane Smith",
      "company": "Acme Corp",
      "status": "APPROVED",
      "approved_by": 5,
      "updated_at": "2026-06-19 18:47:12"
    }
  }
  ```

##### 3. List Registered Visitors:
* **Endpoint**: `GET /mobile/visitors`
* **Success Response (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Visitors fetched",
    "data": [
      {
        "id": 15,
        "visitor_name": "Jane Smith",
        "company": "Acme Corp",
        "pass_code": "VIS-A8D3F90B",
        "status": "PENDING"
      }
    ]
  }
  ```

---

### 2.6 Billing & Invoices Screen

Enables tenant administrators to view contract billing details, utility adjustments, and pay invoices.

#### UI & Screen Contents:
- **Total Outstanding Card**: Displays sum of unpaid billing amounts with a "Pay Now" Razorpay trigger button.
- **Invoice List Feed**:
  - Invoice Number & Billing Cycle date range.
  - Amount and Breakdown (Lease, Utility, Parking, Meeting Room).
  - Status Chip: `PENDING` (Orange) or `PAID` (Green).

#### API Integration:
* **Endpoint**: `GET /mobile/invoices`
* **Success Response (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Invoices fetched",
    "data": [
      {
        "id": 1,
        "invoice_no": "INV-2026-001",
        "total_amount": 250750.00,
        "status": "PENDING",
        "created_at": "2026-06-01 00:00:00"
      }
    ]
  }
  ```

---

### 2.7 Service Request / Facility Helpdesk Screen

Allows occupants to raise support tickets for housekeeping, cafeteria, IT network, security, or maintenance requests directly from their phones.

#### UI & Screen Contents:
- **Ticket List View**: Past tickets raised by user with details (Ticket No, Type, Creation Date, Status).
- **Raise New Ticket Form**:
  - `Request Type` (Dropdown: IT Support, Cleaning, Electrical, HVAC, Plumbing)
  - `Description` (Multi-line text input)
- **Submit Button**: Launches a spinner, raises the ticket, and adds it to the list.

#### API Integration:

##### 1. Fetch Raised Service Tickets:
* **Endpoint**: `GET /mobile/service-requests`
* **Success Response (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Service requests fetched",
    "data": [
      {
        "id": 8,
        "request_no": "REQ-4721",
        "request_type": "IT Support",
        "description": "WiFi disconnects every 10 minutes",
        "status": "OPEN"
      }
    ]
  }
  ```

##### 2. Create Service Ticket:
* **Endpoint**: `POST /mobile/service-request`
* **Request Payload**:
  ```json
  {
    "request_type": "IT Support",
    "description": "Conference Room A projector isn't turning on."
  }
  ```
* **Success Response (201 Created)**:
  ```json
  {
    "success": true,
    "message": "Service request raised via mobile",
    "data": {
      "id": 9,
      "request_no": "REQ-1594",
      "client_id": 1,
      "request_type": "IT Support",
      "description": "Conference Room A projector isn't turning on.",
      "raised_by": 5,
      "status": "OPEN",
      "created_at": "2026-06-19 18:45:00",
      "updated_at": "2026-06-19 18:45:00"
    }
  }
  ```

---

### 2.8 Announcements & Community Events Screen

Engages members by broadcasting building-wide management bulletins and social events.

#### UI & Screen Contents:
- **Segmented Controls (Tabs)**: `Announcements` / `Upcoming Events`
- **Announcements Feed**:
  - Title, Date, Content snippet, and "Read More" drawer.
- **Events Calendar**:
  - Interactive cards listing event name, calendar icon, start/end dates, host profile, and registration toggle.

#### API Integration:

##### 1. Fetch Community Announcements:
* **Endpoint**: `GET /mobile/announcements`
* **Success Response (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Announcements fetched",
    "data": [
      {
        "id": 1,
        "title": "Facility maintenance scheduled for Sunday",
        "content": "Please note that the main power line will undergo maintenance this Sunday between 9 AM and 3 PM."
      }
    ]
  }
  ```

##### 2. Fetch Community Events:
* **Endpoint**: `GET /mobile/events`
* **Success Response (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Events fetched",
    "data": [
      {
        "id": 3,
        "title": "Community Networking Mixer",
        "event_date": "2026-06-30",
        "start_time": "18:00:00",
        "location": "Rooftop Lounge"
      }
    ]
  }
  ```
