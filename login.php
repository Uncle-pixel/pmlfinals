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

// Initialize error message
$error_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = isset($_POST['selected_role']) ? trim($_POST['selected_role']) : 'student';
    
    $user_found = false;
    $user_id = null;
    $user_name = null;
    $user_role = null;
    
    // Check in the appropriate table based on selected role
    if ($role == 'student') {
        $stmt = $conn->prepare("SELECT student_id, name, password FROM students WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $user_name, $stored_password);
            $stmt->fetch();
            
            if ($password === $stored_password) {
                $user_found = true;
                $user_role = 'student';
            }
        }
        $stmt->close();
    } 
    elseif ($role == 'faculty') {
        $stmt = $conn->prepare("SELECT faculty_id, name, password FROM faculty WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $user_name, $stored_password);
            $stmt->fetch();
            
            if ($password === $stored_password) {
                $user_found = true;
                $user_role = 'faculty';
            }
        }
        $stmt->close();
    } 
    elseif ($role == 'admin') {
        $stmt = $conn->prepare("SELECT admin_id, name, password FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $user_name, $stored_password);
            $stmt->fetch();
            
            if ($password === $stored_password) {
                $user_found = true;
                $user_role = 'admin';
            }
        }
        $stmt->close();
    }
    
    // If user is found and password matches, set session variables and redirect
    if ($user_found) {
        // Set session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $user_name;
        $_SESSION['user_role'] = $user_role;
        $_SESSION['logged_in'] = true;
        
        // Set permissions based on role
        if ($user_role == 'admin') {
            $_SESSION['can_edit'] = true;
            $_SESSION['can_view'] = true;
            $_SESSION['can_delete'] = true;
        } elseif ($user_role == 'faculty') {
            $_SESSION['can_edit'] = true;
            $_SESSION['can_view'] = true;
            $_SESSION['can_delete'] = false;
        } elseif ($user_role == 'student') {
            $_SESSION['can_edit'] = false;
            $_SESSION['can_view'] = true;
            $_SESSION['can_delete'] = false;
        }

        // Set last activity time for session timeout
        $_SESSION['last_activity'] = time();
        
        // Redirect to the dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Invalid email or password for the selected role.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - SPCF PORTAL</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('school-bg.jpg');
            background-size: cover;
            background-position: center;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 320px;
            text-align: center;
        }

        .school-logo {
            width: 80px;
            margin-bottom: 15px;
        }

        .login-container h1 {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-size: 14px;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 10px;
            top: 12px;
            color: #aaa;
        }

        .login-container input[type="email"],
        .login-container input[type="password"] {
            width: calc(100% - 40px);
            padding: 10px 10px 10px 30px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .login-container button {
            width: 100%;
            padding: 12px;
            background-color: #0052cc;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-weight: bold;
        }

        .login-container button:hover {
            background-color: #003d99;
        }

        .login-options {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            font-size: 14px;
        }

        .login-options a {
            color: #0052cc;
            text-decoration: none;
        }

        .login-options a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #e74c3c;
            background-color: #fce4e4;
            padding: 8px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .role-selector {
            margin-bottom: 15px;
            text-align: center;
        }

        .role-option {
            display: inline-block;
            margin: 0 5px;
            padding: 5px 10px;
            background-color: #f0f0f0;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .role-option.active {
            background-color: #0052cc;
            color: white;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="school-logo.png" alt="SPCF Logo" class="school-logo">
        <h1>SPCF Portal Login</h1>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <div class="role-selector">
            <div class="role-option active" data-role="student">Student</div>
            <div class="role-option" data-role="faculty">Faculty</div>
            <div class="role-option" data-role="admin">Admin</div>
        </div>
        
        <form action="login.php" method="POST">
            <input type="hidden" name="selected_role" id="selected_role" value="student">
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
            </div>
            
            <button type="submit">Login</button>
        </form>
        
        <div class="login-options">
            <a href="forgot-password.php">Forgot Password?</a>
            <a href="register.php">Create Account</a>
        </div>
    </div>

    <script>
        // Role selector functionality
        document.querySelectorAll('.role-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all options
                document.querySelectorAll('.role-option').forEach(opt => opt.classList.remove('active'));
                
                // Add active class to selected option
                this.classList.add('active');
                
                // Update hidden input value with selected role
                const role = this.getAttribute('data-role');
                document.getElementById('selected_role').value = role;
            });
        });
    </script>
</body>
</html>