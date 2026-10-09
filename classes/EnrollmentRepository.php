<?php
// classes/EnrollmentRepository.php
// Data-access class that records students, enrollments and slot updates
// inside a single database transaction.
class EnrollmentRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Record a NEW student AND enroll them into a class.
    public function recordStudent($full_name, $email, $phone, $class_id)
    {
        try {
            $this->db->beginTransaction();

            // 1. Check that the class exists and still has available slots
            $stmt = $this->db->prepare("SELECT slots FROM classes WHERE class_id = :class_id FOR UPDATE");
            $stmt->execute([':class_id' => (int)$class_id]);
            $class = $stmt->fetch();

            if (!$class || (int)$class['slots'] <= 0) {
                $this->db->rollBack();
                return false; // No slots available
            }

            // 2. INSERT INTO students (full_name, email, phone)
            $stmt = $this->db->prepare("INSERT INTO students (full_name, email, phone) VALUES (:name, :email, :phone)");
            $stmt->execute([
                ':name'  => $full_name,
                ':email' => $email,
                ':phone' => $phone
            ]);
            $student_id = $this->db->lastInsertId();

            // 3. INSERT INTO enrollments (student_id, class_id)
            $stmt = $this->db->prepare("INSERT INTO enrollments (student_id, class_id, status) VALUES (:student_id, :class_id, 'active')");
            $stmt->execute([
                ':student_id' => $student_id,
                ':class_id'   => (int)$class_id
            ]);

            // 4. UPDATE classes SET slots = slots - 1
            $stmt = $this->db->prepare("UPDATE classes SET slots = slots - 1 WHERE class_id = :class_id");
            $stmt->execute([':class_id' => (int)$class_id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    // Enroll an EXISTING student into a class.
    public function enroll($student_id, $class_id)
    {
        try {
            $this->db->beginTransaction();

            // 1. Check class slots
            $stmt = $this->db->prepare("SELECT slots FROM classes WHERE class_id = :class_id FOR UPDATE");
            $stmt->execute([':class_id' => (int)$class_id]);
            $class = $stmt->fetch();

            if (!$class || (int)$class['slots'] <= 0) {
                $this->db->rollBack();
                return false; // No slots left
            }

            // Check if student is already actively enrolled in this class
            $stmt = $this->db->prepare("SELECT enrollment_id FROM enrollments WHERE student_id = :student_id AND class_id = :class_id AND status = 'active'");
            $stmt->execute([
                ':student_id' => (int)$student_id,
                ':class_id'   => (int)$class_id
            ]);
            if ($stmt->fetch()) {
                $this->db->rollBack();
                return "already_enrolled";
            }

            // 2. INSERT INTO enrollments (student_id, class_id)
            $stmt = $this->db->prepare("INSERT INTO enrollments (student_id, class_id, status) VALUES (:student_id, :class_id, 'active')");
            $stmt->execute([
                ':student_id' => (int)$student_id,
                ':class_id'   => (int)$class_id
            ]);

            // 3. UPDATE classes SET slots = slots - 1
            $stmt = $this->db->prepare("UPDATE classes SET slots = slots - 1 WHERE class_id = :class_id");
            $stmt->execute([':class_id' => (int)$class_id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function cancel($enrollment_id)
    {
        try {
            $this->db->beginTransaction();

            // 1. Check enrollment status
            $stmt = $this->db->prepare("SELECT class_id, status FROM enrollments WHERE enrollment_id = :id FOR UPDATE");
            $stmt->execute([':id' => (int)$enrollment_id]);
            $enrollment = $stmt->fetch();

            if (!$enrollment || $enrollment['status'] === 'cancelled') {
                $this->db->rollBack();
                return false;
            }

            // 2. UPDATE enrollments SET status = 'cancelled'
            $stmt = $this->db->prepare("UPDATE enrollments SET status = 'cancelled' WHERE enrollment_id = :id");
            $stmt->execute([':id' => (int)$enrollment_id]);

            // 3. UPDATE classes SET slots = slots + 1
            $stmt = $this->db->prepare("UPDATE classes SET slots = slots + 1 WHERE class_id = :class_id");
            $stmt->execute([':class_id' => (int)$enrollment['class_id']]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function allWithDetails()
    {
        $sql = "SELECT e.enrollment_id, e.enrollment_date, e.status,
                       s.student_id, s.full_name, s.email, s.phone,
                       c.class_id, c.class_code, c.schedule, c.instructor, c.slots,
                       co.course_id, co.course_code, co.course_name
                FROM enrollments e
                JOIN students s ON e.student_id = s.student_id
                JOIN classes c ON e.class_id = c.class_id
                JOIN courses co ON c.course_id = co.course_id
                ORDER BY e.enrollment_id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}