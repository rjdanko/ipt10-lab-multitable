<?php
// admin/enrollments.php - View and Manage System Enrollments
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';

$repo = new EnrollmentRepository($db);
$message = '';
$error = '';

// Handle Cancellation
if (isset($_GET['cancel_id'])) {
    $cancel_id = (int)$_GET['cancel_id'];
    try {
        if ($repo->cancel($cancel_id)) {
            $message = "Enrollment #$cancel_id has been cancelled and 1 slot has been restored to the class section!";
        } else {
            $error = "Unable to cancel enrollment #$cancel_id (it may already be cancelled).";
        }
    } catch (Exception $e) {
        $error = "Error cancelling enrollment: " . $e->getMessage();
    }
}

// Fetch all enrollments with class and course details using multi-table JOINs
$rows = $repo->allWithDetails();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Enrollment Master List</h1>
    <a href="students.php" class="btn btn-primary">+ Record New Student</a>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card">
    <h2 class="card-title">All Enrollments (Multi-Table SQL JOIN Query)</h2>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Class Section</th>
                    <th>Schedule</th>
                    <th>Enrollment Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                            No enrollment records found. Record a student or enroll an existing student to get started!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td>#<?php echo (int)$row['enrollment_id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['full_name']); ?></strong><br>
                                <small style="color: var(--text-muted);"><?php echo htmlspecialchars($row['email'] ?? 'No Email'); ?></small>
                            </td>
                            <td>
                                <div><?php echo htmlspecialchars($row['course_name']); ?></div>
                                <small style="color: var(--text-muted);"><?php echo htmlspecialchars($row['course_code']); ?></small>
                            </td>
                            <td><strong><?php echo htmlspecialchars($row['class_code']); ?></strong></td>
                            <td>
                                <div><?php echo htmlspecialchars($row['schedule'] ?? 'TBA'); ?></div>
                                <small style="color: var(--text-muted);"><?php echo htmlspecialchars($row['instructor'] ?? ''); ?></small>
                            </td>
                            <td><?php echo date('M d, Y h:i A', strtotime($row['enrollment_date'])); ?></td>
                            <td>
                                <span class="badge badge-<?php echo ($row['status'] === 'active') ? 'active' : 'cancelled'; ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['status'] === 'active'): ?>
                                    <a href="enrollments.php?cancel_id=<?php echo $row['enrollment_id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to cancel enrollment #<?php echo $row['enrollment_id']; ?>? This will restore 1 available slot to the class.');">
                                        Cancel
                                    </a>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.85rem;">Cancelled</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>