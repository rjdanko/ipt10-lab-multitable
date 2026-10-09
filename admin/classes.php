<?php
// admin/classes.php - Manage Class Sections & Slots
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/ClassSection.php';
require_once __DIR__ . '/../classes/Course.php';

$classModel = new ClassSection($db);
$courseModel = new Course($db);

$message = '';
$error = '';
$editClass = null;

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action     = $_POST['action'] ?? '';
    $course_id  = (int)($_POST['course_id'] ?? 0);
    $class_code = trim($_POST['class_code'] ?? '');
    $schedule   = trim($_POST['schedule'] ?? '');
    $instructor = trim($_POST['instructor'] ?? '');
    $slots      = (int)($_POST['slots'] ?? 0);
    $class_id   = (int)($_POST['class_id'] ?? 0);

    if ($action === 'create') {
        if ($course_id > 0 && !empty($class_code) && $slots >= 0) {
            try {
                $classModel->create($course_id, $class_code, $schedule, $instructor, $slots);
                $message = "Class section '$class_code' created successfully with $slots available slots!";
            } catch (Exception $e) {
                $error = "Error creating class section: " . $e->getMessage();
            }
        } else {
            $error = "Please select a course, provide a class code, and enter valid slot count.";
        }
    } elseif ($action === 'update') {
        if ($class_id > 0 && $course_id > 0 && !empty($class_code) && $slots >= 0) {
            try {
                $classModel->update($class_id, $course_id, $class_code, $schedule, $instructor, $slots);
                $message = "Class section updated successfully!";
            } catch (Exception $e) {
                $error = "Error updating class section: " . $e->getMessage();
            }
        } else {
            $error = "Invalid class section data.";
        }
    }
}

// Handle GET Actions
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    try {
        $classModel->delete($delete_id);
        $message = "Class section deleted successfully!";
    } catch (Exception $e) {
        $error = "Cannot delete class section: " . $e->getMessage();
    }
}

if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $editClass = $classModel->find($edit_id);
}

$classList = $classModel->allWithCourse();
$courseList = $courseModel->all();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Manage Class Sections</h1>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="grid-2">
    <!-- Class Form -->
    <div class="card">
        <h2 class="card-title"><?php echo $editClass ? 'Edit Class Section' : 'Create Class Section'; ?></h2>
        <form method="POST" action="classes.php">
            <input type="hidden" name="action" value="<?php echo $editClass ? 'update' : 'create'; ?>">
            <?php if ($editClass): ?>
                <input type="hidden" name="class_id" value="<?php echo (int)$editClass['class_id']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label class="form-label" for="course_id">Target Course</label>
                <select id="course_id" name="course_id" class="form-control" required>
                    <option value="">-- Select Course --</option>
                    <?php foreach ($courseList as $co): ?>
                        <option value="<?php echo $co['course_id']; ?>" 
                            <?php echo ($editClass && $editClass['course_id'] == $co['course_id']) ? 'selected' : ''; ?>>
                            [<?php echo htmlspecialchars($co['course_code']); ?>] <?php echo htmlspecialchars($co['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="class_code">Class Section Code</label>
                <input type="text" id="class_code" name="class_code" class="form-control" 
                       value="<?php echo htmlspecialchars($editClass['class_code'] ?? ''); ?>" 
                       placeholder="e.g. WD-SEC-A" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="schedule">Schedule</label>
                <input type="text" id="schedule" name="schedule" class="form-control" 
                       value="<?php echo htmlspecialchars($editClass['schedule'] ?? ''); ?>" 
                       placeholder="e.g. Mon/Wed 9:00 AM - 12:00 PM">
            </div>

            <div class="form-group">
                <label class="form-label" for="instructor">Instructor</label>
                <input type="text" id="instructor" name="instructor" class="form-control" 
                       value="<?php echo htmlspecialchars($editClass['instructor'] ?? ''); ?>" 
                       placeholder="e.g. Prof. John Doe">
            </div>

            <div class="form-group">
                <label class="form-label" for="slots">Available Slots</label>
                <input type="number" id="slots" name="slots" class="form-control" min="0" 
                       value="<?php echo htmlspecialchars($editClass['slots'] ?? '15'); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">
                <?php echo $editClass ? 'Save Changes' : 'Create Class Section'; ?>
            </button>
            <?php if ($editClass): ?>
                <a href="classes.php" class="btn btn-outline">Cancel</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Class List -->
    <div class="card">
        <h2 class="card-title">Class Offerings (<?php echo count($classList); ?>)</h2>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Class Section</th>
                        <th>Course</th>
                        <th>Schedule & Instructor</th>
                        <th>Slots</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($classList)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted);">No class sections scheduled. Create one first!</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($classList as $cls): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($cls['class_code']); ?></strong></td>
                                <td>
                                    <div><?php echo htmlspecialchars($cls['course_name']); ?></div>
                                    <small style="color: var(--text-muted);"><?php echo htmlspecialchars($cls['course_code']); ?></small>
                                </td>
                                <td>
                                    <div><?php echo htmlspecialchars($cls['schedule'] ?? 'TBA'); ?></div>
                                    <small style="color: var(--text-muted);"><?php echo htmlspecialchars($cls['instructor'] ?? 'Unassigned'); ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-slot <?php echo ($cls['slots'] <= 0) ? 'zero' : ''; ?>">
                                        <?php echo (int)$cls['slots']; ?> remaining
                                    </span>
                                </td>
                                <td>
                                    <a href="classes.php?edit=<?php echo $cls['class_id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                                    <a href="classes.php?delete=<?php echo $cls['class_id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Delete this class section?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
