<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "teacher") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$teacher_email = $_SESSION["user_email"];
$teacher_query = "SELECT tid FROM teachers t JOIN users u ON t.user_id = u.id WHERE u.email = '$teacher_email' LIMIT 1";
$teacher_result = mysqli_query($conn, $teacher_query);
$teacher_data = mysqli_fetch_assoc($teacher_result);
$tid = $teacher_data['tid'];

// Get class schedule for this teacher
$schedule_query = "SELECT cs.*, s.subject_code, s.subject_name, c.course_code, c.cname 
                   FROM class_schedule cs 
                   JOIN subjects s ON cs.subject_id = s.id 
                   LEFT JOIN courses c ON s.cid = c.cid 
                   WHERE cs.tid = $tid 
                   ORDER BY 
                   FIELD(cs.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
                   cs.start_time";
$schedule_result = mysqli_query($conn, $schedule_query);

// Group by day
$schedule_by_day = [];
while ($row = mysqli_fetch_assoc($schedule_result)) {
    $day = $row['day_of_week'];
    if (!isset($schedule_by_day[$day])) {
        $schedule_by_day[$day] = [];
    }
    $schedule_by_day[$day][] = $row;
}

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Schedule - Teacher Panel</title>
    <link rel="stylesheet" href="teacher.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'teacher_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-clock"></i> Class Schedule</h1>
            <p>Your weekly class timetable</p>
        </div>

        <div class="form-container">
            <?php if (!empty($schedule_by_day)): ?>
                <div style="display: grid; gap: 20px;">
                    <?php foreach ($days as $day): ?>
                        <?php if (isset($schedule_by_day[$day])): ?>
                            <div style="border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                                <div style="background: #2196F3; color: white; padding: 12px 20px; font-weight: 600;">
                                    <i class="fas fa-calendar-day"></i> <?php echo $day; ?>
                                </div>
                                <div style="padding: 20px;">
                                    <table class="data-table" style="margin: 0;">
                                        <thead>
                                            <tr>
                                                <th>Time</th>
                                                <th>Subject</th>
                                                <th>Course</th>
                                                <th>Room</th>
                                                <th>Semester</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($schedule_by_day[$day] as $schedule): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo date('h:i A', strtotime($schedule['start_time'])); ?></strong> - 
                                                        <?php echo date('h:i A', strtotime($schedule['end_time'])); ?>
                                                    </td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($schedule['subject_code']); ?></strong><br>
                                                        <small><?php echo htmlspecialchars($schedule['subject_name']); ?></small>
                                                    </td>
                                                    <td><?php echo $schedule['cname'] ? htmlspecialchars($schedule['course_code'] . ' - ' . $schedule['cname']) : 'N/A'; ?></td>
                                                    <td><?php echo htmlspecialchars($schedule['room_number'] ?? 'N/A'); ?></td>
                                                    <td><?php echo htmlspecialchars($schedule['semester'] ?? 'N/A'); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #666;">
                    <i class="fas fa-clock" style="font-size: 48px; margin-bottom: 20px; opacity: 0.3;"></i>
                    <p>No schedule assigned yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

