# 🚜 Uziuzi Agrovet System | Enterprise Documentation

Welcome to the official documentation for **Uziuzi Agrovet**, a premium, high-performance agricultural e-commerce platform specifically engineered for the Kenyan market.

---

## 🚀 1. Executive Summary
Uziuzi Agrovet is a full-stack, secure, and highly animated e-commerce solution that bridges the gap between agricultural suppliers and Kenyan farmers. The system provides a seamless shopping experience with real-time tracking for admins and integrated mobile financial services.

---

## 📦 2. Key Features

### 🛒 A. Prestige Farm Shop (Customer)
*   **Glassmorphic UI**: High-end floating "Prestige Hero" section with dynamic entrance animations.
*   **Animated Product Showcase**: Floating 3D icons of farm items (Maize, Fertilizers, Tools) in the header.
*   **Live Inventory Tracking**: Glowing green "Pulse" dots on every product indicating real-time availability.
*   **Magnetic Shopping Cart**: Persistent, pulsing "Add to Cart" buttons and a floating mini-cart banner.
*   **Smart Search**: Instant filtering of products across categories.
*   **WhatsApp "Farm Assistant"**: Floating chat icon for instant direct communication with the supplier.

### 💰 B. Fintech Integration (M-Pesa)
*   **M-Pesa STK Push**: Instant PIN prompt sent directly to the customer's phone upon checkout.
*   **Daraja API Engine**: Integrated OAuth 2.0 handshake with Safaricom servers.
*   **Diagnostic Monitor**: Real-time error reporting from Safaricom for failed or pending transactions.

### 📊 C. Business Intelligence Dashboard (Admin)
*   **Live Revenue Charts**: High-impact visualization of weekly sales performance using Chart.js.
*   **Sales Hub Ledger**: A live-updating table of every receipt and order processed.
*   **Low-Stock Guardian**: Automatic red "Warning" badges for any product with less than 10 units left.
*   **Activity Tracking**: Monitoring of logins and completions of the checkout process.

---

## ⚡ 3. Technical Blueprint

### 🏗️ Technology Stack
*   **Backend**: PHP 8.x with `mysqli` prepared statements for bank-grade security.
*   **Frontend**: Professional Vanilla CSS (Custom tokens) & Javascript (AJAX Fetch API).
*   **Database**: MySQL (Relational Schema).
*   **Integrations**: Safaricom Daraja API (STK Push), Chart.js (Data Vis).

### 📂 Core File Architecture
| File | Purpose |
| :--- | :--- |
| `index.php` | Admin Command Center & Sales Analytics |
| `view.php` | Premium Customer Shop & Showcase |
| `login.php` | Unified Portal (Farmer/Staff) |
| `checkout.php` | Transaction & Payment Gateway |
| `mpesa.php` | Safaricom STK Push Engine |
| `print_receipt.php` | Professional Thermal Receipt Generator |
| `connection.php` | Secure Database Bridge |

---

## 🛠️ 4. Installation & Setup

1.  **Server Environment**: Deploy using **XAMPP** or a standard LAMP stack.
2.  **Database Initialisation**:
    *   Create a database named `uziuzi-Agrovet`.
    *   Import the provided SQL schema from `database_master.sql`.
3.  **M-Pesa Activation**:
    *   Open `mpesa.php`.
    *   Insert your **Consumer Key** and **Consumer Secret** from the Safaricom Developer Portal.
4.  **Admin Access**: Default admin credentials can be set via `register.php` (Staff mode).

---

## 🛡️ 5. Security Protocols
*   **🔐 Data Integrity**: All user inputs are sanitized using **Prepared Statements** to eliminate SQL Injection risks.
*   **🔑 Password Safety**: User passwords are encrypted using `password_hash()` (Bcrypt), ensuring that even database administrators cannot see plain-text passwords.
*   **🚪 Session Control**: Secure session management prevents unauthorized access to the Admin Dashboard.

---

## 🚜 6. Operational Guide for Admin
*   **Maintaining Inventory**: Use the "Add Product" form in `index.php` to upload new items with images.
*   **Processing Sales**: Monitor the **Business Sales Hub** for new orders. Use the **Live Charts** to analyze which days are your peak sales periods.
*   **Inventory Restocking**: Check the "Low Stock" markers in the inventory table to know exactly what to re-order from manufacturers.

---

**© 2026 Uziuzi Agrovet System | Precision Agriculture. High-Impact Tech.**
