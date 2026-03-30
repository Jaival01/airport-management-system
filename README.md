# ✈️ Airport Management System

A **PHP + MySQL web application** designed to manage airport operations including flight bookings, baggage tracking, and administrative workflows.
This system provides separate dashboards for **users and administrators** to efficiently manage flights, bookings, staff, and passenger information.

---

## 📌 Overview

The **Airport Management System** helps simulate real-world airport operations through a centralized web platform. Users can search flights, book tickets, generate boarding passes, and track baggage, while administrators can manage flights, staff, gates, and users through an admin dashboard.

This project demonstrates practical implementation of **web development, database management, and role-based system design**.

---

## 🚀 Features

### 👤 User Features

* User registration and login
* Flight search and booking
* Ticket generation
* Boarding pass generation
* Baggage tracking
* User dashboard
* Profile management

### 🛠 Admin Features

* Admin dashboard
* Manage flights
* Manage users
* Manage staff
* Manage gates
* View and manage bookings

---

## 🧰 Technology Stack

**Frontend**

* HTML
* CSS
* JavaScript

**Backend**

* PHP

**Database**

* MySQL / MariaDB

**Server Environment**

* Apache Server (WAMP / XAMPP / LAMP)

---

## 📂 Project Structure

```
Airport Management Module/
├── config.php
├── database.sql
├── index.php
├── login.php
├── logout.php
├── register.php
├── admin/
│   ├── bookings.php
│   ├── dashboard.php
│   ├── flights.php
│   ├── gates.php
│   ├── staff.php
│   └── users.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── app.js
└── user/
    ├── baggage.php
    ├── boarding-pass.php
    ├── bookings.php
    ├── dashboard.php
    ├── flights.php
    ├── profile.php
    └── ticket.php
```

---

## ⚙️ Requirements

To run this project locally you need:

* **PHP 7.4+ / PHP 8.x**
* **MySQL or MariaDB**
* **Apache Server**
* **WAMP / XAMPP / LAMP**
* Any modern browser (Chrome, Edge, Firefox)

---

## 🖥 Setup Instructions (Using WAMP)

### 1️⃣ Place Project Folder

Move the project folder into:

```
C:\wamp64\www\Airport Management Module
```

---

### 2️⃣ Start Services

Open **WAMP Server** and start:

* Apache
* MySQL

---

### 3️⃣ Import Database

1. Open **phpMyAdmin**
2. Create a new database:

```
airport_management
```

3. Import the file:

```
database.sql
```

---

### 4️⃣ Configure Database Connection

Open:

```
config.php
```

Verify the database settings:

```
DB_HOST = localhost
DB_USER = root
DB_PASS = ''
DB_NAME = airport_management
```

---

### 5️⃣ Run the Application

Open your browser and go to:

```
http://localhost/Airpot%20Management%20Module
```

*(Note: URL spelling follows the project folder configuration.)*

---

## 👨‍💻 Default Demo Accounts

⚠️ Passwords are stored in **plain text for demo purposes only**.

### Admin Account

```
Username: admin
Password: admin123
```

### User Account

```
Username: jaival
Password: jaival01
```

---

## 🌐 Main Routes

### Public Pages

```
/index.php
/login.php
/register.php
```

### User Panel

```
/user/dashboard.php
/user/flights.php
/user/bookings.php
```

### Admin Panel

```
/admin/dashboard.php
/admin/flights.php
/admin/users.php
```

---

## 📝 Notes

* Default timezone is set to **Asia/Kolkata** in `config.php`.
* `SITE_URL` currently uses:

```
Airpot%20Management%20Module
```

(The spelling matches the project configuration.)

---


## 🎓 Purpose

This project was developed for **educational and demonstration purposes** to showcase concepts of:

* Web application development
* Database integration
* Role-based access control
* Airport system workflow simulation

---

## 📄 License

This project is intended for **learning and demonstration purposes only**.

