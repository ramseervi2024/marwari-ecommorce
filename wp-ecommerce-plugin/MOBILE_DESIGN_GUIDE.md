# Mārwāri E-Commerce — Mobile App UI/UX Layout Design Guide
### React Native (iOS & Android) Component Specs, Design System & Screen Wireframes

---

## 🎨 1. Design System & Visual Tokens

The visual identity combines **Royal Rajasthani Heritage** with modern, high-converting e-commerce UX (clean cards, smooth micro-animations, glassmorphism badges, and 8pt spatial grid).

### 1.1 Color Palette

| Token Name | Hex Code | Usage |
| :--- | :--- | :--- |
| **Primary (Royal Maroon)** | `#831843` / `#991B1B` | Primary CTAs, active highlights, badges, price labels |
| **Secondary (Heritage Amber)** | `#B45309` / `#D97706` | Secondary accents, star ratings, category highlights |
| **Royal Gold Accent** | `#FEF08A` / `#F59E0B` | Notice banners, royal crests, VIP/Artisan tags |
| **Auth Brand Teal** | `#077B9F` / `#056482` | Fullscreen Auth background & form buttons (per spec reference) |
| **Background (Light)** | `#F8FAFC` | App screen canvas background |
| **Card / Surface** | `#FFFFFF` | Elevated cards, bottom sheets, navigation bar |
| **Text Primary** | `#0F172A` | Product titles, section headings, prices |
| **Text Secondary** | `#64748B` | Subtitles, specifications, meta info |
| **Border / Divider** | `#E2E8F0` | Card borders, dividers, list separators |
| **Success Green** | `#059669` | Order completed, in-stock badges, applied coupons |
| **Danger / Alert** | `#DC2626` | Error states, delete action, out-of-stock tags |

### 1.2 Typography Hierarchy

| Style | Size | Line Height | Weight | Usage |
| :--- | :--- | :--- | :--- | :--- |
| **Hero Display** | 28px | 34px | Bold (800) | Hero banner headings, Welcome titles |
| **H1 Heading** | 22px | 28px | Bold (700) | Screen titles, Product detail name |
| **H2 Section Title**| 18px | 24px | SemiBold (600) | Section headers ("Explore Categories") |
| **H3 Card Title** | 15px | 20px | SemiBold (600) | Product card titles, order numbers |
| **Body Large** | 15px | 22px | Regular (400) | Product descriptions, terms |
| **Body Regular** | 13px | 18px | Regular (400) | Subtitles, address details, specs |
| **Caption / Badge** | 11px | 14px | Medium (600) | Category tags, discount badges, timestamps |

### 1.3 Spatial Spacing (8pt Grid)
- **Container Screen Padding**: `16px` (Horizontal margins)
- **Card Padding**: `12px` to `16px`
- **Component Gap**: `8px`, `12px`, `16px`, `24px`
- **Card Border Radius**: `12px` to `16px`
- **Button Border Radius**: `10px` (or `99px` for pill buttons)

---

## 📱 2. Information Architecture & Navigation Hierarchy

```text
RootNavigator
│
├── AuthStack (Conditional if Guest / Logged out)
│   ├── WelcomeScreen (Hero intro, slider)
│   ├── LoginScreen (#077B9F Teal Card Layout)
│   ├── RegisterScreen (Name, Email, Mobile, Password)
│   └── VerifyOTPScreen (6-digit keypad & auto-read)
│
└── MainAppTabs (Bottom Tab Navigator)
    ├── Tab 1: HomeScreen (Feed, Hero Carousel, Categories, Treasures, Cities)
    ├── Tab 2: CategoriesScreen (All 7 Heritage Collections with counts)
    ├── Tab 3: CartScreen (Cart list, Coupon input, Order bill summary)
    ├── Tab 4: OrdersScreen (Order history, shipment tracking, invoices)
    └── Tab 5: ProfileScreen (Profile details, saved addresses, preferences)

Common Stack Screens (Pushed over Tabs):
├── ProductDetailsScreen (Zoomable gallery, specs, buy bar)
├── CheckoutScreen (Address selector, payment gateways)
├── OrderSuccessScreen (Confetti celebration, order invoice)
├── OrderTrackingScreen (Timeline stepper, delivery status)
└── UpdateProfileScreen (Edit personal details, address CRUD)
```

---

## 🖼️ 3. Screen-by-Screen Layout Specifications

---

### Screen 1: Home Screen (`HomeScreen.jsx`)

#### Top Navigation Bar (Sticky)
- **Left**: Royal Logo / App Title (`MĀRWĀRI E-COMMERCE`) with location picker dropdown (`📍 Jodhpur, 342001 ▼`).
- **Right**: 
  - Search icon button (taps open instant search modal).
  - Wishlist Heart button (with badge).
  - Shopping Bag button (with live item count badge).

#### Section 1: Hero Carousel Banner
- **Format**: Auto-playing snap carousel (`ReanimatedUniversalCarousel`) with dot indicators.
- **Card Layout**:
  - Left: "Royal Rajasthan Collection" gold badge + Catchy Headline ("Handcrafted Elegance by Master Artisans") + "Explore Collection →" CTA button.
  - Right: High-resolution transparent cutout or portrait of heritage art.

#### Section 2: Heritage Categories Scroll (`7 Categories`)
- Horizontal scroller with round or squircle avatar cards:
  1. 👗 **Royal Apparel** (Bandhani & Jodhpuri suits)
  2. 🏺 **Handicrafts** (Blue Pottery, Wood carving)
  3. 💍 **Silver Jewellery** (Kundan & Meenakari)
  4. 👞 **Marwari Mojari** (Handcrafted camel leather)
  5. 🍬 **Food & Spices** (Mathania chilli & Bikaneri sweets)
  6. 🛋️ **Home & Décor** (Quilts & Brass lamps)
  7. 🎨 **Art & Collectibles** (Miniature paintings & Puppets)

#### Section 3: Featured Treasures (2-Column Grid)
- **Product Card (`ProductCard.jsx`)**:
  - Image with 1:1 aspect ratio, subtle inner shadow.
  - Floating top-left badge: `15% OFF` or `Bestseller`.
  - Floating top-right heart icon: Wishlist toggle (turns red when active).
  - Category pill (`ROYAL APPAREL`).
  - Product Title (2-line truncate with `...`).
  - Star rating with review count (`⭐ 5.0 (134)`).
  - Price row: `₹7,899` (Bold Maroon) + `₹9,083` (Strikethrough Gray).
  - Quick "Add to Bag" button with shopping cart icon.

#### Section 4: Royal Cities of Rajasthan (Horizontal Cards)
- Cards for **Jodhpur**, **Jaipur**, **Udaipur**, **Bikaner**.
- High-res palace imagery, specialty tagline, and tap to filter.

#### Section 5: Artisan Trust Banner
- Horizontal grid highlighting 4 trust pillars:
  - 🛡️ 100% Authentic Heritage Craft
  - 🚚 Pan-India Free Delivery (> ₹999)
  - 🔁 7-Day Hassle-Free Returns
  - 🔒 100% Secure Razorpay / UPI

---

### Screen 2: Product Detail Screen (`ProductDetailsScreen.jsx`)

#### Visual Layout:
1. **Header**: Back arrow (`←`), Product Title (shortened), Share button, Cart badge button.
2. **Image Gallery**:
   - High-resolution hero image container with pinch-to-zoom and double-tap zoom gestures.
   - Bottom thumbnail carousel for switching angles/colors.
   - "2.2x Interactive Zoom" overlay badge.
3. **Product Information Block**:
   - Category name in uppercase with letter-spacing (`HANDICRAFTS`).
   - Title: `Imperial Udaipur Heritage Silver Peacock Box` (22px bold).
   - Star Rating row: 5 gold stars + `(134 Verified Reviews)` + `✓ Certified Authentic`.
   - Price Row: `₹7,899` (Large 26px Maroon) | `₹9,083` (Strikethrough) | `15% OFF` (Green chip).
4. **Selector Blocks**:
   - Variant / Size chip selector (e.g. Standard, Luxury Gift Box).
   - Live Quantity Stepper: `[ - ]` ` 1 ` `[ + ]`.
5. **Description Accordion**:
   - Story of the craft, artisan origin, materials (pure brass/silver), dimensions, and care instructions.
6. **Related Heritage Treasures Carousel**:
   - Horizontal list of 4 recommended products from the same category.
7. **Sticky Bottom Action Bar**:
   - Left: Wishlist heart toggle button.
   - Center: **Add to Bag** (Outline maroon button).
   - Right: **Buy Now** (Solid Maroon button &rarr; navigates straight to Checkout).

---

### Screen 3: Login & Register Screens (`LoginScreen.jsx`, `RegisterScreen.jsx`)

*(Engineered to match your reference design `media_1791296500459.png`)*

- **Background Canvas**: Fullscreen deep teal (`#077B9F`).
- **Elevated Card**:
  - Centered white card (`#FFFFFF`, border radius: `8px`, elevation: `10`, horizontal margin: `20px`).
  - Top Back to Store link (`← Back to Store`).
  - Title: `Login` or `Create Account` (24px, color `#4B5563`).
- **Input Fields**:
  - Clean light border (`#D1D5DB`, active focus border `#077B9F`).
  - Fields: Email / Username, Password.
  - "Show Password" checkbox with live text toggle.
- **Action Button**:
  - Full-width uppercase `SIGN IN` or `SIGN UP` button in `#077B9F` with white bold text.
- **Footer Links**:
  - "Forgot Username / Password?" (Password reset modal).
  - "Don't have an account? **Sign up**" (Instantly toggles between Login & Register without screen flicker).

---

### Screen 4: Cart & Bag Screen (`CartScreen.jsx`)

- **Empty State**: Elegant illustration of Rajasthani shopping bag + "Your royal bag is empty" + "Start Shopping" button.
- **Cart List Items**:
  - Row layout: 80x80 thumbnail image on left.
  - Middle: Product name, category, price per unit.
  - Right: Quantity Stepper (`-`, `1`, `+`) + Trash icon button to remove.
- **Coupon Code Box**:
  - Text input with "APPLY" button.
  - Quick-tap coupon chips below: `[ MARWARI10 ]` (10% Off), `[ ROYAL500 ]` (₹500 Off).
- **Price Breakdown Card**:
  - Item Subtotal: `₹7,899`
  - Coupon Discount: `- ₹789`
  - Delivery Fee: `FREE` (Orders above ₹999)
  - Estimated Tax / GST: `Included`
  - **Total Payable**: `₹7,110` (Bold)
- **Sticky Bottom Checkout Bar**:
  - Displays total amount on left + large **"Proceed to Checkout →"** button on right.

---

### Screen 5: Checkout & Payment (`CheckoutScreen.jsx`)

1. **Delivery Address Section**:
   - Selected address card with radio button.
   - Customer name, phone number, complete address, PIN code.
   - "+ Add New Address" button opens bottom sheet modal.
2. **Order Summary Review**:
   - Collapsible list of items being purchased.
3. **Payment Methods (Accordion Selection)**:
   - ⚡ **UPI Instant Payment** (Google Pay, PhonePe, Paytm, BHIM).
   - 💳 **Credit / Debit Cards & Net Banking** (via Razorpay SDK).
   - 💵 **Cash on Delivery (COD)** (Verified with OTP).
4. **Place Order CTA**:
   - Sticky button: **"Place Order & Pay ₹7,110"**.
   - Triggers order placement and redirects to `OrderSuccessScreen` with celebration confetti.

---

### Screen 6: Orders & Live Tracking (`OrdersScreen.jsx`, `OrderDetailsScreen.jsx`)

- **Order Card**:
  - Header: Order ID (`#ORD-2026-8941`) + Date + Status badge (`Processing` in amber, `Delivered` in green).
  - Body: Thumbnail images of ordered products with item count.
  - Footer: Total amount paid + "Track Order" button.
- **Order Tracking Stepper Screen**:
  - Step 1: `Order Placed` (Checked)
  - Step 2: `Packed & Dispatched from Jodhpur` (Checked)
  - Step 3: `In Transit (Courier: BlueDart)` (Active with tracking code copy button)
  - Step 4: `Out for Delivery`
  - Step 5: `Delivered`
  - "Download Tax Invoice (PDF)" action button.

---

## 🧩 4. Ready-to-Use React Native Component Snippets

### 4.1 Product Card Component (`ProductCard.jsx`)

```jsx
import React from 'react';
import { View, Text, Image, TouchableOpacity, StyleSheet } from 'react-native';
import { Heart, ShoppingBag } from 'lucide-react-native';

export default function ProductCard({ product, onPress, onAddToCart, onToggleWishlist, isWishlisted }) {
  const originalPrice = product.price + Math.floor(product.price * 0.15);

  return (
    <TouchableOpacity style={styles.card} activeOpacity={0.85} onPress={onPress}>
      <View style={styles.imageContainer}>
        <Image source={{ uri: product.image }} style={styles.image} resizeMode="cover" />
        
        {product.badge && (
          <View style={styles.badge}>
            <Text style={styles.badgeText}>{product.badge}</Text>
          </View>
        )}

        <TouchableOpacity 
          style={styles.wishlistBtn} 
          onPress={() => onToggleWishlist(product.id)}
          hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
        >
          <Heart size={18} color={isWishlisted ? '#DC2626' : '#64748B'} fill={isWishlisted ? '#DC2626' : 'transparent'} />
        </TouchableOpacity>
      </View>

      <View style={styles.info}>
        <Text style={styles.category}>{product.category}</Text>
        <Text style={styles.title} numberOfLines={2}>{product.name}</Text>
        
        <View style={styles.priceRow}>
          <Text style={styles.price}>₹{product.price.toLocaleString('en-IN')}</Text>
          <Text style={styles.originalPrice}>₹{originalPrice.toLocaleString('en-IN')}</Text>
        </View>

        <TouchableOpacity style={styles.addCartBtn} onPress={() => onAddToCart(product.id)}>
          <ShoppingBag size={15} color="#FFFFFF" />
          <Text style={styles.addCartText}>Add to Bag</Text>
        </TouchableOpacity>
      </View>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  card: {
    flex: 1,
    backgroundColor: '#FFFFFF',
    borderRadius: 14,
    margin: 6,
    borderWidth: 1,
    borderColor: '#E2E8F0',
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 6,
    elevation: 2,
  },
  imageContainer: {
    width: '100%',
    aspectRatio: 1,
    backgroundColor: '#F8FAFC',
    position: 'relative',
  },
  image: {
    width: '100%',
    height: '100%',
  },
  badge: {
    position: 'absolute',
    top: 8,
    left: 8,
    backgroundColor: 'rgba(131, 24, 67, 0.9)',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 6,
  },
  badgeText: {
    color: '#FFFFFF',
    fontSize: 10,
    fontWeight: '700',
    textTransform: 'uppercase',
  },
  wishlistBtn: {
    position: 'absolute',
    top: 8,
    right: 8,
    backgroundColor: '#FFFFFF',
    padding: 6,
    borderRadius: 20,
    elevation: 3,
  },
  info: {
    padding: 12,
  },
  category: {
    fontSize: 10,
    fontWeight: '700',
    color: '#B45309',
    textTransform: 'uppercase',
    letterSpacing: 0.5,
    marginBottom: 4,
  },
  title: {
    fontSize: 13,
    fontWeight: '600',
    color: '#0F172A',
    lineHeight: 18,
    minHeight: 36,
    marginBottom: 8,
  },
  priceRow: {
    flexDirection: 'row',
    alignItems: 'baseline',
    gap: 6,
    marginBottom: 10,
  },
  price: {
    fontSize: 16,
    fontWeight: '800',
    color: '#991B1B',
  },
  originalPrice: {
    fontSize: 12,
    color: '#94A3B8',
    textDecorationLine: 'line-through',
  },
  addCartBtn: {
    backgroundColor: '#831843',
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 8,
    borderRadius: 8,
    gap: 6,
  },
  addCartText: {
    color: '#FFFFFF',
    fontSize: 12,
    fontWeight: '600',
  },
});
```

---

### 4.2 Category Circular Pill Component (`CategoryPill.jsx`)

```jsx
import React from 'react';
import { View, Text, Image, TouchableOpacity, StyleSheet } from 'react-native';

export default function CategoryPill({ item, isSelected, onPress }) {
  return (
    <TouchableOpacity style={styles.container} activeOpacity={0.8} onPress={onPress}>
      <View style={[styles.imageWrapper, isSelected && styles.selectedWrapper]}>
        <Image source={{ uri: item.image }} style={styles.image} resizeMode="cover" />
      </View>
      <Text style={[styles.name, isSelected && styles.selectedName]} numberOfLines={1}>
        {item.name}
      </Text>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  container: {
    alignItems: 'center',
    width: 76,
    marginHorizontal: 6,
  },
  imageWrapper: {
    width: 62,
    height: 62,
    borderRadius: 31,
    padding: 2,
    borderWidth: 2,
    borderColor: '#E2E8F0',
    overflow: 'hidden',
    backgroundColor: '#FFFFFF',
  },
  selectedWrapper: {
    borderColor: '#831843',
    borderWidth: 2.5,
  },
  image: {
    width: '100%',
    height: '100%',
    borderRadius: 30,
  },
  name: {
    fontSize: 11,
    fontWeight: '500',
    color: '#475569',
    marginTop: 6,
    textAlign: 'center',
  },
  selectedName: {
    color: '#831843',
    fontWeight: '700',
  },
});
```

---

## 📋 5. Summary Checklist for Frontend Developers

- [x] **Theme & Colors**: Setup primary maroon `#831843`, amber `#B45309`, and auth teal `#077B9F` in `src/theme/theme.ts`.
- [x] **API Connectivity**: Use base URL `https://rpsdigitalworld.store/wp-json/wp-ecommerce/v1` (endpoints documented in `REACT_NATIVE_SWAGGER_API.md`).
- [x] **Guest User Rules**: Browse products freely; on "Add to Cart" or "Checkout", trigger the `#077B9F` Auth modal.
- [x] **Product Detail View**: Support 20 complete heritage products with image zoom, rating stars, and live quantity selection.
- [x] **Order Flow**: Order placement calls `POST /orders` and routes to `OrderDetailsScreen` with live BlueDart/SpeedPost tracking stepper.
