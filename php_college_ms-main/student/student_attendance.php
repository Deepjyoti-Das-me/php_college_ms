<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "student") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$student_email = $_SESSION["user_email"];
$student_query = "SELECT sid FROM students s JOIN users u ON s.user_id = u.id WHERE u.email = '$student_email' LIMIT 1";
$student_result = mysqli_query($conn, $student_query);
$student_row = mysqli_fetch_assoc($student_result);
$sid = $student_row['sid'];

// Get attendance records
$attendance_query = "SELECT a.*, sub.subject_code, sub.subject_name, sub.subject_type
                     FROM attendance a
                     JOIN subjects sub ON a.subject_id = sub.id
                     WHERE a.sid = $sid
                     ORDER BY a.date DESC
                     LIMIT 100";
$attendance_result = mysqli_query($conn, $attendance_query);

// Calculate statistics
$stats_query = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late
                FROM attendance WHERE sid = $sid";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
$attendance_percentage = $stats['total'] > 0 ? round(($stats['present'] / $stats['total']) * 100, 2) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Attendance - Student Panel</title>
    <link rel="stylesheet" href="student.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'student_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-calendar-check"></i> My Attendance</h1>
        </div>

        <!-- Statistics Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 24px;">
            <div class="form-container" style="text-align: center; padding: 20px;">
                <div style="font-size: 36px; font-weight: 700; color: #4CAF50; margin-bottom: 8px;">
                    <?php echo $attendance_percentage; ?>%
                </div>
                <div style="color: #666;">Overall Attendance</div>
            </div>
            <div class="form-container" style="text-align: center; padding: 20px;">
                <div style="font-size: 36px; font-weight: 700; color: #4CAF50; margin-bottom: 8px;">
                    <?php echo $stats['present']; ?>
                </div>
                <div style="color: #666;">Present</div>
            </div>
            <div class="form-container" style="text-align: center; padding: 20px;">
                <div style="font-size: 36px; font-weight: 700; color: #E64A4A; margin-bottom: 8px;">
                    <?php echo $stats['absent']; ?>
                </div>
                <div style="color: #666;">Absent</div>
            </div>
            <div class="form-container" style="text-align: center; padding: 20px;">
                <div style="font-size: 36px; font-weight: 700; color: #FF9800; margin-bottom: 8px;">
                    <?php echo $stats['late']; ?>
                </div>
                <div style="color: #666;">Late</div>
            </div>
        </div>

        <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($attendance_result) > 0): ?>
                        <?php while ($attendance = mysqli_fetch_assoc($attendance_result)): ?>
                            <tr>
                                <td><?php echo date('d M Y', strtotime($attendance['date'])); ?></td>
                                <td><strong><?php echo htmlspecialchars($attendance['subject_code']); ?></strong></td>
                                <td><?php echo htmlspecialchars($attendance['subject_name']); ?></td>
                                <td>
                                    <span style="padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 600; background: #e3f2fd; color: #1976d2;">
                                        <?php echo htmlspecialchars($attendance['subject_type']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $status_colors = [
                                        'present' => ['#d4edda', '#155724'],
                                        'absent' => ['#f8d7da', '#721c24'],
                                        'late' => ['#fff3cd', '#856404'],
                                        'excused' => ['#d1ecf1', '#0c5460']
                                    ];
                                    $colors = $status_colors[$attendance['status']] ?? ['#e0e0e0', '#666'];
                                    ?>
                                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; 
                                        background: <?php echo $colors[0]; ?>;
                                        color: <?php echo $colors[1]; ?>;">
                                        <?php echo ucfirst($attendance['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($attendance['remarks'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                                No attendance records found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>


