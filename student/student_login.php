<?php
include(__DIR__ . "/../db.php"); // Connect to your database
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matric = trim($_POST["matric"]);
    $password = trim($_POST["password"]);

    if (empty($matric) || empty($password)) {
        $error = "Both fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM students WHERE matric_number=?");
        $stmt->bind_param("s", $matric);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (!password_verify($password, $user['password'])) {
                $error = "Incorrect password.";
            } elseif ($user['status'] != "approved") {
                $error = "Your account is still pending admin approval.";
            } elseif ($user['scholarship_status'] != "active") {
                $error = "You are not eligible to login. Scholarship inactive.";
            } else {
                // Login success
                $_SESSION['student_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                header("Location: student_dashboard.php");
                exit();
            }
        } else {
            $error = "Matric number not found.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Login</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: url('assets/images/campus.jpg') no-repeat center center fixed;
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
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1;
        }

        .card img {
            display: block;
            margin: 0 auto 15px auto;
            width: 80px;
            height: auto;
        }

        .card h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .card input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: none;
        }

        .card button {
            width: 100%;
            padding: 12px;
            background: #1e3d59;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .card button:hover {
            background: #163347;
        }

        .card a {
            display: block;
            text-align: center;
            margin-top: 12px;
            color: #ffd700;
            text-decoration: none;
            font-weight: bold;
        }

        .error {
            color: #ff6666;
            text-align: center;
            margin-bottom: 10px;
        }
        /* Back link */
        .back-link { position: absolute; top: 20px; left: 20px; color: white; text-decoration: none; background: rgba(0,0,0,0.6); padding: 10px 15px; border-radius: 5px; font-weight: bold; z-index: 10; }
        .back-link:hover { background: rgba(0,0,0,0.8); }
    </style>
</head>
<body>
    <a href="index.php" class="back-link">← Back to Role Selection</a>

<div class="card">
    <img src="assets/images/OIP.webp" alt="Adeleke University Logo">
    <h2>Student Portal Login</h2>

    <?php if(!empty($error)) { echo "<div class='error'>$error</div>"; } ?>

    <form method="POST">
        Matric Number:
        <input type="text" name="matric" placeholder="Enter your matric number" required>

        Password:
        <input type="password" name="password" placeholder="Enter your password" required>

        <button type="submit">Login</button>
    </form>

    <a href="student_register.php">Don't have an account? Sign up</a>
</div>

</body>
</html>