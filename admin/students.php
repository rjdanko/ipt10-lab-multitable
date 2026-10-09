<?php
// admin/students.php - Student recording form (Transactional Multi-table Form)
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';
require_once __DIR__ . '/../classes/ClassSection.php';

$repo = new EnrollmentRepository($db);
$classes = new ClassSection($db);

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $class_id  = (int)($_POST['class_id'] ?? 0);

    // Validate inputs
    if (empty($full_name)) {
        $error = "Student full name is required.";
    } elseif ($class_id <= 0) {
        $error = "Please select a valid class section.";
    } else {
        try {
            $result = $repo->recordStudent($full_name, $email, $phone, $class_id);
            if ($result === true) {
                $message = "Student '$full_name' successfully registered and enrolled into the class section!";
            } else {
                $error = "No slots available for the selected class section! Transaction rolled back.";
            }
        } catch (Exception $e) {
            $error = "Transaction failed: " . $e->getMessage();
        }
    }
}

$classList = $classes->allWithCourse(); // for the class dropdown

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Record Student & Enroll</h1>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card" style="max-width: 650px; margin: 0 auto;">
    <h2 class="card-title">Multi-Table Student Enrollment Form</h2>
    <p style="color: var(--text-muted); margin-bottom: 1.25rem; font-size: 0.9rem;">
        Submitting this form executes a single database transaction that inserts the student record, creates an active enrollment, and decrements available class slots.
    </p>

    <form method="POST" action="students.php">
        <div class="form-group">
            <label class="form-label" for="full_name">Full Name *</label>
            <input type="text" id="full_name" name="full_name" class="form-control" 
                   placeholder="e.g. Maria Clara Santos" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" 
                   placeholder="e.g. maria.clara@example.com">
        </div>

        <div class="form-group">
            <label class="form-label" for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" class="form-control" 
                   placeholder="e.g. 0917-123-4567">
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
            Record Student & Process Enrollment
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>