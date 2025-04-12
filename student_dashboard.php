<?php
// student_dashboard.php
session_start();

if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    header("Location: student_login.php");
    exit;
}

$student_id = $_SESSION['student_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
</head>
<body>
    <h2>Student Dashboard</h2>
    <p>Welcome, Student ID: <?php echo $student_id; ?>!</p>
    <a href="student_logout.php">Logout</a>
</body>
</html>
