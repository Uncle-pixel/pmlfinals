<?php
session_start();

// Example: Check if the user is logged in (optional)
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Example: Fetch dynamic data for student information (replace with your database logic)
$student_info = [
    'name' => 'John Doe',
    'student_id' => '20230001',
    'email' => 'john.doe@spcf.edu',
    'course' => 'Bachelor of Science in Computer Science',
    'year_level' => '3rd Year',
    'status' => 'Active'
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Information - SPCF PORTAL</title>
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
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .info-table th,
        .info-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .info-table th {
            background-color: #0073e6;
            color: white;
        }
        .info-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .info-table tr:hover {
            background-color: #f1f1f1;
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
            <a href="profile.php">Profile</a>
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
        <h2>Student Information</h2>
        <table class="info-table">
            <tr>
                <th>Name</th>
                <td><?php echo htmlspecialchars($student_info['name']); ?></td>
            </tr>
            <tr>
                <th>Student ID</th>
                <td><?php echo htmlspecialchars($student_info['student_id']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($student_info['email']); ?></td>
            </tr>
            <tr>
                <th>Course</th>
                <td><?php echo htmlspecialchars($student_info['course']); ?></td>
            </tr>
            <tr>
                <th>Year Level</th>
                <td><?php echo htmlspecialchars($student_info['year_level']); ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><?php echo htmlspecialchars($student_info['status']); ?></td>
            </tr>
        </table>
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