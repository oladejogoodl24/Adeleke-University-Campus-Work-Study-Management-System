<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../db.php");

// If not logged in
if (!isset($_SESSION['management_id'])) {
    header("Location: ../supervisor_login.php");
    exit();
}

$supervisor_id = $_SESSION['management_id'];
$name = $_SESSION['management_name'];
$role = $_SESSION['management_role'];

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $log_id = $_POST['log_id'];
    $action = $_POST['action'];
    $rejection_reason = isset($_POST['rejection_reason']) ? trim($_POST['rejection_reason']) : null;

    if ($action == 'approve') {
        $status = 'approved';
        $message = "Work log approved successfully.";
        $update_query = "UPDATE work_logs SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $status, $log_id);
    } elseif ($action == 'reject') {
        $status = 'rejected';
        $message = "Work log rejected.";
        $update_query = "UPDATE work_logs SET status = ?, rejection_reason = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssi", $status, $rejection_reason, $log_id);
    } else {
        header("Location: approve_worklog.php?error=Invalid action");
        exit();
    }

    if ($stmt->execute()) {
        header("Location: approve_worklog.php?success=" . urlencode($message));
    } else {
        header("Location: approve_worklog.php?error=Database error");
    }
    exit();
}

// If no POST data, show the work logs page
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Work Logs - Management Dashboard</title>
<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Segoe UI, Arial, sans-serif;
    background: #D5E4EF;
    color: #1a1a1a;
}

/* SIDEBAR */
.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 250px;
    height: 100vh;
    background: #261661;
    padding: 20px;
    color: white;
    overflow-y: auto;
    transition: left 0.3s ease;
    z-index: 1000;
}

.sidebar.hide {
    left: -250px;
}

.sidebar h2 {
    color: #FFBF00;
    text-align: center;
    margin-bottom: 30px;
}

.sidebar a {
    display: block;
    color: white;
    text-decoration: none;
    padding: 12px;
    margin: 8px 0;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.15);
    transition: all 0.3s ease;
}

.sidebar a:hover {
    background: #FFBF00;
    color: #261661;
}

.menu-toggle {
    display: none;
    position: fixed;
    top: 15px;
    left: 15px;
    z-index: 1001;
    background: #261661;
    border: none;
    color: white;
    font-size: 24px;
    padding: 10px 15px;
    border-radius: 6px;
    cursor: pointer;
}

.wrapper {
    display: flex;
}

.main {
    flex: 1;
    margin-left: 250px;
    padding: 30px;
    background: #D5E4EF;
    min-height: 100vh;
    transition: margin-left 0.3s ease;
}

.main.full {
    margin-left: 0;
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #261661;
    flex-wrap: wrap;
    gap: 15px;
}

.topbar h2 {
    color: #261661;
    margin: 0;
    font-size: 24px;
}

.role-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12px;
    background: #261661;
    color: #FFBF00;
    font-weight: bold;
}

.card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

.card h3 {
    margin-top: 0;
    color: #261661;
}

button {
    padding: 8px 16px;
    background: #261661;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    color: white;
    transition: all 0.3s ease;
}

button:hover {
    background: #FFBF00;
    color: #261661;
}

.reject-btn {
    background: #dc2626 !important;
    color: white !important;
}

.reject-btn:hover {
    background: #b91c1c !important;
    color: white !important;
}

.success {
    color: #10b981;
    padding: 12px;
    background: #d1fae5;
    border-left: 4px solid #10b981;
    border-radius: 5px;
    margin-bottom: 20px;
}

.error {
    color: #dc2626;
    padding: 12px;
    background: #fee2e2;
    border-left: 4px solid #dc2626;
    border-radius: 5px;
    margin-bottom: 20px;
}

.status-pending {
    color: #f59e0b;
}

.status-approved {
    color: #10b981;
}

.status-rejected {
    color: #ef4444;
}

/* MOBILE RESPONSIVE */
@media (max-width: 768px) {
    .menu-toggle {
        display: block;
    }

    .sidebar {
        width: 100%;
        max-width: 250px;
    }

    .sidebar.hide {
        left: -100%;
    }

    .main {
        margin-left: 0;
        padding: 15px 15px 80px 15px;
    }

    .main.full {
        margin-left: 0;
    }

    .topbar {
        flex-direction: column;
        align-items: flex-start;
    }

    .topbar h2 {
        font-size: 20px;
    }

    .card {
        padding: 15px;
    }

    button {
        padding: 6px 12px;
        font-size: 12px;
    }
}
</style>
</head>
<body>

<button class="menu-toggle" onclick="toggleMenu()">☰</button>

<!-- SIDEBAR -->
<div id="sidebar" class="sidebar">
    <h2>Work-Study</h2>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="approve_student.php">👥 Approve Students</a>

    <?php if ($role == 'admin') { ?>
        <a href="approve_supervisour.php">🎓 Approve Supervisors</a>
    <?php } ?>

    <a href="approve_applications.php">📋 Job Applications</a>
    <a href="approve_worklog.php">⏰ View Work Logs</a>
    <a href="../logout.php">🚪 Logout</a>
</div>

<!-- WRAPPER FOR MOBILE -->
<div class="wrapper">

<!-- MAIN CONTENT -->
<div id="main" class="main">

    <div class="topbar">
        <div>
            <h2>⏰ View Work Logs</h2>
            <p style="margin: 5px 0 0 0; color: #718096; font-size: 14px;">Review and approve student work hours</p>
        </div>
        <span class="role-badge"><?php echo strtoupper($role); ?></span>
    </div>

    <?php
    if (isset($_GET['success'])) {
        echo "<div class='success'>" . htmlspecialchars($_GET['success']) . "</div>";
    }
    if (isset($_GET['error'])) {
        echo "<div class='error'>" . htmlspecialchars($_GET['error']) . "</div>";
    }
    ?>

    <!-- PENDING WORK LOGS -->
    <div class="card">
        <h3>Pending Work Logs</h3>

        <?php
        $worklogs = $conn->query("SELECT wl.*, s.full_name as student_name, s.matric_number, sup.full_name as supervisor_name
                                  FROM work_logs wl
                                  JOIN students s ON wl.student_id = s.id
                                  JOIN supervisors sup ON wl.supervisor_id = sup.id
                                  WHERE wl.supervisor_id = " . $supervisor_id . " AND wl.status='pending'
                                  ORDER BY wl.work_date DESC");

        if ($worklogs->num_rows > 0) {
            while ($row = $worklogs->fetch_assoc()) {
                echo "<div style='border-bottom: 1px solid #334155; padding: 15px 0;'>
                        <strong>{$row['student_name']} ({$row['matric_number']})</strong><br>
                        <span style='color: #94a3b8;'>Supervisor: {$row['supervisor_name']}</span><br>
                        <span style='color: #94a3b8;'>Date: {$row['work_date']} | Hours: {$row['hours_worked']}</span><br>
                        <span style='color: #94a3b8;'>Description: {$row['description']}</span><br>
                        <span class='status-pending'>Status: {$row['status']}</span><br><br>
                        <form method='POST' action='approve_worklog.php' style='display:inline;'>
                            <input type='hidden' name='log_id' value='{$row['id']}'>
                            <input type='hidden' name='action' value='approve'>
                            <button type='submit'>Approve</button>
                        </form>
                        <form method='POST' action='approve_worklog.php' style='display:block; margin-top: 10px;'>
                            <input type='hidden' name='log_id' value='{$row['id']}'>
                            <input type='hidden' name='action' value='reject'>
                            <textarea name='rejection_reason' placeholder='Reason for rejection (optional)' style='width:100%; padding:8px; margin:5px 0 5px 0; border:1px solid #ccc; border-radius:5px; font-family:Arial; font-size:12px;' rows='2'></textarea>
                            <button type='submit' class='reject-btn'>Reject</button>
                        </form>
                      </div>";
            }
        } else {
            echo "<p>No pending work logs.</p>";
        }
        ?>
    </div>

    <!-- ALL WORK LOGS -->
    <div class="card">
        <h3>All Work Logs</h3>

        <?php
        $all_worklogs = $conn->query("SELECT wl.*, s.full_name as student_name, s.matric_number, sup.full_name as supervisor_name
                                      FROM work_logs wl
                                      JOIN students s ON wl.student_id = s.id
                                      JOIN supervisors sup ON wl.supervisor_id = sup.id
                                      WHERE wl.supervisor_id = " . $supervisor_id . "
                                      ORDER BY wl.work_date DESC LIMIT 50");

        if ($all_worklogs->num_rows > 0) {
            while ($row = $all_worklogs->fetch_assoc()) {
                $status_class = "status-" . $row['status'];
                $reason_display = !empty($row['rejection_reason']) ? "<p style='margin: 5px 0; padding: 8px; background: #fee2e2; border-left: 2px solid #dc2626; color: #991b1b; font-size: 12px;'><strong>Reason:</strong> " . htmlspecialchars($row['rejection_reason']) . "</p>" : "";
                echo "<div style='border-bottom: 1px solid #334155; padding: 10px 0;'>
                        <strong>{$row['student_name']} ({$row['matric_number']})</strong> - {$row['supervisor_name']}<br>
                        <span style='color: #94a3b8;'>{$row['work_date']} | {$row['hours_worked']} hours | <span class='$status_class'>{$row['status']}</span></span><br>
                        <span style='color: #94a3b8;'>{$row['description']}</span>
                        {$reason_display}
                      </div>";
            }
        } else {
            echo "<p>No work logs found.</p>";
        }
        ?>
    </div>

</div>

</div>

<script>
function toggleMenu() {
    var sidebar = document.getElementById('sidebar');
    var main = document.getElementById('main');
    
    sidebar.classList.toggle('hide');
    main.classList.toggle('full');
}

// Close menu when clicking on a link
document.querySelectorAll('.sidebar a').forEach(link => {
    link.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
            document.getElementById('sidebar').classList.add('hide');
            document.getElementById('main').classList.remove('full');
        }
    });
});

// Auto-close menu when window is resized to desktop
window.addEventListener('resize', function() {
    if (window.innerWidth > 768) {
        document.getElementById('sidebar').classList.remove('hide');
        document.getElementById('main').classList.remove('full');
    }
});
</script>

</body>
</html>