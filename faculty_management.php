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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            text-align: center;
        }
        h1 {
            background-color: #0073e6;
            color: white;
            padding: 20px;
            margin: 0;
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
    <h1>Faculty Management</h1>
    <nav>
        <ul>
            <li><a href="student_info.php">Student Information</a></li>
            <li><a href="course_registration.php">Course Registration</a></li>
            <li><a href="faculty_management.php">Faculty Management</a></li>
            <li><a href="grading_system.php">Grading System</a></li> <!-- Link to grading_system.php -->
            <li><a href="class_scheduling.php">Class Scheduling</a></li>
            <li><a href="notifications.php">Notifications</a></li>
            <li><a href="logout.php">Log Out</a></li>
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
</body>
</html>