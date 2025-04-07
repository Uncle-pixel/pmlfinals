<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Example: Fetch dynamic data for courses (replace with DB query later)
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
    <title>Course Registration - SPCF Portal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        h1 {
            background-color: #0044cc;
            color: white;
            padding: 20px;
            text-align: center;
        }
        nav {
            background-color: #005bb5;
            padding: 15px;
            text-align: center;
        }
        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
        }
        nav a:hover {
            color: #ffd700;
        }
        .container {
            max-width: 1000px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .course-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
        }
        .course-card h3 {
            color: #0044cc;
        }
        .add-btn {
            padding: 10px 20px;
            border: none;
            background: #27ae60;
            color: white;
            border-radius: 25px;
            cursor: pointer;
        }
        .add-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <h1>Course Registration</h1>
    <nav>
        <a href="student_info.php">Student Info</a>
        <a href="course_registration.php">Course Registration</a>
        <a href="faculty_management.php">Faculty Management</a>
        <a href="grading_system.php">Grading</a>
        <a href="class_scheduling.php">Scheduling</a>
        <a href="notifications.php">Notifications</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <h2>Available Courses</h2>
        <?php foreach ($courses as $course): ?>
            <div class="course-card">
                <div>
                    <h3><?php echo htmlspecialchars($course['course_name']); ?> (<?php echo $course['course_code']; ?>)</h3>
                    <p><?php echo htmlspecialchars($course['description']); ?></p>
                </div>
                <button class="add-btn" <?php echo $course['status'] === 'Full' ? 'disabled' : ''; ?>>
                    <?php echo $course['status'] === 'Full' ? 'Full' : 'Add Course'; ?>
                </button>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>