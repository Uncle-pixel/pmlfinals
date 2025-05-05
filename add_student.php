<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user role from session
$user_role = $_SESSION['user_role'] ?? 'student';

// Check if user has permission to add students
if (!in_array($user_role, ['admin', 'faculty'])) {
    header("Location: dashboard.php");
    exit();
}

// Include database connection
$conn = new mysqli("localhost", "root", "", "spcf_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for form
$success_message = "";
$error_message = "";

// Generate a random student ID
function generateStudentID() {
    $year = date('Y'); // Current year
    $random_number = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT); // Random 4-digit number
    return $year . $random_number;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['student_id']) || empty($_POST['password'])) {
        $error_message = "Please fill all required fields!";
    } else {
        // Handle image upload
        $profile_picture = null;
        if (!empty($_FILES['profile_picture']['name'])) {
            $target_dir = "uploads/"; // Directory to save uploaded images
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true); // Create directory if it doesn't exist
            }
            $target_file = $target_dir . basename($_FILES['profile_picture']['name']);
            $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Validate image file type
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($image_file_type, $allowed_types)) {
                $error_message = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            } elseif ($_FILES['profile_picture']['size'] > 5000000) { // 5MB limit
                $error_message = "The file size must not exceed 5MB.";
            } else {
                // Save the uploaded file
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                    $profile_picture = $target_file;
                } else {
                    $error_message = "Failed to upload the profile picture.";
                }
            }
        }

        if (empty($error_message)) {
            // Prepare and bind SQL statement
            $stmt = $conn->prepare("INSERT INTO students (student_number, name, email, password, program, year_level, date_of_birth, gender, contact_number, address, emergency_contact_name, emergency_contact_number, enrollment_date, class, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $is_active = 1; // Default to active status
            $stmt->bind_param(
                "ssssssssssssssi",
                $_POST['student_id'],
                $_POST['name'],
                $_POST['email'],
                $_POST['password'], // Store the password in plain text
                $_POST['course'],
                $_POST['year_level'],
                $_POST['birthdate'],
                $_POST['gender'],
                $_POST['phone'],
                $_POST['address'],
                $_POST['emergency_contact_name'],
                $_POST['emergency_contact_phone'],
                $_POST['enrollment_date'],
                $_POST['class'], // Add the class field
                $is_active
            );

            // Execute the query
            if ($stmt->execute()) {
                $success_message = "Student added successfully!";
                $_POST = []; // Clear the form data
            } else {
                $error_message = "Failed to add student. Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}

$conn->close();

// Sample programs list for dropdown
$available_programs = [
    'Bachelor of Science in Computer Science',
    'Bachelor of Science in Information Technology',
    'Bachelor of Science in Electrical Engineering',
    'Bachelor of Science in Information Systems',
    'Bachelor of Arts in Communication',
    'Bachelor of Elementary Education',
    'Bachelor of Secondary Education'
];

// Sample options for dropdowns
$year_levels = ["1st Year", "2nd Year", "3rd Year", "4th Year", "5th Year"];
$status_options = ["Active", "Inactive", "Pending"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - SPCF PORTAL</title>
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
        
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            color: var(--light-text);
        }
        
        /* Form styles */
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }
        
        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .card-title {
            font-size: 1.2rem;
            color: var(--primary-color);
            font-weight: 600;
            margin: 0;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.1rem;
            color: var(--dark-text);
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -0.75rem 1rem;
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
        
        .required-label::after {
            content: "*";
            color: var(--danger-color);
            margin-left: 0.25rem;
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
        
        .form-control.is-invalid {
            border-color: var(--danger-color);
        }
        
        .invalid-feedback {
            color: var(--danger-color);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .form-text {
            font-size: 0.875rem;
            color: var(--light-text);
            margin-top: 0.25rem;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            padding-top: 1.5rem;
            margin-top: 1rem;
            border-top: 1px solid var(--border-color);
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-speed);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-icon {
            margin-right: 0.5rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
        }
        
        .btn-success {
            background-color: var(--success-color);
            color: white;
            border: none;
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
        
        .btn-secondary {
            background-color: white;
            color: var (--dark-text);
            border: 1px solid var(--border-color);
        }
        
        .btn-secondary:hover {
            background-color: #f8f9fa;
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
        
        /* Upload image area */
        .upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 4px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 1rem;
            transition: border-color var(--transition-speed);
            cursor: pointer;
        }
        
        .upload-area:hover {
            border-color: var(--primary-color);
        }
        
        .upload-icon {
            font-size: 2.5rem;
            color: var(--light-text);
            margin-bottom: 1rem;
        }
        
        .upload-text {
            font-size: 1rem;
            color: var(--dark-text);
            margin-bottom: 0.5rem;
        }
        
        .upload-subtext {
            font-size: 0.875rem;
            color: var(--light-text);
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
        
        /* Student List Preview (Placeholder) */
        .student-preview {
            margin-top: 2rem;
        }
        
        .student-preview-header {
            font-size: 1.2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .student-list {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            overflow: hidden;
        }
        
        .student-list-header {
            background-color: var(--primary-light);
            padding: 0.75rem 1rem;
            font-weight: 600;
            display: grid;
            grid-template-columns: 50px 2fr 2fr 2fr 1fr 100px;
            gap: 1rem;
            align-items: center;
        }
        
        .student-list-item {
            padding: 0.75rem 1rem;
            display: grid;
            grid-template-columns: 50px 2fr 2fr 2fr 1fr 100px;
            gap: 1rem;
            align-items: center;
            border-top: 1px solid var(--border-color);
        }
        
        .student-list-item:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            background-color: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .student-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .action-btn {
            background: none;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            color: var(--light-text);
            transition: color var(--transition-speed);
        }
        
        .action-btn:hover {
            color: var(--primary-color);
        }
        
        .action-btn.edit-btn:hover {
            color: var(--info-color);
        }
        
        .action-btn.delete-btn:hover {
            color: var(--danger-color);
        }
        
        .student-status {
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-align: center;
        }
        
        .status-active {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .status-inactive {
            background-color: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }
        
        .status-pending {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        
        /* Responsive */
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
                background-color: var (--primary-dark);
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
            
            .form-row {
                margin: 0;
            }
            
            .form-group {
                flex: 1 1 100%;
                padding: 0;
            }
            
            .student-list-header, .student-list-item {
                grid-template-columns: 2fr 2fr 1fr;
            }
            
            .student-list-header > :nth-child(1),
            .student-list-item > :nth-child(1),
            .student-list-header > :nth-child(4),
            .student-list-item > :nth-child(4),
            .student-list-header > :nth-child(5),
            .student-list-item > :nth-child(5) {
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
        <div class="page-header">
            <h1 class="page-title">Add New Student</h1>
            <p class="page-subtitle">Create a new student account in the SPCF Portal system.</p>
        </div>
        
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
        
        <form method="POST" enctype="multipart/form-data">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-user-plus"></i> Student Registration Form</h2>
                </div>
                
                <div class="card-body">
                    <!-- Basic Information Section -->
                    <div class="form-section">
                        <h3 class="section-title">Basic Information</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="profile_picture" class="form-label">Profile Picture</label>
                                <div class="upload-area" id="uploadArea">
                                    <input type="file" id="profile_picture" name="profile_picture" style="display: none;" accept="image/*">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <div class="upload-text">Click to upload profile picture</div>
                                    <div class="upload-subtext">JPG, PNG or GIF up to 5MB</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name" class="form-label required-label">Full Name</label>
                                <input type="text" id="name" name="name" class="form-control" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="student_id" class="form-label required-label">Student ID</label>
                                <input type="text" id="student_id" name="student_id" class="form-control" readonly value="<?php echo generateStudentID(); ?>">
                                <small class="form-text">Format: YYYYXXXX (e.g., 20230001)</small>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email" class="form-label required-label">Email Address</label>
                                <input type="email" id="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                <small class="form-text">Will be used for login and communication</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="password" class="form-label required-label">Initial Password</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                                <small class="form-text">Student will be prompted to change upon first login</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Academic Information Section -->
                    <div class="form-section">
                        <h3 class="section-title">Academic Information</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="course" class="form-label required-label">Program</label>
                                <select id="course" name="course" class="form-control" required>
                                    <option value="">-- Select Program --</option>
                                    <?php foreach ($available_programs as $program): ?>
                                        <option value="<?php echo htmlspecialchars($program); ?>" <?php echo (isset($_POST['course']) && $_POST['course'] === $program) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($program); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="year_level" class="form-label required-label">Year Level</label>
                                <select id="year_level" name="year_level" class="form-control" required>
                                    <option value="">-- Select Year Level --</option>
                                    <?php foreach ($year_levels as $year): ?>
                                        <option value="<?php echo htmlspecialchars($year); ?>" <?php echo (isset($_POST['year_level']) && $_POST['year_level'] === $year) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($year); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="enrollment_date" class="form-label required-label">Enrollment Date</label>
                                <input type="date" id="enrollment_date" name="enrollment_date" class="form-control" required value="<?php echo htmlspecialchars($_POST['enrollment_date'] ?? date('Y-m-d')); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="status" class="form-label required-label">Status</label>
                                <select id="status" name="status" class="form-control" required>
                                    <?php foreach ($status_options as $status): ?>
                                        <option value="<?php echo htmlspecialchars($status); ?>" <?php echo (isset($_POST['status']) && $_POST['status'] === $status) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($status); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Class Assignment Section -->
                    <div class="form-section">
                        <h3 class="section-title">Class Assignment</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="class" class="form-label required-label">Class</label>
                                <select id="class" name="class" class="form-control" required>
                                    <option value="">-- Select Class --</option>
                                    <option value="Class A" <?php echo (isset($_POST['class']) && $_POST['class'] === 'Class A') ? 'selected' : ''; ?>>Class A</option>
                                    <option value="Class B" <?php echo (isset($_POST['class']) && $_POST['class'] === 'Class B') ? 'selected' : ''; ?>>Class B</option>
                                    <option value="Class C" <?php echo (isset($_POST['class']) && $_POST['class'] === 'Class C') ? 'selected' : ''; ?>>Class C</option>
                                    <option value="Class D" <?php echo (isset($_POST['class']) && $_POST['class'] === 'Class D') ? 'selected' : ''; ?>>Class D</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Personal Information Section -->
                    <div class="form-section">
                        <h3 class="section-title">Personal Information</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="birthdate" class="form-label">Date of Birth</label>
                                <input type="date" id="birthdate" name="birthdate" class="form-control" value="<?php echo htmlspecialchars($_POST['birthdate'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="gender" class="form-label">Gender</label>
                                <select id="gender" name="gender" class="form-control">
                                    <option value="">-- Select Gender --</option>
                                    <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" id="address" name="address" class="form-control" value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Emergency Contact Section -->
                    <div class="form-section">
                        <h3 class="section-title">Emergency Contact</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="emergency_contact_name" class="form-label">Name</label>
                                <input type="text" id="emergency_contact_name" name="emergency_contact_name" class="form-control" value="<?php echo htmlspecialchars($_POST['emergency_contact_name'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="emergency_contact_relationship" class="form-label">Relationship</label>
                                <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship" class="form-control" value="<?php echo htmlspecialchars($_POST['emergency_contact_relationship'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="emergency_contact_phone" class="form-label">Phone Number</label>
                                <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" class="form-control" value="<?php echo htmlspecialchars($_POST['emergency_contact_phone'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Add Student</button>
                        <a href="student_info.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> SPCF Portal. All rights reserved.</p>
        </div>
    </footer>

    <!-- JS for Mobile Menu Toggle -->
    <script>
        document.getElementById("mobileMenuToggle").addEventListener("click", function() {
            document.getElementById("navLinks").classList.toggle("active");
        });
    </script>
</body>
</html>
