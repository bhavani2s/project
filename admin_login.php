<?php
// admin_dashboard.php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$admin_username = $_SESSION['admin_username'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Welcome, <?php echo $admin_username; ?>!</h2>
    <div class="navbar">
        <a href="admin_dashboard.php">Home</a>
        <a href="add_student.php">Add Student</a>
        <a href="add_teacher.php">Add Teacher</a>
        <a href="manage_admins.php">Admins</a>
    </div>

    <button>
        <a href="logout.php">Logout</a>
    </button>
</body>
</html>
