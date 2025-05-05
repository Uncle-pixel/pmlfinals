<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Redirect if not a student
if ($_SESSION['user_role'] != 'student') {
    header("Location: dashboard.php");
    exit();
}

// Include database connection
$conn = new mysqli("localhost", "root", "", "spcf_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Initialize variables
$student_info = [];
$current_courses = [];
$notifications = [];
$schedule = [];
$success_message = "";
$error_message = "";

// Fetch student details
// Try both student_id and student_number to find the student
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ? OR student_number = ?");
$stmt->bind_param("is", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $student_info = $result->fetch_assoc();
} else {
    $error_message = "Unable to find student information. Please contact the administrator.";
}
$stmt->close();

// Fetch enrolled courses (mock data - in a real system, you'd query from enrollments table)
// This is simplified for demonstration purposes
$current_courses = [
    ['course_code' => 'CS101', 'course_name' => 'Introduction to Programming', 'schedule' => 'MWF 9:00 AM - 10:30 AM', 'instructor' => 'Prof. Smith', 'units' => 3],
    ['course_code' => 'MATH201', 'course_name' => 'Calculus II', 'schedule' => 'TTh 1:00 PM - 2:30 PM', 'instructor' => 'Prof. Johnson', 'units' => 4],
    ['course_code' => 'ENG105', 'course_name' => 'Technical Writing', 'schedule' => 'MWF 2:00 PM - 3:00 PM', 'instructor' => 'Prof. Williams', 'units' => 3]
];

// Fetch notifications (mock data - in a real system, you'd query from a notifications table)
$notifications = [
    ['id' => 1, 'title' => 'Midterm Exams Schedule', 'date' => 'May 01, 2025', 'content' => 'Midterm exams will be held from May 15 to May 20. Please check your schedule.', 'priority' => 'high', 'icon' => 'calendar-alt'],
    ['id' => 2, 'title' => 'System Maintenance', 'date' => 'April 28, 2025', 'content' => 'The portal will be unavailable on April 30 from 12:00 AM to 6:00 AM for maintenance.', 'priority' => 'medium', 'icon' => 'tools'],
    ['id' => 3, 'title' => 'New Course Registration', 'date' => 'April 25, 2025', 'content' => 'Course registration for the next semester is now open. Deadline: May 10.', 'priority' => 'high', 'icon' => 'book']
];

// Fetch schedule (mock data - in a real system, you'd build this from enrollments)
$schedule = [
    'Monday' => [
        ['time' => '9:00 AM - 10:30 AM', 'course' => 'CS101', 'room' => 'Room 302', 'instructor' => 'Prof. Smith'],
        ['time' => '2:00 PM - 3:00 PM', 'course' => 'ENG105', 'room' => 'Room 201', 'instructor' => 'Prof. Williams']
    ],
    'Tuesday' => [
        ['time' => '1:00 PM - 2:30 PM', 'course' => 'MATH201', 'room' => 'Room 105', 'instructor' => 'Prof. Johnson']
    ],
    'Wednesday' => [
        ['time' => '9:00 AM - 10:30 AM', 'course' => 'CS101', 'room' => 'Room 302', 'instructor' => 'Prof. Smith'],
        ['time' => '2:00 PM - 3:00 PM', 'course' => 'ENG105', 'room' => 'Room 201', 'instructor' => 'Prof. Williams']
    ],
    'Thursday' => [
        ['time' => '1:00 PM - 2:30 PM', 'course' => 'MATH201', 'room' => 'Room 105', 'instructor' => 'Prof. Johnson']
    ],
    'Friday' => [
        ['time' => '9:00 AM - 10:30 AM', 'course' => 'CS101', 'room' => 'Room 302', 'instructor' => 'Prof. Smith'],
        ['time' => '2:00 PM - 3:00 PM', 'course' => 'ENG105', 'room' => 'Room 201', 'instructor' => 'Prof. Williams']
    ]
];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - SPCF Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0052cc;
            --primary-light: #e6f0ff;
            --primary-dark: #003d99;
            --secondary-color: #f9f9f9;
            --accent-color: #ffcc00;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --warning-color: #ffc107;
            --light-text: #6c757d;
            --dark-text: #343a40;
            --border-color: #dee2e6;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition-speed: 0.3s;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: var(--dark-text);
            line-height: 1.6;
        }
        
        /* Header & Navigation */
        .header {
            background-color: var(--primary-color);
            color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 100;
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo img {
            height: 50px;
            margin-right: 1rem;
        }
        
        .logo h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }
        
        .nav-links {
            display: flex;
            background-color: var(--primary-dark);
            padding: 0.75rem 2rem;
        }
        
        .nav-links ul {
            display: flex;
            list-style: none;
            margin: 0 auto;
            padding: 0;
            max-width: 1400px;
        }
        
        .nav-links li {
            margin-right: 1.5rem;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 0.75rem;
            border-radius: 4px;
            transition: background-color var(--transition-speed);
        }
        
        .nav-links a:hover, .nav-links a.active {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .user-menu {
            display: flex;
            align-items: center;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            margin-right: 1rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .user-menu .button {
            background-color: var(--primary-dark);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color var(--transition-speed);
            text-decoration: none;
            display: inline-block;
        }
        
        .user-menu .button:hover {
            background-color: rgba(0, 0, 0, 0.2);
        }
        
        /* Mobile menu */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        /* Main content */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }
        
        /* Dashboard styles */
        .dashboard-header {
            margin-bottom: 2rem;
        }
        
        .dashboard-title {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .dashboard-subtitle {
            color: var(--light-text);
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .dashboard-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }
        
        .card-header {
            background-color: var(--primary-light);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-title {
            color: var(--primary-color);
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .card-title i {
            margin-right: 0.75rem;
        }
        
        .card-title-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .card-title-button {
            background-color: transparent;
            border: none;
            color: var(--primary-color);
            font-size: 1rem;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 4px;
            transition: background-color var(--transition-speed);
        }
        
        .card-title-button:hover {
            background-color: rgba(0, 82, 204, 0.1);
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Profile summary */
        .profile-summary {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.5rem;
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: bold;
        }
        
        .profile-details h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--dark-text);
        }
        
        .profile-details p {
            color: var(--light-text);
            margin-bottom: 0.25rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .info-item {
            padding: 1rem;
            background-color: var(--primary-light);
            border-radius: 6px;
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 0.8rem;
            color: var(--light-text);
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        /* Classes schedule */
        .schedule-tabs {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 1rem;
            overflow-x: auto;
            white-space: nowrap;
        }
        
        .schedule-tab {
            padding: 0.75rem 1.25rem;
            cursor: pointer;
            color: var(--light-text);
            font-weight: 500;
            position: relative;
        }
        
        .schedule-tab.active {
            color: var(--primary-color);
        }
        
        .schedule-tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--primary-color);
        }
        
        .schedule-content {
            display: none;
        }
        
        .schedule-content.active {
            display: block;
        }
        
        .schedule-list {
            margin-top: 1rem;
        }
        
        .schedule-item {
            padding: 1rem;
            border-radius: 6px;
            background-color: #f8f9fa;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
        }
        
        .schedule-time {
            min-width: 120px;
            padding-right: 1rem;
            border-right: 2px solid var(--primary-color);
            font-weight: 600;
        }
        
        .schedule-details {
            padding-left: 1rem;
            flex: 1;
        }
        
        .schedule-course {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .schedule-room, .schedule-instructor {
            font-size: 0.9rem;
            color: var(--light-text);
        }
        
        /* Courses */
        .course-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
        }
        
        .course-card {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 1.25rem;
            border-left: 4px solid var(--primary-color);
            transition: transform var(--transition-speed);
        }
        
        .course-card:hover {
            transform: translateY(-3px);
        }
        
        .course-code {
            font-size: 0.9rem;
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .course-name {
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        
        .course-details {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        
        .course-detail {
            display: flex;
            align-items: center;
        }
        
        .course-detail i {
            width: 20px;
            color: var(--light-text);
            margin-right: 0.5rem;
        }
        
        /* Notifications */
        .notification-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .notification-item {
            padding: 1rem;
            border-radius: 6px;
            background-color: #f8f9fa;
            display: flex;
            border-left: 4px solid var(--primary-color);
            position: relative;
        }
        
        .notification-item.high-priority {
            border-left-color: var(--danger-color);
        }
        
        .notification-item.medium-priority {
            border-left-color: var(--warning-color);
        }
        
        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: var(--primary-color);
        }
        
        .notification-item.high-priority .notification-icon {
            background-color: #f8d7da;
            color: var(--danger-color);
        }
        
        .notification-item.medium-priority .notification-icon {
            background-color: #fff3cd;
            color: var(--warning-color);
        }
        
        .notification-content {
            flex: 1;
        }
        
        .notification-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .notification-date {
            font-size: 0.8rem;
            color: var(--light-text);
            margin-bottom: 0.5rem;
        }
        
        .notification-text {
            font-size: 0.95rem;
        }
        
        .view-all-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem;
            border-radius: 4px;
            transition: background-color var(--transition-speed);
        }
        
        .view-all-link:hover {
            background-color: var(--primary-light);
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            transition: background-color var(--transition-speed);
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
        }
        
        .btn-secondary {
            background-color: white;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }
        
        .btn-secondary:hover {
            background-color: var(--primary-light);
        }
        
        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
        }
        
        /* Footer */
        .footer {
            background-color: var(--primary-dark);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
            text-align: center;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin: 1rem 0;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            margin: 0 1rem;
            text-decoration: none;
            transition: color var(--transition-speed);
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .copyright {
            margin-top: 1rem;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
        }
        
        /* Alert message */
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-icon {
            margin-right: 0.5rem;
        }
        
        /* Responsive design */
        @media (max-width: 992px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
            }
            
            .logo h1 {
                font-size: 1.2rem;
            }
            
            .mobile-menu-toggle {
                display: block;
            }
            
            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background-color: var(--primary-dark);
            }
            
            .nav-links.active {
                display: block;
            }
            
            .nav-links ul {
                flex-direction: column;
            }
            
            .nav-links li {
                margin: 0.5rem 0;
                width: 100%;
                text-align: center;
            }
            
            .user-menu {
                display: none;
            }
            
            .user-menu.active {
                display: flex;
                position: absolute;
                top: 100%;
                right: 0;
                background-color: var(--primary-dark);
                padding: 1rem;
                border-radius: 0 0 0 8px;
            }
            
            .profile-summary {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-avatar {
                margin-right: 0;
                margin-bottom: 1rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .schedule-time {
                min-width: auto;
                border-right: none;
                border-bottom: 2px solid var(--primary-color);
                padding-right: 0;
                padding-bottom: 0.5rem;
                margin-bottom: 0.5rem;
            }
            
            .schedule-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .schedule-details {
                padding-left: 0;
                margin-top: 0.5rem;
            }
            
            .course-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="navbar">
            <div class="logo">
                <img src="logo.png" alt="SPCF Logo" onerror="this.src='https://via.placeholder.com/50x50?text=SPCF'">
                <h1>SPCF PORTAL</h1>
            </div>
            
            <!-- Class Schedule Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-calendar-alt"></i> Class Schedule</h2>
                    <div class="card-title-buttons">
                        <a href="class_scheduling.php" class="card-title-button" title="View Full Schedule">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="schedule-tabs">
                        <?php 
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                        $current_day = date('l'); // Get current day name
                        
                        foreach ($days as $index => $day) {
                            $active = ($day === $current_day) ? 'active' : '';
                            echo "<div class='schedule-tab $active' data-day='$day'>$day</div>";
                        }
                        ?>
                    </div>
                    
                    <?php foreach ($days as $day): ?>
                        <div class="schedule-content <?php echo ($day === $current_day) ? 'active' : ''; ?>" id="<?php echo $day; ?>-schedule">
                            <?php if (isset($schedule[$day]) && !empty($schedule[$day])): ?>
                                <div class="schedule-list">
                                    <?php foreach ($schedule[$day] as $class): ?>
                                        <div class="schedule-item">
                                            <div class="schedule-time"><?php echo htmlspecialchars($class['time']); ?></div>
                                            <div class="schedule-details">
                                                <div class="schedule-course"><?php echo htmlspecialchars($class['course']); ?></div>
                                                <div class="schedule-room"><?php echo htmlspecialchars($class['room']); ?></div>
                                                <div class="schedule-instructor"><?php echo htmlspecialchars($class['instructor']); ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div style="text-align: center; padding: 2rem 0;">
                                    <i class="fas fa-coffee" style="font-size: 2rem; color: var(--light-text); margin-bottom: 1rem;"></i>
                                    <p>No classes scheduled for <?php echo $day; ?>.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <div style="margin-top: 1.5rem; text-align: center;">
                        <a href="class_scheduling.php" class="btn btn-secondary">View Full Schedule</a>
                    </div>
                </div>
            </div>
            
            <!-- Notifications Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-bell"></i> Notifications</h2>
                    <div class="card-title-buttons">
                        <a href="notifications.php" class="card-title-button" title="View All Notifications">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($notifications)): ?>
                        <div class="notification-list">
                            <?php foreach (array_slice($notifications, 0, 3) as $notification): ?>
                                <div class="notification-item <?php echo $notification['priority']; ?>-priority">
                                    <div class="notification-icon">
                                        <i class="fas fa-<?php echo htmlspecialchars($notification['icon']); ?>"></i>
                                    </div>
                                    <div class="notification-content">
                                        <div class="notification-title"><?php echo htmlspecialchars($notification['title']); ?></div>
                                        <div class="notification-date">
                                            <i class="far fa-clock"></i> <?php echo htmlspecialchars($notification['date']); ?>
                                        </div>
                                        <div class="notification-text"><?php echo htmlspecialchars($notification['content']); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="notifications.php" class="view-all-link">View All Notifications</a>
                    <?php else: ?>
                        <div style="text-align: center; padding: 2rem 0;">
                            <i class="fas fa-check-circle" style="font-size: 2rem; color: var(--light-text); margin-bottom: 1rem;"></i>
                            <p>No new notifications at this time.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="user-menu">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo substr($student_info['name'] ?? $_SESSION['user_name'] ?? 'S', 0, 1); ?>
                    </div>
                    <span><?php echo $student_info['name'] ?? $_SESSION['user_name'] ?? 'Student'; ?></span>
                </div>
                <a href="logout.php" class="button">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        
        <nav class="nav-links" id="navLinks">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="student_dashboard.php" class="active"><i class="fas fa-user-graduate"></i> My Information</a></li>
                <li><a href="course_registration.php"><i class="fas fa-book"></i> Course Registration</a></li>
                <li><a href="class_scheduling.php"><i class="fas fa-calendar-alt"></i> Class Schedule</a></li>
                <li><a href="grading_system.php"><i class="fas fa-chart-line"></i> Grades</a></li>
                <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
            </ul>
        </nav>
    </header>
    
    <!-- Main Container -->
    <div class="container">
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle alert-icon"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle alert-icon"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <div class="dashboard-header">
            <h1 class="dashboard-title">Student Dashboard</h1>
            <p class="dashboard-subtitle">Welcome back, <?php echo htmlspecialchars($student_info['name'] ?? 'Student'); ?>. Here's your academic overview.</p>
        </div>
        
        <div class="dashboard-grid">
            <!-- Student Information Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-user-graduate"></i> Student Information</h2>
                    <div class="card-title-buttons">
                        <a href="student_info.php" class="card-title-button" title="View Full Profile">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="profile-summary">
                        <div class="profile-avatar">
                            <?php echo substr($student_info['name'] ?? 'S', 0, 1); ?>
                        </div>
                        <div class="profile-details">
                            <h2><?php echo htmlspecialchars($student_info['name'] ?? 'Student Name'); ?></h2>
                            <p><strong>Student ID:</strong> <?php echo htmlspecialchars($student_info['student_number'] ?? 'N/A'); ?></p>
                            <p><strong>Program:</strong> <?php echo htmlspecialchars($student_info['program'] ?? 'N/A'); ?></p>
                            <p><strong>Year Level:</strong> <?php echo htmlspecialchars($student_info['year_level'] ?? 'N/A'); ?></p>
                        </div>
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Status</div>
                            <div class="info-value"><?php echo htmlspecialchars($student_info['status'] ?? 'Active'); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Enrollment Date</div>
                            <div class="info-value"><?php echo htmlspecialchars($student_info['enrollment_date'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?php echo htmlspecialchars($student_info['email'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Contact Number</div>
                            <div class="info-value"><?php echo htmlspecialchars($student_info['contact_number'] ?? 'N/A'); ?></div>
                        </div>
                    </div>
                    <div style="margin-top: 1.5rem; text-align: center;">
                        <a href="student_info.php" class="btn btn-secondary">View Complete Profile</a>
                    </div>
                </div>
            </div>