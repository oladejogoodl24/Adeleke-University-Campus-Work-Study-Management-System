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

        .field { margin-bottom: 18px; }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #34415C;
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

        button {
            width: 100%;
            margin-top: 16px;
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
            margin-top: 18px;
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
        <div class="brand">
            <img src="assets/images/OIP.webp" alt="Adeleke University Logo">
        </div>
        <h2>Supervisor Registration</h2>
        <p class="subtitle">Register your supervisor account and wait for admin approval.</p>

        <?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>

        <form method="POST">
            <div class="field">
                <label for="full_name">Full Name</label>
                <input id="full_name" type="text" name="full_name" placeholder="Enter your full name" required>
            </div>
            <div class="field">
                <label for="staff_id">Staff ID</label>
                <input id="staff_id" type="text" name="staff_id" placeholder="Enter your staff ID" required>
            </div>
            <div class="field">
                <label for="email">Official Email</label>
                <input id="email" type="email" name="email" placeholder="you@adelekeuniversity.edu.ng" required>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="Choose a password" required>
            </div>
            <button type="submit">Create Account</button>
        </form>

        <a class="text-link" href="supervisor_login.php">Already have an account? Login</a>
    </div>
</body>
</html>
