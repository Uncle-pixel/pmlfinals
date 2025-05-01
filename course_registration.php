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
            text-align: left;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: left;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-right: 5px;
        }
        
        .tab.active {
            border-bottom: 3px solid #0073e6;
            color: #0073e6;
            font-weight: bold;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .course-list {
            margin-top: 20px;
        }
        
        .course-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .course-card h3 {
            margin: 0;
            color: #0073e6;
        }
        
        .course-code {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 10px;
        }
        
        .course-details {
            margin: 15px 0;
        }
        
        .course-details p {
            margin: 5px 0;
        }
        
        .status {
            font-weight: bold;
        }
        
        .status.available {
            color: #27ae60;
        }
        
        .status.full {
            color: #e74c3c;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f2f2f2;
        }
        
        .btn {
            display: inline-block;
            background-color: #0073e6;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        
        .btn:hover {
            background-color: #005bb5;
        }
        
        .btn-danger {
            background-color: #e74c3c;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        
        .summary-card {
            display: flex;
            justify-content: space-between;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0073e6;
        }
        
        .summary-label {
            font-size: 0.9rem;
            color: #666;
        }
        
        @media (max-width: 768px) {
            nav ul {
                flex-direction: column;
            }
            
            nav ul li {
                margin: 5px 0;
            }
            
            .summary-card {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <h1>Course Registration</h1>
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
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_message']; ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <h2>Course Registration</h2>
        <p>Register for courses for the Academic Year 2024-2025, 2nd Semester.</p>
        
        <div class="tabs">
            <div class="tab active" data-tab="available">Available Courses</div>
            <div class="tab" data-tab="registered">Registered Courses</div>
            <div class="tab" data-tab="summary">Registration Summary</div>
        </div>
        
        <div id="available" class="tab-content active">
            <h3>Available Courses for Registration</h3>
            <p>Below are the courses available for the current semester. Click "Register" to add a course to your schedule.</p>
            
            <div class="course-list">
                <?php foreach ($courses as $course): ?>
                    <div class="course-card">
                        <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
                        <div class="course-code"><?php echo htmlspecialchars($course['course_code']); ?></div>
                        
                        <p><?php echo htmlspecialchars($course['description']); ?></p>
                        
                        <div class="course-details">
                            <p>Instructor: <?php echo htmlspecialchars($course['instructor']); ?></p>
                            <p>Schedule: <?php echo htmlspecialchars($course['schedule']); ?></p>
                            <p>Units: <?php echo htmlspecialchars($course['units']); ?></p>
                            <p>Slots: <?php echo htmlspecialchars($course['slots']); ?></p>
                            <p class="status <?php echo strtolower($course['status']); ?>">
                                Status: <?php echo htmlspecialchars($course['status']); ?>
                            </p>
                        </div>
                        
                        <form method="post" action="">
                            <input type="hidden" name="course_code" value="<?php echo htmlspecialchars($course['course_code']); ?>">
                            <button type="submit" name="add_course" class="btn" <?php echo $course['status'] === 'Full' ? 'disabled' : ''; ?>>
                                <?php echo $course['status'] === 'Full' ? 'Course Full' : 'Register Course'; ?>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div id="registered" class="tab-content">
            <h3>Your Registered Courses</h3>
            <p>These are the courses you have registered for the current semester.</p>
            
            <?php if (empty($registered_courses)): ?>
                <div class="alert alert-success">
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
                                        <button type="submit" class="btn btn-danger">Drop</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div id="summary" class="tab-content">
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
            
            <div class="course-card">
                <h3>Registration Deadlines</h3>
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