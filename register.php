<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "spcf_portal";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$error_message = "";
$success_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = trim($_POST['role']);
    $student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : null;
    $program = isset($_POST['program']) ? trim($_POST['program']) : null;
    $year_level = isset($_POST['year_level']) ? intval($_POST['year_level']) : null;

    // Validate form inputs
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } elseif ($role === "student" && (empty($student_id) || empty($program) || empty($year_level))) {
        $error_message = "Student details are required.";
    } else {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Check if the email already exists in any of the three user tables
            $email_exists = false;
            
            // Check students table
            $check_student_email = $conn->prepare("SELECT student_id FROM students WHERE email = ?");
            $check_student_email->bind_param("s", $email);
            $check_student_email->execute();
            $check_student_email->store_result();
            if ($check_student_email->num_rows > 0) {
                $email_exists = true;
            }
            $check_student_email->close();
            
            // Check faculty table
            if (!$email_exists) {
                $check_faculty_email = $conn->prepare("SELECT faculty_id FROM faculty WHERE email = ?");
                $check_faculty_email->bind_param("s", $email);
                $check_faculty_email->execute();
                $check_faculty_email->store_result();
                if ($check_faculty_email->num_rows > 0) {
                    $email_exists = true;
                }
                $check_faculty_email->close();
            }
            
            // Check admins table
            if (!$email_exists) {
                $check_admin_email = $conn->prepare("SELECT admin_id FROM admins WHERE email = ?");
                $check_admin_email->bind_param("s", $email);
                $check_admin_email->execute();
                $check_admin_email->store_result();
                if ($check_admin_email->num_rows > 0) {
                    $email_exists = true;
                }
                $check_admin_email->close();
            }
            
            if ($email_exists) {
                $error_message = "This email is already registered.";
                throw new Exception($error_message);
            }
            
            // Check if student ID already exists (for student role)
            if ($role === "student" && !empty($student_id)) {
                $check_student_id = $conn->prepare("SELECT student_id FROM students WHERE student_number = ?");
                $check_student_id->bind_param("s", $student_id);
                $check_student_id->execute();
                $check_student_id->store_result();
                
                if ($check_student_id->num_rows > 0) {
                    $error_message = "This student ID is already registered.";
                    $check_student_id->close();
                    throw new Exception($error_message);
                }
                $check_student_id->close();
            }
            
            // Insert user based on role
            if ($role === "student") {
                $insert_student = $conn->prepare("INSERT INTO students (name, email, password, student_number, program, year_level, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $insert_student->bind_param("sssssi", $name, $email, $password, $student_id, $program, $year_level);
                
                if (!$insert_student->execute()) {
                    throw new Exception("Error creating student account: " . $insert_student->error);
                }
                $insert_student->close();
                
            } elseif ($role === "teacher") {
                // Generate an employee ID for teachers
                $employee_id = "TCHR" . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
                
                $insert_teacher = $conn->prepare("INSERT INTO faculty (name, email, password, employee_id, created_at) VALUES (?, ?, ?, ?, NOW())");
                $insert_teacher->bind_param("ssss", $name, $email, $password, $employee_id);
                
                if (!$insert_teacher->execute()) {
                    throw new Exception("Error creating teacher account: " . $insert_teacher->error);
                }
                $insert_teacher->close();
                
            } elseif ($role === "admin") {
                // Generate an admin number for admins
                $admin_number = "ADM" . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
                
                $insert_admin = $conn->prepare("INSERT INTO admins (name, email, password, admin_number, created_at) VALUES (?, ?, ?, ?, NOW())");
                $insert_admin->bind_param("ssss", $name, $email, $password, $admin_number);
                
                if (!$insert_admin->execute()) {
                    throw new Exception("Error creating admin account: " . $insert_admin->error);
                }
                $insert_admin->close();
            }
            
            // Commit transaction
            $conn->commit();
            
            // Set success message or redirect
            $_SESSION['registration_success'] = true;
            header("Location: login.php?registered=true");
            exit();
            
        } catch (Exception $e) {
            // Roll back the transaction if something failed
            $conn->rollback();
            $error_message = $e->getMessage();
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SPCF PORTAL</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #0044cc;
            --primary-dark: #003399;
            --secondary-color: #005bb5;
            --accent-color: #ffcc00;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --light-bg: #f8f9fa;
            --dark-text: #333;
            --light-text: #f8f9fa;
            --border-radius: 8px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: var(--light-bg);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            background-image: url('school-bg.jpg');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }
        
        .register-container {
            background: white;
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 500px;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        
        .logo {
            margin-bottom: 20px;
        }
        
        .logo img {
            height: 80px;
        }
        
        .register-container h1 {
            margin-bottom: 20px;
            color: var(--primary-color);
            font-size: 2rem;
        }
        
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--dark-text);
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }
        
        .register-container input[type="text"],
        .register-container input[type="email"],
        .register-container input[type="password"],
        .register-container select {
            width: 100%;
            padding: 12px 12px 12px 35px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .register-container input:focus,
        .register-container select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 68, 204, 0.1);
        }
        
        .role-specific-fields {
            display: none;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 4px;
            margin-top: 10px;
            border: 1px solid #eee;
        }
        
        .register-container button {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        
        .register-container button:hover {
            background-color: var(--primary-dark);
        }
        
        .register-container a {
            display: block;
            margin-top: 20px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .register-container a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            color: var(--danger-color);
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8d7da;
            border-radius: 4px;
            border: 1px solid #f5c6cb;
        }
        
        .success-message {
            color: var(--success-color);
            margin-bottom: 15px;
            padding: 10px;
            background-color: #d4edda;
            border-radius: 4px;
            border: 1px solid #c3e6cb;
        }
        
        .password-requirements {
            margin-top: 5px;
            font-size: 12px;
            color: #666;
        }
        
        .password-strength {
            height: 5px;
            width: 100%;
            background-color: #ddd;
            margin-top: 5px;
            border-radius: 2px;
            position: relative;
        }
        
        .password-strength span {
            height: 100%;
            width: 0;
            border-radius: 2px;
            position: absolute;
            transition: width 0.3s, background-color 0.3s;
        }
        
        .strength-weak {
            background-color: #dc3545;
            width: 33% !important;
        }
        
        .strength-medium {
            background-color: #ffc107;
            width: 66% !important;
        }
        
        .strength-strong {
            background-color: #28a745;
            width: 100% !important;
        }
        
        @media (max-width: 576px) {
            .register-container {
                padding: 20px;
            }
        }
        
        /* Toggle password visibility */
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <img src="school-logo.png" alt="SPCF Logo">
        </div>
        <h1>Create Your Account</h1>
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        <form action="register.php" method="POST" id="registerForm">
            <div class="form-group">
                <label for="name">Full Name</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Create a password" required>
                    <span class="password-toggle" id="passwordToggle">
                        <i class="far fa-eye"></i>
                    </span>
                </div>
                <div class="password-strength">
                    <span id="passwordStrengthBar"></span>
                </div>
                <div class="password-requirements">
                    Password must be at least 8 characters long
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                    <span class="password-toggle" id="confirmPasswordToggle">
                        <i class="far fa-eye"></i>
                    </span>
                </div>
            </div>
            
            <div class="form-group">
                <label for="role">Role</label>
                <div class="input-group">
                    <i class="fas fa-user-tag"></i>
                    <select id="role" name="role" required>
                        <option value="" disabled selected>Select your role</option>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            
            <!-- Student-specific fields -->
            <div id="studentFields" class="role-specific-fields">
                <div class="form-group">
                    <label for="student_id">Student ID</label>
                    <div class="input-group">
                        <i class="fas fa-id-card"></i>
                        <input type="text" id="student_id" name="student_id" placeholder="Enter your student ID">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="program">Program</label>
                    <div class="input-group">
                        <i class="fas fa-graduation-cap"></i>
                        <select id="program" name="program">
                            <option value="" disabled selected>Select your program</option>
                            <option value="BSCS">BS Computer Science</option>
                            <option value="BSIT">BS Information Technology</option>
                            <option value="BSN">BS Nursing</option>
                            <option value="BSBA">BS Business Administration</option>
                            <option value="BSED">BS Education</option>
                            <option value="BSCE">BS Civil Engineering</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="year_level">Year Level</label>
                    <div class="input-group">
                        <i class="fas fa-layer-group"></i>
                        <select id="year_level" name="year_level">
                            <option value="" disabled selected>Select your year level</option>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                            <option value="5">5th Year</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <button type="submit">Create Account</button>
        </form>
        <a href="login.php">Already have an account? Log In</a>
    </div>
    
    <script>
        // Show/hide fields based on role selection
        document.getElementById('role').addEventListener('change', function() {
            var studentFields = document.getElementById('studentFields');
            
            if (this.value === 'student') {
                studentFields.style.display = 'block';
                document.getElementById('student_id').required = true;
                document.getElementById('program').required = true;
                document.getElementById('year_level').required = true;
            } else {
                studentFields.style.display = 'none';
                document.getElementById('student_id').required = false;
                document.getElementById('program').required = false;
                document.getElementById('year_level').required = false;
            }
        });
        
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            var password = this.value;
            var strengthBar = document.getElementById('passwordStrengthBar');
            
            // Reset the strength bar
            strengthBar.className = '';
            
            if (password.length === 0) {
                strengthBar.style.width = '0';
                return;
            }
            
            // Evaluate password strength
            var strength = 0;
            if (password.length >= 8) strength += 1;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1;
            if (password.match(/\d/)) strength += 1;
            if (password.match(/[^a-zA-Z\d]/)) strength += 1;
            
            // Update the strength bar
            if (strength <= 1) {
                strengthBar.className = 'strength-weak';
            } else if (strength <= 2) {
                strengthBar.className = 'strength-medium';
            } else {
                strengthBar.className = 'strength-strong';
            }
        });
        
        // Toggle password visibility
        document.getElementById('passwordToggle').addEventListener('click', function() {
            var passwordInput = document.getElementById('password');
            var icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.className = 'far fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                icon.className = 'far fa-eye';
            }
        });
        
        document.getElementById('confirmPasswordToggle').addEventListener('click', function() {
            var passwordInput = document.getElementById('confirm_password');
            var icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.className = 'far fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                icon.className = 'far fa-eye';
            }
        });
        
        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                event.preventDefault();
                alert('Passwords do not match!');
            }
        });
    </script>
</body>
</html>