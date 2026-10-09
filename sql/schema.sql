CREATE DATABASE IF NOT EXISTS training_db;
USE training_db;

CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(30),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS courses (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    course_code VARCHAR(20) NOT NULL UNIQUE,
    course_name VARCHAR(100) NOT NULL,
    description TEXT
);

CREATE TABLE IF NOT EXISTS classes (
    class_id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    class_code VARCHAR(20) NOT NULL,
    schedule VARCHAR(100),
    instructor VARCHAR(100),
    slots INT NOT NULL DEFAULT 0,
    FOREIGN KEY (course_id) REFERENCES courses (course_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS enrollments (
    enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    class_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'active',
    FOREIGN KEY (student_id) REFERENCES students (student_id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes (class_id) ON DELETE CASCADE
);

-- Seed Data for testing
INSERT INTO courses (course_code, course_name, description) VALUES
('CS101', 'Web Development Fundamentals', 'Introduction to HTML, CSS, JavaScript, and PHP development.'),
('CS102', 'Database Management Systems', 'Relational database design, SQL queries, and PDO integration.'),
('CS103', 'Advanced PHP Architecture', 'Object-oriented programming, design patterns, and MVC architectures.')
ON DUPLICATE KEY UPDATE course_code=course_code;

INSERT INTO classes (course_id, class_code, schedule, instructor, slots) VALUES
(1, 'WD-SEC-A', 'Mon/Wed 9:00 AM - 12:00 PM', 'Prof. John Doe', 15),
(1, 'WD-SEC-B', 'Tue/Thu 1:00 PM - 4:00 PM', 'Prof. Jane Smith', 10),
(2, 'DB-SEC-A', 'Mon/Wed 1:00 PM - 4:00 PM', 'Dr. Alan Turing', 20),
(3, 'PHP-SEC-A', 'Friday 8:00 AM - 5:00 PM', 'Prof. Grace Hopper', 5);
