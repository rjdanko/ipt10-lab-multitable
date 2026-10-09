# Training Enrollment System (Multi-Table PHP PDO Application)
**Integrative Programming and Technology (IPT)**

A database-driven web application built with PHP and PDO featuring transactional integrity, multi-table JOINs, prepared statements, server-side validation, and software design patterns.

---

## Features

1. **Course Management**: Full CRUD operations for training program courses.
2. **Class Section Management**: Scheduling and slot tracking per course.
3. **Multi-Table Student Recording Form**: Atomic enrollment registration for new students using PDO database transactions (`recordStudent`).
4. **Existing Student Enrollment**: Transactional enrollment assignment with automatic slot decrementing and duplicate checks (`enroll`).
5. **Enrollment Master List**: Multi-table SQL `INNER` and `LEFT JOIN` view with status badges and cancellation support (`cancel`).
6. **Cancellation & Slot Restoration**: Atomic cancellation that updates enrollment status and restores class capacity (`+1 slot`).
7. **Reports & Analytics**: Real-time breakdown of student enrollments, slot availability, and capacity statistics.

---

## Design Patterns Explanation

### 1. Singleton Pattern (`classes/Database.php`)
* **Purpose**: Guarantees that only a single `Database` (extending `PDO`) instance exists throughout the application lifecycle.
* **Why it was used**: Reusing a single shared database connection avoids the overhead of opening multiple connections per request, prevents connection exhaustion, and ensures consistent transaction boundaries across classes.
* **Implementation**:
  ```php
  public static function getInstance($dsn, $user = null, $pass = null) {
      if (self::$instance == null) {
          self::$instance = new Database($dsn, $user, $pass);
      }
      return self::$instance;
  }
  ```

### 2. Repository Pattern (`classes/EnrollmentRepository.php`)  
* **Purpose**: Mediates between the domain entities and data mapping layers using a collection-like interface for accessing domain objects.
* **Why it was used**: Encapsulates complex multi-step database transactions (`beginTransaction`, slot checks, inserts, slot updates, `commit`/`rollBack`) away from presentation pages (`admin/*.php`).
* **Implementation**:
  - `recordStudent()`: Inserts a student, inserts an enrollment record, and decrements class slots inside a single transaction.
  - `enroll()`: Checks slot availability, inserts enrollment, and decrements slots inside a transaction.
  - `cancel()`: Marks enrollment as cancelled and restores class slot capacity inside a transaction.

---

## Database Schema & Setup

1. Make sure XAMPP (Apache and MySQL) is running.
2. Import the database schema and seed data into MySQL:
   ```bash
   mysql -u root < sql/schema.sql
   ```
3. Database Configuration (`config/db.php`):
   ```php
   $dsn  = "mysql:host=localhost;dbname=training_db;charset=utf8mb4";
   $user = "root";
   $pass = "";
   $db   = Database::getInstance($dsn, $user, $pass);
   ```

---

## Directory Structure

```text
training_enrollment/
├── config/
│   └── db.php                     # Single PDO connection instantiation
├── classes/
│   ├── Database.php               # PDO wrapper (Singleton pattern)
│   ├── Student.php                # Student data access
│   ├── Course.php                 # Course data access & CRUD
│   ├── ClassSection.php           # Class section data access & JOINs
│   ├── EnrollmentRepository.php   # Transactional repository (recordStudent, enroll, cancel)
│   └── Pet.php                    # Reference PDO CRUD example
├── admin/
│   ├── courses.php                # Course management page
│   ├── classes.php                # Class section management page
│   ├── students.php               # Student recording & enrollment form
│   ├── enroll.php                 # Existing student enrollment page
│   ├── enrollments.php            # Master enrollment table (JOINS)
│   └── reports.php                # Summary analytics page
├── includes/
│   ├── header.php                 # Shared header & navigation
│   └── footer.php                 # Shared footer
├── css/
│   └── style.css                  # UI styling
├── sql/
│   └── schema.sql                 # SQL database schema & seed data
├── index.php                      # Main dashboard landing page
└── README.md                      # Documentation & design pattern write-up
```
