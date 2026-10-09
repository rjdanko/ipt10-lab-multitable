<?php
// includes/header.php
$base_url = (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false) ? '../' : './';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Dragons | Training Enrollment System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Cinzel:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/style.css">
</head>
<body>
<header class="app-header">
    <div class="header-container">
        <a href="<?php echo $base_url; ?>index.php" class="brand-logo">
            <img src="<?php echo $base_url; ?>images/ccsdragons.png" alt="CCS Dragons Logo" class="dragon-logo-img">
            <div class="brand-text-container">
                <span class="logo-title">CCS DRAGONS</span>
                <span class="logo-subtitle">Training Enrollment System</span>
            </div>
        </a>
        <nav class="main-nav">
            <a href="<?php echo $base_url; ?>index.php" class="nav-link">Home</a>
            <a href="<?php echo $base_url; ?>admin/courses.php" class="nav-link">Courses</a>
            <a href="<?php echo $base_url; ?>admin/classes.php" class="nav-link">Classes</a>
            <a href="<?php echo $base_url; ?>admin/students.php" class="nav-link">Record Student</a>
            <a href="<?php echo $base_url; ?>admin/enroll.php" class="nav-link">Enroll</a>
            <a href="<?php echo $base_url; ?>admin/enrollments.php" class="nav-link">Enrollments</a>
            <a href="<?php echo $base_url; ?>admin/reports.php" class="nav-link">Reports</a>
        </nav>
    </div>
</header>
<main class="main-content">