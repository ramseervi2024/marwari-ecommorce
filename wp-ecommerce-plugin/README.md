# Marwari E-Commerce Platform

## Project Name

Marwari E-Commerce – Padharo Mhare Des

---

# Project Description

Marwari E-Commerce is a premium Rajasthan heritage marketplace showcasing authentic Marwari products including:

* Royal Jodhpuri Suits
* Bandhani Sarees
* Jaipur Handicrafts
* Silver Jewelry
* Traditional Mojaris
* Sweets & Spices
* Blue Pottery
* Heritage Collections

The platform consists of:

1. Customer Website
2. Authentication System
3. Shopping Cart
4. Checkout Process
5. Admin Dashboard
6. Product Management
7. Order Management

---

# Technology Stack

## Frontend

* WordPress
* Elementor Pro
* HTML5
* CSS3
* JavaScript

## Backend

* WordPress CMS
* PHP 8+

## Database

* MySQL

## E-Commerce

* WooCommerce

---

# User Roles

## Guest User

Can:

* View Homepage
* Browse Products
* Search Products
* View Categories
* View Product Details

Cannot:

* Place Orders
* View Orders
* Access Dashboard

---

## Customer

Can:

* Login
* Signup
* Add to Cart
* Checkout
* View Orders
* Update Profile
* Manage Address
* Logout

---

## Admin

Can:

* Manage Products
* Manage Orders
* Manage Customers
* View Dashboard Analytics
* Manage Categories
* Manage Coupons
* Logout

---

# Demo Login Credentials

## Customer Login

Mobile Number:
9876543210

OTP:
123456

---

## Admin Login

Username:
admin

Password:
123456

Note:
These credentials are only for development/demo purposes.

---

# Website Pages

## Public Pages

### Home

URL:
/

Sections:

* Header
* Search Bar
* Hero Banner
* Category Navigation
* Featured Products
* Heritage Collection
* Bestseller Products
* Footer

---

### Shop

URL:
/shop

Features:

* Product Listing
* Search
* Filters
* Categories
* Pagination

---

### Product Details

URL:
/product/{slug}

Features:

* Product Images
* Product Description
* Product Price
* Category
* Add To Cart
* Related Products

---

### Login

URL:
/login

Fields:

* Mobile Number
* OTP Code

Buttons:

* Send OTP
* Verify OTP

---

### Signup

URL:
/signup

Fields:

* Full Name
* Mobile Number
* Email
* Password

---

### Cart

URL:
/cart

Features:

* Product List
* Quantity Update
* Remove Product
* Total Amount

---

### Checkout

URL:
/checkout

Fields:

* Name
* Mobile
* Address
* City
* State
* Pincode

Payment Methods:

* Razorpay
* UPI
* COD

---

### My Account

URL:
/account

Sections:

* Profile
* Orders
* Addresses
* Logout

---

# Homepage Sections

## Header

Logo:
Mārwāri

Menu:

* Home
* Shop
* Categories
* Login

Search Box:

Placeholder:

Search for royal heritage products...

---

## Hero Banner

Title:

Padharo Mhare Des

Subtitle:

Authentic Marwari Treasures

CTA:

Explore Collection

Banner Image:

Royal Jodhpuri Suit

---

## Categories

* Royal Apparel
* Jaipur Handicrafts
* Silver Jewelry
* Sweets & Spices

---

## Product Cards

Show:

* Image
* Badge
* Category
* Product Name
* Description
* Price
* Add To Cart

---

# Database Structure

## Users

users

* id
* name
* mobile
* email
* password
* role
* status
* created_at

---

## Products

products

* id
* title
* slug
* description
* category_id
* image
* price
* stock
* status
* created_at

---

## Categories

categories

* id
* name
* slug
* image

---

## Orders

orders

* id
* customer_id
* total_amount
* payment_method
* status
* created_at

---

## Order Items

order_items

* id
* order_id
* product_id
* quantity
* price

---

# Admin Panel

## URL

/admin

or

/wp-admin

---

# Admin Sidebar

Dashboard

├── Overview
├── Products
├── Orders
├── Customers
├── Categories
├── Coupons
├── Reports
├── Settings
└── Logout

---

# Overview Dashboard

Cards:

* Total Products
* Total Orders
* Total Customers
* Revenue

Widgets:

* Recent Orders
* Latest Customers
* Best Selling Products

---

# Product Management

## Product List

Columns:

* Image
* Product Name
* Category
* Price
* Stock
* Status
* Actions

Actions:

* Edit
* Delete
* View

---

## Add Product

Fields:

* Product Name
* Description
* Category
* Price
* Sale Price
* Images
* Inventory
* Status

Buttons:

* Save Draft
* Publish

---

## Edit Product

Admin can:

* Update Information
* Change Images
* Change Pricing
* Change Stock

---

# Order Management

## Order List

Columns:

* Order ID
* Customer
* Amount
* Status
* Date

Actions:

* View
* Update Status
* Cancel

---

# Order Status

* Pending
* Processing
* Shipped
* Delivered
* Cancelled

---

# Customer Management

Features:

* View Customer
* Search Customer
* View Orders
* Block User

---

# Category Management

Admin can:

* Create Category
* Edit Category
* Delete Category

Default Categories:

* Royal Apparel
* Jaipur Handicrafts
* Silver Jewelry
* Sweets & Spices

---

# Coupon Management

Fields:

* Coupon Code
* Discount Type
* Discount Amount
* Expiry Date

---

# Reports

Show:

* Sales Report
* Orders Report
* Customer Report
* Revenue Analytics

---

# Notifications

Customer:

* Order Confirmed
* Order Shipped
* Order Delivered

Admin:

* New Order Received
* Product Out Of Stock

---

# Security

* JWT Authentication
* Role Based Access
* Password Hashing
* SSL Enabled
* CSRF Protection

---

# Future Enhancements

Phase 2:

* Wishlist
* Product Reviews
* Referral System
* Loyalty Points

Phase 3:

* Mobile App
* Multi Vendor Marketplace
* WhatsApp Commerce
* AI Product Recommendations

---

# Deployment

Production Server Requirements

* PHP 8.2+
* MySQL 8+
* Apache/Nginx
* SSL Certificate

Recommended Hosting:

* Hostinger VPS
* DigitalOcean
* AWS Lightsail

---

# Version

v1.0.0

---

# Copyright

© 2026 Marwari E-Commerce

Padharo Mhare Des

Crafted with Royal Pride for Rajasthan Heritage.
