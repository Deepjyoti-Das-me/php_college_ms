<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "staff") {
   header("Location: login.php");
   exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - College Management System</title>
    <link rel="stylesheet" href="staff.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
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
                <img src="https://ui-avatars.com/api/?name=Staff&background=FF9800&color=fff" alt="Staff">
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
                    <img src="https://ui-avatars.com/api/?name=Staff&background=FF9800&color=fff" alt="User">
                </div>
                <div class="sidebar-username">Staff</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="staff.php" class="nav-item active">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-user-edit"></i>
                <span>My Profile</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-calendar-check"></i>
                <span>Attendance</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-tasks"></i>
                <span>Tasks & Duties</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-building"></i>
                <span>Department</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-clock"></i>
                <span>Schedule</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
            </a>
            <a href="logout.php" class="nav-item logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Welcome, Staff Member!</h1>
            <p>Manage your tasks and activities</p>
        </div>

        <!-- Metrics Cards Row -->
        <div class="metrics-row">
            <div class="metric-card card-orange">
                <div class="metric-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">12</div>
                    <div class="metric-label">Active Tasks</div>
                </div>
                <div class="metric-footer">
                    <a href="#">View Tasks <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-blue">
                <div class="metric-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">98%</div>
                    <div class="metric-label">Attendance</div>
                </div>
                <div class="metric-footer">
                    <a href="#">View Details <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-green">
                <div class="metric-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">8</div>
                    <div class="metric-label">Reports This Month</div>
                </div>
                <div class="metric-footer">
                    <a href="#">View Reports <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-purple">
                <div class="metric-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">24</div>
                    <div class="metric-label">Team Members</div>
                </div>
                <div class="metric-footer">
                    <a href="#">View Team <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-row">
            <!-- Task Status Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Task Status Overview</h3>
                </div>
                <div class="chart-body">
                    <canvas id="taskChart"></canvas>
                </div>
            </div>

            <!-- Monthly Activity Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Monthly Activity</h3>
                </div>
                <div class="chart-body">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Sidebar Toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-content');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // Task Chart
        const taskCtx = document.getElementById('taskChart').getContext('2d');
        const taskChart = new Chart(taskCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'In Progress', 'Pending'],
                datasets: [{
                    data: [45, 12, 8],
                    backgroundColor: ['#4CAF50', '#FF9800', '#F44336'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });

        // Activity Chart
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(activityCtx, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Tasks Completed',
                    data: [8, 12, 10, 15],
                    backgroundColor: '#FF9800',
                    borderRadius: 8,
                    barThickness: 50
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>




