<?php
// index.php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/classes/EnrollmentRepository.php';
require_once __DIR__ . '/includes/header.php';

// Fetch quick statistics
$courseCount = $db->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$classCount  = $db->query("SELECT COUNT(*) FROM classes")->fetchColumn();
$studentCount = $db->query("SELECT COUNT(*) FROM students")->fetchColumn();
$enrollmentCount = $db->query("SELECT COUNT(*) FROM enrollments WHERE status = 'active'")->fetchColumn();

// Fetch recent 5 enrollments for preview
$repo = new EnrollmentRepository($db);
$allEnrollments = $repo->allWithDetails();
$recentEnrollments = array_slice($allEnrollments, 0, 5);
?>

<!-- Hero Banner (Light Mode with Red Accent) -->
<div class="hero-banner">
    <div class="hero-content">
        <span class="hero-badge">College of Computer Studies</span>
        <h1 class="hero-title">
            CCS Dragons <span>Training Enrollment System</span>
        </h1>
        <p class="hero-description">
            Multi-Table PHP PDO Application managing courses, scheduled class sections, participant registrations, and atomic database transactions.
        </p>
        <div class="hero-actions">
            <a href="admin/students.php" class="btn btn-primary">Record New Student</a>
            <a href="admin/enrollments.php" class="btn btn-outline">View Master Enrollments</a>
        </div>
    </div>
    <div class="hero-logo-wrapper">
        <img src="images/ccsdragons.png" alt="CCS Dragons Logo">
    </div>
</div>

<!-- Key Performance Stat Cards -->
<div class="grid-4" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-number"><?php echo (int)$studentCount; ?></div>
        <div class="stat-label">Registered Students</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" style="color: var(--primary-red);"><?php echo (int)$enrollmentCount; ?></div>
        <div class="stat-label">Active Enrollments</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo (int)$classCount; ?></div>
        <div class="stat-label">Class Sections</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo (int)$courseCount; ?></div>
        <div class="stat-label">Courses Offered</div>
    </div>
</div>

<!-- Main 2-Column Rearranged Layout -->
<div class="grid-2">
    <!-- Left Column: Primary Enrollment Actions -->
    <div>
        <div class="page-header" style="margin-bottom: 1.25rem;">
            <h2 class="page-title" style="font-size: 1.35rem;">Student Enrollment Operations</h2>
        </div>

        <div class="card card-accent-top">
            <h3 class="card-title card-title-red">Record & Enroll New Student</h3>
            <p style="color: var(--text-muted); font-size: 0.92rem; margin-bottom: 1.25rem;">
                Register a participant and allocate a class section slot in a single database transaction (`recordStudent`).
            </p>
            <a href="admin/students.php" class="btn btn-primary" style="width: 100%;">Record New Student</a>
        </div>

        <div class="card card-accent-top">
            <h3 class="card-title">Enroll Existing Student</h3>
            <p style="color: var(--text-muted); font-size: 0.92rem; margin-bottom: 1.25rem;">
                Select a previously registered student and assign them to an available open class section (`enroll`).
            </p>
            <a href="admin/enroll.php" class="btn btn-outline" style="width: 100%;">Enroll Existing Student</a>
        </div>

        <div class="card card-accent-top">
            <h3 class="card-title">Master Enrollment Records</h3>
            <p style="color: var(--text-muted); font-size: 0.92rem; margin-bottom: 1.25rem;">
                View active and cancelled enrollments with multi-table SQL JOINs and process cancellations.
            </p>
            <a href="admin/enrollments.php" class="btn btn-outline" style="width: 100%;">View All Enrollments</a>
        </div>
    </div>

    <!-- Right Column: Academic Catalog & System Administration -->
    <div>
        <div class="page-header" style="margin-bottom: 1.25rem;">
            <h2 class="page-title" style="font-size: 1.35rem;">Catalog & Administration</h2>
        </div>

        <div class="card card-accent-top">
            <h3 class="card-title">Course Management</h3>
            <p style="color: var(--text-muted); font-size: 0.92rem; margin-bottom: 1.25rem;">
                Add, edit, or remove training program courses from the academic catalog.
            </p>
            <a href="admin/courses.php" class="btn btn-outline" style="width: 100%;">Manage Courses</a>
        </div>

        <div class="card card-accent-top">
            <h3 class="card-title">Class Section Schedules</h3>
            <p style="color: var(--text-muted); font-size: 0.92rem; margin-bottom: 1.25rem;">
                Set class schedules, assign instructors, and set maximum slot capacities per section.
            </p>
            <a href="admin/classes.php" class="btn btn-outline" style="width: 100%;">Manage Class Sections</a>
        </div>

        <div class="card card-accent-top">
            <h3 class="card-title">Reports & Capacity Analytics</h3>
            <p style="color: var(--text-muted); font-size: 0.92rem; margin-bottom: 1.25rem;">
                Inspect slot allocation, occupancy rates, and enrollment summary statistics.
            </p>
            <a href="admin/reports.php" class="btn btn-outline" style="width: 100%;">View Summary Reports</a>
        </div>
    </div>
</div>

<!-- Live Enrollment Activity Preview -->
<div class="card" style="margin-top: 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3 class="card-title" style="margin-bottom: 0;">Recent Enrollment Activity</h3>
        <a href="admin/enrollments.php" class="btn btn-sm btn-outline">View Master List &rarr;</a>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Course Code</th>
                    <th>Class Section</th>
                    <th>Enrollment Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentEnrollments)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">
                            No recent enrollments recorded. Click "Record New Student" to get started.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentEnrollments as $row): ?>
                        <tr>
                            <td>#<?php echo (int)$row['enrollment_id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['full_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['course_code']); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['class_code']); ?></strong></td>
                            <td><?php echo date('M d, Y', strtotime($row['enrollment_date'])); ?></td>
                            <td>
                                <span class="badge badge-<?php echo ($row['status'] === 'active') ? 'active' : 'cancelled'; ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>