<?php
include(__DIR__ . "/db.php"); // Database connection

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
        :root {
            --primary: #1d2d6f;
            --accent: #ffd453;
            --bg: #f3f7ff;
            --card: #ffffff;
            --text: #1f2d4b;
            --muted: #6b7a99;
        }

        * { box-sizing: border-box; }

        body, html {
            margin: 0;
            min-height: 100%;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(180deg, #dbe9ff 0%, #f3f7ff 100%);
            color: var(--text);
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .back-link {
            position: absolute;
            top: 20px;
            left: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(255,255,255,0.9);
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
            box-shadow: 0 18px 40px rgba(29,45,111,0.08);
        }

        .back-link:hover {
            transform: translateY(-1px);
            background: rgba(255,255,255,1);
        }

        .card {
            width: 100%;
            max-width: 440px;
            border-radius: 28px;
            background: var(--card);
            box-shadow: 0 35px 80px rgba(15,23,42,0.12);
            border: 1px solid rgba(29,45,111,0.08);
            padding: 40px 36px;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: "";
            position: absolute;
            top: -90px;
            right: -90px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255,212,83,0.18), transparent 55%);
            filter: blur(18px);
        }

        .brand {
            width: 70px;
            height: 70px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--primary), #2c3b93);
            color: #fff;
            margin: 0 auto 18px;
            box-shadow: 0 18px 40px rgba(29,45,111,0.18);
        }

        h2 {
            margin: 0 0 12px;
            text-align: center;
            font-size: 28px;
            letter-spacing: -0.4px;
        }

        p.subtitle {
            margin: 0 0 28px;
            text-align: center;
            color: var(--muted);
            line-height: 1.7;
        }

        .field { margin-bottom: 18px; }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #34415C;
        }

        input, select {
            width: 100%;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid #d8e1f3;
            background: #f7f9ff;
            color: var(--text);
            font-size: 15px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 5px rgba(29,45,111,0.08);
        }

        button {
            width: 100%;
            margin-top: 26px;
            padding: 14px 18px;
            border-radius: 16px;
            border: none;
            background: var(--accent);
            color: var(--primary);
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 30px rgba(255,181,0,0.2);
        }

        .text-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
        }

        .error {
            margin-bottom: 20px;
            padding: 14px 16px;
            background: #ffe7e8;
            border: 1px solid #f7c3c6;
            color: #9f2c2d;
            border-radius: 14px;
            text-align: center;
        }

        @media (max-width: 560px) {
            .card { padding: 30px 22px; }
            .brand { width: 60px; height: 60px; }
            h2 { font-size: 24px; }
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-link">← Back to Role Selection</a>
    <div class="card">
        <div class="brand">S</div>
        <h2>Student Registration</h2>
        <p class="subtitle">Create your account and wait for admin approval before logging in.</p>

        <?php if(!empty($error)) { echo "<div class='error'>$error</div>"; } ?>

        <form method="POST">
            <div class="field">
                <label for="full_name">Full Name</label>
                <input id="full_name" type="text" name="full_name" placeholder="Enter your full name" required>
            </div>
            <div class="field">
                <label for="email">Adeleke Email</label>
                <input id="email" type="email" name="email" placeholder="firstname.lastname@student.adelekeuniversity.edu.ng" required>
            </div>
            <div class="field">
                <label for="matric_number">Matric Number</label>
                <input id="matric_number" type="text" name="matric_number" placeholder="24/0856" required>
            </div>
            <div class="field">
                <label for="department">Department</label>
                <input id="department" type="text" name="department" placeholder="Computer Science" required>
            </div>
            <div class="field">
                <label for="level">Level</label>
                <select id="level" name="level" required>
                    <option value="">Select Level</option>
                    <option value="100">100</option>
                    <option value="200">200</option>
                    <option value="300">300</option>
                    <option value="400">400</option>
                    <option value="500">500</option>
                </select>
            </div>
            <div class="field">
                <label for="scholarship_status">Scholarship Status</label>
                <select id="scholarship_status" name="scholarship_status" required>
                    <option value="">Select Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="Choose a password" required>
            </div>
            <button type="submit">Create Account</button>
        </form>

        <a class="text-link" href="student_login.php">Already have an account? Login</a>
    </div>
</body>
</html>