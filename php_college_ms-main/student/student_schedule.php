<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "student") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$student_email = $_SESSION["user_email"];
$student_query = "SELECT s.sid, s.semester, s.cid FROM students s JOIN users u ON s.user_id = u.id WHERE u.email = '$student_email' LIMIT 1";
$student_result = mysqli_query($conn, $student_query);
$student_data = mysqli_fetch_assoc($student_result);
$sid = $student_data['sid'];
$semester = $student_data['semester'];
$cid = $student_data['cid'];

// Get class schedule for student's course and semester
$schedule_query = "SELECT cs.*, sub.subject_code, sub.subject_name, t.fname as t_fname, t.lname as t_lname
                   FROM class_schedule cs
                   JOIN subjects sub ON cs.subject_id = sub.id
                   LEFT JOIN teachers t ON cs.tid = t.tid
                   WHERE sub.cid = $cid
                   AND (cs.semester = '$semester' OR cs.semester IS NULL OR cs.semester = '')
                   ORDER BY 
                   CASE cs.day_of_week
                       WHEN 'Monday' THEN 1
                       WHEN 'Tuesday' THEN 2
                       WHEN 'Wednesday' THEN 3
                       WHEN 'Thursday' THEN 4
                       WHEN 'Friday' THEN 5
                       WHEN 'Saturday' THEN 6
                       WHEN 'Sunday' THEN 7
                   END,
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
    <title>Class Schedule - Student Panel</title>
    <link rel="stylesheet" href="student.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'student_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-clock"></i> Class Schedule</h1>
            <div style="color: #666; font-size: 14px;">
                Semester: <strong><?php echo htmlspecialchars($semester ?? 'N/A'); ?></strong>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <?php foreach ($days as $day): ?>
                <div class="form-container">
                    <h3 style="margin-bottom: 16px; color: #4CAF50; border-bottom: 2px solid #4CAF50; padding-bottom: 8px;">
                        <i class="fas fa-calendar-day"></i> <?php echo $day; ?>
                    </h3>
                    <?php if (isset($schedule_by_day[$day]) && count($schedule_by_day[$day]) > 0): ?>
                        <?php foreach ($schedule_by_day[$day] as $class): ?>
                            <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; margin-bottom: 12px; border-left: 4px solid #4CAF50;">
                                <div style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    <i class="fas fa-book"></i> <?php echo htmlspecialchars($class['subject_code'] . ' - ' . $class['subject_name']); ?>
                                </div>
                                <div style="color: #666; font-size: 14px; margin-bottom: 4px;">
                                    <i class="fas fa-clock"></i> 
                                    <?php echo date('h:i A', strtotime($class['start_time'])); ?> - 
                                    <?php echo date('h:i A', strtotime($class['end_time'])); ?>
                                </div>
                                <?php if ($class['room_number']): ?>
                                    <div style="color: #666; font-size: 14px; margin-bottom: 4px;">
                                        <i class="fas fa-door-open"></i> Room: <?php echo htmlspecialchars($class['room_number']); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($class['t_fname']): ?>
                                    <div style="color: #666; font-size: 14px;">
                                        <i class="fas fa-chalkboard-teacher"></i> 
                                        <?php echo htmlspecialchars($class['t_fname'] . ' ' . $class['t_lname']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 20px; color: #999;">
                            <i class="fas fa-calendar-times"></i><br>
                            No classes scheduled
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>


