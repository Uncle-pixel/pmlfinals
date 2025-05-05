<?php
session_start();

// Example: Check if the user is logged in (optional)
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Example: Fetch dynamic data for notifications (replace with your database logic)
$notifications = [
    [
        'title' => 'Midterm Exams Schedule',
        'date' => 'March 25, 2025',
        'content' => 'Midterm exams will be held from April 1 to April 5. Please check your schedule.',
        'priority' => 'high',
        'icon' => 'calendar'
    ],
    [
        'title' => 'System Maintenance',
        'date' => 'March 20, 2025',
        'content' => 'The portal will be unavailable on March 30 from 12:00 AM to 6:00 AM for maintenance.',
        'priority' => 'medium',
        'icon' => 'tools'
    ],
    [
        'title' => 'New Course Registration',
        'date' => 'March 15, 2025',
        'content' => 'Course registration for the next semester is now open. Deadline: April 10.',
        'priority' => 'high',
        'icon' => 'book'
    ]
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notifications - SPCF PORTAL</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a56db;
            --primary-dark: #1e429f;
            --secondary: #e5edff;
            --accent: #f59e0b;
            --text: #333;
            --text-light: #6b7280;
            --light-bg: #f9fafb;
            --white: #ffffff;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --radius: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: var(--text);
            line-height: 1.6;
        }

        /* Header Styles */
        header {
            background-color: var(--white);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-container img {
            height: 50px;
            margin-right: 1rem;
        }

        .logo-container h1 {
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 600;
        }

        /* Sidebar and Navigation */
        .page-container {
            display: flex;
            max-width: 1400px;
            margin: 2rem auto;
            gap: 2rem;
            padding: 0 1rem;
        }

        .sidebar {
            width: 280px;
            background-color: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .sidebar-header .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            color: var(--primary);
            font-weight: bold;
        }

        .sidebar-header .user-info {
            display: flex;
            flex-direction: column;
        }

        .sidebar-header .user-name {
            font-weight: 600;
            font-size: 1rem;
        }

        .sidebar-header .user-role {
            font-size: 0.75rem;
            color: var(--text-light);
        }

        .nav-links {
            list-style: none;
        }

        .nav-links li {
            margin-bottom: 0.5rem;
        }

        .nav-links a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--text);
            padding: 0.75rem 1rem;
            border-radius: var(--radius);
            transition: all 0.2s ease;
        }

        .nav-links a:hover {
            background-color: var(--secondary);
            color: var(--primary);
        }

        .nav-links a.active {
            background-color: var(--primary);
            color: white;
        }

        .nav-links i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .content-header h2 {
            font-size: 1.5rem;
            color: var(--text);
            font-weight: 600;
        }

        .filter-options {
            display: flex;
            gap: 1rem;
        }

        .filter-options select {
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            border: 1px solid #e5e7eb;
            background-color: var(--white);
            font-size: 0.875rem;
        }

        /* Notification Cards */
        .notifications-container {
            display: grid;
            gap: 1rem;
        }

        .notification-card {
            background-color: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            position: relative;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .notification-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .notification-icon {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            width: 40px;
            height: 40px;
            background-color: var(--secondary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .notification-card.priority-high .notification-icon {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .notification-card.priority-medium .notification-icon {
            background-color: #fef3c7;
            color: #d97706;
        }

        .notification-header {
            margin-bottom: 0.75rem;
        }

        .notification-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.25rem;
            padding-right: 3rem;
        }

        .notification-date {
            font-size: 0.875rem;
            color: var(--text-light);
            display: flex;
            align-items: center;
        }

        .notification-date i {
            margin-right: 0.5rem;
            font-size: 0.75rem;
        }

        .notification-content {
            color: var(--text);
            margin-bottom: 1rem;
        }

        .notification-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }

        .notification-actions button {
            background-color: transparent;
            border: none;
            padding: 0.5rem;
            border-radius: var(--radius);
            cursor: pointer;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            transition: background-color 0.2s ease;
        }

        .notification-actions button:hover {
            background-color: var(--secondary);
        }

        .notification-actions button i {
            margin-right: 0.25rem;
        }

        /* User Menu */
        .user-menu {
            position: relative;
        }

        .user-menu-button {
            display: flex;
            align-items: center;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: var(--radius);
        }

        .user-menu-button:hover {
            background-color: var(--secondary);
        }

        .user-menu-button img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }

        .user-menu-dropdown {
            position: absolute;
            right: 0;
            top: 100%;
            width: 200px;
            background-color: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 0.5rem;
            display: none;
            z-index: 100;
        }

        .user-menu-dropdown.show {
            display: block;
        }

        .user-menu-dropdown a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            text-decoration: none;
            color: var(--text);
            border-radius: var(--radius);
            transition: background-color 0.2s ease;
        }

        .user-menu-dropdown a:hover {
            background-color: var(--secondary);
        }

        .user-menu-dropdown i {
            margin-right: 0.75rem;
            width: 16px;
            text-align: center;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary);
            cursor: pointer;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .page-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                position: static;
            }
        }

        @media (max-width: 768px) {
            .header-container {
                padding: 1rem;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .sidebar {
                display: none;
            }

            .sidebar.show {
                display: block;
            }

            .page-container {
                margin: 1rem auto;
                padding: 0 1rem;
            }

            .notification-icon {
                width: 32px;
                height: 32px;
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <div class="logo-container">
                <img src="logo.png" alt="SPCF Logo">
                <h1>SPCF PORTAL</h1>
            </div>
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="user-menu">
                <button class="user-menu-button" id="userMenuButton">
                    <img src="/api/placeholder/32/32" alt="User Avatar">
                    <span>John Doe</span>
                    <i class="fas fa-chevron-down" style="margin-left: 0.5rem;"></i>
                </button>
                <div class="user-menu-dropdown" id="userMenuDropdown">
                    <a href="student_info.php"><i class="fas fa-user"></i> My Profile</a>
                    <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="page-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="user-avatar">JD</div>
                <div class="user-info">
                    <span class="user-name">John Doe</span>
                    <span class="user-role">Student</span>
                </div>
            </div>
            <ul class="nav-links">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="student_info.php"><i class="fas fa-user-graduate"></i> Student Information</a></li>
                <li><a href="course_registration.php"><i class="fas fa-book"></i> Course Registration</a></li>
                <li><a href="schedule.php"><i class="fas fa-calendar-alt"></i> Class Schedule</a></li>
                <li><a href="grades.php"><i class="fas fa-chart-line"></i> Grades</a></li>
                <li><a href="faculty_management.php"><i class="fas fa-chalkboard-teacher"></i> Faculty</a></li>
                <li><a href="notifications.php" class="active"><i class="fas fa-bell"></i> Notifications</a></li>
                
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-header">
                <h2>Notifications & Announcements</h2>
                <div class="filter-options">
                    <select id="sortNotifications">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="priority">By Priority</option>
                    </select>
                    <select id="filterNotifications">
                        <option value="all">All Notifications</option>
                        <option value="high">High Priority</option>
                        <option value="medium">Medium Priority</option>
                        <option value="low">Low Priority</option>
                    </select>
                </div>
            </div>

            <div class="notifications-container">
                <?php foreach ($notifications as $notification): 
                    $iconClass = '';
                    switch($notification['icon']) {
                        case 'calendar':
                            $iconClass = 'fa-calendar-alt';
                            break;
                        case 'tools':
                            $iconClass = 'fa-tools';
                            break;
                        case 'book':
                            $iconClass = 'fa-book';
                            break;
                        default:
                            $iconClass = 'fa-bell';
                    }
                ?>
                <div class="notification-card priority-<?php echo htmlspecialchars($notification['priority']); ?>">
                    <div class="notification-icon">
                        <i class="fas <?php echo $iconClass; ?>"></i>
                    </div>
                    <div class="notification-header">
                        <h3 class="notification-title"><?php echo htmlspecialchars($notification['title']); ?></h3>
                        <div class="notification-date">
                            <i class="fas fa-clock"></i>
                            <?php echo htmlspecialchars($notification['date']); ?>
                        </div>
                    </div>
                    <div class="notification-content">
                        <?php echo htmlspecialchars($notification['content']); ?>
                    </div>
                    <div class="notification-actions">
                        <button><i class="fas fa-check"></i> Mark as Read</button>
                        <button><i class="fas fa-share"></i> Share</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <script>
        // Toggle user menu dropdown
        const userMenuButton = document.getElementById('userMenuButton');
        const userMenuDropdown = document.getElementById('userMenuDropdown');

        userMenuButton.addEventListener('click', function() {
            userMenuDropdown.classList.toggle('show');
        });

        // Close the dropdown when clicking outside
        window.addEventListener('click', function(event) {
            if (!userMenuButton.contains(event.target) && !userMenuDropdown.contains(event.target)) {
                userMenuDropdown.classList.remove('show');
            }
        });

        // Toggle mobile menu
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const sidebar = document.getElementById('sidebar');

        mobileMenuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });

        // Filter and sort functionality (can be expanded as needed)
        const sortSelect = document.getElementById('sortNotifications');
        const filterSelect = document.getElementById('filterNotifications');

        function applyFiltersAndSort() {
            // This is a placeholder for actual filtering logic
            console.log('Sort by:', sortSelect.value);
            console.log('Filter by:', filterSelect.value);
            
            // Here you would add actual implementation to sort and filter the notifications
            // For a complete solution, you might want to use AJAX to fetch sorted/filtered data
            // or manipulate the DOM to show/hide/reorder notifications
        }

        sortSelect.addEventListener('change', applyFiltersAndSort);
        filterSelect.addEventListener('change', applyFiltersAndSort);
    </script>
</body>
</html>