# 🏫 Advanced School Management System ERP

**Version:** 1.0  
**Release Date:** 2026-05-15  

---

## 📌 Overview

The **Advanced School Management System ERP** is a complete web-based solution for managing school operations including students, teachers, exams, attendance, fees, library, transport, and reporting. It is built using **Core PHP and MySQL** with a modern responsive UI.

---

## ⚙️ System Requirements

- XAMPP Server (Apache + MySQL)
- PHP 8.0 or higher
- MySQL 5.7+ / MariaDB 10.3+
- Modern Web Browser (Chrome, Firefox, Edge)

---

## 🚀 Installation Guide

### Step 1: Install XAMPP
- Download XAMPP: https://www.apachefriends.org
- Start Apache and MySQL from XAMPP Control Panel

---

### Step 2: Setup Project
- Copy project folder: school-management-system
- Paste into:

Windows:
C:\xampp\htdocs\

Linux:
/opt/lampp/htdocs/

Final path:
C:\xampp\htdocs\school-management-system\

---

### Step 3: Create Database
- Open: http://localhost/phpmyadmin
- Create new database:
school_management

---

### Step 4: Import Database
- Select database school_management
- Go to Import
- Choose file:
database/school_management.sql
- Click Go

---

### Step 5: Run Application
Open browser:
http://localhost/school-management-system/

---

## 🔐 Sample Login Credentials

Role | Email | Password
----|------|---------
Admin | admin@school.com | admin123
Teacher | teacher@school.com | teacher123
Student | student@school.com | student123
Parent | parent@school.com | parent123

Note: All passwords are hashed using password_hash() and default login uses admin123.

---

## 📁 Project Structure

school-management-system/
- assets/
- config/
- includes/
- admin/
- teacher/
- student/
- parent/
- accountant/
- modules/
- ajax/
- database/
- login.php
- logout.php
- dashboard.php
- index.php
- unauthorized.php
- README.md

---

## ✨ Key Features

- Authentication & Role Management
- Student Management System
- Teacher Management System
- Class & Subject Management
- Attendance System
- Examination System
- Fee Management System
- Notice Board
- Reports & Dashboard Analytics

---

## 🧰 Technology Stack

Frontend:
- HTML5, CSS3, Bootstrap 5
- JavaScript, jQuery, AJAX

Backend:
- Core PHP
- MySQL / MariaDB

Libraries:
- Chart.js
- DataTables
- SweetAlert2
- Font Awesome
- Select2
- Flatpickr

---

## 🔒 Security Features

- SQL Injection Protection
- XSS Protection
- CSRF Protection
- Password Hashing
- Session Management
- Role-based Access Control

---

## 📞 Contact

Email: Rashidshangla@gmail.com  
Website: https://www.pashtomedium.com/

---

## 📄 License

Educational and commercial use allowed. All rights reserved.
