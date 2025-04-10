<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Example: Fetch dynamic data for faculty members (replace with your database logic)
$faculty_members = [
    [
        'name' => 'Dr. John Smith',
        'department' => 'Computer Science',
        'email' => 'john.smith@spcf.edu',
        'status' => 'Active'
    ],
    [
        'name' => 'Prof. Jane Doe',
        'department' => 'Mathematics',
        'email' => 'jane.doe@spcf.edu',
        'status' => 'On Leave'
    ]
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Faculty Management - SPCF PORTAL</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            text-align: center;
        }
        .hamburger-container {
            position: absolute;
            top: 10px;
            left: 10px;
        }
        .hamburger {
            display: inline-block;
            cursor: pointer;
        }
        .hamburger div {
            width: 30px;
            height: 4px;
            background-color: #333;
            margin: 6px 0;
        }
        .menu {
            display: none;
            position: absolute;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 10px;
        }
        .menu a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
        }
        h1 {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #0073e6;
            color: white;
            padding: 20px;
            margin: 0;
        }
        h1 img {
            height: 50px;
            margin-right: 10px;
        }
        nav {
            background-color: #005bb5;
            padding: 15px;
        }
        nav ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            margin: 0;
        }
        nav ul li {
            margin: 0 15px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
        }
        nav ul li a:hover {
            background-color: #003d80;
        }
        .content {
            margin: 20px;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .faculty-list {
            margin-top: 20px;
            text-align: left;
        }
        .faculty-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .faculty-card h3 {
            margin: 0;
            color: #0073e6;
        }
        .faculty-card p {
            margin: 5px 0;
        }
        .faculty-card .status {
            font-weight: bold;
            color: #27ae60;
        }
        .faculty-card .status.on-leave {
            color: #e67e22;
        }
    </style>
</head>
<body>
    <div class="hamburger-container">
        <div class="hamburger" onclick="toggleMenu()">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <div id="menu" class="menu">
            <a href="#">Login</a>
            <a href="#">Log Out</a>
            <a href="#">Profile</a>
        </div>
    </div>
    <h1>
        <img src="logo.png" alt="SPCF Logo">
        <span>SPCF PORTAL</span>
    </h1>
    <nav>
        <ul>
            <li><a href="student_info.html">Student Information</a></li>
            <li><a href="course_registration.html">Course Registration</a></li>
            <li><a href="faculty_management.php">Faculty Management</a></li>
            <li><a href="grading_system.html">Grading System</a></li>
            <li><a href="class_scheduling.html">Class Scheduling</a></li>
            <li><a class="button" href="notifications.html">Notifications/ Announcements</a></li>
        </ul>
    </nav>
    <div class="content">
        <h2>Faculty Management</h2>
        <p>Manage and view the details of the faculty members.</p>
        <div class="faculty-list">
            <?php foreach ($faculty_members as $faculty): ?>
            <div class="faculty-card">
                <h3><?php echo htmlspecialchars($faculty['name']); ?></h3>
                <p>Department: <?php echo htmlspecialchars($faculty['department']); ?></p>
                <p>Email: <?php echo htmlspecialchars($faculty['email']); ?></p>
                <p class="status <?php echo $faculty['status'] === 'On Leave' ? 'on-leave' : ''; ?>">
                    Status: <?php echo htmlspecialchars($faculty['status']); ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        function toggleMenu() {
            var menu = document.getElementById("menu");
            if (menu.style.display === "block") {
                menu.style.display = "none";
            } else {
                menu.style.display = "block";
            }
        }
    </script>
</body>
</html>