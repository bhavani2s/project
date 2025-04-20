<?php

session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "sai";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST["student_id"];
    $password = $_POST["password"];

    $student_id = mysqli_real_escape_string($conn, $student_id);
    $password = mysqli_real_escape_string($conn, $password);

    $sql = "INSERT INTO students (student_id, password) VALUES ('$student_id', '$password')";

    if ($conn->query($sql) === TRUE) {
        $success_message = "Student added successfully.";
    } else {
        $error_message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
</head>
<body>
    <div class="navbar">
        <a href="admin_dashboard.php">Home</a>
        <a href="add_student.php">Add Student</a>
        <a href="add_teacher.php">Add Teacher</a>
        <a href="manage_admins.php">Admins</a>
    </div>

    <h2>Add Student</h2>

    <?php if (!empty($error_message)) { ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php } ?>

    <?php if (!empty($success_message)) { ?>
        <p style="color: green;"><?php echo $success_message; ?></p>
    <?php } ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="student_id">Student ID:</label>
        <input type="text" name="student_id" id="student_id" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br><br>

        <input type="submit" value="Add Student">
    </form>
</body>
</html>
