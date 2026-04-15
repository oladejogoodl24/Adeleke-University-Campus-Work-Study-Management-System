<?php
include(__DIR__ . "/db.php"); // Connect to your database
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
        :root {
            --primary: #1d2d6f;
            --accent: #ffd453;
            --bg: #f3f7ff;
            --card: #ffffff;
            --text: #1f2d4b;
            --muted: #6b7a99;
        }

        * {
            box-sizing: border-box;
        }

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
            max-width: 420px;
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
            top: -120px;
            right: -90px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(255,212,83,0.18), transparent 55%);
            filter: blur(16px);
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

        .brand img {
            width: 42px;
            height: auto;
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

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #324069;
        }

        .field {
            margin-bottom: 18px;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid #d8e1f3;
            background: #f7f9ff;
            color: var(--text);
            font-size: 15px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 5px rgba(29,45,111,0.08);
        }

        .action {
            margin-top: 10px;
        }

        button {
            width: 100%;
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

        .note {
            text-align: center;
            margin-top: 22px;
            color: var(--muted);
            font-size: 14px;
        }

        .note a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
        }

        .error {
            margin-bottom: 20px;
            padding: 14px 16px;
            border-radius: 16px;
            background: #fff1f2;
            color: #b91c1c;
            border: 1px solid #fecdd3;
            text-align: center;
            font-weight: 600;
        }

        @media (max-width: 560px) {
            .card {
                padding: 30px 22px;
            }
            .brand {
                width: 60px;
                height: 60px;
            }
            h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-link">← Back to Role Selection</a>
    <div class="card">
        <div class="brand">
            <img src="assets/images/OIP.webp" alt="Adeleke University Logo">
        </div>
        <h2>Student Portal Login</h2>
        <p class="subtitle">Use your matric number and password to access your student work-study dashboard.</p>

        <?php if(!empty($error)) { echo "<div class='error'>$error</div>"; } ?>

        <form method="POST">
            <div class="field">
                <label for="matric">Matric Number</label>
                <input id="matric" type="text" name="matric" placeholder="24/0856" required>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="action">
                <button type="submit">Login</button>
            </div>
        </form>

        <p class="note">Don’t have an account? <a href="student_register.php">Sign up now</a></p>
    </div>
</body>
</html>