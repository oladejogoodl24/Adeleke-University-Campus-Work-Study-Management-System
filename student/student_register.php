<?php
include(__DIR__ . "/../db.php"); // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $matric = trim($_POST["matric_number"]);
    $department = trim($_POST["department"]);
    $level = trim($_POST["level"]);
    $scholarship = $_POST["scholarship_status"];
    $password = trim($_POST["password"]);

    if (empty($name) || empty($email) || empty($matric) || empty($department) || empty($level) || empty($scholarship) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!preg_match("/^[a-zA-Z]+\.[a-zA-Z]+@student\.adelekeuniversity\.edu\.ng$/", $email)) {
        $error = "Please use your Adeleke University student email (firstname.lastname@student.adelekeuniversity.edu.ng).";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO students (full_name, email, matric_number, department, level, scholarship_status, status, password) VALUES (?, ?, ?, ?, ?, ?, 'pending', ?)");
        $stmt->bind_param("sssssss", $name, $email, $matric, $department, $level, $scholarship, $hashed_password);

        if ($stmt->execute()) {
            header("Location: student_login.php");
            exit();
        } else {
            $error = "Registration failed. Email or matric number might already exist.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
    <style>
        /* Full page background image */
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        body {
            background: url('assets/images/unnamed.webp') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Card container */
        .card {
            background: rgba(255, 255, 255, 0.85);
            padding: 30px;
            width: 400px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            backdrop-filter: blur(10px);
        }

        .card h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #1e3d59;
        }

        .card input, .card select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
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
            margin-top: 10px;
            text-decoration: none;
            color: #1e3d59;
            font-weight: bold;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        /* Top back link */
        .top-link {
            position: absolute;
            top: 20px;
            right: 30px;
            background: rgba(30, 61, 89, 0.8);
            padding: 10px 15px;
            border-radius: 5px;
        }
        .top-link a {
            color: white;
            text-decoration: none;
            font-weight: bold;
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
                margin-top: 10px;
                text-decoration: none;
                color: #1e3d59;
                font-weight: bold;
            }

            .error {
                color: red;
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
    <h2>Create Your Account</h2>

    <?php if(!empty($error)) { echo "<div class='error'>$error</div>"; } ?>

    <form method="POST">
        Full Name:
        <input type="text" name="full_name" required>

        Adeleke Email:
        <input type="email" name="email" placeholder="firstname.lastname@student.adelekeuniversity.edu.ng" required>

        Matric Number:
        <input type="text" name="matric_number" required>

        Department:
        <input type="text" name="department" required>

        Level:
        <select name="level" required>
            <option value="">Select Level</option>
            <option value="100">100</option>
            <option value="200">200</option>
            <option value="300">300</option>
            <option value="400">400</option>
            <option value="500">500</option>
        </select>

        Scholarship Status:
        <select name="scholarship_status" required>
            <option value="">Select Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>

        Password:
        <input type="password" name="password" required>

        <button type="submit">Register</button>
    </form>

    <a href="student_login.php">Already have an account? Login</a>
</div>

</body>
</html>