
## ✅ Tested Working Installation Method

This README documents **only the method that is confirmed working**.

---

## 📂 Required Folder Structure

Place the package exactly here:

packages/
└── SleepyBear/
    └── Razorpay/

Folder names and case **must match exactly**.

---

## 🚀 Installation Steps

### 1️⃣ Upload Package Files

Upload the full package into:

packages/SleepyBear/Razorpay

You may upload using:
- cPanel File Manager
- SFTP
- Git clone

---

### 2️⃣ Require Package via Composer

From your Bagisto root directory:

composer require sleepybear/bagisto-razorpay:dev-main

This registers autoloading and providers.

---

### 3️⃣ Clear Cache

php artisan optimize:clear

---

### 4️⃣ Verify Installation

php artisan package:discover

No errors = success.

---

## ⚙️ Enable Razorpay in Admin

Admin Panel → Configure → Sales → Payment Methods → Razorpay

Enter:
- Razorpay Key ID
- Razorpay Key Secret

Enable & Save.

---

## 🛒 Checkout Flow

- Razorpay visible in checkout
- Popup opens
- Payment completes
- Order & invoice created
- Cart cleared
- Default Bagisto success page shown

---

## 🧾 Stored Payment Data

Database table:

order_payment

Column:

additional (JSON)

Example:

{
  "Payment Status": "Paid",
  "Payment Gateway": "Razorpay",
  "Razorpay Order ID": "order_xxxx",
  "Razorpay Payment ID": "pay_xxxx"
}


---

## 🧹 Uninstall

composer remove sleepybear/bagisto-razorpay
php artisan optimize:clear

---

## 📜 License

MIT
"""
