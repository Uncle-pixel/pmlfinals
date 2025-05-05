<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Database connection would be here in a real application
// For this example, we'll simulate with arrays

// User roles
define('ROLE_STUDENT', 'student');
define('ROLE_FACULTY', 'faculty');
define('ROLE_ADMIN', 'admin');

// Sample user data - in a real app, this would come from a database
$current_user = [
    'id' => $_SESSION['user_id'] ?? 1,
    'name' => $_SESSION['user_name'] ?? 'Test User',
    'role' => $_SESSION['user_role'] ?? ROLE_STUDENT // Default to student for safety
];

// Sample grades data - in a real app, this would come from a database
$grades = [
    ['id' => 1, 'student_id' => 101, 'student_name' => 'John Doe', 'course' => 'Introduction to Programming', 'grade' => 'A', 'status' => 'Passed'],
    ['id' => 2, 'student_id' => 102, 'student_name' => 'Jane Smith', 'course' => 'Calculus II', 'grade' => 'B', 'status' => 'Passed'],
    ['id' => 3, 'student_id' => 103, 'student_name' => 'Mark Johnson', 'course' => 'Physics I', 'grade' => 'F', 'status' => 'Failed'],
    ['id' => 4, 'student_id' => 101, 'student_name' => 'John Doe', 'course' => 'Database Systems', 'grade' => 'C+', 'status' => 'Passed'],
    ['id' => 5, 'student_id' => 102, 'student_name' => 'Jane Smith', 'course' => 'Web Development', 'grade' => 'A-', 'status' => 'Passed']
];

// Process grade updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_grade') {
    // Check if user has permission to edit
    if ($current_user['role'] === ROLE_FACULTY || $current_user['role'] === ROLE_ADMIN) {
        $grade_id = $_POST['grade_id'] ?? 0;
        $new_grade = $_POST['grade'] ?? '';
        $new_status = ($new_grade === 'F') ? 'Failed' : 'Passed';
        
        // In a real app, update the database
        // For this example, update our array
        foreach ($grades as $key => $grade) {
            if ($grade['id'] == $grade_id) {
                $grades[$key]['grade'] = $new_grade;
                $grades[$key]['status'] = $new_status;
                
                // Set a success message
                $_SESSION['message'] = "Grade updated successfully!";
                $_SESSION['message_type'] = "success";
                break;
            }
        }
    } else {
        // Set an error message
        $_SESSION['message'] = "You don't have permission to edit grades.";
        $_SESSION['message_type'] = "error";
    }
    
    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// For student users, filter grades to show only their own
if ($current_user['role'] === ROLE_STUDENT) {
    // In a real app, this would filter by the actual student ID
    // For this example, let's assume student_id 101 is logged in
    $student_id = 101; // This would be fetched from the session in a real app
    $filtered_grades = array_filter($grades, function($grade) use ($student_id) {
        return $grade['student_id'] == $student_id;
    });
    $grades = $filtered_grades;
}

// Function to determine if user can edit grades
function canEditGrades($userRole) {
    return $userRole === ROLE_FACULTY || $userRole === ROLE_ADMIN;
}

// Function to determine if user is admin
function isAdmin($userRole) {
    return $userRole === ROLE_ADMIN;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Grading System - SPCF PORTAL</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            text-align: center;
        }
        .hamburger-container {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1000;
        }
        .hamburger {
            display: inline-block;
            cursor: pointer;
        }
        .hamburger div {
            width: 30px;
            height: 4px;
            background-color: #333;
            margin: 6px 0;
        }
        .menu {
            display: none;
            position: absolute;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 10px;
            z-index: 100;
            text-align: left;
            min-width: 150px;
        }
        .menu a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
        }
        .menu a:hover {
            background-color: #eee;
        }
        .user-info {
            position: absolute;
            top: 10px;
            right: 10px;
            color: white;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 5px 10px;
            border-radius: 5px;
        }
        h1 {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #0073e6;
            color: white;
            padding: 20px;
            margin: 0;
        }
        h1 img {
            height: 50px;
            margin-right: 10px;
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
            flex-wrap: wrap;
        }
        nav ul li {
            margin: 0 15px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            display: block;
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
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .grades-table th,
        .grades-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .grades-table th {
            background-color: #0073e6;
            color: white;
        }
        .grades-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .grades-table tr:hover {
            background-color: #f1f1f1;
        }
        .status-passed {
            color: #27ae60;
            font-weight: bold;
        }
        .status-failed {
            color: #e74c3c;
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            padding: 8px 12px;
            margin: 2px;
            background-color: #0073e6;
            color: white;
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
        .btn-success {
            background-color: #27ae60;
        }
        .btn-success:hover {
            background-color: #219653;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 5px;
            width: 50%;
            max-width: 500px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: black;
        }
        .form-group {
            margin: 10px 0;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .role-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .role-student {
            background-color: #3498db;
            color: white;
        }
        .role-faculty {
            background-color: #f39c12;
            color: white;
        }
        .role-admin {
            background-color: #8e44ad;
            color: white;
        }
        .filter-section {
            margin-bottom: 20px;
            text-align: left;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }
        .search-box {
            width: 100%;
            max-width: 300px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-right: 10px;
        }
        @media (max-width: 768px) {
            nav ul {
                flex-direction: column;
            }
            nav ul li {
                margin: 5px 0;
            }
            .grades-table {
                font-size: 14px;
            }
            .modal-content {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <!-- Hamburger Menu -->
    <div class="hamburger-container">
        <div class="hamburger" onclick="toggleMenu()">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <div id="menu" class="menu">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="profile.php"><i class="fas fa-user-circle"></i> My Profile</a>
            <?php if (canEditGrades($current_user['role'])): ?>
            <a href="grade_management.php"><i class="fas fa-chart-bar"></i> Grade Management</a>
            <?php endif; ?>
            <?php if (isAdmin($current_user['role'])): ?>
            <a href="user_management.php"><i class="fas fa-users-cog"></i> User Management</a>
            <a href="system_settings.php"><i class="fas fa-cogs"></i> System Settings</a>
            <?php endif; ?>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>
        </div>
    </div>

    <!-- User Info -->
    <div class="user-info">
        <span><?php echo htmlspecialchars($current_user['name']); ?></span>
        <span class="role-badge role-<?php echo strtolower($current_user['role']); ?>">
            <?php echo htmlspecialchars(ucfirst($current_user['role'])); ?>
        </span>
    </div>

    <!-- Header -->
    <h1>
        <img src="logo.png" alt="SPCF Logo">
        <span>SPCF PORTAL</span>
    </h1>

    <!-- Navigation -->
    <nav>
        <ul>
            <li><a href="student_info.php"><i class="fas fa-user-graduate"></i> Student Information</a></li>
            <li><a href="course_registration.php"><i class="fas fa-book-open"></i> Course Registration</a></li>
            <?php if (canEditGrades($current_user['role'])): ?>
            <li><a href="faculty_management.php"><i class="fas fa-chalkboard-teacher"></i> Faculty Management</a></li>
            <?php endif; ?>
            <li><a href="grading_system.php"><i class="fas fa-chart-line"></i> Grading System</a></li>
            <li><a href="class_scheduling.php"><i class="fas fa-calendar-alt"></i> Class Scheduling</a></li>
            <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="content">
        <h2>Grading System</h2>
        
        <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
            <?php 
                echo htmlspecialchars($_SESSION['message']); 
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            ?>
        </div>
        <?php endif; ?>

        <?php if (canEditGrades($current_user['role'])): ?>
        <p>View and manage student grades. As a <?php echo htmlspecialchars(ucfirst($current_user['role'])); ?>, you can edit grades.</p>
        
        <!-- Filter and Search Section (for faculty/admin) -->
        <div class="filter-section">
            <input type="text" id="searchInput" class="search-box" placeholder="Search by name or course..." onkeyup="searchTable()">
            <button class="btn" onclick="resetSearch()"><i class="fas fa-sync-alt"></i> Reset</button>
            <?php if (isAdmin($current_user['role'])): ?>
            <button class="btn btn-success" onclick="openAddGradeModal()"><i class="fas fa-plus"></i> Add New Grade</button>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <p>View your academic performance and grades.</p>
        <?php endif; ?>
        
        <table class="grades-table" id="gradesTable">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Grade</th>
                    <th>Status</th>
                    <?php if (canEditGrades($current_user['role'])): ?>
                    <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grades as $grade): ?>
                <tr>
                    <td><?php echo htmlspecialchars($grade['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($grade['course']); ?></td>
                    <td><?php echo htmlspecialchars($grade['grade']); ?></td>
                    <td class="<?php echo $grade['status'] === 'Passed' ? 'status-passed' : 'status-failed'; ?>">
                        <?php echo htmlspecialchars($grade['status']); ?>
                    </td>
                    <?php if (canEditGrades($current_user['role'])): ?>
                    <td>
                        <button class="btn" onclick="openEditModal(<?php echo $grade['id']; ?>, '<?php echo htmlspecialchars($grade['student_name']); ?>', '<?php echo htmlspecialchars($grade['course']); ?>', '<?php echo htmlspecialchars($grade['grade']); ?>')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <?php if (isAdmin($current_user['role'])): ?>
                        <button class="btn btn-danger" onclick="confirmDelete(<?php echo $grade['id']; ?>)">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (canEditGrades($current_user['role'])): ?>
    <!-- Edit Grade Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editModal')">&times;</span>
            <h3>Edit Grade</h3>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="action" value="update_grade">
                <input type="hidden" id="editGradeId" name="grade_id">
                
                <div class="form-group">
                    <label for="editStudentName">Student Name:</label>
                    <input type="text" id="editStudentName" class="form-control" readonly>
                </div>
                
                <div class="form-group">
                    <label for="editCourse">Course:</label>
                    <input type="text" id="editCourse" class="form-control" readonly>
                </div>
                
                <div class="form-group">
                    <label for="editGrade">Grade:</label>
                    <select id="editGrade" name="grade" class="form-control" required>
                        <option value="A+">A+</option>
                        <option value="A">A</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B">B</option>
                        <option value="B-">B-</option>
                        <option value="C+">C+</option>
                        <option value="C">C</option>
                        <option value="C-">C-</option>
                        <option value="D+">D+</option>
                        <option value="D">D</option>
                        <option value="D-">D-</option>
                        <option value="F">F</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-success">Save Changes</button>
            </form>
        </div>
    </div>

    <?php if (isAdmin($current_user['role'])): ?>
    <!-- Add Grade Modal -->
    <div id="addGradeModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addGradeModal')">&times;</span>
            <h3>Add New Grade</h3>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="action" value="add_grade">
                
                <div class="form-group">
                    <label for="addStudentName">Student Name:</label>
                    <input type="text" id="addStudentName" name="student_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="addStudentId">Student ID:</label>
                    <input type="number" id="addStudentId" name="student_id" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="addCourse">Course:</label>
                    <input type="text" id="addCourse" name="course" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="addGrade">Grade:</label>
                    <select id="addGrade" name="grade" class="form-control" required>
                        <option value="A+">A+</option>
                        <option value="A">A</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B">B</option>
                        <option value="B-">B-</option>
                        <option value="C+">C+</option>
                        <option value="C">C</option>
                        <option value="C-">C-</option>
                        <option value="D+">D+</option>
                        <option value="D">D</option>
                        <option value="D-">D-</option>
                        <option value="F">F</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-success">Add Grade</button>
            </form>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('deleteModal')">&times;</span>
            <h3>Confirm Deletion</h3>
            <p>Are you sure you want to delete this grade record? This action cannot be undone.</p>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="action" value="delete_grade">
                <input type="hidden" id="deleteGradeId" name="grade_id">
                <button type="button" class="btn" onclick="closeModal('deleteModal')">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <script>
        function toggleMenu() {
            var menu = document.getElementById("menu");
            menu.style.display = (menu.style.display === "block") ? "none" : "block";
        }
        
        <?php if (canEditGrades($current_user['role'])): ?>
        function openEditModal(gradeId, studentName, course, grade) {
            document.getElementById("editGradeId").value = gradeId;
            document.getElementById("editStudentName").value = studentName;
            document.getElementById("editCourse").value = course;
            document.getElementById("editGrade").value = grade;
            document.getElementById("editModal").style.display = "block";
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }
        
        function searchTable() {
            var input, filter, table, tr, td, i, j, txtValue, found;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("gradesTable");
            tr = table.getElementsByTagName("tr");
            
            for (i = 1; i < tr.length; i++) {
                found = false;
                for (j = 0; j < 2; j++) { // Search in first two columns (name and course)
                    td = tr[i].getElementsByTagName("td")[j];
                    if (td) {
                        txtValue = td.textContent || td.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = found ? "" : "none";
            }
        }
        
        function resetSearch() {
            document.getElementById("searchInput").value = "";
            var table = document.getElementById("gradesTable");
            var tr = table.getElementsByTagName("tr");
            for (var i = 1; i < tr.length; i++) {
                tr[i].style.display = "";
            }
        }
        
        <?php if (isAdmin($current_user['role'])): ?>
        function openAddGradeModal() {
            document.getElementById("addGradeModal").style.display = "block";
        }
        
        function confirmDelete(gradeId) {
            document.getElementById("deleteGradeId").value = gradeId;
            document.getElementById("deleteModal").style.display = "block";
        }
        <?php endif; ?>
        <?php endif; ?>
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            var modals = document.getElementsByClassName("modal");
            for (var i = 0; i < modals.length; i++) {
                if (event.target == modals[i]) {
                    modals[i].style.display = "none";
                }
            }
        }
    </script>
</body>
</html>