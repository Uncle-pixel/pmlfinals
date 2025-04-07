<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Debugging: Check session variables (remove this in production)
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Retrieve user's name or set default
$user_name = $_SESSION['user_name'] ?? 'Guest';
?>
<!DOCTYPE html>
<html>
<head>
    <title>SPCF PORTAL</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #ffffff, #0044cc);
            margin: 0;
            padding: 0;
            color: #0044cc;
            text-align: center;
        }
        h1 {
            background: #0044cc;
            color: white;
            padding: 20px;
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
            background: #005bb5;
            border-radius: 5px;
        }
        nav ul li a:hover {
            background: #003d80;
        }
    </style>
</head>
<body>
    <h1>Welcome to SPCF Portal</h1>
    <p>Welcome, <strong><?php echo htmlspecialchars($user_name); ?></strong>!</p>
    <nav>
        <ul>
            <li><a href="student_info.php">Student Information</a></li>
            <li><a href="course_registration.php">Course Registration</a></li>
            <li><a href="faculty_management.php">Faculty Management</a></li>
            <li><a href="grading_system.php">Grading System</a></li>
            <li><a href="class_scheduling.php">Class Scheduling</a></li>
            <li><a href="notifications.php">Notifications</a></li>
            <li><a href="logout.php">Log Out</a></li>
        </ul>
    </nav>
</body>
</html>
