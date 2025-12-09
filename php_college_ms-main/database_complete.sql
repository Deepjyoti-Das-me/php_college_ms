-- ============================================
-- College Management System - Complete Database Schema
-- Based on ER Diagram
-- Database Name: college_ms1
-- ============================================

CREATE DATABASE IF NOT EXISTS college_ms1;
USE college_ms1;

-- ============================================
-- 0. USERS TABLE (Must be created first - referenced by all other tables)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('admin', 'student', 'teacher', 'staff') NOT NULL DEFAULT 'student',
    phone VARCHAR(20),
    address TEXT,
    profile_image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 1. COURSE TABLE (Must be created before students - students reference courses)
-- ============================================
CREATE TABLE IF NOT EXISTS courses (
    cid INT(11) AUTO_INCREMENT PRIMARY KEY,
    course_code VARCHAR(20) UNIQUE NOT NULL,
    cname VARCHAR(200) NOT NULL,
    major VARCHAR(100),
    duration_years INT(2),
    total_semesters INT(2),
    fees DECIMAL(10,2),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_course_code (course_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 2. ADMIN TABLE (aid as primary key)
-- ============================================
CREATE TABLE IF NOT EXISTS admin (
    aid INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    fname VARCHAR(50) NOT NULL,
    mname VARCHAR(50),
    lname VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    ph_no VARCHAR(20),
    s_no VARCHAR(20),
    s_name VARCHAR(100),
    pincode VARCHAR(10),
    district VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100) DEFAULT 'India',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 3. TEACHER TABLE (tid as primary key)
-- ============================================
CREATE TABLE IF NOT EXISTS teachers (
    tid INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    teacher_id VARCHAR(50) UNIQUE NOT NULL,
    fname VARCHAR(50) NOT NULL,
    mname VARCHAR(50),
    lname VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    ph_no VARCHAR(20),
    s_no VARCHAR(20),
    s_name VARCHAR(100),
    pincode VARCHAR(10),
    district VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100) DEFAULT 'India',
    salary DECIMAL(10,2),
    hire_date DATE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_teacher_id (teacher_id),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 4. TEACHER SUBJECTS (Multi-valued attribute) - Must be after teachers
-- ============================================
CREATE TABLE IF NOT EXISTS teacher_subjects (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    tid INT(11) NOT NULL,
    subject_type ENUM('arts', 'science') NOT NULL,
    FOREIGN KEY (tid) REFERENCES teachers(tid) ON DELETE CASCADE,
    INDEX idx_teacher (tid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 5. STAFF TABLE (sfid as primary key)
-- ============================================
CREATE TABLE IF NOT EXISTS staff (
    sfid INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    staff_id VARCHAR(50) UNIQUE NOT NULL,
    fname VARCHAR(50) NOT NULL,
    mname VARCHAR(50),
    lname VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    ph_no VARCHAR(20),
    s_no VARCHAR(20),
    s_name VARCHAR(100),
    pincode VARCHAR(10),
    district VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100) DEFAULT 'India',
    salary DECIMAL(10,2),
    designation VARCHAR(100),
    shift_morning BOOLEAN DEFAULT FALSE,
    shift_day BOOLEAN DEFAULT FALSE,
    hire_date DATE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_staff_id (staff_id),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 6. STUDENT TABLE (sid, cid as composite primary key) - Must be after courses and users
-- ============================================
CREATE TABLE IF NOT EXISTS students (
    sid INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    cid INT(11) NOT NULL,
    roll_no VARCHAR(50) UNIQUE NOT NULL,
    fname VARCHAR(50) NOT NULL,
    mname VARCHAR(50),
    lname VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    ph_no VARCHAR(20),
    s_no VARCHAR(20),
    s_name VARCHAR(100),
    pincode VARCHAR(10),
    district VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100) DEFAULT 'India',
    enrollment_date DATE,
    semester VARCHAR(20),
    year INT(4),
    status ENUM('active', 'inactive', 'graduated') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (cid) REFERENCES courses(cid) ON DELETE RESTRICT,
    UNIQUE KEY unique_student_course (sid, cid),
    INDEX idx_roll_no (roll_no),
    INDEX idx_course (cid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 7. COURSE VAC (Multi-valued attribute) - Must be after courses
-- ============================================
CREATE TABLE IF NOT EXISTS course_vac (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    cid INT(11) NOT NULL,
    vac_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (cid) REFERENCES courses(cid) ON DELETE CASCADE,
    INDEX idx_course (cid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 8. COURSE SEC (Multi-valued attribute)
-- ============================================
CREATE TABLE IF NOT EXISTS course_sec (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    cid INT(11) NOT NULL,
    sec_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (cid) REFERENCES courses(cid) ON DELETE CASCADE,
    INDEX idx_course (cid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 9. COURSE AEC (Multi-valued attribute)
-- ============================================
CREATE TABLE IF NOT EXISTS course_aec (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    cid INT(11) NOT NULL,
    aec_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (cid) REFERENCES courses(cid) ON DELETE CASCADE,
    INDEX idx_course (cid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 10. COURSE IDC (Multi-valued attribute)
-- ============================================
CREATE TABLE IF NOT EXISTS course_idc (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    cid INT(11) NOT NULL,
    idc_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (cid) REFERENCES courses(cid) ON DELETE CASCADE,
    INDEX idx_course (cid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 11. COURSE MINOR (Multi-valued attribute)
-- ============================================
CREATE TABLE IF NOT EXISTS course_minor (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    cid INT(11) NOT NULL,
    minor_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (cid) REFERENCES courses(cid) ON DELETE CASCADE,
    INDEX idx_course (cid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 12. SUBJECTS TABLE - Must be after courses and teachers
-- ============================================
CREATE TABLE IF NOT EXISTS subjects (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(20) UNIQUE NOT NULL,
    subject_name VARCHAR(200) NOT NULL,
    cid INT(11),
    semester INT(2),
    credits INT(2),
    tid INT(11),
    subject_type ENUM('VAC', 'SEC', 'AEC', 'IDC', 'Minor', 'Core') DEFAULT 'Core',
    description TEXT,
    FOREIGN KEY (cid) REFERENCES courses(cid) ON DELETE SET NULL,
    FOREIGN KEY (tid) REFERENCES teachers(tid) ON DELETE SET NULL,
    INDEX idx_course (cid),
    INDEX idx_teacher (tid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 13. SESSIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS sessions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    session_name VARCHAR(50) NOT NULL,
    start_date DATE,
    end_date DATE,
    status ENUM('active', 'inactive', 'completed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 14. RELATIONSHIP: STUDENT - STUDIES - TEACHER - Must be after students, teachers, subjects
-- ============================================
CREATE TABLE IF NOT EXISTS student_teacher (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    sid INT(11) NOT NULL,
    tid INT(11) NOT NULL,
    subject_id INT(11),
    FOREIGN KEY (sid) REFERENCES students(sid) ON DELETE CASCADE,
    FOREIGN KEY (tid) REFERENCES teachers(tid) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL,
    UNIQUE KEY unique_student_teacher_subject (sid, tid, subject_id),
    INDEX idx_student (sid),
    INDEX idx_teacher (tid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 15. RELATIONSHIP: ADMIN - MANAGES - TEACHER - Must be after admin and teachers
-- ============================================
CREATE TABLE IF NOT EXISTS admin_manages_teacher (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    aid INT(11) NOT NULL,
    tid INT(11) NOT NULL,
    managed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aid) REFERENCES admin(aid) ON DELETE CASCADE,
    FOREIGN KEY (tid) REFERENCES teachers(tid) ON DELETE CASCADE,
    INDEX idx_admin (aid),
    INDEX idx_teacher (tid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 16. RELATIONSHIP: ADMIN - MANAGES - STAFF - Must be after admin and staff
-- ============================================
CREATE TABLE IF NOT EXISTS admin_manages_staff (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    aid INT(11) NOT NULL,
    sfid INT(11) NOT NULL,
    managed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aid) REFERENCES admin(aid) ON DELETE CASCADE,
    FOREIGN KEY (sfid) REFERENCES staff(sfid) ON DELETE CASCADE,
    INDEX idx_admin (aid),
    INDEX idx_staff (sfid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 17. RELATIONSHIP: ADMIN - MANAGES - COURSE - Must be after admin and courses
-- ============================================
CREATE TABLE IF NOT EXISTS admin_manages_course (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    aid INT(11) NOT NULL,
    cid INT(11) NOT NULL,
    managed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aid) REFERENCES admin(aid) ON DELETE CASCADE,
    FOREIGN KEY (cid) REFERENCES courses(cid) ON DELETE CASCADE,
    INDEX idx_admin (aid),
    INDEX idx_course (cid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 18. ATTENDANCE TABLE - Must be after students, subjects, teachers
-- ============================================
CREATE TABLE IF NOT EXISTS attendance (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    sid INT(11) NOT NULL,
    subject_id INT(11) NOT NULL,
    tid INT(11),
    date DATE NOT NULL,
    status ENUM('present', 'absent', 'late', 'excused') DEFAULT 'absent',
    remarks TEXT,
    FOREIGN KEY (sid) REFERENCES students(sid) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (tid) REFERENCES teachers(tid) ON DELETE SET NULL,
    INDEX idx_student (sid),
    INDEX idx_date (date),
    UNIQUE KEY unique_attendance (sid, subject_id, date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 19. GRADES/RESULTS TABLE - Must be after students, subjects, teachers
-- ============================================
CREATE TABLE IF NOT EXISTS grades (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    sid INT(11) NOT NULL,
    subject_id INT(11) NOT NULL,
    tid INT(11),
    exam_type VARCHAR(50),
    marks_obtained DECIMAL(5,2),
    total_marks DECIMAL(5,2),
    grade VARCHAR(5),
    semester VARCHAR(20),
    exam_date DATE,
    remarks TEXT,
    FOREIGN KEY (sid) REFERENCES students(sid) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (tid) REFERENCES teachers(tid) ON DELETE SET NULL,
    INDEX idx_student (sid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 20. ASSIGNMENTS TABLE - Must be after subjects and teachers
-- ============================================
CREATE TABLE IF NOT EXISTS assignments (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    subject_id INT(11) NOT NULL,
    tid INT(11) NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    due_date DATETIME,
    total_marks DECIMAL(5,2),
    status ENUM('active', 'closed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (tid) REFERENCES teachers(tid) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 21. ASSIGNMENT SUBMISSIONS TABLE - Must be after assignments and students
-- ============================================
CREATE TABLE IF NOT EXISTS assignment_submissions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT(11) NOT NULL,
    sid INT(11) NOT NULL,
    submission_file VARCHAR(255),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    marks_obtained DECIMAL(5,2),
    feedback TEXT,
    status ENUM('submitted', 'graded', 'late') DEFAULT 'submitted',
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (sid) REFERENCES students(sid) ON DELETE CASCADE,
    INDEX idx_assignment (assignment_id),
    INDEX idx_student (sid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 22. NOTIFICATIONS TABLE - Must be after users
-- ============================================
CREATE TABLE IF NOT EXISTS notifications (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    message TEXT,
    target_type ENUM('all', 'student', 'teacher', 'staff', 'admin') DEFAULT 'all',
    target_id INT(11),
    created_by INT(11),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 23. CLASS SCHEDULE TABLE - Must be after subjects and teachers
-- ============================================
CREATE TABLE IF NOT EXISTS class_schedule (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    subject_id INT(11) NOT NULL,
    tid INT(11) NOT NULL,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
    start_time TIME,
    end_time TIME,
    room_number VARCHAR(50),
    semester VARCHAR(20),
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (tid) REFERENCES teachers(tid) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 24. STUDENT FEEDBACK TABLE - Must be after students
-- ============================================
CREATE TABLE IF NOT EXISTS student_feedback (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    sid INT(11) NOT NULL,
    feedback_type ENUM('course', 'teacher', 'facility', 'general') DEFAULT 'general',
    subject VARCHAR(200),
    message TEXT,
    rating INT(1) CHECK (rating >= 1 AND rating <= 5),
    status ENUM('pending', 'reviewed', 'resolved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sid) REFERENCES students(sid) ON DELETE CASCADE,
    INDEX idx_student (sid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 25. STAFF FEEDBACK TABLE - Must be after staff
-- ============================================
CREATE TABLE IF NOT EXISTS staff_feedback (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    sfid INT(11) NOT NULL,
    feedback_type ENUM('workload', 'facility', 'management', 'general') DEFAULT 'general',
    subject VARCHAR(200),
    message TEXT,
    rating INT(1) CHECK (rating >= 1 AND rating <= 5),
    status ENUM('pending', 'reviewed', 'resolved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sfid) REFERENCES staff(sfid) ON DELETE CASCADE,
    INDEX idx_staff (sfid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 26. STAFF LEAVE TABLE - Must be after staff and admin
-- ============================================
CREATE TABLE IF NOT EXISTS staff_leave (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    sfid INT(11) NOT NULL,
    leave_type ENUM('sick', 'casual', 'emergency', 'vacation', 'other') DEFAULT 'casual',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    days INT(3),
    reason TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by INT(11),
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sfid) REFERENCES staff(sfid) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES admin(aid) ON DELETE SET NULL,
    INDEX idx_staff (sfid),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 27. STUDENT LEAVE TABLE - Must be after students and admin
-- ============================================
CREATE TABLE IF NOT EXISTS student_leave (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    sid INT(11) NOT NULL,
    leave_type ENUM('sick', 'emergency', 'personal', 'other') DEFAULT 'personal',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    days INT(3),
    reason TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by INT(11),
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sid) REFERENCES students(sid) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES admin(aid) ON DELETE SET NULL,
    INDEX idx_student (sid),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Insert Default Admin Account
-- ============================================
-- First insert into users table
INSERT INTO users (full_name, email, password, user_type, status) VALUES
('Admin User', 'admin@college.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active')
ON DUPLICATE KEY UPDATE email=email;

-- Then insert into admin table (get the user_id)
SET @admin_user_id = (SELECT id FROM users WHERE email = 'admin@college.com' LIMIT 1);

INSERT INTO admin (user_id, fname, lname, email, country, status) VALUES
(@admin_user_id, 'Admin', 'User', 'admin@college.com', 'India', 'active')
ON DUPLICATE KEY UPDATE email=email;

-- ============================================
-- Insert Sample Course
-- ============================================
INSERT INTO courses (course_code, cname, major, duration_years, total_semesters) VALUES
('CS101', 'Computer Science', 'Computer Science', 4, 8),
('EE101', 'Electrical Engineering', 'Electrical Engineering', 4, 8),
('ME101', 'Mechanical Engineering', 'Mechanical Engineering', 4, 8)
ON DUPLICATE KEY UPDATE course_code=course_code;

-- ============================================
-- Insert Sample Session
-- ============================================
INSERT INTO sessions (session_name, start_date, end_date, status) VALUES
('2024-2025', '2024-01-01', '2025-12-31', 'active')
ON DUPLICATE KEY UPDATE session_name=session_name;
