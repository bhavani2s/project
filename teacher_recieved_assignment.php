<?php
session_start();

if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    header("Location: teacher_login.php");
    exit;
}

$teacher_id = $_SESSION['teacher_id'];

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "sai";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT sa.student_id, s.student_name, sa.submission_time, sa.submission_date, sa.submission_content
        FROM submitted_assignments sa
        JOIN students s ON sa.student_id = s.student_id
        WHERE sa.teacher_id = ?
        ORDER BY sa.submission_time DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $teacher_id);  
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Received Assignments</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { margin-bottom: 20px; }
        .assignment-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .assignment-info {
            margin-bottom: 10px;
            font-size: 0.9em;
            color: #555;
        }
        .assignment-content {
            white-space: pre-line; 
        }
    </style>
</head>
<body>
    <h1>Received Assignments</h1>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="assignment-item">
                <div class="assignment-info">
                    Sent by Student: <?php echo htmlspecialchars($row['student_name']); ?> (ID: <?php echo htmlspecialchars($row['student_id']); ?>)
                    on <?php echo htmlspecialchars($row['submission_date']); ?>
                </div>
                <div class="assignment-content">
                    <?php
                        $content = $row['submission_content'];
                        $content = str_replace(array('\r\n', '\r', '\n'), '', $content);
                        echo strip_tags(trim($content));
                    ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No assignments received yet.</p>
    <?php endif; ?>

    <p><a href="teacher_dashboard.php">Back to Dashboard</a></p>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
