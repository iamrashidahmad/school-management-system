-- ============================================================
-- Advanced School Management System ERP - Complete Database
-- ============================================================
-- Version: 1.0
-- Date: 2026-05-15
-- Compatible with: MySQL 5.7+ / MariaDB 10.3+
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Create Database
CREATE DATABASE IF NOT EXISTS school_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE school_management;

-- ============================================================
-- 1. SCHOOL INFORMATION TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS school_info (
    school_id INT(11) NOT NULL AUTO_INCREMENT,
    school_name VARCHAR(255) NOT NULL,
    school_address TEXT,
    school_phone VARCHAR(50),
    school_email VARCHAR(100),
    school_website VARCHAR(100),
    school_logo VARCHAR(255) DEFAULT 'default_logo.png',
    principal_name VARCHAR(100),
    established_year YEAR,
    registration_number VARCHAR(100),
    academic_session VARCHAR(20) DEFAULT '2025-2026',
    session_start_date DATE,
    session_end_date DATE,
    currency_symbol VARCHAR(10) DEFAULT '$',
    timezone VARCHAR(50) DEFAULT 'UTC',
    theme_color VARCHAR(20) DEFAULT '#4e73df',
    sidebar_color VARCHAR(20) DEFAULT '#2e59d9',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (school_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. USERS TABLE (Authentication)
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    user_id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','teacher','student','parent','accountant') NOT NULL,
    profile_image VARCHAR(255) DEFAULT 'default.png',
    phone VARCHAR(20),
    last_login DATETIME,
    login_ip VARCHAR(45),
    is_active TINYINT(1) DEFAULT 1,
    remember_token VARCHAR(255),
    email_verified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id),
    UNIQUE KEY uk_email (email),
    UNIQUE KEY uk_username (username),
    KEY idx_role (role),
    KEY idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. CLASSES TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS classes (
    class_id INT(11) NOT NULL AUTO_INCREMENT,
    class_name VARCHAR(50) NOT NULL,
    class_name_numeric INT(11),
    class_teacher_id INT(11) DEFAULT NULL,
    capacity INT(11) DEFAULT 40,
    section_count INT(11) DEFAULT 0,
    description TEXT,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (class_id),
    KEY idx_class_teacher (class_teacher_id),
    KEY idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. SECTIONS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS sections (
    section_id INT(11) NOT NULL AUTO_INCREMENT,
    section_name VARCHAR(20) NOT NULL,
    class_id INT(11) NOT NULL,
    class_teacher_id INT(11) DEFAULT NULL,
    capacity INT(11) DEFAULT 40,
    room_number VARCHAR(20),
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (section_id),
    KEY idx_class_section (class_id),
    KEY idx_section_teacher (class_teacher_id),
    CONSTRAINT fk_section_class FOREIGN KEY (class_id) REFERENCES classes(class_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. SUBJECTS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS subjects (
    subject_id INT(11) NOT NULL AUTO_INCREMENT,
    subject_name VARCHAR(100) NOT NULL,
    subject_code VARCHAR(30) NOT NULL,
    class_id INT(11) NOT NULL,
    teacher_id INT(11) DEFAULT NULL,
    full_marks INT(11) DEFAULT 100,
    pass_marks INT(11) DEFAULT 40,
    description TEXT,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (subject_id),
    UNIQUE KEY uk_subject_code (subject_code),
    KEY idx_class_subject (class_id),
    KEY idx_teacher_subject (teacher_id),
    CONSTRAINT fk_subject_class FOREIGN KEY (class_id) REFERENCES classes(class_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. TEACHERS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS teachers (
    teacher_id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    teacher_code VARCHAR(30) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    gender ENUM('Male','Female','Other') NOT NULL,
    date_of_birth DATE,
    cnic VARCHAR(30),
    qualification VARCHAR(255),
    specialization VARCHAR(100),
    experience_years INT(11) DEFAULT 0,
    joining_date DATE,
    salary DECIMAL(12,2) DEFAULT 0.00,
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    zip_code VARCHAR(20),
    country VARCHAR(50),
    photo VARCHAR(255) DEFAULT 'default_teacher.png',
    biography TEXT,
    status ENUM('active','inactive','on_leave','retired') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (teacher_id),
    UNIQUE KEY uk_teacher_code (teacher_code),
    UNIQUE KEY uk_teacher_user (user_id),
    CONSTRAINT fk_teacher_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. PARENTS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS parents (
    parent_id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    father_name VARCHAR(100) NOT NULL,
    mother_name VARCHAR(100),
    father_cnic VARCHAR(30),
    mother_cnic VARCHAR(30),
    father_phone VARCHAR(20),
    mother_phone VARCHAR(20),
    father_email VARCHAR(100),
    mother_email VARCHAR(100),
    father_occupation VARCHAR(100),
    mother_occupation VARCHAR(100),
    father_income DECIMAL(12,2),
    mother_income DECIMAL(12,2),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    zip_code VARCHAR(20),
    country VARCHAR(50),
    emergency_contact VARCHAR(20),
    emergency_contact_name VARCHAR(100),
    photo VARCHAR(255) DEFAULT 'default_parent.png',
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (parent_id),
    UNIQUE KEY uk_parent_user (user_id),
    CONSTRAINT fk_parent_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. STUDENTS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS students (
    student_id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    admission_no VARCHAR(30) NOT NULL,
    roll_no VARCHAR(30),
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    gender ENUM('Male','Female','Other') NOT NULL,
    date_of_birth DATE,
    cnic_bform VARCHAR(30),
    religion VARCHAR(50),
    blood_group VARCHAR(10),
    phone VARCHAR(20),
    email VARCHAR(100),
    class_id INT(11) NOT NULL,
    section_id INT(11),
    parent_id INT(11),
    admission_date DATE,
    previous_school VARCHAR(100),
    previous_class VARCHAR(50),
    transfer_certificate VARCHAR(255),
    birth_certificate VARCHAR(255),
    photo VARCHAR(255) DEFAULT 'default_student.png',
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    zip_code VARCHAR(20),
    country VARCHAR(50),
    transport_id INT(11) DEFAULT NULL,
    hostel_required TINYINT(1) DEFAULT 0,
    medical_notes TEXT,
    status ENUM('active','inactive','graduated','transferred','suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (student_id),
    UNIQUE KEY uk_admission_no (admission_no),
    UNIQUE KEY uk_student_user (user_id),
    KEY idx_class_student (class_id),
    KEY idx_section_student (section_id),
    KEY idx_parent_student (parent_id),
    CONSTRAINT fk_student_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_student_class FOREIGN KEY (class_id) REFERENCES classes(class_id),
    CONSTRAINT fk_student_section FOREIGN KEY (section_id) REFERENCES sections(section_id),
    CONSTRAINT fk_student_parent FOREIGN KEY (parent_id) REFERENCES parents(parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. STUDENT DOCUMENTS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS student_documents (
    document_id INT(11) NOT NULL AUTO_INCREMENT,
    student_id INT(11) NOT NULL,
    document_name VARCHAR(100) NOT NULL,
    document_type VARCHAR(50),
    document_file VARCHAR(255) NOT NULL,
    uploaded_by INT(11),
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    PRIMARY KEY (document_id),
    KEY idx_student_doc (student_id),
    CONSTRAINT fk_doc_student FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ============================================================
-- 10. ATTENDANCE TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS attendance (
    attendance_id INT(11) NOT NULL AUTO_INCREMENT,
    student_id INT(11) NOT NULL,
    class_id INT(11) NOT NULL,
    section_id INT(11),
    attendance_date DATE NOT NULL,
    status ENUM('Present','Absent','Late','Half Day','On Leave') NOT NULL,
    remarks VARCHAR(255),
    marked_by INT(11),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (attendance_id),
    UNIQUE KEY uk_attendance (student_id, attendance_date),
    KEY idx_att_date (attendance_date),
    KEY idx_att_class (class_id),
    KEY idx_att_status (status),
    CONSTRAINT fk_att_student FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    CONSTRAINT fk_att_class FOREIGN KEY (class_id) REFERENCES classes(class_id),
    CONSTRAINT fk_att_section FOREIGN KEY (section_id) REFERENCES sections(section_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 11. TEACHER ATTENDANCE TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS teacher_attendance (
    att_id INT(11) NOT NULL AUTO_INCREMENT,
    teacher_id INT(11) NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('Present','Absent','Late','Half Day','On Leave') NOT NULL,
    remarks VARCHAR(255),
    marked_by INT(11),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (att_id),
    UNIQUE KEY uk_teacher_att (teacher_id, attendance_date),
    KEY idx_teacher_att_date (attendance_date),
    CONSTRAINT fk_teacher_att_teacher FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 12. EXAM TYPES TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS exam_types (
    exam_type_id INT(11) NOT NULL AUTO_INCREMENT,
    exam_name VARCHAR(100) NOT NULL,
    percentage_weight DECIMAL(5,2) DEFAULT 0.00,
    description TEXT,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (exam_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 13. EXAMS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS exams (
    exam_id INT(11) NOT NULL AUTO_INCREMENT,
    exam_type_id INT(11) NOT NULL,
    class_id INT(11) NOT NULL,
    session_year VARCHAR(20) NOT NULL,
    semester_name VARCHAR(50),
    exam_name VARCHAR(100) NOT NULL,
    start_date DATE,
    end_date DATE,
    publish_result TINYINT(1) DEFAULT 0,
    notes TEXT,
    status TINYINT(1) DEFAULT 1,
    created_by INT(11),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (exam_id),
    KEY idx_exam_type (exam_type_id),
    KEY idx_exam_class (class_id),
    KEY idx_exam_session (session_year),
    CONSTRAINT fk_exam_type FOREIGN KEY (exam_type_id) REFERENCES exam_types(exam_type_id),
    CONSTRAINT fk_exam_class FOREIGN KEY (class_id) REFERENCES classes(class_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 14. EXAM SUBJECTS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS exam_subjects (
    exam_subject_id INT(11) NOT NULL AUTO_INCREMENT,
    exam_id INT(11) NOT NULL,
    subject_id INT(11) NOT NULL,
    total_marks INT(11) NOT NULL DEFAULT 100,
    pass_marks INT(11) DEFAULT 40,
    exam_date DATE,
    start_time TIME,
    end_time TIME,
    room VARCHAR(50),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (exam_subject_id),
    UNIQUE KEY uk_exam_subject (exam_id, subject_id),
    CONSTRAINT fk_examsub_exam FOREIGN KEY (exam_id) REFERENCES exams(exam_id) ON DELETE CASCADE,
    CONSTRAINT fk_examsub_subject FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 15. STUDENT MARKS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS student_marks (
    mark_id INT(11) NOT NULL AUTO_INCREMENT,
    student_id INT(11) NOT NULL,
    exam_id INT(11) NOT NULL,
    exam_subject_id INT(11) NOT NULL,
    subject_id INT(11) NOT NULL,
    obtained_marks DECIMAL(8,2) DEFAULT 0.00,
    total_marks INT(11) DEFAULT 100,
    percentage DECIMAL(5,2) DEFAULT 0.00,
    grade VARCHAR(5),
    remarks VARCHAR(255),
    entered_by INT(11),
    entered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (mark_id),
    UNIQUE KEY uk_student_mark (student_id, exam_id, exam_subject_id),
    KEY idx_mark_student (student_id),
    KEY idx_mark_exam (exam_id),
    CONSTRAINT fk_mark_student FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    CONSTRAINT fk_mark_exam FOREIGN KEY (exam_id) REFERENCES exams(exam_id) ON DELETE CASCADE,
    CONSTRAINT fk_mark_examsub FOREIGN KEY (exam_subject_id) REFERENCES exam_subjects(exam_subject_id) ON DELETE CASCADE,
    CONSTRAINT fk_mark_subject FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 16. GRADING SYSTEM TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS grading_system (
    grade_id INT(11) NOT NULL AUTO_INCREMENT,
    grade_name VARCHAR(10) NOT NULL,
    min_percentage DECIMAL(5,2) NOT NULL,
    max_percentage DECIMAL(5,2) NOT NULL,
    grade_point DECIMAL(3,2),
    description VARCHAR(100),
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (grade_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 17. RESULTS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS results (
    result_id INT(11) NOT NULL AUTO_INCREMENT,
    student_id INT(11) NOT NULL,
    exam_id INT(11) NOT NULL,
    class_id INT(11) NOT NULL,
    total_obtained DECIMAL(10,2) DEFAULT 0.00,
    total_marks INT(11) DEFAULT 0,
    percentage DECIMAL(5,2) DEFAULT 0.00,
    grade VARCHAR(10),
    grade_point DECIMAL(3,2),
    class_position INT(11),
    status ENUM('Pass','Fail','Pending') DEFAULT 'Pending',
    remarks TEXT,
    published_by INT(11),
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (result_id),
    UNIQUE KEY uk_student_exam_result (student_id, exam_id),
    KEY idx_result_class (class_id),
    KEY idx_result_percentage (percentage),
    CONSTRAINT fk_result_student FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    CONSTRAINT fk_result_exam FOREIGN KEY (exam_id) REFERENCES exams(exam_id) ON DELETE CASCADE,
    CONSTRAINT fk_result_class FOREIGN KEY (class_id) REFERENCES classes(class_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 18. FEE TYPES TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS fee_types (
    fee_type_id INT(11) NOT NULL AUTO_INCREMENT,
    fee_name VARCHAR(100) NOT NULL,
    fee_code VARCHAR(30),
    description TEXT,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (fee_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 19. FEE STRUCTURE TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS fee_structure (
    fee_structure_id INT(11) NOT NULL AUTO_INCREMENT,
    class_id INT(11) NOT NULL,
    fee_type_id INT(11) NOT NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    frequency ENUM('Monthly','Quarterly','Half Yearly','Yearly','One Time') DEFAULT 'Monthly',
    academic_year VARCHAR(20),
    due_day INT(11) DEFAULT 10,
    late_fine_amount DECIMAL(10,2) DEFAULT 0.00,
    late_fine_per_day DECIMAL(10,2) DEFAULT 0.00,
    notes TEXT,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (fee_structure_id),
    UNIQUE KEY uk_class_fee_type (class_id, fee_type_id, academic_year),
    CONSTRAINT fk_fees_class FOREIGN KEY (class_id) REFERENCES classes(class_id),
    CONSTRAINT fk_fees_type FOREIGN KEY (fee_type_id) REFERENCES fee_types(fee_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 20. STUDENT FEES TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS student_fees (
    fee_id INT(11) NOT NULL AUTO_INCREMENT,
    student_id INT(11) NOT NULL,
    fee_structure_id INT(11) NOT NULL,
    fee_type_id INT(11) NOT NULL,
    class_id INT(11) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    paid_amount DECIMAL(12,2) DEFAULT 0.00,
    discount_amount DECIMAL(12,2) DEFAULT 0.00,
    fine_amount DECIMAL(10,2) DEFAULT 0.00,
    balance_amount DECIMAL(12,2) DEFAULT 0.00,
    month_year VARCHAR(20),
    due_date DATE,
    payment_date DATE,
    payment_method ENUM('Cash','Bank Transfer','Check','Online','Card') DEFAULT 'Cash',
    transaction_id VARCHAR(100),
    receipt_no VARCHAR(50),
    status ENUM('Paid','Unpaid','Partial','Overdue') DEFAULT 'Unpaid',
    notes TEXT,
    collected_by INT(11),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (fee_id),
    KEY idx_fee_student (student_id),
    KEY idx_fee_status (status),
    KEY idx_fee_receipt (receipt_no),
    CONSTRAINT fk_fee_student FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    CONSTRAINT fk_fee_structure FOREIGN KEY (fee_structure_id) REFERENCES fee_structure(fee_structure_id),
    CONSTRAINT fk_fee_type FOREIGN KEY (fee_type_id) REFERENCES fee_types(fee_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 21. FEE PAYMENTS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS fee_payments (
    payment_id INT(11) NOT NULL AUTO_INCREMENT,
    fee_id INT(11) NOT NULL,
    student_id INT(11) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    payment_method ENUM('Cash','Bank Transfer','Check','Online','Card') DEFAULT 'Cash',
    transaction_id VARCHAR(100),
    receipt_no VARCHAR(50),
    payment_date DATE,
    collected_by INT(11),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (payment_id),
    KEY idx_payment_student (student_id),
    KEY idx_payment_receipt (receipt_no),
    CONSTRAINT fk_payment_fee FOREIGN KEY (fee_id) REFERENCES student_fees(fee_id) ON DELETE CASCADE,
    CONSTRAINT fk_payment_student FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 22. LIBRARY BOOKS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS library_books (
    book_id INT(11) NOT NULL AUTO_INCREMENT,
    book_title VARCHAR(255) NOT NULL,
    book_code VARCHAR(50) NOT NULL,
    isbn VARCHAR(50),
    author VARCHAR(100),
    publisher VARCHAR(100),
    edition VARCHAR(50),
    category VARCHAR(50),
    language VARCHAR(30) DEFAULT 'English',
    pages INT(11),
    price DECIMAL(10,2),
    quantity INT(11) DEFAULT 1,
    available_quantity INT(11) DEFAULT 1,
    shelf_location VARCHAR(50),
    publish_year YEAR,
    description TEXT,
    cover_image VARCHAR(255),
    status ENUM('Available','Issued','Reserved','Damaged','Lost') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (book_id),
    UNIQUE KEY uk_book_code (book_code),
    KEY idx_book_isbn (isbn),
    KEY idx_book_category (category),
    KEY idx_book_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 23. BOOK ISSUES TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS book_issues (
    issue_id INT(11) NOT NULL AUTO_INCREMENT,
    book_id INT(11) NOT NULL,
    student_id INT(11) NOT NULL,
    issue_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE,
    fine_amount DECIMAL(10,2) DEFAULT 0.00,
    fine_paid DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('Issued','Returned','Overdue','Lost') DEFAULT 'Issued',
    issued_by INT(11),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (issue_id),
    KEY idx_issue_book (book_id),
    KEY idx_issue_student (student_id),
    KEY idx_issue_status (status),
    CONSTRAINT fk_issue_book FOREIGN KEY (book_id) REFERENCES library_books(book_id),
    CONSTRAINT fk_issue_student FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 24. TRANSPORT ROUTES TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS transport_routes (
    route_id INT(11) NOT NULL AUTO_INCREMENT,
    route_name VARCHAR(100) NOT NULL,
    route_code VARCHAR(30),
    start_location VARCHAR(100),
    end_location VARCHAR(100),
    distance_km DECIMAL(8,2),
    fare_amount DECIMAL(10,2),
    description TEXT,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (route_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 25. TRANSPORT VEHICLES TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS transport_vehicles (
    vehicle_id INT(11) NOT NULL AUTO_INCREMENT,
    vehicle_number VARCHAR(30) NOT NULL,
    vehicle_type VARCHAR(50),
    model VARCHAR(50),
    capacity INT(11),
    driver_name VARCHAR(100),
    driver_phone VARCHAR(20),
    driver_license VARCHAR(50),
    route_id INT(11),
    status ENUM('Active','Maintenance','Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (vehicle_id),
    KEY idx_vehicle_route (route_id),
    CONSTRAINT fk_vehicle_route FOREIGN KEY (route_id) REFERENCES transport_routes(route_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 26. STUDENT TRANSPORT TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS student_transport (
    st_id INT(11) NOT NULL AUTO_INCREMENT,
    student_id INT(11) NOT NULL,
    vehicle_id INT(11) NOT NULL,
    route_id INT(11) NOT NULL,
    pickup_point VARCHAR(100),
    drop_point VARCHAR(100),
    fare_amount DECIMAL(10,2),
    start_date DATE,
    end_date DATE,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (st_id),
    KEY idx_st_student (student_id),
    CONSTRAINT fk_st_student FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    CONSTRAINT fk_st_vehicle FOREIGN KEY (vehicle_id) REFERENCES transport_vehicles(vehicle_id),
    CONSTRAINT fk_st_route FOREIGN KEY (route_id) REFERENCES transport_routes(route_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 27. NOTICES TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS notices (
    notice_id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    notice_type ENUM('General','Exam','Fee','Event','Holiday','Urgent') DEFAULT 'General',
    target_role ENUM('All','Admin','Teacher','Student','Parent','Accountant') DEFAULT 'All',
    target_class INT(11),
    attachment VARCHAR(255),
    posted_by INT(11),
    is_pinned TINYINT(1) DEFAULT 0,
    start_date DATE,
    end_date DATE,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (notice_id),
    KEY idx_notice_type (notice_type),
    KEY idx_notice_target (target_role),
    KEY idx_notice_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 28. ASSIGNMENTS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS assignments (
    assignment_id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    class_id INT(11) NOT NULL,
    section_id INT(11),
    subject_id INT(11) NOT NULL,
    teacher_id INT(11) NOT NULL,
    assigned_date DATE,
    submission_date DATE,
    total_marks INT(11) DEFAULT 100,
    attachment VARCHAR(255),
    status ENUM('Active','Closed','Draft') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (assignment_id),
    KEY idx_assignment_class (class_id),
    KEY idx_assignment_subject (subject_id),
    KEY idx_assignment_teacher (teacher_id),
    CONSTRAINT fk_assignment_class FOREIGN KEY (class_id) REFERENCES classes(class_id),
    CONSTRAINT fk_assignment_subject FOREIGN KEY (subject_id) REFERENCES subjects(subject_id),
    CONSTRAINT fk_assignment_teacher FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 29. ASSIGNMENT SUBMISSIONS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS assignment_submissions (
    submission_id INT(11) NOT NULL AUTO_INCREMENT,
    assignment_id INT(11) NOT NULL,
    student_id INT(11) NOT NULL,
    submission_file VARCHAR(255),
    remarks TEXT,
    obtained_marks INT(11),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Submitted','Graded','Late') DEFAULT 'Submitted',
    PRIMARY KEY (submission_id),
    UNIQUE KEY uk_assignment_student (assignment_id, student_id),
    CONSTRAINT fk_submission_assignment FOREIGN KEY (assignment_id) REFERENCES assignments(assignment_id) ON DELETE CASCADE,
    CONSTRAINT fk_submission_student FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 30. QUIZZES TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS quizzes (
    quiz_id INT(11) NOT NULL AUTO_INCREMENT,
    quiz_title VARCHAR(255) NOT NULL,
    class_id INT(11) NOT NULL,
    section_id INT(11),
    subject_id INT(11) NOT NULL,
    teacher_id INT(11) NOT NULL,
    total_marks INT(11) DEFAULT 20,
    duration_minutes INT(11) DEFAULT 30,
    quiz_date DATE,
    instructions TEXT,
    status ENUM('Draft','Published','Closed') DEFAULT 'Draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (quiz_id),
    KEY idx_quiz_class (class_id),
    KEY idx_quiz_subject (subject_id),
    CONSTRAINT fk_quiz_class FOREIGN KEY (class_id) REFERENCES classes(class_id),
    CONSTRAINT fk_quiz_subject FOREIGN KEY (subject_id) REFERENCES subjects(subject_id),
    CONSTRAINT fk_quiz_teacher FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 31. QUIZ QUESTIONS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS quiz_questions (
    question_id INT(11) NOT NULL AUTO_INCREMENT,
    quiz_id INT(11) NOT NULL,
    question_text TEXT NOT NULL,
    question_type ENUM('MCQ','True/False','Short Answer') DEFAULT 'MCQ',
    options TEXT,
    correct_answer TEXT,
    marks INT(11) DEFAULT 1,
    sort_order INT(11) DEFAULT 0,
    PRIMARY KEY (question_id),
    CONSTRAINT fk_question_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(quiz_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 32. ACTIVITY LOGS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS activity_logs (
    log_id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11),
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (log_id),
    KEY idx_log_user (user_id),
    KEY idx_log_action (action),
    KEY idx_log_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 33. SETTINGS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS settings (
    setting_id INT(11) NOT NULL AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT,
    setting_group VARCHAR(50) DEFAULT 'general',
    description VARCHAR(255),
    PRIMARY KEY (setting_id),
    UNIQUE KEY uk_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 34. NOTIFICATIONS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT,
    type ENUM('info','success','warning','error') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    related_id INT(11),
    related_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (notification_id),
    KEY idx_notif_user (user_id),
    KEY idx_notif_read (is_read),
    CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 35. BACKUPS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS backups (
    backup_id INT(11) NOT NULL AUTO_INCREMENT,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size BIGINT(20),
    backup_type ENUM('Manual','Scheduled') DEFAULT 'Manual',
    created_by INT(11),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (backup_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- INSERT DUMMY DATA
-- ============================================================

-- School Info
INSERT INTO school_info (school_name, school_address, school_phone, school_email, school_website, principal_name, established_year, registration_number, academic_session, currency_symbol) VALUES
('Excellence Public School', '123 Education Street, Knowledge City', '+1-234-567-8900', 'info@excellence.edu', 'www.excellence.edu', 'Dr. John Smith', 1995, 'REG-1995-001', '2025-2026', '$');

-- Users (Password: admin123, teacher123, student123, parent123, accountant123 - all hashed with password_hash)
INSERT INTO users (username, email, password, role, phone, is_active) VALUES
('admin', 'admin@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '+1-234-567-8901', 1),
('teacher1', 'teacher@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', '+1-234-567-8902', 1),
('student1', 'student@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '+1-234-567-8903', 1),
('parent1', 'parent@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'parent', '+1-234-567-8904', 1),
('accountant1', 'accountant@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'accountant', '+1-234-567-8905', 1);

-- Classes
INSERT INTO classes (class_name, class_name_numeric, capacity, description) VALUES
('Play Group', 0, 30, 'Early childhood education'),
('Nursery', 1, 35, 'Foundation stage'),
('Class 1', 1, 40, 'Primary education'),
('Class 2', 2, 40, 'Primary education'),
('Class 3', 3, 40, 'Primary education'),
('Class 4', 4, 40, 'Primary education'),
('Class 5', 5, 40, 'Primary education'),
('Class 6', 6, 40, 'Middle school'),
('Class 7', 7, 40, 'Middle school'),
('Class 8', 8, 40, 'Middle school'),
('Class 9', 9, 40, 'Secondary school'),
('Class 10', 10, 40, 'Secondary school'),
('Class 11', 11, 35, 'Higher secondary'),
('Class 12', 12, 35, 'Higher secondary');

-- Sections
INSERT INTO sections (section_name, class_id, capacity, room_number) VALUES
('A', 3, 25, 'R-101'), ('B', 3, 25, 'R-102'),
('A', 4, 25, 'R-103'), ('B', 4, 25, 'R-104'),
('A', 5, 25, 'R-201'), ('B', 5, 25, 'R-202'),
('A', 6, 25, 'R-203'), ('B', 6, 25, 'R-204'),
('A', 7, 25, 'R-301'), ('B', 7, 25, 'R-302'),
('A', 8, 25, 'R-303'), ('B', 8, 25, 'R-304'),
('A', 9, 25, 'R-401'), ('B', 9, 25, 'R-402'),
('A', 10, 25, 'R-403'), ('B', 10, 25, 'R-404'),
('A', 11, 20, 'R-501'), ('B', 11, 20, 'R-502'),
('A', 12, 20, 'R-503'), ('B', 12, 20, 'R-504');

-- Teachers
INSERT INTO teachers (user_id, teacher_code, first_name, last_name, gender, date_of_birth, cnic, qualification, specialization, experience_years, joining_date, salary, phone, email, address, city, status) VALUES
(2, 'TCH-001', 'Robert', 'Johnson', 'Male', '1980-05-15', '35201-1234567-1', 'M.Ed, B.Sc', 'Mathematics', 15, '2010-08-01', 3500.00, '+1-234-567-8910', 'teacher@school.com', '456 Teacher Lane', 'Knowledge City', 'active');

-- Parents
INSERT INTO parents (user_id, father_name, mother_name, father_cnic, mother_cnic, father_phone, mother_phone, father_email, father_occupation, mother_occupation, address, city) VALUES
(4, 'Michael Brown', 'Sarah Brown', '35201-7654321-0', '35201-7654321-1', '+1-234-567-8920', '+1-234-567-8921', 'parent@school.com', 'Engineer', 'Doctor', '789 Family Road', 'Knowledge City');

-- Students
INSERT INTO students (user_id, admission_no, roll_no, first_name, last_name, gender, date_of_birth, cnic_bform, religion, blood_group, phone, email, class_id, section_id, parent_id, admission_date, previous_school, address, city, status) VALUES
(3, 'ADM-2025-001', 'R-001', 'James', 'Brown', 'Male', '2010-03-20', '35201-1111111-2', 'Christian', 'O+', '+1-234-567-8930', 'student@school.com', 5, 5, 1, '2025-04-01', 'Little Stars School', '789 Family Road', 'Knowledge City', 'active');

-- Exam Types
INSERT INTO exam_types (exam_name, percentage_weight, description) VALUES
('1st Semester', 40.00, 'First semester examination'),
('2nd Semester', 40.00, 'Second semester examination'),
('Monthly Assessment', 5.00, 'Monthly class assessment'),
('Quiz', 5.00, 'Class quiz assessment'),
('Assignment', 5.00, 'Homework assignment'),
('Midterm', 5.00, 'Midterm examination');

-- Grading System
INSERT INTO grading_system (grade_name, min_percentage, max_percentage, grade_point, description) VALUES
('A+', 95.00, 100.00, 4.00, 'Outstanding'),
('A', 85.00, 94.99, 3.70, 'Excellent'),
('B+', 80.00, 84.99, 3.30, 'Very Good'),
('B', 75.00, 79.99, 3.00, 'Good'),
('C+', 70.00, 74.99, 2.70, 'Above Average'),
('C', 65.00, 69.99, 2.30, 'Average'),
('D', 60.00, 64.99, 2.00, 'Below Average'),
('F', 0.00, 59.99, 0.00, 'Fail');

-- Fee Types
INSERT INTO fee_types (fee_name, fee_code, description) VALUES
('Tuition Fee', 'TUI', 'Monthly tuition fee'),
('Admission Fee', 'ADM', 'One time admission fee'),
('Examination Fee', 'EXM', 'Examination fee per term'),
('Library Fee', 'LIB', 'Annual library fee'),
('Transport Fee', 'TRN', 'Monthly transport fee'),
('Computer Fee', 'COM', 'Computer lab fee'),
('Sports Fee', 'SPT', 'Annual sports fee'),
('Laboratory Fee', 'LAB', 'Science lab fee');

-- Transport Routes
INSERT INTO transport_routes (route_name, route_code, start_location, end_location, distance_km, fare_amount) VALUES
('Route 1 - North', 'R001', 'School', 'North City Center', 12.5, 80.00),
('Route 2 - South', 'R002', 'School', 'South City Mall', 15.0, 95.00),
('Route 3 - East', 'R003', 'School', 'East Township', 10.0, 65.00),
('Route 4 - West', 'R004', 'School', 'West Valley', 18.0, 110.00);

-- Transport Vehicles
INSERT INTO transport_vehicles (vehicle_number, vehicle_type, model, capacity, driver_name, driver_phone, driver_license, route_id) VALUES
('BUS-001', 'Bus', 'Toyota Coaster', 30, 'David Wilson', '+1-234-567-8940', 'DL-123456', 1),
('BUS-002', 'Bus', 'Mitsubishi Rosa', 28, 'Thomas Anderson', '+1-234-567-8941', 'DL-123457', 2),
('VAN-001', 'Van', 'Toyota HiAce', 15, 'Chris Martinez', '+1-234-567-8942', 'DL-123458', 3);

-- Library Books
INSERT INTO library_books (book_title, book_code, isbn, author, publisher, edition, category, language, pages, price, quantity, available_quantity, shelf_location, publish_year, status) VALUES
('Mathematics for Class 5', 'BK-001', '978-0-123456-78-9', 'John Davis', 'Academic Press', '3rd', 'Mathematics', 'English', 320, 25.00, 5, 4, 'A-01-01', 2023, 'Available'),
('Science Discovery', 'BK-002', '978-0-123456-79-6', 'Mary Johnson', 'Science Books Ltd', '2nd', 'Science', 'English', 280, 22.00, 4, 3, 'A-02-01', 2024, 'Available'),
('English Grammar', 'BK-003', '978-0-123456-80-2', 'Robert Smith', 'Language Press', '5th', 'English', 'English', 350, 20.00, 6, 5, 'B-01-01', 2023, 'Available'),
('History of the World', 'BK-004', '978-0-123456-81-9', 'Patricia White', 'History Publishers', '1st', 'History', 'English', 420, 30.00, 3, 3, 'C-01-01', 2024, 'Available'),
('Computer Basics', 'BK-005', '978-0-123456-82-6', 'Michael Brown', 'Tech Books Inc', '4th', 'Computer', 'English', 250, 28.00, 5, 5, 'D-01-01', 2023, 'Available');

-- Settings
INSERT INTO settings (setting_key, setting_value, setting_group, description) VALUES
('school_name', 'Excellence Public School', 'general', 'Name of the school'),
('academic_session', '2025-2026', 'general', 'Current academic session'),
('currency', '$', 'general', 'Currency symbol'),
('theme_color', '#4e73df', 'appearance', 'Primary theme color'),
('sidebar_color', '#2e59d9', 'appearance', 'Sidebar color'),
('records_per_page', '25', 'general', 'Default records per page'),
('enable_registration', '0', 'general', 'Enable student self-registration'),
('maintenance_mode', '0', 'general', 'Put site in maintenance mode'),
('fee_due_day', '10', 'fees', 'Day of month when fees are due'),
('library_fine_per_day', '1.00', 'library', 'Library fine amount per day'),
('attendance_threshold', '75.00', 'attendance', 'Minimum attendance percentage required');

-- Notices
INSERT INTO notices (title, content, notice_type, target_role, posted_by, is_pinned, start_date, end_date) VALUES
('Welcome to New Academic Session 2025-2026', 'Welcome all students, parents, and staff to the new academic session. Classes begin on April 1st, 2025.', 'General', 'All', 1, 1, '2025-04-01', '2025-04-30'),
('First Semester Examination Schedule', 'First semester exams will begin on May 15th, 2025. Please check the exam timetable.', 'Exam', 'Student', 1, 0, '2025-05-01', '2025-05-31'),
('Fee Payment Reminder', 'Please pay your monthly fees by the 10th of each month to avoid late fines.', 'Fee', 'Parent', 1, 0, '2025-04-01', '2025-06-30'),
('Annual Sports Day', 'Annual Sports Day will be held on June 15th, 2025. All students must participate.', 'Event', 'All', 1, 0, '2025-05-15', '2025-06-15'),
('Summer Vacation Dates', 'Summer vacation will start from July 1st to August 31st, 2025.', 'Holiday', 'All', 1, 1, '2025-06-15', '2025-08-31');

-- Activity Logs
INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES
(1, 'Login', 'Admin logged in successfully', '127.0.0.1'),
(1, 'School Setup', 'School information configured', '127.0.0.1'),
(1, 'Class Created', 'Created Class 1 to Class 12', '127.0.0.1'),
(1, 'Exam Type Created', 'Created default exam types', '127.0.0.1'),
(1, 'Teacher Added', 'Added teacher Robert Johnson', '127.0.0.1');

-- Notifications
INSERT INTO notifications (user_id, title, message, type, is_read) VALUES
(1, 'New Student Registration', 'A new student has been registered in Class 5', 'info', 0),
(1, 'Fee Payment Received', 'Fee payment received from James Brown', 'success', 0),
(1, 'Exam Schedule Published', 'First semester exam schedule has been published', 'info', 0),
(2, 'New Assignment', 'You have been assigned to teach Mathematics', 'info', 0),
(3, 'Exam Reminder', 'Your first semester exam starts on May 15th', 'warning', 0);

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- END OF DATABASE SCRIPT
-- ============================================================