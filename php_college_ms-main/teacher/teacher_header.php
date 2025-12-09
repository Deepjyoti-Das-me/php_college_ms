<?php
require_once "../database.php";
$teacher_email = $_SESSION["user_email"] ?? "";
$teacher_name = $_SESSION["user_name"] ?? "Teacher";

$teacher_query = "SELECT t.*, u.email FROM teachers t JOIN users u ON t.user_id = u.id WHERE u.email = '$teacher_email' LIMIT 1";
$teacher_result = mysqli_query($conn, $teacher_query);
$teacher_data = mysqli_fetch_assoc($teacher_result);
$teacher_fname = $teacher_data['fname'] ?? 'Teacher';
$teacher_lname = $teacher_data['lname'] ?? '';
$tid = $teacher_data['tid'] ?? 0;
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
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($teacher_fname . ' ' . $teacher_lname); ?>&background=2196F3&color=fff" alt="Teacher">
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
            <img src="https://ui-avatars.com/api/?name=CMS&background=2196F3&color=fff&size=60" alt="Logo">
        </div>
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($teacher_fname . ' ' . $teacher_lname); ?>&background=2196F3&color=fff" alt="User">
            </div>
            <div class="sidebar-username"><?php echo $teacher_fname; ?></div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="teacher.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'teacher.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="teacher_my_profile.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'teacher_my_profile.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-edit"></i>
            <span>My Profile</span>
        </a>
        <a href="teacher_classes.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'teacher_classes.php' ? 'active' : ''; ?>">
            <i class="fas fa-chalkboard"></i>
            <span>My Classes</span>
        </a>
        <a href="teacher_take_attendance.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'teacher_take_attendance.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-check"></i>
            <span>Take Attendance</span>
        </a>
        <a href="teacher_assignments.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'teacher_assignments.php' ? 'active' : ''; ?>">
            <i class="fas fa-tasks"></i>
            <span>Assignments</span>
        </a>
        <a href="teacher_grades.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'teacher_grades.php' ? 'active' : ''; ?>">
            <i class="fas fa-chart-line"></i>
            <span>Grades & Results</span>
        </a>
        <a href="teacher_my_students.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'teacher_my_students.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-friends"></i>
            <span>My Students</span>
        </a>
        <a href="teacher_schedule.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'teacher_schedule.php' ? 'active' : ''; ?>">
            <i class="fas fa-clock"></i>
            <span>Schedule</span>
        </a>
        <a href="teacher_notifications.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'teacher_notifications.php' ? 'active' : ''; ?>">
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

