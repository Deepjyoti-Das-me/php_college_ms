<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "staff") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

// Get staff data
$staff_email = $_SESSION["user_email"];
$staff_query = "SELECT s.*, u.email 
                FROM staff s 
                JOIN users u ON s.user_id = u.id 
                WHERE u.email = '$staff_email' LIMIT 1";
$staff_result = mysqli_query($conn, $staff_query);
$staff_data = mysqli_fetch_assoc($staff_result);
$sfid = $staff_data['sfid'];

// Get statistics
// Leave requests
$leave_query = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved
                FROM staff_leave WHERE sfid = $sfid";
$leave_result = mysqli_query($conn, $leave_query);
$leave_stats = mysqli_fetch_assoc($leave_result);

// Feedback count
$feedback_query = "SELECT COUNT(*) as count FROM staff_feedback WHERE sfid = $sfid";
$feedback_result = mysqli_query($conn, $feedback_query);
$feedback_data = mysqli_fetch_assoc($feedback_result);
$feedback_count = $feedback_data['count'] ?? 0;

// Notifications count
$notifications_query = "SELECT COUNT(*) as count FROM notifications 
                        WHERE (target_type = 'all' OR target_type = 'staff')
                        AND (target_id IS NULL OR target_id = $sfid)
                        AND status = 'active'";
$notifications_result = mysqli_query($conn, $notifications_query);
$notifications_data = mysqli_fetch_assoc($notifications_result);
$notifications_count = $notifications_data['count'] ?? 0;
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
    <link rel="stylesheet" href="../deep_ai_chatbot.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'staff_header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Welcome, <?php echo htmlspecialchars($staff_data['fname']); ?>!</h1>
            <p>Staff ID: <strong><?php echo htmlspecialchars($staff_data['staff_id']); ?></strong> | Designation: <strong><?php echo htmlspecialchars($staff_data['designation'] ?? 'N/A'); ?></strong></p>
        </div>

        <!-- Metrics Cards Row -->
        <div class="metrics-row">
            <div class="metric-card card-orange">
                <div class="metric-icon">
                    <i class="fas fa-calendar-minus"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value"><?php echo $leave_stats['pending'] ?? 0; ?></div>
                    <div class="metric-label">Pending Leave</div>
                </div>
                <div class="metric-footer">
                    <a href="staff_leave.php">View Leave <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-blue">
                <div class="metric-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value"><?php echo $leave_stats['approved'] ?? 0; ?></div>
                    <div class="metric-label">Approved Leave</div>
                </div>
                <div class="metric-footer">
                    <a href="staff_leave.php">View Details <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-green">
                <div class="metric-icon">
                    <i class="fas fa-comment-dots"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value"><?php echo $feedback_count; ?></div>
                    <div class="metric-label">Feedback Submitted</div>
                </div>
                <div class="metric-footer">
                    <a href="staff_feedback.php">View Feedback <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="metric-card card-purple">
                <div class="metric-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value"><?php echo $notifications_count; ?></div>
                    <div class="metric-label">Notifications</div>
                </div>
                <div class="metric-footer">
                    <a href="staff_notifications.php">View All <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-row">
            <!-- Leave Status Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Leave Status Overview</h3>
                </div>
                <div class="chart-body">
                    <canvas id="taskChart"></canvas>
                </div>
            </div>

            <!-- Monthly Leave Requests Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Monthly Leave Requests</h3>
                </div>
                <div class="chart-body">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Sidebar toggle is handled in staff_header.php

        // Leave Status Chart
        const taskCtx = document.getElementById('taskChart').getContext('2d');
        const taskChart = new Chart(taskCtx, {
            type: 'doughnut',
            data: {
                labels: ['Approved', 'Pending', 'Rejected'],
                datasets: [{
                    data: [
                        <?php echo $leave_stats['approved'] ?? 0; ?>,
                        <?php echo $leave_stats['pending'] ?? 0; ?>,
                        <?php echo ($leave_stats['total'] ?? 0) - ($leave_stats['approved'] ?? 0) - ($leave_stats['pending'] ?? 0); ?>
                    ],
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

        // Leave Requests Chart (Last 6 months)
        <?php
        $monthly_leave = "SELECT 
                         DATE_FORMAT(created_at, '%b %Y') as month,
                         COUNT(*) as count
                         FROM staff_leave 
                         WHERE sfid = $sfid 
                         AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                         GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                         ORDER BY DATE_FORMAT(created_at, '%Y-%m')
                         LIMIT 6";
        $monthly_result = mysqli_query($conn, $monthly_leave);
        $month_labels = [];
        $month_data = [];
        while ($row = mysqli_fetch_assoc($monthly_result)) {
            $month_labels[] = $row['month'];
            $month_data[] = $row['count'];
        }
        ?>
        
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(activityCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($month_labels); ?>,
                datasets: [{
                    label: 'Leave Requests',
                    data: <?php echo json_encode($month_data); ?>,
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

    <!-- Deep AI Chatbot -->
    <?php include '../deep_ai_chatbot.php'; ?>

</body>
</html>





