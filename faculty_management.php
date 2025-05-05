<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Define roles
$roles = [
    'admin' => 'admin',
    'faculty' => 'faculty',
    'student' => 'student'
];

// Check user role
$user_role = $_SESSION['user_role'] ?? 'student'; // Default to student if not set
$logged_in_user = $_SESSION['user_id'] ?? null;

// Define faculty list based on user role
if ($user_role === 'admin') {
    $facList = [
        [
            'name' => 'Dr. John Smith',
            'department' => 'Computer Science',
            'email' => 'john.smith@spcf.edu',
            'status' => 'Active'
        ],
        [
            'name' => 'Dr. Jane Doe',
            'department' => 'Mathematics',
            'email' => 'jane.doe@spcf.edu',
            'status' => 'Active'
        ]
    ];
} else {
    $facList = [
        [
            'name' => 'Dr. John Smith',
            'department' => 'Computer Science',
            'email' => 'john.smith@spcf.edu',
            'status' => 'Blocked'
        ],
        [
            'name' => 'Dr. Jane Doe',
            'department' => 'Mathematics',
            'email' => 'jane.doe@spcf.edu',
            'status' => 'Active'
        ]
    ];
}

// Delete faculty functionality for admin
if ($user_role === 'admin' && isset($_GET['delete_faculty'])) {
    $deleted_id = $_GET['delete_faculty'];
    if (isset($facList[$deleted_id])) {
        unset($facList[$deleted_id]);
    }
    header("Location: faculty_management.php");
    exit();
}

// Edit functionality for faculty
if ($user_role === 'faculty' && isset($_POST['submit'])) {
    $facList[$logged_in_user]['name'] = $_POST['name'];
    $facList[$logged_in_user]['email'] = $_POST['email'];
    $facList[$logged_in_user]['status'] = $_POST['status'];
}

// Close the PHP tag properly
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Management Portal</title>
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

        /* Add styles for editing faculty management */
        .edit-form {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .edit-form label {
            margin-bottom: 10px;
        }
        .edit-form input[type="text"], .edit-form select {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .edit-form input[type="submit"] {
            padding: 10px 20px;
            background-color: #005bb5;
            color: white;
            border: none;
            border-radius: 5px;
        }
        .edit-form input[type="submit"]:hover {
            background-color: #003d80;
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
            <li><a href="grading_system.php">Grading System</a></li>
            <li><a href="class_scheduling.php">Class Scheduling</a></li>
            <li><a href="notifications.php">Notifications</a></li>
            <li><a href="logout.php">Log Out</a></li>
        </ul>
    </nav>
    <div class="content">
        <h2>Faculty Management</h2>
        <p>Manage and view the details of the faculty members.</p>
        <div class="faculty-list">
            <?php foreach ($facList as $index => $faculty): ?>
            <div class="faculty-card">
                <h3><?php echo htmlspecialchars($faculty['name']); ?></h3>
                <p>Department: <?php echo htmlspecialchars($faculty['department']); ?></p>
                <p>Email: <?php echo htmlspecialchars($faculty['email']); ?></p>
                <p class="status <?php echo $faculty['status'] === 'On Leave' ? 'on-leave' : ''; ?>">
                    Status: <?php echo htmlspecialchars($faculty['status']); ?>
                </p>
                <?php if ($user_role === 'admin'): ?>
                    <a href="?delete_faculty=<?php echo $index; ?>" onclick="return confirm('Are you sure you want to delete?')">Delete</a>
                    <a href="edit_faculty.php?editor=<?php echo $index; ?>">Edit</a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if ($user_role === 'admin'): ?>
            <button onclick="window.location.href='add_faculty.php'">Add New Faculty</button>
            <button onclick="window.location.href='edit_all_faculties.php'">Edit All Faculties</button>
        <?php endif; ?>
        <?php if ($user_role === 'faculty'): ?>
            <!-- Display edit form only for faculty members -->
            <div class="edit-form">
                <h2>Edit Your Details</h2>
                <form action="" method="POST">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($facList[$logged_in_user]['name']); ?>">
                    <br><br>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($facList[$logged_in_user]['email']); ?>">
                    <br><br>
                    <label for="status">Status:</label>
                    <select id="status" name="status">
                        <option value="Active" <?php if ($facList[$logged_in_user]['status'] === 'Active'): ?>selected<?php endif; ?>>Active</option>
                        <option value="On Leave" <?php if ($facList[$logged_in_user]['status'] === 'On Leave'): ?>selected<?php endif; ?>>On Leave</option>
                    </select>
                    <br><br>
                    <input type="submit" name="submit" value="Save Changes">
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>