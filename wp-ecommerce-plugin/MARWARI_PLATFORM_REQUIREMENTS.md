# Mārwāri E-Commerce Platform Requirements

This document captures the recent advanced features built for the **Mobile App** and maps out the equivalent features and system capabilities required for the **Consumer Website** and the **Admin Dashboard** to ensure a fully synced, premium ecosystem.

---

## Part 1: Mobile App Accomplishments (Reference)

Here is a summary of the critical logic and UI/UX implementations recently completed in the Mobile App. You can use this as the standard for how the rest of the platform should behave.

### 1. Advanced Guest Gatekeeping (Auth Interceptors)
- **Seamless Browsing**: Guest users can browse the Home Feed, Collections, view Product Details, zoom into images, and even add items to their local Cart without interruption.
- **Tab-Level Interception**: Clicking restricted tabs (Profile, Orders, Cart) automatically pops up the `GuestAuthModal` without breaking the navigation stack.
- **Action-Level Interception**: Clicking the "Place Order" button in Checkout triggers the login popup if the user is unauthenticated.
- **Route-Level Protection**: The `ProfileScreen`, `OrdersScreen`, and `CartScreen` conditionally check `!isAuthenticated` and block rendering with a full-screen prompt. We used `useIsFocused` to ensure modals don't freeze the app in the background.

### 2. Premium Authentication Flow
- **Redesigned UI**: Implemented a rich Maroon/Slate "Royal Patron" aesthetic for `LoginScreen`, `RegisterScreen`, and `VerifyOTPScreen`.
- **Streamlined UX**: Removed social/Google auth options per request, tightened padding/margins for a professional compact look, and pinned the header so the form scrolls smoothly underneath it.
- **Persistent Sessions**: Integrated `AsyncStorage` to securely remember authenticated tokens and OTP verifications across app restarts, with a clean "Logout" flow to clear them.

### 3. E-Commerce & Checkout Enhancements
- **Address Management**: Users must add and select a valid delivery address before proceeding. Integrated a smooth "Add New Address" modal within the checkout flow.
- **Product Zooming**: Enabled pinch-to-zoom on product images for a premium "Masterpiece" inspection experience.
- **Coupon Logic**: Hardcoded promo codes (`MARWARI10`, `ROYAL500`) with dynamic cart total calculations.
- **State Persistence**: Ensured local Redux state accurately pushes new orders to the top of the user's "Order History" immediately after checkout.

---

## Part 2: Consumer Website Implementation Guide

To maintain a consistent brand experience, the web application (React/Next.js) must mirror the mobile app's behavior.

### 1. UI & Aesthetics
- **Theme**: Stick to the Maroon (`#831843`), Slate/Dark Navy (`#0F172A`), and White color palette.
- **Typography**: Use a premium sans-serif or serif font (e.g., Playfair Display for headers, Inter for body) to match the "Heritage" feel.

### 2. Authentication & Guest Flow
- **Persistent Navbar**: The header should contain links to Home, Collections, Cart, and Profile.
- **Guest Interceptors**: 
  - If a guest clicks "Profile", "Orders", or "Checkout", trigger a **React Portal Modal** (the web equivalent of `GuestAuthModal`) prompting them to Login/Register.
  - Do NOT redirect them to a separate `/login` page if they click "Checkout"—keep them in context using the modal.
- **Auth State**: Use JWT tokens stored in `HttpOnly` cookies or `localStorage`, managed via Redux/Context.

### 3. Shopping Experience
- **Product Gallery**: Implement an image lightbox library (e.g., `yet-another-react-lightbox`) for high-resolution pinch-to-zoom.
- **Cart & Checkout**:
  - Cart state must persist for guests (e.g., using `localStorage`).
  - Checkout must require Address creation/selection. Provide a clean UI form for adding addresses dynamically.
  - Implement the exact same Coupon validation logic.

---

## Part 3: Admin Dashboard Implementation Guide

To manage this advanced e-commerce platform, the Admin Dashboard (React/Next.js + Tailwind) requires robust backend controls.

### 1. Order Management
- **Live Order Feed**: A real-time data table showing all incoming orders with their `ORD-XXXX` IDs.
- **Status Updates**: Admins must be able to change order statuses (`Processing` -> `Dispatched` -> `In Transit` -> `Delivered`). Changing these statuses should reflect instantly in the User's Mobile App / Web App "Orders" tab.
- **Invoicing**: Ability to generate and download PDF invoices for orders.

### 2. Catalog & Product Management
- **CRUD Operations**: Add, Edit, Delete, and Hide products.
- **Image Uploads**: Support high-res image uploads to AWS S3 or similar, returning URLs for the mobile/web apps.
- **Stock Management**: Track inventory quantity. Prevent users from adding items to the cart if stock is 0.

### 3. User & Patron Management
- **Customer Directory**: View all registered users, their phone numbers (verified via OTP), and their saved addresses.
- **Order History**: View the complete purchase history of any specific user to provide "Royal Concierge Support".

### 4. Promotions & Coupons
- **Dynamic Coupon Engine**: Instead of hardcoding `MARWARI10` or `ROYAL500`, admins should be able to create new coupons in the dashboard.
- **Fields required**: `Coupon Code`, `Discount Type` (Flat vs Percent), `Discount Value`, `Expiry Date`, and `Usage Limit`.

### 5. Admin Authentication
- **Role-Based Access**: The admin portal should be strictly locked behind a Super-Admin login.
- **Security**: Implement standard JWT authentication with short expiries to protect business data.
