<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../db.php"); // go up one level

if (!isset($_SESSION['student_id'])) {
    header("Location: ../student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $work_date = $_POST['work_date'];
    $hours = $_POST['hours'];
    $description = $_POST['description'];

    // TEMP: supervisor_id = 1
    $supervisor_id = 1;

    $stmt = $conn->prepare("INSERT INTO work_logs 
        (student_id, supervisor_id, work_date, hours_worked, description) 
        VALUES (?, ?, ?, ?, ?)");

    $stmt->bind_param("iisds", $student_id, $supervisor_id, $work_date, $hours, $description);
    $stmt->execute();
    $stmt->close();

    $success = "Work log submitted successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Log Work</title>
<style>
body { font-family: Arial; background:#0b3d91; color:white; padding:40px; }
.card { background:white; color:#0b3d91; padding:20px; border-radius:10px; width:400px; }
input, textarea { width:100%; padding:10px; margin-bottom:10px; border-radius:5px; border:none; }
button { padding:10px; background:#FFD700; color:#0b3d91; border:none; border-radius:5px; cursor:pointer; }
button:hover { background:#ffc400; }
a { color:#FFD700; text-decoration:none; }
</style>
</head>
<body>

<a href="../student_dashboard.php">← Back to Dashboard</a>

<div class="card">
<h2>Log Work Hours</h2>

<?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>

<form method="POST">
    Date:
    <input type="date" name="work_date" required>

    Hours Worked:
    <input type="number" name="hours" min="1" required>

    Description:
    <textarea name="description" required></textarea>

    <button type="submit">Submit</button>
</form>
</div>

</body>
</html>