<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
$conn = new mysqli("localhost", "root", "", "spcf_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user role and ID from session
$user_role = $_SESSION['user_role'] ?? 'student';
$user_id = $_SESSION['user_id'];

// Initialize variables
$student_info = [
    'name' => '',
    'student_number' => '',
    'student_id' => '',
    'email' => '',
    'program' => '',
    'year_level' => '',
    'status' => '',
    'gpa' => '',
    'enrollment_date' => '',
    'contact_number' => '',
    'address' => '',
    'emergency_contact_name' => '',
    'emergency_contact_relationship' => '',
    'emergency_contact_number' => '',
    'date_of_birth' => '',
    'gender' => '',
    'family_members' => '',
    'work_experience' => '',
    'achievements' => '',
    'skills' => '',
    'high_school' => '',
    'college' => '',
    'profile_picture' => 'default_profile.jpg',
    'programs' => ['BSIT', 'BSCS', 'BSCE', 'BSBA', 'BSA', 'BEED', 'BSED']
];

$students = [];
$success_message = "";

// Set is_admin_or_faculty variable
$is_admin_or_faculty = in_array($user_role, ['admin', 'faculty']);

// Fetch student details for students
if ($user_role === 'student') {
    $student_number = $_SESSION['student_number'] ?? null;

    // Use student_number if available, otherwise fallback to student_id
    if ($student_number) {
        $stmt = $conn->prepare("SELECT * FROM students WHERE student_number = ?");
        $stmt->bind_param("s", $student_number);
    } else {
        $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
        $stmt->bind_param("i", $user_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $student_info = $result->fetch_assoc();
    } else {
        $error_message = "Unable to find student information. Please contact the administrator.";
    }
    $stmt->close();
} elseif ($is_admin_or_faculty) {
    // Fetch all students for admin/faculty
    $class_filter = $_GET['class'] ?? null;
    if ($class_filter) {
        $stmt = $conn->prepare("SELECT * FROM students WHERE class = ?");
        $stmt->bind_param("s", $class_filter);
    } else {
        $stmt = $conn->prepare("SELECT * FROM students");
    }
    $stmt->execute();
    $result = $stmt->get_result();
    if($result && $result->num_rows > 0) {
        $students = $result->fetch_all(MYSQLI_ASSOC);
    }
    $stmt->close();
}

// Handle student selection for admin/faculty
if ($is_admin_or_faculty && isset($_GET['student_id'])) {
    $selected_student_id = $_GET['student_id'];

    // Determine if this is a student_number or student_id
    if (is_numeric($selected_student_id) && strlen($selected_student_id) <= 11) {
        $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
        $stmt->bind_param("i", $selected_student_id);
    } else {
        $stmt = $conn->prepare("SELECT * FROM students WHERE student_number = ?");
        $stmt->bind_param("s", $selected_student_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    if($result && $result->num_rows > 0) {
        $student_info = array_merge($student_info, $result->fetch_assoc());
    } else {
        die("No student found with the given ID.");
    }
    $stmt->close();
}

// Handle form submission for editing student details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_admin_or_faculty) {
    $student_id = $_POST['student_id'] ?? null;
    $course = $_POST['course'] ?? null;
    $year_level = $_POST['year_level'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($student_id && $course && $year_level && $status) {
        $stmt = $conn->prepare("UPDATE students SET program = ?, year_level = ?, status = ? WHERE student_number = ?");
        $stmt->bind_param("ssss", $course, $year_level, $status, $student_id);

        if ($stmt->execute()) {
            $success_message = "Student information updated successfully!";
        } else {
            $success_message = "Failed to update student information. Error: " . $stmt->error;
        }
        $stmt->close();

        // Fetch updated data
        $stmt = $conn->prepare("SELECT * FROM students WHERE student_number = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $student_info = array_merge($student_info, $result->fetch_assoc());
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - SPCF PORTAL</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS remains the same as in your original code */
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
        
        /* Profile header */
        .profile-header {
            background-color: white;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .profile-picture {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary-light);
            margin-right: 2rem;
        }
        
        .profile-info {
            flex: 1;
        }
        
        .profile-name {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .profile-subtitle {
            color: var(--light-text);
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }
        
        .profile-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-top: 1rem;
        }
        
        .stat-item {
            background-color: var(--primary-light);
            padding: 0.75rem 1.25rem;
            border-radius: 6px;
            display: flex;
            align-items: center;
        }
        
        .stat-icon {
            margin-right: 0.75rem;
            font-size: 1.2rem;
            color: var(--primary-color);
        }
        
        .stat-info h4 {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--light-text);
            margin: 0;
        }
        
        .stat-info p {
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
        }
        
        .profile-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .action-button {
            padding: 0.75rem 1.25rem;
            border-radius: 4px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition-speed);
        }
        
        .primary-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }
        
        .primary-button:hover {
            background-color: var(--primary-dark);
        }
        
        .secondary-button {
            background-color: white;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }
        
        .secondary-button:hover {
            background-color: var(--primary-light);
        }
        
        .button-icon {
            margin-right: 0.5rem;
        }
        
        /* Tabs */
        .profile-tabs {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 2rem;
        }
        
        .tab {
            padding: 1rem 1.5rem;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            color: var(--light-text);
            transition: all var(--transition-speed);
        }
        
        .tab.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }
        
        .tab:hover:not(.active) {
            color: var(--dark-text);
            border-bottom-color: var(--border-color);
        }
        
        /* Tab content */
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Info sections */
        .info-section {
            background-color: white;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
        }
        
        .section-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 0.75rem;
        }
        
        .section-edit {
            color: var(--light-text);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            transition: color var(--transition-speed);
        }
        
        .section-edit:hover {
            color: var(--primary-color);
        }
        
        .section-content {
            padding: 1.5rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .info-item {
            margin-bottom: 1rem;
        }
        
        .info-item:last-child {
            margin-bottom: 0;
        }
        
        .info-label {
            font-size: 0.85rem;
            color: var(--light-text);
            margin-bottom: 0.25rem;
            font-weight: 600;
        }
        
        .info-value {
            font-size: 1rem;
            word-break: break-word;
        }
        
        .full-width {
            grid-column: 1 / -1;
        }
        
        /* Form elements */
        .edit-form {
            padding: 1.5rem;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -0.75rem 1.5rem;
        }
        
        .form-group {
            flex: 1 1 300px;
            padding: 0 0.75rem;
            margin-bottom: 1rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark-text);
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color var(--transition-speed), box-shadow var(--transition-speed);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 82, 204, 0.25);
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            border-top: 1px solid var(--border-color);
            padding-top: 1.5rem;
            margin-top: 1rem;
        }
        
        .form-button {
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-speed);
        }
        
        .form-submit {
            background-color: var(--success-color);
            color: white;
            border: none;
        }
        
        .form-submit:hover {
            background-color: #218838;
        }
        
        .form-cancel {
            background-color: white;
            color: var(--light-text);
            border: 1px solid var(--border-color);
        }
        
        .form-cancel:hover {
            background-color: #f8f9fa;
            color: var(--dark-text);
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
        
        .alert-icon {
            margin-right: 0.5rem;
        }
        
        /* Additional components */
        .badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .badge-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .badge-primary {
            background-color: rgba(0, 82, 204, 0.1);
            color: var(--primary-color);
        }
        
        .badge-info {
            background-color: rgba(23, 162, 184, 0.1);
            color: #17a2b8;
        }
        
        .progress-bar-container {
            width: 100%;
            background-color: #e9ecef;
            border-radius: 4px;
            height: 0.5rem;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            background-color: var(--primary-color);
            border-radius: 4px;
        }

        /* Student List Table */
        .student-table {
            width: 100%;
            border-collapse: collapse;
        }

        .student-table th,
        .student-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .student-table th {
            background-color: var(--primary-light);
            color: var(--primary-color);
            font-weight: 600;
        }

        .student-table tr:nth-child(even) {
            background-color: #f9f9fa;
        }

        .student-table tr:hover {
            background-color: #f1f4f9;
        }

        .view-button {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            border-radius: 4px;
            background-color: var(--primary-light);
            color: var(--primary-color);
            text-decoration: none;
            border: 1px solid var(--primary-color);
            display: inline-flex;
            align-items: center;
            transition: all var(--transition-speed);
        }

        .view-button:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .view-button i {
            margin-right: 0.25rem;
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
        
        /* Responsive design */
        @media (max-width: 992px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-picture {
                margin-right: 0;
                margin-bottom: 1.5rem;
            }
            
            .profile-stats {
                justify-content: center;
            }
            
            .profile-actions {
                justify-content: center;
            }
        }
        
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
            }
            
            .logo h1 {
                font-size: 1.2rem;
            }
            
            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background-color: var(--primary-dark);
                flex-direction: column;
                padding: 1rem;
            }
            
            .nav-links.active {
                display: block;
            }
            
            .nav-links ul {
                flex-direction: column;
            }
            
            .nav-links li {
                margin: 0.5rem 0;
            }
            
            .mobile-menu-toggle {
                display: block;
            }
            
            .user-menu {
                display: none;
            }
            
            .profile-tabs {
                overflow-x: auto;
                white-space: nowrap;
                padding-bottom: 0.5rem;
            }
            
            .tab {
                padding: 1rem;
            }
            
            .form-row {
                margin: 0 0 1rem;
            }
            
            .form-group {
                flex: 1 1 100%;
                padding: 0;
            }

            .student-table th:nth-child(3),
            .student-table td:nth-child(3),
            .student-table th:nth-child(4),
            .student-table td:nth-child(4) {
                display: none;
            }
        }
        
        @media (max-width: 576px) {
            .container {
                padding: 0 1rem;
            }
            
            .profile-header {
                padding: 1.5rem;
            }
            
            .profile-picture {
                width: 100px;
                height: 100px;
            }
            
            .profile-name {
                font-size: 1.5rem;
            }
            
            .stat-item {
                width: 100%;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }

            .student-table th:nth-child(5),
            .student-table td:nth-child(5) {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="navbar">
            <div class="logo">
                <img src="logo.png" alt="SPCF Logo">
                <h1>SPCF PORTAL</h1>
            </div>
            
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="user-menu">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo substr($_SESSION['user_name'] ?? 'U', 0, 1); ?>
                    </div>
                    <span><?php echo $_SESSION['user_name'] ?? 'User'; ?></span>
                </div>
                <a href="logout.php" class="button">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        
        <nav class="nav-links" id="navLinks">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="student_info.php" class="active"><i class="fas fa-user-graduate"></i> Student Information</a></li>
                <li><a href="course_registration.php"><i class="fas fa-book"></i> Course Registration</a></li>
                <li><a href="faculty_management.php"><i class="fas fa-chalkboard-teacher"></i> Faculty Management</a></li>
                <li><a href="grading_system.php"><i class="fas fa-chart-line"></i> Grading System</a></li>
                <li><a href="class_scheduling.php"><i class="fas fa-calendar-alt"></i> Class Scheduling</a></li>
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

        <!-- Student List for Admin/Faculty -->
        <?php if ($is_admin_or_faculty && empty($_GET['student_id'])): ?>
            <!-- Class Filter -->
            <div class="info-section" style="margin-bottom: 1.5rem;">
                <div class="section-header">
                    <h3 class="section-title"><i class="fas fa-filter"></i> Filter Students</h3>
                </div>
                <div class="section-content">
                    <form method="GET" style="display: flex; gap: 1rem; align-items: center;">
                        <div style="flex: 1;">
                            <label for="class_filter" class="form-label">Class</label>
                            <select id="class_filter" name="class" class="form-control">
                                <option value="">All Classes</option>
                                <option value="Class A" <?php echo isset($_GET['class']) && $_GET['class'] === 'Class A' ? 'selected' : ''; ?>>Class A</option>
                                <option value="Class B" <?php echo isset($_GET['class']) && $_GET['class'] === 'Class B' ? 'selected' : ''; ?>>Class B</option>
                                <option value="Class C" <?php echo isset($_GET['class']) && $_GET['class'] === 'Class C' ? 'selected' : ''; ?>>Class C</option>
                                <option value="Class D" <?php echo isset($_GET['class']) && $_GET['class'] === 'Class D' ? 'selected' : ''; ?>>Class D</option>
                            </select>
                        </div>
                        <div style="align-self: flex-end;">
                            <button type="submit" class="action-button primary-button">
                                <i class="fas fa-filter button-icon"></i> Apply Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Student List -->
            <div class="info-section">
                <div class="section-header">
                    <h3 class="section-title"><i class="fas fa-users"></i> Student List</h3>
                    <a href="add_student.php" class="action-button primary-button" style="font-size: 0.85rem; padding: 0.5rem 0.75rem;">
                        <i class="fas fa-user-plus button-icon"></i> Add Student
                    </a>
                </div>
                <div class="section-content">
                    <?php if (empty($students)): ?>
                        <div style="text-align: center; padding: 2rem 0;">
                            <i class="fas fa-user-graduate" style="font-size: 3rem; color: var(--light-text); margin-bottom: 1rem;"></i>
                            <p>No students found. <?php echo isset($_GET['class']) ? 'Try a different class filter or add a new student.' : 'Add a new student to get started.'; ?></p>
                        </div>
                    <?php else: ?>
                        <table class="student-table">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Program</th>
                                    <th>Year Level</th>
                                    <th>Class</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['student_number']); ?></td>
                                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['program']); ?></td>
                                    <td><?php echo htmlspecialchars($student['year_level']); ?></td>
                                    <td><?php echo htmlspecialchars($student['class'] ?? 'N/A'); ?></td>
                                    <td>
                                        <a href="student_info.php?student_id=<?php echo htmlspecialchars($student['student_number']); ?>" class="view-button">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif ($is_admin_or_faculty && isset($_GET['student_id']) || $user_role === 'student'): ?>
            <!-- Profile Header -->
            <div class="profile-header">
                <img src="<?php echo htmlspecialchars($student_info['profile_picture'] ?? 'default-profile.jpg'); ?>" alt="Profile Picture" class="profile-picture">
                
                <div class="profile-info">
                    <h2 class="profile-name"><?php echo htmlspecialchars($student_info['name'] ?? 'Student Name'); ?></h2>
                    <p class="profile-subtitle">Student ID: <?php echo htmlspecialchars($student_info['student_number'] ?? 'N/A'); ?></p>
                    
                    <div class="profile-stats">
                        <div class="stat-item">
                            <div class="stat-icon"><i class="fas fa-graduation-cap"></i></div>
                            <div class="stat-info">
                                <h4>Program</h4>
                                <p><?php echo htmlspecialchars($student_info['program'] ?? 'N/A'); ?></p>
                            </div>
                        </div>
                        
                        <div class="stat-item">
                            <div class="stat-icon"><i class="fas fa-user-clock"></i></div>
                            <div class="stat-info">
                                <h4>Status</h4>
                                <p><?php echo htmlspecialchars($student_info['status'] ?? 'N/A'); ?></p>
                            </div>
                        </div>
                        
                        <div class="stat-item">
                            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                            <div class="stat-info">
                                <h4>GPA</h4>
                                <p><?php echo htmlspecialchars($student_info['gpa'] ?? 'N/A'); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="profile-actions">
                        <a href="#" class="action-button primary-button">
                            <i class="fas fa-download button-icon"></i> Download Records
                        </a>
                        <?php if ($is_admin_or_faculty): ?>
                            <a href="#edit-profile" class="action-button secondary-button">
                                <i class="fas fa-edit button-icon"></i> Edit Profile
                            </a>
                            <a href="student_info.php" class="action-button secondary-button">
                                <i class="fas fa-arrow-left button-icon"></i> Back to List
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Profile Tabs -->
            <div class="profile-tabs">
                <div class="tab active" data-tab="basic-info">Basic Information</div>
                <div class="tab" data-tab="academic">Academic Information</div>
                <div class="tab" data-tab="personal">Personal Information</div>
                <div class="tab" data-tab="achievements">Achievements & Skills</div>
            </div>
            
            <!-- Tab Content: Basic Information -->
            <div id="basic-info" class="tab-content active">
                <div class="info-section">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-info-circle"></i> Basic Information</h3>
                        <?php if ($is_admin_or_faculty): ?>
                            <button class="section-edit" id="edit-basic-info">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        <?php endif; ?>
                    </div>
                    
                    <div class="section-content" id="basic-info-content">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Full Name</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['name'] ?? 'N/A'); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Student ID</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['student_number'] ?? 'N/A'); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Email Address</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['email'] ?? 'N/A'); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Program</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['program'] ?? 'N/A'); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Year Level</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['year_level'] ?? 'N/A'); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Status</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['status'] ?? 'N/A'); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Enrollment Date</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['enrollment_date'] ?? 'N/A'); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($is_admin_or_faculty): ?>
                        <form class="edit-form" id="basic-info-form" style="display: none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="year_level" class="form-label">Year Level</label>
                                    <input type="text" id="year_level" name="year_level" class="form-control" value="<?php echo htmlspecialchars($student_info['year_level'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="status" class="form-label">Status</label>
                                    <select id="status" name="status" class="form-control">
                                        <option value="Active" <?php echo ($student_info['status'] === 'Active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="Inactive" <?php echo ($student_info['status'] === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                        <option value="On Leave" <?php echo ($student_info['status'] === 'On Leave') ? 'selected' : ''; ?>>On Leave</option>
                                        <option value="Graduated" <?php echo ($student_info['status'] === 'Graduated') ? 'selected' : ''; ?>>Graduated</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="button" class="form-button form-cancel" id="cancel-basic-info">Cancel</button>
                                <button type="submit" class="form-button form-submit">Save Changes</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
                
                <div class="info-section">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-phone-alt"></i> Contact Information</h3>
                    </div>
                    
                    <div class="section-content">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Phone Number</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['contact_number'] ?? 'N/A'); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Email Address</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['email'] ?? 'N/A'); ?></div>
                            </div>
                            
                            <div class="info-item full-width">
                                <div class="info-label">Address</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['address'] ?? 'N/A'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="info-section">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-ambulance"></i> Emergency Contact</h3>
                    </div>
                    
                    <div class="section-content">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Name</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['emergency_contact_name'] ?? 'N/A'); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Relationship</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['emergency_contact_relationship'] ?? 'N/A'); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Phone Number</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['emergency_contact_number'] ?? 'N/A'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab Content: Academic Information -->
            <div id="academic" class="tab-content">
                <div class="info-section">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-graduation-cap"></i> Academic Information</h3>
                    </div>
                    
                    <div class="section-content">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Current Program</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['program'] ?? 'N/A'); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Year Level</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['year_level'] ?? 'N/A'); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Enrollment Date</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['enrollment_date'] ?? 'N/A'); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">GPA</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['gpa'] ?? 'N/A'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="info-section">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-history"></i> Education History</h3>
                    </div>
                    
                    <div class="section-content">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">High School</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['high_school'] ?? 'N/A'); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">College</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['college'] ?? 'N/A'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="info-section">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-book"></i> Current Courses</h3>
                    </div>
                    
                    <div class="section-content">
                        <!-- Example table for current courses -->
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                                    <th style="padding: 0.75rem; text-align: left;">Course Code</th>
                                    <th style="padding: 0.75rem; text-align: left;">Course Name</th>
                                    <th style="padding: 0.75rem; text-align: left;">Schedule</th>
                                    <th style="padding: 0.75rem; text-align: left;">Instructor</th>
                                    <th style="padding: 0.75rem; text-align: left;">Units</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 0.75rem;">CS101</td>
                                    <td style="padding: 0.75rem;">Introduction to Programming</td>
                                    <td style="padding: 0.75rem;">MWF 9:00 AM - 10:30 AM</td>
                                    <td style="padding: 0.75rem;">Prof. Smith</td>
                                    <td style="padding: 0.75rem;">3</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 0.75rem;">MATH201</td>
                                    <td style="padding: 0.75rem;">Calculus II</td>
                                    <td style="padding: 0.75rem;">TTh 1:00 PM - 2:30 PM</td>
                                    <td style="padding: 0.75rem;">Prof. Johnson</td>
                                    <td style="padding: 0.75rem;">4</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 0.75rem;">ENG105</td>
                                    <td style="padding: 0.75rem;">Technical Writing</td>
                                    <td style="padding: 0.75rem;">MWF 2:00 PM - 3:00 PM</td>
                                    <td style="padding: 0.75rem;">Prof. Williams</td>
                                    <td style="padding: 0.75rem;">3</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="info-section">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-chart-line"></i> Academic Progress</h3>
                    </div>
                    
                    <div class="section-content">
                        <div style="margin-bottom: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span style="font-weight: 600;">Overall Progress</span>
                                <span>75%</span>
                            </div>
                            <div class="progress-bar-container">
                                <div class="progress-bar" style="width: 75%;"></div>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span style="font-weight: 600;">Core Courses</span>
                                <span>80%</span>
                            </div>
                            <div class="progress-bar-container">
                                <div class="progress-bar" style="width: 80%;"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span style="font-weight: 600;">Elective Courses</span>
                                <span>60%</span>
                            </div>
                            <div class="progress-bar-container">
                                <div class="progress-bar" style="width: 60%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab Content: Personal Information -->
            <div id="personal" class="tab-content">
                <div class="info-section">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-user"></i> Personal Information</h3>
                    </div>
                    
                    <div class="section-content">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Full Name</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['name'] ?? 'N/A'); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Date of Birth</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['date_of_birth'] ?? 'N/A'); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Gender</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['gender'] ?? 'N/A'); ?></div>
                            </div>
                            
                            <div class="info-item full-width">
                                <div class="info-label">Address</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['address'] ?? 'N/A'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="info-section">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-users"></i> Family Background</h3>
                    </div>
                    
                    <div class="section-content">
                        <div class="info-grid">
                            <div class="info-item full-width">
                                <div class="info-label">Family Members</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['family_members'] ?? 'N/A'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="info-section">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-briefcase"></i> Work Experience</h3>
                    </div>
                    
                    <div class="section-content">
                        <div class="info-grid">
                            <div class="info-item full-width">
                                <div class="info-label">Experience</div>
                                <div class="info-value"><?php echo htmlspecialchars($student_info['work_experience'] ?? 'N/A'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab Content: Achievements & Skills -->
            <div id="achievements" class="tab-content">
                <div class="info-section">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-trophy"></i> Achievements</h3>
                    </div>
                    
                    <div class="section-content">
                        <div class="info-grid">
                            <div class="info-item full-width">
                                <div class="info-value">
                                    <?php 
                                    $achievements = explode(', ', $student_info['achievements'] ?? '');
                                    if (!empty($student_info['achievements'])) {
                                        foreach ($achievements as $achievement) {
                                            echo '<span class="badge badge-success">' . htmlspecialchars($achievement) . '</span> ';
                                        }
                                    } else {
                                        echo 'No achievements added yet.';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="info-section">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-code"></i> Skills</h3>
                    </div>
                    
                    <div class="section-content">
                        <div class="info-grid">
                            <div class="info-item full-width">
                                <div class="info-value">
                                    <?php 
                                    $skills = explode(', ', $student_info['skills'] ?? '');
                                    if (!empty($student_info['skills'])) {
                                        foreach ($skills as $skill) {
                                            echo '<span class="badge badge-primary">' . htmlspecialchars($skill) . '</span> ';
                                        }
                                    } else {
                                        echo 'No skills added yet.';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="info-section">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-certificate"></i> Certifications</h3>
                    </div>
                    
                    <div class="section-content">
                        <div style="text-align: center; padding: 2rem 0; color: var(--light-text);">
                            <i class="fas fa-file-alt" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                            <p>No certifications added yet.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Edit Profile Form (for admin/faculty) -->
            <?php if ($is_admin_or_faculty): ?>
                <div id="edit-profile" class="info-section" style="margin-top: 2rem;">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-user-edit"></i> Edit Student Profile</h3>
                    </div>
                    
                    <form method="POST" class="edit-form">
                        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_info['student_number'] ?? ''); ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="edit_course" class="form-label">Program</label>
                                <select id="edit_course" name="course" class="form-control">
                                    <?php foreach ($student_info['programs'] as $program): ?>
                                        <option value="<?php echo htmlspecialchars($program); ?>" <?php echo ($student_info['program'] === $program) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($program); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_year_level" class="form-label">Year Level</label>
                                <input type="text" id="edit_year_level" name="year_level" class="form-control" value="<?php echo htmlspecialchars($student_info['year_level'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="edit_status" class="form-label">Status</label>
                                <select id="edit_status" name="status" class="form-control">
                                    <option value="Active" <?php echo ($student_info['status'] === 'Active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="Inactive" <?php echo ($student_info['status'] === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="On Leave" <?php echo ($student_info['status'] === 'On Leave') ? 'selected' : ''; ?>>On Leave</option>
                                    <option value="Graduated" <?php echo ($student_info['status'] === 'Graduated') ? 'selected' : ''; ?>>Graduated</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_gpa" class="form-label">GPA</label>
                                <input type="text" id="edit_gpa" name="gpa" class="form-control" value="<?php echo htmlspecialchars($student_info['gpa'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="achievements" class="form-label">Achievements</label>
                                <input type="text" id="achievements" name="achievements" class="form-control" value="<?php echo htmlspecialchars($student_info['achievements'] ?? ''); ?>">
                                <small class="text-muted">Separate with commas</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="skills" class="form-label">Skills</label>
                                <input type="text" id="skills" name="skills" class="form-control" value="<?php echo htmlspecialchars($student_info['skills'] ?? ''); ?>">
                                <small class="text-muted">Separate with commas</small>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <a href="dashboard.php" class="form-button form-cancel">Cancel</a>
                            <button type="submit" class="form-button form-submit">Save Changes</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <!-- Footer -->
    <footer class="footer" style="background-color: #0052cc; color: white; text-align: center; padding: 1.5rem 0; margin-top: 3rem; width: 100%; bottom: 0;">
        <div class="footer-content">
            <div class="footer-links" style="display: flex; justify-content: center; flex-wrap: wrap; margin-bottom: 1rem;">
                <a href="dashboard.php" style="color: white; text-decoration: none; margin: 0 15px; opacity: 0.8; transition: opacity 0.3s;">Dashboard</a>
                <a href="privacy-policy.php" style="color: white; text-decoration: none; margin: 0 15px; opacity: 0.8; transition: opacity 0.3s;">Privacy Policy</a>
                <a href="terms-of-service.php" style="color: white; text-decoration: none; margin: 0 15px; opacity: 0.8; transition: opacity 0.3s;">Terms of Service</a>
                <a href="contact-us.php" style="color: white; text-decoration: none; margin: 0 15px; opacity: 0.8; transition: opacity 0.3s;">Contact Us</a>
            </div>
            <div class="copyright" style="font-size: 0.9rem;">
                &copy; <?php echo date('Y'); ?> SPCF PORTAL. All Rights Reserved.
            </div>
        </div>
    </footer>
    
    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuToggle').addEventListener('click', function() {
            document.getElementById('navLinks').classList.toggle('active');
        });
        
        // Tab functionality
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs and content
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked tab and corresponding content
                // Tab functionality (continuation)
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
        // Remove active class from all tabs and content
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        // Add active class to the clicked tab and corresponding content
        tab.classList.add('active');
        const targetContent = document.getElementById(tab.getAttribute('data-tab'));
        targetContent.classList.add('active');
    });
});

// Set session variables for student
<?php
$_SESSION['user_id'] = $student['student_number']; // Use student_number as the unique identifier
$_SESSION['user_role'] = 'student'; // Set the role to 'student'
?>
</script>
</body>
</html>
