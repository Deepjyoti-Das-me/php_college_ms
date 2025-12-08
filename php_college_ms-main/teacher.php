<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "teacher") {
   header("Location: login.php");
   exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - College Management System</title>
    <link rel="stylesheet" href="teacher.css">
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
                <img src="https://ui-avatars.com/api/?name=Teacher&background=2196F3&color=fff" alt="Teacher">
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
                    <img src="https://ui-avatars.com/api/?name=Teacher&background=2196F3&color=fff" alt="User">
                </div>
                <div class="sidebar-username">Teacher</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="teacher.php" class="nav-item active">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-user-edit"></i>
                <span>My Profile</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-chalkboard"></i>
                <span>My Classes</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-calendar-check"></i>
                <span>Take Attendance</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-tasks"></i>
                <span>Assignments</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-grades"></i>
                <span>Grades & Results</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-user-friends"></i>
                <span>My Students</span>
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
            <h1>Welcome, Teacher!</h1>
            <p>Manage your classes and students</p>
        </div>

        <!-- Metrics Cards Row -->
        <div class="metrics-row">
            <div class="metric-card card-blue">
                <div class="metric-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">156</div>
                    <div class="metric-label">Total Students</div>
                </div>
                <div class="metric-footer">
                    <a href="#">View Students <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-green">
                <div class="metric-icon">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">4</div>
                    <div class="metric-label">Active Classes</div>
                </div>
                <div class="metric-footer">
                    <a href="#">View Classes <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-orange">
                <div class="metric-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">23</div>
                    <div class="metric-label">Pending Grading</div>
                </div>
                <div class="metric-footer">
                    <a href="#">View Tasks <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-purple">
                <div class="metric-icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">87%</div>
                    <div class="metric-label">Avg Attendance</div>
                </div>
                <div class="metric-footer">
                    <a href="#">View Details <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-row">
            <!-- Class Attendance Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Class Attendance Overview</h3>
                </div>
                <div class="chart-body">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>

            <!-- Student Performance Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Student Performance Distribution</h3>
                </div>
                <div class="chart-body">
                    <canvas id="performanceChart"></canvas>
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

        // Attendance Chart
        const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(attendanceCtx, {
            type: 'bar',
            data: {
                labels: ['Class A', 'Class B', 'Class C', 'Class D'],
                datasets: [{
                    label: 'Attendance %',
                    data: [92, 85, 88, 90],
                    backgroundColor: '#2196F3',
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
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
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

        // Performance Chart
        const performanceCtx = document.getElementById('performanceChart').getContext('2d');
        const performanceChart = new Chart(performanceCtx, {
            type: 'doughnut',
            data: {
                labels: ['Excellent (A+)', 'Good (A)', 'Average (B)', 'Below Average (C)'],
                datasets: [{
                    data: [45, 60, 35, 16],
                    backgroundColor: ['#4CAF50', '#2196F3', '#FF9800', '#F44336'],
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
    </script>
</body>
</html>




