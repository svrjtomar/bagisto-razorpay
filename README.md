# Bagisto Razorpay Payment Gateway

Razorpay payment gateway integration for Bagisto.  
This package adds Razorpay as a prepaid payment method without modifying Bagisto core files.

---

## Features

- Razorpay Checkout integration
- Secure payment signature verification
- Cart → Order mapping using Razorpay receipt
- Payment details saved in Bagisto order payment
- Invoice generation after successful payment
- Compatible with Bagisto default success page
- No core file overrides

---

## Requirements

- PHP 8.1+
- Bagisto 2.x
- Razorpay account (Test or Live)

---

## Installation

Install the package via Composer:


composer require sleepybear/bagisto-razorpay:dev-main


### Step 2: Publish Assets


php artisan vendor:publish --tag=razorpay-assets
```

### Step 3: Clear Cache


php artisan config:cache
php artisan route:cache
php artisan optimize:clear
```

---

## ⚙️ Configuration

### 1. Get PhonePe Credentials

1. Login to [PhonePe Business Dashboard](https://business.razorpay.com/)
2. Navigate to **Developer Settings**
3. Copy your **Client ID** and **Client Secret**

### 2. Configure in Bagisto Admin

1. Go to **Admin Panel → Configuration → Sales → Payment Methods**
2. Find **PhonePe** in the payment methods list

---
