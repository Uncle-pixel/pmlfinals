<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Example grades (replace with DB logic)
$grades = [
    ['student_name' => 'John Doe', 'course' => 'Introduction to Programming', 'grade' => 'A', 'status' => 'Passed'],
    ['student_name' => 'Jane Smith', 'course' => 'Calculus II', 'grade' => 'B', 'status' => 'Passed'],
    ['student_name' => 'Mark Johnson', 'course' => 'Physics I', 'grade' => 'F', 'status' => 'Failed']
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Grading System - SPCF PORTAL</title>
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
            z-index: 100;
        }
        .menu a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
        }
        .menu a:hover {
            background-color: #eee;
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
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .grades-table th,
        .grades-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .grades-table th {
            background-color: #0073e6;
            color: white;
        }
        .grades-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .grades-table tr:hover {
            background-color: #f1f1f1;
        }
        .status-passed {
            color: #27ae60;
            font-weight: bold;
        }
        .status-failed {
            color: #e74c3c;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Hamburger Menu -->
    <div class="hamburger-container">
        <div class="hamburger" onclick="toggleMenu()">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <div id="menu" class="menu">
            <a href="logout.php">Log Out</a>
        </div>
    </div>

    <!-- Header -->
    <h1>
        <img src="logo.png" alt="SPCF Logo">
        <span>SPCF PORTAL</span>
    </h1>

    <!-- Navigation -->
    <nav>
        <ul>
            <li><a href="student_info.php">Student Information</a></li>
            <li><a href="course_registration.php">Course Registration</a></li>
            <li><a href="faculty_management.php">Faculty Management</a></li>
            <li><a href="grading_system.php">Grading System</a></li>
            <li><a href="class_scheduling.php">Class Scheduling</a></li>
            <li><a href="notifications.php">Notifications</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="content">
        <h2>Grading System</h2>
        <p>View and manage student grades.</p>
        <table class="grades-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Grade</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grades as $grade): ?>
                <tr>
                    <td><?php echo htmlspecialchars($grade['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($grade['course']); ?></td>
                    <td><?php echo htmlspecialchars($grade['grade']); ?></td>
                    <td class="<?php echo $grade['status'] === 'Passed' ? 'status-passed' : 'status-failed'; ?>">
                        <?php echo htmlspecialchars($grade['status']); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleMenu() {
            var menu = document.getElementById("menu");
            menu.style.display = (menu.style.display === "block") ? "none" : "block";
        }
    </script>
</body>
</html>
