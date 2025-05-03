<?php
session_start();

if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    header("Location: student_login.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$student_course = '';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sai";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql_student = "SELECT course FROM students WHERE student_id = ?";
$stmt_student = $conn->prepare($sql_student);
$stmt_student->bind_param("i", $student_id);
$stmt_student->execute();
$result_student = $stmt_student->get_result();

if ($result_student->num_rows > 0) {
    $row_student = $result_student->fetch_assoc();
    $student_course = $row_student['course'];
}
$stmt_student->close();

$sql_teachers = "SELECT teacher_id, teacher_name FROM teachers WHERE department = ?";
$stmt_teachers = $conn->prepare($sql_teachers);
$stmt_teachers->bind_param("s", $student_course);
$stmt_teachers->execute();
$result_teachers = $stmt_teachers->get_result();
$teachers = [];
if ($result_teachers->num_rows > 0) {
    while ($row_teacher = $result_teachers->fetch_assoc()) {
        $teachers[] = $row_teacher;
    }
}
$stmt_teachers->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['selected_teachers']) && is_array($_POST['selected_teachers']) && isset($_POST['assignment_content'])) {
        $assignment_content = $_POST['assignment_content'];  
        $submission_date = date("Y-m-d");

        foreach ($_POST['selected_teachers'] as $teacher_id) {
            $teacher_id = mysqli_real_escape_string($conn, $teacher_id);
            $assignment_content = mysqli_real_escape_string($conn, $assignment_content);

            $sql = "INSERT INTO submitted_assignments (student_id, teacher_id, submission_content, submission_date)
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $student_id, $teacher_id, $assignment_content, $submission_date);

            if ($stmt->execute()) {
            } else {
                echo "Error: " . $stmt->error . "<br>";
            }
            $stmt->close();
        }

        $conn->close();
        echo "<div id='success-message'>Assignment Sent Successfully!</div>";
        echo "<script>
                    setTimeout(function() {
                        window.location.href = 'student_dashboard.php';
                    }, 2000);
                  </script>";
        exit;

    } else {
        echo "No teachers selected or assignment content missing.";
    }
} else {
    if (isset($conn)) {
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Teachers</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .teacher-list { margin-bottom: 20px; }
        .teacher-item { margin-bottom: 10px; }
        input[type="checkbox"] { margin-right: 5px; }
        button { padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #0056b3; }
        #success-message { margin-top: 20px; color: green; font-weight: bold; display: none; }
    </style>
</head>
<body>
    <h1>Select Teachers to Send Assignment</h1>
    <form id="teacher-selection-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <div class="teacher-list">
            <?php if (empty($teachers)): ?>
                <p>No teachers found for your course.</p>
            <?php else: ?>
                <?php foreach ($teachers as $teacher): ?>
                    <div class="teacher-item">
                        <input type="checkbox" id="teacher_<?php echo $teacher['teacher_id']; ?>" name="selected_teachers[]" value="<?php echo $teacher['teacher_id']; ?>">
                        <label for="teacher_<?php echo $teacher['teacher_id']; ?>"><?php echo $teacher['teacher_name']; ?></label>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <input type="hidden" id="assignment_content" name="assignment_content">  <button type="submit">Submit Assignment</button>
        <div id="success-message">Assignment Sent Successfully!</div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('teacher-selection-form');
            const assignmentContentInput = document.getElementById('assignment_content');

            const assignmentContent = localStorage.getItem('assignmentContent');
            if (assignmentContent) {
                assignmentContentInput.value = assignmentContent;  
            } else {
                alert("Assignment content not found. Please try again.");
                window.location.href = 'write_assignment.php'; 
                return;
            }

            form.addEventListener('submit', function(event) {
            });
        });
    </script>
</body>
</html>
