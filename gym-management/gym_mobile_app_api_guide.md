# Gym Management — Mobile App Development Guide

> **Purpose:** Screen-by-screen API mapping for Flutter / React Native mobile app development.
> **API Base URL:** `{YOUR_SITE}/wp-json/gym/v1`
> **Auth:** JWT Bearer Token (HS256, 7-day expiry)

---

## Quick Reference — All 35 APIs

| # | Method | Endpoint | Auth | Used On Screen |
|:--|:--|:--|:--|:--|
| 1 | `POST` | `/auth/login` | ❌ | Login |
| 2 | `GET` | `/auth/me` | ✅ | Splash, Profile |
| 3 | `POST` | `/auth/logout` | ✅ | Profile / Settings |
| 4 | `GET` | `/dashboard/stats` | ✅ | Dashboard |
| 5 | `GET` | `/members` | ✅ | Member List |
| 6 | `GET` | `/members/{id}` | ✅ | Member Detail |
| 7 | `POST` | `/members` | ✅ | Add Member |
| 8 | `PUT` | `/members/{id}` | ✅ | Edit Member |
| 9 | `DELETE` | `/members/{id}` | ✅ | Member Detail |
| 10 | `GET` | `/trainers` | ✅ | Trainer List |
| 11 | `POST` | `/trainers` | ✅ | Add Trainer |
| 12 | `DELETE` | `/trainers/{id}` | ✅ | Trainer List |
| 13 | `GET` | `/plans` | ✅ | Plan List, Assign Membership |
| 14 | `POST` | `/plans` | ✅ | Add Plan |
| 15 | `GET` | `/memberships` | ✅ | Membership List |
| 16 | `GET` | `/memberships/expiring` | ✅ | Dashboard, Expiring Alert |
| 17 | `POST` | `/memberships` | ✅ | Assign Membership |
| 18 | `GET` | `/payments` | ✅ | Payment List |
| 19 | `POST` | `/payments` | ✅ | Record Payment |
| 20 | `GET` | `/diet-plans` | ✅ | Diet Plan List |
| 21 | `POST` | `/diet-plans` | ✅ | Assign Diet Plan |
| 22 | `GET` | `/attendance` | ✅ | Attendance List |
| 23 | `POST` | `/attendance` | ✅ | Mark Attendance |
| 24 | `GET` | `/workout-plans` | ✅ | Workout Plan List |
| 25 | `GET` | `/workout-plans/{id}` | ✅ | Workout Plan Detail |
| 26 | `POST` | `/workout-plans` | ✅ | Create Workout Plan |
| 27 | `PUT` | `/workout-plans/{id}` | ✅ | Edit Workout Plan |
| 28 | `DELETE` | `/workout-plans/{id}` | ✅ | Workout Plan Detail |
| 29 | `GET` | `/equipment` | ✅ | Equipment List |
| 30 | `GET` | `/equipment/{id}` | ✅ | Equipment Detail |
| 31 | `GET` | `/equipment/summary` | ✅ | Equipment Summary |
| 32 | `POST` | `/equipment` | ✅ | Add Equipment |
| 33 | `PUT` | `/equipment/{id}` | ✅ | Edit Equipment |
| 34 | `DELETE` | `/equipment/{id}` | ✅ | Equipment Detail |
| 35 | `POST` | `/equipment/{id}/maintenance` | ✅ | Equipment Detail |

---

## App Navigation Structure

```
App Launch
├── Splash Screen (auto-login check)
│   ├── Token exists → GET /auth/me → Dashboard
│   └── No token → Login Screen
│
├── Login Screen
│   └── POST /auth/login → Dashboard
│
└── Main App (Bottom Navigation — 5 Tabs)
    ├── 🏠 Tab 1: Dashboard
    ├── 👥 Tab 2: Members
    ├── ✅ Tab 3: Attendance (FAB Check-In button)
    ├── 💰 Tab 4: Payments
    └── ☰ Tab 5: More Menu
        ├── Trainers
        ├── Plans
        ├── Memberships
        ├── Diet Plans
        ├── Workout Plans
        ├── Equipment
        ├── Profile
        └── Logout
```

---

## Screen-by-Screen API Guide

---

### SCREEN 1: Splash Screen

**Purpose:** Check if user has a saved token and validate it.

**Flow:**
1. Read JWT token from secure storage
2. If token exists → call API to verify
3. If valid → go to Dashboard
4. If invalid/missing → go to Login

**API Call:**

```
GET /auth/me
Header: Authorization: Bearer {saved_token}
```

**Success Response (200) — Go to Dashboard:**
```json
{
  "success": true,
  "message": "User.",
  "data": {
    "id": 2,
    "username": "gymadmin",
    "name": "Gym Admin",
    "role": "gym_admin"
  }
}
```

**Failure (401) — Go to Login:**
```json
{
  "code": "jwt_invalid",
  "message": "Invalid or expired Token",
  "data": { "status": 401 }
}
```

**UI Notes:**
- Show app logo centered with a loading spinner below
- Auto-navigate after API response (no user action needed)
- If network error → go to Login (don't crash)

---

### SCREEN 2: Login Screen

**Purpose:** User enters credentials to get JWT token.

**UI Elements:**
- App logo at top
- Username text field
- Password text field (with show/hide toggle)
- "Login" button
- Loading state on button while API call

**API Call:**

```
POST /auth/login
Content-Type: application/json

{
  "username": "gymadmin",
  "password": "123456"
}
```

| Field | Type | Required | Validation |
|:--|:--|:--|:--|
| `username` | string | ✅ | Cannot be empty |
| `password` | string | ✅ | Cannot be empty |

**Success Response (200):**
```json
{
  "success": true,
  "message": "Login successful.",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjoyLCJ1c2VybmFtZSI6Imd5bWFkbWluIiwiZXhwIjoxNzUwOTI3MjAwfQ.abc123",
    "user": {
      "id": 2,
      "username": "gymadmin",
      "email": "admin@gym.local",
      "name": "Gym Admin",
      "role": "gym_admin",
      "status": "APPROVED"
    }
  }
}
```

**After Success:**
1. Save `data.token` to secure storage
2. Save `data.user` to local state (for role-based UI)
3. Navigate to Dashboard

**Error Response (401) — Wrong credentials:**
```json
{
  "success": false,
  "message": "Invalid username or password.",
  "data": []
}
```

**Error Response (403) — Account blocked:**
```json
{
  "success": false,
  "message": "Account pending or blocked",
  "data": []
}
```

**UI Notes:**
- Show `response.message` as a snackbar/toast on error
- Disable login button while loading
- Pre-filled test credentials for dev: `gymadmin` / `123456`

**Test Accounts:**

| Username | Password | Role |
|:--|:--|:--|
| `gymadmin` | `123456` | Admin (full access) |
| `gymstaff` | `123456` | Staff (limited access) |

---

### SCREEN 3: Dashboard

**Purpose:** Show live gym stats, expiring memberships, recent payments.

**API Call:**

```
GET /dashboard/stats
Header: Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Dashboard stats.",
  "data": {
    "summary": {
      "total_members": 125,
      "total_trainers": 8,
      "attendance_today": 47,
      "revenue_today": 15000.00
    },
    "expiring_soon": [
      {
        "id": 45,
        "member_id": 12,
        "plan_id": 2,
        "trainer_id": 3,
        "start_date": "2026-03-18",
        "end_date": "2026-06-22",
        "status": "Active",
        "created_at": "2026-03-18 10:00:00",
        "deleted_at": null,
        "member_name": "Rahul Sharma",
        "plan_name": "3 Months Quarterly Plan"
      }
    ],
    "recent_payments": [
      {
        "id": 89,
        "invoice_number": "INV-6671A2B3C4D5E",
        "member_id": 12,
        "membership_id": 45,
        "amount": "2500.00",
        "payment_date": "2026-06-18",
        "payment_mode": "UPI",
        "status": "Paid",
        "notes": null,
        "collected_by": 2,
        "created_at": "2026-06-18 10:30:00",
        "deleted_at": null,
        "member_name": "Rahul Sharma"
      }
    ]
  }
}
```

**UI Layout:**

```
┌──────────────────────────────────────┐
│  📊 Dashboard                        │
├──────────┬──────────┐                │
│ 👥 125   │ 🏋️ 8     │  ← Stat Cards  │
│ Members  │ Trainers │                │
├──────────┼──────────┤                │
│ ✅ 47    │ 💰₹15K   │                │
│ Today    │ Revenue  │                │
├──────────┴──────────┘                │
│                                      │
│ ⚠️ Expiring Soon (7 days)            │
│ ┌──────────────────────────────────┐ │
│ │ Rahul Sharma                     │ │
│ │ 3 Months Plan · Expires Jun 22  │ │
│ └──────────────────────────────────┘ │
│                                      │
│ 💳 Recent Payments                   │
│ ┌──────────────────────────────────┐ │
│ │ Rahul Sharma     ₹2,500         │ │
│ │ INV-6671A2...    UPI · Jun 18   │ │
│ └──────────────────────────────────┘ │
└──────────────────────────────────────┘
```

**UI Notes:**
- 4 stat cards in a 2×2 grid at top
- `expiring_soon` list below (show max 5, "See All" → Expiring screen)
- `recent_payments` list below that (show max 5)
- Pull-to-refresh → re-call `GET /dashboard/stats`
- Revenue card should format as currency: `₹15,000`
- If `expiring_soon` is empty, show "No memberships expiring soon" message

---

### SCREEN 4: Member List

**Purpose:** Paginated, searchable list of all gym members.

**API Call:**

```
GET /members?page=1&limit=15&search=rahul&orderby=name&order=ASC
Header: Authorization: Bearer {token}
```

**Query Parameters:**

| Param | Type | Default | Description |
|:--|:--|:--|:--|
| `page` | int | `1` | Page number |
| `limit` | int | `10` | Items per page (use 15 for mobile) |
| `search` | string | — | Searches: `member_id`, `name`, `mobile` |
| `orderby` | string | `id` | Sort by: `name` or `member_id` |
| `order` | string | `DESC` | `ASC` or `DESC` |

**Success Response (200):**
```json
{
  "success": true,
  "message": "Members.",
  "data": {
    "data": [
      {
        "id": 1,
        "member_id": "MEM-00001",
        "name": "Rahul Sharma",
        "mobile": "9876543210",
        "email": "rahul@email.com",
        "dob": "1995-08-15",
        "gender": "Male",
        "blood_group": "O+",
        "address": "123 Main Street, Jaipur",
        "emergency_contact_name": "Suresh Sharma",
        "emergency_contact_number": "9876543211",
        "join_date": "2026-01-15",
        "height_cm": "175.50",
        "weight_kg": "78.00",
        "medical_history": "None",
        "status": "Active",
        "created_at": "2026-01-15 09:00:00",
        "deleted_at": null
      }
    ],
    "total": 125,
    "page": 1,
    "limit": 15,
    "pages": 9
  }
}
```

**UI Layout:**

```
┌──────────────────────────────────────┐
│ 👥 Members                    [+ Add]│
├──────────────────────────────────────┤
│ 🔍 Search members...                │
├──────────────────────────────────────┤
│ ┌──────────────────────────────────┐ │
│ │ 👤 Rahul Sharma      MEM-00001  │ │
│ │    📱 9876543210   Active ✅     │ │
│ ├──────────────────────────────────┤ │
│ │ 👤 Priya Patel       MEM-00002  │ │
│ │    📱 9876543211   Active ✅     │ │
│ └──────────────────────────────────┘ │
│        ← Loading more... →          │
└──────────────────────────────────────┘
```

**UI Notes:**
- Search bar at top with debounce (300ms delay before API call)
- Infinite scroll pagination: when user reaches bottom, load next page
- Show member avatar (first letter of name), name, member_id, mobile, status badge
- Tap on member → navigate to Member Detail screen
- FAB (+) button → navigate to Add Member screen
- Pull-to-refresh → reload page 1
- Show total count: "125 Members" in header

---

### SCREEN 5: Member Detail

**Purpose:** Full profile view of a single member with action buttons.

**API Call:**

```
GET /members/1
Header: Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Member.",
  "data": {
    "id": 1,
    "member_id": "MEM-00001",
    "name": "Rahul Sharma",
    "mobile": "9876543210",
    "email": "rahul@email.com",
    "dob": "1995-08-15",
    "gender": "Male",
    "blood_group": "O+",
    "address": "123 Main Street, Jaipur",
    "emergency_contact_name": "Suresh Sharma",
    "emergency_contact_number": "9876543211",
    "join_date": "2026-01-15",
    "height_cm": "175.50",
    "weight_kg": "78.00",
    "medical_history": "None",
    "status": "Active",
    "created_at": "2026-01-15 09:00:00",
    "deleted_at": null
  }
}
```

**UI Layout:**

```
┌──────────────────────────────────────┐
│ ← Member Detail          [✏️] [🗑️]  │
├──────────────────────────────────────┤
│         👤 (Large Avatar)            │
│        Rahul Sharma                  │
│        MEM-00001 · Active            │
├──────────────────────────────────────┤
│ Personal Info                        │
│ Mobile      : 9876543210 [📞] [💬]   │
│ Email       : rahul@email.com        │
│ DOB         : 15 Aug 1995            │
│ Gender      : Male                   │
│ Blood Group : O+                     │
├──────────────────────────────────────┤
│ Physical Info                        │
│ Height : 175.5 cm                    │
│ Weight : 78.0 kg                     │
│ BMI    : 25.3 (Calculated)           │
├──────────────────────────────────────┤
│ Emergency Contact                    │
│ Name   : Suresh Sharma               │
│ Phone  : 9876543211 [📞]             │
├──────────────────────────────────────┤
│ Address                              │
│ 123 Main Street, Jaipur              │
├──────────────────────────────────────┤
│ Medical History                      │
│ None                                 │
├──────────────────────────────────────┤
│ Quick Actions                        │
│ [📋 Assign Plan] [💪 Workout Plan]   │
│ [🥗 Diet Plan]   [✅ Mark Attendance]│
└──────────────────────────────────────┘
```

**Quick Actions (navigate to other screens with `member_id` pre-filled):**
- "Assign Plan" → Assign Membership screen (pre-fill `member_id`)
- "Workout Plan" → Create Workout Plan screen (pre-fill `member_id`)
- "Diet Plan" → Assign Diet Plan screen (pre-fill `member_id`)
- "Mark Attendance" → Call `POST /attendance` directly

**Delete Member:**

```
DELETE /members/1
Header: Authorization: Bearer {token}
```

**Response:**
```json
{ "success": true, "message": "Deleted." }
```

**UI Notes:**
- Calculate BMI: `weight_kg / (height_cm/100)²` — show it even though API doesn't return it
- Phone tap → open dialer; message icon → open SMS/WhatsApp
- Edit icon → navigate to Edit Member form (pre-filled)
- Delete icon → show confirmation dialog first, then call DELETE API
- After delete → navigate back to Member List with refresh

---

### SCREEN 6: Add Member Form

**Purpose:** Create a new gym member.

**API Call:**

```
POST /members
Header: Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Rahul Sharma",
  "mobile": "9876543210",
  "email": "rahul@email.com",
  "gender": "Male",
  "dob": "1995-08-15",
  "address": "123 Main Street, Jaipur",
  "height_cm": 175.5,
  "weight_kg": 78.0,
  "medical_history": "None"
}
```

**Form Fields:**

| Field | Label | Type | Required | Input Widget |
|:--|:--|:--|:--|:--|
| `name` | Full Name | string | ✅ | Text Input |
| `mobile` | Mobile Number | string | ✅ | Phone Input (numeric keyboard) |
| `email` | Email | string | ❌ | Email Input |
| `gender` | Gender | string | ❌ | Dropdown: Male, Female, Other |
| `dob` | Date of Birth | date | ❌ | Date Picker (YYYY-MM-DD format) |
| `address` | Address | string | ❌ | Multiline Text Input |
| `height_cm` | Height (cm) | decimal | ❌ | Numeric Input |
| `weight_kg` | Weight (kg) | decimal | ❌ | Numeric Input |
| `medical_history` | Medical History | string | ❌ | Multiline Text Input |

**Success Response (200):**
```json
{
  "success": true,
  "message": "Created.",
  "data": {
    "id": 126,
    "member_id": "MEM-00126"
  }
}
```

**After Success:**
- Show success snackbar with member_id: "Member MEM-00126 created!"
- Navigate back to Member List (refresh it)
- OR navigate to Member Detail screen for the new member

**Error Response (400):**
```json
{ "success": false, "message": "Name and Mobile required.", "data": [] }
```

---

### SCREEN 7: Edit Member Form

**Purpose:** Update existing member details.

**API Call:**

```
PUT /members/1
Header: Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Rahul K. Sharma",
  "weight_kg": 76.5,
  "address": "456 New Street, Jaipur"
}
```

> **Important:** Send ONLY the fields that changed. No need to send all fields.

**Updatable Fields:** `name`, `mobile`, `email`, `gender`, `dob`, `address`, `height_cm`, `weight_kg`, `medical_history`

**Success Response (200):**
```json
{ "success": true, "message": "Updated." }
```

**UI Notes:**
- Pre-fill form with data from `GET /members/{id}`
- Same form layout as Add Member
- On save, compare fields and send only changed ones
- After success → navigate back to Member Detail (refresh)

---

### SCREEN 8: Trainer List

**Purpose:** List all gym trainers.

**API Call:**

```
GET /trainers?page=1&limit=15&search=vikram
Header: Authorization: Bearer {token}
```

**Query Parameters:**

| Param | Type | Default | Searchable Columns |
|:--|:--|:--|:--|
| `search` | string | — | `name`, `specialization` |
| `orderby` | string | `id` | `name` |
| `page` | int | `1` | — |
| `limit` | int | `10` | — |

**Success Response (200):**
```json
{
  "success": true,
  "message": "Trainers.",
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Vikram Singh",
        "mobile": "9998887776",
        "email": "vikram@gym.local",
        "specialization": "Weight Training",
        "salary": "25000.00",
        "join_date": "2026-01-01",
        "status": "Active",
        "created_at": "2026-01-01 09:00:00",
        "deleted_at": null
      }
    ],
    "total": 8,
    "page": 1,
    "limit": 15,
    "pages": 1
  }
}
```

**UI Layout:**

```
┌──────────────────────────────────────┐
│ 🏋️ Trainers                   [+ Add]│
├──────────────────────────────────────┤
│ 🔍 Search trainers...               │
├──────────────────────────────────────┤
│ ┌──────────────────────────────────┐ │
│ │ 👤 Vikram Singh                  │ │
│ │ 🏷️ Weight Training  📱9998887776│ │
│ │ 💰 ₹25,000/month    Active ✅   │ │
│ │                         [🗑️ Del] │ │
│ └──────────────────────────────────┘ │
└──────────────────────────────────────┘
```

**Delete Trainer:**

```
DELETE /trainers/1
Header: Authorization: Bearer {token}
```

**Response:**
```json
{ "success": true, "message": "Deleted." }
```

**UI Notes:**
- Swipe-to-delete or delete icon on each card
- Show confirmation dialog before delete
- Specialization as a colored badge/chip
- After delete → refresh list

---

### SCREEN 9: Add Trainer Form

**Purpose:** Create a new trainer.

**API Call:**

```
POST /trainers
Header: Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Vikram Singh",
  "mobile": "9998887776",
  "email": "vikram@gym.local",
  "specialization": "Weight Training",
  "salary": 25000
}
```

**Form Fields:**

| Field | Label | Type | Required | Input Widget |
|:--|:--|:--|:--|:--|
| `name` | Full Name | string | ✅ | Text Input |
| `mobile` | Mobile | string | ❌ | Phone Input |
| `email` | Email | string | ❌ | Email Input |
| `specialization` | Specialization | string | ❌ | Dropdown: Weight Training, Cardio, Yoga, CrossFit, Zumba, Personal Training, Other |
| `salary` | Monthly Salary | decimal | ❌ | Numeric Input |

**Success Response (200):**
```json
{ "success": true, "message": "Created.", "data": { "id": 9 } }
```

**UI Notes:**
- `join_date` is auto-set to today by the server — no form field needed
- After success → navigate back to Trainer List (refresh)

---

### SCREEN 10: Plan List

**Purpose:** View all membership plans/packages.

**API Call:**

```
GET /plans?page=1&limit=20
Header: Authorization: Bearer {token}
```

**Search Column:** `name`

**Success Response (200):**
```json
{
  "success": true,
  "message": "Plans.",
  "data": {
    "data": [
      {
        "id": 1,
        "name": "1 Month Monthly Plan",
        "duration_days": 30,
        "price": "1000.00",
        "description": null,
        "is_active": 1,
        "created_at": "2026-01-01 00:00:00",
        "deleted_at": null
      },
      {
        "id": 2,
        "name": "3 Months Quarterly Plan",
        "duration_days": 90,
        "price": "2500.00",
        "description": null,
        "is_active": 1,
        "created_at": "2026-01-01 00:00:00",
        "deleted_at": null
      },
      {
        "id": 3,
        "name": "6 Months Half-Yearly Plan",
        "duration_days": 180,
        "price": "4500.00",
        "description": null,
        "is_active": 1,
        "created_at": "2026-01-01 00:00:00",
        "deleted_at": null
      },
      {
        "id": 4,
        "name": "1 Year Annual Plan",
        "duration_days": 365,
        "price": "8000.00",
        "description": null,
        "is_active": 1,
        "created_at": "2026-01-01 00:00:00",
        "deleted_at": null
      }
    ],
    "total": 4,
    "page": 1,
    "limit": 20,
    "pages": 1
  }
}
```

**UI Layout:**

```
┌──────────────────────────────────────┐
│ 📦 Plans                      [+ Add]│
├──────────────────────────────────────┤
│ ┌──────────────┐ ┌──────────────┐   │
│ │ 1 Month      │ │ 3 Months     │   │
│ │ ₹1,000       │ │ ₹2,500       │   │  ← Card Grid
│ │ 30 days      │ │ 90 days      │   │
│ └──────────────┘ └──────────────┘   │
│ ┌──────────────┐ ┌──────────────┐   │
│ │ 6 Months     │ │ 1 Year       │   │
│ │ ₹4,500       │ │ ₹8,000       │   │
│ │ 180 days     │ │ 365 days     │   │
│ └──────────────┘ └──────────────┘   │
└──────────────────────────────────────┘
```

**UI Notes:**
- Show as card grid (2 columns)
- Each card: plan name, price (formatted ₹), duration in days
- Calculate per-day cost: `price / duration_days` and show (e.g., "₹33/day")

---

### SCREEN 11: Add Plan Form

**Purpose:** Create a new membership plan.

**API Call:**

```
POST /plans
Header: Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "2 Months Bi-Monthly Plan",
  "duration_days": 60,
  "price": 1800,
  "description": "Two-month membership with full access"
}
```

**Form Fields:**

| Field | Label | Type | Required |
|:--|:--|:--|:--|
| `name` | Plan Name | string | ✅ |
| `duration_days` | Duration (days) | integer | ✅ |
| `price` | Price (₹) | decimal | ❌ |
| `description` | Description | string | ❌ |

**Success Response (200):**
```json
{ "success": true, "message": "Created.", "data": { "id": 5 } }
```

---

### SCREEN 12: Active Memberships List

**Purpose:** View all currently active memberships.

**API Call:**

```
GET /memberships
Header: Authorization: Bearer {token}
```

> **Note:** This API auto-expires old memberships before returning results. No pagination — returns flat array.

**Success Response (200):**
```json
{
  "success": true,
  "message": "Memberships.",
  "data": [
    {
      "id": 45,
      "member_id": 12,
      "plan_id": 2,
      "trainer_id": 3,
      "start_date": "2026-03-18",
      "end_date": "2026-06-22",
      "status": "Active",
      "created_at": "2026-03-18 10:00:00",
      "deleted_at": null,
      "member_name": "Rahul Sharma",
      "member_code": "MEM-00012",
      "plan_name": "3 Months Quarterly Plan"
    }
  ]
}
```

> ⚠️ **Important:** `data` is a **flat array** here, NOT the paginated `{data, total, page}` object. Handle this differently in your model.

**UI Layout:**

```
┌──────────────────────────────────────┐
│ 🎫 Active Memberships         [+ New]│
├──────────────────────────────────────┤
│ ┌──────────────────────────────────┐ │
│ │ Rahul Sharma        MEM-00012   │ │
│ │ 📦 3 Months Quarterly Plan      │ │
│ │ 📅 Mar 18 → Jun 22, 2026       │ │
│ │ ⏳ 4 days remaining              │ │
│ └──────────────────────────────────┘ │
└──────────────────────────────────────┘
```

**UI Notes:**
- Calculate "X days remaining" from `end_date - today`
- Color code: Green (>30 days), Yellow (7-30 days), Red (<7 days)
- (+) button → navigate to Assign Membership screen
- Pull-to-refresh to reload

---

### SCREEN 13: Expiring Memberships

**Purpose:** Alert view showing memberships expiring within 7 days.

**API Call:**

```
GET /memberships/expiring
Header: Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Expiring Soon.",
  "data": [
    {
      "id": 45,
      "member_id": 12,
      "plan_id": 2,
      "trainer_id": 3,
      "start_date": "2026-03-18",
      "end_date": "2026-06-22",
      "status": "Active",
      "member_name": "Rahul Sharma",
      "member_code": "MEM-00012",
      "plan_name": "3 Months Quarterly Plan",
      "mobile": "9876543210"
    }
  ]
}
```

> **Note:** This response includes `mobile` field (the member list doesn't). Use it for quick call/message actions.

**UI Notes:**
- Show each entry with a red/orange urgency indicator
- Quick action buttons: 📞 Call, 💬 WhatsApp (use `mobile` field)
- "Renew" button → navigate to Assign Membership (pre-fill `member_id`)
- Also accessible from Dashboard's "Expiring Soon" section

---

### SCREEN 14: Assign Membership

**Purpose:** Assign a plan to a member with optional payment.

**Pre-requisite API Calls (to populate dropdowns):**

```
GET /members?limit=100    → For member dropdown
GET /plans?limit=100      → For plan dropdown
GET /trainers?limit=100   → For trainer dropdown (optional)
```

**API Call:**

```
POST /memberships
Header: Authorization: Bearer {token}
Content-Type: application/json

{
  "member_id": 12,
  "plan_id": 2,
  "trainer_id": 3,
  "start_date": "2026-06-18",
  "end_date": "2026-09-18",
  "amount_paid": 2500,
  "payment_mode": "UPI"
}
```

**Form Fields:**

| Field | Label | Type | Required | Input Widget |
|:--|:--|:--|:--|:--|
| `member_id` | Select Member | integer | ✅ | Searchable Dropdown (from /members) |
| `plan_id` | Select Plan | integer | ✅ | Dropdown (from /plans) |
| `start_date` | Start Date | date | ✅ | Date Picker |
| `end_date` | End Date | date | ✅ | Date Picker (auto-calculate from plan) |
| `trainer_id` | Assign Trainer | integer | ❌ | Dropdown (from /trainers) |
| `amount_paid` | Payment Amount | decimal | ❌ | Numeric Input (pre-fill plan price) |
| `payment_mode` | Payment Mode | string | ❌ | Dropdown: Cash, UPI, Card, Online |

**Smart Auto-Fill Logic:**
1. When user selects a **Plan**, auto-calculate `end_date` = `start_date` + `plan.duration_days`
2. When user selects a **Plan**, auto-fill `amount_paid` with `plan.price`
3. If `member_id` comes from Member Detail screen, pre-fill it

**Success Response (200):**
```json
{ "success": true, "message": "Membership assigned.", "data": { "id": 46 } }
```

> **Bonus:** When `amount_paid` is provided, the API automatically creates a Payment record too. No need to call `/payments` separately.

---

### SCREEN 15: Payment List

**Purpose:** View all payment records with invoice search.

**API Call:**

```
GET /payments?page=1&limit=15&search=INV-667
Header: Authorization: Bearer {token}
```

**Query Parameters:**

| Param | Searchable Columns | Sortable |
|:--|:--|:--|
| `search` | `invoice_number` | `invoice_number`, `payment_date` |

**Success Response (200):**
```json
{
  "success": true,
  "message": "Payments.",
  "data": {
    "data": [
      {
        "id": 89,
        "invoice_number": "INV-6671A2B3C4D5E",
        "member_id": 12,
        "membership_id": 45,
        "amount": "2500.00",
        "payment_date": "2026-06-18",
        "payment_mode": "UPI",
        "status": "Paid",
        "notes": null,
        "collected_by": null,
        "created_at": "2026-06-18 10:30:00",
        "deleted_at": null
      }
    ],
    "total": 89,
    "page": 1,
    "limit": 15,
    "pages": 6
  }
}
```

**UI Layout:**

```
┌──────────────────────────────────────┐
│ 💰 Payments                  [+ New] │
├──────────────────────────────────────┤
│ 🔍 Search by invoice number...      │
├──────────────────────────────────────┤
│ ┌──────────────────────────────────┐ │
│ │ INV-6671A2B3C4D5E               │ │
│ │ ₹2,500.00        💳 UPI         │ │
│ │ 📅 Jun 18, 2026   ✅ Paid       │ │
│ └──────────────────────────────────┘ │
└──────────────────────────────────────┘
```

**UI Notes:**
- Infinite scroll pagination
- Search by invoice number
- Payment mode icon: 💵 Cash, 📱 UPI, 💳 Card, 🌐 Online
- Each card shows: invoice number, amount, date, mode, status badge

---

### SCREEN 16: Record Payment Form

**Purpose:** Record a standalone payment for a member.

**API Call:**

```
POST /payments
Header: Authorization: Bearer {token}
Content-Type: application/json

{
  "member_id": 12,
  "membership_id": 45,
  "amount": 2500,
  "payment_date": "2026-06-18",
  "payment_mode": "UPI"
}
```

**Form Fields:**

| Field | Label | Type | Required | Input Widget |
|:--|:--|:--|:--|:--|
| `member_id` | Select Member | integer | ✅ | Searchable Dropdown |
| `amount` | Amount (₹) | decimal | ✅ | Numeric Input |
| `membership_id` | Membership | integer | ❌ | Dropdown (memberships of selected member) |
| `payment_date` | Payment Date | date | ❌ | Date Picker (default: today) |
| `payment_mode` | Payment Mode | string | ❌ | Dropdown: Cash, UPI, Card, Online |

**Success Response (200):**
```json
{ "success": true, "message": "Payment recorded.", "data": { "id": 90 } }
```

**UI Notes:**
- Invoice number is auto-generated by the server — no form field needed
- Default `payment_date` to today
- Default `payment_mode` to "Cash"

---

### SCREEN 17: Diet Plan List

**Purpose:** View all assigned diet plans.

**API Call:**

```
GET /diet-plans?page=1&limit=15
Header: Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Diet Plans.",
  "data": {
    "data": [
      {
        "id": 5,
        "member_id": 12,
        "trainer_id": 3,
        "plan_details": "Breakfast: Oats + Banana\nLunch: Rice + Dal + Salad\nDinner: Grilled Chicken + Vegetables\nSnacks: Almonds, Protein Shake",
        "assigned_date": "2026-06-18",
        "notes": "High protein diet for muscle gain",
        "created_at": "2026-06-18 11:00:00",
        "deleted_at": null
      }
    ],
    "total": 5,
    "page": 1,
    "limit": 15,
    "pages": 1
  }
}
```

**UI Notes:**
- Show `plan_details` truncated (first 2 lines) in the list card
- Tap to expand and see full diet plan details
- Show `assigned_date` and `notes` if available
- `member_id` and `trainer_id` are raw IDs — you may want to cross-reference with member/trainer names from local cache

---

### SCREEN 18: Assign Diet Plan Form

**Purpose:** Assign a diet plan to a member.

**API Call:**

```
POST /diet-plans
Header: Authorization: Bearer {token}
Content-Type: application/json

{
  "member_id": 12,
  "trainer_id": 3,
  "plan_details": "Breakfast: Oats + Banana\nLunch: Rice + Dal + Salad\nDinner: Grilled Chicken + Vegetables"
}
```

**Form Fields:**

| Field | Label | Type | Required | Input Widget |
|:--|:--|:--|:--|:--|
| `member_id` | Select Member | integer | ✅ | Searchable Dropdown |
| `plan_details` | Diet Plan Details | string | ✅ | Large Multiline Text (min 5 lines) |
| `trainer_id` | Assigned Trainer | integer | ❌ | Dropdown |

**Success Response (200):**
```json
{ "success": true, "message": "Diet plan assigned.", "data": { "id": 6 } }
```

**UI Notes:**
- `assigned_date` is auto-set to today by the server
- Provide a template hint in the text area:
  ```
  Breakfast: 
  Mid-Morning: 
  Lunch: 
  Evening Snack: 
  Dinner: 
  Pre-Workout: 
  Post-Workout: 
  ```

---

### SCREEN 19: Attendance List

**Purpose:** View attendance records (today's and historical).

**API Call:**

```
GET /attendance?page=1&limit=20&search=Member
Header: Authorization: Bearer {token}
```

**Query Parameters:**

| Param | Searchable Columns |
|:--|:--|
| `search` | `user_type` (filter by "Member" or "Trainer") |

**Success Response (200):**
```json
{
  "success": true,
  "message": "Attendance.",
  "data": {
    "data": [
      {
        "id": 234,
        "user_type": "Member",
        "reference_id": 12,
        "check_in": "2026-06-18 07:30:00",
        "check_out": "2026-06-18 09:15:00",
        "created_at": "2026-06-18 07:30:00"
      },
      {
        "id": 233,
        "user_type": "Trainer",
        "reference_id": 3,
        "check_in": "2026-06-18 06:00:00",
        "check_out": null,
        "created_at": "2026-06-18 06:00:00"
      }
    ],
    "total": 234,
    "page": 1,
    "limit": 20,
    "pages": 12
  }
}
```

**UI Layout:**

```
┌──────────────────────────────────────┐
│ ✅ Attendance              [+ Mark]  │
├──────────────────────────────────────┤
│ [All] [Members] [Trainers]  ← Tabs  │
├──────────────────────────────────────┤
│ ┌──────────────────────────────────┐ │
│ │ 👤 Member #12                    │ │
│ │ ↘️ 07:30 AM  ↗️ 09:15 AM         │ │
│ │ Duration: 1h 45m    ✅ Complete  │ │
│ ├──────────────────────────────────┤ │
│ │ 🏋️ Trainer #3                    │ │
│ │ ↘️ 06:00 AM  ↗️ Still In         │ │
│ │ Duration: —         🟡 Active    │ │
│ └──────────────────────────────────┘ │
└──────────────────────────────────────┘
```

**UI Notes:**
- Tabs/chips to filter: All / Members / Trainers (use `search=Member` or `search=Trainer`)
- `check_out = null` means still checked in → show "Still In" with green dot
- Calculate duration: `check_out - check_in`
- `reference_id` is the member or trainer ID — resolve names from local cache

---

### SCREEN 20: Mark Attendance

**Purpose:** Check-in or check-out a member/trainer.

**API Call:**

```
POST /attendance
Header: Authorization: Bearer {token}
Content-Type: application/json

{
  "user_type": "Member",
  "reference_id": 12
}
```

**Form Fields:**

| Field | Label | Type | Required | Values |
|:--|:--|:--|:--|:--|
| `user_type` | Type | string | ✅ | `"Member"` or `"Trainer"` |
| `reference_id` | Select Person | integer | ✅ | Member ID or Trainer ID |

**Smart Toggle Behavior:**

| Scenario | Response |
|:--|:--|
| 1st call today (no record) | `{ "success": true, "message": "Checked in successfully." }` |
| 2nd call today (checked in, not out) | `{ "success": true, "message": "Checked out successfully." }` |
| 3rd+ call today (already checked out) | `{ "success": false, "message": "Already checked out today." }` |

**UI Layout:**

```
┌──────────────────────────────────────┐
│ ✅ Mark Attendance                   │
├──────────────────────────────────────┤
│                                      │
│   [👥 Member]  [🏋️ Trainer]  ← Toggle│
│                                      │
│   🔍 Search and select person...     │
│                                      │
│   ┌──────────────────────────────┐   │
│   │                              │   │
│   │    ✅  CHECK IN / OUT        │   │  ← Big button
│   │                              │   │
│   └──────────────────────────────┘   │
│                                      │
│   Response: "Checked in successfully"│
└──────────────────────────────────────┘
```

**UI Notes:**
- Toggle between Member/Trainer first
- Then select person from a searchable list
- Big "Check In/Out" button
- Show response message as feedback (green for success, red for error)
- The same button handles both check-in and check-out (server decides)

---

### SCREEN 21: Workout Plan List

**Purpose:** View all workout plans with member and trainer names.

**API Call:**

```
GET /workout-plans?page=1&limit=15
Header: Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Workout plans.",
  "data": {
    "data": [
      {
        "id": 1,
        "member_id": 12,
        "trainer_id": 3,
        "title": "Beginner Muscle Building",
        "goal": "Muscle Gain",
        "level": "Beginner",
        "monday": "Chest: Bench Press 4x12, Incline DB Press 3x10",
        "tuesday": "Back: Deadlift 4x8, Lat Pulldown 3x12",
        "wednesday": "Rest Day / Light Cardio",
        "thursday": "Shoulders: OHP 4x10, Lateral Raise 3x15",
        "friday": "Legs: Squat 4x10, Leg Press 3x12",
        "saturday": "Arms: Bicep Curl 3x12, Tricep Pushdown 3x12",
        "sunday": "Rest Day",
        "notes": "Increase weight by 5% each week",
        "start_date": "2026-06-18",
        "end_date": "2026-09-18",
        "status": "Active",
        "created_at": "2026-06-18 12:00:00",
        "deleted_at": null,
        "member_name": "Rahul Sharma",
        "trainer_name": "Vikram Singh"
      }
    ],
    "total": 10,
    "page": 1,
    "limit": 15,
    "pages": 1
  }
}
```

**UI Layout (List Card):**

```
┌──────────────────────────────────────┐
│ 🏋️ Workout Plans              [+ New]│
├──────────────────────────────────────┤
│ ┌──────────────────────────────────┐ │
│ │ Beginner Muscle Building         │ │
│ │ 👤 Rahul Sharma  🏋️ Vikram Singh│ │
│ │ 🎯 Muscle Gain  📊 Beginner     │ │
│ │ 📅 Jun 18 → Sep 18   Active ✅  │ │
│ └──────────────────────────────────┘ │
└──────────────────────────────────────┘
```

---

### SCREEN 22: Workout Plan Detail

**Purpose:** View full weekly workout schedule.

**API Call:**

```
GET /workout-plans/1
Header: Authorization: Bearer {token}
```

**Response:** Same object as list item (single object, not array)

**UI Layout:**

```
┌──────────────────────────────────────┐
│ ← Workout Plan           [✏️] [🗑️]  │
├──────────────────────────────────────┤
│ Beginner Muscle Building             │
│ 🎯 Muscle Gain · 📊 Beginner        │
│ 👤 Rahul Sharma · 🏋️ Vikram Singh   │
│ 📅 Jun 18 → Sep 18, 2026            │
├──────────────────────────────────────┤
│ [Mon][Tue][Wed][Thu][Fri][Sat][Sun]  │
│ ← Scrollable day tabs                │
├──────────────────────────────────────┤
│ 📅 Monday                            │
│ ──────────────────────────────────── │
│ Chest: Bench Press 4x12             │
│ Incline DB Press 3x10               │
│ Cable Fly 3x12                      │
│ Push-ups 3x failure                 │
├──────────────────────────────────────┤
│ 📝 Notes                             │
│ Increase weight by 5% each week     │
└──────────────────────────────────────┘
```

**UI Notes:**
- Horizontal scrollable day tabs (Mon-Sun)
- Tap on a day → show that day's exercises
- If day field is empty → show "Rest Day" or "No exercises planned"
- Edit button → Edit Workout Plan form (pre-filled)
- Delete button → confirm dialog → `DELETE /workout-plans/{id}`

**Delete:**
```
DELETE /workout-plans/1
```
Response: `{ "success": true, "message": "Deleted." }`

---

### SCREEN 23: Create Workout Plan Form

**Purpose:** Create a weekly workout plan for a member.

**API Call:**

```
POST /workout-plans
Header: Authorization: Bearer {token}
Content-Type: application/json

{
  "member_id": 12,
  "trainer_id": 3,
  "title": "Beginner Muscle Building",
  "goal": "Muscle Gain",
  "level": "Beginner",
  "monday": "Chest: Bench Press 4x12, Incline DB Press 3x10",
  "tuesday": "Back: Deadlift 4x8, Lat Pulldown 3x12",
  "wednesday": "Rest Day / Light Cardio",
  "thursday": "Shoulders: OHP 4x10, Lateral Raise 3x15",
  "friday": "Legs: Squat 4x10, Leg Press 3x12",
  "saturday": "Arms: Bicep Curl 3x12, Tricep Pushdown 3x12",
  "sunday": "Rest Day",
  "notes": "Increase weight by 5% each week",
  "start_date": "2026-06-18",
  "end_date": "2026-09-18"
}
```

**Form Fields:**

| Field | Label | Type | Required | Input Widget |
|:--|:--|:--|:--|:--|
| `member_id` | Select Member | integer | ✅ | Searchable Dropdown |
| `title` | Plan Title | string | ✅ | Text Input |
| `trainer_id` | Assign Trainer | integer | ❌ | Dropdown |
| `goal` | Fitness Goal | string | ❌ | Dropdown: General Fitness, Muscle Gain, Weight Loss, Endurance, Flexibility |
| `level` | Level | string | ❌ | Dropdown: Beginner, Intermediate, Advanced |
| `monday` | Monday Exercises | string | ❌ | Multiline Text |
| `tuesday` | Tuesday Exercises | string | ❌ | Multiline Text |
| `wednesday` | Wednesday Exercises | string | ❌ | Multiline Text |
| `thursday` | Thursday Exercises | string | ❌ | Multiline Text |
| `friday` | Friday Exercises | string | ❌ | Multiline Text |
| `saturday` | Saturday Exercises | string | ❌ | Multiline Text |
| `sunday` | Sunday Exercises | string | ❌ | Multiline Text |
| `notes` | Notes | string | ❌ | Multiline Text |
| `start_date` | Start Date | date | ❌ | Date Picker (default: today) |
| `end_date` | End Date | date | ❌ | Date Picker |

**Success Response (201):**
```json
{ "success": true, "message": "Workout plan created.", "data": { "id": 11 } }
```

**UI Notes:**
- Use a multi-step form or scrollable form (many fields)
- Step 1: Basic info (member, title, goal, level, dates)
- Step 2: Day-by-day exercises (scrollable tab view for Mon-Sun)
- Step 3: Notes → Submit

---

### SCREEN 24: Edit Workout Plan

**Purpose:** Update an existing workout plan.

**API Call:**

```
PUT /workout-plans/1
Header: Authorization: Bearer {token}
Content-Type: application/json

{
  "level": "Intermediate",
  "monday": "Chest: Bench Press 5x10, Cable Fly 4x12",
  "notes": "Progressive overload week 5"
}
```

> **Send ONLY changed fields.** All fields from create are updatable plus `status`.

**Success Response (200):**
```json
{ "success": true, "message": "Updated." }
```

---

### SCREEN 25: Equipment List

**Purpose:** View all gym equipment inventory.

**API Call:**

```
GET /equipment?page=1&limit=15&search=treadmill
Header: Authorization: Bearer {token}
```

**Query Parameters:**

| Param | Searchable Columns |
|:--|:--|
| `search` | `name`, `category`, `brand`, `location` |

**Success Response (200):**
```json
{
  "success": true,
  "message": "Equipment list.",
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Commercial Treadmill",
        "category": "Cardio",
        "brand": "Life Fitness",
        "model_number": "T5-GO",
        "serial_number": "LF-2024-001",
        "purchase_date": "2025-01-15",
        "purchase_price": "250000.00",
        "warranty_expiry": "2028-01-15",
        "location": "Cardio Zone",
        "condition_status": "Good",
        "last_maintenance_date": "2026-05-01",
        "next_maintenance_date": "2026-08-01",
        "maintenance_notes": "Regular belt check",
        "status": "Active",
        "created_at": "2025-01-15 09:00:00",
        "deleted_at": null
      }
    ],
    "total": 25,
    "page": 1,
    "limit": 15,
    "pages": 2
  }
}
```

**UI Layout:**

```
┌──────────────────────────────────────┐
│ 🔧 Equipment      [📊 Summary][+ Add]│
├──────────────────────────────────────┤
│ 🔍 Search equipment...              │
├──────────────────────────────────────┤
│ ┌──────────────────────────────────┐ │
│ │ Commercial Treadmill    Cardio   │ │
│ │ Life Fitness T5-GO               │ │
│ │ 📍 Cardio Zone                   │ │
│ │ 🟢 Good        Active           │ │
│ └──────────────────────────────────┘ │
└──────────────────────────────────────┘
```

**UI Notes:**
- Color dot for condition: 🟢 Good, 🟡 Fair, 🔴 Poor, ⚫ Out of Service
- Category as a badge/chip
- Tap → Equipment Detail screen
- Summary button → Equipment Summary screen

---

### SCREEN 26: Equipment Detail

**Purpose:** Full view of single equipment item with maintenance actions.

**API Call:**

```
GET /equipment/1
Header: Authorization: Bearer {token}
```

**Response:** Same object as list item (single)

**UI Layout:**

```
┌──────────────────────────────────────┐
│ ← Equipment Detail        [✏️] [🗑️] │
├──────────────────────────────────────┤
│ Commercial Treadmill                 │
│ 🏷️ Cardio · Life Fitness            │
├──────────────────────────────────────┤
│ Specifications                       │
│ Model    : T5-GO                     │
│ Serial   : LF-2024-001              │
│ Location : Cardio Zone               │
│ Condition: 🟢 Good                   │
├──────────────────────────────────────┤
│ Purchase Info                        │
│ Price        : ₹2,50,000            │
│ Purchase Date: Jan 15, 2025          │
│ Warranty     : Jan 15, 2028          │
│   (⏳ 1 year 7 months remaining)     │
├──────────────────────────────────────┤
│ Maintenance                          │
│ Last     : May 01, 2026              │
│ Next Due : Aug 01, 2026              │
│ Notes    : Regular belt check        │
│                                      │
│ [🔧 Log Maintenance]  ← Button      │
├──────────────────────────────────────┤
│ [✏️ Edit]  [🗑️ Delete]               │
└──────────────────────────────────────┘
```

**Delete Equipment:**
```
DELETE /equipment/1
```
Response: `{ "success": true, "message": "Deleted." }`

**Log Maintenance button → opens Log Maintenance form (Screen 29)**

---

### SCREEN 27: Equipment Summary

**Purpose:** Dashboard-style overview of all equipment stats.

**API Call:**

```
GET /equipment/summary
Header: Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Equipment summary.",
  "data": {
    "total_equipment": 25,
    "by_status": { "Active": 22, "Retired": 3 },
    "by_condition": { "Good": 18, "Fair": 5, "Poor": 2 },
    "total_value": 3500000.00,
    "maintenance_due": 4
  }
}
```

**UI Layout:**

```
┌──────────────────────────────────────┐
│ 📊 Equipment Summary                │
├──────────────────────────────────────┤
│ Total Equipment: 25                  │
│ Total Value: ₹35,00,000             │
│ Maintenance Due: 4 ⚠️               │
├──────────────────────────────────────┤
│ By Status           By Condition     │
│ ┌─────────────┐   ┌──────────────┐  │
│ │ 🟢 Active 22│   │ 🟢 Good   18 │  │
│ │ 🔴 Retired 3│   │ 🟡 Fair    5 │  │
│ │             │   │ 🔴 Poor    2 │  │
│ └─────────────┘   └──────────────┘  │
│                                      │
│ (Use Pie Charts for visual display)  │
└──────────────────────────────────────┘
```

**UI Notes:**
- Use pie charts or donut charts for `by_status` and `by_condition`
- Highlight "Maintenance Due" count with warning color
- Total value formatted as Indian currency: ₹35,00,000

---

### SCREEN 28: Add Equipment Form

**Purpose:** Add new equipment to inventory.

**API Call:**

```
POST /equipment
Header: Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Commercial Treadmill",
  "category": "Cardio",
  "brand": "Life Fitness",
  "model_number": "T5-GO",
  "serial_number": "LF-2024-001",
  "purchase_date": "2025-01-15",
  "purchase_price": 250000,
  "warranty_expiry": "2028-01-15",
  "location": "Cardio Zone",
  "condition_status": "Good"
}
```

**Form Fields:**

| Field | Label | Type | Required | Input Widget |
|:--|:--|:--|:--|:--|
| `name` | Equipment Name | string | ✅ | Text Input |
| `category` | Category | string | ❌ | Dropdown: Cardio, Strength, Free Weights, Machines, Accessories, General |
| `brand` | Brand | string | ❌ | Text Input |
| `model_number` | Model Number | string | ❌ | Text Input |
| `serial_number` | Serial Number | string | ❌ | Text Input |
| `purchase_date` | Purchase Date | date | ❌ | Date Picker |
| `purchase_price` | Purchase Price (₹) | decimal | ❌ | Numeric Input |
| `warranty_expiry` | Warranty Expiry | date | ❌ | Date Picker |
| `location` | Location | string | ❌ | Dropdown: Main Hall, Cardio Zone, Weight Room, Yoga Studio, CrossFit Area, Other |
| `condition_status` | Condition | string | ❌ | Dropdown: Good, Fair, Poor, Out of Service |
| `maintenance_notes` | Notes | string | ❌ | Multiline Text |

**Success Response (201):**
```json
{ "success": true, "message": "Equipment added.", "data": { "id": 26 } }
```

---

### SCREEN 29: Edit Equipment

**Purpose:** Update equipment details.

**API Call:**

```
PUT /equipment/1
Header: Authorization: Bearer {token}
Content-Type: application/json

{
  "condition_status": "Fair",
  "location": "Weight Room"
}
```

> Send ONLY changed fields. Same form as Add Equipment, pre-filled.

---

### SCREEN 30: Log Maintenance

**Purpose:** Record a maintenance event for equipment.

**API Call:**

```
POST /equipment/1/maintenance
Header: Authorization: Bearer {token}
Content-Type: application/json

{
  "next_maintenance_date": "2026-11-01",
  "condition_status": "Good",
  "maintenance_notes": "Belt replaced, motor inspected. All clear."
}
```

**Form Fields:**

| Field | Label | Type | Required | Input Widget |
|:--|:--|:--|:--|:--|
| `condition_status` | Current Condition | string | ❌ | Dropdown: Good, Fair, Poor |
| `next_maintenance_date` | Next Maintenance Date | date | ❌ | Date Picker |
| `maintenance_notes` | Maintenance Notes | string | ❌ | Multiline Text |

> `last_maintenance_date` is automatically set to **today** by the server.

**Success Response (200):**
```json
{ "success": true, "message": "Maintenance logged." }
```

---

### SCREEN 31: Profile / Settings

**Purpose:** View current user info and logout.

**API Call (fetch profile):**

```
GET /auth/me
Header: Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "User.",
  "data": {
    "id": 2,
    "username": "gymadmin",
    "name": "Gym Admin",
    "role": "gym_admin"
  }
}
```

**API Call (logout):**

```
POST /auth/logout
Header: Authorization: Bearer {token}
```

**Response:**
```json
{ "success": true, "message": "Logged out." }
```

**After Logout:**
1. Call `POST /auth/logout` (logs the activity)
2. Clear token from secure storage
3. Clear all local state/cache
4. Navigate to Login screen (clear navigation stack)

**UI Layout:**

```
┌──────────────────────────────────────┐
│ 👤 Profile                          │
├──────────────────────────────────────┤
│         (Large Avatar)               │
│         Gym Admin                    │
│         @gymadmin                    │
│         🏷️ gym_admin                 │
├──────────────────────────────────────┤
│ Settings                             │
│ ├─ 🔔 Notifications                 │
│ ├─ 🌙 Dark Mode                     │
│ └─ ℹ️ About                          │
├──────────────────────────────────────┤
│ [🚪 Logout]                          │
└──────────────────────────────────────┘
```

---

## Data Models (for Mobile App)

### Generic API Response Wrapper

```dart
class ApiResponse<T> {
  final bool success;
  final String message;
  final T? data;
}
```

### Paginated Response

```dart
class PaginatedResponse<T> {
  final List<T> data;
  final int total;
  final int page;
  final int limit;
  final int pages;
}
```

### All Models

```dart
// Member
class Member {
  int id;
  String memberId;      // "MEM-00001"
  String name;
  String? mobile;
  String? email;
  String? dob;           // "YYYY-MM-DD"
  String? gender;
  String? bloodGroup;
  String? address;
  String? emergencyContactName;
  String? emergencyContactNumber;
  String? joinDate;
  double? heightCm;
  double? weightKg;
  String? medicalHistory;
  String status;          // "Active"
  String createdAt;
}

// Trainer
class Trainer {
  int id;
  String name;
  String? mobile;
  String? email;
  String? specialization;
  double? salary;
  String? joinDate;
  String status;
  String createdAt;
}

// Plan
class Plan {
  int id;
  String name;
  int durationDays;
  double price;
  String? description;
  int isActive;
  String createdAt;
}

// Membership
class Membership {
  int id;
  int memberId;
  int planId;
  int? trainerId;
  String startDate;
  String endDate;
  String status;
  String createdAt;
  // Joined fields (from GET /memberships):
  String? memberName;
  String? memberCode;
  String? planName;
  String? mobile;   // Only from /memberships/expiring
}

// Payment
class Payment {
  int id;
  String invoiceNumber;
  int memberId;
  int? membershipId;
  double amount;
  String paymentDate;
  String paymentMode;    // Cash, UPI, Card, Online
  String status;         // Paid
  String? notes;
  int? collectedBy;
  String createdAt;
  // Joined field (from /dashboard/stats):
  String? memberName;
}

// DietPlan
class DietPlan {
  int id;
  int memberId;
  int? trainerId;
  String planDetails;
  String assignedDate;
  String? notes;
  String createdAt;
}

// Attendance
class Attendance {
  int id;
  String userType;       // "Member" or "Trainer"
  int referenceId;
  String checkIn;        // DateTime string
  String? checkOut;      // null if still checked in
  String createdAt;
}

// WorkoutPlan
class WorkoutPlan {
  int id;
  int memberId;
  int? trainerId;
  String title;
  String goal;           // "General Fitness", "Muscle Gain", etc.
  String level;          // "Beginner", "Intermediate", "Advanced"
  String? monday;
  String? tuesday;
  String? wednesday;
  String? thursday;
  String? friday;
  String? saturday;
  String? sunday;
  String? notes;
  String? startDate;
  String? endDate;
  String status;
  String createdAt;
  // Joined fields:
  String? memberName;
  String? trainerName;
}

// Equipment
class Equipment {
  int id;
  String name;
  String category;
  String? brand;
  String? modelNumber;
  String? serialNumber;
  String? purchaseDate;
  double? purchasePrice;
  String? warrantyExpiry;
  String? location;
  String conditionStatus;  // Good, Fair, Poor, Out of Service
  String? lastMaintenanceDate;
  String? nextMaintenanceDate;
  String? maintenanceNotes;
  String status;
  String createdAt;
}

// DashboardStats
class DashboardStats {
  DashboardSummary summary;
  List<Membership> expiringSoon;
  List<Payment> recentPayments;
}

class DashboardSummary {
  int totalMembers;
  int totalTrainers;
  int attendanceToday;
  double revenueToday;
}

// User (from login/me)
class User {
  int id;
  String username;
  String? email;
  String name;
  String role;       // gym_admin, gym_staff, gym_trainer
  String? status;    // APPROVED
}

// EquipmentSummary
class EquipmentSummary {
  int totalEquipment;
  Map<String, int> byStatus;
  Map<String, int> byCondition;
  double totalValue;
  int maintenanceDue;
}
```

---

## Role-Based UI Visibility

Show/hide features based on `user.role` from login response:

| Feature | `gym_admin` | `gym_staff` | `gym_trainer` |
|:--|:--|:--|:--|
| Dashboard | ✅ Full | ✅ Full | ✅ View only |
| Members — View | ✅ | ✅ | ✅ (limited) |
| Members — Add/Edit/Delete | ✅ | ✅ | ❌ |
| Trainers — View | ✅ | ✅ | ✅ (self) |
| Trainers — Add/Delete | ✅ | ❌ | ❌ |
| Plans — View | ✅ | ✅ | ✅ |
| Plans — Add | ✅ | ❌ | ❌ |
| Memberships — View | ✅ | ✅ | ✅ |
| Memberships — Assign | ✅ | ✅ | ❌ |
| Payments — View | ✅ | ✅ | ❌ |
| Payments — Record | ✅ | ✅ | ❌ |
| Attendance — View | ✅ | ✅ | ✅ |
| Attendance — Mark | ✅ | ✅ | ✅ (self) |
| Diet Plans — View | ✅ | ✅ | ✅ |
| Diet Plans — Assign | ✅ | ❌ | ✅ |
| Workout Plans — Full | ✅ | ✅ | ✅ |
| Equipment — Full | ✅ | ✅ | ❌ |
| Equipment — View | ✅ | ✅ | ✅ |

---

## HTTP Headers Template (Every Request)

```
Content-Type: application/json
Authorization: Bearer {jwt_token}
```

**For `/auth/login` only — NO Authorization header needed.**

---

## Error Handling Cheat Sheet

| HTTP Code | Meaning | Mobile App Action |
|:--|:--|:--|
| `200` | Success | Parse `data`, update UI |
| `201` | Created | Show success message, navigate back |
| `400` | Validation error | Show `message` as form error |
| `401` | Token expired/invalid | Clear token, redirect to Login |
| `403` | Account blocked | Show `message`, redirect to Login |
| `404` | Not found | Show "Not found" message |
| `500` | Server error | Show "Something went wrong, try again" |
| Network error | No internet | Show offline banner, retry button |

---

## Total Screen Count: 31 Screens

| Category | Screens | Count |
|:--|:--|:--|
| Auth | Splash, Login | 2 |
| Dashboard | Dashboard | 1 |
| Members | List, Detail, Add, Edit | 4 |
| Trainers | List, Add | 2 |
| Plans | List, Add | 2 |
| Memberships | Active List, Expiring, Assign | 3 |
| Payments | List, Record | 2 |
| Diet Plans | List, Assign | 2 |
| Attendance | List, Mark | 2 |
| Workout Plans | List, Detail, Create, Edit | 4 |
| Equipment | List, Detail, Summary, Add, Edit, Log Maintenance | 6 |
| Profile | Profile/Logout | 1 |
| **Total** | | **31** |
