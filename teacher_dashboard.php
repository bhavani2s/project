<?php
// teacher_dashboard.php
session_start();

if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    header("Location: teacher_login.php");
    exit;
}

$teacher_id = $_SESSION['teacher_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Teacher Dashboard</title>
</head>
<body>
    <h2>Teacher Dashboard</h2>
    <p>Welcome, Teacher ID: <?php echo $teacher_id; ?>!</p>
    <a href="teacher_logout.php">Logout</a>
</body>
</html>
