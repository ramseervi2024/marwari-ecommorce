# AppForge E-Commerce — WordPress + WooCommerce Product Specification

## 1. Product Goal

Build a reusable, ready-made E-Commerce solution that can be demonstrated to clients and customized for different businesses.

Each client should receive:

- Customer Mobile App — React Native
- Customer Website — WordPress + WooCommerce
- WordPress Admin — WooCommerce Admin
- Custom WordPress Plugin(s) for business-specific APIs/configuration
- Payment integration
- Shipping/delivery configuration
- Push notifications
- Product, order, customer and inventory management

The goal is to build the core product once and reuse it for multiple clients by changing branding, content, configuration and selected features.

---

# 2. Recommended Architecture

```text
                         APPFORGE
                            |
              +-------------+-------------+
              |             |             |
        React Native     Website      WordPress
        Mobile App      Frontend       Admin
              |             |             |
              +-------------+-------------+
                            |
                  WordPress + WooCommerce
                            |
                 WooCommerce REST API
                            |
                  Custom AppForge Plugin
                            |
                    WordPress Database
                            |
       +--------------------+--------------------+
       |                    |                    |
   Payment Gateway      Shipping/Delivery    Notifications
   Razorpay/Stripe      Provider/API         FCM/Email
```

## Core principle

WordPress + WooCommerce is the main backend and source of truth.

Do NOT create a separate product/order/customer database for the mobile app.

The mobile app and website consume the same WooCommerce data.

---

# 3. Technology Stack

## Website

- WordPress
- WooCommerce
- Responsive WordPress theme
- Elementor or Gutenberg, depending on client requirement
- SEO plugin
- Cache/performance plugin
- Security plugin

## Mobile

- React Native
- TypeScript
- React Navigation
- Redux Toolkit or Zustand
- Axios/fetch
- Firebase Cloud Messaging
- AsyncStorage/Secure Storage

## Backend

- WordPress
- WooCommerce
- PHP
- WordPress REST API
- WooCommerce REST API
- Custom AppForge WordPress plugin

## Database

- MySQL/MariaDB through WordPress

## Payments

Keep payment provider configurable:

- Razorpay
- Stripe
- PayU
- Cashfree
- WooCommerce-supported gateway
- Cash on Delivery

## Hosting

Client can use:

- Hostinger
- AWS
- DigitalOcean
- Cloudways
- Any WordPress-compatible hosting

---

# 4. Product Components

```text
E-Commerce
|
+-- Customer Mobile App
|
+-- Customer Website
|
+-- WordPress Admin
|
+-- WooCommerce
|
+-- AppForge Custom Plugin
|
+-- Payment Gateway
|
+-- Shipping Integration
|
+-- Notification System
|
+-- Analytics
```

---

# 5. Customer Mobile App

## 5.1 Splash Screen

Display:

- Client logo
- Brand name
- Loading indicator

Configuration should come from the API where possible.

Example:

```text
APP LOGO

Brand Name

Loading...
```

---

# 5.2 Onboarding

Optional.

Screens:

1. Welcome
2. Shop products
3. Easy payment
4. Fast delivery

Buttons:

- Skip
- Get Started

This should be configurable so it can be disabled for clients who do not need it.

---

# 5.3 Authentication

## Login

Fields:

- Email/mobile
- Password

Actions:

- Login
- Forgot password
- Register
- Continue as guest, if enabled

## Registration

Fields:

- First name
- Last name
- Email
- Mobile
- Password
- Confirm password

Optional:

- OTP verification
- Email verification
- Google login
- Apple login

Authentication implementation must respect the selected WordPress/WooCommerce authentication approach.

Do not store the user's WordPress password in the mobile app.

---

# 5.4 Home Screen

The home screen should be controlled as much as possible from WordPress.

Sections:

```text
Header
|
+-- Location
+-- Search
+-- Notification

Banner
|
+-- Promotional banner
+-- Campaign banner

Categories

Featured Products

Best Sellers

New Arrivals

Discount Products

Recommended Products

Brands

Promotional Sections
```

Admin should be able to change:

- Banner image
- Banner title
- Banner link
- Product category
- Featured products
- Promotional content

---

# 5.5 Categories

Display WooCommerce product categories.

Example:

```text
Men
|
+-- Clothing
+-- Shoes
+-- Watches
+-- Accessories

Women
|
+-- Clothing
+-- Shoes
+-- Bags
+-- Accessories
```

Mobile app should fetch categories dynamically from WooCommerce.

Do not hard-code categories.

---

# 5.6 Product Listing

Display:

- Product image
- Product name
- Regular price
- Sale price
- Discount
- Rating
- Wishlist button
- Stock status

Example:

```text
Nike Shoes

₹2,999
₹3,999

20% OFF

★★★★★

[Add to Cart]
```

Support:

- Pagination
- Infinite scrolling
- Sorting
- Filtering

---

# 5.7 Product Filters

Depending on WooCommerce product configuration:

- Category
- Price
- Brand
- Rating
- Size
- Color
- Attribute
- Availability
- Sale
- Custom attributes

Sort:

- Latest
- Price low to high
- Price high to low
- Popular
- Rating
- Best selling

---

# 5.8 Product Details

Display:

- Product images
- Product name
- Short description
- Full description
- Regular price
- Sale price
- Discount
- SKU
- Stock status
- Rating
- Reviews
- Attributes
- Variations
- Related products
- Cross-sell products
- Upsell products

For variable products:

```text
Color:
[Black] [White] [Blue]

Size:
[S] [M] [L] [XL]

Quantity:
[-] 1 [+]
```

Actions:

- Add to Wishlist
- Add to Cart
- Buy Now

---

# 5.9 Cart

Cart should reflect WooCommerce cart state.

Display:

```text
Product
Price
Quantity
Subtotal
Remove
```

Order summary:

```text
Subtotal
Discount
Shipping
Tax
Total
```

Actions:

- Update quantity
- Remove item
- Apply coupon
- Proceed to checkout

Important:

Do not calculate final payable price only on the mobile app.

The server/WooCommerce must calculate the final amount.

---

# 5.10 Wishlist

Wishlist can be implemented using:

- A compatible WooCommerce wishlist plugin, OR
- AppForge custom WordPress plugin/API

Features:

- Add
- Remove
- Move to cart
- Check availability

Wishlist must be linked to the logged-in customer.

---

# 5.11 Address Management

Support:

- Add address
- Edit address
- Delete address
- Default address

Fields:

```text
First Name
Last Name
Phone
Address Line 1
Address Line 2
City
State
Country
Postcode
```

Use WooCommerce customer billing/shipping data wherever practical.

---

# 5.12 Checkout

Checkout should support:

```text
Customer Information
|
+-- Billing Address
+-- Shipping Address
|
+-- Shipping Method
|
+-- Coupon
|
+-- Payment Method
|
+-- Order Summary
|
+-- Place Order
```

Payment options can include:

- Razorpay
- Stripe
- COD
- Other WooCommerce gateway

The exact options depend on the client's installed WooCommerce payment gateway.

---

# 5.13 Payment Flow

Recommended flow:

```text
Mobile App
    |
    | Create/prepare checkout
    v
WooCommerce / AppForge API
    |
    | Create payment/order
    v
Payment Gateway
    |
    | Success / Failure
    v
WooCommerce
    |
    | Verify server-side
    v
Order Status Updated
```

Never trust a payment success response coming only from the mobile client.

Payment verification/webhooks must be handled server-side by the payment gateway/WooCommerce integration.

---

# 5.14 Orders

Customer can view:

- Order number
- Date
- Products
- Quantity
- Total
- Payment status
- Order status
- Shipping details

Order statuses can include:

```text
Pending payment
Processing
On hold
Completed
Cancelled
Refunded
Failed
```

Additional custom statuses can be introduced through WooCommerce/custom plugin if required.

---

# 5.15 Order Details

```text
Order #10025

Order Date

Products
----------------
Product A x 2
Product B x 1

Subtotal
Shipping
Tax
Discount
Total

Payment Method

Shipping Address

Order Status
```

Actions where applicable:

- Cancel order
- Download invoice
- Track order
- Reorder
- Request return/refund

---

# 5.16 Order Tracking

Basic version:

```text
Order Placed
     |
Confirmed
     |
Processing
     |
Shipped
     |
Out for Delivery
     |
Delivered
```

Advanced version can integrate a shipping provider's tracking API.

Do not build live GPS tracking into the base e-commerce product unless the client specifically needs it.

---

# 5.17 Coupons

Coupons should come from WooCommerce.

Support:

- Percentage discount
- Fixed cart discount
- Fixed product discount
- Minimum spend
- Maximum spend
- Expiry
- Usage limit
- Individual-use coupon
- Product/category restrictions

Mobile app should validate coupons through the server.

---

# 5.18 Reviews & Ratings

Customer can:

- Give rating
- Write review
- Upload image if supported
- Edit/delete review depending on configuration

Admin can:

- Approve
- Reject
- Moderate

Use WooCommerce/WordPress review functionality where possible.

---

# 5.19 Notifications

Use Firebase Cloud Messaging for mobile push notifications.

Notification examples:

```text
Order Confirmed
Payment Successful
Order Shipped
Out for Delivery
Order Delivered

New Product
New Offer
Coupon Available
Sale Started
```

The AppForge plugin should provide a secure way to map WordPress customers/devices to FCM tokens.

Do not expose Firebase server credentials in the mobile app.

---

# 5.20 Customer Profile

```text
My Profile
|
+-- Personal Information
+-- My Orders
+-- Wishlist
+-- Addresses
+-- Coupons
+-- Notifications
+-- Support
+-- Privacy Policy
+-- Terms & Conditions
+-- Logout
```

---

# 6. Customer Website

The website can use normal WooCommerce pages.

Required pages:

```text
Home
Shop
Categories
Product Listing
Product Details
Cart
Checkout
My Account
Orders
Wishlist
About Us
Contact Us
FAQ
Privacy Policy
Terms & Conditions
Refund Policy
Shipping Policy
```

The website can be:

1. Standard WooCommerce storefront
2. Custom React/Next.js frontend consuming WooCommerce APIs
3. WordPress theme with custom UI

For the ready-made product, start with a standard/custom WooCommerce WordPress storefront because it reduces development and maintenance time.

---

# 7. WordPress Admin

WordPress/WooCommerce already provides most of the administration functionality.

Do not recreate the entire WooCommerce admin in React unless there is a specific business reason.

Admin should manage:

```text
WordPress Dashboard
|
+-- Products
+-- Categories
+-- Orders
+-- Customers
+-- Coupons
+-- Reviews
+-- Inventory
+-- Payments
+-- Shipping
+-- Taxes
+-- Media
+-- Pages
+-- Menus
+-- Banners
+-- Settings
```

---

# 8. Product Management

Admin can create:

- Simple product
- Variable product
- Grouped product
- External/affiliate product, if needed
- Virtual product
- Downloadable product

Fields:

```text
Product Name
Description
Short Description
SKU
Regular Price
Sale Price
Tax Class
Stock
Stock Status
Weight
Dimensions
Categories
Tags
Images
Gallery
Attributes
Variations
Shipping Class
Linked Products
Upsells
Cross-sells
```

---

# 9. Inventory

WooCommerce should be the inventory source.

Features:

- Stock quantity
- Stock status
- Low stock threshold
- Backorders
- SKU
- Inventory management
- Variation-level stock

Example:

```text
Nike Shoes
SKU: NIKE-001
Stock: 25
Status: In Stock
Low Stock Threshold: 5
```

---

# 10. Orders

Admin can:

- View orders
- Search orders
- Filter orders
- View customer
- View products
- Update order status
- Add notes
- Add tracking information
- Refund
- Resend order email
- View payment details

---

# 11. Customers

Use WooCommerce/WordPress customer accounts.

Display:

- Name
- Email
- Phone
- Orders
- Total spending
- Billing address
- Shipping address
- Account status

Avoid creating a duplicate customer system unless required.

---

# 12. Shipping

Base product should support WooCommerce shipping configuration.

Shipping methods:

- Flat rate
- Free shipping
- Local pickup
- Shipping zones
- Third-party shipping plugins/APIs

Example:

```text
India
|
+-- Karnataka
|   +-- ₹50
|
+-- Rajasthan
|   +-- ₹80
|
+-- Other States
    +-- ₹100
```

For advanced clients, integrate shipping providers such as Shiprocket or another WooCommerce-compatible provider.

---

# 13. Tax

WooCommerce should calculate tax.

Configurable:

- Tax rates
- Inclusive/exclusive pricing
- GST
- State-specific tax rules
- Tax classes

Tax configuration depends on the client's country/business requirements.

---

# 14. Payment

Recommended ready-made configuration:

```text
Payment
|
+-- Razorpay
+-- Stripe
+-- COD
+-- PayU
+-- Cashfree
```

Do not hard-code one gateway into the mobile app.

The selected WooCommerce gateway should be configured per client.

---

# 15. AppForge Custom WordPress Plugin

This is the most important custom component.

Instead of modifying WooCommerce core files, create:

```text
appforge-ecommerce/
|
+-- appforge-ecommerce.php
|
+-- includes/
|   +-- class-auth.php
|   +-- class-products.php
|   +-- class-cart.php
|   +-- class-orders.php
|   +-- class-customers.php
|   +-- class-wishlist.php
|   +-- class-notifications.php
|   +-- class-settings.php
|
+-- api/
|   +-- routes.php
|
+-- admin/
|   +-- settings.php
|
+-- assets/
|
+-- readme.txt
```

The plugin should extend WooCommerce rather than replace it.

---

# 16. Custom REST API

Use a namespace:

```text
/wp-json/appforge/v1/
```

Examples:

```text
GET  /wp-json/appforge/v1/home
GET  /wp-json/appforge/v1/categories
GET  /wp-json/appforge/v1/products
GET  /wp-json/appforge/v1/products/{id}
GET  /wp-json/appforge/v1/config
```

Customer endpoints can include:

```text
POST /wp-json/appforge/v1/auth/login
POST /wp-json/appforge/v1/auth/register

GET  /wp-json/appforge/v1/me
PUT  /wp-json/appforge/v1/me

GET  /wp-json/appforge/v1/cart
POST /wp-json/appforge/v1/cart/items
PUT  /wp-json/appforge/v1/cart/items/{id}
DELETE /wp-json/appforge/v1/cart/items/{id}

GET  /wp-json/appforge/v1/orders
GET  /wp-json/appforge/v1/orders/{id}

GET  /wp-json/appforge/v1/wishlist
POST /wp-json/appforge/v1/wishlist
DELETE /wp-json/appforge/v1/wishlist/{product_id}

POST /wp-json/appforge/v1/device-token
```

Use WooCommerce REST API where it already provides what you need. Create custom endpoints only where the mobile app requires a better combined response or functionality not covered cleanly by the standard APIs.

---

# 17. Home API

Instead of making 10 API requests when the mobile app opens, create:

```text
GET /wp-json/appforge/v1/home
```

Response can contain:

```json
{
  "banners": [],
  "categories": [],
  "featuredProducts": [],
  "bestSellingProducts": [],
  "newProducts": [],
  "saleProducts": []
}
```

This makes the mobile home screen faster and easier to maintain.

---

# 18. App Configuration API

Create:

```text
GET /wp-json/appforge/v1/config
```

Example:

```json
{
  "appName": "ABC Store",
  "currency": "INR",
  "currencySymbol": "₹",
  "primaryColor": "#000000",
  "secondaryColor": "#FFFFFF",
  "logo": "https://example.com/logo.png",
  "enableWishlist": true,
  "enableReviews": true,
  "enableCod": true,
  "enablePushNotifications": true
}
```

This is extremely useful for your reusable product.

---

# 19. Branding System

For every client, configure:

```text
Brand Name
Logo
Favicon
Primary Color
Secondary Color
Font
App Icon
Splash Screen
Website Logo
Contact Information
Social Media
Support Email
Phone
Address
```

The mobile app should support white-label customization as much as practical.

---

# 20. Security

Important rules:

- HTTPS only
- Never store WordPress passwords locally
- Never expose WordPress admin credentials
- Never put payment secret keys in React Native
- Validate all API inputs
- Sanitize WordPress inputs
- Escape WordPress outputs
- Use WordPress nonces where applicable
- Use authentication tokens appropriately
- Restrict admin endpoints
- Apply role/capability checks
- Rate-limit sensitive endpoints
- Validate file uploads
- Keep plugins updated
- Keep WordPress updated
- Keep WooCommerce updated
- Use a security plugin/firewall where appropriate

Never modify WooCommerce core files.

---

# 21. Mobile App Project Structure

```text
appforge-ecommerce-mobile/
|
+-- src/
|   |
|   +-- api/
|   |   +-- client.ts
|   |   +-- authApi.ts
|   |   +-- productApi.ts
|   |   +-- cartApi.ts
|   |   +-- orderApi.ts
|   |   +-- userApi.ts
|   |
|   +-- screens/
|   |   +-- Splash/
|   |   +-- Onboarding/
|   |   +-- Auth/
|   |   +-- Home/
|   |   +-- Category/
|   |   +-- Product/
|   |   +-- Cart/
|   |   +-- Checkout/
|   |   +-- Orders/
|   |   +-- Wishlist/
|   |   +-- Profile/
|   |
|   +-- components/
|   +-- navigation/
|   +-- store/
|   +-- hooks/
|   +-- utils/
|   +-- constants/
|   +-- assets/
|   +-- types/
|
+-- android/
+-- ios/
```

---

# 22. WordPress Plugin Project Structure

```text
appforge-ecommerce/
|
+-- appforge-ecommerce.php
|
+-- includes/
|   +-- class-plugin.php
|   +-- class-settings.php
|   +-- class-auth.php
|   +-- class-home.php
|   +-- class-products.php
|   +-- class-cart.php
|   +-- class-orders.php
|   +-- class-wishlist.php
|   +-- class-notifications.php
|
+-- rest-api/
|   +-- class-routes.php
|   +-- class-auth-controller.php
|   +-- class-home-controller.php
|   +-- class-product-controller.php
|   +-- class-cart-controller.php
|   +-- class-order-controller.php
|
+-- admin/
|   +-- settings-page.php
|
+-- assets/
|   +-- css/
|   +-- js/
|
+-- languages/
|
+-- readme.txt
```

---

# 23. Recommended WordPress Plugins

Keep the base stack small.

## Required

- WooCommerce
- Payment gateway plugin
- SEO plugin
- Security plugin
- Cache/performance plugin
- SMTP/email plugin

## Optional

- Wishlist
- Product filter
- Shipping integration
- Invoice/PDF
- WhatsApp notifications
- Multi-vendor
- Product brands
- Advanced analytics

Do not install 30+ plugins by default. Every additional plugin increases maintenance, security and compatibility risk.

---

# 24. Client Customization Levels

This is important for your business model.

## Package 1 — Ready-made

Client gets:

- Existing design
- Mobile app
- Website
- WooCommerce
- Admin
- Standard features

Only basic branding changes.

## Package 2 — Customized

Client gets:

- Logo
- Colors
- Fonts
- Homepage changes
- Product/category structure
- Payment gateway
- Shipping
- Custom screens
- Additional features

## Package 3 — Advanced

Client gets:

- Custom UI/UX
- Custom WordPress plugin
- Advanced APIs
- Multi-vendor
- Delivery app
- Loyalty
- Referral
- Subscription
- Advanced reports
- Third-party integrations

---

# 25. Portfolio Demo

Your AppForge product page should show:

```text
E-Commerce App

Ready-made E-Commerce Solution

Customer Mobile App
Website
WooCommerce Admin
Payment Integration
Order Management
Inventory
Coupons
Reviews
Notifications

[ View Website ]
[ Test Mobile App ]
[ View Admin Demo ]
[ View Features ]
[ Request Customization ]
```

Include:

- Screenshots
- Short video
- Live website
- Demo credentials
- Mobile APK/TestFlight link where appropriate
- Admin demo
- Feature list
- Technology stack
- Customization options

Do not expose real client data in demos.

---

# 26. Demo Data

Create realistic demo data:

```text
Categories:
- Men
- Women
- Electronics
- Shoes
- Accessories

Products:
- 20–50 products

Customers:
- 10–20 demo users

Orders:
- 20–50 sample orders

Coupons:
- WELCOME10
- SALE20

Banners:
- Summer Sale
- New Arrivals
- Special Offer
```

The demo should look like a real business, not an empty development project.

---

# 27. Reusable Client Setup

For a new client:

```text
1. Create WordPress installation
2. Install WooCommerce
3. Install required plugins
4. Install AppForge plugin
5. Configure payment
6. Configure shipping
7. Configure tax
8. Add client logo/branding
9. Import/configure products
10. Configure homepage
11. Configure mobile app
12. Build Android/iOS release
13. Configure domain
14. Test
15. Deploy
```

The goal is to turn this into a repeatable checklist.

---

# 28. Future Modules

Do not build these into version 1 unless a client needs them.

```text
Advanced E-Commerce
|
+-- Multi Vendor
+-- Seller App
+-- Delivery Partner App
+-- Live Delivery Tracking
+-- Loyalty Points
+-- Referral Program
+-- Wallet
+-- Subscription Products
+-- Membership
+-- Gift Cards
+-- Abandoned Cart
+-- WhatsApp Automation
+-- AI Product Search
+-- Product Recommendations
+-- Multi Language
+-- Multi Currency
+-- Advanced Analytics
```

These can become premium customization opportunities.

---

# 29. Development Roadmap

## Phase 1 — Foundation

- WordPress setup
- WooCommerce setup
- AppForge plugin
- Mobile project
- API connection
- Authentication
- Basic configuration

## Phase 2 — Catalog

- Categories
- Products
- Product details
- Search
- Filters
- Variations
- Images
- Inventory

## Phase 3 — Shopping

- Cart
- Wishlist
- Address
- Coupons
- Checkout
- Shipping
- Tax

## Phase 4 — Orders & Payments

- Order creation
- Payment gateway
- Payment verification
- Order status
- Order history
- Cancellation
- Refund flow

## Phase 5 — Customer Experience

- Reviews
- Push notifications
- Profile
- Support
- Reorder
- Tracking

## Phase 6 — Website

- WooCommerce storefront
- Responsive design
- SEO
- Performance
- Legal pages

## Phase 7 — Demo & Productization

- Demo data
- Demo account
- Portfolio page
- Documentation
- Screenshots
- Video
- Pricing packages
- Customization checklist

---

# 30. Definition of Done — Base Product

The E-Commerce product is ready for your AppForge portfolio when:

- [ ] Customer can register/login
- [ ] Customer can browse categories
- [ ] Customer can search products
- [ ] Customer can filter products
- [ ] Customer can view product details
- [ ] Customer can select variations
- [ ] Customer can add to cart
- [ ] Customer can manage quantity
- [ ] Customer can manage addresses
- [ ] Customer can apply coupons
- [ ] Customer can checkout
- [ ] Customer can pay
- [ ] Customer can see orders
- [ ] Customer can track order status
- [ ] Customer can review products
- [ ] Customer can manage wishlist
- [ ] Customer receives notifications
- [ ] Admin can manage products
- [ ] Admin can manage categories
- [ ] Admin can manage inventory
- [ ] Admin can manage orders
- [ ] Admin can manage customers
- [ ] Admin can manage coupons
- [ ] Admin can manage banners
- [ ] Admin can configure shipping
- [ ] Admin can configure payments
- [ ] Website is responsive
- [ ] Mobile app works on Android
- [ ] Mobile app works on iOS
- [ ] API authentication is secure
- [ ] Payment verification is server-side
- [ ] Demo data is available
- [ ] Client branding can be changed
- [ ] No WooCommerce core files are modified

---

# 31. Recommended First Version

Do not spend months building every possible feature.

Build this first:

```text
                    APPFORGE E-COMMERCE
                           |
        +------------------+------------------+
        |                  |                  |
   React Native        WooCommerce       WordPress
   Customer App        Website/Admin      Plugin
        |                  |                  |
        +------------------+------------------+
                           |
                     Core Features
                           |
       Auth
       Categories
       Products
       Variations
       Search
       Cart
       Wishlist
       Address
       Coupons
       Checkout
       Payment
       Orders
       Reviews
       Notifications
       Inventory
```

Once this is stable, use the same architecture as your base for other AppForge products.

---

# 32. Business Strategy

The main advantage of this architecture is reuse.

You build the core once.

Then:

```text
Client A
Fashion Store
    ↓
AppForge E-Commerce
    ↓
Brand customization

Client B
Electronics Store
    ↓
Same AppForge E-Commerce
    ↓
Different branding + features

Client C
Grocery Store
    ↓
Same core
    ↓
Different categories + delivery rules
```

You are not selling "a WordPress website".

You are selling:

> **A ready-made E-Commerce business solution that can be customized for the client's brand and requirements.**

This dramatically reduces your development time for future projects.
