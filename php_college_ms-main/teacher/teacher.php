<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "teacher") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

// Get teacher data
$teacher_email = $_SESSION["user_email"];
$teacher_query = "SELECT t.*, u.email 
                  FROM teachers t 
                  JOIN users u ON t.user_id = u.id 
                  WHERE u.email = '$teacher_email' LIMIT 1";
$teacher_result = mysqli_query($conn, $teacher_query);
$teacher_data = mysqli_fetch_assoc($teacher_result);
$tid = $teacher_data['tid'];

// Get statistics
// Total students (students in subjects taught by this teacher)
$students_query = "SELECT COUNT(DISTINCT s.sid) as total
                   FROM students s
                   JOIN subjects sub ON s.cid = sub.cid
                   WHERE sub.tid = $tid";
$students_result = mysqli_query($conn, $students_query);
$students_data = mysqli_fetch_assoc($students_result);
$total_students = $students_data['total'] ?? 0;

// Active classes (subjects taught by this teacher)
$classes_query = "SELECT COUNT(DISTINCT id) as total FROM subjects WHERE tid = $tid AND cid IS NOT NULL";
$classes_result = mysqli_query($conn, $classes_query);
$classes_data = mysqli_fetch_assoc($classes_result);
$active_classes = $classes_data['total'] ?? 0;

// Pending grading (assignments with submissions not yet graded)
$grading_query = "SELECT COUNT(DISTINCT asub.id) as total
                  FROM assignment_submissions asub
                  JOIN assignments a ON asub.assignment_id = a.id
                  WHERE a.tid = $tid AND asub.status = 'submitted' AND asub.marks_obtained IS NULL";
$grading_result = mysqli_query($conn, $grading_query);
$grading_data = mysqli_fetch_assoc($grading_result);
$pending_grading = $grading_data['total'] ?? 0;

// Average attendance (for students in this teacher's subjects)
$attendance_query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present
                    FROM attendance a
                    JOIN subjects sub ON a.subject_id = sub.id
                    WHERE sub.tid = $tid";
$attendance_result = mysqli_query($conn, $attendance_query);
$attendance_stats = mysqli_fetch_assoc($attendance_result);
$avg_attendance = $attendance_stats['total'] > 0 ? round(($attendance_stats['present'] / $attendance_stats['total']) * 100, 2) : 0;
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
    <link rel="stylesheet" href="../deep_ai_chatbot.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'teacher_header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Welcome Back, <?php echo htmlspecialchars($teacher_data['fname']); ?>!</h1>
            <p>Teacher ID: <strong><?php echo htmlspecialchars($teacher_data['teacher_id']); ?></strong> | Manage your classes and students</p>
        </div>

        <!-- Metrics Cards Row -->
        <div class="metrics-row">
            <div class="metric-card card-blue">
                <div class="metric-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value"><?php echo $total_students; ?></div>
                    <div class="metric-label">Total Students</div>
                </div>
                <div class="metric-footer">
                    <a href="teacher_my_students.php">View Students <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-green">
                <div class="metric-icon">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value"><?php echo $active_classes; ?></div>
                    <div class="metric-label">Active Classes</div>
                </div>
                <div class="metric-footer">
                    <a href="teacher_classes.php">View Classes <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-orange">
                <div class="metric-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value"><?php echo $pending_grading; ?></div>
                    <div class="metric-label">Pending Grading</div>
                </div>
                <div class="metric-footer">
                    <a href="teacher_assignments.php">View Tasks <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-purple">
                <div class="metric-icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value"><?php echo $avg_attendance; ?>%</div>
                    <div class="metric-label">Avg Attendance</div>
                </div>
                <div class="metric-footer">
                    <a href="teacher_take_attendance.php">View Details <i class="fas fa-arrow-right"></i></a>
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
        // Attendance Chart - Get data from PHP
        <?php
        // Get attendance by subject
        $chart_attendance_query = "SELECT 
            sub.subject_name,
            COUNT(a.id) as total,
            SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present
            FROM subjects sub
            LEFT JOIN attendance a ON sub.id = a.subject_id
            WHERE sub.tid = $tid
            GROUP BY sub.id, sub.subject_name
            ORDER BY sub.subject_name
            LIMIT 5";
        $chart_attendance_result = mysqli_query($conn, $chart_attendance_query);
        $chart_labels = [];
        $chart_data = [];
        while ($row = mysqli_fetch_assoc($chart_attendance_result)) {
            $chart_labels[] = $row['subject_name'];
            $percentage = $row['total'] > 0 ? round(($row['present'] / $row['total']) * 100, 0) : 0;
            $chart_data[] = $percentage;
        }
        ?>

        // Performance Chart - Get grade distribution
        <?php
        $grade_dist_query = "SELECT 
            CASE 
                WHEN (g.marks_obtained / g.total_marks * 100) >= 90 THEN 'Excellent (A+)'
                WHEN (g.marks_obtained / g.total_marks * 100) >= 80 THEN 'Good (A)'
                WHEN (g.marks_obtained / g.total_marks * 100) >= 70 THEN 'Average (B)'
                ELSE 'Below Average (C)'
            END as grade_category,
            COUNT(*) as count
            FROM grades g
            JOIN subjects sub ON g.subject_id = sub.id
            WHERE sub.tid = $tid AND g.marks_obtained IS NOT NULL
            GROUP BY grade_category";
        $grade_dist_result = mysqli_query($conn, $grade_dist_query);
        $grade_labels = ['Excellent (A+)', 'Good (A)', 'Average (B)', 'Below Average (C)'];
        $grade_counts = [0, 0, 0, 0];
        while ($row = mysqli_fetch_assoc($grade_dist_result)) {
            $idx = array_search($row['grade_category'], $grade_labels);
            if ($idx !== false) {
                $grade_counts[$idx] = (int)$row['count'];
            }
        }
        ?>

        // Attendance Chart
        const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(attendanceCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Attendance %',
                    data: <?php echo json_encode($chart_data); ?>,
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
                labels: <?php echo json_encode($grade_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($grade_counts); ?>,
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

    <!-- Deep AI Chatbot -->
    <?php include '../deep_ai_chatbot.php'; ?>

</body>
</html>






