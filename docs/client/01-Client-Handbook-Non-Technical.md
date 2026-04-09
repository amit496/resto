# Restaurant Ordering & Admin System — Client Handbook (Non-Technical)

**Product:** Multi-branch restaurant storefront + admin (this codebase)  
**Version:** 1.1  
**For:** Business owners and managers (no technical background required)  
**Seller / Provider:** [YOUR NAME OR COMPANY]  
**Date:** [DATE]

---

## 1. What this document is

This handbook explains **what happens when you get the system for the first time**, **what support you can expect**, and **how extra work or changes are charged**. It is written in **simple language** without technical jargon, and it reflects **what this project actually includes** (see Section 2).

---

## 2. What you are getting — aligned with this product

You are getting a **web-based system** with two sides: **customers** (public website) and **your team** (admin panel). Below is what **this codebase** is built for. Your **demo** and **quotation** remain the final word if anything differs.

### A) Customer-facing website (public)

| Area | What customers can typically do |
|------|----------------------------------|
| **Home** | Marketing-style home with content that can be managed from admin settings (e.g. sliders, banners, promotional blocks). |
| **Menu** | Browse categories and subcategories, search/filter, open product details, add to cart. |
| **Cart & checkout** | Shopping cart, coupons, checkout flow, online payments (depending on gateway configuration). |
| **Branches** | See branch list and branch detail pages (addresses, manager info where configured). |
| **Offers** | View promotional offers / coupons as configured in admin. |
| **About & contact** | Information pages; contact form submissions can be stored for your team. |
| **Reviews** | Read published reviews; logged-in customers may submit reviews where the workflow allows. |
| **Reservations** | Request table/service bookings from the storefront (subject to your configuration). |
| **Customer account** | Register/login, profile, saved addresses, order history, loyalty (where enabled). |
| **Installable web app (PWA)** | “Add to home screen” style behaviour on supported phones/browsers — not a separate App Store / Play Store app. |

### B) Admin panel (`/admin` — staff users)

Your team can manage operations from a browser, **subject to each user’s permissions** (not every user sees everything):

- **Restaurant & catalog:** restaurants (where applicable), branches, categories, subcategories, products.  
- **Sales:** orders, customers, coupons, delivery staff, payments, refunds.  
- **Operations:** kitchen order view, inventory, reservations (admin side), delivery tracking, suppliers, purchases, expenses, notifications, loyalty (admin).  
- **Engagement:** reports (including export), moderation of food/branch reviews.  
- **Communication:** admin email tool (where configured).  
- **Settings & people:** app/site settings, staff users, audit logs, user management (permission-based).

Exactly **which menus and features** you enable in production will match your **agreement** and how we **configure roles** for your staff.

---

## 3. First time — what happens after you buy

Typical steps (exact order may vary as per your package):

1. **Kick-off** — We confirm your business name, branches (if any), payment providers you will use, and what must go live first.  
2. **Access** — You receive **website URL**, **admin URL**, and **login** details (or we create the first admin user with your approval).  
3. **Go-live** — The system runs on **your hosting** (or hosting we provision for you, if purchased).  
4. **Walkthrough** — Short training (video call or recorded video) for **daily admin tasks** (menu, orders, branches).  
5. **Handover** — We treat the agreed scope as **delivered** for that milestone.

**Important:** The build that is **live on delivery day** is your **delivered version**. It does **not automatically receive** every future improvement we add to our own copy of the product unless we agree **separately** (Section 6).

---

## 4. Support — how much and what kind

Support must match your **signed quote**. Example template (replace with your real numbers):

| Topic | Typical agreement (example — fill in yours) |
|-------|-----------------------------------------------|
| **Support period** | e.g. **[30 / 45 / 60] days** after go-live |
| **Channel** | e.g. **WhatsApp / email:** [YOUR CONTACT] |
| **Response time** | e.g. **Within 24–48 business hours** (24×7 only if explicitly purchased) |
| **What support covers** | How to use admin features we delivered, configuration we agreed to handle, **bugs** in agreed functionality |
| **What support does not cover** | New features, full redesigns, bank/gateway KYC, hosting outages, data loss without backups, misuse by staff |

**Bugs vs changes**

- **Bug** — Something in the **agreed scope** worked at handover and **breaks** without you asking for a change.  
- **Change / new work** — New screens, new rules, new integrations, new payment provider, new reports. **Quoted separately.**

---

## 5. Changes and new work — how charges work

| Type | Description | How we usually charge |
|------|-------------|------------------------|
| **Small tweak** | Text, small setting, minor adjustment | Small fixed fee or part of AMC |
| **Medium change** | New section, moderate admin rule, extra field | Quote after discussion |
| **Large change** | Major module, heavy integration | Written scope + milestones |

**Process:** describe need → we estimate **time + price** → **you approve** → we start → **no surprise work** without approval.

**Future updates:** Improvements we make to **our internal** codebase later are **not auto-installed** on your server. Optional **paid upgrade** or **maintenance plan** can cover merges and releases.

---

## 6. What is usually not included (unless your quote says so)

- Domain, hosting bills, SSL (sometimes we set up; you still own renewal).  
- **Payment gateways:** Stripe / Razorpay / PayPal **merchant accounts**, API keys, webhooks on the gateway dashboard — you or your CA/compliance team complete KYC.  
- Legal/tax/FSSAI advice; compliance is **your** responsibility.  
- **Native** iOS/Android store apps — this product is **web + PWA**, not a published Play Store / App Store binary unless separately contracted.  
- Full menu photography and data entry (can be a paid add-on).  
- Marketing, SEO, ads.

---

## 7. Payments and milestones (example)

| Milestone | % or amount | When |
|-----------|-------------|------|
| Project start | [___%] | On agreement |
| After UAT / demo sign-off | [___%] | Before or at go-live |
| Final | [___%] | After handover |

---

## 8. Single point of contact

Nominate **one** owner/manager for day-to-day requests to avoid duplicate tickets.

---

## 9. Acceptance

By paying or signing off, you confirm you read this handbook and that **your quotation overrides** these notes if they conflict.

**Provider signature / stamp:** _________________________ **Date:** _________  

**Client name / business:** _________________________ **Signature:** _________ **Date:** _________  

---

*PDF: paste into Google Docs / Word → Print → Save as PDF.*
