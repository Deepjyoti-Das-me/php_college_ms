<?php
require_once "../database.php";
// Get admin info
$admin_email = $_SESSION["user_email"] ?? "";
$admin_name = $_SESSION["user_name"] ?? "Admin";

// Get admin details from database
$admin_query = "SELECT a.*, u.email FROM admin a JOIN users u ON a.user_id = u.id WHERE u.email = '$admin_email' LIMIT 1";
$admin_result = mysqli_query($conn, $admin_query);
$admin_data = mysqli_fetch_assoc($admin_result);
$admin_fname = $admin_data['fname'] ?? 'Admin';
$admin_lname = $admin_data['lname'] ?? 'User';
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
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_fname . ' ' . $admin_lname); ?>&background=1E5DE7&color=fff" alt="Admin">
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
            <img src="https://ui-avatars.com/api/?name=CMS&background=22B3A6&color=fff&size=60" alt="Logo">
        </div>
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_fname . ' ' . $admin_lname); ?>&background=22B3A6&color=fff" alt="User">
            </div>
            <div class="sidebar-username"><?php echo $admin_fname . ', Admin'; ?></div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="admin.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="admin_update_profile.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_update_profile.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-edit"></i>
            <span>Update Profile</span>
        </a>
        
        <!-- Course with Dropdown -->
        <div class="nav-item-dropdown <?php echo (strpos($_SERVER['PHP_SELF'], 'course') !== false) ? 'active' : ''; ?>">
            <a href="#" class="nav-item dropdown-toggle">
                <i class="fas fa-book"></i>
                <span>Course</span>
                <i class="fas fa-chevron-down dropdown-arrow"></i>
            </a>
            <ul class="dropdown-menu">
                <li><a href="admin_add_course.php"><i class="fas fa-plus"></i> Add Course</a></li>
                <li><a href="admin_manage_course.php"><i class="fas fa-list"></i> Manage Courses</a></li>
            </ul>
        </div>
        
        <!-- Subject with Dropdown -->
        <div class="nav-item-dropdown <?php echo (strpos($_SERVER['PHP_SELF'], 'subject') !== false) ? 'active' : ''; ?>">
            <a href="#" class="nav-item dropdown-toggle">
                <i class="fas fa-book-open"></i>
                <span>Subject</span>
                <i class="fas fa-chevron-down dropdown-arrow"></i>
            </a>
            <ul class="dropdown-menu">
                <li><a href="admin_add_subject.php"><i class="fas fa-plus"></i> Add Subject</a></li>
                <li><a href="admin_manage_subject.php"><i class="fas fa-list"></i> Manage Subjects</a></li>
            </ul>
        </div>
        
        <a href="admin_session.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_session.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-alt"></i>
            <span>Session</span>
        </a>
        <a href="admin_add_teacher.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_add_teacher.php' ? 'active' : ''; ?>">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Add Teacher</span>
        </a>
        <a href="admin_manage_teacher.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_manage_teacher.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-tie"></i>
            <span>Manage Teacher</span>
        </a>
        <a href="admin_add_staff.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_add_staff.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-plus"></i>
            <span>Add Staff</span>
        </a>
        <a href="admin_manage_staff.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_manage_staff.php' ? 'active' : ''; ?>">
            <i class="fas fa-users-cog"></i>
            <span>Manage Staff</span>
        </a>
        <a href="admin_add_student.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_add_student.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-graduate"></i>
            <span>Add Student</span>
        </a>
        <a href="admin_manage_student.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_manage_student.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-friends"></i>
            <span>Manage Student</span>
        </a>
        <a href="admin_notify_staff.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_notify_staff.php' ? 'active' : ''; ?>">
            <i class="fas fa-bell"></i>
            <span>Notify Staff</span>
        </a>
        <a href="admin_notify_student.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_notify_student.php' ? 'active' : ''; ?>">
            <i class="fas fa-bell"></i>
            <span>Notify Student</span>
        </a>
        <a href="admin_view_attendance.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_view_attendance.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-check"></i>
            <span>View Attendance</span>
        </a>
        <a href="admin_student_feedback.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_student_feedback.php' ? 'active' : ''; ?>">
            <i class="fas fa-comment-dots"></i>
            <span>Student Feedback</span>
        </a>
        <a href="admin_staff_feedback.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_staff_feedback.php' ? 'active' : ''; ?>">
            <i class="fas fa-comments"></i>
            <span>Staff Feedback</span>
        </a>
        <a href="admin_staff_leave.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_staff_leave.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-times"></i>
            <span>Staff Leave</span>
        </a>
        <a href="admin_student_leave.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_student_leave.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-minus"></i>
            <span>Student Leave</span>
        </a>
        <a href="../logout.php" class="nav-item logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </nav>
</aside>

<script>
    // Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            if (mainContent) mainContent.classList.toggle('expanded');
        });
    }

    // Dropdown Toggle
    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = this.closest('.nav-item-dropdown');
            const isActive = dropdown.classList.contains('active');
            
            document.querySelectorAll('.nav-item-dropdown').forEach(dd => {
                if (dd !== dropdown) {
                    dd.classList.remove('active');
                }
            });
            
            dropdown.classList.toggle('active', !isActive);
        });
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.nav-item-dropdown')) {
            document.querySelectorAll('.nav-item-dropdown').forEach(dd => {
                dd.classList.remove('active');
            });
        }
    });
</script>

