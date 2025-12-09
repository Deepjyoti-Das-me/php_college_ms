<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "student") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

// Get student data
$student_email = $_SESSION["user_email"];
$student_query = "SELECT s.*, c.course_code, c.cname, u.email 
                   FROM students s 
                   JOIN users u ON s.user_id = u.id 
                   LEFT JOIN courses c ON s.cid = c.cid 
                   WHERE u.email = '$student_email' LIMIT 1";
$student_result = mysqli_query($conn, $student_query);
$student_data = mysqli_fetch_assoc($student_result);
$sid = $student_data['sid'];

// Get statistics
$attendance_query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present
                    FROM attendance WHERE sid = $sid";
$attendance_result = mysqli_query($conn, $attendance_query);
$attendance_stats = mysqli_fetch_assoc($attendance_result);
$attendance_percentage = $attendance_stats['total'] > 0 ? round(($attendance_stats['present'] / $attendance_stats['total']) * 100, 2) : 0;

// Get course count
$course_count = 1; // Student is enrolled in one course

// Get pending assignments
$assignments_query = "SELECT COUNT(*) as count FROM assignments a
                      JOIN subjects sub ON a.subject_id = sub.id
                      WHERE sub.cid = " . $student_data['cid'] . "
                      AND a.status = 'active'
                      AND a.due_date >= CURDATE()
                      AND NOT EXISTS (
                          SELECT 1 FROM assignment_submissions asub 
                          WHERE asub.assignment_id = a.id AND asub.sid = $sid
                      )";
$assignments_result = mysqli_query($conn, $assignments_query);
$assignments_data = mysqli_fetch_assoc($assignments_result);
$pending_assignments = $assignments_data['count'] ?? 0;

// Get overall grade
$grades_query = "SELECT AVG((marks_obtained / total_marks) * 100) as avg_percentage
                 FROM grades WHERE sid = $sid AND marks_obtained IS NOT NULL";
$grades_result = mysqli_query($conn, $grades_query);
$grades_data = mysqli_fetch_assoc($grades_result);
$avg_percentage = $grades_data['avg_percentage'] ?? 0;
$overall_grade = 'N/A';
if ($avg_percentage >= 90) $overall_grade = 'A+';
elseif ($avg_percentage >= 80) $overall_grade = 'A';
elseif ($avg_percentage >= 70) $overall_grade = 'B+';
elseif ($avg_percentage >= 60) $overall_grade = 'B';
elseif ($avg_percentage >= 50) $overall_grade = 'C';
elseif ($avg_percentage > 0) $overall_grade = 'D';
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
    <link rel="stylesheet" href="../deep_ai_chatbot.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'student_header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Welcome Back, <?php echo htmlspecialchars($student_data['fname']); ?>!</h1>
            <p>Roll No: <strong><?php echo htmlspecialchars($student_data['roll_no']); ?></strong> | <?php echo htmlspecialchars(($student_data['course_code'] ?? 'N/A') . ' - ' . ($student_data['cname'] ?? 'No Course')); ?></p>
        </div>

        <!-- Metrics Cards Row -->
        <div class="metrics-row">
            <div class="metric-card card-green">
                <div class="metric-icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value"><?php echo $attendance_percentage; ?>%</div>
                    <div class="metric-label">Attendance</div>
                </div>
                <div class="metric-footer">
                    <a href="student_attendance.php">View Details <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-blue">
                <div class="metric-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value"><?php echo $course_count; ?></div>
                    <div class="metric-label">Active Courses</div>
                </div>
                <div class="metric-footer">
                    <a href="student_courses.php">View Courses <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-orange">
                <div class="metric-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value"><?php echo $pending_assignments; ?></div>
                    <div class="metric-label">Pending Assignments</div>
                </div>
                <div class="metric-footer">
                    <a href="student_assignments.php">View Assignments <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-purple">
                <div class="metric-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value"><?php echo $overall_grade; ?></div>
                    <div class="metric-label">Overall Grade</div>
                </div>
                <div class="metric-footer">
                    <a href="student_grades.php">View Grades <i class="fas fa-arrow-right"></i></a>
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
        // Sidebar toggle is handled in student_header.php

        // Attendance Chart - Get real data
        <?php
        $attendance_by_subject = "SELECT sub.subject_name, 
                                  COUNT(*) as total,
                                  SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present
                                  FROM attendance a
                                  JOIN subjects sub ON a.subject_id = sub.id
                                  WHERE a.sid = $sid
                                  GROUP BY sub.id, sub.subject_name
                                  ORDER BY sub.subject_name
                                  LIMIT 6";
        $attendance_subject_result = mysqli_query($conn, $attendance_by_subject);
        $subject_labels = [];
        $subject_data = [];
        while ($row = mysqli_fetch_assoc($attendance_subject_result)) {
            $subject_labels[] = $row['subject_name'];
            $percentage = $row['total'] > 0 ? round(($row['present'] / $row['total']) * 100, 0) : 0;
            $subject_data[] = $percentage;
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

        // Grade Chart - Get real data
        <?php
        $grade_distribution = "SELECT 
                              SUM(CASE WHEN (marks_obtained / total_marks) * 100 >= 90 THEN 1 ELSE 0 END) as a_plus,
                              SUM(CASE WHEN (marks_obtained / total_marks) * 100 >= 80 AND (marks_obtained / total_marks) * 100 < 90 THEN 1 ELSE 0 END) as a,
                              SUM(CASE WHEN (marks_obtained / total_marks) * 100 >= 70 AND (marks_obtained / total_marks) * 100 < 80 THEN 1 ELSE 0 END) as b_plus,
                              SUM(CASE WHEN (marks_obtained / total_marks) * 100 >= 60 AND (marks_obtained / total_marks) * 100 < 70 THEN 1 ELSE 0 END) as b,
                              SUM(CASE WHEN (marks_obtained / total_marks) * 100 < 60 THEN 1 ELSE 0 END) as c
                              FROM grades WHERE sid = $sid AND marks_obtained IS NOT NULL";
        $grade_dist_result = mysqli_query($conn, $grade_distribution);
        $grade_dist_data = mysqli_fetch_assoc($grade_dist_result);
        ?>
        
        const gradeCtx = document.getElementById('gradeChart').getContext('2d');
        const gradeChart = new Chart(gradeCtx, {
            type: 'doughnut',
            data: {
                labels: ['A+', 'A', 'B+', 'B', 'C'],
                datasets: [{
                    data: [
                        <?php echo $grade_dist_data['a_plus'] ?? 0; ?>,
                        <?php echo $grade_dist_data['a'] ?? 0; ?>,
                        <?php echo $grade_dist_data['b_plus'] ?? 0; ?>,
                        <?php echo $grade_dist_data['b'] ?? 0; ?>,
                        <?php echo $grade_dist_data['c'] ?? 0; ?>
                    ],
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

    <!-- Deep AI Chatbot -->
    <?php include '../deep_ai_chatbot.php'; ?>

</body>
</html>





