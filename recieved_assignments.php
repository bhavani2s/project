<?php
session_start();

if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    header("Location: student_login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "sai";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT a.assignment_id, a.question, a.teacher_id, t.teacher_name, a.deadline
        FROM assignments a
        JOIN teachers t ON a.teacher_id = t.teacher_id
        WHERE a.student_id = '$student_id'
        ORDER BY a.sent_at DESC";
$result = $conn->query($sql);

$assignments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $assignments[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Received Assignments</title>
    <style>
        .assignment-item {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            display: flex; /* Use flexbox for layout */
            justify-content: space-between; /* Push content to the edges */
            align-items: flex-start; /* Align items to the start (top) */
            flex-wrap: wrap; /* Allow items to wrap on smaller screens */
        }

        .assignment-content { /* New container for question and teacher info */
            flex: 1; /* Take up available space */
            margin-right: 20px; /* Add some margin to separate from the button */
            min-width: 200px; /* Ensure content doesn't get too narrow */
        }

        .teacher-info {
            font-size: small;
            color: #777;
            margin-bottom: 5px;
        }

        .deadline {
            font-size: small;
            color: #777;
            margin-bottom: 5px;
        }

        .write-assignment-button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            align-self: flex-start; /* Align button to the start (top) */
        }

        .write-assignment-button:hover {
            background-color: #0056b3;
        }

        /* Responsive adjustments (optional - adjust as needed) */
        @media (max-width: 768px) {
            .assignment-item {
                flex-direction: column; /* Stack items vertically on small screens */
            }
            .assignment-content {
                margin-right: 0; /* Remove margin on small screens */
                margin-bottom: 10px; /* Add margin below content on small screens */
            }
            .write-assignment-button {
                align-self: stretch; /* Make button full width on small screens if desired */
            }
        }
    </style>
</head>
<body>
    <h2>Received Assignment Questions</h2>

    <?php if (empty($assignments)): ?>
        <p>No assignments received yet.</p>
    <?php else: ?>
        <?php foreach ($assignments as $assignment): ?>
            <div class="assignment-item">
                <div class="assignment-content">
                    <div class="teacher-info">
                        Sent by Teacher: <?php echo $assignment['teacher_name']; ?> (ID: <?php echo $assignment['teacher_id']; ?>)
                    </div>
                    <p><?php echo $assignment['question']; ?></p>
                    <?php if ($assignment['deadline'] != null && $assignment['deadline'] != ""): ?>
                        <div class="deadline">
                            Deadline: <?php echo $assignment['deadline']; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <a href="write_assignment.php?assignment_id=<?php echo $assignment['assignment_id']; ?>" class="write-assignment-button">Write Assignment</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <p><a href="student_dashboard.php">Back to Dashboard</a></p>
</body>
</html>

