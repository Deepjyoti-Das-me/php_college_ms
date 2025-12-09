<?php
require_once "../database.php";
$staff_email = $_SESSION["user_email"] ?? "";
$staff_name = $_SESSION["user_name"] ?? "Staff";

$staff_query = "SELECT s.*, u.email FROM staff s JOIN users u ON s.user_id = u.id WHERE u.email = '$staff_email' LIMIT 1";
$staff_result = mysqli_query($conn, $staff_query);
$staff_data = mysqli_fetch_assoc($staff_result);
$staff_fname = $staff_data['fname'] ?? 'Staff';
$staff_lname = $staff_data['lname'] ?? '';
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
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($staff_fname . ' ' . $staff_lname); ?>&background=FF9800&color=fff" alt="Staff">
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
            <img src="https://ui-avatars.com/api/?name=CMS&background=FF9800&color=fff&size=60" alt="Logo">
        </div>
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($staff_fname . ' ' . $staff_lname); ?>&background=FF9800&color=fff" alt="User">
            </div>
            <div class="sidebar-username"><?php echo $staff_fname; ?></div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="staff.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'staff.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="staff_my_profile.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'staff_my_profile.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-edit"></i>
            <span>My Profile</span>
        </a>
        <a href="staff_leave.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'staff_leave.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-minus"></i>
            <span>Leave Request</span>
        </a>
        <a href="staff_feedback.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'staff_feedback.php' ? 'active' : ''; ?>">
            <i class="fas fa-comment-dots"></i>
            <span>Give Feedback</span>
        </a>
        <a href="staff_notifications.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'staff_notifications.php' ? 'active' : ''; ?>">
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


