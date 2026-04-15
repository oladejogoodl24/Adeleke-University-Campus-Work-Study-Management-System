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
    $application_id = $_POST['application_id'];
    $action = $_POST['action'];
    $rejection_reason = isset($_POST['rejection_reason']) ? trim($_POST['rejection_reason']) : null;

    if ($action == 'approve') {
        $status = 'approved';
        $message = "Application approved successfully.";
        $update_query = "UPDATE job_applications SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $status, $application_id);
    } elseif ($action == 'reject') {
        $status = 'rejected';
        $message = "Application rejected.";
        $update_query = "UPDATE job_applications SET status = ?, rejection_reason = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssi", $status, $rejection_reason, $application_id);
    } else {
        header("Location: approve_applications.php?error=Invalid action");
        exit();
    }

    if ($stmt->execute()) {
        header("Location: approve_applications.php?success=" . urlencode($message));
    } else {
        header("Location: approve_applications.php?error=Database error");
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Job Applications - Management Dashboard</title>
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
    overflow-y: auto;
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

.sidebar a.active {
    background: #FFBF00;
    color: #261661;
    font-weight: bold;
}

/* MAIN CONTENT */
.main {
    flex: 1;
    padding: 30px;
    overflow-y: auto;
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
    margin: 0;
    font-size: 28px;
    color: #261661;
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
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

.card h3 {
    margin-top: 0;
    color: #261661;
    font-size: 18px;
    margin-bottom: 20px;
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
    padding: 15px;
    background: #d1fae5;
    border-radius: 5px;
    margin-bottom: 20px;
    border-left: 4px solid #10b981;
}

.error {
    color: #dc2626;
    padding: 15px;
    background: #fee2e2;
    border-radius: 5px;
    margin-bottom: 20px;
    border-left: 4px solid #dc2626;
}

.application-item {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 15px;
    border-left: 4px solid #261661;
}

.application-item h4 {
    margin: 0 0 10px 0;
    color: #261661;
    font-size: 16px;
}

.application-details {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin: 15px 0;
}

.detail-item {
    display: flex;
    flex-direction: column;
}

.detail-item label {
    color: #718096;
    font-size: 12px;
    margin-bottom: 5px;
    text-transform: uppercase;
    font-weight: bold;
}

.detail-item span {
    color: #1a1a1a;
    font-size: 14px;
}

.detail-item strong {
    color: #261661;
}

.application-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e5e7eb;
}

.application-actions form {
    display: inline;
    margin: 0;
}

.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    margin-top: 5px;
}

.status-pending {
    background: #fdb913;
    color: #261661;
}

.status-approved {
    background: #10b981;
    color: white;
}

.status-rejected {
    background: #dc2626;
    color: white;
}

.empty-message {
    text-align: center;
    padding: 40px 20px;
    color: #718096;
}

.filter-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.filter-tabs button {
    padding: 10px 20px;
    background: #e5e7eb;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    color: #261661;
    font-weight: bold;
    transition: all 0.3s ease;
}

.filter-tabs button.active {
    background: #38bdf8;
    color: black;
}

@media (max-width: 768px) {
    .application-details {
        grid-template-columns: 1fr;
    }

    .topbar {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }

    .sidebar {
        width: 200px;
    }

    .main {
        padding: 15px;
    }
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

    <a href="approve_applications.php" class="active">Job Applications</a>
    <a href="approve_worklog.php">View Work Logs</a>
    <a href="../logout.php">Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">

    <div class="topbar">
        <div>
            <h2>Student Job Applications</h2>
            <p style="margin: 5px 0 0 0; color: #94a3b8; font-size: 14px;">Review and manage student job applications</p>
        </div>
        <span class="role-badge"><?php echo strtoupper($role); ?></span>
    </div>

    <?php
    if (isset($_GET['success'])) {
        echo "<div class='success'>✓ " . htmlspecialchars($_GET['success']) . "</div>";
    }
    if (isset($_GET['error'])) {
        echo "<div class='error'>✗ " . htmlspecialchars($_GET['error']) . "</div>";
    }
    ?>

    <!-- PENDING APPLICATIONS -->
    <div class="card">
        <h3>📋 Pending Applications</h3>

        <?php
        // Get all pending applications for jobs posted by this supervisor
        $pending_query = "SELECT ja.id, ja.application_date, ja.status, 
                                s.id as student_id, s.full_name, s.email, s.matric_number, s.department, s.level,
                                j.id as job_id, j.title, j.hours_required
                         FROM job_applications ja
                         JOIN students s ON ja.student_id = s.id
                         JOIN jobs j ON ja.job_id = j.id
                         WHERE j.supervisor_id = ? AND ja.status = 'pending'
                         ORDER BY ja.application_date DESC";
        
        $stmt = $conn->prepare($pending_query);
        $stmt->bind_param("i", $supervisor_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='application-item'>
                        <h4>" . htmlspecialchars($row['title']) . "</h4>
                        
                        <div class='application-details'>
                            <div class='detail-item'>
                                <label>Student Name</label>
                                <span><strong>" . htmlspecialchars($row['full_name']) . "</strong></span>
                            </div>
                            <div class='detail-item'>
                                <label>Matric Number</label>
                                <span>" . htmlspecialchars($row['matric_number']) . "</span>
                            </div>
                            <div class='detail-item'>
                                <label>Email</label>
                                <span>" . htmlspecialchars($row['email']) . "</span>
                            </div>
                            <div class='detail-item'>
                                <label>Department</label>
                                <span>" . htmlspecialchars($row['department']) . " - Level " . $row['level'] . "</span>
                            </div>
                            <div class='detail-item'>
                                <label>Position</label>
                                <span>" . htmlspecialchars($row['title']) . " (" . $row['hours_required'] . " hrs/week)</span>
                            </div>
                            <div class='detail-item'>
                                <label>Applied On</label>
                                <span>" . date('M d, Y at H:i', strtotime($row['application_date'])) . "</span>
                            </div>
                        </div>

                        <div class='application-actions'>
                            <form method='POST' action='approve_applications.php' style='display:inline;'>
                                <input type='hidden' name='application_id' value='" . $row['id'] . "'>
                                <input type='hidden' name='action' value='approve'>
                                <button type='submit'>✓ Approve</button>
                            </form>
                            <form method='POST' action='approve_applications.php' style='display:block; margin-top: 10px;'>
                                <input type='hidden' name='application_id' value='" . $row['id'] . "'>
                                <input type='hidden' name='action' value='reject'>
                                <textarea name='rejection_reason' placeholder='Reason for rejection (optional)' style='width:100%; padding:8px; margin:5px 0 5px 0; border:1px solid #ccc; border-radius:5px; font-family:Arial; font-size:12px;' rows='2'></textarea>
                                <button type='submit' class='reject-btn'>✗ Reject</button>
                            </form>
                        </div>
                      </div>";
            }
        } else {
            echo "<div class='empty-message'>
                    <p>✓ No pending applications</p>
                    <p style='font-size: 12px; margin-top: 10px;'>All applications have been reviewed!</p>
                  </div>";
        }
        ?>
    </div>

    <!-- APPROVED APPLICATIONS -->
    <div class="card">
        <h3>✓ Approved Applications</h3>

        <?php
        $approved_query = "SELECT ja.id, ja.application_date, 
                                  s.full_name, s.email, s.matric_number,
                                  j.title
                           FROM job_applications ja
                           JOIN students s ON ja.student_id = s.id
                           JOIN jobs j ON ja.job_id = j.id
                           WHERE j.supervisor_id = ? AND ja.status = 'approved'
                           ORDER BY ja.application_date DESC";
        
        $stmt = $conn->prepare($approved_query);
        $stmt->bind_param("i", $supervisor_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='application-item' style='border-left-color: #10b981;'>
                        <div style='display: flex; justify-content: space-between; align-items: start;'>
                            <div>
                                <h4 style='color: #10b981; margin: 0 0 5px 0;'>" . htmlspecialchars($row['title']) . "</h4>
                                <p style='margin: 5px 0; color: #94a3b8;'><strong>" . htmlspecialchars($row['full_name']) . "</strong> (" . $row['matric_number'] . ")</p>
                                <p style='margin: 5px 0; color: #94a3b8; font-size: 12px;'>" . $row['email'] . "</p>
                                <p style='margin: 5px 0; color: #94a3b8; font-size: 12px;'>Applied: " . date('M d, Y', strtotime($row['application_date'])) . "</p>
                            </div>
                            <span class='status-badge status-approved'>APPROVED</span>
                        </div>
                      </div>";
            }
        } else {
            echo "<div class='empty-message'>
                    <p>No approved applications yet</p>
                  </div>";
        }
        ?>
    </div>

    <!-- REJECTED APPLICATIONS -->
    <div class="card">
        <h3>✗ Rejected Applications</h3>

        <?php
        $rejected_query = "SELECT ja.id, ja.application_date, ja.rejection_reason,
                                  s.full_name, s.email, s.matric_number,
                                  j.title
                           FROM job_applications ja
                           JOIN students s ON ja.student_id = s.id
                           JOIN jobs j ON ja.job_id = j.id
                           WHERE j.supervisor_id = ? AND ja.status = 'rejected'
                           ORDER BY ja.application_date DESC";
        
        $stmt = $conn->prepare($rejected_query);
        $stmt->bind_param("i", $supervisor_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $reason_display = !empty($row['rejection_reason']) ? "<p style='margin: 10px 0; padding: 10px; background: #fee2e2; border-left: 3px solid #dc2626; color: #991b1b; font-size: 12px;'><strong>Reason:</strong> " . htmlspecialchars($row['rejection_reason']) . "</p>" : "";
                echo "<div class='application-item' style='border-left-color: #ef4444;'>
                        <div style='display: flex; justify-content: space-between; align-items: start;'>
                            <div style='flex: 1;'>
                                <h4 style='color: #ef4444; margin: 0 0 5px 0;'>" . htmlspecialchars($row['title']) . "</h4>
                                <p style='margin: 5px 0; color: #94a3b8;'><strong>" . htmlspecialchars($row['full_name']) . "</strong> (" . $row['matric_number'] . ")</p>
                                <p style='margin: 5px 0; color: #94a3b8; font-size: 12px;'>" . $row['email'] . "</p>
                                <p style='margin: 5px 0; color: #94a3b8; font-size: 12px;'>Applied: " . date('M d, Y', strtotime($row['application_date'])) . "</p>
                                " . $reason_display . "
                            </div>
                            <span class='status-badge status-rejected'>REJECTED</span>
                        </div>
                      </div>";
            }
        } else {
            echo "<div class='empty-message'>
                    <p>No rejected applications</p>
                  </div>";
        }
        ?>
    </div>

</div>

</body>
</html>