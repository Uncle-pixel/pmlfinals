<?php
session_start();

// Example: Check if the user is logged in (optional)
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Example: Fetch dynamic data for notifications (replace with your database logic)
$notifications = [
    [
        'title' => 'Midterm Exams Schedule',
        'date' => 'March 25, 2025',
        'content' => 'Midterm exams will be held from April 1 to April 5. Please check your schedule.'
    ],
    [
        'title' => 'System Maintenance',
        'date' => 'March 20, 2025',
        'content' => 'The portal will be unavailable on March 30 from 12:00 AM to 6:00 AM for maintenance.'
    ],
    [
        'title' => 'New Course Registration',
        'date' => 'March 15, 2025',
        'content' => 'Course registration for the next semester is now open. Deadline: April 10.'
    ]
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notifications - SPCF PORTAL</title>
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
        .notification {
            text-align: left;
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .notification h3 {
            margin: 0;
            color: #0073e6;
        }
        .notification p {
            margin: 5px 0;
        }
        .notification .date {
            font-size: 0.9em;
            color: #888;
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
            <a href="login.php">Login</a>
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <h1>
        <img src="logo.png" alt="SPCF Logo">
        <span>SPCF PORTAL</span>
    </h1>
    <nav>
        <ul>
            <li><a href="student_info.php">Student Information</a></li>
            <li><a href="course_registration.php">Course Registration</a></li>
            <li><a href="faculty_management.php">Faculty Management</a></li>
            <li><a href="grading_system.php">Grading System</a></li>
            <li><a href="class_scheduling.php">Class Scheduling</a></li>
            <li><a class="button" href="notifications.php">Notifications/ Announcements</a></li>
        </ul>
    </nav>
    <div class="content">
        <h2>Notifications & Announcements</h2>
        <p>Stay up to date with the latest announcements from the portal.</p>
        <?php foreach ($notifications as $notification): ?>
            <div class="notification">
                <h3><?php echo htmlspecialchars($notification['title']); ?></h3>
                <p class="date"><?php echo htmlspecialchars($notification['date']); ?></p>
                <p><?php echo htmlspecialchars($notification['content']); ?></p>
            </div>
        <?php endforeach; ?>
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
