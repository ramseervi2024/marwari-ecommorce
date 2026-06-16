# Retail POS ERP API & System Overview

This custom WordPress plugin implements a fully featured Point of Sale (POS) and inventory management ERP for retail stores, supermarkets, pharmacies, and multi-branch chains.

## 1. Directory Structure

The module follows a highly structured, clean Controller-Repository-Service architecture:

```
retail-pos/
├── composer.json               # Package definitions
├── retail-pos-api.php          # Plugin bootstrap & routing registers
├── retail_management_overview.md # Operations and API documentation
├── database/
│   └── Migrations.php          # 14 custom database tables & sample data seeds
├── middleware/
│   ├── AuthMiddleware.php      # JWT Token decrypter & authenticator
│   └── RoleMiddleware.php      # Capability validation middleware
├── services/
│   ├── AuthService.php         # OTP Generation & login service
│   └── JwtService.php          # JWT Signer & validator
├── repositories/
│   ├── BaseRepository.php      # Generic PDO SQL helper
│   ├── ProductRepository.php   # Product queries
│   ├── CategoryRepository.php  # Category queries
│   ├── BrandRepository.php     # Brand queries
│   ├── CustomerRepository.php  # Customer profiles
│   ├── SupplierRepository.php  # Suppliers catalog
│   ├── SaleRepository.php      # Sales/Checkout headers
│   ├── PurchaseRepository.php  # Restock orders
│   ├── InventoryRepository.php # Stock status metrics
│   └── ExpenseRepository.php   # Operating costs ledger
├── controllers/
│   ├── BaseController.php      # API response formatter
│   ├── AuthController.php      # Auth, User approval, & SMTP controllers
│   ├── ProductController.php   # Product management & vector Barcodes
│   ├── CategoryController.php
│   ├── BrandController.php
│   ├── CustomerController.php   # Loyalty balances & points ledger
│   ├── SupplierController.php
│   ├── SaleController.php       # Transactional checkouts & void voids
│   ├── PurchaseController.php   # Restock order workflows
│   ├── InventoryController.php  # Manual counts audit
│   ├── ExpenseController.php
│   ├── DashboardController.php  # Real-time card KPIs & charts data
│   ├── ReportsController.php    # GST splits & profit/loss sheets
│   └── MediaController.php      # WordPress media uploader helper
├── routes/
│   ├── auth.php, product.php, category.php, brand.php, customer.php,
│   │   supplier.php, sale.php, purchase.php, inventory.php, expense.php,
│   │   media.php, dashboard.php, reports.php # 13 Route registers
├── swagger/
│   ├── index.php               # Swagger UI docs sandbox page
│   └── swagger.json            # OpenAPI 3.0 API specifications schema
└── views/
    └── dashboard-view.php      # Premium Dark HSL Single-Page Application (SPA)
```

---

## 2. Database Schema (14 Tables)

Upon activation, the plugin initializes these tables with native custom SQL structures:
1. `wp_pos_categories`: Product categories taxonomy.
2. `wp_pos_brands`: Brands/Manufacturers catalog.
3. `wp_pos_products`: Product master list with prices (purchase/selling), units, SKU, and barcode.
4. `wp_pos_customers`: Customer registries with loyalty points balances and total purchases sums.
5. `wp_pos_suppliers`: B2B vendors directory.
6. `wp_pos_sales`: Invoice header ledger containing subtotal, discounts, CGST/SGST taxes, grand totals, and payment modes.
7. `wp_pos_sale_items`: Transaction line items capturing historical purchase cost, retail price, and tax at checkout.
8. `wp_pos_purchases`: Restocking logs detailing cost, quantity, tax, and order statuses.
9. `wp_pos_inventory`: Real-time stock status tracker representing available, reserved, and damaged stock levels.
10. `wp_pos_expenses`: Operating costs sheet (Rent, Utilities, etc.).
11. `wp_pos_loyalty`: Points log tracking earned vs redeemed loyalty history.
12. `wp_pos_stores`: Retail locations/branches list.
13. `wp_pos_documents`: B2B invoice file attachments.
14. `wp_pos_activity_logs`: System audit trail logging actions (logins, checkouts, void voids).

---

## 3. Built-In Test Operators

The migration automatically creates 4 testing roles and operator accounts. 

> [!NOTE]
> All preset passwords are set to their respective default values during database seeding.

| Role | Username | Password | Assigned Capabilities |
| :--- | :--- | :--- | :--- |
| **POS Super Admin** | `possuperadmin` | `123456` | Full access (`manage_pos`, `manage_users`, `view_reports`, etc.) |
| **POS Store Manager** | `pos_manager` | `managerpass123` | Product catalog, PO restocking, reports, sales views. |
| **POS Cashier** | `pos_cashier` | `cashierpass123` | POS checkout, customer management, loyalty redemption. |
| **POS Inventory Manager** | `pos_inventory` | `inventorypass123` | Supplier directories, inventory adjustments, and restocking. |

---

## 4. Key Workflows & Business Logic

### Secure JWT & OTP Auth Flow
1. **Initiate Verification**: The user requests a code via `/auth/login/initiate`.
2. **Mail dispatch**: The system generates a random 6-digit OTP code, saves it to database user metadata, and sends it to the operator's registered email using WP Mail SMTP configs.
3. **JWT Retrieval**: The user verifies the OTP via `/auth/login` and receives an `access_token` (JWT) and a `refresh_token`. All subsequent requests must carry the `Authorization: Bearer <token>` header.

### GST Compliance Calculations
At POS checkout, the plugin automatically extracts base prices and CGST/SGST amounts from tax-inclusive retail selling prices:
- `Price_Without_GST = Selling_Price / (1 + GST_Pct/100)`
- `GST_Amount = Selling_Price - Price_Without_GST`
CGST and SGST are calculated by splitting `GST_Amount` equally (50% / 50%) to comply with standard Indian local sales GST filings.

### Barcode System Integration
- **Lookup**: Scanners send a fast `GET /products/barcode/{code}` lookup query to retrieve item details.
- **Generation**: The native Code-39 vector generator in `ProductController` dynamically renders barcodes in standard vector SVGs (`/products/barcode/generate`) without external third-party library dependencies.

### Loyalty Points System
- **Earn**: Every completed transaction awards 1 loyalty point per ₹100 spent.
- **Redeem**: Operators can attach customers at checkout and move a slider to redeem points. Points are deducted, acting as a ₹1 discount per 1 point redeemed, recorded directly in the transaction ledger.

---

## 5. System Access URL Routes

- **Admin/Cashier SPA Dashboard Portal**: `/retail-pos/`
- **Swagger REST Sandbox documentation**: `/retail-pos-api-docs/`
