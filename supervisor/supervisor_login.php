<?php
include __DIR__ . "/../db.php";  // ✅ Correct path to parent directory
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $staff_id = trim($_POST["staff_id"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT * FROM supervisors WHERE staff_id=?");
    $stmt->bind_param("s", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if (!password_verify($password, $user['password'])) {
            $error = "Incorrect password.";
        } elseif ($user['status'] != "approved") {
            $error = "Account pending approval.";
        } else {
            $_SESSION['management_id'] = $user['id'];
            $_SESSION['management_name'] = $user['full_name'];
            $_SESSION['management_role'] = $user['role']; // Use role from database
            
            // Additional admin detection: Check if staff_id matches admin pattern
            if (substr($user['staff_id'], 0, 3) === 'ADM' || $user['staff_id'] === 'admin') {
                $_SESSION['management_role'] = 'admin';
            }

            // REDIRECT TO SHARED MANAGEMENT DASHBOARD
            header("Location: ../management/dashboard.php");
            exit();
        }

    } else {
        $error = "Staff ID not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Supervisor Login</title>
<style>
body, html { height: 100%; margin: 0; font-family: Arial, sans-serif; }

body {
    background: url('../assets/images/campus.jpg') no-repeat center center fixed;
    background-size: cover;
    display: flex;
    justify-content: center;
    align-items: center;
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

.card {
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 30px;
    width: 400px;
    border-radius: 10px;
    box-shadow: 0px 0px 15px rgba(0,0,0,0.5);
    backdrop-filter: blur(8px);
    position: relative;
    z-index: 1;
}

.card img { display: block; margin: 0 auto 15px auto; width: 80px; height: auto; }
.card h2 { text-align: center; margin-bottom: 20px; }

.card input {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: none;
    background: rgba(255,255,255,0.95);
    color: #000;
}

.card button {
    width: 100%;
    padding: 12px;
    background: #1e3d59;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
}

.card button:hover { background: #163347; }

.card a {
    display: block;
    text-align: center;
    margin-top: 12px;
    color: #ffd700;
    text-decoration: none;
    font-weight: bold;
}

.error { color: #ff6666; text-align: center; margin-bottom: 10px; }

.back-link {
    position: absolute;
    top: 20px;
    left: 20px;
    color: white;
    text-decoration: none;
    background: rgba(0,0,0,0.6);
    padding: 10px 15px;
    border-radius: 5px;
    font-weight: bold;
    z-index: 10;
}
.back-link:hover { background: rgba(0,0,0,0.8); }
</style>
</head>
<body>

<a href="../index.php" class="back-link">← Back to Role Selection</a>

<div class="card">
    <img src="../assets/images/OIP.webp" alt="Adeleke University Logo">
    <h2>Supervisor Portal Login</h2>

    <?php 
    if(isset($_GET['registered'])) echo "<p style='color:#8fd19e;text-align:center;'>Registration successful. Await approval.</p>";
    if(!empty($error)) echo "<p class='error'>$error</p>";
    ?>

    <form method="POST">
        Staff ID:
        <input type="text" name="staff_id" placeholder="Enter your staff ID" required>

        Password:
        <input type="password" name="password" placeholder="Enter your password" required>

        <button type="submit">Login</button>
    </form>

    <a href="supervisor_register.php">Don't have an account? Sign up</a>
</div>

</body>
</html>