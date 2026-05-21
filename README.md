================================================================================
  ADVANCED SCHOOL MANAGEMENT SYSTEM ERP
  Version: 1.0
  Date: 2026-05-15
================================================================================

============================================================
  SYSTEM REQUIREMENTS
============================================================
1. XAMPP Server (Apache + MySQL)
2. PHP 8.0 or higher
3. MySQL 5.7 or higher / MariaDB 10.3+
4. Web Browser (Chrome, Firefox, Edge)

============================================================
  INSTALLATION INSTRUCTIONS
============================================================

Step 1: Install XAMPP
---------------------
- Download and install XAMPP from https://www.apachefriends.org
- Start Apache and MySQL services from XAMPP Control Panel

Step 2: Setup Project
---------------------
- Copy the entire "school-management-system" folder
- Paste it to C:\xampp\htdocs\ (Windows) or /opt/lampp/htdocs/ (Linux)
- Final path should be: C:\xampp\htdocs\school-management-system\

Step 3: Create Database
-----------------------
- Open your browser and go to http://localhost/phpmyadmin
- Click "New" to create a new database
- Database name: school_management
- Click "Create"

Step 4: Import SQL File
-----------------------
- Select the "school_management" database
- Click "Import" tab
- Click "Choose File" and select:
  school-management-system\database\school_management.sql
- Click "Go" to import

Step 5: Access the Application
------------------------------
- Open browser and navigate to:
  http://localhost/school-management-system/
- Login with any of the credentials below

============================================================
  SAMPLE LOGIN CREDENTIALS
============================================================

Role      | Email                | Password
----------|----------------------|----------
Admin     | admin@school.com     | admin123
Teacher   | teacher@school.com   | teacher123
Student   | student@school.com   | student123
Parent    | parent@school.com    | parent123

Note: All passwords use the default hash (all passwords are admin123
as they are hashed using password_hash() in the database. 
You can login with admin123 for any account.)

============================================================
  PROJECT STRUCTURE
============================================================

/school-management-system/
|
|-- /assets/          CSS, JS, Images, Uploads
|-- /config/          Database configuration
|-- /includes/        Header, Footer, Sidebar, Navbar, Auth, Functions
|-- /admin/           Admin panel pages
|-- /teacher/         Teacher panel pages
|-- /student/         Student panel pages
|-- /parent/          Parent panel pages
|-- /accountant/      Accountant panel pages
|-- /modules/         All feature modules
|   |-- /dashboard/   Dashboard views
|   |-- /students/    Student management
|   |-- /teachers/    Teacher management
|   |-- /classes/     Class management
|   |-- /subjects/    Subject management
|   |-- /attendance/  Attendance system
|   |-- /exams/       Examination system
|   |-- /fees/        Fee management
|   |-- /library/     Library management
|   |-- /transport/   Transport management
|   |-- /notices/     Notices & announcements
|   |-- /settings/    System settings
|   |-- /reports/     Reports generation
|   |-- /logs/        Activity logs
|-- /ajax/            AJAX helper files
|-- /database/        SQL database file
|
|-- login.php         Login page
|-- logout.php        Logout script
|-- dashboard.php     Main dashboard
|-- index.php         Entry point
|-- unauthorized.php  Access denied page
|-- README.txt        This file

============================================================
  FEATURES
============================================================

1. AUTHENTICATION & SECURITY
   - Secure login with password hashing
   - CSRF protection
   - Session management with timeout
   - Role-based access control (5 roles)
   - Remember me functionality
   - Activity logging

2. STUDENT MANAGEMENT
   - Add/Edit/Delete/View students
   - Photo upload
   - Class & section assignment
   - Parent/guardian details
   - Medical notes
   - Document management

3. TEACHER MANAGEMENT
   - Add/Edit/Delete/View teachers
   - Qualification & experience
   - Salary information
   - Subject assignment
   - Photo upload

4. CLASS & SECTION MANAGEMENT
   - Create classes with capacity
   - Create sections
   - Assign class teachers

5. SUBJECT MANAGEMENT
   - Add/Edit/Delete subjects
   - Subject codes
   - Assign to classes
   - Assign teachers
   - Set full/pass marks

6. ATTENDANCE SYSTEM
   - Daily attendance marking
   - Present/Absent/Late/Half Day/On Leave
   - Monthly reports
   - Attendance percentage calculation

7. EXAMINATION SYSTEM
   - Customizable exam types
   - Custom weightage percentages
   - Semester/Annual exams
   - Multiple exam types support
   - Marks entry
   - Result calculation
   - Publish/Unpublish results
   - Report cards with print/PDF

8. FEE MANAGEMENT
   - Fee structure setup
   - Fee collection with receipt
   - Payment history
   - Pending fees tracking
   - Multiple payment methods

9. NOTICE BOARD
   - Post notices & announcements
   - Role-based display
   - Pinned notices
   - Notice types

10. SETTINGS
    - School information
    - Academic session
    - Theme customization
    - Currency settings

11. REPORTS
    - Student reports
    - Attendance reports
    - Exam results
    - Fee reports
    - Print & Export

12. DASHBOARD
    - Statistics cards
    - Charts (Revenue, Gender)
    - Recent activities
    - Upcoming exams
    - Quick action buttons
    - Notifications

============================================================
  TECHNOLOGY STACK
============================================================

Frontend:
  - HTML5
  - Bootstrap 5
  - CSS3 (Custom styling)
  - JavaScript
  - jQuery 3.7
  - AJAX

Backend:
  - Core PHP (No framework)
  - MySQL/MariaDB

Libraries & Plugins:
  - Chart.js (Charts)
  - DataTables (Tables)
  - SweetAlert2 (Notifications)
  - Font Awesome 6 (Icons)
  - Select2 (Dropdowns)
  - Flatpickr (Date Picker)

============================================================
  DATABASE TABLES
============================================================

- school_info         School configuration
- users               User accounts (5 roles)
- classes             Classes
- sections            Sections
- subjects            Subjects
- teachers            Teacher records
- parents             Parent records
- students            Student records
- student_documents   Student documents
- attendance          Student attendance
- teacher_attendance  Teacher attendance
- exam_types          Exam types (customizable)
- exams               Exams
- exam_subjects       Exam subject assignments
- student_marks       Student marks
- grading_system      Grade definitions
- results             Exam results
- fee_types           Fee categories
- fee_structure       Fee structure
- student_fees        Student fee records
- fee_payments        Payment records
- library_books       Library books
- book_issues         Book issue/return
- transport_routes    Transport routes
- transport_vehicles  Vehicles
- student_transport   Student transport
- notices             Notices & announcements
- assignments         Assignments
- assignment_submissions Student submissions
- quizzes             Quizzes
- quiz_questions      Quiz questions
- activity_logs       Activity logs
- settings            System settings
- notifications       User notifications
- backups             Database backups

============================================================
  SECURITY FEATURES
============================================================

- SQL Injection Prevention (Prepared Statements)
- XSS Protection (htmlspecialchars)
- CSRF Token Validation
- Password Hashing (bcrypt)
- Secure Session Management
- Session Timeout (30 minutes)
- Input Validation & Sanitization
- Role-based Access Control
- File Upload Validation

============================================================
  TROUBLESHOOTING
============================================================

Issue: Database connection error
Solution: Make sure XAMPP MySQL service is running and
database credentials in config/database.php are correct.
Default XAMPP credentials: root / (no password)

Issue: 404 Not Found
Solution: Make sure the project folder is in htdocs and
you are accessing the correct URL.

Issue: Styles not loading
Solution: Check that BASE_URL in config/database.php
matches your setup. Default: http://localhost/school-management-system/

Issue: Permission denied
Solution: Make sure the uploads folders have write permissions.
Set folder permissions to 755 or 777 for:
- assets/uploads/students/
- assets/uploads/teachers/
- assets/uploads/documents/
- assets/uploads/logos/

============================================================
  CONTACT & SUPPORT
============================================================

For support or inquiries, please contact:
Email: Rashidshangla@gmail.com
Website: https://www.pashtomedium.com/

============================================================
  LICENSE
============================================================

This software is provided as-is for educational and
commercial use. All rights reserved.

============================================================
  END OF README
============================================================
