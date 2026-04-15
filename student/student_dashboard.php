<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../db.php");

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../student_login.php");
    exit();
}

$name = $_SESSION['student_name'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Dashboard</title>
<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #0b3d91; /* Deep Blue */
    color: white;
}

.topbar {
    background: #FFD700; /* Yellow */
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #0b3d91;
    font-weight: bold;
}

.container {
    padding: 40px;
}

.card {
    background: white;
    color: #0b3d91;
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 20px;
}

button {
    padding: 12px 20px;
    background: #FFD700;
    color: #0b3d91;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background: #ffc400;
}

a {
    text-decoration: none;
}
</style>
</head>
<body>

<div class="topbar">
    <div>Welcome, <?php echo $name; ?></div>
    <a href="../logout.php">Logout</a>
</div>

<div class="container">

    <div class="card">
        <h2>Student Work-Study Dashboard</h2>
        <p>Use the button below to log your daily work hours.</p>

        <!-- LOG WORK BUTTON -->
        <a href="log_work.php">
            <button>Log Work Hours</button>
        </a>
    </div>

    <div class="card">
        <h3>Your Submitted Work Logs</h3>

        <?php
        $student_id = $_SESSION['student_id'];

        $logs = $conn->query("SELECT work_date, hours_worked, status FROM work_logs WHERE student_id = $student_id ORDER BY work_date DESC");

        if ($logs->num_rows > 0) {
            while ($row = $logs->fetch_assoc()) {
                echo "<p>
                        Date: {$row['work_date']} |
                        Hours: {$row['hours_worked']} |
                        Status: {$row['status']}
                      </p>";
            }
        } else {
            echo "<p>No work logs submitted yet.</p>";
        }
        ?>
    </div>

</div>

</body>
</html>