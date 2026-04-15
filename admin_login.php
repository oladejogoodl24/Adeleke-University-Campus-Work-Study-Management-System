<?php
include(__DIR__ . "/db.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $admin_id = $_POST["admin_id"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE admin_id=?");
    $stmt->bind_param("s", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $admin = $result->fetch_assoc();

       if (!password_verify(trim($password), $admin['password'])) {
            $error = "Incorrect password.";
        } else {
            $_SESSION['management_id'] = $admin['id'];
            $_SESSION['management_name'] = $admin['full_name'];
            $_SESSION['management_role'] = 'admin';
            header("Location: management/dashboard.php");
            exit();
        }

    } else {
        $error = "Admin ID not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<style>
body{
    margin:0;
    font-family:Arial;
    background:url('assets/images/campus.jpg') no-repeat center center fixed;
    background-size:cover;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    position: relative;
}
body::before {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    top: 0;
    left: 0;
    z-index: 0;
}
.card{
    background: rgba(0,0,0,0.7);
    color: white;
    padding:30px;
    width:400px;
    border-radius:12px;
    box-shadow: 0px 0px 15px rgba(0,0,0,0.5);
    backdrop-filter: blur(10px);
    position: relative;
    z-index: 1;
}
.card h2 {
    text-align: center;
    margin-bottom: 25px;
}
input{
    width:100%;
    padding:12px;
    margin-bottom:15px;
    border-radius: 5px;
    border: none;
}
button{
    width:100%;
    padding:12px;
    background:#1e3d59;
    color:white;
    border:none;
    cursor:pointer;
    border-radius: 5px;
    font-weight: bold;
}
button:hover {
    background: #163347;
}
.error{color:red; text-align: center; margin-bottom: 10px;}
/* Back link */
.back-link { position: absolute; top: 20px; left: 20px; color: white; text-decoration: none; background: rgba(0,0,0,0.6); padding: 10px 15px; border-radius: 5px; font-weight: bold; z-index: 10; }
.back-link:hover { background: rgba(0,0,0,0.8); }
</style>
</head>
<body>

    <a href="index.php" class="back-link">← Back to Role Selection</a>

<div class="card">
<h2>Admin Login</h2>

<?php if(!empty($error)) echo "<p class='error'>$error</p>"; ?>

<form method="POST">
Admin ID:
<input type="text" name="admin_id" required>

Password:
<input type="password" name="password" required>

<button type="submit">Login</button>
</form>
</div>

</body>
</html>