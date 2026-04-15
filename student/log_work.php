<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../db.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: ../student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $work_date = $_POST['work_date'];
    $hours = $_POST['hours'];
    $description = $_POST['description'];

    // For now we assume supervisor_id = 1
    // Later we make proper assignment system
    $supervisor_id = 1;

    $stmt = $conn->prepare("INSERT INTO work_logs (student_id, supervisor_id, work_date, hours_worked, description) VALUES (?, ?, ?, ?, ?)");
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
body { font-family: Arial; background:#0f172a; color:white; padding:40px; }
.card { background:#1e293b; padding:20px; border-radius:10px; width:400px; }
input, textarea { width:100%; padding:10px; margin-bottom:10px; border-radius:5px; border:none; }
button { padding:10px; background:#38bdf8; border:none; border-radius:5px; cursor:pointer; }
button:hover { background:#0ea5e9; }
a { color:#38bdf8; text-decoration:none; }
</style>
</head>
<body>

<a href="dashboard.php">← Back to Dashboard</a>

<div class="card">
<h2>Log Work Hours</h2>

<?php if (!empty($success)) echo "<p style='color:lightgreen;'>$success</p>"; ?>

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