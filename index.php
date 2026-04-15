<!DOCTYPE html>
<html>
<head>
    <title>Adeleke University Work-Study Portal</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            height: 100vh;
            background: url('assets/images/R.jfif') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Dark overlay */
        body::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.65);
            top: 0;
            left: 0;
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 1;
            text-align: center;
            color: white;
            width: 90%;
            max-width: 1100px;
        }

        .container h1 {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .container p {
            font-size: 18px;
            margin-bottom: 50px;
            color: #ddd;
        }

        .cards {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 40px 30px;
            width: 280px;
            cursor: pointer;
            transition: 0.3s ease;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .card:hover {
            transform: translateY(-8px);
            background: rgba(255,255,255,0.2);
        }

        .card h3 {
            margin-bottom: 15px;
            font-size: 22px;
        }

        .card p {
            font-size: 14px;
            color: #eee;
        }

        .footer {
            margin-top: 60px;
            font-size: 13px;
            color: #ccc;
        }

        @media(max-width: 768px){
            .container h1 {
                font-size: 32px;
            }
        }
    </style>
</head>

<body>

<div class="container">
    <h1>Work-Study Portal</h1>
    <p>Adeleke University Campus Work-Study Management System</p>

    <div class="cards">
        <div class="card" onclick="location.href='student_login.php'">
            <h3>Student</h3>
            <p>Apply for jobs and track your work-study activities</p>
        </div>

        <div class="card" onclick="location.href='supervisor_login.php'">
            <h3>Supervisor</h3>
            <p>Manage job postings and approve student work hours</p>
        </div>

        <div class="card" onclick="location.href='admin_login.php'">
            <h3>Admin (Bursary/HR)</h3>
            <p>Manage system-wide approvals and reports</p>
        </div>
    </div>

    <div class="footer">
        © <?php echo date("Y"); ?> Adeleke University. All Rights Reserved.
    </div>
</div>

</body>
</html>