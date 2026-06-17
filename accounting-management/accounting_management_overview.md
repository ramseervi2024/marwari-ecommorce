# GST Billing & Accounting ERP Module Overview

This document provides a comprehensive overview of the decoupled, OOP-based **GST Billing & Accounting ERP** WordPress plugin (`accounting-management`).

---

## Mapped Roles & Credentials

During activation, the system provisions 5 custom roles with specific capability matrices. You can log in using these pre-seeded demo accounts:

| Username | Role | Default Password | Access Level |
| --- | --- | --- | --- |
| `asuperadmin` | Accounting Super Admin | `123456` | Full system capability bypasses check layers |
| `aaccountant` | Accounting Accountant | `123456` | Sales, purchases, journals, ledgers, and GSTR reporting |
| `asalesexec` | Accounting Sales Executive | `123456` | Sales invoices, customer profiles, and collections |
| `apurchasemgr` | Accounting Purchase Manager | `123456` | Vendor records and purchase bills |
| `aauditor` | Accounting Auditor | `123456` | Read-only ledger, balance sheets, and audit reports |

---

## Custom Database Entities

All transaction and master records use the prefix `wp_acc_`. The 18 schema tables are:

1. `wp_acc_customers` - Client directories, credit limits, and outstanding balances.
2. `wp_acc_vendors` - Suppliers outstanding payable ledger.
3. `wp_acc_items` - Product inventories and HSN/SAC parameters.
4. `wp_acc_sales` - Main invoices header listing subtotals, CGST/SGST/IGST, and payment statuses.
5. `wp_acc_sale_items` - Lines details matching invoice ID, quantity, and taxes.
6. `wp_acc_purchases` - Inward bill header.
7. `wp_acc_purchase_items` - Inward bill lines items.
8. `wp_acc_expenses` - Categorized business expenditures.
9. `wp_acc_accounts` - Chart of Accounts mapping Assets, Liabilities, Equities, Incomes, and Expenses.
10. `wp_acc_journals` - Transaction lines registering Debit vs Credit double entry postings.
11. `wp_acc_ledger` - General ledger entries listing dates, amounts, and reference IDs.
12. `wp_acc_gst` - Aggregated period GSTR parameters.
13. `wp_acc_einvoice` - Mock IRN logs, ack data, and QR images.
14. `wp_acc_ewaybill` - Vehicle movement documents.
15. `wp_acc_inventory` - Stock location settings and alert thresholds.
16. `wp_acc_payments` - Customer collections and vendor payment vouchers.
17. `wp_acc_documents` - PDF attachments repository.
18. `wp_acc_activity_logs` - Operations audit trails.

---

## REST Endpoints Registry

Namespace: `/wp-json/accounting-management/v1`

### Authentication
* `POST /auth/register` - Submit signup credentials.
* `POST /auth/register/verify` - Submit verification code OTP.
* `POST /auth/login/initiate` - Initiate login (sends OTP code).
* `POST /auth/login` - Complete session validation and issue JWT bearer token.
* `POST /auth/refresh-token` - Rotate JWT and session access tokens.
* `GET /auth/me` - Profile overview details.
* `POST /auth/logout` - Revoke refresh tokens.
* `GET/POST /auth/smtp` - Manage verification server configurations.

### Master Registry Modules
* `GET/POST/PUT/DELETE /customers` - Operations on customer records.
* `GET/POST/PUT/DELETE /vendors` - Operations on vendor files.
* `GET/POST/PUT/DELETE /items` - Operations on items catalog.

### Billing & Double Entry Postings
* `GET/POST/DELETE /sales` - Record sales invoice transactions and post ledger balances.
* `GET/POST/DELETE /purchases` - Record inward supply bills.
* `GET/POST/DELETE /expenses` - Outward cash expense vouchers.
* `GET/POST/PUT/DELETE /accounts` - Customize Chart of Accounts.
* `GET/POST/DELETE /journals` - Post adjustment journals.
* `GET /ledger` - Query entries in the ledger table.

### Compliance & Warehousing
* `GET /gst` - Retrieve tax returns summaries.
* `POST /einvoice/generate` - Register Sales Invoice as E-Invoice (IRN/QR).
* `POST /ewaybill/generate` - Generate transit documents.
* `GET/POST /inventory/adjust` - Adjust product count levels.
* `GET/POST /payment` - Register collections/payments.
