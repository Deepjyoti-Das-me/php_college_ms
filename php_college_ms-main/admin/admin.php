<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

// Get statistics from database
// Total Students
$students_query = "SELECT COUNT(*) as total FROM students WHERE status = 'active'";
$students_result = mysqli_query($conn, $students_query);
$students_data = mysqli_fetch_assoc($students_result);
$total_students = $students_data['total'] ?? 0;

// Total Staff
$staff_query = "SELECT COUNT(*) as total FROM staff WHERE status = 'active'";
$staff_result = mysqli_query($conn, $staff_query);
$staff_data = mysqli_fetch_assoc($staff_result);
$total_staff = $staff_data['total'] ?? 0;

// Total Courses
$courses_query = "SELECT COUNT(*) as total FROM courses WHERE status = 'active'";
$courses_result = mysqli_query($conn, $courses_query);
$courses_data = mysqli_fetch_assoc($courses_result);
$total_courses = $courses_data['total'] ?? 0;

// Average Attendance Rate
$attendance_query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present
                    FROM attendance";
$attendance_result = mysqli_query($conn, $attendance_query);
$attendance_data = mysqli_fetch_assoc($attendance_result);
$attendance_rate = $attendance_data['total'] > 0 ? round(($attendance_data['present'] / $attendance_data['total']) * 100, 0) : 0;
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
    <link rel="stylesheet" href="../deep_ai_chatbot.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Metrics Cards Row -->
        <div class="metrics-row">
            <div class="metric-card card-red">
                <div class="metric-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($total_students); ?></div>
                    <div class="metric-label">Total Students</div>
                </div>
                <div class="metric-footer">
                    <a href="admin_manage_student.php">More info <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-blue">
                <div class="metric-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($total_staff); ?></div>
                    <div class="metric-label">Total Staff</div>
                </div>
                <div class="metric-footer">
                    <a href="admin_manage_staff.php">More info <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-graphite">
                <div class="metric-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($total_courses); ?></div>
                    <div class="metric-label">Total Courses</div>
                </div>
                <div class="metric-footer">
                    <a href="admin_manage_course.php">More info <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-pink">
                <div class="metric-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value"><?php echo $attendance_rate; ?>%</div>
                    <div class="metric-label">Attendance Rate</div>
                </div>
                <div class="metric-footer">
                    <a href="admin_view_attendance.php">More info <i class="fas fa-arrow-right"></i></a>
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
        // Sidebar toggle is handled in admin_header.php

        // Dropdown Toggle for Course and Subject
        document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const dropdown = this.closest('.nav-item-dropdown');
                const isActive = dropdown.classList.contains('active');
                
                // Close all other dropdowns
                document.querySelectorAll('.nav-item-dropdown').forEach(dd => {
                    if (dd !== dropdown) {
                        dd.classList.remove('active');
                    }
                });
                
                // Toggle current dropdown
                dropdown.classList.toggle('active', !isActive);
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.nav-item-dropdown')) {
                document.querySelectorAll('.nav-item-dropdown').forEach(dd => {
                    dd.classList.remove('active');
                });
            }
        });

        // Attendance Chart - Get real data
        <?php
        $attendance_by_subject = "SELECT sub.subject_name, 
                                  COUNT(*) as total,
                                  SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present
                                  FROM attendance a
                                  JOIN subjects sub ON a.subject_id = sub.id
                                  GROUP BY sub.id, sub.subject_name
                                  ORDER BY sub.subject_name
                                  LIMIT 4";
        $attendance_subject_result = mysqli_query($conn, $attendance_by_subject);
        $subject_labels = [];
        $subject_data = [];
        while ($row = mysqli_fetch_assoc($attendance_subject_result)) {
            $subject_labels[] = $row['subject_name'];
            $percentage = $row['total'] > 0 ? round(($row['present'] / $row['total']) * 100, 0) : 0;
            $subject_data[] = $percentage;
        }
        // If no data, use default
        if (empty($subject_labels)) {
            $subject_labels = ['Math', 'Science', 'Java', 'Python'];
            $subject_data = [0, 0, 0, 0];
        }
        ?>
        
        const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(attendanceCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($subject_labels); ?>,
                datasets: [{
                    label: 'Attendance %',
                    data: <?php echo json_encode($subject_data); ?>,
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

        // Overview Chart (Donut) - Use real data
        const overviewCtx = document.getElementById('overviewChart').getContext('2d');
        const overviewChart = new Chart(overviewCtx, {
            type: 'doughnut',
            data: {
                labels: ['Students', 'Staff'],
                datasets: [{
                    data: [<?php echo $total_students; ?>, <?php echo $total_staff; ?>],
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

    <!-- Deep AI Chatbot -->
    <?php include '../deep_ai_chatbot.php'; ?>

</body>
</html>





