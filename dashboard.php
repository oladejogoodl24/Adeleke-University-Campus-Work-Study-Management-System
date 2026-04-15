<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../db.php");

// Check if logged in
if (!isset($_SESSION['management_id'])) {
    header("Location: ../supervisor_login.php");
    exit();
}

$name = $_SESSION['management_name'];
$role = $_SESSION['management_role'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Management Dashboard</title>
<style>
body { margin:0; font-family: Arial,sans-serif; background: #0f172a; color: white; display: flex; }

/* SIDEBAR */
.sidebar {
    width: 250px;
    background: #1e293b;
    height: 100vh;
    padding: 20px;
}
.sidebar h2 { color: #38bdf8; text-align:center; }
.sidebar a { display:block; color:white; text-decoration:none; padding:12px; margin:10px 0; border-radius:6px; background:#334155; }
.sidebar a:hover { background:#475569; }

/* MAIN CONTENT */
.main { flex:1; padding:30px; }
.topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
.role-badge { padding:6px 12px; border-radius:20px; font-size:12px; background:#38bdf8; color:black; font-weight:bold; }

.card { background:#1e293b; padding:20px; border-radius:10px; margin-bottom:20px; }
.card h3 { margin-top:0; color:#38bdf8; }
button { padding:6px 12px; background:#38bdf8; border:none; border-radius:5px; cursor:pointer; font-weight:bold; }
button:hover { background:#0ea5e9; }
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Work-Study</h2>
    <a href="#">Dashboard</a>

    <?php if ($role == 'admin') { ?>
        <a href="#">Approve Students</a>
        <a href="#">Approve Supervisors</a>
        <a href="#">View All Work Logs</a>
    <?php } else { ?>
        <a href="#">My Students</a>
        <a href="#">Approve Work Logs</a>
    <?php } ?>

    <a href="../logout.php">Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">

    <div class="topbar">
        <h2>Welcome, <?php echo $name; ?></h2>
        <span class="role-badge"><?php echo strtoupper($role); ?></span>
    </div>

<?php if ($role == 'admin') { ?>

    <!-- PENDING STUDENTS -->
    <div class="card">
        <h3>Pending Student Registrations</h3>
        <?php
        $students = $conn->query("SELECT * FROM students WHERE status='pending'");
        if ($students->num_rows > 0) {
            while ($row = $students->fetch_assoc()) {
                echo "<p>{$row['full_name']} ({$row['email']}) <a href='approve_student.php?id={$row['id']}'><button>Approve</button></a></p>";
            }
        } else { echo "<p>No pending students.</p>"; }
        ?>
    </div>

    <!-- PENDING SUPERVISORS -->
    <div class="card">
        <h3>Pending Supervisor Registrations</h3>
        <?php
        $supervisors = $conn->query("SELECT * FROM supervisors WHERE status='pending'");
        if ($supervisors->num_rows > 0) {
            while ($row = $supervisors->fetch_assoc()) {
                echo "<p>{$row['full_name']} ({$row['staff_id']}) <a href='approve_supervisor.php?id={$row['id']}'><button>Approve</button></a></p>";
            }
        } else { echo "<p>No pending supervisors.</p>"; }
        ?>
    </div>

<?php } else { // SUPERVISOR VIEW ?>

    <!-- PENDING WORK LOGS FOR SUPERVISOR'S STUDENTS -->
    <div class="card">
        <h3>Pending Work Logs</h3>
        <?php
        $sup_id = $_SESSION['management_id'];
        $logs = $conn->query("
            SELECT wl.id, s.full_name, wl.work_date, wl.hours_worked, wl.description 
            FROM work_logs wl
            JOIN students s ON wl.student_id = s.id
            WHERE wl.supervisor_id = $sup_id AND wl.status='pending'
        ");
        if ($logs->num_rows > 0) {
            while ($row = $logs->fetch_assoc()) {
                echo "<p>
                        {$row['full_name']} | Date: {$row['work_date']} | Hours: {$row['hours_worked']}
                        <br>{$row['description']}
                        <br><a href='approve_worklog.php?id={$row['id']}'><button>Approve</button></a>
                      </p><hr>";
            }
        } else { echo "<p>No pending work logs.</p>"; }
        ?>
    </div>

<?php } ?>

</div>
</body>
</html>