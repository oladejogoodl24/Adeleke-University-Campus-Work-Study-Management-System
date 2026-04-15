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

$name = $_SESSION['management_name'];
$role = $_SESSION['management_role'];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Management Dashboard</title>
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

/* MOBILE MENU BUTTON */
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

/* MAIN CONTENT */
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

/* STATS GRID */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    border-left: 4px solid #261661;
}

.stat-card h4 {
    color: #718096;
    font-size: 12px;
    margin: 0 0 10px 0;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.stat-card .number {
    color: #261661;
    font-size: 32px;
    font-weight: bold;
    margin: 10px 0;
}

.stat-card p {
    color: #94a3b8;
    margin: 5px 0;
    font-size: 13px;
}

/* CARD */
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
    font-size: 18px;
    border-bottom: 2px solid #FFBF00;
    padding-bottom: 10px;
}

.item-row {
    padding: 15px 0;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.item-row:last-child {
    border-bottom: none;
}

.item-info {
    flex: 1;
    min-width: 200px;
}

.item-name {
    font-weight: bold;
    color: #261661;
    margin-bottom: 5px;
}

.item-meta {
    font-size: 13px;
    color: #718096;
}

.item-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
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
    font-size: 13px;
}

button:hover {
    background: #FFBF00;
    color: #261661;
}

.btn-reject {
    background: #dc2626;
}

.btn-reject:hover {
    background: #b91c1c;
    color: white;
}

.empty-msg {
    padding: 30px;
    text-align: center;
    color: #718096;
    background: #f8f9fa;
    border-radius: 8px;
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

    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-card {
        padding: 15px;
    }

    .stat-card .number {
        font-size: 24px;
    }

    .card {
        padding: 15px;
    }

    .item-row {
        flex-direction: column;
        align-items: flex-start;
    }

    .item-actions {
        width: 100%;
    }

    .item-actions button {
        flex: 1;
        min-width: 100px;
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
            <h2>Welcome, <?php echo htmlspecialchars($name); ?></h2>
            <p style="margin: 5px 0 0 0; color: #718096; font-size: 14px;">Management Dashboard</p>
        </div>
        <span class="role-badge"><?php echo strtoupper($role); ?></span>
    </div>

    <?php
    if (isset($_GET['success'])) {
        echo "<div style='color: #10b981; padding: 12px; background: #d1fae5; border-left: 4px solid #10b981; border-radius: 5px; margin-bottom: 20px;'>" . htmlspecialchars($_GET['success']) . "</div>";
    }
    if (isset($_GET['error'])) {
        echo "<div style='color: #dc2626; padding: 12px; background: #fee2e2; border-left: 4px solid #dc2626; border-radius: 5px; margin-bottom: 20px;'>" . htmlspecialchars($_GET['error']) . "</div>";
    }
    ?>

    <!-- STATISTICS CARDS -->
    <div class="stats-grid">
        <?php
        // Get statistics
        $pending_students = $conn->query("SELECT COUNT(*) as count FROM students WHERE status='pending'")->fetch_assoc();
        $approved_students = $conn->query("SELECT COUNT(*) as count FROM students WHERE status='approved'")->fetch_assoc();
        $pending_supervisors = $conn->query("SELECT COUNT(*) as count FROM supervisors WHERE status='pending'")->fetch_assoc();
        $pending_applications = $conn->query("SELECT COUNT(*) as count FROM job_applications WHERE status='pending'")->fetch_assoc();
        $pending_worklogs = $conn->query("SELECT COUNT(*) as count FROM work_logs WHERE status='pending'")->fetch_assoc();
        ?>
        
        <div class="stat-card">
            <h4>⏳ Pending Students</h4>
            <div class="number"><?php echo $pending_students['count']; ?></div>
            <p>Awaiting approval</p>
        </div>

        <div class="stat-card">
            <h4>✓ Approved Students</h4>
            <div class="number"><?php echo $approved_students['count']; ?></div>
            <p>Active accounts</p>
        </div>

        <?php if ($role == 'admin') { ?>
        <div class="stat-card">
            <h4>⏳ Pending Supervisors</h4>
            <div class="number"><?php echo $pending_supervisors['count']; ?></div>
            <p>Awaiting approval</p>
        </div>
        <?php } ?>

        <div class="stat-card">
            <h4>📋 Pending Applications</h4>
            <div class="number"><?php echo $pending_applications['count']; ?></div>
            <p>Student job applications</p>
        </div>

        <div class="stat-card">
            <h4>⏰ Pending Hours</h4>
            <div class="number"><?php echo $pending_worklogs['count']; ?></div>
            <p>Work logs to review</p>
        </div>
    </div>

    <!-- PENDING STUDENTS -->
    <div class="card">
        <h3>👥 Pending Student Registrations (<?php echo $pending_students['count']; ?>)</h3>

        <?php
        $students = $conn->query("SELECT * FROM students WHERE status='pending' ORDER BY id DESC LIMIT 10");

        if ($students->num_rows > 0) {
            while ($row = $students->fetch_assoc()) {
                echo "
                <div class='item-row'>
                    <div class='item-info'>
                        <div class='item-name'>" . htmlspecialchars($row['full_name']) . "</div>
                        <div class='item-meta'>📧 " . htmlspecialchars($row['email']) . "</div>
                        <div class='item-meta'>📚 " . htmlspecialchars($row['department']) . " - Level " . htmlspecialchars($row['level']) . "</div>
                    </div>
                    <div class='item-actions'>
                        <form method='POST' action='approve_student.php' style='margin:0;'>
                            <input type='hidden' name='student_id' value='" . $row['id'] . "'>
                            <input type='hidden' name='action' value='approve'>
                            <button type='submit'>✓ Approve</button>
                        </form>
                        <form method='POST' action='approve_student.php' style='margin:0;'>
                            <input type='hidden' name='student_id' value='" . $row['id'] . "'>
                            <input type='hidden' name='action' value='reject'>
                            <button type='submit' class='btn-reject'>✗ Reject</button>
                        </form>
                    </div>
                </div>";
            }
        } else {
            echo "<div class='empty-msg'>✓ No pending students</div>";
        }
        ?>
    </div>

    <?php if ($role == 'admin') { ?>
    <!-- PENDING SUPERVISORS (ADMIN ONLY) -->
    <div class="card">
        <h3>🎓 Pending Supervisor Registrations (<?php echo $pending_supervisors['count']; ?>)</h3>

        <?php
        $supervisors = $conn->query("SELECT * FROM supervisors WHERE status='pending' ORDER BY id DESC LIMIT 10");

        if ($supervisors->num_rows > 0) {
            while ($row = $supervisors->fetch_assoc()) {
                echo "
                <div class='item-row'>
                    <div class='item-info'>
                        <div class='item-name'>" . htmlspecialchars($row['full_name']) . "</div>
                        <div class='item-meta'>🆔 Staff ID: " . htmlspecialchars($row['staff_id']) . "</div>
                        <div class='item-meta'>📧 " . htmlspecialchars($row['email']) . "</div>
                    </div>
                    <div class='item-actions'>
                        <form method='POST' action='approve_supervisour.php' style='margin:0;'>
                            <input type='hidden' name='supervisor_id' value='" . $row['id'] . "'>
                            <input type='hidden' name='action' value='approve'>
                            <button type='submit'>✓ Approve</button>
                        </form>
                        <form method='POST' action='approve_supervisour.php' style='margin:0;'>
                            <input type='hidden' name='supervisor_id' value='" . $row['id'] . "'>
                            <input type='hidden' name='action' value='reject'>
                            <button type='submit' class='btn-reject'>✗ Reject</button>
                        </form>
                    </div>
                </div>";
            }
        } else {
            echo "<div class='empty-msg'>✓ No pending supervisors</div>";
        }
        ?>
    </div>
    <?php } ?>

    <!-- QUICK ACTIONS -->
    <div class="card">
        <h3>⚡ Quick Actions</h3>
        <div class="item-row">
            <div class="item-info">
                <div class="item-name">📋 Review Job Applications</div>
                <div class="item-meta">Review student applications for your positions</div>
            </div>
            <div class="item-actions">
                <button onclick="window.location.href='approve_applications.php'">Go to Applications</button>
            </div>
        </div>
        <div class="item-row">
            <div class="item-info">
                <div class="item-name">⏰ Review Work Logs</div>
                <div class="item-meta">Approve or reject submitted work hours</div>
            </div>
            <div class="item-actions">
                <button onclick="window.location.href='approve_worklog.php'">Review Work Logs</button>
            </div>
        </div>
        <div class="item-row">
            <div class="item-info">
                <div class="item-name">👥 Manage Students</div>
                <div class="item-meta">Approve or reject student registrations</div>
            </div>
            <div class="item-actions">
                <button onclick="window.location.href='approve_student.php'">Manage Students</button>
            </div>
        </div>
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