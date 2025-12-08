<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: login.php");
   exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - College Management System</title>
    <link rel="stylesheet" href="admin.css">
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
                <img src="https://ui-avatars.com/api/?name=Admin&background=1E5DE7&color=fff" alt="Admin">
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
                    <img src="https://ui-avatars.com/api/?name=Kumar&background=22B3A6&color=fff" alt="User">
                </div>
                <div class="sidebar-username">Kumar, Admin</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="admin.php" class="nav-item active">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-user-edit"></i>
                <span>Update Profile</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-book"></i>
                <span>Course</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-book-open"></i>
                <span>Subject</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Session</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-user-plus"></i>
                <span>Add Staff</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-users-cog"></i>
                <span>Manage Staff</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-user-graduate"></i>
                <span>Add Student</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-user-friends"></i>
                <span>Manage Student</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-bell"></i>
                <span>Notify Staff</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-bell"></i>
                <span>Notify Student</span>
            </a>
            <a href="logout.php" class="nav-item logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Metrics Cards Row -->
        <div class="metrics-row">
            <div class="metric-card card-red">
                <div class="metric-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">1,234</div>
                    <div class="metric-label">Total Students</div>
                </div>
                <div class="metric-footer">
                    <a href="#">More info <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-blue">
                <div class="metric-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">45</div>
                    <div class="metric-label">Total Staff</div>
                </div>
                <div class="metric-footer">
                    <a href="#">More info <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-graphite">
                <div class="metric-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">12</div>
                    <div class="metric-label">Total Courses</div>
                </div>
                <div class="metric-footer">
                    <a href="#">More info <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-pink">
                <div class="metric-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">89%</div>
                    <div class="metric-label">Attendance Rate</div>
                </div>
                <div class="metric-footer">
                    <a href="#">More info <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-row">
            <!-- Attendance Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Attendance Per Subject</h3>
                </div>
                <div class="chart-body">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>

            <!-- Staff-Student Overview Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Staffs - Students Overview</h3>
                </div>
                <div class="chart-body">
                    <canvas id="overviewChart"></canvas>
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
                labels: ['Math', 'Science', 'Java', 'Python'],
                datasets: [{
                    label: 'Attendance %',
                    data: [85, 92, 78, 88],
                    backgroundColor: '#FFA500',
                    borderRadius: 8,
                    barThickness: 50
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        enabled: true
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

        // Overview Chart (Donut)
        const overviewCtx = document.getElementById('overviewChart').getContext('2d');
        const overviewChart = new Chart(overviewCtx, {
            type: 'doughnut',
            data: {
                labels: ['Students', 'Staff'],
                datasets: [{
                    data: [1234, 45],
                    backgroundColor: ['#E64A4A', '#1976D2'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: {
                                size: 14,
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed;
                                return label;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>




