<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "student") {
   header("Location: login.php");
   exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - College Management System</title>
    <link rel="stylesheet" href="student.css">
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
                <img src="https://ui-avatars.com/api/?name=Student&background=4CAF50&color=fff" alt="Student">
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
                    <img src="https://ui-avatars.com/api/?name=Student&background=4CAF50&color=fff" alt="User">
                </div>
                <div class="sidebar-username">Student</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="student.php" class="nav-item active">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-user-edit"></i>
                <span>My Profile</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-calendar-check"></i>
                <span>My Attendance</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-book"></i>
                <span>My Courses</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-clipboard-list"></i>
                <span>Assignments</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-chart-line"></i>
                <span>Grades & Results</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-clock"></i>
                <span>Class Schedule</span>
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
            <h1>Welcome Back, Student!</h1>
            <p>Here's your academic overview</p>
        </div>

        <!-- Metrics Cards Row -->
        <div class="metrics-row">
            <div class="metric-card card-green">
                <div class="metric-icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">92%</div>
                    <div class="metric-label">Attendance</div>
                </div>
                <div class="metric-footer">
                    <a href="#">View Details <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-blue">
                <div class="metric-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">6</div>
                    <div class="metric-label">Active Courses</div>
                </div>
                <div class="metric-footer">
                    <a href="#">View Courses <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-orange">
                <div class="metric-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">8</div>
                    <div class="metric-label">Pending Assignments</div>
                </div>
                <div class="metric-footer">
                    <a href="#">View Assignments <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-purple">
                <div class="metric-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">A+</div>
                    <div class="metric-label">Overall Grade</div>
                </div>
                <div class="metric-footer">
                    <a href="#">View Grades <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-row">
            <!-- Attendance Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3>My Attendance by Subject</h3>
                </div>
                <div class="chart-body">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>

            <!-- Grade Distribution Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Grade Distribution</h3>
                </div>
                <div class="chart-body">
                    <canvas id="gradeChart"></canvas>
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
                labels: ['Math', 'Science', 'Java', 'Python', 'English', 'History'],
                datasets: [{
                    label: 'Attendance %',
                    data: [95, 90, 88, 92, 94, 89],
                    backgroundColor: '#4CAF50',
                    borderRadius: 8,
                    barThickness: 40
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

        // Grade Chart
        const gradeCtx = document.getElementById('gradeChart').getContext('2d');
        const gradeChart = new Chart(gradeCtx, {
            type: 'doughnut',
            data: {
                labels: ['A+', 'A', 'B+', 'B', 'C'],
                datasets: [{
                    data: [3, 2, 1, 0, 0],
                    backgroundColor: ['#4CAF50', '#8BC34A', '#FFC107', '#FF9800', '#F44336'],
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




