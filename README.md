# 🏢 Tenant Management System (Rent + Water Billing)

A comprehensive web-based rental management system designed to help landlords efficiently manage tenants, rent payments, and utility billing (water), all in one place.

---

## 🚀 Overview

This system simplifies rental property management by combining:

* Tenant management
* Rent tracking
* Meter-based water billing
* Payment recording (rent + water combined)
* Automated balance calculations
* Dashboard reporting

It is built to reflect real-world landlord workflows, especially where utilities like water are billed per unit.

---

## ✨ Key Features

### 👥 Tenant Management

* Add, edit, and delete tenants
* Assign tenants to houses
* Track tenant details and move-in dates

---

### 🏠 Property Management

* Manage rental units (houses)
* Set monthly rental rates
* Ensure one active tenant per house

---

### 💧 Water Billing System

* Input previous and current meter readings
* Automatically calculate:

  * Units consumed
  * Water cost per tenant
* Auto-carry forward readings (current → next previous)
* Monthly water billing (no accumulation errors)

---

### 💰 Payment System

* Record payments per tenant
* System automatically:

  * Applies payment to **water first**
  * Then applies remaining to **rent**
* Prevents incorrect balance calculations

---

### 📊 Dashboard

* Total houses
* Total tenants
* Payments this month
* 💧 Water units consumed
* 💰 Total monies collected (all time)

---

### 📄 Reports

* Tenant balances
* Payment history
* Outstanding amounts (rent + water)

---

## 🧠 System Logic Highlights

* **Water billing is monthly-based** (not cumulative)
* **Latest water reading = current bill**
* **Payments prioritize water before rent**
* **Outstanding = Rent + Water – Payments**
* **Automatic meter chaining** ensures accurate readings

---

## 🛠️ Tech Stack

* **Backend:** PHP (Procedural + OOP mix)
* **Database:** MySQL
* **Frontend:** HTML, CSS, Bootstrap
* **JavaScript:** jQuery, AJAX
* **UI Components:** DataTables, Font Awesome

---



## 🔐 Default Access

*(Update this if you have login credentials)*

* Username:
* Password: 
`Contact developer for credentials`

---

## 📌 Usage Flow

1. Add houses
2. Add tenants and assign houses
3. Record monthly water readings
4. Record payments
5. Monitor dashboard and reports

---

## 💼 Business Value

This system helps landlords:

* Track rent and water accurately
* Reduce financial leakage
* Eliminate manual calculations
* Improve tenant accountability
* Operate professionally

---

## 🚀 Future Improvements

* SMS payment notifications
* M-Pesa integration
* Multi-property support
* Mobile-friendly dashboard
* Role-based user access

---

## 👨‍💻 Author

Developed at **Melbur Studios**
🌐 https://melbur.co.ke

---

## 📄 License

This project is proprietary software.
For licensing or commercial use, contact the developer.

---

## ⭐ Support

If you find this project useful:

* Star the repository
* Share with others
* Reach out for customization or deployment

---
