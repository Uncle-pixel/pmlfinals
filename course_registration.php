<?php
session_start();

// Example: Check if the user is logged in (optional)
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.html");
    exit();
}

// Example: Fetch dynamic data for courses (replace with your database logic)
$courses = [
    [
        'course_code' => 'CS101',
        'course_name' => 'Introduction to Programming',
        'description' => 'Learn the basics of programming using Python.',
        'status' => 'Available'
    ],
    [
        'course_code' => 'MATH201',
        'course_name' => 'Calculus II',
        'description' => 'Advanced calculus topics for engineering students.',
        'status' => 'Full'
    ]
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Course Registration - SPCF PORTAL</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #f5f6fa;
            color: #2c3e50;
            text-align: center;
        }
        .hamburger-container {
            position: absolute;
            top: 10px;
            left: 10px;
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
            max-width: 1200px;
            margin: 20px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .search-filter {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            justify-content: center;
        }
        .search-box {
            flex: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 25px;
            display: flex;
            align-items: center;
        }
        .search-box input {
            border: none;
            outline: none;
            flex: 1;
            padding-left: 10px;
        }
        .filter-select {
            padding: 12px 15px;
            border-radius: 25px;
            border: 1px solid #ddd;
            background: white;
            min-width: 180px;
        }
        .course-list {
            display: grid;
            gap: 15px;
        }
        .course-card {
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s ease;
        }
        .course-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .course-info h3 {
            color: #2980b9;
            margin-bottom: 5px;
        }
        .add-course-btn {
            background: #27ae60;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .add-course-btn:hover {
            background: #219a52;
        }
        .registration-controls {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .btn-primary {
            background: #2980b9;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .btn-primary:hover {
            background: #2471a3;
        }
    </style>
</head>
<body>
    <div class="hamburger-container" onclick="toggleMenu()">
        <div class="hamburger">
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
            <li><a href="course_registration.php">Course Registration</a></li>
            <li><a href="faculty_management.html">Faculty Management</a></li>
            <li><a href="grading_system.html">Grading System</a></li>
            <li><a href="class_scheduling.html">Class Scheduling</a></li>
            <li><a class="button" href="notifications.html">Notifications</a></li>
        </ul>
    </nav>
    <div class="content">
        <h2>Course Registration</h2>
        <p>Manage and register for your courses efficiently.</p>
        <div class="course-list">
            <?php foreach ($courses as $course): ?>
            <div class="course-card">
                <div class="course-info">
                    <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
                    <p><?php echo htmlspecialchars($course['description']); ?></p>
                </div>
                <button class="add-course-btn" <?php echo $course['status'] === 'Full' ? 'disabled' : ''; ?>>
                    <?php echo $course['status'] === 'Full' ? 'Full' : 'Add Course'; ?>
                </button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        function toggleMenu() {
            var menu = document.getElementById("menu");
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        }
    </script>
</body>
</html>
