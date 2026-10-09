<?php
// admin/enroll.php - Enroll an EXISTING student into a class section
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';
require_once __DIR__ . '/../classes/Student.php';
require_once __DIR__ . '/../classes/ClassSection.php';

$repo = new EnrollmentRepository($db);
$studentModel = new Student($db);
$classModel = new ClassSection($db);

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)($_POST['student_id'] ?? 0);
    $class_id   = (int)($_POST['class_id'] ?? 0);

    if ($student_id <= 0 || $class_id <= 0) {
        $error = "Please select both a student and a class section.";
    } else {
        try {
            $result = $repo->enroll($student_id, $class_id);
            if ($result === true) {
                $message = "Student successfully enrolled into class section!";
            } elseif ($result === 'already_enrolled') {
                $error = "Student is already actively enrolled in this class section.";
            } else {
                $error = "No slots available for this class section.";
            }
        } catch (Exception $e) {
            $error = "Enrollment failed: " . $e->getMessage();
        }
    }
}

$students = $studentModel->all();
$classList = $classModel->allWithCourse();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Enroll Existing Student</h1>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card" style="max-width: 650px; margin: 0 auto;">
    <h2 class="card-title">Enroll Existing Student into Class</h2>
    <p style="color: var(--text-muted); margin-bottom: 1.25rem; font-size: 0.9rem;">
        Select an existing student from the database and assign them to an available class section.
    </p>

    <form method="POST" action="enroll.php">
        <div class="form-group">
            <label class="form-label" for="student_id">Select Existing Student *</label>
            <select id="student_id" name="student_id" class="form-control" required>
                <option value="">-- Choose Student --</option>
                <?php foreach ($students as $s): ?>
                    <option value="<?php echo $s['student_id']; ?>">
                        <?php echo htmlspecialchars($s['full_name']); ?> 
                        (<?php echo htmlspecialchars($s['email'] ?? 'No Email'); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="class_id">Select Class Section *</label>
            <select id="class_id" name="class_id" class="form-control" required>
                <option value="">-- Choose Class Section --</option>
                <?php foreach ($classList as $cls): ?>
                    <option value="<?php echo $cls['class_id']; ?>" <?php echo ($cls['slots'] <= 0) ? 'disabled' : ''; ?>>
                        [<?php echo htmlspecialchars($cls['class_code']); ?>] 
                        <?php echo htmlspecialchars($cls['course_name']); ?> 
                        (Slots left: <?php echo (int)$cls['slots']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">
            Process Enrollment
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>