<?php
session_start();
include(__DIR__ . "/db.php");

// Handle job application
if(isset($_POST['apply_job'])){
    $student_id = $_SESSION['student_id'];
    $job_id = $_POST['job_id'];
    
    // Check if already applied
    $checkStmt = $conn->prepare("SELECT id FROM job_applications WHERE student_id = ? AND job_id = ?");
    $checkStmt->bind_param("ii", $student_id, $job_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if($checkResult->num_rows > 0){
        echo "<script>alert('You have already applied for this job.');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO job_applications (student_id, job_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $student_id, $job_id);
        
        if($stmt->execute()){
            echo "<script>alert('Job application submitted successfully!');</script>";
        } else {
            echo "<script>alert('Error applying for job. Please try again.');</script>";
        }
        $stmt->close();
    }
    $checkStmt->close();
}

if(isset($_POST['date'])){

    $student_id = $_SESSION['student_id'];
    $job = $_POST['job'];
    $hours = $_POST['hours'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    // Get supervisor_id from the jobs table based on the job selected
    // For now, we'll try to get it; if not available, default to 1
    $supervisor_id = 1;
    if (!empty($job)) {
        $job_stmt = $conn->prepare("SELECT supervisor_id FROM jobs WHERE id = ? LIMIT 1");
        $job_stmt->bind_param("i", $job);
        $job_stmt->execute();
        $job_result = $job_stmt->get_result();
        if ($job_result->num_rows > 0) {
            $job_row = $job_result->fetch_assoc();
            $supervisor_id = $job_row['supervisor_id'];
        }
        $job_stmt->close();
    }

    // Insert into work_logs with proper status
    $stmt = $conn->prepare("INSERT INTO work_logs (student_id, supervisor_id, work_date, hours_worked, description, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("iisds", $student_id, $supervisor_id, $date, $hours, $description);

    if($stmt->execute()){
        echo "<script>alert('Work logged successfully'); closelog();</script>";
    } else {
        echo "<script>alert('Error logging work: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
if(!isset($_SESSION['student_id'])){
    header("Location: student_login.php");
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : "dashboard";
?>

<!DOCTYPE html>
<html>
<head>
<title>Work Study Portal</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>

:root{
--primary:#261661;
--accent:#FFBF00;
--bg:#D5E4EF;
--white:#ffffff;
}

body{
margin:0;
font-family:Segoe UI;
background:var(--bg);
}

.wrapper{
display:flex;
}

/* SIDEBAR */

.sidebar{
width:250px;
height:100vh;
background:var(--primary);
color:white;
position:fixed;
transition:0.3s;
}

.sidebar.hide{
left:-250px;
}

.logo{
text-align:center;
padding:20px;
border-bottom:1px solid rgba(255,255,255,0.1);
}

.logo img{
width:60px;
margin-bottom:10px;
}

.logo h3{
margin:0;
font-weight:500;
}

.nav{
padding:20px;
}

.nav a{
display:block;
color:white;
text-decoration:none;
padding:12px;
border-radius:8px;
margin-bottom:8px;
}

.nav a:hover{
background:rgba(255,255,255,0.1);
}

.active{
background:var(--accent);
color:black !important;
}

/* MAIN */

.main{
margin-left:250px;
flex:1;
padding:30px;
transition:0.3s;
}

.main.full{
margin-left:0;
}

/* HEADER */

.header{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;
}

.menu{
font-size:24px;
cursor:pointer;
}

.user{
background:white;
padding:8px 14px;
border-radius:20px;
}

/* CARDS */

.grid{
display:grid;
grid-template-columns:repeat(3,1fr);
gap:20px;
margin-bottom:30px;
}

.card{
background:white;
padding:20px;
border-radius:12px;
box-shadow:0 4px 10px rgba(0,0,0,0.05);
}

/* JOB GRID */

.job-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
gap:20px;
}

.job{
background:white;
padding:20px;
border-radius:12px;
box-shadow:0 4px 10px rgba(0,0,0,0.05);
}

.job button{
margin-top:10px;
padding:10px;
border:none;
background:var(--primary);
color:white;
border-radius:6px;
cursor:pointer;
width:100%;
}

.job button:hover{
background:#1a1047;
}

/* APPLICATION CARDS */

.application{
background:white;
padding:25px;
border-radius:12px;
margin-bottom:20px;
box-shadow:0 4px 10px rgba(0,0,0,0.05);
}

/* STATUS BADGES */

.badge{
padding:6px 10px;
border-radius:6px;
font-size:12px;
}

.pending{
background:orange;
color:white;
}

.approved{
background:green;
color:white;
}

.rejected{
background:red;
color:white;
}

/* TABLE */

table{
width:100%;
border-collapse:collapse;
}

th,td{
padding:12px;
border-bottom:1px solid #eee;
text-align:left;
}

/* PROGRESS BAR */

.progress{
background:#eee;
height:12px;
border-radius:10px;
overflow:hidden;
}

.progress-bar{
height:12px;
background:var(--accent);
width:50%;
}
/* MOBILE RESPONSIVE */
@media(max-width:768px){

.sidebar{
left:-250px;
position:fixed;
z-index:1000;
}

.sidebar.show{
left:0;
}

.main{
margin-left:0;
padding:15px;
}

.grid{
grid-template-columns:1fr;
}

.job-grid{
grid-template-columns:1fr;
}

.card{
padding:15px;
}

.modal-content{
width:90%;
}

.header{
flex-direction:column;
gap:10px;
align-items:flex-start;
}

}
</style>
</head>

<body>

<div class="wrapper">

<!-- SIDEBAR -->

<div id="sidebar" class="sidebar">

<div class="logo">
<img src="assets/images/logo.png.webp" alt="Work Study Portal Logo">
</div>

<div class="nav">

<a href="?page=dashboard" class="<?= $page=='dashboard'?'active':'' ?>">Dashboard</a>

<a href="?page=browse" class="<?= $page=='browse'?'active':'' ?>">Browse Jobs</a>

<a href="?page=applications" class="<?= $page=='applications'?'active':'' ?>">My Applications</a>

<a href="?page=loghours" class="<?= $page=='loghours'?'active':'' ?>">Log Hours</a>

<a href="?page=summary" class="<?= $page=='summary'?'active':'' ?>">Work Summary</a>

<a href="logout.php">Logout</a>
</div>

</div>

<!-- MAIN -->

<div id="main" class="main">

<div class="header">
<div class="menu" onclick="toggle()">☰</div>

<div class="user">
<?php echo $_SESSION['full_name']; ?>
</div>

</div>

<!-- DASHBOARD -->

<?php if($page=="dashboard"){ ?>

<div class="grid">

<div class="card">
<h3>Hours This Week</h3>
<h1>2 / 4</h1>
</div>

<div class="card">
<h3>Hours This Month</h3>
<h1>10</h1>
</div>

<div class="card">
<h3>Approved Hours</h3>
<h1>8</h1>
</div>

</div>

<div class="card">

<h3>Weekly Progress</h3>

<div class="progress">
<div class="progress-bar"></div>
</div>

<p>2 of 4 hours completed this week</p>

</div>

<?php } ?>

<!-- BROWSE JOBS -->

<?php if($page=="browse"){ ?>

<h2>Available Campus Jobs</h2>

<div class="job-grid">

<?php
$result = $conn->query("SELECT * FROM jobs");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="job">';
        echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
        echo '<p>Department: ' . htmlspecialchars($row['department']) . '</p>';
        echo '<p>Hours Required: ' . $row['hours_required'] . ' hours/week</p>';
        echo '<p>Positions: ' . (5 - $row['positions_available']) . ' / ' . $row['positions_available'] . '</p>';
        echo '<p>Supervisor: ' . htmlspecialchars($row['supervisor']) . '</p>';
        echo '<form method="POST" style="margin:0;">';
        echo '<input type="hidden" name="apply_job" value="1">';
        echo '<input type="hidden" name="job_id" value="' . $row['id'] . '">';
        echo '<button type="submit" style="width:100%; padding:10px; border:none; background:#261661; color:white; border-radius:6px; cursor:pointer; margin-top:10px;">Apply</button>';
        echo '</form>';
        echo '</div>';
    }
} else {
    echo '<p>No jobs available.</p>';
}
?>

</div>

<?php } ?>

<!-- APPLICATIONS -->

<?php if($page=="applications"){ ?>

<h2>My Applications</h2>

<?php
$student_id = $_SESSION['student_id'];
$stmt = $conn->prepare("SELECT ja.id, ja.status, ja.application_date, ja.rejection_reason, j.title, j.department, j.supervisor
    FROM job_applications ja
    JOIN jobs j ON ja.job_id = j.id
    WHERE ja.student_id = ?
    ORDER BY ja.application_date DESC");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $statusClass = ($row['status'] == 'approved') ? 'approved' : (($row['status'] == 'rejected') ? 'rejected' : 'pending');
        $reason_display = !empty($row['rejection_reason']) ? "<p style='margin: 10px 0; padding: 10px; background: #fee2e2; border-left: 3px solid #dc2626; color: #991b1b; border-radius: 4px;'><strong>Rejection Reason:</strong> " . htmlspecialchars($row['rejection_reason']) . "</p>" : "";
        
        echo '<div class="application">';
        echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
        echo '<p>Department: ' . htmlspecialchars($row['department']) . '</p>';
        echo '<p>Supervisor: ' . htmlspecialchars($row['supervisor']) . '</p>';
        echo '<p>Applied: ' . date('M d, Y', strtotime($row['application_date'])) . '</p>';
        echo '<span class="badge ' . $statusClass . '">' . ucfirst($row['status']) . '</span>';
        echo $reason_display;
        echo '</div>';
    }
} else {
    echo '<div class="card"><p>No applications yet. Browse jobs and click Apply to submit.</p></div>';
}

$stmt->close();
?>

<?php } ?>

<!-- LOG HOURS -->

<?php if($page=="loghours"){ ?>

<h2>Log Work Hours</h2>
<button onclick="openlog()" style="padding:10px 
16px; background:#FFBF00; colour:black;border:none;
border-radius:6px;margin-bottom:20px;">
+Log Hours
</button>


<div class="card">

<table>

<tr>
<th>Date</th>
<th>Job</th>
<th>Hours</th>
<th>Description</th>
<th>Status</th>
</tr>

<tr>
<td>2026-03-10</td>
<td>Library Assistant</td>
<td>2h</td>
<td>Shelved books</td>
<td><span class="badge pending">Pending</span></td>
</tr>

<tr>
<td>2026-03-08</td>
<td>ICT Support</td>
<td>2h</td>
<td>Printer setup</td>
<td><span class="badge approved">Approved</span></td>
</tr>

</table>

</div>

<?php } ?>

<!-- WORK SUMMARY -->

<?php if($page=="summary"){ ?>

<h2>Work Summary</h2>

<div class="grid">

<div class="card">
<h3>This Week</h3>
<h1>2 / 4 hours</h1>
</div>

<div class="card">
<h3>This Month</h3>
<h1>10 hours</h1>
</div>

<div class="card">
<h3>Total Approved</h3>
<h1>32 hours</h1>
</div>

</div>

<div class="card">

<h3>Work History</h3>

<table>

<tr>
<th>Week</th>
<th>Hours</th>
<th>Approved By</th>
<th>Status</th>
</tr>

<tr>
<td>Mar 1 - Mar 7</td>
<td>4h</td>
<td>Dr Adewale</td>
<td><span class="badge approved">Approved</span></td>
</tr>

<tr>
<td>Feb 22 - Feb 28</td>
<td>3h</td>
<td>Mr Akin</td>
<td><span class="badge approved">Approved</span></td>
</tr>

</table>

</div>

<?php } ?>

</div>

</div>

<!-- LOG MODAL -->
<div id="logmodel" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:1000;">
  <div style="background:white; padding:30px; border-radius:12px; width:90%; max-width:500px; box-shadow:0 10px 40px rgba(0,0,0,0.2);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h2 style="margin:0;">Log Work Hours</h2>
      <button onclick="closelog()" style="background:none; border:none; font-size:24px; cursor:pointer;">×</button>
    </div>
    
    <form method="POST" action="">
      <div style="margin-bottom:15px;">
        <label style="display:block; margin-bottom:5px; font-weight:bold;">Date</label>
        <input type="date" name="date" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; box-sizing:border-box;">
      </div>
      
      <div style="margin-bottom:15px;">
        <label style="display:block; margin-bottom:5px; font-weight:bold;">Job</label>
        <select name="job" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; box-sizing:border-box;">
          <option value="">Select Job</option>
          <?php
          $jobResult = $conn->query("SELECT title FROM jobs");
          while ($jobRow = $jobResult->fetch_assoc()) {
              echo '<option value="' . htmlspecialchars($jobRow['title']) . '">' . htmlspecialchars($jobRow['title']) . '</option>';
          }
          ?>
        </select>
      </div>
      
      <div style="margin-bottom:15px;">
        <label style="display:block; margin-bottom:5px; font-weight:bold;">Hours Worked</label>
        <input type="number" name="hours" step="0.5" min="0" max="8" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; box-sizing:border-box;">
      </div>
      
      <div style="margin-bottom:20px;">
        <label style="display:block; margin-bottom:5px; font-weight:bold;">Description</label>
        <textarea name="description" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; box-sizing:border-box; height:80px;"></textarea>
      </div>
      
      <div style="display:flex; gap:10px;">
        <button type="submit" style="flex:1; padding:12px; background:#261661; color:white; border:none; border-radius:6px; cursor:pointer; font-weight:bold;">Submit</button>
        <button type="button" onclick="closelog()" style="flex:1; padding:12px; background:#ddd; color:black; border:none; border-radius:6px; cursor:pointer; font-weight:bold;">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>

function openlog(){
    document.getElementById("logmodel").style.display = "flex";
}

function closelog(){
    document.getElementById("logmodel").style.display = "none";
}

function toggle(){

let sidebar=document.getElementById("sidebar");

if(window.innerWidth <= 768){
    sidebar.classList.toggle("show");
}else{
    sidebar.classList.toggle("hide");
    document.getElementById("main").classList.toggle("full");
}

}

</script>
</script>

</body>
</html>