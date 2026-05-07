# Laravel Store — Single-Vendor E-Commerce Platform

A full-featured, production-ready single-vendor e-commerce platform built with **Laravel 12**. Supports Arabic (RTL) and English out of the box, with a complete admin panel, REST API, guest & authenticated shopping cart, and PDF invoice generation.

---

## Features at a Glance

| Area | What's included |
|---|---|
| Storefront | Home, product listing & detail, category pages, search, cart, checkout, order success |
| Customer Account | Dashboard, orders, profile, addresses, wishlist |
| Admin Panel | Full CRUD for every entity + 4 report screens |
| CMS | Pages, FAQs, Banners (hero / sidebar / popup / section) |
| Orders | Status & payment-status tracking, admin notes, PDF invoices |
| Marketing | Coupons (%, fixed, free-shipping), newsletter, product reviews |
| REST API | Auth, catalog, cart, checkout, orders (Sanctum) |
| Bilingual | Arabic (RTL default) + English, switchable per request |

---

## Table of Contents

1. [Tech Stack](#tech-stack)
2. [Requirements](#requirements)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [Database Seeding](#database-seeding)
6. [Running the App](#running-the-app)
7. [Admin Panel](#admin-panel)
8. [Storefront](#storefront)
9. [REST API](#rest-api)
10. [Project Structure](#project-structure)
11. [License](#license)

---

## Tech Stack

- **Framework**: Laravel 12 (PHP 8.2+)
- **Database**: MySQL 8
- **Auth**: Laravel Session (web) + Laravel Sanctum (API)
- **PDF**: barryvdh/laravel-dompdf
- **Slugs**: spatie/laravel-sluggable
- **Testing**: PestPHP
- **Assets**: Vite
- **Templating**: Blade (RTL/LTR aware)

---

## Requirements

- PHP 8.2+
- Composer
- MySQL 8+
- Node.js 18+ & npm

---

## Installation

```bash
# 1. Clone the repository
git clone https://github.com/YOUR_USERNAME/store.git
cd store

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies and build assets
npm install && npm run build

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Run migrations
php artisan migrate

# 7. Seed with demo data
php artisan db:seed

# 8. Create storage symlink
php artisan storage:link
```

---

## Configuration

Edit `.env` for your environment:

```env
APP_NAME="متجري"
APP_URL=http://localhost:8000
APP_LOCALE=ar

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=store
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="noreply@yourstore.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Product images, logos, and banners are stored under `storage/app/public` and served via the storage symlink.

---

## Database Seeding

The default seeder creates:

- **Admin user** — `admin@store.com` / `password`
- **Store settings** — Arabic store name, SAR currency, 15% tax, 20 SAR flat shipping (free above 200 SAR)
- **6 product categories** — Electronics, Clothing, Home & Kitchen, Beauty, Sports, Books

```bash
php artisan db:seed
```

---

## Running the App

```bash
php artisan serve --port=8000
```

| URL | Description |
|---|---|
| `http://localhost:8000` | Storefront |
| `http://localhost:8000/admin` | Admin panel |

---

## Admin Panel

Sign in at `/admin` with the seeded admin credentials.

### Dashboard

- Total sales, orders, customers, and products
- Sales chart (daily / weekly / monthly toggle)
- Recent orders list
- Low-stock alerts

### Product Management

- **Products** — Create/edit products with multiple images, description (AR/EN), SEO fields (meta title, description), status toggle, bulk actions
- **Variants** — Per-product variants with SKU, regular price, compare-at price, stock quantity, weight, and attribute values
- **Categories** — Hierarchical parent/child categories, icon, Arabic + English names, auto-generated slug
- **Brands** — Logo, country, active/inactive toggle
- **Attributes** — Types: `select`, `color`, `size`, `text`; values with Arabic name, English name, and color code

### Order Management

- Full order detail — line items, variant info, shipping address, coupon discount
- Update order status: `pending → processing → shipped → delivered → cancelled`
- Update payment status: `pending → paid → refunded`
- Add admin notes
- Print / download PDF invoice

### Customer Management

- Customer list with order count and total spend
- Customer detail page with full order history
- Toggle account active / inactive

### Marketing

| Feature | Details |
|---|---|
| Coupons | Percentage, fixed-amount, or free-shipping discounts; minimum order amount, per-user and global usage limits, expiry date |
| Banners | 4 placements: `hero`, `sidebar`, `popup`, `section`; start/end dates; Arabic + English title and button text |
| Reviews | Customer product reviews with star rating, visible on product detail page |

### CMS

- **Pages** — Rich-text content pages (Arabic + English), custom slug, `is_published` toggle
- **FAQs** — Grouped question/answer pairs, inline add / edit / delete
- **Newsletter** — Subscriber list with CSV export
- **Contact Messages** — Inbox for contact-form submissions with inline expand

### Reports

| Report | Metrics |
|---|---|
| Sales | Revenue chart, total orders, average order value, top products, sales by period |
| Products | Total products, low-stock count, out-of-stock count, top-selling products, breakdown by category |
| Customers | Total customers, new vs returning, top spenders, customer growth chart |
| Inventory | All product variants with live stock levels, filter by category |

### Store Settings

All settings are stored in a single `store_settings` row and cached for one hour.

- Store name (AR/EN), logo, favicon, cover image
- Theme: primary / secondary / accent color, light/dark/system mode, font family
- Contact info (phone, email, address AR/EN), social link URLs, meta tags, footer text
- Hero section title and subtitle (AR/EN)
- Currency, currency symbol, shipping cost, free-shipping threshold, tax rate, minimum order amount
- Feature toggles: reviews, wishlist, newsletter, maintenance mode
- Default locale (`ar` / `en`)

### Activity Logs

Every admin create, update, and delete action is recorded with the acting user, action type, model type/ID, and timestamp.

---

## Storefront

### Public Pages

| Route | Description |
|---|---|
| `/` | Home — hero banner, featured categories, featured products |
| `/products` | Product listing with pagination |
| `/products/{slug}` | Product detail — image gallery, variant selector, reviews, add-to-cart |
| `/category/{slug}` | Category page with sub-category filter chips |
| `/search` | Full-text search results |
| `/page/{slug}` | CMS static pages |
| `/faq` | FAQ accordion |
| `/contact` | Contact form |

### Shopping Cart

- Works for **guests** (session-based) and **logged-in users** (database-backed)
- Mini-cart drawer updated via AJAX
- Quantity increment/decrement, item removal, clear all
- Coupon code field with live discount and total recalculation

### Checkout (requires login)

- Saved-address selection or new address entry
- Order summary with itemized coupon discount and shipping cost
- Order confirmation redirects to success page with order number

### Customer Account (`/account`)

| Page | Features |
|---|---|
| Dashboard | Quick stats — open orders, wishlist count |
| Orders | Paginated order list with status badges + order detail view |
| Profile | Edit name, email, phone; change password |
| Addresses | Multiple shipping addresses, set default |
| Wishlist | Add/remove products, quick add-to-cart |

### Language Switcher

`GET /lang/{locale}` — switches between `ar` (Arabic, RTL) and `en` (English, LTR), persisted in the session.

---

## REST API

Base URL: `/api/v1`  
Authentication: **Laravel Sanctum** bearer tokens.

### Auth

```
POST /api/v1/auth/register
POST /api/v1/auth/login
POST /api/v1/auth/forgot-password
POST /api/v1/auth/reset-password
POST /api/v1/auth/logout           (Bearer token required)
```

### Catalog

```
GET  /api/v1/products              Paginated product list
GET  /api/v1/products/{slug}       Single product with variants
```

### Cart (Bearer token required)

```
GET    /api/v1/cart
POST   /api/v1/cart/sync           Add or update a cart item
DELETE /api/v1/cart/items/{id}     Remove an item
DELETE /api/v1/cart                Clear the entire cart
```

### Checkout (Bearer token required)

```
POST /api/v1/checkout              Place an order
```

### Orders (Bearer token required)

```
GET    /api/v1/orders
GET    /api/v1/orders/{id}
PATCH  /api/v1/orders/{id}/cancel
```

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Web/Admin/          Admin panel controllers
│   │   ├── Web/Store/          Storefront controllers
│   │   ├── Web/AuthController.php
│   │   └── API/V1/             REST API controllers
│   └── Requests/Admin/         Validated form requests
├── Models/                     Eloquent models
└── Services/
    ├── CartService.php          Guest + auth cart logic
    ├── OrderService.php
    ├── ReportService.php
    └── SettingsService.php

database/
├── migrations/                 30+ chronological migrations
└── seeders/

resources/views/
├── layouts/                    admin, store, auth Blade layouts
├── admin/                      All admin Blade views
├── store/                      Storefront Blade views
└── auth/                       Login, register, password-reset

routes/
├── web.php                     Web routes
└── api.php                     API routes
```

### Core Models

| Model | Notes |
|---|---|
| `User` | Roles: `admin`, `customer` |
| `Product` | JSON image array, SEO fields, slug |
| `ProductVariant` | SKU, price, compare-at price, stock, weight |
| `Attribute` / `AttributeValue` | Configurable product attributes with color support |
| `Cart` / `CartItem` | Guest (`session_id`) + auth (`user_id`) carts |
| `Order` / `OrderItem` | Full order with status history table |
| `Coupon` | Percentage, fixed, free-shipping with per-user usage tracking |
| `StoreSetting` | Single-row config table, 1-hour cache |
| `Banner` | 4 positions, bilingual content, date range |
| `Page` / `Faq` | CMS content with `is_published` flag |
| `NewsletterSubscriber` | Email + subscription date |
| `ContactMessage` | Contact form submissions |
| `ActivityLog` | Admin action audit trail |

---

## License

This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
