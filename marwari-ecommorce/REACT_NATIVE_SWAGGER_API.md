# Mārwāri E-Commerce API Specification (Swagger / OpenAPI 3.0)
### React Native Mobile App (iOS / Android) & Web Client Integration

---

## 📌 1. API Overview & Server Environments

| Property | Value |
| :--- | :--- |
| **Base URL (Production)** | `https://rpsdigitalworld.store/wp-json/wp-ecommerce/v1` |
| **Base URL (Local)** | `http://localhost/wp-json/wp-ecommerce/v1` |
| **Content-Type** | `application/json` |
| **Interactive Swagger UI** | [`https://rpsdigitalworld.store/ecommerce/api-docs`](https://rpsdigitalworld.store/ecommerce/api-docs) |
| **Raw OpenAPI 3.0 Spec** | [`https://rpsdigitalworld.store/wp-json/wp-ecommerce/v1/swagger`](https://rpsdigitalworld.store/wp-json/wp-ecommerce/v1/swagger) |
| **Auth Scheme** | Bearer JWT Token (`Authorization: Bearer <token>`) |

---

## 🔐 2. Authentication & Authorization

All protected routes (Cart, Checkout, Customer Profile, Order History) require the Bearer token in the HTTP Authorization header:

```http
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

---

## 📡 3. Complete Endpoints Reference

### 3.1 Authentication (`/auth`)

#### 1. Email / Password Login
- **Endpoint:** `POST /auth/login`
- **Description:** Authenticates user and returns session JWT token.

**Request Body:**
```json
{
  "email": "user@gmail.com",
  "password": "password123"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJlbWFpbCI6InVzZXJAZ21haWwuY29tIiwicm9sZSI6InVzZXIiLCJleHAiOjE3OTIxNjAwMDB9.signature",
  "user": {
    "username": "user",
    "email": "user@gmail.com",
    "name": "Ramesh Seervi",
    "role": "user",
    "phone": "9001122334",
    "addresses": [
      {
        "id": "addr-1",
        "label": "Home Base",
        "street": "12 Heritage Lane",
        "city": "Jodhpur",
        "zip": "342001",
        "default": true
      }
    ]
  }
}
```

#### 2. Customer Registration
- **Endpoint:** `POST /auth/register`
- **Description:** Creates new customer account and logs in automatically.

**Request Body:**
```json
{
  "name": "Ramesh Seervi",
  "email": "ramesh@example.com",
  "phone": "9876543210",
  "password": "SecurePassword123"
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "username": "ramesh",
    "email": "ramesh@example.com",
    "name": "Ramesh Seervi",
    "phone": "9876543210",
    "role": "user"
  }
}
```

#### 3. Send Mobile OTP
- **Endpoint:** `POST /auth/send-otp`
- **Description:** Sends SMS verification code for mobile login.

**Request Body:**
```json
{
  "phone": "9876543210"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "OTP code sent to +91 9876543210"
}
```

#### 4. Verify Mobile OTP
- **Endpoint:** `POST /auth/verify-otp`
- **Description:** Verifies OTP code and authenticates mobile session.

**Request Body:**
```json
{
  "phone": "9876543210",
  "otp": "123456"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "username": "9876543210",
    "name": "Guest 3210",
    "phone": "9876543210",
    "role": "user"
  }
}
```

---

### 3.2 Mobile Home Screen Feed (`/home`)

#### Get Unified Home Feed
- **Endpoint:** `GET /home`
- **Description:** Single high-speed payload for React Native initial screen with banners, heritage categories, top treasures, and royal cities.

**Response (200 OK):**
```json
{
  "banners": [
    {
      "id": "b-1",
      "title": "The Royal Heritage of Rajasthan",
      "subtitle": "Handcrafted by Master Artisans of Marwar",
      "image": "https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?auto=format&fit=crop&w=1200&q=80",
      "action_url": "/category/Royal Apparel"
    }
  ],
  "categories": [
    { "id": "cat-1", "name": "Royal Apparel", "slug": "Royal Apparel", "image": "https://..." },
    { "id": "cat-2", "name": "Handicrafts", "slug": "Handicrafts", "image": "https://..." },
    { "id": "cat-3", "name": "Silver Jewellery", "slug": "Silver Jewellery", "image": "https://..." },
    { "id": "cat-4", "name": "Marwari Mojari", "slug": "Marwari Mojari", "image": "https://..." },
    { "id": "cat-5", "name": "Food & Spices", "slug": "Food & Spices", "image": "https://..." },
    { "id": "cat-6", "name": "Home & Décor", "slug": "Home & Décor", "image": "https://..." },
    { "id": "cat-7", "name": "Art & Collectibles", "slug": "Art & Collectibles", "image": "https://..." }
  ],
  "featured_products": [
    {
      "id": "prod-1",
      "name": "Royal Jaipuri Silk Bandhani Saree",
      "category": "Royal Apparel",
      "price": 8499,
      "badge": "Bestseller",
      "image": "https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=600&q=80"
    },
    {
      "id": "prod-20",
      "name": "Imperial Udaipur Heritage Silver Peacock Box",
      "category": "Handicrafts",
      "price": 7899,
      "badge": "Royal Masterpiece",
      "image": "https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?auto=format&fit=crop&w=600&q=80"
    }
  ],
  "heritage_cities": [
    { "name": "Jodhpur", "specialty": "Royal Bandhgala & Mojaris" },
    { "name": "Jaipur", "specialty": "Blue Pottery & Bandhani" },
    { "name": "Udaipur", "specialty": "Silver & Meenakari Art" },
    { "name": "Bikaner", "specialty": "Sweets, Spices & Bhujia" }
  ]
}
```

---

### 3.3 Products Catalog (`/products`)

#### 1. List Products
- **Endpoint:** `GET /products`
- **Query Parameters:**
  - `category` *(optional)*: Filter by category (e.g. `Royal Apparel`, `Handicrafts`)
  - `search` *(optional)*: Search query string
  - `sort_by` *(optional)*: `price_asc`, `price_desc`, `newest`, `popular`
  - `page` *(optional)*: Default `1`
  - `limit` *(optional)*: Default `20`

**Response (200 OK):**
```json
[
  {
    "id": "prod-20",
    "name": "Imperial Udaipur Heritage Silver Peacock Box",
    "category": "Handicrafts",
    "price": 7899,
    "original_price": 9083,
    "discount_percent": 15,
    "description": "An extraordinary masterwork created by royal silversmiths of Udaipur. Crafted with hand-carved floral repoussé engraving and a majestic perched peacock on the lid, lined with royal maroon velvet.",
    "image": "https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?auto=format&fit=crop&w=600&q=80",
    "badge": "Royal Masterpiece",
    "rating": 5.0,
    "reviews_count": 134,
    "in_stock": true
  }
]
```

#### 2. Get Single Product by ID
- **Endpoint:** `GET /products/{id}`
- **Path Parameter:** `id` can be numeric (`20`) or prefixed (`prod-20`).

**Response (200 OK):**
```json
{
  "product": {
    "id": "prod-20",
    "name": "Imperial Udaipur Heritage Silver Peacock Box",
    "category": "Handicrafts",
    "price": 7899,
    "original_price": 9083,
    "description": "An extraordinary masterwork created by royal silversmiths of Udaipur...",
    "image": "https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?auto=format&fit=crop&w=600&q=80",
    "badge": "Royal Masterpiece"
  },
  "recommended": [
    {
      "id": "prod-7",
      "name": "Jaipur Traditional Blue Pottery Vase",
      "price": 2299
    }
  ]
}
```

---

### 3.4 Categories (`/categories`)

#### List Categories
- **Endpoint:** `GET /categories`
- **Response (200 OK):**
```json
[
  { "id": "cat-1", "name": "Royal Apparel", "slug": "Royal Apparel", "image": "https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=400&q=80" },
  { "id": "cat-2", "name": "Handicrafts", "slug": "Handicrafts", "image": "https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=400&q=80" },
  { "id": "cat-3", "name": "Silver Jewellery", "slug": "Silver Jewellery", "image": "https://images.unsplash.com/photo-1630019852942-f89202989a59?auto=format&fit=crop&w=400&q=80" },
  { "id": "cat-4", "name": "Marwari Mojari", "slug": "Marwari Mojari", "image": "https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=400&q=80" },
  { "id": "cat-5", "name": "Food & Spices", "slug": "Food & Spices", "image": "https://images.unsplash.com/photo-1587314168485-3236d6710814?auto=format&fit=crop&w=400&q=80" },
  { "id": "cat-6", "name": "Home & Décor", "slug": "Home & Décor", "image": "https://images.unsplash.com/photo-1583847268964-b28dc8f51f92?auto=format&fit=crop&w=400&q=80" },
  { "id": "cat-7", "name": "Art & Collectibles", "slug": "Art & Collectibles", "image": "https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?auto=format&fit=crop&w=400&q=80" }
]
```

---

### 3.5 Cart Management (`/cart`)

*(Headers: `Authorization: Bearer <token>`)*

#### 1. Fetch Cart
- **Endpoint:** `GET /cart`
- **Response (200 OK):**
```json
{
  "items": [
    {
      "product": {
        "id": "prod-20",
        "name": "Imperial Udaipur Heritage Silver Peacock Box",
        "price": 7899
      },
      "quantity": 1,
      "subtotal": 7899
    }
  ],
  "subtotal": 7899,
  "discount": 0,
  "shipping": 0,
  "total": 7899
}
```

#### 2. Add Item to Cart
- **Endpoint:** `POST /cart/items`
- **Request Body:**
```json
{
  "productId": "prod-20",
  "quantity": 1
}
```

#### 3. Update Item Quantity
- **Endpoint:** `PUT /cart/items/{id}`
- **Request Body:**
```json
{
  "quantity": 2
}
```

#### 4. Remove Item from Cart
- **Endpoint:** `DELETE /cart/items/{id}`

---

### 3.6 Orders & Checkout (`/orders`)

*(Headers: `Authorization: Bearer <token>`)*

#### 1. Place Order
- **Endpoint:** `POST /orders`
- **Request Body:**
```json
{
  "shippingAddress": {
    "name": "Ramesh Seervi",
    "phone": "9001122334",
    "street": "12 Heritage Lane",
    "city": "Jodhpur",
    "zip": "342001"
  },
  "paymentMethod": "razorpay",
  "couponCode": "MARWARI10"
}
```

**Response (201 Created):**
```json
{
  "id": "ORD-2026-8941",
  "status": "Processing",
  "total": 7109,
  "payment_method": "razorpay",
  "payment_status": "paid",
  "tracking_number": "MRW-IND-9921448",
  "date": "2026-10-06T15:30:00Z"
}
```

#### 2. Get Order History
- **Endpoint:** `GET /orders`

#### 3. Get Order Detail & Live Tracking
- **Endpoint:** `GET /orders/{id}`

---

### 3.7 Customer Profile (`/me`)

*(Headers: `Authorization: Bearer <token>`)*

#### 1. Get Profile
- **Endpoint:** `GET /me`

#### 2. Update Profile
- **Endpoint:** `PUT /me`
- **Request Body:**
```json
{
  "name": "Ramesh Seervi",
  "phone": "9001122334"
}
```

---

### 3.8 Media Upload (`/upload`)

- **Endpoint:** `POST /upload`
- **Header:** `Content-Type: multipart/form-data`
- **Body:** Form data field `file` containing binary image/document.
- **Response (200 OK):**
```json
{
  "success": true,
  "url": "https://rpsdigitalworld.store/wp-content/uploads/2026/10/peacock.jpg"
}
```

---

## 💻 4. React Native Integration (TypeScript / JavaScript)

### 4.1 Axios API Client (`src/services/api.ts`)

```typescript
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const API_BASE_URL = 'https://rpsdigitalworld.store/wp-json/wp-ecommerce/v1';

const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
  timeout: 10000,
});

// Automatic Bearer Token Interceptor
apiClient.interceptors.request.use(async (config) => {
  const token = await AsyncStorage.getItem('user_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export const AuthAPI = {
  login: (credentials: { email: string; password: string }) => 
    apiClient.post('/auth/login', credentials),
  register: (data: { name: string; email: string; phone?: string; password: string }) => 
    apiClient.post('/auth/register', data),
  sendOTP: (phone: string) => 
    apiClient.post('/auth/send-otp', { phone }),
  verifyOTP: (phone: string, otp: string) => 
    apiClient.post('/auth/verify-otp', { phone, otp }),
};

export const CatalogAPI = {
  getHomeFeed: () => apiClient.get('/home'),
  getProducts: (params?: { category?: string; search?: string; page?: number; limit?: number }) => 
    apiClient.get('/products', { params }),
  getProductDetail: (id: string | number) => 
    apiClient.get(`/products/${id}`),
  getCategories: () => 
    apiClient.get('/categories'),
};

export const CartAPI = {
  getCart: () => apiClient.get('/cart'),
  addItem: (productId: string, quantity: number = 1) => 
    apiClient.post('/cart/items', { productId, quantity }),
  updateQuantity: (itemId: string, quantity: number) => 
    apiClient.put(`/cart/items/${itemId}`, { quantity }),
  removeItem: (itemId: string) => 
    apiClient.delete(`/cart/items/${itemId}`),
};

export const OrderAPI = {
  placeOrder: (orderPayload: any) => 
    apiClient.post('/orders', orderPayload),
  getOrderHistory: () => 
    apiClient.get('/orders'),
  getOrderDetail: (orderId: string) => 
    apiClient.get(`/orders/${orderId}`),
};

export default apiClient;
```

---

### 4.2 TypeScript Types Definition (`src/types/ecommerce.ts`)

```typescript
export interface Product {
  id: string;
  name: string;
  category: string;
  price: number;
  original_price?: number;
  discount_percent?: number;
  description: string;
  image: string;
  badge?: string;
  rating?: number;
  reviews_count?: number;
  in_stock: boolean;
}

export interface Category {
  id: string;
  name: string;
  slug: string;
  image: string;
}

export interface Address {
  id?: string;
  name: string;
  phone: string;
  street: string;
  city: string;
  zip: string;
  default?: boolean;
}

export interface Order {
  id: string;
  customer_name: string;
  date: string;
  status: 'Processing' | 'Dispatched' | 'In Transit' | 'Delivered' | 'Cancelled';
  total: number;
  payment_method: string;
  payment_status: string;
  tracking_number: string;
}
```

---

## ⚡ 5. Verification & Testing

To test and execute endpoints interactively:
1. Open [`https://rpsdigitalworld.store/ecommerce/api-docs`](https://rpsdigitalworld.store/ecommerce/api-docs) in your browser.
2. Click **Authorize** to paste your Bearer JWT token if testing protected routes.
3. Click any endpoint &rarr; **Try it out** &rarr; **Execute** to see real server responses.
