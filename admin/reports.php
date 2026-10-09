<?php
// admin/reports.php - Summary Reports & System Analytics
require_once __DIR__ . '/../config/db.php';

// Fetch summary metrics
$totalStudents = $db->query("SELECT COUNT(*) FROM students")->fetchColumn();
$activeEnrollments = $db->query("SELECT COUNT(*) FROM enrollments WHERE status = 'active'")->fetchColumn();
$cancelledEnrollments = $db->query("SELECT COUNT(*) FROM enrollments WHERE status = 'cancelled'")->fetchColumn();
$totalCourses = $db->query("SELECT COUNT(*) FROM courses")->fetchColumn();

// Class enrollment report query
$classReportSql = "
    SELECT c.class_id, c.class_code, c.schedule, c.instructor, c.slots,
           co.course_code, co.course_name,
           COUNT(e.enrollment_id) as total_enrolled,
           SUM(CASE WHEN e.status = 'active' THEN 1 ELSE 0 END) as active_count,
           SUM(CASE WHEN e.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count
    FROM classes c
    JOIN courses co ON c.course_id = co.course_id
    LEFT JOIN enrollments e ON c.class_id = e.class_id
    GROUP BY c.class_id
    ORDER BY co.course_name ASC, c.class_code ASC
";
$classReports = $db->query($classReportSql)->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Summary Reports & Analytics</h1>
</div>

<!-- Stat Cards Grid -->
<div class="grid-4" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-number"><?php echo (int)$totalStudents; ?></div>
        <div class="stat-label">Total Registered Students</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" style="color: var(--success);"><?php echo (int)$activeEnrollments; ?></div>
        <div class="stat-label">Active Enrollments</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" style="color: var(--danger);"><?php echo (int)$cancelledEnrollments; ?></div>
        <div class="stat-label">Cancelled Enrollments</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" style="color: var(--warning);"><?php echo (int)$totalCourses; ?></div>
        <div class="stat-label">Active Courses</div>
    </div>
</div>

<div class="card">
    <h2 class="card-title">Class Section Slot & Enrollment Summary</h2>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Class Code</th>
                    <th>Course Name</th>
                    <th>Instructor</th>
                    <th>Active Enrollments</th>
                    <th>Cancelled</th>
                    <th>Remaining Slots</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($classReports)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted);">No class data available.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($classReports as $cr): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($cr['class_code']); ?></strong></td>
                            <td><?php echo htmlspecialchars($cr['course_name']); ?> (<?php echo htmlspecialchars($cr['course_code']); ?>)</td>
                            <td><?php echo htmlspecialchars($cr['instructor'] ?? 'TBA'); ?></td>
                            <td><strong style="color: var(--success);"><?php echo (int)$cr['active_count']; ?></strong></td>
                            <td><span style="color: var(--text-muted);"><?php echo (int)$cr['cancelled_count']; ?></span></td>
                            <td>
                                <span class="badge badge-slot <?php echo ($cr['slots'] <= 0) ? 'zero' : ''; ?>">
                                    <?php echo (int)$cr['slots']; ?> slots
                                </span>
                            </td>
                            <td>
                                <?php if ($cr['slots'] <= 0): ?>
                                    <span class="badge badge-cancelled">Full</span>
                                <?php else: ?>
                                    <span class="badge badge-active">Open</span>
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
