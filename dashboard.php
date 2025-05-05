<?php
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php"); // Redirect to login page after logging out
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Session timeout after 30 minutes of inactivity
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: login.php?message=session_expired");
    exit();
}
$_SESSION['last_activity'] = time();

// Get user information from session
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];
$can_edit = $_SESSION['can_edit'];
$can_view = $_SESSION['can_view'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "spcf_portal"; // Adjust database name as needed

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch announcements
$announcements = [];
$result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}

// Fetch courses (example data - adjust query based on your schema)
$courses = [];
$result = $conn->query("SELECT * FROM courses ORDER BY course_code");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPCF Portal - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #0052cc;
            --secondary-color: #f9f9f9;
            --accent-color: #00bfa5;
            --text-color: #333;
            --light-text: #777;
            --border-color: #ddd;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            color: var(--text-color);
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #fff;
            border-right: 1px solid var(--border-color);
            padding: 20px 0;
            position: fixed;
            height: 100%;
            overflow-y: auto;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
        }
        
        .sidebar-header img {
            width: 40px;
            margin-right: 10px;
        }
        
        .school-name {
            font-size: 16px;
            font-weight: 600;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-category {
            color: var(--light-text);
            font-size: 12px;
            text-transform: uppercase;
            padding: 10px 20px;
            letter-spacing: 0.5px;
        }
        
        .menu-item {
            padding: 10px 20px;
            display: flex;
            align-items: center;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .menu-item:hover, .menu-item.active {
            background-color: #f0f4f8;
            color: var(--primary-color);
            border-left: 4px solid var(--primary-color);
        }
        
        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Main content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 20px;
        }
        
        .welcome-message {
            font-size: 24px;
            font-weight: 600;
        }
        
        .role-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .role-student {
            background-color: #e3f2fd;
            color: #1565c0;
        }
        
        .role-faculty {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .role-admin {
            background-color: #fce4ec;
            color: #c2185b;
        }
        
        .user-actions {
            display: flex;
            align-items: center;
        }
        
        .notification-bell {
            background: none;
            border: none;
            color: #666;
            font-size: 18px;
            margin-right: 20px;
            position: relative;
            cursor: pointer;
        }
        
        .notification-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--accent-color);
            color: white;
            font-size: 10px;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        /* Dashboard widgets */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .dashboard-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 20px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: 600;
        }
        
        .card-actions a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
        }
        
        .announcement {
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .announcement:last-child {
            border-bottom: none;
        }
        
        .announcement-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .announcement-meta {
            font-size: 12px;
            color: var(--light-text);
        }
        
        /* Table styles */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .data-table th, .data-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        .data-table th {
            background-color: #f9f9f9;
            font-weight: 600;
        }
        
        .data-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .btn {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .btn i {
            margin-right: 5px;
        }
        
        .btn-view {
            background-color: #e3f2fd;
            color: #1565c0;
            border: 1px solid #bbdefb;
        }
        
        .btn-edit {
            background-color: #fff8e1;
            color: #f57f17;
            border: 1px solid #ffecb3;
        }
        
        .btn-delete {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
        
        .disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Footer */
        .footer {
            border-top: 1px solid var(--border-color);
            padding-top: 20px;
            text-align: center;
            color: var(--light-text);
            font-size: 14px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="school-logo.png" alt="SPCF Logo">
                <div class="school-name">SPCF Portal</div>
            </div>
            
            <nav class="sidebar-menu">
                <div class="menu-category">Main</div>
                <a href="index.php" class="menu-item">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="dashboard.php" class="menu-item active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                
                <div class="menu-category">Academic</div>
                <a href="student_info.php" class="menu-item">
                    <i class="fas fa-user-graduate"></i> Student Information
                </a>
                <a href="grading_system.php" class="menu-item">
                    <i class="fas fa-chart-line"></i> Grading System
                </a>
                
                <div class="menu-category">Administration</div>
                <a href="faculty_management.php" class="menu-item">
                    <i class="fas fa-chalkboard-teacher"></i> Faculty Management
                </a>
                <a href="notifications.php" class="menu-item">
                    <i class="fas fa-bell"></i> Notifications
                </a>
                
                <div class="menu-category">Account</div>
                <a href="profile.php" class="menu-item">
                    <i class="fas fa-user-circle"></i> Profile
                </a>
                <a href="dashboard.php?logout=1" class="menu-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <div>
                    <h1 class="welcome-message">Welcome, <?php echo htmlspecialchars($user_name); ?></h1>
                    <span class="role-badge role-<?php echo strtolower($user_role); ?>"><?php echo ucfirst($user_role); ?></span>
                </div>
                <div class="user-actions">
                    <button class="notification-bell">
                        <i class="fas fa-bell"></i>
                        <span class="notification-count">3</span>
                    </button>
                    <div class="user-menu">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                        </div>
                        <span><?php echo htmlspecialchars($user_name); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Dashboard Content -->
            <div class="dashboard-grid">
                <!-- Announcements Card -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2 class="card-title">Announcements</h2>
                    </div>
                    <?php if (!empty($announcements)): ?>
                        <?php foreach ($announcements as $announcement): ?>
                        <div class="announcement">
                            <div class="announcement-title"><?php echo htmlspecialchars($announcement['title']); ?></div>
                            <p><?php echo substr(htmlspecialchars($announcement['content']), 0, 100) . '...'; ?></p>
                            <div class="announcement-meta">
                                <span><i class="far fa-clock"></i> <?php echo date('M d, Y', strtotime($announcement['created_at'])); ?></span> • 
                                <span><i class="far fa-user"></i> <?php echo htmlspecialchars($announcement['posted_by']); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No announcements available.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Quick Links Card -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2 class="card-title">Quick Links</h2>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <a href="#" style="text-decoration: none;">
                            <div style="background-color: #e3f2fd; padding: 15px; border-radius: 5px; text-align: center;">
                                <i class="fas fa-book" style="font-size: 24px; color: #1565c0;"></i>
                                <div style="margin-top: 5px; color: #333;">Library</div>
                            </div>
                        </a>
                        <a href="#" style="text-decoration: none;">
                            <div style="background-color: #e8f5e9; padding: 15px; border-radius: 5px; text-align: center;">
                                <i class="fas fa-calendar-alt" style="font-size: 24px; color: #2e7d32;"></i>
                                <div style="margin-top: 5px; color: #333;">Calendar</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="footer">
                <p>&copy; <?php echo date('Y'); ?> SPCF Portal. All rights reserved.</p>
            </div>
        </main>
    </div>
</body>
</html>
