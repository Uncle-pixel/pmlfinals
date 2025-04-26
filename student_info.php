<?php
session_start();

// Example: Check if the user is logged in (optional)
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Example: Fetch dynamic data for student information (replace with your database logic)
$student_info = [
    'name' => 'John Doe',
    'student_id' => '20230001',
    'email' => 'john.doe@spcf.edu',
    'course' => 'Bachelor of Science in Computer Science',
    'year_level' => '3rd Year',
    'status' => 'Active',
    'birthdate' => 'January 1, 2003',
    'gender' => 'Male',
    'address' => '123 Main Street, Cityville',
    'phone' => '09123456789',
    'emergency_contact_name' => 'Jane Doe',
    'emergency_contact_relationship' => 'Mother',
    'emergency_contact_phone' => '09876543210',
    'high_school' => 'City High School',
    'college' => 'SPCF University',
    'family_members' => 'Father: John Sr., Mother: Jane',
    'work_experience' => 'Intern at Tech Solutions Inc.'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information - SPCF PORTAL</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #0073e6;
            color: white;
            padding: 20px;
            text-align: center;
        }
        header img {
            height: 50px;
            vertical-align: middle;
            margin-right: 10px;
        }
        header span {
            font-size: 24px;
            font-weight: bold;
        }
        nav {
            background-color: #005bb5;
            padding: 10px 0;
        }
        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }
        nav ul li {
            margin: 0 10px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        nav ul li a:hover {
            background-color: #003d80;
        }
        .content {
            margin: 20px auto;
            max-width: 800px;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .content h2 {
            text-align: center;
            color: #0073e6;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .info-table th,
        .info-table td {
            padding: 15px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .info-table th {
            background-color: #0073e6;
            color: white;
        }
        .info-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .info-table tr:hover {
            background-color: #f1f1f1;
        }
        footer {
            background-color: #005bb5;
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: 20px;
        }
        @media (max-width: 600px) {
            nav ul {
                flex-direction: column;
                align-items: center;
            }
            nav ul li {
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <header>
        <img src="logo.png" alt="SPCF Logo">
        <span>SPCF PORTAL</span>
    </header>
    <nav>
        <ul>
            <li><a href="student_info.php">Student Information</a></li>
            <li><a href="course_registration.php">Course Registration</a></li>
            <li><a href="faculty_management.php">Faculty Management</a></li>
            <li><a href="grading_system.php">Grading System</a></li>
            <li><a href="class_scheduling.php">Class Scheduling</a></li>
            <li><a href="notifications.php">Notifications/Announcements</a></li>
        </ul>
    </nav>
    <div class="content">
        <h2>Student Information</h2>
        <table class="info-table">
            <tr>
                <th>Name</th>
                <td><?php echo htmlspecialchars($student_info['name']); ?></td>
            </tr>
            <tr>
                <th>Student ID</th>
                <td><?php echo htmlspecialchars($student_info['student_id']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($student_info['email']); ?></td>
            </tr>
            <tr>
                <th>Course</th>
                <td><?php echo htmlspecialchars($student_info['course']); ?></td>
            </tr>
            <tr>
                <th>Year Level</th>
                <td><?php echo htmlspecialchars($student_info['year_level']); ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><?php echo htmlspecialchars($student_info['status']); ?></td>
            </tr>
        </table>

        <h2>Personal Information</h2>
        <table class="info-table">
            <tr>
                <th>Birthdate</th>
                <td><?php echo htmlspecialchars($student_info['birthdate']); ?></td>
            </tr>
            <tr>
                <th>Gender</th>
                <td><?php echo htmlspecialchars($student_info['gender']); ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?php echo htmlspecialchars($student_info['address']); ?></td>
            </tr>
        </table>

        <h2>Contact Information</h2>
        <table class="info-table">
            <tr>
                <th>Phone</th>
                <td><?php echo htmlspecialchars($student_info['phone']); ?></td>
            </tr>
        </table>

        <h2>Emergency Contact</h2>
        <table class="info-table">
            <tr>
                <th>Name</th>
                <td><?php echo htmlspecialchars($student_info['emergency_contact_name']); ?></td>
            </tr>
            <tr>
                <th>Relationship</th>
                <td><?php echo htmlspecialchars($student_info['emergency_contact_relationship']); ?></td>
            </tr>
            <tr>
                <th>Phone</th>
                <td><?php echo htmlspecialchars($student_info['emergency_contact_phone']); ?></td>
            </tr>
        </table>

        <h2>Education Background</h2>
        <table class="info-table">
            <tr>
                <th>High School</th>
                <td><?php echo htmlspecialchars($student_info['high_school']); ?></td>
            </tr>
            <tr>
                <th>College</th>
                <td><?php echo htmlspecialchars($student_info['college']); ?></td>
            </tr>
        </table>

        <h2>Family Background</h2>
        <table class="info-table">
            <tr>
                <th>Family Members</th>
                <td><?php echo htmlspecialchars($student_info['family_members']); ?></td>
            </tr>
        </table>

        <h2>Work Experience</h2>
        <table class="info-table">
            <tr>
                <th>Experience</th>
                <td><?php echo htmlspecialchars($student_info['work_experience']); ?></td>
            </tr>
        </table>
    </div>
    <footer>
        &copy; <?php echo date("Y"); ?> SPCF PORTAL. All Rights Reserved.
    </footer>
</body>
</html>