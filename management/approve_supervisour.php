<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../db.php");

// Check if logged in and role is admin
if (!isset($_SESSION['management_id']) || $_SESSION['management_role'] != 'admin') {
    header("Location: ../supervisor_login.php");
    exit();
}

$name = $_SESSION['management_name'];
$role = $_SESSION['management_role'];

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $supervisor_id = $_POST['supervisor_id'];
    $action = $_POST['action'];
    $rejection_reason = isset($_POST['rejection_reason']) ? trim($_POST['rejection_reason']) : null;

    if ($action == 'approve') {
        $status = 'approved';
        $message = "Supervisor approved successfully.";
        $stmt = $conn->prepare("UPDATE supervisors SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $supervisor_id);
    } elseif ($action == 'reject') {
        $status = 'rejected';
        $message = "Supervisor rejected.";
        // Note: Add rejection_reason column to supervisors table if needed
        $stmt = $conn->prepare("UPDATE supervisors SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $supervisor_id);
    } else {
        header("Location: dashboard.php?error=Invalid action");
        exit();
    }

    if ($stmt->execute()) {
        header("Location: approve_supervisour.php?success=" . urlencode($message));
    } else {
        header("Location: approve_supervisour.php?error=Database error");
    }
    exit();
}

// If no POST data, show the approval page
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Approve Supervisors - Management Dashboard</title>
<style>
body {
    margin: 0;
    font-family: Segoe UI, Arial, sans-serif;
    background: #D5E4EF;
    color: #1a1a1a;
    display: flex;
}

/* SIDEBAR */
.sidebar {
    width: 250px;
    background: #261661;
    height: 100vh;
    padding: 20px;
    color: white;
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

/* MAIN CONTENT */
.main {
    flex: 1;
    padding: 30px;
    background: #D5E4EF;
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #261661;
}

.topbar h2 {
    color: #261661;
    margin: 0;
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
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Work-Study</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="approve_student.php">Approve Students</a>

    <?php if ($role == 'admin') { ?>
        <a href="approve_supervisour.php">Approve Supervisors</a>
    <?php } ?>

    <a href="approve_applications.php">Job Applications</a>
    <a href="approve_worklog.php">View Work Logs</a>
    <a href="../logout.php">Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">

    <div class="topbar">
        <h2>Approve Supervisors</h2>
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

    <!-- PENDING SUPERVISORS -->
    <div class="card">
        <h3>Pending Supervisor Registrations</h3>

        <?php
        $supervisors = $conn->query("SELECT * FROM supervisors WHERE status='pending'");

        if ($supervisors->num_rows > 0) {
            while ($row = $supervisors->fetch_assoc()) {
                echo "<div style='border-bottom: 1px solid #334155; padding: 15px 0;'>
                        <strong>{$row['full_name']}</strong><br>
                        <span style='color: #94a3b8;'>Email: {$row['email']}</span><br>
                        <span style='color: #94a3b8;'>Staff ID: {$row['staff_id']}</span><br><br>
                        <form method='POST' action='approve_supervisour.php' style='display:inline;'>
                            <input type='hidden' name='supervisor_id' value='{$row['id']}'>
                            <input type='hidden' name='action' value='approve'>
                            <button type='submit'>Approve</button>
                        </form>
                        <form method='POST' action='approve_supervisour.php' style='display:block; margin-top: 10px;'>
                            <input type='hidden' name='supervisor_id' value='{$row['id']}'>
                            <input type='hidden' name='action' value='reject'>
                            <textarea name='rejection_reason' placeholder='Reason for rejection (optional)' style='width:100%; padding:8px; margin:5px 0 5px 0; border:1px solid #ccc; border-radius:5px; font-family:Arial; font-size:12px;' rows='2'></textarea>
                            <button type='submit' class='reject-btn'>Reject</button>
                        </form>
                      </div>";
            }
        } else {
            echo "<p>No pending supervisors.</p>";
        }
        ?>
    </div>

    <!-- APPROVED SUPERVISORS -->
    <div class="card">
        <h3>Approved Supervisors</h3>

        <?php
        $approved_supervisors = $conn->query("SELECT * FROM supervisors WHERE status='approved' ORDER BY full_name");

        if ($approved_supervisors->num_rows > 0) {
            while ($row = $approved_supervisors->fetch_assoc()) {
                echo "<div style='border-bottom: 1px solid #334155; padding: 10px 0;'>
                        <strong>{$row['full_name']}</strong> - {$row['email']} ({$row['staff_id']})
                      </div>";
            }
        } else {
            echo "<p>No approved supervisors yet.</p>";
        }
        ?>
    </div>

</div>

</body>
</html>