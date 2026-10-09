<?php
// admin/courses.php - Manage Training Courses
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/Course.php';

$courseModel = new Course($db);
$message = '';
$error = '';

$editCourse = null;

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $course_code = trim($_POST['course_code'] ?? '');
    $course_name = trim($_POST['course_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $course_id   = (int)($_POST['course_id'] ?? 0);

    if ($action === 'create') {
        if (!empty($course_code) && !empty($course_name)) {
            try {
                $courseModel->create($course_code, $course_name, $description);
                $message = "Course '$course_name' ($course_code) successfully added!";
            } catch (Exception $e) {
                $error = "Error adding course: " . $e->getMessage();
            }
        } else {
            $error = "Course Code and Course Name are required.";
        }
    } elseif ($action === 'update') {
        if ($course_id > 0 && !empty($course_code) && !empty($course_name)) {
            try {
                $courseModel->update($course_id, $course_code, $course_name, $description);
                $message = "Course updated successfully!";
            } catch (Exception $e) {
                $error = "Error updating course: " . $e->getMessage();
            }
        } else {
            $error = "Invalid course data.";
        }
    }
}

// Handle GET Delete & Edit
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    try {
        $courseModel->delete($delete_id);
        $message = "Course successfully deleted!";
    } catch (Exception $e) {
        $error = "Cannot delete course: " . $e->getMessage();
    }
}

if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $editCourse = $courseModel->find($edit_id);
}

$courses = $courseModel->all();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Manage Training Courses</h1>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="grid-2">
    <!-- Course Form -->
    <div class="card">
        <h2 class="card-title"><?php echo $editCourse ? 'Edit Course' : 'Add New Course'; ?></h2>
        <form method="POST" action="courses.php">
            <input type="hidden" name="action" value="<?php echo $editCourse ? 'update' : 'create'; ?>">
            <?php if ($editCourse): ?>
                <input type="hidden" name="course_id" value="<?php echo (int)$editCourse['course_id']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label class="form-label" for="course_code">Course Code</label>
                <input type="text" id="course_code" name="course_code" class="form-control" 
                       value="<?php echo htmlspecialchars($editCourse['course_code'] ?? ''); ?>" 
                       placeholder="e.g. CS101" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="course_name">Course Name</label>
                <input type="text" id="course_name" name="course_name" class="form-control" 
                       value="<?php echo htmlspecialchars($editCourse['course_name'] ?? ''); ?>" 
                       placeholder="e.g. Web Development Fundamentals" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" class="form-control" 
                          placeholder="Brief description of course offerings..."><?php echo htmlspecialchars($editCourse['description'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                <?php echo $editCourse ? 'Save Changes' : 'Add Course'; ?>
            </button>
            <?php if ($editCourse): ?>
                <a href="courses.php" class="btn btn-outline">Cancel</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Course List -->
    <div class="card">
        <h2 class="card-title">Existing Courses (<?php echo count($courses); ?>)</h2>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Course Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($courses)): ?>
                        <tr>
                            <td colspan="3" style="text-align: center; color: var(--text-muted);">No courses found. Add your first course!</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($courses as $c): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($c['course_code']); ?></strong></td>
                                <td>
                                    <div><?php echo htmlspecialchars($c['course_name']); ?></div>
                                    <small style="color: var(--text-muted);"><?php echo htmlspecialchars($c['description'] ?? ''); ?></small>
                                </td>
                                <td>
                                    <a href="courses.php?edit=<?php echo $c['course_id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                                    <a href="courses.php?delete=<?php echo $c['course_id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this course? Associated classes may be affected.');">Delete</a>
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
