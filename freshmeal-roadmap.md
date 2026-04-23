# 🍽 MealBox Project — Progress & Roadmap

## 📌 Project Overview

MealBox is a full-stack PHP + MySQL web application for meal subscription and ordering.

---

# ✅ COMPLETED FEATURES (So Far)

## 🧱 1. Project Setup

- XAMPP installed (Apache + MySQL)
- Project inside `htdocs/mealbox`
- Basic PHP app running via `public/index.php`

---

## 🏗 2. MVC Structure

- Created:
    - `app/Controllers`
    - `app/Views`

- Implemented:
    - `HomeController`
    - `MenuController`
    - `LoginController`
    - `OrderController`

---

## 🔀 3. Routing System

- Built custom router in `index.php`
- Supports routes:
    - `/` (home)
    - `/menu`
    - `/login`
    - `/dashboard`
    - `/order`
    - `/logout`

---

## 🎨 4. UI Setup

- Using Tailwind CSS via CDN
- Built:
    - Landing page (basic)
    - Navbar
    - Hero section
    - Login page UI
    - Menu UI (cards)
    - Dashboard UI

---

## 🔐 5. Authentication System

- Login form
- Password hashing (`password_hash`)
- Password verification (`password_verify`)
- Session-based authentication
- Protected routes (dashboard)
- Logout system

---

## 🗄 6. Database Setup

### Tables:

- `users`
- `meals`
- `orders`

### Features:

- Insert test user
- Store hashed passwords
- Store meals data
- Store user orders

---

## 🍱 7. Menu System (Dynamic)

- Fetch meals from database
- Display meals in UI cards
- Show:
    - Name
    - Description
    - Price

---

## 🛒 8. Order System

- “Order” button per meal
- Stores:
    - user_email
    - meal_id

- Redirect to dashboard after order

---

## 📊 9. Dashboard

- Protected page
- Shows logged-in user
- Displays:
    - User orders (JOIN query)
    - Meal name
    - Price
    - Order date

---

# 🧠 CURRENT ARCHITECTURE

- PHP (Custom MVC)
- MySQL (via PDO)
- Tailwind CSS (CDN)
- Session-based auth

---

# 🚧 NEXT FEATURES TO BUILD

## 🔥 Phase 1 — Order Management

- [ ] Cancel/Delete order
- [ ] Update order (optional)
- [ ] Add order confirmation UI

---

## 💳 Phase 2 — Subscription System

- [ ] Create subscription plans table
- [ ] Weekly / Monthly plans
- [ ] Link users to subscriptions
- [ ] Auto-generate orders (advanced)

---

## 👤 Phase 3 — User System Upgrade

- [ ] Registration page
- [ ] Password validation
- [ ] User profile page
- [ ] Edit profile

---

## 🎨 Phase 4 — UI Improvement

- [ ] Better dashboard layout (cards, stats)
- [ ] Sidebar navigation
- [ ] Responsive design improvements
- [ ] Loading states / alerts

---

## 🏠 Phase 5 — Public Landing Page

- [ ] Full landing page design
- [ ] Pricing section
- [ ] Features section
- [ ] CTA buttons
- [ ] SEO-friendly structure

---

## 🧾 Phase 6 — Orders & History

- [ ] Order history page
- [ ] Filter orders
- [ ] Order status (pending, completed)

---

## 🔐 Phase 7 — Security Improvements

- [ ] CSRF protection
- [ ] Input validation
- [ ] Prepared statements everywhere
- [ ] Session security improvements

---

## ⚙️ Phase 8 — Backend Refactor

- [ ] Create base Controller class
- [ ] Create Router class (cleaner routing)
- [ ] Environment config (.env)
- [ ] Move DB logic to Models

---

## 🚀 Phase 9 — Production Setup

- [ ] Replace Tailwind CDN with build system
- [ ] Optimize CSS (purge unused)
- [ ] Error handling pages
- [ ] Deployment (hosting/server)

---

# 🎯 FINAL GOAL

A complete SaaS-like app with:

- Authentication
- Meal browsing
- Ordering system
- Subscription plans
- Clean UI
- Secure backend

---

# 📝 HOW TO USE THIS FILE

When you come back:

👉 Just paste this file and say:

> “continue from roadmap”

I’ll instantly understand:

- what’s done ✅
- what’s next 🚀

---

# 💡 NOTE

Project is already at:
👉 **Intermediate full-stack level**

Next steps will turn it into:
👉 **Production-ready SaaS app**
