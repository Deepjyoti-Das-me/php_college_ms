<?php
require_once "../database.php";
$student_email = $_SESSION["user_email"] ?? "";
$student_name = $_SESSION["user_name"] ?? "Student";

$student_query = "SELECT s.*, u.email FROM students s JOIN users u ON s.user_id = u.id WHERE u.email = '$student_email' LIMIT 1";
$student_result = mysqli_query($conn, $student_query);
$student_data = mysqli_fetch_assoc($student_result);
$student_fname = $student_data['fname'] ?? 'Student';
$student_lname = $student_data['lname'] ?? '';
?>
<!-- Header -->
<header class="header">
    <div class="header-left">
        <button class="hamburger-btn" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="logo-text">College Management System</div>
    </div>
    <div class="header-right">
        <div class="user-avatar">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student_fname . ' ' . $student_lname); ?>&background=4CAF50&color=fff" alt="Student">
        </div>
        <button class="settings-btn">
            <i class="fas fa-cog"></i>
        </button>
    </div>
</header>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-top">
        <div class="sidebar-logo">
            <img src="https://ui-avatars.com/api/?name=CMS&background=4CAF50&color=fff&size=60" alt="Logo">
        </div>
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student_fname . ' ' . $student_lname); ?>&background=4CAF50&color=fff" alt="User">
            </div>
            <div class="sidebar-username"><?php echo $student_fname; ?></div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="student.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'student.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="student_my_profile.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'student_my_profile.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-edit"></i>
            <span>My Profile</span>
        </a>
        <a href="student_attendance.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'student_attendance.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-check"></i>
            <span>My Attendance</span>
        </a>
        <a href="student_courses.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'student_courses.php' ? 'active' : ''; ?>">
            <i class="fas fa-book"></i>
            <span>My Courses</span>
        </a>
        <a href="student_assignments.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'student_assignments.php' ? 'active' : ''; ?>">
            <i class="fas fa-clipboard-list"></i>
            <span>Assignments</span>
        </a>
        <a href="student_grades.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'student_grades.php' ? 'active' : ''; ?>">
            <i class="fas fa-chart-line"></i>
            <span>Grades & Results</span>
        </a>
        <a href="student_schedule.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'student_schedule.php' ? 'active' : ''; ?>">
            <i class="fas fa-clock"></i>
            <span>Class Schedule</span>
        </a>
        <a href="student_leave.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'student_leave.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-minus"></i>
            <span>Leave Request</span>
        </a>
        <a href="student_feedback.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'student_feedback.php' ? 'active' : ''; ?>">
            <i class="fas fa-comment-dots"></i>
            <span>Give Feedback</span>
        </a>
        <a href="student_notifications.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'student_notifications.php' ? 'active' : ''; ?>">
            <i class="fas fa-bell"></i>
            <span>Notifications</span>
        </a>
        <a href="../logout.php" class="nav-item logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </nav>
</aside>

<script>
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            if (mainContent) mainContent.classList.toggle('expanded');
        });
    }
</script>


