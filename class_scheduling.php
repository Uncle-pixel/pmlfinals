<!-- filepath: c:\xampp\htdocs\pmlfinals\class_scheduling.php -->
<?php
// Start the session to manage user authentication or other session data
session_start();

// Example: Check if the user is logged in (optional)
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.html");
    exit();
}

// Example: Fetch dynamic data for the schedule (replace with your database logic)
$schedules = [
    [
        'course_code' => 'CS101',
        'course_name' => 'Introduction to Programming',
        'schedule' => 'MWF 9:00 AM - 10:30 AM',
        'room' => 'Room 302',
        'instructor' => 'Prof. Smith',
        'status' => 'Active'
    ],
    [
        'course_code' => 'MATH201',
        'course_name' => 'Calculus II',
        'schedule' => 'TTH 1:00 PM - 2:30 PM',
        'room' => 'Room 105',
        'instructor' => 'Prof. Johnson',
        'status' => 'Completed'
    ]
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Class Scheduling - SPCF PORTAL</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Add your CSS styles here (same as in the original HTML file) */
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f6fa;
            color: #2c3e50;
        }

        /* Hamburger Menu Styles */
        .hamburger-container {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }

        .hamburger div {
            width: 30px;
            height: 3px;
            background-color: #2c3e50;
            margin: 5px 0;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        .menu {
            display: none;
            position: absolute;
            left: 0;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            padding: 10px;
            width: 150px;
        }

        .menu a {
            display: block;
            padding: 8px 12px;
            color: #2c3e50;
            text-decoration: none;
            transition: background 0.3s;
        }

        .menu a:hover {
            background: #f5f6fa;
            border-radius: 4px;
        }

        /* Header Styles */
        h1 {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #0073e6;
            color: white;
            padding: 20px;
            margin: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h1 img {
            height: 50px;
            margin-right: 10px;
        }

        /* Navigation Styles */
        nav {
            background-color: #005bb5;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
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
            transition: all 0.3s ease;
            font-weight: 500;
        }

        nav ul li a:hover {
            background-color: #003d80;
        }

        nav ul li a.button {
            background-color: #ff4500;
        }

        nav ul li a.button:hover {
            background-color: #e63900;
        }

        /* Content Styles */
        .content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        h2 {
            color: #2980b9;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        /* Schedule Table Styles */
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .schedule-table th,
        .schedule-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .schedule-table th {
            background: #2980b9;
            color: white;
        }

        .schedule-table tr:hover {
            background: #f8f9fa;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            nav ul {
                flex-wrap: wrap;
                gap: 10px;
            }

            nav a {
                padding: 8px 15px;
                font-size: 14px;
            }

            .content {
                padding: 20px;
            }
        }

        /* Search and Filters */
        .schedule-controls {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .search-box {
            flex: 1;
            padding: 10px;
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

        .term-select {
            padding: 10px 15px;
            border-radius: 25px;
            border: 1px solid #ddd;
            background: white;
        }

        /* Status Indicators */
        .status-indicator {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
        }

        .status-active {
            background: #27ae60;
            color: white;
        }

        .status-completed {
            background: #7f8c8d;
            color: white;
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
            <a href="#"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="#"><i class="fas fa-sign-out-alt"></i> Log Out</a>
            <a href="#"><i class="fas fa-user"></i> Profile</a>
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
            <li><a href="faculty_management.html">Faculty Management</a></li>
            <li><a href="grading_system.html">Grading System</a></li>
            <li><a class="active" href="class_scheduling.php">Class Scheduling</a></li>
            <li><a class="button" href="notifications.html">Notifications</a></li>
        </ul>
    </nav>
    <div class="content">
        <h2>Class Scheduling</h2>
        <div class="schedule-controls">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search classes...">
            </div>
            <select class="term-select">
                <option>Spring 2023</option>
                <option>Fall 2023</option>
                <option>Winter 2023</option>
            </select>
        </div>
        <table class="schedule-table">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Schedule</th>
                    <th>Room</th>
                    <th>Instructor</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schedules as $schedule): ?>
                <tr>
                    <td><?php echo htmlspecialchars($schedule['course_code']); ?></td>
                    <td><?php echo htmlspecialchars($schedule['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($schedule['schedule']); ?></td>
                    <td><?php echo htmlspecialchars($schedule['room']); ?></td>
                    <td><?php echo htmlspecialchars($schedule['instructor']); ?></td>
                    <td>
                        <span class="status-indicator <?php echo $schedule['status'] === 'Active' ? 'status-active' : 'status-completed'; ?>">
                            <?php echo htmlspecialchars($schedule['status']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleMenu() {
            const menu = document.getElementById('menu');
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</body>
</html>