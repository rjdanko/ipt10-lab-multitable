<?php
// classes/ClassSection.php
class ClassSection
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // JOIN classes with courses so each row shows the course name
    public function allWithCourse()
    {
        $sql = "SELECT c.*, co.course_code, co.course_name 
                FROM classes c
                JOIN courses co ON c.course_id = co.course_id
                ORDER BY co.course_name ASC, c.class_code ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $sql = "SELECT c.*, co.course_code, co.course_name 
                FROM classes c
                JOIN courses co ON c.course_id = co.course_id
                WHERE c.class_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => (int)$id]);
        return $stmt->fetch();
    }

    public function create($course_id, $code, $schedule, $instructor, $slots)
    {
        $sql = "INSERT INTO classes (course_id, class_code, schedule, instructor, slots) 
                VALUES (:course_id, :code, :schedule, :instructor, :slots)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':course_id'  => (int)$course_id,
            ':code'       => $code,
            ':schedule'   => $schedule,
            ':instructor' => $instructor,
            ':slots'      => (int)$slots
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $course_id, $code, $schedule, $instructor, $slots)
    {
        $sql = "UPDATE classes 
                SET course_id = :course_id, class_code = :code, schedule = :schedule, 
                    instructor = :instructor, slots = :slots 
                WHERE class_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'         => (int)$id,
            ':course_id'  => (int)$course_id,
            ':code'       => $code,
            ':schedule'   => $schedule,
            ':instructor' => $instructor,
            ':slots'      => (int)$slots
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM classes WHERE class_id = :id");
        return $stmt->execute([':id' => (int)$id]);
    }

    public function getSlots($class_id)
    {
        $stmt = $this->db->prepare("SELECT slots FROM classes WHERE class_id = :id");
        $stmt->execute([':id' => (int)$class_id]);
        $row = $stmt->fetch();
        return $row ? (int)$row['slots'] : 0;
    }
}
