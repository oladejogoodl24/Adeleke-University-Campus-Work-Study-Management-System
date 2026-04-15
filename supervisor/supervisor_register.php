<?php
include __DIR__ . "/db.php";  // ✅ Correct - uses absolute path

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $staff_id = trim($_POST["staff_id"]);
    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    if (!empty($staff_id) && !empty($full_name) && !empty($email) && !empty($_POST["password"])) {

        if (!str_ends_with($email, "@adelekeuniversity.edu.ng")) {
            $error = "Only official Adeleke University emails allowed.";
        } else {

            $check = $conn->prepare("SELECT id FROM supervisors WHERE staff_id=? OR email=?");
            $check->bind_param("ss", $staff_id, $email);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $error = "Staff ID or Email already exists.";
            } else {

                $status = "pending";

                $stmt = $conn->prepare("INSERT INTO supervisors (staff_id, full_name, email, password, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $staff_id, $full_name, $email, $password, $status);

                if ($stmt->execute()) {
                    header("Location: supervisor_login.php?registered=1");
                    exit();
                } else {
                    $error = "Registration failed.";
                }
            }
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Supervisor Registration</title>
<style>
body{
    margin:0;
    font-family:Arial;
    background:url('assets/images/unnamed.webp') no-repeat center center fixed;
    background-size:cover;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}
.overlay{
    position:absolute;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.7);
}
.card{
    position:relative;
    background:white;
    padding:30px;
    width:400px;
    border-radius:12px;
    z-index:1;
}
input{
    width:100%;
    padding:10px;
    margin-bottom:15px;
}
button{
    width:100%;
    padding:12px;
    background:#1e3a8a;
    color:white;
    border:none;
    cursor:pointer;
}
.error{color:red;}
a{text-decoration:none;color:#1e3a8a;}
/* Role selection header */
/* Back link */
.back-link { position: absolute; top: 20px; left: 20px; color: white; text-decoration: none; background: rgba(0,0,0,0.6); padding: 10px 15px; border-radius: 5px; font-weight: bold; z-index: 10; }
.back-link:hover { background: rgba(0,0,0,0.8); }
</style>
</head>
<body>

    <a href="index.php" class="back-link">← Back to Role Selection</a>

<div class="overlay"></div>

<div class="card">
<h2>Supervisor Registration</h2>

<?php if(!empty($error)) echo "<p class='error'>$error</p>"; ?>

<form method="POST">
    Full Name:
    <input type="text" name="full_name" placeholder="Enter your full name" required>

    Staff ID:
    <input type="text" name="staff_id" placeholder="Enter your staff ID" required>

    Official Email:
    <input type="email" name="email" placeholder="you@adelekeuniversity.edu.ng" required>

    Password:
    <input type="password" name="password" placeholder="Choose a password" required>

    <button type="submit">Register</button>
</form>

<p>Already have account? <a href="supervisor_login.php">Login</a></p>
</div>

</body>
</html>
