<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Process course registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    $course_code = $_POST['course_code'];
    // In a real application, you would:
    // 1. Check if the student is already registered for this course
    // 2. Check if there are available slots
    // 3. Insert the registration into the database
    
    // Simulating successful registration
    $_SESSION['success_message'] = "Successfully registered for " . $course_code;
    header("Location: course_registration.php");
    exit();
}

// Example: Fetch dynamic data for courses (replace with DB query later)
$courses = [
    [
        'course_code' => 'CS101',
        'course_name' => 'Introduction to Programming',
        'description' => 'Learn the basics of programming using Python.',
        'instructor' => 'Dr. Smith',
        'schedule' => 'MWF 10:00 AM - 11:30 AM',
        'units' => 3,
        'status' => 'Available',
        'slots' => '25/30'
    ],
    [
        'course_code' => 'MATH201',
        'course_name' => 'Calculus II',
        'description' => 'Advanced calculus topics for engineering students.',
        'instructor' => 'Dr. Johnson',
        'schedule' => 'TTh 1:00 PM - 2:30 PM',
        'units' => 4,
        'status' => 'Full',
        'slots' => '40/40'
    ],
    [
        'course_code' => 'ENG105',
        'course_name' => 'Technical Writing',
        'description' => 'Develop professional writing skills for technical documentation.',
        'instructor' => 'Prof. Williams',
        'schedule' => 'MWF 2:00 PM - 3:00 PM',
        'units' => 3,
        'status' => 'Available',
        'slots' => '18/30'
    ],
    [
        'course_code' => 'PHYS202',
        'course_name' => 'Physics for Engineers',
        'description' => 'Applied physics concepts for engineering applications.',
        'instructor' => 'Dr. Garcia',
        'schedule' => 'TTh 9:00 AM - 11:00 AM',
        'units' => 4,
        'status' => 'Available',
        'slots' => '22/35'
    ]
];

// Get registered courses (in a real app, this would come from the database)
$registered_courses = [
    [
        'course_code' => 'BIO101',
        'course_name' => 'Introduction to Biology',
        'schedule' => 'MWF 8:00 AM - 9:30 AM',
        'instructor' => 'Dr. Miller',
        'units' => 3
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Registration - SPCF Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #0044cc;
            --primary-dark: #003399;
            --secondary-color: #005bb5;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-gray: #f4f4f4;
            --white: #ffffff;
            --shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: var(--light-gray);
            line-height: 1.6;
            color: #333;
        }
        
        header {
            background-color: var(--primary-color);
            color: var(--white);
            padding: 1rem 0;
            box-shadow: var(--shadow);
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo img {
            height: 50px;
            margin-right: 10px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-info span {
            margin-right: 15px;
        }
        
        nav {
            background-color: var(--secondary-color);
            padding: 0.8rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
        }
        
        nav a {
            color: var(--white);
            margin: 0 15px;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        nav a:hover {
            color: #ffd700;
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .card {
            background: var(--white);
            border-radius: 8px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .card-header {
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .tabs {
            display: flex;
            border-bottom: 1px solid #eee;
        }
        
        .tab {
            padding: 12px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
        }
        
        .tab.active {
            border-bottom: 3px solid var(--primary-color);
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .tab-content {
            display: none;
            padding: 20px;
        }
        
        .tab-content.active {
            display: block;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .course-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .course-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }
        
        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        
        .course-title h3 {
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .course-code {
            font-size: 0.9rem;
            color: #666;
        }
        
        .course-details {
            margin: 15px 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
        }
        
        .detail-item i {
            margin-right: 8px;
            color: var(--secondary-color);
            width: 16px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn-success {
            background-color: var(--success-color);
            color: white;
        }
        
        .btn-success:hover {
            background-color: #219653;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .summary-card {
            display: flex;
            justify-content: space-between;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .summary-label {
            font-size: 0.9rem;
            color: #666;
        }
        
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 50px;
        }
        
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                align-items: center;
            }
            
            nav a {
                margin: 5px 10px;
            }
            
            .course-details {
                grid-template-columns: 1fr;
            }
            
            .summary-card {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <img src="/api/placeholder/50/50" alt="SPCF Logo">
                <h1>SPCF Student Portal</h1>
            </div>
            <div class="user-info">
                <span><i class="fas fa-user"></i> <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Student'; ?></span>
                <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </header>
    
    <nav>
        <div class="nav-container">
            <div>
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="student_info.php"><i class="fas fa-user-graduate"></i> Student Info</a>
                <a href="course_registration.php" class="active"><i class="fas fa-book"></i> Course Registration</a>
                <a href="class_schedule.php"><i class="fas fa-calendar-alt"></i> Class Schedule</a>
            </div>
            <div>
                <a href="grades.php"><i class="fas fa-chart-line"></i> Grades</a>
                <a href="payments.php"><i class="fas fa-credit-card"></i> Payments</a>
                <a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_message']; ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-graduation-cap"></i> Course Registration</h2>
                <span>Academic Year 2024-2025, 2nd Semester</span>
            </div>
            
            <div class="tabs">
                <div class="tab active" data-tab="available">Available Courses</div>
                <div class="tab" data-tab="registered">Registered Courses</div>
                <div class="tab" data-tab="summary">Registration Summary</div>
            </div>
            
            <div id="available" class="tab-content active">
                <div class="card-body">
                    <h3>Available Courses for Registration</h3>
                    <p>Below are the courses available for the current semester. Click "Register" to add a course to your schedule.</p>
                    
                    <?php foreach ($courses as $course): ?>
                        <div class="course-card">
                            <div class="course-header">
                                <div class="course-title">
                                    <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
                                    <div class="course-code"><?php echo htmlspecialchars($course['course_code']); ?></div>
                                </div>
                                <div>
                                    <?php if ($course['status'] === 'Available'): ?>
                                        <span class="badge badge-success">Available</span>
                                    <?php elseif ($course['status'] === 'Full'): ?>
                                        <span class="badge badge-danger">Full</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning"><?php echo htmlspecialchars($course['status']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <p><?php echo htmlspecialchars($course['description']); ?></p>
                            
                            <div class="course-details">
                                <div class="detail-item">
                                    <i class="fas fa-user-tie"></i>
                                    <span><?php echo htmlspecialchars($course['instructor']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?php echo htmlspecialchars($course['schedule']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-book"></i>
                                    <span><?php echo htmlspecialchars($course['units']); ?> Units</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-users"></i>
                                    <span>Slots: <?php echo htmlspecialchars($course['slots']); ?></span>
                                </div>
                            </div>
                            
                            <form method="post" action="">
                                <input type="hidden" name="course_code" value="<?php echo htmlspecialchars($course['course_code']); ?>">
                                <button type="submit" name="add_course" class="btn btn-success" <?php echo $course['status'] === 'Full' ? 'disabled' : ''; ?>>
                                    <i class="fas fa-plus-circle"></i> <?php echo $course['status'] === 'Full' ? 'Course Full' : 'Register Course'; ?>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div id="registered" class="tab-content">
                <div class="card-body">
                    <h3>Your Registered Courses</h3>
                    <p>These are the courses you have registered for the current semester.</p>
                    
                    <?php if (empty($registered_courses)): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> You haven't registered for any courses yet.
                        </div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Schedule</th>
                                    <th>Instructor</th>
                                    <th>Units</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registered_courses as $course): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                                        <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                        <td><?php echo htmlspecialchars($course['schedule']); ?></td>
                                        <td><?php echo htmlspecialchars($course['instructor']); ?></td>
                                        <td><?php echo htmlspecialchars($course['units']); ?></td>
                                        <td>
                                            <form method="post" action="">
                                                <input type="hidden" name="drop_course" value="<?php echo htmlspecialchars($course['course_code']); ?>">
                                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Drop</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
            
            <div id="summary" class="tab-content">
                <div class="card-body">
                    <h3>Registration Summary</h3>
                    <p>Summary of your course registration for the current semester.</p>
                    
                    <div class="summary-card">
                        <div class="summary-item">
                            <div class="summary-value"><?php echo count($registered_courses); ?></div>
                            <div class="summary-label">Registered Courses</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-value">
                                <?php 
                                $total_units = 0;
                                foreach ($registered_courses as $course) {
                                    $total_units += $course['units'];
                                }
                                echo $total_units;
                                ?>
                            </div>
                            <div class="summary-label">Total Units</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-value">21</div>
                            <div class="summary-label">Maximum Units Allowed</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-value">
                                <?php echo 21 - $total_units; ?>
                            </div>
                            <div class="summary-label">Units Remaining</div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h4>Registration Deadlines</h4>
                        </div>
                        <div class="card-body">
                            <table>
                                <tr>
                                    <th>Event</th>
                                    <th>Date</th>
                                </tr>
                                <tr>
                                    <td>Registration Start</td>
                                    <td>March 15, 2025</td>
                                </tr>
                                <tr>
                                    <td>Registration End</td>
                                    <td>May 10, 2025</td>
                                </tr>
                                <tr>
                                    <td>Late Registration</td>
                                    <td>May 11 - May 20, 2025</td>
                                </tr>
                                <tr>
                                    <td>Add/Drop Period</td>
                                    <td>May 21 - June 5, 2025</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2025 SPCF Student Portal. All rights reserved.</p>
    </footer>
    
    <script>
        // Tab functionality
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs and content
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked tab and corresponding content
                tab.classList.add('active');
                document.getElementById(tab.dataset.tab).classList.add('active');
            });
        });
    </script>
</body>
</html>